<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Course;
use App\Models\EventRegistration;

class DashboardController extends Controller
{
    public function index()
    {
        // Redirect admin users to admin dashboard just in case route protection misses
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Upcoming events: tampilkan event yang baru DITAMBAHKAN (created_at terbaru) di paling kiri.
        // Tetap hanya ambil event dengan tanggal >= hari ini.
        $threshold = now()->subHours(6)->format('Y-m-d H:i:s');
        $upcomingEvents = Event::query()
            ->withCount('registrations')
            // Hanya event yang belum lewat 6 jam sejak start
            ->where(function($q) use ($threshold){
                $q->whereNull('event_date')
                  ->orWhereRaw("TIMESTAMP(event_date, COALESCE(event_time,'00:00:00')) >= ?", [$threshold]);
            })
            // Tambahan: tetap filter tanggal >= hari ini untuk preferensi UI (opsional)
            ->whereDate('event_date', '>=', now()->toDateString())
            ->orderByDesc('created_at') // terbaru dulu
            ->limit(8)
            ->get();

        // Mark events that the current (non-admin) user has registered for
        if (Auth::check() && Auth::user()->role !== 'admin' && $upcomingEvents->isNotEmpty()) {
            $registeredIds = EventRegistration::query()
                ->where('user_id', Auth::id())
                ->whereIn('event_id', $upcomingEvents->pluck('id'))
                ->pluck('event_id')
                ->all();

            if (!empty($registeredIds)) {
                $upcomingEvents->transform(function($ev) use ($registeredIds) {
                    $ev->is_registered = in_array($ev->id, $registeredIds, true);
                    return $ev;
                });
            }
        }

        // Featured courses sample (adjust logic as needed)
        $featuredCourses = Course::query()
            ->with(['category', 'modules'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'upcomingEvents' => $upcomingEvents,
            'featuredCourses' => $featuredCourses,
        ]);
    }
}
