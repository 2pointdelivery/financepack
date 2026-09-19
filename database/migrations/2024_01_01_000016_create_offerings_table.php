<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->string('unit_name')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('cost_price', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_taxable')->default(true);
            $table->string('tax_rate_id')->nullable();
            $table->string('currency_code', 3)->default('USD');
            $table->boolean('archived')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('type');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerings');
    }
};
