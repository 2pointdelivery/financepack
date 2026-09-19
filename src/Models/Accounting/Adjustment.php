<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\AdjustmentCategory;
use FinancePack\Enums\AdjustmentType;
use FinancePack\Enums\AdjustmentComputation;
use FinancePack\Traits\CompanyOwned;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Adjustment extends Model
{
    use CompanyOwned;

    protected $table = 'adjustments';

    protected $fillable = [
        'company_id',
        'account_id',
        'name',
        'category',
        'type',
        'computation',
        'rate',
        'recoverable',
    ];

    protected $casts = [
        'category' => AdjustmentCategory::class,
        'type' => AdjustmentType::class,
        'computation' => AdjustmentComputation::class,
        'rate' => 'decimal:2',
        'recoverable' => 'boolean',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function adjustmentables(): MorphToMany
    {
        return $this->morphToMany(
            static::class,
            'adjustmentable',
            'adjustmentables',
            'adjustment_id',
            'adjustmentable_id'
        )->withPivot('rate', 'amount', 'tax_rate');
    }
}
