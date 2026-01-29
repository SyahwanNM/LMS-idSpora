<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Akun - idSPORA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2Pkf3BD3vO5e5pSxb6YV9jwWTA/gG05Jg9TLEbiFU6BxZ1S3XmGmGC3w9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            padding-top: 85px; 
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .neu-input {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .neu-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
        }
        
        .gold-accent {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: #1e1b4b;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .gold-accent:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(251, 191, 36, 0.4);
        }

        /* Settings page container - centered, slightly smaller */
        .settings-container {
            max-width: 920px;
            margin: 0 auto;
            padding: 1.25rem 1rem 2.75rem;
        }

        .settings-tabs {
            display: inline-flex;
            padding: 0.35rem;
            border-radius: 999px;
            background: #e5e7eb;
            margin-bottom: 1.25rem;
        }

        .settings-tab {
            border-radius: 999px;
            padding: 0.5rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            border: none;
            text-decoration: none;
            color: #64748b;
            background: transparent;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .settings-tab.active {
            background: #4f46e5;
            color: #ffffff;
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.25);
        }

        .settings-tab:not(.active):hover {
            background: #f3f4f6;
            color: #111827;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")
    
    <div class="settings-container fade-in">
        <header class="mb-4">
            <h1 class="text-3xl md:text-4xl font-bold mb-2" style="color:#0f172a;">Pengaturan Akun</h1>
            <p class="text-sm md:text-base" style="color:#64748b;">Kelola identitas publik dan keamanan akun Anda.</p>
        </header>

        <div class="settings-tabs">
            <a href="{{ route('profile.edit') }}"
               class="settings-tab {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i class="bi bi-person"></i>
                Edit Profil
            </a>
            <a href="{{ route('profile.account-settings') }}"
               class="settings-tab {{ request()->routeIs('profile.account-settings') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i>
                Keamanan Akun
            </a>
        </div>

        @include('profile.partials.account-settings-content')
    </div>

    @include('partials.footer-after-login')
</body>
</html>
