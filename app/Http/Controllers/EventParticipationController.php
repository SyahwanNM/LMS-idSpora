<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\UserNotification;

class EventParticipationController extends Controller
{
    public function register(Event $event, Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return redirect()->back()->with('error','Login dahulu untuk mendaftar.');
        }
        // Cek apakah sudah terdaftar
        $registration = EventRegistration::where('event_id',$event->id)->where('user_id',$user->id)->first();
        if($registration && $registration->status === 'active'){
            return redirect()->back()->with('info','Anda sudah terdaftar.');
        }
        $createdNewOrActivated = false;
        DB::transaction(function() use ($event, $user, &$registration, &$createdNewOrActivated){
            if(!$registration){
                $registration = new EventRegistration();
                $registration->event_id = $event->id;
                $registration->user_id = $user->id;
                $createdNewOrActivated = true;
            }
            if($registration->status !== 'active'){
                $createdNewOrActivated = true;
            }
            $registration->status = 'active';
            if(empty($registration->registration_code)){
                $registration->registration_code = 'EVT'.$event->id.'-'.Str::upper(Str::random(6));
            }
            $registration->save();
        });
        
        // Add points for event registration (only if newly created or activated)
        if($createdNewOrActivated){
            try {
                $pointsService = app(\App\Services\UserPointsService::class);
                $pointsService->addEventPoints($user, $event, $registration);
            } catch (\Throwable $e) {
                // Log error but don't block registration
                \Log::warning('Failed to add points for event registration', [
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Create a user notification for successful free registration
        if($createdNewOrActivated){
            try{
                UserNotification::create([
                    'user_id' => $user->id,
                    'type' => 'event_registration',
                    'title' => 'Pendaftaran Dikonfirmasi',
                    'message' => 'Pendaftaran untuk "'.$event->title.'" telah dikonfirmasi.',
                    // Redirect back to detail event instead of ticket page
                    'data' => [ 'url' => route('events.registered.detail', $event) ],
                    'expires_at' => now()->addDays(14),
                ]);
            } catch(\Throwable $e) { /* ignore notification errors */ }
        }
        return redirect()->back()->with('success','Anda berhasil terdaftar!');
    }

    public function submitFeedback(Event $event, Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return redirect()->back()->with('error','Login diperlukan.');
        }
        $registration = EventRegistration::where('event_id',$event->id)->where('user_id',$user->id)->first();
        if(!$registration || $registration->status !== 'active'){
            return redirect()->back()->with('error','Anda belum terdaftar.');
        }
        // Pastikan event sudah selesai (robust parsing for time fields)
        $eventDate = $event->event_date ? Carbon::parse($event->event_date) : null;
        $parseEventTime = function($date, $raw){
            if(empty($raw)) return null;
            if($raw instanceof Carbon) return $raw;
            $s = trim((string)$raw);
            // Strip trailing timezone labels
            $s = preg_replace('/\s*(WIB|WITA|WIT)\s*$/i','',$s);
            // Normalize 14.30 -> 14:30
            if(preg_match('/^\d{1,2}\.\d{2}$/',$s)) $s = str_replace('.',':',$s);
            // If already contains a date, parse directly
            if(preg_match('/\d{4}-\d{2}-\d{2}/',$s)){
                try { return Carbon::parse($s); } catch(\Throwable $e){ return null; }
            }
            // Combine with provided date
            if($date){
                $dateStr = $date instanceof Carbon ? $date->format('Y-m-d') : (string)$date;
                try { return Carbon::parse($dateStr.' '.$s); } catch(\Throwable $e){ return null; }
            }
            try { return Carbon::parse($s); } catch(\Throwable $e){ return null; }
        };
        $endTime = $parseEventTime($eventDate, $event->event_time_end);
        if(!$endTime && $eventDate){ $endTime = $eventDate->copy()->endOfDay(); }
        if(!$endTime || Carbon::now()->lte($endTime)){
            return redirect()->back()->with('error','Event belum selesai, feedback belum dapat dikirim.');
        }
        $data = $request->validate([
            'feedback_text' => 'required|string|min:5',
        ]);
        $registration->feedback_text = $data['feedback_text'];
        $registration->feedback_submitted_at = Carbon::now();
        // Issue certificate immediately after feedback submitted
        if (empty($registration->certificate_issued_at)) {
            $registration->certificate_issued_at = Carbon::now();
        }
        $registration->save();
        
        // Add points for feedback
        try {
            $pointsService = app(\App\Services\UserPointsService::class);
            $pointsService->addFeedbackPoints($user);
        } catch (\Throwable $e) {
            \Log::warning('Failed to add points for feedback', [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);
        }
        
        return redirect()->back()->with('success','Terima kasih atas feedback Anda! Sertifikat telah terbuka.');
    }

    public function submitAttendance(Event $event, Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return redirect()->back()->with('error','Login diperlukan.');
        }
        $registration = EventRegistration::where('event_id',$event->id)->where('user_id',$user->id)->first();
        if(!$registration || $registration->status !== 'active'){
            return redirect()->back()->with('error','Anda belum terdaftar.');
        }
        // Pastikan event sudah selesai
        $eventDate = $event->event_date ? Carbon::parse($event->event_date) : null;
        $parseEventTime = function($date, $raw){
            if(empty($raw)) return null;
            if($raw instanceof Carbon) return $raw;
            $s = trim((string)$raw);
            $s = preg_replace('/\s*(WIB|WITA|WIT)\s*$/i','',$s);
            if(preg_match('/^\d{1,2}\.\d{2}$/',$s)) $s = str_replace('.',':',$s);
            if(preg_match('/\d{4}-\d{2}-\d{2}/',$s)){
                try { return Carbon::parse($s); } catch(\Throwable $e){ return null; }
            }
            if($date){
                $dateStr = $date instanceof Carbon ? $date->format('Y-m-d') : (string)$date;
                try { return Carbon::parse($dateStr.' '.$s); } catch(\Throwable $e){ return null; }
            }
            try { return Carbon::parse($s); } catch(\Throwable $e){ return null; }
        };
        $endTime = $parseEventTime($eventDate, $event->event_time_end);
        if(!$endTime && $eventDate){ $endTime = $eventDate->copy()->endOfDay(); }
        if(!$endTime || Carbon::now()->lte($endTime)){
            return redirect()->back()->with('error','Event belum selesai, attendance belum dapat dikirim.');
        }
        // Fallback: attended yes/no for non-code flows
        $data = $request->validate([
            'attended' => 'required|in:yes,no',
        ]);
        $registration->attendance_status = $data['attended'];
        $registration->attended_at = Carbon::now();
        $registration->attendance_scan_qr = Carbon::now();
        $registration->save();
        return redirect()->back()->with('success','Attendance berhasil disimpan.');
    }

    /**
     * Persist attendance upon scanning the event QR.
     * Accepts the full decoded text (URL with ?t=token or raw token).
     * Marks `attendance_status = yes` and sets `attended_at` for the active registration.
     */
    public function scanAttendance(Event $event, Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response()->json(['message' => 'Login diperlukan.'], 401);
        }
        $registration = EventRegistration::where('event_id',$event->id)->where('user_id',$user->id)->first();
        if(!$registration || $registration->status !== 'active'){
            return response()->json(['message' => 'Anda belum terdaftar aktif.'], 403);
        }
        // Gate: event must have started (not necessarily finished)
        $startAt = $event->start_at; $endAt = $event->end_at;
        $now = Carbon::now();
        if($startAt && $now->lt($startAt)){
            return response()->json(['message' => 'Event belum dimulai.'], 422);
        }

        $data = $request->validate([
            'qr_text' => 'required|string|max:2048',
        ]);

        // Extract token from text: support full URL or raw token
        $token = null; $text = trim($data['qr_text']);
        if (preg_match('/[?&]t=([0-9a-f]{16,})/i', $text, $m)) {
            $token = $m[1];
        } else if (preg_match('/^[0-9a-f]{16,}$/i', $text)) {
            $token = $text;
        }
        $tokenOk = false;
        if (!empty($event->attendance_qr_token) && $token) {
            $tokenOk = hash_equals((string)$event->attendance_qr_token, (string)$token);
        }
        // Fallback: accept legacy QR that encodes the event URL without token
        $eventUrlPattern = sprintf('/\/events\/%d(\?|$)/', (int) $event->id);
        $urlOk = (bool) preg_match($eventUrlPattern, $text);
        if (!$tokenOk && !$urlOk) {
            \Log::info('Attendance scan rejected', [
                'event_id' => $event->id,
                'user_id' => $user->id,
                'has_event_token' => !empty($event->attendance_qr_token),
                'extracted_token' => $token,
                'text' => $text,
            ]);
            return response()->json(['message' => 'QR tidak valid untuk event ini. Gunakan QR terbaru dari panitia.'], 422);
        }
        if (!empty($registration->attendance_scan_qr) || !empty($registration->attended_at)) {
            return response()->json(['message' => 'Attendance sudah tercatat.'], 200);
        }

        $registration->attendance_status = 'yes';
        $registration->attended_at = Carbon::now();
        $registration->attendance_scan_qr = Carbon::now();
        $registration->save();

        return response()->json(['message' => 'Attendance berhasil disimpan.'], 200);
    }
}
