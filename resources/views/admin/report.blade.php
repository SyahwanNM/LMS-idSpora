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
                <button type="button" class="btn_report btn btn-outline-primary active" data-target="pendapatan">Pendapatan</button>
                <button type="button" class="btn_report btn btn-outline-primary" data-target="pertumbuhan">Pertumbuhan</button>
                <button type="button" class="btn_report btn btn-outline-primary" data-target="organize_course">Organize Course</button>
            </div>
        </div>
        <div id="pendapatan" class="box_report active">
            <h3>Laporan Pendapatan</h3>
            <div class="box_pendapatan">
                <div class="box_btn_laporan">
                    <div class="btn-group" role="group" aria-label="Revenue period">
                        <button type="button" class="btn_laporan btn btn-outline-warning" data-period="daily">Harian</button>
                        <button type="button" class="btn_laporan btn btn-outline-warning" data-period="weekly">Mingguan</button>
                        <button type="button" class="btn_laporan btn btn-outline-warning active" data-period="monthly">Bulanan</button>
                    </div>
                </div>
                <div class="box_unduh">
                    <button class="btn_unduh">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                        </svg>
                        <p>Export PDF</p>
                    </button>
                    <button class="btn_unduh">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-down-fill" viewBox="0 0 16 16">
                            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-1 4v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 11.293V7.5a.5.5 0 0 1 1 0" />
                        </svg>
                        <p>Export Excel</p>
                    </button>
                </div>
            </div>
            <div class="box_detail_laporan">
                <div class="detail_laporan">
                    <h4>Total Pendapatan</h4>
                    <h3 class="total_kenaikan" id="totalRevenue">
                        Rp. {{ number_format((float)($revenueReport['totals']['total_revenue'] ?? 0), 0, ',', '.') }}
                    </h3>
                    <div class="informasi_kenaikan_pendapatan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        <p>12% dari bulan lalu</p>
                    </div>
                </div>
                <div class="detail_laporan">
                    <h4>Pendapatan per Level Course</h4>
                    <h3 class="total_kenaikan" id="topLevelRevenue">
                        Rp. {{ number_format((float)(($revenueReport['revenue_by_level'][0]['revenue_total'] ?? 0)), 0, ',', '.') }}
                    </h3>
                    <div class="informasi_penurunan_pendapatan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                        </svg>
                        <p>5% dari bulan lalu</p>
                    </div>
                </div>
                <div class="detail_laporan">
                    <h4>Pendapatan per Modul</h4>
                    <h3 class="total_kenaikan" id="totalTransactions">
                        {{ (int)($revenueReport['totals']['total_transactions'] ?? 0) }} transaksi
                    </h3>
                    <div class="informasi_kenaikan_pendapatan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        <p>8% dari bulan lalu</p>
                    </div>
                </div>
            </div>
            <div class="box_cari_pendapatan">
                <h5>Pendapatan per Course</h5>
                <div class="cari_pendapatan">
                    <div class="box_pendapatan_per_course">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                        <input class="cari_course" type="text" placeholder="Cari Course">
                    </div>
                    <p class="mulai_course">Dari Tanggal:</p>
                    <input class="tanggal_course" type="date" id="reportFrom" value="{{ $from ?? '' }}">
                    <p class="mulai_course">Sampai Tanggal:</p>
                    <input class="tanggal_course" type="date" id="reportTo" value="{{ $to ?? '' }}">
                    <button class="btn_terapkan" id="applyRevenueFilter">Terapkan</button>
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
            <h3>Laporan Pertumbuhan</h3>
            <div class="box_pertumbuhan">
                <div class="box_btn_laporan">
                    <button class="btn_laporan active" data-target="harian">Harian</button>
                    <button class="btn_laporan" data-target="mingguan">Mingguan</button>
                    <button class="btn_laporan" data-target="bulanan">Bulanan</button>
                </div>
                <div class="box_unduh_pertumbuhan">
                    <button class="btn_unduh_pertumbuhan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                        </svg>
                        <p>Export PDF</p>
                    </button>
                    <button class="btn_unduh_pertumbuhan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-file-earmark-arrow-down-fill" viewBox="0 0 16 16">
                            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-1 4v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 11.293V7.5a.5.5 0 0 1 1 0" />
                        </svg>
                        <p>Export Excel</p>
                    </button>
                </div>
            </div>
            <div class="box_detail_laporan">
                <div class="detail_laporan_pertumbuhan">
                    <h4>Jumlah User yang menyelesaikan Course</h4>
                    <p>Total peserta yang menuntaskan kursus</p>
                    <h3 class="total_kenaikan">950</h3>
                    <div class="informasi_kenaikan_pendapatan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        <p>12% dari bulan lalu</p>
                    </div>
                </div>
                <div class="detail_laporan_pertumbuhan">
                    <h4>Engagement Rate</h4>
                    <p>Interaksi Peserta</p>
                    <div class="analisis_pertumbuhan">
                        <div class="angka_penilaian_pertumbuhan">
                            <p>Rating</p>
                            <p class="total_perhitungan">250</p>
                        </div>
                        <div class="angka_penilaian_pertumbuhan">
                            <p>Durasi Penyelesaian Course</p>
                            <p class="total_perhitungan">120</p>
                        </div>
                        <div class="angka_penilaian_pertumbuhan">
                            <p>Peserta Aktif</p>
                            <p class="total_perhitungan">50</p>
                        </div>
                        <div class="angka_penilaian_pertumbuhan">
                            <p>Rata-rata Nilai Pengerjaan Kuis</p>
                            <p class="total_perhitungan">90</p>
                        </div>
                    </div>
                </div>
                <div class="detail_laporan_pertumbuhan">
                    <h4>Waktu Tonton Permodul</h4>
                    <p>Durasi menonton</p>
                    <div class="durasi_menonton">
                        <div class="analisis_durasi">
                            <p>Modul Intro</p>
                            <p>90%</p>
                        </div>
                        <div class="progress_bg">
                            <div class="progress_fill" style="width: 90%"></div>
                        </div>

                    </div>
                    <div>
                        <div class="analisis_durasi">
                            <p>Modul Dasar</p>
                            <p>90%</p>
                        </div>
                        <div class="progress_bg">
                            <div class="progress_fill" style="width: 90%"></div>
                        </div>

                    </div>
                    <div>
                        <div class="analisis_durasi">
                            <p>Modul Lanjut</p>
                            <p>90%</p>
                        </div>
                        <div class="progress_bg">
                            <div class="progress_fill" style="width: 90%"></div>
                        </div>

                    </div>
                    <div>
                        <div class="analisis_durasi">
                            <p>Modul Akhir</p>
                            <p>90%</p>
                        </div>
                        <div class="progress_bg">
                            <div class="progress_fill" style="width: 90%"></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="box_cari_pendapatan">
                <h3>Detail Performa Course</h3>
            </div>
            <div class="box_tabel_performa">
                <div class="tabel_performa">
                    <h5>Performa Course</h5>
                    <p>Detail pertumbuhan dan interaksi per course</p>
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
                            <th>Tingkat Penyelesaian</th>
                            <th>Komentar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Dasar UI/UX</td>
                            <td>Beginner</td>
                            <td>1.2K</td>
                            <td>45 min</td>
                            <td>
                                <button class="persentase">
                                    85%
                                </button>
                            </td>
                            <td>120</td>

                        </tr>
                        <tr>
                            <td>Dasar UI/UX</td>
                            <td>Advance</td>
                            <td>1.2K</td>
                            <td>45 min</td>
                            <td>
                                <button class="persentase">
                                    85%
                                </button>
                            </td>
                            <td>120</td>

                        </tr>
                        <tr>
                            <td>Dasar UI/UX</td>
                            <td>Beginner</td>
                            <td>1.2K</td>
                            <td>45 min</td>
                            <td>
                                <button class="persentase">
                                    85%
                                </button>
                            </td>
                            <td>120</td>

                        </tr>
                        <tr>
                            <td>Dasar UI/UX</td>
                            <td>Intermediate</td>
                            <td>1.2K</td>
                            <td>45 min</td>
                            <td>
                                <button class="persentase">
                                    85%
                                </button>
                            </td>
                            <td>120</td>

                        </tr>
                        <tr>
                            <td>Dasar UI/UX</td>
                            <td>Beginner</td>
                            <td>1.2K</td>
                            <td>45 min</td>
                            <td>
                                <button class="persentase">
                                    85%
                                </button>
                            </td>
                            <td>120</td>
                        </tr>
                        <tr>
                            <td>Dasar UI/UX</td>
                            <td>Beginner</td>
                            <td>1.2K</td>
                            <td>45 min</td>
                            <td>
                                <button class="persentase">
                                    85%
                                </button>
                            </td>
                            <td>120</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="organize_course" class="box_report">
            <div class="box_kelengkapan">
                <h3>Tabel Kelengkapan per Course</h3>
                <div class="box_pencarian">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                        <input class="cari_course" type="text" placeholder="Cari Course">
                    </div>
                    <button class="btn_unduh" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                        </svg>
                        <p>Export CSV</p>
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="tabel_organize table table-striped table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nama Course</th>
                                <th>Jumlah Modul</th>
                                <th>Status Kelengkapan</th>
                                <th>Jumlah Video</th>
                                <th>Jumlah PDF</th>
                                <th>Quiz/Tugas Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($courses ?? []) as $course)
                                @php
                                    $status = strtolower((string)($course->status ?? ''));
                                    $statusClass = 'status_kelengkapan_inprogress';
                                    $statusText = $course->status ?? 'In Progress';
                                    if (in_array($status, ['published', 'active', 'complete', 'completed'])) {
                                        $statusClass = 'status_kelengkapan_complete';
                                        $statusText = 'Complete';
                                    } elseif (in_array($status, ['missing', 'draft', 'inactive'])) {
                                        $statusClass = 'status_kelengkapan_miss';
                                        $statusText = 'Missing';
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $course->name }}</td>
                                    <td>{{ (int)($course->modules_count ?? 0) }}</td>
                                    <td>
                                        <div class="{{ $statusClass }}">
                                            <p>{{ $statusText }}</p>
                                        </div>
                                    </td>
                                    <td>{{ (int)($course->video_count ?? 0) }}</td>
                                    <td>{{ (int)($course->pdf_count ?? 0) }}</td>
                                    <td>{{ (int)($course->quiz_count ?? 0) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada course.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
            const fromInput = document.getElementById('reportFrom');
            const toInput = document.getElementById('reportTo');
            const applyBtn = document.getElementById('applyRevenueFilter');

            let currentPeriod = 'monthly';
            const periodButtons = document.querySelectorAll('#pendapatan .btn_laporan[data-period]');
            periodButtons.forEach(btn => {
                if (btn.classList.contains('active')) {
                    currentPeriod = btn.getAttribute('data-period') || currentPeriod;
                }
            });

            function render(report) {
                const totals = report?.totals || {};
                const rows = report?.rows || [];
                const byLevel = report?.revenue_by_level || [];

                if (totalRevenueEl) {
                    totalRevenueEl.textContent = 'Rp. ' + formatIDR(totals.total_revenue || 0);
                }
                if (topLevelRevenueEl) {
                    topLevelRevenueEl.textContent = 'Rp. ' + formatIDR((byLevel[0] && byLevel[0].revenue_total) ? byLevel[0].revenue_total : 0);
                }
                if (totalTransactionsEl) {
                    totalTransactionsEl.textContent = String(totals.total_transactions || 0) + ' transaksi';
                }

                if (!tbody) return;

                if (!rows.length) {
                    tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">Belum ada transaksi course pada rentang tanggal ini.</td></tr>';
                    return;
                }

                tbody.innerHTML = rows.map(r => {
                    const lastPaid = r.last_paid_at ? new Date(r.last_paid_at) : null;
                    const lastPaidText = lastPaid && !isNaN(lastPaid.getTime())
                        ? String(lastPaid.getDate()).padStart(2, '0') + '/' + String(lastPaid.getMonth() + 1).padStart(2, '0') + '/' + lastPaid.getFullYear()
                        : '-';
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
                const from = fromInput?.value || '';
                const to = toInput?.value || '';
                const url = new URL(apiUrl, window.location.origin);
                if (from) url.searchParams.set('from', from);
                if (to) url.searchParams.set('to', to);
                url.searchParams.set('period', currentPeriod);

                try {
                    const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
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
                    refresh();
                });
            });

            if (applyBtn) {
                applyBtn.addEventListener('click', function() {
                    refresh();
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