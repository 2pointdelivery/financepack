<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetAllocation extends Model
{
    use HasFactory;

    protected $table = 'budget_allocations';

    protected $fillable = [
        'budget_id',
        'budget_item_id',
        'period',
        'amount',
        'spent',
        'growth_rate_applied',
        'is_projected',
        'actual_amount',
        'variance',
        'variance_percent',
    ];

    protected $casts = [
        'amount' => 'integer',
        'spent' => 'integer',
        'growth_rate_applied' => 'decimal:2',
        'is_projected' => 'boolean',
        'actual_amount' => 'integer',
        'variance' => 'integer',
        'variance_percent' => 'decimal:2',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function budgetItem(): BelongsTo
    {
        return $this->belongsTo(BudgetItem::class, 'budget_item_id');
    }
}
