<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_id')->constrained('budgets')->cascadeOnDelete();
            $table->foreignId('budget_item_id')->constrained('budget_items')->cascadeOnDelete();
            $table->string('period');
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('spent', 15, 2)->default(0);
            $table->timestamps();

            $table->index('budget_id');
            $table->index('budget_item_id');
            $table->index(['budget_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budget_allocations');
    }
};
