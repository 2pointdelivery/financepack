<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\BillStatus;
use FinancePack\Enums\Accounting\DocumentType;
use FinancePack\Enums\Accounting\JournalEntryType;
use FinancePack\Enums\Accounting\TransactionType;
use FinancePack\Models\Common\Vendor;
use FinancePack\Models\Setting\DocumentDefault;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;

class Bill extends Document
{
    protected $table = 'bills';

    protected $fillable = [
        'company_id',
        'currency_code',
        'vendor_id',
        'account_id',
        'bill_number',
        'date',
        'due_date',
        'discount_method',
        'discount_computation',
        'discount_rate',
        'subtotal',
        'tax_total',
        'discount_total',
        'total',
        'amount_paid',
        'terms',
        'footer',
        'status',
        'approved_at',
        'paid_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => BillStatus::class,
        'date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function transactions(): MorphMany
    {
        return $this->morphMany(Transaction::class, 'transactionable');
    }

    public function payments(): MorphMany
    {
        return $this->transactions()->where('is_payment', true);
    }

    public function approvalTransaction(): MorphOne
    {
        return $this->transactions()->where('type', TransactionType::Journal);
    }

    public function documentType(): DocumentType
    {
        return DocumentType::Bill;
    }

    public function documentNumber(): ?string
    {
        return $this->bill_number;
    }

    public function documentDate(): ?string
    {
        return $this->date?->toDateString();
    }

    public function dueDate(): ?string
    {
        return $this->due_date?->toDateString();
    }

    public function amountDue(): ?string
    {
        return (string) ($this->total - $this->amount_paid);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => $this->status->getLabel());
    }

    public function canBeApproved(): bool
    {
        return $this->status->isDraft() && is_null($this->approved_at);
    }

    public function canRecordPayment(): bool
    {
        return ! $this->status->isDraft()
            && ! $this->status->isPaid()
            && ! $this->status->isVoid();
    }

    public function canBulkRecordPayment(): bool
    {
        $company = $this->company;
        $companyCurrency = $company->currency_code;

        return $this->currency_code === $companyCurrency;
    }

    public function shouldBeOverdue(): bool
    {
        return $this->due_date !== null
            && $this->due_date->isBefore(company_today())
            && in_array($this->status, BillStatus::canBeOverdue(), true);
    }

    public static function getNextDocumentNumber(?Company $company = null): string
    {
        $company = $company ?? auth()->user()?->currentCompany;

        $default = DocumentDefault::where('company_id', $company?->id)
            ->where('type', 'bill')
            ->first();

        $prefix = $default?->number_prefix ?? 'BILL-';
        $nextNumber = ($default?->next_number ?? 1);

        $number = $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);

        if ($default) {
            $default->increment('next_number');
        }

