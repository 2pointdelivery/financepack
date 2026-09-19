<?php

namespace FinancePack\Events;

use FinancePack\Models\Invoice;
use FinancePack\Models\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaymentRecorded
{
    use Dispatchable, SerializesModels;

    public Invoice $invoice;
    public Transaction $payment;

    public function __construct(Invoice $invoice, Transaction $payment)
    {
        $this->invoice = $invoice;
        $this->payment = $payment;
    }
}
