@extends('layouts.admin')
@section('title', 'Edit Carousel')
@section('content')
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .preview-container {
        border: 2px dashed #dee2e6;
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    .preview-container.has-image {
        border-color: #0d6efd;
        background: #f0f7ff;
    }
    .preview-container img {
        max-width: 100%;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .current-image {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Carousel
        </h4>
        <p class="text-muted mb-0">Edit informasi dan gambar carousel</p>
    </div>
    <a href="{{ route('admin.carousels.index', ['location' => $carousel->location]) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form action="{{ route('admin.carousels.update', $carousel) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-section">
                        <h6 class="fw-semibold mb-3">
                            <i class="bi bi-info-circle me-2 text-primary"></i>Informasi Dasar
                        </h6>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label fw-semibold">
                                <i class="bi bi-geo-alt me-2"></i>Lokasi <span class="text-danger">*</span>
                            </label>
                            <select name="location" id="location" class="form-select @error('location') is-invalid @enderror" required>
                                <option value="">Pilih Lokasi</option>
                                @foreach($locations as $key => $label)
                                    <option value="{{ $key }}" {{ old('location', $carousel->location) === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="title" class="form-label fw-semibold">
                                <i class="bi bi-type me-2"></i>Judul (Alt Text)
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title', $carousel->title) }}"
                                   placeholder="Contoh: Promo Event Spesial">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Judul akan digunakan sebagai alt text untuk aksesibilitas
                            </small>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-image me-2"></i>Gambar
                        </label>
                        <div class="mb-3">
                            <p class="small text-muted mb-2">Gambar Saat Ini:</p>
                            <img src="{{ $carousel->image_url }}" 
                                 alt="{{ $carousel->title ?? 'Carousel' }}" 
                                 class="current-image img-fluid" 
                                 style="max-height: 250px;"
                                 onerror="this.src='{{ asset('aset/poster.png') }}'">
                        </div>
                        <label for="image" class="form-label fw-semibold">Ganti Gambar (Opsional)</label>
                        <div class="preview-container" id="previewContainer">
                            <div id="previewPlaceholder">
                                <i class="bi bi-cloud-upload fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-2">Klik untuk memilih gambar baru atau drag & drop</p>
                                <p class="small text-muted">Kosongkan jika tidak ingin mengganti gambar</p>
                            </div>
                            <div id="imagePreview" style="display: none;">
                                <p class="small text-success mb-2"><i class="bi bi-check-circle me-1"></i>Preview Gambar Baru:</p>
                                <img id="previewImg" src="" alt="Preview" class="img-fluid">
                            </div>
                        </div>
                        <input type="file" 
                               name="image" 
                               id="image" 
                               class="form-control mt-3 @error('image') is-invalid @enderror" 
                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                               onchange="previewImage(this)">
                        <div class="mt-2">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Format: JPG, PNG, GIF, WEBP (Maks. 5MB). 
                                <strong>Ukuran Optimal:</strong> 1920x600px atau 1350x400px
                            </small>
                        </div>
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-section">
                        <h6 class="fw-semibold mb-3">
                            <i class="bi bi-link-45deg me-2 text-primary"></i>Pengaturan Tambahan
                        </h6>
                        
                        <div class="mb-3">
                            <label for="link_url" class="form-label fw-semibold">
                                <i class="bi bi-link-45deg me-2"></i>Link URL (Opsional)
                            </label>
                            <input type="url" 
                                   name="link_url" 
                                   id="link_url" 
                                   class="form-control @error('link_url') is-invalid @enderror" 
                                   value="{{ old('link_url', $carousel->link_url) }}"
                                   placeholder="https://example.com">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                URL yang akan dibuka saat gambar diklik
                            </small>
                            @error('link_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="order" class="form-label fw-semibold">
                                    <i class="bi bi-sort-numeric-down me-2"></i>Urutan
                                </label>
                                <input type="number" 
                                       name="order" 
                                       id="order" 
                                       class="form-control @error('order') is-invalid @enderror" 
                                       value="{{ old('order', $carousel->order) }}"
                                       min="0">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Angka lebih kecil akan ditampilkan lebih dulu
                                </small>
                                @error('order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-toggle-on me-2"></i>Status
                                </label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="is_active" 
                                           id="is_active" 
                                           value="1"
                                           {{ old('is_active', $carousel->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Aktifkan carousel ini
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                        <a href="{{ route('admin.carousels.index', ['location' => $carousel->location]) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary shadow-sm">
                            <i class="bi bi-check-circle me-2"></i>Update Carousel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const previewPlaceholder = document.getElementById('previewPlaceholder');
    const previewContainer = document.getElementById('previewContainer');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
            previewPlaceholder.style.display = 'none';
            previewContainer.classList.add('has-image');
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        previewPlaceholder.style.display = 'block';
        previewContainer.classList.remove('has-image');
    }
}
</script>
@endsection

