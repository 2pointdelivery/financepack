<?php

namespace FinancePack\Contracts\Services;

use FinancePack\Models\Company;

interface ChartOfAccountsServiceInterface
{
    public function createChartOfAccounts(Company $company): void;
}
