<?php

namespace FinancePack\Enums\Accounting;

enum DocumentEntityType: string
{
    case Client = 'client';
    case Vendor = 'vendor';

    public function getLabel(): string
    {
        return match ($this) {
            self::Client => 'Client',
            self::Vendor => 'Vendor',
        };
    }
}
