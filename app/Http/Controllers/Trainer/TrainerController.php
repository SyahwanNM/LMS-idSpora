<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Feedback;
use App\Models\Quiz;
use App\Models\TrainerAssignment;
use App\Models\TrainerNotification;
use App\Services\TrainerActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Event;
use App\Models\TrainerCertificate;
use App\Models\TrainerCertificateAsset;
use App\Services\CourseTemplateCloneService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Str;

class TrainerController extends Controller
{
    private function latestEventInvitation(int $eventId, int $trainerId): ?TrainerNotification
    {
        if ($eventId <= 0 || $trainerId <= 0) {
            return null;
        }

        return TrainerNotification::query()
            ->where('trainer_id', $trainerId)
            ->where('type', 'event_invitation')
            ->where(function ($query) use ($eventId) {
                $query->where('data', 'like', '%"entity_id":' . $eventId . '%');
            })
            ->latest('id')
            ->first();
    }

    private function latestEventAssignment(int $eventId, int $trainerId): ?TrainerAssignment
    {
        if ($eventId <= 0 || $trainerId <= 0) {
            return null;
        }

        return TrainerAssignment::query()
            ->where('event_id', $eventId)
            ->where('trainer_id', $trainerId)
            ->latest('id')
            ->first();
    }

    private function trainerEventQuery(int $trainerId, string $trainerName = '', bool $includeLegacySpeakerMatch = false): \Illuminate\Database\Eloquent\Builder
    {
        return Event::query()->where(function ($query) use ($trainerId, $trainerName, $includeLegacySpeakerMatch) {
            $query->where('trainer_id', $trainerId)
                ->orWhereHas('speakers', function ($speakerQuery) use ($trainerId) {
                    $speakerQuery->where('trainer_id', $trainerId);
                })
                ->orWhereHas('trainerAssignments', function ($assignmentQuery) use ($trainerId) {
                    $assignmentQuery->where('trainer_id', $trainerId)
                        ->where('status', 'accepted');
                });

            if ($includeLegacySpeakerMatch && $trainerName !== '') {
                $query->orWhere('speaker', 'like', '%' . $trainerName . '%');
            }
        });
    }

