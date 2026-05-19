@extends('layouts.admin-trainer')

@section('title', 'Kelola Template Sertifikat')

@php
    $modelTitle = $context === 'event'
        ? ($model->title ?? '-')
        : ($model->name ?? '-');

    $selectedTemplate = old(
        'certificate_template',
        optional($assets->where('type', 'template')->first())->name ?? 'template_1'
    );

    $logos = $assets->where('type', 'logo')->values();
    $signatures = $assets->where('type', 'signature')->values();
@endphp

@push('admin-trainer-styles')
<style>
    :root {
        --cert-primary: #2f3fcb;
        --cert-navy: #1a237e;
        --cert-soft: #eef1ff;
        --cert-border: #e6eaf2;
        --cert-muted: #6b7a99;
        --cert-bg: #f8fafc;
        --cert-danger: #e11d48;
    }

    .cert-edit-page { width: 100%; }
    .cert-edit-page * { box-sizing: border-box; }

    .cert-breadcrumb {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
        font-size: 13px;
        color: #718096;
    }

    .back-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1px solid var(--cert-border);
        background: #fff;
        color: var(--cert-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        flex-shrink: 0;
    }

    .cert-hero {
        background: linear-gradient(135deg, #2935b8 0%, #4858db 58%, #dce3ff 100%);
        border-radius: 22px;
        padding: 34px 38px;
        color: #fff;
        min-height: 155px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(47, 63, 203, .14);
        margin-bottom: 22px;
    }

    .cert-hero::after {
        content: '';
        position: absolute;
        right: 55px;
        top: 24px;
        width: 245px;
        height: 120px;
        border-radius: 26px;
        background: rgba(255,255,255,.18);
    }

    .cert-hero-content {
        position: relative;
        z-index: 2;
    }

    .page-eyebrow {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255,255,255,.9);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .page-eyebrow::before {
        content: '';
        width: 22px;
        height: 2px;
        background: rgba(255,255,255,.9);
        border-radius: 999px;
    }

    .cert-hero h1 {
        font-size: 30px;
        font-weight: 900;
        margin: 0 0 8px;
        letter-spacing: -.6px;
    }

    .cert-hero p {
        margin: 0;
        font-size: 15px;
        line-height: 1.55;
        color: rgba(255,255,255,.95);
    }

    .config-card {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 22px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .config-section {
        padding: 28px 30px;
        border-bottom: 1px solid var(--cert-border);
        text-align: left !important;
    }

    .config-section:last-child { border-bottom: 0; }

    .cert-step-header {
        display: flex !important;
        justify-content: flex-start !important;
        align-items: flex-start !important;
        gap: 16px !important;
        width: 100% !important;
        margin-bottom: 22px;
        text-align: left !important;
    }

    .cert-step-badge {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 50%;
        background: var(--cert-primary);
        color: #fff;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(47,63,203,.25);
        flex-shrink: 0;
    }

    .cert-step-text {
        flex: 1;
        min-width: 0;
        text-align: left !important;
    }

    .cert-section-title {
        font-size: 17px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 4px;
        text-transform: none;
        letter-spacing: 0;
        text-align: left !important;
        line-height: 1.3;
    }

    .cert-section-subtitle {
        font-size: 13px;
        color: var(--cert-muted);
        margin: 0;
        text-align: left !important;
        line-height: 1.45;
    }

    .section-content { padding-left: 54px; }

    .template-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .template-option {
        cursor: pointer;
        display: block;
    }

    .template-option input { display: none; }

    .template-card {
        border: 1px solid var(--cert-border);
        border-radius: 16px;
        padding: 10px;
        background: #fff;
        position: relative;
        transition: .2s;
    }

    .template-option input:checked + .template-card {
        border-color: var(--cert-primary);
        box-shadow: 0 0 0 3px rgba(47,63,203,.12);
    }

    .template-check {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: var(--cert-primary);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .template-option input:checked + .template-card .template-check {
        display: flex;
    }

    .template-preview {
        height: 145px;
        border-radius: 12px;
        background: #f8fafc;
        overflow: hidden;
        border: 1px solid #edf1f7;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .template-paper {
        width: 90%;
        height: 80%;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        position: relative;
        padding-top: 26px;
        text-align: center;
        overflow: hidden;
    }

    .template-paper h6 {
        font-size: 14px;
        line-height: 1.15;
        font-weight: 900;
        color: #1a237e;
        letter-spacing: 1.4px;
        margin-bottom: 12px;
    }

    .template-paper small {
        font-size: 9px;
        color: #334155;
    }

    .template-paper .line {
        height: 1.5px;
        width: 76px;
        background: #94a3b8;
        margin: 12px auto 0;
    }

    .template-paper.t1::before,
    .template-paper.t1::after,
    .template-paper.t2::before,
    .template-paper.t2::after,
    .template-paper.t3::before,
    .template-paper.t3::after {
        content: '';
        position: absolute;
        width: 90px;
        height: 90px;
    }

    .template-paper.t1::before {
        left: -10px;
        bottom: -10px;
        border-left: 12px solid #0f2d52;
        border-bottom: 12px solid #fbbf24;
    }

    .template-paper.t1::after {
        right: -10px;
        top: -10px;
        border-top: 12px solid #fbbf24;
        border-right: 12px solid #0f2d52;
    }

    .template-paper.t2::before {
        left: -8px;
        top: -8px;
        border-left: 10px solid #0f2d52;
        border-top: 10px solid #fbbf24;
    }

    .template-paper.t2::after {
        right: -8px;
        bottom: -8px;
        border-right: 10px solid #0f2d52;
        border-bottom: 10px solid #fbbf24;
    }

    .template-paper.t3::before {
        left: -8px;
        top: -8px;
        border-left: 10px solid #047857;
        border-top: 10px solid #fbbf24;
    }

    .template-paper.t3::after {
        right: -8px;
        bottom: -8px;
        border-right: 10px solid #047857;
        border-bottom: 10px solid #fbbf24;
    }

    .template-name {
        text-align: center;
        font-weight: 900;
        font-size: 13px;
        color: #0f172a;
        padding: 12px 0 2px;
    }

    .template-option input:checked + .template-card .template-name {
        color: var(--cert-primary);
    }

    .logo-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .logo-card,
    .add-card {
        min-height: 92px;
        border-radius: 14px;
        border: 1px dashed #cbd5e1;
        background: #fff;
        padding: 14px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .logo-card img {
        max-width: 100%;
        max-height: 48px;
        object-fit: contain;
    }

    .remove-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 26px;
        height: 26px;
        min-width: 26px;
        min-height: 26px;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: #fff1f2;
        color: var(--cert-danger);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        font-size: 14px;
        box-shadow: 0 4px 10px rgba(225, 29, 72, 0.12);
        cursor: pointer;
        z-index: 5;
    }

    .remove-btn:hover {
        background: var(--cert-danger);
        color: #fff;
    }

    .add-card {
        cursor: pointer;
        flex-direction: column;
        gap: 4px;
    }

    .add-card input { display: none; }

    .add-card i {
        font-size: 24px;
        color: var(--cert-primary);
    }

    .add-card span {
        font-size: 12px;
        font-weight: 900;
        color: #0f172a;
    }

    .add-card small {
        font-size: 11px;
        color: var(--cert-muted);
    }

    .signature-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .signature-card {
        border: 1px solid var(--cert-border);
        border-radius: 14px;
        background: #fff;
        padding: 14px;
        position: relative;
        min-height: 170px;
    }

    .signature-preview {
        height: 64px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .signature-preview img {
        max-height: 58px;
        max-width: 100%;
        object-fit: contain;
    }

    .signature-add-box {
        width: 100%;
        height: 64px;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        cursor: pointer;
        text-align: center;
    }

    .signature-add-box input { display: none; }

    .signature-add-box i {
        font-size: 18px;
        color: var(--cert-primary);
    }

    .signature-add-box span {
        font-size: 12px;
        font-weight: 900;
        color: #0f172a;
    }

    .signature-add-box small {
        font-size: 10px;
        color: var(--cert-muted);
    }

    .signature-input {
        height: 34px;
        font-size: 12px;
        border: 1px solid #dbe3ef;
        border-radius: 9px;
        padding: 7px 10px;
        width: 100%;
        margin-top: 8px;
    }

    .side-panel {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
        position: sticky;
        top: 96px;
    }

    .side-content { padding: 24px; }

    .guide-title {
        font-size: 16px;
        font-weight: 900;
        color: var(--cert-primary);
        margin-bottom: 14px;
    }

    .guide-desc {
        font-size: 13px;
        color: var(--cert-muted);
        line-height: 1.55;
        margin-bottom: 18px;
    }

    .guide-item {
        display: flex;
        gap: 14px;
        padding: 18px 0;
        border-bottom: 1px solid var(--cert-border);
    }

    .guide-icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        background: #eef1ff;
        color: var(--cert-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .guide-item h6 {
        font-size: 13px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 6px;
    }

    .guide-item p {
        font-size: 12px;
        line-height: 1.55;
        color: var(--cert-muted);
        margin: 0;
    }

    .side-footer {
        border-top: 1px solid var(--cert-border);
        padding: 22px;
    }

    .btn-save-config {
        width: 100%;
        height: 48px;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #2f3fcb, #2636bd);
        color: #fff;
        font-weight: 900;
    }

    .save-note {
        text-align: center;
        font-size: 12px;
        color: var(--cert-muted);
        line-height: 1.5;
        margin: 14px 0 0;
    }

    .marked-remove {
        opacity: .35;
        pointer-events: none;
    }

    @media (max-width: 1200px) {
        .template-grid,
        .logo-grid,
        .signature-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .section-content { padding-left: 0; }
        .cert-step-header { gap: 12px !important; }

        .template-grid,
        .logo-grid,
        .signature-grid {
            grid-template-columns: 1fr;
        }

        .cert-hero { padding: 26px; }
    }
</style>
@endpush

@section('admin-trainer-content')
<div class="cert-edit-page">
    <div class="cert-breadcrumb">
        <a href="{{ route('admin.trainer.certificates.index') }}" class="back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <span>Sertifikat & Penghargaan</span>
        <i class="bi bi-chevron-right"></i>
        <strong class="text-primary">Kelola Template</strong>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <strong>Gagal menyimpan konfigurasi:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
        action="{{ route('admin.trainer.certificates.update', [
            'trainer' => $trainer->id,
            'context' => $context,
            'id' => $model->id,
        ]) }}"
        enctype="multipart/form-data">
        @csrf

        <div id="removeAssetsContainer"></div>

        <div class="row g-4">
            <div class="col-xl-9">
                <section class="cert-hero">
                    <div class="cert-hero-content">
                        <div class="page-eyebrow">Recognition System</div>
                        <h1>Konfigurasi Sertifikat</h1>
                        <p>
                            {{ strtoupper($context) }}: {{ $modelTitle }}<br>
                            Trainer: {{ $trainer->name }}
                        </p>
                    </div>
                </section>

                <div class="config-card">
                    <section class="config-section">
                        <div class="cert-step-header">
                            <div class="cert-step-badge">1</div>

                            <div class="cert-step-text">
                                <h5 class="cert-section-title">Pilih Template Desain</h5>
                                <p class="cert-section-subtitle">
                                    Pilih desain template sertifikat yang akan digunakan
                                </p>
                            </div>
                        </div>

                        <div class="section-content">
                            <div class="template-grid">
                                @foreach([
                                    'template_1' => ['Template 1', 't1'],
                                    'template_2' => ['Template 2', 't2'],
                                    'template_3' => ['Template 3', 't3'],
                                ] as $value => $data)
                                    <label class="template-option">
                                        <input type="radio"
                                            name="certificate_template"
                                            value="{{ $value }}"
                                            {{ $selectedTemplate === $value ? 'checked' : '' }}>

                                        <div class="template-card">
                                            <div class="template-check">
                                                <i class="bi bi-check-lg"></i>
                                            </div>

                                            <div class="template-preview">
                                                <div class="template-paper {{ $data[1] }}">
                                                    <h6>SERTIFIKAT<br>PENGHARGAAN</h6>
                                                    <small>Nama Penerima</small>
                                                    <div class="line"></div>
                                                </div>
                                            </div>

                                            <div class="template-name">
                                                {{ $data[0] }}
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <section class="config-section">
                        <div class="cert-step-header">
                            <div class="cert-step-badge">2</div>

                            <div class="cert-step-text">
                                <h5 class="cert-section-title">Upload Logo Partner</h5>
                                <p class="cert-section-subtitle">
                                    Upload logo partner atau sponsor yang akan ditampilkan di sertifikat
                                </p>
                            </div>
                        </div>

                        <div class="section-content">
                            <div class="logo-grid">
                                @foreach($logos as $logo)
                                    <div class="logo-card">
                                        <button type="button"
                                            class="remove-btn"
                                            onclick="markRemoveAsset(this, '{{ $logo->id }}')">
                                            <i class="bi bi-x"></i>
                                        </button>

                                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="Logo">
                                    </div>
                                @endforeach
<<<<<<< HEAD

                                @for($i = $logos->count(); $i < 3; $i++)
                                    <label class="add-card">
                                        <input type="file"
                                            name="certificate_logo[]"
                                            accept=".jpg,.jpeg,.png,.webp,.svg">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Tambah Logo</span>
                                        <small>Maks. 3 logo</small>
                                    </label>
                                @endfor
                            </div>
                        </div>
                    </section>

                    <section class="config-section">
                        <div class="cert-step-header">
                            <div class="cert-step-badge">3</div>

                            <div class="cert-step-text">
                                <h5 class="cert-section-title">Upload Tanda Tangan</h5>
                                <p class="cert-section-subtitle">
                                    Upload tanda tangan dan isi nama serta jabatan penandatangan
                                </p>
                            </div>
                        </div>

                        <div class="section-content">
                            <div class="signature-grid">
                                @for($i = 0; $i < 3; $i++)
                                    @php
                                        $signature = $signatures->get($i);
                                    @endphp

                                    <div class="signature-card">
                                        @if($signature)
                                            <button type="button"
                                                class="remove-btn"
                                                onclick="markRemoveAsset(this, '{{ $signature->id }}')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        @endif

                                        <div class="signature-preview">
                                            @if($signature)
                                                <img src="{{ asset('storage/' . $signature->image_path) }}" alt="Signature">
                                            @else
                                                <label class="signature-add-box">
                                                    <input type="file"
                                                        name="certificate_signature_file[{{ $i }}]"
                                                        accept=".jpg,.jpeg,.png,.webp,.svg">
                                                    <i class="bi bi-plus-lg"></i>
                                                    <span>Tambah TTD</span>
                                                    <small>Maks. 3</small>
                                                </label>
                                            @endif
                                        </div>

                                        <input type="text"
                                            name="signature_name[{{ $i }}]"
                                            class="signature-input"
                                            placeholder="Nama Lengkap"
                                            value="{{ old("signature_name.$i", $signature?->name) }}">

                                        <input type="text"
                                            name="signature_position[{{ $i }}]"
                                            class="signature-input"
                                            placeholder="Jabatan"
                                            value="{{ old("signature_position.$i", $signature?->position) }}">
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="col-xl-3">
                <aside class="side-panel">
                    <div class="side-content">
                        <h5 class="guide-title">Panduan Kelola Aset</h5>
                        <p class="guide-desc">
                            Pastikan semua aset yang diupload memenuhi ketentuan berikut:
                        </p>

                        <div class="guide-item">
                            <div class="guide-icon"><i class="bi bi-file-earmark-text"></i></div>
                            <div>
                                <h6>Template Desain</h6>
                                <p>Pilih salah satu template sertifikat yang tersedia.</p>
                            </div>
                        </div>

                        <div class="guide-item">
                            <div class="guide-icon"><i class="bi bi-image"></i></div>
                            <div>
                                <h6>Logo Partner</h6>
                                <p>Format PNG/JPG/SVG, ukuran maksimal 2MB, maksimal 3 logo.</p>
                            </div>
                        </div>

                        <div class="guide-item">
                            <div class="guide-icon"><i class="bi bi-pen"></i></div>
                            <div>
                                <h6>Tanda Tangan</h6>
                                <p>Gunakan PNG transparan agar hasil sertifikat terlihat rapi.</p>
                            </div>
                        </div>
                    </div>

                    <div class="side-footer">
                        <button type="submit" class="btn-save-config">
                            <i class="bi bi-save me-2"></i>
                            Simpan Konfigurasi
                        </button>

                        <p class="save-note">
                            Konfigurasi akan disimpan untuk penerbitan sertifikat.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </form>
</div>
@endsection

@push('admin-trainer-scripts')
<script>
    function markRemoveAsset(button, assetId) {
        if (!assetId) return;

        if (!confirm('Hapus aset ini?')) {
            return;
        }

        const container = document.getElementById('removeAssetsContainer');
        const input = document.createElement('input');

        input.type = 'hidden';
        input.name = 'remove_assets[]';
        input.value = assetId;

        container.appendChild(input);

        const card = button.closest('.logo-card') || button.closest('.signature-card');

        if (card) {
            card.classList.add('marked-remove');
        }
    }
</script>
@endpush
=======
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
>>>>>>> 227a9f6 (revisi layout trainer)
