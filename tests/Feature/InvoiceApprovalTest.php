<?php

declare(strict_types=1);

namespace FinancePack\Tests\Feature;

use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Enums\Accounting\InvoiceStatus;
use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Enums\Accounting\TransactionType;
use FinancePack\Models\Accounting\Account;
use FinancePack\Models\Accounting\Invoice;
use FinancePack\Models\Banking\BankAccount;
use FinancePack\Models\Common\Client;
use FinancePack\Models\Company;
use FinancePack\Tests\TestCase;

class InvoiceApprovalTest extends TestCase
{
    private Company $company;
    private Account $assetAccount;
    private Account $arAccount;
    private Account $revenueAccount;
    private BankAccount $bankAccount;
    private Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = $this->createCompany();

        $this->assetAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Cash',
            'code' => '1000',
        ]);

        $this->arAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Asset,
            'type' => AccountType::CurrentAsset,
            'name' => 'Accounts Receivable',
            'code' => '1200',
        ]);

        $this->revenueAccount = $this->createAccount($this->company, [
            'category' => AccountCategory::Revenue,
            'type' => AccountType::OperatingRevenue,
            'name' => 'Service Revenue',
            'code' => '4000',
            'default' => true,
        ]);

        $this->bankAccount = $this->createBankAccount($this->company, $this->assetAccount);
        $this->client = $this->createClient($this->company);
    }

    public function test_invoice_can_be_approved(): void
    {
        $invoice = Invoice::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'account_id' => $this->assetAccount->id,
            'invoice_number' => 'INV-00001',
            'date' => now(),
            'due_date' => now()->addDays(30),
            'status' => InvoiceStatus::Draft,
            'subtotal' => 100000,
            'tax_total' => 0,
            'discount_total' => 0,
            'total' => 100000,
            'amount_paid' => 0,
            'currency_code' => 'USD',
        ]);

        $this->assertTrue($invoice->canBeApproved());
        $this->assertEquals(InvoiceStatus::Draft, $invoice->status);

        $invoice->approveDraft();

        $invoice->refresh();
        $this->assertEquals(InvoiceStatus::Unsent, $invoice->status);
        $this->assertNotNull($invoice->approved_at);
        $this->assertFalse($invoice->canBeApproved());
    }

    public function test_approval_creates_journal_entries(): void
    {
        $invoice = Invoice::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'account_id' => $this->assetAccount->id,
            'invoice_number' => 'INV-00002',
            'date' => now(),
            'due_date' => now()->addDays(30),
            'status' => InvoiceStatus::Draft,
            'subtotal' => 200000,
            'tax_total' => 0,
            'discount_total' => 0,
            'total' => 200000,
            'amount_paid' => 0,
            'currency_code' => 'USD',
        ]);

        $invoice->approveDraft();

        $transaction = $invoice->approvalTransaction()->first();
        $this->assertNotNull($transaction);
        $this->assertEquals(TransactionType::Journal, $transaction->type);

        $entries = $transaction->journalEntries()->get();
        $this->assertGreaterThanOrEqual(2, $entries->count());

        $debitEntries = $entries->where('type', JournalEntryType::Debit);
        $creditEntries = $entries->where('type', JournalEntryType::Credit);

        $totalDebits = $debitEntries->sum('amount');
        $totalCredits = $creditEntries->sum('amount');

        $this->assertEquals($totalDebits, $totalCredits);
    }

    public function test_payment_recording(): void
    {
        $invoice = Invoice::withoutGlobalScopes()->create([
            'company_id' => $this->company->id,
            'client_id' => $this->client->id,
            'account_id' => $this->assetAccount->id,
            'invoice_number' => 'INV-00003',
            'date' => now(),
            'due_date' => now()->addDays(30),
            'status' => InvoiceStatus::Sent,
            'subtotal' => 150000,
            'tax_total' => 0,
            'discount_total' => 0,
            'total' => 150000,
            'amount_paid' => 0,
            'currency_code' => 'USD',
        ]);

        $invoice->recordPayment([
            'amount' => 150000,
            'payment_method' => 'bank_transfer',
            'reference' => 'PAY-001',
            'posted_at' => now(),
        ]);

        $invoice->refresh();

        $this->assertEquals(150000, $invoice->amount_paid);
        $this->assertEquals(InvoiceStatus::Paid, $invoice->status);
        $this->assertNotNull($invoice->paid_at);

        $payments = $invoice->payments()->get();
        $this->assertEquals(1, $payments->count());

        $payment = $payments->first();
        $this->assertEquals(TransactionType::Deposit, $payment->type);
        $this->assertTrue($payment->is_payment);
        $this->assertEquals(150000, $payment->amount);
    }
}
