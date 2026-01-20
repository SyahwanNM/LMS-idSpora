<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Skip middleware for admin users
        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        // Skip middleware if user is already on profile edit page or account settings
        if ($request->routeIs('profile.edit') || $request->routeIs('profile.update') || $request->routeIs('profile.account-settings') || $request->routeIs('profile.update-account-settings')) {
            return $next($request);
        }

        // Check if profile is incomplete
        if ($user && !$user->isProfileComplete()) {
            // Get the first missing field for deep-link
            $missingFields = $user->getMissingProfileFields();
            $focusField = !empty($missingFields) ? $missingFields[0] : null;
            
            $redirectUrl = route('profile.edit');
            if ($focusField) {
                $redirectUrl .= '?focus=' . $focusField;
            }
            
            return redirect($redirectUrl)->with('warning', 'Silakan lengkapi profil Anda terlebih dahulu untuk melanjutkan.');
        }

        return $next($request);
    }
}
