<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'environment' => env('MIDTRANS_ENVIRONMENT', 'sandbox'),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    'payment_notification_url' => env('MIDTRANS_PAYMENT_NOTIFICATION_URL'),
    'redirect_url' => env('MIDTRANS_REDIRECT_URL'),
    'is_production' => env('MIDTRANS_ENVIRONMENT') === 'production',
    'is_sanitized' => true,
    'is_3ds' => true,
];
