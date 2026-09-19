<?php

namespace FinancePack\Providers;

use FinancePack\Services\AccountService;
use FinancePack\Services\CurrencyService;
use FinancePack\Services\TransactionService;
use FinancePack\Services\ReportService;
use FinancePack\Services\ChartOfAccountsService;
use FinancePack\Services\PlaidService;
use FinancePack\Contracts\Services\AccountServiceInterface;
use FinancePack\Contracts\Services\TransactionServiceInterface;
use FinancePack\Contracts\Services\ReportServiceInterface;
use FinancePack\Contracts\Services\ChartOfAccountsServiceInterface;
use FinancePack\Contracts\Services\CurrencyServiceInterface;
use FinancePack\Contracts\Services\PlaidServiceInterface;
use Illuminate\Support\ServiceProvider;

class FinancePackServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Config
        $this->mergeConfigFrom(__DIR__ . '/../../config/financepack.php', 'financepack');
        $this->mergeConfigFrom(__DIR__ . '/../../config/chart-of-accounts.php', 'financepack.chart-of-accounts');
        $this->mergeConfigFrom(__DIR__ . '/../../config/plaid.php', 'financepack.plaid');

        $this->publishes([
            __DIR__ . '/../../config/financepack.php' => config_path('financepack.php'),
            __DIR__ . '/../../config/chart-of-accounts.php' => config_path('chart-of-accounts.php'),
            __DIR__ . '/../../config/plaid.php' => config_path('financepack-plaid.php'),
        ], 'financepack-config');

        // Migrations
        $this->publishes([
            __DIR__ . '/../../database/migrations/' => database_path('migrations'),
        ], 'financepack-migrations');

        // Views
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'financepack');
        $this->publishes([
            __DIR__ . '/../../resources/views/' => resource_path('views/vendor/financepack'),
        ], 'financepack-views');

        // Translations
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'financepack');

        // Helpers
        require_once __DIR__ . '/../../src/Helpers/helpers.php';
    }

    public function register(): void
    {
        // Merge default config
        $this->mergeConfigFrom(__DIR__ . '/../../config/financepack.php', 'financepack');

        // Bind services as singletons
        $this->app->singleton(AccountServiceInterface::class, AccountService::class);
        $this->app->singleton(TransactionServiceInterface::class, TransactionService::class);
        $this->app->singleton(ReportServiceInterface::class, ReportService::class);
        $this->app->singleton(ChartOfAccountsServiceInterface::class, ChartOfAccountsService::class);
        $this->app->singleton(CurrencyServiceInterface::class, function ($app) {
            return new CurrencyService(
                apiKey: config('financepack.currency.api_key'),
                baseUrl: config('financepack.currency.base_url'),
                client: $app['http']->client(config('services.currency_api') ?? [])
            );
        });
        $this->app->singleton(PlaidServiceInterface::class, PlaidService::class);

        // Facade aliases
        $this->app->alias(AccountServiceInterface::class, 'financepack.accounting');
        $this->app->alias(CurrencyServiceInterface::class, 'financepack.forex');
    }
}
