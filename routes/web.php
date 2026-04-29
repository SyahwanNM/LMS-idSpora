<?php
// Payment page for course
Route::middleware(['auth'])->get('/courses/{course}/payment', [App\Http\Controllers\Admin\CourseController::class, 'payment'])->name('course.payment');

// Learn course modules (requires purchase/enrollment)
Route::middleware(['auth'])->get('/courses/{course}/learn', [App\Http\Controllers\Admin\CourseController::class, 'learn'])->name('course.learn');

// Mark a video/pdf module as completed (called from JS when video ends)
Route::middleware(['auth'])->post('/courses/{course}/modules/{module}/complete', [App\Http\Controllers\Admin\CourseController::class, 'markModuleComplete'])->name('course.module.complete');

// Mark a video module as watching (called from JS when user clicks play)
Route::middleware(['auth'])->post('/courses/{course}/modules/{module}/watching', [App\Http\Controllers\Admin\CourseController::class, 'markModuleWatching'])->name('course.module.watching');

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Public\PublicEventController;
use App\Http\Controllers\Public\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Enrollment;



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
use App\Http\Controllers\Public\PublicTrainerProfileController;
use App\Http\Controllers\Admin\CourseReportController;
use App\Http\Controllers\Admin\CourseRevenueDetailController;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Http\Controllers\User\ResellerController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainerNotificationsController;
use App\Http\Controllers\Api\PaymentController;

Route::get('/admin/detail-event', function () {
    return view('/admin/detail-event');
});

Route::get('/course-detail/{course}', [CourseController::class, 'show'])->name('course.detail');

// Canonical course detail route (alias used in views)
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/report', [CourseReportController::class, 'index'])->name('report');
    Route::get('/admin/report/revenue', [CourseReportController::class, 'revenue'])->name('admin.report.revenue');
    Route::get('/admin/report/growth', [CourseReportController::class, 'growth'])->name('admin.report.growth');
});

Route::middleware(['auth', 'admin'])->get('/admin/add-users', function () {
    // Pull regular users only (exclude admin and trainer)
    $users = \App\Models\User::with([
        'eventRegistrations' => function ($q) {
            $q->with('event')->orderBy('created_at', 'desc');
        }
    ])
        ->select('id', 'name', 'email', 'phone', 'profession', 'institution', 'avatar', 'created_at', 'bio')
        ->where('role', 'user')
        ->orderBy('name')
        ->get();
    return view('/admin/add-users', compact('users'));
})->name('admin.add-users');


Route::middleware(['auth'])->group(function () {
    Route::get('/reseller', [ResellerController::class, 'index'])->name('reseller.index');
    Route::post('/reseller/withdraw', [ResellerController::class, 'storeWithdraw'])->name('reseller.withdraw');

    // Route Baru untuk Generate Kode
    Route::post('/reseller/activate', [ResellerController::class, 'activate'])->name('reseller.activate');
    Route::get('/reseller/history/download', [ResellerController::class, 'downloadHistory'])->name('reseller.history.download');
    Route::get('/reseller/withdraw/history', [ResellerController::class, 'withdrawHistory'])->name('reseller.withdraw.history');
    Route::get('/reseller/withdraw/download', [\App\Http\Controllers\User\ResellerController::class, 'downloadWithdrawHistory'])->name('reseller.withdraw.download');
    // --- TAMBAHAN ROUTE BUAT CEK KODE REFERRAL AJAX BIAR AUTO GA PERLU REFRESH ---
    Route::post('/reseller/check', [ResellerController::class, 'checkReferral'])->name('check.referral');

    // Referral check (auto discount 10% if valid)
    Route::get('/courses/{course}/check-referral', [\App\Http\Controllers\Admin\CourseManualPaymentController::class, 'checkReferral'])->name('courses.check-referral');
    Route::get('/payment/{event}/check-referral', [\App\Http\Controllers\Admin\ManualPaymentController::class, 'checkReferral'])->name('payment.check-referral');

});



Route::get('/bandingin', function () {
    return view('reseller.bandingin');
});


