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
use App\Http\Controllers\Admin\CourseTemplateAdminController;
use App\Http\Controllers\Public\PublicTrainerProfileController;

// Preview: Trainer public profile (temporary, for admin preview)
Route::get('/admin/trainer-profile/{trainer}', [PublicTrainerProfileController::class, 'show'])->name('admin.trainer-profile.show');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/report', [CourseReportController::class, 'index'])->name('report');
    Route::get('/admin/report/revenue', [CourseReportController::class, 'revenue'])->name('admin.report.revenue');
    Route::get('/admin/report/growth', [CourseReportController::class, 'growth'])->name('admin.report.growth');
    Route::get('/admin/report/export/pdf', [CourseReportController::class, 'exportPdf'])->name('admin.report.export.pdf');
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
// Serve Add Event at a friendly URL using the canonical create form (auth+admin)
Route::middleware(['auth', 'admin'])->get('/admin/add-event', [EventController::class, 'create'])->name('admin.add-event');
// History (finished events)
Route::middleware(['auth', 'admin'])->get('/admin/events/history', [EventController::class, 'history'])->name('admin.events.history');
Route::get('admin/course-builder', function () {
    return view('admin/course-builder');
})->name('admin/course-builder');

// Legacy Add Course page (standalone view) with categories for the form
Route::get('/admin/add-course', function () {
    $categories = \App\Models\Category::select('id', 'name')->orderBy('name')->get();
    $trainers = \App\Models\User::where('role', 'trainer')->orderBy('name')->get(['id', 'name', 'email']);
    return view('admin/add-course', compact('categories', 'trainers'));
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

// Admin dashboard (only for admin users)
Route::middleware(['admin'])->group(function () {
    Route::get('/admin/reseller', [ResellerController::class, 'admin'])->name('admin.reseller');
    // Admin view: Pendapatan (financial breakdown)
    Route::get('/admin/view-pendapatan', [CourseRevenueDetailController::class, 'show'])
        ->name('admin.view-pendapatan');

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/finance', [\App\Http\Controllers\Admin\FinanceController::class, 'index'])->name('admin.finance.index');
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
    // Admin JSON endpoints
    Route::get('/admin/api/trainers', [TrainerApiController::class, 'index'])->name('admin.api.trainers');
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

    // Course Template Management (untuk admin membuat template struktur course)
    Route::get('/admin/templates', [CourseTemplateAdminController::class, 'index'])->name('admin.templates.index');
    Route::get('/admin/templates/create', [CourseTemplateAdminController::class, 'create'])->name('admin.templates.create');
    Route::post('/admin/templates', [CourseTemplateAdminController::class, 'store'])->name('admin.templates.store');
    Route::get('/admin/templates/{template}', [CourseTemplateAdminController::class, 'show'])->name('admin.templates.show');
    Route::get('/admin/templates/{template}/edit', [CourseTemplateAdminController::class, 'edit'])->name('admin.templates.edit');
    Route::put('/admin/templates/{template}', [CourseTemplateAdminController::class, 'update'])->name('admin.templates.update');
    Route::delete('/admin/templates/{template}', [CourseTemplateAdminController::class, 'destroy'])->name('admin.templates.destroy');

    // Course management routes
    // Publish course (set status active)
    Route::post('/admin/courses/{course}/publish', [CourseController::class, 'publish'])->name('admin.courses.publish');
    Route::get('/admin/courses/export', [CourseController::class, 'export'])->name('admin.courses.export');
    Route::get('/admin/courses/{course}/participants', [CourseController::class, 'participants'])->name('admin.courses.participants');
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
    // Publish event (show on user pages)
    Route::post('/admin/events/{event}/publish', [EventController::class, 'publish'])->name('admin.events.publish');
    // Unpublish event (batal publish)
    Route::post('/admin/events/{event}/unpublish', [EventController::class, 'unpublish'])->name('admin.events.unpublish');
    // Event QR actions (admin)
    Route::post('/admin/events/{event}/qr/generate', [EventController::class, 'generateQr'])->name('admin.events.qr.generate');
    Route::get('/admin/events/{event}/qr/download', [EventController::class, 'downloadQr'])->name('admin.events.qr.download');
    // Utility: resolve Google Maps short links to lat/lng
    Route::post('/admin/maps/resolve', [EventController::class, 'resolveMap'])->name('admin.maps.resolve');
    // Event document uploads (admin)
    Route::post('/admin/events/{event}/documents', [EventController::class, 'uploadDocuments'])->name('admin.events.documents.upload');
    // Admin: verify/reject trainer event module submission
    Route::post('/admin/events/{event}/module/approve', [EventController::class, 'approveModule'])->name('admin.events.module.approve');
    Route::post('/admin/events/{event}/module/reject', [EventController::class, 'rejectModule'])->name('admin.events.module.reject');
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
