<?php

declare(strict_types=1);

namespace FinancePack\Services;

use FinancePack\Contracts\Services\PlaidServiceInterface;
use GuzzleHttp\Client;

class PlaidService implements PlaidServiceInterface
{
    protected Client $client;
    protected string $clientId;
    protected string $clientSecret;
    protected string $environment;
    protected string $apiVersion = '2020-09-14';

    public function __construct(?Client $client = null)
    {
        $this->clientId = config('financepack.plaid.client_id', '');
        $this->clientSecret = config('financepack.plaid.client_secret', '');
        $this->environment = config('financepack.plaid.environment', 'sandbox');
        $this->client = $client ?? new Client();
    }

    public function isEnabled(): bool
    {
        return ! empty($this->clientId) && ! empty($this->clientSecret);
    }

    public function createLinkToken(string $clientName, string $language, array $countryCodes, array $user, array $products): object
    {
        return $this->makeRequest('link/token/create', [
            'client_id' => $this->clientId,
            'secret' => $this->clientSecret,
            'user' => $user,
            'client_name' => $clientName,
            'language' => $language,
            'country_codes' => $countryCodes,
            'products' => $products,
        ]);
    }

    public function exchangePublicToken(string $publicToken): object
    {
        return $this->makeRequest('item/public_token/exchange', [
            'client_id' => $this->clientId,
            'secret' => $this->clientSecret,
            'public_token' => $publicToken,
        ]);
    }

    public function getAccounts(string $accessToken, array $options = []): object
    {
        return $this->makeRequest('accounts/get', array_merge([
            'client_id' => $this->clientId,
            'secret' => $this->clientSecret,
            'access_token' => $accessToken,
        ], $options));
    }

    public function getInstitution(string $institutionId, string $country): object
    {
        return $this->makeRequest('institutions/get_by_id', [
            'client_id' => $this->clientId,
            'secret' => $this->clientSecret,
            'institution_id' => $institutionId,
            'country_codes' => [$country],
        ]);
    }

    public function getTransactions(string $accessToken, string $startDate, string $endDate, array $options = []): object
    {
        return $this->makeRequest('transactions/get', array_merge([
            'client_id' => $this->clientId,
            'secret' => $this->clientSecret,
            'access_token' => $accessToken,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ], $options));
    }

    public function refreshTransactions(string $accessToken): object
    {
        return $this->makeRequest('transactions/refresh', [
            'client_id' => $this->clientId,
            'secret' => $this->clientSecret,
            'access_token' => $accessToken,
        ]);
    }

    public function removeItem(string $accessToken): object
    {
        return $this->makeRequest('item/remove', [
            'client_id' => $this->clientId,
            'secret' => $this->clientSecret,
            'access_token' => $accessToken,
        ]);
    }

    protected function getBaseUrl(): string
    {
        return match ($this->environment) {
            'production' => 'https://production.plaid.com',
            'development' => 'https://development.plaid.com',
            default => 'https://sandbox.plaid.com',
        };
    }

    protected function getApiVersion(): string
    {
        return $this->apiVersion;
    }

    protected function buildHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'PLAID-CLIENT-ID' => $this->clientId,
            'PLAID-SECRET' => $this->clientSecret,
            'PLAID-API-VERSION' => $this->getApiVersion(),
        ];
    }

    protected function makeRequest(string $endpoint, array $payload): object
    {
        $response = $this->client->post("{$this->getBaseUrl()}/{$endpoint}", [
            'headers' => $this->buildHeaders(),
            'json' => $payload,
        ]);

        $body = json_decode($response->getBody()->getContents());

        if (isset($body->error)) {
            throw new \RuntimeException("Plaid API error: {$body->error->error_code} - {$body->error->display_message}");
        }

        return $body;
    }
}
