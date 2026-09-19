<?php

declare(strict_types=1);

namespace FinancePack\Enums\Accounting;

enum BudgetType: string
{
    case IncomeStatement = 'income_statement';
    case BalanceSheet = 'balance_sheet';
    case Combined = 'combined';

    public function getLabel(): string
    {
        return match ($this) {
            self::IncomeStatement => 'Income Statement',
            self::BalanceSheet => 'Balance Sheet',
            self::Combined => 'Combined',
        };
    }
}
