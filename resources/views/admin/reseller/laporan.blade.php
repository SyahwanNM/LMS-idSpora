<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Performa Reseller - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-dark: #4c1d95;
            --primary-light: #8b5cf6;
            --primary-subtle: #f3e8ff;
            --bg-surface: #ffffff;
            --text-main: #1e1b4b;
            --text-muted: #64748b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8FAFC;
        }

        .text-primary-dark {
            color: var(--primary-dark) !important;
        }

        .btn-primary-custom {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            color: #ffffff !important;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #3b1673 !important;
            border-color: #3b1673 !important;
        }

        .btn-outline-primary-custom {
            color: var(--primary-dark) !important;
            border: 1px solid var(--primary-dark) !important;
            background-color: transparent !important;
            transition: all 0.2s ease;
        }

        .btn-outline-primary-custom:hover {
            color: #ffffff !important;
            background-color: var(--primary-dark) !important;
        }

        .hover-card-up {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, border-color 0.3s ease;
            border-radius: 16px !important;
        }
        .hover-card-up:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06) !important;
        }
    </style>
</head>
<body>
    @include('partials.navbar-reseller')

    <main class="main-content min-vh-100">
        <div class="p-4 p-md-5">

            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1 text-dark fs-2">Laporan Performa</h2>
                    <p class="text-secondary mb-0">Analisis dan ekspor pertumbuhan mitra reseller IdSpora.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.reseller.export.pdf', ['type' => 'referrals', 'range' => $range ?? '6_months']) }}" target="_blank" class="btn btn-primary-custom rounded-3 px-4 shadow-sm d-flex align-items-center gap-2" style="height: 44px;">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF Penjualan
                    </a>
                    <a href="{{ route('admin.reseller.export.pdf', ['type' => 'withdrawals', 'range' => $range ?? '6_months']) }}" target="_blank" class="btn btn-outline-primary-custom rounded-3 px-4 shadow-sm d-flex align-items-center gap-2" style="height: 44px;">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF Penarikan
                    </a>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <!-- Card 1: Rata-rata Komisi -->
                <div class="col-12 col-md-4">
                    <div class="card h-100 shadow-sm border-0 hover-card-up bg-white" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-3" style="gap: 10px;">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-success bg-opacity-10 text-success" style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <i class="bi bi-cash-stack fs-5"></i>
                                </div>
                                <div class="lh-sm">
                                    <h6 class="text-muted small fw-medium mb-0">Rata-rata Komisi</h6>
                                    <h5 class="fw-semibold mb-0 text-dark">Rp 128.500</h5>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Pendaftaran Reseller -->
                <div class="col-12 col-md-4">
                    <div class="card h-100 shadow-sm border-0 hover-card-up bg-white" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-3" style="gap: 10px;">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-warning bg-opacity-10 text-warning" style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <i class="bi bi-person-plus-fill fs-5"></i>
                                </div>
                                <div class="lh-sm">
                                    <h6 class="text-muted small fw-medium mb-0">Total Pendaftaran</h6>
                                    <h5 class="fw-semibold mb-0 text-dark">{{ $totalResellers }} Mitra</h5>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tingkat Konversi -->
                <div class="col-12 col-md-4">
                    <div class="card h-100 shadow-sm border-0 hover-card-up bg-white" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div class="d-flex align-items-center mb-3" style="gap: 10px;">
                                <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger bg-opacity-10 text-danger" style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <i class="bi bi-percent fs-5"></i>
                                </div>
                                <div class="lh-sm">
                                    <h6 class="text-muted small fw-medium mb-0">Tingkat Konversi</h6>
                                    <h5 class="fw-semibold mb-0 text-dark">8.4%</h5>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-4 gap-2">
                            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 1.15rem;">
                                <i class="bi bi-bar-chart-line-fill text-primary-dark"></i> Grafik Penjualan Referral
                            </h5>
                            <div>
                                <select class="form-select form-select-sm text-secondary bg-light border-light-subtle rounded-3" onchange="location = this.value;" style="width: auto; font-size: 0.85rem; font-weight: 500; height: 36px; padding-right: 30px;">
                                    <option value="{{ route('admin.reseller.laporan', ['range' => '6_months']) }}" {{ ($range ?? '6_months') === '6_months' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                                    <option value="{{ route('admin.reseller.laporan', ['range' => '30_days']) }}" {{ ($range ?? '6_months') === '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                                    <option value="{{ route('admin.reseller.laporan', ['range' => '1_year']) }}" {{ ($range ?? '6_months') === '1_year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                                </select>
                            </div>
                        </div>
                        <div style="position: relative; height: 280px; width: 100%;">
                            <canvas id="reportingChart"></canvas>
                        </div>
                        
                        <!-- Stats at the bottom of chart card -->
                        <div class="row g-3 mt-3 justify-content-center">
                            <!-- Stat 1: Total Komisi -->
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center p-3 border border-light-subtle rounded-3 bg-light bg-opacity-25 h-100">
                                    <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3 me-3" style="width: 44px; height: 44px; flex-shrink: 0;">
                                        <i class="bi bi-wallet2 fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-secondary d-block" style="font-size: 0.75rem; font-weight: 500; line-height: 1.2;">{{ $labelTotalKomisi ?? 'Total Komisi' }}</small>
                                        <span class="fw-bold text-dark" style="font-size: 1.05rem;">Rp {{ number_format($totalKomisi6BulanVal, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Stat 2: Periode Terbaik -->
                            <div class="col-12 col-md-6">
                                <div class="d-flex align-items-center p-3 border border-light-subtle rounded-3 bg-light bg-opacity-25 h-100">
                                    <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3 me-3" style="width: 44px; height: 44px; flex-shrink: 0;">
                                        <i class="bi bi-calendar-check fs-5"></i>
                                    </div>
                                    <div>
                                        <small class="text-secondary d-block" style="font-size: 0.75rem; font-weight: 500; line-height: 1.2;">{{ $labelBestPeriod ?? 'Bulan Terbaik' }}</small>
                                        <span class="fw-bold text-dark" style="font-size: 1.05rem;">{{ $bestMonthVal }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2" style="font-size: 1.15rem;">
                                    <i class="bi bi-layers-half text-primary-dark"></i> Distribusi Level
                                </h5>
                                <i class="bi bi-info-circle text-muted" style="cursor: pointer;" data-bs-toggle="tooltip" title="Distribusi level reseller saat ini"></i>
                            </div>

                            <div class="d-flex flex-column gap-3 mt-3">
                                <!-- Bronze -->
                                <div class="border border-light-subtle rounded-3 p-3 bg-white">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-warning-emphasis bg-warning bg-opacity-10" style="width: 36px; height: 36px; color: #b45309 !important; background-color: rgba(180, 83, 9, 0.1) !important;">
                                                <i class="bi bi-shield-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">Bronze</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $bronzeCount }} Reseller</small>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-warning-emphasis" style="color: #b45309 !important; font-size: 1.1rem;">{{ $bronzePercent }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $bronzePercent }}%; background-color: #b45309 !important;" aria-valuenow="{{ $bronzePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                
                                <!-- Silver -->
                                <div class="border border-light-subtle rounded-3 p-3 bg-white">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-secondary bg-secondary bg-opacity-10" style="width: 36px; height: 36px;">
                                                <i class="bi bi-shield-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">Silver</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $silverCount }} Reseller</small>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-secondary" style="font-size: 1.1rem;">{{ $silverPercent }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                        <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $silverPercent }}%;" aria-valuenow="{{ $silverPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                
                                <!-- Gold -->
                                <div class="border border-light-subtle rounded-3 p-3 bg-white">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="d-flex align-items-center justify-content-center rounded-circle text-warning bg-warning bg-opacity-10" style="width: 36px; height: 36px; color: #d97706 !important; background-color: rgba(217, 119, 6, 0.1) !important;">
                                                <i class="bi bi-shield-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-dark d-block" style="font-size: 0.9rem;">Gold</span>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $goldCount }} Reseller</small>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-warning" style="color: #d97706 !important; font-size: 1.1rem;">{{ $goldPercent }}%</span>
                                    </div>
                                    <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $goldPercent }}%; background-color: #d97706 !important;" aria-valuenow="{{ $goldPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total and Update footer -->
                        <div class="row g-3 mt-auto border-top">
                            <div class="col-12">
                                <div class="d-flex align-items-center p-2 border border-light-subtle rounded-3 bg-light bg-opacity-25 h-100">
                                    <div class="d-flex align-items-center justify-content-center bg-primary-subtle text-primary-dark rounded-circle me-2" style="width: 32px; height: 32px; flex-shrink: 0; background-color: var(--primary-subtle) !important;">
                                        <i class="bi bi-clock-fill" style="color: var(--primary-dark);"></i>
                                    </div>
                                    <div>
                                        <small class="text-secondary">Update Terakhir</small>
                                        <div class="fw-medium text-dark" style="font-size: 12px;">{{ now()->translatedFormat('d M Y, H:i') }} WIB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Notification Tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Load reporting chart
        const ctxReport = document.getElementById('reportingChart');
        if(ctxReport) {
            const ctxReport2d = ctxReport.getContext('2d');
            const gradientReport = ctxReport2d.createLinearGradient(0, 0, 0, 300);
            gradientReport.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); // Emerald-500
            gradientReport.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctxReport, {
                type: 'line',
                data: {
                    labels: @json($chartLabels ?? []),
                    datasets: [{ 
                        label: 'Total Komisi (Rp)', 
                        data: @json($chartValues ?? []), 
                        borderColor: '#10b981', 
                        backgroundColor: gradientReport, 
                        fill: true, 
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#047857',
                        pointHoverRadius: 7
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { 
                        legend: { 
                            display: true,
                            position: 'top',
                            align: 'center',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 6,
                                pointStyle: 'circle',
                                font: {
                                    size: 11,
                                    weight: '500'
                                }
                            }
                        } 
                    }, 
                    scales: { 
                        y: { 
                            grid: { color: 'rgba(226, 232, 240, 0.5)' },
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }, 
                        x: { 
                            grid: { display: false },
                            ticks: { maxTicksLimit: 12 }
                        } 
                    } 
                }
            });
        }
    </script>
</body>
</html>
