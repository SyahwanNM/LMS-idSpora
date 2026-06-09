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

// Validate referral code for course payment page (AJAX)
Route::middleware(['auth'])->get('/courses/{course}/check-referral', function (\Illuminate\Http\Request $request, \App\Models\Course $course) {
    $code = trim((string) $request->query('code', ''));
    if ($code === '') {
        return response()->json(['valid' => false, 'message' => 'Kode referral kosong.']);
    }

    $user = $request->user();

    // Cannot use own referral code
    if ($user && $user->referral_code && strcasecmp($code, (string) $user->referral_code) === 0) {
        return response()->json(['valid' => false, 'message' => 'Tidak dapat menggunakan kode referral milik sendiri.']);
    }

    // Only applies for reseller courses
    if (!(bool) ($course->is_reseller_course ?? false)) {
        return response()->json(['valid' => false, 'message' => 'Course ini tidak mendukung kode referral.']);
    }

    $referrer = \App\Models\User::where('referral_code', $code)->first();
    if (!$referrer) {
        return response()->json(['valid' => false, 'message' => 'Kode referral tidak valid.']);
    }

    $discountRate = 0.10; // 10%
    $hasDiscount = method_exists($course, 'hasDiscount') ? (bool) $course->hasDiscount() : false;
    $baseAmount = (float) ($hasDiscount ? ($course->discounted_price ?? $course->price) : ($course->price ?? 0));
    $finalAmount = round($baseAmount * (1 - $discountRate), 2);

    return response()->json([
        'valid'         => true,
        'discount_rate' => $discountRate,
        'final_amount'  => $finalAmount,
        'message'       => 'Kode referral valid! Diskon ' . ($discountRate * 100) . '% diterapkan.',
    ]);
})->name('courses.check-referral');

// Validate unified promo/referral code for event payment page (AJAX)
Route::middleware(['auth'])->get('/payment/{event}/check-code', function (\Illuminate\Http\Request $request, \App\Models\Event $event) {
    $code = trim((string) $request->query('code', ''));
    if ($code === '') {
        return response()->json(['valid' => false, 'message' => 'Kode kosong.']);
    }

    $user = $request->user();
    
    $attendanceType = strtolower(trim((string) $request->query('attendance_type', $request->input('attendance_type', 'offline'))));
    $isHybridEvent  = !empty($event->maps_url) && !empty($event->zoom_link) && ($event->price_offline > 0 || $event->price_online > 0);

    if ($isHybridEvent) {
        $rawHybridPrice = $attendanceType === 'online' ? (float) ($event->price_online ?? 0) : (float) ($event->price_offline ?? 0);
        $discountPct = ($event->hasDiscount()) ? (float) ($event->discount_percentage ?? 0) : 0.0;
        $baseAmount = $discountPct > 0 ? round($rawHybridPrice * (1 - $discountPct / 100), 2) : $rawHybridPrice;
    } else {
        $baseAmount = (float) ($event->hasDiscount() ? ($event->discounted_price ?? $event->price) : ($event->price ?? 0));
    }

    // 1. Cek apakah ini kode referral
    $referrer = \App\Models\User::where('referral_code', $code)->first();
    if ($referrer) {
        if ($user && $user->referral_code && strcasecmp($code, (string) $user->referral_code) === 0) {
            return response()->json(['valid' => false, 'message' => 'Tidak dapat menggunakan kode referral milik sendiri.']);
        }
        if (!(bool) ($event->is_reseller_event ?? false)) {
            return response()->json(['valid' => false, 'message' => 'Event ini tidak mendukung kode referral.']);
        }
        $discountRate = 0.10; // 10%
        $finalAmount = round($baseAmount * (1 - $discountRate), 2);
        return response()->json([
            'valid' => true,
            'type' => 'referral',
            'discount_rate' => $discountRate,
            'final_amount' => $finalAmount,
            'message' => 'Kode referral valid! Diskon ' . ($discountRate * 100) . '% diterapkan.',
        ]);
    }

    // 2. Cek apakah ini kode voucher
    $redemption = \App\Models\VoucherRedemption::where('user_id', $user->id)
        ->where('code', $code)
        ->first();
    if ($redemption) {
        if (!$redemption->isUsable()) {
            return response()->json(['valid' => false, 'message' => 'Voucher sudah kedaluwarsa atau telah terpakai.']);
        }
        $voucher = $redemption->voucher;
        if ($baseAmount < $voucher->min_purchase) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimal pembelian untuk menggunakan voucher ini adalah Rp' . number_format($voucher->min_purchase, 0, ',', '.') . '.'
            ]);
        }
        $discount = $voucher->calculateDiscount($baseAmount);
        $finalAmount = max(0.0, $baseAmount - $discount);
        return response()->json([
            'valid' => true,
            'type' => 'voucher',
            'discount' => (int) $discount,
            'final_amount' => (int) $finalAmount,
            'message' => 'Voucher berhasil diterapkan! Potongan Rp' . number_format($discount, 0, ',', '.'),
        ]);
    }

    return response()->json(['valid' => false, 'message' => 'Kode tidak ditemukan atau tidak valid.']);
})->name('payment.check-code');

