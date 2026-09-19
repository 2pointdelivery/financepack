<?php

declare(strict_types=1);

namespace FinancePack\DTO;

use FinancePack\Enums\Accounting\OutstandingItemType;
use FinancePack\Enums\Accounting\ItemSeverity;

class OutstandingItemDTO
{
    public function __construct(
        public OutstandingItemType $type,
        public string $label,
        public string $deepLink,
        public ?int $amountCents,
        public ItemSeverity $severity,
        public string $entityName,
        public ?string $dueDate = null,
        public int $daysOverdue = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'type' => $this->type->value,
            'label' => $this->label,
            'deep_link' => $this->deepLink,
            'amount' => $this->amountCents,
            'severity' => $this->severity->value,
            'entity_name' => $this->entityName,
            'due_date' => $this->dueDate,
            'days_overdue' => $this->daysOverdue,
        ];
    }
}
