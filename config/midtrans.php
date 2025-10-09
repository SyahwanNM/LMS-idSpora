<?php

return [
    'is_production' => (bool) env('MIDTRANS_PRODUCTION', false),
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'timeout' => env('MIDTRANS_TIMEOUT', 10),
    // Optional fraud detection and 3ds
    'enable_3ds' => (bool) env('MIDTRANS_ENABLE_3DS', true),
    'sanitize' => (bool) env('MIDTRANS_SANITIZE', true),
];
