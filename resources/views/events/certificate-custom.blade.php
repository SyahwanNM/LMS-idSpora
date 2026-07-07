@php
    $itemTitle = $event->title ?? $course->name ?? 'Program';
    $customTemplate = $event->certificate_custom_template ?? $course->certificate_custom_template ?? [];
    
    // Default background and elements if template is empty
    $bgColor = $customTemplate['background']['color'] ?? '#ffffff';
    $bgGradient = $customTemplate['background']['gradient'] ?? null;
    $elements = $customTemplate['elements'] ?? [];

    $dateStr = isset($issuedAt) ? \Carbon\Carbon::parse($issuedAt)->translatedFormat('d F Y') : now()->translatedFormat('d F Y');
    $certNo = $certificateNumber ?? 'CERT-SAMPLE-1234';
    $userName = $user->name ?? 'Nama Peserta';

    // Helper to replace place holders
    $replaceVars = function($text) use ($userName, $itemTitle, $dateStr, $certNo) {
        $text = str_replace('{{nama}}', $userName, $text);
        $text = str_replace('{{event}}', $itemTitle, $text);
        $text = str_replace('{{course}}', $itemTitle, $text);
        $text = str_replace('{{tanggal}}', $dateStr, $text);
        $text = str_replace('{{nomor_sertifikat}}', $certNo, $text);
        return $text;
    };
@endphp
@if(!isset($is_preview) || !$is_preview)
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        @font-face {
            font-family: 'Great Vibes';
            font-style: normal;
            font-weight: 400;
            src: url(https://fonts.gstatic.com/s/greatvibes/v15/RWmUzKKvo7wSSA9A72-LKiS4WKGwQA.ttf) format('truetype');
        }
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
            height: 210mm;
            overflow: hidden;
        }
        .certificate-page {
            width: 297mm;
            height: 210mm;
            position: relative;
            overflow: hidden;
        }
    </style>
</head>
<body>
@endif

<div class="certificate-page" style="
    background: {{ $bgGradient ? $bgGradient : $bgColor }};
    @if(isset($is_preview) && $is_preview)
        position: relative; top: 0; left: 0; width: 100%; height: 100%;
        transform: scale(var(--cert-scale, 1)); transform-origin: top left;
    @endif
