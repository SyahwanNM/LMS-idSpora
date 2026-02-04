<!DOCTYPE html>

<html lang="en">

<head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta name="csrf-token" content="{{ csrf_token() }}"> <title>Event</title> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"> @vite(['resources/css/app.css', 'resources/js/app.js']) <style> /* FIX FOOTER FULL WIDTH */ body { overflow-x: hidden; margin: 0; padding: 0; } .footer-section { width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; margin-top: 40px; }

    /* FIX SCROLL/TOP SPACING (Agar tidak tertutup navbar) */
    .hero-carousel {
        margin-top: 115px; /* Jarak dari atas ditingkatkan untuk navbar premium */
    }

    /* Event page image enlargement (slightly taller) */
    .event .event-list {row-gap:40px; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));}
    @media (max-width: 576px){
        .event .event-list {grid-template-columns: 1fr;}
    }
    .event .card-event .thumb-wrapper {position:relative; height:380px;} /* enlarged from presumed ~320/340 */
    .event .card-event .card-image-event {width:100%; height:100%; object-fit:cover;}
    @media (max-width:1200px){ .event .card-event .thumb-wrapper {height:360px;} }
    @media (max-width:992px){ .event .card-event .thumb-wrapper {height:340px;} }
    @media (max-width:768px){ .event .card-event .thumb-wrapper {height:280px;} }
    /* Add extra top spacing for event section */
    section.event {margin-top:55px;}
    section.event .header-card {margin-bottom:28px;}
    /* Lower the main heading relative to filter dropdowns */
    section.event .header-card h3 {margin-top:14px;}
    /* Ensure event titles in listing are visible (override global .event-title {color:white}) */
    .event .card-event .event-title { color:#212529; margin-left:0; font-weight:600; }
/* Discount badge styling (moved to bottom-left) */
.event .card-event .thumb-wrapper {overflow:hidden;}
.event .card-event .discount-badge {position:absolute; bottom:12px; left:12px; background:#212f4d; color:#d6bc3a; font-size:13px; font-weight:600; padding:6px 10px 5px; border-radius:6px; line-height:1; letter-spacing:.5px; box-shadow:0 2px 6px rgba(0,0,0,.25); display:inline-flex; align-items:center; gap:4px; text-transform:uppercase;}
/* Manage/Create banner (small ribbon) */
.event .card-event .manage-badge {position:absolute; top:12px; left:12px; color:#fff; font-size:12px; font-weight:600; padding:5px 10px; border-radius:6px; line-height:1; letter-spacing:.5px; box-shadow:0 2px 6px rgba(0,0,0,.25); text-transform:uppercase;}
.event .card-event .manage-badge.manage {background:#0d6efd;} /* Bootstrap primary */
.event .card-event .manage-badge.create {background:#6f42c1;} /* Bootstrap purple */
    /* Countdown styles */
    .countdown-wrapper {margin-top:10px; display:flex; align-items:center; gap:6px; font-size:13px; font-weight:500;}
    .countdown-label {color:#555; font-weight:500;}
    .countdown-timer {background:#212f4d; color:#ffd54f; padding:2px 8px; border-radius:4px; font-family:monospace; letter-spacing:1px; min-width:140px; text-align:center;}
    .countdown-timer.started {background:#198754; color:#fff;}
    .countdown-timer.expired {background:#6c757d; color:#fff;}
    /* Ensure search input text is visible (not white) */
    .search-input-2 { color:#212529 !important; }
    .search-input-2::placeholder { color:#6c757d !important; opacity:1; }
    .search-input-2:-ms-input-placeholder { color:#6c757d !important; }
    .search-input-2::-ms-input-placeholder { color:#6c757d !important; }
/* Price styles (yellow for paid, green pill for free) */
.price-now:not(.price-free) { color:#ffd54f; font-weight:700; }
.price-old { color:#6c757d; text-decoration: line-through; }
.price-free { color:#15803d; font-weight:600; letter-spacing:.5px; background:#dcfce7; padding:4px 10px; border-radius:30px; font-size:.78rem; display:inline-block; line-height:1.05; box-shadow:0 0 0 1px #bbf7d0 inset; }
    /* Search suggestions dropdown */
    .search-container .search-wrap { position: relative; }
    .search-suggest {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid rgba(0,0,0,.125);
        border-top: none;
        z-index: 1000;
        list-style: none;
        margin: 0;
        padding: 4px 0;
        max-height: 260px;
        overflow-y: auto;
        display: none;
    }
    .search-suggest.show { display: block; }
    .search-suggest li { padding: 8px 12px; cursor: pointer; display:flex; align-items:center; gap:8px; }
    .search-suggest li:hover, .search-suggest li.active { background: #f8f9fa; }
    .search-suggest .muted { color:#6c757d; font-size: 12px; }
    .search-suggest .clear-all { color:#dc3545; font-weight:500; }
    /* Tag badge background override (speaker/location) */
    .event .card-event .tags .tag { background-color:#E4E4E6 !important; color:#3B3B43; }
    /* Ensure filter dropdowns open above other overlays */
    .header-card .dropdown-menu { z-index: 1100; }
    /* Prevent clipping within header filters */
    .header-card .dropdown-box, .header-card .dropdown { overflow: visible; }
</style>
</head>
<body> @include('partials.navbar-after-login') <section class="hero-carousel"> <div id="carouselExampleInterval" class="carousel slide custom-carousel" data-bs-ride="carousel"> <div class="carousel-inner"> @if(isset($eventCarousels) && $eventCarousels->count() > 0) @foreach($eventCarousels as $i => $carousel) <div class="carousel-item {{ $i === 0 ? 'active' : '' }}" data-bs-interval="{{ $i === 0 ? 10000 : 2000 }}"> @if($carousel->link_url) <a href="{{ $carousel->link_url }}" target="_blank" style="display: block;"> <img src="{{ $carousel->image_url }}" class="d-block" alt="{{ $carousel->title ?? 'Carousel' }}" onerror="this.src='{{ asset('aset/poster.png') }}'"> </a> @else <img src="{{ $carousel->image_url }}" class="d-block" alt="{{ $carousel->title ?? 'Carousel' }}" onerror="this.src='{{ asset('aset/poster.png') }}'"> @endif </div> @endforeach @else <div class="carousel-item active" data-bs-interval="10000"> <img src="{{ asset('aset/poster.png') }}" class="d-block" alt="Carousel"> </div> <div class="carousel-item" data-bs-interval="2000"> <img src="{{ asset('aset/poster.png') }}" class="d-block" alt="Carousel"> </div> <div class="carousel-item"> <img src="{{ asset('aset/poster.png') }}" class="d-block" alt="Carousel"> </div> @endif </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>
<div class="filter-container">
    <form id="eventFilters" class="filter-box" action="{{ route('events.index') }}" method="get">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" id="filter-free" name="free" value="{{ request()->boolean('free') ? 1 : '' }}">
        <input type="hidden" id="filter-day" name="day" value="{{ request('day') }}">
        <input type="hidden" id="filter-type" name="event_type" value="{{ request('event_type') }}">
        <input type="hidden" id="filter-category" name="category" value="{{ request('category') }}">
        <input type="hidden" id="filter-status" name="status" value="{{ request('status') }}">
        <div class="options">
            <label>Place</label>
            <select name="location" id="filter-location" class="form-select">
                <option value="">All Places</option>
                @foreach(($locations ?? []) as $loc)
                    <option value="{{ $loc }}" {{ request('location') === $loc ? 'selected' : '' }}>{{ $loc }}</option>
                @endforeach
            </select>
        </div>
        <div class="options">
            <label>Price</label>
            <select name="price" id="filter-price" class="form-select">
                <option value="">Default (Newest)</option>
                <option value="asc" {{ request('price')==='asc' ? 'selected' : '' }}>Low to High</option>
                <option value="desc" {{ request('price')==='desc' ? 'selected' : '' }}>High to Low</option>
                <option value="free" {{ request()->boolean('free') ? 'selected' : '' }}>Free</option>
            </select>
        </div>
    </form>
    <div class="search-container">
        <form class="search-form" action="{{ route('events.index') }}" method="get" autocomplete="off">
            <div class="search-wrap">
                <input id="site-search" class="form-control search-input-2" type="search" name="search"
                    placeholder="Search" aria-label="Search" aria-expanded="false" aria-controls="search-suggest" value="{{ request('search') }}">
                <input type="hidden" name="location" value="{{ request('location') }}">
                <input type="hidden" name="price" value="{{ request('price') }}">
                <input type="hidden" name="free" value="{{ request()->boolean('free') ? 1 : '' }}">
                <input type="hidden" name="day" value="{{ request('day') }}">
                <input type="hidden" name="event_type" value="{{ request('event_type') }}">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <span class="search-icon" id="search-submit-trigger" ariza-hidden="false" tabindex="0" role="button" aria-label="Cari">
                    <svg id="search-icon-svg" xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" width="18" height="18"
                        fill="currentColor" viewBox="0 0 16 16" focusable="false" style="cursor:pointer;">
                        <path
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                    </svg>
                </span>

                    <ul id="search-suggest" class="search-suggest" role="listbox"></ul>
                </div>
            </form>
        </div>
    </div>
    @if(auth()->check() && auth()->user()->role === 'admin')
    <div class="container my-3">
        </div>
    </div>
    @endif
    <section class="event">
        <div class="header-card">
            <h3>Daftar Event</h3>
            <div class="dropdown-box d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ request('day') === 'weekdays' ? 'Weekdays' : (request('day') === 'weekend' ? 'Weekend' : (request('day') === 'today' ? 'Today' : 'Any Day')) }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-filter="day" data-value="">Any Day</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="day" data-value="weekdays">Weekdays</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="day" data-value="weekend">Weekend</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="day" data-value="today">Today</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ request('event_type') === 'online' ? 'Online' : (request('event_type') === 'onsite' ? 'Onsite' : (request('event_type') === 'hybrid' ? 'Hybrid' : 'Event Type')) }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-filter="event_type" data-value="">Any Type</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="event_type" data-value="online">Online</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="event_type" data-value="onsite">Onsite</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="event_type" data-value="hybrid">Hybrid</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ request('category') ? ucwords(request('category')) : 'Any Category' }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-filter="category" data-value="">Any Category</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="category" data-value="workshop">Workshop</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="category" data-value="training">Training</a></li>
                        <li><a class="dropdown-item" href="#" data-filter="category" data-value="webinar">Webinar</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    @php
                        $reqStatus = request('status');
                        $statusLabel = $reqStatus === 'upcoming' ? 'Mendatang' : ($reqStatus === 'ongoing' ? 'Sedang Berlangsung' : ($reqStatus === 'finished' ? 'Telah Selesai' : 'Semua Status'));
                    @endphp
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="statusFilterBtn">
                        {{ $statusLabel }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-status-filter data-value="all">Semua Status</a></li>
                        <li><a class="dropdown-item" href="#" data-status-filter data-value="upcoming">Mendatang</a></li>
                        <li><a class="dropdown-item" href="#" data-status-filter data-value="ongoing">Sedang Berlangsung</a></li>
                        <li><a class="dropdown-item" href="#" data-status-filter data-value="finished">Telah Selesai</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="event-list">
            @php
                // Pastikan variabel $events tersedia dari PublicEventController.
                // Jika halaman ini dipakai reusable dan hanya punya $upcomingEvents, fallback sederhana.
                $loopEvents = isset($events) ? $events : ($upcomingEvents ?? collect());
            @endphp
            @forelse($loopEvents as $event)
                @php
                    $startAt = null;
                    if($event->event_date){
                        $dateStr = $event->event_date->format('Y-m-d');
                        if($event->event_time){
                            $timeStr = method_exists($event->event_time,'format') ? $event->event_time->format('H:i:s') : (is_string($event->event_time) ? $event->event_time : '00:00:00');
                        } else { $timeStr = '00:00:00'; }
                        try { $startAt = \Carbon\Carbon::parse($dateStr.' '.$timeStr, config('app.timezone')); } catch (Exception $e) { $startAt = null; }
                    }
                @endphp
                @php
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
                        @if($event->image_url)
                            <img class="card-image-event" src="{{ $event->image_url }}" alt="{{ $event->title }}" onerror="this.src='{{ asset('aset/poster.png') }}'">
                        @else
                            <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="{{ $event->title }}">
                        @endif
                        @php
                            $showDiscountBadge = method_exists($event,'hasDiscount') && $event->hasDiscount() && $event->price > 0 && $event->price > $event->discounted_price;
                            $percentOff = $showDiscountBadge ? round((($event->price - $event->discounted_price) / $event->price) * 100) : 0;
                        @endphp
                        @if($showDiscountBadge && $percentOff > 0)
                            <span class="discount-badge">{{ $percentOff }}% off</span>
                        @endif
                        <div class="badge-save-group" style="gap:12px;">
                            <button class="save-btn" aria-label="Save event" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M2 2v13.5l6-3 6 3V2z" />
                                </svg>
                            </button>
                            
                        </div>
                    </div>
                    <div class="card-body">
                        <h4 class="event-title" style="cursor:pointer;">{{ $event->title }}</h4>
                        <div class="tags">
                            <span class="tag">{{ $event->jenis ? Str::limit($event->jenis,18) : 'Jenis' }}</span>
                            <span class="tag">{{ $event->materi ? Str::limit($event->materi,18) : 'Materi' }}</span>
                            <div class="meta" style="margin-left:auto; gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                </svg>
                                <span>{{ $event->registrations_count ?? ($event->registrations()->where('status','active')->distinct('user_id')->count('user_id')) }}</span>
                            </div>
                        </div>
                        <div class="desc-event rich-desc">{!! Str::limit(strip_tags($event->description,'<p><br><strong><em><ul><ol><li><b><i>'), 220) !!}</div>
                        <div class="keterangan keterangan-row">
                            <div class="keterangan-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-event" viewBox="0 0 16 16">
                                    <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                </svg>
                                <span>{{ $event->event_date?->format('d F Y') ?? '-' }}</span>
                            </div>
                            <div class="keterangan-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                                </svg>
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
                            <span class="countdown-label" data-countdown-label>Mulai dalam:</span>
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
                                        <span class="price-now price-free">Gratis</span>
                                    @else
                                        <span class="price-now">{{ 'Rp'.number_format($event->price,0,',','.') }}</span>
                                    @endif
                                @endif
                            </div>
                            @php 
                                $registered = !empty($event->is_registered);
                                $isFinished = ($status === 'finished');
                            @endphp
                            <button class="btn-register register-btn btn {{ $registered ? 'btn-success' : ($isFinished ? 'btn-secondary' : 'btn-primary') }}" type="button" {{ ($registered || $isFinished) ? 'disabled' : '' }} onclick="event.stopPropagation();">
                                {{ $registered ? 'Anda Terdaftar' : ($isFinished ? 'Telah Selesai' : 'Daftar') }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5" style="grid-column:1/-1;">
                    <h5 class="mb-2">Belum ada event</h5>
                    <p class="text-muted mb-0">Event akan segera tersedia.</p>
                </div>
            @endforelse
        </div>
        @if(isset($events) && $events instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="mt-4 d-flex justify-content-center">{!! $events->links() !!}</div>
        @endif
    <!-- Success Registration Modal -->
    <div class="modal fade" id="registrationSuccessModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white py-2">
            <h6 class="modal-title">Pendaftaran Berhasil</h6>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="mb-0">Anda berhasil terdaftar pada event: <strong id="registeredEventTitle"></strong></p>
          </div>
          <div class="modal-footer py-2">
            <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
          </div>
        </div>
      </div>
    </div>
    </section>
    @include('partials.footer-before-login')
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Search submit behavior (icon click or Enter)
        (function(){
            const searchForm = document.querySelector('.search-form');
            const input = document.getElementById('site-search');
            const trigger = document.getElementById('search-submit-trigger');
            const suggest = document.getElementById('search-suggest');
            const LS_KEY = 'events_search_history_v1';
            const LIMIT = 10;

        function loadHistory(){
            try { return JSON.parse(localStorage.getItem(LS_KEY) || '[]'); } catch { return []; }
        }
        function saveHistory(list){
            try { localStorage.setItem(LS_KEY, JSON.stringify(list.slice(0, LIMIT))); } catch {}
        }
        function addToHistory(term){
            const t = (term||'').trim();
            if(!t) return;
            let list = loadHistory();
            const lower = t.toLowerCase();
            list = list.filter(x => (x||'').toString().toLowerCase() !== lower);
            list.unshift(t);
            saveHistory(list);
        }
        function clearHistory(){ saveHistory([]); renderSuggestions(); }

        function renderSuggestions(filter=''){
            if(!suggest) return;
            const all = loadHistory();
            const f = (filter||'').trim().toLowerCase();
            let items = all;
            if(f){ items = all.filter(x => x.toLowerCase().includes(f)); }
            suggest.innerHTML = '';
            if(items.length === 0){
                // If nothing to show and nothing typed, hide
                suggest.classList.remove('show');
                return;
            }
            items.forEach((txt, idx)=>{
                const li = document.createElement('li');
                li.setAttribute('role','option');
                li.setAttribute('tabindex','-1');
                li.textContent = txt;
                li.addEventListener('mousedown', function(e){ e.preventDefault(); });
                li.addEventListener('click', function(){
                    input.value = txt;
                    submitIfNotEmpty();
                });
                suggest.appendChild(li);
            });
            // Clear all control
            const divider = document.createElement('li');
            divider.innerHTML = '<span class="muted">—</span>';
            divider.style.cursor='default';
            divider.addEventListener('mousedown', e=>e.preventDefault());
            suggest.appendChild(divider);
            const clear = document.createElement('li');
            clear.innerHTML = '<span class="clear-all">Hapus riwayat pencarian</span>';
            clear.addEventListener('mousedown', function(e){ e.preventDefault(); });
            clear.addEventListener('click', clearHistory);
            suggest.appendChild(clear);
            suggest.classList.add('show');
            activeIndex = -1;
        }

        function hideSuggestions(){ suggest?.classList.remove('show'); }

        let activeIndex = -1;
        function moveActive(delta){
            if(!suggest || !suggest.classList.contains('show')) return;
            const items = Array.from(suggest.querySelectorAll('li')).filter(li=>!li.querySelector('.muted') && !li.querySelector('.clear-all'));
            if(items.length === 0) return;
            activeIndex = (activeIndex + delta + items.length) % items.length;
            items.forEach(li=>li.classList.remove('active'));
            const cur = items[activeIndex];
            cur.classList.add('active');
            cur.scrollIntoView({ block: 'nearest' });
        }

        function submitIfNotEmpty(){
            if(!input || !searchForm) return;
            const q = (input.value || '').trim();
            if(q.length === 0){ input.value=''; hideSuggestions(); searchForm.submit(); return; }
            input.value = q; // normalize spaces
            addToHistory(q);
            hideSuggestions();
            searchForm.submit();
        }
        trigger?.addEventListener('click', submitIfNotEmpty);
        trigger?.addEventListener('keydown', (e)=>{ if(e.key==='Enter' || e.key===' '){ e.preventDefault(); submitIfNotEmpty(); }});
        function getSelectableItems(){
            if(!suggest) return [];
            return Array.from(suggest.querySelectorAll('li')).filter(li=>!li.querySelector('.muted') && !li.querySelector('.clear-all'));
        }
        input?.addEventListener('keydown', (e)=>{
            if(e.key==='Enter'){
                e.preventDefault();
                const items = getSelectableItems();
                if(suggest?.classList.contains('show') && items.length && activeIndex>=0){
                    const chosen = items[activeIndex];
                    if(chosen){ input.value = chosen.textContent || input.value; }
                }
                submitIfNotEmpty();
            }
            else if(e.key==='ArrowDown'){ e.preventDefault(); renderSuggestions(input.value); moveActive(1); }
            else if(e.key==='ArrowUp'){ e.preventDefault(); renderSuggestions(input.value); moveActive(-1); }
            else if(e.key==='Escape'){ hideSuggestions(); }
        });
        input?.addEventListener('input', function(){ renderSuggestions(this.value); });
        input?.addEventListener('focus', function(){ renderSuggestions(this.value); });
        document.addEventListener('click', function(e){
            if(!suggest) return;
            if(e.target === input || suggest.contains(e.target)) return;
            hideSuggestions();
        });
    })();
    // Auto-submit filters
    const form = document.getElementById('eventFilters');
    const locSel = document.getElementById('filter-location');
    const priceSel = document.getElementById('filter-price');
    const freeHidden = document.getElementById('filter-free');
    function submitFilters(){ form && form.submit(); }
    if(locSel){ locSel.addEventListener('change', submitFilters); }
    if(priceSel){
        priceSel.addEventListener('change', function(){
            if(this.value === 'free'){
                // Clear price sort and set free=1
                this.value = '';
                if(freeHidden){ freeHidden.value = 1; }
            } else {
                if(freeHidden){ freeHidden.value = ''; }
            }
            submitFilters();
        });
    }
    // Dropdown filters (day/type/category)
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
            const hidden = hiddenMap[key];
            if(hidden){ hidden.value = val; }
            submitFilters();
        });
    });
    // Robust dropdown toggler for header filters (independent of Bootstrap)
    (function(){
        const containerSelector = '.header-card';
        const toggles = document.querySelectorAll(containerSelector + ' .dropdown-toggle');
        function closeAll(except){
            document.querySelectorAll(containerSelector + ' .dropdown-menu.show').forEach(m=>{ if(m!==except) m.classList.remove('show'); });
            document.querySelectorAll(containerSelector + ' .dropdown-toggle[aria-expanded]')
                .forEach(b=>b.setAttribute('aria-expanded','false'));
        }
        toggles.forEach(btn => {
            // Remove data attribute to avoid double-toggling if Bootstrap also present
            if(btn.hasAttribute('data-bs-toggle')) btn.removeAttribute('data-bs-toggle');
            btn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                const menu = this.nextElementSibling;
                if(!menu) return;
                const isOpen = menu.classList.contains('show');
                closeAll(menu);
                menu.classList.toggle('show', !isOpen);
                this.setAttribute('aria-expanded', String(!isOpen));
            });
        });
        document.addEventListener('click', function(e){
            if(!e.target.closest(containerSelector + ' .dropdown')){
                closeAll();
            }
        });
    })();
    // Card click -> go to detail
    document.querySelectorAll('.card-event').forEach(card => {
        const url = card.getAttribute('data-detail-url');
        if(!url) return;
        card.addEventListener('click', function(){
            window.location = url;
        });
        // Prevent nested anchor style selection issues
        const titleEl = card.querySelector('.event-title');
        if(titleEl){
            titleEl.addEventListener('click', function(e){
                e.stopPropagation();
                window.location = url;
            });
        }
    });
    // Register button -> go to detail page instead of direct registration
    document.querySelectorAll('.register-btn').forEach(btn => {
        btn.addEventListener('click', function(){
            if(this.disabled) return;
            const card = this.closest('.card-event');
            const url = card ? card.getAttribute('data-detail-url') : null;
            if(url) window.location = url;
        });
    });
    // Status filter: submit to server for correct dataset (finished/ongoing/upcoming)
    (function(){
        const statusBtn = document.getElementById('statusFilterBtn');
        const statusInput = document.getElementById('filter-status');
        const links = document.querySelectorAll('[data-status-filter]');
        function labelOf(val){
            if(val==='upcoming') return 'Mendatang';
            if(val==='ongoing') return 'Sedang Berlangsung';
            if(val==='finished') return 'Telah Selesai';
            return 'Semua Status';
        }
        // Initialize label from current query
        const cur = (new URLSearchParams(window.location.search).get('status')) || '{{ request('status') }}' || '';
        if(statusBtn){ statusBtn.textContent = labelOf(cur || 'all'); }
        links.forEach(a => {
            a.addEventListener('click', function(e){
                e.preventDefault();
                const val = this.getAttribute('data-value') || 'all';
                if(statusBtn){ statusBtn.textContent = labelOf(val); }
                if(statusInput){ statusInput.value = (val==='all') ? '' : val; }
                submitFilters();
            });
        });
    })();
});
</script>
<script>
// Countdown script (hari jam menit detik) for event listing
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
        if(days > 0) parts.push(days + ' hari');
        if(hours > 0 || days > 0) parts.push(hours + ' jam');
        // Show mm:ss when under 1 hour; otherwise show minutes only
        if(days === 0 && hours === 0){
            parts.push(pad(minutes) + ':' + pad(seconds));
        } else {
            parts.push(minutes + ' menit');
        }
        return parts.join(' ');
    }
    function update(){
        const now = Math.floor(Date.now()/1000);
        document.querySelectorAll('[data-countdown]').forEach(el => {
            const start = parseInt(el.getAttribute('data-start-ts'),10);
            if(!start) return;
            const endAttr = el.getAttribute('data-end-ts');
            const end = endAttr ? parseInt(endAttr,10) : null;
            const label = el.closest('[data-countdown-wrapper]')?.querySelector('[data-countdown-label]');
            // Finished
            if(end && now > end){
                el.textContent = 'Telah Selesai';
                el.classList.remove('started');
                el.classList.add('expired');
                if(label) label.textContent = 'Status:';
                return;
            }
            // Started but not finished
            if(now >= start){
                el.textContent = 'Sedang Berlangsung';
                el.classList.remove('expired');
                el.classList.add('started');
                if(label) label.textContent = 'Status:';
                return;
            }
            // Not started yet -> countdown
            const diff = start - now;
            el.textContent = formatDiff(diff);
            el.classList.remove('started','expired');
            if(label) label.textContent = 'Mulai dalam:';
        });
    }
    // Initial paint and 1s interval for smoother countdown, esp. near start time
    update();
    setInterval(update, 1000);
})();
</script>
 @include('partials.footer-before-login')