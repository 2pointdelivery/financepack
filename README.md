# FinancePack

A comprehensive Laravel package for double-entry accrual accounting with multi-company support, Plaid integration, and full financial reporting.

## Features

- Double-entry bookkeeping with automatic journal entry creation
- Multi-company data isolation
- Chart of Accounts with customizable account types
- Invoice and Bill management with approval workflows
- Bank account management with Plaid integration
- Multi-currency support with live exchange rates
- Financial reports: Balance Sheet, Income Statement, Trial Balance, Cash Flow Statement, Aging Report
- PDF report generation via Laravel Snappy

## Requirements

- PHP 8.2+
- Laravel 11.x or 12.x
- ext-bcmath
- ext-intl

## Installation

```bash
composer require financepack/financepack
```

Publish the configuration, migrations, and views:

```bash
php artisan vendor:publish --provider="FinancePack\Providers\FinancePackServiceProvider" --tag=financepack-config
php artisan vendor:publish --provider="FinancePack\Providers\FinancePackServiceProvider" --tag=financepack-migrations
php artisan vendor:publish --provider="FinancePack\Providers\FinancePackServiceProvider" --tag=financepack-views
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

After publishing, edit `config/financepack.php`:

```php
return [
    'company_model' => App\Models\Company::class,
    'user_model' => App\Models\User::class,
    'default_currency' => 'USD',

    'currency' => [
        'api_key' => env('CURRENCY_API_KEY'),
        'base_url' => env('CURRENCY_API_BASE_URL', 'https://v6.exchangerate-api.com/v6'),
    ],

    'pdf' => [
        'driver' => env('FINANCEPACK_PDF_DRIVER', 'snappy'),
        'wkhtmltopdf_binary' => env('WKHTMLTOPDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
    ],

    'transactions' => [
        'auto_create_journal_entries' => true,
        'require_approval_for_large_amounts' => false,
        'large_amount_threshold' => 1000000,
    ],
];
```

## Quick Start

### Create a Company

```php
use FinancePack\Models\Company;

$company = Company::create([
    'name' => 'My Company',
    'currency_code' => 'USD',
    'default_timezone' => 'America/New_York',
]);
```

### Create Accounts

```php
use FinancePack\Models\Accounting\Account;
use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;

$cash = Account::create([
    'company_id' => $company->id,
    'category' => AccountCategory::Asset,
    'type' => AccountType::CurrentAsset,
    'code' => '1000',
    'name' => 'Cash',
    'currency_code' => 'USD',
]);

$revenue = Account::create([
    'company_id' => $company->id,
    'category' => AccountCategory::Revenue,
    'type' => AccountType::OperatingRevenue,
    'code' => '4000',
    'name' => 'Sales Revenue',
    'currency_code' => 'USD',
    'default' => true,
]);
```

### Create a Bank Account

```php
use FinancePack\Models\Banking\BankAccount;

$bankAccount = BankAccount::create([
    'company_id' => $company->id,
    'account_id' => $cash->id,
    'name' => 'Main Checking',
    'bank_name' => 'Chase',
    'account_number' => '123456789',
    'routing_number' => '987654321',
    'balance' => 0,
    'currency_code' => 'USD',
]);
```

### Store Transactions

```php
use FinancePack\Services\TransactionService;

$transactionService = app(TransactionService::class);

$transactionService->storeTransaction($company, $bankAccount, (object) [
    'name' => 'Client Payment',
    'amount' => 500000, // $5,000.00 in cents
    'posted_at' => now()->toDateTimeString(),
    'reference' => 'PAY-001',
]);
```

## API Reference

### AccountService

```php
use FinancePack\Services\AccountService;

$accountService = app(AccountService::class);

// Get debit balance for an account within a date range
$debitBalance = $accountService->getDebitBalance($account, '2024-01-01', '2024-12-31');

// Get credit balance
$creditBalance = $accountService->getCreditBalance($account, '2024-01-01', '2024-12-31');

// Get net movement (debit - credit for asset/expense, credit - debit for others)
$netMovement = $accountService->getNetMovement($account, '2024-01-01', '2024-12-31');

// Get starting balance (sum of all entries before startDate)
$startingBalance = $accountService->getStartingBalance($account, '2024-01-01');

// Get ending balance (starting + net movement)
$endingBalance = $accountService->getEndingBalance($account, '2024-01-01', '2024-12-31');

// Get all balances as an array
$balances = $accountService->getBalances($account, '2024-01-01', '2024-12-31');
// Returns: ['debit_balance' => Money, 'credit_balance' => Money, 'net_movement' => Money, 'starting_balance' => Money, 'ending_balance' => Money]

// Get account balances builder for reporting
$builder = $accountService->getAccountBalances('2024-01-01', '2024-12-31');
$results = $builder->get();
```

### TransactionService

```php
use FinancePack\Services\TransactionService;

$transactionService = app(TransactionService::class);

// Store a single transaction (auto-creates journal entries)
$transactionService->storeTransaction($company, $bankAccount, $transactionData);

// Store multiple transactions
$transactionService->storeTransactions($company, $bankAccount, $transactions);

// Manually create journal entries for a transaction
$transactionService->createJournalEntries($transaction);

// Update journal entries (delete + recreate)
$transactionService->updateJournalEntries($transaction);

// Determine which accounts to debit/credit
[$debitAccount, $creditAccount] = $transactionService->determineAccounts($transaction);
```

### ReportService

```php
use FinancePack\Services\ReportService;

$reportService = app(ReportService::class);

// Balance Sheet
$balanceSheet = $reportService->buildBalanceSheetReport('2024-12-31');

// Income Statement
$incomeStatement = $reportService->buildIncomeStatementReport('2024-01-01', '2024-12-31');

// Trial Balance
$trialBalance = $reportService->buildTrialBalanceReport('preClosing', '2024-12-31');

// Cash Flow Statement
$cashFlow = $reportService->buildCashFlowStatementReport('2024-01-01', '2024-12-31');

// Account Balance Report
$accountBalances = $reportService->buildAccountBalanceReport('2024-01-01', '2024-12-31');

// Aging Report
$agingReport = $reportService->buildAgingReport('2024-12-31', 'client');
```

## Multi-Company Setup

FinancePack supports multi-tenant data isolation. Each model using the `CompanyOwned` trait automatically scopes queries to the current company.

### Company Model

Your Company model must be configured in `config/financepack.php`:

```php
'company_model' => App\Models\Company::class,
```

### Setting the Current Company

Set the current company via the authenticated user:

```php
// In your auth logic
auth()->user()->current_company_id = $company->id;
```

All FinancePack models will automatically filter by this company ID.

### Switching Companies

```php
// Switch to a different company
auth()->user()->current_company_id = $newCompany->id;
```

## Chart of Accounts Customization

FinancePack includes a default chart of accounts that can be customized:

```php
use FinancePack\Services\ChartOfAccountsService;

$chartService = app(ChartOfAccountsService::class);

// Import a custom chart of accounts
$chartService->import($companyId, $accountsArray);
```

### Account Categories

| Category | Normal Balance | Description |
|----------|---------------|-------------|
| Asset | Debit | Resources owned by the business |
| Liability | Credit | Obligations owed by the business |
| Equity | Credit | Owner's claim on assets |
| Revenue | Credit | Income from business operations |
| Expense | Debit | Costs of doing business |

### Account Types

Each category has specific account types:

- **Assets**: Current Asset, Non-Current Asset, Contra Asset
- **Liabilities**: Current Liability, Non-Current Liability, Contra Liability
- **Equity**: Equity, Contra Equity
- **Revenue**: Operating Revenue, Non-Operating Revenue, Contra Revenue
- **Expenses**: Operating Expense, Non-Operating Expense, Contra Expense

## Multi-Currency Support

FinancePack supports multi-currency transactions with live exchange rates.

### Configuration

```php
'currency' => [
    'api_key' => env('CURRENCY_API_KEY'),
    'base_url' => env('CURRENCY_API_BASE_URL', 'https://v6.exchangerate-api.com/v6'),
],
```

### Using Currency Conversion

```php
use FinancePack\Utilities\Currency\CurrencyConverter;

// Convert between currencies
$converted = CurrencyConverter::convert(10000, 'USD', 'EUR');

// Format money values
$formatted = CurrencyConverter::formatToMoney(50000, 'USD'); // $500.00
```

### Money Value Object

```php
use FinancePack\ValueObjects\Money;

$money = new Money(50000, 'USD');

$money->getAmount();      // 50000 (in cents)
$money->getCurrencyCode(); // 'USD'
$money->format();          // '$500.00'
$money->formatSimple();    // '500.00'
```

## Plaid Integration

Connect bank accounts and sync transactions via Plaid.

### Configuration

```php
// config/financepack-plaid.php
return [
    'client_id' => env('PLAID_CLIENT_ID'),
    'client_secret' => env('PLAID_CLIENT_SECRET'),
    'environment' => env('PLAID_ENVIRONMENT', 'sandbox'),
    'webhook_url' => env('PLAID_WEBHOOK_URL'),
];
```

### Connecting a Bank Account

```php
use FinancePack\Services\PlaidService;

$plaidService = app(PlaidService::class);

// Exchange public token for access token
$accessToken = $plaidService->exchangePublicToken($publicToken);

// Create a bank account from Plaid item
$bankAccount = $plaidService->createBankAccount($company, $institutionId, $accessToken, $itemId);
```

### Syncing Transactions

```php
// Sync transactions from Plaid
$transactions = $plaidService->getTransactions($bankAccount, $startDate, $endDate);

// Store synced transactions
$transactionService->storeTransactions($company, $bankAccount, $transactions);
```

## PDF Report Generation

FinancePack uses Laravel Snappy for PDF generation.

### Configuration

```php
'pdf' => [
    'driver' => 'snappy',
    'wkhtmltopdf_binary' => '/usr/local/bin/wkhtmltopdf',
],
```

### Generating PDFs

```php
use Barryvdh\Snappy\Facades\SnappyPdf;

// Generate PDF from a Blade view
$pdf = SnappyPdf::loadView('financepack::reports.balance-sheet', [
    'report' => $report,
    'company' => $company,
])->download('balance-sheet.pdf');
```

### Customizing Report Views

Publish the views to customize:

```bash
php artisan vendor:publish --provider="FinancePack\Providers\FinancePackServiceProvider" --tag=financepack-views
```

Report views are located in `resources/views/vendor/financepack/reports/`.

## Testing

### Run Tests

```bash
cd packages/financepack
./vendor/bin/pest
```

### Test Structure

```
tests/
├── Pest.php                          # Pest configuration
├── TestCase.php                      # Base test case
├── Unit/
│   └── Services/
│       ├── AccountServiceTest.php    # Account balance tests
│       └── TransactionServiceTest.php # Transaction journal entry tests
└── Feature/
    ├── DoubleEntryBookkeepingTest.php # Double-entry verification
    ├── InvoiceApprovalTest.php       # Invoice workflow tests
    ├── MultiCompanyTest.php          # Data isolation tests
    └── FinancialReportsTest.php      # Report generation tests
```

### Writing Tests

The base `TestCase` provides helper methods:

```php
use FinancePack\Tests\TestCase;

class MyTest extends TestCase
{
    public function test_something(): void
    {
        $company = $this->createCompany();
        $account = $this->createAccount($company, ['category' => 'asset']);
        $bankAccount = $this->createBankAccount($company, $account);
        $client = $this->createClient($company);
        $vendor = $this->createVendor($company);
    }
}
```

## Enums Reference

### TransactionType

| Value | Description |
|-------|-------------|
| `deposit` | Money coming in |
| `withdrawal` | Money going out |
| `journal` | Manual journal entry |
| `transfer` | Between bank accounts |

### InvoiceStatus

| Value | Description |
|-------|-------------|
| `draft` | Not yet approved |
| `unsent` | Approved, not sent |
| `sent` | Sent to client |
| `viewed` | Client viewed |
| `partial` | Partially paid |
| `paid` | Fully paid |
| `overdue` | Past due date |
| `overpaid` | Paid more than owed |
| `void` | Cancelled |

### BillStatus

| Value | Description |
|-------|-------------|
| `draft` | Not yet approved |
| `open` | Approved, awaiting payment |
| `partial` | Partially paid |
| `paid` | Fully paid |
| `overdue` | Past due date |
| `void` | Cancelled |

## License

MIT
