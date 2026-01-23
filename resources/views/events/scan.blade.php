@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan QR Event</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f8fafc; }
        .scan-container { max-width: 860px; margin: 24px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
        .scan-header { padding: 16px 20px; border-bottom: 1px solid #eee; }
        .scan-body { padding: 16px 20px 24px; }
        #reader { width: 100%; min-height: 360px; border: 1px dashed #ddd; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #fafafa; }
        .status { font-size: 14px; color: #374151; }
        .status.ok { color: #16a34a; font-weight: 600; }
        .status.warn { color: #b45309; font-weight: 600; }
        .status.err { color: #dc2626; font-weight: 600; }
        .success-anim { display: none; width: 100%; min-height: 220px; border-radius: 8px; background: #fafafa; align-items: center; justify-content: center; flex-direction: column; padding: 24px 0; }
        .success-anim svg { width: 120px; height: 120px; }
        .success-anim .circle { stroke: #22c55e; stroke-width: 6; fill: none; stroke-linecap: round; stroke-dasharray: 302; stroke-dashoffset: 302; animation: drawCircle 800ms ease-out forwards; }
        .success-anim .check { stroke: #22c55e; stroke-width: 6; fill: none; stroke-linecap: round; stroke-linejoin: round; stroke-dasharray: 100; stroke-dashoffset: 100; animation: drawCheck 600ms 500ms ease-out forwards; }
        @keyframes drawCircle { to { stroke-dashoffset: 0; } }
        @keyframes drawCheck { to { stroke-dashoffset: 0; } }
        /* Disable-style for label acting as upload button */
        .btn.disabled { pointer-events: none; opacity: 0.6; }
    </style>
</head>
<body>
<div class="scan-container">
    <div class="scan-header d-flex align-items-center justify-content-between">
        <div>
            <h5 class="mb-0">Scan QR Event</h5>
            <small class="text-muted">{{ $event->title ?? 'Event' }}</small>
        </div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('events.show', $event) }}">Kembali ke Detail</a>
    </div>
    <div class="scan-body">
        @php
            $canScan = isset($eventStarted) && $eventStarted && isset($registration) && $registration && $registration->status === 'active';
        @endphp
        @if(!$canScan)
            <div class="alert alert-warning" role="alert">
                Scan kamera tersedia saat acara dimulai dan Anda terdaftar aktif.
            </div>
        @endif
        <div class="mb-3">
            <p class="status" id="scan-status">{{ $canScan ? 'Menyiapkan kamera...' : 'Kamera dinonaktifkan.' }}</p>
        </div>
        <div id="reader"></div>
        <div id="success-anim" class="success-anim">
            <svg viewBox="0 0 120 120" aria-hidden="true">
                <circle class="circle" cx="60" cy="60" r="48"></circle>
                <path class="check" d="M40 60 L55 75 L82 45"></path>
            </svg>
            <div class="status ok mt-2">Absensi Berhasil Dilakukan</div>
        </div>
        <div class="mt-3 d-flex align-items-center" style="gap:12px;">
            <label class="btn btn-outline-primary btn-sm mb-0 {{ !$canScan ? 'disabled' : '' }}" for="file-input" title="Upload Foto untuk Scan" aria-label="Upload Foto untuk Scan">
                <i class="bi bi-upload"></i>
                <span class="visually-hidden">Upload Foto untuk Scan</span>
            </label>
            <input id="file-input" type="file" accept="image/png,image/jpeg,image/webp,image/svg+xml" style="display:none;" @if(!$canScan) disabled @endif>

            <button id="start-btn" class="btn btn-outline-success btn-sm" type="button" @if(!$canScan) disabled @endif title="Mulai Kamera" aria-label="Mulai Kamera">
                <i class="bi bi-camera-video"></i>
                <span class="visually-hidden">Mulai Kamera</span>
            </button>

            <button id="stop-btn" class="btn btn-outline-secondary btn-sm" type="button" disabled title="Hentikan Kamera" aria-label="Hentikan Kamera">
                <i class="bi bi-stop-circle"></i>
                <span class="visually-hidden">Hentikan Kamera</span>
            </button>

            <button id="swap-btn" class="btn btn-outline-secondary btn-sm" type="button" disabled title="Ganti Kamera" aria-label="Ganti Kamera">
                <i class="bi bi-arrow-repeat"></i>
                <span class="visually-hidden">Ganti Kamera</span>
            </button>
        </div>
        <div class="mt-3">
            <div id="scan-result" class="status"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
(function(){
    const statusEl = document.getElementById('scan-status');
    const resultEl = document.getElementById('scan-result');
    const startBtn = document.getElementById('start-btn');
    const stopBtn = document.getElementById('stop-btn');
    const swapBtn = document.getElementById('swap-btn');
    const fileInput = document.getElementById('file-input');
    const fileLabel = document.querySelector('label[for="file-input"]');
    let html5Qr = null;
    let activeCameraId = null;
    let cameras = [];
    let isSaving = false;
    const successAnimEl = document.getElementById('success-anim');
    const readerEl = document.getElementById('reader');

    function setStatus(text, cls){
        statusEl.textContent = text;
        statusEl.className = 'status' + (cls ? (' ' + cls) : '');
    }
    function setResult(text, cls){
        resultEl.textContent = text;
        resultEl.className = 'status' + (cls ? (' ' + cls) : '');
    }

    function setUploadEnabled(enabled){
        try {
            if (fileInput) fileInput.disabled = !enabled;
            if (fileLabel) {
                if (enabled) fileLabel.classList.remove('disabled');
                else fileLabel.classList.add('disabled');
            }
        } catch(_) {}
    }

    async function startCamera(cameraId){
        try {
            if (!Html5Qrcode) { setStatus('Library scanner tidak tersedia.', 'err'); return; }
            const devices = await Html5Qrcode.getCameras();
            if (!devices || devices.length === 0) { setStatus('Tidak ada kamera terdeteksi.', 'err'); return; }
            cameras = devices;
            // Pilih kamera default: prefer "back/rear/environment" jika tersedia
            function pickPreferred(devs){
                let back = devs.find(d => /back|rear|environment/i.test((d.label||'')));
                return back || devs[0];
            }
            if (!activeCameraId) {
                activeCameraId = (cameraId ? cameraId : pickPreferred(devices).id);
            } else if (cameraId) {
                activeCameraId = cameraId;
            }
            html5Qr = new Html5Qrcode('reader');
            setStatus('Membuka kamera...', 'warn');
            await html5Qr.start(
                activeCameraId,
                { fps: 10, qrbox: { width: 260, height: 260 } },
                async (decodedText, decodedResult) => {
                    if (isSaving) return; // prevent duplicate submissions
                    isSaving = true;
                    setResult('Berhasil: ' + decodedText, 'ok');
                    await persistAttendance(decodedText);
                },
                (errorMessage) => {
                    // ignore frequent scan errors; keep UI responsive
                }
            );
            stopBtn.disabled = false;
            startBtn.disabled = true;
            swapBtn.disabled = !(cameras && cameras.length > 1);
            // Disable upload while camera is active
            setUploadEnabled(false);
            setStatus('Kamera aktif, Scan pada QR Code yang diberikan oleh Panitia Penyelenggara Event', 'ok');
        } catch (e) {
            setStatus('Gagal membuka kamera: ' + (e && e.message ? e.message : e), 'err');
        }
    }

    async function stopCamera(){
        try {
            if (html5Qr) {
                await html5Qr.stop();
                await html5Qr.clear();
                html5Qr = null;
                stopBtn.disabled = true;
                swapBtn.disabled = true;
                startBtn.disabled = false;
                // Re-enable upload when camera stops
                setUploadEnabled(true);
                setStatus('Kamera dihentikan.', 'warn');
            }
        } catch (e) {
            setStatus('Gagal menghentikan kamera.', 'err');
        }
    }

    // Auto start camera on page load if allowed
    const canScan = {{ json_encode($canScan) }};
    document.addEventListener('DOMContentLoaded', function(){
        if (canScan) {
            startCamera();
            startBtn.disabled = true;
        } else {
            // Ensure controls are disabled when event not eligible
            if (startBtn) startBtn.disabled = true;
            setUploadEnabled(false);
        }
    });

    async function persistAttendance(decodedText){
        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
            // Use a relative URL to avoid cross-origin/CORS issues in dev
            const url = '{{ route('events.attendance.scan', $event, false) }}';
            const resp = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ qr_text: decodedText })
            });
            let data = {};
            try {
                const ct = resp.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    data = await resp.json();
                }
            } catch(_) { /* ignore json parse errors */ }
            if (resp.ok) {
                setStatus(data.message || 'Attendance berhasil disimpan.', 'ok');
                // Show success animation and hide camera view
                try {
                    if (readerEl) readerEl.style.display = 'none';
                    if (successAnimEl) successAnimEl.style.display = 'flex';
                } catch(_) {}
                try {
                    await stopCamera();
                } catch (e) {
                    setStatus('Attendance tersimpan. Kamera tidak dimatikan.', 'warn');
                }
            } else {
                setStatus((data && data.message) ? data.message : ('Attendance gagal ('+resp.status+').'), 'err');
            }
        } catch (e) {
            const msg = (e && e.message ? e.message : e);
            if (String(msg).toLowerCase().includes('failed to fetch')) {
                setStatus('Gagal menyimpan attendance: masalah jaringan atau alamat tidak cocok. Coba ulang dan pastikan halaman dan server sama domain.', 'err');
            } else {
                setStatus('Gagal menyimpan attendance: ' + msg, 'err');
            }
        } finally {
            isSaving = false;
        }
    }

    stopBtn.addEventListener('click', function(){
        stopCamera();
    });

    startBtn.addEventListener('click', function(){
        if (!canScan) {
            setStatus('Scan kamera tersedia saat acara dimulai dan Anda terdaftar aktif.', 'warn');
            return;
        }
        startCamera();
    });

    function swapCamera(){
        try {
            if (!cameras || cameras.length < 2) {
                setStatus('Tidak ada kamera lain untuk diganti.', 'warn');
                return;
            }
            const idx = cameras.findIndex(c => c.id === activeCameraId);
            const nextIdx = (idx >= 0) ? ((idx + 1) % cameras.length) : 0;
            const nextId = cameras[nextIdx].id;
            stopCamera().then(() => startCamera(nextId));
        } catch(e){
            setStatus('Gagal mengganti kamera.', 'err');
        }
    }

    swapBtn.addEventListener('click', swapCamera);

    fileInput.addEventListener('change', async function(ev){
        const file = ev.target.files && ev.target.files[0];
        if (!file) return;
        const type = (file.type || '').toLowerCase();

        async function decodeBlobWithFallback(blob){
            try {
                if (!html5Qr) html5Qr = new Html5Qrcode('reader');
                setStatus('Memindai gambar...', 'warn');
                const decodedText = await html5Qr.scanFile(blob, true);
                return decodedText;
            } catch (primaryError) {
                return new Promise((resolve, reject) => {
                    const url = URL.createObjectURL(blob);
                    const img = new Image();
                    img.onload = function(){
                        const maxSide = 1600;
                        const iw = img.naturalWidth || img.width;
                        const ih = img.naturalHeight || img.height;
                        const scale = Math.min(1, maxSide / Math.max(iw, ih));
                        const w = Math.max(320, Math.floor(iw * scale));
                        const h = Math.max(320, Math.floor(ih * scale));
                        const canvas = document.createElement('canvas');
                        canvas.width = w; canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, w, h);
                        try {
                            const imageData = ctx.getImageData(0, 0, w, h);
                            const code = jsQR(imageData.data, w, h, { inversionAttempts: 'attemptBoth' });
                            if (code && code.data) {
                                resolve(code.data);
                            } else {
                                reject(new Error('jsQR gagal decode'));
                            }
                        } catch (e) {
                            reject(e);
                        } finally {
                            URL.revokeObjectURL(url);
                        }
                    };
                    img.onerror = function(){
                        URL.revokeObjectURL(url);
                        reject(new Error('Gagal memuat gambar untuk decode'));
                    };
                    img.src = url;
                });
            }
        }

        // Handle SVG uploads by rasterizing to PNG before scanning
        if (type === 'image/svg+xml') {
            try {
                const svgText = await file.text();
                const svgBlob = new Blob([svgText], { type: 'image/svg+xml' });
                const url = URL.createObjectURL(svgBlob);
                const img = new Image();
                img.onload = async function(){
                    const w = img.naturalWidth || 1024;
                    const h = img.naturalHeight || 1024;
                    const canvas = document.createElement('canvas');
                    canvas.width = w; canvas.height = h;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, w, h);
                    canvas.toBlob(async (pngBlob) => {
                        try {
                            const decodedText = await decodeBlobWithFallback(pngBlob);
                            setResult('Berhasil: ' + decodedText, 'ok');
                            setStatus('Pemindaian dari foto selesai.', 'ok');
                            persistAttendance(decodedText);
                        } catch (e) {
                            setResult('Tidak dapat membaca QR dari foto.', 'err');
                            setStatus('Pastikan QR jelas dan tidak blur.', 'warn');
                        }
                        URL.revokeObjectURL(url);
                    }, 'image/png');
                };
                img.onerror = function(){
                    setResult('Tidak dapat membaca QR dari foto.', 'err');
                    setStatus('Unggah PNG/JPG/WebP yang jelas.', 'warn');
                    URL.revokeObjectURL(url);
                };
                img.src = url;
            } catch (e) {
                setResult('Tidak dapat memproses file SVG.', 'err');
                setStatus('Unggah PNG/JPG/WebP yang jelas.', 'warn');
            }
            return;
        }

        // Restrict to raster image formats for reliable decoding
        const allowed = ['image/png','image/jpeg','image/webp'];
        if (!allowed.includes(type)) {
            setResult('Format tidak didukung. Unggah PNG/JPG/WebP.', 'err');
            setStatus('Upload foto berformat PNG/JPG/WebP.', 'warn');
            return;
        }

        try {
            const decodedText = await decodeBlobWithFallback(file);
            setResult('Berhasil: ' + decodedText, 'ok');
            setStatus('Pemindaian dari foto selesai.', 'ok');
            persistAttendance(decodedText);
        } catch (e) {
            setResult('Tidak dapat membaca QR dari foto.', 'err');
            setStatus('Pastikan QR jelas, fokus, dan tidak terpotong.', 'warn');
        }
    });
})();
</script>
</body>
</html>
@include('partials.footer-before-login')
