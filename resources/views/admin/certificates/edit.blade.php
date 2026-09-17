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

    /* Section Tabs for Lomba */
    .cert-section-nav {
        display: flex;
        gap: 10px;
        background: #f1f5f9;
        padding: 6px;
        border-radius: 14px;
        border: 1px solid var(--crm-border-soft);
        margin-bottom: 1.5rem;
    }
    .cert-section-nav-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 16px;
        border-radius: 10px;
        border: 1px solid transparent;
        background: transparent;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--crm-text-subtle);
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .cert-section-nav-btn:hover {
        color: var(--crm-navy);
        background: rgba(255,255,255,0.6);
    }
    .cert-section-nav-btn.active {
        background: #fff;
        color: var(--crm-navy);
        box-shadow: 0 3px 10px rgba(0,0,0,0.06);
    }
    .cert-section-nav-btn.active.is-lolos {
        border-color: rgba(16,185,129,0.3);
        color: #065f46;
    }
    .cert-section-nav-btn.active.is-tidak-lolos {
        border-color: rgba(100,116,139,0.3);
        color: #334155;
    }
    .cert-section-nav-btn.active.is-pemenang {
        border-color: rgba(245,158,11,0.4);
        color: #b45309;
        background: #fffdf5;
    }

    .winner-cat-tab-btn {
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 20px;
        padding: 5px 14px;
        font-weight: 700;
        font-size: 0.78rem;
        color: var(--crm-text-subtle);
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .winner-cat-tab-btn:hover {
        border-color: #f59e0b;
        color: #b45309;
        background: #fffdf5;
    }
    .winner-cat-tab-btn.active {
        border-color: #f59e0b;
        background: #fef3c7;
        color: #92400e;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.18);
    }

    .winner-card-item {
        background: #ffffff;
        border: 1.5px solid #fde68a;
        border-radius: 12px;
        padding: 12px 14px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 5px rgba(245, 158, 11, 0.05);
    }
    .winner-card-item:hover {
        border-color: #f59e0b;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.12);
    }
    .winner-search-result-item {
        padding: 9px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    .winner-search-result-item:last-child {
        border-bottom: none;
    }
    .winner-search-result-item:hover {
        background: #fffbeb;
    }

    .preview-switcher-btn {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 8px;
        border: 1px solid var(--crm-border);
        background: var(--crm-border-soft);
        color: var(--crm-text-subtle);
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .preview-switcher-btn.active {
        background: var(--crm-navy);
        color: #fff;
        border-color: var(--crm-navy);
    }

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
        font-family: 'Great Vibes', 'Georgia', serif !important; 
        font-weight: normal !important; 
        font-style: italic !important;
        color: #0f172a !important; 
        margin: 5px 0 10px !important;
        border: none !important;
        display: block !important;
    }
    #cert-preview-scaler .template_1 .logo-row {
        height: 48px;
        margin-bottom: 5px;
        position: relative;
        z-index: 2;
    }
    #cert-preview-scaler .template_1 .preview-logo-container-t12 {
        display: inline-flex;
        align-items: center;
        gap: 15px;
    }
    #cert-preview-scaler .template_1 .preview-logo-container-t12 .logo-item {
        height: 38px;
        max-width: 140px;
        object-fit: contain;
    }
    #cert-preview-scaler .template_1 .cert-footer {
        position: absolute;
        bottom: 50px;
        left: 60px;
        right: 60px;
        z-index: 2;
    }
    #cert-preview-scaler .template_1 .sig-box {
        display: inline-block;
        text-align: center;
        width: 180px;
        margin: 0 15px;
    }
    #cert-preview-scaler .template_1 .sig-box .sig-line {
        border-bottom: 1.5px solid #1e293b;
        margin: 5px 0 8px;
    }
    #cert-preview-scaler .template_1 .verification-tag {
        position: absolute;
        bottom: 15px;
        left: 50px;
        font-size: 8pt;
        color: #94a3b8;
        letter-spacing: 2px;
        font-family: 'Helvetica', sans-serif;
    }
    #cert-preview-scaler .template_1 .cert-id {
        position: absolute;
        bottom: 15px;
        right: 50px;
        font-size: 8pt;
        color: #94a3b8;
        font-family: 'Helvetica', sans-serif;
    }

    /* Template 2: Modern Minimal */
    #cert-preview-scaler .template_2 { 
        padding: 50px; 
        height: 642px; 
        width: 1020px;
        box-sizing: border-box;
        background: #faf8f5;
        position: relative;
        overflow: hidden;
    }
    #cert-preview-scaler .template_2 .logo-row {
        position: absolute;
        top: 30px;
        right: 40px;
        z-index: 10;
        margin: 0;
        height: auto;
    }
    #cert-preview-scaler .template_2 .preview-logo-container-t12 {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    #cert-preview-scaler .template_2 .preview-logo-container-t12 .logo-item {
        height: 40px;
        max-width: 120px;
        object-fit: contain;
    }
    #cert-preview-scaler .template_2 .header {
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

    /* Template 3: Creative Dynamic */
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
    #cert-preview-scaler .template_3 h1 { 
        font-size: 30pt; 
        font-weight: 900; 
        margin: 0; 
        text-transform: uppercase; 
        letter-spacing: 3px;
        font-family: Arial, sans-serif;
    }
    #cert-preview-scaler .template_3 .recipient-name { 
        font-size: 36pt; 
        font-weight: 900; 
        color: #1e1b4b; 
        margin: 15px 0; 
        text-transform: uppercase;
        font-family: Arial, sans-serif;
    }
    #cert-preview-scaler .template_3 .cert-footer {
        position: absolute;
        bottom: 40px;
        left: 70px;
        right: 70px;
        z-index: 2;
    }
    #cert-preview-scaler .template_3 .sig-box {
        display: inline-block;
        text-align: center;
        width: 200px;
        margin: 0 15px;
    }
    #cert-preview-scaler .template_3 .sig-box .sig-line {
        border-bottom: 2px solid #1e1b4b;
        margin: 5px 0 8px;
    }
    #cert-preview-scaler .template_3 .preview-logo-container-t3 {
        display: flex;
        align-items: center;
        gap: 15px;
    }

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
        top: 0; left: 0; width: 100%; height: 100%;
        z-index: 1; pointer-events: none;
    }
    #cert-preview-scaler .template_4 .logo-banner-container {
        position: absolute;
        top: 24px; left: 55px;
        z-index: 10;
        display: flex; align-items: center; gap: 15px;
    }
    #cert-preview-scaler .template_4 .logo-poster-img {
        height: 52px; width: auto; object-fit: contain;
    }
    #cert-preview-scaler .template_4 .logo-item-top {
        height: 42px; width: auto; object-fit: contain;
    }
    #cert-preview-scaler .template_4 .content-blue {
        position: absolute;
        top: 130px; left: 60px; right: 60px;
        z-index: 10; text-align: center;
    }
    #cert-preview-scaler .template_4 .recipient-underline {
        width: 480px; height: 1.5px; background-color: #1a1a1a;
        margin: 2px auto 0 auto;
    }
    #cert-preview-scaler .template_4 .cert-footer {
        position: absolute;
        bottom: 25px; left: 60px; right: 60px;
        z-index: 10;
        display: flex; justify-content: center; gap: 20px;
    }
    #cert-preview-scaler .template_4 .sig-box {
        display: inline-block; text-align: center; width: 220px;
    }
    #cert-preview-scaler .template_4 .sig-position {
        font-size: 8pt; color: #1a1a1a; margin: 0 0 2px 0;
        font-family: Arial, Helvetica, sans-serif;
    }
    #cert-preview-scaler .template_4 .sig-image-wrap {
        height: 48px; display: flex; align-items: center; justify-content: center;
    }
    #cert-preview-scaler .template_4 .sig-img {
        max-height: 44px; max-width: 140px; width: auto;
        display: block; margin: 0 auto; object-fit: contain;
    }
    #cert-preview-scaler .template_4 .sig-line {
        width: 180px; border-bottom: 1.5px solid #1a1a1a; margin: 2px auto;
    }
    #cert-preview-scaler .template_4 .sig-name {
        font-weight: bold; margin: 5px 0 0 0; font-size: 8.5pt; color: #1a1a1a;
        font-family: Arial, Helvetica, sans-serif;
    }
</style>
@endsection

@section('content')
@php
    $isLomba = strtolower(trim($event->jenis ?? '')) === 'lomba';

    $tpls = [
        ['id'=>'template_1','name'=>'Classic Royal','desc'=>'Elegan dengan aksen emas dan navy.','bg'=>'linear-gradient(135deg, #1e1b4b 0%, #312e81 100%)','icon'=>'bi-award'],
        ['id'=>'template_2','name'=>'Modern Minimal','desc'=>'Bersih, fokus pada tipografi modern.','bg'=>'#f1f5f9','icon'=>'bi-file-earmark-text','color'=>'#1e293b'],
        ['id'=>'template_3','name'=>'Creative Dynamic','desc'=>'Enerjik dengan gradien dan pola.','bg'=>'linear-gradient(135deg, #6d28d9 0%, #db2777 100%)','icon'=>'bi-palette'],
        ['id'=>'template_4','name'=>'Blue Shield','desc'=>'Biru navy elegan dengan aksen emas.','bg'=>'linear-gradient(155deg, #001060 0%, #0033cc 60%, #0050ff 100%)','icon'=>'bi-shield-fill-check']
    ];

    $logosLolos = is_array($event->certificate_logo) ? $event->certificate_logo : ($event->certificate_logo ? [$event->certificate_logo] : []);
    $sigsLolos = is_array($event->certificate_signature) ? $event->certificate_signature : ($event->certificate_signature ? [$event->certificate_signature] : []);

    $logosTidakLolos = is_array($event->certificate_logo_tidak_lolos) ? $event->certificate_logo_tidak_lolos : ($event->certificate_logo_tidak_lolos ? [$event->certificate_logo_tidak_lolos] : []);
    $sigsTidakLolos = is_array($event->certificate_signature_tidak_lolos) ? $event->certificate_signature_tidak_lolos : ($event->certificate_signature_tidak_lolos ? [$event->certificate_signature_tidak_lolos] : []);

    $logosPemenang = is_array($event->certificate_logo_pemenang) ? $event->certificate_logo_pemenang : ($event->certificate_logo_pemenang ? [$event->certificate_logo_pemenang] : []);
    $sigsPemenang = is_array($event->certificate_signature_pemenang) ? $event->certificate_signature_pemenang : ($event->certificate_signature_pemenang ? [$event->certificate_signature_pemenang] : []);
    $winnerCategories = $winnerCategories ?? ($isLomba ? $event->winnerCategories()->with(['winnerAssignments.registration.user', 'winnerAssignments.registration.team'])->get() : collect());

    $eventParticipants = $eventParticipants ?? ($eventRegistrations ?? collect())->map(function($r) {
        $name = $r->user->name ?? $r->full_name ?? ('Peserta #' . $r->id);
        $email = $r->user->email ?? '-';
        $team = $r->team->name ?? $r->team_name ?? null;
        return [
            'id' => (int)$r->id,
            'name' => (string)$name,
            'email' => (string)$email,
            'team' => $team ? (string)$team : null,
            'is_winner' => (bool)$r->is_winner,
            'winner_title' => (string)($r->winner_title ?? 'Juara 1')
        ];
    })->values();
@endphp

<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="page-eyebrow">Template Settings</div>
        <div class="d-flex align-items-center gap-2">
            <h1 style="font-size:1.5rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.8px;margin:0;">Konfigurasi Sertifikat Event</h1>
            @if($isLomba)
                <span class="badge" style="background:rgba(234,179,8,0.15);color:#b45309;font-weight:800;font-size:0.75rem;padding:6px 12px;border-radius:8px;">
                    🏆 Tipe Event: Lomba (3 Kategori Sertifikat)
                </span>
            @endif
        </div>
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

