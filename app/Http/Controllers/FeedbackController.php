<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\EventRegistration;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'rating' => 'required|integer|min:1|max:5',
            'speaker_rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);
        $userId = Auth::id();
        $event = Event::find($request->event_id);

        // Only allow feedback after event has finished
        if (!$event || !$event->isFinished()) {
            return response()->json(['success' => false, 'message' => 'Feedback hanya dibuka setelah event selesai.'], 400);
        }

        // Check registration
        $registration = EventRegistration::where('event_id', $event->id)->where('user_id', $userId)->first();
        if (!$registration || $registration->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Anda harus terdaftar pada event ini untuk memberikan feedback.'], 403);
        }

        // Prevent duplicate feedback (either via registration flag or existing feedback row)
        if (($registration->feedback_submitted_at) || Feedback::where('event_id', $event->id)->where('user_id', $userId)->exists()) {
            return response()->json(['success' => false, 'message' => 'Anda sudah mengirim feedback untuk event ini.'], 409);
        }

        // Create feedback and mark registration
        $feedback = Feedback::create([
            'event_id' => $request->event_id,
            'user_id' => $userId,
            'rating' => $request->rating,
            'speaker_rating' => $request->speaker_rating ?? null,
            'comment' => $request->comment,
        ]);

        try {
            $registration->feedback_submitted_at = Carbon::now();
            // Unlock certificate immediately after feedback
            if (empty($registration->certificate_issued_at)) {
                $registration->certificate_issued_at = Carbon::now();
            }
            $registration->save();
            
            // Add points for feedback
            try {
                $user = Auth::user();
                if ($user) {
                    $pointsService = app(\App\Services\UserPointsService::class);
                    $pointsService->addFeedbackPoints($user);
                }
            } catch (\Throwable $e) { /* ignore */ }
        } catch (\Throwable $e) {
            // non-blocking: we still return success, but log could be added
        }

        return response()->json([
            'success' => true,
            'user_name' => Auth::user()->name,
            'rating' => $feedback->rating,
            'comment' => $feedback->comment,
        ]);
    }
}
