<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Pendapatan</title>
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
        <h1 class="judul_view">Pengantar UI/UX Dasar</h1>
        <p class="deskripsi_view">Laporan Detail Financial & Konten Course </p>
        <div class="tabel_paling_atas">
            <div class="tanggal_view">
                <p>Tanggal</p>
                <h5>14/10/2025</h5>
            </div>
            <div class="total_peserta_view">
                <p>Total peserta</p>
                <h5>100</h5>
            </div>
            <div class="status_view">
                <p>Status</p>
                <h5>Complete</h5>
            </div>
            <div class="harga_modul_pada_view">
                <p>Harga Per Unit</p>
                <h5>Rp. 10.000</h5>
            </div>
        </div>
        <div class="box_luar_breakdown_pendapatan">
            <div class="box_judul_breakdown_pendapatan">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="green" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                </svg>
                <h2>Breakdown Pendapatan</h2>
            </div>
            <div class="box_dalam_breakdown_pendapatan">
                <h5>Penjualan Normal</h5>
                <h3>Rp. 1.000.000</h3>
                <div class="isi_box_pendapatan">
                    <p class="peserta">Peserta:</p>
                    <p>100</p>
                </div>
                <div class="isi_harga_perunit">
                    <p class="peserta">Harga Per Unit:</p>
                    <p>Rp. 10.000</p>
                </div>
                <div class="isi_kalkulasi">
                    <p class="peserta">Kalkulasi:</p>
                    <p>100 x Rp. 10.000</p>
                </div>
                <div class="garis_abu">
                    <div class="garis_hijau"></div>
                </div>
            </div>
            <div class="box_dalam_pendapatan">
                <h4>Total Pendapatan</h4>
                <h4 class="satu_juta">Rp. 1.000.000</h4>
            </div>
        </div>
        <div class="box_luar_breakdown_pengeluaran">
            <div class="box_judul_breakdown_pengeluaran">
                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="red" class="bi bi-graph-down-arrow" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 11.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 0-1 0v2.6l-3.613-4.417a.5.5 0 0 0-.74-.037L7.06 8.233 3.404 3.206a.5.5 0 0 0-.808.588l4 5.5a.5.5 0 0 0 .758.06l2.609-2.61L13.445 11H10.5a.5.5 0 0 0-.5.5" />
                </svg>
                <h2>Breakdown Pengeluaran</h2>
            </div>
            <div class="box_isi_pengeluaran">
                <div class="judul_pertama">
                    <p class="subjudul_pertama">Honor Instruktur</p>
                    <p class="persentase_box">40.0%</p>
                </div>
                <h5>Rp. 40.000</h5>
                <div class="garis_abu">
                    <div class="garis_orange"></div>
                </div>
            </div>
            <div class="box_isi_pengeluaran">
                <div class="judul_pertama">
                    <p class="subjudul_pertama">Platform Fee</p>
                    <p class="persentase_box">20.0%</p>
                </div>
                <h5>Rp. 20.000</h5>
                <div class="garis_abu">
                    <div class="garis_kuning"></div>
                </div>
            </div>
            <div class="box_isi_pengeluaran">
                <div class="judul_pertama">
                    <p class="subjudul_pertama">Marketing $ Promosi</p>
                    <p class="persentase_box">15.0%</p>
                </div>
                <h5>Rp. 15.000</h5>
                <div class="garis_abu">
                    <div class="garis_ungu"></div>
                </div>
            </div>
            <div class="box_isi_pengeluaran">
                <div class="judul_pertama">
                    <p class="subjudul_pertama">Infrastruktur & Server</p>
                    <p class="persentase_box">15.0%</p>
                </div>
                <h5>Rp. 15.000</h5>
                <div class="garis_abu">
                    <div class="garis_biru"></div>
                </div>
            </div>
            <div class="box_isi_pengeluaran">
                <div class="judul_pertama">
                    <p class="subjudul_pertama">Customer Support</p>
                    <p class="persentase_box">10.0%</p>
                </div>
                <h5>Rp. 10.000</h5>
                <div class="garis_abu">
                    <div class="garis_pink"></div>
                </div>
            </div>
            <div class="box_dalam_pengeluaran">
                <h4>Total Pengeluaran</h4>
                <h4 class="satu_juta">Rp. 100.000</h4>
            </div>
        </div>
        <div class="box_luar_analisis_keuntungan">
            <h2>Analisis Keuntungan</h2>
            <div class="box_isi_untung">
                <div class="box_status">
                    <h5>Status keuntungan</h5>
                    <div class="box_validasi_status">
                        <h5>Menguntungkan</h5>
                    </div>
                </div>
                <h3>Rp. 900.000</h3>
            </div>
            <div class="box_dalam_perhitungan">
                <h5>Cara Perhitungan</h5>
                <div class="pendapatan_kotor">
                    <p>Pendapatan Kotor</p>
                    <h5 class="satu_juta_hitung">Rp. 1.000.000</h5>
                </div>
                <div class="total_pengeluaran">
                    <p>Total Pengeluaran</p>
                    <h5 class="seratus_ribu">Rp. 100.000</h5>
                </div>
                <div class="keuntungan_bersih">
                    <p>Keuntungan Bersih</p>
                    <h5 class="sembilan_ratus_ribu">Rp. 900.000</h5>
                </div>
            </div>
        </div>
        <div class="box_luar_financial">
            <h2>Ringkasan Financial</h2>
            <div class="box_chart_financial">
                <canvas id="financeChart" height="100"></canvas>
            </div>

        </div>

    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('financeChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total'],
                datasets: [{
                        label: 'Pendapatan',
                        data: [1000000],
                        backgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Pengeluaran',
                        data: [100000],
                        backgroundColor: '#ef4444'
                    },
                    {
                        label: 'Keuntungan',
                        data: [900000],
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

</body>

</html>