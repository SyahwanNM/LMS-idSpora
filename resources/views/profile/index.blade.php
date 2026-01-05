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
    </script>
</body>
</html>d