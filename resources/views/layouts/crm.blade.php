<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CRM - @yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body>
    <!-- Admin Navbar -->
    @php $user = auth()->user(); @endphp
    <nav class="navbar navbar-expand-lg navbar-dark bg-purple-gradient shadow-sm fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('aset/logo.png') }}" alt="logo" class="me-2" style="height:28px;">
                <span class="fw-semibold">Admin</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center dropdown-toggle" href="javascript:void(0)" id="adminProfileDropdown" role="button" data-bs-toggle="dropdown" data-bs-offset="0,8" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                            <span class="avatar-circle me-2">
                                <img src="{{ $user?->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                            </span>
                            <span class="d-none d-lg-inline user-name small fw-semibold">{{ $user?->name ?? 'Admin' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow profile-dropdown" aria-labelledby="adminProfileDropdown">
                            <li class="d-flex justify-content-end align-items-center pt-2 px-2">
                                <button type="button" class="btn-close dropdown-close" aria-label="Close"></button>
                            </li>
                            <li><h6 class="dropdown-header small text-muted">Akun</h6></li>
                            @if($user && $user->role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-sliders me-1"></i> Sistem</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item small text-danger">
                                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CRM Sidebar with 3D Effect - Fixed -->
    <aside class="crm-sidebar">
        <div class="sidebar-content p-4">
            <div class="crm-brand mb-4">
                <div class="brand-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>
                <h5 class="text-dark fw-bold mb-0 mt-2">CRM System</h5>
                <small class="text-muted">Customer Management</small>
            </div>
            <nav class="nav flex-column crm-nav">
                <a class="nav-link crm-nav-item {{ request()->routeIs('admin.crm.dashboard') ? 'active' : '' }}" href="{{ route('admin.crm.dashboard') }}">
                    <div class="nav-icon-wrapper">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <span>Dashboard</span>
                    <div class="nav-indicator"></div>
                </a>
                <a class="nav-link crm-nav-item {{ request()->routeIs('admin.crm.certificates.*') ? 'active' : '' }}" href="{{ route('admin.crm.certificates.index') }}">
                    <div class="nav-icon-wrapper">
                        <i class="bi bi-award"></i>
                    </div>
                    <span>Generate Sertifikat</span>
                    <div class="nav-indicator"></div>
                </a>
                <a class="nav-link crm-nav-item {{ request()->routeIs('admin.crm.customers.*') ? 'active' : '' }}" href="{{ route('admin.crm.customers.index') }}">
                    <div class="nav-icon-wrapper">
                        <i class="bi bi-people"></i>
                    </div>
                    <span>Kelola Customer</span>
                    <div class="nav-indicator"></div>
                </a>
                <a class="nav-link crm-nav-item {{ request()->routeIs('admin.crm.feedback.*') ? 'active' : '' }}" href="{{ route('admin.crm.feedback.index') }}">
                    <div class="nav-icon-wrapper">
                        <i class="bi bi-chat-left-text"></i>
                    </div>
                    <span>Feedback Analysis</span>
                    <div class="nav-indicator"></div>
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content with Background -->
    <div class="crm-main-wrapper" style="padding-top: 78px;">
        <main class="crm-main-content">
            <div class="crm-content-wrapper px-4 py-4">
                @yield('content')
            </div>
        </main>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Dropdown initialization
        var trigger = document.getElementById('adminProfileDropdown');
        if (trigger && window.bootstrap && bootstrap.Dropdown) {
            try { new bootstrap.Dropdown(trigger, { autoClose: 'outside', display: 'static' }); } catch(e){}
            trigger.addEventListener('click', function(ev){ ev.preventDefault(); try {
                const dd = bootstrap.Dropdown.getOrCreateInstance(trigger, { autoClose: 'outside', display: 'static' });
                dd.toggle();
            } catch(e){} });

            document.querySelectorAll('.dropdown-close').forEach(function(btn){
                btn.addEventListener('click', function(ev){
                    ev.preventDefault(); ev.stopPropagation();
                    try {
                        const dd = bootstrap.Dropdown.getOrCreateInstance(trigger, { autoClose: 'outside', display: 'static' });
                        dd.hide();
                    } catch(e){}
                });
            });
        }
    });
    </script>
    <style>
    * { font-family: 'Poppins', sans-serif; }
    
    body {
        background: #f8f9fa;
        min-height: 100vh;
    }
    
    .bg-purple-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    }    
    .navbar { z-index: 1040; }
    .navbar .nav-link {color: rgba(255,255,255,.9);} 
    .navbar .nav-link:hover {color: #fff;}
    .avatar-circle {
        width:40px;height:40px;border-radius:50%;
        overflow:hidden;border:2px solid #EBBC01;
        background:#6b7280;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .avatar-circle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(235, 188, 1, 0.4);
    }
    .avatar-circle img {width:100%;height:100%;object-fit:cover;display:block;}
    .profile-dropdown {
        margin-top:.25rem;
        opacity:0;
        transform:translateY(-6px) scale(.98);
        transition:opacity .16s ease, transform .16s ease;
        z-index:1045;
        position: relative;
        background: rgba(255,255,255,0.96);
        backdrop-filter: saturate(180%) blur(6px);
        -webkit-backdrop-filter: saturate(180%) blur(6px);
        border: 1px solid rgba(0,0,0,.06);
        box-shadow: 0 10px 24px rgba(0,0,0,.12), 0 4px 10px rgba(0,0,0,.06);
        border-radius: 12px;
        overflow: hidden;
    }
    .profile-dropdown.show {opacity:1;transform:translateY(0) scale(1);} 
    .profile-dropdown .dropdown-close { position:absolute; top:6px; right:8px; opacity:.8; }
    .profile-dropdown .dropdown-close:hover { opacity:1; }
    .profile-dropdown .dropdown-header { color: #6b7280; }
    .profile-dropdown .dropdown-divider { opacity: .15; }
    .profile-dropdown .dropdown-item { color: #111827; }
    .profile-dropdown .dropdown-item:hover, .profile-dropdown .dropdown-item:focus {
        background: #f3f4f6;
        color: #111827;
    }
    .user-name { color: #0f172a !important; }
    
    /* CRM Main Wrapper */
    .crm-main-wrapper {
        position: relative;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    
    /* Ensure body doesn't interfere */
    body {
        overflow-x: hidden;
    }
    
    /* CRM Sidebar with 3D Effect - Fixed Position (Sticky) */
    .crm-sidebar {
        position: fixed !important;
        top: 78px !important;
        left: 0 !important;
        height: calc(100vh - 78px) !important;
        background: linear-gradient(180deg, rgba(255,255,255,0.95) 0%, rgba(255,255,255,0.98) 100%);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        box-shadow: 4px 0 30px rgba(0,0,0,0.1), 
                    -2px 0 20px rgba(102, 126, 234, 0.08),
                    -1px 0 15px rgba(220, 38, 38, 0.1);
        border-right: 1px solid rgba(220, 38, 38, 0.15);
        transform-style: preserve-3d;
        z-index: 1030;
        overflow-y: auto;
        overflow-x: hidden;
        /* Ensure sidebar stays fixed during scroll */
        transform: translateZ(0);
        -webkit-transform: translateZ(0);
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        /* Prevent any movement */
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Sidebar width based on Bootstrap breakpoints */
    @media (min-width: 992px) {
        .crm-sidebar {
            width: 250px; /* Fixed width for lg */
        }
    }
    
    @media (min-width: 768px) and (max-width: 991.98px) {
        .crm-sidebar {
            width: 220px; /* Fixed width for md */
        }
    }
    
    @media (max-width: 767.98px) {
        .crm-sidebar {
            width: 250px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }
        .crm-sidebar.show {
            transform: translateX(0);
        }
    }
    
    .sidebar-content {
        position: relative;
        z-index: 1;
        padding-bottom: 2rem;
    }
    
    /* Custom scrollbar for sidebar */
    .crm-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    .crm-sidebar::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
    }
    
    .crm-sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #dc2626 100%);
        border-radius: 10px;
    }
    
    .crm-sidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #dc2626 0%, #764ba2 50%, #667eea 100%);
    }
    
    .crm-brand {
        text-align: center;
        padding: 1.5rem 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #dc2626 100%);
        border-radius: 16px;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.25),
                    0 4px 16px rgba(220, 38, 38, 0.2),
                    inset 0 1px 0 rgba(255,255,255,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .crm-brand::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .brand-icon {
        width: 60px;
        height: 60px;
        margin: 0 auto;
        background: rgba(255,255,255,0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2),
                    inset 0 2px 10px rgba(255,255,255,0.3);
        position: relative;
        z-index: 1;
    }
    
    .crm-brand h5 {
        color: white;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    
    .crm-brand small {
        color: rgba(255,255,255,0.9);
        position: relative;
        z-index: 1;
    }
    
    .crm-nav {
        gap: 0.5rem;
    }
    
    .crm-nav-item {
        position: relative;
        color: #6b7280;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 0.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 1rem;
        text-decoration: none;
        background: rgba(255,255,255,0.5);
        border: 1px solid rgba(102, 126, 234, 0.1);
        overflow: hidden;
        transform-style: preserve-3d;
    }
    
    .crm-nav-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    
    .crm-nav-item:hover::before {
        left: 100%;
    }
    
    .crm-nav-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(220, 38, 38, 0.08) 100%);
        color: #dc2626;
        transform: translateX(5px) translateY(-2px);
        box-shadow: 0 8px 25px rgba(220, 38, 38, 0.15),
                    0 4px 10px rgba(0,0,0,0.1);
        border-color: rgba(220, 38, 38, 0.2);
    }
    
    .crm-nav-item.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #dc2626 100%);
        color: white;
        box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3),
                    0 4px 15px rgba(220, 38, 38, 0.25),
                    0 4px 15px rgba(0,0,0,0.2),
                    inset 0 1px 0 rgba(255,255,255,0.2);
        transform: translateX(5px);
        border-color: transparent;
    }
    
    .crm-nav-item.active .nav-icon-wrapper {
        background: rgba(255,255,255,0.2);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2),
                    inset 0 2px 5px rgba(255,255,255,0.3);
    }
    
    .nav-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        background: rgba(220, 38, 38, 0.1);
        transition: all 0.3s ease;
    }
    
    .crm-nav-item.active .nav-icon-wrapper i {
        color: white;
    }
    
    .crm-nav-item span {
        flex: 1;
        font-weight: 500;
    }
    
    .nav-indicator {
        width: 4px;
        height: 0;
        background: white;
        border-radius: 2px;
        transition: height 0.3s ease;
    }
    
    .crm-nav-item.active .nav-indicator {
        height: 60%;
    }
    
    /* Main Content */
    .crm-main-content {
        background: transparent;
        min-height: calc(100vh - 78px);
        width: 100%;
        margin-left: 0;
        padding-left: 0;
    }
    
    /* Adjust main content padding to account for fixed sidebar */
    @media (min-width: 992px) {
        .crm-main-content {
            padding-left: 250px !important; /* lg: sidebar width */
        }
    }
    
    @media (min-width: 768px) and (max-width: 991.98px) {
        .crm-main-content {
            padding-left: 220px !important; /* md: sidebar width */
        }
    }
    
    @media (max-width: 767.98px) {
        .crm-main-content {
            padding-left: 0 !important;
        }
    }
    
    .crm-content-wrapper {
        animation: fadeInUp 0.5s ease;
        max-width: 100%;
        overflow-x: hidden;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Ensure content doesn't get cut off */
    .crm-main-wrapper {
        overflow-x: hidden;
    }
    
    /* Minimalist Card Effect */
    .card-3d {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .card-3d:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: #d1d5db;
    }
    
    /* Minimalist Stat Card */
    .stat-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border-color: #d1d5db;
    }
    
    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-icon {
        transform: scale(1.05);
    }
    
    /* Glassmorphism Effect */
    .glass-card {
        background: rgba(255,255,255,0.25);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
    
    /* Minimalist Table */
    .table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table tbody tr {
        background: #ffffff;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .table tbody tr:hover {
        background: #f9fafb;
    }
    
    .table thead th {
        background: #f9fafb;
        border: none;
        border-bottom: 2px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
        padding: 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .table tbody td {
        border: none;
        padding: 1rem;
        vertical-align: middle;
        color: #6b7280;
    }
    
    /* Minimalist Badge */
    .badge {
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    
    /* Minimalist Button */
    .btn {
        transition: all 0.2s ease;
        border-radius: 8px;
        font-weight: 500;
        border: 1px solid transparent;
    }
    
    .btn:hover {
        transform: translateY(-1px);
    }
    
    .btn-primary {
        background: #667eea;
        border-color: #667eea;
    }
    
    .btn-primary:hover {
        background: #5a67d8;
        border-color: #5a67d8;
    }
    
    /* Minimalist Card Body */
    .card-3d .card-body {
        padding: 1.5rem;
    }
    
    /* Minimalist Alert */
    .alert {
        border-radius: 8px;
        border: 1px solid transparent;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    /* Minimalist Input */
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #d1d5db;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }
    
    ::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #dc2626 100%);
        border-radius: 10px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #dc2626 0%, #764ba2 50%, #667eea 100%);
    }
    </style>
    @yield('scripts')
</body>
</html>

