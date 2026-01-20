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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2Pkf3BD3vO5e5pSxb6YV9jwWTA/gG05Jg9TLEbiFU6BxZ1S3XmGmGC3w9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            padding-top: 70px;
        }
        
        /* Ensure navbar is visible and on top */
        .navbar {
            z-index: 1050 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
        }
        
        .navbar-gradient {
            background: linear-gradient(90deg, #252346 0%, #5b56ac 100%) !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Ensure navbar elements are visible */
        .navbar-gradient .navbar-brand,
        .navbar-gradient .nav-link,
        .navbar-gradient .navbar-text {
            color: #fff !important;
        }
        
        .navbar-gradient .nav-link:hover,
        .navbar-gradient .nav-link:focus {
            color: #ffe8b3 !important;
        }
        
        .navbar-gradient .nav-link.active {
            font-weight: 600;
            color: #ffe8b3 !important;
        }
        
        /* Notification and user dropdown */
        #notifBtn,
        #userDropdown {
            color: white !important;
        }
        
        #notifBtn:hover,
        #userDropdown:hover {
            color: #ffe8b3 !important;
        }
        
        /* Search bar */
        .navbar .form-control {
            color: white !important;
        }
        
        .navbar .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        /* Ensure navbar container is visible */
        .navbar .container-fluid {
            display: flex !important;
            visibility: visible !important;
        }
        
        /* Ensure all navbar elements are visible */
        .navbar-brand,
        .navbar-nav,
        .navbar-collapse {
            display: flex !important;
            visibility: visible !important;
        }
        
        /* Navbar Responsive */
        @media (max-width: 991px) {
            .navbar-brand {
                margin-left: 15px !important;
            }
            .navbar-brand img {
                max-width: 60px !important;
            }
            .navbar .form-control {
                width: 100% !important;
                margin: 0.5rem 0 !important;
            }
            .navbar-nav {
                flex-direction: column !important;
                width: 100%;
                margin: 0.5rem 0 !important;
            }
            .navbar-nav .nav-item {
                margin: 0.25rem 0 !important;
                width: 100%;
            }
            .navbar-collapse {
                padding: 1rem 0;
            }
            #notifBtn,
            #userDropdown {
                margin: 0.5rem 0 !important;
            }
            .d-flex.align-items-center.ms-3 {
                flex-direction: column !important;
                width: 100%;
                margin-right: 0 !important;
                margin-left: 0 !important;
            }
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
    </style>
</head>
<body>
    @include("partials.navbar-after-login")
    
    <div class="flex min-h-screen" style="align-items: flex-start; justify-content: center; max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <!-- Minimalist Sidebar - Settings -->
        <aside class="glass-sidebar flex flex-col" style="width: 280px; background: #ffffff; flex-shrink: 0; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); position: sticky; top: 90px; left: 0; height: fit-content; max-height: calc(100vh - 100px); overflow-y: auto; z-index: 100; margin: 2rem 1rem 2rem 0;">
            <!-- Sidebar Header -->
            <div class="sidebar-header" style="padding: 1.25rem 1.25rem; border-bottom: 1px solid #e5e7eb;">
                <h3 style="font-size: 0.875rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">Navigasi Profil</h3>
            </div>
            
            <!-- Menu Items -->
            <nav style="padding: 0.5rem 0;">
                <a href="{{ route('profile.edit') }}" class="menu-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}" style="display: flex; align-items: center; padding: 0.875rem 1.25rem; color: #374151; text-decoration: none; transition: all 0.2s; border-left: 3px solid transparent;">
                    <i class="bi bi-person" style="font-size: 1.125rem; margin-right: 0.75rem; width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.9375rem; font-weight: 500;">Profil</span>
                </a>
                
                <a href="{{ route('profile.account-settings') }}" class="menu-item {{ request()->routeIs('profile.account-settings') ? 'active' : '' }}" style="display: flex; align-items: center; padding: 0.875rem 1.25rem; color: #374151; text-decoration: none; transition: all 0.2s; border-left: 3px solid transparent;">
                    <i class="bi bi-shield-lock" style="font-size: 1.125rem; margin-right: 0.75rem; width: 20px; text-align: center;"></i>
                    <span style="font-size: 0.9375rem; font-weight: 500;">Akun</span>
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content-with-sidebar flex-1 overflow-y-auto" style="margin-top: 70px; padding: 2rem 1rem;">
            <div class="max-w-4xl mx-auto fade-in" style="max-width: 100%;">
                @include('profile.partials.account-settings-content')
            </div>
        </main>
    </div>
    
    <style>
        .menu-item {
            transition: all 0.2s ease;
            color: #374151;
            text-decoration: none;
        }
        
        .menu-item:hover:not(.active) {
            background-color: #f9fafb;
        }
        
        .menu-item.active {
            background-color: #eff6ff !important;
            color: #2563eb !important;
            border-left-color: #2563eb !important;
        }
        
        /* Override any yellow/gold background for active menu */
        .menu-item.active {
            background: #eff6ff !important;
            background-color: #eff6ff !important;
            background-image: none !important;
            color: #2563eb !important;
        }
        
        /* Container untuk sidebar dan content - Centered layout */
        .flex.min-h-screen {
            align-items: flex-start;
            justify-content: center;
        }
        
        .main-content-with-sidebar {
            flex: 1;
            min-width: 0;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
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
        
        @media (max-width: 1024px) {
            .flex.min-h-screen {
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
            body {
                padding-top: 70px;
            }
            .main-content-with-sidebar {
                padding: 1rem;
            }
            .glass-card {
                padding: 1.5rem !important;
            }
        }
        
        @media (max-width: 576px) {
            .glass-card {
                padding: 1rem !important;
            }
            .glass-card h1 {
                font-size: 1.5rem !important;
            }
        }
    </style>
    @include('partials.footer-after-login')
</body>
</html>
