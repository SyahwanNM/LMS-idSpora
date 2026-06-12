<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Reseller - Admin Panel</title>
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


        



        .hover-card {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
            border-radius: 16px !important;
        }

        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(76, 29, 149, 0.08) !important;
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        .text-primary-dark {
            color: var(--primary-dark) !important;
        }

        .bg-primary-subtle-custom {
            background-color: var(--primary-subtle) !important;
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

        /* Timeline style */
        .activity-timeline {
            position: relative;
            padding-left: 24px;
            border-left: 2px solid #e2e8f0;
        }

        .activity-item {
            position: relative;
            margin-bottom: 20px;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: -31px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--primary-dark);
            border: 2px solid #fff;
        }

        .activity-item.success::before {
            background-color: #10b981;
        }

        .activity-item.warning::before {
            background-color: #f59e0b;
        }

        /* Subtle transition & hover animations */
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

    @php
        $tab = request('tab', 'dashboard');
        
        // Helper queries for layout
        $totalResellers = \App\Models\User::whereNotNull('referral_code')->count();
        $activeResellers = \App\Models\User::whereNotNull('referral_code')->has('referrals')->count();
        $activeResellerProducts = \App\Models\Course::where('is_reseller_course', 1)->count() + \App\Models\Event::where('is_reseller_event', 1)->count();
        
        // Top performer reseller
        $topProducts = \App\Models\Referral::where('status', 'paid')
            ->select('description', \DB::raw('count(*) as sales_count'))
            ->groupBy('description')
            ->orderByDesc('sales_count')
            ->take(5)
            ->get();

        foreach ($topProducts as $item) {
            $thumbnail = null;
            $productName = $item->description;
            $categoryName = 'Produk Reseller';

            if (str_starts_with($productName, 'Komisi Course: ')) {
                $productName = substr($productName, strlen('Komisi Course: '));
                $categoryName = 'Kursus';
                $course = \App\Models\Course::where('name', $productName)->first();
                if ($course) {
                    $courseThumb = (string) ($course->card_thumbnail ?? '');
                    $thumbnail = $courseThumb !== ''
                        ? (str_starts_with($courseThumb, 'http://') || str_starts_with($courseThumb, 'https://')
                            ? $courseThumb
                            : asset('uploads/' . ltrim(str_replace('storage/', '', $courseThumb), '/')))
                        : asset('aset/poster.png');
                }
            } elseif (str_starts_with($productName, 'Komisi Event: ')) {
                $productName = substr($productName, strlen('Komisi Event: '));
                $categoryName = 'Event';
                $event = \App\Models\Event::where('title', $productName)->first();
                if ($event) {
                    $eventImage = (string) ($event->image ?? '');
                    $thumbnail = $eventImage !== ''
                        ? (str_starts_with($eventImage, 'http://') || str_starts_with($eventImage, 'https://')
                            ? $eventImage
                            : asset('uploads/' . ltrim(str_replace('storage/', '', $eventImage), '/')))
                        : asset('aset/poster.png');
                }
            } else {
                $course = \App\Models\Course::where('name', $productName)->first();
                if ($course) {
                    $categoryName = 'Kursus';
                    $courseThumb = (string) ($course->card_thumbnail ?? '');
                    $thumbnail = $courseThumb !== ''
                        ? (str_starts_with($courseThumb, 'http://') || str_starts_with($courseThumb, 'https://')
                            ? $courseThumb
                            : asset('uploads/' . ltrim(str_replace('storage/', '', $courseThumb), '/')))
                        : asset('aset/poster.png');
                } else {
                    $event = \App\Models\Event::where('title', $productName)->first();
                    if ($event) {
                        $categoryName = 'Event';
                        $eventImage = (string) ($event->image ?? '');
                        $thumbnail = $eventImage !== ''
                            ? (str_starts_with($eventImage, 'http://') || str_starts_with($eventImage, 'https://')
                                ? $eventImage
                                : asset('uploads/' . ltrim(str_replace('storage/', '', $eventImage), '/')))
                            : asset('aset/poster.png');
                    }
                }
            }

            $item->product_display_name = $productName;
            $item->category_name = $categoryName;
            $item->thumbnail = $thumbnail ?? asset('aset/poster.png');
        }
            
        // Reseller status distribution count (2 status: Aktif & Suspend)
        $statusActiveCount = \App\Models\User::whereNotNull('referral_code')->where('reseller_status', 'active')->count();
        $statusSuspendedCount = \App\Models\User::whereNotNull('referral_code')->where('reseller_status', 'suspended')->count();
        $totalStatus = max(1, $statusActiveCount + $statusSuspendedCount);

        // Count reseller levels dynamically
        $resellersWithCount = \App\Models\User::whereNotNull('referral_code')
            ->withCount('referrals')
            ->get();
        
        $bronzeCount = 0;
        $silverCount = 0;
        $goldCount = 0;

        foreach ($resellersWithCount as $r) {
            $c = $r->referrals_count ?? 0;
            if ($c >= 151) {
                $goldCount++;
            } elseif ($c >= 51) {
                $silverCount++;
            } else {
                $bronzeCount++;
            }
        }

        $bronzePercent = $totalResellers > 0 ? round(($bronzeCount / $totalResellers) * 100) : 0;
        $silverPercent = $totalResellers > 0 ? round(($silverCount / $totalResellers) * 100) : 0;
        $goldPercent = $totalResellers > 0 ? round(($goldCount / $totalResellers) * 100) : 0;

        // Total komisi 6 bulan terakhir
        $sixMonthsAgo = now()->subMonths(6);
        $totalKomisi6Bulan = \App\Models\Referral::where('status', 'paid')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->sum('amount');

        // Komisi bulanan tertinggi dan bulan terbaik dalam 6 bulan terakhir
        $monthlyCommissions = \App\Models\Referral::where('status', 'paid')
            ->where('created_at', '>=', $sixMonthsAgo)
            ->select(
                \DB::raw('SUM(amount) as total_amount'),
                \DB::raw('DATE_FORMAT(created_at, "%M %Y") as month_year')
            )
            ->groupBy('month_year')
            ->orderByDesc('total_amount')
            ->get();

        $highestKomisi = 0;
        $highestKomisiMonth = '-';
        $bestMonth = '-';

        if ($monthlyCommissions->count() > 0) {
            $highestKomisi = $monthlyCommissions->first()->total_amount;
            $highestKomisiMonth = $monthlyCommissions->first()->month_year;
            $bestMonth = $monthlyCommissions->first()->month_year;
        }

        $totalKomisi6BulanVal = $totalKomisi6Bulan > 0 ? $totalKomisi6Bulan : 617000;
        $highestKomisiVal = $highestKomisi > 0 ? $highestKomisi : 450000;
        $highestKomisiMonthVal = $highestKomisiMonth !== '-' ? $highestKomisiMonth : 'Juni 2026';
        $bestMonthVal = $bestMonth !== '-' ? $bestMonth : 'Juni 2026';
    @endphp

    <main class="main-content min-vh-100">
        <div class="p-4 p-md-5">
            
            <!-- Tab: Dashboard -->
                <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
                    <div>
                        <h2 class="fw-bold mb-1 text-dark fs-2">Dashboard Reseller</h2>
                        <p class="text-secondary mb-0" style="font-size: 1.05rem;">Kelola program reseller dengan mudah dan pantau performa secara real-time.</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-primary-custom dropdown-toggle rounded-pill px-4 fw-bold shadow-sm" type="button" data-bs-toggle="dropdown" style="height: 44px; display: flex; align-items: center; gap: 8px;">
                            <i class="bi bi-download"></i> Export Laporan
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel</a></li>
                            <li><a class="dropdown-item py-2" href="#"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                        </ul>
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row g-4 mb-4">
                    <!-- Card 1: Total Reseller -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 shadow-sm border-0 hover-card-up bg-white" style="border-radius: 16px;">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="background-color: var(--primary-subtle); color: var(--primary-dark); width: 44px; height: 44px; flex-shrink: 0;">
                                        <i class="bi bi-people-fill fs-5"></i>
                                    </div>
                                    <div class="lh-sm">
                                        <h6 class="text-muted small fw-medium mb-0">Total Reseller</h6>
                                        <h5 class="fw-semibold mb-0 text-dark">{{ $totalResellers }}</h5>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <span class="text-success small fw-medium">
                                        <i class="bi bi-arrow-up"></i> 22% <span class="text-muted fw-normal">dari bulan lalu</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Reseller Aktif -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 shadow-sm border-0 hover-card-up bg-white" style="border-radius: 16px;">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="background-color: var(--primary-subtle); color: var(--primary-dark); width: 44px; height: 44px; flex-shrink: 0;">
                                        <i class="bi bi-person-check-fill fs-5"></i>
                                    </div>
                                    <div class="lh-sm">
                                        <h6 class="text-muted small fw-medium mb-0" style="font-size: 0.8rem;">Reseller Aktif</h6>
                                        <h5 class="fw-semibold mb-0 text-dark">{{ $activeResellers }}</h5>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <span class="text-success small fw-medium">
                                        <i class="bi bi-arrow-up"></i> 20% <span class="text-muted fw-normal">dari bulan lalu</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Penjualan Referral -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 shadow-sm border-0 hover-card-up bg-white" style="border-radius: 16px;">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="background-color: var(--primary-subtle); color: var(--primary-dark); width: 44px; height: 44px; flex-shrink: 0;">
                                        <i class="bi bi-cart-fill fs-5"></i>
                                    </div>
                                    <div class="lh-sm">
                                        <h6 class="text-muted small fw-medium mb-0" style="font-size: 0.8rem;">Penjualan Referral</h6>
                                        <h5 class="fw-semibold mb-0 text-dark">{{ $totalSalesCount ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <span class="text-success small fw-medium">
                                        <i class="bi bi-arrow-up"></i> 33% <span class="text-muted fw-normal">dari bulan lalu</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4: Produk Reseller Aktif -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card h-100 shadow-sm border-0 hover-card-up bg-white" style="border-radius: 16px;">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div class="d-flex align-items-center" style="gap: 10px;">
                                    <div class="d-flex align-items-center justify-content-center rounded-3" style="background-color: var(--primary-subtle); color: var(--primary-dark); width: 44px; height: 44px; flex-shrink: 0;">
                                        <i class="bi bi-journal-bookmark-fill fs-5"></i>
                                    </div>
                                    <div class="lh-sm">
                                        <h6 class="text-muted small fw-medium mb-0" style="font-size: 0.8rem;">Produk Reseller Aktif</h6>
                                        <h5 class="fw-semibold mb-0 text-dark">{{ $activeResellerProducts }}</h5>
                                    </div>
                                </div>
                                <div class="mt-auto">
                                    <span class="text-secondary small fw-medium">
                                        — 0% <span class="text-muted fw-normal">dari bulan lalu</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Graphs & Top lists -->
                <div class="row g-4">
                    <!-- Left column -->
                    <div class="col-lg-8">
                        <div class="d-flex flex-column gap-4">
                            <!-- Chart Card -->
                            <div class="card border-0 shadow-sm rounded-4 p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-activity"></i> Tren Penjualan Referral
                                    </h5>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="Time Filter">
                                        <button type="button" class="btn btn-outline-secondary active">7 Hari</button>
                                        <button type="button" class="btn btn-outline-secondary">30 Hari</button>
                                        <button type="button" class="btn btn-outline-secondary">3 Bulan</button>
                                        <button type="button" class="btn btn-outline-secondary">1 Tahun</button>
                                    </div>
                                </div>
                                <div style="position: relative; height: 320px; width: 100%;">
                                    <canvas id="dashboardChart"></canvas>
                                </div>
                            </div>

                            <!-- Top Products Card -->
                            <div class="card border-0 shadow-sm rounded-4 p-4">
                                <h5 class="fw-bold mb-4 text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-fire"></i> Produk Terlaris
                                </h5>
                                <div class="list-group list-group-flush">
                                    @forelse($topProducts as $index => $item)
                                        @php
                                            $rankNum = $index + 1;
                                        @endphp
                                        <div class="list-group-item px-0 py-3 border-light d-flex align-items-center justify-content-between bg-white border-0 mb-2 rounded-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="d-flex align-items-center justify-content-center bg-light text-secondary rounded fw-bold" style="width: 30px; height: 30px; font-size: 0.9rem;">
                                                    {{ $rankNum }}
                                                </div>
                                                 <img src="{{ $item->thumbnail }}" alt="{{ $item->product_display_name }}" class="rounded shadow-sm" style="width: 44px; height: 44px; object-fit: cover;">
                                                 <div>
                                                     <h6 class="mb-0 fw-bold text-dark small" style="font-size: 0.95rem;">{{ $item->product_display_name }}</h6>
                                                     <small class="text-muted" style="font-size: 0.75rem;">Kategori: {{ $item->category_name }}</small>
                                                 </div>
                                            </div>
                                            <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: var(--primary-subtle); color: var(--primary-dark); font-size: 0.75rem;">
                                                {{ $item->sales_count }} Terjual
                                            </span>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4 small">
                                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                            Belum ada produk terlaris.
                                        </div>
                                    @endforelse
                                </div>
                                <div class="text-center mt-3 border-top pt-3">
                                    <a href="{{ route('admin.reseller.dashboard', ['tab' => 'katalog']) }}" class="btn w-100 fw-bold text-primary-dark py-2 shadow-sm" style="font-size: 0.85rem; border: 1px solid var(--primary-dark); background-color: transparent;">
                                        Lihat Semua Produk <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right column -->
                    <div class="col-lg-4">
                        <div class="d-flex flex-column gap-4">
                            <!-- Top Performers Card -->
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-header bg-white border-0 p-4 pb-0">
                                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-trophy-fill"></i> Top Performer
                                    </h5>
                                </div>
                                <div class="list-group list-group-flush p-3">
                                    @forelse($topResellers ?? [] as $index => $reseller)
                                        @php
                                            $rankNum = $index + 1;
                                            $avatarInitials = strtoupper(substr($reseller->name, 0, 2));
                                            $earned = $reseller->total_earned ?? 0;
                                        @endphp
                                        <div class="list-group-item border-0 d-flex align-items-center justify-content-between rounded-3 mb-2 p-2 bg-white">
                                            <div class="d-flex align-items-center gap-3">
                                                <span style="width: 24px; text-align: center; display: inline-block;">
                                                    @if($rankNum == 1)
                                                        <i class="bi bi-trophy-fill text-warning"></i>
                                                    @elseif($rankNum == 2)
                                                        <i class="bi bi-trophy-fill text-secondary"></i>
                                                    @elseif($rankNum == 3)
                                                        <i class="bi bi-trophy-fill" style="color: #cd7f32;"></i>
                                                    @else
                                                        <span class="fw-semibold text-secondary" style="font-size: 0.9rem;">{{ $rankNum }}</span>
                                                    @endif
                                                </span>
                                                <img src="{{ $reseller->avatar_url }}" alt="{{ $reseller->name }}" class="rounded-circle shadow-sm" style="width: 38px; height: 38px; object-fit: cover; border: 1.5px solid #fff;">
                                                <span class="fw-bold text-dark small">{{ $reseller->name }}</span>
                                            </div>
                                            <span class="text-success fw-bold small">Rp {{ number_format($earned, 0, ',', '.') }}</span>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted small py-4">Belum ada data performer.</div>
                                    @endforelse
                                </div>
                                <div class="card-footer bg-white border-0 text-center pb-4 pt-0">
                                    <a href="{{ route('admin.reseller.data') }}" class="btn w-100 fw-bold text-primary-dark py-2 shadow-sm" style="font-size: 0.85rem; border: 1px solid var(--primary-dark); background-color: transparent;">
                                        Lihat Semua Reseller <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Status Reseller Card -->
                            <div class="card border-0 shadow-sm rounded-4 p-4">
                                <h5 class="fw-bold mb-4 text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-check-circle"></i> Status Keaktifan Reseller
                                </h5>
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <div style="position: relative; height: 110px; width: 100%;">
                                            <canvas id="statusChart"></canvas>
                                        </div>
                                    </div>
                                    <div class="col-6 ps-0">
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <span class="d-inline-block rounded-circle bg-success me-2" style="width: 8px; height: 8px;"></span>
                                                    <span class="small text-secondary fw-semibold">Aktif</span>
                                                </div>
                                                <span class="small fw-bold text-dark">{{ $statusActiveCount }} ({{ $totalStatus > 0 ? round(($statusActiveCount/$totalStatus)*100) : 0 }}%)</span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <span class="d-inline-block rounded-circle bg-danger me-2" style="width: 8px; height: 8px;"></span>
                                                    <span class="small text-secondary fw-semibold">Suspend</span>
                                                </div>
                                                <span class="small fw-bold text-dark">{{ $statusSuspendedCount }} ({{ $totalStatus > 0 ? round(($statusSuspendedCount/$totalStatus)*100) : 0 }}%)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center mt-3 border-top pt-3">
                                    <a href="{{ route('admin.reseller.data') }}" class="btn w-100 fw-bold text-primary-dark py-2 shadow-sm" style="font-size: 0.85rem; border: 1px solid var(--primary-dark); background-color: transparent;">
                                        Lihat Detail Status <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



        </div>
    </main>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="actionToast" class="toast align-items-center text-white border-0 bg-dark" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <span id="toastMessage">Tindakan berhasil diselesaikan!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Notification Tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Toast Helper
        function showToast(msg) {
            document.getElementById('toastMessage').innerText = msg;
            const toastEl = document.getElementById('actionToast');
            const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
            toast.show();
        }

        // Toggle reseller referral status dynamically in frontend
        function toggleRefStatus(id, name) {
            const switchEl = document.getElementById('refSwitch' + id);
            const labelEl = document.getElementById('refStatusLabel' + id);
            if (switchEl.checked) {
                labelEl.innerText = "Aktif";
                labelEl.className = "badge bg-success bg-opacity-10 text-success rounded-pill";
                showToast(`Kode referral untuk "${name}" telah diaktifkan.`);
            } else {
                labelEl.innerText = "Suspended";
                labelEl.className = "badge bg-danger bg-opacity-10 text-danger rounded-pill";
                showToast(`Kode referral untuk "${name}" telah ditangguhkan.`);
            }
        }

        // Load dashboard chart
        const ctx = document.getElementById('dashboardChart');
        if(ctx) {
            const ctx2d = ctx.getContext('2d');
            const gradient = ctx2d.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(139, 92, 246, 0.4)'); // Purple-500
            gradient.addColorStop(1, 'rgba(139, 92, 246, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($chartLabels ?? []),
                    datasets: [{ 
                        label: 'Transaksi Sukses', 
                        data: @json($chartValues ?? []), 
                        borderColor: '#6d28d9', 
                        backgroundColor: gradient, 
                        fill: true, 
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#4c1d95',
                        pointHoverRadius: 7
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    plugins: { legend: { display: false } }, 
                    scales: { 
                        y: { 
                            grid: { color: 'rgba(226, 232, 240, 0.5)' },
                            beginAtZero: true, 
                            ticks: { precision: 0 }
                        }, 
                        x: { 
                            grid: { display: false },
                            ticks: { maxTicksLimit: 12 }
                        } 
                    } 
                }
            });
        }

        // Load status keaktifan reseller donut chart
        const ctxStatus = document.getElementById('statusChart');
        if(ctxStatus) {
            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Aktif', 'Suspend'],
                    datasets: [{
                        data: [{{ $statusActiveCount }}, {{ $statusSuspendedCount }}],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    cutout: '70%'
                }
            });
        }
    </script>
</body>
</html>