<?php

namespace FinancePack\Enums\Accounting;

enum AccountCategory: string
{
    case Asset = 'asset';
    case Liability = 'liability';
    case Equity = 'equity';
    case Revenue = 'revenue';
    case Expense = 'expense';

    public function getLabel(): string
    {
        return match ($this) {
            self::Asset => 'Asset',
            self::Liability => 'Liability',
            self::Equity => 'Equity',
            self::Revenue => 'Revenue',
            self::Expense => 'Expense',
        };
    }

    public function getPluralLabel(): string
    {
        return match ($this) {
            self::Asset => 'Assets',
            self::Liability => 'Liabilities',
            self::Equity => 'Equity',
            self::Revenue => 'Revenue',
            self::Expense => 'Expenses',
        };
    }

    public static function fromPluralLabel(string $label): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->getPluralLabel() === $label) {
                return $case;
            }
        }

        return null;
    }

    public function isNormalDebitBalance(): bool
    {
        return in_array($this, [self::Asset, self::Expense], true);
    }

    public function isNormalCreditBalance(): bool
    {
        return ! $this->isNormalDebitBalance();
    }

    public function isNominal(): bool
    {
        return in_array($this, [self::Revenue, self::Expense], true);
    }

    public function isReal(): bool
    {
        return ! $this->isNominal();
    }

    public function getRelevantBalanceFields(): array
    {
        $commonFields = ['debit_balance', 'credit_balance', 'net_movement'];

        return match ($this->isReal()) {
            true => [...$commonFields, 'starting_balance', 'ending_balance'],
            false => $commonFields,
        };
    }

    public static function getOrderedCategories(): array
    {
        return [
            self::Asset,
            self::Liability,
            self::Equity,
            self::Revenue,
            self::Expense,
        ];
    }
}
