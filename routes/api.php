<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventCertificateController;
use App\Http\Controllers\Api\CourseCertificateController;
use App\Http\Controllers\Api\EventAttendanceController;
use App\Http\Controllers\Admin\CourseReportController as AdminCourseReportController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\MyCourseController;
use App\Http\Controllers\Api\CoursePaymentController;
use App\Http\Controllers\Api\CourseAccessController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\Admin\EventController as AdminEventController;
use App\Http\Controllers\Api\Admin\ApiAdminCourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\CourseTemplateController as AdminCourseTemplateController;
use App\Http\Controllers\Api\Admin\CourseModuleController as AdminCourseModuleController;
use App\Http\Controllers\Api\Admin\CoursePaymentController as AdminCoursePaymentController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\EventGrowthReportController as AdminEventGrowthReportController;
use App\Http\Controllers\Api\Trainer\EventController as TrainerEventController;
use App\Http\Controllers\Api\Trainer\EventModuleSubmissionController as TrainerEventModuleSubmissionController;
use App\Http\Controllers\Api\Trainer\CourseSubmissionController as TrainerCourseSubmissionController;


// Throttle login to mitigate brute-force attempts (10 req/min per IP or user)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
// Register new account (5 req/min per IP to prevent spam)
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
// Verify OTP after register (10 req/min per IP)
Route::post('/register/verify-otp', [AuthController::class, 'verifyRegisterOtp'])->middleware('throttle:10,1');
// Resend OTP for registration (3 req/min per IP)
Route::post('/register/resend-otp', [AuthController::class, 'resendRegisterOtp'])->middleware('throttle:3,1');
// Public events listing throttled to avoid scraping (120 req/min)
Route::get('/events', [EventController::class, 'index'])->middleware('throttle:120,1');
Route::get('/events/{id}', [EventController::class, 'show'])->where('id', '[0-9]+')->middleware('throttle:120,1');

