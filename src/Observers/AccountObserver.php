<?php

namespace FinancePack\Observers;

use FinancePack\Models\Account;

class AccountObserver
{
    public function created(Account $account): void
    {
        // Dispatch account created event if needed
    }

    public function updated(Account $account): void
    {
        // Dispatch account updated event if needed
    }

    public function deleted(Account $account): void
    {
        // Dispatch account deleted event if needed
    }
}
