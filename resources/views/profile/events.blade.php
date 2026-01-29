<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas & Histori - idSPORA</title>
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
        
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            padding-top: 85px; 
        }

        /* Activity page: centered, not full-width */
        .activity-container {
            max-width: 1040px;
            margin: 0 auto;
            padding: 1.25rem 1rem 2.75rem;
        }

        .activity-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.06);
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.05);
            padding: 1.25rem;
            text-align: center;
        }

        .tab-button {
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            background: transparent;
            color: #64748b;
        }

        .tab-button.active {
            background: #4f46e5;
            color: #ffffff;
        }

        .tab-button:hover:not(.active) {
            background: #f1f5f9;
            color: #0f172a;
        }

        .activity-item-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
        }

        .activity-item-card:hover {
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.08);
            transform: translateY(-2px);
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-badge.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.upcoming {
            background: #dbeafe;
            color: #1e40af;
        }

        .btn-certificate {
            background: #4f46e5;
            color: #ffffff;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .btn-certificate:hover {
            background: #4338ca;
            transform: translateY(-1px);
        }

        .btn-feedback {
            background: #fbbf24;
            color: #111827;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .btn-feedback:hover {
            background: #f59e0b;
            transform: translateY(-1px);
        }

        .btn-detail {
            background: #f1f5f9;
            color: #374151;
            border: none;
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .btn-detail:hover {
            background: #e2e8f0;
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

        @media (max-width: 768px) {
            .activity-container {
                padding: 1rem 0.75rem 2rem;
            }
            .stat-card {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")
    
    @php
        $user = Auth::user();
        $currentFilter = request()->get('filter', 'all');
        
<<<<<<< Updated upstream
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
                
                <!-- Saved Events (Event Tersimpan) -->
                <div class="glass-card rounded-2xl p-6 shadow-lg mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold" style="color:#111827;">
                            <i class="bi bi-bookmark-star me-2" style="color:#f59e0b;"></i>Event Tersimpan
                        </h2>
                        <span class="text-sm" style="color:#6b7280;">{{ isset($savedEvents) ? $savedEvents->count() : 0 }} tersimpan</span>
                    </div>
                    @if(isset($savedEvents) && $savedEvents->count() > 0)
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($savedEvents as $sevent)
                                <div class="event-card rounded-xl p-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold mb-1" style="color:#111827;">{{ $sevent->title }}</h3>
                                            @if(!empty($sevent->event_date))
                                                <p class="text-xs mb-1" style="color:#6b7280;">
                                                    <i class="bi bi-calendar3 me-1" style="color:#667eea;"></i>
                                                    {{ \Carbon\Carbon::parse($sevent->event_date)->format('d F Y') }}
                                                </p>
                                            @endif
                                            @if(!empty($sevent->location))
                                                <p class="text-xs" style="color:#6b7280;">
                                                    <i class="bi bi-geo-alt me-1" style="color:#667eea;"></i>{{ $sevent->location }}
                                                </p>
                                            @endif
                                        </div>
                                        <button class="px-3 py-1 rounded text-xs font-semibold" style="background:#f3f4f6; color:#374151; border:none;" data-event-id="{{ $sevent->id }}" onclick="unsaveEvent(this)">
                                            Unsave
                                        </button>
                                    </div>
                                    <div class="flex items-center justify-between mt-3">
                                        <a href="{{ route('events.show', $sevent->id) }}" class="px-3 py-2 rounded-lg border-2 text-xs font-semibold" style="border-color:#d1d5db; color:#374151; text-decoration:none;">Detail</a>
                                        <span class="text-xs" style="color:#6b7280;">Disimpan: {{ \Carbon\Carbon::parse($sevent->saved_at)->diffForHumans() }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <i class="bi bi-bookmark text-3xl mb-2" style="color:#d1d5db;"></i>
                            <p class="text-sm" style="color:#6b7280;">Belum ada event tersimpan</p>
                        </div>
                    @endif
                </div>
=======
        // Filter registrations based on tab
        $filteredRegistrations = $registrations->filter(function($reg) use ($currentFilter) {
            if (!$reg->event) return false;
            $event = $reg->event;
            
            if ($currentFilter === 'completed') {
                return $event->isFinished() || !empty($reg->certificate_issued_at);
            } elseif ($currentFilter === 'upcoming') {
                return !$event->isFinished() && empty($reg->certificate_issued_at);
            }
            return true; // 'all'
        });
>>>>>>> Stashed changes

        // Calculate points earned from events
        $pointsEarned = 0;
        foreach ($registrations as $reg) {
            if ($reg->event) {
                if ($reg->event->price > 0) {
                    $pointsEarned += 30; // Paid event
                } else {
                    $pointsEarned += 10; // Free event
                }
            }
        }
    @endphp

<<<<<<< Updated upstream
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
                                $amountPaid = $payment ? $payment->amount : 0;
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
=======
    <div class="activity-container fade-in">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <div>
                <h1 class="text-4xl font-bold mb-2" style="color: #0f172a;">Aktivitas & Histori</h1>
                <p class="text-base" style="color: #64748b;">Kelola semua sertifikat dan perkembangan belajarmu di sini.</p>
            </div>
            
            <!-- Tab Navigation -->
            <div class="flex gap-2 mt-4 md:mt-0">
                <button class="tab-button {{ $currentFilter === 'all' ? 'active' : '' }}" onclick="filterActivities('all')">
                    SEMUA
                </button>
                <button class="tab-button {{ $currentFilter === 'completed' ? 'active' : '' }}" onclick="filterActivities('completed')">
                    SELESAI
                </button>
                <button class="tab-button {{ $currentFilter === 'upcoming' ? 'active' : '' }}" onclick="filterActivities('upcoming')">
                    AKAN DATANG
                </button>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="stat-card">
                <div class="flex items-center justify-center mb-3" style="width: 48px; height: 48px; margin: 0 auto; background: #eef2ff; border-radius: 12px; color: #4f46e5;">
                    <i class="bi bi-calendar-event" style="font-size: 1.5rem;"></i>
                </div>
                <div class="text-3xl font-bold mb-1" style="color: #0f172a;">{{ $totalEvents }}</div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #94a3b8;">Event Diikuti</div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-center mb-3" style="width: 48px; height: 48px; margin: 0 auto; background: #fff7ed; border-radius: 12px; color: #f59e0b;">
                    <i class="bi bi-award" style="font-size: 1.5rem;"></i>
                </div>
                <div class="text-3xl font-bold mb-1" style="color: #0f172a;">{{ $certifiedEvents }}</div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #94a3b8;">Sertifikat</div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-center mb-3" style="width: 48px; height: 48px; margin: 0 auto; background: #e0f2fe; border-radius: 12px; color: #0ea5e9;">
                    <i class="bi bi-chat-left-text" style="font-size: 1.5rem;"></i>
                </div>
                <div class="text-3xl font-bold mb-1" style="color: #0f172a;">{{ $feedbackSubmitted }}</div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #94a3b8;">Feedback</div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-center mb-3" style="width: 48px; height: 48px; margin: 0 auto; background: #fee2e2; border-radius: 12px; color: #ef4444;">
                    <i class="bi bi-lightning-charge-fill" style="font-size: 1.5rem;"></i>
                </div>
                <div class="text-3xl font-bold mb-1" style="color: #0f172a;">+{{ number_format($pointsEarned, 0, ',', '.') }}</div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #94a3b8;">Poin Masuk</div>
            </div>
        </div>

        <!-- Activity List -->
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4">
                <i class="bi bi-clock-history" style="font-size: 1.25rem; color: #4f46e5;"></i>
                <h2 class="text-xl font-bold" style="color: #0f172a;">Daftar Aktivitas Terbaru</h2>
            </div>

            @if($filteredRegistrations->count() > 0)
                <div class="space-y-4">
                    @foreach($filteredRegistrations as $registration)
                        @php
                            $event = $registration->event;
                            if (!$event) continue;
                            
                            $isFinished = $event->isFinished();
                            $isCompleted = $isFinished || !empty($registration->certificate_issued_at);
                            $isUpcoming = !$isFinished && empty($registration->certificate_issued_at);
                            
                            $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : null;
                            $eventImage = $event->image_url ?? 'https://via.placeholder.com/120x80?text=Event';
                            $mentorName = $event->speaker ?? 'Expert Industry Leader';
                            
                            $isCertificateReady = false;
                            if($event->event_date) {
                                $eventDateObj = \Carbon\Carbon::parse($event->event_date);
                                $isCertificateReady = now()->greaterThanOrEqualTo($eventDateObj->copy()->addDays(3));
                            }
                        @endphp
                        
                        <div class="activity-item-card p-5">
                            <div class="flex flex-col md:flex-row gap-4">
                                <!-- Image Thumbnail -->
                                <div class="flex-shrink-0">
                                    <img 
                                        src="{{ $eventImage }}" 
                                        alt="{{ $event->title }}"
                                        class="w-full md:w-32 h-24 object-cover rounded-xl"
                                        style="min-width: 120px;"
                                        onerror="this.onerror=null; this.src='https://via.placeholder.com/120x80/4f46e5/ffffff?text={{ urlencode(substr($event->title, 0, 10)) }}';"
                                    >
>>>>>>> Stashed changes
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="status-badge {{ $isCompleted ? 'completed' : 'upcoming' }}">
                                            {{ $isCompleted ? 'COMPLETED' : 'UPCOMING' }}
                                        </span>
                                        @if($eventDate)
                                            <span class="text-sm" style="color: #64748b;">{{ $eventDate }}</span>
                                        @endif
                                    </div>
                                    
                                    <h3 class="text-xl font-bold mb-2" style="color: #0f172a;">{{ $event->title }}</h3>
                                    <p class="text-sm mb-4" style="color: #64748b;">Mentor: {{ $mentorName }}</p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="flex flex-wrap gap-2">
                                        @if($isCompleted && $isCertificateReady && Route::has('certificates.download'))
                                            <a 
                                                href="{{ route('certificates.download', [$event, $registration]) }}" 
                                                class="btn-certificate inline-flex items-center gap-2"
                                                style="text-decoration: none;"
                                            >
                                                <i class="bi bi-download"></i>
                                                Sertifikat
                                            </a>
                                        @endif
                                        
                                        @if($isCompleted && !$registration->feedback_submitted_at && Route::has('events.show'))
                                            <a 
                                                href="{{ route('events.show', $event) }}#feedback" 
                                                class="btn-feedback inline-flex items-center gap-2"
                                                style="text-decoration: none;"
                                            >
                                                <i class="bi bi-chat-left-text"></i>
                                                Kirim Feedback
                                            </a>
                                        @endif
                                        
                                        @if($isUpcoming && Route::has('events.show'))
                                            <a 
<<<<<<< Updated upstream
                                                href="{{ route('events.show', $event->id) }}" 
                                                class="px-4 py-2 rounded-lg border-2 font-semibold transition-all duration-300 text-sm"
                                                style="border-color: #d1d5db; color: #374151; text-decoration: none;"
                                                onmouseover="this.style.backgroundColor='#f9fafb'"
                                                onmouseout="this.style.backgroundColor='transparent'"
=======
                                                href="{{ route('events.show', $event) }}" 
                                                class="btn-detail inline-flex items-center gap-2"
                                                style="text-decoration: none;"
>>>>>>> Stashed changes
                                            >
                                                Lihat Detail
                                            </a>
                                        @endif
                                        
                                        @if($isCompleted && Route::has('events.show'))
                                            <a 
                                                href="{{ route('events.show', $event) }}" 
                                                class="btn-detail inline-flex items-center gap-2"
                                                style="text-decoration: none;"
                                            >
                                                Lihat Detail
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="activity-card p-12 text-center">
                    <i class="bi bi-calendar-x text-6xl mb-4" style="color: #d1d5db;"></i>
                    <h3 class="text-xl font-bold mb-2" style="color: #0f172a;">
                        @if($currentFilter === 'completed')
                            Belum Ada Aktivitas yang Selesai
                        @elseif($currentFilter === 'upcoming')
                            Belum Ada Aktivitas yang Akan Datang
                        @else
                            Belum Ada Aktivitas
                        @endif
                    </h3>
                    <p class="text-sm mb-6" style="color: #64748b;">
                        @if($currentFilter === 'all')
                            Anda belum mengikuti event apapun
                        @else
                            Tidak ada aktivitas yang sesuai dengan filter ini
                        @endif
                    </p>
                    @if($currentFilter !== 'all')
                        <button onclick="filterActivities('all')" class="btn-certificate inline-flex items-center gap-2" style="text-decoration: none;">
                            Lihat Semua Aktivitas
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <script>
        function filterActivities(filter) {
            const url = new URL(window.location.href);
            url.searchParams.set('filter', filter);
            window.location.href = url.toString();
        }
    </script>
    
    @include('partials.footer-after-login')
</body>
</html>
