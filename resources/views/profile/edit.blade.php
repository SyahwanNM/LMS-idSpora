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
        
        /* Card styling with light background */
        .glass-card {
            background: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
        }
        
        /* Settings page container - centered, not full width */
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
            margin-bottom: 1.5rem;
        }

        .settings-tab {
            border-radius: 999px;
            padding: 0.5rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            border: none;
            text-decoration: none;
            color: #4b5563;
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
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            body {
                padding-top: 70px;
            }
            .flex.min-h-screen {
                flex-direction: column;
                padding: 0 0.5rem;
            }
            .glass-sidebar {
                position: relative !important;
                width: 100% !important;
                height: auto !important;
                top: 0 !important;
                margin: 1rem 0 !important;
            }
            .main-content-with-sidebar {
                margin-left: 0;
                margin-top: 0;
                padding: 1.5rem 0.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .glass-card {
                padding: 1.5rem !important;
            }
            
            .glass-card h1 {
                font-size: 1.75rem !important;
            }
            
            .profile-img-wrapper {
                width: 100%;
                display: flex;
                justify-content: center;
                margin-bottom: 1rem;
            }
            
            .flex.items-start.space-x-6 {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .flex.items-start.space-x-6 > * {
                margin-bottom: 1rem;
            }
            
            .country-code-wrapper {
                width: 100% !important;
            }
            
            .flex.gap-3 {
                flex-direction: column;
                gap: 0.75rem !important;
            }
            
            #input-phone {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .glass-card {
                padding: 1rem !important;
            }
            
            .glass-card h1 {
                font-size: 1.5rem !important;
            }
            
            .country-code-select {
                font-size: 0.875rem;
                padding: 0.65rem 2.25rem 0.65rem 0.875rem !important;
            }
            
            .neu-input {
                font-size: 0.9rem;
                padding: 0.75rem 1rem !important;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            .flex.min-h-screen {
                flex-direction: column;
            }
            .glass-sidebar {
                width: 100%;
                padding: 1rem;
            }
            .glass-sidebar .search-bar {
                margin-bottom: 1rem;
            }
            .main-content-with-sidebar {
                padding: 1rem;
            }
            .glass-card {
                padding: 1.5rem !important;
            }
            .glass-card h1 {
                font-size: 1.75rem !important;
            }
            .profile-img-wrapper img {
                width: 80px !important;
                height: 80px !important;
            }
            .flex.items-center.space-x-4 {
                flex-direction: column;
                align-items: flex-start;
            }
            .flex.items-center.space-x-4 > * {
                margin-bottom: 1rem;
            }
            .grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            .menu-item {
                padding: 0.75rem !important;
            }
            .menu-item .w-10 {
                width: 2rem !important;
                height: 2rem !important;
            }
            .menu-item .font-semibold {
                font-size: 0.875rem !important;
            }
            .menu-item .text-xs {
                font-size: 0.75rem !important;
            }
            .neu-input {
                font-size: 16px !important; /* Prevent zoom on iOS */
            }
        }
        
        @media (max-width: 480px) {
            .glass-card {
                padding: 1rem !important;
            }
            .glass-card h1 {
                font-size: 1.5rem !important;
            }
            .profile-img-wrapper img {
                width: 60px !important;
                height: 60px !important;
            }
            .premium-badge {
                font-size: 0.625rem !important;
                padding: 0.25rem 0.75rem !important;
            }
            .gold-accent {
                width: 100%;
                justify-content: center;
            }
        }
        

        .search-bar input::placeholder {
            color: #9ca3af;
        }
        
        .search-bar i {
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Input styling with light theme */
        .neu-input {
            background: white;
            border: 1px solid #d1d5db;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .neu-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
            outline: none;
        }
        
        /* Premium Gold Accent */
        .gold-accent {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        }
        
        .gold-accent:hover {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            box-shadow: 0 6px 20px rgba(251, 191, 36, 0.4);
            transform: translateY(-1px);
        }
        
        /* Profile Image Overlay */
        .profile-img-wrapper {
            position: relative;
            display: inline-block;
        }
        
        .profile-img-overlay {
            position: absolute;
            bottom: 0;
            right: 0;
            background: rgba(251, 191, 36, 0.9);
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #fbbf24;
        }
        
        .profile-img-overlay:hover {
            background: rgba(234, 179, 8, 0.9);
            transform: scale(1.1);
        }
        
        /* Smooth Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        /* Country Code Dropdown Styling */
        .country-code-wrapper {
            position: relative;
        }

        .country-code-select {
            padding: 0.75rem 2.5rem 0.75rem 1rem !important;
            font-size: 0.9375rem;
            font-weight: 500;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12' fill='none'%3E%3Cpath d='M2 4L6 8L10 4' stroke='%23374151' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.875rem center;
            background-size: 12px;
            cursor: pointer;
        }

        .country-code-select:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
            outline: none;
        }

        .country-code-select option {
            padding: 0.5rem;
            font-size: 0.9375rem;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .country-code-wrapper {
                width: 100% !important;
            }
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

        @include('profile.partials.edit-content')
    </div>

    @include('partials.footer-after-login')
</body>
</html>
