<?php

namespace FinancePack\Utilities\Currency;

use FinancePack\Models\Company;

class CurrencyAccessor
{
    public static function getDefaultCurrency(): string
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'currentCompany')) {
            $company = $user->currentCompany;

            if ($company && $company->defaultCurrency) {
                return $company->defaultCurrency->code;
            }
        }

        return config('financepack.default_currency', 'USD');
    }

    public static function setDefaultCurrency(string $currencyCode): void
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'currentCompany')) {
            $company = $user->currentCompany;

            if ($company) {
                $company->defaultCurrency()->associate($currencyCode);
                $company->save();
            }
        }
    }
}
