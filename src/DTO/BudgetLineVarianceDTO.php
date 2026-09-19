<?php

declare(strict_types=1);

namespace FinancePack\DTO;

class BudgetLineVarianceDTO
{
    public function __construct(
        public string $itemName,
        public string $period,
        public int $budgetedCents,
        public ?int $actualCents,
        public ?int $varianceCents,
        public ?float $variancePercent,
        public bool $exceedsThreshold,
    ) {}

    public function toArray(): array
    {
        return [
            'item_name' => $this->itemName,
            'period' => $this->period,
            'budgeted' => $this->budgetedCents,
            'actual' => $this->actualCents,
            'variance' => $this->varianceCents,
            'variance_percent' => $this->variancePercent,
            'exceeds_threshold' => $this->exceedsThreshold,
        ];
    }
}
