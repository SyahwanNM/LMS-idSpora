<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity & History - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        .activity-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            text-align: center;
        }

        .tab-button {
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.813rem;
            transition: all 0.2s;
            border: 1px solid transparent;
            background: white;
            color: #64748b;
        }

        .tab-button.active {
            background: #4f46e5;
            color: white;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        .tab-button:not(.active):hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        .activity-item-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .activity-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            letter-spacing: 0.05em;
        }

        .status-badge.completed {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.upcoming {
            background: #e0f2fe;
            color: #075985;
        }

        .btn-certificate {
            background: #4f46e5;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.813rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-certificate:hover {
            background: #4338ca;
            transform: scale(1.02);
            color: white;
        }

        .btn-feedback {
            background: #f5f3ff;
            color: #6d28d9;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.813rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-feedback:hover {
            background: #ede9fe;
            color: #5b21b6;
        }

        .btn-detail {
            background: #f8fafc;
            color: #475569;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-size: 0.813rem;
            font-weight: 600;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .btn-detail:hover {
            background: #f1f5f9;
            color: #334155;
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
                                        
                                        @if(Route::has('events.show'))
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

        <!-- Saved Events Section (Integrating from main/Updated upstream) -->
        <div class="glass-card rounded-2xl p-6 shadow-sm mb-6 bg-white border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold" style="color:#0f172a;">
                    <i class="bi bi-bookmark-star me-2" style="color:#f59e0b;"></i>Event Tersimpan
                </h2>
                <span class="text-sm" style="color:#64748b;">{{ isset($savedEvents) ? $savedEvents->count() : 0 }} tersimpan</span>
            </div>
            @if(isset($savedEvents) && $savedEvents->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($savedEvents as $sevent)
                        <div class="activity-item-card rounded-xl p-4">
                            <div class="flex items-start justify-between">
                                <div class="min-w-0 flex-1">
                                    <h3 class="text-lg font-semibold mb-1 truncate" style="color:#0f172a;">{{ $sevent->title }}</h3>
                                    @if(!empty($sevent->event_date))
                                        <p class="text-xs mb-1" style="color:#64748b;">
                                            <i class="bi bi-calendar3 me-1" style="color:#4f46e5;"></i>
                                            {{ \Carbon\Carbon::parse($sevent->event_date)->format('d F Y') }}
                                        </p>
                                    @endif
                                    @if(!empty($sevent->location))
                                        <p class="text-xs truncate" style="color:#64748b;">
                                            <i class="bi bi-geo-alt me-1" style="color:#4f46e5;"></i>{{ $sevent->location }}
                                        </p>
                                    @endif
                                </div>
                                <button class="px-3 py-1 rounded text-xs font-semibold" style="background:#f1f5f9; color:#475569; border:none;" data-event-id="{{ $sevent->id }}" onclick="unsaveEvent(this)">
                                    Unsave
                                </button>
                            </div>
                            <div class="flex items-center justify-between mt-3">
                                <a href="{{ route('events.show', $sevent->id) }}" class="px-3 py-1.5 rounded-lg border text-xs font-semibold" style="border-color:#e2e8f0; color:#475569; text-decoration:none;">Detail</a>
                                <span class="text-[10px]" style="color:#94a3b8;">{{ \Carbon\Carbon::parse($sevent->saved_at)->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="bi bi-bookmark text-4xl mb-2" style="color:#e2e8f0;"></i>
                    <p class="text-sm" style="color:#64748b;">Belum ada event tersimpan</p>
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

        function unsaveEvent(btn) {
            const eventId = btn.getAttribute('data-event-id');
            // Assuming there's an endpoint for this, if not, this is just UI logic placeholder
            alert('Fitur unsave event ID: ' + eventId);
        }
    </script>
    
    @include('partials.footer-after-login')
</body>
</html>
