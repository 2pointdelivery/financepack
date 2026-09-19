<?php

namespace FinancePack\Observers;

use FinancePack\Events\TransactionCreated;
use FinancePack\Events\TransactionDeleted;
use FinancePack\Events\TransactionUpdated;
use FinancePack\Exceptions\PeriodLockedException;
use FinancePack\Models\Accounting\Transaction;
use FinancePack\Services\TransactionService;
use FinancePack\Services\FinancialClosureService;

class TransactionObserver
{
    public function __construct(
        protected TransactionService $transactionService,
        protected FinancialClosureService $closureService,
    ) {}

    public function creating(Transaction $transaction): void
    {
        if ($transaction->posted_at === null) {
            $transaction->posted_at = now();
        }

        if (! $transaction->approved_override && $this->closureService->isPeriodLocked(
            (string) $transaction->company_id,
            $transaction->posted_at->format('Y-m-d')
        )) {
            throw new PeriodLockedException($transaction->posted_at->format('Y-m'));
        }
    }

    public function created(Transaction $transaction): void
    {
        event(new TransactionCreated($transaction));

        $this->transactionService->createJournalEntries($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        event(new TransactionUpdated($transaction));

        $this->transactionService->updateJournalEntries($transaction);
    }

    public function deleted(Transaction $transaction): void
    {
        event(new TransactionDeleted($transaction));

        $this->transactionService->deleteJournalEntries($transaction);
    }
}
