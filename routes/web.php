<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PublicEventController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\UserModuleController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\UserManagementController;

// Landing page: jika sudah login arahkan ke dashboard
Route::get('/', function(){
    if(auth()->check()) {
        return redirect()->route('dashboard');
    }
    return app(\App\Http\Controllers\LandingPageController::class)->index(request());
})->name('landing-page');


Route::get('/payment', function () {
    return view('payment');
})->name('payment');

// Event routes now require authentication to view & register
Route::middleware('auth')->group(function(){
    Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [PublicEventController::class, 'show'])->name('events.show');
    Route::post('/events/{event}/register', [App\Http\Controllers\EventController::class, 'register'])->name('events.register');
});
Route::get('/courses', [\App\Http\Controllers\PublicCourseController::class, 'index'])->name('courses.index');

// Authentication routes (only for guests)
Route::middleware(['guest'])->group(function () {
    Route::get('/sign-in', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/sign-in', [AuthController::class, 'login'])->name('login.post');

    Route::get('/sign-up', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/sign-up', [AuthController::class, 'register'])->name('register.post');
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
    // User dashboard (only for non-admin users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Admin dashboard (only for admin users)
    Route::middleware(['admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/active-users-count', [AdminController::class, 'activeUsersCount'])->name('admin.active-users-count');
        Route::post('/admin/events', [AdminController::class, 'storeEvent'])->name('admin.events.store');
        Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');

    // User management (Admin accounts & regular users)
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [UserManagementController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserManagementController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{user}/edit', [UserManagementController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [UserManagementController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
        
        // Course management routes
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
        
        // Quiz management routes
        Route::get('/admin/courses/{course}/modules/{module}/quiz', [QuizController::class, 'index'])->name('admin.courses.modules.quiz.index');
        Route::get('/admin/courses/{course}/modules/{module}/quiz/create', [QuizController::class, 'create'])->name('admin.courses.modules.quiz.create');
        Route::post('/admin/courses/{course}/modules/{module}/quiz', [QuizController::class, 'store'])->name('admin.courses.modules.quiz.store');
        Route::get('/admin/courses/{course}/modules/{module}/quiz/{question}', [QuizController::class, 'show'])->name('admin.courses.modules.quiz.show');
        Route::get('/admin/courses/{course}/modules/{module}/quiz/{question}/edit', [QuizController::class, 'edit'])->name('admin.courses.modules.quiz.edit');
        Route::put('/admin/courses/{course}/modules/{module}/quiz/{question}', [QuizController::class, 'update'])->name('admin.courses.modules.quiz.update');
        Route::delete('/admin/courses/{course}/modules/{module}/quiz/{question}', [QuizController::class, 'destroy'])->name('admin.courses.modules.quiz.destroy');
    });
    
    // User module access routes
    Route::get('/courses/{course}/modules', [UserModuleController::class, 'index'])->name('user.modules.index');
    Route::get('/courses/{course}/modules/{module}', [UserModuleController::class, 'show'])->name('user.modules.show');
    Route::get('/courses/{course}/modules/{module}/download', [UserModuleController::class, 'download'])->name('user.modules.download');
    Route::get('/courses/{course}/modules/{module}/stream', [UserModuleController::class, 'stream'])->name('user.modules.stream');
    
    // User quiz routes
    Route::get('/courses/{course}/modules/{module}/quiz/start', [QuizController::class, 'start'])->name('user.quiz.start');
    Route::get('/courses/{course}/modules/{module}/quiz/{attempt}', [QuizController::class, 'take'])->name('user.quiz.take');
    Route::post('/courses/{course}/modules/{module}/quiz/{attempt}/answer', [QuizController::class, 'submitAnswer'])->name('user.quiz.answer');
    Route::get('/courses/{course}/modules/{module}/quiz/{attempt}/result', [QuizController::class, 'result'])->name('user.quiz.result');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::resource('admin/events', \App\Http\Controllers\EventController::class, [
            'names' => [
                'index' => 'admin.events.index',
                'create' => 'admin.events.create',
                'store' => 'admin.events.store',
                'show' => 'admin.events.show',
                'edit' => 'admin.events.edit',
                'update' => 'admin.events.update',
                'destroy' => 'admin.events.destroy',
            ]
        ]);
    });
}); 