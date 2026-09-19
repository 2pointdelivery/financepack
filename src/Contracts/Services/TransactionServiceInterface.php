<?php

namespace FinancePack\Contracts\Services;

use FinancePack\Models\Accounting\Account;
use FinancePack\Models\Accounting\Transaction;
use FinancePack\Models\Banking\BankAccount;
use FinancePack\Models\Company;

interface TransactionServiceInterface
{
    public function createStartingBalanceIfNeeded(Company $company, BankAccount $bankAccount, array $transactions, float $currentBalance, string $startDate): void;

    public function storeTransactions(Company $company, BankAccount $bankAccount, array $transactions): void;

    public function storeTransaction(Company $company, BankAccount $bankAccount, object $transaction): void;

    public function createStartingBalanceTransaction(Company $company, BankAccount $bankAccount, float $startingBalance, string $startDate): void;

    public function createJournalEntries(Transaction $transaction): void;

    public function updateJournalEntries(Transaction $transaction): void;

    public function deleteJournalEntries(Transaction $transaction): void;

    public function determineAccounts(Transaction $transaction): array;

    public function getAccountFromTransaction(Company $company, object $transaction, string $transactionType): Account;
}
