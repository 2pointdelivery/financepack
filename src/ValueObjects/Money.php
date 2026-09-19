<?php

namespace FinancePack\ValueObjects;

use FinancePack\Utilities\Currency\CurrencyAccessor;
use FinancePack\Utilities\Currency\CurrencyConverter;

class Money
{
    private ?int $convertedAmount = null;

    public function __construct(
        private readonly int $amount,
        private ?string $currencyCode,
    ) {
        $this->currencyCode = $currencyCode ?: CurrencyAccessor::getDefaultCurrency();
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getEffectiveAmount(): int
    {
        return $this->convertedAmount ?? $this->amount;
    }

    public function getConvertedAmount(): ?int
    {
        return $this->convertedAmount;
    }

    public function getValue(): float
    {
        return money($this->amount, $this->currencyCode)->getValue();
    }

    public function format(): string
    {
        return money($this->getEffectiveAmount(), $this->getCurrencyCode())->format();
    }

    public function formatInDefaultCurrency(): string
    {
        return money($this->getEffectiveAmount(), CurrencyAccessor::getDefaultCurrency())->format();
    }

    public function formatSimple(): string
    {
        return money($this->getEffectiveAmount(), $this->getCurrencyCode())->formatSimple();
    }

    public function formatWithCode(bool $codeBefore = false): string
    {
        return money($this->getEffectiveAmount(), $this->getCurrencyCode())->formatWithCode($codeBefore);
    }

    public function convert(): self
    {
        $fromCurrency = CurrencyAccessor::getDefaultCurrency();
        $toCurrency = $this->currencyCode;

        if ($fromCurrency !== $toCurrency) {
            $this->convertedAmount = CurrencyConverter::convertBalance($this->amount, $fromCurrency, $toCurrency);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->formatSimple();
    }
}
