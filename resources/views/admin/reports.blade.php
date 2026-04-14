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
            <button class="btn-report" data-target="operasional">Operasional</button>
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
            <div class="card mb-3">
                <div class="card-body">
                    <canvas id="laporanChart" height="90"></canvas>
                </div>
            </div>

            <div style="margin-bottom:12px;">
            </div>

            <div class="recap-card-box" style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin:14px 0;">
                <!-- Total Pendapatan -->
                <div class="recap-card" style="border:1px solid #eee; border-radius:10px; padding:12px;">
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
                <div class="recap-card" style="border:1px solid #eee; border-radius:10px; padding:12px;">
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
                <div class="recap-card" style="border:1px solid #eee; border-radius:10px; padding:12px;">
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
                    <div class="filter-kiri" style="display:flex; gap:10px; align-items:flex-end;">
                        <div class="filter-group">
                            <label for="filter-event-pendapatan" class="filter-label">Cari Event</label>
                            <input type="text" id="filter-event-pendapatan" class="filter-input" placeholder="Cari nama event...">
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
                            // Selalu generate $eventRows di sini agar tidak ada cache lama dari controller
                            $paidStatuses = ['settlement','capture','success'];
                            $revenueMap = \App\Models\ManualPayment::query()
                                ->selectRaw('event_id, SUM(amount) as total')
                                ->where('status','settled')
                                ->groupBy('event_id')
                                ->pluck('total','event_id');
                            // Ambil event yang event_date-nya tidak null dan sesuai bulan/tahun yang dipilih
                            $eventsTmp = \App\Models\Event::withCount('registrations')
                                ->whereNotNull('event_date')
                                ->whereYear('event_date', $selectedDate->year)
                                ->whereMonth('event_date', $selectedDate->month)
                                ->orderBy('event_date','desc')->get();
                            $eventRows = $eventsTmp->map(function($e) use ($revenueMap){
                                $price = $e->discounted_price ?? $e->price;
                                $payments = \App\Models\ManualPayment::where('event_id',$e->id)->where('status','settled')->get();
                                $revenue = (float) $payments->sum('amount');
                                $registeredCount = (int) $e->registrations()->where('status','active')->count();
                                $avgUnit = $registeredCount > 0 ? (float) round($revenue / $registeredCount, 2) : 0.0;
                                $incomeRows = [
                                    [ 'label' => 'Tiket Pendaftar', 'qty' => $registeredCount, 'unit' => $avgUnit, 'total' => (float)$revenue ],
                                ];
                                $expenseModels = $e->expenses()->get(['item','quantity','unit_price','total']);
                                $expenseRows = $expenseModels->map(function($row){
                                    return [
                                        'label' => $row->item,
                                        'qty' => (int)($row->quantity ?? 0),
                                        'unit' => (float)($row->unit_price ?? 0),
                                        'total' => (float)($row->total ?? 0),
                                    ];
                                })->values()->all();
                                $expense = (float) array_sum(array_map(fn($r)=> (float)($r['total'] ?? 0), $expenseRows));
                                return [
                                    'id' => $e->id,
                                    'name' => $e->title,
                                    'date' => optional($e->event_date)->format('d/m/Y'),
                                    'participants' => (int)$e->registrations_count,
                                    'registered_count' => $registeredCount,
                                    'price' => (float)$price,
                                    'revenue' => $revenue,
                                    'expense' => $expense,
                                    'profit' => $revenue - $expense,
                                    'income_rows' => $incomeRows,
                                    'expense_rows' => $expenseRows,
                                ];
                            });
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
            </div>
        </div>

        <div id="pertumbuhan" class="rekap-box">
            <div class="box-diagram-pertumbuhan">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div style="height: 300px ; position: relative;">
                            <canvas id="chartEvent"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="data-box">
                <div class="data-pengguna">
                    <div class="data-pengguna-kiri">
                        @php
                            // Pengguna Baru: user yang pertama kali mendaftar event pada bulan terpilih.
                            // Ambil earliest registration (MIN(created_at)) per user dan filter di DB via HAVING.
                            $newUsersCurrent = \App\Models\EventRegistration::selectRaw('user_id, MIN(created_at) as first_created')
                                ->groupBy('user_id')
                                ->havingRaw('YEAR(MIN(created_at)) = ? AND MONTH(MIN(created_at)) = ?', [$selectedDate->year, $selectedDate->month])
                                ->get()->count();
                            $newUsersPrev = \App\Models\EventRegistration::selectRaw('user_id, MIN(created_at) as first_created')
                                ->groupBy('user_id')
                                ->havingRaw('YEAR(MIN(created_at)) = ? AND MONTH(MIN(created_at)) = ?', [$prevDate->year, $prevDate->month])
                                ->get()->count();
                            // Growth pengguna baru (gunakan logika earliest baseline sama seperti growth lain)
                            $growthUsers = function($curr,$prev, Carbon $prevDate, Carbon $earliestDate){
                                if($prevDate->lt($earliestDate)) return 0; // sebelum earliest -> 0%
                                if($prev == 0){ return $curr == 0 ? 0 : 100; }
                                return (($curr - $prev)/($prev))*100;
                            };
                            $usersGrowth = $growthUsers($newUsersCurrent,$newUsersPrev,$prevDate,$earliestDate);
                            $usersUp = $usersGrowth >= 0;
                            $usersPctAbs = round(abs($usersGrowth),1);
                        @endphp
                        <h5>Pengguna Baru</h5>
                        <h4>{{ $newUsersCurrent }}</h4>
                        <div class="deskripsi-kenaikan" style="display:flex;align-items:center;gap:6px;color:{{ $usersUp ? 'green':'red' }};">
                            @if($usersUp)
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                                </svg>
                            @endif
                            <p class="mb-0">{{ $usersPctAbs }}% vs bulan lalu</p>
                        </div>
                        <small class="text-muted">Hanya hitung user pertama kali mendaftar bulan ini.</small>
                    </div>
                    <div class="data-pengguna-kanan">
                        <div class="pertumbuhan-acara">
                            <h5>Pertumbuhan Acara</h5>
                            <div class="pertumbuhan-box">
                                <div class="pertumbuhan-box-isi">
                                    <h4>{{ $totalPaidEventsSelected ?? 0 }}</h4>
                                    <p>Total Event Berbayar</p>
                                </div>
                                <div class="pertumbuhan-box-isi">
                                    <h4>{{ $totalFreeEventsSelected ?? 0 }}</h4>
                                    <p>Total Event Free</p>
                                </div>
                                @php
                                    // Total Peserta: distinct users who registered in the selected month
                                    $totalParticipantsUsers = \App\Models\EventRegistration::query()
                                        ->whereYear('created_at', $selectedDate->year)
                                        ->whereMonth('created_at', $selectedDate->month)
                                        ->where('status','active')
                                        ->distinct('user_id')
                                        ->count('user_id');
                                @endphp
                                <div class="pertumbuhan-box-isi">
                                    <h4>{{ number_format($totalParticipantsUsers) }}</h4>
                                    <p>Total Peserta</p>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h5 class="title-laporan-metrik">Metrik Operasional Rinci</h5>
            <div class="filter-section" id="filters-pertumbuhan">
                <div class="filter-kiri">
                    <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="tab" value="pertumbuhan">
                        <input type="text" id="filter-event-pertumbuhan" class="filter-input" placeholder="Cari nama event..." style="width:200px">
                        
                        <label for="period_pertumbuhan" class="filter-label ms-2 mb-0">Periode</label>
                        <input type="month" name="period" id="period_pertumbuhan" value="{{ $selectedDate->format('Y-m') }}" class="form-control form-control-sm" style="width:150px">
                        
                        <button type="submit" class="btn btn-primary btn-sm ms-2">Tampilkan</button>
                    </form>
                </div>
                <div class="filter-kanan d-flex align-items-end gap-2">
                    <!-- Additional JS-based Date Range (Optional, currently reused for table filtering) -->
                    <!-- We keep them but hidden or secondary if Month Filter is primary -->
                    <div class="filter-group d-none">
                         <!-- Hide these if we rely on Controller Month Filter -->
                        <label for="date-from-pertumbuhan" class="filter-label">Dari</label>
                         <input type="date" id="date-from-pertumbuhan" class="filter-input">
                    </div>
                    <div class="small text-muted">Menampilkan data bulan: <strong id="month-label-pertumbuhan">{{ $selectedDate->translatedFormat('F Y') }}</strong></div>
                    <button type="button" class="btn-export-report btn btn-sm" data-export-tab="pertumbuhan">Export</button>
                </div>
            </div>
            <table class="tabel-pendapatan" id="table-pertumbuhan">
                <thead>
                    <tr>
                        <th style="background-color: #E4E4E6;" scope="col">Nama Event</th>
                        <th style="background-color: #E4E4E6;" scope="col">Tanggal</th>
                        <th style="background-color: #E4E4E6;" scope="col">Peserta</th>
                        <th style="background-color: #E4E4E6;" scope="col">Pembicara</th>
                        <th style="background-color: #E4E4E6;" scope="col">Rating Acara</th>
                        <th style="background-color: #E4E4E6;" scope="col">Rating Pembicara</th>
                        <th style="background-color: #E4E4E6;" scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $growthRows = $growthRows ?? \App\Models\Event::withCount('registrations')
                            ->orderBy('event_date','desc')
                            ->get()
                            ->map(function($e){
                                return [
                                    'id' => $e->id,
                                    'name' => $e->title,
                                    'date' => optional($e->event_date)->format('d/m/Y'),
                                    'participants' => (int)$e->registrations_count,
                                    'speaker' => $e->speaker,
                                    'event_rating' => null,
                                    'speaker_rating' => null,
                                ];
                            });
                    @endphp
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
                                @if(isset($row['event_rating']) && $row['event_rating'] !== null)
                                    {{ $row['event_rating'] }}/5
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if(isset($row['speaker_rating']) && $row['speaker_rating'] !== null)
                                    {{ $row['speaker_rating'] }}/5
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada data event</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div id="operasional" class="rekap-box">
            <div>
                <h5>Aktivitas Acara</h5>
                <div class="box-diagram-operasional">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h6 class="mb-3">Total Event Create vs Manage</h6>
                            <div style="height: 300px; position: relative;">
                                <canvas id="chartEventCreateManage"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 mb-4">
                    <form method="GET" action="{{ url()->current() }}" class="d-flex flex-wrap align-items-end gap-2">
                        <input type="hidden" name="tab" value="operasional">
                        <div>
                            <label for="period_op" class="form-label mb-1 text-dark">Periode Bulan</label>
                            <input type="month" name="period" id="period_op" value="{{ $selectedDate->format('Y-m') }}" class="form-control" style="max-width:180px;">
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
                
                <div class="info-operasional-box" style="display:flex; gap:12px;">
                    <div class="info-operasional" style="border:1px solid #eee; border-radius:10px; padding:12px;">
                        <h4>{{ $totalCreateEventsSelected ?? 0 }}</h4>
                        <p>Total Event Create</p>
                    </div>
                    <div class="info-operasional" style="border:1px solid #eee; border-radius:10px; padding:12px;">
                        <h4>{{ $totalManageEventsSelected ?? 0 }}</h4>
                        <p>Total Event Manage</p>
                    </div>
                </div>
                <div class="proggress-box-operasional">
                    <div class="proggress-operasional">
                        <p class="title-prog-operasional">Presentase Event Terlaksana</p>
                            <div class="line-proggress-abu"><div class="line-proggress" style="width: {{ $percentCompleted ?? 0 }}%; max-width:100%"></div></div>
                            <p>{{ $percentCompleted ?? 0 }}% Selesai</p>
                    </div>
                    <div class="proggress-operasional">
                        <p class="title-prog-operasional">Presentase Event Belum Terlaksana</p>
                            <div class="line-proggress-abu"><div class="line-proggress" style="width: {{ $percentNotCompleted ?? 0 }}%; max-width:100%"></div></div>
                            <p>{{ $percentNotCompleted ?? 0 }}% Belum</p>
                    </div>
                </div>

                <div>
                    <h5>Manajemen Dokumen Per Event</h5>
                    <div class="filter-section" id="filters-operasional">
                        <div class="filter-kiri">
                            <div class="filter-group">
                                <label for="filter-event-operasional" class="filter-label">Cari Event</label>
                                <input type="text" id="filter-event-operasional" class="filter-input" placeholder="Cari nama event...">
                            </div>
                        </div>
                            <div class="filter-kanan">
                            <div class="filter-group">
                                <label for="date-from-operasional" class="filter-label">Dari Tanggal</label>
                                <div class="filter-date-group">
                                    <input type="date" id="date-from-operasional" class="filter-input">
                                </div>
                            </div>
                            <div class="filter-group">
                                <label for="date-to-operasional" class="filter-label">Sampai Tanggal</label>
                                <div class="filter-date-group">
                                    <input type="date" id="date-to-operasional" class="filter-input">
                                </div>
                            </div>
                            <div class="filter-actions"><button type="button" class="btn-apply btn-reset" id="btn-reset-operasional" style="background:#6c757d;">Reset</button></div>
                        </div>
                    </div>
                    <table class="tabel-pendapatan" id="table-operasional">
                        <thead>
                            <tr>
                                <th style="background-color: #E4E4E6;" scope="col">Nama Event</th>
                                <th style="background-color: #E4E4E6;" scope="col">Tanggal</th>
                                <th style="background-color: #E4E4E6;" scope="col">Jenis Kegiatan</th>
                                <th style="background-color: #E4E4E6;" scope="col">Progress Dokumen</th>
                                <th style="background-color: #E4E4E6;" scope="col">Status Kelengkapan Dokumen</th>
                                <th style="background-color: #E4E4E6;" scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                if(!isset($operationalRows)) {
                                    $operationalRows = \App\Models\Event::query()
                                        ->orderBy('event_date','desc')
                                        ->get()
                                        ->map(function($e){
                                            return [
                                                'id' => $e->id,
                                                'name' => $e->title,
                                                'date' => optional($e->event_date)->format('d/m/Y'),
                                                'type' => $e->jenis ?? 'N/A',
                                                'documents_percent' => $e->documents_completion_percent,
                                                'vbg_url' => !empty($e->vbg_path) ? ($e->vbg_file_url ?? '') : '',
                                                'cert_url' => !empty($e->certificate_path) ? Storage::url($e->certificate_path) : '',
                                                'module_url' => !empty($e->module_path) ? ($e->module_file_url ?? '') : '',
                                                'abs_url' => !empty($e->attendance_path) ? Storage::url($e->attendance_path) : '',
                                                // attendance QR data
                                                'qr_token' => $e->attendance_qr_token,
                                                'qr_url' => $e->attendance_qr_token ? url('/events/'.$e->id.'?t='.$e->attendance_qr_token) : null,
                                                'qr_image_url' => $e->attendance_qr_image_url,
                                            ];
                                        });
                                }
                            @endphp
                            @forelse($operationalRows as $row)
                                <tr data-name="{{ Str::lower($row['name']) }}" data-date="{{ isset($row['date']) ? \Carbon\Carbon::createFromFormat('d/m/Y',$row['date'])->format('Y-m-d') : '' }}" data-type="{{ Str::lower($row['type']) }}" data-docs="{{ $row['documents_percent'] }}">
                                    <td>{{ $row['name'] }}</td>
                                    <td>{{ $row['date'] ?? '-' }}</td>
                                    <td>{{ $row['type'] }}</td>
                                    <td>
                                        <h3 class="add-dokumen" 
                                            data-bs-id="{{ $row['id'] }}"
                                            data-vbg="{{ $row['vbg_url'] ?? '' }}"
                                            data-cert="{{ $row['cert_url'] ?? '' }}"
                                            data-module="{{ $row['module_url'] ?? '' }}"
                                            data-abs="{{ $row['abs_url'] ?? '' }}"
                                            data-qr-img="{{ $row['qr_image_url'] ?? '' }}"
                                        >
                                            {{ $row['documents_percent'] }}%
                                        </h3>
                                    </td>
                                    <td>
                                        @if($row['documents_percent'] == 100)
                                            <span class="status-lengkap">Lengkap</span>
                                        @elseif($row['documents_percent'] > 0)
                                            <span class="status-kurang-lengkap">Kurang Lengkap</span>
                                        @else
                                            <span class="status-tidak-lengkap">Tidak Lengkap</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $ev = isset($row['id']) ? \App\Models\Event::find($row['id']) : null;
                                            $qrToken = $ev?->attendance_qr_token ?: '';
                                            $qrUrl = $qrToken ? url('/events/'.$ev->id.'?t='.$qrToken) : '';
                                            $qrImageUrl = ($ev && $ev->attendance_qr_image) ? ($ev->attendance_qr_image_url ?? '') : '';
                                        @endphp
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal" data-name="{{ $row['name'] }}" data-vbg="{{ $row['vbg_url'] ?? '' }}" data-cert="{{ $row['cert_url'] ?? '' }}" data-module="{{ $row['module_url'] ?? '' }}" data-abs="{{ $row['abs_url'] ?? '' }}" data-qr="{{ $qrUrl }}" data-qr-img="{{ $qrImageUrl }}">
                                            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                        </svg>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada data event</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
    <div class="modal-view-operasional modal fade" id="viewOperasionalModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="content-operasional-view modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewOperasionalTitle">Status Dokumen Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Tinjau status semua dokumen terkait acara dan administrasi.</p>
                    <div id="operasionalStatusContainer" class="d-flex flex-column gap-2"></div>
                </div>
            </div>
        </div>
    </div>
   
