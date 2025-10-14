<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Detail Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.navbar-after-login')
    <div class="link-box mb-3" style="margin-top:80px;">
        <a href="{{ route('dashboard') }}">Home</a>
        <p>/</p>
        <a href="{{ route('events.index') }}">Event</a>
        <p>/</p>
        <a class="active" href="#">{{ Str::limit($event->title,50) }}</a>
    </div>
    @php 
        $hasDiscount = $event->hasDiscount();
        $isFree = (int)$event->price === 0;
        $finalPrice = $hasDiscount ? $event->discounted_price : $event->price;
        $daysLeft = null;
        if($event->event_date){
            $daysLeft = now()->diffInDays($event->event_date, false);
        }
        // Target datetime untuk countdown event (gabungkan tanggal + waktu jika ada)
        $targetDateTime = null;
        if($event->event_date){
            $datePart = $event->event_date->format('Y-m-d');
            $timePart = $event->event_time?->format('H:i:s') ?? '00:00:00';
            $targetDateTime = $datePart.' '.$timePart; // diasumsikan zona waktu server Asia/Jakarta
        }
        // Sanitized content
        $rawDescription = $event->description ?? '';
        $cleanDescription = strip_tags($rawDescription, '<p><br><strong><b><em><i><ul><ol><li><a>');
        $rawTnc = $event->terms_and_conditions ?? '';
        $cleanTnc = strip_tags($rawTnc, '<p><br><strong><b><em><i><ul><ol><li><a><blockquote>');
    @endphp

    <div class="event-detail-grid">
        <div class="col-main">
            <div class="event-hero">
                <img src="{{ $event->image ? Storage::url($event->image) : asset('aset/event.png') }}" alt="{{ $event->title }}" data-preview="1">
            </div>
            <h2 class="event-title mt-3">{{ $event->title }}</h2>
            <p class="event-subtitle mb-3">Created by <b>IdSpora</b></p>
            <div class="card-tabs">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-overview" type="button" role="tab">Overview</button>
                    </li>
                    <li class="nav-item" role="presentation">
                         <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-tnc" type="button" role="tab">Terms & Conditions</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        @php $locked = !($event->is_registered ?? false); @endphp
                        <button class="nav-link wa-tab d-inline-flex align-items-center {{ $locked ? 'disabled opacity-75' : '' }}" data-bs-toggle="tab" data-bs-target="#tab-schedule" type="button" role="tab" {{ $locked ? 'aria-disabled=true' : '' }}>
                            @if($locked)
                                <i class="bi bi-lock-fill me-1"></i>
                            @else
                                <i class="bi bi-whatsapp me-1"></i>
                            @endif
                            Link WhatsApp
                        </button>
                    </li>
                </ul>
                <div class="tab-content p-3">
                    <div class="tab-pane fade show active" id="tab-overview" role="tabpanel">
                        <div class="event-description-rich">{!! $cleanDescription !!}</div>
                    </div>
                    <div class="tab-pane fade" id="tab-schedule" role="tabpanel">
                        @php $waLink = $event->whatsapp_link ?? null; @endphp
                        @auth
                            @if(!$event->is_registered)
                                <div class="alert alert-warning d-flex align-items-center" role="alert">
                                    <i class="bi bi-lock-fill me-2"></i>
                                    <div>Fitur ini terkunci. Daftar event terlebih dahulu untuk mengakses link WhatsApp.</div>
                                </div>
                            @else
                                @if($waLink)
                                    <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-success d-inline-flex align-items-center gap-2">
                                        <i class="bi bi-whatsapp"></i>
                                        Buka Link WhatsApp
                                    </a>
                                    <p class="small text-muted mt-2 mb-0">Link ini hanya dapat diakses oleh peserta terdaftar.</p>
                                @else
                                    <p class="text-muted mb-0">Link WhatsApp belum disediakan oleh admin.</p>
                                @endif
                            @endif
                        @endauth
                        @guest
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="bi bi-info-circle me-2"></i>
                                <div>Silakan login dan daftar event untuk membuka link WhatsApp.</div>
                            </div>
                        @endguest
                    </div>
                    <div class="tab-pane fade" id="tab-tnc" role="tabpanel">
                        @if(!empty(trim($cleanTnc)))
                            <div class="event-tnc-rich">{!! $cleanTnc !!}</div>
                        @else
                            <p class="text-muted mb-0">Belum ada syarat dan ketentuan yang ditambahkan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-side">
            <div class="sidebar-card">
                <div class="price-wrap">
                    @if($hasDiscount && !$isFree)
                        <div class="mb-1">
                            <span class="old-price">Rp{{ number_format($event->price,0,',','.') }}</span>
                        </div>
                        <div class="price">Rp{{ number_format($event->discounted_price,0,',','.') }}</div>
                        <span class="badge-off d-inline-block mt-3">{{ (int)$event->discount_percentage }}% off</span>

                    @else
                        <div class="price">@if($isFree) FREE @else Rp{{ number_format($event->price,0,',','.') }} @endif</div>
                    @endif
                    @if($targetDateTime)
                        <div id="priceCountdown" class="countdown-box mt-2" data-target="{{ $targetDateTime }}">
                            <i class="bi bi-alarm me-1"></i>
                            <span id="countdownTextPrice">Menghitung...</span>
                        </div>
                    @endif
                </div>
                <div class="mini-info">
                    <div><i class="bi bi-calendar2-date"></i><span>{{ $event->event_date?->format('d F Y') ?? '-' }}</span></div>
                    <div><i class="bi bi-clock"></i><span>{{ $event->event_time?->format('H:i') ?? '-' }} WIB</span></div>
                    <div><i class="bi bi-geo-alt"></i><span>{{ $event->location ?? 'TBA' }}</span></div>
                </div>
                @php 
                    $registered = !empty($event->is_registered);
                    $isEventFree = (int)$finalPrice === 0;
                @endphp
                <div class="cta">
                @auth
                    @if($registered)
                        <a href="{{ route('events.ticket',$event) }}" class="btn btn-warning w-100 fw-semibold text-dark">Cek Tiket / Kode</a>
                    @else
                        <button id="registerBtn" class="btn btn-warning w-100 fw-semibold text-dark enroll" data-event-id="{{ $event->id }}" data-paid="{{ $isEventFree ? '0':'1' }}">Register Now</button>
                    @endif
                @endauth
                @guest
                    <a href="{{ route('login',['redirect'=>request()->fullUrl()]) }}" class="btn btn-warning w-100 fw-semibold text-dark">Login untuk Mendaftar</a>
                @endguest
                    <button class="btn btn-light w-100 mt-2 save">Save</button>
                </div>
                <div class="includes">
                    <h6 class="fw-semibold mb-2">This event includes:</h6>
                    <ul class="list-unstyled m-0">
                        <li><i class="bi bi-file-earmark-text text-warning me-2"></i>Slide Materi</li>
                        <li><i class="bi bi-award text-warning me-2"></i>Sertifikat</li>
                        <li><i class="bi bi-people text-warning me-2"></i>Grup Diskusi</li>
                    </ul>
                </div>
                <div class="share">
                    <p class="fw-semibold mb-2">Share this event:</p>
                    <div class="share-list">
                        <button type="button" class="share-item" onclick="copyLink()" aria-label="Copy link">
                            <i class="bi bi-clipboard"></i>
                            <span>Copy link</span>
                        </button>
                        <a href="#" class="share-item" aria-label="Share to Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="share-item" aria-label="Share to X/Twitter"><i class="bi bi-twitter"></i></a>
                        <a href="mailto:?subject={{ urlencode($event->title) }}" class="share-item" aria-label="Share via email"><i class="bi bi-envelope"></i></a>
                        <a href="https://wa.me/?text={{ urlencode($event->title.' '.request()->fullUrl()) }}" target="_blank" class="share-item" aria-label="Share to WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Grid layout matching mockup */
        .event-detail-grid{max-width:1200px;margin:20px auto 60px;padding:0 20px;display:grid;grid-template-columns:1.8fr 1fr;gap:24px}
        .col-side{position:relative}
        .sidebar-card{position:sticky;top:88px;background:#fff;border-radius:16px;padding:18px 18px 16px;box-shadow:0 12px 40px -18px rgba(0,0,0,.2),0 4px 12px -4px rgba(0,0,0,.08)}
        .price-wrap .old-price{color:#9ca3af;text-decoration:line-through}
    .price-wrap .badge-off{background:#252346;color:#F4C430;border-radius:999px;padding:6px 12px;font-weight:700;font-size:12px;}
        .price-wrap .price{font-size:28px;font-weight:800;margin:4px 0}
        .price-wrap .left-note{font-size:12px;color:#ef4444;font-weight:600}
    .countdown-box{display:flex;align-items:center;gap:6px;background:#fee2e2;border:1px solid #fecaca;color:#dc2626;border-radius:10px;padding:8px 10px;font-size:13px;font-weight:600}
        .mini-info{display:grid;grid-template-columns:1fr;gap:8px;margin:10px 0 12px}
        .mini-info i{color:#9ca3af;margin-right:8px}
        .mini-info div{display:flex;align-items:center;font-size:14px;color:#111827}
    /* Share section styling */
    .share-list{display:flex;align-items:center;gap:14px;flex-wrap:wrap}
    .share-item{display:inline-flex;align-items:center;gap:8px;color:#64748b;text-decoration:none;background:none;border:none;padding:0;font-size:14px;cursor:pointer}
    .share-item i{font-size:18px;color:#64748b}
    .share-item span{font-weight:500}
    .share-item:hover{color:#475569}
    .share-item:hover i{color:#475569}
    .card-tabs{background:#fff;border-radius:16px;box-shadow:0 12px 40px -18px rgba(0,0,0,.2),0 4px 12px -4px rgba(0,0,0,.08)}
    .card-tabs .nav-tabs{border:none;background:#f1f5f9;border-radius:12px;gap:12px;padding:8px 10px;display:flex;flex-wrap:wrap}
    .card-tabs .nav-link{position:relative;border:none;border-radius:10px;font-weight:600;color:#64748b;padding:10px 16px;transition:color .2s, background-color .2s, box-shadow .2s; min-width:120px; text-align:center}
    /* WhatsApp tab style */
    .card-tabs .nav-link.wa-tab{background:#25D366;color:#fff}
    .card-tabs .nav-link.wa-tab i{color:#fff}
    .card-tabs .nav-link.wa-tab::after{display:none}
    .card-tabs .nav-link.wa-tab:hover, .card-tabs .nav-link.wa-tab:focus{background:#1EBE5A;color:#fff}
    .card-tabs .nav-link.wa-tab:hover i, .card-tabs .nav-link.wa-tab:focus i{color:#fff}
    .card-tabs .nav-link.wa-tab.active{background:#19A956;color:#fff;box-shadow:0 10px 24px -14px rgba(0,0,0,.25)}
    .card-tabs .nav-link:hover{color:#f4c430;background:#fff}
    .card-tabs .nav-link.active{color:#f4c430;background:#fff;box-shadow:0 10px 24px -14px rgba(0,0,0,.25)}
    .card-tabs .nav-link::after{content:'';position:absolute;left:12px;right:12px;bottom:-8px;height:3px;background:#f4c430;border-radius:6px;opacity:0;transform:scaleX(0);transition:transform .25s ease,opacity .25s ease}
    .card-tabs .nav-link.active::after{opacity:1;transform:scaleX(1)}
    .card-tabs .tab-content{padding:16px}
    .event-description-rich p, .event-tnc-rich p{margin-bottom:.85rem;line-height:1.7;color:#0f172a}
    .event-description-rich ul, .event-tnc-rich ul{padding-left:1.25rem}
    .event-description-rich li, .event-tnc-rich li{margin:.25rem 0}
        .event-hero{position:relative;border-radius:18px;overflow:hidden;box-shadow:0 12px 40px -18px rgba(0,0,0,.2),0 4px 12px -4px rgba(0,0,0,.08)}
        .event-hero img{display:block;width:100%;height:360px;object-fit:cover}
    .hero-chips{position:absolute;left:12px;bottom:12px;display:flex;gap:8px;flex-wrap:wrap; pointer-events: none;}
        .hero-chips .chip{background:rgba(15,23,42,.82);color:#fff;border-radius:999px;padding:6px 12px;font-size:12px;backdrop-filter:saturate(180%) blur(8px)}
    .event-title{font-size:24px;font-weight:700}
    .event-subtitle{margin-top:2px;color:#64748b}
    .sidebar-card .includes{margin-top:16px;padding-top:12px;border-top:1px solid #e5e7eb}
    .includes ul li{margin:6px 0}
    .sidebar-card .share{margin-top:20px;padding-top:12px;border-top:1px solid #e5e7eb}
        .save{border:1px solid #e5e7eb}

        /* Success registration modal */
        .success-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: none; align-items: center; justify-content: center; z-index: 9999; }
        .success-overlay.show { display: flex; animation: fadeIn .15s ease-out; }
        @keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
        .success-modal { background: #fff; border-radius: 16px; padding: 24px 22px; width: 92%; max-width: 380px; text-align: center; box-shadow: 0 18px 44px -16px rgba(0,0,0,.35), 0 6px 18px -6px rgba(0,0,0,.18); transform: scale(.94); animation: pop .18s ease-out forwards; }
        @keyframes pop { to { transform: scale(1) } }
        .success-check { width: 86px; height: 86px; border-radius: 50%; background: #eafaf1; border: 2px solid #34c75930; margin: 6px auto 14px; display: grid; place-items: center; }
        .success-check svg { width: 54px; height: 54px; }
        .success-check path { stroke: #19a05a; stroke-width: 6; fill: none; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 60; stroke-dashoffset: 60; animation: draw .6s ease forwards .15s; }
        @keyframes draw { to { stroke-dashoffset: 0 } }
        .success-title { font-weight: 700; margin: 0 0 6px; color: #0f5132; }
        .success-text { margin: 0; color: #276749; font-size: 14px; }
        .success-small { margin-top: 10px; font-size: 12px; color: #64748b; }

        /* Confirm registration modal */
        .confirm-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.45); display: none; align-items: center; justify-content: center; z-index: 9999; }
        .confirm-overlay.show { display: flex; animation: fadeIn .15s ease-out; }
        .confirm-modal { background: #fff; border-radius: 16px; padding: 20px 20px; width: 92%; max-width: 380px; text-align: center; box-shadow: 0 18px 44px -16px rgba(0,0,0,.35), 0 6px 18px -6px rgba(0,0,0,.18); transform: scale(.94); animation: pop .18s ease-out forwards; }
        .confirm-title { font-weight: 700; margin: 4px 0 8px; color: #111827; }
        .confirm-text { margin: 0 0 12px; color: #374151; font-size: 14px; }
        .confirm-actions { display:flex; gap:10px; justify-content:center; margin-top:6px; }
    .btn-outline { background:#fff; border:1px solid #cbd5e1; color:#334155; border-radius:10px; padding:8px 14px; font-weight:600; cursor:pointer; transition: all .15s ease; }
    .btn-primary-confirm { background:#4f46e5; color:#fff; border:none; border-radius:10px; padding:8px 14px; font-weight:600; cursor:pointer; transition: all .15s ease; }
    .btn-outline:hover { background:#f1f5f9; border-color:#94a3b8; box-shadow: 0 6px 16px -8px rgba(2,6,23,.25); transform: translateY(-1px); }
    .btn-primary-confirm:hover { background:#4338ca; box-shadow: 0 8px 18px -10px rgba(2,6,23,.35); transform: translateY(-1px); }
    .btn-outline:active, .btn-primary-confirm:active { transform: translateY(0); box-shadow:none; }

    /* Hover for register/cek tiket button */
    .enroll { transition: transform .12s ease, box-shadow .12s ease, filter .12s ease; }
    .enroll:hover { transform: translateY(-1px); box-shadow: 0 8px 18px -10px rgba(0,0,0,.25); filter: brightness(1.02); }
        /* Save-styled anchor: yellow button look */
        a.save, a.save:hover, a.save:focus, a.save:active { text-decoration: none !important; }
        a.save {
            background: #f4c430; /* yellow */
            color: #111827; /* dark text for contrast */
            display: block;
            width: 100%;
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 700;
            text-align: center;
            border: none;
            transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
        }
        a.save:hover { filter: brightness(0.98); transform: translateY(-1px); box-shadow: 0 8px 18px -10px rgba(0,0,0,.25); }
        /* Save button text in yellow for button variant */
        button.save { color: #f4c430 !important; font-weight: 700; }
        /* Image preview affordance */
        .event-hero img[data-preview="1"]{ cursor: zoom-in; }
        /* Modal tweaks for image preview */
        .modal.image-preview .modal-content{ background: rgba(0,0,0,.92); border: 0; }
        .modal.image-preview .modal-body{ padding: 0; display:flex; align-items:center; justify-content:center; }
        .modal.image-preview img{ max-width: 100%; max-height: 85vh; object-fit: contain; }
    .modal.image-preview .preview-close{ position:absolute; top:10px; right:12px; z-index:2; filter: invert(1) grayscale(1); }
        @media (max-width: 992px){
            .event-detail-grid{grid-template-columns:1fr}
            .sidebar-card{position:static}
            .event-hero img{height:240px}
        }
        @media (max-width: 576px){
            .card-tabs .nav-tabs{gap:8px;padding:6px}
            .card-tabs .nav-link{min-width:0;padding:8px 12px}
        }
    </style>
    <script>
        // Persist active tab via URL hash (#tab=overview|schedule|tnc)
        (function(){
            const hash = location.hash.match(/#tab=(overview|schedule|tnc)/)?.[1];
            if(hash){
                const btn = document.querySelector(`[data-bs-target="#tab-${hash}"]`);
                if(btn && window.bootstrap){
                    const t = new bootstrap.Tab(btn);
                    t.show();
                }
            }
            document.querySelectorAll('.card-tabs [data-bs-toggle="tab"]').forEach(el=>{
                el.addEventListener('shown.bs.tab', (e)=>{
                    const target = e.target.getAttribute('data-bs-target');
                    const name = target ? target.replace('#tab-','') : '';
                    if(name){ history.replaceState(null, '', `#tab=${name}`); }
                });
            });
        })();
        // Countdown di card harga (menuju waktu mulai event)
        (function(){
            const box = document.getElementById('priceCountdown');
            if(!box) return;
            const targetStr = box.getAttribute('data-target'); // 'YYYY-MM-DD HH:MM:SS'
            if(!targetStr) return;
            const parts = targetStr.split(/[- :]/);
            const target = new Date(parts[0], parts[1]-1, parts[2], parts[3], parts[4], parts[5]);
            const textEl = document.getElementById('countdownTextPrice');
            function pad(n){return n<10?'0'+n:n;}
            function tick(){
                const now = new Date();
                let diff = target.getTime() - now.getTime();
                if(diff <= 0){ textEl.textContent = 'Sedang berlangsung / selesai'; clearInterval(timer); return; }
                const days = Math.floor(diff / (1000*60*60*24));
                diff -= days * (1000*60*60*24);
                const hours = Math.floor(diff / (1000*60*60));
                diff -= hours * (1000*60*60);
                const minutes = Math.floor(diff / (1000*60));
                diff -= minutes * (1000*60);
                const seconds = Math.floor(diff / 1000);
                if(days > 0){
                    textEl.textContent = `${days} hari ${pad(hours)} jam ${pad(minutes)} menit ${pad(seconds)} Detik `;
                } else {
                    textEl.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)} lagi`;
                }
            }
            tick();
            const timer = setInterval(tick,1000);
        })();
        // Image preview handler
        (function(){
            const img = document.querySelector('.event-hero img[data-preview="1"]');
            if(!img) return;
            img.addEventListener('click', ()=>{
                const modalEl = document.getElementById('imagePreviewModal');
                const target = document.getElementById('previewImg');
                if(!modalEl || !target) return;
                const src = img.getAttribute('src');
                if(!src) return;
                target.src = src;
                if(window.bootstrap){
                    const m = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: true, keyboard: true });
                    m.show();
                } else {
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                }
            });
            // Fallback close when Bootstrap isn't present
            const closeBtn = document.querySelector('#imagePreviewModal .preview-close');
            closeBtn?.addEventListener('click', ()=>{
                const modalEl = document.getElementById('imagePreviewModal');
                if(window.bootstrap){
                    const m = bootstrap.Modal.getOrCreateInstance(modalEl);
                    m.hide();
                } else if(modalEl){
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                }
            });
        })();
    </script>
    <!-- Image Preview Modal -->
    <div class="modal fade image-preview" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="btn-close btn-close-white preview-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <img id="previewImg" alt="Preview {{ $event->title }}">
                </div>
            </div>
        </div>
    </div>
    <!-- Success Modal -->
    <div id="successOverlay" class="success-overlay" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="success-modal">
            <div class="success-check">
                <svg viewBox="0 0 80 80" aria-hidden="true">
                    <path d="M18 42 L34 58 L62 26"></path>
                </svg>
            </div>
            <h5 id="successTitle" class="success-title">Pendaftaran Berhasil</h5>
            <p id="successText" class="success-text">Anda telah terdaftar pada event ini.</p>
            <div class="success-small">Anda bisa cek tiket/kode dari tombol di halaman ini.</div>
        </div>
    </div>
    <!-- Confirm Modal -->
    <div id="confirmOverlay" class="confirm-overlay" role="dialog" aria-modal="true" aria-hidden="true">
        <div class="confirm-modal">
            <h5 class="confirm-title">Konfirmasi</h5>
            <p class="confirm-text">Apakah kamu yakin ingin mendaftar event ini?</p>
            <div class="confirm-actions">
                <button type="button" id="confirmNo" class="btn-outline">Batal</button>
                <button type="button" id="confirmYes" class="btn-primary-confirm">Ya, daftar</button>
            </div>
        </div>
    </div>
    <script>
        function copyLink(){
            const temp = document.createElement('input');
            temp.value = window.location.href;
            document.body.appendChild(temp);
            temp.select();
            temp.setSelectionRange(0,99999);
            navigator.clipboard.writeText(temp.value).then(()=>{ alert('Link berhasil disalin!'); }).catch(()=>{ alert('Gagal menyalin link'); });
            document.body.removeChild(temp);
        }
        @auth
        const btn = document.getElementById('registerBtn');
        if(btn){
            btn.addEventListener('click', () => {
                if(btn.classList.contains('btn-success')) return;
                const isPaid = btn.getAttribute('data-paid') === '1';
                showConfirmRegister(async () => {
                    if(isPaid){
                        window.location.href = `{{ route('payment',$event) }}`;
                        return;
                    }
                    btn.disabled = true;
                    fetch(`/events/${btn.dataset.eventId}/register`, {
                        method:'POST',
                        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                        body: JSON.stringify({})
                    }).then(r=>r.json()).then(data=>{
                        // Show success animation for free registration success
                        if(data.status==='ok'){
                            showRegistrationSuccess('Pendaftaran Berhasil','Anda telah terdaftar pada event ini.');
                        } else if(data.status==='already'){
                            showRegistrationSuccess('Sudah Terdaftar','Anda sudah terdaftar pada event ini.');
                        }
                        if(data.status==='ok' || data.status==='already'){
                            const ticketUrl = `/events/${btn.dataset.eventId}/ticket`;
                            const link = document.createElement('a');
                            link.href = ticketUrl;
                            link.className = 'save cek-tiket-btn';
                            link.textContent = 'Cek Tiket / Kode';
                            link.style.textAlign = 'center';
                            btn.replaceWith(link);
                        } else if(data.status==='payment_required' && data.redirect){
                            window.location.href = data.redirect;
                        } else { alert(data.message || 'Gagal mendaftar'); btn.disabled=false; }
                    }).catch(()=>{ alert('Terjadi kesalahan'); btn.disabled=false; });
                });
            });
        }
        @endauth

        function showRegistrationSuccess(title, text){
            try{
                const overlay = document.getElementById('successOverlay');
                const t = document.getElementById('successTitle');
                const d = document.getElementById('successText');
                if(!overlay || !t || !d) return;
                t.textContent = title || 'Pendaftaran Berhasil';
                d.textContent = text || 'Anda telah terdaftar pada event ini.';
                overlay.classList.add('show');
                // auto hide after 2 seconds
                setTimeout(()=>{ overlay.classList.remove('show'); }, 2000);
            }catch(_e){ /* no-op */ }
        }
        function showConfirmRegister(onYes){
            const overlay = document.getElementById('confirmOverlay');
            const yesBtn = document.getElementById('confirmYes');
            const noBtn = document.getElementById('confirmNo');
            if(!overlay || !yesBtn || !noBtn) { if(typeof onYes==='function') onYes(); return; }
            let closed = false;
            const close = ()=>{ if(closed) return; closed = true; overlay.classList.remove('show'); cleanup(); };
            const cleanup = ()=>{
                yesBtn.removeEventListener('click', yesHandler);
                noBtn.removeEventListener('click', noHandler);
                overlay.removeEventListener('click', backdropHandler);
            };
            const yesHandler = ()=>{ close(); if(typeof onYes==='function') onYes(); };
            const noHandler = ()=>{ close(); };
            const backdropHandler = (e)=>{ if(e.target === overlay) close(); };
            yesBtn.addEventListener('click', yesHandler);
            noBtn.addEventListener('click', noHandler);
            overlay.addEventListener('click', backdropHandler);
            overlay.classList.add('show');
        }
        // Countdown (Hari : Jam : Menit : Detik) sampai waktu event
        (function(){
            const box = document.getElementById('eventCountdown');
            if(!box) return;
            const targetStr = box.getAttribute('data-target'); // format 'YYYY-MM-DD HH:MM:SS'
            // Asumsikan timezone server Asia/Jakarta; paksa interpretasi lokal browser lalu hitung selisih
            const parts = targetStr.split(/[- :]/); // [Y,m,d,H,i,s]
            const target = new Date(parts[0], parts[1]-1, parts[2], parts[3], parts[4], parts[5]);
            const textEl = document.getElementById('countdownText');
            function pad(n){return n<10?'0'+n:n;}
            function tick(){
                const now = new Date();
                let diff = target.getTime() - now.getTime();
                if(diff <= 0){
                    textEl.textContent = 'Sedang berlangsung atau selesai';
                    clearInterval(timer);
                    return;
                }
                const days = Math.floor(diff / (1000*60*60*24));
                diff -= days * (1000*60*60*24);
                const hours = Math.floor(diff / (1000*60*60));
                diff -= hours * (1000*60*60);
                const minutes = Math.floor(diff / (1000*60));
                diff -= minutes * (1000*60);
                const seconds = Math.floor(diff / 1000);
                if(days > 0){
                    textEl.textContent = `Sisa ${days} hari ${pad(hours)} jam ${pad(minutes)} menit ${pad(seconds)} detik`;
                } else {
                    textEl.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)} lagi`;
                }
            }
            tick();
            const timer = setInterval(tick,1000);
        })();
    </script>
    @include('partials.footer-before-login')
</body>
</html>
