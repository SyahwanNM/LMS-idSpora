<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #F8FAFC;
    }

    /* --- SIDEBAR DESKTOP --- */
    .sidebar-desktop {
        width: 260px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        overflow-y: auto;
        border-right: 1px solid #e9ecef;
        background: #ffffff;
        z-index: 1040;
        display: flex;
        flex-direction: column;
    }

    .main-content {
        margin-left: 260px;
        transition: margin-left 0.3s ease;
    }

    .sidebar-link {
        color: #64748B;
        transition: all 0.2s ease;
        text-decoration: none;
        padding: 10px 16px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        margin-bottom: 5px;
    }

    .sidebar-link:hover {
        background-color: #FFFBEB;
        color: #B45309;
    }

    .sidebar-link.active {
        background-color: #FEF3C7;
        color: #B45309;
        font-weight: 700;
    }

    .sidebar-link-back {
        background-color: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }

    /* --- MOBILE NAVBAR (DROP DOWN STYLE) --- */
    @media (max-width: 991.98px) {
        .sidebar-desktop { display: none; }
        .main-content { margin-left: 0; padding-top: 75px; }
        
        .navbar-mobile {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e9ecef;
        }

        .navbar-collapse {
            background: #ffffff;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
            border-radius: 0 0 15px 15px;
        }

        .navbar-nav .sidebar-link {
            padding: 12px 16px;
            margin-bottom: 8px;
        }
    }

    /* --- COMPONENT STYLES --- */
    .avatar-circle { width:36px; height:36px; border-radius:50%; border:2px solid #EBBC01; background:#6b7280; display:inline-flex; align-items:center; justify-content:center; overflow:hidden;}
    .avatar-circle img { width:100%; height:100%; object-fit:cover; }
    
    .global-notification { position: fixed; top: 14px; right: 14px; display:flex; flex-direction:column; gap:10px; z-index:11050; pointer-events:none; }
    .notification { pointer-events:auto; display:flex; align-items:center; gap:12px; padding:12px 16px; border-radius:12px; color:#fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transform: translateY(-10px); opacity:0; transition: all 0.3s ease; }
    .notification.show { transform: translateY(0); opacity:1; }
    .notification.success { background: linear-gradient(90deg,#16a34a,#34d399); }
    .notification.error { background: linear-gradient(90deg,#dc2626,#f43f5e); }
</style>

@php $user = auth()->user(); @endphp

<nav class="navbar navbar-expand-lg navbar-mobile fixed-top d-lg-none">
    <div class="container-fluid px-3 py-1">
        <div class="d-flex align-items-center gap-1">
            <button class="navbar-toggler text-dark border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <i class="bi bi-list fs-2"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center text-dark" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/logo-idspora-light.png') }}" alt="IdSpora" style="height:28px; margin-right:5px;">
                <span class="fw-bold small">Admin Reseller</span>
            </a>
        </div>
        
        <div class="dropdown ms-auto">
            <a href="#" class="d-block text-decoration-none" data-bs-toggle="dropdown">
                <div class="avatar-circle shadow-sm">
                    <img src="{{ $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=Admin&background=ffc107' }}" alt="admin">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2 position-absolute">
                <li><h6 class="dropdown-header">Halo, {{ Str::limit($user?->name ?? 'Admin', 10) }}</h6></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <button class="dropdown-item text-danger d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </li>
            </ul>
        </div>

        <div class="collapse navbar-collapse w-100 mt-2" id="navbarMenu">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-back">
                        <i class="bi bi-arrow-left-circle-fill"></i> Admin Utama
                    </a>
                </li>
                <li class="nav-item">
                    <small class="text-uppercase text-secondary fw-bold px-2 my-2 d-block" style="font-size: 0.7rem;">Menu Reseller</small>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reseller.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i> Ringkasan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reseller.data') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.data') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Data Reseller
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="sidebar-desktop d-none d-lg-flex">
    <div class="p-4 d-flex align-items-center gap-3 mb-2">
        <a class="navbar-brand d-flex align-items-center text-dark" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/logo-idspora-light.png') }}" alt="IdSpora" style="height:35px; margin-right:5px;">
                <h5 class="fw-bold">Admin Reseller</h5>
        </a>
    </div>

    <div class="px-3 flex-grow-1">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-back mb-4">
            <i class="bi bi-arrow-left-circle-fill"></i> Admin Utama
        </a>

        <small class="text-uppercase text-secondary fw-bold px-2 mb-2 d-block" style="font-size: 0.75rem;">Menu Reseller</small>
        
        <a href="{{ route('admin.reseller.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-fill"></i> Ringkasan
        </a>
        
        <a href="{{ route('admin.reseller.data') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.data') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Data Reseller
        </a>
    </div>

    <div class="p-3 border-top mt-auto">
        <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-circle" style="width:32px; height:32px;">
                    <img src="{{ $user?->avatar_url ?? 'https://ui-avatars.com/api/?name=Admin&background=ffc107' }}" alt="admin">
                </div>
                <div class="lh-sm">
                    <small class="d-block fw-bold text-dark">{{ Str::limit($user?->name ?? 'Admin', 12) }}</small>
                    <small class="text-muted" style="font-size:10px;">Administrator</small>
                </div>
            </div>
            <button class="btn btn-sm btn-light text-danger p-1 border-0" data-bs-toggle="modal" data-bs-target="#confirmLogoutModal">
                <i class="bi bi-box-arrow-right fs-5"></i>
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body text-center p-4">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex p-3 mb-3">
                    <i class="bi bi-box-arrow-right fs-1"></i>
                </div>
                <h5 class="fw-bold mb-2">Keluar dari Akun?</h5>
                <p class="text-muted small mb-4">Anda harus login kembali untuk mengakses panel admin.</p>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger rounded-pill fw-bold">Ya, Logout</button>
                        <button type="button" class="btn btn-light rounded-pill fw-bold" data-bs-dismiss="modal">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success') || session('error'))
<div id="globalNotifications" class="global-notification">
    @if(session('success'))
        <div class="notification success" data-timeout="3000">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span class="notif-message">{{ session('success') }}</span>
            <button type="button" class="notif-close border-0 bg-transparent text-white" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="notification error" data-timeout="4000">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span class="notif-message">{{ session('error') }}</span>
            <button type="button" class="notif-close border-0 bg-transparent text-white" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
        </div>
    @endif
</div>
@endif