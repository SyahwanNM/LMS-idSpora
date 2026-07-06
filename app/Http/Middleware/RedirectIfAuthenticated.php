<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Support\AdminSettings;

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

            if ($role === 'event_admin') {
                $assignedEventIds = \Illuminate\Support\Facades\DB::table('event_admin_assignments')
                    ->where('user_id', Auth::id())
                    ->pluck('event_id')
                    ->toArray();
                if (count($assignedEventIds) === 1) {
                    return redirect()->route('admin.events.show', $assignedEventIds[0]);
                } elseif (count($assignedEventIds) > 1) {
                    return redirect()->route('admin.events.index');
                }
                return redirect()->route('admin.dashboard');
            }

            // During maintenance, keep non-admin users on landing page.
            if (AdminSettings::maintenanceEnabled()) {
                $msg = AdminSettings::maintenanceMessage() ?: 'Mohon maaf, akses LMS sedang maintenance.';
                return redirect('/')->with('maintenance_notice', $msg);
            }

            if ($role === 'trainer') {
                return redirect()->route('trainer.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
