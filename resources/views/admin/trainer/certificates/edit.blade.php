@extends('layouts.admin-trainer')

@section('title', 'Kelola Template Sertifikat')

@php
    $modelTitle = $context === 'event'
        ? ($model->title ?? '-')
        : ($model->name ?? '-');

    $selectedTemplate = old(
        'certificate_template',
        $model->certificate_template
            ?? optional($assets->where('type', 'template')->first())->name
            ?? 'template_1'
    );

    $logos = $assets->where('type', 'logo')->values();
    $signatures = $assets->where('type', 'signature')->values();

    $isCrmSource = !empty($model->certificate_template)
        || !empty($model->certificate_logo)
        || !empty($model->certificate_signature);

    $certificate = \App\Models\TrainerCertificate::where('trainer_id', $trainer->id)
        ->where('certifiable_type', get_class($model))
        ->where('certifiable_id', $model->id)
        ->first();
    $issuedDate = $certificate?->issued_at ?? $certificate?->created_at ?? now();
    $formattedDate = \Carbon\Carbon::parse($issuedDate)->translatedFormat('d F Y');

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
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');

    :root {
        --cert-primary: #1e1b4b;
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
        background: #1e1b4b;
        border-radius: 22px;
        padding: 34px 38px;
        color: #fff;
        min-height: 155px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(30, 27, 75, .14);
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
        box-shadow: 0 8px 18px rgba(30, 27, 75,.25);
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
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    @media (max-width: 575px) {
        .template-grid {
            grid-template-columns: 1fr;
        }
    }

    .template-preview-icon {
        width: 76px;
        min-width: 76px;
        align-self: stretch;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 28px;
        flex-shrink: 0;
    }

    .template-card-body {
        flex: 1;
        min-width: 0;
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 3px;
    }

    .template-desc {
        text-align: left;
        font-size: 11px;
        color: var(--cert-muted);
        line-height: 1.45;
        margin: 0;
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
        border-radius: 14px;
        padding: 0;
        background: #fff;
        position: relative;
        transition: .2s;
        display: flex;
        flex-direction: row;
        align-items: stretch;
        overflow: hidden;
        min-height: 84px;
    }

    .template-option input:checked + .template-card {
        border-color: var(--cert-primary);
        box-shadow: 0 0 0 3px rgba(30, 27, 75,.12);
    }

    .template-check {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--cert-primary);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 2;
        font-size: 12px;
        box-shadow: 0 2px 6px rgba(30, 27, 75, .25);
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
        box-sizing: border-box;
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

    .template-name {
        text-align: left;
        font-weight: 800;
        font-size: 13px;
        color: #0f172a;
        padding: 0;
        margin: 0;
        line-height: 1.3;
    }

    .template-option input:checked + .template-card .template-name {
        color: var(--cert-primary);
    }

    .logo-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
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
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 22px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
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

    .guide-item:last-of-type {
        border-bottom: 0;
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
        border: 0;
        border-radius: 12px;
        background: #1e1b4b;
        color: #fff;
        font-weight: 900;
        cursor: pointer;
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

    /* Template 4: Blue Shield (CRM) */
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
        vertical-align: middle;
        display: inline-block;
    }
    #cert-preview-scaler .template_4 .logo-item-top {
        height: 38px;
        width: auto;
        vertical-align: middle;
        display: inline-block;
        margin: 0 5px;
    }
    #cert-preview-scaler .template_4 .content-blue {
        position: absolute;
        top: 136px;
        left: 0;
        width: 1020px;
        text-align: center;
        z-index: 5;
        padding: 0;
        box-sizing: border-box;
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
        font-family: Arial, Helvetica, sans-serif;
    }
    #cert-preview-scaler .template_4 .sig-image-wrap {
        height: 48px;
        margin: 4px auto;
        text-align: center;
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
        font-family: Arial, Helvetica, sans-serif;
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

    <form id="certificate-config-form" method="POST"
          action="{{ route('admin.trainer.certificates.update', [
              'trainer' => $trainer->id,
              'context' => $context,
              'id' => $model->id,
          ]) }}"
          enctype="multipart/form-data">
        @csrf

        <div id="removeAssetsContainer"></div>

        <section class="cert-hero">
            <div class="cert-hero-content">
                <div class="page-eyebrow">Sistem Rekognisi</div>
                <h1 style="color: #FFF;">Konfigurasi Sertifikat</h1>
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

        <div class="row g-4">
            <!-- Left Column: Form Configuration -->
            <div class="col-lg-6">
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
                                    'template_1' => ['Classic Royal', 'Elegan dengan aksen emas dan navy.', 'bi-award', 'linear-gradient(135deg, #1e1b4b 0%, #1e1b4b 100%)', '#fff'],
                                    'template_2' => ['Modern Minimal', 'Bersih, fokus pada tipografi modern.', 'bi-file-earmark-text', '#f1f5f9', '#1e293b'],
                                    'template_3' => ['Creative Dynamic', 'Enerjik dengan gradien dan pola.', 'bi-palette', 'linear-gradient(135deg, #6d28d9 0%, #db2777 100%)', '#fff'],
                                    'template_4' => ['Blue Shield', 'Biru navy elegan dengan aksen emas.', 'bi-shield-fill-check', 'linear-gradient(155deg, #001060 0%, #0033cc 60%, #0050ff 100%)', '#fff'],
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

                                            <div class="template-preview-icon" style="background: {{ $data[3] }}; color: {{ $data[4] }};">
                                                <i class="bi {{ $data[2] }}"></i>
                                            </div>

                                            <div class="template-card-body">
                                                <div class="template-name">{{ $data[0] }}</div>
                                                <div class="template-desc">{{ $data[1] }}</div>
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

            <!-- Right Column: Live Preview & Guides -->
            <div class="col-lg-6">
                <!-- Certificate Live Preview Card -->
                <div class="card-minimal p-4 mb-4 sticky-top shadow-sm" style="top: 20px; z-index: 10; background: #fff; border: 1px solid var(--cert-border); border-radius: 16px;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div style="width:24px;height:24px;border-radius:6px;background:var(--cert-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;"><i class="bi bi-eye-fill"></i></div>
                        <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--cert-navy);">Live Preview Sertifikat</h6>
                    </div>
                    
                    <!-- Scaling Container -->
                    <div id="certificate-preview-container" style="border: 1px solid var(--cert-border); border-radius: 12px; box-shadow: 0 10px 24px rgba(15, 23, 42, .08); background: #fff; overflow: hidden; width: 100%; position: relative;">
                        <div id="cert-preview-aspect" style="width: 100%; padding-top: 62.96%; position: relative; overflow: hidden;">
                            <div id="cert-preview-scaler" style="position: absolute; top: 0; left: 0; width: 1020px; height: 642px; transform-origin: top left;">
                                
                                <!-- The dynamic certificate preview page -->
                                <div class="certificate-page {{ $selectedTemplate }}" id="preview-cert-page">
                                    
                                    <!-- Template 1 Decorations -->
                                    <div class="template-decorations-1" style="{{ $selectedTemplate === 'template_1' ? '' : 'display: none;' }}">
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
                                    <div class="template-decorations-2" style="{{ $selectedTemplate === 'template_2' ? '' : 'display: none;' }}">
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
                                    <div class="template-decorations-3" style="{{ $selectedTemplate === 'template_3' ? '' : 'display: none;' }}">
                                        <img src="{{ asset('aset/bg-creative.png') }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
                                    </div>

                                    <!-- Template 4 Decorations: Blue Shield (CRM) -->
                                    <div class="template-decorations-4" style="{{ $selectedTemplate === 'template_4' ? '' : 'display: none;' }}">
                                        <img src="{{ asset('aset/bg-blue-shield.png') }}" class="bg-image" alt="">
                                    </div>

                                    <div id="preview-t4-top" style="{{ $selectedTemplate === 'template_4' ? '' : 'display: none;' }}" class="logo-banner-container preview-logo-container-t4">
                                        <img src="{{ asset('aset/logo poster.png') }}" class="logo-poster-img" alt="idSpora">
                                    </div>

                                    <div id="preview-t4-bottom" style="{{ $selectedTemplate === 'template_4' ? '' : 'display: none;' }}" class="content-blue">
                                        <h1 style="font-size: 22pt; font-weight: 900; margin: 0; letter-spacing: 3px; font-family: Arial, Helvetica, sans-serif;">SERTIFIKAT</h1>
                                        <p style="font-size: 8.5pt; font-weight: bold; letter-spacing: 4px; margin: 9px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">DIBERIKAN KEPADA</p>
                                        <div style="font-size: 20pt; font-weight: bold; margin: 11px 0 4px 0; font-family: Arial, Helvetica, sans-serif;" id="preview-t4-name">
                                            {{ strtoupper($trainer->name) }}
                                        </div>
                                        <div class="recipient-underline"></div>
                                        <p style="font-size: 8.5pt; margin: 8px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">Atas Kontribusinya Sebagai</p>
                                        <p style="font-size: 13pt; font-weight: bold; margin: 4px 0 8px 0; font-family: Arial, Helvetica, sans-serif;">NARASUMBER</p>
                                        <p style="font-size: 8.5pt; margin: 8px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">Dalam Program</p>
                                        <h2 style="font-size: 14pt; font-weight: bold; margin: 4px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">"{{ $modelTitle }}"</h2>
                                        <p style="font-size: 8pt; margin: 9px 0 0 0; font-family: Arial, Helvetica, sans-serif;">
                                            Diterbitkan pada <strong>{{ \Carbon\Carbon::parse($issuedDate)->translatedFormat('d F Y') }}</strong>
                                        </p>
                                    </div>

                                    <!-- Template 3 Header Area -->
                                    <div class="header-bg" id="preview-t3-header" style="{{ $selectedTemplate === 'template_3' ? '' : 'display: none;' }}">
                                        <div style="float: right;" class="preview-logo-container-t3">
                                            <img src="{{ asset('aset/logo-idspora.png') }}" class="logo-item" id="preview-main-logo-t3" style="height: 50px; width: auto;">
                                        </div>
                                        <h1>Sertifikat Penghargaan</h1>
                                        <p style="color: #d97706; font-family: 'Helvetica', sans-serif; font-size: 11pt; font-weight: bold; text-transform: uppercase; letter-spacing: 5px; margin-top: 4px; margin-bottom: 12px;">NARASUMBER</p>
                                    </div>

                                    <!-- Template 1 & 2 Header Area -->
                                    <div class="header" id="preview-t12-header" style="{{ !in_array($selectedTemplate, ['template_3', 'template_4'], true) ? '' : 'display: none;' }}">
                                        <div class="logo-row">
                                            <div class="logo-container preview-logo-container-t12">
                                                <img src="{{ asset('aset/logo idspora_dark.png') }}" class="logo-item" id="preview-main-logo-t12">
                                            </div>
                                        </div>
                                        <h1 style="margin-top: 15px; font-size: 32pt;" id="preview-h1-t12">Sertifikat Penghargaan</h1>
                                        <p style="color: #fbbf24; font-weight: bold; letter-spacing: 5px; font-size: 16pt; margin: 0; text-transform: uppercase;" id="preview-subtitle-t12">NARASUMBER</p>
                                        <div style="width: 200px; height: 2px; background: #fbbf24; margin: 15px auto;" id="preview-line-t12"></div>
                                    </div>

                                    <!-- Content Box -->
                                    <div class="cert-content" id="preview-content-box">
                                        <p style="font-size: 16pt; color: #64748b; font-style: italic; margin-bottom: 5px;" id="preview-certify-text">Diberikan kepada:</p>
                                        <div class="recipient-name" style="font-family: inherit;">{{ $selectedTemplate === 'template_1' ? $trainer->name : strtoupper($trainer->name) }}</div>
                                        <div id="preview-name-divider-t1" style="width: 70%; border-top: 1.5px dotted #7f1d1d; margin: 10px auto; display: {{ $selectedTemplate === 'template_1' ? 'block' : 'none' }};"></div>
                                        <p style="font-size: 14pt; line-height: 1.5; color: #1e293b; margin-top: 10px;" id="preview-completed-text">Atas kontribusinya sebagai narasumber dalam</p>
                                        <h2 style="font-size: 18pt; color: #1e1b4b; margin: 8px 0; font-family: 'Georgia', serif; line-height: 1.3;" id="preview-course-name">"{{ $modelTitle }}"</h2>
                                        <p style="font-size: 12pt; color: #64748b;" id="preview-date-text">diterbitkan pada {{ $formattedDate }}</p>
                                    </div>

                                    <!-- Signature Footer -->
                                    <div class="cert-footer">
                                        <div style="float: right;" id="preview-signatures-container">
                                            <!-- Dynamic signatures rendered by JS -->
                                        </div>
                                    </div>

                                    <div class="verification-tag">VERIFIED BY IDSPORA.COM</div>
                                    <div class="cert-id" style="background: rgba(251, 191, 36, 0.1); padding: 5px 10px; border-radius: 4px;">Verified Certificate ID: Verified Certificate ID</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Aside Panel Guides & Save controls -->
                <aside class="side-panel shadow-sm">
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

                    <div class="side-footer bg-light">
                        <button type="submit" class="btn-save-config btn btn-primary w-100 py-3">
                            <i class="bi bi-save me-2"></i>
                            Simpan Konfigurasi
                        </button>

                        <p class="save-note mt-2">
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
        
        // Trigger live preview rendering
        renderPreview();
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
            
            // Trigger live preview rendering
            renderPreview();
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

    // ─── Live Preview Engine ───
    function renderPreview() {
        const templateInput = document.querySelector('input[name="certificate_template"]:checked');
        const template = templateInput ? templateInput.value : 'template_1';
        const page = document.getElementById('preview-cert-page');
        if (!page) return;

        page.className = 'certificate-page ' + template;

        // Toggle template decorations
        const decor1 = document.querySelector('.template-decorations-1');
        const decor2 = document.querySelector('.template-decorations-2');
        const decor3 = document.querySelector('.template-decorations-3');
        const decor4 = document.querySelector('.template-decorations-4');
        if (decor1) decor1.style.display = (template === 'template_1') ? 'block' : 'none';
        if (decor2) decor2.style.display = (template === 'template_2') ? 'block' : 'none';
        if (decor3) decor3.style.display = (template === 'template_3') ? 'block' : 'none';
        if (decor4) decor4.style.display = (template === 'template_4') ? 'block' : 'none';

        // Toggle headers
        const headerT3 = document.getElementById('preview-t3-header');
        const headerT12 = document.getElementById('preview-t12-header');
        const headerT4top = document.getElementById('preview-t4-top');
        const headerT4bot = document.getElementById('preview-t4-bottom');
        if (headerT3) headerT3.style.display = (template === 'template_3') ? 'block' : 'none';
        if (headerT4top) headerT4top.style.display = (template === 'template_4') ? 'flex' : 'none';
        if (headerT4bot) headerT4bot.style.display = (template === 'template_4') ? 'block' : 'none';
        if (headerT12) {
            headerT12.style.display = (template !== 'template_3' && template !== 'template_4') ? 'block' : 'none';
            if (template === 'template_2') {
                headerT12.style.cssText = 'padding: 40px 10px 0 10px; text-align: center;';
            } else {
                headerT12.style.cssText = '';
            }
        }

        // Toggle content box alignment
        const contentBox = document.getElementById('preview-content-box');
        const sigContainer = document.getElementById('preview-signatures-container');
        if (contentBox) {
            if (template === 'template_4') {
                contentBox.style.display = 'none';
            } else if (template === 'template_2') {
                contentBox.style.display = '';
                contentBox.removeAttribute('style');
                contentBox.style.cssText = 'padding: 20px 10px 0 10px; text-align: center; margin-top: 0;';
            } else if (template === 'template_3') {
                contentBox.style.display = '';
                contentBox.removeAttribute('style');
                contentBox.style.cssText = 'position: absolute; top: 175px; left: 40px; right: 40px; padding: 0; text-align: center; margin: 0; z-index: 2;';
            } else {
                contentBox.style.display = '';
                contentBox.removeAttribute('style');
                contentBox.style.cssText = 'margin-top: 25px; text-align: center;';
            }
        }
        if (sigContainer) {
            sigContainer.style.float = 'none';
            sigContainer.style.textAlign = 'center';
            sigContainer.style.width = '100%';
            sigContainer.style.whiteSpace = 'nowrap';
        }

        const certFooter = page.querySelector('.cert-footer');
        if (certFooter) {
            if (template === 'template_3') {
                certFooter.style.cssText = 'bottom: 95px !important; padding: 0 40px !important; white-space: nowrap !important;';
            } else if (template === 'template_4') {
                certFooter.style.cssText = 'bottom: 45px !important; padding: 0 !important; left: 75px !important; right: 75px !important; width: auto !important; white-space: nowrap !important;';
            } else {
                certFooter.style.cssText = 'bottom: 50px !important; padding: 0 70px !important; white-space: nowrap !important;';
            }
        }

        const dividerT1 = document.getElementById('preview-name-divider-t1');
        if (dividerT1) {
            dividerT1.style.display = (template === 'template_1') ? 'block' : 'none';
        }

        const nameDiv = document.querySelector('#preview-content-box .recipient-name');
        const trainerName = @json($trainer->name);
        if (nameDiv) {
            if (template === 'template_1') {
                nameDiv.textContent = trainerName;
            } else {
                nameDiv.textContent = trainerName.toUpperCase();
            }
        }

        const t4NameDiv = document.getElementById('preview-t4-name');
        if (t4NameDiv) {
            t4NameDiv.textContent = trainerName.toUpperCase();
        }

        // Render Logos
        renderLogos(template);

        // Render Signatures
        renderSignatures();

        // Trigger scaling calculations
        scalePreview();
    }

    function renderLogos(template) {
        const containerT12 = document.querySelector('.preview-logo-container-t12');
        const containerT3 = document.querySelector('.preview-logo-container-t3');
        const containerT4 = document.querySelector('.preview-logo-container-t4');
        if (!containerT12 && !containerT3 && !containerT4) return;

        const assetPath = "{{ asset('aset') }}";
        const mainLogoUrl = (template === 'template_3' || template === 'template_4')
            ? `${assetPath}/logo-idspora.png`
            : `${assetPath}/logo idspora_dark.png`;

        const renderIn = (container, mainId) => {
            if (!container) return;
            container.innerHTML = '';

            const mainImg = document.createElement('img');
            if (template === 'template_4') {
                mainImg.src = `${assetPath}/logo poster.png`;
                mainImg.className = 'logo-poster-img';
            } else {
                mainImg.src = mainLogoUrl;
                mainImg.className = 'logo-item';
                mainImg.id = mainId;
            }
            container.appendChild(mainImg);

            document.querySelectorAll('.logo-card.has-preview img.upload-preview').forEach(img => {
                const partnerImg = document.createElement('img');
                partnerImg.src = img.src;
                partnerImg.className = (template === 'template_4') ? 'logo-item-top' : 'logo-item';
                container.appendChild(partnerImg);
            });
        };

        renderIn(containerT12, 'preview-main-logo-t12');
        renderIn(containerT3, 'preview-main-logo-t3');
        renderIn(containerT4, 'preview-main-logo-t4');
    }

    function renderSignatures() {
        const container = document.getElementById('preview-signatures-container');
        if (!container) return;
        container.innerHTML = '';

        const templateInput = document.querySelector('input[name="certificate_template"]:checked');
        const template = templateInput ? templateInput.value : 'template_1';

        for (let i = 0; i < 3; i++) {
            const card = document.querySelector(`.signature-card[data-index="${i}"]`);
            if (!card) continue;

            const uploadBox = card.querySelector('.upload-box');
            const hasPreview = uploadBox && uploadBox.classList.contains('has-preview');
            const img = uploadBox ? uploadBox.querySelector('img.upload-preview') : null;
            const nameInput = card.querySelector('[name^="signature_name"]');
            const posInput = card.querySelector('[name^="signature_position"]');

            const nameValue = nameInput ? nameInput.value.trim() : '';
            const posValue = posInput ? posInput.value.trim() : '';
            const imgSrc = hasPreview && img ? img.src : '';

            if (imgSrc || nameValue || posValue) {
                const sigBox = document.createElement('div');
                sigBox.className = 'sig-box';

                if (template === 'template_4') {
                    let imgHtml = '<div class="sig-image-wrap"></div>';
                    if (imgSrc) {
                        imgHtml = `<div class="sig-image-wrap"><img src="${imgSrc}" class="sig-img" alt=""></div>`;
                    }
                    sigBox.innerHTML = `
                        <p class="sig-position">${posValue || 'Authorized Position'}</p>
                        ${imgHtml}
                        <div class="sig-line"></div>
                        <p class="sig-name">${nameValue || 'Authorized Signature'}</p>
                    `;
                } else {
                    let imgHtml = '<div style="height: 50px;"></div>';
                    if (imgSrc) {
                        imgHtml = `<img src="${imgSrc}" style="height: 50px; width: auto; display: block; margin: 0 auto; object-fit: contain;" alt="">`;
                    }
                    sigBox.innerHTML = `
                        ${imgHtml}
                        <div class="sig-line"></div>
                        <p style="font-weight: bold; margin: 0; font-size: 11pt; color: #1e1b4b;">${nameValue || 'Authorized Signature'}</p>
                        <p style="margin: 2px 0 0; font-size: 9pt; color: #64748b; font-style: italic;">${posValue || 'Authorized Position'}</p>
                    `;
                }

                container.appendChild(sigBox);
            }
        }
    }

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

    // Bind listeners for live preview updates
    document.querySelectorAll('input[name="certificate_template"]').forEach(radio => {
        radio.addEventListener('change', renderPreview);
    });

    document.querySelectorAll('.signature-input').forEach(input => {
        input.addEventListener('input', renderPreview);
    });

    window.addEventListener('resize', scalePreview);

    // Form submit validation
    const form = document.getElementById('certificate-config-form');
    if (form) {
        form.addEventListener('submit', function (event) {
            const sigCards = document.querySelectorAll('.signature-card');
            let hasSignature = false;
            let incompleteSignature = false;
            let nameOrPositionWithoutImage = false;

            sigCards.forEach(card => {
                const uploadArea = card.querySelector('.signature-upload-area');
                const hasPreview = uploadArea && uploadArea.classList.contains('has-preview');
                const nameInput = card.querySelector('input[name^="signature_name"]');
                const posInput = card.querySelector('input[name^="signature_position"]');
                const nameVal = nameInput ? nameInput.value.trim() : '';
                const posVal = posInput ? posInput.value.trim() : '';

                if (hasPreview) {
                    hasSignature = true;
                    if (!nameVal || !posVal) {
                        incompleteSignature = true;
                    }
                } else {
                    if (nameVal || posVal) {
                        nameOrPositionWithoutImage = true;
                    }
                }
            });

            const showError = (msg) => {
                if (window.adminNotify) {
                    window.adminNotify('error', msg, 6000);
                } else {
                    const div = document.createElement('div');
                    div.style.cssText = 'position:fixed; top:20px; right:20px; background:#ef4444; color:#fff; padding:15px 20px; border-radius:8px; z-index:99999; box-shadow:0 4px 12px rgba(0,0,0,0.15); font-family:sans-serif; font-size:14px;';
                    div.innerHTML = '<strong>Terdapat kesalahan:</strong><br>' + msg;
                    document.body.appendChild(div);
                    setTimeout(() => div.remove(), 6000);
                }
            };

            if (!hasSignature) {
                event.preventDefault();
                showError('Konfigurasi tidak lengkap! Anda harus mengunggah minimal satu tanda tangan.');
                return false;
            }

            if (incompleteSignature) {
                event.preventDefault();
                showError('Konfigurasi tidak lengkap! Harap isi nama lengkap dan jabatan untuk semua tanda tangan yang diunggah.');
                return false;
            }

            if (nameOrPositionWithoutImage) {
                event.preventDefault();
                showError('Konfigurasi tidak lengkap! Harap unggah berkas tanda tangan untuk nama/jabatan yang diisi.');
                return false;
            }
        });
    }

    // Initial render
    renderPreview();
});
</script>
@endpush