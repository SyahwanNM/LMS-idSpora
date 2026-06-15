<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$regs = \App\Models\EventRegistration::where('event_id', 44)->get();
echo "Total registrations for Event 44: " . $regs->count() . "\n";
foreach ($regs as $reg) {
    echo "Reg ID: {$reg->id} | User ID: {$reg->user_id} | Status: {$reg->status} | Created: {$reg->created_at}\n";
}
