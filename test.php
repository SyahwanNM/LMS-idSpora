<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = \Illuminate\Http\Request::create('/admin/events/3/registrations/23/check-in', 'POST', ['day_number' => 1]);
// We need to bypass auth for this CLI test or mock a user.
\Illuminate\Support\Facades\Auth::loginUsingId(1); // admin user

$controller = new \App\Http\Controllers\Admin\EventController();
$event = \App\Models\Event::find(3);
$registration = \App\Models\EventRegistration::find(23);

try {
    $response = $controller->manualCheckIn($event, $registration, $request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
