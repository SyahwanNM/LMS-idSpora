<nav class="navbar navbar-expand-lg border-bottom sticky-top shadow-sm"
    style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(15px); z-index: 1050; height: 72px;">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.trainer.index') }}">
            <img src="{{ asset('images/logo-idspora-nobg-dark.png') }}" alt="idSpora"
                style="height:32px; width:auto; object-fit:contain;"
                onerror="this.onerror=null;this.src='{{ asset('aset/logo-idspora.png') }}'">
            <div class="d-flex flex-column justify-content-center" style="line-height:1;">
                <span class="fw-800 text-dark tracking-tight"
                    style="font-size:1.1rem;font-family:'Plus Jakarta Sans',sans-serif;">
                    Admin<span class="text-indigo"> Trainer</span>
                </span>
            </div>
        </a>
        <div class="d-flex align-items-center gap-3">

            <div class="dropdown">
                <button class="btn border-0 p-1 d-flex align-items-center gap-3 shadow-none admin-profile-trigger"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold text-dark mb-0 small">{{ Auth::user()->name ?? 'Admin' }}</div>
                        <div class="text-muted small" style="font-size: 11px;">Super Admin</div>
                    </div>
                    <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm"
                        style="width: 40px; height: 40px; font-size: 0.9rem; background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%); border: 2px solid #fff;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 2)) }}
                    </div>
                </button>

                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2"
                    style="min-width: 240px;">
                    <li>
                        <h6 class="dropdown-header small text-muted uppercase tracking-wider fw-bold">
                            <i class="bi bi-layers-half me-1"></i> NAVIGATION
                        </h6>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center"
                            href="{{ route('admin.dashboard') }}">
                            <div class="bg-light rounded p-1 me-2"><i class="bi bi-speedometer2 text-primary"></i></div>
                            Main Dashboard
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider opacity-25 my-2">
                    </li>
                    
                    <li>
                        <hr class="dropdown-divider opacity-25 my-2">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="dropdown-item text-danger rounded-3 py-2 fw-semibold d-flex align-items-center">Sign Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</nav>

<style>
    /* (rest of navbar styles unchanged) */

    .text-indigo {
        color: #3949ab;
    }

    .bg-indigo {
        background-color: #3949ab;
    }

    .bg-indigo-subtle {
        background-color: #e8eaf6;
    }

    .border-indigo-subtle {
        border-color: #c5cae9 !important;
    }

    .fw-800 {
        font-weight: 800;
    }

    .tracking-tight {
        letter-spacing: -0.5px;
    }

    /* Hover effect pada dropdown item */
    .dropdown-item:hover {
        background-color: #f8f9fa;
        transform: translateX(3px);
    }

    .dropdown-item {
        transition: all 0.2s ease;
    }

    /* Ensure no caret/ellipsis indicator appears next to admin profile trigger */
    .admin-profile-trigger {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: none !important;
        padding-right: 0 !important;
    }

    .admin-profile-trigger::after {
        display: none !important;
        content: none !important;
    }

    .admin-profile-trigger.dropdown-toggle::after {
        display: none !important;
        content: none !important;
    }

    .admin-profile-trigger::-ms-expand {
        display: none !important;
    }
</style>