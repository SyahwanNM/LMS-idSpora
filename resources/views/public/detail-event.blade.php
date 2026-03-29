<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - idSpora</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .container-detail {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            max-width: 1200px;
            margin: 85px auto 50px;
            padding: 0 20px;
        }
        .link-box {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #64748b;
        }
        .link-box a { 
            text-decoration: none; 
            color: #6d28d9; 
            font-weight: 500;
        }
        .link-box a:hover { color: #4c1d95; text-decoration: underline; }
        .link-box span.sep { color: #cbd5e1; }
        .link-box .active { color: #1e1b4b; font-weight: 600; }
        
        .kiri img.event-img {
            width: 100%;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .hover-up { transition: transform 0.2s; }
        .hover-up:hover { transform: translateY(-3px); }
        
        @media (max-width: 992px) {
            .container-detail { grid-template-columns: 1fr; margin-top: 40px; padding: 0 15px !important; }
            .link-box { margin-top: 80px; margin-bottom: 10px; padding: 0 15px !important; }
            .kanan { max-width: 100% !important; }
        }
    </style>
</head>

<body style="padding-top: 0;">
    @include("partials.navbar-after-login")
    <div class="link-box">
        <a href="{{ route('dashboard') }}">Home</a>
        <span class="sep">/</span>
        <a href="{{ route('events.index') }}">Event</a>
        <span class="sep">/</span>
        <span class="active">{{ $event->title }}</span>
    </div>

    <div class="container-detail">
        <div class="kiri">
            <img src="{{ $event->image_url ?? asset('aset/event.png') }}"
                class="event-img" alt="{{ $event->title }}" onerror="this.src='{{ asset('aset/event.png') }}'">
            <div class="profile-box">
                <img src="{{ asset('aset/profile.png') }}" alt="profile" class="rounded-circle" width="60" height="60">
                <div>
                    <h5 class="mb-1">{{ $event->title }}</h5>
                    <h6 class="text-muted">Created by <span class="black">idSpora</span></h6>
                </div>

            </div>
            <div class="overview">
                <h3 class="mb-3">Overview</h3>
                <div>{!! $event->description !!}</div>
            </div>
            <div class="terms-condition">
                <h3 class="mb-3">Terms and Condition</h3>
                <div>{!! $event->terms_and_condition ?? 'Aturan standar berlaku.' !!}</div>
            </div>
        </div>
        <div class="kanan">
                <div class="price shadow-sm p-4 bg-white rounded-4 border">
                    <div class="mb-3">
                        @if($event->price > 0)
                            @if($event->discounted_price && $event->discounted_price < $event->price)
                                <span class="text-muted text-decoration-line-through d-block mb-1">Rp.{{ number_format($event->price, 0, ',', '.') }}</span>
                                <h3 class="price-text fw-bold text-primary mb-0">Rp.{{ number_format($event->discounted_price, 0, ',', '.') }}</h3>
                                <div class="mt-2">
                                    <span class="badge bg-danger rounded-pill">{{ round((($event->price - $event->discounted_price) / $event->price) * 100) }}% OFF</span>
                                </div>
                            @else
                                <h3 class="price-text fw-bold text-primary mb-0">Rp.{{ number_format($event->price, 0, ',', '.') }}</h3>
                            @endif
                        @else
                            <h3 class="price-text fw-bold text-success mb-0">GRATIS</h3>
                        @endif
                    </div>
                
                    <hr class="my-3 opacity-10">
                    
                    <div class="info-box space-y-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar-event me-3 text-secondary"></i>
                            <div>
                                <small class="text-muted d-block">Tanggal</small>
                                <span class="fw-medium">{{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-clock me-3 text-secondary"></i>
                            <div>
                                <small class="text-muted d-block">Waktu</small>
                                <span class="fw-medium">{{ $event->event_time ?? '19:30 WIB - Selesai' }}</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-geo-alt me-3 text-secondary"></i>
                            <div>
                                <small class="text-muted d-block">Lokasi</small>
                                <span class="fw-medium">{{ $event->location }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        @if($event->price > 0)
                            <a href="{{ route('payment', $event) }}" class="btn btn-warning w-100 py-3 fw-bold rounded-3 shadow-sm mb-3 transition-transform hover-up">
                                Enroll Now
                            </a>
                        @else
                            <form action="{{ route('events.register', $event) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 py-3 fw-bold rounded-3 shadow-sm mb-3 transition-transform hover-up">
                                    Join for Free
                                </button>
                            </form>
                        @endif
                        
                        @php
                            $isSaved = auth()->check() && \DB::table('user_saved_events')
                                ->where('user_id', auth()->id())
                                ->where('event_id', $event->id)
                                ->exists();
                        @endphp
                        <button type="button" id="saveEventBtn" data-save-url="{{ route('events.save', $event) }}" 
                                class="btn {{ $isSaved ? 'btn-danger' : 'btn-outline-secondary' }} w-100 py-2 rounded-3">
                            <i class="bi {{ $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }} me-2"></i>
                            {{ $isSaved ? 'Saved' : 'Save Event' }}
                        </button>
                    </div>

                    <p class="text-center text-muted small mt-3">Secure payment & guarantee</p>
                </div>

                <p class="note">Note: all course have 30-days money-back guarantee</p>
            </div>
            <hr>
            <div class="box-benefit">
                <h4>This Event Include</h4>
                <div class="materi">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-book" viewBox="0 0 16 16">
                        <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
                    </svg>
                    <p class="materi-text">Materi pembelajaran Lengkap</p>
                </div>
                <div class="sertif">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-trophy" viewBox="0 0 16 16">
                        <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z" />
                    </svg>
                    <p class="sertif-text">Sertifikat Kehadiran</p>
                </div>
                <div class="record">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-tv" viewBox="0 0 16 16">
                        <path d="M2.5 13.5A.5.5 0 0 1 3 13h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5M13.991 3l.024.001a1.5 1.5 0 0 1 .538.143.76.76 0 0 1 .302.254c.067.1.145.277.145.602v5.991l-.001.024a1.5 1.5 0 0 1-.143.538.76.76 0 0 1-.254.302c-.1.067-.277.145-.602.145H2.009l-.024-.001a1.5 1.5 0 0 1-.538-.143.76.76 0 0 1-.302-.254C1.078 10.502 1 10.325 1 10V4.009l.001-.024a1.5 1.5 0 0 1 .143-.538.76.76 0 0 1 .254-.302C1.498 3.078 1.675 3 2 3zM14 2H2C0 2 0 4 0 4v6c0 2 2 2 2 2h12c2 0 2-2 2-2V4c0-2-2-2-2-2" />
                    </svg>
                    <p class="record-text">Rekaman Tersedia</p>
                </div>
                <div class="online">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-layers" viewBox="0 0 16 16">
                        <path d="M8.235 1.559a.5.5 0 0 0-.47 0l-7.5 4a.5.5 0 0 0 0 .882L3.188 8 .264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l2.922-1.559a.5.5 0 0 0 0-.882zm3.515 7.008L14.438 10 8 13.433 1.562 10 4.25 8.567l3.515 1.874a.5.5 0 0 0 .47 0zM8 9.433 1.562 6 8 2.567 14.438 6z" />
                    </svg>
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
                <p class="fw-semibold">Share this course:</p>
                <div class="box-copy">


                    <button class="btn btn-outline-secondary" onclick="copyLink()">
                        <i class="bi bi-clipboard"></i> Copy link
                    </button>

                    <a href="#" class="text-dark fs-5">
                        <i class="bi bi-facebook"></i>
                    </a>

                    <a href="#" class="text-dark fs-5">
                        <i class="bi bi-twitter"></i>
                    </a>

                    <a href="mailto:?subject=Check this course" class="text-dark fs-5">
                        <i class="bi bi-envelope"></i>
                    </a>

                    <a href="https://wa.me/?text=Check this course" target="_blank" class="text-dark fs-5">
                        <i class="bi bi-whatsapp"></i>
                    </a>

                </div>
            </div>

            <!-- Bootstrap Icons -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

        </div>
    </div>
    <script>
        function copyLink() {
            navigator.clipboard.writeText(window.location.href)
                .then(() => {
                    alert("Link berhasil disalin!");
                })
                .catch(() => {
                    alert("Gagal menyalin link");
                });
        }

        document.getElementById('saveEventBtn').addEventListener('click', function() {
            const btn = this;
            const url = btn.getAttribute('data-save-url');
            
            btn.disabled = true;
            btn.style.opacity = '0.7';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.status === 401) {
                    window.location.href = "{{ route('login') }}";
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    if (data.saved) {
                        btn.textContent = 'Saved';
                        btn.classList.remove('btn-outline-secondary');
                        btn.classList.add('btn-danger');
                    } else {
                        btn.textContent = 'Save';
                        btn.classList.remove('btn-danger');
                        btn.classList.add('btn-outline-secondary');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            })
            .finally(() => {
                btn.disabled = false;
                btn.style.opacity = '1';
            });
        });
    </script>
</body>

</html>
@include('partials.footer-after-login')