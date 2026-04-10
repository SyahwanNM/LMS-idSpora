<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $role = strtolower(trim((string) (Auth::user()->role ?? '')));

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            if ($role === 'trainer') {
                return redirect()->route('trainer.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
