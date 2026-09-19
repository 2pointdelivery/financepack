<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->morphs('documentable');
            $table->foreignId('offering_id')->nullable()->constrained('offerings')->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 15, 4)->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('discount_rate', 5, 2)->default(0);
            $table->decimal('discount_total', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('company_id');
            $table->index(['documentable_type', 'documentable_id']);
            $table->index('offering_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_line_items');
    }
};
