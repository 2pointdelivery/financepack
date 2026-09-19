<?php

namespace FinancePack\DTO;

class CashFlowOverviewDTO
{
    public function __construct(
        public array $categories = [],
    ) {}
}
