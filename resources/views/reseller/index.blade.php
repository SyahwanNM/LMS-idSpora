{{-- @include ('partials.navbar-after-login') --}}
@include ('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Reseller IdSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    {{--
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="hero-box" aria-hidden="true"></div>
    <main class="container-xxl pt-5 mt-4">
        <!-- Navbar -->
        {{-- <nav class="navbar navbar-expand-lg navbar-dark sticky-top mb-4 rounded-4 p-2 mt-3 navbar-bg">
            <div class="container-fluid">
                <a class="navbar-brand me-auto" href="#">
                    <img src="img/logo.png" alt="IdSpora" height="50">
                </a>
                <div class="collapse navbar-collapse mx-auto justify-content-center" id="navbarScroll">
                    <ul class="navbar-nav my-2 my-lg-0 navbar-nav-scroll" style="--bs-scroll-height: 100px;">
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2 active" aria-current="page" href="index.html">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="products.html">Products</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mx-lg-2" href="earnings.html">Earnings</a>
                        </li>
                    </ul>
                </div>
                <div class="profile d-flex align-items-center">
                    <img src="img/profile.jpg" alt="Profile Picture" class="profile-pic">
                    <div class="profile-info ms-3">
                        <div class="profile-name">Vero Glorify</div>
                        <div class="profile-email">veroglorify@mail.com</div>
                    </div>
                </div>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll"
                    aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav> --}}
        <!-- End of Navbar -->


        <div class="container-xl">
            <!-- Card for Target Komisi Bulan Ini -->
            {{-- <div class="card mb-4 border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-bullseye fs-1 text-warning"></i>
                        <div class="ms-2">
                            <h5 class="card-title mb-0">Target Komisi Bulan Ini</h5>
                            <p class="card-text">Kamu sudah mencapai 80% dari target!</p>
                        </div>
                    </div>
                    <div class="progress" role="progressbar" aria-label="Warning Animated striped example"
                        aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                            style="width: 75%">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <p class="card-text">Rp 8.000.000</p>
                        <p class="card-text">Rp 10.000.000</p>
                    </div>
                </div>
            </div> --}}
            <!-- End of Card for Target Komisi Bulan Ini -->

<!-- Withdraw Section -->
<div class="card mb-4 border-0 shadow-sm rounded-4">
    <div class="card-body p-4">
        <h5 class="mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-cash-coin text-warning fs-1"></i>
            <span class="ms-2">Withdraw Komisi</span>
        </h5>

        <div class="row g-4 align-items-center">
            <div class="col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <small class="text-muted">Saldo Bisa Ditarik</small>
                    <h3 class="text-success mb-0">Rp 1.200.000</h3>
                </div>
            </div>

            <div class="col-md-4">
                <div class="border rounded-3 p-3 h-100 bg-light">
                    <small class="text-muted">Saldo Pending</small>
                    <h3 class="text-warning mb-0">Rp 640.000</h3>
                </div>
            </div>

            <div class="col-md-4 text-end">
                <button class="btn btn-warning rounded-pill px-5 py-2">
                    <i class="bi bi-wallet2 me-1"></i>
                    Tarik Komisi
                </button>
                <div class="small text-muted mt-2">
                    Minimal penarikan Rp 50.000
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Cards Section -->
            <div class="row row-cols-1 row-cols-md-3 mb-4">
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-body-secondary mb-1">Total Referrals</h6>
                                <h3 class="card-title mb-1">150</h3>
                                <div class="d-flex">
                                    <p class="card-text text-success mb-0">+15 bulan ini</p>
                                    <i class="bi bi-arrow-up-right text-success ms-3"></i>
                                </div>

                            </div>
                            <i class="bi bi-person-circle fs-1 text-warning"></i>

                        </div>
                        <div class="card-footer">
                            <small class="text-body-secondary">Pembaruan terakhir: 09:40:05 WIB</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-body-secondary">Total Earnings (All Time)</h6>
                                <h3 class="card-title">Rp 5.000.000</h3>
                                <p class="card-text text-success mb-0">+Rp 520k bulan ini</p>
                            </div>
                            <i class="bi bi-cash-stack fs-1 text-warning"></i>
                        </div>
                        <div class="card-footer">
                            <small class="text-body-secondary">Pembaruan terakhir: 09:40:05 WIB</small>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-body-secondary">Conversion Rate</h6>
                                <h3 class="card-title">24.5%</h3>
                                <p class="card-text text-success mb-0">+2.3% dari bulan lalu</p>
                            </div>
                            <i class="bi bi-graph-up fs-1 text-warning"></i>
                        </div>
                        <div class="card-footer">
                            <small class="text-body-secondary">Pembaruan terakhir: 09:40:05 WIB</small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Cards Section -->


            <!-- Referral Tools Section -->
            <div class="card mb-4 border-0 shadow-sm rounded-4">
                <div class="card-body mt-3 mb-2">
                    <h5 class="card-title mb-0">Referrals Tools</h5>
                    <div class="row g-4 mt-1 align-items-end">
    <div class="col-lg-4">
        <label for="referralCode" class="form-label fw-medium small text-body-secondary border-start border-warning border-4 ps-2 mb-2">
            Referral Code
        </label>
        <div class="input-group">
            <input type="text" class="form-control bg-light" id="referralCode" value="616ja03095" readonly>
            <button class="btn btn-warning text-white" type="button" onclick="copyToClipboard(this, 'referralCode')" title="Copy code">
                <i class="bi bi-clipboard"></i>
            </button>
        </div>
    </div>

    <div class="col-lg-4">
        <label for="referralLink" class="form-label fw-medium small text-body-secondary border-start border-warning border-4 ps-2 mb-2">
            Referral Link
        </label>
        <div class="input-group">
            <input type="text" class="form-control bg-light" id="referralLink" value="https://idspora.com/course/?ref=616ja03095" readonly>
            <button class="btn btn-warning text-white" type="button" onclick="copyToClipboard(this, 'referralLink')" title="Copy link">
                <i class="bi bi-clipboard"></i>
            </button>
        </div>
    </div>

    <div class="col-lg-4">
        <label for="referralCaption" class="form-label fw-medium small text-body-secondary border-start border-warning border-4 ps-2 mb-2">
            Broadcast Caption
        </label>
        <div class="input-group">
            <input type="text" class="form-control bg-light text-truncate" id="referralCaption" 
                   value="Join IdSpora and unlock exclusive events! Use my referral link to get started: https://idspora..." readonly>
            <button class="btn btn-warning text-white" type="button" onclick="copyToClipboard(this, 'referralCaption')" title="Copy caption">
                <i class="bi bi-clipboard"></i>
            </button>
        </div>
    </div>
</div>
                    <hr>
                    <h5 class="card-title mb-0">How It Works</h5>
                    <div class="row row-cols-1 row-cols-md-3 g-4 mt-1">
                        <div class="col-lg-4 text-center">
                            <i class="bi bi-share-fill fs-1 mb-2 text-warning"></i><br>
                            <p>Share your referral code with friends and family or on social media to earn rewards!</p>
                        </div>

                        <div class="col-lg-4 text-center">
                            <i class="bi bi-gift-fill fs-1 mb-2 text-warning"></i><br>
                            <p>When your friends book a course or an event using your referral code, they'll
                                automatically
                                get
                                15% off their purchase.</p>
                        </div>
                        <div class="col-lg-4 text-center">
                            <i class="bi bi-cash-stack fs-1 mb-2 text-warning"></i><br>
                            <p>For every successful booking made with your code, you'll receive 10% of the ticket price
                                as
                                your
                                reward!</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Referral Tools Section -->

            <!-- Recent Referrals Table-->
            <div class="card mb-4 border-0 shadow-sm rounded-4">
                <div class="card-body mt-3 mb-2">
                    <h5 class="card-title mb-1 fw-semibold">Riwayat Komisi Terbaru</h5>
                    <p class="text-muted-sm mb-4">Detail transaksi dan komisi yang Anda peroleh
                    </p>
                    <div class="table-responsive">
                        <table class="table align-middle text-center table-hover">
                            <thead>
                                <tr>
                                    <th class="fw-semibold small">Tanggal</th>
                                    <th class="fw-semibold small">Nama Pembeli</th>
                                    <th class="fw-semibold small">Kursus / Event</th>
                                    <th class="fw-semibold small">Jumlah Pembelian</th>
                                    <th class="fw-semibold small">Komisi</th>
                                    <th class="fw-semibold small">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>28 Nov 2025</td>
                                    <td>Vero Glorify</td>
                                    <td>Workshop Figma 101</td>
                                    <td>Rp200.000</td>
                                    <td class="fw-semibold text-success">Rp25.000</td>
                                    <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success px-4 py-2 rounded-pill">Dibayar</span></td>
                                </tr>
                                <tr>
                                    <td>27 Nov 2025</td>
                                    <td>Maria S.</td>
                                    <td>Workshop SLR & Bibliometrik</td>
                                    <td>Rp200.000</td>
                                    <td class="fw-semibold text-success">Rp25.000</td>
                                    <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success px-4 py-2 rounded-pill">Dibayar</span></td>
                                </tr>
                                <tr>
                                    <td>26 Nov 2025</td>
                                    <td>Agvin P.</td>
                                    <td>Workshop Web Design vol 2</td>
                                    <td>Rp500.000</td>
                                    <td class="fw-semibold text-success">Rp40.000</td>
                                    <td class="text-center"><span class="badge bg-warning bg-opacity-10 text-warning px-4 py-2 rounded-pill">Menunggu</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Recent Referrals Table -->

            <div class="row">
                <div class="col-md-6 mb-4">


                    {{-- Peringkat Reseller Bulan Ini --}}
                    <div class="card rounded-4 mb-4 h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-semibold">Peringkat Reseller Bulan Ini</h5>
                            <p class="text-muted mb-4">Top 3 reseller dengan performa terbaik</p>

                            <ul class="list-group list-group-flush">

                                <!-- #1 -->
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 fw-bold text-warning fs-5">#1</div>
                                        <div>
                                            <div class="fw-semibold small">Stephanie A. Tarigan</div>
                                            <small class="text-muted">245 referrals</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning text-dark rounded-pill">Gold</span>
                                        <div class="fw-semibold">Rp 1.225.000</div>
                                    </div>
                                </li>

                                <!-- #2 -->
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 fw-bold text-secondary fs-5">#2</div>
                                        <div>
                                            <div class="fw-semibold small">Vero Glorify S.</div>
                                            <small class="text-muted">198 referrals</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-secondary rounded-pill">Silver</span>
                                        <div class="fw-semibold">Rp 990.000</div>
                                    </div>
                                </li>

                                <!-- #3 -->
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 fw-bold text-secondary fs-5">#3</div>
                                        <div>
                                            <div class="fw-semibold small">Tayo Wenas</div>
                                            <small class="text-muted">100 referrals</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-secondary rounded-pill">Silver</span>
                                        <div class="fw-semibold">Rp 500.000</div>
                                    </div>
                                </li>
                                <!-- #50 user -->
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light border rounded-3 mt-2">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 fw-bold text-warning fs-5">#50</div>
                                        <div>
                                            <div class="fw-semibold small">You (Sutupani)</div>
                                            <small class="text-muted">10 referrals</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-warning text-dark rounded-pill">Bronze</span>
                                        <div class="fw-semibold">Rp 50.000</div>
                                    </div>
                                </li>

                            </ul>
                        </div>
                    </div>
                    <!-- End of Peringkat Reseller Bulan Ini -->
                </div>
                <div class="col-md-6 mb-4">
                    <!-- Sistem Tingkat (Tiers) Section -->
                    <div class="card rounded-4 mb-4 h-100 border-0 shadow-sm rounded-4">
                        <div class="card-body p-4">

                            <h5 class="fw-semibold">Sistem Tingkat (Tiers)</h5>
                            <p class="text-muted mb-4">Tingkat Anda saat ini: <span
                                    class="fw-bold text-warning">Bronze</span>
                            </p>

                            <div class="row text-center mb-2">

                                <div class="col-md-4 mb-3">
                                    <div class="border rounded-3 p-3 h-100 border-warning shadow-sm">
                                        <h6 class="fw-bold text-warning">Bronze</h6>
                                        <p class="mb-1">0-50 referrals</p>
                                        <small class="text-muted">Komisi: 10%</small>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="border rounded-3 p-3 h-100">
                                        <h6 class="fw-bold text-secondary">Silver</h6>
                                        <p class="mb-1">51-150 referrals</p>
                                        <small class="text-muted">Komisi: 12%</small>
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <div class="border rounded-3 p-3 h-100">
                                        <h6 class="fw-bold text-warning">Gold</h6>
                                        <p class="mb-1">151+ referrals</p>
                                        <small class="text-muted">Komisi: 15%</small>
                                    </div>
                                </div>

                            </div>

                            <p class="fw-semibold mb-1">Progress ke Silver</p>
                            <div class="progress mb-2 progress-h-sm">
                                <div class="progress-bar bg-dark" style="width: 76%;"></div>
                            </div>

                            <small class="text-muted">
                                Anda butuh <span class="text-success fw-bold">36 referrals</span> lagi untuk naik ke
                                Silver dan
                                mendapatkan <span class="fw-bold">+2% komisi!</span>
                            </small>

                        </div>
                    </div>
                    <!-- End of Sistem Tingkat (Tiers) Section -->
                </div>
            </div>



            {{-- <div class="card rounded-4 mb-4"> --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-question-circle text-warning me-1"></i>
                        Frequently Asked Questions
                    </h6>

                    <div class="accordion" id="faqAccordion">

                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#faq1">
                                    Bagaimana cara menarik dana komisi saya?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small">
                                    Dana dapat ditarik melalui menu "Withdraw" di dashboard reseller.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#faq2">
                                    Kapan saya mendapatkan komisi dari referral?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small">
                                    Komisi masuk setelah pembelian berhasil & tidak refund.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#faq3">
                                    Berapa lama link referral saya aktif?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small">
                                    Link berlaku tanpa batas selama akun Anda aktif.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#faq4">
                                    Apakah saya bisa menggunakan referral code untuk diri sendiri?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small">
                                    Tidak, referral untuk pembelian orang lain.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-2">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse"
                                    data-bs-target="#faq5">
                                    Bagaimana cara naik ke tier yang lebih tinggi?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-muted small">
                                    Tingkatkan jumlah referral sesuai persyaratan tier.
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4 p-3 rounded-3 bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">Masih ada pertanyaan?</div>
                            <small class="text-muted">Tim support kami siap membantu Anda 24/7</small>
                        </div>
                        <button class="btn btn-warning rounded-pill px-4">Hubungi Support</button>
                    </div>

                </div>
            </div>



        </div>
    </main>




@include ('partials.footer-before-login')
    <script>
    function copyToClipboard(button, elementId) {
        // 1. Ambil teks dan copy
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); // Untuk support mobile
        navigator.clipboard.writeText(copyText.value);

        // 2. Logika Ubah Icon (Clipboard -> Checklist -> Clipboard)
        var icon = button.querySelector('i'); // Ambil elemen ikon di dalam tombol
        
        // Simpan class asli (bi-clipboard)
        var originalClass = "bi bi-clipboard";
        // Class untuk checklist
        var successClass = "bi bi-check-lg"; // Bootstrap icon checklist tebal

        // Ubah ikon jadi checklist
        icon.className = successClass;

        // Kembalikan ke ikon awal setelah 2 detik (2000ms)
        setTimeout(function() {
            icon.className = originalClass;
        }, 2000);
    }
</script>
</body>

</html>