<?php

namespace FinancePack\Collections\Accounting;

use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Support\Money;
use Illuminate\Database\Eloquent\Collection;

class JournalEntryCollection extends Collection
{
    public function sumDebits(): Money
    {
        $amount = $this->filter(fn ($entry) => $entry->type === JournalEntryType::Debit)
            ->sum('amount');

        return new Money($amount);
    }

    public function sumCredits(): Money
    {
        $amount = $this->filter(fn ($entry) => $entry->type === JournalEntryType::Credit)
            ->sum('amount');

        return new Money($amount);
    }

    public function areBalanced(): bool
    {
        return $this->sumDebits()->equals($this->sumCredits());
    }
}
