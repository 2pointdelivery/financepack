<?php

namespace FinancePack\DTO;

class PaymentMetricsDTO
{
    public function __construct(
        public int $totalDocuments = 0,
        public ?int $onTimeCount = null,
        public ?int $lateCount = null,
        public ?int $avgDaysToPay = null,
        public ?int $avgDaysLate = null,
        public ?string $onTimePaymentRate = null,
    ) {}
}
