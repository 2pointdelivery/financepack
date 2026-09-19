<?php

namespace FinancePack\DTO;

class AccountTypeDTO
{
    public function __construct(
        public array $accounts = [],
        public ?AccountBalanceDTO $summary = null,
    ) {}
}
