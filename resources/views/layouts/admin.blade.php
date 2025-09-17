<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - @yield('title', 'Event')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <!-- Modern Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 mb-4">
        <div class="container py-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <a href="{{ route('dashboard') }}" class="me-3 text-decoration-none">
                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <span class="fs-4 fw-bold text-dark">@yield('title', 'Admin')</span>
                    <div class="text-muted small">LMS Admin Panel</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a class="btn btn-outline-primary" href="{{ route('dashboard') }}">Dashboard</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">@csrf
                    <button class="btn btn-outline-danger" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </header>
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
    @yield('scripts')
</body>
</html>