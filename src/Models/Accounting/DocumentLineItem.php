<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Models\Offering;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class DocumentLineItem extends Model
{
    protected $table = 'document_line_items';

    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'offering_id',
        'name',
        'description',
        'quantity',
        'unit_price',
        'subtotal',
        'total',
        'discount_rate',
        'tax_rate',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'subtotal' => 'integer',
        'total' => 'integer',
        'discount_rate' => 'decimal:2',
        'tax_rate' => 'decimal:2',
    ];

    public function documentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class, 'offering_id');
    }

    public function adjustments(): MorphToMany
    {
        return $this->morphToMany(
            Adjustment::class,
            'adjustmentable',
            'adjustmentables',
            'adjustmentable_id',
            'adjustment_id'
        )->withPivot('rate', 'amount', 'tax_rate');
    }
}
