<<<<<<< HEAD

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="navbar-admin-course">
        <ul>
            <li><img src="{{ asset('aset/logo.png') }}" alt=""></li>
            <a href="">
                <li>Course Builder</li>
            </a>
            <a href="">
                <li>Report</li>
            </a>
        </ul>
    </div>
</body>

</html>
=======
<div class="course-admin-navbar shadow-sm">
    <div class="container">
        <div class="nav-inner">
            <a href="{{ route('admin.courses.index') }}" class="brand">
                <img src="{{ asset('aset/logo.png') }}" alt="idSpora" class="brand-logo">
                <span class="brand-text">Admin • Course</span>
            </a>
            <nav class="nav-links">
                <a href="{{ route('admin.courses.index') }}" class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}">Course Builder</a>
                <a href="{{ route('report') }}" class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}">Report</a>
            </nav>
        </div>
    </div>
    
</div>

<!-- Spacer to prevent content being hidden under fixed navbar -->
<div class="course-admin-navbar-spacer"></div>

<style>
/* Compact admin navbar without Tailwind dependency */
.course-admin-navbar { 
    background: linear-gradient(90deg,#6f42c1 0%, #a855f7 100%);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    z-index: 1030; /* above content */
}
.course-admin-navbar-spacer { height: 56px; }
.course-admin-navbar .container { width: 100%; max-width: none; padding: 0 16px; margin: 0 auto; }
.course-admin-navbar .nav-inner {
    height: 56px; /* matches standard navbar height */
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.course-admin-navbar .brand {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}
.course-admin-navbar .brand-logo { height: 32px; width: auto; display:block; }
.course-admin-navbar .brand-text { color:#fff; font-weight:600; font-size: 14px; }
.course-admin-navbar .nav-links { display:flex; align-items:center; gap:24px; }
.course-admin-navbar .nav-link { color: rgba(255,255,255,.9); text-decoration: none; position: relative; font-size: 14px; }
.course-admin-navbar .nav-link:hover { color:#fff; }
.course-admin-navbar .nav-link.active { color:#fff; }
.course-admin-navbar .nav-link.active::after { 
    content:""; position:absolute; left:.25rem; right:.25rem; bottom:-.35rem; height:2px; background:#fff; 
    border-radius:2px; opacity:.9;
}
</style>
>>>>>>> f587188d4cab519fdc0662389f336cb06bb0dcca
