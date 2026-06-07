@extends('layouts.admin-trainer')

@section('title', 'Detail Sertifikat')

@php
    $certificate = $certificate ?? null;
    $trainer = $trainer ?? $certificate?->trainer;
    $model = $model ?? $certificate?->certifiable;
    $assets = $assets ?? collect();

    $modelTitle = $model?->title ?? $model?->name ?? 'Program Sertifikat';
    $trainerName = $trainer?->name ?? '-';
    $issuedDate = $certificate?->issued_at ?? $certificate?->created_at;
    $programTypeRaw = $model ? class_basename(get_class($model)) : '-';
    $programType = strtolower($programTypeRaw) === 'course' ? 'Kursus' : (strtolower($programTypeRaw) === 'event' ? 'Acara' : $programTypeRaw);
    $context = $model && strtolower(class_basename(get_class($model))) === 'course' ? 'course' : 'event';

    $templateAsset = $assets->where('type', 'template')->first();
    $templateName = $templateAsset?->name
        ?? $model?->certificate_template
        ?? 'template_1';

    $logos = $assets->where('type', 'logo')->values();
    $signatures = $assets->where('type', 'signature')->values();

    $isPublished = in_array($certificate?->status ?? '', ['sent', 'published']);
    $publishBtnText = $isPublished ? 'Terbitkan Ulang' : 'Terbitkan Sertifikat';
    $publishBtnIcon = $isPublished ? 'bi-arrow-counterclockwise' : 'bi-send-check';

    if ($model && \Route::has('certificates.events.download') && \Route::has('certificates.courses.download')) {
        $downloadUrl = $context === 'course'
            ? route('certificates.courses.download', $model)
            : route('certificates.events.download', $model);
    } else {
        $downloadUrl = route('admin.trainer.certificates.detail', [
            'certificate' => $certificate->id,
        ]);
    }

    $fullPreviewUrl = route('admin.trainer.certificates.detail', [
        'certificate' => $certificate->id,
        'full' => 1,
    ]);

    $assetUrl = function (?string $path): ?string {
        if (!$path) {
            return null;
        }
        if (preg_match('~^https?://~i', $path)) {
            return $path;
        }

        $normalized = ltrim(str_replace('\\', '/', $path), '/');
        $normalized = preg_replace('~^public/~i', '', $normalized) ?? $normalized;
        $normalized = preg_replace('~^storage/app/public/~i', '', $normalized) ?? $normalized;

        if (str_starts_with($normalized, 'storage/')) {
            return asset($normalized);
        }

        if (str_starts_with($normalized, 'uploads/')) {
            return asset($normalized);
        }

        $storageUrl = \Storage::disk('public')->url($normalized);
        if (file_exists(public_path('uploads/' . $normalized))) {
            return asset('uploads/' . $normalized);
        }
        if (file_exists(public_path('storage/' . $normalized))) {
            return $storageUrl;
        }
        if (file_exists(public_path($normalized))) {
            return asset($normalized);
        }

        return $storageUrl;
    };
@endphp

