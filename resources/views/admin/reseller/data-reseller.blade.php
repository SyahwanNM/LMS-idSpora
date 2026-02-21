<div id="resellers-view" class="view-section" style="display: none;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-4 gap-3">
        <div>
            <h2 class="fw-bold text-dark mb-1">Data Reseller</h2>
            <p class="text-muted mb-0">Daftar lengkap seluruh user reseller beserta performanya.</p>
        </div>
        <input type="text" class="form-control rounded-3" placeholder="Cari reseller..." style="max-width: 250px;">
    </div>
    
    <div class="card border-0 shadow-sm rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Nama</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Kode Referral</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Level</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Total Earnings</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Total Referral</th>
                        <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Status</th>
                        <th class="py-3 pe-4 text-center text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 py-3 fw-bold">Stephanie</td>
                        <td><span class="badge bg-light text-dark border font-monospace">STP992</span></td>
                        <td><span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i> Gold</span></td>
                        <td class="text-success fw-bold">Rp 2.500.000</td>
                        <td>162 User</td>
                        <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-3">Active</span></td>
                        <td class="pe-4 text-center">
                            <button class="btn btn-sm btn-outline-primary rounded-3" onclick="openResellerDetail('Stephanie', 'STP992', 'Rp 2.500.000', '162')">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>