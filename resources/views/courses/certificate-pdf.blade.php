@php
    $template = $course->certificate_template ?? 'template_1';
@endphp
@if(!isset($is_preview) || !$is_preview)<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Sertifikat Kursus</title>@endif<style>@import url('https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap');@if(!isset($is_preview) || !$is_preview)@page{size:A4 landscape;margin:0;}*{box-sizing:border-box;-webkit-print-color-adjust:exact;}html,body{margin:0;padding:0;width:297mm;@if($template != 'template_4')height:210mm;overflow:hidden;@endif background:white;font-family:'Helvetica','Arial',sans-serif;}@endif
        
        .certificate-page { 
            width: 270mm; 
            height: 170mm; 
            position: absolute; 
            top: 20mm; left: 13.5mm;
            overflow: hidden; 
            background: white;
            color: #1e293b;
            box-sizing: border-box;
            page-break-after: avoid;
            page-break-inside: avoid;
            display: block;
            @if(isset($is_preview) && $is_preview)
                position: relative; 
                top: 0; left: 0; 
                transform: scale(var(--cert-scale, 1));
                transform-origin: top left;
            @endif
        }

        @if(!isset($is_preview) || !$is_preview)
            .template_4.certificate-page {
                position: relative !important;
                top: 20mm !important;
                left: 13.5mm !important;
                display: block !important;
                float: none !important;
            }
            .template_4.page-1 {
                page-break-after: always !important;
                margin-bottom: 40mm !important;
            }
            .template_4.page-2 {
                page-break-after: avoid !important;
            }
        @endif

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

        /* Template 1: Premium Royal (Elegant Maroon & Gold Waves) */
        .template_1 { 
            border: none; 
            height: 170mm; 
            width: 270mm;
            position: relative; 
            padding: 35px;
            box-sizing: border-box;
            background: #ffffff;
            overflow: hidden;
        }
        .template_1 .header { text-align: center; position: relative; z-index: 2; padding-top: 20px; }
        .template_1 h1 { 
            font-family: 'Georgia', serif; 
            font-size: 36pt; 
            color: #1e1b4b; 
            margin: 10px 0 2px; 
            text-transform: uppercase; 
            letter-spacing: 4px;
            font-weight: 700;
        }
        .template_1 .sub-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 11pt;
            color: #7f1d1d;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-top: 4px;
            margin-bottom: 25px;
        }
        .template_1 .recipient-name { 
            font-size: 38pt; 
            font-weight: normal; 
            color: #7f1d1d;
            border-bottom: none; 
            display: inline-block; 
            padding: 4px 50px; 
            margin: 10px 0;
            font-family: 'Great Vibes', cursive;
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
        }
        .template_1 .content { text-align: center; position: relative; z-index: 2; }
        .template_1 .sig-box {
            display: inline-block !important;
            float: none !important;
            text-align: center !important;
            width: 230px !important;
            margin: 0 30px !important;
        }
        .template_1 .sig-line {
            width: 170px;
            border-bottom: 1.5px dashed #7f1d1d;
            margin: 8px auto;
        }

        /* Template 2: Modern Corporate (Sleek Professional) */
        .template_2 { 
            padding: 0; 
            height: 170mm; 
            width: 270mm;
            box-sizing: border-box; 
            overflow: hidden; 
            background: #f8fafc;
            position: relative;
        }
        .template_2 .content-wrap {
            padding: 50px 20px 20px 20px;
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
            margin-bottom: 25px;
        }
        .template_2 .recipient-name { 
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
        .template_2 .gold-badge {
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
        .template_2 .gold-badge-inner {
            position: absolute;
            top: 5px; left: 5px; right: 5px; bottom: 5px;
            border-radius: 50%;
            border: 1px solid #d4af37;
            background: #faf8f5;
        }
        .template_2 .cert-footer {
            padding: 0 !important;
            left: 50px !important;
            right: 50px !important;
            width: calc(100% - 100px) !important;
            text-align: center !important;
        }
        .template_2 .sig-box {
            display: inline-block !important;
            float: none !important;
            text-align: center !important;
            width: 250px !important;
            margin: 0 30px !important;
        }

        /* Template 3: Creative Professional (Dynamic Wave & Gradients) */
        .template_3 { 
            padding: 0; 
            height: 170mm; 
            width: 270mm;
            box-sizing: border-box; 
            background: #fdfdfd;
            border: 15px solid #ffffff;
            position: relative;
            overflow: hidden;
        }


        /* Template 4: Blue Shield (Navy Blue & Gold Pentagon) */
        .template_4 { 
            padding: 0; 
            height: 170mm; 
            width: 270mm;
            box-sizing: border-box;
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }
        .template_4 .bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 270mm;
            height: 170mm;
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
            top: 36mm;
            left: 0;
            width: 270mm;
            text-align: center;
            z-index: 5;
            padding: 0;
            box-sizing: border-box;
            color: #ffffff;
            font-family: Arial, Helvetica, sans-serif;
        }
        .template_4 .recipient-underline {
            width: 160mm;
            height: 1.5px;
            background-color: #ffffff;
            margin: 2mm auto 4mm auto;
        }
        .template_4 .cert-footer {
            position: absolute !important;
            bottom: 12mm !important;
            left: 20mm !important;
            right: 20mm !important;
            text-align: center !important;
            width: auto !important;
            z-index: 6 !important;
            padding: 0 !important;
        }
        .template_4 .sig-box {
            display: inline-block !important;
            vertical-align: bottom !important;
            float: none !important;
            text-align: center !important;
            width: 70mm !important;
            margin: 0 8mm !important;
        }
        .template_4 .sig-position {
            font-weight: bold;
            margin: 0 0 1mm 0;
            font-size: 8pt;
            color: #1a1a1a;
            font-family: Arial, Helvetica, sans-serif;
        }
        .template_4 .sig-image-wrap {
            height: 48px;
            margin: 1mm auto;
            text-align: center;
        }
        .template_4 .sig-img {
            height: 48px;
            width: auto;
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }
        .template_4 .sig-line {
            width: 55mm;
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

        .template_3 .content-wrap {
            padding: 40px 20px 20px 20px;
            text-align: center;
            position: relative;
            z-index: 5;
        }
        .template_3 h1 {
            font-family: 'Georgia', serif;
            font-size: 32pt;
            font-weight: 900;
            color: #1e1b4b;
            margin: 0;
            letter-spacing: 3px;
            text-transform: uppercase;
        }
        .template_3 .sub-title {
            font-family: 'Helvetica', sans-serif;
            font-size: 11pt;
            color: #d97706;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-top: 4px;
            margin-bottom: 25px;
        }
        .template_3 .recipient-name {
            font-family: 'Great Vibes', 'Georgia', serif;
            font-size: 38pt;
            font-weight: normal;
            color: #4c1d95;
            margin: 10px auto;
            display: inline-block;
            font-style: italic;
            border-bottom: 2px solid #d97706;
            padding-bottom: 5px;
        }
        .template_3 .cert-footer {
            padding: 0 !important;
            left: 50px !important;
            right: 50px !important;
            width: calc(100% - 100px) !important;
            text-align: center !important;
        }
        .template_3 .sig-box {
            display: inline-block !important;
            float: none !important;
            text-align: center !important;
            width: 230px !important;
            margin: 0 20px !important;
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid #f3f4f6;
            padding: 6px 12px;
            border-radius: 8px;
        }
        .template_3 .sig-line {
            width: 170px;
            border-bottom: 1.5px solid #4c1d95;
            margin: 8px auto;
        }

        /* Shared / Layout Components */
        .logo-row { text-align: center; margin-bottom: 15px; width: 100%; }
        .logo-container { display: inline-block; vertical-align: middle; }
        .logo-item { height: 48px; width: auto; margin: 0 10px; vertical-align: middle; }
        
        .template_2 .logo-row { text-align: left; margin-bottom: 25px; }
        
        .cert-footer { position: absolute; bottom: 70px; width: 100%; left: 0; padding: 0 70px; box-sizing: border-box; z-index: 3; }
        .template_2 .cert-footer { padding: 0 70px 0 160px; } /* Indent footer for sidebar in template 2 */
        
        .sig-box { float: right; text-align: center; margin-left: 35px; }
        .template_3 .sig-box {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.8);
            padding: 10px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
            backdrop-filter: blur(6px);
        }
        .sig-line { width: 170px; border-bottom: 1.5px solid #0f172a; margin: 8px auto; }
        .template_3 .sig-line { border-bottom-color: #4f46e5; }
        
        .cert-id { position: absolute; bottom: 25px; right: 40px; font-size: 8.5pt; color: #94a3b8; font-weight: 600; z-index: 3; }
        .verification-tag { position: absolute; bottom: 25px; left: 40px; font-size: 7.5pt; color: #94a3b8; font-family: monospace; letter-spacing: 1.5px; font-weight: 600; z-index: 3; }
        .template_2 .verification-tag { left: 160px; } /* Offset for sidebar in modern template */
        .template_3 .verification-tag { left: 70px; bottom: 25px; }
        .template_3 .cert-id { right: 70px; bottom: 25px; }
    </style>
@if(!isset($is_preview) || !$is_preview)</head><body>@endif

    @php $template = $course->certificate_template ?? 'template_1'; @endphp
    
    @if($template == 'template_4')
        {{-- Template 4: Blue Shield - 2-page layout --}}
        @php 
            $bgBlueShieldUrl = request()->schemeAndHttpHost() . '/aset/bg-blue-shield.png';
            $logoPosterUrl = request()->schemeAndHttpHost() . '/aset/logo poster.png';
            $logosToRender4 = (isset($is_preview) && $is_preview && !empty($logosUrl)) ? $logosUrl : $logosBase64;
        @endphp

        {{-- PAGE 1 --}}
        <div class="certificate-page template_4 page-1">
            <img src="{{ $bgBlueShieldUrl }}" class="bg-image">

            {{-- Top content: Logo Header Banner --}}
            <div class="logo-banner-container">
                <img src="{{ $logoPosterUrl }}" class="logo-poster-img">
                @foreach(array_slice($logosToRender4, 0, 2) as $logo)
                    <img src="{{ $logo }}" class="logo-item-top">
                @endforeach
            </div>

            {{-- Blue Area Content --}}
            <div class="content-blue">
                <h1 style="font-size: 22pt; font-weight: 900; margin: 0; letter-spacing: 3px; font-family: Arial, Helvetica, sans-serif;">SERTIFIKAT</h1>
                <p style="font-size: 8.5pt; font-weight: bold; letter-spacing: 4px; margin: 2.5mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">DIBERIKAN KEPADA</p>
                
                <div style="font-size: 20pt; font-weight: bold; margin: 3mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">
                    {{ strtoupper($user->name) }}
                </div>
                <div class="recipient-underline"></div>

                <p style="font-size: 8.5pt; margin: 2mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">Atas Keberhasilannya Menyelesaikan Seluruh Persyaratan</p>
                <p style="font-size: 13pt; font-weight: bold; margin: 1mm 0 2mm 0; font-family: Arial, Helvetica, sans-serif;">PROGRAM KURSUS</p>
                <p style="font-size: 8.5pt; margin: 2mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">Dalam Pembelajaran Materi</p>
                
                <h2 style="font-size: 14pt; font-weight: bold; margin: 1mm 0 1mm 0; font-family: Arial, Helvetica, sans-serif;">
                    "{{ $course->name }}"
                </h2>
                <p style="font-size: 8.5pt; margin: 0 0 2mm 0; font-family: Arial, Helvetica, sans-serif;">
                    Designing Learning Experiences to Develop Real Competencies and Professional Portfolios
                </p>
                
                <p style="font-size: 8pt; margin: 2.5mm 0 0 0; font-family: Arial, Helvetica, sans-serif;">
                    Yang diselenggarakan oleh: 
                    <strong>IdSPora Learning Academy</strong> pada tanggal 
                    <strong>{{ $issuedAt->format('d F Y') }}</strong>
                </p>
            </div>

            {{-- Signature footer for template_4 inside the white area --}}
            <div class="cert-footer">
                <div style="text-align: center; width: 100%;">
                    @php 
                        $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64' => $b, 'name' => '', 'position' => ''], $signaturesBase64);
                    @endphp
                    @forelse($sigsToRender as $sig)
                        <div class="sig-box">
                            <p class="sig-position">{{ $sig['position'] ?? 'Authorized Position' }}</p>
                            <div class="sig-image-wrap">
                                @php $sigSrc = (isset($is_preview) && $is_preview && !empty($sig['url'])) ? $sig['url'] : $sig['base64']; @endphp
                                <img src="{{ $sigSrc }}" class="sig-img">
                            </div>
                            <div class="sig-line"></div>
                            <p class="sig-name">{{ $sig['name'] ?? 'Authorized Signature' }}</p>
                        </div>
                    @empty
                        <div class="sig-box">
                            <p class="sig-position">Authorized Position</p>
                            <div class="sig-image-wrap">
                                <div style="height:48px;"></div>
                            </div>
                            <div class="sig-line"></div>
                            <p class="sig-name">Authorized Signature</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="verification-tag">VERIFIED BY IDSPORA.COM ACADEMY</div>
            <div class="cert-id" style="background: rgba(251, 191, 36, 0.1); padding: 5px 10px; border-radius: 4px;">Verified Certificate ID: {{ $certificateNumber }}</div>
        </div>

        {{-- PAGE 2 --}}
        <div class="certificate-page template_4 page-2">
            <img src="{{ $bgBlueShieldUrl }}" class="bg-image">

            {{-- Top content: Logo Header Banner --}}
            <div class="logo-banner-container">
                <img src="{{ $logoPosterUrl }}" class="logo-poster-img">
                @foreach(array_slice($logosToRender4, 0, 2) as $logo)
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
            <div class="cert-id" style="background: rgba(251, 191, 36, 0.1); padding: 5px 10px; border-radius: 4px;">Verified Certificate ID: {{ $certificateNumber }}</div>
        </div>
    @else
        <div class="certificate-page {{ $template }}">
            @if($template == 'template_1') 
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
            @elseif($template == 'template_2')
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
            @endif

            @if($template == 'template_3')
                @php 
                    $bgCreativeUrl = request()->schemeAndHttpHost() . '/aset/bg-creative.png';
                @endphp
                <img src="{{ $bgCreativeUrl }}" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1;">
                
                <div class="content-wrap">
                    <div class="logo-row">
                        @php 
                            $logoFileName = 'logo-idspora.png';
                            $mainLogoUrl = request()->schemeAndHttpHost() . '/aset/' . $logoFileName;
                        @endphp
                        <img src="{{ $mainLogoUrl }}" class="logo-item" style="height: 50px; width: auto;">

                        @php $logosToRender = (isset($is_preview) && $is_preview && !empty($logosUrl)) ? $logosUrl : $logosBase64; @endphp
                        @foreach($logosToRender as $logo)
                            <img src="{{ $logo }}" class="logo-item" style="height: 50px; width: auto;">
                        @endforeach
                    </div>
                    <h1>COURSE CERTIFICATE</h1>
                    <div class="sub-title">PROFESSIONAL EDUCATION</div>
                    
                    <p style="font-size: 14pt; color: #64748b; font-style: italic; margin-bottom: 5px;">This certificate is proudly awarded to</p>
                    <div class="recipient-name">{{ strtoupper($user->name) }}</div>
                    <p style="font-size: 12pt; color: #1e293b; margin-top: 10px;">for successful completion and mastery of the online course</p>
                    <h2 style="font-size: 24pt; color: #1e1b4b; margin: 10px 0; font-family: 'Georgia', serif;">"{{ $course->name }}"</h2>
                    <p style="font-size: 11pt; color: #64748b;">Issued by IdSPora Learning Academy on {{ $issuedAt->format('d F Y') }}</p>
                </div>
            @else
                <div class="header" style="{{ ($template == 'template_1' || $template == 'template_2') ? 'padding: 40px 10px 0 10px; text-align: center;' : '' }}">
                    @if($template == 'template_1')
                        <div class="logo-row">
                            <div class="logo-container">
                                @php 
                                    $logoFileName = 'logo idspora_dark.png';
                                    $mainLogoPath = public_path('aset/' . $logoFileName); 
                                    $mainLogoUrl = request()->schemeAndHttpHost() . '/aset/' . $logoFileName;
                                @endphp
                                @if(isset($is_preview) && $is_preview)
                                    <img src="{{ $mainLogoUrl }}" class="logo-item">
                                @elseif(file_exists($mainLogoPath))
                                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($mainLogoPath)) }}" class="logo-item">
                                @endif

                                @php $logosToRender = (isset($is_preview) && $is_preview && !empty($logosUrl)) ? $logosUrl : $logosBase64; @endphp
                                @foreach(array_slice($logosToRender, 0, 3) as $logo)
                                    <img src="{{ $logo }}" class="logo-item">
                                @endforeach
                            </div>
                        </div>
                        <h1 style="margin-top: 10px; font-size: 38pt; font-family: 'Georgia', serif; color: #1e1b4b;">Course Certificate</h1>
                        <div class="sub-title">Certificate of Completion</div>
                    @elseif($template == 'template_2')
                        <div class="logo-row">
                            @php 
                                $logoFileName = ($template == 'template_3') ? 'logo.png' : 'logo idspora_dark.png';
                                $mainLogoPath = public_path('aset/' . $logoFileName); 
                                $mainLogoUrl = request()->schemeAndHttpHost() . '/aset/' . $logoFileName;
                            @endphp
                            @if(isset($is_preview) && $is_preview)
                                <img src="{{ $mainLogoUrl }}" class="logo-item" style="height: 50px; width: auto;">
                            @elseif(file_exists($mainLogoPath))
                                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($mainLogoPath)) }}" class="logo-item" style="height: 50px; width: auto;">
                            @endif

                            @php $logosToRender = (isset($is_preview) && $is_preview && !empty($logosUrl)) ? $logosUrl : $logosBase64; @endphp
                            @foreach($logosToRender as $logo)
                                <img src="{{ $logo }}" class="logo-item" style="height: 50px; width: auto;">
                            @endforeach
                        </div>
                        <h1>COURSE CERTIFICATE</h1>
                        <div class="sub-title">Completion & Mastery</div>
                    @endif
                </div>
                <div class="content" style="{{ ($template == 'template_1' || $template == 'template_2') ? 'padding: 20px 10px 0 10px; text-align: center; margin-top: 0;' : 'margin-top: 40px;' }}">
                    <p style="font-size: 16pt; color: #64748b; font-style: italic; margin-bottom: 5px;">This is to certify that</p>
                    @if($template == 'template_1')
                        <div class="recipient-name">{{ ucwords(strtolower($user->name)) }}</div>
                        <div style="width: 70%; border-top: 1.5px dotted #7f1d1d; margin: 15px auto;"></div>
                    @else
                        <div class="recipient-name">{{ strtoupper($user->name) }}</div>
                    @endif
                    <p style="font-size: 14pt; line-height: 1.5; color: #1e293b; margin-top: 10px;">has successfully completed all requirements of the course</p>
                    <h2 style="font-size: 26pt; color: #1e1b4b; margin: 15px 0; font-family: 'Georgia', serif;">"{{ $course->name }}"</h2>
                    <p style="font-size: 12pt; color: #64748b;">Issued on {{ $issuedAt->format('d F Y') }} by idSpora Academy</p>
                </div>
            @endif

            <div class="cert-footer">
                <div style="text-align: center; width: 100%;">
                    @php 
                        $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64' => $b, 'name' => '', 'position' => ''], $signaturesBase64);
                    @endphp
                    @forelse($sigsToRender as $sig)
                        <div class="sig-box">
                            @php $sigSrc = (isset($is_preview) && $is_preview && !empty($sig['url'])) ? $sig['url'] : $sig['base64']; @endphp
                            <img src="{{ $sigSrc }}" style="height: 90px; width: auto; display: block; margin: 0 auto;">
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
                            <div style="height: 90px;"></div>
                            <div class="sig-line"></div>
                            <p style="font-weight: bold; margin: 0; font-size: 11pt; color: #1e1b4b;">Authorized Signature</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="verification-tag">VERIFIED BY IDSPORA.COM ACADEMY</div>
            <div class="cert-id" style="background: rgba(251, 191, 36, 0.1); padding: 5px 10px; border-radius: 4px;">Verified Certificate ID: {{ $certificateNumber }}</div>
        </div>
    @endif

    @if(isset($is_preview) && $is_preview)
    <script>
        function adjustScale() {
            const cert = document.querySelector('.certificate-page');
            if (!cert) return;
            // 29.7cm at 96dpi is ~1123px
            const baseWidth = 1123;
            const currentWidth = window.innerWidth;
            const scale = currentWidth / baseWidth;
            cert.style.setProperty('--cert-scale', scale);
            // Also adjust body height to prevent scrolling if possible
            document.body.style.overflow = 'hidden';
        }
        window.addEventListener('resize', adjustScale);
        window.addEventListener('load', adjustScale);
        setTimeout(adjustScale, 100);
    </script>
    @endif
</body>
</html>
