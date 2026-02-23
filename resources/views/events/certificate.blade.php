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
            background: #f1f5f9; 
            font-family: 'Inter', sans-serif; 
            padding-top: 120px; /* Increased spacer for fixed premium navbar */
            margin: 0;
        }
        .preview-wrapper { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        
        .paper-container {
            width: 100%;
            aspect-ratio: 1.414 / 1;
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin: 0 auto;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
            /* Scale logic */
            --base-width: 1122; /* 29.7cm at 96dpi approx */
        }

        .cert-scaler {
            transform-origin: top left;
            width: 29.7cm;
            height: 21cm;
        }

        /* Responsive scaling */
        @media (min-width: 1200px) { .cert-scaler { transform: scale(1.01); } }
        @media (max-width: 1199px) { .cert-scaler { transform: scale(0.85); } }
        @media (max-width: 991px) { .cert-scaler { transform: scale(0.6); } }
        @media (max-width: 767px) { .cert-scaler { transform: scale(0.4); } }
        @media (max-width: 480px) { .cert-scaler { transform: scale(0.28); } }
        
        .text-navy { color: #1e1b4b; }
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
                        <li class="breadcrumb-item"><a href="{{ route('profile.events') }}" class="text-decoration-none">Riwayat</a></li>
                        <li class="breadcrumb-item active">Sertifikat</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-0 text-navy">E-Certificate Preview</h4>
            </div>
            <div class="d-flex gap-3">
                @if($certificateReady)
                    <a href="{{ route('certificates.download', [$event, $registration]) }}" class="btn btn-primary px-4 shadow-sm" target="_blank">
                        <i class="bi bi-download me-2"></i> Download PDF
                    </a>
                @else
                    <button class="btn btn-secondary px-4 shadow-sm" disabled>
                        <i class="bi bi-clock me-2"></i> Tersedia H+3 ({{ $event->event_date?->copy()->addDays(3)->format('d M') }})
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
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div>
                    <strong>Sertifikat sedang dalam proses validasi.</strong><br>
                    Anda dapat melihat preview di bawah ini. Tombol download akan aktif otomatis setelah 3 hari dari tanggal event.
                </div>
            </div>
        @endif

        <!-- The actual certificate render -->
        <div class="paper-container mb-5">
            <div class="cert-scaler">
                @include('events.certificate-pdf', ['is_preview' => true])
            </div>
        </div>
    </div>

    @include('partials.footer-before-login')
</body>
</html>
