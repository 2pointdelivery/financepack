<?php

declare(strict_types=1);

namespace FinancePack\Models\Banking;

use FinancePack\Concerns\CompanyOwned;
use FinancePack\Models\Accounting\Account;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class BankAccount extends Model
{
    use CompanyOwned;

    protected $table = 'bank_accounts';

    protected $fillable = [
        'company_id',
        'account_id',
        'name',
        'currency_code',
        'account_number',
        'routing_number',
        'bank_name',
        'institution_id',
        'plaid_access_token',
        'plaid_item_id',
        'balance',
        'last_synced_at',
    ];

    protected $casts = [
        'balance' => 'integer',
        'last_synced_at' => 'datetime',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function connectedBankAccount(): HasOne
    {
        return $this->hasOne(ConnectedBankAccount::class, 'bank_account_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(\FinancePack\Models\Accounting\Transaction::class, 'bank_account_id');
    }

    protected function balanceFormatted(): Attribute
    {
        return Attribute::get(fn () => format_cents_to_money($this->balance, $this->currency_code));
    }
}
