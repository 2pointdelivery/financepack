<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Models\Accounting\Account;
use Illuminate\Support\Facades\DB;

class ExportService
{
    public function exportAccountBalances(string $startDate, string $endDate, string $format = 'csv'): string
    {
        $accounts = Account::with('journalEntries.transaction')
            ->where('archived', false)
            ->get();

        $data = $accounts->map(function (Account $account) use ($startDate, $endDate) {
            $balances = app(AccountService::class)->getBalances($account, $startDate, $endDate);

            return [
                'code' => $account->code,
                'name' => $account->name,
                'category' => $account->category->getLabel(),
                'type' => $account->type->getLabel(),
                'currency' => $account->currency_code,
                'debit_balance' => $balances['debit_balance']->getAmount(),
                'credit_balance' => $balances['credit_balance']->getAmount(),
                'net_movement' => $balances['net_movement']->getAmount(),
                'starting_balance' => $balances['starting_balance']->getAmount() ?? 0,
                'ending_balance' => $balances['ending_balance']->getAmount() ?? 0,
            ];
        });

        return $this->formatOutput($data->toArray(), $format);
    }

    public function exportTransactions(string $startDate, string $endDate, string $format = 'csv'): string
    {
        $transactions = DB::table('transactions')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->leftJoin('clients', 'transactions.contact_id', '=', 'clients.id')
            ->where('transactions.posted_at', '>=', $startDate)
            ->where('transactions.posted_at', '<=', $endDate)
            ->select(
                'transactions.id',
                'transactions.posted_at',
                'transactions.type',
                'transactions.description',
                'transactions.amount',
                'transactions.reference',
                'transactions.payment_method',
                'accounts.name as account_name',
                'accounts.code as account_code',
                'clients.name as contact_name'
            )
            ->orderBy('transactions.posted_at')
            ->get();

        return $this->formatOutput($transactions->toArray(), $format);
    }

    public function exportJournalEntries(string $startDate, string $endDate, string $format = 'csv'): string
    {
        $entries = DB::table('journal_entries')
            ->join('transactions', 'journal_entries.transaction_id', '=', 'transactions.id')
            ->join('accounts', 'journal_entries.account_id', '=', 'accounts.id')
            ->where('transactions.posted_at', '>=', $startDate)
            ->where('transactions.posted_at', '<=', $endDate)
            ->select(
                'journal_entries.id',
                'transactions.posted_at',
                'transactions.description as transaction_description',
                'accounts.code as account_code',
                'accounts.name as account_name',
                'journal_entries.type',
                'journal_entries.amount',
                'journal_entries.description'
            )
            ->orderBy('transactions.posted_at')
            ->get();

        return $this->formatOutput($entries->toArray(), $format);
    }

    protected function formatOutput(array $data, string $format): string
    {
        return match ($format) {
            'csv' => $this->toCsv($data),
            'json' => json_encode($data, JSON_PRETTY_PRINT),
            default => $this->toCsv($data),
        };
    }

    protected function toCsv(array $data): string
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');

        fputcsv($output, array_keys($data[0]));

        foreach ($data as $row) {
            fputcsv($output, array_values($row));
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }
}
