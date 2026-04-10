<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>Cetak Sertifikat - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Override ringan agar layout mirip screenshot dan tidak mengubah page lain */
        body { background: #f8fafc; font-family: 'Poppins', sans-serif; }
        .certificate-page { max-width: 1050px; margin: 0 auto; padding: 32px 16px 48px; }

        .steps {
            max-width: 720px;
            margin: 12px auto 36px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            border-radius: 14px;
            padding: 16px 18px;
        }
        .steps .step { font-weight: 600; color: #111827; }
        .steps .circle { background: #e5e7eb; color: #111827; }
        .steps .active .circle { background: #FFD33B; }

        .certificate-title { text-align: center; font-size: 32px; font-weight: 800; color: #2f55d4; margin: 0 0 10px; }
        .certificate-desc { max-width: 760px; margin: 0 auto; text-align: center; color: #111827; font-weight: 600; line-height: 1.55; }

        .certificate img {
            display: block;
            margin: 22px auto 0;
            width: 100%;
            max-width: 860px;
            border-radius: 10px;
            background: #fff;
        }

        .btn-area { margin-top: 28px; display: flex; justify-content: flex-end; }
        .btn-area button { min-width: 160px; background: #FFD33B; }

        @media print {
            /* fokus hanya cetaknya sertifikat */
            nav, header, footer { display: none !important; }
            .steps, .certificate-title, .certificate-desc, .btn-area { display: none !important; }
            .certificate-page { padding: 0 !important; }
            .cert-preview-wrap { border: 0 !important; padding: 0 !important; }
        }
    </style>
</head>

<body>
    @include('partials.navbar-after-login')

    <div class="certificate-page">
        <div class="steps" aria-label="Langkah penilaian dan sertifikat">
            <div class="step">
                <span class="circle">1</span>
                Beri Penilaian Kelas
            </div>
            <div class="line"></div>
            <div class="step active">
                <span class="circle">2</span>
                Cetak Sertifikat
            </div>
        </div>

        @if(isset($certificateReady) && $certificateReady)
            <h1 class="certificate-title">Selamat! Anda telah menyelesaikan semua modul</h1>
            <p class="certificate-desc">
                Kami sangat bangga atas dedikasi dan kerja keras Anda dalam menyelesaikan semua modul kursus. Ini adalah pencapaian yang luar biasa dan merupakan bukti komitmen Anda terhadap pembelajaran dan pengembangan diri. Teruslah berkarya dan raih sukses!
            </p>
        @else
            <h1 class="certificate-title">Sertifikat belum tersedia</h1>
            <p class="certificate-desc">
                @if(isset($progressPercent))
                    Progress Anda saat ini: <strong>{{ (int) $progressPercent }}%</strong>. Selesaikan semua modul terlebih dahulu, lalu kembali ke halaman ini.
                @else
                    Selesaikan semua modul terlebih dahulu, lalu kembali ke halaman ini.
                @endif
            </p>
        @endif

        @if(isset($certificateReady) && $certificateReady)
            <div class="certificate" aria-label="Pratinjau sertifikat">
                <div class="cert-preview-wrap" style="margin-top: 22px; background: #fff; border-radius: 10px; border: 1px solid #e5e7eb; padding: 14px; overflow: auto;">
                    <div style="width: 1122px; height: 794px; background: white; border-radius: 6px; overflow: hidden; margin: 0 auto;">
                        <div style="transform-origin: top left; transform: scale(.9); width: 29.7cm; height: 21cm;">
                            @include('courses.certificate-pdf', ['is_preview' => true])
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-area">
                <button type="button" onclick="window.print()">Cetak Sertifikat</button>
            </div>
        @else
            <div style="margin-top: 22px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 14px; text-align: center; color: #111827;">
                <div style="font-weight: 800; font-size: 18px;">Sertifikat belum tersedia</div>
                <div style="margin-top: 6px; color: #6b7280;">Selesaikan semua modul terlebih dahulu, lalu kembali ke halaman ini.</div>
            </div>

            <div class="btn-area">
                <button type="button" disabled style="opacity: .65; cursor: not-allowed;">Cetak Sertifikat</button>
            </div>
        @endif
    </div>

    @include('partials.footer-after-login')
</body>
</html>