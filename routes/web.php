<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

Route::get('/sign-up', function () {
    return view('sign-up');
})->name('sign-up');
Route::get('/sign-in', function () {
    return view('sign-in');
})->name('sign-in');
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgot-password');

Route::post('/sign-up', [App\Http\Controllers\AuthController::class, 'register'])->name('sign-up.post');