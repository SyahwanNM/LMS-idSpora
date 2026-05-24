@extends('layouts.admin-trainer')

@section('title', 'Detail Sertifikat')

@php
    $certificate = $certificate ?? null;
    $trainer = $trainer ?? $certificate?->trainer;
    $model = $model ?? $certificate?->certifiable;
    $assets = $assets ?? collect();

    $modelTitle = $model?->title ?? $model?->name ?? 'Program Sertifikat';
    $trainerName = $trainer?->name ?? '-';

    $template = $assets->where('type', 'template')->first();
    $logos = $assets->where('type', 'logo')->values();
    $signatures = $assets->where('type', 'signature')->values();

    $issuedDate = $certificate?->issued_at ?? $certificate?->created_at;
    $programType = $model ? class_basename(get_class($model)) : '-';
@endphp

@push('admin-trainer-styles')
<style>
    :root {
        --cert-primary: #2f3fcb;
        --cert-primary-2: #4858db;
        --cert-border: #e6eaf2;
        --cert-muted: #6b7a99;
        --cert-success: #059669;
        --cert-gold: #d8a835;
        --cert-navy: #102a4c;
    }

    .detail-page { width: 100%; }

    .detail-breadcrumb {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
        font-size: 13px;
        color: #74809a;
    }

    .back-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid var(--cert-border);
        background: #fff;
        color: var(--cert-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .detail-hero {
        background: linear-gradient(135deg, #2935b8 0%, #4858db 58%, #dce3ff 100%);
        border-radius: 20px;
        padding: 34px 36px;
        color: #fff;
        min-height: 170px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(47, 63, 203, .14);
        margin-bottom: 24px;
    }

    .detail-hero::after {
        content: '';
        position: absolute;
        right: 55px;
        top: 24px;
        width: 250px;
        height: 125px;
        border-radius: 26px;
        background: rgba(255,255,255,.18);
    }

    .detail-hero::before {
        content: '✦';
        position: absolute;
        right: 95px;
        top: 78px;
        color: rgba(255,255,255,.75);
        font-size: 46px;
        z-index: 2;
    }

    .hero-content {
        position: relative;
        z-index: 3;
        max-width: 620px;
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
        margin-bottom: 18px;
    }

    .page-eyebrow::before {
        content: '';
        width: 22px;
        height: 2px;
        background: rgba(255,255,255,.9);
        border-radius: 999px;
    }

    .detail-hero h1 {
        font-size: 32px;
        font-weight: 900;
        margin: 0 0 12px;
        letter-spacing: -.6px;
    }

    .detail-hero p {
        margin: 0;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(255,255,255,.95);
    }

    .preview-card,
    .history-card,
    .side-card {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 20px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
    }

    .preview-card {
        padding: 24px;
        margin-bottom: 22px;
    }

    .section-title {
        font-size: 17px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 18px;
    }

    .certificate-preview-wrap {
        display: flex;
        justify-content: center;
    }

    .certificate-preview {
        width: 760px;
        max-width: 100%;
        aspect-ratio: 16 / 9;
        background: #fff;
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .08);
    }

    .cert-bg-blue-top {
        position: absolute;
        top: 0;
        left: 0;
        width: 42%;
        height: 92px;
        background: linear-gradient(135deg, #102a4c, #173b69);
        border-bottom-right-radius: 85% 70%;
    }

    .cert-bg-gold-top {
        position: absolute;
        top: 36px;
        left: 0;
        width: 45%;
        height: 34px;
        background: linear-gradient(90deg, #d8a835, #f5d77a);
        transform: skewY(-8deg);
    }

    .cert-bg-blue-bottom {
        position: absolute;
        bottom: 0;
        right: 0;
        width: 45%;
        height: 82px;
        background: linear-gradient(135deg, #102a4c, #173b69);
        border-top-left-radius: 90% 70%;
    }

    .cert-bg-gold-bottom {
        position: absolute;
        bottom: 50px;
        right: 0;
        width: 50%;
        height: 30px;
        background: linear-gradient(90deg, #f5d77a, #d8a835);
        transform: skewY(-8deg);
    }

    .cert-border-line {
        position: absolute;
        inset: 28px;
        border: 2px solid #d8a835;
        pointer-events: none;
    }

    .cert-content {
        position: relative;
        z-index: 4;
        height: 100%;
        padding: 58px 70px 42px;
        text-align: center;
        color: #172554;
    }

    .cert-content h2 {
        font-family: Georgia, serif;
        font-size: 38px;
        line-height: 1.08;
        letter-spacing: 4px;
        font-weight: 500;
        margin-bottom: 18px;
    }

    .cert-content .given {
        font-size: 12px;
        letter-spacing: 2px;
        margin-bottom: 14px;
        font-weight: 700;
    }

    .cert-name {
        font-family: 'Brush Script MT', cursive;
        font-size: 42px;
        color: #111827;
        margin-bottom: 4px;
    }

    .cert-line {
        width: 380px;
        max-width: 70%;
        height: 2px;
        background: #1f2937;
        margin: 0 auto 18px;
    }

    .cert-desc {
        color: #111827;
        font-size: 13px;
        line-height: 1.6;
    }

    .cert-program {
        display: block;
        font-size: 18px;
        font-weight: 900;
        margin: 4px 0;
    }

    .cert-signatures {
        position: absolute;
        bottom: 38px;
        left: 100px;
        right: 100px;
        display: flex;
        justify-content: space-between;
        align-items: end;
    }

    .signature-box {
        width: 160px;
        text-align: center;
        color: #111827;
        font-size: 10px;
    }

    .signature-text {
        font-family: 'Brush Script MT', cursive;
        font-size: 32px;
        margin-bottom: -4px;
    }

    .signature-line {
        height: 1px;
        background: #475569;
        margin-bottom: 4px;
    }

    .medal {
        width: 66px;
        height: 66px;
        border-radius: 50%;
        background: radial-gradient(circle, #ffe89a, #d8a835);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 28px;
        box-shadow: 0 8px 18px rgba(216, 168, 53, .35);
    }

    .preview-actions {
        display: flex;
        justify-content: center;
        gap: 14px;
        margin-top: 18px;
    }

    .btn-preview-action {
        min-width: 210px;
        height: 44px;
        border-radius: 10px;
        border: 1px solid #dbe3ef;
        background: #fff;
        color: #334155;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        text-decoration: none;
    }

    .btn-preview-action.primary-soft {
        background: #eef1ff;
        color: var(--cert-primary);
        border-color: #eef1ff;
    }

    .history-card {
        padding: 22px;
    }

    .history-table-wrap {
        border: 1px solid var(--cert-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .history-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .history-table th {
        background: #f8fafc;
        color: #6b7a99;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        padding: 13px 16px;
    }

    .history-table td {
        padding: 15px 16px;
        border-top: 1px solid var(--cert-border);
        color: #334155;
        font-size: 13px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #dcfce7;
        color: #15803d;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 900;
    }

    .action-eye {
        width: 32px;
        height: 32px;
        border: 1px solid var(--cert-border);
        border-radius: 8px;
        background: #fff;
        color: var(--cert-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .side-card {
        overflow: hidden;
        margin-bottom: 20px;
    }

    .side-content {
        padding: 24px;
    }

    .side-title {
        font-size: 17px;
        font-weight: 900;
        color: var(--cert-primary);
        margin-bottom: 18px;
    }

    .info-item,
    .asset-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 20px;
    }

    .info-item:last-child,
    .asset-item:last-child {
        margin-bottom: 0;
    }

    .info-icon,
    .asset-icon {
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

    .info-label,
    .asset-label {
        font-size: 13px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 5px;
    }

    .info-value,
    .asset-value {
        font-size: 13px;
        color: #64748b;
        line-height: 1.4;
    }

    .asset-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex: 1;
        gap: 10px;
    }

    .asset-status {
        background: #dcfce7;
        color: #15803d;
        border-radius: 999px;
        padding: 4px 9px;
        font-size: 10px;
        font-weight: 900;
        white-space: nowrap;
    }

    .template-card-footer {
        padding: 18px;
        border-top: 1px solid var(--cert-border);
        text-align: center;
    }

    .btn-template {
        width: 100%;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #2f3fcb, #2636bd);
        color: #fff;
        text-decoration: none;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
    }

    .btn-template:hover {
        color: #fff;
    }

    .template-note {
        font-size: 12px;
        color: #64748b;
        line-height: 1.6;
        margin: 14px 0 0;
    }

    @media(max-width: 768px) {
        .detail-hero { padding: 26px; }
        .detail-hero h1 { font-size: 26px; }
        .preview-card { padding: 18px; }
        .cert-content { padding: 38px 30px; }
        .cert-content h2 { font-size: 24px; }
        .cert-name { font-size: 30px; }
        .cert-signatures { left: 30px; right: 30px; }
        .preview-actions { flex-direction: column; }
        .btn-preview-action { width: 100%; }
    }
</style>
@endpush

@section('admin-trainer-content')
<div class="detail-page">

    <div class="detail-breadcrumb">
        <a href="{{ route('admin.trainer.certificates.show', $trainer->id) }}" class="back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>

        <span>Sertifikat & Penghargaan</span>
        <i class="bi bi-chevron-right"></i>
        <strong class="text-primary">Detail Sertifikat</strong>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-xl-9">
            <section class="detail-hero">
                <div class="hero-content">
                    <div class="page-eyebrow">Recognition System</div>
                    <h1>Detail Sertifikat</h1>
                    <p>
                        Informasi lengkap sertifikat yang telah diterbitkan.
                    </p>
                </div>
            </section>

            <section class="preview-card">
                <h5 class="section-title">Preview Sertifikat</h5>

                <div class="certificate-preview-wrap">
                    <div class="certificate-preview">
                        <div class="cert-bg-blue-top"></div>
                        <div class="cert-bg-gold-top"></div>
                        <div class="cert-bg-blue-bottom"></div>
                        <div class="cert-bg-gold-bottom"></div>
                        <div class="cert-border-line"></div>

                        <div class="cert-content">
                            <h2>SERTIFIKAT<br>PENGHARGAAN</h2>
                            <div class="given">DIBERIKAN KEPADA</div>

                            <div class="cert-name">{{ $trainerName }}</div>
                            <div class="cert-line"></div>

                            <div class="cert-desc">
                                Atas partisipasinya sebagai trainer dalam
                                <span class="cert-program">{{ $modelTitle }}</span>
                                yang diterbitkan pada
                                {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y') : '-' }}
                            </div>

                            <div class="cert-signatures">
                                <div class="signature-box">
                                    <div class="signature-text">
                                        {{ $signatures->get(0)?->name ? 'Sign' : 'Sign' }}
                                    </div>
                                    <div class="signature-line"></div>
                                    <strong>{{ $signatures->get(0)?->name ?? 'Admin idSpora' }}</strong><br>
                                    {{ $signatures->get(0)?->position ?? 'Learning Manager' }}
                                </div>

                                <div class="medal">
                                    <i class="bi bi-star-fill"></i>
                                </div>

                                <div class="signature-box">
                                    <div class="signature-text">
                                        {{ $signatures->get(1)?->name ? 'Sign' : 'Sign' }}
                                    </div>
                                    <div class="signature-line"></div>
                                    <strong>{{ $signatures->get(1)?->name ?? 'Head of Training' }}</strong><br>
                                    {{ $signatures->get(1)?->position ?? 'idSpora' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="preview-actions">
                    <a href="#" class="btn-preview-action">
                        <i class="bi bi-download"></i>
                        Download Sertifikat
                    </a>

                    <a href="#" class="btn-preview-action primary-soft">
                        <i class="bi bi-arrows-fullscreen"></i>
                        Pratinjau Penuh
                    </a>
                </div>
            </section>

            <section class="history-card">
                <h5 class="section-title mb-3">Riwayat Penerbitan</h5>

                <div class="history-table-wrap">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Diterbitkan Oleh</th>
                                <th>Nomor Sertifikat</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>
                                    {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y, H:i') . ' WIB' : '-' }}
                                </td>
                                <td>{{ $certificate->issuer?->name ?? 'Admin idSpora' }}</td>
                                <td>{{ $certificate->certificate_number ?? '-' }}</td>
                                <td>
                                    <span class="status-pill">
                                        <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                                        Diterbitkan
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="action-eye">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-xl-3">
            <aside class="side-card">
                <div class="side-content">
                    <h5 class="side-title">Informasi Program</h5>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <div class="info-label">Tipe Program</div>
                            <div class="info-value">{{ $programType }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-tag"></i>
                        </div>
                        <div>
                            <div class="info-label">Judul Program</div>
                            <div class="info-value">{{ $modelTitle }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div>
                            <div class="info-label">Tanggal Terbit</div>
                            <div class="info-value">
                                {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <div class="info-label">Trainer</div>
                            <div class="info-value">{{ $trainerName }}</div>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="info-label">Status Sertifikat</div>
                            <span class="status-pill">
                                <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                                Diterbitkan
                            </span>
                        </div>
                    </div>
                </div>
            </aside>

            <aside class="side-card">
                <div class="side-content">
                    <h5 class="side-title">Aset Sertifikat</h5>

                    <div class="asset-item">
                        <div class="asset-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="asset-row">
                            <div>
                                <div class="asset-label">Template</div>
                                <div class="asset-value">{{ $template?->name ?? 'Template 1' }}</div>
                            </div>
                            <span class="asset-status">Tersedia</span>
                        </div>
                    </div>

                    <div class="asset-item">
                        <div class="asset-icon">
                            <i class="bi bi-file-image"></i>
                        </div>
                        <div class="asset-row">
                            <div>
                                <div class="asset-label">Logo Partner</div>
                                <div class="asset-value">{{ $logos->count() }} Logo</div>
                            </div>
                            <span class="asset-status">Tersedia</span>
                        </div>
                    </div>

                    <div class="asset-item">
                        <div class="asset-icon">
                            <i class="bi bi-pen"></i>
                        </div>
                        <div class="asset-row">
                            <div>
                                <div class="asset-label">Tanda Tangan</div>
                                <div class="asset-value">{{ $signatures->count() }} TTD</div>
                            </div>
                            <span class="asset-status">Tersedia</span>
                        </div>
                    </div>
                </div>
            </aside>

            <aside class="side-card">
                <div class="template-card-footer">
                    @if($model)
                        <a href="{{ route('admin.trainer.certificates.edit', [
                            'trainer' => $trainer->id,
                            'context' => strtolower($programType) === 'course' ? 'course' : 'event',
                            'id' => $model->id,
                        ]) }}" class="btn-template">
                            <i class="bi bi-gear"></i>
                            Kelola Template
                        </a>
                    @else
                        <a href="{{ route('admin.trainer.certificates.index') }}" class="btn-template">
                            <i class="bi bi-gear"></i>
                            Kelola Template
                        </a>
                    @endif

                    <p class="template-note">
                        Ubah template, logo, atau tanda tangan jika diperlukan.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection