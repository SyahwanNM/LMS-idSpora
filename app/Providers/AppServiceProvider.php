<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotification;
use Illuminate\Support\Facades\URL;

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
        if (app()->environment('production') || app()->environment('staging')) {
            URL::forceScheme('https');
        }

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

        // Share notifications with the navbar
        View::composer('partials.navbar-after-login', function ($view) {
            if (Auth::check()) {
                $notifications = UserNotification::where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->take(10) // Limit to latest 10
                    ->get();
                $unreadNotificationCount = UserNotification::where('user_id', Auth::id())
                    ->whereNull('read_at')
                    ->count();
            } else {
                $notifications = collect();
                $unreadNotificationCount = 0;
            }
            $view->with('notifications', $notifications)
                 ->with('unreadNotificationCount', $unreadNotificationCount);
        });
    }
}
