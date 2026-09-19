<?php

declare(strict_types=1);

namespace FinancePack\DTO;

use FinancePack\Models\Accounting\Budget;

class BudgetVarianceDTO
{
    public function __construct(
        public Budget $budget,
        public array $variances,
        public array $alerts,
        public int $totalBudgetedCents,
        public int $totalActualCents,
        public int $totalVarianceCents,
    ) {}

    public function toArray(): array
    {
        return [
            'budget_id' => $this->budget->id,
            'variances' => array_map(fn ($v) => $v->toArray(), $this->variances),
            'alerts' => array_map(fn ($a) => $a->toArray(), $this->alerts),
            'total_budgeted' => $this->totalBudgetedCents,
            'total_actual' => $this->totalActualCents,
            'total_variance' => $this->totalVarianceCents,
        ];
    }
}
