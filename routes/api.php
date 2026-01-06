<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CallbackController;


// Route Khusus untuk Midtrans (Jangan dikasih Middleware Auth!)
Route::post('/midtrans-callback', [CallbackController::class, 'callback']);


Route::post('/login', [AuthController::class, 'login']);
Route::get ('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cek Profil Sendiri
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/events/{id}/register', [EventController::class, 'register']);

    // Disarankan tambahan endpoint untuk alur event
    Route::get('/events/registrations', [EventController::class, 'listRegistrations']);
    Route::get('/events/{id}/registration/status', [EventController::class, 'registrationStatus']);
    Route::post('/events/{id}/payment', [EventController::class, 'createPayment']);
    Route::post('/events/{id}/cancel', [EventController::class, 'cancelRegistration']);
});