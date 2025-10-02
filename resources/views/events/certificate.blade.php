@include('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $event->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>
        body{background:#f1f5f9;font-family:'Poppins',sans-serif;}
        .cert-container{max-width:1000px;margin:90px auto 50px;padding:40px 46px;background:#fff;box-shadow:0 8px 30px -10px rgba(0,0,0,.15);border-radius:26px;}
        .cert-header{text-align:center;margin-bottom:34px;}
        .cert-header h1{font-size:42px;font-weight:700;letter-spacing:1px;margin-bottom:6px;}
        .cert-divider{height:4px;background:linear-gradient(90deg,#535088,#f4c430);width:240px;margin:14px auto 0;border-radius:4px;}
        .cert-body{margin-top:20px;text-align:center;}
        .cert-body h2{font-size:28px;font-weight:600;margin:18px 0 6px;}
        .cert-body p.lead{font-size:16px;color:#475569;margin-bottom:12px;}
        .cert-meta{display:flex;justify-content:center;flex-wrap:wrap;gap:22px;margin-top:30px;}
        .meta-box{background:#f8fafc;border:1px solid #e2e8f0;padding:14px 20px;border-radius:14px;min-width:200px;text-align:left;}
        .meta-box h6{font-size:11px;font-weight:600;color:#64748b;margin:0 0 4px;letter-spacing:.7px;text-transform:uppercase;}
        .meta-box p{margin:0;font-weight:500;color:#0f172a;}
        .actions{margin-top:38px;display:flex;flex-wrap:wrap;gap:14px;justify-content:center;}
        .actions a{padding:12px 22px;font-weight:600;text-decoration:none;border-radius:14px;}
        .btn-back{background:#334155;color:#fff;}
        .btn-back:hover{background:#1e293b;color:#f4d24b;}
        .btn-download{background:#f4c430;color:#000;}
        .btn-download:hover{filter:brightness(.9);}
        .badge-number{display:inline-block;margin-top:20px;background:#535088;color:#f4d24b;padding:8px 18px;font-weight:600;border-radius:40px;letter-spacing:1px;}
        @media(max-width:768px){
            .cert-container{padding:28px 22px;}
            .cert-header h1{font-size:34px;}
            .cert-body h2{font-size:24px;}
        }
    </style>
</head>
<body>
    <div class="link-box mb-3" style="margin-top:80px;">
        <a href="{{ route('dashboard') }}">Home</a><p>/</p>
        <a href="{{ route('events.index') }}">Event</a><p>/</p>
        <a href="{{ route('events.ticket',$event) }}">Ticket</a><p>/</p>
        <a class="active" href="#">Certificate</a>
    </div>
    <div class="cert-container">
        <div class="cert-header position-relative">
            <h1>Sertifikat Kehadiran</h1>
            <div class="cert-divider"></div>
            <div class="badge-number">{{ $certificateNumber }}</div>
            @if(!$certificateReady)
                <div style="position:absolute;top:12px;left:24px;background:#f59e0b;color:#1e293b;font-size:12px;font-weight:600;padding:6px 14px;border-radius:30px;">PREVIEW MODE</div>
            @endif
            @if(!$certificateReady)
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-18deg);font-size:68px;font-weight:700;color:rgba(83,80,136,0.09);pointer-events:none;white-space:nowrap;">PREVIEW</div>
            @endif
        </div>
        <div class="cert-body">
            <p class="lead mb-1">Diberikan kepada</p>
            <h2>{{ strtoupper($user->name) }}</h2>
            <p class="lead">Sebagai peserta yang telah mengikuti event:</p>
            <h3 style="font-size:22px;font-weight:600;color:#111;">“{{ $event->title }}”</h3>
            <p class="lead mt-3">Dikeluarkan pada: <strong>{{ $issuedAt->format('d F Y') }}</strong></p>
        </div>
        <div class="cert-meta">
            <div class="meta-box">
                <h6>Tanggal Event</h6>
                <p>{{ $event->event_date?->format('d F Y') ?? '-' }}</p>
            </div>
            <div class="meta-box">
                <h6>Waktu</h6>
                <p>{{ $event->event_time?->format('H:i') ?? '-' }} WIB</p>
            </div>
            <div class="meta-box">
                <h6>Lokasi</h6>
                <p>{{ $event->location ?? 'Online' }}</p>
            </div>
            <div class="meta-box">
                <h6>Peserta</h6>
                <p>{{ $user->name }}</p>
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('events.ticket',$event) }}" class="btn-back">Kembali</a>
            @if($certificateReady)
                <a href="{{ route('certificates.download',[$event,$registration]) }}" class="btn-download">Download PDF</a>
            @else
                <a href="#" class="btn-download" style="pointer-events:none;opacity:.55;">Belum Bisa Download (H+4)</a>
            @endif
            @if(app()->environment('local'))
                <a href="{{ route('certificates.download',[$event,$registration]) }}?inline=1&force=1" class="btn-download" style="background:#535088;color:#f4d24b;">Lihat PDF (Inline)</a>
            @endif
        </div>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>
@include('partials.footer-before-login')
