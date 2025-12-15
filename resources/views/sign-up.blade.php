<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --bg-start:#51376C; --bg-end:#2E2050; --accent:#f4a442; --accent-hover:#e68a00; --glass:rgba(255,255,255,.08); --border:rgba(255,255,255,.18);
        }
        body {
            min-height:100vh; margin:0; font-family:"Poppins",sans-serif; background:radial-gradient(circle,var(--bg-start) 0%,var(--bg-end) 100%); color:#fff; display:flex; align-items:center; justify-content:center; padding:30px 16px;
        }
        .layout { width:100%; max-width:1080px; display:grid; gap:40px; grid-template-columns:repeat(auto-fit,minmax(360px,1fr)); align-items:stretch; }
        .panel-left { display:flex; flex-direction:column; justify-content:center; position:relative; }
        .brand-box { text-align:center; }
        .brand-box img { width:300px; max-width:100%; height:auto; filter:drop-shadow(0 6px 18px rgba(0,0,0,.35)); }
        .signup-card { background:linear-gradient(145deg,rgba(255,255,255,.12),rgba(255,255,255,.04)); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); border:1px solid var(--border); border-radius:28px; padding:40px 42px 36px; position:relative; box-shadow:0 10px 30px -5px rgba(0,0,0,.45); }
        .signup-card h3 { font-weight:600; margin:0 0 24px; letter-spacing:.5px; }
        form .form-group { margin-bottom:18px; }
        label h6, h6.label { font-size:13px; font-weight:600; letter-spacing:.5px; text-transform:uppercase; margin:0 0 6px; opacity:.85; }
        .form-control { width:100%; border:1px solid rgba(255,255,255,.35); border-radius:12px; background:var(--glass); color:#fff; padding:11px 14px; font-size:14px; transition:.25s border, .25s box-shadow, .25s background; }
        .form-control:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(244,164,66,.35); }
        input[type=file].form-control { padding:7px 14px; }
        .avatar-wrap { display:flex; align-items:center; gap:18px; margin-bottom:20px; }
        .avatar-ring { width:88px; height:88px; border-radius:50%; overflow:hidden; position:relative; background:linear-gradient(135deg,#ffffff22,#ffffff05); border:2px solid rgba(255,255,255,.35); box-shadow:0 4px 14px -2px rgba(0,0,0,.5); }
        .avatar-ring img { width:100%; height:100%; object-fit:cover; display:block; transition:.25s transform; }
        .avatar-actions { position:absolute; inset:0; display:flex; align-items:flex-end; justify-content:center; pointer-events:none; }
        .avatar-actions .btn-icon { pointer-events:auto; background:rgba(0,0,0,.35); color:#fff; border:none; border-radius:999px; display:flex; align-items:center; justify-content:center; width:28px; height:28px; margin-bottom:6px; backdrop-filter:blur(6px); box-shadow:0 4px 10px -3px rgba(0,0,0,.6); }
        .avatar-actions .btn-icon + .btn-icon { margin-left:6px; }
        .btn-icon svg { width:18px; height:18px; }
        .avatar-hint { font-size:11px; line-height:1.4; opacity:.7; }
        .btn-register { background:var(--accent); border:none; color:#fff; font-weight:600; width:100%; padding:13px 18px; border-radius:14px; margin-top:4px; font-size:15px; letter-spacing:.3px; box-shadow:0 6px 18px -4px rgba(244,164,66,.55); transition:.25s background,.25s transform,.25s box-shadow; }
        .btn-register:hover { background:var(--accent-hover); transform:translateY(-2px); box-shadow:0 10px 24px -6px rgba(244,164,66,.65); }
        .btn-register:active { transform:translateY(0); box-shadow:0 4px 12px -2px rgba(244,164,66,.55); }
        .split-text { display:flex; align-items:center; gap:14px; margin:26px 0 18px; font-size:12px; letter-spacing:.5px; text-transform:uppercase; color:rgba(255,255,255,.55); }
        .split-text:before, .split-text:after { content:""; flex:1; height:1px; background:linear-gradient(90deg,rgba(255,255,255,.05),rgba(255,255,255,.45),rgba(255,255,255,.05)); }
        .btn-google { background:#fff; color:#373737; border:1px solid #d8d8d8; font-weight:600; width:100%; padding:12px 16px; border-radius:14px; font-size:14px; display:flex; align-items:center; justify-content:center; gap:10px; position:relative; overflow:hidden; transition:.25s box-shadow,.25s transform,.25s background; }
        .btn-google img { width:20px; height:20px; }
        .btn-google:hover { background:#f5f5f5; box-shadow:0 6px 18px -6px rgba(0,0,0,.3); transform:translateY(-2px); }
        .login-meta { margin-top:22px; font-size:13px; text-align:center; }
        .login-meta a { color:var(--accent); font-weight:600; text-decoration:none; }
        .login-meta a:hover { text-decoration:underline; }
        .alert { border-radius:14px; padding:14px 18px; font-size:13px; backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,.25); }
        .alert-danger { background:rgba(190,40,56,.25); color:#ffb4bc; }
        .alert-success { background:rgba(32,132,70,.25); color:#b2f5c5; }
        .invalid-feedback { display:block; font-size:12px; margin-top:4px; }
        .back-btn { position:absolute; top:18px; left:20px; width:24px; cursor:pointer; opacity:.9; transition:.25s transform; }
        .back-btn:hover { transform:translateX(-4px); }
        @media (max-width: 960px){ .layout { gap:28px; } .signup-card { padding:34px 30px 30px; } }
        @media (max-width: 680px){ body { padding:20px 14px; } .signup-card { padding:32px 24px 28px; } .avatar-ring { width:76px; height:76px; } }
        /* Cropper circular adjustments */
        .modal-crop .modal-dialog { max-width:480px; }
        .cropper-view-box, .cropper-face { border-radius:50% !important; }
        .btn-crop-confirm { background:var(--accent); border:none; color:#fff; font-weight:600; padding:10px 20px; border-radius:12px; }
        .btn-crop-confirm:hover { background:var(--accent-hover); }
    </style>
</head>

<body>
    <div class="layout">
        <div class="panel-left">
            <div class="brand-box">
                <img src="{{ asset('aset/logo.png') }}" alt="Logo">
            </div>
        </div>

        <div class="signup-card">
            <a href="{{ route('landing-page') }}" class="d-inline-block" title="Kembali">
                <img src="{{ asset('aset/back.png') }}" class="back-btn" alt="Kembali">
            </a>
            <h3>Buat Akun idSpora</h3>

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
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

                <div class="avatar-wrap">
                    <div class="avatar-ring">
                        <img id="avatarPreview" src="{{ asset('aset/profile.png') }}" alt="Preview">
                        <div class="avatar-actions">
                            <button type="button" class="btn-icon" title="Pilih gambar" onclick="triggerAvatarFile()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 7h3l2-2h6l2 2h3v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 17a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg>
                            </button>
                            <button type="button" class="btn-icon" title="Atur ulang/crop" onclick="openCropperFromPreview()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 3h6v6M3 9V3h6M3 15v6h6M15 21h6v-6"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 8l8 8"/></svg>
                            </button>
                        </div>
                    </div>
                    <div style="flex:1;">
                        <h6 class="label">Foto Profil (opsional)</h6>
                        <input id="avatarFileInput" type="file" accept="image/*" name="avatar" class="form-control @error('avatar') is-invalid @enderror" onchange="openCropper(event)">
                        <div class="avatar-hint">JPG / PNG / WEBP, maksimal 2MB.</div>
                        @error('avatar')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <h6 class="label">Nama Lengkap</h6>
                    <input type="text" autocomplete="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <h6 class="label">Email</h6>
                    <input type="email" autocomplete="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <h6 class="label">Kata Sandi</h6>
                    <div class="input-group">
                        <input type="password" autocomplete="new-password" name="password" id="passwordInput" class="form-control @error('password') is-invalid @enderror" required aria-describedby="passwordHelp">
                        <button class="btn btn-outline-light" type="button" id="togglePassword" aria-label="Tampilkan/Sembunyikan kata sandi" style="border-radius: 12px; display:flex; align-items:center;">
                            <svg id="icon-eye" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="icon-eye-slash" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M3 3l18 18"/>
                                <path d="M10.58 10.58a3 3 0 104.24 4.24"/>
                                <path d="M7.11 7.11C4.6 8.55 3 12 3 12s4 8 9 8c2.03 0 3.88-.73 5.37-1.88"/>
                                <path d="M20.89 16.89C21.4 16.02 22 14.9 22 12c0 0-4-8-10-8-1.22 0-2.36.23-3.43.62"/>
                            </svg>
                        </button>
                    </div>
                    <small id="passwordHelp" class="text-warning d-block mt-2">
                        Minimal 8 karakter, mengandung huruf besar, angka, dan simbol.
                    </small>
                    <div id="passwordErrors" class="invalid-feedback" style="display:none;"></div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="form-group" style="margin-bottom:26px;">
                    <h6 class="label">Konfirmasi Kata Sandi</h6>
                    <div class="input-group">
                        <input type="password" autocomplete="new-password" name="password_confirmation" id="passwordConfirmInput" class="form-control" required>
                        <button class="btn btn-outline-light" type="button" id="togglePasswordConfirm" aria-label="Tampilkan/Sembunyikan konfirmasi" style="border-radius: 12px; display:flex; align-items:center;">
                            <svg id="icon-eye2" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg id="icon-eye-slash2" xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M3 3l18 18"/>
                                <path d="M10.58 10.58a3 3 0 104.24 4.24"/>
                                <path d="M7.11 7.11C4.6 8.55 3 12 3 12s4 8 9 8c2.03 0 3.88-.73 5.37-1.88"/>
                                <path d="M20.89 16.89C21.4 16.02 22 14.9 22 12c0 0-4-8-10-8-1.22 0-2.36.23-3.43.62"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-register">Daftar</button>
            </form>

            <div class="split-text">Atau</div>

            <a href="{{ route('auth.google') }}" class="btn-google" style="text-decoration:none;">
                <img src="{{ asset('aset/logo-google.png') }}" alt="logo google">
                Daftar dengan Google
            </a>

            <div class="login-meta">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></div>
        </div>
    </div>
</body>
    <!-- Modal Cropper -->
    <div class="modal fade modal-crop" id="avatarCropperModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1f1830; border:1px solid var(--border); border-radius:20px;">
                <div class="modal-header" style="border-bottom:1px solid rgba(255,255,255,.12);">
                    <h5 class="modal-title" style="color:#fff;">Atur Foto Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="min-height:360px; display:flex; flex-direction:column; gap:14px;">
                    <div style="flex:1; position:relative;">
                        <img id="cropperSource" alt="Crop Source" style="max-width:100%; display:block;">
                    </div>
                    <div class="d-flex justify-content-between align-items-center" style="gap:10px; flex-wrap:wrap;">
                        <div class="btn-group" role="group" aria-label="Kontrol Zoom">
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="cropperZoom(0.1)">Zoom +</button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="cropperZoom(-0.1)">Zoom -</button>
                            <button type="button" class="btn btn-sm btn-outline-light" onclick="cropperRotate(90)">Rotate 90°</button>
                        </div>
                        <button type="button" class="btn-crop-confirm" onclick="applyCroppedAvatar()">Simpan</button>
                    </div>
                    <small style="opacity:.65; font-size:11px;">Geser / pinch (mobile) untuk menyesuaikan posisi gambar dalam lingkaran.</small>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>
<link href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet" />
<script>
let cropperInstance = null; let originalFileInput = null;
function openCropper(e){
    const file = e.target.files[0];
    if(!file) return;
    originalFileInput = e.target;
    const reader = new FileReader();
    reader.onload = ev => {
        const img = document.getElementById('cropperSource');
        img.src = ev.target.result;
        const modalEl = document.getElementById('avatarCropperModal');
        const bsModal = new bootstrap.Modal(modalEl);
        bsModal.show();
        setTimeout(()=>{
            if(cropperInstance){ cropperInstance.destroy(); }
            cropperInstance = new Cropper(img, {
                aspectRatio:1,
                viewMode:1,
                dragMode:'move',
                autoCropArea:1,
                background:false,
                responsive:true,
                movable:true,
                zoomable:true,
                rotatable:true,
            });
        },250);
    };
    reader.readAsDataURL(file);
}
function triggerAvatarFile(){
    const input = document.getElementById('avatarFileInput');
    input?.click();
}
function openCropperFromPreview(){
    const imgSrc = document.getElementById('avatarPreview')?.src;
    if(!imgSrc) return;
    const img = document.getElementById('cropperSource');
    img.src = imgSrc;
    const modalEl = document.getElementById('avatarCropperModal');
    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();
    setTimeout(()=>{
        if(cropperInstance){ cropperInstance.destroy(); }
        cropperInstance = new Cropper(img, {
            aspectRatio:1,
            viewMode:1,
            dragMode:'move',
            autoCropArea:1,
            background:false,
            responsive:true,
            movable:true,
            zoomable:true,
            rotatable:true,
        });
    },250);
}
function cropperZoom(delta){ if(cropperInstance){ cropperInstance.zoom(delta); } }
function cropperRotate(deg){ if(cropperInstance){ cropperInstance.rotate(deg); } }
function applyCroppedAvatar(){
    if(!cropperInstance || !originalFileInput) return;
    const canvas = cropperInstance.getCroppedCanvas({ width:400, height:400, imageSmoothingQuality:'high' });
    canvas.toBlob(blob => {
        const file = new File([blob], 'avatar-cropped.png', { type:'image/png' });
        const dt = new DataTransfer(); dt.items.add(file); originalFileInput.files = dt.files;
        const preview = document.getElementById('avatarPreview');
        preview.src = canvas.toDataURL('image/png');
        const modalEl = document.getElementById('avatarCropperModal');
        const bsModal = bootstrap.Modal.getInstance(modalEl); bsModal.hide();
    }, 'image/png', 0.92);
}
</script>

<script>
// Password show/hide toggles with eye icons
(() => {
    const pw = document.getElementById('passwordInput');
    const pwc = document.getElementById('passwordConfirmInput');
    const btn = document.getElementById('togglePassword');
    const btn2 = document.getElementById('togglePasswordConfirm');
    const eye = document.getElementById('icon-eye');
    const eyeSlash = document.getElementById('icon-eye-slash');
    const eye2 = document.getElementById('icon-eye2');
    const eyeSlash2 = document.getElementById('icon-eye-slash2');
    if (btn && pw && eye && eyeSlash) {
        btn.addEventListener('click', function(){
            const isHidden = pw.type === 'password';
            pw.type = isHidden ? 'text' : 'password';
            eye.style.display = isHidden ? 'none' : '';
            eyeSlash.style.display = isHidden ? '' : 'none';
        });
    }
    if (btn2 && pwc && eye2 && eyeSlash2) {
        btn2.addEventListener('click', function(){
            const isHidden = pwc.type === 'password';
            pwc.type = isHidden ? 'text' : 'password';
            eye2.style.display = isHidden ? 'none' : '';
            eyeSlash2.style.display = isHidden ? '' : 'none';
        });
    }
})();

// Client-side password policy validation
(() => {
    const input = document.getElementById('passwordInput');
    const errorsBox = document.getElementById('passwordErrors');
    if(!input || !errorsBox) return;
    const rules = [
        { test: v => v.length >= 8, msg: 'Minimal 8 karakter' },
        { test: v => /[A-Z]/.test(v), msg: 'Mengandung huruf besar (A-Z)' },
        { test: v => /[0-9]/.test(v), msg: 'Mengandung angka (0-9)' },
        { test: v => /[^A-Za-z0-9]/.test(v), msg: 'Mengandung tanda baca/simbol' },
    ];
    function validate(){
        const val = input.value || '';
        const fails = rules.filter(r => !r.test(val)).map(r => r.msg);
        if(fails.length){
            errorsBox.style.display = '';
            errorsBox.innerHTML = 'Syarat kata sandi: ' + fails.join(', ') + '.';
            input.classList.add('is-invalid');
        } else {
            errorsBox.style.display = 'none';
            errorsBox.innerHTML = '';
            input.classList.remove('is-invalid');
        }
    }
    input.addEventListener('input', validate);
})();
</script>

</html>