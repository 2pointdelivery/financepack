<?php

declare(strict_types=1);

namespace FinancePack\DTO;

use FinancePack\Models\Accounting\Budget;

class BudgetProjectionDTO
{
    public function __construct(
        public Budget $budget,
        public array $months,
        public array $lines,
        public array $categoryTotals,
        public int $grandTotalCents,
    ) {}

    public function toArray(): array
    {
        return [
            'budget_id' => $this->budget->id,
            'months' => $this->months,
            'lines' => array_map(fn ($line) => $line->toArray(), $this->lines),
            'category_totals' => array_map(fn ($total) => $total, $this->categoryTotals),
            'grand_total' => $this->grandTotalCents,
        ];
    }
}
