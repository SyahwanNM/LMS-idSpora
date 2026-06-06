<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$now = \Carbon\Carbon::now(config('app.timezone'));
echo "App Timezone: " . config('app.timezone') . "\n";
echo "Current Time (App Timezone): " . $now->format('Y-m-d H:i:s') . "\n";

$events = \App\Models\Event::whereNotNull('event_date')->orderBy('created_at', 'desc')->take(3)->get();
foreach ($events as $event) {
    echo "Event ID: " . $event->id . " | Title: " . $event->title . "\n";
    echo "  Event Date: " . $event->event_date . "\n";
    echo "  Event Time: " . $event->event_time . " - " . $event->event_time_end . "\n";
    
    // View logic simulation
    $eventDate = $event->event_date ? ($event->event_date instanceof \Carbon\Carbon ? $event->event_date : \Carbon\Carbon::parse($event->event_date, config('app.timezone'))) : null;
    
    $parseEventTime = function ($date, $raw) {
        if (empty($raw)) return null;
        if ($raw instanceof \Carbon\Carbon) return $raw;
        $rawStr = trim((string) $raw);
        $norm = preg_replace('/\s*(WIB|WITA|WIT)\s*$/i', '', $rawStr);
        if (preg_match('/^\d{1,2}\.\d{2}$/', $norm)) {
            $norm = str_replace('.', ':', $norm);
        }
        if (preg_match('/\d{4}-\d{2}-\d{2}/', $norm)) {
            try { return \Carbon\Carbon::parse($norm, config('app.timezone')); } catch (\Throwable $e) { return null; }
        }
        if ($date) {
            $dateStr = $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : (string) $date;
            try { return \Carbon\Carbon::parse($dateStr . ' ' . $norm, config('app.timezone')); } catch (\Throwable $e) { return null; }
        }
        try { return \Carbon\Carbon::parse($norm, config('app.timezone')); } catch (\Throwable $e) { return null; }
    };

    $startTime = $parseEventTime($eventDate, $event->event_time);
    $endTime = $parseEventTime($eventDate, $event->event_time_end);
    
    if (!$startTime && $eventDate) $startTime = $eventDate->copy()->startOfDay();
    if (!$endTime && $eventDate) $endTime = $eventDate->copy()->endOfDay();
    
    $eventStarted = $startTime ? $now->gte($startTime) : $now->isSameDay($eventDate);
    $eventFinished = $now->gt($endTime ? $endTime : $eventDate->copy()->endOfDay());

    echo "  Parsed Start Time: " . ($startTime ? $startTime->format('Y-m-d H:i:s P') : 'null') . "\n";
    echo "  Parsed End Time:   " . ($endTime ? $endTime->format('Y-m-d H:i:s P') : 'null') . "\n";
    echo "  eventStarted: " . ($eventStarted ? 'true' : 'false') . "\n";
    echo "  eventFinished: " . ($eventFinished ? 'true' : 'false') . "\n";
    echo "---------------------------\n";
}
