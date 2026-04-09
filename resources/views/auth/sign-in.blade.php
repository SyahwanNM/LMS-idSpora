<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk - idSPORA</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      padding: 0;
      min-height: 100vh;
      font-family: "Poppins", sans-serif;
      background: #2E2050;
      background: radial-gradient(circle at center, #51376C 0%, #2E2050 100%);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow-x: hidden;
      position: relative;
    }

    /* Background Decorations */
    body::before, body::after {
      content: "";
      position: absolute;
      width: 500px;
      height: 500px;
      border-radius: 50%;
      filter: blur(100px);
      z-index: -1;
      opacity: 0.3;
      animation: float 20s infinite alternate;
    }

    body::before {
      background: #7a5ba3;
      top: -200px;
      left: -100px;
    }

    body::after {
      background: #412d61;
      bottom: -200px;
      right: -100px;
      animation-delay: -10s;
    }

    @keyframes float {
      0% { transform: translate(0, 0) scale(1); }
      100% { transform: translate(40px, 40px) scale(1.05); }
    }

    .main-container {
      display: flex;
      width: 100%;
      max-width: 1100px;
      padding: 40px;
      min-height: 90vh;
      align-items: center;
      justify-content: center;
      gap: 60px;
      z-index: 1;
    }

    .back {
      position: absolute;
      top: 30px;
      left: 30px;
      width: 40px;
      height: 40px;
      padding: 10px;
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
      z-index: 10;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-decoration: none;
    }

    .back:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateX(-5px);
      color: white;
    }

    .kiri {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      animation: fadeInSide 0.8s ease forwards;
    }

    .logo {
      width: 100%;
      max-width: 400px;
      height: auto;
      filter: drop-shadow(0 15px 25px rgba(0,0,0,0.3));
    }

    .kiri-text {
      margin-top: 30px;
    }

    .kiri-text h1 {
      font-size: 3rem;
      font-weight: 800;
      letter-spacing: 1px;
      color: white;
      margin-bottom: 5px;
    }

    .kiri-text p {
      color: rgba(255,255,255,0.7);
      font-size: 1.2rem;
      font-weight: 300;
    }

    .kanan {
      flex: 1;
      width: 100%;
      max-width: 450px;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(25px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 30px;
      padding: 50px;
      box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.4);
      animation: fadeInSlideUp 0.8s ease forwards;
    }

    @keyframes fadeInSide {
      from { opacity: 0; transform: translateX(-40px); }
      to { opacity: 1; transform: translateX(0); }
    }

    @keyframes fadeInSlideUp {
      from { opacity: 0; transform: translateY(40px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .kanan h3 {
      font-weight: 700;
      font-size: 2rem;
      margin-bottom: 10px;
      text-align: center;
    }

    .subtitle {
      color: rgba(255,255,255,0.6);
      margin-bottom: 35px;
      font-size: 1rem;
      text-align: center;
    }

    .input-group-custom {
      margin-bottom: 25px;
    }

    .input-group-custom label {
      display: block;
      margin-bottom: 10px;
      font-size: 0.95rem;
      font-weight: 500;
      color: rgba(255,255,255,0.9);
    }

    .form-control {
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 15px;
      background: rgba(255, 255, 255, 0.05);
      padding: 14px 18px;
      width: 100%;
      color: white;
      transition: all 0.3s ease;
      font-size: 1rem;
    }

    .form-control:focus {
      border-color: #f4a442;
      box-shadow: 0 0 0 4px rgba(244, 164, 66, 0.15);
      background: rgba(255, 255, 255, 0.1);
      outline: none;
      color: white;
    }

    .btn-register {
      background: linear-gradient(135deg, #f4a442, #e68a00);
      border: none;
      color: white;
      font-weight: 600;
      width: 100%;
      padding: 16px;
      border-radius: 15px;
      margin-top: 10px;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 10px 25px -5px rgba(244, 164, 66, 0.4);
    }

    .btn-register:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 30px -5px rgba(244, 164, 66, 0.5);
    }

    .garis {
      display: flex;
      align-items: center;
      text-align: center;
      color: rgba(255, 255, 255, 0.4);
      margin: 30px 0;
      font-size: 0.9rem;
    }

    .garis::before,
    .garis::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .garis:not(:empty)::before { margin-right: 1.5em; }
    .garis:not(:empty)::after { margin-left: 1.5em; }

    .btn-google {
      background: white;
      color: #2E2050;
      font-weight: 600;
      width: 100%;
      padding: 14px;
      border-radius: 15px;
      border: none;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      text-decoration: none;
      transition: all 0.3s ease;
      font-size: 1rem;
    }

    .btn-google:hover {
      background: #f1f1f1;
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .text-login {
      margin-top: 30px;
      text-align: center;
      font-size: 0.95rem;
      color: rgba(255,255,255,0.6);
    }

    .text-login a {
      color: #f4a442;
      font-weight: 600;
      text-decoration: none;
    }

    .text-login a:hover { text-decoration: underline; }

    .input-group .btn-outline-light {
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-left: none;
      background: rgba(255, 255, 255, 0.05);
      border-radius: 0 15px 15px 0;
      color: white;
    }

    .input-group .form-control {
      border-radius: 15px 0 0 15px;
    }

    @media (max-width: 991px) {
      .main-container {
        flex-direction: column;
        padding: 100px 20px 60px;
        gap: 50px;
      }
      .kiri {
        margin-bottom: 0;
      }
      .logo { max-width: 300px; }
      .kanan { padding: 40px; }
      .kiri-text h1 { font-size: 2.5rem; }
    }

    @media (max-width: 576px) {
      .kanan { padding: 30px; }
      .kanan h3 { font-size: 1.8rem; }
      .kiri-text h1 { font-size: 2rem; }
    }
  </style>
</head>

<body>
  <a href="{{ route('landing-page') }}" class="back">
     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M19 12H5M12 19l-7-7 7-7"/>
     </svg>
  </a>
  <div class="main-container">
    <div class="kiri">
      <img class="logo" src="{{ asset('aset/logo.png') }}" alt="idSPORA">
    </div>

    <div class="kanan">
      <h3>Selamat Datang</h3>
      <p class="subtitle">Silakan masuk ke akun Anda</p>

      <form action="{{ route('login') }}" method="POST">
        @csrf
        @php
          $redirectTarget = old('redirect', request('redirect'));
        @endphp
        @if(!empty($redirectTarget))
          <input type="hidden" name="redirect" value="{{ $redirectTarget }}">
        @endif

        @if ($errors->any())
          <div class="alert alert-danger" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); border-radius: 15px; color: #ff8e97; padding: 15px; margin-bottom: 25px; font-size: 0.9rem;">
            <ul class="mb-0" style="list-style: none; padding-left: 0;">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        
        @if (session('success'))
          <div class="alert alert-success" style="background: rgba(40, 167, 69, 0.1); border: 1px solid rgba(40, 167, 69, 0.2); border-radius: 15px; color: #72f38e; padding: 15px; margin-bottom: 25px; font-size: 0.9rem;">
            {{ session('success') }}
          </div>
        @endif

        <div class="input-group-custom">
          <label>Email</label>
          <input type="email" name="email" autocomplete="username" class="form-control" 
                 value="{{ old('email') }}" placeholder="user@example.com" required>
        </div>

        <div class="input-group-custom">
          <label>Kata Sandi</label>
          <div class="input-group">
            <input id="signin-password" type="password" name="password" autocomplete="current-password" class="form-control" placeholder="••••••••" required>
            <button type="button" class="btn btn-outline-light" id="toggle-password" aria-label="Tampilkan/Sembunyikan kata sandi">
              <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg id="icon-eye-slash" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                <path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                <line x1="2" y1="2" x2="22" y2="22"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
          <div class="form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember" style="font-size: 0.85rem; color: rgba(255,255,255,0.7);">
              Ingat saya
            </label>
          </div>
          <div class="lupa-password" style="margin-top:0;">
            <a href="{{ route('forgot-password') }}">Lupa Kata Sandi?</a>
          </div>
        </div>

        <button type="submit" class="btn-register">Masuk</button>
      </form>

      <div class="garis">Atau masuk dengan</div>

      <a href="{{ route('auth.google', array_filter(['redirect' => $redirectTarget ?? null])) }}" class="btn-google">
        <img src="{{ asset('aset/logo-google.png') }}" alt="Google" width="20">
        Google Account
      </a>

      <div class="text-login">
        Belum punya akun? <a href="{{ route('register') }}">Daftar Sekarang</a>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const input = document.getElementById('signin-password');
      const btn = document.getElementById('toggle-password');
      const eye = document.getElementById('icon-eye');
      const eyeSlash = document.getElementById('icon-eye-slash');
      if (input && btn && eye && eyeSlash) {
        btn.addEventListener('click', function(){
          const isHidden = input.type === 'password';
          input.type = isHidden ? 'text' : 'password';
          eye.style.display = isHidden ? 'none' : '';
          eyeSlash.style.display = isHidden ? '' : 'none';
        });
      }
    })();
    (function(){
      const emailInput = document.querySelector('input[name="email"]');
      const rememberCb = document.getElementById('remember');
      if (!emailInput || !rememberCb) return;
      try {
        const savedEmail = localStorage.getItem('remember_email');
        if (savedEmail && !emailInput.value) {
          emailInput.value = savedEmail;
          rememberCb.checked = true;
        }
        const form = emailInput.form;
        if (form) {
          form.addEventListener('submit', function(){
            if (rememberCb.checked) {
              localStorage.setItem('remember_email', emailInput.value || '');
            } else {
              localStorage.removeItem('remember_email');
            }
          });
        }
      } catch(e){}
    })();
  </script>
</body>

</html>