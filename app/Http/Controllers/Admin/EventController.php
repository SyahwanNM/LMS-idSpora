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
use App\Mail\PaymentInvoiceMail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
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

    private function syncEventSpeakers(Event $event, array $speakerNames, array $salaries): void
    {
        // Delete existing and re-sync
        \App\Models\EventSpeaker::where('event_id', $event->id)->delete();

        foreach ($speakerNames as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') continue;

            $salary = (float) preg_replace('/[^\d.]/', '', (string) ($salaries[$i] ?? 0));

            // Try to find matching trainer
            $trainer = \App\Models\User::where('role', 'trainer')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->first();

            \App\Models\EventSpeaker::create([
                'event_id'   => $event->id,
                'trainer_id' => $trainer?->id,
                'name'       => $name,
                'salary'     => $salary,
                'order'      => $i,
            ]);
        }
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
            'invitation_status' => 'pending',
            'data' => [
                'entity_type' => 'event',
                'entity_id' => $event->id,
                'url' => route('trainer.events.show', $event->id),
                'invitation_status' => 'pending',
                'invitation_source' => $source,
                'due_at' => ($event->material_deadline ?: now()->addDays(7))->toIso8601String(),
                'material_deadline' => optional($event->material_deadline)->toIso8601String(),
            ],
        ]);
    }

    public function index()
    {
        return $this->create();
    }

    public function create()
    {
        // Show Add Event modal UI with ALL events list (active + finished) for full filtering in the UI
        $events = Event::query()
            ->with(['approvedTrainerModules.trainer', 'speakers'])
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->paginate(10);
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
        // Normalize price inputs (strip formatting like "1.000.000")
        foreach (['price', 'price_offline', 'price_online'] as $field) {
            $raw = $request->input($field, null);
            if (!is_null($raw)) {
                $clean = preg_replace('/\D/', '', (string) $raw);
                $request->merge([$field => $clean === '' ? 0 : (int) $clean]);
            }
        }

        // For hybrid events: derive base price as the minimum of offline/online
        $locMode = strtolower(trim((string) $request->input('location_mode', 'offline')));
        if ($locMode === 'hybrid') {
            $priceOffline = (int) $request->input('price_offline', 0);
            $priceOnline  = (int) $request->input('price_online', 0);
            $request->merge(['price' => min($priceOffline, $priceOnline)]);
        }

        // Derive location from place_name or location_mode if location is missing or empty
        if (!$request->has('location') || empty($request->input('location'))) {
            $pName = $request->input('place_name');
            if ($locMode === 'online') {
                $request->merge(['location' => 'Online']);
            } elseif (!empty($pName)) {
                $request->merge(['location' => $pName]);
            } else {
                $request->merge(['location' => $locMode ?: 'Online']);
            }
        }

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
            'location' => 'nullable|string|max:255',
            'maps_url' => 'nullable|string|max:512',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'zoom_link' => 'nullable|url|max:255',
            'price' => 'required|numeric|min:0',
            'price_offline' => 'nullable|numeric|min:0',
            'price_online' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'discount_until' => 'nullable|date',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'event_time_end' => 'nullable',
            'event_until_date' => 'nullable|date|after_or_equal:event_date',
            'event_until_time' => 'nullable',
            'material_deadline' => 'nullable|date|after_or_equal:today|before:event_date',
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

        // Allow hybrid events: maps_url and zoom_link may both be filled.
        // Normalize empty strings to null. Lat/lng only kept when maps_url is provided.
        $mapsUrl = trim((string) $request->input('maps_url', ''));
        $zoomLink = trim((string) $request->input('zoom_link', ''));
        $latitude = $mapsUrl !== '' ? $request->input('latitude') : null;
        $longitude = $mapsUrl !== '' ? $request->input('longitude') : null;

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
        // Determine reseller flag: prefer explicit hidden field, fallback to radio input
        $isReseller = null;
        if ($request->has('is_reseller_event')) {
            $isReseller = $request->boolean('is_reseller_event');
        } elseif ($request->has('is_reseller_event_radio')) {
            $isReseller = ((string) $request->input('is_reseller_event_radio')) === '1';
        } else {
            $isReseller = false;
        }

        $submittedSpeakers = collect((array) $request->input('speakers', []))
            ->map(fn($speaker) => trim((string) $speaker))
            ->filter()
            ->values();

        $normalizedSpeaker = trim((string) $request->input('speaker', ''));
        if ($normalizedSpeaker === '' && $submittedSpeakers->isNotEmpty()) {
            $normalizedSpeaker = $submittedSpeakers->implode('|');
        }

        $event = Event::create([
            'trainer_id' => $request->input('trainer_id') ?: null,
            'title' => $request->title,
            'speaker' => $normalizedSpeaker,
            'manage_action' => $request->manage_action,
            'materi' => $request->materi,
            'jenis' => $request->jenis,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'benefit' => $request->benefit,
            'terms_and_conditions' => $request->terms_and_conditions,
            'location' => $request->location,
            'maps_url' => $mapsUrl !== '' ? $mapsUrl : null,
            'latitude' => $mapsUrl !== '' ? $latitude : null,
            'longitude' => $mapsUrl !== '' ? $longitude : null,
            'zoom_link' => $zoomLink !== '' ? $zoomLink : null,
            'price' => $request->price,
            'price_offline' => $request->price_offline ?? null,
            'price_online' => $request->price_online ?? null,
            'max_participants' => $request->max_participants ? (int) $request->max_participants : null,
            'discount_percentage' => $request->discount_percentage ?? 0,
            'discount_until' => $request->discount_until,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'event_time_end' => $request->event_time_end,
            'event_until_date' => $request->event_until_date ?? null,
            'event_until_time' => $request->event_until_time ?? null,
            'material_deadline' => $request->material_deadline,
            'image' => $imagePath,
            'schedule_json' => $scheduleRows,
            'expenses_json' => $expenseRows,
            'is_reseller_event' => (bool) $isReseller,
        ]);

        $assignedTrainerIds = $this->resolveAssignedTrainerIds(
            !empty($event->trainer_id) ? (int) $event->trainer_id : null,
            $event->speaker
        );

        // Sync speakers with salaries
        $speakerNames = collect((array) $request->input('speakers', []))->map(fn($s) => trim((string) $s))->filter()->values()->all();
        $speakerSalaries = (array) $request->input('speaker_salaries', []);
        if (!empty($speakerNames)) {
            $this->syncEventSpeakers($event, $speakerNames, $speakerSalaries);
        }

        foreach ($assignedTrainerIds as $trainerId) {
            $source = ((int) ($event->trainer_id ?? 0) === (int) $trainerId) ? 'trainer_id' : 'speaker_match';
            $this->notifyTrainerEventInvitation($event, $trainerId, $source);
        }

        // Notify about zoom link if provided
        if (!empty($request->zoom_link)) {
            foreach ($assignedTrainerIds as $trainerId) {
                TrainerNotification::create([
                    'trainer_id' => (int) $trainerId,
                    'type' => 'event_zoom_link_shared',
                    'title' => 'Link Zoom Event Dibagikan',
                    'message' => 'The Zoom link for event "' . $event->title . '" telah disiapkan dan dibagikan kepada Anda.',
                    'data' => [
                        'entity_type' => 'event',
                        'entity_id' => (int) $event->id,
                        'zoom_link' => $request->zoom_link,
                        'url' => route('trainer.events.show', $event->id),
                    ],
                    'expires_at' => now()->addDays(30),
                ]);
            }
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

        // Auto-generate per-day QR codes (supports multi-day via event_until_date)
        try {
            app(\App\Services\EventDailyQrService::class)->ensureAllDailyQrs($event->fresh());
        } catch (\Throwable $e) {
            \Log::warning('Failed to auto-generate daily QRs', ['event_id' => $event->id, 'error' => $e->getMessage()]);
        }

        $statusFilter = $event->isFinished() ? 'finished' : null;

        return redirect()
            ->route('admin.add-event')
            ->with('success', 'Event created successfully!')
            ->with('statusFilter', $statusFilter);
    }

    public function show(Event $event)
    {
        // event_admin can only view their assigned events
        $user = auth()->user();
        if ($user->role === 'event_admin' && !$user->isEventAdmin($event->id)) {
            abort(403, 'You do not have access to this event.');
        }

        $event->load(['trainerModules.trainer', 'approvedTrainerModules.trainer']);

        // Lazy-generate per-day QRs if they don't exist yet (covers events created before this feature)
        try {
            $existingCount = \App\Models\EventDailyQr::where('event_id', $event->id)->count();
            if ($existingCount === 0 && !empty($event->event_date)) {
                app(\App\Services\EventDailyQrService::class)->ensureAllDailyQrs($event);
            }
        } catch (\Throwable $e) {
            // Non-critical — don't block the page
        }

        return view('admin.events.show', compact('event'));
    }

    public function getAttendanceStats(Event $event)
    {
        $user = auth()->user();
        if ($user->role === 'event_admin' && !$user->isEventAdmin($event->id)) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $totalActiveReg = $event->registrations()->where('status', 'active')->count();
        $dailyQrs = \App\Models\EventDailyQr::where('event_id', $event->id)->orderBy('day_number')->get();
        $daysData = [];
        $logs = [];

        if ($dailyQrs->isNotEmpty()) {
            foreach ($dailyQrs as $dqr) {
                $checkedInCount = \App\Models\EventDailyAttendance::where('event_daily_qr_id', $dqr->id)
                    ->whereHas('registration', function($q) {
                        $q->where('status', 'active');
                    })
                    ->count();
                $percent = $totalActiveReg > 0 ? round(($checkedInCount / $totalActiveReg) * 100) : 0;
                $daysData[] = [
                    'day_number' => $dqr->day_number,
                    'date' => \Carbon\Carbon::parse($dqr->qr_date)->format('d M Y'),
                    'date_raw' => \Carbon\Carbon::parse($dqr->qr_date)->format('Y-m-d'),
                    'checked_in' => $checkedInCount,
                    'total' => $totalActiveReg,
                    'percent' => $percent
                ];
            }

            // Fetch recent check-ins
            $recentAttendances = \App\Models\EventDailyAttendance::whereIn('event_registration_id', function($q) use ($event) {
                    $q->select('id')->from('event_registrations')->where('event_id', $event->id)->where('status', 'active');
                })
                ->with('registration.user')
                ->latest('scanned_at')
                ->limit(15)
                ->get();

            $logs = $recentAttendances->map(function($att) {
                return [
                    'id' => $att->id,
                    'name' => $att->registration->user->name ?? 'User',
                    'email' => $att->registration->user->email ?? '-',
                    'day_number' => $att->day_number,
                    'scanned_at' => $att->scanned_at->format('H:i:s'),
                    'date' => $att->scanned_at->format('d F Y')
                ];
            });
        } else {
            // Fallback for single day / legacy check-ins
            $checkedInCount = $event->registrations()->where('status', 'active')->where(function($q) {
                $q->whereNotNull('attended_at')
                  ->orWhere('attendance_status', 'yes');
            })->count();
            $percent = $totalActiveReg > 0 ? round(($checkedInCount / $totalActiveReg) * 100) : 0;
            $daysData[] = [
                'day_number' => 1,
                'date' => $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : '-',
                'date_raw' => $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : '',
                'checked_in' => $checkedInCount,
                'total' => $totalActiveReg,
                'percent' => $percent
            ];

            // Fetch recent check-ins based on event_registrations.attended_at
            $recentRegs = $event->registrations()
                ->where('status', 'active')
                ->whereNotNull('attended_at')
                ->with('user')
                ->latest('attended_at')
                ->limit(15)
                ->get();

            $logs = $recentRegs->map(function($reg) {
                return [
                    'id' => $reg->id,
                    'name' => $reg->user->name ?? 'User',
                    'email' => $reg->user->email ?? '-',
                    'day_number' => 1,
                    'scanned_at' => $reg->attended_at->format('H:i:s'),
                    'date' => $reg->attended_at->format('d F Y')
                ];
            });
        }

        return response()->json([
            'status' => 'success',
            'total_active_participants' => $totalActiveReg,
            'days' => $daysData,
            'logs' => $logs
        ]);
    }

    public function manualCheckIn(Event $event, EventRegistration $registration, Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'event_admin' && !$user->isEventAdmin($event->id)) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $request->validate([
            'day_number' => 'required|integer|min:1',
        ]);

        $dayNumber = (int) $request->day_number;

        // Try to find the daily QR record
        $dailyQr = \App\Models\EventDailyQr::where('event_id', $event->id)
            ->where('day_number', $dayNumber)
            ->first();

        if (!$dailyQr) {
            try {
                app(\App\Services\EventDailyQrService::class)->ensureAllDailyQrs($event);
                $dailyQr = \App\Models\EventDailyQr::where('event_id', $event->id)
                    ->where('day_number', $dayNumber)
                    ->first();
            } catch (\Throwable $e) {}
        }

        if ($dailyQr) {
            $qrDayDate = $dailyQr->qr_date instanceof \Carbon\Carbon
                ? $dailyQr->qr_date->format('Y-m-d')
                : \Carbon\Carbon::parse((string) $dailyQr->qr_date)->format('Y-m-d');

            // Check if already checked in
            $attendance = \App\Models\EventDailyAttendance::where('event_registration_id', $registration->id)
                ->where('day_number', $dayNumber)
                ->first();

            if (!$attendance) {
                \App\Models\EventDailyAttendance::create([
                    'event_registration_id' => $registration->id,
                    'event_daily_qr_id'     => $dailyQr->id,
                    'attendance_date'       => $qrDayDate,
                    'day_number'            => $dayNumber,
                    'scanned_at'            => \Carbon\Carbon::now(config('app.timezone')),
                ]);
            }
        } else {
            // Fallback: If still no dailyQr, just create the attendance directly if nullable (or fail gracefully)
            // Assuming event_daily_qr_id might be nullable or we just update the top-level status
            $attendance = \App\Models\EventDailyAttendance::where('event_registration_id', $registration->id)
                ->where('day_number', $dayNumber)
                ->first();
            
            if (!$attendance) {
                try {
                    \App\Models\EventDailyAttendance::create([
                        'event_registration_id' => $registration->id,
                        'event_daily_qr_id'     => null,
                        'attendance_date'       => \Carbon\Carbon::now(config('app.timezone'))->format('Y-m-d'),
                        'day_number'            => $dayNumber,
                        'scanned_at'            => \Carbon\Carbon::now(config('app.timezone')),
                    ]);
                } catch (\Exception $e) {
                    // Ignore if event_daily_qr_id is not nullable, it will rely on top-level status
                }
            }
        }

        // Always update the top-level registration status
        $registration->attendance_status = 'yes';
        if (empty($registration->attended_at)) {
            $registration->attended_at = \Carbon\Carbon::now(config('app.timezone'));
        }
        if (empty($registration->attendance_scan_qr)) {
            $registration->attendance_scan_qr = \Carbon\Carbon::now(config('app.timezone'));
        }
        $registration->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Kehadiran Hari ' . $dayNumber . ' berhasil dicatat secara manual.',
        ]);
    }

    public function cancelDailyAttendance(Event $event, EventRegistration $registration, Request $request)
    {
        $user = auth()->user();
        if ($user->role === 'event_admin' && !$user->isEventAdmin($event->id)) {
            return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);
        }

        $request->validate([
            'day_number' => 'required|integer|min:1',
        ]);

        $dayNumber = (int) $request->day_number;

        // Delete the daily attendance record directly
        \App\Models\EventDailyAttendance::where('event_registration_id', $registration->id)
            ->where('day_number', $dayNumber)
            ->delete();

        // Count remaining daily attendances
        $remainingCount = \App\Models\EventDailyAttendance::where('event_registration_id', $registration->id)->count();

        if ($remainingCount === 0) {
            // If no daily attendances left, revert top-level status
            $registration->attendance_status = 'no';
            $registration->attended_at = null;
            $registration->attendance_scan_qr = null;
            $registration->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kehadiran Hari ' . $dayNumber . ' berhasil dibatalkan.',
        ]);
    }

    public function edit(Event $event)
    {
        // event_admin cannot edit events
        if (auth()->user()?->role === 'event_admin') {
            abort(403, 'Event admin cannot edit events.');
        }

        $materiOptions = Event::query()->whereNotNull('materi')->distinct()->orderBy('materi')->pluck('materi');
        $jenisOptions = Event::query()->whereNotNull('jenis')->distinct()->orderBy('jenis')->pluck('jenis');
        return view('admin.events.edit', compact('event', 'materiOptions', 'jenisOptions'));
    }

    public function update(Request $request, Event $event)
    {
        if (auth()->user()?->role === 'event_admin') {
            abort(403, 'Event admin cannot edit events.');
        }

        $previousAssignedTrainerIds = $this->resolveAssignedTrainerIds(
            !empty($event->trainer_id) ? (int) $event->trainer_id : null,
            $event->speaker
        );

        $submittedSpeakers = collect((array) $request->input('speakers', []))
            ->map(fn($speaker) => trim((string) $speaker))
            ->filter()
            ->values();

        $normalizedSpeaker = trim((string) $request->input('speaker', ''));
        if ($normalizedSpeaker === '' && $submittedSpeakers->isNotEmpty()) {
            $normalizedSpeaker = $submittedSpeakers->implode('|');
        }

        $normalizedLocationMode = strtolower(trim((string) $request->input('location_mode', 'offline')));
        $normalizedPlaceName = trim((string) $request->input('place_name', ''));
        if ($normalizedPlaceName === '' && $normalizedLocationMode !== 'online') {
            $existingLocation = trim((string) $event->location);
            if ($existingLocation !== '' && strtolower($existingLocation) !== 'online') {
                $normalizedPlaceName = $existingLocation;
            }
        }

        $normalizedMapsUrl = trim((string) $request->input('maps_url', ''));
        $normalizedZoomLink = trim((string) $request->input('zoom_link', ''));
        if ($normalizedZoomLink !== '' && !preg_match('#^https?://#i', $normalizedZoomLink)) {
            $normalizedZoomLink = 'https://' . ltrim($normalizedZoomLink, '/');
        }

        $request->merge([
            'speaker' => $normalizedSpeaker,
            'location_mode' => $normalizedLocationMode,
            'place_name' => $normalizedPlaceName,
            'maps_url' => $normalizedMapsUrl,
            'zoom_link' => $normalizedZoomLink,
        ]);

        // Derive location for update as well
        if (!$request->has('location') || empty($request->input('location'))) {
            if ($normalizedLocationMode === 'online') {
                $request->merge(['location' => 'Online']);
            } elseif (!empty($normalizedPlaceName)) {
                $request->merge(['location' => $normalizedPlaceName]);
            } else {
                $request->merge(['location' => $normalizedLocationMode ?: 'Online']);
            }
        }

        // Normalize price inputs in case client sent formatted strings (e.g. "1.000.000").
        foreach (['price', 'price_offline', 'price_online'] as $field) {
            $raw = $request->input($field, null);
            if (!is_null($raw)) {
                $clean = preg_replace('/\D/', '', (string) $raw);
                $request->merge([$field => $clean === '' ? 0 : (int) $clean]);
            }
        }

        // For hybrid events: derive base price as the minimum of offline/online
        $updateLocMode = strtolower(trim((string) $request->input('location_mode', 'offline')));
        if ($updateLocMode === 'hybrid') {
            $priceOffline = (int) $request->input('price_offline', 0);
            $priceOnline  = (int) $request->input('price_online', 0);
            $request->merge(['price' => min($priceOffline, $priceOnline)]);
        }

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
            'location' => 'nullable|string|max:255',
            'maps_url' => 'nullable|string|max:512',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'zoom_link' => 'nullable|url|max:255',
            'price' => 'required|numeric|min:0',
            'price_offline' => 'nullable|numeric|min:0',
            'price_online' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'discount_percentage' => 'nullable|integer|min:0|max:100',
            'discount_until' => 'nullable|date',
            'event_date' => 'required|date',
            'event_time' => 'required',
            'event_time_end' => 'nullable',
            'event_until_date' => 'nullable|date|after_or_equal:event_date',
            'event_until_time' => 'nullable',
            'material_deadline' => 'nullable|date|after_or_equal:today|before:event_date',
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
            'price_offline',
            'price_online',
            'max_participants',
            'discount_percentage',
            'discount_until',
            'event_date',
            'event_time',
            'event_time_end',
            'event_until_date',
            'event_until_time',
            'material_deadline'
        ]);

        // Allow hybrid events: maps_url and zoom_link may both be filled.
        // Normalize empty strings to null. Lat/lng only kept when maps_url is provided.
        $mapsUrl = trim((string) $request->input('maps_url', ''));
        $zoomLink = trim((string) $request->input('zoom_link', ''));
        $data['maps_url'] = $mapsUrl !== '' ? $mapsUrl : null;
        $data['zoom_link'] = $zoomLink !== '' ? $zoomLink : null;
        // Normalize max_participants: null if empty/zero
        $data['max_participants'] = !empty($data['max_participants']) ? (int) $data['max_participants'] : null;
        $data['latitude'] = $mapsUrl !== '' ? $request->input('latitude') : null;
        $data['longitude'] = $mapsUrl !== '' ? $request->input('longitude') : null;

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

        // Ensure price is numeric (accept formatted strings)
        if (array_key_exists('price', $data)) {
            if (is_string($data['price'])) {
                $clean = preg_replace('/\D/', '', $data['price']);
                $data['price'] = $clean === '' ? 0 : (float) $clean;
            } else {
                $data['price'] = (float) $data['price'];
            }
        }

        $event->update($data);

        // Clear trainer_id if the previously assigned trainer is no longer in the speakers list
        $resolvedNewTrainerIds = $this->resolveTrainerIdsFromSpeakers($event->speaker);
        if ($event->trainer_id && !in_array((int) $event->trainer_id, $resolvedNewTrainerIds, true)) {
            $event->trainer_id = null;
            $event->save();
        }

        $currentAssignedTrainerIds = $this->resolveAssignedTrainerIds(
            !empty($event->trainer_id) ? (int) $event->trainer_id : null,
            $event->speaker
        );
        $newlyAssignedTrainerIds = array_values(array_diff($currentAssignedTrainerIds, $previousAssignedTrainerIds));

        // Sync speakers with salaries (call always to allow deletion)
        $speakerNames = collect((array) $request->input('speakers', []))->map(fn($s) => trim((string) $s))->filter()->values()->all();
        $speakerSalaries = (array) $request->input('speaker_salaries', []);
        $this->syncEventSpeakers($event, $speakerNames, $speakerSalaries);

        foreach ($newlyAssignedTrainerIds as $trainerId) {
            $source = ((int) ($event->trainer_id ?? 0) === (int) $trainerId) ? 'trainer_id' : 'speaker_match';
            $this->notifyTrainerEventInvitation($event, (int) $trainerId, $source);
        }

        // Notify about zoom link updates
        $previousZoomLink = (string) data_get($event->getRawOriginal(), 'zoom_link', '');
        $currentZoomLink = (string) data_get($data, 'zoom_link', '');
        if (!empty($currentZoomLink) && $previousZoomLink !== $currentZoomLink) {
            foreach ($currentAssignedTrainerIds as $trainerId) {
                TrainerNotification::create([
                    'trainer_id' => (int) $trainerId,
                    'type' => 'event_zoom_link_updated',
                    'title' => 'Zoom Link Updated',
                    'message' => 'The Zoom link for event "' . $event->title . '" has been updated.',
                    'data' => [
                        'entity_type' => 'event',
                        'entity_id' => (int) $event->id,
                        'zoom_link' => $currentZoomLink,
                        'url' => route('trainer.events.show', $event->id),
                    ],
                    'expires_at' => now()->addDays(30),
                ]);
            }
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

        // Sync per-day QRs (removes out-of-range, adds missing when dates changed)
        try {
            app(\App\Services\EventDailyQrService::class)->syncDailyQrs($event->fresh());
        } catch (\Throwable $e) {
            \Log::warning('Failed to sync daily QRs on update', ['event_id' => $event->id, 'error' => $e->getMessage()]);
        }

        return redirect()->route('admin.add-event')->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        if (auth()->user()?->role === 'event_admin') {
            abort(403, 'Event admin cannot delete events.');
        }

        $event->delete();
        // Redirect back to history page if the user came from there; otherwise to add-event
        $prev = url()->previous();
        $toHistory = is_string($prev) && str_contains($prev, '/admin/events/history');
        $route = $toHistory ? route('admin.events.history') : route('admin.add-event');
        return redirect($route)->with('success', 'Event deleted successfully!');
    }

    /**
     * Duplicate an event: copy all content fields, reset operational docs,
     * unpublished, no participants.
     */
    public function duplicate(Event $event)
    {
        $copy = $event->replicate([
            'is_published', 'published_at',
            'vbg_path', 'module_path', 'module_submission_path', 'certificate_path',
            'material_status', 'material_approved_at', 'material_approved_by', 'material_rejection_reason',
            'module_submitted_at', 'module_verified_at', 'module_verified_by',
            'module_rejected_at', 'module_rejected_by', 'module_rejection_reason',
            'attendance_qr_token', 'attendance_qr_image', 'attendance_qr_generated_at',
        ]);

        $copy->title                      = $event->title . ' (Copy)';
        $copy->is_published               = false;
        $copy->published_at               = null;
        $copy->vbg_path                   = null;
        $copy->module_path                = null;
        $copy->module_submission_path     = null;
        $copy->certificate_path           = null;
        $copy->material_status            = 'pending'; // NOT NULL, reset to default
        $copy->material_approved_at       = null;
        $copy->material_approved_by       = null;
        $copy->material_rejection_reason  = null;
        $copy->module_submitted_at        = null;
        $copy->module_verified_at         = null;
        $copy->module_verified_by         = null;
        $copy->module_rejected_at         = null;
        $copy->module_rejected_by         = null;
        $copy->module_rejection_reason    = null;
        $copy->attendance_qr_token        = null;
        $copy->attendance_qr_image        = null;
        $copy->attendance_qr_generated_at = null;
        $copy->save();

        // Copy schedule items
        foreach ($event->scheduleItems as $item) {
            $copy->scheduleItems()->create($item->only([
                'time_start', 'time_end', 'activity', 'speaker', 'order',
            ]));
        }

        // Copy expenses
        foreach ($event->expenses as $expense) {
            $copy->expenses()->create($expense->only([
                'item', 'quantity', 'unit_price', 'total', 'note',
            ]));
        }

        // Auto-generate attendance QR for the duplicated event
        $this->generateAttendanceQrForEvent($copy);

        return response()->json([
            'status'  => 'success',
            'message' => 'Event duplicated successfully.',
            'data'    => ['id' => $copy->id, 'title' => $copy->title],
        ], 201);
    }

    /**
     * Generate attendance QR code for an event (shared helper).
     */
    private function generateAttendanceQrForEvent(Event $event): void
    {
        try {
            $token    = bin2hex(random_bytes(16));
            $content  = url('/events/' . $event->id . '?t=' . $token);
            $filename = null;
            $png      = null;

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
                $svg = null;
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
                    $placeholder = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/58BAgMDAv8x2WQAAAAASUVORK5CYII=');
                    \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $placeholder);
                }
            }

            $event->attendance_qr_token        = $token;
            $event->attendance_qr_image        = $filename;
            $event->attendance_qr_generated_at = now();
            $event->save();
        } catch (\Throwable $e) {
            // QR generation is non-critical — do not fail the request
        }
    }

    /**
     * Admin: publish event so it becomes visible on user-facing pages.
     */
    public function publish(Request $request, Event $event)
    {
        if (auth()->user()?->role === 'event_admin') {
            abort(403, 'Event admin cannot publish events.');
        }

        if ((bool) $event->is_published) {
            return back()->with('success', 'Event is already published.');
        }

        // Prevent publishing if operational documents are incomplete
        $hasMapsLink   = !empty($event->maps_url);
        $hasZoomLink   = !empty($event->zoom_link);
        $isOfflineOnly = $hasMapsLink && !$hasZoomLink;
        $requiresVbg   = !$isOfflineOnly;
        $hasVbg        = !empty($event->vbg_path);
        // Module: cek module_path ATAU approved trainer modules
        $hasModule     = !empty($event->module_path)
                         || $event->approvedTrainerModules()->exists();
        $hasAbsFile    = !empty($event->attendance_path);
        $hasAbsQrImg   = !empty($event->attendance_qr_image);
        $hasAbsQrToken = !empty($event->attendance_qr_token);
        $hasAbs        = $hasAbsFile || $hasAbsQrImg || $hasAbsQrToken;

        $totalDocs     = $requiresVbg ? 3 : 2;
        $doneDocs      = ($requiresVbg ? ($hasVbg ? 1 : 0) : 0) + ($hasModule ? 1 : 0) + ($hasAbs ? 1 : 0);
        $pct           = $totalDocs > 0 ? ($doneDocs >= $totalDocs ? 100 : (int) floor(($doneDocs / $totalDocs) * 100)) : 0;

        if ($pct < 100) {
            $missing = [];
            if ($requiresVbg && !$hasVbg) $missing[] = 'Virtual Background';
            if (!$hasModule) $missing[] = 'Module (Trainer)';
            if (!$hasAbs) $missing[] = 'Absensi';

            $msg = 'Kelengkapan dokumen belum 100%.';
            if (!empty($missing)) {
                $msg .= ' Item yang belum lengkap: ' . implode(', ', $missing) . '.';
            }
            $msg .= ' Lengkapi dokumen sebelum menerbitkan.';

            return back()->with('error', $msg)->with('publish_missing_items', $missing);
        }

        $event->forceFill([
            'is_published' => true,
            'published_at' => now(),
        ])->save();

        return back()->with('success', 'Event published successfully!');
    }

    /**
     * Admin: unpublish event (batal publish).
     */
    public function unpublish(Request $request, Event $event)
    {
        if (auth()->user()?->role === 'event_admin') {
            abort(403, 'Event admin cannot unpublish events.');
        }

        if (!(bool) $event->is_published) {
            return back()->with('success', 'Event has not been published yet.');
        }

        $event->forceFill([
            'is_published' => false,
            'published_at' => null,
        ])->save();

        return back()->with('success', 'Event publication has been cancelled!');
    }

    // Public registration (AJAX)
    public function register(Request $request, Event $event)
    {
        $request->validate([]); // no fields yet, just user context

        // Block registrations for unpublished events
        if (!(bool) $event->is_published) {
            return response()->json([
                'status' => 'error',
                'message' => 'Event is not published.',
            ], 404);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Sync profile fields
        $profileUpdates = [];
        $fullName = $request->input('full_name') ?: $user->name;
        $email    = $request->input('email')     ?: $user->email;
        $phone    = $request->input('whatsapp')  ?: $user->phone;
        $university = $request->input('university_origin') ?: $user->institution;
        $position   = $request->input('position')          ?: $user->profession;

        if ($fullName !== $user->name)  $profileUpdates['name']  = $fullName;
        if ($email    !== $user->email) $profileUpdates['email'] = $email;
        if ($phone    !== $user->phone) $profileUpdates['phone'] = $phone;
        if ($university !== $user->institution) $profileUpdates['institution'] = $university;
        if ($position !== $user->profession)   $profileUpdates['profession']  = $position;

        if (!empty($profileUpdates)) {
            $user->update($profileUpdates);
            $user->refresh();
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
                'university_origin' => $request->input('university_origin'),
                'study_program'     => $request->input('study_program'),
                'position'          => $request->input('position'),
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
                'university_origin' => $request->input('university_origin'),
                'study_program'     => $request->input('study_program'),
                'position'          => $request->input('position'),
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
            'success' => true,
            'message' => 'Successfully registered for event (Free)',
            'event_title' => $event->title,
            'button_text' => 'Anda Terdaftar',
            'redirect' => route('events.registered.detail', $event)
        ]);
    }

    // Admin: upload operational documents (VBG, attendance)
    public function uploadDocuments(Request $request, Event $event)
    {
        $validated = $request->validate([
            'virtual_background' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'attendance' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:8192',
        ]);

        $updates = [];
        $notificationMessages = [];

        if ($request->hasFile('virtual_background')) {
            $updates['vbg_path'] = $request->file('virtual_background')->store('events/docs', 'public');
            $notificationMessages[] = 'Virtual background (VBG)';
        }
        if ($request->hasFile('attendance')) {
            $updates['attendance_path'] = $request->file('attendance')->store('events/docs', 'public');
            $notificationMessages[] = 'Absensi';
        }

        if (!empty($updates)) {
            $event->update($updates);

            // Notify trainer(s) about uploaded documents
            if (!empty($event->trainer_id)) {
                $trainer = User::query()->where('id', (int) $event->trainer_id)->where('role', 'trainer')->first();
                if ($trainer) {
                    TrainerNotification::create([
                        'trainer_id' => (int) $trainer->id,
                        'type' => 'event_documents_uploaded',
                        'title' => 'Dokumen Event Diunggah',
                        'message' => 'Admin telah mengunggah dokumen untuk event "' . $event->title . '": ' . implode(', ', $notificationMessages),
                        'data' => [
                            'entity_type' => 'event',
                            'entity_id' => (int) $event->id,
                            'url' => route('trainer.events.show', $event->id),
                        ],
                        'expires_at' => now()->addDays(30),
                    ]);
                }
            }
        }

        return back()->with('success', 'Document updated successfully.');
    }

    /**
     * Admin: approve trainer module submission for an event.
     * Supports per-trainer module via event_trainer_modules table.
     */
    public function approveModule(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        $moduleId = $request->input('module_id');

        if ($moduleId) {
            // Approve specific module
            $module = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            $module->update([
                'status'      => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'rejection_reason' => null,
            ]);

            // Notify the specific trainer
            try {
                \App\Models\TrainerNotification::create([
                    'trainer_id' => $module->trainer_id,
                    'type'       => 'event_material_approved',
                    'title'      => 'Materi Event Diterima',
                    'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title . '" telah disetujui.',
                    'data'       => [
                        'entity_type' => 'event',
                        'entity_id'   => (int) $event->id,
                        'url'         => route('trainer.events.show', $event->id),
                    ],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
        } else {
            // Legacy: approve all pending modules for this event
            $pending = \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('status', 'pending_review')
                ->get();

            if ($pending->isEmpty()) {
                return back()->with('error', 'Tidak ada module yang menunggu verifikasi.')
                    ->with('module_error', 'Tidak ada module yang menunggu verifikasi.');
            }

            foreach ($pending as $module) {
                $module->update([
                    'status'      => 'approved',
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'rejection_reason' => null,
                ]);

                try {
                    \App\Models\TrainerNotification::create([
                        'trainer_id' => $module->trainer_id,
                        'type'       => 'event_material_approved',
                        'title'      => 'Materi Event Diterima',
                        'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title . '" telah disetujui.',
                        'data'       => [
                            'entity_type' => 'event',
                            'entity_id'   => (int) $event->id,
                            'url'         => route('trainer.events.show', $event->id),
                        ],
                        'expires_at' => now()->addDays(30),
                    ]);
                } catch (\Throwable $e) {}
            }
        }

        // Update event-level material_status if all modules approved
        $stillPending = \App\Models\EventTrainerModule::where('event_id', $event->id)
            ->where('status', 'pending_review')->count();
        if ($stillPending === 0) {
            $event->update([
                'material_status'    => 'approved',
                'material_approved_at' => now(),
                'material_approved_by' => auth()->id(),
                'material_rejection_reason' => null,
                'module_verified_at' => now(),
                'module_verified_by' => auth()->id(),
                'module_rejected_at' => null,
                'module_rejected_by' => null,
                'module_rejection_reason' => null,
            ]);
        }

        return back()->with('success', 'Trainer module verified successfully.')
            ->with('module_success', 'Trainer module verified successfully.');
    }

    /**
     * Admin: reject trainer module submission with a reason.
     */
    public function rejectModule(Request $request, Event $event)
    {
        if (!auth()->check() || (auth()->user()->role ?? null) !== 'admin') {
            abort(403, 'Hanya admin yang dapat melakukan aksi ini.');
        }

        $validated = $request->validate([
            'reason'    => ['required', 'string', 'max:500'],
            'module_id' => ['nullable', 'integer'],
        ]);

        $moduleId = $validated['module_id'] ?? null;

        if ($moduleId) {
            $module = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->firstOrFail();

            $module->update([
                'status'           => 'rejected',
                'reviewed_by'      => auth()->id(),
                'reviewed_at'      => now(),
                'rejection_reason' => $validated['reason'],
            ]);

            try {
                \App\Models\TrainerNotification::create([
                    'trainer_id' => $module->trainer_id,
                    'type'       => 'event_material_rejected',
                    'title'      => 'Materi Event Ditolak',
                    'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title . '" ditolak. Alasan: ' . $validated['reason'],
                    'data'       => [
                        'entity_type'      => 'event',
                        'entity_id'        => (int) $event->id,
                        'url'              => route('trainer.events.show', $event->id),
                        'rejection_reason' => $validated['reason'],
                    ],
                    'expires_at' => now()->addDays(30),
                ]);
            } catch (\Throwable $e) {}
        } else {
            // Legacy: reject all pending
            $pending = \App\Models\EventTrainerModule::where('event_id', $event->id)
                ->where('status', 'pending_review')
                ->get();

            if ($pending->isEmpty()) {
                return back()->with('error', 'Tidak ada module yang menunggu verifikasi.')
                    ->with('module_error', 'Tidak ada module yang menunggu verifikasi.');
            }

            foreach ($pending as $module) {
                $module->update([
                    'status'           => 'rejected',
                    'reviewed_by'      => auth()->id(),
                    'reviewed_at'      => now(),
                    'rejection_reason' => $validated['reason'],
                ]);

                try {
                    \App\Models\TrainerNotification::create([
                        'trainer_id' => $module->trainer_id,
                        'type'       => 'event_material_rejected',
                        'title'      => 'Materi Event Ditolak',
                        'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title . '" ditolak. Alasan: ' . $validated['reason'],
                        'data'       => [
                            'entity_type'      => 'event',
                            'entity_id'        => (int) $event->id,
                            'url'              => route('trainer.events.show', $event->id),
                            'rejection_reason' => $validated['reason'],
                        ],
                        'expires_at' => now()->addDays(30),
                    ]);
                } catch (\Throwable $e) {}
            }

            $event->update([
                'material_status'           => 'rejected',
                'material_approved_at'      => null,
                'material_approved_by'      => null,
                'material_rejection_reason' => $validated['reason'],
                'module_verified_at'        => null,
                'module_verified_by'        => null,
                'module_rejected_at'        => now(),
                'module_rejected_by'        => auth()->id(),
                'module_rejection_reason'   => $validated['reason'],
            ]);
        }

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
            /** @var \App\Services\EventDailyQrService $qrService */
            $qrService = app(\App\Services\EventDailyQrService::class);

            // Check if a specific day is being regenerated (POST param: day_id)
            $dayId = request()->input('day_id');
            if ($dayId) {
                $dailyQr = \App\Models\EventDailyQr::where('id', $dayId)
                    ->where('event_id', $event->id)
                    ->firstOrFail();
                $qrService->regenerateDailyQr($dailyQr, $event);
                return back()->with('success', 'QR Hari ke-' . $dailyQr->day_number . ' berhasil di-regenerate.');
            }

            // Generate / ensure all daily QRs for the event
            $qrService->ensureAllDailyQrs($event);

            // Also keep legacy single QR (for backward compat with older scan views)
            if (empty($event->attendance_qr_token)) {
                $token   = bin2hex(random_bytes(16));
                $content = url('/events/' . $event->id . '?t=' . $token);
                $png = null;
                try {
                    if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
                        $png = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(600)->margin(1)->generate($content);
                    }
                } catch (\Throwable $e) {}

                $filename = 'events/qr/event-' . $event->id . '-qr.png';
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $png ?: base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/58BAgMDAv8x2WQAAAAASUVORK5CYII='));

                $event->attendance_qr_token        = $token;
                $event->attendance_qr_image        = $filename;
                $event->attendance_qr_generated_at = now();
                $event->save();
            }

            return back()->with('success', 'QR Attendance per hari berhasil di-generate.');
        } catch (\Throwable $e) {
            \Log::error('generateQr failed: ' . $e->getMessage());
            return back()->with('error', 'Gagal generate QR Absensi: ' . $e->getMessage());
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
    }    public function approveRegistration(Request $request, Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            return back()->with('error', 'Data tidak valid.');
        }

        $registration->update([
            'status'               => 'active',
            'payment_verified_at'  => now(),
            'payment_verified_by'  => auth()->id(),
            'rejection_reason'     => null,
        ]);

        // Update related ManualPayment
        \App\Models\ManualPayment::where('event_registration_id', $registration->id)
            ->where('status', 'pending')
            ->update(['status' => 'settled']);

        // Notify user
        try {
            \App\Models\UserNotification::create([
                'user_id'    => $registration->user_id,
                'type'       => 'event_registration_approved',
                'title'      => 'Pendaftaran Dikonfirmasi',
                'message'    => 'Pembayaran transfer Anda untuk event "' . ($event->title ?? 'Event') . '" telah dikonfirmasi.',
                'data'       => ['event_id' => $event->id, 'url' => route('events.show', $event)],
                'expires_at' => now()->addDays(14),
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success', 'Pendaftaran berhasil dikonfirmasi.');
    }

    /**
     * Admin: Reject a pending event registration (manual proof verification).
     */
    public function rejectRegistration(Request $request, Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            return back()->with('error', 'Data tidak valid.');
        }

        $reason = trim((string) $request->input('rejection_reason', 'Bukti pembayaran tidak valid.'));

        $registration->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        \App\Models\ManualPayment::where('event_registration_id', $registration->id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected', 'rejection_reason' => $reason]);

        // Notify user
        try {
            \App\Models\UserNotification::create([
                'user_id'    => $registration->user_id,
                'type'       => 'event_registration_rejected',
                'title'      => 'Pendaftaran Ditolak',
                'message'    => 'Bukti pembayaran untuk event "' . ($event->title ?? 'Event') . '" ditolak. Alasan: ' . $reason,
                'data'       => ['event_id' => $event->id, 'url' => route('payment', $event)],
                'expires_at' => now()->addDays(14),
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success', 'Pendaftaran berhasil ditolak.');
    }

    /**
     * Admin: Cancel an approved registration — set back to pending.
     */
    public function cancelApprovalRegistration(Request $request, Event $event, EventRegistration $registration)
    {
        if ($registration->event_id !== $event->id) {
            return back()->with('error', 'Data tidak valid.');
        }

        if ($registration->status !== 'active') {
            return back()->with('error', 'Hanya registrasi aktif yang bisa dibatalkan.');
        }

        $registration->update([
            'status'               => 'pending',
            'payment_verified_at'  => null,
            'payment_verified_by'  => null,
        ]);

        // Reset ManualPayment back to pending
        \App\Models\ManualPayment::where('event_registration_id', $registration->id)
            ->where('status', 'settled')
            ->update(['status' => 'pending']);

        // Notify user
        try {
            \App\Models\UserNotification::create([
                'user_id'    => $registration->user_id,
                'type'       => 'event_registration_cancelled',
                'title'      => 'Konfirmasi Pembayaran Dibatalkan',
                'message'    => 'Konfirmasi pembayaran Anda untuk event "' . ($event->title ?? 'Event') . '" telah dibatalkan oleh admin. Silakan hubungi admin untuk informasi lebih lanjut.',
                'data'       => ['event_id' => $event->id],
                'expires_at' => now()->addDays(14),
            ]);
        } catch (\Throwable $e) {}

        return back()->with('success', 'Approval berhasil dibatalkan. Status kembali ke pending.');
    }

    /**
     * Delete an event registration and cleanup related data/files.
     */
    public function destroyRegistration(Event $event, EventRegistration $registration)
    {
        try {
            // Cleanup related manual payments and their physical proof files
            $manuals = \App\Models\ManualPayment::where('event_registration_id', $registration->id)->get();
            foreach ($manuals as $m) {
                $proofs = \App\Models\PaymentProof::where('manual_payment_id', $m->id)->get();
                foreach ($proofs as $p) {
                    if ($p->file_path && Storage::disk('public')->exists($p->file_path)) {
                        Storage::disk('public')->delete($p->file_path);
                    }
                    $p->delete();
                }
                $m->delete();
            }

            // Cleanup the primary payment_proof file if exists
            if ($registration->payment_proof && Storage::disk('public')->exists($registration->payment_proof)) {
                Storage::disk('public')->delete($registration->payment_proof);
            }

            $registration->delete();

            return redirect()->back()->with('success', 'Registration data updated successfully dihapus.');
        } catch (\Throwable $e) {
            \Log::error('Registration deletion failed', ['id' => $registration->id, 'error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus pendaftaran: ' . $e->getMessage());
        }
    }

    /**
     * Admin: Store multiple event materials (files and links)
     */
    public function storeMaterials(Request $request, Event $event)
    {
        $trainerId = $event->trainer_id ?? auth()->id();

        if (!$request->hasFile('files') && (empty($request->input('links')) || !is_array($request->input('links')))) {
            return back()->with('error', 'Harap sertakan setidaknya satu file atau satu link materi.');
        }

        $request->validate([
            'files' => 'nullable|array',
            'files.*' => 'required|file|mimes:pdf,mp4,pptx,ppt,docx,doc|max:512000',
            'links' => 'nullable|array',
            'links.*.url' => 'required|url|max:2048',
            'links.*.name' => 'nullable|string|max:255',
        ]);

        $primaryMaterialPath = null;

        // Process files
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $filepath = $file->storeAs('events/' . $event->id . '/materials', $filename, 'public');

                \App\Models\EventTrainerModule::create([
                    'event_id' => $event->id,
                    'trainer_id' => $trainerId,
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $filepath,
                    'status' => 'approved', // Admin uploads are auto-approved!
                ]);

                if ($primaryMaterialPath === null) {
                    $primaryMaterialPath = $filepath;
                }
            }
        }

        // Process links
        if (!empty($request->input('links')) && is_array($request->input('links'))) {
            foreach ($request->input('links') as $link) {
                if (empty($link['url'])) continue;
                $linkUrl = $link['url'];
                $linkName = !empty($link['name']) ? $link['name'] : $linkUrl;

                \App\Models\EventTrainerModule::create([
                    'event_id' => $event->id,
                    'trainer_id' => $trainerId,
                    'original_name' => $linkName,
                    'path' => $linkUrl,
                    'status' => 'approved', // Admin uploads are auto-approved!
                ]);

                if ($primaryMaterialPath === null) {
                    $primaryMaterialPath = $linkUrl;
                }
            }
        }

        if ($primaryMaterialPath) {
            $event->update([
                'module_path' => $primaryMaterialPath,
                'material_status' => 'approved',
            ]);
        }

        return back()->with('success', 'Materi event berhasil disimpan.');
    }

    /**
     * Admin: Delete specific event material
     */
    public function destroyMaterial(Event $event, \App\Models\EventTrainerModule $module)
    {
        if ($module->event_id !== $event->id) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        if (!preg_match('#^https?://#i', $module->path)) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($module->path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($module->path);
            }
        }

        $module->delete();

        if ($event->module_path === $module->path) {
            $nextModule = \App\Models\EventTrainerModule::where('event_id', $event->id)->first();
            $event->update([
                'module_path' => $nextModule ? $nextModule->path : null,
                'material_status' => $nextModule ? $event->material_status : 'draft',
            ]);
        }

        return back()->with('success', 'Materi event berhasil dihapus.');
    }

    /**
     * Admin: Update specific event material feedback link
     */
    public function updateFeedbackLink(Request $request, Event $event, \App\Models\EventTrainerModule $module)
    {
        if ($module->event_id !== $event->id) {
            return back()->with('error', 'Aksi tidak valid.');
        }

        $validated = $request->validate([
            'feedback_link' => 'nullable|url|max:2048',
        ]);

        $module->update([
            'survey_link' => $validated['feedback_link'],
            'feedback_link' => $validated['feedback_link']
        ]);

        return back()->with('success', 'Link feedback materi berhasil diperbarui.');
    }
}


