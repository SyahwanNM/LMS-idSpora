<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\Review;
use App\Models\TrainerNotification;
use App\Models\User;
use Carbon\Carbon;

class TrainerActivityService
{
    /**
     * @var array<int, array<string, mixed>>
     */
    private array $summaryCache = [];

    public function tiers(): array
    {
        return [
            'associate' => [
                'label' => 'Associate Trainer',
                'rank' => 1,
                'color' => '#64748b',
            ],
            'professional' => [
                'label' => 'Professional Trainer',
                'rank' => 2,
                'color' => '#0f766e',
            ],
            'expert' => [
                'label' => 'Expert Trainer',
                'rank' => 3,
                'color' => '#b45309',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(User $trainer, bool $persist = false): array
    {
        $trainerId = (int) ($trainer->id ?? 0);
        if ($trainerId <= 0) {
            return $this->defaultSummary();
        }

        if (!$persist && isset($this->summaryCache[$trainerId])) {
            return $this->summaryCache[$trainerId];
        }

        $totalCoursesCompleted = $this->countCompletedActivities($trainerId);
        $averageRating = $this->calculateAverageRating($trainerId);
        $lateUploads = max(0, (int) ($trainer->consecutive_late_uploads ?? $trainer->late_uploads ?? 0));
        $expiredInvitations = max(0, (int) ($trainer->consecutive_expired_invitations ?? 0));
        $lastTeachingAt = $this->latestTeachingTimestamp($trainerId);
        $baseTier = $this->resolveBaseTier($totalCoursesCompleted, $averageRating);
        $trainerTier = $baseTier;
        $userStatus = $this->resolveUserStatus($trainer, (string) ($trainer->user_status ?? 'active'), $lateUploads, $expiredInvitations, $lastTeachingAt);

        $summary = [
            'total_courses_completed' => $totalCoursesCompleted,
            'average_rating' => $averageRating,
            'late_uploads' => $lateUploads,
            'consecutive_late_uploads' => $lateUploads,
            'consecutive_expired_invitations' => $expiredInvitations,
            'last_teaching_at' => $lastTeachingAt?->toIso8601String(),
            'user_status' => $userStatus,
            'base_tier' => $baseTier,
            'trainer_tier' => $trainerTier,
            'trainer_tier_label' => $this->tierLabel($trainerTier),
            'trainer_tier_rank' => $this->tierRank($trainerTier),
            'available_schemes' => $this->availableContributionSchemesByTier($trainerTier),
        ];

        $this->summaryCache[$trainerId] = $summary;

        if ($persist) {
            $trainer->forceFill([
                'total_courses_completed' => $totalCoursesCompleted,
                'average_rating' => $averageRating,
                'late_uploads' => $lateUploads,
                'consecutive_late_uploads' => $lateUploads,
                'consecutive_expired_invitations' => $expiredInvitations,
                'last_teaching_at' => $lastTeachingAt,
                'user_status' => $userStatus,
                'trainer_tier' => $trainerTier,
            ])->save();
        }

        return $summary;
    }

    /**
     * @return array<string, mixed>
     */
    public function refresh(User $trainer): array
    {
        return $this->summary($trainer, true);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function availableContributionSchemes(User $trainer): array
    {
        return $this->availableContributionSchemesByTier((string) ($trainer->trainer_tier ?? 'associate'));
    }

    public function schemeAllowedForTrainer(User $trainer, string $schemeKey): bool
    {
        return array_key_exists($schemeKey, $this->availableContributionSchemes($trainer));
    }

    /**
     * @return array<string, mixed>
     */
    public function incrementLateUploads(User $trainer, array $context = []): array
    {
        $oldLateUploads = max(0, (int) ($trainer->consecutive_late_uploads ?? $trainer->late_uploads ?? 0));
        $newLateUploads = $oldLateUploads + 1;

        $trainer->consecutive_late_uploads = $newLateUploads;
        $trainer->late_uploads = $newLateUploads;

        if ($newLateUploads >= 3) {
            $trainer->user_status = 'suspended';
        }

        $summary = $this->refresh($trainer);
        $this->notifyLateUploadChange($trainer, $oldLateUploads, $newLateUploads, (string) $summary['trainer_tier'], $context);

        return $summary;
    }

    /**
     * @return array<string, mixed>
     */
    public function decrementLateUploads(User $trainer, array $context = []): array
    {
        return $this->resetLateUploads($trainer, $context);
    }

    /**
     * Pemutihan instan: jika upload tepat waktu pada undangan berikutnya, reset strike jadi 0.
     *
     * @return array<string, mixed>
     */
    public function resetLateUploads(User $trainer, array $context = []): array
    {
        $oldLateUploads = max(0, (int) ($trainer->consecutive_late_uploads ?? $trainer->late_uploads ?? 0));
        if ($oldLateUploads === 0) {
            return $this->summary($trainer, false);
        }

        $trainer->consecutive_late_uploads = 0;
        $trainer->late_uploads = 0;
        $summary = $this->refresh($trainer);

        $this->createNotification(
            $trainer,
            'trainer_strike_reset',
            'Pemutihan Otomatis Berhasil',
            'Anda berhasil upload tepat waktu. Poin keterlambatan berturut-turut telah direset menjadi 0.',
            [
                'entityType' => (string) data_get($context, 'entity_type', 'materi'),
                'entityId' => (int) data_get($context, 'entity_id', 0),
                'entityTitle' => (string) data_get($context, 'entity_title', ''),
                'url' => data_get($context, 'url'),
                'newLateUploads' => 0,
                'currentTier' => (string) ($summary['trainer_tier'] ?? 'associate'),
            ]
        );

        return $summary;
    }

    /**
     * @return array<string, mixed>
     */
    public function recordExpiredInvitation(User $trainer): array
    {
        $trainer->consecutive_expired_invitations = max(0, (int) ($trainer->consecutive_expired_invitations ?? 0)) + 1;

        if ((int) $trainer->consecutive_expired_invitations >= 3 && (string) ($trainer->user_status ?? 'active') !== 'suspended') {
            $trainer->user_status = 'inactive';
        }

        return $this->refresh($trainer);
    }

    public function resetExpiredInvitationStreak(User $trainer): array
    {
        if ((int) ($trainer->consecutive_expired_invitations ?? 0) === 0) {
            return $this->summary($trainer, false);
        }

        $trainer->consecutive_expired_invitations = 0;

        return $this->refresh($trainer);
    }

    public function canReceiveInvitation(User $trainer): bool
    {
        return (string) ($trainer->user_status ?? 'active') === 'active';
    }

    public function tierLabel(string $tier): string
    {
        return $this->tiers()[$tier]['label'] ?? $this->tiers()['associate']['label'];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function availableContributionSchemesByTier(string $tier): array
    {
        $schemes = config('trainer_schemes', []);

        return collect($schemes)
            ->sortBy('order')
            ->all();
    }

    private function resolveBaseTier(int $totalCoursesCompleted, float $averageRating): string
    {
        if ($totalCoursesCompleted >= 35 && $averageRating >= 4.6) {
            return 'expert';
        }

        if ($totalCoursesCompleted >= 10 && $averageRating >= 4.0) {
            return 'professional';
        }

        return 'associate';
    }

    private function calculateAverageRating(int $trainerId): float
    {
        $courseStats = Review::query()
            ->whereHas('course', function ($query) use ($trainerId) {
                $query->where('trainer_id', $trainerId);
            })
            ->selectRaw('COUNT(*) as total_count, COALESCE(AVG(rating), 0) as average_rating')
            ->first();

        $eventStats = Feedback::query()
            ->whereHas('event', function ($query) use ($trainerId) {
                $query->where('trainer_id', $trainerId);
            })
            ->selectRaw('COUNT(*) as total_count, COALESCE(AVG(rating), 0) as average_rating')
            ->first();

        $courseCount = (int) data_get($courseStats, 'total_count', 0);
        $eventCount = (int) data_get($eventStats, 'total_count', 0);
        $totalRatings = $courseCount + $eventCount;

        if ($totalRatings <= 0) {
            return 0.0;
        }

        $courseAverage = (float) data_get($courseStats, 'average_rating', 0);
        $eventAverage = (float) data_get($eventStats, 'average_rating', 0);
        $weightedAverage = (($courseAverage * $courseCount) + ($eventAverage * $eventCount)) / $totalRatings;

        return (float) number_format($weightedAverage, 2, '.', '');
    }

    private function countCompletedActivities(int $trainerId): int
    {
        $completedCourses = Course::query()
            ->where('trainer_id', $trainerId)
            ->whereIn('status', ['approved', 'completed', 'finished', 'archived'])
            ->count();

        $completedEvents = Event::query()
            ->where('trainer_id', $trainerId)
            ->where('material_status', 'approved')
            ->whereNotNull('event_date')
            ->whereRaw("TIMESTAMP(event_date, COALESCE(event_time_end, COALESCE(event_time, '23:59:59'))) < ?", [now()->format('Y-m-d H:i:s')])
            ->count();

        return $completedCourses + $completedEvents;
    }

    private function latestTeachingTimestamp(int $trainerId): ?Carbon
    {
        $courseLastApprovedAt = Course::query()
            ->where('trainer_id', $trainerId)
            ->whereIn('status', ['approved', 'completed', 'finished', 'archived'])
            ->max('approved_at');

        $eventLastDate = Event::query()
            ->where('trainer_id', $trainerId)
            ->where('material_status', 'approved')
            ->max('event_date');

        $lastCourse = $courseLastApprovedAt ? Carbon::parse($courseLastApprovedAt) : null;
        $lastEvent = $eventLastDate ? Carbon::parse($eventLastDate)->endOfDay() : null;

        if ($lastCourse && $lastEvent) {
            return $lastCourse->greaterThan($lastEvent) ? $lastCourse : $lastEvent;
        }

        return $lastCourse ?? $lastEvent;
    }

    private function resolveUserStatus(User $trainer, string $currentStatus, int $lateUploads, int $expiredInvitations, ?Carbon $lastTeachingAt): string
    {
        if ($currentStatus === 'suspended' || $lateUploads >= 3) {
            return 'suspended';
        }

        if ($expiredInvitations >= 3) {
            return 'inactive';
        }

        if ($currentStatus === 'inactive') {
            return 'inactive';
        }

        if ($lastTeachingAt && $lastTeachingAt->lt(now()->subMonths(3))) {
            return 'inactive';
        }

        if (!$lastTeachingAt && $trainer->created_at && $trainer->created_at->lt(now()->subMonths(3))) {
            return 'inactive';
        }

        return 'active';
    }

    private function notifyLateUploadChange(User $trainer, int $oldLateUploads, int $newLateUploads, string $currentTier, array $context = []): void
    {
        if ($newLateUploads <= 0) {
            return;
        }

        $entityType = (string) data_get($context, 'entity_type', 'materi');
        $entityId = (int) data_get($context, 'entity_id', 0);
        $entityTitle = trim((string) data_get($context, 'entity_title', ''));
        $url = data_get($context, 'url');

        if ($oldLateUploads < 1 && $newLateUploads >= 1) {
            $this->createNotification($trainer, 'trainer_late_upload_warning', 'Kartu Kuning (SP1)', 'Anda telat mengunggah materi. Ini adalah peringatan pertama.', compact('entityType', 'entityId', 'entityTitle', 'url', 'newLateUploads', 'currentTier'));
            return;
        }

        if ($oldLateUploads < 2 && $newLateUploads >= 2) {
            $this->createNotification($trainer, 'trainer_hard_warning', 'Peringatan Keras (SP2)', 'Pelanggaran keterlambatan kedua terdeteksi. Satu pelanggaran lagi akun Anda akan dibekukan.', compact('entityType', 'entityId', 'entityTitle', 'url', 'newLateUploads', 'currentTier'));
            return;
        }

        if ($oldLateUploads < 3 && $newLateUploads >= 3) {
            $this->createNotification($trainer, 'trainer_account_suspended', 'Akun Dibekukan', 'Pelanggaran keterlambatan ke-3 terdeteksi. Akun Anda otomatis dibekukan (suspended).', compact('entityType', 'entityId', 'entityTitle', 'url', 'newLateUploads', 'currentTier'));
        }
    }

    private function createNotification(User $trainer, string $type, string $title, string $message, array $context = []): void
    {
        TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => [
                'entity_type' => $context['entityType'] ?? null,
                'entity_id' => $context['entityId'] ?? null,
                'entity_title' => $context['entityTitle'] ?? null,
                'late_uploads' => $context['newLateUploads'] ?? null,
                'trainer_tier' => $context['currentTier'] ?? null,
                'url' => $context['url'] ?? null,
            ],
            'expires_at' => now()->addDays(14),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultSummary(): array
    {
        return [
            'total_courses_completed' => 0,
            'average_rating' => 0.0,
            'late_uploads' => 0,
            'consecutive_late_uploads' => 0,
            'consecutive_expired_invitations' => 0,
            'last_teaching_at' => null,
            'user_status' => 'active',
            'base_tier' => 'associate',
            'trainer_tier' => 'associate',
            'trainer_tier_label' => $this->tierLabel('associate'),
            'trainer_tier_rank' => 1,
            'available_schemes' => $this->availableContributionSchemesByTier('associate'),
        ];
    }

    private function tierRank(string $tier): int
    {
        return (int) data_get($this->tiers(), $tier . '.rank', 1);
    }
}