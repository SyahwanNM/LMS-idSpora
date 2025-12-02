<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include("partials.navbar-after-login")
    <div class="box_luar_payment">
        <div class="link_back_payment_course">
            <a href=""> Home </a>
            <p>/</p>
            <a href=""> Course </a>
            <p>/</p>
            <a href=""> Learn Artificial Inteligence Phyton </a>
            <p>/</p>
            <a href=""> Payment </a>
        </div>
        <div class="biodata_payment_course">
            <div class="box_kiri_biodata">
                <h5>Data Peserta</h5>
                <div class="input_biodata">
                    <p>Email</p>
                    <input class="kolom_input_biodata" type="text">
                </div>
                <div class="input_biodata">
                    <p>Nama Lengkap</p>
                    <div class="info_biodata">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="red" class="bi bi-info-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                        </svg>
                        <p>Nama akan digunakan pada sertifikat</p>
                    </div>
                    <input class="kolom_input_biodata" type="text">
                </div>
                <div class="input_biodata">
                    <p>No Whatsapp</p>
                    <div class="whatsapp_biodata">
                        <div class="dropdown">
                            <button class="btn_nomor btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> Kode Dial
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">+62</a></li>
                                <li><a class="dropdown-item" href="#">+68</a></li>
                                <li><a class="dropdown-item" href="#">+72</a></li>
                            </ul>
                        </div>
                        <input class="input_nomor" type="text" placeholder="No Whatsapp">
                    </div>
                </div>
                <div class="input_biodata">
                    <p>Metode Pembayaran</p>
                    <div class="whatsapp_biodata">
                        <div class="radio_input_box">
                            <div class="radio_input">
                                <input type="radio">
                                <p>Transfer</p>
                            </div>
                            <div class="radio_input">
                                <input type="radio">
                                <p>Bank</p>
                            </div>
                            <div class="radio_input">
                                <input type="radio">
                                <p>Qris</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box_kanan_biodata">
                <div class="box_biodata">
                    <h3>Order Detail</h3>
                    <div class="box_event_payment">
                        <img src="https://img.freepik.com/vektor-premium/live-concert-horizontal-banner-template_23-2150997973.jpg"
                            class="d-block" alt="...">
                        <div class="judul_event">
                            <h4>Digital Marketing Masterclass 2025</h4>
                            <p class="penyelenggara">Idspora</p>
                            <p class="harga_judul_event">Rp 50.000</p>
                        </div>
                    </div>
                    <div class="harga_teks_payment">
                        <div class="teks_payment">
                            <p>Total</p>
                            <h4>Rp 50.000</h4>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-file-earmark" viewBox="0 0 16 16">
                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
                        </svg>
                    </div>
                </div>
                <button class="btn_bayar_payment">Bayar</button>
            </div>

        </div>

    </div>
</body>

</html>