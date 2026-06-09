@extends('layouts.crm')

@section('title', 'Konfigurasi Sertifikat Event')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--crm-primary); border-radius: 2px; }

    .template-card {
        cursor: pointer; transition: all 0.25s ease;
        border: 2.5px solid var(--crm-border-soft); border-radius: 16px;
        overflow: hidden; position: relative; background: #fff;
    }
    .template-card:hover { transform: translateY(-4px); box-shadow: var(--crm-shadow-md); border-color: var(--crm-border); }
    .template-card.active { border-color: var(--crm-primary); box-shadow: 0 0 0 4px rgba(124,58,237,0.1); }
    .template-card .check-icon {
        position: absolute; top: 12px; right: 12px;
        background: var(--crm-primary); color: #fff;
        width: 22px; height: 22px; border-radius: 50%;
        display: none; align-items: center; justify-content: center; z-index: 2; font-size: 0.75rem;
    }
    .template-card.active .check-icon { display: flex; }
    
    .template-preview {
        height: 110px; display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: rgba(255,255,255,0.4);
    }

    .form-field-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--crm-text-subtle); margin-bottom: 6px; display: block;
    }
    .form-field {
        width: 100%; border: 1px solid var(--crm-border); border-radius: 9px;
        padding: 0.55rem 0.9rem; font-size: 0.85rem; color: var(--crm-navy);
        background: var(--crm-border-soft); outline: none; transition: all 0.2s;
    }
    .form-field:focus { border-color: var(--crm-primary); background: #fff; box-shadow: 0 0 0 3px rgba(124,58,237,0.08); }

    .asset-item {
        position: relative; border-radius: 10px; border: 1px solid var(--crm-border);
        padding: 8px; background: #fff; width: fit-content;
    }
    .asset-delete {
        position: absolute; top: -8px; right: -8px;
        width: 22px; height: 22px; border-radius: 50%;
        background: #ef4444; color: #fff; border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem; cursor: pointer; transition: transform 0.2s;
    }
    .asset-delete:hover { transform: scale(1.1); }

    .sig-entry {
        background: var(--crm-border-soft); border: 1px solid var(--crm-border);
        border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem;
    }

    /* Live Preview Styles */
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
        margin-bottom: 25px !important;
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
    #cert-preview-scaler .template_1 .content { text-align: center; position: relative; z-index: 2; }
    #cert-preview-scaler .template_1 .sig-box {
        display: inline-block !important;
        float: none !important;
        text-align: center !important;
        width: 230px !important;
        margin: 0 30px !important;
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
    #cert-preview-scaler .template_2 .sub-title {
        font-family: 'Helvetica', sans-serif;
        font-size: 13pt;
        color: #475569;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 4px;
        margin-top: 5px;
        margin-bottom: 25px;
    }
    #cert-preview-scaler .template_2 .recipient-name { 
        font-family: 'Great Vibes', 'Georgia', serif;
        font-size: 38pt; 
        font-weight: normal; 
        color: #0f172a; 
        margin: 15px auto;
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
        float: none !important;
        text-align: center !important;
        width: 250px !important;
        margin: 0 30px !important;
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
        padding: 60px 70px 10px 70px;
        color: #1e1b4b;
        position: relative;
        z-index: 2;
        text-align: center;
    }
    #cert-preview-scaler .template_3 .header-bg::after {
        display: none;
    }
    #cert-preview-scaler .template_3 h1 { 
        font-size: 30pt; 
        font-weight: 900; 
        margin: 0; 
        text-transform: uppercase;
        letter-spacing: 3px;
        font-family: 'Georgia', serif;
        color: #1e1b4b;
        text-shadow: none;
    }
    #cert-preview-scaler .template_3 .main-content {
        padding: 10px 70px;
        position: relative;
        z-index: 2;
        text-align: center;
    }
    #cert-preview-scaler .template_3 .recipient-name { 
        font-family: 'Great Vibes', 'Georgia', serif;
        font-size: 38pt; 
        font-weight: normal; 
        color: #4c1d95; 
        margin: 10px auto;
        display: inline-block;
        border-bottom: 2px solid #d97706;
        padding-bottom: 5px;
        -webkit-background-clip: initial;
        -webkit-text-fill-color: initial;
    }
    #cert-preview-scaler .template_3 .award-line {
        display: none;
    }

    /* Shared / Layout Components */
    #cert-preview-scaler .logo-row { text-align: center; margin-bottom: 15px; width: 100%; }
    #cert-preview-scaler .logo-container { display: inline-block; vertical-align: middle; }
    #cert-preview-scaler .logo-item { height: 48px; width: auto; margin: 0 10px; vertical-align: middle; }
    
    #cert-preview-scaler .cert-footer { position: absolute; bottom: 70px; width: 100%; left: 0; padding: 0 70px; box-sizing: border-box; z-index: 3; }
    
    #cert-preview-scaler .sig-box { float: right; text-align: center; margin-left: 35px; }
    #cert-preview-scaler .template_3 .sig-box {
        display: inline-block !important;
        float: none !important;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(226, 232, 240, 0.8);
        padding: 10px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        backdrop-filter: blur(6px);
        margin: 0 20px !important;
    }
    #cert-preview-scaler .sig-line { width: 170px; border-bottom: 1.5px solid #0f172a; margin: 8px auto; }
    #cert-preview-scaler .template_3 .sig-line { border-bottom-color: #4f46e5; }
    
    #cert-preview-scaler .cert-id { position: absolute; bottom: 25px; right: 40px; font-size: 8.5pt; color: #94a3b8; font-weight: 600; z-index: 3; }
    #cert-preview-scaler .verification-tag { position: absolute; bottom: 25px; left: 40px; font-size: 7.5pt; color: #94a3b8; font-family: monospace; letter-spacing: 1.5px; font-weight: 600; z-index: 3; }
    #cert-preview-scaler .template_3 .verification-tag { left: 70px; bottom: 25px; }
    #cert-preview-scaler .template_3 .cert-id { right: 70px; bottom: 25px; }

    /* Template 4: Blue Shield */
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
        width: 264px !important;
        margin: 0 30px !important;
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
        width: 208px;
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
    #cert-preview-scaler .template_4 .curriculum-table {
        width: 85%;
        margin: 5px auto;
        border-collapse: collapse;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 9.5pt;
        z-index: 10;
        position: relative;
    }
    #cert-preview-scaler .template_4 .curriculum-table th {
        background-color: #7cc2f7;
        color: #000000;
        border: 1.5px solid #000000;
        padding: 6px 4px;
        font-weight: bold;
        text-align: center;
    }
    #cert-preview-scaler .template_4 .curriculum-table td {
        background-color: #faf9f6;
        color: #000000;
        border: 1.5px solid #000000;
        padding: 5px 10px;
        font-weight: bold;
    }
    #cert-preview-scaler .template_4 .curriculum-table td.center {
        text-align: center;
    }
