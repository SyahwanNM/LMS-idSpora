<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: "Poppins";
            background: radial-gradient(circle, #51376C 0%, #2E2050 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            padding: 40px;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
        }

        .back {
            position: fixed;
            top: 50px;
            left: 70px;
            width: 20px;
            height: auto;
            cursor: pointer;
        }

        .kiri {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            font-weight: bold;
        }

        .logo {
            width: 400px;
            height: auto;
            margin-right: 100px;
        }

        .kanan {
            flex: 1;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kanan h3 {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .form-control {
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            background-color: #AFB6E54D;
            padding: 10px;
            width: 100%;
            color: white;
        }

        .form-control:focus {
            border-color: #f4a442;
            box-shadow: 0 0 0 2px rgba(244, 164, 66, 0.3);
            background-color: #AFB6E54D;
        }

        .btn-register {
            background-color: #f4a442;
            border: none;
            color: white;
            font-weight: bold;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .btn-register:hover {
            background-color: #e68a00;
        }

        .garis {
            display: flex;
            align-items: center;
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            margin: 15px 0;
        }

        .garis::before,
        .garis::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.3);
        }

        .garis:not(:empty)::before {
            margin-right: .75em;
        }

        .garis:not(:empty)::after {
            margin-left: .75em;
        }

        .btn-google {
            background-color: white;
            color: #444;
            font-weight: 600;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-google:hover {
            background-color: #f0f0f0;
        }

        .kirim-ulang {
            margin-top: 8px;
            text-align: right;
            font-size: 13px;
        }

        .kirim-ulang a {
            color: white;
            font-weight: 500;
            text-decoration: none;
        }

        .kirim-ulang a:hover {
            text-decoration: underline;
        }

        .kirim-ulang-teks:hover {
            color: #f4a442;
            text-decoration: none;
        }

        .text-login {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }

        .text-login a {
            color: #f4a442;
            font-weight: bold;
            text-decoration: none;
        }

        .text-login a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ff6b6b;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #51cf66;
        }

        .form-check-input:checked {
            background-color: #f4a442;
            border-color: #f4a442;
        }

        .form-check-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .hint {
            color: #cbd5e1;
            font-size: 13px;
        }

        .cooldown {
            color: #cbd5e1;
            font-size: 13px;
            display: none;
        }
    </style>
</head>

<body>
    <a href="{{ route('login') }}"><img class="back" src="{{ asset('aset/back.png') }}" alt="Kembali"></a>
    <div class="main-container">
        <div class="kiri">
            <img class="logo" src="{{ asset('aset/logo.png') }}" alt="Logo">
        </div>

        <div class="kanan">
            <h3>Verifikasi OTP</h3>

            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <p class="hint">Kami telah mengirimkan kode ke email: <b>{{ $maskedEmail ?? '' }}</b>. Kode berlaku 10 menit.</p>

            <form action="{{ route('login.otp.verify') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <h6>Kode OTP</h6>
                    <input type="text" name="code" class="form-control" inputmode="numeric" pattern="[0-9]{6}"
                        maxlength="6" placeholder="Masukkan 6 digit" required>
                </div>
                <button type="submit" class="btn-register">Verifikasi</button>
            </form>

            <form id="resendForm" action="{{ route('login.otp.resend') }}" method="POST" class="mt-2">
                @csrf
                <div class="kirim-ulang">
                    <button type="submit" id="resendBtn" class="btn btn-link p-0 kirim-ulang-teks">Kirim ulang kode?</button>
                </div>
                <div class="cooldown" id="cooldownText">Tunggu <span id="sec">60</span> detik untuk kirim ulang</div>
                <div class="hint mt-1" id="resendStatus" style="display:none;"></div>
            </form>
        </div>
    </div>

    <script>
        // Resend cooldown (60s) with localStorage persistence and fetch-based submit
        (function(){
            const form = document.getElementById('resendForm');
            const resendBtn = document.getElementById('resendBtn');
            const cd = document.getElementById('cooldownText');
            const sec = document.getElementById('sec');
            const statusBox = document.getElementById('resendStatus');
            const KEY = 'otpResendUntil';

            function startCountdown(msLeft){
                let s = Math.max(0, Math.ceil(msLeft/1000));
                resendBtn.disabled = true;
                cd.style.display = 'block';
                sec.textContent = s;
                const timer = setInterval(() => {
                    s -= 1;
                    sec.textContent = s;
                    if (s <= 0) {
                        clearInterval(timer);
                        resendBtn.disabled = false;
                        cd.style.display = 'none';
                        statusBox.style.display = 'none';
                        localStorage.removeItem(KEY);
                    }
                }, 1000);
            }

            // Initialize from localStorage
            try {
                const until = parseInt(localStorage.getItem(KEY) || '0', 10);
                const now = Date.now();
                if (until && until > now) {
                    startCountdown(until - now);
                }
            } catch {}

            if (form && resendBtn) {
                form.addEventListener('submit', function(e){
                    e.preventDefault();
                    // If still in cooldown, ignore
                    const now = Date.now();
                    const until = parseInt(localStorage.getItem(KEY) || '0', 10);
                    if (until && until > now) return;

                    // Set cooldown for 60s
                    const newUntil = now + 60000;
                    try { localStorage.setItem(KEY, String(newUntil)); } catch {}
                    startCountdown(60000);

                    // Send POST via fetch without page reload
                    const url = form.action;
                    const tokenInput = form.querySelector('input[name="_token"]');
                    const token = tokenInput ? tokenInput.value : '';
                    statusBox.style.display = 'block';
                    statusBox.textContent = 'Mengirim ulang kode...';

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
                        },
                        credentials: 'same-origin',
                        body: new URLSearchParams({ _token: token })
                    }).then(() => {
                        statusBox.textContent = 'Kode OTP baru telah dikirim.';
                    }).catch(() => {
                        statusBox.textContent = 'Gagal mengirim ulang kode OTP. Coba lagi nanti.';
                    });
                });
            }
        })();
    </script>
</body>

</html>