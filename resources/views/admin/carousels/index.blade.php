@extends('layouts.admin')
@section('title', 'Kelola Carousel')
@section('content')
<style>
    .carousel-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        overflow: hidden;
    }
    .carousel-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
        border-color: #0d6efd;
    }
    .carousel-image-wrapper {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    .carousel-image-wrapper img {
        transition: transform 0.5s ease;
    }
    .carousel-card:hover .carousel-image-wrapper img {
        transform: scale(1.1);
    }
    .location-tab {
        transition: all 0.3s ease;
        border-radius: 8px;
        font-weight: 500;
    }
    .location-tab:hover {
        transform: translateY(-2px);
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
    }
    .empty-state {
        padding: 4rem 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 16px;
    }
</style>

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">
            <i class="bi bi-images text-primary me-2"></i>Kelola Carousel
        </h4>
        <p class="text-muted mb-0">Kelola gambar carousel untuk dashboard, event, course, dan landing page</p>
    </div>
    <a href="{{ route('admin.carousels.create', ['location' => $location]) }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-circle me-2"></i>Tambah Carousel
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card shadow-sm">
            <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                    <div class="small opacity-75">Total Carousel</div>
                    <div class="h4 mb-0 fw-bold">{{ $carousels->count() }}</div>
                </div>
                <i class="bi bi-images fs-1 opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small text-muted">Aktif</div>
                        <div class="h4 mb-0 fw-bold text-success">{{ $carousels->where('is_active', true)->count() }}</div>
                    </div>
                    <i class="bi bi-check-circle-fill text-success fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small text-muted">Nonaktif</div>
                        <div class="h4 mb-0 fw-bold text-secondary">{{ $carousels->where('is_active', false)->count() }}</div>
                    </div>
                    <i class="bi bi-x-circle-fill text-secondary fs-3"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small text-muted">Dengan Link</div>
                        <div class="h4 mb-0 fw-bold text-info">{{ $carousels->whereNotNull('link_url')->count() }}</div>
                    </div>
                    <i class="bi bi-link-45deg text-info fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter by Location -->
<div class="card shadow-sm mb-4 border-0">
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted fw-semibold me-2">
                <i class="bi bi-funnel me-1"></i>Filter Lokasi:
            </span>
            @foreach($locations as $key => $label)
                <a href="{{ route('admin.carousels.index', ['location' => $key]) }}" 
                   class="location-tab btn {{ $location === $key ? 'btn-primary shadow-sm' : 'btn-outline-secondary' }}">
                    <i class="bi bi-{{ $key === 'dashboard' ? 'speedometer2' : ($key === 'event' ? 'calendar-event' : ($key === 'course' ? 'book' : 'house')) }} me-1"></i>
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Carousel List -->
<div class="row g-4">
    @forelse($carousels as $carousel)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 carousel-card shadow-sm border-0">
                <div class="carousel-image-wrapper" style="height: 220px;">
                    <img src="{{ $carousel->image_url }}" 
                         alt="{{ $carousel->title ?? 'Carousel' }}" 
                         class="w-100 h-100" 
                         style="object-fit: cover;"
                         onerror="this.src='{{ asset('aset/poster.png') }}'">
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge rounded-pill px-3 py-2 shadow-sm {{ $carousel->is_active ? 'bg-success' : 'bg-secondary' }}">
                            <i class="bi bi-{{ $carousel->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                            {{ $carousel->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    @if($carousel->link_url)
                        <div class="position-absolute bottom-0 start-0 m-2">
                            <span class="badge bg-info rounded-pill px-3 py-2 shadow-sm">
                                <i class="bi bi-link-45deg me-1"></i>Link
                            </span>
                        </div>
                    @endif
                    <div class="position-absolute top-0 start-0 m-2">
                        <span class="badge bg-dark bg-opacity-75 rounded-pill px-2 py-1">
                            #{{ $carousel->order }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <h6 class="card-title mb-3 fw-semibold">
                        {{ $carousel->title ?? 'Tanpa Judul' }}
                    </h6>
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                            <span class="fw-medium">{{ $locations[$carousel->location] ?? $carousel->location }}</span>
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-sort-numeric-down text-info me-2"></i>
                            Urutan: <span class="fw-medium">{{ $carousel->order }}</span>
                        </div>
                        @if($carousel->link_url)
                            <div class="mb-2">
                                <i class="bi bi-link-45deg text-success me-2"></i>
                                <a href="{{ $carousel->link_url }}" target="_blank" class="text-decoration-none text-truncate d-inline-block" style="max-width: 200px;">
                                    {{ Str::limit($carousel->link_url, 30) }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top pt-3 pb-3">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" 
                                class="btn btn-sm flex-fill btn-outline-{{ $carousel->is_active ? 'warning' : 'success' }} toggle-active-btn"
                                data-carousel-id="{{ $carousel->id }}"
                                data-is-active="{{ $carousel->is_active ? '1' : '0' }}">
                            <i class="bi bi-{{ $carousel->is_active ? 'eye-slash' : 'eye' }} me-1"></i>
                            {{ $carousel->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                        <a href="{{ route('admin.carousels.edit', $carousel) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <button type="button" 
                                class="btn btn-sm btn-danger btn-delete-carousel"
                                data-carousel-id="{{ $carousel->id }}"
                                data-carousel-title="{{ $carousel->title ?? 'Carousel' }}"
                                data-delete-url="{{ route('admin.carousels.destroy', $carousel) }}">
                            <i class="bi bi-trash me-1"></i>Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state text-center">
                <div class="mb-4">
                    <i class="bi bi-images" style="font-size: 5rem; color: #adb5bd;"></i>
                </div>
                <h5 class="fw-bold mb-2">Belum Ada Carousel</h5>
                <p class="text-muted mb-4">Mulai dengan menambahkan carousel pertama untuk lokasi <strong>{{ $locations[$location] ?? $location }}</strong></p>
                <a href="{{ route('admin.carousels.create', ['location' => $location]) }}" class="btn btn-primary btn-lg shadow-sm">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Carousel Pertama
                </a>
            </div>
        </div>
    @endforelse
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCarouselModal" tabindex="-1" aria-labelledby="deleteCarouselLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0 fw-bold" id="deleteCarouselLabel">Konfirmasi Hapus</h5>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="mb-2">Anda yakin ingin menghapus carousel <strong id="deleteCarouselTitle" class="text-danger">-</strong>?</p>
                <p class="text-muted small mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Tindakan ini tidak dapat dibatalkan dan gambar akan dihapus dari server.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Batal
                </button>
                <form id="deleteCarouselForm" action="#" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger shadow-sm">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Active Status
    document.querySelectorAll('.toggle-active-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const carouselId = this.getAttribute('data-carousel-id');
            const isActive = this.getAttribute('data-is-active') === '1';
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value || '';
            
            fetch(`/admin/carousels/${carouselId}/toggle-active`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengubah status');
            });
        });
    });

    // Delete Carousel
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCarouselModal'));
    const deleteForm = document.getElementById('deleteCarouselForm');
    const deleteTitle = document.getElementById('deleteCarouselTitle');

    document.querySelectorAll('.btn-delete-carousel').forEach(btn => {
        btn.addEventListener('click', function() {
            const title = this.getAttribute('data-carousel-title');
            const url = this.getAttribute('data-delete-url');
            
            deleteTitle.textContent = title;
            deleteForm.action = url;
            deleteModal.show();
        });
    });
});
</script>
@endsection

