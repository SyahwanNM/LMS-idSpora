<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cetak Sertifikat - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            padding-top: 80px;
        }

        .main-container {
            max-width: 960px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem;
        }

        /* Card Base */
        .feedback-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        /* Step Indicator */
        .step-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
        }

        .step-item.inactive {
            color: #94a3b8;
        }

        .step-num {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e2e8f0;
            color: #64748b;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .step-item.active .step-num {
            background: #fbbf24;
            color: #000;
        }

        .step-line {
            width: 80px;
            height: 2px;
            background: #e2e8f0;
        }

        .congrats-title {
            color: #1e293b;
            font-size: 2.25rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }

        .congrats-text {
            color: #64748b;
            font-size: 1.1rem;
            font-weight: 500;
            line-height: 1.7;
            max-width: 800px;
            margin: 0 auto 3rem;
        }

        .certificate-preview-box {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
            margin-bottom: 3.5rem;
            display: block;
            width: 100%;
            max-width: 850px;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #e2e8f0;
        }

        .certificate-preview-content {
            position: relative;
            width: 100%;
            padding-top: 70.7%; /* A4 Aspect Ratio Landscape */
            background: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
        }

        .certificate-preview-content iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .btn-cetak {
            background: #fbbf24;
            color: #000 !important;
            padding: 1.25rem 3rem;
            border-radius: 50px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 10px 20px rgba(251, 191, 36, 0.2);
            font-size: 1.1rem;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cetak:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(251, 191, 36, 0.3);
        }

        .btn-back {
            background: #fff;
            color: #1e293b !important;
            padding: 1.25rem 3rem;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            border: 1.5px solid #e2e8f0;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .congrats-title {
                font-size: 1.75rem;
            }
            .step-wrapper {
                flex-direction: column;
                gap: 1rem;
            }
            .step-line {
                display: none;
            }
            .action-area {
                flex-direction: column-reverse;
                gap: 1rem;
            }
            .btn-cetak, .btn-back {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")

    <div class="main-container">
        <!-- Step Indicator Card -->
        <div class="feedback-card">
            <div class="step-wrapper">
                <div class="step-item">
                    <span class="step-num">1</span>
                    <span>Beri Penilaian Kelas</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item active">
                    <span class="step-num">2</span>
                    <span>Cetak Sertifikat</span>
                </div>
            </div>
        </div>

        <div class="text-center">
            <h1 class="congrats-title">Selamat Anda telah menyelesaikan semua Modul!</h1>
            
            <p class="congrats-text">
                Kami sangat bangga atas dedikasi dan kerja keras Anda dalam menyelesaikan semua modul kursus. 
                Ini adalah pencapaian yang luar biasa dan merupakan bukti komitmen Anda terhadap pengembangan diri.
            </p>

            <div class="certificate-preview-box">
                <div class="certificate-preview-content">
                    <iframe src="{{ route('course.certificates.preview', [$course->id, $enrollment->id]) }}" 
                            title="Pratinjau Sertifikat">
                    </iframe>
                </div>
            </div>

            <div class="flex flex-wrap justify-center gap-4 mb-12 action-area">
                <a href="{{ route('dashboard') }}" class="btn-back">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                </a>
                <a href="{{ route('course.certificates.download', [$course->id, $enrollment->id]) }}" class="btn-cetak">
                    <i class="bi bi-printer me-2"></i>Cetak Sertifikat
                </a>
            </div>
        </div>
    </div>

    @include('partials.footer-after-login')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>