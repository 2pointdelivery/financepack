<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\TransactionType;
use FinancePack\Enums\Accounting\PaymentMethod;
use FinancePack\Models\Contact;
use FinancePack\Concerns\Blamable;
use FinancePack\Concerns\CompanyOwned;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Transaction extends Model
{
    use Blamable, CompanyOwned, HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'company_id',
        'account_id',
        'bank_account_id',
        'plaid_transaction_id',
        'contact_id',
        'type',
        'payment_channel',
        'payment_method',
        'is_payment',
        'description',
        'notes',
        'reference',
        'amount',
        'pending',
        'reviewed',
        'approved_override',
        'posted_at',
        'meta',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'payment_method' => PaymentMethod::class,
        'is_payment' => 'boolean',
        'amount' => 'integer',
        'pending' => 'boolean',
        'reviewed' => 'boolean',
        'approved_override' => 'boolean',
        'posted_at' => 'date',
        'meta' => 'array',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'transaction_id');
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function payeeable(): MorphTo
    {
        return $this->morphTo();
    }
}
