<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Sertifikat Kursus</title><style>@page{size:A4 landscape;margin:15mm 20mm;}*{box-sizing:border-box;-webkit-print-color-adjust:exact;}html,body{margin:0;padding:0;background:white;font-family:'Helvetica','Arial',sans-serif;width:100%;height:100%;}.certificate-container{width:100%;display:table;height:100%;}.certificate-cell{display:table-cell;vertical-align:middle;text-align:center;}.certificate-page{width:240mm;height:160mm;margin:0 auto;position:relative;background:white;text-align:left;overflow:hidden;}.template_1{width:240mm;height:160mm;background:#1e1b4b;padding:7mm;}.template_1 .cert-inner{width:226mm;height:146mm;background:white;position:relative;padding:20px 30px;}.template_1 .inner-border{position:absolute;top:10px;left:10px;right:10px;bottom:10px;border:2px double #fbbf24;pointer-events:none;}.template_1 .header{text-align:center;}.template_1 h1{font-family:'Georgia',serif;font-size:28pt;color:#1e1b4b;margin:5px 0;text-transform:uppercase;letter-spacing:3px;}.template_1 .recipient-name{font-size:26pt;font-weight:bold;color:#1e1b4b;border-bottom:2px solid #fbbf24;display:inline-block;padding:4px 25px;margin:5px 0;font-family:'Times New Roman',serif;}.template_1 .content{text-align:center;}.template_2{width:240mm;height:160mm;background:#ffffff;border:1px solid #e2e8f0;position:relative;overflow:hidden;}.template_2 .sidebar{position:absolute;left:0;top:0;bottom:0;width:50px;background:#1e1b4b;}.template_2 .gold-accent{position:absolute;left:50px;top:0;bottom:0;width:5px;background:#fbbf24;}.template_2 .content-wrap{margin-left:75px;padding:35px 45px 35px 30px;}.template_2 h1{font-size:32pt;font-weight:800;color:#1e1b4b;margin:0;letter-spacing:-1px;}.template_2 .sub-title{font-size:11pt;color:#fbbf24;font-weight:700;text-transform:uppercase;letter-spacing:3px;margin-bottom:15px;}.template_2 .recipient-name{font-size:26pt;font-weight:900;color:#1e1b4b;margin:6px 0;border-left:6px solid #fbbf24;padding-left:12px;}.template_3{width:240mm;height:160mm;background:#f8fafc;position:relative;overflow:hidden;}.template_3 .header-bg{height:85px;background:#1e1b4b;padding:20px 45px;color:white;position:relative;}.template_3 h1{font-size:26pt;font-weight:800;margin:0;text-transform:uppercase;}.template_3 .main-content{padding:20px 45px 80px 45px;}.template_3 .recipient-name{font-size:28pt;font-weight:bold;color:#1e1b4b;margin:6px 0;}.logo-row{text-align:center;margin-bottom:5px;width:100%;}.logo-container{display:inline-block;vertical-align:middle;}.logo-item{height:30px;width:auto;margin:0 4px;vertical-align:middle;}.cert-footer{position:absolute;bottom:45px;right:40px;}.sig-box{display:block;text-align:center;width:160px;}.sig-line{width:160px;border-bottom:1.5px solid #1e1b4b;margin:5px auto;}.cert-id{position:absolute;bottom:4px;right:20px;font-size:7pt;color:#94a3b8;}.verification-tag{position:absolute;bottom:4px;left:20px;font-size:6pt;color:#94a3b8;font-family:monospace;letter-spacing:1px;}.template_2 .verification-tag{left:75px;}</style></head><body><div class="certificate-container"><div class="certificate-cell">
@php
    $template = $course->certificate_template ?? 'template_1';
@endphp
<div class="certificate-page {{ $template }}">
    @if($template == 'template_1')
    <div class="cert-inner">
        <div class="inner-border"></div>
        <div class="header">
                    <div class="logo-row">
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
            <h1>Course Certificate</h1>
            <p style="color:#fbbf24;font-weight:bold;letter-spacing:4px;font-size:12pt;margin:3px 0;text-transform:uppercase;">Certificate of Completion</p>
            <div style="width:140px;height:2px;background:#fbbf24;margin:10px auto;"></div>
        </div>
        <div class="content" style="margin-top:10px;">
            <p style="font-size:13pt;color:#64748b;font-style:italic;margin-bottom:3px;">This is to certify that</p>
            <div class="recipient-name">{{ strtoupper($user->name) }}</div>
            <p style="font-size:11pt;line-height:1.4;color:#1e293b;margin-top:7px;">has successfully completed all requirements of the course</p>
            <h2 style="font-size:18pt;color:#1e1b4b;margin:6px 0;font-family:'Georgia',serif;">"{{ $course->name }}"</h2>
            <p style="font-size:9pt;color:#64748b;">Issued on {{ $issuedAt->format('d F Y') }} by idSpora Academy</p>
        </div>

        {{-- Signature footer inside cert-inner for template_1 --}}
        <div class="cert-footer">
                @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64); @endphp
                @forelse($sigsToRender as $sig)
                    <div class="sig-box">
                        <img src="{{ $sig['base64'] }}" style="height:50px;width:auto;display:block;margin:0 auto;">
                        <div class="sig-line"></div>
                        @if(!empty($sig['name']))
                            <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">{{ $sig['name'] }}</p>
                            @if(!empty($sig['position']))
                                <p style="margin:1px 0 0;font-size:7pt;color:#64748b;font-style:italic;">{{ $sig['position'] }}</p>
                            @endif
                        @else
                            <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">Authorized Signature</p>
                        @endif
                    </div>
                @empty
                    <div class="sig-box">
                        <div style="height:50px;"></div>
                        <div class="sig-line"></div>
                        <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">Authorized Signature</p>
                    </div>
                @endforelse
        </div>
    </div>{{-- end cert-inner --}}

    @elseif($template == 'template_2')
        <div class="sidebar"></div>
        <div class="gold-accent"></div>
        <div class="content-wrap">
            <div class="logo-row" style="text-align:left;margin-bottom:10px;">
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
            <p style="font-size:13pt;color:#64748b;font-style:italic;margin-bottom:3px;">This is to certify that</p>
            <div class="recipient-name">{{ strtoupper($user->name) }}</div>
            <p style="font-size:11pt;color:#1e293b;margin-top:7px;">has successfully completed all requirements of the course</p>
            <h2 style="font-size:18pt;color:#1e1b4b;margin:6px 0;">"{{ $course->name }}"</h2>
            <p style="font-size:9pt;color:#64748b;">Issued on {{ $issuedAt->format('d F Y') }} by idSpora Academy</p>
        </div>{{-- end content-wrap --}}

        {{-- Signature footer for template_2 --}}
        <div class="cert-footer">
                @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64); @endphp
                @forelse($sigsToRender as $sig)
                    <div class="sig-box">
                        <img src="{{ $sig['base64'] }}" style="height:50px;width:auto;display:block;margin:0 auto;">
                        <div class="sig-line"></div>
                        @if(!empty($sig['name']))
                            <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">{{ $sig['name'] }}</p>
                            @if(!empty($sig['position']))
                                <p style="margin:1px 0 0;font-size:7pt;color:#64748b;font-style:italic;">{{ $sig['position'] }}</p>
                            @endif
                        @else
                            <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">Authorized Signature</p>
                        @endif
                    </div>
                @empty
                    <div class="sig-box">
                        <div style="height:50px;"></div>
                        <div class="sig-line"></div>
                        <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">Authorized Signature</p>
                    </div>
                @endforelse
        </div>

    @else{{-- template_3 --}}
        <div class="header-bg">
            <div style="float:right;">
                @php 
                    $logoFileName = ($template == 'template_3') ? 'logo-idspora.png' : 'logo idspora_dark.png';
                    $mainLogoPath = public_path('aset/' . $logoFileName); 
                @endphp
                @if(file_exists($mainLogoPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($mainLogoPath)) }}" class="logo-item" style="filter:brightness(0) invert(1);">
                @endif
                @foreach(array_slice($logosBase64, 0, 3) as $logo)
                    <img src="{{ $logo }}" class="logo-item" style="filter:brightness(0) invert(1);">
                @endforeach
            </div>
            <h1>Course Certificate</h1>
            <p style="color:#fbbf24;font-weight:bold;margin:0;font-size:11pt;">PROFESSIONAL EDUCATION</p>
        </div>
        <div class="main-content">
            <p style="font-size:12pt;color:#64748b;margin:0;">This certificate is proudly awarded to</p>
            <div class="recipient-name">{{ strtoupper($user->name) }}</div>
            <div class="award-line"></div>
            <p style="font-size:12pt;color:#1e293b;margin-top:10px;">for successful completion of the course</p>
            <h2 style="font-size:20pt;color:#1e1b4b;margin:6px 0;font-weight:800;">"{{ $course->name }}"</h2>
            <p style="font-size:9pt;color:#64748b;">Issued by IdSPora Academy on {{ $issuedAt->format('d F Y') }}</p>
        </div>

        {{-- Signature footer for template_3 --}}
        <div class="cert-footer">
                @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64); @endphp
                @forelse($sigsToRender as $sig)
                    <div class="sig-box">
                        <img src="{{ $sig['base64'] }}" style="height:50px;width:auto;display:block;margin:0 auto;">
                        <div class="sig-line"></div>
                        @if(!empty($sig['name']))
                            <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">{{ $sig['name'] }}</p>
                            @if(!empty($sig['position']))
                                <p style="margin:1px 0 0;font-size:7pt;color:#64748b;font-style:italic;">{{ $sig['position'] }}</p>
                            @endif
                        @else
                            <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">Authorized Signature</p>
                        @endif
                    </div>
                @empty
                    <div class="sig-box">
                        <div style="height:50px;"></div>
                        <div class="sig-line"></div>
                        <p style="font-weight:bold;margin:0;font-size:8pt;color:#1e1b4b;">Authorized Signature</p>
                    </div>
                @endforelse
        </div>
    @endif

    <div class="verification-tag">VERIFIED BY IDSPORA.COM ACADEMY</div>
    <div class="cert-id" style="background:rgba(251,191,36,0.1);padding:3px 6px;border-radius:3px;">ID: {{ $certificateNumber }}</div>
</div>
</div></div></body></html>

