<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - @yield('title', 'Event')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body>
    <!-- Admin Navbar (Bootstrap) -->
    @php $user = auth()->user(); @endphp
    <nav class="navbar navbar-expand-lg navbar-dark bg-purple-gradient shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('aset/logo.png') }}" alt="logo" class="me-2" style="height:28px;">
                <span class="fw-semibold">Admin</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link {{ (request()->routeIs('admin.events.*') || request()->routeIs('admin.add-event')) ? 'active' : '' }}" href="{{ route('admin.add-event') }}">Manage Event</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Manage Users</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">Report</a></li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center dropdown-toggle" href="javascript:void(0)" id="adminProfileDropdown" role="button" data-bs-toggle="dropdown" data-bs-offset="0,8" data-bs-display="static" data-bs-auto-close="outside" aria-expanded="false">
                            <span class="avatar-circle me-2">
                                <img src="{{ $user?->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                            </span>
                            <span class="d-none d-lg-inline user-name small fw-semibold">{{ $user?->name ?? 'Admin' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow profile-dropdown" aria-labelledby="adminProfileDropdown">
                            <li class="d-flex justify-content-end align-items-center pt-2 px-2">
                                <button type="button" class="btn-close dropdown-close" aria-label="Close"></button>
                            </li>
                            <li><h6 class="dropdown-header small text-muted">Akun</h6></li>
                            @if($user && $user->role === 'admin')
                                <li><a class="dropdown-item" href="{{ route('admin.settings') }}"><i class="bi bi-sliders me-1"></i> Sistem</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif
                            <li>
                                <form id="adminLogoutForm" action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="button" id="openAdminLogoutModal" class="dropdown-item small text-danger" data-bs-toggle="modal" data-bs-target="#adminLogoutModal">
                                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    {{-- Quick Actions Scrollable Bar (override with @section('admin_quick_actions') if needed) --}}
    {{-- Quick actions bar removed per request: New Course/Event/User, All Courses/Events/Users, Reports --}}
    <div class="container">
        @yield('content')
    </div>
    
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Ensure dropdown initialization even if other scripts errored earlier
        var trigger = document.getElementById('adminProfileDropdown');
        if (trigger && window.bootstrap && bootstrap.Dropdown) {
            try { new bootstrap.Dropdown(trigger, { autoClose: 'outside', display: 'static' }); } catch(e){}
            // Defensive: manually toggle on click if data-api missed
            trigger.addEventListener('click', function(ev){ ev.preventDefault(); try {
                const dd = bootstrap.Dropdown.getOrCreateInstance(trigger, { autoClose: 'outside', display: 'static' });
                dd.toggle();
            } catch(e){} });

            // Close button in dropdown
            document.querySelectorAll('.dropdown-close').forEach(function(btn){
                btn.addEventListener('click', function(ev){
                    ev.preventDefault(); ev.stopPropagation();
                    try {
                        const dd = bootstrap.Dropdown.getOrCreateInstance(trigger, { autoClose: 'outside', display: 'static' });
                        dd.hide();
                    } catch(e){}
                });
            });
        }
    });
        // Admin Logout Modal logic
        document.addEventListener('DOMContentLoaded', function(){
            if(!window.bootstrap) return;
            const openBtn = document.getElementById('openAdminLogoutModal');
            const modalEl = document.getElementById('adminLogoutModal');
            if(!openBtn || !modalEl) return;
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: true, keyboard: true });
            // Ensure profile dropdown hides to avoid z-index overlap with modal
            const ddTrigger = document.getElementById('adminProfileDropdown');
            const ddInstance = ddTrigger ? bootstrap.Dropdown.getOrCreateInstance(ddTrigger, { autoClose: 'outside', display: 'static' }) : null;
            openBtn.addEventListener('click', function(e){
                e.preventDefault();
                e.stopPropagation();
                try { ddInstance?.hide(); } catch(_) {}
                setTimeout(()=> modal.show(), 120);
            });
            const confirmBtn = document.getElementById('adminLogoutConfirmBtn');
            const checkbox = document.getElementById('adminLogoutCheckbox');
            const form = document.getElementById('adminLogoutForm');
            function sync(){ if(confirmBtn) confirmBtn.disabled = !checkbox?.checked; }
            // Reset state each time modal is shown
            modalEl.addEventListener('show.bs.modal', function(){
                if(checkbox){ checkbox.checked = false; }
                sync();
            });
            checkbox?.addEventListener('change', sync);
            sync();
            confirmBtn?.addEventListener('click', function(){
                confirmBtn.classList.add('tapped');
                try { showAdminLogoutSuccessState(); } catch(_e){}
                setTimeout(()=>{ form?.submit(); }, 900);
            });
        });
    </script>
    <style>
    .bg-purple-gradient {background:linear-gradient(90deg,#6f42c1 0%, #a855f7 100%);}    
    .navbar { z-index: 1040; }
    .navbar .nav-link {color: rgba(255,255,255,.9);} 
    .navbar .nav-link:hover {color: #fff;}
    .navbar .nav-link.active {color:#fff;position:relative;}
    .navbar .nav-link.active::after {content:"";position:absolute;left:.5rem;right:.5rem;bottom:-.4rem;height:2px;background:#fff;border-radius:2px;opacity:.9;}
    .avatar-circle {width:40px;height:40px;border-radius:50%;overflow:hidden;border:2px solid #EBBC01;background:#6b7280;display:inline-flex;align-items:center;justify-content:center;}
    .avatar-circle img {width:100%;height:100%;object-fit:cover;display:block;}
    /* Subtle dropdown animation + visible background */
    .profile-dropdown {
        margin-top:.25rem;
        opacity:0;
        transform:translateY(-6px) scale(.98);
        transition:opacity .16s ease, transform .16s ease;
        /* Keep below Bootstrap modal (1060) but above navbar */
        z-index:1045;
        position: relative;
        background: rgba(255,255,255,0.96);
        backdrop-filter: saturate(180%) blur(6px);
        -webkit-backdrop-filter: saturate(180%) blur(6px);
        border: 1px solid rgba(0,0,0,.06);
        box-shadow: 0 10px 24px rgba(0,0,0,.12), 0 4px 10px rgba(0,0,0,.06);
        border-radius: 12px;
        overflow: hidden;
    }
    .profile-dropdown.show {opacity:1;transform:translateY(0) scale(1);} 
    .profile-dropdown::before {display:none !important;} 
    .profile-dropdown .dropdown-close { position:absolute; top:6px; right:8px; opacity:.8; }
    .profile-dropdown .dropdown-close:hover { opacity:1; }
    .profile-dropdown .dropdown-header { color: #6b7280; }
    .profile-dropdown .dropdown-divider { opacity: .15; }
    .profile-dropdown .dropdown-item { color: #111827; }
    .profile-dropdown .dropdown-item:hover, .profile-dropdown .dropdown-item:focus {
        background: #f3f4f6;
        color: #111827;
    }
    /* Ensure admin username in the profile toggle is readable on light pill backgrounds */
    .user-name { color: #0f172a !important; }
    /* Body padding to prevent content from hiding under fixed navbar */
    /* Extra top spacing so main content sits a bit lower under fixed navbar */
    body { padding-top: 78px; }
    @media (max-width: 991.98px){ body { padding-top: 66px; } }
    </style>
    <!-- Admin Logout Confirmation Modal (modern + animated) -->
    <div class="modal fade" id="adminLogoutModal" tabindex="-1" aria-labelledby="adminLogoutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content modal-modern position-relative">
                <span class="gradient-ring" aria-hidden="true"></span>
                <div class="modal-header border-0">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-pill logout-pulse"><i class="bi bi-box-arrow-right fs-4"></i></div>
                        <div>
                            <h5 class="modal-title mb-0" id="adminLogoutLabel">Konfirmasi Logout</h5>
                            <small class="text-muted">Anda akan mengakhiri sesi admin</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <p class="mb-3">Apakah Anda yakin ingin keluar sekarang?</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="adminLogoutCheckbox">
                        <label class="form-check-label text-dark" for="adminLogoutCheckbox">Saya yakin ingin logout.</label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="adminLogoutConfirmBtn" class="btn btn-danger confirm-danger-btn" disabled>
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
    /* Modern modal look (shared) */
    .modal-modern{border:0;border-radius:18px;background:rgba(255,255,255,0.92);backdrop-filter:saturate(180%) blur(10px);-webkit-backdrop-filter:saturate(180%) blur(10px);box-shadow:0 20px 40px rgba(0,0,0,.18),0 8px 18px rgba(0,0,0,.08);overflow:hidden}
    .gradient-ring{position:absolute;inset:-2px;border-radius:20px;padding:2px;background:linear-gradient(135deg,#6366f1,#ef4444,#f59e0b,#10b981);background-size:300% 300%;animation:hue-shift 6s ease infinite;-webkit-mask:linear-gradient(#fff 0 0) content-box,linear-gradient(#fff 0 0);-webkit-mask-composite:xor;mask-composite:exclude;pointer-events:none}
    @keyframes hue-shift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
    .icon-pill{width:56px;height:56px;border-radius:14px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#eef2ff,#faf5ff);color:#6d28d9;box-shadow:inset 0 0 0 1px rgba(109,40,217,.25)}
    .logout-pulse{animation:pulse 1.4s ease-in-out infinite}
    @keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.06)}}
    .confirm-danger-btn{background:#dc2626;border-color:#dc2626}
    .confirm-danger-btn:hover{background:#b91c1c;border-color:#b91c1c}
    #adminLogoutConfirmBtn.tapped{transform:scale(.98)}
    /* Force logout confirmation label text to black */
    #adminLogoutModal .form-check-label { color:#000 !important; }
    </style>
    <style>
    /* Animated check for logout success */
    .check-anim { width:88px; height:88px; display:block; margin:0 auto; }
    .check-anim .circle { fill: none; stroke: #10b981; stroke-width:4; stroke-linecap:round; stroke-linejoin:round; stroke-dasharray: 201; stroke-dashoffset: 201; animation: drawCircle .6s ease forwards; }
    .check-anim .check { fill: none; stroke: #10b981; stroke-width:4; stroke-linecap:round; stroke-linejoin:round; stroke-dasharray: 60; stroke-dashoffset: 60; animation: drawCheck .5s .45s ease forwards; }
    @keyframes drawCircle { to { stroke-dashoffset: 0; } }
    @keyframes drawCheck { to { stroke-dashoffset: 0; } }
    .logout-success-feedback p { margin-top: 10px; font-weight:600; }
    </style>

    <script>
    // Replace modal body with animated success state during logout
    function showAdminLogoutSuccessState(){
        const modalEl = document.getElementById('adminLogoutModal');
        if(!modalEl) return;
        const body = modalEl.querySelector('.modal-body');
        const footer = modalEl.querySelector('.modal-footer');
        if(footer) footer.style.display='none';
        if(body){
            body.classList.add('d-flex','flex-column','align-items-center','justify-content-center');
            body.innerHTML = `
                <div class="logout-success-feedback text-center">
                    <svg class="check-anim" viewBox="0 0 72 72" aria-hidden="true">
                        <circle class="circle" cx="36" cy="36" r="32"></circle>
                        <path class="check" d="M22 36.5 32 46 50 27"></path>
                    </svg>
                    <p class="mb-0">Berhasil logout</p>
                    <small class="text-muted">Mengalihkan...</small>
                </div>`;
        }
    }
    </script>
    @yield('scripts')
</body>
</html>