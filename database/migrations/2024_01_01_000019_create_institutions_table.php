<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->string('plaid_institution_id')->nullable();
            $table->string('logo')->nullable();
            $table->string('url')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('plaid_institution_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
