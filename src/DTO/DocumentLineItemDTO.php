<?php

namespace FinancePack\DTO;

class DocumentLineItemDTO
{
    public function __construct(
        public int $offeringId,
        public string $name,
        public int $quantity,
        public int $unitPrice,
        public int $subtotal,
        public int $total,
        public ?array $adjustments = null,
    ) {}
}