</style>
@endsection

@section('content')
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="page-eyebrow">Template Settings</div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.8px;margin:0;">Konfigurasi Sertifikat Event</h1>
        <p style="font-size:0.8rem;color:var(--crm-text-subtle);margin:5px 0 0;">Event: <span class="fw-700 text-primary">{{ $event->title }}</span></p>
    </div>
    <a href="{{ route('admin.crm.certificates.index') }}" class="btn btn-sm px-3 fw-600 mt-3 mt-md-0"
       style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:8px;font-size:0.8rem;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

@if(session('success'))
<div class="alert border-0 alert-dismissible fade show mb-4" style="background:rgba(16,185,129,0.1);color:#059669;border-radius:12px;padding:0.85rem 1rem;font-size:0.85rem;" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert border-0 alert-dismissible fade show mb-4" style="background:rgba(239,68,68,0.08);color:#dc2626;border-radius:12px;padding:0.85rem 1rem;font-size:0.82rem;" role="alert">
    <div class="fw-800 mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('admin.crm.certificates.update', $event) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="row g-4">
        <!-- Left Column: Form Configuration -->
        <div class="col-lg-6">
            {{-- Step 1 --}}
            <div class="card-minimal p-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">1</div>
                    <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Pilih Template Desain</h6>
                </div>
                
                <div class="row g-3">
                    @php $tpls = [
                        ['id'=>'template_1','name'=>'Classic Royal','desc'=>'Elegan dengan aksen emas dan navy.','bg'=>'linear-gradient(135deg, #1e1b4b 0%, #312e81 100%)','icon'=>'bi-award'],
                        ['id'=>'template_2','name'=>'Modern Minimal','desc'=>'Bersih, fokus pada tipografi modern.','bg'=>'#f1f5f9','icon'=>'bi-file-earmark-text','color'=>'#1e293b'],
                        ['id'=>'template_3','name'=>'Creative Dynamic','desc'=>'Enerjik dengan gradien dan pola.','bg'=>'linear-gradient(135deg, #6d28d9 0%, #db2777 100%)','icon'=>'bi-palette'],
                        ['id'=>'template_4','name'=>'Blue Shield','desc'=>'Biru navy elegan dengan aksen emas.','bg'=>'linear-gradient(155deg, #001060 0%, #0033cc 60%, #0050ff 100%)','icon'=>'bi-shield-fill-check']
                    ]; @endphp
                    @foreach($tpls as $t)
                    <div class="col-md-4">
                        <div class="template-card {{ ($event->certificate_template ?? 'template_1') == $t['id'] ? 'active' : '' }}" onclick="selectTemplate('{{ $t['id'] }}', this)">
                            <div class="check-icon"><i class="bi bi-check"></i></div>
                            <div class="template-preview" style="background:{{ $t['bg'] }}; color:{{ $t['color'] ?? '#fff' }};">
                                <i class="bi {{ $t['icon'] }}"></i>
                            </div>
                            <div class="p-3">
                                <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">{{ $t['name'] }}</div>
                                <div style="font-size:0.7rem;color:var(--crm-text-subtle);line-height:1.4;margin-top:2px;">{{ $t['desc'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="certificate_template" id="selected_template" value="{{ $event->certificate_template ?? 'template_1' }}">
            </div>

            {{-- Step 2 --}}
            <div class="card-minimal p-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">2</div>
                    <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Kelola Aset Visual</h6>
                </div>

                <div class="row g-4">
                    {{-- Logos --}}
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-field-label mb-0">Logo Partner Tambahan</label>
                            <button type="button" id="addLogoBtn" onclick="addLogoField()" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Baris
                            </button>
                        </div>
                        <div id="logoUploadContainer" class="mb-3">
                            <input type="file" name="certificate_logo[]" class="form-field mb-2 logo-file-input" accept="image/*" onchange="onLogoFileChange(this, 'init_0')">
                        </div>
                        
                        <div id="existingLogos" class="d-flex flex-wrap gap-3">
                            @php $logos = is_array($event->certificate_logo) ? $event->certificate_logo : ($event->certificate_logo ? [$event->certificate_logo] : []); @endphp
                            @foreach($logos as $logo)
                                <div class="asset-item">
                                    <img src="{{ asset('uploads/' . $logo) }}" style="height:40px;object-fit:contain;">
                                    <div class="asset-delete" onclick="markDelete('logo', '{{ $logo }}', this, event)"><i class="bi bi-x"></i></div>
                                    <input type="hidden" name="delete_logos[]" value="" class="delete-logo-input">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr style="border-color:var(--crm-border-soft);margin:0.5rem 0;">

                    {{-- Signatures --}}
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-field-label mb-0">Tanda Tangan Digital</label>
                            <button type="button" onclick="addSignatureField()" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                <i class="bi bi-plus-lg me-1"></i>Tambah TTD
                            </button>
                        </div>

                        <div id="signaturesContainer">
                            @php
                                $sigs = is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : []);
                            @endphp
                            @foreach($sigs as $i => $sig)
                                @php
                                    $isObj = is_array($sig);
                                    $sigPath = $isObj ? ($sig['image'] ?? '') : $sig;
                                    $sigName = $isObj ? ($sig['name'] ?? '') : '';
                                    $sigPos  = $isObj ? ($sig['position'] ?? '') : '';
                                @endphp
                                <div class="sig-entry">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-field-label">Gambar TTD <span class="text-danger">*</span></label>
                                            @if($sigPath)
                                                <div class="d-flex align-items-center gap-3 mb-2">
                                                    <img src="{{ asset('uploads/' . $sigPath) }}" style="height:45px;background:#fff;padding:4px;border-radius:6px;border:1px solid var(--crm-border);object-fit:contain;">
                                                    <label style="font-size:0.75rem;font-weight:700;color:var(--crm-primary);cursor:pointer;">
                                                        <input type="checkbox" name="replace_sig_{{ $i }}" value="1" style="display:none;" class="sig-replace-checkbox" onchange="toggleSigReplace(this, {{ $i }})">
                                                        Ganti Gambar
                                                    </label>
                                                </div>
                                                <input type="hidden" name="existing_signature_image[{{ $i }}]" value="{{ $sigPath }}" class="existing-sig-path">
                                                <div id="sig_file_{{ $i }}" style="display:none;">
                                                    <input type="file" name="certificate_signature_file[{{ $i }}]" class="form-field sig-file-input" accept="image/*" onchange="onSigFileChange(this, {{ $i }})">
                                                </div>
                                            @else
                                                <input type="file" name="certificate_signature_file[{{ $i }}]" class="form-field sig-file-input" accept="image/*" onchange="onSigFileChange(this, {{ $i }})">
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-field-label">Nama Penandatangan</label>
                                            <input type="text" name="signature_name[{{ $i }}]" value="{{ $sigName }}" class="form-field sig-name-input" placeholder="cth: Dr. Ahmad Fauzi" onkeyup="renderPreview()">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-field-label mb-0">Jabatan</label>
                                                <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="removeSigEntry(this, '{{ $sigPath }}', event)">Hapus</button>
                                            </div>
                                            <input type="text" name="signature_position[{{ $i }}]" value="{{ $sigPos }}" class="form-field sig-pos-input" placeholder="cth: Direktur Utama" onkeyup="renderPreview()">
                                            <input type="hidden" name="delete_signatures[]" value="" class="delete-sig-input">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn fw-800 px-5" style="background:var(--crm-navy);color:#fff;border-radius:10px;padding-top:0.75rem;padding-bottom:0.75rem;">Simpan Perubahan</button>
            </div>
        </div>

        <!-- Right Column: Live Preview & Guides -->
        <div class="col-lg-6">
            <!-- Certificate Live Preview Card -->
            <div class="card-minimal p-4 mb-4 sticky-top shadow-sm" style="top: 20px; z-index: 10; background: #fff; border: 1px solid var(--crm-border-soft); border-radius: 16px;">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;"><i class="bi bi-eye-fill"></i></div>
                    <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Live Preview Sertifikat</h6>
                </div>
                
                <!-- Scaling Container -->
                <div id="certificate-preview-container" style="border: 1px solid var(--crm-border); border-radius: 12px; box-shadow: var(--crm-shadow-sm); background: #fff; overflow: hidden; width: 100%; position: relative;">
                    <div id="cert-preview-aspect" style="width: 100%; padding-top: 62.96%; position: relative; overflow: hidden;">
                        <div id="cert-preview-scaler" style="position: absolute; top: 0; left: 0; width: 1020px; height: 642px; transform-origin: top left;">
                            
                            <!-- The dynamic certificate preview page -->
                            <div class="certificate-page {{ ($event->certificate_template ?? 'template_1') }}" id="preview-cert-page">
                                
                                <!-- Template 1 Decorations -->
                                <div class="template-decorations-1">
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
                                <div class="template-decorations-2">
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
                                <div class="template-decorations-3" style="display: none;">
                                    <img src="{{ asset('aset/bg-creative.png') }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
                                </div>

                                <!-- Template 4 Decorations: Blue Shield -->
                                <div class="template-decorations-4" style="display: none;">
                                    <img src="{{ asset('aset/bg-blue-shield.png') }}" class="bg-image">
                                </div>

                                <!-- Template 4 Top Content Area (white text on blue) -->
                                <div id="preview-t4-top" style="display: none;" class="logo-banner-container preview-logo-container-t4">
                                    <img src="{{ asset('aset/logo poster.png') }}" class="logo-poster-img">
                                </div>

                                <!-- Template 4 Bottom Content Area (dark text on white) -->
                                <div id="preview-t4-bottom" style="display: none;" class="content-blue">
                                    <h1 style="font-size: 22pt; font-weight: 900; margin: 0; letter-spacing: 3px; font-family: Arial, Helvetica, sans-serif;">SERTIFIKAT</h1>
                                    <p style="font-size: 8.5pt; font-weight: bold; letter-spacing: 4px; margin: 9px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">DIBERIKAN KEPADA</p>
                                    
                                    <div style="font-size: 20pt; font-weight: bold; margin: 11px 0 4px 0; font-family: Arial, Helvetica, sans-serif;" id="preview-t4-name">
                                        NAMA PESERTA DEMO
                                    </div>
                                    <div class="recipient-underline"></div>

                                    <p style="font-size: 8.5pt; margin: 8px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">Atas Partisipasinya Sebagai</p>
                                    <p style="font-size: 13pt; font-weight: bold; margin: 4px 0 8px 0; font-family: Arial, Helvetica, sans-serif;">PESERTA</p>
                                    <p style="font-size: 8.5pt; margin: 8px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">Dalam Kegiatan Workshop</p>
                                    
                                    <h2 style="font-size: 14pt; font-weight: bold; margin: 4px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">
                                        "{{ $event->title }}"
                                    </h2>
                                    <p style="font-size: 8.5pt; margin: 0 0 8px 0; font-family: Arial, Helvetica, sans-serif;">
                                        Designing Learning Experiences to Develop Real Competencies and Professional Portfolios
                                    </p>
                                    
                                    <p style="font-size: 8pt; margin: 9px 0 0 0; font-family: Arial, Helvetica, sans-serif;">
                                        Yang diselenggarakan pada: 
                                        <strong>
                                            @if($event->event_until_date && $event->event_date->format('m Y') == $event->event_until_date->format('m Y'))
                                                {{ $event->event_date->format('d') }}-{{ $event->event_until_date->format('d F Y') }}
                                            @else
                                                {{ $event->event_date?->format('d F Y') }}
                                            @endif
                                        </strong> 
                                        di 
                                        <strong>{{ $event->location }}</strong>
                                    </p>
                                </div>

                                <!-- Template 3 Header Area -->
                                <div class="header-bg" id="preview-t3-header" style="display: none;">
                                    <div style="float: right;" class="preview-logo-container-t3">
                                        <img src="{{ asset('aset/logo-idspora.png') }}" class="logo-item" id="preview-main-logo-t3" style="height: 50px; width: auto;">
                                    </div>
                                    <h1>Certificate</h1>
                                    <p style="color: #d97706; font-family: 'Helvetica', sans-serif; font-size: 11pt; font-weight: bold; text-transform: uppercase; letter-spacing: 5px; margin-top: 4px; margin-bottom: 25px;">PROFESSIONAL RECOGNITION</p>
                                </div>

                                <!-- Template 1 & 2 Header Area -->
                                <div class="header" id="preview-t12-header">
                                    <div class="logo-row">
                                        <div class="logo-container preview-logo-container-t12">
                                            <img src="{{ asset('aset/logo idspora_dark.png') }}" class="logo-item" id="preview-main-logo-t12">
                                        </div>
                                    </div>
                                    <h1 style="margin-top: 15px; font-size: 42pt;" id="preview-h1-t12">Certificate</h1>
                                    <p style="color: #fbbf24; font-weight: bold; letter-spacing: 5px; font-size: 16pt; margin: 0; text-transform: uppercase;" id="preview-subtitle-t12">of Achievement</p>
                                    <div style="width: 200px; height: 2px; background: #fbbf24; margin: 15px auto;" id="preview-line-t12"></div>
                                </div>

                                <!-- Content Box -->
                                <div class="content" id="preview-content-box">
                                    <p style="font-size: 16pt; color: #64748b; font-style: italic; margin-bottom: 5px;" id="preview-certify-text">This is to certify that</p>
                                    <div class="recipient-name" style="font-family: inherit;">NAMA PESERTA DEMO</div>
                                    <div id="preview-name-divider-t1" style="width: 70%; border-top: 1.5px dotted #7f1d1d; margin: 15px auto; display: none;"></div>
                                    <p style="font-size: 14pt; line-height: 1.5; color: #1e293b; margin-top: 10px;" id="preview-completed-text">has successfully completed the program</p>
                                    <h2 style="font-size: 26pt; color: #1e1b4b; margin: 15px 0; font-family: 'Georgia', serif;" id="preview-course-name">"{{ $event->title }}"</h2>
                                    <p style="font-size: 12pt; color: #64748b;" id="preview-date-text">Issued on {{ $event->event_date ? $event->event_date->format('d F Y') : now()->format('d F Y') }} by idSpora Team</p>
                                </div>

                                <!-- Signature Footer -->
                                <div class="cert-footer">
                                    <div style="float: right;" id="preview-signatures-container">
                                        <!-- Dynamic signatures rendered by JS -->
                                    </div>
                                </div>

                                <div class="verification-tag">VERIFIED BY IDSPORA.COM</div>
                                <div class="cert-id" style="background: rgba(251, 191, 36, 0.1); padding: 5px 10px; border-radius: 4px;">Verified Certificate ID: CERT-EVE-DEMO-1234</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Asset Guides -->
            <div class="card-minimal p-4 mb-3" style="background:var(--crm-accent-light);border:1px solid rgba(124,58,237,0.12); border-radius:16px;">
                <h6 style="font-size:0.85rem;font-weight:800;color:var(--crm-primary);margin-bottom:1.25rem;">📝 Panduan Aset</h6>
                <div class="d-flex gap-3 mb-3">
                    <div style="width:30px;height:30px;border-radius:8px;background:#fff;color:var(--crm-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:var(--crm-shadow-sm);"><i class="bi bi-file-image"></i></div>
                    <div style="font-size:0.75rem;color:var(--crm-navy-soft);line-height:1.5;"><b>Format File:</b> JPG, PNG, WEBP, atau SVG diperbolehkan.</div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div style="width:30px;height:30px;border-radius:8px;background:#fff;color:#ef4444;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:var(--crm-shadow-sm);"><i class="bi bi-hdd"></i></div>
                    <div style="font-size:0.75rem;color:var(--crm-navy-soft);line-height:1.5;"><b>Ukuran:</b> Maksimal 2MB per file.</div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div style="width:30px;height:30px;border-radius:8px;background:#fff;color:#f59e0b;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:var(--crm-shadow-sm);"><i class="bi bi-layers"></i></div>
                    <div style="font-size:0.75rem;color:var(--crm-navy-soft);line-height:1.5;"><b>Limit:</b> Maksimal 3 Logo Partner dan 3 Tanda Tangan.</div>
                </div>
            </div>
            <div style="background:rgba(245,158,11,0.05);border:1px solid rgba(245,158,11,0.2);border-radius:12px;padding:1rem;font-size:0.75rem;color:#d97706;line-height:1.5; margin-bottom: 2rem;">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> Perubahan template akan langsung berdampak pada sertifikat yang sudah diunduh peserta.
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Global data stores for preview assets
    const uploadedFiles = {
        logos: {},
        signatures: {}
    };

    function selectTemplate(id, element) {
        document.querySelectorAll('.template-card').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('selected_template').value = id;
        renderPreview();
    }

    function markDelete(type, path, element, event) {
        if(event) { event.preventDefault(); event.stopPropagation(); }
        if(confirm('Hapus aset ini?')) {
            const wrapper = element.closest('.asset-item');
            const input = wrapper.querySelector('.delete-logo-input');
            if (input) input.value = path;
            wrapper.style.opacity = '0.3';
            wrapper.style.pointerEvents = 'none';
            wrapper.classList.add('marked-deleted');
            checkLogoCount();
            renderPreview();
        }
    }

    function previewNewAsset(input) {
        const file = input.files[0];
        if (file) {
            let preview = input.parentElement.querySelector('.new-asset-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.className = 'new-asset-preview mt-2';
                preview.style.cssText = 'height:40px; border-radius:6px; border:1px solid var(--crm-border); object-fit:contain; background:#fff; padding:3px;';
                input.parentElement.appendChild(preview);
            }
            const reader = new FileReader();
            reader.onload = function(e) { preview.src = e.target.result; }
            reader.readAsDataURL(file);
        }
    }

    function onLogoFileChange(input, id) {
        previewNewAsset(input);
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedFiles.logos[id] = e.target.result;
                renderPreview();
            };
            reader.readAsDataURL(file);
        } else {
            delete uploadedFiles.logos[id];
            renderPreview();
        }
    }

    function onRemoveLogoField(btn, id) {
        btn.parentElement.remove();
        delete uploadedFiles.logos[id];
        checkLogoCount();
        renderPreview();
    }

    let logoFileCounter = 1;
    function addLogoField() {
        const container = document.getElementById('logoUploadContainer');
        const currentInputs = container.querySelectorAll('input[type="file"]').length;
        const existingLogos = document.querySelectorAll('.asset-item:not(.marked-deleted)').length;
        if ((currentInputs + existingLogos) < 3) {
            const id = 'dyn_' + logoFileCounter++;
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-2';
            div.innerHTML = `
                <div class="w-100"><input type="file" name="certificate_logo[]" class="form-field logo-file-input" accept="image/*" onchange="onLogoFileChange(this, '${id}')"></div>
                <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="onRemoveLogoField(this, '${id}')"><i class="bi bi-trash"></i></button>
            `;
            container.appendChild(div);
            checkLogoCount();
        }
    }

    function checkLogoCount() {
        const currentInputs = document.querySelectorAll('#logoUploadContainer input[type="file"]').length;
        const existingLogos = document.querySelectorAll('.asset-item:not(.marked-deleted)').length;
        const btn = document.getElementById('addLogoBtn');
        if ((currentInputs + existingLogos) >= 3) btn.style.display = 'none';
        else btn.style.display = 'inline-flex';
    }

    let sigIndex = {{ count(is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : [])) }};
    let sigFileCounter = 100;

    function addSignatureField() {
        const container = document.getElementById('signaturesContainer');
        const existing = container.querySelectorAll('.sig-entry').length;
        if (existing >= 3) { alert('Maksimal 3 tanda tangan.'); return; }
        
        const idx = sigIndex++;
        const uniqueId = 'new_' + sigFileCounter++;
        const div = document.createElement('div');
        div.className = 'sig-entry';
        div.innerHTML = `
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-field-label">Gambar TTD <span class="text-danger">*</span></label>
                    <input type="file" name="certificate_signature_file[${idx}]" class="form-field sig-file-input" accept="image/*" onchange="onSigFileChange(this, '${uniqueId}')">
                </div>
                <div class="col-md-4">
                    <label class="form-field-label">Nama Penandatangan</label>
                    <input type="text" name="signature_name[${idx}]" class="form-field sig-name-input" placeholder="cth: Dr. Ahmad Fauzi" onkeyup="renderPreview()">
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-field-label mb-0">Jabatan</label>
                        <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="onRemoveSigRow(this, '${uniqueId}')">Hapus</button>
                    </div>
                    <input type="text" name="signature_position[${idx}]" class="form-field sig-pos-input" placeholder="cth: Direktur Utama" onkeyup="renderPreview()">
                </div>
            </div>`;
        container.appendChild(div);
        renderPreview();
    }

    function onRemoveSigRow(btn, uniqueId) {
        btn.closest('.sig-entry').remove();
        delete uploadedFiles.signatures[uniqueId];
        renderPreview();
    }

    function onSigFileChange(input, idx) {
        previewNewAsset(input);
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedFiles.signatures[idx] = e.target.result;
                renderPreview();
            };
            reader.readAsDataURL(file);
        } else {
            delete uploadedFiles.signatures[idx];
            renderPreview();
        }
    }

    function toggleSigReplace(checkbox, idx) {
        const fileDiv = document.getElementById('sig_file_' + idx);
        if (fileDiv) fileDiv.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            const fileInput = fileDiv.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';
            delete uploadedFiles.signatures[idx];
        }
        renderPreview();
    }

    function removeSigEntry(btn, path, event) {
        if(event) { event.preventDefault(); event.stopPropagation(); }
        if (!confirm('Hapus tanda tangan ini?')) return;
        const entry = btn.closest('.sig-entry');
        if (path) {
            const hidden = entry.querySelector('.delete-sig-input');
            if (hidden) hidden.value = path;
            entry.style.opacity = '0.3';
            entry.style.pointerEvents = 'none';
            renderPreview();
        } else {
            entry.remove();
            renderPreview();
        }
    }

    // Interactive Preview Engine
    function renderPreview() {
        const template = document.getElementById('selected_template').value;
        const page = document.getElementById('preview-cert-page');
        if (!page) return;

        // Change template style class
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
        if (headerT4top) headerT4top.style.display = (template === 'template_4') ? 'block' : 'none';
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
                contentBox.style.cssText = 'padding: 10px 70px; text-align: center; margin-top: 0;';
            } else {
                contentBox.style.display = '';
                contentBox.removeAttribute('style');
                contentBox.style.cssText = 'margin-top: 40px; text-align: center;';
            }
        }
        if (sigContainer) {
            sigContainer.style.float = 'none';
            sigContainer.style.textAlign = 'center';
            sigContainer.style.width = '100%';
        }
        const dividerT1 = document.getElementById('preview-name-divider-t1');
        if (dividerT1) {
            dividerT1.style.display = (template === 'template_1') ? 'block' : 'none';
        }
        const nameDiv = document.querySelector('#preview-content-box .recipient-name');
        if (nameDiv) {
            if (template === 'template_1') {
                nameDiv.textContent = 'Nama Peserta Demo';
            } else {
                nameDiv.textContent = 'NAMA PESERTA DEMO';
            }
        }

        const t4NameDiv = document.getElementById('preview-t4-name');
        if (t4NameDiv) {
            t4NameDiv.textContent = 'NAMA PESERTA DEMO';
        }

        // Update logo source depending on template (light vs dark)
        const assetPath = "{{ asset('aset') }}";
        const mainLogoUrl = (template === 'template_3' || template === 'template_4') ? `${assetPath}/logo-idspora.png` : `${assetPath}/logo idspora_dark.png`;
        
        // Render Logos
        renderLogosInContainer('.preview-logo-container-t12', mainLogoUrl, 'preview-main-logo-t12', template);
        renderLogosInContainer('.preview-logo-container-t3', mainLogoUrl, 'preview-main-logo-t3', template);
        renderLogosInContainer('.preview-logo-container-t4', mainLogoUrl, 'preview-main-logo-t4', template);

        // Render Signatures
        renderSignatures();
        
        // Trigger scaling calculations
        scalePreview();
    }

    function renderLogosInContainer(containerSelector, mainLogoUrl, mainLogoId, template) {
        const container = document.querySelector(containerSelector);
        if (!container) return;
        
        container.innerHTML = '';
        
        // Main logo
        const mainImg = document.createElement('img');
        if (template === 'template_4') {
            mainImg.src = "{{ asset('aset/logo poster.png') }}";
            mainImg.className = 'logo-poster-img';
        } else {
            mainImg.src = mainLogoUrl;
            mainImg.className = 'logo-item';
            mainImg.id = mainLogoId;
        }
        container.appendChild(mainImg);

        // Render existing logos
        const existingLogos = document.querySelectorAll('#existingLogos .asset-item');
        existingLogos.forEach(item => {
            const deleteInput = item.querySelector('.delete-logo-input');
            if (deleteInput && deleteInput.value === '') { // Not marked deleted
                const img = item.querySelector('img');
                if (img) {
                    const newImg = document.createElement('img');
                    newImg.src = img.src;
                    newImg.className = (template === 'template_4') ? 'logo-item-top' : 'logo-item';
                    container.appendChild(newImg);
                }
            }
        });

        // Render uploaded logos from store
        Object.keys(uploadedFiles.logos).forEach(key => {
            if (uploadedFiles.logos[key]) {
                const newImg = document.createElement('img');
                newImg.src = uploadedFiles.logos[key];
                newImg.className = (template === 'template_4') ? 'logo-item-top' : 'logo-item';
                container.appendChild(newImg);
            }
        });
    }

    function renderSignatures() {
        const container = document.getElementById('preview-signatures-container');
        if (!container) return;
        container.innerHTML = '';

        const template = document.getElementById('selected_template').value;

        const entries = document.querySelectorAll('#signaturesContainer .sig-entry');
        entries.forEach((entry, index) => {
            // Skip if marked deleted
            const deleteInput = entry.querySelector('.delete-sig-input');
            if (deleteInput && deleteInput.value !== '') {
                return;
            }

            const nameInput = entry.querySelector('.sig-name-input');
            if (!nameInput) return;
            const match = nameInput.name.match(/\[(\d+)\]/);
            const idx = match ? match[1] : 'dyn_' + index;

            const posInput = entry.querySelector('.sig-pos-input');
            const nameValue = nameInput.value.trim();
            const posValue = posInput ? posInput.value.trim() : '';

            // Get image source
            let imgSrc = '';
            const replaceCheckbox = entry.querySelector('.sig-replace-checkbox');
            const isReplaced = replaceCheckbox ? replaceCheckbox.checked : false;
            
            const existingInput = entry.querySelector('.existing-sig-path');
            const existingPath = existingInput ? existingInput.value : '';

            if (existingPath && !isReplaced) {
                imgSrc = "{{ asset('uploads') }}/" + existingPath;
            } else if (uploadedFiles.signatures[idx]) {
                imgSrc = uploadedFiles.signatures[idx];
            }

            const sigBox = document.createElement('div');
            sigBox.className = 'sig-box';

            if (template === 'template_4') {
                let imgHtml = '<div class="sig-image-wrap"></div>';
                if (imgSrc) {
                    imgHtml = `<div class="sig-image-wrap"><img src="${imgSrc}" class="sig-img"></div>`;
                }
                sigBox.innerHTML = `
                    <p class="sig-position">${posValue || 'Authorized Position'}</p>
                    ${imgHtml}
                    <div class="sig-line"></div>
                    <p class="sig-name">${nameValue || 'Authorized Signature'}</p>
                `;
            } else {
                let imgHtml = '<div style="height: 90px;"></div>';
                if (imgSrc) {
                    imgHtml = `<img src="${imgSrc}" style="height: 90px; width: auto; display: block; margin: 0 auto; object-fit: contain;">`;
                }
                sigBox.innerHTML = `
                    ${imgHtml}
                    <div class="sig-line"></div>
                    <p style="font-weight: bold; margin: 0; font-size: 11pt; color: #1e1b4b;">${nameValue || 'Authorized Signature'}</p>
                    <p style="margin: 2px 0 0; font-size: 9pt; color: #64748b; font-style: italic;">${posValue || 'Authorized Position'}</p>
                `;
            }

            container.appendChild(sigBox);
        });
    }

    function scalePreview() {
        const scaler = document.getElementById('cert-preview-scaler');
        if (!scaler) return;
        const container = document.getElementById('certificate-preview-container');
        if (!container) return;
        
        const containerW = container.offsetWidth;
        const certNaturalW = 1020;
        const scale = containerW / certNaturalW;
        
        scaler.style.transform = 'scale(' + scale + ')';
        container.style.height = (scale * 642) + 'px';
    }

    // Initialize Page
    document.addEventListener('DOMContentLoaded', () => {
        checkLogoCount();
        renderPreview();
        
        // Listen for new input fields events
        window.addEventListener('resize', scalePreview);
    });
</script>
@endsection