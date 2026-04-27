<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sertifikat Kursus</title>
<style>
@page {
    size: 297mm 155mm;
    margin: 0;
}
* {
    box-sizing: border-box;
    -webkit-print-color-adjust: exact;
}
html, body {
    margin: 0;
    padding: 0;
    width: 297mm;
    height: 155mm;
    background: white;
    font-family: 'Helvetica', 'Arial', sans-serif;
}

/* Full page table layout */
.page-wrap {
    width: 297mm;
    height: 155mm;
    display: table;
}
.page-cell {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    padding: 6mm 10mm;
}

/* ── Template 1: Premium Royal ── */
.template_1 {
    border: 16px solid #1e1b4b;
    position: relative;
    padding: 18px 28px 16px;
    text-align: center;
    background: white;
    display: inline-block;
    width: 100%;
}
.template_1 .inner-border {
    position: absolute;
    top: 8px; left: 8px; right: 8px; bottom: 8px;
    border: 2px double #fbbf24;
    pointer-events: none;
}
.template_1 h1 {
    font-family: 'Georgia', serif;
    font-size: 24pt;
    color: #1e1b4b;
    margin: 4px 0 2px;
    text-transform: uppercase;
    letter-spacing: 3px;
}
.template_1 .recipient-name {
    font-size: 22pt;
    font-weight: bold;
    color: #1e1b4b;
    border-bottom: 2px solid #fbbf24;
    display: inline-block;
    padding: 3px 36px;
    margin: 6px 0;
    font-family: 'Times New Roman', serif;
}

/* ── Template 2: Modern Corporate ── */
.template_2 {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    position: relative;
    display: inline-block;
    width: 100%;
    text-align: left;
}
.template_2 .sidebar {
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 50px;
    background: #1e1b4b;
}
.template_2 .gold-accent {
    position: absolute;
    left: 50px; top: 0; bottom: 0;
    width: 5px;
    background: #fbbf24;
}
.template_2 .content-wrap {
    margin-left: 72px;
    padding: 22px 36px 18px 24px;
}
.template_2 h1 {
    font-size: 26pt;
    font-weight: 800;
    color: #1e1b4b;
    margin: 0;
    letter-spacing: -1px;
}
.template_2 .sub-title {
    font-size: 10pt;
    color: #fbbf24;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 3px;
    margin-bottom: 10px;
}
.template_2 .recipient-name {
    font-size: 22pt;
    font-weight: 900;
    color: #1e1b4b;
    margin: 6px 0;
    border-left: 6px solid #fbbf24;
    padding-left: 12px;
}

/* ── Template 3: Creative Professional ── */
.template_3 {
    background: #f8fafc;
    display: inline-block;
    width: 100%;
    text-align: left;
}
.template_3 .header-bg {
    background: #1e1b4b;
    padding: 18px 50px;
    color: white;
}
.template_3 h1 {
    font-size: 22pt;
    font-weight: 800;
    margin: 0;
    text-transform: uppercase;
}
.template_3 .main-content {
    padding: 16px 50px;
}
.template_3 .recipient-name {
    font-size: 24pt;
    font-weight: bold;
    color: #1e1b4b;
    margin: 6px 0;
}
.template_3 .award-line {
    width: 90px;
    height: 3px;
    background: #fbbf24;
    margin: 8px 0;
}

/* ── Shared ── */
.logo-row { text-align: center; margin-bottom: 4px; width: 100%; }
.logo-container { display: inline-block; vertical-align: middle; }
.logo-item { height: 32px; width: auto; margin: 0 5px; vertical-align: middle; }

