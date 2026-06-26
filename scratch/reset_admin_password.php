<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$admin = User::where('email', 'admin@idspora.com')->first();
if ($admin) {
    $admin->password = Hash::make('password');
    $admin->save();
    echo "Admin password reset successfully to: password\n";
} else {
    echo "Admin user not found.\n";
}
