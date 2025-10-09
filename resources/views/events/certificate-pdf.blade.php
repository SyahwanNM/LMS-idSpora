<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $event->title }}</title>
    <style>
        @page { margin: 40px; }
        body { font-family: DejaVu Sans, sans-serif; color:#1e293b; }
        .wrap { border:6px solid #535088; padding:40px 50px; position:relative; height:100%; }
        h1 { text-align:center; font-size:46px; margin:0 0 10px; letter-spacing:1px; }
        .divider { height:5px; background:linear-gradient(90deg,#f4c430,#535088); margin:0 auto 30px; width:320px; }
        h2 { text-align:center; font-size:30px; margin:10px 0 0; }
        p.lead { text-align:center; font-size:16px; margin:8px 0; }
        .event-title { font-size:22px; font-weight:600; text-align:center; margin:18px 0 10px; }
        .meta { display:flex; justify-content:space-between; margin-top:35px; font-size:13px; }
        .meta .col { width:24%; }
        .meta h6 { font-size:11px; margin:0 0 4px; text-transform:uppercase; letter-spacing:.7px; color:#475569; }
        .meta p { margin:0; font-weight:600; }
        .cert-no { position:absolute; top:18px; right:28px; font-size:12px; font-weight:600; background:#535088; color:#f4d24b; padding:6px 14px; border-radius:20px; }
        .footer { position:absolute; bottom:30px; left:0; width:100%; text-align:center; font-size:12px; color:#64748b; }
        .sign-block { margin-top:50px; text-align:center; }
        .sign-line { margin:40px auto 6px; width:240px; border-bottom:1px solid #334155; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="cert-no">{{ $certificateNumber }}</div>
        <h1>SERTIFIKAT</h1>
        <div class="divider"></div>
        <p class="lead">Diberikan kepada</p>
        <h2>{{ strtoupper($user->name) }}</h2>
        <p class="lead">Atas partisipasi pada event</p>
        <div class="event-title">“{{ $event->title }}”</div>
        <p class="lead">Dikeluarkan pada {{ $issuedAt->format('d F Y') }}</p>
        <div class="meta">
            <div class="col">
                <h6>Tanggal Event</h6>
                <p>{{ $event->event_date?->format('d F Y') ?? '-' }}</p>
            </div>
            <div class="col">
                <h6>Waktu</h6>
                <p>{{ $event->event_time?->format('H:i') ?? '-' }} WIB</p>
            </div>
            <div class="col">
                <h6>Lokasi</h6>
                <p>{{ $event->location ?? 'Online' }}</p>
            </div>
            <div class="col">
                <h6>Peserta</h6>
                <p>{{ $user->name }}</p>
            </div>
        </div>
        <div class="sign-block">
            <div class="sign-line"></div>
            <div style="font-size:13px;font-weight:600;">IdSpora</div>
            <div style="font-size:11px;color:#475569;">Penyelenggara</div>
        </div>
        <div class="footer">Sertifikat ini diterbitkan secara digital oleh sistem IdSpora</div>
    </div>
</body>
</html>