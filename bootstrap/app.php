<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'admin'])->group(base_path('routes/admin.php'));
            \Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'admin'])->group(base_path('routes/crm.php'));
            \Illuminate\Support\Facades\Route::middleware(['web', 'auth'])->group(base_path('routes/user.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\CheckUserMaintenance::class,
        ]);

        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'profile.complete' => 
            \App\Http\Middleware\RequireProfileComplete::class,
            'trainer' => \App\Http\Middleware\TrainerMiddleware::class,
            'trainer' => \App\Http\Middleware\TrainerMiddleware::class,
            'profile.complete' => \App\Http\Middleware\RequireProfileComplete::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
