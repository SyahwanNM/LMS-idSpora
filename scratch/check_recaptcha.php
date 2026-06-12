<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Site Key: " . config('services.recaptcha.site_key') . "\n";
echo "Secret Key: " . config('services.recaptcha.secret_key') . "\n";
