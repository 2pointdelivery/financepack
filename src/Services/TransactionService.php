<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Contracts\Services\TransactionServiceInterface;
use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Enums\Accounting\TransactionType;
use FinancePack\Models\Accounting\Account;
use FinancePack\Models\Accounting\JournalEntry;
use FinancePack\Models\Accounting\Transaction;
use FinancePack\Models\Banking\BankAccount;
use FinancePack\Models\Company;
use Illuminate\Support\Facades\DB;

class TransactionService implements TransactionServiceInterface
{
    public function createStartingBalanceIfNeeded(Company $company, BankAccount $bankAccount, array $transactions, float $currentBalance, string $startDate): void
    {
        if (empty($transactions)) {
            return;
        }

        $earliestTransactionDate = min(array_map(
            fn ($t) => $t->posted_at ?? $t->date ?? $startDate,
            $transactions
        ));

        $runningBalance = 0.0;
        $sortedTransactions = collect($transactions)->sortBy('posted_at')->values()->all();

        foreach ($sortedTransactions as $transaction) {
            $postedAt = $transaction->posted_at ?? $transaction->date;

            if ($postedAt >= $startDate) {
                break;
            }

            $amount = (float) ($transaction->amount ?? 0);
            $type = $transaction->type ?? ($amount >= 0 ? 'deposit' : 'withdrawal');

            $runningBalance += $type === 'deposit' ? abs($amount) : -abs($amount);
        }

        $expectedStartingBalance = $currentBalance - $runningBalance;

        $hasStartingBalance = Transaction::where('company_id', $company->id)
            ->where('bank_account_id', $bankAccount->id)
            ->where('type', TransactionType::Deposit)
            ->where('description', 'LIKE', '%Starting Balance%')
            ->where('posted_at', $startDate)
            ->exists();

        if (! $hasStartingBalance && abs($expectedStartingBalance) > 0.001) {
            $this->createStartingBalanceTransaction($company, $bankAccount, $expectedStartingBalance, $startDate);
        }
    }

    public function storeTransactions(Company $company, BankAccount $bankAccount, array $transactions): void
    {
        foreach ($transactions as $transaction) {
            $this->storeTransaction($company, $bankAccount, $transaction);
        }
    }

    public function storeTransaction(Company $company, BankAccount $bankAccount, object $transaction): void
    {
        $amount = (float) ($transaction->amount ?? 0);
        $transactionType = $amount >= 0
            ? TransactionType::Deposit
            : TransactionType::Withdrawal;

        $account = $this->getAccountFromTransaction($company, $transaction, $transactionType->value);

        $postedAt = $transaction->posted_at ?? $transaction->date ?? now()->toDateTimeString();

        $record = Transaction::create([
            'company_id' => $company->id,
            'account_id' => $account->id,
            'bank_account_id' => $bankAccount->id,
            'plaid_transaction_id' => $transaction->plaid_transaction_id ?? null,
            'contact_id' => $transaction->contact_id ?? null,
            'type' => $transactionType,
            'payment_channel' => $transaction->payment_channel ?? null,
            'payment_method' => $transaction->payment_method ?? null,
            'is_payment' => $transaction->is_payment ?? false,
            'description' => $transaction->name ?? $transaction->description ?? '',
            'notes' => $transaction->notes ?? null,
            'reference' => $transaction->reference ?? null,
            'amount' => convert_to_cents(abs($amount)),
            'pending' => $transaction->pending ?? false,
            'reviewed' => $transaction->reviewed ?? false,
            'posted_at' => $postedAt,
            'meta' => $transaction->meta ?? null,
        ]);

        $this->createJournalEntries($record);
    }

    public function createStartingBalanceTransaction(Company $company, BankAccount $bankAccount, float $startingBalance, string $startDate): void
    {
        $isPositive = $startingBalance >= 0;

        $transactionType = $isPositive
            ? TransactionType::Deposit
            : TransactionType::Withdrawal;

        $account = $isPositive
            ? Account::where('company_id', $company->id)
                ->where('category', AccountCategory::Equity)
                ->where('name', 'LIKE', '%Owner%Investment%')
                ->first()
            : Account::where('company_id', $company->id)
                ->where('category', AccountCategory::Equity)
                ->where('name', 'LIKE', '%Owner%Drawings%')
                ->first();

        if (! $account) {
            $account = Account::where('company_id', $company->id)
                ->where('category', AccountCategory::Equity)
                ->first();
        }

        if (! $account) {
            return;
        }

        $record = Transaction::create([
            'company_id' => $company->id,
            'account_id' => $account->id,
            'bank_account_id' => $bankAccount->id,
            'type' => $transactionType,
            'description' => 'Starting Balance',
            'amount' => convert_to_cents(abs($startingBalance)),
            'pending' => false,
            'reviewed' => true,
            'posted_at' => $startDate,
        ]);

        $this->createJournalEntries($record);
    }

