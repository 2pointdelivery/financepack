<?php

namespace FinancePack\Concerns;

use FinancePack\Scopes\CompanyScope;

trait CompanyOwned
{
    public static function bootCompanyOwned(): void
    {
        static::addGlobalScope(new CompanyScope);
    }

    public static function bootUsingCompanyOwned(): void
    {
        static::creating(function ($model) {
            if (is_null($model->company_id)) {
                $model->company_id = $this->getCurrentCompanyId();
            }
        });
    }

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        $companyModel = config('financepack.company_model', 'App\\Models\\Company');

        return $this->belongsTo($companyModel);
    }

    protected function getCurrentCompanyId(): ?int
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'currentCompany')) {
            return $user->currentCompany?->id;
        }

        return $user?->current_company_id;
    }
}
