<?php

declare(strict_types=1);

namespace FinancePack\Tests\Unit\Services;

use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Enums\Accounting\TransactionType;
use FinancePack\Models\Accounting\Account;
use FinancePack\Models\Accounting\JournalEntry;
use FinancePack\Models\Accounting\Transaction;
use FinancePack\Models\Banking\BankAccount;
use FinancePack\Models\Company;
use FinancePack\Services\TransactionService;
use FinancePack\Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    private TransactionService $transactionService;
    private Company $company;
    private Account $assetAccount;
    private Account $revenueAccount;
    private Account $expenseAccount;
    private BankAccount $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionService = app(TransactionService::class);

        $this->company = $this->createCompany();

        $this->assetAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Cash',
            'code' => '1000',
        ]);

        $this->revenueAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Revenue,
            'type' => AccountType::OperatingRevenue,
            'name' => 'Sales Revenue',
            'code' => '4000',
            'default' => true,
        ]);

        $this->expenseAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Expense,
            'type' => AccountType::OperatingExpense,
            'name' => 'Office Supplies',
            'code' => '6000',
            'default' => true,
        ]);

        $this->bankAccount = $this->createBankAccount($this->company, $this->assetAccount);
    }

    public function test_create_journal_entries_for_deposit(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Test deposit',
            'amount' => 50000,
            'posted_at' => now(),
        ]);

        $this->transactionService->createJournalEntries($transaction);

        $entries = $transaction->journalEntries()->get();

        $this->assertEquals(2, $entries->count());

        $debitEntry = $entries->firstWhere('type', JournalEntryType::Debit);
        $creditEntry = $entries->firstWhere('type', JournalEntryType::Credit);

        $this->assertNotNull($debitEntry);
        $this->assertNotNull($creditEntry);
        $this->assertEquals($this->assetAccount->id, $debitEntry->account_id);
        $this->assertEquals($this->revenueAccount->id, $creditEntry->account_id);
        $this->assertEquals(50000, $debitEntry->amount);
        $this->assertEquals(50000, $creditEntry->amount);
    }

    public function test_create_journal_entries_for_withdrawal(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Withdrawal,
            'description' => 'Test withdrawal',
            'amount' => 25000,
            'posted_at' => now(),
        ]);

        $this->transactionService->createJournalEntries($transaction);

        $entries = $transaction->journalEntries()->get();

        $this->assertEquals(2, $entries->count());

        $debitEntry = $entries->firstWhere('type', JournalEntryType::Debit);
        $creditEntry = $entries->firstWhere('type', JournalEntryType::Credit);

        $this->assertNotNull($debitEntry);
        $this->assertNotNull($creditEntry);
        $this->assertEquals($this->expenseAccount->id, $debitEntry->account_id);
        $this->assertEquals($this->assetAccount->id, $creditEntry->account_id);
        $this->assertEquals(25000, $debitEntry->amount);
        $this->assertEquals(25000, $creditEntry->amount);
    }

    public function test_journal_entries_are_balanced(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Test balanced entries',
            'amount' => 100000,
            'posted_at' => now(),
        ]);

        $this->transactionService->createJournalEntries($transaction);

        $entries = $transaction->journalEntries()->get();

        $this->assertTrue($entries->areBalanced());
    }

    public function test_store_transaction(): void
    {
        $transactionData = (object) [
            'name' => 'Test Store Transaction',
            'amount' => 75000,
            'posted_at' => now()->toDateTimeString(),
            'reference' => 'REF-001',
            'notes' => 'Test notes',
        ];

        $this->transactionService->storeTransaction(
            $this->company,
            $this->bankAccount,
            $transactionData
        );

        $transaction = Transaction::withoutGlobalScopes()
            ->where('company_id', $this->company->id)
            ->where('description', 'Test Store Transaction')
            ->first();

        $this->assertNotNull($transaction);
        $this->assertEquals(TransactionType::Deposit, $transaction->type);
        $this->assertEquals(75000, $transaction->amount);
        $this->assertEquals('REF-001', $transaction->reference);

        $journalEntries = $transaction->journalEntries()->get();
        $this->assertEquals(2, $journalEntries->count());
        $this->assertTrue($journalEntries->areBalanced());
    }

    public function test_determine_accounts(): void
    {
        $depositTransaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Deposit test',
            'amount' => 50000,
            'posted_at' => now(),
        ]);

        [$debitAccount, $creditAccount] = $this->transactionService->determineAccounts($depositTransaction);

        $this->assertEquals($this->assetAccount->id, $debitAccount->id);
        $this->assertEquals($this->revenueAccount->id, $creditAccount->id);

        $withdrawalTransaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Withdrawal,
            'description' => 'Withdrawal test',
            'amount' => 25000,
            'posted_at' => now(),
        ]);

        [$debitAccount, $creditAccount] = $this->transactionService->determineAccounts($withdrawalTransaction);

        $this->assertEquals($this->expenseAccount->id, $debitAccount->id);
        $this->assertEquals($this->assetAccount->id, $creditAccount->id);
    }
}
