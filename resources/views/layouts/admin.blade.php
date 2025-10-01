<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - @yield('title', 'Event')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <!-- Enhanced Admin Navbar -->
    <header class="shadow-sm mb-4">
        <nav class="navbar navbar-expand-lg navbar-light" style="background:linear-gradient(90deg,#ffffff 0%,#f8fafc 100%); border-bottom:1px solid #e5e7eb;">
            <div class="container">
                <div class="d-flex align-items-center me-3">
                    <a href="{{ route('admin.dashboard') }}" class="d-flex align-items-center text-decoration-none me-2">
                        <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="logo" style="height:32px;" class="me-2">
                    </a>
                    <div class="d-none d-md-block">
                        <span class="fw-semibold text-dark" style="font-size:1.05rem;">Admin Panel</span>
                        <div class="text-muted small">@yield('title','Manajemen')</div>
                    </div>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav" aria-controls="adminNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.courses.index') }}"><i class="bi bi-journal-bookmark me-1"></i>Courses</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.events.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.events.index') }}"><i class="bi bi-calendar-event me-1"></i>Events</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-semibold' : '' }}" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-1"></i>Users</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.reports') ? 'active fw-semibold' : '' }}" href="{{ route('admin.reports') }}"><i class="bi bi-graph-up me-1"></i>Reports</a></li>
                    </ul>
                    <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center" style="gap:.65rem;">
                        <li class="nav-item d-none d-lg-block">
                            <a href="{{ route('landing-page') }}" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Public Site</a>
                        </li>
                        <li class="nav-item d-none d-lg-block">
                            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-house-door me-1"></i>User Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">@csrf
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    {{-- Quick Actions Scrollable Bar (override with @section('admin_quick_actions') if needed) --}}
    @hasSection('admin_quick_actions')
        <div class="admin-quick-actions-wrapper">@yield('admin_quick_actions')</div>
    @else
        <div class="admin-quick-actions-wrapper">
            <div class="admin-quick-actions-scroll" role="navigation" aria-label="Quick actions" tabindex="0">
                <a href="{{ route('admin.courses.create') }}" class="qa-btn"><i class="bi bi-plus-circle me-1"></i>New Course</a>
                <a href="{{ route('admin.events.create') }}" class="qa-btn"><i class="bi bi-calendar-plus me-1"></i>New Event</a>
                <a href="{{ route('admin.users.create') }}" class="qa-btn"><i class="bi bi-person-plus me-1"></i>New User</a>
                <a href="{{ route('admin.courses.index') }}" class="qa-btn"><i class="bi bi-grid me-1"></i>All Courses</a>
                <a href="{{ route('admin.events.index') }}" class="qa-btn"><i class="bi bi-calendar-event me-1"></i>All Events</a>
                <a href="{{ route('admin.users.index') }}" class="qa-btn"><i class="bi bi-people me-1"></i>All Users</a>
                <a href="{{ route('admin.reports') }}" class="qa-btn"><i class="bi bi-graph-up me-1"></i>Reports</a>
            </div>
        </div>
    @endif
    <div class="container">
        @yield('content')
    </div>
    
    <!-- Admin Footer -->
    <footer class="mt-5" style="background: linear-gradient(to right, #d97706, #eab308);">
        <div class="container py-4">
            <div class="row">
                <!-- Brand Section -->
                <div class="col-md-4 mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="idSpora Logo" class="me-3" style="height: 32px;">
                        <span class="h5 text-white fw-bold mb-0">idSpora</span>
                    </div>
                    <p class="text-white-50 small mb-3">
                        Learning Management System yang memudahkan proses pembelajaran dan pengembangan skill di era digital.
                    </p>
                    <div class="d-flex">
                        <a href="#" class="text-white-50 me-3" style="text-decoration: none;">
                            <i class="bi bi-twitter fs-5"></i>
                        </a>
                        <a href="#" class="text-white-50 me-3" style="text-decoration: none;">
                            <i class="bi bi-facebook fs-5"></i>
                        </a>
                        <a href="#" class="text-white-50" style="text-decoration: none;">
                            <i class="bi bi-linkedin fs-5"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-semibold mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('admin.dashboard') }}" class="text-white-50 text-decoration-none small">Dashboard</a></li>
                        <li class="mb-2"><a href="{{ route('admin.courses.index') }}" class="text-white-50 text-decoration-none small">Manage Courses</a></li>
                        <li class="mb-2"><a href="{{ route('admin.events.index') }}" class="text-white-50 text-decoration-none small">Manage Events</a></li>
                        <li class="mb-2"><a href="{{ route('admin.users.index') }}" class="text-white-50 text-decoration-none small">Manage Users</a></li>
                        <li class="mb-2"><a href="{{ route('admin.reports') }}" class="text-white-50 text-decoration-none small">Analytics</a></li>
                        <li class="mb-2"><a href="{{ route('landing-page') }}" class="text-white-50 text-decoration-none small">Public Site</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="col-md-4 mb-4">
                    <h5 class="text-white fw-semibold mb-3">Contact Info</h5>
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-envelope text-white-50 me-2"></i>
                            <span class="text-white-50 small">admin@idspora.com</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-telephone text-white-50 me-2"></i>
                            <span class="text-white-50 small">+62 21 1234 5678</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-geo-alt text-white-50 me-2"></i>
                            <span class="text-white-50 small">Jakarta, Indonesia</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-top mt-4 pt-3" style="border-color: rgba(255,255,255,0.2) !important;">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="text-white-50 small">
                            © {{ date('Y') }} idSpora. All rights reserved.
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <div class="d-flex justify-content-md-end gap-3">
                            <a href="#" class="text-white-50 text-decoration-none small">Privacy Policy</a>
                            <a href="#" class="text-white-50 text-decoration-none small">Terms of Service</a>
                            <a href="#" class="text-white-50 text-decoration-none small">Help Center</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .navbar-nav .nav-link {position:relative;}
        .navbar-nav .nav-link.active {color:#0d6efd !important;}
        .navbar-nav .nav-link.active:after {
            content:"";position:absolute;left:8px;right:8px;bottom:0;height:3px;border-radius:3px;background:#0d6efd;
        }
        .admin-quick-actions-wrapper {background:#ffffff;border-bottom:1px solid #e5e7eb;}
        .admin-quick-actions-scroll {display:flex;gap:.65rem;overflow-x:auto;padding:.55rem 1rem;scroll-snap-type:x mandatory;-webkit-overflow-scrolling:touch; position:relative;}
        .admin-quick-actions-scroll::-webkit-scrollbar {height:6px;}
        .admin-quick-actions-scroll::-webkit-scrollbar-track {background:transparent;}
        .admin-quick-actions-scroll::-webkit-scrollbar-thumb {background:#d1d5db;border-radius:20px;}
        .qa-btn {scroll-snap-align:start;flex:0 0 auto;display:inline-flex;align-items:center;font-size:.75rem;font-weight:500;line-height:1;padding:.5rem .85rem;border:1px solid #e5e7eb;border-radius:30px;background:#f8fafc;text-decoration:none;color:#374151;transition:all .18s ease;white-space:nowrap;}
        .qa-btn:hover {background:#0d6efd;color:#fff;border-color:#0d6efd;}
        .qa-btn:active {transform:scale(.95);}    
        @media (max-width: 576px){
            .qa-btn {font-size:.7rem;padding:.45rem .75rem;}
        }
        .admin-quick-actions-wrapper {position:relative;}
        .admin-quick-actions-wrapper:before, .admin-quick-actions-wrapper:after {content:"";position:absolute;top:0;bottom:0;width:40px;pointer-events:none;z-index:2;}
        .admin-quick-actions-wrapper:before {left:0;background:linear-gradient(90deg,#fff 0%,rgba(255,255,255,0));}
        .admin-quick-actions-wrapper:after {right:0;background:linear-gradient(-90deg,#fff 0%,rgba(255,255,255,0));}
        .admin-quick-actions-scroll:focus-visible {outline:2px solid #0d6efd;outline-offset:2px;}
    </style>
    @yield('scripts')
</body>
</html>