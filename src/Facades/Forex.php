<?php

namespace FinancePack\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isEnabled()
 * @method static ?array getSupportedCurrencies()
 * @method static ?array getExchangeRates(string $baseCurrency, array $targetCurrencies)
 * @method static ?float getCachedExchangeRate(string $baseCurrency, string $targetCurrency)
 * @method static ?array updateCurrencyRatesCache(string $baseCurrency)
 *
 * @see \FinancePack\Services\CurrencyService
 */
class Forex extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'financepack.forex';
    }
}
