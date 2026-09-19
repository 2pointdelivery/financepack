<?php

namespace FinancePack\Collections\Accounting;

use Illuminate\Database\Eloquent\Collection;

class DocumentCollection extends Collection
{
    public function sumMoneyInDefaultCurrency(string $column): int
    {
        return $this->reduce(function (int $carry, $document) use ($column) {
            $amount = (int) $document->{$column};

            if ($document->currency_code && $document->currency_code !== $document->company->currency_code) {
                $rate = $document->currency->exchange_rate ?? 1;
                $amount = (int) round($amount * $rate);
            }

            return $carry + $amount;
        }, 0);
    }
}
