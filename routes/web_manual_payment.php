<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManualPaymentController;
use App\Http\Controllers\CourseManualPaymentController;

// Manual payment registration (user-uploaded proof)
Route::post('/payment/{event}/manual-register', [ManualPaymentController::class, 'register'])->name('payment.manual.register');

// Course manual payment proof upload (user-uploaded proof)
Route::middleware(['auth'])->group(function () {
	Route::post('/manual-payment/upload/{course}', [CourseManualPaymentController::class, 'upload'])->name('courses.manual-payment.upload');
	Route::post('/courses/{course}/free-enroll', [CourseManualPaymentController::class, 'freeEnroll'])->name('courses.free-enroll');

});

// Admin endpoints to verify or reject uploaded proofs
Route::middleware(['auth','admin'])->group(function(){
	Route::post('/admin/events/{event}/registrations/{registration}/approve', [\App\Http\Controllers\EventController::class, 'approveRegistration'])
		->name('admin.events.registrations.approve');
	Route::post('/admin/events/{event}/registrations/{registration}/reject', [\App\Http\Controllers\EventController::class, 'rejectRegistration'])
		->name('admin.events.registrations.reject');

	// Admin review for course manual payments
	Route::post('/admin/courses/{course}/manual-payments/{manualPayment}/approve', [CourseManualPaymentController::class, 'approve'])
		->name('admin.courses.manual-payments.approve');
	Route::post('/admin/courses/{course}/manual-payments/{manualPayment}/reject', [CourseManualPaymentController::class, 'reject'])
		->name('admin.courses.manual-payments.reject');
	Route::post('/admin/courses/{course}/manual-payments/{manualPayment}/pending', [CourseManualPaymentController::class, 'pending'])
		->name('admin.courses.manual-payments.pending');
});
