<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\BudgetStatus;
use FinancePack\Enums\Accounting\BudgetType;
use FinancePack\Concerns\CompanyOwned;
use FinancePack\Concerns\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Budget extends Model
{
    use CompanyOwned, Blamable, HasFactory;

    protected $table = 'budgets';

    protected $fillable = [
        'company_id',
        'name',
        'start_date',
        'end_date',
        'amount',
        'spent',
        'status',
        'notes',
        'growth_rate',
        'currency_code',
        'type',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => BudgetStatus::class,
        'type' => BudgetType::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'integer',
        'spent' => 'integer',
        'growth_rate' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class, 'budget_id');
    }

    public function getMonthsCount(): int
    {
        return $this->start_date->diffInMonths($this->end_date) + 1;
    }

    public function getCategoryGrowthRates(): array
    {
        $rates = [];

        foreach ($this->items as $item) {
            $category = $item->category;
            if ($category && ! isset($rates[$category->value])) {
                $rates[$category->value] = (float) ($item->growth_rate ?? $this->growth_rate);
            }
        }

        return $rates;
    }
}
