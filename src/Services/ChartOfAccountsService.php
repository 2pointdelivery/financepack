<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Contracts\Services\ChartOfAccountsServiceInterface;
use FinancePack\Enums\Accounting\AccountCategory;
use FinancePack\Enums\Accounting\AccountType;
use FinancePack\Enums\Accounting\AdjustmentCategory;
use FinancePack\Enums\Accounting\AdjustmentComputation;
use FinancePack\Models\Accounting\Account;
use FinancePack\Models\Accounting\AccountSubtype;
use FinancePack\Models\Accounting\Adjustment;
use FinancePack\Models\Company;
use Illuminate\Support\Facades\DB;

class ChartOfAccountsService implements ChartOfAccountsServiceInterface
{
    public function createChartOfAccounts(Company $company): void
    {
        $chartConfig = config('financepack.chart-of-accounts');

        if (empty($chartConfig)) {
            return;
        }

        DB::transaction(function () use ($company, $chartConfig) {
            foreach ($chartConfig as $category => $subtypes) {
                $accountCategory = AccountCategory::from($category);

                foreach ($subtypes as $subtypeKey => $subtypeData) {
                    $accountType = AccountType::from($subtypeData['type']);

                    $subtype = AccountSubtype::create([
                        'company_id' => $company->id,
                        'name' => $subtypeData['name'],
                        'multi_currency' => $subtypeData['multi_currency'] ?? false,
                        'inverse_cash_flow' => $subtypeData['inverse_cash_flow'] ?? false,
                        'category' => $accountCategory,
                        'type' => $accountType,
                    ]);

                    foreach ($subtypeData['accounts'] as $accountData) {
                        $account = Account::create([
                            'company_id' => $company->id,
                            'subtype_id' => $subtype->id,
                            'category' => $accountCategory,
                            'type' => $accountType,
                            'code' => $accountData['code'],
                            'name' => $accountData['name'],
                            'currency_code' => $company->currency_code,
                            'description' => $accountData['description'] ?? null,
                            'archived' => false,
                            'default' => $accountData['default'] ?? false,
                        ]);

                        if (($accountData['is_bank'] ?? false) && class_exists(\FinancePack\Models\Accounting\BankAccount::class)) {
                            \FinancePack\Models\Accounting\BankAccount::create([
                                'company_id' => $company->id,
                                'account_id' => $account->id,
                                'name' => $accountData['name'],
                                'bank_name' => $accountData['bank_name'] ?? null,
                                'type' => $accountData['bank_account_type'] ?? \FinancePack\Enums\Banking\BankAccountType::Checking,
                                'currency_code' => $company->currency_code,
                                'balance' => 0,
                            ]);
                        }

                        if (isset($accountData['adjustment'])) {
                            $adjustmentData = $accountData['adjustment'];

                            Adjustment::create([
                                'company_id' => $company->id,
                                'account_id' => $account->id,
                                'name' => $adjustmentData['name'],
                                'category' => AdjustmentCategory::from($adjustmentData['category']),
                                'computation' => AdjustmentComputation::from($adjustmentData['computation']),
                                'rate' => $adjustmentData['rate'] ?? 0,
                                'recoverable' => $adjustmentData['recoverable'] ?? false,
                            ]);
                        }
                    }
                }
            }
        });
    }
}
