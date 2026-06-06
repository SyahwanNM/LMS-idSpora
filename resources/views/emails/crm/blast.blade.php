<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $broadcast->title }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; width: 100% !important;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; margin: 0; padding: 40px 0; width: 100%;">
        <tr>
            <td align="center" valign="top">
                <!-- Main Email Card Container -->
                <table width="600" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03); max-width: 600px; width: 100%; border-collapse: separate;">
                    
                    <!-- Top Logo (Centered) -->
                    <tr>
                        <td align="center" valign="top" style="padding: 40px 30px 20px 30px; background-color: #ffffff;">
                            <img src="{{ $message->embed(public_path('aset/logo idspora_dark.png')) }}" alt="idSpora Logo" style="height: 48px; width: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>
                    
                    <!-- Subject Heading (Centered) -->
                    <tr>
                        <td align="center" valign="top" style="padding: 0 30px 24px 30px; background-color: #ffffff; text-align: center;">
                            <h1 style="margin: 0 0 10px 0; color: #1e1b4b; font-size: 24px; font-weight: 800; line-height: 1.3; font-family: 'Segoe UI', sans-serif;">
                                {{ $broadcast->title }}
                            </h1>
                            <p style="margin: 0; color: #64748b; font-size: 14px; font-family: 'Segoe UI', sans-serif; font-weight: 500;">
                                Pengumuman penting dari idSpora Academy
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Purple Layout Container Box with Megaphone Illustration -->
                    <tr>
                        <td align="center" valign="top" style="padding: 0 40px 30px 40px; background-color: #ffffff;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background: linear-gradient(135deg, #7c3aed 0%, #4c1d95 100%); background-color: #7c3aed; border-radius: 20px; overflow: hidden; border-collapse: separate; box-shadow: 0 4px 10px rgba(124, 58, 237, 0.15);">
                                <tr>
                                    <!-- Megaphone Icon (Left) -->
                                    <td align="center" valign="middle" style="padding: 30px 10px 30px 30px; width: 110px;">
                                        <img src="https://img.icons8.com/clouds/200/megaphone.png" alt="Megaphone" style="width: 100px; height: auto; display: block;">
                                    </td>
                                    
                                    <!-- Message & Button (Right) -->
                                    <td align="left" valign="middle" style="padding: 30px 30px 30px 20px;">
                                        <!-- Message Content -->
                                        <div style="font-size: 15px; line-height: 1.65; color: #ffffff; margin-bottom: 24px; white-space: pre-wrap; font-family: 'Segoe UI', sans-serif; font-weight: 500;">
                                            {{ $broadcast->message }}
                                        </div>
                                        
                                        <!-- Yellow Button -->
                                        <table border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td align="center" style="border-radius: 12px; background-color: #fbbf24;">
                                                    <a href="{{ $broadcast->link ?: config('app.url') }}" target="_blank" style="background-color: #fbbf24; border: 1px solid #fbbf24; border-radius: 12px; color: #1e1b4b !important; display: inline-block; padding: 12px 28px; font-size: 14px; font-weight: 700; text-decoration: none; font-family: 'Segoe UI', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">
                                                        Kunjungi Platform
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Signature / Footer Divider -->
                    <tr>
                        <td align="left" valign="top" style="padding: 10px 40px 30px 40px; background-color: #ffffff;">
                            <hr style="border: 0; border-top: 1px solid #f1f5f9; margin-top: 10px; margin-bottom: 24px;">
                            
                            <p style="margin: 0; font-size: 14px; color: #64748b; font-family: 'Segoe UI', sans-serif; line-height: 1.5;">
                                Salam hangat,<br>
                                <span style="font-weight: 700; color: #1e1b4b;">Admin {{ config('app.name') }}</span>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Bottom Gray Footer Band -->
                    <tr>
                        <td align="center" valign="top" style="padding: 24px 40px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center;">
                            <p style="margin: 0 0 6px 0; font-size: 11px; color: #94a3b8; font-family: 'Segoe UI', sans-serif; line-height: 1.4;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.
                            </p>
                            <p style="margin: 0; font-size: 10px; color: #cbd5e1; font-family: 'Segoe UI', sans-serif; line-height: 1.4;">
                                Anda menerima email ini karena terdaftar sebagai member di platform kami.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
