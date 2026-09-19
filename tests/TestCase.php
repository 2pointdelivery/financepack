<?php

declare(strict_types=1);

namespace FinancePack\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use FinancePack\Providers\FinancePackServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->register(FinancePackServiceProvider::class);

        $this->app['config']->set('database.default', 'testing');
        $this->app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $this->app['config']->set('financepack.company_model', 'FinancePack\\Models\\Company');
        $this->app['config']->set('financepack.user_model', 'App\\Models\\User');
        $this->app['config']->set('financepack.default_currency', 'USD');
    }

    protected function getPackageProviders($app): array
    {
        return [
            FinancePackServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('view.paths', [
            resource_path('views'),
            package_path('resources/views'),
        ]);

        $app['config']->set('app.key', 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=');
    }

    protected function createCompany(array $attributes = []): \FinancePack\Models\Company
    {
        return \FinancePack\Models\Company::create(array_merge([
            'name' => $this->faker->company(),
            'currency_code' => 'USD',
            'default_timezone' => 'UTC',
            'address' => $this->faker->address(),
            'phone_number' => $this->faker->phoneNumber(),
        ], $attributes));
    }

    protected function createAccount(\FinancePack\Models\Company $company, array $attributes = []): \FinancePack\Models\Accounting\Account
    {
        return \FinancePack\Models\Accounting\Account::withoutGlobalScopes()->create(array_merge([
            'company_id' => $company->id,
            'category' => 'asset',
            'type' => 'current_asset',
            'code' => $this->faker->unique()->numerify('####'),
            'name' => $this->faker->words(3, true),
            'currency_code' => $company->currency_code,
            'description' => $this->faker->sentence(),
            'archived' => false,
            'default' => false,
        ], $attributes));
    }

    protected function createBankAccount(\FinancePack\Models\Company $company, \FinancePack\Models\Accounting\Account $account, array $attributes = []): \FinancePack\Models\Banking\BankAccount
    {
        return \FinancePack\Models\Banking\BankAccount::create(array_merge([
            'company_id' => $company->id,
            'account_id' => $account->id,
            'name' => $this->faker->randomElement(['Chase', 'Bank of America', 'Wells Fargo']) . ' Checking',
            'bank_name' => $this->faker->randomElement(['Chase', 'Bank of America', 'Wells Fargo']),
            'account_number' => $this->faker->numerify('############'),
            'routing_number' => $this->faker->numerify('############'),
            'balance' => 0,
            'currency_code' => $company->currency_code,
        ], $attributes));
    }

    protected function createClient(\FinancePack\Models\Company $company, array $attributes = []): \FinancePack\Models\Common\Client
    {
        return \FinancePack\Models\Common\Client::create(array_merge([
            'company_id' => $company->id,
            'name' => $this->faker->company(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'currency_code' => $company->currency_code,
            'is_active' => true,
        ], $attributes));
    }

    protected function createVendor(\FinancePack\Models\Company $company, array $attributes = []): \FinancePack\Models\Common\Vendor
    {
        return \FinancePack\Models\Common\Vendor::create(array_merge([
            'company_id' => $company->id,
            'name' => $this->faker->company(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'currency_code' => $company->currency_code,
            'is_active' => true,
        ], $attributes));
    }
}
