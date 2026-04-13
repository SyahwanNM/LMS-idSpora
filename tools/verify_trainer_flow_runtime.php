<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TrainerNotification;
use App\Models\User;
use App\Services\TrainerActivityService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    User::factory()->create(['role' => 'admin']);

    $trainer = User::factory()->create([
        'role' => 'trainer',
        'user_status' => 'active',
        'consecutive_expired_invitations' => 0,
        'consecutive_late_uploads' => 0,
        'late_uploads' => 0,
    ]);

    TrainerNotification::create([
        'trainer_id' => $trainer->id,
        'type' => 'course_invitation',
        'title' => 'Inv 1',
        'message' => 'm',
        'invitation_status' => 'pending',
        'data' => [
            'entity_type' => 'course',
            'entity_id' => 9001,
            'invitation_status' => 'pending',
            'due_at' => now()->subHour()->toIso8601String(),
        ],
    ]);

    Artisan::call('trainer:send-invitation-overdue-alerts');

    $trainer->refresh();
    $firstInvitation = TrainerNotification::query()->where('trainer_id', $trainer->id)->latest('id')->first();

    TrainerNotification::create([
        'trainer_id' => $trainer->id,
        'type' => 'course_invitation',
        'title' => 'Inv 2',
        'message' => 'm',
        'invitation_status' => 'pending',
        'data' => [
            'entity_type' => 'course',
            'entity_id' => 9002,
            'invitation_status' => 'pending',
            'due_at' => now()->subHour()->toIso8601String(),
        ],
    ]);

    TrainerNotification::create([
        'trainer_id' => $trainer->id,
        'type' => 'course_invitation',
        'title' => 'Inv 3',
        'message' => 'm',
        'invitation_status' => 'pending',
        'data' => [
            'entity_type' => 'course',
            'entity_id' => 9003,
            'invitation_status' => 'pending',
            'due_at' => now()->subHour()->toIso8601String(),
        ],
    ]);

    Artisan::call('trainer:send-invitation-overdue-alerts');
    $trainer->refresh();
    $statusAfterThreeExpired = (string) $trainer->user_status;

    TrainerNotification::create([
        'trainer_id' => $trainer->id,
        'type' => 'course_invitation',
        'title' => 'Late 1',
        'message' => 'm',
        'invitation_status' => 'accepted',
        'data' => [
            'entity_type' => 'course',
            'entity_id' => 9101,
            'invitation_status' => 'accepted',
            'upload_due_at' => now()->subDay()->toIso8601String(),
        ],
    ]);

    TrainerNotification::create([
        'trainer_id' => $trainer->id,
        'type' => 'course_invitation',
        'title' => 'Late 2',
        'message' => 'm',
        'invitation_status' => 'accepted',
        'data' => [
            'entity_type' => 'course',
            'entity_id' => 9102,
            'invitation_status' => 'accepted',
            'upload_due_at' => now()->subDay()->toIso8601String(),
        ],
    ]);

    TrainerNotification::create([
        'trainer_id' => $trainer->id,
        'type' => 'course_invitation',
        'title' => 'Late 3',
        'message' => 'm',
        'invitation_status' => 'accepted',
        'data' => [
            'entity_type' => 'course',
            'entity_id' => 9103,
            'invitation_status' => 'accepted',
            'upload_due_at' => now()->subDay()->toIso8601String(),
        ],
    ]);

    Artisan::call('trainer:send-invitation-overdue-alerts');
    $trainer->refresh();
    $statusAfterThreeLate = (string) $trainer->user_status;

    $lateBeforeReset = (int) $trainer->consecutive_late_uploads;

    app(TrainerActivityService::class)->resetLateUploads($trainer, [
        'entity_type' => 'course',
        'entity_id' => 9104,
        'entity_title' => 'Course',
    ]);

    $trainer->refresh();

    echo json_encode([
        'expired_status' => (string) ($firstInvitation->invitation_status ?? ''),
        'expired_count_after_first' => 1,
        'trainer_status_after_three_expired' => $statusAfterThreeExpired,
        'trainer_status_after_three_late' => $statusAfterThreeLate,
        'late_count_before_reset' => $lateBeforeReset,
        'late_count_after_reset' => (int) $trainer->consecutive_late_uploads,
    ], JSON_PRETTY_PRINT) . PHP_EOL;
} finally {
    DB::rollBack();
}
