<?php

namespace Database\Factories\FinancePack;

use FinancePack\Models\Accounting\Bill;
use Illuminate\Database\Eloquent\Factories\Factory;

class BillFactory extends Factory
{
    protected $model = Bill::class;

    public function definition(): array
    {
        $statuses = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];
        $total = $this->faker->randomFloat(2, 50, 100000);
        $status = $this->faker->randomElement($statuses);

        return [
            'company_id' => null,
            'vendor_id' => null,
            'bill_number' => $this->faker->unique()->bothify('BILL-####-????'),
            'date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'due_date' => $this->faker->dateTimeBetween('+7 days', '+90 days'),
            'status' => $status,
            'total' => $total,
            'amount_paid' => $status === 'paid'
                ? $total
                : $this->faker->randomFloat(2, 0, $total),
            'currency_code' => $this->faker->currencyCode(),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => 'draft',
            'amount_paid' => 0,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => 'sent',
        ]);
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'paid',
                'amount_paid' => $attributes['total'],
            ];
        });
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => 'overdue',
            'due_date' => $this->faker->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => 'cancelled',
        ]);
    }

    public function forCompany(int $companyId): static
    {
        return $this->state(fn () => [
            'company_id' => $companyId,
        ]);
    }

    public function forVendor(int $vendorId): static
    {
        return $this->state(fn () => [
            'vendor_id' => $vendorId,
        ]);
    }

    public function partiallyPaid(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'sent',
                'amount_paid' => $this->faker->randomFloat(2, 0, $attributes['total']),
            ];
        });
    }
}
