@extends('layouts.admin')

@section('title', 'Manage Finance')

@section('navbar')
    @include('partials.navbar-finance')
@endsection

@section('styles')
<!-- Google Fonts: Inter/Roboto Style -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --ids-primary: #FFB703;
        --ids-secondary: #FB8500;
        --ids-bg: #F8F9FA;
        --ids-card-bg: #FFFFFF;
        --ids-text-main: #1A1D1F;
        --ids-text-muted: #6F767E;
        --ids-border: #EFEFEF;
    }

    body {
        background-color: var(--ids-bg) !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Override standard container to allow sidebar feel */
    .finance-wrapper {
        display: flex;
        min-height: calc(100vh - 100px);
        margin: 0 -12px; /* Pull out of standard container padding */
    }

    /* Sidebar-like Nav */
    .finance-sidebar {
        width: 240px;
        background: #fff;
        padding: 24px;
        border-right: 1px solid var(--ids-border);
        display: none; /* Hide on small screens */
    }

    @media (min-width: 992px) {
        .finance-sidebar { display: block; }
    }

    .nav-menu-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--ids-text-muted);
        letter-spacing: 1px;
        margin-bottom: 16px;
        display: block;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: var(--ids-text-main);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 4px;
        transition: all 0.2s;
    }

    .sidebar-link i {
        font-size: 1.2rem;
        margin-right: 12px;
        color: var(--ids-text-muted);
    }

    .sidebar-link:hover {
        background: #F4F4F4;
        color: var(--ids-text-main);
    }

    .sidebar-link.active {
        background: #FEF6E6;
        color: var(--ids-text-main);
    }

    .sidebar-link.active i {
        color: var(--ids-secondary);
    }

    .badge-notif {
        background: #D93F3F;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        margin-left: auto;
    }

    /* Main Content */
    .finance-main {
        flex: 1;
        padding: 24px;
    }

    /* Cards Styling */
    .stat-card {
        background: #fff;
        border: 1px solid var(--ids-border);
        border-radius: 20px;
        padding: 24px;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .stat-card.urgent {
        background: #FFF7E6;
        border-color: #FFE5B2;
    }

    .icon-square {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: 1px solid var(--ids-border);
        margin-bottom: 20px;
    }

    .stat-card.urgent .icon-square {
        border-color: #FFE5B2;
        color: #FB8500;
    }

    .stat-label {
        font-size: 0.9rem;
        color: var(--ids-text-muted);
        margin-bottom: 4px;
        font-weight: 500;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--ids-text-main);
        margin-bottom: 8px;
    }

    .urgent-badge {
        background: #FB8500;
        color: #fff;
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
    }

    /* Buttons */
    .btn-action {
        background: var(--ids-primary);
        color: var(--ids-text-main);
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        width: 100%;
        margin-top: 12px;
        transition: all 0.2s;
    }

    .btn-action:hover {
        background: var(--ids-secondary);
        color: #fff;
    }

    /* Performer Row */
    .performer-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--ids-border);
    }

    .performer-item:last-child { border-bottom: none; }

    .avatar-circle-sm {
        width: 40px;
        height: 40px;
        background: #F4F4F4;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: var(--ids-text-muted);
        margin-right: 12px;
    }

    .rank-badge {
        background: #F4F4F4;
        border-radius: 4px;
        padding: 2px 8px;
        font-size: 12px;
        font-weight: 700;
        margin-left: auto;
    }

    .rank-1 { background: #FFF7E6; color: #FB8500; }
    .rank-2 { background: #F1F3F5; color: #495057; }

    /* Chart Overrides */
    /* Glassmorphism Header */
    .finance-hero {
        background: linear-gradient(135deg, #1A1D1F 0%, #33383C 100%);
        border-radius: 24px;
        padding: 32px;
        color: #fff;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .finance-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(251, 133, 0, 0.15) 0%, rgba(251, 133, 0, 0) 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .hero-label {
        background: rgba(251, 133, 0, 0.2);
        color: #FFB703;
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        display: inline-block;
        margin-bottom: 16px;
        border: 1px solid rgba(251, 133, 0, 0.3);
    }

    .hero-title {
        font-size: 2.25rem;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .hero-subtitle {
        color: rgba(255, 255, 255, 0.6);
        max-width: 500px;
        line-height: 1.6;
        font-weight: 400;
        margin-bottom: 0;
    }

    .calendar-badge {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px 20px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .calendar-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFB703;
    }

    .calendar-text {
        display: flex;
        flex-direction: column;
    }

    .date-label {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.5);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .date-value {
        font-size: 14px;
        font-weight: 700;
        color: #fff;
    }
</style>

@endsection

@section('content')
<div class="finance-wrapper" style="margin-top: 0;">
    <!-- Sidebar -->
    @include('partials.finance-sidebar')

    <!-- Main Content -->
    <main class="finance-main">
        <!-- Premium Hero Header -->
        <div class="finance-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="z-2">
                <span class="hero-label">Financial Center</span>
                <h1 class="hero-title">Keuangan & Payout</h1>
                <p class="hero-subtitle">Kelola seluruh arus kas platform, pendapatan kotor, hingga verifikasi komisi reseller dalam satu dashboard terpadu.</p>
            </div>
            <div class="z-2 mt-4 mt-md-0">
                <div class="calendar-badge shadow-lg">
                    <div class="calendar-icon">
                        <i class="bi bi-calendar-check-fill fs-5"></i>
                    </div>
                    <div class="calendar-text">
                        <span class="date-label">Laporan Per Hari Ini</span>
                        <span class="date-value">{{ now()->format('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Stat Cards -->
        <div class="row g-4 mb-4">
            <!-- Payout Requests -->
            <div class="col-md-4">
                <div class="stat-card urgent">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="icon-square">
                            <i class="bi bi-hourglass-split fs-4"></i>
                        </div>
                        @if($pendingWithdrawals > 0)
                            <span class="urgent-badge">Urgent</span>
                        @endif
                    </div>
                    <div class="stat-value">{{ $pendingWithdrawals }} Request</div>
                    <div class="stat-label">Permintaan penarikan pending.</div>
                    <a href="{{ route('admin.withdrawals.index') }}" class="btn btn-action">Proses Sekarang</a>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="icon-square">
                        <i class="bi bi-cash-stack fs-4 text-success"></i>
                    </div>
                    <div class="stat-value">Rp {{ number_format($totalOmzet / 1000000, 1, ',', '.') }}jt</div>
                    <div class="stat-label">Total omzet bruto platform.</div>
                    <div class="mt-3 p-2 bg-light rounded-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Event:</span>
                            <span class="fw-bold">Rp {{ number_format($eventRevenue / 1000, 0, ',', '.') }}rb</span>
                        </div>
                        <div class="d-flex justify-content-between small">
                            <span class="text-muted">Course:</span>
                            <span class="fw-bold">Rp {{ number_format($courseRevenue / 1000, 0, ',', '.') }}rb</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Net Profit Card -->
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="icon-square">
                            <i class="bi bi-piggy-bank fs-4 text-primary"></i>
                        </div>
                    </div>
                    <div class="stat-value">Rp {{ number_format($pendapatanBersih / 1000000, 1, ',', '.') }}jt</div>
                    <div class="stat-label">Estimasi pendapatan bersih platform.</div>
                    <div class="mt-3 small text-muted">Setelah dikurangi komisi reseller.</div>
                </div>
            </div>
        </div>

        <!-- Activity Overview (Event & Course tracking) -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card" style="padding: 16px; border-style: dashed;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-square mb-0" style="width: 40px; height: 40px; background: #E6F0FF; color: #0066FF; border: none;">
                            <i class="bi bi-calendar-event fs-5"></i>
                        </div>
                        <div>
                            <div class="stat-label mb-0" style="font-size: 11px;">Event Terjual</div>
                            <div class="fw-bold" style="font-size: 16px;">{{ $eventSettledCount }} item</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="padding: 16px; border-style: dashed;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-square mb-0" style="width: 40px; height: 40px; background: #FFF0E6; color: #FF6600; border: none;">
                            <i class="bi bi-book fs-5"></i>
                        </div>
                        <div>
                            <div class="stat-label mb-0" style="font-size: 11px;">Course Terjual</div>
                            <div class="fw-bold" style="font-size: 16px;">{{ $courseSettledCount }} item</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="padding: 16px; border-style: dashed;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-square mb-0" style="width: 40px; height: 40px; background: #E6FFFA; color: #00BFA5; border: none;">
                            <i class="bi bi-gift fs-5"></i>
                        </div>
                        <div>
                            <div class="stat-label mb-0" style="font-size: 11px;">Free Access</div>
                            <div class="fw-bold" style="font-size: 16px;">{{ $freeCount }} user</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="padding: 16px; border-style: dashed;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-square mb-0" style="width: 40px; height: 40px; background: #F3E5F5; color: #9C27B0; border: none;">
                            <i class="bi bi-credit-card fs-5"></i>
                        </div>
                        <div>
                            <div class="stat-label mb-0" style="font-size: 11px;">Paid Access</div>
                            <div class="fw-bold" style="font-size: 16px;">{{ $paidCount }} user</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Revenue Trend Chart -->
            <div class="col-lg-6">
                <div class="stat-card">
                    <h5 class="card-title">Tren Pendapatan Bulanan</h5>
                    <div style="height: 250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Category Distribution Plate -->
            <div class="col-lg-3">
                <div class="stat-card text-center d-flex flex-column justify-content-center">
                    <h5 class="card-title mb-4">Proporsi Revenue</h5>
                    <div style="height: 180px; position: relative;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                    <div class="mt-3 small text-muted">
                        Split Pendapatan Event vs Course
                    </div>
                </div>
            </div>

            <!-- Top Performers / Top Earners -->
            <div class="col-lg-3">
                <div class="stat-card">
                    <h5 class="card-title">Top Affiliates</h5>
                    <div class="performer-list mt-3">
                        @forelse($topPerformers as $index => $performer)
                            <div class="performer-item" style="padding: 8px 0;">
                                <div class="avatar-circle-sm" style="width: 32px; height: 32px; font-size: 11px;">
                                    {{ strtoupper(substr($performer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="fw-bold" style="font-size: 11px;">{{ $performer->name }}</div>
                                    <div class="text-success" style="font-size: 10px;">Rp {{ number_format($performer->total_commission / 1000, 0, ',', '.') }}k</div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted small italic">Belum ada data.</p>
                        @endforelse
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-light w-100 rounded-pill small fw-bold py-1" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="bi bi-download me-1"></i> Ekspor
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title mb-0">Transaksi Terakhir</h5>
                        <a href="#" class="text-decoration-none small fw-bold text-primary">Lihat Semua</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-top">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="py-3 border-0">ID Order</th>
                                    <th class="py-3 border-0">Pelanggan</th>
                                    <th class="py-3 border-0">Metode</th>
                                    <th class="py-3 border-0">Nominal</th>
                                    <th class="py-3 border-0 text-center">Invoice</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $tx)
                                <tr>
                                    <td class="py-3">
                                        <code>{{ $tx['id'] }}</code>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $tx['user'] }}</div>
                                        <div class="small text-muted">{{ $tx['date']->format('d M Y, H:i') }}</div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">{{ $tx['source'] ?? 'General' }}</div>
                                        <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3 mt-1">
                                            Transfer {{ $tx['type'] }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">Rp {{ number_format($tx['amount'], 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <a href="{{ $tx['url'] }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted small">Belum ada transaksi sukses.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const lineCtx = document.getElementById('revenueChart').getContext('2d');
        
        // Custom gradient for the chart
        const gradient = lineCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(56, 116, 255, 0.2)');
        gradient.addColorStop(1, 'rgba(56, 116, 255, 0)');

        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: @json(collect($monthlyRevenue)->pluck('month')),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json(collect($monthlyRevenue)->pluck('revenue')),
                    borderColor: '#3874FF',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3874FF',
                    pointHoverRadius: 6,
                    pointRadius: 4,
                    fill: true,
                    backgroundColor: gradient,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#F1F3F5',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value/1000000).toFixed(1) + 'jt';
                                if (value >= 1000) return 'Rp ' + (value/1000).toFixed(0) + 'rb';
                                return 'Rp ' + value;
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, weight: 'bold' } }
                    }
                }
            }
        });

        // Category breakdown chart
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: ['Event', 'Course'],
                datasets: [{
                    data: [{{ $eventRevenue }}, {{ $courseRevenue }}],
                    backgroundColor: ['#0066FF', '#FF6600'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="exportModalLabel">Ekspor Laporan Keuangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.finance.export') }}" method="GET" target="_blank">
                <div class="modal-body p-4">
                    <p class="text-muted small mb-4">Pilih periode dan format laporan yang Anda butuhkan untuk proses pembukuan.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Periode Laporan</label>
                        <select name="period" id="periodSelect" class="form-select rounded-3 border-light-subtle py-2">
                            <option value="this_week">Minggu Ini</option>
                            <option value="this_month" selected>Bulan Ini</option>
                            <option value="custom">Custom Range</option>
                        </select>
                    </div>

                    <div id="customDateRange" class="mb-3 d-none">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small">Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control rounded-3 border-light-subtle">
                            </div>
                            <div class="col-6">
                                <label class="form-label small">Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control rounded-3 border-light-subtle">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Format File</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="formatPdf" value="pdf" checked>
                                <label class="form-check-label small" for="formatPdf">
                                    <i class="bi bi-file-pdf text-danger me-1"></i> PDF Document
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="format" id="formatExcel" value="excel">
                                <label class="form-check-label small" for="formatExcel">
                                    <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel/CSV
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 rounded-3 mb-0 d-flex align-items-start gap-3">
                        <i class="bi bi-info-circle-fill"></i>
                        <div class="small">Laporan mencakup seluruh transaksi transfer manual dan komisi reseller pada periode terpilih.</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Unduh Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodSelect = document.getElementById('periodSelect');
        const customRange = document.getElementById('customDateRange');

        if (periodSelect) {
            periodSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    customRange.classList.remove('d-none');
                } else {
                    customRange.classList.add('d-none');
                }
            });
        }
    });
</script>

