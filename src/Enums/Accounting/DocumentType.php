<?php

namespace FinancePack\Enums\Accounting;

enum DocumentType: string
{
    case Invoice = 'invoice';
    case Bill = 'bill';
    case Estimate = 'estimate';
    case RecurringInvoice = 'recurring_invoice';

    public function getLabel(): string
    {
        return match ($this) {
            self::Invoice => 'Invoice',
            self::Bill => 'Bill',
            self::Estimate => 'Estimate',
            self::RecurringInvoice => 'Recurring Invoice',
        };
    }
}
