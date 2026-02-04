<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivitas & Histori - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            padding-top: 110px; /* Increased spacer for fixed premium navbar */
        }

        .activity-container {
            max-width: 1200px; /* Increased for better laptop display */
            margin: 0 auto;
            padding: clamp(1.5rem, 3vw, 2.5rem) 1rem 4rem;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: clamp(1rem, 2vw, 1.5rem);
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
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

        .activity-item-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            transition: all 0.3s ease;
        }

        .activity-item-card:hover {
            transform: translateX(5px);
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

        .btn-certificate:hover { background: #4338ca; color: white; transform: scale(1.05); }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in { animation: fadeIn 0.5s ease-out; }

        @media (max-width: 768px) {
            .activity-container { padding: 1rem 0.75rem 2rem; }
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
            return true;
        });

        // Calculate points earned from events
        $pointsEarned = 0;
        foreach ($registrations as $reg) {
            if ($reg->event) {
                if ($reg->event->price > 0) { $pointsEarned += 30; } 
                else { $pointsEarned += 10; }
            }
        }
    @endphp

    <div class="activity-container fade-in mt-4 lg:mt-6">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl lg:text-4xl font-extrabold mb-2" style="color: #0f172a;">Aktivitas & Histori</h1>
                <p class="text-base" style="color: #64748b;">Kelola semua sertifikat dan perkembangan belajarmu di sini.</p>
            </div>
            
            <!-- Tab Navigation -->
            <div class="flex gap-2 mt-4 md:mt-0 p-1 bg-slate-100 rounded-xl">
                <button class="tab-button {{ $currentFilter === 'all' ? 'active' : '' }}" onclick="filterActivities('all')">SEMUA</button>
                <button class="tab-button {{ $currentFilter === 'completed' ? 'active' : '' }}" onclick="filterActivities('completed')">SELESAI</button>
                <button class="tab-button {{ $currentFilter === 'upcoming' ? 'active' : '' }}" onclick="filterActivities('upcoming')">AKAN DATANG</button>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div class="stat-card">
                <div class="flex items-center justify-center mb-3 mx-auto" style="width: 48px; height: 48px; background: #eef2ff; border-radius: 12px; color: #4f46e5;">
                    <i class="bi bi-calendar-event" style="font-size: 1.5rem;"></i>
                </div>
                <div class="text-3xl font-bold mb-1" style="color: #0f172a;">{{ $totalEvents }}</div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #94a3b8;">Event Diikuti</div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-center mb-3 mx-auto" style="width: 48px; height: 48px; background: #fff7ed; border-radius: 12px; color: #f59e0b;">
                    <i class="bi bi-award" style="font-size: 1.5rem;"></i>
                </div>
                <div class="text-3xl font-bold mb-1" style="color: #0f172a;">{{ $certifiedEvents }}</div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #94a3b8;">Sertifikat</div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-center mb-3 mx-auto" style="width: 48px; height: 48px; background: #e0f2fe; border-radius: 12px; color: #0ea5e9;">
                    <i class="bi bi-chat-left-text" style="font-size: 1.5rem;"></i>
                </div>
                <div class="text-3xl font-bold mb-1" style="color: #0f172a;">{{ $feedbackSubmitted }}</div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #94a3b8;">Feedback</div>
            </div>
            
            <div class="stat-card">
                <div class="flex items-center justify-center mb-3 mx-auto" style="width: 48px; height: 48px; background: #fee2e2; border-radius: 12px; color: #ef4444;">
                    <i class="bi bi-lightning-charge-fill" style="font-size: 1.5rem;"></i>
                </div>
                <div class="text-3xl font-bold mb-1" style="color: #0f172a;">+{{ number_format($pointsEarned, 0, ',', '.') }}</div>
                <div class="text-xs font-semibold uppercase tracking-wider" style="color: #94a3b8;">Poin Masuk</div>
            </div>
        </div>

        <!-- Activity List -->
        <div class="mb-10">
            <div class="flex items-center gap-3 mb-6">
                <i class="bi bi-clock-history text-2xl" style="color: #4f46e5;"></i>
                <h2 class="text-2xl font-bold" style="color: #0f172a;">Aktivitas Terbaru</h2>
            </div>

            @if($filteredRegistrations->count() > 0)
                <div class="space-y-4">
                    @foreach($filteredRegistrations as $registration)
                        @php
                            $event = $registration->event;
                            if (!$event) continue;
                            $isCompleted = $event->isFinished() || !empty($registration->certificate_issued_at);
                            $isUpcoming = !$event->isFinished() && empty($registration->certificate_issued_at);
                        @endphp
                        <div class="activity-item-card p-5">
                            <div class="flex flex-col md:flex-row gap-5">
                                <img src="{{ $event->image_url ?? 'https://via.placeholder.com/300x200' }}" class="w-full md:w-48 h-32 object-cover rounded-xl" alt="{{ $event->title }}">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="status-badge {{ $isCompleted ? 'completed' : 'upcoming' }}">{{ $isCompleted ? 'COMPLETED' : 'UPCOMING' }}</span>
                                        <span class="text-xs text-slate-400 fw-bold">{{ $event->event_date }}</span>
                                    </div>
                                    <h3 class="text-xl font-bold mb-1 text-slate-800">{{ $event->title }}</h3>
                                    <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ $event->short_description ?? 'Learn amazing skills in this event.' }}</p>
                                    <div class="flex flex-wrap gap-2">
                                        @if($isCompleted)
                                            <a href="{{ route('certificates.show', [$event->id, $registration->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-700 transition" target="_blank">
                                                <i class="bi bi-eye me-1"></i> Lihat Sertifikat
                                            </a>
                                            <a href="{{ route('certificates.download', [$event->id, $registration->id]) }}" class="inline-flex items-center px-4 py-2 border border-slate-200 rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-50 transition" target="_blank">
                                                <i class="bi bi-download me-1"></i> Unduh PDF
                                            </a>
                                        @endif
                                        <a href="{{ route('events.show', $event->id) }}" class="px-4 py-2 border rounded-lg text-sm font-bold text-slate-600 hover:bg-slate-50">Detail Event</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl p-12 text-center border border-slate-100">
                    <i class="bi bi-calendar-x text-6xl text-slate-200 mb-4"></i>
                    <h3 class="text-xl font-bold text-slate-800">Tidak ada aktivitas ditemukan</h3>
                    <p class="text-slate-500 mb-6">Mulai ikuti event menarik untuk mengembangkan skill Anda.</p>
                    <a href="{{ route('events.index') }}" class="btn-certificate inline-block">Cari Event</a>
                </div>
            @endif
        </div>

        <!-- Saved Events (Integrated from Main) -->
        <div class="bg-white border border-slate-100 rounded-3xl p-6 lg:p-8 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold" style="color:#0f172a;">
                    <i class="bi bi-bookmark-star me-2" style="color:#f59e0b;"></i>Event Tersimpan
                </h2>
                <span class="text-sm font-semibold text-slate-400">{{ isset($savedEvents) ? $savedEvents->count() : 0 }} tersimpan</span>
            </div>
            @if(isset($savedEvents) && $savedEvents->count() > 0)
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($savedEvents as $sevent)
                        <div class="p-4 border border-slate-100 rounded-2xl hover:border-indigo-100 transition-all">
                            <h3 class="font-bold text-slate-800 mb-2 truncate">{{ $sevent->title }}</h3>
                            <div class="text-xs text-slate-500 space-y-1 mb-4">
                                <div class="flex items-center gap-1"><i class="bi bi-calendar3"></i> {{ $sevent->event_date }}</div>
                                <div class="flex items-center gap-1"><i class="bi bi-geo-alt"></i> {{ $sevent->location ?? 'Online' }}</div>
                            </div>
                            <div class="flex items-center justify-between">
                                <a href="{{ route('events.show', $sevent->id) }}" class="text-xs font-bold text-indigo-600">Lihat Detail</a>
                                <button class="text-xs text-slate-400 hover:text-red-500" onclick="alert('Unsave event: {{ $sevent->id }}')">Unsave</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-slate-400 italic">Belum ada event yang Anda simpan.</p>
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
