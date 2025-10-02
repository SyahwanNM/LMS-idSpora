<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    /**
     * Display certificate preview (HTML) if ready (H+4 after event_date) for a registration.
     */
    public function show(Event $event, EventRegistration $registration)
    {
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
    public function download(Request $request, Event $event, EventRegistration $registration)
    {
        $this->authorizeAccess($event, $registration);

        $certificateReady = $this->isCertificateReady($event);
        // Allow forced preview in local environment for testing (e.g. ?force=1)
        $force = app()->environment('local') && $request->boolean('force');
        if(!$certificateReady && !$force){
            return redirect()->route('events.ticket', $event)->with('info','Sertifikat belum tersedia. (H+4)');
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
        $data = [
            'event' => $event,
            'registration' => $registration,
            'user' => $registration->user,
            'issuedAt' => $registration->certificate_issued_at ?? now(),
            'certificateNumber' => $certificateNumber,
        ];

        $html = view('events.certificate-pdf', $data)->render();
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $filename = 'sertifikat-'.$event->id.'-'.$registration->id.'.pdf';

        $inline = $request->boolean('inline');
        $disposition = $inline ? 'inline' : 'attachment';
        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition.'; filename="'.$filename.'"'
        ]);
    }

    private function isCertificateReady(Event $event): bool
    {
        if(!$event->event_date) return false;
        return now()->greaterThanOrEqualTo($event->event_date->copy()->addDays(4));
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
}
