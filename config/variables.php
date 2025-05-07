<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    | api_key can be added here if api requires it 
    |--------------------------------------------------------------------------
    */
    'currency' => [
        'base_currency' => 'EUR',
        'api_url' => 'https://developers.paysera.com/tasks/api/currency-exchange-rates',
        'scales' => [
            'JPY' => 0,
            'EUR' => 2,
            'USD' => 2,
            'default' => 2,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission Rates
    |--------------------------------------------------------------------------
    */
    'commission' => [
        'deposit_rate' => 0.0003, // 0.03%
        'private_withdraw_rate' => 0.003, // 0.3%
        'business_withdraw_rate' => 0.005, // 0.5%
        'private_free_limit_amount' => 1000.0,
        'private_free_operations_count' => 3,
    ],

        /*
    |--------------------------------------------------------------------------
    | Operation types
    |--------------------------------------------------------------------------
    */
    'operation' => [
        'TYPE_DEPOSIT' => 'deposit',
        'TYPE_WITHDRAW' => 'withdraw',
        'USER_TYPE_PRIVATE' => 'private',
        'USER_TYPE_BUSINESS' => 'business',
    ],
]; 