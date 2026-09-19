<?php

declare(strict_types=1);

namespace FinancePack\Tests\Feature;

use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Enums\Accounting\TransactionType;
use FinancePack\Models\Accounting\Account;
use FinancePack\Models\Accounting\JournalEntry;
use FinancePack\Models\Accounting\Transaction;
use FinancePack\Models\Banking\BankAccount;
use FinancePack\Models\Company;
use FinancePack\Tests\TestCase;

class MultiCompanyTest extends TestCase
{
    public function test_company_data_isolation(): void
    {
        $companyA = $this->createCompany(['name' => 'Company A']);
        $companyB = $this->createCompany(['name' => 'Company B']);

        $accountA = $this->createAccount($companyA, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Cash A',
            'code' => '1000',
        ]);

        $accountB = $this->createAccount($companyB, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Cash B',
            'code' => '1000',
        ]);

        $bankAccountA = $this->createBankAccount($companyA, $accountA);
        $bankAccountB = $this->createBankAccount($companyB, $accountB);

        Transaction::withoutGlobalScopes()->create([
            'company_id' => $companyA->id,
            'account_id' => $accountA->id,
            'bank_account_id' => $bankAccountA->id,
            'type' => TransactionType::Deposit,
            'description' => 'Transaction A',
            'amount' => 100000,
            'posted_at' => now(),
        ]);

        Transaction::withoutGlobalScopes()->create([
            'company_id' => $companyB->id,
            'account_id' => $accountB->id,
            'bank_account_id' => $bankAccountB->id,
            'type' => TransactionType::Deposit,
            'description' => 'Transaction B',
            'amount' => 200000,
            'posted_at' => now(),
        ]);

        $companyATransactions = Transaction::withoutGlobalScopes()
            ->where('company_id', $companyA->id)
            ->get();

        $companyBTransactions = Transaction::withoutGlobalScopes()
            ->where('company_id', $companyB->id)
            ->get();

        $this->assertEquals(1, $companyATransactions->count());
        $this->assertEquals(1, $companyBTransactions->count());
        $this->assertEquals('Transaction A', $companyATransactions->first()->description);
        $this->assertEquals('Transaction B', $companyBTransactions->first()->description);
    }

    public function test_transactions_scoped_to_company(): void
    {
        $companyA = $this->createCompany(['name' => 'Company A']);
        $companyB = $this->createCompany(['name' => 'Company B']);

        $accountA = $this->createAccount($companyA, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Cash',
            'code' => '1000',
        ]);

        $accountB = $this->createAccount($companyB, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Cash',
            'code' => '1000',
        ]);

        $bankAccountA = $this->createBankAccount($companyA, $accountA);
        $bankAccountB = $this->createBankAccount($companyB, $accountB);

        // Create transactions for Company A
        for ($i = 0; $i < 3; $i++) {
            $transaction = Transaction::withoutGlobalScopes()->create([
                'company_id' => $companyA->id,
                'account_id' => $accountA->id,
                'bank_account_id' => $bankAccountA->id,
                'type' => TransactionType::Deposit,
                'description' => "Company A Transaction {$i}",
                'amount' => 10000 * ($i + 1),
                'posted_at' => now()->subDays($i),
            ]);

            JournalEntry::create([
                'company_id' => $companyA->id,
                'transaction_id' => $transaction->id,
                'account_id' => $accountA->id,
                'type' => JournalEntryType::Debit,
                'amount' => 10000 * ($i + 1),
                'description' => 'Debit',
            ]);
        }

        // Create transactions for Company B
        for ($i = 0; $i < 2; $i++) {
            $transaction = Transaction::withoutGlobalScopes()->create([
                'company_id' => $companyB->id,
                'account_id' => $accountB->id,
                'bank_account_id' => $bankAccountB->id,
                'type' => TransactionType::Deposit,
                'description' => "Company B Transaction {$i}",
                'amount' => 15000 * ($i + 1),
                'posted_at' => now()->subDays($i),
            ]);

            JournalEntry::create([
                'company_id' => $companyB->id,
                'transaction_id' => $transaction->id,
                'account_id' => $accountB->id,
                'type' => JournalEntryType::Debit,
                'amount' => 15000 * ($i + 1),
                'description' => 'Debit',
            ]);
        }

        // Verify Company A data isolation
        $companyATransactions = Transaction::withoutGlobalScopes()
            ->where('company_id', $companyA->id)
            ->get();

        $companyAJournalEntries = JournalEntry::withoutGlobalScopes()
            ->where('company_id', $companyA->id)
            ->get();

        $this->assertEquals(3, $companyATransactions->count());
        $this->assertEquals(3, $companyAJournalEntries->count());

        $companyATotalDebit = $companyAJournalEntries
            ->where('type', JournalEntryType::Debit)
            ->sum('amount');

        $this->assertEquals(60000, $companyATotalDebit);

        // Verify Company B data isolation
        $companyBTransactions = Transaction::withoutGlobalScopes()
            ->where('company_id', $companyB->id)
            ->get();

        $companyBJournalEntries = JournalEntry::withoutGlobalScopes()
            ->where('company_id', $companyB->id)
            ->get();

        $this->assertEquals(2, $companyBTransactions->count());
        $this->assertEquals(2, $companyBJournalEntries->count());

        $companyBTotalDebit = $companyBJournalEntries
            ->where('type', JournalEntryType::Debit)
            ->sum('amount');

        $this->assertEquals(45000, $companyBTotalDebit);
    }
}
