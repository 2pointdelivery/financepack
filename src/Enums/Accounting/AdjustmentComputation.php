<?php

namespace FinancePack\Enums\Accounting;

use FinancePack\Enums\Concerns\ParsesEnum;

enum AdjustmentComputation: string
{
    use ParsesEnum;

    case Fixed = 'fixed';
    case Percentage = 'percentage';

    public function getLabel(): string
    {
        return match ($this) {
            self::Fixed => 'Fixed',
            self::Percentage => 'Percentage',
        };
    }

    public function isFixed(): bool
    {
        return $this === self::Fixed;
    }

    public function isPercentage(): bool
    {
        return $this === self::Percentage;
    }
}
