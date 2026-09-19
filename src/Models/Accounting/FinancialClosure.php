<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\ClosureStatus;
use FinancePack\Concerns\CompanyOwned;
use FinancePack\Concerns\Blamable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class FinancialClosure extends Model
{
    use CompanyOwned, Blamable, HasFactory;

    protected $table = 'financial_closures';

    protected $fillable = [
        'company_id',
        'period_start',
        'period_end',
        'status',
        'closed_at',
        'reopened_at',
        'closed_by',
        'reopened_by',
        'notes',
        'summary',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => ClosureStatus::class,
        'period_start' => 'date',
        'period_end' => 'date',
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
        'summary' => 'array',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ClosureItem::class, 'closure_id');
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(config('financepack.user_model', 'App\\Models\\User'), 'closed_by');
    }

    public function reopenedBy(): BelongsTo
    {
        return $this->belongsTo(config('financepack.user_model', 'App\\Models\\User'), 'reopened_by');
    }

    public function isLocked(): bool
    {
        return $this->status === ClosureStatus::Closed;
    }

    public function scopeForPeriod(Builder $query, string $start, string $end): Builder
    {
        return $query->where('period_start', '<=', $end)
            ->where('period_end', '>=', $start);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', ClosureStatus::Closed);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', ClosureStatus::Draft);
    }
}
