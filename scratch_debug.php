<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Event;
use App\Models\Course;

echo "Events total count: " . Event::count() . "\n";
$events = Event::withCount('registrations')->get();
echo "Events query count: " . $events->count() . "\n";

echo "Courses total count: " . Course::count() . "\n";
$courses = Course::withCount('enrollments')->get();
echo "Courses query count: " . $courses->count() . "\n";

if ($events->count() > 0) {
    echo "First event: " . $events->first()->title . " - Date: " . $events->first()->event_date . "\n";
}

if ($courses->count() > 0) {
    echo "First course: " . $courses->first()->name . " - Status: " . $courses->first()->status . "\n";
}
