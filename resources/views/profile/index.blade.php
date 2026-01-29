<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - idSPORA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2Pkf3BD3vO5e5pSxb6YV9jwWTA/gG05Jg9TLEbiFU6BxZ1S3XmGmGC3w9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        

        /* Profile page: centered, not full-width */
        .profile-container {
            max-width: 1040px;
            margin: 0 auto;
            padding: 1.25rem 1rem 2.75rem;
        }

        .profile-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
        }

        .profile-soft-card {
            background: #ffffff;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
        }

        .profile-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.8rem;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #fff;
            font-weight: 600;
            font-size: 0.85rem;
            color: #111827;
            text-decoration: none;
        }

        .profile-pill:disabled,
        .profile-pill.disabled {
            opacity: 0.55;
            pointer-events: none;
        }

        .points-chip {
            background: #eef2ff;
            border: 1px solid #e0e7ff;
            color: #4f46e5;
            border-radius: 16px;
            padding: 1rem 1.25rem;
            min-width: 140px;
        }

        .progress-track {
            height: 8px;
            background: #eef2ff;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 999px;
        }

        .badge-tile {
            border-radius: 18px;
            border: 1px solid #eef2f7;
            background: #fbfdff;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }

        .badge-tile.active {
            border-color: #c7d2fe;
            background: #eef2ff;
        }

        .badge-tile:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }
        
        /* Consistent spacing for fixed-top navbar */
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            padding-top: 85px; 
        }
        
        /* Card styling with light background */
        .glass-card {
            background: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
        }
        
        .glass-sidebar {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 90px;
            left: 0;
            height: fit-content;
            max-height: calc(100vh - 100px);
            overflow-y: auto;
            z-index: 100;
            width: 280px;
            margin: 2rem 0 2rem 2rem;
        }
        
        .sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        /* Main content with sidebar offset */
        .main-content-with-sidebar {
            padding: 2rem;
            flex: 1;
        }
        
        /* Container untuk sidebar dan content */
        .flex.min-h-screen {
            align-items: flex-start;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            body {
                padding-top: 70px;
            }
            .flex.min-h-screen {
                flex-direction: column;
            }
            .glass-sidebar {
                position: relative;
                width: 100%;
                height: auto;
                top: 0;
                margin: 1rem;
                max-height: none;
            }
            .main-content-with-sidebar {
                margin-left: 0;
                margin-top: 0;
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .glass-card {
                padding: 1.5rem !important;
            }
            .glass-card h1 {
                font-size: 1.75rem !important;
            }
            .glass-card h2 {
                font-size: 1.5rem !important;
            }
            .profile-img-wrapper img {
                width: 80px !important;
                height: 80px !important;
            }
            .flex.items-start.space-x-6 {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .flex.items-start.space-x-6 > * {
                margin-bottom: 1rem;
            }
            .grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            .menu-item {
                padding: 0.75rem !important;
            }
            .menu-item .w-10 {
                width: 2rem !important;
                height: 2rem !important;
            }
            .menu-item .font-semibold {
                font-size: 0.875rem !important;
            }
            .menu-item .text-xs {
                font-size: 0.75rem !important;
            }
            
            /* Profile Completion Widget Responsive */
            .completion-card-compact {
                padding: 1rem;
            }
            
            .completion-content-compact {
                flex-wrap: wrap;
                gap: 0.75rem;
            }
            
            .completion-icon-compact {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .completion-label-compact {
                font-size: 0.75rem;
            }
            
            .completion-percentage-compact {
                font-size: 0.8rem;
                min-width: 35px;
            }
        }
        
        @media (max-width: 576px) {
            .glass-card {
                padding: 1rem !important;
            }
            .glass-card h1 {
                font-size: 1.5rem !important;
            }
            .glass-card h2 {
                font-size: 1.25rem !important;
            }
            .profile-img-wrapper img {
                width: 60px !important;
                height: 60px !important;
            }
            .premium-badge {
                font-size: 0.625rem !important;
                padding: 0.25rem 0.75rem !important;
            }
            
            /* Profile Completion Widget Mobile */
            .completion-card-compact {
                padding: 0.875rem;
            }
            
            .completion-content-compact {
                gap: 0.625rem;
            }
            
            .completion-icon-compact {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }
            
            .completion-label-compact {
                font-size: 0.7rem;
                margin-bottom: 0.25rem;
            }
            
            .completion-progress-track-compact {
                height: 5px;
            }
            
            .completion-percentage-compact {
                font-size: 0.75rem;
                min-width: 32px;
            }
            
            .completion-link-compact,
            .completion-check-compact {
                width: 28px;
                height: 28px;
                font-size: 0.9rem;
            }
        }
        
        /* Input styling with light theme */
        .neu-input {
            background: white;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .neu-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
            outline: none;
        }
        
        /* Premium Gold Accent */
        .gold-accent {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        }
        
        .gold-accent:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
            transform: translateY(-1px);
        }
        
        /* Sidebar Menu Item - Minimalist Design */
        .menu-item {
            transition: all 0.2s ease;
            color: #374151;
            text-decoration: none;
        }
        
        .menu-item:hover:not(.active) {
            background-color: #f9fafb;
        }
        
        .menu-item.active {
            background-color: #eff6ff;
            color: #2563eb;
            border-left-color: #2563eb !important;
        }
        
        /* Premium Badge */
        .premium-badge {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 2px 10px rgba(251, 191, 36, 0.3);
        }
        
        /* Profile Image Overlay */
        .profile-img-wrapper {
            position: relative;
            display: inline-block;
        }
        
        .profile-img-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(30, 27, 75, 0.9);
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid rgba(251, 191, 36, 0.5);
        }
        
        .profile-img-overlay:hover {
            background: rgba(234, 179, 8, 0.9);
            transform: scale(1.1);
        }
        
        
        /* Event Card */
        .event-card {
            background: white;
            border: 1px solid #e5e7eb;
            transition: all 0.3s;
        }
        
        .event-card:hover {
            background: #f9fafb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .event-card .font-semibold {
            color: #111827;
        }
        
        .event-card .text-gray-400 {
            color: #6b7280;
        }
        
        /* Smooth Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Profile Completion Widget - Compact */
        .profile-completion-widget-compact {
            animation: fadeInUp 0.3s ease-out;
        }

        .completion-card-compact {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 0.875rem 1rem;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
            transition: all 0.3s ease;
        }

        .completion-card-compact:hover {
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            transform: translateY(-1px);
        }

        .completion-content-compact {
            display: flex;
            align-items: center;
            gap: 0.875rem;
        }

        .completion-icon-compact {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            color: white;
            flex-shrink: 0;
        }

        .completion-info-compact {
            flex: 1;
            min-width: 0;
        }

        .completion-label-compact {
            color: rgba(255, 255, 255, 0.95);
            font-size: 0.8rem;
            font-weight: 600;
            display: block;
            margin-bottom: 0.375rem;
        }

        .completion-progress-compact {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .completion-progress-track-compact {
            flex: 1;
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            overflow: hidden;
            position: relative;
        }

        .completion-progress-fill-compact {
            height: 100%;
            background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: 6px;
            transition: width 0.6s ease;
            box-shadow: 0 0 6px rgba(251, 191, 36, 0.4);
        }

        .completion-percentage-compact {
            color: white;
            font-size: 0.85rem;
            font-weight: 700;
            min-width: 38px;
            text-align: right;
        }

        .completion-link-compact {
            width: 32px;
            height: 32px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .completion-link-compact:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateX(2px);
            color: white;
        }

        .completion-check-compact {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fbbf24;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Custom collapse animation */
        #badgeTerms.collapse,
        #badgeFAQ.collapse {
            transition: height 0.35s ease;
            overflow: hidden;
        }
        
        #badgeTerms.collapse.show,
        #badgeFAQ.collapse.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Ensure content inside is visible when shown */
        #badgeTerms.collapse.show > div,
        #badgeFAQ.collapse.show > div {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        #badgeTerms.collapse:not(.show),
        #badgeFAQ.collapse:not(.show) {
            display: none;
        }

        @media (max-width: 768px) {
            .completion-card-compact {
                padding: 0.75rem;
            }

            .completion-icon-compact {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }

            .completion-label-compact {
                font-size: 0.75rem;
            }

            .completion-percentage-compact {
                font-size: 0.8rem;
                min-width: 35px;
            }
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")
    
    @php
        // Define all badges for modal display - available throughout the view
        $allBadges = [
            'beginner' => ['name' => 'Beginner', 'min' => 0, 'max' => 99, 'gradient' => 'linear-gradient(135deg, #94a3b8 0%, #64748b 100%)', 'icon' => 'bi-star', 'color' => '#94a3b8'],
            'explorer' => ['name' => 'Explorer', 'min' => 100, 'max' => 249, 'gradient' => 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', 'icon' => 'bi-compass', 'color' => '#3b82f6'],
            'learner' => ['name' => 'Learner', 'min' => 250, 'max' => 499, 'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', 'icon' => 'bi-book', 'color' => '#8b5cf6'],
            'expert' => ['name' => 'Expert', 'min' => 500, 'max' => 999, 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-trophy', 'color' => '#f59e0b'],
            'master' => ['name' => 'Master', 'min' => 1000, 'max' => 9999, 'gradient' => 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)', 'icon' => 'bi-gem', 'color' => '#dc2626'],
        ];
    @endphp
    
    @php
        $user = Auth::user();
        $badgeInfo = $user->badge_info;
        $nextBadgeInfo = $user->next_badge_info;
        $currentPoints = $user->points ?? 0;
        $currentBadge = $user->badge ?? 'beginner';
        $completion = $user->getProfileCompletionPercentage();

        // Visual progress helpers (safe defaults)
        $eventsCountSafe = (int) ($eventsCount ?? 0);
        $coursesCountSafe = (int) ($coursesCount ?? 0);
        $eventsProgress = min(100, (int) round(($eventsCountSafe / 20) * 100));
        $coursesProgress = min(100, (int) round(($coursesCountSafe / 20) * 100));

        $subtitleParts = [];
        if (!empty($user->profession)) $subtitleParts[] = $user->profession;
        if (!empty($user->institution)) $subtitleParts[] = $user->institution;
        $subtitle = implode(' | ', $subtitleParts);
    @endphp

    <div class="profile-container fade-in">
        <!-- Header -->
        <div class="profile-card p-6 md:p-8 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="profile-img-wrapper">
                        <img
                            src="{{ $user->avatar_url }}"
                            alt="Profile"
                            class="w-24 h-24 rounded-full object-cover border-4 shadow-lg"
                            style="border-color: #c7d2fe;"
                            referrerpolicy="no-referrer"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=EEF2FF&color=1E1B4B&size=128';"
                        >
                    </div>

                    <div class="min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-3xl md:text-4xl font-bold mb-0" style="color:#0f172a;">
                                {{ $user->name }}
                            </h1>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background:#fef3c7; color:#92400e;">
                                {{ ucfirst($user->role ?? 'user') }}
                            </span>
                        </div>
                        <p class="mt-1 mb-0 text-sm md:text-base" style="color:#64748b;">
                            {{ $subtitle ?: 'Lengkapi profil untuk menampilkan profesi & institusi.' }}
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            {{-- Belum ada field khusus linkedin/github di DB --}}
                            <a class="profile-pill disabled" href="#" aria-disabled="true" title="Tambahkan link LinkedIn di pengaturan profil (belum tersedia field khusus)">
                                <i class="bi bi-linkedin"></i>
                                LinkedIn
                            </a>
                            <a class="profile-pill disabled" href="#" aria-disabled="true" title="Tambahkan link GitHub di pengaturan profil (belum tersedia field khusus)">
                                <i class="bi bi-github"></i>
                                GitHub
                            </a>
                            @if(!empty($user->website))
                                <a class="profile-pill" href="{{ $user->website }}" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-globe2"></i>
                                    Portfolio Site
                                </a>
                            @else
                                <a class="profile-pill disabled" href="#" aria-disabled="true" title="Tambahkan website di pengaturan profil">
                                    <i class="bi bi-globe2"></i>
                                    Portfolio Site
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="points-chip self-start lg:self-auto">
                    <div class="text-xs font-bold uppercase tracking-widest" style="color:#6366f1;">
                        Total Points
                    </div>
                    <div class="mt-2 flex items-center gap-2">
                        <i class="bi bi-lightning-charge-fill" style="color:#fbbf24; font-size:1.25rem;"></i>
                        <div class="text-3xl font-extrabold" style="color:#4f46e5;">
                            {{ number_format($currentPoints, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
                <p class="text-green-800 text-sm font-medium mb-0">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Skill / Progress -->
            <div class="profile-soft-card p-6 lg:col-span-2">
                <div class="flex items-center gap-3 mb-5">
                    <div class="flex items-center justify-center rounded-xl" style="width:42px; height:42px; background:#fff7ed; color:#f59e0b;">
                        <i class="bi bi-briefcase" style="font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <div class="text-xl font-bold" style="color:#0f172a;">Skill Matrix & Experience</div>
                        <div class="text-sm" style="color:#64748b;">Ringkasan progress akun & aktivitas.</div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-semibold" style="color:#0f172a;">Kelengkapan Profil</div>
                            <div class="text-sm font-bold" style="color:#4f46e5;">{{ $completion }}%</div>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $completion }}%;"></div>
                        </div>
                        <div class="mt-2 text-xs" style="color:#64748b;">
                            @if(!$user->isProfileComplete())
                                Belum lengkap. <a href="{{ route('profile.settings') }}" style="color:#4f46e5; font-weight:700; text-decoration:none;">Lengkapi sekarang</a>
                            @else
                                Profil kamu sudah lengkap.
                            @endif
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-semibold" style="color:#0f172a;">Event Participation</div>
                            <div class="text-sm font-bold" style="color:#4f46e5;">{{ $eventsCountSafe }} event</div>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $eventsProgress }}%;"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-semibold" style="color:#0f172a;">Course Progress</div>
                            <div class="text-sm font-bold" style="color:#4f46e5;">{{ $coursesCountSafe }} course</div>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill" style="width: {{ $coursesProgress }}%;"></div>
                        </div>
                    </div>

                    <div class="mt-2 p-4 rounded-2xl" style="background:#f8fafc; border:1px solid #eef2f7;">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center rounded-xl" style="width:44px; height:44px; background:#ffffff; border:1px solid #eef2f7; color:#4f46e5;">
                                <i class="bi bi-stack" style="font-size:1.1rem;"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold" style="color:#0f172a;">Learning Path</div>
                                <div class="text-sm truncate" style="color:#64748b;">
                                    {{ $user->profession ?: 'Pilih profesi di pengaturan profil' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="profile-soft-card p-6">
                <div class="text-xl font-bold mb-5" style="color:#0f172a;">Informasi Akun</div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center rounded-xl" style="width:40px; height:40px; background:#f1f5f9; color:#64748b;">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold uppercase tracking-wider" style="color:#94a3b8;">Email</div>
                            <div class="font-semibold truncate" style="color:#0f172a;">{{ $user->email }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center rounded-xl" style="width:40px; height:40px; background:#f1f5f9; color:#64748b;">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold uppercase tracking-wider" style="color:#94a3b8;">Telepon</div>
                            <div class="font-semibold truncate" style="color:#0f172a;">
                                {{ $user->formatted_phone ?? $user->phone ?? 'Belum diisi' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center rounded-xl" style="width:40px; height:40px; background:#f1f5f9; color:#64748b;">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold uppercase tracking-wider" style="color:#94a3b8;">Bergabung</div>
                            <div class="font-semibold" style="color:#0f172a;">
                                {{ optional($user->created_at)->format('Y-m-d') ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex items-center justify-center rounded-xl" style="width:40px; height:40px; background:#f1f5f9; color:#64748b;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-xs font-bold uppercase tracking-wider" style="color:#94a3b8;">Status</div>
                            <div class="font-semibold" style="color:#0f172a;">
                                {{ $user->profession ?: 'Mahasiswa / Umum' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Achievement Badges -->
            <div class="profile-soft-card p-6 lg:col-span-2">
                <div class="text-xl font-bold mb-4" style="color:#0f172a;">Achievement Badges</div>

                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
                    @foreach($allBadges as $badgeKey => $badge)
                        <div class="badge-tile p-4 text-center {{ $badgeKey === $currentBadge ? 'active' : '' }}">
                            <div class="mx-auto mb-3 flex items-center justify-center" style="width:54px; height:54px; border-radius:18px; background: {{ $badge['gradient'] }};">
                                <i class="bi {{ $badge['icon'] }}" style="color:white; font-size:1.35rem;"></i>
                            </div>
                            <div class="text-xs font-extrabold tracking-widest" style="color:#0f172a;">
                                {{ strtoupper($badge['name']) }}
                            </div>
                            <div class="mt-1 text-[11px]" style="color:#94a3b8;">
                                {{ number_format($badge['min'], 0, ',', '.') }}{{ $badge['max'] < 9999 ? ' - ' . number_format($badge['max'], 0, ',', '.') : '+' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Roadmap -->
            <div class="profile-card p-6" style="background: #0b0f2b; border-color: rgba(255,255,255,0.06);">
                <div class="text-xl font-extrabold mb-4" style="color:#ffffff;">Roadmap Karir</div>

                @if($nextBadgeInfo)
                    @php
                        $progressPercent = min(100, (($currentPoints - $badgeInfo['min_points']) / max(1, ($nextBadgeInfo['min_points'] - $badgeInfo['min_points']))) * 100);
                    @endphp
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-xs font-bold uppercase tracking-widest" style="color: rgba(255,255,255,0.7);">
                            {{ strtoupper($badgeInfo['name'] ?? 'LEVEL') }}
                        </div>
                        <div class="text-xs font-bold" style="color: rgba(255,255,255,0.9);">
                            {{ number_format($currentPoints, 0, ',', '.') }}/{{ number_format($nextBadgeInfo['min_points'], 0, ',', '.') }} PTS
                        </div>
                    </div>
                    <div style="height:8px; background: rgba(255,255,255,0.12); border-radius:999px; overflow:hidden;">
                        <div style="height:100%; width: {{ $progressPercent }}%; background: linear-gradient(90deg, #fbbf24 0%, #f59e0b 100%); border-radius:999px;"></div>
                    </div>
                    <p class="mt-4 text-sm" style="color: rgba(255,255,255,0.8); line-height:1.6;">
                        Kumpulkan <b style="color:#ffffff;">{{ number_format($nextBadgeInfo['points_needed'], 0, ',', '.') }}</b> poin lagi untuk membuka badge <b style="color:#ffffff;">{{ $nextBadgeInfo['name'] }}</b>.
                    </p>
                @else
                    <p class="text-sm" style="color: rgba(255,255,255,0.8); line-height:1.6;">
                        Kamu sudah mencapai level tertinggi. Pertahankan streak dan terus ikuti event/course untuk reward lainnya.
                    </p>
                @endif

                <button type="button" onclick="openBadgeInfoModal()" class="w-full mt-4 py-3 rounded-2xl font-extrabold tracking-widest"
                        style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); color:#111827; border:none;">
                    REDEEM REWARDS
                </button>
            </div>
        </div>
    </div>
    
    <!-- Badge Info Modal - Minimalis -->
    @php
        $user = Auth::user();
        $badgeInfo = $user->badge_info;
        $currentBadge = $user->badge ?? 'beginner';
    @endphp
    <div class="modal fade" id="badgeInfoModal" tabindex="-1" aria-labelledby="badgeInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
            <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15); overflow: hidden;">
                <div class="modal-header" style="background: white; border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem;">
                    <h5 class="modal-title" id="badgeInfoModalLabel" style="color: #1e293b; font-weight: 600; font-size: 1.25rem; margin: 0;">
                        Badge & Poin
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="opacity: 0.5;"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem; background: white;">
                    <!-- Cara Mendapatkan Poin - Minimalis -->
                    <div class="mb-4">
                        <h6 style="color: #475569; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            Cara Mendapatkan Poin
                        </h6>
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                                <div style="width: 32px; height: 32px; background: #3b82f6; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-gift" style="color: white; font-size: 0.875rem;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.8125rem;">Event Gratis</div>
                                    <div style="color: #3b82f6; font-weight: 700; font-size: 0.875rem; margin-top: 0.125rem;">+10</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                                <div style="width: 32px; height: 32px; background: #f59e0b; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-credit-card" style="color: white; font-size: 0.875rem;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.8125rem;">Event Berbayar</div>
                                    <div style="color: #f59e0b; font-weight: 700; font-size: 0.875rem; margin-top: 0.125rem;">+30</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                                <div style="width: 32px; height: 32px; background: #8b5cf6; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-chat-left-text" style="color: white; font-size: 0.875rem;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.8125rem;">Feedback</div>
                                    <div style="color: #8b5cf6; font-weight: 700; font-size: 0.875rem; margin-top: 0.125rem;">+5</div>
                                </div>
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #f8fafc; border-radius: 8px;">
                                <div style="width: 32px; height: 32px; background: #22c55e; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="bi bi-lightning-charge" style="color: white; font-size: 0.875rem;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.8125rem;">Streak</div>
                                    <div style="color: #22c55e; font-weight: 700; font-size: 0.875rem; margin-top: 0.125rem;">+5</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Daftar Badge - Minimalis -->
                    <div class="mb-4">
                        <h6 style="color: #475569; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            Level Badge
                        </h6>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            @foreach($allBadges as $badgeKey => $badge)
                            <div style="display: flex; align-items: center; gap: 0.75rem; padding: 0.875rem; background: {{ $badgeKey === $currentBadge ? '#fef3c7' : '#f8fafc' }}; border-radius: 10px; border: 1px solid {{ $badgeKey === $currentBadge ? $badgeInfo['color'] : '#e2e8f0' }}; position: relative;">
                                @if($badgeKey === $currentBadge)
                                <div style="position: absolute; top: 0.5rem; right: 0.75rem; background: {{ $badgeInfo['color'] }}; color: white; padding: 0.125rem 0.5rem; border-radius: 4px; font-size: 0.625rem; font-weight: 700; text-transform: uppercase;">
                                    Anda
                                </div>
                                @endif
                                <div style="width: 48px; height: 48px; background: {{ $badge['gradient'] }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);">
                                    <i class="bi {{ $badge['icon'] }}" style="color: white; font-size: 1.25rem;"></i>
                                </div>
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-weight: 600; color: #1e293b; font-size: 0.9375rem; margin-bottom: 0.125rem;">
                                        {{ $badge['name'] }}
                                    </div>
                                    <div style="color: #64748b; font-size: 0.8125rem;">
                                        {{ number_format($badge['min'], 0, ',', '.') }}{{ $badge['max'] < 9999 ? ' - ' . number_format($badge['max'], 0, ',', '.') : '+' }} Poin
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Terms & Conditions / FAQ -->
                    <div style="border-top: 1px solid #e2e8f0; padding-top: 1rem;">
                        <div style="margin-bottom: 0.75rem;">
                            <button type="button" class="btn btn-link p-0 text-start w-100 badge-terms-btn" aria-expanded="false" aria-controls="badgeTerms" style="text-decoration: none; color: #475569; font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; justify-content: space-between; border: none; background: none; cursor: pointer; width: 100%;">
                                <span style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="bi bi-info-circle" style="font-size: 1rem;"></i>
                                    <span>Syarat & Ketentuan</span>
                                </span>
                                <i class="bi bi-chevron-down badge-chevron-terms" style="font-size: 0.75rem; transition: transform 0.3s ease;"></i>
                            </button>
                        </div>
                        <div class="collapse" id="badgeTerms">
                            <div style="background: #f8fafc; border-radius: 8px; padding: 1rem; font-size: 0.8125rem; color: #475569; line-height: 1.6; margin-top: 0.5rem;">
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">1. Poin Event</strong>
                                    <ul style="margin: 0; padding-left: 1.25rem; color: #64748b;">
                                        <li>Poin event diberikan saat registrasi berhasil</li>
                                        <li>Event gratis: +10 poin, Event berbayar: +30 poin</li>
                                        <li>Poin tidak dapat ditransfer atau dikembalikan</li>
                                    </ul>
                                </div>
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">2. Poin Feedback</strong>
                                    <ul style="margin: 0; padding-left: 1.25rem; color: #64748b;">
                                        <li>Poin feedback diberikan setelah event selesai</li>
                                        <li>Hanya 1 feedback per event yang mendapat poin</li>
                                        <li>Feedback harus diisi dengan lengkap dan valid</li>
                                    </ul>
                                </div>
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">3. Bonus Streak</strong>
                                    <ul style="margin: 0; padding-left: 1.25rem; color: #64748b;">
                                        <li>Bonus streak diberikan jika mengikuti event dalam 7 hari setelah event sebelumnya</li>
                                        <li>Streak dihitung berdasarkan tanggal event, bukan tanggal registrasi</li>
                                        <li>Bonus hanya diberikan untuk event yang sudah selesai</li>
                                    </ul>
                                </div>
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">4. Badge & Level</strong>
                                    <ul style="margin: 0; padding-left: 1.25rem; color: #64748b;">
                                        <li>Badge otomatis terupdate berdasarkan total poin</li>
                                        <li>Badge tidak dapat diturunkan setelah diperoleh</li>
                                        <li>Level Master dapat dicapai dengan 1000+ poin</li>
                                    </ul>
                                </div>
                                <div>
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">5. Ketentuan Umum</strong>
                                    <ul style="margin: 0; padding-left: 1.25rem; color: #64748b;">
                                        <li>Poin dan badge adalah sistem reward yang tidak dapat ditukar dengan uang</li>
                                        <li>Kami berhak mengubah sistem poin dengan pemberitahuan sebelumnya</li>
                                        <li>Penyalahgunaan sistem akan mengakibatkan poin dibatalkan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-top: 0.75rem;">
                            <button type="button" class="btn btn-link p-0 text-start w-100 badge-faq-btn" aria-expanded="false" aria-controls="badgeFAQ" style="text-decoration: none; color: #475569; font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; justify-content: space-between; border: none; background: none; cursor: pointer; width: 100%;">
                                <span style="display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="bi bi-question-circle" style="font-size: 1rem;"></i>
                                    <span>Pertanyaan Umum</span>
                                </span>
                                <i class="bi bi-chevron-down badge-chevron-faq" style="font-size: 0.75rem; transition: transform 0.3s ease;"></i>
                            </button>
                        </div>
                        <div class="collapse" id="badgeFAQ">
                            <div style="background: #f8fafc; border-radius: 8px; padding: 1rem; font-size: 0.8125rem; color: #475569; line-height: 1.6; margin-top: 0.5rem;">
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">Q: Apakah poin bisa hilang?</strong>
                                    <p style="margin: 0; color: #64748b;">A: Poin tidak akan hilang kecuali ada pelanggaran ketentuan. Badge yang sudah diperoleh juga tidak akan diturunkan.</p>
                                </div>
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">Q: Bagaimana cara mendapatkan bonus streak?</strong>
                                    <p style="margin: 0; color: #64748b;">A: Ikuti event dalam 7 hari setelah event sebelumnya selesai. Bonus streak hanya diberikan untuk event yang sudah terjadi.</p>
                                </div>
                                <div style="margin-bottom: 0.75rem;">
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">Q: Apakah bisa mendapat poin untuk event yang sudah lewat?</strong>
                                    <p style="margin: 0; color: #64748b;">A: Tidak, poin hanya diberikan saat registrasi aktif. Event yang sudah lewat tidak akan mendapat poin tambahan.</p>
                                </div>
                                <div>
                                    <strong style="color: #1e293b; display: block; margin-bottom: 0.25rem;">Q: Bagaimana jika poin saya tidak sesuai?</strong>
                                    <p style="margin: 0; color: #64748b;">A: Hubungi admin untuk verifikasi. Poin akan dihitung ulang berdasarkan riwayat registrasi dan feedback Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: white; border-top: 1px solid #f1f5f9; padding: 1rem 1.5rem;">
                    <button type="button" class="btn btn-sm" data-bs-dismiss="modal" style="background: #f1f5f9; color: #475569; border: none; border-radius: 8px; padding: 0.5rem 1.25rem; font-weight: 500; font-size: 0.875rem;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // reserved for future profile interactions
        });
        
        // Avatar preview function
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Open Badge Info Modal
        function openBadgeInfoModal() {
            const modalEl = document.getElementById('badgeInfoModal');
            if (!modalEl) return;
            
            // Check if Bootstrap is available
            if (window.bootstrap && typeof bootstrap.Modal === 'function') {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                
                // Simple manual toggle without Bootstrap Collapse API
                modalEl.addEventListener('shown.bs.modal', function() {
                    const termsBtn = modalEl.querySelector('.badge-terms-btn');
                    const termsCollapse = modalEl.querySelector('#badgeTerms');
                    const termsChevron = modalEl.querySelector('.badge-chevron-terms');
                    const faqBtn = modalEl.querySelector('.badge-faq-btn');
                    const faqCollapse = modalEl.querySelector('#badgeFAQ');
                    const faqChevron = modalEl.querySelector('.badge-chevron-faq');
                    
                    // Terms collapse - simple toggle
                    if (termsBtn && termsCollapse && termsChevron) {
                        termsBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const isExpanded = termsCollapse.classList.contains('show');
                            
                            if (isExpanded) {
                                // Close
                                termsCollapse.classList.remove('show');
                                termsCollapse.style.height = '0px';
                                termsChevron.style.transform = 'rotate(0deg)';
                                termsBtn.setAttribute('aria-expanded', 'false');
                                
                                // Wait for transition then hide
                                setTimeout(() => {
                                    termsCollapse.style.display = 'none';
                                    termsCollapse.style.height = '';
                                }, 350);
                            } else {
                                // Open - set display first
                                termsCollapse.style.display = 'block';
                                termsCollapse.style.visibility = 'visible';
                                termsCollapse.style.opacity = '1';
                                
                                // Ensure content is visible
                                const content = termsCollapse.querySelector('div');
                                if (content) {
                                    content.style.display = 'block';
                                    content.style.visibility = 'visible';
                                    content.style.opacity = '1';
                                }
                                
                                // Set initial height to 0 for animation
                                termsCollapse.style.height = '0px';
                                termsCollapse.style.overflow = 'hidden';
                                
                                // Force reflow
                                void termsCollapse.offsetHeight;
                                
                                // Add show class
                                termsCollapse.classList.add('show');
                                
                                // Calculate height - content should be visible now
                                const height = termsCollapse.scrollHeight;
                                termsCollapse.style.height = height + 'px';
                                termsChevron.style.transform = 'rotate(180deg)';
                                termsBtn.setAttribute('aria-expanded', 'true');
                                
                                // After transition, set height to auto and overflow visible
                                setTimeout(() => {
                                    if (termsCollapse.classList.contains('show')) {
                                        termsCollapse.style.height = 'auto';
                                        termsCollapse.style.overflow = 'visible';
                                    }
                                }, 350);
                            }
                        });
                    }
                    
                    // FAQ collapse - simple toggle
                    if (faqBtn && faqCollapse && faqChevron) {
                        faqBtn.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            const isExpanded = faqCollapse.classList.contains('show');
                            
                            if (isExpanded) {
                                // Close
                                faqCollapse.classList.remove('show');
                                faqCollapse.style.height = '0px';
                                faqChevron.style.transform = 'rotate(0deg)';
                                faqBtn.setAttribute('aria-expanded', 'false');
                                
                                // Wait for transition then hide
                                setTimeout(() => {
                                    faqCollapse.style.display = 'none';
                                    faqCollapse.style.height = '';
                                }, 350);
                            } else {
                                // Open - set display first
                                faqCollapse.style.display = 'block';
                                faqCollapse.style.visibility = 'visible';
                                faqCollapse.style.opacity = '1';
                                
                                // Ensure content is visible
                                const content = faqCollapse.querySelector('div');
                                if (content) {
                                    content.style.display = 'block';
                                    content.style.visibility = 'visible';
                                    content.style.opacity = '1';
                                }
                                
                                // Set initial height to 0 for animation
                                faqCollapse.style.height = '0px';
                                faqCollapse.style.overflow = 'hidden';
                                
                                // Force reflow
                                void faqCollapse.offsetHeight;
                                
                                // Add show class
                                faqCollapse.classList.add('show');
                                
                                // Calculate height - content should be visible now
                                const height = faqCollapse.scrollHeight;
                                faqCollapse.style.height = height + 'px';
                                faqChevron.style.transform = 'rotate(180deg)';
                                faqBtn.setAttribute('aria-expanded', 'true');
                                
                                // After transition, set height to auto and overflow visible
                                setTimeout(() => {
                                    if (faqCollapse.classList.contains('show')) {
                                        faqCollapse.style.height = 'auto';
                                        faqCollapse.style.overflow = 'visible';
                                    }
                                }, 350);
                            }
                        });
                    }
                });
            } else {
                // Fallback: show modal using jQuery or vanilla JS
                modalEl.style.display = 'block';
                modalEl.classList.add('show');
                document.body.classList.add('modal-open');
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'badgeModalBackdrop';
                document.body.appendChild(backdrop);
                
                // Close handler
                const closeModal = function() {
                    modalEl.style.display = 'none';
                    modalEl.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    const backdropEl = document.getElementById('badgeModalBackdrop');
                    if (backdropEl) backdropEl.remove();
                };
                
                // Close on backdrop click
                backdrop.addEventListener('click', closeModal);
                
                // Close on close button click
                const closeBtns = modalEl.querySelectorAll('[data-bs-dismiss="modal"], .btn-close');
                closeBtns.forEach(btn => {
                    btn.addEventListener('click', closeModal);
                });
            }
        }
    </script>
    @include('partials.footer-after-login')
</body>
</html>