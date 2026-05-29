@extends('layouts.trainer')

@section('title', 'Profile - Trainer')

@php
    $pageTitle = 'Profile';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Profile']
    ];

    $displayRole = $trainer->profession ?: 'Trainer';
    $displayLocation = $trainer->institution ?: 'Location not set';
    $displayBio = $trainer->bio ?: 'Profil belum dilengkapi. Tambahkan bio agar peserta mengenal Anda lebih baik.';
    $displayFullName = $trainer->full_name_with_title ?: $trainer->name;
    $displayLinkedIn = $trainer->linkedin_url ?: null;
    $displayBankName = $trainer->bank_name ?: 'Belum diisi';
    $displayBankAccountNumber = $trainer->bank_account_number ?: 'Belum diisi';
    $displayBankAccountHolder = $trainer->bank_account_holder ?: 'Belum diisi';

    $headline = $trainer->profession
        ? ($trainer->institution ? $trainer->profession . ' at ' . $trainer->institution : $trainer->profession)
        : 'Praktisi & Trainer Profesional';
    $isVerifiedTrainer = !empty($trainer->email_verified_at);

    $activeStatus = ['active', 'published', 'ongoing'];
    $activeCoursesCollection = collect($courses)->filter(function ($courseItem) use ($activeStatus) {
        return in_array(strtolower((string) ($courseItem->status ?? '')), $activeStatus, true);
    })->values();

    $archivedCoursesCollection = collect($courses)->reject(function ($courseItem) use ($activeStatus) {
        return in_array(strtolower((string) ($courseItem->status ?? '')), $activeStatus, true);
    })->values();

    if ($activeCoursesCollection->isEmpty()) {
        $activeCoursesCollection = collect($topCourses)->values();
    }

    $activeCourses = $activeCoursesCollection->take(3);
    $archivedCourses = $archivedCoursesCollection->take(3);
    $selectedTestimonials = collect($recentFeedbacks)->take(3);
    $completedEventsCount = (int) ($completedEventsCount ?? 0);
    $completedCoursesCount = (int) ($completedCoursesCount ?? 0);
    $totalCertificates = (int) ($totalCertificates ?? 0);
    $trainerCertificates = collect($trainerCertificates ?? [])->take(3);
    $profileCompletion = (int) $trainer->getProfileCompletionPercentage();