        return $number;
    }

    public function recordPayment(array $data): void
    {
        if (! $this->canRecordPayment()) {
            return;
        }

        $amount = $data['amount'] ?? 0;
        $paymentMethod = $data['payment_method'] ?? null;
        $reference = $data['reference'] ?? null;
        $description = $data['description'] ?? null;
        $postedAt = $data['posted_at'] ?? now();

        $this->transactions()->create([
            'company_id' => $this->company_id,
            'type' => TransactionType::Withdrawal,
            'is_payment' => true,
            'payment_method' => $paymentMethod,
            'reference' => $reference,
            'description' => $description ?? "Payment for Bill {$this->bill_number}",
            'amount' => $amount,
            'posted_at' => $postedAt,
        ]);

        $this->increment('amount_paid', $amount);

        $this->updateStatusAfterPayment();
    }

    public function approveDraft(?Carbon $approvedAt = null): void
    {
        if (! $this->canBeApproved()) {
            return;
        }

        $approvedAt = $approvedAt ?? now();

        $this->createApprovalTransaction();

        $this->update([
            'status' => BillStatus::Open,
            'approved_at' => $approvedAt,
        ]);
    }

    public function createApprovalTransaction(): void
    {
        $transaction = $this->transactions()->create([
            'company_id' => $this->company_id,
            'type' => TransactionType::Journal,
            'is_payment' => false,
            'description' => "Approval of Bill {$this->bill_number}",
            'amount' => $this->total,
            'posted_at' => now(),
        ]);

        $apAccount = Account::getAccountsPayableAccount($this->company_id);

        if ($apAccount) {
            $transaction->journalEntries()->create([
                'company_id' => $this->company_id,
                'account_id' => $apAccount->id,
                'type' => JournalEntryType::Credit,
                'amount' => $this->total,
                'description' => "Bill {$this->bill_number} - Accounts Payable",
            ]);
        }

        $expenseAccounts = $this->getExpenseAccountsForApproval();

        foreach ($expenseAccounts as [$account, $amount]) {
            $transaction->journalEntries()->create([
                'company_id' => $this->company_id,
                'account_id' => $account->id,
                'type' => JournalEntryType::Debit,
                'amount' => $amount,
                'description' => "Bill {$this->bill_number} - Expense",
            ]);
        }

        if ($this->tax_total > 0) {
            $taxAdjustments = $this->getTaxAdjustments();
            foreach ($taxAdjustments as $adjustment) {
                if ($adjustment->account) {
                    $taxAmount = $this->calculateTaxForAdjustment($adjustment);
                    $transaction->journalEntries()->create([
                        'company_id' => $this->company_id,
                        'account_id' => $adjustment->account->id,
                        'type' => JournalEntryType::Debit,
                        'amount' => $taxAmount,
                        'description' => "Bill {$this->bill_number} - {$adjustment->name}",
                    ]);
                }
            }
        }

        $discountAccount = Account::getPurchaseDiscountAccount($this->company_id);
        if ($discountAccount && $this->discount_total > 0) {
            $transaction->journalEntries()->create([
                'company_id' => $this->company_id,
                'account_id' => $discountAccount->id,
                'type' => JournalEntryType::Credit,
                'amount' => $this->discount_total,
                'description' => "Bill {$this->bill_number} - Purchase Discount",
            ]);
        }
    }

    public function markAsSent(?Carbon $sentAt = null): void
    {
        $sentAt = $sentAt ?? now();

        $this->update([
            'status' => BillStatus::Open,
        ]);
    }

    public function replicateLineItems(Model $target): void
    {
        foreach ($this->lineItems as $lineItem) {
            $newLineItem = $target->lineItems()->create([
                'offering_id' => $lineItem->offering_id,
                'name' => $lineItem->name,
                'description' => $lineItem->description,
                'quantity' => $lineItem->quantity,
                'unit_price' => $lineItem->unit_price,
                'subtotal' => $lineItem->subtotal,
                'total' => $lineItem->total,
                'discount_rate' => $lineItem->discount_rate,
                'tax_rate' => $lineItem->tax_rate,
            ]);

            foreach ($lineItem->adjustments as $adjustment) {
                $newLineItem->adjustments()->attach($adjustment->id, [
                    'rate' => $adjustment->pivot->rate,
                    'amount' => $adjustment->pivot->amount,
                    'tax_rate' => $adjustment->pivot->tax_rate,
                ]);
            }
        }
    }

    protected function updateStatusAfterPayment(): void
    {
        if ($this->amount_paid >= $this->total) {
            $this->update([
                'status' => BillStatus::Paid,
                'paid_at' => now(),
            ]);
        } elseif ($this->amount_paid > 0) {
            $this->update(['status' => BillStatus::Partial]);
        }
    }

    protected function getExpenseAccountsForApproval(): array
    {
        $accounts = [];

        foreach ($this->lineItems as $lineItem) {
            if ($lineItem->offering && $lineItem->offering->account) {
                $accountId = $lineItem->offering->account->id;
                $accounts[$accountId] = $accounts[$accountId] ?? [$lineItem->offering->account, 0];
                $accounts[$accountId][1] += $lineItem->subtotal - $lineItem->discount_rate;
            }
        }

        if (empty($accounts)) {
            $defaultExpense = Account::where('company_id', $this->company_id)
                ->where('category', \FinancePack\Enums\Accounting\AccountCategory::Expense)
                ->where('default', true)
                ->first();

            if ($defaultExpense) {
                $accounts[$defaultExpense->id] = [
                    $defaultExpense,
                    $this->subtotal - $this->discount_total,
                ];
            }
        }

        return array_values($accounts);
    }

    protected function getTaxAdjustments(): \Illuminate\Database\Eloquent\Collection
    {
        $adjustmentIds = $this->lineItems
            ->flatMap(fn (DocumentLineItem $lineItem) => $lineItem->adjustments->pluck('id'))
            ->unique();

        return Adjustment::whereIn('id', $adjustmentIds)
            ->where('category', \FinancePack\Enums\Accounting\AdjustmentCategory::Tax)
            ->get();
    }

    protected function calculateTaxForAdjustment(Adjustment $adjustment): int
    {
        $total = 0;

        foreach ($this->lineItems as $lineItem) {
            $pivot = $lineItem->adjustments()
                ->where('adjustments.id', $adjustment->id)
                ->first()?->pivot;

            if ($pivot) {
                $total += (int) $pivot->amount;
            }
        }

        return $total;
    }
}
