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
            aspect-ratio: 1.414 / 1;
            background: white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.08);
            margin: 0 auto;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            border: 1px solid #e2e8f0;
        }

        .cert-scaler {
            transform-origin: top left;
            width: 29.7cm;
            height: 21cm;
            position: absolute;
            top: 0;
            left: 0;
        }

        /* Responsive scaling for the 29.7cm x 21cm virtual paper */
        @media (min-width: 1100px) { .cert-scaler { transform: scale(0.96); } }
        @media (max-width: 1099px) { .cert-scaler { transform: scale(0.85); } }
        @media (max-width: 991px) { .cert-scaler { transform: scale(0.72); } }
        @media (max-width: 767px) { .cert-scaler { transform: scale(0.5); } }
        @media (max-width: 576px) { .cert-scaler { transform: scale(0.35); } }
        @media (max-width: 400px) { .cert-scaler { transform: scale(0.28); } }
        
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
            <div class="cert-scaler">
                @include('events.certificate-pdf', ['is_preview' => true])
            </div>
        </div>
    </div>

    @include('partials.footer-before-login')
</body>
</html>
