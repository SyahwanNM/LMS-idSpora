<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include("partials.navbar-admin-course")
    <div class="box_luar_report">
        <h1 class="judul_report">Laporan EduPlatform Admin</h1>
        <p class="keterangan_judul">Berikut adalah laporan course.</p>
        <div class="btn_box_report">
            <div class="btn-group" role="group" aria-label="Report sections">
                <button type="button" class="btn_report active" data-target="pendapatan">Pendapatan</button>
                <button type="button" class="btn_report" data-target="pertumbuhan">Pertumbuhan</button>
            </div>
            <div class="box_unduh">
                <button type="button" class="btn_unduh" id="btnCourseExportPdf">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                    </svg>
                    <p>Export PDF</p>
                </button>
                <button type="button" class="btn_unduh" id="btnCourseExportExcel">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-down-fill" viewBox="0 0 16 16">
                        <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-1 4v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 11.293V7.5a.5.5 0 0 1 1 0" />
                    </svg>
                    <p>Export Excel</p>
                </button>
            </div>
        </div>
        <div id="pendapatan" class="box_report active">
            <div class="box_detail_laporan">
                <div class="detail_laporan">
                    <h4>Total Pendapatan</h4>
                    <h3 class="total_kenaikan" id="totalRevenue">
                        Rp. {{ number_format((float)($revenueReport['totals']['total_revenue'] ?? 0), 0, ',', '.') }}
                    </h3>
                    <div id="totalRevenueChange">
                        @php
                        $chg = $revenueReport['changes']['total_revenue'] ?? ['percent' => 0, 'direction' => 'up'];
                        $chgLabel = $revenueReport['changes']['label'] ?? 'dari bulan lalu';
                        $isDown = ($chg['direction'] ?? 'up') === 'down';
                        @endphp
                        <div class="{{ $isDown ? 'informasi_penurunan_pendapatan' : 'informasi_kenaikan_pendapatan' }}">
                            @if($isDown)
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                            </svg>
                            @endif
                            <p>{{ (float)($chg['percent'] ?? 0) }}% {{ $chgLabel }}</p>
                        </div>
                    </div>
                </div>
                <div class="detail_laporan">
                    <h4>Pendapatan per Level Course</h4>
                    <h3 class="total_kenaikan" id="topLevelRevenue">
                        Rp. {{ number_format((float)(($revenueReport['revenue_by_level'][0]['revenue_total'] ?? 0)), 0, ',', '.') }}
                    </h3>
                    <div id="topLevelRevenueChange">
                        @php
                        $chg = $revenueReport['changes']['top_level_revenue'] ?? ['percent' => 0, 'direction' => 'up'];
                        $chgLabel = $revenueReport['changes']['label'] ?? 'dari bulan lalu';
                        $isDown = ($chg['direction'] ?? 'up') === 'down';
                        @endphp
                        <div class="{{ $isDown ? 'informasi_penurunan_pendapatan' : 'informasi_kenaikan_pendapatan' }}">
                            @if($isDown)
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                            </svg>
                            @endif
                            <p>{{ (float)($chg['percent'] ?? 0) }}% {{ $chgLabel }}</p>
                        </div>
                    </div>
                </div>
                <div class="detail_laporan">
                    <h4>Pendapatan per Modul</h4>
                    <h3 class="total_kenaikan" id="totalTransactions">
                        @php
                        $totalRevenue = (float)($revenueReport['totals']['total_revenue'] ?? 0);
                        $totalTransactions = (int)($revenueReport['totals']['total_transactions'] ?? 0);
                        $revenuePerModule = $totalTransactions > 0 ? (float) round($totalRevenue / $totalTransactions) : 0;
                        @endphp
                        Rp. {{ number_format($revenuePerModule, 0, ',', '.') }}
                    </h3>
                    <div id="revenuePerModuleChange">
                        @php
                        $chg = $revenueReport['changes']['revenue_per_module'] ?? ['percent' => 0, 'direction' => 'up'];
                        $chgLabel = $revenueReport['changes']['label'] ?? 'dari bulan lalu';
                        $isDown = ($chg['direction'] ?? 'up') === 'down';
                        @endphp
                        <div class="{{ $isDown ? 'informasi_penurunan_pendapatan' : 'informasi_kenaikan_pendapatan' }}">
                            @if($isDown)
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                            </svg>
                            @endif
                            <p>{{ (float)($chg['percent'] ?? 0) }}% {{ $chgLabel }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box_cari_pendapatan">
                <h5>Pendapatan per Course</h5>
                <div class="box_filter_cari">
                    <div class="cari_pendapatan">
                        <div class="box_pendapatan_per_course">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                            <input class="cari_course" id="revenueSearch" type="text" placeholder="Cari Course">
                        </div>
                    </div>
                    <div class="box_filter">
                        <p class="mulai_course">Bulan</p>
                        <input class="tanggal_course" id="revenueMonth" type="month">
                        <button class="btn_terapkan" id="applyRevenueFilter">Terapkan</button>
                    </div>

                </div>
            </div>
            <div class="table-responsive">
                <table class="tabel_pendapatan table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama Course</th>
                            <th>Tanggal</th>
                            <th>Peserta</th>
                            <th>Harga</th>
                            <th>Pendapatan</th>
                            <th>Pengeluaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="revenueTableBody">
                        @forelse(($revenueReport['rows'] ?? []) as $row)
                        <tr>
                            <td>{{ $row['course_name'] }}</td>
                            <td>
                                {{ $row['last_paid_at'] ? \Carbon\Carbon::parse($row['last_paid_at'])->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ (int)($row['participants_count'] ?? 0) }}</td>
                            <td>{{ number_format((float)($row['course_price'] ?? 0), 0, ',', '.') }}</td>
                            <td>{{ number_format((float)($row['revenue_total'] ?? 0), 0, ',', '.') }}</td>
                            <td>{{ number_format((float)($row['expense_total'] ?? 0), 0, ',', '.') }}</td>
                            <td>
                                <a href="{{ route('preview-pendapatan') }}?course_id={{ $row['course_id'] }}" class="text-decoration-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada transaksi course pada rentang tanggal ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div id="pertumbuhan" class="box_report">
            <div class="container-fluid mt-4">
                <div class="row g-3">

                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6>Total View</h6>
                                <h3 id="totalViews">{{ (int)(data_get($growthReport, 'summary.total_views', 0)) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6>Waktu Tonton Rata-rata</h6>
                                <h3 id="avgWatch">{{ (int)(data_get($growthReport, 'summary.avg_watch_minutes', 0)) }} Menit</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6>Peserta</h6>
                                <h3 id="totalStudents">{{ (int)(data_get($growthReport, 'summary.participants', 0)) }}</h3>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card shadow-sm">
                            <div class="card-body text-center">
                                <h6>Rating Keseluruhan</h6>
                                <h3 id="courseRating">{{ number_format((float)(data_get($growthReport, 'summary.rating_avg', 0)), 1) }} ⭐</h3>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Report Pertumbuhan Course</h5>
                        <canvas id="growthChart" height="90"></canvas>
                    </div>
                </div>
            </div>
            <div class="box_cari_pendapatan">
                <h3>Detail Performa Course</h3>
            </div>
            <div class="box_cari_pendapatan">
                <h5>Pertumbuhan per Course</h5>
                <div class="box_filter_cari">
                    <div class="cari_pendapatan">
                        <div class="box_pendapatan_per_course">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                            <input class="cari_course" id="growthSearch" type="text" placeholder="Cari Course" value="{{ $growthQuery ?? '' }}">
                        </div>
                    </div>
                    <div class="box_filter">
                        <p class="mulai_course">Bulan</p>
                        <input class="tanggal_course" id="growthMonth" type="month" value="{{ $growthMonth ?? now()->format('Y-m') }}">
                        <button class="btn_terapkan" id="applyGrowthFilter" type="button">Terapkan</button>
                    </div>

                </div>
            </div>

            <div class="table-responsive">
                <table class="tabel_pertumbuhan table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama Course</th>
                            <th>Level</th>
                            <th>Total View</th>
                            <th>Waktu tonton rata-rata</th>
                            <th>Peserta</th>
                            <th>Rating Keseluruhan Course</th>
                        </tr>
                    </thead>
                    <tbody id="growthTableBody">
                        @forelse(($growthReport['rows'] ?? []) as $row)
                        <tr>
                            <td>{{ $row['course_name'] ?? '-' }}</td>
                            <td>{{ $row['course_level'] ?? '-' }}</td>
                            <td>{{ $row['total_views_compact'] ?? '0' }}</td>
                            <td>{{ $row['avg_watch_time_label'] ?? '0 min' }}</td>
                            <td>{{ (int)($row['participants_count'] ?? 0) }}</td>
                            @php($rowRating = (float)($row['rating_avg'] ?? 0))
                            <td>{{ $rowRating > 0 ? number_format($rowRating, 1, '.', '') : '0' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Belum ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <script>
        (function() {
            function sanitizeFilenamePart(str) {
                return String(str || '')
                    .replace(/[^a-z0-9-_]+/gi, '-')
                    .replace(/-+/g, '-')
                    .replace(/(^-|-$)/g, '')
                    .toLowerCase();
            }

            function getActiveTabId() {
                const active = document.querySelector('.box_report.active');
                return active ? (active.getAttribute('id') || '') : '';
            }

            function removeColumnsByHeaderText(table, matcher) {
                const headRow = table.tHead && table.tHead.rows && table.tHead.rows[0] ? table.tHead.rows[0] : null;
                if (!headRow) return;

                const indices = [];
                Array.from(headRow.cells).forEach((th, idx) => {
                    const txt = (th.textContent || '').trim();
                    if (matcher.test(txt)) indices.push(idx);
                });
                if (indices.length === 0) return;

                indices.sort((a, b) => b - a);
                const removeCellsAt = (row) => {
                    indices.forEach(i => {
                        if (row.cells && row.cells[i]) row.deleteCell(i);
                    });
                };
                removeCellsAt(headRow);
                Array.from(table.tBodies || []).forEach(tb => {
                    Array.from(tb.rows || []).forEach(tr => removeCellsAt(tr));
                });
            }

            function cleanupInteractiveElements(root) {
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

            function cloneCleanTable(sourceTable) {
                const cloned = sourceTable.cloneNode(true);
                cloned.removeAttribute('id');
                cleanupInteractiveElements(cloned);
                removeColumnsByHeaderText(cloned, /^aksi$/i);

                cloned.style.borderCollapse = 'collapse';
                cloned.style.width = '100%';
                cloned.querySelectorAll('th, td').forEach(cell => {
                    cell.style.border = '1px solid #e5e7eb';
                    cell.style.padding = '6px 8px';
                    cell.style.verticalAlign = 'top';
                });
                cloned.querySelectorAll('th').forEach(th => {
                    th.style.backgroundColor = '#E4E4E6';
                    th.style.fontWeight = '700';
                });
                return cloned;
            }

            function buildPrintable(titleText, subtitleText, tableEl) {
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
                title.style.width = '100%';
                title.style.overflow = 'visible';
                title.style.whiteSpace = 'normal';
                title.textContent = titleText;

                const subtitle = document.createElement('div');
                subtitle.style.color = '#6B7280';
                subtitle.style.marginBottom = '12px';
                subtitle.style.width = '100%';
                subtitle.style.overflow = 'visible';
                subtitle.style.whiteSpace = 'normal';
                subtitle.textContent = subtitleText;

                printable.appendChild(title);
                printable.appendChild(subtitle);
                printable.appendChild(tableEl);
                return printable;
            }

            function getExportMeta(activeTab) {
                const now = new Date();
                const yyyy = now.getFullYear();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const dd = String(now.getDate()).padStart(2, '0');
                const todayPart = `${yyyy}${mm}${dd}`;

                if (activeTab === 'pertumbuhan') {
                    const activeBtn = document.querySelector('#pertumbuhan .btn_laporan.active[data-target]');
                    const periodLabel = activeBtn ? (activeBtn.textContent || '').trim() : 'Bulanan';
                    return {
                        title: 'Laporan Course - Pertumbuhan',
                        subtitle: `Periode: ${periodLabel}`,
                        fileLabel: `pertumbuhan-${sanitizeFilenamePart(periodLabel) || 'bulanan'}-${todayPart}`,
                        sheetName: 'Pertumbuhan'
                    };
                }

                // default: pendapatan
                const activeBtn = document.querySelector('#pendapatan .btn_laporan.active[data-period]');
                const periodLabel = activeBtn ? (activeBtn.textContent || '').trim() : 'Bulanan';
                const from = (document.getElementById('reportFrom')?.value || '').trim();
                const to = (document.getElementById('reportTo')?.value || '').trim();
                const rangeLabel = (from || to) ? `, Tanggal: ${from || '-'} s/d ${to || '-'}` : '';
                return {
                    title: 'Laporan Course - Pendapatan',
                    subtitle: `Periode: ${periodLabel}${rangeLabel}`,
                    fileLabel: `pendapatan-${sanitizeFilenamePart(periodLabel) || 'bulanan'}-${todayPart}`,
                    sheetName: 'Pendapatan'
                };
            }

            function computeWorksheetColWidths(ws) {
                const ref = ws['!ref'];
                if (!ref || !window.XLSX) return;
                const range = window.XLSX.utils.decode_range(ref);

                const widths = [];
                for (let C = range.s.c; C <= range.e.c; C++) {
                    let maxLen = 10;
                    for (let R = range.s.r; R <= range.e.r; R++) {
                        const cell = ws[window.XLSX.utils.encode_cell({ r: R, c: C })];
                        const v = cell && typeof cell.v !== 'undefined' ? String(cell.v) : '';
                        maxLen = Math.max(maxLen, v.length);
                    }
                    // Clamp widths so they don't get absurd
                    widths.push({ wch: Math.min(Math.max(maxLen + 2, 10), 45) });
                }
                ws['!cols'] = widths;
            }

            document.addEventListener('DOMContentLoaded', function() {
                const btnPdf = document.getElementById('btnCourseExportPdf');
                const btnExcel = document.getElementById('btnCourseExportExcel');
                if (!btnPdf || !btnExcel) return;

                const exportPdfBaseUrl = @json(route('admin.report.export.pdf'));

                function getActiveTable() {
                    const tab = getActiveTabId() || 'pendapatan';
                    if (tab === 'pertumbuhan') {
                        return document.querySelector('#pertumbuhan table.tabel_pertumbuhan');
                    }
                    return document.querySelector('#pendapatan table.tabel_pendapatan');
                }

                btnPdf.addEventListener('click', function() {
                    const activeTab = getActiveTabId() || 'pendapatan';
                    const url = new URL(exportPdfBaseUrl, window.location.origin);
                    url.searchParams.set('tab', activeTab);

                    if (activeTab === 'pendapatan') {
                        // period comes from active button if present
                        const activeBtn = document.querySelector('#pendapatan .btn_laporan.active[data-period]');
                        const period = activeBtn ? (activeBtn.getAttribute('data-period') || '') : '';
                        if (period) url.searchParams.set('period', period);

                        const from = (document.getElementById('reportFrom')?.value || '').trim();
                        const to = (document.getElementById('reportTo')?.value || '').trim();
                        if (from) url.searchParams.set('from', from);
                        if (to) url.searchParams.set('to', to);
                    } else {
                        const activeBtn = document.querySelector('#pertumbuhan .btn_laporan.active[data-target]');
                        // map UI labels -> API period
                        const t = (activeBtn ? (activeBtn.getAttribute('data-target') || '') : '').toLowerCase();
                        const p = (t === 'harian') ? 'daily' : (t === 'mingguan' ? 'weekly' : 'monthly');
                        url.searchParams.set('period', p);

                        const month = (document.getElementById('growthMonth')?.value || '').trim();
                        const q = (document.getElementById('growthSearch')?.value || '').trim();
                        if (month) url.searchParams.set('month', month);
                        if (q) url.searchParams.set('q', q);
                    }

                    window.location.href = url.toString();
                });

                btnExcel.addEventListener('click', function() {
                    if (!window.XLSX) return;
                    const activeTab = getActiveTabId() || 'pendapatan';
                    const table = getActiveTable();
                    if (!table) return;

                    const meta = getExportMeta(activeTab);
                    const tableClone = cloneCleanTable(table);

                    const wb = window.XLSX.utils.book_new();
                    const ws = window.XLSX.utils.table_to_sheet(tableClone);
                    computeWorksheetColWidths(ws);
                    window.XLSX.utils.book_append_sheet(wb, ws, meta.sheetName);
                    window.XLSX.writeFile(wb, `report-course-${meta.fileLabel}.xlsx`);
                });
            });
        })();
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const ctx = document.getElementById('growthChart');

            const growthChartPayload = @json($growthChart ?? null);
            const dbSeries = (growthChartPayload && growthChartPayload.series) ? growthChartPayload.series : {};
            const seriesViews = Array.isArray(dbSeries.views) ? dbSeries.views : [];
            const seriesParticipants = Array.isArray(dbSeries.participants) ? dbSeries.participants : [];
            const seriesWatch = Array.isArray(dbSeries.watch_minutes) ? dbSeries.watch_minutes : [];
            const seriesRating = Array.isArray(dbSeries.rating) ? dbSeries.rating : [];

            const growthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
                        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
                    ],
                    datasets: [{
                            label: 'Total View',
                            data: (seriesViews.length === 12 ? seriesViews : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]),
                            borderColor: '#4e73df',
                            backgroundColor: 'rgba(78,115,223,0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Peserta',
                            data: (seriesParticipants.length === 12 ? seriesParticipants : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]),
                            borderColor: '#1cc88a',
                            backgroundColor: 'rgba(28,200,138,0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Waktu Tonton (Menit)',
                            data: (seriesWatch.length === 12 ? seriesWatch : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]),
                            borderColor: '#f6c23e',
                            backgroundColor: 'rgba(246,194,62,0.1)',
                            tension: 0.4
                        },
                        {
                            label: 'Rating Course',
                            data: (seriesRating.length === 12 ? seriesRating : [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]),
                            borderColor: '#e74a3b',
                            backgroundColor: 'rgba(231,74,59,0.1)',
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Expose for the filter script (keeps style/config identical; only data changes).
            window.__growthChart = growthChart;

        });
    </script>
    <script>
        const buttons = document.querySelectorAll(".btn_report");
        const sections = document.querySelectorAll(".box_report");

        buttons.forEach(button => {
            button.addEventListener("click", () => {
                sections.forEach(section => section.classList.remove("active"));

                const targetId = button.dataset.target;
                const targetSection = document.getElementById(targetId);

                if (targetSection) {
                    targetSection.classList.add("active");
                }
                buttons.forEach(btn => btn.classList.remove("active"));
                button.classList.add("active");
            });
        });
    </script>

    <script>
        (function() {
            const apiUrl = @json(route('admin.report.revenue'));
            const formatIDR = (n) => {
                try {
                    return new Intl.NumberFormat('id-ID').format(Number(n || 0));
                } catch (e) {
                    return String(n || 0);
                }
            };

            const totalRevenueEl = document.getElementById('totalRevenue');
            const topLevelRevenueEl = document.getElementById('topLevelRevenue');
            const totalTransactionsEl = document.getElementById('totalTransactions');
            const tbody = document.getElementById('revenueTableBody');
            const monthInput = document.getElementById('revenueMonth');
            const searchInput = document.getElementById('revenueSearch');
            const applyBtn = document.getElementById('applyRevenueFilter');

            let currentPeriod = 'monthly';
            const periodButtons = document.querySelectorAll('#pendapatan .btn_laporan[data-period]');
            periodButtons.forEach(btn => {
                if (btn.classList.contains('active')) {
                    currentPeriod = btn.getAttribute('data-period') || currentPeriod;
                }
            });

            function toInputDateValue(d) {
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            }

            function setRangeForPeriod(period) {
                const now = new Date();
                let from = new Date(now);
                let to = new Date(now);

                if (period === 'daily') {
                    // today
                } else if (period === 'weekly') {
                    // Week-to-date (Monday start)
                    const day = now.getDay(); // 0=Sun..6=Sat
                    const diffToMonday = (day === 0 ? -6 : 1) - day;
                    from = new Date(now);
                    from.setDate(now.getDate() + diffToMonday);
                } else {
                    // monthly: month-to-date
                    from = new Date(now.getFullYear(), now.getMonth(), 1);
                }

                if (fromInput) fromInput.value = toInputDateValue(from);
                if (toInput) toInput.value = toInputDateValue(to);
            }

            function render(report) {
                const totals = report?.totals || {};
                const rows = report?.rows || [];
                const byLevel = report?.revenue_by_level || [];
                const changes = report?.changes || {};

                if (totalRevenueEl) {
                    totalRevenueEl.textContent = 'Rp. ' + formatIDR(totals.total_revenue || 0);
                }
                if (topLevelRevenueEl) {
                    topLevelRevenueEl.textContent = 'Rp. ' + formatIDR((byLevel[0] && byLevel[0].revenue_total) ? byLevel[0].revenue_total : 0);
                }
                if (totalTransactionsEl) {
                    const tx = Number(totals.total_transactions || 0);
                    const rev = Number(totals.total_revenue || 0);
                    const perModule = tx > 0 ? Math.round(rev / tx) : 0;
                    totalTransactionsEl.textContent = 'Rp. ' + formatIDR(perModule);
                }

                function renderChange(containerId, changeObj) {
                    const el = document.getElementById(containerId);
                    if (!el) return;

                    const label = String(changes.label || 'dari bulan lalu');
                    const percent = Number(changeObj?.percent ?? 0);
                    const direction = (changeObj?.direction === 'down') ? 'down' : 'up';

                    if (direction === 'down') {
                        el.innerHTML = `
                            <div class="informasi_penurunan_pendapatan">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                                </svg>
                                <p>${percent}% ${label}</p>
                            </div>
                        `;
                    } else {
                        el.innerHTML = `
                            <div class="informasi_kenaikan_pendapatan">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                                </svg>
                                <p>${percent}% ${label}</p>
                            </div>
                        `;
                    }
                }

                renderChange('totalRevenueChange', changes.total_revenue);
                renderChange('topLevelRevenueChange', changes.top_level_revenue);
                renderChange('revenuePerModuleChange', changes.revenue_per_module);

                if (!tbody) return;

                if (!rows.length) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Belum ada transaksi course pada rentang tanggal ini.</td></tr>';
                    return;
                }

                tbody.innerHTML = rows.map(r => {
                    const lastPaid = r.last_paid_at ? new Date(r.last_paid_at) : null;
                    const lastPaidText = lastPaid && !isNaN(lastPaid.getTime()) ?
                        String(lastPaid.getDate()).padStart(2, '0') + '/' + String(lastPaid.getMonth() + 1).padStart(2, '0') + '/' + lastPaid.getFullYear() :
                        '-';
                    const previewUrl = @json(route('preview-pendapatan')) + '?course_id=' + encodeURIComponent(r.course_id);
                    return `
                        <tr>
                            <td>${(r.course_name ?? '')}</td>
                            <td>${lastPaidText}</td>
                            <td>${Number(r.participants_count || 0)}</td>
                            <td>${formatIDR(r.course_price || 0)}</td>
                            <td>${formatIDR(r.revenue_total || 0)}</td>
                            <td>${formatIDR(r.expense_total || 0)}</td>
                            <td>
                                <a href="${previewUrl}" class="text-decoration-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    `;
                }).join('');
            }

            async function refresh() {
                const month = monthInput?.value || '';
                const q = searchInput?.value || '';
                const url = new URL(apiUrl, window.location.origin);
                if (month) url.searchParams.set('month', month);
                if (q) url.searchParams.set('q', q);
                url.searchParams.set('period', currentPeriod);

                try {
                    const res = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    render(data);
                } catch (e) {
                    // ignore fetch errors
                }
            }

            periodButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    periodButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentPeriod = this.getAttribute('data-period') || currentPeriod;
                    setRangeForPeriod(currentPeriod);
                    refresh();
                });
            });

            if (applyBtn) {
                applyBtn.addEventListener('click', function() {
                    refresh();
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        refresh();
                    }
                });
            }
        })();
    </script>

    <script>
        (function() {
            const apiUrl = @json(route('admin.report.growth'));

            const tbody = document.getElementById('growthTableBody');
            const totalViewsEl = document.getElementById('totalViews');
            const avgWatchEl = document.getElementById('avgWatch');
            const totalStudentsEl = document.getElementById('totalStudents');
            const courseRatingEl = document.getElementById('courseRating');
            const monthInput = document.getElementById('growthMonth');
            const searchInput = document.getElementById('growthSearch');
            const applyBtn = document.getElementById('applyGrowthFilter');
            const periodButtons = document.querySelectorAll('#pertumbuhan .btn_laporan[data-target]');

            if (!tbody) return;

            const escapeHtml = (s) => String(s ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

            const mapTargetToPeriod = (t) => {
                const target = String(t || '').toLowerCase();
                if (target === 'harian') return 'daily';
                if (target === 'mingguan') return 'weekly';
                return 'monthly';
            };

            const renderRows = (rows) => {
                if (!Array.isArray(rows) || rows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada data.</td></tr>';
                    return;
                }

                tbody.innerHTML = rows.map((r) => {
                    const name = escapeHtml(r.course_name ?? '-');
                    const level = escapeHtml(r.course_level ?? '-');
                    const views = escapeHtml(r.total_views_compact ?? String(r.total_views ?? 0));
                    const avg = escapeHtml(r.avg_watch_time_label ?? '0 min');
                    const participants = escapeHtml(String(r.participants_count ?? 0));
                    const rating = escapeHtml(String(typeof r.rating_avg !== 'undefined' ? r.rating_avg : 0));

                    return (
                        '<tr>' +
                        '<td>' + name + '</td>' +
                        '<td>' + level + '</td>' +
                        '<td>' + views + '</td>' +
                        '<td>' + avg + '</td>' +
                        '<td>' + participants + '</td>' +
                        '<td>' + rating + '</td>' +
                        '</tr>'
                    );
                }).join('');
            };

            const updateSummary = (summary) => {
                if (!summary) return;
                if (totalViewsEl) totalViewsEl.textContent = String(summary.total_views || 0);
                if (avgWatchEl) avgWatchEl.textContent = String(summary.avg_watch_minutes || 0) + ' Menit';
                if (totalStudentsEl) totalStudentsEl.textContent = String(summary.participants || 0);
                if (courseRatingEl) {
                    const r = Number(summary.rating_avg || 0);
                    courseRatingEl.textContent = r > 0 ? (r.toFixed(1) + ' ⭐') : '0 ⭐';
                }
            };

            const updateChart = (chartPayload) => {
                const ch = window.__growthChart;
                if (!ch || !chartPayload || !chartPayload.series) return;

                const s = chartPayload.series || {};
                const views = Array.isArray(s.views) ? s.views : [];
                const participants = Array.isArray(s.participants) ? s.participants : [];
                const watch = Array.isArray(s.watch_minutes) ? s.watch_minutes : [];
                const rating = Array.isArray(s.rating) ? s.rating : [];

                if (views.length === 12) ch.data.datasets[0].data = views;
                if (participants.length === 12) ch.data.datasets[1].data = participants;
                if (watch.length === 12) ch.data.datasets[2].data = watch;
                if (rating.length === 12) ch.data.datasets[3].data = rating;
                ch.update();
            };

            const fetchAndRender = async (period) => {
                const url = new URL(apiUrl, window.location.origin);
                url.searchParams.set('period', period);

                const month = (monthInput?.value || '').trim();
                const q = (searchInput?.value || '').trim();
                if (month) url.searchParams.set('month', month);
                if (q) url.searchParams.set('q', q);

                const res = await fetch(url.toString(), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) {
                    throw new Error('Failed to load growth report');
                }
                const data = await res.json();

                renderRows(data.rows || []);
                updateSummary(data.summary || {});
                if (data.chart) updateChart(data.chart);
            };

            const getCurrentPeriod = () => {
                const activeBtn = document.querySelector('#pertumbuhan .btn_laporan.active[data-target]');
                return mapTargetToPeriod(activeBtn ? activeBtn.dataset.target : 'bulanan');
            };

            periodButtons.forEach((btn) => {
                btn.addEventListener('click', async () => {
                    periodButtons.forEach((b) => b.classList.remove('active'));
                    btn.classList.add('active');

                    try {
                        await fetchAndRender(mapTargetToPeriod(btn.dataset.target));
                    } catch (e) {
                        // Keep current UI if the request fails.
                        // eslint-disable-next-line no-console
                        console.warn(e);
                    }
                });
            });

            if (applyBtn) {
                applyBtn.addEventListener('click', async () => {
                    try {
                        await fetchAndRender(getCurrentPeriod());
                    } catch (e) {
                        console.warn(e);
                    }
                });
            }

            if (searchInput) {
                searchInput.addEventListener('keydown', async (e) => {
                    if (e.key !== 'Enter') return;
                    e.preventDefault();
                    try {
                        await fetchAndRender(getCurrentPeriod());
                    } catch (err) {
                        console.warn(err);
                    }
                });
            }
        })();
    </script>

    <!-- Hidden logout form for inactivity auto-logout -->
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <!-- Inactivity auto-logout (idle timeout) -->
    <script>
        (function() {
            // Adjust minutes as needed; defaults to 30 minutes
            const IDLE_MINUTES = 30;
            const EVENTS = ['click', 'mousemove', 'keydown', 'scroll', 'touchstart', 'touchmove'];
            const logoutForm = document.getElementById('logoutForm');
            let timer;

            function reset() {
                if (timer) clearTimeout(timer);
                timer = setTimeout(function() {
                    if (logoutForm) {
                        try {
                            logoutForm.submit();
                        } catch (e) {}
                    }
                }, IDLE_MINUTES * 60 * 1000);
            }
            EVENTS.forEach(function(evt) {
                window.addEventListener(evt, reset, {
                    passive: true
                });
            });
            reset();
        })();
    </script>

</body>


</html>