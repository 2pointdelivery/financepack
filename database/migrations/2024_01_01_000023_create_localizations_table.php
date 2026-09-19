<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('localizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('language_code', 10);
            $table->string('country_code', 10)->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('translations')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index(['language_code', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localizations');
    }
};
