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
    $templateName = $model?->certificate_template
        ?? $templateAsset?->name
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
            --cert-primary: #1e1b4b;
            --cert-primary-2: #1e1b4b;
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
            background: #1e1b4b;
            border-radius: 20px;
            padding: 34px 36px;
            color: #fff;
            min-height: 170px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(30, 27, 75, .14);
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

        #cert-preview-scaler {
            font-family: 'Poppins', sans-serif;
            transform-origin: top left !important;
            -webkit-transform-origin: top left !important;
        }

        #cert-preview-scaler .header {
            position: relative !important;
            z-index: 10 !important;
        }

        /* Live Preview Styles from CRM */
        #cert-preview-scaler .certificate-page {
            position: relative;
            top: 0; left: 0;
            width: 1020px;
            height: 642px;
            box-sizing: border-box;
            display: block;
            overflow: hidden;
            background: white;
            color: #1e293b;
            font-family: 'Poppins', sans-serif;
        }
        #cert-preview-scaler .template_1 { 
            border: none; 
            height: 642px; 
            width: 1020px;
            position: relative; 
            padding: 35px;
            box-sizing: border-box;
            background: #ffffff;
            overflow: hidden;
        }
        #cert-preview-scaler .template_1 .header { text-align: center; position: relative; z-index: 2; }
        #cert-preview-scaler .template_1 h1 { 
            font-family: 'Georgia', serif; 
            font-size: 36pt; 
            color: #1e1b4b; 
            margin: 10px 0 2px; 
            text-transform: uppercase; 
            letter-spacing: 4px;
            font-weight: 700;
        }
        #cert-preview-scaler .template_1 #preview-subtitle-t12 {
            font-family: 'Helvetica', sans-serif !important;
            font-size: 11pt !important;
            color: #7f1d1d !important;
            font-weight: bold !important;
            text-transform: uppercase !important;
            letter-spacing: 5px !important;
            margin-top: 4px !important;
            margin-bottom: 15px !important;
        }
        #cert-preview-scaler .template_1 #preview-line-t12 {
            display: none !important;
        }
        #cert-preview-scaler .template_1 .recipient-name { 
            font-size: 38pt !important; 
            font-weight: normal !important; 
            color: #7f1d1d !important;
            border-bottom: none !important; 
            display: inline-block !important; 
            padding: 4px 50px !important; 
            margin: 10px 0 !important;
            font-family: 'Great Vibes', cursive !important;
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
        }
        #cert-preview-scaler .template_1 .cert-content { text-align: center; position: relative; z-index: 2; }
        #cert-preview-scaler .template_1 .sig-box {
            display: inline-block !important;
            vertical-align: bottom !important;
            float: none !important;
            text-align: center !important;
            width: 200px !important;
            margin: 0 15px !important;
        }
        #cert-preview-scaler .template_1 .sig-line {
            width: 170px !important;
            border-bottom: 1.5px dashed #7f1d1d !important;
            margin: 8px auto !important;
        }

        /* Template 2: Modern Corporate */
        #cert-preview-scaler .template_2 { 
            padding: 0; 
            height: 642px; 
            width: 1020px;
            box-sizing: border-box; 
            overflow: hidden; 
            background: #f8fafc;
            position: relative;
        }
        #cert-preview-scaler .template_2 .content-wrap {
            padding: 50px 20px 20px 20px;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        #cert-preview-scaler .template_2 h1 { 
            font-family: 'Georgia', serif;
            font-size: 32pt; 
            font-weight: bold; 
            color: #0f172a; 
            margin: 0; 
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        #cert-preview-scaler .template_2 #preview-subtitle-t12 {
            font-family: 'Helvetica', sans-serif !important;
            font-size: 11pt !important;
            color: #475569 !important;
            font-weight: bold !important;
            text-transform: uppercase !important;
            letter-spacing: 5px !important;
            margin-top: 4px !important;
            margin-bottom: 12px !important;
        }
        #cert-preview-scaler .template_2 #preview-line-t12 {
            display: none !important;
        }
        #cert-preview-scaler .template_2 .recipient-name { 
            font-family: 'Great Vibes', 'Georgia', serif;
            font-size: 38pt; 
            font-weight: normal; 
            color: #0f172a; 
            margin: 8px auto;
            display: inline-block;
            font-style: italic;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 5px;
        }
        #cert-preview-scaler .template_2 .gold-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid #d4af37;
            background: #ffffff;
            z-index: 5;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        #cert-preview-scaler .template_2 .gold-badge-inner {
            position: absolute;
            top: 5px; left: 5px; right: 5px; bottom: 5px;
            border-radius: 50%;
            border: 1px solid #d4af37;
            background: #faf8f5;
        }
        #cert-preview-scaler .template_2 .cert-footer {
            padding: 0 !important;
            left: 50px !important;
            right: 50px !important;
            width: calc(100% - 100px) !important;
            text-align: center !important;
        }
        #cert-preview-scaler .template_2 .sig-box {
            display: inline-block !important;
            vertical-align: bottom !important;
            float: none !important;
            text-align: center !important;
            width: 200px !important;
            margin: 0 15px !important;
        }

        /* Template 3: Creative Professional */
        #cert-preview-scaler .template_3 { 
            padding: 0; 
            height: 642px; 
            width: 1020px;
            box-sizing: border-box; 
            background: #ffffff;
            border: 15px solid #ffffff;
            position: relative;
            overflow: hidden;
        }
        #cert-preview-scaler .template-decorations-3 img {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1;
        }
        #cert-preview-scaler .template_3 .header-bg {
            height: auto;
            background: transparent;
            padding: 15px 40px 5px 40px;
            color: #1e1b4b;
            position: relative;
            z-index: 2;
            text-align: center;
        }
        #cert-preview-scaler .template_3 .header-bg p {
            margin-top: 2px !important;
            margin-bottom: 6px !important;
            font-size: 9.5pt !important;
            letter-spacing: 4px !important;
        }
        #cert-preview-scaler .template_3 .header-bg::after {
            display: none;
        }
        #cert-preview-scaler .template_3 h1 { 
            font-size: 26pt; 
            font-weight: 900; 
            margin: 0; 
            text-transform: uppercase;
            letter-spacing: 3px;
            font-family: 'Georgia', serif;
            color: #1e1b4b;
            text-shadow: none;
        }
        #cert-preview-scaler .template_3 .cert-content {
            position: absolute !important;
            top: 175px !important;
            left: 40px !important;
            right: 40px !important;
            padding: 0 !important;
            margin: 0 !important;
            z-index: 2 !important;
            text-align: center !important;
        }
        #cert-preview-scaler .template_3 .recipient-name { 
            font-family: 'Great Vibes', 'Georgia', serif;
            font-size: 28pt; 
            font-weight: normal; 
            color: #4c1d95; 
            margin: 4px auto;
            display: inline-block;
            border-bottom: 2px solid #d97706;
            padding-bottom: 3px;
            -webkit-background-clip: initial;
            -webkit-text-fill-color: initial;
        }
        #cert-preview-scaler .template_3 #preview-certify-text {
            font-size: 12pt !important;
            margin-bottom: 2px !important;
        }
        #cert-preview-scaler .template_3 #preview-completed-text {
            font-size: 10.5pt !important;
            margin-top: 4px !important;
            line-height: 1.3 !important;
        }
        #cert-preview-scaler .template_3 #preview-course-name {
            font-size: 14pt !important;
            margin: 4px 0 !important;
            line-height: 1.2 !important;
        }
        #cert-preview-scaler .template_3 #preview-date-text {
            font-size: 9.5pt !important;
            margin-top: 3px !important;
        }
        #cert-preview-scaler .template_3 .award-line {
            display: none;
        }
        #cert-preview-scaler .template_3 .cert-footer {
            bottom: 95px !important;
            padding: 0 40px !important;
        }

        /* Shared / Layout Components */
        #cert-preview-scaler .logo-row { text-align: center; margin-bottom: 15px; width: 100%; }
        #cert-preview-scaler .logo-container { display: inline-block; vertical-align: middle; }
        #cert-preview-scaler .logo-item { height: 48px; width: auto; margin: 0 10px; vertical-align: middle; }
        
        #cert-preview-scaler .cert-footer { position: absolute; bottom: 50px; width: 100%; left: 0; padding: 0 70px; box-sizing: border-box; z-index: 3; white-space: nowrap !important; }
        
        #cert-preview-scaler .sig-box {
            display: inline-block !important;
            vertical-align: bottom !important;
            float: none !important;
            text-align: center !important;
            margin-left: 35px;
        }
        #cert-preview-scaler .template_3 .sig-box {
            display: inline-block !important;
            vertical-align: bottom !important;
            float: none !important;
            background: transparent !important;
            border: none !important;
            padding: 0 12px !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            backdrop-filter: none !important;
            margin: 0 15px !important;
        }
        #cert-preview-scaler .template_3 .sig-box img {
            height: 38px !important;
            margin: 0 auto !important;
        }
        #cert-preview-scaler .template_3 .sig-box div[style*="height: 50px"] {
            height: 38px !important;
        }
        #cert-preview-scaler .sig-line { width: 170px; border-bottom: 1.5px solid #0f172a; margin: 8px auto; }
        #cert-preview-scaler .template_3 .sig-line {
            width: 140px !important;
            border-bottom-color: #1e1b4b !important;
            margin: 4px auto !important;
        }
        #cert-preview-scaler .template_3 .sig-box p {
            font-size: 9.5pt !important;
            margin: 0 !important;
            color: #1e1b4b !important;
            font-weight: bold !important;
        }
        #cert-preview-scaler .template_3 .sig-box p + p {
            font-size: 8pt !important;
            margin-top: 1px !important;
            color: #64748b !important;
            font-style: italic !important;
            font-weight: normal !important;
        }
        
        #cert-preview-scaler .cert-id { position: absolute; bottom: 25px; right: 40px; font-size: 8.5pt; color: #94a3b8; font-weight: 600; z-index: 3; }
        #cert-preview-scaler .verification-tag { position: absolute; bottom: 25px; left: 40px; font-size: 7.5pt; color: #94a3b8; font-family: monospace; letter-spacing: 1.5px; font-weight: 600; z-index: 3; }
        #cert-preview-scaler .template_3 .verification-tag { left: 70px; bottom: 25px; }
        #cert-preview-scaler .template_3 .cert-id { right: 70px; bottom: 25px; }

        #cert-preview-scaler .template_4 {
            padding: 0;
            height: 642px;
            width: 1020px;
            box-sizing: border-box;
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }
        #cert-preview-scaler .template_4 .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 1020px;
            height: 642px;
            z-index: 1;
        }
        #cert-preview-scaler .template_4 .logo-banner-container {
            position: absolute;
            top: 0;
            left: 28%;
            width: 44%;
            background-color: #ffffff;
            border-radius: 0 0 15px 15px;
            padding: 8px 20px;
            text-align: center;
            z-index: 10;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        #cert-preview-scaler .template_4 .logo-poster-img {
            height: 45px;
            width: auto;
        }
        #cert-preview-scaler .template_4 .logo-item-top {
            height: 38px;
            width: auto;
            margin: 0 5px;
        }
        #cert-preview-scaler .template_4 .content-blue {
            position: absolute;
            top: 136px;
            left: 0;
            width: 1020px;
            text-align: center;
            z-index: 5;
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
        }
        #cert-preview-scaler .template_4 .recipient-underline {
            width: 604px;
            height: 1.5px;
            background-color: #ffffff;
            margin: 8px auto 15px auto;
        }
        #cert-preview-scaler .template_4 .cert-footer {
            position: absolute !important;
            bottom: 45px !important;
            left: 75px !important;
            right: 75px !important;
            text-align: center !important;
            width: auto !important;
            z-index: 6 !important;
            padding: 0 !important;
        }
        #cert-preview-scaler .template_4 .sig-box {
            display: inline-block !important;
            vertical-align: bottom !important;
            float: none !important;
            text-align: center !important;
            width: 200px !important;
            margin: 0 15px !important;
        }
        #cert-preview-scaler .template_4 .sig-position {
            font-weight: bold;
            margin: 0 0 4px 0;
            font-size: 8pt;
            color: #1a1a1a;
        }
        #cert-preview-scaler .template_4 .sig-image-wrap {
            height: 48px;
            margin: 4px auto;
        }
        #cert-preview-scaler .template_4 .sig-img {
            height: 48px;
            width: auto;
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }
        #cert-preview-scaler .template_4 .sig-line {
            width: 150px !important;
            border-bottom: 1.5px solid #1a1a1a;
            margin: 2px auto;
        }
        #cert-preview-scaler .template_4 .sig-name {
            font-weight: bold;
            margin: 6px 0 0 0;
            font-size: 8.5pt;
            color: #1a1a1a;
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
            background: #1e1b4b;
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
                        <h1 style="color:#fff">Detail Sertifikat</h1>
                        <p>
                            Informasi lengkap sertifikat yang telah diterbitkan.
                        </p>
                    </div>
                </section>

                <section class="preview-card">
                    <h5 class="section-title mb-3">Preview Sertifikat</h5>

                    <div id="certificate-preview-container" style="border: 1px solid var(--cert-border); border-radius: 12px; box-shadow: 0 10px 24px rgba(15, 23, 42, .08); background: #fff; overflow: hidden; width: 100%; position: relative;">
                        <div id="cert-preview-aspect" style="width: 100%; padding-top: 62.96%; position: relative; overflow: hidden;">
                            <div id="cert-preview-scaler" style="position: absolute; top: 0; left: 0; width: 1020px; height: 642px; transform-origin: top left;">
                                
                                <div class="certificate-page {{ $templateName }}" id="preview-cert-page">
                                    
                                    <!-- Template 1 Decorations -->
                                    <div class="template-decorations-1" style="{{ $templateName === 'template_1' ? '' : 'display: none;' }}">
                                        <!-- Top Left Gold Bar -->
                                        <div style="position: absolute; top: 46px; left: 50px; width: 480px; height: 4px; background: #eab308; z-index: 2;"></div>
                                        <!-- Bottom Right Gold Bar -->
                                        <div style="position: absolute; bottom: 46px; right: 50px; width: 480px; height: 4px; background: #eab308; z-index: 2;"></div>

                                        <!-- Top Right Maroon & Gold Waves -->
                                        <div style="position: absolute; top: 0; right: 0; width: 412px; height: 366px; z-index: 1; pointer-events: none;">
                                            <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                                <path d="M 30,0 C 50,40 70,60 100,80 L 100,0 Z" fill="#7f1d1d" />
                                                <path d="M 40,0 C 58,38 74,54 100,70 L 100,0 Z" fill="#eab308" />
                                                <path d="M 50,0 C 66,34 78,46 100,60 L 100,0 Z" fill="#991b1b" />
                                                <path d="M 65,0 C 78,26 86,34 100,45 L 100,0 Z" fill="#eab308" />
                                                <path d="M 75,0 C 85,20 90,25 100,35 L 100,0 Z" fill="#7f1d1d" />
                                            </svg>
                                        </div>

                                        <!-- Bottom Left Maroon & Gold Waves -->
                                        <div style="position: absolute; bottom: 0; left: 0; width: 412px; height: 366px; z-index: 1; pointer-events: none;">
                                            <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                                <path d="M 0,30 C 40,50 60,70 80,100 L 0,100 Z" fill="#7f1d1d" />
                                                <path d="M 0,40 C 38,58 54,74 70,100 L 0,100 Z" fill="#eab308" />
                                                <path d="M 0,50 C 34,66 46,78 60,100 L 0,100 Z" fill="#991b1b" />
                                                <path d="M 0,65 C 26,78 34,86 45,100 L 0,100 Z" fill="#eab308" />
                                                <path d="M 0,75 C 20,85 25,90 35,100 L 0,100 Z" fill="#7f1d1d" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Template 2 Decorations -->
                                    <div class="template-decorations-2" style="{{ $templateName === 'template_2' ? '' : 'display: none;' }}">
                                        <!-- SVG background decorations -->
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

                                    <!-- Template 3 Decorations -->
                                    <div class="template-decorations-3" style="{{ $templateName === 'template_3' ? '' : 'display: none;' }}">
                                        <img src="{{ asset('aset/bg-creative.png') }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
                                    </div>

                                    <!-- Template 4 Decorations: Blue Shield (CRM) -->
                                    <div class="template-decorations-4" style="{{ $templateName === 'template_4' ? '' : 'display: none;' }}">
                                        <img src="{{ asset('aset/bg-blue-shield.png') }}" class="bg-image" alt="">
                                    </div>

                                    <div id="preview-t4-top" style="{{ $templateName === 'template_4' ? '' : 'display: none;' }}" class="logo-banner-container">
                                        <img src="{{ asset('aset/logo poster.png') }}" class="logo-poster-img" alt="idSpora">
                                        @foreach($logos as $logo)
                                            @php $logoUrl = $assetUrl($logo->image_path); @endphp
                                            @if($logoUrl)
                                                <img class="logo-item-top" src="{{ $logoUrl }}" alt="Logo">
                                            @endif
                                        @endforeach
                                    </div>

                                    <div id="preview-t4-bottom" style="{{ $templateName === 'template_4' ? '' : 'display: none;' }}" class="content-blue">
                                        <h1 style="font-size: 22pt; font-weight: 900; margin: 0; letter-spacing: 3px;">SERTIFIKAT</h1>
                                        <p style="font-size: 8.5pt; font-weight: bold; letter-spacing: 4px; margin: 9px 0 4px 0;">DIBERIKAN KEPADA</p>
                                        <div style="font-size: 20pt; font-weight: bold; margin: 11px 0 4px 0;">{{ strtoupper($trainerName) }}</div>
                                        <div class="recipient-underline"></div>
                                        <p style="font-size: 8.5pt; margin: 8px 0 4px 0;">Atas Kontribusinya Sebagai</p>
                                        <p style="font-size: 13pt; font-weight: bold; margin: 4px 0 8px 0;">NARASUMBER</p>
                                        <p style="font-size: 8.5pt; margin: 8px 0 4px 0;">Dalam Program</p>
                                        <h2 style="font-size: 14pt; font-weight: bold; margin: 4px 0 4px 0;">"{{ $modelTitle }}"</h2>
                                        <p style="font-size: 8pt; margin: 9px 0 0 0;">
                                            Diterbitkan pada <strong>{{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d F Y') : now()->format('d F Y') }}</strong>
                                        </p>
                                    </div>

                                    <!-- Template 3 Header Area -->
                                    <div class="header-bg" id="preview-t3-header" style="{{ $templateName === 'template_3' ? '' : 'display: none;' }}">
                                        <div style="float: right;" class="preview-logo-container-t3">
                                            <img src="{{ asset('aset/logo-idspora.png') }}" class="logo-item" id="preview-main-logo-t3" style="height: 50px; width: auto;">
                                            @foreach($logos as $logo)
                                                @php $logoUrl = $assetUrl($logo->image_path); @endphp
                                                @if($logoUrl)
                                                    <img class="logo-item" src="{{ $logoUrl }}" alt="Logo">
                                                @endif
                                            @endforeach
                                        </div>
                                        <h1>Sertifikat Penghargaan</h1>
                                        <p style="color: #d97706; font-family: 'Helvetica', sans-serif; font-size: 11pt; font-weight: bold; text-transform: uppercase; letter-spacing: 5px; margin-top: 4px; margin-bottom: 12px;">NARASUMBER</p>
                                    </div>

                                    <!-- Template 1 & 2 Header Area -->
                                    <div class="header" id="preview-t12-header" style="{{ !in_array($templateName, ['template_3', 'template_4'], true) ? '' : 'display: none;' }}">
                                        <div class="logo-row">
                                            <div class="logo-container preview-logo-container-t12">
                                                <img src="{{ asset('aset/logo idspora_dark.png') }}" class="logo-item" id="preview-main-logo-t12">
                                                @foreach($logos as $logo)
                                                    @php $logoUrl = $assetUrl($logo->image_path); @endphp
                                                    @if($logoUrl)
                                                        <img class="logo-item" src="{{ $logoUrl }}" alt="Logo">
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <h1 style="margin-top: 15px; font-size: 32pt;" id="preview-h1-t12">Sertifikat Penghargaan</h1>
                                        <p style="color: #fbbf24; font-weight: bold; letter-spacing: 5px; font-size: 16pt; margin: 0; text-transform: uppercase;" id="preview-subtitle-t12">NARASUMBER</p>
                                        <div style="width: 200px; height: 2px; background: #fbbf24; margin: 15px auto;" id="preview-line-t12"></div>
                                    </div>

                                    <!-- Content Box -->
                                    <div class="cert-content" id="preview-content-box" style="{{ $templateName === 'template_4' ? 'display: none;' : ($templateName === 'template_2' ? 'padding: 20px 10px 0 10px; text-align: center; margin-top: 0;' : ($templateName === 'template_3' ? 'position: absolute; top: 175px; left: 40px; right: 40px; padding: 0; text-align: center; margin: 0; z-index: 2;' : 'margin-top: 25px; text-align: center;')) }}">
                                        <p style="font-size: 16pt; color: #64748b; font-style: italic; margin-bottom: 5px;" id="preview-certify-text">Diberikan kepada:</p>
                                        <div class="recipient-name" style="font-family: inherit;">{{ $templateName === 'template_1' ? $trainerName : strtoupper($trainerName) }}</div>
                                        <div id="preview-name-divider-t1" style="width: 70%; border-top: 1.5px dotted #7f1d1d; margin: 10px auto; display: {{ $templateName === 'template_1' ? 'block' : 'none' }};"></div>
                                        <p style="font-size: 14pt; line-height: 1.5; color: #1e293b; margin-top: 10px;" id="preview-completed-text">Atas kontribusinya sebagai narasumber dalam</p>
                                        <h2 style="font-size: 18pt; color: #1e1b4b; margin: 8px 0; font-family: 'Georgia', serif; line-height: 1.3;" id="preview-course-name">"{{ $modelTitle }}"</h2>
                                        <p style="font-size: 12pt; color: #64748b;" id="preview-date-text">diterbitkan pada {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y') : '-' }}</p>
                                    </div>

                                    <!-- Signature Footer -->
                                    <div class="cert-footer" style="{{ $templateName === 'template_3' ? 'bottom: 95px !important; padding: 0 40px !important;' : ($templateName === 'template_4' ? 'bottom: 45px !important; padding: 0 !important; left: 75px !important; right: 75px !important; width: auto !important;' : 'bottom: 50px !important; padding: 0 70px !important;') }}">
                                        <div style="float: right; text-align: center; width: 100%;" id="preview-signatures-container">
                                            @foreach($signatures->take(3) as $signature)
                                                @php $sigUrl = $assetUrl($signature->image_path); @endphp
                                                @if($templateName === 'template_4')
                                                    <div class="sig-box">
                                                        <p class="sig-position">{{ $signature->position ?? 'Authorized Position' }}</p>
                                                        <div class="sig-image-wrap">
                                                            @if($sigUrl)
                                                                <img src="{{ $sigUrl }}" class="sig-img" alt="">
                                                            @endif
                                                        </div>
                                                        <div class="sig-line"></div>
                                                        <p class="sig-name">{{ $signature->name ?? 'Authorized Signature' }}</p>
                                                    </div>
                                                @else
                                                    <div class="sig-box" style="{{ $templateName === 'template_3' ? 'display: inline-block !important; vertical-align: bottom !important; float: none !important; background: transparent; border: none; padding: 0 12px; border-radius: 0; box-shadow: none; backdrop-filter: none; margin: 0 15px !important;' : 'display: inline-block !important; vertical-align: bottom !important; float: none !important; text-align: center !important; margin: 0 30px !important; width: ' . ($templateName === 'template_2' ? '250px' : '230px') . ' !important;' }}">
                                                        @if($sigUrl)
                                                            <img src="{{ $sigUrl }}" style="height: 50px; width: auto; display: block; margin: 0 auto; object-fit: contain;">
                                                        @else
                                                            <div style="height: 50px;"></div>
                                                        @endif
                                                        <div class="sig-line" style="width: 170px; border-bottom: 1.5px {{ $templateName === 'template_1' ? 'dashed #7f1d1d' : ($templateName === 'template_3' ? 'solid #1e1b4b' : 'solid #0f172a') }}; margin: 8px auto;"></div>
                                                        <p style="font-weight: bold; margin: 0; font-size: 11pt; color: #1e1b4b;">{{ $signature->name ?? 'Authorized Signature' }}</p>
                                                        <p style="margin: 2px 0 0; font-size: 9pt; color: #64748b; font-style: italic;">{{ $signature->position ?? 'Authorized Position' }}</p>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="verification-tag" style="{{ $templateName === 'template_3' ? 'left: 70px; bottom: 25px;' : '' }}">VERIFIED BY IDSPORA.COM</div>
                                    <div class="cert-id" style="background: rgba(251, 191, 36, 0.1); padding: 5px 10px; border-radius: 4px; {{ $templateName === 'template_3' ? 'right: 70px; bottom: 25px;' : '' }}">Verified Certificate ID: {{ $certificate->certificate_number ?? '-' }}</div>
                                </div>

                            </div>
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
                                Kelola Template / Aset
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

@push('admin-trainer-scripts')
<script>
    function scalePreview() {
        const scaler = document.getElementById('cert-preview-scaler');
        if (!scaler) return;
        const container = document.getElementById('certificate-preview-container');
        if (!container) return;
        
        const containerW = container.offsetWidth;
        const certNaturalW = 1020;
        const scale = containerW / certNaturalW;
        
        scaler.style.webkitTransformOrigin = 'top left';
        scaler.style.transformOrigin = 'top left';
        scaler.style.webkitTransform = 'scale(' + scale + ')';
        scaler.style.transform = 'scale(' + scale + ')';
        container.style.height = (scale * 642) + 'px';
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        scalePreview();
        window.addEventListener('resize', scalePreview);
    });
</script>
@endpush