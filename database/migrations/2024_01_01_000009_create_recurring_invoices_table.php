<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('invoice_number');
            $table->string('frequency');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->date('next_date')->nullable();
            $table->string('status')->default('active');
            $table->string('currency_code', 3)->default('USD');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('client_id');
            $table->index('status');
            $table->index('next_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_invoices');
    }
};
