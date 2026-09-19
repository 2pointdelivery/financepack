<?php

namespace FinancePack\Models\Setting;

use FinancePack\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDefault extends Model
{
    protected $table = 'company_defaults';

    protected $fillable = [
        'company_id',
        'currency_id',
        'fiscal_year_start_month',
        'fiscal_year_start_day',
    ];

    protected $casts = [
        'fiscal_year_start_month' => 'integer',
        'fiscal_year_start_day' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
