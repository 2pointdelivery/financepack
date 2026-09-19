<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Collections\Accounting\JournalEntryCollection;
use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Concerns\Blamable;
use FinancePack\Concerns\CompanyOwned;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

class JournalEntry extends Model
{
    use Blamable, CompanyOwned;

    protected $table = 'journal_entries';

    protected $fillable = [
        'company_id',
        'transaction_id',
        'account_id',
        'type',
        'amount',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'type' => JournalEntryType::class,
        'amount' => 'integer',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function newCollection(array $models = []): Collection
    {
        return new JournalEntryCollection($models);
    }
}
