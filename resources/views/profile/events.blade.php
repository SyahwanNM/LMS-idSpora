<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Event - idSPORA</title>
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
        
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            padding-top: 70px;
        }
        
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
        
        #notifBtn,
        #userDropdown {
            color: white !important;
        }
        
        #notifBtn:hover,
        #userDropdown:hover {
            color: #ffe8b3 !important;
        }
        
        .navbar .form-control {
            color: white !important;
        }
        
        .navbar .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        .navbar .container-fluid {
            display: flex !important;
            visibility: visible !important;
        }
        
        .navbar-brand,
        .navbar-nav,
        .navbar-collapse {
            display: flex !important;
            visibility: visible !important;
        }
        
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
        
        .main-content-with-sidebar {
            margin-left: 280px;
            padding: 2rem;
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
        
        .gold-accent {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        }
        
        .gold-accent:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
            transform: translateY(-1px);
        }
        
        .badge-status {
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-status.active {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-status.pending {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-status.canceled {
            background: #fee2e2;
            color: #991b1b;
        }
        
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
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            .main-content-with-sidebar {
                margin-left: 0;
                margin-top: 0;
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            .flex.min-h-screen {
                flex-direction: column;
            }
            .glass-sidebar {
                width: 100%;
            }
            .main-content-with-sidebar {
                padding: 1rem;
            }
            .glass-card {
                padding: 1.5rem !important;
            }
            .menu-item {
                padding: 0.75rem !important;
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
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold mb-2" style="color: #111827;">History Event</h1>
                    <p class="text-sm" style="color: #6b7280;">Daftar event yang telah Anda ikuti</p>
                </div>
                
                <!-- Track Record Statistics -->
                @if($registrations->count() > 0)
                <div class="glass-card rounded-2xl p-6 shadow-lg mb-6">
                    <h2 class="text-xl font-bold mb-4" style="color: #111827;">
                        <i class="bi bi-graph-up-arrow me-2" style="color: #667eea;"></i>Track Record
                    </h2>
                    <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-blue-600 mb-1">Total Event</p>
                                    <p class="text-2xl font-bold text-blue-900">{{ $totalEvents }}</p>
                                </div>
                                <i class="bi bi-calendar-event text-3xl text-blue-500"></i>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-green-600 mb-1">Event Berbayar</p>
                                    <p class="text-2xl font-bold text-green-900">{{ $paidEvents }}</p>
                                </div>
                                <i class="bi bi-credit-card text-3xl text-green-500"></i>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-purple-600 mb-1">Event Gratis</p>
                                    <p class="text-2xl font-bold text-purple-900">{{ $freeEvents }}</p>
                                </div>
                                <i class="bi bi-gift text-3xl text-purple-500"></i>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-yellow-600 mb-1">Total Spending</p>
                                    <p class="text-2xl font-bold text-yellow-900">Rp {{ number_format($totalSpending, 0, ',', '.') }}</p>
                                </div>
                                <i class="bi bi-wallet2 text-3xl text-yellow-500"></i>
                            </div>
                        </div>
                    </div>
                    <div class="grid md:grid-cols-3 gap-4 pt-4 border-t" style="border-color: #e5e7eb;">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="bi bi-check-circle-fill text-2xl me-2" style="color: #22c55e;"></i>
                                <span class="text-lg font-bold" style="color: #111827;">{{ $attendedEvents }}</span>
                            </div>
                            <p class="text-xs" style="color: #6b7280;">Event Dihadiri</p>
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="bi bi-award-fill text-2xl me-2" style="color: #fbbf24;"></i>
                                <span class="text-lg font-bold" style="color: #111827;">{{ $certifiedEvents }}</span>
                            </div>
                            <p class="text-xs" style="color: #6b7280;">Sertifikat Diperoleh</p>
                        </div>
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-2">
                                <i class="bi bi-chat-left-text-fill text-2xl me-2" style="color: #667eea;"></i>
                                <span class="text-lg font-bold" style="color: #111827;">{{ $feedbackSubmitted }}</span>
                            </div>
                            <p class="text-xs" style="color: #6b7280;">Feedback Dikirim</p>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Events List -->
                @if($registrations->count() > 0)
                    <div class="space-y-4">
                        @foreach($registrations as $registration)
                            @php
                                $event = $registration->event;
                                $isCertificateReady = false;
                                if($event && $event->event_date) {
                                    $eventDate = \Carbon\Carbon::parse($event->event_date);
                                    $isCertificateReady = now()->greaterThanOrEqualTo($eventDate->copy()->addDays(3));
                                }
                                // Check if event is paid or free
                                $isPaid = $event && $event->price > 0;
                                $payment = isset($payments[$event->id ?? 0]) ? $payments[$event->id] : null;
                                $amountPaid = $payment ? $payment->gross_amount : 0;
                                $finalPrice = $event ? ($event->hasDiscount() ? $event->discounted_price : $event->price) : 0;
                            @endphp
                            <div class="glass-card rounded-2xl p-6 shadow-lg event-card">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-3 flex-wrap">
                                            <h3 class="text-xl font-bold" style="color: #111827;">{{ $event->title ?? 'Event Tidak Ditemukan' }}</h3>
                                            <span class="badge-status {{ $registration->status }}">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                            @if($isPaid)
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: #fef3c7; color: #92400e;">
                                                    <i class="bi bi-credit-card me-1"></i>Berbayar
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: #dbeafe; color: #1e40af;">
                                                    <i class="bi bi-gift me-1"></i>Gratis
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($event)
                                            <div class="grid md:grid-cols-2 gap-4 mb-4" style="color: #6b7280;">
                                                @if($event->event_date)
                                                    <div class="flex items-center space-x-2">
                                                        <i class="bi bi-calendar3" style="color: #667eea;"></i>
                                                        <span><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}</span>
                                                    </div>
                                                @endif
                                                @if($event->event_time)
                                                    <div class="flex items-center space-x-2">
                                                        <i class="bi bi-clock" style="color: #667eea;"></i>
                                                        <span><strong>Waktu:</strong> {{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }} 
                                                        @if($event->event_time_end)
                                                            - {{ \Carbon\Carbon::parse($event->event_time_end)->format('H:i') }}
                                                        @endif
                                                        </span>
                                                    </div>
                                                @endif
                                                @if($event->location)
                                                    <div class="flex items-center space-x-2">
                                                        <i class="bi bi-geo-alt" style="color: #667eea;"></i>
                                                        <span><strong>Lokasi:</strong> {{ $event->location }}</span>
                                                    </div>
                                                @endif
                                                @if($registration->registration_code)
                                                    <div class="flex items-center space-x-2">
                                                        <i class="bi bi-ticket-perforated" style="color: #667eea;"></i>
                                                        <span><strong>Kode:</strong> {{ $registration->registration_code }}</span>
                                                    </div>
                                                @endif
                                                @if($isPaid)
                                                    <div class="flex items-center space-x-2">
                                                        <i class="bi bi-currency-dollar" style="color: #fbbf24;"></i>
                                                        <span><strong>Harga:</strong> Rp {{ number_format($finalPrice, 0, ',', '.') }}</span>
                                                        @if($payment)
                                                            <span class="text-xs px-2 py-1 rounded" style="background: #dcfce7; color: #166534;">
                                                                <i class="bi bi-check-circle me-1"></i>Dibayar: Rp {{ number_format($amountPaid, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                                @if($registration->attended_at)
                                                    <div class="flex items-center space-x-2">
                                                        <i class="bi bi-check-circle-fill" style="color: #22c55e;"></i>
                                                        <span><strong>Hadir:</strong> {{ \Carbon\Carbon::parse($registration->attended_at)->format('d M Y H:i') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if($event->short_description)
                                                <p class="text-sm mb-4" style="color: #6b7280;">{{ \Illuminate\Support\Str::limit($event->short_description, 150) }}</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between pt-4 border-t" style="border-color: #e5e7eb;">
                                    <div class="flex items-center space-x-4 flex-wrap">
                                        <!-- Status Indicators -->
                                        <div class="flex items-center space-x-2">
                                            @if($registration->attendance_status)
                                                <span class="text-xs px-2 py-1 rounded-full" style="background: #dcfce7; color: #166534;">
                                                    <i class="bi bi-check-circle me-1"></i>Hadir
                                                </span>
                                            @else
                                                <span class="text-xs px-2 py-1 rounded-full" style="background: #fef2f2; color: #991b1b;">
                                                    <i class="bi bi-x-circle me-1"></i>Belum Hadir
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($registration->certificate_issued_at)
                                                <span class="text-xs px-2 py-1 rounded-full" style="background: #fef3c7; color: #92400e;">
                                                    <i class="bi bi-award me-1"></i>Sertifikat
                                                </span>
                                            @else
                                                <span class="text-xs px-2 py-1 rounded-full" style="background: #f3f4f6; color: #6b7280;">
                                                    <i class="bi bi-award me-1"></i>Belum Ada Sertifikat
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($registration->feedback_submitted_at)
                                                <span class="text-xs px-2 py-1 rounded-full" style="background: #dbeafe; color: #1e40af;">
                                                    <i class="bi bi-chat-left-text me-1"></i>Feedback Terkirim
                                                </span>
                                            @else
                                                <span class="text-xs px-2 py-1 rounded-full" style="background: #f3f4f6; color: #6b7280;">
                                                    <i class="bi bi-chat-left-text me-1"></i>Belum Feedback
                                                </span>
                                            @endif
                                        </div>
                                        @if($registration->certificate_number)
                                            <span class="text-xs" style="color: #6b7280;">
                                                <i class="bi bi-hash"></i> No. Sertifikat: {{ $registration->certificate_number }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-3 flex-wrap">
                                        @if($event)
                                            <a 
                                                href="{{ route('events.show', $event) }}" 
                                                class="px-4 py-2 rounded-lg border-2 font-semibold transition-all duration-300 text-sm"
                                                style="border-color: #d1d5db; color: #374151; text-decoration: none;"
                                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                                onmouseout="this.style.backgroundColor='transparent'"
                                            >
                                                <i class="bi bi-eye mr-2"></i>Detail
                                            </a>
                                        @endif
                                        
                                        @if($event && Route::has('certificates.show'))
                                            <a 
                                                href="{{ route('certificates.show', [$event, $registration]) }}" 
                                                class="px-4 py-2 rounded-lg font-semibold transition-all duration-300 text-sm"
                                                style="background: #535088; color: #f4d24b; text-decoration: none;"
                                                onmouseover="this.style.filter='brightness(1.1)'"
                                                onmouseout="this.style.filter='brightness(1)'"
                                            >
                                                <i class="bi bi-eye mr-2"></i>
                                                <span>@if($isCertificateReady) Lihat / Unduh Sertifikat @else Preview Sertifikat @endif</span>
                                            </a>
                                        @endif
                                        
                                        @if($event && Route::has('certificates.download'))
                                            @if($isCertificateReady)
                                                <a 
                                                    href="{{ route('certificates.download', [$event, $registration]) }}" 
                                                    class="gold-accent px-4 py-2 rounded-lg text-gray-900 font-semibold flex items-center space-x-2 transition-all duration-300 text-sm"
                                                    style="text-decoration: none;"
                                                >
                                                    <i class="bi bi-download"></i>
                                                    <span>Download Sertifikat</span>
                                                </a>
                                            @else
                                                <a 
                                                    href="{{ route('certificates.download', [$event, $registration]) }}?force=1" 
                                                    class="px-4 py-2 rounded-lg font-semibold flex items-center space-x-2 transition-all duration-300 text-sm"
                                                    style="background: #f59e0b; color: #fff; text-decoration: none;"
                                                    title="Download untuk testing (bypass H+3)"
                                                >
                                                    <i class="bi bi-download"></i>
                                                    <span>Download (Testing)</span>
                                                </a>
                                            @endif
                                        @endif
                                        
                                        @if($event && !$isCertificateReady)
                                            <span 
                                                class="px-4 py-2 rounded-lg font-semibold text-sm"
                                                style="background: #f3f4f6; color: #9ca3af; cursor: not-allowed;"
                                            >
                                                <i class="bi bi-clock mr-2"></i>Sertifikat belum tersedia (H+3)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="glass-card rounded-2xl p-12 text-center">
                        <i class="bi bi-calendar-x text-6xl mb-4" style="color: #d1d5db;"></i>
                        <h3 class="text-xl font-bold mb-2" style="color: #111827;">Belum Ada Event</h3>
                        <p class="text-sm mb-6" style="color: #6b7280;">Anda belum mengikuti event apapun</p>
                        <a 
                            href="{{ route('events.index') }}" 
                            class="gold-accent px-6 py-3 rounded-xl text-gray-900 font-semibold inline-flex items-center space-x-2 transition-all duration-300"
                            style="text-decoration: none;"
                        >
                            <i class="bi bi-calendar-plus"></i>
                            <span>Lihat Event</span>
                        </a>
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>