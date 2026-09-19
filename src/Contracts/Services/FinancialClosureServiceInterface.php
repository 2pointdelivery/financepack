<?php

declare(strict_types=1);

namespace FinancePack\Contracts\Services;

use FinancePack\DTO\ClosureResultDTO;
use FinancePack\Models\Accounting\FinancialClosure;
use Illuminate\Support\Collection;

interface FinancialClosureServiceInterface
{
    public function runClosure(string $companyId, string $periodStart, string $periodEnd): ClosureResultDTO;

    public function collectOutstandingItems(string $companyId, string $periodStart, string $periodEnd): Collection;

    public function markPendingReview(int $closureId): FinancialClosure;

    public function closePeriod(int $closureId, ?int $userId): FinancialClosure;

    public function reopenPeriod(int $closureId, ?int $userId, ?string $reason): FinancialClosure;

    public function isPeriodLocked(string $companyId, string $date): bool;

    public function getClosureHistory(string $companyId): Collection;
}
