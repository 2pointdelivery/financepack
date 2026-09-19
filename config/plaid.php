<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Plaid Client ID
    |--------------------------------------------------------------------------
    |
    | Your Plaid API client ID. Obtain from https://dashboard.plaid.com/
    |
    */

    'client_id' => env('PLAID_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Plaid Client Secret
    |--------------------------------------------------------------------------
    |
    | Your Plaid API client secret. Keep this secret and never commit it.
    |
    */

    'client_secret' => env('PLAID_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Plaid Environment
    |--------------------------------------------------------------------------
    |
    | The Plaid environment to use: sandbox, development, or production.
    |
    */

    'environment' => env('PLAID_ENVIRONMENT', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Webhook URL
    |--------------------------------------------------------------------------
    |
    | The URL for Plaid webhook callbacks. Must end with /api/plaid/webhook.
    |
    */

    'webhook_url' => env('PLAID_WEBHOOK_URL'),

    /*
    |--------------------------------------------------------------------------
    | Transaction Days Requested
    |--------------------------------------------------------------------------
    |
    | Number of days of transaction history to request from Plaid.
    |
    */

    'transaction_days_requested' => 730,

];
