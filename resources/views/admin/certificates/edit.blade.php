@extends('layouts.crm')

@section('title', 'Pengaturan Sertifikat - ' . $event->title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
        <h4 class="mb-0 text-dark fw-semibold"><i class="bi bi-award me-2"></i>Pengaturan Sertifikat</h4>
            <p class="text-muted small mb-0">Event: <strong>{{ $event->title }}</strong></p>
        </div>
    <a href="{{ route('admin.crm.certificates.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('admin.crm.certificates.update', $event) }}" method="POST" enctype="multipart/form-data" id="certificateForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-award text-primary me-2" style="font-size: 1.2rem;"></i>
                                <h6 class="fw-semibold mb-0">Logo & Tanda Tangan Sertifikat</h6>
                            </div>
                            <p class="text-muted small mb-4">Upload logo dan tanda tangan yang akan digunakan pada sertifikat event ini. Anda dapat upload lebih dari satu logo dan tanda tangan. Semua logo akan ditampilkan di bagian atas sertifikat dengan ukuran yang sama, sedangkan semua tanda tangan akan ditampilkan di bagian bawah.</p>
                            
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="certificate_logo" class="form-label fw-semibold">
                                        <i class="bi bi-image me-1"></i>Logo Sertifikat (Bisa Multiple)
                                    </label>
                                    <input type="file" name="certificate_logo[]" id="certificate_logo" class="form-control" accept="image/*" multiple>
                                    <div class="form-text small text-muted">Format: PNG/JPG, maksimal 2MB per file. Pilih beberapa file untuk upload multiple logo.</div>
                                    
                                    @php
                                        $logos = is_array($event->certificate_logo) ? $event->certificate_logo : ($event->certificate_logo ? [$event->certificate_logo] : []);
                                    @endphp
                                    @if(count($logos) > 0)
                                    <div class="mt-3">
                                        <div class="small text-muted mb-2">Logo yang sudah diupload ({{ count($logos) }}):</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($logos as $logo)
                                            @php
                                                $logoPath = str_replace('storage/', '', $logo);
                                                $logoUrl = Storage::url($logoPath);
                                            @endphp
                                            <div class="position-relative border rounded p-2 bg-light" style="width:120px;">
                                                <img src="{{ $logoUrl }}" alt="Logo" class="img-thumbnail rounded mb-1" style="width:100px;height:60px;object-fit:contain;" onerror="this.src='{{ asset('aset/profile.png') }}'">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" style="width:24px;height:24px;padding:0;font-size:12px;" onclick="deleteLogo('{{ $logo }}', this)" title="Hapus">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                                <input type="hidden" name="delete_logos[]" id="delete_logo_{{ str_replace(['/', '\\', '.', '-'], '_', $logo) }}" value="">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @else
                                    <div class="mt-3 p-3 border rounded bg-light text-center">
                                        <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                        <div class="small text-muted mt-2">Belum ada logo</div>
                                    </div>
                                    @endif
                                    
                                    <div id="logoPreview" class="mt-3" style="display:none;">
                                        <div class="small text-muted mb-2">Preview logo baru:</div>
                                        <div id="logoPreviewContainer" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="certificate_signature" class="form-label fw-semibold">
                                        <i class="bi bi-pen me-1"></i>Tanda Tangan Sertifikat (Bisa Multiple)
                                    </label>
                                    <input type="file" name="certificate_signature[]" id="certificate_signature" class="form-control" accept="image/*" multiple>
                                    <div class="form-text small text-muted">Format: PNG/JPG, maksimal 2MB per file. Pilih beberapa file untuk upload multiple tanda tangan.</div>
                                    
                                    @php
                                        $signatures = is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : []);
                                    @endphp
                                    @if(count($signatures) > 0)
                                    <div class="mt-3">
                                        <div class="small text-muted mb-2">Tanda tangan yang sudah diupload ({{ count($signatures) }}):</div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($signatures as $signature)
                                            @php
                                                $sigPath = str_replace('storage/', '', $signature);
                                                $sigUrl = Storage::url($sigPath);
                                            @endphp
                                            <div class="position-relative border rounded p-2 bg-light" style="width:120px;">
                                                <img src="{{ $sigUrl }}" alt="Signature" class="img-thumbnail rounded mb-1" style="width:100px;height:60px;object-fit:contain;" onerror="this.src='{{ asset('aset/profile.png') }}'">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" style="width:24px;height:24px;padding:0;font-size:12px;" onclick="deleteSignature('{{ $signature }}', this)" title="Hapus">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                                <input type="hidden" name="delete_signatures[]" id="delete_signature_{{ str_replace(['/', '\\', '.', '-'], '_', $signature) }}" value="">
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @else
                                    <div class="mt-3 p-3 border rounded bg-light text-center">
                                        <i class="bi bi-pen text-muted" style="font-size: 2rem;"></i>
                                        <div class="small text-muted mt-2">Belum ada tanda tangan</div>
                                    </div>
                                    @endif
                                    
                                    <div id="signaturePreview" class="mt-3" style="display:none;">
                                        <div class="small text-muted mb-2">Preview tanda tangan baru:</div>
                                        <div id="signaturePreviewContainer" class="d-flex flex-wrap gap-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                            <a href="{{ route('admin.crm.certificates.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Nama Event</small>
                        <strong>{{ $event->title }}</strong>
                    </div>
                    @if($event->event_date)
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Tanggal Event</small>
                        <strong>{{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}</strong>
                    </div>
                    @endif
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Jumlah Peserta</small>
                        <strong>{{ $event->registrations()->count() }} peserta</strong>
                    </div>
                    <hr>
                    <div class="alert alert-info small mb-0">
                        <strong>Tips:</strong> Gunakan logo dengan latar belakang transparan (PNG) untuk hasil terbaik. Tanda tangan sebaiknya dalam format PNG dengan latar belakang transparan. Semua logo dan tanda tangan akan ditampilkan dengan ukuran yang sama pada sertifikat.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Certificate logo preview (multiple)
    const logoInp = document.getElementById('certificate_logo');
    logoInp?.addEventListener('change', function(ev) {
        const files = Array.from(ev.target.files);
        const wrap = document.getElementById('logoPreview');
        const container = document.getElementById('logoPreviewContainer');
        
        if(!files || files.length === 0) {
            wrap.style.display = 'none';
            return;
        }
        
        container.innerHTML = '';
        
        files.forEach((f, index) => {
            const sizeMB = f.size / (1024 * 1024);
            if(sizeMB > 2) {
                alert(`Logo ${index + 1} melebihi 2MB. File akan diabaikan.`);
                return;
            }
            
            const r = new FileReader();
            r.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'border rounded p-2 bg-light';
                div.style.width = '120px';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="img-thumbnail rounded mb-1" style="width:100px;height:60px;object-fit:contain;">
                    <div class="small text-muted">Logo ${index + 1}</div>
                `;
                container.appendChild(div);
            };
            r.readAsDataURL(f);
        });
        
        if(container.children.length > 0) {
            wrap.style.display = 'block';
        }
    });

    // Certificate signature preview (multiple)
    const sigInp = document.getElementById('certificate_signature');
    sigInp?.addEventListener('change', function(ev) {
        const files = Array.from(ev.target.files);
        const wrap = document.getElementById('signaturePreview');
        const container = document.getElementById('signaturePreviewContainer');
        
        if(!files || files.length === 0) {
            wrap.style.display = 'none';
            return;
        }
        
        container.innerHTML = '';
        
        files.forEach((f, index) => {
            const sizeMB = f.size / (1024 * 1024);
            if(sizeMB > 2) {
                alert(`Tanda tangan ${index + 1} melebihi 2MB. File akan diabaikan.`);
                return;
            }
            
            const r = new FileReader();
            r.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'border rounded p-2 bg-light';
                div.style.width = '120px';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" class="img-thumbnail rounded mb-1" style="width:100px;height:60px;object-fit:contain;">
                    <div class="small text-muted">TTD ${index + 1}</div>
                `;
                container.appendChild(div);
            };
            r.readAsDataURL(f);
        });
        
        if(container.children.length > 0) {
            wrap.style.display = 'block';
        }
    });
});

function deleteLogo(logoPath, btn) {
    if(confirm('Hapus logo ini?')) {
        const inputId = 'delete_logo_' + logoPath.replace(/[\/\\\.\-]/g, '_');
        const input = document.getElementById(inputId);
        if(input) {
            input.value = logoPath;
        }
        btn.closest('.position-relative').style.display = 'none';
    }
}

function deleteSignature(sigPath, btn) {
    if(confirm('Hapus tanda tangan ini?')) {
        const inputId = 'delete_signature_' + sigPath.replace(/[\/\\\.\-]/g, '_');
        const input = document.getElementById(inputId);
        if(input) {
            input.value = sigPath;
        }
        btn.closest('.position-relative').style.display = 'none';
    }
}
</script>
@endsection