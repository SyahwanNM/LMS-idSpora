<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Reset Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { margin:0; padding:0; min-height:100vh; font-family:"Poppins"; background:radial-gradient(circle,#51376C 0%,#2E2050 100%); color:white; display:flex; align-items:center; justify-content:center; overflow-x:hidden; }
        .main-container { display:flex; width:100%; max-width:1000px; padding:40px; min-height:100vh; align-items:center; justify-content:center; flex-wrap:wrap; }
        .back { position:absolute; top:30px; left:30px; width:25px; height:auto; cursor:pointer; z-index:10; }
        .kiri { flex:1; display:flex; align-items:center; justify-content:center; min-width:300px; }
        .logo { width:100%; max-width:400px; height:auto; margin-right:50px; }
        .kanan { flex:1; width:100%; max-width:400px; display:flex; flex-direction:column; justify-content:center; padding:20px 0; }
        .kanan h3 { font-weight:bold; margin-bottom:20px; }
        .form-control { border:1px solid rgba(255,255,255,0.4); border-radius:8px; background-color:#AFB6E54D; padding:10px; width:100%; color:white; }
        .form-control:focus { border-color:#f4a442; box-shadow:0 0 0 2px rgba(244,164,66,0.3); background-color:#AFB6E54D; color:white; }
        .btn-register { background-color:#f4a442; border:none; color:white; font-weight:bold; width:100%; padding:12px; border-radius:10px; margin-top:15px; }
        .btn-register:hover { background-color:#e68a00; }
        .cooldown { color:rgba(255,255,255,0.7); font-size:13px; margin-top:8px; display:none; }
        @media(max-width:991px){ .main-container{flex-direction:column;padding:60px 20px 40px;min-height:auto;} .logo{margin-right:0;margin-bottom:30px;max-width:300px;} .kiri{min-width:auto;} .kanan{max-width:100%;} .back{top:20px;left:20px;} }
    </style>
</head>
<body>
    <a href="{{ route('forgot-password') }}" title="Kembali">
        <img class="back" src="{{ asset('aset/back.png') }}" alt="Kembali">
    </a>
    <div class="main-container">
        <div class="kiri">
            <img class="logo" src="{{ asset('aset/logo.png') }}" alt="">
        </div>
        <div class="kanan">
            <h3>Verifikasi</h3>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('verifikasi.verify') }}" method="post">
                @csrf
                <div class="mb-3">
                    <h6>Masukkan Kode Verifikasi</h6>
                    <input type="text" name="verification_code" class="form-control"
                           value="{{ old('verification_code') }}" placeholder="000000" maxlength="6" required>
                    <small class="text-white">Kode verifikasi telah dikirim ke email Anda</small>
                </div>
                <button type="submit" class="btn-register">Verifikasi</button>
            </form>

            <div style="margin-top:15px; text-align:center; font-size:14px;">
                <form id="resendForm" action="{{ route('forgot-password.resend') }}" method="post" style="display:inline;">
                    @csrf
                    <button id="resendBtn" type="submit" class="btn btn-link p-0"
                            style="color:#f4a442; font-weight:bold; text-decoration:none;">
                        Kirim Ulang Kode
                    </button>
                </form>
                <div class="cooldown" id="cooldownText">Tunggu <span id="sec">60</span> detik untuk kirim ulang</div>
            </div>
        </div>
    </div>

    <script>
    (function(){
        const resendBtn = document.getElementById('resendBtn');
        const cd = document.getElementById('cooldownText');
        const sec = document.getElementById('sec');
        const KEY = 'forgotResendUntil';

        function startCountdown(msLeft) {
            let s = Math.max(0, Math.ceil(msLeft / 1000));
            resendBtn.disabled = true;
            resendBtn.style.opacity = '0.5';
            cd.style.display = 'block';
            sec.textContent = s;
            const timer = setInterval(() => {
                s--;
                sec.textContent = s;
                if (s <= 0) {
                    clearInterval(timer);
                    resendBtn.disabled = false;
                    resendBtn.style.opacity = '1';
                    cd.style.display = 'none';
                    localStorage.removeItem(KEY);
                }
            }, 1000);
        }

        // Check existing cooldown on page load
        try {
            const until = parseInt(localStorage.getItem(KEY) || '0', 10);
            const msLeft = until - Date.now();
            if (msLeft > 0) startCountdown(msLeft);
        } catch {}

        // On resend click, start 60s cooldown
        const form = document.getElementById('resendForm');
        if (form && resendBtn) {
            form.addEventListener('submit', function() {
                const until = Date.now() + 60000;
                try { localStorage.setItem(KEY, String(until)); } catch {}
                startCountdown(60000);
            });
        }
    })();
    </script>
</body>
</html>
