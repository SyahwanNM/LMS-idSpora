<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Event - IdSpora</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* --- LAYOUT FIXES (Sticky Footer & Full Width) --- */
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column; /* Susun elemen vertikal */
            min-height: 100vh;      /* Minimal setinggi layar */
            overflow-x: hidden;
            font-family: 'Poppins', sans-serif;
        }

        /* Konten utama akan mengambil sisa ruang agar footer terdorong ke bawah */
        main {
            flex: 1 0 auto;
            width: 100%;
        }

        /* Footer Full Width */
        .footer-section-wrapper {
            flex-shrink: 0;
            width: 100%;
            margin-top: auto;
        }

        /* --- EXISTING STYLES --- */
        .hero-carousel {
            margin-top: 115px; /* Jarak navbar */
        }

        /* Event Grid Responsive */
        
        .event .event-list {
            display: grid;
            row-gap: 40px;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 20px;
            align-items: stretch;
        }

     

        /* Tags & Meta */
        .tags { display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; font-size: 0.85rem; }
        .event .card-event .tags .tag { background-color: #E4E4E6 !important; color: #3B3B43; padding: 4px 10px; border-radius: 6px; font-weight: 500; font-size: 0.75rem; }
        .meta { display: flex; align-items: center; color: #6c757d; font-size: 0.85rem; }

        /* Description & Info */
        .desc-event { font-size: 0.9rem; color: #555; margin-bottom: 15px; line-height: 1.5; height: 42px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        /* Push price+button to bottom of card */
        .event .card-event .card-body .keterangan { margin-top: auto; }
        .keterangan-row { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; font-size: 0.9rem; color: #555; }
        .keterangan-item { display: flex; align-items: center; gap: 8px; }

        /* Countdown */
        .countdown-wrapper { margin-top: 10px; display: flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; margin-bottom: 15px; }
        .countdown-label { color: #555; font-weight: 500; }
        .countdown-timer { background: #212f4d; color: #ffd54f; padding: 2px 8px; border-radius: 4px; font-family: monospace; letter-spacing: 1px; min-width: 140px; text-align: center; }
        .countdown-timer.started { background: #198754; color: #fff; }
        .countdown-timer.expired { background: #6c757d; color: #fff; }

        /* Search Input */
        .search-input-2 { color: #212529 !important; }
        .search-input-2::placeholder { color: #6c757d !important; opacity: 1; }

        /* Price & Button */
        .price-row { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 15px; border-top: 1px solid #f0f0f0; }
        .price-col { display: flex; flex-direction: column; }
        .price-now:not(.price-free) { color: #ffd54f; font-weight: 700; font-size: 1.1rem; color: #d6bc3a; /* Gold override */ }
        .price-old { color: #6c757d; text-decoration: line-through; font-size: 0.85rem; }
        .price-free { color: #15803d; font-weight: 600; letter-spacing: .5px; background: #dcfce7; padding: 4px 10px; border-radius: 30px; font-size: .78rem; display: inline-block; line-height: 1.05; box-shadow: 0 0 0 1px #bbf7d0 inset; }

        /* Search Suggestions */
        .search-container .search-wrap { position: relative; }
        .search-suggest {
            position: absolute; top: 100%; left: 0; right: 0;
            background: #fff; border: 1px solid rgba(0, 0, 0, .125); border-top: none;
            z-index: 1000; list-style: none; margin: 0; padding: 4px 0;
            max-height: 260px; overflow-y: auto; display: none; border-radius: 0 0 12px 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .search-suggest.show { display: block; }
        .search-suggest li { padding: 8px 16px; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .search-suggest li:hover, .search-suggest li.active { background: #f8f9fa; }
        .search-suggest .muted { color: #6c757d; font-size: 12px; }
        .search-suggest .clear-all { color: #dc3545; font-weight: 500; }

        /* Dropdowns */
        .header-card .dropdown-menu { z-index: 1100; }
        .header-card .dropdown-box, .header-card .dropdown { overflow: visible; }

        .header-card .dropdown-menu .dropdown-header {
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding-top: .4rem;
            padding-bottom: .25rem;
        }
        .header-card .dropdown-menu .dropdown-divider { margin: .35rem 0; }
        .header-card .dropdown-menu .dropdown-item.active { font-weight: 700; }
    </style>
    <style>
        .carousel-control-prev,
        .carousel-control-next {
            display: none !important;
        }
        .carousel-indicators [data-bs-target] {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #f4c430;
            opacity: 0.5;
            transition: opacity 0.2s;
            border: none;
            margin: 0 4px;
        }
        .carousel-indicators .active {
            opacity: 1;
            background-color: #51376c;
        }
    </style>
</head>

<body>
    
    @include('partials.navbar-after-login')

    <main class="container-xl pb-5">
        
        <div id="carouselCaptions" class="carousel slide rounded-4 overflow-hidden mb-4 hero-carousel" data-bs-ride="carousel">
            <div class="carousel-indicators">
                @forelse($eventCarousels as $index => $carousel)
                    <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="{{ $index }}"
                        class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}"></button>
                @empty
                    <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                @endforelse
            </div>

            <div class="carousel-inner">
                @forelse($eventCarousels as $index => $carousel)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" style="height: clamp(250px, 40vh, 420px); position: relative;">
                        @php
                            $btnUrl = $carousel->link_url ?? '#';
                            $isExternal = Str::startsWith($btnUrl, ['http://', 'https://']);
                        @endphp

                        @if($carousel->link_url)
                            <a href="{{ $btnUrl }}" {{ $isExternal ? 'target="_blank"' : '' }}>
                        @endif

                        <img src="{{ $carousel->image_url }}" alt="{{ $carousel->title ?? 'Slide ' . ($index + 1) }}"
                            style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; "
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
                        <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop" alt="Slide 1"
                            style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; ">
                        <div class="carousel-caption text-start" style="bottom: 40px; left: 60px;">
                            <h2 class="fw-bold">Upgrade Skill Digitalmu</h2>
                            <p>Belajar langsung dari praktisi industri dengan kurikulum relevan.</p>
                            <button class="btn btn-warning fw-bold">Mulai Sekarang</button>
                        </div>
                    </div>
                @endforelse
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span><span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span><span class="visually-hidden">Next</span>
            </button>
        </div>

        <div class="row justify-content-center mb-5" style="margin-top: -30px; position: relative; z-index: 10;">
            <div class="col-lg-8">
                <form id="eventFilters" action="{{ route('events.index') }}" method="GET" class="search-form d-flex bg-white rounded-pill p-2 shadow-sm border">
                    <input type="hidden" id="filter-day" name="day" value="{{ request('day') }}">
                    <input type="hidden" id="filter-type" name="event_type" value="{{ request('event_type') }}">
                    <input type="hidden" id="filter-category" name="category" value="{{ request('category') }}">
                    <input type="hidden" id="filter-status" name="status" value="{{ request('status') }}">
                    <input type="hidden" id="filter-free" name="free" value="{{ request('free') }}">
                    <input type="hidden" id="filter-location" name="location" value="{{ request('location') }}">
                    <input type="hidden" id="filter-price" name="price" value="{{ request('price') }}">

                    <div class="search-container flex-grow-1">
                        <div class="search-wrap">
                            <input id="site-search" name="search" value="{{ request('search') }}" class="form-control border-0 rounded-pill ps-4 py-2 search-input-2" type="search" placeholder="Search for events by event name..." aria-label="Search" style="box-shadow: none;">
                            <ul id="search-suggest" class="search-suggest" role="listbox" aria-label="Search suggestions"></ul>
                        </div>
                    </div>
                    <button id="search-submit-trigger" class="btn rounded-pill px-4 fw-bold" type="button" style="background-color: #51376c; color: white;">
                        Search
                    </button>
                </form>
            </div>
        </div>

        <section class="event">
            <div class="header-card">
                <h3>Event List</h3>
                <div class="dropdown-box d-flex gap-2 flex-wrap">
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ request('day') === 'weekdays' ? 'Weekdays' : (request('day') === 'weekend' ? 'Weekend' : (request('day') === 'today' ? 'Today' : 'All day')) }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ !request('day') ? 'active' : '' }}" href="#" data-filter="day" data-value="">All day</a></li>
                            <li><a class="dropdown-item {{ request('day') === 'weekdays' ? 'active' : '' }}" href="#" data-filter="day" data-value="weekdays">Weekdays</a></li>
                            <li><a class="dropdown-item {{ request('day') === 'weekend' ? 'active' : '' }}" href="#" data-filter="day" data-value="weekend">Weekend</a></li>
                            <li><a class="dropdown-item {{ request('day') === 'today' ? 'active' : '' }}" href="#" data-filter="day" data-value="today">Today</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ request('event_type') === 'online' ? 'Online' : (request('event_type') === 'offline' ? 'Offline' : (request('event_type') === 'hybrid' ? 'Hybrid' : 'Event Type')) }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ !request('event_type') ? 'active' : '' }}" href="#" data-filter="event_type" data-value="">All Types</a></li>
                            <li><a class="dropdown-item {{ request('event_type') === 'online' ? 'active' : '' }}" href="#" data-filter="event_type" data-value="online">Online</a></li>
                            <li><a class="dropdown-item {{ request('event_type') === 'offline' ? 'active' : '' }}" href="#" data-filter="event_type" data-value="offline">Offline</a></li>
                            <li><a class="dropdown-item {{ request('event_type') === 'hybrid' ? 'active' : '' }}" href="#" data-filter="event_type" data-value="hybrid">Hybrid</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ request('category') ? ucwords(request('category')) : 'All Category' }}
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item {{ !request('category') ? 'active' : '' }}" href="#" data-filter="category" data-value="">All Category</a></li>
                            <li><a class="dropdown-item {{ request('category') === 'workshop' ? 'active' : '' }}" href="#" data-filter="category" data-value="workshop">Workshop</a></li>
                            <li><a class="dropdown-item {{ request('category') === 'seminar' ? 'active' : '' }}" href="#" data-filter="category" data-value="seminar">Seminar</a></li>
                            <li><a class="dropdown-item {{ request('category') === 'webinar' ? 'active' : '' }}" href="#" data-filter="category" data-value="webinar">Webinar</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        @php
                            $reqStatus = request('status');
                            $statusLabel = $reqStatus === 'upcoming' ? 'Upcoming' : ($reqStatus === 'ongoing' ? 'On Going' : ($reqStatus === 'finished' ? 'Finished' : 'All Status'));
                        @endphp
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="statusFilterBtn">
                            {{ $statusLabel }}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="statusFilterBtn">
                            <li><h6 class="dropdown-header">Tampilkan</h6></li>
                            <li>
                                <a class="dropdown-item {{ empty($reqStatus) ? 'active' : '' }}" href="#" data-status-filter data-value="all" aria-current="{{ empty($reqStatus) ? 'true' : 'false' }}">
                                    All Status
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">All Status</h6></li>
                            <li>
                                <a class="dropdown-item {{ $reqStatus === 'upcoming' ? 'active' : '' }}" href="#" data-status-filter data-value="upcoming" aria-current="{{ $reqStatus === 'upcoming' ? 'true' : 'false' }}">
                                    Upcoming
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $reqStatus === 'ongoing' ? 'active' : '' }}" href="#" data-status-filter data-value="ongoing" aria-current="{{ $reqStatus === 'ongoing' ? 'true' : 'false' }}">
                                    On Going
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ $reqStatus === 'finished' ? 'active' : '' }}" href="#" data-status-filter data-value="finished" aria-current="{{ $reqStatus === 'finished' ? 'true' : 'false' }}">
                                    Finished
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="event-list">
                @php
                    $loopEvents = isset($events) ? $events : ($upcomingEvents ?? collect());
                @endphp
                @forelse($loopEvents as $event)
                    @php
                        $startAt = null;
                        if($event->event_date){
                            $dateStr = $event->event_date->format('Y-m-d');
                            $timeStr = $event->event_time ? (method_exists($event->event_time,'format') ? $event->event_time->format('H:i:s') : $event->event_time) : '00:00:00';
                            try { $startAt = \Carbon\Carbon::parse($dateStr.' '.$timeStr, config('app.timezone')); } catch (Exception $e) { $startAt = null; }
                        }
                        
                        $endAt = null;
                        if($startAt){
                            $timeEndStr = null;
                            if(!empty($event->event_time_end)){
                                $timeEndStr = method_exists($event->event_time_end,'format') ? $event->event_time_end->format('H:i:s') : (is_string($event->event_time_end) ? $event->event_time_end : null);
                            }
                            if($timeEndStr){
                                try { $endAt = \Carbon\Carbon::parse($startAt->format('Y-m-d'). ' ' . $timeEndStr, config('app.timezone')); } catch (Exception $e) { $endAt = $startAt->copy()->endOfDay(); }
                            } else {
                                $endAt = $startAt->copy()->endOfDay();
                            }
                        }

                        $nowTs = \Carbon\Carbon::now();
                        $status = 'all';
                        if($startAt && $endAt){
                            if($nowTs->lt($startAt)) $status = 'upcoming';
                            elseif($nowTs->between($startAt, $endAt)) $status = 'ongoing';
                            elseif($nowTs->gt($endAt)) $status = 'finished';
                        }
                    @endphp

                    <div class="card-event" @if($startAt) data-event-start-ts="{{ $startAt->timestamp }}" @endif data-status="{{ $status }}" data-detail-url="{{ route('events.show',$event) }}" style="cursor:pointer;">
                        <div class="thumb-wrapper">
                            @php $action = $event->manage_action ?? null; @endphp
                            @if($action)
                                <span class="manage-badge {{ $action === 'manage' ? 'manage' : 'create' }}">{{ $action === 'manage' ? 'Manage' : 'Create' }}</span>
                            @endif
                            
                            <img class="card-image-event" src="{{ $event->image_url ?? asset('aset/poster.png') }}" alt="{{ $event->title }}" onerror="this.src='{{ asset('aset/poster.png') }}'">
                            
                            @php
                                $showDiscountBadge = method_exists($event,'hasDiscount') && $event->hasDiscount() && $event->price > 0 && $event->price > $event->discounted_price;
                                $percentOff = $showDiscountBadge ? round((($event->price - $event->discounted_price) / $event->price) * 100) : 0;
                            @endphp
                            @if($showDiscountBadge && $percentOff > 0)
                                <span class="discount-badge">{{ $percentOff }}% off</span>
                            @endif
                            <div class="badge-save-group" style="gap:12px;">
                                <button class="save-btn" aria-label="Save event" type="button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2v13.5l6-3 6 3V2z" /></svg>
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <h4 class="event-title">{{ $event->title }}</h4>
                            <div class="tags">
                                <span class="tag">{{ $event->jenis ? Str::limit($event->jenis,18) : 'Jenis' }}</span>
                                <span class="tag">{{ $event->materi ? Str::limit($event->materi,18) : 'Materi' }}</span>
                                <div class="meta" style="margin-left:auto; gap:6px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" /></svg>
                                    <span>{{ $event->registrations_count ?? ($event->registrations()->where('status','active')->distinct('user_id')->count('user_id')) }}</span>
                                </div>
                            </div>
                            <div class="desc-event rich-desc">{!! Str::limit(strip_tags($event->description,'<p><br><strong><em><ul><ol><li><b><i>'), 220) !!}</div>
                            
                            <div class="keterangan keterangan-row">
                                <div class="keterangan-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                                        <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                                    </svg>
                                    <span>
                                        @if($event->speaker)
                                            {{ $event->speaker }}
                                        @elseif($event->trainer)
                                            <a href="{{ route('public.trainer-profile.show', $event->trainer->id) }}" style="color: inherit; text-decoration: none; font-weight: 500;" onclick="event.stopPropagation();">
                                                {{ $event->trainer->full_name_with_title ?: $event->trainer->name }}
                                            </a>
                                        @else
                                            idSpora Team
                                        @endif
                                    </span>
                                </div>
                                <div class="keterangan-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-event" viewBox="0 0 16 16"><path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" /><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" /></svg>
                                    <span>{{ $event->event_date?->format('d F Y') ?? '-' }}</span>
                                </div>
                                <div class="keterangan-item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" /></svg>
                                <span>
                                        @if($event->location)
                                            {{ $event->location }}@if($event->event_time) • {{ $event->event_time?->format('H:i') }} WIB @endif
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>

                            @if($startAt)
                                <div class="countdown-wrapper" data-countdown-wrapper>
                                    <span class="countdown-label" data-countdown-label>Start In:</span>
                                    <span class="countdown-timer" data-countdown data-start-ts="{{ $startAt->timestamp }}" @if($endAt) data-end-ts="{{ $endAt->timestamp }}" @endif>--</span>
                                </div>
                            @endif

                            <div class="price-row">
                                <div class="price-col">
                                    @if(method_exists($event,'hasDiscount') && $event->hasDiscount())
                                        <span class="price-old">Rp{{ number_format($event->price,0,',','.') }}</span>
                                        <span class="price-now">Rp{{ number_format($event->discounted_price,0,',','.') }}</span>
                                    @else
                                        @php $isFree = (int)$event->price === 0; @endphp
                                        @if($isFree)
                                            <span class="price-now price-free">Free</span>
                                        @else
                                            <span class="price-now">{{ 'Rp'.number_format($event->price,0,',','.') }}</span>
                                        @endif
                                    @endif
                                </div>
                                @php 
                                    $registered = !empty($event->is_registered);
                                    $isFinished = ($status === 'finished');
                                    $btnLabel = $registered ? 'Registered' : ($isFinished ? 'Finished' : 'See Details');
                                    $btnClass = $registered ? 'btn-success' : ($isFinished ? 'btn-secondary' : 'btn-primary');
                                @endphp
                                <button class="btn-register register-btn btn {{ $btnClass }}" type="button" {{ ($registered || $isFinished) ? 'disabled' : '' }} onclick="event.stopPropagation();">
                                    {{ $btnLabel }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5" style="grid-column:1/-1;">
                        <h5 class="mb-2">There are no events yet</h5>
                        <p class="text-muted mb-0">Event will be available soon.</p>
                    </div>
                @endforelse
            </div>

            @if(isset($events) && $events instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="mt-4 d-flex justify-content-center">{!! $events->links() !!}</div>
            @endif

            <div class="modal fade" id="registrationSuccessModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white py-2">
                            <h6 class="modal-title">Registration Successful</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">You have successfully registered for the event: <strong id="registeredEventTitle"></strong></p>
                        </div>
                        <div class="modal-footer py-2">
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        
    </main> <div class="footer-section-wrapper">
        @include('partials.footer-after-login')
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // --- Search Logic ---
            (function(){
                const searchForm = document.querySelector('.search-form');
                const input = document.getElementById('site-search');
                const trigger = document.getElementById('search-submit-trigger');
                const suggest = document.getElementById('search-suggest');
                const LS_KEY = 'events_search_history_v1';
                const LIMIT = 10;

                function loadHistory(){ try { return JSON.parse(localStorage.getItem(LS_KEY) || '[]'); } catch { return []; } }
                function saveHistory(list){ try { localStorage.setItem(LS_KEY, JSON.stringify(list.slice(0, LIMIT))); } catch {} }
                function addToHistory(term){
                    const t = (term||'').trim(); if(!t) return;
                    let list = loadHistory();
                    const lower = t.toLowerCase();
                    list = list.filter(x => (x||'').toString().toLowerCase() !== lower);
                    list.unshift(t); saveHistory(list);
                }
                function clearHistory(){ saveHistory([]); renderSuggestions(); }
                function renderSuggestions(filter=''){
                    if(!suggest) return;
                    const all = loadHistory();
                    const f = (filter||'').trim().toLowerCase();
                    let items = all; if(f){ items = all.filter(x => x.toLowerCase().includes(f)); }
                    suggest.innerHTML = '';
                    if(items.length === 0){ suggest.classList.remove('show'); return; }
                    items.forEach((txt, idx)=>{
                        const li = document.createElement('li'); li.setAttribute('role','option'); li.setAttribute('tabindex','-1');
                        li.textContent = txt;
                        li.addEventListener('mousedown', function(e){ e.preventDefault(); });
                        li.addEventListener('click', function(){ input.value = txt; submitIfNotEmpty(); });
                        suggest.appendChild(li);
                    });
                    const divider = document.createElement('li'); divider.innerHTML = '<span class="muted">—</span>';
                    divider.style.cursor='default'; divider.addEventListener('mousedown', e=>e.preventDefault()); suggest.appendChild(divider);
                    const clear = document.createElement('li'); clear.innerHTML = '<span class="clear-all">Hapus riwayat pencarian</span>';
                    clear.addEventListener('mousedown', e=>e.preventDefault()); clear.addEventListener('click', clearHistory); suggest.appendChild(clear);
                    suggest.classList.add('show'); activeIndex = -1;
                }
                function hideSuggestions(){ suggest?.classList.remove('show'); }
                let activeIndex = -1;
                function moveActive(delta){
                    if(!suggest || !suggest.classList.contains('show')) return;
                    const items = Array.from(suggest.querySelectorAll('li')).filter(li=>!li.querySelector('.muted') && !li.querySelector('.clear-all'));
                    if(items.length === 0) return;
                    activeIndex = (activeIndex + delta + items.length) % items.length;
                    items.forEach(li=>li.classList.remove('active'));
                    const cur = items[activeIndex]; cur.classList.add('active'); cur.scrollIntoView({ block: 'nearest' });
                }
                function submitIfNotEmpty(){
                    if(!input || !searchForm) return;
                    const q = (input.value || '').trim();
                    if(q.length === 0){ input.value=''; hideSuggestions(); searchForm.submit(); return; }
                    input.value = q; addToHistory(q); hideSuggestions(); searchForm.submit();
                }
                trigger?.addEventListener('click', submitIfNotEmpty);
                trigger?.addEventListener('keydown', (e)=>{ if(e.key==='Enter' || e.key===' '){ e.preventDefault(); submitIfNotEmpty(); }});
                function getSelectableItems(){ if(!suggest) return []; return Array.from(suggest.querySelectorAll('li')).filter(li=>!li.querySelector('.muted') && !li.querySelector('.clear-all')); }
                input?.addEventListener('keydown', (e)=>{
                    if(e.key==='Enter'){ e.preventDefault(); const items = getSelectableItems(); if(suggest?.classList.contains('show') && items.length && activeIndex>=0){ const chosen = items[activeIndex]; if(chosen){ input.value = chosen.textContent || input.value; } } submitIfNotEmpty(); }
                    else if(e.key==='ArrowDown'){ e.preventDefault(); renderSuggestions(input.value); moveActive(1); }
                    else if(e.key==='ArrowUp'){ e.preventDefault(); renderSuggestions(input.value); moveActive(-1); }
                    else if(e.key==='Escape'){ hideSuggestions(); }
                });
                input?.addEventListener('input', function(){ renderSuggestions(this.value); });
                input?.addEventListener('focus', function(){ renderSuggestions(this.value); });
                document.addEventListener('click', function(e){ if(!suggest) return; if(e.target === input || suggest.contains(e.target)) return; hideSuggestions(); });
            })();

            // --- Auto-Submit Filters ---
            const form = document.getElementById('eventFilters');
            const locSel = document.getElementById('filter-location');
            const priceSel = document.getElementById('filter-price');
            const freeHidden = document.getElementById('filter-free');
            function submitFilters(){ form && form.submit(); }
            if(locSel){ locSel.addEventListener('change', submitFilters); }
            if(priceSel){
                priceSel.addEventListener('change', function(){
                    if(this.value === 'free'){ this.value = ''; if(freeHidden){ freeHidden.value = 1; } } 
                    else { if(freeHidden){ freeHidden.value = ''; } }
                    submitFilters();
                });
            }

            // --- Dropdown Filters ---
            document.querySelectorAll('.dropdown-menu [data-filter]').forEach(item => {
                item.addEventListener('click', function(e){
                    e.preventDefault();
                    const key = this.getAttribute('data-filter');
                    const val = this.getAttribute('data-value') || '';
                    const hiddenMap = {
                        day: document.getElementById('filter-day'),
                        event_type: document.getElementById('filter-type'),
                        category: document.getElementById('filter-category')
                    };
                    const hidden = hiddenMap[key]; if(hidden){ hidden.value = val; }
                    submitFilters();
                });
            });

            // --- Header Dropdown Toggler ---
            (function(){
                const containerSelector = '.header-card';
                const toggles = document.querySelectorAll(containerSelector + ' .dropdown-toggle');
                function closeAll(except){
                    document.querySelectorAll(containerSelector + ' .dropdown-menu.show').forEach(m=>{ if(m!==except) m.classList.remove('show'); });
                    document.querySelectorAll(containerSelector + ' .dropdown-toggle[aria-expanded]').forEach(b=>b.setAttribute('aria-expanded','false'));
                }
                toggles.forEach(btn => {
                    if(btn.hasAttribute('data-bs-toggle')) btn.removeAttribute('data-bs-toggle');
                    btn.addEventListener('click', function(e){
                        e.preventDefault(); e.stopPropagation();
                        const menu = this.nextElementSibling; if(!menu) return;
                        const isOpen = menu.classList.contains('show');
                        closeAll(menu); menu.classList.toggle('show', !isOpen); this.setAttribute('aria-expanded', String(!isOpen));
                    });
                });
                document.addEventListener('click', function(e){ if(!e.target.closest(containerSelector + ' .dropdown')){ closeAll(); } });
            })();

            // --- Click Card to Detail ---
            document.querySelectorAll('.card-event').forEach(card => {
                const url = card.getAttribute('data-detail-url'); if(!url) return;
                card.addEventListener('click', function(){ window.location = url; });
                const titleEl = card.querySelector('.event-title'); if(titleEl){ titleEl.addEventListener('click', function(e){ e.stopPropagation(); window.location = url; }); }
            });
            document.querySelectorAll('.register-btn').forEach(btn => {
                btn.addEventListener('click', function(){ if(this.disabled) return; const card = this.closest('.card-event'); const url = card ? card.getAttribute('data-detail-url') : null; if(url) window.location = url; });
            });

            // --- Status Filter UI ---
            (function(){
                const statusBtn = document.getElementById('statusFilterBtn');
                const statusInput = document.getElementById('filter-status');
                const links = document.querySelectorAll('[data-status-filter]');
                function labelOf(val){
                    if(val==='upcoming') return 'Upcoming'; if(val==='ongoing') return 'On Going'; if(val==='finished') return 'Finished'; return 'All Status';
                }
                const cur = (new URLSearchParams(window.location.search).get('status')) || '{{ request('status') }}' || '';
                if(statusBtn){ statusBtn.textContent = labelOf(cur || 'all'); }
                links.forEach(a => {
                    a.addEventListener('click', function(e){
                        e.preventDefault(); const val = this.getAttribute('data-value') || 'all';
                        if(statusBtn){ statusBtn.textContent = labelOf(val); }
                        if(statusInput){ statusInput.value = (val==='all') ? '' : val; }
                        submitFilters();
                    });
                });
            })();
        });

        // --- Countdown Script ---
        (function(){
            function pad(n){ return n < 10 ? '0'+n : String(n); }
            function formatDiff(totalSec){
                if(totalSec <= 0) return 'Dimulai';
                let sec = totalSec;
                const days = Math.floor(sec/86400); sec%=86400;
                const hours = Math.floor(sec/3600); sec%=3600;
                const minutes = Math.floor(sec/60); sec%=60;
                const seconds = sec;
                const parts = [];
                if(days > 0) parts.push(days + ' day');
                if(hours > 0 || days > 0) parts.push(hours + ' hour');
                if(days === 0 && hours === 0){ parts.push(pad(minutes) + ':' + pad(seconds)); } else { parts.push(minutes + ' minute'); }
                return parts.join(' ');
            }
            function update(){
                const now = Math.floor(Date.now()/1000);
                document.querySelectorAll('[data-countdown]').forEach(el => {
                    const start = parseInt(el.getAttribute('data-start-ts'),10); if(!start) return;
                    const endAttr = el.getAttribute('data-end-ts');
                    const end = endAttr ? parseInt(endAttr,10) : null;
                    const label = el.closest('[data-countdown-wrapper]')?.querySelector('[data-countdown-label]');
                    if(end && now > end){
                        el.textContent = 'Finished'; el.classList.remove('started'); el.classList.add('expired');
                        if(label) label.textContent = 'Status:';
                        // Update register button if not registered
                        const card = el.closest('.card-event');
                        if(card){
                            const btn = card.querySelector('.register-btn');
                            if(btn && !btn.classList.contains('btn-success')){
                                btn.textContent = 'Finished';
                                btn.classList.remove('btn-primary','btn-warning');
                                btn.classList.add('btn-secondary');
                                btn.disabled = true;
                            }
                        }
                        return;
                    }
                    if(now >= start){ el.textContent = 'Ongoing'; el.classList.remove('expired'); el.classList.add('started'); if(label) label.textContent = 'Status:'; return; }
                    const diff = start - now; el.textContent = formatDiff(diff); el.classList.remove('started','expired'); if(label) label.textContent = 'Begin at:';
                });
            }
            update(); setInterval(update, 1000);
        })();
    </script>
</body>
</html>