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
                <div>
                    <label for="period" class="form-label mb-1">Periode Bulan</label>
                    <input type="month" name="period" id="period" value="{{ sprintf('%04d-%02d',$selectedYear,$selectedMonth) }}" class="form-control" style="max-width:180px;">
                </div>
                <div class="d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm" style="height:38px;">Tampilkan</button>
                    <a href="{{ $isPastPrev ? '#' : url()->current().'?period='.$periodFmt($prevDate) }}" class="btn btn-outline-secondary btn-sm {{ $isPastPrev ? 'disabled' : '' }}" style="height:38px;">&laquo; {{ $prevDate->translatedFormat('F Y') }}</a>
                    <a href="{{ $isFutureNext ? '#' : url()->current().'?period='.$periodFmt($nextDate) }}" class="btn btn-outline-secondary btn-sm {{ $isFutureNext ? 'disabled' : '' }}" style="height:38px;">{{ $nextDate->translatedFormat('F Y') }} &raquo;</a>
                </div>
                <div class="ms-auto small text-muted">Menampilkan data bulan: <strong>{{ $selectedDate->translatedFormat('F Y') }}</strong></div>
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
                <canvas id="trendChart" height="100"></canvas>
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
                <table class="tabel-pendapatan">
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
                                ->orderBy('event_date','asc')->get();
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
            @php
                // Prepare last 6 months labels ending with current month
                $months = [];
                $paidEvents = [];
                $freeEvents = [];
                $end = Carbon::createFromDate((int) now()->year, (int) now()->month, 1)->startOfMonth();
                $start = (clone $end)->subMonths(5);
                for($m = 0; $m < 6; $m++){
                    $d = (clone $start)->addMonths($m);
                    $months[] = $d->format('M');
                    $paidEvents[] = \App\Models\Event::whereYear('created_at',$d->year)->whereMonth('created_at',$d->month)->where('price','>',0)->count();
                    $freeEvents[] = \App\Models\Event::whereYear('created_at',$d->year)->whereMonth('created_at',$d->month)->where(function($q){ $q->whereNull('price')->orWhere('price',0); })->count();
                }
            @endphp
            <div style="margin-bottom:14px;">
                <canvas id="growthChart" height="110"></canvas>
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
                                @php
                                    // Count events scheduled in the current month (system time)
                                    $now = \Carbon\Carbon::now();
                                    $eventsThisMonth = \App\Models\Event::query()
                                        ->whereYear('event_date', $now->year)
                                        ->whereMonth('event_date', $now->month)
                                        ->count();
                                @endphp
                                <div class="pertumbuhan-box-isi">
                                    <h4>{{ $eventsThisMonth }}</h4>
                                    <p>Acara baru bulan ini</p>
                                    <div class="deskripsi-kenaikan">
                                        <svg class="naik" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="blue" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0  0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                        </svg>
                                        <p>12.0%</p>
                                    </div>
                                </div>
                                @php
                                    // Total Peserta: distinct users who registered for any event this current month
                                    $now = \Carbon\Carbon::now();
                                    $totalParticipantsUsers = \App\Models\EventRegistration::query()
                                        ->whereYear('created_at', $now->year)
                                        ->whereMonth('created_at', $now->month)
                                        ->where('status','active')
                                        ->distinct('user_id')
                                        ->count('user_id');
                                @endphp
                                <div class="pertumbuhan-box-isi">
                                    <h4>{{ number_format($totalParticipantsUsers) }}</h4>
                                    <p>Total Peserta</p>
                                </div>
                                <div class="pertumbuhan-box-isi">
                                    <h4>71.4</h4>
                                    <p>Tingkat Partisipasi Acara</p>
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
                    <div class="filter-group">
                        <label for="filter-event-pertumbuhan" class="filter-label">Cari Event</label>
                        <input type="text" id="filter-event-pertumbuhan" class="filter-input" placeholder="Cari nama event...">
                    </div>
                    <div>
                        <button class="btn-cari" id="btn-cari-pertumbuhan">cari</button>
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
                    <div class="filter-actions">
                        <button type="button" class="btn-apply btn-reset" id="btn-reset-pertumbuhan" style="background:#6c757d;">Reset</button>
                    </div>
                </div>
            </div>
            <table class="tabel-pendapatan">
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
                        <tr data-name="{{ Str::lower($row['name']) }}" data-date="{{ isset($row['date']) ? \Carbon\Carbon::createFromFormat('d/m/Y',$row['date'])->format('Y-m-d') : '' }}" data-participants="{{ $row['participants'] }}">
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['date'] ?? '-' }}</td>
                            <td>{{ $row['participants'] }} Peserta</td>
                            <td>{{ $row['speaker'] ?? '-' }}</td>
                            <td>{{ $row['event_rating'] ?? '-' }}</td>
                            <td>{{ $row['speaker_rating'] ?? '-' }}</td>
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
                <div class="info-operasional-box" style="display:flex; gap:12px;">
                    <div class="info-operasional" style="border:1px solid #eee; border-radius:10px; padding:12px;">
                        <h4>{{ $activeCount ?? 0 }}</h4>
                        <p>Acara Aktif</p>
                    </div>
                    <div class="info-operasional" style="border:1px solid #eee; border-radius:10px; padding:12px;">
                        <h4>{{ $completedCount ?? 0 }}</h4>
                        <p>Acara Selesai</p>
                    </div>
                    <div class="info-operasional" style="border:1px solid #eee; border-radius:10px; padding:12px;">
                        <h4>{{ $upcomingCount ?? 0 }}</h4>
                        <p>Acara Mendatang</p>
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
                    <table class="tabel-pendapatan">
                        <thead>
                            <tr>
                                <th style="background-color: #E4E4E6;" scope="col">Nama Event</th>
                                <th style="background-color: #E4E4E6;" scope="col">Tanggal</th>
                                <th style="background-color: #E4E4E6;" scope="col">Jenis Kegiatan</th>
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
                                                'has_vbg' => !empty($e->vbg_path),
                                                'has_cert' => !empty($e->certificate_path),
                                                'has_abs' => !empty($e->attendance_path),
                                                // attendance QR data
                                                'qr_token' => $e->attendance_qr_token,
                                                'qr_url' => $e->attendance_qr_token ? url('/events/'.$e->id.'?t='.$e->attendance_qr_token) : null,
                                                'qr_image_url' => $e->attendance_qr_image ? asset('storage/'.$e->attendance_qr_image) : null,
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
                                        <button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">
                                            {{ $row['documents_percent'] }}%
                                        </button>
                                    </td>
                                    <td>
                                        @php
                                            $ev = isset($row['id']) ? \App\Models\Event::find($row['id']) : null;
                                            $qrToken = $ev?->attendance_qr_token ?: '';
                                            $qrUrl = $qrToken ? url('/events/'.$ev->id.'?t='.$qrToken) : '';
                                            $qrImageUrl = ($ev && $ev->attendance_qr_image) ? asset('storage/'.$ev->attendance_qr_image) : '';
                                        @endphp
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal" data-name="{{ $row['name'] }}" data-vbg="{{ !empty($row['has_vbg']) ? 1 : 0 }}" data-cert="{{ !empty($row['has_cert']) ? 1 : 0 }}" data-abs="{{ !empty($row['has_abs']) ? 1 : 0 }}" data-qr="{{ $qrUrl }}" data-qr-img="{{ $qrImageUrl }}">
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

    <!-- Modals -->
    <div class="modal-view-pendapatan modal fade" id="viewPendapatanModal" tabindex="-1" aria-labelledby="viewPendapatanLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                    <p>Detail Penilaian untuk acara ini.</p>
                    <div class="pertumbuhan-dialog-box">
                        <div class="pertumbuhan-dialog-card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#4b2dbf" class="bi bi-people" viewBox="0 0 16 16">
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0  0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                            </svg>
                            <div class="view-pertumbuhan">
                                <p class="label-view">Jumlah Peserta</p>
                                <p>250 Peserta</p>
                            </div>
                        </div>
                        <div class="pertumbuhan-dialog-card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#4b2dbf" class="bi bi-star" viewBox="0 0 16 16">
                                <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z" />
                            </svg>
                            <div class="view-pertumbuhan">
                                <p class="label-view">Rata-rata Rating Acara</p>
                                <p>4.5/5</p>
                            </div>
                        </div>
                        <div class="pertumbuhan-dialog-card">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#4b2dbf" class="bi bi-star" viewBox="0 0 16 16">
                                <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.56.56 0 0 0-.163-.505L1.71 6.745l4.052-.576a.53.53 0 0 0 .393-.288L8 2.223l1.847 3.658a.53.53 0 0 0 .393.288l4.052.575-2.906 2.77a.56.56 0 0 0-.163.506l.694 3.957-3.686-1.894a.5.5 0 0 0-.461 0z" />
                            </svg>
                            <div class="view-pertumbuhan">
                                <p class="label-view">Rata-rata Rating Event</p>
                                <p>4.5/5</p>
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
    <div class="modal-upload-operasional modal fade" id="uploadOperasionalModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="content-operasional-view modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Status Dokumen Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tinjau status semua dokumen terkait acara dan administrasi.</p>
                    <form action="">
                        <div class="box-up mb-3"><label for="vbg" class="form-label">Virtual Background</label><input type="file" class="form-control" id="vbg"></div>
                        <div class="box-up mb-3"><label for="sertif" class="form-label">Sertifikat</label><input type="file" class="form-control" id="sertif"></div>
                        <div class="box-up mb-3"><label for="absensi" class="form-label">Absensi</label><input type="file" class="form-control" id="absensi"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