@endphp

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --primary-soft: #eff6ff;
            --primary-border: #bfdbfe;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --bg-gray: #f9fafb;
            --border-light: #f3f4f6;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
        }

        body { background-color: #ffffff; font-family: 'Inter', sans-serif; }

        .profile-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            color: var(--text-dark);
        }

        .top-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        /* HERO CARD (LEFT) */
        .hero-card {
            background: linear-gradient(135deg, #ffffff 0%, #faf8ff 100%);
            border-radius: var(--radius-xl);
            padding: 32px;
            border: 1px solid var(--border-light);
            position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            display: flex;
            gap: 24px;
            align-items: center;
        }
        .hero-card .edit-btn {
            position: absolute; top: 24px; right: 24px;
            background: #ffffff; border: 1px solid var(--border-light); color: var(--primary);
            padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
            display: flex; align-items: center; gap: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .hero-avatar { position: relative; flex-shrink: 0; }
        .hero-avatar img {
            width: 140px; height: 140px; border-radius: 50%; object-fit: cover;
            border: 4px solid #ffffff; box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        }
        .hero-avatar .edit-photo {
            position: absolute; bottom: 4px; right: 4px;
            background: var(--primary-light); color: #ffffff; border-radius: 50%; width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(139,92,246,0.3);
            border: 2px solid #ffffff; cursor: pointer; font-size: 14px;
        }
        
        .hero-info { display: flex; flex-direction: column; justify-content: center; flex-grow: 1; }
        .hero-info h1 {
            margin: 0; font-size: 22px; font-weight: 700; color: var(--text-dark);
            display: flex; align-items: center; gap: 8px; letter-spacing: -0.5px;
        }
        .hero-info h1 i { color: var(--primary-light); font-size: 18px; }
        
        .role-badge {
            display: inline-block; background: var(--primary-soft); color: var(--primary-light);
            padding: 6px 16px; border-radius: 99px; font-size: 12px; font-weight: 600; margin: 12px 0;
            width: fit-content;
        }
        .hero-bio { font-size: 13px; color: var(--text-muted); line-height: 1.6; margin: 0 0 20px; max-width: 500px; }
        
        .hero-meta { display: flex; gap: 20px; font-size: 12px; color: var(--text-muted); font-weight: 500; margin-bottom: 20px; }
        .hero-meta span { display: flex; align-items: center; gap: 6px; }
        .hero-meta i { font-size: 14px; }

        .hero-contacts {
            display: flex; gap: 24px; border-top: 1px solid var(--border-light); padding-top: 16px;
            font-size: 12px; color: var(--text-dark); font-weight: 500;
        }
        .hero-contacts span { display: flex; align-items: center; gap: 8px; }
        .hero-contacts i { color: var(--primary-light); font-size: 14px; }

        /* ACTIVITY CARD (RIGHT) */
        .sidebar-card {
            background: #ffffff;
            border-radius: var(--radius-xl);
            padding: 24px;
            border: 1px solid var(--border-light);
            box-shadow: 0 1px 3px rgba(0,0,0,0.01);
            height: fit-content;
        }
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .sidebar-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }
        .activity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        .activity-item {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }
        .activity-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; font-size: 18px;
            flex-shrink: 0;
        }
        .activity-icon.primary { background: var(--primary-soft); color: var(--primary-light); }
        .activity-icon.green { background: #ecfdf5; color: #10b981; }
        .activity-icon.orange { background: #fffbeb; color: #f59e0b; }
        .activity-icon.blue { background: #eff6ff; color: #3b82f6; }
        
        .activity-text h4 { margin: 0; font-size: 18px; font-weight: 700; color: var(--text-dark); }
        .activity-text p { margin: 4px 0 0; font-size: 11px; color: var(--text-muted); }

        /* ACHIEVEMENT LIST */
        .achievement-list { display: flex; flex-direction: column; gap: 20px; }
        .achievement-item { display: flex; gap: 12px; align-items: flex-start; }
        .achieve-icon { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 16px; }
        .achieve-icon.gold { background: #fef3c7; color: #d97706; }
        .achieve-icon.green { background: #dcfce7; color: #15803d; }
        .achieve-icon.primary { background: var(--primary-soft); color: var(--primary-light); }
        
        .achieve-info { flex-grow: 1; }
        .achieve-info-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; }
        .achieve-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin: 0; }
        .achieve-date { font-size: 11px; color: var(--text-muted); }
        .achieve-desc { font-size: 12px; color: var(--text-muted); margin: 0; line-height: 1.4; }

        /* RATING SUMMARY SIDEBAR */
        .rating-sidebar-big { text-align: left; margin-bottom: 24px; border-bottom: 1px solid var(--border-light); padding-bottom: 24px; }
        .rating-sidebar-big h2 { margin: 0; font-size: 40px; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 8px; letter-spacing: -1px; }
        .rating-sidebar-big h2 i { font-size: 24px; color: #facc15; }
        .rating-sidebar-big p { margin: 4px 0 0; font-size: 13px; color: var(--text-muted); }
        
        .rating-bars { display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px; }
        .rating-bar-row { display: flex; align-items: center; gap: 12px; font-size: 12px; font-weight: 600; color: var(--text-muted); }
        .rating-bar-wrap { flex-grow: 1; height: 6px; background: #e5e7eb; border-radius: 99px; overflow: hidden; }
        .rating-bar-fill { height: 100%; background: #facc15; border-radius: 99px; }
        .rating-count { width: 30px; text-align: right; color: var(--text-dark); }
        .rating-pct { font-size: 11px; color: var(--text-muted); width: 35px; text-align: right; }

        .rating-aspects { display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px; }
        .aspect-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 12px; }
        .aspect-row { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: var(--text-muted); }
        .aspect-row span:last-child { font-weight: 600; color: var(--text-dark); }
        .aspect-row i { color: var(--primary-light); margin-right: 8px; font-size: 16px; width: 20px; text-align: center; display: inline-block; }
        .btn-write-review { width: 100%; background: #ffffff; border: 1px solid var(--primary-light); color: var(--primary-light); padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; text-align: center; transition: all 0.2s; }
        .btn-write-review:hover { background: var(--primary-soft); }

        /* TABS NAV */
        .profile-tabs {
            display: flex; padding: 0 32px; justify-content: space-between;
            border-bottom: 1px solid var(--border-light);
        }
        .tab-item {
            padding: 16px 12px; font-size: 13px; font-weight: 600; color: var(--text-muted);
            text-decoration: none; display: flex; align-items: center; gap: 6px; white-space: nowrap;
            border-bottom: 2px solid transparent; cursor: pointer; transition: all 0.2s ease;
        }
        .tab-item.active { color: var(--primary); border-bottom-color: var(--primary); }
        .tab-item i { font-size: 16px; }

        /* TAB CONTENT PANELS */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        /* SHARED SECTION STYLES */
        .content-section {
            background: #ffffff; border-radius: var(--radius-xl); padding: 32px;
            border: 1px solid var(--border-light); box-shadow: 0 1px 3px rgba(0,0,0,0.01);
            margin-bottom: 24px;
        }
        .section-header-flex { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
        .section-title { font-size: 18px; font-weight: 700; color: var(--text-dark); margin: 0; letter-spacing: -0.5px; }
        .section-subtitle { font-size: 14px; color: var(--text-muted); margin: 8px 0 24px; }
        .btn-outline-primary {
            background: #ffffff; border: 1px solid var(--border-light); color: var(--primary);
            padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px; text-decoration: none;
        }
        .btn-icon-edit {
            background: transparent; border: none; color: var(--text-muted);
            font-size: 16px; cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 50%;
        }
        .btn-icon-edit:hover { color: var(--primary); background: var(--primary-soft); }

        .view-all-link {
            font-size: 13px; font-weight: 600; color: var(--primary); text-decoration: none;
            display: inline-flex; align-items: center; gap: 4px;
        }

        /* ---------------- TENTANG SAYA TAB ---------------- */
        .about-text { font-size: 14px; color: var(--text-muted); line-height: 1.8; margin: 0 0 32px; }
        .pill-list { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 32px; }
        .pill {
            background: #f9fafb; border: 1px solid var(--border-light); color: #4b5563;
            padding: 8px 20px; border-radius: 99px; font-size: 13px; font-weight: 600;
        }
        .pill.active { background: var(--primary-soft); color: var(--primary-light); border-color: var(--primary-border); }
        .stat-horizontal-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
        .stat-h-box {
            background: #f9fafb; border-radius: var(--radius-md); padding: 16px; border: 1px solid var(--border-light);
            display: flex; align-items: center; gap: 12px;
        }
        .stat-h-icon {
            width: 40px; height: 40px; border-radius: 50%; background: #ffffff; border: 1px solid var(--border-light);
            display: flex; align-items: center; justify-content: center; color: var(--primary-light); font-size: 16px;
        }
        .stat-h-text p { margin: 0; font-size: 11px; color: var(--text-muted); }
        .stat-h-text h4 { margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--text-dark); letter-spacing: -0.5px; }

        /* ---------------- KEAHLIAN TAB ---------------- */
        .expertise-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .expertise-card {
            border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 20px;
            background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.01);
        }
        .exp-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; }
        .exp-icon {
            width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 18px;
        }
        .exp-icon.purple { background: var(--primary-soft); color: var(--primary-light); }
        .exp-icon.green { background: #ecfdf5; color: #10b981; }
        .exp-icon.orange { background: #fffbeb; color: #f59e0b; }
        .exp-title-box h4 { margin: 0; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .exp-title-box p { margin: 2px 0 0; font-size: 12px; color: var(--text-muted); }
        .exp-progress-wrap { display: flex; align-items: center; gap: 12px; }
        .exp-progress-bar { flex-grow: 1; height: 6px; background: #f3f4f6; border-radius: 99px; overflow: hidden; }
        .exp-progress-fill { height: 100%; background: var(--primary); border-radius: 99px; }
        .exp-percentage { font-size: 13px; font-weight: 700; color: var(--text-dark); width: 35px; text-align: right; }

        /* ---------------- PENGALAMAN TAB ---------------- */
        .timeline { position: relative; padding-left: 24px; margin-top: 12px; }
        .timeline::before { content: ''; position: absolute; left: 0; top: 8px; bottom: 0; width: 1px; background: #e5e7eb; }
        .timeline-item { position: relative; margin-bottom: 32px; display: grid; grid-template-columns: 100px 1fr; gap: 24px; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-dot { position: absolute; left: -28px; top: 8px; width: 8px; height: 8px; border-radius: 50%; background: var(--primary-light); border: 2px solid #ffffff; box-shadow: 0 0 0 2px var(--primary-light); }
        .timeline-date { font-size: 12px; color: var(--text-muted); font-weight: 600; line-height: 1.5; padding-top: 4px; }
        .timeline-content { border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 20px; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.01); }
        .timeline-content-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .timeline-role { font-size: 15px; font-weight: 700; color: var(--text-dark); margin: 0; }
        .timeline-company { font-size: 13px; color: var(--text-muted); margin: 4px 0 0; }
        .timeline-duration { font-size: 11px; font-weight: 600; color: var(--primary-light); background: var(--primary-soft); padding: 4px 10px; border-radius: 99px; }
        .timeline-desc { font-size: 13px; color: var(--text-muted); margin: 12px 0 0; line-height: 1.6; }

        /* ---------------- PENDIDIKAN & SERTIFIKASI TAB ---------------- */
        .edu-cert-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
        
        .ec-list-card {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 16px;
            background: #ffffff;
            display: flex;
            gap: 16px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.01);
            transition: all 0.2s ease;
        }
        .ec-list-card:hover { border-color: #d1d5db; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        
        .ec-icon {
            width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 24px;
        }
        .ec-icon.gold { background: #fff8e1; color: #f5b041; }
        .ec-icon.blue { background: #eff6ff; color: #3b82f6; }
        .ec-icon.navy { background: #f8fafc; color: #475569; }
        .ec-icon.orange { background: #fff7ed; color: #f97316; }
        
        .ec-info { flex-grow: 1; }
        .ec-info-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 4px; }
        .ec-title { font-size: 15px; font-weight: 700; color: var(--text-dark); margin: 0; }
        .ec-subtitle { font-size: 13px; font-weight: 600; color: var(--text-dark); margin: 4px 0 2px; }
        .ec-desc { font-size: 12px; color: var(--text-muted); margin: 0; line-height: 1.5; }
        .ec-year { font-size: 12px; color: var(--text-muted); font-weight: 600; }
        
        .ec-badge {
            font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; text-transform: uppercase;
        }
        .ec-badge.primary { background: var(--primary-soft); color: var(--primary); }
        .ec-badge.blue { background: #eff6ff; color: #3b82f6; }
        .ec-badge.green { background: #dcfce7; color: #166534; }
        
        /* Timeline specific */
        .edu-timeline { position: relative; padding-left: 24px; margin-top: 24px; }
        .edu-timeline::before { content: ''; position: absolute; left: 0; top: 12px; bottom: 0; width: 1px; background: #e5e7eb; }
        .edu-timeline-item { position: relative; margin-bottom: 24px; }
        .edu-timeline-item:last-child { margin-bottom: 0; }
        .edu-timeline-dot {
            position: absolute; left: -29px; top: 16px; width: 10px; height: 10px; border-radius: 50%;
            background: var(--primary-light); border: 2px solid #ffffff; box-shadow: 0 0 0 2px var(--primary-light);
        }
        
        .cert-list { display: flex; flex-direction: column; gap: 24px; margin-top: 24px; }

        /* ---------------- ULASAN TAB ---------------- */
        .review-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .review-title { display: flex; align-items: center; gap: 8px; font-size: 18px; font-weight: 700; color: var(--text-dark); margin: 0; }
        .review-filters { display: flex; gap: 12px; }
        .filter-select { padding: 8px 12px; border: 1px solid var(--border-light); border-radius: 8px; font-size: 13px; color: var(--text-dark); background: #ffffff; outline: none; }
        
        .review-list { display: flex; flex-direction: column; gap: 0; }
        .review-card { padding: 24px 0; border-bottom: 1px solid var(--border-light); display: flex; gap: 16px; }
        .review-card:last-child { border-bottom: none; }
        .reviewer-img { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; }
        .review-content { flex-grow: 1; }
        .review-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px; }
        .reviewer-name { font-size: 14px; font-weight: 700; color: var(--text-dark); margin: 0; }
        .reviewer-role { font-size: 12px; color: var(--text-muted); margin: 2px 0 0; }
        .review-date { font-size: 12px; color: var(--text-muted); margin-top: 4px; display: block; }
        
        .review-rating-badge { display: flex; align-items: center; gap: 4px; font-size: 12px; font-weight: 600; color: var(--text-dark); }
        .review-rating-badge i { color: #facc15; }
        .review-text { font-size: 13px; color: var(--text-dark); line-height: 1.6; margin: 8px 0 12px; }
        .review-event-badge { display: inline-block; background: var(--primary-soft); color: var(--primary-light); padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: 600; }
        
        .review-pagination { display: flex; justify-content: center; gap: 8px; margin-top: 24px; }
        .page-btn { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: var(--text-muted); border: 1px solid transparent; }
        .page-btn:hover { background: #f9fafb; }
        .page-btn.active { background: var(--primary-soft); color: var(--primary-light); }
        
        /* ---------------- EVENT & COURSE TERBARU ---------------- */
        .event-course-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .ec-card {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #ffffff;
            display: flex; flex-direction: column;
        }
        .ec-img-wrap { position: relative; height: 160px; }
        .ec-img { width: 100%; height: 100%; object-fit: cover; }
        .ec-badge-overlay { position: absolute; top: 12px; left: 12px; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; text-transform: uppercase; color: #ffffff; }
        .ec-badge-overlay.event { background: rgba(139, 92, 246, 0.9); }
        .ec-badge-overlay.course { background: rgba(16, 185, 129, 0.9); }
        .ec-body { padding: 20px; flex-grow: 1; display: flex; flex-direction: column; }
        .ec-title { font-size: 16px; font-weight: 700; color: var(--text-dark); margin: 0 0 16px; line-height: 1.4; }
        .ec-meta { display: flex; gap: 8px; flex-wrap: wrap; font-size: 12px; }
        .ec-meta span { display: flex; align-items: center; gap: 6px; background: #f1f5f9; padding: 6px 12px; border-radius: 8px; color: #475569; font-weight: 600; }
        .ec-meta i { font-size: 14px; }

        @media (max-width: 992px) {
            .top-grid, .main-grid { grid-template-columns: 1fr; }
            .hero-card { flex-direction: column; align-items: center; text-align: center; }
            .hero-info { align-items: center; }
            .hero-contacts { justify-content: center; flex-wrap: wrap; }
            .stat-horizontal-grid, .expertise-grid, .edu-cert-grid, .event-course-grid { grid-template-columns: 1fr; }
            .timeline-item { grid-template-columns: 1fr; gap: 12px; }
            .timeline-date { padding-top: 0; }
            .timeline-dot { top: 6px; left: -28px; }
        }
        /* ---------------- MODALS ---------------- */
        .modal-overlay {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(17, 24, 39, 0.6); z-index: 1000;
            display: none; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.3s ease; padding: 20px;
        }
        .modal-overlay.active { display: flex; opacity: 1; }
        .modal-box {
            background: #ffffff; border-radius: var(--radius-xl);
            width: 100%; max-width: 600px; max-height: 90vh;
            display: flex; flex-direction: column;
            transform: translateY(20px); transition: transform 0.3s ease;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .modal-overlay.active .modal-box { transform: translateY(0); }
        .modal-header {
            padding: 24px; border-bottom: 1px solid var(--border-light);
            display: flex; justify-content: space-between; align-items: center;
        }
        .modal-title { font-size: 18px; font-weight: 700; color: var(--text-dark); margin: 0; }
        .btn-close-modal {
            background: none; border: none; font-size: 24px; color: var(--text-muted);
            cursor: pointer; padding: 0; display: flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; border-radius: 50%; transition: background 0.2s;
        }
        .btn-close-modal:hover { background: var(--bg-gray); color: var(--text-dark); }
        .modal-body { padding: 24px; overflow-y: auto; flex-grow: 1; }
        .modal-footer {
            padding: 20px 24px; border-top: 1px solid var(--border-light);
            display: flex; justify-content: flex-end; gap: 12px;
        }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-weight: 500; margin-bottom: 8px; color: var(--text-dark); font-size: 14px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb; border-radius: 8px; font-family: inherit; font-size: 14px; transition: border-color 0.2s; }
        .form-control:focus { outline: none; border-color: var(--primary); }
        textarea.form-control { resize: vertical; min-height: 100px; }

        /* Tag Input Styles */
        .tag-input-container { border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; cursor: text; background: white; }
        .tag-input-container:focus-within { border-color: var(--primary); }
        .tag-item { background: #eff6ff; color: #1e3a8a; padding: 4px 10px; border-radius: 99px; font-size: 12px; display: flex; align-items: center; gap: 6px; font-weight:500; }
        .tag-item button { background: none; border: none; color: #1e3a8a; cursor: pointer; font-size: 16px; padding: 0; line-height: 1; display:flex; align-items:center;}
        .tag-item button:hover { color: #dc2626; }

        .btn-primary { background: var(--primary); color: #ffffff; border: none; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-primary:hover { opacity: 0.9; }
        .btn-secondary { background: #ffffff; color: var(--text-dark); border: 1px solid #d1d5db; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-secondary:hover { background: var(--bg-gray); }
        </style>
@endpush

@section('content')
    <div class="profile-container">
        
        <!-- TOP SECTION: HERO & ACTIVITY SUMMARY -->
        <div class="top-grid">
            <!-- HERO CARD (LEFT) -->
            <div class="hero-card">
                <button class="edit-btn" onclick="openModal('modal-edit-profil')"><i class="bi bi-pencil"></i> Edit Profil</button>
                <div class="hero-avatar">
                    <img src="{{ $trainer->avatar_url }}" alt="Profile Photo">
                    <div class="edit-photo" onclick="document.getElementById('photo-upload').click()"><i class="bi bi-pencil-fill"></i></div>
                    <input type="file" id="photo-upload" style="display:none;" accept="image/*" onchange="alert('Data Tersimpan! (Simulasi: Foto profil berhasil diubah)')">
                </div>
                <div class="hero-info">
                    <h1 id="ui-hero-name">{{ $displayFullName }} @if($isVerifiedTrainer) <i class="bi bi-patch-check-fill" title="Verified"></i> @endif</h1>
                    <span id="ui-hero-role" class="role-badge">{{ $displayRole }}</span>
                    <p id="ui-hero-bio" class="hero-bio">{{ \Illuminate\Support\Str::limit($displayBio, 150) }}</p>
                    <div class="hero-meta">
                        <span><i class="bi bi-geo-alt"></i> <span id="ui-hero-location">{{ $displayLocation }}</span></span>
                        <span><i class="bi bi-calendar3"></i> Bergabung sejak {{ $trainer->created_at ? $trainer->created_at->format('M Y') : 'Jan 2023' }}</span>
                    </div>
                    <div class="hero-contacts">
                        <span><i class="bi bi-envelope"></i> <span id="ui-hero-email">{{ $trainer->email }}</span></span>
                        <span><i class="bi bi-telephone"></i> <span id="ui-hero-phone">{{ $trainer->phone ?: '+62 812 3456 7890' }}</span></span>
                    </div>
                </div>
            </div>

            <!-- ACTIVITY SUMMARY (RIGHT) -->
            <div class="sidebar-card">
                <div class="sidebar-header">
                    <h3 class="sidebar-title">Ringkasan Aktivitas</h3>
                    <a href="#" class="view-all-link">Lihat Detail</a>
                </div>
                <div class="activity-grid">
                    <div class="activity-item">
                        <div class="activity-icon primary"><i class="bi bi-calendar-event"></i></div>
                        <div class="activity-text">
                            <h4>12</h4>
                            <p>Event Diampu</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon green"><i class="bi bi-play-btn"></i></div>
                        <div class="activity-text">
                            <h4>24</h4>
                            <p>Course Diampu</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon orange"><i class="bi bi-people"></i></div>
                        <div class="activity-text">
                            <h4>1.250+</h4>
                            <p>Total Peserta</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon blue"><i class="bi bi-person-badge"></i></div>
                        <div class="activity-text">
                            <h4>4.8 <i class="bi bi-star-fill" style="font-size:12px; color:#facc15;"></i></h4>
                            <p>Rating Pengajar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="main-grid">
            
            <!-- LEFT COLUMN: TAB PANELS -->
            <div class="left-content content-section" style="padding: 0; overflow: hidden;">
                <!-- TABS NAV -->
                <div class="profile-tabs">
                    <a href="#" class="tab-item active" onclick="switchTab(event, 'tab-tentang')">
                        <i class="bi bi-person-vcard"></i> Tentang Saya
                    </a>
                    <a href="#" class="tab-item" onclick="switchTab(event, 'tab-keahlian')">
                        <i class="bi bi-lightning"></i> Keahlian
                    </a>
                    <a href="#" class="tab-item" onclick="switchTab(event, 'tab-pengalaman')">
                        <i class="bi bi-briefcase"></i> Pengalaman
                    </a>
                    <a href="#" class="tab-item" onclick="switchTab(event, 'tab-pendidikan')">
                        <i class="bi bi-mortarboard"></i> Pendidikan & Sertifikasi
                    </a>
                    <a href="#" class="tab-item" onclick="switchTab(event, 'tab-ulasan')">
                        <i class="bi bi-star"></i> Ulasan (48)
                    </a>
                </div>
                
                <div style="padding: 32px;">
                <!-- TAB: TENTANG SAYA -->
                <div id="tab-tentang" class="tab-panel active">
                        <div class="section-header-flex">
                            <h3 class="section-title">Tentang Saya</h3>
                            <button class="btn-icon-edit" onclick="openModal('modal-edit-tentang')"><i class="bi bi-pencil"></i></button>
                        </div>
                        <p id="ui-about-text" class="about-text">{{ $displayBio }}</p>

                        <div class="section-header-flex" style="margin-bottom: 12px;">
                            <h3 class="section-title">Spesialisasi Saya</h3>
                            <button class="btn-icon-edit" onclick="openModalSpesialisasi()"><i class="bi bi-pencil"></i></button>
                        </div>
                        <div class="pill-list" id="ui-spesialisasi-list">
                            @if(empty($expertiseTags))
                                <span class="pill active">Leadership</span>
                                <span class="pill active">Team Building</span>
                                <span class="pill active">Communication</span>
                                <span class="pill active">Public Speaking</span>
                                <span class="pill active">Time Management</span>
                            @else
                                @foreach($expertiseTags as $tag)
                                    <span class="pill active">{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>

                        <div class="stat-horizontal-grid">
                            <div class="stat-h-box">
                                <div class="stat-h-icon"><i class="bi bi-person-workspace"></i></div>
                                <div class="stat-h-text">
                                    <p>Total Jam Mengajar</p>
                                    <h4>256 Jam</h4>
                                </div>
                            </div>
                            <div class="stat-h-box">
                                <div class="stat-h-icon" style="color: #3b82f6;"><i class="bi bi-clock-history"></i></div>
                                <div class="stat-h-text">
                                    <p>Tingkat Kepuasan Peserta</p>
                                    <h4>98%</h4>
                                </div>
                            </div>
                            <div class="stat-h-box">
                                <div class="stat-h-icon" style="color: #3b82f6;"><i class="bi bi-journal-text"></i></div>
                                <div class="stat-h-text">
                                    <p>Rata-rata Rating</p>
                                    <h4><i class="bi bi-star-fill" style="font-size:12px; color:#111827;"></i> {{ number_format($averageRating ?? 4.8, 1) }} / 5.0</h4>
                                </div>
                            </div>
                        </div>

                        <div class="section-header-flex" style="margin-bottom: 12px;">
                            <h3 class="section-title">Bahasa</h3>
                            <button class="btn-icon-edit" onclick="openModalBahasa()"><i class="bi bi-pencil"></i></button>
                        </div>
                        <div class="pill-list" style="margin-bottom: 0;" id="ui-bahasa-list">
                            <span class="pill">Bahasa Indonesia (Native)</span>
                            <span class="pill">English (Professional)</span>
                        </div>
                </div>

                <!-- TAB: KEAHLIAN -->
                <div id="tab-keahlian" class="tab-panel">
                        <div class="section-header-flex" style="margin-bottom: 24px;">
                            <div>
                                <h3 class="section-title">Keahlian Saya</h3>
                                <p class="section-subtitle" style="margin-bottom: 0;">Keahlian yang saya kuasai dan sering saya terapkan dalam pelatihan.</p>
                            </div>
                            <button class="btn-outline-primary" onclick="openAddKeahlian()"><i class="bi bi-plus"></i> Tambah Keahlian</button>
                        </div>
                        
                        <div class="expertise-grid" id="ui-keahlian-list">
                            <div class="expertise-card">
                                <div style="position:absolute; top:12px; right:12px;">
                                    <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditKeahlian(this)"><i class="bi bi-pencil"></i></button>
                                </div>
                                <div class="exp-header">
                                    <div class="exp-icon primary"><i class="bi bi-person-fill"></i></div>
                                    <div class="exp-title-box">
                                        <h4 class="skill-name">Leadership</h4>
                                        <p>Ahli</p>
                                    </div>
                                </div>
                                <div class="exp-progress-wrap">
                                    <div class="exp-progress-bar"><div class="exp-progress-fill" style="width: 85%;"></div></div>
                                    <span class="exp-percentage skill-percent">85%</span>
                                </div>
                            </div>
                            <div class="expertise-card">
                                <div style="position:absolute; top:12px; right:12px;">
                                    <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditKeahlian(this)"><i class="bi bi-pencil"></i></button>
                                </div>
                                <div class="exp-header">
                                    <div class="exp-icon primary"><i class="bi bi-chat-dots-fill"></i></div>
                                    <div class="exp-title-box">
                                        <h4 class="skill-name">Communication</h4>
                                        <p>Ahli</p>
                                    </div>
                                </div>
                                <div class="exp-progress-wrap">
                                    <div class="exp-progress-bar"><div class="exp-progress-fill" style="width: 92%;"></div></div>
                                    <span class="exp-percentage skill-percent">92%</span>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- TAB: PENGALAMAN -->
                <div id="tab-pengalaman" class="tab-panel">
                        <div class="section-header-flex">
                            <div>
                                <h3 class="section-title">Pengalaman Mengajar & Profesional</h3>
                                <p class="section-subtitle" style="margin-bottom:0;">Pengalaman saya dalam mengajar dan bekerja di berbagai bidang.</p>
                            </div>
                            <button class="btn-outline-primary" onclick="openAddPengalaman()"><i class="bi bi-plus"></i> Tambah Pengalaman</button>
                        </div>
                        
                        <div class="timeline" id="ui-pengalaman-list">
                            <div class="timeline-item">
                                <div class="timeline-date">Jan 2021<br>- Sekarang</div>
                                <div class="timeline-dot"></div>
                                <div class="timeline-content" style="position:relative;">
                                    <div style="position:absolute; top:0; right:0;">
                                        <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPengalaman(this)"><i class="bi bi-pencil"></i></button>
                                    </div>
                                    <div class="timeline-content-head">
                                        <div>
                                            <h4 class="timeline-role">Senior Trainer & Facilitator</h4>
                                            <p class="timeline-company">PT Inovasi Global Nusantara</p>
                                        </div>
                                        <span class="timeline-duration">3 thn 4 bln</span>
                                    </div>
                                    <p class="timeline-desc">Merancang dan memfasilitasi program pelatihan leadership, komunikasi, dan team building untuk berbagai perusahaan dan instansi.</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-date">Jun 2017<br>- Des 2020</div>
                                <div class="timeline-dot"></div>
                                <div class="timeline-content" style="position:relative;">
                                    <div style="position:absolute; top:0; right:0;">
                                        <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPengalaman(this)"><i class="bi bi-pencil"></i></button>
                                    </div>
                                    <div class="timeline-content-head">
                                        <div>
                                            <h4 class="timeline-role">Training Consultant</h4>
                                            <p class="timeline-company">Bright Future Consulting</p>
                                        </div>
                                        <span class="timeline-duration">3 thn 7 bln</span>
                                    </div>
                                    <p class="timeline-desc">Memberikan konsultasi dan pelatihan pengembangan SDM untuk klien korporat di berbagai industri.</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-date">Jan 2014<br>- Mei 2017</div>
                                <div class="timeline-dot"></div>
                                <div class="timeline-content" style="position:relative;">
                                    <div style="position:absolute; top:0; right:0;">
                                        <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPengalaman(this)"><i class="bi bi-pencil"></i></button>
                                    </div>
                                    <div class="timeline-content-head">
                                        <div>
                                            <h4 class="timeline-role">Corporate Trainer</h4>
                                            <p class="timeline-company">Global Success Institute</p>
                                        </div>
                                        <span class="timeline-duration">3 thn 4 bln</span>
                                    </div>
                                    <p class="timeline-desc">Menyelenggarakan program pelatihan soft skills dan pengembangan kepemimpinan bagi karyawan level staff hingga manajer.</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-date">Jan 2012<br>- Des 2013</div>
                                <div class="timeline-dot"></div>
                                <div class="timeline-content" style="position:relative;">
                                    <div style="position:absolute; top:0; right:0;">
                                        <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPengalaman(this)"><i class="bi bi-pencil"></i></button>
                                    </div>
                                    <div class="timeline-content-head">
                                        <div>
                                            <h4 class="timeline-role">HR Development Staff</h4>
                                            <p class="timeline-company">PT Maju Bersama</p>
                                        </div>
                                        <span class="timeline-duration">3 thn 11 bln</span>
                                    </div>
                                    <p class="timeline-desc">Bertanggung jawab dalam program pengembangan karyawan dan pelatihan internal.</p>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- TAB: PENDIDIKAN & SERTIFIKASI -->
                <div id="tab-pendidikan" class="tab-panel">
                        <div class="edu-cert-grid">
                            <div>
                                <div class="section-header-flex">
                                    <h3 class="section-title">Pendidikan</h3>
                                    <button class="btn-outline-primary" style="padding: 4px 12px; font-size: 12px;" onclick="openAddPendidikan()"><i class="bi bi-plus"></i> Tambah</button>
                                </div>
                                <div class="edu-timeline" id="ui-pendidikan-list">
                                    <div class="edu-timeline-item">
                                        <div class="edu-timeline-dot"></div>
                                        <div class="ec-list-card" style="position:relative;">
                                            <div style="position:absolute; top:8px; right:8px;">
                                                <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPendidikan(this)"><i class="bi bi-pencil"></i></button>
                                            </div>
                                            <div class="ec-icon gold"><i class="bi bi-bank"></i></div>
                                            <div class="ec-info">
                                                <div class="ec-info-head">
                                                    <div>
                                                        <h4 class="ec-title">Universitas Indonesia</h4>
                                                        <div class="ec-year">2010 - 2012</div>
                                                    </div>
                                                </div>
                                                <div class="ec-subtitle" style="margin-top:6px;">Magister Psikologi (M.Psi.)</div>
                                                <p class="ec-desc">Fakultas Psikologi</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="edu-timeline-item">
                                        <div class="edu-timeline-dot"></div>
                                        <div class="ec-list-card" style="position:relative;">
                                            <div style="position:absolute; top:8px; right:8px;">
                                                <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPendidikan(this)"><i class="bi bi-pencil"></i></button>
                                            </div>
                                            <div class="ec-icon blue"><i class="bi bi-book"></i></div>
                                            <div class="ec-info">
                                                <div class="ec-info-head">
                                                    <div>
                                                        <h4 class="ec-title">Universitas Gadjah Mada</h4>
                                                        <div class="ec-year">2006 - 2010</div>
                                                    </div>
                                                </div>
                                                <div class="ec-subtitle" style="margin-top:6px;">Sarjana Psikologi (S.Psi.)</div>
                                                <p class="ec-desc">Fakultas Psikologi</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="edu-timeline-item">
                                        <div class="edu-timeline-dot"></div>
                                        <div class="ec-list-card" style="position:relative;">
                                            <div style="position:absolute; top:8px; right:8px;">
                                                <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPendidikan(this)"><i class="bi bi-pencil"></i></button>
                                            </div>
                                            <div class="ec-icon blue"><i class="bi bi-building"></i></div>
                                            <div class="ec-info">
                                                <div class="ec-info-head">
                                                    <div>
                                                        <h4 class="ec-title">SMA Negeri 1 Yogyakarta</h4>
                                                        <div class="ec-year">2003 - 2006</div>
                                                    </div>
                                                </div>
                                                <div class="ec-subtitle" style="margin-top:6px;">Ilmu Pengetahuan Sosial</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="section-header-flex">
                                    <h3 class="section-title">Sertifikasi</h3>
                                    <button class="btn-outline-primary" style="padding: 4px 12px; font-size: 12px;" onclick="openAddSertifikasi()"><i class="bi bi-plus"></i> Tambah</button>
                                </div>
                                <div class="cert-list" id="ui-sertifikasi-list">
                                    <div class="ec-list-card" style="position:relative;">
                                        <div style="position:absolute; top:8px; right:8px;">
                                            <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditSertifikasi(this)"><i class="bi bi-pencil"></i></button>
                                        </div>
                                        <div class="ec-icon navy"><i class="bi bi-patch-check-fill"></i></div>
                                        <div class="ec-info">
                                            <div class="ec-info-head">
                                                <div>
                                                    <h4 class="ec-title">Certified Professional Trainer (CPT)</h4>
                                                    <div class="ec-year">2023</div>
                                                </div>
                                                <span class="ec-badge green">Aktif</span>
                                            </div>
                                            <div class="ec-subtitle" style="margin-top:6px;">Badan Nasional Sertifikasi Profesi (BNSP)</div>
                                            <p class="ec-desc">Masa Berlaku: 2023 - 2026</p>
                                        </div>
                                    </div>
                                    <div class="ec-list-card" style="position:relative;">
                                        <div style="position:absolute; top:8px; right:8px;">
                                            <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditSertifikasi(this)"><i class="bi bi-pencil"></i></button>
                                        </div>
                                        <div class="ec-icon navy"><i class="bi bi-briefcase-fill"></i></div>
                                        <div class="ec-info">
                                            <div class="ec-info-head">
                                                <div>
                                                    <h4 class="ec-title">Design Thinking for Facilitator</h4>
                                                    <div class="ec-year">2022</div>
                                                </div>
                                                <span class="ec-badge green">Aktif</span>
                                            </div>
                                            <div class="ec-subtitle" style="margin-top:6px;">IDEO U - Online Course</div>
                                            <p class="ec-desc">Masa Berlaku: 2022 - 2025</p>
                                        </div>
                                    </div>
                                    <div class="ec-list-card">
                                        <div class="ec-icon blue"><i class="bi bi-laptop"></i></div>
                                        <div class="ec-info">
                                            <div class="ec-info-head">
                                                <div>
                                                    <h4 class="ec-title">Coaching Skills for Leaders</h4>
                                                    <div class="ec-year">2021</div>
                                                </div>
                                                <span class="ec-badge green">Aktif</span>
                                            </div>
                                            <div class="ec-subtitle" style="margin-top:6px;">Corporate Coach U - Online Course</div>
                                            <p class="ec-desc">Masa Berlaku: 2021 - 2024</p>
                                        </div>
                                    </div>
                                    <div class="ec-list-card">
                                        <div class="ec-icon orange"><i class="bi bi-chat-dots-fill"></i></div>
                                        <div class="ec-info">
                                            <div class="ec-info-head">
                                                <div>
                                                    <h4 class="ec-title">Effective Communication Masterclass</h4>
                                                    <div class="ec-year">2020</div>
                                                </div>
                                                <span class="ec-badge green">Aktif</span>
                                            </div>
                                            <div class="ec-subtitle" style="margin-top:6px;">LinkedIn Learning</div>
                                            <p class="ec-desc">Masa Berlaku: 2020 - 2023</p>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: center; margin-top: 24px;">
                                    <button class="btn-outline-primary" style="width:100%; justify-content:center;">Lihat Semua Sertifikasi (4) <i class="bi bi-arrow-right"></i></button>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- TAB: ULASAN -->
                <div id="tab-ulasan" class="tab-panel">
                        <div class="review-header-flex">
                            <h3 class="review-title">Ulasan Peserta <span style="font-size:18px; color:var(--text-dark);">4.8 <i class="bi bi-star-fill" style="color:#facc15; font-size:14px;"></i></span> <span style="font-size:13px; color:var(--text-muted); font-weight:normal;">(48 ulasan)</span></h3>
                            <div class="review-filters">
                                <select class="filter-select">
                                    <option>Semua Rating</option>
                                    <option>5 Bintang</option>
                                </select>
                                <select class="filter-select">
                                    <option>Terbaru</option>
                                    <option>Terlama</option>
                                </select>
                            </div>
                        </div>

                        <div class="review-list">
                            <div class="review-card">
                                <img src="https://ui-avatars.com/api/?name=Andi+Pratama&background=random" alt="Avatar" class="reviewer-img">
                                <div class="review-content">
                                    <div class="review-head">
                                        <div>
                                            <h4 class="reviewer-name">Andi Pratama</h4>
                                            <p class="reviewer-role">Corporate Manager</p>
                                            <span class="review-date">15 Mei 2024</span>
                                        </div>
                                        <div style="text-align: right;">
                                            <div class="review-rating-badge">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                                <span>5.0</span>
                                            </div>
                                            <!-- option button -->
                                            <i class="bi bi-three-dots-vertical" style="color:var(--text-muted); cursor:pointer; margin-top:4px; display:inline-block;"></i>
                                        </div>
                                    </div>
                                    <p class="review-text">Materi sangat relevan dan aplikatif. Coach Budi menyampaikan dengan jelas, interaktif, dan memberikan banyak insight yang bisa langsung diterapkan di kerjaan.</p>
                                    <span class="review-event-badge">Leadership Training</span>
                                </div>
                            </div>
                            <div class="review-card">
                                <img src="https://ui-avatars.com/api/?name=Siti+Rahmawati&background=random" alt="Avatar" class="reviewer-img">
                                <div class="review-content">
                                    <div class="review-head">
                                        <div>
                                            <h4 class="reviewer-name">Siti Rahmawati</h4>
                                            <p class="reviewer-role">HR Specialist</p>
                                            <span class="review-date">28 Apr 2024</span>
                                        </div>
                                        <div style="text-align: right;">
                                            <div class="review-rating-badge">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                                <span>5.0</span>
                                            </div>
                                            <i class="bi bi-three-dots-vertical" style="color:var(--text-muted); cursor:pointer; margin-top:4px; display:inline-block;"></i>
                                        </div>
                                    </div>
                                    <p class="review-text">Salah satu trainer terbaik yang pernah saya ikuti. Penyampaiannya inspiratif dan mampu membangun diskusi yang sangat bermakna.</p>
                                    <span class="review-event-badge">Team Building Workshop</span>
                                </div>
                            </div>
                            <div class="review-card">
                                <img src="https://ui-avatars.com/api/?name=Dewangga+Putra&background=random" alt="Avatar" class="reviewer-img">
                                <div class="review-content">
                                    <div class="review-head">
                                        <div>
                                            <h4 class="reviewer-name">Dewangga Putra</h4>
                                            <p class="reviewer-role">Project Lead</p>
                                            <span class="review-date">10 Apr 2024</span>
                                        </div>
                                        <div style="text-align: right;">
                                            <div class="review-rating-badge">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-half"></i>
                                                <span>4.5</span>
                                            </div>
                                            <i class="bi bi-three-dots-vertical" style="color:var(--text-muted); cursor:pointer; margin-top:4px; display:inline-block;"></i>
                                        </div>
                                    </div>
                                    <p class="review-text">Penjelasan mudah dipahami dan banyak contoh kasus nyata. Sesi Q&A juga sangat membantu memperjelas materi.</p>
                                    <span class="review-event-badge">Public Speaking Masterclass</span>
                                </div>
                            </div>
                            <div class="review-card">
                                <img src="https://ui-avatars.com/api/?name=Nadia+Aulia&background=random" alt="Avatar" class="reviewer-img">
                                <div class="review-content">
                                    <div class="review-head">
                                        <div>
                                            <h4 class="reviewer-name">Nadia Aulia</h4>
                                            <p class="reviewer-role">Marketing Executive</p>
                                            <span class="review-date">03 Apr 2024</span>
                                        </div>
                                        <div style="text-align: right;">
                                            <div class="review-rating-badge">
                                                <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                                <span>5.0</span>
                                            </div>
                                            <i class="bi bi-three-dots-vertical" style="color:var(--text-muted); cursor:pointer; margin-top:4px; display:inline-block;"></i>
                                        </div>
                                    </div>
                                    <p class="review-text">Workshop yang sangat berkesan! Banyak tools praktis yang bisa digunakan untuk meningkatkan komunikasi dalam tim.</p>
                                    <span class="review-event-badge">Effective Communication</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="review-pagination">
                            <div class="page-btn"><i class="bi bi-chevron-left"></i></div>
                            <div class="page-btn active">1</div>
                            <div class="page-btn">2</div>
                            <div class="page-btn">3</div>
                            <div class="page-btn">4</div>
                            <div class="page-btn">5</div>
                            <div class="page-btn"><i class="bi bi-chevron-right"></i></div>
                        </div>
                </div>
                </div> <!-- END PADDING WRAPPER -->

            </div> <!-- END LEFT COLUMN -->

            <!-- RIGHT COLUMN: SIDEBARS -->
            <div class="right-content">
                
                <!-- PENCAPAIAN SIDEBAR (Visible by default except on Ulasan) -->
                <div id="sidebar-pencapaian" class="sidebar-card">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">Pencapaian</h3>
                        <a href="#" class="view-all-link">Lihat Semua</a>
                    </div>
                    <div class="achievement-list">
                        <div class="achievement-item">
                            <div class="achieve-icon primary"><i class="bi bi-trophy-fill"></i></div>
                            <div class="achieve-info">
                                <div class="achieve-info-head">
                                    <h4 class="achieve-title">Top Rated Trainer</h4>
                                    <span class="achieve-date">Mei 2024</span>
                                </div>
                                <p class="achieve-desc">Mendapatkan rating tertinggi dari peserta</p>
                            </div>
                        </div>
                        <div class="achievement-item">
                            <div class="achieve-icon green"><i class="bi bi-patch-check-fill"></i></div>
                            <div class="achieve-info">
                                <div class="achieve-info-head">
                                    <h4 class="achieve-title">1000+ Peserta</h4>
                                    <span class="achieve-date">Mar 2024</span>
                                </div>
                                <p class="achieve-desc">Telah mengajar lebih dari 1000 peserta</p>
                            </div>
                        </div>
                        <div class="achievement-item">
                            <div class="achieve-icon gold"><i class="bi bi-star-fill"></i></div>
                            <div class="achieve-info">
                                <div class="achieve-info-head">
                                    <h4 class="achieve-title">Elite Trainer</h4>
                                    <span class="achieve-date">Jan 2024</span>
                                </div>
                                <p class="achieve-desc">Masuk dalam 10% trainer terbaik</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RINGKASAN PENILAIAN SIDEBAR (Visible ONLY on Ulasan tab) -->
                <div id="sidebar-penilaian" class="sidebar-card" style="display: none;">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">Ringkasan Penilaian</h3>
                    </div>
                    
                    <div class="rating-sidebar-big">
                        <h2>4.8 <i class="bi bi-star-fill"></i></h2>
                        <p>Dari 48 ulasan</p>
                    </div>

                    <div class="rating-bars">
                        <div class="rating-bar-row">
                            <span>5 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap"><div class="rating-bar-fill" style="width: 75%;"></div></div>
                            <span class="rating-count">36</span>
                            <span class="rating-pct">(75%)</span>
                        </div>
                        <div class="rating-bar-row">
                            <span>4 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap"><div class="rating-bar-fill" style="width: 21%;"></div></div>
                            <span class="rating-count">10</span>
                            <span class="rating-pct">(21%)</span>
                        </div>
                        <div class="rating-bar-row">
                            <span>3 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap"><div class="rating-bar-fill" style="width: 4%;"></div></div>
                            <span class="rating-count">2</span>
                            <span class="rating-pct">(4%)</span>
                        </div>
                        <div class="rating-bar-row">
                            <span>2 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap"><div class="rating-bar-fill" style="width: 0%;"></div></div>
                            <span class="rating-count">0</span>
                            <span class="rating-pct">(0%)</span>
                        </div>
                        <div class="rating-bar-row">
                            <span>1 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap"><div class="rating-bar-fill" style="width: 0%;"></div></div>
                            <span class="rating-count">0</span>
                            <span class="rating-pct">(0%)</span>
                        </div>
                    </div>

                    <div class="rating-aspects">
                        <h4 class="aspect-title">Aspek Penilaian</h4>
                        <div class="aspect-row">
                            <span><i class="bi bi-journal-text"></i> Penyampaian Materi</span>
                            <span>4.9</span>
                        </div>
                        <div class="aspect-row">
                            <span><i class="bi bi-book"></i> Penguasaan Materi</span>
                            <span>4.8</span>
                        </div>
                        <div class="aspect-row">
                            <span><i class="bi bi-people"></i> Interaktivitas</span>
                            <span>4.8</span>
                        </div>
                        <div class="aspect-row">
                            <span><i class="bi bi-lightbulb"></i> Manfaat & Aplikasi</span>
                            <span>4.7</span>
                        </div>
                    </div>

                    <button class="btn-write-review">Tulis Ulasan</button>
                </div>

            </div> <!-- END RIGHT COLUMN -->

        </div> <!-- END MAIN GRID -->

        <!-- EVENT & COURSE TERBARU (Bottom section) -->
        <div class="content-section" style="margin-bottom: 0;">
            <div class="section-header-flex">
                <h3 class="section-title">Event & Course Terbaru</h3>
                <a href="#" class="view-all-link">Lihat Semua</a>
            </div>
            
            <div class="event-course-grid">
                <div class="ec-card">
                    <div class="ec-img-wrap">
                        <span class="ec-badge-overlay event">EVENT</span>
                        <img src="https://images.unsplash.com/photo-1544531586-fde5298cdd40?w=500&q=80" alt="Event" class="ec-img">
                    </div>
                    <div class="ec-body">
                        <h4 class="ec-title">Leadership Camp 2024</h4>
                        <div class="ec-meta">
                            <span><i class="bi bi-calendar3"></i> 20-22 Mei 2024</span>
                            <span><i class="bi bi-people"></i> 150 Peserta</span>
                        </div>
                    </div>
                </div>
                <div class="ec-card">
                    <div class="ec-img-wrap">
                        <span class="ec-badge-overlay course">COURSE</span>
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&q=80" alt="Course" class="ec-img">
                    </div>
                    <div class="ec-body">
                        <h4 class="ec-title">Effective Communication Masterclass</h4>
                        <div class="ec-meta">
                            <span><i class="bi bi-calendar3"></i> 15 Mei 2024</span>
                            <span><i class="bi bi-people"></i> 85 Peserta</span>
                        </div>
                    </div>
                </div>
                <div class="ec-card">
                    <div class="ec-img-wrap">
                        <span class="ec-badge-overlay course">COURSE</span>
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=500&q=80" alt="Course" class="ec-img">
                    </div>
                    <div class="ec-body">
                        <h4 class="ec-title">Team Building Fundamentals</h4>
                        <div class="ec-meta">
                            <span><i class="bi bi-calendar3"></i> 10 Mei 2024</span>
                            <span><i class="bi bi-people"></i> 120 Peserta</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ---------------- HTML MODALS ---------------- -->
    
    <!-- Modal Edit Profil -->
    <div id="modal-edit-profil" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-profil')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Profil Utama</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-profil')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" id="input-hero-name" class="form-control" value="{{ $displayFullName }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Jabatan / Peran</label>
                    <input type="text" id="input-hero-role" class="form-control" value="{{ $displayRole }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Lokasi</label>
                    <input type="text" id="input-hero-location" class="form-control" value="{{ $displayLocation }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Bio Singkat</label>
                    <textarea id="input-hero-bio" class="form-control">{{ $displayBio }}</textarea>
                </div>
                <div style="display:flex; gap:16px;">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Email</label>
                        <input type="email" id="input-hero-email" class="form-control" value="{{ $trainer->email }}">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" id="input-hero-phone" class="form-control" value="{{ $trainer->phone ?: '+62 812 3456 7890' }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-profil')">Batal</button>
                <button class="btn-primary" onclick="saveHeroProfil()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Tentang Saya -->
    <div id="modal-edit-tentang" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-tentang')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Tentang Saya</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-tentang')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Deskripsi Tentang Saya</label>
                    <textarea id="input-tentang-bio" class="form-control">{{ $displayBio }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-tentang')">Batal</button>
                <button class="btn-primary" onclick="saveTentang()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Spesialisasi -->
    <div id="modal-edit-spesialisasi" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-spesialisasi')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Spesialisasi</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-spesialisasi')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Spesialisasi</label>
                    <div class="tag-input-container" onclick="document.getElementById('input-tag-spesialisasi').focus()">
                        <div id="tag-list-spesialisasi" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
                        <input type="text" id="input-tag-spesialisasi" placeholder="Ketik spesialisasi lalu tekan Enter..." style="border:none; outline:none; flex:1; min-width:120px;" onkeydown="handleTagInput(event, 'spesialisasi')">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-spesialisasi')">Batal</button>
                <button class="btn-primary" onclick="saveSpesialisasi()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Bahasa -->
    <div id="modal-edit-bahasa" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-bahasa')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Bahasa</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-bahasa')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Bahasa</label>
                    <div class="tag-input-container" onclick="document.getElementById('input-tag-bahasa').focus()">
                        <div id="tag-list-bahasa" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
                        <input type="text" id="input-tag-bahasa" placeholder="Ketik bahasa lalu tekan Enter..." style="border:none; outline:none; flex:1; min-width:120px;" onkeydown="handleTagInput(event, 'bahasa')">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-bahasa')">Batal</button>
                <button class="btn-primary" onclick="saveBahasa()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Keahlian -->
    <div id="modal-edit-keahlian" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-keahlian')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Keahlian</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-keahlian')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Keahlian Baru</label>
                    <input type="text" id="input-keahlian-nama" class="form-control" placeholder="Contoh: Problem Solving">
                </div>
                <div class="form-group">
                    <label class="form-label">Tingkat Penguasaan (%)</label>
                    <input type="number" id="input-keahlian-persen" class="form-control" placeholder="85">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-keahlian')">Batal</button>
                <button class="btn-primary" onclick="saveKeahlian()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Pengalaman -->
    <div id="modal-edit-pengalaman" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-pengalaman')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Tambah/Edit Pengalaman</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-pengalaman')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Jabatan / Posisi</label>
                    <input type="text" id="input-pengalaman-jabatan" class="form-control" placeholder="Contoh: Senior Trainer">
                </div>
                <div class="form-group">
                    <label class="form-label">Nama Perusahaan / Organisasi</label>
                    <input type="text" id="input-pengalaman-perusahaan" class="form-control" placeholder="Contoh: PT Sukses Selalu">
                </div>
                <div style="display:flex; gap:16px;">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Bulan & Tahun Mulai</label>
                        <input type="month" id="input-pengalaman-mulai" class="form-control">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Bulan & Tahun Selesai</label>
                        <input type="month" id="input-pengalaman-selesai" class="form-control">
                        <small style="color:var(--text-muted); font-size:12px;">Kosongkan jika masih bekerja di sini</small>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Deskripsi Pekerjaan</label>
                    <textarea id="input-pengalaman-deskripsi" class="form-control" placeholder="Jelaskan peran Anda..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-pengalaman')">Batal</button>
                <button class="btn-primary" onclick="savePengalaman()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Pendidikan -->
    <div id="modal-edit-pendidikan" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-pendidikan')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Pendidikan</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-pendidikan')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Institusi / Universitas</label>
                    <input type="text" id="input-pendidikan-kampus" class="form-control" placeholder="Contoh: Universitas Indonesia">
                </div>
                <div class="form-group">
                    <label class="form-label">Gelar / Tingkat / Jurusan</label>
                    <input type="text" id="input-pendidikan-gelar" class="form-control" placeholder="Contoh: S2 Magister Manajemen">
                </div>
                <div style="display:flex; gap:16px;">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Tahun Masuk</label>
                        <input type="number" id="input-pendidikan-mulai" class="form-control" placeholder="YYYY" min="1950" max="2050">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Tahun Lulus</label>
                        <input type="number" id="input-pendidikan-selesai" class="form-control" placeholder="YYYY" min="1950" max="2050">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-pendidikan')">Batal</button>
                <button class="btn-primary" onclick="savePendidikan()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Sertifikasi -->
    <div id="modal-edit-sertifikasi" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-sertifikasi')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Sertifikasi</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-sertifikasi')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Nama Sertifikasi</label>
                    <input type="text" id="input-sertifikasi-nama" class="form-control" placeholder="Contoh: Certified Professional Trainer">
                </div>
                <div class="form-group">
                    <label class="form-label">Penerbit (Publisher)</label>
                    <input type="text" id="input-sertifikasi-penerbit" class="form-control" placeholder="Contoh: BNSP">
                </div>
                <div style="display:flex; gap:16px;">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Mulai Berlaku</label>
                        <input type="month" id="input-sertifikasi-mulai" class="form-control">
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Berakhir Pada</label>
                        <input type="month" id="input-sertifikasi-selesai" class="form-control">
                        <small style="color:var(--text-muted); font-size:12px;">Kosongkan jika berlaku selamanya</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-sertifikasi')">Batal</button>
                <button class="btn-primary" onclick="saveSertifikasi()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Script for Tab Switching & Sidebar Toggling & Modals -->
    <script>
        // Modal Logic
        function openModal(modalId, title = '') {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                if(title && document.getElementById('modal-section-title')) {
                    document.getElementById('modal-section-title').innerText = 'Edit ' + title;
                }
            }
        }
        
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if(modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        function closeModalOutside(event, modalId) {
            if(event.target.id === modalId) {
                closeModal(modalId);
            }
        }

        // --- Advanced Mock Save Functions for Simulation ---
        let currentEditCard = null;

        function saveHeroProfil() {
            if(document.getElementById('input-hero-name')) {
                document.getElementById('ui-hero-name').innerHTML = document.getElementById('input-hero-name').value + ' @if($isVerifiedTrainer) <i class="bi bi-patch-check-fill" title="Verified"></i> @endif';
                document.getElementById('ui-hero-role').innerText = document.getElementById('input-hero-role').value;
                document.getElementById('ui-hero-location').innerText = document.getElementById('input-hero-location').value;
                document.getElementById('ui-hero-bio').innerText = document.getElementById('input-hero-bio').value;
                document.getElementById('ui-hero-email').innerText = document.getElementById('input-hero-email').value;
                document.getElementById('ui-hero-phone').innerText = document.getElementById('input-hero-phone').value;
            }
            closeModal('modal-edit-profil');
        }

        function saveTentang() {
            if(document.getElementById('input-tentang-bio')) {
                document.getElementById('ui-about-text').innerText = document.getElementById('input-tentang-bio').value;
            }
            closeModal('modal-edit-tentang');
        }

        let activeTags = { spesialisasi: [], bahasa: [] };

        function renderTags(type) {
            const list = document.getElementById(`tag-list-${type}`);
            if(list) {
                list.innerHTML = '';
                activeTags[type].forEach((tag, idx) => {
                    list.innerHTML += `<div class="tag-item"><span>${tag}</span><button type="button" onclick="removeTag('${type}', ${idx})">&times;</button></div>`;
                });
            }
        }

        function handleTagInput(event, type) {
            if(event.key === 'Enter') {
                event.preventDefault();
                const val = event.target.value.trim();
                if(val && !activeTags[type].includes(val)) {
                    activeTags[type].push(val);
                    renderTags(type);
                }
                event.target.value = '';
            }
        }

        function removeTag(type, idx) {
            activeTags[type].splice(idx, 1);
            renderTags(type);
        }

        function openModalSpesialisasi() {
            const container = document.getElementById('ui-spesialisasi-list');
            activeTags.spesialisasi = Array.from(container.querySelectorAll('.pill')).map(p => p.innerText);
            renderTags('spesialisasi');
            openModal('modal-edit-spesialisasi');
        }

        function saveSpesialisasi() {
            const container = document.getElementById('ui-spesialisasi-list');
            if(container) {
                container.innerHTML = '';
                activeTags.spesialisasi.forEach(tag => {
                    container.innerHTML += `<span class="pill active">${tag}</span>`;
                });
            }
            closeModal('modal-edit-spesialisasi');
        }

        function openModalBahasa() {
            const container = document.getElementById('ui-bahasa-list');
            activeTags.bahasa = Array.from(container.querySelectorAll('.pill')).map(p => p.innerText);
            renderTags('bahasa');
            openModal('modal-edit-bahasa');
        }

        function saveBahasa() {
            const container = document.getElementById('ui-bahasa-list');
            if(container) {
                container.innerHTML = '';
                activeTags.bahasa.forEach(tag => {
                    container.innerHTML += `<span class="pill">${tag}</span>`;
                });
            }
            closeModal('modal-edit-bahasa');
        }

        // Keahlian
        function openAddKeahlian() {
            currentEditCard = null;
            document.getElementById('input-keahlian-nama').value = '';
            document.getElementById('input-keahlian-persen').value = '';
            openModal('modal-edit-keahlian', 'Tambah Keahlian');
        }
        function openEditKeahlian(btn) {
            currentEditCard = btn.closest('.expertise-card');
            document.getElementById('input-keahlian-nama').value = currentEditCard.querySelector('.skill-name').innerText;
            document.getElementById('input-keahlian-persen').value = currentEditCard.querySelector('.skill-percent').innerText.replace('%','');
            openModal('modal-edit-keahlian', 'Edit Keahlian');
        }
        function saveKeahlian() {
            const nama = document.getElementById('input-keahlian-nama').value;
            const persen = document.getElementById('input-keahlian-persen').value || 0;
            if(!nama) return;
            if(currentEditCard) {
                currentEditCard.querySelector('.skill-name').innerText = nama;
                currentEditCard.querySelector('.skill-percent').innerText = persen + '%';
                currentEditCard.querySelector('.exp-progress-fill').style.width = persen + '%';
            } else {
                const html = `<div class="expertise-card">
                    <div style="position:absolute; top:12px; right:12px;">
                        <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditKeahlian(this)"><i class="bi bi-pencil"></i></button>
                    </div>
                    <div class="exp-header">
                        <div class="exp-icon primary"><i class="bi bi-star-fill"></i></div>
                        <div class="exp-title-box"><h4 class="skill-name">${nama}</h4><p>Ahli</p></div>
                    </div>
                    <div class="exp-progress-wrap">
                        <div class="exp-progress-bar"><div class="exp-progress-fill" style="width: ${persen}%;"></div></div>
                        <span class="exp-percentage skill-percent">${persen}%</span>
                    </div>
                </div>`;
                document.getElementById('ui-keahlian-list').insertAdjacentHTML('afterbegin', html);
            }
            closeModal('modal-edit-keahlian');
        }

        // Utility Functions for Dates
        function formatMonthString(val) {
            if(!val) return 'Sekarang';
            const parts = val.split('-');
            if(parts.length !== 2) return val;
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${months[parseInt(parts[1])-1]} ${parts[0]}`;
        }
        function parseMonthString(str) {
            if(!str || str.toLowerCase().includes('sekarang')) return '';
            str = str.replace('-', '').trim();
            const parts = str.split(' ');
            if(parts.length !== 2) return '';
            const months = { 'Jan':'01', 'Feb':'02', 'Mar':'03', 'Apr':'04', 'Mei':'05', 'Jun':'06', 'Jul':'07', 'Ags':'08', 'Sep':'09', 'Okt':'10', 'Nov':'11', 'Des':'12' };
            const mm = months[parts[0]] || '01';
            return `${parts[1]}-${mm}`;
        }

        // Pengalaman
        function openAddPengalaman() {
            currentEditCard = null;
            document.getElementById('input-pengalaman-jabatan').value = '';
            document.getElementById('input-pengalaman-perusahaan').value = '';
            document.getElementById('input-pengalaman-mulai').value = '';
            document.getElementById('input-pengalaman-selesai').value = '';
            document.getElementById('input-pengalaman-deskripsi').value = '';
            openModal('modal-edit-pengalaman', 'Tambah Pengalaman');
        }
        function openEditPengalaman(btn) {
            currentEditCard = btn.closest('.timeline-item');
            document.getElementById('input-pengalaman-jabatan').value = currentEditCard.querySelector('.timeline-role').innerText;
            document.getElementById('input-pengalaman-perusahaan').value = currentEditCard.querySelector('.timeline-company').innerText;
            const dates = currentEditCard.querySelector('.timeline-date').innerText.split('-');
            document.getElementById('input-pengalaman-mulai').value = parseMonthString(dates[0] ? dates[0].trim() : '');
            document.getElementById('input-pengalaman-selesai').value = parseMonthString(dates[1] ? dates[1].trim() : '');
            document.getElementById('input-pengalaman-deskripsi').value = currentEditCard.querySelector('.timeline-desc').innerText;
            openModal('modal-edit-pengalaman', 'Edit Pengalaman');
        }
        function savePengalaman() {
            const jabatan = document.getElementById('input-pengalaman-jabatan').value;
            const perusahaan = document.getElementById('input-pengalaman-perusahaan').value;
            const mulai = document.getElementById('input-pengalaman-mulai').value;
            const selesai = document.getElementById('input-pengalaman-selesai').value;
            const deskripsi = document.getElementById('input-pengalaman-deskripsi').value;
            if(!jabatan) return;
            const mulaiText = formatMonthString(mulai);
            const selesaiText = selesai ? formatMonthString(selesai) : 'Sekarang';

            if(currentEditCard) {
                currentEditCard.querySelector('.timeline-role').innerText = jabatan;
                currentEditCard.querySelector('.timeline-company').innerText = perusahaan;
                currentEditCard.querySelector('.timeline-date').innerHTML = `${mulaiText}<br>- ${selesaiText}`;
                currentEditCard.querySelector('.timeline-desc').innerText = deskripsi;
            } else {
                const html = `<div class="timeline-item">
                    <div class="timeline-date">${mulaiText}<br>- ${selesaiText}</div>
                    <div class="timeline-dot"></div>
                    <div class="timeline-content" style="position:relative;">
                        <div style="position:absolute; top:0; right:0;">
                            <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPengalaman(this)"><i class="bi bi-pencil"></i></button>
                        </div>
                        <div class="timeline-content-head">
                            <div>
                                <h4 class="timeline-role">${jabatan}</h4>
                                <p class="timeline-company">${perusahaan}</p>
                            </div>
                            <span class="timeline-duration">Baru</span>
                        </div>
                        <p class="timeline-desc">${deskripsi}</p>
                    </div>
                </div>`;
                document.getElementById('ui-pengalaman-list').insertAdjacentHTML('afterbegin', html);
            }
            closeModal('modal-edit-pengalaman');
        }

        // Pendidikan
        function openAddPendidikan() {
            currentEditCard = null;
            document.getElementById('input-pendidikan-kampus').value = '';
            document.getElementById('input-pendidikan-gelar').value = '';
            document.getElementById('input-pendidikan-mulai').value = '';
            document.getElementById('input-pendidikan-selesai').value = '';
            openModal('modal-edit-pendidikan', 'Tambah Pendidikan');
        }
        function openEditPendidikan(btn) {
            currentEditCard = btn.closest('.edu-timeline-item');
            document.getElementById('input-pendidikan-kampus').value = currentEditCard.querySelector('.ec-title').innerText;
            document.getElementById('input-pendidikan-gelar').value = currentEditCard.querySelector('.ec-subtitle').innerText;
            const dates = currentEditCard.querySelector('.ec-year').innerText.split('-');
            document.getElementById('input-pendidikan-mulai').value = dates[0] ? dates[0].trim() : '';
            document.getElementById('input-pendidikan-selesai').value = dates[1] ? dates[1].trim() : '';
            openModal('modal-edit-pendidikan', 'Edit Pendidikan');
        }
        function savePendidikan() {
            const kampus = document.getElementById('input-pendidikan-kampus').value;
            const gelar = document.getElementById('input-pendidikan-gelar').value;
            const mulai = document.getElementById('input-pendidikan-mulai').value;
            const selesai = document.getElementById('input-pendidikan-selesai').value;
            const tahunText = selesai ? `${mulai} - ${selesai}` : mulai;
            if(!kampus) return;
            if(currentEditCard) {
                currentEditCard.querySelector('.ec-title').innerText = kampus;
                currentEditCard.querySelector('.ec-subtitle').innerText = gelar;
                currentEditCard.querySelector('.ec-year').innerText = tahunText;
            } else {
                const html = `<div class="edu-timeline-item">
                    <div class="edu-timeline-dot"></div>
                    <div class="ec-list-card" style="position:relative;">
                        <div style="position:absolute; top:8px; right:8px;">
                            <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditPendidikan(this)"><i class="bi bi-pencil"></i></button>
                        </div>
                        <div class="ec-icon gold"><i class="bi bi-bank"></i></div>
                        <div class="ec-info">
                            <div class="ec-info-head">
                                <div>
                                    <h4 class="ec-title">${kampus}</h4>
                                    <div class="ec-year">${tahunText}</div>
                                </div>
                            </div>
                            <div class="ec-subtitle" style="margin-top:6px;">${gelar}</div>
                        </div>
                    </div>
                </div>`;
                document.getElementById('ui-pendidikan-list').insertAdjacentHTML('afterbegin', html);
            }
            closeModal('modal-edit-pendidikan');
        }

        // Sertifikasi
        function openAddSertifikasi() {
            currentEditCard = null;
            document.getElementById('input-sertifikasi-nama').value = '';
            document.getElementById('input-sertifikasi-penerbit').value = '';
            document.getElementById('input-sertifikasi-mulai').value = '';
            document.getElementById('input-sertifikasi-selesai').value = '';
            openModal('modal-edit-sertifikasi', 'Tambah Sertifikasi');
        }
        function openEditSertifikasi(btn) {
            currentEditCard = btn.closest('.ec-list-card');
            document.getElementById('input-sertifikasi-nama').value = currentEditCard.querySelector('.ec-title').innerText;
            document.getElementById('input-sertifikasi-penerbit').value = currentEditCard.querySelector('.ec-subtitle').innerText;
            
            const descEl = currentEditCard.querySelector('.ec-desc');
            const desc = descEl ? descEl.innerText : '';
            const dates = desc.replace('Masa Berlaku:', '').trim().split('-');
            
            let mulaiFormat = '';
            let selesaiFormat = '';
            if(dates[0]) {
                const parts = dates[0].trim().split(' ');
                if(parts.length===1) { mulaiFormat = `${parts[0]}-01`; } // Only year
                else { mulaiFormat = parseMonthString(dates[0]); }
            }
            if(dates[1]) {
                const parts = dates[1].trim().split(' ');
                if(parts.length===1) { selesaiFormat = `${parts[0]}-12`; }
                else { selesaiFormat = parseMonthString(dates[1]); }
            }
            
            document.getElementById('input-sertifikasi-mulai').value = mulaiFormat;
            document.getElementById('input-sertifikasi-selesai').value = selesaiFormat;
            openModal('modal-edit-sertifikasi', 'Edit Sertifikasi');
        }
        function saveSertifikasi() {
            const nama = document.getElementById('input-sertifikasi-nama').value;
            const penerbit = document.getElementById('input-sertifikasi-penerbit').value;
            const mulai = document.getElementById('input-sertifikasi-mulai').value;
            const selesai = document.getElementById('input-sertifikasi-selesai').value;
            if(!nama) return;
            
            const mulaiText = formatMonthString(mulai);
            const selesaiText = selesai ? formatMonthString(selesai) : '';
            const tahunText = mulai.split('-')[0] || '';
            const masaBerlaku = selesaiText ? `Masa Berlaku: ${mulaiText} - ${selesaiText}` : (mulaiText ? `Berlaku sejak: ${mulaiText}` : '');

            if(currentEditCard) {
                currentEditCard.querySelector('.ec-title').innerText = nama;
                currentEditCard.querySelector('.ec-subtitle').innerText = penerbit;
                currentEditCard.querySelector('.ec-year').innerText = tahunText;
                let descEl = currentEditCard.querySelector('.ec-desc');
                if(!descEl) {
                    descEl = document.createElement('p');
                    descEl.className = 'ec-desc';
                    currentEditCard.querySelector('.ec-info').appendChild(descEl);
                }
                descEl.innerText = masaBerlaku;
            } else {
                const html = `<div class="ec-list-card" style="position:relative;">
                    <div style="position:absolute; top:8px; right:8px;">
                        <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;" onclick="openEditSertifikasi(this)"><i class="bi bi-pencil"></i></button>
                    </div>
                    <div class="ec-icon navy"><i class="bi bi-patch-check-fill"></i></div>
                    <div class="ec-info">
                        <div class="ec-info-head">
                            <div>
                                <h4 class="ec-title">${nama}</h4>
                                <div class="ec-year">${tahunText}</div>
                            </div>
                            <span class="ec-badge green">Aktif</span>
                        </div>
                        <div class="ec-subtitle" style="margin-top:6px;">${penerbit}</div>
                        ${masaBerlaku ? `<p class="ec-desc">${masaBerlaku}</p>` : ''}
                    </div>
                </div>`;
                document.getElementById('ui-sertifikasi-list').insertAdjacentHTML('afterbegin', html);
            }
            closeModal('modal-edit-sertifikasi');
        }

        function switchTab(event, tabId) {
            event.preventDefault();
            
            // Remove active class from all tab links
            document.querySelectorAll('.tab-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked tab link
            event.currentTarget.classList.add('active');
            
            // Hide all tab panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            
            // Show target panel
            document.getElementById(tabId).classList.add('active');

            // Handle Right Sidebar Visibility
            const sidebarPencapaian = document.getElementById('sidebar-pencapaian');
            const sidebarPenilaian = document.getElementById('sidebar-penilaian');
            
            if (tabId === 'tab-ulasan') {
                sidebarPencapaian.style.display = 'none';
                sidebarPenilaian.style.display = 'block';
            } else {
                sidebarPencapaian.style.display = 'block';
                sidebarPenilaian.style.display = 'none';
            }
        }
    </script>
@endsection