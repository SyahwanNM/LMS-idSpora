<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kursus - idSPORA</title>
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
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem 5rem;
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
            display: inline-block;
            max-width: 100%;
            border: 1px solid #f1f5f9;
        }

        .certificate-preview-box img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .action-area {
            display: flex;
            justify-content: flex-end;
            margin-top: 1rem;
        }

        .btn-cetak {
            background: #fbbf24;
            color: #000;
            padding: 0.75rem 2.25rem;
            border-radius: 10px;
            font-weight: 800;
            text-decoration: none;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(251, 191, 36, 0.2);
            font-size: 0.95rem;
            border: none;
        }

        .btn-cetak:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(251, 191, 36, 0.3);
            color: #000;
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
            <h1 class="congrats-title text-blue-600">Selamat Anda telah menyelesaikan semua Modul!</h1>
            
            <p class="congrats-text">
                Kami sangat bangga atas dedikasi dan kerja keras Anda dalam menyelesaikan semua modul kursus. 
                Ini adalah pencapaian yang luar biasa dan merupakan bukti komitmen Anda terhadap pembelajaran 
                dan pengembangan diri. Teruslah berkarya dan raih sukses!
            </p>

            <div class="certificate-preview-box">
                @php
                    // For preview, we display the PDF component as an image using a route that returns PNG or similar if available,
                    // but here we'll use a link to a specialized preview route or simply the download route with inline=true.
                    // Note: If the platform doesn't have a PNG previewer, we can use an icon or a placeholder with the real download.
                @endphp
                <img src="{{ route('course.certificates.download', [$course->id, $enrollment->id, 'inline' => 1]) }}" 
                     alt="Sertifikat {{ $course->name }}"
                     onerror="this.src='https://placehold.co/800x565/white/2563eb?text=Pratinjau+Sertifikat+{{ urlencode($course->name) }}';">
            </div>

            <div class="action-area">
                <a href="{{ route('course.certificates.download', [$course->id, $enrollment->id]) }}" class="btn-cetak">
                    Cetak Sertifikat
                </a>
            </div>
        </div>
    </div>

    @include('partials.footer-after-login')
</body>
</html>