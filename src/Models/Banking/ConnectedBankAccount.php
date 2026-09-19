<?php

declare(strict_types=1);

namespace FinancePack\Models\Banking;

use FinancePack\Concerns\CompanyOwned;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectedBankAccount extends Model
{
    use CompanyOwned;

    protected $table = 'connected_bank_accounts';

    protected $fillable = [
        'company_id',
        'bank_account_id',
        'institution_id',
        'plaid_access_token',
        'plaid_item_id',
        'plaid_webhook_code',
        'status',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class, 'institution_id');
    }
}
