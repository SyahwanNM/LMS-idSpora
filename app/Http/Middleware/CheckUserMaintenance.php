<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\AdminSettings;

class CheckUserMaintenance
{
    /**
     * Handle an incoming request.
     * Block public/user routes when maintenance is enabled, but allow admin and login routes.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!AdminSettings::maintenanceEnabled()) {
            return $next($request);
        }

        // If maintenance is enabled, non-admin users should not remain logged in.
        // Force-logout to avoid showing authenticated navbar/state.
        try {
            if (Auth::check()) {
                $role = strtolower(trim((string) (Auth::user()->role ?? '')));
                if ($role !== 'admin') {
                    Auth::logout();
                    try {
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    } catch (\Throwable $e) {
                    }
                    $msg = AdminSettings::maintenanceMessage() ?: 'Mohon maaf, akses LMS sedang maintenance.';
                    try {
                        $request->session()->flash('maintenance_notice', $msg);
                    } catch (\Throwable $e) {
                    }
                }
            }
        } catch (\Throwable $e) {
        }

        $pathInfo = $request->path();

        // Always allow landing page during maintenance
        // Note: Request::path() returns '' for root ('/').
        if ($pathInfo === '' || $pathInfo === '/') {
            return $next($request);
        }

        // Allow if route starts with admin
        if (str_starts_with('/'.$pathInfo, '/admin')) {
            return $next($request);
        }

        // Allow login and sign-in routes
        $allowed = [
            'sign-in',
            'sign-up',
            'login',
            'logout',
            'auth',
            'forgot-password',
            'verifikasi',
            'new-password',
            'register',
        ];
        foreach ($allowed as $a) {
            if ($pathInfo === $a || str_starts_with($pathInfo, $a.'/')) {
                return $next($request);
            }
        }

        // Allow storage proxy route for landing page images/files
        if (str_starts_with('/'.$pathInfo, '/storage/')) {
            return $next($request);
        }

        // Allow if authenticated user is admin (so admin can access pages)
        try {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return $next($request);
            }
        } catch (\Throwable $_e) {
            // ignore
        }

        // Block all other routes for non-admin users by redirecting to landing page.
        // This ensures user/trainer can't reach dashboards during maintenance.
        $msg = AdminSettings::maintenanceMessage() ?: 'Mohon maaf, akses LMS sedang maintenance.';
        return redirect('/')->with('maintenance_notice', $msg);
    }
}
