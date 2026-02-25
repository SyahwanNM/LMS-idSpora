<div id="dashboard-view" class="view-section">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Dashboard</h2>
            <p class="text-secondary mb-0">Overview aktivitas reseller hari ini.</p>
        </div>
        <div class="d-none d-md-block">
            <button class="btn btn-white bg-white border shadow-sm px-3 py-2 rounded-3 text-secondary">
                <i class="bi bi-calendar3 me-2"></i> {{ date('d M Y') }}
            </button>
        </div>
    </div>

    <div class="row g-4 mb-5">
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card bg-warning bg-opacity-10 border border-warning">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between mb-3">
                    <div class="bg-white text-warning rounded-3 p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                    <div class="stat-label text-warning-emphasis fw-semibold">Perlu Persetujuan</div>
                </div>
                <div class="stat-value text-warning-emphasis fw-bold fs-3 mb-1">Rp 450.000</div>
                <div class="text-muted small mb-3">Dari <strong>3 reseller</strong> menunggu</div>
                
                <div class="mt-auto">
                    <button class="btn btn-warning btn-sm w-100 fw-bold shadow-sm" onclick="switchView('finance', document.querySelectorAll('.sidebar')[1])">Proses Sekarang</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card bg-success bg-opacity-10 border border-success">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between mb-3">
                    <div class="bg-white text-success rounded-3 p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                    <div class="stat-label text-success-emphasis fw-semibold">Total Dibayarkan</div>
                </div>
                <div class="stat-value text-success-emphasis fw-bold fs-3 mb-1">Rp 12.500.000</div>
                <div class="text-muted small mb-3">Dana telah berhasil ditransfer</div>
                
                <div class="mt-auto"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card bg-danger bg-opacity-10 border border-danger">
            <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between mb-3">
                    <div class="bg-white text-danger rounded-3 p-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-shield-x fs-4"></i>
                    </div>
                    <div class="stat-label text-danger-emphasis fw-semibold">Total Ditolak</div>
                </div>
                <div class="stat-value text-danger-emphasis fw-bold fs-3 mb-1">Rp 1.250.000</div>
                <div class="text-muted small mb-3">Dana dikembalikan ke saldo user</div>
                
                <div class="mt-auto"></div>
            </div>
        </div>
    </div>

</div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                <h6 class="fw-bold mb-4">Tren Pendaftaran Reseller</h6>
                <canvas id="dashboardChart" style="max-height: 300px;"></canvas>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-0 h-100">
                <div class="card-header bg-white border-bottom p-4">
                    <h6 class="fw-bold mb-0">Top Performer</h6>
                </div>
                <div class="list-group list-group-flush">
                    <div class="list-group-item p-3 border-0 d-flex align-items-center gap-3">
                        <div class="bg-light rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">JO</div>
                        <div>
                            <h6 class="mb-0 fw-bold small">Jocua Cuherman</h6>
                            <small class="text-success">Rp 1.2jt komisi</small>
                        </div>
                        <span class="ms-auto badge bg-warning text-dark">#1</span>
                    </div>
                    <div class="list-group-item p-3 border-0 d-flex align-items-center gap-3">
                        <div class="bg-light rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">VS</div>
                        <div>
                            <h6 class="mb-0 fw-bold small">Ver Sianu</h6>
                            <small class="text-success">Rp 990k komisi</small>
                        </div>
                        <span class="ms-auto badge bg-secondary">#2</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>