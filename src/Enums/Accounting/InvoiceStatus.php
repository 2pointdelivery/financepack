<?php

namespace FinancePack\Enums\Accounting;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Unsent = 'unsent';
    case Sent = 'sent';
    case Viewed = 'viewed';
    case Partial = 'partial';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Overpaid = 'overpaid';
    case Void = 'void';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Unsent => 'Unsent',
            self::Sent => 'Sent',
            self::Viewed => 'Viewed',
            self::Partial => 'Partial',
            self::Paid => 'Paid',
            self::Overdue => 'Overdue',
            self::Overpaid => 'Overpaid',
            self::Void => 'Void',
        };
    }

    public static function unpaidStatuses(): array
    {
        return [self::Unsent, self::Sent, self::Viewed, self::Partial, self::Overdue];
    }

    public static function canBeOverdue(): array
    {
        return [self::Unsent, self::Sent, self::Viewed, self::Partial];
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

    public function isOverpaid(): bool
    {
        return $this === self::Overpaid;
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
