<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\AccountCategory;
use FinancePack\Enums\AccountType;
use FinancePack\Models\Company;
use FinancePack\Traits\CompanyOwned;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountSubtype extends Model
{
    use CompanyOwned;

    protected $table = 'account_subtypes';

    protected $fillable = [
        'company_id',
        'name',
        'multi_currency',
        'inverse_cash_flow',
        'category',
        'type',
    ];

    protected $casts = [
        'multi_currency' => 'boolean',
        'inverse_cash_flow' => 'boolean',
        'category' => AccountCategory::class,
        'type' => AccountType::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'subtype_id');
    }
}
