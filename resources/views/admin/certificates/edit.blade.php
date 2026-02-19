@extends('layouts.crm')

@section('title', 'Konfigurasi Sertifikat - ' . $event->title)

@section('styles')
<style>
    .template-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    .template-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .template-card.active {
        border-color: var(--crm-primary);
        box-shadow: 0 0 0 4px rgba(109, 40, 217, 0.1);
    }
    .template-card .check-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: var(--crm-primary);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    .template-card.active .check-badge {
        display: flex;
    }
    .template-preview-img {
        width: 100%;
        height: 120px;
        object-fit: cover;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--crm-text-muted);
        font-size: 0.8rem;
    }
    .asset-preview {
        width: 100px;
        height: 60px;
        object-fit: contain;
        background: white;
        border: 1px solid var(--crm-border);
        border-radius: 8px;
    }
    .delete-overlay {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ef4444;
        color: white;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
    }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h3 class="fw-bold text-navy mb-1">Konfigurasi Sertifikat</h3>
        <p class="text-muted small mb-0">Event: <span class="text-primary fw-medium">{{ $event->title }}</span></p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.crm.certificates.index') }}" class="btn btn-outline-secondary btn-sm bg-white">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.crm.certificates.update', $event) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <!-- Main Configuration -->
        <div class="col-lg-8">
            <!-- Step 1: Choose Template -->
            <div class="card-minimal mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">Langkah 1: Pilih Template Desain</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="template-card border {{ ($event->certificate_template ?? 'template_1') == 'template_1' ? 'active' : '' }}" onclick="selectTemplate('template_1', this)">
                                <div class="check-badge"><i class="bi bi-check-lg"></i></div>
                                <div class="template-preview-img" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #fbbf24;">
                                    <div class="text-center">
                                        <i class="bi bi-award fs-2"></i>
                                        <div class="smaller mt-1">Classic Royal</div>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <div class="fw-bold small mb-1">Classic Royal</div>
                                    <div class="text-muted smaller">Elegan dengan aksen emas dan navy.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="template-card border {{ ($event->certificate_template ?? 'template_1') == 'template_2' ? 'active' : '' }}" onclick="selectTemplate('template_2', this)">
                                <div class="check-badge"><i class="bi bi-check-lg"></i></div>
                                <div class="template-preview-img" style="background: #ffffff; color: #1e293b; border-bottom: 1px solid #e2e8f0;">
                                    <div class="text-center">
                                        <i class="bi bi-file-earmark-text fs-2"></i>
                                        <div class="smaller mt-1">Modern Minimal</div>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <div class="fw-bold small mb-1">Modern Minimal</div>
                                    <div class="text-muted smaller">Bersih, fokus pada tipografi modern.</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="template-card border {{ ($event->certificate_template ?? 'template_1') == 'template_3' ? 'active' : '' }}" onclick="selectTemplate('template_3', this)">
                                <div class="check-badge"><i class="bi bi-check-lg"></i></div>
                                <div class="template-preview-img" style="background: linear-gradient(135deg, #6d28d9 0%, #db2777 100%); color: #ffffff;">
                                    <div class="text-center">
                                        <i class="bi bi-palette fs-2"></i>
                                        <div class="smaller mt-1">Creative Dynamic</div>
                                    </div>
                                </div>
                                <div class="p-3">
                                    <div class="fw-bold small mb-1">Creative Dynamic</div>
                                    <div class="text-muted smaller">Enerjik dengan gradien dan pola.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="certificate_template" id="selected_template" value="{{ $event->certificate_template ?? 'template_1' }}">
                </div>
            </div>

            <!-- Step 2: Assets Upload -->
            <div class="card-minimal mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">Langkah 2: Kelola Aset (Logo & TTD)</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Logos -->
                        <div class="col-md-6">
                            <label class="form-label smaller fw-bold text-muted">Upload Logo Partner</label>
                            <input type="file" name="certificate_logo[]" class="form-control form-control-sm mb-3" accept="image/*" multiple id="logoInput">
                            
                            <div id="existingLogos" class="d-flex flex-wrap gap-2">
                                @php $logos = is_array($event->certificate_logo) ? $event->certificate_logo : ($event->certificate_logo ? [$event->certificate_logo] : []); @endphp
                                @foreach($logos as $logo)
                                    <div class="position-relative">
                                        <img src="{{ Storage::url(str_replace('storage/', '', $logo)) }}" class="asset-preview">
                                        <div class="delete-overlay" onclick="markDelete('logo', '{{ $logo }}', this)"><i class="bi bi-x"></i></div>
                                        <input type="hidden" name="delete_logos[]" value="">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Signatures -->
                        <div class="col-md-6">
                            <label class="form-label smaller fw-bold text-muted">Upload Tanda Tangan</label>
                            <input type="file" name="certificate_signature[]" class="form-control form-control-sm mb-3" accept="image/*" multiple id="sigInput">
                            
                            <div id="existingSigs" class="d-flex flex-wrap gap-2">
                                @php $sigs = is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : []); @endphp
                                @foreach($sigs as $sig)
                                    <div class="position-relative">
                                        <img src="{{ Storage::url(str_replace('storage/', '', $sig)) }}" class="asset-preview">
                                        <div class="delete-overlay" onclick="markDelete('sig', '{{ $sig }}', this)"><i class="bi bi-x"></i></div>
                                        <input type="hidden" name="delete_signatures[]" value="">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3">
                <button type="submit" class="btn btn-navy px-5" style="background: var(--crm-navy); color: white;">Simpan Konfigurasi</button>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card-minimal h-100 p-4">
                <h6 class="fw-bold text-navy mb-3">Panduan Desain</h6>
                <div class="d-flex align-items-start mb-3">
                    <div class="me-3 text-primary"><i class="bi bi-info-circle fs-4"></i></div>
                    <div class="smaller text-muted">
                        <b>Logo Default:</b> Logo IdSPora akan selalu muncul sebagai identitas penyelenggara utama.
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <div class="me-3 text-warning"><i class="bi bi-layers fs-4"></i></div>
                    <div class="smaller text-muted">
                        <b>Partner Logo:</b> Digunakan jika event bekerja sama dengan instansi lain. Max 3 logo tambahan.
                    </div>
                </div>
                <div class="d-flex align-items-start mb-4">
                    <div class="me-3 text-success"><i class="bi bi-pen fs-4"></i></div>
                    <div class="smaller text-muted">
                        <b>Tanda Tangan:</b> Pastikan menggunakan latar belakang transparan (PNG) untuk estetika maksimal.
                    </div>
                </div>

                <div class="alert alert-warning border-0 smaller py-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Perubahan pada template akan langsung berdampak pada sertifikat yang sudah diunduh oleh user.
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function selectTemplate(id, element) {
        document.querySelectorAll('.template-card').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('selected_template').value = id;
    }

    function markDelete(type, path, element) {
        if(confirm('Hapus aset ini?')) {
            const input = element.nextElementSibling;
            input.value = path;
            element.parentElement.style.opacity = '0.3';
            element.parentElement.style.pointerEvents = 'none';
        }
    }
</script>
@endsection