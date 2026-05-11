<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

echo "APP_URL: " . env('APP_URL') . "\n";
echo "Public Disk URL for 'test.png': " . Storage::disk('public')->url('test.png') . "\n";
echo "Asset URL for 'test.png': " . asset('test.png') . "\n";
echo "URL helper for 'test.png': " . url('test.png') . "\n";
