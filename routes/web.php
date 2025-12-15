<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserModuleController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationsController;
use App\Models\Event;
use App\Models\EventRegistration;

Route::get('/admin/detail-event', function () {
    return view('/admin/detail-event');
});

Route::get('/admin/report', function () {
    // Legacy path -> redirect to controller-powered reports so the view gets data
    return redirect()->route('admin.reports');
});

Route::get('/admin/add-users', function () {
    return view('/admin/add-users');
});

Route::get('/reseller', function () {
    return view('reseller.index');
});


// Serve Add Event at a friendly URL using the canonical create form (auth+admin)
Route::middleware(['auth','admin'])->get('/admin/add-event', [EventController::class, 'create'])->name('admin.add-event');
// History (finished events)
Route::middleware(['auth','admin'])->get('/admin/events/history', [EventController::class, 'history'])->name('admin.events.history');

// Detail event (registered) view should receive Event from DB
Route::middleware('auth')->get('/detail-event-registered/{event}', function (Event $event) {
    // Load feedbacks for display on the event detail page
    $feedbacks = \App\Models\Feedback::with('user')->where('event_id', $event->id)->orderBy('created_at', 'desc')->get();
    return view('detail-event-registered', compact('event', 'feedbacks'));
})->name('events.registered.detail');

// Landing page: jika sudah login arahkan ke dashboard
Route::get('/auth', function () {
    return view('/auth');
});
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return app(\App\Http\Controllers\LandingPageController::class)->index(request());
})->name('landing-page');

// Payment page (requires auth) only BEFORE registration; jika sudah terdaftar arahkan balik
Route::middleware('auth')->get('/payment/{event}', function(Event $event) {
    $user = auth()->user();
    $already = $user && $user->eventRegistrations()->where('event_id',$event->id)->exists();
    if($already){
        return redirect()->route('events.show',$event)->with('info','Anda sudah terdaftar.');
    }
    return view('payment', compact('event'));
})->name('payment');

// Midtrans Snap token endpoint (auth required)
Route::middleware('auth')->get('/payment/{event}/snap-token', [PaymentController::class, 'snapToken'])->name('payment.snap-token');

// Finalize registration after successful payment (auth required)
Route::middleware('auth')->post('/payment/{event}/finalize', [PaymentController::class, 'finalize'])->name('payment.finalize');

// Midtrans notification webhook (no auth)
Route::post('/midtrans/notify', [PaymentController::class, 'notify'])->name('midtrans.notify');

// Event routes now require authentication to view & register
Route::middleware('auth')->group(function(){
    // Feedback AJAX route
    Route::post('/feedback/store', [\App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [PublicEventController::class, 'show'])->name('events.show');
        // Redirect search to the best-matching event detail (exact title match preferred)
        Route::get('/search/events', [PublicEventController::class, 'searchRedirect'])->name('events.searchRedirect');
    Route::post('/events/{event}/register', [App\Http\Controllers\EventController::class, 'register'])->name('events.register');
    // Form-based (non-AJAX) free registration & feedback submission
    Route::post('/events/{event}/register/form', [\App\Http\Controllers\EventParticipationController::class, 'register'])->name('events.register.form');
    Route::post('/events/{event}/feedback', [\App\Http\Controllers\EventParticipationController::class, 'submitFeedback'])->name('events.feedback');
    Route::post('/events/{event}/attendance', [\App\Http\Controllers\EventParticipationController::class, 'submitAttendance'])->name('events.attendance');
    Route::get('/events/{event}/ticket', [PublicEventController::class, 'ticket'])->name('events.ticket');
    // Notifications
    Route::get('/notifications', [NotificationsController::class,'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationsController::class,'markAllRead'])->name('notifications.markAllRead');
    // Certificate (event) - show & download (H+4 logic inside controller)
    Route::get('/events/{event}/certificate/{registration}', [\App\Http\Controllers\CertificateController::class, 'show'])->name('certificates.show');
    Route::get('/events/{event}/certificate/{registration}/download', [\App\Http\Controllers\CertificateController::class, 'download'])->name('certificates.download');

    // User profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/events', [\App\Http\Controllers\ProfileController::class, 'events'])->name('profile.events');
    Route::get('/profile/edit', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Save/unsave event
    Route::post('/events/{event}/save', function(\Illuminate\Http\Request $request, \App\Models\Event $event){
        $user = $request->user();
        if(!$user){ return response()->json(['success'=>false,'message'=>'Unauthorized'], 401); }
        $exists = \DB::table('user_saved_events')->where('user_id',$user->id)->where('event_id',$event->id)->exists();
        if($exists){
            \DB::table('user_saved_events')->where('user_id',$user->id)->where('event_id',$event->id)->delete();
            return response()->json(['success'=>true,'saved'=>false]);
        }
        \DB::table('user_saved_events')->insert(['user_id'=>$user->id,'event_id'=>$event->id,'created_at'=>now(),'updated_at'=>now()]);
        return response()->json(['success'=>true,'saved'=>true]);
    })->name('events.save');
});
Route::get('/courses', [\App\Http\Controllers\PublicCourseController::class, 'index'])->name('courses.index');

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
Route::get('/verifikasi', [AuthController::class, 'showVerification'])->name('verifikasi');
Route::post('/verifikasi', [AuthController::class, 'verifyCode'])->name('verifikasi.verify');
Route::get('/new-password', [AuthController::class, 'showNewPassword'])->name('new-password');
Route::post('/new-password', [AuthController::class, 'resetPassword'])->name('new-password.reset');
                                                                        
// Protected routes (require authentication)
Route::middleware(['auth'])->group(function () {
    // User dashboard (only for non-admin users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin dashboard (only for admin users)
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
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
        
        // Course management routes
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
    
    // User quiz routes
    Route::get('/courses/{course}/modules/{module}/quiz/start', [QuizController::class, 'start'])->name('user.quiz.start');
    Route::get('/courses/{course}/modules/{module}/quiz/{attempt}', [QuizController::class, 'take'])->name('user.quiz.take');
    Route::post('/courses/{course}/modules/{module}/quiz/{attempt}/answer', [QuizController::class, 'submitAnswer'])->name('user.quiz.answer');
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
        Route::resource('admin/events', \App\Http\Controllers\EventController::class, [
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
        // Certificate management
        Route::get('/admin/certificates', [\App\Http\Controllers\CertificateController::class, 'index'])->name('admin.certificates.index');
        Route::get('/admin/certificates/{event}/edit', [\App\Http\Controllers\CertificateController::class, 'edit'])->name('admin.certificates.edit');
        Route::put('/admin/certificates/{event}', [\App\Http\Controllers\CertificateController::class, 'update'])->name('admin.certificates.update');
        Route::get('/admin/events/{event}/certificates/generate-massal', [\App\Http\Controllers\CertificateController::class, 'generateMassal'])->name('admin.certificates.generate-massal');
    });
});