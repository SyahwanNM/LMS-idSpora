<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Event::query()
            ->where('trainer_id', $user->id)
            ->orderByDesc('event_date');

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where('title', 'like', '%' . $search . '%');
        }

        $events = $query
            ->paginate((int) ($request->input('per_page', 15)));

        return response()->json($events);
    }

    public function show(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();

        if ((int) $event->trainer_id !== (int) $user->id) {
            abort(403, 'Event ini bukan milik Anda.');
        }

        $event->load([
            'scheduleItems' => function ($q) {
                $q->orderBy('start', 'asc');
            }
        ]);

        return response()->json([
            'data' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'event_date' => optional($event->event_date)->format('Y-m-d'),
                'event_time' => $event->event_time,
                'event_time_end' => $event->event_time_end,
                'location' => $event->location,
                'jenis' => $event->jenis,
                'speaker' => $event->speaker,
                'image_url' => $event->image_url,
                'documents_completion_percent' => $event->documents_completion_percent ?? null,
                'module_uploaded' => !empty($event->module_path),
                'module_url' => !empty($event->module_path) ? $event->module_file_url : null,
                'module_submission_url' => !empty($event->module_path) ? $event->module_file_url : null,
                'module_submitted_at' => $event->created_at,
                'schedule_items' => ($event->scheduleItems ?? collect())->map(function ($it) {
                    return [
                        'id' => $it->id,
                        'start' => $it->start,
                        'end' => $it->end,
                        'title' => $it->title,
                        'description' => $it->description,
                    ];
                })->values(),
            ],
        ]);
    }
}
