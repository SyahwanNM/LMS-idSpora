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
            <button id="registerBtn" class="btn {{ $registered ? 'btn-success' : 'btn-primary' }}" {{ $registered ? 'disabled' : '' }} data-event-id="{{ $event->id }}">
                {{ $registered ? 'Anda Terdaftar' : 'Daftar Sekarang' }}
            </button>
            <a href="{{ route('events.index') }}" class="btn btn-link">&larr; Kembali ke daftar event</a>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
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
</script>
</body>
</html>
