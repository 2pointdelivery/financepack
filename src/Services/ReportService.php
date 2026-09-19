<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Contracts\BalanceFormattable;
use FinancePack\Contracts\Services\ReportServiceInterface;
use FinancePack\DTO\AccountBalanceDTO;
use FinancePack\DTO\AccountCategoryDTO;
use FinancePack\DTO\AccountDTO;
use FinancePack\DTO\AccountTransactionDTO;
use FinancePack\DTO\AccountTypeDTO;
use FinancePack\DTO\AgingBucketDTO;
use FinancePack\DTO\CashFlowOverviewDTO;
use FinancePack\DTO\EntityBalanceDTO;
use FinancePack\DTO\EntityReportDTO;
use FinancePack\DTO\ReportDTO;
use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Models\Accounting\Account;
use FinancePack\Support\Column;
use FinancePack\ValueObjects\Money;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService implements ReportServiceInterface
{
    public function __construct(
        protected AccountService $accountService,
    ) {}

    public function buildAccountBalanceReport(string $startDate, string $endDate, array $columns = []): ReportDTO
    {
        $accountBalances = $this->accountService->getAccountBalances($startDate, $endDate)->get();

        $grouped = $accountBalances->groupBy('category');

        $categories = [];
        $overallTotals = ['debit_balance' => 0, 'credit_balance' => 0, 'net_movement' => 0, 'starting_balance' => 0, 'ending_balance' => 0];

        foreach (AccountCategory::getOrderedCategories() as $category) {
            $accounts = $grouped->get($category->value, collect());

            if ($accounts->isEmpty()) {
                continue;
            }

            $typeGroups = $accounts->groupBy('type');
            $types = [];

            $categoryTotals = ['debit_balance' => 0, 'credit_balance' => 0, 'net_movement' => 0, 'starting_balance' => 0, 'ending_balance' => 0];

            foreach ($typeGroups as $typeValue => $typeAccounts) {
                $accountType = AccountType::from($typeValue);
                $typeBalance = ['debit_balance' => 0, 'credit_balance' => 0, 'net_movement' => 0];

                $accountDTOs = $typeAccounts->map(function ($account) use ($category, $startDate, $endDate, &$typeBalance) {
                    $balances = $this->accountService->getBalances(
                        Account::withoutGlobalScopes()->find($account->id),
                        $startDate,
                        $endDate
                    );

                    $netMovement = $category->isNormalDebitBalance()
                        ? ($balances['debit_balance']->getAmount() - $balances['credit_balance']->getAmount())
                        : ($balances['credit_balance']->getAmount() - $balances['debit_balance']->getAmount());

                    $typeBalance['debit_balance'] += $balances['debit_balance']->getAmount();
                    $typeBalance['credit_balance'] += $balances['credit_balance']->getAmount();
                    $typeBalance['net_movement'] += $netMovement;

                    $balanceData = [
                        'debit_balance' => $balances['debit_balance']->formatSimple(),
                        'credit_balance' => $balances['credit_balance']->formatSimple(),
                        'net_movement' => money($netMovement, $account->currency_code)->formatSimple(),
                    ];

                    if ($category->isReal()) {
                        $balanceData['starting_balance'] = $balances['starting_balance']->formatSimple();
                        $balanceData['ending_balance'] = $balances['ending_balance']->formatSimple();
                        $typeBalance['starting_balance'] = ($typeBalance['starting_balance'] ?? 0) + $balances['starting_balance']->getAmount();
                        $typeBalance['ending_balance'] = ($typeBalance['ending_balance'] ?? 0) + $balances['ending_balance']->getAmount();
                    }

                    return new AccountDTO(
                        accountName: $account->name,
                        accountCode: $account->code,
                        accountId: $account->id,
                        balance: AccountBalanceDTO::fromArray($balanceData),
                    );
                });

                $types[$typeValue] = new AccountTypeDTO(
                    accounts: $accountDTOs->toArray(),
                    summary: AccountBalanceDTO::fromArray([
                        'debit_balance' => format_cents_to_money($typeBalance['debit_balance']),
                        'credit_balance' => format_cents_to_money($typeBalance['credit_balance']),
                        'net_movement' => format_cents_to_money($typeBalance['net_movement']),
                    ]),
                );

                foreach ($typeBalance as $key => $value) {
                    $categoryTotals[$key] = ($categoryTotals[$key] ?? 0) + $value;
                }
            }

            foreach ($categoryTotals as $key => $value) {
                $overallTotals[$key] += $value;
            }

            $categories[] = new AccountCategoryDTO(
                accounts: $accountDTOs->toArray(),
                summary: AccountBalanceDTO::fromArray([
                    'debit_balance' => format_cents_to_money($categoryTotals['debit_balance']),
                    'credit_balance' => format_cents_to_money($categoryTotals['credit_balance']),
                    'net_movement' => format_cents_to_money($categoryTotals['net_movement']),
                ]),
                types: $types,
            );
        }

        return new ReportDTO(
            categories: $categories,
            overallTotal: AccountBalanceDTO::fromArray([
                'debit_balance' => format_cents_to_money($overallTotals['debit_balance']),
                'credit_balance' => format_cents_to_money($overallTotals['credit_balance']),
                'net_movement' => format_cents_to_money($overallTotals['net_movement']),
            ]),
            fields: $this->getBalanceReportFields($columns),
            startDate: Carbon::parse($startDate),
            endDate: Carbon::parse($endDate),
        );
    }

    public function buildAccountTransactionsReport(string $startDate, string $endDate, ?array $columns = null, ?string $accountId = 'all', ?string $entityId = 'all'): ReportDTO
    {
        $query = DB::table('transactions')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id')
            ->leftJoin('clients', 'transactions.contact_id', '=', 'clients.id')
            ->where('transactions.posted_at', '>=', $startDate)
            ->where('transactions.posted_at', '<=', $endDate)
            ->select(
                'transactions.id',
                'transactions.posted_at as date',
                'transactions.description',
                'transactions.type',
                'transactions.amount',
                'accounts.id as account_id',
                'accounts.name as account_name',
                'accounts.code as account_code',
                'accounts.category as account_category',
                'accounts.currency_code',
                'clients.id as entity_id',
                'clients.name as entity_name'
            )
            ->orderBy('transactions.posted_at');

        if ($accountId !== 'all' && $accountId !== null) {
            $query->where('transactions.account_id', $accountId);
        }

        if ($entityId !== 'all' && $entityId !== null) {
            $query->where('transactions.contact_id', $entityId);
        }

        $transactions = $query->get();

        $grouped = $transactions->groupBy('account_id');
        $categories = [];

        foreach (AccountCategory::getOrderedCategories() as $category) {
            $categoryAccounts = $grouped->filter(fn ($txns) => $txns->first()->account_category === $category->value);

            if ($categoryAccounts->isEmpty()) {
                continue;
            }

            $typeGroups = [];
            $categoryTotalDebit = 0;
            $categoryTotalCredit = 0;

            foreach ($categoryAccounts as $accountTransactions) {
                $firstTxn = $accountTransactions->first();
                $accountType = $firstTxn->type;
                $currencyCode = $firstTxn->currency_code;
                $runningBalance = 0;

                $account = Account::withoutGlobalScopes()->find($firstTxn->account_id);

                if ($account && $category->isReal()) {
                    $startingBalance = $this->accountService->getStartingBalance($account, $startDate);
                    $runningBalance = $startingBalance?->getAmount() ?? 0;
                }

                $accountDTOs = [];

                foreach ($accountTransactions as $txn) {
                    $amount = (int) $txn->amount;
                    $isDebit = in_array($txn->type, ['deposit', 'journal'], true);

                    $debit = $isDebit ? $amount : 0;
                    $credit = ! $isDebit ? $amount : 0;
                    $runningBalance += $isDebit ? $amount : -$amount;

                    $categoryTotalDebit += $debit;
                    $categoryTotalCredit += $credit;

                    $accountDTOs[] = new AccountTransactionDTO(
                        id: (int) $txn->id,
                        date: $txn->date,
                        description: $txn->description,
                        debit: $debit > 0 ? format_cents_to_money($debit, $currencyCode) : null,
                        credit: $credit > 0 ? format_cents_to_money($credit, $currencyCode) : null,
                        balance: format_cents_to_money($runningBalance, $currencyCode),
                    );
                }

                $typeName = $accountType ?? 'default';
                $typeGroups[$typeName] = new AccountTypeDTO(
                    accounts: $accountDTOs,
                    summary: AccountBalanceDTO::fromArray([
                        'debit_balance' => format_cents_to_money($categoryTotalDebit, $currencyCode),
                        'credit_balance' => format_cents_to_money($categoryTotalCredit, $currencyCode),
                    ]),
                );
            }

            $categories[] = new AccountCategoryDTO(
                accounts: [],
                summary: AccountBalanceDTO::fromArray([
                    'debit_balance' => format_cents_to_money($categoryTotalDebit),
                    'credit_balance' => format_cents_to_money($categoryTotalCredit),
                ]),
                types: $typeGroups,
            );
        }

        return new ReportDTO(
            categories: $categories,
            overallTotal: new AccountBalanceDTO(),
            fields: $columns ?? $this->getTransactionReportFields(),
            startDate: Carbon::parse($startDate),
            endDate: Carbon::parse($endDate),
        );
    }

    public function buildTrialBalanceReport(string $trialBalanceType, string $asOfDate, array $columns = []): ReportDTO
    {
        $startDate = $trialBalanceType === 'postClosing'
            ? $this->accountService->getEarliestTransactionDate()
            : Carbon::now()->startOfYear()->toDateString();

        $accountBalances = $this->accountService->getAccountBalances($startDate, $asOfDate)->get();

        $grouped = $accountBalances->groupBy('category');
        $categories = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach (AccountCategory::getOrderedCategories() as $category) {
            $accounts = $grouped->get($category->value, collect());

            if ($accounts->isEmpty()) {
                continue;
            }

            if ($trialBalanceType === 'postClosing' && $category->isNominal()) {
                continue;
            }

            $accountDTOs = [];
            $typeDebit = 0;
            $typeCredit = 0;

            foreach ($accounts as $accountData) {
                $account = Account::withoutGlobalScopes()->find($accountData->id);

                $balances = $this->accountService->getBalances($account, $startDate, $asOfDate);

                $netMovement = $category->isNormalDebitBalance()
                    ? $balances['debit_balance']->getAmount() - $balances['credit_balance']->getAmount()
                    : $balances['credit_balance']->getAmount() - $balances['debit_balance']->getAmount();

                $debit = $netMovement > 0 ? $netMovement : 0;
                $credit = $netMovement < 0 ? abs($netMovement) : 0;

                $typeDebit += $debit;
                $typeCredit += $credit;

                $accountDTOs[] = new AccountDTO(
                    accountName: $accountData->name,
                    accountCode: $accountData->code,
                    accountId: $accountData->id,
                    balance: AccountBalanceDTO::fromArray([
                        'debit_balance' => $debit > 0 ? format_cents_to_money($debit) : null,
                        'credit_balance' => $credit > 0 ? format_cents_to_money($credit) : null,
                    ]),
                );
            }

            $totalDebit += $typeDebit;
            $totalCredit += $typeCredit;

            $categories[] = new AccountCategoryDTO(
                accounts: $accountDTOs,
                summary: AccountBalanceDTO::fromArray([
                    'debit_balance' => format_cents_to_money($typeDebit),
                    'credit_balance' => format_cents_to_money($typeCredit),
                ]),
            );
        }

        return new ReportDTO(
            categories: $categories,
            overallTotal: AccountBalanceDTO::fromArray([
                'debit_balance' => format_cents_to_money($totalDebit),
                'credit_balance' => format_cents_to_money($totalCredit),
            ]),
            reportType: $trialBalanceType,
            fields: $columns ?: $this->getTrialBalanceFields(),
            startDate: Carbon::parse($startDate),
            endDate: Carbon::parse($asOfDate),
        );
    }

    public function buildIncomeStatementReport(string $startDate, string $endDate, array $columns = []): ReportDTO
    {
        $accountBalances = $this->accountService->getAccountBalances($startDate, $endDate)->get();

        $revenueAccounts = $accountBalances->where('category', AccountCategory::Revenue->value);
        $expenseAccounts = $accountBalances->where('category', AccountCategory::Expense->value);

        $totalRevenue = 0;
        $totalExpenses = 0;
        $categories = [];

        $revenueDTOs = $this->buildNominalAccountDTOs($revenueAccounts, $startDate, $endDate, AccountCategory::Revenue, $totalRevenue);
        $expenseDTOs = $this->buildNominalAccountDTOs($expenseAccounts, $startDate, $endDate, AccountCategory::Expense, $totalExpenses);

        $categories[] = new AccountCategoryDTO(
            accounts: $revenueDTOs,
            summary: AccountBalanceDTO::fromArray([
                'net_movement' => format_cents_to_money($totalRevenue),
            ]),
        );

        $categories[] = new AccountCategoryDTO(
            accounts: $expenseDTOs,
            summary: AccountBalanceDTO::fromArray([
                'net_movement' => format_cents_to_money($totalExpenses),
            ]),
        );

        $netIncome = $totalRevenue - $totalExpenses;

        return new ReportDTO(
            categories: $categories,
            overallTotal: AccountBalanceDTO::fromArray([
                'net_movement' => format_cents_to_money($netIncome),
            ]),
            fields: $columns ?: $this->getIncomeStatementFields(),
            startDate: Carbon::parse($startDate),
            endDate: Carbon::parse($endDate),
        );
    }

    public function buildCashFlowStatementReport(string $startDate, string $endDate, array $columns = []): ReportDTO
    {
        $accountBalances = $this->accountService->getAccountBalances($startDate, $endDate)->get();

        $operatingAccounts = $accountBalances->filter(function ($account) {
            $type = AccountType::tryFrom($account->type);
            return in_array($type, [
                AccountType::OperatingRevenue,
                AccountType::OperatingExpense,
                AccountType::CurrentAsset,
                AccountType::CurrentLiability,
            ], true);
        });

        $investingAccounts = $accountBalances->filter(function ($account) {
            $type = AccountType::tryFrom($account->type);
            return $type === AccountType::NonCurrentAsset;
        });

        $financingAccounts = $accountBalances->filter(function ($account) {
            $type = AccountType::tryFrom($account->type);
            return in_array($type, [
                AccountType::NonCurrentLiability,
                AccountType::Equity,
                AccountType::ContraEquity,
            ], true);
        });

        $operatingTotal = 0;
        $investingTotal = 0;
        $financingTotal = 0;

        $categories = [];

        $categories[] = $this->buildCashFlowCategory(
            'Operating Activities',
            $operatingAccounts,
            $startDate,
            $endDate,
            $operatingTotal
        );

        $categories[] = $this->buildCashFlowCategory(
            'Investing Activities',
            $investingAccounts,
            $startDate,
            $endDate,
            $investingTotal
        );

        $categories[] = $this->buildCashFlowCategory(
            'Financing Activities',
            $financingAccounts,
            $startDate,
            $endDate,
            $financingTotal
        );

        $netCashFlow = $operatingTotal + $investingTotal + $financingTotal;

        return new ReportDTO(
            categories: $categories,
            overallTotal: AccountBalanceDTO::fromArray([
                'net_movement' => format_cents_to_money($netCashFlow),
            ]),
            overview: new CashFlowOverviewDTO(categories: [
                'operating' => format_cents_to_money($operatingTotal),
                'investing' => format_cents_to_money($investingTotal),
                'financing' => format_cents_to_money($financingTotal),
                'net_cash_flow' => format_cents_to_money($netCashFlow),
            ]),
            fields: $columns ?: $this->getCashFlowFields(),
            startDate: Carbon::parse($startDate),
            endDate: Carbon::parse($endDate),
        );
    }

    public function buildBalanceSheetReport(string $asOfDate, array $columns = []): ReportDTO
    {
        $startDate = $this->accountService->getEarliestTransactionDate();
        $accountBalances = $this->accountService->getAccountBalances($startDate, $asOfDate)->get();

        $assetAccounts = $accountBalances->where('category', AccountCategory::Asset->value);
        $liabilityAccounts = $accountBalances->where('category', AccountCategory::Liability->value);
        $equityAccounts = $accountBalances->where('category', AccountCategory::Equity->value);

        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        $assetDTOs = $this->buildRealAccountDTOs($assetAccounts, $startDate, $asOfDate, AccountCategory::Asset, $totalAssets);
        $liabilityDTOs = $this->buildRealAccountDTOs($liabilityAccounts, $startDate, $asOfDate, AccountCategory::Liability, $totalLiabilities);
        $equityDTOs = $this->buildRealAccountDTOs($equityAccounts, $startDate, $asOfDate, AccountCategory::Equity, $totalEquity);

        $retainedEarnings = $this->calculateRetainedEarnings($startDate, $asOfDate);
        $totalEquity += $retainedEarnings->getAmount();

        $categories = [];

        $categories[] = new AccountCategoryDTO(
            accounts: $assetDTOs,
            summary: AccountBalanceDTO::fromArray([
                'ending_balance' => format_cents_to_money($totalAssets),
            ]),
        );

        $categories[] = new AccountCategoryDTO(
            accounts: $liabilityDTOs,
            summary: AccountBalanceDTO::fromArray([
                'ending_balance' => format_cents_to_money($totalLiabilities),
            ]),
        );

        $categories[] = new AccountCategoryDTO(
            accounts: $equityDTOs,
            summary: AccountBalanceDTO::fromArray([
                'ending_balance' => format_cents_to_money($totalEquity),
            ]),
        );

        return new ReportDTO(
            categories: $categories,
            overallTotal: AccountBalanceDTO::fromArray([
                'ending_balance' => format_cents_to_money($totalAssets),
            ]),
            fields: $columns ?: $this->getBalanceSheetFields(),
            startDate: null,
            endDate: Carbon::parse($asOfDate),
        );
    }

    public function buildAgingReport(string $asOfDate, string $entityType, array $columns = [], int $daysPerPeriod = 30, int $numberOfPeriods = 4): ReportDTO
    {
        $asOf = Carbon::parse($asOfDate);
        $buckets = array_fill(0, $numberOfPeriods, 0);
        $bucketLabels = [];

        for ($i = 0; $i < $numberOfPeriods; $i++) {
            $startDay = $i * $daysPerPeriod;
            $endDay = ($i + 1) * $daysPerPeriod;
            $bucketLabels[] = "{$startDay}-{$endDay}";
        }

        $overPeriodsTotal = 0;

        if ($entityType === 'client') {
            $query = $this->accountService->getUnpaidClientInvoices($asOfDate);
        } else {
            $query = $this->accountService->getUnpaidVendorBills($asOfDate);
        }

        $documents = $query->get();

        $entityReports = [];
        $grouped = $documents->groupBy(fn ($doc) => $entityType === 'client' ? $doc->client_id : $doc->vendor_id);

        $currentTotal = 0;

        foreach ($grouped as $entityId => $entityDocuments) {
            $entityName = $entityType === 'client'
                ? $entityDocuments->first()->client_name
                : $entityDocuments->first()->vendor_name;

            $entityBalance = 0;
            $entityBuckets = array_fill(0, $numberOfPeriods, 0);
            $entityOverPeriods = 0;
            $entityCurrent = 0;

            foreach ($entityDocuments as $doc) {
                $balance = (int) ($doc->balance ?? ($doc->total - ($doc->amount_paid ?? 0)));
                $dueDate = Carbon::parse($doc->due_date);
                $daysOverdue = max(0, $asOf->diffInDays($dueDate, false));

                $placed = false;
                for ($i = 0; $i < $numberOfPeriods; $i++) {
                    $startDay = $i * $daysPerPeriod;
                    $endDay = ($i + 1) * $daysPerPeriod;

                    if ($daysOverdue >= $startDay && $daysOverdue < $endDay) {
                        $entityBuckets[$i] += $balance;
                        $placed = true;
                        break;
                    }
                }

                if (! $placed) {
                    if ($daysOverdue < 0) {
                        $entityCurrent += $balance;
                        $currentTotal += $balance;
                    } else {
                        $entityOverPeriods += $balance;
                        $overPeriodsTotal += $balance;
                    }
                }

                $entityBalance += $balance;
            }

            $entityReports[] = new EntityReportDTO(
                name: $entityName,
                id: (int) $entityId,
                aging: AgingBucketDTO::fromArray(array_merge([
                    'current' => format_cents_to_money($entityCurrent),
                    'total' => format_cents_to_money($entityBalance),
                    'over_periods' => format_cents_to_money($entityOverPeriods),
                ], array_combine(
                    array_map(fn ($i) => 'period_' . ($i + 1), range(1, $numberOfPeriods)),
                    array_map(fn ($amount) => format_cents_to_money($amount), $entityBuckets)
                ))),
                balance: EntityBalanceDTO::fromArray([
                    'total_balance' => format_cents_to_money($entityBalance),
                    'unpaid_balance' => format_cents_to_money($entityBalance),
                ]),
            );
        }

        $totalOverall = $currentTotal + array_sum($buckets) + $overPeriodsTotal;

        $summaryBuckets = [
            'current' => format_cents_to_money($currentTotal),
            'total' => format_cents_to_money($totalOverall),
            'over_periods' => format_cents_to_money($overPeriodsTotal),
        ];

        for ($i = 0; $i < $numberOfPeriods; $i++) {
            $summaryBuckets['period_' . ($i + 1)] = format_cents_to_money($buckets[$i]);
        }

        return new ReportDTO(
            categories: [],
            overallTotal: new AccountBalanceDTO(),
            agingSummary: AgingBucketDTO::fromArray($summaryBuckets),
            entityBalanceTotal: EntityBalanceDTO::fromArray([
                'total_balance' => format_cents_to_money($totalOverall),
                'unpaid_balance' => format_cents_to_money($totalOverall),
            ]),
            fields: $columns ?: $this->getAgingFields($bucketLabels),
            startDate: null,
            endDate: Carbon::parse($asOfDate),
        );
    }

    public function calculateRetainedEarnings(?string $startDate, string $endDate): Money
    {
        $accountBalances = $this->accountService->getAccountBalances(
            $startDate ?? $this->accountService->getEarliestTransactionDate(),
            $endDate
        )->get();

        $totalRevenue = 0;
        $totalExpenses = 0;

        foreach ($accountBalances as $accountData) {
            $account = Account::withoutGlobalScopes()->find($accountData->id);
            $category = $accountData->category;

            $balances = $this->accountService->getBalances($account, $startDate ?? $this->accountService->getEarliestTransactionDate(), $endDate);

            if ($category === AccountCategory::Revenue->value) {
                $totalRevenue += $balances['credit_balance']->getAmount() - $balances['debit_balance']->getAmount();
            } elseif ($category === AccountCategory::Expense->value) {
                $totalExpenses += $balances['debit_balance']->getAmount() - $balances['credit_balance']->getAmount();
            }
        }

        return new Money($totalRevenue - $totalExpenses, config('financepack.default_currency', 'USD'));
    }

    public function formatBalances(array $balances, ?string $dtoClass = null, bool $formatZeros = true): BalanceFormattable
    {
        $formatted = [];

        foreach ($balances as $key => $value) {
            if ($value instanceof Money) {
                $formatted[$key] = $formatZeros || $value->getAmount() !== 0
                    ? $value->formatSimple()
                    : null;
            } else {
                $formatted[$key] = $value;
            }
        }

        return match ($dtoClass) {
            AccountBalanceDTO::class => AccountBalanceDTO::fromArray($formatted),
            AgingBucketDTO::class => AgingBucketDTO::fromArray($formatted),
            EntityBalanceDTO::class => EntityBalanceDTO::fromArray($formatted),
            default => AccountBalanceDTO::fromArray($formatted),
        };
    }

    public function calculateAccountBalances(Account $account): array
    {
        $startDate = $this->accountService->getEarliestTransactionDate();
        $endDate = now()->toDateString();

        return $this->accountService->getBalances($account, $startDate, $endDate);
    }

    public function calculateTrialBalances(AccountCategory $category, int $endingBalance): array
    {
        if ($category->isNormalDebitBalance()) {
            return [
                'debit' => max(0, $endingBalance),
                'credit' => max(0, -$endingBalance),
            ];
        }

        return [
            'debit' => max(0, -$endingBalance),
            'credit' => max(0, $endingBalance),
        ];
    }

    protected function buildNominalAccountDTOs(Collection $accounts, string $startDate, string $endDate, AccountCategory $category, int &$total): array
    {
        $dto = [];

        foreach ($accounts as $accountData) {
            $account = Account::withoutGlobalScopes()->find($accountData->id);

            $balances = $this->accountService->getBalances($account, $startDate, $endDate);

            $netMovement = $category->isNormalDebitBalance()
                ? $balances['debit_balance']->getAmount() - $balances['credit_balance']->getAmount()
                : $balances['credit_balance']->getAmount() - $balances['debit_balance']->getAmount();

            $total += $netMovement;

            $dto[] = new AccountDTO(
                accountName: $accountData->name,
                accountCode: $accountData->code,
                accountId: $accountData->id,
                balance: AccountBalanceDTO::fromArray([
                    'debit_balance' => $balances['debit_balance']->formatSimple(),
                    'credit_balance' => $balances['credit_balance']->formatSimple(),
                    'net_movement' => money($netMovement, $account->currency_code)->formatSimple(),
                ]),
            );
        }

        return $dto;
    }

    protected function buildRealAccountDTOs(Collection $accounts, string $startDate, string $endDate, AccountCategory $category, int &$total): array
    {
        $dto = [];

        foreach ($accounts as $accountData) {
            $account = Account::withoutGlobalScopes()->find($accountData->id);

            $balances = $this->accountService->getBalances($account, $startDate, $endDate);

            $endingBalance = $balances['ending_balance']->getAmount();

            $total += $endingBalance;

            $dto[] = new AccountDTO(
                accountName: $accountData->name,
                accountCode: $accountData->code,
                accountId: $accountData->id,
                balance: AccountBalanceDTO::fromArray([
                    'starting_balance' => $balances['starting_balance']->formatSimple(),
                    'debit_balance' => $balances['debit_balance']->formatSimple(),
                    'credit_balance' => $balances['credit_balance']->formatSimple(),
                    'net_movement' => $balances['net_movement']->formatSimple(),
                    'ending_balance' => $balances['ending_balance']->formatSimple(),
                ]),
            );
        }

        return $dto;
    }

    protected function buildCashFlowCategory(string $categoryName, Collection $accounts, string $startDate, string $endDate, int &$total): AccountCategoryDTO
    {
        $dto = [];
        $categoryTotal = 0;

        foreach ($accounts as $accountData) {
            $account = Account::withoutGlobalScopes()->find($accountData->id);

            $balances = $this->accountService->getBalances($account, $startDate, $endDate);

            $netMovement = $account->category->isNormalDebitBalance()
                ? $balances['debit_balance']->getAmount() - $balances['credit_balance']->getAmount()
                : $balances['credit_balance']->getAmount() - $balances['debit_balance']->getAmount();

            $categoryTotal += $netMovement;

            $dto[] = new AccountDTO(
                accountName: $accountData->name,
                accountCode: $accountData->code,
                accountId: $accountData->id,
                balance: AccountBalanceDTO::fromArray([
                    'net_movement' => money($netMovement, $account->currency_code)->formatSimple(),
                ]),
            );
        }

        $total = $categoryTotal;

        return new AccountCategoryDTO(
            accounts: $dto,
            summary: AccountBalanceDTO::fromArray([
                'net_movement' => format_cents_to_money($categoryTotal),
            ]),
        );
    }

    protected function getBalanceReportFields(array $columns): array
    {
        if (! empty($columns)) {
            return $columns;
        }

        return [
            Column::make('code', 'Code'),
            Column::make('name', 'Name'),
            Column::make('debit_balance', 'Debit Balance'),
            Column::make('credit_balance', 'Credit Balance'),
            Column::make('net_movement', 'Net Movement'),
            Column::make('starting_balance', 'Starting Balance'),
            Column::make('ending_balance', 'Ending Balance'),
        ];
    }

    protected function getTransactionReportFields(): array
    {
        return [
            Column::make('date', 'Date'),
            Column::make('description', 'Description'),
            Column::make('debit', 'Debit'),
            Column::make('credit', 'Credit'),
            Column::make('balance', 'Balance'),
        ];
    }

    protected function getTrialBalanceFields(): array
    {
        return [
            Column::make('code', 'Code'),
            Column::make('name', 'Name'),
            Column::make('debit_balance', 'Debit'),
            Column::make('credit_balance', 'Credit'),
        ];
    }

    protected function getIncomeStatementFields(): array
    {
        return [
            Column::make('code', 'Code'),
            Column::make('name', 'Name'),
            Column::make('net_movement', 'Amount'),
        ];
    }

    protected function getCashFlowFields(): array
    {
        return [
            Column::make('code', 'Code'),
            Column::make('name', 'Name'),
            Column::make('net_movement', 'Amount'),
        ];
    }

    protected function getBalanceSheetFields(): array
    {
        return [
            Column::make('code', 'Code'),
            Column::make('name', 'Name'),
            Column::make('starting_balance', 'Beginning Balance'),
            Column::make('ending_balance', 'Ending Balance'),
        ];
    }

    protected function getAgingFields(array $bucketLabels): array
    {
        $fields = [Column::make('name', 'Name')];

        foreach ($bucketLabels as $label) {
            $fields[] = Column::make("period_{$label}", $label);
        }

        $fields[] = Column::make('over_periods', 'Over ' . end($bucketLabels) . ' Days');
        $fields[] = Column::make('total', 'Total');

        return $fields;
    }
}
