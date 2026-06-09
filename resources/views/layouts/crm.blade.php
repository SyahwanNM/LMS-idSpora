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
        --crm-primary: #7c3aed;
        --crm-primary-light: #a78bfa;
        --crm-primary-dark: #5b21b6;
        --crm-primary-bg: #f5f3ff;
        --crm-secondary: #f59e0b;
        --crm-navy: #0f172a;
        --crm-navy-soft: #1e293b;
        --crm-bg: #f8fafc;
        --crm-sidebar-bg: #ffffff;
        --crm-text-main: #0f172a;
        --crm-text-muted: #64748b;
        --crm-text-subtle: #94a3b8;
        --crm-border: #e2e8f0;
        --crm-border-soft: #f1f5f9;
        --crm-accent-light: #f5f3ff;
        --crm-glass: rgba(255, 255, 255, 0.85);
        --crm-shadow-sm: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
        --crm-shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
        --crm-shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
        --crm-radius-sm: 8px;
        --crm-radius-md: 12px;
        --crm-radius-lg: 16px;
        --crm-radius-xl: 20px;
    }

    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

    body {
        background: var(--crm-bg);
        color: var(--crm-text-main);
        min-height: 100vh;
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
    }

    /* ─── NAVBAR ─────────────────────────────── */
    .navbar-crm {
        background: var(--crm-glass);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-bottom: 1px solid var(--crm-border);
        height: 64px;
        z-index: 1040;
    }

    .navbar-brand .brand-text {
        color: var(--crm-navy) !important;
        font-weight: 800;
        letter-spacing: -0.5px;
        font-size: 1.15rem;
    }

    .navbar-brand .text-primary { color: var(--crm-primary) !important; }

    .avatar-circle {
        width: 36px; height: 36px;
        border-radius: 10px;
        overflow: hidden;
        border: 1.5px solid var(--crm-border);
        transition: all 0.25s ease;
        background: var(--crm-bg);
        display: flex; align-items: center; justify-content: center;
    }
    .avatar-circle:hover {
        border-color: var(--crm-primary-light);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }

    /* ─── SIDEBAR ─────────────────────────────── */
    .crm-sidebar {
        position: fixed;
        top: 64px; left: 0;
        height: calc(100vh - 64px);
        width: 240px;
        background: var(--crm-sidebar-bg);
        border-right: 1px solid var(--crm-border);
        z-index: 1030;
        transition: all 0.3s ease;
        padding: 1.25rem 0.75rem;
        overflow-y: auto;
    }

    @media (max-width: 991.98px) {
        .crm-sidebar { transform: translateX(-100%); }
        .crm-sidebar.show { transform: translateX(0); }
    }

    .crm-nav-group-title {
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--crm-text-subtle);
        letter-spacing: 0.8px;
        margin: 1.5rem 0 0.4rem 0.75rem;
    }

    .crm-nav-item {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.5rem 0.75rem;
        color: var(--crm-text-muted);
        text-decoration: none !important;
        border-radius: var(--crm-radius-sm);
        font-weight: 500;
        font-size: 0.82rem;
        transition: all 0.2s ease;
        margin-bottom: 2px;
        border: none;
        position: relative;
    }

    .crm-nav-item i {
        font-size: 1rem;
        color: var(--crm-text-subtle);
        transition: all 0.2s ease;
        flex-shrink: 0;
        width: 18px;
        text-align: center;
    }

    .crm-nav-item:hover {
        background: var(--crm-border-soft);
        color: var(--crm-navy);
    }
    .crm-nav-item:hover i { color: var(--crm-navy-soft); }

    .crm-nav-item.active {
        background: var(--crm-primary-bg);
        color: var(--crm-primary);
        font-weight: 600;
    }
    .crm-nav-item.active::before {
        content: '';
        position: absolute;
        left: -0.75rem;
        top: 50%; transform: translateY(-50%);
        width: 3px; height: 60%;
        background: var(--crm-primary);
        border-radius: 0 3px 3px 0;
    }
    .crm-nav-item.active i { color: var(--crm-primary); }

    /* ─── MAIN CONTENT ────────────────────────── */
    .crm-main-content {
        padding-top: 64px;
        padding-left: 240px;
        min-height: 100vh;
        transition: all 0.3s ease;
    }
    @media (max-width: 991.98px) {
        .crm-main-content { padding-left: 0; }
    }

    .crm-page-container {
        padding: 2rem 2.25rem;
        animation: fadeSlideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    @keyframes fadeSlideUp {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* ─── PAGE HEADER ─────────────────────────── */
    .crm-page-header {
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--crm-border-soft);
    }

    /* ─── CARDS ───────────────────────────────── */
    .card-minimal {
        background: #ffffff;
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius-xl);
        box-shadow: var(--crm-shadow-sm);
        transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
        overflow: hidden;
    }
    .card-minimal:hover {
        box-shadow: var(--crm-shadow-md);
        border-color: rgba(124, 58, 237, 0.15);
    }

    /* ─── KPI CARDS (Premium) ─────────────────── */
    .kpi-card-v2 {
        background: #fff;
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius-lg);
        padding: 1.4rem 1.5rem;
        box-shadow: var(--crm-shadow-sm);
        transition: all 0.25s ease;
        cursor: default;
        position: relative;
        overflow: hidden;
    }
    .kpi-card-v2::after {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 80px; height: 80px;
        border-radius: 0 var(--crm-radius-lg) 0 100%;
        opacity: 0.06;
        background: var(--kpi-color, var(--crm-primary));
        transition: opacity 0.25s ease;
    }
    .kpi-card-v2:hover {
        box-shadow: var(--crm-shadow-lg);
        transform: translateY(-2px);
        border-color: rgba(124, 58, 237, 0.2);
    }
    .kpi-card-v2:hover::after { opacity: 0.1; }

    .kpi-icon-v2 {
        width: 42px; height: 42px;
        border-radius: var(--crm-radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        margin-bottom: 1rem;
    }
    .kpi-label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: var(--crm-text-subtle);
        margin-bottom: 0.25rem;
    }
    .kpi-number {
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--crm-navy);
        letter-spacing: -1px;
        line-height: 1;
        margin-bottom: 0.6rem;
    }
    .kpi-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 100px;
    }
    .kpi-trend.up { background: #dcfce7; color: #15803d; }
    .kpi-trend.neutral { background: #f1f5f9; color: var(--crm-text-muted); }
    .kpi-trend.down { background: #fef2f2; color: #dc2626; }

    /* ─── TABLES ──────────────────────────────── */
    .crm-table { width: 100%; border-collapse: collapse; }
    .crm-table thead tr {
        border-bottom: 1px solid var(--crm-border);
    }
    .crm-table thead th {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        color: var(--crm-text-subtle);
        padding: 0.85rem 1rem;
        white-space: nowrap;
        background: transparent;
    }
    .crm-table tbody tr {
        border-bottom: 1px solid var(--crm-border-soft);
        transition: background 0.15s ease;
        cursor: pointer;
    }
    .crm-table tbody tr:hover {
        background: #fafafa;
    }
    .crm-table tbody tr:hover .row-action-btn {
        opacity: 1;
        transform: translateX(0);
    }
    .crm-table tbody td {
        padding: 0.9rem 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
    }
    .row-action-btn {
        opacity: 0;
        transform: translateX(-4px);
        transition: all 0.2s ease;
    }

    /* ─── BADGES ──────────────────────────────── */
    .badge-soft {
        display: inline-flex;
        align-items: center;
        font-size: 0.68rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 100px;
        letter-spacing: 0.2px;
    }

    /* ─── QUICK-ACTION CARD ───────────────────── */
    .quick-action-card {
        background: linear-gradient(135deg, var(--crm-primary) 0%, var(--crm-primary-dark) 100%);
        border-radius: var(--crm-radius-lg);
        padding: 1.5rem;
        color: white;
    }

    /* ─── STAT BAR ────────────────────────────── */
    .stat-bar { height: 5px; background: var(--crm-border-soft); border-radius: 100px; overflow: hidden; }
    .stat-bar-fill { height: 100%; border-radius: 100px; transition: width 0.8s ease; }

    /* ─── SCROLLBAR ───────────────────────────── */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    /* ─── FINAL POLISH: ANIMATIONS ────────────── */
    .fade-in-up {
        animation: fadeInUp 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes fadeInUp {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .hover-scale { transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1); }
    .hover-scale:hover { transform: scale(1.05); }

    .pulse-soft { animation: pulseSoft 2s infinite; }
    @keyframes pulseSoft {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(124, 58, 237, 0.2); }
        70% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(124, 58, 237, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(124, 58, 237, 0); }
    }

    /* ─── FINAL POLISH: TOAST SYSTEM ──────────── */
    .toast-container-custom {
        position: fixed; top: 80px; right: 24px; z-index: 9999;
    }
    .crm-toast {
        background: #fff; border-radius: 14px; padding: 12px 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 1px solid var(--crm-border);
        display: flex; align-items: center; gap: 12px; margin-bottom: 12px;
        min-width: 320px; max-width: 400px;
        transform: translateX(120%); transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }
    .crm-toast.show { transform: translateX(0); }
    .crm-toast-icon {
        width: 38px; height: 38px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;
    }
    .crm-toast-success .crm-toast-icon { background: rgba(16,185,129,0.1); color: #10b981; }
    .crm-toast-error .crm-toast-icon { background: rgba(239,68,68,0.1); color: #ef4444; }
    .crm-toast-info .crm-toast-icon { background: rgba(124,58,237,0.1); color: var(--crm-primary); }
    
    .crm-toast-content { flex-grow: 1; }
    .crm-toast-title { font-size: 0.85rem; font-weight: 800; color: var(--crm-navy); margin-bottom: 2px; }
    .crm-toast-message { font-size: 0.75rem; color: var(--crm-text-muted); }
    
    .crm-toast-close {
        color: var(--crm-text-subtle); cursor: pointer; padding: 4px; transition: color 0.2s;
    }
    .crm-toast-close:hover { color: var(--crm-navy); }

    /* ─── FINAL POLISH: EMPTY STATES ───────────── */
    .empty-state-wrapper {
        text-align: center; padding: 4rem 2rem;
    }
    .empty-state-icon {
        width: 80px; height: 80px; background: var(--crm-border-soft);
        border-radius: 20px; display: inline-flex; align-items: center;
        justify-content: center; font-size: 2.5rem; color: var(--crm-text-subtle);
        margin-bottom: 1.5rem; opacity: 0.5;
    }
    </style>
    @yield('styles')
</head>
<body>
    @php $user = auth()->user(); @endphp
    
    {{-- Global Toast Container --}}
    <div class="toast-container-custom" id="globalToastContainer"></div>

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
                                <img src="{{ $user?->avatar_url }}" alt="avatar" class="w-100 h-100 object-fit-cover" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user?->name ?? 'Admin') }}&background=7c3aed&color=fff'">
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
            <a class="crm-nav-item {{ request()->routeIs('admin.crm.vouchers.*') ? 'active' : '' }}" href="{{ route('admin.crm.vouchers.index') }}">
                <i class="bi bi-ticket-perforated"></i>
                <span>Loyalty & Voucher</span>
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
        <div class="crm-page-container fade-in-up">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // ─── CRM GLOBAL UTILITIES ──────────────────────
    const CRM = {
        toast: function(title, message, type = 'success') {
            const container = document.getElementById('globalToastContainer');
            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };
            
            const toastId = 'toast-' + Math.random().toString(36).substr(2, 9);
            const html = `
                <div id="${toastId}" class="crm-toast crm-toast-${type}">
                    <div class="crm-toast-icon"><i class="bi ${icons[type] || icons.info}"></i></div>
                    <div class="crm-toast-content">
                        <div class="crm-toast-title">${title}</div>
                        <div class="crm-toast-message">${message}</div>
                    </div>
                    <div class="crm-toast-close" onclick="CRM.closeToast('${toastId}')"><i class="bi bi-x-lg"></i></div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', html);
            const element = document.getElementById(toastId);
            
            setTimeout(() => element.classList.add('show'), 100);
            
            setTimeout(() => {
                CRM.closeToast(toastId);
            }, 5000);
        },
        closeToast: function(id) {
            const element = document.getElementById(id);
            if (element) {
                element.classList.remove('show');
                setTimeout(() => element.remove(), 400);
            }
        }
    };

    // Auto-trigger session toasts
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            CRM.toast('Berhasil!', "{{ session('success') }}", 'success');
        @endif
        @if(session('error'))
            CRM.toast('Gagal!', "{{ session('error') }}", 'error');
        @endif
        @if(session('info'))
            CRM.toast('Informasi', "{{ session('info') }}", 'info');
        @endif
    });
    </script>
    @yield('scripts')
    @stack('modals')
</body>
</html>

