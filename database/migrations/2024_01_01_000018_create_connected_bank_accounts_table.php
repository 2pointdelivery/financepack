<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connected_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->cascadeOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->string('plaid_access_token')->nullable();
            $table->string('plaid_account_id')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('bank_account_id');
            $table->index('institution_id');
            $table->index('plaid_account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('connected_bank_accounts');
    }
};
