@include("partials.navbar-admin-event")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="box-report">

        <h5>Ikhtisar Laporan</h5>
        <p>Berikut laporan dari event IdSpora</p>

        <div class="btn-report-box">
            <button class="btn-report active" data-target="pendapatan">Pendapatan</button>
            <button class="btn-report" data-target="pertumbuhan">Pertumbuhan</button>
            <button class="btn-report" data-target="operasional">Operasional</button>
        </div>

        <div id="pendapatan" class="rekap-box active">

            <div class="recap-card-box">
                <div class="recap-card">
                    <div class="recap-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
                            <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518z" />
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11m0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12" />
                        </svg>
                        <h5>Total Pendapatan</h5>
                    </div>
                    <h3>Rp.23.000.000</h3>
                    <div class="recap-increase">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        <p>12% dari bulan lalu</p>
                    </div>
                </div>
                <div class="recap-card">
                    <div class="recap-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
                            <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518z" />
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11m0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12" />
                        </svg>
                        <h5>Biaya Operasional</h5>
                    </div>
                    <h3>Rp.23.000.000</h3>
                    <div class="recap-increase">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="green" class="bi bi-arrow-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L7.5 2.707V14.5a.5.5 0 0 0 .5.5" />
                        </svg>
                        <p>12% dari bulan lalu</p>
                    </div>
                </div>
                <div class="recap-card">
                    <div class="recap-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-coin" viewBox="0 0 16 16">
                            <path d="M5.5 9.511c.076.954.83 1.697 2.182 1.785V12h.6v-.709c1.4-.098 2.218-.846 2.218-1.932 0-.987-.626-1.496-1.745-1.76l-.473-.112V5.57c.6.068.982.396 1.074.85h1.052c-.076-.919-.864-1.638-2.126-1.716V4h-.6v.719c-1.195.117-2.01.836-2.01 1.853 0 .9.606 1.472 1.613 1.707l.397.098v2.034c-.615-.093-1.022-.43-1.114-.9zm2.177-2.166c-.59-.137-.91-.416-.91-.836 0-.47.345-.822.915-.925v1.76h-.005zm.692 1.193c.717.166 1.048.435 1.048.91 0 .542-.412.914-1.135.982V8.518z" />
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="M8 13.5a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11m0 .5A6 6 0 1 0 8 2a6 6 0 0 0 0 12" />
                        </svg>
                        <h5>Margin Keuntungan</h5>
                    </div>
                    <h3>Rp.23.000.000</h3>
                    <div class="recap-increase">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="red" class="bi bi-arrow-down" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1" />
                        </svg>
                        <p>12% dari bulan lalu</p>
                    </div>
                </div>
            </div>

            <div class="pendapatan-box">
                <h5>Pendapatan Per Acara</h5>
                <div class="filter-section">
                    <div class="filter-kiri">
                        <div class="filter-group">
                            <label for="filter-event" class="filter-label">Cari Event</label>
                            <input type="text" id="filter-event" class="filter-input" placeholder="Cari nama event...">
                        </div>
                        <div>
                            <button class="btn-cari">cari</button>
                        </div>
                    </div>
                    <div class="filter-kanan">
                        <div class="filter-group">
                            <label for="date-from" class="filter-label">Dari Tanggal</label>
                            <div class="filter-date-group">
                                <input type="date" id="date-from" class="filter-input">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                        </div>

                        <div class="filter-group">
                            <label for="date-to" class="filter-label">Sampai Tanggal</label>
                            <div class="filter-date-group">
                                <input type="date" id="date-to" class="filter-input">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                        </div>

                        <div class="filter-actions">
                            <button class="btn-apply">Terapkan</button>
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
                            <th style="background-color: #E4E4E6;" scope="col">Pendapatan</th>
                            <th style="background-color: #E4E4E6;" scope="col">Pengeluaran</th>
                            <th style="background-color: #E4E4E6;" scope="col">Keuntungan</th>
                            <th style="background-color: #E4E4E6;" scope="col">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td>10.000</td>
                            <td>2000.000</td>
                            <td>300.000</td>
                            <td>1.700.000</td>
                            <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPendapatanModal">
                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                </svg>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div id="pertumbuhan" class="rekap-box">
            <div class="data-box">
                <div class="data-pengguna">
                    <div class="data-pengguna-kiri">
                        <h5>Pengguna Baru</h5>
                        <h4>600</h4>
                        <div class="deskripsi-kenaikan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="blue" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                            </svg>
                            <p>13.2% bulan ini</p>
                        </div>
                    </div>
                    <div class="data-pengguna-kanan">
                        <div class="pertumbuhan-acara">
                            <h5>Pertumbuhan Acara</h5>
                            <div class="pertumbuhan-box">
                                <div class="pertumbuhan-box-isi">
                                    <h4>28</h4>
                                    <p>Acara baru bulan ini</p>
                                    <div class="deskripsi-kenaikan">
                                        <svg class="naik" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="blue" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5" />
                                        </svg>
                                        <p>12.0%</p>
                                    </div>
                                </div>
                                <div class="pertumbuhan-box-isi">
                                    <h4>2000</h4>
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
            <div class="filter-section">
                                <div class="filter-kiri">
                                    <div class="filter-group">
                                        <label for="filter-event" class="filter-label">Cari Event</label>
                                        <input type="text" id="filter-event" class="filter-input" placeholder="Cari nama event...">
                                    </div>
                                    <div>
                                        <button class="btn-cari">cari</button>
                                    </div>
                                </div>
                                <div class="filter-kanan">
                                    <div class="filter-group">
                                        <label for="date-from" class="filter-label">Dari Tanggal</label>
                                        <div class="filter-date-group">
                                            <input type="date" id="date-from" class="filter-input">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                    </div>

                                    <div class="filter-group">
                                        <label for="date-to" class="filter-label">Sampai Tanggal</label>
                                        <div class="filter-date-group">
                                            <input type="date" id="date-to" class="filter-input">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                    </div>

                                    <div class="filter-actions">
                                        <button class="btn-apply">Terapkan</button>
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
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>

                        </td>
                    </tr>
                    <tr>
                        <td>Workshop</td>
                        <td>12/10/2025</td>
                        <td>200 Peserta</td>
                        <td>Lauren</td>
                        <td>4.5</td>
                        <td>5</td>
                        <td>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewPertumbuhanModal">
                                <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                            </svg>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="operasional" class="rekap-box">
            <div>
                <h5>Aktivitas Acara</h5>
                <div class="info-operasional-box">
                    <div class="info-operasional">
                        <h4>15</h4>
                        <p>Acara Aktif</p>
                    </div>
                    <div class="info-operasional">
                        <h4>85</h4>
                        <p>Acara Selesai</p>
                    </div>
                    <div class="info-operasional">
                        <h4>20</h4>
                        <p>Acara Mendatang</p>
                    </div>
                </div>
                <div class="proggress-box-operasional">
                    <div class="proggress-operasional">
                        <p class="title-prog-operasional">Presentase Event Terlaksana</p>
                        <div class="line-proggress-abu">
                            <div class="line-proggress"></div>
                        </div>
                        <p>50% Selesai</p>
                    </div>
                    <div class="proggress-operasional">
                        <p class="title-prog-operasional">Presentase Event Belum Terlaksana</p>
                        <div class="line-proggress-abu">
                            <div class="line-proggress"></div>
                        </div>
                        <p>25% Selesai</p>
                    </div>
                </div>

                <div>
                    <h5>Manajemen Dokumen Per Event</h5>
                    <div class="filter-section">
                        <div class="filter-kiri">
                            <div class="filter-group">
                                <label for="filter-event" class="filter-label">Cari Event</label>
                                <input type="text" id="filter-event" class="filter-input" placeholder="Cari nama event...">
                            </div>
                            <div>
                                <button class="btn-cari">cari</button>
                            </div>
                        </div>
                        <div class="filter-kanan">
                            <div class="filter-group">
                                <label for="date-from" class="filter-label">Dari Tanggal</label>
                                <div class="filter-date-group">
                                    <input type="date" id="date-from" class="filter-input">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                            </div>

                            <div class="filter-group">
                                <label for="date-to" class="filter-label">Sampai Tanggal</label>
                                <div class="filter-date-group">
                                    <input type="date" id="date-to" class="filter-input">
                                    <i class="bi bi-calendar-event"></i>
                                </div>
                            </div>

                            <div class="filter-actions">
                                <button class="btn-apply">Terapkan</button>
                            </div>
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
                            <tr>
                                <td>Workshop</td>
                                <td>12/10/2025</td>
                                <td>Online</td>
                                <td>
                                    <button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">
                                        Lengkapi Dokumen
                                    </button>
                                </td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>

                                </td>
                            </tr>
                            <tr>
                                <td>Workshop</td>
                                <td>12/10/2025</td>
                                <td>Online</td>
                                <td><button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">80%</button></td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>

                                </td>
                            </tr>
                            <tr>
                                <td>Workshop</td>
                                <td>12/10/2025</td>
                                <td>Online</td>
                                <td><button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">80%</button></td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>

                                </td>
                            </tr>
                            <tr>
                                <td>Workshop</td>
                                <td>12/10/2025</td>
                                <td>Online</td>
                                <td><button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">80%</button></td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>

                                </td>
                            </tr>
                            <tr>
                                <td>Workshop</td>
                                <td>12/10/2025</td>
                                <td>Online</td>
                                <td><button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">80%</button></td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>

                                </td>
                            </tr>
                            <tr>
                                <td>Workshop</td>
                                <td>12/10/2025</td>
                                <td>Online</td>
                                <td><button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">80%</button></td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>

                                </td>
                            </tr>
                            <tr>
                                <td>Workshop</td>
                                <td>12/10/2025</td>
                                <td>Online</td>
                                <td><button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">80%</button></td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>

                                </td>
                            </tr>
                            <tr>
                                <td>Workshop</td>
                                <td>12/10/2025</td>
                                <td>Online</td>
                                <td><button class="add-dokumen" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal">80%</button></td>
                                <td>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#0A3EB6" class="bi bi-eye-fill" viewBox="0 0 16 16" data-bs-toggle="modal" data-bs-target="#viewOperasionalModal">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0" />
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7" />
                                    </svg>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-view-pendapatan modal fade" id="viewPendapatanModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="content-event modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Rekap Pendaftaran Workshop UI</h5>
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
                                <tbody>
                                    <tr>
                                        <td>Tiket Pendaftar</td>
                                        <td>200</td>
                                        <td>20.000</td>
                                        <td>4.000.000</td>
                                    </tr>
                                    <tr>
                                        <td>Sponsor</td>
                                        <td>1</td>
                                        <td>500.000</td>
                                        <td>500.000</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="row-harga">
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td>4.500.000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="tabel-pengeluaran">
                            <h5>Pengeluaran</h5>
                            <table>
                                <thead>
                                    <tr>
                                        <th style="background-color: #E4E4E6;" scope="col">Kebutuhan</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Kuantitas</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Harga Satuan</th>
                                        <th style="background-color: #E4E4E6;" scope="col">Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Ads</td>
                                        <td>3 hari</td>
                                        <td>20.000</td>
                                        <td>60.000</td>
                                    </tr>
                                    <tr>
                                        <td>Pembicara</td>
                                        <td>2 jam</td>
                                        <td>500.000</td>
                                        <td>1000.000</td>
                                    </tr>
                                    <tr>
                                        <td>Snack Box</td>
                                        <td>200</td>
                                        <td>5.000</td>
                                        <td>1000.000</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr class="row-harga">
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td>2.060.000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="keuntungan">
                        <h5>Keuntungan</h5>
                        <p>Keuntungan (Laba Bersih)=Total Pemasukan−Total Pengeluaran</p>
                        <h6>Rp4.000.000 - Rp2.060.000 </h6>
                        <h6>Rp1.940.000 </h6>
                    </div>
                    </form>
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
                                <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
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
                    <h5 class="modal-title">Status Dokumen Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Tinjau status semua dokumen terkait acara dan administrasi.</p>
                    <div class="box-kelengkapan">
                        <h6>Vbg</h6>
                        <button class="btn-selesai">Selesai</button>
                    </div>
                    <div class="box-kelengkapan">
                        <h6>Sertifikat</h6>
                        <button class="btn-pending">Pending</button>
                    </div>
                    <div class="box-kelengkapan">
                        <h6>Daftar Hadir</h6>
                        <button class="btn-pending">Pending</button>
                    </div>
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
                        <div class="box-up mb-3">
                            <label for="vbg" class="form-label">Virtual Background</label>
                            <input type="file" class="form-control" id="vbg">
                        </div>
                        <div class="box-up mb-3">
                            <label for="sertif" class="form-label">Sertifikat</label>
                            <input type="file" class="form-control" id="sertif">
                        </div>
                        <div class="box-up mb-3">
                            <label for="absensi" class="form-label">Absensi</label>
                            <input type="file" class="form-control" id="absensi">
                        </div>
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
    <script>
        const buttons = document.querySelectorAll(".btn-report");
        const sections = document.querySelectorAll(".rekap-box");

        buttons.forEach(button => {
            button.addEventListener("click", () => {
                sections.forEach(section => section.classList.remove("active"));

                const targetId = button.getAttribute("data-target");
                const targetSection = document.getElementById(targetId);

                if (targetSection) {
                    targetSection.classList.add("active");
                }

                buttons.forEach(btn => btn.classList.remove("active"));
                button.classList.add("active");
            });
        });
    </script>

</body>

</html>