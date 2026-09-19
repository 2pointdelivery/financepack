<?php

namespace Database\Factories\FinancePack;

use FinancePack\Models\Accounting\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        $categories = ['asset', 'liability', 'equity', 'revenue', 'expense'];
        $types = ['current_asset', 'fixed_asset', 'current_liability', 'long_term_liability', 'equity', 'income', 'cost_of_goods', 'operating_expense', 'non_operating_expense'];

        $category = $this->faker->randomElement($categories);
        $name = $this->faker->words(3, true);

        return [
            'company_id' => null,
            'subtype_id' => null,
            'category' => $category,
            'type' => $this->faker->randomElement($types),
            'code' => $this->faker->unique()->numerify('####'),
            'name' => ucfirst($name),
            'currency_code' => $this->faker->currencyCode(),
            'description' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(90),
            'opening_balance' => $this->faker->randomFloat(2, 0, 100000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function asset(): static
    {
        return $this->state(fn () => [
            'category' => 'asset',
            'type' => $this->faker->randomElement(['current_asset', 'fixed_asset']),
        ]);
    }

    public function liability(): static
    {
        return $this->state(fn () => [
            'category' => 'liability',
            'type' => $this->faker->randomElement(['current_liability', 'long_term_liability']),
        ]);
    }

    public function equity(): static
    {
        return $this->state(fn () => [
            'category' => 'equity',
            'type' => 'equity',
        ]);
    }

    public function revenue(): static
    {
        return $this->state(fn () => [
            'category' => 'revenue',
            'type' => 'income',
        ]);
    }

    public function expense(): static
    {
        return $this->state(fn () => [
            'category' => 'expense',
            'type' => $this->faker->randomElement(['cost_of_goods', 'operating_expense', 'non_operating_expense']),
        ]);
    }

    public function forCompany(int $companyId): static
    {
        return $this->state(fn () => [
            'company_id' => $companyId,
        ]);
    }
}
