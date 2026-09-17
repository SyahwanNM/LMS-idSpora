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
use Illuminate\Support\Collection;
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

        $event->certificate_custom_template = $this->hydrateCustomTemplateAssets($event->certificate_custom_template);
        $event->certificate_custom_template_tidak_lolos = $this->hydrateCustomTemplateAssets($event->certificate_custom_template_tidak_lolos);
        $event->certificate_custom_template_pemenang = $this->hydrateCustomTemplateAssets($event->certificate_custom_template_pemenang);

        $eventRegistrations = EventRegistration::with(['user:id,name,email', 'team:id,name'])
            ->where('event_id', $event->id)
            ->get();

        $eventParticipants = $eventRegistrations->map(function ($r) {
            $name = $r->user->name ?? $r->full_name ?? ('Peserta #' . $r->id);
            $email = $r->user->email ?? '-';
            $team = $r->team->name ?? $r->team_name ?? null;
            return [
                'id' => (int)$r->id,
                'name' => (string)$name,
                'email' => (string)$email,
                'team' => $team ? (string)$team : null,
                'is_winner' => (bool)$r->is_winner,
                'winner_title' => (string)($r->winner_title ?? 'Juara 1'),
            ];
        })->values();

        return view('admin.certificates.edit', compact('event', 'eventRegistrations', 'eventParticipants'));
    }

    /**
     * Update certificate settings (logo & signature) for an event (Admin only).
     */
    public function update(Request $request, Event $event)
    {
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        $isLomba = strtolower(trim($event->jenis ?? '')) === 'lomba';

        $rules = [
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
            'file_tambahan'               => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ];

        if ($isLomba) {
            $rules = array_merge($rules, [
                'certificate_template_tidak_lolos'        => 'nullable|string|in:template_1,template_2,template_3,template_4',
                'certificate_logo_tidak_lolos'            => 'nullable|array',
                'certificate_logo_tidak_lolos.*'          => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
                'delete_logos_tidak_lolos'                => 'nullable|array',
                'delete_logos_tidak_lolos.*'              => 'nullable|string',
                'delete_signatures_tidak_lolos'           => 'nullable|array',
                'delete_signatures_tidak_lolos.*'         => 'nullable|string',
                'certificate_signature_file_tidak_lolos'  => 'nullable|array',
                'certificate_signature_file_tidak_lolos.*'=> 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
                'existing_signature_image_tidak_lolos'    => 'nullable|array',
                'signature_name_tidak_lolos'              => 'nullable|array',
                'signature_position_tidak_lolos'          => 'nullable|array',
                'file_tambahan_tidak_lolos'               => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',

                // Pemenang (Winner)
                'certificate_template_pemenang'           => 'nullable|string|in:template_1,template_2,template_3,template_4',
                'certificate_logo_pemenang'               => 'nullable|array',
                'certificate_logo_pemenang.*'             => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
                'delete_logos_pemenang'                   => 'nullable|array',
                'delete_logos_pemenang.*'                 => 'nullable|string',
                'delete_signatures_pemenang'              => 'nullable|array',
                'delete_signatures_pemenang.*'            => 'nullable|string',
                'certificate_signature_file_pemenang'     => 'nullable|array',
                'certificate_signature_file_pemenang.*'   => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
                'existing_signature_image_pemenang'       => 'nullable|array',
                'signature_name_pemenang'                 => 'nullable|array',
                'signature_position_pemenang'             => 'nullable|array',
                'file_tambahan_pemenang'                  => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
                'winner_registration_ids'                 => 'nullable|array',
                'winner_registration_ids.*'               => 'nullable|integer',
                'winner_titles'                           => 'nullable|array',
            ]);
        }

        $request->validate($rules);

        $data = ['certificate_template' => $request->certificate_template];

        // Handle Logos (Lolos / Standard)
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

        // Handle Signatures (Lolos / Standard)
        $data['certificate_signature'] = $this->processSignatures($request, $event->certificate_signature);

        if ($request->has('delete_file_tambahan') && $request->delete_file_tambahan == '1') {
            if ($event->file_tambahan) {
                Storage::disk('public')->delete(str_replace('storage/', '', $event->file_tambahan));
                $data['file_tambahan'] = null;
            }
        }

        if ($request->hasFile('file_tambahan')) {
            if ($event->file_tambahan) {
                Storage::disk('public')->delete(str_replace('storage/', '', $event->file_tambahan));
            }
            $data['file_tambahan'] = $request->file('file_tambahan')->store('certificates', 'public');
        }

        // Handle Tidak Lolos & Pemenang for Lomba
        if ($isLomba) {
            $data['certificate_template_tidak_lolos'] = $request->input('certificate_template_tidak_lolos', 'template_1') ?: 'template_1';

            // Logos Tidak Lolos
            $existingLogosTL = is_array($event->certificate_logo_tidak_lolos) ? $event->certificate_logo_tidak_lolos : ($event->certificate_logo_tidak_lolos ? [$event->certificate_logo_tidak_lolos] : []);
            if ($request->has('delete_logos_tidak_lolos')) {
                foreach ($request->delete_logos_tidak_lolos as $logo) {
                    if (!empty($logo)) {
                        Storage::disk('public')->delete(str_replace('storage/', '', $logo));
                        $existingLogosTL = array_values(array_filter($existingLogosTL, fn($l) => $l !== $logo));
                    }
                }
            }
            if ($request->hasFile('certificate_logo_tidak_lolos')) {
                foreach ($request->file('certificate_logo_tidak_lolos') as $file) {
                    $existingLogosTL[] = $file->store('certificates', 'public');
                }
            }
            $data['certificate_logo_tidak_lolos'] = array_values(array_unique($existingLogosTL));

            // Signatures Tidak Lolos
            $data['certificate_signature_tidak_lolos'] = $this->processSignatures(
                $request,
                $event->certificate_signature_tidak_lolos,
                'delete_signatures_tidak_lolos',
                'existing_signature_image_tidak_lolos',
                'signature_name_tidak_lolos',
                'signature_position_tidak_lolos',
                'certificate_signature_file_tidak_lolos'
            );

            // File Tambahan Tidak Lolos
            if ($request->has('delete_file_tambahan_tidak_lolos') && $request->delete_file_tambahan_tidak_lolos == '1') {
                if ($event->file_tambahan_tidak_lolos) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $event->file_tambahan_tidak_lolos));
                    $data['file_tambahan_tidak_lolos'] = null;
                }
            }

            if ($request->hasFile('file_tambahan_tidak_lolos')) {
                if ($event->file_tambahan_tidak_lolos) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $event->file_tambahan_tidak_lolos));
                }
                $data['file_tambahan_tidak_lolos'] = $request->file('file_tambahan_tidak_lolos')->store('certificates', 'public');
            }

            // ── Pemenang (Winner) Configuration ──
            $data['certificate_template_pemenang'] = $request->input('certificate_template_pemenang', 'template_1') ?: 'template_1';

            // Logos Pemenang
            $existingLogosP = is_array($event->certificate_logo_pemenang) ? $event->certificate_logo_pemenang : ($event->certificate_logo_pemenang ? [$event->certificate_logo_pemenang] : []);
            if ($request->has('delete_logos_pemenang')) {
                foreach ($request->delete_logos_pemenang as $logo) {
                    if (!empty($logo)) {
                        Storage::disk('public')->delete(str_replace('storage/', '', $logo));
                        $existingLogosP = array_values(array_filter($existingLogosP, fn($l) => $l !== $logo));
                    }
                }
            }
            if ($request->hasFile('certificate_logo_pemenang')) {
                foreach ($request->file('certificate_logo_pemenang') as $file) {
                    $existingLogosP[] = $file->store('certificates', 'public');
                }
            }
            $data['certificate_logo_pemenang'] = array_values(array_unique($existingLogosP));

            // Signatures Pemenang
            $data['certificate_signature_pemenang'] = $this->processSignatures(
                $request,
                $event->certificate_signature_pemenang,
                'delete_signatures_pemenang',
                'existing_signature_image_pemenang',
                'signature_name_pemenang',
                'signature_position_pemenang',
                'certificate_signature_file_pemenang'
            );

            // File Tambahan Pemenang
            if ($request->has('delete_file_tambahan_pemenang') && $request->delete_file_tambahan_pemenang == '1') {
                if ($event->file_tambahan_pemenang) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $event->file_tambahan_pemenang));
                    $data['file_tambahan_pemenang'] = null;
                }
            }

            if ($request->hasFile('file_tambahan_pemenang')) {
                if ($event->file_tambahan_pemenang) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $event->file_tambahan_pemenang));
                }
                $data['file_tambahan_pemenang'] = $request->file('file_tambahan_pemenang')->store('certificates', 'public');
            }

            // Sync Winner Participants
            $winnerIds = array_filter(array_map('intval', (array) $request->input('winner_registration_ids', [])));
            $winnerTitles = (array) $request->input('winner_titles', []);

            // Reset registrations that are no longer winners
            EventRegistration::where('event_id', $event->id)
                ->where('is_winner', true)
                ->whereNotIn('id', $winnerIds)
                ->update([
                    'is_winner' => false,
                    'winner_title' => null,
                ]);

            // Set selected winners and update their winner_title
            foreach ($winnerIds as $wId) {
                $rawTitle = $winnerTitles[$wId] ?? $winnerTitles[(string)$wId] ?? '';
                $title = trim((string) $rawTitle);
                EventRegistration::where('event_id', $event->id)
                    ->where('id', $wId)
                    ->update([
                        'is_winner' => true,
                        'winner_title' => $title ?: 'Pemenang',
                    ]);
            }
            $data['certificate_winner_ids'] = array_values($winnerIds);
        }

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

        $course->certificate_custom_template = $this->hydrateCustomTemplateAssets($course->certificate_custom_template);

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
    private function processSignatures(
        Request $request,
        $existingRaw,
        string $deleteField = 'delete_signatures',
        string $existingImgField = 'existing_signature_image',
        string $nameField = 'signature_name',
        string $posField = 'signature_position',
        string $fileField = 'certificate_signature_file'
    ): array
    {
        // Normalisasi data lama
        $existingRaw = is_array($existingRaw) ? $existingRaw : ($existingRaw ? [$existingRaw] : []);

        // Konversi data lama ke format baru (string path → {image, name, position})
        $oldSigs = array_map(function ($s) {
            if (is_array($s)) return $s;
            return ['image' => $s, 'name' => '', 'position' => ''];
        }, $existingRaw);

        // Path gambar yang harus dihapus
        $toDelete = array_filter((array) $request->input($deleteField, []));
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
        $existingImages = $request->input($existingImgField, []);
        $sigNames       = $request->input($nameField, []);
        $sigPositions   = $request->input($posField, []);
        $sigFiles       = $request->file($fileField, []);

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
        
        if($certificateReady && !self::isSequentialCertificateNumber($registration->certificate_number)){
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

        if(!self::isSequentialCertificateNumber($registration->certificate_number)) {
            $registration->update([
                'certificate_number' => self::generateCertificateNumber($event, $registration),
                'certificate_issued_at' => now(),
            ]);
        }
        
        $data = $this->getCertificateData($event, $registration->fresh());
        
        $isLomba = strtolower(trim($event->jenis ?? '')) === 'lomba';
        $isLolos = strtolower(trim($registration->submission_status ?? '')) === 'lolos';
        $isMenang = (bool) ($registration->is_winner ?? false);
        if (!$isMenang && !empty($event->certificate_winner_ids) && is_array($event->certificate_winner_ids)) {
            $isMenang = in_array((int)$registration->id, array_map('intval', $event->certificate_winner_ids), true);
        }

        if ($isMenang && !empty($event->certificate_custom_template_pemenang)) {
            $customTpl = $event->certificate_custom_template_pemenang;
        } elseif ($isLomba && !$isLolos && !empty($event->certificate_custom_template_tidak_lolos)) {
            $customTpl = $event->certificate_custom_template_tidak_lolos;
        } else {
            $customTpl = $event->certificate_custom_template;
        }

        // Gunakan template custom jika tersedia, otherwise gunakan template bawaan
        $viewName = !empty($customTpl)
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

        $isLomba = strtolower(trim($event->jenis ?? '')) === 'lomba';

        foreach($registrations as $registration) {
            if (empty($registration->user)) {
                continue; // Lewati registrasi jika data akun user relasi kosong/dihapus
            }
            if(!self::isSequentialCertificateNumber($registration->certificate_number)) {
                $registration->update([
                    'certificate_number' => $this->generateCertificateNumber($event, $registration),
                    'certificate_issued_at' => now(),
                ]);
            }
            $data = $this->getCertificateData($event, $registration->fresh());
            
            $isLolos = strtolower(trim($registration->submission_status ?? '')) === 'lolos';
            $isMenang = (bool) ($registration->is_winner ?? false);
            if (!$isMenang && !empty($event->certificate_winner_ids) && is_array($event->certificate_winner_ids)) {
                $isMenang = in_array((int)$registration->id, array_map('intval', $event->certificate_winner_ids), true);
            }

            if ($isMenang && !empty($event->certificate_custom_template_pemenang)) {
                $customTpl = $event->certificate_custom_template_pemenang;
            } elseif ($isLomba && !$isLolos && !empty($event->certificate_custom_template_tidak_lolos)) {
                $customTpl = $event->certificate_custom_template_tidak_lolos;
            } else {
                $customTpl = $event->certificate_custom_template;
            }

            $viewName = !empty($customTpl)
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


        if(!self::isSequentialCertificateNumber($enrollment->certificate_number)) {
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

        if(!self::isSequentialCertificateNumber($enrollment->certificate_number)) {
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
        $isLomba = strtolower(trim($event->jenis ?? '')) === 'lomba';
        $isLolos = strtolower(trim($registration->submission_status ?? '')) === 'lolos';
        $isMenang = (bool) ($registration->is_winner ?? false);
        if (!$isMenang && !empty($event->certificate_winner_ids) && is_array($event->certificate_winner_ids)) {
            $isMenang = in_array((int)$registration->id, array_map('intval', $event->certificate_winner_ids), true);
        }
        $winnerTitle = $registration->winner_title ?: 'Pemenang';

        // Tentukan template aktif
        $template = $event->certificate_template ?? 'template_1';
        if ($isMenang && !empty($event->certificate_template_pemenang)) {
            $template = $event->certificate_template_pemenang;
        } elseif ($isLomba && !$isLolos && !empty($event->certificate_template_tidak_lolos)) {
            $template = $event->certificate_template_tidak_lolos ?: $template;
        }

        // Tentukan logo aktif
        $logoField = $event->certificate_logo;
        if ($isMenang && !empty($event->certificate_logo_pemenang)) {
            $logoField = $event->certificate_logo_pemenang;
        } elseif ($isLomba && !$isLolos && !empty($event->certificate_logo_tidak_lolos)) {
            $logoField = $event->certificate_logo_tidak_lolos;
        }

        $logosBase64 = [];
        $logosUrl = [];
        foreach(is_array($logoField) ? $logoField : [] as $l) {
            $path = str_replace('storage/', '', $l);
            if(Storage::disk('public')->exists($path)) {
                $mime = Storage::disk('public')->mimeType($path);
                $content = base64_encode(Storage::disk('public')->get($path));
                $logosBase64[] = "data:$mime;base64,$content";
                $logosUrl[] = request()->schemeAndHttpHost() . '/uploads/' . $path;
            }
        }

        // Tentukan signatures aktif
        $sigsRaw = $event->certificate_signature;
        if ($isMenang && !empty($event->certificate_signature_pemenang)) {
            $sigsRaw = $event->certificate_signature_pemenang;
        } elseif ($isLomba && !$isLolos && !empty($event->certificate_signature_tidak_lolos)) {
            $sigsRaw = $event->certificate_signature_tidak_lolos;
        }
        $sigsRaw = is_array($sigsRaw) ? $sigsRaw : [];

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

        // Tentukan file tambahan aktif
        $fileTambahanRaw = $event->file_tambahan;
        if ($isMenang && !empty($event->file_tambahan_pemenang)) {
            $fileTambahanRaw = $event->file_tambahan_pemenang;
        } elseif ($isLomba && !$isLolos && !empty($event->file_tambahan_tidak_lolos)) {
            $fileTambahanRaw = $event->file_tambahan_tidak_lolos;
        }

        $fileTambahanBase64 = null;
        if ($fileTambahanRaw) {
            $path = str_replace('storage/', '', $fileTambahanRaw);
            if(Storage::disk('public')->exists($path)) {
                $mime = Storage::disk('public')->mimeType($path);
                $content = base64_encode(Storage::disk('public')->get($path));
                $fileTambahanBase64 = "data:$mime;base64,$content";
            }
        }

        return [
            'event'            => $event,
            'user'             => $registration->user,
            'template'         => $template,
            'isLomba'          => $isLomba,
            'isLolos'          => $isLolos,
            'isMenang'         => $isMenang,
            'winnerTitle'      => $winnerTitle,
            'issuedAt'         => $registration->certificate_issued_at ?? now(),
            'certificateNumber'=> $registration->certificate_number,
            'fileTambahanBase64'=> $fileTambahanBase64,
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

    public function isCertificateReady(Event $event, ?EventRegistration $registration = null) {
        if ($registration && $registration->certificate_issued_at) return true;

        if ($registration && !empty($registration->has_link_feedback)) return true;
        
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
        $registrations = EventRegistration::query()
            ->with('user:id,name')
            ->where('event_id', $event->id)
            ->where('status', 'active')
            ->get();

        $year = ($event && $event->event_date) ? $event->event_date->format('Y') : now()->format('Y');
        $year = $year ?: '2026';

        $sequence = self::buildCertificateSequenceNumber($registrations, $reg, fn ($item) => $item->user?->name ?? $item->full_name ?? '');

        return "{$sequence}/AKD10/AKD-BPA/{$year}";
    }

    public static function generateCertificateNumberCourse($course, $enrollment) {
        $enrollments = Enrollment::query()
            ->with('user:id,name')
            ->where('course_id', $course->id)
            ->where('status', 'completed')
            ->get();

        $year = now()->format('Y') ?: '2026';

        $sequence = self::buildCertificateSequenceNumber($enrollments, $enrollment, fn ($item) => $item->user?->name ?? '');

        return "{$sequence}/AKD10/AKD-BPA/{$year}";
    }

    private static function buildCertificateSequenceNumber(Collection $records, $currentRecord, callable $nameResolver): string
    {
        // If currentRecord already has a certificate number with a sequence (e.g. '009' or '009/AKD10/AKD-BPA/2026'), preserve it
        $existing = data_get($currentRecord, 'certificate_number');
        if (is_string($existing) && preg_match('/^(\d+)/', trim($existing), $matches)) {
            return str_pad($matches[1], 3, '0', STR_PAD_LEFT);
        }

        $sortedRecords = $records->sortBy(function ($record) use ($nameResolver) {
            $name = trim((string) $nameResolver($record));
            $id = (int) data_get($record, 'id', 0);

            return Str::lower($name) . '|' . str_pad((string) $id, 12, '0', STR_PAD_LEFT);
        })->values();

        $currentId = (int) data_get($currentRecord, 'id', 0);
        $position = $sortedRecords->search(fn ($record) => (int) data_get($record, 'id', 0) === $currentId);

        if ($position === false) {
            $position = 0;
        }

        return str_pad((string) (1 + (int) $position), 3, '0', STR_PAD_LEFT);
    }

    private static function isSequentialCertificateNumber(?string $value): bool
    {
        return is_string($value) && preg_match('/^\d+\/AKD10\/AKD-BPA\/\d{4}$/', trim($value)) === 1;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CUSTOM TEMPLATE BUILDER
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Show the visual template builder for an Event certificate.
     */
    public function templateBuilder(Request $request, Event $event)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

        $isLomba = strtolower(trim($event->jenis ?? '')) === 'lomba';
        $reqType = $request->query('type');
        $type = 'lolos';
        if ($reqType === 'pemenang') {
            $type = 'pemenang';
        } elseif ($isLomba && $reqType === 'tidak_lolos') {
            $type = 'tidak_lolos';
        }

        if ($type === 'pemenang') {
            $rawLogos = $event->certificate_logo_pemenang ?: $event->certificate_logo;
            $rawSigs = $event->certificate_signature_pemenang ?: $event->certificate_signature;
            $customTemplate = $event->certificate_custom_template_pemenang;
        } elseif ($type === 'tidak_lolos') {
            $rawLogos = $event->certificate_logo_tidak_lolos ?: $event->certificate_logo;
            $rawSigs = $event->certificate_signature_tidak_lolos ?: $event->certificate_signature;
            $customTemplate = $event->certificate_custom_template_tidak_lolos;
        } else {
            $rawLogos = $event->certificate_logo;
            $rawSigs = $event->certificate_signature;
            $customTemplate = $event->certificate_custom_template;
        }

        $existingLogos = [];
        foreach (is_array($rawLogos) ? $rawLogos : [] as $l) {
            $resolved = $this->resolveAssetBase64($l);
            if ($resolved) {
                $existingLogos[] = $resolved;
            }
        }

        $existingSigs = [];
        foreach (is_array($rawSigs) ? $rawSigs : [] as $s) {
            $imgPath = is_array($s) ? ($s['image'] ?? '') : $s;
            $sigName = is_array($s) ? ($s['name'] ?? '') : '';
            $sigPos  = is_array($s) ? ($s['position'] ?? '') : '';
            $resolved = $this->resolveAssetBase64($imgPath);
            if ($resolved) {
                $existingSigs[] = [
                    'path'     => $resolved['path'],
                    'base64'   => $resolved['base64'],
                    'name'     => $sigName,
                    'position' => $sigPos,
                ];
            }
        }

        $customTemplate = $this->hydrateCustomTemplateAssets($customTemplate);

        return view('admin.certificates.template_builder', compact('event', 'existingLogos', 'existingSigs', 'customTemplate', 'type'));
    }

    /**
     * Save the custom template JSON for an Event.
     */
    public function saveCustomTemplate(Request $request, Event $event)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);

        $validated = $request->validate([
            'template_json' => 'required|string',
            'type'          => 'nullable|string|in:lolos,tidak_lolos,pemenang',
        ]);

        $templateData = json_decode($validated['template_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'JSON tidak valid'], 422);
        }

        $type = $request->input('type', 'lolos');
        if ($type === 'pemenang') {
            $field = 'certificate_custom_template_pemenang';
        } elseif ($type === 'tidak_lolos') {
            $field = 'certificate_custom_template_tidak_lolos';
        } else {
            $field = 'certificate_custom_template';
        }

        // Strip heavy base64 strings from elements that already have a file path (src) to keep database lightweight
        if (isset($templateData['elements']) && is_array($templateData['elements'])) {
            foreach ($templateData['elements'] as &$el) {
                if (is_array($el) && !empty($el['src']) && isset($el['base64'])) {
                    unset($el['base64']);
                }
            }
            unset($el);
        }

        $event->update([$field => $templateData]);

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Template berhasil disimpan!']);
        }

        return redirect()->route('admin.crm.certificates.edit', $event)
            ->with('success', 'Template custom sertifikat berhasil disimpan!');
    }

    /**
     * Reset (delete) the custom template for an Event.
     */
    public function resetCustomTemplate(Request $request, Event $event)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') abort(403);
        $type = $request->input('type', 'lolos');
        if ($type === 'pemenang') {
            $field = 'certificate_custom_template_pemenang';
        } elseif ($type === 'tidak_lolos') {
            $field = 'certificate_custom_template_tidak_lolos';
        } else {
            $field = 'certificate_custom_template';
        }
        $event->update([$field => null]);
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
            $resolved = $this->resolveAssetBase64($l);
            if ($resolved) {
                $existingLogos[] = $resolved;
            }
        }

        $existingSigs = [];
        foreach (is_array($course->certificate_signature) ? $course->certificate_signature : [] as $s) {
            $imgPath = is_array($s) ? ($s['image'] ?? '') : $s;
            $sigName = is_array($s) ? ($s['name'] ?? '') : '';
            $sigPos  = is_array($s) ? ($s['position'] ?? '') : '';
            $resolved = $this->resolveAssetBase64($imgPath);
            if ($resolved) {
                $existingSigs[] = [
                    'path'     => $resolved['path'],
                    'base64'   => $resolved['base64'],
                    'name'     => $sigName,
                    'position' => $sigPos,
                ];
            }
        }

        $customTemplate = $this->hydrateCustomTemplateAssets($course->certificate_custom_template);

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

        // Strip heavy base64 strings from elements that already have a file path (src) to keep database lightweight
        if (isset($templateData['elements']) && is_array($templateData['elements'])) {
            foreach ($templateData['elements'] as &$el) {
                if (is_array($el) && !empty($el['src']) && isset($el['base64'])) {
                    unset($el['base64']);
                }
            }
            unset($el);
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
        $url  = asset('uploads/' . $path);

        $mime    = Storage::disk('public')->mimeType($path);
        $content = base64_encode(Storage::disk('public')->get($path));
        $base64  = "data:$mime;base64,$content";

        return response()->json([
            'path'   => $path,
            'url'    => $url,
            'base64' => $base64,
        ]);
    }

    /**
     * Resolve a relative or absolute storage path to a valid base64 data URI and cleaned path.
     */
    private function resolveAssetBase64(?string $rawPath): ?array
    {
        if (empty($rawPath) || !is_string($rawPath)) return null;

        // If it's already a base64 data URI
        if (str_starts_with($rawPath, 'data:image')) {
            return ['path' => '', 'base64' => $rawPath];
        }

        // Normalize slashes and strip common prefixes
        $clean = str_replace('\\', '/', trim($rawPath));
        $clean = preg_replace('#^https?://[^/]+/(uploads/|storage/)?#i', '', $clean);
        $clean = ltrim($clean, '/');
        $clean = preg_replace('#^(storage/app/public/|storage/|uploads/|public/)+#i', '', $clean);

        if ($clean === '') return null;

        // 1. Try public disk (which maps to public/uploads)
        if (Storage::disk('public')->exists($clean)) {
            $mime = Storage::disk('public')->mimeType($clean) ?: 'image/png';
            $content = base64_encode(Storage::disk('public')->get($clean));
            return ['path' => $clean, 'base64' => "data:$mime;base64,$content"];
        }

        // 2. Try candidate filesystem locations
        $candidates = [
            public_path('uploads/' . $clean),
            public_path('uploads/' . $rawPath),
            storage_path('app/public/' . $clean),
            storage_path('app/public/' . $rawPath),
            public_path($clean),
            public_path($rawPath),
            public_path('storage/' . $clean),
            base_path('../public_html/uploads/' . $clean),
            base_path('../public_html/' . $clean),
            base_path('../public_html/storage/' . $clean),
            base_path('public/uploads/' . $clean),
        ];

        foreach ($candidates as $cand) {
            if ($cand && file_exists($cand) && is_file($cand)) {
                $mime = @mime_content_type($cand);
                if (!$mime) {
                    $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                    $mimes = [
                        'jpg'  => 'image/jpeg',
                        'jpeg' => 'image/jpeg',
                        'png'  => 'image/png',
                        'gif'  => 'image/gif',
                        'webp' => 'image/webp',
                        'svg'  => 'image/svg+xml',
                    ];
                    $mime = $mimes[$ext] ?? 'image/png';
                }
                $content = base64_encode(file_get_contents($cand));
                return ['path' => $clean, 'base64' => "data:$mime;base64,$content"];
            }
        }

        return null;
    }

    /**
     * Hydrate base64 data URIs for all logo/shape/signature elements in a custom template.
     */
    private function hydrateCustomTemplateAssets(?array $template): ?array
    {
        if (empty($template) || !is_array($template)) {
            return $template;
        }

        // Hydrate background image if it's a file path
        if (!empty($template['background']['image']) && is_string($template['background']['image']) && !str_starts_with($template['background']['image'], 'data:')) {
            $resolvedBg = $this->resolveAssetBase64($template['background']['image']);
            if ($resolvedBg) {
                $template['background']['image'] = $resolvedBg['base64'];
            }
        }

        if (empty($template['elements']) || !is_array($template['elements'])) {
            return $template;
        }

        foreach ($template['elements'] as &$el) {
            if (!is_array($el)) continue;
            $type = $el['type'] ?? '';
            if (in_array($type, ['logo', 'shape', 'signature'])) {
                $hasValidB64 = !empty($el['base64']) && is_string($el['base64']) && str_starts_with($el['base64'], 'data:');
                if (!$hasValidB64 && !empty($el['src'])) {
                    $resolved = $this->resolveAssetBase64($el['src']);
                    if ($resolved) {
                        $el['base64'] = $resolved['base64'];
                        $el['src']    = $resolved['path'];
                    }
                }
            }
        }
        unset($el);

        return $template;
    }
}