@extends('layouts.admin')

@section('title', 'Manage Events')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Event
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($events->count() > 0)
        <div class="row g-4">
            @foreach($events as $event)
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    @if($event->image)
                        <div class="position-relative">
                            <img src="{{ Storage::url($event->image) }}"
                                 class="card-img-top event-preview-image"
                                 alt="{{ $event->title }}"
                                 data-title="{{ $event->title }}"
                                 data-src="{{ Storage::url($event->image) }}"
                                 style="height:200px;object-fit:cover;cursor:zoom-in;">
                            <span class="badge bg-dark bg-opacity-75 position-absolute top-0 end-0 m-2 small">Klik untuk perbesar</span>
                        </div>
                    @else
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height:200px;">
                            <span>No Image</span>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text mb-1"><strong>Speaker:</strong> {{ $event->speaker }}</p>
                        <p class="card-text mb-1"><strong>Date:</strong> {{ $event->event_date }} <strong>Time:</strong> {{ $event->event_time }}</p>
                        <p class="card-text mb-1"><strong>Location:</strong> {{ $event->location }}</p>
                        <div class="card-text mb-2"><strong>Price:</strong>
                            @php $isFree = (int)$event->price === 0; @endphp
                            @if($isFree)
                                <span class="badge bg-success ms-1">Gratis</span>
                            @elseif($event->hasDiscount())
                                <span class="text-muted text-decoration-line-through ms-1">Rp{{ number_format($event->price,0,',','.') }}</span>
                                <span class="fw-semibold text-success ms-1">Rp{{ number_format($event->discounted_price,0,',','.') }}</span>
                                <span class="badge bg-danger ms-1">-{{ $event->discount_percentage }}%</span>
                            @else
                                <span class="ms-1">Rp{{ number_format($event->price,0,',','.') }}</span>
                            @endif
                        </div>
                        <p class="card-text text-muted event-description-preview" style="font-size: 0.95em;">{{ Str::limit(strip_tags($event->description), 80) }}</p>
                        <div class="mt-auto d-flex justify-content-between">
                            <a href="{{ route('admin.events.show', $event) }}" class="btn btn-outline-info btn-sm">View</a>
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="showDeleteModal('{{ $event->title }}', '{{ route('admin.events.destroy', $event) }}', '{{ $event->image ? Storage::url($event->image) : '' }}')">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $events->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <h5 class="mb-3">Belum ada event.</h5>
        </div>
    @endif
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteEventModalLabel">Konfirmasi Hapus Event</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalEventImage" src="#" alt="Event Image" style="max-width:100%;max-height:180px;border-radius:8px;display:none;margin-bottom:15px;">
        <p class="mb-0">Apakah Anda yakin ingin menghapus event <strong id="eventTitle"></strong>?</p>
        <small class="text-danger">Tindakan ini tidak dapat dibatalkan.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form id="deleteEventForm" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Hapus</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-dark text-white">
            <div class="modal-header border-secondary">
                <h5 class="modal-title" id="imagePreviewModalLabel">Pratinjau Gambar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="previewStage" class="w-100 position-relative d-flex align-items-center justify-content-center preview-stage">
                    <img id="previewModalImage" src="#" alt="Preview" class="img-fluid user-select-none" style="max-width:100%;max-height:80vh;cursor:grab;">
                    <div class="position-absolute top-0 start-0 m-2 small fw-semibold bg-dark bg-opacity-50 px-2 py-1 rounded" id="zoomIndicator">100%</div>
                </div>
            </div>
            <div class="modal-footer justify-content-between flex-wrap gap-2 border-secondary">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-light" id="zoomInBtn" title="Zoom In (+)"><i class="bi bi-zoom-in"></i></button>
                    <button type="button" class="btn btn-outline-light" id="zoomOutBtn" title="Zoom Out (-)"><i class="bi bi-zoom-out"></i></button>
                    <button type="button" class="btn btn-outline-light" id="resetZoomBtn" title="Reset (R)"><i class="bi bi-arrow-counterclockwise"></i></button>
                </div>
                <div class="d-flex align-items-center gap-2 small text-secondary flex-grow-1 justify-content-center" id="imageMeta"></div>
                <div class="d-flex gap-2">
                    <a href="#" id="openNewTabBtn" target="_blank" class="btn btn-outline-info btn-sm"><i class="bi bi-box-arrow-up-right"></i> Buka Tab</a>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Modal elements
    const deleteEventModal = document.getElementById('deleteEventModal');
    const eventTitleSpan = document.getElementById('eventTitle');
    const deleteEventForm = document.getElementById('deleteEventForm');
    const modalEventImage = document.getElementById('modalEventImage');

    window.showDeleteModal = function(eventTitle, deleteUrl, imageUrl) {
        eventTitleSpan.textContent = eventTitle;
        deleteEventForm.action = deleteUrl;
        if (imageUrl) {
            modalEventImage.src = imageUrl;
            modalEventImage.style.display = 'block';
        } else {
            modalEventImage.style.display = 'none';
        }
        const modal = new bootstrap.Modal(deleteEventModal);
        modal.show();
    }

    // Image Preview Logic
    const imagePreviewModalEl = document.getElementById('imagePreviewModal');
    const imagePreviewModal = imagePreviewModalEl ? new bootstrap.Modal(imagePreviewModalEl) : null;
    const previewImg = document.getElementById('previewModalImage');
    const zoomIndicator = document.getElementById('zoomIndicator');
    const imageMeta = document.getElementById('imageMeta');
    const openNewTabBtn = document.getElementById('openNewTabBtn');
    const stage = document.getElementById('previewStage');
    let scale = 1, minScale = 1, maxScale = 5, translateX = 0, translateY = 0;
    let isPanning = false, startX = 0, startY = 0, imgStartX = 0, imgStartY = 0;

    function applyTransform() {
        if(!previewImg) return;
        previewImg.style.transform = `translate(${translateX}px, ${translateY}px) scale(${scale})`;
        if(zoomIndicator) zoomIndicator.textContent = Math.round(scale * 100) + '%';
    }
    function resetZoom() { scale = 1; translateX = 0; translateY = 0; applyTransform(); }
    function updateMeta(src) {
        if(!imageMeta) return;
        const img = new Image();
        img.onload = () => { imageMeta.innerHTML = `<span class=\"text-light\">${img.width}x${img.height}px</span>`; };
        img.src = src;
    }
    function openImagePreview(src, title) {
        if(!imagePreviewModal || !previewImg) return;
        previewImg.src = src;
        const label = document.getElementById('imagePreviewModalLabel');
        if(label) label.textContent = title || 'Pratinjau Gambar';
        if(openNewTabBtn) openNewTabBtn.href = src;
        resetZoom();
        updateMeta(src);
        imagePreviewModal.show();
    }

    document.querySelectorAll('.event-preview-image').forEach(img => {
        img.addEventListener('click', () => {
            openImagePreview(img.dataset.src || img.src, img.dataset.title || img.alt);
        });
    });

    // Zoom Buttons
    const zoomInBtn = document.getElementById('zoomInBtn');
    const zoomOutBtn = document.getElementById('zoomOutBtn');
    const resetZoomBtn = document.getElementById('resetZoomBtn');
    zoomInBtn && zoomInBtn.addEventListener('click', () => { if(scale < maxScale){ scale = +(scale + 0.25).toFixed(2); applyTransform(); }});
    zoomOutBtn && zoomOutBtn.addEventListener('click', () => { if(scale > minScale){ scale = +(scale - 0.25).toFixed(2); if(scale < minScale) scale = minScale; applyTransform(); }});
    resetZoomBtn && resetZoomBtn.addEventListener('click', resetZoom);

    // Wheel Zoom
    stage && stage.addEventListener('wheel', e => {
        e.preventDefault();
        const delta = e.deltaY < 0 ? 0.1 : -0.1;
        let newScale = scale + delta;
        if(newScale < minScale) newScale = minScale;
        if(newScale > maxScale) newScale = maxScale;
        scale = +newScale.toFixed(2);
        applyTransform();
    }, { passive: false });

    // Pan logic
    previewImg && previewImg.addEventListener('mousedown', e => {
        if(scale === 1) return;
        isPanning = true; startX = e.clientX; startY = e.clientY; imgStartX = translateX; imgStartY = translateY; previewImg.style.cursor = 'grabbing';
    });
    window.addEventListener('mousemove', e => {
        if(!isPanning) return;
        translateX = imgStartX + (e.clientX - startX);
        translateY = imgStartY + (e.clientY - startY);
        applyTransform();
    });
    window.addEventListener('mouseup', () => { if(isPanning){ isPanning = false; if(previewImg) previewImg.style.cursor = scale>1 ? 'grab' : 'default'; }});

    imagePreviewModalEl && imagePreviewModalEl.addEventListener('hidden.bs.modal', resetZoom);
    previewImg && previewImg.addEventListener('dblclick', resetZoom);
    imagePreviewModalEl && imagePreviewModalEl.addEventListener('keydown', e => {
        if(e.key === '+'){ zoomInBtn && zoomInBtn.click(); }
        if(e.key === '-') { zoomOutBtn && zoomOutBtn.click(); }
        if(e.key && e.key.toLowerCase() === 'r'){ resetZoom(); }
    });
</script>
@endsection

@section('styles')
<style>
/* Event Description Preview Styling */
.event-description-preview {
    line-height: 1.4;
    color: #6c757d;
}

.event-description-preview strong {
    font-weight: 600;
    color: #495057;
}

.event-description-preview em {
    font-style: italic;
    color: #6c757d;
}

/* Image Preview Modal */
.preview-stage { background:#000; min-height:60vh; overflow:hidden; }
#previewModalImage { transition: transform .15s ease-out; will-change: transform; }
#zoomIndicator { font-size:.7rem; }
@media (max-width: 768px){ .preview-stage { min-height:50vh; } }
</style>
@endsection