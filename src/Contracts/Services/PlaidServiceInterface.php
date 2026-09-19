<?php

namespace FinancePack\Contracts\Services;

interface PlaidServiceInterface
{
    public function isEnabled(): bool;

    public function createLinkToken(string $clientName, string $language, array $countryCodes, array $user, array $products): object;

    public function exchangePublicToken(string $publicToken): object;

    public function getAccounts(string $accessToken, array $options = []): object;

    public function getInstitution(string $institutionId, string $country): object;

    public function getTransactions(string $accessToken, string $startDate, string $endDate, array $options = []): object;

    public function refreshTransactions(string $accessToken): object;

    public function removeItem(string $accessToken): object;
}
