<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('subtype_id')->nullable()->constrained('account_subtypes')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('category');
            $table->string('type');
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('currency_code', 3)->default('USD');
            $table->text('description')->nullable();
            $table->boolean('archived')->default(false);
            $table->boolean('default')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('subtype_id');
            $table->index('parent_id');
            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'category']);
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
