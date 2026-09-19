<?php

declare(strict_types=1);

namespace FinancePack\Enums\Accounting;

enum ClosureStatus: string
{
    case Draft = 'draft';
    case PendingReview = 'pending_review';
    case Closed = 'closed';
    case Reopened = 'reopened';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::PendingReview => 'Pending Review',
            self::Closed => 'Closed',
            self::Reopened => 'Reopened',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::Draft, self::Reopened], true);
    }

    public function isClosed(): bool
    {
        return $this === self::Closed;
    }
}
