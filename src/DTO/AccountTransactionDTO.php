<?php

namespace FinancePack\DTO;

use FinancePack\Enums\Accounting\TransactionType;

class AccountTransactionDTO
{
    public function __construct(
        public ?int $id = null,
        public ?string $date = null,
        public ?string $description = null,
        public ?string $debit = null,
        public ?string $credit = null,
        public ?string $balance = null,
        public ?TransactionType $type = null,
        public ?string $url = null,
    ) {}
}
