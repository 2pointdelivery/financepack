<?php

namespace FinancePack\Models\Setting;

use FinancePack\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentDefault extends Model
{
    protected $table = 'document_defaults';

    protected $fillable = [
        'company_id',
        'type',
        'number_prefix',
        'next_number',
        'payment_terms_days',
    ];

    protected $casts = [
        'next_number' => 'integer',
        'payment_terms_days' => 'integer',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public static function invoice(): ?self
    {
        return static::where('type', 'invoice')->first();
    }

    public static function bill(): ?self
    {
        return static::where('type', 'bill')->first();
    }
}
