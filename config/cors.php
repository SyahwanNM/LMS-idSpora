<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration determines what cross-origin operations may execute
    | in web browsers. By default we allow the production domain and common
    | local dev origins. Adjust via CORS_ALLOWED_ORIGINS in .env.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_filter(array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', 'https://academy.idspora.com,http://localhost,http://127.0.0.1:8000')))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Set true only if you authenticate via cookies (SPA stateful Sanctum).
    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),

];
