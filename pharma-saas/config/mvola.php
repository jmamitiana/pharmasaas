<?php

return [
    'enabled' => env('MVOLA_ENABLED', false),
    'api_url' => env('MVOLA_API_URL', 'https://api.mvola.mg'),
    'api_key' => env('MVOLA_API_KEY'),
    'api_secret' => env('MVOLA_API_SECRET'),
    'merchant_id' => env('MVOLA_MERCHANT_ID'),
];
