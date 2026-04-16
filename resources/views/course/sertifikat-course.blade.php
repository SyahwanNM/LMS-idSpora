<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kursus - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            padding-top: 80px;
        }

        .main-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 1rem 6rem;
            min-height: calc(100vh - 80px);
        }

        /* Step Indicator Styles */
        .step-container {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 4rem;
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
            transition: all 0.3s ease;
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

        .success-card {
            text-align: center;
        }

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
            margin: 0 auto 3.5rem;
        }

        .certificate-preview-box {
            background: white;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            margin-bottom: 3.5rem;
            display: block;
            width: 100%;
            max-width: 850px;
            margin-left: auto;
            margin-right: auto;
            border: 1px solid #f1f5f9;
        }

        .certificate-preview-content {
            position: relative;
            width: 100%;
            padding-top: 70.7%; /* A4 Aspect Ratio Landscape */
            background: #f8fafc;
            border-radius: 4px;
            overflow: hidden;
        }

        .certificate-preview-content iframe,
        .certificate-preview-content img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .action-area {
            display: flex;
            justify-content: center;
            gap: 1.25rem;
            margin-top: 2rem;
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
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
            font-size: 1rem;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cetak:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(251, 191, 36, 0.4);
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
            justify-content: center;
        }

        .btn-back:hover {
            background: #f8fafc;
            color: #0f172a !important;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .congrats-title {
                font-size: 1.5rem;
            }
            .certificate-preview-box {
                padding: 8px;
            }
            .btn-cetak, .btn-back {
                width: 100%;
                padding: 0.75rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")

    <div class="main-container">
        <!-- Step Indicator -->
        <div class="step-container">
            <div class="step-item">
                <span class="step-circle">1</span>
                Beri Penilaian Kelas
            </div>
            <div class="step-line"></div>
            <div class="step-item active">
                <span class="step-circle">2</span>
                Cetak Sertifikat
            </div>
        </div>

        <div class="success-card">
            <h1 class="congrats-title">Selamat Anda telah menyelesaikan semua Modul!</h1>
            
            <p class="congrats-text">
                Kami sangat bangga atas dedikasi dan kerja keras Anda dalam menyelesaikan semua modul kursus. 
                Ini adalah pencapaian yang luar biasa dan merupakan bukti komitmen Anda terhadap pembelajaran 
                dan pengembangan diri. Teruslah berkarya dan raih sukses!
            </p>

            <div class="certificate-preview-box">
                <div class="certificate-preview-content">
                    <iframe src="{{ route('course.certificates.preview', [$course->id, $enrollment->id]) }}" 
                            title="Pratinjau Sertifikat"
                            onerror="this.style.display='none'; document.getElementById('cert-fallback').style.display='flex';">
                    </iframe>
                    <div id="cert-fallback" style="display: none; height: 100%; width: 100%; align-items: center; justify-content: center; flex-direction: column; background: #f1f5f9;">
                        <i class="bi bi-file-earmark-pdf text-primary mb-3" style="font-size: 4rem;"></i>
                        <p class="text-muted fw-bold">Pratinjau Sertifikat Siap</p>
                    </div>
                </div>
            </div>

            <div class="action-area">
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