<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
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

            // If client already expects JSON, let Laravel handle it normally.
            if ($request->expectsJson()) {
                return null;
            }

            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Resource not found',
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
                    ], 404);
                }

                return response()->json([
                    'status' => 'error',
                    'message' => 'Route not found',
                ], 404);
            }

            if ($e instanceof HttpExceptionInterface) {
                $status = (int) $e->getStatusCode();
                $message = $status >= 500 ? 'Server error' : ($e->getMessage() ?: 'Request error');
                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                ], $status);
            }

            return null;
        });
    })->create();
