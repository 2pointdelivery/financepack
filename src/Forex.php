<?php

declare(strict_types=1);

namespace FinancePack;

use FinancePack\Contracts\Services\CurrencyServiceInterface;

class Forex
{
    public function __construct(
        protected CurrencyServiceInterface $currencyService,
    ) {}

    public function isEnabled(): bool
    {
        return $this->currencyService->isEnabled();
    }

    public function getSupportedCurrencies(): ?array
    {
        return $this->currencyService->getSupportedCurrencies();
    }

    public function getExchangeRates(string $baseCurrency, array $targetCurrencies): ?array
    {
        return $this->currencyService->getExchangeRates($baseCurrency, $targetCurrencies);
    }

    public function getCachedExchangeRate(string $baseCurrency, string $targetCurrency): ?float
    {
        return $this->currencyService->getCachedExchangeRate($baseCurrency, $targetCurrency);
    }

    public function updateCurrencyRatesCache(string $baseCurrency): ?array
    {
        return $this->currencyService->updateCurrencyRatesCache($baseCurrency);
    }
}
