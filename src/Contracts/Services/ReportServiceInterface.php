<?php

namespace FinancePack\Contracts\Services;

use FinancePack\DTO\ReportDTO;

interface ReportServiceInterface
{
    public function buildAccountBalanceReport(string $startDate, string $endDate, array $columns = []): ReportDTO;

    public function buildAccountTransactionsReport(string $startDate, string $endDate, ?array $columns = null, ?string $accountId = 'all', ?string $entityId = 'all'): ReportDTO;

    public function buildTrialBalanceReport(string $trialBalanceType, string $asOfDate, array $columns = []): ReportDTO;

    public function buildIncomeStatementReport(string $startDate, string $endDate, array $columns = []): ReportDTO;

    public function buildCashFlowStatementReport(string $startDate, string $endDate, array $columns = []): ReportDTO;

    public function buildBalanceSheetReport(string $asOfDate, array $columns = []): ReportDTO;

    public function buildAgingReport(string $asOfDate, string $entityType, array $columns = [], int $daysPerPeriod = 30, int $numberOfPeriods = 4): ReportDTO;

    public function calculateRetainedEarnings(?string $startDate, string $endDate): \FinancePack\ValueObjects\Money;
}
