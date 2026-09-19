<?php

namespace Database\Factories\FinancePack;

use FinancePack\Models\Banking\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition(): array
    {
        $bankNames = [
            'Chase', 'Bank of America', 'Wells Fargo', 'Citibank',
            'U.S. Bank', 'PNC Bank', 'Capital One', 'TD Bank',
            'BB&T', 'SunTrust', 'Goldman Sachs', 'Morgan Stanley',
        ];

        return [
            'company_id' => null,
            'account_id' => null,
            'name' => $this->faker->randomElement($bankNames) . ' ' . $this->faker->randomElement(['Checking', 'Savings', 'Business']),
            'bank_name' => $this->faker->randomElement($bankNames),
            'account_number' => $this->faker->numerify('############'),
            'routing_number' => $this->faker->numerify('############'),
            'account_type' => $this->faker->randomElement(['checking', 'savings', 'credit_card']),
            'balance' => $this->faker->randomFloat(2, 1000, 500000),
            'currency_code' => $this->faker->currencyCode(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function checking(): static
    {
        return $this->state(fn () => [
            'account_type' => 'checking',
        ]);
    }

    public function savings(): static
    {
        return $this->state(fn () => [
            'account_type' => 'savings',
        ]);
    }

    public function creditCard(): static
    {
        return $this->state(fn () => [
            'account_type' => 'credit_card',
        ]);
    }

    public function forCompany(int $companyId): static
    {
        return $this->state(fn () => [
            'company_id' => $companyId,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function withBalance(float $balance): static
    {
        return $this->state(fn () => [
            'balance' => $balance,
        ]);
    }
}
