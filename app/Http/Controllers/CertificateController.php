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
        if(!Auth::check() || Auth::user()->role !== 'admin'){
            abort(403, 'Hanya admin yang dapat mengakses fitur ini');
        }

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
            'certificate_logo' => 'nullable|array',
            'certificate_logo.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'certificate_signature' => 'nullable|array',
            'certificate_signature.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'certificate_template' => 'required|string|in:template_1,template_2,template_3',
            'delete_logos' => 'nullable|array',
            'delete_logos.*' => 'string',
            'delete_signatures' => 'nullable|array',
            'delete_signatures.*' => 'string',
        ]);

        $data = ['certificate_template' => $request->certificate_template];
        
        // Handle Logos
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

        // Handle Signatures
        $existingSigs = is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : []);
        if($request->has('delete_signatures')) {
            foreach($request->delete_signatures as $sig) {
                if(!empty($sig)) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $sig));
                    $existingSigs = array_values(array_filter($existingSigs, fn($s) => $s !== $sig));
                }
            }
        }
        if ($request->hasFile('certificate_signature')) {
            foreach($request->file('certificate_signature') as $file) {
                $existingSigs[] = $file->store('certificates', 'public');
            }
        }
        $data['certificate_signature'] = array_values(array_unique($existingSigs));

        $event->update($data);
        return redirect()->route('admin.crm.certificates.index')->with('success', 'Konfigurasi sertifikat berhasil diperbarui!');
    }

    public function show(Event $event, $registration)
    {
        if(!($registration instanceof EventRegistration)) {
            $registration = EventRegistration::findOrFail($registration);
        }
        if($registration->event_id !== $event->id) abort(404);
        
        $this->authorizeAccess($event, $registration);
        $certificateReady = $this->isCertificateReady($event);
        
        if($certificateReady && !$registration->certificate_number){
            $registration->update([
                'certificate_number' => $this->generateCertificateNumber($event, $registration),
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
        $certificateReady = $this->isCertificateReady($event);
        $force = $request->boolean('force');
        
        if(!$certificateReady && !$force) {
            return redirect()->back()->with('info','Sertifikat belum tersedia (H+3).');
        }

        if(!$registration->certificate_number) {
            $registration->update([
                'certificate_number' => $this->generateCertificateNumber($event, $registration),
                'certificate_issued_at' => now(),
            ]);
        }
        
        $data = $this->getCertificateData($event, $registration->fresh());
        
        $dompdf = new Dompdf();
        $options = $dompdf->getOptions();
        $options->set('show_warnings', true);
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $dompdf->setOptions($options);
        
        $html = view('events.certificate-pdf', $data)->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
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

        $dompdf = new Dompdf();
        $dompdf->setPaper('A4', 'landscape');
        $options = $dompdf->getOptions();
        $options->setIsRemoteEnabled(true);
        $dompdf->setOptions($options);

        foreach($registrations as $registration) {
            if(!$registration->certificate_number) {
                $registration->update([
                    'certificate_number' => $this->generateCertificateNumber($event, $registration),
                    'certificate_issued_at' => now(),
                ]);
            }
            $data = $this->getCertificateData($event, $registration->fresh());
            $html = view('events.certificate-pdf', $data)->render();
            $dompdf->loadHtml($html);
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

    private function getCertificateData(Event $event, EventRegistration $registration)
    {
        $logos = [];
        foreach(is_array($event->certificate_logo) ? $event->certificate_logo : [] as $l) {
            $path = str_replace('storage/', '', $l);
            if(Storage::disk('public')->exists($path)) {
                $logos[] = 'data:'.Storage::disk('public')->mimeType($path).';base64,'.base64_encode(Storage::disk('public')->get($path));
            }
        }
        $sigs = [];
        foreach(is_array($event->certificate_signature) ? $event->certificate_signature : [] as $s) {
            $path = str_replace('storage/', '', $s);
            if(Storage::disk('public')->exists($path)) {
                $sigs[] = 'data:'.Storage::disk('public')->mimeType($path).';base64,'.base64_encode(Storage::disk('public')->get($path));
            }
        }
        return [
            'event' => $event,
            'user' => $registration->user,
            'issuedAt' => $registration->certificate_issued_at ?? now(),
            'certificateNumber' => $registration->certificate_number,
            'logosBase64' => $logos,
            'signaturesBase64' => $sigs,
        ];
    }

    private function isCertificateReady(Event $event) {
        return $event->event_date && now()->greaterThanOrEqualTo($event->event_date->copy()->addDays(3));
    }

    private function authorizeAccess($event, $registration) {
        if(Auth::user()->role === 'admin') return;
        if(Auth::id() !== $registration->user_id) abort(403);
    }

    private function generateCertificateNumber($event, $reg) {
        return 'CERT-'.($event->event_date?->format('Ymd')??'0000').'-'.$event->id.'-'.$reg->id.'-'.strtoupper(Str::random(4));
    }
}