<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\ActivityLog;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
        'course' => \App\Models\Course::class,
        'event' => \App\Models\Event::class,
    ]);
        // Register middleware to protect user pages with app-level maintenance
        try {
            $router = $this->app->make(Router::class);
            $router->pushMiddlewareToGroup('web', \App\Http\Middleware\CheckUserMaintenance::class);
        } catch (\Throwable $_e) {
            // ignore if router not available yet
        }

        // Global activity logs for auth events (covers all login flows)
        try {
            Event::listen(Login::class, function (Login $event) {
                try {
                    ActivityLog::create([
                        'user_id' => optional($event->user)->id,
                        'action' => 'Login',
                        'description' => 'Login (event)'
                    ]);
                } catch (\Throwable $e) { /* swallow */ }
            });
            Event::listen(Logout::class, function (Logout $event) {
                try {
                    ActivityLog::create([
                        'user_id' => optional($event->user)->id,
                        'action' => 'Logout',
                        'description' => 'Logout (event)'
                    ]);
                } catch (\Throwable $e) { /* swallow */ }
            });
        } catch (\Throwable $_e) {
            // ignore if event dispatcher not ready
        }
    }
}
