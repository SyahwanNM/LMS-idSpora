@extends('layouts.admin')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

            :root {
                --admin-primary: #1e1b4b;
                --admin-secondary: #1e1b4b;
                --admin-accent: #1e1b4b;
                --admin-bg: #f8fafc;
                --admin-border: #e2e8f0;
                --admin-text-muted: #64748b;
            }

            body {
                background-color: var(--admin-bg);
                font-family: 'Plus Jakarta Sans', sans-serif !important;
            }

            html {
                scrollbar-gutter: stable;
            }

            .admin-trainer-wrapper {
                display: flex;
                min-height: calc(100vh - 72px);
                overflow-x: hidden;
            }

            .trainer-sidebar {
                width: 260px;
                background: #fff;
                padding: 24px 16px;
                border-right: 1px solid #eee;
                flex-shrink: 0;
                position: sticky;
                top: 72px;
                height: calc(100vh - 72px);
                overflow-y: auto;
            }

            .admin-trainer-main {
                flex-grow: 1;
                min-width: 0;
                padding: 32px;
                background-color: #F8F9FA;
                overflow-x: auto;
            }

            .admin-sidebar-toggle {
                width: 100%;
                border-radius: 14px;
                font-weight: 700;
                background: var(--admin-secondary);
                border-color: var(--admin-secondary);
            }

            .admin-trainer-offcanvas {
                width: 290px;
                border-right: 0;
            }

            .admin-trainer-offcanvas .offcanvas-header {
                border-bottom: 1px solid #e2e8f0;
            }

            .admin-trainer-offcanvas .offcanvas-body {
                padding: 20px 16px;
            }

            .nav-menu-label {
                font-size: 11px;
                text-transform: uppercase;
                font-weight: 700;
                color: #94a3b8;
                letter-spacing: 1px;
                margin-bottom: 12px;
                margin-top: 24px;
                display: block;
                padding-left: 16px;
            }

            .nav-menu-label:first-child {
                margin-top: 0;
            }

            .sidebar-link {
                display: flex;
                align-items: center;
                padding: 11px 16px;
                color: #1e293b;
                text-decoration: none;
                border-radius: 10px;
                margin-bottom: 4px;
                font-weight: 600;
                font-size: 0.9rem;
                transition: all 0.2s ease;
                gap: 12px;
                border: 0;
                width: 100%;
                position: relative;
            }

            .sidebar-link::before {
                content: '';
                position: absolute;
                left: 0;
                top: 20%;
                height: 60%;
                width: 4px;
                background-color: var(--admin-accent);
                border-radius: 0 4px 4px 0;
                opacity: 0;
                transition: opacity 0.2s ease;
            }

            .sidebar-link.active::before {
                opacity: 1;
            }

            .sidebar-link i {
                font-size: 1.15rem;
                color: #64748b;
                transition: color 0.2s ease;
            }

            .sidebar-link:hover {
                background-color: #eff6ff;
                color: var(--admin-secondary);
            }

            .sidebar-link:hover i {
                color: var(--admin-secondary);
            }

            .sidebar-link.active {
                background-color: var(--admin-secondary);
                color: #fff;
                box-shadow: 0 4px 12px rgba(30, 27, 75, 0.15);
            }

            .sidebar-link.active i {
                color: #fff;
            }

            .sidebar-parent {
                justify-content: space-between;
            }

            .sidebar-parent span {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .sidebar-chevron {
                font-size: 0.8rem;
                transition: transform 0.2s ease;
                margin-left: auto;
            }

            .sidebar-parent[aria-expanded='true'] .sidebar-chevron {
                transform: rotate(180deg);
            }

            .sidebar-submenu {
                margin: 4px 0 8px;
            }

            .sidebar-submenu .sidebar-link {
                margin-left: 14px;
                padding: 7px 10px;
                font-size: 0.82rem;
                border-radius: 8px;
                width: calc(100% - 14px);
            }

            .sidebar-submenu .sidebar-link i {
                font-size: 0.95rem;
            }

            .admin-trainer-hero {
                background-color: var(--admin-secondary);
                border-radius: 24px;
                padding: 36px;
                color: #fff;
                margin-bottom: 28px;
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 20px 40px rgba(30, 27, 75, 0.12);
            }

            .admin-card {
                border: 0;
                border-radius: 18px;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
                overflow: hidden;
            }

            .admin-card .card-header {
                background: #f8f9ff;
                border-bottom: 1px solid #e9ecef;
                color: #1a237e;
                font-weight: 800;
                padding: 16px 20px;
            }

            .admin-table thead th {
                font-size: 13px;
                text-transform: uppercase;
                letter-spacing: .04em;
                color: #334155;
                background: #f8fafc;
                border-bottom: 1px solid #e2e8f0;
                padding: 14px 16px;
                white-space: nowrap;
            }

            .admin-table tbody td {
                padding: 14px 16px;
                vertical-align: middle;
                border-color: #eef2f7;
            }

            /* Desktop defaults: hide mobile elements */
            .mobile-only {
                display: none !important;
            }

            .trainer-sidebar .sidebar-link span.mobile-only {
                display: none !important;
            }

            .trainer-sidebar .sidebar-link.mobile-only {
                display: none !important;
            }

            @media (max-width: 991.98px) {
                .admin-trainer-wrapper {
                    display: block;
                }

                .admin-trainer-main {
                    padding: 20px;
                    padding-top: 20px;
                    padding-bottom: 100px;
                    width: 100%;
                }

                /* Mobile overrides: hide desktop, show mobile */
                .desktop-only {
                    display: none !important;
                }

                .mobile-only {
                    display: flex !important;
                }

                .trainer-sidebar span.mobile-only {
                    display: block !important;
                }

                .trainer-sidebar .sidebar-link.mobile-only {
                    display: flex !important;
                }

                .trainer-sidebar .sidebar-link span.mobile-only {
                    display: block !important;
                }

                .trainer-sidebar .sidebar-link.desktop-only {
                    display: none !important;
                }

                .trainer-sidebar .sidebar-link span.desktop-only {
                    display: none !important;
                }

                .trainer-sidebar {
                    height: 65px !important;
                    width: 92% !important;
                    left: 4% !important;
                    bottom: 16px !important;
                    position: fixed !important;
                    top: auto !important;
                    border-right: none !important;
                    border-radius: 20px !important;
                    padding: 0 !important;
                    display: flex !important;
                    flex-direction: row !important;
                    justify-content: space-around !important;
                    align-items: center !important;
                    z-index: 1000 !important;
                    background: rgba(255, 255, 255, 0.55) !important;
                    backdrop-filter: blur(24px) saturate(180%) !important;
                    -webkit-backdrop-filter: blur(24px) saturate(180%) !important;
                    border: 1px solid rgba(255, 255, 255, 0.8) !important;
                    box-shadow: 0 10px 40px rgba(31, 38, 135, 0.07), inset 0 0 0 1px rgba(255,255,255,0.3) !important;
                    overflow: hidden !important;
                }

                .trainer-sidebar .nav-menu-label,
                .trainer-sidebar .sidebar-submenu,
                .trainer-sidebar .sidebar-chevron {
                    display: none !important;
                }

                .trainer-sidebar .sidebar-link {
                    flex: 1;
                    height: 48px;
                    padding: 4px 0 !important;
                    margin: 2px 4px !important;
                    border-radius: 12px !important;
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    gap: 2px !important;
                    background: transparent !important;
                    color: #475569 !important;
                    text-decoration: none !important;
                    transition: all 0.3s ease !important;
                }

                .trainer-sidebar .sidebar-link:hover {
                    transform: translateY(-2px) !important;
                    background-color: rgba(30, 27, 75, 0.08) !important;
                    color: #1e1b4b !important;
                }

                .trainer-sidebar .sidebar-link.active {
                    background-color: var(--admin-secondary) !important;
                    color: #ffffff !important;
                    box-shadow: 0 4px 12px rgba(30, 27, 75, 0.3) !important;
                    transform: translateY(-2px) !important;
                }

                .trainer-sidebar .sidebar-link i {
                    font-size: 1.2rem !important;
                    color: inherit !important;
                    margin: 0 !important;
                    transition: all 0.3s ease !important;
                }

                .trainer-sidebar .sidebar-link span:not(.desktop-only) {
                    display: block !important;
                    font-size: 10px !important;
                    line-height: 1 !important;
                    margin-top: 2px !important;
                    font-weight: 600 !important;
                }
            }

            @media (min-width: 992px) {
                .trainer-sidebar {
                    position: fixed;
                    top: 72px;
                    left: 0;
                    bottom: 0;
                    height: auto;
                    overflow-y: auto;
                    z-index: 1040;
                }

                .admin-trainer-main {
                    margin-left: 260px;
                    padding: 32px;
                }

                .admin-trainer-wrapper {
                    min-height: 100vh;
                }
            }
        </style>

    @stack('admin-trainer-styles')
@endsection

@section('content')
    <div class="admin-trainer-wrapper">
        @include('admin.trainer.partials.sidebar')

        <main class="admin-trainer-main">
            @yield('admin-trainer-content')
        </main>
    </div>

    <!-- Mobile offcanvas is provided by the sidebar partial to avoid duplicate IDs -->
@endsection

@section('scripts')
    @stack('admin-trainer-scripts')
@endsection