@include('partials.navbar-admin')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - IdSpora</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* CSS HANYA UNTUK CONFIG & ANIMASI */
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FAFC; /* Slate-50 */
        }

        /* Sidebar Fixed Width untuk Desktop */
        .sidebar-desktop {
            width: 280px;
            height: 100vh;
            position: fixed;
            overflow-y: auto;
            border-right: 1px solid #e9ecef;
            background: white;
        }

        /* Main Content Offset */
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }

        /* Responsive Fix */
        @media (max-width: 992px) {
            .sidebar-desktop { display: none; }
            .main-content { margin-left: 0; }
        }

        /* Animasi Hover Card */
        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.05)!important;
        }

        /* Active Menu Styling */
        .sidebar.active {
            background-color: #FEF3C7; /* Amber-100 */
            color: #B45309; /* Amber-700 */
            font-weight: 600;
        }
        .sidebar {
            color: #64748B;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .sidebar:hover {
            background-color: #FFFBEB;
            color: #B45309;
        }
    </style>
</head>

<body>

    <div class="sidebar-desktop d-none d-lg-block p-3 z-3">
        <div class="d-flex align-items-center gap-2 px-3 mb-5 mt-2">
            <div class="bg-warning rounded-3 p-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="bi bi-flower1 text-white fs-5"></i>
            </div>
            <h5 class="fw-bold mb-0 text-dark">IdSpora Admin</h5>
        </div>

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

    <main class="main-content min-vh-100">
        
        <nav class="navbar bg-white border-bottom sticky-top px-4 py-3 d-lg-none">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="fw-bold">Admin Panel</span>
            </div>
        </nav>

        <div class="p-4 p-md-5">
            
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
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="bg-white text-warning rounded-3 p-2 shadow-sm" style="width: 48px; height: 48px; display: grid; place-items: center;">
                                        <i class="bi bi-hourglass-split fs-4"></i>
                                    </div>
                                    <div class="stat-label text-warning-emphasis">Perlu Persetujuan</div>
                                </div>
                        <div class="stat-value text-warning-emphasis">Rp 450.000</div>
                        <div class="text-muted small">Dari <strong>3 reseller</strong> menunggu</div>
                                <button class="btn btn-warning btn-sm w-100 mt-3 fw-bold shadow-sm" onclick="switchView('finance', document.querySelectorAll('.sidebar')[1])">Proses Sekarang</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2" style="width: 48px; height: 48px; display: grid; place-items: center;">
                                        <i class="bi bi-cash-stack fs-4"></i>
                                    </div>
                                    <div class="stat-label">Total Dibayarkan</div>
                                </div>
                                
                        <div class="stat-value">Rp 12.500.000</div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2" style="width: 48px; height: 48px; display: grid; place-items: center;">
                                        <i class="bi bi-people-fill fs-4"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold mb-1">142 User</h3>
                                <p class="text-muted small mb-0">Reseller aktif terdaftar.</p>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2" style="width: 48px; height: 48px; display: grid; place-items: center;">
                                        <i class="bi bi-shield-x"></i>
                                    </div>
                                    <div class="stat-label">Total Ditolak (Fraud/Salah)</div>
                                </div>
                                
                        <div class="stat-value text-danger">Rp 1.250.000</div>
                        <div class="text-muted small">Dana dikembalikan ke saldo user</div>
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

            [ini tambahin di tabelnya nanti ID WITHDRAW TRUS TAMBAH WAKTU REQUESTNYA JUGA BIAR ENAK CEKNYA]
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
                    <div>
                        <strong>Perhatian!</strong> Ada 3 permintaan penarikan baru yang menunggu persetujuan Anda.
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Reseller</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Bank Tujuan</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Nominal</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Status</th>
                                    <th class="py-3 pe-4 text-end text-secondary text-uppercase" style="font-size: 0.75rem; font-weight: 700;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="ps-4 py-3">
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
                                    <td>
                                        <div class="fw-bold text-dark fs-6">Rp 150.000</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis px-3 py-2 rounded-pill">Pending</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-primary btn-sm px-3 py-2 rounded-pill fw-bold" 
                                            onclick="openReviewModal('Ver Sianu', '150000', 'BCA', '1234567890', 'a.n Ver Sianu')">
                                            Tinjau
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 bg-warning bg-opacity-10 text-warning-emphasis fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">JO</div>
                                            <div>
                                                <div class="fw-bold text-dark">Jocua C.</div>
                                                <small class="text-muted">Gold Tier</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">GoPay</div>
                                        <small class="text-muted font-monospace">0812345678</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark fs-6">Rp 50.000</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning bg-opacity-10 text-warning-emphasis px-3 py-2 rounded-pill">Pending</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-primary btn-sm px-3 py-2 rounded-pill fw-bold" 
                                            onclick="openReviewModal('Jocua C.', '50000', 'GoPay', '0812345678', 'a.n Jocua')">
                                            Tinjau
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-white py-3 text-center">
                        <small class="text-muted">Menampilkan 2 dari 2 permintaan pending</small>
                    </div>
                </div>
            </div>

            [DI SINI TAMBAHIN Nama	Kode Referral	Level	Total Earnings	Total Referral	Status]
            [TRUS KALO DIKLIK SI USERNYA BAKAL MUNCUL DETAIL-DETAILNYA SALAH SATUNYAKEK SIAPA AJA YANG DAH MAKE KODE DIA GITU BUAT MEMUDAHKAN CEKNYA]
            <div id="resellers-view" class="view-section" style="display: none;">
                <h2 class="fw-bold text-dark mb-4">Data Reseller</h2>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted">Halaman ini akan berisi daftar lengkap seluruh user reseller, status keaktifan, dan total pendapatan mereka.</p>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Join Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Budi Santoso</td>
                                        <td>budi@example.com</td>
                                        <td>10 Jan 2026</td>
                                        <td><span class="badge bg-success">Active</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold">Konfirmasi Transfer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    
                    <div class="bg-light p-4 rounded-4 mb-4 text-center border border-dashed">
                        <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem; letter-spacing: 1px;">TOTAL TRANSFER</small>
                        <h1 class="fw-bold text-success my-2" id="modalAmount">Rp 0</h1>
                        <span class="badge bg-white border text-dark rounded-pill px-3" id="modalReseller">Nama User</span>
                    </div>

                    <ul class="list-group list-group-flush mb-4 rounded-3 border">
                        <li class="list-group-item d-flex justify-content-between py-3">
                            <span class="text-secondary">Bank Tujuan</span>
                            <span class="fw-bold text-dark" id="modalBank">BCA</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-3">
                            <span class="text-secondary">No. Rekening</span>
                            <span class="fw-bold text-dark font-monospace" id="modalRekening">1234567890</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between py-3">
                            <span class="text-secondary">Atas Nama</span>
                            <span class="fw-bold text-dark" id="modalName">Nama Pemilik</span>
                        </li>
                    </ul>

                    <div class="d-grid gap-2">
                        <button class="btn btn-success py-3 rounded-3 fw-bold shadow-sm" data-bs-dismiss="modal">
                            <i class="bi bi-check-circle-fill me-2"></i> Sudah Ditransfer (Approve)
                        </button>
                        <button class="btn btn-outline-danger py-3 rounded-3 fw-bold" data-bs-toggle="collapse" data-bs-target="#rejectSection">
                            Tolak Permintaan
                        </button>
                    </div>

                    <div class="collapse mt-3" id="rejectSection">
                        <div class="card card-body bg-danger bg-opacity-10 border-danger border-opacity-25 rounded-3 border-0">
                            <label class="small fw-bold text-danger mb-2">Alasan Penolakan:</label>
                            <textarea class="form-control mb-2" rows="2" placeholder="Contoh: Nomor rekening salah..."></textarea>
                            <button class="btn btn-danger btn-sm w-100">Konfirmasi Tolak</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. Logic Pindah Menu (SPA Feel)
        function switchView(viewId, navElement) {
            // Hide all views
            document.querySelectorAll('.view-section').forEach(el => el.style.display = 'none');
            // Show selected
            document.getElementById(viewId + '-view').style.display = 'block';

            // Update Desktop Nav Active State
            if (navElement) {
                document.querySelectorAll('.sidebar-desktop .sidebar').forEach(el => el.classList.remove('active'));
                navElement.classList.add('active');
            }
        }

        // 2. Logic Close Offcanvas (Mobile)
        function closeOffcanvas() {
            const offcanvasEl = document.getElementById('mobileSidebar');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (bsOffcanvas) bsOffcanvas.hide();
        }

        // 3. Logic Isi Data Modal secara Dinamis
        function openReviewModal(name, amount, bank, rek, holder) {
            document.getElementById('modalReseller').innerText = name;
            document.getElementById('modalBank').innerText = bank;
            document.getElementById('modalRekening').innerText = rek;
            document.getElementById('modalName').innerText = holder;
            
            // Format Rupiah
            const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
            document.getElementById('modalAmount').innerText = formatted;

            // Show Modal
            const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
            modal.show();
        }

        // 4. Initialize Chart (Dummy Data)
        const ctx = document.getElementById('dashboardChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        label: 'Reseller Baru',
                        data: [2, 5, 3, 8, 4, 10, 6],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }
    </script>
</body>
</html>