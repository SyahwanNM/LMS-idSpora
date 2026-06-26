<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$qrs = \App\Models\EventDailyQr::where('event_id', 44)->get();
echo "Daily QRs count: " . $qrs->count() . "\n";
foreach ($qrs as $q) {
    echo " - ID: {$q->id}, Day: {$q->day_number}, Date: {$q->qr_date}\n";
}
