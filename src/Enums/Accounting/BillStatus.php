<?php

namespace FinancePack\Enums\Accounting;

enum BillStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Partial = 'partial';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Void = 'void';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Open => 'Open',
            self::Partial => 'Partial',
            self::Paid => 'Paid',
            self::Overdue => 'Overdue',
            self::Void => 'Void',
        };
    }

    public static function unpaidStatuses(): array
    {
        return [self::Open, self::Partial, self::Overdue];
    }

    public static function canBeOverdue(): array
    {
        return [self::Open, self::Partial];
    }

    public function isDraft(): bool
    {
        return $this === self::Draft;
    }

    public function isPaid(): bool
    {
        return $this === self::Paid;
    }

    public function isVoid(): bool
    {
        return $this === self::Void;
    }

    public function isOverdue(): bool
    {
        return $this === self::Overdue;
    }

    public function isUnpaid(): bool
    {
        return in_array($this, self::unpaidStatuses(), true);
    }
}
