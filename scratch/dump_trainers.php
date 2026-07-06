<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = Schema::getTables();
echo "Tables in DB:\n";
foreach ($tables as $t) {
    echo "- " . $t['name'] . "\n";
}
