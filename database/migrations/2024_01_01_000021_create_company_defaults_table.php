<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_defaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('currency_code', 3)->default('USD');
            $table->string('date_format')->default('Y-m-d');
            $table->string('timezone')->default('UTC');
            $table->string('fiscal_year_start')->default('01');
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_defaults');
    }
};
