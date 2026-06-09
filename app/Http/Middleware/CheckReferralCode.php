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
            $refCode = $request->query('ref');
            // Simpan ke cookie selama 7 hari (10080 menit)
            $response->withCookie(cookie('referral_code', $refCode, 10080));

            try {
                // Cari reseller berdasarkan kode referral
                $reseller = \App\Models\User::where('referral_code', $refCode)->first();
                if ($reseller) {
                    $ip = $request->ip();
                    $userAgent = $request->userAgent();

                    // Check throttle: 15 menit per IP untuk reseller yang sama
                    $recentClick = \Illuminate\Support\Facades\DB::table('referral_clicks')
                        ->where('user_id', $reseller->id)
                        ->where('ip_address', $ip)
                        ->where('created_at', '>=', now()->subMinutes(15))
                        ->exists();

                    if (!$recentClick) {
                        \Illuminate\Support\Facades\DB::table('referral_clicks')->insert([
                            'user_id' => $reseller->id,
                            'ip_address' => $ip,
                            'user_agent' => $userAgent,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Failed to log referral click: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
