<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$admins = User::where('role', 'admin')->get();
foreach ($admins as $a) {
    echo "Admin ID: {$a->id} | Name: {$a->name} | Email: {$a->email}\n";
}
