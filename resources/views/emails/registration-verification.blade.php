<!DOCTYPE html>
<html lang="id">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pendaftaran Akun - LMS IdSPora</title>
        <style>
                * { margin:0; padding:0; box-sizing:border-box; }
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background: linear-gradient(135deg, #fbbf24 0%, #a855f7 50%, #6366f1 100%); min-height: 100vh; padding: 20px; line-height: 1.6; }
                .email-container { max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px rgba(0,0,0,.15); }
                .header { background: linear-gradient(135deg, #f59e0b 0%, #a855f7 100%); padding: 40px 30px; text-align: center; color: white; position: relative; }
                .logo-text { font-size: 32px; font-weight: bold; text-align: center; line-height: 1; display: inline-block; }
                .logo-id { color: #f4a442; font-weight: bold; }
                .logo-spora { color: #51376C; font-weight: bold; }
                .header h1 { font-size: 28px; font-weight: 700; margin-bottom: 10px; color: white; text-shadow: 2px 2px 4px rgba(0,0,0,.3); }
                .header p { font-size: 16px; opacity: .9; color: white; text-shadow: 1px 1px 2px rgba(0,0,0,.3); }
                .content { padding: 50px 40px; text-align: center; }
                .greeting { font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 20px; }
                .message { color: #6b7280; font-size: 16px; margin-bottom: 30px; max-width: 480px; margin-left: auto; margin-right: auto; }
                .code-label { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 15px; text-transform: uppercase; letter-spacing: .05em; }
                .verification-code { background: #f59e0b; color: white; font-size: 32px; font-weight: 700; padding: 20px 40px; border-radius: 16px; letter-spacing: .3em; border: 2px solid #a855f7; font-family: 'Courier New', monospace; display:inline-block; }
                .warning { background:#f3f4f6; border:2px solid #a855f7; padding:16px 20px; border-radius:12px; margin:30px 0; text-align:center; }
                .warning-text { color:#6b46c1; font-size:14px; font-weight:500; }
                .footer { background:#f9fafb; padding:30px; text-align:center; border-top:1px solid #e5e7eb; }
                .footer-logo-text { font-size:20px; font-weight:bold; line-height:1; display:inline-block; }
                .footer-logo-id { color:#f4a442; text-shadow:1px 1px 2px rgba(244,164,66,.3); }
                .footer-logo-spora { color:#51376C; text-shadow:1px 1px 2px rgba(81,55,108,.3); }
                @media (max-width:640px){ body{ padding:10px !important; background:#fbbf24 !important;} .content{ padding:30px 20px !important;} .header{ padding:30px 20px !important;} .verification-code{ font-size:24px !important; padding:15px 25px !important; letter-spacing:.2em !important; } }
        </style>
 </head>
 <body>
     <div class="email-container">
         <div class="header">
             <div class="logo-text"><span class="logo-id">Id</span><span class="logo-spora">SPora</span></div>
             <h1>LMS IdSPora</h1>
             <p>Learning Management System</p>
         </div>
         <div class="content">
             <div class="greeting">Halo {{ $name }} 👋</div>
             <div class="message">Terima kasih telah mendaftar. Silakan gunakan kode verifikasi di bawah ini untuk mengaktifkan akun Anda:</div>
             <div class="code-label">Kode Verifikasi</div>
             <div class="verification-code">{{ $code }}</div>
             <div class="warning">
                 <div class="warning-text"><strong>Penting:</strong> Kode ini berlaku selama {{ $expires }} menit. Jika Anda tidak melakukan pendaftaran, abaikan email ini.</div>
             </div>
             <div style="color:#9ca3af; font-size:13px;">Email ini dikirim otomatis. Jangan balas email ini.</div>
         </div>
         <div class="footer">
             <div class="footer-logo-text"><span class="footer-logo-id">Id</span><span class="footer-logo-spora">SPora</span></div>
             <p style="font-weight:600; color:#a855f7; font-size:16px; margin-bottom:5px;">LMS IdSPora - Learning Management System</p>
             <p style="color:#6b7280; font-size:12px; margin-bottom:15px;">Email ini dikirim otomatis. Jangan balas email ini.</p>
             <div style="color:#9ca3af; font-size:11px; margin-top:15px;">© {{ date('Y') }} LMS IdSpora. All rights reserved.</div>
         </div>
     </div>
 </body>
 </html>
