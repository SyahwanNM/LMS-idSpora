@extends('layouts.admin')

@section('title', 'Detail Event')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-dark">
                            <i class="bi bi-calendar-event me-2"></i>
                            Detail Event
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Event Image -->
                        <div class="col-lg-4 mb-4">
                            <div class="position-relative event-preview-wrapper">
                                @if($event->image)
                                    <figure class="event-image-figure mb-0" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" style="cursor:zoom-in;">
                                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                                             class="img-fluid rounded shadow-sm event-main-image">
                                        <figcaption class="event-image-overlay small">
                                            <i class="bi bi-arrows-fullscreen me-1"></i> Klik untuk perbesar
                                        </figcaption>
                                    </figure>
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center no-image-block">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                                            <p class="mt-2 mb-0">No Image</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <h2 class="text-dark mb-3">{{ $event->title }}</h2>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-fill text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Pembicara</small>
                                                <strong>{{ $event->speaker }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-journal-text text-secondary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Materi</small>
                                                <strong>{{ $event->materi ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt-fill text-success me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Lokasi</small>
                                                <strong>{{ $event->location }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-diagram-3 text-dark me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Jenis</small>
                                                <strong>{{ $event->jenis ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-date text-info me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Tanggal</small>
                                                <strong>{{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock text-warning me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Waktu</small>
                                                <strong>{{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }} WIB</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-currency-dollar text-success me-2"></i>
                                        <small class="text-muted">Harga Tiket</small>
                                    </div>
                                    @php
                                        $isFree = (int)$event->price === 0;
                                    @endphp
                                    @if($isFree)
                                        <h4 class="text-success mb-0">Gratis</h4>
                                    @elseif($event->hasDiscount())
                                        <div class="d-flex align-items-baseline flex-wrap gap-2">
                                            <span class="text-muted text-decoration-line-through">Rp{{ number_format($event->price, 0, ',', '.') }}</span>
                                            <h4 class="text-success mb-0">Rp{{ number_format($event->discounted_price, 0, ',', '.') }}</h4>
                                            <span class="badge bg-danger">-{{ $event->discount_percentage }}%</span>
                                        </div>
                                    @else
                                        <h4 class="text-success mb-0">Rp{{ number_format($event->price, 0, ',', '.') }}</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Description -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="border-top pt-4">
                                <h5 class="text-dark mb-3">
                                    <i class="bi bi-file-text me-2"></i>Deskripsi Event
                                </h5>
                                <div class="bg-light rounded p-4">
                                    <div class="event-description">
                                        {!! $event->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                                </a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary btn-lg px-4">
                                    <i class="bi bi-pencil me-1"></i> Edit Event
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.event-description {
    line-height: 1.6;
    color: #333;
}

.event-description h1,
.event-description h2,
.event-description h3,
.event-description h4,
.event-description h5,
.event-description h6 {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.event-description p {
    margin-bottom: 1rem;
}

.event-description ul,
.event-description ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.event-description blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6c757d;
}

.event-description img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

.event-description table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.event-description table th,
.event-description table td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}

.event-description table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
@endsection

@section('scripts')
@if($event->image)
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg image-preview-modal">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title small text-muted" id="imagePreviewLabel">Preview Gambar Event</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="image-preview-container">
                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="preview-full-image" id="previewFullImage">
                </div>
            </div>
            <div class="modal-footer justify-content-between py-2 border-0">
                <div class="d-flex gap-2 align-items-center small text-muted flex-wrap">
                        <span><i class="bi bi-image me-1"></i>Resolusi asli ditampilkan proporsional</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnZoomIn"><i class="bi bi-zoom-in"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnZoomOut"><i class="bi bi-zoom-out"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetZoom"><i class="bi bi-aspect-ratio"></i></button>
                        <a href="{{ Storage::url($event->image) }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i> Buka Tab</a>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
        const img = document.getElementById('previewFullImage');
        if(!img) return;
        let scale = 1;
        const step = 0.15;
        const maxScale = 3;
        const minScale = 0.4;
        const zoomInBtn = document.getElementById('btnZoomIn');
        const zoomOutBtn = document.getElementById('btnZoomOut');
        const resetBtn = document.getElementById('btnResetZoom');
        function apply(){ img.style.transform = `scale(${scale})`; }
        zoomInBtn.addEventListener('click', ()=>{ if(scale < maxScale){ scale += step; apply(); }});
        zoomOutBtn.addEventListener('click', ()=>{ if(scale > minScale){ scale -= step; apply(); }});
        resetBtn.addEventListener('click', ()=>{ scale = 1; apply(); });
        // Drag to pan when zoomed
        let isDown = false, startX, startY, scrollLeft, scrollTop;
        const container = document.querySelector('.image-preview-container');
        container.addEventListener('mousedown', (e)=>{ if(scale<=1) return; isDown=true; container.classList.add('dragging'); startX=e.pageX - container.offsetLeft; startY=e.pageY - container.offsetTop; scrollLeft=container.scrollLeft; scrollTop=container.scrollTop; });
        container.addEventListener('mouseleave', ()=>{ isDown=false; container.classList.remove('dragging'); });
        container.addEventListener('mouseup', ()=>{ isDown=false; container.classList.remove('dragging'); });
        container.addEventListener('mousemove', (e)=>{ if(!isDown) return; e.preventDefault(); const x = e.pageX - container.offsetLeft; const y = e.pageY - container.offsetTop; const walkX = (x - startX); const walkY = (y - startY); container.scrollLeft = scrollLeft - walkX; container.scrollTop = scrollTop - walkY; });
        // Wheel zoom (Ctrl + wheel)
        container.addEventListener('wheel', (e)=>{ if(!e.ctrlKey) return; e.preventDefault(); if(e.deltaY < 0 && scale < maxScale){ scale += step; } else if(e.deltaY > 0 && scale > minScale){ scale -= step; } apply(); }, { passive:false });
        // Reset zoom each time modal opens
        const modalEl = document.getElementById('imagePreviewModal');
        modalEl.addEventListener('show.bs.modal', ()=>{ scale=1; apply(); container.scrollTo({top:0,left:0}); });
});
</script>
<style>
/* Image preview enhancements */
.event-preview-wrapper .event-main-image { width:100%; height:300px; object-fit:cover; border-radius:14px; }
@media (max-width:575.98px){ .event-preview-wrapper .event-main-image { height:240px; } }
.event-image-figure { position:relative; }
.event-image-overlay { position:absolute; inset:0; display:flex; align-items:flex-end; justify-content:flex-start; padding:10px 14px; background:linear-gradient(to top,rgba(0,0,0,.55),rgba(0,0,0,0)); color:#f1f5f9; opacity:0; transition:opacity .35s; border-radius:14px; font-size:.75rem; letter-spacing:.5px; font-weight:500; }
.event-image-figure:hover .event-image-overlay { opacity:1; }
.image-preview-modal .modal-content { border-radius:20px; }
.image-preview-container { max-height:70vh; overflow:auto; background:#0f172a; border-radius:14px; padding:12px; display:flex; align-items:center; justify-content:center; }
.image-preview-container.dragging { cursor:grabbing; }
.preview-full-image { max-width:100%; height:auto; transition:transform .25s ease; transform-origin:center center; user-select:none; }
.image-preview-container::-webkit-scrollbar { width:10px; height:10px; }
.image-preview-container::-webkit-scrollbar-thumb { background:#334155; border-radius:20px; }
.image-preview-container::-webkit-scrollbar-track { background:transparent; }
</style>
@endif
@endsection