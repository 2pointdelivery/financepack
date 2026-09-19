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
use FinancePack\Services\AccountService;
use FinancePack\Tests\TestCase;

class AccountServiceTest extends TestCase
{
    private AccountService $accountService;
    private Company $company;
    private Account $assetAccount;
    private Account $revenueAccount;
    private BankAccount $bankAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accountService = app(AccountService::class);

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
        ]);
        $this->bankAccount = $this->createBankAccount($this->company, $this->assetAccount);
    }

    public function test_get_debit_balance(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Test deposit',
            'amount' => 50000,
            'posted_at' => '2024-06-15',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $transaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => 50000,
            'description' => 'Test debit',
        ]);

        $balance = $this->accountService->getDebitBalance(
            $this->assetAccount,
            '2024-06-01',
            '2024-06-30'
        );

        $this->assertEquals(50000, $balance->getAmount());
        $this->assertEquals('USD', $balance->getCurrencyCode());
    }

    public function test_get_credit_balance(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->revenueAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Test revenue',
            'amount' => 75000,
            'posted_at' => '2024-06-15',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $transaction->id,
            'account_id' => $this->revenueAccount->id,
            'type' => JournalEntryType::Credit,
            'amount' => 75000,
            'description' => 'Test credit',
        ]);

        $balance = $this->accountService->getCreditBalance(
            $this->revenueAccount,
            '2024-06-01',
            '2024-06-30'
        );

        $this->assertEquals(75000, $balance->getAmount());
        $this->assertEquals('USD', $balance->getCurrencyCode());
    }

    public function test_get_net_movement(): void
    {
        $depositTransaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Test deposit',
            'amount' => 100000,
            'posted_at' => '2024-06-15',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $depositTransaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => 100000,
            'description' => 'Test debit',
        ]);

        $withdrawalTransaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Withdrawal,
            'description' => 'Test withdrawal',
            'amount' => 30000,
            'posted_at' => '2024-06-20',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $withdrawalTransaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Credit,
            'amount' => 30000,
            'description' => 'Test credit',
        ]);

        $netMovement = $this->accountService->getNetMovement(
            $this->assetAccount,
            '2024-06-01',
            '2024-06-30'
        );

        // Asset accounts have normal debit balance: debit - credit
        $this->assertEquals(70000, $netMovement->getAmount());
    }

    public function test_get_starting_balance(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Prior period transaction',
            'amount' => 200000,
            'posted_at' => '2024-05-15',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $transaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => 200000,
            'description' => 'Test debit',
        ]);

        $startingBalance = $this->accountService->getStartingBalance(
            $this->assetAccount,
            '2024-06-01'
        );

        $this->assertNotNull($startingBalance);
        $this->assertEquals(200000, $startingBalance->getAmount());
    }

    public function test_get_ending_balance(): void
    {
        // Starting balance
        $priorTransaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Prior period',
            'amount' => 100000,
            'posted_at' => '2024-05-15',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $priorTransaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => 100000,
            'description' => 'Test debit',
        ]);

        // Current period transaction
        $currentTransaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Current period deposit',
            'amount' => 50000,
            'posted_at' => '2024-06-15',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $currentTransaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => 50000,
            'description' => 'Test debit',
        ]);

        $endingBalance = $this->accountService->getEndingBalance(
            $this->assetAccount,
            '2024-06-01',
            '2024-06-30'
        );

        $this->assertNotNull($endingBalance);
        $this->assertEquals(150000, $endingBalance->getAmount());
    }

    public function test_account_balances_builder(): void
    {
        $transaction = Transaction::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'account_id' => $this->assetAccount->id,
            'bank_account_id' => $this->bankAccount->id,
            'type' => TransactionType::Deposit,
            'description' => 'Test transaction',
            'amount' => 100000,
            'posted_at' => '2024-06-15',
        ]);

        JournalEntry::create([
            'company_id' => $this->company->id,
            'transaction_id' => $transaction->id,
            'account_id' => $this->assetAccount->id,
            'type' => JournalEntryType::Debit,
            'amount' => 100000,
            'description' => 'Test debit',
        ]);

        $builder = $this->accountService->getAccountBalances('2024-06-01', '2024-06-30');
        $results = $builder->get();

        $this->assertNotEmpty($results);

        $cashAccount = $results->firstWhere('id', $this->assetAccount->id);
        $this->assertNotNull($cashAccount);
        $this->assertEquals(100000, $cashAccount->total_debit);
        $this->assertEquals(0, $cashAccount->total_credit);
    }
}
