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

    $isCrmSource = !empty($model->certificate_template)
        || !empty($model->certificate_logo)
        || !empty($model->certificate_signature);

    $assetUrl = function ($path) {
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;

        $path = ltrim(str_replace('\\', '/', $path), '/');
        $path = preg_replace('~^public/~i', '', $path) ?? $path;
        $path = preg_replace('~^storage/app/public/~i', '', $path) ?? $path;

        if (str_starts_with($path, 'storage/')) return asset($path);
        if (str_starts_with($path, 'uploads/')) return asset($path);

        $storageUrl = Storage::disk('public')->url($path);
        if (file_exists(public_path('storage/' . $path))) return $storageUrl;

        if (file_exists(public_path('uploads/' . $path))) return asset('uploads/' . $path);
        if (file_exists(public_path($path))) return asset($path);

        return $storageUrl;
    };
@endphp

@push('admin-trainer-styles')
<style>
    :root {
        --cert-primary: #2f3fcb;
        --cert-primary-soft: #eef1ff;
        --cert-navy: #1a237e;
        --cert-border: #e6eaf2;
        --cert-muted: #6b7a99;
        --cert-danger: #ef4444;
        --cert-danger-soft: #fff1f2;
    }

    .cert-edit-page,
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
        flex-wrap: wrap;
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

    .crm-source-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 999px;
        background: rgba(255,255,255,.18);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        margin-top: 14px;
    }

    .config-card,
    .side-panel {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 22px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .config-section {
        padding: 28px 30px;
        border-bottom: 1px solid var(--cert-border);
    }

    .config-section:last-child {
        border-bottom: 0;
    }

    .cert-step-header {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 22px;
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
    }

    .cert-section-title {
        font-size: 17px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 4px;
        line-height: 1.3;
    }

    .cert-section-subtitle {
        font-size: 13px;
        color: var(--cert-muted);
        margin: 0;
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
        position: relative;
        z-index: 10;
    }

    .template-paper small {
        font-size: 9px;
        color: #334155;
        position: relative;
        z-index: 10;
    }

    .template-paper .line {
        height: 1.5px;
        width: 76px;
        background: #94a3b8;
        margin: 12px auto 0;
        position: relative;
        z-index: 10;
    }

    /* ─── Template 1 Mockup (Maroon & Gold Waves) ─── */
    .template-paper.t1 {
        background-color: #ffffff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100' preserveAspectRatio='none'%3E%3Cpath d='M 30,0 C 50,40 70,60 100,80 L 100,0 Z' fill='%237f1d1d' /%3E%3Cpath d='M 40,0 C 58,38 74,54 100,70 L 100,0 Z' fill='%23eab308' /%3E%3Cpath d='M 50,0 C 66,34 78,46 100,60 L 100,0 Z' fill='%23991b1b' /%3E%3Cpath d='M 0,30 C 40,50 60,70 80,100 L 0,100 Z' fill='%237f1d1d' /%3E%3Cpath d='M 0,40 C 38,58 54,74 70,100 L 0,100 Z' fill='%23eab308' /%3E%3Cpath d='M 0,50 C 34,66 46,78 60,100 L 0,100 Z' fill='%23991b1b' /%3E%3C/svg%3E");
        background-size: cover;
    }
    .template-paper.t1 h6 {
        color: #1e1b4b;
    }
    .template-paper.t1 small {
        color: #7f1d1d;
    }
    .template-paper.t1 .line {
        background: #7f1d1d;
    }
    .template-paper.t1::before {
        content: '';
        position: absolute;
        top: 6px;
        left: 6px;
        width: 45px;
        height: 2px;
        background: #eab308;
        z-index: 8;
    }
    .template-paper.t1::after {
        content: '';
        position: absolute;
        bottom: 6px;
        right: 6px;
        width: 45px;
        height: 2px;
        background: #eab308;
        z-index: 8;
    }

    /* ─── Template 2 Mockup (Diagonal Gold Ribbons) ─── */
    .template-paper.t2 {
        background-color: #f8fafc;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 297 210' preserveAspectRatio='none'%3E%3Cpolygon points='0,0 60,0 0,60' fill='%23d4af37' /%3E%3Cpolygon points='0,0 55,0 0,55' fill='%23fef08a' /%3E%3Cpolygon points='0,0 40,0 0,40' fill='%23ca8a04' /%3E%3Cpolygon points='297,0 215,0 297,125' fill='%230f172a' /%3E%3Cpolygon points='297,210 185,210 297,135' fill='%23ca8a04' /%3E%3Cpolygon points='297,210 190,210 297,137' fill='%23fbbf24' /%3E%3C/svg%3E");
        background-size: cover;
    }
    .template-paper.t2 h6 {
        color: #0f172a;
    }
    .template-paper.t2 small {
        color: #0f172a;
    }
    .template-paper.t2 .line {
        background: #0f172a;
    }
    .template-paper.t2::before {
        content: '';
        position: absolute;
        top: 8px;
        left: 8px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 1.5px solid #d4af37;
        background: #ffffff;
        z-index: 8;
    }
    .template-paper.t2::after {
        content: '';
        position: absolute;
        top: 10px;
        left: 10px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        border: 0.5px solid #d4af37;
        background: #faf8f5;
        z-index: 9;
    }

    /* ─── Template 3 Mockup (Creative Theme) ─── */
    .template-paper.t3 {
        background-color: #fdfdfd;
        background-image: url("/aset/bg-creative.png");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
    }
    .template-paper.t3 h6 {
        color: #1e1b4b;
    }
    .template-paper.t3 small {
        color: #4c1d95;
    }
    .template-paper.t3 .line {
        background: #d97706;
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

    .upload-box {
        border-radius: 14px;
        border: 1px dashed #cbd5e1;
        background: #fff;
        padding: 14px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
        overflow: hidden;
        transition: .2s ease;
    }

    .upload-box:hover {
        border-color: var(--cert-primary);
        background: #fbfcff;
    }

    .upload-box.has-preview {
        border-style: solid;
        border-color: #dbe3ef;
        background: #fff;
    }

    .logo-card {
        min-height: 92px;
    }

    .signature-upload-area {
        height: 76px;
        min-height: 76px;
        margin-bottom: 10px;
    }

    .upload-preview {
        max-width: 100%;
        max-height: 58px;
        object-fit: contain;
        display: block;
        pointer-events: none;
    }

    .logo-card .upload-preview {
        max-height: 52px;
    }

    .preview-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        color: var(--cert-primary);
        pointer-events: none;
    }

    .preview-placeholder i {
        font-size: 24px;
    }

    .preview-placeholder span {
        font-size: 12px;
        font-weight: 900;
        color: #0f172a;
    }

    .preview-placeholder small {
        font-size: 11px;
        color: var(--cert-muted);
    }

    .preview-close {
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
        background: var(--cert-danger-soft);
        color: var(--cert-danger);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
        font-size: 12px;
        box-shadow: 0 4px 10px rgba(239, 68, 68, .14);
        cursor: pointer;
        z-index: 5;
    }

    .preview-close:hover {
        background: var(--cert-danger);
        color: #fff;
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
        min-height: 190px;
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
        background: var(--cert-primary-soft);
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

        .template-grid,
        .logo-grid,
        .signature-grid {
            grid-template-columns: 1fr;
        }

        .cert-hero {
            padding: 26px;
        }

        .cert-hero h1 {
            font-size: 24px;
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
                        <div class="page-eyebrow">Sistem Rekognisi</div>
                        <h1>Konfigurasi Sertifikat</h1>
                        <p>
                            {{ strtoupper($context === 'course' ? 'Kursus' : 'Acara') }}: {{ $modelTitle }}<br>
                            Trainer: {{ $trainer->name }}
                        </p>

                        @if($isCrmSource)
                            <div class="crm-source-badge">
                                <i class="bi bi-shield-check"></i>
                                Aset sertifikat mengikuti data CRM
                            </div>
                        @endif
                    </div>
                </section>

                <div class="config-card">
                    <section class="config-section">
                        <div class="cert-step-header">
                            <div class="cert-step-badge">1</div>
                            <div>
                                <h5 class="cert-section-title">Pilih Template Desain</h5>
                                <p class="cert-section-subtitle">Pilih desain template sertifikat yang akan digunakan</p>
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

                                            <div class="template-name">{{ $data[0] }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </section>

                    <section class="config-section">
                        <div class="cert-step-header">
                            <div class="cert-step-badge">2</div>
                            <div>
                                <h5 class="cert-section-title">Upload Logo Partner</h5>
                                <p class="cert-section-subtitle">
                                    Upload logo partner atau sponsor yang akan ditampilkan di sertifikat
                                </p>
                            </div>
                        </div>

                        <div class="section-content">
                            <div class="logo-grid">
                                @foreach($logos as $logo)
                                    <div class="logo-card upload-box has-preview" data-asset-id="{{ $logo->id }}">
                                        <button type="button"
                                                class="preview-close"
                                                data-asset-id="{{ $logo->id }}">
                                            <i class="bi bi-x-lg"></i>
                                        </button>

                                        <img src="{{ $assetUrl($logo->image_path) }}"
                                             alt="Logo"
                                             class="upload-preview">

                                        <input type="file"
                                               name="certificate_logo[]"
                                               accept=".jpg,.jpeg,.png,.webp,.svg"
                                               hidden>
                                    </div>
                                @endforeach

                                @for($i = $logos->count(); $i < 3; $i++)
                                    <div class="logo-card upload-box">
                                        <input type="file"
                                               name="certificate_logo[]"
                                               accept=".jpg,.jpeg,.png,.webp,.svg"
                                               hidden>

                                        <div class="preview-placeholder">
                                            <i class="bi bi-plus-lg"></i>
                                            <span>Tambah Logo</span>
                                            <small>Maks. 3 logo</small>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </section>

                    <section class="config-section">
                        <div class="cert-step-header">
                            <div class="cert-step-badge">3</div>
                            <div>
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

                                    <div class="signature-card" data-index="{{ $i }}">
                                        <div class="signature-upload-area upload-box {{ $signature ? 'has-preview' : '' }}"
                                             @if($signature) data-asset-id="{{ $signature->id }}" @endif>
                                            @if($signature)
                                                <button type="button"
                                                        class="preview-close"
                                                        data-asset-id="{{ $signature->id }}">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>

                                                <img src="{{ $assetUrl($signature->image_path) }}"
                                                     alt="Signature"
                                                     class="upload-preview">
                                            @else
                                                <div class="preview-placeholder">
                                                    <i class="bi bi-plus-lg"></i>
                                                    <span>Tambah TTD</span>
                                                    <small>Maks. 3</small>
                                                </div>
                                            @endif

                                            <input type="file"
                                                   name="certificate_signature_file[{{ $i }}]"
                                                   accept=".jpg,.jpeg,.png,.webp,.svg"
                                                   hidden>
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
                            <div class="guide-icon">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div>
                                <h6>Template Desain</h6>
                                <p>Pilih salah satu template sertifikat yang tersedia.</p>
                            </div>
                        </div>

                        <div class="guide-item">
                            <div class="guide-icon">
                                <i class="bi bi-image"></i>
                            </div>
                            <div>
                                <h6>Logo Partner</h6>
                                <p>Format PNG/JPG/SVG, ukuran maksimal 2MB, maksimal 3 logo.</p>
                            </div>
                        </div>

                        <div class="guide-item">
                            <div class="guide-icon">
                                <i class="bi bi-pen"></i>
                            </div>
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
    const removeAssetsContainer = document.getElementById('removeAssetsContainer');

    function addRemoveAssetInput(assetId) {
        if (!assetId || !removeAssetsContainer) return;

        const exists = removeAssetsContainer.querySelector(`input[name="remove_assets[]"][value="${assetId}"]`);
        if (exists) return;

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'remove_assets[]';
        input.value = assetId;

        removeAssetsContainer.appendChild(input);
    }

    function getBoxType(box) {
        return box.classList.contains('logo-card') ? 'logo' : 'signature';
    }

    function makePlaceholder(box) {
        const type = getBoxType(box);

        const placeholder = document.createElement('div');
        placeholder.className = 'preview-placeholder';
        placeholder.innerHTML = `
            <i class="bi bi-plus-lg"></i>
            <span>${type === 'logo' ? 'Tambah Logo' : 'Tambah TTD'}</span>
            <small>${type === 'logo' ? 'Maks. 3 logo' : 'Maks. 3'}</small>
        `;

        box.appendChild(placeholder);
    }

    function clearBox(box, assetId = null) {
        if (assetId) {
            addRemoveAssetInput(assetId);
        }

        const preview = box.querySelector('.upload-preview');
        const closeBtn = box.querySelector('.preview-close');
        const placeholder = box.querySelector('.preview-placeholder');
        const input = box.querySelector('input[type="file"]');

        if (preview) preview.remove();
        if (closeBtn) closeBtn.remove();
        if (placeholder) placeholder.remove();

        if (input) {
            input.value = '';
        }

        box.classList.remove('has-preview');
        makePlaceholder(box);
    }

    function setPreview(input) {
        if (!input.files || !input.files[0]) return;

        const box = input.closest('.upload-box');
        if (!box) return;

        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function (event) {
            const oldPreview = box.querySelector('.upload-preview');
            const oldPlaceholder = box.querySelector('.preview-placeholder');
            const oldClose = box.querySelector('.preview-close');

            if (oldPreview) oldPreview.remove();
            if (oldPlaceholder) oldPlaceholder.remove();
            if (oldClose) oldClose.remove();

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'preview-close';
            closeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';

            const img = document.createElement('img');
            img.src = event.target.result;
            img.className = 'upload-preview';
            img.alt = 'Preview';

            box.prepend(img);
            box.prepend(closeBtn);
            box.classList.add('has-preview');
        };

        reader.readAsDataURL(file);
    }

    document.addEventListener('click', function (event) {
        const closeBtn = event.target.closest('.preview-close');

        if (closeBtn) {
            event.preventDefault();
            event.stopPropagation();

            const box = closeBtn.closest('.upload-box');
            const assetId = closeBtn.dataset.assetId;

            if (box) {
                clearBox(box, assetId);
            }

            return;
        }

        const box = event.target.closest('.upload-box');

        if (!box) return;

        if (
            event.target.closest('button') ||
            event.target.closest('input') ||
            event.target.closest('textarea')
        ) {
            return;
        }

        const input = box.querySelector('input[type="file"]');

        if (input) {
            input.click();
        }
    });

    document.addEventListener('change', function (event) {
        const input = event.target;

        if (!input.matches('input[type="file"]')) return;

        setPreview(input);
    });
});
</script>
@endpush