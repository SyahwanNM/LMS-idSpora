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
        .nav-link.active {
            background-color: #FEF3C7; /* Amber-100 */
            color: #B45309; /* Amber-700 */
            font-weight: 600;
        }
        .nav-link {
            color: #64748B;
            transition: all 0.2s ease;
        }
        .nav-link:hover {
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
                <a class="nav-link rounded-3 px-3 py-2 active d-flex align-items-center gap-3" href="#" onclick="switchView('dashboard', this)">
                    <i class="bi bi-grid-fill"></i> Ringkasan
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link rounded-3 px-3 py-2 d-flex align-items-center gap-3" href="#" onclick="switchView('resellers', this)">
                    <i class="bi bi-people-fill"></i> Data Reseller
                </a>
            </li>
        </ul>

        <small class="text-uppercase text-secondary fw-bold px-3 mb-2 d-block" style="font-size: 0.75rem;">Akun</small>
        <ul class="nav flex-column gap-1">
            <li class="nav-item">
                <a class="nav-link rounded-3 px-3 py-2 d-flex align-items-center gap-3 text-danger" href="#">
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
                <li><a class="nav-link active p-3 bg-light rounded-3 text-dark fw-bold" href="#" onclick="switchView('dashboard', null); closeOffcanvas()">Ringkasan</a></li>

                <li><a class="nav-link p-3 text-secondary" href="#" onclick="switchView('resellers', null); closeOffcanvas()">Data Reseller</a></li>
                <li class="mt-4"><a class="nav-link p-3 text-danger border rounded-3" href="#">Keluar</a></li>
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
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card bg-primary bg-opacity-10 border border-primary">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="bg-white text-primary rounded-3 p-2 shadow-sm" style="width: 48px; height: 48px; display: grid; place-items: center;">
                                        <i class="bi bi-people-fill fs-4"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold mb-1">{{ $activeResellersCount }} User</h3>
                                <p class="text-muted small mb-0">Reseller aktif terdaftar.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="bg-success bg-opacity-10 text-success rounded-3 p-2" style="width: 48px; height: 48px; display: grid; place-items: center;">
                                        <i class="bi bi-cart-check-fill fs-4"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold mb-1">{{ $totalSalesCount }} Sales</h3>
                                <p class="text-muted small mb-0">Total konversi/penjualan berhasil.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2" style="width: 48px; height: 48px; display: grid; place-items: center;">
                                        <i class="bi bi-clock-history fs-4"></i>
                                    </div>
                                </div>
                                <h3 class="fw-bold mb-1">{{ $totalPendingReferrals }} Pending</h3>
                                <p class="text-muted small mb-0">Referral menunggu verifikasi.</p>
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
                                @forelse($topResellers as $index => $reseller)
                                <div class="list-group-item p-3 border-0 d-flex align-items-center gap-3">
                                    <div class="bg-light rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($reseller->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold small">{{ $reseller->name }}</h6>
                                        <small class="text-success">Rp {{ number_format($reseller->total_earned / 1000, 0, ',', '.') }}k komisi</small>
                                    </div>
                                    <span class="ms-auto badge {{ $index == 0 ? 'bg-warning text-dark' : ($index == 1 ? 'bg-secondary' : 'bg-light text-muted') }}">#{{ $index + 1 }}</span>
                                </div>
                                @empty
                                    <p class="p-4 text-center text-muted small">Belum ada data.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <div id="resellers-view" class="view-section" style="display: none;">
                <h2 class="fw-bold text-dark mb-4">Data Reseller</h2>
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <p class="text-muted">Halaman ini akan berisi daftar lengkap seluruh user reseller, status keaktifan, dan total pendapatan mereka.</p>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Reseller</th>
                                        <th>Email</th>
                                        <th>Kode</th>
                                        <th>Total Earnings</th>
                                        <th>Join Date</th>
                                        <th>Tier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resellers as $reseller)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($reseller->name, 0, 2)) }}
                                                </div>
                                                <div class="fw-semibold">{{ $reseller->name }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $reseller->email }}</td>
                                        <td><span class="badge bg-light text-dark border"><code>{{ $reseller->referral_code }}</code></span></td>
                                        <td class="fw-bold text-success">Rp {{ number_format($reseller->total_earned ?? 0, 0, ',', '.') }}</td>
                                        <td class="small">{{ $reseller->created_at->format('d M Y') }}</td>
                                        <td>
                                            @php
                                                $count = $reseller->referrals_count;
                                                $tier = 'Bronze';
                                                $class = 'bg-secondary';
                                                if($count >= 151) { $tier = 'Gold'; $class = 'bg-warning text-dark'; }
                                                elseif($count >= 51) { $tier = 'Silver'; $class = 'bg-info text-dark'; }
                                            @endphp
                                            <span class="badge {{ $class }}">{{ $tier }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

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
                document.querySelectorAll('.sidebar-desktop .nav-link').forEach(el => el.classList.remove('active'));
                navElement.classList.add('active');
            }
        }

        // 2. Logic Close Offcanvas (Mobile)
        function closeOffcanvas() {
            const offcanvasEl = document.getElementById('mobileSidebar');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (bsOffcanvas) bsOffcanvas.hide();
        }


        // 4. Initialize Chart (Dummy Data)
        const ctx = document.getElementById('dashboardChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                        label: 'Reseller Baru',
                        data: @json($chartValues),
                        borderColor: '#B45309',
                        backgroundColor: 'rgba(217, 119, 6, 0.1)',
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