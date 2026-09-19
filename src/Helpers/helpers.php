<?php

use FinancePack\Services\CompanySettingsService;
use FinancePack\Utilities\Currency\CurrencyConverter;
use Illuminate\Support\Carbon;

if (! function_exists('financepack_money')) {
    function financepack_money(float $amount, ?string $currency = null): string
    {
        return CurrencyConverter::formatToMoney($amount, $currency);
    }
}

if (! function_exists('company_today')) {
    function company_today(): Carbon
    {
        return today(CompanySettingsService::getDefaultTimezone());
    }
}

if (! function_exists('company_now')) {
    function company_now(): Carbon
    {
        return now(CompanySettingsService::getDefaultTimezone());
    }
}

if (! function_exists('is_demo_environment')) {
    function is_demo_environment(): bool
    {
        return app()->environment('demo');
    }
}

if (! function_exists('format_cents_to_money')) {
    function format_cents_to_money(int $amount, ?string $currency = null, bool $withCode = false): string
    {
        return CurrencyConverter::formatCentsToMoney($amount, $currency, $withCode);
    }
}

if (! function_exists('convert_to_cents')) {
    function convert_to_cents(string|float $amount, ?string $currency = null): int
    {
        return CurrencyConverter::convertToCents($amount, $currency);
    }
}

if (! function_exists('money_mask')) {
    function money_mask(?string $currency = null): string
    {
        $precision = currency($currency)->getPrecision();
        $decimalMark = currency($currency)->getDecimalMark();
        $thousandsSeparator = currency($currency)->getThousandsSeparator();

        return "\$money(\$input, '" . $decimalMark . "', '" . $thousandsSeparator . "', " . $precision . ');';
    }
}
