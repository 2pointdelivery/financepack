<?php

namespace FinancePack\Enums\Accounting;

enum JournalEntryType: string
{
    case Debit = 'debit';
    case Credit = 'credit';

    public function getLabel(): string
    {
        return match ($this) {
            self::Debit => 'Debit',
            self::Credit => 'Credit',
        };
    }

    public function isDebit(): bool
    {
        return $this === self::Debit;
    }

    public function isCredit(): bool
    {
        return $this === self::Credit;
    }
}
