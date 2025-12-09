@extends('layouts.admin')
@section('title', 'History Event')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-clock-history me-2"></i>History Event (Selesai)</h4>
        <div class="btn-group">
            <a href="{{ route('admin.add-event') }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Kembali</a>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <div class="input-group" style="max-width:420px">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" id="historyEventSearch" class="form-control" placeholder="Cari nama event selesai..." autocomplete="off">
                </div>
            </div>
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            @if($events->count())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Pembicara</th>
                                <th>Tanggal</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th>Lokasi</th>
                                <th>Harga</th>
                                <th>Diskon %</th>
                                <th>Benefit</th>
                                <th>Dokumen (%)</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr data-title="{{ Str::lower($event->title) }}">
                                    <td style="width:90px;">
                                        @if($event->image)
                                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="img-thumbnail" style="max-width:80px;height:54px;object-fit:cover;">
                                        @else
                                            <span class="badge bg-secondary">No Image</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $event->title }}</td>
                                    <td>{{ $event->speaker ?? '—' }}</td>
                                    <td>{{ $event->event_date }}</td>
                                    <td>{{ $event->event_time }}</td>
                                    <td>{{ $event->event_time_end ?? '—' }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>
                                        @php $isFree=(int)$event->price===0; $finalPrice = $event->hasDiscount() ? $event->discounted_price : $event->price; @endphp
                                        @if($isFree)
                                            <span class="badge bg-success">GRATIS</span>
                                        @else
                                            @if($event->hasDiscount())
                                                <span class="text-decoration-line-through text-muted small">Rp{{ number_format($event->price,0,',','.') }}</span>
                                                <span class="fw-semibold">Rp{{ number_format($finalPrice,0,',','.') }}</span>
                                            @else
                                                <span>Rp{{ number_format($event->price,0,',','.') }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $event->discount_percentage ? ($event->discount_percentage.'%') : '—' }}</td>
                                    <td class="text-truncate" style="max-width:160px;" title="{{ strip_tags($event->benefit) }}">{{ \Illuminate\Support\Str::limit(strip_tags($event->benefit), 40) ?: '—' }}</td>
                                    <td>{{ $event->documents_completion_percent }}%</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.events.show',$event) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteHistoryModal" data-event-id="{{ $event->id }}" data-event-title="{{ $event->title }}" data-event-image="{{ $event->image ? Storage::url($event->image) : '' }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $events->links() }}</div>
            @else
                <div class="text-center py-5">Belum ada event selesai untuk ditampilkan.</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    const input = document.getElementById('historyEventSearch');
    const rows = Array.from(document.querySelectorAll('table tbody tr'));
    let tId;
    input && input.addEventListener('input', () => {
        clearTimeout(tId);
        tId = setTimeout(() => {
            const term = (input.value || '').toLowerCase().trim();
            rows.forEach(row => {
                const title = (row.getAttribute('data-title') || '').toLowerCase();
                row.style.display = (term === '' || title.includes(term)) ? '' : 'none';
            });
        }, 150);
    });
        // Delete confirmation modal wiring
        const modalEl = document.getElementById('deleteHistoryModal');
        if (modalEl) {
                modalEl.addEventListener('show.bs.modal', function (ev) {
                        const btn = ev.relatedTarget;
                        const id = btn?.getAttribute('data-event-id');
                        const title = btn?.getAttribute('data-event-title') || 'Event';
                        const img = btn?.getAttribute('data-event-image');
                        const nameEl = modalEl.querySelector('#deleteHistoryName');
                        const imgWrap = modalEl.querySelector('#deleteHistoryImageWrapper');
                        const imgEl = modalEl.querySelector('#deleteHistoryImage');
                        const formEl = modalEl.querySelector('#deleteHistoryForm');
                        if (nameEl) nameEl.textContent = title;
                        if (img && imgEl && imgWrap) { imgEl.src = img; imgWrap.style.display = 'block'; } else if (imgWrap) { imgWrap.style.display = 'none'; }
                        if (formEl && id) { formEl.action = `{{ url('/admin/events') }}/${id}`; }
                        // disable confirm until checkbox checked
                        const confirmBtn = modalEl.querySelector('#deleteHistoryConfirmBtn');
                        const cb = modalEl.querySelector('#deleteHistoryConfirm');
                        if (confirmBtn) confirmBtn.disabled = !(cb && cb.checked);
                        if (cb && confirmBtn) {
                                cb.addEventListener('change', function(){ confirmBtn.disabled = !this.checked; }, { once: true });
                        }
                });
        }
});
</script>
<!-- Centered delete confirmation modal -->
<div class="modal fade" id="deleteHistoryModal" tabindex="-1" aria-labelledby="deleteHistoryLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mb-0" id="deleteHistoryLabel">Hapus Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center mb-2">Konfirmasi penghapusan event ini dari history.</p>
                <div class="p-2 rounded border bg-light text-center"><i class="bi bi-calendar-event me-1"></i> <strong id="deleteHistoryName">Event</strong></div>
                <div id="deleteHistoryImageWrapper" class="mt-3 text-center" style="display:none;">
                        <img id="deleteHistoryImage" src="" alt="Gambar Event" class="img-fluid rounded shadow-sm" style="max-height:200px;object-fit:cover;">
                </div>
                <div class="form-check mt-3 d-flex justify-content-center">
                    <input class="form-check-input me-2" type="checkbox" value="1" id="deleteHistoryConfirm">
                    <label class="form-check-label text-dark" style="color:#000 !important;" for="deleteHistoryConfirm">Saya yakin menghapus event ini secara permanen.</label>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <form id="deleteHistoryForm" action="#" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" id="deleteHistoryConfirmBtn" disabled>Hapus</button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
@endsection