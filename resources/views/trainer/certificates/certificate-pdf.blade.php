@if(!isset($is_preview) || !$is_preview)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Trainer</title>
@endif
<style>
    @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');
    
    @if(!isset($is_preview) || !$is_preview)
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact; }
        body { margin: 0; padding: 0; font-family: 'Helvetica', 'Arial', sans-serif; background: #fff; }
    @endif

    .certificate-page {
        width: 29.7cm;
        height: 21cm;
        position: relative;
        overflow: hidden;
        background: #fff;
        color: #1e293b;
        box-sizing: border-box;
        @if(isset($is_preview) && $is_preview)
        transform: scale(var(--cert-scale, 1));
        transform-origin: top left;
        --cert-font-scale: 0.82;
        @endif
    }

    /* ─── Shared Styles across Templates ─── */
    .logo-row {
        text-align: center;
        margin-bottom: 15px;
        width: 100%;
        position: relative;
        z-index: 5;
    }
    .logo-item {
        height: 48px;
        width: auto;
        margin: 0 10px;
        vertical-align: middle;
        object-fit: contain;
    }

    .cert-title {
        font-family: 'Georgia', serif;
        text-transform: uppercase;
        font-weight: 700;
        margin: 10px 0 2px;
        text-align: center;
    }

    .sub-title {
        font-family: 'Helvetica', sans-serif;
        font-weight: bold;
        text-transform: uppercase;
        margin-top: 4px;
        text-align: center;
    }

    .presented-text {
        font-size: 14px;
        color: #64748b;
        font-style: italic;
        margin-bottom: 5px;
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .recipient-name {
        font-family: 'Great Vibes', 'Georgia', serif;
        font-weight: normal;
        text-align: center;
        display: inline-block;
        letter-spacing: 1px;
        position: relative;
        z-index: 2;
    }

    .cert-desc {
        text-align: center;
        font-size: 13px;
        line-height: 1.65;
        color: #1f2937;
        margin-top: 10px;
        position: relative;
        z-index: 2;
    }

    .cert-footer {
        position: absolute;
        bottom: 70px;
        width: 100%;
        left: 0;
        padding: 0 70px;
        box-sizing: border-box;
        z-index: 3;
    }

    .sig-container {
        text-align: center;
        width: 100%;
    }

    .sig-box {
        display: inline-block;
        text-align: center;
        width: 220px;
        margin: 0 24px;
        vertical-align: bottom;
    }

    .sig-box img {
        max-height: 65px;
        max-width: 140px;
        object-fit: contain;
        display: block;
        margin: 0 auto 4px;
    }

    .sig-line {
        width: 170px;
        margin: 8px auto;
        border-bottom: 1.5px solid #0f172a;
    }

    .cert-id {
        position: absolute;
        bottom: 25px;
        right: 40px;
        font-size: 8.5pt;
        color: #94a3b8;
        font-weight: 600;
        z-index: 3;
        background: rgba(251, 191, 36, 0.1);
        padding: 5px 10px;
        border-radius: 4px;
    }

    .verification-tag {
        position: absolute;
        bottom: 25px;
        left: 40px;
        font-size: 7.5pt;
        color: #94a3b8;
        font-family: monospace;
        letter-spacing: 1.5px;
        font-weight: 600;
        z-index: 3;
    }

    /* ─── Template 1: Premium Royal (Maroon & Gold Waves) ─── */
    .template_1 {
        padding: 35px;
        background: #ffffff;
    }
    .template_1 .content-wrap {
        padding-top: 40px;
        text-align: center;
        position: relative;
        z-index: 2;
    }
    .template_1 .cert-title {
        font-size: 36pt;
        color: #1e1b4b;
        letter-spacing: 4px;
    }
    .template_1 .sub-title {
        font-size: 11pt;
        color: #7f1d1d;
        letter-spacing: 5px;
        margin-bottom: 25px;
    }
    .template_1 .recipient-name {
        font-size: 38pt;
        color: #7f1d1d;
        margin: 10px 0;
    }
    .template_1 .sig-line {
        border-bottom-style: dashed;
        border-bottom-color: #7f1d1d;
    }

    /* ─── Template 2: Modern Corporate (Sleek Ribbon & Navy Corners) ─── */
    .template_2 {
        padding: 0;
        background: #f8fafc;
    }
    .template_2 .content-wrap {
        padding: 60px 20px 20px 20px;
        text-align: center;
        position: relative;
        z-index: 2;
    }
    .template_2 .cert-title {
        font-size: 32pt;
        color: #0f172a;
        letter-spacing: 2px;
    }
    .template_2 .sub-title {
        font-size: 13pt;
        color: #475569;
        letter-spacing: 4px;
        margin-bottom: 25px;
    }
    .template_2 .recipient-name {
        font-size: 38pt;
        color: #0f172a;
        margin: 15px auto;
        border-bottom: 2px solid #0f172a;
        padding-bottom: 5px;
    }
    .template_2 .gold-badge {
        position: absolute;
        top: 25px;
        left: 25px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 4px solid #d4af37;
        background: #ffffff;
        z-index: 5;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .template_2 .gold-badge-inner {
        position: absolute;
        top: 5px; left: 5px; right: 5px; bottom: 5px;
        border-radius: 50%;
        border: 1px solid #d4af37;
        background: #faf8f5;
    }
    .template_2 .cert-footer {
        padding: 0 70px 0 160px;
    }
    .template_2 .verification-tag {
        left: 160px;
    }

    /* ─── Template 3: Creative Professional (Dynamic Wave & Gradients) ─── */
    .template_3 {
        padding: 0;
        background: #fdfdfd;
        border: 15px solid #ffffff;
    }
    .template_3 .content-wrap {
        padding: 50px 20px 20px 20px;
        text-align: center;
        position: relative;
        z-index: 5;
    }
    .template_3 .cert-title {
        font-size: 32pt;
        color: #1e1b4b;
        letter-spacing: 3px;
    }
    .template_3 .sub-title {
        font-size: 11pt;
        color: #d97706;
        letter-spacing: 5px;
        margin-bottom: 25px;
    }
    .template_3 .recipient-name {
        font-size: 38pt;
        color: #4c1d95;
        margin: 10px auto;
        border-bottom: 2px solid #d97706;
        padding-bottom: 5px;
    }
    .template_3 .sig-box {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(226, 232, 240, 0.8);
        padding: 8px 16px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    }
    .template_3 .sig-line {
        border-bottom-color: #4c1d95;
    }
    .template_3 .verification-tag {
        left: 70px;
    }
    .template_3 .cert-id {
        right: 70px;
    }

    /* ─── Template 4: Blue Shield (CRM) ─── */
    .template_4 {
        width: 29.7cm;
        height: 21cm;
        background: #ffffff !important;
        position: relative;
        overflow: hidden;
        padding: 0;
    }
    .template_4 .bg-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 29.7cm;
        height: 21cm;
        z-index: 1;
    }
    .template_4 .logo-banner-container {
        position: absolute;
        top: 0;
        left: 28%;
        width: 44%;
        background-color: #ffffff;
        border-radius: 0 0 15px 15px;
        padding: 8px 20px;
        text-align: center;
        z-index: 10;
    }
    .template_4 .logo-poster-img {
        height: 45px;
        width: auto;
        vertical-align: middle;
        display: inline-block;
    }
    .template_4 .logo-item-top {
        height: 38px;
        width: auto;
        vertical-align: middle;
        display: inline-block;
        margin: 0 5px;
    }
    .template_4 .content-blue {
        position: absolute;
        top: 45mm;
        left: 0;
        width: 29.7cm;
        text-align: center;
        z-index: 5;
        padding: 0 15mm;
        box-sizing: border-box;
        color: #ffffff;
        font-family: Arial, Helvetica, sans-serif;
    }
    .template_4 .recipient-underline {
        width: 180mm;
        height: 1.5px;
        background-color: #ffffff;
        margin: 2mm auto 4mm auto;
    }
    .template_4 .cert-footer {
        position: absolute;
        bottom: 15mm;
        left: 20mm;
        right: 20mm;
        text-align: center;
        width: auto;
        z-index: 6;
        padding: 0;
    }
    .template_4 .sig-box {
        display: inline-block;
        vertical-align: bottom;
        text-align: center;
        width: 80mm;
        margin: 0 10mm;
    }
    .template_4 .sig-position {
        font-weight: bold;
        margin: 0 0 1mm 0;
        font-size: 8pt;
        color: #1a1a1a;
        font-family: Arial, Helvetica, sans-serif;
    }
    .template_4 .sig-image-wrap {
        height: 55px;
        margin: 1mm auto;
        text-align: center;
    }
    .template_4 .sig-img {
        height: 55px;
        width: auto;
        display: block;
        margin: 0 auto;
        object-fit: contain;
    }
    .template_4 .sig-line {
        width: 65mm;
        border-bottom: 1.5px solid #1a1a1a;
        margin: 2px auto;
    }
    .template_4 .sig-name {
        font-weight: bold;
        margin: 1.5mm 0 0 0;
        font-size: 8.5pt;
        color: #1a1a1a;
        font-family: Arial, Helvetica, sans-serif;
    }
    .template_4 .verification-tag {
        left: 20mm;
        bottom: 5mm;
        color: #94a3b8;
    }
    .template_4 .cert-id {
        right: 20mm;
        bottom: 5mm;
        color: #94a3b8;
        background: rgba(251, 191, 36, 0.1);
        padding: 3px 6px;
        border-radius: 3px;
    }

    @media (max-width: 768px) {
        .recipient-name {
            font-size: 26pt !important;
        }
        .cert-title {
            font-size: 24pt !important;
        }
    }
</style>
@if(!isset($is_preview) || !$is_preview)
    </head>
    <body>
@endif

@php
    $template   = $template ?? 'template_1';
    $trainerName = $user->full_name_with_title ?? $user->name ?? 'Trainer';
    $title      = $context === 'event' ? ($event->title ?? 'EVENT') : ($course->name ?? 'COURSE');
    $issuedDateText = ($issuedAt ?? now())->translatedFormat('d M Y');
    $certNum    = $certificateNumber ?? '';
    $role       = $roleLabel ?? 'Narasumber';

    // Build logo list: logo idSpora + logos dari admin
    $idSporaLogoPath = public_path('aset/logo idspora_dark.png');
    if (!file_exists($idSporaLogoPath)) {
        $idSporaLogoPath = public_path('aset/logo-idspora.png');
    }
    $idSporaLogoB64  = file_exists($idSporaLogoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($idSporaLogoPath))
        : null;

    $allLogos = [];
    if ($idSporaLogoB64) {
        $allLogos[] = $idSporaLogoB64;
    }
    foreach (($logosBase64 ?? []) as $lb) {
        $allLogos[] = $lb;
    }

    $bgBlueShieldPath = public_path('aset/bg-blue-shield.png');
    $logoPosterPath = public_path('aset/logo poster.png');
@endphp

@if($template === 'template_4')
<div class="certificate-page template_4">
    @if(file_exists($bgBlueShieldPath))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($bgBlueShieldPath)) }}" class="bg-image" alt="">
    @endif

    <div class="logo-banner-container">
        @if(file_exists($logoPosterPath))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPosterPath)) }}" class="logo-poster-img" alt="">
        @endif
        @foreach(array_slice($allLogos, 0, 2) as $logo)
            <img src="{{ $logo }}" class="logo-item-top" alt="Logo">
        @endforeach
    </div>

    <div class="content-blue">
        <h1 style="font-size: 26pt; font-weight: 900; margin: 0; letter-spacing: 3px;">SERTIFIKAT</h1>
        <p style="font-size: 9pt; font-weight: bold; letter-spacing: 4px; margin: 3mm 0 1mm 0;">DIBERIKAN KEPADA</p>
        <div style="font-size: 24pt; font-weight: bold; margin: 4mm 0 1mm 0;">{{ strtoupper($trainerName) }}</div>
        <div class="recipient-underline"></div>
        <p style="font-size: 9pt; margin: 2mm 0 1mm 0;">Atas Kontribusinya Sebagai</p>
        <p style="font-size: 14pt; font-weight: bold; margin: 1mm 0 2mm 0;">{{ strtoupper($role) }}</p>
        <p style="font-size: 9pt; margin: 2mm 0 1mm 0;">Dalam Program</p>
        <h2 style="font-size: 16pt; font-weight: bold; margin: 1mm 0 1mm 0;">"{{ $title }}"</h2>
        <p style="font-size: 8.5pt; margin: 3mm 0 0 0;">
            Diterbitkan pada <strong>{{ $issuedDateText }}</strong>
        </p>
    </div>

    <div class="cert-footer">
        @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64 ?? []); @endphp
        @forelse($sigsToRender as $sig)
            <div class="sig-box">
                <p class="sig-position">{{ $sig['position'] ?? 'Authorized Position' }}</p>
                <div class="sig-image-wrap">
                    @if(!empty($sig['base64']))
                        <img src="{{ $sig['base64'] }}" class="sig-img" alt="">
                    @endif
                </div>
                <div class="sig-line"></div>
                <p class="sig-name">{{ $sig['name'] ?? 'Authorized Signature' }}</p>
            </div>
        @empty
            <div class="sig-box">
                <p class="sig-position">Authorized Position</p>
                <div class="sig-image-wrap"><div style="height:55px;"></div></div>
                <div class="sig-line"></div>
                <p class="sig-name">Authorized Signature</p>
            </div>
        @endforelse
    </div>

    <div class="verification-tag">VERIFIED BY IDSPORA.COM</div>
    <div class="cert-id">ID: {{ $certNum }}</div>