// QRCode library (client-side render)
</script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    const buttons = document.querySelectorAll('.btn-report');
    const sections = document.querySelectorAll('.rekap-box');
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            sections.forEach(section => section.classList.remove('active'));
            const targetId = button.getAttribute('data-target');
            const targetSection = document.getElementById(targetId);
            if (targetSection) targetSection.classList.add('active');
            buttons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            // Re-apply filters when switching tabs
            applyAllFilters();
        });
    });

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

    // Populate Status Dokumen modal dynamically
    const viewOperasionalModal = document.getElementById('viewOperasionalModal');
    if (viewOperasionalModal) {
        viewOperasionalModal.addEventListener('show.bs.modal', function (ev) {
            const trigger = ev.relatedTarget;
            const name = trigger?.getAttribute('data-name') || 'Event';
            const hasVbg = (trigger?.getAttribute('data-vbg') === '1');
            const hasCert = (trigger?.getAttribute('data-cert') === '1');
            const hasAbs = (trigger?.getAttribute('data-abs') === '1');
            const qrText = trigger?.getAttribute('data-qr') || '';
            const qrImage = trigger?.getAttribute('data-qr-img') || '';
            const titleEl = document.getElementById('viewOperasionalTitle');
            if (titleEl) titleEl.textContent = 'Status Dokumen: ' + name;
            const container = document.getElementById('operasionalStatusContainer');
            if (!container) return;
            const row = (label, done) => {
                const cls = done ? 'btn-selesai' : 'btn-pending';
                const text = done ? 'Selesai' : 'Pending';
                // Special handling: always show QR code row for Absensi
                if (label === 'QR Absensi') {
                    if (qrImage) {
                        return `<div class="box-kelengkapan d-flex align-items-center justify-content-between">
                            <h6 class="mb-0">${label}</h6>
                            <div class="qr-box"><img id="attendanceQrImg" src="${qrImage}" alt="QR Absensi"> 
                                <div class="small text-muted">Scan untuk absensi</div></div>
                        </div>`;
                    }
                    return `<div class="box-kelengkapan d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">${label}</h6>
                        <div class="qr-box"><canvas id="attendanceQrCanvas" aria-label="QR Absensi"></canvas>
                            <div class="small text-muted">Scan untuk absensi</div></div>
                    </div>`;
                }
                return `<div class="box-kelengkapan d-flex align-items-center justify-content-between">
                        <h6 class="mb-0">${label}</h6>
                        <button class="${cls}">${text}</button>
                    </div>`;
            };
            container.innerHTML = [
                row('Vbg', hasVbg),
                row('Sertifikat', hasCert),
                row('QR Absensi', hasAbs),
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
    // Render trend chart using server-side computed arrays
    (function(){
        const labels = {!! json_encode($labels ?? []) !!};
        const revenue = {!! json_encode($seriesRevenue ?? []) !!};
        const expense = {!! json_encode($seriesExpense ?? []) !!};
        const profit = {!! json_encode($seriesProfit ?? []) !!};

        const ctx = document.getElementById('trendChart');
        if(!ctx) return;
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pendapatan',
                        data: revenue,
                        borderColor: '#6B7CFF',
                        backgroundColor: 'rgba(107,124,255,0.08)',
                        tension: 0.25,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                    },
                    {
                        label: 'Pengeluaran',
                        data: expense,
                        borderColor: '#FF7A7A',
                        backgroundColor: 'rgba(255,122,122,0.08)',
                        tension: 0.25,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                    },
                    {
                        label: 'Keuntungan',
                        data: profit,
                        borderColor: '#3BD1C6',
                        backgroundColor: 'rgba(59,209,198,0.08)',
                        tension: 0.25,
                        fill: true,
                        borderWidth: 2,
                        pointRadius: 2,
                        pointHoverRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: { ticks: { callback: function(v){ return new Intl.NumberFormat('id-ID').format(v); } } }
                },
                plugins: { legend: { position: 'right' } }
            }
        });
    })();
</script>
<script>
    // Growth chart (free vs paid events per month)
    (function(){
        const labelsGrowth = {!! json_encode($months ?? []) !!};
        const paid = {!! json_encode($paidEvents ?? []) !!};
        const free = {!! json_encode($freeEvents ?? []) !!};
        const ctxG = document.getElementById('growthChart');
        if(!ctxG) return;
        new Chart(ctxG, {
            type: 'line',
            data: {
                labels: labelsGrowth,
                datasets: [
                    { label: 'Total Event Berbayar', data: paid, borderColor: '#FF8A80', backgroundColor: 'rgba(255,138,128,0.08)', tension:0.25, fill:true, borderWidth:2, pointRadius:2, pointHoverRadius:4 },
                    { label: 'Total Event Free', data: free, borderColor: '#8A8CFF', backgroundColor: 'rgba(138,140,255,0.08)', tension:0.25, fill:true, borderWidth:2, pointRadius:2, pointHoverRadius:4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: { x: { grid: { display:false } }, y: { beginAtZero:true } }
            }
        });
    })();
</script>
@endsection
