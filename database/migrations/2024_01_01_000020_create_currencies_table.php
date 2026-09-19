<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('name');
            $table->string('symbol');
            $table->integer('precision')->default(2);
            $table->string('thousand_separator')->default(',');
            $table->string('decimal_separator')->default('.');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
