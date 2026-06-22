<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$event = App\Models\Event::find(44);
if ($event) {
    echo "jenis: '" . $event->jenis . "'\n";
    echo "bin2hex(jenis): " . bin2hex($event->jenis) . "\n";
    echo "strcmp(jenis, 'Lomba'): " . strcmp($event->jenis, 'Lomba') . "\n";
    echo "strcmp(jenis, 'lomba'): " . strcmp($event->jenis, 'lomba') . "\n";
} else {
    echo "Event 44 not found.\n";
}