</div>
@endsection
@section('scripts')



<script>

    //diagram report pertumbuhan event (Free vs Paid Trend)
document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById('chartEvent');
    if(ctx){
        // Data from controller
        const labels = @json($chartLabels ?? []);
        const freeData = @json($chartFreeData ?? []);
        const paidData = @json($chartPaidData ?? []);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Event Free',
                        data: freeData,
                        borderColor: '#6f42c1', // Purple
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3
                    },
                    {
                        label: 'Total Event Berbayar',
                        data: paidData,
                        borderColor: '#fd7e14', // Orange
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        tension: 0.4,
                        pointRadius: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true }
                    }
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
            const name = (row.getAttribute('data-name')||'').toLowerCase();
            const date = row.getAttribute('data-date');
            const searchVal = (searchInput?.value || '').toLowerCase().trim();
            const fromVal = dateFromInput?.value || '';
            const toVal = dateToInput?.value || '';
            // Name filter
            if(searchVal && !name.includes(searchVal)) return false;
            // Date range filter
            if(date){
                if(fromVal && date < fromVal) return false;
                if(toVal && date > toVal) return false;
            }
            return true;
        }
        function apply(){
            rows.forEach(r => {
                if(matches(r)){
                    r.style.display='';
                } else {
                    r.style.display='none';
                }
            });
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

        const header = document.createElement('div');
        header.style.marginBottom = '10px';
        header.innerHTML = `
            <div style="font-weight:700; font-size:16px;">${titleEl.textContent}</div>
            <div class="small text-muted">Menampilkan data bulan: <strong>${monthText}</strong></div>
        `;

        if (!table || !table.tBodies || table.tBodies.length === 0 || table.tBodies[0].rows.length === 0) {
            previewEl.innerHTML = '<div class="text-muted">Tidak ada data untuk diexport.</div>';
            btnPdf.disabled = true;
            btnExcel.disabled = true;
        } else {
            // ensure borders for preview readability
            table.querySelectorAll('th, td').forEach(cell => {
                cell.style.border = '1px solid #e5e7eb';
            });
            table.style.borderCollapse = 'collapse';
            table.style.width = '100%';

            previewEl.appendChild(header);
            previewEl.appendChild(table);
            btnPdf.disabled = false;
            btnExcel.disabled = false;
        }

        currentExport = { tab, title: cfg.title, period };

        const bsModal = window.bootstrap?.Modal ? new window.bootstrap.Modal(modalEl) : null;
        if (bsModal) {
            bsModal.show();
        } else {
            // Fallback (shouldn't happen in admin)
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
        const filename = `report-${tabPart}-${periodPart}.pdf`;
        if (typeof window.html2pdf !== 'function') return;

        // Build a clean, full-width printable layout off-screen to avoid cramped modal sizing
        const printable = document.createElement('div');
        printable.style.width = '1120px';
        printable.style.padding = '16px';
        printable.style.background = '#ffffff';
        printable.style.color = '#111827';
        printable.style.fontSize = '12px';
        printable.style.boxSizing = 'border-box';

        const title = document.createElement('div');
        title.style.fontWeight = '700';
        title.style.fontSize = '16px';
        title.style.marginBottom = '4px';
        title.textContent = 'Laporan ' + (currentExport.title || '');

        const subtitle = document.createElement('div');
        subtitle.style.color = '#6B7280';
        subtitle.style.marginBottom = '12px';
        subtitle.innerHTML = 'Menampilkan data bulan: <strong>' + (monthEl?.textContent || '-') + '</strong>';

        const table = previewEl.querySelector('table');
        if (!table) return;
        const tableClone = table.cloneNode(true);
        tableClone.querySelectorAll('th, td').forEach(cell => {
            cell.style.border = '1px solid #e5e7eb';
            cell.style.padding = '6px 8px';
        });
        tableClone.style.borderCollapse = 'collapse';
        tableClone.style.width = '100%';
        tableClone.querySelectorAll('th').forEach(th => {
            th.style.backgroundColor = '#E4E4E6';
        });

        printable.appendChild(title);
        printable.appendChild(subtitle);
        printable.appendChild(tableClone);

        const offscreen = document.createElement('div');
        offscreen.style.position = 'fixed';
        offscreen.style.left = '-10000px';
        offscreen.style.top = '0';
        offscreen.appendChild(printable);
        document.body.appendChild(offscreen);

        const opt = {
            margin: 8,
            filename,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, windowWidth: 1120 },
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
        const filename = `report-${tabPart}-${periodPart}.xlsx`;
        const wb = window.XLSX.utils.book_new();
        const ws = window.XLSX.utils.table_to_sheet(table);
        window.XLSX.utils.book_append_sheet(wb, ws, currentExport.title || 'Report');
        window.XLSX.writeFile(wb, filename);
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@endsection
