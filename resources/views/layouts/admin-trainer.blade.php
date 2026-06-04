@extends('layouts.admin')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
        <style>
            :root {
                --admin-primary: #1e1b4b;
                --admin-secondary: #3949ab;
                --admin-bg: #f8fafc;
                --admin-border: #e2e8f0;
                --admin-text-muted: #64748b;
            }

            body {
                background-color: var(--admin-bg);
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
                background: #3949ab;
                border-color: #3949ab;
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
            }

            .sidebar-link i {
                font-size: 1.15rem;
                color: #64748b;
                transition: color 0.2s ease;
            }

            .sidebar-link:hover {
                background-color: #f8fafc;
                color: #3949ab;
            }

            .sidebar-link:hover i {
                color: #3949ab;
            }

            .sidebar-link.active {
                background-color: #3949ab;
                color: #fff;
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
                background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
                border-radius: 24px;
                padding: 36px;
                color: #fff;
                margin-bottom: 28px;
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(255, 255, 255, 0.1);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
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

            @media (max-width: 991.98px) {
                .admin-trainer-wrapper {
                    display: block;
                }

                .admin-trainer-main {
                    padding: 20px;
                    padding-top: calc(20px + 72px);
                    width: 100%;
                }

                .trainer-sidebar {
                    display: none;
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
            <button class="btn btn-primary admin-sidebar-toggle d-lg-none mb-3" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#adminTrainerSidebarMobile" aria-controls="adminTrainerSidebarMobile">
                <i class="bi bi-list me-2"></i>
                Menu Admin Trainer
            </button>

            @yield('admin-trainer-content')
        </main>
    </div>

    <!-- Mobile offcanvas is provided by the sidebar partial to avoid duplicate IDs -->
@endsection

@section('scripts')
    @stack('admin-trainer-scripts')
@endsection