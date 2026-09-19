<?php

namespace FinancePack\Models\Setting;

use FinancePack\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Currency extends Model
{
    protected $table = 'currencies';

    protected $fillable = [
        'company_id',
        'code',
        'name',
        'symbol',
        'precision',
        'thousand_separator',
        'decimal_mark',
        'exchange_rate',
        'is_default',
    ];

    protected $casts = [
        'exchange_rate' => 'decimal:8',
        'is_default' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
