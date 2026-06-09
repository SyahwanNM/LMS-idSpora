@php
    $template = $course->certificate_template ?? 'template_1';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kursus</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        html, body {
            margin: 0;
            padding: 0;
            background: white;
            font-family: 'Helvetica', 'Arial', sans-serif;
            width: 297mm;
            @if($template != 'template_4')
            height: 210mm;
            overflow: hidden;
            @endif
        }
        .certificate-page {
            width: 297mm;
            height: 210mm;
            position: relative;
            background: white;
            text-align: left;
            overflow: hidden;
        }

        /* Curriculum Table styling for Template 4 Page 2 */
        .template_4 .curriculum-table {
            width: 85%;
            margin: 5px auto;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            z-index: 10;
            position: relative;
        }
        .template_4 .curriculum-table th {
            background-color: #7cc2f7;
            color: #000000;
            border: 1.5px solid #000000;
            padding: 8px 6px;
            font-weight: bold;
            text-align: center;
        }
        .template_4 .curriculum-table td {
            background-color: #faf9f6;
            color: #000000;
            border: 1.5px solid #000000;
            padding: 6px 12px;
            font-weight: bold;
        }
        .template_4 .curriculum-table td.center {
            text-align: center;
        }

        .template_4.page-1 {
            page-break-after: always !important;
        }
        .template_4.page-2 {
            page-break-after: avoid !important;
        }

        /* --- BASE COMPONENTS --- */
        .logo-row {
            text-align: center;
            margin-bottom: 5px;
            width: 100%;
        }
        .logo-container {
            display: inline-block;
            vertical-align: middle;
        }
        .logo-item {
            height: 32px;
            width: auto;
            margin: 0 4px;
            vertical-align: middle;
        }
        .cert-footer {
            position: absolute;
            bottom: 15mm;
            right: 15mm;
            z-index: 5;
        }
        .sig-box {
            display: inline-block;
            text-align: center;
            width: 140px;
            margin-left: 15px;
            vertical-align: top;
        }
        .sig-line {
            width: 140px;
            border-bottom: 1.5px solid #0f172a;
            margin: 5px auto;
        }
        .cert-id {
            position: absolute;
            bottom: 6mm;
            right: 10mm;
            font-size: 7.5pt;
            color: #94a3b8;
            font-weight: bold;
        }
        .verification-tag {
            position: absolute;
            bottom: 6mm;
            left: 10mm;
            font-size: 7pt;
            color: #94a3b8;
            font-family: monospace;
            letter-spacing: 1px;
            font-weight: bold;
        }

        /* --- TEMPLATE 1: PREMIUM ROYAL --- */
        .template_1 {
            width: 297mm;
            height: 210mm;
            background: #ffffff !important;
            position: relative;
            overflow: hidden;
        }
        .template_1 .cert-inner {
            width: 297mm;
            height: 210mm;
            background: transparent;
            position: absolute;
            top: 0;
            left: 0;
            padding: 20mm 15mm 10mm 15mm;
            border: none;
            z-index: 2;
        }
        .template_1 .header {
            text-align: center;
            padding-top: 10mm;
        }
        .template_1 h1 {
            font-family: 'Georgia', serif;
            font-size: 28pt;
            color: #1e1b4b;
            margin: 6px 0 2px;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: bold;
        }
        .template_1 .sub-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 10pt;
            color: #7f1d1d;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-top: 3px;
            margin-bottom: 15px;
        }
        .template_1 .recipient-name {
            font-family: 'Great Vibes', 'Georgia', serif;
            font-size: 32pt;
            font-weight: normal;
            color: #7f1d1d;
            border-bottom: none;
            display: inline-block;
            padding: 4px 30px;
            margin: 8px 0;
        }
        .template_1 .content {
            text-align: center;
        }
        .template_1 .cert-footer {
            position: absolute;
            bottom: 18mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            width: auto;
            z-index: 5;
            padding: 0;
        }
        .template_1 .sig-box {
            display: inline-block;
            vertical-align: top;
            text-align: center;
            width: 70mm;
            margin: 0 10mm;
        }
        .template_1 .sig-line {
            width: 50mm;
            border-bottom: 1.5px dashed #7f1d1d;
            margin: 6px auto;
        }
        .template_1 .verification-tag {
            left: 20mm;
            bottom: 10mm;
        }
        .template_1 .cert-id {
            right: 20mm;
            bottom: 10mm;
        }

        /* --- TEMPLATE 2: MODERN CORPORATE --- */
        .template_2 {
            width: 297mm;
            height: 210mm;
            background: #f8fafc !important;
            position: relative;
            overflow: hidden;
        }
        .template_2 .cert-border {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border: 1px solid #e2e8f0;
            pointer-events: none;
            z-index: 10;
        }
        .template_2 .content-wrap {
            width: 217mm;  /* 297mm - 80mm padding */
            margin: 0 auto;
            padding: 20mm 10mm 10mm 10mm;
            text-align: center;
            position: relative;
            z-index: 2;
        }
        .template_2 h1 {
            font-family: 'Georgia', serif;
            font-size: 32pt;
            font-weight: bold;
            color: #0f172a;
            margin: 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .template_2 .sub-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 13pt;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-top: 5px;
            margin-bottom: 15px;
        }
        .template_2 .recipient-name {
            font-family: 'Great Vibes', 'Georgia', serif;
            font-size: 34pt;
            font-weight: normal;
            color: #0f172a;
            margin: 15px auto;
            display: inline-block;
            font-style: italic;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 5px;
        }
        .template_2 .gold-badge {
            position: absolute;
            top: 6mm;
            left: 6mm;
            width: 24mm;
            height: 24mm;
            border-radius: 50%;
            border: 4px solid #d4af37;
            background: #ffffff;
            z-index: 5;
        }
        .template_2 .gold-badge-inner {
            position: absolute;
            top: 1.5mm; left: 1.5mm; right: 1.5mm; bottom: 1.5mm;
            border-radius: 50%;
            border: 1px solid #d4af37;
            background: #faf8f5;
        }
        .template_2 .cert-footer {
            position: absolute;
            bottom: 15mm;
            left: 45mm;
            right: auto;
            width: 207mm;
            padding: 0;
            text-align: center;
            z-index: 5;
        }
        .template_2 .sig-box {
            display: inline-block;
            text-align: center;
            width: 80mm;
            margin: 0 10mm;
            vertical-align: top;
        }
        .template_2 .verification-tag {
            left: 45mm;
            bottom: 10mm;
        }
        .template_2 .cert-id {
            right: 20mm;
            bottom: 10mm;
        }

        /* Template 2 Background Ornaments (CSS Triangles for Dompdf compatibility) */
        .template_2 .decor-tl-1 {
            position: absolute;
            top: 0; left: 0;
            width: 0; height: 0;
            border-top: 60mm solid #d4af37;
            border-right: 60mm solid transparent;
            z-index: 1;
        }
        .template_2 .decor-tl-2 {
            position: absolute;
            top: 0; left: 0;
            width: 0; height: 0;
            border-top: 55mm solid #fef08a;
            border-right: 55mm solid transparent;
            z-index: 2;
        }
        .template_2 .decor-tl-3 {
            position: absolute;
            top: 0; left: 0;
            width: 0; height: 0;
            border-top: 40mm solid #ca8a04;
            border-right: 40mm solid transparent;
            z-index: 3;
        }
        .template_2 .decor-tr-navy {
            position: absolute;
            top: 0; right: 0;
            width: 0; height: 0;
            border-top: 125mm solid #0f172a;
            border-left: 82mm solid transparent;
            z-index: 1;
        }
        .template_2 .decor-br-gold-1 {
            position: absolute;
            bottom: 0; right: 0;
            width: 0; height: 0;
            border-bottom: 75mm solid #ca8a04;
            border-left: 112mm solid transparent;
            z-index: 2;
        }
        .template_2 .decor-br-gold-2 {
            position: absolute;
            bottom: 0; right: 0;
            width: 0; height: 0;
            border-bottom: 73mm solid #fbbf24;
            border-left: 107mm solid transparent;
            z-index: 3;
        }

        /* --- TEMPLATE 3: CREATIVE PROFESSIONAL --- */
        .template_3 {
            width: 297mm;
            height: 210mm;
            background: #ffffff !important;
            position: relative;
            overflow: hidden;
        }
        .template_3 .cert-border {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            border: 15px solid #ffffff;
            pointer-events: none;
            z-index: 10;
        }
        .template_3 .content-wrap {
            padding: 20mm 15mm 10mm 15mm;
            text-align: center;
            position: relative;
            z-index: 5;
        }
        .template_3 h1 {
            font-family: 'Georgia', serif;
            font-size: 28pt;
            font-weight: bold;
            color: #1e1b4b;
            margin: 0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .template_3 .sub-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 10pt;
            color: #d97706;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-top: 3px;
            margin-bottom: 15px;
        }
        .template_3 .recipient-name {
            font-family: 'Great Vibes', 'Georgia', serif;
            font-size: 32pt;
            font-weight: normal;
            color: #4c1d95;
            margin: 8px auto;
            display: inline-block;
            font-style: italic;
            border-bottom: 2px solid #d97706;
            padding-bottom: 4px;
        }
        .template_3 .cert-footer {
            position: absolute;
            bottom: 18mm;
            left: 20mm;
            right: 20mm;
            text-align: center;
            width: auto;
            z-index: 5;
            padding: 0;
        }
        .template_3 .sig-box {
            display: inline-block;
            vertical-align: top;
            text-align: center;
            width: 70mm;
            margin: 0 10mm;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid #f3f4f6;
            padding: 4px 8px;
            border-radius: 6px;
        }
        .template_3 .sig-line {
            width: 50mm;
            border-bottom: 1.5px solid #4c1d95;
            margin: 6px auto;
        }
        .template_3 .verification-tag {
            left: 20mm;
            bottom: 10mm;
        }
        .template_3 .cert-id {
            right: 20mm;
            bottom: 10mm;
        }

        /* --- TEMPLATE 4: BLUE SHIELD (Image Background) --- */
        .template_4 {
            width: 297mm;
            height: 210mm;
            background: #ffffff !important;
            position: relative;
            overflow: hidden;
        }
        .template_4 .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
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
            width: 297mm;
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
        }
        .curriculum-table {
            width: 250mm;
            margin: 0 auto;
            border-collapse: collapse;
            color: #ffffff;
            font-size: 10pt;
        }
        .curriculum-table th, .curriculum-table td {
            border: 1px solid #ffffff;
            padding: 8px 12px;
        }
        .center { text-align: center; }
    </style>
