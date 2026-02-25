<div class="sidebar-desktop d-none d-lg-block p-3 z-3 mt-2">
    <small class="text-uppercase text-secondary fw-bold px-3 mb-2 d-block" style="font-size: 0.75rem;">Menu Utama</small>
    <ul class="nav flex-column gap-1 mb-4">
        <li class="nav-item">
            <a class="sidebar rounded-3 px-3 py-2 active d-flex align-items-center gap-3" href="#" onclick="switchView('dashboard', this)">
                <i class="bi bi-grid-fill"></i> Ringkasan
            </a>
        </li>
        <li class="nav-item">
            <a class="sidebar rounded-3 px-3 py-2 d-flex align-items-center gap-3" href="#" onclick="switchView('finance', this)">
                <i class="bi bi-wallet2"></i> Keuangan & Payout
                <span class="badge bg-danger rounded-pill ms-auto">3</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="sidebar rounded-3 px-3 py-2 d-flex align-items-center gap-3" href="#" onclick="switchView('resellers', this)">
                <i class="bi bi-people-fill"></i> Data Reseller
            </a>
        </li>
    </ul>

    <small class="text-uppercase text-secondary fw-bold px-3 mb-2 d-block" style="font-size: 0.75rem;">Akun</small>
    <ul class="nav flex-column gap-1">
        <li class="nav-item">
            <a class="sidebar rounded-3 px-3 py-2 d-flex align-items-center gap-3 text-danger" href="#">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </li>
    </ul>
</div>

<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header border-bottom">
        <h5 class="offcanvas-title fw-bold">IdSpora Admin</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-3">
        <ul class="nav flex-column gap-2">
            <li><a class="sidebar active p-3 bg-light rounded-3 text-dark fw-bold" href="#" onclick="switchView('dashboard', null); closeOffcanvas()">Ringkasan</a></li>
            <li><a class="sidebar p-3 text-secondary" href="#" onclick="switchView('finance', null); closeOffcanvas()">Keuangan (3 Pending)</a></li>
            <li><a class="sidebar p-3 text-secondary" href="#" onclick="switchView('resellers', null); closeOffcanvas()">Data Reseller</a></li>
            <li class="mt-4"><a class="sidebar p-3 text-danger border rounded-3" href="#">Keluar</a></li>
        </ul>
    </div>
</div>