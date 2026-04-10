<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Store feedback submission (AJAX endpoint)
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'rating' => 'required|integer|min:1|max:5',
            'speaker_rating' => 'nullable|integer|min:1|max:5',
            'committee_rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user is registered for this event
        $event = Event::findOrFail($request->event_id);
        $registration = $event->registrations()
            ->where('user_id', Auth::id())
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'Not registered for this event'
            ], 403);
        }

        // Check if feedback already exists
        $existing = Feedback::where('event_id', $request->event_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            // Update existing feedback
            $existing->update([
                'rating' => $request->rating,
                'speaker_rating' => $request->speaker_rating,
                'committee_rating' => $request->committee_rating,
                'comment' => $request->comment,
            ]);
            $feedback = $existing;
        } else {
            // Create new feedback
            $feedback = Feedback::create([
                'event_id' => $request->event_id,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'speaker_rating' => $request->speaker_rating,
                'committee_rating' => $request->committee_rating,
                'comment' => $request->comment,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Feedback saved successfully',
            'feedback_id' => $feedback->id
        ]);
    }
}
