<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Reseller</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>.hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; } .hover-card:hover { transform: translateY(-4px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important; }</style>
</head>
<body>
    @include('partials.navbar-reseller')

    <main class="main-content min-vh-100">
        <div class="p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-end mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Dashboard Reseller</h2>
                    <p class="text-secondary mb-0">Overview performa reseller sistem.</p>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-card bg-primary bg-opacity-10 border border-primary">
                        <div class="card-body p-4">
                            <div class="bg-white text-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $activeResellersCount }} User</h3>
                            <p class="text-muted small mb-0">Total Reseller Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                        <div class="card-body p-4">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                                <i class="bi bi-cart-check-fill fs-4"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $totalSalesCount }} Sales</h3>
                            <p class="text-muted small mb-0">Konversi Berhasil (Paid)</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 hover-card">
                        <div class="card-body p-4">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 48px; height: 48px;">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                            <h3 class="fw-bold mb-1">{{ $totalPendingReferrals }} Pending</h3>
                            <p class="text-muted small mb-0">Transaksi Menunggu Verifikasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                        <h6 class="fw-bold mb-4">Tren Penjualan dari Referral</h6>
                        <canvas id="dashboardChart" style="max-height: 280px;"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-header bg-white border-bottom p-4">
                            <h6 class="fw-bold mb-0">Top 5 Performers <i class="bi bi-trophy-fill text-warning ms-1"></i></h6>
                        </div>
                        <div class="list-group list-group-flush p-2">
                            @forelse($topResellers as $index => $reseller)
                            <div class="list-group-item border-0 d-flex align-items-center gap-3 rounded-3 mb-1 {{ $index == 0 ? 'bg-warning bg-opacity-10' : '' }}">
                                <div class="bg-white border rounded-circle fw-bold d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; color: #B45309;">
                                    {{ strtoupper(substr($reseller->name, 0, 2)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold small text-dark">{{ $reseller->name }}</h6>
                                    <small class="text-success fw-medium">Rp {{ number_format($reseller->total_earned / 1000, 0, ',', '.') }}k</small>
                                </div>
                                <span class="badge {{ $index == 0 ? 'bg-warning text-dark' : 'bg-light text-muted' }} rounded-pill">#{{ $index + 1 }}</span>
                            </div>
                            @empty
                                <div class="text-center text-muted small py-4">Belum ada data performer.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ctx = document.getElementById('dashboardChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels),
                    datasets: [{ 
                        label: 'Transaksi Sukses', // <--- Udah diganti jadi transaksi sukses
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
                        y: { 
                            beginAtZero: true, 
                            ticks: { precision: 0 } // Biar angkanya genap (nggak ada koma kayak 1.5 transaksi)
                        }, 
                        x: { 
                            grid: { display: false },
                            ticks: { maxTicksLimit: 12 } // <--- Ganti jadi 12 biar semua bulan tampil
                        } 
                    } 
                }
            });
        }
    </script>
</body>
</html>