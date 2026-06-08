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
use Illuminate\Support\Facades\DB;
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

        // All registered trainer IDs in the system
        $allTrainerIds = User::query()->where('role', 'trainer')->pluck('id');

        // Fetch all published/sent certificates grouped by course/event and trainer
        $publishedCertificates = TrainerCertificate::query()
            ->whereIn('trainer_id', $allTrainerIds)
            ->whereIn('status', ['sent', 'published'])
            ->get()
            ->groupBy(function ($cert) {
                return $cert->certifiable_type . ':' . $cert->certifiable_id . ':' . $cert->trainer_id;
            });

        // Group by certifiable key for legacy view reference
        $publishedKeysGlobal = TrainerCertificate::query()
            ->whereIn('trainer_id', $allTrainerIds)
            ->whereIn('status', ['sent', 'published'])
            ->get()
            ->groupBy(function ($cert) {
                return $cert->certifiable_type . ':' . $cert->certifiable_id;
            });

        // Fetch all certificates (including drafts) for lookup in blade
        $allCertificates = TrainerCertificate::query()
            ->whereIn('trainer_id', $allTrainerIds)
            ->get()
            ->groupBy(function ($cert) {
                return $cert->certifiable_type . ':' . $cert->certifiable_id . ':' . $cert->trainer_id;
            });

        // Calculate pending and published counts for the 10 trainers on the current page
        $pageTrainerIds = $trainers->pluck('id');
        $pagePublishedCerts = TrainerCertificate::query()
            ->whereIn('trainer_id', $pageTrainerIds)
            ->whereIn('status', ['sent', 'published'])
            ->get()
            ->groupBy('trainer_id');

        $trainers->getCollection()->transform(function ($trainer) use ($pagePublishedCerts) {
            $myPublished = $pagePublishedCerts->get($trainer->id) ?: collect();
            $myPublishedKeys = $myPublished->mapWithKeys(function ($cert) {
                return [$cert->certifiable_type . ':' . $cert->certifiable_id => true];
            });

            // Pending events: trainer is primary OR speaker, and event is finished/past, and certificate not published
            $pendingEvents = Event::query()
                ->where(function ($query) use ($trainer) {
                    $query->where('trainer_id', $trainer->id)
                          ->orWhereIn('id', function ($sub) use ($trainer) {
                              $sub->select('event_id')
                                  ->from('event_speakers')
                                  ->where('trainer_id', $trainer->id);
                          });
                })
                ->whereNotNull('event_date')
                ->whereDate('event_date', '<=', now()->toDateString())
                ->get()
                ->filter(fn($event) => !$myPublishedKeys->has(Event::class . ':' . $event->id))
                ->count();

            // Pending courses
            $pendingCourses = Course::query()
                ->where('trainer_id', $trainer->id)
                ->whereIn('status', ['published', 'approved', 'active'])
                ->get()
                ->filter(fn($course) => !$myPublishedKeys->has(Course::class . ':' . $course->id))
                ->count();

            $trainer->pending_certificates_count = $pendingEvents + $pendingCourses;
            $trainer->published_certificates_count = $myPublished->count();

            return $trainer;
        });

        // Fetch all finished events that have at least one trainer associated (primary or speaker)
        $events = Event::query()
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<=', now()->toDateString())
            ->with(['trainer', 'speakers.trainer'])
            ->withCount('registrations')
            ->get()
            ->filter(function ($event) use ($allTrainerIds) {
                if ($event->trainer_id && $allTrainerIds->contains($event->trainer_id)) {
                    return true;
                }
                return $event->speakers->contains(function ($speaker) use ($allTrainerIds) {
                    return $speaker->trainer_id && $allTrainerIds->contains($speaker->trainer_id);
                });
            })
            ->each(function ($item) {
                $item->context = 'event';
                $item->sort_date = $item->event_date;
            });

        // Fetch all active/approved courses
        $courses = Course::query()
            ->whereIn('trainer_id', $allTrainerIds)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->with(['category', 'trainer'])
            ->withCount('enrollments')
            ->get()
            ->each(function ($item) {
                $item->context = 'course';
                $item->sort_date = $item->updated_at;
            });

        // Helper to extract relevant registered trainers for an item
        $getTrainers = function ($item) use ($allTrainerIds) {
            $trainers = collect();
            if ($item->trainer && $allTrainerIds->contains($item->trainer_id)) {
                $trainers->push($item->trainer);
            }
            if ($item instanceof Event) {
                foreach ($item->speakers as $speaker) {
                    if ($speaker->trainer && $allTrainerIds->contains($speaker->trainer_id)) {
                        $trainers->push($speaker->trainer);
                    }
                }
            }
            return $trainers->unique('id');
        };

        // Categorize into unsent and sent items
        $unsentItems = collect();
        $sentItems = collect();

        foreach ($events->concat($courses) as $item) {
            $itemTrainers = $getTrainers($item);
            if ($itemTrainers->isEmpty()) {
                continue;
            }

            $allSent = true;
            foreach ($itemTrainers as $trn) {
                $key = get_class($item) . ':' . $item->id . ':' . $trn->id;
                if (!$publishedCertificates->has($key)) {
                    $allSent = false;
                    break;
                }
            }

            if ($allSent) {
                $sentItems->push($item);
            } else {
                $unsentItems->push($item);
            }
        }

        $unsentItems = $unsentItems->sortByDesc('sort_date')->values();
        $sentItems = $sentItems->sortByDesc('sort_date')->values();

        $totalTrainers = User::query()
            ->where('role', 'trainer')
            ->count();

        // Calculate total pending certificates for all trainers globally
        $totalPending = 0;
        foreach ($events->concat($courses) as $item) {
            $itemTrainers = $getTrainers($item);
            foreach ($itemTrainers as $trn) {
                $key = get_class($item) . ':' . $item->id . ':' . $trn->id;
                if (!$publishedCertificates->has($key)) {
                    $totalPending++;
                }
            }
        }

        // Global total published certificates count
        $totalPublished = TrainerCertificate::query()
            ->whereIn('trainer_id', $allTrainerIds)
            ->whereIn('status', ['sent', 'published'])
            ->count();

        return view('admin.trainer.certificates.index', compact(
            'trainers',
            'unsentItems',
            'sentItems',
            'totalTrainers',
            'totalPending',
            'totalPublished',
            'allCertificates'
        ) + [
            'tab' => $request->query('tab', 'unsentItems'),
            'publishedKeys' => $publishedKeysGlobal
        ]);
    }

    public function show(User $trainer)
    {
        $this->ensureTrainer($trainer);

        $publishedKeys = $this->publishedKeys($trainer);

        $events = Event::query()
            ->where(function ($query) use ($trainer) {
                $query->where('trainer_id', $trainer->id)
                      ->orWhereIn('id', function ($sub) use ($trainer) {
                          $sub->select('event_id')
                              ->from('event_speakers')
                              ->where('trainer_id', $trainer->id);
                      });
            })
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<=', now()->toDateString())
            ->withCount('registrations')
            ->orderByDesc('event_date')
            ->get()
            ->filter(fn($event) => !$publishedKeys->has(Event::class . ':' . $event->id))
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
            ->filter(fn($course) => !$publishedKeys->has(Course::class . ':' . $course->id))
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
            ->with(['trainer', 'issuer', 'certifiable'])
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

        $this->syncTrainerAssetsFromCrm($model, $trainer, $context);

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

            'signature_name' => ['nullable', 'array'],
            'signature_name.*' => ['nullable', 'string', 'max:255'],

            'signature_position' => ['nullable', 'array'],
            'signature_position.*' => ['nullable', 'string', 'max:255'],

            'remove_assets' => ['nullable', 'array'],
            'remove_assets.*' => ['nullable', 'integer'],
        ]);

        $trainerName = mb_strtolower(trim((string) ($trainer->name ?? '')));
        $signatureNames = (array) $request->input('signature_name', []);

        foreach ($signatureNames as $name) {
            $normalized = mb_strtolower(trim((string) $name));
            if ($normalized !== '' && $trainerName !== '' && $normalized === $trainerName) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'signature_name' => 'Nama penandatangan tidak boleh sama dengan nama trainer. Silakan gunakan penandatangan lain.',
                    ]);
            }
        }

        DB::beginTransaction();

        try {
            $certifiableType = get_class($model);
            $certifiableId = $model->id;

            $removeIds = array_filter((array) $request->input('remove_assets', []));

            if (!empty($removeIds)) {
                $assetsToRemove = TrainerCertificateAsset::query()
                    ->where('certifiable_type', $certifiableType)
                    ->where('certifiable_id', $certifiableId)
                    ->whereIn('id', $removeIds)
                    ->get();

                foreach ($assetsToRemove as $asset) {
                    if ($this->shouldDeleteTrainerAsset($asset->image_path)) {
                        Storage::disk('public')->delete($asset->image_path);
                    }

                    $asset->delete();
                }
            }

            TrainerCertificateAsset::updateOrCreate(
                [
                    'certifiable_type' => $certifiableType,
                    'certifiable_id' => $certifiableId,
                    'type' => 'template',
                ],
                [
                    'name' => $request->input('certificate_template'),
                    'position' => null,
                    'image_path' => '-',
                    'order_no' => 0,
                ]
            );

            if ($request->hasFile('certificate_logo')) {
                foreach ($request->file('certificate_logo') as $logo) {
                    if (!$logo) {
                        continue;
                    }

                    $path = $logo->store(
                        'trainer_certificate_assets/' . class_basename($model) . '/' . $certifiableId . '/logos',
                        'public'
                    );

                    TrainerCertificateAsset::create([
                        'certifiable_type' => $certifiableType,
                        'certifiable_id' => $certifiableId,
                        'type' => TrainerCertificateAsset::TYPE_LOGO,
                        'name' => null,
                        'position' => null,
                        'image_path' => $path,
                        'order_no' => $this->nextAssetOrder($model, TrainerCertificateAsset::TYPE_LOGO),
                    ]);
                }
            }

            if ($request->hasFile('certificate_signature_file')) {
                foreach ($request->file('certificate_signature_file') as $index => $file) {
                    if (!$file) {
                        continue;
                    }

                    $path = $file->store(
                        'trainer_certificate_assets/' . class_basename($model) . '/' . $certifiableId . '/signatures',
                        'public'
                    );

                    TrainerCertificateAsset::create([
                        'certifiable_type' => $certifiableType,
                        'certifiable_id' => $certifiableId,
                        'type' => TrainerCertificateAsset::TYPE_SIGNATURE,
                        'name' => $request->input("signature_name.$index"),
                        'position' => $request->input("signature_position.$index"),
                        'image_path' => $path,
                        'order_no' => $this->nextAssetOrder($model, TrainerCertificateAsset::TYPE_SIGNATURE),
                    ]);
                }
            }

            $existingSignatures = TrainerCertificateAsset::query()
                ->where('certifiable_type', $certifiableType)
                ->where('certifiable_id', $certifiableId)
                ->where('type', TrainerCertificateAsset::TYPE_SIGNATURE)
                ->orderBy('order_no')
                ->get();

            foreach ($existingSignatures as $index => $signature) {
                $signature->update([
                    'name' => $request->input("signature_name.$index", $signature->name),
                    'position' => $request->input("signature_position.$index", $signature->position),
                ]);
            }

            $this->syncTrainerAssetsFromCrm($model, $trainer, $context);

            DB::commit();

            $certificate = $this->ensureCertificate($trainer, $model, $context);

            if (!empty($certificate->file_path)) {
                $absolute = storage_path('app/' . $certificate->file_path);
                if (is_file($absolute)) {
                    @unlink($absolute);
                }
                $certificate->update(['file_path' => null]);
            }

            return redirect()
                ->route('admin.trainer.certificates.detail', [
                    'certificate' => $certificate->id,
                ])
                ->with('success', 'Konfigurasi sertifikat berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'upload' => $e->getMessage(),
                ]);
        }
    }

    public function publish(Request $request, User $trainer, string $context, int $id)
    {
        $this->ensureTrainer($trainer);

        $model = $this->getCertifiableModel($context, $id);

        $certificate = $this->ensureCertificate($trainer, $model, $context);

        if (!empty($certificate->file_path)) {
            $absolute = storage_path('app/' . $certificate->file_path);
            if (is_file($absolute)) {
                @unlink($absolute);
            }
        }

        $certificate->update([
            'status' => 'published',
            'issued_at' => now(),
            'file_path' => null,
        ]);

        return redirect()
            ->route('admin.trainer.certificates.detail', ['certificate' => $certificate->id])
            ->with('success', 'Sertifikat berhasil diterbitkan.');
    }

    private function ensureCertificate(User $trainer, $model, string $context): TrainerCertificate
    {
        $certifiableType = get_class($model);

        $this->syncTrainerAssetsFromCrm($model, $trainer, $context);

        $existing = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->where('certifiable_type', $certifiableType)
            ->where('certifiable_id', $model->id)
            ->first();

        $sequence = $existing?->sequence ?: $this->nextSequence($trainer, $model);
        $certificateNumber = $existing?->certificate_number
            ?: $this->generateCertificateNumber($trainer, $model, $context);

        return TrainerCertificate::updateOrCreate(
            [
                'trainer_id' => $trainer->id,
                'certifiable_type' => $certifiableType,
                'certifiable_id' => $model->id,
            ],
            [
                'activity_code' => $this->activityCode($model),
                'type_code' => TrainerCertificate::TYPE_CODE_TRAINER,
                'sequence' => $sequence,
                'certificate_number' => $certificateNumber,
                'issued_at' => $existing?->issued_at,
                'issued_by' => Auth::id(),
                'status' => $existing?->status ?: TrainerCertificate::STATUS_DRAFT,
                'file_path' => null,
            ]
        );
    }

    public function detail(TrainerCertificate $certificate)
    {
        $certificate->load(['trainer', 'issuer', 'certifiable']);

        $model = $certificate->certifiable;
        $trainer = $certificate->trainer;

        $assets = collect();
        $template = 'template_1';
        $logos = collect();
        $signatures = collect();

        if ($model) {
            $context = strtolower(class_basename(get_class($model))) === 'course' ? 'course' : 'event';
            $this->syncTrainerAssetsFromCrm($model, $trainer, $context);

            $assets = TrainerCertificateAsset::query()
                ->where('certifiable_type', get_class($model))
                ->where('certifiable_id', $model->id)
                ->orderByRaw("
                CASE
                    WHEN type = 'template' THEN 1
                    WHEN type = 'logo' THEN 2
                    WHEN type = 'signature' THEN 3
                    ELSE 4
                END
            ")
                ->orderBy('order_no')
                ->get();

            $template = $assets->where('type', 'template')->first()?->name
                ?? $model->certificate_template
                ?? 'template_1';

            $logos = $assets->where('type', TrainerCertificateAsset::TYPE_LOGO)->values();
            $signatures = $assets->where('type', TrainerCertificateAsset::TYPE_SIGNATURE)->values();
        }

        return view('admin.trainer.certificates.detail', compact(
            'certificate',
            'trainer',
            'model',
            'assets',
            'template',
            'logos',
            'signatures'
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

        do {
            $sequence = str_pad((string) $count, 3, '0', STR_PAD_LEFT);
            $number = "{$activityCode}/{$typeCode}/TRN/{$year}/{$sequence}";
            $exists = TrainerCertificate::query()
                ->where('certificate_number', $number)
                ->exists();
            $count++;
        } while ($exists);

        return $number;
    }

    private function syncTrainerAssetsFromCrm($model, User $trainer, string $context): void
    {
        $certifiableType = get_class($model);
        $certifiableId = (int) $model->id;

        // Skip sync if assets already exist for this certifiable
        $exists = TrainerCertificateAsset::query()
            ->where('certifiable_type', $certifiableType)
            ->where('certifiable_id', $certifiableId)
            ->exists();
        if ($exists) {
            return;
        }

        $rawTemplate = $model->getAttribute('certificate_template');
        $hasCrmTemplate = is_string($rawTemplate) && trim($rawTemplate) !== '';
        $hasCrmLogos = !empty($model->certificate_logo);
        $hasCrmSignatures = !empty($model->certificate_signature);

        if (!$hasCrmTemplate && !$hasCrmLogos && !$hasCrmSignatures) {
            return;
        }

        $crmTemplate = $hasCrmTemplate ? $rawTemplate : 'template_1';
        $trainerTemplate = $this->mapTrainerTemplate($crmTemplate, $context);

        $crmLogos = is_array($model->certificate_logo)
            ? $model->certificate_logo
            : ($model->certificate_logo ? [$model->certificate_logo] : []);

        $crmSignaturesRaw = is_array($model->certificate_signature)
            ? $model->certificate_signature
            : ($model->certificate_signature ? [$model->certificate_signature] : []);

        $trainerName = mb_strtolower(trim((string) ($trainer->name ?? '')));

        $crmSignatures = [];
        foreach ($crmSignaturesRaw as $sig) {
            $image = '';
            $name = '';
            $position = '';

            if (is_array($sig)) {
                $image = (string) ($sig['image'] ?? '');
                $name = (string) ($sig['name'] ?? '');
                $position = (string) ($sig['position'] ?? '');
            } else {
                $image = (string) $sig;
            }

            if ($trainerName !== '' && mb_strtolower(trim($name)) === $trainerName) {
                continue;
            }

            if (trim($image) === '') {
                continue;
            }

            $crmSignatures[] = [
                'image' => $this->normalizeCrmPath($image),
                'name' => $name,
                'position' => $position,
            ];
        }

        $crmLogos = array_values(array_filter(array_map(function ($logo) {
            return $this->normalizeCrmPath((string) $logo);
        }, $crmLogos)));

        DB::transaction(function () use ($certifiableType, $certifiableId, $trainerTemplate, $crmLogos, $crmSignatures) {
            $existingAssets = TrainerCertificateAsset::query()
                ->where('certifiable_type', $certifiableType)
                ->where('certifiable_id', $certifiableId)
                ->whereIn('type', ['template', TrainerCertificateAsset::TYPE_LOGO, TrainerCertificateAsset::TYPE_SIGNATURE])
                ->get();

            foreach ($existingAssets as $asset) {
                if ($this->shouldDeleteTrainerAsset($asset->image_path)) {
                    Storage::disk('public')->delete($asset->image_path);
                }
                $asset->delete();
            }

            TrainerCertificateAsset::create([
                'certifiable_type' => $certifiableType,
                'certifiable_id' => $certifiableId,
                'type' => 'template',
                'name' => $trainerTemplate,
                'position' => null,
                'image_path' => '-',
                'order_no' => 0,
            ]);

            $orderNo = 1;
            foreach (array_slice($crmLogos, 0, 3) as $logoPath) {
                TrainerCertificateAsset::create([
                    'certifiable_type' => $certifiableType,
                    'certifiable_id' => $certifiableId,
                    'type' => TrainerCertificateAsset::TYPE_LOGO,
                    'name' => null,
                    'position' => null,
                    'image_path' => $logoPath,
                    'order_no' => $orderNo++,
                ]);
            }

            foreach (array_slice($crmSignatures, 0, 3) as $signature) {
                TrainerCertificateAsset::create([
                    'certifiable_type' => $certifiableType,
                    'certifiable_id' => $certifiableId,
                    'type' => TrainerCertificateAsset::TYPE_SIGNATURE,
                    'name' => $signature['name'] ?: null,
                    'position' => $signature['position'] ?: null,
                    'image_path' => $signature['image'],
                    'order_no' => $orderNo++,
                ]);
            }
        });
    }

    private function normalizeCrmPath(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');
        $normalized = preg_replace('~^storage/~i', '', $normalized) ?? $normalized;
        return $normalized;
    }

    private function shouldDeleteTrainerAsset(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        return str_contains($path, 'trainer_certificate_assets/');
    }

    private function mapTrainerTemplate(?string $crmTemplate, string $context): string
    {
        $template = $crmTemplate ?: 'template_1';
        if (str_contains($template, '_trainer')) {
            return $template;
        }

        return $template;
    }

    public function viewCertificateFile(\App\Models\TrainerCertificate $certificate)
    {
        if (empty($certificate->file_path)) {
            abort(404);
        }

        $absolutePath = storage_path('app/' . $certificate->file_path);
        if (!is_file($absolutePath)) {
            abort(404);
        }

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($absolutePath) . '"'
        ]);
    }
}