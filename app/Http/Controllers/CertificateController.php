<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Dompdf\Dompdf;
use Illuminate\Support\Str;
use ZipArchive;

class CertificateController extends Controller
{
    /**
     * Display list of events for certificate management (Admin only).
     */
    public function index()
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Get all events with registration count and certificate info
        $events = Event::withCount('registrations')
            ->orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.certificates.index', compact('events'));
    }

    /**
     * Show form to edit certificate settings (logo & signature) for an event (Admin only).
     */
    public function edit(Event $event)
    {
        // Only admin can access
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
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Validate file uploads
        $request->validate([
            'certificate_logo' => 'nullable|array',
            'certificate_logo.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'certificate_signature' => 'nullable|array',
            'certificate_signature.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'delete_logos' => 'nullable|array',
            'delete_logos.*' => 'string',
            'delete_signatures' => 'nullable|array',
            'delete_signatures.*' => 'string',
        ]);

        $data = [];
        $hasChanges = false;

        // Handle certificate logo uploads (multiple)
        $existingLogos = is_array($event->certificate_logo) ? $event->certificate_logo : ($event->certificate_logo ? [$event->certificate_logo] : []);
        
        // Delete old logos if requested
        if($request->has('delete_logos') && is_array($request->delete_logos)) {
            foreach($request->delete_logos as $logoToDelete) {
                if(!empty($logoToDelete)) {
                    // Normalize path
                    $logoPath = str_replace('storage/', '', $logoToDelete);
                    if(Storage::disk('public')->exists($logoPath)) {
                        Storage::disk('public')->delete($logoPath);
                    }
                    $existingLogos = array_values(array_filter($existingLogos, fn($l) => $l !== $logoToDelete && str_replace('storage/', '', $l) !== $logoPath));
                }
            }
            $hasChanges = true;
        }
        
        // Add new logos
        if ($request->hasFile('certificate_logo')) {
            $newLogos = [];
            foreach($request->file('certificate_logo') as $logoFile) {
                if($logoFile->isValid()) {
                    $newLogos[] = $logoFile->store('certificates', 'public');
                }
            }
            if(!empty($newLogos)) {
                $existingLogos = array_merge($existingLogos, $newLogos);
                $hasChanges = true;
            }
        }
        
        if($hasChanges || $request->has('delete_logos') || $request->hasFile('certificate_logo')) {
            $data['certificate_logo'] = array_values(array_unique($existingLogos));
        }

        // Handle certificate signature uploads (multiple)
        $existingSignatures = is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : []);
        $hasSigChanges = false;
        
        // Delete old signatures if requested
        if($request->has('delete_signatures') && is_array($request->delete_signatures)) {
            foreach($request->delete_signatures as $sigToDelete) {
                if(!empty($sigToDelete)) {
                    // Normalize path
                    $sigPath = str_replace('storage/', '', $sigToDelete);
                    if(Storage::disk('public')->exists($sigPath)) {
                        Storage::disk('public')->delete($sigPath);
                    }
                    $existingSignatures = array_values(array_filter($existingSignatures, fn($s) => $s !== $sigToDelete && str_replace('storage/', '', $s) !== $sigPath));
                }
            }
            $hasSigChanges = true;
        }
        
        // Add new signatures
        if ($request->hasFile('certificate_signature')) {
            $newSignatures = [];
            foreach($request->file('certificate_signature') as $sigFile) {
                if($sigFile->isValid()) {
                    $newSignatures[] = $sigFile->store('certificates', 'public');
                }
            }
            if(!empty($newSignatures)) {
                $existingSignatures = array_merge($existingSignatures, $newSignatures);
                $hasSigChanges = true;
            }
        }
        
        if($hasSigChanges || $request->has('delete_signatures') || $request->hasFile('certificate_signature')) {
            $data['certificate_signature'] = array_values(array_unique($existingSignatures));
        }

        if(!empty($data)){
            try {
                $event->update($data);
                return redirect()->route('admin.certificates.index')->with('success', 'Pengaturan sertifikat berhasil diperbarui!');
            } catch(\Exception $e) {
                \Log::error('Error updating certificate settings: ' . $e->getMessage());
                return redirect()->route('admin.certificates.edit', $event)
                    ->with('error', 'Terjadi kesalahan saat menyimpan pengaturan. Silakan coba lagi.');
            }
        }

        return redirect()->route('admin.certificates.edit', $event)->with('info', 'Tidak ada perubahan yang disimpan.');
    }

    /**
     * Display certificate preview (HTML) if ready (H+3 after event_date) for a registration.
     */
    public function show(Event $event, $registration)
    {
        // Resolve registration if it's an ID
        if(!($registration instanceof EventRegistration)) {
            $registration = EventRegistration::findOrFail($registration);
        }
        
        // Verify registration belongs to event
        if($registration->event_id !== $event->id) {
            abort(404, 'Registrasi tidak ditemukan untuk event ini.');
        }
        
        $this->authorizeAccess($event, $registration);
        $certificateReady = $this->isCertificateReady($event); // preview allowed even if false
        // Use existing stored certificate number if exists (persisted after first ready generation)
        $storedNumber = $registration->certificate_number;
        $generated = $storedNumber ?: $this->generateCertificateNumber($event, $registration);
        if($certificateReady && !$storedNumber){
            $registration->update([
                'certificate_number' => $generated,
                'certificate_issued_at' => now(),
            ]);
        }
        return view('events.certificate', [
            'event' => $event,
            'registration' => $registration->fresh(),
            'user' => $registration->user,
            'issuedAt' => $registration->certificate_issued_at ?? now(),
            'certificateNumber' => $generated,
            'certificateReady' => $certificateReady,
        ]);
    }

    /**
     * Download PDF of certificate.
     */
    public function download(Request $request, Event $event, $registration)
    {
        // Resolve registration if it's an ID
        if(!($registration instanceof EventRegistration)) {
            $registration = EventRegistration::with('user', 'event')->findOrFail($registration);
        } else {
            // Load relationships if not already loaded
            if(!$registration->relationLoaded('user')) {
                $registration->load('user');
            }
            if(!$registration->relationLoaded('event')) {
                $registration->load('event');
            }
        }
        
        // Verify registration belongs to event
        if($registration->event_id !== $event->id) {
            abort(404, 'Registrasi tidak ditemukan untuk event ini.');
        }
        
        // Ensure user exists
        if(!$registration->user) {
            abort(404, 'Data user tidak ditemukan.');
        }
        
        $this->authorizeAccess($event, $registration);

        $certificateReady = $this->isCertificateReady($event);
        // Allow forced download in local environment for testing (e.g. ?force=1)
        $force = app()->environment('local') && $request->boolean('force');
        // Also allow force in any environment if explicitly requested (for testing)
        if(!$force) {
            $force = $request->boolean('force');
        }
        if(!$certificateReady && !$force){
            return redirect()->route('profile.events')->with('info','Sertifikat belum tersedia. (H+3)');
        }

        // Persist certificate number if ready or forced
        $storedNumber = $registration->certificate_number;
        $certificateNumber = $storedNumber ?: $this->generateCertificateNumber($event, $registration);
        if(($certificateReady || $force) && !$storedNumber){
            $registration->update([
                'certificate_number' => $certificateNumber,
                'certificate_issued_at' => now(),
            ]);
        }
        $registration->refresh();
        
        // Prepare image paths for PDF (support multiple)
        $logosBase64 = [];
        $signaturesBase64 = [];
        
        $logos = is_array($event->certificate_logo) ? $event->certificate_logo : ($event->certificate_logo ? [$event->certificate_logo] : []);
        foreach($logos as $logoPath) {
            if(str_starts_with($logoPath, 'storage/')) {
                $logoPath = str_replace('storage/', '', $logoPath);
            }
            $fullLogoPath = storage_path('app/public/' . $logoPath);
            if(file_exists($fullLogoPath)) {
                $logoData = file_get_contents($fullLogoPath);
                $logoInfo = pathinfo($fullLogoPath);
                $logoMime = 'image/' . strtolower($logoInfo['extension'] ?? 'png');
                $logosBase64[] = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
            }
        }
        
        $signatures = is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : []);
        foreach($signatures as $signaturePath) {
            if(str_starts_with($signaturePath, 'storage/')) {
                $signaturePath = str_replace('storage/', '', $signaturePath);
            }
            $fullSignaturePath = storage_path('app/public/' . $signaturePath);
            if(file_exists($fullSignaturePath)) {
                $signatureData = file_get_contents($fullSignaturePath);
                $signatureInfo = pathinfo($fullSignaturePath);
                $signatureMime = 'image/' . strtolower($signatureInfo['extension'] ?? 'png');
                $signaturesBase64[] = 'data:' . $signatureMime . ';base64,' . base64_encode($signatureData);
            }
        }
        
        $data = [
            'event' => $event,
            'registration' => $registration,
            'user' => $registration->user,
            'issuedAt' => $registration->certificate_issued_at ?? now(),
            'certificateNumber' => $certificateNumber,
            'logosBase64' => $logosBase64,
            'signaturesBase64' => $signaturesBase64,
        ];

        try {
            $html = view('events.certificate-pdf', $data)->render();
            
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->setIsRemoteEnabled(true);
            $options->setIsHtml5ParserEnabled(true);
            $options->setChroot(public_path());
            $dompdf->setOptions($options);
            
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            $filename = 'sertifikat-'.$event->id.'-'.$registration->id.'.pdf';
            $output = $dompdf->output();
            
            if(empty($output)) {
                \Log::error('Dompdf output is empty for event: ' . $event->id . ', registration: ' . $registration->id);
                return redirect()->route('profile.events')
                    ->with('error', 'Gagal menghasilkan PDF sertifikat. Silakan coba lagi atau hubungi administrator.');
            }

            $inline = $request->boolean('inline');
            $disposition = $inline ? 'inline' : 'attachment';
            
            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition.'; filename="'.$filename.'"',
                'Content-Length' => strlen($output),
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]);
        } catch(\Exception $e) {
            \Log::error('Error generating certificate PDF: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('profile.events')
                ->with('error', 'Terjadi kesalahan saat menghasilkan PDF sertifikat: ' . $e->getMessage());
        }
    }

    private function isCertificateReady(Event $event): bool
    {
        if(!$event->event_date) return false;
        return now()->greaterThanOrEqualTo($event->event_date->copy()->addDays(3));
    }

    private function authorizeAccess(Event $event, EventRegistration $registration): void
    {
        $user = Auth::user();
        if(!$user || $registration->user_id !== $user->id || $registration->event_id !== $event->id){
            abort(403, 'Tidak berwenang');
        }
    }

    private function generateCertificateNumber(Event $event, EventRegistration $registration): string
    {
        // Format: EVT-YYYYMMDD-EventID-REGID-RANDOM
        $datePart = $event->event_date?->format('Ymd') ?? '00000000';
        return 'EVT-'.$datePart.'-'.$event->id.'-'.$registration->id.'-'.strtoupper(Str::random(4));
    }

    /**
     * Generate certificates for all registered users of an event (Admin only).
     */
    public function generateMassal(Request $request, Event $event)
    {
        // Only admin can access
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

        // Get all registrations for this event
        $registrations = $event->registrations()->with('user')->get();
        
        if($registrations->isEmpty()){
            return redirect()->route('admin.events.show', $event)
                ->with('error', 'Tidak ada peserta yang terdaftar untuk event ini.');
        }

        // Create temporary directory for PDFs
        $tempDir = storage_path('app/temp/certificates_' . $event->id . '_' . time());
        if(!is_dir($tempDir)){
            mkdir($tempDir, 0755, true);
        }

        $generated = 0;
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);

        foreach($registrations as $registration){
            try {
                // Generate certificate number if not exists
                $storedNumber = $registration->certificate_number;
                $certificateNumber = $storedNumber ?: $this->generateCertificateNumber($event, $registration);
                
                if(!$storedNumber){
                    $registration->update([
                        'certificate_number' => $certificateNumber,
                        'certificate_issued_at' => now(),
                    ]);
                }

                // Prepare image paths for PDF (support multiple)
                $logosBase64 = [];
                $signaturesBase64 = [];
                
                $logos = is_array($event->certificate_logo) ? $event->certificate_logo : ($event->certificate_logo ? [$event->certificate_logo] : []);
                foreach($logos as $logoPath) {
                    if(str_starts_with($logoPath, 'storage/')) {
                        $logoPath = str_replace('storage/', '', $logoPath);
                    }
                    $fullLogoPath = storage_path('app/public/' . $logoPath);
                    if(file_exists($fullLogoPath)) {
                        $logoData = file_get_contents($fullLogoPath);
                        $logoInfo = pathinfo($fullLogoPath);
                        $logoMime = 'image/' . strtolower($logoInfo['extension'] ?? 'png');
                        $logosBase64[] = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
                    }
                }
                
                $signatures = is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : []);
                foreach($signatures as $signaturePath) {
                    if(str_starts_with($signaturePath, 'storage/')) {
                        $signaturePath = str_replace('storage/', '', $signaturePath);
                    }
                    $fullSignaturePath = storage_path('app/public/' . $signaturePath);
                    if(file_exists($fullSignaturePath)) {
                        $signatureData = file_get_contents($fullSignaturePath);
                        $signatureInfo = pathinfo($fullSignaturePath);
                        $signatureMime = 'image/' . strtolower($signatureInfo['extension'] ?? 'png');
                        $signaturesBase64[] = 'data:' . $signatureMime . ';base64,' . base64_encode($signatureData);
                    }
                }
                
                $data = [
                    'event' => $event,
                    'registration' => $registration->fresh(),
                    'user' => $registration->user,
                    'issuedAt' => $registration->certificate_issued_at ?? now(),
                    'certificateNumber' => $certificateNumber,
                    'logosBase64' => $logosBase64,
                    'signaturesBase64' => $signaturesBase64,
                ];

                $html = view('events.certificate-pdf', $data)->render();
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'landscape');
                $dompdf->render();

                // Save PDF to temp directory
                $filename = 'Sertifikat_' . str_replace([' ', '/'], '_', $registration->user->name) . '_' . $registration->id . '.pdf';
                $filepath = $tempDir . '/' . $filename;
                file_put_contents($filepath, $dompdf->output());
                $generated++;
            } catch(\Exception $e){
                \Log::error('Error generating certificate for registration ' . $registration->id . ': ' . $e->getMessage());
                continue;
            }
        }

        if($generated === 0){
            // Cleanup
            array_map('unlink', glob($tempDir . '/*'));
            rmdir($tempDir);
            return redirect()->route('admin.events.show', $event)
                ->with('error', 'Gagal menghasilkan sertifikat.');
        }

        // Create ZIP file
        $zipFilename = 'Sertifikat_' . str_replace([' ', '/'], '_', $event->title) . '_' . date('YmdHis') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFilename);
        
        $zip = new ZipArchive();
        if($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE){
            $files = glob($tempDir . '/*.pdf');
            foreach($files as $file){
                $zip->addFile($file, basename($file));
            }
            $zip->close();
        }

        // Cleanup temp directory
        array_map('unlink', glob($tempDir . '/*'));
        rmdir($tempDir);

        // Return ZIP file
        return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
    }
}
