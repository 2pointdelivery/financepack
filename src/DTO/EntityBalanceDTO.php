<?php

namespace FinancePack\DTO;

use FinancePack\Contracts\BalanceFormattable;

class EntityBalanceDTO implements BalanceFormattable
{
    public function __construct(
        public ?string $totalBalance = null,
        public ?string $paidBalance = null,
        public ?string $unpaidBalance = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            totalBalance: $data['total_balance'] ?? null,
            paidBalance: $data['paid_balance'] ?? null,
            unpaidBalance: $data['unpaid_balance'] ?? null,
        );
    }
}
