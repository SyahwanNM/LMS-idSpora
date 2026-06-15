<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "midtrans.client_key: " . var_export(config('midtrans.client_key'), true) . "\n";
echo "midtrans.server_key: " . var_export(config('midtrans.server_key'), true) . "\n";
echo "midtrans.is_production: " . var_export(config('midtrans.is_production'), true) . "\n";
