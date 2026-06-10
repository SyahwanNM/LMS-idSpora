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
    @stack('styles')
    <style>
        /* Ensure toast notifications appear above fixed navbar and profile dropdown */
        .toast-container.position-fixed { z-index: 11050 !important; }
    </style>
</head>
<body>
    @php 
        $user = auth()->user();
        $isSpecialPage = request()->routeIs('admin.finance.*') || 
                         request()->routeIs('admin.withdrawals.*') || 
                         request()->routeIs('admin.crm.*') || 
                         request()->routeIs('admin.trainer.*') ||
                         request()->routeIs('admin.event-material*') ||
                         request()->routeIs('admin.courses.studio*') ||
                         request()->routeIs('admin.material.*');
    @endphp

    @unless($isSpecialPage)
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom fixed-top" style="height: 64px;">
        <div class="container-fluid px-4">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('aset/logo.png') }}" alt="logo" class="me-2" style="height:26px;">
                <span class="fw-bold text-dark fs-5" style="letter-spacing: -0.5px;">Admin</span>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @if(auth()->user()?->role === 'admin' && !request()->routeIs('admin.dashboard'))
                        <li class="nav-item ms-lg-3">
                            <a class="nav-link fw-medium text-primary d-flex align-items-center" href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-grid-1x2-fill me-2"></i> Module Hub
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()?->role === 'admin' && !request()->routeIs('admin.dashboard'))
                        <li class="nav-item ms-lg-3">
                            <a class="nav-link fw-medium text-primary d-flex align-items-center {{ (request()->routeIs('admin.events.*') || request()->routeIs('admin.add-event')) ? 'active' : '' }}" href="{{ route('admin.events.index') }}">
                                <i class="bi bi-calendar-event me-2"></i> Manage Event
                            </a>
                        </li>
                        <li class="nav-item ms-lg-3">
                            <a class="nav-link fw-medium text-primary d-flex align-items-center {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                                <i class="bi bi-graph-up me-2"></i> Reports
                            </a>
                        </li>
                    @endif
                    @if(auth()->user()?->role === 'admin')
                        <li class="nav-item ms-lg-2">
                            <a class="btn btn-sm btn-warning fw-semibold d-flex align-items-center gap-1 {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                               href="{{ route('admin.users.index') }}"
                               style="border-radius:8px;padding:5px 12px;color:#1a1a2e;">
                                <i class="bi bi-people-fill"></i>
                                <span class="d-none d-sm-inline">Kelola Akun</span>
                            </a>
                        </li>
                    @elseif(auth()->user()?->role === 'event_admin')
                        @php
                            $assignedId = \Illuminate\Support\Facades\DB::table('event_admin_assignments')
                                ->where('user_id', auth()->id())
                                ->value('event_id');
                            $assignedEvent = $assignedId ? \App\Models\Event::find($assignedId) : null;
                        @endphp
                        @if($assignedEvent)
                        <li class="nav-item ms-lg-3">
                            <a class="nav-link fw-medium text-primary d-flex align-items-center"
                               href="{{ route('admin.events.show', $assignedEvent) }}">
                                <i class="bi bi-calendar-event me-2"></i>
                                {{ \Illuminate\Support\Str::limit($assignedEvent->title, 30) }}
                            </a>
                        </li>
                        @endif
                    @endif
                </ul>
                <ul class="navbar-nav ms-auto align-items-center gap-3">
                    <li class="nav-item dropdown">
                        <a class="nav-link d-flex align-items-center dropdown-toggle" href="#" id="adminProfileDropdown" role="button" data-bs-toggle="dropdown" data-bs-offset="0,8" data-bs-auto-close="outside" aria-expanded="false">
                            <span class="avatar-circle me-2">
                                <img src="{{ $user?->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                            </span>
                            <span class="d-none d-lg-inline user-name small fw-semibold text-dark">{{ $user?->name ?? 'Admin' }}</span>
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
                                    <button type="button" id="logoutTrigger"
                                        class="dropdown-item small text-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#confirmLogoutModal">
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
    @endunless
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
                                <h5 class="modal-title mb-0" id="confirmLogoutLabel">Confirm Logout</h5>
                                <small class="text-muted">Make sure this is really you</small>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body pt-0">
                        <p class="text-secondary mb-3">Are you sure you want to log out of the admin account?</p>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <div class="w-100 d-grid gap-2 d-sm-flex justify-content-end">
                            <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger px-4" id="logoutConfirmBtn"
                                onclick="document.getElementById('logoutForm').submit();">
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
    @yield('navbar')

    <div class="@if($isSpecialPage) container-fluid p-0 @else container-fluid px-4 @endif">
        @yield('content')
    </div>

    <style>
        body { padding-top: 64px !important; }
        @if($isSpecialPage)
            /* Special pages might handle their own padding if needed, but for now we enforce the top offset for the fixed navbar */
            .container-fluid.p-0 { padding-top: 0 !important; } 
        @endif
    </style>
    
    
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const trigger = document.getElementById('adminProfileDropdown');
        const logoutTrigger = document.getElementById('logoutTrigger');
        const logoutForm = document.getElementById('logoutForm');
        const modalEl = document.getElementById('confirmLogoutModal');

        const getBootstrap = () => window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);

        let _adminInitDone = false;
        const initAdminNavbar = () => {
            if (_adminInitDone) return;
            const bs = getBootstrap();
            if (!bs || !bs.Dropdown || !bs.Modal) return;
            _adminInitDone = true;

            // 1. Profile Dropdown Logic
            if (trigger) {
                const dd = new bs.Dropdown(trigger, { autoClose: 'outside' });
                trigger.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    dd.toggle();
                });

                // Close button in dropdown
                document.querySelectorAll('.dropdown-close').forEach(function(btn){
                    btn.addEventListener('click', function(ev){
                        ev.preventDefault(); ev.stopPropagation();
                        dd.hide();
                    });
                });
            }

            // 2. Logout Modal Logic
            if (logoutTrigger && logoutForm && modalEl) {
                const confirmModal = new bs.Modal(modalEl);

                const initialBodyHtml = modalEl.querySelector('.modal-body')?.innerHTML || '';
                const initialBodyClass = modalEl.querySelector('.modal-body')?.className || '';
                const initialFooterHtml = modalEl.querySelector('.modal-footer')?.innerHTML || '';
                const initialFooterDisplay = modalEl.querySelector('.modal-footer')?.style.display || '';

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
                    const confirmBtn = modalEl.querySelector('#logoutConfirmBtn');
                    if(confirmBtn) confirmBtn.disabled = false;
                }

                logoutTrigger.addEventListener('click', function(ev){
                    ev.preventDefault();
                    resetLogoutModal();
                    confirmModal.show();
                    setTimeout(function(){
                        try { modalEl.querySelector('#logoutConfirmBtn')?.focus(); } catch(e) {}
                    }, 150);
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
                                <p class="fw-semibold mb-1 mt-3">Logout successful</p>
                                <small class="text-muted">Redirecting...</small>
                            </div>`;
                    }
                }

                modalEl.addEventListener('click', function(ev){
                    const target = ev.target;
                    const btn = target && (target.id === 'logoutConfirmBtn' ? target : target.closest && target.closest('#logoutConfirmBtn'));
                    if(!btn) return;
                    ev.preventDefault();
                    btn.disabled = true;
                    try { showLogoutSuccessState(); } catch(e){}
                    setTimeout(function(){ logoutForm.submit(); }, 900);
                });

                modalEl.addEventListener('hidden.bs.modal', function(){
                    try { resetLogoutModal(); } catch(e){}
                });
            }
        };

        // Polling for bootstrap
        let _checkCount = 0;
        const _interval = setInterval(() => {
            if (getBootstrap() && getBootstrap().Dropdown) {
                clearInterval(_interval);
                initAdminNavbar();
            }
            if (++_checkCount > 100) clearInterval(_interval);
        }, 100);

        window.addEventListener('load', initAdminNavbar);


        
        // Inactivity auto-logout (idle timeout)
        try {
            const IDLE_MINUTES = {{ (int) config('session.lifetime', 120) }};
            const WARN_BEFORE_S = 60;
            const EVENTS = ['click','mousemove','keydown','scroll','touchstart','touchmove'];
            let idleTimer, warnTimer, countdownInterval;
            let idleWarned = false;

            // Create warning modal dynamically
            const idleModal = document.createElement('div');
            idleModal.id = 'adminIdleModal';
            idleModal.style.cssText = 'display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:99999;align-items:center;justify-content:center;';
            idleModal.innerHTML = `
                <div style="background:#fff;border-radius:16px;padding:32px 28px;max-width:380px;width:90%;text-align:center;box-shadow:0 8px 32px rgba(0,0,0,.18);">
                    <div style="font-size:2.5rem;margin-bottom:12px;">⏱️</div>
                    <h5 style="font-weight:700;margin-bottom:8px;">Session Expiring Soon</h5>
                    <p style="color:#6b7280;font-size:14px;margin-bottom:20px;">You have been inactive. You will be logged out in <strong id="adminIdleCountdown">60</strong> seconds.</p>
                    <div style="display:flex;gap:12px;justify-content:center;">
                        <button id="adminIdleStayBtn" style="flex:1;padding:10px;background:#4f46e5;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Stay Logged In</button>
                        <button onclick="document.getElementById('logoutForm')?.submit()" style="flex:1;padding:10px;background:#ef4444;color:#fff;border:none;border-radius:8px;font-weight:600;cursor:pointer;">Logout Now</button>
                    </div>
                </div>`;
            document.body.appendChild(idleModal);

            const cdEl = document.getElementById('adminIdleCountdown');
            const stayBtn = document.getElementById('adminIdleStayBtn');

            function doAdminLogout(){ document.getElementById('logoutForm')?.submit(); }

            function startAdminCountdown(){
                let secs = WARN_BEFORE_S;
                cdEl && (cdEl.textContent = secs);
                countdownInterval = setInterval(function(){
                    secs--;
                    cdEl && (cdEl.textContent = secs);
                    if (secs <= 0){ clearInterval(countdownInterval); doAdminLogout(); }
                }, 1000);
            }

            function showAdminWarning(){
                idleWarned = true;
                idleModal.style.display = 'flex';
                startAdminCountdown();
            }

            function hideAdminWarning(){
                idleModal.style.display = 'none';
                clearInterval(countdownInterval);
                idleWarned = false;
            }

            stayBtn && stayBtn.addEventListener('click', function(){ resetAdminIdle(); });

            function resetAdminIdle(){
                clearTimeout(idleTimer);
                clearTimeout(warnTimer);
                hideAdminWarning();
                const totalMs = IDLE_MINUTES * 60 * 1000;
                const warnMs  = totalMs - (WARN_BEFORE_S * 1000);
                warnTimer = setTimeout(showAdminWarning, warnMs > 0 ? warnMs : totalMs);
                idleTimer = setTimeout(doAdminLogout, totalMs);
            }

            EVENTS.forEach(function(evt){
                window.addEventListener(evt, resetAdminIdle, { passive: true });
            });
            resetAdminIdle();
        } catch(e){ /* noop */ }
    });
    </script>
    <style>
    .bg-purple-gradient {background:linear-gradient(#4B2DBF 100%);}    
    /* Ensure navbar always sits above any page overlays */
    .navbar { z-index: 10000; pointer-events:auto; overflow: visible !important; }
    .navbar .container, .navbar .container-fluid { overflow: visible !important; }
    .navbar { padding-top: .35rem; padding-bottom: .35rem; }
    .navbar .nav-link { color: #4b5563 !important; font-size: .95rem; font-weight: 500; transition: color 0.15s ease-in-out; position: relative; }
    .navbar .nav-link:hover { color: #0d6efd !important; }
    .navbar .nav-link.active { color: #0d6efd !important; font-weight: 600; }
    .navbar .nav-link::after { content: ""; position: absolute; left: 0.5rem; right: 0.5rem; bottom: -0.25rem; height: 2px; background-color: #0d6efd; border-radius: 2px; transform: scaleX(0); transition: transform 0.18s ease-in-out; }
    .navbar .nav-link.active::after, .navbar .nav-link:hover::after { transform: scaleX(1); }
    .avatar-circle {width:34px;height:34px;border-radius:50%;overflow:hidden;border:2px solid #EBBC01;background:#6b7280;display:inline-flex;align-items:center;justify-content:center;}
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
    .notification { min-width: 320px; max-width:480px; pointer-events:auto; display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:12px; box-shadow: 0 8px 30px rgba(2,6,23,0.12); color:#fff; transform: translateY(-6px) scale(.99); opacity:0; transition: transform .22s cubic-bezier(.2,.9,.2,1), opacity .22s ease; }
    .notification.show { transform: translateY(0) scale(1); opacity:1; }
    .notification.success { background: linear-gradient(90deg,#16a34a,#34d399); }
    .notification.error { background: linear-gradient(90deg,#dc2626,#f43f5e); }
    .notification.info { background: linear-gradient(90deg,#2563eb,#60a5fa); }
    .notification.warning { background: linear-gradient(90deg,#d97706,#fbbf24); }
    .notification .notif-message{ flex:1; font-weight:600; font-size:0.95rem; line-height:1.4; }
    .notification .notif-close { background:transparent; border:0; color:rgba(255,255,255,.95); flex-shrink:0; padding:0; line-height:1; }
    /* Body padding to prevent content from hiding under fixed navbar */
    /* Extra top spacing so main content sits a bit lower under fixed navbar */
    body { padding-top: 64px; }
    @media (max-width: 991.98px){ body { padding-top: 58px; } }
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

    @if(session('success') || session('login_success') || session('error') || session('info') || session('warning') || $errors->any())
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
            @if(session('info'))
                <div class="notification info" role="status" data-timeout="4500">
                    <div class="notif-message"><i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}</div>
                    <button class="notif-close" aria-label="Close">&times;</button>
                </div>
            @endif
            @if(session('warning'))
                <div class="notification warning" role="status" data-timeout="5000">
                    <div class="notif-message"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('warning') }}</div>
                    <button class="notif-close" aria-label="Close">&times;</button>
                </div>
            @endif
            @if($errors->any())
                <div class="notification error" role="status" data-timeout="7000">
                    <div class="notif-message">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan:</div>
                        <ul class="mb-0 ps-3 small">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
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

    // Programmatic API for the new notification banner (replaces legacy Bootstrap toasts)
    window.adminNotify = function(type, message, timeout){
        try {
            let kind = 'success';
            if (type === 'error') kind = 'error';
            else if (type === 'info') kind = 'info';
            else if (type === 'warning') kind = 'warning';

            const text = (message == null) ? '' : String(message);
            const ms = Number.isFinite(Number(timeout)) ? Math.max(800, Number(timeout)) : 3800;

            let wrap = document.getElementById('globalNotifications');
            if(!wrap){
                wrap = document.createElement('div');
                wrap.id = 'globalNotifications';
                wrap.className = 'global-notification';
                wrap.setAttribute('aria-live', 'polite');
                wrap.setAttribute('aria-atomic', 'true');
                document.body.appendChild(wrap);
            }

            const n = document.createElement('div');
            n.className = 'notification ' + kind;
            n.setAttribute('role', 'status');
            n.setAttribute('data-timeout', String(ms));

            const msg = document.createElement('div');
            msg.className = 'notif-message';
            
            let iconHtml = '<i class="bi bi-check-circle-fill me-2"></i>';
            if (kind === 'error') {
                iconHtml = '<i class="bi bi-x-circle-fill me-2"></i>';
            } else if (kind === 'info') {
                iconHtml = '<i class="bi bi-info-circle-fill me-2"></i>';
            } else if (kind === 'warning') {
                iconHtml = '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
            }
            msg.innerHTML = iconHtml + text;

            const close = document.createElement('button');
            close.className = 'notif-close';
            close.setAttribute('aria-label', 'Close');
            close.type = 'button';
            close.innerHTML = '&times;';

            n.appendChild(msg);
            n.appendChild(close);
            wrap.appendChild(n);

            const hide = function(){ n.classList.remove('show'); setTimeout(()=> n.remove(), 260); };
            close.addEventListener('click', hide);
            setTimeout(function(){ n.classList.add('show'); }, 20);
            setTimeout(hide, ms);
        } catch(e){}
    };
    </script>

    @yield('scripts')
    @stack('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>