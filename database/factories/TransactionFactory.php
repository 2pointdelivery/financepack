<?php

namespace Database\Factories\FinancePack;

use FinancePack\Models\Accounting\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $types = ['credit', 'debit'];

        return [
            'company_id' => null,
            'account_id' => null,
            'bank_account_id' => null,
            'type' => $this->faker->randomElement($types),
            'amount' => $this->faker->randomFloat(2, 10, 50000),
            'description' => $this->faker->sentence(),
            'reference' => $this->faker->optional()->bothify('REF-####-????'),
            'posted_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function credit(): static
    {
        return $this->state(fn () => [
            'type' => 'credit',
        ]);
    }

    public function debit(): static
    {
        return $this->state(fn () => [
            'type' => 'debit',
        ]);
    }

    public function forCompany(int $companyId): static
    {
        return $this->state(fn () => [
            'company_id' => $companyId,
        ]);
    }

    public function forAccount(int $accountId): static
    {
        return $this->state(fn () => [
            'account_id' => $accountId,
        ]);
    }

    public function forBankAccount(int $bankAccountId): static
    {
        return $this->state(fn () => [
            'bank_account_id' => $bankAccountId,
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn () => [
            'posted_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
