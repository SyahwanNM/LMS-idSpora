<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\TrainerNotification;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Mail\EventPaymentRejectedMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use GuzzleHttp\Client;

class EventController extends Controller
{
    private function parseSpeakerNames(?string $speaker): array
    {
        if (!is_string($speaker) || trim($speaker) === '') {
            return [];
        }

        $rawNames = preg_split('/[,;\n\r]+/', $speaker) ?: [];

        return collect($rawNames)
            ->map(fn($name) => trim((string) $name))
            ->filter()
            ->unique(fn($name) => mb_strtolower($name))
            ->values()
            ->all();
    }

    private function resolveTrainerIdsFromSpeakers(?string $speaker): array
    {
        $names = $this->parseSpeakerNames($speaker);
        if (empty($names)) {
            return [];
        }

        $lowerNames = collect($names)
            ->map(fn($name) => mb_strtolower($name))
            ->values()
            ->all();

        return User::query()
            ->where('role', 'trainer')
            ->whereIn('id', function ($query) use ($lowerNames) {
                $query->select('id')
                    ->from('users')
                    ->whereIn(\DB::raw('LOWER(name)'), $lowerNames);
            })
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function resolveAssignedTrainerIds(?int $trainerId, ?string $speaker): array
    {
        $ids = [];

        if (!empty($trainerId)) {
            $ids[] = (int) $trainerId;
        }

        $ids = array_merge($ids, $this->resolveTrainerIdsFromSpeakers($speaker));

        return collect($ids)
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();
    }

    private function notifyTrainerEventInvitation(Event $event, int $trainerId, string $source = 'trainer_id'): void
    {
        $trainer = User::query()
            ->where('id', $trainerId)
            ->where('role', 'trainer')
            ->first();

        if (!$trainer) {
            return;
        }

        TrainerNotification::create([
            'trainer_id' => $trainer->id,
            'type' => 'event_invitation',
            'title' => 'Undangan Menjadi Narasumber Event',
            'message' => 'Anda diundang menjadi narasumber untuk event "' . $event->title . '".',
            'data' => [
                'entity_type' => 'event',
                'entity_id' => $event->id,
                'url' => route('trainer.events.show', $event->id),
                'invitation_status' => 'pending',
                'invitation_source' => $source,
                'due_at' => now()->addDays(7)->toIso8601String(),
            ],
        ]);
    }

    public function index()
    {
        $threshold = now()->subHours(6)->format('Y-m-d H:i:s');
        $events = Event::query()
            ->where(function ($q) use ($threshold) {
                $q->whereNull('event_date')
                    ->orWhereRaw("TIMESTAMP(event_date, COALESCE(event_time,'00:00:00')) >= ?", [$threshold]);
            })
            ->latest()
            ->paginate(10);
        $events = Event::query()->latest()->paginate(10);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        // Show Add Event modal UI with ALL events list (active + finished) for full filtering in the UI
        $events = Event::query()->latest()->paginate(10);
        $materiOptions = Event::query()->whereNotNull('materi')->distinct()->orderBy('materi')->pluck('materi');
        $jenisOptions = Event::query()->whereNotNull('jenis')->distinct()->orderBy('jenis')->pluck('jenis');
        return view('admin.add-event', compact('events', 'materiOptions', 'jenisOptions'));
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
            'trainer_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereRaw('LOWER(role) = ?', ['trainer']);
                }),
            ],
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

        // Normalize path (remove 'storage/' prefix if exists)
        // Method store() returns path like 'events/filename.png' (relative to public disk)
        // We ensure it's stored as 'events/filename.png' in database
        $imagePath = ltrim(str_replace('storage/', '', $imagePath), '/');

        // Ensure path starts with 'events/' for consistency
        if (!str_starts_with($imagePath, 'events/')) {
            $imagePath = 'events/' . basename($imagePath);
        }

        // Normalisasi schedule
        $rawSchedule = $request->input('schedule', []);
        $scheduleRows = [];
        foreach ((array) $rawSchedule as $row) {
            if (!is_array($row))
                continue;
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
        foreach ((array) $rawExpenses as $row) {
            if (!is_array($row))
                continue;
            $item = trim($row['item'] ?? '');
            $qty = (float) ($row['quantity'] ?? 0);
            $unit = (float) ($row['unit_price'] ?? 0);
            if ($item) {
                $total = max(0, $qty) * max(0, $unit);
                $expenseRows[] = [
                    'item' => $item,
                    'quantity' => (int) $qty,
                    'unit_price' => (int) $unit,
                    'total' => (int) $total,
                ];
            }
        }

        // Simpan data ke database
        $event = Event::create([
            'trainer_id' => $request->input('trainer_id') ?: null,
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

        $assignedTrainerIds = $this->resolveAssignedTrainerIds(
            !empty($event->trainer_id) ? (int) $event->trainer_id : null,
            $event->speaker
        );
        foreach ($assignedTrainerIds as $trainerId) {
            $source = ((int) ($event->trainer_id ?? 0) === (int) $trainerId) ? 'trainer_id' : 'speaker_match';
            $this->notifyTrainerEventInvitation($event, $trainerId, $source);
        }

        // Persist relational schedule items & expenses for analytics / future queries
        if (!empty($scheduleRows)) {
            foreach ($scheduleRows as $row) {
                $event->scheduleItems()->create([
                    'start' => $row['start'] ?: null,
                    'end' => $row['end'] ?: null,
                    'title' => $row['title'] ?: null,
                    'description' => $row['description'] ?: null,
                ]);
            }
        }
        if (!empty($expenseRows)) {
            foreach ($expenseRows as $row) {
                $event->expenses()->create([
                    'item' => $row['item'],
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'total' => $row['total'],
                ]);
            }
        }

        // Generate one-time attendance QR (only once per event)
        try {
            if (empty($event->attendance_qr_token) && empty($event->attendance_qr_image)) {
                $token = bin2hex(random_bytes(16));
                $content = url('/events/' . $event->id . '?t=' . $token);
                // Try PNG first; if GD not available, fallback to SVG
                $png = null;
                $svg = null;
                $filename = null;
                try {
                    if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                        $png = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(600)->margin(1)->generate($content);
                    }
                } catch (\Throwable $e) {
                    $png = null;
                }
                if ($png) {
                    $filename = 'events/qr/event-' . $event->id . '-qr.png';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $png);
                } else {
                    // Attempt SVG generation as a reliable fallback
                    try {
                        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                            $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(600)->margin(1)->generate($content);
                        }
                    } catch (\Throwable $e) {
                        $svg = null;
                    }
                    if ($svg) {
                        $filename = 'events/qr/event-' . $event->id . '-qr.svg';
                        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $svg);
                    } else {
                        // Final minimal PNG to avoid errors
                        $filename = 'events/qr/event-' . $event->id . '-qr.png';
                        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/58BAgMDAv8x2WQAAAAASUVORK5CYII=');
                        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $png);
                    }
                }
                $event->attendance_qr_token = $token;
                $event->attendance_qr_image = $filename;
                $event->attendance_qr_generated_at = now();
                $event->save();
            }
        } catch (\Throwable $e) { /* ignore QR errors */
        }

        // If the newly created event is already finished based on end time, pre-select the Finished filter
        $statusFilter = $event->isFinished() ? 'finished' : null;

        return redirect()
            ->route('admin.add-event')
            ->with('success', 'Event berhasil ditambahkan!')
            ->with('statusFilter', $statusFilter);
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
        $previousAssignedTrainerIds = $this->resolveAssignedTrainerIds(
            !empty($event->trainer_id) ? (int) $event->trainer_id : null,
            $event->speaker
        );

        $request->validate([
            'title' => 'required|string|max:255',
            'trainer_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereRaw('LOWER(role) = ?', ['trainer']);
                }),
            ],
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
            'title',
            'trainer_id',
            'speaker',
            'manage_action',
            'materi',
            'jenis',
            'short_description',
            'description',
            'benefit',
            'terms_and_conditions',
            'location',
            'maps_url',
            'latitude',
            'longitude',
            'zoom_link',
            'price',
            'discount_percentage',
            'discount_until',
            'event_date',
            'event_time',
            'event_time_end'
        ]);

        if (array_key_exists('trainer_id', $data) && empty($data['trainer_id'])) {
            $data['trainer_id'] = null;
        }

        // Normalisasi schedule (update)
        $rawSchedule = $request->input('schedule', []);
        $scheduleRows = [];
        foreach ((array) $rawSchedule as $row) {
            if (!is_array($row))
                continue;
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
        foreach ((array) $rawExpenses as $row) {
            if (!is_array($row))
                continue;
            $item = trim($row['item'] ?? '');
            $qty = (float) ($row['quantity'] ?? 0);
            $unit = (float) ($row['unit_price'] ?? 0);
            if ($item) {
                $total = max(0, $qty) * max(0, $unit);
                $expenseRows[] = [
                    'item' => $item,
                    'quantity' => (int) $qty,
                    'unit_price' => (int) $unit,
                    'total' => (int) $total,
                ];
            }
        }
        $data['expenses_json'] = $expenseRows;

        // Jika ada gambar baru, simpan ke storage
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            // Normalize path (remove 'storage/' prefix if exists)
            // Method store() returns path like 'events/filename.png' (relative to public disk)
            // We ensure it's stored as 'events/filename.png' in database
            $imagePath = ltrim(str_replace('storage/', '', $imagePath), '/');

            // Ensure path starts with 'events/' for consistency
            if (!str_starts_with($imagePath, 'events/')) {
                $imagePath = 'events/' . basename($imagePath);
            }

            $data['image'] = $imagePath;

            // Delete old image if exists
            if ($event->image && !str_starts_with($event->image, 'http')) {
                $oldPath = str_replace('storage/', '', $event->image);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
        }

        $event->update($data);

        $currentAssignedTrainerIds = $this->resolveAssignedTrainerIds(
            !empty($event->trainer_id) ? (int) $event->trainer_id : null,
            $event->speaker
        );
        $newlyAssignedTrainerIds = array_values(array_diff($currentAssignedTrainerIds, $previousAssignedTrainerIds));
        foreach ($newlyAssignedTrainerIds as $trainerId) {
            $source = ((int) ($event->trainer_id ?? 0) === (int) $trainerId) ? 'trainer_id' : 'speaker_match';
            $this->notifyTrainerEventInvitation($event, (int) $trainerId, $source);
        }

        // Refresh relational schedule & expenses (simple replace strategy)
        $event->scheduleItems()->delete();
        foreach ($scheduleRows as $row) {
            $event->scheduleItems()->create([
                'start' => $row['start'] ?: null,
                'end' => $row['end'] ?: null,
                'title' => $row['title'] ?: null,
                'description' => $row['description'] ?: null,
            ]);
        }
        $event->expenses()->delete();
        foreach ($expenseRows as $row) {
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
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        $existing = EventRegistration::where('user_id', $user->id)->where('event_id', $event->id)->first();
        if ($existing) {
            if ($existing->status === 'rejected') {
                // Allow re-registration: delete old rejected record
                $existing->delete();
            } else {
                return response()->json([
                    'status' => 'already',
                    'message' => 'Sudah terdaftar',
                    'event_title' => $event->title,
                    'redirect' => route('events.show', $event)
                ]);
            }
        }
        // Hitung final price (sesudah diskon bila ada)
        $finalPrice = $event->hasDiscount() ? $event->discounted_price : $event->price;
        $isFree = (int) $finalPrice === 0;

        if (!$isFree) {
            // Event berbayar: jangan langsung daftar; arahkan ke payment
            return response()->json([
                'status' => 'payment_required',
                'message' => 'Pembayaran diperlukan sebelum pendaftaran dikonfirmasi.',
                'redirect' => route('payment', $event)
            ], 200);
        }

        // Event gratis: langsung daftarkan
        if ($isFree) {
            $reg = EventRegistration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'active',
                'registration_code' => 'EVT-' . strtoupper(uniqid()),
                'total_price' => 0.00,
            ]);

            // Track in Finance (Amount 0)
            \App\Models\ManualPayment::create([
                'event_id' => $event->id,
                'event_registration_id' => $reg->id,
                'user_id' => $user->id,
                'order_id' => $reg->registration_code,
                'amount' => 0,
                'currency' => 'IDR',
                'method' => 'free',
                'status' => 'settled',
                'metadata' => ['source' => 'event', 'type' => 'free']
            ]);
        } else {
            // Paid event: create pending registration and accept uploaded proof if provided
            $regData = [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'pending',
                'registration_code' => 'EVT-' . strtoupper(uniqid()),
                'total_price' => $event->discounted_price ?? $event->price,
            ];
            // handle proof upload if present (this API used by web/mobile)
            if ($request->hasFile('payment_proof')) {
                $file = $request->file('payment_proof');
                if ($file->isValid()) {
                    $path = $file->store('payments', 'public');
                    $regData['payment_proof'] = $path;
                }
            }
            $reg = EventRegistration::create($regData);
        }

        // Add points for event registration
        try {
            $pointsService = app(\App\Services\UserPointsService::class);
            $pointsService->addEventPoints($user, $event, $reg);
        } catch (\Throwable $e) { /* ignore */
        }

        // Create notification (expires in 14 days)
        try {
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'event_registration',
                'title' => 'Pendaftaran Dikonfirmasi',
                'message' => 'Pendaftaran untuk "' . $event->title . '" telah dikonfirmasi.',
                'data' => ['url' => route('events.show', $event)],
                'expires_at' => now()->addDays(14),
            ]);
        } catch (\Throwable $e) { /* ignore */
        }
        return response()->json([
            'status' => 'ok',
            'message' => 'Berhasil daftar event (GRATIS)',
            'event_title' => $event->title,
            'button_text' => 'Anda Terdaftar',
            'redirect' => route('events.show', $event)
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

    // Admin: remind trainer(s) to upload event module
    public function remindModuleUpload(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        if (!empty($event->module_path)) {
            return back()->with('success', 'Module sudah diupload.');
        }

        $speaker = trim((string) ($event->speaker ?? ''));
        if ($speaker === '') {
            return back()->with('error', 'Tidak ada pembicara yang terdaftar pada event ini.');
        }

        $parts = preg_split('/\s*[,;]+\s*/', $speaker) ?: [];
        $names = array_values(array_unique(array_filter(array_map('trim', $parts))));
        if (empty($names)) {
            return back()->with('error', 'Tidak dapat membaca nama pembicara.');
        }

        $trainers = User::query()
            ->where('role', 'trainer')
            ->whereIn('name', $names)
            ->get();

        if ($trainers->isEmpty()) {
            return back()->with('error', 'Trainer tidak ditemukan untuk pembicara: '.implode(', ', $names));
        }

        $sent = 0;
        foreach ($trainers as $t) {
            UserNotification::create([
                'user_id' => $t->id,
                'type' => 'trainer_module_reminder',
                'title' => 'Ingat Upload Module Event',
                'message' => 'Mohon upload module/materi untuk event "'.$event->title.'".',
                'data' => ['url' => route('trainer.events.modules')],
                'expires_at' => now()->addDays(14),
            ]);
            $sent++;
        }

        return back()->with('success', 'Reminder terkirim ke '.$sent.' trainer.');
    }

    /**
     * Admin: approve trainer module submission for an event.
     * Once approved, module_path will be set and document completeness will increase.
     */
    public function approveModule(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        $submission = trim((string) ($event->module_submission_path ?? ''));
        if ($submission === '') {
            return back()->with('error', 'Tidak ada module yang menunggu verifikasi.')
                ->with('module_error', 'Tidak ada module yang menunggu verifikasi.');
        }

        $event->update([
            'module_path' => $submission,
            'module_submission_path' => null,
            'module_verified_at' => now(),
            'module_verified_by' => auth()->id(),
            'module_rejected_at' => null,
            'module_rejected_by' => null,
            'module_rejection_reason' => null,
        ]);

        return back()->with('success', 'Module trainer berhasil diverifikasi.')
            ->with('module_success', 'Module trainer berhasil diverifikasi.');
    }

    /**
     * Admin: reject trainer module submission with a reason.
     */
    public function rejectModule(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        $submission = trim((string) ($event->module_submission_path ?? ''));
        if ($submission === '') {
            return back()->with('error', 'Tidak ada module yang menunggu verifikasi.')
                ->with('module_error', 'Tidak ada module yang menunggu verifikasi.');
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $event->update([
            'module_rejected_at' => now(),
            'module_rejected_by' => auth()->id(),
            'module_rejection_reason' => $validated['reason'],
            'module_verified_at' => null,
            'module_verified_by' => null,
        ]);

        return back()->with('success', 'Module trainer ditolak.')
            ->with('module_success', 'Module trainer ditolak.');
    }

    // Admin: download event QR image
    public function downloadQr(Event $event)
    {
        $path = (string) $event->attendance_qr_image;
        if (!$path)
            abort(404, 'QR image not available');
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        if (!$disk->exists($path)) {
            // Fallback: try public/storage
            $alt = public_path('storage/' . ltrim($path, '/'));
            if (!is_file($alt))
                abort(404, 'QR image file missing');
            $ext = strtolower(pathinfo($alt, PATHINFO_EXTENSION));
            $downloadName = 'event-' . $event->id . '-qr.' . ($ext ?: 'png');
            return response()->download($alt, $downloadName);
        }
        $full = $disk->path($path);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $downloadName = 'event-' . $event->id . '-qr.' . ($ext ?: 'png');
        return response()->download($full, $downloadName);
    }

    // Admin: generate or regenerate event attendance QR
    public function generateQr(Event $event)
    {
        try {
            // Generate new token and QR image (PNG preferred, SVG fallback)
            $token = bin2hex(random_bytes(16));
            $content = url('/events/' . $event->id . '?t=' . $token);
            $png = null;
            $svg = null;
            $filename = null;
            try {
                if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                    $png = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(600)->margin(1)->generate($content);
                }
            } catch (\Throwable $e) {
                $png = null;
            }
            if ($png) {
                $filename = 'events/qr/event-' . $event->id . '-qr.png';
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $png);
            } else {
                try {
                    if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(600)->margin(1)->generate($content);
                    }
                } catch (\Throwable $e) {
                    $svg = null;
                }
                if ($svg) {
                    $filename = 'events/qr/event-' . $event->id . '-qr.svg';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $svg);
                } else {
                    $filename = 'events/qr/event-' . $event->id . '-qr.png';
                    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/58BAgMDAv8x2WQAAAAASUVORK5CYII=');
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $png);
                }
            }
            $event->attendance_qr_token = $token;
            $event->attendance_qr_image = $filename;
            $event->attendance_qr_generated_at = now();
            $event->save();
            return back()->with('success', 'QR Absensi berhasil digenerate.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal generate QR Absensi.');
        }
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
        if (!preg_match('/(^|\.)((maps\.app\.goo\.gl)|(goo\.gl)|(google\.com))$/i', $host)) {
            return response()->json(['message' => 'Domain tidak didukung. Gunakan link Google Maps.'], 422);
        }

        // Try to follow redirects and get final URL
        try {
            $client = new Client(['allow_redirects' => ['track_redirects' => true, 'max' => 5], 'http_errors' => false, 'timeout' => 8]);
            $res = $client->request('GET', $url);
            $history = $res->getHeader('X-Guzzle-Redirect-History');
            $finalUrl = empty($history) ? (string) $res->getUri() : end($history);
            $decoded = urldecode($finalUrl);
            // Parse coords with extended patterns
            $patterns = [
                '/@(-?\d+\.\d+),\s*(-?\d+\.\d+)/',
                '/[?&]q=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/',
                '/[?&]ll=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/',
                '/[?&]center=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/',
                '/\/place\/\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/',
            ];
            foreach ($patterns as $re) {
                if (preg_match($re, $decoded, $m)) {
                    $lat = (float) $m[1];
                    $lng = (float) $m[2];
                    return response()->json(['lat' => $lat, 'lng' => $lng]);
                }
            }
            // !3dLAT!4dLNG pattern
            if (preg_match('/!3d(-?\d+\.\d+)/', $decoded, $m3d) && preg_match('/!4d(-?\d+\.\d+)/', $decoded, $m4d)) {
                return response()->json(['lat' => (float) $m3d[1], 'lng' => (float) $m4d[1]]);
            }
            // Fallback: try to extract from body if any embeds
            $body = (string) $res->getBody();
            if (preg_match('/@(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $body, $m)) {
                return response()->json(['lat' => (float) $m[1], 'lng' => (float) $m[2]]);
            }
            // Generic fallback: first suitable lat/lng pair in body
            if (preg_match_all('/-?\d+\.\d+/', $body, $nums) && count($nums[0]) >= 2) {
                $lat = (float) $nums[0][0];
                $lng = (float) $nums[0][1];
                if (abs($lat) <= 90 && abs($lng) <= 180) {
                    return response()->json(['lat' => $lat, 'lng' => $lng]);
                }
            }
            return response()->json(['message' => 'Koordinat tidak ditemukan dari link.'], 422);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal memproses link.'], 422);
        }
    }

    /**
     * Admin: Approve a pending event registration (manual proof verification).
     */
    public function approveRegistration(Request $request, Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            return back()->with('error', 'Pendaftaran tidak ditemukan untuk event ini.');
        }
        $registration->status = 'active';
        $registration->payment_verified_at = now();
        $registration->payment_verified_by = $request->user() ? $request->user()->id : null;
        $registration->save();

        // mark any related manual payments as settled
        try {
            $manuals = \App\Models\ManualPayment::where('event_registration_id', $registration->id)->get();
            foreach ($manuals as $m) {
                if ($m->status !== 'settled') {
                    $m->status = 'settled';
                    $m->save();

                    // Process Referral Commission (10%)
                    if (!empty($m->referral_code)) {
                        $referrer = \App\Models\User::where('referral_code', $m->referral_code)->first();
                        if ($referrer && $referrer->id !== $m->user_id) {
                            $commissionAmount = $m->amount * 0.10; // 10% commission

                            $existingReferral = \App\Models\Referral::where('user_id', $referrer->id)
                                ->where('referred_user_id', $m->user_id)
                                ->where('description', 'Komisi Event: ' . $event->title)
                                ->first();

                            if (!$existingReferral && $commissionAmount > 0) {
                                \App\Models\Referral::create([
                                    'user_id' => $referrer->id,
                                    'referred_user_id' => $m->user_id,
                                    'amount' => $commissionAmount,
                                    'status' => 'paid',
                                    'description' => 'Komisi Event: ' . $event->title
                                ]);

                                $referrer->increment('wallet_balance', $commissionAmount);
                            }
                        }
                    }
                }
            }
        } catch (\Throwable $e) { /* ignore */
        }

        try {
            UserNotification::create([
                'user_id' => $registration->user_id,
                'type' => 'event_registration_verified',
                'title' => 'Pembayaran Diterima',
                'message' => 'Pembayaran Anda untuk event "' . $event->title . '" telah diverifikasi oleh admin. Pendaftaran Anda aktif.',
                'data' => ['event_id' => $event->id, 'registration_id' => $registration->id],
                'expires_at' => now()->addDays(14),
            ]);
        } catch (\Throwable $e) { /* ignore notification errors */
        }

        return back()->with('success', 'Pendaftaran berhasil diverifikasi dan diaktifkan.');
    }

    /**
     * Admin: Reject a pending event registration (manual proof verification).
     */
    public function rejectRegistration(Request $request, Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            return back()->with('error', 'Pendaftaran tidak ditemukan untuk event ini.');
        }

        $adminContactNumber = '+62 898-9260-731';
        $allowedReasons = [
            'Nominal pembayaran kurang',
            'Nominal pembayaran lebih',
            'Gambar bukti pembayaran blur/buram. Silahkan kirim ulang',
            'Pembayaran dinyatakan tidak valid',
        ];

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500', Rule::in($allowedReasons)],
        ]);

        $reason = $validated['reason'];

        $userFacingMessage = match ($reason) {
            'Nominal pembayaran kurang' => 'Nominal pembayaran Anda kurang dari yang seharusnya. Silahkan lakukan pembayaran tambahan (top up) dan kirim ulang bukti pembayaran. Anda dapat melakukan registrasi lagi sekarang.',
            'Nominal pembayaran lebih' => 'Nominal pembayaran Anda lebih dari yang seharusnya. Admin akan menghubungi Anda untuk meminta nomor rekening agar selisih dapat dikembalikan. Anda dapat melakukan registrasi lagi sekarang.',
            'Gambar bukti pembayaran blur/buram. Silahkan kirim ulang' => 'Gambar bukti pembayaran blur/buram. Silahkan kirim ulang bukti pembayaran yang lebih jelas. Anda dapat melakukan registrasi lagi sekarang.',
            'Pembayaran dinyatakan tidak valid' => 'Pembayaran dinyatakan tidak valid. Silahkan kontak admin IdSpora di nomor '.$adminContactNumber.'. Anda dapat melakukan registrasi lagi sekarang.',
            default => 'Pendaftaran Anda ditolak. Anda dapat melakukan registrasi lagi sekarang.',
        };

        $registration->status = 'rejected';
        $registration->rejection_reason = $reason;
        $registration->payment_verified_at = now();
        $registration->payment_verified_by = $request->user() ? $request->user()->id : null;
        $registration->save();

        // mark any related manual payments as rejected
        try {
            $manuals = \App\Models\ManualPayment::where('event_registration_id', $registration->id)->get();
            foreach ($manuals as $m) {
                $m->status = 'rejected';
                $m->note = $reason; // Save reason to manual payment note as well if applicable
                $m->save();
            }
        } catch (\Throwable $e) { /* ignore */
        }

        try {
            UserNotification::create([
                'user_id' => $registration->user_id,
                'type' => 'event_registration_rejected',
                'title' => 'Pendaftaran Ditolak',
                'message' => 'Pendaftaran Anda untuk event "' . $event->title . '" ditolak. Alasan: ' . $reason,
                'data' => ['event_id' => $event->id, 'registration_id' => $registration->id],
                'message' => 'Pendaftaran Anda untuk event "'.$event->title.'" ditolak. ' . $userFacingMessage,
                'data' => ['event_id' => $event->id, 'registration_id' => $registration->id, 'reason' => $reason],
                'expires_at' => now()->addDays(14),
            ]);
        } catch (\Throwable $e) { /* ignore notification errors */
        }

        // Send email to user (best-effort)
        try {
            $user = $registration->user;
            if ($user && !empty($user->email)) {
                Mail::to($user->email)->send(new EventPaymentRejectedMail(
                    userName: (string) ($user->name ?? 'User'),
                    eventTitle: (string) ($event->title ?? 'Event'),
                    reason: (string) $reason,
                    messageText: (string) $userFacingMessage,
                    adminContactNumber: (string) $adminContactNumber,
                ));
            }
        } catch (\Throwable $e) { /* ignore mail errors */ }

        return back()->with('success', 'Pendaftaran ditolak dengan alasan yang diberikan.');
    }
}