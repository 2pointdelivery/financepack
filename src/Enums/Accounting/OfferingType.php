<?php

namespace FinancePack\Enums\Accounting;

enum OfferingType: string
{
    case Good = 'good';
    case Service = 'service';

    public function getLabel(): string
    {
        return match ($this) {
            self::Good => 'Good',
            self::Service => 'Service',
        };
    }
}
