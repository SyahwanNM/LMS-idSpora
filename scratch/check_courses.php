<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;
use App\Models\User;

echo "--- ALL COURSES ---\n";
$courses = Course::all();
foreach ($courses as $c) {
    $trainer = $c->trainer_id ? (User::find($c->trainer_id)->name ?? 'Unknown') : 'None';
    echo "ID: {$c->id} | Name: {$c->name} | Price: Rp " . number_format($c->price ?? 0, 2) . " | Trainer: {$trainer} (ID: {$c->trainer_id}) | Revenue Share: {$c->trainer_revenue_percent}%\n";
}
