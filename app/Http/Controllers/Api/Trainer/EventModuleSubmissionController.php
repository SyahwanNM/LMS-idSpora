<?php

namespace App\Http\Controllers\Api\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventModuleSubmissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $trainerName = trim((string) ($user->name ?? ''));
        if ($trainerName === '') {
            return response()->json(['data' => []]);
        }

        // Mirror current web behavior: match event by parsed speaker list.
        $candidates = Event::query()
            ->whereNotNull('speaker')
            ->where('speaker', 'like', '%' . $trainerName . '%')
            ->orderByDesc('event_date')
            ->limit(200)
            ->get();

        $items = $candidates->filter(function (Event $event) use ($trainerName) {
            $names = $this->parseSpeakerNames((string) $event->speaker);
            return in_array(mb_strtolower($trainerName), $names, true);
        })->map(function (Event $event) {
            return [
                'event_id' => $event->id,
                'title' => $event->title,
                'event_date' => optional($event->event_date)->format('Y-m-d'),
                'jenis' => $event->jenis,
                'module_uploaded' => !empty($event->module_path),
                'module_url' => !empty($event->module_path) ? $event->module_file_url : null,
                'module_submission_url' => !empty($event->module_path) ? $event->module_file_url : null,
                'module_submitted_at' => $event->module_submitted_at,
                'module_verified_at' => $event->module_verified_at,
                'module_rejected_at' => $event->module_rejected_at,
                'module_rejection_reason' => $event->module_rejection_reason,
            ];
        })->values();

        return response()->json(['data' => $items]);
    }

    public function store(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();
        if (($user->role ?? null) !== 'trainer') {
            abort(403, 'Hanya trainer yang dapat mengupload module.');
        }

        $trainerName = trim((string) ($user->name ?? ''));
        $names = $this->parseSpeakerNames((string) $event->speaker);
        if ($trainerName === '' || !in_array(mb_strtolower($trainerName), $names, true)) {
            abort(403, 'Event ini bukan milik Anda.');
        }

        $request->validate([
            'module' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar,7z|max:20480',
        ]);

        $file = $request->file('module');
        $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $path = $file->storeAs('events/modules/submissions/' . $event->id, $filename, 'public');

        $event->update([
            'module_path' => $path,
        ]);

        return response()->json([
            'message' => 'Module berhasil diupload dan menunggu verifikasi admin.',
            'data' => [
                'event_id' => $event->id,
                'module_submission_path' => $event->module_path,
                'module_submission_url' => $event->module_file_url,
                'module_submitted_at' => $event->created_at,
            ],
        ], 201);
    }

    /**
     * Parse the speaker field into lowercase names.
     */
    private function parseSpeakerNames(string $speaker): array
    {
        $speaker = trim($speaker);
        if ($speaker === '') {
            return [];
        }

        $parts = preg_split('/\s*[,;]+\s*/', $speaker) ?: [];
        $names = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') {
                $names[] = mb_strtolower($p);
            }
        }

        return array_values(array_unique($names));
    }
}
