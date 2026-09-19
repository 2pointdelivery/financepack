<?php

namespace FinancePack\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $companyId = $this->getCurrentCompanyId();

        if ($companyId) {
            $builder->where('company_id', $companyId);
        }
    }

    protected function getCurrentCompanyId(): ?int
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'currentCompany')) {
            return $user->currentCompany?->id;
        }

        return $user?->current_company_id;
    }

    public function extend(Builder $builder): void
    {
        $builder->macro('withoutCompanyScope', function (Builder $builder) {
            return $builder->withoutGlobalScope(CompanyScope::class);
        });
    }
}
