<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManualPaymentController;

// Manual payment registration (user-uploaded proof)
Route::post('/payment/{event}/manual-register', [ManualPaymentController::class, 'register'])->name('payment.manual.register');

// Admin endpoints to verify or reject uploaded proofs
Route::middleware(['auth','admin'])->group(function(){
	Route::post('/admin/events/{event}/registrations/{registration}/approve', [\App\Http\Controllers\EventController::class, 'approveRegistration'])
		->name('admin.events.registrations.approve');
	Route::post('/admin/events/{event}/registrations/{registration}/reject', [\App\Http\Controllers\EventController::class, 'rejectRegistration'])
		->name('admin.events.registrations.reject');
});
