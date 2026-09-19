<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('plaid_transaction_id')->nullable();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('type');
            $table->string('payment_channel')->nullable();
            $table->string('payment_method')->nullable();
            $table->boolean('is_payment')->default(false);
            $table->string('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('reference')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->boolean('pending')->default(false);
            $table->boolean('reviewed')->default(false);
            $table->timestamp('posted_at')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('account_id');
            $table->index('bank_account_id');
            $table->index('contact_id');
            $table->index('plaid_transaction_id');
            $table->index(['company_id', 'type']);
            $table->index('posted_at');
            $table->index('pending');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
