<?php

namespace FinancePack\Enums\Accounting;

enum AdjustmentCategory: string
{
    case Tax = 'tax';
    case Discount = 'discount';

    public function getLabel(): string
    {
        return match ($this) {
            self::Tax => 'Tax',
            self::Discount => 'Discount',
        };
    }

    public function isTax(): bool
    {
        return $this === self::Tax;
    }

    public function isDiscount(): bool
    {
        return $this === self::Discount;
    }
}
