<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CRM\CertificateController;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventCertificateController extends Controller
{
    /**
     * GET /api/events/{event}/certificate
     * Returns certificate info for the authenticated user.
     */
    public function show(Request $request, Event $event)
    {
        $user = $request->user();

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$registration) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak terdaftar pada event ini',
            ], 403);
        }

        $isReady = $this->isCertificateReady($event, $registration);

        if (!$isReady) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sertifikat belum tersedia. Event belum selesai atau kehadiran belum dikonfirmasi.',
            ], 422);
        }

        // Auto-generate certificate number on first access
        if (!$registration->certificate_number) {
            $registration->update([
                'certificate_number'   => CertificateController::generateCertificateNumber($event, $registration),
                'certificate_issued_at' => now(),
            ]);
            $registration->refresh();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Sertifikat tersedia',
            'data'    => $this->formatCertificate($event, $registration),
        ]);
    }

    /**
     * GET /api/me/event-certificates
     * List all event certificates owned by the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $registrations = EventRegistration::with('event')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('certificate_number')
            ->latest('certificate_issued_at')
            ->get();

        $data = $registrations->map(fn($reg) => [
            'event_id'             => $reg->event_id,
            'event_title'          => $reg->event?->title,
            'event_date'           => $reg->event?->event_date?->toDateString(),
            'certificate_number'   => $reg->certificate_number,
            'certificate_issued_at' => $reg->certificate_issued_at?->toISOString(),
            'download_url'         => route('api.events.certificate.download', [
                'event'        => $reg->event_id,
                'registration' => $reg->id,
            ]),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Daftar sertifikat event',
            'data'    => $data,
        ]);
    }

    /**
     * GET /api/events/{event}/certificate/download
     * Returns a PDF download of the certificate.
     */
    public function download(Request $request, Event $event)
    {
        $user = $request->user();

        $registration = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if (!$registration) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Anda tidak terdaftar pada event ini',
            ], 403);
        }

        $isReady = $this->isCertificateReady($event, $registration);

        if (!$isReady) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sertifikat belum tersedia.',
            ], 422);
        }

        if (!$registration->certificate_number) {
            $registration->update([
                'certificate_number'   => CertificateController::generateCertificateNumber($event, $registration),
                'certificate_issued_at' => now(),
            ]);
            $registration->refresh();
        }

        // Delegate PDF generation to the existing CRM controller
        return app(CertificateController::class)->download($request, $event, $registration->id);
    }

    // -------------------------------------------------------------------------

    private function isCertificateReady(Event $event, EventRegistration $registration): bool
    {
        if ($registration->certificate_issued_at) {
            return true;
        }
        return $event->isFinished();
    }

    private function formatCertificate(Event $event, EventRegistration $registration): array
    {
        return [
            'event_id'             => $event->id,
            'event_title'          => $event->title,
            'event_date'           => $event->event_date?->toDateString(),
            'registration_code'    => $registration->registration_code,
            'certificate_number'   => $registration->certificate_number,
            'certificate_issued_at' => $registration->certificate_issued_at?->toISOString(),
            'recipient_name'       => $registration->user?->name ?? $registration->user()->value('name'),
            'download_url'         => route('api.events.certificate.download', [
                'event'        => $event->id,
                'registration' => $registration->id,
            ]),
        ];
    }
}
