<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class EventController extends Controller
{
    public function index()
    {
        $threshold = now()->subHours(6)->format('Y-m-d H:i:s');
        $events = Event::query()
            ->where(function($q) use ($threshold){
                $q->whereNull('event_date')
                  ->orWhereRaw("TIMESTAMP(event_date, COALESCE(event_time,'00:00:00')) >= ?", [$threshold]);
            })
            ->latest()
            ->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        // Show Add Event modal UI with existing ACTIVE (not finished) events list + dynamic jenis/materi options
        $events = Event::active()->latest()->paginate(10);
        $materiOptions = Event::query()->whereNotNull('materi')->distinct()->orderBy('materi')->pluck('materi');
        $jenisOptions = Event::query()->whereNotNull('jenis')->distinct()->orderBy('jenis')->pluck('jenis');
        return view('admin.add-event', compact('events','materiOptions','jenisOptions'));
    }

    /**
     * History listing (finished events only).
     */
    public function history()
    {
        $events = Event::finished()->latest()->paginate(10);
        return view('admin.events-history', compact('events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'manage_action' => 'required|in:manage,create',
            // Relax validation so new dynamic materi/jenis values allowed
            'materi' => 'nullable|string|max:255',
            'jenis' => 'nullable|string|max:100',
            'short_description' => 'required|string',
            'description' => 'required',
            'terms_and_conditions' => 'nullable|string',
            'location' => 'required|string|max:255',
            'maps_url' => 'nullable|string|max:512',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'zoom_link' => 'nullable|url|max:255',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'discount_until' => 'nullable|date',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'event_time_end' => 'nullable',
            // Increase max image size to 5MB (5120 KB)
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'benefit' => 'nullable|string',
            'schedule' => 'nullable|array',
            'schedule.*.start' => 'nullable|string',
            'schedule.*.end' => 'nullable|string',
            'schedule.*.title' => 'nullable|string|max:255',
            'schedule.*.description' => 'nullable|string|max:500',
            'expenses' => 'nullable|array',
            'expenses.*.item' => 'nullable|string|max:255',
            'expenses.*.quantity' => 'nullable|numeric|min:0',
            'expenses.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        // Simpan gambar ke storage
        $imagePath = $request->file('image')->store('events', 'public');

        // Normalisasi schedule
        $rawSchedule = $request->input('schedule', []);
        $scheduleRows = [];
        foreach ((array)$rawSchedule as $row) {
            if (!is_array($row)) continue;
            $start = trim($row['start'] ?? '');
            $end = trim($row['end'] ?? '');
            $title = trim($row['title'] ?? '');
            $desc = trim($row['description'] ?? '');
            // Simpan hanya jika ada minimal title atau start
            if ($start || $title) {
                $scheduleRows[] = [
                    'start' => $start,
                    'end' => $end,
                    'title' => $title,
                    'description' => $desc,
                ];
            }
        }
        // Normalisasi expenses
        $rawExpenses = $request->input('expenses', []);
        $expenseRows = [];
        foreach ((array)$rawExpenses as $row) {
            if (!is_array($row)) continue;
            $item = trim($row['item'] ?? '');
            $qty = (float)($row['quantity'] ?? 0);
            $unit = (float)($row['unit_price'] ?? 0);
            if ($item) {
                $total = max(0, $qty) * max(0, $unit);
                $expenseRows[] = [
                    'item' => $item,
                    'quantity' => (int)$qty,
                    'unit_price' => (int)$unit,
                    'total' => (int)$total,
                ];
            }
        }

        // Simpan data ke database
        $event = Event::create([
            'title' => $request->title,
            'speaker' => $request->speaker,
            'manage_action' => $request->manage_action,
            'materi' => $request->materi,
            'jenis' => $request->jenis,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'benefit' => $request->benefit,
            'terms_and_conditions' => $request->terms_and_conditions,
            'location' => $request->location,
            'maps_url' => $request->maps_url,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'zoom_link' => $request->zoom_link,
            'price' => $request->price,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'discount_until' => $request->discount_until,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'event_time_end' => $request->event_time_end,
            'image' => $imagePath,
            'schedule_json' => $scheduleRows,
            'expenses_json' => $expenseRows,
        ]);

        // Persist relational schedule items & expenses for analytics / future queries
        if(!empty($scheduleRows)){
            foreach($scheduleRows as $row){
                $event->scheduleItems()->create([
                    'start' => $row['start'] ?: null,
                    'end' => $row['end'] ?: null,
                    'title' => $row['title'] ?: null,
                    'description' => $row['description'] ?: null,
                ]);
            }
        }
        if(!empty($expenseRows)){
            foreach($expenseRows as $row){
                $event->expenses()->create([
                    'item' => $row['item'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $row['total'],
                ]);
            }
        }

    return redirect()->route('admin.add-event')->with('success', 'Event berhasil ditambahkan!');
    }

    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'speaker' => 'required|string|max:255',
            'manage_action' => 'required|in:manage,create',
            'materi' => 'nullable|string|max:255',
            'jenis' => 'nullable|string|max:100',
            'short_description' => 'required|string',
            'description' => 'required',
            'terms_and_conditions' => 'nullable|string',
            'location' => 'required|string|max:255',
            'maps_url' => 'nullable|string|max:512',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'zoom_link' => 'nullable|url|max:255',
            'price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'discount_until' => 'nullable|date',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'event_time_end' => 'nullable',
            // Increase max image size to 5MB (5120 KB)
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'benefit' => 'nullable|string',
            'schedule' => 'nullable|array',
            'schedule.*.start' => 'nullable|string',
            'schedule.*.end' => 'nullable|string',
            'schedule.*.title' => 'nullable|string|max:255',
            'schedule.*.description' => 'nullable|string|max:500',
            'expenses' => 'nullable|array',
            'expenses.*.item' => 'nullable|string|max:255',
            'expenses.*.quantity' => 'nullable|numeric|min:0',
            'expenses.*.unit_price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->only([
            'title', 'speaker', 'manage_action', 'materi', 'jenis', 'short_description', 'description', 'benefit', 'terms_and_conditions',
            'location', 'maps_url', 'latitude', 'longitude', 'zoom_link', 'price', 'discount_percentage', 'discount_until',
            'event_date', 'event_time', 'event_time_end'
        ]);

        // Normalisasi schedule (update)
        $rawSchedule = $request->input('schedule', []);
        $scheduleRows = [];
        foreach ((array)$rawSchedule as $row) {
            if (!is_array($row)) continue;
            $start = trim($row['start'] ?? '');
            $end = trim($row['end'] ?? '');
            $title = trim($row['title'] ?? '');
            $desc = trim($row['description'] ?? '');
            if ($start || $title) {
                $scheduleRows[] = [
                    'start' => $start,
                    'end' => $end,
                    'title' => $title,
                    'description' => $desc,
                ];
            }
        }
        $data['schedule_json'] = $scheduleRows;
        // Normalisasi expenses (update)
        $rawExpenses = $request->input('expenses', []);
        $expenseRows = [];
        foreach ((array)$rawExpenses as $row) {
            if (!is_array($row)) continue;
            $item = trim($row['item'] ?? '');
            $qty = (float)($row['quantity'] ?? 0);
            $unit = (float)($row['unit_price'] ?? 0);
            if ($item) {
                $total = max(0, $qty) * max(0, $unit);
                $expenseRows[] = [
                    'item' => $item,
                    'quantity' => (int)$qty,
                    'unit_price' => (int)$unit,
                    'total' => (int)$total,
                ];
            }
        }
        $data['expenses_json'] = $expenseRows;

        // Jika ada gambar baru, simpan ke storage
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        // Refresh relational schedule & expenses (simple replace strategy)
        $event->scheduleItems()->delete();
        foreach($scheduleRows as $row){
            $event->scheduleItems()->create([
                'start' => $row['start'] ?: null,
                'end' => $row['end'] ?: null,
                'title' => $row['title'] ?: null,
                'description' => $row['description'] ?: null,
            ]);
        }
        $event->expenses()->delete();
        foreach($expenseRows as $row){
            $event->expenses()->create([
                'item' => $row['item'],
                'quantity' => $row['quantity'],
                'unit_price' => $row['unit_price'],
                'total' => $row['total'],
            ]);
        }

        return redirect()->route('admin.add-event')->with('success', 'Event berhasil diupdate!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        // Redirect back to history page if the user came from there; otherwise to add-event
        $prev = url()->previous();
        $toHistory = is_string($prev) && str_contains($prev, '/admin/events/history');
        $route = $toHistory ? route('admin.events.history') : route('admin.add-event');
        return redirect($route)->with('success', 'Event berhasil dihapus!');
    }

    // Public registration (AJAX)
    public function register(Request $request, Event $event)
    {
        $request->validate([]); // no fields yet, just user context
        $user = $request->user();
        if(!$user){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $existing = EventRegistration::where('user_id',$user->id)->where('event_id',$event->id)->first();
        if($existing){
            return response()->json([
                'status' => 'already',
                'message' => 'Sudah terdaftar',
                'event_title' => $event->title,
                'redirect' => route('events.ticket', $event)
            ]);
        }
        // Hitung final price (sesudah diskon bila ada)
        $finalPrice = $event->hasDiscount() ? $event->discounted_price : $event->price;
        $isFree = (int)$finalPrice === 0;

        if(!$isFree){
            // Event berbayar: jangan langsung daftar; arahkan ke payment
            return response()->json([
                'status' => 'payment_required',
                'message' => 'Pembayaran diperlukan sebelum pendaftaran dikonfirmasi.',
                'redirect' => route('payment', $event)
            ], 200);
        }

        // Event gratis: langsung daftarkan
        $reg = EventRegistration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'active',
            'registration_code' => 'EVT-'.strtoupper(uniqid())
        ]);
        // Create notification (expires in 14 days)
        try{
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'event_registration',
                'title' => 'Pendaftaran Event Berhasil',
                'message' => 'Kamu terdaftar di "'.$event->title.'".',
                'data' => ['url' => route('events.show', $event)],
                'expires_at' => now()->addDays(14),
            ]);
        }catch(\Throwable $e){ /* ignore */ }
        return response()->json([
            'status' => 'ok',
            'message' => 'Berhasil daftar event (GRATIS)',
            'event_title' => $event->title,
            'button_text' => 'Anda Terdaftar',
            'redirect' => route('events.ticket', $event)
        ]);
    }

    // Admin: upload operational documents (VBG, certificate, attendance)
    public function uploadDocuments(Request $request, Event $event)
    {
        $validated = $request->validate([
            'virtual_background' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'certificate' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:8192',
            'attendance' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:8192',
        ]);

        $updates = [];
        if ($request->hasFile('virtual_background')) {
            $updates['vbg_path'] = $request->file('virtual_background')->store('events/docs', 'public');
        }
        if ($request->hasFile('certificate')) {
            $updates['certificate_path'] = $request->file('certificate')->store('events/docs', 'public');
        }
        if ($request->hasFile('attendance')) {
            $updates['attendance_path'] = $request->file('attendance')->store('events/docs', 'public');
        }

        if (!empty($updates)) {
            $event->update($updates);
        }

        return back()->with('success', 'Dokumen berhasil diperbarui.');
    }

    // Resolve Google Maps short links to coordinates (admin only)
    public function resolveMap(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:512'
        ]);
        $url = $request->input('url');
        // Basic allow-list for domains
        $host = parse_url($url, PHP_URL_HOST) ?: '';
        if(!preg_match('/(^|\.)((maps\.app\.goo\.gl)|(goo\.gl)|(google\.com))$/i', $host)){
            return response()->json(['message' => 'Domain tidak didukung. Gunakan link Google Maps.'], 422);
        }

        // Try to follow redirects and get final URL
        try {
            $client = new Client(['allow_redirects' => ['track_redirects' => true, 'max' => 5], 'http_errors' => false, 'timeout' => 8]);
            $res = $client->request('GET', $url);
            $history = $res->getHeader('X-Guzzle-Redirect-History');
            $finalUrl = empty($history) ? (string)$res->getUri() : end($history);
            $decoded = urldecode($finalUrl);
            // Parse coords with extended patterns
            $patterns = [
                '/@(-?\d+\.\d+),\s*(-?\d+\.\d+)/',
                '/[?&]q=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/',
                '/[?&]ll=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/',
                '/[?&]center=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/',
                '/\/place\/\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/',
            ];
            foreach($patterns as $re){
                if(preg_match($re, $decoded, $m)){
                    $lat = (float)$m[1];
                    $lng = (float)$m[2];
                    return response()->json(['lat' => $lat, 'lng' => $lng]);
                }
            }
            // !3dLAT!4dLNG pattern
            if(preg_match('/!3d(-?\d+\.\d+)/', $decoded, $m3d) && preg_match('/!4d(-?\d+\.\d+)/', $decoded, $m4d)){
                return response()->json(['lat' => (float)$m3d[1], 'lng' => (float)$m4d[1]]);
            }
            // Fallback: try to extract from body if any embeds
            $body = (string)$res->getBody();
            if(preg_match('/@(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $body, $m)){
                return response()->json(['lat' => (float)$m[1], 'lng' => (float)$m[2]]);
            }
            // Generic fallback: first suitable lat/lng pair in body
            if(preg_match_all('/-?\d+\.\d+/', $body, $nums) && count($nums[0]) >= 2){
                $lat = (float)$nums[0][0];
                $lng = (float)$nums[0][1];
                if(abs($lat) <= 90 && abs($lng) <= 180){
                    return response()->json(['lat' => $lat, 'lng' => $lng]);
                }
            }
            return response()->json(['message' => 'Koordinat tidak ditemukan dari link.'], 422);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal memproses link.'], 422);
        }
    }
}