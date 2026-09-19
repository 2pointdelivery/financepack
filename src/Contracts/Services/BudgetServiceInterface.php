<?php

declare(strict_types=1);

namespace FinancePack\Contracts\Services;

use FinancePack\DTO\BudgetProjectionDTO;
use FinancePack\DTO\BudgetVarianceDTO;
use FinancePack\Models\Accounting\Budget;
use FinancePack\Models\Accounting\BudgetItem;
use Illuminate\Support\Collection;

interface BudgetServiceInterface
{
    public function createBudget(array $data, array $lineItems): Budget;

    public function generateProjections(Budget $budget): BudgetProjectionDTO;

    public function projectMonthlyAmounts(Budget $budget): array;

    public function calculateVariance(Budget $budget, string $startDate, string $endDate, float $thresholdPercent = 10.0): BudgetVarianceDTO;

    public function getActualAmounts(Budget $budget, string $startDate, string $endDate): array;

    public function seedFromFinancialStatement(Budget $budget): void;

    public function updateGrowthRate(BudgetItem $item, float $growthRate): BudgetProjectionDTO;

    public function archive(Budget $budget): void;
}
