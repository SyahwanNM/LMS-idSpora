<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin - @yield('title', 'Event')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
    <style>
        /* Ensure toast notifications appear above fixed navbar and profile dropdown */
        .toast-container.position-fixed { z-index: 11050 !important; }
    </style>
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
                    @unless(request()->routeIs('admin.dashboard') || request()->routeIs('admin.users.*') || request()->routeIs('admin.carousels.*'))
                    <li class="nav-item">
                        <a class="nav-link {{ (request()->routeIs('admin.add-event') || request()->routeIs('admin.events.*')) ? 'active' : '' }}" href="{{ route('admin.add-event') }}">Manage Event</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.add-users') ? 'active' : '' }}" href="{{ route('admin.add-users') }}">Manage User</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">Report</a>
                    </li>
                    @endunless
                    {{-- Certificate management moved to CRM --}}
                    @if(request()->routeIs('admin.dashboard'))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Manage Accounts Admin</a>
                    </li>
                    @endif
                </ul>
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center dropdown-toggle" href="#" id="adminProfileDropdown" role="button" data-bs-toggle="dropdown" data-bs-offset="0,8" data-bs-auto-close="outside" aria-expanded="false">
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
                                <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="button" id="logoutTrigger" class="dropdown-item small text-danger">
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
        <!-- Logout Confirmation Modal -->
        <div class="modal fade logout-modal" id="confirmLogoutModal" tabindex="-1" aria-labelledby="confirmLogoutLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <div class="d-flex align-items-center gap-3">
                            <div class="logout-icon" aria-hidden="true">
                                <i class="bi bi-box-arrow-right"></i>
                            </div>
                            <div>
                                <h5 class="modal-title mb-0" id="confirmLogoutLabel">Konfirmasi Logout</h5>
                                <small class="text-muted">Pastikan ini memang Anda</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <p class="text-secondary mb-3">Apakah Anda yakin ingin keluar dari akun admin?</p>

                        <div class="logout-check d-flex align-items-start gap-3 p-3 rounded-3">
                            
                            <div class="flex-grow-1">
                                <div class="form-check m-0">
                                    <input class="form-check-input" type="checkbox" value="1" id="logoutConfirmCheck" aria-describedby="logoutConfirmHelp">
                                    <label class="form-check-label fw-semibold" for="logoutConfirmCheck">Saya yakin ingin logout</label>
                                </div>
                                <small id="logoutConfirmHelp" class="text-muted d-block mt-1">Anda akan keluar dari sesi admin dan perlu login kembali.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <div class="w-100 d-grid gap-2 d-sm-flex justify-content-end">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-danger px-4" id="logoutConfirmBtn" disabled>
                                <span class="me-1">Logout</span>
                                <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- Quick Actions Scrollable Bar (override with @section('admin_quick_actions') if needed) --}}
    {{-- Quick actions bar removed per request: New Course/Event/User, All Courses/Events/Users, Reports --}}
    <div class="container">
        @yield('content')
    </div>
    
    
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        // Ensure dropdown initialization even if other scripts errored earlier
        var trigger = document.getElementById('adminProfileDropdown');
        if (trigger && window.bootstrap && bootstrap.Dropdown) {
            try { new bootstrap.Dropdown(trigger, { autoClose: 'outside' }); } catch(e){}
            // Defensive: manually toggle on click if data-api missed
            trigger.addEventListener('click', function(ev){ try {
                const dd = bootstrap.Dropdown.getOrCreateInstance(trigger, { autoClose: 'outside' });
                dd.toggle();
                // Fallback: force show if still hidden
                const menu = document.querySelector('ul.profile-dropdown.dropdown-menu');
                if(menu && !menu.classList.contains('show')){
                    menu.classList.add('show');
                    menu.style.display = 'block';
                    trigger.setAttribute('aria-expanded', 'true');
                }
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
        // Auto-show any server-rendered Bootstrap toasts (flash messages)
        try {
            document.querySelectorAll('.toast').forEach(function(t){
                try {
                    if(window.bootstrap && bootstrap.Toast){
                        var inst = bootstrap.Toast.getOrCreateInstance(t);
                        inst.show();
                    }
                } catch(e) {}
            });
        } catch(e) {}
        
        // Logout confirmation (modern modal)
        const logoutTrigger = document.getElementById('logoutTrigger');
        const logoutForm = document.getElementById('logoutForm');
        const modalEl = document.getElementById('confirmLogoutModal');
        if (logoutTrigger && logoutForm && modalEl && window.bootstrap && bootstrap.Modal) {
            const confirmModal = new bootstrap.Modal(modalEl);

            const initialBodyHtml = modalEl.querySelector('.modal-body')?.innerHTML || '';
            const initialBodyClass = modalEl.querySelector('.modal-body')?.className || '';
            const initialFooterHtml = modalEl.querySelector('.modal-footer')?.innerHTML || '';
            const initialFooterDisplay = modalEl.querySelector('.modal-footer')?.style.display || '';

            function setConfirmState(checked){
                const confirmBtn = modalEl.querySelector('#logoutConfirmBtn');
                const box = modalEl.querySelector('.logout-check .check-anim');
                if(confirmBtn) confirmBtn.disabled = !checked;
                if(box) box.classList.toggle('active', !!checked);
            }

            function resetLogoutModal(){
                const body = modalEl.querySelector('.modal-body');
                const footer = modalEl.querySelector('.modal-footer');
                if(body){
                    body.className = initialBodyClass;
                    body.innerHTML = initialBodyHtml;
                }
                if(footer){
                    footer.style.display = initialFooterDisplay;
                    footer.innerHTML = initialFooterHtml;
                }
                const confirmCheck = modalEl.querySelector('#logoutConfirmCheck');
                if(confirmCheck) confirmCheck.checked = false;
                setConfirmState(false);
            }

            logoutTrigger.addEventListener('click', function(ev){
                ev.preventDefault();
                resetLogoutModal();
                confirmModal.show();
                // focus checkbox for faster keyboard flow
                setTimeout(function(){
                    try { modalEl.querySelector('#logoutConfirmCheck')?.focus(); } catch(e) {}
                }, 150);
            });

            modalEl.addEventListener('change', function(ev){
                const target = ev.target;
                if(target && target.id === 'logoutConfirmCheck'){
                    setConfirmState(!!target.checked);
                }
            });

            function showLogoutSuccessState(){
                const body = modalEl.querySelector('.modal-body');
                const footer = modalEl.querySelector('.modal-footer');
                if(footer) footer.style.display = 'none';
                if(body){
                    body.classList.add('d-flex','flex-column','align-items-center','justify-content-center');
                    body.innerHTML = `
                        <div class="logout-success-feedback text-center">
                            <svg class="check-anim" viewBox="0 0 72 72" width="88" height="88" aria-hidden="true">
                                <circle class="circle" cx="36" cy="36" r="32" fill="none" stroke="#16a34a" stroke-width="4" />
                                <path class="check" fill="none" stroke="#16a34a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M22 36.5 32 46 50 27" />
                            </svg>
                            <p class="fw-semibold mb-1 mt-3">Berhasil logout</p>
                            <small class="text-muted">Mengalihkan...</small>
                        </div>`;
                }
            }

            modalEl.addEventListener('click', function(ev){
                const target = ev.target;
                const btn = target && (target.id === 'logoutConfirmBtn' ? target : target.closest && target.closest('#logoutConfirmBtn'));
                if(!btn) return;
                ev.preventDefault();
                const confirmCheck = modalEl.querySelector('#logoutConfirmCheck');
                if(!confirmCheck || !confirmCheck.checked) return;
                btn.disabled = true;
                try { showLogoutSuccessState(); } catch(e){}
                setTimeout(function(){ logoutForm.submit(); }, 900);
            });

            modalEl.addEventListener('hidden.bs.modal', function(){
                // keep modal consistent if user closes it mid-way
                try { resetLogoutModal(); } catch(e){}
            });
        }
        
        // Inactivity auto-logout (idle timeout)
        try {
            const logoutFormEl = document.getElementById('logoutForm');
            // Adjust minutes as needed; defaults to 30 minutes
            const IDLE_MINUTES = 30;
            const EVENTS = ['click','mousemove','keydown','scroll','touchstart','touchmove'];
            let idleTimer;
            const resetIdle = function(){
                if(idleTimer) clearTimeout(idleTimer);
                idleTimer = setTimeout(function(){
                    // Prefer graceful modal if present; otherwise submit directly
                    if (logoutFormEl) {
                        try {
                            // If confirmation modal exists, bypass UI and submit
                            logoutFormEl.submit();
                        } catch(e){ /* noop */ }
                    }
                }, IDLE_MINUTES * 60 * 1000);
            };
            EVENTS.forEach(function(evt){
                window.addEventListener(evt, resetIdle, { passive: true });
            });
            resetIdle();
        } catch(e){ /* noop */ }
        });
    </script>
    <style>
    .bg-purple-gradient {background:linear-gradient(#4B2DBF 100%);}    
    /* Ensure navbar always sits above any page overlays */
    .navbar { z-index: 10000; pointer-events:auto; overflow: visible !important; }
    .navbar .container { overflow: visible !important; }
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
        transition:opacity .16s ease;
        /* Keep above navbar */
        z-index:10001;
        position: absolute;
        top: 100%;
        right: 0;
        left: auto;
        background: rgba(255,255,255,0.96);
        backdrop-filter: saturate(180%) blur(6px);
        -webkit-backdrop-filter: saturate(180%) blur(6px);
        border: 1px solid rgba(0,0,0,.06);
        box-shadow: 0 10px 24px rgba(0,0,0,.12), 0 4px 10px rgba(0,0,0,.06);
        border-radius: 12px;
        overflow: hidden;
    }
    .profile-dropdown.show {opacity:1;} 
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
    /* Prevent toast container from blocking navbar clicks while keeping toasts interactive */
    .toast-container { pointer-events: none; z-index: 11050 !important; }
    .toast-container .toast, .toast-container .btn-close { pointer-events: auto; }

    /* New global notification component */
    .global-notification { position: fixed; top: 14px; right: 14px; display:flex; flex-direction:column; gap:10px; align-items:flex-end; z-index:12050; pointer-events:none; }
    .notification { min-width: 300px; max-width:420px; pointer-events:auto; display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:12px; box-shadow: 0 8px 30px rgba(2,6,23,0.12); color:#fff; transform: translateY(-6px) scale(.99); opacity:0; transition: transform .22s cubic-bezier(.2,.9,.2,1), opacity .22s ease; }
    .notification.show { transform: translateY(0) scale(1); opacity:1; }
    .notification.success { background: linear-gradient(90deg,#16a34a,#34d399); }
    .notification.error { background: linear-gradient(90deg,#dc2626,#f43f5e); }
    .notification .notif-message{ flex:1; font-weight:600; font-size:0.95rem; }
    .notification .notif-close { background:transparent; border:0; color:rgba(255,255,255,.95); }
    /* Body padding to prevent content from hiding under fixed navbar */
    /* Extra top spacing so main content sits a bit lower under fixed navbar */
    body { padding-top: 78px; }
    @media (max-width: 991.98px){ body { padding-top: 66px; } }
    /* Logout modal (modern look) */
    .logout-modal .modal-content{ border-radius: 18px; overflow:hidden; }
    .logout-modal .modal-header{ padding: 1.1rem 1.1rem .75rem; }
    .logout-modal .modal-body{ padding: 0 1.1rem 1rem; }
    .logout-modal .modal-footer{ padding: .25rem 1.1rem 1.1rem; }
    .logout-modal .logout-icon{
        width: 42px; height: 42px; border-radius: 14px;
        display:flex; align-items:center; justify-content:center;
        color: #dc2626;
        background: rgba(220,38,38,.10);
        border: 1px solid rgba(220,38,38,.18);
        font-size: 20px;
        flex: 0 0 auto;
    }
    .logout-modal .logout-check{ background: #f8fafc; border: 1px solid rgba(15,23,42,.08); }
    .logout-modal .form-check-input{ cursor:pointer; }
    .logout-modal .form-check-label{ cursor:pointer; color:#0f172a; }
    .logout-modal .btn{ border-radius: 12px; padding-top: .6rem; padding-bottom: .6rem; }

    /* Animated checklist for logout confirmation */
    .logout-check .check-anim{ width:26px; height:26px; border:2px solid #198754; border-radius:8px; display:inline-flex; align-items:center; justify-content:center; transition: background-color .25s ease, border-color .25s ease; margin-top: 2px; }
    .logout-check .check-anim svg{ display:block; }
    .logout-check .check-anim .path{ stroke:#198754; stroke-width:3; fill:none; stroke-linecap:round; stroke-linejoin:round; stroke-dasharray:22; stroke-dashoffset:22; transition: stroke-dashoffset .3s ease .05s; }
    .logout-check .check-anim.active{ background-color:#d1e7dd; border-color:#198754; }
    .logout-check .check-anim.active .path{ stroke-dashoffset:0; }
    /* Logout success animation (modal) */
    .logout-success-feedback .check-anim { display:block; }
    .logout-success-feedback .circle { stroke-dasharray: 201; stroke-dashoffset:201; animation: draw-circle .55s ease-out forwards; }
    .logout-success-feedback .check { stroke-dasharray: 40; stroke-dashoffset:40; animation: draw-check .35s ease-out .45s forwards; }
    @keyframes draw-circle { to { stroke-dashoffset:0; } }
    @keyframes draw-check { to { stroke-dashoffset:0; } }
    </style>

    @if(session('success') || session('login_success') || session('error'))
        <div id="globalNotifications" class="global-notification" aria-live="polite" aria-atomic="true">
            @if(session('login_success'))
                <div class="notification success" role="status" data-timeout="4200">
                    <div class="notif-message"><i class="bi bi-check-circle-fill me-2"></i>{{ session('login_success') }}</div>
                    <button class="notif-close" aria-label="Close">&times;</button>
                </div>
            @elseif(session('success'))
                <div class="notification success" role="status" data-timeout="3800">
                    <div class="notif-message"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
                    <button class="notif-close" aria-label="Close">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="notification error" role="status" data-timeout="6000">
                    <div class="notif-message"><i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}</div>
                    <button class="notif-close" aria-label="Close">&times;</button>
                </div>
            @endif
        </div>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        try {
            const wrap = document.getElementById('globalNotifications');
            if(!wrap) return;
            wrap.querySelectorAll('.notification').forEach(function(n){
                // show animation
                setTimeout(function(){ n.classList.add('show'); }, 20);
                const timeout = parseInt(n.getAttribute('data-timeout') || 4000, 10);
                const closeBtn = n.querySelector('.notif-close');
                const hide = function(){ n.classList.remove('show'); setTimeout(()=> n.remove(), 260); };
                if(closeBtn) closeBtn.addEventListener('click', hide);
                setTimeout(hide, timeout);
            });
        } catch(e){}
    });
    </script>

    @yield('scripts')
</body>
</html>