@push('admin-trainer-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');

        :root {
            --cert-primary: #2f3fcb;
            --cert-primary-2: #4858db;
            --cert-border: #e6eaf2;
            --cert-muted: #6b7a99;
            --cert-success: #059669;
            --cert-gold: #d8a835;
            --cert-navy: #102a4c;
        }

        .detail-page {
            width: 100%;
            font-family: 'Poppins', sans-serif;
            font-weight: 500;
        }

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
            background: rgba(255, 255, 255, .18);
        }

        .detail-hero::before {
            content: '✦';
            position: absolute;
            right: 95px;
            top: 78px;
            color: rgba(255, 255, 255, .75);
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
            color: rgba(255, 255, 255, .9);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .page-eyebrow::before {
            content: '';
            width: 22px;
            height: 2px;
            background: rgba(255, 255, 255, .9);
            border-radius: 999px;
        }

        .detail-hero h1 {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 12px;
            letter-spacing: -.6px;
        }

        .detail-hero p {
            margin: 0;
            font-size: 15px;
            line-height: 1.7;
            color: rgba(255, 255, 255, .95);
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
            font-weight: 700;
            color: #0f172a;
            margin: 5px;
            padding-top: 0px;
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

        .certificate-preview > .template_1,
        .certificate-preview > .template_2,
        .certificate-preview > .template_3 {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 1;
        }

        .tpl-inner {
            position: relative;
            height: 100%;
            width: 100%;
            padding: 20px 36px;
            text-align: center;
            color: #1e293b;
            box-sizing: border-box;
            z-index: 2;
        }

        .tpl-logo-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
            flex-wrap: wrap;
        }

        .tpl-logo {
            height: 28px;
            max-width: 100px;
            object-fit: contain;
        }

        .tpl-title {
            font-family: 'Georgia', serif;
            font-size: 20px;
            letter-spacing: 2px;
            margin: 6px 0 2px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .tpl-subtitle {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .presented-text {
            font-size: 10px;
            color: #64748b;
            font-style: italic;
            margin: 4px 0 2px;
        }

        .tpl-name {
            font-family: 'Great Vibes', 'Georgia', serif;
            font-size: 28px;
            font-weight: normal;
            margin: 6px 0;
        }

        .tpl-desc {
            font-size: 10px;
            line-height: 1.5;
            color: #1f2937;
            max-width: 580px;
            margin: 6px auto 0;
        }

        .tpl-footer {
            margin-top: 18px;
            display: flex;
            justify-content: center;
            gap: 24px;
        }

        .tpl-sig {
            text-align: center;
            min-width: 120px;
            font-size: 9px;
            color: #0f172a;
        }

        .tpl-sig img {
            max-height: 32px;
            max-width: 90px;
            object-fit: contain;
            display: block;
            margin: 0 auto 2px;
        }

        .tpl-sig-line {
            border-top: 1px solid #0f172a;
            margin: 4px 0;
        }

        /* ─── Template 1: Premium Royal (Maroon & Gold Waves) ─── */
        .template_1 {
            padding: 16px;
            background: #ffffff;
        }
        .template_1 .tpl-title {
            color: #1e1b4b;
        }
        .template_1 .tpl-subtitle {
            color: #7f1d1d;
            margin-bottom: 6px;
        }
        .template_1 .tpl-name {
            color: #7f1d1d;
        }
        .template_1 .tpl-sig-line {
            border-bottom: 1.5px dashed #7f1d1d;
        }

        /* ─── Template 2: Modern Corporate (Sleek Ribbon & Navy Corners) ─── */
        .template_2 {
            padding: 0;
            background: #f8fafc;
        }
        .template_2 .tpl-inner {
            padding: 24px 20px 20px 20px;
        }
        .template_2 .tpl-title {
            color: #0f172a;
        }
        .template_2 .tpl-subtitle {
            color: #475569;
            margin-bottom: 6px;
        }
        .template_2 .tpl-name {
            color: #0f172a;
            margin: 8px auto;
            border-bottom: 1.5px solid #0f172a;
            padding-bottom: 2px;
        }
        .template_2 .gold-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 2px solid #d4af37;
            background: #ffffff;
            z-index: 5;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .template_2 .gold-badge-inner {
            position: absolute;
            top: 3px; left: 3px; right: 3px; bottom: 3px;
            border-radius: 50%;
            border: 1px solid #d4af37;
            background: #faf8f5;
        }
        .template_2 .tpl-inner {
            padding-left: 90px;
        }
        .template_2 .tpl-verification {
            left: 90px;
        }

        /* ─── Template 3: Creative Professional (Dynamic Wave & Gradients) ─── */
        .template_3 {
            padding: 0;
            background: #fdfdfd;
            border: 8px solid #ffffff;
        }
        .template_3 .tpl-inner {
            padding: 20px 20px 10px 20px;
        }
        .template_3 .tpl-title {
            color: #1e1b4b;
        }
        .template_3 .tpl-subtitle {
            color: #d97706;
            margin-bottom: 6px;
        }
        .template_3 .tpl-name {
            color: #4c1d95;
            margin: 6px auto;
            border-bottom: 1.5px solid #d97706;
            padding-bottom: 2px;
        }
        .template_3 .tpl-sig {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 4px 10px;
            border-radius: 8px;
        }
        .template_3 .tpl-sig-line {
            border-bottom-color: #4c1d95;
        }
        .template_3 .tpl-verification {
            left: 40px;
        }
        .template_3 .tpl-cert-id {
            right: 40px;
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
            font-weight: 600;
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
            overflow-x: auto;
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
            font-weight: 700;
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
            font-weight: 600;
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
            font-weight: 700;
            color: var(--cert-primary);
            margin-bottom: 18px;
        }

        .info-item {
            flex-direction: row;
            justify-content: flex-start;
            gap: 14px;
            margin-bottom: 20px;
        }

        .asset-item {
            display: flex;
            gap: 14px;
            margin-bottom: 20px;
        }

        .info-item:last-child,
        .asset-item:last-child {
            margin-bottom: 0;
        }

        .info-icon,
        .asset-icon {
            width: 40px;
            height: auto;
            align-self: stretch;
            min-height: 34px;
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
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .info-value,
        .asset-value {
            font-size: 13px;
            color: #64748b;
            line-height: 1.4;
            font-weight: 500;
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
            font-weight: 600;
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
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
        }

        .btn-template:hover {
            color: #fff;
        }

        .btn-template.btn-outline {
            border: 1.5px solid var(--cert-primary);
            background: transparent;
            color: var(--cert-primary);
        }

        .btn-template.btn-outline:hover {
            background: #eef1ff;
            color: var(--cert-primary-2);
        }

        .status-pill.draft {
            background: #fef3c7;
            color: #d97706;
        }

        .template-note {
            font-size: 12px;
            color: #64748b;
            line-height: 1.6;
            margin: 14px 0 0;
        }

        @media(max-width: 768px) {
            .detail-hero {
                padding: 26px;
            }

            .detail-hero h1 {
                font-size: 26px;
            }

            .preview-card {
                padding: 18px;
            }

            .tpl-inner {
                padding: 28px 24px;
            }

            .tpl-title {
                font-size: 22px;
            }

            .tpl-name {
                font-size: 26px;
            }

            .template_2 .tpl-inner {
                padding: 28px 24px 28px 84px;
            }

            .tpl-footer {
                justify-content: center;
                flex-wrap: wrap;
            }

            .preview-actions {
                flex-direction: column;
            }

            .btn-preview-action {
                width: 100%;
            }
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="detail-page">

        <div class="detail-breadcrumb">
            <a href="{{ route('admin.trainer.certificates.index')}}" class="back-btn">
                <i class="bi bi-chevron-left"></i>
            </a>

            <span>Sertifikat & Penghargaan</span>
            <i class="bi bi-chevron-right"></i>
            <strong class="text-primary">Detail Sertifikat</strong>
        </div>

        <div class="row g-4">
            <div class="col-xl-9">
                <section class="detail-hero">
                    <div class="hero-content">
                        <div class="page-eyebrow">Sistem Rekognisi</div>
                        <h1>Detail Sertifikat</h1>
                        <p>
                            Informasi lengkap sertifikat yang telah diterbitkan.
                        </p>
                    </div>
                </section>

                <section class="preview-card">
                    <h5 class="section-title mb-3">Preview Sertifikat</h5>

                    <div class="certificate-preview-wrap">
                        <div class="certificate-preview {{ $templateName }}">
                            @if($templateName === 'template_1')
                                <div class="template_1">
                                    <!-- Top Left Gold Bar -->
                                    <div style="position: absolute; top: 12px; left: 12px; width: 120px; height: 3px; background: #eab308; z-index: 2;"></div>
                                    <!-- Bottom Right Gold Bar -->
                                    <div style="position: absolute; bottom: 12px; right: 12px; width: 120px; height: 3px; background: #eab308; z-index: 2;"></div>

                                    <!-- Top Right Maroon & Gold Waves -->
                                    <div style="position: absolute; top: 0; right: 0; width: 140px; height: 140px; z-index: 1; pointer-events: none;">
                                        <svg width="140px" height="140px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                            <path d="M 30,0 C 50,40 70,60 100,80 L 100,0 Z" fill="#7f1d1d" />
                                            <path d="M 40,0 C 58,38 74,54 100,70 L 100,0 Z" fill="#eab308" />
                                            <path d="M 50,0 C 66,34 78,46 100,60 L 100,0 Z" fill="#991b1b" />
                                            <path d="M 65,0 C 78,26 86,34 100,45 L 100,0 Z" fill="#eab308" />
                                            <path d="M 75,0 C 85,20 90,25 100,35 L 100,0 Z" fill="#7f1d1d" />
                                        </svg>
                                    </div>

                                    <!-- Bottom Left Maroon & Gold Waves -->
                                    <div style="position: absolute; bottom: 0; left: 0; width: 140px; height: 140px; z-index: 1; pointer-events: none;">
                                        <svg width="140px" height="140px" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                            <path d="M 0,30 C 40,50 60,70 80,100 L 0,100 Z" fill="#7f1d1d" />
                                            <path d="M 0,40 C 38,58 54,74 70,100 L 0,100 Z" fill="#eab308" />
                                            <path d="M 0,50 C 34,66 46,78 60,100 L 0,100 Z" fill="#991b1b" />
                                            <path d="M 0,65 C 26,78 34,86 45,100 L 0,100 Z" fill="#eab308" />
                                            <path d="M 0,75 C 20,85 25,90 35,100 L 0,100 Z" fill="#7f1d1d" />
                                        </svg>
                                    </div>
                                </div>
                            @elseif($templateName === 'template_2')
                                <div class="template_2">
                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;">
                                        <svg width="100%" height="100%" viewBox="0 0 297 210" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                            <!-- Top-left diagonal gold ribbon -->
                                            <polygon points="0,0 60,0 0,60" fill="#d4af37" />
                                            <polygon points="0,0 55,0 0,55" fill="#fef08a" />
                                            <polygon points="0,0 40,0 0,40" fill="#ca8a04" />
                                            
                                            <!-- Right side navy & gold triangles -->
                                            <polygon points="297,0 215,0 297,125" fill="#0f172a" />
                                            <polygon points="297,210 185,210 297,135" fill="#ca8a04" />
                                            <polygon points="297,210 190,210 297,137" fill="#fbbf24" />
                                        </svg>
                                    </div>
                                    
                                    <div class="gold-badge">
                                        <div class="gold-badge-inner"></div>
                                    </div>
                                </div>
                            @else
                                <div class="template_3">
                                    <img src="{{ asset('aset/bg-creative.png') }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
                                </div>
                            @endif

                            {{-- Content wrap containing layout details --}}
                            <div class="tpl-inner">
                                <div class="logo-row">
                                    <img class="tpl-logo" src="{{ asset('aset/logo idspora_dark.png') }}" onerror="this.src='{{ asset('aset/logo-idspora.png') }}'" alt="Logo idSpora">
                                    @foreach($logos as $logo)
                                        @php $logoUrl = $assetUrl($logo->image_path); @endphp
                                        @if($logoUrl)
                                            <img class="tpl-logo" src="{{ $logoUrl }}" alt="Logo">
                                        @endif
                                    @endforeach
                                </div>

                                <div class="tpl-title">Sertifikat Penghargaan</div>
                                <div class="tpl-subtitle">Narasumber</div>

                                <div class="presented-text">Diberikan kepada:</div>
                                <div class="tpl-name">{{ $trainerName }}</div>

                                <div class="tpl-desc">
                                    Atas kontribusinya sebagai narasumber dalam
                                    <strong>{{ $modelTitle }}</strong>,
                                    diterbitkan pada
                                    {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y') : '-' }}.
                                </div>

                                <div class="tpl-footer">
                                    @foreach($signatures->take(3) as $signature)
                                        <div class="tpl-sig">
                                            @php $sigUrl = $assetUrl($signature->image_path); @endphp
                                            @if($sigUrl)
                                                <img src="{{ $sigUrl }}" alt="Signature">
                                            @endif
                                            <div class="tpl-sig-line"></div>
                                            <strong>{{ $signature->name ?? 'Admin idSpora' }}</strong><br>
                                            {{ $signature->position ?? 'Learning Manager' }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="tpl-verification">VERIFIED BY IDSPORA.COM</div>
                            <div class="tpl-cert-id">Verified Certificate ID: {{ $certificate->certificate_number ?? '-' }}</div>
                        </div>
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
                                @if($isPublished)
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
                                            <a href="{{ $fullPreviewUrl }}" class="action-eye">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">
                                            Sertifikat belum diterbitkan. Silakan klik tombol <strong>Terbitkan Sertifikat</strong> di menu sebelah kanan.
                                        </td>
                                    </tr>
                                @endif
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
                                <span class="status-pill {{ $isPublished ? '' : 'draft' }}">
                                    <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                                    {{ $isPublished ? 'Diterbitkan' : 'Draft' }}
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
                                    <div class="asset-value">{{ $templateName }}</div>
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
                            <form method="POST" action="{{ route('admin.trainer.certificates.publish', [
                                'trainer' => $trainer->id,
                                'context' => strtolower($programType) === 'course' ? 'course' : 'event',
                                'id' => $model->id,
                            ]) }}" class="mb-3">
                                @csrf
                                <button type="submit" class="btn-template">
                                    <i class="bi {{ $publishBtnIcon }}"></i>
                                    {{ $publishBtnText }}
                                </button>
                            </form>

                            <a href="{{ route('admin.trainer.certificates.edit', [
                                'trainer' => $trainer->id,
                                'context' => strtolower($programType) === 'course' ? 'course' : 'event',
                                'id' => $model->id,
                            ]) }}" class="btn-template btn-outline">
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
                            Ubah template, logo, atau tanda tangan jika diperlukan sebelum menerbitkan.
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection