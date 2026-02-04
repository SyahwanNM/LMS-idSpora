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
        --crm-primary: #6d28d9; /* Royal Purple */
        --crm-secondary: #fbbf24; /* Golden Yellow */
        --crm-navy: #1e1b4b; /* Deep Indigo/Purple Navy */
        --crm-bg: #fdfcfe;
        --crm-sidebar-bg: #ffffff;
        --crm-text-main: #1e293b;
        --crm-text-muted: #64748b;
        --crm-border: #e2e8f0;
        --crm-accent-light: #f5f3ff;
    }

    * { font-family: 'Poppins', sans-serif; }
    
    body {
        background: var(--crm-bg);
        color: var(--crm-text-main);
        min-height: 100vh;
        overflow-x: hidden;
    }
    
    .navbar-crm {
        background: #ffffff;
        border-bottom: 2px solid var(--crm-secondary);
        height: 64px;
        z-index: 1040;
    }

    .navbar-brand .brand-text {
        color: var(--crm-navy) !important;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .navbar-brand .text-primary {
        color: var(--crm-primary) !important;
    }
    
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid var(--crm-secondary);
        transition: all 0.3s ease;
    }

    /* CRM Sidebar */
    .crm-sidebar {
        position: fixed;
        top: 64px;
        left: 0;
        height: calc(100vh - 64px);
        width: 260px;
        background: var(--crm-sidebar-bg);
        border-right: 1px solid var(--crm-border);
        z-index: 1030;
        transition: all 0.3s ease;
        padding: 1.5rem 1rem;
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
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--crm-primary);
        opacity: 0.7;
        letter-spacing: 1px;
        margin: 1.5rem 0 0.75rem 0.75rem;
    }

    .crm-nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: var(--crm-text-main);
        text-decoration: none !important;
        border-radius: 10px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        margin-bottom: 0.25rem;
    }
    
    .crm-nav-item i {
        font-size: 1.1rem;
        color: var(--crm-text-muted);
    }
    
    .crm-nav-item:hover {
        background: var(--crm-accent-light);
        color: var(--crm-primary);
    }

    .crm-nav-item:hover i {
        color: var(--crm-primary);
    }
    
    .crm-nav-item.active {
        background: var(--crm-primary);
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(109, 40, 217, 0.2);
    }
    
    .crm-nav-item.active i {
        color: var(--crm-secondary);
    }
    
    /* Main Content Wrapper */
    .crm-main-content {
        padding-top: 64px;
        padding-left: 260px;
        min-height: 100vh;
        transition: all 0.3s ease;
    }
    
    @media (max-width: 991.98px) {
        .crm-main-content {
            padding-left: 0;
        }
    }
    
    .crm-page-container {
        padding: 2rem;
        animation: fadeIn 0.4s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card-minimal {
        background: #ffffff;
        border: 1px solid var(--crm-border);
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .card-minimal:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
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
        </nav>
        
        <div class="crm-nav-group-title">Bantuan & Support</div>
        <nav class="nav flex-column">
            <a class="crm-nav-item" href="#">
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
