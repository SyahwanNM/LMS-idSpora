<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Models\Event;


// Throttle login to mitigate brute-force attempts (10 req/min per IP or user)


// Throttle login to mitigate brute-force attempts (10 req/min per IP or user)
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
// Public events listing throttled to avoid scraping (120 req/min)
Route::get ('/events', [EventController::class, 'index'])->middleware('throttle:120,1');
Route::get('/events/{id}', [EventController::class, 'show'])->where('id', '[0-9]+')->middleware('throttle:120,1');

// Authenticated user actions with moderate throttle (100 req/min)
Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cek Profil Sendiri
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/events/{id}/register', [EventController::class, 'register']);

    // tambahan endpoint untuk alur event
    Route::get('/events/registrations', [EventController::class, 'listRegistrations']);
    Route::get('/events/{id}/registration/status', [EventController::class, 'registrationStatus']);
    Route::post('/events/{id}/payment', [EventController::class, 'createPayment']);
    Route::post('/events/{id}/cancel', [EventController::class, 'cancelRegistration']);

    // Manual Payment Endpoints
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{id}', [PaymentController::class, 'show']);
    Route::post('/manual-payment', [PaymentController::class, 'store']);
    Route::post('/manual-payment/{id}', [PaymentController::class, 'update']);
    Route::delete('/manual-payment/{id}', [PaymentController::class, 'destroy']);
});

// Admin Manage Events API (CRUD)
// Admin endpoints with stricter throttle (60 req/min)
Route::middleware(['auth:sanctum', 'admin', 'throttle:60,1'])->prefix('admin')->group(function () {
    // List events with simple pagination
    Route::get('/events', function (Illuminate\Http\Request $request) {
        $perPage = max(1, min((int)$request->query('per_page', 10), 100));
        $events = Event::query()->latest()->paginate($perPage);
        return response()->json([
            'status' => 'success',
            'message' => 'Daftar event (admin)',
            'data' => $events,
        ]);
    });

    // Show single event
    Route::get('/events/{event}', function (Event $event) {
        return response()->json([
            'status' => 'success',
            'message' => 'Detail event',
            'data' => $event,
        ]);
    });

    // Create event
    Route::post('/events', function (Illuminate\Http\Request $request) {
        $validated = $request->validate([
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

        // Store image if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $imagePath = ltrim(str_replace('storage/', '', $imagePath), '/');
            if (!str_starts_with($imagePath, 'events/')) {
                $imagePath = 'events/' . basename($imagePath);
            }
        }

        // Normalize schedule
        $scheduleRows = [];
        foreach ((array)$request->input('schedule', []) as $row) {
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

        // Normalize expenses
        $expenseRows = [];
        foreach ((array)$request->input('expenses', []) as $row) {
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
            'image' => $imagePath,
            'schedule_json' => $scheduleRows,
            'expenses_json' => $expenseRows,
        ]);

        // Persist relational schedule & expenses
        foreach($scheduleRows as $row){ $event->scheduleItems()->create($row); }
        foreach($expenseRows as $row){ $event->expenses()->create($row); }

        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil dibuat',
            'data' => $event->fresh(),
        ], 201);
    });

    // Update event
    Route::put('/events/{event}', function (Illuminate\Http\Request $request, Event $event) {
        $validated = $request->validate([
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
        ];

        // Image
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $imagePath = ltrim(str_replace('storage/', '', $imagePath), '/');
            if (!str_starts_with($imagePath, 'events/')) {
                $imagePath = 'events/' . basename($imagePath);
            }
            $data['image'] = $imagePath;
        }

        // Schedules
        $scheduleRows = [];
        foreach ((array)$request->input('schedule', []) as $row) {
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

        // Expenses
        $expenseRows = [];
        foreach ((array)$request->input('expenses', []) as $row) {
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

        $event->update($data);

        // Replace relational schedules & expenses
        $event->scheduleItems()->delete();
        foreach($scheduleRows as $row){ $event->scheduleItems()->create($row); }
        $event->expenses()->delete();
        foreach($expenseRows as $row){ $event->expenses()->create($row); }

        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil diupdate',
            'data' => $event->fresh(),
        ]);
    });

    // Delete event
    Route::delete('/events/{event}', function (Event $event) {
        $event->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil dihapus',
        ]);
    });
});