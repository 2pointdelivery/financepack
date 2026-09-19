<?php

declare(strict_types=1);

namespace FinancePack\DTO;

use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;

class BudgetLineDTO
{
    public function __construct(
        public string $name,
        public AccountCategory $category,
        public ?AccountType $accountType,
        public array $monthlyAmounts,
        public array $growthRates,
        public int $totalCents,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'category' => $this->category->value,
            'account_type' => $this->accountType?->value,
            'monthly_amounts' => $this->monthlyAmounts,
            'growth_rates' => $this->growthRates,
            'total' => $this->totalCents,
        ];
    }
}
