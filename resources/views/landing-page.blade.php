<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>idSpora - Tingkatkan Keahlian Web Event & Webinar</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .hero-section {
            background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%);
            position: relative;
            overflow: hidden;
            padding: clamp(80px, 12vh, 140px) 0 clamp(40px, 8vh, 100px);
        }
        .hero-blob {
            position: absolute;
            background: linear-gradient(180deg, rgba(79, 70, 229, 0.15) 0%, rgba(245, 158, 11, 0.15) 100%);
            filter: blur(80px);
            z-index: 0;
            border-radius: 50%;
        }
        .hero-blob-1 { top: -100px; right: -100px; width: 600px; height: 600px; }
        .hero-blob-2 { bottom: -100px; left: -100px; width: 400px; height: 400px; }
        
        .stat-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.6);
            padding: 24px;
            border-radius: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-number { font-size: 2.5rem; font-weight: 800; color: var(--primary); display: block; line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 0.9rem; color: var(--text-muted); font-weight: 600; }

        .category-card {
            background: white;
            padding: 30px 20px;
            border-radius: 24px;
            text-align: center;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }
        .category-card:hover {
            border-color: var(--primary);
            box-shadow: 0 20px 40px -15px rgba(79, 70, 229, 0.2);
            transform: translateY(-5px);
        }
        .cat-icon {
            width: 64px; height: 64px;
            background: var(--primary-subtle);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 15px;
            color: var(--primary);
            font-size: 1.5rem;
            transition: all 0.3s;
        }
        .category-card:hover .cat-icon {
            background: var(--primary);
            color: white;
        }

        .event-card-new {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
            transition: all 0.3s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .event-card-new:hover {
            box-shadow: 0 15px 30px -10px rgba(0,0,0,0.1);
            transform: translateY(-5px);
        }
        .ec-thumb {
            height: clamp(180px, 20vh, 240px);
            position: relative;
            overflow: hidden;
        }
        .ec-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .event-card-new:hover .ec-thumb img { transform: scale(1.05); }
        .ec-date-badge {
            position: absolute;
            top: 15px; right: 15px;
            background: rgba(255,255,255,0.95);
            padding: 8px 12px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .ec-date-badge .day { display: block; font-size: 1.2rem; font-weight: 800; line-height: 1; color: var(--text-main); }
        .ec-date-badge .month { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: var(--primary); }
        
        .section-header { text-align: center; margin-bottom: 60px; max-width: 700px; margin-left: auto; margin-right: auto; }
        .section-header .sub { color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem; display: block; margin-bottom: 10px; }
        .section-header h2 { font-size: 2.5rem; font-weight: 800; color: var(--navy); margin-bottom: 15px; }
        .section-header p { color: var(--text-muted); font-size: 1.1rem; }

        /* Testimonial New */
        .testi-card-new {
            background: white;
            padding: 30px;
            border-radius: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
            height: 100%;
        }
        .testi-user { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
        .testi-avatar { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; }
        .testi-info h5 { margin: 0; font-size: 1rem; font-weight: 700; }
        .testi-info span { font-size: 0.8rem; color: var(--text-muted); }
        .quote-icon { color: var(--primary-light); font-size: 2rem; opacity: 0.3; margin-bottom: 10px; }

        /* Partner Logos */
        .partner-grid {
            display: flex; flex-wrap: wrap; justify-content: center; gap: 40px; align-items: center;
            opacity: 0.7;
        }
        .partner-logo { max-height: 40px; filter: grayscale(100%); transition: all 0.3s; opacity: 0.6; }
        .partner-logo:hover { filter: grayscale(0%); opacity: 1; transform: scale(1.05); }

        /* Feature Card Premium */
        .feature-card-premium {
            background: white;
            padding: 40px 30px;
            border-radius: 28px;
            border: 1px solid #f1f5f9;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .feature-card-premium:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px -20px rgba(0,0,0,0.1);
            border-color: var(--primary-light);
        }
        .f-icon-wrap {
            width: 56px; height: 56px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 5px;
        }

    </style>
</head>
<body class="bg-surface" style="padding-top: 80px;">
    @include('partials.navbar-before-login')

    <!-- HERO SECTION -->
    <section class="hero-section">
        <div class="hero-blob hero-blob-1"></div>
        <div class="hero-blob hero-blob-2"></div>
        <div class="container position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <span class="d-inline-block py-1 px-3 rounded-pill bg-white text-primary fw-bold border border-primary-subtle shadow-sm mb-4">
                        🚀 Platform Belajar Web Event #1 di Indonesia
                    </span>
                    <h1 class="display-3 fw-bold mb-4 text-navy" style="line-height: 1.2;">
                        Kembangkan <span class="text-gradient">Potensi Skill</span> <br>Masa Depanmu
                    </h1>
                    <p class="fs-5 text-muted mb-5" style="max-width: 500px;">
                        Gabung dengan ribuan learner lainnya dalam webinar eksklusif dan kursus interaktif. Belajar langsung dari praktisi terbaik.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="#event-section" class="btn-primary-new text-decoration-none">
                            Jelajahi Event
                        </a>
                        <a href="{{ route('courses.index') }}" class="btn-outline-new text-decoration-none">
                            Lihat Kursus
                        </a>
                    </div>
                    
                    <div class="mt-5 pt-4 border-top border-2" style="border-color: rgba(0,0,0,0.05) !important;">
                        <div class="d-flex gap-5">
                            <div>
                                <h4 class="fw-bold mb-0 text-navy">10K+</h4>
                                <span class="fs-small text-muted fw-bold">ALUMNUS</span>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-navy">500+</h4>
                                <span class="fs-small text-muted fw-bold">WEBINAR</span>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-navy">4.8/5</h4>
                                <span class="fs-small text-muted fw-bold">RATING</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <div class="position-relative">
                        <!-- Main Hero Image -->
                        <div class="position-relative z-2">
                            <img src="{{ asset('aset/lp.png') }}" onerror="this.src='{{ asset('aset/poster.png') }}'" class="img-fluid rounded-4 shadow-soft" style="transform: rotate(2deg); border: 8px solid white;" alt="Hero Image">
                        </div>
                        
                        <!-- Floating Badges -->
                        <div class="position-absolute bg-white p-3 rounded-4 shadow-lg animate-float" style="top: 10%; left: -20px; z-index: 3; width: 180px;">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-success-subtle p-2 rounded-3 text-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>
                                </div>
                                <div class="text-start">
                                    <small class="d-block text-muted" style="font-size: 10px;">Status</small>
                                    <span class="fw-bold text-navy" style="font-size: 12px;">Terverifikasi</span>
                                </div>
                            </div>
                        </div>

                        <div class="position-absolute bg-white p-3 rounded-4 shadow-lg animate-float-delayed" style="bottom: 10%; right: -20px; z-index: 3;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex">
                                    <img src="https://ui-avatars.com/api/?name=A&background=random" class="rounded-circle border border-2 border-white" width="35">
                                    <img src="https://ui-avatars.com/api/?name=B&background=random" class="rounded-circle border border-2 border-white" width="35" style="margin-left: -12px">
                                    <img src="https://ui-avatars.com/api/?name=C&background=random" class="rounded-circle border border-2 border-white" width="35" style="margin-left: -12px">
                                </div>
                                <div>
                                    <span class="fw-bold d-block text-navy is-bold" style="font-size: 14px;">1k+ Join</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    </section>
    
    <!-- ABOUT US SECTION -->
    <section class="section-padding bg-surface" id="tentang">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-5 mb-lg-0">
                    <div class="pe-lg-5">
                        <span class="sub text-primary fw-bold text-uppercase ls-1 mb-2 d-block">Tentang idSPORA</span>
                        <h2 class="display-6 fw-bold text-navy mb-4">Solusi Terpadu Menuju <span class="text-gradient">Karir Profesional</span></h2>
                        <p class="text-muted fs-5 mb-4">
                            idSPORA hadir sebagai ekosistem pembelajaran yang dirancang khusus untuk menjembatani kesenjangan antara kurikulum akademis dan kebutuhan industri modern.
                        </p>
                        <p class="text-muted mb-5">
                            Kami berfokus pada penyediaan materi yang relevan, praktis, dan langsung dibimbing oleh para praktisi yang telah berpengalaman di bidangnya. Visi kami adalah melahirkan talenta digital yang siap bersaing secara global.
                        </p>
                        <div class="row g-4">
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary-subtle p-2 rounded-3 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/></svg>
                                    </div>
                                    <h6 class="fw-bold text-navy mb-0">Mentor Praktisi</h6>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary-subtle p-2 rounded-3 text-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/></svg>
                                    </div>
                                    <h6 class="fw-bold text-navy mb-0">Kurikulum Industri</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="position-relative">
                        <img src="{{ asset('aset/ai.jpg') }}" class="img-fluid rounded-5 shadow-lg" alt="About idSpora" onerror="this.src='{{ asset('aset/poster.png') }}'">
                        <div class="position-absolute bottom-0 start-0 m-4 p-4 bg-white rounded-4 shadow-lg d-none d-md-block" style="max-width: 250px;">
                            <h5 class="fw-bold text-primary mb-1">Misi Kami</h5>
                            <p class="small text-muted mb-0">Mendigitalisasi pendidikan untuk masa depan yang lebih cerah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SERVICES SECTION (Migrated from services.blade.php) -->
    <section class="section-padding bg-main" id="layanan">
        <div class="container">
            <div class="section-header">
                <span class="sub">Layanan Kami</span>
                <h2>Program Pelatihan</h2>
                <p>Pilih format belajar yang paling cocok: webinar interaktif, seminar onsite, atau course mandiri.</p>
            </div>

            <div class="space-y-5">
                <!-- Webinar -->
                <div class="card border-0 mb-5 overflow-hidden rounded-4 shadow-sm">
                    <div class="row g-0 align-items-center">
                        <div class="col-lg-6 p-4 p-md-5">
                            <span class="badge bg-primary-subtle text-primary fw-bold mb-3 px-3 py-2 rounded-pill"><i class="bi bi-camera-video me-2"></i>Interactive Webinar</span>
                            <h3 class="fw-bold text-navy mb-3">Interactive Webinar (Online)</h3>
                            <p class="text-muted mb-4">
                                Dapatkan wawasan dari para ahli industri langsung dari kenyamanan rumah Anda. Webinar kami dirancang untuk interaksi dua arah yang intens.
                            </p>
                            <ul class="list-unstyled mb-4 d-grid gap-2">
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-success"></i> Sesi Tanya Jawab Langsung</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-success"></i> Rekaman Sesi Tersedia Selamanya</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-success"></i> E-Certificate Instan via Dashboard</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-success"></i> Networking Group Telegram/Discord</li>
                            </ul>
                            <div class="d-flex flex-wrap gap-3">
                                <a href="{{ route('events.index') }}" class="btn btn-primary-new px-4">Cari Webinar</a>
                                <a href="{{ route('public.support') }}" class="btn btn-outline-new px-4">Butuh Bantuan?</a>
                            </div>
                        </div>
                        <div class="col-lg-6 h-100">
                             <img src="{{ asset('aset/ai2.jpg') }}" alt="Webinar" class="img-fluid w-100 h-100 object-fit-cover" style="min-height: 400px;" onerror="this.onerror=null; this.src='{{ asset('aset/poster.png') }}';">
                        </div>
                    </div>
                </div>

                <!-- Seminar -->
                <div class="card border-0 mb-5 overflow-hidden rounded-4 shadow-sm">
                    <div class="row g-0 align-items-center flex-lg-row-reverse">
                         <div class="col-lg-6 p-4 p-md-5">
                            <span class="badge bg-warning-subtle text-warning-emphasis fw-bold mb-3 px-3 py-2 rounded-pill"><i class="bi bi-geo-alt me-2"></i>Exclusive Seminar</span>
                            <h3 class="fw-bold text-navy mb-3">Exclusive Seminar (Onsite)</h3>
                            <p class="text-muted mb-4">
                                Rasakan atmosfer belajar tatap muka yang tidak tergantikan. Seminar onsite kami fokus pada kolaborasi fisik dan networking tingkat tinggi.
                            </p>
                            <ul class="list-unstyled mb-4 d-grid gap-2">
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-warning"></i> Fasilitas Coffee Break & Lunch</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-warning"></i> Materi Cetak (Handout)</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-warning"></i> Workshop Hands-on Langsung</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-warning"></i> Sertifikat Fisik Bertanda Tangan</li>
                            </ul>
                             <a href="{{ route('events.index') }}" class="btn btn-outline-new px-4">Lihat Jadwal Seminar</a>
                        </div>
                        <div class="col-lg-6 h-100">
                             <img src="{{ asset('aset/event.png') }}" alt="Seminar" class="img-fluid w-100 h-100 object-fit-cover" style="min-height: 400px;" onerror="this.onerror=null; this.src='{{ asset('aset/poster.png') }}';">
                        </div>
                    </div>
                </div>

                <!-- Course -->
                <div class="card border-0 overflow-hidden rounded-4 shadow-sm">
                    <div class="row g-0 align-items-center">
                        <div class="col-lg-6 p-4 p-md-5">
                            <span class="badge bg-success-subtle text-success fw-bold mb-3 px-3 py-2 rounded-pill"><i class="bi bi-book me-2"></i>Online Course</span>
                            <h3 class="fw-bold text-navy mb-3">Self-Paced Online Course</h3>
                            <p class="text-muted mb-4">
                                Belajar sesuai ritme Anda sendiri. Kurikulum terstruktur dari tingkat dasar hingga mahir dengan proyek portofolio nyata.
                            </p>
                            <ul class="list-unstyled mb-4 d-grid gap-2">
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-success"></i> 100+ Video Pembelajaran HD</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-success"></i> Akses Selamanya (Life-time)</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-success"></i> Kuis & Tugas Per-Modul</li>
                                <li class="d-flex align-items-center gap-2 text-muted fw-medium"><i class="bi bi-check-circle-fill text-success"></i> Feedback Langsung dari Mentor</li>
                            </ul>
                            <a href="{{ route('courses.index') }}" class="btn btn-primary-new px-4">Mulai Belajar Sekarang</a>
                        </div>
                        <div class="col-lg-6 h-100">
                             <img src="{{ asset('aset/code.png') }}" alt="Course" class="img-fluid w-100 h-100 object-fit-cover" style="min-height: 400px;" onerror="this.onerror=null; this.src='{{ asset('aset/poster.png') }}';">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- FEATURES SECTION -->
    <section class="section-padding bg-main" id="fitur">
        <div class="container">
            <div class="section-header">
                <span class="sub text-primary">Fitur Unggulan</span>
                <h2 class="text-navy">Mengapa Belajar di idSPORA?</h2>
                <p>Kami menyediakan berbagai keunggulan untuk memastikan pengalaman belajarmu maksimal.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card-premium">
                        <div class="f-icon-wrap bg-warning-subtle text-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/><path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zM3.108 5.575c.123-.141.29-.214.505-.214.198 0 .351.064.457.193.106.129.159.333.159.615 0 .28-.052.483-.155.611-.102.127-.258.19-.467.19a.45.45 0 0 1-.453-.254v.053c0 .354.067.63.2.825.132.196.347.293.645.293.228 0 .413-.045.553-.135v.654c-.161.085-.398.128-.71.128-.426 0-.751-.137-.975-.41-.223-.273-.335-.67-.335-1.192 0-.329.043-.604.13-.824.088-.22.215-.403.383-.548zm.135 1.258h.507c.068 0 .121-.018.158-.054.038-.036.057-.1.057-.193 0-.094-.019-.158-.058-.192-.039-.035-.091-.052-.157-.052-.064 0-.115.018-.152.054-.037.037-.056.1-.056.19v.247z"/></svg>
                        </div>
                        <h5 class="fw-bold text-navy">Sertifikat Digital</h5>
                        <p class="text-muted small mb-0">Download sertifikat resmi secara otomatis langsung setelah menyelesaikan event atau kursus sebagai bukti keahlianmu.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card-premium">
                        <div class="f-icon-wrap bg-primary-subtle text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 10.1V3.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/></svg>
                        </div>
                        <h5 class="fw-bold text-navy">Akses Selamanya</h5>
                        <p class="text-muted small mb-0">Beli kursus sekali, akses materi selamanya. Kamu bisa mengulang pembelajaran kapan saja tanpa batas waktu.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card-premium">
                        <div class="f-icon-wrap bg-success-subtle text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/><path d="M2 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H2zm10-2.25V4.75a.75.75 0 0 0-1.5 0v7a.75.75 0 0 0 1.5 0z"/><path d="M14.75 10.25a.75.75 0 0 0-1.5 0v1.5a.75.75 0 0 0 1.5 0v-1.5z"/></svg>
                        </div>
                        <h5 class="fw-bold text-navy">Mentor Ahli</h5>
                        <p class="text-muted small mb-0">Belajar langsung dari mentor praktisi yang bekerja di perusahaan ternama. Dapatkan insight nyata dari lapangan.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card-premium">
                        <div class="f-icon-wrap bg-danger-subtle text-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h4v-3zM5 1h6v3H5V1zm6 4H5v3h6V5zm0 4H5v3h6V9zm0 4H5v3h6v-3zM4 1H1v3h3V1zM1 5v3h3V5H1zm0 4v3h3V9H1zm0 4v3h3v-3H1z"/></svg>
                        </div>
                        <h5 class="fw-bold text-navy">Grup Komunitas</h5>
                        <p class="text-muted small mb-0">Daftar sekarang dan bergabunglah dengan grup diskusi eksklusif di Telegram/Discord untuk networking.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CATEGORY SECTION -->
    <section class="section-padding bg-surface" id="kategori">
        <div class="container">
            <div class="section-header">
                <span class="sub text-primary">Eksplorasi</span>
                <h2 class="text-navy">Kategori Populer</h2>
                <p>Temukan materi pembelajaran yang sesuai dengan minat dan kebutuhan karirmu.</p>
            </div>
            
            <div class="row g-4">
                @foreach(['Programming' => 'code.png', 'Digital Marketing' => 'logo-google.png', 'UI/UX Design' => 'profile.png', 'Data Science' => 'ai.jpg', 'Business' => 'ikon-participant.png', 'Soft Skills' => 'ikon-bintang.png'] as $cat => $icon)
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="category-card">
                        <div class="cat-icon">
                             <!-- Using generic icon if file not exact, but logic is simplified here -->
                             @if(str_contains($cat, 'Programming')) <svg width="32" height="32" viewBox="0 0 24 24" fill="none" class="feather"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
                             @elseif(str_contains($cat, 'Marketing')) <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 6l-9.5 9.5-5-5L1 18"/></svg>
                             @elseif(str_contains($cat, 'Design')) <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19l7-7 3 3-7 7-3-3z"/><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="M2 2l7.586 7.586"/><circle cx="11" cy="11" r="2"/></svg>
                             @elseif(str_contains($cat, 'Data')) <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                             @elseif(str_contains($cat, 'Business')) <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                             @else <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                             @endif
                        </div>
                        <h6 class="fw-bold text-navy mb-0">{{ $cat }}</h6>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- EVENT SECTION -->
    <section class="section-padding bg-main" id="event-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div style="max-width: 600px;">
                    <span class="text-primary fw-bold text-uppercase ls-1">Webinar & Workshop</span>
                    <h2 class="display-6 fw-bold text-navy mt-2">Event Akan Datang</h2>
                </div>
                <a href="{{ route('events.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">Lihat Semua</a>
            </div>

            <div class="row g-4">
                @forelse($upcomingEvents as $event)
                <div class="col-lg-3 col-md-6">
                    <div class="event-card-new shadow-sm">
                        <div class="ec-thumb">
                            <img src="{{ $event->image_url ?: asset('aset/poster.png') }}" onerror="this.src='{{ asset('aset/poster.png') }}'" alt="{{ $event->title }}">
                            <div class="ec-date-badge">
                                <span class="day">{{ $event->event_date->format('d') }}</span>
                                <span class="month">{{ $event->event_date->format('M') }}</span>
                            </div>
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 bg-gradient-to-t from-black/80 to-transparent">
                                @if($event->price == 0)
                                <span class="badge bg-success rounded-pill">Gratis</span>
                                @else
                                <span class="badge bg-warning text-dark rounded-pill">Rp {{ number_format($event->price,0,',','.') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="p-4 d-flex flex-column flex-grow-1">
                            <h5 class="fw-bold text-navy mb-2 line-clamp-2" style="min-height: 3rem;">{{ $event->title }}</h5>
                            <div class="d-flex align-items-center gap-2 text-muted fs-small mb-3">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                {{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }} WIB
                            </div>
                            <div class="d-flex align-items-center gap-2 text-muted fs-small mb-4">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $event->speaker }}
                            </div>
                            
                            <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary-new w-100 mt-auto py-2 fs-small">Daftar Sekarang</a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                    <img src="{{ asset('aset/calendar-empty.png') }}" style="width: 120px; opacity: 0.5;" class="mb-3">
                    <h5 class="text-muted">Belum ada event jadwal baru.</h5>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- COURSE SECTION -->
    <section class="section-padding bg-surface">
        <div class="container">
            <div class="section-header">
                <span class="sub">Upgrade Skill</span>
                <h2>Kursus Unggulan</h2>
                <p>Materi terstruktur dengan mentor berpengalaman.</p>
            </div>

            <div class="row g-4">
                 @php
                    $publishedFeaturedCourses = isset($featuredCourses)
                        ? $featuredCourses->filter(function($c){ return ($c->status ?? null) === 'active'; })
                        : collect();
                @endphp
                @forelse($publishedFeaturedCourses as $course)
                <div class="col-lg-3 col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative group">
                        <div class="position-relative">
                             @if($course->image)
                                <img src="{{ Storage::url($course->image) }}" class="card-img-top" alt="{{ $course->name }}" style="height: 180px; object-fit: cover;">
                            @else
                                <img src="https://via.placeholder.com/300x200/4f46e5/ffffff?text=Course" class="card-img-top" style="height: 180px; object-fit: cover;">
                            @endif
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-white text-dark shadow-sm">{{ ucfirst($course->level) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size: 10px;">{{ $course->category->name ?? 'General' }}</span>
                                <div class="d-flex align-items-center gap-1 text-warning" style="font-size: 12px;">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>
                                    <span class="fw-bold">4.8</span>
                                </div>
                            </div>
                            <h6 class="card-title fw-bold text-navy mb-3 line-clamp-2">{{ $course->name }}</h6>
                            
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <div>
                                     @if((int)$course->price === 0)
                                        <span class="text-success fw-bold">GRATIS</span>
                                     @else
                                        <span class="text-navy fw-bold">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                     @endif
                                </div>
                                <button class="btn btn-sm btn-outline-primary rounded-pill">Enroll</button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                 <div class="col-12 text-center py-5">
                    <h5 class="text-muted">Kursus akan segera hadir.</h5>
                </div>
                @endforelse
            </div>
             <div class="text-center mt-5">
                <a href="{{ route('courses.index') }}" class="btn btn-link link-primary fw-bold text-decoration-none">Lihat Semua Kursus &rarr;</a>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="section-padding bg-main">
        <div class="container">
            <div class="section-header">
                <span class="sub">Apa Kata Mereka</span>
                <h2>Cerita Sukses Alumni</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testi-card-new">
                        <div class="quote-icon">“</div>
                        <p class="mb-4 text-muted">Kursus React idSpora sangat terstruktur. Saya yang awalnya bingung sekarang sudah bisa bikin project sendiri. Mantap!</p>
                        <hr class="border-light">
                        <div class="testi-user">
                            <img src="https://ui-avatars.com/api/?name=Sarah+Sechan&background=random" class="testi-avatar" alt="User">
                            <div class="testi-info">
                                <h5 class="text-navy">Sarah Sechan</h5>
                                <span>Frontend Developer</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testi-card-new">
                        <div class="quote-icon">“</div>
                        <p class="mb-4 text-muted">Webinar-webinar yang diadakan selalu menghadirkan pembicara yang kompeten. Insight-nya daging semua.</p>
                        <hr class="border-light">
                        <div class="testi-user">
                            <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=random" class="testi-avatar" alt="User">
                            <div class="testi-info">
                                <h5 class="text-navy">Budi Santoso</h5>
                                <span>Product Manager</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testi-card-new">
                        <div class="quote-icon">“</div>
                        <p class="mb-4 text-muted">Platform belajar terbaik untuk upskilling. Harganya terjangkau tapi kualitas materinya premium.</p>
                        <hr class="border-light">
                        <div class="testi-user">
                            <img src="https://ui-avatars.com/api/?name=Diana+P&background=random" class="testi-avatar" alt="User">
                            <div class="testi-info">
                                <h5 class="text-navy">Diana P.</h5>
                                <span>UI/UX Designer</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="py-5">
        <div class="container">
            <div class="p-5 rounded-5 bg-gradient-primary text-white text-center position-relative overflow-hidden">
                <div class="position-relative z-2">
                    <h2 class="fw-bold mb-3">Siap Memulai Perjalanan Karirmu?</h2>
                    <p class="mb-4 opacity-75 fs-5">Gabung sekarang dan akses ratusan materi premium.</p>
                    <a href="{{ route('register') }}" class="btn btn-warning btn-lg rounded-pill fw-bold shadow-lg px-5">Daftar Gratis Sekarang</a>
                </div>
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-10" style="background-image: url('data:image/svg+xml,...'); opacity: 0.05;"></div>
            </div>
        </div>
    </section>

    @include('partials.footer-before-login')

    <script>
        // Animations
        document.addEventListener('DOMContentLoaded', () => {
           const floatBadges = document.querySelectorAll('.animate-float');
           // Add simple float animation via JS if needed or just rely on CSS
        });
    </script>
    <style>
        .animate-float { animation: floating 3s ease-in-out infinite; }
        .animate-float-delayed { animation: floating 3s ease-in-out 1.5s infinite; }
        @keyframes floating { 
            0% { transform: translate(0,  0px); }
            50% { transform: translate(0, 15px); }
            100% { transform: translate(0, -0px); }    
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</body>
</html>