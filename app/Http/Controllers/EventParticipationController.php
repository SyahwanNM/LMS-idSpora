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
        // Create a user notification for successful free registration
        if($createdNewOrActivated){
            try{
                UserNotification::create([
                    'user_id' => $user->id,
                    'type' => 'event_registration',
                    'title' => 'Pendaftaran Dikonfirmasi',
                    'message' => 'Pendaftaran "'.$event->title.'" telah dikonfirmasi.',
                    'data' => [ 'url' => route('events.ticket', $event) ],
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
        // Pastikan event sudah selesai
        $eventDate = $event->event_date ? Carbon::parse($event->event_date) : null;
        $endTime = $event->event_time_end ? Carbon::parse($event->event_date.' '.$event->event_time_end) : ($eventDate ? $eventDate->copy()->endOfDay() : null);
        if(!$endTime || Carbon::now()->lte($endTime)){
            return redirect()->back()->with('error','Event belum selesai, feedback belum dapat dikirim.');
        }
        $data = $request->validate([
            'feedback_text' => 'required|string|min:5',
        ]);
        $registration->feedback_text = $data['feedback_text'];
        $registration->feedback_submitted_at = Carbon::now();
        $registration->save();
        return redirect()->back()->with('success','Terima kasih atas feedback Anda!');
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
        $endTime = $event->event_time_end ? Carbon::parse($event->event_date.' '.$event->event_time_end) : ($eventDate ? $eventDate->copy()->endOfDay() : null);
        if(!$endTime || Carbon::now()->lte($endTime)){
            return redirect()->back()->with('error','Event belum selesai, attendance belum dapat dikirim.');
        }
        $data = $request->validate([
            'attended' => 'required|in:yes,no',
        ]);
        $registration->attendance_status = $data['attended'];
        $registration->attended_at = Carbon::now();
        $registration->save();
        return redirect()->back()->with('success','Attendance berhasil disimpan.');
    }
}
