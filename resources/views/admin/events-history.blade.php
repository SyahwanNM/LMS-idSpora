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
                                        <form action="{{ route('admin.events.destroy',$event) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus event ini secara permanen?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                                        </form>
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
});
</script>
@endsection