<nav class="navbar navbar-expand-lg border-bottom sticky-top shadow-sm" style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(15px); z-index: 1050; height: 72px;">
    <div class="container-fluid px-4">
        <!-- Brand / Identity -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.finance.index') }}">
            <div class="bg-warning rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                <i class="bi bi-wallet2 text-dark fs-5"></i>
            </div>
            <span class="fw-800 text-dark tracking-tighter" style="font-size: 1.25rem; font-family: 'Plus Jakarta Sans', sans-serif;">FINANCE<span class="text-warning">HUB</span></span>
        </a>

        <!-- Divider -->
        <div class="vr mx-3 d-none d-lg-block text-muted opacity-25" style="height: 24px;"></div>

        <!-- Role Badge -->
        <div class="d-none d-md-flex align-items-center me-auto">
            <span class="badge bg-dark-subtle text-dark border rounded-pill px-3 py-2 small fw-bold">
                <i class="bi bi-shield-lock-fill me-1 text-warning"></i> Admin Financial Access
            </span>
        </div>

        <!-- Right Side: User & Quick Actions -->
        <div class="d-flex align-items-center gap-3">
            <!-- Payout Counter (Mini) -->
            @if(isset($pendingWithdrawals) && $pendingWithdrawals > 0)
            <a href="{{ route('admin.withdrawals.index') }}" class="d-none d-sm-flex align-items-center gap-2 text-decoration-none bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill transition-all hover-scale" style="font-size: 0.85rem; font-weight: 700;">
                <span class="position-relative d-flex">
                    <i class="bi bi-bell-fill"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle anim-pulse"></span>
                </span>
                {{ $pendingWithdrawals }} Payout Orders
            </a>
            @endif

            <!-- User Profile Dropdown -->
            <div class="dropdown">
                <button class="btn border-0 p-1 d-flex align-items-center gap-3 dropdown-toggle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold text-dark mb-0 small">{{ Auth::user()->name }}</div>
                        <div class="text-muted" style="font-size: 10px;">Chief Financial Officer</div>
                    </div>
                    <div class="avatar-sm bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px; font-size: 0.9rem;">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2" style="min-width: 200px;">
                    <li><h6 class="dropdown-header small text-muted uppercase tracking-wider">Session Settings</h6></li>
                    <li><a class="dropdown-item rounded-3 py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2 me-2"></i> Main Dashboard</a></li>
                    <li><hr class="dropdown-divider opacity-50"></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger rounded-3 py-2"><i class="bi bi-box-arrow-right me-2"></i> End Session</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<style>
    .hover-scale:hover { transform: scale(1.05); }
    .anim-pulse { animation: pulse-red 2s infinite; }
    @keyframes pulse-red {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
    .fw-800 { font-weight: 800; }
    .tracking-tighter { letter-spacing: -0.5px; }
</style>