    public function createJournalEntries(Transaction $transaction): void
    {
        [$debitAccount, $creditAccount] = $this->determineAccounts($transaction);

        $amount = $transaction->amount;

        $debitEntry = JournalEntry::create([
            'company_id' => $transaction->company_id,
            'transaction_id' => $transaction->id,
            'account_id' => $debitAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => $amount,
            'description' => $transaction->description,
        ]);

        $creditEntry = JournalEntry::create([
            'company_id' => $transaction->company_id,
            'transaction_id' => $transaction->id,
            'account_id' => $creditAccount->id,
            'type' => JournalEntryType::Credit,
            'amount' => $amount,
            'description' => $transaction->description,
        ]);

        $entries = $transaction->journalEntries()->get();

        if (! $entries->areBalanced()) {
            $debitEntry->delete();
            $creditEntry->delete();

            throw new \RuntimeException(
                "Journal entries are not balanced for transaction {$transaction->id}."
            );
        }
    }

    public function updateJournalEntries(Transaction $transaction): void
    {
        $this->deleteJournalEntries($transaction);
        $this->createJournalEntries($transaction);
    }

    public function deleteJournalEntries(Transaction $transaction): void
    {
        JournalEntry::where('transaction_id', $transaction->id)->delete();
    }

    public function determineAccounts(Transaction $transaction): array
    {
        return match ($transaction->type) {
            TransactionType::Deposit => [
                $transaction->account,
                $this->getRevenueAccount($transaction),
            ],
            TransactionType::Withdrawal => [
                $this->getExpenseAccount($transaction),
                $transaction->account,
            ],
            TransactionType::Transfer => [
                $this->getDestinationBankAccount($transaction),
                $transaction->account,
            ],
            default => [
                $transaction->account,
                $transaction->account,
            ],
        };
    }

    public function getAccountFromTransaction(Company $company, object $transaction, string $transactionType): Account
    {
        $category = $transactionType === 'deposit'
            ? AccountCategory::Revenue
            : AccountCategory::Expense;

        $transactionName = $transaction->name ?? $transaction->description ?? '';
        $plaidCategories = $transaction->category ?? [];
        $plaidDetailedCategories = $transaction->personal_finance_category ?? [];

        $candidates = Account::where('company_id', $company->id)
            ->where('category', $category)
            ->where('archived', false)
            ->get();

        if ($candidates->isEmpty()) {
            return Account::where('company_id', $company->id)
                ->where('category', $category)
                ->firstOrFail();
        }

        $bestMatch = null;
        $bestScore = 0;

        foreach ($candidates as $candidate) {
            foreach ($plaidCategories as $plaidCategory) {
                $score = 0;
                similar_text(strtolower($plaidCategory), strtolower($candidate->name), $score);

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $candidate;
                }
            }

            foreach ($plaidDetailedCategories as $detailedCategory) {
                if (is_string($detailedCategory)) {
                    $score = 0;
                    similar_text(strtolower($detailedCategory), strtolower($candidate->name), $score);

                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestMatch = $candidate;
                    }
                }
            }

            $score = 0;
            similar_text(strtolower($transactionName), strtolower($candidate->name), $score);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $candidate;
            }
        }

        return $bestMatch ?? $candidates->first();
    }

    protected function getRevenueAccount(Transaction $transaction): Account
    {
        $account = Account::where('company_id', $transaction->company_id)
            ->where('category', AccountCategory::Revenue)
            ->where('default', true)
            ->first();

        return $account ?? Account::where('company_id', $transaction->company_id)
            ->where('category', AccountCategory::Revenue)
            ->firstOrFail();
    }

    protected function getExpenseAccount(Transaction $transaction): Account
    {
        $account = Account::where('company_id', $transaction->company_id)
            ->where('category', AccountCategory::Expense)
            ->where('default', true)
            ->first();

        return $account ?? Account::where('company_id', $transaction->company_id)
            ->where('category', AccountCategory::Expense)
            ->firstOrFail();
    }

    protected function getDestinationBankAccount(Transaction $transaction): Account
    {
        $bankAccountId = $transaction->destination_bank_account_id ?? $transaction->transfer_to_bank_account_id;

        if ($bankAccountId) {
            $bankAccount = BankAccount::find($bankAccountId);

            if ($bankAccount && $bankAccount->account) {
                return $bankAccount->account;
            }
        }

        return Account::where('company_id', $transaction->company_id)
            ->where('category', AccountCategory::Asset)
            ->where('type', AccountType::CurrentAsset)
            ->firstOrFail();
    }
}
