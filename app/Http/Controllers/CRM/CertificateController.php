<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Illuminate\Support\Str;
use ZipArchive;
use Carbon\Carbon;

class CertificateController extends Controller
{
    /**
     * Display list of events for certificate management (Admin only).
     */
    public function index(Request $request)
    {
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        $tab = $request->get('tab', 'events');

        $events = Event::withCount('registrations')
            ->orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $courses = Course::withCount([
                'enrollments', 
                'enrollments as completed_enrollments_count' => function($query) {
                    $query->where('status', 'completed');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        // \Illuminate\Support\Facades\Log::info('CertificateController@index - Events count: ' . $events->count());
        // \Illuminate\Support\Facades\Log::info('CertificateController@index - Courses count: ' . $courses->count());

        return view('admin.certificates.index', compact('events', 'courses', 'tab'));
    }

    /**
     * Show form to edit certificate settings (logo & signature) for an event (Admin only).
     */
    public function edit(Event $event)
    {
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        return view('admin.certificates.edit', compact('event'));
    }

    /**
     * Update certificate settings (logo & signature) for an event (Admin only).
     */
    public function update(Request $request, Event $event)
    {
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        $request->validate([
            'certificate_template'        => 'required|string|in:template_1,template_2,template_3,template_4',
            'certificate_logo'            => 'nullable|array',
            'certificate_logo.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'delete_logos'                => 'nullable|array',
            'delete_logos.*'              => 'nullable|string',
            'delete_signatures'           => 'nullable|array',
            'delete_signatures.*'         => 'nullable|string',
            'certificate_signature_file'  => 'nullable|array',
            'certificate_signature_file.*'=> 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'existing_signature_image'    => 'nullable|array',
            'signature_name'              => 'nullable|array',
            'signature_position'          => 'nullable|array',
        ]);

        $data = ['certificate_template' => $request->certificate_template];

        // Handle Logos (unchanged)
        $existingLogos = is_array($event->certificate_logo) ? $event->certificate_logo : ($event->certificate_logo ? [$event->certificate_logo] : []);
        if($request->has('delete_logos')) {
            foreach($request->delete_logos as $logo) {
                if(!empty($logo)) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $logo));
                    $existingLogos = array_values(array_filter($existingLogos, fn($l) => $l !== $logo));
                }
            }
        }
        if ($request->hasFile('certificate_logo')) {
            foreach($request->file('certificate_logo') as $file) {
                $existingLogos[] = $file->store('certificates', 'public');
            }
        }
        $data['certificate_logo'] = array_values(array_unique($existingLogos));

        // Handle Signatures (new format: array of {image, name, position})
        $data['certificate_signature'] = $this->processSignatures($request, $event->certificate_signature);

        $event->update($data);
        return redirect()->route('admin.crm.certificates.index', ['tab' => 'events'])->with('success', 'Konfigurasi sertifikat event berhasil diperbarui!');
    }

    /**
     * Show form to edit certificate settings for a course (Admin only).
     */
    public function editCourse(Course $course)
    {
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        return view('admin.certificates.edit_course', compact('course'));
    }

