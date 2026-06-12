<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $broadcast->title }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f5f7; font-family: 'Segoe UI', Arial, sans-serif; color: #333333; -webkit-font-smoothing: antialiased; width: 100% !important;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f4f5f7; margin: 0; padding: 40px 0; width: 100%;">
        <tr>
            <td align="center" valign="top">
                <!-- Main Email Card Container -->
                <table width="600" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e1e4e8; border-radius: 8px; overflow: hidden; max-width: 600px; width: 100%; border-collapse: separate;">
                    
                    <!-- Header with Logo -->
                    <tr>
                        <td align="left" valign="top" style="padding: 30px 40px; background-color: #ffffff; border-bottom: 1px solid #edf2f7;">
                            <img src="{{ $message->embed(public_path('aset/logo idspora_dark.png')) }}" alt="idSpora Logo" style="height: 38px; width: auto; display: block;">
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td align="left" valign="top" style="padding: 40px 40px 30px 40px; background-color: #ffffff;">
                            <h2 style="margin: 0 0 20px 0; color: #1e1b4b; font-size: 20px; font-weight: 700; line-height: 1.4; font-family: 'Segoe UI', sans-serif;">
                                {{ $broadcast->title }}
                            </h2>
                            
                            <div style="font-size: 15px; line-height: 1.65; color: #4a5568; margin-bottom: 30px; white-space: pre-wrap; font-family: 'Segoe UI', sans-serif;">
                                {{ $broadcast->message }}
                            </div>
                            
                            <!-- Call To Action Button (Optional) -->
                            @if($broadcast->link)
                            <table border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 30px;">
                                <tr>
                                    <td align="center" style="border-radius: 6px; background-color: #7c3aed;">
                                        <a href="{{ $broadcast->link }}" target="_blank" style="background-color: #7c3aed; border: 1px solid #7c3aed; border-radius: 6px; color: #ffffff !important; display: inline-block; padding: 12px 24px; font-size: 14px; font-weight: 600; text-decoration: none; font-family: 'Segoe UI', sans-serif; letter-spacing: 0.3px;">
                                            Kunjungi Halaman Halaman
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                            <!-- File Attachments List (Optional) -->
                            @if($broadcast->attachment)
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #edf2f7;">
                                <strong style="font-size: 12px; color: #718096; display: block; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.8px;">Lampiran Dokumen:</strong>
                                <ul style="margin: 0; padding-left: 20px; color: #7c3aed; font-size: 14px; font-family: 'Segoe UI', sans-serif;">
                                    @php
                                        $paths = json_decode($broadcast->attachment, true);
                                    @endphp
                                    @if(is_array($paths))
                                        @foreach($paths as $path)
                                            @php
                                                $filename = basename($path);
                                                $cleanFilename = preg_replace('/^\d+_/', '', $filename);
                                            @endphp
                                            <li style="margin-bottom: 8px;">
                                                <a href="{{ Storage::disk('public')->url($path) }}" target="_blank" style="color: #7c3aed; text-decoration: underline; font-weight: 600;">
                                                    {{ $cleanFilename }}
                                                </a>
                                            </li>
                                        @endforeach
                                    @else
                                        @php
                                            $filename = basename($broadcast->attachment);
                                            $cleanFilename = preg_replace('/^\d+_/', '', $filename);
                                        @endphp
                                        <li>
                                            <a href="{{ Storage::disk('public')->url($broadcast->attachment) }}" target="_blank" style="color: #7c3aed; text-decoration: underline; font-weight: 600;">
                                                {{ $cleanFilename }}
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            @endif
                        </td>
                    </tr>
                    
                    <!-- Signature / Sign-off -->
                    <tr>
                        <td align="left" valign="top" style="padding: 10px 40px 40px 40px; background-color: #ffffff;">
                            <hr style="border: 0; border-top: 1px solid #edf2f7; margin-top: 10px; margin-bottom: 24px;">
                            
                            <p style="margin: 0; font-size: 14px; color: #718096; font-family: 'Segoe UI', sans-serif; line-height: 1.5;">
                                Salam hormat,<br>
                                <strong style="font-weight: 700; color: #1e1b4b; display: inline-block; margin-top: 4px;">Tim idSpora Academy</strong>
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Bottom Footer -->
                    <tr>
                        <td align="center" valign="top" style="padding: 24px 40px; background-color: #f8fafc; border-top: 1px solid #edf2f7; text-align: center;">
                            <p style="margin: 0 0 6px 0; font-size: 11px; color: #a0aec0; font-family: 'Segoe UI', sans-serif; line-height: 1.4;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.
                            </p>
                            <p style="margin: 0; font-size: 10px; color: #cbd5e1; font-family: 'Segoe UI', sans-serif; line-height: 1.4;">
                                Email ini dikirim secara otomatis oleh sistem administrasi idSpora.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
