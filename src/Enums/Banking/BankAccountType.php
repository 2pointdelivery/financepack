<?php

namespace FinancePack\Enums\Banking;

enum BankAccountType: string
{
    case Depository = 'depository';
    case Credit = 'credit';
    case Loan = 'loan';
    case Investment = 'investment';

    public function getLabel(): string
    {
        return match ($this) {
            self::Depository => 'Depository',
            self::Credit => 'Credit',
            self::Loan => 'Loan',
            self::Investment => 'Investment',
        };
    }
}
