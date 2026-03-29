
<?php
// Payment page for course
Route::get('/courses/{course}/payment', [App\Http\Controllers\Admin\CourseController::class, 'payment'])->name('course.payment');

// Learn course modules (requires purchase/enrollment)
Route::middleware(['auth'])->get('/courses/{course}/learn', [App\Http\Controllers\Admin\CourseController::class, 'learn'])->name('course.learn');

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

Route::get('/admin/detail-event', function () {
    return view('/admin/detail-event');
});

Route::get('/course-detail/{course}', [CourseController::class, 'show'])->name('course.detail');

// Canonical course detail route (alias used in views)
Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');



Route::get('/bandingin', function () {
    return view('reseller.bandingin');
});



// Detail event (registered) view should receive Event from DB

// PUNYA DINI
Route::get('/modul-course', function () {
    return view('course.modul-course');
})->name('modul-course');

Route::get('/aturan-kuis', function () {
    return view('course.aturan-kuis');
})->name('aturan-kuis');

Route::get('/payment-course', function () {
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
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return app(\App\Http\Controllers\Public\LandingPageController::class)->index(request());
})->name('landing-page');

// Public pages

Route::get('/kendala', [PublicPagesController::class, 'support'])->name('public.support');
Route::post('/kendala', [PublicPagesController::class, 'storeSupport'])->name('public.support.store');
Route::middleware('auth')->get('/panduan', [PublicPagesController::class, 'guide'])->name('public.guide');


Route::get('/courses', [\App\Http\Controllers\Public\PublicCourseController::class, 'index'])->name('courses.index');

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


    // Trainer routes
    Route::middleware(['trainer'])->group(function () {
        Route::get('/trainer/event-modules', [TrainerEventModuleController::class, 'index'])->name('trainer.events.modules');
        Route::get('/trainer/api/event-modules', [TrainerEventModuleController::class, 'apiIndex'])->name('trainer.api.event-modules');
        Route::post('/trainer/events/{event}/module', [TrainerEventModuleController::class, 'upload'])->name('trainer.events.module.upload');
    });



});
// Include additional manual-payment routes (manual QRIS proof upload)
require __DIR__ . '/web_manual_payment.php';
