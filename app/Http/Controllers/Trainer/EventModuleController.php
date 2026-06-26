<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventTrainerModule;
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
        $trainerId = (int) $user->id;

        if ($trainerName === '') {
            return response()->json(['data' => []]);
        }

        // Find events where this trainer is speaker or trainer_id
        $candidates = Event::query()
            ->where(function ($q) use ($trainerId, $trainerName) {
                $q->where('trainer_id', $trainerId)
                  ->orWhere('speaker', 'like', '%' . $trainerName . '%');
            })
            ->orderByDesc('event_date')
            ->limit(200)
            ->get();

        $items = $candidates->filter(function (Event $event) use ($trainerId, $trainerName) {
            if ((int) ($event->trainer_id ?? 0) === $trainerId) return true;
            $names = $this->parseSpeakerNames((string) $event->speaker);
            return in_array(mb_strtolower($trainerName), $names, true);
        })->map(function (Event $event) use ($trainerId) {
            $modules = EventTrainerModule::where('event_id', $event->id)
                ->where('trainer_id', $trainerId)
                ->orderByDesc('created_at')
                ->get()
                ->map(fn($m) => [
                    'id'          => $m->id,
                    'name'        => $m->original_name,
                    'path'        => $m->path,
                    'url'         => $m->download_url,
                    'status'      => $m->status,
                    'uploaded_at' => $m->created_at?->toDateTimeString(),
                    'rejection_reason' => $m->rejection_reason,
                ])->values()->all();

            return [
                'id'              => $event->id,
                'title'           => $event->title,
                'event_date'      => optional($event->event_date)->format('Y-m-d'),
                'jenis'           => $event->jenis,
                'module_uploaded' => count($modules) > 0,
                'modules'         => $modules,
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

        $trainerId = (int) $user->id;
        $trainerName = trim((string) ($user->name ?? ''));

        // Check authorization: trainer_id match OR speaker name match
        $authorized = ((int) ($event->trainer_id ?? 0) === $trainerId);
        if (!$authorized && $trainerName !== '') {
            $names = $this->parseSpeakerNames((string) $event->speaker);
            $authorized = in_array(mb_strtolower($trainerName), $names, true);
        }

        if (!$authorized) {
            abort(403, 'Event ini bukan milik Anda.');
        }

        $assignment = \App\Models\TrainerAssignment::where('event_id', $event->id)
            ->where('trainer_id', $user->id)
            ->first();


        $request->validate([
            'module' => 'required|file|mimes:pdf,mp4,doc,docx,ppt,pptx,zip,rar,7z|max:20480',
        ]);

        $file = $request->file('module');
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . str_replace(' ', '_', $originalName);
        $path = $file->storeAs('events/modules/submissions/' . $event->id, $filename, 'public');

        \App\Models\EventTrainerModule::create([
            'event_id' => $event->id,
            'trainer_id' => $user->id,
            'original_name' => $originalName,
            'path' => $path,
            'status' => 'pending_review',
        ]);

        $assignment = \App\Models\TrainerAssignment::where('event_id', $event->id)
            ->where('trainer_id', $user->id)
            ->first();

        if (!$assignment) {
            $assignment = \App\Models\TrainerAssignment::create([
                'trainer_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'accepted',
                'sla_upload_deadline' => $event->material_deadline ?: now()->addDays(3),
            ]);
        }

        $assignment->update([
            'material_path' => $path,
            'materials_uploaded_at' => now(),
            'material_status' => 'pending_review',
            'material_submitted_at' => now(),
            'material_approved_at' => null,
            'material_approved_by' => null,
            'material_rejected_at' => null,
            'material_rejected_by' => null,
            'material_rejection_reason' => null,
        ]);

        return back()->with('success', 'Module berhasil diupload dan menunggu verifikasi admin.');
    }

    private function parseSpeakerNames(string $speaker): array
    {
        $speaker = trim($speaker);
        if ($speaker === '') return [];

        $parts = preg_split('/\s*[,;]+\s*/', $speaker) ?: [];
        $names = [];
        foreach ($parts as $p) {
            $p = trim($p);
            if ($p !== '') $names[] = mb_strtolower($p);
        }
        return array_values(array_unique($names));
    }
}
