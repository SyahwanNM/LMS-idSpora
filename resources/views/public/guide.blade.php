@extends('layouts.app')

@section('title', 'Panduan Platform - idSPORA')

@section('content')
<!-- Include the standard navbar -->
@include('partials.navbar-after-login')

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Purple & Yellow Brand Palette */
            --primary: #6D28D9; /* Deep Purple */
            --primary-light: #8B5CF6;
            --primary-subtle: #F5F3FF;
            --secondary: #FBBD23; /* Vibrant Yellow */
            --secondary-light: #FDE68A;
            --navy: #1e293b;
            --text-main: #334155;
            --text-muted: #64748b;
            --bg-surface: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-surface);
            padding-top: 20px; /* Slight offset for better spacing behind fixed navbar */
        }

        /* Hero Section */
        .guide-hero {
            background: linear-gradient(135deg, #f8fafc 0%, #F5F3FF 100%);
            padding: clamp(100px, 15vh, 160px) 0 clamp(40px, 8vh, 80px); /* Responsive padding for laptops */
            position: relative;
            overflow: hidden;
        }

        .hero-blob {
            position: absolute;
            background: linear-gradient(180deg, rgba(109, 40, 217, 0.08) 0%, rgba(251, 189, 35, 0.08) 100%);
            filter: blur(80px);
            z-index: 0;
            border-radius: 50%;
        }
        .hero-blob-1 { top: -150px; right: -100px; width: 500px; height: 500px; }
        .hero-blob-2 { bottom: -100px; left: -150px; width: 400px; height: 400px; }

        .hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-block;
            background: white;
            color: var(--primary);
            font-weight: 700;
            font-size: 0.85rem;
            padding: 8px 20px;
            border-radius: 50px;
            border: 1px solid var(--primary-subtle);
            box-shadow: 0 4px 15px rgba(109, 40, 217, 0.1);
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem); /* Scalable title */
            font-weight: 800;
            color: var(--navy);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.15rem;
            color: var(--text-muted);
            line-height: 1.7;
        }

        /* Content Section */
        .content-section {
            padding: 60px 0 80px;
        }

        .guide-card {
            background: white;
            border-radius: 28px;
            border: 1px solid #f1f5f9;
            padding: clamp(24px, 4vw, 48px); /* Responsive inner padding */
            margin-bottom: 32px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
        }

        .guide-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .guide-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -15px rgba(0, 0, 0, 0.1);
            border-color: var(--primary-light);
        }

        .guide-card:hover::before {
            opacity: 1;
        }

        .card-icon {
            width: 64px;
            height: 64px;
            background: var(--primary-subtle);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 20px;
        }

        .card-text {
            font-size: 1.05rem;
            color: var(--text-main);
            line-height: 1.8;
            margin-bottom: 16px;
        }

        /* Step List */
        .step-list {
            margin-top: 32px;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 24px;
            padding: 24px;
            background: var(--bg-surface);
            border-radius: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--secondary);
            transition: all 0.3s ease;
        }

        .step-item:hover {
            background: #fdfdfd;
            transform: translateX(8px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .step-number {
            flex-shrink: 0;
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.3rem;
            box-shadow: 0 8px 16px -6px rgba(109, 40, 217, 0.3);
        }

        .step-content h4 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 8px;
        }

        .step-content p {
            font-size: 0.98rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.7;
        }

        /* Points Grid */
        .points-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 28px;
        }

        .point-card {
            background: var(--bg-surface);
            padding: 24px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .point-card:hover {
            background: white;
            border-color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -10px rgba(251, 189, 35, 0.3);
        }

        .point-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--secondary) 0%, #F59E0B 100%);
            color: var(--navy);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 1.1rem;
        }

        .point-card h5 {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 8px;
        }

        .point-card p {
            font-size: 0.92rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.6;
        }

        /* CTA Section */
        .cta-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 56px;
            border-radius: 32px;
            text-align: center;
            color: white;
            box-shadow: 0 20px 50px -15px rgba(109, 40, 217, 0.4);
            position: relative;
            overflow: hidden;
        }

        .cta-card::after {
            content: '';
            position: absolute;
            top: -20%; right: -10%;
            width: 300px; height: 300px;
            background: rgba(251, 189, 35, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .cta-card h3 {
            font-size: 2.25rem;
            font-weight: 800;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
        }

        .cta-card p {
            font-size: 1.15rem;
            margin-bottom: 36px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .btn-support {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: var(--secondary);
            color: var(--navy);
            font-weight: 700;
            padding: 18px 36px;
            border-radius: 16px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 10px 25px -5px rgba(251, 189, 35, 0.4);
            position: relative;
            z-index: 1;
        }

        .btn-support:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px -5px rgba(251, 189, 35, 0.5);
            background: #fff;
            color: var(--primary);
        }

        /* Color highlighting */
        .text-purple { color: var(--primary) !important; }
        .text-yellow { color: var(--secondary) !important; }
        .bg-purple { background-color: var(--primary) !important; }
        .bg-yellow { background-color: var(--secondary) !important; }

        /* Responsive */
        @media (max-width: 768px) {
            .guide-hero { padding: 120px 0 60px; }
            .hero-title { font-size: 2.25rem; }
            .hero-subtitle { font-size: 1rem; }
            .guide-card { padding: 32px 24px; }
            .card-title { font-size: 1.5rem; }
            .cta-card { padding: 40px 24px; }
            .cta-card h3 { font-size: 1.75rem; }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .guide-card { animation: fadeInUp 0.6s ease-out backwards; }
        .guide-card:nth-child(1) { animation-delay: 0.1s; }
        .guide-card:nth-child(2) { animation-delay: 0.2s; }
        .guide-card:nth-child(3) { animation-delay: 0.3s; }
    </style>
</head>
<body>

    <!-- Hero Section -->
    <section class="guide-hero">
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="container">
            <div class="hero-content">
                <span class="hero-badge">📚 Panduan Platform</span>
                <h1 class="hero-title">Optimalkan <span class="text-purple">Belajarmu</span> di idSPORA</h1>
                <p class="hero-subtitle">
                    Temukan cara terbaik untuk menjelajahi kursus, mengikuti event eksklusif, 
                    dan mengumpulkan point untuk reward menarik di masa depan.
                </p>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="content-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <!-- About idSPORA -->
                    <div class="guide-card">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z"/>
                                <path d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319z"/>
                            </svg>
                        </div>
                        <h2 class="card-title">Tentang Platform Kami</h2>
                        <p class="card-text">
                            <strong>idSPORA</strong> adalah manifestasi dari visi untuk menciptakan ekosistem pembelajaran yang relevan dengan industri. 
                            Kami percaya bahwa setiap individu memiliki potensi besar yang bisa diasah melalui bimbingan praktisi yang tepat.
                        </p>
                        <p class="card-text">
                            Dengan kombinasi kurikulum yang <span class="text-purple fw-bold">Up-to-Date</span> dan komunitas yang suportif, kami membangun jembatan bagi Anda untuk meraih karir impian di era digital.
                        </p>
                    </div>

                    <!-- Hub Menggunakan Platform -->
                    <div class="guide-card">
                        <div class="card-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                            </svg>
                        </div>
                        <h2 class="card-title">Langkah Memulai</h2>
                        
                        <div class="step-list">
                            <div class="step-item">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h4>Eksplorasi Katalog</h4>
                                    <p>Gunakan menu <strong>Courses</strong> untuk melihat kelas fundamental hingga advance, atau <strong>Events</strong> untuk webinar terbaru yang sedang hits.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h4>Registrasi & Verifikasi</h4>
                                    <p>Daftar pada kelas pilihan Anda. Gunakan metode pembayaran yang fleksibel dan tim admin kami akan melakukan verifikasi secara instan.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h4>Belajar Secara Mandiri</h4>
                                    <p>Masuk ke <strong>Dashboard</strong> untuk mengakses video materi, handout PDF, dan kerjakan quiz untuk menguji pemahaman Anda.</p>
                                </div>
                            </div>

                            <div class="step-item">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h4>Sertifikasi & Point</h4>
                                    <p>Unduh sertifikat digital Anda setelah lulus dan kumpulkan point yang terakumulasi otomatis ke profil Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sistem Point -->
                    <div class="guide-card">
                        <div class="card-icon" style="background: rgba(251, 189, 35, 0.1); color: var(--secondary);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                            </svg>
                        </div>
                        <h2 class="card-title">Sistem Point & Reward</h2>
                        <p class="card-text">
                            Kami menghargai setiap menit waktu yang Anda investasikan untuk belajar. Itulah mengapa <span class="text-yellow fw-bold">idSPORA Points</span> hadir sebagai apresiasi nyata.
                        </p>
                        
                        <div class="points-grid">
                            <div class="point-card">
                                <div class="point-icon">⭐</div>
                                <h5>Quiz Master</h5>
                                <p>Selesaikan quiz modul dengan hasil sempurna untuk mendapatkan point maksimal.</p>
                            </div>
                            <div class="point-card">
                                <div class="point-icon">🎫</div>
                                <h5>Event Attendee</h5>
                                <p>Hadir tepat waktu di webinar dan lakukan scan kehadiran untuk klaim point Anda.</p>
                            </div>
                            <div class="point-card">
                                <div class="point-icon">🎓</div>
                                <h5>Course Completion</h5>
                                <p>Penyelesaian satu kursus utuh memberikan point bonus yang signifikan ke rank profil Anda.</p>
                            </div>
                        </div>
                        
                        <p class="card-text mt-4" style="font-weight: 600; color: var(--navy);">
                            🚀 Semakin tinggi point Anda, semakin besar peluang mendapatkan akses kelas eksklusif dan merchandise idSpora!
                        </p>
                    </div>

                    <!-- Dukungan -->
                    <div class="cta-card">
                        <h3>Masih ada pertanyaan?</h3>
                        <p>Jangan ragu untuk menghubungi kami jika Anda menemui kendala teknis atau memiliki saran untuk platform.</p>
                        <a href="{{ route('public.support') }}" class="btn-support">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                                <path d="M8 1a5 5 0 0 0-5 5v1h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a6 6 0 1 1 12 0v6a2.5 2.5 0 0 1-2.5 2.5H9.366a1 1 0 0 1-.866.5h-1a1 1 0 1 1 0-2h1a1 1 0 0 1 .866.5H11.5A1.5 1.5 0 0 0 13 12h-1a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1h1V6a5 5 0 0 0-5-5z"/>
                            </svg>
                            Hubungi Kami Sekarang
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

</body>
</html>
@endsection
