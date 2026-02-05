{{-- @include ('partials.navbar-after-login') --}}
@include ('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Reseller IdSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    {{--
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="hero-box" aria-hidden="true"></div>
    <main class="container-xl pt-4 mt-4">
        <!-- Navbar -->
        {{-- <nav class="navbar navbar-expand-lg navbar-dark sticky-top mb-4 p-2 mt-3 navbar-bg">
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


        <div class="container-xxl">
            <!-- Card for Target Komisi Bulan Ini -->
            {{-- <div class="card mb-4  shadow-sm">
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

            <!-- Withdraw Komisi Content -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body p-4 mt-2">
                    <h5 class="mb-3 d-flex align-items-start gap-2">
                        <i class="bi bi-cash-coin text-warning fs-2"></i>
                        <span class="fw-bold">Withdraw Komisi</span>
                    </h5>

                    <div class="row g-4 align-items-end">
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100">
                                <small class="text-muted">Saldo Bisa Ditarik</small>
                                <h3 class="text-success mb-0">Rp {{ number_format($user->wallet_balance, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <small class="text-muted">Saldo Pending</small>
                                <h3 class="text-warning mb-0">Rp {{ number_format($pendingEarnings, 0, ',', '.') }}</h3>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted mt-2 mb-2 border border-warning rounded-3 p-2 text-center">
                                Minimal penarikan Rp 50.000
                            </div>
                            <button class="btn btn-warning px-5 py-2 w-100 fw-bold" data-bs-toggle="modal"
                                data-bs-target="#withdrawModal">
                                <i class="bi bi-wallet-fill me-1"></i>
                                Tarik Komisi
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Section -->
            <div class="row row-cols-1 row-cols-md-3">
                <div class="col mb-3">
                    <div class="card h-100  shadow-sm">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-body-secondary">Total Earnings (All Time)</h6>
                                <h3 class="card-title">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h3>
                                <p class="card-text text-success mb-0">+Rp {{ number_format($earningsThisMonth/1000, 0)
                                    }}k bulan ini</p>
                            </div>
                            <i class="bi bi-cash-stack fs-1 text-warning"></i>
                        </div>
                        <div class="card-footer">
                            <small class="text-body-secondary">Pembaruan terakhir: 09:40:05 WIB</small>
                        </div>
                    </div>
                </div>
                <div class="col mb-3">
                    <div class="card h-100  shadow-sm">
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
                <div class="col mb-3">
                    <div class="card h-100  shadow-sm">
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
            <div class="card mb-4  shadow-sm">
                <div class="card-body mt-3 mb-2">
                    <h5 class="fw-bold"><i class="bi bi-megaphone-fill text-warning text-secondary me-2"></i>Referral
                        Tools</h5>

                    <!-- Referral Input Fields (Code, Link, Caption) -->
                    <div class="row g-4 mt-1 align-items-end">
                        <div class="col-lg-4">
                            <label for="referralCode"
                                class="form-label fw-medium small text-body-secondary border-start border-warning border-4 ps-2 mb-2">
                                Referral Code
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" id="referralCode" value="{{ $user->referral_code }}">
                                <button class="btn btn-warning text-white" type="button"
                                    onclick="copyToClipboard(this, 'referralCode')" title="Copy code">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label for="referralLink"
                                class="form-label fw-medium small text-body-secondary border-start border-warning border-4 ps-2 mb-2">
                                Referral Link
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" id="referralLink"
                                    value="{{ url('/register?ref=' . $user->referral_code) }}">
                                <button class="btn btn-warning text-white" type="button"
                                    onclick="copyToClipboard(this, 'referralLink')" title="Copy link">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label for="referralCaption"
                                class="form-label fw-medium small text-body-secondary border-start border-warning border-4 ps-2 mb-2">
                                Broadcast Caption
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light text-truncate" id="referralCaption"
                                    value="Join IdSpora and unlock exclusive events! Use my referral link to get started: https://idspora..."
                                    readonly>
                                <button class="btn btn-warning text-white" type="button"
                                    onclick="copyToClipboard(this, 'referralCaption')" title="Copy caption">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <!-- How It Works Section -->
                    <h5 class="card-title mb-3">How It Works</h5>
                    <div class="row row-cols-1 g-4 mt-1">
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
            <div class="row g-4 mb-4">
                {{-- Level Section --}}
                <div class="col-lg-4">
                    <div class="card h-100  shadow-sm rounded-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Level Anda</h5>

                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block mb-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                                        style="width: 80px; height: 80px; background: linear-gradient(135deg, #8B4513 0%, #CD853F 100%); color: white;">
                                        <i class="bi bi-person-fill fs-1"></i>
                                    </div>
                                    <span
                                        class="position-absolute bottom-0 start-50 translate-middle-x badge bg-white text-dark border shadow-sm rounded-pill px-3 py-1 mt-2">
                                        Bronze
                                    </span>
                                </div>
                                <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                                <p class="text-muted small">{{ $totalReferrals }} Referrals</p>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between small fw-bold mb-1">
                                    <span>Progress ke {{ ($level == 'Bronze') ? 'Silver' : (($level == 'Silver') ?
                                        'Gold' : 'Max') }}</span>
                                    <span class="text-warning">{{ $nextLevelTarget }} lagi</span>
                                </div>
                                <div class="progress" role="progressbar" aria-label="Warning Animated striped"
                                    aria-valuenow="24" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                        style="width: {{ $progress }}%">
                                    </div>
                                </div>
                            </div>

                            <hr class="border-secondary-subtle border-dashed">

                            <h6 class="fw-semibold small text-muted mb-3">Sistem Tiers</h6>
                            <div class="d-flex flex-column gap-2">
                                <div
                                    class="p-2 rounded-3 border border-warning bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-star text-warning me-2"></i>
                                        <div class="lh-1">
                                            <span class="d-block fw-bold small text-dark">Bronze (0-50)</span>
                                            <span class="d-block text-muted" style="font-size: 10px;">Komisi 10%</span>
                                        </div>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-warning"></i>
                                </div>
                                <div
                                    class="p-2 rounded-3 border d-flex justify-content-between align-items-center opacity-75">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-star-half text-secondary me-2"></i>
                                        <div class="lh-1">
                                            <span class="d-block fw-bold small text-dark">Silver (51-150)</span>
                                            <span class="d-block text-muted" style="font-size: 10px;">Komisi 12%</span>
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 10px;"><i
                                            class="bi bi-lock-fill"></i></small>
                                </div>
                                <div
                                    class="p-2 rounded-3 border d-flex justify-content-between align-items-center opacity-50">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-star-fill text-secondary me-2"></i>
                                        <div class="lh-1">
                                            <span class="d-block fw-bold small text-dark">Gold (151+)</span>
                                            <span class="d-block text-muted" style="font-size: 10px;">Komisi 15%</span>
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size: 10px;"><i
                                            class="bi bi-lock-fill"></i></small>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Top Resellers Section --}}
                <div class="col-lg-4">
                    <div class="card h-100  shadow-sm rounded-4">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">Top Resellers</h5>
                                <i class="bi bi-trophy-fill text-warning fs-5"></i>
                            </div>

                            <ul class="list-group list-group-flush flex-grow-1 fw-medium">
                                <li class="list-group-item  px-0 py-2 d-flex align-items-center">
                                    <div class="text-warning fst-italic me-2" style="min-width: 30px;">#1</div>
                                    <div class="rounded-circle bg-warning text-white fw-bold d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">JO</div>
                                    <div class="flex-grow-1 lh-sm">
                                        <div class="fw-bold text-dark small">Jocua Cuherman</div>
                                        <small class="text-muted" style="font-size: 11px;">245 referrals</small>
                                    </div>
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">Rp
                                        1.2M</span>
                                </li>

                                <li class="list-group-item  px-0 py-2 d-flex align-items-center">
                                    <div class="text-secondary fst-italic me-2" style="min-width: 30px;">#2
                                    </div>
                                    <div class="rounded-circle bg-secondary text-white fw-bold d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">VS</div>
                                    <div class="flex-grow-1 lh-sm">
                                        <div class="fw-bold text-dark small">Ver Sianu</div>
                                        <small class="text-muted" style="font-size: 11px;">198 referrals</small>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill">Rp 990k</span>
                                </li>

                                <li class="list-group-item  px-0 py-2 d-flex align-items-center">
                                    <div class="text-secondary fst-italic me-2" style="min-width: 30px;">#3
                                    </div>
                                    <div class="rounded-circle bg-warning bg-opacity-75 text-white fw-bold d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">TR</div>
                                    <div class="flex-grow-1 lh-sm">
                                        <div class="fw-bold text-dark small">Tayo Rapes</div>
                                        <small class="text-muted" style="font-size: 11px;">100 referrals</small>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill">Rp 500k</span>
                                </li>

                                <li class="list-group-item  px-0 py-2 d-flex align-items-center">
                                    <div class="text-secondary fst-italic me-2" style="min-width: 30px;">#4
                                    </div>
                                    <div class="rounded-circle bg-secondary bg-opacity-50 text-white fw-bold d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">BU</div>
                                    <div class="flex-grow-1 lh-sm">
                                        <div class="fw-bold text-dark small">Budi Udin</div>
                                        <small class="text-muted" style="font-size: 11px;">80 referrals</small>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill">Rp 400k</span>
                                </li>

                                <li class="list-group-item  px-0 py-2 d-flex align-items-center opacity-75">
                                    <div class="text-secondary fst-italic me-2" style="min-width: 30px;">#5
                                    </div>
                                    <div class="rounded-circle bg-light text-secondary border fw-bold d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">AY</div>
                                    <div class="flex-grow-1 lh-sm">
                                        <div class="fw-bold text-dark small">Ailop Yu</div>
                                        <small class="text-muted" style="font-size: 11px;">65 referrals</small>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill">Rp 320k</span>
                                </li>
                                <li class="list-group-item  px-0 py-2 d-flex align-items-center opacity-75">
                                    <div class="text-secondary fst-italic me-2" style="min-width: 30px;">#6
                                    </div>
                                    <div class="rounded-circle bg-light text-secondary border fw-bold d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">CS</div>
                                    <div class="flex-grow-1 lh-sm">
                                        <div class="fw-bold text-dark small">Citra Schoolastika</div>
                                        <small class="text-muted" style="font-size: 11px;">50 referrals</small>
                                    </div>
                                    <span class="badge bg-light text-secondary border rounded-pill">Rp 250k</span>
                                </li>
                            </ul>
                            <hr>
                            <div
                                class="p-2 rounded-3 border border-warning bg-warning bg-opacity-10 d-flex align-items-center">
                                <div class="text-dark fst-italic me-2" style="min-width: 30px;">#50</div>
                                <div class="rounded-circle bg-white text-warning border border-warning fw-bold d-flex align-items-center justify-content-center me-3"
                                    style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="flex-grow-1 lh-sm">
                                    <div class="fw-bold text-dark small mb-0">Sutupani</div>
                                    <small class="text-dark opacity-75" style="font-size: 11px;">10 referrals</small>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <span class="badge bg-white text-warning border border-warning rounded-pill"
                                        style="font-size: 9px; letter-spacing: 0.5px;">ANDA</span>
                                    <span class="badge bg-light text-dark border border-warning rounded-pill">Rp
                                        50k</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Riwayat (History) Section --}}
                <div class="col-lg-4">
                    <div class="card h-100  shadow-sm rounded-4">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="fw-bold mb-0">Riwayat</h5>
                                <a href="#" class="text-decoration-none text-warning fw-bold small">Lihat Semua</a>
                            </div>

                            <div class="d-flex flex-column gap-3 flex-grow-1">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Vero Mupon</div>
                                            <small class="text-muted" style="font-size: 11px;">28 Nov • Figma
                                                101</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success small">+Rp 25.000</div>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-1"
                                            style="font-size: 9px;">Paid</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Maria Sibowo</div>
                                            <small class="text-muted" style="font-size: 11px;">27 Nov • SLR WS</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success small">+Rp 25.000</div>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-1"
                                            style="font-size: 9px;">Paid</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-clock-fill"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Agvin Amal</div>
                                            <small class="text-muted" style="font-size: 11px;">26 Nov • Web Vol
                                                2</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-secondary small">+Rp 40.000</div>
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-1"
                                            style="font-size: 9px;">Pending</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Misyu Somat</div>
                                            <small class="text-muted" style="font-size: 11px;">25 Nov • Python
                                                101</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success small">+Rp 35.000</div>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-1"
                                            style="font-size: 9px;">Paid</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Siti Aminah</div>
                                            <small class="text-muted" style="font-size: 11px;">24 Nov • Data Sc</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success small">+Rp 50.000</div>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-1"
                                            style="font-size: 9px;">Paid</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-check-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark small">Rina Nose</div>
                                            <small class="text-muted" style="font-size: 11px;">22 Nov • UI/UX</small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-success small">+Rp 25.000</div>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-1"
                                            style="font-size: 9px;">Paid</span>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="mt-3">
                                <button class="btn btn-warning w-100 fw-bold text-dark shadow-sm py-2">
                                    <i class="bi bi-cloud-arrow-down-fill me-3"></i>Download Laporan
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>



            {{-- <div class="card mb-4"> --}}
                <div class="card mb-4  shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3 align-items-center d-flex">
                            <i class="bi bi-question-circle text-warning fs-20 me-4"></i>
                            Frequently Asked Questions
                        </h6>

                        <div class="accordion" id="faqAccordion">

                            <div class="accordion-item mb-2">
                                <h2 class="display-2 accordion-header">
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
                                <h2 class="display-2 accordion-header">
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
                            <button class="btn btn-warning px-4">Hubungi Support</button>
                        </div>

                    </div>
                </div>



            </div>
    </main>




    @include('partials.withdraw-modal')
    @include ('partials.footer-after-login')
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
            setTimeout(function () {
                icon.className = originalClass;
            }, 2000);
        }
    </script>
</body>

</html>