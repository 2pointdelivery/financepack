<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('name');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('spent', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('budget_id');
            $table->index('account_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
