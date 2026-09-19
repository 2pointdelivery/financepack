<?php

namespace Database\Seeders\FinancePack;

use Database\Factories\FinancePack\AccountFactory;
use Database\Factories\FinancePack\BankAccountFactory;
use Database\Factories\FinancePack\BillFactory;
use Database\Factories\FinancePack\ClientFactory;
use Database\Factories\FinancePack\InvoiceFactory;
use Database\Factories\FinancePack\TransactionFactory;
use Database\Factories\FinancePack\VendorFactory;
use FinancePack\Models\Company;
use FinancePack\Services\ChartOfAccountsService;
use Illuminate\Database\Seeder;

class FinancePackSeeder extends Seeder
{
    public function __construct(
        protected ChartOfAccountsService $chartOfAccountsService,
    ) {}

    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['name' => 'Acme Corporation'],
            [
                'currency_code' => 'USD',
                'default_timezone' => 'America/New_York',
                'phone_number' => '+1-555-0100',
                'address' => '123 Business Ave, Suite 100, New York, NY 10001',
            ]
        );

        $this->chartOfAccountsService->createChartOfAccounts($company);

        $cashAccount = $company->accounts()->where('name', 'Cash')->first();
        $bankAccount = BankAccountFactory::new()
            ->forCompany($company->id)
            ->forAccount($cashAccount?->id)
            ->create([
                'name' => 'Main Business Checking',
                'bank_name' => 'Chase',
                'balance' => 50000,
            ]);

        $clients = ClientFactory::new()
            ->forCompany($company->id)
            ->count(5)
            ->create();

        $vendors = VendorFactory::new()
            ->forCompany($company->id)
            ->count(5)
            ->create();

        $invoiceAccounts = $company->accounts()->where('category', 'revenue')->get();

        foreach ($clients as $client) {
            $invoiceCount = rand(2, 5);
            for ($i = 0; $i < $invoiceCount; $i++) {
                $invoice = InvoiceFactory::new()
                    ->forCompany($company->id)
                    ->forClient($client->id)
                    ->create([
                        'invoice_number' => 'INV-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    ]);

                if ($invoice->status === 'paid' || $invoice->status === 'sent') {
                    TransactionFactory::new()
                        ->forCompany($company->id)
                        ->forAccount($invoiceAccounts->random()->id)
                        ->forBankAccount($bankAccount->id)
                        ->credit()
                        ->create([
                            'amount' => $invoice->amount_paid,
                            'description' => 'Payment received for ' . $invoice->invoice_number,
                            'posted_at' => $invoice->date,
                        ]);
                }
            }
        }

        $expenseAccounts = $company->accounts()->where('category', 'expense')->get();

        foreach ($vendors as $vendor) {
            $billCount = rand(1, 3);
            for ($i = 0; $i < $billCount; $i++) {
                $bill = BillFactory::new()
                    ->forCompany($company->id)
                    ->forVendor($vendor->id)
                    ->create([
                        'bill_number' => 'BILL-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                    ]);

                if ($bill->status === 'paid' || $bill->status === 'sent') {
                    TransactionFactory::new()
                        ->forCompany($company->id)
                        ->forAccount($expenseAccounts->random()->id)
                        ->forBankAccount($bankAccount->id)
                        ->debit()
                        ->create([
                            'amount' => $bill->amount_paid,
                            'description' => 'Payment for ' . $bill->bill_number,
                            'posted_at' => $bill->date,
                        ]);
                }
            }
        }

        $this->command->info('FinancePack demo data seeded successfully.');
    }
}
