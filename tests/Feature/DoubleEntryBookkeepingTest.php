<?php

declare(strict_types=1);

namespace FinancePack\Tests\Feature;

use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Enums\Accounting\TransactionType;
use FinancePack\Models\Accounting\Account;
use FinancePack\Models\Accounting\JournalEntry;
use FinancePack\Models\Accounting\Transaction;
use FinancePack\Models\Banking\BankAccount;
use FinancePack\Models\Company;
use FinancePack\Services\TransactionService;
use FinancePack\Tests\TestCase;

class DoubleEntryBookkeepingTest extends TestCase
{
    private Company $company;
    private Account $assetAccount;
    private Account $revenueAccount;
    private Account $expenseAccount;
    private BankAccount $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = $this->createCompany();

        $this->assetAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Cash',
            'code' => '1000',
        ]);

        $this->revenueAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Revenue,
            'type' => AccountType::OperatingRevenue,
            'name' => 'Sales Revenue',
            'code' => '4000',
            'default' => true,
        ]);

        $this->expenseAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Expense,
            'type' => AccountType::OperatingExpense,
            'name' => 'Office Supplies',
            'code' => '6000',
            'default' => true,
        ]);

        $this->bankAccount = $this->createBankAccount($this->company, $this->assetAccount);
    }

    public function test_transaction_creates_balanced_journal_entries(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Revenue from sales',
            'amount' => 150000,
            'posted_at' => now(),
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $transaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => 150000,
            'description' => 'Debit cash',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $transaction->id,
            'account_id' => $this->revenueAccount->id,
            'type' => JournalEntryType::Credit,
            'amount' => 150000,
            'description' => 'Credit revenue',
        ]);

        $entries = $transaction->journalEntries()->get();

        $this->assertEquals(2, $entries->count());
        $this->assertTrue($entries->areBalanced());
    }

    public function test_all_debits_equal_all_credits(): void
    {
        $transactions = [
            ['type' => TransactionType::Deposit, 'amount' => 100000],
            ['type' => TransactionType::Deposit, 'amount' => 50000],
            ['type' => TransactionType::Withdrawal, 'amount' => 30000],
            ['type' => TransactionType::Withdrawal, 'amount' => 20000],
        ];

        foreach ($transactions as $txnData) {
            $transaction = Transaction::withoutGlobalScopes()->create([
                'company_id' => $this->company->id,
                'account_id' => $this->assetAccount->id,
                'bank_account_id' => $this->bankAccount->id,
                'type' => $txnData['type'],
                'description' => "Test {$txnData['type']->value}",
                'amount' => $txnData['amount'],
                'posted_at' => now(),
            ]);

            $debitAccount = $txnData['type'] === TransactionType::Deposit
                ? $this->assetAccount
                : $this->expenseAccount;
            $creditAccount = $txnData['type'] === TransactionType::Deposit
                ? $this->revenueAccount
                : $this->assetAccount;

            JournalEntry::create([
                'company_id' => $this->company->id,
                'transaction_id' => $transaction->id,
                'account_id' => $debitAccount->id,
                'type' => JournalEntryType::Debit,
                'amount' => $txnData['amount'],
                'description' => 'Debit entry',
            ]);

            JournalEntry::create([
                'company_id' => $this->company->id,
                'transaction_id' => $transaction->id,
                'account_id' => $creditAccount->id,
                'type' => JournalEntryType::Credit,
                'amount' => $txnData['amount'],
                'description' => 'Credit entry',
            ]);
        }

        $totalDebits = JournalEntry::withoutGlobalScopes()
            ->where('company_id', $this->company->id)
            ->where('type', JournalEntryType::Debit)
            ->sum('amount');

        $totalCredits = JournalEntry::withoutGlobalScopes()
            ->where('company_id', $this->company->id)
            ->where('type', JournalEntryType::Credit)
            ->sum('amount');

        $this->assertEquals($totalDebits, $totalCredits);
        $this->assertEquals(200000, $totalDebits);
        $this->assertEquals(200000, $totalCredits);
    }

    public function test_journal_entry_collection_are_balanced(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Test collection balance',
            'amount' => 85000,
            'posted_at' => now(),
        ]);

        $debitEntry = JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $transaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => 85000,
            'description' => 'Debit',
        ]);

        $creditEntry = JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $transaction->id,
            'account_id' => $this->revenueAccount->id,
            'type' => JournalEntryType::Credit,
            'amount' => 85000,
            'description' => 'Credit',
        ]);

        $collection = $transaction->journalEntries()->get();

        $this->assertTrue($collection->areBalanced());
        $this->assertEquals(85000, $collection->sumDebits()->getAmount());
        $this->assertEquals(85000, $collection->sumCredits()->getAmount());
    }
}
