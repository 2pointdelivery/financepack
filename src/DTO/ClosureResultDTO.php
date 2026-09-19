<?php

declare(strict_types=1);

namespace FinancePack\DTO;

use FinancePack\Models\Accounting\FinancialClosure;
use Illuminate\Database\Eloquent\Collection;

class ClosureResultDTO
{
    public function __construct(
        public FinancialClosure $closure,
        public Collection $outstandingItems,
        public array $reportSnapshots,
        public int $totalOutstandingCents,
        public int $itemCount,
        public bool $canClose,
    ) {}

    public function toArray(): array
    {
        return [
            'closure_id' => $this->closure->id,
            'status' => $this->closure->status->value,
            'period_start' => $this->closure->period_start->toDateString(),
            'period_end' => $this->closure->period_end->toDateString(),
            'item_count' => $this->itemCount,
            'total_outstanding' => $this->totalOutstandingCents,
            'can_close' => $this->canClose,
            'items' => $this->outstandingItems->map(fn ($item) => [
                'id' => $item->id,
                'type' => $item->item_type->value,
                'label' => $item->label,
                'deep_link' => $item->getDeepLinkUrl(),
                'amount' => $item->amount,
                'severity' => $item->severity->value,
                'resolved' => $item->resolved,
            ])->toArray(),
        ];
    }
}
