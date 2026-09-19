<?php

namespace FinancePack\Enums\Accounting;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Check = 'check';
    case BankTransfer = 'bank_transfer';
    case CreditCard = 'credit_card';
    case DebitCard = 'debit_card';
    case PayPal = 'paypal';
    case Stripe = 'stripe';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Check => 'Check',
            self::BankTransfer => 'Bank Transfer',
            self::CreditCard => 'Credit Card',
            self::DebitCard => 'Debit Card',
            self::PayPal => 'PayPal',
            self::Stripe => 'Stripe',
            self::Other => 'Other',
        };
    }
}
