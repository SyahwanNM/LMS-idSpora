<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$u = \App\Models\User::find(24);
if ($u) {
    echo "User ID 24: Name: {$u->name}, Email: {$u->email}\n";
} else {
    echo "No user 24 found\n";
}
