<?php

namespace FinancePack\DTO;

class EntityReportDTO
{
    public function __construct(
        public string $name,
        public ?int $id = null,
        public ?AgingBucketDTO $aging = null,
        public ?EntityBalanceDTO $balance = null,
        public ?PaymentMetricsDTO $paymentMetrics = null,
    ) {}
}