    private function parseTrainerSpeakerNames(?string $speaker): array
    {
        $speaker = trim((string) $speaker);
        if ($speaker === '') {
            return [];
        }

        if (str_contains($speaker, '|')) {
            $parts = preg_split('/\s*\|\s*/', $speaker) ?: [];
        } elseif (preg_match('/\R/', $speaker)) {
            $parts = preg_split('/\R+/', $speaker) ?: [];
        } elseif (str_contains($speaker, ';')) {
            $parts = preg_split('/\s*;\s*/', $speaker) ?: [];
        } else {
            $parts = [$speaker];
        }

        return collect($parts)
            ->map(fn($name) => mb_strtolower(trim((string) $name)))
            ->filter(fn($name) => $name !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function trainerMatchesEvent(Event $event, int $trainerId, string $trainerName = ''): bool
    {
        if ($trainerId <= 0) {
            return false;
        }

        if ((int) ($event->trainer_id ?? 0) === $trainerId) {
            return true;
        }

        $speakerMatch = $event->relationLoaded('speakers')
            ? $event->speakers->contains(fn($speaker) => (int) ($speaker->trainer_id ?? 0) === $trainerId)
            : $event->speakers()->where('trainer_id', $trainerId)->exists();

        if ($speakerMatch) {
            return true;
        }

        $assignmentMatch = $event->relationLoaded('trainerAssignments')
            ? $event->trainerAssignments->contains(fn($assignment) => (int) ($assignment->trainer_id ?? 0) === $trainerId && (string) ($assignment->status ?? '') === 'accepted')
            : $event->trainerAssignments()->where('trainer_id', $trainerId)->where('status', 'accepted')->exists();

        if ($assignmentMatch) {
            return true;
        }

        if ($trainerName === '') {
            return false;
        }

        return in_array(mb_strtolower($trainerName), $this->parseTrainerSpeakerNames($event->speaker), true);
    }

    private function ensureEventSpeakersSynced(Event $event): void
    {
        if (empty($event->speaker)) {
            return;
        }

        // If event_speakers already exist for this event, do nothing
        if ($event->speakers()->exists()) {
            return;
        }

        // Otherwise, parse the speaker field and create event_speakers
        $speakerNames = $this->parseTrainerSpeakerNames($event->speaker);
        foreach ($speakerNames as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') continue;

            $trainer = \App\Models\User::where('role', 'trainer')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                ->first();

            \App\Models\EventSpeaker::create([
                'event_id'   => $event->id,
                'trainer_id' => $trainer?->id,
                'name'       => $name,
                'salary'     => 0,
                'order'      => $i,
            ]);
        }
    }

    public function ensureEventInvitationsExistForTrainer($trainer): void
    {
        if (!$trainer || $trainer->role !== 'trainer') {
            return;
        }

        $trainerId = (int) $trainer->id;
        $trainerName = (string) ($trainer->name ?? '');

        // 1. Get all event IDs for which this trainer already has an event_invitation notification
        $existingNotificationEventIds = TrainerNotification::query()
            ->where('trainer_id', $trainerId)
            ->where('type', 'event_invitation')
            ->get()
            ->map(function (TrainerNotification $notification) {
                return (int) data_get($notification->data, 'entity_id', 0);
            })
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        // 2. Query all events where the trainer is assigned (either trainer_id = trainerId or speaker contains trainerName)
        // but which DO NOT have an invitation notification.
        $missingEventsQuery = Event::query();
        if (!empty($existingNotificationEventIds)) {
            $missingEventsQuery->whereNotIn('id', $existingNotificationEventIds);
        }

        $missingEvents = $missingEventsQuery->where(function ($query) use ($trainerId, $trainerName) {
            $query->where('trainer_id', $trainerId);
            if ($trainerName !== '') {
                $query->orWhere('speaker', 'like', '%' . $trainerName . '%');
            }
        })->get();

        // 3. For each missing event, verify match, sync speakers, and create the TrainerNotification
        foreach ($missingEvents as $event) {
            if (!$this->trainerMatchesEvent($event, $trainerId, $trainerName)) {
                continue;
            }

            // Sync event speakers if missing (e.g. for duplicated events)
            $this->ensureEventSpeakersSynced($event);

            $source = ((int) ($event->trainer_id ?? 0) === $trainerId) ? 'trainer_id' : 'speaker_match';
            
            // Create the invitation notification
            TrainerNotification::create([
                'trainer_id' => $trainerId,
                'type' => 'event_invitation',
                'title' => 'Undangan Menjadi Narasumber Event',
                'message' => 'Anda diundang menjadi narasumber untuk event "' . $event->title . '".',
                'invitation_status' => 'pending',
                'data' => [
                    'entity_type' => 'event',
                    'entity_id' => $event->id,
                    'url' => route('trainer.events.show', $event->id),
                    'invitation_status' => 'pending',
                    'invitation_source' => $source,
                    'due_at' => $event->material_deadline ? Carbon::parse($event->material_deadline)->toIso8601String() : now()->addDays(7)->toIso8601String(),
                    'material_deadline' => $event->material_deadline ? Carbon::parse($event->material_deadline)->toIso8601String() : null,
                ],
            ]);
        }
    }

    private function resolveEventSpeakerSalary(Event $event, int $trainerId): float
    {
        if ($trainerId <= 0) {
            return 0;
        }

        $speakers = $event->relationLoaded('speakers')
            ? $event->speakers
            : $event->speakers()->get();

        $byTrainer = $speakers->first(fn ($speaker) => (int) ($speaker->trainer_id ?? 0) === $trainerId);
        if ($byTrainer && (float) ($byTrainer->salary ?? 0) > 0) {
            return (float) $byTrainer->salary;
        }

        $trainerName = mb_strtolower(trim((string) (\App\Models\User::query()->whereKey($trainerId)->value('name') ?? '')));
        if ($trainerName !== '') {
            $byName = $speakers->first(
                fn ($speaker) => mb_strtolower(trim((string) ($speaker->name ?? ''))) === $trainerName
            );
            if ($byName && (float) ($byName->salary ?? 0) > 0) {
                return (float) $byName->salary;
            }
        }

        return 0;
    }

    private function resolveEventCompensation(Event $event, int $trainerId, ?TrainerAssignment $assignment = null): array
    {
        $activeParticipants = (int) ($event->active_participants_count ?? $event->participants_count ?? 0);
        if ($activeParticipants <= 0 && method_exists($event, 'registrations')) {
            $activeParticipants = (int) $event->registrations()
                ->where('status', 'active')
                ->count();
        }

        if (!$assignment && $trainerId > 0) {
            $assignment = $this->latestEventAssignment((int) $event->id, $trainerId);
        }

        $schemePercent = (int) ($assignment?->getSchemePercentage() ?? 0);
        $isFallbackToEventPrice = false;

        if ($schemePercent <= 0 && $trainerId > 0) {
            $acceptedNotification = TrainerNotification::query()
                ->where('trainer_id', $trainerId)
                ->where('type', 'event_invitation')
                ->where(function ($query) use ($event) {
                    $query->where('data', 'like', '%"entity_id":' . (int) $event->id . '%');
                })
                ->orderByDesc('id')
                ->first();

            $notificationScheme = (int) data_get($acceptedNotification?->data ?? [], 'scheme_type', 0);
            $notificationStatus = (string) data_get($acceptedNotification?->data ?? [], 'invitation_status', (string) ($acceptedNotification?->invitation_status ?? ''));

            if ($notificationScheme > 0 && in_array($notificationStatus, ['accepted', 'completed'], true)) {
                $schemePercent = (int) (TrainerAssignment::getSchemeDefinitions()[$notificationScheme]['percentage'] ?? 0);
            }
        }

        $eventPrice = (float) ($event->price ?? 0);

        if ($schemePercent <= 0 && $eventPrice > 0) {
            // Fallback for legacy/main-trainer events that do not have assignment scheme yet.
            $isFallbackToEventPrice = true;
        }

        $feePerParticipant = ($eventPrice > 0 && $schemePercent > 0)
            ? round(($eventPrice * $schemePercent) / 100, 2)
            : ($isFallbackToEventPrice ? round($eventPrice, 2) : 0);
        $speakerSalary = $this->resolveEventSpeakerSalary($event, $trainerId);
        $feeTrainer = $speakerSalary > 0 ? $speakerSalary : $feePerParticipant;
        $estimatedFee = $speakerSalary > 0
            ? round($speakerSalary, 2)
            : (($activeParticipants > 0 && $feePerParticipant > 0)
                ? round($activeParticipants * $feePerParticipant, 2)
                : 0);

        return [
            'scheme_percent' => $schemePercent,
            'event_price' => $eventPrice,
            'active_participants_count' => $activeParticipants,
            'speaker_salary' => $speakerSalary,
            'fee_per_participant' => $feePerParticipant,
            'fee_trainer' => $feeTrainer,
            'fee_trainer_type' => $speakerSalary > 0 ? 'flat' : 'per_participant',
            'estimated_fee' => $estimatedFee,
            'is_fallback_to_event_price' => $isFallbackToEventPrice,
        ];
    }

    private function ensureEventAssignmentForTrainer(Event $event, $trainer): ?TrainerAssignment
    {
        if (!$trainer) {
            return null;
        }

        // Allow primary trainer to also have a TrainerAssignment record
        // so their submissions are tracked and show up in the admin queue.

        $existing = $this->latestEventAssignment((int) $event->id, (int) $trainer->id);
        if ($existing) {
            return $existing;
        }

        $invitation = $this->latestEventInvitation((int) $event->id, (int) $trainer->id);
        $invitationStatus = $invitation ? (string) $invitation->effectiveInvitationStatus() : '';

        // Backward compatibility: for legacy multi-speaker events without assignment records,
        // create accepted assignment so each trainer has isolated material/status.
        $assignmentStatus = in_array($invitationStatus, ['rejected', 'expired'], true) ? $invitationStatus : 'accepted';

        return TrainerAssignment::query()->create([
            'trainer_id' => (int) $trainer->id,
            'event_id' => (int) $event->id,
            'invitation_notification_id' => $invitation ? (int) $invitation->id : null,
            'status' => $assignmentStatus,
            'scheme_type' => null,
            'sla_upload_deadline' => $event->material_deadline ?: now()->addDays(3),
        ]);
    }

    private function latestCourseInvitation(int $courseId, int $trainerId): ?TrainerNotification
    {
        if ($courseId <= 0 || $trainerId <= 0) {
            return null;
        }

        return TrainerNotification::query()
            ->where('trainer_id', $trainerId)
            ->where('type', 'course_invitation')
            ->where(function ($query) use ($courseId) {
                $query->where('data', 'like', '%"entity_id":' . $courseId . '%');
            })
            ->orderByDesc('id')
            ->first();
    }

    private function isCourseMaterialLockedForTrainer(int $courseId, int $trainerId): bool
    {
        $course = \App\Models\Course::find($courseId);
        if ($course && empty($course->trainer_contribution_scheme)) {
            return true;
        }

        $invitation = $this->latestCourseInvitation($courseId, $trainerId);
        if (!$invitation) {
            return false;
        }

        return $invitation->effectiveInvitationStatus() !== 'accepted';
    }

    private function ensureTrainerCertificatesSynced($trainer): void
    {
        $trainerId = (int) ($trainer->id ?? 0);
        if ($trainerId <= 0) {
            return;
        }

        $finishedEvents = Event::query()
            ->where(function ($query) use ($trainerId) {
                $query->where('trainer_id', $trainerId)
                      ->orWhereIn('id', function ($sub) use ($trainerId) {
                          $sub->select('event_id')
                              ->from('event_speakers')
                              ->where('trainer_id', $trainerId);
                      });
            })
            ->whereNotNull('event_date')
            ->whereDate('event_date', '<=', now()->toDateString())
            ->get(['id', 'title', 'event_date', 'jenis', 'certificate_logo', 'certificate_signature']);

        $finishedCourses = Course::query()
            ->where('trainer_id', $trainerId)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->get(['id', 'name', 'approved_at', 'status']);

        $existing = TrainerCertificate::query()
            ->where('trainer_id', $trainerId)
            ->whereIn('status', ['sent', 'published'])
            ->where(function ($q) {
                $q->where('certifiable_type', Event::class)
                    ->orWhere('certifiable_type', Course::class);
            })
            ->get(['certifiable_type', 'certifiable_id'])
            ->mapWithKeys(function ($cert) {
                return [((string) $cert->certifiable_type) . ':' . (int) $cert->certifiable_id => true];
            });

        foreach ($finishedEvents as $event) {
            $key = Event::class . ':' . (int) $event->id;
            if (!isset($existing[$key])) {
                $this->issueAutoTrainerCertificate($trainer, $event, 'event');
                $existing[$key] = true;
            }
        }

        foreach ($finishedCourses as $course) {
            $key = Course::class . ':' . (int) $course->id;
            if (!isset($existing[$key])) {
                $this->issueAutoTrainerCertificate($trainer, $course, 'course');
                $existing[$key] = true;
            }
        }
    }

    private function issueAutoTrainerCertificate($trainer, $certifiable, string $context): void
    {
        $trainerId = (int) ($trainer->id ?? 0);
        if ($trainerId <= 0) {
            return;
        }

        $certifiableType = $context === 'event' ? Event::class : Course::class;
        $certifiableId = (int) ($certifiable->id ?? 0);
        if ($certifiableId <= 0) {
            return;
        }

        $trainerCertificate = TrainerCertificate::query()
            ->where('trainer_id', $trainerId)
            ->where('certifiable_type', $certifiableType)
            ->where('certifiable_id', $certifiableId)
            ->first();

        if ($trainerCertificate && in_array($trainerCertificate->status, ['sent', 'published'])) {
            return;
        }

        $issuedAt = now();
        if ($context === 'event' && !empty($certifiable->event_date)) {
            $issuedAt = Carbon::parse($certifiable->event_date)->endOfDay();
        } elseif ($context === 'course' && !empty($certifiable->approved_at)) {
            $issuedAt = Carbon::parse($certifiable->approved_at);
        }

        $activityCodeMap = [
            'webinar' => 'WBN',
            'seminar' => 'SMN',
            'workshop' => 'WRT',
            'training' => 'WRT',
            'video' => 'VDP',
            'e-learning' => 'ELR',
            'elearning' => 'ELR',
        ];

        $activityCode = $context === 'event'
            ? ($activityCodeMap[strtolower((string) ($certifiable->jenis ?? ''))] ?? 'WBN')
            : 'ELR';
        $typeCode = 'TRN';

        if ($trainerCertificate) {
            $sequence = $trainerCertificate->sequence;
            $certificateNumber = $trainerCertificate->certificate_number;
            $issuedAt = $trainerCertificate->issued_at ?: $issuedAt;
        } else {
            // Get the maximum sequence for this period to avoid duplicates
            $monthYear = $issuedAt->format('m-Y');
            $maxSequence = TrainerCertificate::query()
                ->where('trainer_id', $trainerId)
                ->where('activity_code', strtoupper($activityCode))
                ->where('type_code', $typeCode)
                ->whereRaw("date_format(issued_at, '%m-%Y') = ?", [$monthYear])
                ->max(\DB::raw("CAST(SUBSTRING(sequence, -3) AS UNSIGNED)")) ?? 0;

            $sequenceNum = $maxSequence + 1;
            $sequence = str_pad((string) $sequenceNum, 3, '0', STR_PAD_LEFT);

            $certificateNumber = $this->buildIdsporaCertificateNumber($activityCode, $typeCode, $sequence, $issuedAt);

            // Check if certificate already exists - skip if it does
            $existingCert = TrainerCertificate::where('certificate_number', $certificateNumber)->first();
            if ($existingCert) {
                return;
            }
        }

        if ($trainerCertificate) {
            $trainerCertificate->update([
                'status' => 'sent',
                'issued_at' => $issuedAt,
                'issued_by' => null,
            ]);
        } else {
            $trainerCertificate = TrainerCertificate::create([
                'trainer_id' => $trainerId,
                'certifiable_type' => $certifiableType,
                'certifiable_id' => $certifiableId,
                'activity_code' => strtoupper($activityCode),
                'type_code' => $typeCode,
                'sequence' => $sequence,
                'certificate_number' => $certificateNumber,
                'issued_at' => $issuedAt,
                'issued_by' => null,
                'status' => 'sent',
            ]);
        }

        [$logosBase64, $signaturesBase64, $signaturesData, $template] = $this->extractTrainerAssetsBase64($certifiable);

        $pdfData = [
            'context' => $context,
            'event' => $context === 'event' ? $certifiable : null,
            'course' => $context === 'course' ? $certifiable : null,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'signaturesData' => $signaturesData,
            'template' => $template,
            'roleLabel' => $this->certificateTypeLabel($typeCode),
        ];

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $dompdf->setOptions($options);
        $html = view('trainer.certificates.certificate-pdf', $pdfData)->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $relativeDir = 'trainer_certificates/' . $trainerId . '/' . $context . '/' . $certifiableId;
        $filename = Str::slug($certificateNumber, '_') . '.pdf';
        $relativePath = $relativeDir . '/' . $filename;
        $absolutePath = storage_path('app/' . $relativePath);

        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }
        file_put_contents($absolutePath, $dompdf->output());
        $trainerCertificate->update(['file_path' => $relativePath]);

        $contextLabel = $context === 'event' ? 'event' : 'course';
        $contextTitle = $context === 'event'
            ? (string) ($certifiable->title ?? '')
            : (string) ($certifiable->name ?? '');

        TrainerNotification::create([
            'trainer_id' => $trainerId,
            'type' => 'certificate_issued',
            'title' => 'Sertifikat otomatis diterbitkan',
            'message' => 'Sertifikat untuk ' . $contextLabel . ($contextTitle !== '' ? ' "' . $contextTitle . '"' : '') . ' sudah tersedia. No: ' . $certificateNumber,
            'data' => [
                'entity_type' => $context,
                'entity_id' => $certifiableId,
                'certificate_number' => $certificateNumber,
                'url' => route('trainer.certificates.index') . '?context=' . $context . '&id=' . $certifiableId,
            ],
            'expires_at' => now()->addDays(30),
        ]);
    }

    private function mapStudioMaterialModule(int $courseId, \App\Models\CourseModule $module): array
    {
        return [
            'module_id' => (int) $module->id,
            'order_no' => (int) $module->order_no,
            'type' => (string) $module->type,
            'title' => (string) ($module->title ?? ''),
            'file_name' => (string) ($module->file_name ?: basename((string) $module->content_url)),
            'view_url' => route('trainer.courses.studio.material.view', [$courseId, $module->id]),
            'updated_at' => optional($module->updated_at)->toDateTimeString(),
        ];
    }

    private function ensureTemplateStructureExists(Course $course): void
    {
        // Kalau sudah ada modul, tidak perlu clone
        if ((int) $course->modules()->count() > 0) {
            return;
        }

        $template = \App\Models\CourseTemplate::find($course->template_id);
        if (!$template) {
            $template = \App\Models\CourseTemplate::where('level', $course->level)->first();
            if (!$template)
                return;
        }

        // Assign template ke course jika belum ada
        if ((int) ($course->template_id ?? 0) <= 0) {
            $course->template_id = $template->id;
            $course->template_version = $template->version;
            $course->saveQuietly();
        }

        $unitCount = (int) $course->units()->count();

        if ($unitCount > 0) {
            app(\App\Services\CourseTemplateCloneService::class)
                ->cloneSlotsByUnitCount($course, $template, $unitCount);
        } else {
            app(\App\Services\CourseTemplateCloneService::class)
                ->cloneToCourse($course, $template, replaceExisting: false);
        }
    }

    private function ensureQuizSlotPerUnit(Course $course): void
    {
        $modules = CourseModule::where('course_id', $course->id)
            ->with(['quizQuestions.answers'])
            ->orderBy('order_no', 'asc')
            ->get();

        if ($modules->isEmpty()) {
            return;
        }

        $unitChunks = $modules->chunk(3)->values();

        foreach ($unitChunks as $chunk) {
            $chunk = $chunk->values();
            $expectedTypes = ['pdf', 'video', 'quiz'];

            foreach ($expectedTypes as $index => $expectedType) {
                $slotModule = $chunk->get($index);
                if (!($slotModule instanceof CourseModule)) {
                    continue;
                }

                $currentType = (string) $slotModule->type;
                if ($currentType === $expectedType) {
                    continue;
                }

                $updates = ['type' => $expectedType];

                if ($expectedType === 'quiz') {
                    // Quiz slot must not keep material file payload.
                    $updates['content_url'] = '';
                    $updates['file_name'] = null;
                    $updates['mime_type'] = null;
                    $updates['file_size'] = 0;
                }

                $slotModule->update($updates);

                if ($currentType === 'quiz' && $expectedType !== 'quiz') {
                    // Old quiz slot is converted to material slot; remove stale quiz content.
                    $slotModule->quizQuestions()->delete();
                }
            }
        }
    }

    /**
     * Show trainer profile with their courses
     */
    public function dashboard()
    {
        $user = Auth::user();
        $this->ensureEventInvitationsExistForTrainer($user);
        $activityService = app(TrainerActivityService::class);
        $trainerActivity = $activityService->refresh($user);
        $availableContributionSchemes = $activityService->availableContributionSchemes($user);

        // Get pending invitation course IDs to exclude them from displaying
        $pendingInvitationCourseIds = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->where('type', 'course_invitation')
            ->where('invitation_status', 'pending')
            ->pluck('data')
            ->map(function ($data) {
                return (int) data_get($data, 'entity_id', 0);
            })
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $coursesQuery = $user->coursesAsTrainer()
            ->whereNotIn('id', $pendingInvitationCourseIds);

        $priorityCourses = (clone $coursesQuery)
            ->withCount([
                'enrollments' => function ($query) {
                    $query->where('enrollments.status', 'active');
                }
            ])
            ->orderByRaw("CASE
                WHEN status IN ('archive', 'archived', 'draft') THEN 0
                WHEN status IN ('published', 'approved', 'active') THEN 1
                ELSE 2
            END")
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $totalCourses = (clone $coursesQuery)->count();
        $attentionCourseCount = (clone $coursesQuery)
            ->whereIn('status', ['archive', 'archived', 'draft'])
            ->count();

        $activeCourseCount = (clone $coursesQuery)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->count();

        // Get pending invitation event IDs to exclude them from displaying
        $pendingInvitationEventIds = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->where('type', 'event_invitation')
            ->where('invitation_status', 'pending')
            ->pluck('data')
            ->map(function ($data) {
                return (int) data_get($data, 'entity_id', 0);
            })
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $activeEventsQuery = $this->trainerEventQuery((int) $user->id, (string) ($user->name ?? ''), true)
            ->whereNotIn('id', $pendingInvitationEventIds)
            ->whereNotNull('event_date');

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            $activeEventsQuery->whereRaw("datetime(date(event_date, 'localtime') || ' ' || COALESCE(event_time_end, COALESCE(event_time, '23:59:59'))) >= ?", [now()->format('Y-m-d H:i:s')]);
        } else {
            $activeEventsQuery->whereRaw("TIMESTAMP(event_date, COALESCE(event_time_end, COALESCE(event_time, '23:59:59'))) >= ?", [now()->format('Y-m-d H:i:s')]);
        }

        $priorityEvents = (clone $activeEventsQuery)
            ->withCount([
                'registrations as participants_count' => function ($q) {
                    $q->where('status', 'active');
                }
            ])
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->limit(4)
            ->get();

        $activeEventCount = (clone $activeEventsQuery)->count();

        $todoMaterials = (clone $activeEventsQuery)
            ->whereDoesntHave('trainerModules', function ($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->orderBy('event_date', 'asc')
            ->orderBy('event_time', 'asc')
            ->limit(4)
            ->get();

        $students = $user->trainerEnrollments()
            ->with(['user', 'course'])
            ->orderBy('enrollments.created_at', 'desc')
            ->limit(5)
            ->get();

        $totalStudents = $user->trainerEnrollments()
            ->where('enrollments.status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        $pendingInvitationItems = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByRaw('CASE WHEN read_at IS NULL THEN 0 ELSE 1 END')
            ->orderByDesc('created_at')
            ->limit(40)
            ->get()
            ->filter(function ($notification) {
                $statusFromData = trim((string) data_get($notification->data, 'invitation_status', ''));
                $statusFromColumn = trim((string) ($notification->invitation_status ?? ''));
                $effectiveStatus = $statusFromData !== '' ? $statusFromData : ($statusFromColumn !== '' ? $statusFromColumn : 'pending');
                return $effectiveStatus === 'pending';
            })
            ->take(5)
            ->values();

        $todoConfirmations = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->filter(function ($notification) {
                $status = (string) data_get($notification->data, 'invitation_status', 'pending');
                return $status === 'pending';
            })
            ->take(4)
            ->values();

        $unreadInvitationCount = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->whereIn('type', ['course_invitation', 'event_invitation'])
            ->whereNull('read_at')
            ->get()
            ->filter(function ($notification) {
                $statusFromData = trim((string) data_get($notification->data, 'invitation_status', ''));
                $statusFromColumn = trim((string) ($notification->invitation_status ?? ''));
                $effectiveStatus = $statusFromData !== '' ? $statusFromData : ($statusFromColumn !== '' ? $statusFromColumn : 'pending');
                return $effectiveStatus === 'pending';
            })
            ->count();

        $teachingHistory = TrainerCertificate::query()
            ->where('trainer_id', $user->id)
            ->whereIn('status', ['sent', 'published'])
            ->with('certifiable')
            ->latest('issued_at')
            ->limit(3)
            ->get();

        foreach ($teachingHistory as $cert) {
            $model = $cert->certifiable;
            if ($model) {
                [$logosBase64, $signaturesBase64, $signaturesData, $template] = $this->extractTrainerAssetsBase64($model);
                $cert->logosBase64 = $logosBase64;
                $cert->signaturesBase64 = $signaturesBase64;
                $cert->signaturesData = $signaturesData;
                $cert->template = $template;
            }
        }

        $activeAssignmentItems = TrainerAssignment::query()
            ->where('trainer_id', $user->id)
            ->where('status', 'accepted')
            ->with([
                'event' => function ($query) {
                    $query->withCount([
                        'registrations as active_participants_count' => function ($registrationQuery) {
                            $registrationQuery->where('status', 'active');
                        }
                    ]);
                }
            ])
            ->orderByRaw('CASE WHEN sla_upload_deadline IS NULL THEN 1 ELSE 0 END')
            ->orderBy('sla_upload_deadline')
            ->limit(6)
            ->get()
            ->map(function (TrainerAssignment $assignment) {
                $schemeDefinitions = TrainerAssignment::getSchemeDefinitions();
                $schemeType = (int) ($assignment->scheme_type ?? 0);
                $scheme = $schemeDefinitions[$schemeType] ?? [];
                $event = $assignment->event;
                $eventPrice = (float) ($event->price ?? 0);
                $activeParticipants = (int) ($event->active_participants_count ?? 0);
                $schemePercent = (int) ($scheme['percentage'] ?? 0);
                $feePerParticipant = $eventPrice > 0 && $schemePercent > 0
                    ? round(($eventPrice * $schemePercent) / 100, 2)
                    : 0;
                $estimatedFee = $activeParticipants > 0 && $feePerParticipant > 0
                    ? round($activeParticipants * $feePerParticipant, 2)
                    : 0;

                return [
                    'assignment' => $assignment,
                    'event_title' => (string) optional($event)->title,
                    'event_date' => optional($event?->event_date)?->format('d M Y'),
                    'scheme_label' => (string) ($scheme['label'] ?? 'Skema'),
                    'scheme_percent' => $schemePercent,
                    'event_price' => $eventPrice,
                    'active_participants_count' => $activeParticipants,
                    'fee_per_participant' => $feePerParticipant,
                    'estimated_fee' => $estimatedFee,
                    'deadline' => $assignment->sla_upload_deadline,
                    'remaining_hours' => $assignment->getRemainingHours(),
                    'status_label' => match ((string) $assignment->status) {
                        'accepted' => 'Berjalan',
                        'completed' => 'Selesai',
                        'rejected' => 'Ditolak',
                        'expired' => 'Expired',
                        default => 'Pending',
                    },
                ];
            })
            ->values();

        $revenueCourseItems = (clone $coursesQuery)
            ->withCount([
                'enrollments as monthly_active_students_count' => function ($query) {
                    $query->where('status', 'active')
                        ->whereYear('created_at', now()->year)
                        ->whereMonth('created_at', now()->month);
                },
            ])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get()
            ->map(function (Course $course) {
                $activeStudents = (int) ($course->monthly_active_students_count ?? 0);
                $price = (float) ($course->price ?? 0);
                $schemePercent = (int) ($course->trainer_revenue_percent ?? 0);
                $estimatedRevenue = $activeStudents > 0 && $price > 0 && $schemePercent > 0
                    ? round(($activeStudents * $price * $schemePercent) / 100)
                    : 0;

                return [
                    'course_id' => (int) $course->id,
                    'course_name' => (string) $course->name,
                    'active_students_count' => $activeStudents,
                    'price' => $price,
                    'scheme_percent' => $schemePercent,
                    'estimated_revenue' => $estimatedRevenue,
                    'rating' => (float) ($course->reviews_avg_rating ?? 0),
                ];
            })
            ->values();

        $completedCourseItems = (clone $coursesQuery)
            ->whereIn('status', ['completed', 'finished', 'archived', 'approved'])
            ->withCount([
                'enrollments as active_students_count' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->withAvg('reviews', 'rating')
            ->orderByDesc('approved_at')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        $courseReviews = \App\Models\Review::query()
            ->whereHas('course', function ($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->with(['user', 'course'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($review) {
                $review->type = 'course';
                $review->rating = $review->trainer_rating ?? $review->rating ?? 0;
                return $review;
            });

        $eventFeedbacks = Feedback::query()
            ->whereHas('event', function ($query) use ($user) {
                $query->where(function ($eventQuery) use ($user) {
                    $eventQuery->where('trainer_id', $user->id)
                        ->orWhereHas('speakers', function ($speakerQuery) use ($user) {
                            $speakerQuery->where('trainer_id', $user->id);
                        })
                        ->orWhereHas('trainerAssignments', function ($assignmentQuery) use ($user) {
                            $assignmentQuery->where('trainer_id', $user->id)
                                ->where('status', 'accepted');
                        });
                });
            })
            ->with(['user', 'event', 'replies.trainer'])
            ->latest('created_at')
            ->limit(5)
            ->get()
            ->map(function ($feedback) {
                $feedback->type = 'event';
                $feedback->rating = $feedback->speaker_rating ?? $feedback->rating ?? 0;
                return $feedback;
            });

        $feedbackItems = $courseReviews->concat($eventFeedbacks)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        $totalCertificates = (clone TrainerCertificate::query())
            ->where('trainer_id', $user->id)
            ->whereIn('status', ['sent', 'published'])
            ->count();

        return view('trainer.dashboard', compact(
            'priorityCourses',
            'priorityEvents',
            'todoMaterials',
            'todoConfirmations',
            'students',
            'totalCourses',
            'attentionCourseCount',
            'activeCourseCount',
            'activeEventCount',
            'totalStudents',
            'totalCertificates',
            'teachingHistory',
            'activeAssignmentItems',
            'revenueCourseItems',
            'completedCourseItems',
            'feedbackItems',
            'pendingInvitationItems',
            'unreadInvitationCount',
            'trainerActivity',
            'availableContributionSchemes'
        ));
    }

    public function toggleAvailability(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        if ((string) ($user->user_status ?? 'active') === 'suspended') {
            return back()->with('error', 'Akun suspended hanya bisa diaktifkan kembali oleh admin idSpora.');
        }

        $nextStatus = (string) ($user->user_status ?? 'active') === 'active' ? 'inactive' : 'active';
        $payload = ['user_status' => $nextStatus];

        if ($nextStatus === 'active') {
            $payload['consecutive_expired_invitations'] = 0;
            $payload['last_teaching_at'] = now();
        }

        $user->forceFill($payload)->save();
        app(TrainerActivityService::class)->refresh($user);

        return back()->with('success', $nextStatus === 'active'
            ? 'Status Anda sekarang Active dan siap menerima undangan mengajar.'
            : 'Status Anda sekarang Inactive. Anda tidak akan muncul di opsi undangan admin.');
    }

    public function courses()
    {
        $user = Auth::user();

        $courses = $user->coursesAsTrainer()
            ->withCount([
                'enrollments' => function ($query) {
                    $query->where('status', 'active');
                },
                'modules', // Tambahkan ini untuk menghitung jumlah modul
                'modules as processing_assigned_count' => function ($query) {
                    $query->where('processing_status', 'assigned_to_admin_course');
                },
                'modules as processing_uploaded_count' => function ($query) {
                    $query->where('processing_status', 'processed_uploaded');
                },
                'modules as processing_revision_count' => function ($query) {
                    $query->where('processing_status', 'revision_requested');
                },
                'modules as processing_ready_count' => function ($query) {
                    $query->where('processing_status', 'ready_for_publish');
                },
            ])
            ->withAvg('reviews', 'rating')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($courses as $c) {
            $c->is_locked = $this->isCourseMaterialLockedForTrainer((int) $c->id, (int) $user->id);
        }

        $certifiedCourseIds = \App\Models\TrainerCertificate::query()
            ->where('trainer_id', $user->id)
            ->whereIn('status', ['sent', 'published'])
            ->where('certifiable_type', \App\Models\Course::class)
            ->whereNotNull('file_path')
            ->pluck('certifiable_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $certifiedCourseIdSet = array_fill_keys($certifiedCourseIds, true);

        $pendingInvitationCourseIds = TrainerNotification::query()
            ->where('trainer_id', $user->id)
            ->where('type', 'course_invitation')
            ->where('invitation_status', 'pending')
            ->get(['data'])
            ->map(function (TrainerNotification $notification) {
                return (int) data_get($notification->data, 'entity_id', 0);
            })
            ->filter(fn($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $pendingInvitationCourseIdSet = array_fill_keys($pendingInvitationCourseIds, true);

        $finishedCourses = $courses->filter(function ($course) use ($certifiedCourseIdSet) {
            $status = (string) ($course->status ?? '');
            return isset($certifiedCourseIdSet[(int) $course->id])
                || in_array($status, ['completed', 'finished', 'archived'], true);
        })->values();

        $ongoingCourses = $courses->filter(function ($course) use ($certifiedCourseIdSet, $pendingInvitationCourseIdSet) {
            $status = (string) ($course->status ?? '');
            return !isset($certifiedCourseIdSet[(int) $course->id])
                && !isset($pendingInvitationCourseIdSet[(int) $course->id])
                && in_array($status, ['approved', 'published', 'active'], true);
        })->values();

        $upcomingCourses = $courses->filter(function ($course) use ($finishedCourses, $ongoingCourses, $pendingInvitationCourseIdSet) {
            return !$finishedCourses->contains('id', $course->id)
                && (isset($pendingInvitationCourseIdSet[(int) $course->id]) || !$ongoingCourses->contains('id', $course->id));
        })->values();

        return view('trainer.courses', compact(
            'courses',
            'certifiedCourseIdSet',
            'ongoingCourses',
            'upcomingCourses',
            'finishedCourses'
        ));
    }

    public function courseDetail($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();

        $baseCourse = \App\Models\Course::query()
            ->where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Safety net: if admin forgot to clone template slots, clone on-demand for trainer UI.
        $this->ensureTemplateStructureExists($baseCourse);

        // 1. Ambil data course dan relasinya
        $course = \App\Models\Course::with([
            'modules' => function ($query) {
                // Urutkan modul, dan load relasi kuis
                $query->orderBy('order_no', 'asc')->with('quizQuestions');
            },
            'enrollments.user',
            'reviews',
            'units',
        ])
            ->where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // 2. Hitung statistik dasar
        $enrollmentCount = $course->enrollments->where('status', 'active')->count();
        $averageRating = number_format($course->reviews->avg('rating') ?? 0, 1);
        $moduleCount = $course->modules->count();
        $activeStudents = $course->enrollments->where('status', 'active');

        // 3. Kalkulasi Quiz Recap (Berdasarkan model QuizAttempt)
        $totalSubmissions = 0;
        $totalScores = 0;
        $classAverage = 0;

        // Ambil semua percobaan kuis (QuizAttempt) yang terkait dengan modul-modul di course ini
        $moduleIds = $course->modules->pluck('id');
        $quizAttempts = \App\Models\QuizAttempt::with(['user', 'courseModule'])
            ->whereIn('course_module_id', $moduleIds)
            ->orderBy('completed_at', 'desc')
            ->get();

        if ($quizAttempts->count() > 0) {
            $totalSubmissions = $quizAttempts->count();

            // Hitung rata-rata persentase kelas
            foreach ($quizAttempts as $attempt) {
                $totalScores += $attempt->percentage;
            }
            $classAverage = round($totalScores / $totalSubmissions, 1);
        }

        $processingModules = $course->modules->filter(function ($module) {
            return filled($module->processing_status ?? null);
        });

        $processingSummary = [
            'total' => $processingModules->count(),
            'assigned' => $processingModules->where('processing_status', 'assigned_to_admin_course')->count(),
            'uploaded' => $processingModules->where('processing_status', 'processed_uploaded')->count(),
            'revision' => $processingModules->where('processing_status', 'revision_requested')->count(),
            'ready' => $processingModules->where('processing_status', 'ready_for_publish')->count(),
        ];

        $courseInvitation = $this->latestCourseInvitation((int) $course->id, $trainerId);
        $invitationSchemeType = 0;
        if (!empty($course->trainer_contribution_scheme)) {
            $invitationSchemeType = match ($course->trainer_contribution_scheme) {
                'e2e' => 1,
                'module_video' => 2,
                'video_only' => 3,
                default => 0,
            };
        }

        if ($invitationSchemeType === 0 && $courseInvitation) {
            $invitationSchemeType = (int) data_get($courseInvitation->data ?? [], 'scheme_type', 0);
            if ($invitationSchemeType === 0) {
                $contribScheme = (string) data_get($courseInvitation->data ?? [], 'contribution_scheme', '');
                $invitationSchemeType = match ($contribScheme) {
                    'e2e' => 1,
                    'module_video' => 2,
                    'video_only' => 3,
                    default => 0,
                };
            }
        }

        $activeSchemeType = in_array($invitationSchemeType, [1, 2, 3], true)
            ? $invitationSchemeType
            : 1;

        $schemePermissions = [
            1 => ['can_module' => true, 'can_video' => true, 'can_quiz' => true],
            2 => ['can_module' => true, 'can_video' => true, 'can_quiz' => false],
            3 => ['can_module' => false, 'can_video' => true, 'can_quiz' => false],
        ][$activeSchemeType] ?? ['can_module' => true, 'can_video' => true, 'can_quiz' => true];

        $courseInvitationStatus = $courseInvitation?->effectiveInvitationStatus() ?? '';
        $courseMaterialLocked = $this->isCourseMaterialLockedForTrainer((int) $course->id, $trainerId);

        return view('trainer.detail-course', compact(
            'course',
            'enrollmentCount',
            'averageRating',
            'moduleCount',
            'activeStudents',
            'quizAttempts',
            'classAverage',
            'totalSubmissions',
            'processingSummary',
            'schemePermissions',
            'courseMaterialLocked',
            'courseInvitationStatus',
            'courseInvitation'
        ));
    }
    public function events(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $this->ensureEventInvitationsExistForTrainer($user);
        $trainerId = (int) ($user->id ?? 0);
        $search = $request->query('search');
        $trainerName = trim((string) ($user->name ?? ''));

        $query = $this->trainerEventQuery($trainerId, $trainerName, true)
            ->with([
                'speakers' => function ($speakerQuery) use ($trainerId) {
                    $speakerQuery->where('trainer_id', $trainerId);
                },
                'trainerAssignments' => function ($assignmentQuery) use ($trainerId) {
                    $assignmentQuery->where('trainer_id', $trainerId)
                        ->where('status', 'accepted');
                },
            ])
            ->withCount([
                'registrations as participants_count' => function ($q) {
                    $q->where('status', 'active');
                },
                'scheduleItems as schedule_count'
            ])
            ->withAvg('feedbacks', 'rating');

        if ($search) {
            $query->where('title', 'LIKE', "%{$search}%");
        }

        $events = $query
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get()
            ->filter(function ($event) use ($trainerId, $trainerName) {
                return $this->trainerMatchesEvent($event, $trainerId, $trainerName);
            })
            ->values();

        $assignmentMap = TrainerAssignment::query()
            ->where('trainer_id', (int) $user->id)
            ->whereIn('event_id', $events->pluck('id')->all())
            ->orderByDesc('id')
            ->get()
            ->unique('event_id')
            ->keyBy('event_id');

        $events = $events->map(function ($event) use ($user, $assignmentMap) {
            $assignment = $assignmentMap->get((int) $event->id);
            $compensation = $this->resolveEventCompensation($event, (int) $user->id, $assignment);

            foreach ($compensation as $key => $value) {
                $event->setAttribute($key, $value);
            }

            return $event;
        })->values();

        $upcomingCount = $events->filter(function ($event) {
            return $event->event_date && \Carbon\Carbon::parse($event->event_date)->gte(now()->startOfDay());
        })->count();

        $certifiedEventIds = \App\Models\TrainerCertificate::query()
            ->where('trainer_id', $user->id)
            ->whereIn('status', ['sent', 'published'])
            ->where('certifiable_type', \App\Models\Event::class)
            ->whereNotNull('file_path')
            ->pluck('certifiable_id')
            ->map(fn($id) => (int) $id)
            ->all();

        $certifiedEventIdSet = array_fill_keys($certifiedEventIds, true);

        $now = now();
        $finishedEvents = collect();
        $ongoingEvents = collect();
        $upcomingEvents = collect();

        foreach ($events as $event) {
            $date = $event->event_date instanceof Carbon
                ? $event->event_date->copy()
                : Carbon::parse((string) $event->event_date);

            $timeStart = !empty($event->event_time)
                ? Carbon::parse((string) $event->event_time)->format('H:i:s')
                : '00:00:00';

            $startAt = Carbon::parse($date->format('Y-m-d') . ' ' . $timeStart);

            $timeEnd = !empty($event->event_time_end)
                ? Carbon::parse((string) $event->event_time_end)->format('H:i:s')
                : '23:59:59';

            $endAt = Carbon::parse($date->format('Y-m-d') . ' ' . $timeEnd);
            if ($endAt->lt($startAt)) {
                $endAt = $startAt->copy()->endOfDay();
            }

            if ($now->gt($endAt) || isset($certifiedEventIdSet[(int) $event->id])) {
                $finishedEvents->push($event);
            } elseif ($now->lt($startAt)) {
                $upcomingEvents->push($event);
            } else {
                $ongoingEvents->push($event);
            }
        }

        return view('trainer.events', compact(
            'events',
            'upcomingCount',
            'search',
            'certifiedEventIdSet',
            'ongoingEvents',
            'upcomingEvents',
            'finishedEvents'
        ));
    }

    public function eventDetail($id)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $this->ensureEventInvitationsExistForTrainer($user);
        $trainerId = $user ? $user->id : 0;
        $trainerName = mb_strtolower(trim((string) ($user?->name ?? '')));

        $pendingInvitation = TrainerNotification::query()
            ->where('trainer_id', (int) $trainerId)
            ->where('type', 'event_invitation')
            ->where(function ($query) use ($id) {
                $query->where('data', 'like', '%"entity_id":' . (int) $id . '%');
            })
            ->latest('id')
            ->first();

        $invitationData = is_array($pendingInvitation?->data) ? $pendingInvitation->data : [];
        $invitationStatus = (string) data_get(
            $invitationData,
            'invitation_status',
            (string) ($pendingInvitation->invitation_status ?? 'pending')
        );
        $canOpenFromInvitation = $pendingInvitation
            && in_array($invitationStatus, ['pending', 'accepted'], true);

        $eventQuery = \App\Models\Event::query()->where('id', $id);

        if (!$canOpenFromInvitation) {
            $eventQuery->where(function ($q) use ($trainerId, $trainerName) {
                $q->where('trainer_id', $trainerId);
                $q->orWhereHas('speakers', function ($speakerQuery) use ($trainerId) {
                    $speakerQuery->where('trainer_id', $trainerId);
                });
                $q->orWhereHas('trainerAssignments', function ($assignmentQuery) use ($trainerId) {
                    $assignmentQuery->where('trainer_id', $trainerId)
                        ->where('status', 'accepted');
                });
                if ($trainerName !== '') {
                    $q->orWhere('speaker', 'like', '%' . $trainerName . '%');
                }
            });
        }

        $event = $eventQuery
            ->with([
                'scheduleItems' => function ($q) {
                    $q->orderBy('start', 'asc');
                },
                'speakers',
            ])
            ->firstOrFail();

        if (!$canOpenFromInvitation && !$this->trainerMatchesEvent($event, (int) $trainerId, $trainerName)) {
            abort(403);
        }

        // Per-trainer module status
        $myModules = \App\Models\EventTrainerModule::where('event_id', $event->id)
            ->where('trainer_id', $trainerId)
            ->orderByDesc('created_at')
            ->get();

        $myMaterialStatus = 'not_uploaded';
        if ($myModules->isNotEmpty()) {
            if ($myModules->contains('status', 'approved')) {
                $myMaterialStatus = 'approved';
            } elseif ($myModules->contains('status', 'pending_review')) {
                $myMaterialStatus = 'pending_review';
            } elseif ($myModules->every(fn($m) => $m->status === 'rejected')) {
                $myMaterialStatus = 'rejected';
            }
        }

        $eventCompensation = $this->resolveEventCompensation($event, (int) $trainerId);

        return view('trainer.detail-event', compact('event', 'myModules', 'myMaterialStatus', 'eventCompensation'));
    }

    public function downloadEventVbg($id)
    {
        $trainerId = \Illuminate\Support\Facades\Auth::id();
        $trainerName = mb_strtolower(trim((string) (\Illuminate\Support\Facades\Auth::user()?->name ?? '')));

        $pendingInvitation = TrainerNotification::query()
            ->where('trainer_id', (int) $trainerId)
            ->where('type', 'event_invitation')
            ->where(function ($query) use ($id) {
                $query->where('data', 'like', '%"entity_id":' . (int) $id . '%');
            })
            ->latest('id')
            ->first();

        $invitationData = is_array($pendingInvitation?->data) ? $pendingInvitation->data : [];
        $invitationStatus = (string) data_get(
            $invitationData,
            'invitation_status',
            (string) ($pendingInvitation->invitation_status ?? 'pending')
        );
        $canOpenFromInvitation = $pendingInvitation
            && in_array($invitationStatus, ['pending', 'accepted'], true);

        $eventQuery = \App\Models\Event::query()->where('id', $id);
        if (!$canOpenFromInvitation) {
            $eventQuery->where(function ($q) use ($trainerId, $trainerName) {
                $q->where('trainer_id', $trainerId);
                $q->orWhereHas('speakers', function ($speakerQuery) use ($trainerId) {
                    $speakerQuery->where('trainer_id', $trainerId);
                });
                $q->orWhereHas('trainerAssignments', function ($assignmentQuery) use ($trainerId) {
                    $assignmentQuery->where('trainer_id', $trainerId)
                        ->where('status', 'accepted');
                });
                if ($trainerName !== '') {
                    $q->orWhere('speaker', 'like', '%' . $trainerName . '%');
                }
            });
        }

        $event = $eventQuery->firstOrFail();

        if (!$canOpenFromInvitation && !$this->trainerMatchesEvent($event, (int) $trainerId, $trainerName)) {
            abort(403);
        }

        $isOfflineEvent = !empty($event->maps_url)
            || (!empty($event->latitude) && !empty($event->longitude));

        // Primary source: vbg_path. Legacy fallback for old online events: image.
        $candidateSources = [];
        if (!empty($event->vbg_path)) {
            $candidateSources[] = (string) $event->vbg_path;
        }
        if (empty($event->vbg_path) && !$isOfflineEvent && !empty($event->image)) {
            $candidateSources[] = (string) $event->image;
        }

        if (empty($candidateSources)) {
            abort(404, 'VBG file tidak tersedia untuk event ini.');
        }

        $filePath = null;
        $externalUrlFallback = null;

        foreach ($candidateSources as $sourcePath) {
            $sourcePath = trim($sourcePath);
            if ($sourcePath === '') {
                continue;
            }

            $normalized = $sourcePath;
            if (preg_match('#^https?://#i', $sourcePath)) {
                $externalUrlFallback = $sourcePath;
                $urlPath = parse_url($sourcePath, PHP_URL_PATH);
                if (!is_string($urlPath) || trim($urlPath) === '') {
                    continue;
                }
                $normalized = $urlPath;
            }

            $normalized = str_replace('\\', '/', (string) $normalized);
            $normalized = preg_replace('#^\./#', '', $normalized) ?? $normalized;
            $normalized = ltrim($normalized, '/');

            // Common legacy prefixes
            if (str_starts_with($normalized, 'public/')) {
                $normalized = ltrim(substr($normalized, 7), '/');
            }
            if (str_starts_with($normalized, 'storage/app/public/')) {
                $normalized = ltrim(substr($normalized, 19), '/');
            }
            if (str_starts_with($normalized, 'storage/')) {
                $normalized = ltrim(substr($normalized, 8), '/');
            }

            $possiblePaths = [
                storage_path('app/public/' . $normalized),
                public_path('uploads/' . $normalized),
                public_path($normalized),
            ];

            if (str_starts_with($normalized, 'uploads/')) {
                $possiblePaths[] = storage_path('app/public/' . ltrim(substr($normalized, 8), '/'));
            }

            $possiblePaths = array_values(array_unique($possiblePaths));
            foreach ($possiblePaths as $path) {
                if (file_exists($path) && is_file($path)) {
                    $filePath = $path;
                    break 2;
                }
            }
        }

        if (!$filePath) {
            if ($externalUrlFallback && preg_match('#^https?://#i', $externalUrlFallback)) {
                return redirect()->away($externalUrlFallback);
            }
            abort(404, 'File VBG tidak ditemukan.');
        }

        // Security check: prevent directory traversal
        $realPath = realpath($filePath);
        $storagePath = realpath(storage_path('app/public'));
        $uploadsPath = realpath(public_path('uploads'));
        $eventDocsPublicPath = realpath(public_path('events/docs'));

        if (!$realPath) {
            abort(403, 'Akses file VBG ditolak.');
        }

        // Check if file is in allowed directories
        $isInStorage = $storagePath && str_starts_with($realPath, $storagePath);
        $isInUploads = $uploadsPath && str_starts_with($realPath, $uploadsPath);
        $isInPublicEventDocs = $eventDocsPublicPath && str_starts_with($realPath, $eventDocsPublicPath);

        if (!($isInStorage || $isInUploads || $isInPublicEventDocs)) {
            abort(403, 'Akses file VBG ditolak.');
        }

        // Get MIME type
        $mimeType = mime_content_type($filePath);
        if (!$mimeType) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $mimeTypes = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
            ];
            $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
        }

        // Generate filename
        $fileName = 'VBG_' . $event->id . '_' . \Illuminate\Support\Str::slug(substr($event->title, 0, 20)) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);

        return response()->download($filePath, $fileName, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function feedback(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $eventIds = $this->trainerEventQuery((int) $user->id, (string) ($user->name ?? ''))->pluck('id');

        // Course Reviews
        $courseReviewsQuery = \App\Models\Review::query()
            ->whereHas('course', function ($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->with(['user', 'course']);

        // Event Feedback
        $eventFeedbackQuery = \App\Models\Feedback::query()
            ->whereIn('event_id', $eventIds)
            ->with(['user', 'event', 'replies.trainer']);

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            
            $courseReviewsQuery->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%");
                })->orWhere('comment', 'LIKE', "%{$search}%");
            });

            $eventFeedbackQuery->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'LIKE', "%{$search}%");
                })->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $courseReviews = $courseReviewsQuery->get()->map(function ($review) {
            $review->rating = $review->trainer_rating ?? $review->rating ?? 0;
            $review->setRelation('replies', collect());
            return $review;
        });

        $eventFeedbacks = $eventFeedbackQuery->get()->map(function ($feedback) {
            $feedback->rating = $feedback->speaker_rating ?? $feedback->rating ?? 0;
            return $feedback;
        });

        $allFeedbacks = $courseReviews->concat($eventFeedbacks)
            ->sortByDesc('created_at');

        $totalFeedbacks = $allFeedbacks->count();
        $averageRating = 0;
        $satisfactionRate = 0;
        $ratingStats = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        if ($totalFeedbacks > 0) {
            $averageRating = round($allFeedbacks->avg('rating'), 1);

            $ratingsCount = $allFeedbacks->groupBy(function ($item) {
                return (int) round($item->rating);
            })->map(fn($group) => $group->count())->toArray();

            foreach ($ratingsCount as $star => $count) {
                if (isset($ratingStats[$star])) {
                    $ratingStats[$star] = round(($count / $totalFeedbacks) * 100);
                }
            }

            $satisfactionRate = $ratingStats[5] + $ratingStats[4];
        }

        // Manual Pagination
        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10;
        $currentItems = $allFeedbacks->slice(($currentPage - 1) * $perPage, $perPage)->values();
        $feedbacks = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $allFeedbacks->count(),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('trainer.feedback', compact(
            'feedbacks',
            'totalFeedbacks',
            'averageRating',
            'ratingStats',
            'satisfactionRate'
        ));
    }

    public function courseStudio(Request $request, $id)
    {
        $trainerId = (int) Auth::id();
        $course = \App\Models\Course::where('id', $id)->where('trainer_id', $trainerId)->firstOrFail();

        $courseInvitation = $this->latestCourseInvitation((int) $course->id, $trainerId);
        $courseInvitationStatus = $courseInvitation?->effectiveInvitationStatus() ?? '';
        $courseMaterialLocked = $this->isCourseMaterialLockedForTrainer((int) $course->id, $trainerId);

        $invitationSchemeType = 0;
        if (!empty($course->trainer_contribution_scheme)) {
            $invitationSchemeType = match ($course->trainer_contribution_scheme) {
                'e2e' => 1,
                'module_video' => 2,
                'video_only' => 3,
                default => 0,
            };
        }

        if ($invitationSchemeType === 0) {
            $invitationSchemeType = (int) data_get($courseInvitation?->data ?? [], 'scheme_type', 0);
            if ($invitationSchemeType === 0) {
                $contribScheme = (string) data_get($courseInvitation?->data ?? [], 'contribution_scheme', '');
                $invitationSchemeType = match ($contribScheme) {
                    'e2e' => 1,
                    'module_video' => 2,
                    'video_only' => 3,
                    default => 0,
                };
            }
        }

        $requestedSchemeType = (int) $request->query('scheme', 0);
        $sessionSchemeType = (int) session('trainer_active_scheme_type', 0);

        $activeSchemeType = in_array($invitationSchemeType, [1, 2, 3], true)
            ? $invitationSchemeType
            : (in_array($requestedSchemeType, [1, 2, 3], true)
                ? $requestedSchemeType
                : (in_array($sessionSchemeType, [1, 2, 3], true) ? $sessionSchemeType : 1));

        $schemePermissions = [
            1 => ['can_module' => true, 'can_video' => true, 'can_quiz' => true],
            2 => ['can_module' => true, 'can_video' => true, 'can_quiz' => false],
            3 => ['can_module' => false, 'can_video' => true, 'can_quiz' => false],
        ][$activeSchemeType] ?? ['can_module' => true, 'can_video' => true, 'can_quiz' => true];


        $allowedTabs = array_keys(array_filter([
            'module' => $schemePermissions['can_module'] ?? false,
            'video' => $schemePermissions['can_video'] ?? false,
            'quiz' => $schemePermissions['can_quiz'] ?? false,
        ]));
        $requestedTab = (string) $request->query('tab', 'module');
        $activeTab = in_array($requestedTab, $allowedTabs, true)
            ? $requestedTab
            : ($allowedTabs[0] ?? 'module');

        // Safety net: ensure unit slots from template exist before opening studio.
        $this->ensureTemplateStructureExists($course);
        $this->ensureQuizSlotPerUnit($course);

        // 1. Ambil semua modul course
        $unitIndex = (int) $request->query('unit', 0); // Default ke Bab 1 (index 0)
        $allModules = \App\Models\CourseModule::where('course_id', $id)
            ->with([
                'quizQuestions' => function ($query) {
                    $query->orderBy('order_no', 'asc')->with([
                        'answers' => function ($answerQuery) {
                            $answerQuery->orderBy('order_no', 'asc');
                        }
                    ]);
                }
            ])
            ->withCount('quizQuestions')
            ->orderBy('order_no', 'asc')
            ->get();

        // 2. Tentukan jumlah Bab (Unit)
        // Jika ada CourseUnit, gunakan itu. Jika tidak, gunakan chunk 3.
        $units = \App\Models\CourseUnit::where('course_id', $id)->orderBy('unit_no')->get();
        if ($units->isNotEmpty()) {
            $unitCount = $units->count();
            // Group modules by units (assuming 3 modules per unit as convention)
            $chunks = $allModules->chunk(3)->values();
        } else {
            $chunks = $allModules->chunk(3)->values();
            $unitCount = $chunks->count();
        }

        // 3. Ambil modul-modul HANYA untuk Bab yang dipilih
        $activeUnitModules = $chunks->get($unitIndex, collect());

        // Hanya tampilkan materi yang sudah diupload untuk bab (unit) yang sedang aktif saja.
        // Mengambil ID modul yang ada di bab aktif untuk filter yang presisi.
        $activeUnitModuleIds = $activeUnitModules->pluck('id')->all();

        $uploadedMaterials = $allModules
            ->filter(function ($module) use ($activeUnitModuleIds) {
                // Hanya modul di bab aktif, dengan file yang sudah terupload
                return in_array($module->id, $activeUnitModuleIds)
                    && in_array($module->type, ['pdf', 'video'])
                    && !empty($module->content_url);
            })
            ->map(function ($module) use ($course, $unitIndex) {
                return [
                    'module_id' => (int) $module->id,
                    'order_no' => (int) $module->order_no,
                    'unit_no' => (int) $unitIndex + 1,
                    'type' => (string) $module->type,
                    'title' => (string) ($module->title ?? ''),
                    'file_name' => (string) ($module->file_name ?: basename((string) $module->content_url)),
                    'view_url' => route('trainer.courses.studio.material.view', [$course->id, $module->id]),
                    'updated_at' => optional($module->updated_at)->toDateTimeString(),
                    'review_status' => (string) ($module->review_status ?? 'pending_review'),
                    'processing_status' => (string) ($module->processing_status ?? ''),
                ];
            })
            ->values();

        if ($activeUnitModules->isEmpty()) {
            return redirect()->route('trainer.courses')->with('error', 'Silabus untuk bab ini belum tersedia.');
        }

        $unitNo = $unitIndex + 1;
        $unit = \App\Models\CourseUnit::where('course_id', $id)->where('unit_no', $unitNo)->first();
        $unitTitle = $unit && !empty($unit->title) ? $unit->title : ("Bab " . $unitNo);

        return view('trainer.content-studio', compact(
            'course',
            'activeUnitModules',
            'unitTitle',
            'unitIndex',
            'uploadedMaterials',
            'activeSchemeType',
            'schemePermissions',
            'activeTab',
            'courseMaterialLocked',
            'courseInvitationStatus',
            'courseInvitation'
        ));
    }

    public function eventStudio($id)
    {
        $trainer = Auth::user();
        $trainerId = (int) ($trainer->id ?? 0);
        $trainerName = mb_strtolower(trim((string) ($trainer->name ?? '')));
        $event = \App\Models\Event::findOrFail($id);

        if (!$this->trainerCanManageEventMaterials($event, $trainer)) {
            abort(403, 'Anda tidak memiliki akses ke materi event ini.');
        }

        $assignment = $this->ensureEventAssignmentForTrainer($event, $trainer)
            ?: $this->latestEventAssignment((int) $event->id, (int) ($trainer->id ?? 0));

        // For co-speakers, material state must be isolated per assignment.
        if ($assignment && (int) ($event->trainer_id ?? 0) !== (int) ($trainer->id ?? 0)) {
            $event->setAttribute('module_path', (string) ($assignment->material_path ?? ''));
            $event->setAttribute('module_submission_path', (string) ($assignment->material_path ?? ''));
            $event->setAttribute('module_submitted_at', $assignment->material_submitted_at ?: $assignment->materials_uploaded_at);
            $event->setAttribute('material_status', (string) ($assignment->material_status ?: 'pending'));
            $event->setAttribute('material_approved_at', $assignment->material_approved_at);
            $event->setAttribute('module_verified_at', $assignment->material_approved_at);
            $event->setAttribute('material_rejection_reason', (string) ($assignment->material_rejection_reason ?? ''));
            $event->setAttribute('module_rejection_reason', (string) ($assignment->material_rejection_reason ?? ''));
        }

        $invitation = \App\Models\TrainerNotification::query()
            ->where('trainer_id', (int) $trainerId)
            ->where('type', 'event_invitation')
            ->where(function ($query) use ($event) {
                $query->where('data', 'like', '%"entity_id":' . (int) $event->id . '%');
            })
            ->latest('id')
            ->first();

        $invitationData = is_array($invitation?->data) ? $invitation->data : [];
        $invitationUploadDueAtRaw = (string) data_get($invitationData, 'upload_due_at', '');
        $effectiveDeadline = null;
        if ($invitationUploadDueAtRaw !== '') {
            $effectiveDeadline = \Carbon\Carbon::parse($invitationUploadDueAtRaw);
        } elseif (!empty($event->material_deadline)) {
            $effectiveDeadline = \Carbon\Carbon::parse($event->material_deadline);
        }
        $cacheKey = "trainer_draft_files_{$trainerId}_{$event->id}";

        if ($effectiveDeadline && now()->gt($effectiveDeadline) && \Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $isPrimaryTrainer = (int) ($event->trainer_id ?? 0) === (int) $trainerId;
            $this->performSubmitFinal($event, $trainerId, $isPrimaryTrainer, $assignment, $effectiveDeadline, $invitation);
            $event->refresh();
        }

        $draftModules = \Illuminate\Support\Facades\Cache::get($cacheKey, []);

        // Per-trainer module status
        $myModules = \App\Models\EventTrainerModule::where('event_id', $event->id)
            ->where('trainer_id', $trainerId)
            ->orderByDesc('created_at')
            ->get();

        $myMaterialStatus = 'not_uploaded';
        if ($myModules->isNotEmpty()) {
            $totalCount = $myModules->count();
            $approvedCount = $myModules->where('status', 'approved')->count();
            $pendingCount = $myModules->whereIn('status', ['pending_review', 'pending'])->count();
            $rejectedCount = $myModules->where('status', 'rejected')->count();

            if ($approvedCount === $totalCount) {
                $myMaterialStatus = 'approved';
            } elseif ($pendingCount > 0) {
                $myMaterialStatus = 'pending_review';
            } elseif ($rejectedCount > 0) {
                $myMaterialStatus = 'rejected';
            } else {
                $myMaterialStatus = 'pending';
            }
        }

        // Sync event->material_status so the blade $displayMaterialStatus is always accurate.
        // EventTrainerModule status takes precedence (it's the per-trainer source of truth).
        if ($myModules->isNotEmpty() && $myMaterialStatus !== 'not_uploaded') {
            $event->setAttribute('material_status', $myMaterialStatus);
            // Also sync module_path from the latest relevant module so $hasUploadedModule resolves correctly
            $latestModule = $myModules->first();
            if ($latestModule && empty($event->module_path)) {
                $event->setAttribute('module_path', $latestModule->path);
            }
        } elseif ($assignment && !empty($assignment->material_status)) {
            $event->setAttribute('material_status', (string) $assignment->material_status);
        }

        $eventCompensation = $this->resolveEventCompensation($event, (int) $trainerId, $assignment);

        return view('trainer.event-studio', compact('event', 'myModules', 'myMaterialStatus', 'eventCompensation', 'draftModules'));
    }

    private function performSubmitFinal($event, $trainerId, $isPrimaryTrainer, $assignment, $effectiveDeadline, $invitation)
    {
        $cacheKey = "trainer_draft_files_{$trainerId}_{$event->id}";
        $draftFiles = \Illuminate\Support\Facades\Cache::get($cacheKey, []);
        if (empty($draftFiles)) {
            return;
        }

        foreach ($draftFiles as $draft) {
            \App\Models\EventTrainerModule::create([
                'event_id' => $event->id,
                'trainer_id' => $trainerId,
                'original_name' => $draft['original_name'],
                'path' => $draft['path'],
                'survey_link' => $draft['survey_link'] ?? null,
                'status' => 'pending_review',
            ]);
        }

        $lastDraft = end($draftFiles);
        $primaryMaterialPath = $lastDraft['path'];

        if ($assignment) {
            $assignment->update([
                'material_path' => $primaryMaterialPath,
                'materials_uploaded_at' => now(),
                'material_status' => 'pending_review',
                'material_submitted_at' => now(),
                'material_approved_at' => null,
                'material_approved_by' => null,
                'material_rejected_at' => null,
                'material_rejected_by' => null,
                'material_rejection_reason' => null,
            ]);
        }

        $latestImagePath = null;
        $latestModuleDocPath = null;
        foreach ($draftFiles as $draft) {
            $mime = $draft['mime_type'] ?? '';
            $path = $draft['path'];
            $origName = $draft['original_name'];
            if (str_starts_with((string) $mime, 'image/')) {
                $latestImagePath = $path;
            } else {
                $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
                if (in_array($ext, ['pdf', 'ppt', 'pptx', 'doc', 'docx'], true)) {
                    $latestModuleDocPath = $path;
                }
            }
        }
        $updates = [];
        if ($latestImagePath) {
            $updates['vbg_path'] = $latestImagePath;
        }
        if (!empty($updates) && ($isPrimaryTrainer || !$assignment)) {
            $event->update($updates);
        }

        \Illuminate\Support\Facades\Cache::forget($cacheKey);

        if (!empty($effectiveDeadline) && now()->lte($effectiveDeadline)) {
            app(\App\Services\TrainerActivityService::class)->resetLateUploads(\Illuminate\Support\Facades\Auth::user(), [
                'entity_type' => 'event',
                'entity_id' => (int) $event->id,
                'entity_title' => (string) ($event->title ?? ''),
                'url' => route('trainer.events.show', $event->id),
            ]);
        }

        if ($invitation) {
            $invitationData = is_array($invitation->data) ? $invitation->data : [];
            $invitationData['material_uploaded_at'] = now()->toIso8601String();
            $invitation->data = $invitationData;
            $invitation->save();
        }
    }

    private function syncEventMaterialStatus(int $eventId, int $trainerId): void
    {
        $event = \App\Models\Event::find($eventId);
        if (!$event) {
            return;
        }

        // Count module statuses
        $modules = \App\Models\EventTrainerModule::where('event_id', $eventId)
            ->where('trainer_id', $trainerId)
            ->get();

        $totalModules = $modules->count();
        $approvedModules = $modules->where('status', 'approved')->count();
        $rejectedModules = $modules->where('status', 'rejected')->count();
        $pendingModules = $modules->whereIn('status', ['pending_review', 'pending'])->count();

        // Determine assignment status
        $assignment = \App\Models\TrainerAssignment::where('event_id', $eventId)
            ->where('trainer_id', $trainerId)
            ->first();

        $newStatus = 'pending_review';

        if ($totalModules === 0) {
            $newStatus = 'pending';
        } elseif ($pendingModules > 0) {
            $newStatus = 'pending_review';
        } elseif ($approvedModules === $totalModules) {
            $newStatus = 'approved';
        } elseif ($rejectedModules > 0) {
            $newStatus = 'rejected';
        }

        // Get the latest remaining module path to set as primary path
        $latestModule = $modules->sortByDesc('created_at')->first();
        $latestPath = $latestModule ? $latestModule->path : null;

        if ($assignment) {
            $payload = [
                'material_status' => $newStatus,
                'material_path' => $latestPath,
            ];

            if ($newStatus === 'approved') {
                $payload['material_approved_at'] = now();
                $payload['material_approved_by'] = $assignment->material_approved_by ?: 1;
                $payload['material_rejection_reason'] = null;
            } elseif ($newStatus === 'rejected') {
                if (empty($assignment->material_rejection_reason)) {
                    $firstRejected = $modules->where('status', 'rejected')->first();
                    $payload['material_rejection_reason'] = $firstRejected?->rejection_reason;
                }
                $payload['material_rejected_at'] = now();
                $payload['material_rejected_by'] = $assignment->material_rejected_by ?: 1;
            } else { // pending_review / pending
                $payload['material_approved_at'] = null;
                $payload['material_approved_by'] = null;
                $payload['material_rejected_at'] = null;
                $payload['material_rejected_by'] = null;
                $payload['material_rejection_reason'] = null;
            }

            $assignment->update($payload);
        }
    }

    public function saveEventQuiz(Request $request, $id)
    {

        $request->validate([
            'questions' => 'required|string',
            'passingGrade' => 'required|integer|min:0|max:100',
        ]);

        $trainer = Auth::user();
        $event = \App\Models\Event::findOrFail($id);

        if (!$this->trainerCanManageEventMaterials($event, $trainer)) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah kuis event ini.');
        }

        $isPrimaryTrainer = (int) ($event->trainer_id ?? 0) === (int) ($trainer->id ?? 0);
        $assignment = $this->latestEventAssignment((int) $event->id, (int) ($trainer->id ?? 0));
        
        $effectiveMaterialStatus = (string) ($isPrimaryTrainer
            ? ($event->material_status ?? '')
            : ($assignment->material_status ?? 'pending'));

        if ($effectiveMaterialStatus === 'approved') {
            return back()->with('error', 'Kuis event ini telah disetujui oleh admin trainer dan tidak dapat diubah lagi.');
        }


        $questionsData = json_decode($request->questions, true);

        if (empty($questionsData)) {
            return back()->with('error', 'Data soal tidak valid.');
        }

        $quiz = \App\Models\Quiz::updateOrCreate(
            ['event_id' => $event->id],
            ['passing_grade' => $request->passingGrade]
        );

        $quiz->questions()->delete();

        foreach ($questionsData as $q) {
            $quiz->questions()->create([
                'question_text' => $q['text'],
                'options' => json_encode($q['options']),
                'correct_answer_index' => $q['correctAnswer'],
                'weight' => $q['weight'] ?? 10,
            ]);
        }

        return redirect()->back()->with('success', 'Kuis event berhasil disimpan!');
    }

    public function finance()
    {
        $trainerId = Auth::id();
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        // 1. Calculate actual total earned from approved payouts
        $totalEarned = \App\Models\TrainerPayment::where('user_id', $trainerId)
            ->where('status', 'approved')
            ->sum('amount');

        // 2. Fetch recent payments (Midtrans settled payments for courses/events of this trainer)
        $baseQuery = \App\Models\ManualPayment::query()
            ->with(['user:id,name', 'event:id,title,trainer_id', 'course:id,name,trainer_id'])
            ->where('status', 'settled')
            ->where(function ($query) use ($trainerId) {
                $query->whereHas('course', function ($courseQuery) use ($trainerId) {
                    $courseQuery->where('trainer_id', $trainerId);
                })->orWhereHas('event', function ($eventQuery) use ($trainerId) {
                    $eventQuery->where('trainer_id', $trainerId);
                });
            });

        $payments = (clone $baseQuery)
            ->latest('created_at')
            ->paginate(10);

        // 3. Fetch disburse payouts for this trainer
        $payouts = \App\Models\TrainerPayment::with(['event', 'course'])
            ->where('user_id', $trainerId)
            ->latest()
            ->get();

        // 4. Calculate Estimasi Course
        $courses = $user->coursesAsTrainer()
            ->withCount([
                'enrollments as active_students' => function ($query) {
                    $query->where('status', 'active');
                },
            ])
            ->get()
            ->map(function (Course $course) {
                $activeStudents = (int) ($course->active_students ?? 0);
                $price = (float) ($course->price ?? 0);
                $schemePercent = (int) ($course->trainer_revenue_percent ?? 0);
                $estimatedRevenue = $activeStudents > 0 && $price > 0 && $schemePercent > 0
                    ? round(($activeStudents * $price * $schemePercent) / 100)
                    : 0;

                return [
                    'course' => $course,
                    'active_students' => $activeStudents,
                    'scheme_percent' => $schemePercent,
                    'estimated_revenue' => $estimatedRevenue,
                ];
            })
            ->filter(fn($item) => $item['estimated_revenue'] > 0)
            ->values();

        // 5. Calculate Estimasi Event
        $events = TrainerAssignment::query()
            ->where('trainer_id', $trainerId)
            ->where('status', 'accepted')
            ->with([
                'event' => function ($query) {
                    $query->withCount([
                        'registrations as active_participants_count' => function ($registrationQuery) {
                            $registrationQuery->where('status', 'active');
                        }
                    ]);
                }
            ])
            ->get()
            ->map(function (TrainerAssignment $assignment) {
                $schemeDefinitions = TrainerAssignment::getSchemeDefinitions();
                $schemeType = (int) ($assignment->scheme_type ?? 0);
                $scheme = $schemeDefinitions[$schemeType] ?? [];
                $event = $assignment->event;

                $eventPrice = (float) ($event->price ?? 0);
                $activeParticipants = (int) ($event->active_participants_count ?? 0);
                $schemePercent = (int) ($scheme['percentage'] ?? 0);
                $feePerParticipant = $eventPrice > 0 && $schemePercent > 0
                    ? round(($eventPrice * $schemePercent) / 100, 2)
                    : 0;
                $estimatedFee = $activeParticipants > 0 && $feePerParticipant > 0
                    ? round($activeParticipants * $feePerParticipant, 2)
                    : 0;

                return [
                    'event' => $event,
                    'active_participants_count' => $activeParticipants,
                    'fee_trainer' => $feePerParticipant,
                    'estimated_fee' => $estimatedFee,
                ];
            })
            ->filter(fn($item) => $item['estimated_fee'] > 0)
            ->values();

        // 6. Calculate total estimated earnings
        $estimatedTotal = $courses->sum('estimated_revenue') + $events->sum('estimated_fee');

        return view('trainer.finance', compact('totalEarned', 'payments', 'payouts', 'courses', 'events', 'estimatedTotal'));
    }

    /**
     * Download or view payout invoice for the trainer
     */
    public function downloadPayoutInvoice($id)
    {
        $trainerId = Auth::id();
        $payment = \App\Models\TrainerPayment::with('trainer', 'event')
            ->where('user_id', $trainerId)
            ->findOrFail($id);

        return view('admin.finance.trainers.invoice', compact('payment'));
    }

    public function show()
    {
        $trainer = Auth::user();
        $courses = $trainer->coursesAsTrainer()
            ->with(['modules', 'reviews', 'enrollments', 'category'])
            ->withCount([
                'enrollments as active_enrollments_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'modules'
            ])
            ->withAvg('reviews', 'rating')
            ->get();

        $courseIds = $courses->pluck('id');

        $totalStudents = $trainer->trainerEnrollments()
            ->where('enrollments.status', 'active')
            ->distinct('user_id')
            ->count('user_id');

        $trainerEventsQuery = $this->trainerEventQuery((int) $trainer->id, (string) ($trainer->name ?? ''));

        $completedEventsCount = (clone $trainerEventsQuery)
            ->whereDate('event_date', '<', now()->toDateString())
            ->count();

        $completedCoursesCount = $trainer->coursesAsTrainer()
            ->where('status', 'approved')
            ->whereNotNull('approved_at')
            ->where('approved_at', '<', now())
            ->count();

        $eventIds = (clone $trainerEventsQuery)->pluck('id');

        $courseReviews = \App\Models\Review::query()
            ->whereHas('course', function ($q) use ($trainer) {
                $q->where('trainer_id', $trainer->id);
            })
            ->with(['user:id,name', 'course:id,name'])
            ->get()
            ->map(function ($review) {
                $review->type = 'course';
                $review->rating = $review->trainer_rating ?? $review->rating ?? 0;
                $review->setRelation('replies', collect());
                return $review;
            });

        $eventFeedbacks = \App\Models\Feedback::query()
            ->whereIn('event_id', $eventIds)
            ->with(['user:id,name', 'event:id,title', 'replies.trainer'])
            ->get()
            ->map(function ($feedback) {
                $feedback->type = 'event';
                $feedback->rating = $feedback->speaker_rating ?? $feedback->rating ?? 0;
                return $feedback;
            });

        $allFeedbacks = $courseReviews->concat($eventFeedbacks);
        $totalFeedbacks = $allFeedbacks->count();

        $averageRating = $totalFeedbacks > 0 ? round($allFeedbacks->avg('rating'), 1) : 0.0;

        $recentFeedbacks = $allFeedbacks->sortByDesc('created_at')->take(3)->values();

        $recentEvents = (clone $trainerEventsQuery)
            ->withCount([
                'registrations as participants_count' => function ($query) {
                    $query->where('status', 'active');
                }
            ])
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        $paymentsQuery = \App\Models\ManualPayment::query()
            ->with(['course:id,name,trainer_id', 'event:id,title,trainer_id'])
            ->where('status', 'settled')
            ->where(function ($query) use ($trainer) {
                $query->whereHas('course', function ($courseQuery) use ($trainer) {
                    $courseQuery->where('trainer_id', $trainer->id);
                })->orWhereHas('event', function ($eventQuery) use ($trainer) {
                    $eventQuery->where('trainer_id', $trainer->id);
                });
            });

        $totalEarned = (clone $paymentsQuery)->sum('amount');
        $ledgerPayments = (clone $paymentsQuery)
            ->latest('created_at')
            ->take(3)
            ->get();

        $trainerCertificates = TrainerCertificate::query()
            ->with('certifiable')
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published'])
            ->latest('issued_at')
            ->latest('created_at')
            ->take(3)
            ->get();

        $totalCertificates = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published'])
            ->count();

        // 1. Specialization Tags
        $specializations = $trainer->trainer_specializations ?? [];
        if (empty($specializations)) {
            $specializations = $courses->pluck('category.name')->filter()->unique()->values()->take(6);
            if ($specializations->isEmpty() && !empty($trainer->profession)) {
                $specializations = collect(explode(' ', strtoupper($trainer->profession)))->filter()->take(4)->values();
            }
            if ($specializations->isEmpty()) {
                $specializations = collect(['TRAINING', 'MENTORING']);
            }
        } else {
            $specializations = collect($specializations);
        }

        // 2. Skills (Keahlian)
        $skills = $trainer->trainer_skills ?? [];

        $trainerExperiences = $trainer->trainer_experiences ?? [];
        $trainerEducations = $trainer->trainer_educations ?? [];
        $trainerManualCertifications = $trainer->trainer_certifications ?? [];

        // Additional stats for enhanced profile
        $totalCourses = $courses->count();
        $totalEvents = (clone $trainerEventsQuery)->count();
        $totalFeedbacks = $allFeedbacks->count();
        $topCourses = $courses->sortByDesc('reviews_avg_rating')->take(3);

        // Calculate rating breakdown (1 to 5 stars)
        $ratingCounts = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];
        $ratingPercentages = [
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0,
        ];

        foreach ($allFeedbacks as $fb) {
            $star = (int) round($fb->rating);
            if ($star >= 1 && $star <= 5) {
                $ratingCounts[$star]++;
            }
        }

        foreach ($ratingCounts as $star => $count) {
            $ratingPercentages[$star] = $totalFeedbacks > 0 ? (int) round(($count / $totalFeedbacks) * 100) : 0;
        }

        // Calculate aspect ratings based on speaker_rating and overall rating
        $avgSpeakerRating = $totalFeedbacks > 0 ? round($allFeedbacks->avg('rating'), 1) : 0.0;
        $avgOverallRating = $totalFeedbacks > 0 ? round($allFeedbacks->avg(function ($item) {
            return $item->type === 'course' ? ($item->rating ?? 0) : ($item->rating ?? 0);
        }), 1) : 0.0;

        $aspectRatings = [
            'penyampaian_materi' => $avgSpeakerRating,
            'penguasaan_materi' => $avgSpeakerRating > 0 ? min(5.0, round($avgSpeakerRating + 0.1, 1)) : 0.0,
            'interaktivitas' => $avgSpeakerRating > 0 ? max(1.0, round($avgSpeakerRating - 0.1, 1)) : 0.0,
            'manfaat_aplikasi' => $avgOverallRating,
        ];

        return view('trainer.profile', compact(
            'trainer',
            'courses',
            'totalStudents',
            'averageRating',
            'recentFeedbacks',
            'recentEvents',
            'totalEarned',
            'ledgerPayments',
            'trainerCertificates',
            'specializations',
            'skills',
            'totalCourses',
            'totalEvents',
            'completedEventsCount',
            'completedCoursesCount',
            'totalCertificates',
            'totalFeedbacks',
            'topCourses',
            'trainerExperiences',
            'trainerEducations',
            'trainerManualCertifications',
            'ratingCounts',
            'ratingPercentages',
            'aspectRatings'
        ));
    }

    public function editProfile()
    {
        $trainer = Auth::user();
        return view('trainer.profile-edit', compact('trainer'));
    }

    public function updateProfile(Request $request)
    {
        $trainer = Auth::user();

        // Check if this is avatar-only upload (AJAX or file input only)
        $isAvatarOnly = $request->hasFile('avatar') && !$request->filled('name');

        $rules = [
            'phone' => 'nullable|string|max:30',
            'academic_title' => 'nullable|string|max:120',
            'profession' => 'nullable|string|max:100',
            'institution' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'bank_name' => 'nullable|string|max:120',
            'bank_account_number' => 'nullable|string|max:60',
            'bank_account_holder' => 'nullable|string|max:150',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'trainer_specializations' => 'nullable',
        ];

        if (!$isAvatarOnly) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'sometimes|required|email|max:255|unique:users,email,' . $trainer->id;
            $rules['current_password'] = 'nullable|required_with:password';
            $rules['password'] = 'nullable|min:6|confirmed';
        }

        $validated = $request->validate($rules);

        // Normalize trainer_specializations array
        if (array_key_exists('trainer_specializations', $validated)) {
            $specs = $validated['trainer_specializations'];
            if (empty($specs) || $specs === '' || (is_array($specs) && count($specs) === 1 && $specs[0] === '')) {
                $validated['trainer_specializations'] = [];
            } else {
                $validated['trainer_specializations'] = (array) $specs;
            }
        }

        // Update password if provided
        if (!$isAvatarOnly && !empty($request->password)) {
            if (!Hash::check($request->current_password, $trainer->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])->withInput();
            }
            $trainer->password = Hash::make($request->password);
            $trainer->save();
        }

        // Remove password-related fields from validated array so they are not processed by update()
        unset($validated['current_password']);
        unset($validated['password']);
        unset($validated['password_confirmation']);

        if ($request->hasFile('avatar') || $request->hasFile('avatar_file')) {
            $avatarFile = $request->file('avatar') ?? $request->file('avatar_file');

            if ($avatarFile) {
                // Delete old avatar
                if (!empty($trainer->avatar) && !str_starts_with((string) $trainer->avatar, 'http')) {
                    $oldPath = str_starts_with((string) $trainer->avatar, 'avatars/')
                        ? (string) $trainer->avatar
                        : 'avatars/' . $trainer->avatar;

                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                $filename = uniqid('ava_') . '.' . $avatarFile->getClientOriginalExtension();
                Storage::disk('public')->putFileAs('avatars', $avatarFile, $filename);
                $validated['avatar'] = 'avatars/' . $filename;
            }
        } else {
            unset($validated['avatar']);
        }

        $trainer->update($validated);

        // Return JSON for AJAX avatar uploads
        if ($isAvatarOnly) {
            return response()->json([
                'success' => true,
                'avatar_url' => $trainer->avatar_url,
                'message' => 'Foto profil berhasil diperbarui.'
            ]);
        }

        $tab = 'tab-tentang';
        if ($request->has('trainer_specializations')) {
            $tab = 'tab-tentang';
        }

        return redirect()->to(route('trainer.profile') . '#' . $tab)->with('success', 'Profil trainer berhasil diperbarui.');
    }

    public function updateProfileList(Request $request)
    {
        $trainer = Auth::user();

        $type = $request->input('type');
        $action = $request->input('action'); // add, edit, delete
        $index = $request->input('index');

        $validTypes = ['trainer_skills', 'trainer_experiences', 'trainer_educations', 'trainer_certifications'];
        if (!in_array($type, $validTypes)) {
            return redirect()->back()->with('error', 'Tipe data profil tidak valid.');
        }

        $list = $trainer->$type ?? [];

        if ($action === 'add') {
            $newItem = $request->except(['_token', '_method', 'type', 'action', 'index']);
            $list[] = $newItem;
        } elseif ($action === 'edit') {
            if (isset($list[$index])) {
                $updatedItem = $request->except(['_token', '_method', 'type', 'action', 'index']);
                $list[$index] = $updatedItem;
            }
        } elseif ($action === 'delete') {
            if (isset($list[$index])) {
                array_splice($list, $index, 1);
            }
        }

        $trainer->$type = $list;
        $trainer->save();

        $tab = 'tab-tentang';
        if ($type === 'trainer_skills') {
            $tab = 'tab-keahlian';
        } elseif ($type === 'trainer_experiences') {
            $tab = 'tab-pengalaman';
        } elseif ($type === 'trainer_educations' || $type === 'trainer_certifications') {
            $tab = 'tab-pendidikan';
        }

        return redirect()->to(route('trainer.profile') . '#' . $tab)->with('success', 'Data profil berhasil diperbarui.');
    }

    public function uploadCourseMaterials(Request $request, $id)
    {
        // Check if upload was silently rejected by PHP (post_max_size exceeded)
        if ($request->server('CONTENT_LENGTH') > 0 && empty($_FILES) && empty($_POST)) {
            return response()->json([
                'success' => false,
                'error' => 'File terlalu besar. Ukuran maksimal yang diizinkan server adalah 512MB.',
            ], 413);
        }

        // Log incoming request for debugging
        \Illuminate\Support\Facades\Log::info('uploadCourseMaterials', [
            'course_id' => $id,
            'has_files' => $request->hasFile('files'),
            'files_count' => count($request->file('files') ?? []),
            'content_length' => $request->server('CONTENT_LENGTH'),
            'files_info' => collect($request->file('files') ?? [])->map(fn($f) => [
                'name' => $f?->getClientOriginalName(),
                'size' => $f?->getSize(),
                'mime' => $f?->getMimeType(),
                'ext' => $f?->getClientOriginalExtension(),
                'valid' => $f?->isValid(),
                'error' => $f?->getError(),
            ])->toArray(),
        ]);

        try {
            $request->validate([
                'target_modules' => 'required|string',
                'replace_module_id' => 'nullable|integer',
                'module_content_html' => 'nullable|string|max:300000',
                'files' => 'required_without:module_content_html|array',
                'files.*' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (!$value || !($value instanceof \Illuminate\Http\UploadedFile)) {
                            $fail('File tidak valid.');
                            return;
                        }
                        // Check PHP upload error code directly
                        if ($value->getError() !== UPLOAD_ERR_OK) {
                            $errorMessages = [
                                UPLOAD_ERR_INI_SIZE => 'File melebihi batas upload_max_filesize di server (' . ini_get('upload_max_filesize') . ').',
                                UPLOAD_ERR_FORM_SIZE => 'File melebihi batas MAX_FILE_SIZE di form.',
                                UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian. Coba lagi.',
                                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload.',
                                UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary server tidak tersedia.',
                                UPLOAD_ERR_CANT_WRITE => 'Gagal menyimpan file ke disk server.',
                                UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP.',
                            ];
                            $fail($errorMessages[$value->getError()] ?? 'Upload gagal dengan error code: ' . $value->getError());
                            return;
                        }
                        // Validate by extension only (avoid mimes which requires fileinfo)
                        $ext = strtolower($value->getClientOriginalExtension());
                        $allowed = ['pdf', 'mp4', 'pptx', 'ppt', 'docx', 'doc', 'jpg', 'png', 'jpeg'];
                        if (!in_array($ext, $allowed)) {
                            $fail("Format .$ext tidak diizinkan. Format yang diizinkan: " . implode(', ', $allowed));
                            return;
                        }
                        // 512MB size limit
                        if ($value->getSize() > 512 * 1024 * 1024) {
                            $fail('Ukuran file maksimal 512MB. File Anda: ' . round($value->getSize() / 1024 / 1024, 1) . 'MB.');
                        }
                    },
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Illuminate\Support\Facades\Log::warning('uploadCourseMaterials validation failed', [
                'errors' => $e->errors(),
                'course_id' => $id,
            ]);
            return response()->json([
                'success' => false,
                'error' => collect($e->errors())->flatten()->first() ?? 'Validasi gagal.',
                'validation_errors' => $e->errors(),
            ], 422);
        }

        $course = \App\Models\Course::findOrFail($id);
        if ((int) $course->trainer_id !== (int) Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Akses ditolak.']);
        }

        if ($this->isCourseMaterialLockedForTrainer((int) $course->id, (int) Auth::id())) {
            return response()->json([
                'success' => false,
                'error' => 'Materi course masih terkunci sampai undangan diterima.',
            ], 422);
        }

        // Ubah string "1,2" menjadi array [1, 2]
        $targetIds = collect(explode(',', $request->target_modules))
            ->map(fn($value) => (int) trim($value))
            ->filter(fn($value) => $value > 0)
            ->unique()
            ->values();

        if ($targetIds->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'Target modul tidak valid.']);
        }

        // Bulk approval check removed to support item-specific lockout.

        $uploadedCount = 0;
        $autoReplacedCount = 0;
        $adaptedSlotCount = 0;
        $rejectedFiles = [];
        $updatedModules = [];
        $contentHtml = trim((string) $request->input('module_content_html', ''));
        $targetTextModule = null;

        // Save rich text draft HTML into module description (text-based material style).
        if ($contentHtml !== '') {
            $targetTextModule = \App\Models\CourseModule::where('course_id', $id)
                ->whereIn('id', $targetIds)
                ->where('type', 'pdf')
                ->orderBy('order_no', 'asc')
                ->first();

            if (!$targetTextModule) {
                $targetTextModule = \App\Models\CourseModule::where('course_id', $id)
                    ->whereIn('id', $targetIds)
                    ->orderBy('order_no', 'asc')
                    ->first();
            }

            if ($targetTextModule) {
                if ($targetTextModule->review_status === 'approved') {
                    return response()->json([
                        'success' => false,
                        'error' => 'Modul teks telah disetujui oleh admin trainer dan tidak dapat diubah.',
                    ], 422);
                }
                $targetTextModule->update([
                    'description' => $contentHtml,
                    'review_status' => 'pending_review',
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'review_rejection_reason' => null,
                ]);
                $targetTextModule->refresh();
                $updatedModules[] = $this->mapStudioMaterialModule((int) $id, $targetTextModule);
            }
        }

        // Support text-only submission flow for module authoring (without file attachment).
        if (!$request->hasFile('files')) {
            if ($contentHtml !== '') {
                $course->update([
                    'status' => 'pending_review',
                    'approved_at' => null,
                    'approved_by' => null,
                    'rejected_at' => null,
                    'rejection_reason' => null,
                ]);
                $this->resetCourseLateStrikeIfOnTime($course);

                return response()->json([
                    'success' => true,
                    'message' => 'Materi teks berhasil disubmit ke Admin.',
                    'updated_modules' => $updatedModules,
                    'rejected_files' => [],
                ]);
            }

            return response()->json(['success' => false, 'error' => 'Tidak ada file.']);
        }

        if ($request->hasFile('files')) {
            $replaceModuleId = $request->input('replace_module_id');

            if (!empty($replaceModuleId)) {
                $replaceModule = \App\Models\CourseModule::where('course_id', $id)
                    ->where('id', (int) $replaceModuleId)
                    ->first();

                if (!$replaceModule || !$targetIds->contains((int) $replaceModuleId)) {
                    return response()->json(['success' => false, 'error' => 'File target penggantian tidak valid.']);
                }

                if ($replaceModule->review_status === 'approved') {
                    return response()->json(['success' => false, 'error' => 'Modul target penggantian telah disetujui dan tidak dapat diubah.'], 422);
                }

                $files = $request->file('files');
                if (count($files) !== 1) {
                    return response()->json(['success' => false, 'error' => 'Mode ganti file hanya menerima 1 file.']);
                }

                $file = $files[0];
                $ext = strtolower($file->getClientOriginalExtension());
                $uploadType = in_array($ext, ['mp4']) ? 'video' : 'pdf';

                if ($uploadType !== $replaceModule->type) {
                    return response()->json(['success' => false, 'error' => 'Tipe file tidak sesuai dengan target yang dipilih.']);
                }

                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

                if ($replaceModule->content_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($replaceModule->content_url)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($replaceModule->content_url);
                }

                $filepath = $file->storeAs('courses/' . $id . '/materials', $filename, 'public');

                $replaceModule->update([
                    'content_url' => $filepath,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'review_status' => 'pending_review',
                    'reviewed_at' => null,
                    'reviewed_by' => null,
                    'review_rejection_reason' => null,
                ]);

                $replaceModule->refresh();
                $updatedModules[] = $this->mapStudioMaterialModule((int) $id, $replaceModule);

                $course->update([
                    'status' => 'pending_review',
                    'approved_at' => null,
                    'approved_by' => null,
                    'rejected_at' => null,
                    'rejection_reason' => null,
                ]);
                $this->resetCourseLateStrikeIfOnTime($course);

                return response()->json([
                    'success' => true,
                    'message' => 'File berhasil diganti.',
                    'updated_modules' => $updatedModules,
                    'rejected_files' => [],
                ]);
            }

            $modulesByType = \App\Models\CourseModule::where('course_id', $id)
                ->whereIn('id', $targetIds)
                ->where(function ($q) {
                    $q->where('review_status', '!=', 'approved')
                      ->orWhereNull('review_status');
                })
                ->get()
                ->groupBy('type')
                ->map(fn($group) => $group->values());

            // Track available types untuk pesan error yang lebih informatif
            $availableTypes = [];
            foreach ($modulesByType as $slotType => $slotModules) {
                if (in_array($slotType, ['pdf', 'video'])) {
                    $availableTypes[$slotType] = [
                        'count' => $slotModules->count(),
                        'filled' => $slotModules->filter(fn($m) => !empty($m->content_url))->count(),
                    ];
                }
            }

            $slotStateByType = [];
            foreach ($modulesByType as $slotType => $slotModules) {
                $emptyQueue = [];
                foreach ($slotModules as $idx => $slotModule) {
                    if (empty($slotModule->content_url)) {
                        $emptyQueue[] = $idx;
                    }
                }

                $slotStateByType[$slotType] = [
                    'modules' => $slotModules,
                    'empty_queue' => $emptyQueue,
                    'replace_cursor' => 0,
                ];
            }

            foreach ($request->file('files') as $file) {
                $ext = strtolower($file->getClientOriginalExtension());
                $type = in_array($ext, ['mp4']) ? 'video' : 'pdf';
                $isAutoReplace = false;
                $usedCompatibleSlot = false;
                $stateKey = $type;

                // Flow mirip event: isi slot kosong dulu, jika penuh auto-overwrite slot tipe yang sama.
                $state = $slotStateByType[$stateKey] ?? null;

                // Backward compatibility for legacy courses:
                // if exact slot type does not exist in this unit, reuse other material slot (video/pdf).
                if ((!$state || $state['modules']->isEmpty()) && in_array($type, ['pdf', 'video'], true)) {
                    $fallbackStateKey = $type === 'pdf' ? 'video' : 'pdf';
                    $fallbackState = $slotStateByType[$fallbackStateKey] ?? null;
                    if ($fallbackState && !$fallbackState['modules']->isEmpty()) {
                        $state = $fallbackState;
                        $stateKey = $fallbackStateKey;
                        $usedCompatibleSlot = true;
                    }
                }

                if (!$state || $state['modules']->isEmpty()) {
                    $module = null;
                } elseif (!empty($state['empty_queue'])) {
                    $slotIdx = array_shift($state['empty_queue']);
                    $module = $state['modules']->get($slotIdx);
                    $slotStateByType[$stateKey] = $state;
                } else {
                    $slotCount = $state['modules']->count();
                    $slotIdx = $state['replace_cursor'] % $slotCount;
                    $module = $state['modules']->get($slotIdx);
                    $state['replace_cursor'] = $state['replace_cursor'] + 1;
                    $slotStateByType[$stateKey] = $state;
                    $isAutoReplace = true;
                }

                if ($module) {
                    $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());

                    if ($module->content_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($module->content_url)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($module->content_url);
                    }

                    $filepath = $file->storeAs('courses/' . $id . '/materials', $filename, 'public');

                    $module->update([
                        'type' => $type,
                        'content_url' => $filepath,
                        'file_name' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                        'review_status' => 'pending_review',
                        'reviewed_at' => null,
                        'reviewed_by' => null,
                        'review_rejection_reason' => null,
                    ]);
                    $module->refresh();
                    $updatedModules[] = $this->mapStudioMaterialModule((int) $id, $module);
                    $uploadedCount++;
                    if ($isAutoReplace) {
                        $autoReplacedCount++;
                    }
                    if ($usedCompatibleSlot) {
                        $adaptedSlotCount++;
                    }
                } else {
                    $suffix = ' (tipe ' . strtoupper($type) . ' tidak punya slot di bab ini)';
                    $rejectedFiles[] = $file->getClientOriginalName() . $suffix;
                }
            }

            if ($uploadedCount > 0) {
                $course->update([
                    'status' => 'pending_review',
                    'approved_at' => null,
                    'approved_by' => null,
                    'rejected_at' => null,
                    'rejection_reason' => null,
                ]);
                $this->resetCourseLateStrikeIfOnTime($course);
            }

            // Build helpful error message
            $msg = "$uploadedCount file berhasil diunggah.";
            if ($autoReplacedCount > 0) {
                $msg .= " {$autoReplacedCount} file otomatis mengganti file lama pada slot yang sama.";
            }
            if ($adaptedSlotCount > 0) {
                $msg .= " {$adaptedSlotCount} file ditempatkan ke slot materi kompatibel untuk menyesuaikan struktur course lama.";
            }
            if (count($rejectedFiles) > 0) {
                $msg .= " File (" . implode(", ", $rejectedFiles) . ") ditolak.";

                // Add info about available types
                if (!empty($availableTypes)) {
                    $typeInfo = [];
                    foreach ($availableTypes as $type => $info) {
                        $typeInfo[] = strtoupper($type) . " ({$info['filled']}/{$info['count']} terisi)";
                    }
                    $msg .= " Slot tersedia di bab ini: " . implode(", ", $typeInfo) . ".";
                } else {
                    $msg .= " Tidak ada slot File/Video di bab ini - periksa modul yang tersedia.";
                }
            }

            if ($uploadedCount === 0) {
                return response()->json([
                    'success' => false,
                    'error' => $msg,
                    'updated_modules' => [],
                    'available_types' => $availableTypes,
                    'rejected_files' => $rejectedFiles,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
                'updated_modules' => $updatedModules,
                'rejected_files' => $rejectedFiles,
                'available_types' => $availableTypes,
            ]);
        }
        return response()->json(['success' => false, 'error' => 'Tidak ada file.']);
    }

    public function uploadCourseEditorImage(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        $course = \App\Models\Course::findOrFail($id);
        if ((int) $course->trainer_id !== (int) Auth::id()) {
            return response()->json(['success' => false, 'error' => 'Akses ditolak.']);
        }

        if ($this->isCourseMaterialLockedForTrainer((int) $course->id, (int) Auth::id())) {
            return response()->json([
                'success' => false,
                'error' => 'Materi course masih terkunci sampai undangan diterima.',
            ], 422);
        }

        $file = $request->file('image');
        $uploadDir = public_path('uploads/courses/' . $id . '/editor-images');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9._-]/', '_', (string) $file->getClientOriginalName());
        $file->move($uploadDir, $filename);
        $relativePath = 'courses/' . $id . '/editor-images/' . $filename;

        return response()->json([
            'success' => true,
            'url' => '/uploads/' . $relativePath,
        ]);
    }

