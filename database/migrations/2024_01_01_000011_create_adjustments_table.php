<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->decimal('rate', 5, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->boolean('is_compound')->default(false);
            $table->boolean('is_percent')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjustments');
    }
};
