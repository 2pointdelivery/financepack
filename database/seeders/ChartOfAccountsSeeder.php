<?php

namespace Database\Seeders\FinancePack;

use FinancePack\Models\Company;
use FinancePack\Services\ChartOfAccountsService;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    public function __construct(
        protected ChartOfAccountsService $chartOfAccountsService,
    ) {}

    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            $this->chartOfAccountsService->createChartOfAccounts($company);
        }
    }
}
