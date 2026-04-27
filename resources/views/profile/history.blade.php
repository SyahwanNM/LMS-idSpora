<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Belajar - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            padding-top: 110px;
        }
        .activity-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem 1rem 4rem;
        }
        
        .main-tab {
            padding: 1rem 2rem; border-radius: 16px; font-weight: 800; font-size: 0.95rem;
            transition: all 0.3s ease; border: 2px solid transparent; background: white; color: #64748b;
            flex: 1; text-align: center; cursor: pointer;
        }
        .main-tab.active {
            background: #ffffff; color: #4f46e5; border-color: #4f46e5;
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.1);
        }
        
        .sub-filter {
            padding: 0.5rem 1.25rem; border-radius: 12px; font-weight: 700; font-size: 0.75rem;
            transition: all 0.2s; border: 1px solid transparent; background: transparent; color: #64748b;
            cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .sub-filter.active {
            background: #4f46e5; color: white; box-shadow: 0 4px 10px rgba(79, 70, 229, 0.3);
        }

        .activity-item-card {
            background: white; border-radius: 24px; border: 1px solid #f1f5f9; transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: block; opacity: 1; scale: 1;
        }
        .activity-item-card:hover {
            transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
            border-color: #e2e8f0;
        }
        .activity-item-card.hidden-filter { display: none !important; }
        
        .status-badge {
            font-size: 0.6rem; font-weight: 800; padding: 0.35rem 0.75rem; border-radius: 50px;
            letter-spacing: 0.03em;
        }
        .status-badge.completed { background: #dcfce7; color: #166534; }
        .status-badge.ongoing { background: #e0f2fe; color: #075985; }
        .status-badge.saved { background: #fef3c7; color: #92400e; }
        
        .fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        
        .stat-icon {
            width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")
    
    @php
        $allActivities = collect();
        $registeredEventIds = $registrations->pluck('event_id')->all();
        $enrolledCourseIds = $enrollments->pluck('course_id')->all();
        
        // Add registrations
        foreach($registrations as $reg) {
            $event = $reg->event;
            if(!$event) continue;
            $isFinished = $event->isFinished() || !empty($reg->certificate_issued_at);
            $allActivities->push([
                'type' => 'EVENT',
                'id' => $event->id,
                'title' => $event->title,
                'image' => $event->image_url ?? asset('aset/poster.png'),
                'date' => $event->event_date ? $event->event_date : '-',
                'description' => $event->short_description,
                'status' => $isFinished ? 'history' : 'ongoing',
                'is_saved' => in_array($event->id, $savedEventIds),
                'certificate_route' => $isFinished ? route('certificates.download', [$event->id, $reg->id]) : null,
                'detail_route' => route('events.show', $event->id),
                'save_route' => route('events.save', $event->id)
            ]);
        }
        
        // Add saved events (that are NOT registered)
        foreach($savedEvents as $event) {
            if(in_array($event->id, $registeredEventIds)) continue;
            $eventSavedAt = $savedEventAtMap[$event->id] ?? null;
            $allActivities->push([
                'type' => 'EVENT',
                'id' => $event->id,
                'title' => $event->title,
                'image' => $event->image_url ?? asset('aset/poster.png'),
                'date' => $eventSavedAt ? \Carbon\Carbon::parse($eventSavedAt)->format('d-m-Y') : '-',
                'description' => $event->short_description,
                'status' => 'saved',
                'is_saved' => true,
                'certificate_route' => null,
                'detail_route' => route('events.show', $event->id),
                'save_route' => route('events.save', $event->id)
            ]);
        }
        
        // Add enrollments
        foreach($enrollments as $enr) {
            $course = $enr->course;
            if(!$course) continue;
            $isCompleted = ($enr->status === 'completed') || !empty($enr->certificate_issued_at);
            $allActivities->push([
                'type' => 'COURSE',
                'id' => $course->id,
                'title' => $course->name,
                'image' => $course->card_thumbnail_url ?? 'https://via.placeholder.com/400x250',
                'date' => $enr->enrolled_at ? $enr->enrolled_at->format('d-m-Y') : '-',
                'description' => $course->short_description,
                'status' => $isCompleted ? 'history' : 'ongoing',
                'is_saved' => in_array($course->id, $savedCourseIds),
                'certificate_route' => $isCompleted ? route('course.certificates.download', [$course->id, $enr->id]) : null,
                'detail_route' => route('course.detail', $course->id),
                'save_route' => route('courses.save', $course->id)
            ]);
        }

        // Add saved courses (that are NOT enrolled)
        foreach($savedCourses as $course) {
            if(in_array($course->id, $enrolledCourseIds)) continue;
            $savedAt = $savedAtMap[$course->id] ?? null;
            $allActivities->push([
                'type' => 'COURSE',
                'id' => $course->id,
                'title' => $course->name,
                'image' => $course->card_thumbnail_url ?? 'https://via.placeholder.com/400x250',
                'date' => $savedAt ? \Carbon\Carbon::parse($savedAt)->format('d-m-Y') : '-',
                'description' => $course->short_description,
                'status' => 'saved',
                'is_saved' => true,
                'certificate_route' => null,
                'detail_route' => route('course.detail', $course->id),
                'save_route' => route('courses.save', $course->id)
            ]);
        }

        $allActivities = $allActivities->sortByDesc('date');
    @endphp

    <div class="activity-container fade-in mt-6">
        <div class="mb-10">
            <h1 class="text-3xl font-black mb-2" style="color: #0f172a;">Riwayat Aktivitas</h1>
            <p class="text-slate-500 font-medium">Pantau aktivitas belajar dan kelola sertifikat kelulusan.</p>
        </div>

        <!-- Main Navigation Tabs -->
        <div class="flex gap-4 mb-8">
            <div class="main-tab active" id="tab-event" onclick="setMainTab('EVENT')">
                <i class="bi bi-calendar-check me-2"></i> EVENT
            </div>
            <div class="main-tab" id="tab-course" onclick="setMainTab('COURSE')">
                <i class="bi bi-mortarboard me-2"></i> COURSE
            </div>
            <div class="main-tab" id="tab-log" onclick="setMainTab('LOG')">
                <i class="bi bi-clock-history me-2"></i> AKTIVITAS
            </div>
        </div>

        <!-- Stats Section (Dynamic based on tab) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-[20px] border border-slate-100 flex items-center gap-4">
                <div class="stat-icon bg-indigo-50 text-indigo-600"><i class="bi bi-collection"></i></div>
                <div>
                    <div class="text-sm font-bold text-slate-400 leading-tight">TOTAL</div>
                    <div class="text-2xl font-black text-slate-800" id="stat-total">{{ $totalEventsCount }}</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-[20px] border border-slate-100 flex items-center gap-4">
                <div class="stat-icon bg-sky-50 text-sky-600"><i class="bi bi-play-circle"></i></div>
                <div>
                    <div class="text-sm font-bold text-slate-400 leading-tight">AKTIVITAS</div>
                    <div class="text-2xl font-black text-slate-800" id="stat-ongoing">{{ $totalEventsCount - $certifiedEvents }}</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-[20px] border border-slate-100 flex items-center gap-4">
                <div class="stat-icon bg-emerald-50 text-emerald-600"><i class="bi bi-check-circle"></i></div>
                <div>
                    <div class="text-sm font-bold text-slate-400 leading-tight">HISTORI</div>
                    <div class="text-2xl font-black text-slate-800" id="stat-history">{{ $certifiedEvents }}</div>
                </div>
            </div>
            <div class="bg-white p-5 rounded-[20px] border border-slate-100 flex items-center gap-4">
                <div class="stat-icon bg-amber-50 text-amber-600"><i class="bi bi-bookmark-heart"></i></div>
                <div>
                    <div class="text-sm font-bold text-slate-400 leading-tight">TERSIMPAN</div>
                    <div class="text-2xl font-black text-slate-800" id="stat-saved">{{ count($savedEventIds) }}</div>
                </div>
            </div>
        </div>

        <!-- Sub Filter Buttons -->
        <div class="flex items-center gap-2 mb-8 p-1.5 bg-slate-100 rounded-2xl w-fit">
            <button class="sub-filter active" onclick="setSubFilter('all', this)">Semua</button>
            <button class="sub-filter" onclick="setSubFilter('ongoing', this)">Aktivitas</button>
            <button class="sub-filter" onclick="setSubFilter('history', this)">Histori</button>
            <button class="sub-filter" onclick="setSubFilter('saved', this)">Tersimpan</button>
        </div>

        <!-- Scrollable List -->
        <div id="activity-list" class="space-y-6 mb-20" style="max-height: 70vh; overflow-y: auto; padding-right: 4px;">
            @foreach($allActivities as $item)
                <div class="activity-item-card p-6" 
                     data-type="{{ $item['type'] }}" 
                     data-status="{{ $item['status'] }}"
                     data-is-saved="{{ $item['is_saved'] ? 'true' : 'false' }}">
                    <div class="flex flex-col md:flex-row gap-6">
                        <div class="w-full md:w-52 h-32 shrink-0 rounded-2xl overflow-hidden bg-slate-100">
                            <img src="{{ $item['image'] }}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 flex flex-col justify-center">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="status-badge {{ $item['status'] == 'history' ? 'completed' : ($item['status'] == 'ongoing' ? 'ongoing' : 'saved') }}">
                                    {{ strtoupper($item['status'] == 'history' ? 'HISTORI' : ($item['status'] == 'ongoing' ? 'AKTIVITAS' : 'TERSIMPAN')) }}
                                </span>
                                <span class="text-[0.65rem] font-bold text-slate-400 ml-auto">{{ $item['date'] }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2 leading-tight">{{ $item['title'] }}</h3>
                            <p class="text-sm font-medium text-slate-500 mb-4 line-clamp-1">{{ $item['description'] }}</p>
                            <div class="flex gap-2">
                                @if($item['certificate_route'])
                                    <a href="{{ $item['certificate_route'] }}" class="btn btn-sm btn-primary border-0 rounded-xl px-4 py-2 font-bold" style="background:#4f46e5;"><i class="bi bi-download me-2"></i> Sertifikat</a>
                                @endif
                                <a href="{{ $item['detail_route'] }}" class="btn btn-sm bg-slate-50 border-0 rounded-xl px-4 py-2 font-bold text-slate-600">Detail</a>
                                
                                <button onclick="toggleSave(this, '{{ $item['save_route'] }}')" class="btn btn-sm {{ $item['is_saved'] ? 'btn-danger' : 'btn-outline-secondary' }} border-0 rounded-xl px-4 py-2 font-bold" style="{{ $item['is_saved'] ? 'background:#fee2e2; color:#ef4444;' : 'background:#f1f5f9; color:#64748b;' }}">
                                    <i class="bi {{ $item['is_saved'] ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div id="empty-state" class="hidden py-16 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                <i class="bi bi-search text-4xl text-slate-300 mb-4"></i>
                <h4 class="text-xl font-bold text-slate-800">Tidak ada riwayat untuk kategori ini</h4>
                <p class="text-slate-500">Mulai ambil program belajar baru untuk melihat aktivitasmu.</p>
            </div>

            <div id="log-section" class="hidden space-y-4">
                @forelse($activitiesLogs as $log)
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 flex items-center gap-4">
                        <div class="stat-icon bg-slate-50 text-slate-600">
                            @if($log->action == 'Login')
                                <i class="bi bi-box-arrow-in-right"></i>
                            @elseif($log->action == 'Save Course' || $log->action == 'Unsave Course')
                                <i class="bi bi-bookmark"></i>
                            @else
                                <i class="bi bi-activity"></i>
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-800">{{ $log->action }}</div>
                            <div class="text-sm text-slate-500">{{ $log->description }}</div>
                        </div>
                        <div class="text-xs font-bold text-slate-400">
                            {{ $log->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                        <i class="bi bi-clock-history text-4xl text-slate-300 mb-4"></i>
                        <h4 class="text-xl font-bold text-slate-800">Belum ada log aktivitas</h4>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        let currentType = 'EVENT';
        let currentStatus = 'all';

        const statsData = {
            EVENT: {
                total: {{ $totalEventsCount }},
                ongoing: {{ $totalEventsCount - $certifiedEvents }},
                history: {{ $certifiedEvents }},
                saved: {{ count($savedEventIds) }}
            },
            COURSE: {
                total: {{ $totalCoursesCount }},
                ongoing: {{ $totalCoursesCount - $certifiedCourses }},
                history: {{ $certifiedCourses }},
                saved: {{ count($savedCourseIds) }}
            }
        };

        function setMainTab(type) {
            currentType = type;
            document.querySelectorAll('.main-tab').forEach(t => t.classList.remove('active'));
            document.getElementById('tab-' + type.toLowerCase()).classList.add('active');
            
            document.getElementById('stat-total').innerText = statsData[type].total;
            document.getElementById('stat-ongoing').innerText = statsData[type].ongoing;
            document.getElementById('stat-history').innerText = statsData[type].history;
            document.getElementById('stat-saved').innerText = statsData[type].saved;
            
            applyFilters();
        }

        function setSubFilter(status, el) {
            currentStatus = status;
            document.querySelectorAll('.sub-filter').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
            applyFilters();
        }

        function applyFilters() {
            const cards = document.querySelectorAll('.activity-item-card');
            const logSection = document.getElementById('log-section');
            const subFilterContainer = document.querySelector('.sub-filter').parentElement;
            let count = 0;
            
            if (currentType === 'LOG') {
                cards.forEach(c => c.classList.add('hidden-filter'));
                logSection.classList.remove('hidden');
                subFilterContainer.classList.add('hidden');
                count = 1; // dummy so empty-state doesn't show
            } else {
                logSection.classList.add('hidden');
                subFilterContainer.classList.remove('hidden');
                cards.forEach(card => {
                    const typeMatch = card.dataset.type === currentType;
                    const isSaved = card.dataset.isSaved === 'true';
                    const statusMatch = currentStatus === 'all' || 
                                       (currentStatus === 'saved' ? isSaved : card.dataset.status === currentStatus);
                    
                    if (typeMatch && statusMatch) {
                        card.classList.remove('hidden-filter');
                        count++;
                    } else {
                        card.classList.add('hidden-filter');
                    }
                });
            }
            
            const empty = document.getElementById('empty-state');
            if (count === 0) empty.classList.remove('hidden');
            else empty.classList.add('hidden');
        }

        function toggleSave(btn, url) {
            btn.style.opacity = '0.5';
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload(); // Refresh to update list logic easily
                }
            })
            .catch(err => console.error(err))
            .finally(() => btn.style.opacity = '1');
        }

        window.onload = () => applyFilters();
    </script>
    
    @include('partials.footer-after-login')
</body>
</html>
