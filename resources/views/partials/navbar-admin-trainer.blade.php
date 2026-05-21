<nav class="navbar navbar-expand-lg border-bottom fixed-top shadow-sm"
    style="background: rgba(255,255,255,.95); backdrop-filter: blur(15px); z-index:1050; height:72px;">
    <div class="container-fluid px-4">

        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.trainer.index') }}">
            <img src="{{ asset('images/logo-idspora-nobg-dark.png') }}" alt="idSpora"
                style="height:32px;width:auto;object-fit:contain;"
                onerror="this.onerror=null;this.src='{{ asset('aset/logo-idspora.png') }}'">

            <span class="fw-800 text-dark tracking-tight"
                style="font-size:1.1rem;font-family:'Plus Jakarta Sans',sans-serif;">
                Admin<span class="text-indigo"> Trainer</span>
            </span>
        </a>

        <div class="ms-auto d-flex align-items-center gap-3">
            <div class="dropdown">
                <button
                    class="btn border-0 p-1 d-flex align-items-center gap-3 shadow-none admin-profile-trigger dropdown-toggle"
                    type="button" id="adminProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">

                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold text-dark mb-0 small">
                            {{ Auth::user()->name ?? 'Admin' }}
                        </div>
                        <div class="text-muted small" style="font-size:11px;">
                            Super Admin
                        </div>
                    </div>

                    <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                        style="width:40px;height:40px;font-size:.9rem;background:linear-gradient(135deg,#1a237e 0%,#3949ab 100%);border:2px solid #fff;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                    </div>
                </button>

                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2"
                    aria-labelledby="adminProfileDropdown" style="min-width:240px; z-index:2000;">

                    <li>
                        <h6 class="dropdown-header small text-muted fw-bold">
                            <i class="bi bi-layers-half me-1"></i> NAVIGATION
                        </h6>
                    </li>

                    <li>
                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center"
                            href="{{ route('admin.dashboard') }}">
                            <div class="bg-light rounded p-1 me-2">
                                <i class="bi bi-speedometer2 text-primary"></i>
                            </div>
                            Main Dashboard
                        </a>
                    </li>

                    <li>
                        <hr class="dropdown-divider opacity-25 my-2">
                    </li>

                    <li>
                        <button type="button"
                            class="dropdown-item text-danger rounded-3 py-2 fw-semibold d-flex align-items-center"
                            data-bs-toggle="modal" data-bs-target="#confirmLogoutModal">
                            Sign Out
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<div class="modal fade" id="confirmLogoutModal" tabindex="-1" aria-labelledby="confirmLogoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="confirmLogoutModalLabel">
                    Konfirmasi Logout
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-muted">
                Apakah kamu yakin ingin keluar dari dashboard admin trainer?
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="button" class="btn btn-danger rounded-3"
                    onclick="document.getElementById('logoutForm').submit();">
                    Ya, Sign Out
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .text-indigo {
        color: #3949ab;
    }

    .fw-800 {
        font-weight: 800;
    }

    .tracking-tight {
        letter-spacing: -0.5px;
    }

    .dropdown-item {
        transition: all .2s ease;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(3px);
    }

    .admin-profile-trigger::after {
        display: none !important;
        content: none !important;
    }
</style>