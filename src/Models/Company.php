<?php

namespace FinancePack\Models;

use FinancePack\Models\Common\Client;
use FinancePack\Models\Common\Vendor;
use FinancePack\Models\Setting\Currency;
use FinancePack\Models\Setting\Localization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Company extends Model
{
    protected $table = 'companies';

    protected $fillable = [
        'name',
        'owner_id',
        'currency_code',
        'default_timezone',
        'logo',
        'phone_number',
        'address',
    ];

    protected $appends = [
        'default_currency',
        'default_timezone',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Authenticatable::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(Authenticatable::class, 'company_user');
    }

    public function defaultCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(\FinancePack\Models\Accounting\Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(\FinancePack\Models\Accounting\Transaction::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(\FinancePack\Models\Accounting\Invoice::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(\FinancePack\Models\Accounting\Bill::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function vendors(): HasMany
    {
        return $this->hasMany(Vendor::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(\FinancePack\Models\Accounting\BankAccount::class);
    }

    public function locale(): HasOne
    {
        return $this->hasOne(Localization::class);
    }

    public function getDefaultCurrencyAttribute(): ?Currency
    {
        return $this->defaultCurrency;
    }

    public function getDefaultTimezoneAttribute(): string
    {
        return $this->default_timezone ?? 'UTC';
    }
}