// Serve Add Event at a friendly URL using the canonical create form (auth+admin)
Route::middleware(['auth', 'admin'])->get('/admin/add-event', [EventController::class, 'create'])->name('admin.add-event');
// History (finished events)
Route::middleware(['auth', 'admin'])->get('/admin/events/history', [EventController::class, 'history'])->name('admin.events.history');

// Detail event (registered) view should receive Event from DB

// PUNYA DINI
Route::get('/modul-course', function () {
    return view('course.modul-course');
})->name('modul-course');

Route::get('/aturan-kuis', function () {
    return view('course.aturan-kuis');
})->name('aturan-kuis');

Route::middleware(['auth'])->get('/payment-course', function () {
    return view('course.payment-course');
})->name('payment-course');

Route::get('/quiz1-course', function () {
    return view('quiz1-course');
})->name('quiz1-course');

Route::get('/quiz-course', function () {
    return view('course.quiz-course');
})->name('quiz-course');

Route::get('/hasil-course', function () {
    return view('hasil-course');
})->name('hasil-course');
Route::get('admin/course-builder', function () {
    return view('admin/course-builder');
})->name('admin/course-builder');
// Legacy Add Course page (standalone view) with categories for the form
Route::get('/admin/add-course', function () {
    $categories = \App\Models\Category::select('id', 'name')->orderBy('name')->get();
    return view('admin/add-course', compact('categories'));
})->name('admin.add-course');
Route::get('/admin/view-modul-course', function () {
    return view('admin/view-modul-course');
})->name('admin/view-modul-course');
Route::get('/admin/add-pdf-module', function () {
    return view('admin/add-pdf-module');
})->name('add-pdf-module');
Route::get('/admin/add-course2', function () {
    return view('admin/add-course2');
})->name('add-course2');
Route::get('/admin/preview-pendapatan', function () {
    return redirect()->route('admin.view-pendapatan', request()->query());
})->middleware(['auth', 'admin'])->name('preview-pendapatan');

Route::get('/rating-course', function () {
    return view('course.rating-course');
})->name('rating-course');

Route::get('/sertifikat-course', function () {
    return view('course.sertifikat-course');
})->name('sertifikat-course');

// Serve storage files (fix 403 error on Windows/PHP built-in server)
// This route serves files from storage when symlink doesn't work properly
Route::get('/storage/{path}', function ($path) {
    // Decode URL-encoded path
    $path = urldecode($path);


    // Security: prevent directory traversal
    if (str_contains($path, "\0")) {
        abort(403, 'Invalid path');
    }

    // Normalize separators then ensure no path traversal segments exist
    $pathNormalized = str_replace('\\', '/', $path);
    $segments = array_values(array_filter(explode('/', $pathNormalized), fn($s) => $s !== ''));
    $segments = array_values(array_filter(explode('/', $pathNormalized), fn($s) => $s !== ''));
    foreach ($segments as $seg) {
        if ($seg === '.' || $seg === '..') {
            abort(403, 'Invalid path');
        }
    }

    // Use normalized path for filesystem lookup
    $path = implode('/', $segments);


    // Get file path in uploads
    $filePath = public_path('uploads/' . $path);


    // Check if file exists
    if (!file_exists($filePath) || !is_file($filePath)) {
        abort(404, 'File not found: ' . $path);
    }


    // Get MIME type
    $mimeType = mime_content_type($filePath);
    if (!$mimeType) {
        // Fallback MIME types based on extension
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
        ];
        $mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';
    }


    return response()->file($filePath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
        'Accept-Ranges' => 'bytes',
    ]);
})->where('path', '.*')->name('storage.serve');

