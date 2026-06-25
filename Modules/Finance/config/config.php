<?php

return [
    'name' => 'Finance',

    /*
    |--------------------------------------------------------------------------
    | Default Sales Commission Percentage
    |--------------------------------------------------------------------------
    |
    | Applied when a deal is won and recordSaleAndCommission() is invoked.
    |
    */
    'default_commission_percentage' => env('FINANCE_DEFAULT_COMMISSION_PERCENTAGE', 10),
];
