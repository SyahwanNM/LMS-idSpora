@extends('layouts.crm')

@section('title', 'Konfigurasi Sertifikat Kursus - ' . $course->name)

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
        z-index: 10;
    }
    .logo-item-wrapper {
        overflow: visible !important;
    }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h3 class="fw-bold text-navy mb-1">Konfigurasi Sertifikat Kursus</h3>
        <p class="text-muted small mb-0">Kursus: <span class="text-primary fw-medium">{{ $course->name }}</span></p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.crm.certificates.index', ['tab' => 'courses']) }}" class="btn btn-outline-secondary btn-sm bg-white">
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

@if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center mb-1">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <span class="fw-bold">Gagal memperbarui konfigurasi:</span>
        </div>
        <ul class="mb-0 smaller">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.crm.certificates.update-course', $course) }}" method="POST" enctype="multipart/form-data">
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
                            <div class="template-card border {{ ($course->certificate_template ?? 'template_1') == 'template_1' ? 'active' : '' }}" onclick="selectTemplate('template_1', this)">
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
                            <div class="template-card border {{ ($course->certificate_template ?? 'template_1') == 'template_2' ? 'active' : '' }}" onclick="selectTemplate('template_2', this)">
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
                            <div class="template-card border {{ ($course->certificate_template ?? 'template_1') == 'template_3' ? 'active' : '' }}" onclick="selectTemplate('template_3', this)">
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
                    <input type="hidden" name="certificate_template" id="selected_template" value="{{ $course->certificate_template ?? 'template_1' }}">
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
                            <label class="form-label smaller fw-bold text-muted d-flex justify-content-between">
                                Upload Logo Partner
                                <span class="text-primary smaller" style="cursor: pointer;" id="addLogoBtn" onclick="addLogoField()">+ Tambah Baris</span>
                            </label>
                            <div id="logoUploadContainer">
                                <input type="file" name="certificate_logo[]" class="form-control form-control-sm mb-2" accept="image/*" onchange="previewNewAsset(this)">
                            </div>
                            <div class="smaller text-muted mb-3"><i class="bi bi-info-circle me-1"></i>Maksimal 3 logo partner tambahan.</div>
                            
                            <div id="existingLogos" class="d-flex flex-wrap gap-2">
                                @php $logos = is_array($course->certificate_logo) ? $course->certificate_logo : ($course->certificate_logo ? [$course->certificate_logo] : []); @endphp
                                @foreach($logos as $logo)
                                    <div class="position-relative logo-item-wrapper">
                                        <img src="{{ asset('uploads/' . $logo) }}" class="asset-preview">
                                        <div class="delete-overlay" onclick="markDelete('logo', '{{ $logo }}', this, event)"><i class="bi bi-x"></i></div>
                                        <input type="hidden" name="delete_logos[]" value="">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Signatures -->
                        <div class="col-12">
                            <label class="form-label smaller fw-bold text-muted d-flex justify-content-between">
                                Tanda Tangan
                                <span class="text-primary smaller" style="cursor: pointer;" onclick="addSignatureField()">+ Tambah Tanda Tangan</span>
                            </label>

                            <div id="signaturesContainer">
                                @php
                                    $sigs = is_array($course->certificate_signature)
                                        ? $course->certificate_signature
                                        : ($course->certificate_signature ? [$course->certificate_signature] : []);
                                @endphp

                                @forelse($sigs as $i => $sig)
                                    @php
                                        $isObj = is_array($sig);
                                        $sigPath = $isObj ? ($sig['image'] ?? '') : $sig;
                                        $sigName = $isObj ? ($sig['name'] ?? '') : '';
                                        $sigPos  = $isObj ? ($sig['position'] ?? '') : '';
                                    @endphp
                                    <div class="sig-entry card border mb-3 p-3" style="background:#f8fafc;border-radius:10px;">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-4">
                                                <label class="form-label smaller text-muted mb-1">Gambar TTD <span class="text-danger">*</span></label>
                                                @if($sigPath)
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <img src="{{ asset('uploads/' . $sigPath) }}" style="height:45px;object-fit:contain;border:1px solid #e2e8f0;border-radius:6px;padding:3px;background:white;">
                                                        <label class="smaller text-primary" style="cursor:pointer;">
                                                            <input type="checkbox" name="replace_sig_{{ $i }}" value="1" style="display:none;" onchange="toggleSigReplace(this, {{ $i }})">
                                                            Ganti Gambar
                                                        </label>
                                                    </div>
                                                    <input type="hidden" name="existing_signature_image[{{ $i }}]" value="{{ $sigPath }}">
                                                    <div id="sig_file_{{ $i }}" style="display:none;">
                                                        <input type="file" name="certificate_signature_file[{{ $i }}]" class="form-control form-control-sm" accept="image/*">
                                                    </div>
                                                @else
                                                    <input type="file" name="certificate_signature_file[{{ $i }}]" class="form-control form-control-sm" accept="image/*">
                                                @endif
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label smaller text-muted mb-1">Nama Penandatangan</label>
                                                <input type="text" name="signature_name[{{ $i }}]" value="{{ $sigName }}" class="form-control form-control-sm" placeholder="cth: Dr. Ahmad Fauzi">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label smaller text-muted mb-1">Jabatan</label>
                                                <input type="text" name="signature_position[{{ $i }}]" value="{{ $sigPos }}" class="form-control form-control-sm" placeholder="cth: Direktur Utama">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="removeSigEntry(this, '{{ $sigPath }}', event)"><i class="bi bi-trash"></i> Hapus</button>
                                                <input type="hidden" name="delete_signatures[]" value="">
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            </div>
                            <div class="smaller text-muted"><i class="bi bi-info-circle me-1"></i>Gunakan PNG transparan untuk hasil terbaik. Maksimal 3 tanda tangan.</div>
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
                <h6 class="fw-bold text-navy mb-3">Panduan & Syarat Aset</h6>
                <div class="d-flex align-items-start mb-3">
                    <div class="me-3 text-primary"><i class="bi bi-info-circle fs-4"></i></div>
                    <div class="smaller text-muted">
                        <b>Format File:</b> Mendukung JPG, JPEG, PNG, WEBP, dan SVG.
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <div class="me-3 text-danger"><i class="bi bi-hdd fs-4"></i></div>
                    <div class="smaller text-muted">
                        <b>Ukuran Maksimal:</b> 2MB (2048 KB) per file. File yang lebih besar akan otomatis ditolak.
                    </div>
                </div>
                <div class="d-flex align-items-start mb-3">
                    <div class="me-3 text-warning"><i class="bi bi-layers fs-4"></i></div>
                    <div class="smaller text-muted">
                        <b>Jumlah Maksimal:</b> Maksimal 3 Logo Partner dan 3 Tanda Tangan.
                    </div>
                </div>
                <div class="d-flex align-items-start mb-4">
                    <div class="me-3 text-success"><i class="bi bi-pen fs-4"></i></div>
                    <div class="smaller text-muted">
                        <b>Rekomendasi TTD:</b> Gunakan background transparan (PNG/SVG) agar terlihat menyatu dengan sertifikat.
                    </div>
                </div>

                <div class="alert alert-info border-0 smaller py-3">
                    <i class="bi bi-info-circle-fill me-2"></i> Sertifikat kursus akan otomatis tersedia bagi siswa yang status enroll-nya sudah "Completed".
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

    function markDelete(type, path, element, event) {
        if(event) {
            event.preventDefault();
            event.stopPropagation();
        }
        if(confirm('Hapus aset ini?')) {
            const wrapper = element.closest('.logo-item-wrapper') || element.closest('.position-relative');
            const input = wrapper.querySelector('input[name="delete_logos[]"]');
            if (input) input.value = path;
            wrapper.style.opacity = '0.3';
            wrapper.style.pointerEvents = 'none';
            wrapper.classList.add('marked-deleted');
            checkLogoCount();
        }
    }

    function previewNewAsset(input) {
        const file = input.files[0];
        if (file) {
            let preview = input.parentElement.querySelector('.new-asset-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.className = 'new-asset-preview mt-2';
                preview.style.cssText = 'height:40px; border-radius:4px; border:1px solid #e2e8f0; object-fit:contain; background:white; padding:2px;';
                input.parentElement.appendChild(preview);
            }
            const reader = new FileReader();
            reader.onload = function(e) { preview.src = e.target.result; }
            reader.readAsDataURL(file);
        }
    }

    function addLogoField() {
        const container = document.getElementById('logoUploadContainer');
        const currentInputs = container.querySelectorAll('input[type="file"]').length;
        const existingLogos = document.querySelectorAll('.logo-item-wrapper:not(.marked-deleted)').length;
        if ((currentInputs + existingLogos) < 3) {
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-2';
            div.innerHTML = `
                <div class="d-flex flex-column gap-1 w-100">
                    <input type="file" name="certificate_logo[]" class="form-control form-control-sm" accept="image/*" onchange="previewNewAsset(this)">
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove(); checkLogoCount();"><i class="bi bi-trash"></i></button>
            `;
            container.appendChild(div);
            checkLogoCount();
        }
    }

    function checkLogoCount() {
        const currentInputs = document.querySelectorAll('#logoUploadContainer input[type="file"]').length;
        const existingLogos = document.querySelectorAll('.logo-item-wrapper:not(.marked-deleted)').length;
        const btn = document.getElementById('addLogoBtn');
        if ((currentInputs + existingLogos) >= 3) {
            btn.style.display = 'none';
        } else {
            btn.style.display = 'inline';
        }
    }

    let sigIndex = {{ count(is_array($course->certificate_signature) ? $course->certificate_signature : ($course->certificate_signature ? [$course->certificate_signature] : [])) }};

    function addSignatureField() {
        const container = document.getElementById('signaturesContainer');
        const existing = container.querySelectorAll('.sig-entry').length;
        if (existing >= 3) { alert('Maksimal 3 tanda tangan.'); return; }
        const idx = sigIndex++;
        const div = document.createElement('div');
        div.className = 'sig-entry card border mb-3 p-3';
        div.style.cssText = 'background:#f8fafc;border-radius:10px;';
        div.innerHTML = `
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label smaller text-muted mb-1">Gambar TTD <span class="text-danger">*</span></label>
                    <input type="file" name="certificate_signature_file[${idx}]" class="form-control form-control-sm" accept="image/*" onchange="previewNewAsset(this)">
                </div>
                <div class="col-md-3">
                    <label class="form-label smaller text-muted mb-1">Nama Penandatangan</label>
                    <input type="text" name="signature_name[${idx}]" class="form-control form-control-sm" placeholder="cth: Dr. Ahmad Fauzi">
                </div>
                <div class="col-md-3">
                    <label class="form-label smaller text-muted mb-1">Jabatan</label>
                    <input type="text" name="signature_position[${idx}]" class="form-control form-control-sm" placeholder="cth: Direktur Utama">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="this.closest('.sig-entry').remove()"><i class="bi bi-trash"></i> Hapus</button>
                    <input type="hidden" name="delete_signatures[]" value="">
                </div>
            </div>`;
        container.appendChild(div);
    }

    function toggleSigReplace(checkbox, idx) {
        const fileDiv = document.getElementById('sig_file_' + idx);
        if (!fileDiv) return;
        fileDiv.style.display = checkbox.checked ? 'block' : 'none';
    }

    function removeSigEntry(btn, path, event) {
        if(event) {
            event.preventDefault();
            event.stopPropagation();
        }
        if (!confirm('Hapus tanda tangan ini?')) return;
        const entry = btn.closest('.sig-entry');
        if (path) {
            const hidden = entry.querySelector('input[name="delete_signatures[]"]') || entry.querySelector('input[name^="delete_signatures"]');
            if (hidden) hidden.value = path;
            entry.style.opacity = '0.3';
            entry.style.pointerEvents = 'none';
        } else {
            entry.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', checkLogoCount);
</script>
@endsection