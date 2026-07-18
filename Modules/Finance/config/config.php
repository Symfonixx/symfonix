<?php

return [
    'name' => 'Finance',

    /*
    |--------------------------------------------------------------------------
    | Default Sales Commission Percentage
    |--------------------------------------------------------------------------
    |
    | Applied when a deal is won and recordDealWonFinance() is invoked.
    |
    */
    'default_commission_percentage' => env('FINANCE_DEFAULT_COMMISSION_PERCENTAGE', 10),

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | Fallback when the Settings `default_currency` key is not set.
    | Runtime resolution should go through CurrencyService::defaultCurrency().
    |
    */
    'default_currency' => env('FINANCE_DEFAULT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Supported Currencies
    |--------------------------------------------------------------------------
    */
    'supported_currencies' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('FINANCE_SUPPORTED_CURRENCIES', 'USD,EUR,GBP,TRY'))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    */
    'invoice_number_prefix' => env('FINANCE_INVOICE_PREFIX', 'INV'),
    'invoice_payment_terms_days' => (int) env('FINANCE_INVOICE_PAYMENT_TERMS_DAYS', 30),
];
