</html>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
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

    .lupa-password {
      margin-top: 8px;
      text-align: right;
      font-size: 13px;
    }

    .lupa-password a {
      color: white;
      font-weight: 500;
      text-decoration: none;
    }

    .lupa-password a:hover {
      text-decoration: underline;
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
  </style>
</head>

<body>
   <a href="{{ route('landing-page') }}">
     <img class="back" src="aset/back.png" alt="Kembali">
   </a>
  <div class="main-container">
    <div class="kiri">
      <img class="logo" src="{{ asset('aset/logo.png') }}" alt="">
    </div>

    <div class="kanan">
      <h3>Masuk</h3>

      <form action="{{ route('login') }}" method="POST">
        @csrf
    @php
      // Pastikan nilai redirect tetap ada setelah error validasi
      $redirectTarget = old('redirect', request('redirect'));
    @endphp
    @if(!empty($redirectTarget))
      <input type="hidden" name="redirect" value="{{ $redirectTarget }}">
    @endif
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

        <div class="mb-3">
          <h6>Email</h6>
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                 value="{{ old('email') }}" required>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <h6>Kata Sandi</h6>
          <div class="input-group">
            <input id="signin-password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            <button type="button" class="btn btn-outline-light" id="toggle-password" aria-label="Tampilkan/Sembunyikan kata sandi" style="border-color: rgba(255,255,255,0.4); display:flex; align-items:center;">
              <!-- Eye icon (Heroicons-style) -->
              <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <!-- Eye-off icon (Heroicons-style) -->
              <svg id="icon-eye-slash" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                <path d="M3 3l18 18"/>
                <path d="M10.58 10.58a3 3 0 104.24 4.24"/>
                <path d="M7.11 7.11C4.6 8.55 3 12 3 12s4 8 9 8c2.03 0 3.88-.73 5.37-1.88"/>
                <path d="M20.89 16.89C21.4 16.02 22 14.9 22 12c0 0-4-8-10-8-1.22 0-2.36.23-3.43.62"/>
              </svg>
            </button>
          </div>
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="mb-3">
          <div class="form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">
              Ingat saya
            </label>
          </div>
        </div>
        <div class="lupa-password">
          <a href="{{ route('forgot-password') }}">
            Lupa Kata Sandi?</a>
        </div>
        <button type="submit" class="btn-register">Masuk</button>
      </form>

      <div class="garis">atau</div>

      <a href="{{ route('auth.google', array_filter(['redirect' => $redirectTarget ?? null])) }}" class="btn-google" style="text-decoration:none;">
        <img src="{{ asset('aset/logo-google.png') }}" alt="logo google">
        Login dengan Google
      </a>

      <div class="text-login">
        Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
      </div>
    </div>
  </div>
</body>

</html>
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
</script>