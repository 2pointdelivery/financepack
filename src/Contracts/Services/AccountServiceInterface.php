<?php

namespace FinancePack\Contracts\Services;

use FinancePack\Models\Accounting\Account;
use FinancePack\ValueObjects\Money;

interface AccountServiceInterface
{
    public function getDebitBalance(Account $account, string $startDate, string $endDate): Money;

    public function getCreditBalance(Account $account, string $startDate, string $endDate): Money;

    public function getNetMovement(Account $account, string $startDate, string $endDate): Money;

    public function getStartingBalance(Account $account, string $startDate, bool $override = false): ?Money;

    public function getEndingBalance(Account $account, string $startDate, string $endDate): ?Money;

    public function getBalances(Account $account, string $startDate, string $endDate): array;

    public function getAccountBalances(string $startDate, string $endDate, array $accountIds = []): \Illuminate\Database\Eloquent\Builder;

    public function getEarliestTransactionDate(): string;
}
