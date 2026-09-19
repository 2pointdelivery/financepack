<?php

namespace FinancePack\Observers;

use FinancePack\Models\Invoice;

class InvoiceObserver
{
    public function created(Invoice $invoice): void
    {
        // Dispatch invoice created event if needed
    }

    public function updated(Invoice $invoice): void
    {
        // Dispatch invoice updated event if needed
    }

    public function deleted(Invoice $invoice): void
    {
        // Dispatch invoice deleted event if needed
    }
}
