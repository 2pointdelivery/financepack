<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Contracts\Services\AccountServiceInterface;
use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\InvoiceStatus;
use FinancePack\Enums\Accounting\BillStatus;
use FinancePack\Models\Accounting\Account;
use FinancePack\Repositories\Accounting\JournalEntryRepository;
use FinancePack\ValueObjects\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AccountService implements AccountServiceInterface
{
    public function __construct(
        protected JournalEntryRepository $journalEntryRepository,
    ) {}

    public function getDebitBalance(Account $account, string $startDate, string $endDate): Money
    {
        $balance = $this->journalEntryRepository->getBalanceForAccount($account, $startDate, $endDate);

        return new Money($balance['debit_balance'] ?? 0, $account->currency_code);
    }

    public function getCreditBalance(Account $account, string $startDate, string $endDate): Money
    {
        $balance = $this->journalEntryRepository->getBalanceForAccount($account, $startDate, $endDate);

        return new Money($balance['credit_balance'] ?? 0, $account->currency_code);
    }

    public function getNetMovement(Account $account, string $startDate, string $endDate): Money
    {
        $balance = $this->journalEntryRepository->getBalanceForAccount($account, $startDate, $endDate);

        $debit = $balance['debit_balance'] ?? 0;
        $credit = $balance['credit_balance'] ?? 0;

        $netMovement = $account->category->isNormalDebitBalance()
            ? $debit - $credit
            : $credit - $debit;

        return new Money($netMovement, $account->currency_code);
    }

    public function getStartingBalance(Account $account, string $startDate, bool $override = false): ?Money
    {
        $balance = DB::table('journal_entries')
            ->join('transactions', 'journal_entries.transaction_id', '=', 'transactions.id')
            ->where('journal_entries.account_id', $account->id)
            ->where('journal_entries.company_id', $account->company_id)
            ->where('transactions.posted_at', '<', $startDate)
            ->selectRaw('
                COALESCE(SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END), 0) as total_debit,
                COALESCE(SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END), 0) as total_credit
            ', ['debit', 'credit'])
            ->first();

        $debit = (int) ($balance->total_debit ?? 0);
        $credit = (int) ($balance->total_credit ?? 0);

        $startingBalance = $account->category->isNormalDebitBalance()
            ? $debit - $credit
            : $credit - $debit;

        return new Money($startingBalance, $account->currency_code);
    }

    public function getEndingBalance(Account $account, string $startDate, string $endDate): ?Money
    {
        $startingBalance = $this->getStartingBalance($account, $startDate);
        $netMovement = $this->getNetMovement($account, $startDate, $endDate);

        $endingAmount = ($startingBalance?->getAmount() ?? 0) + $netMovement->getAmount();

        return new Money($endingAmount, $account->currency_code);
    }

    public function getBalances(Account $account, string $startDate, string $endDate): array
    {
        $startingBalance = $this->getStartingBalance($account, $startDate);
        $debitBalance = $this->getDebitBalance($account, $startDate, $endDate);
        $creditBalance = $this->getCreditBalance($account, $startDate, $endDate);
        $netMovement = $this->getNetMovement($account, $startDate, $endDate);
        $endingBalance = $this->getEndingBalance($account, $startDate, $endDate);

        $result = [
            'debit_balance' => $debitBalance,
            'credit_balance' => $creditBalance,
            'net_movement' => $netMovement,
        ];

        if ($account->category->isReal()) {
            $result['starting_balance'] = $startingBalance;
            $result['ending_balance'] = $endingBalance;
        }

        return $result;
    }

    public function getAccountBalances(string $startDate, string $endDate, array $accountIds = []): Builder
    {
        $query = Account::query()
            ->select(
                'accounts.id',
                'accounts.company_id',
                'accounts.subtype_id',
                'accounts.parent_id',
                'accounts.category',
                'accounts.type',
                'accounts.code',
                'accounts.name',
                'accounts.currency_code',
                'accounts.description',
                'accounts.archived',
                'accounts.default',
                DB::raw('
                    COALESCE(
                        (SELECT COALESCE(SUM(CASE WHEN je_pre.type = ? THEN je_pre.amount ELSE 0 END), 0)
                         - COALESCE(SUM(CASE WHEN je_pre.type = ? THEN je_pre.amount ELSE 0 END), 0)
                         FROM journal_entries AS je_pre
                         JOIN transactions AS t_pre ON je_pre.transaction_id = t_pre.id
                         WHERE je_pre.account_id = accounts.id
                         AND je_pre.company_id = accounts.company_id
                         AND t_pre.posted_at < ?),
                    0) AS starting_balance
                ', ['debit', 'credit', $startDate]),
                DB::raw('
                    COALESCE(
                        (SELECT COALESCE(SUM(CASE WHEN je.type = ? THEN je.amount ELSE 0 END), 0)
                         FROM journal_entries AS je
                         JOIN transactions AS t ON je.transaction_id = t.id
                         WHERE je.account_id = accounts.id
                         AND je.company_id = accounts.company_id
                         AND t.posted_at >= ?
                         AND t.posted_at <= ?),
                    0) AS total_debit
                ', ['debit', $startDate, $endDate]),
                DB::raw('
                    COALESCE(
                        (SELECT COALESCE(SUM(CASE WHEN je.type = ? THEN je.amount ELSE 0 END), 0)
                         FROM journal_entries AS je
                         JOIN transactions AS t ON je.transaction_id = t.id
                         WHERE je.account_id = accounts.id
                         AND je.company_id = accounts.company_id
                         AND t.posted_at >= ?
                         AND t.posted_at <= ?),
                    0) AS total_credit
                ', ['credit', $startDate, $endDate])
            );

        if (! empty($accountIds)) {
            $query->whereIn('accounts.id', $accountIds);
        }

        $query->groupBy(
            'accounts.id',
            'accounts.company_id',
            'accounts.subtype_id',
            'accounts.parent_id',
            'accounts.category',
            'accounts.type',
            'accounts.code',
            'accounts.name',
            'accounts.currency_code',
            'accounts.description',
            'accounts.archived',
            'accounts.default'
        );

        return $query;
    }

    public function getEarliestTransactionDate(): string
    {
        $earliest = DB::table('transactions')
            ->min('posted_at');

        return $earliest ? date('Y-m-d', strtotime($earliest)) : date('Y-m-d');
    }

    public function getUnpaidClientInvoices(?string $asOfDate = null): Builder
    {
        $query = DB::table('invoices')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->join('accounts', 'invoices.account_id', '=', 'accounts.id')
            ->whereIn('invoices.status', InvoiceStatus::unpaidStatuses())
            ->select(
                'invoices.id',
                'invoices.company_id',
                'invoices.client_id',
                'invoices.invoice_number',
                'invoices.status',
                'invoices.due_date',
                'invoices.amount',
                'invoices.balance',
                'invoices.currency_code',
                'clients.name as client_name',
                'accounts.name as account_name'
            );

        if ($asOfDate !== null) {
            $query->where('invoices.due_date', '<=', $asOfDate);
        }

        return $query;
    }

    public function getUnpaidVendorBills(?string $asOfDate = null): Builder
    {
        $query = DB::table('bills')
            ->join('vendors', 'bills.vendor_id', '=', 'vendors.id')
            ->join('accounts', 'bills.account_id', '=', 'accounts.id')
            ->whereIn('bills.status', BillStatus::unpaidStatuses())
            ->select(
                'bills.id',
                'bills.company_id',
                'bills.vendor_id',
                'bills.bill_number',
                'bills.status',
                'bills.due_date',
                'bills.amount',
                'bills.balance',
                'bills.currency_code',
                'vendors.name as vendor_name',
                'accounts.name as account_name'
            );

        if ($asOfDate !== null) {
            $query->where('bills.due_date', '<=', $asOfDate);
        }

        return $query;
    }
}
