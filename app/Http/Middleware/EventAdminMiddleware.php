<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows access to users with role 'admin' OR 'event_admin'.
 * For event_admin, further scope checks happen inside controllers.
 */
class EventAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $isApiRequest = $request->expectsJson() || $request->is('api/*');

        if (!auth()->check()) {
            if ($isApiRequest) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = auth()->user();
        $role = (string) ($user->role ?? '');

        if (!in_array($role, ['admin', 'event_admin'], true)) {
            if ($isApiRequest) {
                return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}
