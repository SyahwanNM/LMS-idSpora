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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        /* Background same as dashboard */
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            padding-top: 70px;
        }
        
        /* Ensure navbar is visible and on top */
        .navbar {
            z-index: 1050 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
        }
        
        .navbar-gradient {
            background: linear-gradient(90deg, #252346 0%, #5b56ac 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Ensure navbar elements are visible */
        .navbar-gradient .navbar-brand,
        .navbar-gradient .nav-link,
        .navbar-gradient .navbar-text {
            color: #fff !important;
        }
        
        .navbar-gradient .nav-link:hover,
        .navbar-gradient .nav-link:focus {
            color: #ffe8b3 !important;
        }
        
        .navbar-gradient .nav-link.active {
            font-weight: 600;
            color: #ffe8b3 !important;
        }
        
        /* Notification and user dropdown */
        #notifBtn,
        #userDropdown {
            color: white !important;
        }
        
        #notifBtn:hover,
        #userDropdown:hover {
            color: #ffe8b3 !important;
        }
        
        /* Search bar */
        .navbar .form-control {
            color: white !important;
        }
        
        .navbar .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        /* Ensure navbar container is visible */
        .navbar .container-fluid {
            display: flex !important;
            visibility: visible !important;
        }
        
        /* Ensure all navbar elements are visible */
        .navbar-brand,
        .navbar-nav,
        .navbar-collapse {
            display: flex !important;
            visibility: visible !important;
        }
        
        /* Navbar Responsive */
        @media (max-width: 991px) {
            .navbar-brand {
                margin-left: 15px !important;
            }
            .navbar-brand img {
                max-width: 60px !important;
            }
            .navbar .form-control {
                width: 100% !important;
                margin: 0.5rem 0 !important;
            }
            .navbar-nav {
                flex-direction: column !important;
                width: 100%;
                margin: 0.5rem 0 !important;
            }
            .navbar-nav .nav-item {
                margin: 0.25rem 0 !important;
                width: 100%;
            }
            .navbar-collapse {
                padding: 1rem 0;
            }
            #notifBtn,
            #userDropdown {
                margin: 0.5rem 0 !important;
            }
            .d-flex.align-items-center.ms-3 {
                flex-direction: column !important;
                width: 100%;
                margin-right: 0 !important;
                margin-left: 0 !important;
            }
        }
        
        /* Card styling with light background */
        .glass-card {
            background: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
        }
        
        .glass-sidebar {
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            border-right: none;
            position: fixed;
            top: 70px;
            left: 0;
            height: calc(100vh - 70px);
            overflow-y: auto;
            z-index: 1000;
            width: 280px;
        }
        
        .sidebar-header {
            text-align: center;
            padding: 1.5rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        /* Main content with sidebar offset */
        .main-content-with-sidebar {
            margin-left: 280px;
            padding: 2rem;
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
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
                padding: 1.25rem;
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
        
        /* Sidebar Menu Item - Matching Navbar Gradient */
        .menu-item {
            transition: all 0.3s ease;
            color: rgba(255, 255, 255, 0.7);
            border-radius: 12px;
            margin: 0.5rem 1rem;
            padding: 0.875rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            position: relative;
        }
        
        .menu-item:hover {
            color: rgba(255, 255, 255, 0.9);
            background: rgba(255, 255, 255, 0.1);
        }
        
        .menu-item.active {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #1e1b4b;
            box-shadow: 0 2px 8px rgba(251, 191, 36, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        .menu-item.active .menu-icon {
            color: #1e1b4b;
        }
        
        .menu-item .menu-text {
            color: inherit;
            font-size: 14px;
            font-weight: 500;
        }
        
        .menu-icon {
            color: rgba(255, 255, 255, 0.7);
            font-size: 20px;
            transition: color 0.3s ease;
            width: 24px;
            text-align: center;
        }
        
        .menu-item:hover .menu-icon {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .menu-item.active .menu-icon {
            color: white;
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
    
    <div class="flex min-h-screen">
        <!-- Sidebar - Dark Theme with Purple Gradient Active -->
        <aside class="glass-sidebar flex flex-col">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                MENU NAVIGASI
            </div>
            
            <!-- Badge Display in Sidebar -->
            @php
                $user = Auth::user();
                $badgeInfo = $user->badge_info;
                $currentPoints = $user->points ?? 0;
            @endphp
            <div class="sidebar-badge" style="margin: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.15);">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                    <div style="width: 40px; height: 40px; background: {{ $badgeInfo['gradient'] }}; border-radius: 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                        <i class="bi {{ $badgeInfo['icon'] }}" style="font-size: 1.25rem; color: white;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="color: white; font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem;">
                            {{ $badgeInfo['name'] }}
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.25rem;">
                            <i class="bi bi-star-fill" style="color: #FFD700; font-size: 0.75rem;"></i>
                            <span style="color: rgba(255, 255, 255, 0.9); font-size: 0.75rem; font-weight: 500;">
                                {{ number_format($currentPoints, 0, ',', '.') }} Poin
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Menu Items -->
            <nav class="flex-1 py-4">
                <a href="{{ route('profile.index') }}" class="menu-item {{ request()->routeIs('profile.index') || request()->routeIs('profile.edit') ? 'active' : '' }}">
                    <i class="bi bi-person menu-icon"></i>
                    <span class="menu-text">Profile</span>
                </a>
                
                <a href="{{ route('profile.events') }}" class="menu-item {{ request()->routeIs('profile.events') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check menu-icon"></i>
                    <span class="menu-text">History Event</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content-with-sidebar flex-1 overflow-y-auto" style="margin-top: 70px;">
            <div class="max-w-6xl mx-auto fade-in">
                <!-- Badge & Points Widget -->
                @php
                    $user = Auth::user();
                    $badgeInfo = $user->badge_info;
                    $nextBadgeInfo = $user->next_badge_info;
                    $currentPoints = $user->points ?? 0;
                    $currentBadge = $user->badge ?? 'beginner';
                    
                    // Define all badges for modal display
                    $allBadges = [
                        'beginner' => ['name' => 'Beginner', 'min' => 0, 'max' => 99, 'gradient' => 'linear-gradient(135deg, #94a3b8 0%, #64748b 100%)', 'icon' => 'bi-star', 'color' => '#94a3b8'],
                        'explorer' => ['name' => 'Explorer', 'min' => 100, 'max' => 249, 'gradient' => 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', 'icon' => 'bi-compass', 'color' => '#3b82f6'],
                        'learner' => ['name' => 'Learner', 'min' => 250, 'max' => 499, 'gradient' => 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', 'icon' => 'bi-book', 'color' => '#8b5cf6'],
                        'expert' => ['name' => 'Expert', 'min' => 500, 'max' => 999, 'gradient' => 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', 'icon' => 'bi-trophy', 'color' => '#f59e0b'],
                        'master' => ['name' => 'Master', 'min' => 1000, 'max' => 9999, 'gradient' => 'linear-gradient(135deg, #dc2626 0%, #991b1b 100%)', 'icon' => 'bi-gem', 'color' => '#dc2626'],
                    ];
                @endphp
                <div class="badge-widget mb-4" style="animation: fadeInUp 0.5s ease-out;">
                    <div class="badge-card" style="background: {{ $badgeInfo['gradient'] }}; border-radius: 16px; padding: 1.5rem; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); position: relative; overflow: hidden;">
                        <!-- Decorative Elements -->
                        <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: rgba(255, 255, 255, 0.1); border-radius: 50%;"></div>
                        <div style="position: absolute; bottom: -30px; left: -30px; width: 100px; height: 100px; background: rgba(255, 255, 255, 0.08); border-radius: 50%;"></div>
                        
                        <div style="display: flex; align-items: center; gap: 1.5rem; position: relative; z-index: 1;">
                            <!-- Badge Icon -->
                            <div class="badge-icon-wrapper" style="width: 80px; height: 80px; background: rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px); border-radius: 20px; display: flex; align-items: center; justify-content: center; border: 3px solid rgba(255, 255, 255, 0.3); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);">
                                <i class="bi {{ $badgeInfo['icon'] }}" style="font-size: 2.5rem; color: white;"></i>
                            </div>
                            
                            <!-- Badge Info -->
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                                    <h3 style="color: white; font-size: 1.5rem; font-weight: 700; margin: 0; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                        {{ $badgeInfo['name'] }}
                                    </h3>
                                    <span style="background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.75rem; font-weight: 600; color: white; border: 1px solid rgba(255, 255, 255, 0.3);">
                                        Level {{ ucfirst($currentBadge) }}
                                    </span>
                                    <!-- Info Toggle Button -->
                                    <button 
                                        type="button" 
                                        onclick="openBadgeInfoModal()"
                                        style="background: rgba(255, 255, 255, 0.25); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 50%; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; color: white; padding: 0;"
                                        onmouseover="this.style.background='rgba(255, 255, 255, 0.35)'; this.style.transform='scale(1.1)'"
                                        onmouseout="this.style.background='rgba(255, 255, 255, 0.25)'; this.style.transform='scale(1)'"
                                        title="Info Badge & Poin"
                                    >
                                        <i class="bi bi-info-circle" style="font-size: 1.1rem;"></i>
                                    </button>
                                </div>
                                
                                <!-- Points Display -->
                                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <i class="bi bi-star-fill" style="color: #FFD700; font-size: 1.25rem; filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));"></i>
                                        <span style="color: white; font-size: 1.5rem; font-weight: 700; text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);">
                                            {{ number_format($currentPoints, 0, ',', '.') }} Poin
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Progress to Next Badge -->
                                @if($nextBadgeInfo)
                                <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border-radius: 12px; padding: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <span style="color: rgba(255, 255, 255, 0.9); font-size: 0.85rem; font-weight: 500;">
                                            Menuju {{ $nextBadgeInfo['name'] }}
                                        </span>
                                        <span style="color: white; font-size: 0.85rem; font-weight: 700;">
                                            {{ $nextBadgeInfo['points_needed'] }} poin lagi
                                        </span>
                                    </div>
                                    @php
                                        $progressPercent = min(100, (($currentPoints - $badgeInfo['min_points']) / ($nextBadgeInfo['min_points'] - $badgeInfo['min_points'])) * 100);
                                    @endphp
                                    <div style="height: 8px; background: rgba(255, 255, 255, 0.2); border-radius: 4px; overflow: hidden;">
                                        <div style="height: 100%; background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%); width: {{ $progressPercent }}%; border-radius: 4px; transition: width 0.6s ease; box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);"></div>
                                    </div>
                                </div>
                                @else
                                <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(10px); border-radius: 12px; padding: 0.75rem; border: 1px solid rgba(255, 255, 255, 0.2);">
                                    <span style="color: rgba(255, 255, 255, 0.9); font-size: 0.85rem; font-weight: 500;">
                                        🏆 Anda telah mencapai level tertinggi!
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Completion Widget - Compact -->
                <div class="profile-completion-widget-compact mb-4">
                    <div class="completion-card-compact">
                        <div class="completion-content-compact">
                            <div class="completion-icon-compact">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="completion-info-compact">
                                <span class="completion-label-compact">Kelengkapan Profil</span>
                                <div class="completion-progress-compact">
                                    <div class="completion-progress-track-compact">
                                        <div class="completion-progress-fill-compact" style="width: {{ Auth::user()->getProfileCompletionPercentage() }}%;"></div>
                                    </div>
                                    <span class="completion-percentage-compact">{{ Auth::user()->getProfileCompletionPercentage() }}%</span>
                                </div>
                            </div>
                            @if(!Auth::user()->isProfileComplete())
                            <a href="{{ route('profile.edit') }}" class="completion-link-compact" title="Lengkapi Profil">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                            @else
                            <span class="completion-check-compact" title="Profil Lengkap">
                                <i class="bi bi-check-circle-fill"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Profile Information Card -->
                <div class="glass-card rounded-2xl p-8 shadow-2xl mb-8">
                    <h1 class="text-3xl font-bold mb-8" style="color: #111827;">Informasi Profil</h1>
                    
                    <!-- Profile Header -->
                    <div class="flex items-start space-x-6 mb-8 pb-8 border-b" style="border-color: #e5e7eb;">
                        <div class="profile-img-wrapper">
                            <img 
                                src="{{ Auth::user()->avatar_url }}" 
                                alt="Profile" 
                                class="w-24 h-24 rounded-full object-cover border-4 border-yellow-400 shadow-lg"
                                referrerpolicy="no-referrer"
                                onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=fbbf24&color=1e1b4b&size=128';"
                            >
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold mb-2" style="color: #111827;">{{ Auth::user()->name }}</h2>
                            <p class="mb-3" style="color: #6b7280;">{{ Auth::user()->email }}</p>
                            <span class="premium-badge inline-block px-4 py-1.5 rounded-full text-xs font-semibold text-gray-900">
                                {{ ucfirst(Auth::user()->role ?? 'user') }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
                            <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
                        </div>
                    @endif
                    
                    <!-- Biodata Section -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold" style="color: #111827;">Biodata</h2>
                            <a 
                                href="{{ route('profile.edit') }}"
                                class="gold-accent px-5 py-2.5 rounded-xl text-gray-900 font-semibold flex items-center space-x-2 transition-all duration-300 text-sm"
                            >
                                <i class="bi bi-pencil"></i>
                                <span>Edit Profile</span>
                            </a>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4" style="color: #374151;">
                            <div>
                                <span style="color: #9ca3af;">Nama:</span>
                                <span class="ml-2 font-medium">{{ Auth::user()->name }}</span>
                            </div>
                            <div>
                                <span style="color: #9ca3af;">Email:</span>
                                <span class="ml-2 font-medium">{{ Auth::user()->email }}</span>
                            </div>
                            @if(Auth::user()->phone)
                            <div>
                                <span style="color: #9ca3af;">Telepon:</span>
                                <span class="ml-2 font-medium">{{ Auth::user()->formatted_phone ?? Auth::user()->phone }}</span>
                            </div>
                            @endif
                            <div>
                                <span style="color: #9ca3af;">Event yang Diikuti:</span>
                                <span class="ml-2 font-medium">{{ $eventsCount ?? 0 }} event</span>
                            </div>
                            <div>
                                <span style="color: #9ca3af;">Course yang Diikuti:</span>
                                <span class="ml-2 font-medium">{{ $coursesCount ?? 0 }} course</span>
                            </div>
                            @if(Auth::user()->bio)
                            <div class="md:col-span-2">
                                <span style="color: #9ca3af;">Bio:</span>
                                <p class="ml-2 font-medium mt-1">{{ Auth::user()->bio }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Events Section -->
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Registered Events -->
                    <div class="glass-card rounded-2xl p-6 shadow-2xl">
                        <h2 class="text-xl font-bold mb-4 flex items-center" style="color: #111827;">
                            <i class="bi bi-calendar-check mr-2" style="color: #fbbf24;"></i>
                            Event Yang Didaftarkan
                        </h2>
                        @php($regs = Auth::user()->eventRegistrations()->with('event')->latest()->get())
                        @if($regs->isEmpty())
                            <p class="text-sm" style="color: #6b7280;">Belum ada event yang didaftarkan.</p>
                        @else
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($regs as $reg)
                                    <div class="event-card rounded-xl p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h3 class="font-semibold mb-1" style="color: #111827;">{{ $reg->event?->title ?? 'Event' }}</h3>
                                                <div class="text-xs text-gray-400 space-y-1">
                                                    @if($reg->event?->date_start)
                                                        <div><i class="bi bi-calendar mr-1"></i>{{ $reg->event->date_start->format('d M Y') }}</div>
                                                    @endif
                                                    @if($reg->event?->location)
                                                        <div><i class="bi bi-geo-alt mr-1"></i>{{ $reg->event->location }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($reg->event)
                                            <a href="{{ route('events.show', $reg->event) }}" class="gold-accent px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-900 ml-3 whitespace-nowrap" style="text-decoration: none;">
                                                Detail
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Saved Events -->
                    <div class="glass-card rounded-2xl p-6 shadow-2xl">
                        <h2 class="text-xl font-bold mb-4 flex items-center" style="color: #111827;">
                            <i class="bi bi-bookmark-star mr-2" style="color: #fbbf24;"></i>
                            Event Tersimpan
                        </h2>
                        @php($saved = Auth::user()->savedEvents()->latest('user_saved_events.created_at')->get())
                        @if($saved->isEmpty())
                            <p class="text-sm" style="color: #6b7280;">Belum ada event yang disimpan.</p>
                        @else
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach($saved as $ev)
                                    <div class="event-card rounded-xl p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h3 class="font-semibold mb-1" style="color: #111827;">{{ $ev->title ?? 'Event' }}</h3>
                                                <div class="text-xs text-gray-400 space-y-1">
                                                    @if($ev->event_date)
                                                        <div><i class="bi bi-calendar mr-1"></i>{{ \Carbon\Carbon::parse($ev->event_date)->format('d M Y') }}</div>
                                                    @endif
                                                    @if($ev->location)
                                                        <div><i class="bi bi-geo-alt mr-1"></i>{{ $ev->location }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <a href="{{ route('events.show', $ev) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold border-2 transition ml-3 whitespace-nowrap" style="border-color: #d1d5db; color: #374151; text-decoration: none;" onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='transparent'">
                                                Detail
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Badge Info Modal - Minimalis -->
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
        // Smooth scroll and animations
        document.addEventListener('DOMContentLoaded', function() {
            // Menu item active state
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    if(this.href && !this.href.includes('#')) {
                        return true;
                    }
                    e.preventDefault();
                    menuItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
            });
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
</body>
</html>
