<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$events = App\Models\Event::select('id', 'title', 'jenis', 'event_date', 'until_submission')->get();
foreach ($events as $event) {
    echo "ID: {$event->id} | Title: {$event->title} | Jenis: {$event->jenis} | Date: {$event->event_date} | Until Sub: {$event->until_submission}\n";
}
