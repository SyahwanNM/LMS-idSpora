<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Trainer - {{ $trainer->full_name_with_title ?: $trainer->name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand-navy: #151650;
            --brand-gold: #fcc12d;
            --muted-text: #64748b;
            --bg-soft: #fcfdfe;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-soft);
            color: #1e293b;
            margin: 0;
            overflow-x: hidden;
        }

        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px;
        }

        /* Hero Section */
        .hero-section {
            padding: 80px 0 60px;
            background: radial-gradient(circle at 100% 0%, #f1f5f9 0%, #ffffff 50%);
        }

        .hero-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 60px;
        }

        .hero-content {
            flex: 1;
        }

        .badge-group {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }

        .badge-item {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 6px 14px;
            border-radius: 6px;
        }

        .badge-expert {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-verified {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .trainer-name {
            font-size: 72px;
            font-weight: 800;
            color: var(--brand-navy);
            margin: 0 0 12px;
            line-height: 1;
        }

        .trainer-role {
            font-size: 24px;
            color: var(--muted-text);
            font-weight: 500;
            margin-bottom: 32px;
        }

        .hero-meta {
            display: flex;
            gap: 32px;
            margin-bottom: 40px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-item i { color: #f59e0b; font-size: 16px; }

        .hero-btns {
            display: flex;
            gap: 16px;
        }

        .btn-navy {
            background-color: var(--brand-navy);
            color: white;
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 800;
            text-decoration: none;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 20px rgba(21, 22, 80, 0.2);
            transition: 0.2s;
        }

        .btn-white {
            background-color: white;
            color: var(--brand-navy);
            padding: 16px 40px;
            border-radius: 12px;
            font-weight: 800;
            text-decoration: none;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #f1f5f9;
            transition: 0.2s;
        }

        .hero-img-box {
            position: relative;
        }

        .trainer-img {
            width: 180px;
            height: 230px;
            object-fit: cover;
            border-radius: 20px;
            border: 6px solid white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }

        .mastery-badge {
            position: absolute;
            bottom: -15px;
            left: -40px;
            background: white;
            padding: 12px 20px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        }

        .mastery-icon {
            width: 40px;
            height: 40px;
            background: #fffbeb;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f59e0b;
        }

        /* Metrics */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 40px;
        }

        .metric-card {
            background: white;
            padding: 32px;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
        }

        .metric-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .metric-icon { 
            width: auto; 
            height: auto; 
            margin: 0; 
            font-size: 20px; 
            display: flex;
            align-items: center;
        }
        
        .metric-blue { color: #3b82f6; }
        .metric-gold { color: #f59e0b; }
        .metric-green { color: #10b981; }
        .metric-purple { color: #8b5cf6; }

        .metric-num {
            display: block;
            font-size: 36px;
            font-weight: 800;
            color: var(--brand-navy);
            line-height: 1;
        }

        .metric-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }

        /* Bio & Sidebar */
        .content-wrap {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 60px;
            margin-bottom: 100px;
        }

        .bio-title {
            font-size: 32px;
            font-weight: 800;
            color: var(--brand-navy);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .bio-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 40px;
            height: 4px;
            background: var(--brand-gold);
            border-radius: 2px;
        }

        .bio-text {
            font-size: 20px;
            line-height: 1.8;
            color: #475569;
            margin-bottom: 40px;
        }

        .philosophy-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .phil-card {
            background-color: #f8fafc;
            padding: 32px;
            border-radius: 20px;
        }

        .phil-header {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--brand-gold);
            letter-spacing: 1px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .phil-text { font-size: 15px; color: #475569; line-height: 1.6; }

        .sidebar-box {
            background: #f8fafc;
            border-radius: 24px;
            padding: 40px 32px;
        }

        .side-label {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--brand-navy);
            letter-spacing: 1px;
            margin-bottom: 24px;
        }

        .tag-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 40px; }
        .tag-pill {
            background: white; border: 1px solid #e2e8f0;
            padding: 8px 16px; border-radius: 12px;
            font-size: 13px; font-weight: 600; color: #64748b;
        }

        .social-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .social-item {
            background: white; border: 1px solid #e2e8f0;
            padding: 12px; border-radius: 12px;
            text-decoration: none; color: #64748b;
            font-size: 12px; font-weight: 700; text-transform: uppercase;
            display: flex; align-items: center; gap: 10px;
        }

        /* Track Record Section */
        .track-section { margin-bottom: 120px; }
        .track-header {
            display: flex; justify-content: space-between; align-items: flex-end;
            margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid #e2e8f0;
        }
        .track-title h2 { font-size: 40px; font-weight: 800; color: var(--brand-navy); margin: 0; text-transform: uppercase; line-height: 1; }
        .track-title p { color: #94a3b8; font-size: 15px; margin: 12px 0 0; font-weight: 500; }

        .tabs-container {
            background: #f8fafc; padding: 6px; border-radius: 14px; display: flex; gap: 4px; border: 1px solid #f1f5f9;
        }
        .tab-link {
            padding: 12px 24px; border-radius: 10px; font-size: 12px; font-weight: 800;
            text-transform: uppercase; color: #94a3b8; text-decoration: none;
            display: flex; align-items: center; gap: 10px; transition: 0.2s;
        }
        .tab-link i { font-size: 16px; }
        .tab-link.active { background: var(--brand-navy); color: white; box-shadow: 0 4px 12px rgba(21, 22, 80, 0.2); }
        .tab-link.active i { color: white; }

        .course-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
        .course-card { background: white; border-radius: 40px; overflow: hidden; border: 1px solid #f1f5f9; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .course-img-box { height: 300px; position: relative; }
        .course-img { width: 100%; height: 100%; object-fit: cover; }
        .course-lvl-badge {
            position: absolute; top: 24px; right: 24px; background: #fef3c7;
            padding: 8px 16px; border-radius: 10px; font-size: 11px; font-weight: 900; color: #f59e0b;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .course-body { padding: 40px; }
        .course-meta-row { display: flex; justify-content: space-between; margin-bottom: 24px; font-size: 12px; font-weight: 900; text-transform: uppercase; }
        .course-meta-row span { display: flex; align-items: center; gap: 8px; color: #f59e0b; }
        
        .course-main-title { font-size: 28px; font-weight: 900; color: var(--brand-navy); margin-bottom: 32px; line-height: 1.2; text-transform: uppercase; }
        .course-btn-details {
            display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%;
            background: #f8fafc; padding: 20px; border-radius: 16px; text-decoration: none;
            color: var(--brand-navy); font-weight: 800; font-size: 13px; text-transform: uppercase;
            transition: 0.2s;
        }
        .course-btn-details:hover { background: #f1f5f9; }

        /* Timeline Experience */
        .timeline-wrap { position: relative; padding-left: 20px; }
        .timeline-wrap::before { content: ''; position: absolute; left: 6px; top: 12px; bottom: 12px; width: 1px; background: #e2e8f0; }
        .timeline-item { position: relative; padding-left: 40px; margin-bottom: 60px; }
        .timeline-item::before {
            content: ''; position: absolute; left: -20px; top: 12px; width: 13px; height: 13px;
            background: var(--brand-gold); border-radius: 50%; box-shadow: 0 0 0 6px white;
        }
        .tm-header-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .tm-role { font-size: 24px; font-weight: 900; color: var(--brand-navy); text-transform: uppercase; }
        .tm-period { background: #f1f5f9; padding: 6px 16px; border-radius: 20px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; }
        .tm-brand { color: var(--brand-gold); font-weight: 800; font-size: 18px; margin-bottom: 16px; }
        .tm-info { color: #64748b; font-size: 16px; line-height: 1.7; max-width: 700px; }

        /* Credentials */
        .cred-card {
            background: white; border-radius: 32px; padding: 40px; border: 1px solid #f1f5f9;
            display: flex; flex-direction: column; align-items: flex-start;
        }
        .cred-icon { width: 52px; height: 52px; margin-bottom: 24px; }
        .cred-title { font-size: 20px; font-weight: 900; color: var(--brand-navy); text-transform: uppercase; line-height: 1.3; margin-bottom: 8px; }
        .cred-meta { font-size: 12px; font-weight: 800; color: #94a3b8; text-transform: uppercase; }

        /* Student Feedback Section */
        .feedback-container {
            background-color: #1a1b52; border-radius: 50px; padding: 100px 80px;
            position: relative; overflow: hidden; margin-bottom: 120px;
        }
        .feedback-container::after {
            content: '”'; position: absolute; right: 40px; bottom: -80px;
            font-size: 450px; color: rgba(255,255,255,0.03); font-family: serif; line-height: 1;
        }
        .feed-head h2 { font-size: 56px; font-weight: 800; color: white; margin-bottom: 16px; text-transform: uppercase; }
        .feed-head p { color: #94a3b8; font-size: 20px; margin-bottom: 80px; font-weight: 500; }

        .f-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .f-card {
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
            padding: 50px; border-radius: 32px; backdrop-filter: blur(8px);
        }
        .f-stars { color: #fcc12d; font-size: 16px; margin-bottom: 28px; display: flex; gap: 4px; }
        .f-text { font-size: 22px; color: white; font-style: italic; line-height: 1.6; margin-bottom: 40px; font-weight: 500; }
        .f-author { display: flex; align-items: center; gap: 20px; }
        .f-img { width: 56px; height: 56px; border-radius: 50%; object-fit: cover; }
        .f-name { color: white; font-weight: 800; font-size: 18px; display: block; }
        .f-role { color: #fcc12d; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.5px; }

        @media (max-width: 1024px) {
            .hero-flex { flex-direction: column; text-align: center; }
            .trainer-name { font-size: 60px; }
            .track-header { flex-direction: column; align-items: flex-start; gap: 32px; }
            .course-grid { grid-template-columns: 1fr; }
            .cta-ready h2 { font-size: 60px; }
            .feedback-container { padding: 60px 40px; }
            .f-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    @include('partials.navbar-after-login')

    <section class="hero-section">
        <div class="container-custom">
            <div class="hero-flex">
                <div class="hero-content">
                    <div class="badge-group">
                        <span class="badge-item badge-expert">Expert Instructor</span>
                        <span class="badge-item badge-verified">Verified Professional</span>
                    </div>
                    <h4 class="trainer-name">{{ $trainer->full_name_with_title ?: $trainer->name }}</h4>
                    <p class="trainer-role">{{ $trainer->profession ?: 'Trainer' }}</p>

                    <div class="hero-meta">
                        <div class="meta-item"><i class="bi bi-geo-alt-fill"></i> {{ $trainer->institution ?: 'Lokasi tidak diisi' }}</div>
                        <div class="meta-item"><i class="bi bi-star-fill"></i> {{ isset($reputation['rating']) ? number_format($reputation['rating'], 1) : '0.0' }} Rating</div>
                        <div class="meta-item"><i class="bi bi-people-fill"></i> {{ isset($reputation['students']) ? number_format($reputation['students']) : '0' }} Students</div>
                    </div>
                </div>

                <div class="hero-img-box">
                    <img src="{{ $trainer->avatar_url }}" alt="{{ $trainer->name }}" class="trainer-img">
                    <div class="mastery-badge">
                        <div class="mastery-icon"><i class="bi bi-trophy-fill"></i></div>
                        <div>
                            <span style="display:block; font-size:9px; font-weight:800; color:#94a3b8; text-transform:uppercase;">Status</span>
                            <strong style="color:var(--brand-navy); font-size:16px;">Mastery</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container-custom">
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-icon metric-blue"><i class="bi bi-briefcase-fill"></i></div>
                    <span class="metric-label">Years Experience</span>
                </div>
                <span class="metric-num">{{ $reputation['experience_years'] }}+</span>
            </div>
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-icon metric-gold"><i class="bi bi-book-half"></i></div>
                    <span class="metric-label">Courses Delivered</span>
                </div>
                <span class="metric-num">{{ $activeCourses->count() }}</span>
            </div>
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-icon metric-green"><i class="bi bi-bullseye"></i></div>
                    <span class="metric-label">Success Rate</span>
                </div>
                <span class="metric-num">{{ $reputation['success_rate'] }}%</span>
            </div>
            <div class="metric-card">
                <div class="metric-top">
                    <div class="metric-icon metric-purple"><i class="bi bi-people-fill"></i></div>
                    <span class="metric-label">Active Learners</span>
                </div>
                <span class="metric-num">{{ number_format($reputation['active_learners']/1000, 1) }}k</span>
            </div>
        </div>

        <div class="content-wrap">
            <main>
                <h2 class="bio-title"><i class="bi bi-person-fill"></i> Professional Biography</h2>
                <div class="bio-text">
                    {{ $trainer->bio ?: 'Profil belum dilengkapi. Tambahkan bio agar peserta mengenal Anda lebih baik.' }}
                </div>

                <div class="philosophy-cards">
                    <div class="phil-card">
                        <div class="phil-header"><i class="bi bi-lightning-fill"></i> Teaching Philosophy</div>
                        <p class="phil-text">{{ $philosophy ?: '-' }}</p>
                    </div>
                    <div class="phil-card">
                        <div class="phil-header"><i class="bi bi-bullseye"></i> Learning Outcomes</div>
                        <p class="phil-text">{{ $outcomes ?: '-' }}</p>
                    </div>
                </div>
            </main>

            <aside>
                <div class="sidebar-box">
                    <div class="side-label">Core Expertise</div>
                    <div class="tag-list">
                        @foreach($expertise as $tag)
                            <span class="tag-pill">{{ strtoupper($tag) }}</span>
                        @endforeach
                    </div>

                    <div class="side-label">Digital Presence</div>
                    <div class="social-grid">
                        <a href="{{ $trainer->linkedin_url ?: '#' }}" class="social-item" {{ $trainer->linkedin_url ? 'target=_blank rel=noopener noreferrer' : 'aria-disabled=true style=opacity:.55;pointer-events:none;' }}><i class="bi bi-linkedin"></i> LinkedIn</a>
                        <a href="{{ $trainer->website ?: '#' }}" class="social-item" {{ $trainer->website ? 'target=_blank rel=noopener noreferrer' : 'aria-disabled=true style=opacity:.55;pointer-events:none;' }}><i class="bi bi-globe"></i> Website</a>
                    </div>
                </div>
            </aside>
        </div>

        <section class="track-section" id="courses">
            <div class="track-header">
                <div class="track-title">
                    <h2>TRACK RECORD</h2>
                    <p>Verified courses, professional experience, and academic credentials.</p>
                </div>
                <div class="tabs-container">
                    <a href="javascript:void(0)" class="tab-link active" onclick="goTab(this, 'c')"><i class="bi bi-book"></i> Courses</a>
                    <a href="javascript:void(0)" class="tab-link" onclick="goTab(this, 'e')"><i class="bi bi-briefcase"></i> Experience</a>
                    <a href="javascript:void(0)" class="tab-link" onclick="goTab(this, 'cr')"><i class="bi bi-patch-check"></i> Credentials</a>
                </div>
            </div>

            <div id="t-c" class="t-panel">
                <div class="course-grid">
                    @forelse($activeCourses as $course)
                    <div class="course-card">
                        <div class="course-img-box">
                            <img src="{{ $course->thumbnail_url ?: 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?q=80&w=800' }}" class="course-img">
                            <span class="course-lvl-badge">{{ $course->level ?? 'Intermediate' }}</span>
                        </div>
                        <div class="course-body">
                            <div class="course-meta-row">
                                <span><i class="bi bi-clock"></i> {{ $course->modules_count ?? '-' }} Modules</span>
                                <span><i class="bi bi-star-fill"></i> {{ $course->rating ?? '-' }}</span>
                            </div>
                            <h3 class="course-main-title">{{ $course->name }}</h3>
                            <a href="{{ route('course.detail', $course->id) }}" class="course-btn-details">Course Details <i class="bi bi-arrow-right ms-2"></i></a>
                        </div>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>

            <div id="t-e" class="t-panel" style="display:none;">
                <div class="timeline-wrap">
                    @forelse($experiences as $exp)
                    <div class="timeline-item">
                        <div class="tm-header-row">
                            <h4 class="tm-role">{{ $exp->role }}</h4>
                            <span class="tm-period">{{ $exp->period }}</span>
                        </div>
                        <div class="tm-brand">{{ $exp->company }}</div>
                        <p class="tm-info">{{ $exp->description }}</p>
                    </div>
                    @empty
                    <div class="timeline-item">
                        <div class="tm-header-row">
                            <h4 class="tm-role">{{ $trainer->profession ?: 'Trainer' }}</h4>
                            <span class="tm-period">PRESENT</span>
                        </div>
                        <div class="tm-brand">{{ $trainer->institution ?: 'idSpora Trainer' }}</div>
                        <p class="tm-info">Aktif mengembangkan pengalaman belajar peserta dengan sesi training praktis.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <div id="t-cr" class="t-panel" style="display:none;">
                <div class="course-grid">
                    @forelse($certificates as $certificate)
                    <div class="cred-card">
                        <img src="{{ $certificate->icon_url ?? 'https://cdn-icons-png.flaticon.com/512/2991/2991148.png' }}" class="cred-icon">
                        <h4 class="cred-title">{{ $certificate->title }}</h4>
                        <p class="cred-meta">{{ $certificate->issuer }} • {{ $certificate->year }}</p>
                    </div>
                    @empty
                    <div class="cred-card">
                        <h4 class="cred-title">Belum ada sertifikat</h4>
                        <p class="cred-meta">Sertifikat akan muncul di sini setelah diverifikasi.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="feedback-container">
            <div class="feed-head">
                <h2>STUDENT FEEDBACK</h2>
                <p>Direct reviews from professionals who completed mentorship programs.</p>
            </div>
            <div class="f-grid">
                @forelse($feedbacks as $feedback)
                <div class="f-card">
                    <div class="f-stars">
                        @for($i = 0; $i < 5; $i++)
                            <i class="bi {{ $i < $feedback->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                        @endfor
                    </div>
                    <p class="f-text">"{{ $feedback->comment }}"</p>
                    <div class="f-author">
                        <img src="{{ $feedback->user_avatar_url ?? 'https://i.pravatar.cc/150?u=' . ($feedback->user_name ?? 'anon') }}" class="f-img">
                        <div>
                            <span class="f-name">{{ $feedback->user_name ?? 'Anonim' }}</span>
                            <span class="f-role">{{ $feedback->user_role ?? '' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="f-card">
                    <p class="f-text">Belum ada feedback dari peserta.</p>
                </div>
                @endforelse
            </div>
        </section>
    </div>

    <script>
        function goTab(el, id) {
            document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
            el.classList.add('active');
            
            document.querySelectorAll('.t-panel').forEach(p => p.style.display = 'none');
            document.getElementById('t-' + id).style.display = 'block';
        }
    </script>
</body>

</html>