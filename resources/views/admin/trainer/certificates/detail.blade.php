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
    $programType = $model ? class_basename(get_class($model)) : '-';
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

        .template_1,
        .template_2,
        .template_3 {
            height: 100%;
            width: 100%;
            position: relative;
        }

        .tpl-inner {
            position: relative;
            height: 100%;
            width: 100%;
            padding: 36px 56px;
            text-align: center;
            color: #1e1b4b;
            box-sizing: border-box;
        }

        .tpl-logo-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }

        .tpl-logo {
            height: 32px;
            max-width: 120px;
            object-fit: contain;
        }

        .tpl-title {
            font-family: 'Georgia', serif;
            font-size: 30px;
            letter-spacing: 4px;
            margin: 6px 0 8px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .tpl-subtitle {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #fbbf24;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .tpl-name {
            font-size: 34px;
            font-weight: 700;
            margin: 8px 0;
            font-family: 'Times New Roman', serif;
        }

        .tpl-desc {
            font-size: 12px;
            line-height: 1.6;
            color: #1f2937;
        }

        .tpl-footer {
            margin-top: 24px;
            display: flex;
            justify-content: flex-end;
            gap: 24px;
        }

        .tpl-sig {
            text-align: center;
            min-width: 140px;
            font-size: 10px;
            color: #1e1b4b;
        }

        .tpl-sig img {
            max-height: 40px;
            max-width: 120px;
            object-fit: contain;
            display: block;
            margin: 0 auto 4px;
        }

        .tpl-sig-line {
            border-top: 1px solid #1e1b4b;
            margin: 4px 0;
        }

        .template_1 {
            border: 12px solid #1e1b4b;
            background: #fff;
        }

        .template_1 .tpl-inner {
            border: 2px double #fbbf24;
            margin: 10px;
            height: calc(100% - 20px);
            width: calc(100% - 20px);
            box-sizing: border-box;
        }

        .template_1 .tpl-name {
            border-bottom: 2px solid #fbbf24;
            display: inline-block;
            padding: 4px 24px;
        }

        .template_2 {
            background: #fff;
            border: 1px solid #e2e8f0;
        }

        .template_2::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 60px;
            background: #1e1b4b;
        }

        .template_2::after {
            content: '';
            position: absolute;
            inset: 0 auto 0 60px;
            width: 6px;
            background: #fbbf24;
        }

        .template_2 .tpl-inner {
            text-align: left;
            padding: 36px 56px 36px 110px;
        }

        .template_2 .tpl-name {
            border-left: 6px solid #fbbf24;
            padding-left: 12px;
        }

        .template_2 .tpl-logo-row {
            justify-content: flex-start;
        }

        .template_2 .tpl-footer {
            justify-content: flex-start;
        }

        .template_3 {
            background: #f8fafc;
        }

        .template_3 .tpl-header {
            background: #1e1b4b;
            color: #fff;
            padding: 18px 56px;
        }

        .template_3 .tpl-header .tpl-title {
            font-size: 24px;
            margin: 0;
        }

        .template_3 .tpl-bar {
            height: 6px;
            background: #fbbf24;
        }

        .template_3 .tpl-inner {
            text-align: left;
            padding: 24px 56px;
            height: calc(100% - 72px);
            box-sizing: border-box;
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
                        <div class="page-eyebrow">Recognition System</div>
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
                                    <div class="tpl-inner">
                                        <div class="tpl-logo-row">
                                            <img class="tpl-logo" src="{{ asset('aset/logo-idspora.png') }}" alt="Logo idSpora">
                                            @foreach($logos as $logo)
                                                @php $logoUrl = $assetUrl($logo->image_path); @endphp
                                                @if($logoUrl)
                                                    <img class="tpl-logo" src="{{ $logoUrl }}" alt="Logo">
                                                @endif
                                            @endforeach
                                        </div>

                                        <div class="tpl-title">Sertifikat Penghargaan</div>
                                        <div class="tpl-subtitle">Narasumber</div>

                                        <div class="tpl-name">{{ $trainerName }}</div>

                                        <div class="tpl-desc">
                                            Atas kontribusinya sebagai narasumber dalam
                                            <strong>{{ $modelTitle }}</strong>,
                                            diterbitkan pada
                                            {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y') : '-' }}.
                                        </div>

                                        <div class="tpl-footer">
                                            @foreach($signatures->take(2) as $signature)
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
                                </div>
                            @elseif($templateName === 'template_2')
                                <div class="template_2">
                                    <div class="tpl-inner">
                                        <div class="tpl-logo-row">
                                            <img class="tpl-logo" src="{{ asset('aset/logo-idspora.png') }}" alt="Logo idSpora">
                                            @foreach($logos as $logo)
                                                @php $logoUrl = $assetUrl($logo->image_path); @endphp
                                                @if($logoUrl)
                                                    <img class="tpl-logo" src="{{ $logoUrl }}" alt="Logo">
                                                @endif
                                            @endforeach
                                        </div>

                                        <div class="tpl-title">Sertifikat Penghargaan</div>
                                        <div class="tpl-subtitle">Narasumber</div>

                                        <div class="tpl-name">{{ $trainerName }}</div>

                                        <div class="tpl-desc">
                                            Atas kontribusinya sebagai narasumber dalam
                                            <strong>{{ $modelTitle }}</strong>,
                                            diterbitkan pada
                                            {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y') : '-' }}.
                                        </div>

                                        <div class="tpl-footer">
                                            @foreach($signatures->take(2) as $signature)
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
                                </div>
                            @else
                                <div class="template_3">
                                    <div class="tpl-header">
                                        <div class="tpl-title">Sertifikat Penghargaan</div>
                                    </div>
                                    <div class="tpl-bar"></div>
                                    <div class="tpl-inner">
                                        @if($logos->isNotEmpty())
                                            <div class="tpl-logo-row">
                                                @foreach($logos as $logo)
                                                    @php $logoUrl = $assetUrl($logo->image_path); @endphp
                                                    @if($logoUrl)
                                                        <img class="tpl-logo" src="{{ $logoUrl }}" alt="Logo">
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="tpl-subtitle">Narasumber</div>
                                        <div class="tpl-name">{{ $trainerName }}</div>

                                        <div class="tpl-desc">
                                            Atas kontribusinya sebagai narasumber dalam
                                            <strong>{{ $modelTitle }}</strong>,
                                            diterbitkan pada
                                            {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y') : '-' }}.
                                        </div>

                                        <div class="tpl-footer">
                                            @foreach($signatures->take(2) as $signature)
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
                                </div>
                            @endif
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