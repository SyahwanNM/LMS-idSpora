<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Course;
use App\Models\EventRegistration;
use App\Models\Carousel;

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
        // Event aktif: gunakan scope active agar yang sudah selesai (end_at < now) otomatis terhapus dari daftar.
        // Tetap ambil yang paling baru dibuat terlebih dahulu.
        $upcomingEvents = Event::active()
            ->withCount('registrations')
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Mark events that the current (non-admin) user has registered for
        if (Auth::check() && Auth::user()->role !== 'admin' && $upcomingEvents->isNotEmpty()) {
            $registeredIds = EventRegistration::query()
                ->where('user_id', Auth::id())
                ->whereIn('event_id', $upcomingEvents->pluck('id'))
                ->where('status', '!=', 'rejected') // Treat rejected as not registered so they can try again
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

        // Distinct materi & jenis from events for dynamic listing
        $materiList = Event::query()->whereNotNull('materi')->distinct()->orderBy('materi')->pluck('materi');
        $jenisList = Event::query()->whereNotNull('jenis')->distinct()->orderBy('jenis')->pluck('jenis');

        // Get carousel images for dashboard
        $dashboardCarousels = Carousel::active()
            ->forLocation('dashboard')
            ->orderBy('order')
            ->get();

        return view('dashboard', [
            'upcomingEvents' => $upcomingEvents,
            'featuredCourses' => $featuredCourses,
            'materiList' => $materiList,
            'jenisList' => $jenisList,
            'dashboardCarousels' => $dashboardCarousels,
        ]);
    }
}
