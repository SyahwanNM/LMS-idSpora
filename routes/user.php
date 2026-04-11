<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Public\PublicEventController;
use App\Http\Controllers\Public\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\User\UserModuleController;
use App\Http\Controllers\Admin\QuizController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Public\SocialAuthController;
use App\Http\Controllers\TrainerApiController;
use App\Http\Controllers\Trainer\EventModuleController as TrainerEventModuleController;

use App\Http\Controllers\User\NotificationsController;
use App\Http\Controllers\Public\PublicPagesController;
use App\Http\Controllers\Admin\CourseReportController;
use App\Http\Controllers\Admin\CourseRevenueDetailController;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Http\Controllers\User\ResellerController;

Route::middleware(['auth'])->group(function () {
    Route::get('/reseller', [ResellerController::class, 'index'])->name('reseller.index');
    Route::post('/reseller/withdraw', [ResellerController::class, 'storeWithdraw'])->name('reseller.withdraw');

    // Route Baru untuk Generate Kode
    Route::post('/reseller/activate', [ResellerController::class, 'activate'])->name('reseller.activate');
});
Route::middleware('auth')->get('/detail-event-registered/{event}', function (Event $event) {
    // Load feedbacks for display on the event detail page
    $feedbacks = \App\Models\Feedback::with('user')->where('event_id', $event->id)->orderBy('created_at', 'desc')->get();
    return view('user.detail-event-registered', compact('event', 'feedbacks'));
})->name('events.registered.detail');
// Payment page (requires auth) only BEFORE registration; jika sudah terdaftar arahkan balik
Route::middleware('auth')->get('/payment/{event}', function (Event $event) {
    $user = auth()->user();
    $registration = $user ? $user->eventRegistrations()->where('event_id', $event->id)->latest('id')->first() : null;
    if ($registration && $registration->status === 'active') {
        return redirect()->route('events.show', $event)->with('info', 'Anda sudah terdaftar.');
    }
    return view('user.payment', compact('event'));
})->name('payment');
// Download event module (materi) — only available after event completion for active registrants
Route::middleware('auth')->get('/events/{event}/modules/download', function (Event $event) {
    $user = auth()->user();
    $registration = $user ? $user->eventRegistrations()->where('event_id', $event->id)->latest('id')->first() : null;
    if (!$registration || $registration->status !== 'active') {
        abort(403);
    }

    if (!$event->isFinished()) {
        return redirect()->route('events.registered.detail', $event)->with('warning', 'Module materi tersedia setelah acara selesai.');
    }

    $path = (string) ($event->module_path ?? '');
    if ($path === '' || !\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
        return redirect()->route('events.registered.detail', $event)->with('warning', 'Module materi belum tersedia.');
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $downloadName = 'materi-event-' . $event->id . ($ext ? ('.' . $ext) : '');
    $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
    return response()->download($fullPath, $downloadName);
})->name('events.modules.download');



// Event routes now require authentication to view & register
Route::middleware('auth')->group(function () {
    // Feedback AJAX route
    Route::post('/feedback/store', [\App\Http\Controllers\User\FeedbackController::class, 'store'])->name('feedback.store');
    Route::post('/events/{event}/register', [App\Http\Controllers\Admin\EventController::class, 'register'])->name('events.register');
    // Form-based (non-AJAX) free registration & feedback submission
    Route::post('/events/{event}/register/form', [\App\Http\Controllers\User\EventParticipationController::class, 'register'])->name('events.register.form');
    Route::post('/events/{event}/feedback', [\App\Http\Controllers\User\EventParticipationController::class, 'submitFeedback'])->name('events.feedback');
    // Dedicated scan page for event QR (auth, require registration)
    Route::get('/events/{event}/scan', function (\Illuminate\Http\Request $request, \App\Models\Event $event) {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }
        $registration = $user->eventRegistrations()->where('event_id', $event->id)->first();
        if (!$registration || $registration->status !== 'active') {
            return redirect()->route('events.show', $event)->with('warning', 'Anda harus terdaftar untuk melakukan scan.');
        }
        // Compute event start/end for gating
        $eventDate = $event->event_date ? ($event->event_date instanceof \Carbon\Carbon ? $event->event_date : \Carbon\Carbon::parse($event->event_date)) : null;
        $startTime = null;
        $endTime = null;
        try {
            $startTime = $event->event_time ? \Carbon\Carbon::parse($event->event_time) : null;
        } catch (\Throwable $e) {
        }
        try {
            $endTime = $event->event_time_end ? \Carbon\Carbon::parse($event->event_time_end) : null;
        } catch (\Throwable $e) {
        }
        if (!$startTime && $eventDate)
            $startTime = $eventDate->copy()->startOfDay();
        if (!$endTime && $eventDate)
            $endTime = $eventDate->copy()->endOfDay();
        $now = \Carbon\Carbon::now(config('app.timezone'));
        $eventStarted = $eventDate ? $now->gte($startTime ?: $eventDate->copy()->startOfDay()) : true;
        $eventFinished = $eventDate ? $now->gt($endTime ?: $eventDate->copy()->endOfDay()) : false;
        return view('events.scan', compact('event', 'registration', 'eventDate', 'startTime', 'endTime', 'eventStarted', 'eventFinished'));
    })->name('events.scan');
    // Attendance via scan: persist attendance when QR is decoded
    Route::post('/events/{event}/attendance/scan', [\App\Http\Controllers\User\EventParticipationController::class, 'scanAttendance'])->name('events.attendance.scan');
    // Ticket page removed; use event detail instead
    // Notifications
    Route::get('/notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationsController::class, 'markAllRead'])->name('notifications.markAllRead');
    // Certificate (event) - show & download (H+4 logic inside controller)
    Route::get('/events/{event}/certificate/{registration}', [\App\Http\Controllers\CRM\CertificateController::class, 'show'])->name('certificates.show');
    Route::get('/events/{event}/certificate/{registration}/download', [\App\Http\Controllers\CRM\CertificateController::class, 'download'])->name('certificates.download');

    // User profile
    Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/history', [\App\Http\Controllers\User\ProfileController::class, 'history'])->name('profile.history');
    Route::get('/profile/settings', [\App\Http\Controllers\User\ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile/edit', [\App\Http\Controllers\User\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/account-settings', [\App\Http\Controllers\User\ProfileController::class, 'accountSettings'])->name('profile.account-settings');
    Route::post('/profile/account-settings', [\App\Http\Controllers\User\ProfileController::class, 'updateAccountSettings'])->name('profile.update-account-settings');

    // Profile Reminder API
    Route::get('/api/profile-reminder/check', [\App\Http\Controllers\User\ProfileReminderController::class, 'check'])->name('profile.reminder.check');
    Route::post('/api/profile-reminder/dismiss', [\App\Http\Controllers\User\ProfileReminderController::class, 'dismiss'])->name('profile.reminder.dismiss');

    // Profile Reminder API
    Route::get('/api/profile-reminder/check', [\App\Http\Controllers\User\ProfileReminderController::class, 'check'])->name('profile.reminder.check');
    Route::post('/api/profile-reminder/dismiss', [\App\Http\Controllers\User\ProfileReminderController::class, 'dismiss'])->name('profile.reminder.dismiss');

    // Save/unsave event
    Route::post('/events/{event}/save', function (\Illuminate\Http\Request $request, \App\Models\Event $event) {
        $user = $request->user();
        if (!$user) {
            // For non-AJAX, redirect to login; for AJAX, return JSON 401
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        $exists = \DB::table('user_saved_events')
            ->where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->exists();

        $saved = true;
        if ($exists) {
            \DB::table('user_saved_events')
                ->where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->delete();
            $saved = false;
        } else {
            \DB::table('user_saved_events')->insert([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $saved = true;
        }

        // If the request expects JSON (AJAX/fetch), return JSON; otherwise redirect back to detail page
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'saved' => $saved]);
        }
        return redirect()->route('events.registered.detail', $event)
            ->with('success', $saved ? 'Event disimpan.' : 'Event dihapus dari tersimpan.');
    })->name('events.save');

    // Save/unsave course
    Route::post('/courses/{course}/save', function (\Illuminate\Http\Request $request, \App\Models\Course $course) {
        $user = $request->user();
        if (!$user) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        $exists = \DB::table('user_saved_courses')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->exists();

        $saved = true;
        if ($exists) {
            \DB::table('user_saved_courses')
                ->where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->delete();
            $saved = false;
        } else {
            \DB::table('user_saved_courses')->insert([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $saved = true;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'saved' => $saved]);
        }
        return back()->with('success', $saved ? 'Course disimpan.' : 'Course dihapus dari tersimpan.');
    })->name('courses.save');
});
// User dashboard (only for non-admin users)
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('profile.complete')->name('dashboard');
// User module access routes
Route::get('/courses/{course}/modules', [UserModuleController::class, 'index'])->name('user.modules.index');
Route::get('/courses/{course}/modules/{module}', [UserModuleController::class, 'show'])->name('user.modules.show');
Route::get('/courses/{course}/modules/{module}/download', [UserModuleController::class, 'download'])->name('user.modules.download');
Route::get('/courses/{course}/modules/{module}/stream', [UserModuleController::class, 'stream'])->name('user.modules.stream');

// Learning time (realtime) endpoints for authenticated users
Route::middleware(['auth', 'throttle:120,1'])->group(function () {
    Route::post('/learning-time/heartbeat', [\App\Http\Controllers\User\LearningTimeController::class, 'heartbeat'])->name('learning-time.heartbeat');
    Route::get('/learning-time/chart', [\App\Http\Controllers\User\LearningTimeController::class, 'chart'])->name('learning-time.chart');
});

// User quiz routes
Route::get('/courses/{course}/modules/{module}/quiz/start', [QuizController::class, 'start'])->name('user.quiz.start');
Route::get('/courses/{course}/modules/{module}/quiz/{attempt}', [QuizController::class, 'take'])->name('user.quiz.take');
Route::post('/courses/{course}/modules/{module}/quiz/{attempt}/answer', [QuizController::class, 'submitAnswer'])->name('user.quiz.answer');
Route::post('/courses/{course}/modules/{module}/quiz/{attempt}/finish', [QuizController::class, 'finish'])->name('user.quiz.finish');
Route::get('/quiz/{attempt}/result', [QuizController::class, 'resultShort'])->name('user.quiz.result.short');
Route::get('/courses/{course}/modules/{module}/quiz/{attempt}/result', [QuizController::class, 'result'])->name('user.quiz.result');

Route::get('/course-quiz-result', function () {
    return view('course.quiz.result');
})->name('course.quiz.result');

Route::get('/course-quiz', function () {
    return view('course.quiz.intro');
})->name('course.quiz.intro');

Route::get('/course-quiz-start', function () {
    return view('course.quiz.start');
})->name('course.quiz.start');


