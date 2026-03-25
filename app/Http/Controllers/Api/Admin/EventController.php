<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), 100));
        $events = Event::query()->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar event (admin)',
            'data' => $events,
        ]);
    }

    public function show(Event $event)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Detail event',
            'data' => $event,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $imagePath = $this->storeEventImageIfAny($request);

        [$scheduleRows, $expenseRows] = $this->normalizeScheduleAndExpenses($request);

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

        foreach ($scheduleRows as $row) {
            $event->scheduleItems()->create($row);
        }
        foreach ($expenseRows as $row) {
            $event->expenses()->create($row);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil dibuat',
            'data' => $event->fresh(),
        ], 201);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $this->validatePayload($request);

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

        return response()->json([
            'status' => 'success',
            'message' => 'Event berhasil diupdate',
            'data' => $event->fresh(),
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

    private function validatePayload(Request $request): array
    {
        return $request->validate([
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
}
