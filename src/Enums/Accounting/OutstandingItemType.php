<?php

declare(strict_types=1);

namespace FinancePack\Enums\Accounting;

enum OutstandingItemType: string
{
    case UnpaidInvoice = 'unpaid_invoice';
    case UnpaidBill = 'unpaid_bill';
    case UnmatchedJournal = 'unmatched_journal';
    case OverduePayment = 'overdue_payment';
    case PendingReconciliation = 'pending_reconciliation';
    case DraftDocument = 'draft_document';

    public function getLabel(): string
    {
        return match ($this) {
            self::UnpaidInvoice => 'Unpaid Invoice',
            self::UnpaidBill => 'Unpaid Bill',
            self::UnmatchedJournal => 'Unmatched Journal Entry',
            self::OverduePayment => 'Overdue Payment',
            self::PendingReconciliation => 'Pending Bank Reconciliation',
            self::DraftDocument => 'Draft Document',
        };
    }

    public function getDeepLinkPrefix(): string
    {
        return match ($this) {
            self::UnpaidInvoice => '/invoices',
            self::UnpaidBill => '/bills',
            self::UnmatchedJournal => '/journal-entries',
            self::OverduePayment => '/payments',
            self::PendingReconciliation => '/bank-accounts',
            self::DraftDocument => '/documents',
        };
    }
}
