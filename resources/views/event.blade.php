<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
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
    </style>
    </head>
<body>
    @include('partials.navbar-after-login')
    <section class="hero-carousel">
        <div id="carouselExampleInterval" class="carousel slide custom-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="10000">
                    <img src="{{ asset('aset/ai.jpg') }}"
                        class="d-block" alt="...">
                </div>
                <div class="carousel-item" data-bs-interval="2000">
                    <img src="{{ asset('aset/ai2.jpg') }}"
                        class="d-block" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('aset/ai3.jpg') }}"
                        class="d-block" alt="...">
                </div>
            </div>

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
                    <span class="search-icon" id="search-submit-trigger" ariza-hidden="false" tabindex="0" role="button" aria-label="Cari">
                        <svg id="search-icon-svg" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
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
    <section class="event">
        <div class="header-card">
            <h3>Event Mendatang</h3>
            <div class="dropdown-box d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Weekdays
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Weekdays</a></li>
                        <li><a class="dropdown-item" href="#">Weekend</a></li>
                        <li><a class="dropdown-item" href="#">Today</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Event Type
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Online</a></li>
                        <li><a class="dropdown-item" href="#">Onsite</a></li>
                        <li><a class="dropdown-item" href="#">Hybrid</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Any Categori
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Workshop</a></li>
                        <li><a class="dropdown-item" href="#">Training</a></li>
                        <li><a class="dropdown-item" href="#">Webinar</a></li>
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
                <div class="card-event" @if($startAt) data-event-start-ts="{{ $startAt->timestamp }}" @endif data-detail-url="{{ route('events.show',$event) }}" style="cursor:pointer;">
                    <div class="thumb-wrapper">
                        @if(!empty($event->image))
                            <img class="card-image-event" src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}">
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
                            <span class="countdown-label">Mulai dalam:</span>
                            <span class="countdown-timer" data-countdown data-start-ts="{{ $startAt->timestamp }}">--</span>
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
                            @php $registered = !empty($event->is_registered); @endphp
                            <button class="btn-register register-btn btn {{ $registered ? 'btn-success' : 'btn-primary' }}" type="button" {{ $registered ? 'disabled' : '' }} onclick="event.stopPropagation();">
                                {{ $registered ? 'Anda Terdaftar' : 'Daftar' }}
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
                const diff = start - now;
                if(diff <= 0){
                    el.textContent = 'Dimulai';
                    el.classList.add('started');
                    return;
                }
                el.textContent = formatDiff(diff);
            });
        }
        // Initial paint and 1s interval for smoother countdown, esp. near start time
        update();
<<<<<<< HEAD
        setInterval(update, 1000);
=======
        setInterval(update, 60000);
>>>>>>> 7c287cc6e13fddde0a1fa94ce4bba305577efb13
    })();
    </script>
</body>
</html>