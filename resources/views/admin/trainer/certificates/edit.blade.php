@extends('layouts.crm')

@section('title', 'Konfigurasi Sertifikat - ' . ($model->title ?? $model->name))

@section('styles')
<style>
    :root {
        --crm-primary: #6d28d9;
        --crm-navy: #1e1b4b;
        --crm-border: #e2e8f0;
        --crm-text-muted: #64748b;
    }

    .card-minimal {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .template-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        background: #fff;
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

    .logo-item-wrapper {
        overflow: visible !important;
    }

    .smaller {
        font-size: 0.82rem;
    }

    .text-navy {
        color: var(--crm-navy);
    }
</style>
@endsection

@section('content')
@php
    $selectedTemplate = old('certificate_template', 'template_1');
    $logos = $assets->where('type', \App\Models\TrainerCertificateAsset::TYPE_LOGO)->values();
    $sigs = $assets->where('type', \App\Models\TrainerCertificateAsset::TYPE_SIGNATURE)->values();
@endphp

<div class="row align-items-center mb-4">
    <div class="col">
        <h3 class="fw-bold text-navy mb-1">Konfigurasi Sertifikat</h3>
        <p class="text-muted small mb-0">
            {{ $context === 'event' ? 'Event' : 'Kursus' }}:
            <span class="text-primary fw-medium">
                {{ $model->title ?? $model->name }}
            </span>
        </p>
    </div>

    <div class="col-auto">
        <a href="{{ route('admin.trainer.certificates.show', $trainer) }}"
           class="btn btn-outline-secondary btn-sm bg-white">
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

<form action="{{ route('admin.trainer.certificates.update', [
        'trainer' => $trainer->id,
        'context' => $context,
        'id' => $model->id,
    ]) }}"
    method="POST"
    enctype="multipart/form-data">

    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">

            <div class="card-minimal mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">Langkah 1: Pilih Template Desain</h6>
                </div>

                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="template-card border {{ $selectedTemplate === 'template_1' ? 'active' : '' }}"
                                 onclick="selectTemplate('template_1', this)">
                                <div class="check-badge"><i class="bi bi-check-lg"></i></div>
                                <div class="template-preview-img"
                                     style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: #fbbf24;">
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
                            <div class="template-card border {{ $selectedTemplate === 'template_2' ? 'active' : '' }}"
                                 onclick="selectTemplate('template_2', this)">
                                <div class="check-badge"><i class="bi bi-check-lg"></i></div>
                                <div class="template-preview-img"
                                     style="background: #ffffff; color: #1e293b; border-bottom: 1px solid #e2e8f0;">
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
                            <div class="template-card border {{ $selectedTemplate === 'template_3' ? 'active' : '' }}"
                                 onclick="selectTemplate('template_3', this)">
                                <div class="check-badge"><i class="bi bi-check-lg"></i></div>
                                <div class="template-preview-img"
                                     style="background: linear-gradient(135deg, #6d28d9 0%, #db2777 100%); color: #ffffff;">
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

                    <input type="hidden"
                           name="certificate_template"
                           id="selected_template"
                           value="{{ $selectedTemplate }}">
                </div>
            </div>

            <div class="card-minimal mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h6 class="fw-bold mb-0">Langkah 2: Kelola Aset (Logo & TTD)</h6>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label smaller fw-bold text-muted d-flex justify-content-between">
                                Upload Logo Partner
                                <span class="text-primary smaller"
                                      style="cursor: pointer;"
                                      id="addLogoBtn"
                                      onclick="addLogoField()">
                                    + Tambah Baris
                                </span>
                            </label>

                            <div id="logoUploadContainer">
                                <input type="file"
                                       name="certificate_logo[]"
                                       class="form-control form-control-sm mb-2"
                                       accept="image/*"
                                       onchange="previewNewAsset(this)">
                            </div>

                            <div class="smaller text-muted mb-3">
                                <i class="bi bi-info-circle me-1"></i>Maksimal 3 logo partner tambahan.
                            </div>

                            <div id="existingLogos" class="d-flex flex-wrap gap-2">
                                @foreach($logos as $logo)
                                    <div class="position-relative logo-item-wrapper">
                                        <img src="{{ asset('storage/' . $logo->image_path) }}" class="asset-preview">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label smaller fw-bold text-muted d-flex justify-content-between">
                                Tanda Tangan
                                <span class="text-primary smaller"
                                      style="cursor: pointer;"
                                      onclick="addSignatureField()">
                                    + Tambah Tanda Tangan
                                </span>
                            </label>

                            <div id="signaturesContainer">
                                @foreach($sigs as $i => $sig)
                                    <div class="sig-entry card border mb-3 p-3"
                                         style="background:#f8fafc;border-radius:10px;">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-4">
                                                <label class="form-label smaller text-muted mb-1">
                                                    Gambar TTD
                                                </label>

                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <img src="{{ asset('storage/' . $sig->image_path) }}"
                                                         style="height:45px;object-fit:contain;border:1px solid #e2e8f0;border-radius:6px;padding:3px;background:white;">
                                                </div>

                                                <input type="file"
                                                       name="certificate_signature_file[{{ $i }}]"
                                                       class="form-control form-control-sm"
                                                       accept="image/*"
                                                       onchange="previewNewAsset(this)">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label smaller text-muted mb-1">
                                                    Nama Penandatangan
                                                </label>

                                                <input type="text"
                                                       name="signature_name[{{ $i }}]"
                                                       value="{{ $sig->name }}"
                                                       class="form-control form-control-sm"
                                                       placeholder="cth: Dr. Ahmad Fauzi">
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label smaller text-muted mb-1">
                                                    Jabatan
                                                </label>

                                                <input type="text"
                                                       name="signature_position[{{ $i }}]"
                                                       value="{{ $sig->position }}"
                                                       class="form-control form-control-sm"
                                                       placeholder="cth: Direktur Utama">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="smaller text-muted">
                                <i class="bi bi-info-circle me-1"></i>Gunakan PNG transparan untuk hasil terbaik. Maksimal 3 tanda tangan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3">
                <button type="submit"
                        class="btn btn-navy px-5"
                        style="background: var(--crm-navy); color: white;">
                    Simpan Konfigurasi
                </button>
            </div>
        </div>

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
                        <b>Ukuran Maksimal:</b> 2MB per file.
                    </div>
                </div>

                <div class="d-flex align-items-start mb-3">
                    <div class="me-3 text-warning"><i class="bi bi-layers fs-4"></i></div>
                    <div class="smaller text-muted">
                        <b>Jumlah Maksimal:</b> Maksimal 3 Logo Partner dan 3 Tanda Tangan.
                    </div>
                </div>

                <div class="alert alert-warning border-0 smaller py-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Konfigurasi ini khusus sertifikat trainer pada kegiatan ini.
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

            reader.onload = function(e) {
                preview.src = e.target.result;
            }

            reader.readAsDataURL(file);
        }
    }

    function addLogoField() {
        const container = document.getElementById('logoUploadContainer');
        const currentInputs = container.querySelectorAll('input[type="file"]').length;
        const existingLogos = document.querySelectorAll('.logo-item-wrapper').length;

        if ((currentInputs + existingLogos) >= 3) {
            alert('Maksimal 3 logo.');
            return;
        }

        const div = document.createElement('div');
        div.className = 'd-flex gap-2 mb-2';

        div.innerHTML = `
            <div class="d-flex flex-column gap-1 w-100">
                <input type="file" name="certificate_logo[]" class="form-control form-control-sm" accept="image/*" onchange="previewNewAsset(this)">
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.parentElement.remove();">
                <i class="bi bi-trash"></i>
            </button>
        `;

        container.appendChild(div);
    }

    let sigIndex = {{ $sigs->count() }};

    function addSignatureField() {
        const container = document.getElementById('signaturesContainer');
        const existing = container.querySelectorAll('.sig-entry').length;

        if (existing >= 3) {
            alert('Maksimal 3 tanda tangan.');
            return;
        }

        const idx = sigIndex++;

        const div = document.createElement('div');
        div.className = 'sig-entry card border mb-3 p-3';
        div.style.cssText = 'background:#f8fafc;border-radius:10px;';

        div.innerHTML = `
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label class="form-label smaller text-muted mb-1">Gambar TTD</label>
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
                    <button type="button" class="btn btn-outline-danger btn-sm w-100" onclick="this.closest('.sig-entry').remove()">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
            </div>
        `;

        container.appendChild(div);
    }
</script>
@endsection