<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth; // Tambahkan import ini

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
            return redirect()->route('login');
        }

        if (strcasecmp(Auth::user()->role, 'trainer') !== 0) {
            

            if (Auth::user()->role === 'admin') {
                return $next($request);
            }

            abort(403, 'Akses ditolak. Halaman ini khusus untuk Instruktur.');
        }

        return $next($request);
    }
}