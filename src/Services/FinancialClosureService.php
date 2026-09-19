<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Contracts\Services\FinancialClosureServiceInterface;
use FinancePack\Contracts\Services\ReportServiceInterface;
use FinancePack\DTO\ClosureResultDTO;
use FinancePack\Enums\Accounting\ClosureStatus;
use FinancePack\Enums\Accounting\OutstandingItemType;
use FinancePack\Enums\Accounting\ItemSeverity;
use FinancePack\Enums\Accounting\InvoiceStatus;
use FinancePack\Enums\Accounting\BillStatus;
use FinancePack\Models\Accounting\FinancialClosure;
use FinancePack\Models\Accounting\ClosureItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialClosureService implements FinancialClosureServiceInterface
{
    public function __construct(
        protected ReportServiceInterface $reportService,
    ) {}

    public function runClosure(string $companyId, string $periodStart, string $periodEnd): ClosureResultDTO
    {
        $closure = FinancialClosure::where('company_id', $companyId)
            ->where('period_start', $periodStart)
            ->where('period_end', $periodEnd)
            ->first();

        if (! $closure) {
            $closure = FinancialClosure::create([
                'company_id' => $companyId,
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'status' => ClosureStatus::Draft,
            ]);
        }

        $existingItems = $closure->items()->get();

        if ($existingItems->isEmpty()) {
            $items = $this->collectOutstandingItems($companyId, $periodStart, $periodEnd);
            $closure->items()->createMany($items->toArray());
        }

        $closure->load('items');

        $reportSnapshots = $this->generateReportSnapshots($companyId, $periodStart, $periodEnd);

        $closure->update(['summary' => $reportSnapshots]);

        $totalOutstanding = $closure->items->where('resolved', false)->sum('amount');
        $hasCritical = $closure->items->where('severity', ItemSeverity::Critical)->where('resolved', false)->isNotEmpty();

        return new ClosureResultDTO(
            closure: $closure,
            outstandingItems: $closure->items,
            reportSnapshots: $reportSnapshots,
            totalOutstandingCents: (int) $totalOutstanding,
            itemCount: $closure->items->where('resolved', false)->count(),
            canClose: ! $hasCritical,
        );
    }

    public function collectOutstandingItems(string $companyId, string $periodStart, string $periodEnd): Collection
    {
        $items = collect();

        $items = $items->merge($this->findUnpaidInvoices($companyId, $periodEnd));
        $items = $items->merge($this->findUnpaidBills($companyId, $periodEnd));
        $items = $items->merge($this->findUnmatchedJournalEntries($companyId, $periodStart, $periodEnd));
        $items = $items->merge($this->findOverduePayments($companyId, $periodEnd));
        $items = $items->merge($this->findPendingReconciliations($companyId));
        $items = $items->merge($this->findDraftDocuments($companyId));

        return $items;
    }

    public function markPendingReview(int $closureId): FinancialClosure
    {
        $closure = FinancialClosure::findOrFail($closureId);
        $closure->update(['status' => ClosureStatus::PendingReview]);

        return $closure->fresh();
    }

    public function closePeriod(int $closureId, ?int $userId): FinancialClosure
    {
        $closure = FinancialClosure::findOrFail($closureId);
        $closure->update([
            'status' => ClosureStatus::Closed,
            'closed_at' => now(),
            'closed_by' => $userId,
        ]);

        return $closure->fresh();
    }

    public function reopenPeriod(int $closureId, ?int $userId, ?string $reason): FinancialClosure
    {
        $closure = FinancialClosure::findOrFail($closureId);
        $closure->update([
            'status' => ClosureStatus::Reopened,
            'reopened_at' => now(),
            'reopened_by' => $userId,
            'notes' => $reason,
        ]);

        return $closure->fresh();
    }

    public function isPeriodLocked(string $companyId, string $date): bool
    {
        return FinancialClosure::where('company_id', $companyId)
            ->where('status', ClosureStatus::Closed)
            ->where('period_start', '<=', $date)
            ->where('period_end', '>=', $date)
            ->exists();
    }

    public function getClosureHistory(string $companyId): Collection
    {
        return FinancialClosure::where('company_id', $companyId)
            ->with('items')
            ->orderByDesc('period_end')
            ->get();
    }

    protected function findUnpaidInvoices(string $companyId, string $periodEnd): Collection
    {
        $invoices = DB::table('invoices')
            ->join('clients', 'invoices.client_id', '=', 'clients.id')
            ->whereIn('invoices.status', InvoiceStatus::unpaidStatuses())
            ->where('invoices.company_id', $companyId)
            ->where('invoices.due_date', '<=', $periodEnd)
            ->select(
                'invoices.id',
                'invoices.invoice_number',
                'invoices.status',
                'invoices.due_date',
                'invoices.total',
                'invoices.amount_paid',
                'invoices.currency_code',
                'clients.name as client_name'
            )
            ->get();

        return $invoices->map(function ($invoice) {
            $dueDate = $invoice->due_date ? Carbon::parse($invoice->due_date) : null;
            $daysOverdue = $dueDate && $dueDate->isPast() ? (int) $dueDate->diffInDays(now()) : 0;
            $balance = (float) $invoice->total - (float) $invoice->amount_paid;
            $amountCents = (int) ($balance * 100);

            return [
                'item_type' => OutstandingItemType::UnpaidInvoice,
                'itemable_type' => 'FinancePack\\Models\\Accounting\\Invoice',
                'itemable_id' => $invoice->id,
                'label' => "Invoice {$invoice->invoice_number} — {$invoice->client_name}",
                'deep_link' => "/invoices/{$invoice->id}",
                'amount' => $amountCents,
                'currency_code' => $invoice->currency_code ?? 'USD',
                'severity' => $this->determineSeverity($amountCents, $daysOverdue),
            ];
        });
    }

    protected function findUnpaidBills(string $companyId, string $periodEnd): Collection
    {
        $bills = DB::table('bills')
            ->join('vendors', 'bills.vendor_id', '=', 'vendors.id')
            ->whereIn('bills.status', BillStatus::unpaidStatuses())
            ->where('bills.company_id', $companyId)
            ->where('bills.due_date', '<=', $periodEnd)
            ->select(
                'bills.id',
                'bills.bill_number',
                'bills.status',
                'bills.due_date',
                'bills.total',
                'bills.amount_paid',
                'bills.currency_code',
                'vendors.name as vendor_name'
            )
            ->get();

        return $bills->map(function ($bill) {
            $dueDate = $bill->due_date ? Carbon::parse($bill->due_date) : null;
            $daysOverdue = $dueDate && $dueDate->isPast() ? (int) $dueDate->diffInDays(now()) : 0;
            $balance = (float) $bill->total - (float) $bill->amount_paid;
            $amountCents = (int) ($balance * 100);

            return [
                'item_type' => OutstandingItemType::UnpaidBill,
                'itemable_type' => 'FinancePack\\Models\\Accounting\\Bill',
                'itemable_id' => $bill->id,
                'label' => "Bill {$bill->bill_number} — {$bill->vendor_name}",
                'deep_link' => "/bills/{$bill->id}",
                'amount' => $amountCents,
                'currency_code' => $bill->currency_code ?? 'USD',
                'severity' => $this->determineSeverity($amountCents, $daysOverdue),
            ];
        });
    }

    protected function findUnmatchedJournalEntries(string $companyId, string $periodStart, string $periodEnd): Collection
    {
        $unmatched = DB::table('journal_entries')
            ->join('transactions', 'journal_entries.transaction_id', '=', 'transactions.id')
            ->where('journal_entries.company_id', $companyId)
            ->whereBetween('transactions.posted_at', [$periodStart, $periodEnd . ' 23:59:59'])
            ->groupBy('journal_entries.transaction_id')
            ->havingRaw('SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END) != SUM(CASE WHEN journal_entries.type = ? THEN journal_entries.amount ELSE 0 END)', ['debit', 'credit'])
            ->select('journal_entries.transaction_id')
            ->get();

        return $unmatched->map(function ($entry) {
            return [
                'item_type' => OutstandingItemType::UnmatchedJournal,
                'itemable_type' => 'FinancePack\\Models\\Accounting\\Transaction',
                'itemable_id' => $entry->transaction_id,
                'label' => "Unmatched journal entries for transaction #{$entry->transaction_id}",
                'deep_link' => "/journal-entries?transaction_id={$entry->transaction_id}",
                'amount' => null,
                'currency_code' => 'USD',
                'severity' => ItemSeverity::Critical,
            ];
        });
    }

    protected function findOverduePayments(string $companyId, string $periodEnd): Collection
    {
        $overdueInvoices = DB::table('invoices')
            ->where('company_id', $companyId)
            ->where('status', InvoiceStatus::Overdue)
            ->where('due_date', '<=', $periodEnd)
            ->select('id', 'invoice_number', 'total', 'amount_paid', 'currency_code', 'due_date')
            ->get();

        $overdueBills = DB::table('bills')
            ->where('company_id', $companyId)
            ->where('status', BillStatus::Overdue)
            ->where('due_date', '<=', $periodEnd)
            ->select('id', 'bill_number', 'total', 'amount_paid', 'currency_code', 'due_date')
            ->get();

        $items = collect();

        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = (int) Carbon::parse($invoice->due_date)->diffInDays(now());
            $balance = (float) $invoice->total - (float) $invoice->amount_paid;
            $items->push([
                'item_type' => OutstandingItemType::OverduePayment,
                'itemable_type' => 'FinancePack\\Models\\Accounting\\Invoice',
                'itemable_id' => $invoice->id,
                'label' => "Overdue invoice {$invoice->invoice_number}",
                'deep_link' => "/invoices/{$invoice->id}",
                'amount' => (int) ($balance * 100),
                'currency_code' => $invoice->currency_code ?? 'USD',
                'severity' => $this->determineSeverity((int) ($balance * 100), $daysOverdue),
            ]);
        }

        foreach ($overdueBills as $bill) {
            $daysOverdue = (int) Carbon::parse($bill->due_date)->diffInDays(now());
            $balance = (float) $bill->total - (float) $bill->amount_paid;
            $items->push([
                'item_type' => OutstandingItemType::OverduePayment,
                'itemable_type' => 'FinancePack\\Models\\Accounting\\Bill',
                'itemable_id' => $bill->id,
                'label' => "Overdue bill {$bill->bill_number}",
                'deep_link' => "/bills/{$bill->id}",
                'amount' => (int) ($balance * 100),
                'currency_code' => $bill->currency_code ?? 'USD',
                'severity' => $this->determineSeverity((int) ($balance * 100), $daysOverdue),
            ]);
        }

        return $items;
    }

    protected function findPendingReconciliations(string $companyId): Collection
    {
        $bankAccounts = DB::table('bank_accounts')
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->get();

        $items = collect();

        foreach ($bankAccounts as $bankAccount) {
            $lastReconciliation = DB::table('connected_bank_accounts')
                ->where('bank_account_id', $bankAccount->id)
                ->max('last_synced_at');

            $hasUnreconciled = DB::table('transactions')
                ->where('bank_account_id', $bankAccount->id)
                ->where('company_id', $companyId)
                ->where('reviewed', false)
                ->exists();

            if ($hasUnreconciled) {
                $items->push([
                    'item_type' => OutstandingItemType::PendingReconciliation,
                    'itemable_type' => 'FinancePack\\Models\\Banking\\BankAccount',
                    'itemable_id' => $bankAccount->id,
                    'label' => "Bank account {$bankAccount->name} has unreconciled transactions",
                    'deep_link' => "/bank-accounts/{$bankAccount->id}/reconcile",
                    'amount' => null,
                    'currency_code' => $bankAccount->currency_code ?? 'USD',
                    'severity' => ItemSeverity::Warning,
                ]);
            }
        }

        return $items;
    }

    protected function findDraftDocuments(string $companyId): Collection
    {
        $items = collect();

        $draftInvoices = DB::table('invoices')
            ->where('company_id', $companyId)
            ->where('status', InvoiceStatus::Draft)
            ->get();

        foreach ($draftInvoices as $invoice) {
            $items->push([
                'item_type' => OutstandingItemType::DraftDocument,
                'itemable_type' => 'FinancePack\\Models\\Accounting\\Invoice',
                'itemable_id' => $invoice->id,
                'label' => "Draft invoice {$invoice->invoice_number}",
                'deep_link' => "/invoices/{$invoice->id}",
                'amount' => (int) ($invoice->amount * 100),
                'currency_code' => $invoice->currency_code ?? 'USD',
                'severity' => ItemSeverity::Info,
            ]);
        }

        $draftBills = DB::table('bills')
            ->where('company_id', $companyId)
            ->where('status', BillStatus::Draft)
            ->get();

        foreach ($draftBills as $bill) {
            $items->push([
                'item_type' => OutstandingItemType::DraftDocument,
                'itemable_type' => 'FinancePack\\Models\\Accounting\\Bill',
                'itemable_id' => $bill->id,
                'label' => "Draft bill {$bill->bill_number}",
                'deep_link' => "/bills/{$bill->id}",
                'amount' => (int) ($bill->amount * 100),
                'currency_code' => $bill->currency_code ?? 'USD',
                'severity' => ItemSeverity::Info,
            ]);
        }

        $draftEstimates = DB::table('estimates')
            ->where('company_id', $companyId)
            ->where('status', 'draft')
            ->get();

        foreach ($draftEstimates as $estimate) {
            $items->push([
                'item_type' => OutstandingItemType::DraftDocument,
                'itemable_type' => 'FinancePack\\Models\\Accounting\\Estimate',
                'itemable_id' => $estimate->id,
                'label' => "Draft estimate {$estimate->estimate_number}",
                'deep_link' => "/estimates/{$estimate->id}",
                'amount' => (int) ($estimate->amount * 100),
                'currency_code' => $estimate->currency_code ?? 'USD',
                'severity' => ItemSeverity::Info,
            ]);
        }

        return $items;
    }

    protected function determineSeverity(int $amountCents, int $daysOverdue): ItemSeverity
    {
        if ($daysOverdue > 30 || $amountCents > 10000000) {
            return ItemSeverity::Critical;
        }

        if ($daysOverdue > 0 || $amountCents > 1000000) {
            return ItemSeverity::Warning;
        }

        return ItemSeverity::Info;
    }

    protected function generateReportSnapshots(string $companyId, string $periodStart, string $periodEnd): array
    {
        return [
            'income_statement' => $this->reportService->buildIncomeStatementReport($periodStart, $periodEnd),
            'balance_sheet' => $this->reportService->buildBalanceSheetReport($periodEnd),
            'trial_balance' => $this->reportService->buildTrialBalanceReport('postClosing', $periodEnd),
        ];
    }
}
