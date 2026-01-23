<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;

class UserPointsService
{
    // Poin untuk berbagai aktivitas
    const POINTS_FREE_EVENT = 10;
    const POINTS_PAID_EVENT = 30;
    const POINTS_FEEDBACK = 5;
    const POINTS_STREAK = 5;

    // Badge thresholds
    const BADGE_BEGINNER = 0;
    const BADGE_EXPLORER = 100;
    const BADGE_LEARNER = 250;
    const BADGE_EXPERT = 500;
    const BADGE_MASTER = 1000;

    /**
     * Menghitung badge berdasarkan poin
     */
    public function calculateBadge(int $points): string
    {
        if ($points >= self::BADGE_MASTER) {
            return 'master';
        } elseif ($points >= self::BADGE_EXPERT) {
            return 'expert';
        } elseif ($points >= self::BADGE_LEARNER) {
            return 'learner';
        } elseif ($points >= self::BADGE_EXPLORER) {
            return 'explorer';
        }
        return 'beginner';
    }

    /**
     * Mendapatkan informasi badge
     */
    public function getBadgeInfo(string $badge): array
    {
        $badges = [
            'beginner' => [
                'name' => 'Beginner',
                'min_points' => 0,
                'max_points' => 99,
                'color' => '#94a3b8',
                'gradient' => 'linear-gradient(135deg, #94a3b8 0%, #64748b 100%)',
                'icon' => 'bi-star',
            ],
            'explorer' => [
                'name' => 'Explorer',
                'min_points' => 100,
                'max_points' => 249,
                'color' => '#3b82f6',
                'gradient' => 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)',
                'icon' => 'bi-compass',
            ],
            'learner' => [
                'name' => 'Learner',
                'min_points' => 250,
                'max_points' => 499,
                'color' => '#8b5cf6',
                'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
                'icon' => 'bi-book',
            ],
            'expert' => [
                'name' => 'Expert',
                'min_points' => 500,
                'max_points' => 999,
                'color' => '#f59e0b',
                'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                'icon' => 'bi-trophy',
            ],
            'master' => [
                'name' => 'Master',
                'min_points' => 1000,
                'max_points' => 9999,
                'color' => '#dc2626',
                'gradient' => 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)',
                'icon' => 'bi-gem',
            ],
        ];

        return $badges[$badge] ?? $badges['beginner'];
    }

    /**
     * Menambahkan poin untuk event registration
     */
    public function addEventPoints(User $user, Event $event, EventRegistration $registration): void
    {
        $pointsToAdd = 0;
        $isPaid = $event->price > 0;
        
        // Poin berdasarkan jenis event - SELALU diberikan saat registrasi
        if ($isPaid) {
            $pointsToAdd += self::POINTS_PAID_EVENT;
        } else {
            $pointsToAdd += self::POINTS_FREE_EVENT;
        }

        // Cek streak (event berturut-turut) - hanya jika event sudah terjadi
        $streakBonus = false;
        if ($event->event_date) {
            $eventDate = Carbon::parse($event->event_date);
            // Hanya beri streak bonus jika event sudah terjadi (tidak di masa depan)
            if ($eventDate->isPast() || $eventDate->isToday()) {
                $streakBonus = $this->checkStreak($user, $event->event_date);
                if ($streakBonus) {
                    $pointsToAdd += self::POINTS_STREAK;
                }
            }
        }

        // Log untuk debugging
        \Log::info('Adding event points', [
            'user_id' => $user->id,
            'event_id' => $event->id,
            'is_paid' => $isPaid,
            'points_to_add' => $pointsToAdd,
            'streak_bonus' => $streakBonus,
            'current_points' => $user->points ?? 0,
        ]);

        // Update poin user
        $this->addPoints($user, $pointsToAdd, false);
    }

    /**
     * Menambahkan poin untuk feedback
     */
    public function addFeedbackPoints(User $user): void
    {
        // Log untuk debugging
        \Log::info('Adding feedback points', [
            'user_id' => $user->id,
            'points_to_add' => self::POINTS_FEEDBACK,
            'current_points' => $user->points ?? 0,
        ]);
        
        $this->addPoints($user, self::POINTS_FEEDBACK, false);
    }

    /**
     * Menambahkan poin ke user
     */
    private function addPoints(User $user, int $points, bool $isStreak = false): void
    {
        $oldPoints = $user->points ?? 0;
        $user->points = $oldPoints + $points;
        $oldBadge = $user->badge ?? 'beginner';
        $user->badge = $this->calculateBadge($user->points);
        $user->save();
        
        // Log untuk debugging
        \Log::info('Points updated', [
            'user_id' => $user->id,
            'points_added' => $points,
            'old_points' => $oldPoints,
            'new_points' => $user->points,
            'old_badge' => $oldBadge,
            'new_badge' => $user->badge,
        ]);
    }

    /**
     * Cek apakah event ini adalah streak (berturut-turut)
     */
    private function checkStreak(User $user, $eventDate): bool
    {
        if (!$eventDate) {
            return false;
        }

        $eventDate = Carbon::parse($eventDate);
        $lastEventDate = $user->last_event_date ? Carbon::parse($user->last_event_date) : null;

        // Update last event date
        $user->last_event_date = $eventDate->format('Y-m-d');
        $user->save();

        // Cek apakah event ini dalam 7 hari setelah event terakhir
        if ($lastEventDate) {
            $daysDiff = $lastEventDate->diffInDays($eventDate);
            return $daysDiff <= 7 && $daysDiff > 0;
        }

        return false;
    }

    /**
     * Recalculate semua poin user (untuk migration atau update)
     */
    public function recalculateUserPoints(User $user): void
    {
        $totalPoints = 0;

        // Hitung poin dari event registrations
        $registrations = EventRegistration::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'asc')
            ->get();

        $lastEventDate = null;
        foreach ($registrations as $registration) {
            if ($registration->event) {
                $isPaid = $registration->event->price > 0;
                $totalPoints += $isPaid ? self::POINTS_PAID_EVENT : self::POINTS_FREE_EVENT;

                // Cek streak - hanya untuk event yang sudah terjadi
                if ($lastEventDate && $registration->event->event_date) {
                    $eventDate = Carbon::parse($registration->event->event_date);
                    $lastDate = Carbon::parse($lastEventDate);
                    $daysDiff = $lastDate->diffInDays($eventDate);
                    // Streak jika event terjadi dalam 7 hari setelah event sebelumnya
                    if ($daysDiff <= 7 && $daysDiff > 0) {
                        $totalPoints += self::POINTS_STREAK;
                    }
                }

                // Update last event date untuk streak calculation
                if ($registration->event->event_date) {
                    $eventDate = Carbon::parse($registration->event->event_date);
                    if (!$lastEventDate || $eventDate->gt(Carbon::parse($lastEventDate))) {
                        $lastEventDate = $registration->event->event_date;
                    }
                }
            }
        }

        // Hitung poin dari feedback
        $feedbackCount = \App\Models\Feedback::where('user_id', $user->id)->count();
        $totalPoints += $feedbackCount * self::POINTS_FEEDBACK;

        // Update user
        $user->points = $totalPoints;
        $user->badge = $this->calculateBadge($totalPoints);
        if ($lastEventDate) {
            $user->last_event_date = Carbon::parse($lastEventDate)->format('Y-m-d');
        }
        $user->save();
    }
}

