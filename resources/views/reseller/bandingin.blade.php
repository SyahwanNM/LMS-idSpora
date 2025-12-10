@include ('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Reseller IdSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>

<body class="bg-light text-dark">
    
    <main class="container-xxl pt-5 mt-5">
        
        {{-- <nav class="navbar ..."> ... </nav> --}}

        <div class="container-xl">
            
            <div class="card mb-4 border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <i class="bi bi-bullseye text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <div class="col">
                            <h5 class="card-title fw-bold mb-1">Target Komisi Bulan Ini</h5>
                            <p class="card-text text-muted mb-2">Kamu sudah mencapai <span class="fw-bold text-dark">80%</span> dari target!</p>
                            
                            <div class="progress" role="progressbar" style="height: 20px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                    style="width: 75%">
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-2 small fw-semibold text-muted">
                                <span>Rp 8.000.000</span>
                                <span>Rp 10.000.000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4">
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="text-secondary fw-semibold mb-1">Total Referrals</h6>
                                    <h3 class="fw-bold mb-0">150</h3>
                                </div>
                                <div class="p-2 bg-warning bg-opacity-10 rounded-3">
                                    <i class="bi bi-person-circle fs-3 text-warning"></i>
                                </div>
                            </div>
                            <div class="d-flex align-items-center text-success small fw-semibold">
                                <i class="bi bi-arrow-up-right me-1"></i>
                                <span>+15 bulan ini</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="text-secondary fw-semibold mb-1">Pending Earnings</h6>
                                    <h3 class="fw-bold mb-0">Rp 1.8jt</h3> </div>
                                <div class="p-2 bg-warning bg-opacity-10 rounded-3">
                                    <i class="bi bi-wallet fs-3 text-warning"></i>
                                </div>
                            </div>
                            <p class="text-warning small fw-semibold mb-0">Menunggu Verifikasi</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="text-secondary fw-semibold mb-1">Total Earnings</h6>
                                    <h3 class="fw-bold mb-0">Rp 5jt</h3>
                                </div>
                                <div class="p-2 bg-warning bg-opacity-10 rounded-3">
                                    <i class="bi bi-cash-stack fs-3 text-warning"></i>
                                </div>
                            </div>
                            <p class="text-success small fw-semibold mb-0">+Rp 520k bulan ini</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="text-secondary fw-semibold mb-1">Conversion Rate</h6>
                                    <h3 class="fw-bold mb-0">24.5%</h3>
                                </div>
                                <div class="p-2 bg-warning bg-opacity-10 rounded-3">
                                    <i class="bi bi-graph-up fs-3 text-warning"></i>
                                </div>
                            </div>
                            <p class="text-success small fw-semibold mb-0">+2.3% dari bulan lalu</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Referrals Tools</h5>
                    
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="form-label small text-muted fw-bold text-uppercase ls-1">Referral Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light border-0" value="616ja03095" readonly>
                                <button class="btn btn-warning text-white" type="button">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label small text-muted fw-bold text-uppercase ls-1">Referral Link</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light border-0" value="idspora.com/ref/616ja" readonly>
                                <button class="btn btn-warning text-white" type="button">
                                    <i class="bi bi-link-45deg"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <label class="form-label small text-muted fw-bold text-uppercase ls-1">Caption</label>
                            <div class="input-group">
                                <textarea class="form-control bg-light border-0" rows="1" style="resize: none;" readonly>Join IdSpora...</textarea>
                                <button class="btn btn-warning text-white" type="button">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="my-4 border-top"></div>

                    <h5 class="fw-bold mb-4">How It Works</h5>
                    <div class="row text-center g-4">
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="bi bi-share-fill display-5 text-warning mb-3 d-block"></i>
                                <h6 class="fw-bold">1. Share</h6>
                                <p class="small text-muted">Bagikan kode referral ke teman atau sosmed.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="bi bi-people-fill display-5 text-warning mb-3 d-block"></i>
                                <h6 class="fw-bold">2. Register</h6>
                                <p class="small text-muted">Teman mendaftar & membeli kursus diskon 15%.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3">
                                <i class="bi bi-cash-coin display-5 text-warning mb-3 d-block"></i>
                                <h6 class="fw-bold">3. Earn</h6>
                                <p class="small text-muted">Dapatkan komisi 10% dari setiap transaksi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h5 class="fw-bold mb-1">Riwayat Komisi Terbaru</h5>
                            <p class="text-muted small mb-0">Detail transaksi dan komisi yang Anda peroleh</p>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill">Lihat Semua</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="bg-light">
                                <tr class="text-secondary small text-uppercase">
                                    <th class="fw-bold border-0 rounded-start">Tanggal</th>
                                    <th class="fw-bold border-0">Nama Pembeli</th>
                                    <th class="fw-bold border-0">Kursus</th>
                                    <th class="fw-bold border-0">Komisi</th>
                                    <th class="fw-bold border-0 rounded-end text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>28 Nov 2025</td>
                                    <td class="fw-semibold">Vero Glorify</td>
                                    <td>Figma 101</td>
                                    <td class="text-success fw-bold">+Rp 25.000</td>
                                    <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">Dibayar</span></td>
                                </tr>
                                <tr>
                                    <td>27 Nov 2025</td>
                                    <td class="fw-semibold">Maria S.</td>
                                    <td>SLR & Bibliometrik</td>
                                    <td class="text-success fw-bold">+Rp 25.000</td>
                                    <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">Dibayar</span></td>
                                </tr>
                                <tr>
                                    <td>26 Nov 2025</td>
                                    <td class="fw-semibold">Agvin P.</td>
                                    <td>Web Design Vol 2</td>
                                    <td class="text-success fw-bold">+Rp 40.000</td>
                                    <td class="text-center"><span class="badge bg-warning bg-opacity-10 text-warning px-3 rounded-pill">Menunggu</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Top Reseller</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-warning text-dark rounded-circle p-2 me-3" style="width: 32px; height: 32px;">1</span>
                                        <div>
                                            <div class="fw-bold text-dark">Stephanie A. Tarigan</div>
                                            <small class="text-muted">245 referrals</small>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-success">Rp 1.2jt</div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-secondary rounded-circle p-2 me-3" style="width: 32px; height: 32px;">2</span>
                                        <div>
                                            <div class="fw-bold">Vero Glorify S.</div>
                                            <small class="text-muted">198 referrals</small>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-success">Rp 990rb</div>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3 mt-2 bg-warning bg-opacity-10 rounded-3 border-0">
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold text-warning me-3 ps-2">#50</span>
                                        <div>
                                            <div class="fw-bold">You (Sutupani)</div>
                                            <small class="text-muted">10 referrals</small>
                                        </div>
                                    </div>
                                    <div class="fw-bold text-success">Rp 50rb</div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Level Anda</h5>
                            <div class="d-flex align-items-center mb-4">
                                <div class="display-6 fw-bold text-warning me-3">Bronze</div>
                                <span class="badge bg-warning text-dark rounded-pill">Komisi 10%</span>
                            </div>

                            <p class="small fw-semibold mb-1 d-flex justify-content-between">
                                <span>Progress ke Silver</span>
                                <span>14/50 Referrals</span>
                            </p>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 28%"></div>
                            </div>
                            <div class="alert alert-light border-warning border-start border-4 small text-muted">
                                Anda butuh <span class="fw-bold text-dark">36 referrals</span> lagi untuk naik ke 
                                <span class="fw-bold text-secondary">Silver</span> dan dapat komisi 12%.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-5">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">FAQ</h5>
                    <div class="accordion accordion-flush" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Kapan komisi cair?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small">
                                    Komisi akan masuk ke saldo "Available to Withdraw" 1x24 jam setelah transaksi dinyatakan valid (tidak direfund).
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Cara withdraw dana?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small">
                                    Masuk ke menu Earnings > Withdraw. Minimal penarikan Rp 50.000 ke rekening bank atau E-Wallet.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>