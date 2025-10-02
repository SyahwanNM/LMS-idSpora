@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - {{ isset($event)? $event->title : 'Event' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #F8FAFC;
        }
    </style>
</head>

<body>
    <div class="link-box mb-3" style="margin-top:80px;">
        <a href="{{ route('dashboard') }}">Home</a>
        <p>/</p>
        <a href="{{ route('events.index') }}">Event</a>
        <p>/</p>
        @if(isset($event))
            <a href="{{ route('events.show',$event) }}">{{ Str::limit($event->title,40) }}</a>
            <p>/</p>
        @endif
        <a class="active" href="#">Payment</a>
    </div>
    <div class="box-payment">
        <div class="kiri-payment">
            <h3>Data Peserta</h3>
            <p class="judul-input">Email</p>
            <input class="form" type="email" value="{{ auth()->user()->email ?? '' }}" readonly>
            <p class="judul-input">Nama Lengkap</p>
            <div class="warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#EC0606" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                </svg>
                <p class="warning-text">Nama akan digunakan di sertifikat</p>
            </div>
            <input class="form" type="text" value="{{ auth()->user()->name ?? '' }}" placeholder="Nama sesuai sertifikat">
            <div class="form-group">
                <p class="judul-input">No Whatsapp</p>
                <div class="wa-input">
                    <select>
                        <option>Kode Dial</option>
                        <option value="+62">+62</option>
                        <option value="+60">+60</option>
                        <option value="+65">+65</option>
                    </select>
                    <input class="no-wa" type="text" placeholder="No Whatsapp">
                </div>
            </div>
        </div>

        <div class="ticket">
            <div class="ticket-header">Order Detail</div>
            <div class="ticket-content"> <img src="{{ asset('aset/event.png') }}" alt="Event">
                <div class="info">
                    <h4>{{ isset($event)? $event->title : 'Event Title' }}</h4>
                    <p>{{ isset($event)? 'IdSpora' : '' }}</p>
                    @php
                        $isFree = isset($event) ? ((int)$event->price === 0) : false;
                        $hasDiscount = isset($event) ? $event->hasDiscount() : false;
                        $finalPrice = isset($event) ? ($hasDiscount ? $event->discounted_price : $event->price) : 0;
                    @endphp
                    <div class="price">
                        @if($isFree)
                            <span class="badge-diskon">Diskon 100%</span>
                            <span class="ms-2 fw-semibold" style="color:#16a34a;">FREE</span>
                        @else
                            @if($hasDiscount)
                                <span class="text-decoration-line-through text-muted me-2">Rp{{ number_format($event->price,0,',','.') }}</span>
                                <span class="fw-semibold">Rp{{ number_format($finalPrice,0,',','.') }}</span>
                                <span class="badge-diskon ms-2">Diskon {{ $event->discount_percentage }}%</span>
                            @else
                                <span class="fw-semibold">Rp{{ number_format($finalPrice,0,',','.') }}</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="ticket-divider"></div>
            <div class="ticket-footer">
                <div>
                    <h5>Total</h5>
                    <p class="m-0">
                        @if(isset($event))
                            @if($isFree)
                                FREE
                            @else
                                Rp{{ number_format($finalPrice,0,',','.') }}
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div class="icon">📄</div>
            </div>
            <form method="POST" action="#">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">
                <button type="submit" class="btn-pay mt-2">@if(isset($event) && $isFree) Daftar Gratis @else Bayar @endif</button>
            </form>
        </div>
    </div>
</body>

</html>