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
        border-right: 1px solid #e2e8f0;
        background: #ffffff;
        z-index: 1040;
        display: flex;
        flex-direction: column;
    }

    .main-content {
        transition: margin-left 0.3s ease;
    }

    @media (min-width: 992px) {
        .main-content {
            margin-left: 260px;
            padding-top: 70px; /* Offset for fixed top header on desktop */
        }
    }

    .sidebar-link {
        color: #64748B;
        transition: all 0.2s ease;
        text-decoration: none;
        padding: 12px 16px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .sidebar-link:hover {
        background-color: #f3e8ff; /* primary subtle background */
        color: var(--primary-dark);
    }

    .sidebar-link.active {
        background-color: var(--primary-dark);
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(76, 29, 149, 0.15);
    }

    .sidebar-link-back {
        background-color: #f0fdf4;
        color: #15803d;
        border: 1px solid #bbf7d0;
    }
    
    .sidebar-link-back:hover {
        background-color: #dcfce7;
        color: #166534;
    }

    /* --- DESKTOP HEADER --- */
    .admin-header-desktop {
        height: 70px;
        background-color: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        position: fixed;
        top: 0;
        right: 0;
        left: 260px;
        z-index: 1020;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1.5rem;
    }

    /* --- MOBILE NAVBAR --- */
    @media (max-width: 991.98px) {
        .sidebar-desktop { display: none; }
        .main-content { margin-left: 0; padding-top: 75px; }
        
        .navbar-mobile {
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e2e8f0;
            height: 70px;
        }

        .navbar-collapse {
            background: #ffffff;
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }

        .navbar-nav .sidebar-link {
            padding: 12px 16px;
            margin-bottom: 8px;
        }
    }

    /* --- COMMON UI COMPONENTS --- */
    .avatar-circle { 
        width: 38px; 
        height: 38px; 
        border-radius: 50%; 
        border: 2px solid #ffc107; 
        background: #f1f5f9; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        overflow: hidden;
    }
    .avatar-circle img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    }
    
    .global-notification { 
        position: fixed; 
        top: 14px; 
        right: 14px; 
        display: flex; 
        flex-direction: column; 
        gap: 10px; 
        z-index: 11050; 
        pointer-events: none; 
    }
    .notification { 
        pointer-events: auto; 
        display: flex; 
        align-items: center; 
        gap: 12px; 
        padding: 12px 16px; 
        border-radius: 12px; 
        color: #fff; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        transform: translateY(-10px); 
        opacity: 0; 
        transition: all 0.3s ease; 
    }
    .notification.show { 
        transform: translateY(0); 
        opacity: 1; 
    }
    .notification.success { 
        background: linear-gradient(90deg, #10b981, #34d399); 
    }
    .notification.error { 
        background: linear-gradient(90deg, #ef4444, #f43f5e); 
    }
</style>

@php $user = auth()->user(); @endphp

<!-- DESKTOP FIXED HEADER -->
<header class="admin-header-desktop d-none d-lg-flex">
    <!-- Global Search Bar -->
    <div class="search-bar flex-grow-1" style="max-width: 400px;">
        <form action="{{ route('admin.reseller.data') }}" method="GET" class="position-relative">
            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
            <input type="text" name="search" class="form-control ps-5 rounded-pill border-0 bg-light" placeholder="Cari reseller, referral, atau event..." value="{{ request('search') }}" style="height: 40px; font-size: 0.9rem;">
        </form>
    </div>

    <!-- Right Actions -->
    <div class="d-flex align-items-center gap-3">
        <!-- Notification Bell Dropdown -->
        <div class="dropdown">
            <button class="btn btn-light rounded-circle position-relative p-0 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" style="width: 40px; height: 40px;">
                <i class="bi bi-bell fs-5 text-secondary"></i>
                <span class="position-absolute translate-middle badge rounded-circle bg-danger border border-white" style="padding: 5px; left: 75% !important; top: 25% !important;">
                    <span class="visually-hidden">unread notifications</span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-3" style="width: 320px;">
                <h6 class="fw-bold mb-3 d-flex justify-content-between align-items-center">
                    <span>Notifikasi</span>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">3 Baru</span>
                </h6>
                <div class="list-group list-group-flush small" style="max-height: 250px; overflow-y: auto;">
                    <a href="{{ route('admin.reseller.data') }}" class="list-group-item list-group-item-action border-0 p-2 rounded-3 mb-1 bg-light">
                        <div class="fw-bold text-dark">Reseller baru bergabung!</div>
                        <div class="text-muted text-truncate">Cinta (BCA) mendaftar sebagai reseller.</div>
                        <small class="text-secondary opacity-75">5 menit yang lalu</small>
                    </a>
                    <a href="{{ route('admin.reseller.katalog') }}" class="list-group-item list-group-item-action border-0 p-2 rounded-3 mb-1">
                        <div class="fw-bold text-dark">Produk reseller baru</div>
                        <div class="text-muted text-truncate">Course "Copywriting Pro" ditambahkan.</div>
                        <small class="text-secondary opacity-75">1 jam yang lalu</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- MOBILE NAVIGATION NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-mobile fixed-top d-lg-none">
    <div class="container-fluid px-3 py-1">
        <div class="d-flex align-items-center gap-1">
            <button class="navbar-toggler text-dark border-0 p-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <i class="bi bi-list fs-2"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center text-dark" href="{{ route('admin.reseller.dashboard') }}" aria-label="Dashboard Reseller">
                <img src="{{ asset('logo-idspora.png') }}" alt="IdSpora" style="height:12px; margin-right:6px; width:auto;">
                <span class="fw-bold small">Admin Reseller</span>
            </a>
        </div>
        
        <div class="d-flex align-items-center gap-2 ms-auto">
            <!-- Mobile Notif Icon -->
            <div class="dropdown">
                <button class="btn btn-light rounded-circle position-relative p-0 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" style="width: 36px; height: 36px;">
                    <i class="bi bi-bell text-secondary"></i>
                    <span class="position-absolute translate-middle badge rounded-circle bg-danger border border-white" style="padding: 4px; left: 75% !important; top: 25% !important;"></span>
                </button>
                <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-3" style="width: 280px; z-index: 1090;">
                    <h6 class="fw-bold mb-2">Notifikasi</h6>
                    <div class="list-group list-group-flush small">
                        <div class="list-group-item border-0 p-2 rounded-3 bg-light mb-1">
                            <div class="fw-bold text-dark">Reseller baru bergabung!</div>
                            <small class="text-secondary opacity-75">5 menit yang lalu</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mobile Profile Icon -->
            <div class="dropdown">
                <a href="#" class="d-block text-decoration-none" data-bs-toggle="dropdown">
                    <div class="avatar-circle shadow-sm" style="width: 36px; height: 36px;">
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
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reseller.data') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.data') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Data Reseller
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.reseller.katalog') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.katalog') ? 'active' : '' }}">
                        <i class="bi bi-journal-bookmark"></i> Katalog Reseller
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.reseller.laporan') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.laporan') ? 'active' : '' }}">
                        <i class="bi bi-bar-chart"></i> Laporan
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- DESKTOP FIXED SIDEBAR -->
<div class="sidebar-desktop d-none d-lg-flex">
    <div class="p-4 d-flex align-items-center gap-3 mb-2">
        <a class="navbar-brand text-dark" href="{{ route('admin.reseller.dashboard') }}" aria-label="Dashboard Reseller">
            <img src="{{ asset('logo-idspora.png') }}" alt="IdSpora" style="height:16px; margin-right:10px; width:auto;">
            <h5 class="fw-bold mb-0">Admin Reseller</h5>
        </a>
    </div>

    <div class="px-3 flex-grow-1">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link sidebar-link-back mb-4">
            <i class="bi bi-arrow-left-circle-fill"></i> Admin Utama
        </a>

        <small class="text-uppercase text-secondary fw-bold px-2 mb-2 d-block" style="font-size: 0.75rem;">Menu Reseller</small>
        
        <a href="{{ route('admin.reseller.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        
        <a href="{{ route('admin.reseller.data') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.data') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Data Reseller
        </a>

        <a href="{{ route('admin.reseller.katalog') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.katalog') ? 'active' : '' }}">
            <i class="bi bi-journal-bookmark"></i> Katalog
        </a>

        <a href="{{ route('admin.reseller.laporan') }}" class="sidebar-link {{ request()->routeIs('admin.reseller.laporan') ? 'active' : '' }}">
            <i class="bi bi-bar-chart"></i> Laporan
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

<!-- LOGOUT CONFIRM MODAL -->
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

<!-- GLOBAL NOTIFICATIONS TOAST -->
@if(session('success') || session('error'))
<div id="globalNotifications" class="global-notification">
    @if(session('success'))
        <div class="notification success show" data-timeout="3000">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <span class="notif-message">{{ session('success') }}</span>
            <button type="button" class="notif-close border-0 bg-transparent text-white" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="notification error show" data-timeout="4000">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span class="notif-message">{{ session('error') }}</span>
            <button type="button" class="notif-close border-0 bg-transparent text-white" onclick="this.parentElement.remove()"><i class="bi bi-x-lg"></i></button>
        </div>
    @endif
</div>
<script>
    setTimeout(function() {
        const notifs = document.querySelectorAll('.notification');
        notifs.forEach(n => n.remove());
    }, 4000);
</script>
@endif