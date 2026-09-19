<?php

namespace FinancePack\DTO;

use FinancePack\Contracts\BalanceFormattable;

class AccountBalanceDTO implements BalanceFormattable
{
    public function __construct(
        public ?string $debitBalance = null,
        public ?string $creditBalance = null,
        public ?string $netMovement = null,
        public ?string $startingBalance = null,
        public ?string $endingBalance = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new static(
            debitBalance: $data['debit_balance'] ?? null,
            creditBalance: $data['credit_balance'] ?? null,
            netMovement: $data['net_movement'] ?? null,
            startingBalance: $data['starting_balance'] ?? null,
            endingBalance: $data['ending_balance'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'debit_balance' => $this->debitBalance,
            'credit_balance' => $this->creditBalance,
            'net_movement' => $this->netMovement,
            'starting_balance' => $this->startingBalance,
            'ending_balance' => $this->endingBalance,
        ], fn ($value) => $value !== null);
    }
}
