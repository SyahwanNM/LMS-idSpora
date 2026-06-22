<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('role', 'trainer')->first();
if ($user) {
    echo "Trainer: {$user->name} (ID: {$user->id})\n";
    $payouts = \App\Models\TrainerPayment::where('user_id', $user->id)->get();
    echo "TrainerPayment count: " . $payouts->count() . "\n";
    foreach ($payouts as $p) {
        echo " - ID: {$p->id}, Title: {$p->title}, Amount: {$p->amount}, Status: {$p->status}, Date: {$p->payment_date}\n";
    }

    $courses = $user->coursesAsTrainer()->get();
    echo "Courses count: " . $courses->count() . "\n";
    foreach ($courses as $c) {
        $activeStudents = (int) $c->enrollments()->where('status', 'active')->count();
        echo " - Course ID: {$c->id}, Name: {$c->name}, Price: {$c->price}, Revenue %: {$c->trainer_revenue_percent}, Active Students: {$activeStudents}\n";
    }

    $assignments = \App\Models\TrainerAssignment::where('trainer_id', $user->id)->where('status', 'accepted')->get();
    echo "TrainerAssignment count: " . $assignments->count() . "\n";
    foreach ($assignments as $a) {
        $event = $a->event;
        if ($event) {
            $activeParticipants = (int) $event->registrations()->where('status', 'active')->count();
            echo " - Event ID: {$event->id}, Title: {$event->title}, Price: {$event->price}, Active Participants: {$activeParticipants}\n";
        }
    }
} else {
    echo "No trainer found\n";
}
