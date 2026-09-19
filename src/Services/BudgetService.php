<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Contracts\Services\BudgetServiceInterface;
use FinancePack\Contracts\Services\ReportServiceInterface;
use FinancePack\DTO\BudgetProjectionDTO;
use FinancePack\DTO\BudgetLineDTO;
use FinancePack\DTO\BudgetVarianceDTO;
use FinancePack\DTO\BudgetLineVarianceDTO;
use FinancePack\DTO\VarianceAlertDTO;
use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Enums\Accounting\BudgetStatus;
use FinancePack\Enums\Accounting\ItemSeverity;
use FinancePack\Models\Accounting\Budget;
use FinancePack\Models\Accounting\BudgetItem;
use FinancePack\Models\Accounting\BudgetAllocation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BudgetService implements BudgetServiceInterface
{
    protected float $warningThreshold;
    protected float $criticalThreshold;

    public function __construct(
        protected ReportServiceInterface $reportService,
    ) {
        $this->warningThreshold = (float) config('financepack.budget.variance_alert_threshold', 10.0);
        $this->criticalThreshold = (float) config('financepack.budget.critical_variance_threshold', 25.0);
    }

    public function createBudget(array $data, array $lineItems): Budget
    {
        $budget = Budget::create([
            'company_id' => $data['company_id'],
            'name' => $data['name'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'amount' => 0,
            'spent' => 0,
            'status' => BudgetStatus::Draft,
            'notes' => $data['notes'] ?? null,
            'growth_rate' => $data['growth_rate'] ?? 0,
            'currency_code' => $data['currency_code'] ?? config('financepack.default_currency', 'USD'),
            'type' => $data['type'] ?? 'income_statement',
        ]);

        foreach ($lineItems as $index => $item) {
            $budgetItem = BudgetItem::create([
                'budget_id' => $budget->id,
                'account_id' => $item['account_id'] ?? null,
                'name' => $item['name'],
                'amount' => $item['amount'] ?? 0,
                'spent' => 0,
                'notes' => $item['notes'] ?? null,
                'category' => $item['category'] ?? null,
                'account_type' => $item['account_type'] ?? null,
                'growth_rate' => $item['growth_rate'] ?? null,
                'is_recurring' => $item['is_recurring'] ?? true,
                'sort_order' => $item['sort_order'] ?? $index,
            ]);

            $this->createAllocation($budgetItem, $budget->start_date->toDateString(), $item['amount'] ?? 0, 0, false);
        }

        $totalAmount = $budget->items()->sum('amount');
        $budget->update(['amount' => $totalAmount]);

        return $budget->fresh();
    }

    public function generateProjections(Budget $budget): BudgetProjectionDTO
    {
        $months = $this->generateMonths($budget);
        $lines = [];

        $categoryTotals = [];
        $grandTotal = 0;

        foreach ($budget->items()->orderBy('sort_order')->get() as $item) {
            $line = $this->buildBudgetLine($item, $months, $budget);
            $lines[] = $line;

            $catKey = $item->category?->value ?? 'other';
            if (! isset($categoryTotals[$catKey])) {
                $categoryTotals[$catKey] = 0;
            }
            $categoryTotals[$catKey] += $line->totalCents;
            $grandTotal += $line->totalCents;
        }

        return new BudgetProjectionDTO(
            budget: $budget,
            months: $months,
            lines: $lines,
            categoryTotals: $categoryTotals,
            grandTotalCents: $grandTotal,
        );
    }

    public function projectMonthlyAmounts(Budget $budget): array
    {
        $months = $this->generateMonths($budget);
        $projections = [];

        foreach ($budget->items()->orderBy('sort_order')->get() as $item) {
            $baseAmount = $item->amount;
            $growthRate = $this->resolveGrowthRate($item, $budget);
            $monthlyAmounts = [];
            $previousAmount = $baseAmount;

            foreach ($months as $index => $month) {
                if ($index === 0) {
                    $monthlyAmounts[$month] = $baseAmount;
                } else {
                    $projected = (int) round($previousAmount * (1 + $growthRate / 100));
                    $monthlyAmounts[$month] = $projected;
                    $previousAmount = $projected;
                }
            }

            $projections[$item->id] = [
                'item' => $item,
                'amounts' => $monthlyAmounts,
                'growth_rate' => $growthRate,
            ];
        }

        return $projections;
    }

    public function calculateVariance(Budget $budget, string $startDate, string $endDate, float $thresholdPercent = 10.0): BudgetVarianceDTO
    {
        $actuals = $this->getActualAmounts($budget, $startDate, $endDate);
        $variances = [];
        $alerts = [];
        $totalBudgeted = 0;
        $totalActual = 0;

        foreach ($budget->items()->get() as $item) {
            $budgetedAmount = $this->getAmountForPeriod($item, $startDate, $endDate);
            $actualAmount = $actuals[$item->id] ?? 0;

            $variance = $actualAmount - $budgetedAmount;
            $variancePercent = $budgetedAmount > 0
                ? round(($variance / $budgetedAmount) * 100, 2)
                : ($actualAmount > 0 ? 100.0 : 0.0);

            $exceedsThreshold = abs($variancePercent) > $thresholdPercent;

            $variances[] = new BudgetLineVarianceDTO(
                itemName: $item->name,
                period: $startDate . ' to ' . $endDate,
                budgetedCents: $budgetedAmount,
                actualCents: $actualAmount,
                varianceCents: $variance,
                variancePercent: $variancePercent,
                exceedsThreshold: $exceedsThreshold,
            );

            if ($exceedsThreshold) {
                $severity = abs($variancePercent) > $this->criticalThreshold
                    ? ItemSeverity::Critical
                    : ItemSeverity::Warning;

                $direction = $variance > 0 ? 'over' : 'under';

                $alerts[] = new VarianceAlertDTO(
                    itemName: $item->name,
                    period: $startDate . ' to ' . $endDate,
                    budgetedCents: $budgetedAmount,
                    actualCents: $actualAmount,
                    varianceCents: $variance,
                    variancePercent: $variancePercent,
                    severity: $severity,
                    message: "{$item->name} is {$direction} budget by " . abs($variancePercent) . "%",
                );
            }

            $totalBudgeted += $budgetedAmount;
            $totalActual += $actualAmount;
        }

        return new BudgetVarianceDTO(
            budget: $budget,
            variances: $variances,
            alerts: $alerts,
            totalBudgetedCents: $totalBudgeted,
            totalActualCents: $totalActual,
            totalVarianceCents: $totalActual - $totalBudgeted,
        );
    }

    public function getActualAmounts(Budget $budget, string $startDate, string $endDate): array
    {
        $actuals = [];

        $incomeStatement = $this->reportService->buildIncomeStatementReport($startDate, $endDate);

        foreach ($incomeStatement->categories as $category) {
            if (! in_array($category->accounts, [null, []], true)) {
                foreach ($category->accounts as $accountDTO) {
                    $key = $accountDTO->accountId ?? $accountDTO->accountName;
                    $netMovement = $accountDTO->balance?->netMovement ?? '0';
                    $actuals[$key] = $this->parseAmount($netMovement);
                }
            }
        }

        foreach ($budget->items()->get() as $item) {
            if (isset($actuals[$item->account_id])) {
                $item->update(['actual_amount' => $actuals[$item->account_id]]);
            }
        }

        return $actuals;
    }

    public function seedFromFinancialStatement(Budget $budget): void
    {
        $template = $this->getIncomeStatementTemplate();

        $sortOrder = 0;

        foreach ($template as $categoryLabel => $groups) {
            foreach ($groups as $groupName => $items) {
                foreach ($items as $itemName) {
                    BudgetItem::create([
                        'budget_id' => $budget->id,
                        'name' => $itemName,
                        'amount' => 0,
                        'spent' => 0,
                        'category' => $this->mapCategoryLabel($categoryLabel),
                        'account_type' => null,
                        'growth_rate' => null,
                        'is_recurring' => true,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }
        }
    }

    public function updateGrowthRate(BudgetItem $item, float $growthRate): BudgetProjectionDTO
    {
        $item->update(['growth_rate' => $growthRate]);

        return $this->generateProjections($item->budget);
    }

    public function archive(Budget $budget): void
    {
        $budget->update(['status' => BudgetStatus::Archived]);
    }

    protected function generateMonths(Budget $budget): array
    {
        $months = [];
        $current = Carbon::parse($budget->start_date)->startOfMonth();
        $end = Carbon::parse($budget->end_date)->endOfMonth();

        while ($current->lte($end)) {
            $months[] = $current->format('Y-m');
            $current->addMonth();
        }

        return $months;
    }

    protected function buildBudgetLine(BudgetItem $item, array $months, Budget $budget): BudgetLineDTO
    {
        $baseAmount = $item->amount;
        $growthRate = $this->resolveGrowthRate($item, $budget);
        $monthlyAmounts = [];
        $growthRates = [];
        $previousAmount = $baseAmount;
        $total = 0;

        foreach ($months as $index => $month) {
            if ($index === 0) {
                $monthlyAmounts[$month] = $baseAmount;
            } else {
                $projected = (int) round($previousAmount * (1 + $growthRate / 100));
                $monthlyAmounts[$month] = $projected;
                $growthRates[$month] = $growthRate;
                $previousAmount = $projected;
            }
            $total += $monthlyAmounts[$month];
        }

        return new BudgetLineDTO(
            name: $item->name,
            category: $item->category ?? AccountCategory::Expense,
            accountType: $item->account_type,
            monthlyAmounts: $monthlyAmounts,
            growthRates: $growthRates,
            totalCents: $total,
        );
    }

    protected function resolveGrowthRate(BudgetItem $item, Budget $budget): float
    {
        if ($item->growth_rate !== null) {
            return (float) $item->growth_rate;
        }

        if ($item->category) {
            $categoryRates = $budget->getCategoryGrowthRates();
            if (isset($categoryRates[$item->category->value])) {
                return (float) $categoryRates[$item->category->value];
            }
        }

        return (float) $budget->growth_rate;
    }

    protected function getAmountForPeriod(BudgetItem $item, string $startDate, string $endDate): int
    {
        $allocation = BudgetAllocation::where('budget_item_id', $item->id)
            ->where('period', '>=', $startDate)
            ->where('period', '<=', $endDate)
            ->sum('amount');

        return (int) ($allocation * 100);
    }

    protected function createAllocation(BudgetItem $item, string $period, int $amount, int $growthRateApplied, bool $isProjected): void
    {
        BudgetAllocation::create([
            'budget_id' => $item->budget_id,
            'budget_item_id' => $item->id,
            'period' => is_string($period) ? $period : $period,
            'amount' => $amount,
            'spent' => 0,
            'growth_rate_applied' => $growthRateApplied,
            'is_projected' => $isProjected,
        ]);
    }

    protected function parseAmount(string $amount): int
    {
        return (int) ((float) str_replace([',', ' '], '', $amount) * 100);
    }

    protected function mapCategoryLabel(string $label): AccountCategory
    {
        return match (strtolower($label)) {
            'revenue' => AccountCategory::Revenue,
            'cost of goods sold', 'cogs' => AccountCategory::Expense,
            'operating expenses' => AccountCategory::Expense,
            'non-operating expenses' => AccountCategory::Expense,
            default => AccountCategory::Expense,
        };
    }

    protected function getIncomeStatementTemplate(): array
    {
        return [
            'Revenue' => [
                'Operating Revenue' => ['Sales Revenue', 'Service Revenue'],
                'Non-Operating Revenue' => ['Interest Income', 'Other Income'],
            ],
            'Cost of Goods Sold' => [
                'Direct Costs' => ['Direct Materials', 'Direct Labor'],
                'Overhead' => ['Manufacturing Overhead'],
            ],
            'Operating Expenses' => [
                'Administrative' => ['Salaries & Wages', 'Rent Expense', 'Utilities', 'Office Supplies'],
                'Depreciation & Amortization' => ['Depreciation', 'Amortization'],
                'Sales & Marketing' => ['Marketing', 'Advertising'],
                'Other' => ['Insurance', 'Professional Fees', 'Travel'],
            ],
            'Non-Operating Expenses' => [
                'Finance Costs' => ['Interest Expense', 'Bank Charges'],
                'Other' => ['Loss on Disposal', 'Miscellaneous Expense'],
            ],
        ];
    }
}
