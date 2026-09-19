<?php

namespace FinancePack\Contracts;

interface CurrencyHandler
{
    public function isEnabled(): bool;

    public function getSupportedCurrencies(): ?array;

    public function getExchangeRates(string $baseCurrency, array $targetCurrencies): ?array;

    public function getCachedExchangeRates(string $baseCurrency, array $targetCurrencies): ?array;

    public function getCachedExchangeRate(string $baseCurrency, string $targetCurrency): ?float;
}
