<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {{ $event->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { 
            background: #f8fafc; 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            padding-top: 100px;
            margin: 0;
        }
        .preview-wrapper { max-width: 1100px; margin: 0 auto; padding: 2.5rem 1.25rem; }
        
        .paper-container {
            width: 100%;
            max-width: 1020px;
            margin: 0 auto 3rem;
            position: relative;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            background: white;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .cert-aspect {
            width: 100%;
            padding-top: 62.96%; /* 642 / 1020 * 100 */
            position: relative;
            overflow: hidden;
        }

        .cert-scaler {
            position: absolute;
            top: 0;
            left: 0;
            width: 1020px;
            height: 642px;
            transform-origin: top left;
        }
        
        .text-navy { color: #1e1b4b; }
        .breadcrumb-item a { color: #64748b; font-weight: 500; }
        .breadcrumb-item.active { color: #1e1b4b; font-weight: 700; }
        
        .btn-download {
            background: #fbbf24;
            color: #000;
            border: none;
            font-weight: 800;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            box-shadow: 0 10px 15px rgba(251, 191, 36, 0.2);
            transition: all 0.3s;
        }
        .btn-download:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(251, 191, 36, 0.3);
            color: #000;
        }
    </style>
</head>
<body>
    @include('partials.navbar-after-login')

    <div class="preview-wrapper">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <nav aria-label="breadcrumb" class="d-none d-md-block">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('profile.history') }}" class="text-decoration-none">Riwayat</a></li>
                        <li class="breadcrumb-item active">Sertifikat</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-navy">E-Certificate Preview</h4>
            </div>
            <div class="d-flex gap-3">
                @if($certificateReady)
                    <a href="{{ route('certificates.download', [$event, $registration]) }}" class="btn-download px-4" target="_blank">
                        <i class="bi bi-download me-2"></i> Download
                    </a>
                @else
                    <button class="btn btn-secondary px-4 shadow-sm" disabled>
                        <i class="bi bi-clock me-2"></i> Belum Tersedia
                    </button>
                    @if(app()->environment('local') || Auth::user()->role === 'admin')
                    <a href="{{ route('certificates.download', [$event, $registration]) }}?force=1" class="btn btn-outline-primary shadow-sm" target="_blank">
                        <i class="bi bi-bug me-2"></i> Force Download
                    </a>
                    @endif
                @endif
            </div>
        </div>

        @if(!$certificateReady)
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div>
                    <strong>Sertifikat sedang disiapkan.</strong><br>
                    Sertifikat akan tersedia segera setelah acara selesai dan Anda telah mengisi feedback & rating.
                </div>
            </div>
        @endif

        <!-- The actual certificate render -->
        <div class="paper-container mb-5">
            <div class="cert-aspect">
                <div class="cert-scaler" id="certScaler">
                    @php
                        $isLomba = $isLomba ?? (strtolower(trim($event->jenis ?? '')) === 'lomba');
                        $isLolos = $isLolos ?? (strtolower(trim($registration->submission_status ?? '')) === 'lolos');
                        $isMenang = $isMenang ?? ((bool) ($registration->is_winner ?? false));
                        if (!$isMenang && !empty($event->certificate_winner_ids) && is_array($event->certificate_winner_ids)) {
                            $isMenang = in_array((int)($registration->id ?? 0), array_map('intval', $event->certificate_winner_ids), true);
                        }
                        $winnerTitle = $winnerTitle ?? ($registration->winner_title ?? 'Pemenang');

                        $activeCustomTpl = $customTemplate ?? (
                            ($isMenang && !empty($event->certificate_custom_template_pemenang))
                                ? $event->certificate_custom_template_pemenang
                                : (($isLomba && !$isLolos && !empty($event->certificate_custom_template_tidak_lolos))
                                    ? $event->certificate_custom_template_tidak_lolos
                                    : $event->certificate_custom_template)
                        );
                    @endphp
                    @if(!empty($activeCustomTpl))
                        @include('events.certificate-custom', ['is_preview' => true, 'customTemplate' => $activeCustomTpl, 'winnerTitle' => $winnerTitle])
                    @else
                        @include('events.certificate-pdf', ['is_preview' => true, 'template' => $template, 'isLomba' => $isLomba, 'isLolos' => $isLolos, 'isMenang' => $isMenang, 'winnerTitle' => $winnerTitle])
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer-before-login')
    <script>
        function scaleCert() {
            const scaler = document.getElementById('certScaler');
            const container = scaler ? scaler.closest('.paper-container') : null;
            if (!scaler || !container) return;
            const containerW = container.offsetWidth;
            const certNaturalW = scaler.offsetWidth;
            if (certNaturalW > 0) {
                const scale = containerW / certNaturalW;
                scaler.style.transform = 'scale(' + scale + ')';
            }
        }
        document.addEventListener('DOMContentLoaded', scaleCert);
        window.addEventListener('resize', scaleCert);
    </script>
</body>
</html>
