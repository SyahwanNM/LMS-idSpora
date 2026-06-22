<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$e = \App\Models\Event::find(44);
if ($e) {
    echo "Event ID 44: Title: {$e->title}, Type: {$e->jenis}\n";
    echo " - event_date: {$e->event_date}\n";
    echo " - event_until_date: {$e->event_until_date}\n";
    echo " - start_submission: {$e->start_submission}\n";
    echo " - until_submission: {$e->until_submission}\n";
    echo " - announcement_date: {$e->announcement_date}\n";
    echo " - until_submission_2: {$e->until_submission_2}\n";
} else {
    echo "Event 44 not found\n";
}
