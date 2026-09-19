<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_subtypes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('multi_currency')->default(false);
            $table->boolean('inverse_cash_flow')->default(false);
            $table->string('category');
            $table->string('type');
            $table->timestamps();

            $table->index('company_id');
            $table->index(['company_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_subtypes');
    }
};
