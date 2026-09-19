<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('bill_number');
            $table->string('order_number')->nullable();
            $table->date('date');
            $table->date('due_date')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
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
            $table->text('notes')->nullable();
            $table->text('footer')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('vendor_id');
            $table->index('status');
            $table->index('bill_number');
            $table->index('due_date');
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
