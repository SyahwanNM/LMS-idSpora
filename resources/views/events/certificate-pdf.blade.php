<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat - {{ $event->title }}</title>
    <style>
        @page { 
            margin: 0;
            size: A4 landscape;
        }
        body { 
            font-family: DejaVu Sans, Arial, sans-serif; 
            color: #1e293b;
            background: #fff;
            margin: 0;
            padding: 30px 40px;
        }
        .cert-container {
            width: 100%;
            padding: 0;
            background: #fff;
        }
        .cert-content {
            width: 100%;
        }
        .cert-inner {
            max-width: 100%;
            margin: 0 auto;
            text-align: center;
        }
        .cert-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .cert-header h1 {
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin: 0 0 6px 0;
            color: #1e293b;
            text-align: center;
        }
        .cert-divider-wrapper {
            margin: 12px auto 0;
            text-align: center;
            width: 100%;
        }
        .cert-divider-top {
            height: 2px;
            width: 180px;
            background: #f4c430;
            margin: 0 auto 2px;
            border-radius: 2px;
        }
        .cert-divider-bottom {
            height: 2px;
            width: 220px;
            background: #535088;
            margin: 0 auto;
            border-radius: 2px;
        }
        .badge-number {
            display: inline-block;
            margin-top: 14px;
            background: #535088;
            color: #f4d24b;
            padding: 7px 16px;
            font-weight: 600;
            border-radius: 40px;
            letter-spacing: 0.5px;
            font-size: 11px;
            text-align: center;
        }
        .cert-body {
            margin-top: 22px;
            text-align: center;
        }
        .cert-body p.lead {
            font-size: 14px;
            color: #475569;
            margin: 0 auto 10px;
            text-align: center;
            width: 100%;
        }
        .cert-body p.lead.mb-1 {
            margin-bottom: 3px;
        }
        .cert-body h2 {
            font-size: 24px;
            font-weight: 600;
            margin: 14px auto 5px;
            color: #1e293b;
            text-align: center;
            width: 100%;
        }
        .cert-body h3 {
            font-size: 19px;
            font-weight: 600;
            color: #111;
            margin: 14px auto 8px;
            text-align: center;
            width: 100%;
        }
        .cert-body p.lead.mt-3 {
            margin-top: 10px;
        }
        .cert-meta {
            margin-top: 24px;
            text-align: center;
            width: 100%;
        }
        .meta-table {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: 14px;
        }
        .meta-table td {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 11px 16px;
            border-radius: 10px;
            text-align: left;
            vertical-align: top;
            width: 25%;
        }
        .meta-table td h6 {
            font-size: 10px;
            font-weight: 600;
            color: #64748b;
            margin: 0 0 4px 0;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .meta-table td p {
            margin: 0;
            font-weight: 500;
            color: #0f172a;
            font-size: 12px;
        }
        .cert-logo {
            width: 120px;
            height: 60px;
            margin: 0 8px 14px;
            display: inline-block;
            object-fit: contain;
        }
        .cert-logos-container {
            text-align: center;
            margin-bottom: 14px;
        }
        .cert-footer {
            display: table;
            width: 100%;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
        }
        .cert-signature-section {
            display: table-cell;
            text-align: center;
            vertical-align: bottom;
            width: 50%;
        }
        .cert-signature {
            width: 140px;
            height: 55px;
            margin: 0 8px;
            display: inline-block;
            object-fit: contain;
        }
        .cert-signatures-container {
            text-align: center;
        }
        .cert-signature-section h6 {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
            margin-top: 8px;
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="cert-content">
            <div class="cert-inner">
                @if(!empty($logosBase64) && count($logosBase64) > 0)
                <div class="cert-logos-container">
                    @foreach($logosBase64 as $logoBase64)
                    <img src="{{ $logoBase64 }}" alt="Logo" class="cert-logo">
                    @endforeach
                </div>
                @endif
                <div class="cert-header">
                    <h1>Sertifikat Kehadiran</h1>
                    <div class="cert-divider-wrapper">
                        <div class="cert-divider-top"></div>
                        <div class="cert-divider-bottom"></div>
                    </div>
                    <div class="badge-number">{{ $certificateNumber }}</div>
                </div>
                <div class="cert-body">
                    <p class="lead mb-1">Diberikan kepada</p>
                    <h2>{{ strtoupper($user->name) }}</h2>
                    <p class="lead">Sebagai peserta yang telah mengikuti event:</p>
                    <h3>"{{ $event->title }}"</h3>
                    <p class="lead mt-3">Dikeluarkan pada: <strong>{{ $issuedAt->format('d F Y') }}</strong></p>
                </div>
                <div class="cert-meta">
                    <table class="meta-table" align="center">
                        <tr>
                            <td>
                                <h6>Tanggal Event</h6>
                                <p>{{ $event->event_date?->format('d F Y') ?? '-' }}</p>
                            </td>
                            <td>
                                <h6>Waktu</h6>
                                <p>{{ $event->event_time?->format('H:i') ?? '-' }} WIB</p>
                            </td>
                            <td>
                                <h6>Lokasi</h6>
                                <p>{{ $event->location ?? 'Online' }}</p>
                            </td>
                            <td>
                                <h6>Peserta</h6>
                                <p>{{ $user->name }}</p>
                            </td>
                        </tr>
                    </table>
                </div>
                @if(!empty($signaturesBase64) && count($signaturesBase64) > 0)
                <div class="cert-footer">
                    <div class="cert-signature-section" style="width:50%;"></div>
                    <div class="cert-signature-section" style="width:50%;">
                        <div class="cert-signatures-container">
                            @foreach($signaturesBase64 as $signatureBase64)
                            <div style="display:inline-block;text-align:center;margin:0 8px;">
                                <img src="{{ $signatureBase64 }}" alt="Tanda Tangan" class="cert-signature">
                                <h6>Direktur</h6>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
