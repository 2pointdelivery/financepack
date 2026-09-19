<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\DocumentType;
use FinancePack\Enums\Accounting\InvoiceStatus;
use FinancePack\Models\Common\Client;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class RecurringInvoice extends Document
{
    protected $table = 'recurring_invoices';

    protected $fillable = [
        'company_id',
        'currency_code',
        'client_id',
        'frequency',
        'start_date',
        'end_date',
        'next_invoice_date',
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
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_invoice_date' => 'date',
        'status' => 'string',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function documentType(): DocumentType
    {
        return DocumentType::RecurringInvoice;
    }

    public function documentNumber(): ?string
    {
        return null;
    }

    public function documentDate(): ?string
    {
        return $this->start_date?->toDateString();
    }

    public function dueDate(): ?string
    {
        return null;
    }

    public function amountDue(): ?string
    {
        return (string) $this->total;
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->status));
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->start_date->isPast()
            && ($this->end_date === null || $this->end_date->isFuture());
    }

    public function shouldGenerateInvoice(): bool
    {
        return $this->isActive()
            && $this->next_invoice_date !== null
            && $this->next_invoice_date->isPastOrToday();
    }

    public function generateInvoice(): ?Invoice
    {
        if (! $this->shouldGenerateInvoice()) {
            return null;
        }

        $invoice = Invoice::create([
            'company_id' => $this->company_id,
            'currency_code' => $this->currency_code,
            'client_id' => $this->client_id,
            'recurring_invoice_id' => $this->id,
            'account_id' => null,
            'invoice_number' => Invoice::getNextDocumentNumber($this->company),
            'order_number' => null,
            'date' => company_today()->toDateString(),
            'due_date' => $this->calculateNextDueDate()->toDateString(),
            'discount_method' => $this->discount_method,
            'discount_computation' => $this->discount_computation,
            'discount_rate' => $this->discount_rate,
            'subtotal' => $this->subtotal,
            'tax_total' => $this->tax_total,
            'discount_total' => $this->discount_total,
            'total' => $this->total,
            'amount_paid' => 0,
            'terms' => $this->terms,
            'footer' => $this->footer,
            'status' => InvoiceStatus::Draft,
        ]);

        $this->replicateLineItems($invoice);

        $this->update([
            'next_invoice_date' => $this->calculateNextInvoiceDate()->toDateString(),
        ]);

        return $invoice;
    }

    public function pause(): void
    {
        $this->update(['status' => 'paused']);
    }

    public function resume(): void
    {
        $this->update([
            'status' => 'active',
            'next_invoice_date' => $this->calculateNextInvoiceDate()->toDateString(),
        ]);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    protected function calculateNextInvoiceDate(): Carbon
    {
        $current = $this->next_invoice_date ?? company_today();

        return match ($this->frequency) {
            'weekly' => $current->addWeek(),
            'monthly' => $this->frequency === 'monthly'
                ? $current->addMonthNoOverflow()
                : $current->addYearNoOverflow(),
            'yearly' => $current->addYearNoOverflow(),
            default => $current->addMonthNoOverflow(),
        };
    }

    protected function calculateNextDueDate(): Carbon
    {
        return $this->calculateNextInvoiceDate()->addDays(30);
    }
}