// Validate unified promo/referral code for course payment page (AJAX)
Route::middleware(['auth'])->get('/courses/{course}/check-code', function (\Illuminate\Http\Request $request, \App\Models\Course $course) {
    $code = trim((string) $request->query('code', ''));
    if ($code === '') {
        return response()->json(['valid' => false, 'message' => 'Kode kosong.']);
    }

    $user = $request->user();

    // 1. Cek apakah ini kode referral
    $referrer = \App\Models\User::where('referral_code', $code)->first();
    if ($referrer) {
        if ($user && $user->referral_code && strcasecmp($code, (string) $user->referral_code) === 0) {
            return response()->json(['valid' => false, 'message' => 'Tidak dapat menggunakan kode referral milik sendiri.']);
        }
        if (!(bool) ($course->is_reseller_course ?? false)) {
            return response()->json(['valid' => false, 'message' => 'Course ini tidak mendukung kode referral.']);
        }
        $discountRate = 0.10; // 10%
        $hasDiscount = method_exists($course, 'hasDiscount') ? (bool) $course->hasDiscount() : false;
        $baseAmount = (float) ($hasDiscount ? ($course->discounted_price ?? $course->price) : ($course->price ?? 0));
        $finalAmount = round($baseAmount * (1 - $discountRate), 2);
        return response()->json([
            'valid' => true,
            'type' => 'referral',
            'discount_rate' => $discountRate,
            'final_amount' => $finalAmount,
            'message' => 'Kode referral valid! Diskon ' . ($discountRate * 100) . '% diterapkan.',
        ]);
    }

    // 2. Cek apakah ini kode voucher
    $redemption = \App\Models\VoucherRedemption::where('user_id', $user->id)
        ->where('code', $code)
        ->first();
    if ($redemption) {
        if (!$redemption->isUsable()) {
            return response()->json(['valid' => false, 'message' => 'Voucher sudah kedaluwarsa atau telah terpakai.']);
        }
        $voucher = $redemption->voucher;
        $hasDiscount = method_exists($course, 'hasDiscount') ? (bool) $course->hasDiscount() : false;
        $baseAmount = (float) ($hasDiscount ? ($course->discounted_price ?? $course->price) : ($course->price ?? 0));
        if ($baseAmount < $voucher->min_purchase) {
            return response()->json([
                'valid' => false,
                'message' => 'Minimal pembelian untuk menggunakan voucher ini adalah Rp' . number_format($voucher->min_purchase, 0, ',', '.') . '.'
            ]);
        }
        $discount = $voucher->calculateDiscount($baseAmount);
        $finalAmount = max(0.0, $baseAmount - $discount);
        return response()->json([
            'valid' => true,
            'type' => 'voucher',
            'discount' => (int) $discount,
            'final_amount' => (int) $finalAmount,
            'message' => 'Voucher berhasil diterapkan! Potongan Rp' . number_format($discount, 0, ',', '.'),
        ]);
    }

    return response()->json(['valid' => false, 'message' => 'Kode tidak ditemukan atau tidak valid.']);
})->name('courses.check-code');

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
