<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\OutstandingItemType;
use FinancePack\Enums\Accounting\ItemSeverity;
use FinancePack\Concerns\CompanyOwned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ClosureItem extends Model
{
    use CompanyOwned, HasFactory;

    protected $table = 'closure_items';

    protected $fillable = [
        'closure_id',
        'company_id',
        'item_type',
        'itemable_type',
        'itemable_id',
        'label',
        'deep_link',
        'amount',
        'currency_code',
        'severity',
        'resolved',
        'resolved_at',
        'resolved_by',
        'notes',
    ];

    protected $casts = [
        'item_type' => OutstandingItemType::class,
        'severity' => ItemSeverity::class,
        'amount' => 'integer',
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function closure(): BelongsTo
    {
        return $this->belongsTo(FinancialClosure::class, 'closure_id');
    }

    public function itemable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getDeepLinkUrl(): string
    {
        $prefix = config('financepack.closure.deep_link_prefix', '');

        return $prefix . $this->deep_link;
    }
}
