@extends('layouts.admin')
@section('title', 'Laporan')
@section('styles')
<style>
    @media (min-width: 768px) { .proggress-box-operasional { display:flex; gap:2.25rem; justify-content:center; margin-left:-5px; } }
    @media (min-width: 1200px) { .proggress-box-operasional { justify-content:center; margin-left:-5px; } }
    .rekap-box { display: none; }
    .rekap-box.active { display: block; }
    .btn-report.active { background-color:#0A3EB6; color:#fff; }
    .btn-report { border:1px solid #0A3EB6; color:#0A3EB6; padding:6px 12px; border-radius:6px; background:#fff; }
    .filter-section { display:flex; justify-content:space-between; align-items:flex-end; gap:16px; margin-bottom:10px; }
    .filter-group { display:flex; flex-direction:column; gap:6px; }
    .filter-input { padding:6px 8px; border:1px solid #ddd; border-radius:6px; }
    .filter-date-group { position:relative; }
    .filter-date-group i { position:absolute; right:10px; top:50%; transform:translateY(-50%); color:#666; }
    .btn-apply, .btn-cari { background:#0A3EB6; color:#fff; border:none; padding:8px 12px; border-radius:6px; }
    .tabel-pendapatan { width:100%; border-collapse:collapse; }
    .tabel-pendapatan th, .tabel-pendapatan td { border:1px solid #e5e7eb; padding:8px 10px; }
    .title-laporan-metrik { margin-top:16px; }
    .content-event.modal-content, .content-operasional-view.modal-content { border-radius:10px; }
    .qr-box { display:flex; align-items:center; gap:12px; }
    .qr-box canvas { border:1px solid #eee; border-radius:8px; }
    .qr-box img { width:140px; height:140px; object-fit:contain; border:1px solid #eee; border-radius:8px; }

    .btn-export-report { background:#0A3EB6; color:#fff; border:none; padding:8px 12px; border-radius:6px; }
    .btn-export-report:hover { background:#08339A; color:#fff; }
    .btn-export-report:disabled { opacity:.6; cursor:not-allowed; }

    /* Export modal: keep close controls compact */
    #exportReportModal .btn-close { background-size: .75em .75em; }
    #exportReportModal .btn-export-close { width:auto !important; padding:.375rem .75rem !important; }

    /* Export Preview Modal: drop slightly lower */
    #exportReportModal .modal-dialog {
        margin-top: clamp(24px, 6vh, 72px);
        margin-bottom: 24px;
    }

    /* Rekap Pendaftaran modal: keep size reasonable (avoid global .modal-dialog max-width:950px) */
    #viewPendapatanModal .modal-dialog {
        max-width: min(560px, calc(100% - 2rem));
        margin: 16px auto;
    }

    /* ===== RESPONSIVE OVERRIDES ===== */
    /* Tablet ≤ 1024px */
    @media (max-width: 1024px) {
        .filter-section { flex-wrap: wrap !important; }
        .filter-kanan { flex-wrap: wrap !important; gap: 8px !important; }
        .filter-input { max-width: 100%; }
        .growth-charts-wrapper { grid-template-columns: 1fr !important; }
        .recap-card-box { grid-template-columns: repeat(2, 1fr) !important; }
    }

    /* Mobile ≤ 640px */
    @media (max-width: 640px) {
        .filter-section { flex-direction: column !important; align-items: stretch !important; }
        .filter-kiri, .filter-kanan { width: 100% !important; flex-direction: column !important; }
        .filter-input { width: 100% !important; box-sizing: border-box; }
        .btn-apply, .btn-reset { width: 100% !important; }
        .recap-card-box { grid-template-columns: 1fr !important; }
        .growth-charts-wrapper { grid-template-columns: 1fr !important; }
        .growth-chart-canvas-wrap { height: 200px !important; }
        /* Override inline grid styles */
        [style*="grid-template-columns:repeat(3"] { grid-template-columns: 1fr !important; }
        [style*="grid-template-columns: repeat(3"] { grid-template-columns: 1fr !important; }
        /* Period form */
        form.d-flex.flex-wrap { flex-direction: column !important; }
        form.d-flex.flex-wrap > div { width: 100% !important; max-width: 100% !important; }
        form.d-flex.flex-wrap input[type="month"] { max-width: 100% !important; width: 100% !important; }
    }
</style>
@endsection
@section('content')
<div class="py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-graph-up me-2"></i>Laporan</h4>
    </div>
    <div class="box-report">
        <h5>Ikhtisar Laporan</h5>
        <p>Berikut laporan dari event IdSpora</p>
        <div class="btn-report-box" style="display:flex; gap:8px;">
            <button class="btn-report active" data-target="pendapatan">Pendapatan</button>
            <button class="btn-report" data-target="pertumbuhan">Pertumbuhan</button>
        </div>

        <div id="pendapatan" class="rekap-box active">
            @php
                use Carbon\Carbon;
                // Batas awal laporan (konfigurasi permintaan user)
                $earliestDate = Carbon::create(2025,11,1,0,0,0); // November 2025
                // Ambil periode dari query (?period=YYYY-MM)
                $periodParam = request('period');
                if($periodParam && preg_match('/^(\d{4})-(\d{2})$/',$periodParam,$m)){
                    $selectedYear = (int)$m[1];
                    $selectedMonth = (int)$m[2];
                } else {
                    $selectedYear = (int) now()->year;
                    $selectedMonth = (int) now()->month;
                }
                $selectedDate = Carbon::create($selectedYear,$selectedMonth,1,0,0,0);
                // Clamp agar tidak sebelum earliestDate
                if($selectedDate->lt($earliestDate)) { $selectedDate = $earliestDate; }
                $prevDate = (clone $selectedDate)->subMonth();
                $nextDate = (clone $selectedDate)->addMonth();
                $currentDate = Carbon::now()->startOfMonth();
                $isFutureNext = $nextDate->greaterThan($currentDate); // disable next jika masa depan
                $isPastPrev = $prevDate->lt($earliestDate); // disable prev jika sebelum earliest
                $periodFmt = fn(Carbon $d) => $d->format('Y-m');
            @endphp
           
            <div class="card mb-3">
                <div class="card-body">
                    <canvas id="laporanChart" height="90"></canvas>
                </div>
            </div>
             <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap align-items-end gap-2 mb-3">
                <input type="hidden" name="tab" value="pendapatan">
                <div>
                    <label for="period" class="form-label mb-1 text-dark">Periode Bulan</label>
                    <input type="month" name="period" id="period" value="{{ sprintf('%04d-%02d',$selectedYear,$selectedMonth) }}" class="form-control" style="max-width:180px;">
                </div>
                <div class="d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm" style="height:38px;">Tampilkan</button>
                    <a href="{{ $isPastPrev ? '#' : url()->current().'?period='.$periodFmt($prevDate) }}" class="btn btn-outline-secondary btn-sm {{ $isPastPrev ? 'disabled' : '' }}" style="height:38px;">&laquo; {{ $prevDate->translatedFormat('F Y') }}</a>
                    <a href="{{ $isFutureNext ? '#' : url()->current().'?period='.$periodFmt($nextDate) }}" class="btn btn-outline-secondary btn-sm {{ $isFutureNext ? 'disabled' : '' }}" style="height:38px;">{{ $nextDate->translatedFormat('F Y') }} &raquo;</a>
                </div>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <div class="small text-muted">Menampilkan data bulan: <strong id="month-label-pendapatan">{{ $selectedDate->translatedFormat('F Y') }}</strong></div>
                    <button type="button" class="btn-export-report btn btn-sm" data-export-tab="pendapatan" style="height:38px;">Export</button>
                </div>
            </form>
            @php
                $paidStatuses = ['settlement','capture','success'];
                // Total bulan terpilih (bukan agregat keseluruhan)
                $totalRevenueAll = \App\Models\ManualPayment::where('status','settled')
                    ->whereYear('created_at',$selectedDate->year)
                    ->whereMonth('created_at',$selectedDate->month)
                    ->sum('amount');
                $totalExpenseAll = \App\Models\EventExpense::whereYear('created_at',$selectedDate->year)
                    ->whereMonth('created_at',$selectedDate->month)
                    ->sum('total');
                $totalMarginAll = $totalRevenueAll - $totalExpenseAll;
                // Revenue & Expense bulan terpilih (berdasarkan created_at transaksi / expense)
                $currentMonthRevenue = $totalRevenueAll;
                $previousMonthRevenue = \App\Models\ManualPayment::where('status','settled')
                    ->whereYear('created_at',$prevDate->year)->whereMonth('created_at',$prevDate->month)
                    ->sum('amount');
                $currentMonthExpense = $totalExpenseAll;
                $previousMonthExpense = \App\Models\EventExpense::whereYear('created_at',$prevDate->year)->whereMonth('created_at',$prevDate->month)->sum('total');
                $currentMonthMargin = $currentMonthRevenue - $currentMonthExpense;
                $previousMonthMargin = $previousMonthRevenue - $previousMonthExpense;
                $fmtRp = function($n){ return 'Rp'.number_format((int)$n,0,',','.'); };
                $growth = function($curr,$prev, Carbon $prevDate, Carbon $earliestDate){
                    // Jika bulan sebelumnya sebelum earliest -> treat growth baseline 0 tanpa lonjakan 100%
                    if($prevDate->lt($earliestDate)) {
                        return 0;
                    }
                    if($prev == 0){
                        if($curr == 0) return 0;
                        // prev == 0 dan ada data sekarang => 100% (lonjakan penuh)
                        return 100;
                    }
                    return (($curr - $prev)/($prev))*100; // bisa negatif
                };
                $revGrowth = $growth($currentMonthRevenue,$previousMonthRevenue,$prevDate,$earliestDate);
                $expGrowth = $growth($currentMonthExpense,$previousMonthExpense,$prevDate,$earliestDate);
                $marGrowth = $growth($currentMonthMargin,$previousMonthMargin,$prevDate,$earliestDate);
                $arrowData = function($percent){
                    $up = $percent >= 0; $clsColor = $up ? 'green' : 'red';
                    $icon = $up ? 'arrow-up' : 'arrow-down';
                    return [$up,$clsColor,$icon,round(abs($percent),1)];
                };
                [$revUp,$revColor,$revIcon,$revPctAbs] = $arrowData($revGrowth);
                [$expUp,$expColor,$expIcon,$expPctAbs] = $arrowData($expGrowth);
                [$marUp,$marColor,$marIcon,$marPctAbs] = $arrowData($marGrowth);
            @endphp
            {{-- Chart: trend per hari di bulan terpilih --}}
            @php
                // Prepare per-day series for the selected month
                $daysInMonth = $selectedDate->daysInMonth;
                $revenuePerDay = \App\Models\ManualPayment::where('status','settled')
                    ->whereYear('created_at',$selectedDate->year)
                    ->whereMonth('created_at',$selectedDate->month)
                    ->selectRaw('DAY(created_at) as day, SUM(amount) as total')
                    ->groupBy('day')
                    ->pluck('total','day');

                $expensePerDay = \App\Models\EventExpense::whereYear('created_at',$selectedDate->year)
                    ->whereMonth('created_at',$selectedDate->month)
                    ->selectRaw('DAY(created_at) as day, SUM(total) as total')
                    ->groupBy('day')
                    ->pluck('total','day');

                $labels = [];
                $seriesRevenue = [];
                $seriesExpense = [];
                $seriesProfit = [];
                for($d=1;$d<=$daysInMonth;$d++){
                    $labels[] = $d;
                    $r = (float) ($revenuePerDay[$d] ?? 0);
                    $e = (float) ($expensePerDay[$d] ?? 0);
                    $seriesRevenue[] = $r;
                    $seriesExpense[] = $e;
                    $seriesProfit[] = $r - $e;
                }
            @endphp

            <div style="margin-bottom:12px;">
            </div>

            <div class="recap-card-box" style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin:14px 0;">
            
               <div class="recap-card" style="border:1px solid #eee; border-radius:10px; padding:20px; padding-left:20px;padding-right:20px;">
                    <div class="recap-title" style="display:flex; gap:8px; align-items:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
                            <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518z" />
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0  0 0 0 16" />
                            <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11m0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12" />
                        </svg>
                        <h5>Total Pendapatan</h5>
                    </div>
                    <h3>{{ $fmtRp($totalRevenueAll) }}</h3>
                    <div class="recap-increase" style="display:flex; gap:8px; align-items:center; color:{{ $revColor }};">
                        @if($revUp)
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                        </svg>
                        @endif
                        <p>{{ $revPctAbs }}% dari bulan lalu</p>
                    </div>
                </div>
                <!-- Biaya Operasional -->
               <div class="recap-card" style="border:1px solid #eee; border-radius:10px; padding:20px; padding-left:20px;padding-right:20px;">
                    <div class="recap-title" style="display:flex; gap:8px; align-items:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
                            <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518z" />
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0  0 0 0 16" />
                            <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11m0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12" />
                        </svg>
                        <h5>Biaya Operasional</h5>
                    </div>
                    <h3>{{ $fmtRp($totalExpenseAll) }}</h3>
                    <div class="recap-increase" style="display:flex; gap:8px; align-items:center; color:{{ $expColor }};">
                        @if($expUp)
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                        </svg>
                        @endif
                        <p>{{ $expPctAbs }}% dari bulan lalu</p>
                    </div>
                </div>
                <!-- Margin Keuntungan -->
               <div class="recap-card" style="border:1px solid #eee; border-radius:10px; padding:20px; padding-left:20px;padding-right:20px;">
                    <div class="recap-title" style="display:flex; gap:8px; align-items:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
                            <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518z" />
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0  0 0 0 16" />
                            <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11m0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12" />
                        </svg>
                        <h5>Margin Keuntungan</h5>
                    </div>
                    <h3>{{ $fmtRp($totalMarginAll) }}</h3>
                    <div class="recap-increase" style="display:flex; gap:8px; align-items:center; color:{{ $marColor }};">
                        @if($marUp)
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                        </svg>
                        @endif
                        <p>{{ $marPctAbs }}% dari bulan lalu</p>
                    </div>
                </div>
            </div>

            <div class="pendapatan-box">
                <h5>Pendapatan Per Acara</h5>
                <div class="filter-section" id="filters-pendapatan">
                    <div class="filter-kiri" >
                        <div class="filter-group" style="padding-left:50px;">
                            <label for="filter-event-pendapatan" class="filter-label" style="margin-left:-20px;">Cari Event</label>
                           <div style="display:flex; gap:6px; align-items:center; position:relative;">
                             <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                            <input type="text" id="filter-event-pendapatan" class="filter-input" placeholder="Cari nama event...">
                           </div> 
                            
                        </div>
                    </div>
                    <div class="filter-kanan" style="display:flex; gap:14px; align-items:flex-end;">
                        <div class="filter-group">
                            <label for="date-from-pendapatan" class="filter-label">Dari Tanggal</label>
                            <div class="filter-date-group">
                                <input type="date" id="date-from-pendapatan" class="filter-input">
                            </div>
                        </div>
                        <div class="filter-group">
                            <label for="date-to-pendapatan" class="filter-label">Sampai Tanggal</label>
                            <div class="filter-date-group">
                                <input type="date" id="date-to-pendapatan" class="filter-input">
                            </div>
                        </div>
                        <div class="filter-actions">
                            <button type="button" class="btn-apply btn-reset" id="btn-reset-pendapatan" style="background:#6c757d;">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                <table class="tabel-pendapatan" id="table-pendapatan">
                    <thead>
                        <tr>
                            <th style="background-color: #E4E4E6;" scope="col">Nama Event</th>
                            <th style="background-color: #E4E4E6;" scope="col">Tanggal</th>
                            <th style="background-color: #E4E4E6;" scope="col">Peserta</th>
                            <th style="background-color: #E4E4E6;" scope="col">Harga</th>
                            <th style="background-color: #E4E4E6;" scope="col">Pemasukan</th>
                            <th style="background-color: #E4E4E6;" scope="col">Pengeluaran</th>
                            <th style="background-color: #E4E4E6;" scope="col">Keuntungan</th>
                            <th style="background-color: #E4E4E6;" scope="col">Detail</th>
                        </tr>
                    <tbody>
                        @php
                            // $eventRows is now provided by the controller and filtered by month/year.
                        @endphp
                        @forelse($eventRows as $row)
                            <tr data-name="{{ Str::lower($row['name']) }}" data-date="{{ isset($row['date']) ? \Carbon\Carbon::createFromFormat('d/m/Y',$row['date'])->format('Y-m-d') : '' }}" data-participants="{{ $row['participants'] }}">
                                <td>{{ $row['name'] }}</td>
                                <td>{{ $row['date'] ?? '-' }}</td>
                                <td>{{ $row['participants'] }} Peserta</td>
                                <td>{{ number_format($row['price'],0,',','.') }}</td>
                                <td>{{ number_format($row['revenue'],0,',','.') }}</td>
                                <td>{{ number_format($row['expense'],0,',','.') }}</td>
                                <td>{{ number_format($row['profit'],0,',','.') }}</td>
                                <td>
                                    @php
                                        // Ensure expense rows are available even if $eventRows came from controller
                                        $expenseRowsLocal = \App\Models\EventExpense::where('event_id', $row['id'] ?? 0)
                                            ->get(['item','quantity','unit_price','total'])
                                            ->map(function($r){
                                                return [
                                                    'label' => $r->item,
                                                    'qty' => (int)($r->quantity ?? 0),
                                                    'unit' => (float)($r->unit_price ?? 0),
                                                    'total' => (float)($r->total ?? 0),
                                                ];
                                            })->values()->all();
                                    @endphp
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal"
                                         data-event-id="{{ $row['id'] ?? '' }}"
                                         data-event-name="{{ $row['name'] ?? 'Event' }}"
                                         data-registered-count="{{ (int)($row['registered_count'] ?? $row['participants'] ?? 0) }}"
                                         data-income='@json($row['income_rows'] ?? [])'
                                         data-expenses='@json(($row['expense_rows'] ?? null) ?: $expenseRowsLocal)'
                                         data-income-total="{{ (float)($row['revenue'] ?? 0) }}"
                                         data-expense-total="{{ (float)($row['expense'] ?? 0) }}"
                                         data-profit-total="{{ (float)(($row['revenue'] ?? 0) - ($row['expense'] ?? 0)) }}"
                                        >
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data event</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>{{-- end table-responsive --}}
            </div>
        </div>

        <div id="pertumbuhan" class="rekap-box">

            {{-- ===== GRAFIK SPLIT: KIRI peserta, KANAN count event ===== --}}
            <div class="growth-charts-wrapper">
                {{-- Kiri: Grafik Total Peserta Event Free vs Berbayar --}}
                <div class="growth-chart-card">
                    <div class="growth-chart-title">Total Peserta per Hari</div>
                    <div class="growth-chart-subtitle">Event Free vs Berbayar</div>
                    <div class="growth-chart-canvas-wrap">
                        <canvas id="chartParticipants"></canvas>
                    </div>
                </div>

                {{-- Kanan: Bar chart total event free/berbayar/manage/create --}}
                <div class="growth-chart-card">
                    <div class="growth-chart-title">Komposisi Event</div>
                    <div class="growth-chart-subtitle">Free · Berbayar · Manage · Create</div>
                    <div class="growth-chart-canvas-wrap">
                        <canvas id="chartEventComposition"></canvas>
                    </div>
                </div>
            </div>

            {{-- Filter periode --}}
            <div class="mt-4 mb-4">
                <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap align-items-end gap-2">
                    <input type="hidden" name="tab" value="operasional">
                    <div>
                        <label for="period_op" class="form-label mb-1 text-dark">Periode Bulan</label>
                        <input type="month" name="period" id="period_op" value="{{ $periodOpValue ?? $selectedDate->format('Y-m') }}" class="form-control" style="max-width:180px;">
                    </div>
                    <div class="d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm" style="height:38px;">Tampilkan</button>
                        @php
                            $prevOp = (clone $selectedDate)->subMonth();
                            $nextOp = (clone $selectedDate)->addMonth();
                            $curr = \Carbon\Carbon::now()->startOfMonth();
                            $isFut = $nextOp->gt($curr);
                        @endphp
                        <a href="{{ url()->current().'?tab=operasional&period='.$prevOp->format('Y-m') }}" class="btn btn-outline-secondary btn-sm" style="height:38px;">&laquo; {{ $prevOp->translatedFormat('F Y') }}</a>
                        <a href="{{ $isFut ? '#' : url()->current().'?tab=operasional&period='.$nextOp->format('Y-m') }}" class="btn btn-outline-secondary btn-sm {{ $isFut ? 'disabled' : '' }}" style="height:38px;">{{ $nextOp->translatedFormat('F Y') }} &raquo;</a>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        <div class="small text-muted">Menampilkan data bulan: <strong id="month-label-operasional">{{ $selectedDate->translatedFormat('F Y') }}</strong></div>
                        <button type="button" class="btn-export-report btn btn-sm" data-export-tab="operasional" style="height:38px;">Export</button>
                    </div>
                </form>
            </div>

            <h5 class="title-laporan-metrik">Metrik Operasional Rinci</h5>
            <div class="filter-section" id="filters-pertumbuhan">
                <div class="filter-kiri">
                    <div class="filter-group" style="padding-left:50px;">
                        <div style="display:flex; gap:6px; align-items:center; position:relative;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
                            </svg>
                            <input type="text" id="filter-event-pertumbuhan" class="filter-input" placeholder="Cari nama event...">
                        </div>
                    </div>
                </div>
                <div class="filter-kanan">
                    <div class="filter-group">
                        <label for="date-from-pertumbuhan" class="filter-label">Dari Tanggal</label>
                        <div class="filter-date-group">
                            <input type="date" id="date-from-pertumbuhan" class="filter-input">
                        </div>
                    </div>
                    <div class="filter-group">
                        <label for="date-to-pertumbuhan" class="filter-label">Sampai Tanggal</label>
                        <div class="filter-date-group">
                            <input type="date" id="date-to-pertumbuhan" class="filter-input">
                        </div>
                    </div>
                    <div class="filter-actions"><button type="button" class="btn-apply btn-reset" id="btn-reset-pertumbuhan" style="background:#6c757d;">Reset</button></div>
                </div>
            </div>

            <div class="table-responsive">
            <table class="tabel-pendapatan" id="table-pertumbuhan">
                <thead>
                    <tr>
                        <th style="background-color: #E4E4E6;" scope="col">Nama Event</th>
                        <th style="background-color: #E4E4E6;" scope="col">Tanggal</th>
                        <th style="background-color: #E4E4E6;" scope="col">Peserta</th>
                        <th style="background-color: #E4E4E6;" scope="col">Pembicara</th>
                        <th style="background-color: #E4E4E6;" scope="col">Kelola Event</th>
                        <th style="background-color: #E4E4E6;" scope="col">Tipe Harga</th>
                        <th style="background-color: #E4E4E6;" scope="col">Rating Acara</th>
                        <th style="background-color: #E4E4E6;" scope="col">Rating Pembicara</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($growthRows as $row)
                        <tr
                            data-name="{{ Str::lower($row['name']) }}"
                            data-date="{{ isset($row['date']) ? \Carbon\Carbon::createFromFormat('d/m/Y',$row['date'])->format('Y-m-d') : '' }}"
                            data-participants="{{ $row['participants'] }}"
                            data-event-rating="{{ $row['event_rating'] ?? '' }}"
                            data-speaker-rating="{{ $row['speaker_rating'] ?? '' }}"
                            data-title="{{ $row['name'] }}">
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['date'] ?? '-' }}</td>
                            <td>{{ $row['participants'] }} Peserta</td>
                            <td>{{ $row['speaker'] ?? '-' }}</td>
                            <td>
                                @php $ma = strtolower(trim($row['manage_action'] ?? 'create')); @endphp
                                <span class="growth-badge {{ $ma === 'manage' ? 'growth-badge-manage' : 'growth-badge-create' }}">
                                    {{ $ma === 'manage' ? 'Manage' : 'Create' }}
                                </span>
                            </td>
                            <td>
                                <span class="growth-badge {{ ($row['is_free'] ?? true) ? 'growth-badge-free' : 'growth-badge-paid' }}">
                                    {{ ($row['is_free'] ?? true) ? 'Free' : 'Berbayar' }}
                                </span>
                            </td>
                            <td>{{ isset($row['event_rating']) && $row['event_rating'] !== null ? $row['event_rating'].'/5' : '-' }}</td>
                            <td>{{ isset($row['speaker_rating']) && $row['speaker_rating'] !== null ? $row['speaker_rating'].'/5' : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data event</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>{{-- end table-responsive --}}
        </div>
    </div>

    <!-- Export Preview Modal -->
    <div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" style="margin-top: 100px; margin-bottom: 24px;">
            <div class="modal-content" style="border-radius:10px;">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportReportModalLabel">Export Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-semibold" id="exportReportTitle">-</div>
                            <div class="small text-muted">Menampilkan data bulan: <strong id="exportReportMonth">-</strong></div>
                        </div>
                    </div>
                    <div id="exportReportPreviewWrapper" style="background:#fff;">
                        <div id="exportReportPreview" class="table-responsive"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm btn-export-close" data-bs-dismiss="modal" >Close</button>
                    <button type="button" class="btn-export-report" id="btnExportPdf">Save PDF</button>
                    <button type="button" class="btn-export-report" id="btnExportExcel">Save Excel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <div class="modal-view-pendapatan modal fade" id="viewPendapatanModal" tabindex="-1" aria-labelledby="viewPendapatanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="content-event modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewPendapatanLabel">Rekap Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="box-rekap-pendapatan">
                        <div class="tabel-pemasukan">
                            <h5>Pemasukan</h5>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="background-color: #E4E4E6;" scope="col">Pemasukan</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Kuantitas</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Harga Satuan</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody id="incomeTbody"></tbody>
                            </table>
                        </div>
                        <div class="tabel-pengeluaran">
                            <h5>Pengeluaran</h5>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="background-color: #E4E4E6;" scope="col">Kebutuhan (Barang)</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Kuantitas</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Harga Satuan (Rp)</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Harga Total (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody id="expenseTbody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="keuntungan">
                        <h5>Keuntungan</h5>
                        <p>Keuntungan (Laba Bersih) = Total Pemasukan − Total Pengeluaran</p>
                        <h6 id="profitFormula"> - </h6>
                        <h6 id="profitTotal"> - </h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-view-pertumbuhan modal fade" id="viewPertumbuhanModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="content-operasional-view modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Status Dokumen Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Detail penilaian untuk acara ini.</p>
                    <div class="mb-3">
                        <small class="text-muted">Event</small>
                        <div class="fw-semibold" id="pertumbuhanEventName">-</div>
                    </div>
                    <div class="pertumbuhan-dialog-box">
                        <div class="pertumbuhan-dialog-card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#4b2dbf" class="bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0  0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                            </svg>
                            <div class="view-pertumbuhan">
                                <p class="label-view">Jumlah Peserta</p>
                                <p class="value-view" id="pertumbuhanParticipants">-</p>
                            </div>
                        </div>
                        <div class="pertumbuhan-dialog-card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#4b2dbf" class="bi bi-star" viewBox="0 0 16 16">
                                <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z" />
                            </svg>
                            <div class="view-pertumbuhan">
                                <p class="label-view">Rata-rata Rating Acara</p>
                                <p class="value-view" id="pertumbuhanEventRating">-</p>
                            </div>
                        </div>
                        <div class="pertumbuhan-dialog-card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#4b2dbf" class="bi bi-star" viewBox="0 0 16 16">
                                <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z" />
                            </svg>
                            <div class="view-pertumbuhan">
                                <p class="label-view">Rata-rata Rating Pembicara</p>
                                <p class="value-view" id="pertumbuhanSpeakerRating">-</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   
</div>
@endsection
@section('scripts')



<script>

    //diagram report pertumbuhan event — Peserta Free vs Berbayar per hari
document.addEventListener("DOMContentLoaded", function () {

    // Chart kiri: line chart peserta per hari
    const ctx = document.getElementById('chartParticipants');
    if(ctx){
        const labels = @json($chartLabels ?? []);
        const freeParticipantData = @json($chartFreeParticipantData ?? []);
        const paidParticipantData = @json($chartPaidParticipantData ?? []);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Peserta Event Free',
                        data: freeParticipantData,
                        borderColor: '#6f42c1',
                        backgroundColor: 'rgba(111,66,193,0.08)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3,
                        fill: true
                    },
                    {
                        label: 'Peserta Event Berbayar',
                        data: paidParticipantData,
                        borderColor: '#fd7e14',
                        backgroundColor: 'rgba(253,126,20,0.08)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { usePointStyle: true } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y + ' peserta'
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Chart kanan: bar chart komposisi event (free/berbayar/manage/create)
    const ctxComp = document.getElementById('chartEventComposition');
    if(ctxComp){
        new Chart(ctxComp, {
            type: 'bar',
            data: {
                labels: ['Event Free', 'Event Berbayar', 'Event Manage', 'Event Create'],
                datasets: [{
                    label: 'Jumlah Event',
                    data: [
                        {{ $totalFreeEventsSelected ?? 0 }},
                        {{ $totalPaidEventsSelected ?? 0 }},
                        {{ $totalManageEvents ?? 0 }},
                        {{ $totalCreateEvents ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(111,66,193,0.75)',
                        'rgba(253,126,20,0.75)',
                        'rgba(29,78,216,0.75)',
                        'rgba(21,128,61,0.75)'
                    ],
                    borderColor: [
                        '#6f42c1',
                        '#fd7e14',
                        '#1d4ed8',
                        '#15803d'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' event'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    }

});
</script>

<script>
// Fill Pertumbuhan detail modal from the clicked table row
document.addEventListener('DOMContentLoaded', function(){
    try {
        const modalEl = document.getElementById('viewPertumbuhanModal');
        if(!modalEl) return;

        const nameEl = document.getElementById('pertumbuhanEventName');
        const participantsEl = document.getElementById('pertumbuhanParticipants');
        const eventRatingEl = document.getElementById('pertumbuhanEventRating');
        const speakerRatingEl = document.getElementById('pertumbuhanSpeakerRating');

        function fmtRating(v){
            const n = Number(v);
            if(!Number.isFinite(n) || n <= 0) return '-';
            // show 1 decimal when needed, otherwise integer
            const s = (Math.round(n * 10) % 10 === 0) ? String(Math.round(n)) : n.toFixed(1);
            return s + '/5';
        }

        modalEl.addEventListener('show.bs.modal', function(ev){
            const trigger = ev.relatedTarget;
            const row = trigger && trigger.closest ? trigger.closest('tr') : null;
            if(!row) return;

            const title = row.getAttribute('data-title') || row.querySelector('td')?.textContent?.trim() || 'Event';
            const participants = row.getAttribute('data-participants') || '';
            const eventRating = row.getAttribute('data-event-rating') || '';
            const speakerRating = row.getAttribute('data-speaker-rating') || '';

            if(nameEl) nameEl.textContent = title;
            if(participantsEl) participantsEl.textContent = participants ? (participants + ' Peserta') : '-';
            if(eventRatingEl) eventRatingEl.textContent = fmtRating(eventRating);
            if(speakerRatingEl) speakerRatingEl.textContent = fmtRating(speakerRating);
        });
    } catch(e) {
        // never block report page
    }
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('laporanChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($labels), 
            datasets: [
                {
                    label: 'Pendapatan',
                    data: @json($seriesRevenue),
                    borderWidth: 2,
                    tension: 0.4
                },
                {
                    label: 'Pengeluaran',
                    data: @json($seriesExpense),
                    borderWidth: 2,
                    tension: 0.4
                },
                {
                    label: 'Keuntungan',
                    data: @json($seriesProfit),
                    borderWidth: 2,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
});
</script>

<script>
// QRCode library (client-side render)
</script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<!-- Export helpers -->
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const buttons = document.querySelectorAll('.btn-report');
    const sections = document.querySelectorAll('.rekap-box');
    
    // Function to activate tab (useful for initial load)
    function activateTab(targetId) {
        sections.forEach(section => section.classList.remove('active'));
        const targetSection = document.getElementById(targetId);
        if (targetSection) targetSection.classList.add('active');
        
        buttons.forEach(btn => {
            btn.classList.remove('active');
            if(btn.getAttribute('data-target') === targetId) {
                btn.classList.add('active');
            }
        });
        
        // Ensure hidden inputs for "tab" in other forms (if any) are updated? 
        // Not strictly needed if each form routes to its tab via hidden input or standard behaviour
    }

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            activateTab(targetId);
            // Optional: update URL query param without reload
            const url = new URL(window.location);
            url.searchParams.set('tab', targetId);
            window.history.pushState({}, '', url);
            
            // Re-apply JS filters for tables (if any active)
            applyAllFilters();
        });
    });

    // Check backend passed active tab
    const activeTabBackend = "{{ $activeTab ?? 'pendapatan' }}";
    activateTab(activeTabBackend);

    // Initial Chart: Event Create vs Manage (Trend Line Chart)
    const ctxCM = document.getElementById('chartEventCreateManage');
    if(ctxCM){
        // Data from controller
        const labels = @json($chartLabels ?? []);
        const createData = @json($chartCreateData ?? []);
        const manageData = @json($chartManageData ?? []);

        new Chart(ctxCM, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Event Create',
                        data: createData,
                        borderColor: '#0d6efd', // Bootstrap primary blue
                        backgroundColor: '#0d6efd',
                        tension: 0.4, // curved line
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: false
                    },
                    {
                        label: 'Total Event Manage',
                        data: manageData,
                        borderColor: '#198754', // Bootstrap success green
                        backgroundColor: '#198754',
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    function setupFilter(config){
        const {
            searchInput, dateFromInput, dateToInput, applyBtn, searchBtn, resetBtn, tableSelector
        } = config;
        const table = document.querySelector(tableSelector);
        if(!table) return;
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        function matches(row){
            // Prefer explicit data-name (already lowercased server-side),
            // but fall back to first cell text when attribute is missing.
            const nameAttr = row.getAttribute('data-name') || '';
            const name = (nameAttr ? nameAttr : (row.querySelector('td')?.textContent || '')).toLowerCase();
            const rawDateAttr = row.getAttribute('data-date') || '';
            const displayedDateText = row.querySelector('td:nth-child(2)')?.textContent?.trim() || '';
            const rowDateStr = rawDateAttr || displayedDateText;
            const searchVal = (searchInput?.value || '').toLowerCase().trim();
            const fromVal = dateFromInput?.value || '';
            const toVal = dateToInput?.value || '';

            // Name filter
            if(searchVal && !name.includes(searchVal)) return false;

            // Generic parser: supports YYYY-MM-DD, YYYY/MM/DD and DD/MM/YYYY
            function parseToTimestamp(s){
                if(!s) return NaN;
                s = String(s).trim();
                // ISO-like YYYY-MM-DD or YYYY/MM/DD
                if(/^\d{4}[\-/]\d{2}[\-/]\d{2}$/.test(s)){
                    const normalized = s.replace(/\//g,'-');
                    const dt = new Date(normalized);
                    return isNaN(dt.getTime()) ? NaN : dt.getTime();
                }
                // dd/mm/yyyy
                if(/^\d{2}\/\d{2}\/\d{4}$/.test(s)){
                    const p = s.split('/');
                    const day = Number(p[0]);
                    const mon = Number(p[1]);
                    const yr = Number(p[2]);
                    const dt = new Date(yr, mon - 1, day);
                    return isNaN(dt.getTime()) ? NaN : dt.getTime();
                }
                const parsed = Date.parse(s);
                return isNaN(parsed) ? NaN : parsed;
            }

            const rowTime = parseToTimestamp(rowDateStr);

            if(fromVal){
                const fromTime = parseToTimestamp(fromVal);
                if(!isNaN(rowTime) && !isNaN(fromTime)){
                    if(rowTime < fromTime) return false;
                } else if(rowDateStr && fromVal){
                    if(rowDateStr < fromVal) return false;
                }
            }
            if(toVal){
                const toTime = parseToTimestamp(toVal);
                if(!isNaN(rowTime) && !isNaN(toTime)){
                    if(rowTime > toTime) return false;
                } else if(rowDateStr && toVal){
                    if(rowDateStr > toVal) return false;
                }
            }

            return true;
        }
        function apply(){
            const tbody = table.tBodies && table.tBodies[0] ? table.tBodies[0] : null;
            const colCount = table.tHead && table.tHead.rows[0] ? table.tHead.rows[0].cells.length : 1;
            let visibleCount = 0;
            rows.forEach(r => {
                if(matches(r)){
                    r.style.display='';
                    visibleCount++;
                } else {
                    r.style.display='none';
                }
            });

            // Manage client-side 'no data' row
            if(tbody){
                const existing = tbody.querySelector('tr.no-data-client');
                if(visibleCount === 0){
                    if(!existing){
                        const tr = document.createElement('tr');
                        tr.className = 'no-data-client';
                        const td = document.createElement('td');
                        td.setAttribute('colspan', String(colCount));
                        td.className = 'text-center';
                        td.textContent = 'Belum ada data event';
                        tr.appendChild(td);
                        tbody.appendChild(tr);
                    }
                } else {
                    if(existing) existing.remove();
                }
            }
        }
        // Expose for global re-apply
        config.apply = apply;
        if(applyBtn) applyBtn.addEventListener('click', apply);
        if(resetBtn) resetBtn.addEventListener('click', () => {
            if(dateFromInput) dateFromInput.value='';
            if(dateToInput) dateToInput.value='';
            apply();
        });
        if(searchBtn) searchBtn.addEventListener('click', apply);
        if(searchInput) searchInput.addEventListener('keyup', debounce(apply,300));
        [dateFromInput,dateToInput].forEach(inp => inp && inp.addEventListener('change', apply));
    }

    function debounce(fn, delay){
        let t; return function(){ clearTimeout(t); t=setTimeout(fn,delay); };
    }

    const filterConfigs = [
        {
            searchInput: document.getElementById('filter-event-pendapatan'),
            dateFromInput: document.getElementById('date-from-pendapatan'),
            dateToInput: document.getElementById('date-to-pendapatan'),
            applyBtn: document.getElementById('btn-apply-pendapatan'),
            searchBtn: document.getElementById('btn-cari-pendapatan'),
            resetBtn: document.getElementById('btn-reset-pendapatan'),
            tableSelector: '#pendapatan table.tabel-pendapatan'
        },
        {
            searchInput: document.getElementById('filter-event-pertumbuhan'),
            dateFromInput: document.getElementById('date-from-pertumbuhan'),
            dateToInput: document.getElementById('date-to-pertumbuhan'),
            applyBtn: document.getElementById('btn-apply-pertumbuhan'),
            searchBtn: document.getElementById('btn-cari-pertumbuhan'),
            resetBtn: document.getElementById('btn-reset-pertumbuhan'),
            tableSelector: '#pertumbuhan table.tabel-pendapatan'
        },
        {
            searchInput: document.getElementById('filter-event-operasional'),
            dateFromInput: document.getElementById('date-from-operasional'),
            dateToInput: document.getElementById('date-to-operasional'),
            applyBtn: document.getElementById('btn-apply-operasional'),
            searchBtn: document.getElementById('btn-cari-operasional'),
            resetBtn: document.getElementById('btn-reset-operasional'),
            tableSelector: '#operasional table.tabel-pendapatan'
        }
    ];
    filterConfigs.forEach(setupFilter);

    function applyAllFilters(){
        filterConfigs.forEach(cfg => cfg.apply && cfg.apply());
    }
    window.applyAllFilters = applyAllFilters;
    // Initial apply to normalize state
    applyAllFilters();

    // Handle Upload Operasional Modal
    const uploadModal = document.getElementById('uploadOperasionalModal');
    if (uploadModal) {
        uploadModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const eventId = button.getAttribute('data-bs-id');
            const vbgUrl = button.getAttribute('data-vbg');
            const certUrl = button.getAttribute('data-cert');
            const moduleUrl = button.getAttribute('data-module');
            const absUrl = button.getAttribute('data-abs');
            const qrImgUrl = button.getAttribute('data-qr-img');

            const form = document.getElementById('formUploadOperasional');
            // Update form action with correct event ID
            form.action = '/admin/events/' + eventId + '/documents';
            
            // Helper to set preview
            const setPreview = (id, url, label, fallbackQr = null) => {
                const container = document.getElementById(id);
                if(!container) return;
                if(url) {
                    // Check if likely an image by extension
                    const isImg = url.match(/\.(jpeg|jpg|png|webp)$/i);
                    if(isImg) {
                        container.innerHTML = `<a href="${url}" target="_blank"><img src="${url}" style="height:60px; border-radius:4px; border:1px solid #dee2e6;"></a> <small class="text-muted d-block mt-1">Klik gambar untuk melihat</small>`;
                    } else {
                        container.innerHTML = `<a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-file-earmark"></i> Lihat File Sekarang</a>`;
                    }
                } else if(fallbackQr) {
                    container.innerHTML = `<div class="d-flex align-items-center gap-2">
                        <img src="${fallbackQr}" style="height:60px; border-radius:4px; border:1px solid #dee2e6;">
                        <div>
                            <span class="badge bg-success mb-1">QR Code Aktif</span>
                            <small class="text-muted d-block">Absensi menggunakan QR Code</small>
                        </div>
                    </div>`;
                } else {
                    container.innerHTML = `<span class="badge bg-light text-dark border">Belum ada file</span>`;
                }
            };

            setPreview('preview-vbg', vbgUrl, 'Virtual Background');
            setPreview('preview-sertif', certUrl, 'Sertifikat');
            setPreview('preview-module', moduleUrl, 'Module');
            setPreview('preview-absensi', absUrl, 'Absensi', qrImgUrl);
        });
    }

    // Populate Status Dokumen modal dynamically
    const viewOperasionalModal = document.getElementById('viewOperasionalModal');
    if (viewOperasionalModal) {
        viewOperasionalModal.addEventListener('show.bs.modal', function (ev) {
            const trigger = ev.relatedTarget;
            const name = trigger?.getAttribute('data-name') || 'Event';
            // Urls
            const vbgUrl = trigger?.getAttribute('data-vbg') || '';
            const certUrl = trigger?.getAttribute('data-cert') || '';
            const moduleUrl = trigger?.getAttribute('data-module') || '';
            const absUrl = trigger?.getAttribute('data-abs') || '';
            
            const qrText = trigger?.getAttribute('data-qr') || '';
            const qrImage = trigger?.getAttribute('data-qr-img') || '';

            const titleEl = document.getElementById('viewOperasionalTitle');
            if (titleEl) titleEl.textContent = 'Status Dokumen: ' + name;

            const container = document.getElementById('operasionalStatusContainer');
            if (!container) return;

            const row = (label, url) => {
                const cls = url ? 'btn-selesai text-decoration-none' : 'btn-pending';
                const content = url ? 'Lihat' : 'Pending';
                
                if(url) {
                    return `<div class="box-kelengkapan d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">${label}</h6>
                        <a href="${url}" target="_blank" class="${cls}" style="display:inline-block; text-align:center;">${content}</a>
                    </div>`;
                }

                return `<div class="box-kelengkapan d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">${label}</h6>
                        <button class="${cls}">${content}</button>
                    </div>`;
            };

            // QR Row Generator
            const qrRow = () => {
                if (qrImage) {
                    return `<div class="box-kelengkapan d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">QR Absensi</h6>
                        <div class="qr-box"><img id="attendanceQrImg" src="${qrImage}" alt="QR Absensi"> 
                            <div class="small text-muted">Scan untuk absensi</div></div>
                    </div>`;
                }
                return `<div class="box-kelengkapan d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">QR Absensi</h6>
                    <div class="qr-box"><canvas id="attendanceQrCanvas" aria-label="QR Absensi"></canvas>
                </div>`;
            };

            container.innerHTML = [
                row('Virtual Background', vbgUrl),
                row('Module (Trainer)', moduleUrl),
                qrRow()
            ].join('');

            // Render QR on canvas if no stored image provided
            try {
                const canvas = document.getElementById('attendanceQrCanvas');
                if (canvas) {
                    if (qrText && window.QRCode) {
                        QRCode.toCanvas(canvas, qrText, { width: 140, margin: 1 }, function (error) {
                            if (error) console.error('QR render error:', error);
                        });
                    } else {
                        const ctx = canvas.getContext('2d');
                        if (ctx) {
                            ctx.fillStyle = '#f8f9fa';
                            ctx.fillRect(0,0,140,140);
                            ctx.fillStyle = '#6c757d';
                            ctx.font = '12px system-ui, -apple-system, Segoe UI, Roboto';
                            ctx.fillText('QR tidak tersedia', 14, 74);
                        }
                    }
                }
            } catch (_e) { /* silent */ }
        });
    }

    // Populate Rekap Pendaftaran (Pendapatan) modal dynamically from data attributes
    const pendapatanModal = document.getElementById('viewPendapatanModal');
    if (pendapatanModal) {
        pendapatanModal.addEventListener('show.bs.modal', function (ev) {
            const trigger = ev.relatedTarget;
            if (!trigger) return;
            const name = trigger.getAttribute('data-event-name') || 'Event';
            const incomeJson = trigger.getAttribute('data-income') || '[]';
            const expenseJson = trigger.getAttribute('data-expenses') || '[]';
            const incomeTotal = parseFloat(trigger.getAttribute('data-income-total') || '0') || 0;
            const expenseTotal = parseFloat(trigger.getAttribute('data-expense-total') || '0') || 0;
            const profitTotal = parseFloat(trigger.getAttribute('data-profit-total') || '0') || 0;
            const registeredCount = parseInt(trigger.getAttribute('data-registered-count') || '0', 10) || 0;
            let incomeRows = [];
            let expenseRows = [];
            try { incomeRows = JSON.parse(incomeJson) || []; } catch (_e) {}
            try { expenseRows = JSON.parse(expenseJson) || []; } catch (_e) {}

            const titleEl = document.getElementById('viewPendapatanLabel');
            if (titleEl) titleEl.textContent = 'Rekap Pendaftaran: ' + name;
            const incomeTbody = document.getElementById('incomeTbody');
            const expenseTbody = document.getElementById('expenseTbody');
            const profitFormula = document.getElementById('profitFormula');
            const profitTotalEl = document.getElementById('profitTotal');

            function fmt(n){ try { return new Intl.NumberFormat('id-ID').format(Math.round((n||0))); } catch(_e){ return (n||0); } }
            const rp = (v) => `Rp${fmt(v)}`;

            if (incomeTbody) {
                // Fallback: if no income rows provided, synthesize 'Tiket Pendaftar'
                if (!Array.isArray(incomeRows) || incomeRows.length === 0) {
                    const unit = registeredCount > 0 ? (incomeTotal / registeredCount) : 0;
                    incomeRows = [{ label: 'Tiket Pendaftar', qty: registeredCount, unit: unit, total: incomeTotal }];
                }
                const rowsHtml = incomeRows.map(r => `
                    <tr>
                        <td>${(r.label||'').toString()}</td>
                        <td>${fmt(r.qty||0)}</td>
                        <td>${fmt(r.unit||0)}</td>
                        <td>${fmt(r.total||0)}</td>
                    </tr>`).join('');
                const totalHtml = `<tr class="row-harga"><td>Total</td><td></td><td></td><td>${fmt(incomeTotal)}</td></tr>`;
                incomeTbody.innerHTML = rowsHtml + totalHtml;
            }

            if (expenseTbody) {
                const rowsHtml = (expenseRows.length ? expenseRows : []).map(r => `
                    <tr>
                        <td>${(r.label||'').toString()}</td>
                        <td>${fmt(r.qty||0)}</td>
                        <td>${rp(r.unit||0)}</td>
                        <td>${rp(r.total||0)}</td>
                    </tr>`).join('');
                const totalHtml = `<tr class="row-harga"><td>Total</td><td></td><td></td><td>${rp(expenseTotal)}</td></tr>`;
                expenseTbody.innerHTML = rowsHtml + totalHtml;
            }

            if (profitFormula) {
                profitFormula.textContent = `${rp(incomeTotal)} - ${rp(expenseTotal)}`;
            }
            if (profitTotalEl) {
                profitTotalEl.textContent = rp(profitTotal);
            }
        });
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const exportButtons = document.querySelectorAll('[data-export-tab]');
    const modalEl = document.getElementById('exportReportModal');
    if (!modalEl || exportButtons.length === 0) return;

    const titleEl = document.getElementById('exportReportTitle');
    const monthEl = document.getElementById('exportReportMonth');
    const previewEl = document.getElementById('exportReportPreview');
    const previewWrapperEl = document.getElementById('exportReportPreviewWrapper');
    const btnPdf = document.getElementById('btnExportPdf');
    const btnExcel = document.getElementById('btnExportExcel');

    let currentExport = { tab: null, title: null, period: null };

    const tabConfig = {
        pendapatan: {
            title: 'Pendapatan',
            tableId: 'table-pendapatan',
            monthLabelId: 'month-label-pendapatan',
            periodInputId: 'period',
        },
        pertumbuhan: {
            title: 'Pertumbuhan',
            tableId: 'table-pertumbuhan',
            monthLabelId: 'month-label-pertumbuhan',
            periodInputId: 'period_pertumbuhan',
        },
        operasional: {
            title: 'Operasional',
            tableId: 'table-operasional',
            monthLabelId: 'month-label-operasional',
            periodInputId: 'period_op',
        },
    };

    function getPeriodValue(tab){
        const cfg = tabConfig[tab];
        const input = cfg?.periodInputId ? document.getElementById(cfg.periodInputId) : null;
        const value = input && typeof input.value === 'string' ? input.value.trim() : '';
        return value !== '' ? value : null;
    }

    function sanitizeFilenamePart(str){
        return String(str || '').replace(/[^a-z0-9-_]+/gi,'-').replace(/-+/g,'-').replace(/(^-|-$)/g,'').toLowerCase();
    }

    function removeColumnsByHeaderText(table, matcher){
        const headRow = table.tHead && table.tHead.rows && table.tHead.rows[0] ? table.tHead.rows[0] : null;
        if (!headRow) return;

        const indices = [];
        Array.from(headRow.cells).forEach((th, idx) => {
            const txt = (th.textContent || '').trim();
            if (matcher.test(txt)) indices.push(idx);
        });
        if (indices.length === 0) return;

        // remove from end to start to keep indices stable
        indices.sort((a,b) => b-a);
        const removeCellsAt = (row) => {
            indices.forEach(i => {
                if (row.cells && row.cells[i]) row.deleteCell(i);
            });
        };
        // head
        removeCellsAt(headRow);
        // bodies
        Array.from(table.tBodies || []).forEach(tb => {
            Array.from(tb.rows || []).forEach(tr => removeCellsAt(tr));
        });
    }

    function cleanupInteractiveElements(root){
        // Replace buttons/links with plain text; remove svg icons.
        root.querySelectorAll('svg').forEach(svg => svg.remove());
        root.querySelectorAll('button').forEach(btn => {
            const span = document.createElement('span');
            span.textContent = (btn.textContent || '').trim();
            btn.replaceWith(span);
        });
        root.querySelectorAll('a').forEach(a => {
            const span = document.createElement('span');
            span.textContent = (a.textContent || '').trim();
            a.replaceWith(span);
        });
    }

    function cloneVisibleTable(tab){
        const cfg = tabConfig[tab];
        if (!cfg) return null;
        const source = document.getElementById(cfg.tableId);
        if (!source) return null;

        const visibleRowIdx = new Set();
        const sourceBody = source.tBodies && source.tBodies[0] ? source.tBodies[0] : null;
        if (sourceBody) {
            Array.from(sourceBody.rows).forEach((tr, idx) => {
                const style = window.getComputedStyle(tr);
                if (style && style.display !== 'none') visibleRowIdx.add(idx);
            });
        }

        const cloned = source.cloneNode(true);
        cloned.removeAttribute('id');

        const clonedBody = cloned.tBodies && cloned.tBodies[0] ? cloned.tBodies[0] : null;
        if (clonedBody) {
            for (let i = clonedBody.rows.length - 1; i >= 0; i--) {
                if (!visibleRowIdx.has(i)) clonedBody.deleteRow(i);
            }
        }

        // Remove action/detail columns
        removeColumnsByHeaderText(cloned, /^(aksi|detail)$/i);

        cleanupInteractiveElements(cloned);

        // Slightly improve preview readability
        cloned.classList.add('table', 'table-sm');
        cloned.querySelectorAll('th').forEach(th => {
            th.style.backgroundColor = '#E4E4E6';
        });
        return cloned;
    }

    function openExport(tab){
        const cfg = tabConfig[tab];
        if (!cfg) return;

        const monthLabel = document.getElementById(cfg.monthLabelId);
        const monthText = monthLabel ? (monthLabel.textContent || '').trim() : '-';
        const period = getPeriodValue(tab);

        const table = cloneVisibleTable(tab);
        previewEl.innerHTML = '';
        titleEl.textContent = 'Laporan ' + cfg.title;
        monthEl.textContent = monthText;

        if (!table || !table.tBodies || table.tBodies.length === 0 || table.tBodies[0].rows.length === 0) {
            previewEl.innerHTML = '<div class="text-muted p-3">Tidak ada data untuk diexport.</div>';
            btnPdf.disabled = true;
            btnExcel.disabled = true;
        } else {
            // Style tabel untuk preview
            table.classList.add('table', 'table-sm');
            table.style.borderCollapse = 'collapse';
            table.style.width = '100%';
            table.style.fontSize = '13px';
            table.querySelectorAll('th').forEach(th => {
                th.style.backgroundColor = '#1e3a5f';
                th.style.color = '#ffffff';
                th.style.padding = '8px 10px';
                th.style.border = '1px solid #1e3a5f';
                th.style.fontWeight = '600';
                th.style.whiteSpace = 'nowrap';
            });
            table.querySelectorAll('td').forEach((td, i) => {
                td.style.padding = '7px 10px';
                td.style.border = '1px solid #e5e7eb';
                td.style.verticalAlign = 'middle';
            });
            // Zebra stripe
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach((tr, i) => {
                tr.style.backgroundColor = i % 2 === 0 ? '#ffffff' : '#f8f9fa';
            });

            const wrapper = document.createElement('div');
            wrapper.style.padding = '16px';
            wrapper.style.background = '#fff';

            // Header preview
            const headerHtml = `
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding-bottom:10px; border-bottom:2px solid #1e3a5f;">
                    <div>
                        <div style="font-size:18px; font-weight:700; color:#1e3a5f;">Laporan ${cfg.title} Event</div>
                        <div style="font-size:12px; color:#6b7280; margin-top:2px;">Periode: <strong>${monthText}</strong></div>
                    </div>
                    <div style="font-size:11px; color:#9ca3af; text-align:right;">
                        Dicetak: ${new Date().toLocaleDateString('id-ID', {day:'2-digit',month:'long',year:'numeric'})}<br>
                        LMS IdSpora Admin
                    </div>
                </div>`;

            wrapper.innerHTML = headerHtml;
            wrapper.appendChild(table);
            previewEl.appendChild(wrapper);
            btnPdf.disabled = false;
            btnExcel.disabled = false;
        }

        currentExport = { tab, title: cfg.title, period, monthText };

        const bsModal = window.bootstrap?.Modal ? new window.bootstrap.Modal(modalEl) : null;
        if (bsModal) {
            bsModal.show();
        } else {
            modalEl.classList.add('show');
            modalEl.style.display = 'block';
        }
    }

    exportButtons.forEach(btn => {
        btn.addEventListener('click', function(){
            const tab = this.getAttribute('data-export-tab');
            openExport(tab);
        });
    });

    btnPdf.addEventListener('click', function(){
        const periodPart = currentExport.period ? sanitizeFilenamePart(currentExport.period) : 'periode';
        const tabPart = sanitizeFilenamePart(currentExport.title);
        const filename = `laporan-event-${tabPart}-${periodPart}.pdf`;
        if (typeof window.html2pdf !== 'function') return;

        const table = previewEl.querySelector('table');
        if (!table) return;
        const tableClone = table.cloneNode(true);

        // Style tabel untuk PDF
        tableClone.style.borderCollapse = 'collapse';
        tableClone.style.width = '100%';
        tableClone.style.fontSize = '11px';
        tableClone.querySelectorAll('th').forEach(th => {
            th.style.backgroundColor = '#1e3a5f';
            th.style.color = '#ffffff';
            th.style.padding = '7px 9px';
            th.style.border = '1px solid #1e3a5f';
            th.style.fontWeight = '600';
        });
        tableClone.querySelectorAll('td').forEach(td => {
            td.style.padding = '6px 9px';
            td.style.border = '1px solid #d1d5db';
            td.style.verticalAlign = 'middle';
        });
        const rows = tableClone.querySelectorAll('tbody tr');
        rows.forEach((tr, i) => {
            tr.style.backgroundColor = i % 2 === 0 ? '#ffffff' : '#f3f4f6';
        });

        const monthText = currentExport.monthText || (monthEl?.textContent || '-');
        const printDate = new Date().toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'});

        const printable = document.createElement('div');
        printable.style.cssText = 'width:1100px; padding:24px 28px; background:#ffffff; color:#111827; font-family:Arial,sans-serif; box-sizing:border-box;';

        printable.innerHTML = `
            <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:18px; padding-bottom:12px; border-bottom:3px solid #1e3a5f;">
                <div>
                    <div style="font-size:20px; font-weight:700; color:#1e3a5f; letter-spacing:-0.3px;">Laporan ${currentExport.title || ''} Event</div>
                    <div style="font-size:12px; color:#6b7280; margin-top:4px;">Periode: <strong style="color:#111827;">${monthText}</strong></div>
                </div>
                <div style="text-align:right; font-size:11px; color:#9ca3af; line-height:1.6;">
                    <div style="font-weight:600; color:#374151; font-size:13px;">LMS IdSpora</div>
                    <div>Dicetak: ${printDate}</div>
                </div>
            </div>`;

        printable.appendChild(tableClone);

        const footer = document.createElement('div');
        footer.style.cssText = 'margin-top:16px; padding-top:10px; border-top:1px solid #e5e7eb; font-size:10px; color:#9ca3af; display:flex; justify-content:space-between;';
        footer.innerHTML = `<span>LMS IdSpora — Laporan ${currentExport.title || ''} Event</span><span>${printDate}</span>`;
        printable.appendChild(footer);

        const offscreen = document.createElement('div');
        offscreen.style.cssText = 'position:fixed; left:-10000px; top:0;';
        offscreen.appendChild(printable);
        document.body.appendChild(offscreen);

        const opt = {
            margin: [8, 6, 8, 6],
            filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, windowWidth: 1160, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        window.html2pdf().set(opt).from(printable).save().then(() => {
            offscreen.remove();
        }).catch(() => {
            offscreen.remove();
        });
    });

    btnExcel.addEventListener('click', function(){
        if (!window.XLSX) return;
        const table = previewEl.querySelector('table');
        if (!table) return;
        const periodPart = currentExport.period ? sanitizeFilenamePart(currentExport.period) : 'periode';
        const tabPart = sanitizeFilenamePart(currentExport.title);
        const filename = `laporan-event-${tabPart}-${periodPart}.xlsx`;
        const monthText = currentExport.monthText || '-';
        const printDate = new Date().toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'});

        const wb = window.XLSX.utils.book_new();

        // Buat sheet dengan header info di baris pertama
        const ws = window.XLSX.utils.aoa_to_sheet([
            [`Laporan ${currentExport.title || ''} Event`],
            [`Periode: ${monthText}`],
            [`Dicetak: ${printDate}`],
            [], // baris kosong
        ]);

        // Append data tabel
        const tableSheet = window.XLSX.utils.table_to_sheet(table);
        const tableData = window.XLSX.utils.sheet_to_json(tableSheet, { header: 1 });
        tableData.forEach((row, i) => {
            window.XLSX.utils.sheet_add_aoa(ws, [row], { origin: { r: 4 + i, c: 0 } });
        });

        // Style header baris info (merge + bold)
        if (!ws['!merges']) ws['!merges'] = [];
        ws['!merges'].push({ s: { r: 0, c: 0 }, e: { r: 0, c: 6 } });

        // Set column widths
        ws['!cols'] = Array(10).fill({ wch: 20 });

        window.XLSX.utils.book_append_sheet(wb, ws, currentExport.title || 'Report');
        window.XLSX.writeFile(wb, filename);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    try {
        const periodInputs = [
            document.getElementById('period'),
            document.getElementById('period_pertumbuhan'),
            document.getElementById('period_op'),
        ].filter(Boolean);

        function formatMonthLabel(ym){
            if(!ym) return '-';
            const parts = ym.split('-');
            if(parts.length !== 2) return ym;
            const y = Number(parts[0]), m = Number(parts[1]);
            const dt = new Date(y, m - 1, 1);
            try {
                return new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' }).format(dt);
            } catch (e) {
                return dt.toLocaleString();
            }
        }

        function setAllPeriods(value){
            if(!value) return;
            periodInputs.forEach(inp => { if(inp && inp.value !== value) inp.value = value; });
            const label = formatMonthLabel(value);
            const labPend = document.getElementById('month-label-pendapatan');
            const labPert = document.getElementById('month-label-pertumbuhan');
            const labOp = document.getElementById('month-label-operasional');
            if(labPend) labPend.textContent = label;
            if(labPert) labPert.textContent = label;
            if(labOp) labOp.textContent = label;

            // Also set per-tab date-from / date-to inputs so client-side table filters reflect the whole month
            const parts = (value || '').split('-');
            if(parts.length === 2){
                const y = Number(parts[0]);
                const m = Number(parts[1]);
                const mm = String(m).padStart(2,'0');
                const firstDay = `${y}-${mm}-01`;
                const lastDate = new Date(y, m, 0).getDate();
                const lastDay = `${y}-${mm}-${String(lastDate).padStart(2,'0')}`;

                const mapping = [
                    ['date-from-pendapatan','date-to-pendapatan'],
                    ['date-from-pertumbuhan','date-to-pertumbuhan'],
                    ['date-from-operasional','date-to-operasional'],
                ];
                mapping.forEach(([fromId,toId]) => {
                    const fromEl = document.getElementById(fromId);
                    const toEl = document.getElementById(toId);
                    if(fromEl) fromEl.value = firstDay;
                    if(toEl) toEl.value = lastDay;
                });

                // Apply client-side filters immediately so the visible tables reflect the chosen month
                if(window.applyAllFilters) window.applyAllFilters();
            }
        }

        let submitTimer = null;
        function submitActiveTabAfterDelay(delay = 300){
            clearTimeout(submitTimer);
            submitTimer = setTimeout(()=>{
                const activeBtn = document.querySelector('.btn-report.active');
                const activeTab = activeBtn?.getAttribute('data-target') || 'pendapatan';
                const form = document.querySelector('#' + activeTab + ' form');
                if(form){
                    const hiddenTab = form.querySelector('input[name="tab"]');
                    if(hiddenTab) hiddenTab.value = activeTab;
                    form.submit();
                }
            }, delay);
        }

        periodInputs.forEach(inp => {
            inp.addEventListener('change', function(){
                const v = this.value;
                setAllPeriods(v);
                // auto submit to refresh lists based on selected month
                submitActiveTabAfterDelay(400);
            });
        });

        // Sync on load from server-rendered values
        (function init(){
            const initial = periodInputs.find(i => i && i.value)?.value;
            if(initial) setAllPeriods(initial);
        })();

        // When switching tabs client-side, ensure inputs and labels reflect current period
        const tabButtons = document.querySelectorAll('.btn-report');
        tabButtons.forEach(btn => btn.addEventListener('click', function(){
            const active = document.querySelector('.btn-report.active');
            const activeTarget = active?.getAttribute('data-target') || 'pendapatan';
            const activePeriodInp = document.querySelector('#' + activeTarget + ' input[type="month"]');
            const val = activePeriodInp?.value || periodInputs.find(i => i && i.value)?.value;
            if(val) setAllPeriods(val);
        }));
    } catch(e){
        console.error('period sync error', e);
    }
});
</script>

@endsection
