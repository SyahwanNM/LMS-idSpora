@include('partials.navbar-after-login')
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
    <div class="link-box mb-3" style="margin-top:80px;">
        <a href="{{ route('dashboard') }}">Home</a>
        <p>/</p>
        <a href="{{ route('events.index') }}">Event</a>
        <p>/</p>
        <a class="active" href="#">{{ Str::limit($event->title,50) }}</a>
    </div>

    <div class="container-detail">
        <div class="kiri">
            <img src="{{ $event->image ? Storage::url($event->image) : asset('aset/event.png') }}" class="event-img" alt="{{ $event->title }}">
            <div class="profile-box">
                <img src="{{ asset('aset/profile.png') }}" alt="profile" class="rounded-circle" width="60" height="60">
                <div>
                    <h5 class="mb-1">{{ $event->title }}</h5>
                    <h6 class="text-muted">Created by <span class="black">IdSpora</span></h6>
                </div>
            </div>
            <div class="overview">
                <h3 class="mb-3">Overview</h3>
                @php
                    $cleanDescription = $event->description ?? '';
                    // Remove <p> and <strong> tags but keep their inner text
                    $cleanDescription = preg_replace('/<\\/?(p|strong)>/i', '', $cleanDescription);
                @endphp
                <p>{!! nl2br(e($cleanDescription)) !!}</p>
            </div>
            <div class="terms-condition">
                <h3 class="mb-3">Terms and Condition</h3>
                <p>1. Event ini berlaku untuk umum <br>
                    2. Peserta diharapkan join grup WA setelah mendaftar (atau melalui dashboard) <br>
                    3. Informasi & link room dibagikan lewat grup WA <br>
                    4. Sertifikat dapat diunduh H+4 setelah acara <br>
                    5. Kontribusi mulai Rp{{ number_format(((int)$event->discounted_price ?: (int)$event->price) ?: 5000,0,',','.') }} untuk akses materi, rekaman, sertifikat (jika berlaku) <br>
                    6. Peserta wajib mengikuti aturan yang berlaku.</p>
            </div>
        </div>
        <div class="kanan">
            <div class="price">
                @php $hasDiscount = $event->hasDiscount(); @endphp
                @if($hasDiscount)
                    <span class="text-muted text-decoration-line-through">Rp{{ number_format($event->price,0,',','.') }}</span>
                    <h4 class="price-text">Rp{{ number_format($event->discounted_price,0,',','.') }}</h4>
                    <div class="box-diskon">
                        <div class="time-alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="ikon bi bi-alarm" viewBox="0 0 16 16"><path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9z"/><path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1zm1.038 3.018a6 6 0 0 1 .924 0 6 6 0 1 1-.924 0M0 3.5c0 .753.333 1.429.86 1.887A8.04 8.04 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5M13.5 1c-.753 0-1.429.333-1.887.86a8.04 8.04 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1"/></svg>
                            <p class="text-danger">Diskon {{ $event->discount_percentage }}%</p>
                        </div>
                        <small class="diskon">{{ $event->discount_percentage }}% OFF</small>
                    </div>
                @else
                    <h4 class="price-text">@if((int)$event->price===0) FREE @else Rp{{ number_format($event->price,0,',','.') }} @endif</h4>
                @endif
                <hr>
                <div class="info-box">
                    <div class="date">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#A1A5B3" class="ikon bi bi-calendar2-date" viewBox="0 0 16 16"><path d="M6.445 12.688V7.354h-.633A13 13 0 0 0 4.5 8.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z"/><path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z"/></svg>
                        <p class="date-judul">Tanggal</p>
                        <p class="date-text">{{ $event->event_date?->format('d F Y') ?? '-' }}</p>
                    </div>
                    <div class="time">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#A1A5B3" class="ikon bi bi-clock" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0"/></svg>
                        <p class="time-judul">Waktu</p>
                        <p class="time-text">{{ $event->event_time?->format('H:i') ?? '-' }} WIB</p>
                    </div>
                    <div class="location">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#A1A5B3" class="ikon bi bi-geo-alt" viewBox="0 0 16 16"><path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/><path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/></svg>
                        <p class="location-judul">Lokasi</p>
                        <p class="location-text">{{ $event->location ?? 'TBA' }}</p>
                    </div>
                    <div class="bahasa">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#A1A5B3" class="ikon bi bi-journal-text" viewBox="0 0 16 16"><path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/><path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/></svg>
                        <p class="bahasa-judul">Bahasa</p>
                        <p class="bahasa-text">Indonesia</p>
                    </div>
                    <div class="sertifikat">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#A1A5B3" class="ikon bi bi-book" viewBox="0 0 16 16"><path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                        <p class="sertifikat-judul">Sertifikat</p>
                        <p class="sertifikat-text">Gratis</p>
                    </div>
                </div>
                <hr>
                @php $registered = !empty($event->is_registered); @endphp
                @auth
                    <button id="registerBtn" class="enroll {{ $registered ? 'btn-success disabled' : '' }}" data-event-id="{{ $event->id }}">{{ $registered ? 'Anda Terdaftar' : 'Daftar Sekarang' }}</button>
                @endauth
                @guest
                    <a href="{{ route('login',['redirect'=>request()->fullUrl()]) }}" class="enroll">Login untuk Mendaftar</a>
                @endguest
                <button class="save">Save</button>
                <p class="note">Note: all course have 30-days money-back guarantee</p>
            </div>
            <hr>
            <div class="box-benefit">
                <h4>This Event Include</h4>
                <div class="materi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-book" viewBox="0 0 16 16"><path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783"/></svg>
                    <p class="materi-text">Materi pembelajaran Lengkap</p>
                </div>
                <div class="sertif">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-trophy" viewBox="0 0 16 16"><path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z"/></svg>
                    <p class="sertif-text">Sertifikat Kehadiran</p>
                </div>
                <div class="record">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-tv" viewBox="0 0 16 16"><path d="M2.5 13.5A.5.5 0 0 1 3 13h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5M13.991 3l.024.001a1.5 1.5 0 0 1 .538.143.76.76 0 0 1 .302.254c.067.1.145.277.145.602v5.991l-.001.024a1.5 1.5 0 0 1-.143.538.76.76 0 0 1-.254.302c-.1.067-.277.145-.602.145H2.009l-.024-.001a1.5 1.5 0 0 1-.538-.143.76.76 0 0 1-.302-.254C1.078 10.502 1 10.325 1 10V4.009l.001-.024a1.5 1.5 0 0 1 .143-.538.76.76 0 0 1 .254-.302C1.498 3.078 1.675 3 2 3zM14 2H2C0 2 0 4 0 4v6c0 2 2 2 2 2h12c2 0 2-2 2-2V4c0-2-2-2-2-2"/></svg>
                    <p class="record-text">Rekaman Tersedia</p>
                </div>
                <div class="online">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-layers" viewBox="0 0 16 16"><path d="M8.235 1.559a.5.5 0 0 0-.47 0l-7.5 4a.5.5 0 0 0 0 .882L3.188 8 .264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l2.922-1.559a.5.5 0 0 0 0-.882zm3.515 7.008L14.438 10 8 13.433 1.562 10 4.25 8.567l3.515 1.874a.5.5 0 0 0 .47 0zM8 9.433 1.562 6 8 2.567 14.438 6z"/></svg>
                    <p class="online-text">100% Online Course</p>
                </div>
            </div>
            <hr>
            <div class="sponsor-box">
                <h4 class="mb-3">Sponsor & Partner</h4>
                <p>Ko+Lab</p>
                <p>Fakultas Ilmu Terapan</p>
                <p>Telkom University</p>
            </div>
            <hr>
            <div class="share-box mt-4">
                <p class="fw-semibold">Share this event:</p>
                <div class="box-copy d-flex gap-3 align-items-center flex-wrap">
                    <button class="btn btn-outline-secondary btn-sm" onclick="copyLink()"><i class="bi bi-clipboard"></i> Copy link</button>
                    <a href="#" class="text-dark fs-6"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-dark fs-6"><i class="bi bi-twitter"></i></a>
                    <a href="mailto:?subject={{ urlencode($event->title) }}" class="text-dark fs-6"><i class="bi bi-envelope"></i></a>
                    <a href="https://wa.me/?text={{ urlencode($event->title.' '.request()->fullUrl()) }}" target="_blank" class="text-dark fs-6"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        /* Shrink event main image (previously full width). Adjust as needed */
        .container-detail .kiri .event-img {
            width:100%;
            max-width:520px; /* limit width on large screens */
            max-height:320px; /* cap vertical size */
            object-fit:cover;
            border-radius:18px;
            display:block;
            margin:0 auto 28px; /* center with spacing below */
            box-shadow:0 8px 24px -8px rgba(0,0,0,.18), 0 2px 6px -2px rgba(0,0,0,.12);
            transition:box-shadow .25s, transform .3s;
        }
        .container-detail .kiri .event-img:hover {transform:translateY(-3px); box-shadow:0 14px 32px -10px rgba(0,0,0,.22),0 3px 10px -3px rgba(0,0,0,.16);}        
        @media (max-width:768px){
            .container-detail .kiri .event-img {max-width:100%; max-height:240px; margin-bottom:22px;}
        }
    </style>
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
                btn.disabled = true;
                fetch(`/events/${btn.dataset.eventId}/register`, {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                    body: JSON.stringify({})
                }).then(r=>r.json()).then(data=>{
                    if(data.status==='ok' || data.status==='already'){
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-success');
                        btn.textContent = 'Anda Terdaftar';
                    } else { alert(data.message || 'Gagal mendaftar'); btn.disabled=false; }
                }).catch(()=>{ alert('Terjadi kesalahan'); btn.disabled=false; });
            });
        }
        @endauth
    </script>
</body>
</html>
@include('partials.footer-before-login')
