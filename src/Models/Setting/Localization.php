<?php

namespace FinancePack\Models\Setting;

use FinancePack\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Localization extends Model
{
    protected $table = 'localizations';

    protected $fillable = [
        'company_id',
        'locale',
        'date_format',
        'number_format',
        'currency_format',
        'percent_first',
    ];

    protected $casts = [
        'percent_first' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
