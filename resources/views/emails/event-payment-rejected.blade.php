<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembayaran Ditolak</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                <tr>
                    <td style="padding:18px 20px;background:#111827;color:#ffffff;">
                        <div style="font-size:16px;font-weight:700;">IdSpora</div>
                        <div style="font-size:13px;opacity:.9;">Informasi verifikasi pembayaran</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p style="margin:0 0 10px 0;font-size:14px;color:#111827;">Halo {{ $userName }},</p>

                        <p style="margin:0 0 10px 0;font-size:14px;color:#111827;">
                            Pembayaran/registrasi Anda untuk event <strong>{{ $eventTitle }}</strong> ditolak oleh admin.
                        </p>

                        <p style="margin:0 0 6px 0;font-size:14px;color:#111827;"><strong>Alasan:</strong> {{ $reason }}</p>

                        <div style="margin:14px 0;padding:12px 14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;line-height:1.5;">
                            {{ $messageText }}
                        </div>

                        <p style="margin:0;font-size:13px;color:#6b7280;line-height:1.5;">
                            silahkan kontak admin IdSpora di nomor <strong>{{ $adminContactNumber }} untuk informasi lebih lanjut</strong>.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;background:#f3f4f6;color:#6b7280;font-size:12px;">
                        Email ini dikirim otomatis. Mohon tidak membalas kecuali diminta oleh admin.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
