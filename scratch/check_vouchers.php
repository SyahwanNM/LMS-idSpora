<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = \App\Models\Voucher::count();
    echo "Total Vouchers in DB: " . $count . "\n";
    $vouchers = \App\Models\Voucher::all();
    foreach ($vouchers as $v) {
        echo "ID: {$v->id}, Code: {$v->code}, Name: {$v->name}, Active: " . ($v->active ? 'Yes' : 'No') . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
