<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - LMS IdSPora</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #fbbf24 0%, #a855f7 50%, #6366f1 100%);
            min-height: 100vh;
            padding: 20px;
            line-height: 1.6;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .header {
            background: linear-gradient(135deg, #f59e0b 0%, #a855f7 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20" fill="none"><defs><linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" style="stop-color:white;stop-opacity:0.1" /><stop offset="100%" style="stop-color:white;stop-opacity:0.05" /></linearGradient></defs><path d="m0 20c20-10 40-15 60-5s40 5 40-15v20z" fill="url(%23grad)"/></svg>') repeat-x;
            opacity: 0.3;
        }
        
        .logo {
            margin: 0 auto 20px;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .logo-text {
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            line-height: 1;
            display: inline-block;
        }
        
        .logo-id {
            color: #f4a442;
            font-weight: bold;
        }
        
        .logo-spora {
            color: #51376C;
            font-weight: bold;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .header p {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            color: white;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .content {
            padding: 50px 40px;
            text-align: center;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
        }
        
        .wave {
            font-size: 28px;
            margin-left: 8px;
            display: inline-block;
            animation: wave 2s ease-in-out infinite;
        }
        
        @keyframes wave {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(20deg); }
            75% { transform: rotate(-10deg); }
        }
        
        .message {
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 40px;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .code-section {
            margin: 40px 0;
            text-align: center;
        }
        
        .code-wrapper {
            display: table;
            margin: 0 auto;
        }
        
        .code-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .verification-code {
            background: #f59e0b;
            color: white;
            font-size: 32px;
            font-weight: 700;
            padding: 20px 40px;
            border-radius: 16px;
            letter-spacing: 0.3em;
            margin: 0 auto;
            display: inline-block;
            border: 2px solid #a855f7;
            font-family: 'Courier New', monospace;
            text-align: center;
        }
        
        
        .warning {
            background: #f3f4f6;
            border: 2px solid #a855f7;
            padding: 16px 20px;
            border-radius: 12px;
            margin: 30px 0;
            text-align: center;
        }
        
        .warning::before {
            content: '⚠️';
            font-size: 20px;
            display: block;
            margin-bottom: 8px;
        }
        
        .warning-text {
            color: #6b46c1;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.5;
        }
        
        .instruction {
            color: #4b5563;
            font-size: 15px;
            margin-bottom: 30px;
        }
        
        .reset-button {
            background: #a855f7;
            color: white;
            padding: 16px 40px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            border: 2px solid #6366f1;
        }
        
        .divider {
            height: 1px;
            background: #e5e7eb;
            margin: 40px 0;
        }
        
        .footer {
            background: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        
        .footer-logo {
            margin: 0 auto 15px;
            text-align: center;
        }
        
        .footer-logo-text {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            line-height: 1;
            display: inline-block;
        }
        
        .footer-logo-id {
            color: #f4a442;
            text-shadow: 1px 1px 2px rgba(244, 164, 66, 0.3);
        }
        
        .footer-logo-spora {
            color: #51376C;
            text-shadow: 1px 1px 2px rgba(81, 55, 108, 0.3);
        }
        
        .footer-brand {
            font-weight: 600;
            color: #a855f7;
            font-size: 16px;
            margin-bottom: 5px;
        }
        
        .footer-subtitle {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 15px;
        }
        
        .footer-links {
            margin: 15px 0;
        }
        
        .footer-links a {
            color: #f4a442;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .copyright {
            color: #9ca3af;
            font-size: 11px;
            margin-top: 15px;
        }
        
        /* Mobile-friendly styles - menggunakan inline styles untuk kompatibilitas */
        @media (max-width: 640px) {
            body {
                padding: 10px !important;
                background: #fbbf24 !important;
            }
            
            .email-container {
                margin: 10px auto !important;
                border-radius: 10px !important;
            }
            
            .content {
                padding: 30px 20px !important;
            }
            
            .header {
                padding: 30px 20px !important;
                background: #f59e0b !important;
            }
            
            .logo-text {
                font-size: 24px !important;
            }
            
            .verification-code {
                font-size: 24px !important;
                padding: 15px 25px !important;
                letter-spacing: 0.2em !important;
                text-align: center !important;
                display: block !important;
                margin: 0 auto !important;
                width: fit-content !important;
            }
            
            .code-section {
                text-align: center !important;
            }
            
            .code-wrapper {
                text-align: center !important;
                width: 100% !important;
            }
            
            .greeting {
                font-size: 20px !important;
            }
            
            .message {
                font-size: 15px !important;
            }
            
            .reset-button {
                padding: 14px 30px !important;
                font-size: 14px !important;
            }
        }
        
        /* Fallback for email clients that don't support CSS */
        .fallback-text {
            color: #1f2937;
            font-size: 16px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="email-container" style="max-width: 600px; margin: 0 auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);">
        <div class="header" style="background: linear-gradient(135deg, #f59e0b 0%, #a855f7 100%); padding: 40px 30px; text-align: center; color: white; position: relative;">
            <div class="logo" style="margin: 0 auto 20px; text-align: center; position: relative; z-index: 1;">
                <div class="logo-text" style="font-size: 32px; font-weight: bold; text-align: center; line-height: 1; display: inline-block;">
                    <span class="logo-id" style="color: #f4a442; font-weight: bold;">Id</span><span class="logo-spora" style="color: #51376C; font-weight: bold;">SPora</span>
                </div>
            </div>
            <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 10px; position: relative; z-index: 1; color: white; text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">LMS IdSPora</h1>
            <p style="font-size: 16px; opacity: 0.9; position: relative; z-index: 1; color: white; text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);">Learning Management System</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Halo {{ $userName }}<span class="wave">👋</span>
            </div>
            
            <div class="message">
                Kami menerima permintaan untuk mereset password akun Anda di LMS IdSpora. Untuk melanjutkan proses reset password, silakan gunakan kode verifikasi berikut:
            </div>
            
            <div class="code-section" style="margin: 40px 0; text-align: center;">
                <div class="code-label" style="font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.05em;">Kode Verifikasi</div>
                <div class="code-wrapper" style="text-align: center; width: 100%;">
                    <div class="verification-code" style="background: #f59e0b; color: white; font-size: 32px; font-weight: 700; padding: 20px 40px; border-radius: 16px; letter-spacing: 0.3em; margin: 0 auto; display: inline-block; border: 2px solid #a855f7; font-family: 'Courier New', monospace; text-align: center;">{{ $verificationCode }}</div>
                </div>
            </div>
            
            <div class="warning" style="background: #f3f4f6; border: 2px solid #a855f7; padding: 16px 20px; border-radius: 12px; margin: 30px 0; text-align: center;">
                <div class="warning-text" style="color: #6b46c1; font-size: 14px; font-weight: 500; line-height: 1.5;">
                    <strong>Penting:</strong> Kode ini berlaku selama 15 menit. Jika Anda tidak meminta reset password, abaikan email ini.
                </div>
            </div>
            
            <div class="instruction" style="color: #4b5563; font-size: 15px; margin-bottom: 30px;">
                Masukkan kode verifikasi di halaman yang terbuka untuk melanjutkan proses reset password Anda.
            </div>
            
            <div style="text-align: center;">
                <a href="#" class="reset-button" style="background: #a855f7; color: white; padding: 16px 40px; border: none; border-radius: 50px; font-size: 16px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; border: 2px solid #6366f1;">Lanjutkan Reset Password</a>
            </div>
            
            <div class="divider"></div>
            
            <div style="color: #9ca3af; font-size: 13px;">
                Email ini dikirim secara otomatis. Jangan balas email ini.
            </div>
            
            <!-- Fallback text for email clients that don't support CSS -->
            <div style="display: none; max-height: 0; overflow: hidden;">
                Kode Verifikasi: {{ $verificationCode }}
                Kode ini berlaku selama 15 menit.
                Jika Anda tidak meminta reset password, abaikan email ini.
            </div>
        </div>
        
        <div class="footer" style="background: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <div class="footer-logo" style="margin: 0 auto 15px; text-align: center;">
                <div class="footer-logo-text" style="font-size: 20px; font-weight: bold; text-align: center; line-height: 1; display: inline-block;">
                    <span class="footer-logo-id" style="color: #f4a442; text-shadow: 1px 1px 2px rgba(244, 164, 66, 0.3);">Id</span><span class="footer-logo-spora" style="color: #51376C; text-shadow: 1px 1px 2px rgba(81, 55, 108, 0.3);">SPora</span>
                </div>
            </div>
            
            <p style="font-weight: 600; color: #a855f7; font-size: 16px; margin-bottom: 5px;"><strong>LMS IdSPora</strong> - Learning Management System</p>
            <p style="color: #6b7280; font-size: 12px; margin-bottom: 15px;">Email ini dikirim secara otomatis. Jangan balas email ini.</p>
            
            <div class="footer-links" style="margin: 15px 0;">
                <a href="https://idspora.com" target="_blank" style="color: #f4a442; text-decoration: none; font-size: 14px; font-weight: 500;">Website</a> •
                <a href="mailto:support@idspora.com" style="color: #f4a442; text-decoration: none; font-size: 14px; font-weight: 500;">Support</a> •
                <a href="https://idspora.com/privacy" target="_blank" style="color: #f4a442; text-decoration: none; font-size: 14px; font-weight: 500;">Privacy Policy</a>
            </div>
            <div class="copyright" style="color: #9ca3af; font-size: 11px; margin-top: 15px;">
                © {{ date('Y') }} LMS IdSpora. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>
