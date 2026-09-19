<?php

declare(strict_types=1);

namespace FinancePack\Enums\Accounting;

enum ItemSeverity: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Critical = 'critical';

    public function getLabel(): string
    {
        return match ($this) {
            self::Info => 'Info',
            self::Warning => 'Warning',
            self::Critical => 'Critical',
        };
    }
}
