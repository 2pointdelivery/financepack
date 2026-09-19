<?php

namespace FinancePack\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \FinancePack\ValueObjects\Money getDebitBalance(\FinancePack\Models\Accounting\Account $account, string $startDate, string $endDate)
 * @method static \FinancePack\ValueObjects\Money getCreditBalance(\FinancePack\Models\Accounting\Account $account, string $startDate, string $endDate)
 * @method static \FinancePack\ValueObjects\Money getNetMovement(\FinancePack\Models\Accounting\Account $account, string $startDate, string $endDate)
 * @method static \FinancePack\DTO\ReportDTO buildAccountBalanceReport(string $startDate, string $endDate, array $columns = [])
 * @method static \FinancePack\DTO\ReportDTO buildTrialBalanceReport(string $trialBalanceType, string $asOfDate, array $columns = [])
 * @method static \FinancePack\DTO\ReportDTO buildIncomeStatementReport(string $startDate, string $endDate, array $columns = [])
 * @method static \FinancePack\DTO\ReportDTO buildCashFlowStatementReport(string $startDate, string $endDate, array $columns = [])
 * @method static \FinancePack\DTO\ReportDTO buildBalanceSheetReport(string $asOfDate, array $columns = [])
 *
 * @see \FinancePack\Services\AccountService
 */
class Accounting extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'financepack.accounting';
    }
}
