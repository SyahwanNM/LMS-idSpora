<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModuleProcessingController extends Controller
{
    public function assignCourse(Request $request, Course $material, CourseModule $module)
    {
        $this->ensureModuleBelongsToCourse($material, $module);

        $validated = $request->validate([
            'assigned_to_admin_course_id' => 'nullable|integer|exists:users,id',
            'assignment_notes' => 'nullable|string|max:1000',
        ]);

        if ($module->type !== 'video') {
            return back()->with('error', 'Handoff hanya berlaku untuk modul video.');
        }

        if (empty($module->content_url) || $module->content_url === 'quiz_submitted') {
            return back()->with('error', 'Video sumber belum tersedia untuk diserahkan.');
        }

        if (($module->review_status ?? '') !== 'approved') {
            return back()->with('error', 'Modul harus disetujui admin trainer terlebih dahulu.');
        }

        if (in_array((string) ($module->processing_status ?? ''), ['processed_uploaded', 'ready_for_publish'], true)) {
            return back()->with('error', 'Modul ini sudah berada di tahap hasil edit, tidak bisa diserahkan ulang.');
        }

        $targetAdminId = $this->resolveAdminCourseTarget((int) ($validated['assigned_to_admin_course_id'] ?? 0));

        $module->update([
            'processing_status' => 'assigned_to_admin_course',
            'assigned_by_admin_trainer_id' => (int) Auth::id(),
            'assigned_to_admin_course_id' => $targetAdminId,
            'assigned_at' => now(),
            'assignment_notes' => $validated['assignment_notes'] ?? null,
            'processed_file_url' => null,
            'processed_file_name' => null,
            'processed_mime' => null,
            'processed_file_size' => null,
            'processed_at' => null,
        ]);

        return back()->with('success', 'Modul video berhasil diserahkan ke admin course untuk proses lanjutan.');
    }

    public function uploadProcessed(Request $request, Course $material, CourseModule $module)
    {
        $this->ensureModuleBelongsToCourse($material, $module);

        $validated = $request->validate([
            'processed_file' => 'required|file|mimes:mp4,mov,mkv,webm|max:1024000',
            'assignment_notes' => 'nullable|string|max:1000',
        ]);

        if ($module->type !== 'video') {
            return back()->with('error', 'Upload hasil edit hanya berlaku untuk modul video.');
        }

        $currentProcessingStatus = (string) ($module->processing_status ?? '');
        if (!in_array($currentProcessingStatus, ['assigned_to_admin_course', 'revision_requested'], true)) {
            return back()->with('error', 'Modul harus berada pada tahap handoff sebelum upload hasil edit.');
        }

        if (empty($module->assigned_to_admin_course_id)) {
            return back()->with('error', 'Admin course tujuan belum ditentukan.');
        }

        $path = $validated['processed_file']->store('courses/' . $material->id . '/processed', 'public');

        $module->update([
            'processing_status' => 'processed_uploaded',
            'processed_file_url' => $path,
            'processed_file_name' => $validated['processed_file']->getClientOriginalName(),
            'processed_mime' => (string) $validated['processed_file']->getClientMimeType(),
            'processed_file_size' => (int) $validated['processed_file']->getSize(),
            'processed_at' => now(),
            'assignment_notes' => $validated['assignment_notes'] ?? $module->assignment_notes,
            'processing_version' => (int) ($module->processing_version ?? 0) + 1,
        ]);

        return back()->with('success', 'Video hasil edit berhasil diunggah ke modul ini.');
    }

    public function acceptProcessed(Course $material, CourseModule $module)
    {
        $this->ensureModuleBelongsToCourse($material, $module);

        if (empty($module->processed_file_url)) {
            return back()->with('error', 'Belum ada hasil edit untuk disetujui.');
        }

        if (($module->processing_status ?? '') !== 'processed_uploaded') {
            return back()->with('error', 'Hasil edit belum berstatus siap direview.');
        }

        $module->update([
            'processing_status' => 'ready_for_publish',
            'review_status' => 'approved',
            'reviewed_at' => now(),
            'reviewed_by' => Auth::id(),
            'review_rejection_reason' => null,
        ]);

        return back()->with('success', 'Hasil edit disetujui dan modul siap dipublikasikan.');
    }

    public function requestRevision(Request $request, Course $material, CourseModule $module)
    {
        $this->ensureModuleBelongsToCourse($material, $module);

        $validated = $request->validate([
            'assignment_notes' => 'required|string|min:10|max:1000',
        ]);

        $currentProcessingStatus = (string) ($module->processing_status ?? '');
        if (!in_array($currentProcessingStatus, ['processed_uploaded', 'ready_for_publish'], true)) {
            return back()->with('error', 'Revisi hanya bisa diminta setelah hasil edit diunggah.');
        }

        $module->update([
            'processing_status' => 'revision_requested',
            'assignment_notes' => $validated['assignment_notes'],
            'reviewed_at' => null,
            'reviewed_by' => null,
        ]);

        return back()->with('success', 'Revisi hasil edit berhasil diminta ke admin trainer/tim produksi.');
    }

    private function ensureModuleBelongsToCourse(Course $material, CourseModule $module): void
    {
        if ((int) $module->course_id !== (int) $material->id) {
            abort(404);
        }
    }

    private function resolveAdminCourseTarget(int $requestedAdminId): ?int
    {
        if ($requestedAdminId > 0) {
            $target = User::query()
                ->where('id', $requestedAdminId)
                ->whereRaw('LOWER(role) = ?', ['admin'])
                ->value('id');

            if (!empty($target)) {
                return (int) $target;
            }
        }

        $firstAdmin = User::query()
            ->whereRaw('LOWER(role) = ?', ['admin'])
            ->orderBy('id')
            ->value('id');

        return $firstAdmin ? (int) $firstAdmin : null;
    }
}
