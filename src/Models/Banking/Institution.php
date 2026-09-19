<?php

declare(strict_types=1);

namespace FinancePack\Models\Banking;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institution extends Model
{
    protected $table = 'institutions';

    protected $fillable = [
        'plaid_institution_id',
        'name',
        'logo',
        'url',
        'primary_color',
        'country_codes',
        'products',
    ];

    protected $casts = [
        'country_codes' => 'array',
        'products' => 'array',
    ];

    public function connectedBankAccounts(): HasMany
    {
        return $this->hasMany(ConnectedBankAccount::class, 'institution_id');
    }
}
