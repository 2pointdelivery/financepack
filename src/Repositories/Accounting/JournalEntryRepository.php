<?php

declare(strict_types=1);

namespace FinancePack\Repositories\Accounting;

use FinancePack\Models\Accounting\Account;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class JournalEntryRepository
{
    public function getBalanceForAccount(Account $account, string $startDate, string $endDate): array
    {
        $result = DB::table('journal_entries')
            ->join('transactions', 'journal_entries.transaction_id', '=', 'transactions.id')
            ->where('journal_entries.account_id', $account->id)
            ->where('journal_entries.company_id', $account->company_id)
            ->where('transactions.posted_at', '>=', $startDate)
            ->where('transactions.posted_at', '<=', $endDate)
            ->selectRaw('
                COALESCE(SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END), 0) as debit_balance,
                COALESCE(SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END), 0) as credit_balance
            ', ['debit', 'credit'])
            ->first();

        return [
            'debit_balance' => (int) ($result->debit_balance ?? 0),
            'credit_balance' => (int) ($result->credit_balance ?? 0),
        ];
    }

    public function getBalancesForAccounts(array $accountIds, string $startDate, string $endDate): Collection
    {
        if (empty($accountIds)) {
            return collect();
        }

        $results = DB::table('journal_entries')
            ->join('transactions', 'journal_entries.transaction_id', '=', 'transactions.id')
            ->whereIn('journal_entries.account_id', $accountIds)
            ->where('transactions.posted_at', '>=', $startDate)
            ->where('transactions.posted_at', '<=', $endDate)
            ->select('journal_entries.account_id')
            ->selectRaw('
                COALESCE(SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END), 0) as debit_balance
            ', ['debit'])
            ->selectRaw('
                COALESCE(SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END), 0) as credit_balance
            ', ['credit'])
            ->groupBy('journal_entries.account_id')
            ->get();

        return $results->mapWithKeys(fn ($row) => [
            $row->account_id => [
                'debit_balance' => (int) $row->debit_balance,
                'credit_balance' => (int) $row->credit_balance,
            ],
        ]);
    }
}