</head>
<body>
    @if($template == 'template_4')
        {{-- Template 4: Blue Shield - 2-page layout --}}
        @php
            $bgBlueShieldPath = public_path('aset/bg-blue-shield.png');
            $logoPosterPath = public_path('aset/logo poster.png');
        @endphp

        {{-- PAGE 1 --}}
        <div class="certificate-page template_4 page-1">
            @if(file_exists($bgBlueShieldPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($bgBlueShieldPath)) }}" class="bg-image">
            @endif

            {{-- Top content: Logo Header Banner --}}
            <div class="logo-banner-container">
                @if(file_exists($logoPosterPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPosterPath)) }}" class="logo-poster-img">
                @endif
                @foreach(array_slice($logosBase64, 0, 2) as $logo)
                    <img src="{{ $logo }}" class="logo-item-top">
                @endforeach
            </div>

            {{-- Blue Area Content --}}
            <div class="content-blue">
                <h1 style="font-size: 26pt; font-weight: 900; margin: 0; letter-spacing: 3px; font-family: Arial, Helvetica, sans-serif;">SERTIFIKAT</h1>
                <p style="font-size: 9pt; font-weight: bold; letter-spacing: 4px; margin: 3mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">DIBERIKAN KEPADA</p>
                
                <div style="font-size: 24pt; font-weight: bold; margin: 4mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">
                    {{ strtoupper($user->name) }}
                </div>
                <div class="recipient-underline"></div>

                <p style="font-size: 9pt; margin: 2mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">Atas Keberhasilannya Menyelesaikan Seluruh Persyaratan</p>
                <p style="font-size: 14pt; font-weight: bold; margin: 1mm 0 2mm 0; font-family: Arial, Helvetica, sans-serif;">PROGRAM KURSUS</p>
                <p style="font-size: 9pt; margin: 2mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">Dalam Pembelajaran Materi</p>
                
                <h2 style="font-size: 16pt; font-weight: bold; margin: 1mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">
                    "{{ $course->name }}"
                </h2>
                <p style="font-size: 9pt; margin: 0 0 2mm 0; font-family: Arial, Helvetica, sans-serif;">
                    Designing Learning Experiences to Develop Real Competencies and Professional Portfolios
                </p>
                
                <p style="font-size: 8.5pt; margin: 3mm 0 0 0; font-family: Arial, Helvetica, sans-serif;">
                    Yang diselenggarakan oleh: 
                    <strong>IdSPora Learning Academy</strong> pada tanggal 
                    <strong>{{ $issuedAt->format('d F Y') }}</strong>
                </p>
            </div>

            {{-- Signature footer inside cert-inner --}}
            <div class="cert-footer">
                @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64); @endphp
                @forelse($sigsToRender as $sig)
                    <div class="sig-box">
                        <p class="sig-position">{{ $sig['position'] ?? 'Authorized Position' }}</p>
                        <div class="sig-image-wrap">
                            <img src="{{ $sig['base64'] }}" class="sig-img">
                        </div>
                        <div class="sig-line"></div>
                        <p class="sig-name">{{ $sig['name'] ?? 'Authorized Signature' }}</p>
                    </div>
                @empty
                    <div class="sig-box">
                        <p class="sig-position">Authorized Position</p>
                        <div class="sig-image-wrap">
                            <div style="height:55px;"></div>
                        </div>
                        <div class="sig-line"></div>
                        <p class="sig-name">Authorized Signature</p>
                    </div>
                @endforelse
            </div>

            <div class="verification-tag">VERIFIED BY IDSPORA.COM ACADEMY</div>
            <div class="cert-id" style="background:rgba(251,191,36,0.1);padding:3px 6px;border-radius:3px;">ID: {{ $certificateNumber }}</div>
        </div>

        {{-- PAGE 2 --}}
        <div class="certificate-page template_4 page-2">
            @if(file_exists($bgBlueShieldPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($bgBlueShieldPath)) }}" class="bg-image">
            @endif

            {{-- Top content: Logo Header Banner --}}
            <div class="logo-banner-container">
                @if(file_exists($logoPosterPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPosterPath)) }}" class="logo-poster-img">
                @endif
                @foreach(array_slice($logosBase64, 0, 2) as $logo)
                    <img src="{{ $logo }}" class="logo-item-top">
                @endforeach
            </div>

            {{-- Table Content --}}
            <div class="content-blue" style="top: 36mm;">
                <h2 style="font-size: 14pt; font-weight: bold; margin: 0 0 1mm 0; font-family: Arial, Helvetica, sans-serif; color: #ffffff;">
                    {{ $course->name }}
                </h2>
                <p style="font-size: 9.5pt; margin: 0 0 4mm 0; font-family: Arial, Helvetica, sans-serif; color: #ffffff; font-weight: bold;">
                    Designing Learning Experiences to Develop Real Competencies and Professional Portfolios
                </p>

                <table class="curriculum-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">No.</th>
                            <th style="width: 74%; text-align: left; padding-left: 15px;">Materi</th>
                            <th style="width: 18%;">Jam Pelajaran (JP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="center">1.</td>
                            <td style="text-align: left; padding-left: 15px;">OBE Canvas</td>
                            <td class="center">2</td>
                        </tr>
                        <tr>
                            <td class="center">2.</td>
                            <td style="text-align: left; padding-left: 15px;">Transforming Teaching: From Content Delivery to Active Learning Design</td>
                            <td class="center">2</td>
                        </tr>
                        <tr>
                            <td class="center">3.</td>
                            <td style="text-align: left; padding-left: 15px;">Design Thinking for OBE: From Learning to Real-World Product & Portfolio</td>
                            <td class="center">2</td>
                        </tr>
                        <tr>
                            <td class="center">4.</td>
                            <td style="text-align: left; padding-left: 15px;">Meningkatkan Engagement di Ruang Kelas</td>
                            <td class="center">3</td>
                        </tr>
                        <tr>
                            <td class="center">5.</td>
                            <td style="text-align: left; padding-left: 15px;">Project-Based Learning for Outcome Based Education</td>
                            <td class="center">2</td>
                        </tr>
                        <tr>
                            <td class="center">6.</td>
                            <td style="text-align: left; padding-left: 15px;">Pemetaan KKNI dan SKKNI ke dalam OBE</td>
                            <td class="center">2</td>
                        </tr>
                        <tr>
                            <td class="center">7.</td>
                            <td style="text-align: left; padding-left: 15px;">Penggunaan AI untuk Pembelajaran</td>
                            <td class="center">2</td>
                        </tr>
                        <tr>
                            <td class="center">8.</td>
                            <td style="text-align: left; padding-left: 15px;">From Learning to Real Output: Membuat Portfolio + Showcase</td>
                            <td class="center">3</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center; font-weight: bold; background-color: #faf9f6; color: #000000; border: 1.5px solid #000000; padding: 6px 12px;">Total Jam Pelajaran</td>
                            <td class="center" style="font-weight: bold; background-color: #faf9f6; color: #000000; border: 1.5px solid #000000; padding: 6px 12px;">18 JP</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="verification-tag">VERIFIED BY IDSPORA.COM ACADEMY</div>
            <div class="cert-id" style="background:rgba(251,191,36,0.1);padding:3px 6px;border-radius:3px;">ID: {{ $certificateNumber }}</div>
        </div>
    @else
    <div class="certificate-page {{ $template }}">
        @if($template == 'template_1')
        <div class="cert-inner">
            <div class="header">
                <div class="logo-row">
                    <div class="logo-container">
                        @php 
                            $logoFileName = 'logo idspora_dark.png';
                            $mainLogoPath = public_path('aset/' . $logoFileName); 
                        @endphp
                        @if(file_exists($mainLogoPath))
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($mainLogoPath)) }}" class="logo-item" style="height: 36px; width: auto; vertical-align: middle;">
                        @endif
                        @foreach(array_slice($logosBase64, 0, 3) as $logo)
                            <img src="{{ $logo }}" class="logo-item" style="height: 36px; width: auto; vertical-align: middle;">
                        @endforeach
                    </div>
                </div>
                <h1>Course Certificate</h1>
                <div class="sub-title">Certificate of Completion</div>
            </div>
            <div class="content" style="margin-top:5px;">
                <p style="font-size:12pt;color:#64748b;font-style:italic;margin-bottom:2px;">This is to certify that</p>
                @if($template == 'template_1')
                    <div class="recipient-name">{{ ucwords(strtolower($user->name)) }}</div>
                @else
                    <div class="recipient-name">{{ strtoupper($user->name) }}</div>
                @endif
                <div style="width: 70%; border-top: 1.5px dotted #7f1d1d; margin: 6px auto 12px auto;"></div>
                <p style="font-size:10pt;line-height:1.4;color:#1e293b;margin-top:5px;">has successfully completed all requirements of the course</p>
                <h2 style="font-size:16pt;color:#0f172a;margin:4px 0;font-family:'Georgia',serif;">"{{ $course->name }}"</h2>
                <p style="font-size:8.5pt;color:#64748b;">Issued on {{ $issuedAt->format('d F Y') }} by idSpora Academy</p>
            </div>

            {{-- Signature footer inside cert-inner for template_1 --}}
            <div class="cert-footer">
                @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64); @endphp
                @forelse($sigsToRender as $sig)
                    <div class="sig-box">
                        <img src="{{ $sig['base64'] }}" style="height:40px;width:auto;display:block;margin:0 auto;object-fit:contain;">
                        <div class="sig-line"></div>
                        @if(!empty($sig['name']))
                            <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#0f172a;">{{ $sig['name'] }}</p>
                            @if(!empty($sig['position']))
                                <p style="margin:1px 0 0;font-size:6.5pt;color:#64748b;font-style:italic;">{{ $sig['position'] }}</p>
                            @endif
                        @else
                            <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#0f172a;">Authorized Signature</p>
                        @endif
                    </div>
                @empty
                    <div class="sig-box">
                        <div style="height:40px;"></div>
                        <div class="sig-line"></div>
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#0f172a;">Authorized Signature</p>
                    </div>
                @endforelse
            </div>
        </div>{{-- end cert-inner --}}

        <!-- Top Left Gold Bar -->
        <div style="position: absolute; top: 15mm; left: 15mm; width: 140mm; height: 4px; background: #eab308; z-index: 2;"></div>
        <!-- Bottom Right Gold Bar -->
        <div style="position: absolute; bottom: 15mm; right: 15mm; width: 140mm; height: 4px; background: #eab308; z-index: 2;"></div>

        <!-- Top Right Maroon & Gold Waves (CSS Rounded Corners anchored at top-right for Dompdf compatibility) -->
        <div style="position: absolute; top: 0; right: 0; width: 120mm; height: 120mm; border-bottom-left-radius: 120mm; background: #7f1d1d; z-index: 1;"></div>
        <div style="position: absolute; top: 0; right: 0; width: 105mm; height: 105mm; border-bottom-left-radius: 105mm; background: #eab308; z-index: 1;"></div>
        <div style="position: absolute; top: 0; right: 0; width: 90mm; height: 90mm; border-bottom-left-radius: 90mm; background: #991b1b; z-index: 1;"></div>
        <div style="position: absolute; top: 0; right: 0; width: 75mm; height: 75mm; border-bottom-left-radius: 75mm; background: #eab308; z-index: 1;"></div>
        <div style="position: absolute; top: 0; right: 0; width: 60mm; height: 60mm; border-bottom-left-radius: 60mm; background: #7f1d1d; z-index: 1;"></div>

        <!-- Bottom Left Maroon & Gold Waves (CSS Rounded Corners anchored at bottom-left for Dompdf compatibility) -->
        <div style="position: absolute; bottom: 0; left: 0; width: 120mm; height: 120mm; border-top-right-radius: 120mm; background: #7f1d1d; z-index: 1;"></div>
        <div style="position: absolute; bottom: 0; left: 0; width: 105mm; height: 105mm; border-top-right-radius: 105mm; background: #eab308; z-index: 1;"></div>
        <div style="position: absolute; bottom: 0; left: 0; width: 90mm; height: 90mm; border-top-right-radius: 90mm; background: #991b1b; z-index: 1;"></div>
        <div style="position: absolute; bottom: 0; left: 0; width: 75mm; height: 75mm; border-top-right-radius: 75mm; background: #eab308; z-index: 1;"></div>
        <div style="position: absolute; bottom: 0; left: 0; width: 60mm; height: 60mm; border-top-right-radius: 60mm; background: #7f1d1d; z-index: 1;"></div>

        @elseif($template == 'template_2')
        <div class="cert-border"></div>
        
        <!-- CSS background decorations for Template 2 (Dompdf compatible) -->
        <div class="decor-tl-1"></div>
        <div class="decor-tl-2"></div>
        <div class="decor-tl-3"></div>
        
        <div class="decor-tr-navy"></div>
        <div class="decor-br-gold-1"></div>
        <div class="decor-br-gold-2"></div>
        
        <div class="gold-badge">
            <div class="gold-badge-inner"></div>
        </div>
        
        <div class="content-wrap">
            <div class="logo-row" style="text-align:center;margin-bottom:10px;">
                <div class="logo-container">
                    @php 
                        $logoFileName = ($template == 'template_3') ? 'logo-idspora.png' : 'logo idspora_dark.png';
                        $mainLogoPath = public_path('aset/' . $logoFileName); 
                    @endphp
                    @if(file_exists($mainLogoPath))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($mainLogoPath)) }}" class="logo-item">
                    @endif
                    @foreach(array_slice($logosBase64, 0, 3) as $logo)
                        <img src="{{ $logo }}" class="logo-item">
                    @endforeach
                </div>
            </div>
            <h1>COURSE CERTIFICATE</h1>
            <div class="sub-title">Completion &amp; Mastery</div>
            <p style="font-size:12pt;color:#64748b;font-style:italic;margin-bottom:2px;">This is to certify that</p>
            <div class="recipient-name">{{ strtoupper($user->name) }}</div>
            <p style="font-size:10pt;color:#1e293b;margin-top:5px;">has successfully completed all requirements of the course</p>
            <h2 style="font-size:16pt;color:#0f172a;margin:4px 0;">"{{ $course->name }}"</h2>
            <p style="font-size:8.5pt;color:#64748b;">Issued on {{ $issuedAt->format('d F Y') }} by idSpora Academy</p>
        </div>{{-- end content-wrap --}}

        {{-- Signature footer for template_2 --}}
        <div class="cert-footer">
            @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64); @endphp
            @forelse($sigsToRender as $sig)
                <div class="sig-box">
                    <img src="{{ $sig['base64'] }}" style="height:40px;width:auto;display:block;margin:0 auto;object-fit:contain;">
                    <div class="sig-line"></div>
                    @if(!empty($sig['name']))
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#0f172a;">{{ $sig['name'] }}</p>
                        @if(!empty($sig['position']))
                            <p style="margin:1px 0 0;font-size:6.5pt;color:#64748b;font-style:italic;">{{ $sig['position'] }}</p>
                        @endif
                    @else
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#0f172a;">Authorized Signature</p>
                    @endif
                </div>
            @empty
                <div class="sig-box">
                    <div style="height:40px;"></div>
                    <div class="sig-line"></div>
                    <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#0f172a;">Authorized Signature</p>
                </div>
            @endforelse
        </div>

        @elseif($template == 'template_3'){{-- template_3 --}}
        <div class="cert-border"></div>
        @php
            $bgCreativePath = public_path('aset/bg-creative.png');
        @endphp
        @if(file_exists($bgCreativePath))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents($bgCreativePath)) }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
        @endif
        
        <div class="content-wrap">
            <div class="logo-row">
                <div class="logo-container">
                    @php 
                        $logoFileName = 'logo-idspora.png';
                        $mainLogoPath = public_path('aset/' . $logoFileName); 
                    @endphp
                    @if(file_exists($mainLogoPath))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($mainLogoPath)) }}" class="logo-item" style="height: 36px; width: auto; vertical-align: middle;">
                    @endif
                    @foreach(array_slice($logosBase64, 0, 3) as $logo)
                        <img src="{{ $logo }}" class="logo-item" style="height: 36px; width: auto; vertical-align: middle;">
                    @endforeach
                </div>
            </div>
            <h1>Course Certificate</h1>
            <div class="sub-title">PROFESSIONAL EDUCATION</div>
            
            <p style="font-size: 11pt; color: #64748b; font-style: italic; margin-bottom: 2px;">This certificate is proudly awarded to</p>
            <div class="recipient-name">{{ strtoupper($user->name) }}</div>
            <p style="font-size: 10pt; color: #1e293b; margin-top: 5px;">for successful completion and mastery of the online course</p>
            <h2 style="font-size: 16pt; color: #1e1b4b; margin: 4px 0; font-family: 'Georgia', serif;">"{{ $course->name }}"</h2>
            <p style="font-size: 8.5pt; color: #64748b;">Issued by IdSPora Learning Academy on {{ $issuedAt->format('d F Y') }}</p>
        </div>

        {{-- Signature footer for template_3 --}}
        <div class="cert-footer">
            @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64); @endphp
            @forelse($sigsToRender as $sig)
                <div class="sig-box">
                    <img src="{{ $sig['base64'] }}" style="height:40px;width:auto;display:block;margin:0 auto;object-fit:contain;">
                    <div class="sig-line"></div>
                    @if(!empty($sig['name']))
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">{{ $sig['name'] }}</p>
                        @if(!empty($sig['position']))
                            <p style="margin:1px 0 0;font-size:6.5pt;color:#64748b;font-style:italic;">{{ $sig['position'] }}</p>
                        @endif
                    @else
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">Authorized Signature</p>
                    @endif
                </div>
            @empty
                <div class="sig-box">
                    <div style="height:40px;"></div>
                    <div class="sig-line"></div>
                    <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">Authorized Signature</p>
                </div>
            @endforelse
        </div>
        @endif

        <div class="verification-tag">VERIFIED BY IDSPORA.COM ACADEMY</div>
        <div class="cert-id" style="background:rgba(251,191,36,0.1);padding:3px 6px;border-radius:3px;">ID: {{ $certificateNumber }}</div>
    </div>
    @endif
</body>
</html>
