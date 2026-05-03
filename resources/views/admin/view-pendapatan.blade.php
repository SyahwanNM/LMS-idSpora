
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Revenue</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include("partials.navbar-admin-course")
    <div class="box_luar_view_pendapatan">
        <div class="d-flex align-items-center gap-3 mb-1">
            <a href="{{ route('report') }}" title="Kembali ke Report Course"
               style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:8px;background:#f1f5f9;color:#475569;text-decoration:none;flex-shrink:0;transition:background .15s;"
               onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
            </a>
            <h1 class="judul_view mb-0">{{ $course->name ?? '-' }}</h1>
            <div class="ms-auto d-flex gap-2">
                <button onclick="exportPendapatanPdf()" class="btn_unduh" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#475569;font-size:13px;font-weight:600;cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/></svg>
                    Export PDF
                </button>
                <button onclick="exportPendapatanExcel()" class="btn_unduh" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;color:#475569;font-size:13px;font-weight:600;cursor:pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-file-earmark-spreadsheet" viewBox="0 0 16 16"><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h3V9H2V2a1 1 0 0 1 1-1h5.5zM2 10h4v2H2zm5 0h4v2H7zm5 0h2v2h-2zM2 13h4v2H2zm5 0h4v2H7zm5 0h2v2h-2z"/></svg>
                    Export Excel
                </button>
            </div>
        </div>
        <p class="deskripsi_view">Financial Detail Report & Course Content</p>
        <div class="tabel_paling_atas">
            <div class="tanggal_view">
                <p>Date</p>
                <h5>{{ ($stats['created_at'] ?? null) ? ($stats['created_at'])->format('d/m/Y') : '-' }}</h5>
            </div>
            <div class="total_peserta_view">
                <p>Total Participants</p>
                <h5>{{ (int)($stats['participants'] ?? 0) }}</h5>
            </div>
            <div class="status_view">
                <p>Status</p>
                <h5>{{ $stats['status'] ?? '-' }}</h5>
            </div>
            <div class="harga_modul_pada_view">
                <p>Price Per Unit</p>
                <h5>Rp. {{ number_format((float)($stats['unit_price'] ?? 0), 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="box_luar_breakdown_pendapatan">
            <div class="box_judul_breakdown_pendapatan">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="green" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                </svg>
                <h2>Breakdown Income</h2>
            </div>
            @foreach($income_rows ?? [['label' => 'Tiket Pendaftar', 'qty' => $stats['participants'] ?? 0, 'unit' => $stats['unit_price'] ?? 0, 'total' => $stats['revenue_total'] ?? 0]] as $incRow)
            <div class="box_dalam_breakdown_pendapatan">
                <h5>{{ $incRow['label'] }}</h5>
                <h3>Rp. {{ number_format((float)($incRow['total'] ?? 0), 0, ',', '.') }}</h3>
                <div class="isi_box_pendapatan">
                    <p class="peserta">Participants:</p>
                    <p>{{ (int)($incRow['qty'] ?? 0) }}</p>
                </div>
                <div class="isi_harga_perunit">
                    <p class="peserta">Price Per Unit:</p>
                    <p>Rp. {{ number_format((float)($incRow['unit'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="isi_kalkulasi">
                    <p class="peserta">Calculation:</p>
                    <p>{{ (int)($incRow['qty'] ?? 0) }} x Rp. {{ number_format((float)($incRow['unit'] ?? 0), 0, ',', '.') }}</p>
                </div>
                <div class="garis_abu">
                    <div class="garis_hijau"></div>
                </div>
            </div>
            @endforeach
            <div class="box_dalam_pendapatan">
                <h4>Total Income</h4>
                <h4 class="satu_juta">Rp. {{ number_format((float)($stats['revenue_total'] ?? 0), 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="box_luar_breakdown_pengeluaran">
            <div class="box_judul_breakdown_pengeluaran">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="red" class="bi bi-graph-down-arrow" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 11.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 0-1 0v2.6l-3.613-4.417a.5.5 0 0 0-.74-.037L7.06 8.233 3.404 3.206a.5.5 0 0 0-.808.588l4 5.5a.5.5 0 0 0 .758.06l2.609-2.61L13.445 11H10.5a.5.5 0 0 0-.5.5" />
                </svg>
                <h2>Breakdown Expenses</h2>
            </div>
            @php
                $expenseRows = is_array($expense_rows ?? null) ? $expense_rows : [];
                $expenseTotal = (float)($stats['expense_total'] ?? 0);
                $barClasses = ['garis_orange', 'garis_kuning', 'garis_ungu', 'garis_biru', 'garis_pink'];
            @endphp

            @if(count($expenseRows) <= 0)
                <div class="box_isi_pengeluaran">
                    <div class="judul_pertama">
                        <p class="subjudul_pertama">Expenses</p>
                        <p class="persentase_box">0.0%</p>
                    </div>
                    <h5>Rp. 0</h5>
                    <div class="garis_abu">
                        <div class="garis_orange" style="width:0%"></div>
                    </div>
                </div>
            @else
                @foreach($expenseRows as $i => $row)
                    @php
                        $item = is_array($row) ? trim((string)($row['item'] ?? 'Pengeluaran')) : 'Pengeluaran';
                        $total = is_array($row) ? (float)($row['total'] ?? 0) : 0;
                        $percent = $expenseTotal > 0 ? ($total / $expenseTotal * 100) : 0;
                        $cls = $barClasses[$i % count($barClasses)];
                    @endphp
                    <div class="box_isi_pengeluaran">
                        <div class="judul_pertama">
                            <p class="subjudul_pertama">{{ $item !== '' ? $item : 'Pengeluaran' }}</p>
                            <p class="persentase_box">{{ number_format($percent, 1) }}%</p>
                        </div>
                        <h5>Rp. {{ number_format($total, 0, ',', '.') }}</h5>
                        <div class="garis_abu">
                            <div class="{{ $cls }}" style="width: {{ number_format($percent, 2, '.', '') }}%"></div>
                        </div>
                    </div>
                @endforeach
            @endif
            <div class="box_dalam_pengeluaran">
                <h4>Total Expenses</h4>
                <h4 class="satu_juta">Rp. {{ number_format((float)($stats['expense_total'] ?? 0), 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="box_luar_analisis_keuntungan">
            <h2>Profit Analysis</h2>
            <div class="box_isi_untung">
                <div class="box_status">
                    <h5>Profit Status</h5>
                    <div class="box_validasi_status">
                        <h5>{{ $stats['profit_status'] ?? '-' }}</h5>
                    </div>
                </div>
                <h3>Rp. {{ number_format((float)($stats['profit'] ?? 0), 0, ',', '.') }}</h3>
            </div>
            <div class="box_dalam_perhitungan">
                <h5>Calculation Method</h5>
                <div class="pendapatan_kotor">
                    <p>Revenue</p>
                    <h5 class="satu_juta_hitung">Rp. {{ number_format((float)($stats['revenue_total'] ?? 0), 0, ',', '.') }}</h5>
                </div>
                <div class="total_pengeluaran">
                    <p>Total Expenses</p>
                    <h5 class="seratus_ribu">Rp. {{ number_format((float)($stats['expense_total'] ?? 0), 0, ',', '.') }}</h5>
                </div>
                <div class="keuntungan_bersih">
                    <p>Net Profit</p>
                    <h5 class="sembilan_ratus_ribu">Rp. {{ number_format((float)($stats['profit'] ?? 0), 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="box_luar_financial">
            <h2>Financial Summary</h2>
            <div class="box_chart_financial">
                <canvas id="financeChart" height="100"></canvas>
            </div>

        </div>

    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('financeChart').getContext('2d');
        const financeData = @json($chart ?? ['revenue' => 0, 'expense' => 0, 'profit' => 0]);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total'],
                datasets: [{
                        label: 'Revenue',
                        data: [Number(financeData.revenue || 0)],
                        backgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Expenses',
                        data: [Number(financeData.expense || 0)],
                        backgroundColor: '#ef4444'
                    },
                    {
                        label: 'Profit',
                        data: [Number(financeData.profit || 0)],
                        backgroundColor: '#22c55e'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
        const COURSE_NAME = @json($course->name ?? 'course');
        const STATS = @json($stats);
        const EXPENSE_ROWS = @json($expenseRows);

        function exportPendapatanPdf() {
            const url = new URL(window.location.href);
            url.searchParams.set('export', 'pdf');
            window.location.href = url.toString();
        }

        function exportPendapatanExcel() {
            if (!window.XLSX) return alert('XLSX library not loaded');

            const wb = XLSX.utils.book_new();

            // Sheet 1: Summary
            const summary = [
                ['Course', COURSE_NAME],
                ['Date', '{{ ($stats["created_at"] ?? null) ? $stats["created_at"]->format("d/m/Y") : "-" }}'],
                ['Total Participants', STATS.participants ?? 0],
                ['Status', STATS.status ?? '-'],
                ['Price Per Unit', 'Rp. ' + Number(STATS.unit_price ?? 0).toLocaleString('id-ID')],
                [],
                ['Total Income', 'Rp. ' + Number(STATS.revenue_total ?? 0).toLocaleString('id-ID')],
                ['Total Expenses', 'Rp. ' + Number(STATS.expense_total ?? 0).toLocaleString('id-ID')],
                ['Profit/Loss', 'Rp. ' + Number(STATS.profit ?? 0).toLocaleString('id-ID')],
                ['Status', STATS.profit_status ?? '-'],
            ];
            const ws1 = XLSX.utils.aoa_to_sheet(summary);
            ws1['!cols'] = [{wch: 20}, {wch: 30}];
            XLSX.utils.book_append_sheet(wb, ws1, 'Summary');

            // Sheet 2: Expense Breakdown
            const expHeader = [['Expense Item', 'Amount']];
            const expData = EXPENSE_ROWS.map(r => [r.item, 'Rp. ' + Number(r.total).toLocaleString('id-ID')]);
            const ws2 = XLSX.utils.aoa_to_sheet([...expHeader, ...expData]);
            ws2['!cols'] = [{wch: 30}, {wch: 20}];
            XLSX.utils.book_append_sheet(wb, ws2, 'Expenses');

            const filename = 'report-' + COURSE_NAME.replace(/[^a-z0-9]/gi, '-').toLowerCase() + '.xlsx';
            XLSX.writeFile(wb, filename);
        }
    </script>

</body>
</html>