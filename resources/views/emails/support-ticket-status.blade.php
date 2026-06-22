<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pembaruan Status Tiket Dukungan</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f6f7fb;padding:24px 12px;">
    <tr>
        <td align="center">
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;">
                <tr>
                    <td style="padding:18px 20px;background:#111827;color:#ffffff;">
                        <div style="font-size:16px;font-weight:700;">idSpora CRM</div>
                        <div style="font-size:13px;opacity:.9;">Pembaruan Status Tiket Dukungan</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px;">
                        <p style="margin:0 0 10px 0;font-size:14px;color:#111827;">Halo {{ $ticket->name }},</p>

                        <p style="margin:0 0 14px 0;font-size:14px;color:#111827;line-height:1.5;">
                            Kami ingin menginformasikan bahwa tiket dukungan Anda dengan ID <strong>#SPT-{{ str_pad($ticket->id, 5, '0', STR_PAD_LEFT) }}</strong> telah diperbarui oleh Admin menjadi status: <strong style="color: {{ $ticket->status === 'resolved' ? '#10b981' : '#ef4444' }};">{{ strtoupper($statusLabel) }}</strong>.
                        </p>

                        <div style="margin:14px 0;padding:12px 14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px;font-size:14px;color:#111827;line-height:1.6;">
                            <strong>Keterangan Admin:</strong><br>
                            {{ $messageText }}
                        </div>

                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:16px 0;border-top:1px solid #e5e7eb;padding-top:12px;font-size:13px;color:#4b5563;">
                            <tr>
                                <td style="padding:4px 0;width:120px;"><strong>Subjek Tiket:</strong></td>
                                <td style="padding:4px 0;">{{ $ticket->subject }}</td>
                            </tr>
                            <tr>
                                <td style="padding:4px 0;width:120px;"><strong>Jenis Laporan:</strong></td>
                                <td style="padding:4px 0;text-transform:uppercase;">{{ $ticket->type }}</td>
                            </tr>
                            <tr>
                                <td style="padding:4px 0;width:120px;"><strong>Tanggal Kirim:</strong></td>
                                <td style="padding:4px 0;">{{ $ticket->created_at->format('d M Y, H:i') }} WIB</td>
                            </tr>
                        </table>

                        <p style="margin:20px 0 0 0;font-size:13px;color:#6b7280;line-height:1.5;">
                            Jika Anda masih memiliki pertanyaan atau kendala lain, silakan kirimkan tiket dukungan baru melalui halaman Hubungi Kami di situs resmi idSpora.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:14px 20px;background:#f3f4f6;color:#6b7280;font-size:12px;">
                        Email ini dikirim secara otomatis oleh sistem CRM idSpora. Mohon tidak membalas email ini secara langsung.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
