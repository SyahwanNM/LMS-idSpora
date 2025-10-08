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
        .event .event-list {row-gap:40px;} /* a bit more vertical spacing between cards */
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
        /* Discount badge styling (added) */
        .event .card-event .thumb-wrapper {overflow:hidden;}
        .event .card-event .discount-badge {position:absolute; top:12px; left:12px; background:#212f4d; color:#d6bc3a; font-size:13px; font-weight:600; padding:6px 10px 5px; border-radius:6px; line-height:1; letter-spacing:.5px; box-shadow:0 2px 6px rgba(0,0,0,.25); display:inline-flex; align-items:center; gap:4px; text-transform:uppercase;}
        /* Countdown styles */
        .countdown-wrapper {margin-top:10px; display:flex; align-items:center; gap:6px; font-size:13px; font-weight:500;}
        .countdown-label {color:#555; font-weight:500;}
        .countdown-timer {background:#212f4d; color:#ffd54f; padding:2px 8px; border-radius:4px; font-family:monospace; letter-spacing:1px; min-width:140px; text-align:center;}
        .countdown-timer.started {background:#198754; color:#fff;}
        .countdown-timer.expired {background:#6c757d; color:#fff;}
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
        <div class="filter-box">
            <div class="options">
                <label>Level</label>
                <select>
                    <option>Beginner</option>
                    <option>Intermediate</option>
                    <option>Advanced</option>
                </select>
            </div>
            <div class="options">
                <label>Place</label>
                <select>
                    <option>Bandung</option>
                    <option>Jakarta</option>
                    <option>Bekasi</option>
                </select>
            </div>
            <div class="options">
                <label>Price</label>
                <select>
                    <option>Low to High</option>
                    <option>High to Low</option>
                    <option>Free</option>
                </select>
            </div>
        </div>
        <div class="search-container">
            <form class="search-form" action="#" method="get" autocomplete="off">
                <div class="search-wrap">
                    <input id="site-search" class="form-control search-input-2" type="search" name="search"
                        placeholder="Search" aria-label="Search" aria-expanded="false" aria-controls="search-suggest">
                    <span class="search-icon" ariza-hidden="false">
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
                            <span class="course-badge beginner">Beginner</span>
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
                            <span class="tag">{{ $event->speaker ? Str::limit($event->speaker,18) : 'Narasumber' }}</span>
                            <span class="tag">{{ $event->location ? Str::limit($event->location,18) : 'Lokasi TBA' }}</span>
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
                                    <span class="price-now">{{ $isFree ? 'Gratis' : 'Rp'.number_format($event->price,0,',','.') }}</span>
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
    // Countdown script (hari jam menit) for event listing
    (function(){
        function formatDiff(totalSec){
            if(totalSec <= 0) return 'Dimulai';
            let sec = totalSec;
            const days = Math.floor(sec/86400); sec%=86400;
            const hours = Math.floor(sec/3600); sec%=3600;
            const minutes = Math.floor(sec/60);
            if(minutes === 0 && hours === 0 && days === 0) return '< 1 menit';
            const parts = [];
            if(days > 0) parts.push(days + ' hari');
            if(hours > 0 || days > 0) parts.push(hours + ' jam');
            parts.push(minutes + ' menit');
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
        update();
        setInterval(update, 60000); // update every minute sufficient for minute resolution
    })();
    </script>
</body>
</html>