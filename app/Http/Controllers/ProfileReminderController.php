<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\ProfileReminderService;

class ProfileReminderController extends Controller
{
    protected $reminderService;

    public function __construct(ProfileReminderService $reminderService)
    {
        $this->reminderService = $reminderService;
    }

    /**
     * Cek status reminder untuk user yang sedang login
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function check()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'should_show' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        // Debug: Log user info
        Log::info('Profile Reminder Check', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_role' => $user->role,
            'completion' => $user->getProfileCompletionPercentage(),
            'is_complete' => $user->isProfileComplete(),
        ]);

        $reminderData = $this->reminderService->getReminderData($user);

        if (!$reminderData) {
            Log::info('Reminder data is null - should not show');
            return response()->json([
                'should_show' => false,
                'message' => 'Reminder tidak perlu ditampilkan',
                'debug' => [
                    'completion' => $user->getProfileCompletionPercentage(),
                    'is_complete' => $user->isProfileComplete(),
                ],
            ]);
        }

        // Tandai sebagai sudah ditampilkan
        $this->reminderService->markAsShown($user);

        Log::info('Reminder should be shown', $reminderData);

        return response()->json($reminderData);
    }

    /**
     * Dismiss reminder (user menutup reminder)
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function dismiss()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $this->reminderService->dismissReminder($user);

        return response()->json([
            'success' => true,
            'message' => 'Reminder dismissed',
        ]);
    }
}
