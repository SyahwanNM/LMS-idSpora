<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: "Poppins";
            background: radial-gradient(circle, #51376C 0%, #2E2050 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
        }

        .main-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            padding: 40px;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .back {
            position: absolute;
            top: 30px;
            left: 30px;
            width: 25px;
            height: auto;
            cursor: pointer;
            z-index: 10;
        }

        .kiri {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 300px;
        }

        .logo {
            width: 100%;
            max-width: 400px;
            height: auto;
            margin-right: 50px;
        }

        .kanan {
            flex: 1;
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 20px 0;
        }

        .kanan h3 {
            font-weight: bold;
            margin-bottom: 20px;
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
            color: white;
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

        @media (max-width: 991px) {
            .main-container {
                flex-direction: column;
                padding: 60px 20px 40px;
                min-height: auto;
            }
            .logo {
                margin-right: 0;
                margin-bottom: 30px;
                max-width: 300px;
            }
            .kiri {
                min-width: auto;
            }
            .kanan {
                max-width: 100%;
            }
            .back {
                top: 20px;
                left: 20px;
            }
        }

        @media (max-width: 576px) {
            .logo {
                max-width: 250px;
            }
            .kanan h3 {
                font-size: 1.5rem;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <a href="{{ route('register') }}" title="Back to Register">
        <img class="back" src="{{ asset('aset/back.png') }}" alt="Back">
    </a>
    <div class="main-container">
        <div class="kiri">
            <img class="logo" src="{{ asset('aset/logo.png') }}" alt="">
        </div>

        <div class="kanan">
            <h3>Verification</h3>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('verifikasi.verify') }}" method="post">
                @csrf
                <div class="mb-3">
                    <h6 class="fw-600">Input Verification Code</h6>
                    <input type="text" name="verification_code" class="form-control text-center fs-4 fw-bold" 
                           value="{{ old('verification_code') }}" 
                           placeholder="000000" maxlength="6" required autofocus>
                    
                    <div class="mt-3 text-center">
                        <div id="otp-timer-container" class="smaller p-2 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10">
                            <span class="text-white opacity-75">Code is valid for:</span>
                            <span id="otp-timer" class="fw-bold text-warning ms-1">10:00</span>
                        </div>
                        <div id="otp-expired-msg" class="smaller p-2 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-20 text-danger fw-bold d-none">
                            The code has expired. Please resend..
                        </div>
                    </div>

                    <small class="text-white opacity-75 d-block mt-3 text-center">
                        A verification code has been sent to your email.
                        @php
                            $regEmail = session('register_verify_email');
                        @endphp
                        @if($regEmail)
                            <br><strong>({{ preg_replace('/(^.).*(@.*$)/', '$1***$2', $regEmail) }})</strong>
                            <input type="hidden" name="register_email" value="{{ $regEmail }}">
                        @endif
                    </small>
                </div>

                <button type="submit" id="verifyBtn" class="btn-register">Verify Account</button>
            </form>
            
            <div class="text-login" style="margin-top: 25px; text-align: center; font-size: 14px;">
                <form id="resendForm" action="{{ route('register.otp.resend') }}" method="post" class="d-inline">
                    @csrf
                    @if(session('register_verify_email'))
                        <input type="hidden" name="register_email" value="{{ session('register_verify_email') }}">
                    @endif
                    <p class="mb-2 opacity-75">Didn't receive the code?</p>
                    <button id="resendBtn" type="submit" class="btn btn-outline-warning rounded-pill px-4 fw-bold smaller">
                        Resend Code
                    </button>
                    <div id="resend-cooldown" class="smaller text-white opacity-50 mt-2 d-none">
                        Wait <span id="resend-sec">60</span> seconds to resend
                    </div>
                    @if(session('resend_count') >= 3)
                        <div class="smaller mt-2 text-warning opacity-75">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            Still not received? Check your <strong>Spam/Junk</strong> folder or contact support.
                        </div>
                    @endif
                </form>
            </div>

            @php
                $otpExpiresAt = session('otp_expires_at') ?? '';
                $errMsg = $errors->first('error') ?? '';
                preg_match('/Wait (\d+) seconds/', $errMsg, $cdMatch);
                $resendCooldownSec = (int)($cdMatch[1] ?? 0);
            @endphp

            <!-- Data injection for JS -->
            <div id="otp-data" 
                 data-expires-at="{{ $otpExpiresAt ?? '' }}" 
                 data-resend-cooldown="{{ $resendCooldownSec ?? 0 }}"
                 style="display:none;"></div>

            <script>
                // ── Read server data from DOM ──
                const dataEl = document.getElementById('otp-data');
                const OTP_EXPIRY_KEY = 'registerOtpExpiresAt';
                const serverExpiresAt = dataEl?.dataset?.expiresAt || '';
                const serverResendCooldown = parseInt(dataEl?.dataset?.resendCooldown || '0', 10);

                // ── OTP validity timer ──
                // Always use server expiry when available — overrides stale localStorage
                let expiresAt;
                if (serverExpiresAt) {
                    // Kurangi 7 menit dari waktu server
                    expiresAt = new Date(serverExpiresAt).getTime() - (7 * 60 * 1000);
                    localStorage.setItem(OTP_EXPIRY_KEY, expiresAt);
                } else {
                    const stored = parseInt(localStorage.getItem(OTP_EXPIRY_KEY) || '0', 10);
                    expiresAt = stored > Date.now() ? stored : (Date.now() + 3 * 60 * 1000);
                }

                const timerDisplay = document.getElementById('otp-timer');
                const timerContainer = document.getElementById('otp-timer-container');
                const expiredMsg = document.getElementById('otp-expired-msg');
                const verifyBtn = document.getElementById('verifyBtn');

                function updateOtpTimer() {
                    const distance = expiresAt - Date.now();
                    if (distance <= 0) {
                        clearInterval(validityInterval);
                        timerContainer.classList.add('d-none');
                        expiredMsg.classList.remove('d-none');
                        if (verifyBtn) { verifyBtn.disabled = true; verifyBtn.style.opacity = '0.5'; }
                        return;
                    }
                    const minutes = Math.floor(distance / 60000);
                    const seconds = Math.floor((distance % 60000) / 1000);
                    timerDisplay.textContent = (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                    timerDisplay.style.color = minutes < 1 ? '#ff4d4d' : '';
                }
                updateOtpTimer();
                const validityInterval = setInterval(updateOtpTimer, 1000);

                // ── Resend cooldown ──
                (function() {
                    const resendBtn = document.getElementById('resendBtn');
                    const resendCd = document.getElementById('resend-cooldown');
                    const resendSec = document.getElementById('resend-sec');
                    const KEY = 'registerResendUntil';

                    // Sync localStorage with server cooldown (server is authoritative)
                    if (serverResendCooldown > 0) {
                        localStorage.setItem(KEY, Date.now() + serverResendCooldown * 1000);
                    }

                    function startResendCountdown(msLeft) {
                        let s = Math.max(0, Math.ceil(msLeft / 1000));
                        resendBtn.disabled = true;
                        resendBtn.style.opacity = '0.5';
                        resendCd.classList.remove('d-none');
                        resendSec.textContent = s;
                        const timer = setInterval(() => {
                            s--;
                            resendSec.textContent = s;
                            if (s <= 0) {
                                clearInterval(timer);
                                resendBtn.disabled = false;
                                resendBtn.style.opacity = '1';
                                resendCd.classList.add('d-none');
                                localStorage.removeItem(KEY);
                            }
                        }, 1000);
                    }

                    const until = parseInt(localStorage.getItem(KEY) || '0', 10);
                    const timeLeft = until - Date.now();
                    if (timeLeft > 0) startResendCountdown(timeLeft);

                    document.getElementById('resendForm').addEventListener('submit', function() {
                        // Set 60s cooldown and clear OTP expiry (will be refreshed from server)
                        localStorage.setItem(KEY, Date.now() + 60000);
                        localStorage.removeItem(OTP_EXPIRY_KEY);
                    });
                })();
            </script>

            
        </div>
    </div>
</body>

</html>