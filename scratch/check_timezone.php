<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "config('app.timezone'): " . config('app.timezone') . "\n";
echo "Carbon::now(): " . \Carbon\Carbon::now()->toDateTimeString() . "\n";
echo "now(): " . now()->toDateTimeString() . "\n";
echo "Database event until_submission: " . \App\Models\Event::find(44)->until_submission . "\n";
