<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Event;
use App\Models\Certificate;
use App\Models\DashboardMetric;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        // Middleware akan dihandle di level route
    }

    public function dashboard()
    {
        // Current totals
        $activeUsers = User::count(); // total accounts
        $totalCourses = Course::count();
        // Hitung hanya event aktif (belum lewat >= 6 jam sejak mulai)
        $threshold = now()->subHours(6)->format('Y-m-d H:i:s');
        $totalEvents = Event::query()
            ->where(function($q) use ($threshold){
                $q->whereNull('event_date')
                  ->orWhereRaw("TIMESTAMP(event_date, COALESCE(event_time,'00:00:00')) >= ?", [$threshold]);
            })
            ->count();
        $totalCertificates = Certificate::count();
        $totalRevenue = Course::sum('price') ?? 0; // Adjust if you have real transaction table

        // Snapshot logic (daily)
        $today = now()->startOfDay()->toDateString();
        $yesterdayDate = now()->subDay()->startOfDay()->toDateString();

        // Ensure today's snapshot exists (idempotent)
        DashboardMetric::firstOrCreate(
            ['snapshot_date' => $today],
            [
                'users_count' => $activeUsers,
                'courses_count' => $totalCourses,
                'events_count' => $totalEvents,
                'revenue_total' => $totalRevenue,
            ]
        );

    $todaySnapshot = DashboardMetric::where('snapshot_date', $today)->first();
    $yesterdaySnapshot = DashboardMetric::where('snapshot_date', $yesterdayDate)->first();

        // Helper closure to compute percent change
        $percentChange = function ($current, $previous) {
            if ($previous === null) return null; // No data
            if ($previous == 0) return $current > 0 ? 100 : 0; // Avoid division by zero
            return round((($current - $previous) / $previous) * 100, 1);
        };

        // Determine baseline (yesterday if exists, otherwise today's first snapshot for intra-day change)
        $usingIntraDayBaseline = false;
        if (!$yesterdaySnapshot) {
            $usingIntraDayBaseline = true;
        }

        $baselineUsers = $yesterdaySnapshot->users_count ?? ($todaySnapshot->users_count ?? null);
        $baselineCourses = $yesterdaySnapshot->courses_count ?? ($todaySnapshot->courses_count ?? null);
        $baselineEvents = $yesterdaySnapshot->events_count ?? ($todaySnapshot->events_count ?? null);
        $baselineRevenue = $yesterdaySnapshot->revenue_total ?? ($todaySnapshot->revenue_total ?? null);

        $activeUsersChangePercent = $percentChange($activeUsers, $baselineUsers);
        $totalCoursesChangePercent = $percentChange($totalCourses, $baselineCourses);
        $totalEventsChangePercent = $percentChange($totalEvents, $baselineEvents);
        $totalRevenueChangePercent = $percentChange($totalRevenue, $baselineRevenue);

        // Ambil 8 aktivitas terakhir (jika tabel sudah ada)
        if (Schema::hasTable('activity_logs')) {
            try {
                // Khusus menampilkan aktivitas login terbaru
                $recentActivities = ActivityLog::with('user')
                    ->where('action', 'like', 'Login%')
                    ->latest()
                    ->limit(8)
                    ->get()
                    ->map(function($log){
                        return [
                            'user' => $log->user?->name ?? 'System',
                            'action' => $log->action,
                            'time' => $log->created_at?->diffForHumans(),
                            'avatar' => $log->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($log->user?->name ?? 'System').'&background=6b7280&color=fff',
                            'description' => $log->description,
                        ];
                    });
            } catch (\Throwable $e) {
                $recentActivities = collect();
            }
        } else {
            $recentActivities = collect();
        }

        return view('admin.dashboard', compact(
            'activeUsers',
            'totalCourses',
            'totalEvents',
            'totalCertificates',
            'totalRevenue',
            'recentActivities',
            'activeUsersChangePercent',
            'totalCoursesChangePercent',
            'totalEventsChangePercent',
            'totalRevenueChangePercent',
            'usingIntraDayBaseline'
        ));
    }

    public function activeUsersCount()
    {
    // Return total accounts count
    $count = User::count();
        return response()->json(['count' => $count]);
    }

    /**
     * Return recent login activities as JSON for AJAX refresh
     */
    public function recentActivities(Request $request)
    {
        $limit = (int) $request->query('limit', 8);
        if (Schema::hasTable('activity_logs')) {
            try {
                $recentActivities = ActivityLog::with('user')
                    ->where('action', 'like', 'Login%')
                    ->latest()
                    ->limit($limit)
                    ->get()
                    ->map(function($log){
                        return [
                            'user' => $log->user?->name ?? 'System',
                            'action' => $log->action,
                            'time' => $log->created_at?->diffForHumans(),
                            'avatar' => $log->user?->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($log->user?->name ?? 'System').'&background=6b7280&color=fff',
                            'description' => $log->description,
                        ];
                    });
            } catch (\Throwable $e) {
                return response()->json(['data' => []]);
            }
        } else {
            $recentActivities = collect();
        }

        return response()->json(['data' => $recentActivities]);
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'level' => 'required|in:beginner,intermediate,advanced',
            'price' => 'required|integer|min:0',
            'duration' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image upload
        $imagePath = $request->file('image')->store('courses', 'public');

        // Create course
        $course = Course::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'level' => $request->level,
            'price' => $request->price,
            'duration' => $request->duration,
            'image' => $imagePath,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Create Course',
            'description' => 'Course "'.$course->name.'" (#'.$course->id.') berhasil dibuat'
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Course created successfully!');
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle image upload
        $imagePath = $request->file('image')->store('events', 'public');

        // Create event
        $event = Event::create([
            'title' => $request->title,
            'speaker' => $request->speaker,
            'description' => $request->description,
            'location' => $request->location,
            'price' => $request->price,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'image' => $imagePath,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Create Event',
            'description' => 'Event "'.$event->title.'" (#'.$event->id.') berhasil dibuat'
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'Event created successfully!');
    }

    public function reports()
    {
        // Build revenue per event and basic finance overview for the report
        // Paid statuses based on Midtrans typical values
        $paidStatuses = ['settlement', 'capture', 'success'];

        // Sum revenue from manual payments (use settled manual payments)
        $revenueMap = \App\Models\ManualPayment::query()
            ->selectRaw('event_id, SUM(COALESCE(amount, 0)) as total')
            ->where('status', 'settled')
            ->groupBy('event_id')
            ->pluck('total', 'event_id');

        // Get events with participants count
        $events = \App\Models\Event::query()
            ->withCount('registrations')
            ->orderBy('event_date', 'asc')
            ->get();

        // Categorize events: upcoming, active, completed
        $now = Carbon::now();
        $activeCount = 0; $completedCount = 0; $upcomingCount = 0; $totalEventsAll = $events->count();
        foreach ($events as $e) {
            if (empty($e->event_date)) { continue; }
            $start = $e->start_at; // accessor from model combines date + time safely
            $end = $e->end_at;     // accessor from model handles fallback to endOfDay
            if ($start && $end) {
                if ($now->lt($start)) { $upcomingCount++; }
                else if ($now->gt($end)) { $completedCount++; }
                else { $activeCount++; }
            } else if ($start) {
                if ($now->lt($start)) { $upcomingCount++; }
                else { $activeCount++; }
            }
        }
        $percentCompleted = $totalEventsAll > 0 ? round(($completedCount / $totalEventsAll) * 100) : 0;
        $percentNotCompleted = $totalEventsAll > 0 ? round((($totalEventsAll - $completedCount) / $totalEventsAll) * 100) : 0;

        // Map into simple rows for the Pendapatan table
        $eventRows = $events->map(function($e) use ($revenueMap){
            $price = $e->discounted_price ?? $e->price;
            $revenue = (float) ($revenueMap[$e->id] ?? 0);
            // Operational cost from DB: sum of EventExpense rows (accessor handles relation/json)
            $expense = (float) ($e->expenses_total ?? 0.0);
            $profit = $revenue - $expense;
            return [
                'id' => $e->id,
                'name' => $e->title,
                'date' => optional($e->event_date)->format('d/m/Y'),
                'participants' => (int) $e->registrations_count,
                'price' => (float) $price,
                'revenue' => $revenue,
                'expense' => $expense,
                'profit' => $profit,
            ];
        });

        // Build rows for Pertumbuhan table (growth metrics per event)
        $growthRows = $events->map(function($e){
            return [
                'id' => $e->id,
                'name' => $e->title,
                'date' => optional($e->event_date)->format('d/m/Y'),
                'participants' => (int) $e->registrations_count,
                'speaker' => $e->speaker,
                'event_rating' => null, // placeholder until rating source available
                'speaker_rating' => null, // placeholder
            ];
        });

        // Build rows for Operasional table (document completeness per event)
        $operationalRows = $events->map(function($e){
            return [
                'id' => $e->id,
                'name' => $e->title,
                'date' => optional($e->event_date)->format('d/m/Y'),
                'type' => $e->jenis ?? 'N/A',
                'documents_percent' => $e->documents_completion_percent, // accessor from model
                'has_vbg' => !empty($e->vbg_path),
                'has_cert' => !empty($e->certificate_path),
                'has_abs' => !empty($e->attendance_path),
            ];
        });

        return view('admin.reports', compact(
            'eventRows',
            'growthRows',
            'operationalRows',
            'activeCount',
            'completedCount',
            'upcomingCount',
            'percentCompleted',
            'percentNotCompleted'
        ));
    }

    // ========== Profile ==========
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'bio' => 'nullable|string|max:1000'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->bio = $validated['bio'];
        if(!empty($validated['password'])){
            $user->password = Hash::make($validated['password']);
        }
        $user->save();
        return redirect()->route('admin.profile')->with('success','Profil berhasil diperbarui');
    }

    // ========== Settings (Simple file-based storage) ==========
    protected function settingsFile(): string
    {
        return storage_path('app/admin_settings.json');
    }

    protected function loadSettings(): array
    {
        $file = $this->settingsFile();
        if(!file_exists($file)) return [
            'maintenance_mode' => false,
            'primary_color' => '#f59e0b',
            'allow_registration' => true,
        ];
        $json = json_decode(file_get_contents($file), true);
        if(!is_array($json)) return [];
        return $json;
    }

    protected function saveSettings(array $data): void
    {
        @file_put_contents($this->settingsFile(), json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }

    public function settings()
    {
        $settings = $this->loadSettings();
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'maintenance_mode' => 'sometimes|boolean',
            'primary_color' => 'required|regex:/^#?[0-9A-Fa-f]{6}$/',
            'allow_registration' => 'sometimes|boolean'
        ]);

        $settings = $this->loadSettings();
        $settings['maintenance_mode'] = (bool)$request->boolean('maintenance_mode');
        $color = $validated['primary_color'];
        if(strpos($color,'#') !== 0){ $color = '#'.$color; }
        $settings['primary_color'] = $color;
        $settings['allow_registration'] = (bool)$request->boolean('allow_registration');
        $this->saveSettings($settings);
        return redirect()->route('admin.settings')->with('success','Pengaturan tersimpan');
    }

    /**
     * Export selected data (users, courses, events) to a single CSV file.
     * Simple streaming CSV to avoid memory spikes.
     */
    public function exportData(Request $request): StreamedResponse
    {
        $fileName = 'export-idspora-'.now()->format('Ymd_His').'.xls'; // .xls so Excel opens HTML table nicely

        $response = new StreamedResponse(function() {
            // Start HTML document (Excel can parse this legacy HTML table format)
            echo '<html><head><meta charset="UTF-8" />';
            echo '<style>
                body{font-family:Arial, Helvetica, sans-serif; font-size:12px;}
                h2{background:#f59e0b;color:#fff;padding:6px 10px;border-radius:4px;font-size:14px;}
                table{border-collapse:collapse;width:100%;margin:12px 0;}
                th,td{border:1px solid #d1d5db;padding:6px 8px;text-align:left;vertical-align:top;}
                th{background:#f3f4f6;font-weight:600;}
                tbody tr:nth-child(even){background:#fafafa;}
                caption{caption-side:top;text-align:left;font-weight:600;margin-bottom:4px;color:#374151;}
            </style>';
            echo '</head><body>';
            echo '<h1 style="font-size:16px;margin:0 0 10px;">idSpora Data Export - '.e(now()->toDateTimeString()).'</h1>';

            // USERS TABLE
            echo '<h2>Users</h2>';
            echo '<table><thead><tr>';
            $userHeaders = ['ID','Name','Email','Role','Created At'];
            foreach($userHeaders as $h){ echo '<th>'.e($h).'</th>'; }
            echo '</tr></thead><tbody>';
            User::orderBy('id')->chunk(300, function($chunk){
                foreach($chunk as $u){
                    echo '<tr>';
                    echo '<td>'.e($u->id).'</td>';
                    echo '<td>'.e($u->name).'</td>';
                    echo '<td>'.e($u->email).'</td>';
                    echo '<td>'.e($u->role).'</td>';
                    echo '<td>'.e(optional($u->created_at)->toDateTimeString()).'</td>';
                    echo '</tr>';
                }
                @ob_flush(); flush();
            });
            echo '</tbody></table>';

            // COURSES TABLE
            echo '<h2>Courses</h2>';
            echo '<table><thead><tr>';
            $courseHeaders = ['ID','Name','Category','Level','Price','Duration (h)','Created At'];
            foreach($courseHeaders as $h){ echo '<th>'.e($h).'</th>'; }
            echo '</tr></thead><tbody>';
            Course::with('category')->orderBy('id')->chunk(300, function($chunk){
                foreach($chunk as $c){
                    echo '<tr>';
                    echo '<td>'.e($c->id).'</td>';
                    echo '<td>'.e($c->name).'</td>';
                    echo '<td>'.e(optional($c->category)->name).'</td>';
                    echo '<td>'.e($c->level).'</td>';
                    echo '<td>'.e($c->price).'</td>';
                    echo '<td>'.e($c->duration).'</td>';
                    echo '<td>'.e(optional($c->created_at)->toDateTimeString()).'</td>';
                    echo '</tr>';
                }
                @ob_flush(); flush();
            });
            echo '</tbody></table>';

            // EVENTS TABLE
            echo '<h2>Events</h2>';
            echo '<table><thead><tr>';
            $eventHeaders = ['ID','Title','Speaker','Location','Price','Event Date','Event Time','Created At'];
            foreach($eventHeaders as $h){ echo '<th>'.e($h).'</th>'; }
            echo '</tr></thead><tbody>';
            Event::orderBy('id')->chunk(300, function($chunk){
                foreach($chunk as $e){
                    echo '<tr>';
                    echo '<td>'.e($e->id).'</td>';
                    echo '<td>'.e($e->title).'</td>';
                    echo '<td>'.e($e->speaker).'</td>';
                    echo '<td>'.e($e->location).'</td>';
                    echo '<td>'.e($e->price).'</td>';
                    echo '<td>'.e(optional($e->event_date)->format('Y-m-d')).'</td>';
                    echo '<td>'.e($e->event_time).'</td>';
                    echo '<td>'.e(optional($e->created_at)->toDateTimeString()).'</td>';
                    echo '</tr>';
                }
                @ob_flush(); flush();
            });
            echo '</tbody></table>';

            echo '<p style="font-size:11px;color:#6b7280;margin-top:24px;">Generated at '.e(now()->toDateTimeString()).' &middot; idSpora LMS</p>';
            echo '</body></html>';
        });

        $response->headers->set('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        $response->headers->set('Cache-Control', 'no-store, no-cache');
        return $response;
    }
}