<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('closure_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('closure_id')->constrained('financial_closures')->cascadeOnDelete();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('item_type');
            $table->string('itemable_type');
            $table->unsignedBigInteger('itemable_id');
            $table->string('label');
            $table->string('deep_link');
            $table->decimal('amount', 15, 2)->nullable();
            $table->char('currency_code', 3)->default('USD');
            $table->string('severity')->default('info');
            $table->boolean('resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('closure_id');
            $table->index(['company_id', 'item_type']);
            $table->index(['itemable_type', 'itemable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('closure_items');
    }
};