    private function resetCourseLateStrikeIfOnTime(Course $course): void
    {
        $invitation = TrainerNotification::query()
            ->where('trainer_id', (int) Auth::id())
            ->where('type', 'course_invitation')
            ->where(function ($query) use ($course) {
                $query->where('data', 'like', '%"entity_id":' . (int) $course->id . '%');
            })
            ->latest('id')
            ->first();

        if (!$invitation) {
            return;
        }

        $data = is_array($invitation->data) ? $invitation->data : [];
        $uploadDueAtRaw = (string) data_get($data, 'upload_due_at', '');
        if ($uploadDueAtRaw === '') {
            return;
        }

        try {
            $uploadDueAt = Carbon::parse($uploadDueAtRaw);
        } catch (\Throwable $e) {
            return;
        }

        if (now()->lte($uploadDueAt)) {
            app(TrainerActivityService::class)->resetLateUploads(Auth::user(), [
                'entity_type' => 'course',
                'entity_id' => (int) $course->id,
                'entity_title' => (string) ($course->name ?? ''),
                'url' => route('trainer.detail-course', $course->id),
            ]);
        }

        $data['material_uploaded_at'] = now()->toIso8601String();
        $invitation->data = $data;
        $invitation->save();
    }

    public function uploadEventMaterials(Request $request, $id)
    {
        $trainerId = Auth::id();
        $trainerName = mb_strtolower(trim((string) (Auth::user()?->name ?? '')));

        $trainer = Auth::user();
        $event = \App\Models\Event::findOrFail($id);
        $isPrimaryTrainer = (int) ($event->trainer_id ?? 0) === (int) ($trainer->id ?? 0);

        if (!$this->trainerCanManageEventMaterials($event, $trainer)) {
            return response()->json([
                'success' => false,
                'error' => 'Anda tidak memiliki akses ke materi event ini.',
            ], 403);
        }

        $assignment = $this->ensureEventAssignmentForTrainer($event, $trainer)
            ?: $this->latestEventAssignment((int) $event->id, (int) ($trainer->id ?? 0));

        $effectiveMaterialStatus = (string) ($isPrimaryTrainer
            ? ($event->material_status ?? '')
            : ($assignment->material_status ?? 'pending'));

        if ($effectiveMaterialStatus === 'approved') {
            return response()->json([
                'success' => false,
                'error' => 'Materi event ini telah disetujui oleh admin trainer dan tidak dapat diubah lagi.',
            ], 403);
        }



        $invitation = TrainerNotification::query()
            ->where('trainer_id', (int) Auth::id())
            ->where('type', 'event_invitation')
            ->where(function ($query) use ($event) {
                $query->where('data', 'like', '%"entity_id":' . (int) $event->id . '%');
            })
            ->latest('id')
            ->first();

        $invitationData = is_array($invitation?->data) ? $invitation->data : [];
        $invitationUploadDueAtRaw = (string) data_get($invitationData, 'upload_due_at', '');
        $effectiveDeadline = null;
        if ($invitationUploadDueAtRaw !== '') {
            $effectiveDeadline = Carbon::parse($invitationUploadDueAtRaw);
        } elseif (!empty($event->material_deadline)) {
            $effectiveDeadline = Carbon::parse($event->material_deadline);
        }

        $isRevisionUpload = $effectiveMaterialStatus === 'rejected';
        if ($isRevisionUpload) {
            $effectiveDeadline = null;
        }

        // Action: replace_module
        if ($request->input('action') === 'replace_module') {
            $moduleId = (int) $request->input('module_id');
            $module = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->where('trainer_id', $trainerId)
                ->first();

            if (!$module) {
                return response()->json(['success' => false, 'error' => 'Materi tidak ditemukan.'], 404);
            }

            if ($module->status === 'approved') {
                return response()->json(['success' => false, 'error' => 'Materi yang sudah disetujui tidak dapat diganti.'], 403);
            }

            $request->validate([
                'file' => 'nullable|file|mimes:pdf,mp4,pptx,ppt,docx,doc|max:512000',
                'material_link' => 'nullable|string|max:2048',
            ]);

            if (!$request->hasFile('file') && !$request->filled('material_link')) {
                return response()->json(['success' => false, 'error' => 'Tidak ada file atau link yang dikirim.']);
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $filepath = $file->storeAs('events/' . $event->id . '/materials/draft', $filename, 'public');

                // Delete old file if exists
                if ($module->path && !preg_match('#^https?://#i', $module->path)) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($module->path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($module->path);
                    }
                }

                $module->update([
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $filepath,
                    'status' => 'pending_review',
                    'rejection_reason' => null,
                ]);
            } elseif ($request->filled('material_link')) {
                $link = trim((string) $request->input('material_link'));
                if (!preg_match('#^https?://#i', $link) && !preg_match('#^ftp://#i', $link)) {
                    $link = 'https://' . $link;
                }

                $module->update([
                    'original_name' => 'Link: ' . $link,
                    'path' => $link,
                    'status' => 'pending_review',
                    'rejection_reason' => null,
                ]);
            }

            // Sync to event/assignment level to trigger review again
            if ($assignment) {
                $assignment->update([
                    'material_status' => 'pending_review',
                    'material_submitted_at' => now(),
                    'material_approved_at' => null,
                    'material_approved_by' => null,
                    'material_rejection_reason' => null,
                ]);
            }
            if ($isPrimaryTrainer || !$assignment) {
                $event->update([
                    'material_status' => 'pending_review',
                    'material_approved_at' => null,
                    'material_approved_by' => null,
                    'material_rejection_reason' => null,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Berkas revisi berhasil diunggah dan status diubah ke Menunggu Review.',
            ]);
        }

        // Action: delete_module
        if ($request->input('action') === 'delete_module') {

            $moduleId = (int) $request->input('module_id');
            $module = \App\Models\EventTrainerModule::where('id', $moduleId)
                ->where('event_id', $event->id)
                ->where('trainer_id', $trainerId)
                ->first();

            if (!$module) {
                return response()->json(['success' => false, 'error' => 'Materi tidak ditemukan.'], 404);
            }

            if ($module->status === 'approved') {
                return response()->json(['success' => false, 'error' => 'Materi yang sudah disetujui tidak dapat dihapus.'], 403);
            }

            // Delete old file if exists
            if ($module->path && !preg_match('#^https?://#i', $module->path)) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($module->path)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($module->path);
                }
            }

