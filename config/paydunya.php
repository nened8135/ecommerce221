<?php

return [
    'mode' => env('PAYDUNYA_MODE', 'test'),

    'master_key' => env('PAYDUNYA_MASTER_KEY'),
    'private_key' => env('PAYDUNYA_PRIVATE_KEY'),
    'token' => env('PAYDUNYA_TOKEN'),

    'test_customer_phone' => env('PAYDUNYA_TEST_CUSTOMER_PHONE'),
    'test_customer_email' => env('PAYDUNYA_TEST_CUSTOMER_EMAIL'),
    'test_customer_password' => env('PAYDUNYA_TEST_CUSTOMER_PASSWORD'),
];