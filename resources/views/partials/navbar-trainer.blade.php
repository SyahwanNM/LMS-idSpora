<nav class="navbar navbar-expand-lg border-bottom sticky-top shadow-sm" style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(15px); z-index: 1050; height: 72px;">
    <div class="container-fluid px-4">
        
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.trainer.index') }}">
            <div class="bg-indigo rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; background: linear-gradient(135deg, #3949ab 0%, #5c6bc0 100%);">
                <i class="bi bi-person-badge-fill text-white fs-5"></i>
            </div>
            <span class="fw-800 text-dark tracking-tighter" style="font-size: 1.25rem; font-family: 'Plus Jakarta Sans', sans-serif;">
                TRAINER<span class="text-indigo">HUB</span>
            </span>
        </a>

        <div class="vr mx-3 d-none d-lg-block text-muted opacity-25" style="height: 24px;"></div>

        <div class="d-none d-md-flex align-items-center me-auto">
            <span class="badge bg-indigo-subtle text-indigo border border-indigo-subtle rounded-pill px-3 py-2 small fw-bold">
                <i class="bi bi-shield-check me-1"></i> Trainer Management Access
            </span>
        </div>

        <div class="d-flex align-items-center gap-3">
            
            @if(isset($totalTrainers) && $totalTrainers > 0)
                <div class="d-none d-sm-flex align-items-center gap-2 text-decoration-none bg-indigo bg-opacity-10 text-indigo px-3 py-2 rounded-pill" style="font-size: 0.85rem; font-weight: 700;">
                    <i class="bi bi-people-fill"></i>
                    {{ $totalTrainers }} Trainers Active
                </div>
            @endif

            <div class="dropdown">
                <button class="btn border-0 p-1 d-flex align-items-center gap-3 dropdown-toggle shadow-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold text-dark mb-0 small">{{ Auth::user()->name }}</div>
                        <div class="text-muted" style="font-size: 10px;">Administrator</div>
                    </div>
                    <div class="avatar-sm text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 40px; height: 40px; font-size: 0.9rem; background: linear-gradient(135deg, #3949ab 0%, #5c6bc0 100%);">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 mt-2" style="min-width: 220px;">
                    <li>
                        <h6 class="dropdown-header small text-muted uppercase tracking-wider">Trainer Management</h6>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2" href="{{ route('admin.trainer.index') }}">
                            <i class="bi bi-list-ul me-2"></i> All Trainers
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2" href="{{ route('admin.trainer.create') }}">
                            <i class="bi bi-plus-circle me-2"></i> Add New Trainer
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider opacity-50">
                    </li>
                    <li>
                        <a class="dropdown-item rounded-3 py-2" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-grid-1x2 me-2"></i> Main Dashboard
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider opacity-50">
                    </li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger rounded-3 py-2">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>
</nav>

<style>
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

    .hover-scale:hover {
        transform: scale(1.05);
    }

    .fw-800 {
        font-weight: 800;
    }

    .tracking-tighter {
        letter-spacing: -0.5px;
    }
</style>