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
    <style>
    :root {
        --crm-primary: #6d28d9; 
        --crm-primary-light: #8b5cf6;
        --crm-primary-dark: #5b21b6;
        --crm-secondary: #fbbf24; 
        --crm-navy: #1e1b4b; 
        --crm-bg: #f8fafc;
        --crm-sidebar-bg: #ffffff;
        --crm-text-main: #1e293b;
        --crm-text-muted: #64748b;
        --crm-border: #e2e8f0;
        --crm-accent-light: #f5f3ff;
        --crm-glass: rgba(255, 255, 255, 0.8);
    }

    * { font-family: 'Poppins', sans-serif; }
    
    body {
        background: var(--crm-bg);
        color: var(--crm-text-main);
        min-height: 100vh;
        overflow-x: hidden;
    }
    
    .navbar-crm {
        background: var(--crm-glass);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        height: 70px;
        z-index: 1040;
    }

    .navbar-brand .brand-text {
        color: var(--crm-navy) !important;
        font-weight: 800;
        letter-spacing: -0.5px;
        font-size: 1.25rem;
    }

    .navbar-brand .text-primary {
        color: var(--crm-primary) !important;
    }
    
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        overflow: hidden;
        border: 2px solid var(--crm-accent-light);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-circle:hover {
        transform: translateY(-2px);
        border-color: var(--crm-primary-light);
        box-shadow: 0 4px 12px rgba(109, 40, 217, 0.15);
    }

    /* CRM Sidebar */
    .crm-sidebar {
        position: fixed;
        top: 70px;
        left: 0;
        height: calc(100vh - 70px);
        width: 280px;
        background: var(--crm-sidebar-bg);
        border-right: 1px solid var(--crm-border);
        z-index: 1030;
        transition: all 0.3s ease;
        padding: 2rem 1.25rem;
        overflow-y: auto;
    }
    
    @media (max-width: 991.98px) {
        .crm-sidebar {
            transform: translateX(-100%);
        }
        .crm-sidebar.show {
            transform: translateX(0);
        }
    }
    
    .crm-nav-group-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--crm-text-muted);
        opacity: 0.8;
        letter-spacing: 1.5px;
        margin: 2rem 0 1rem 0.75rem;
    }

    .crm-nav-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.85rem 1.25rem;
        color: var(--crm-text-main);
        text-decoration: none !important;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 0.5rem;
        border: 1px solid transparent;
    }
    
    .crm-nav-item i {
        font-size: 1.25rem;
        color: var(--crm-text-muted);
        transition: all 0.25s ease;
    }
    
    .crm-nav-item:hover {
        background: var(--crm-accent-light);
        color: var(--crm-primary);
        transform: translateX(4px);
    }

    .crm-nav-item:hover i {
        color: var(--crm-primary);
    }
    
    .crm-nav-item.active {
        background: linear-gradient(135deg, var(--crm-primary), var(--crm-primary-dark));
        color: #ffffff;
        box-shadow: 0 8px 16px -4px rgba(109, 40, 217, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .crm-nav-item.active i {
        color: #ffffff;
    }
    
    /* Main Content Wrapper */
    .crm-main-content {
        padding-top: 70px;
        padding-left: 280px;
        min-height: 100vh;
        transition: all 0.3s ease;
    }
    
    @media (max-width: 991.98px) {
        .crm-main-content {
            padding-left: 0;
        }
    }
    
    .crm-page-container {
        padding: 2.5rem;
        animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card-minimal {
        background: #ffffff;
        border: 1px solid var(--crm-border);
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    
    .card-minimal:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: rgba(109, 40, 217, 0.1);
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--crm-bg); }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; border: 2px solid var(--crm-bg); }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    @yield('styles')
</head>
<body>
    @php $user = auth()->user(); @endphp
    <nav class="navbar navbar-crm navbar-expand-lg fixed-top">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('aset/logo.png') }}" alt="logo" class="me-2" style="height:24px;">
                <span class="brand-text">Admin <span class="text-primary">CRM</span></span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#crmNavbar">
                <i class="bi bi-list fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="crmNavbar">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                        <a class="nav-link text-muted fw-medium" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-grid-fill me-1"></i> Dashboard Utama
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center dropdown-toggle p-0" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="avatar-circle">
                                <img src="{{ $user?->avatar_url }}" alt="avatar" class="w-100 h-100 object-fit-cover">
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 mt-2" style="border-radius: 12px; min-width: 200px;">
                            <li><h6 class="dropdown-header text-muted small">Akun Admin</h6></li>
                            <li><a class="dropdown-item rounded-2 py-2" href="{{ route('admin.settings') }}"><i class="bi bi-gear me-2"></i> Pengaturan</a></li>
                            <li><hr class="dropdown-divider mx-2"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger rounded-2 py-2">
                                        <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <aside class="crm-sidebar" id="sidebar">
        <div class="crm-nav-group-title">Menu Utama</div>
        <nav class="nav flex-column">
            <a class="crm-nav-item {{ request()->routeIs('admin.crm.dashboard') ? 'active' : '' }}" href="{{ route('admin.crm.dashboard') }}">
                <i class="bi bi-speedometer2"></i>
                <span>Analitik Ringkas</span>
            </a>
            <a class="crm-nav-item {{ request()->routeIs('admin.crm.customers.*') ? 'active' : '' }}" href="{{ route('admin.crm.customers.index') }}">
                <i class="bi bi-people"></i>
                <span>Data Pelanggan</span>
            </a>
        </nav>

        <div class="crm-nav-group-title">Operasional</div>
        <nav class="nav flex-column">
            <a class="crm-nav-item {{ request()->routeIs('admin.crm.certificates.*') ? 'active' : '' }}" href="{{ route('admin.crm.certificates.index') }}">
                <i class="bi bi-award"></i>
                <span>Sertifikat</span>
            </a>
            <a class="crm-nav-item {{ request()->routeIs('admin.crm.feedback.*') ? 'active' : '' }}" href="{{ route('admin.crm.feedback.index') }}">
                <i class="bi bi-chat-heart"></i>
                <span>Analisis Feedback</span>
            </a>
            <a class="crm-nav-item {{ request()->routeIs('admin.crm.broadcast.*') ? 'active' : '' }}" href="{{ route('admin.crm.broadcast.index') }}">
                <i class="bi bi-megaphone"></i>
                <span>Blast Broadcast</span>
            </a>
        </nav>
        
        <div class="crm-nav-group-title">Bantuan & Support</div>
        <nav class="nav flex-column">
            <a class="crm-nav-item {{ request()->routeIs('admin.crm.support.*') ? 'active' : '' }}" href="{{ route('admin.crm.support.index') }}">
                <i class="bi bi-headset"></i>
                <span>Tiket Support</span>
            </a>
        </nav>
    </aside>

    <div class="crm-main-content">
        <div class="crm-page-container">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
