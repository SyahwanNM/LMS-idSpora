@if(!isset($is_preview) || !$is_preview)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Trainer</title>
@endif
<style>
    @if(!isset($is_preview) || !$is_preview)
        @page { size: A4 landscape; margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: 'Georgia', 'Times New Roman', serif; }
    @endif

    .certificate-page {
        width: 29.7cm;
        height: 21cm;
        position: relative;
        overflow: hidden;
        background: #fff;
        color: #1e1b4b;
        box-sizing: border-box;
        @if(isset($is_preview) && $is_preview)
        transform: scale(var(--cert-scale, 1));
        transform-origin: top left;
        @endif
    }

    /* ─── Shared Layout Classes ─── */
    .tpl-inner {
        position: relative;
        height: 100%;
        width: 100%;
        padding: 36px 56px;
        text-align: center;
        color: #1e1b4b;
        box-sizing: border-box;
    }

    .tpl-logo-row {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .tpl-logo {
        height: 42px;
        max-width: 140px;
        object-fit: contain;
    }

    .tpl-title {
        font-family: 'Georgia', serif;
        font-size: 38px;
        letter-spacing: 4px;
        margin: 6px 0 8px;
        text-transform: uppercase;
        font-weight: 700;
    }

    .tpl-subtitle {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #fbbf24;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .tpl-presented {
        font-size: 14px;
        color: #64748b;
        margin: 10px 0 4px;
        font-style: italic;
    }

    .tpl-name {
        font-size: 44px;
        font-weight: 700;
        margin: 8px 0;
        font-family: 'Times New Roman', serif;
    }

    .tpl-desc {
        font-size: 13px;
        line-height: 1.65;
        color: #1f2937;
        margin-top: 6px;
    }

    .tpl-footer {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 32px;
        align-items: flex-end;
    }

    .tpl-sig {
        text-align: center;
        min-width: 160px;
        font-size: 11px;
        color: #1e1b4b;
    }

    .tpl-sig img {
        max-height: 50px;
        max-width: 140px;
        object-fit: contain;
        display: block;
        margin: 0 auto 4px;
    }

    .tpl-sig-line {
        border-top: 1px solid #1e1b4b;
        margin: 4px 0;
    }

    .tpl-cert-id {
        position: absolute;
        bottom: 18px;
        right: 32px;
        font-size: 8px;
        color: #94a3b8;
        font-family: monospace;
        letter-spacing: 1px;
    }

    .tpl-verification {
        position: absolute;
        bottom: 18px;
        left: 32px;
        font-size: 8px;
        color: #94a3b8;
        font-family: monospace;
        letter-spacing: 1px;
    }

    /* ─── Template 1: Royal ─── */
    .template_1 {
        border: 14px solid #1e1b4b;
        background: #fff;
        height: 21cm;
        position: relative;
    }

    .template_1 .tpl-inner {
        border: 2px double #fbbf24;
        margin: 12px;
        height: calc(100% - 24px);
        width: calc(100% - 24px);
        box-sizing: border-box;
    }

    .template_1 .tpl-name {
        border-bottom: 2px solid #fbbf24;
        display: inline-block;
        padding: 4px 32px;
    }

    /* ─── Template 2: Modern ─── */
    .template_2 {
        background: #fff;
        border: 1px solid #e2e8f0;
        height: 21cm;
        position: relative;
    }

    .template_2::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 70px;
        background: #1e1b4b;
    }

    .template_2::after {
        content: '';
        position: absolute;
        inset: 0 auto 0 70px;
        width: 7px;
        background: #fbbf24;
    }

    .template_2 .tpl-inner {
        text-align: left;
        padding: 40px 60px 36px 120px;
    }

    .template_2 .tpl-logo-row {
        justify-content: flex-start;
    }

    .template_2 .tpl-name {
        border-left: 6px solid #fbbf24;
        padding-left: 14px;
    }

    .template_2 .tpl-footer {
        justify-content: flex-start;
    }

    .template_2 .tpl-verification {
        left: 120px;
    }

    /* ─── Template 3: Clean ─── */
    .template_3 {
        background: #f8fafc;
        height: 21cm;
        position: relative;
    }

    .template_3 .tpl-header {
        background: #1e1b4b;
        color: #fff;
        padding: 20px 56px;
    }

    .template_3 .tpl-header .tpl-title {
        font-size: 28px;
        margin: 0;
        color: #fff;
        letter-spacing: 2px;
    }

    .template_3 .tpl-bar {
        height: 7px;
        background: #fbbf24;
    }

    .template_3 .tpl-inner {
        text-align: left;
        padding: 28px 56px;
        height: calc(100% - 80px);
        box-sizing: border-box;
    }

    .template_3 .tpl-logo-row {
        justify-content: flex-start;
    }

    .template_3 .tpl-footer {
        justify-content: flex-start;
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
    $idSporaLogoPath = public_path('aset/logo-idspora.png');
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
@endphp

<div class="certificate-page {{ $template }}">

    {{-- ═══ TEMPLATE 1: Royal ═══ --}}
    @if($template === 'template_1')
        <div class="tpl-inner">
            <div class="tpl-logo-row">
                @foreach($allLogos as $logo)
                    <img class="tpl-logo" src="{{ $logo }}" alt="Logo">
                @endforeach
            </div>

            <div class="tpl-title">Sertifikat Penghargaan</div>
            <div class="tpl-subtitle">{{ strtoupper($role) }}</div>
            <div class="tpl-name">{{ $trainerName }}</div>

            <div class="tpl-desc">
                Atas kontribusinya sebagai {{ strtolower($role) }} dalam <strong>{{ $title }}</strong>, diterbitkan pada {{ $issuedDateText }}.
            </div>

            <div class="tpl-footer">
                @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64 ?? []); @endphp
                @foreach($sigsToRender as $sig)
                    <div class="tpl-sig">
                        @if(!empty($sig['base64']))
                            <img src="{{ $sig['base64'] }}" alt="Tanda Tangan">
                        @endif
                        <div class="tpl-sig-line"></div>
                        <strong>{{ $sig['name'] ?: 'Admin idSpora' }}</strong><br>
                        {{ $sig['position'] ?: 'Learning Manager' }}
                    </div>
                @endforeach
            </div>

            <div class="tpl-verification">IDSPORA • TRAINER CERTIFICATE</div>
            <div class="tpl-cert-id">{{ $certNum }}</div>
        </div>

    {{-- ═══ TEMPLATE 2: Modern ═══ --}}
    @elseif($template === 'template_2')
        <div class="tpl-inner">
            <div class="tpl-logo-row">
                @foreach($allLogos as $logo)
                    <img class="tpl-logo" src="{{ $logo }}" alt="Logo">
                @endforeach
            </div>

            <div class="tpl-title">Sertifikat Penghargaan</div>
            <div class="tpl-subtitle">{{ strtoupper($role) }}</div>
            <div class="tpl-name">{{ $trainerName }}</div>

            <div class="tpl-desc">
                Atas kontribusinya sebagai {{ strtolower($role) }} dalam <strong>{{ $title }}</strong>, diterbitkan pada {{ $issuedDateText }}.
            </div>

            <div class="tpl-footer">
                @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64 ?? []); @endphp
                @foreach($sigsToRender as $sig)
                    <div class="tpl-sig">
                        @if(!empty($sig['base64']))
                            <img src="{{ $sig['base64'] }}" alt="Tanda Tangan">
                        @endif
                        <div class="tpl-sig-line"></div>
                        <strong>{{ $sig['name'] ?: 'Admin idSpora' }}</strong><br>
                        {{ $sig['position'] ?: 'Learning Manager' }}
                    </div>
                @endforeach
            </div>

            <div class="tpl-verification">IDSPORA • TRAINER CERTIFICATE</div>
            <div class="tpl-cert-id">{{ $certNum }}</div>
        </div>

    {{-- ═══ TEMPLATE 3: Clean ═══ --}}
    @else
        <div class="tpl-header">
            <div class="tpl-title">Sertifikat Penghargaan</div>
        </div>
        <div class="tpl-bar"></div>
        <div class="tpl-inner">
            @if(!empty($allLogos))
                <div class="tpl-logo-row">
                    @foreach($allLogos as $logo)
                        <img class="tpl-logo" src="{{ $logo }}" alt="Logo">
                    @endforeach
                </div>
            @endif

            <div class="tpl-subtitle">{{ strtoupper($role) }}</div>
            <div class="tpl-name">{{ $trainerName }}</div>

            <div class="tpl-desc">
                Atas kontribusinya sebagai {{ strtolower($role) }} dalam <strong>{{ $title }}</strong>, diterbitkan pada {{ $issuedDateText }}.
            </div>

            <div class="tpl-footer">
                @php $sigsToRender = !empty($signaturesData) ? $signaturesData : array_map(fn($b) => ['base64'=>$b,'name'=>'','position'=>''], $signaturesBase64 ?? []); @endphp
                @foreach($sigsToRender as $sig)
                    <div class="tpl-sig">
                        @if(!empty($sig['base64']))
                            <img src="{{ $sig['base64'] }}" alt="Tanda Tangan">
                        @endif
                        <div class="tpl-sig-line"></div>
                        <strong>{{ $sig['name'] ?: 'Admin idSpora' }}</strong><br>
                        {{ $sig['position'] ?: 'Learning Manager' }}
                    </div>
                @endforeach
            </div>

            <div class="tpl-verification">IDSPORA • TRAINER CERTIFICATE</div>
            <div class="tpl-cert-id">{{ $certNum }}</div>
        </div>
    @endif

</div>

@if(!isset($is_preview) || !$is_preview)
    </body>
    </html>
@endif