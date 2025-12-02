<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserMaintenance
{
    /**
     * Handle an incoming request.
     * Block public/user routes when maintenance is enabled, but allow admin and login routes.
     */
    public function handle(Request $request, Closure $next)
    {
        // Use admin settings file for maintenance flag
        $path = storage_path('app/admin_settings.json');
        $isDown = false;
        $payload = null;
        if (file_exists($path)) {
            try {
                $payload = json_decode(file_get_contents($path), true);
                $isDown = !empty($payload['maintenance_mode']);
            } catch (\Throwable $_e) {
                $isDown = false;
            }
        }

        if (!$isDown) {
            return $next($request);
        }

        // Allow admin routes (prefix /admin) and API or asset routes
        $uri = $request->getRequestUri();
        $pathInfo = $request->path();

        // Allow if route starts with admin
        if (str_starts_with('/'.$pathInfo, '/admin')) {
            return $next($request);
        }

        // Allow login and sign-in routes
        $allowed = [
            'sign-in',
            'login',
            'logout',
        ];
        foreach ($allowed as $a) {
            if ($pathInfo === $a || str_starts_with($pathInfo, $a.'/')) {
                return $next($request);
            }
        }

        // Allow if authenticated user is admin (so admin can access pages)
        try {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return $next($request);
            }
        } catch (\Throwable $_e) {
            // ignore
        }

        // Otherwise show maintenance page
        $message = $payload['message'] ?? null;
        return response()->view('errors.503', ['message' => $message], 503);
    }
}
