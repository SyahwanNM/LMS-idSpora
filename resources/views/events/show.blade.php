<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $event->title }} - Detail Event</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
@include('partials.navbar-after-login')
<div class="container py-5" style="margin-top:70px;">
    <div class="row">
        <div class="col-md-6 mb-4">
            @if($event->image)
                <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="img-fluid rounded shadow">
            @else
                <img src="https://via.placeholder.com/800x500/4f46e5/ffffff?text=No+Image" alt="{{ $event->title }}" class="img-fluid rounded shadow">
            @endif
        </div>
        <div class="col-md-6">
            <h1 class="h3 fw-bold mb-2">{{ $event->title }}</h1>
            <p class="text-muted mb-3">{{ $event->speaker ?? 'Narasumber TBA' }} • {{ $event->location ?? 'Lokasi TBA' }}</p>
            <div class="mb-3">
                <strong>Tanggal:</strong> {{ $event->event_date?->format('d F Y') ?? '-' }}<br>
                <strong>Waktu:</strong> {{ $event->event_time?->format('H:i') ?? '-' }} WIB
            </div>
            <div class="mb-3">
                @if($event->hasDiscount())
                    <span class="text-decoration-line-through text-muted">Rp {{ number_format($event->price,0,',','.') }}</span>
                    <span class="badge bg-danger">-{{ $event->discount_percentage }}%</span>
                    <div class="fs-4 fw-semibold mt-1">Rp {{ number_format($event->discounted_price,0,',','.') }}</div>
                @else
                    <div class="fs-4 fw-semibold">Rp {{ number_format($event->price,0,',','.') }}</div>
                @endif
            </div>
            <div class="mb-4">
                {!! nl2br(e($event->description)) !!}
            </div>
      @php $registered = !empty($event->is_registered); @endphp
      @auth
        <button id="registerBtn" class="btn {{ $registered ? 'btn-success' : 'btn-primary' }}" {{ $registered ? 'disabled' : '' }} data-event-id="{{ $event->id }}">
          {{ $registered ? 'Anda Terdaftar' : 'Daftar Sekarang' }}
        </button>
      @endauth
      @guest
        <button id="guestRegisterBtn" class="btn btn-primary" type="button" data-open-login-required>Daftar Sekarang</button>
      @endguest
      <a href="{{ route('events.index') }}" class="btn btn-link mt-2 mt-md-0">&larr; Kembali ke daftar event</a>
        </div>
    </div>
</div>

<!-- Modal sukses -->
<div class="modal fade" id="regSuccessModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white py-2">
        <h6 class="modal-title">Pendaftaran Berhasil</h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-0">Anda berhasil terdaftar pada event: <strong>{{ $event->title }}</strong></p>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

@guest
<!-- Modal: Login Required (Hybrid Option F) -->
<div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-semibold" id="loginRequiredLabel">Login Diperlukan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body pt-2">
        <div class="d-flex gap-3 align-items-start">
          <div class="flex-shrink-0" style="width:54px;height:54px;border-radius:16px;display:flex;align-items:center;justify-content:center;background:#eef2ff;color:#4f46e5;">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
              <path fill="#fff" d="M8.93 6.588a.5.5 0 0 0-.832-.374L5.5 8.293V9.5a.5.5 0 0 0 .5.5h.793l2.105-2.105a.5.5 0 0 0 .032-.707z"/>
            </svg>
          </div>
          <div>
            <h6 class="mb-1">Anda belum login</h6>
            <p class="text-muted mb-0 small">Login terlebih dahulu untuk mendaftar event ini. Setelah login Anda akan kembali ke halaman ini.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0 d-flex justify-content-between">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Nanti</button>
        <a href="{{ route('login', ['redirect' => request()->fullUrl()]) }}" class="btn btn-primary">Login Sekarang</a>
      </div>
    </div>
  </div>
</div>
@endguest

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
@auth
const btn = document.getElementById('registerBtn');
if(btn){
  btn.addEventListener('click', () => {
    if(btn.classList.contains('btn-success')) return;
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    btn.disabled = true;
    fetch(`/events/${btn.dataset.eventId}/register`, {
      method: 'POST',
      headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': token,
        'Accept':'application/json'
      },
      body: JSON.stringify({})
    }).then(r => r.json())
    .then(data => {
      if(data.status === 'ok' || data.status === 'already'){
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-success');
        btn.textContent = 'Anda Terdaftar';
        const m = new bootstrap.Modal(document.getElementById('regSuccessModal'));
        m.show();
      } else {
        alert(data.message || 'Gagal mendaftar');
        btn.disabled = false;
      }
    }).catch(()=>{ btn.disabled=false; alert('Terjadi kesalahan'); });
  });
}
@endauth
@guest
// Guest button triggers login modal (hybrid Option F)
document.addEventListener('DOMContentLoaded', () => {
  const trigger = document.querySelector('[data-open-login-required]');
  if(!trigger) return;
  trigger.addEventListener('click', () => {
    const modalEl = document.getElementById('loginRequiredModal');
    if(window.bootstrap && modalEl){
      const m = new bootstrap.Modal(modalEl);
      m.show();
    } else {
      // Fallback simple prompt
      if(confirm('Anda perlu login untuk mendaftar. Buka halaman login?')){
        window.location = '{{ route('login', ['redirect' => request()->fullUrl()]) }}';
      }
    }
  });
});
@endguest
</script>
</body>
</html>
