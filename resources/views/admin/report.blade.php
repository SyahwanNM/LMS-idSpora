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
    @include("partials.navbar-admin-course-bootstrap")
    <!-- Scoped page styles to tidy layout without changing markup -->
    <style>
        .box_luar_report{max-width:1140px;margin:0 auto;padding:24px 16px}
        .judul_report{font-weight:700;font-size:1.75rem;margin-bottom:.25rem}
        .keterangan_judul{color:#6b7280;margin-bottom:1rem}
        .btn_box_report{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem}
        .btn_report{border:1px solid #dee2e6;background:#fff;color:#111827;padding:.5rem .75rem;border-radius:.5rem;cursor:pointer}
        .btn_report.active{background:#6f42c1;color:#fff;border-color:#6f42c1;box-shadow:0 4px 12px rgba(111,66,193,.2)}
        .box_report{display:none}
        .box_report.active{display:block;animation:fadeIn .2s ease}
        .box_pendapatan{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1rem}
        .box_btn_laporan{display:flex;gap:.5rem;flex-wrap:wrap}
        .btn_laporan{border:1px solid #dee2e6;background:#fff;color:#111827;padding:.4rem .7rem;border-radius:.5rem;cursor:pointer}
        .btn_laporan.active{background:#fbbf24;color:#111827;border-color:#f59e0b}
        .box_unduh{display:flex;gap:.5rem;margin-right:.75rem}
        .btn_unduh{display:flex;align-items:center;gap:.45rem;border:1px solid #dee2e6;background:#fff;color:#111827;padding:.45rem .75rem;border-radius:.5rem}
        .box_detail_laporan{display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.25rem;margin:1rem 0}
        .detail_laporan,.detail_laporan_pertumbuhan{background:#fff;border:1px solid #e5e7eb;border-radius:1rem;padding:1.25rem;box-shadow:0 8px 24px rgba(0,0,0,.05)}
        .detail_laporan{display:flex;flex-direction:column;justify-content:space-between;min-height:170px}
        .detail_laporan_pertumbuhan{display:flex;flex-direction:column;gap:.5rem;min-height:170px}
        .detail_laporan:hover,.detail_laporan_pertumbuhan:hover{box-shadow:0 12px 28px rgba(0,0,0,.06);transform:translateY(-2px);transition:all .15s ease}
        .detail_laporan h4,.detail_laporan_pertumbuhan h4{font-weight:600;color:#111827;margin-bottom:.25rem}
        .total_kenaikan{font-weight:700;margin:.25rem 0;font-size:1.85rem;line-height:1.25;color:#111827}
        .informasi_kenaikan_pendapatan,.informasi_penurunan_pendapatan{display:flex;align-items:center;gap:.5rem;color:#6b7280;font-size:.95rem}
        .box_cari_pendapatan{margin-top:1.25rem}
        .cari_pendapatan{display:flex;align-items:center;gap:.75rem;flex-wrap:wrap}
        .cari_pendapatan>div{display:flex;align-items:center;gap:.5rem;border:1px solid #dee2e6;border-radius:.5rem;padding:.35rem .6rem;background:#fff}
        .cari_course{border:none;outline:none;min-width:220px}
        .tabel_pendapatan,.tabel_pertumbuhan,.tabel_organize{margin-top:1rem}
        .tabel_pendapatan th,.tabel_pertumbuhan th,.tabel_organize th{white-space:nowrap}
        .tabel_performa{margin-bottom:.5rem}
        .persentase{border:none;background:#6f42c1;color:#fff;border-radius:.5rem;padding:.25rem .5rem}
        .status_lengkap{background:#22c55e;color:#fff;border:none;border-radius:.5rem;padding:.25rem .5rem}
        .status_progress{background:#f59e0b;color:#fff;border:none;border-radius:.5rem;padding:.25rem .5rem}
        .status_missing{background:#ef4444;color:#fff;border:none;border-radius:.5rem;padding:.25rem .5rem}
        .durasi_menonton{margin-bottom:.5rem}
        .progress_bg{background:#e9ecef;border-radius:999px;height:8px;overflow:hidden}
        .progress_fill{background:#667eea;height:8px}
        .box_kelengkapan{margin-top:2rem}
        .box_pencarian{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:.75rem}
        .box_pencarian>div{display:flex;align-items:center;gap:.5rem;border:1px solid #dee2e6;border-radius:.5rem;padding:.35rem .6rem;background:#fff}
        .btn_terapkan{background:#0d6efd;color:#fff;border:none;border-radius:.5rem;padding:.45rem .8rem}
        @keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
    </style>
    <div class="box_luar_report">
        <h1 class="judul_report">Laporan EduPlatform Admin</h1>
        <p class="keterangan_judul">Berikut adalah laporan course.</p>
        <div class="btn_box_report">
            <button class="btn_report active" data-target="pendapatan">Pendapatan</button>
            <button class="btn_report" data-target="pertumbuhan">Pertumbuhan</button>
            <button class="btn_report" data-target="organize_course">Organize Course</button>
        </div>
        <div id="pendapatan" class="box_report active">
            <h3>Laporan Pendapatan</h3>
            <div class="box_pendapatan">
                <div class="box_btn_laporan">
                    <button class="btn_laporan active" data-target="harian">Harian</button>
                    <button class="btn_laporan" data-target="mingguan">Mingguan</button>
                    <button class="btn_laporan" data-target="bulanan">Bulanan</button>
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
                    <h3 class="total_kenaikan">Rp. 23.000.000</h3>
                    <div class="informasi_kenaikan_pendapatan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        <p>12% dari bulan lalu</p>
                    </div>
                </div>
                <div class="detail_laporan">
                    <h4>Pendapatan per Level Course</h4>
                    <h3 class="total_kenaikan">Rp. 18.500.000</h3>
                    <div class="informasi_penurunan_pendapatan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                        </svg>
                        <p>5% dari bulan lalu</p>
                    </div>
                </div>
                <div class="detail_laporan">
                    <h4>Pendapatan per Modul</h4>
                    <h3 class="total_kenaikan">Rp. 4.500.000</h3>
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
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                        <input class="cari_course" type="text" placeholder="Cari Course">
                    </div>
                    <p class="mulai_course">Dari Tanggal:</p>
                    <input class="tanggal_course" type="date">
                    <p class="mulai_course">Sampai Tanggal:</p>
                    <input class="tanggal_course" type="date">
                    <button class="btn_terapkan">Terapkan</button>
                </div>
            </div>
            <table class="tabel_pendapatan table table-striped">
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
                <tbody>
                    <tr>
                        <td>Pengantar UI/UX Dasar</td>
                        <td>14/10/2025</td>
                        <td>100</td>
                        <td>10.000</td>
                        <td>1.000.000</td>
                        <td>100.000</td>
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
                    </tr>
                    <tr>
                        <td>Pengantar UI/UX Dasar</td>
                        <td>14/10/2025</td>
                        <td>100</td>
                        <td>10.000</td>
                        <td>1.000.000</td>
                        <td>100.000</td>
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
                    </tr>
                    <tr>
                        <td>Pengantar UI/UX Dasar</td>
                        <td>14/10/2025</td>
                        <td>100</td>
                        <td>10.000</td>
                        <td>1.000.000</td>
                        <td>100.000</td>
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
                    </tr>
                    <tr>
                        <td>Pengantar UI/UX Dasar</td>
                        <td>14/10/2025</td>
                        <td>100</td>
                        <td>10.000</td>
                        <td>1.000.000</td>
                        <td>100.000</td>
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
                    </tr>
                    <tr>
                        <td>Pengantar UI/UX Dasar</td>
                        <td>14/10/2025</td>
                        <td>100</td>
                        <td>10.000</td>
                        <td>1.000.000</td>
                        <td>100.000</td>
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
                    </tr>
                    <tr>
                        <td>Pengantar UI/UX Dasar</td>
                        <td>14/10/2025</td>
                        <td>100</td>
                        <td>10.000</td>
                        <td>1.000.000</td>
                        <td>100.000</td>
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="pertumbuhan" class="box_report">
            <h3>Laporan Pertumbuhan</h3>
            <div class="box_pendapatan">
                <div class="box_btn_laporan">
                    <button class="btn_laporan active" data-target="harian">Harian</button>
                    <button class="btn_laporan" data-target="mingguan">Mingguan</button>
                    <button class="btn_laporan" data-target="bulanan">Bulanan</button>
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
            <table class="tabel_pertumbuhan table table-striped">
                <thead>
                    <tr>
                        <th>Nama Course</th>
                        <th>Level</th>
                        <th>Total View</th>
                        <th>Waktu tonton rata-rata</th>
                        <th>Tingkat Penyelesaian</th>
                        <th>Komentar</th>
                        <th>Aksi</th>
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
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
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
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
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
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
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
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
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
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
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
                        <td><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#5b35d5" class="bi bi-eye-fill" viewBox="0 0 16 16">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="organize_course" class="box_report">

        </div>
        <div class="box_kelengkapan" >
            <h3>Tabel Kelengkapan per Course</h3>
            <div class="box_pencarian">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                    </svg>
                    <input class="cari_course" type="text" placeholder="Cari Course">
                </div>
                <button class="btn_unduh">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                    </svg>
                    <p>Export CSV</p>
                </button>
            </div>
              <table class="tabel_organize table table-striped">
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
                    <tr>
                        <td>Pengantar Desain UI/UX</td>
                        <td>12</td>
                         <td>
                            <button class="status_lengkap">
                                Complete
                            </button>
                        </td>
                        <td>8</td>
                        <td>4</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Pengantar Desain UI/UX</td>
                        <td>12</td>
                         <td>
                            <button class="status_lengkap">
                                Complete
                            </button>
                        </td>
                        <td>8</td>
                        <td>4</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Pengantar Desain UI/UX</td>
                        <td>12</td>
                         <td>
                            <button class="status_lengkap">
                                Complete
                            </button>
                        </td>
                        <td>8</td>
                        <td>4</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Pengantar Desain UI/UX</td>
                        <td>12</td>
                         <td>
                            <button class="status_progress">
                                In Progress
                            </button>
                        </td>
                        <td>8</td>
                        <td>4</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Pengantar Desain UI/UX</td>
                        <td>12</td>
                         <td>
                            <button class="status_missing">
                                Missing Material
                            </button>
                        </td>
                        <td>8</td>
                        <td>4</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>Pengantar Desain UI/UX</td>
                        <td>12</td>
                         <td>
                            <button class="status_lengkap">
                                Complete
                            </button>
                        </td>
                        <td>8</td>
                        <td>4</td>
                        <td>1</td>
                    </tr>
                </tbody>
            </table>
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

    <!-- Hidden logout form for inactivity auto-logout -->
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

    <!-- Inactivity auto-logout (idle timeout) -->
    <script>
    (function(){
        // Adjust minutes as needed; defaults to 30 minutes
        const IDLE_MINUTES = 30;
        const EVENTS = ['click','mousemove','keydown','scroll','touchstart','touchmove'];
        const logoutForm = document.getElementById('logoutForm');
        let timer;
        function reset(){
            if(timer) clearTimeout(timer);
            timer = setTimeout(function(){
                if (logoutForm) {
                    try { logoutForm.submit(); } catch(e){}
                }
            }, IDLE_MINUTES * 60 * 1000);
        }
        EVENTS.forEach(function(evt){ window.addEventListener(evt, reset, { passive: true }); });
        reset();
    })();
    </script>

</body>


</html>