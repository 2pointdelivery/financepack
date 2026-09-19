<?php

namespace FinancePack\Observers;

use FinancePack\Events\TransactionCreated;
use FinancePack\Events\TransactionDeleted;
use FinancePack\Events\TransactionUpdated;
use FinancePack\Models\Transaction;
use FinancePack\Services\TransactionService;

class TransactionObserver
{
    public function __construct(
        protected TransactionService $transactionService
    ) {}

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
