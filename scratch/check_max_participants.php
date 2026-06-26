<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$event = App\Models\Event::find(44);
if ($event) {
    echo "max_participants: " . var_export($event->max_participants, true) . "\n";
} else {
    echo "Event 44 not found.\n";
}