// Landing page: jika sudah login arahkan ke dashboard
Route::get('/auth', function () {
    return view('auth.auth');
});
Route::get('/', function () {
    // During maintenance, non-admin users should stay on landing page.
    try {
        if (\App\Support\AdminSettings::maintenanceEnabled()) {
            if (Auth::check()) {
                $role = strtolower(trim((string) (Auth::user()->role ?? '')));
                if ($role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }
                // user/trainer stays on landing page
                return app(\App\Http\Controllers\Public\LandingPageController::class)->index(request());
            }
            return app(\App\Http\Controllers\Public\LandingPageController::class)->index(request());
        }
    } catch (\Throwable $e) {
        // fallback to normal behavior
    }

    if (Auth::check()) {
        $role = strtolower(trim((string) (Auth::user()->role ?? '')));

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'trainer') {
            return redirect()->route('trainer.dashboard');
        }

        return redirect()->route('dashboard');
    }
    return app(\App\Http\Controllers\Public\LandingPageController::class)->index(request());
})->name('landing-page');

// Public pages

Route::get('/kendala', [PublicPagesController::class, 'support'])->name('public.support');
Route::post('/kendala', [PublicPagesController::class, 'storeSupport'])->name('public.support.store');
Route::middleware('auth')->get('/panduan', [PublicPagesController::class, 'guide'])->name('public.guide');

// Public event pages (accessible without login)
Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [PublicEventController::class, 'show'])->name('events.show');
// Public trainer profile: constrain to numeric ID to avoid colliding with /trainer/dashboard.
Route::get('/trainer/{trainer}', [PublicTrainerProfileController::class, 'show'])
    ->whereNumber('trainer')
    ->name('public.trainer-profile.show');
Route::get('/trainers/{trainer}', [PublicTrainerProfileController::class, 'show'])
    ->whereNumber('trainer')
    ->name('trainers.profile');
// Redirect search to the best-matching event detail (exact title match preferred)
Route::get('/search/events', [PublicEventController::class, 'searchRedirect'])->name('events.searchRedirect');

// Payment page (requires auth) only BEFORE registration; jika sudah terdaftar arahkan balik
Route::middleware('auth')->get('/payment/{event}', function (Event $event) {
    $user = auth()->user();
    $already = $user && $user->eventRegistrations()->where('event_id', $event->id)->exists();
    if ($already) {
        return redirect()->route('events.show', $event)->with('info', 'Anda sudah terdaftar.');
    }
    return view('payment', compact('event'));
})->name('payment');

// Midtrans Snap token endpoint (auth required)
Route::middleware('auth')->get('/payment/{event}/snap-token', [PaymentController::class, 'snapToken'])->name('payment.snap-token');

// Refresh course payment status (used by client after Snap modal success)
Route::post('/payment/refresh-course/{orderId}', [PaymentController::class, 'refreshCoursePayment'])->name('payment.refresh-course');

// Query current pending order for this user+event (auth required)
Route::middleware('auth')->get('/payment/{event}/pending-order', [PaymentController::class, 'pendingOrder'])->name('payment.pending-order');

// Finalize registration after successful payment (auth required)
Route::middleware('auth')->post('/payment/{event}/finalize', [PaymentController::class, 'finalize'])->name('payment.finalize');

// Midtrans notification webhook (no auth)
Route::post('/midtrans/notify', [PaymentController::class, 'notify'])->name('midtrans.notify');

// Optional finish redirect target from Snap callbacks to avoid 404 after payment
// Optional finish redirect target from Snap callbacks to avoid 404 after payment
Route::get('/payment/finish', function () {
    return redirect()->route('dashboard')->with('success', 'Pembayaran sedang diproses.');
})->name('payment.finish');

// Fallback: Generate QRIS via Core API, return qr_string + base64 PNG (auth required)
Route::middleware('auth')->get('/payment/{event}/qris-core', [PaymentController::class, 'qrisCore'])->name('payment.qris-core');

