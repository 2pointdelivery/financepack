<?php

namespace FinancePack\Models\Common;

use FinancePack\Enums\Accounting\OfferingType;
use FinancePack\Models\Accounting\Account;
use FinancePack\Models\Company;
use FinancePack\Concerns\CompanyOwned;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Offering extends Model
{
    use CompanyOwned;

    protected $table = 'offerings';

    protected $fillable = [
        'company_id',
        'name',
        'description',
        'unit_price',
        'income_account_id',
        'type',
        'archived',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'archived' => 'boolean',
        'type' => OfferingType::class,
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function incomeAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'income_account_id');
    }

    public function lineItems(): MorphMany
    {
        return $this->morphMany(\FinancePack\Models\Accounting\DocumentLineItem::class, 'offering');
    }
}
