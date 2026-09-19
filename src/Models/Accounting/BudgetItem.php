<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Concerns\CompanyOwned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class BudgetItem extends Model
{
    use CompanyOwned, HasFactory;

    protected $table = 'budget_items';

    protected $fillable = [
        'budget_id',
        'account_id',
        'name',
        'amount',
        'spent',
        'notes',
        'category',
        'account_type',
        'growth_rate',
        'is_recurring',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'integer',
        'spent' => 'integer',
        'category' => AccountCategory::class,
        'account_type' => AccountType::class,
        'growth_rate' => 'decimal:2',
        'is_recurring' => 'boolean',
    ];

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class, 'budget_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(BudgetAllocation::class, 'budget_item_id');
    }

    public function scopeByCategory(Builder $query, AccountCategory $category): Builder
    {
        return $query->where('category', $category);
    }
}
