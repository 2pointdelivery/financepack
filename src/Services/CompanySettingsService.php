<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Models\Company;

class CompanySettingsService
{
    public static function getDefaultTimezone(): string
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'currentCompany')) {
            $company = $user->currentCompany;
            if ($company && $company->default_timezone) {
                return $company->default_timezone;
            }
        }

        if ($user && isset($user->current_company_id)) {
            $company = Company::find($user->current_company_id);
            if ($company && $company->default_timezone) {
                return $company->default_timezone;
            }
        }

        return config('app.timezone', 'UTC');
    }
}
