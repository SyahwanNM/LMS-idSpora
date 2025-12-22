<?php

namespace App\Services;

use App\Models\User;
use App\Models\ProfileReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProfileReminderService
{
    /**
     * Cek apakah reminder harus ditampilkan untuk user
     * 
     * Rules:
     * - Profile < 80%
     * - Maksimal 1x per hari
     * - Belum dismiss 2x
     * - Reminder masih aktif
     * 
     * @param User $user
     * @return bool
     */
    public function shouldShowReminder(User $user): bool
    {
        // Skip untuk admin
        if ($user->role === 'admin') {
            Log::info('User is admin, skipping reminder');
            return false;
        }

        // Jika profile sudah lengkap, tidak perlu reminder
        $completion = $user->getProfileCompletionPercentage();
        if ($completion >= 80) {
            Log::info('Profile already complete', ['completion' => $completion]);
            $this->deactivateReminder($user);
            return false;
        }

        $reminder = ProfileReminder::firstOrCreate(
            ['user_id' => $user->id],
            [
                'dismiss_count' => 0,
                'is_active' => true,
                'last_shown_at' => null,
            ]
        );

        // Jika sudah dismiss 2x, nonaktifkan
        if ($reminder->dismiss_count >= 2) {
            Log::info('User dismissed 2x, deactivating reminder', ['dismiss_count' => $reminder->dismiss_count]);
            $reminder->update(['is_active' => false]);
            return false;
        }

        // Jika reminder tidak aktif, tidak tampilkan
        if (!$reminder->is_active) {
            Log::info('Reminder is not active');
            return false;
        }

        // Cek apakah sudah ditampilkan hari ini
        if ($reminder->last_shown_at) {
            $lastShown = Carbon::parse($reminder->last_shown_at);
            $today = Carbon::today();
            
            // Jika sudah ditampilkan hari ini, jangan tampilkan lagi
            if ($lastShown->isSameDay($today)) {
                Log::info('Reminder already shown today', [
                    'last_shown_at' => $reminder->last_shown_at,
                    'today' => $today,
                ]);
                return false;
            }
        }

        Log::info('Reminder should be shown', [
            'user_id' => $user->id,
            'completion' => $completion,
            'dismiss_count' => $reminder->dismiss_count,
            'is_active' => $reminder->is_active,
            'last_shown_at' => $reminder->last_shown_at,
        ]);

        return true;
    }

    /**
     * Tandai reminder sebagai sudah ditampilkan
     * 
     * @param User $user
     * @return void
     */
    public function markAsShown(User $user): void
    {
        $reminder = ProfileReminder::firstOrCreate(
            ['user_id' => $user->id],
            [
                'dismiss_count' => 0,
                'is_active' => true,
            ]
        );

        $reminder->update([
            'last_shown_at' => now(),
        ]);
    }

    /**
     * Dismiss reminder (user menutup reminder)
     * 
     * @param User $user
     * @return void
     */
    public function dismissReminder(User $user): void
    {
        $reminder = ProfileReminder::firstOrCreate(
            ['user_id' => $user->id],
            [
                'dismiss_count' => 0,
                'is_active' => true,
            ]
        );

        $newDismissCount = $reminder->dismiss_count + 1;
        
        $reminder->update([
            'dismiss_count' => $newDismissCount,
            'is_active' => $newDismissCount < 2, // Nonaktifkan jika sudah 2x
        ]);
    }

    /**
     * Nonaktifkan reminder (ketika profile sudah lengkap)
     * 
     * @param User $user
     * @return void
     */
    public function deactivateReminder(User $user): void
    {
        ProfileReminder::updateOrCreate(
            ['user_id' => $user->id],
            [
                'is_active' => false,
            ]
        );
    }

    /**
     * Reset reminder untuk user (untuk testing atau admin)
     * 
     * @param User $user
     * @return void
     */
    public function resetReminder(User $user): void
    {
        ProfileReminder::updateOrCreate(
            ['user_id' => $user->id],
            [
                'dismiss_count' => 0,
                'is_active' => true,
                'last_shown_at' => null,
            ]
        );
    }

    /**
     * Dapatkan data reminder untuk API
     * 
     * @param User $user
     * @return array|null
     */
    public function getReminderData(User $user): ?array
    {
        if (!$this->shouldShowReminder($user)) {
            return null;
        }

        $completion = $user->getProfileCompletionPercentage();
        $missingFields = $user->getMissingProfileFields();
        
        // Tentukan field pertama yang kosong untuk deep-link
        $firstMissingField = !empty($missingFields) ? $missingFields[0] : null;

        return [
            'should_show' => true,
            'completion_percentage' => $completion,
            'missing_fields' => $missingFields,
            'first_missing_field' => $firstMissingField,
            'is_complete' => $user->isProfileComplete(),
        ];
    }
}