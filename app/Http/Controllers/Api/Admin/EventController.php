<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventTrainerModule;
use App\Models\TrainerNotification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'per_page'       => 'nullable|integer|min:1|max:100',
            'search'         => 'nullable|string|max:255',       // Search Event Name
            'status'         => 'nullable|in:upcoming,ongoing,finished', // Event Status
            'manage_action'  => 'nullable|in:manage,create',     // Manage Type
            'event_month'    => 'nullable|date_format:Y-m',      // Event Month (format: 2026-04)
            'jenis'          => 'nullable|string|max:100',
            'is_published'   => 'nullable|boolean',
            'date_from'      => 'nullable|date',
            'date_to'        => 'nullable|date|after_or_equal:date_from',
            'price_min'      => 'nullable|numeric|min:0',
            'price_max'      => 'nullable|numeric|min:0',
            'trainer_id'     => 'nullable|integer|exists:users,id',
            'sort_by'        => 'nullable|in:event_date,price,title,created_at',
            'sort_dir'       => 'nullable|in:asc,desc',
        ]);

        $perPage = max(1, min((int) $request->query('per_page', 10), 100));

        $query = Event::query()->with(['scheduleItems', 'expenses']);

        // --- Search by title or speaker ---
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('speaker', 'like', "%{$search}%");
            });
        }

        // --- Filter by jenis (tipe event) ---
        if ($jenis = $request->query('jenis')) {
            $query->where('jenis', $jenis);
        }

        // --- Filter by manage_action (Manage Type) ---
        if ($manageAction = $request->query('manage_action')) {
            $query->where('manage_action', $manageAction);
        }

        // --- Filter by event_month (format: Y-m, contoh: 2026-04) ---
        if ($eventMonth = $request->query('event_month')) {
            [$year, $month] = explode('-', $eventMonth);
            $query->whereYear('event_date', $year)
                  ->whereMonth('event_date', $month);
        }

        // --- Filter by published status ---
        if ($request->has('is_published')) {
            $query->where('is_published', filter_var($request->query('is_published'), FILTER_VALIDATE_BOOLEAN));
        }

        // --- Filter by event date range ---
        if ($dateFrom = $request->query('date_from')) {
            $query->whereDate('event_date', '>=', $dateFrom);
        }
        if ($dateTo = $request->query('date_to')) {
            $query->whereDate('event_date', '<=', $dateTo);
        }

        // --- Filter by price range ---
        if ($priceMin = $request->query('price_min')) {
            $query->where('price', '>=', $priceMin);
        }
        if ($priceMax = $request->query('price_max')) {
            $query->where('price', '<=', $priceMax);
        }

        // --- Filter by trainer ---
        if ($trainerId = $request->query('trainer_id')) {
            $query->where('trainer_id', $trainerId);
        }

        // --- Filter by status (upcoming / ongoing / finished) ---
        if ($status = $request->query('status')) {
            $now = now()->format('Y-m-d H:i:s');
            $today = now()->format('Y-m-d');
            match ($status) {
                'upcoming' => $query->whereDate('event_date', '>', $today),
                'ongoing'  => $query->whereDate('event_date', $today),
                'finished' => $query->whereRaw(
                    "TIMESTAMP(event_date, COALESCE(event_time_end, COALESCE(event_time,'23:59:59'))) < ?",
                    [$now]
                ),
                default    => null,
            };
        }

        // --- Sorting ---
        $sortBy  = $request->query('sort_by', 'created_at');
        $sortDir = $request->query('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $events = $query->paginate($perPage);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar event (admin)',
            'filters' => [
                'search'        => $request->query('search'),
                'status'        => $request->query('status'),
                'manage_action' => $request->query('manage_action'),
                'event_month'   => $request->query('event_month'),
                'jenis'         => $request->query('jenis'),
                'is_published'  => $request->query('is_published'),
                'date_from'     => $request->query('date_from'),
                'date_to'       => $request->query('date_to'),
                'price_min'     => $request->query('price_min'),
                'price_max'     => $request->query('price_max'),
                'trainer_id'    => $request->query('trainer_id'),
                'sort_by'       => $sortBy,
                'sort_dir'      => $sortDir,
            ],
            'data'       => $events->items(),
            'pagination' => [
                'current_page' => $events->currentPage(),
                'per_page'     => $events->perPage(),
                'total'        => $events->total(),
                'last_page'    => $events->lastPage(),
            ],
        ]);
    }

    public function show(Event $event)
    {
        $event->loadMissing(['scheduleItems', 'expenses']);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail event',
            'data' => $event,
        ]);
    }

    /**
     * GET /api/admin/events/{event}/registrations
     * Daftar peserta terdaftar pada sebuah event, dengan filter pencarian nama/email.
     */
    public function registrations(Request $request, Event $event)
    {
        $q       = trim((string) $request->query('q', ''));
        $perPage = max(1, min((int) $request->query('per_page', 20), 100));

        $query = \App\Models\EventRegistration::query()
            ->with('user:id,name,email')
            ->where('event_id', $event->id)
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('user', function ($uq) use ($q) {
                    $uq->where('name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->latest('created_at');

        $paginated = $query->paginate($perPage)->appends($request->query());

        $isFinished = method_exists($event, 'isFinished') && $event->isFinished();

        $data = $paginated->map(fn($reg) => [
            'id'              => $reg->id,
            'user_id'         => $reg->user_id,
            'name'            => $reg->user?->name,
            'email'           => $reg->user?->email,
            'status'          => ($isFinished && $reg->status === 'active' && empty($reg->attended_at))
                                    ? 'alpha'
                                    : $reg->status,
            'registered_at'   => $reg->created_at?->toDateTimeString(),
            'total_price'     => (float) ($reg->total_price ?? 0),
            'attended'        => !empty($reg->attended_at),
            'attended_at'     => $reg->attended_at?->toISOString(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar peserta event',
            'data'    => $data,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'last_page'    => $paginated->lastPage(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request, isUpdate: false);

        $imagePath = $this->storeEventImageIfAny($request);

        [$scheduleRows, $expenseRows] = $this->normalizeScheduleAndExpenses($request);

        $isPublished = (bool) ($validated['is_published'] ?? false);
        $publishedAt = $validated['published_at'] ?? null;
        if ($isPublished && !$publishedAt) {
            $publishedAt = now();
        }
        if (!$isPublished) {
            $publishedAt = null;
        }

        $event = Event::create([
            'title' => $validated['title'],
            'speaker' => $validated['speaker'],
            'manage_action' => $validated['manage_action'],
            'materi' => $validated['materi'] ?? null,
            'jenis' => $validated['jenis'] ?? null,
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'benefit' => $validated['benefit'] ?? null,
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
            'location' => $validated['location'],
            'maps_url' => $validated['maps_url'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'zoom_link' => $validated['zoom_link'] ?? null,
            'price' => $validated['price'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'discount_until' => $validated['discount_until'] ?? null,
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'event_time_end' => $validated['event_time_end'] ?? null,
            'material_deadline' => $validated['material_deadline'] ?? null,
            'image' => $imagePath,
            'schedule_json' => $scheduleRows,
            'expenses_json' => $expenseRows,
            'is_published' => $isPublished,
            'published_at' => $publishedAt,
        ]);

        // Guard: pastikan tidak ada module/registrasi/assignment/dll yang ter-create secara tidak sengaja untuk event baru (orphan dari deleted events)
        \App\Models\EventRegistration::where('event_id', $event->id)->delete();
        \App\Models\EventTrainerModule::where('event_id', $event->id)->delete();
        \App\Models\TrainerAssignment::where('event_id', $event->id)->forceDelete();
        \App\Models\EventDailyQr::where('event_id', $event->id)->delete();
        \App\Models\EventSpeaker::where('event_id', $event->id)->delete();
        \App\Models\EventScheduleItem::where('event_id', $event->id)->delete();
        \App\Models\EventExpense::where('event_id', $event->id)->delete();
        \App\Models\Feedback::where('event_id', $event->id)->delete();
        \Illuminate\Support\Facades\DB::table('user_saved_events')->where('event_id', $event->id)->delete();

        foreach ($scheduleRows as $row) {
            $event->scheduleItems()->create($row);
        }
        foreach ($expenseRows as $row) {
            $event->expenses()->create($row);
        }

        // Auto-generate attendance QR code for the new event
        $this->generateAttendanceQr($event);

        // Auto-generate per-day QR codes (multi-day support)
        try {
            app(\App\Services\EventDailyQrService::class)->ensureAllDailyQrs($event);
        } catch (\Throwable $e) {
            \Log::warning('Failed to auto-generate daily QRs on store', ['event_id' => $event->id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil dibuat',
            'data' => $event->fresh()->load(['scheduleItems', 'expenses']),
        ], 201);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $this->validatePayload($request, isUpdate: true);

        $data = [
            'title' => $validated['title'],
            'speaker' => $validated['speaker'],
            'manage_action' => $validated['manage_action'],
            'materi' => $validated['materi'] ?? null,
            'jenis' => $validated['jenis'] ?? null,
            'short_description' => $validated['short_description'],
            'description' => $validated['description'],
            'benefit' => $validated['benefit'] ?? null,
            'terms_and_conditions' => $validated['terms_and_conditions'] ?? null,
            'location' => $validated['location'],
            'maps_url' => $validated['maps_url'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'zoom_link' => $validated['zoom_link'] ?? null,
            'price' => $validated['price'],
            'discount_percentage' => $validated['discount_percentage'] ?? 0,
            'discount_until' => $validated['discount_until'] ?? null,
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'event_time_end' => $validated['event_time_end'] ?? null,
            'material_deadline' => $validated['material_deadline'] ?? null,
            'is_reseller_event' => (bool) ($validated['is_reseller_event'] ?? $event->is_reseller_event ?? false),
        ];

        if (array_key_exists('is_published', $validated)) {
            $isPublished = (bool) $validated['is_published'];

            // Guard: cek kelengkapan sebelum boleh publish
            if ($isPublished) {
                $incomplete = [];

                $hasMapsLink = !empty($event->maps_url);
                $hasZoomLink = !empty($event->zoom_link);
                $isOfflineOnly = $hasMapsLink && !$hasZoomLink;
                $requiresVbg = !$isOfflineOnly;

                if ($requiresVbg && empty($event->vbg_path)) {
                    $incomplete[] = 'Virtual background (vbg) belum diupload';
                }

                if ($event->jenis !== 'Lomba') {
                    $hasApprovedModule = \App\Models\EventTrainerModule::where('event_id', $event->id)
                        ->where('status', 'approved')
                        ->exists();

                    if (!$hasApprovedModule) {
                        $incomplete[] = 'Belum ada modul trainer yang disetujui';
                    }
                }

                if (!empty($incomplete)) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Event belum lengkap, tidak dapat dipublish.',
                        'data'    => ['incomplete' => $incomplete],
                    ], 422);
                }
            }

            $data['is_published'] = $isPublished;

            if ($isPublished) {
                $data['published_at'] = $validated['published_at'] ?? ($event->published_at ?? now());
            } else {
                $data['published_at'] = null;
            }
        } elseif (array_key_exists('published_at', $validated)) {
            // Allow admin to adjust published_at only when already published.
            if ((bool) $event->is_published) {
                $data['published_at'] = $validated['published_at'];
            }
        }

        $imagePath = $this->storeEventImageIfAny($request);
        if ($imagePath) {
            $data['image'] = $imagePath;
        }

        [$scheduleRows, $expenseRows] = $this->normalizeScheduleAndExpenses($request);
        $data['schedule_json'] = $scheduleRows;
        $data['expenses_json'] = $expenseRows;

        $event->update($data);

        $event->scheduleItems()->delete();
        foreach ($scheduleRows as $row) {
            $event->scheduleItems()->create($row);
        }

        $event->expenses()->delete();
        foreach ($expenseRows as $row) {
            $event->expenses()->create($row);
        }

        // Ensure per-day QRs exist (removes out-of-range, adds missing when dates changed)
        try {
            app(\App\Services\EventDailyQrService::class)->syncDailyQrs($event->fresh());
        } catch (\Throwable $e) {
            \Log::warning('Failed to sync daily QRs on update', ['event_id' => $event->id, 'error' => $e->getMessage()]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil diupdate',
            'data' => $event->fresh()->load(['scheduleItems', 'expenses']),
        ]);
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil dihapus',
        ]);
    }

    /**
     * POST /api/admin/events/{event}/documents
     *
     * Upload dokumen operasional event: virtual background dan/atau absensi.
     * Gunakan multipart/form-data dengan field:
     *   - virtual_background  (file: jpg/jpeg/png/webp, max 4 MB) — opsional
     *   - attendance          (file: jpg/jpeg/png/webp/pdf, max 8 MB) — opsional
     */
    public function uploadDocuments(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'virtual_background' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'attendance'         => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:8192',
        ]);

        if (!$request->hasFile('virtual_background') && !$request->hasFile('attendance')) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada file yang dikirim. Sertakan virtual_background dan/atau attendance.',
            ], 422);
        }

        $updates              = [];
        $notificationMessages = [];

        if ($request->hasFile('virtual_background')) {
            // Hapus file lama jika ada
            if (!empty($event->vbg_path)) {
                Storage::disk('public')->delete($event->vbg_path);
            }
            $updates['vbg_path']      = $request->file('virtual_background')->store('events/docs', 'public');
            $notificationMessages[]   = 'Virtual Background (VBG)';
        }

        if ($request->hasFile('attendance')) {
            if (!empty($event->attendance_path)) {
                Storage::disk('public')->delete($event->attendance_path);
            }
            $updates['attendance_path'] = $request->file('attendance')->store('events/docs', 'public');
            $notificationMessages[]     = 'Absensi';
        }

        $event->update($updates);

        // Kirim notifikasi ke trainer jika ada
        if (!empty($event->trainer_id)) {
            $trainer = User::query()
                ->where('id', (int) $event->trainer_id)
                ->where('role', 'trainer')
                ->first();

            if ($trainer) {
                TrainerNotification::create([
                    'trainer_id' => (int) $trainer->id,
                    'type'       => 'event_documents_uploaded',
                    'title'      => 'Dokumen Event Diunggah',
                    'message'    => 'Admin telah mengunggah dokumen untuk event "' . $event->title . '": '
                                    . implode(', ', $notificationMessages),
                    'data'       => [
                        'entity_type' => 'event',
                        'entity_id'   => (int) $event->id,
                        'url'         => route('trainer.events.show', $event->id),
                    ],
                    'expires_at' => now()->addDays(30),
                ]);
            }
        }

        $event->refresh();

        return response()->json([
            'status'  => 'success',
            'message' => 'Dokumen berhasil diunggah: ' . implode(', ', $notificationMessages),
            'data'    => [
                'event_id'        => $event->id,
                'vbg_url'         => $event->vbg_file_url,
                'attendance_url'  => $event->attendance_path
                                        ? Storage::disk('public')->url($event->attendance_path)
                                        : null,
                'documents_pct'   => $event->documents_completion_percent,
            ],
        ]);
    }

    /**
     * POST /api/admin/events/{event}/trainer-modules/{module}/approve
     *
     * Setujui submission modul trainer.
     * Setelah approve, trainer mendapat notifikasi.
     */
    public function approveModule(Request $request, Event $event, EventTrainerModule $module): JsonResponse
    {
        if ($module->event_id !== $event->id) {
            return response()->json(['status' => 'error', 'message' => 'Module tidak ditemukan untuk event ini.'], 404);
        }

        if ($module->status === 'approved') {
            return response()->json(['status' => 'error', 'message' => 'Module sudah disetujui sebelumnya.'], 422);
        }

        $module->update([
            'status'           => 'approved',
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
            'rejection_reason' => null,
        ]);

        // Sinkronkan status event jika semua module sudah approved
        $pendingCount = EventTrainerModule::where('event_id', $event->id)
            ->whereNotIn('status', ['approved'])
            ->count();

        if ($pendingCount === 0) {
            $event->update([
                'material_status'      => 'approved',
                'material_approved_at' => now(),
                'material_approved_by' => $request->user()->id,
            ]);
        }

        // Notifikasi trainer
        try {
            TrainerNotification::create([
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
        } catch (\Throwable $e) {
            // Notifikasi non-kritis, jangan gagalkan request
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Module berhasil disetujui.',
            'data'    => [
                'module_id'   => $module->id,
                'status'      => $module->status,
                'reviewed_by' => $module->reviewed_by,
                'reviewed_at' => $module->reviewed_at,
            ],
        ]);
    }

    /**
     * POST /api/admin/events/{event}/trainer-modules/{module}/reject
     *
     * Tolak submission modul trainer.
     * Body JSON/form: { "rejection_reason": "..." } (wajib)
     */
    public function rejectModule(Request $request, Event $event, EventTrainerModule $module): JsonResponse
    {
        if ($module->event_id !== $event->id) {
            return response()->json(['status' => 'error', 'message' => 'Module tidak ditemukan untuk event ini.'], 404);
        }

        if ($module->status === 'approved') {
            return response()->json(['status' => 'error', 'message' => 'Module sudah disetujui, tidak dapat ditolak.'], 422);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $module->update([
            'status'           => 'rejected',
            'reviewed_by'      => $request->user()->id,
            'reviewed_at'      => now(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);

        // Update status event ke rejected jika belum approved
        if (($event->material_status ?? '') !== 'approved') {
            $event->update([
                'material_status'          => 'rejected',
                'module_rejected_at'       => now(),
                'module_rejected_by'       => $request->user()->id,
                'module_rejection_reason'  => $request->input('rejection_reason'),
            ]);
        }

        // Notifikasi trainer
        try {
            TrainerNotification::create([
                'trainer_id' => $module->trainer_id,
                'type'       => 'event_material_rejected',
                'title'      => 'Materi Event Ditolak',
                'message'    => 'Modul "' . $module->original_name . '" untuk event "' . $event->title
                                . '" ditolak. Alasan: ' . $request->input('rejection_reason'),
                'data'       => [
                    'entity_type'      => 'event',
                    'entity_id'        => (int) $event->id,
                    'url'              => route('trainer.events.show', $event->id),
                    'rejection_reason' => $request->input('rejection_reason'),
                ],
                'expires_at' => now()->addDays(30),
            ]);
        } catch (\Throwable $e) {
            // Notifikasi non-kritis
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Module berhasil ditolak.',
            'data'    => [
                'module_id'        => $module->id,
                'status'           => $module->status,
                'rejection_reason' => $module->rejection_reason,
                'reviewed_by'      => $module->reviewed_by,
                'reviewed_at'      => $module->reviewed_at,
            ],
        ]);
    }

    /**
     * GET /api/admin/events/{event}/trainer-modules
     *
     * Daftar semua submission modul trainer untuk event ini.
     */
    public function listModules(Event $event): JsonResponse
    {
        $modules = EventTrainerModule::with('trainer:id,name,email')
            ->where('event_id', $event->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn(EventTrainerModule $m) => [
                'id'               => $m->id,
                'trainer_id'       => $m->trainer_id,
                'trainer_name'     => $m->trainer?->name,
                'trainer_email'    => $m->trainer?->email,
                'original_name'    => $m->original_name,
                'download_url'     => $m->download_url,
                'status'           => $m->status,
                'rejection_reason' => $m->rejection_reason,
                'reviewed_by'      => $m->reviewed_by,
                'reviewed_at'      => $m->reviewed_at,
                'submitted_at'     => $m->created_at,
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar modul trainer untuk event ini.',
            'data'    => $modules,
        ]);
    }

    private function validatePayload(Request $request, bool $isUpdate = false): array
    {
        // Saat store: materi, jenis, image, is_reseller_event wajib diisi
        // Saat update: boleh kosong (tidak perlu re-upload / re-set)
        $materiRule        = $isUpdate ? 'nullable|string|max:255'              : 'required|string|max:255';
        $jenisRule         = $isUpdate ? 'nullable|string|max:100'              : 'required|string|max:100';
        $imageRule         = $isUpdate ? 'nullable|image|mimes:jpg,jpeg,png|max:5120' : 'required|image|mimes:jpg,jpeg,png|max:5120';
        $resellerRule      = $isUpdate ? 'nullable|boolean'                     : 'required|boolean';

        return $request->validate([
            'title'                => 'required|string|max:255',
            'speaker'              => 'required|string|max:255',
            'manage_action'        => 'required|in:manage,create',
            'materi'               => $materiRule,
            'jenis'                => $jenisRule,
            'short_description'    => 'required|string',
            'description'          => 'required',
            'terms_and_conditions' => 'nullable|string',
            'location'             => 'required|string|max:255',
            'maps_url'             => 'nullable|string|max:512',
            'latitude'             => 'nullable|numeric|between:-90,90',
            'longitude'            => 'nullable|numeric|between:-180,180',
            'zoom_link'            => 'nullable|url|max:255',
            'price'                => 'required|numeric|min:0',
            'discount_percentage'  => 'nullable|integer|min:0|max:100',
            'discount_until'       => 'nullable|date',
            'event_date'           => 'required|date',
            'event_time'           => 'required',
            'event_time_end'       => 'nullable',
            'material_deadline'    => 'nullable|date|after_or_equal:today|before:event_date',
            'image'                => $imageRule,
            'benefit'              => 'nullable|array',
            'benefit.*'            => 'string|max:255',
            'is_published'         => 'nullable|boolean',
            'published_at'         => 'nullable|date',
            'is_reseller_event'    => $resellerRule,
            'schedule'             => 'nullable|array',
            'schedule.*.start'     => 'nullable|string',
            'schedule.*.end'       => 'nullable|string',
            'schedule.*.title'     => 'nullable|string|max:255',
            'schedule.*.description' => 'nullable|string|max:500',
            'expenses'             => 'nullable|array',
            'expenses.*.item'      => 'nullable|string|max:255',
            'expenses.*.quantity'  => 'nullable|numeric|min:0',
            'expenses.*.unit_price' => 'nullable|numeric|min:0',
        ]);
    }

    private function storeEventImageIfAny(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $imagePath = $request->file('image')->store('events', 'public');

        $imagePath = ltrim(str_replace('storage/', '', $imagePath), '/');
        if (!str_starts_with($imagePath, 'events/')) {
            $imagePath = 'events/' . basename($imagePath);
        }

        return $imagePath;
    }

    /**
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, array<string, mixed>>}
     */
    private function normalizeScheduleAndExpenses(Request $request): array
    {
        $scheduleRows = [];
        foreach ((array) $request->input('schedule', []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $start = trim((string) ($row['start'] ?? ''));
            $end = trim((string) ($row['end'] ?? ''));
            $title = trim((string) ($row['title'] ?? ''));
            $desc = trim((string) ($row['description'] ?? ''));
            if ($start || $title) {
                $scheduleRows[] = [
                    'start' => $start,
                    'end' => $end,
                    'title' => $title,
                    'description' => $desc,
                ];
            }
        }

        $expenseRows = [];
        foreach ((array) $request->input('expenses', []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $item = trim((string) ($row['item'] ?? ''));
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

        return [$scheduleRows, $expenseRows];
    }

    /**
     * Generate a one-time attendance QR code for the event.
     * Silently skips if QR already exists or if generation fails.
     */
    private function generateAttendanceQr(Event $event): void
    {
        if (!empty($event->attendance_qr_token) || !empty($event->attendance_qr_image)) {
            return; // Already generated, skip
        }

        try {
            $token    = bin2hex(random_bytes(16));
            $content  = url('/events/' . $event->id . '?t=' . $token);
            $filename = null;

            // Try PNG via SimpleSoftwareIO QrCode
            $png = null;
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
                // Fallback: SVG
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
                    // Final fallback: minimal 1x1 placeholder PNG
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
     * POST /api/admin/events/{event}/duplicate
     * Menduplikasi event: menyalin semua data konten, reset dokumen operasional,
     * status tidak publish, dan tanpa peserta.
     */
    public function duplicate(Event $event): JsonResponse
    {
        // Field konten yang disalin
        $copy = $event->replicate([
            // Reset: status publish
            'is_published',
            'published_at',
            // Reset: dokumen operasional
            'vbg_path',
            'module_path',
            'module_submission_path',
            'certificate_path',
            'material_status',
            'material_approved_at',
            'material_approved_by',
            'material_rejection_reason',
            'module_submitted_at',
            'module_verified_at',
            'module_verified_by',
            'module_rejected_at',
            'module_rejected_by',
            'module_rejection_reason',
            // Reset: QR attendance (akan di-generate ulang)
            'attendance_qr_token',
            'attendance_qr_image',
            'attendance_qr_generated_at',
        ]);

        $copy->title        = $event->title . ' (Copy)';
        $copy->is_published = false;
        $copy->published_at = null;

        // Reset semua dokumen operasional
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

        // Pastikan relasi trainerModules tidak ikut di-carry oleh replicate()
        $copy->unsetRelation('trainerModules');
        $copy->unsetRelation('approvedTrainerModules');
        $copy->unsetRelation('registrations');

        $copy->save();

        // Hapus EventTrainerModule dan EventRegistration yang mungkin ter-copy otomatis (dan orphan dari deleted events)
        \App\Models\EventTrainerModule::where('event_id', $copy->id)->delete();
        \App\Models\EventRegistration::where('event_id', $copy->id)->delete();
        \App\Models\TrainerAssignment::where('event_id', $copy->id)->forceDelete();
        \App\Models\EventDailyQr::where('event_id', $copy->id)->delete();
        \App\Models\EventSpeaker::where('event_id', $copy->id)->delete();
        \App\Models\EventScheduleItem::where('event_id', $copy->id)->delete();
        \App\Models\EventExpense::where('event_id', $copy->id)->delete();
        \App\Models\Feedback::where('event_id', $copy->id)->delete();
        \Illuminate\Support\Facades\DB::table('user_saved_events')->where('event_id', $copy->id)->delete();

        // Salin schedule items
        foreach ($event->scheduleItems as $item) {
            $copy->scheduleItems()->create($item->only([
                'time_start', 'time_end', 'activity', 'speaker', 'order',
            ]));
        }

        // Salin expenses
        foreach ($event->expenses as $expense) {
            $copy->expenses()->create($expense->only([
                'item', 'quantity', 'unit_price', 'total', 'note',
            ]));
        }

        // Salin EventSpeaker records agar assignment trainer tetap terjaga
        // (tanpa ini, has_approved_modules tidak bisa menemukan trainer_id yang di-assign)
        foreach ($event->speakers as $speaker) {
            $copy->speakers()->create($speaker->only([
                'trainer_id', 'name', 'salary', 'notes', 'order',
            ]));
        }

        // Generate QR attendance baru untuk event duplikat
        $this->generateAttendanceQr($copy);

        return $this->jsonSuccess('Event berhasil diduplikasi.', $copy->fresh(), null, 201);
    }
}