</div>
@else
<div class="certificate-page {{ $template }}">

    {{-- ═══ BACKGROUND DECORATIONS FOR TEMPLATE 1 ═══ --}}
    @if($template === 'template_1')
        <!-- Top Left Gold Bar -->
        <div style="position: absolute; top: 15mm; left: 15mm; width: 140mm; height: 4px; background: #eab308; z-index: 2;"></div>
        <!-- Bottom Right Gold Bar -->
        <div style="position: absolute; bottom: 15mm; right: 15mm; width: 140mm; height: 4px; background: #eab308; z-index: 2;"></div>

        <!-- Top Right Maroon & Gold Waves -->
        <div style="position: absolute; top: 0; right: 0; width: 120mm; height: 120mm; z-index: 1; pointer-events: none;">
            <svg width="120mm" height="120mm" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M 30,0 C 50,40 70,60 100,80 L 100,0 Z" fill="#7f1d1d" />
                <path d="M 40,0 C 58,38 74,54 100,70 L 100,0 Z" fill="#eab308" />
                <path d="M 50,0 C 66,34 78,46 100,60 L 100,0 Z" fill="#991b1b" />
                <path d="M 65,0 C 78,26 86,34 100,45 L 100,0 Z" fill="#eab308" />
                <path d="M 75,0 C 85,20 90,25 100,35 L 100,0 Z" fill="#7f1d1d" />
            </svg>
        </div>

        <!-- Bottom Left Maroon & Gold Waves -->
        <div style="position: absolute; bottom: 0; left: 0; width: 120mm; height: 120mm; z-index: 1; pointer-events: none;">
            <svg width="120mm" height="120mm" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M 0,30 C 40,50 60,70 80,100 L 0,100 Z" fill="#7f1d1d" />
                <path d="M 0,40 C 38,58 54,74 70,100 L 0,100 Z" fill="#eab308" />
                <path d="M 0,50 C 34,66 46,78 60,100 L 0,100 Z" fill="#991b1b" />
                <path d="M 0,65 C 26,78 34,86 45,100 L 0,100 Z" fill="#eab308" />
                <path d="M 0,75 C 20,85 25,90 35,100 L 0,100 Z" fill="#7f1d1d" />
            </svg>
        </div>

    {{-- ═══ BACKGROUND DECORATIONS FOR TEMPLATE 2 ═══ --}}
    @elseif($template === 'template_2')
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

    {{-- ═══ BACKGROUND DECORATIONS FOR TEMPLATE 3 ═══ --}}
    @elseif($template === 'template_3')
        @php 
            $bgCreativeUrl = request()->schemeAndHttpHost() . '/aset/bg-creative.png';
        @endphp
        <img src="{{ $bgCreativeUrl }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
    @endif

    {{-- ═══ CONTENT WRAPPER ═══ --}}
    <div class="content-wrap">
        <div class="logo-row">
            @foreach(array_slice($allLogos, 0, 4) as $logo)
                <img class="logo-item" src="{{ $logo }}" alt="Logo">
            @endforeach
        </div>

        <h1 class="cert-title">Sertifikat Penghargaan</h1>
        <div class="sub-title">{{ strtoupper($role) }}</div>

        <p class="presented-text">Diberikan kepada:</p>
        <div class="recipient-name">{{ $trainerName }}</div>

        <p class="cert-desc">
            Atas kontribusinya sebagai {{ strtolower($role) }} dalam <strong>{{ $title }}</strong>, diterbitkan pada {{ $issuedDateText }}.
        </p>
    </div>

    {{-- ═══ SIGNATURE FOOTER ═══ --}}
    <div class="cert-footer">
        <div class="sig-container">
            @php 
                $sigsToRender = !empty($signaturesData) 
                    ? $signaturesData 
                    : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64 ?? []); 
            @endphp
            @forelse(array_slice($sigsToRender, 0, 3) as $sig)
                <div class="sig-box">
                    @if(!empty($sig['base64']))
                        <img src="{{ $sig['base64'] }}" alt="Tanda Tangan">
                    @endif
                    <div class="sig-line"></div>
                    @if(!empty($sig['name']))
                        <p style="font-weight: bold; margin: 0; font-size: 11pt; color: #1e1b4b;">{{ $sig['name'] }}</p>
                        @if(!empty($sig['position']))
                            <p style="margin: 2px 0 0; font-size: 9pt; color: #64748b; font-style: italic;">{{ $sig['position'] }}</p>
                        @endif
                    @else
                        <p style="font-weight: bold; margin: 0; font-size: 11pt; color: #1e1b4b;">Authorized Signature</p>
                    @endif
                </div>
            @empty
                <div class="sig-box">
                    <div style="height: 65px;"></div>
                    <div class="sig-line"></div>
                    <p style="font-weight: bold; margin: 0; font-size: 11pt; color: #1e1b4b;">Authorized Signature</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══ VERIFICATION INFO ═══ --}}
    <div class="verification-tag">VERIFIED BY IDSPORA.COM</div>
    <div class="cert-id">Verified Certificate ID: {{ $certNum }}</div>

</div>
@endif

@if(!isset($is_preview) || !$is_preview)
</body>
</html>
@endif
