<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ManualPaymentController;
use App\Http\Controllers\Admin\CourseManualPaymentController;

// Manual payment registration (user-uploaded proof)
Route::post('/payment/{event}/manual-register', [ManualPaymentController::class, 'register'])->name('payment.manual.register');

// Validate referral code for event payment page (AJAX)
Route::middleware(['auth'])->get('/payment/{event}/check-referral', function (\Illuminate\Http\Request $request, \App\Models\Event $event) {
    $code = trim((string) $request->query('code', ''));
    if ($code === '') {
        return response()->json(['valid' => false, 'message' => 'Kode referral kosong.']);
    }

    $user = $request->user();

    // Cannot use own referral code
    if ($user && $user->referral_code && strcasecmp($code, (string) $user->referral_code) === 0) {
        return response()->json(['valid' => false, 'message' => 'Tidak dapat menggunakan kode referral milik sendiri.']);
    }

    // Only applies for reseller events
    if (!(bool) ($event->is_reseller_event ?? false)) {
        return response()->json(['valid' => false, 'message' => 'Event ini tidak mendukung kode referral.']);
    }

    $referrer = \App\Models\User::where('referral_code', $code)->first();
    if (!$referrer) {
        return response()->json(['valid' => false, 'message' => 'Kode referral tidak valid.']);
    }

    $discountRate = 0.10; // 10%
    return response()->json([
        'valid'         => true,
        'discount_rate' => $discountRate,
        'message'       => 'Kode referral valid! Diskon ' . ($discountRate * 100) . '% diterapkan.',
    ]);
})->name('payment.check-referral');

// Course manual payment proof upload (user-uploaded proof)
Route::middleware(['auth'])->group(function () {
	Route::post('/manual-payment/upload/{course}', [CourseManualPaymentController::class, 'upload'])->name('courses.manual-payment.upload');
	Route::post('/courses/{course}/free-enroll', [CourseManualPaymentController::class, 'freeEnroll'])->name('courses.free-enroll');

});

// Admin endpoints to verify or reject uploaded proofs
Route::middleware(['auth','admin'])->group(function(){
	Route::post('/admin/events/{event}/registrations/{registration}/approve', [\App\Http\Controllers\Admin\EventController::class, 'approveRegistration'])
		->name('admin.events.registrations.approve');
	Route::post('/admin/events/{event}/registrations/{registration}/reject', [\App\Http\Controllers\Admin\EventController::class, 'rejectRegistration'])
		->name('admin.events.registrations.reject');
	Route::patch('/admin/events/{event}/registrations/{registration}/cancel', [\App\Http\Controllers\Admin\EventController::class, 'cancelApprovalRegistration'])
		->name('admin.events.registrations.cancel');
	Route::delete('/admin/events/{event}/registrations/{registration}', [\App\Http\Controllers\Admin\EventController::class, 'destroyRegistration'])
		->name('admin.events.registrations.destroy');

	// Admin review for course manual payments
	Route::post('/admin/courses/{course}/manual-payments/{manualPayment}/approve', [CourseManualPaymentController::class, 'approve'])
		->name('admin.courses.manual-payments.approve');
	Route::post('/admin/courses/{course}/manual-payments/{manualPayment}/reject', [CourseManualPaymentController::class, 'reject'])
		->name('admin.courses.manual-payments.reject');
	Route::post('/admin/courses/{course}/manual-payments/{manualPayment}/pending', [CourseManualPaymentController::class, 'pending'])
		->name('admin.courses.manual-payments.pending');
});
