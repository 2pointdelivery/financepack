<?php

namespace Database\Factories\FinancePack;

use FinancePack\Models\Common\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition(): array
    {
        return [
            'company_id' => null,
            'name' => $this->faker->company(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->optional()->secondaryAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->stateAbbr(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->countryCode(),
            'currency_code' => $this->faker->currencyCode(),
            'tax_number' => $this->faker->optional()->bothify('TAX-########'),
            'website' => $this->faker->optional()->url(),
            'notes' => $this->faker->optional()->sentence(),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
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
}
