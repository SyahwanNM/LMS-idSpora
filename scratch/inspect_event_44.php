<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$event = App\Models\Event::find(44);
if ($event) {
    echo "ID: {$event->id}\n";
    echo "Title: {$event->title}\n";
    echo "Jenis: {$event->jenis}\n";
    echo "Price: {$event->price}\n";
    echo "Event Date: {$event->event_date}\n";
    echo "Event Until Date: {$event->event_until_date}\n";
    echo "Event Time: {$event->event_time}\n";
    echo "Event Time End: {$event->event_time_end}\n";
    echo "Event Until Time: {$event->event_until_time}\n";
    echo "Until Submission: {$event->until_submission}\n";
    echo "Is Published: " . ($event->is_published ? 'true' : 'false') . "\n";
} else {
    echo "Event 44 not found.\n";
}
