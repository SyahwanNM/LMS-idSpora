<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TrainerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        $user = Auth::user();
        $role = (string) ($user->role ?? '');

        // Admin boleh mengakses halaman trainer bila dibutuhkan (mis. debugging / review).
        if (strcasecmp($role, 'admin') === 0) {
            return $next($request);
        }

        if (strcasecmp($role, 'trainer') !== 0) {
            abort(403, 'Akses ditolak. Halaman ini khusus untuk Instruktur.');
        }

        return $next($request);
    }
}
