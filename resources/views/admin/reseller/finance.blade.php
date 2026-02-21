<div id="finance-view" class="view-section" style="display: none;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Keuangan & Payout</h2>
            <p class="text-secondary mb-0">Kelola permintaan penarikan dana dari reseller.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary rounded-3">History</button>
            <button class="btn btn-dark rounded-3"><i class="bi bi-download me-2"></i>Export</button>
        </div>
    </div>

    <div class="alert alert-warning border-0 rounded-3 d-flex align-items-center gap-3 mb-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-4 text-warning-emphasis"></i>
        <div><strong>Perhatian!</strong> Ada 3 permintaan penarikan baru yang menunggu persetujuan Anda.</div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">ID / Waktu</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Reseller</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Bank Tujuan</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Nominal</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Status</th>
                        <th class="py-3 pe-4 text-end text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">#WD-1029</div>
                            <small class="text-muted">22 Feb 2026, 08:30</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">VS</div>
                                <div>
                                    <div class="fw-bold text-dark">Ver Sianu</div>
                                    <small class="text-muted">Silver Tier</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">BCA</div>
                            <small class="text-muted font-monospace">1234567890</small>
                        </td>
                        <td><div class="fw-bold text-dark fs-6">Rp 150.000</div></td>
                        <td><span class="badge bg-warning bg-opacity-10 text-warning-emphasis px-3 py-2 rounded-pill">Pending</span></td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-primary btn-sm px-3 py-2 rounded-pill fw-bold" onclick="openReviewModal('Ver Sianu', '150000', 'BCA', '1234567890', 'a.n Ver Sianu')">
                                Tinjau
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>