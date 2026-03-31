@if(!isset($is_preview) || !$is_preview)
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Trainer</title>
@endif
    <style>
        @if(!isset($is_preview) || !$is_preview)
            @page { size: A4 landscape; margin: 0; }
            body { margin: 0; padding: 0; font-family: 'Helvetica', 'Arial', sans-serif; }
        @endif

        .certificate-page {
            width: 29.7cm;
            height: 21cm;
            position: relative;
            overflow: hidden;
            background: white;
            color: #1e293b;
            box-sizing: border-box;
            @if(isset($is_preview) && $is_preview)
                transform: scale(var(--cert-scale, 1));
                transform-origin: top left;
            @endif
        }

        /* Template 1: Premium Royal */
        .template_1 {
            border: 30px solid #1e1b4b;
            height: 21cm;
            position: relative;
            padding: 60px;
            box-sizing: border-box;
        }
        .template_1 .inner-border {
            position: absolute;
            top: 15px; left: 15px; right: 15px; bottom: 15px;
            border: 4px double #fbbf24;
            pointer-events: none;
        }
        .template_1 .corner-element {
            position: absolute;
            width: 100px;
            height: 100px;
            border: 2px solid #fbbf24;
            border-radius: 50%;
        }
        .template_1 .corner-tl { top: -50px; left: -50px; }
        .template_1 .corner-tr { top: -50px; right: -50px; }
        .template_1 .corner-bl { bottom: -50px; left: -50px; }
        .template_1 .corner-br { bottom: -50px; right: -50px; }
        .template_1 .header { text-align: center; }
        .template_1 h1 {
            font-family: 'Georgia', serif;
            font-size: 48pt;
            color: #1e1b4b;
            margin: 20px 0 10px;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        .template_1 .recipient-name {
            font-size: 42pt;
            font-weight: bold;
            color: #1e1b4b;
            border-bottom: 2px solid #fbbf24;
            display: inline-block;
            padding: 10px 60px;
            margin: 20px 0;
            font-family: 'Times New Roman', serif;
        }
        .template_1 .content { text-align: center; }

        /* Template 2: Modern */
        .template_2 {
            padding: 0;
            height: 21cm;
            box-sizing: border-box;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }
        .template_2 .sidebar {
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 80px;
            background: #1e1b4b;
        }
        .template_2 .gold-accent {
            position: absolute;
            left: 80px; top: 0; bottom: 0;
            width: 8px;
            background: #fbbf24;
        }
        .template_2 .content-wrap {
            margin-left: 120px;
            padding: 80px 80px 80px 60px;
        }
        .template_2 h1 {
            font-size: 56pt;
            font-weight: 800;
            color: #1e1b4b;
            margin: 0;
            letter-spacing: -2px;
        }
        .template_2 .sub-title {
            font-size: 18pt;
            color: #fbbf24;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 40px;
        }
        .template_2 .recipient-name {
            font-size: 48pt;
            font-weight: 900;
            color: #1e1b4b;
            margin: 20px 0;
            border-left: 10px solid #fbbf24;
            padding-left: 30px;
        }
        .template_2 .watermark {
            position: absolute;
            right: -50px; bottom: -50px;
            width: 400px; height: 400px;
            opacity: 0.03;
            pointer-events: none;
        }

        /* Template 3: Creative */
        .template_3 {
            padding: 0;
            height: 21cm;
            box-sizing: border-box;
            background: #f8fafc;
            border: 20px solid #ffffff;
            box-shadow: inset 0 0 100px rgba(30, 27, 75, 0.03);
        }
        .template_3 .header-bg {
            height: 150px;
            background: #1e1b4b;
            padding: 40px 80px;
            color: white;
            position: relative;
        }
        .template_3 .header-bg::after {
            content: '';
            position: absolute;
            bottom: -20px; right: 80px;
            width: 150px; height: 10px;
            background: #fbbf24;
        }
        .template_3 h1 {
            font-size: 44pt;
            font-weight: 800;
            margin: 0;
            text-transform: uppercase;
        }
        .template_3 .main-content {
            padding: 60px 80px;
        }
        .template_3 .recipient-name {
            font-size: 52pt;
            font-weight: bold;
            color: #1e1b4b;
            margin: 15px 0;
            background: linear-gradient(to right, #1e1b4b, #4338ca);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .template_3 .award-line {
            width: 120px;
            height: 4px;
            background: #fbbf24;
            margin: 20px 0;
        }

        /* Shared */
        .logo-row { margin-bottom: 10px; }
        .logo-item { height: 60px; margin: 0 15px; vertical-align: middle; }
        .cert-footer { position: absolute; bottom: 80px; width: 100%; left: 0; padding: 0 80px; box-sizing: border-box; }
        .sig-box { float: right; text-align: center; margin-left: 40px; }
        .sig-line { width: 180px; border-bottom: 1px solid #1e1b4b; margin: 10px auto; }
        .cert-id { position: absolute; bottom: 30px; right: 40px; font-size: 9pt; color: #94a3b8; }
        .verification-tag { position: absolute; bottom: 30px; left: 40px; font-size: 8pt; color: #94a3b8; font-family: monospace; letter-spacing: 1px; }
        .template_2 .verification-tag { left: 120px; }
    </style>
@if(!isset($is_preview) || !$is_preview)
</head>
<body>
@endif

    @php
        $template = ($context === 'event' ? ($event->certificate_template ?? 'template_1') : 'template_1');
        $title = $context === 'event' ? ($event->title ?? 'EVENT') : ($course->name ?? 'COURSE');
        $issuedDateText = ($issuedAt ?? now())->format('d F Y');
        $subtitle = $context === 'event' ? 'of Appreciation' : 'of Recognition';
    @endphp

    <div class="certificate-page {{ $template }}">
        @if($template == 'template_1')
            <div class="inner-border"></div>
            <div class="corner-element corner-tl"></div>
            <div class="corner-element corner-tr"></div>
            <div class="corner-element corner-bl"></div>
            <div class="corner-element corner-br"></div>
        @elseif($template == 'template_2')
            <div class="sidebar"></div>
            <div class="gold-accent"></div>
            @php $mainLogoPath = public_path('aset/logo-idspora.png'); @endphp
            @if(file_exists($mainLogoPath))
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents($mainLogoPath)) }}" class="watermark">
            @endif
        @endif

        @if($template == 'template_3')
            <div class="header-bg">
                <div style="float: right;">
                    @foreach(($logosBase64 ?? []) as $logo)
                        <img src="{{ $logo }}" class="logo-item" style="filter: brightness(0) invert(1);">
                    @endforeach
                </div>
                <h1>Certificate</h1>
                <p style="color: #fbbf24; font-weight: bold; margin: 0;">TRAINER RECOGNITION</p>
            </div>
            <div class="main-content">
                <p style="font-size: 14pt; color: #64748b; margin: 0;">This certificate is proudly presented to</p>
                <div class="recipient-name">{{ strtoupper($user->name ?? 'TRAINER') }}</div>
                <div class="award-line"></div>
                <p style="font-size: 15pt; color: #1e293b; margin-top: 20px;">
                    for contribution as <strong>{{ $roleLabel ?? 'Narasumber' }}</strong>
                </p>
                <h2 style="font-size: 28pt; color: #1e1b4b; margin: 10px 0; font-weight: 800;">{{ strtoupper($title) }}</h2>
                <p style="font-size: 11pt; color: #64748b; margin-top: 20px;">Issued by idSpora on {{ $issuedDateText }}</p>
            </div>
        @else
            <div class="header" style="{{ $template == 'template_2' ? 'padding: 80px 80px 0 120px; text-align: left;' : '' }}">
                <div class="logo-row" style="{{ $template == 'template_2' ? 'margin-bottom: 20px;' : '' }}">
                    @php $mainLogoPath = public_path('aset/logo-idspora.png'); @endphp
                    @if(file_exists($mainLogoPath))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($mainLogoPath)) }}" class="logo-item" style="height: {{ $template == 'template_2' ? '50px' : '60px' }};">
                    @else
                        <img src="{{ asset('aset/logo-idspora.png') }}" class="logo-item" style="height: {{ $template == 'template_2' ? '50px' : '60px' }};">
                    @endif

                    @foreach(($logosBase64 ?? []) as $logo)
                        <img src="{{ $logo }}" class="logo-item">
                    @endforeach
                </div>

                <h1 style="{{ $template == 'template_2' ? 'font-size: 56pt;' : '' }}">Certificate</h1>
                <p style="color: #fbbf24; font-weight: bold; letter-spacing: 5px; font-size: 16pt; margin: 0; text-transform: uppercase;">
                    {{ $subtitle }}
                </p>
                @if($template == 'template_1')
                    <div style="width: 200px; height: 2px; background: #fbbf24; margin: 15px auto;"></div>
                @elseif($template == 'template_2')
                    <div class="sub-title">TRAINER RECOGNITION</div>
                @endif
            </div>

            <div class="content" style="{{ $template == 'template_2' ? 'padding: 0 80px 0 120px; text-align: left;' : '' }}">
                <p style="font-size: 16pt; color: #64748b; margin: 30px 0 10px;">
                    This certificate is presented to
                </p>
                <div class="recipient-name">{{ strtoupper($user->name ?? 'TRAINER') }}</div>
                <p style="font-size: 15pt; color: #1e293b; margin-top: 10px;">
                    for contribution as <strong>{{ $roleLabel ?? 'Narasumber' }}</strong> in
                </p>
                <h2 style="font-size: 28pt; color: #1e1b4b; margin: 10px 0; font-weight: 800;">
                    {{ strtoupper($title) }}
                </h2>
                <p style="font-size: 11pt; color: #64748b; margin-top: 10px;">Issued by idSpora on {{ $issuedDateText }}</p>
            </div>
        @endif

        <div class="cert-footer" style="{{ $template == 'template_2' ? 'padding: 0 80px 0 120px;' : '' }}">
            @php $sigs = $signaturesBase64 ?? []; @endphp
            @foreach($sigs as $sig)
                <div class="sig-box">
                    <img src="{{ $sig }}" style="height: 60px; width: auto; display: block; margin: 0 auto 6px;">
                    <div class="sig-line"></div>
                    <div style="font-size: 10pt; color: #334155;">Authorized Sign</div>
                </div>
            @endforeach
        </div>

        <div class="verification-tag">IDSPORA • TRAINER CERTIFICATE</div>
        <div class="cert-id">{{ $certificateNumber ?? '' }}</div>
    </div>

@if(!isset($is_preview) || !$is_preview)
</body>
</html>
@endif

