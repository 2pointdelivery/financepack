<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('currency_code', 3)->default('USD');
            $table->string('default_timezone')->default('UTC');
            $table->string('logo')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            $table->index('owner_id');
            $table->index('currency_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
