<?php

namespace FinancePack\DTO;

class AccountDTO
{
    public function __construct(
        public string $accountName,
        public ?string $accountCode = null,
        public ?int $accountId = null,
        public ?AccountBalanceDTO $balance = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {}
}
