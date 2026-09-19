<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company Model
    |--------------------------------------------------------------------------
    |
    | The company model used for multi-tenancy. Replace this with your own
    | Company model if needed.
    |
    */

    'company_model' => env('FINANCEPACK_COMPANY_MODEL', 'App\\Models\\Company'),

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The user model used for authentication and blamable traits.
    |
    */

    'user_model' => env('FINANCEPACK_USER_MODEL', 'App\\Models\\User'),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency code used for company financial reporting.
    |
    */

    'default_currency' => env('FINANCEPACK_DEFAULT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Currency API
    |--------------------------------------------------------------------------
    |
    | Configuration for live currency exchange rate service.
    |
    */

    'currency' => [
        'api_key' => env('CURRENCY_API_KEY'),
        'base_url' => env('CURRENCY_API_BASE_URL', 'https://v6.exchangerate-api.com/v6'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Generation
    |--------------------------------------------------------------------------
    |
    | Configuration for PDF report generation using Laravel Snappy.
    |
    */

    'pdf' => [
        'driver' => env('FINANCEPACK_PDF_DRIVER', 'snappy'),
        'wkhtmltopdf_binary' => env('WKHTMLTOPDF_BINARY', '/usr/local/bin/wkhtmltopdf'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for transaction processing.
    |
    */

    'transactions' => [
        'auto_create_journal_entries' => true,
        'require_approval_for_large_amounts' => false,
        'large_amount_threshold' => 1000000, // cents
    ],

    /*
    |--------------------------------------------------------------------------
    | Plaid Settings (deprecated - use financepack.plaid config)
    |--------------------------------------------------------------------------
    |
    | @deprecated Use config/financepack-plaid.php instead.
    |
    */

    'plaid' => [
        'client_id' => env('PLAID_CLIENT_ID'),
        'client_secret' => env('PLAID_CLIENT_SECRET'),
        'environment' => env('PLAID_ENVIRONMENT', 'sandbox'),
        'webhook_url' => env('PLAID_WEBHOOK_URL'),
    ],

];