">
    @if(!empty($customTemplate['background']['image']))
        <img src="{{ $customTemplate['background']['image'] }}" style="position: absolute; left: 0; top: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;">
    @endif

    @foreach($elements as $el)
        @php
            // Calculate absolute position based on canvas dimensions in builder: 1000px width x 706px height
            // We convert to percentages or mm for absolute layout. Let's use percentages for absolute layout so it's fully fluid and looks identical.
            $left = ($el['x'] / 1000) * 100;
            $top = ($el['y'] / 706) * 100;
            $width = isset($el['width']) ? (($el['width'] / 1000) * 100) . '%' : 'auto';
            $height = isset($el['height']) ? (($el['height'] / 706) * 100) . '%' : 'auto';
            
            $elStyle = "position: absolute; left: {$left}%; top: {$top}%; z-index: " . ($el['zIndex'] ?? 1) . ";";
        @endphp

        @if($el['type'] === 'text' || $el['type'] === 'variable')
            @php
                $fontFamily = $el['fontFamily'] ?? 'Helvetica';
                // Convert pixel font size to point size (A4 Landscape target). Canvas is 706px high, PDF is 595.27pt high.
                // Ratio: 595.27 / 706 = 0.843
                $fontSizePt = ($el['fontSize'] ?? 14) * 0.843;
                $color = $el['color'] ?? '#1e293b';
                $bold = !empty($el['bold']) ? 'font-weight: bold;' : '';
                $italic = !empty($el['italic']) ? 'font-style: italic;' : '';
                $underline = !empty($el['underline']) ? 'text-decoration: underline;' : '';
                $align = $el['align'] ?? 'left';
            @endphp
            <div style="{{ $elStyle }} width: {{ $width }}; text-align: {{ $align }}; font-family: '{{ $fontFamily }}', sans-serif; font-size: {{ $fontSizePt }}pt; color: {{ $color }}; {{ $bold }} {{ $italic }} {{ $underline }} line-height: 1.2;">
                {!! nl2br(e($replaceVars($el['content'] ?? ''))) !!}
            </div>
        @elseif($el['type'] === 'logo' || $el['type'] === 'shape')
            @php
                $imgSrc = $el['src'] ?? '';
                if (str_starts_with($imgSrc, 'storage/')) {
                    $imgSrc = str_replace('storage/', '', $imgSrc);
                }
                
                // Use existing base64 (e.g. for vector ornaments) or fallback to storage file path
                $logoBase64 = $el['base64'] ?? '';
                if (!$logoBase64 && $imgSrc && Storage::disk('public')->exists($imgSrc)) {
                    $mime = Storage::disk('public')->mimeType($imgSrc);
                    $logoBase64 = "data:$mime;base64," . base64_encode(Storage::disk('public')->get($imgSrc));
                }
            @endphp
            @if($logoBase64)
                <div style="{{ $elStyle }} width: {{ $width }}; height: {{ $height }}; text-align: center;">
                    <img src="{{ $logoBase64 }}" style="width: 100%; height: 100%; display: block;">
                </div>
            @endif
        @elseif($el['type'] === 'signature')
            @php
                $imgSrc = $el['src'] ?? '';
                if (str_starts_with($imgSrc, 'storage/')) {
                    $imgSrc = str_replace('storage/', '', $imgSrc);
                }
                
                // Fetch base64 of the image for rendering in dompdf
                $sigBase64 = '';
                if ($imgSrc && Storage::disk('public')->exists($imgSrc)) {
                    $mime = Storage::disk('public')->mimeType($imgSrc);
                    $sigBase64 = "data:$mime;base64," . base64_encode(Storage::disk('public')->get($imgSrc));
                }
                $sigName = $el['name'] ?? '';
                $sigPos = $el['position'] ?? '';
            @endphp
            <div style="{{ $elStyle }} width: {{ $width }}; text-align: center; font-family: 'Helvetica', sans-serif;">
                @if($sigBase64)
                    <img src="{{ $sigBase64 }}" style="width: auto; height: 50pt; max-height: 70px; display: block; margin: 0 auto 5px;">
                @else
                    <div style="height: 50pt; max-height: 70px;"></div>
                @endif
                <div style="width: 80%; border-bottom: 1.5px solid #000; margin: 5px auto;"></div>
                @if($sigName)
                    <div style="font-size: 11pt; font-weight: bold; color: #1e1b4b; margin: 2px 0;">{{ $sigName }}</div>
                @endif
                @if($sigPos)
                    <div style="font-size: 9pt; color: #64748b; font-style: italic;">{{ $sigPos }}</div>
                @endif
            </div>
        @elseif($el['type'] === 'box')
            @php
                $boxBgColor = $el['bgColor'] ?? 'transparent';
                $boxBorderColor = $el['borderColor'] ?? 'none';
                $boxBorderWidth = isset($el['borderWidth']) ? $el['borderWidth'] . 'px' : '0px';
                $boxBorderStyle = $el['borderStyle'] ?? 'solid';
                $boxRadius = isset($el['borderRadius']) ? $el['borderRadius'] . 'px' : '0px';
            @endphp
            <div style="{{ $elStyle }} width: {{ $width }}; height: {{ $height }}; background: {{ $boxBgColor }}; border: {{ $boxBorderWidth }} {{ $boxBorderStyle }} {{ $boxBorderColor }}; border-radius: {{ $boxRadius }};"></div>
        @endif
    @endforeach
</div>

@if(!isset($is_preview) || !$is_preview)
</body>
</html>
@endif
