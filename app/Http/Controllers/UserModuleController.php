<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\ManualPayment;

use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserModuleController extends Controller
{
    private function normalizePublicPath(?string $path): string
    {
        $normalized = ltrim((string) $path, '/');
        if (str_starts_with($normalized, 'uploads/')) {
            $normalized = substr($normalized, strlen('uploads/'));
        }

        return $normalized;
    }

    private function assertCanAccessModule(Course $course, CourseModule $module): void
    {
        if ((int) $module->course_id !== (int) $course->id) {
            abort(404);
        }

        $user = Auth::user();
        if (!$user) {
            abort(401);
        }

        $enrollment = Enrollment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        $enrolledActive = $enrollment && $enrollment->status === 'active';

        $hasSettledPayment = ManualPayment::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'settled')
            ->exists();

        if (!$enrolledActive && !$hasSettledPayment) {
            abort(403, 'Silakan lakukan pembelian course terlebih dahulu.');
        }

        $modules = $course->modules()->orderBy('order_no')->get();

        $isFreeCourse = (int) ($course->price ?? 0) <= 0;
        $freeAccessMode = $isFreeCourse ? (string) ($course->free_access_mode ?? 'limit_2') : 'all';

        if ($isFreeCourse && $freeAccessMode === 'limit_2') {
            $allowedIds = $modules
                ->take(2)
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->values()
                ->all();

            if (!in_array((int) $module->id, $allowedIds, true)) {
                abort(403, 'Course gratis ini hanya membuka 2 modul pertama.');
            }
        }

        $passingPercent = 75;
        $prevModule = $modules
            ->filter(fn($m) => (int) ($m->order_no ?? 0) < (int) ($module->order_no ?? 0))
            ->sortByDesc('order_no')
            ->first();

        if ($prevModule && strtolower(trim((string) ($prevModule->type ?? ''))) === 'quiz') {
            $lastAttempt = QuizAttempt::query()
                ->where('user_id', $user->id)
                ->where('course_module_id', $prevModule->id)
                ->whereNotNull('completed_at')
                ->orderByDesc('completed_at')
                ->first();

            $passedPrevQuiz = $lastAttempt ? $lastAttempt->isPassed($passingPercent) : false;
            if (!$passedPrevQuiz) {
                abort(403, 'Kamu harus lulus kuis terlebih dahulu untuk membuka materi selanjutnya.');
            }
        }
    }

    private function guessMimeType(CourseModule $module): string
    {
        $mime = (string) ($module->mime_type ?? '');
        if ($mime !== '') {
            return $mime;
        }

        try {
            $detected = Storage::disk('public')->mimeType($this->normalizePublicPath($module->content_url));
            if (is_string($detected) && $detected !== '') {
                return $detected;
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $ext = strtolower(pathinfo((string) $module->content_url, PATHINFO_EXTENSION));
        return match ($ext) {
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg', 'ogv' => 'video/ogg',
            'pdf' => 'application/pdf',
            default => ($module->isVideo() ? 'video/mp4' : 'application/octet-stream'),
        };
    }

    public function index(Course $course)
    {
        // Check if user is enrolled in this course
        // For now, we'll allow all authenticated users to view modules
        // You can add enrollment check later
        
        $modules = $course->modules;
        return view('user.modules.index', compact('course', 'modules'));
    }

    public function show(Course $course, CourseModule $module)
    {
        // Check if user is enrolled in this course
        // For now, we'll allow all authenticated users to view modules
        
        $nextModule = $course->modules()
            ->where('order_no', '>', $module->order_no)
            ->orderBy('order_no')
            ->first();
            
        $prevModule = $course->modules()
            ->where('order_no', '<', $module->order_no)
            ->orderBy('order_no', 'desc')
            ->first();

        return view('user.modules.show', compact('course', 'module', 'nextModule', 'prevModule'));
    }

    public function download(Course $course, CourseModule $module)
    {
        $this->assertCanAccessModule($course, $module);

        $contentPath = $this->normalizePublicPath($module->content_url);
        if (!Storage::disk('public')->exists($contentPath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($contentPath);
    }

    public function stream(Course $course, CourseModule $module)
    {
        $this->assertCanAccessModule($course, $module);

        $contentPath = $this->normalizePublicPath($module->content_url);
        if (!Storage::disk('public')->exists($contentPath)) {
            abort(404, 'File tidak ditemukan');
        }

        $filePath = Storage::disk('public')->path($contentPath);
        $fileSize = filesize($filePath);
        $fileName = basename($contentPath);

        $mime = $this->guessMimeType($module);
        $range = request()->header('Range');
        $headers = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
            'Accept-Ranges' => 'bytes',
        ];

        if (is_string($range) && preg_match('/bytes=(\d*)-(\d*)/i', $range, $matches)) {
            $start = ($matches[1] !== '') ? (int) $matches[1] : 0;
            $end = ($matches[2] !== '') ? (int) $matches[2] : ($fileSize - 1);
            $end = min($end, $fileSize - 1);
            if ($start > $end) {
                $start = 0;
            }

            $length = $end - $start + 1;
            $headers['Content-Length'] = $length;
            $headers['Content-Range'] = 'bytes ' . $start . '-' . $end . '/' . $fileSize;

            return response()->stream(function () use ($filePath, $start, $end) {
                $chunkSize = 8192;
                $handle = fopen($filePath, 'rb');
                if ($handle === false) {
                    return;
                }
                fseek($handle, $start);
                $remaining = $end - $start + 1;

                while ($remaining > 0 && !feof($handle)) {
                    $read = ($remaining > $chunkSize) ? $chunkSize : $remaining;
                    $buffer = fread($handle, $read);
                    if ($buffer === '' || $buffer === false) {
                        break;
                    }
                    echo $buffer;
                    flush();
                    $remaining -= strlen($buffer);
                }

                fclose($handle);
            }, 206, $headers);
        }

        $headers['Content-Length'] = $fileSize;
        return response()->file($filePath, $headers);
    }
}