@include('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket / Kode Registrasi - {{ $event->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body{background:#F8FAFC;font-family:'Poppins',sans-serif;}
        /* Samakan top spacing dengan detail page (breadcrumb margin-top:80px) */
        .ticket-wrapper{max-width:880px;margin:20px auto 60px;padding:32px 40px;border-radius:24px;background:#fff;box-shadow:0 12px 40px -18px rgba(0,0,0,.2),0 4px 12px -4px rgba(0,0,0,.08);}        
        .code-box{background:#1e293b;color:#f4d24b;font-weight:600;font-size:26px;letter-spacing:3px;padding:16px 24px;border-radius:16px;display:inline-block;box-shadow:0 6px 18px -6px rgba(0,0,0,.35);}        
        .badge-status{background:#16a34a;color:#fff;border-radius:30px;padding:6px 18px;font-size:12px;font-weight:600;letter-spacing:.5px;}
        .meta-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:18px;margin-top:28px;}
        .meta-item{background:#f1f5f9;border:1px solid #e2e8f0;padding:14px 16px;border-radius:14px;}
        .meta-item h6{font-size:11px;text-transform:uppercase;letter-spacing:.8px;color:#64748b;margin-bottom:4px;font-weight:600;}
        .meta-item p{margin:0;font-weight:500;color:#0f172a;}
        .actions{margin-top:34px;display:flex;flex-wrap:wrap;gap:14px;}
        .actions a,.actions button{border:none;border-radius:14px;padding:12px 22px;font-weight:600;display:inline-flex;align-items:center;gap:8px;font-size:14px;text-decoration:none;}
        .btn-back{background:#334155;color:#fff;}
        .btn-back:hover{background:#1e293b;color:#f4d24b;}
        .btn-copy{background:#535088;color:#f4d24b;}
        .btn-copy:hover{background:#474273;color:#fff;}
        @media (max-width:768px){
            .ticket-wrapper{padding:28px 22px;}
            .code-box{font-size:22px;}
        }
    </style>
</head>
<body>
    <!-- Breadcrumb mirip detail.blade -->
    <div class="link-box mb-3" style="margin-top:80px;">
        <a href="{{ route('dashboard') }}">Home</a>
        <p>/</p>
        <a href="{{ route('events.index') }}">Event</a>
        <p>/</p>
        <a class="active" href="{{ route('events.ticket',$event) }}">Ticket</a>
    </div>
    <div class="ticket-wrapper">
        <h2 class="mb-2">Tiket Event</h2>
        <p class="text-muted mb-4">Simpan kode registrasi ini untuk validasi / sertifikat.</p>
        @php
            $certificateReady = false;
            if($event->event_date){
                // Sertifikat siap H+4 setelah tanggal event
                $certificateReady = now()->greaterThanOrEqualTo($event->event_date->copy()->addDays(4));
            }
        @endphp
        <div class="d-flex align-items-center gap-3 flex-wrap mb-4">
            <div class="code-box" id="regCode">{{ $registration->registration_code }}</div>
            <span class="badge-status">Terdaftar</span>
            <button class="btn-copy" id="copyBtn">Salin Kode</button>
        </div>
        <h4 class="mb-1">{{ $event->title }}</h4>
        <p class="text-muted mb-3">Oleh IdSpora</p>
        <div class="meta-grid">
            <div class="meta-item">
                <h6>Tanggal</h6>
                <p>{{ $event->event_date?->format('d F Y') }}</p>
            </div>
            <div class="meta-item">
                <h6>Waktu</h6>
                <p>{{ $event->event_time?->format('H:i') }} WIB</p>
            </div>
            <div class="meta-item">
                <h6>Lokasi</h6>
                <p>{{ $event->location }}</p>
            </div>
            <div class="meta-item">
                <h6>Status</h6>
                <p>{{ ucfirst($registration->status) }}</p>
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('events.show',$event) }}" class="btn-back">Kembali ke Detail</a>
            <a href="{{ route('dashboard') }}" class="btn-back" style="background:#475569;">Dashboard</a>
            @if(Route::has('certificates.show'))
                <a href="{{ route('certificates.show',[$event,$registration]) }}" class="btn-cert" style="background:#535088;color:#f4d24b;">@if($certificateReady) Lihat / Unduh Sertifikat @else Preview Sertifikat @endif</a>
                @if(app()->environment('local'))
                    <a href="{{ route('certificates.download',[$event,$registration]) }}?inline=1&force=1" class="btn-cert" style="background:#334155;color:#f4d24b;">PDF Inline</a>
                @endif
            @endif
            <button type="button" id="printBtn" class="btn-print" style="background:#f4c430;color:#000;">Print Tiket</button>
        </div>
    </div>
    <script>
        document.getElementById('copyBtn').addEventListener('click',()=>{
            const code = document.getElementById('regCode').innerText.trim();
            navigator.clipboard.writeText(code).then(()=>{
                alert('Kode disalin: '+code);
            }).catch(()=>alert('Gagal menyalin kode'));
        });
        const printBtn = document.getElementById('printBtn');
        if(printBtn){ printBtn.addEventListener('click',()=> window.print()); }
    </script>
    <style>
        .actions .btn-cert, .actions .btn-print { text-decoration:none; }
        .actions .btn-print:hover { filter:brightness(.9); }
        .actions .btn-cert:hover { filter:brightness(1.1); }
        @media print {
            body { background:#fff !important; }
            .link-box, .actions #printBtn, .actions #copyBtn, .actions .btn-back { display:none !important; }
            .ticket-wrapper { box-shadow:none !important; margin:0 !important; max-width:100% !important; padding:20px 10px !important; }
        }
    </style>
</body>
</html>
@include('partials.footer-before-login')