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
use FinancePack\Services\ReportService;
use FinancePack\Tests\TestCase;

class FinancialReportsTest extends TestCase
{
    private ReportService $reportService;
    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reportService = app(ReportService::class);
        $this->company = $this->createCompany();

        $this->seedAccountsWithTransactions();
    }

    private function seedAccountsWithTransactions(): void
    {
        $cashAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Cash',
            'code' => '1000',
        ]);

        $arAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Accounts Receivable',
            'code' => '1200',
        ]);

        $equipmentAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::NonCurrentAsset,
            'name' => 'Equipment',
            'code' => '1500',
        ]);

        $apAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Liability,
            'type' => AccountType::CurrentLiability,
            'name' => 'Accounts Payable',
            'code' => '2000',
        ]);

        $loanAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Liability,
            'type' => AccountType::NonCurrentLiability,
            'name' => 'Long-term Loan',
            'code' => '2500',
        ]);

        $equityAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Equity,
            'type' => AccountType::Equity,
            'name' => 'Owner Equity',
            'code' => '3000',
        ]);

        $revenueAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Revenue,
            'type' => AccountType::OperatingRevenue,
            'name' => 'Service Revenue',
            'code' => '4000',
            'default' => true,
        ]);

        $expenseAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Expense,
            'type' => AccountType::OperatingExpense,
            'name' => 'Office Supplies',
            'code' => '6000',
            'default' => true,
        ]);

        $bankAccount = $this->createBankAccount($this->company, $cashAccount);

        // Seed journal entries for various accounts
        $entries = [
            [$cashAccount, JournalEntryType::Debit, 500000],
            [$equityAccount, JournalEntryType::Credit, 500000],
            [$cashAccount, JournalEntryType::Debit, 200000],
            [$revenueAccount, JournalEntryType::Credit, 200000],
            [$arAccount, JournalEntryType::Debit, 75000],
            [$revenueAccount, JournalEntryType::Credit, 75000],
            [$expenseAccount, JournalEntryType::Debit, 45000],
            [$cashAccount, JournalEntryType::Credit, 45000],
            [$equipmentAccount, JournalEntryType::Debit, 120000],
            [$cashAccount, JournalEntryType::Credit, 120000],
        ];

        foreach ($entries as [$account, $type, $amount]) {
            $transaction = Transaction::withoutGlobalScopes()->create([
                'company_id' => $this->company->id,
                'account_id' => $account->id,
                'bank_account_id' => $bankAccount->id,
                'type' => $type->isDebit() ? TransactionType::Deposit : TransactionType::Withdrawal,
                'description' => "Seed {$type->value} for {$account->name}",
                'amount' => $amount,
                'posted_at' => '2024-06-15',
            ]);

            JournalEntry::create([
                'company_id' => $this->company->id,
                'transaction_id' => $transaction->id,
                'account_id' => $account->id,
                'type' => $type,
                'amount' => $amount,
                'description' => "Seed {$type->value}",
            ]);
        }
    }

    public function test_build_balance_sheet(): void
    {
        $report = $this->reportService->buildBalanceSheetReport('2024-12-31');

        $this->assertNotNull($report);
        $this->assertNotEmpty($report->categories);
        $this->assertNotNull($report->overallTotal);

        $totalAssets = $report->overallTotal->ending_balance;
        $this->assertNotNull($totalAssets);
    }

    public function test_build_income_statement(): void
    {
        $report = $this->reportService->buildIncomeStatementReport('2024-01-01', '2024-12-31');

        $this->assertNotNull($report);
        $this->assertNotEmpty($report->categories);
        $this->assertNotNull($report->overallTotal);
        $this->assertNotNull($report->overallTotal->net_movement);
    }

    public function test_build_trial_balance(): void
    {
        $report = $this->reportService->buildTrialBalanceReport('preClosing', '2024-12-31');

        $this->assertNotNull($report);
        $this->assertNotEmpty($report->categories);

        $totalDebit = $report->overallTotal->debit_balance;
        $totalCredit = $report->overallTotal->credit_balance;

        $this->assertNotNull($totalDebit);
        $this->assertNotNull($totalCredit);

        // Convert formatted money strings to numeric for comparison
        $debitValue = (float) preg_replace('/[^0-9.\-]/', '', $totalDebit);
        $creditValue = (float) preg_replace('/[^0-9.\-]/', '', $totalCredit);

        $this->assertEqualsWithDelta($debitValue, $creditValue, 0.01);
    }
}
