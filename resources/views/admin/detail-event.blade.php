@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    <div class="container my-5">

        <div class="banner-detail">
            <img src="{{ asset('aset/event.png') }}" alt="Gambar Event">
            <div class="banner-overlay">
                <h1>Global Tech Summit 2024</h1>
                <p>Membentuk Masa Depan Teknologi Bersama</p>
            </div>
        </div>

        <div class="content-detail-grid">
            <div class="content-detail-left">
                <div class="card-detail-section">
                    <h3>Global Tech Summit 2024</h3>
                    <p>
                        Global Tech Summit adalah acara tahunan terkemuka yang menyatukan para pemimpin industri,
                        inovator, dan pembuat kebijakan untuk mengeksplorasi tren teknologi terbaru dan dampaknya
                        terhadap masyarakat. Bergabunglah dengan kami untuk sesi inspiratif, lokakarya interaktif,
                        dan peluang jaringan yang tak tertandingi.
                    </p>

                    <ul class="event-info">
                        <li><strong>Tanggal:</strong> 26 - 28 Oktober 2024</li>
                        <li><strong>Waktu:</strong> 09.00 - 17.00 WIB</li>
                        <li><strong>Harga:</strong> Gratis</li>
                    </ul>
                </div>

                <div class="card-detail-section">
                    <h4>Lokasi & Akses</h4>
                    <p><strong>Jakarta Convention Center</strong></p>
                    <p>Jl. Jend. Gatot Subroto No.1, Jakarta Pusat, Indonesia</p>
                    <button class="btn-map">Lihat Peta</button>
                    <h4>Link Zoom & Vbg</h4>
                    <p><strong>https://zoom.com</strong></p>
                    <p>Username : 555 499 25 <br> Password : 12466</p>
                    <button class="btn-map">Downlaod Vbg</button>
                </div>

                <div class="card-detail-section">
                    <h4>Syarat & Ketentuan</h4>
                    <ol>
                        <li>Pendaftaran dilakukan melalui platform resmi kami.</li>
                        <li>Tiket tidak dapat dipindahtangankan tanpa persetujuan penyelenggara.</li>
                        <li>Kebijakan pembatalan berlaku 7 hari sebelum acara dimulai.</li>
                        <li>Peserta wajib mematuhi tata tertib yang berlaku selama kegiatan.</li>
                        <li>Informasi pribadi peserta dijaga kerahasiaannya.</li>
                    </ol>
                </div>
            </div>

            <div class="content-detail-right">
                <div class="benefit-box">
                    <h4>Benefit</h4>
                    <ol>
                        <li>Materi yang bermanfaat</li>
                        <li>Sertifikat Nasional</li>
                        <li>Relasi</li>
                    </ol>
                </div>
                <div class="card-detail-section">
                    <h4>Jadwal Rinci</h4>
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Sesi</th>
                                <th>Pembicara</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>09:00</td>
                                <td>Pendaftaran & Sambutan</td>
                                <td>Tim Penyelenggara</td>
                            </tr>
                            <tr>
                                <td>10:00</td>
                                <td>Keynote: Masa Depan AI</td>
                                <td>Dr. Ardi Wijaya</td>
                            </tr>
                            <tr>
                                <td>11:00</td>
                                <td>Panel: Etika dalam Teknologi</td>
                                <td>Sarah Tan, Budi Santoso</td>
                            </tr>
                            <tr>
                                <td>12:30</td>
                                <td>Makan Siang</td>
                                <td>-</td>
                            </tr>
                            <tr>
                                <td>13:30</td>
                                <td>Workshop: Web Modern</td>
                                <td>Fitriani Dewi</td>
                            </tr>
                            <tr>
                                <td>15:00</td>
                                <td>Sesi Demo Startup</td>
                                <td>Berbagai Startup</td>
                            </tr>
                            <tr>
                                <td>16:30</td>
                                <td>Penutupan & Jaringan</td>
                                <td>Tim Penyelenggara</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div>
            <div class="pendapatan-box">
                <h5>Daftar Peserta</h5>
                <table class="tabel-pendapatan">
                    <thead>
                        <tr>
                            <th style="background-color: #E4E4E6;" scope="col">Nama Peserta</th>
                            <th style="background-color: #E4E4E6;" scope="col">Institusi</th>
                            <th style="background-color: #E4E4E6;" scope="col">Profesi</th>
                            <th style="background-color: #E4E4E6;" scope="col">Proggress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="registered-btn">Registered</button></td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="registered-btn">Registered</button></td>

                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="attended-btn">Attended</button></td>

                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="feedback-btn">Feedback</button></td>

                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="certificate-btn">Certificate</button></td>

                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="feedback-btn">Feedback</button></td>

                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="certificate-btn">Certificate</button></td>

                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="feedback-btn">Feedback</button></td>

                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="certificate-btn">Certificate</button></td>

                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="certificate-btn">Certificate</button></td>
                        </tr>
                        <tr>
                            <td>Workshop UI</td>
                            <td>12/10/2025</td>
                            <td>200 Peserta</td>
                            <td><button class="certificate-btn">Certificate</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

</body>

</html>