<form action="{{ route('admin.crm.certificates.update', $event) }}" method="POST" enctype="multipart/form-data" id="mainCertificateForm">
    @csrf @method('PUT')

    <div class="row g-4">
        <!-- Left Column: Form Configuration -->
        <div class="col-lg-6">

            @if($isLomba)
                <!-- Lomba 3-Section Switcher -->
                <div class="cert-section-nav">
                    <button type="button" class="cert-section-nav-btn is-lolos active" data-section="lolos" onclick="switchSection('lolos')">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Peserta Lolos</span>
                    </button>
                    <button type="button" class="cert-section-nav-btn is-tidak-lolos" data-section="tidak_lolos" onclick="switchSection('tidak_lolos')">
                        <i class="bi bi-award text-secondary"></i>
                        <span>Peserta Tidak Lolos</span>
                    </button>
                    <button type="button" class="cert-section-nav-btn is-pemenang" data-section="pemenang" onclick="switchSection('pemenang')">
                        <i class="bi bi-trophy-fill text-warning"></i>
                        <span>Peserta Pemenang</span>
                    </button>
                </div>
            @endif

            {{-- SECTION 1: LOLOS / DEFAULT --}}
            <div id="pane-lolos" class="cert-pane" style="display: block;">
                @if($isLomba)
                    <div class="p-3 mb-3 rounded-3" style="background:#ecfdf5; border: 1px solid #a7f3d0;">
                        <div class="fw-800 text-success small"><i class="bi bi-check-circle-fill me-1"></i> Desain Sertifikat: Peserta Lolos / Finalis</div>
                        <div class="text-muted" style="font-size:0.75rem;">Konfigurasi template, logo, dan tanda tangan khusus untuk peserta yang lolos seleksi / babak berikutnya.</div>
                    </div>
                @endif

                {{-- Step 1 (Lolos) --}}
                <div class="card-minimal p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">1</div>
                        <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Pilih Template Desain {{ $isLomba ? '(Peserta Lolos)' : '' }}</h6>
                    </div>
                    
                    <div class="row g-3 template-card-container">
                        @if(!empty($event->certificate_custom_template))
                        <div class="col-12">
                            <div class="template-card template-card-lolos active" id="card-custom-lolos" onclick="selectCustomTemplate('lolos')" style="border-color: #10b981; background: #f0fdf4;">
                                <div class="check-icon" style="background: #10b981; display: flex;"><i class="bi bi-check"></i></div>
                                <div class="d-flex align-items-center p-3 gap-3">
                                    <div style="width:46px; height:46px; border-radius:12px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0;">
                                        <i class="bi bi-magic"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="font-weight:800; font-size:0.88rem; color:#065f46;">Template Custom Builder (Aktif)</div>
                                            <span class="badge bg-success" style="font-size:0.65rem;">Sedang Digunakan</span>
                                        </div>
                                        <div style="font-size:0.72rem; color:#047857; margin-top:2px;">Template sertifikat visual hasil rancangan dari Visual Builder.</div>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.crm.certificates.template-builder', ['event' => $event, 'type' => 'lolos']) }}" class="btn btn-sm btn-success fw-bold px-3" style="font-size:0.75rem; border-radius:8px;">
                                            <i class="bi bi-pencil-square me-1"></i> Edit Builder
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @foreach($tpls as $t)
                        <div class="col-md-6">
                            <div class="template-card template-card-lolos {{ (empty($event->certificate_custom_template) && ($event->certificate_template ?? 'template_1') == $t['id']) ? 'active' : '' }}" onclick="selectTemplate('{{ $t['id'] }}', this, 'lolos')">
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
                    <input type="hidden" name="certificate_template" id="selected_template_lolos" value="{{ $event->certificate_template ?? 'template_1' }}">
                    
                    <div class="d-flex mt-4 justify-content-between align-items-center mb-2">
                        <label class="form-field-label mb-0">File Tambahan (Halaman Kedua)</label>
                    </div>
                    <div class="mb-3">
                        @if(!empty($event->file_tambahan))
                            <div class="mb-2 position-relative d-inline-block" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--crm-border);" id="existing-file-tambahan-container">
                                <img src="{{ asset('uploads/' . str_replace('storage/', '', $event->file_tambahan)) }}" style="width: 100%; height: 100%; object-fit: contain; background: #fff;" alt="File Tambahan">
                                <label class="position-absolute d-flex align-items-center justify-content-center" style="top:5px; right:5px; width:24px; height:24px; background:rgba(255,255,255,0.9); border-radius:6px; cursor:pointer; box-shadow:0 2px 4px rgba(0,0,0,0.1);" title="Hapus File Tambahan">
                                    <input type="checkbox" name="delete_file_tambahan" value="1" class="d-none" onchange="document.getElementById('existing-file-tambahan-container').style.opacity = this.checked ? '0.3' : '1';">
                                    <i class="bi bi-trash text-danger" style="font-size:0.75rem;"></i>
                                </label>
                            </div>
                        @endif
                        <div class="mb-2 d-none position-relative" id="new-file-tambahan-preview" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--crm-border);">
                            <img id="new-file-tambahan-img" src="" style="width: 100%; height: 100%; object-fit: contain; background: #fff;" alt="New File Tambahan">
                            <span class="position-absolute badge bg-primary" style="bottom: 5px; right: 5px;">Baru</span>
                        </div>
                        <input type="file" accept="image/*" name="file_tambahan" class="form-field mb-2" onchange="if(this.files && this.files[0]) { let reader = new FileReader(); reader.onload = function(e) { document.getElementById('new-file-tambahan-img').src = e.target.result; document.getElementById('new-file-tambahan-preview').classList.remove('d-none'); document.getElementById('new-file-tambahan-preview').classList.add('d-inline-block'); }; reader.readAsDataURL(this.files[0]); } else { document.getElementById('new-file-tambahan-preview').classList.add('d-none'); document.getElementById('new-file-tambahan-preview').classList.remove('d-inline-block'); }">
                        <small class="text-muted d-block mt-1">Opsional. File ini akan digabungkan di halaman kedua PDF sertifikat.</small>
                    </div>

                    <div class="mt-4 p-3 rounded-4 bg-light border d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-dark small"><i class="bi bi-magic me-1 text-warning"></i> Custom Template Builder</div>
                            <div class="text-muted" style="font-size:0.75rem;">Buat template custom untuk sertifikat lolos dengan drag &amp; drop visual.</div>
                        </div>
                        <a href="{{ route('admin.crm.certificates.template-builder', ['event' => $event, 'type' => 'lolos']) }}" class="btn btn-sm btn-primary fw-bold px-3 py-1.5" style="font-size:0.75rem; border-radius:8px;">
                            Buka Builder
                        </a>
                    </div>

                    @if(!empty($event->certificate_custom_template))
                    <div class="mt-3 p-3 rounded-4 border d-flex justify-content-between align-items-center" style="background:#ecfdf5; border-color:#a7f3d0;">
                        <div>
                            <div class="fw-bold text-success small"><i class="bi bi-patch-check-fill me-1"></i> Menggunakan Template Custom Lolos</div>
                            <div class="text-muted" style="font-size:0.75rem;">Template custom aktif untuk sertifikat peserta lolos.</div>
                        </div>
                        <button type="submit" form="reset-custom-form-lolos" class="btn btn-sm btn-outline-danger fw-bold px-3 py-1.5" style="font-size:0.75rem; border-radius:8px;">
                            Hapus Custom
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Step 2 (Lolos) --}}
                <div class="card-minimal p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">2</div>
                        <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Kelola Aset Visual {{ $isLomba ? '(Peserta Lolos)' : '' }}</h6>
                    </div>

                    <div class="row g-4">
                        {{-- Logos --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-field-label mb-0">Logo Partner Tambahan</label>
                                <button type="button" id="addLogoBtn" onclick="addLogoField('lolos')" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                    <i class="bi bi-plus-lg me-1"></i>Tambah Baris
                                </button>
                            </div>
                            <div id="logoUploadContainer" class="mb-3">
                                <input type="file" name="certificate_logo[]" class="form-field mb-2 logo-file-input" accept="image/*" onchange="onLogoFileChange(this, 'init_lolos_0', 'lolos')">
                            </div>
                            
                            <div id="existingLogos" class="d-flex flex-wrap gap-3">
                                @foreach($logosLolos as $logo)
                                    <div class="asset-item">
                                        <img src="{{ asset('uploads/' . $logo) }}" style="height:40px;object-fit:contain;">
                                        <div class="asset-delete" onclick="markDelete('logo', '{{ $logo }}', this, event, 'lolos')"><i class="bi bi-x"></i></div>
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
                                <button type="button" id="addSigBtn" onclick="addSignatureField('lolos')" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                    <i class="bi bi-plus-lg me-1"></i>Tambah TTD
                                </button>
                            </div>

                            <div id="signaturesContainer">
                                @foreach($sigsLolos as $i => $sig)
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
                                                            <input type="checkbox" name="replace_sig_{{ $i }}" value="1" style="display:none;" class="sig-replace-checkbox" onchange="toggleSigReplace(this, {{ $i }}, 'lolos')">
                                                            Ganti Gambar
                                                        </label>
                                                    </div>
                                                    <input type="hidden" name="existing_signature_image[{{ $i }}]" value="{{ $sigPath }}" class="existing-sig-path">
                                                    <div id="sig_file_lolos_{{ $i }}" style="display:none;">
                                                        <input type="file" name="certificate_signature_file[{{ $i }}]" class="form-field sig-file-input" accept="image/*" onchange="onSigFileChange(this, {{ $i }}, 'lolos')">
                                                    </div>
                                                @else
                                                    <input type="file" name="certificate_signature_file[{{ $i }}]" class="form-field sig-file-input" accept="image/*" onchange="onSigFileChange(this, {{ $i }}, 'lolos')">
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-field-label">Nama Penandatangan</label>
                                                <input type="text" name="signature_name[{{ $i }}]" value="{{ $sigName }}" class="form-field sig-name-input" placeholder="cth: Dr. Ahmad Fauzi" onkeyup="renderPreview()">
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-field-label mb-0">Jabatan</label>
                                                    <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="removeSigEntry(this, '{{ $sigPath }}', event, 'lolos')">Hapus</button>
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
            </div>

            @if($isLomba)
            {{-- SECTION 2: TIDAK LOLOS --}}
            <div id="pane-tidak-lolos" class="cert-pane" style="display: none;">
                <div class="p-3 mb-3 rounded-3" style="background:#f8fafc; border: 1px solid #cbd5e1;">
                    <div class="fw-800 text-secondary small"><i class="bi bi-info-circle-fill me-1"></i> Desain Sertifikat: Peserta Tidak Lolos / Partisipan</div>
                    <div class="text-muted" style="font-size:0.75rem;">Konfigurasi template, logo, dan tanda tangan khusus untuk peserta yang belum lolos ke babak berikutnya (apresiasi kepesertaan).</div>
                </div>

                {{-- Step 1 (Tidak Lolos) --}}
                <div class="card-minimal p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">1</div>
                        <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Pilih Template Desain (Peserta Tidak Lolos)</h6>
                    </div>
                    
                    <div class="row g-3 template-card-container">
                        @if(!empty($event->certificate_custom_template_tidak_lolos))
                        <div class="col-12">
                            <div class="template-card template-card-tidak-lolos active" id="card-custom-tidak-lolos" onclick="selectCustomTemplate('tidak_lolos')" style="border-color: #10b981; background: #f0fdf4;">
                                <div class="check-icon" style="background: #10b981; display: flex;"><i class="bi bi-check"></i></div>
                                <div class="d-flex align-items-center p-3 gap-3">
                                    <div style="width:46px; height:46px; border-radius:12px; background:#10b981; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0;">
                                        <i class="bi bi-magic"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2">
                                            <div style="font-weight:800; font-size:0.88rem; color:#065f46;">Template Custom Builder Tidak Lolos (Aktif)</div>
                                            <span class="badge bg-success" style="font-size:0.65rem;">Sedang Digunakan</span>
                                        </div>
                                        <div style="font-size:0.72rem; color:#047857; margin-top:2px;">Template sertifikat visual hasil rancangan dari Visual Builder.</div>
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.crm.certificates.template-builder', ['event' => $event, 'type' => 'tidak_lolos']) }}" class="btn btn-sm btn-success fw-bold px-3" style="font-size:0.75rem; border-radius:8px;">
                                            <i class="bi bi-pencil-square me-1"></i> Edit Builder
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @foreach($tpls as $t)
                        <div class="col-md-6">
                            <div class="template-card template-card-tidak-lolos {{ (empty($event->certificate_custom_template_tidak_lolos) && ($event->certificate_template_tidak_lolos ?? 'template_1') == $t['id']) ? 'active' : '' }}" onclick="selectTemplate('{{ $t['id'] }}', this, 'tidak_lolos')">
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
                    <input type="hidden" name="certificate_template_tidak_lolos" id="selected_template_tidak_lolos" value="{{ $event->certificate_template_tidak_lolos ?? 'template_1' }}">
                    
                    <div class="d-flex mt-4 justify-content-between align-items-center mb-2">
                        <label class="form-field-label mb-0">File Tambahan (Halaman Kedua)</label>
                    </div>
                    <div class="mb-3">
                        @if(!empty($event->file_tambahan_tidak_lolos))
                            <div class="mb-2 position-relative d-inline-block" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--crm-border);" id="existing-file-tambahan-tidak-lolos-container">
                                <img src="{{ asset('uploads/' . str_replace('storage/', '', $event->file_tambahan_tidak_lolos)) }}" style="width: 100%; height: 100%; object-fit: contain; background: #fff;" alt="File Tambahan Tidak Lolos">
                                <label class="position-absolute d-flex align-items-center justify-content-center" style="top:5px; right:5px; width:24px; height:24px; background:rgba(255,255,255,0.9); border-radius:6px; cursor:pointer; box-shadow:0 2px 4px rgba(0,0,0,0.1);" title="Hapus File Tambahan">
                                    <input type="checkbox" name="delete_file_tambahan_tidak_lolos" value="1" class="d-none" onchange="document.getElementById('existing-file-tambahan-tidak-lolos-container').style.opacity = this.checked ? '0.3' : '1';">
                                    <i class="bi bi-trash text-danger" style="font-size:0.75rem;"></i>
                                </label>
                            </div>
                        @endif
                        <div class="mb-2 d-none position-relative" id="new-file-tambahan-tidak-lolos-preview" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--crm-border);">
                            <img id="new-file-tambahan-tidak-lolos-img" src="" style="width: 100%; height: 100%; object-fit: contain; background: #fff;" alt="New File Tambahan Tidak Lolos">
                            <span class="position-absolute badge bg-primary" style="bottom: 5px; right: 5px;">Baru</span>
                        </div>
                        <input type="file" accept="image/*" name="file_tambahan_tidak_lolos" class="form-field mb-2" onchange="if(this.files && this.files[0]) { let reader = new FileReader(); reader.onload = function(e) { document.getElementById('new-file-tambahan-tidak-lolos-img').src = e.target.result; document.getElementById('new-file-tambahan-tidak-lolos-preview').classList.remove('d-none'); document.getElementById('new-file-tambahan-tidak-lolos-preview').classList.add('d-inline-block'); }; reader.readAsDataURL(this.files[0]); } else { document.getElementById('new-file-tambahan-tidak-lolos-preview').classList.add('d-none'); document.getElementById('new-file-tambahan-tidak-lolos-preview').classList.remove('d-inline-block'); }">
                        <small class="text-muted d-block mt-1">Opsional. File ini akan digabungkan di halaman kedua PDF sertifikat tidak lolos.</small>
                    </div>

                    <div class="mt-4 p-3 rounded-4 bg-light border d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-dark small"><i class="bi bi-magic me-1 text-warning"></i> Custom Template Builder</div>
                            <div class="text-muted" style="font-size:0.75rem;">Buat template custom untuk sertifikat tidak lolos dengan drag &amp; drop visual.</div>
                        </div>
                        <a href="{{ route('admin.crm.certificates.template-builder', ['event' => $event, 'type' => 'tidak_lolos']) }}" class="btn btn-sm btn-secondary fw-bold px-3 py-1.5" style="font-size:0.75rem; border-radius:8px;">
                            Buka Builder
                        </a>
                    </div>

                    @if(!empty($event->certificate_custom_template_tidak_lolos))
                    <div class="mt-3 p-3 rounded-4 border d-flex justify-content-between align-items-center" style="background:#f8fafc; border-color:#cbd5e1;">
                        <div>
                            <div class="fw-bold text-secondary small"><i class="bi bi-patch-check-fill me-1"></i> Menggunakan Template Custom Tidak Lolos</div>
                            <div class="text-muted" style="font-size:0.75rem;">Template custom aktif untuk sertifikat peserta tidak lolos.</div>
                        </div>
                        <button type="submit" form="reset-custom-form-tidak-lolos" class="btn btn-sm btn-outline-danger fw-bold px-3 py-1.5" style="font-size:0.75rem; border-radius:8px;">
                            Hapus Custom
                        </button>
                    </div>
                    @endif
                </div>

                {{-- Step 2 (Tidak Lolos) --}}
                <div class="card-minimal p-4 mb-4">
                    <div class="d-flex align-items-center gap-2 mb-4">
                        <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">2</div>
                        <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Kelola Aset Visual (Peserta Tidak Lolos)</h6>
                    </div>

                    <div class="row g-4">
                        {{-- Logos --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-field-label mb-0">Logo Partner Tambahan</label>
                                <button type="button" id="addLogoBtn_tidak_lolos" onclick="addLogoField('tidak_lolos')" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                    <i class="bi bi-plus-lg me-1"></i>Tambah Baris
                                </button>
                            </div>
                            <div id="logoUploadContainer_tidak_lolos" class="mb-3">
                                <input type="file" name="certificate_logo_tidak_lolos[]" class="form-field mb-2 logo-file-input" accept="image/*" onchange="onLogoFileChange(this, 'init_tidak_lolos_0', 'tidak_lolos')">
                            </div>
                            
                            <div id="existingLogos_tidak_lolos" class="d-flex flex-wrap gap-3">
                                @foreach($logosTidakLolos as $logo)
                                    <div class="asset-item">
                                        <img src="{{ asset('uploads/' . $logo) }}" style="height:40px;object-fit:contain;">
                                        <div class="asset-delete" onclick="markDelete('logo', '{{ $logo }}', this, event, 'tidak_lolos')"><i class="bi bi-x"></i></div>
                                        <input type="hidden" name="delete_logos_tidak_lolos[]" value="" class="delete-logo-input-tidak-lolos">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <hr style="border-color:var(--crm-border-soft);margin:0.5rem 0;">

                        {{-- Signatures --}}
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-field-label mb-0">Tanda Tangan Digital</label>
                                <button type="button" id="addSigBtn_tidak_lolos" onclick="addSignatureField('tidak_lolos')" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                    <i class="bi bi-plus-lg me-1"></i>Tambah TTD
                                </button>
                            </div>

                            <div id="signaturesContainer_tidak_lolos">
                                @foreach($sigsTidakLolos as $i => $sig)
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
                                                            <input type="checkbox" name="replace_sig_tidak_lolos_{{ $i }}" value="1" style="display:none;" class="sig-replace-checkbox" onchange="toggleSigReplace(this, {{ $i }}, 'tidak_lolos')">
                                                            Ganti Gambar
                                                        </label>
                                                    </div>
                                                    <input type="hidden" name="existing_signature_image_tidak_lolos[{{ $i }}]" value="{{ $sigPath }}" class="existing-sig-path">
                                                    <div id="sig_file_tidak_lolos_{{ $i }}" style="display:none;">
                                                        <input type="file" name="certificate_signature_file_tidak_lolos[{{ $i }}]" class="form-field sig-file-input" accept="image/*" onchange="onSigFileChange(this, {{ $i }}, 'tidak_lolos')">
                                                    </div>
                                                @else
                                                    <input type="file" name="certificate_signature_file_tidak_lolos[{{ $i }}]" class="form-field sig-file-input" accept="image/*" onchange="onSigFileChange(this, {{ $i }}, 'tidak_lolos')">
                                                @endif
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-field-label">Nama Penandatangan</label>
                                                <input type="text" name="signature_name_tidak_lolos[{{ $i }}]" value="{{ $sigName }}" class="form-field sig-name-input" placeholder="cth: Dr. Ahmad Fauzi" onkeyup="renderPreview()">
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <label class="form-field-label mb-0">Jabatan</label>
                                                    <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="removeSigEntry(this, '{{ $sigPath }}', event, 'tidak_lolos')">Hapus</button>
                                                </div>
                                                <input type="text" name="signature_position_tidak_lolos[{{ $i }}]" value="{{ $sigPos }}" class="form-field sig-pos-input" placeholder="cth: Direktur Utama" onkeyup="renderPreview()">
                                                <input type="hidden" name="delete_signatures_tidak_lolos[]" value="" class="delete-sig-input-tidak-lolos">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: PEMENANG (WINNERS - MULTI CATEGORY) --}}
            <div id="pane-pemenang" class="cert-pane" style="display: none;">
                <div class="p-3 mb-3 rounded-3" style="background:#fffbeb; border: 1px solid #fde68a;">
                    <div class="fw-800 text-warning small"><i class="bi bi-trophy-fill me-1"></i> Desain Sertifikat: Peserta Pemenang (Kategori Lomba)</div>
                    <div class="text-muted" style="font-size:0.75rem;">Event lomba ini mendukung beragam kategori pemenang (misal: Web Design, UI/UX, Juara Favorit). Setiap kategori memiliki daftar pemenang, template sertifikat, builder visual, dan aset tanda tangan tersendiri.</div>
                </div>

                {{-- Category Navigation Bar --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mb-4 pb-2 border-bottom" id="winner-cat-tabs-bar">
                    @foreach($winnerCategories as $wCat)
                        <button type="button" class="winner-cat-tab-btn {{ $loop->first ? 'active' : '' }}" id="cat-tab-btn-{{ $wCat->id }}" data-cat-id="{{ $wCat->id }}" onclick="switchWinnerCategory({{ $wCat->id }})">
                            🏆 <span id="cat-tab-title-{{ $wCat->id }}">{{ $wCat->name }}</span>
                            <span class="badge bg-warning text-dark ms-1" id="cat-tab-count-{{ $wCat->id }}">{{ $wCat->winnerAssignments->count() }}</span>
                        </button>
                    @endforeach
                    <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-3 shadow-sm" onclick="openAddCategoryModal()" style="font-size:0.78rem;">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Kategori
                    </button>
                </div>

                {{-- Category Subpanes --}}
                @foreach($winnerCategories as $wCat)
                <div class="winner-category-subpane" id="winner-cat-subpane-{{ $wCat->id }}" style="{{ $loop->first ? 'display:block;' : 'display:none;' }}">
                    <input type="hidden" name="winner_category_ids[]" value="{{ $wCat->id }}">

                    {{-- Category Header Action Bar --}}
                    <div class="p-3 mb-4 rounded-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3" style="background:#fffdf5; border:1px solid #fed7aa;">
                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                            <label class="form-field-label mb-0 fw-800 text-dark" style="white-space:nowrap; font-size:0.75rem;">Nama Kategori:</label>
                            <input type="text" name="category_name_{{ $wCat->id }}" id="category_name_input_{{ $wCat->id }}" value="{{ $wCat->name }}" class="form-control form-control-sm fw-bold border-warning" style="max-width:320px; font-size:0.85rem;" oninput="updateCatTabTitle({{ $wCat->id }}, this.value)">
                        </div>
                        <div>
                            @if($winnerCategories->count() > 1)
                                <button type="button" class="btn btn-sm btn-outline-danger fw-bold" style="font-size:0.75rem; border-radius:8px;" onclick="deleteWinnerCategory({{ $wCat->id }}, '{{ addslashes($wCat->name) }}')">
                                    <i class="bi bi-trash me-1"></i> Hapus Kategori Ini
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Step 1 (Pemenang): Cari & Pilih Peserta Pemenang Kategori Ini --}}
                    <div class="card-minimal p-4 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div style="width:24px;height:24px;border-radius:6px;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">1. Cari &amp; Pilih Pemenang untuk <span class="text-warning cat-name-display-{{ $wCat->id }}">{{ $wCat->name }}</span></h6>
                        </div>
                        <p class="text-muted" style="font-size:0.75rem; margin-top:-4px; margin-bottom:12px;">
                            Ketik nama peserta, email, atau nama tim terdaftar untuk menambahkan pemenang di kategori ini. Anda dapat menambahkan predikat juaranya (misal: "Juara 1", "Juara 2", "Juara Favorit").
                        </p>

                        <!-- Search Input -->
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius:10px 0 0 10px;">
                                    <i class="bi bi-search text-muted"></i>
                                </span>
                                <input type="text" id="winner-search-input-cat-{{ $wCat->id }}" class="form-control border-start-0 winner-search-field" placeholder="Ketik nama peserta, email, atau nama tim..." style="border-radius:0 10px 10px 0; font-size:0.85rem;" autocomplete="off" oninput="filterWinnerParticipantsForCategory({{ $wCat->id }}, this.value)" onfocus="filterWinnerParticipantsForCategory({{ $wCat->id }}, this.value)">
                            </div>
                            <!-- Live Search Results Dropdown -->
                            <div id="winner-search-results-cat-{{ $wCat->id }}" class="position-absolute w-100 bg-white border rounded-3 shadow-lg mt-1 p-2 winner-search-dropdown" style="display:none; z-index:1050; max-height:260px; overflow-y:auto;">
                            </div>
                        </div>

                        <!-- Selected Winners List -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-field-label mb-0">Daftar Pemenang Kategori Ini (<span id="winner-count-label-cat-{{ $wCat->id }}">0</span>)</label>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:0.68rem; font-weight:700;">Multi-Kategori Aktif</span>
                        </div>

                        <div id="selected-winners-container-cat-{{ $wCat->id }}" class="d-flex flex-column gap-2 mb-2">
                            <!-- Populated by JavaScript -->
                        </div>

                        <div id="winner-empty-state-cat-{{ $wCat->id }}" class="text-center py-3 px-2 text-muted rounded-3 border border-dashed" style="background:#fdfcf9; border-color:#fed7aa; font-size:0.78rem;">
                            <i class="bi bi-info-circle text-warning me-1"></i> Belum ada peserta yang dipilih sebagai pemenang untuk kategori ini. Gunakan pencarian di atas untuk menambahkan.
                        </div>
                    </div>

                    {{-- Step 2 (Pemenang): Pilih Template Desain Kategori Ini --}}
                    <div class="card-minimal p-4 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div style="width:24px;height:24px;border-radius:6px;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">2</div>
                            <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Pilih Template Desain (Kategori: <span class="cat-name-display-{{ $wCat->id }}">{{ $wCat->name }}</span>)</h6>
                        </div>
                        
                        <div class="row g-3 template-card-container">
                            @if(!empty($wCat->certificate_custom_template))
                            <div class="col-12">
                                <div class="template-card template-card-cat-{{ $wCat->id }} active" id="card-custom-cat-{{ $wCat->id }}" onclick="selectCategoryCustomTemplate({{ $wCat->id }})" style="border-color: #f59e0b; background: #fffbeb;">
                                    <div class="check-icon" style="background: #f59e0b; display: flex;"><i class="bi bi-check"></i></div>
                                    <div class="d-flex align-items-center p-3 gap-3">
                                        <div style="width:46px; height:46px; border-radius:12px; background:#f59e0b; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.4rem; flex-shrink:0;">
                                            <i class="bi bi-trophy-fill"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2">
                                                <div style="font-weight:800; font-size:0.88rem; color:#92400e;">Template Custom Builder Aktif</div>
                                                <span class="badge bg-warning text-dark" style="font-size:0.65rem;">Sedang Digunakan</span>
                                            </div>
                                            <div style="font-size:0.72rem; color:#b45309; margin-top:2px;">Template sertifikat visual khusus hasil rancangan dari Visual Builder untuk kategori ini.</div>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.crm.certificates.template-builder', ['event' => $event, 'type' => 'pemenang', 'category_id' => $wCat->id]) }}" class="btn btn-sm btn-warning text-dark fw-bold px-3" style="font-size:0.75rem; border-radius:8px;">
                                                <i class="bi bi-pencil-square me-1"></i> Edit Builder
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @foreach($tpls as $t)
                            <div class="col-md-6">
                                <div class="template-card template-card-cat-{{ $wCat->id }} {{ (empty($wCat->certificate_custom_template) && ($wCat->certificate_template ?? 'template_1') == $t['id']) ? 'active' : '' }}" onclick="selectCategoryTemplate({{ $wCat->id }}, '{{ $t['id'] }}', this)">
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
                        <input type="hidden" name="certificate_template_cat_{{ $wCat->id }}" id="selected_template_cat_{{ $wCat->id }}" value="{{ $wCat->certificate_template ?? 'template_1' }}">
                        
                        <div class="d-flex mt-4 justify-content-between align-items-center mb-2">
                            <label class="form-field-label mb-0">File Tambahan Kategori (Halaman Kedua)</label>
                        </div>
                        <div class="mb-3">
                            @if(!empty($wCat->file_tambahan))
                                <div class="mb-2 position-relative d-inline-block" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--crm-border);" id="existing-file-tambahan-cat-{{ $wCat->id }}-container">
                                    <img src="{{ asset('uploads/' . str_replace('storage/', '', $wCat->file_tambahan)) }}" style="width: 100%; height: 100%; object-fit: contain; background: #fff;" alt="File Tambahan Kategori">
                                    <label class="position-absolute d-flex align-items-center justify-content-center" style="top:5px; right:5px; width:24px; height:24px; background:rgba(255,255,255,0.9); border-radius:6px; cursor:pointer; box-shadow:0 2px 4px rgba(0,0,0,0.1);" title="Hapus File Tambahan">
                                        <input type="checkbox" name="delete_file_tambahan_cat_{{ $wCat->id }}" value="1" class="d-none" onchange="document.getElementById('existing-file-tambahan-cat-{{ $wCat->id }}-container').style.opacity = this.checked ? '0.3' : '1';">
                                        <i class="bi bi-trash text-danger" style="font-size:0.75rem;"></i>
                                    </label>
                                </div>
                            @endif
                            <div class="mb-2 d-none position-relative" id="new-file-tambahan-cat-{{ $wCat->id }}-preview" style="width: 120px; height: 120px; border-radius: 12px; overflow: hidden; border: 1px solid var(--crm-border);">
                                <img id="new-file-tambahan-cat-{{ $wCat->id }}-img" src="" style="width: 100%; height: 100%; object-fit: contain; background: #fff;" alt="New File Tambahan">
                                <span class="position-absolute badge bg-primary" style="bottom: 5px; right: 5px;">Baru</span>
                            </div>
                            <input type="file" accept="image/*" name="file_tambahan_cat_{{ $wCat->id }}" class="form-field mb-2" onchange="if(this.files && this.files[0]) { let reader = new FileReader(); reader.onload = function(e) { document.getElementById('new-file-tambahan-cat-{{ $wCat->id }}-img').src = e.target.result; document.getElementById('new-file-tambahan-cat-{{ $wCat->id }}-preview').classList.remove('d-none'); document.getElementById('new-file-tambahan-cat-{{ $wCat->id }}-preview').classList.add('d-inline-block'); }; reader.readAsDataURL(this.files[0]); } else { document.getElementById('new-file-tambahan-cat-{{ $wCat->id }}-preview').classList.add('d-none'); document.getElementById('new-file-tambahan-cat-{{ $wCat->id }}-preview').classList.remove('d-inline-block'); }">
                            <small class="text-muted d-block mt-1">Opsional. File ini akan digabungkan di halaman kedua PDF sertifikat untuk kategori ini.</small>
                        </div>

                        <div class="mt-4 p-3 rounded-4 bg-light border d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold text-dark small"><i class="bi bi-magic me-1 text-warning"></i> Custom Template Builder (Kategori Ini)</div>
                                <div class="text-muted" style="font-size:0.75rem;">Buat template visual custom khusus untuk sertifikat kategori ini dengan drag &amp; drop.</div>
                            </div>
                            <a href="{{ route('admin.crm.certificates.template-builder', ['event' => $event, 'type' => 'pemenang', 'category_id' => $wCat->id]) }}" class="btn btn-sm btn-warning text-dark fw-bold px-3 py-1.5" style="font-size:0.75rem; border-radius:8px;">
                                Buka Builder
                            </a>
                        </div>

                        @if(!empty($wCat->certificate_custom_template))
                        <div class="mt-3 p-3 rounded-4 border d-flex justify-content-between align-items-center" style="background:#fffdf5; border-color:#fde68a;">
                            <div>
                                <div class="fw-bold text-warning small"><i class="bi bi-patch-check-fill me-1"></i> Menggunakan Template Custom Kategori</div>
                                <div class="text-muted" style="font-size:0.75rem;">Template custom aktif untuk kategori ini.</div>
                            </div>
                            <button type="submit" form="reset-custom-form-cat-{{ $wCat->id }}" class="btn btn-sm btn-outline-danger fw-bold px-3 py-1.5" style="font-size:0.75rem; border-radius:8px;">
                                Hapus Custom
                            </button>
                        </div>
                        @endif
                    </div>

                    {{-- Step 3 (Pemenang): Kelola Aset Visual Kategori Ini --}}
                    <div class="card-minimal p-4 mb-4">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div style="width:24px;height:24px;border-radius:6px;background:#f59e0b;color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">3</div>
                            <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Kelola Aset Visual (Kategori: <span class="cat-name-display-{{ $wCat->id }}">{{ $wCat->name }}</span>)</h6>
                        </div>

                        @php
                            $catLogos = is_array($wCat->certificate_logo) ? $wCat->certificate_logo : ($wCat->certificate_logo ? [$wCat->certificate_logo] : $logosPemenang);
                            $catSigs = is_array($wCat->certificate_signature) ? $wCat->certificate_signature : ($wCat->certificate_signature ? [$wCat->certificate_signature] : $sigsPemenang);
                        @endphp

                        <div class="row g-4">
                            {{-- Logos --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-field-label mb-0">Logo Partner Kategori</label>
                                    <button type="button" id="addLogoBtn_cat_{{ $wCat->id }}" onclick="addCategoryLogoField({{ $wCat->id }})" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                        <i class="bi bi-plus-lg me-1"></i>Tambah Baris
                                    </button>
                                </div>
                                <div id="logoUploadContainer_cat_{{ $wCat->id }}" class="mb-3">
                                    <input type="file" name="certificate_logo_cat_{{ $wCat->id }}[]" class="form-field mb-2 logo-file-input" accept="image/*" onchange="onCategoryLogoFileChange(this, 'init_cat_{{ $wCat->id }}_0', {{ $wCat->id }})">
                                </div>
                                
                                <div id="existingLogos_cat_{{ $wCat->id }}" class="d-flex flex-wrap gap-3">
                                    @foreach($catLogos as $logo)
                                        <div class="asset-item">
                                            <img src="{{ asset('uploads/' . $logo) }}" style="height:40px;object-fit:contain;">
                                            <div class="asset-delete" onclick="markCategoryDelete('logo', '{{ $logo }}', this, event, {{ $wCat->id }})"><i class="bi bi-x"></i></div>
                                            <input type="hidden" name="delete_logos_cat_{{ $wCat->id }}[]" value="" class="delete-logo-input-cat-{{ $wCat->id }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <hr style="border-color:var(--crm-border-soft);margin:0.5rem 0;">

                            {{-- Signatures --}}
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-field-label mb-0">Tanda Tangan Digital Kategori</label>
                                    <button type="button" id="addSigBtn_cat_{{ $wCat->id }}" onclick="addCategorySignatureField({{ $wCat->id }})" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                        <i class="bi bi-plus-lg me-1"></i>Tambah TTD
                                    </button>
                                </div>

                                <div id="signaturesContainer_cat_{{ $wCat->id }}">
                                    @foreach($catSigs as $i => $sig)
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
                                                                <input type="checkbox" name="replace_sig_cat_{{ $wCat->id }}_{{ $i }}" value="1" style="display:none;" class="sig-replace-checkbox" onchange="toggleCategorySigReplace(this, {{ $wCat->id }}, {{ $i }})">
                                                                Ganti Gambar
                                                            </label>
                                                        </div>
                                                        <input type="hidden" name="existing_signature_image_cat_{{ $wCat->id }}[{{ $i }}]" value="{{ $sigPath }}" class="existing-sig-path">
                                                        <div id="sig_file_cat_{{ $wCat->id }}_{{ $i }}" style="display:none;">
                                                            <input type="file" name="certificate_signature_file_cat_{{ $wCat->id }}[{{ $i }}]" class="form-field sig-file-input" accept="image/*" onchange="onCategorySigFileChange(this, {{ $wCat->id }}, {{ $i }})">
                                                        </div>
                                                    @else
                                                        <input type="file" name="certificate_signature_file_cat_{{ $wCat->id }}[{{ $i }}]" class="form-field sig-file-input" accept="image/*" onchange="onCategorySigFileChange(this, {{ $wCat->id }}, {{ $i }})">
                                                    @endif
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-field-label">Nama Penandatangan</label>
                                                    <input type="text" name="signature_name_cat_{{ $wCat->id }}[{{ $i }}]" value="{{ $sigName }}" class="form-field sig-name-input" placeholder="cth: Dr. Ahmad Fauzi" onkeyup="renderPreview()">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <label class="form-field-label mb-0">Jabatan</label>
                                                        <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="removeCategorySigEntry(this, '{{ $sigPath }}', event, {{ $wCat->id }})">Hapus</button>
                                                    </div>
                                                    <input type="text" name="signature_position_cat_{{ $wCat->id }}[{{ $i }}]" value="{{ $sigPos }}" class="form-field sig-pos-input" placeholder="cth: Ketua Juri Lomba" onkeyup="renderPreview()">
                                                    <input type="hidden" name="delete_signatures_cat_{{ $wCat->id }}[]" value="" class="delete-sig-input-cat-{{ $wCat->id }}">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <div class="d-flex justify-content-end mb-4">
                <button type="submit" class="btn fw-800 px-5 shadow-sm" style="background:var(--crm-navy);color:#fff;border-radius:10px;padding-top:0.75rem;padding-bottom:0.75rem;">
                    <i class="bi bi-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </div>

        <!-- Right Column: Live Preview & Guides -->
        <div class="col-lg-6">
            <!-- Certificate Live Preview Card -->
            <div class="card-minimal p-4 mb-4 sticky-top shadow-sm" style="top: 20px; z-index: 10; background: #fff; border: 1px solid var(--crm-border-soft); border-radius: 16px;">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;"><i class="bi bi-eye-fill"></i></div>
                        <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Live Preview Sertifikat</h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold" id="badge-custom-active" style="display:none; font-size:0.65rem;">
                            <i class="bi bi-magic me-1"></i>Custom Builder
                        </span>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <!-- Mode Switcher: Custom vs Standar (visible when custom template exists) -->
                        <div class="btn-group btn-group-sm" id="preview-mode-switch" style="display: none;">
                            <button type="button" class="btn btn-outline-primary btn-sm py-0 px-2 active fw-bold" id="btn-mode-custom" style="font-size:0.7rem;" onclick="setPreviewMode('custom')">
                                <i class="bi bi-magic me-1"></i>Custom
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-2 fw-bold" id="btn-mode-standard" style="font-size:0.7rem;" onclick="setPreviewMode('standard')">
                                <i class="bi bi-layout-text-window-reverse me-1"></i>Standar
                            </button>
                        </div>

                        @if($isLomba)
                        <!-- Preview Switcher -->
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="preview-switcher-btn active" data-section="lolos" onclick="switchSection('lolos')">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>Lolos
                            </button>
                            <button type="button" class="preview-switcher-btn" data-section="tidak_lolos" onclick="switchSection('tidak_lolos')">
                                <i class="bi bi-award text-secondary me-1"></i>Tidak Lolos
                            </button>
                            <button type="button" class="preview-switcher-btn is-pemenang" data-section="pemenang" onclick="switchSection('pemenang')">
                                <i class="bi bi-trophy-fill text-warning me-1"></i>Pemenang
                            </button>
                        </div>
                        <div id="preview-cat-picker-container" style="display: none;">
                            <select class="form-select form-select-sm py-0 px-2 fw-bold" id="preview-cat-select" style="font-size:0.7rem; border-color:#fde68a; background:#fffdf5; color:#92400e; height:26px;" onchange="switchWinnerCategory(this.value)">
                                @foreach($winnerCategories as $wCat)
                                    <option value="{{ $wCat->id }}">🏆 {{ $wCat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Scaling Container -->
                <div id="certificate-preview-container" style="border: 1px solid var(--crm-border); border-radius: 12px; box-shadow: var(--crm-shadow-sm); background: #fff; overflow: hidden; width: 100%; position: relative;">
                    <div id="cert-preview-aspect" style="width: 100%; padding-top: 62.96%; position: relative; overflow: hidden;">
                        <div id="cert-preview-scaler" style="position: absolute; top: 0; left: 0; width: 1020px; height: 642px; transform-origin: top left;">
                            
                            <!-- The dynamic certificate preview page -->
                            <div class="certificate-page {{ ($event->certificate_template ?? 'template_1') }}" id="preview-cert-page">
                                
                                <!-- Template 1 Decorations -->
                                <div class="template-decorations-1">
                                    <div style="position: absolute; top: 46px; left: 50px; width: 480px; height: 4px; background: #eab308; z-index: 2;"></div>
                                    <div style="position: absolute; bottom: 46px; right: 50px; width: 480px; height: 4px; background: #eab308; z-index: 2;"></div>

                                    <div style="position: absolute; top: 0; right: 0; width: 412px; height: 366px; z-index: 1; pointer-events: none;">
                                        <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                            <path d="M 30,0 C 50,40 70,60 100,80 L 100,0 Z" fill="#7f1d1d" />
                                            <path d="M 40,0 C 58,38 74,54 100,70 L 100,0 Z" fill="#eab308" />
                                            <path d="M 50,0 C 66,34 78,46 100,60 L 100,0 Z" fill="#991b1b" />
                                            <path d="M 65,0 C 78,26 86,34 100,45 L 100,0 Z" fill="#eab308" />
                                            <path d="M 75,0 C 85,20 90,25 100,35 L 100,0 Z" fill="#7f1d1d" />
                                        </svg>
                                    </div>

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
                                    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;">
                                        <svg width="100%" height="100%" viewBox="0 0 297 210" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                                            <polygon points="0,0 60,0 0,60" fill="#d4af37" />
                                            <polygon points="0,0 55,0 0,55" fill="#fef08a" />
                                            <polygon points="0,0 40,0 0,40" fill="#ca8a04" />
                                            <polygon points="297,0 215,0 297,125" fill="#0f172a" />
                                            <polygon points="297,210 185,210 297,135" fill="#ca8a04" />
                                            <polygon points="297,210 190,210 297,137" fill="#fbbf24" />
                                        </svg>
                                    </div>
                                    <div class="gold-badge"><div class="gold-badge-inner"></div></div>
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
                                    <p style="font-size: 13pt; font-weight: bold; margin: 4px 0 8px 0; font-family: Arial, Helvetica, sans-serif;" id="preview-t4-role">
                                        {{ $isLomba ? 'PESERTA LOLOS / FINALIS' : 'PESERTA' }}
                                    </p>
                                    <p style="font-size: 8.5pt; margin: 8px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">Dalam Kegiatan</p>
                                    
                                    <h2 style="font-size: 14pt; font-weight: bold; margin: 4px 0 4px 0; font-family: Arial, Helvetica, sans-serif;">
                                        "{{ $event->title }}"
                                    </h2>
                                    
                                    <p style="font-size: 8pt; margin: 9px 0 0 0; font-family: Arial, Helvetica, sans-serif;">
                                        Yang diselenggarakan pada: 
                                        <strong>
                                            @if($event->event_until_date && $event->event_date && $event->event_date->format('m Y') == $event->event_until_date->format('m Y'))
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
                                <div class="cert-id" style="background: rgba(251, 191, 36, 0.1); padding: 5px 10px; border-radius: 4px;">Verified Certificate ID: 001/AKD10/AKD-BPA/2026</div>
                            </div>

                            <!-- The dynamic certificate preview page for Custom Builder template -->
                            <div id="preview-custom-page" style="display: none; position: absolute; top: 0; left: 0; width: 1000px; height: 706px; overflow: hidden; background: #ffffff;">
                                <!-- Dynamically rendered by renderCustomPreview() -->
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

@if(!empty($event->certificate_custom_template))
<form id="reset-custom-form-lolos" action="{{ route('admin.crm.certificates.reset-custom-template', $event) }}" method="POST" style="display:none;" onsubmit="return confirm('Hapus template custom lolos dan kembali ke template standar?')">
    @csrf
    <input type="hidden" name="type" value="lolos">
</form>
@endif

@if($isLomba && !empty($event->certificate_custom_template_tidak_lolos))
<form id="reset-custom-form-tidak-lolos" action="{{ route('admin.crm.certificates.reset-custom-template', $event) }}" method="POST" style="display:none;" onsubmit="return confirm('Hapus template custom tidak lolos dan kembali ke template standar?')">
    @csrf
    <input type="hidden" name="type" value="tidak_lolos">
</form>
@endif

@if(!empty($event->certificate_custom_template_pemenang))
<form id="reset-custom-form-pemenang" action="{{ route('admin.crm.certificates.reset-custom-template', $event) }}" method="POST" style="display:none;" onsubmit="return confirm('Hapus template custom pemenang dan kembali ke template standar?')">
    @csrf
    <input type="hidden" name="type" value="pemenang">
</form>
@endif

@foreach($winnerCategories as $wCat)
    @if(!empty($wCat->certificate_custom_template))
    <form id="reset-custom-form-cat-{{ $wCat->id }}" action="{{ route('admin.crm.certificates.reset-custom-template', $event) }}" method="POST" style="display:none;" onsubmit="return confirm('Hapus template custom kategori {{ addslashes($wCat->name) }} dan kembali ke template standar?')">
        @csrf
        <input type="hidden" name="type" value="pemenang">
        <input type="hidden" name="category_id" value="{{ $wCat->id }}">
    </form>
    @endif
@endforeach

<!-- Modal Tambah Kategori Pemenang -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;border-radius:8px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <div>
                        <h6 class="modal-title fw-800 mb-0" id="addCategoryModalLabel">Tambah Kategori Pemenang</h6>
                        <small class="text-muted" style="font-size:0.75rem;">Buat kategori juara baru dengan sertifikat &amp; builder mandiri</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold small text-muted">Nama Kategori Pemenang <span class="text-danger">*</span></label>
                    <input type="text" id="new-category-name-input" class="form-control" placeholder="Contoh: Kategori UI/UX Design, Kategori Mobile App..." style="font-size:0.88rem; border-radius:10px;">
                    <small class="text-muted mt-1 d-block" style="font-size:0.75rem;">Setiap kategori akan memiliki halaman builder visual dan daftar pemenang tersendiri.</small>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-2 px-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-3 px-4 shadow-sm" id="btn-submit-new-category" onclick="submitNewCategory()">
                    <i class="bi bi-plus-circle me-1"></i> Buat Kategori
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const isLomba = {{ $isLomba ? 'true' : 'false' }};
    let currentSection = 'lolos'; // 'lolos', 'tidak_lolos', or 'pemenang'

    // Custom templates from backend for lolos & tidak_lolos
    const customTemplates = {
        lolos: @json($event->certificate_custom_template),
        tidak_lolos: @json($event->certificate_custom_template_tidak_lolos),
        pemenang: @json($event->certificate_custom_template_pemenang)
    };

    // Active preview mode for lolos & tidak_lolos
    let previewMode = {
        lolos: (customTemplates.lolos && customTemplates.lolos.elements && customTemplates.lolos.elements.length > 0) ? 'custom' : 'standard',
        tidak_lolos: (customTemplates.tidak_lolos && customTemplates.tidak_lolos.elements && customTemplates.tidak_lolos.elements.length > 0) ? 'custom' : 'standard'
    };

    // Global data stores for preview assets
    const uploadedFiles = {
        lolos: { logos: {}, signatures: {} },
        tidak_lolos: { logos: {}, signatures: {} }
    };

    // ── Multi-Category Winner State ──
    const winnerCategoriesData = {!! json_encode($winnerCategories->map(function($c) {
        return [
            'id' => (int)$c->id,
            'name' => (string)$c->name,
            'certificate_template' => (string)($c->certificate_template ?: 'template_1'),
            'certificate_custom_template' => $c->certificate_custom_template,
            'certificate_logo' => is_array($c->certificate_logo) ? $c->certificate_logo : ($c->certificate_logo ? [$c->certificate_logo] : []),
            'certificate_signature' => is_array($c->certificate_signature) ? $c->certificate_signature : ($c->certificate_signature ? [$c->certificate_signature] : []),
            'assignments' => $c->winnerAssignments->map(function($a) {
                $name = $a->registration->user->name ?? $a->registration->full_name ?? ('Peserta #' . $a->event_registration_id);
                $email = $a->registration->user->email ?? '-';
                $team = $a->registration->team->name ?? $a->registration->team_name ?? null;
                return [
                    'id' => (int)$a->event_registration_id,
                    'name' => (string)$name,
                    'email' => (string)$email,
                    'team' => $team ? (string)$team : null,
                    'winner_title' => (string)($a->winner_title ?: 'Juara 1')
                ];
            })->values()
        ];
    })->values()) !!};

    let activeWinnerCatId = (winnerCategoriesData.length > 0) ? winnerCategoriesData[0].id : null;

    let selectedWinnersByCat = {};
    winnerCategoriesData.forEach(cat => {
        selectedWinnersByCat[cat.id] = {};
        if (cat.assignments && Array.isArray(cat.assignments)) {
            cat.assignments.forEach(a => {
                selectedWinnersByCat[cat.id][a.id] = {
                    id: a.id,
                    name: a.name,
                    email: a.email,
                    team: a.team,
                    winner_title: a.winner_title || 'Juara 1'
                };
            });
        }
    });

    let categoryPreviewMode = {};
    winnerCategoriesData.forEach(cat => {
        const hasCustom = cat.certificate_custom_template && cat.certificate_custom_template.elements && cat.certificate_custom_template.elements.length > 0;
        categoryPreviewMode[cat.id] = hasCustom ? 'custom' : 'standard';
    });

    const uploadedCategoryFiles = {};
    winnerCategoriesData.forEach(cat => {
        uploadedCategoryFiles[cat.id] = { logos: {}, signatures: {} };
    });

    // ── Event Participants ──
    const eventParticipants = {!! json_encode($eventParticipants ?? []) !!};

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    // ── Winner Search & Selection Per Category ──
    function filterWinnerParticipantsForCategory(catId, query) {
        const resultsContainer = document.getElementById(`winner-search-results-cat-${catId}`);
        if (!resultsContainer) return;

        const q = (query || '').trim().toLowerCase();
        let matches = [];

        if (!q) {
            matches = eventParticipants.slice(0, 8);
        } else {
            matches = eventParticipants.filter(p => {
                const nameMatch = p.name.toLowerCase().includes(q);
                const emailMatch = p.email.toLowerCase().includes(q);
                const teamMatch = p.team && p.team.toLowerCase().includes(q);
                return nameMatch || emailMatch || teamMatch;
            }).slice(0, 15);
        }

        if (matches.length === 0) {
            resultsContainer.innerHTML = `
                <div class="p-3 text-center text-muted" style="font-size:0.78rem;">
                    <i class="bi bi-person-x me-1"></i> Tidak ditemukan peserta yang cocok dengan kata kunci.
                </div>
            `;
            resultsContainer.style.display = 'block';
            return;
        }

        const catWinners = selectedWinnersByCat[catId] || {};
        let html = '';
        matches.forEach(p => {
            const isAlready = !!catWinners[p.id];
            html += `
                <div class="winner-search-result-item d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bold text-dark" style="font-size:0.82rem;">${escapeHtml(p.name)}</div>
                        <div class="text-muted" style="font-size:0.7rem;">
                            <span>${escapeHtml(p.email)}</span>
                            ${p.team ? `<span class="ms-2 badge bg-light text-secondary border">${escapeHtml(p.team)}</span>` : ''}
                        </div>
                    </div>
                    <div>
                        ${isAlready ? `
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.68rem;">
                                <i class="bi bi-check-lg me-1"></i>Sudah Terpilih
                            </span>
                        ` : `
                            <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" style="font-size:0.72rem; border-radius:6px; padding:3px 10px;" onclick="addWinnerToCategory(${catId}, ${p.id})">
                                <i class="bi bi-plus-circle me-1"></i>Pilih
                            </button>
                        `}
                    </div>
                </div>
            `;
        });

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
    }

    function addWinnerToCategory(catId, regId, defaultTitle = 'Juara 1') {
        const participant = eventParticipants.find(p => p.id === regId);
        if (!participant) return;

        if (!selectedWinnersByCat[catId]) selectedWinnersByCat[catId] = {};
        selectedWinnersByCat[catId][regId] = {
            id: participant.id,
            name: participant.name,
            email: participant.email,
            team: participant.team,
            winner_title: defaultTitle
        };

        renderSelectedWinnersForCategory(catId);

        const searchInput = document.getElementById(`winner-search-input-cat-${catId}`);
        const resultsContainer = document.getElementById(`winner-search-results-cat-${catId}`);
        if (searchInput) searchInput.value = '';
        if (resultsContainer) resultsContainer.style.display = 'none';

        if (currentSection === 'pemenang' && activeWinnerCatId === catId) {
            renderPreview();
        }
    }

    function removeWinnerFromCategory(catId, regId) {
        if (selectedWinnersByCat[catId]) {
            delete selectedWinnersByCat[catId][regId];
            renderSelectedWinnersForCategory(catId);
            if (currentSection === 'pemenang' && activeWinnerCatId === catId) {
                renderPreview();
            }
        }
    }

    function updateWinnerCategoryTitle(catId, regId, title) {
        if (selectedWinnersByCat[catId] && selectedWinnersByCat[catId][regId]) {
            selectedWinnersByCat[catId][regId].winner_title = title;
            if (currentSection === 'pemenang' && activeWinnerCatId === catId) {
                renderPreview();
            }
        }
    }

    function renderSelectedWinnersForCategory(catId) {
        const container = document.getElementById(`selected-winners-container-cat-${catId}`);
        const emptyState = document.getElementById(`winner-empty-state-cat-${catId}`);
        const countLabel = document.getElementById(`winner-count-label-cat-${catId}`);
        const tabCountBadge = document.getElementById(`cat-tab-count-${catId}`);
        if (!container) return;

        const catWinners = selectedWinnersByCat[catId] || {};
        const keys = Object.keys(catWinners);
        if (countLabel) countLabel.textContent = keys.length;
        if (tabCountBadge) tabCountBadge.textContent = keys.length;

        if (keys.length === 0) {
            container.innerHTML = '';
            if (emptyState) emptyState.style.display = 'block';
            return;
        }

        if (emptyState) emptyState.style.display = 'none';

        let html = '';
        keys.forEach(key => {
            const w = catWinners[key];
            html += `
                <div class="winner-card-item d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3" data-id="${w.id}">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:36px; height:36px; border-radius:10px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0;">
                            <i class="bi bi-trophy-fill"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size:0.88rem;">${escapeHtml(w.name)}</div>
                            <div class="text-muted" style="font-size:0.72rem;">
                                <i class="bi bi-envelope me-1"></i>${escapeHtml(w.email)}
                                ${w.team ? `<span class="ms-2 badge bg-light text-secondary border"><i class="bi bi-people me-1"></i>${escapeHtml(w.team)}</span>` : ''}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                        <div class="input-group input-group-sm" style="max-width: 220px;">
                            <span class="input-group-text bg-light text-muted" style="font-size:0.72rem; font-weight:600;">Predikat</span>
                            <input type="text" name="winner_titles_cat_${catId}[${w.id}]" class="form-control form-control-sm" value="${escapeHtml(w.winner_title)}" placeholder="cth: Juara 1" oninput="updateWinnerCategoryTitle(${catId}, ${w.id}, this.value)" style="font-size:0.75rem;">
                        </div>
                        <input type="hidden" name="winner_registration_ids_cat_${catId}[]" value="${w.id}">
                        <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:8px; padding: 4px 8px;" title="Hapus dari kategori ini" onclick="removeWinnerFromCategory(${catId}, ${w.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    }

    // ── Winner Category Switcher ──
    function switchWinnerCategory(catId) {
        catId = parseInt(catId);
        activeWinnerCatId = catId;

        document.querySelectorAll('.winner-cat-tab-btn').forEach(btn => {
            btn.classList.toggle('active', parseInt(btn.dataset.catId) === catId);
        });

        document.querySelectorAll('.winner-category-subpane').forEach(pane => {
            pane.style.display = (pane.id === `winner-cat-subpane-${catId}`) ? 'block' : 'none';
        });

        const previewSelect = document.getElementById('preview-cat-select');
        if (previewSelect && parseInt(previewSelect.value) !== catId) {
            previewSelect.value = catId;
        }

        checkCategoryLogoCount(catId);
        renderPreview();
    }

    function updateCatTabTitle(catId, val) {
        const titleSpan = document.getElementById(`cat-tab-title-${catId}`);
        if (titleSpan) titleSpan.textContent = val || 'Kategori';
        document.querySelectorAll(`.cat-name-display-${catId}`).forEach(el => el.textContent = val || 'Kategori');
        
        const previewSelect = document.getElementById('preview-cat-select');
        if (previewSelect) {
            const opt = previewSelect.querySelector(`option[value="${catId}"]`);
            if (opt) opt.textContent = '🏆 ' + (val || 'Kategori');
        }

        const catObj = winnerCategoriesData.find(c => c.id === catId);
        if (catObj) catObj.name = val;

        if (currentSection === 'pemenang' && activeWinnerCatId === catId) {
            renderPreview();
        }
    }

    // ── Modal Add & Delete Category ──
    function openAddCategoryModal() {
        const input = document.getElementById('new-category-name-input');
        if (input) input.value = '';
        const modalEl = document.getElementById('addCategoryModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
        setTimeout(() => input && input.focus(), 400);
    }

    function submitNewCategory() {
        const input = document.getElementById('new-category-name-input');
        const nameVal = (input ? input.value : '').trim();
        if (!nameVal) {
            alert('Silakan masukkan nama kategori pemenang.');
            if (input) input.focus();
            return;
        }

        const btn = document.getElementById('btn-submit-new-category');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        }

        fetch("{{ route('admin.crm.certificates.winner-categories.store', $event) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ name: nameVal })
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                window.location.hash = '#pemenang';
                window.location.reload();
            } else {
                alert((data && data.message) || 'Gagal menambahkan kategori.');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Buat Kategori';
                }
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Buat Kategori';
            }
        });
    }

    function deleteWinnerCategory(catId, catName) {
        if (!confirm(`Hapus kategori "${catName}"? Pemenang dan konfigurasi di kategori ini akan dihapus.`)) return;

        const url = "{{ route('admin.crm.certificates.winner-categories.destroy', [$event, ':catId']) }}".replace(':catId', catId);
        fetch(url, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.success) {
                window.location.hash = '#pemenang';
                window.location.reload();
            } else {
                alert((data && data.message) || 'Gagal menghapus kategori.');
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan jaringan.');
        });
    }

    // ── Template Selection per Category ──
    function selectCategoryTemplate(catId, tplId, element) {
        const parentContainer = element.closest('.template-card-container');
        if (parentContainer) {
            parentContainer.querySelectorAll('.template-card').forEach(el => el.classList.remove('active'));
        }
        element.classList.add('active');

        const customCard = document.getElementById(`card-custom-cat-${catId}`);
        if (customCard) customCard.classList.remove('active');

        const input = document.getElementById(`selected_template_cat_${catId}`);
        if (input) input.value = tplId;

        const catObj = winnerCategoriesData.find(c => c.id === catId);
        if (catObj) catObj.certificate_template = tplId;

        categoryPreviewMode[catId] = 'standard';
        if (currentSection === 'pemenang' && activeWinnerCatId === catId) {
            renderPreview();
        }
    }

    function selectCategoryCustomTemplate(catId) {
        const pane = document.getElementById(`winner-cat-subpane-${catId}`);
        if (pane) {
            pane.querySelectorAll('.template-card-container .template-card').forEach(el => el.classList.remove('active'));
        }
        const customCard = document.getElementById(`card-custom-cat-${catId}`);
        if (customCard) customCard.classList.add('active');

        categoryPreviewMode[catId] = 'custom';
        if (currentSection === 'pemenang' && activeWinnerCatId === catId) {
            renderPreview();
        }
    }

    // ── Category Logos & Signatures ──
    function addCategoryLogoField(catId) {
        const container = document.getElementById(`logoUploadContainer_cat_${catId}`);
        const existingContainer = document.getElementById(`existingLogos_cat_${catId}`);
        if (!container) return;

        const currentInputs = container.querySelectorAll('input[type="file"]').length;
        const existingLogos = existingContainer ? existingContainer.querySelectorAll('.asset-item:not(.marked-deleted)').length : 0;
        if ((currentInputs + existingLogos) < 3) {
            const id = 'dyn_cat_' + catId + '_' + Date.now();
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-2';
            div.innerHTML = `
                <div class="w-100"><input type="file" name="certificate_logo_cat_${catId}[]" class="form-field logo-file-input" accept="image/*" onchange="onCategoryLogoFileChange(this, '${id}', ${catId})"></div>
                <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="onRemoveCategoryLogoField(this, '${id}', ${catId})"><i class="bi bi-trash"></i></button>
            `;
            container.appendChild(div);
            checkCategoryLogoCount(catId);
        }
    }

    function onCategoryLogoFileChange(input, id, catId) {
        previewNewAsset(input);
        const file = input.files[0];
        if (!uploadedCategoryFiles[catId]) uploadedCategoryFiles[catId] = { logos: {}, signatures: {} };
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedCategoryFiles[catId].logos[id] = e.target.result;
                if (currentSection === 'pemenang' && activeWinnerCatId === catId) renderPreview();
            };
            reader.readAsDataURL(file);
        } else {
            delete uploadedCategoryFiles[catId].logos[id];
            if (currentSection === 'pemenang' && activeWinnerCatId === catId) renderPreview();
        }
    }

    function onRemoveCategoryLogoField(btn, id, catId) {
        btn.parentElement.remove();
        if (uploadedCategoryFiles[catId]) delete uploadedCategoryFiles[catId].logos[id];
        checkCategoryLogoCount(catId);
        if (currentSection === 'pemenang' && activeWinnerCatId === catId) renderPreview();
    }

    function checkCategoryLogoCount(catId) {
        const container = document.getElementById(`logoUploadContainer_cat_${catId}`);
        const existingContainer = document.getElementById(`existingLogos_cat_${catId}`);
        const btn = document.getElementById(`addLogoBtn_cat_${catId}`);
        if (!container || !btn) return;
        const currentInputs = container.querySelectorAll('input[type="file"]').length;
        const existingLogos = existingContainer ? existingContainer.querySelectorAll('.asset-item:not(.marked-deleted)').length : 0;
        btn.style.display = (currentInputs + existingLogos >= 3) ? 'none' : 'inline-flex';
    }

    function markCategoryDelete(type, path, element, event, catId) {
        if(event) { event.preventDefault(); event.stopPropagation(); }
        if(confirm('Hapus aset ini?')) {
            const wrapper = element.closest('.asset-item');
            const input = wrapper.querySelector(`.delete-logo-input-cat-${catId}`);
            if (input) input.value = path;
            wrapper.style.opacity = '0.3';
            wrapper.style.pointerEvents = 'none';
            wrapper.classList.add('marked-deleted');
            checkCategoryLogoCount(catId);
            if (currentSection === 'pemenang' && activeWinnerCatId === catId) renderPreview();
        }
    }

    let catSigIndex = {};
    function addCategorySignatureField(catId) {
        const container = document.getElementById(`signaturesContainer_cat_${catId}`);
        if (!container) return;
        const existing = container.querySelectorAll('.sig-entry').length;
        if (existing >= 3) { alert('Maksimal 3 tanda tangan.'); return; }

        if (!catSigIndex[catId]) catSigIndex[catId] = 10;
        const idx = catSigIndex[catId]++;
        const uniqueId = 'new_cat_' + catId + '_' + Date.now();

        const div = document.createElement('div');
        div.className = 'sig-entry';
        div.innerHTML = `
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-field-label">Gambar TTD <span class="text-danger">*</span></label>
                    <input type="file" name="certificate_signature_file_cat_${catId}[${idx}]" class="form-field sig-file-input" accept="image/*" onchange="onCategorySigFileChange(this, '${uniqueId}', ${catId})">
                </div>
                <div class="col-md-4">
                    <label class="form-field-label">Nama Penandatangan</label>
                    <input type="text" name="signature_name_cat_${catId}[${idx}]" class="form-field sig-name-input" placeholder="cth: Dr. Ahmad Fauzi" onkeyup="renderPreview()">
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-field-label mb-0">Jabatan</label>
                        <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="onRemoveCategorySigRow(this, '${uniqueId}', ${catId})">Hapus</button>
                    </div>
                    <input type="text" name="signature_position_cat_${catId}[${idx}]" class="form-field sig-pos-input" placeholder="cth: Ketua Juri Lomba" onkeyup="renderPreview()">
                </div>
            </div>`;
        container.appendChild(div);
        renderPreview();
    }

    function onRemoveCategorySigRow(btn, uniqueId, catId) {
        btn.closest('.sig-entry').remove();
        if (uploadedCategoryFiles[catId]) delete uploadedCategoryFiles[catId].signatures[uniqueId];
        renderPreview();
    }

    function onCategorySigFileChange(input, id, catId) {
        const file = input.files[0];
        if (!uploadedCategoryFiles[catId]) uploadedCategoryFiles[catId] = { logos: {}, signatures: {} };
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedCategoryFiles[catId].signatures[id] = e.target.result;
                if (currentSection === 'pemenang' && activeWinnerCatId === catId) renderPreview();
            };
            reader.readAsDataURL(file);
        } else {
            delete uploadedCategoryFiles[catId].signatures[id];
            if (currentSection === 'pemenang' && activeWinnerCatId === catId) renderPreview();
        }
    }

    function toggleCategorySigReplace(checkbox, catId, idx) {
        const fileDiv = document.getElementById(`sig_file_cat_${catId}_${idx}`);
        if (!fileDiv) return;
        fileDiv.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            const fileInput = fileDiv.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';
            if (uploadedCategoryFiles[catId]) delete uploadedCategoryFiles[catId].signatures[idx];
        }
        renderPreview();
    }

    function removeCategorySigEntry(btn, path, event, catId) {
        if(event) { event.preventDefault(); event.stopPropagation(); }
        if (!confirm('Hapus tanda tangan ini?')) return;
        const entry = btn.closest('.sig-entry');
        if (path) {
            const hidden = entry.querySelector(`.delete-sig-input-cat-${catId}`);
            if (hidden) hidden.value = path;
            entry.style.opacity = '0.3';
            entry.style.pointerEvents = 'none';
            renderPreview();
        } else {
            entry.remove();
            renderPreview();
        }
    }

    // ── Section Switcher (Lolos / Tidak Lolos / Pemenang) ──
    function switchSection(section) {
        currentSection = section;

        document.querySelectorAll('.cert-section-nav-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.section === section);
        });

        document.querySelectorAll('.preview-switcher-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.section === section);
        });

        const paneLolos = document.getElementById('pane-lolos');
        const paneTidakLolos = document.getElementById('pane-tidak-lolos');
        const panePemenang = document.getElementById('pane-pemenang');
        if (paneLolos) paneLolos.style.display = (section === 'lolos') ? 'block' : 'none';
        if (paneTidakLolos) paneTidakLolos.style.display = (section === 'tidak_lolos') ? 'block' : 'none';
        if (panePemenang) panePemenang.style.display = (section === 'pemenang') ? 'block' : 'none';

        if (section === 'pemenang' && activeWinnerCatId) {
            checkCategoryLogoCount(activeWinnerCatId);
        } else {
            checkLogoCount(section);
        }
        renderPreview();
    }

    function selectCustomTemplate(section = 'lolos') {
        const paneId = (section === 'tidak_lolos') ? 'pane-tidak-lolos' : 'pane-lolos';
        const pane = document.getElementById(paneId);
        if (pane) {
            pane.querySelectorAll('.template-card-container .template-card').forEach(el => el.classList.remove('active'));
        }
        const customCardId = (section === 'tidak_lolos') ? 'card-custom-tidak-lolos' : 'card-custom-lolos';
        const customCard = document.getElementById(customCardId);
        if (customCard) customCard.classList.add('active');

        previewMode[section] = 'custom';
        if (currentSection !== section) {
            switchSection(section);
        } else {
            renderPreview();
        }
    }

    function setPreviewMode(mode) {
        if (currentSection === 'pemenang') {
            categoryPreviewMode[activeWinnerCatId] = mode;
        } else {
            previewMode[currentSection] = mode;
        }
        renderPreview();
    }

    function selectTemplate(id, element, section = 'lolos') {
        const parentContainer = element.closest('.template-card-container');
        if (parentContainer) {
            parentContainer.querySelectorAll('.template-card').forEach(el => el.classList.remove('active'));
        }
        element.classList.add('active');

        const customCardId = (section === 'tidak_lolos') ? 'card-custom-tidak-lolos' : 'card-custom-lolos';
        const customCard = document.getElementById(customCardId);
        if (customCard) customCard.classList.remove('active');

        const inputId = (section === 'tidak_lolos') ? 'selected_template_tidak_lolos' : 'selected_template_lolos';
        const input = document.getElementById(inputId);
        if (input) input.value = id;

        previewMode[section] = 'standard';

        if (currentSection !== section) {
            switchSection(section);
        } else {
            renderPreview();
        }
    }

    function markDelete(type, path, element, event, section = 'lolos') {
        if(event) { event.preventDefault(); event.stopPropagation(); }
        if(confirm('Hapus aset ini?')) {
            const wrapper = element.closest('.asset-item');
            const hiddenClass = (section === 'tidak_lolos') ? '.delete-logo-input-tidak-lolos' : '.delete-logo-input';
            const input = wrapper.querySelector(hiddenClass);
            if (input) input.value = path;
            wrapper.style.opacity = '0.3';
            wrapper.style.pointerEvents = 'none';
            wrapper.classList.add('marked-deleted');
            checkLogoCount(section);
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
            reader.onload = function(e) { preview.src = e.target.result; };
            reader.readAsDataURL(file);
        }
    }

    function onLogoFileChange(input, id, section = 'lolos') {
        previewNewAsset(input);
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedFiles[section].logos[id] = e.target.result;
                renderPreview();
            };
            reader.readAsDataURL(file);
        } else {
            delete uploadedFiles[section].logos[id];
            renderPreview();
        }
    }

    function onRemoveLogoField(btn, id, section = 'lolos') {
        btn.parentElement.remove();
        delete uploadedFiles[section].logos[id];
        checkLogoCount(section);
        renderPreview();
    }

    let logoFileCounter = { lolos: 1, tidak_lolos: 1 };
    function addLogoField(section = 'lolos') {
        const containerId = (section === 'tidak_lolos') ? 'logoUploadContainer_tidak_lolos' : 'logoUploadContainer';
        const existingContainerId = (section === 'tidak_lolos') ? 'existingLogos_tidak_lolos' : 'existingLogos';
        const inputName = (section === 'tidak_lolos') ? 'certificate_logo_tidak_lolos[]' : 'certificate_logo[]';
        
        const container = document.getElementById(containerId);
        if (!container) return;
        const currentInputs = container.querySelectorAll('input[type="file"]').length;
        const existingLogos = document.querySelectorAll(`#${existingContainerId} .asset-item:not(.marked-deleted)`).length;
        
        if ((currentInputs + existingLogos) < 3) {
            const id = 'dyn_' + (logoFileCounter[section]++);
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-2';
            div.innerHTML = `
                <div class="w-100"><input type="file" name="${inputName}" class="form-field logo-file-input" accept="image/*" onchange="onLogoFileChange(this, '${id}', '${section}')"></div>
                <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="onRemoveLogoField(this, '${id}', '${section}')"><i class="bi bi-trash"></i></button>
            `;
            container.appendChild(div);
            checkLogoCount(section);
        }
    }

    function checkLogoCount(section = 'lolos') {
        const containerId = (section === 'tidak_lolos') ? 'logoUploadContainer_tidak_lolos' : 'logoUploadContainer';
        const existingContainerId = (section === 'tidak_lolos') ? 'existingLogos_tidak_lolos' : 'existingLogos';
        const btnId = (section === 'tidak_lolos') ? 'addLogoBtn_tidak_lolos' : 'addLogoBtn';
        
        const container = document.getElementById(containerId);
        const btn = document.getElementById(btnId);
        if (!container || !btn) return;
        
        const currentInputs = container.querySelectorAll('input[type="file"]').length;
        const existingLogos = document.querySelectorAll(`#${existingContainerId} .asset-item:not(.marked-deleted)`).length;
        if ((currentInputs + existingLogos) >= 3) btn.style.display = 'none';
        else btn.style.display = 'inline-flex';
    }

    let sigIndex = {
        lolos: {{ count($sigsLolos) }},
        tidak_lolos: {{ count($sigsTidakLolos) }}
    };
    let sigFileCounter = { lolos: 100, tidak_lolos: 200 };

    function addSignatureField(section = 'lolos') {
        const containerId = (section === 'tidak_lolos') ? 'signaturesContainer_tidak_lolos' : 'signaturesContainer';
        const container = document.getElementById(containerId);
        if (!container) return;
        const existing = container.querySelectorAll('.sig-entry').length;
        if (existing >= 3) { alert('Maksimal 3 tanda tangan.'); return; }
        
        const idx = sigIndex[section]++;
        const uniqueId = 'new_' + (sigFileCounter[section]++);
        const fileField = (section === 'tidak_lolos') ? `certificate_signature_file_tidak_lolos[${idx}]` : `certificate_signature_file[${idx}]`;
        const nameField = (section === 'tidak_lolos') ? `signature_name_tidak_lolos[${idx}]` : `signature_name[${idx}]`;
        const posField  = (section === 'tidak_lolos') ? `signature_position_tidak_lolos[${idx}]` : `signature_position[${idx}]`;

        const div = document.createElement('div');
        div.className = 'sig-entry';
        div.innerHTML = `
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-field-label">Gambar TTD <span class="text-danger">*</span></label>
                    <input type="file" name="${fileField}" class="form-field sig-file-input" accept="image/*" onchange="onSigFileChange(this, '${uniqueId}', '${section}')">
                </div>
                <div class="col-md-4">
                    <label class="form-field-label">Nama Penandatangan</label>
                    <input type="text" name="${nameField}" class="form-field sig-name-input" placeholder="cth: Dr. Ahmad Fauzi" onkeyup="renderPreview()">
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-field-label mb-0">Jabatan</label>
                        <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="onRemoveSigRow(this, '${uniqueId}', '${section}')">Hapus</button>
                    </div>
                    <input type="text" name="${posField}" class="form-field sig-pos-input" placeholder="cth: Ketua Juri Lomba" onkeyup="renderPreview()">
                </div>
            </div>`;
        container.appendChild(div);
        renderPreview();
    }

    function onRemoveSigRow(btn, uniqueId, section = 'lolos') {
        btn.closest('.sig-entry').remove();
        delete uploadedFiles[section].signatures[uniqueId];
        renderPreview();
    }

    function onSigFileChange(input, id, section = 'lolos') {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                uploadedFiles[section].signatures[id] = e.target.result;
                renderPreview();
            };
            reader.readAsDataURL(file);
        } else {
            delete uploadedFiles[section].signatures[id];
            renderPreview();
        }
    }

    function toggleSigReplace(checkbox, idx, section = 'lolos') {
        const fileDiv = document.getElementById(`sig_file_${section}_${idx}`);
        if (!fileDiv) return;
        fileDiv.style.display = checkbox.checked ? 'block' : 'none';
        if (!checkbox.checked) {
            const fileInput = fileDiv.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';
            delete uploadedFiles[section].signatures[idx];
        }
        renderPreview();
    }

    function removeSigEntry(btn, path, event, section = 'lolos') {
        if(event) { event.preventDefault(); event.stopPropagation(); }
        if (!confirm('Hapus tanda tangan ini?')) return;
        const entry = btn.closest('.sig-entry');
        if (path) {
            const hiddenClass = (section === 'tidak_lolos') ? '.delete-sig-input-tidak-lolos' : '.delete-sig-input';
            const hidden = entry.querySelector(hiddenClass);
            if (hidden) hidden.value = path;
            entry.style.opacity = '0.3';
            entry.style.pointerEvents = 'none';
            renderPreview();
        } else {
            entry.remove();
            renderPreview();
        }
    }

    function resolveAssetUrl(src, base64) {
        if (base64 && base64.length > 0) return base64;
        if (!src) return '';
        if (src.startsWith('data:') || src.startsWith('blob:')) return src;
        if (src.startsWith('http://') || src.startsWith('https://')) {
            if (window.location.protocol === 'https:' && src.startsWith('http://' + window.location.host)) {
                return src.replace('http://', 'https://');
            }
            return src;
        }
        let clean = src.replace(/\\/g, '/').replace(/^\/+/, '');
        clean = clean.replace(/^(storage\/app\/public\/|storage\/|uploads\/|public\/)+/gi, '');
        return window.location.origin + '/uploads/' + clean;
    }

    function handleAssetImgFallback(img, cleanPath) {
        if (!img || !cleanPath) return;
        const origin = window.location.origin;
        if (!img.dataset.fallbackStep) {
            img.dataset.fallbackStep = '1';
            img.src = origin + '/storage/' + cleanPath;
        } else if (img.dataset.fallbackStep === '1') {
            img.dataset.fallbackStep = '2';
            img.src = origin + '/' + cleanPath;
        } else if (img.dataset.fallbackStep === '2') {
            img.dataset.fallbackStep = '3';
            img.src = origin + '/uploads/' + cleanPath;
        }
    }

    function replaceTemplateVariables(text) {
        if (!text) return '';
        let demoName = 'Nama Peserta Demo';
        let winnerTitle = 'Juara 1';
        let winnerCatName = 'Kategori Lomba';

        if (currentSection === 'pemenang' && activeWinnerCatId) {
            const catObj = winnerCategoriesData.find(c => c.id === activeWinnerCatId);
            if (catObj) winnerCatName = catObj.name;
            const catWinners = selectedWinnersByCat[activeWinnerCatId] || {};
            const keys = Object.keys(catWinners);
            if (keys.length > 0) {
                const firstWinner = catWinners[keys[0]];
                demoName = firstWinner.name;
                winnerTitle = firstWinner.winner_title || 'Juara 1';
            }
        }

        const eventTitle = @json($event->title ?? 'Judul Event');
        const dateStr = @json($event->event_date ? $event->event_date->format('d F Y') : now()->format('d F Y'));
        const certNo = '001/AKD10/AKD-BPA/2026';

        return text
            .replace(/\{\{nama\}\}/g, demoName)
            .replace(/\{\{event\}\}/g, eventTitle)
            .replace(/\{\{course\}\}/g, eventTitle)
            .replace(/\{\{tanggal\}\}/g, dateStr)
            .replace(/\{\{nomor_sertifikat\}\}/g, certNo)
            .replace(/\{\{juara\}\}/g, winnerTitle)
            .replace(/\{\{predikat\}\}/g, winnerTitle)
            .replace(/\{\{pemenang\}\}/g, winnerTitle)
            .replace(/\{\{kategori\}\}/g, winnerCatName);
    }

    function renderCustomPreview(templateData) {
        const container = document.getElementById('preview-custom-page');
        if (!container || !templateData) return;

        container.innerHTML = '';

        const bg = templateData.background || {};
        if (bg.gradient) {
            container.style.background = bg.gradient;
        } else {
            container.style.background = bg.color || '#ffffff';
        }

        if (bg.image) {
            const bgImg = document.createElement('img');
            bgImg.src = bg.image;
            bgImg.style.cssText = 'position:absolute; left:0; top:0; width:100%; height:100%; z-index:1; pointer-events:none; display:block;';
            container.appendChild(bgImg);
        }

        const elements = templateData.elements || [];
        elements.forEach(el => {
            const div = document.createElement('div');
            div.style.position = 'absolute';
            div.style.left = (el.x || 0) + 'px';
            div.style.top = (el.y || 0) + 'px';
            div.style.zIndex = el.zIndex || 1;
            div.boxSizing = 'border-box';

            if (el.width) div.style.width = el.width + 'px';
            if (el.height) div.style.height = el.height + 'px';

            if (el.type === 'text' || el.type === 'variable') {
                div.style.fontFamily = (el.fontFamily || 'Helvetica') + ', sans-serif';
                div.style.fontSize = (el.fontSize || 14) + 'px';
                div.style.color = el.color || '#1e293b';
                div.style.textAlign = el.align || 'left';
                div.style.fontWeight = el.bold ? 'bold' : 'normal';
                div.style.fontStyle = el.italic ? 'italic' : 'normal';
                div.style.textDecoration = el.underline ? 'underline' : 'none';
                div.style.whiteSpace = 'pre-wrap';
                div.style.lineHeight = '1.2';
                div.innerHTML = replaceTemplateVariables(el.content || '');
            }
            else if (el.type === 'logo' || el.type === 'shape') {
                const imgUrl = resolveAssetUrl(el.src, el.base64);
                const cleanPath = (el.src || '').replace(/\\/g, '/').replace(/^\/+/, '').replace(/^(storage\/app\/public\/|storage\/|uploads\/|public\/)+/gi, '');
                div.innerHTML = `<img src="${imgUrl}" onerror="handleAssetImgFallback(this, '${cleanPath}')" style="width:100%; height:100%; display:block; pointer-events:none;">`;
            }
            else if (el.type === 'signature') {
                const sigUrl = resolveAssetUrl(el.src, el.base64);
                const cleanPath = (el.src || '').replace(/\\/g, '/').replace(/^\/+/, '').replace(/^(storage\/app\/public\/|storage\/|uploads\/|public\/)+/gi, '');
                let imgHtml = '<div style="height:55px;"></div>';
                if (sigUrl) {
                    imgHtml = `<img src="${sigUrl}" onerror="handleAssetImgFallback(this, '${cleanPath}')" style="height:55px; width:auto; display:block; margin:0 auto 2px; object-fit:contain; pointer-events:none;">`;
                }
                div.style.fontFamily = 'Helvetica, sans-serif';
                div.style.textAlign = 'center';
                div.innerHTML = `
                    ${imgHtml}
                    <div style="width:90%; border-bottom:1.5px solid #000; margin:2px auto;"></div>
                    <div style="font-size:11px; font-weight:bold; color:#0f172a; margin-top:2px;">${el.name || 'Authorized Signee'}</div>
                    <div style="font-size:9px; color:#64748b; font-style:italic;">${el.position || 'Authorized Position'}</div>
                `;
            }
            else if (el.type === 'box') {
                div.style.background = el.bgColor || 'transparent';
                div.style.border = `${el.borderWidth || 0}px ${el.borderStyle || 'solid'} ${el.borderColor || '#000'}`;
                div.style.borderRadius = `${el.borderRadius || 0}px`;
            }

            container.appendChild(div);
        });
    }

    // ── Main Interactive Preview Engine ──
    function renderPreview() {
        let activeTpl = null;
        let isCustom = false;

        const catPicker = document.getElementById('preview-cat-picker-container');
        if (catPicker) {
            catPicker.style.display = (currentSection === 'pemenang') ? 'block' : 'none';
        }

        if (currentSection === 'pemenang') {
            const catObj = winnerCategoriesData.find(c => c.id === activeWinnerCatId);
            activeTpl = catObj ? catObj.certificate_custom_template : null;
            const hasCustom = activeTpl && activeTpl.elements && activeTpl.elements.length > 0;
            const catMode = categoryPreviewMode[activeWinnerCatId] || (hasCustom ? 'custom' : 'standard');
            isCustom = hasCustom && (catMode === 'custom');

            const modeSwitch = document.getElementById('preview-mode-switch');
            const badgeCustom = document.getElementById('badge-custom-active');
            if (modeSwitch) modeSwitch.style.display = hasCustom ? 'inline-flex' : 'none';
            if (badgeCustom) badgeCustom.style.display = isCustom ? 'inline-flex' : 'none';
            const btnCustom = document.getElementById('btn-mode-custom');
            const btnStandard = document.getElementById('btn-mode-standard');
            if (btnCustom && btnStandard) {
                btnCustom.classList.toggle('active', isCustom);
                btnStandard.classList.toggle('active', !isCustom);
            }
        } else {
            activeTpl = customTemplates[currentSection];
            const hasCustom = activeTpl && activeTpl.elements && activeTpl.elements.length > 0;
            isCustom = hasCustom && (previewMode[currentSection] === 'custom');

            const modeSwitch = document.getElementById('preview-mode-switch');
            const badgeCustom = document.getElementById('badge-custom-active');
            if (modeSwitch) modeSwitch.style.display = hasCustom ? 'inline-flex' : 'none';
            if (badgeCustom) badgeCustom.style.display = isCustom ? 'inline-flex' : 'none';
            const btnCustom = document.getElementById('btn-mode-custom');
            const btnStandard = document.getElementById('btn-mode-standard');
            if (btnCustom && btnStandard) {
                btnCustom.classList.toggle('active', isCustom);
                btnStandard.classList.toggle('active', !isCustom);
            }
        }

        const standardPage = document.getElementById('preview-cert-page');
        const customPage = document.getElementById('preview-custom-page');

        if (isCustom) {
            if (standardPage) standardPage.style.display = 'none';
            if (customPage) customPage.style.display = 'block';
            renderCustomPreview(activeTpl);
        } else {
            if (customPage) customPage.style.display = 'none';
            if (standardPage) standardPage.style.display = 'block';
            renderStandardPreview();
        }

        scalePreview();
    }

    // ── Standard Template Preview Engine ──
    function renderStandardPreview() {
        let template = 'template_1';
        if (currentSection === 'pemenang') {
            const input = document.getElementById(`selected_template_cat_${activeWinnerCatId}`);
            template = input ? input.value : 'template_1';
        } else if (currentSection === 'tidak_lolos') {
            const input = document.getElementById('selected_template_tidak_lolos');
            template = input ? input.value : 'template_1';
        } else {
            const input = document.getElementById('selected_template_lolos');
            template = input ? input.value : 'template_1';
        }
        
        const page = document.getElementById('preview-cert-page');
        if (!page) return;

        page.className = 'certificate-page ' + template;

        const decor1 = document.querySelector('.template-decorations-1');
        const decor2 = document.querySelector('.template-decorations-2');
        const decor3 = document.querySelector('.template-decorations-3');
        const decor4 = document.querySelector('.template-decorations-4');
        if (decor1) decor1.style.display = (template === 'template_1') ? 'block' : 'none';
        if (decor2) decor2.style.display = (template === 'template_2') ? 'block' : 'none';
        if (decor3) decor3.style.display = (template === 'template_3') ? 'block' : 'none';
        if (decor4) decor4.style.display = (template === 'template_4') ? 'block' : 'none';

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

        const isPemenang = (currentSection === 'pemenang');
        const isLolos = (currentSection === 'lolos');
        
        let displayWinnerTitle = 'JUARA 1';
        let displayDemoName = 'NAMA PESERTA DEMO';
        let displayCategoryName = 'KATEGORI LOMBA';

        if (isPemenang && activeWinnerCatId) {
            const catObj = winnerCategoriesData.find(c => c.id === activeWinnerCatId);
            if (catObj) displayCategoryName = catObj.name;
            const catWinners = selectedWinnersByCat[activeWinnerCatId] || {};
            const keys = Object.keys(catWinners);
            if (keys.length > 0) {
                const firstWinner = catWinners[keys[0]];
                displayDemoName = firstWinner.name;
                displayWinnerTitle = firstWinner.winner_title || 'Juara 1';
            }
        }

        const badgeCertId = document.querySelector('.cert-id');
        if (badgeCertId) {
            let label = isLolos ? 'PESERTA LOLOS' : 'PESERTA TIDAK LOLOS';
            let color = isLolos ? '#059669' : '#64748b';
            if (isPemenang) {
                label = `PEMENANG: ${displayWinnerTitle.toUpperCase()} (${displayCategoryName.toUpperCase()})`;
                color = '#d97706';
            }
            badgeCertId.innerHTML = `Verified Certificate ID: 001/AKD10/AKD-BPA/2026 &bull; <strong style="color:${color}">${label}</strong>`;
        }

        const t4Role = document.getElementById('preview-t4-role');
        if (t4Role) {
            if (isPemenang) {
                t4Role.textContent = `${displayWinnerTitle.toUpperCase()} - ${displayCategoryName.toUpperCase()}`;
            } else {
                t4Role.textContent = isLomba ? (isLolos ? 'PESERTA LOLOS / FINALIS' : 'PESERTA / PARTISIPAN') : 'PESERTA';
            }
        }

        const subtitleT12 = document.getElementById('preview-subtitle-t12');
        if (subtitleT12) {
            subtitleT12.textContent = isPemenang ? 'OF WINNER & EXCELLENCE' : 'OF ACHIEVEMENT';
        }

        const completedText = document.getElementById('preview-completed-text');
        if (completedText) {
            if (isPemenang) {
                completedText.textContent = `telah berhasil meraih prestasi sebagai ${displayWinnerTitle} (${displayCategoryName}) dalam kegiatan`;
            } else if (isLomba) {
                completedText.textContent = isLolos 
                    ? 'telah dinyatakan LOLOS dan menyelesaikan seluruh tahapan kompetisi'
                    : 'atas dedikasi dan partisipasinya dalam kegiatan kompetisi';
            } else {
                completedText.textContent = 'has successfully completed the program';
            }
        }

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
            nameDiv.textContent = (template === 'template_1') ? displayDemoName : displayDemoName.toUpperCase();
        }

        const t4NameDiv = document.getElementById('preview-t4-name');
        if (t4NameDiv) {
            t4NameDiv.textContent = displayDemoName.toUpperCase();
        }

        const assetPath = "{{ asset('aset') }}";
        const mainLogoUrl = (template === 'template_3' || template === 'template_4') ? `${assetPath}/logo-idspora.png` : `${assetPath}/logo idspora_dark.png`;
        
        renderLogosInContainer('.preview-logo-container-t12', mainLogoUrl, 'preview-main-logo-t12', template);
        renderLogosInContainer('.preview-logo-container-t3', mainLogoUrl, 'preview-main-logo-t3', template);
        renderLogosInContainer('.preview-logo-container-t4', mainLogoUrl, 'preview-main-logo-t4', template);

        renderSignatures();
    }

    function renderLogosInContainer(containerSelector, mainLogoUrl, mainLogoId, template) {
        const container = document.querySelector(containerSelector);
        if (!container) return;
        
        container.innerHTML = '';
        
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

        if (currentSection === 'pemenang') {
            const existingContainerId = `existingLogos_cat_${activeWinnerCatId}`;
            const deleteClass = `.delete-logo-input-cat-${activeWinnerCatId}`;

            const existingLogos = document.querySelectorAll(`#${existingContainerId} .asset-item`);
            existingLogos.forEach(item => {
                const deleteInput = item.querySelector(deleteClass);
                if (deleteInput && deleteInput.value === '') {
                    const img = item.querySelector('img');
                    if (img) {
                        const newImg = document.createElement('img');
                        newImg.src = img.src;
                        newImg.className = (template === 'template_4') ? 'logo-item-top' : 'logo-item';
                        container.appendChild(newImg);
                    }
                }
            });

            if (uploadedCategoryFiles[activeWinnerCatId]) {
                const store = uploadedCategoryFiles[activeWinnerCatId].logos;
                Object.keys(store).forEach(key => {
                    if (store[key]) {
                        const newImg = document.createElement('img');
                        newImg.src = store[key];
                        newImg.className = (template === 'template_4') ? 'logo-item-top' : 'logo-item';
                        container.appendChild(newImg);
                    }
                });
            }
        } else {
            const existingContainerId = (currentSection === 'tidak_lolos') ? 'existingLogos_tidak_lolos' : 'existingLogos';
            const deleteClass = (currentSection === 'tidak_lolos') ? '.delete-logo-input-tidak-lolos' : '.delete-logo-input';

            const existingLogos = document.querySelectorAll(`#${existingContainerId} .asset-item`);
            existingLogos.forEach(item => {
                const deleteInput = item.querySelector(deleteClass);
                if (deleteInput && deleteInput.value === '') {
                    const img = item.querySelector('img');
                    if (img) {
                        const newImg = document.createElement('img');
                        newImg.src = img.src;
                        newImg.className = (template === 'template_4') ? 'logo-item-top' : 'logo-item';
                        container.appendChild(newImg);
                    }
                }
            });

            const store = uploadedFiles[currentSection].logos;
            Object.keys(store).forEach(key => {
                if (store[key]) {
                    const newImg = document.createElement('img');
                    newImg.src = store[key];
                    newImg.className = (template === 'template_4') ? 'logo-item-top' : 'logo-item';
                    container.appendChild(newImg);
                }
            });
        }
    }

    function renderSignatures() {
        const container = document.getElementById('preview-signatures-container');
        if (!container) return;
        container.innerHTML = '';

        let template = 'template_1';
        let containerId = '';
        let deleteClass = '';

        if (currentSection === 'pemenang') {
            const tplInput = document.getElementById(`selected_template_cat_${activeWinnerCatId}`);
            template = tplInput ? tplInput.value : 'template_1';
            containerId = `signaturesContainer_cat_${activeWinnerCatId}`;
            deleteClass = `.delete-sig-input-cat-${activeWinnerCatId}`;
        } else if (currentSection === 'tidak_lolos') {
            const tplInput = document.getElementById('selected_template_tidak_lolos');
            template = tplInput ? tplInput.value : 'template_1';
            containerId = 'signaturesContainer_tidak_lolos';
            deleteClass = '.delete-sig-input-tidak-lolos';
        } else {
            const tplInput = document.getElementById('selected_template_lolos');
            template = tplInput ? tplInput.value : 'template_1';
            containerId = 'signaturesContainer';
            deleteClass = '.delete-sig-input';
        }

        const entries = document.querySelectorAll(`#${containerId} .sig-entry`);
        entries.forEach((entry, index) => {
            const deleteInput = entry.querySelector(deleteClass);
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

            let imgSrc = '';
            const replaceCheckbox = entry.querySelector('.sig-replace-checkbox');
            const isReplaced = replaceCheckbox ? replaceCheckbox.checked : false;
            
            const existingInput = entry.querySelector('.existing-sig-path');
            const existingPath = existingInput ? existingInput.value : '';

            if (existingPath && !isReplaced) {
                imgSrc = "{{ asset('uploads') }}/" + existingPath;
            } else if (currentSection === 'pemenang' && uploadedCategoryFiles[activeWinnerCatId] && uploadedCategoryFiles[activeWinnerCatId].signatures[idx]) {
                imgSrc = uploadedCategoryFiles[activeWinnerCatId].signatures[idx];
            } else if (currentSection !== 'pemenang' && uploadedFiles[currentSection].signatures[idx]) {
                imgSrc = uploadedFiles[currentSection].signatures[idx];
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
                let imgHtml = '<div style="height: 50px;"></div>';
                if (imgSrc) {
                    imgHtml = `<img src="${imgSrc}" style="height: 50px; width: auto; display: block; margin: 0 auto; object-fit: contain;">`;
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
        const aspect = document.getElementById('cert-preview-aspect');

        let isCustom = false;
        if (currentSection === 'pemenang') {
            const catObj = winnerCategoriesData.find(c => c.id === activeWinnerCatId);
            const activeTpl = catObj ? catObj.certificate_custom_template : null;
            const hasCustom = activeTpl && activeTpl.elements && activeTpl.elements.length > 0;
            const catMode = categoryPreviewMode[activeWinnerCatId] || (hasCustom ? 'custom' : 'standard');
            isCustom = hasCustom && (catMode === 'custom');
        } else {
            const activeTpl = customTemplates[currentSection];
            const hasCustom = activeTpl && activeTpl.elements && activeTpl.elements.length > 0;
            isCustom = hasCustom && (previewMode[currentSection] === 'custom');
        }

        const containerW = container.offsetWidth;
        const certNaturalW = isCustom ? 1000 : 1020;
        const certNaturalH = isCustom ? 706 : 642;
        const scale = containerW / certNaturalW;

        scaler.style.width = certNaturalW + 'px';
        scaler.style.height = certNaturalH + 'px';
        scaler.style.transform = 'scale(' + scale + ')';
        container.style.height = (scale * certNaturalH) + 'px';
        if (aspect) {
            aspect.style.paddingTop = isCustom ? '70.6%' : '62.96%';
        }
    }

    // ── Initialize Page ──
    document.addEventListener('DOMContentLoaded', () => {
        checkLogoCount('lolos');
        if (isLomba) {
            checkLogoCount('tidak_lolos');
            winnerCategoriesData.forEach(cat => {
                renderSelectedWinnersForCategory(cat.id);
                checkCategoryLogoCount(cat.id);
            });
        }
        renderPreview();
        
        window.addEventListener('resize', scalePreview);

        if (window.location.hash === '#pemenang') {
            switchSection('pemenang');
        }

        document.addEventListener('click', (e) => {
            document.querySelectorAll('.winner-search-dropdown').forEach(dropdown => {
                const searchField = dropdown.closest('.position-relative')?.querySelector('.winner-search-field');
                if (searchField && !searchField.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection