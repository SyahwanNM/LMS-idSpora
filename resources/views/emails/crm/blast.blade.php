<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $broadcast->title }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7fa;
            color: #334155;
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
        .header {
            background: linear-gradient(135deg, #6d28d9 0%, #4f46e5 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px;
            background-color: #ffffff;
        }
        .message-box {
            font-size: 16px;
            color: #475569;
            white-space: pre-line;
        }
        .footer {
            padding: 30px;
            text-align: center;
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
            color: #94a3b8;
            font-size: 12px;
        }
        .btn {
            display: inline-block;
            padding: 14px 30px;
            background-color: #6d28d9;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            margin-top: 30px;
            box-shadow: 0 4px 6px rgba(109, 40, 217, 0.2);
        }
        .logo {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 10px;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="logo">{{ config('app.name') }}</span>
            <h1>{{ $broadcast->title }}</h1>
        </div>
        <div class="content">
            <div class="message-box">
                {{ $broadcast->message }}
            </div>
            
            <div style="text-align: center;">
                <a href="{{ config('app.url') }}" class="btn">Kunjungi Platform</a>
            </div>
            
            <p style="margin-top: 40px; font-size: 14px; color: #64748b;">
                Salam hangat,<br>
                <strong>Admin {{ config('app.name') }}</strong>
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. Seluruh hak cipta dilindungi.</p>
            <p>Anda menerima email ini karena terdaftar sebagai member di platform kami.</p>
        </div>
    </div>
</body>
</html>
