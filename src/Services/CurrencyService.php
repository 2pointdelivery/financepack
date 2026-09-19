<?php

declare(strict_types=1);

namespace FinancePack\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class CurrencyService implements \FinancePack\Contracts\CurrencyHandler
{
    protected Client $client;
    protected ?string $apiKey;
    protected ?string $baseUrl;

    public function __construct(
        ?string $apiKey = null,
        ?string $baseUrl = null,
        ?Client $client = null,
    ) {
        $this->apiKey = $apiKey ?? config('financepack.currency.api_key');
        $this->baseUrl = rtrim($baseUrl ?? config('financepack.currency.base_url', 'https://v6.exchangerate-api.com/v6'), '/');
        $this->client = $client ?? new Client();
    }

    public function isEnabled(): bool
    {
        return ! empty($this->apiKey);
    }

    public function getSupportedCurrencies(): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $cacheKey = 'financepack.supported_currencies';

        return Cache::remember($cacheKey, now()->addDay(), function (): ?array {
            try {
                $response = $this->client->get("{$this->baseUrl}/{$this->apiKey}/codes");

                $data = json_decode($response->getBody()->getContents(), true);

                if (isset($data['supported_codes'])) {
                    return collect($data['supported_codes'])->mapWithKeys(fn (array $pair) => [$pair[0] => $pair[1]])->toArray();
                }

                return null;
            } catch (\Exception $e) {
                return null;
            }
        });
    }

    public function getExchangeRates(string $baseCurrency, array $targetCurrencies): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        $cacheKey = "financepack.exchange_rates.{$baseCurrency}";

        $rates = Cache::get($cacheKey);

        if (is_null($rates)) {
            $rates = $this->updateCurrencyRatesCache($baseCurrency);
        }

        if (is_null($rates)) {
            return null;
        }

        return collect($rates)
            ->filter(fn ($rate, $code) => in_array($code, $targetCurrencies, true))
            ->toArray();
    }

    public function getCachedExchangeRates(string $baseCurrency, array $targetCurrencies): ?array
    {
        $cacheKey = "financepack.exchange_rates.{$baseCurrency}";

        $rates = Cache::get($cacheKey);

        if (is_null($rates)) {
            return null;
        }

        return collect($rates)
            ->filter(fn ($rate, $code) => in_array($code, $targetCurrencies, true))
            ->toArray();
    }

    public function getCachedExchangeRate(string $baseCurrency, string $targetCurrency): ?float
    {
        $cacheKey = "financepack.exchange_rates.{$baseCurrency}";

        $rates = Cache::get($cacheKey);

        if (is_null($rates) || ! isset($rates[$targetCurrency])) {
            return null;
        }

        return (float) $rates[$targetCurrency];
    }

    public function updateCurrencyRatesCache(string $baseCurrency): ?array
    {
        if (! $this->isEnabled()) {
            return null;
        }

        try {
            $response = $this->client->get("{$this->baseUrl}/{$this->apiKey}/latest/{$baseCurrency}");

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['conversion_rates'])) {
                $rates = $data['conversion_rates'];
                $cacheKey = "financepack.exchange_rates.{$baseCurrency}";
                Cache::put($cacheKey, $rates, now()->addHours(12));

                return $rates;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
