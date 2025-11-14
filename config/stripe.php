<?php

return [
    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    'price_monthly' => env('STRIPE_PRICE_MONTHLY'),
    'price_yearly' => env('STRIPE_PRICE_YEARLY'),
];
