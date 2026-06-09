<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$event = \App\Models\Event::find(7);
if ($event) {
    echo "Event materi type: " . gettype($event->materi) . "\n";
    echo "Event materi value: \n";
    var_dump($event->materi);
}
