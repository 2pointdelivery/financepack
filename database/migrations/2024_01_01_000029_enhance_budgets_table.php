<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->decimal('growth_rate', 5, 2)->default(0)->after('notes');
            $table->char('currency_code', 3)->default('USD')->after('growth_rate');
            $table->string('type')->default('income_statement')->after('currency_code');
        });

        Schema::table('budget_items', function (Blueprint $table) {
            $table->string('category')->nullable()->after('account_id');
            $table->string('account_type')->nullable()->after('category');
            $table->decimal('growth_rate', 5, 2)->nullable()->after('account_type');
            $table->boolean('is_recurring')->default(true)->after('growth_rate');
            $table->integer('sort_order')->default(0)->after('is_recurring');
        });

        Schema::table('budget_allocations', function (Blueprint $table) {
            $table->decimal('growth_rate_applied', 5, 2)->nullable()->after('spent');
            $table->boolean('is_projected')->default(false)->after('growth_rate_applied');
            $table->decimal('actual_amount', 15, 2)->nullable()->after('is_projected');
            $table->decimal('variance', 15, 2)->nullable()->after('actual_amount');
            $table->decimal('variance_percent', 5, 2)->nullable()->after('variance');
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn(['growth_rate', 'currency_code', 'type']);
        });

        Schema::table('budget_items', function (Blueprint $table) {
            $table->dropColumn(['category', 'account_type', 'growth_rate', 'is_recurring', 'sort_order']);
        });

        Schema::table('budget_allocations', function (Blueprint $table) {
            $table->dropColumn(['growth_rate_applied', 'is_projected', 'actual_amount', 'variance', 'variance_percent']);
        });
    }
};
