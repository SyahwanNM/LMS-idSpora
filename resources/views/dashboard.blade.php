@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Learner</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Animasi hover pada kartu */
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        }

        /* Animasi hover pada tombol */
        .btn {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        /* Transisi smooth untuk elemen interaktif */
        a,
        button,
        input,
        textarea,
        select {
            transition: all 0.25s ease;
        }

        /* Animasi progress bar */
        .progress-bar {
            transition: width 0.6s ease;
        }

        /* Efek hover pada baris tabel */
        .table tbody tr {
            transition: background-color 0.25s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(81, 55, 108, 0.05);
        }

        /* Transisi smooth carousel */
        .carousel-item {
            transition: opacity 0.6s ease-in-out;
        }

        /* Zoom image saat card dihover */
        .card img {
            transition: transform 0.3s ease;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        /* Animasi badge */
        .badge {
            transition: all 0.25s ease;
        }

        /* Smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Animasi icon button */
        .btn-circle,
        .btn-light {
            transition: all 0.25s ease;
        }

        .btn-circle:hover,
        .btn-light:hover {
            transform: scale(1.1);
        }

        /* Transisi card body */
        .card-body {
            transition: all 0.3s ease;
        }

        /* Animasi statistik card */
        .card[style*="background-color"] {
            transition: all 0.3s ease;
        }

        /* Efek glow pada tombol daftar */
        .bg-warning.text-dark {
            transition: all 0.3s ease;
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
        }

        .bg-warning.text-dark:hover {
            background-color: #ffb300 !important;
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.6), 0 4px 12px rgba(255, 193, 7, 0.4);
            transform: translateY(-2px);
        }

        .bg-warning.text-dark:active {
            box-shadow: 0 0 10px rgba(255, 193, 7, 0.4), 0 2px 6px rgba(255, 193, 7, 0.3);
            transform: translateY(0);
        }
        .hero-carousel {
            margin-top: 115px;
        }
    </style>
</head>

<body style="background-color: var(--bg-main);">

    <main class="container-xl">
        {{-- <div class="container pb-5"> --}}

            {{-- /* Banner Promo */ --}}
            <div id="carouselCaptions" class="carousel slide rounded-4 overflow-hidden mb-4 hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @forelse($dashboardCarousels as $index => $carousel)
                        <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="{{ $index }}" 
                            class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                            aria-label="Slide {{ $index + 1 }}"></button>
                    @empty
                        <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                    @endforelse
                </div>

                <div class="carousel-inner">
                    @forelse($dashboardCarousels as $index => $carousel)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" style="height: clamp(250px, 40vh, 420px); position: relative;">
                            @php
                                $btnUrl = $carousel->link_url ?? '#';
                                $isExternal = Str::startsWith($btnUrl, ['http://', 'https://']);
                            @endphp
                            
                            @if($carousel->link_url)
                                <a href="{{ $btnUrl }}" {{ $isExternal ? 'target="_blank"' : '' }}>
                            @endif
                            
                            <img src="{{ $carousel->image_url }}"
                                alt="{{ $carousel->title ?? 'Slide ' . ($index + 1) }}"
                                style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; filter:brightness(0.6);"
                                onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop'">

                            @if($carousel->title)
                            <div class="carousel-caption text-start" style="bottom: 40px; left: 60px;">
                                <h2 class="fw-bold">{{ $carousel->title }}</h2>
                                @if($carousel->link_url)
                                    <button class="btn btn-warning fw-bold mt-2">Lihat Detail</button>
                                @endif
                            </div>
                            @endif

                            @if($carousel->link_url)
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="carousel-item active" style="height: clamp(250px, 40vh, 420px); position: relative;">
                            <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop"
                                alt="Slide 1"
                                style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; filter:brightness(0.6);">

                            <div class="carousel-caption text-start" style="bottom: 40px; left: 60px;">
                                <h2 class="fw-bold">Upgrade Skill Digitalmu</h2>
                                <p>Belajar langsung dari praktisi industri dengan kurikulum relevan.</p>
                                <button class="btn btn-warning fw-bold">Mulai Sekarang</button>
                            </div>
                        </div>
                    @endforelse
                </div>

                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselCaptions"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Previous</span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#carouselCaptions"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>


            <div class="row g-4">
                <div class="col-lg-8">

                    {{-- /* Section Lanjutkan Belajar */ --}}
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0" style="color: var(--navy);">Lanjutkan Belajar</h5>
                            <a href="#" class="text-decoration-none fw-semibold"
                                style="color: var(--primary); font-size: 14px;">Lihat Semua &raquo;</a>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr style="font-size: 13px; color: #666;">
                                            <th class="border-0 ps-4 py-3" style="width: 50%;">Course Name</th>
                                            <th class="border-0 py-3" style="width: 35%;">Progress</th>
                                            <th class="border-0 py-3 text-center pe-4" style="width: 15%;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($userEnrollments as $enrollment)
                                            @php
                                                $progress = $enrollment->getProgressPercentage();
                                                $course = $enrollment->course;
                                            @endphp
                                            <tr>
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="rounded-3 overflow-hidden flex-shrink-0"
                                                            style="width: 48px; height: 48px;">
                                                            <img src="{{ $course->card_thumbnail ? (str_starts_with($course->card_thumbnail, 'http') ? $course->card_thumbnail : asset('storage/' . $course->card_thumbnail)) : asset('aset/poster.png') }}"
                                                                class="w-100 h-100 object-fit-cover" alt="Thumb">
                                                        </div>
                                                        <h6 class="fw-semibold mb-0"
                                                            style="font-size: 14px; color: var(--navy);">{{ $course->name }}
                                                        </h6>
                                                    </div>
                                                </td>
                                                <td class="py-3">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <div class="progress flex-grow-1"
                                                            style="height: 8px; background-color: #f1f5f9;">
                                                            <div class="progress-bar"
                                                                style="width: {{ $progress }}%; background-color: var(--secondary);">
                                                            </div>
                                                        </div>
                                                        <small class="fw-bold text-muted"
                                                            style="font-size: 12px; min-width: 35px; text-align: right;">{{ $progress }}%</small>
                                                    </div>
                                                </td>
                                                <td class="text-center pe-4 py-3">
                                                    <a href="{{ route('user.modules.index', $course->id) }}"
                                                        class="btn btn-sm text-white rounded-circle d-inline-flex align-items-center justify-content-center"
                                                        style="width: 36px; height: 36px; background-color: var(--navy);">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                            fill="currentColor" viewBox="0 0 16 16">
                                                            <path
                                                                d="m11.596 8.697-6.363 3.692c-.54.313-1.233-.066-1.233-.697V4.308c0-.63.692-1.01 1.233-.696l6.363 3.692a.802.802 0 0 1 0 1.393z" />
                                                        </svg>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-5">
                                                    <div class="mb-3 text-muted opacity-50">
                                                        <i class="bi bi-journal-x" style="font-size: 40px;"></i>
                                                    </div>
                                                    <h6 class="fw-bold mb-1" style="font-size: 14px; color: #2e2050;">Belum Ada Kursus</h6>
                                                    <p class="text-muted mb-3" style="font-size: 11px;">Ayo mulai tingkatkan keahlianmu hari ini!</p>
                                                    <a href="{{ route('courses.index') }}" class="btn btn-warning btn-sm fw-bold px-3">Cari Kursus</a>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- /* Section Rekomendasi Course */ --}}
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0" style="color: var(--navy);">Rekomendasi Course</h5>
                            <a href="/courses" class="btn btn-sm btn-outline-warning rounded-pill px-3"
                                style="color: var(--primary); border-color: var(--secondary);">Lihat Lainnya</a>
                        </div>

                        <div class="d-flex overflow-auto pb-3 gap-3" style="white-space: nowrap;">
                            @forelse($featuredCourses as $course)
                                @php
                                    $rating = $course->reviews_avg_rating ?? 5.0;
                                    $students = $course->enrollments_count ?? 0;
                                @endphp
                                <div class="flex-shrink-0" style="width: 280px;">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden"
                                        style="background: white;">
                                        <div class="position-relative" style="height: 160px;">
                                            <img src="{{ $course->card_thumbnail ? asset('storage/' . $course->card_thumbnail) : 'https://via.placeholder.com/280x160' }}"
                                                class="w-100 h-100 object-fit-cover" alt="{{ $course->name }}">
                                            <span
                                                class="badge position-absolute top-0 start-0 m-2 bg-white text-dark shadow-sm fw-semibold"
                                                style="font-size: 11px;">{{ ucfirst($course->level ?? 'General') }}</span>
                                            <button
                                                class="btn btn-light btn-sm rounded-circle shadow-sm position-absolute top-0 end-0 m-2 d-flex align-items-center justify-content-center"
                                                style="width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-bookmark"></i>
                                            </button>
                                        </div>

                                        <div class="card-body p-3 d-flex flex-column">
                                            <h6 class="fw-bold mb-3 text-wrap"
                                                style="line-height: 1.4; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                {{ $course->name }}</h6>

                                            <div class="d-flex align-items-center justify-content-between mb-3 text-muted"
                                                style="font-size: 11px;">
                                                <div class="d-flex align-items-center gap-1 text-truncate"
                                                    style="max-width: 120px;">
                                                    <i class="bi bi-grid"></i>
                                                    <span class="text-truncate">{{ $course->category->name ?? 'Category' }}</span>
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                        <span>{{ number_format($rating, 1) }}</span>
                                                    </div>
                                                    <div class="d-flex align-items-center gap-1">
                                                        <i class="bi bi-people-fill"></i>
                                                        <span>{{ $students }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div
                                                class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                                <div class="fw-bold" style="color: var(--primary); font-size: 16px;">
                                                    {{ $course->price > 0 ? 'Rp ' . number_format($course->price, 0, ',', '.') : 'Gratis' }}
                                                </div>
                                                <a href="{{ route('courses.show', $course->id) }}"
                                                    class="btn btn-warning btn-sm px-3 fw-bold border-0">{{ Route::currentRouteName() == 'admin.dashboard' ? 'Detail' : 'Mulai' }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 w-100">
                                    <p class="text-muted">Tidak ada rekomendasi course saat ini.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- /* Section Event Terbaru */ --}}
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0" style="color: #2e2050;">Event Terbaru</h5>
                            <a href="/events" class="btn btn-sm btn-outline-warning rounded-pill px-3"
                                style="color: var(--primary); border-color: var(--secondary);">Lihat Lainnya</a>
                        </div>

                        <div class="d-flex overflow-auto pb-3 gap-3" style="white-space: nowrap;">
                            @forelse($upcomingEvents as $event)

                            <div class="flex-shrink-0" style="width: 320px;">
                                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden"
                                    style="background:white;">

                                    <div class="position-relative overflow-hidden" style="height: 180px;">
                                        <img src="{{ $event->image_url ?? asset('aset/poster.png') }}"
                                            class="w-100 h-100 object-fit-cover" alt="{{ $event->title }}"
                                            onerror="this.onerror=null;this.src='{{ asset('aset/poster.png') }}';">

                                        @if($event->original_price && $event->price < $event->original_price)
                                            <span
                                                style="position:absolute; bottom:12px; left:12px; background:#212f4d; color:#d6bc3a; font-size:11px; font-weight:700; padding:6px 10px; border-radius:6px; box-shadow:0 2px 6px rgba(0,0,0,.25); text-transform:uppercase;">
                                                {{ round((($event->original_price - $event->price) /
                                                $event->original_price) * 100) }}% OFF
                                            </span>
                                            @elseif($event->price == 0)
                                            @endif

                                            <span
                                                style="position:absolute; top:12px; left:12px; background:{{ ($event->manage_action == 'create') ? '#6F42C1' : '#0D6EFD' }}; color:#fff; font-size:11px; font-weight:700; padding:5px 10px; border-radius:6px; text-transform:uppercase;">
                                                {{ $event->manage_action ?? 'EVENT' }}
                                            </span>
                                    </div>

                                    <div class="card-body pt-3 d-flex flex-column">
                                        <h6 class="fw-bold mb-2 text-wrap"
                                            style="line-height: 1.4; white-space: normal;">{{ $event->title }}</h6>

                                        <div class="mb-3 d-flex gap-2">
                                            <span class="badge"
                                                style="background-color:#f3f4f6; color:#4b5563; font-weight: 500;">{{
                                                $event->materi }}</span>
                                            <span class="badge"
                                                style="background-color:#f3f4f6; color:#4b5563; font-weight: 500;">{{
                                                $event->jenis }}</span>
                                        </div>

                                        <div class="d-flex flex-column gap-2 mb-3 text-muted" style="font-size:13px;">
                                            <div class="d-flex align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                                    <path
                                                        d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                                </svg>
                                                <span>{{ $event->event_date ?
                                                    \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y')
                                                    : 'TBA' }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    fill="currentColor" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                                                </svg>
                                                <span class="text-truncate" style="max-width: 200px;">{{
                                                    $event->location ?? 'Online' }} • {{ $event->event_time ?
                                                    \Carbon\Carbon::parse($event->event_time)->format('H:i') . ' WIB' :
                                                    '' }}</span>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            @php
                                            $quota = $event->quota ?? 100;
                                            $registered = $event->registrations_count ?? 0;
                                            $percentage = $quota > 0 ? min(100, round(($registered / $quota) * 100)) :
                                            100;
                                            $isFull = $registered >= $quota;
                                            @endphp
                                            <div class="d-flex justify-content-between align-items-center mb-1"
                                                style="font-size: 11px;">
                                                <div class="d-flex align-items-center gap-1 text-muted">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                        fill="currentColor" class="bi bi-people-fill"
                                                        viewBox="0 0 16 16">
                                                        <path
                                                            d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                                    </svg>
                                                    <span>{{ $isFull ? 'Kuota Penuh' : 'Kuota Terisi' }}</span>
                                                </div>
                                                <span
                                                    class="fw-bold {{ $isFull ? 'text-danger' : ($percentage > 80 ? 'text-warning' : 'text-primary') }}">
                                                    {{ $registered }}/{{ $quota }}
                                                </span>
                                            </div>
                                            <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                                <div class="progress-bar {{ $isFull ? 'bg-danger' : ($percentage > 80 ? 'bg-warning' : 'bg-primary') }}"
                                                    role="progressbar" style="width: {{ $percentage }}%"
                                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                    aria-valuemax="100"></div>
                                            </div>
                                        </div>

                                        <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded"
                                            style="background:#f8f9fa;">
                                            <span class="small fw-bold text-muted">Mulai dalam:</span>
                                            <span class="font-monospace px-2 py-1 rounded"
                                                style="background:#212f4d; color:#ffd54f; letter-spacing:1px; font-size:11px;">
                                                {{ $event->event_date ?
                                                \Carbon\Carbon::parse($event->event_date)->diffForHumans(null, true,
                                                true) : '-' }}
                                            </span>
                                        </div>

                                        <div
                                            class="d-flex justify-content-between align-items-end mt-auto pt-3 border-top">
                                            <div class="d-flex flex-column">
                                                @if($event->price == 0)
                                                <span
                                                    style="color: #198754; font-weight:700; font-size:16px;">Gratis</span>
                                                @else
                                                @if($event->original_price && $event->original_price > $event->price)
                                                <span
                                                    style="color:#9ca3af; text-decoration: line-through; font-size:11px;">Rp
                                                    {{ number_format($event->original_price, 0, ',', '.') }}</span>
                                                @endif
                                                <span style="color: var(--navy); font-weight:700; font-size:16px;">Rp
                                                    {{ number_format($event->price, 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('events.show', $event->id) }}"
                                                class="btn btn-primary btn-sm px-3 bg-warning text-dark border-0 fw-semibold {{ $isFull ? 'disabled' : '' }}">
                                                {{ $isFull ? 'Penuh' : 'Detail' }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center w-100 py-5">
                                <p class="text-muted">Belum ada event terbaru saat ini.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">

                    {{-- /* Sidebar - Kalender Events */ --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0" style="color: #2e2050;">Events</h5>
                            <button class="btn btn-sm p-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#999"
                                    class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                                    <path
                                        d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                                </svg>
                            </button>
                        </div>

                        <div class="d-flex justify-content-between text-center mb-4">
                            @php
                                $today = \Carbon\Carbon::now();
                                $daysToShow = 5;
                                // Prepare unique dates that have events for the user
                                $eventDates = $userEvents->pluck('event_date')->map(function($date) {
                                    return $date ? (\Carbon\Carbon::parse($date)->format('Y-m-d')) : null;
                                })->filter()->unique()->toArray();
                            @endphp
                            @for($i = 0; $i < $daysToShow; $i++)
                                @php
                                    $date = $today->copy()->addDays($i);
                                    $isToday = $i === 0;
                                    $hasEvent = in_array($date->format('Y-m-d'), $eventDates);
                                @endphp
                                <div class="p-2 rounded-3 d-flex flex-column align-items-center justify-content-center {{ $isToday ? 'text-white' : 'text-muted' }}" 
                                     style="font-size: 13px; min-width: 45px; {{ $isToday ? 'background-color: var(--primary);' : '' }}">
                                    <span>{{ $date->format('D') }}</span>
                                    <span class="fw-bold fs-6">{{ $date->format('d') }}</span>
                                    @if($hasEvent)
                                        <div class="mt-1 rounded-circle" 
                                             style="width: 4px; height: 4px; background-color: {{ $isToday ? '#fff' : 'var(--primary)' }};">
                                        </div>
                                    @endif
                                </div>
                            @endfor
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @php
                                $eventStyles = [
                                    'WEBINAR' => ['icon' => 'bi-laptop', 'bg' => '#eef2ff', 'color' => '#6366f1'],
                                    'SEMINAR' => ['icon' => 'bi-mic', 'bg' => '#fff7ed', 'color' => '#f97316'],
                                    'ONSITE' => ['icon' => 'bi-geo-alt', 'bg' => '#ecfccb', 'color' => '#65a30d'],
                                ];
                                $defaultStyle = ['icon' => 'bi-calendar-event', 'bg' => '#f3f4f6', 'color' => '#6b7280'];
                            @endphp

                            @forelse($userEvents as $userEv)
                                @php
                                    $style = $eventStyles[strtoupper($userEv->jenis)] ?? $defaultStyle;
                                    $evDate = $userEv->event_date ? $userEv->event_date->format('d M Y') : 'TBA';
                                    $evTime = $userEv->event_time ? (\Carbon\Carbon::parse($userEv->event_time)->format('H:i')) : 'TBA';
                                @endphp
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 45px; height: 45px; background-color: {{ $style['bg'] }}; color: {{ $style['color'] }};">
                                        <i class="bi {{ $style['icon'] }}" style="font-size: 20px;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0">
                                        <h6 class="fw-bold mb-0 text-truncate" style="font-size: 14px;">{{ $userEv->title }}</h6>
                                        <small class="text-muted d-block text-truncate" style="font-size: 11px;">
                                            {{ $evDate }} • {{ $evTime }} WIB
                                        </small>
                                    </div>
                                    <a href="{{ route('events.show', $userEv->id) }}" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-chevron-right" style="font-size: 12px;"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <div class="mb-3 text-muted opacity-50">
                                        <i class="bi bi-calendar-x" style="font-size: 40px;"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1" style="font-size: 14px; color: #2e2050;">Belum Ada Event</h6>
                                    <p class="text-muted mb-3" style="font-size: 11px;">Ayo mulai eksplorasi event menarik!</p>
                                    <a href="{{ route('events.index') }}" class="btn btn-warning btn-sm fw-bold px-3">Cari Event</a>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    

                    {{-- /* Sidebar - Chart Waktu Belajar */ --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0" style="color: var(--navy);">Waktu Belajar</h5>
                            <select class="form-select form-select-sm border-0 bg-light rounded-pill"
                                style="width: auto; font-size: 12px; font-weight: 500;">
                                <option>Minggu Ini</option>
                                <option>Bulan Ini</option>
                            </select>
                        </div>

                        <div style="height: 200px; width: 100%;">
                            <canvas id="learningChart"></canvas>
                        </div>
                    </div>

                    {{-- /* Sidebar - Topik Populer */ --}}
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0" style="color: var(--navy);">Topik Populer</h5>
                            <button class="btn btn-sm p-0 text-muted">
                                <i class="bi bi-three-dots" style="font-size: 20px;"></i>
                            </button>
                        </div>

                        <div class="d-flex flex-column gap-3">
                            @php
                                $topicStyles = [
                                    ['bg' => '#fce7f3', 'color' => '#db2777', 'icon' => 'bi-palette-fill'],
                                    ['bg' => '#e0f2fe', 'color' => '#0284c7', 'icon' => 'bi-code-slash'],
                                    ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'bi-graph-up-arrow'],
                                    ['bg' => '#fef3c7', 'color' => '#d97706', 'icon' => 'bi-megaphone-fill'],
                                ];
                            @endphp

                            @forelse($popularTopics as $index => $topic)
                                @php
                                    $style = $topicStyles[$index % count($topicStyles)];
                                @endphp
                                <a href="{{ route('courses.index', ['category' => $topic->name]) }}"
                                    class="d-flex align-items-center gap-3 text-decoration-none group-item p-2 rounded-3 hover-bg-light"
                                    style="transition: 0.2s;">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 48px; height: 48px; background-color: {{ $style['bg'] }}; color: {{ $style['color'] }};">
                                        <i class="bi {{ $style['icon'] }}" style="font-size: 20px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 14px;">{{ $topic->name }}</h6>
                                        <small class="text-muted" style="font-size: 12px;">{{ $topic->courses_count }} Kursus • {{ $topic->enrollments_count }} Siswa</small>
                                    </div>
                                    <div class="text-muted">
                                        <i class="bi bi-chevron-right" style="font-size: 14px;"></i>
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-3">
                                    <small class="text-muted">Belum ada data topik.</small>
                                </div>
                            @endforelse
                        </div>
                    </div>

                        </div>
                    </div>


                </div>
            </div>
        {{-- </div> --}}
    </main>

    @include('partials.footer-after-login')

    {{-- /* Script - Library Chart.js untuk Visualisasi Data */ --}}
    {{--
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('learningChart').getContext('2d');

            // Data Riil (Jam Belajar)
            const dataValues = @json($learningChartData);

            // Logika Warna (High value berwarna cerah, low berwarna gelap/abu)
            const backgroundColors = dataValues.map((value) => {
                if (value > 2) return '#f4c430'; // Secondary/Yellow (High intensity)
                if (value > 0) return '#51376c'; // Primary/Purple (Active)
                return '#e2e8f0'; // Grey (Inactive)
            });

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        label: 'Jam Belajar',
                        data: dataValues,
                        backgroundColor: backgroundColors,
                        borderRadius: 8, // Membuat sudut bar membulat
                        borderSkipped: false,
                        barThickness: 25, // Lebar batang
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }, // Sembunyikan legenda
                        tooltip: {
                            backgroundColor: '#2e2050',
                            titleFont: { family: 'Poppins' },
                            bodyFont: { family: 'Poppins' },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return context.raw + ' Jam';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                borderDash: [5, 5], // Garis putus-putus
                                drawBorder: false,
                                color: '#f1f5f9'
                            },
                            ticks: {
                                font: { family: 'Poppins', size: 10 },
                                color: '#94a3b8'
                            }
                        },
                        x: {
                            grid: { display: false }, // Hilangkan garis vertikal
                            ticks: {
                                font: { family: 'Poppins', size: 11 },
                                color: '#64748b'
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });
        });
    </script>
</body>

</html>