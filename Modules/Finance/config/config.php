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
    | Applied to ledger entries when no source currency is available.
    |
    */
    'default_currency' => env('FINANCE_DEFAULT_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    */
    'invoice_number_prefix' => env('FINANCE_INVOICE_PREFIX', 'INV'),
    'invoice_payment_terms_days' => (int) env('FINANCE_INVOICE_PAYMENT_TERMS_DAYS', 30),
];
