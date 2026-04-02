<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\MyCourseController;
use App\Http\Controllers\Api\CoursePaymentController;
use App\Http\Controllers\Api\CourseAccessController;
use App\Http\Controllers\Api\Admin\EventController as AdminEventController;
use App\Http\Controllers\Api\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Api\Admin\CourseTemplateController as AdminCourseTemplateController;
use App\Http\Controllers\Api\Admin\CourseModuleController as AdminCourseModuleController;
use App\Http\Controllers\Api\Admin\CoursePaymentController as AdminCoursePaymentController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Trainer\EventController as TrainerEventController;
use App\Http\Controllers\Api\Trainer\EventModuleSubmissionController as TrainerEventModuleSubmissionController;


// Throttle login to mitigate brute-force attempts (10 req/min per IP or user)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
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
        return $request->user();
    });

    Route::post('/events/{id}/register', [EventController::class, 'register']);

    // tambahan endpoint untuk alur event
    Route::get('/events/registrations', [EventController::class, 'listRegistrations']);
    Route::get('/events/{id}/registration/status', [EventController::class, 'registrationStatus']);
    Route::post('/events/{id}/payment', [EventController::class, 'createPayment']);
    Route::post('/events/{id}/cancel', [EventController::class, 'cancelRegistration']);

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

    // Trainer APIs (RESTful)
    Route::middleware(['trainer', 'throttle:100,1'])->prefix('trainer')->group(function () {
        // Events owned by trainer
        Route::get('events', [TrainerEventController::class, 'index']);
        Route::get('events/{event}', [TrainerEventController::class, 'show']);

        // Module submissions (upload for admin approval)
        Route::get('event-module-submissions', [TrainerEventModuleSubmissionController::class, 'index']);
        Route::post('events/{event}/module-submissions', [TrainerEventModuleSubmissionController::class, 'store']);
    });
});

// Admin Manage APIs (CRUD)
// Admin endpoints with stricter throttle (60 req/min)
Route::middleware(['auth:sanctum', 'admin', 'throttle:60,1'])->prefix('admin')->group(function () {
    // Users (read-only)
    Route::get('users', [AdminUserController::class, 'index']);
    Route::get('users/{user}', [AdminUserController::class, 'show']);

    // Events CRUD
    Route::apiResource('events', AdminEventController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    // Courses CRUD
    Route::apiResource('courses', AdminCourseController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

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
});