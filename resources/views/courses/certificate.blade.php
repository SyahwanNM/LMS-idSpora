<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat - {{ $course->name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            padding-top: 120px;
            margin: 0;
            min-height: 100vh;
            overflow-y: auto;
        }

        html {
            overflow-y: auto;
        }

        .main-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 1rem 6rem;
        }

        /* Step Indicator */
        .step-container {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 3rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .step-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
        }
        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.875rem;
        }
        .step-item.active .step-circle {
            background: #fbbf24;
            color: #000;
        }
        .step-line {
            width: 80px;
            height: 1px;
            background: #e2e8f0;
        }

        /* Congrats */
        .success-card { text-align: center; }
        .congrats-title {
            color: #2563eb;
            font-size: 2.25rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }
        .congrats-text {
            color: #1a1b1e;
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto 3rem;
        }

        /* Certificate preview */
        .paper-container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto 3rem;
            position: relative;
            border-radius: 4px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            background: white;
            overflow: hidden;
        }
        .cert-aspect {
            width: 100%;
            padding-top: 62.96%; /* 170/270 * 100 = certificate height/width ratio */
            position: relative;
            overflow: hidden;
        }
        .cert-scaler {
            position: absolute;
            top: 0;
            left: 0;
            /* width matches .certificate-page: 270mm @ 96dpi ≈ 1020px */
            width: 1020px;
            height: 642px; /* 170mm @ 96dpi ≈ 642px */
            transform-origin: top left;
        }

        /* Action buttons */
        .action-area {
            display: flex;
            justify-content: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }
        .btn-cetak {
            background: #fbbf24;
            color: #000 !important;
            padding: 0.85rem 2.5rem;
            border-radius: 12px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(251,191,36,0.3);
            font-size: 1rem;
            border: none;
            display: inline-flex;
            align-items: center;
        }
        .btn-cetak:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            color: #000 !important;
        }
        .btn-back {
            background: #fff;
            color: #1e293b !important;
            padding: 0.85rem 2.5rem;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
        }
        .btn-back:hover {
            background: #f8fafc;
            color: #0f172a !important;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .congrats-title { font-size: 1.5rem; }
            .btn-cetak, .btn-back { width: 100%; padding: 0.75rem 1.5rem; }
        }
    </style>
</head>
<body>
    @include('partials.navbar-after-login')

    <div class="main-container">
        {{-- Step Indicator --}}
        <div class="step-container">
            <div class="step-item">
                <span class="step-circle">1</span>
                Give Rating Class
            </div>
            <div class="step-line"></div>
            <div class="step-item active">
                <span class="step-circle">2</span>
                Print Certificate
            </div>
        </div>

        <div class="success-card">
            <h1 class="congrats-title">Congratulations on completing all Modules!</h1>
            <p class="congrats-text">
                We are very proud of your dedication and hard work in completing all the course modules.
                This is a remarkable achievement and a testament to your commitment to learning and self-development. Keep up the good work and achieve success!
            </p>

            {{-- Certificate Preview --}}
            <div class="paper-container">
                <div class="cert-aspect">
                    <div class="cert-scaler" id="certScaler">
                        @include('courses.certificate-pdf', array_merge(
                            compact('course', 'user', 'enrollment', 'issuedAt', 'certificateNumber', 'logosBase64', 'signaturesBase64'),
                            ['is_preview' => true]
                        ))
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="action-area">
                <a href="{{ route('dashboard') }}" class="btn-back">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
                @if($certificateReady)
                    <a href="{{ route('course.certificates.download', [$course->id, $enrollment->id]) }}"
                       class="btn-cetak" target="_blank">
                        <i class="bi bi-download me-2"></i>Download PDF
                    </a>
                @else
                    <button class="btn-cetak" style="opacity:.6; cursor:not-allowed;" disabled>
                        <i class="bi bi-clock me-2"></i>Belum Tersedia
                    </button>
                @endif
            </div>
        </div>
    </div>

    @include('partials.footer-after-login')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function scaleCert() {
            const scaler = document.getElementById('certScaler');
            if (!scaler) return;
            const container = scaler.closest('.cert-aspect');
            if (!container) return;
            const containerW = container.offsetWidth;
            // cert-scaler natural width = 270mm @ 96dpi = 1020px
            const certNaturalW = 1020;
            const scale = containerW / certNaturalW;
            scaler.style.transform = 'scale(' + scale + ')';
        }
        document.addEventListener('DOMContentLoaded', scaleCert);
        window.addEventListener('resize', scaleCert);
    </script>
</body>
</html>