// Event actions (register/feedback/etc.) require authentication
Route::middleware('auth')->group(function () {
    // Feedback AJAX route
    Route::post('/feedback/store', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
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
    Route::get('/courses/{course}/certificate/{enrollment}/download', [\App\Http\Controllers\CRM\CertificateController::class, 'downloadCourse'])->name('course.certificates.download');
    Route::get('/courses/{course}/certificate/{enrollment}/preview', [\App\Http\Controllers\CRM\CertificateController::class, 'previewCourse'])->name('course.certificates.preview');

    // User profile
    Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/history', [\App\Http\Controllers\User\ProfileController::class, 'history'])->name('profile.history');
    Route::get('/profile/events', function() { return redirect()->route('profile.history'); }); // Redirect legacy route
    Route::get('/profile/settings', [\App\Http\Controllers\User\ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile/edit', [\App\Http\Controllers\User\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/account-settings', [\App\Http\Controllers\User\ProfileController::class, 'accountSettings'])->name('profile.account-settings');
    Route::post('/profile/account-settings', [\App\Http\Controllers\User\ProfileController::class, 'updateAccountSettings'])->name('profile.update-account-settings');

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
    Route::post('/courses/{course}/save', [\App\Http\Controllers\Public\PublicCourseController::class, 'toggleSave'])->name('courses.save');

    // Course Rating
    Route::get('/courses/{course}/rating', [\App\Http\Controllers\User\CourseReviewController::class, 'create'])->name('course.rating');
    Route::post('/courses/{course}/rating', [\App\Http\Controllers\User\CourseReviewController::class, 'store'])->name('course.rating.store');
    Route::get('/courses/{course}/rating/success', [\App\Http\Controllers\User\CourseReviewController::class, 'success'])->name('course.rating.success');
});
Route::get('/courses', [\App\Http\Controllers\Public\PublicCourseController::class, 'index'])->name('courses.index');

// Redirect legacy /login path to the actual login page at /sign-in
Route::get('/login', fn() => redirect('/sign-in'));

// Authentication routes (only for guests)
Route::middleware(['guest'])->group(function () {
    Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/sign-in', [AuthController::class, 'login'])->name('login.post');

    Route::get('/sign-up', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/sign-up', [AuthController::class, 'register'])->name('register.post');

    // Resend OTP for registration verification
    Route::post('/register/resend-otp', [AuthController::class, 'resendRegisterOtp'])->name('register.otp.resend');

    // Social auth (Google)
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

    // Login OTP
    Route::get('/auth', [AuthController::class, 'showLoginOtpForm'])->name('login.otp');
    Route::post('/auth', [AuthController::class, 'verifyLoginOtp'])->name('login.otp.verify');
    Route::post('/login/resend-otp', [AuthController::class, 'resendLoginOtp'])->name('login.otp.resend');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
Route::post('/forgot-password', [AuthController::class, 'sendResetCode'])->name('forgot-password.send');
Route::post('/forgot-password/resend', [AuthController::class, 'resendResetCode'])->name('forgot-password.resend');
Route::get('/verifikasi', [AuthController::class, 'showVerification'])->name('verifikasi');
Route::post('/verifikasi', [AuthController::class, 'verifyCode'])->name('verifikasi.verify');
Route::get('/new-password', [AuthController::class, 'showNewPassword'])->name('new-password');
Route::post('/new-password', [AuthController::class, 'resetPassword'])->name('new-password.reset');


// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // User dashboard (only for non-admin users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('profile.complete')->name('dashboard');

    // Admin dashboard (only for admin users)
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/reseller', [\App\Http\Controllers\User\ResellerController::class, 'admin'])->name('admin.reseller');
        // Admin view: Pendapatan (financial breakdown)
        Route::get('/admin/view-pendapatan', [CourseRevenueDetailController::class, 'show'])
            ->name('admin.view-pendapatan');

        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance.index');
        Route::post('/admin/finance/expense', [\App\Http\Controllers\Admin\FinanceController::class, 'storeExpense'])->name('admin.finance.store-expense');
        Route::post('/admin/finance/trainer-payment', [\App\Http\Controllers\Admin\FinanceController::class, 'storeTrainerPayment'])->name('admin.finance.store-trainer-payment');
        Route::get('/admin/finance/events', [\App\Http\Controllers\Admin\FinanceController::class, 'events'])->name('admin.finance.events');
        Route::get('/admin/finance/events/{id}', [\App\Http\Controllers\Admin\FinanceController::class, 'eventDetail'])->name('admin.finance.event-detail');
        Route::get('/admin/finance/courses', [\App\Http\Controllers\Admin\FinanceController::class, 'courses'])->name('admin.finance.courses');
        Route::get('/admin/finance/courses/{id}', [\App\Http\Controllers\Admin\FinanceController::class, 'courseDetail'])->name('admin.finance.course-detail');
        Route::get('/admin/finance/export', [\App\Http\Controllers\Admin\FinanceController::class, 'export'])->name('admin.finance.export');

        Route::get('/invoice/manual/{order_id}', [\App\Http\Controllers\Admin\InvoiceController::class, 'manualInvoice'])->name('invoice.manual');
        Route::get('/admin/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'index'])->name('admin.withdrawals.index');
        Route::post('/admin/withdrawals/{withdrawal}/approve', [\App\Http\Controllers\Admin\WithdrawalController::class, 'approve'])->name('admin.withdrawals.approve');
        Route::post('/admin/withdrawals/{withdrawal}/reject', [\App\Http\Controllers\Admin\WithdrawalController::class, 'reject'])->name('admin.withdrawals.reject');
        // Recent activities AJAX (returns latest login activities)
        Route::get('/admin/recent-activities', [AdminController::class, 'recentActivities'])->name('admin.recent-activities');
        // Note: Removed temporary '/admin/dashboard/create' shortcut; use admin.events.create or admin.events.index directly
        Route::get('/admin/active-users-count', [AdminController::class, 'activeUsersCount'])->name('admin.active-users-count');
        Route::get('/admin/export', [AdminController::class, 'exportData'])->name('admin.export');
        // The resource route below defines admin.events.store handled by EventController@store
        // Removed legacy conflicting route to prevent route name collision
        Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
        Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::post('/admin/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
        Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::post('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

        // User management (Admin accounts & regular users)
        Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
        Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
        Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
        Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
        Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
        Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');

        // Carousel Management
        Route::get('/admin/carousels', [\App\Http\Controllers\Admin\CarouselController::class, 'index'])->name('admin.carousels.index');
        Route::get('/admin/carousels/create', [\App\Http\Controllers\Admin\CarouselController::class, 'create'])->name('admin.carousels.create');
        Route::post('/admin/carousels', [\App\Http\Controllers\Admin\CarouselController::class, 'store'])->name('admin.carousels.store');
        Route::get('/admin/carousels/{carousel}/edit', [\App\Http\Controllers\Admin\CarouselController::class, 'edit'])->name('admin.carousels.edit');
        Route::put('/admin/carousels/{carousel}', [\App\Http\Controllers\Admin\CarouselController::class, 'update'])->name('admin.carousels.update');
        Route::delete('/admin/carousels/{carousel}', [\App\Http\Controllers\Admin\CarouselController::class, 'destroy'])->name('admin.carousels.destroy');
        Route::post('/admin/carousels/{carousel}/toggle-active', [\App\Http\Controllers\Admin\CarouselController::class, 'toggleActive'])->name('admin.carousels.toggle-active');

        // Course management routes
        // Publish course (set status active)
        Route::post('/admin/courses/{course}/publish', [CourseController::class, 'publish'])->name('admin.courses.publish');
        Route::get('/admin/courses', [CourseController::class, 'index'])->name('admin.courses.index');
        Route::get('/admin/courses/create', [CourseController::class, 'create'])->name('admin.courses.create');
        Route::post('/admin/courses', [CourseController::class, 'store'])->name('admin.courses.store');
        Route::get('/admin/courses/{course}', [CourseController::class, 'show'])->name('admin.courses.show');
        Route::get('/admin/courses/{course}/edit', [CourseController::class, 'edit'])->name('admin.courses.edit');
        Route::put('/admin/courses/{course}', [CourseController::class, 'update'])->name('admin.courses.update');
        Route::delete('/admin/courses/{course}', [CourseController::class, 'destroy'])->name('admin.courses.destroy');

        // Module management routes
        Route::get('/admin/courses/{course}/modules', [ModuleController::class, 'index'])->name('admin.courses.modules.index');
        Route::get('/admin/courses/{course}/modules/create', [ModuleController::class, 'create'])->name('admin.courses.modules.create');
        Route::post('/admin/courses/{course}/modules', [ModuleController::class, 'store'])->name('admin.courses.modules.store');
        Route::get('/admin/courses/{course}/modules/{module}', [ModuleController::class, 'show'])->name('admin.courses.modules.show');
        Route::get('/admin/courses/{course}/modules/{module}/edit', [ModuleController::class, 'edit'])->name('admin.courses.modules.edit');
        Route::put('/admin/courses/{course}/modules/{module}', [ModuleController::class, 'update'])->name('admin.courses.modules.update');
        Route::delete('/admin/courses/{course}/modules/{module}', [ModuleController::class, 'destroy'])->name('admin.courses.modules.destroy');
        Route::post('/admin/courses/{course}/modules/reorder', [ModuleController::class, 'reorder'])->name('admin.courses.modules.reorder');

        // Event document uploads (admin)
        Route::post('/admin/events/{event}/documents', [EventController::class, 'uploadDocuments'])->name('admin.events.documents.upload');
        // Event QR actions (admin)
        Route::post('/admin/events/{event}/qr/generate', [EventController::class, 'generateQr'])->name('admin.events.qr.generate');
        Route::get('/admin/events/{event}/qr/download', [EventController::class, 'downloadQr'])->name('admin.events.qr.download');
        // Utility: resolve Google Maps short links to lat/lng
        Route::post('/admin/maps/resolve', [EventController::class, 'resolveMap'])->name('admin.maps.resolve');

        // Quiz management routes
        Route::get('/admin/courses/{course}/modules/{module}/quiz', [QuizController::class, 'index'])->name('admin.courses.modules.quiz.index');
        Route::get('/admin/courses/{course}/modules/{module}/quiz/create', [QuizController::class, 'create'])->name('admin.courses.modules.quiz.create');
        Route::post('/admin/courses/{course}/modules/{module}/quiz', [QuizController::class, 'store'])->name('admin.courses.modules.quiz.store');
        Route::get('/admin/courses/{course}/modules/{module}/quiz/{question}', [QuizController::class, 'show'])->name('admin.courses.modules.quiz.show');
        Route::get('/admin/courses/{course}/modules/{module}/quiz/{question}/edit', [QuizController::class, 'edit'])->name('admin.courses.modules.quiz.edit');
        Route::put('/admin/courses/{course}/modules/{module}/quiz/{question}', [QuizController::class, 'update'])->name('admin.courses.modules.quiz.update');
        Route::delete('/admin/courses/{course}/modules/{module}/quiz/{question}', [QuizController::class, 'destroy'])->name('admin.courses.modules.quiz.destroy');
    });

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



    Route::middleware(['auth', 'admin'])->group(function () {
        // Remove the default create route; use /admin/add-event (named admin.add-event) instead
        Route::resource('admin/events', \App\Http\Controllers\Admin\EventController::class, [
            'except' => ['create'],
            'names' => [
                'index' => 'admin.events.index',
                'store' => 'admin.events.store',
                'show' => 'admin.events.show',
                'edit' => 'admin.events.edit',
                'update' => 'admin.events.update',
                'destroy' => 'admin.events.destroy',
            ]
        ]);

        // Legacy certificate routes (keep for backward compatibility, redirect to CRM)
        Route::get('/admin/certificates', function () {
            return redirect()->route('admin.crm.certificates.index');
        })->name('admin.certificates.index');
        Route::get('/admin/certificates/{event}/edit', function (\App\Models\Event $event) {
            return redirect()->route('admin.crm.certificates.edit', $event);
        })->name('admin.certificates.edit');
        Route::put('/admin/certificates/{event}', function (\App\Models\Event $event) {
            return redirect()->route('admin.crm.certificates.update', $event);
        })->name('admin.certificates.update');
        Route::get('/admin/events/{event}/certificates/generate-massal', function (\App\Models\Event $event) {
            return redirect()->route('admin.crm.certificates.generate-massal', $event);
        })->name('admin.certificates.generate-massal');
    });
});
// Include additional manual-payment routes (manual QRIS proof upload)
require __DIR__ . '/web_manual_payment.php';


Route::middleware(['auth', 'trainer'])->prefix('trainer')->name('trainer.')->group(function () {
    Route::get('/dashboard', [TrainerController::class, 'dashboard'])->name('dashboard');
    Route::post('/availability/toggle', [TrainerController::class, 'toggleAvailability'])->name('availability.toggle');
    Route::get('/courses', [TrainerController::class, 'courses'])->name('courses');
    Route::get('/courses/{id}', [TrainerController::class, 'courseDetail'])->name('detail-course');
    Route::get('/finance', [TrainerController::class, 'finance'])->name('finance');
    Route::get('/profile', [TrainerController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [TrainerController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [TrainerController::class, 'updateProfile'])->name('profile.update');

    Route::get('/events', [TrainerController::class, 'events'])->name('events');
    Route::get('/events/{event}/vbg/download', [TrainerController::class, 'downloadVbg'])->name('events.vbg.download');
    // Upload module khusus event (pending verifikasi admin)
    Route::get('/events/modules', [TrainerEventModuleController::class, 'index'])->name('events.modules');
    Route::get('/api/event-modules', [TrainerEventModuleController::class, 'apiIndex'])->name('api.event-modules');
    Route::post('/events/{event}/module', [TrainerEventModuleController::class, 'upload'])->name('events.module.upload');
    Route::get('/events/{id}', [TrainerController::class, 'eventDetail'])->name('events.show');
    Route::get('/feedback', [TrainerController::class, 'feedback'])->name('feedback');
    Route::post('/feedback/reply/store', [TrainerController::class, 'storeFeedbackReply'])->name('feedback.reply.store');
    Route::get('/notifications', [TrainerNotificationsController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [TrainerNotificationsController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/{notification}/open', [TrainerNotificationsController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/{notification}/respond', [TrainerNotificationsController::class, 'respond'])->name('notifications.respond');

    // --- STUDIO UNTUK COURSE ---
    Route::get('/courses/{id}/studio', [TrainerController::class, 'courseStudio'])->name('courses.studio');
    Route::get('/courses/{courseId}/materials/{moduleId}/view', [TrainerController::class, 'viewCourseMaterial'])->name('courses.studio.material.view');
    Route::post('/courses/{id}/studio/upload', [TrainerController::class, 'uploadCourseMaterials'])->name('courses.studio.upload');
    Route::post('/courses/{id}/studio/quiz', [TrainerController::class, 'saveCourseQuiz'])->name('courses.studio.quiz');

    // --- STUDIO UNTUK EVENT ---
    Route::get('/events/{id}/studio', [TrainerController::class, 'eventStudio'])->name('events.studio');
    Route::post('/events/{id}/studio/upload', [TrainerController::class, 'uploadEventMaterials'])->name('events.studio.upload');
    Route::post('/events/{id}/studio/quiz', [TrainerController::class, 'saveEventQuiz'])->name('events.studio.quiz');
    Route::post('/events/{id}/invitation/accept', [TrainerController::class, 'acceptEventInvitation'])->name('events.invitation.accept');
    Route::post('/events/{id}/invitation/reject', [TrainerController::class, 'rejectEventInvitation'])->name('events.invitation.reject');

    // --- SERTIFIKAT TRAINER (riwayat & download) ---
    Route::get('/certificates', [TrainerController::class, 'certificatesIndex'])->name('certificates.index');
    Route::get('/certificates/events/{event}', [TrainerController::class, 'certificateEventShow'])->name('certificates.events.show');
    Route::get('/certificates/events/{event}/download', [TrainerController::class, 'certificateEventDownload'])->name('certificates.events.download');
    Route::get('/certificates/courses/{course}', [TrainerController::class, 'certificateCourseShow'])->name('certificates.courses.show');
    Route::get('/certificates/courses/{course}/download', [TrainerController::class, 'certificateCourseDownload'])->name('certificates.courses.download');
    // (moved) kirim sertifikat sekarang di area admin
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/trainer', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'index'])->name('admin.trainer.index');
    Route::get('/admin/trainer/create', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'create'])->name('admin.trainer.create');
    Route::post('/admin/trainer', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'store'])->name('admin.trainer.store');
    Route::get('/admin/trainer/{trainer}', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'show'])->name('admin.trainer.show');
    Route::get('/admin/trainer/{trainer}/edit', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'edit'])->name('admin.trainer.edit');
    Route::put('/admin/trainer/{trainer}', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'update'])->name('admin.trainer.update');
    Route::delete('/admin/trainer/{trainer}', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'destroy'])->name('admin.trainer.destroy');
    Route::post('/admin/trainer/{trainer}/certificates', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'issueCertificate'])->name('admin.trainer.certificates.issue');
    // Allow admin to upload/manual-send a certificate file to a trainer
    Route::post('/admin/trainer/{trainer}/certificates/send', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'sendCertificate'])->name('admin.trainer.certificates.send');
    // Show form to upload/send certificate (GET)
    Route::get('/admin/trainer/{trainer}/certificates/send', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'showSendCertificateForm'])->name('admin.trainer.certificates.send.form');
    // Certificates queue for trainers (admin)
    Route::get('/admin/trainer/certificates/queue', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'certificatesQueue'])->name('admin.trainer.certificates.queue');
    Route::post('/admin/trainer/certificates/preview', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'previewCertificate'])->name('admin.trainer.certificates.preview');
    Route::delete('/admin/trainer/certificates/{trainerCertificate}', [\App\Http\Controllers\Admin\TrainerManagementController::class, 'revokeCertificate'])->name('admin.trainer.certificates.revoke');

    // Material Approval Routes
    Route::get('/admin/material/approvals', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'index'])->name('admin.material.approvals');
    Route::get('/admin/material/approved', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'approved'])->name('admin.material.approved');
    Route::get('/admin/material/rejected', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'rejected'])->name('admin.material.rejected');
    Route::get('/admin/material/{material}/modules/{module}/stream', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'streamModule'])->name('admin.material.module.stream');
    Route::post('/admin/material/{material}/modules/{module}/approve', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'approveModule'])->name('admin.material.module.approve');
    Route::post('/admin/material/{material}/modules/{module}/reject', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'rejectModule'])->name('admin.material.module.reject');
    Route::post('/admin/material/{material}/modules/{module}/assign-course', [\App\Http\Controllers\Admin\ModuleProcessingController::class, 'assignCourse'])->name('admin.material.module.assign-course');
    Route::post('/admin/material/{material}/modules/{module}/upload-processed', [\App\Http\Controllers\Admin\ModuleProcessingController::class, 'uploadProcessed'])->name('admin.material.module.upload-processed');
    Route::post('/admin/material/{material}/modules/{module}/accept-processed', [\App\Http\Controllers\Admin\ModuleProcessingController::class, 'acceptProcessed'])->name('admin.material.module.accept-processed');
    Route::post('/admin/material/{material}/modules/{module}/request-revision', [\App\Http\Controllers\Admin\ModuleProcessingController::class, 'requestRevision'])->name('admin.material.module.request-revision');
    Route::get('/admin/material/{material}', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'show'])->name('admin.material.show');
    Route::post('/admin/material/{material}/approve', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'approve'])->name('admin.material.approve');
    Route::post('/admin/material/{material}/reject', [\App\Http\Controllers\Admin\MaterialApprovalController::class, 'reject'])->name('admin.material.reject');

    // Event Material Approval Routes
    Route::get('/admin/event-materials', [\App\Http\Controllers\Admin\EventMaterialApprovalController::class, 'index'])->name('admin.event-materials.index');
    Route::get('/admin/event/{event}/material', [\App\Http\Controllers\Admin\EventMaterialApprovalController::class, 'show'])->name('admin.event-material.show');
    Route::get('/admin/event/{event}/material/stream', [\App\Http\Controllers\Admin\EventMaterialApprovalController::class, 'stream'])->name('admin.event-material.stream');
    Route::post('/admin/event/{event}/material/approve', [\App\Http\Controllers\Admin\EventMaterialApprovalController::class, 'approve'])->name('admin.event-material.approve');
    Route::post('/admin/event/{event}/material/reject', [\App\Http\Controllers\Admin\EventMaterialApprovalController::class, 'reject'])->name('admin.event-material.reject');
});
