@include('partials.navbar-reseller')
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

        /* Main Content Offset */
        .main-content {
            margin-left: 280px;
            transition: margin-left 0.3s ease;
        }


        /* Animasi Hover Card */
        .hover-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.05)!important;
        }

    </style>
</head>

<body>
    <main class="main-content min-vh-100">

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
                                    <div class="stat-label">Total Dibayarkan</div>
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
                        <p class="text-muted">Halaman ini berisi daftar lengkap seluruh user reseller, status keaktifan, total referral, dan pendapatannya.</p>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Profil Reseller</th>
                                        <th>Kode Referral</th>
                                        <th>Total Earnings</th>
                                        <th>Total Referral</th>
                                        <th>Join Date</th>
                                        <th>Level</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resellers as $reseller)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm bg-light text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold border border-warning" style="width: 36px; height: 36px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($reseller->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $reseller->name }}</div>
                                                    <div class="small text-muted" style="font-size:11px;">{{ $reseller->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-tag-fill text-warning me-1"></i> {{ $reseller->referral_code }}</span></td>
                                        <td class="fw-bold text-success">Rp {{ number_format($reseller->total_earned ?? 0, 0, ',', '.') }}</td>
                                        <td class="fw-bold">{{ $reseller->referrals_count ?? 0 }} Org</td>
                                        <td class="small text-muted">{{ $reseller->created_at?->format('d M Y') ?? '-' }}</td>
                                        <td>
                                            @php
                                                $count = $reseller->referrals_count ?? 0;
                                                $tier = 'Bronze';
                                                $class = 'bg-secondary';
                                                if($count >= 151) { $tier = 'Gold'; $class = 'bg-warning text-dark'; }
                                                elseif($count >= 51) { $tier = 'Silver'; $class = 'bg-info text-dark'; }
                                            @endphp
                                            <span class="badge {{ $class }} rounded-pill px-3">{{ $tier }}</span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $reseller->id }}">
                                                <i class="bi bi-person-lines-fill me-1"></i> Detail
                                            </button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="detailModal{{ $reseller->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold">
                                                        <i class="bi bi-people-fill text-warning me-2"></i> Daftar Referral: {{ $reseller->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="table-responsive border rounded-3">
                                                        <table class="table table-sm table-hover align-middle mb-0">
                                                            <thead class="bg-light text-muted small">
                                                                <tr>
                                                                    <th class="ps-3 py-3 border-bottom-0">Nama Pengguna</th>
                                                                    <th class="py-3 border-bottom-0">Tanggal Daftar</th>
                                                                    <th class="py-3 border-bottom-0">Status Komisi</th>
                                                                    <th class="text-end pe-3 py-3 border-bottom-0">Jumlah</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($reseller->referrals()->with('referredUser')->latest()->get() as $ref)
                                                                <tr>
                                                                    <td class="ps-3 fw-medium text-dark">{{ $ref->referredUser->name ?? 'User Anonim' }}</td>
                                                                    <td class="text-muted small">{{ $ref->created_at?->format('d M Y') ?? '-' }}</td>
                                                                    <td>
                                                                        @if(strtolower($ref->status) == 'paid')
                                                                            <span class="badge bg-success bg-opacity-10 text-success rounded-1">Paid</span>
                                                                        @elseif(strtolower($ref->status) == 'rejected')
                                                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-1">Rejected</span>
                                                                        @else
                                                                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-1">Pending</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-end pe-3 fw-bold {{ strtolower($ref->status) == 'rejected' ? 'text-decoration-line-through text-danger opacity-75' : 'text-success' }}">
                                                                        Rp {{ number_format($ref->amount, 0, ',', '.') }}
                                                                    </td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="4" class="text-center text-muted py-4">Belum ada pengguna yang memakai kode ini.</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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

        // 3. Initialize Chart (Dummy Data)
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