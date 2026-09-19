<?php

namespace FinancePack\DTO;

class AccountCategoryDTO
{
    public function __construct(
        public array $accounts = [],
        public ?AccountBalanceDTO $summary = null,
        public ?array $types = null,
    ) {}
}
