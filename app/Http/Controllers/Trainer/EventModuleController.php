<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventModuleController extends Controller
{
    public function index()
    {
        return view('trainer.event-modules');
    }

    public function apiIndex(Request $request): JsonResponse
    {
        $user = $request->user();
        $trainerName = trim((string) ($user->name ?? ''));
        if ($trainerName === '') {
            return response()->json(['data' => []]);
        }

        // Narrow down by LIKE first, then confirm via exact-name match in parsed speaker list.
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
                'id' => $event->id,
                'title' => $event->title,
                'event_date' => optional($event->event_date)->format('Y-m-d'),
                'jenis' => $event->jenis,
                'module_uploaded' => !empty($event->module_path),
                'module_url' => !empty($event->module_path) ? $event->module_file_url : null,
            ];
        })->values();

        return response()->json(['data' => $items]);
    }

    public function upload(Request $request, Event $event)
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
            'material_status' => 'pending_review',
            'material_approved_at' => null,
            'material_approved_by' => null,
            'material_rejection_reason' => null,
            'module_submitted_at' => now(),
            'module_verified_at' => null,
            'module_verified_by' => null,
            'module_rejected_at' => null,
            'module_rejected_by' => null,
            'module_rejection_reason' => null,
        ]);

        return back()->with('success', 'Module berhasil diupload dan menunggu verifikasi admin.');
    }

    /**
     * Parse the speaker field into lowercase names.
     */
    private function parseSpeakerNames(string $speaker): array
    {
        $speaker = trim($speaker);
        if ($speaker === '')
            return [];

        $parts = preg_split('/\s*[,;]+\s*/', $speaker) ?: [];
        $names = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '')
                $names[] = mb_strtolower($p);
        }
        return array_values(array_unique($names));
    }
}
