<?php
return [
    'deposit_commission_rate' => 0.03,
    'private_withdrawal_free_weekly_amount' => 1000,
    'private_withdrawal_max_weekly_discounts_number' => 3,
    'withdrawal_private_fee_rate' => 0.3,
    'withdrawal_business_fee_rate' => 0.5,
    'currency_default_scale' => 2,
    'currency_scale_map' => [
        'JPY' => 0,
    ],
    'currency_default_code' => 'EUR',
    'supported_currency_codes' => [
        'EUR',
        'USD',
        'JPY',
    ],
    'currency_api_url' => 'http://api.exchangeratesapi.io/v1/',
    'currency_api_key' => '82217a47065c2c0dc0e7a14e312536ba',
];