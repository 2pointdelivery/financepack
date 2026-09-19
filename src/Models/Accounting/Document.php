<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Concerns\Blamable;
use FinancePack\Concerns\CompanyOwned;
use FinancePack\Enums\Accounting\AdjustmentComputation;
use FinancePack\Enums\Accounting\DocumentDiscountMethod;
use FinancePack\Enums\Accounting\DocumentType;
use FinancePack\Models\Setting\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

abstract class Document extends Model
{
    use Blamable, CompanyOwned;

    protected $table = 'documents';

    protected $fillable = [
        'company_id',
        'currency_code',
        'discount_method',
        'discount_computation',
        'discount_rate',
        'subtotal',
        'tax_total',
        'discount_total',
        'total',
        'amount_paid',
        'terms',
        'footer',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'discount_method' => DocumentDiscountMethod::class,
        'discount_computation' => AdjustmentComputation::class,
        'discount_rate' => 'decimal:2',
        'subtotal' => 'integer',
        'tax_total' => 'integer',
        'discount_total' => 'integer',
        'total' => 'integer',
        'amount_paid' => 'integer',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function lineItems(): MorphMany
    {
        return $this->morphMany(DocumentLineItem::class, 'documentable');
    }

    abstract public function documentType(): DocumentType;

    abstract public function documentNumber(): ?string;

    abstract public function documentDate(): ?string;

    abstract public function dueDate(): ?string;

    abstract public function amountDue(): ?string;

    public function getAmountDueAttribute(): int
    {
        return $this->total - $this->amount_paid;
    }

    public function hasInactiveAdjustments(): bool
    {
        foreach ($this->lineItems as $lineItem) {
            $adjustmentIds = $lineItem->adjustments->pluck('adjustment_id');
            if ($adjustmentIds->isNotEmpty()) {
                $activeIds = Adjustment::whereIn('id', $adjustmentIds)
                    ->pluck('id');
                if ($adjustmentIds->diff($activeIds)->isNotEmpty()) {
                    return true;
                }
            }
        }

        return false;
    }
}
