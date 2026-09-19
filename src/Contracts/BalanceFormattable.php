<?php

namespace FinancePack\Contracts;

interface BalanceFormattable
{
    public static function fromArray(array $data): static;
}
