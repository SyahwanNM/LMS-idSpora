<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Course;

try {
    $course = Course::withCount(['enrollments', 'lessons', 'sections'])->first();
    echo "Success: relationships exist.\n";
    print_r($course->toArray());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
