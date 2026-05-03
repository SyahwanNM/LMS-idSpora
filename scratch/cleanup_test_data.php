<?php

use App\Models\Course;
use App\Models\Event;
use App\Models\Enrollment;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Starting cleanup of testing data...\n";

// 1. Clean Courses
$cIds = Course::where('name', 'like', '%testing%')
              ->orWhere('name', 'like', '%dummy%')
              ->pluck('id');

if ($cIds->isNotEmpty()) {
    $enrollCount = Enrollment::whereIn('course_id', $cIds)->delete();
    $courseCount = Course::whereIn('id', $cIds)->delete();
    echo "Deleted $courseCount courses and $enrollCount enrollments.\n";
} else {
    echo "No testing courses found.\n";
}

// 2. Clean Events
$eIds = Event::where('title', 'like', '%testing%')
             ->orWhere('title', 'like', '%dummy%')
             ->pluck('id');

if ($eIds->isNotEmpty()) {
    $regCount = EventRegistration::whereIn('event_id', $eIds)->delete();
    $eventCount = Event::whereIn('id', $eIds)->delete();
    echo "Deleted $eventCount events and $regCount registrations.\n";
} else {
    echo "No testing events found.\n";
}

// 3. Clean any orphaned certificates data in registrations if needed
// (Already handled by deleting registrations)

echo "Cleanup completed successfully.\n";
