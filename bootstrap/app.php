<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        $middleware->api(append: [
            \App\Http\Middleware\NormalizeApiEnvelope::class,
            \App\Http\Middleware\AppendApiResponseCode::class,
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
        // Ensure API routes always return JSON errors (even when opened via browser).
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'data' => null,
                    'errors' => $e->errors(),
                    'pagination' => null,
                ], 422);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated',
                    'data' => null,
                    'errors' => null,
                    'pagination' => null,
                ], 401);
            }

            if ($e instanceof AuthorizationException) {
                return response()->json([
                    'status' => 'error',
                    'message' => $e->getMessage() ?: 'Forbidden',
                    'data' => null,
                    'errors' => null,
                    'pagination' => null,
                ], 403);
            }

            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found',
                    'data' => null,
                    'errors' => null,
                    'pagination' => null,
                ], 404);
            }

            if ($e instanceof NotFoundHttpException) {
                $msg = (string) $e->getMessage();
                // When implicit route-model binding fails, Laravel wraps it as NotFoundHttpException
                // with message like: "No query results for model [App\\Models\\Event] 6".
                if (str_starts_with($msg, 'No query results for model')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Resource not found',
                        'data' => null,
                        'errors' => null,
                        'pagination' => null,
                    ], 404);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Route not found',
                    'data' => null,
                    'errors' => null,
                    'pagination' => null,
                ], 404);
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = (int) $e->getStatusCode();
                $message = $status >= 500 ? 'Server error' : ($e->getMessage() ?: 'Request error');
                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'data' => null,
                    'errors' => null,
                    'pagination' => null,
                ], $status);
            }

            // Fallback for other exceptions on API routes
            return response()->json([
                'status' => 'error',
                'message' => 'Server error',
                'data' => null,
                'errors' => null,
                'pagination' => null,
            ], 500);
        });
    })->create();
