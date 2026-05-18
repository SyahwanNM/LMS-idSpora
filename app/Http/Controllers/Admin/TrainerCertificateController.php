<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Models\TrainerCertificateAsset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrainerCertificateController extends Controller
{
    public function index(Request $request)
    {
        $certificates = TrainerCertificate::query()
            ->with(['trainer', 'issuer', 'certifiable'])
            ->latest('issued_at')
            ->latest('created_at')
            ->paginate(20);

        return view('admin.trainer.certificates.index', compact('certificates'));
    }

    public function queue(Request $request)
    {
        $trainers = User::query()
            ->where('role', 'trainer')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.trainer.certificates.queue', compact('trainers'));
    }

    private function normalizeSignatures(array $signatures): array
    {
        return collect($signatures)
            ->filter(function ($signature) {
                return !empty($signature['name'])
                    || !empty($signature['position'])
                    || !empty($signature['file']);
            })
            ->values()
            ->all();
    }

    public function show(User $trainer)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $publishedKeys = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', [
                TrainerCertificate::STATUS_PUBLISHED,
                'sent',
                'revoked'
            ])
            ->get()
            ->mapWithKeys(function ($certificate) {
                return [
                    $certificate->certifiable_type . ':' . $certificate->certifiable_id => true,
                ];
            });

        $events = Event::query()
            ->where('trainer_id', $trainer->id)
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<=', now()->toDateString())
            ->orderByDesc('event_date')
            ->get()
            ->filter(function ($event) use ($publishedKeys) {
                return !$publishedKeys->has(Event::class . ':' . $event->id);
            })
            ->map(function ($event) {
                return [
                    'context' => 'event',
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->event_date,
                    'type' => $event->jenis ?? 'Event',
                ];
            });

        $courses = Course::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->orderByDesc('updated_at')
            ->get()
            ->filter(function ($course) use ($publishedKeys) {
                return !$publishedKeys->has(Course::class . ':' . $course->id);
            })
            ->map(function ($course) {
                return [
                    'context' => 'course',
                    'id' => $course->id,
                    'title' => $course->name,
                    'date' => $course->updated_at,
                    'type' => 'Course',
                ];
            });

        $pendingItems = $events
            ->concat($courses)
            ->sortByDesc('date')
            ->values();

        $certificates = TrainerCertificate::query()
            ->with(['certifiable', 'issuer'])
            ->where('trainer_id', $trainer->id)
            ->latest('issued_at')
            ->latest('created_at')
            ->get();

        return view('admin.trainer.certificates.show', compact(
            'trainer',
            'pendingItems',
            'certificates'
        ));
    }

    public function edit(
        User $trainer,
        string $context,
        int $id
    ) {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $model = $context === 'event'
            ? Event::findOrFail($id)
            : Course::findOrFail($id);

        $assets = TrainerCertificateAsset::query()
            ->where(
                'certifiable_type',
                get_class($model)
            )
            ->where(
                'certifiable_id',
                $model->id
            )
            ->orderBy('order_no')
            ->get();

        return view(
            'admin.trainer.certificates.edit',
            compact(
                'trainer',
                'context',
                'model',
                'assets'
            )
        );
    }

    public function update(Request $request, User $trainer, string $context, int $id)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $model = $context === 'event'
            ? Event::findOrFail($id)
            : Course::findOrFail($id);

        $request->validate([
            'template' => ['required', 'in:template_1,template_2,template_3'],

            'logos' => ['nullable', 'array', 'max:3'],
            'logos.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'signatures' => ['nullable', 'array', 'max:3'],
            'signatures.*.file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'signatures.*.name' => ['nullable', 'string', 'max:255'],
            'signatures.*.position' => ['nullable', 'string', 'max:255'],
        ]);

        TrainerCertificateAsset::query()
            ->where('certifiable_type', get_class($model))
            ->where('certifiable_id', $model->id)
            ->delete();

        foreach (($request->file('logos') ?? []) as $index => $logo) {
            if (!$logo) {
                continue;
            }

            $path = $logo->store(
                'trainer_certificate_assets/' . class_basename($model) . '/' . $model->id . '/logos',
                'public'
            );

            TrainerCertificateAsset::create([
                'certifiable_type' => get_class($model),
                'certifiable_id' => $model->id,
                'type' => TrainerCertificateAsset::TYPE_LOGO,
                'image_path' => $path,
                'order_no' => $index + 1,
            ]);
        }

        foreach (($request->input('signatures') ?? []) as $index => $signature) {
            $file = $request->file("signatures.$index.file");

            if (!$file) {
                continue;
            }

            $path = $file->store(
                'trainer_certificate_assets/' . class_basename($model) . '/' . $model->id . '/signatures',
                'public'
            );

            TrainerCertificateAsset::create([
                'certifiable_type' => get_class($model),
                'certifiable_id' => $model->id,
                'type' => TrainerCertificateAsset::TYPE_SIGNATURE,
                'name' => $signature['name'] ?? null,
                'position' => $signature['position'] ?? null,
                'image_path' => $path,
                'order_no' => $index + 1,
            ]);
        }

        return redirect()
            ->route('admin.trainer.certificates.edit', [
                'trainer' => $trainer->id,
                'context' => $context,
                'id' => $model->id,
            ])
            ->with('success', 'Konfigurasi sertifikat berhasil disimpan.');
    }

}