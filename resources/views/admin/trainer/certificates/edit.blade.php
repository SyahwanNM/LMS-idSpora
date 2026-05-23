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
    }

    .cert-edit-page {
        width: 100%;
    }

    .cert-edit-page * {
        box-sizing: border-box;
    }

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
        color: #fff !important;
        
        line-height: 1.05;
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

    .config-section:last-child {
        border-bottom: 0;
    }

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

    .section-content {
        padding-left: 54px;
    }

    .template-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 22px;
    }

    .template-option {
        cursor: pointer;
        display: block;
    }

    .template-option input {
        display: none;
    }

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
        border-bottom: 12px solid var(--cert-primary);
    }

    .template-paper.t1::after {
        right: -10px;
        top: -10px;
        border-top: 12px solid var(--cert-primary);
        border-right: 12px solid #0f2d52;
    }

    .template-paper.t2::before {
        left: -8px;
        top: -8px;
        border-left: 10px solid #0f2d52;
        border-top: 10px solid var(--cert-primary);
    }

    .template-paper.t2::after {
        right: -8px;
        bottom: -8px;
        border-right: 10px solid #0f2d52;
        border-bottom: 10px solid var(--cert-primary);
    }

    .template-paper.t3::before {
        left: -8px;
        top: -8px;
        border-left: 10px solid #047857;
        border-top: 10px solid var(--cert-primary);
    }

    .template-paper.t3::after {
        right: -8px;
        bottom: -8px;
        border-right: 10px solid #047857;
        border-bottom: 10px solid var(--cert-primary);
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
        grid-template-columns: repeat(3, minmax(0, 1fr));
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
        border: 0;
        border-radius: 8px;
        background: #ffffff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        padding: 0;
        box-shadow: 0 4px 10px rgba(15,23,42,.06);
        border: 1px solid var(--cert-border);
        transition: background .12s, color .12s, transform .08s;
        z-index: 6;
    }

    .remove-btn:hover {
        background: #f8fafc;
        color: #e11d48;
        transform: translateY(-1px);
    }

    /* unified preview close button for logos and signatures */
    .logo-card .preview-close,
    .signature-card .preview-close,
    label.add-card .preview-close {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #ffffff;
        color: var(--cert-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--cert-border);
        cursor: pointer;
        box-shadow: 0 6px 14px rgba(2,6,23,0.06);
        z-index: 7;
        padding: 0;
        overflow: hidden;
        transition: background .12s, color .12s; /* no translate on hover */
    }

    .preview-close i {
        line-height: 0;
        color: inherit; /* icon follows parent color */
    }

    .logo-card {
        position: relative;
    }

    .signature-card {
        position: relative;
    }

    .signature-preview.has-image input[type="file"],
    .signature-preview.has-image .choose-file {
        display: none !important;
    }

    .add-card.has-image input {
        display: none !important;
    }

    /* hide name/position inputs until card has image or is in editing state */
    .signature-card .signature-input {
        display: none;
    }

    .signature-card.has-image .signature-input,
    .signature-card.editing .signature-input {
        display: block;
    }

    .add-card {
        cursor: pointer;
        flex-direction: column;
        gap: 4px;
    }

    .add-card input {
        display: none;
    }

    .upload-preview {
        display: block;
        max-width: 100%;
        max-height: 72px;
        border-radius: 10px;
        object-fit: contain;
        margin-bottom: 6px;
    }

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
        min-height: 150px;
    }

    .signature-preview {
        min-height: 92px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 14px;
        border-radius: 14px;
        background: #fff;
        border: 1px dashed #cbd5e1;
    }

    .signature-preview img {
        max-height: 86px;
        max-width: 100%;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    .signature-card input[type="file"] {
        font-size: 11px;
        max-width: 100%;
    }

    /* ensure add-card is not rendered inside preview; center when placed directly inside the card */
    .add-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        color: #0f172a;
        cursor: pointer;
    }

    .add-placeholder i {
        font-size: 24px;
        color: var(--cert-primary);
    }

    .add-placeholder .add-text {
        font-weight: 900;
        font-size: 13px;
        text-align: center;
    }

    .add-placeholder .muted {
        font-size: 11px;
        color: var(--cert-muted);
        font-weight: 400;
    }

    /* stronger specificity to override global styles if needed */
    .logo-card .preview-close,
    .signature-card .preview-close {
        width: 28px !important;
        height: 28px !important;
        border-radius: 50% !important;
        padding: 0 !important;
        line-height: 0 !important;
        background: #ffffff !important;
        color: var(--cert-primary) !important;
        border: 1px solid var(--cert-border) !important;
        transition: background .12s, color .12s !important;
        z-index: 1200 !important;
        box-shadow: 0 6px 14px rgba(2,6,23,0.06) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        overflow: hidden !important;
    }

    .logo-card .preview-close:hover,
    .signature-card .preview-close:hover {
        background: var(--cert-primary) !important;
        color: #ffffff !important;
        transform: none !important; /* do not move on hover */
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

    .signature-preview img.upload-preview {
        max-height: 86px;
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

    .side-content {
        padding: 24px;
    }

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

    @media (max-width: 1200px) {
        .template-grid,
        .logo-grid,
        .signature-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .section-content {
            padding-left: 0;
        }

        .cert-step-header {
            gap: 12px !important;
        }

        .template-grid,
        .logo-grid,
        .signature-grid {
            grid-template-columns: 1fr;
        }

        .cert-hero {
            padding: 26px;
        }
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
                                    <div class="logo-card has-image" data-asset-id="{{ $logo->id }}">
                                        <button type="button" class="remove-btn" data-asset-id="{{ $logo->id }}">
                                            <i class="bi bi-x"></i>
                                        </button>
                                        <button type="button" class="preview-close" aria-label="Hapus logo" data-asset-id="{{ $logo->id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                        <img src="{{ asset('storage/' . $logo->image_path) }}" alt="Logo" class="upload-preview">
                                    </div>
                                @endforeach

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

                            <div id="removeAssetsContainer"></div>
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

                                    <div class="signature-card @if($signature) has-image @endif" data-index="{{ $i }}" @if($signature) data-asset-id="{{ $signature->id }}" @endif>
                                        @if($signature)
                                            <button type="button" class="remove-btn" data-asset-id="{{ $signature->id }}">
                                                <i class="bi bi-x"></i>
                                            </button>
                                            <button type="button" class="preview-close" aria-label="Hapus tanda tangan" data-asset-id="{{ $signature->id }}">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endif

                                        <div class="signature-preview">
                                            @if($signature)
                                                <img src="{{ asset('storage/' . $signature->image_path) }}" alt="Signature" class="upload-preview">
                                            @else
                                                <div class="add-placeholder">
                                                    <i class="bi bi-plus-lg"></i>
                                                    <div class="add-text">Tambah Tanda Tangan
                                                        <div class="muted">Maks. 3 tanda tangan</div>
                                                    </div>
                                                </div>
                                                <input type="file" class="signature-input-file" style="display:none"
                                                    name="certificate_signature_file[{{ $i }}]"
                                                    accept=".jpg,.jpeg,.png,.webp,.svg">
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
            document.addEventListener('DOMContentLoaded', function () {
            // delegated change handler for file inputs (handles dynamically added inputs too)
            document.addEventListener('change', function (e) {
                    const target = e.target;
                    if (!target) return;

                    // logo inputs
                    if (target.matches('input[name="certificate_logo[]"]')) {
                        const file = target.files && target.files[0];
                        const label = target.closest('label');
                        if (!file || !label) return;

                        // hide icon/text
                        const icon = label.querySelector('i');
                        const span = label.querySelector('span');
                        const small = label.querySelector('small');
                        if (icon) icon.style.display = 'none';
                        if (span) span.style.display = 'none';
                        if (small) small.style.display = 'none';

                        let img = label.querySelector('img.upload-preview');
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'upload-preview';
                            label.insertBefore(img, label.firstChild);
                        }

                        img.src = URL.createObjectURL(file);
                        label.classList.add('has-image');

                        // add preview-close button if not present
                        if (!label.querySelector('.preview-close')) {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'preview-close';
                            btn.setAttribute('aria-label', 'Hapus gambar');
                            btn.innerHTML = '<i class="bi bi-x-lg"></i>';
                            label.appendChild(btn);
                        }
                    }

                    // signature inputs (name includes index)
                    if (target.matches('input[name^="certificate_signature_file"]')) {
                        const file = target.files && target.files[0];
                        const container = target.closest('.signature-preview');
                        if (!file || !container) return;

                        let img = container.querySelector('img.upload-preview');
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'upload-preview';
                            container.insertBefore(img, container.firstChild);
                        }

                        img.src = URL.createObjectURL(file);
                        container.classList.add('has-image');

                        // hide/remove the add-placeholder inside the preview to avoid duplicate controls
                        const addPlaceholder = container.querySelector('.add-placeholder');
                        if (addPlaceholder) addPlaceholder.remove();

                        // add preview-close button inside signature-card and mark card has-image
                        const card = target.closest('.signature-card');
                        if (card) {
                            card.classList.add('has-image');
                            if (!card.querySelector('.preview-close')) {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'preview-close';
                                btn.setAttribute('aria-label', 'Hapus tanda tangan');
                                btn.innerHTML = '<i class="bi bi-x-lg"></i>';
                                card.insertBefore(btn, card.firstChild);
                            }
                        }
                    }
                });

                // clicking the placeholder triggers the hidden file input and enters editing state
                document.addEventListener('click', function (e) {
                    const ph = e.target.closest && e.target.closest('.add-placeholder');
                    if (!ph) return;
                    const card = ph.closest('.signature-card');
                    if (!card) return;
                    card.classList.add('editing');
                    const input = card.querySelector('input.signature-input-file, input[name^="certificate_signature_file"]');
                    if (input) input.click();
                });

                // Signature preview for inputs starting with certificate_signature_file
                document.querySelectorAll('input[name^="certificate_signature_file"]').forEach(function (input) {
                    input.addEventListener('change', function (e) {
                        const file = this.files && this.files[0];
                        const container = this.closest('.signature-preview');
                        if (!file || !container) return;

                        let img = container.querySelector('img.upload-preview');
                        if (!img) {
                            img = document.createElement('img');
                            img.className = 'upload-preview';
                            // insert before input so preview shows above
                            container.insertBefore(img, container.firstChild);
                        }

                        img.src = URL.createObjectURL(file);
                        container.classList.add('has-image');

                        // remove any placeholder inside the preview so the UI stays clean
                        const addPlaceholder = container.querySelector('.add-placeholder');
                        if (addPlaceholder) addPlaceholder.remove();

                        const card = this.closest('.signature-card');
                        if (card) card.classList.add('has-image');
                    });
                });

                // Clicking remove buttons: for existing assets, mark for removal; for new uploads, clear preview/file
                document.querySelectorAll('.remove-btn').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        const assetId = this.getAttribute('data-asset-id');
                        const card = this.closest('.logo-card, .signature-card');
                        if (!card) return;

                        // If this is an existing asset, append a hidden input to signal removal
                        if (assetId) {
                            const container = document.getElementById('removeAssetsContainer');
                            if (container) {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'remove_assets[]';
                                input.value = assetId;
                                container.appendChild(input);
                            }
                            // remove preview from UI
                            const preview = card.querySelector('img');
                            if (preview) preview.remove();
                            // visually mark card as removed
                            card.classList.add('removed');
                            card.style.opacity = '0.5';
                            // hide the remove button once clicked
                            this.style.display = 'none';
                            return;
                        }

                        // Otherwise, it's a dynamically added preview: remove img and clear file input if present
                        const preview = card.querySelector('img.upload-preview');
                        if (preview) preview.remove();
                        const fileInput = card.querySelector('input[type=file]');
                        if (fileInput) fileInput.value = '';

                        // restore placeholder UI if applicable (find the placeholder and ensure it's visible)
                        const addPlaceholder = card.querySelector('.add-placeholder');
                        if (addPlaceholder) addPlaceholder.style.display = '';
                    });
                });
                
                // delegated click handler for preview-close buttons (supports dynamic buttons)
                document.addEventListener('click', function (e) {
                    const closeBtn = e.target.closest && e.target.closest('.preview-close');
                    if (!closeBtn) return;

                    const assetId = closeBtn.getAttribute('data-asset-id');
                    const card = closeBtn.closest('.logo-card, .signature-card, label.add-card');
                    if (!card) return;

                    // If existing asset -> mark for removal and replace with upload input
                    if (assetId) {
                        const container = document.getElementById('removeAssetsContainer');
                        if (container) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'remove_assets[]';
                            input.value = assetId;
                            container.appendChild(input);
                        }

                        // remove preview image
                        const preview = card.querySelector('img.upload-preview');
                        if (preview) preview.remove();

                        // hide existing remove buttons
                        const rem = card.querySelectorAll('.remove-btn, .preview-close');
                        rem.forEach(r => r.style.display = 'none');

                        card.classList.add('removed');
                        card.style.opacity = '0.5';

                        // insert an add-card input so admin can replace the file (logos: certificate_logo[], signatures: certificate_signature_file[index])
                        if (card.classList.contains('logo-card')) {
                            const label = document.createElement('label');
                            label.className = 'add-card';
                            const input = document.createElement('input');
                            input.type = 'file';
                            input.name = 'certificate_logo[]';
                            input.accept = '.jpg,.jpeg,.png,.webp,.svg';
                            label.appendChild(input);

                            const icon = document.createElement('i');
                            icon.className = 'bi bi-plus-lg';
                            icon.style.fontSize = '24px';
                            icon.style.color = 'var(--cert-primary)';

                            const span = document.createElement('span');
                            span.textContent = 'Tambah Logo';

                            const small = document.createElement('small');
                            small.textContent = 'Maks. 3 logo';

                            label.appendChild(icon);
                            label.appendChild(span);
                            label.appendChild(small);

                            // replace the entire card node with the placeholder label so grid children remain consistent
                            if (card.parentNode) {
                                card.parentNode.replaceChild(label, card);
                            }
                        }

                        if (card.classList.contains('signature-card')) {
                            const idx = card.getAttribute('data-index') || 0;
                            const previewWrap = card.querySelector('.signature-preview');

                            const placeholder = document.createElement('div');
                            placeholder.className = 'add-placeholder';

                            const icon = document.createElement('i');
                            icon.className = 'bi bi-plus-lg';
                            icon.style.fontSize = '24px';
                            icon.style.color = 'var(--cert-primary)';

                            const span = document.createElement('div');
                            span.className = 'add-text';
                            span.textContent = 'Tambah Tanda Tangan';

                            const small = document.createElement('div');
                            small.className = 'muted';
                            small.textContent = 'Maks. 3 tanda tangan';

                            span.appendChild(small);
                            placeholder.appendChild(icon);
                            placeholder.appendChild(span);

                            const input = document.createElement('input');
                            input.type = 'file';
                            input.name = 'certificate_signature_file[' + idx + ']';
                            input.accept = '.jpg,.jpeg,.png,.webp,.svg';
                            input.className = 'signature-input-file';
                            input.style.display = 'none';

                            if (previewWrap) {
                                previewWrap.appendChild(placeholder);
                                previewWrap.appendChild(input);
                            }
                        }

                        return;
                    }

                    // Otherwise it's a dynamically created preview close -> remove preview and show input again
                    const preview = card.querySelector('img.upload-preview');
                    if (preview) preview.remove();

                    // show/hide appropriate inputs
                    const fileInput = card.querySelector('input[type=file]');
                    if (fileInput) fileInput.value = '';
                    if (fileInput) fileInput.style.display = '';

                    // remove the close button itself
                    closeBtn.remove();
                });

                // Client-side form validation: require template, at least one logo, and at least one signature
                const form = document.querySelector('form');
                if (form) {
                    form.addEventListener('submit', function (e) {
                        const selectedTemplate = document.querySelector('input[name="certificate_template"]:checked');
                        if (!selectedTemplate) {
                            e.preventDefault();
                            alert('Pilih template sertifikat terlebih dahulu.');
                            return;
                        }

                        // Count visible logos (existing non-removed logo-card with image OR file inputs with files)
                        let logoCount = 0;
                        document.querySelectorAll('.logo-card').forEach(function (c) {
                            if (c.classList.contains('removed')) return;
                            if (c.querySelector('img')) logoCount++;
                        });
                        document.querySelectorAll('input[name="certificate_logo[]"]').forEach(function (inp) {
                            if (inp.files && inp.files.length) logoCount += inp.files.length;
                        });

                        let signatureCount = 0;
                        document.querySelectorAll('.signature-card').forEach(function (c) {
                            if (c.classList.contains('removed')) return;
                            if (c.querySelector('img')) signatureCount++;
                        });
                        document.querySelectorAll('input[name^="certificate_signature_file"]').forEach(function (inp) {
                            if (inp.files && inp.files.length) signatureCount += inp.files.length;
                        });

                        if (logoCount < 1) {
                            e.preventDefault();
                            alert('Harap upload minimal 1 logo untuk sertifikat.');
                            return;
                        }

                        if (signatureCount < 1) {
                            e.preventDefault();
                            alert('Harap upload minimal 1 tanda tangan untuk sertifikat.');
                            return;
                        }
                    });
                }

                // No-op: signature add-card is rendered inside .signature-preview by template
            });
        </script>
        @endpush