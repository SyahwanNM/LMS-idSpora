<div class="course-admin-navbar shadow-sm">
    <div class="container">
        <div class="nav-inner">
            <a href="{{ route('admin.dashboard') }}" class="brand">
                <img src="{{ asset('aset/logo.png') }}" alt="idSpora" class="brand-logo">
                
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
    background: linear-gradient( #4B2DBF 100%);
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
    justify-content: flex-start; /* keep brand + nav links on the left */
}
.course-admin-navbar .brand {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}
.course-admin-navbar .brand-logo { height: 32px; width: auto; display:block; }
.course-admin-navbar .brand-text { color:#fff; font-weight:600; font-size: 14px; }
.course-admin-navbar .nav-links { display:flex; align-items:center; gap:24px; margin-left:16px; }
.course-admin-navbar .nav-link { color: rgba(255,255,255,.9); text-decoration: none; position: relative; font-size: 14px; }
.course-admin-navbar .nav-link:hover { color:#fff; }
.course-admin-navbar .nav-link.active { color:#fff; }
.course-admin-navbar .nav-link.active::after { 
    content:""; position:absolute; left:.25rem; right:.25rem; bottom:-.35rem; height:2px; background:#fff; 
    border-radius:2px; opacity:.9;
}
</style>