// Public courses listing throttled to avoid scraping (120 req/min)
Route::get('/courses', [CourseController::class, 'index'])->middleware('throttle:120,1');
Route::get('/courses/{course}', [CourseController::class, 'show'])->whereNumber('course')->middleware('throttle:120,1');
// Authenticated user actions with moderate throttle (100 req/min)
Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cek Profil Sendiri
    Route::get('/user', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'message' => 'OK',
            'data' => $request->user(),
        ]);
    });

    Route::post('/events/{id}/register', [EventController::class, 'register']);

    // Event endpoints
    Route::get('/events/registrations', [EventController::class, 'listRegistrations']);
    Route::get('/events/{id}/registration/status', [EventController::class, 'registrationStatus']);
    Route::post('/events/{id}/payment', [EventController::class, 'createPayment']);
    Route::post('/events/{id}/cancel', [EventController::class, 'cancelRegistration']);
    Route::post('/events/{id}/feedback', [EventController::class, 'submitFeedback']);
    Route::get('/events/{id}/materials', [EventController::class, 'materials']);

    // Midtrans payment endpoints untuk event
    Route::get('/events/{id}/midtrans/pending-order', [EventController::class, 'midtransPendingOrder'])->where('id', '[0-9]+');
    Route::get('/events/{id}/midtrans/snap-token',    [EventController::class, 'midtransSnapToken'])->where('id', '[0-9]+');
    Route::post('/events/{id}/midtrans/finalize',     [EventController::class, 'midtransFinalize'])->where('id', '[0-9]+');

    // Manual Payment Endpoints
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
    Route::post('/manual-payment', [PaymentController::class, 'store']);
    // Legacy: update via POST (kept for backward compatibility)
    Route::post('/manual-payment/{id}', [PaymentController::class, 'update']);
    // RESTful: update via PUT/PATCH
    Route::match(['PUT', 'PATCH'], '/manual-payment/{id}', [PaymentController::class, 'update']);
    Route::delete('/manual-payment/{id}', [PaymentController::class, 'destroy']);

    // Courses - user flow
    Route::get('/me/courses', [MyCourseController::class, 'index']);
    Route::get('/courses/{course}/purchase/status', [CoursePaymentController::class, 'status'])->whereNumber('course');
    Route::post('/courses/{course}/enroll', [CoursePaymentController::class, 'enroll'])->whereNumber('course');
    Route::post('/courses/{course}/payment-proof', [CoursePaymentController::class, 'uploadProof'])->whereNumber('course');

    Route::get('/courses/{course}/modules', [CourseAccessController::class, 'modules'])->whereNumber('course');
    Route::get('/courses/{course}/modules/{module}', [CourseAccessController::class, 'module'])->whereNumber('course')->whereNumber('module');
    Route::post('/courses/{course}/modules/{module}/complete', [CourseAccessController::class, 'complete'])->whereNumber('course')->whereNumber('module');
    Route::get('/courses/{course}/progress', [CourseAccessController::class, 'progress'])->whereNumber('course');

    // Quiz
    Route::get('/courses/{course}/modules/{module}/quiz', [QuizController::class, 'show'])->whereNumber('course')->whereNumber('module');
    Route::post('/courses/{course}/modules/{module}/quiz/start', [QuizController::class, 'start'])->whereNumber('course')->whereNumber('module');
    Route::post('/courses/{course}/modules/{module}/quiz/attempts/{attempt}/answer', [QuizController::class, 'submitAnswer'])->whereNumber('course')->whereNumber('module')->whereNumber('attempt');
    Route::post('/courses/{course}/modules/{module}/quiz/attempts/{attempt}/finish', [QuizController::class, 'finish'])->whereNumber('course')->whereNumber('module')->whereNumber('attempt');
    Route::get('/courses/{course}/modules/{module}/quiz/result', [QuizController::class, 'result'])->whereNumber('course')->whereNumber('module');

    // Course reviews
    Route::get('/courses/{course}/reviews', [CourseController::class, 'reviews'])->whereNumber('course');
    Route::post('/courses/{course}/reviews', [CourseController::class, 'submitReview'])->whereNumber('course');

    // Event certificates
    Route::get('/me/event-certificates', [EventCertificateController::class, 'index']);
    Route::get('/events/{event}/certificate', [EventCertificateController::class, 'show'])->whereNumber('event');
    Route::get('/events/{event}/certificate/download', [EventCertificateController::class, 'download'])
        ->whereNumber('event')
        ->name('api.events.certificate.download');

    // Course certificates
    Route::get('/me/course-certificates', [CourseCertificateController::class, 'index']);
    Route::get('/courses/{course}/certificate', [CourseCertificateController::class, 'show'])->whereNumber('course');
    Route::get('/courses/{course}/certificate/download', [CourseCertificateController::class, 'download'])
        ->whereNumber('course')
        ->name('api.courses.certificate.download');

    // Midtrans payment endpoints untuk course
    Route::get('/courses/{course}/midtrans/pending-order', [PaymentController::class, 'coursePendingOrder'])->whereNumber('course');
    Route::get('/courses/{course}/midtrans/snap-token',    [PaymentController::class, 'courseSnapToken'])->whereNumber('course');
    // Finalize/refresh by order_id (called from onClose/onSuccess after Snap popup)
    Route::post('/courses/midtrans/finalize/{orderId}',    [PaymentController::class, 'refreshCoursePayment']);

    // Event attendance (QR scan)
    Route::get('/events/{event}/attendance/status', [EventAttendanceController::class, 'status'])->whereNumber('event');
    Route::get('/events/{event}/attendance/qr-info', [EventAttendanceController::class, 'qrInfo'])->whereNumber('event');
    Route::post('/events/{event}/attendance/scan', [EventAttendanceController::class, 'scan'])
        ->whereNumber('event')
        ->name('api.events.attendance.scan');

    // Trainer APIs (RESTful)
    Route::middleware(['trainer', 'throttle:100,1'])->prefix('trainer')->group(function () {
        // Events owned by trainer
        Route::get('events', [TrainerEventController::class, 'index']);
        Route::get('events/{event}', [TrainerEventController::class, 'show']);

        // Module submissions (upload for admin approval)
        Route::get('event-module-submissions', [TrainerEventModuleSubmissionController::class, 'index']);
        Route::post('events/{event}/module-submissions', [TrainerEventModuleSubmissionController::class, 'store']);

        // Course submissions: trainer kirim daftar judul → otomatis buat course + clone template modul
        Route::post('course-submissions', [TrainerCourseSubmissionController::class, 'store']);
    });
});

