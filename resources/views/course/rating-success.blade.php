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
            padding: 2rem 1.5rem;
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
            padding: 2rem 0;
        }

        .congrats-title {
            color: #2563eb;
            font-size: 2.25rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
        }

        .congrats-text {
            color: #1e293b;
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.6;
            max-width: 700px;
            margin: 0 auto 3rem;
        }

        .certificate-preview {
            background: white;
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 3rem;
            display: inline-block;
            max-width: 100%;
        }

        .certificate-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }

        .action-container {
            display: flex;
            justify-content: flex-end;
            margin-top: 2rem;
        }

        .btn-print {
            background: #fbbf24;
            color: #000;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(251, 191, 36, 0.2);
        }

        .btn-print:hover {
            background: #f59e0b;
            transform: translateY(-1px);
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

            <div class="certificate-preview">
                <img src="{{ route('certificate.view', ['type' => 'course', 'id' => $enrollment->id]) }}" alt="Sertifikat {{ $course->name }}">
            </div>

            <div class="action-container">
                <a href="{{ route('certificate.download', ['type' => 'course', 'id' => $enrollment->id]) }}" class="btn-print">
                    Cetak Sertifikat
                </a>
            </div>
        </div>
    </div>

    @include('partials.footer-after-login')
</body>
</html>
