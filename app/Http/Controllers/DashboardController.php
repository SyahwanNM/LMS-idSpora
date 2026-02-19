<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Course;
use App\Models\EventRegistration;
use App\Models\Carousel;
use App\Models\Enrollment;
use App\Models\Progress;
use App\Models\LearningTimeDaily;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
            ->withCount(['registrationsActive as registrations_count'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Mark events that the current (non-admin) user has registered for or saved
        if (Auth::check() && Auth::user()->role !== 'admin' && $upcomingEvents->isNotEmpty()) {
            $userId = Auth::id();
            $eventIds = $upcomingEvents->pluck('id');

            $registeredIds = EventRegistration::query()
                ->where('user_id', $userId)
                ->whereIn('event_id', $eventIds)
                ->where('status', '!=', 'rejected')
                ->pluck('event_id')
                ->all();

            $savedIds = \Illuminate\Support\Facades\DB::table('user_saved_events')
                ->where('user_id', $userId)
                ->whereIn('event_id', $eventIds)
                ->pluck('event_id')
                ->all();

            $upcomingEvents->transform(function($ev) use ($registeredIds, $savedIds) {
                $ev->is_registered = in_array($ev->id, $registeredIds, true);
                $ev->is_saved = in_array($ev->id, $savedIds, true);
                return $ev;
            });
        }

        // Featured courses sample (adjust logic as needed)
        $featuredCourses = Course::query()
            ->with(['category', 'modules'])
            ->withCount('enrollments')
            ->withAvg('reviews', 'rating')
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

        // Get events registered by the current user (only active registrations and future/ongoing events)
        $userEvents = EventRegistration::query()
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->with('event')
            ->whereHas('event', function($q) {
                $q->where('event_date', '>=', now()->startOfDay());
            })
            ->get()
            ->pluck('event')
            ->sortBy('event_date');

        // Get active course enrollments for "Lanjutkan Belajar" section
        $userEnrollments = Enrollment::query()
            ->where('user_id', Auth::id())
            ->where('status', 'active')
            ->with(['course.modules', 'progress'])
            ->latest('enrolled_at')
            ->limit(5)
            ->get();

        // Get learning statistics (hours spent per day in current week).
        // Primary source: realtime learning-time aggregation table.
        // Fallback: approximate from completed modules' duration (legacy behavior).
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $labels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $dailyLearning = array_fill_keys($labels, 0.0);

        $rows = LearningTimeDaily::query()
            ->select('learned_on', DB::raw('SUM(seconds) as total_seconds'))
            ->where('user_id', Auth::id())
            ->whereBetween('learned_on', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->groupBy('learned_on')
            ->get();

        $labelByIsoDow = [
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab',
            7 => 'Min',
        ];

        foreach ($rows as $row) {
            $date = Carbon::parse($row->learned_on);
            $label = $labelByIsoDow[$date->isoWeekday()] ?? null;
            if (!$label) {
                continue;
            }
            $hours = ((int) $row->total_seconds) / 3600;
            $dailyLearning[$label] = (float) number_format($hours, 1);
        }

        $learningChartData = array_values($dailyLearning);

        // Fallback to legacy approximation if we don't have realtime logs yet.
        if (array_sum($learningChartData) == 0.0) {
            $learningProgress = Progress::query()
                ->whereHas('enrollment', function ($q) {
                    $q->where('user_id', Auth::id());
                })
                ->where('completed', true)
                ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->with('module:id,duration')
                ->get();

            $dayMap = [
                1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 0 => 'Min'
            ];

            foreach ($learningProgress as $record) {
                $day = $record->updated_at->dayOfWeek;
                $dayName = $dayMap[$day] ?? null;
                if (!$dayName) {
                    continue;
                }
                $durationHours = ($record->module->duration ?? 0) / 3600;
                $dailyLearning[$dayName] += $durationHours;
            }

            foreach ($dailyLearning as $day => $value) {
                $dailyLearning[$day] = (float) number_format($value, 1);
            }

            $learningChartData = array_values($dailyLearning);
        }

        // Get popular topics (categories with most enrollments)
        $popularTopics = \App\Models\Category::query()
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(4)
            ->get();

        return view('dashboard', [
            'upcomingEvents' => $upcomingEvents,
            'featuredCourses' => $featuredCourses,
            'userEvents' => $userEvents,
            'userEnrollments' => $userEnrollments,
            'learningChartData' => $learningChartData,
            'popularTopics' => $popularTopics,
            'materiList' => $materiList,
            'jenisList' => $jenisList,
            'dashboardCarousels' => $dashboardCarousels,
        ]);
    }
}
