<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Server Maintenance</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f4ff 0%, #faf8f0 50%, #fff7ed 100%);
            color: #0f172a;
        }

        .wrap {
            max-width: 480px;
            width: 90%;
            text-align: center;
            animation: fadeUp 0.6s ease both;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .logo {
            margin-bottom: 32px;
        }

        .logo img {
            max-width: 130px;
            opacity: 0.95;
        }

        .icon-wrap {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25);
        }

        .icon-wrap svg {
            width: 36px;
            height: 36px;
            color: #fff;
        }

        h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            letter-spacing: -0.3px;
        }

        .desc {
            font-size: 15px;
            color: #64748b;
            line-height: 1.65;
            margin-bottom: 32px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 13px;
            font-weight: 500;
        }

        .badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #f59e0b;
            animation: pulse 1.6s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.85); }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="logo">
            <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="Logo">
        </div>

        <div class="icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l5.653-4.655m5.833-4.329c1.037-.497 2.24-.55 3.328-.13l-3.196 3.197.818 2.353 2.352.817 3.197-3.196c.42 1.088.367 2.291-.13 3.328" />
            </svg>
        </div>

        <h1>Website sedang dalam pemeliharaan</h1>

        <p class="desc">
            {{ $message ?? 'Maaf, layanan sedang kami hentikan sementara untuk pemeliharaan. Silakan coba lagi dalam beberapa saat.' }}
        </p>

        <div class="badge">
            <span class="badge-dot"></span>
            Sedang dalam proses pembaruan
        </div>
    </div>
</body>
</html>