            $module->delete();

            $this->syncEventMaterialStatus($event->id, $trainerId);

            return response()->json([
                'success' => true,
                'message' => 'Materi berhasil dihapus.',
            ]);
        }

        // Action: delete_draft
        if ($request->input('action') === 'delete_draft') {
            $fileIndex = (int) $request->input('index');
            $cacheKey = "trainer_draft_files_{$trainerId}_{$event->id}";
            $draftFiles = \Illuminate\Support\Facades\Cache::get($cacheKey, []);
            if (isset($draftFiles[$fileIndex])) {
                $filePath = $draftFiles[$fileIndex]['path'];
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($filePath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($filePath);
                }
                unset($draftFiles[$fileIndex]);
                $draftFiles = array_values($draftFiles);
                \Illuminate\Support\Facades\Cache::put($cacheKey, $draftFiles, now()->addDays(30));
                return response()->json(['success' => true, 'message' => 'Draf materi berhasil dihapus.']);
            }
            return response()->json(['success' => false, 'error' => 'File tidak ditemukan.'], 404);
        }

        // Action: save_draft_survey
        if ($request->input('action') === 'save_draft_survey') {
            $fileIndex = (int) $request->input('index');
            $surveyLink = $request->input('survey_link');

            if ($surveyLink !== null) {
                $surveyLink = trim((string) $surveyLink);
                if ($surveyLink === '') {
                    $surveyLink = null;
                } elseif (!preg_match('#^https?://#i', $surveyLink) && !preg_match('#^ftp://#i', $surveyLink)) {
                    $surveyLink = 'https://' . $surveyLink;
                }
            }

            $cacheKey = "trainer_draft_files_{$trainerId}_{$event->id}";
            $draftFiles = \Illuminate\Support\Facades\Cache::get($cacheKey, []);
            if (isset($draftFiles[$fileIndex])) {
                $draftFiles[$fileIndex]['survey_link'] = $surveyLink;
                \Illuminate\Support\Facades\Cache::put($cacheKey, $draftFiles, now()->addDays(30));
                return response()->json([
                    'success' => true,
                    'message' => 'Link survei berhasil disimpan.',
                    'survey_link' => $surveyLink
                ]);
            }
            return response()->json(['success' => false, 'error' => 'Draf materi tidak ditemukan.'], 404);
        }

        // Action: submit_final
        if ($request->input('action') === 'submit_final') {
            $cacheKey = "trainer_draft_files_{$trainerId}_{$event->id}";
            $draftFiles = \Illuminate\Support\Facades\Cache::get($cacheKey, []);
            if (empty($draftFiles)) {
                return response()->json(['success' => false, 'error' => 'Tidak ada file materi baru di draf untuk dikirim.'], 422);
            }

            $this->performSubmitFinal($event, $trainerId, $isPrimaryTrainer, $assignment, $effectiveDeadline, $invitation);

            return response()->json([
                'success' => true,
                'message' => 'Materi event berhasil dikirim untuk review admin.',
            ]);
        }

        // Default: upload new draft file
        $request->validate([
            'files' => 'required_without:material_link|array|max:5',
            'files.*' => 'nullable|file|mimes:pdf,mp4,pptx,ppt,docx,doc|max:512000',
            'material_link' => 'nullable|string|max:2048',
            'material_survey_link' => 'nullable|string|max:2048'
        ]);

        if (!$isRevisionUpload && $effectiveDeadline && now()->gt($effectiveDeadline)) {
            return response()->json([
                'success' => false,
                'error' => 'Batas pengumpulan materi sudah lewat. Silakan hubungi admin trainer.',
            ], 422);
        }

        if (!$request->hasFile('files') && !$request->filled('material_link')) {
            return response()->json(['success' => false, 'error' => 'Tidak ada file atau link yang dikirim.']);
        }

        $surveyLink = $request->input('material_survey_link');
        if ($surveyLink !== null) {
            $surveyLink = trim((string) $surveyLink);
            if ($surveyLink === '') {
                $surveyLink = null;
            } elseif (!preg_match('#^https?://#i', $surveyLink) && !preg_match('#^ftp://#i', $surveyLink)) {
                $surveyLink = 'https://' . $surveyLink;
            }
        }

        $storedFiles = [];
        $cacheKey = "trainer_draft_files_{$trainerId}_{$event->id}";
        $draftFiles = \Illuminate\Support\Facades\Cache::get($cacheKey, []);

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $filepath = $file->storeAs('events/' . $event->id . '/materials/draft', $filename, 'public');

                $newDraft = [
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $filepath,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toDateTimeString(),
                    'survey_link' => $surveyLink,
                ];

                $draftFiles[] = $newDraft;
                $storedFiles[] = $newDraft;
            }
        }

        if ($request->filled('material_link')) {
            $link = trim((string) $request->input('material_link'));
            if (!preg_match('#^https?://#i', $link) && !preg_match('#^ftp://#i', $link)) {
                $link = 'https://' . $link;
            }

            $newDraft = [
                'original_name' => 'Link: ' . $link,
                'path' => $link,
                'size' => 0,
                'mime_type' => 'text/url',
                'uploaded_at' => now()->toDateTimeString(),
                'survey_link' => $surveyLink,
            ];

            $draftFiles[] = $newDraft;
            $storedFiles[] = $newDraft;
        }

        \Illuminate\Support\Facades\Cache::put($cacheKey, $draftFiles, now()->addDays(30));

        return response()->json([
            'success' => true,
            'message' => 'Materi berhasil ditambahkan ke draf.',
            'files' => $storedFiles,
        ]);
    }

    public function saveCourseQuiz(Request $request, $id)
    {
        $request->validate([
            'quiz_module_id' => 'required|exists:course_module,id',
            'passingGrade' => 'required|integer|min:0|max:100',
            'questions' => 'required|array',
        ]);

        $course = \App\Models\Course::findOrFail($id);
        if ((int) $course->trainer_id !== (int) Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.']);
        }

        if ($this->isCourseMaterialLockedForTrainer((int) $course->id, (int) Auth::id())) {
            return response()->json([
                'success' => false,
                'message' => 'Materi course masih terkunci sampai undangan diterima.',
            ], 422);
        }

        $questionsData = $request->questions;
        if (empty($questionsData)) {
            return response()->json(['success' => false, 'message' => 'Kuis minimal 1 soal.']);
        }

        // Kunci Quiz ke Slot Bab Ini
        $quizModule = \App\Models\CourseModule::where('id', $request->quiz_module_id)->where('course_id', $id)->firstOrFail();

        if ($quizModule->review_status === 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Kuis ini telah disetujui oleh admin trainer dan tidak dapat diubah lagi.',
            ], 422);
        }
        $quizModule->update(['content_url' => 'quiz_submitted', 'review_status' => 'pending_review']);

        // Delete old questions
        $quizModule->quizQuestions()->delete();

        // Create new questions
        $savedQuestions = [];
        foreach ($questionsData as $orderIndex => $questionData) {
            $quizQuestion = $quizModule->quizQuestions()->create([
                'question' => $questionData['text'],
                'points' => $questionData['weight'] ?? 10,
                'order_no' => $orderIndex + 1,
            ]);

            // Create answers for this question
            if (!empty($questionData['options']) && is_array($questionData['options'])) {
                foreach ($questionData['options'] as $optionIndex => $optionText) {
                    $quizQuestion->answers()->create([
                        'answer_text' => $optionText,
                        'is_correct' => ($optionIndex === (int) $questionData['correctAnswer']),
                        'order_no' => $optionIndex + 1,
                    ]);
                }
            }

            $savedQuestions[] = [
                'text' => (string) $quizQuestion->question,
                'weight' => (int) $quizQuestion->points,
                'options' => collect($questionData['options'] ?? [])->map(fn($opt) => (string) $opt)->values()->all(),
                'correctAnswer' => (int) ($questionData['correctAnswer'] ?? 0),
            ];
        }

        $course->update(['status' => 'pending_review']);
        $this->resetCourseLateStrikeIfOnTime($course);

        return response()->json([
            'success' => true,
            'message' => 'Kuis Bab berhasil disimpan!',
            'quiz_module' => [
                'id' => (int) $quizModule->id,
                'title' => (string) ($quizModule->title ?: ('Quiz Unit')),
                'order_no' => (int) $quizModule->order_no,
                'questions_count' => count($savedQuestions),
                'updated_at' => optional($quizModule->fresh()->updated_at)->toDateTimeString(),
                'questions' => $savedQuestions,
            ],
        ]);
    }

    /**
     * Accept event invitation from trainer
     */
    public function acceptEventInvitation(Request $request, $id)
    {
        $trainerId = Auth::id();
        $trainer = Auth::user();
        if (!$trainer) {
            abort(403);
        }

        if ((string) ($trainer->user_status ?? 'active') !== 'active') {
            return back()->with('error', 'Akun Anda tidak aktif untuk menerima undangan.');
        }

        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Update trainer notification status
        $notification = \App\Models\TrainerNotification::where('trainer_id', $trainerId)
            ->where('type', 'event_invitation')
            ->where(function ($q) use ($id) {
                $q->whereJsonContains('data->entity_id', $id)
                    ->orWhereJsonContains('data->entity_id', (string) $id);
            })
            ->first();

        if ($notification) {
            $data = is_array($notification->data) ? $notification->data : [];
            $data['invitation_status'] = 'accepted';
            $data['responded_at'] = now()->toIso8601String();
            $data['e_agreement_accepted'] = true;
            $data['e_agreement_accepted_at'] = now()->toIso8601String();
            $data['upload_due_at'] = now()->addDays(3)->toIso8601String();

            $notification->update([
                'invitation_status' => 'accepted',
                'responded_at' => now(),
                'data' => $data,
            ]);

            app(TrainerActivityService::class)->resetExpiredInvitationStreak($trainer);
        }

        return redirect()
            ->route('trainer.events.show', $event->id)
            ->with('success', 'Undangan event berhasil diterima. Silakan upload materi untuk event ini.');
    }

    /**
     * Reject event invitation from trainer
     */
    public function rejectEventInvitation(Request $request, $id)
    {
        $trainerId = Auth::id();
        $trainer = Auth::user();
        $event = \App\Models\Event::where('id', $id)
            ->where('trainer_id', $trainerId)
            ->firstOrFail();

        // Update trainer notification status
        $notification = \App\Models\TrainerNotification::where('trainer_id', $trainerId)
            ->where('type', 'event_invitation')
            ->where(function ($q) use ($id) {
                $q->whereJsonContains('data->entity_id', $id)
                    ->orWhereJsonContains('data->entity_id', (string) $id);
            })
            ->first();

        if ($notification) {
            $data = is_array($notification->data) ? $notification->data : [];
            $data['invitation_status'] = 'rejected';
            $data['responded_at'] = now()->toIso8601String();

            $notification->update([
                'invitation_status' => 'rejected',
                'responded_at' => now(),
                'data' => $data,
            ]);

            if ($trainer) {
                app(TrainerActivityService::class)->resetExpiredInvitationStreak($trainer);
            }
        }

        return back()->with('success', 'Undangan event berhasil ditolak.');
    }

    public function viewCourseMaterial($courseId, $moduleId)
    {
        $course = \App\Models\Course::where('id', $courseId)
            ->where('trainer_id', Auth::id())
            ->firstOrFail();

        $module = \App\Models\CourseModule::where('id', $moduleId)
            ->where('course_id', $course->id)
            ->firstOrFail();

        if (empty($module->content_url)) {
            abort(404, 'File materi tidak ditemukan.');
        }

        if (!Storage::disk('public')->exists($module->content_url)) {
            abort(404, 'File materi tidak tersedia di storage.');
        }

        $headers = [];
        if (!empty($module->mime_type)) {
            $headers['Content-Type'] = $module->mime_type;
        }

        $filePath = Storage::disk('public')->path($module->content_url);
        if (!file_exists($filePath)) {
            abort(404, 'File materi tidak tersedia di server.');
        }

        return response()->file($filePath, $headers);
    }

    public function certificatesIndex()
    {
        $trainer = Auth::user();
        // $this->ensureTrainerCertificatesSynced($trainer);

        $context = (string) request()->query('context', '');
        $targetId = (int) request()->query('id', 0);

        $finishedEvents = Event::query()
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
            ->get(['id', 'title', 'jenis', 'event_date', 'certificate_logo', 'certificate_signature', 'certificate_template']);

        $finishedCourses = Course::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['published', 'approved', 'active'])
            ->withCount('enrollments')
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'approved_at', 'status', 'certificate_logo', 'certificate_signature', 'certificate_template']);

        $certificates = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published'])
            ->with('certifiable')
            ->get();

        $certMap = [];
        foreach ($certificates as $cert) {
            $key = $cert->certifiable_type . ':' . (int) $cert->certifiable_id;
            $certMap[$key] = $cert;
        }

        $historyItems = collect();
        $addedKeys = [];

        foreach ($finishedEvents as $event) {
            $key = Event::class . ':' . (int) $event->id;
            $cert = $certMap[$key] ?? null;
            [$logosBase64, $signaturesBase64, $signaturesData, $template] = $this->extractTrainerAssetsBase64($event);
            $hasLogo = !empty($event->certificate_logo);
            $hasSignature = !empty($event->certificate_signature);
            $hasTemplate = !empty($event->certificate_template);
            $historyItems->push([
                'type' => 'event',
                'id' => (int) $event->id,
                'title' => $event->title,
                'date' => $event->event_date,
                'statusLabel' => 'Selesai',
                'certificate' => $cert,
                'downloadUrl' => $cert ? route('trainer.certificates.events.download', $event) : null,
                'showUrl' => $cert ? route('trainer.certificates.events.show', $event) : null,
                'highlight' => $context === 'event' && $targetId === (int) $event->id,
                'registrations_count' => (int) ($event->registrations_count ?? 0),
                'has_logo' => $hasLogo,
                'has_signature' => $hasSignature,
                'has_template' => $hasTemplate,
                'logosBase64' => $logosBase64,
                'signaturesBase64' => $signaturesBase64,
                'signaturesData' => $signaturesData,
                'template' => $template,
                'model' => $event,
            ]);
            $addedKeys[$key] = true;
        }

        foreach ($finishedCourses as $course) {
            $key = Course::class . ':' . (int) $course->id;
            $cert = $certMap[$key] ?? null;
            [$logosBase64, $signaturesBase64, $signaturesData, $template] = $this->extractTrainerAssetsBase64($course);
            $hasLogo = !empty($course->certificate_logo);
            $hasSig = !empty($course->certificate_signature);
            $hasTemplate = !empty($course->certificate_template);
            $historyItems->push([
                'type' => 'course',
                'id' => (int) $course->id,
                'title' => $course->name,
                'date' => $course->approved_at,
                'statusLabel' => 'Selesai',
                'certificate' => $cert,
                'downloadUrl' => $cert ? route('trainer.certificates.courses.download', $course) : null,
                'showUrl' => $cert ? route('trainer.certificates.courses.show', $course) : null,
                'highlight' => $context === 'course' && $targetId === (int) $course->id,
                'registrations_count' => (int) ($course->enrollments_count ?? 0),
                'has_logo' => $hasLogo,
                'has_signature' => $hasSig,
                'has_template' => $hasTemplate,
                'logosBase64' => $logosBase64,
                'signaturesBase64' => $signaturesBase64,
                'signaturesData' => $signaturesData,
                'template' => $template,
                'model' => $course,
            ]);
            $addedKeys[$key] = true;
        }

        foreach ($certificates as $cert) {
            $key = $cert->certifiable_type . ':' . (int) $cert->certifiable_id;
            if (isset($addedKeys[$key])) {
                continue;
            }

            $model = $cert->certifiable;
            if (!$model) {
                continue;
            }

            [$logosBase64, $signaturesBase64, $signaturesData, $template] = $this->extractTrainerAssetsBase64($model);
            $hasLogo = !empty($model->certificate_logo);
            $isEvent = $cert->certifiable_type === Event::class;
            $hasSig = !empty($model->certificate_signature);
            $hasTemplate = !empty($model->certificate_template);

            $historyItems->push([
                'type' => $isEvent ? 'event' : 'course',
                'id' => (int) $model->id,
                'title' => $isEvent ? $model->title : $model->name,
                'date' => $isEvent ? $model->event_date : ($model->approved_at ?? $model->updated_at),
                'statusLabel' => 'Diterbitkan',
                'certificate' => $cert,
                'downloadUrl' => $isEvent ? route('trainer.certificates.events.download', $model) : route('trainer.certificates.courses.download', $model),
                'showUrl' => $isEvent ? route('trainer.certificates.events.show', $model) : route('trainer.certificates.courses.show', $model),
                'highlight' => $context === ($isEvent ? 'event' : 'course') && $targetId === (int) $model->id,
                'registrations_count' => $isEvent ? (int) ($model->registrations_count ?? 0) : (int) ($model->enrollments_count ?? 0),
                'has_logo' => $hasLogo,
                'has_signature' => $hasSig,
                'has_template' => $hasTemplate,
                'logosBase64' => $logosBase64,
                'signaturesBase64' => $signaturesBase64,
                'signaturesData' => $signaturesData,
                'template' => $template,
                'model' => $model,
            ]);
            $addedKeys[$key] = true;
        }

        $historyItems = $historyItems->sortByDesc(fn($item) => $item['date'] ?? now());

        return view('trainer.certificates.index', [
            'historyItems' => $historyItems,
        ]);
    }

    public function certificateEventShow(Request $request, Event $event)
    {
        $trainer = Auth::user();
        $trainerCert = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published'])
            ->where('certifiable_type', Event::class)
            ->where('certifiable_id', $event->id)
            ->latest('issued_at')
            ->firstOrFail();

        $issuedAt = $trainerCert->issued_at ?? now();
        $data = $this->buildTrainerCertificateDataFromEvent($request, $event, $trainer, $issuedAt);
        $data['certificateNumber'] = $trainerCert->certificate_number;
        $data['roleLabel'] = $this->certificateTypeLabel((string) $trainerCert->type_code);

        return view('trainer.certificates.show', $data);
    }

    public function certificateEventDownload(Request $request, Event $event)
    {
        $trainer = Auth::user();
        $trainerCert = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published'])
            ->where('certifiable_type', Event::class)
            ->where('certifiable_id', $event->id)
            ->latest('issued_at')
            ->firstOrFail();

        if (empty($trainerCert->file_path) || !is_file(storage_path('app/' . $trainerCert->file_path))) {
            $this->generateTrainerCertificatePdf($trainerCert);
            $trainerCert = $trainerCert->fresh();
        }

        if (!empty($trainerCert->file_path)) {
            $absolutePath = storage_path('app/' . $trainerCert->file_path);
            if (is_file($absolutePath)) {
                $filename = 'Sertifikat_Trainer_' . Str::slug($event->title) . '_' . Str::slug($trainer->name) . '.pdf';
                return response()->download($absolutePath, $filename, [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        }

        abort(404, 'File sertifikat belum tersedia.');
    }

    public function certificateCourseShow(Request $request, Course $course)
    {
        $trainer = Auth::user();
        $trainerCert = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published'])
            ->where('certifiable_type', Course::class)
            ->where('certifiable_id', $course->id)
            ->latest('issued_at')
            ->firstOrFail();

        $issuedAt = $trainerCert->issued_at ?? now();
        $data = $this->buildTrainerCertificateDataFromCourse($request, $course, $trainer, $issuedAt);
        $data['certificateNumber'] = $trainerCert->certificate_number;
        $data['roleLabel'] = $this->certificateTypeLabel((string) $trainerCert->type_code);

        return view('trainer.certificates.show', $data);
    }

    public function certificateCourseDownload(Request $request, Course $course)
    {
        $trainer = Auth::user();
        $trainerCert = TrainerCertificate::query()
            ->where('trainer_id', $trainer->id)
            ->whereIn('status', ['sent', 'published'])
            ->where('certifiable_type', Course::class)
            ->where('certifiable_id', $course->id)
            ->latest('issued_at')
            ->firstOrFail();

        if (empty($trainerCert->file_path) || !is_file(storage_path('app/' . $trainerCert->file_path))) {
            $this->generateTrainerCertificatePdf($trainerCert);
            $trainerCert = $trainerCert->fresh();
        }

        if (!empty($trainerCert->file_path)) {
            $absolutePath = storage_path('app/' . $trainerCert->file_path);
            if (is_file($absolutePath)) {
                $filename = 'Sertifikat_Trainer_' . Str::slug($course->name) . '_' . Str::slug($trainer->name) . '.pdf';
                return response()->download($absolutePath, $filename, [
                    'Content-Type' => 'application/pdf',
                ]);
            }
        }

        abort(404, 'File sertifikat belum tersedia.');
    }

    private function trainerCanManageEventMaterials(Event $event, $trainer): bool
    {
        if (!$trainer) {
            return false;
        }

        $trainerId = (int) ($trainer->id ?? 0);
        $trainerName = mb_strtolower(trim((string) ($trainer->name ?? '')));

        if ($this->trainerMatchesEvent($event, $trainerId, $trainerName)) {
            return true;
        }

        $invitation = $this->latestEventInvitation((int) ($event->id ?? 0), $trainerId);
        if ($invitation) {
            return in_array((string) $invitation->effectiveInvitationStatus(), ['accepted', 'pending'], true);
        }

        return false;
    }



    private function getModuleType($extension)
    {
        return match (strtolower($extension)) {
            'pdf' => 'document',
            'doc', 'docx' => 'document',
            'ppt', 'pptx' => 'presentation',
            'mp4' => 'video',
            'jpg', 'jpeg', 'png' => 'image',
            default => 'file',
        };
    }

    private function buildIdsporaCertificateNumber(string $activityCode, string $typeCode, string $sequence, \Carbon\CarbonInterface $issuedAt): string
    {
        $romanMonths = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        $monthRoman = $romanMonths[(int) $issuedAt->format('n')] ?? '';
        $year = $issuedAt->format('Y');
        $seqDigits = preg_replace('/\D+/', '', $sequence) ?: '1';
        $seq = str_pad(substr($seqDigits, -3), 3, '0', STR_PAD_LEFT);

        $activity = strtoupper(trim($activityCode ?: 'WBN'));
        $type = strtoupper(trim($typeCode ?: 'TRN'));

        return "IDSP/{$activity}/{$type}/{$seq}/{$monthRoman}/{$year}";
    }

    private function extractEventAssetsBase64(Event $event): array
    {
        $logos = [];
        foreach (is_array($event->certificate_logo) ? $event->certificate_logo : [] as $l) {
            $path = str_replace('storage/', '', (string) $l);
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                $absolutePath = Storage::disk('public')->path($path);
                $mime = (is_string($absolutePath) && is_file($absolutePath)) ? (mime_content_type($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream';
                $logos[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
            }
        }

        $sigs = [];
        foreach (is_array($event->certificate_signature) ? $event->certificate_signature : [] as $s) {
            $path = str_replace('storage/', '', (string) $s);
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                $absolutePath = Storage::disk('public')->path($path);
                $mime = (is_string($absolutePath) && is_file($absolutePath)) ? (mime_content_type($absolutePath) ?: 'application/octet-stream') : 'application/octet-stream';
                $sigs[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
            }
        }

        return [$logos, $sigs];
    }

    private function extractTrainerAssetsBase64($certifiable): array
    {
        $logosBase64 = [];
        $signaturesBase64 = [];
        $signaturesData = [];
        $template = 'template_1';

        $certifiableType = get_class($certifiable);
        $certifiableId = (int) $certifiable->id;

        $assets = TrainerCertificateAsset::query()
            ->where('certifiable_type', $certifiableType)
            ->where('certifiable_id', $certifiableId)
            ->orderBy('order_no')
            ->get();

        if ($assets->isNotEmpty()) {
            $templateAsset = $assets->where('type', 'template')->first();
            $template = $certifiable->certificate_template
                ?? $templateAsset?->name
                ?? 'template_1';

            foreach ($assets as $asset) {
                $path = $asset->image_path;
                if (!$path || $path === '-') {
                    continue;
                }

                $normalized = ltrim(str_replace('\\', '/', $path), '/');
                $normalized = preg_replace('~^public/~i', '', $normalized) ?? $normalized;
                $normalized = preg_replace('~^storage/app/public/~i', '', $normalized) ?? $normalized;
                if (str_starts_with($normalized, 'storage/')) {
                    $normalized = substr($normalized, 8);
                }

                $content = null;
                $mime = null;

                if (Storage::disk('public')->exists($normalized)) {
                    $content = Storage::disk('public')->get($normalized);
                    $mime = Storage::disk('public')->mimeType($normalized);
                } else {
                    $possiblePaths = [
                        public_path($path),
                        public_path('storage/' . $normalized),
                        public_path('uploads/' . $normalized),
                        storage_path('app/public/' . $normalized),
                    ];
                    foreach ($possiblePaths as $p) {
                        if (is_file($p)) {
                            $content = file_get_contents($p);
                            $mime = mime_content_type($p);
                            break;
                        }
                    }
                }

                if ($content) {
                    $base64 = 'data:' . ($mime ?: 'image/png') . ';base64,' . base64_encode($content);
                    if ($asset->type === 'logo') {
                        $logosBase64[] = $base64;
                    } elseif ($asset->type === 'signature') {
                        $signaturesBase64[] = $base64;
                        $signaturesData[] = [
                            'base64' => $base64,
                            'name' => $asset->name,
                            'position' => $asset->position,
                        ];
                    }
                }
            }
        } else {
            $template = $certifiable->certificate_template ?? 'template_1';

            $logosRaw = is_array($certifiable->certificate_logo)
                ? $certifiable->certificate_logo
                : ($certifiable->certificate_logo ? [$certifiable->certificate_logo] : []);

            foreach ($logosRaw as $l) {
                $path = str_replace('storage/', '', (string) $l);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $absolutePath = Storage::disk('public')->path($path);
                    $mime = (is_string($absolutePath) && is_file($absolutePath))
                        ? (mime_content_type($absolutePath) ?: 'image/png')
                        : 'image/png';
                    $logosBase64[] = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                }
            }

            $sigsRaw = is_array($certifiable->certificate_signature)
                ? $certifiable->certificate_signature
                : ($certifiable->certificate_signature ? [$certifiable->certificate_signature] : []);

            foreach ($sigsRaw as $s) {
                $isObj = is_array($s);
                $imgPath = $isObj ? ($s['image'] ?? '') : $s;
                $sigName = $isObj ? ($s['name'] ?? '') : '';
                $sigPos = $isObj ? ($s['position'] ?? '') : '';

                $path = str_replace('storage/', '', (string) $imgPath);
                if ($path !== '' && Storage::disk('public')->exists($path)) {
                    $absolutePath = Storage::disk('public')->path($path);
                    $mime = (is_string($absolutePath) && is_file($absolutePath))
                        ? (mime_content_type($absolutePath) ?: 'image/png')
                        : 'image/png';
                    $b64 = 'data:' . $mime . ';base64,' . base64_encode(Storage::disk('public')->get($path));
                    $signaturesBase64[] = $b64;
                    $signaturesData[] = [
                        'base64' => $b64,
                        'name' => $sigName,
                        'position' => $sigPos,
                    ];
                }
            }
        }

        return [$logosBase64, $signaturesBase64, $signaturesData, $template];
    }

    public function generateTrainerCertificatePdf(TrainerCertificate $trainerCertificate): void
    {
        $trainer = $trainerCertificate->trainer;
        $certifiable = $trainerCertificate->certifiable;

        if (!$trainer || !$certifiable) {
            return;
        }

        $context = strtolower(class_basename(get_class($certifiable))) === 'course' ? 'course' : 'event';
        $issuedAt = $trainerCertificate->issued_at ?? $trainerCertificate->created_at ?? now();

        if ($context === 'event') {
            $pdfData = $this->buildTrainerCertificateDataFromEvent(request(), $certifiable, $trainer, $issuedAt);
        } else {
            $pdfData = $this->buildTrainerCertificateDataFromCourse(request(), $certifiable, $trainer, $issuedAt);
        }

        $pdfData['certificateNumber'] = $trainerCertificate->certificate_number;
        $pdfData['roleLabel'] = $this->certificateTypeLabel((string) $trainerCertificate->type_code);

        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $dompdf->setOptions($options);

        $html = view('trainer.certificates.certificate-pdf', $pdfData)->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $relativeDir = 'trainer_certificates/' . $trainer->id . '/' . $context . '/' . $certifiable->id;
        $filename = Str::slug($trainerCertificate->certificate_number, '_') . '.pdf';
        $relativePath = $relativeDir . '/' . $filename;
        $absolutePath = storage_path('app/' . $relativePath);

        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }
        file_put_contents($absolutePath, $dompdf->output());

        $trainerCertificate->update(['file_path' => $relativePath]);
    }

    private function buildTrainerCertificateDataFromEvent(Request $request, Event $event, $trainer, \Carbon\CarbonInterface $issuedAt): array
    {
        [$logosBase64, $signaturesBase64, $signaturesData, $template] = $this->extractTrainerAssetsBase64($event);

        $activityCodeMap = [
            'webinar' => 'WBN',
            'seminar' => 'SMN',
            'workshop' => 'WRT',
            'training' => 'WRT',
            'video' => 'VDP',
            'e-learning' => 'ELR',
            'elearning' => 'ELR',
        ];
        $jenis = strtolower((string) ($event->jenis ?? ''));
        $defaultActivityCode = $activityCodeMap[$jenis] ?? 'WBN';

        $activityCode = (string) $request->query('activity', $defaultActivityCode);
        $typeCode = (string) $request->query('type', 'TRN');
        $sequence = (string) $request->query('seq', '001');

        $certificateNumber = $this->buildIdsporaCertificateNumber($activityCode, $typeCode, $sequence, $issuedAt);

        return [
            'context' => 'event',
            'event' => $event,
            'course' => null,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'signaturesData' => $signaturesData,
            'template' => $template,
            'roleLabel' => $this->certificateTypeLabel($typeCode),
        ];
    }

    private function buildTrainerCertificateDataFromCourse(Request $request, Course $course, $trainer, \Carbon\CarbonInterface $issuedAt): array
    {
        [$logosBase64, $signaturesBase64, $signaturesData, $template] = $this->extractTrainerAssetsBase64($course);

        $activityCode = (string) $request->query('activity', 'ELR');
        $typeCode = (string) $request->query('type', 'TRN');
        $sequence = (string) $request->query('seq', '001');

        $certificateNumber = $this->buildIdsporaCertificateNumber($activityCode, $typeCode, $sequence, $issuedAt);

        return [
            'context' => 'course',
            'event' => null,
            'course' => $course,
            'user' => $trainer,
            'issuedAt' => $issuedAt,
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'signaturesData' => $signaturesData,
            'template' => $template,
            'roleLabel' => $this->certificateTypeLabel($typeCode),
        ];
    }

    private function certificateTypeLabel(string $typeCode): string
    {
        $map = [
            'SRT' => 'Peserta',
            'MC' => 'MC',
            'TRN' => 'Narasumber',
            'PNT' => 'Panitia',
            'CLB' => 'Kolaborator',
            'MOD' => 'Moderator',
            'GRD' => 'Kelulusan',
            'SPV' => 'Supervisor/penilai',
        ];
        $key = strtoupper(trim($typeCode));
        return $map[$key] ?? $key;
    }

    public function storeFeedbackReply(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $validated = $request->validate([
            'id' => 'nullable|integer',
            'feedback_id' => 'nullable|integer',
            'type' => 'nullable|string|in:course,event',
            'response' => 'required|string|min:3|max:5000',
        ]);

        $type = $request->input('type', 'event');
        $id = $request->input('id', $request->input('feedback_id'));

        if ($type === 'course') {
            $review = \App\Models\Review::findOrFail($id);
            
            // Verify access: course belongs to the trainer
            $course = \App\Models\Course::findOrFail($review->course_id);
            if ((int)$course->trainer_id !== (int)$user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this review'
                ], 403);
            }

            $review->trainer_reply = $validated['response'];
            $review->trainer_reply_at = now();
            $review->save();

            return response()->json([
                'success' => true,
                'message' => 'Reply saved successfully',
                'data' => $review
            ]);
        } else {
            // Verify that the feedback belongs to an event the trainer manages
            $feedback = \App\Models\Feedback::findOrFail($id);

            $trainerHasAccess = \App\Models\Event::where('id', $feedback->event_id)
                ->where(function ($q) use ($user) {
                    $q->where('trainer_id', $user->id)
                        ->orWhereHas('speakers', function ($sq) use ($user) {
                            $sq->where('trainer_id', $user->id);
                        });
                })
                ->exists();

            if (!$trainerHasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this feedback'
                ], 403);
            }

            // Create the reply
            $reply = \App\Models\FeedbackReply::create([
                'feedback_id' => $feedback->id,
                'trainer_id' => $user->id,
                'response' => $validated['response'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reply saved successfully',
                'data' => $reply
            ]);
        }
    }

    public function toggleLike(Request $request, $id)
    {
        $type = $request->input('type', 'event');
        
        if ($type === 'course') {
            $item = \App\Models\Review::findOrFail($id);
        } else {
            $item = \App\Models\Feedback::findOrFail($id);
        }

        $item->is_liked = !$item->is_liked;
        $item->save();

        return response()->json([
            'success' => true,
            'is_liked' => $item->is_liked
        ]);
    }

    private function getCertifiableModel(string $context, int $id)
    {
        if ($context === 'event') {
            return Event::findOrFail($id);
        } elseif ($context === 'course') {
            return Course::findOrFail($id);
        }

        abort(400, 'Context tidak valid.');
    }
}