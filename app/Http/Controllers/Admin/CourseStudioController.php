<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseUnit;
use App\Models\Quiz;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CourseStudioController extends Controller
{
    public function list()
    {
        $courses = Course::with('trainer')
            ->whereNotNull('trainer_id')
            ->withCount('enrollments')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.trainer.studio-list', compact('courses'));
    }

    public function index(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        // 1. Get the trainer's selected scheme from the course invitation
        $trainerId = $course->trainer_id;
        $courseInvitation = \App\Models\TrainerNotification::where('trainer_id', $trainerId)
            ->where('type', 'course_invitation')
            ->where(function ($query) use ($course) {
                $query->where('data', 'like', '%"entity_id":' . $course->id . '%')
                      ->orWhere('data', 'like', '%"course_id":' . $course->id . '%')
                      ->orWhere('data', 'like', '%"entity_id":"' . $course->id . '"%')
                      ->orWhere('data', 'like', '%"course_id":"' . $course->id . '"%');
            })
            ->latest()
            ->first();

        $invitationSchemeType = (int) data_get($courseInvitation?->data ?? [], 'scheme_type', 0);
        if ($invitationSchemeType === 0) {
            $contribScheme = (string) data_get($courseInvitation?->data ?? [], 'contribution_scheme', '');
            $invitationSchemeType = match ($contribScheme) {
                'e2e' => 1,
                'module_video' => 2,
                'video_only' => 3, 
                default => 1,
            };
        }

        $activeSchemeType = in_array($invitationSchemeType, [1, 2, 3], true) ? $invitationSchemeType : 1;

        // Trainer Permissions
        $trainerPermissions = [
            1 => ['can_module' => true, 'can_video' => true, 'can_quiz' => true],
            2 => ['can_module' => true, 'can_video' => true, 'can_quiz' => false],
            3 => ['can_module' => true, 'can_video' => false, 'can_quiz' => false],
        ][$activeSchemeType];

        // Admin Permissions are the INVERSE of trainer permissions (so they don't clash)
        $schemePermissions = [
            'can_module' => !$trainerPermissions['can_module'],
            'can_video' => !$trainerPermissions['can_video'],
            'can_quiz' => !$trainerPermissions['can_quiz'],
        ];

        $courseMaterialLocked = false;
        $courseInvitationStatus = 'accepted';

        $allowedTabs = ['module', 'video', 'quiz'];
        $requestedTab = (string) $request->query('tab', 'module');
        $activeTab = in_array($requestedTab, $allowedTabs, true) ? $requestedTab : 'module';

        // 1. Get all modules
        $unitIndex = (int) $request->query('unit', 0);
        $allModules = CourseModule::where('course_id', $id)
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

        // 2. Determine units
        $units = CourseUnit::where('course_id', $id)->orderBy('unit_no')->get();
        if ($units->isNotEmpty()) {
            $chunks = $allModules->chunk(3)->values();
        } else {
            $chunks = $allModules->chunk(3)->values();
        }

        // 3. Active unit modules
        $activeUnitModules = $chunks->get($unitIndex, collect());
        $activeUnitModuleIds = $activeUnitModules->pluck('id')->all();

        $uploadedMaterials = $allModules
            ->filter(function ($module) use ($activeUnitModuleIds) {
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
                    'view_url' => '#', // Admin doesn't need to view it identically, or maybe we link to admin preview
                    'updated_at' => optional($module->updated_at)->toDateTimeString(),
                    'review_status' => (string) ($module->review_status ?? 'pending_review'),
                    'processing_status' => (string) ($module->processing_status ?? ''),
                ];
            })
            ->values();

        if ($activeUnitModules->isEmpty()) {
            return redirect()->route('admin.courses.index')->with('error', 'Silabus untuk bab ini belum tersedia.');
        }

        $unitNo = $unitIndex + 1;
        $unit = CourseUnit::where('course_id', $id)->where('unit_no', $unitNo)->first();
        $unitTitle = $unit && !empty($unit->title) ? $unit->title : ("Bab " . $unitNo);

        $isAdmin = true;

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
            'isAdmin'
        ));
    }

    public function upload(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $targetIds = collect(explode(',', $request->target_modules))
            ->map(fn($value) => (int) trim($value))
            ->filter(fn($value) => $value > 0)
            ->unique()
            ->values();

        if ($targetIds->isEmpty()) {
            return response()->json(['success' => false, 'error' => 'Target modul tidak valid.']);
        }

        $contentHtml = trim((string) $request->input('module_content_html', ''));
        $updatedModules = [];

        if ($contentHtml !== '') {
            $targetTextModule = CourseModule::where('course_id', $id)
                ->whereIn('id', $targetIds)
                ->orderBy('order_no', 'asc')
                ->first();

            if ($targetTextModule) {
                $targetTextModule->update([
                    'description' => $contentHtml,
                    'review_status' => 'approved', // Admin changes auto-approve
                ]);
                $updatedModules[] = [
                    'module_id' => $targetTextModule->id,
                    'title' => $targetTextModule->title,
                    'type' => $targetTextModule->type,
                    'status' => 'approved',
                ];
            }
        }

        if (!$request->hasFile('files')) {
            if ($contentHtml !== '') {
                return response()->json([
                    'success' => true,
                    'message' => 'Materi teks berhasil disubmit oleh Admin.',
                    'updated_modules' => $updatedModules,
                    'rejected_files' => [],
                ]);
            }
            return response()->json(['success' => false, 'error' => 'Tidak ada file.']);
        }

        foreach ($request->file('files') as $file) {
            $ext = strtolower($file->getClientOriginalExtension());
            $type = in_array($ext, ['mp4']) ? 'video' : 'pdf';

            // Find an empty module for this file
            $emptyModule = CourseModule::where('course_id', $id)
                ->whereIn('id', $targetIds)
                ->where('type', $type)
                ->whereNull('content_url')
                ->orderBy('order_no', 'asc')
                ->first();

            if (!$emptyModule) {
                // Try fallback to any slot
                $emptyModule = CourseModule::where('course_id', $id)
                    ->whereIn('id', $targetIds)
                    ->whereNull('content_url')
                    ->orderBy('order_no', 'asc')
                    ->first();
            }

            if ($emptyModule) {
                $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                $filepath = $file->storeAs('courses/' . $id . '/materials', $filename, 'public');

                $emptyModule->update([
                    'content_url' => $filepath,
                    'file_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'type' => $type, // Update type if it fell back
                    'review_status' => 'approved', // Admin files are pre-approved
                ]);

                $updatedModules[] = [
                    'module_id' => $emptyModule->id,
                    'title' => $emptyModule->title,
                    'type' => $emptyModule->type,
                    'status' => 'approved',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'File berhasil diupload oleh Admin.',
            'updated_modules' => $updatedModules,
            'rejected_files' => [],
        ]);
    }

    public function quiz(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        
        $request->validate([
            'module_id' => 'required|integer',
            'questions' => 'required|string',
            'passingGrade' => 'required|integer|min:0|max:100',
        ]);

        $module = CourseModule::where('course_id', $id)->findOrFail($request->module_id);
        
        $questionsData = json_decode($request->questions, true);

        if (empty($questionsData)) {
            return back()->with('error', 'Data soal tidak valid.');
        }

        $module->update([
            'quiz_passing_grade' => $request->passingGrade,
            'review_status' => 'approved',
        ]);

        $module->quizQuestions()->delete();

        foreach ($questionsData as $q) {
            $module->quizQuestions()->create([
                'question_text' => $q['text'],
                'options' => json_encode($q['options']),
                'correct_answer_index' => $q['correctAnswer'],
                'weight' => $q['weight'] ?? 10,
            ]);
        }

        return redirect()->back()->with('success', 'Kuis berhasil disimpan oleh Admin!');
    }

    public function editorImage(Request $request, $id)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
            $path = $file->storeAs("courses/{$id}/editor_images", $filename, 'public');
            return response()->json(['location' => Storage::disk('public')->url($path)]);
        }
        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function viewMaterial($courseId, $materialId)
    {
        $course = Course::findOrFail($courseId);
        $material = CourseMaterial::where('course_id', $courseId)->findOrFail($materialId);

        return response()->json([
            'success' => true,
            'title' => $material->title,
            'content' => $material->content,
            'type' => $material->type
        ]);
    }
}
