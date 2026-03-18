<?php

return [
    'enabled' => env('ORANGE_MONEY_ENABLED', false),
    'api_url' => env('ORANGE_MONEY_API_URL', 'https://api.orange.mg'),
    'api_key' => env('ORANGE_MONEY_API_KEY'),
    'api_secret' => env('ORANGE_MONEY_API_SECRET'),
    'merchant_id' => env('ORANGE_MERCHANT_ID'),
];
