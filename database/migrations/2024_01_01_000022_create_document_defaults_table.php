<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_defaults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('document_type');
            $table->string('number_prefix')->nullable();
            $table->integer('next_number')->default(1);
            $table->text('terms')->nullable();
            $table->text('footer')->nullable();
            $table->string('logo')->nullable();
            $table->string('accent_color')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'document_type']);
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_defaults');
    }
};
