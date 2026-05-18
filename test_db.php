<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "BOOTED\n";
echo "User count: " . \App\Models\User::count() . "\n";
echo "Event count: " . \App\Models\Event::count() . "\n";
