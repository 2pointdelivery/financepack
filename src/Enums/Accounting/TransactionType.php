<?php

namespace FinancePack\Enums\Accounting;

use FinancePack\Enums\Concerns\ParsesEnum;

enum TransactionType: string
{
    use ParsesEnum;

    case Deposit = 'deposit';
    case Withdrawal = 'withdrawal';
    case Journal = 'journal';
    case Transfer = 'transfer';

    public function getLabel(): string
    {
        return match ($this) {
            self::Deposit => 'Deposit',
            self::Withdrawal => 'Withdrawal',
            self::Journal => 'Journal',
            self::Transfer => 'Transfer',
        };
    }

    public function isDeposit(): bool
    {
        return $this === self::Deposit;
    }

    public function isWithdrawal(): bool
    {
        return $this === self::Withdrawal;
    }

    public function isJournal(): bool
    {
        return $this === self::Journal;
    }

    public function isTransfer(): bool
    {
        return $this === self::Transfer;
    }

    public function isStandard(): bool
    {
        return in_array($this, [self::Deposit, self::Withdrawal], true);
    }
}