/* Footer as normal flow — no absolute */
.cert-footer {
    margin-top: 16px;
    padding: 0 28px;
    overflow: hidden;
}
.sig-block { float: right; text-align: center; margin-left: 20px; }
.sig-line { width: 120px; border-bottom: 1px solid #1e1b4b; margin: 4px auto; }
.clearfix::after { content: ''; display: table; clear: both; }

.cert-meta {
    margin-top: 6px;
    padding: 0 28px;
    display: table;
    width: 100%;
}
.cert-meta-left {
    display: table-cell;
    text-align: left;
    font-size: 6pt;
    color: #94a3b8;
    font-family: monospace;
    letter-spacing: 1px;
    vertical-align: middle;
}
.cert-meta-right {
    display: table-cell;
    text-align: right;
    font-size: 6.5pt;
    color: #94a3b8;
    vertical-align: middle;
}
.cert-id-badge {
    background: rgba(251,191,36,0.1);
    padding: 2px 6px;
    border-radius: 3px;
    display: inline-block;
}
</style>
</head>
<body>
<div class="page-wrap">
<div class="page-cell">

<?php $template = $course->certificate_template ?? 'template_1'; ?>

<?php if($template === 'template_1'): ?>
<div class="template_1">
    <div class="inner-border"></div>

    <div class="logo-row">
        <div class="logo-container">
            <?php $mainLogoPath = public_path('aset/logo-idspora.png'); ?>
            <?php if(file_exists($mainLogoPath)): ?>
                <img src="data:image/png;base64,<?= base64_encode(file_get_contents($mainLogoPath)) ?>" class="logo-item">
            <?php endif; ?>
            <?php foreach(array_slice($logosBase64, 0, 3) as $logo): ?>
                <img src="<?= $logo ?>" class="logo-item">
            <?php endforeach; ?>
        </div>
    </div>

    <h1>Course Certificate</h1>
    <p style="color:#fbbf24;font-weight:bold;letter-spacing:4px;font-size:9pt;margin:2px 0;text-transform:uppercase;">Certificate of Completion</p>
    <div style="width:140px;height:2px;background:#fbbf24;margin:6px auto;"></div>

    <p style="font-size:10pt;color:#64748b;font-style:italic;margin:10px 0 2px;">This is to certify that</p>
    <div class="recipient-name"><?= strtoupper($user->name) ?></div>
    <p style="font-size:9.5pt;line-height:1.4;color:#1e293b;margin:6px 0;">has successfully completed all requirements of the course</p>
    <h2 style="font-size:15pt;color:#1e1b4b;margin:5px 0;font-family:'Georgia',serif;">"<?= $course->name ?>"</h2>
    <p style="font-size:7.5pt;color:#64748b;margin:3px 0 14px;">Issued on <?= $issuedAt->format('d F Y') ?> by idSpora Academy</p>

    <div class="cert-footer clearfix">
        <div style="float:right;">
            <?php if(!empty($signaturesBase64)): ?>
                <?php foreach($signaturesBase64 as $sig): ?>
                <div class="sig-block">
                    <img src="<?= $sig ?>" style="height:48px;width:auto;display:block;margin:0 auto;">
                    <div class="sig-line"></div>
                    <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">Authorized Signature</p>
                    <p style="font-size:6.5pt;color:#64748b;margin:0;">Academy Director</p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="sig-block">
                    <div style="height:48px;"></div>
                    <div class="sig-line"></div>
                    <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">Authorized Signature</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="cert-meta">
        <div class="cert-meta-left">VERIFIED BY IDSPORA.COM ACADEMY</div>
        <div class="cert-meta-right"><span class="cert-id-badge">ID: <?= $certificateNumber ?></span></div>
    </div>
</div>

<?php elseif($template === 'template_2'): ?>
<div class="template_2">
    <div class="sidebar"></div>
    <div class="gold-accent"></div>

    <div class="content-wrap">
        <div class="logo-row" style="text-align:left;margin-bottom:6px;">
            <div class="logo-container">
                <?php $mainLogoPath = public_path('aset/logo-idspora.png'); ?>
                <?php if(file_exists($mainLogoPath)): ?>
                    <img src="data:image/png;base64,<?= base64_encode(file_get_contents($mainLogoPath)) ?>" class="logo-item">
                <?php endif; ?>
                <?php foreach($logosBase64 as $logo): ?>
                    <img src="<?= $logo ?>" class="logo-item">
                <?php endforeach; ?>
            </div>
        </div>
        <h1>COURSE CERTIFICATE</h1>
        <div class="sub-title">Completion &amp; Mastery</div>
        <p style="font-size:10pt;color:#64748b;font-style:italic;margin-bottom:2px;">This is to certify that</p>
        <div class="recipient-name"><?= strtoupper($user->name) ?></div>
        <p style="font-size:9.5pt;line-height:1.4;color:#1e293b;margin-top:5px;">has successfully completed all requirements of the course</p>
        <h2 style="font-size:15pt;color:#1e1b4b;margin:5px 0;">"<?= $course->name ?>"</h2>
        <p style="font-size:7.5pt;color:#64748b;margin:3px 0 14px;">Issued on <?= $issuedAt->format('d F Y') ?> by idSpora Academy</p>

        <div class="cert-footer clearfix">
            <div style="float:right;">
                <?php if(!empty($signaturesBase64)): ?>
                    <?php foreach($signaturesBase64 as $sig): ?>
                    <div class="sig-block">
                        <img src="<?= $sig ?>" style="height:48px;width:auto;display:block;margin:0 auto;">
                        <div class="sig-line"></div>
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">Authorized Signature</p>
                        <p style="font-size:6.5pt;color:#64748b;margin:0;">Academy Director</p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="sig-block">
                        <div style="height:48px;"></div>
                        <div class="sig-line"></div>
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">Authorized Signature</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="cert-meta" style="padding:0;">
            <div class="cert-meta-left">VERIFIED BY IDSPORA.COM ACADEMY</div>
            <div class="cert-meta-right"><span class="cert-id-badge">ID: <?= $certificateNumber ?></span></div>
        </div>
    </div>
</div>

<?php else: /* template_3 */ ?>
<div class="template_3">
    <div class="header-bg">
        <div style="float:right;">
            <?php foreach($logosBase64 as $logo): ?>
                <img src="<?= $logo ?>" class="logo-item" style="filter:brightness(0) invert(1);">
            <?php endforeach; ?>
        </div>
        <h1>Course Certificate</h1>
        <p style="color:#fbbf24;font-weight:bold;margin:0;font-size:9pt;">PROFESSIONAL EDUCATION</p>
    </div>
    <div class="main-content">
        <p style="font-size:10pt;color:#64748b;margin:0;">This certificate is proudly awarded to</p>
        <div class="recipient-name"><?= strtoupper($user->name) ?></div>
        <div class="award-line"></div>
        <p style="font-size:10pt;color:#1e293b;margin-top:6px;">for successful completion and mastery of the online course</p>
        <h2 style="font-size:17pt;color:#1e1b4b;margin:5px 0;font-weight:800;"><?= strtoupper($course->name) ?></h2>
        <p style="font-size:7.5pt;color:#64748b;margin-top:6px 0 14px;">Issued by IdSPora Learning Academy on <?= $issuedAt->format('d F Y') ?></p>

        <div class="cert-footer clearfix">
            <div style="float:right;">
                <?php if(!empty($signaturesBase64)): ?>
                    <?php foreach($signaturesBase64 as $sig): ?>
                    <div class="sig-block">
                        <img src="<?= $sig ?>" style="height:48px;width:auto;display:block;margin:0 auto;">
                        <div class="sig-line"></div>
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">Authorized Signature</p>
                        <p style="font-size:6.5pt;color:#64748b;margin:0;">Academy Director</p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="sig-block">
                        <div style="height:48px;"></div>
                        <div class="sig-line"></div>
                        <p style="font-weight:bold;margin:0;font-size:7.5pt;color:#1e1b4b;">Authorized Signature</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="cert-meta" style="padding:0;">
            <div class="cert-meta-left">VERIFIED BY IDSPORA.COM ACADEMY</div>
            <div class="cert-meta-right"><span class="cert-id-badge">ID: <?= $certificateNumber ?></span></div>
        </div>
    </div>
</div>
<?php endif; ?>

</div><!-- .page-cell -->
</div><!-- .page-wrap -->
</body>
</html>
