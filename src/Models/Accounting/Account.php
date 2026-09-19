<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Models\Currency;
use FinancePack\Concerns\Blamable;
use FinancePack\Concerns\CompanyOwned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Account extends Model
{
    use Blamable, CompanyOwned, HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'company_id',
        'subtype_id',
        'parent_id',
        'category',
        'type',
        'code',
        'name',
        'currency_code',
        'description',
        'archived',
        'default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'category' => AccountCategory::class,
        'type' => AccountType::class,
        'archived' => 'boolean',
        'default' => 'boolean',
    ];

    public function subtype(): BelongsTo
    {
        return $this->belongsTo(AccountSubtype::class, 'subtype_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function bankAccount(): HasOne
    {
        return $this->hasOne(BankAccount::class, 'account_id');
    }

    public function adjustment(): HasOne
    {
        return $this->hasOne(Adjustment::class, 'account_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'account_id');
    }

    public static function getAccountsReceivableAccount(?string $companyId): ?self
    {
        return static::where('company_id', $companyId)
            ->where('category', AccountCategory::Asset)
            ->where('name', 'Accounts Receivable')
            ->first();
    }

    public static function getAccountsPayableAccount(?string $companyId): ?self
    {
        return static::where('company_id', $companyId)
            ->where('category', AccountCategory::Liability)
            ->where('name', 'Accounts Payable')
            ->first();
    }

    public static function getSalesDiscountAccount(?string $companyId): ?self
    {
        return static::where('company_id', $companyId)
            ->where('category', AccountCategory::Revenue)
            ->where('name', 'Sales Discount')
            ->first();
    }

    public static function getPurchaseDiscountAccount(?string $companyId): ?self
    {
        return static::where('company_id', $companyId)
            ->where('category', AccountCategory::Expense)
            ->where('name', 'Purchase Discount')
            ->first();
    }

    public function scopeBudgetable(Builder $query): Builder
    {
        return $query->whereIn('category', [AccountCategory::Revenue, AccountCategory::Expense])
            ->where('category', '!=', AccountCategory::Uncategorized);
    }
}
