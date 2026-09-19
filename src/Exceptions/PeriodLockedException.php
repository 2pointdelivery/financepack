<?php

declare(strict_types=1);

namespace FinancePack\Exceptions;

class PeriodLockedException extends \RuntimeException
{
    public function __construct(string $period)
    {
        parent::__construct("Period {$period} is closed. Transactions cannot be posted to a closed period without override approval.");
    }
}
