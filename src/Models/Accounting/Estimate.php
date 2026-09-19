<?php

declare(strict_types=1);

namespace FinancePack\Models\Accounting;

use FinancePack\Enums\Accounting\DocumentType;
use FinancePack\Models\Common\Client;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estimate extends Document
{
    protected $table = 'estimates';

    protected $fillable = [
        'company_id',
        'currency_code',
        'client_id',
        'estimate_number',
        'date',
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
        'date' => 'date',
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
        return DocumentType::Estimate;
    }

    public function documentNumber(): ?string
    {
        return $this->estimate_number;
    }

    public function documentDate(): ?string
    {
        return $this->date?->toDateString();
    }

    public function dueDate(): ?string
    {
        return null;
    }

    public function amountDue(): ?string
    {
        return (string) ($this->total - $this->amount_paid);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::get(fn () => ucfirst($this->status));
    }

    public function convertToInvoice(): Invoice
    {
        $invoice = Invoice::create([
            'company_id' => $this->company_id,
            'currency_code' => $this->currency_code,
            'client_id' => $this->client_id,
            'estimate_id' => $this->id,
            'account_id' => null,
            'invoice_number' => Invoice::getNextDocumentNumber($this->company),
            'order_number' => null,
            'date' => company_today()->toDateString(),
            'due_date' => null,
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
            'status' => \FinancePack\Enums\Accounting\InvoiceStatus::Draft,
        ]);

        $this->replicateLineItems($invoice);

        $this->update(['status' => 'accepted']);

        return $invoice;
    }
}
