<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - @yield('title', 'Event')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>