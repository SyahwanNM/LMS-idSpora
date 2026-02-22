<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Server Maintenance</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;background:#f8fafc;color:#0f172a}
        .wrap{max-width:720px;padding:28px;text-align:center}
        h1{font-size:28px;margin-bottom:8px}
        p{color:#475569}
        .logo{margin-bottom:18px}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo"><img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="Logo" style="max-width:140px;opacity:0.9"></div>
        <h1>Website sedang dalam pemeliharaan</h1>
        <p>{{ $message ?? 'Maaf, saat ini layanan sedang kami hentikan sementara untuk pemeliharaan. Silakan coba lagi nanti.' }}</p>
        <p class="text-muted"><small>Jika Anda admin, masuk ke panel admin untuk menonaktifkan mode maintenance.</small></p>
    </div>
</body>
</html>
