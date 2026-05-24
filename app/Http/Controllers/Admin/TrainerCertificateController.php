<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Models\TrainerCertificateAsset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $trainers->getCollection()->transform(function ($trainer) use ($publishedCounts) {
            $publishedKeys = $this->publishedKeys($trainer);

            $pendingEvents = Event::query()
                ->where('trainer_id', $trainer->id)
                ->whereNotNull('event_date')
                ->whereDate('event_date', '<=', now()->toDateString())
                ->get()
                ->filter(fn ($event) => !$publishedKeys->has(Event::class . ':' . $event->id))
                ->count();

            $pendingCourses = Course::query()
                ->where('trainer_id', $trainer->id)
                ->whereIn('status', ['published', 'approved', 'active'])
                ->get()
                ->filter(fn ($course) => !$publishedKeys->has(Course::class . ':' . $course->id))
                ->count();

            $trainer->pending_certificates_count = $pendingEvents + $pendingCourses;
            $trainer->published_certificates_count = (int) ($publishedCounts[$trainer->id] ?? 0);

            return $trainer;
        });

        $pendingItems = Event::query()
            ->whereIn('trainer_id', $trainerIds)
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<=', now()->toDateString())
            ->withCount('registrations')
            ->orderByDesc('event_date')
            ->get();

        $certificates = Course::query()
            ->whereIn('trainer_id', $trainerIds)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->with('category')
            ->withCount('enrollments')
            ->orderByDesc('updated_at')
            ->get();

        $totalTrainers = User::query()
            ->where('role', 'trainer')
            ->count();

        $totalPending = $trainers->getCollection()->sum('pending_certificates_count');

        return view('admin.trainer.certificates.index', compact(
            'trainers',
            'pendingItems',
            'certificates',
            'totalTrainers',
            'totalPending'
        ) + [
            'tab' => $request->query('tab', 'pendingItems'),
        ]);
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
        $this->ensureTrainer($trainer);

        $publishedKeys = $this->publishedKeys($trainer);

        $events = Event::query()
            ->where('trainer_id', $trainer->id)
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<=', now()->toDateString())
            ->withCount('registrations')
            ->orderByDesc('event_date')
            ->get()
            ->filter(fn ($event) => !$publishedKeys->has(Event::class . ':' . $event->id))
            ->map(function ($event) {
                return [
                    'context' => 'event',
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->event_date,
                    'type' => $event->jenis ?? 'Event',
                    'participants_count' => $event->registrations_count ?? 0,
                ];
            });

        $courses = Course::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->withCount('enrollments')
            ->orderByDesc('updated_at')
            ->get()
            ->filter(fn ($course) => !$publishedKeys->has(Course::class . ':' . $course->id))
            ->map(function ($course) {
                return [
                    'context' => 'course',
                    'id' => $course->id,
                    'title' => $course->name,
                    'date' => $course->updated_at,
                    'type' => 'Course',
                    'participants_count' => $course->enrollments_count ?? 0,
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
        $this->ensureTrainer($trainer);

        $model = $this->getCertifiableModel($context, $id);

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
        $this->ensureTrainer($trainer);

        $model = $this->getCertifiableModel($context, $id);

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

            'remove_assets' => ['nullable', 'array'],
            'remove_assets.*' => ['nullable', 'integer'],
        ]);

        $removeIds = array_filter((array) $request->input('remove_assets', []));

        if (!empty($removeIds)) {
            $assetsToRemove = TrainerCertificateAsset::query()
                ->where('certifiable_type', get_class($model))
                ->where('certifiable_id', $model->id)
                ->whereIn('id', $removeIds)
                ->get();

            foreach ($assetsToRemove as $asset) {
                if ($asset->image_path && $asset->image_path !== '-') {
                    Storage::disk('public')->delete($asset->image_path);
                }

                $asset->delete();
            }
        }

        TrainerCertificateAsset::updateOrCreate(
            [
                'certifiable_type' => get_class($model),
                'certifiable_id' => $model->id,
                'type' => 'template',
            ],
            [
                'name' => $request->input('certificate_template'),
                'position' => null,
                'image_path' => '-',
                'order_no' => 0,
            ]
        );

        foreach (($request->file('certificate_logo') ?? []) as $logo) {
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
                'order_no' => $this->nextAssetOrder($model, TrainerCertificateAsset::TYPE_LOGO),
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
                'order_no' => $this->nextAssetOrder($model, TrainerCertificateAsset::TYPE_SIGNATURE),
            ]);
        }

        $existingSignatures = TrainerCertificateAsset::query()
            ->where('certifiable_type', get_class($model))
            ->where('certifiable_id', $model->id)
            ->where('type', TrainerCertificateAsset::TYPE_SIGNATURE)
            ->orderBy('order_no')
            ->get();

        foreach ($existingSignatures as $index => $signature) {
            $signature->update([
                'name' => $request->input("signature_name.$index", $signature->name),
                'position' => $request->input("signature_position.$index", $signature->position),
            ]);
        }

        return redirect()
            ->route('admin.trainer.certificates.show', [
                'trainer' => $trainer->id,
            ])
            ->with('success', 'Konfigurasi sertifikat berhasil disimpan.');
    }

    public function publish(Request $request, User $trainer, string $context, int $id)
    {
        $this->ensureTrainer($trainer);

        $model = $this->getCertifiableModel($context, $id);

        $certificate = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('certifiable_type', get_class($model))
            ->where('certifiable_id', $model->id)
            ->first();

        if (!$certificate) {
            $certificate = TrainerCertificate::create([
                'trainer_id' => $trainer->id,
                'certifiable_type' => get_class($model),
                'certifiable_id' => $model->id,
                'activity_code' => $this->activityCode($model),
                'type_code' => $context === 'event' ? 'EVT' : 'CRS',
                'sequence' => $this->nextSequence($trainer, $model),
                'certificate_number' => $this->generateCertificateNumber($trainer, $model, $context),
                'issued_at' => now(),
                'issued_by' => Auth::id(),
                'status' => 'sent',
                'file_path' => null,
            ]);
        } else {
            $certificate->update([
                'issued_at' => $certificate->issued_at ?? now(),
                'issued_by' => $certificate->issued_by ?? Auth::id(),
                'status' => 'sent',
            ]);
        }

        return redirect()
            ->route('admin.trainer.certificates.detail', [
                'certificate' => $certificate->id,
            ])
            ->with('success', 'Sertifikat berhasil diterbitkan.');
    }

    public function detail(TrainerCertificate $certificate)
    {
        $certificate->load(['trainer', 'issuer', 'certifiable']);

        $model = $certificate->certifiable;

        $assets = collect();

        if ($model) {
            $assets = TrainerCertificateAsset::query()
                ->where('certifiable_type', get_class($model))
                ->where('certifiable_id', $model->id)
                ->orderBy('order_no')
                ->get();
        }

        $trainer = $certificate->trainer;

        return view('admin.trainer.certificates.detail', compact(
            'certificate',
            'trainer',
            'model',
            'assets'
        ));
    }

    private function ensureTrainer(User $trainer): void
    {
        if ($trainer->role !== 'trainer') {
            abort(404);
        }
    }

    private function getCertifiableModel(string $context, int $id)
    {
        if ($context === 'event') {
            return Event::findOrFail($id);
        }

        if ($context === 'course') {
            return Course::findOrFail($id);
        }

        abort(404);
    }

    private function publishedKeys(User $trainer)
    {
        return TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published', 'revoked'])
            ->get()
            ->mapWithKeys(function ($certificate) {
                return [
                    $certificate->certifiable_type . ':' . $certificate->certifiable_id => true,
                ];
            });
    }

    private function nextAssetOrder($model, string $type): int
    {
        return ((int) TrainerCertificateAsset::query()
            ->where('certifiable_type', get_class($model))
            ->where('certifiable_id', $model->id)
            ->where('type', $type)
            ->max('order_no')) + 1;
    }

    private function nextSequence(User $trainer, $model): string
    {
        $total = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('certifiable_type', get_class($model))
            ->count() + 1;

        return str_pad((string) $total, 3, '0', STR_PAD_LEFT);
    }

    private function activityCode($model): string
    {
        $source = $model->title ?? $model->name ?? 'IDSPORA';

        return strtoupper(substr(Str::slug($source, ''), 0, 3)) ?: 'IDS';
    }

    private function generateCertificateNumber(User $trainer, $model, string $context): string
    {
        $activityCode = $this->activityCode($model);
        $typeCode = $context === 'event' ? 'EVT' : 'CRS';
        $year = now()->format('Y');

        $count = TrainerCertificate::query()
            ->whereYear('created_at', $year)
            ->count() + 1;

        $sequence = str_pad((string) $count, 3, '0', STR_PAD_LEFT);

        return "{$activityCode}/{$typeCode}/TRN/{$year}/{$sequence}";
    }
}