<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Event;

$event = Event::latest()->first();
echo "Event ID: " . $event->id . "\n";
echo "Title: " . $event->title . "\n";
echo "Certificate Logo: " . json_encode($event->certificate_logo) . "\n";
echo "Certificate Signature: " . json_encode($event->certificate_signature) . "\n";