// Admin Manage APIs (CRUD)
// Admin endpoints with stricter throttle (60 req/min)
Route::middleware(['auth:sanctum', 'admin', 'throttle:60,1'])->prefix('admin')->as('api.admin.')->group(function () {
    // Users (read-only)
    Route::get('users', [AdminUserController::class, 'index']);
    Route::get('users/{user}', [AdminUserController::class, 'show']);

    // Events CRUD
    Route::apiResource('events', AdminEventController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    // Daftar peserta terdaftar per event
    Route::get('events/{event}/registrations', [AdminEventController::class, 'registrations'])->whereNumber('event');
    // Upload dokumen operasional event (virtual background & absensi)
    Route::post('events/{event}/documents', [AdminEventController::class, 'uploadDocuments'])->whereNumber('event');
    // Trainer module submissions: list, approve, reject
    Route::get('events/{event}/trainer-modules', [AdminEventController::class, 'listModules'])->whereNumber('event');
    Route::post('events/{event}/trainer-modules/{module}/approve', [AdminEventController::class, 'approveModule'])->whereNumber('event')->whereNumber('module');
    Route::post('events/{event}/trainer-modules/{module}/reject', [AdminEventController::class, 'rejectModule'])->whereNumber('event')->whereNumber('module');

    // Courses CRUD
    Route::apiResource('courses', AdminCourseController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    // Publish / Unpublish course
    Route::post('courses/{course}/publish',   [AdminCourseController::class, 'publish'])->whereNumber('course');
    Route::post('courses/{course}/unpublish', [AdminCourseController::class, 'unpublish'])->whereNumber('course');

    // Course templates CRUD (versioned blueprint for courses)
    Route::apiResource('course-templates', AdminCourseTemplateController::class)
        ->only(['index', 'show', 'store', 'update', 'destroy']);

    // Course payments (approve/reject manual payments for courses)
    Route::get('course-payments', [AdminCoursePaymentController::class, 'index']);
    Route::get('course-payments/{coursePayment}', [AdminCoursePaymentController::class, 'show']);
    Route::post('course-payments/{coursePayment}/approve', [AdminCoursePaymentController::class, 'approve']);
    Route::post('course-payments/{coursePayment}/reject', [AdminCoursePaymentController::class, 'reject']);
    Route::post('course-payments/{coursePayment}/pending', [AdminCoursePaymentController::class, 'pending']);

    // Course modules CRUD
    Route::get('courses/{course}/modules', [AdminCourseModuleController::class, 'index']);
    Route::post('courses/{course}/modules', [AdminCourseModuleController::class, 'store']);
    Route::put('courses/{course}/modules/{module}', [AdminCourseModuleController::class, 'update']);
    Route::delete('courses/{course}/modules/{module}', [AdminCourseModuleController::class, 'destroy']);
    Route::post('courses/{course}/modules/reorder', [AdminCourseModuleController::class, 'reorder']);

    // Report Revenue & Growth
    Route::get('reports/revenue', [AdminCourseReportController::class, 'revenue'])->name('api.admin.reports.revenue');
    Route::get('reports/growth', [AdminCourseReportController::class, 'growth'])->name('api.admin.reports.growth');
    Route::get('reports/event-revenue', function (\Illuminate\Http\Request $request) {
        return app(\App\Http\Controllers\Admin\AdminController::class)->eventRevenueApi($request);
    })->name('api.admin.reports.event-revenue');

    // Registration Recap: detail revenue + expenses per event
    Route::get('events/{event}/revenue-detail', function (\Illuminate\Http\Request $request, \App\Models\Event $event) {
        // Revenue dari settled payments
        $payments = \App\Models\ManualPayment::where('event_id', $event->id)
            ->whereIn('status', ['paid', 'verified', 'settled'])
            ->get(['amount', 'referral_code']);

        $normalPayments   = $payments->filter(fn($p) => empty($p->referral_code));
        $referralPayments = $payments->filter(fn($p) => !empty($p->referral_code));

        $incomeRows = [];
        if ($normalPayments->count() > 0) {
            $normalTotal = (float) $normalPayments->sum('amount');
            $normalUnit  = (float) round($normalTotal / $normalPayments->count());
            $incomeRows[] = ['label' => 'Tiket Normal', 'qty' => $normalPayments->count(), 'unit_price' => $normalUnit, 'total' => $normalTotal];
        }
        if ($referralPayments->count() > 0) {
            $referralTotal = (float) $referralPayments->sum('amount');
            $referralUnit  = (float) round($referralTotal / $referralPayments->count());
            $incomeRows[] = ['label' => 'Tiket Referral', 'qty' => $referralPayments->count(), 'unit_price' => $referralUnit, 'total' => $referralTotal];
        }
        if (empty($incomeRows)) {
            $registeredCount = (int) $event->registrations()->where('status', 'active')->count();
            $revenue = (float) $payments->sum('amount');
            $avgUnit = $registeredCount > 0 ? (float) round($revenue / $registeredCount) : (float) ($event->price ?? 0);
            $incomeRows[] = ['label' => 'Tiket Pendaftar', 'qty' => $registeredCount, 'unit_price' => $avgUnit, 'total' => $revenue];
        }

        // Referral discount sebagai pengeluaran
        $normalUnitPrice       = (float) ($event->price ?? 0);
        $referralDiscountTotal = 0.0;
        foreach ($referralPayments as $rp) {
            $referralDiscountTotal += max(0, $normalUnitPrice - (float) ($rp->amount ?? 0));
        }

        // Expense rows dari tabel event_expenses
        $expenseRows = $event->expenses()->get(['item', 'quantity', 'unit_price', 'total'])
            ->map(fn($r) => [
                'item'       => $r->item,
                'qty'        => (int) ($r->quantity ?? 0),
                'unit_price' => (float) ($r->unit_price ?? 0),
                'total'      => (float) ($r->total ?? 0),
            ])->values()->all();

        if ($referralDiscountTotal > 0) {
            $expenseRows[] = [
                'item'       => 'Diskon Kode Referral (' . $referralPayments->count() . ' peserta)',
                'qty'        => $referralPayments->count(),
                'unit_price' => $referralPayments->count() > 0 ? round($referralDiscountTotal / $referralPayments->count()) : 0,
                'total'      => $referralDiscountTotal,
            ];
        }

        // Speaker salaries
        $speakerSalaries = \App\Models\EventSpeaker::where('event_id', $event->id)
            ->where('salary', '>', 0)->get(['name', 'salary']);
        foreach ($speakerSalaries as $sp) {
            $expenseRows[] = ['item' => 'Gaji Trainer: ' . $sp->name, 'qty' => 1, 'unit_price' => (float) $sp->salary, 'total' => (float) $sp->salary];
        }

        $revenueTotal  = (float) $payments->sum('amount');
        $expenseTotal  = array_sum(array_column($expenseRows, 'total'));
        $profit        = $revenueTotal - $expenseTotal;

        return response()->json([
            'status'  => 'success',
            'message' => 'Registration Recap: ' . $event->title,
            'data'    => [
                'event'             => ['id' => $event->id, 'title' => $event->title, 'date' => $event->event_date?->toDateString(), 'price' => (float) $event->price],
                'income_breakdown'  => $incomeRows,
                'expense_breakdown' => $expenseRows,
                'summary'           => ['revenue_total' => $revenueTotal, 'expense_total' => $expenseTotal, 'profit' => $profit, 'profit_status' => $profit >= 0 ? 'Menguntungkan' : 'Rugi'],
            ],
        ]);
    })->whereNumber('event');

    // Course revenue detail (financial breakdown per course)
    Route::get('courses/{course}/revenue-detail', [\App\Http\Controllers\Admin\CourseRevenueDetailController::class, 'apiShow'])->whereNumber('course');

    // Reports
    Route::get('reports/events/growth', AdminEventGrowthReportController::class);
});