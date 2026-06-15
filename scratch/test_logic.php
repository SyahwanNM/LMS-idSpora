<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;

// Mock the current time to 2026-06-12 16:58:04
Carbon::setTestNow(Carbon::create(2026, 6, 12, 16, 58, 4));

$event = App\Models\Event::find(44);

// Simulate variable definitions in detail-event-registered.blade.php
$eventDate = $event->event_date ? Carbon::parse($event->event_date) : null;
$startTime = $event->event_time ? Carbon::parse($event->event_time) : null;
$endTime = $event->event_time_end ? Carbon::parse($event->event_time_end) : null;

if ($startTime && $eventDate && !$startTime->isSameDay($eventDate)) {
    $startTime = $eventDate->copy()->startOfDay();
}
if ($endTime && $eventDate && !$endTime->isSameDay($eventDate)) {
    $endTime = $eventDate->copy()->endOfDay();
}

$nowTs = Carbon::now();
$eventStarted = false;
$eventFinished = false;
if ($eventDate) {
    $eventStarted = $startTime ? $nowTs->gte($startTime) : $nowTs->isSameDay($eventDate);
    $eventFinished = $nowTs->gt($endTime ? $endTime : $eventDate->copy()->endOfDay());
}

$isRegistered = false; // Mock unregistered user

$hasDiscountLocal = method_exists($event, 'hasDiscount') ? $event->hasDiscount() : false;
$finalPriceLocal = $hasDiscountLocal ? ($event->discounted_price ?? $event->price) : $event->price;
$isFree = ((int) ($finalPriceLocal ?? 0)) === 0;

$hasStartTime = !empty($event->event_time);

if ($event->jenis === 'Lomba') {
    $canRegister = (!$isRegistered) && (
        $event->until_submission
        ? Carbon::now()->lt(Carbon::parse($event->until_submission))
        : true
    );
} else {
    $canRegister = (!$isRegistered) && (
        $eventDate
        ? (
             ($isFree ? ((!$eventStarted) && (!$eventFinished)) : ($hasStartTime ? (!$eventStarted) : (!$eventFinished)))
        )
        : true
    );
}

$evQuotaDetail   = !empty($event->max_participants) ? (int) $event->max_participants : null;
$evFilledDetail  = \App\Models\EventRegistration::where('event_id', $event->id)
                    ->whereIn('status', ['active', 'pending'])
                    ->count();
$evIsFullDetail  = $evQuotaDetail && $evFilledDetail >= $evQuotaDetail;
if ($evIsFullDetail && !$isRegistered) {
    $canRegister = false;
}

echo "Current Mock Time: " . Carbon::now()->toDateTimeString() . "\n";
echo "eventStarted: " . ($eventStarted ? 'true' : 'false') . "\n";
echo "eventFinished: " . ($eventFinished ? 'true' : 'false') . "\n";
echo "isFree: " . ($isFree ? 'true' : 'false') . "\n";
echo "canRegister: " . ($canRegister ? 'true' : 'false') . "\n";
echo "evIsFullDetail: " . ($evIsFullDetail ? 'true' : 'false') . "\n";
echo "isRegistered: " . ($isRegistered ? 'true' : 'false') . "\n";
