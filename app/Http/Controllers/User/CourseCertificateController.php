<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\CertificateController;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CourseCertificateController extends Controller
{
    public function show(Request $request, Course $course)
    {
        $user = Auth::user();

        $enrollment = Enrollment::query()
            ->with(['user', 'course'])
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return redirect()->route('course.detail', $course->id)
                ->with('error', 'Anda belum terdaftar di course ini.');
        }

        $progressPercent = $enrollment->getProgressPercentage();
        $certificateReady =
            $enrollment->status === 'completed'
            || !is_null($enrollment->completed_at)
            || $progressPercent >= 100;

        if ($certificateReady) {
            if ($enrollment->status !== 'completed') {
                $enrollment->status = 'completed';
            }
            if (!$enrollment->completed_at) {
                $enrollment->completed_at = now();
            }
            if (!$enrollment->certificate_issued_at) {
                $enrollment->certificate_issued_at = now();
            }
            if (empty($enrollment->certificate_number)) {
                $enrollment->certificate_number = CertificateController::generateCertificateNumberCourse($course, $enrollment);
            }
            $enrollment->save();
        }

        $logosBase64 = [];
        $logoSources = is_array($course->certificate_logo)
            ? $course->certificate_logo
            : ($course->certificate_logo ? [$course->certificate_logo] : []);

        foreach ($logoSources as $logoPath) {
            $path = str_replace('storage/', '', (string) $logoPath);
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                $mime = Storage::disk('public')->mimeType($path);
                $content = base64_encode(Storage::disk('public')->get($path));
                $logosBase64[] = "data:$mime;base64,$content";
            }
        }

        $signaturesBase64 = [];
        $sigSources = is_array($course->certificate_signature)
            ? $course->certificate_signature
            : ($course->certificate_signature ? [$course->certificate_signature] : []);

        foreach ($sigSources as $sigPath) {
            $path = str_replace('storage/', '', (string) $sigPath);
            if ($path !== '' && Storage::disk('public')->exists($path)) {
                $mime = Storage::disk('public')->mimeType($path);
                $content = base64_encode(Storage::disk('public')->get($path));
                $signaturesBase64[] = "data:$mime;base64,$content";
            }
        }

        return view('course.sertifikat-course', [
            'course' => $course,
            'user' => $user,
            'enrollment' => $enrollment,
            'issuedAt' => $enrollment->certificate_issued_at ?? now(),
            'certificateNumber' => $enrollment->certificate_number,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
            'certificateReady' => $certificateReady,
            'progressPercent' => (int) $progressPercent,
        ]);
    }
}
