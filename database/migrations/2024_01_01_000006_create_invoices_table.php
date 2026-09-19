<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained('estimates')->nullOnDelete();
            $table->foreignId('recurring_invoice_id')->nullable()->constrained('recurring_invoices')->nullOnDelete();
            $table->string('invoice_number');
            $table->string('order_number')->nullable();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('last_viewed_at')->nullable();
            $table->string('status')->default('draft');
            $table->string('currency_code', 3)->default('USD');
            $table->string('discount_method')->nullable();
            $table->string('discount_computation')->nullable();
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('client_id');
            $table->index('status');
            $table->index('invoice_number');
            $table->index('due_date');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
