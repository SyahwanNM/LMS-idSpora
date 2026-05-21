<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Models\TrainerCertificateAsset;
use App\Models\User;
use Illuminate\Http\Request;

class TrainerCertificateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $trainers = User::query()
            ->where('role', 'trainer')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $trainerIds = $trainers->pluck('id');

        $publishedCounts = TrainerCertificate::query()
            ->whereIn('trainer_id', $trainerIds)
            ->selectRaw('trainer_id, COUNT(*) as total')
            ->groupBy('trainer_id')
            ->pluck('total', 'trainer_id');

        $totalCertificates = TrainerCertificate::query()->count();

        $totalTrainers = User::query()
            ->where('role', 'trainer')
            ->count();

        $trainers->getCollection()->transform(function ($trainer) use ($publishedCounts) {
            $publishedKeys = TrainerCertificate::query()
                ->where('trainer_id', $trainer->id)
                ->whereIn('status', [
                    TrainerCertificate::STATUS_PUBLISHED,
                    'sent',
                    'revoked',
                ])
                ->get()
                ->mapWithKeys(function ($certificate) {
                    return [
                        $certificate->certifiable_type . ':' . $certificate->certifiable_id => true,
                    ];
                });

            $pendingEvents = Event::query()
                ->where('trainer_id', $trainer->id)
                ->whereNotNull('event_date')
                ->whereDate('event_date', '<=', now()->toDateString())
                ->get()
                ->filter(function ($event) use ($publishedKeys) {
                    return !$publishedKeys->has(Event::class . ':' . $event->id);
                })
                ->count();

            $pendingCourses = Course::query()
                ->where('trainer_id', $trainer->id)
                ->whereIn('status', ['published', 'approved', 'active'])
                ->get()
                ->filter(function ($course) use ($publishedKeys) {
                    return !$publishedKeys->has(Course::class . ':' . $course->id);
                })
                ->count();

            $trainer->pending_certificates_count = $pendingEvents + $pendingCourses;
            $trainer->published_certificates_count = (int) ($publishedCounts[$trainer->id] ?? 0);

            return $trainer;
        });

        $totalPending = $trainers->getCollection()->sum('pending_certificates_count');

        $pendingItems = \App\Models\Event::query()
            ->whereIn('trainer_id', $trainerIds)
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<=', now()->toDateString())
            ->withCount('registrations')
            ->orderByDesc('event_date')
            ->get();

        $certificates = \App\Models\Course::query()
            ->whereIn('trainer_id', $trainerIds)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->with('category')
            ->withCount('enrollments')
            ->get();

        return view('admin.trainer.certificates.index', compact(
            'trainers',
            'pendingItems',
            'certificates',
            'totalTrainers',
            'totalPending'
        ) + ['tab' => $request->query('tab', 'pendingItems')]);
    }

    public function queue(Request $request)
    {
        $trainers = User::query()
            ->where('role', 'trainer')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.trainer.certificates.queue', compact('trainers'));
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
                'revoked',
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

    public function edit(User $trainer, string $context, int $id)
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }

        $model = $context === 'event'
            ? Event::findOrFail($id)
            : Course::findOrFail($id);

        $assets = TrainerCertificateAsset::query()
            ->where('certifiable_type', get_class($model))
            ->where('certifiable_id', $model->id)
            ->orderBy('order_no')
            ->get();

        return view('admin.trainer.certificates.edit', compact(
            'trainer',
            'context',
            'model',
            'assets'
        ));
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
            'certificate_template' => ['required', 'in:template_1,template_2,template_3'],

            'certificate_logo' => ['nullable', 'array', 'max:3'],
            'certificate_logo.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],

            'certificate_signature_file' => ['nullable', 'array', 'max:3'],
            'certificate_signature_file.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],

            'signature_name' => ['nullable', 'array', 'max:3'],
            'signature_name.*' => ['nullable', 'string', 'max:255'],

            'signature_position' => ['nullable', 'array', 'max:3'],
            'signature_position.*' => ['nullable', 'string', 'max:255'],
        ]);

        TrainerCertificateAsset::query()
            ->where('certifiable_type', get_class($model))
            ->where('certifiable_id', $model->id)
            ->delete();

        foreach (($request->file('certificate_logo') ?? []) as $index => $logo) {
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
                'name' => null,
                'position' => null,
                'image_path' => $path,
                'order_no' => $index + 1,
            ]);
        }

        foreach (($request->file('certificate_signature_file') ?? []) as $index => $file) {
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
                'name' => $request->input("signature_name.$index"),
                'position' => $request->input("signature_position.$index"),
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