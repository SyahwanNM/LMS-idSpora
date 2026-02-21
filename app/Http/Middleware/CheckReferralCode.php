<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;

class CheckReferralCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cek apakah ada parameter 'ref' di URL
        if ($request->has('ref')) {
            // Simpan ke cookie selama 7 hari (10080 menit)
            $response->withCookie(cookie('referral_code', $request->query('ref'), 10080));
        }

        return $response;
    }
}
