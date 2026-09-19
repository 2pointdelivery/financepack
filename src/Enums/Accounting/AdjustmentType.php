<?php

namespace FinancePack\Enums\Accounting;

enum AdjustmentType: string
{
    case Withholding = 'withholding';
    case VAT = 'vat';

    public function getLabel(): string
    {
        return match ($this) {
            self::Withholding => 'Withholding',
            self::VAT => 'VAT',
        };
    }
}
