<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - idSPORA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2Pkf3BD3vO5e5pSxb6YV9jwWTA/gG05Jg9TLEbiFU6BxZ1S3XmGmGC3w9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Override app.css navbar styling - must be after app.css */
        body .navbar.navbar-gradient,
        body .navbar-gradient,
        body nav.navbar.navbar-gradient,
        body nav.navbar-gradient {
            background: linear-gradient(90deg, #252346 0%, #5b56ac 100%) !important;
            opacity: 1 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }
        * {
            font-family: 'Inter', 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        /* Background same as dashboard */
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            padding-top: 70px;
        }
        
        /* Ensure navbar is visible and on top - High specificity to override app.css */
        body .navbar,
        body nav.navbar {
            z-index: 1050 !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            background: linear-gradient(90deg, #252346 0%, #5b56ac 100%) !important;
        }
        
        body .navbar.navbar-gradient,
        body .navbar-gradient,
        body nav.navbar.navbar-gradient,
        body nav.navbar-gradient {
            background: linear-gradient(90deg, #252346 0%, #5b56ac 100%) !important;
            opacity: 1 !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3) !important;
        }
        
        /* Ensure navbar elements are visible - High specificity */
        body .navbar-gradient .navbar-brand,
        body .navbar-gradient .nav-link,
        body .navbar-gradient .navbar-text,
        body .navbar .navbar-brand,
        body .navbar .nav-link,
        body .navbar .navbar-text,
        body nav.navbar-gradient .navbar-brand,
        body nav.navbar-gradient .nav-link,
        body nav.navbar .navbar-brand,
        body nav.navbar .nav-link {
            color: #fff !important;
        }
        
        body .navbar-gradient .nav-link:hover,
        body .navbar-gradient .nav-link:focus,
        body .navbar .nav-link:hover,
        body .navbar .nav-link:focus,
        body nav.navbar-gradient .nav-link:hover,
        body nav.navbar .nav-link:hover {
            color: #ffe8b3 !important;
        }
        
        body .navbar-gradient .nav-link.active,
        body .navbar .nav-link.active,
        body nav.navbar-gradient .nav-link.active,
        body nav.navbar .nav-link.active {
            font-weight: 600;
            color: #ffe8b3 !important;
        }
        
        /* Notification and user dropdown - High specificity */
        body #notifBtn,
        body #userDropdown,
        body .navbar #notifBtn,
        body .navbar #userDropdown,
        body nav.navbar #notifBtn,
        body nav.navbar #userDropdown {
            color: white !important;
        }
        
        body #notifBtn:hover,
        body #userDropdown:hover,
        body .navbar #notifBtn:hover,
        body .navbar #userDropdown:hover,
        body nav.navbar #notifBtn:hover,
        body nav.navbar #userDropdown:hover {
            color: #ffe8b3 !important;
        }
        
        /* Search bar - High specificity */
        body .navbar .form-control,
        body nav.navbar .form-control,
        body .navbar-gradient .form-control {
            color: white !important;
            background: rgba(255, 255, 255, 0.1) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
        
        body .navbar .form-control::placeholder,
        body nav.navbar .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        
        body .navbar .form-control:focus,
        body nav.navbar .form-control:focus {
            background: rgba(255, 255, 255, 0.15) !important;
            border-color: rgba(255, 255, 255, 0.5) !important;
            color: white !important;
        }
        
        /* Ensure navbar container is visible */
        body .navbar .container-fluid,
        body nav.navbar .container-fluid {
            display: flex !important;
            visibility: visible !important;
        }
        
        /* Ensure all navbar elements are visible */
        body .navbar-brand,
        body .navbar-nav,
        body .navbar-collapse,
        body nav .navbar-brand,
        body nav .navbar-nav,
        body nav .navbar-collapse {
            display: flex !important;
            visibility: visible !important;
        }
        
        /* Navbar toggler for mobile */
        body .navbar-toggler,
        body nav.navbar .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
        
        body .navbar-toggler-icon,
        body nav.navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
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
        
        /* Card styling with light background */
        .glass-card {
            background: white;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-radius: 0.75rem;
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
                @include('profile.partials.edit-content')
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
    
    <script>
        // Avatar preview function
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Phone number formatting
        document.addEventListener('DOMContentLoaded', function() {
            const phoneInput = document.getElementById('input-phone');
            
            if (phoneInput) {
                // Format phone number saat input (hanya angka dengan spasi untuk readability)
                phoneInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    
                    // Format dengan spasi untuk readability (3-4-4 pattern)
                    let formatted = value.replace(/(\d{3})(\d{4})(\d{0,4})/, function(match, p1, p2, p3) {
                        if (p3) {
                            return p1 + ' ' + p2 + ' ' + p3;
                        } else if (p2) {
                            return p1 + ' ' + p2;
                        }
                        return p1;
                    });
                    
                    e.target.value = formatted;
                });
                
                // Hapus leading zero saat blur
                phoneInput.addEventListener('blur', function(e) {
                    let value = e.target.value.replace(/[^0-9]/g, '');
                    value = value.replace(/^0+/, ''); // Hapus leading zero
                    
                    // Format ulang
                    let formatted = value.replace(/(\d{3})(\d{4})(\d{0,4})/, function(match, p1, p2, p3) {
                        if (p3) {
                            return p1 + ' ' + p2 + ' ' + p3;
                        } else if (p2) {
                            return p1 + ' ' + p2;
                        }
                        return p1;
                    });
                    
                    e.target.value = formatted;
                });
            }
        });

        // Deep-link to field based on query parameter
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const focusField = urlParams.get('focus');
            
            if (focusField) {
                // Map field names to input IDs
                const fieldMap = {
                    'name': 'input-name',
                    'email': 'input-email',
                    'phone': 'input-phone',
                    'avatar': 'avatarInput',
                    'bio': 'input-bio'
                };
                
                const inputId = fieldMap[focusField];
                if (inputId) {
                    const inputElement = document.getElementById(inputId);
                    if (inputElement) {
                        // Scroll to field
                        const fieldDiv = document.getElementById('field-' + focusField);
                        if (fieldDiv) {
                            fieldDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        
                        // Focus on input after a short delay
                        setTimeout(function() {
                            inputElement.focus();
                            
                            // Highlight the field
                            inputElement.style.border = '2px solid #fbbf24';
                            inputElement.style.boxShadow = '0 0 0 3px rgba(251, 191, 36, 0.2)';
                            
                            // Remove highlight after 3 seconds
                            setTimeout(function() {
                                inputElement.style.border = '';
                                inputElement.style.boxShadow = '';
                            }, 3000);
                        }, 300);
                    }
                }
            }
        });
    </script>
    @include('partials.footer-after-login')
</body>
</html>