    /**
     * Update certificate settings for a course (Admin only).
     */
    public function updateCourse(Request $request, Course $course)
    {
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        $request->validate([
            'certificate_template'        => 'required|string|in:template_1,template_2,template_3,template_4',
            'certificate_logo'            => 'nullable|array',
            'certificate_logo.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'delete_logos'                => 'nullable|array',
            'delete_logos.*'              => 'nullable|string',
            'delete_signatures'           => 'nullable|array',
            'delete_signatures.*'         => 'nullable|string',
            'certificate_signature_file'  => 'nullable|array',
            'certificate_signature_file.*'=> 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'existing_signature_image'    => 'nullable|array',
            'signature_name'              => 'nullable|array',
            'signature_position'          => 'nullable|array',
        ]);

        $data = ['certificate_template' => $request->certificate_template];

        // Handle Logos (unchanged)
        $existingLogos = is_array($course->certificate_logo) ? $course->certificate_logo : ($course->certificate_logo ? [$course->certificate_logo] : []);
        if($request->has('delete_logos')) {
            foreach($request->delete_logos as $logo) {
                if(!empty($logo)) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $logo));
                    $existingLogos = array_values(array_filter($existingLogos, fn($l) => $l !== $logo));
                }
            }
        }
        if ($request->hasFile('certificate_logo')) {
            foreach($request->file('certificate_logo') as $file) {
                $existingLogos[] = $file->store('certificates', 'public');
            }
        }
        $data['certificate_logo'] = array_values(array_unique($existingLogos));

        // Handle Signatures (new format: array of {image, name, position})
        $data['certificate_signature'] = $this->processSignatures($request, $course->certificate_signature);

        $course->update($data);
        return redirect()->route('admin.crm.certificates.index', ['tab' => 'courses'])->with('success', 'Konfigurasi sertifikat kursus berhasil diperbarui!');
    }

    /**
     * Proses data tanda tangan dari form baru.
     * Mengembalikan array of {image, name, position}.
     */
    private function processSignatures(Request $request, $existingRaw): array
    {
        // Normalisasi data lama
        $existingRaw = is_array($existingRaw) ? $existingRaw : ($existingRaw ? [$existingRaw] : []);

        // Konversi data lama ke format baru (string path → {image, name, position})
        $oldSigs = array_map(function ($s) {
            if (is_array($s)) return $s;
            return ['image' => $s, 'name' => '', 'position' => ''];
        }, $existingRaw);

        // Path gambar yang harus dihapus
        $toDelete = array_filter((array) $request->input('delete_signatures', []));
        foreach ($toDelete as $delPath) {
            Storage::disk('public')->delete(str_replace('storage/', '', $delPath));
            $oldSigs = array_values(array_filter($oldSigs, fn($s) => ($s['image'] ?? '') !== $delPath));
        }

        // Re-index existing sigs by their image path for lookup
        $oldSigsByPath = [];
        foreach ($oldSigs as $s) {
            $oldSigsByPath[$s['image']] = $s;
        }

        $newSigs = [];
        $existingImages = $request->input('existing_signature_image', []);
        $sigNames       = $request->input('signature_name', []);
        $sigPositions   = $request->input('signature_position', []);
        $sigFiles       = $request->file('certificate_signature_file', []);

        // Semua index yang ada di form (dari existing + new)
        $allIndexes = array_unique(array_merge(
            array_keys($existingImages),
            array_keys($sigNames),
            array_keys($sigPositions),
            array_keys($sigFiles ?? [])
        ));
        sort($allIndexes);

        foreach ($allIndexes as $idx) {
            $existingPath = $existingImages[$idx] ?? null;
            $newFile      = $sigFiles[$idx] ?? null;
            $name         = trim($sigNames[$idx] ?? '');
            $position     = trim($sigPositions[$idx] ?? '');

            // Skip if this signature was marked for deletion
            if ($existingPath && in_array($existingPath, $toDelete)) {
                continue;
            }

            if ($newFile && $newFile->isValid()) {
                // Hapus file lama jika ada
                if ($existingPath) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $existingPath));
                }
                $imagePath = $newFile->store('certificates', 'public');
            } elseif ($existingPath) {
                $imagePath = $existingPath;
            } else {
                // Tidak ada gambar — lewati
                continue;
            }

            $newSigs[] = [
                'image'    => $imagePath,
                'name'     => $name,
                'position' => $position,
            ];
        }

        return array_values(array_slice($newSigs, 0, 3));
    }

    public function show(Event $event, $registration)
    {
        if(!($registration instanceof EventRegistration)) {
            $registration = EventRegistration::findOrFail($registration);
        }
        if($registration->event_id !== $event->id) abort(404);
        
        $this->authorizeAccess($event, $registration);
        $certificateReady = $this->isCertificateReady($event, $registration);
        
        if($certificateReady && !$registration->certificate_number){
            $registration->update([
                'certificate_number' => self::generateCertificateNumber($event, $registration),
                'certificate_issued_at' => now(),
            ]);
        }
        
        $data = $this->getCertificateData($event, $registration->fresh());
        $data['certificateReady'] = $certificateReady;
        $data['registration'] = $registration;

        return view('events.certificate', $data);
    }

    public function download(Request $request, Event $event, $registration)
    {
        if(!($registration instanceof EventRegistration)) {
            $registration = EventRegistration::with('user', 'event')->findOrFail($registration);
        }
        if($registration->event_id !== $event->id) abort(404);
        
        $this->authorizeAccess($event, $registration);
        $certificateReady = $this->isCertificateReady($event, $registration);
        
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $force = $request->boolean('force') && $isAdmin;
        
        if(!$certificateReady && !$force) {
            return redirect()->back()->with('info','Sertifikat belum tersedia.');
        }

        if (empty($registration->user)) {
            return redirect()->back()->with('error', 'Gagal mengunduh: Akun user terkait pendaftaran ini tidak ditemukan di database.');
        }

        if(!$registration->certificate_number) {
            $registration->update([
                'certificate_number' => self::generateCertificateNumber($event, $registration),
                'certificate_issued_at' => now(),
            ]);
        }
        
        $data = $this->getCertificateData($event, $registration->fresh());
        
        // Gunakan template custom jika tersedia, otherwise gunakan template bawaan
        $viewName = !empty($event->certificate_custom_template)
            ? 'events.certificate-custom'
            : 'events.certificate-pdf-only';

        $dompdf = $this->makeDompdf();
        $html = trim(view($viewName, $data)->render());
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        
        $filename = 'Sertifikat_'.Str::slug($event->title).'_'.Str::slug($registration->user->name).'.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => ($request->boolean('inline') ? 'inline' : 'attachment').'; filename="'.$filename.'"',
        ]);
    }

    public function generateMassal(Request $request, Event $event)
    {
        if(!Auth::check() || Auth::user()->role !== 'admin') abort(403);
        $registrations = $event->registrations()->with('user')->get();
        if($registrations->isEmpty()) return redirect()->back()->with('error', 'Tidak ada peserta.');

        $tempDir = storage_path('app/temp/certs_'.time());
        if(!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        foreach($registrations as $registration) {
            if (empty($registration->user)) {
                continue; // Lewati registrasi jika data akun user relasi kosong/dihapus
            }
            if(!$registration->certificate_number) {
                $registration->update([
                    'certificate_number' => $this->generateCertificateNumber($event, $registration),
                    'certificate_issued_at' => now(),
                ]);
            }
            $data = $this->getCertificateData($event, $registration->fresh());
            
            $viewName = !empty($event->certificate_custom_template)
                ? 'events.certificate-custom'
                : 'events.certificate-pdf-only';
            $html = trim(view($viewName, $data)->render());

            // Buat instance Dompdf BARU untuk setiap sertifikat agar state tidak bocor
            $dompdf = $this->makeDompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->render();

            $name = Str::slug($registration->user->name).'_'.$registration->id.'.pdf';
            file_put_contents($tempDir.'/'.$name, $dompdf->output());
        }

        $zipName = 'Sertifikat_'.Str::slug($event->title).'.zip';
        $zipPath = storage_path('app/temp/'.$zipName);
        $zip = new ZipArchive();
        if($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach(glob($tempDir.'/*.pdf') as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        array_map('unlink', glob($tempDir.'/*'));
        rmdir($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function downloadCourse(Request $request, Course $course, $enrollment)
    {
        if(!($enrollment instanceof Enrollment)) {
            $enrollment = Enrollment::with('user', 'course')->findOrFail($enrollment);
        }
        if($enrollment->course_id !== $course->id) abort(404);
        
        $this->authorizeAccessCourse($course, $enrollment);
        
        $certificateReady = $this->isCertificateReadyCourse($course, $enrollment);
        
        $isAdmin = Auth::check() && Auth::user()->role === 'admin';
        $force = $request->boolean('force') && $isAdmin;
        
        if(!$certificateReady && !$force) {
            return redirect()->back()->with('error','Kursus belum selesai.');
        }

        if (empty($enrollment->user)) {
            return redirect()->back()->with('error', 'Gagal mengunduh: Akun user terkait pendaftaran ini tidak ditemukan di database.');
        }

        if($certificateReady && $enrollment->status !== 'completed'){
            $enrollment->update(['status' => 'completed']);
        }


        if(!$enrollment->certificate_number) {
            $enrollment->update([
                'certificate_number' => self::generateCertificateNumberCourse($course, $enrollment),
                'certificate_issued_at' => now(),
            ]);
        }
        
        $data = $this->getCertificateDataCourse($course, $enrollment->fresh());
        
        $viewName = !empty($course->certificate_custom_template)
            ? 'events.certificate-custom'
            : 'courses.certificate-pdf-only';

        $dompdf = $this->makeDompdf();
        $html = trim(view($viewName, $data)->render());
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->render();
        
        $filename = 'Sertifikat_Course_'.Str::slug($course->name).'_'.Str::slug($enrollment->user->name).'.pdf';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => (request()->boolean('inline') ? 'inline' : 'attachment').'; filename="'.$filename.'"',
        ]);
    }

    public function previewCourse(Course $course, $enrollment)
    {
        if(!($enrollment instanceof Enrollment)) {
            $enrollment = Enrollment::with('user', 'course')->findOrFail($enrollment);
        }
        if($enrollment->course_id !== $course->id) abort(404);
        
        $this->authorizeAccessCourse($course, $enrollment);
        
        $certificateReady = $this->isCertificateReadyCourse($course, $enrollment);
        if(!$certificateReady) {
            // Jika belum siap, arahkan ke halaman belajar
            return redirect()->route('course.learn', $course->id)->with('error','Selesaikan semua modul untuk melihat sertifikat.');
        }

        if($enrollment->status !== 'completed'){
            $enrollment->update(['status' => 'completed']);
        }

        if(!$enrollment->certificate_number) {
            $enrollment->update([
                'certificate_number' => self::generateCertificateNumberCourse($course, $enrollment),
                'certificate_issued_at' => now(),
            ]);
        }
        
        $data = $this->getCertificateDataCourse($course, $enrollment->fresh());
        $data['is_preview'] = true;
        
        return view('courses.certificate-pdf', $data);
    }


    public function generateMassalCourse(Request $request, Course $course)
    {
        if(!Auth::check() || Auth::user()->role !== 'admin') abort(403);
        $enrollments = $course->enrollments()->where('status', 'completed')->with('user')->get();
        if($enrollments->isEmpty()) return redirect()->back()->with('error', 'Tidak ada peserta yang menyelesaikan kursus.');

        $tempDir = storage_path('app/temp/course_certs_'.time());
        if(!is_dir($tempDir)) mkdir($tempDir, 0755, true);

        foreach($enrollments as $enrollment) {
            if (empty($enrollment->user)) {
                continue; // Lewati enrollment jika data akun user relasi kosong/dihapus
            }
            if(!$enrollment->certificate_number) {
                $enrollment->update([
                    'certificate_number' => self::generateCertificateNumberCourse($course, $enrollment),
                    'certificate_issued_at' => now(),
                ]);
            }
            $data = $this->getCertificateDataCourse($course, $enrollment->fresh());
            
            $viewName = !empty($course->certificate_custom_template)
                ? 'events.certificate-custom'
                : 'courses.certificate-pdf-only';
            $html = trim(view($viewName, $data)->render());

            // Buat instance Dompdf BARU untuk setiap sertifikat agar state tidak bocor
            $dompdf = $this->makeDompdf();
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->render();

            $name = Str::slug($enrollment->user->name).'_'.$enrollment->id.'.pdf';
            file_put_contents($tempDir.'/'.$name, $dompdf->output());
        }

        $zipName = 'Sertifikat_Course_'.Str::slug($course->name).'.zip';
        $zipPath = storage_path('app/temp/'.$zipName);
        $zip = new ZipArchive();
        if($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            foreach(glob($tempDir.'/*.pdf') as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        array_map('unlink', glob($tempDir.'/*'));
        rmdir($tempDir);

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Buat instance Dompdf baru dengan konfigurasi standar.
     * Selalu gunakan instance baru untuk setiap dokumen agar state render tidak bocor.
     */
    private function makeDompdf(): Dompdf
    {
        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $dompdf->setOptions($options);
        return $dompdf;
    }

    private function getCertificateData(Event $event, EventRegistration $registration)
    {
        $logosBase64 = [];
        $logosUrl = [];
        foreach(is_array($event->certificate_logo) ? $event->certificate_logo : [] as $l) {
            $path = str_replace('storage/', '', $l);
            if(Storage::disk('public')->exists($path)) {
                $mime = Storage::disk('public')->mimeType($path);
                $content = base64_encode(Storage::disk('public')->get($path));
                $logosBase64[] = "data:$mime;base64,$content";
                $logosUrl[] = request()->schemeAndHttpHost() . '/uploads/' . $path;
            }
        }

        $sigsRaw = is_array($event->certificate_signature) ? $event->certificate_signature : [];
        $signaturesData = [];
        $signaturesBase64 = []; // backward compat
        foreach ($sigsRaw as $s) {
            $isObj    = is_array($s);
            $imgPath  = $isObj ? ($s['image'] ?? '') : $s;
            $sigName  = $isObj ? ($s['name'] ?? '') : '';
            $sigPos   = $isObj ? ($s['position'] ?? '') : '';

            $path = str_replace('storage/', '', $imgPath);
            if ($path && Storage::disk('public')->exists($path)) {
                $mime    = Storage::disk('public')->mimeType($path);
                $b64     = "data:$mime;base64," . base64_encode(Storage::disk('public')->get($path));
                $url     = request()->schemeAndHttpHost() . '/uploads/' . $path;
                $signaturesBase64[] = $b64;
                $signaturesData[]   = [
                    'base64' => $b64, 
                    'url'    => $url,
                    'name'   => $sigName, 
                    'position' => $sigPos
                ];
            }
        }

        return [
            'event'            => $event,
            'user'             => $registration->user,
            'issuedAt'         => $registration->certificate_issued_at ?? now(),
            'certificateNumber'=> $registration->certificate_number,
            'logosBase64'      => $logosBase64,
            'logosUrl'         => $logosUrl,
            'signaturesBase64' => $signaturesBase64,
            'signaturesData'   => $signaturesData,
        ];
    }

    private function getCertificateDataCourse(Course $course, Enrollment $enrollment)
    {
        $logosBase64 = [];
        $logosUrl = [];
        foreach(is_array($course->certificate_logo) ? $course->certificate_logo : [] as $l) {
            $path = str_replace('storage/', '', $l);
            if(Storage::disk('public')->exists($path)) {
                $mime = Storage::disk('public')->mimeType($path);
                $content = base64_encode(Storage::disk('public')->get($path));
                $logosBase64[] = "data:$mime;base64,$content";
                $logosUrl[] = request()->schemeAndHttpHost() . '/uploads/' . $path;
            }
        }

        $sigsRaw = is_array($course->certificate_signature) ? $course->certificate_signature : [];
        $signaturesData = [];
        $signaturesBase64 = []; // backward compat
        foreach ($sigsRaw as $s) {
            $isObj    = is_array($s);
            $imgPath  = $isObj ? ($s['image'] ?? '') : $s;
            $sigName  = $isObj ? ($s['name'] ?? '') : '';
            $sigPos   = $isObj ? ($s['position'] ?? '') : '';

            $path = str_replace('storage/', '', $imgPath);
            if ($path && Storage::disk('public')->exists($path)) {
                $mime    = Storage::disk('public')->mimeType($path);
                $b64     = "data:$mime;base64," . base64_encode(Storage::disk('public')->get($path));
                $url     = request()->schemeAndHttpHost() . '/uploads/' . $path;
                $signaturesBase64[] = $b64;
                $signaturesData[]   = [
                    'base64' => $b64, 
                    'url'    => $url,
                    'name'   => $sigName, 
                    'position' => $sigPos
                ];
            }
        }

        return [
            'course'           => $course,
            'user'             => $enrollment->user,
            'issuedAt'         => $enrollment->certificate_issued_at ?? now(),
            'certificateNumber'=> $enrollment->certificate_number,
            'logosBase64'      => $logosBase64,
            'logosUrl'         => $logosUrl,
            'signaturesBase64' => $signaturesBase64,
            'signaturesData'   => $signaturesData,
        ];
    }

    public function isCertificateReadyCourse(Course $course, Enrollment $enrollment) {
        if ($enrollment->status === 'completed' || $enrollment->certificate_issued_at) return true;
        
        // Cek jika progress sudah 100%
        return $enrollment->isFullyCompleted();
    }

    public function isCertificateReady(Event $event, EventRegistration $registration = null) {
        if ($registration && $registration->certificate_issued_at) return true;
        
        // Jika user sudah absen hadir, sertifikat boleh diakses tanpa menunggu event selesai
        if ($registration) {
            $status = strtolower((string) ($registration->attendance_status ?? ''));
            $isAttended = in_array($status, ['present', 'attended', 'checked-in', 'yes'], true) 
                || !empty($registration->attended_at);
            
            if ($isAttended) return true;
        }

        return $event->isFinished();
    }

    private function authorizeAccess($event, $registration) {
        if(Auth::user()->role === 'admin') return;
        if(Auth::id() !== $registration->user_id) abort(403);
    }

    private function authorizeAccessCourse($course, $enrollment) {
        if(Auth::user()->role === 'admin') return;
        if(Auth::id() !== $enrollment->user_id) abort(403);
    }

    public static function generateCertificateNumber($event, $reg) {
        return 'CERT-EVE-' . ($event->event_date ? $event->event_date->format('Ymd') : '0000') . '-' . $event->id . '-' . $reg->id . '-' . strtoupper(Str::random(4));
    }

    public static function generateCertificateNumberCourse($course, $enrollment) {
        return 'CERT-CRS-' . now()->format('Ymd') . '-' . $course->id . '-' . $enrollment->id . '-' . strtoupper(Str::random(4));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CUSTOM TEMPLATE BUILDER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Show the visual template builder for an Event certificate.
     */
    public function templateBuilder(Event $event)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

        $existingLogos = [];
        foreach (is_array($event->certificate_logo) ? $event->certificate_logo : [] as $l) {
            $path = str_replace('storage/', '', $l);
            if (Storage::disk('public')->exists($path)) {
                $mime    = Storage::disk('public')->mimeType($path);
                $content = base64_encode(Storage::disk('public')->get($path));
                $existingLogos[] = [
                    'path'   => $l,
                    'base64' => "data:$mime;base64,$content",
                ];
            }
        }

        $existingSigs = [];
        foreach (is_array($event->certificate_signature) ? $event->certificate_signature : [] as $s) {
            $imgPath = is_array($s) ? ($s['image'] ?? '') : $s;
            $sigName = is_array($s) ? ($s['name'] ?? '') : '';
            $sigPos  = is_array($s) ? ($s['position'] ?? '') : '';
            $path    = str_replace('storage/', '', $imgPath);
            if ($path && Storage::disk('public')->exists($path)) {
                $mime    = Storage::disk('public')->mimeType($path);
                $b64     = "data:$mime;base64," . base64_encode(Storage::disk('public')->get($path));
                $existingSigs[] = ['path' => $imgPath, 'base64' => $b64, 'name' => $sigName, 'position' => $sigPos];
            }
        }

        $customTemplate = $event->certificate_custom_template;

        return view('admin.certificates.template_builder', compact('event', 'existingLogos', 'existingSigs', 'customTemplate'));
    }

    /**
     * Save the custom template JSON for an Event.
     */
    public function saveCustomTemplate(Request $request, Event $event)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'template_json' => 'required|string',
        ]);

        $templateData = json_decode($validated['template_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'JSON tidak valid'], 422);
        }

        $event->update(['certificate_custom_template' => $templateData]);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Template berhasil disimpan!']);
        }

        return redirect()->route('admin.crm.certificates.edit', $event)
            ->with('success', 'Template custom sertifikat berhasil disimpan!');
    }

    /**
     * Reset (delete) the custom template for an Event.
     */
    public function resetCustomTemplate(Event $event)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);
        $event->update(['certificate_custom_template' => null]);
        return redirect()->route('admin.crm.certificates.edit', $event)
            ->with('success', 'Template custom telah dihapus. Sistem akan menggunakan template bawaan.');
    }

    /**
     * Show the visual template builder for a Course certificate.
     */
    public function templateBuilderCourse(Course $course)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

        $existingLogos = [];
        foreach (is_array($course->certificate_logo) ? $course->certificate_logo : [] as $l) {
            $path = str_replace('storage/', '', $l);
            if (Storage::disk('public')->exists($path)) {
                $mime    = Storage::disk('public')->mimeType($path);
                $content = base64_encode(Storage::disk('public')->get($path));
                $existingLogos[] = ['path' => $l, 'base64' => "data:$mime;base64,$content"];
            }
        }

        $existingSigs = [];
        foreach (is_array($course->certificate_signature) ? $course->certificate_signature : [] as $s) {
            $imgPath = is_array($s) ? ($s['image'] ?? '') : $s;
            $sigName = is_array($s) ? ($s['name'] ?? '') : '';
            $sigPos  = is_array($s) ? ($s['position'] ?? '') : '';
            $path    = str_replace('storage/', '', $imgPath);
            if ($path && Storage::disk('public')->exists($path)) {
                $mime    = Storage::disk('public')->mimeType($path);
                $b64     = "data:$mime;base64," . base64_encode(Storage::disk('public')->get($path));
                $existingSigs[] = ['path' => $imgPath, 'base64' => $b64, 'name' => $sigName, 'position' => $sigPos];
            }
        }

        $customTemplate = $course->certificate_custom_template;

        return view('admin.certificates.template_builder', [
            'event'          => null,
            'course'         => $course,
            'existingLogos'  => $existingLogos,
            'existingSigs'   => $existingSigs,
            'customTemplate' => $customTemplate,
            'mode'           => 'course',
        ]);
    }

    /**
     * Save the custom template JSON for a Course.
     */
    public function saveCustomTemplateCourse(Request $request, Course $course)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'template_json' => 'required|string',
        ]);

        $templateData = json_decode($validated['template_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'JSON tidak valid'], 422);
        }

        $course->update(['certificate_custom_template' => $templateData]);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Template berhasil disimpan!']);
        }

        return redirect()->route('admin.crm.certificates.edit-course', $course)
            ->with('success', 'Template custom sertifikat kursus berhasil disimpan!');
    }

    /**
     * Reset (delete) the custom template for a Course.
     */
    public function resetCustomTemplateCourse(Course $course)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);
        $course->update(['certificate_custom_template' => null]);
        return redirect()->route('admin.crm.certificates.edit-course', $course)
            ->with('success', 'Template custom telah dihapus.');
    }

    /**
     * Upload an asset (logo/signature) for use in the template builder.
     */
    public function uploadBuilderAsset(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp,svg|max:3072',
        ]);

        $path = $request->file('file')->store('certificates/builder', 'public');
        $url  = asset('storage/' . $path);

        $mime    = Storage::disk('public')->mimeType($path);
        $content = base64_encode(Storage::disk('public')->get($path));
        $base64  = "data:$mime;base64,$content";

        return response()->json([
            'path'   => $path,
            'url'    => $url,
            'base64' => $base64,
        ]);
    }
}