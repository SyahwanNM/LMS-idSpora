<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\ManualPayment;
use App\Models\TrainerPayment;
use App\Models\Course;

echo "--- TRAINERS AND THEIR BALANCES ---\n";
$trainers = User::whereIn('role', ['trainer', 'Trainer'])->get();
foreach ($trainers as $t) {
    echo "ID: {$t->id} | Name: {$t->name} | Email: {$t->email} | Balance: Rp " . number_format($t->wallet_balance, 2) . "\n";
}

echo "\n--- SETTLED COURSE PAYMENTS ---\n";
$coursePayments = ManualPayment::where('status', 'settled')
    ->whereNotNull('course_id')
    ->get();
echo "Total settled course payments: " . $coursePayments->count() . "\n";
foreach ($coursePayments as $p) {
    $course = Course::find($p->course_id);
    $trainerName = $course && $course->trainer_id ? (User::find($course->trainer_id)->name ?? 'Unknown') : 'None (trainer_id NULL)';
    $trainerId = $course ? $course->trainer_id : 'N/A';
    echo "Payment ID: {$p->id} | Course: " . ($course->name ?? 'N/A') . " (Trainer ID: {$trainerId}, Name: {$trainerName}) | Amount: Rp " . number_format($p->amount, 2) . " | Date: {$p->created_at}\n";
}

echo "\n--- TRAINER PAYMENTS (FEE EVENTS / PAYOUTS) ---\n";
$tPayments = TrainerPayment::all();
foreach ($tPayments as $tp) {
    echo "ID: {$tp->id} | Trainer: {$tp->trainer_name} (User ID: {$tp->user_id}) | Title: {$tp->title} | Amount: Rp " . number_format($tp->amount, 2) . " | Type: {$tp->type} | Status: {$tp->status}\n";
}
