<?php

namespace FinancePack\DTO;

use FinancePack\Contracts\BalanceFormattable;

class AgingBucketDTO implements BalanceFormattable
{
    public function __construct(
        public ?string $current = null,
        public ?string $period1 = null,
        public ?string $period2 = null,
        public ?string $period3 = null,
        public ?string $period4 = null,
        public ?string $overPeriods = null,
        public ?string $total = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            current: $data['current'] ?? null,
            period1: $data['period_1'] ?? null,
            period2: $data['period_2'] ?? null,
            period3: $data['period_3'] ?? null,
            period4: $data['period_4'] ?? null,
            overPeriods: $data['over_periods'] ?? null,
            total: $data['total'] ?? null,
        );
    }
}
