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
    <style>
        /* Typography & layout consistency */
        body {
            background-color: #f8f9fa !important;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Subtle transition & hover animations */
        .hover-card-up {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, border-color 0.3s ease;
            border-radius: 16px !important;
        }
        .hover-card-up:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06) !important;
        }

        /* Hover scale for button */
        .hover-scale {
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, box-shadow 0.2s ease;
        }
        .hover-scale:hover {
            transform: scale(1.015);
            box-shadow: 0 6px 15px rgba(255, 193, 7, 0.2) !important;
        }

        /* Fade-in animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeInUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        /* FAQ Section Hover Fix (Remove yellow hover background/color) */
        .accordion-button {
            transition: background-color 0.2s ease, color 0.2s ease;
            color: #212529 !important;
            font-weight: 500 !important;
            background-color: #ffffff !important;
            border: 1px solid rgba(0,0,0,.08) !important;
        }
        .accordion-button:hover {
            background-color: #f8f9fa !important; 
            color: #212529 !important;
        }
        .accordion-button:focus {
            box-shadow: none !important;
            border-color: rgba(0, 0, 0, 0.08) !important;
            background-color: #ffffff !important;
        }
        .accordion-button:not(.collapsed) {
            background-color: #fffaf0 !important; 
            color: #ffc107 !important;
            border-color: #fcd34d !important;
        }

        /* Table UI cleanups */
        .table thead th {
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-bottom: 2px solid #dee2e6 !important;
        }

        .reseller-action-btn {
            width: 44px;
            height: 44px;
            padding: 0;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .reseller-action-btn:hover {
            transform: scale(1.05);
        }

        @media (max-width: 576px) {
            .reseller-action-btn {
                width: 40px;
                height: 40px;
                border-radius: 10px;
            }
        }

        /* Onboarding Tour Style Rules */
        .tour-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.55);
            z-index: 1040;
            transition: opacity 0.3s ease;
        }

        .tour-highlighted-element {
            position: relative;
            z-index: 1045 !important;
            box-shadow: 0 0 0 10px rgba(109, 40, 217, 0.25), 0 0 0 9999px rgba(0, 0, 0, 0.65) !important;
            pointer-events: none; /* disable pointer events during highlighting */
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .tour-popover {
            position: absolute;
            z-index: 1050;
            background-color: #ffffff;
            border-radius: 16px;
            padding: 20px;
            width: 320px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.05);
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            pointer-events: auto;
        }

        .tour-popover.show {
            opacity: 1;
            transform: scale(1);
        }

        .tour-popover-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1e1b4b; /* Navy */
            margin-bottom: 8px;
        }

        .tour-popover-desc {
            font-size: 0.85rem;
            color: #64748b; /* Slate 500 */
            line-height: 1.5;
            margin-bottom: 0;
        }

        .tour-popover-progress {
            font-size: 0.75rem;
            color: #94a3b8;
        }

        /* Popover Arrow */
        .tour-popover-arrow {
            position: absolute;
            width: 0;
            height: 0;
            border-style: solid;
        }

        /* Positioning variations for popover arrow */
        .tour-popover[data-popper-placement^="top"] .tour-popover-arrow {
            bottom: -8px;
            left: calc(50% - 8px);
            border-width: 8px 8px 0 8px;
            border-color: #ffffff transparent transparent transparent;
        }

        .tour-popover[data-popper-placement^="bottom"] .tour-popover-arrow {
            top: -8px;
            left: calc(50% - 8px);
            border-width: 0 8px 8px 8px;
            border-color: transparent transparent #ffffff transparent;
        }

        .tour-popover[data-popper-placement^="left"] .tour-popover-arrow {
            right: -8px;
            top: calc(50% - 8px);
            border-width: 8px 0 8px 8px;
            border-color: transparent transparent transparent #ffffff;
        }

        .tour-popover[data-popper-placement^="right"] .tour-popover-arrow {
            left: -8px;
            top: calc(50% - 8px);
            border-width: 8px 8px 8px 0;
            border-color: transparent #ffffff transparent transparent;
        }
    </style>
</head>

<body>
    @include ('partials.navbar-after-login')
    <div class="hero-box" aria-hidden="true"></div>
    <main class="pt-4 mt-4">
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
            <div id="tour-withdraw-box" class="card mb-4 border-0 shadow-sm animate-fade-in" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="m-0 d-flex align-items-center gap-2 text-dark">
                            <i class="bi bi-cash-coin text-warning fs-3"></i>
                            <span class="fw-semibold" style="letter-spacing: -0.2px;">Withdraw Komisi</span>
                        </h5>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill d-flex align-items-center gap-1 fw-semibold px-3 py-1.5" onclick="startOnboardingTour()" style="font-size: 0.8rem;">
                            <i class="bi bi-question-circle"></i>
                            <span>Panduan Pengguna</span>
                        </button>
                    </div>

                    <div class="row g-4 align-items-center">
                        <!-- Saldo Bisa Ditarik -->
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 bg-white" style="border-color: #e5e7eb !important;">
                                <div class="text-muted small mb-1" style="font-size: 0.85rem; font-weight: 400;">Saldo Bisa Ditarik</div>
                                <h3 class="mb-0 text-success" style="font-weight: 500; font-size: 2.1rem; letter-spacing: -0.5px;">
                                    Rp {{ number_format($user->wallet_balance, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>

                        <!-- Saldo Pending -->
                        <div class="col-md-4">
                            <div class="border rounded-3 p-3 bg-light" style="border-color: #e5e7eb !important; background-color: #f9fafb !important;">
                                <div class="text-muted small mb-1" style="font-size: 0.85rem; font-weight: 400;">Saldo Pending</div>
                                <h3 class="mb-0 text-warning" style="font-weight: 500; font-size: 2.1rem; letter-spacing: -0.5px;">
                                    Rp {{ number_format($pendingEarnings, 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>

                        <!-- Aksi Penarikan -->
                        <div class="col-md-4 d-flex flex-column gap-2">
                            <div class="py-2 px-3 border border-warning rounded-pill text-center small bg-white text-muted" 
                                 style="font-size: 0.85rem; font-weight: 500;">
                                Minimal penarikan Rp 50.000
                            </div>
                            <button class="btn btn-warning py-2 w-100 fw-semibold d-flex align-items-center justify-content-center gap-2 text-dark rounded-3 hover-scale" 
                                    data-bs-toggle="modal" data-bs-target="#withdrawModal"
                                    style="font-size: 0.95rem;">
                                <i class="bi bi-briefcase-fill"></i>
                                <span>Tarik Komisi</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Section -->
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-3 mb-4">
                <!-- Card 1: Total Earnings -->
                <div class="col animate-fade-in">
                    <div class="card h-100 shadow-sm border-0 hover-card-up" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="text-muted small fw-medium mb-1 d-flex align-items-center gap-1" style="font-size: 0.8rem;">Total Earnings
                                        <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip" title="Total komisi lunas yang Anda peroleh." style="font-size: 0.75rem; cursor: help;"></i>
                                    </h6>
                                    <h4 class="fw-semibold mb-0 text-dark">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h4>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                                    <i class="bi bi-cash-stack fs-5"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <span class="text-success small fw-medium">+Rp {{ number_format($earningsThisMonth/1000, 0) }}k bulan ini</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Total Klik Link -->
                <div class="col animate-fade-in delay-1">
                    <div class="card h-100 shadow-sm border-0 hover-card-up" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="text-muted small fw-medium mb-1 d-flex align-items-center gap-1" style="font-size: 0.8rem;">Total Klik Link
                                        <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip" title="Jumlah total klik pada link referral Anda." style="font-size: 0.75rem; cursor: help;"></i>
                                    </h6>
                                    <h4 class="fw-semibold mb-0 text-dark">{{ number_format($totalClicks, 0, ',', '.') }}</h4>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                                    <i class="bi bi-cursor-fill fs-5"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <span class="text-muted small">Klik unik terdeteksi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Pendaftar Baru -->
                <div class="col animate-fade-in delay-2">
                    <div class="card h-100 shadow-sm border-0 hover-card-up" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="text-muted small fw-medium mb-1 d-flex align-items-center gap-1" style="font-size: 0.8rem;">Pendaftar Baru
                                        <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip" title="Jumlah pengguna yang mendaftar melalui link Anda." style="font-size: 0.75rem; cursor: help;"></i>
                                    </h6>
                                    <h4 class="fw-semibold mb-0 text-dark">{{ number_format($totalSignups, 0, ',', '.') }}</h4>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                                    <i class="bi bi-person-plus-fill fs-5"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <span class="text-muted small">Registrasi akun baru</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Pembelian -->
                <div class="col animate-fade-in delay-3">
                    <div class="card h-100 shadow-sm border-0 hover-card-up" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="text-muted small fw-medium mb-1 d-flex align-items-center gap-1" style="font-size: 0.8rem;">Pembelian
                                        <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip" title="Transaksi pembelian event/course yang menggunakan kode Anda (settled/pending)." style="font-size: 0.75rem; cursor: help;"></i>
                                    </h6>
                                    <h4 class="fw-semibold mb-0 text-dark">{{ number_format($totalPurchases, 0, ',', '.') }}</h4>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                                    <i class="bi bi-cart-check-fill fs-5"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <span class="text-success small fw-medium">+{{ $referralsThisMonth }} transaksi bulan ini</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5: Conversion Rate -->
                <div class="col animate-fade-in delay-4">
                    <div class="card h-100 shadow-sm border-0 hover-card-up" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex flex-column justify-content-between">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="text-muted small fw-medium mb-1 d-flex align-items-center gap-1" style="font-size: 0.8rem;">Conversion Rate
                                        <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip" title="Rasio pembelian dibanding total klik link." style="font-size: 0.75rem; cursor: help;"></i>
                                    </h6>
                                    <h4 class="fw-semibold mb-0 text-dark">{{ number_format($conversionRate, 1) }}%</h4>
                                </div>
                                <div class="bg-warning bg-opacity-10 p-2 rounded-3 text-warning">
                                    <i class="bi bi-graph-up fs-5"></i>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <span class="text-muted small">Rasio Klik-ke-Beli</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visual Performance Chart Card -->
            <div class="card mb-4 border-0 shadow-sm animate-fade-in delay-1" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-4 d-flex align-items-center gap-2 text-dark">
                        <i class="bi bi-graph-up-arrow text-warning fs-3"></i>
                        <span>Grafik Performa Reseller (6 Bulan Terakhir)</span>
                    </h5>
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- End of Cards Section -->


            <!-- Referral Tools Section -->
            <div id="tour-tools-box" class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-megaphone-fill text-warning me-3"></i>
                        Referral Tools
                    </h5>
                    <div class="row g-4 mt-1 align-items-end">
                        <div class="col-lg-4">
                            <label for="referralCode"
                                class="form-label fw-medium small text-body-secondary border-start border-warning border-4 ps-2 mb-2">
                                Referral Code
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" id="referralCode"
                                    value="{{ $user->referral_code }}" readonly>
                                <button class="btn btn-warning text-white" type="button"
                                    onclick="copyToClipboard(this, 'referralCode')" title="Copy code">
                                    <i class="bi bi-clipboard"></i>
                                </button>
                                @if($totalPurchases >= 5)
                                    <button class="btn btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#editReferralCodeModal" title="Kustomisasi Kode">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                @else
                                    <button class="btn btn-outline-secondary" type="button" disabled data-bs-toggle="tooltip" data-bs-placement="top" title="Minimal 5 pembelian untuk ganti kode (Saat ini baru {{ $totalPurchases }} pembelian)">
                                        <i class="bi bi-lock-fill"></i> Edit
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <label for="referralLink"
                                class="form-label fw-medium small text-body-secondary border-start border-warning border-4 ps-2 mb-2">
                                Referral Link
                            </label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" id="referralLink"
                                    value="{{ route('register', ['ref' => $user->referral_code]) }}" readonly>
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
                                    value="Join IdSpora and unlock exclusive courses and events! Use my referral link to get started: {{ route('register', ['ref' => $user->referral_code]) }}"
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
                            <p class="mt-3 text-body-secondary">Bagikan kode referralmu ke teman, keluarga, atau media
                                sosial dan mulai kumpulkan keuntungan!</p>
                        </div>

                        <div class="col-lg-4 text-center">
                            <i class="bi bi-gift-fill fs-1 mb-2 text-warning"></i><br>
                            <p class="mt-3 text-body-secondary">Temanmu otomatis dapat diskon 10% untuk setiap kursus
                                atau event yang mereka beli pakai kodemu.</p>
                        </div>
                        <div class="col-lg-4 text-center">
                            <i class="bi bi-cash-stack fs-1 mb-2 text-warning"></i><br>
                            <p class="mt-3 text-body-secondary">Dapatkan komisi 10-15% dari setiap transaksi yang
                                sukses. Semakin banyak rekan yang Anda ajak, semakin besar komisi yang didapatkan!</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End of Referral Tools Section -->

            <!-- List Produk Komisi Reseller -->
            <div id="tour-products-box" class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-grid-fill text-warning me-2"></i>
                            Produk Komisi Reseller
                        </h5>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <form action="{{ route('reseller.index') }}" method="GET" class="d-flex m-0">
                                <div class="input-group input-group-sm">
                                    <label for="searchProgram" class="visually-hidden">Cari program</label>
                                    <input type="text" id="searchProgram" name="search" class="form-control" placeholder="Cari program..." value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary bg-white" type="submit" aria-label="Cari" title="Cari"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                            <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning-subtle px-3 py-2">
                                Komisi {{ number_format($commissionRate * 100, 0) }}%
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-top mb-0 text-start">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="py-3 border-0 text-start">Program</th>
                                    <th class="py-3 border-0 text-start">Kategori</th>
                                    <th class="py-3 border-0 text-start">Harga</th>
                                    <th class="py-3 border-0 text-start">Komisi</th>
                                    <th class="py-3 border-0 text-start">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commissionProducts as $product)
                                <tr>
                                    <td class="py-3 text-start">
                                        <div class="fw-bold text-dark">{{ $product['program'] }}</div>
                                        <small class="text-muted">{{ $product['type'] }}</small>
                                    </td>
                                    <td class="py-3 text-start">
                                        <span class="badge bg-light text-dark border">{{ $product['category'] }}</span>
                                    </td>
                                    <td class="py-3 text-start fw-bold text-dark">
                                        Rp {{ number_format($product['price'], 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 text-start">
                                        <div class="fw-bold text-success">Rp {{ number_format($product['commission_amount'], 0, ',', '.') }}</div>
                                    </td>
                                    <td class="py-3 text-start">
                                        <button type="button"
                                            class="btn btn-border-warning btn-outline-warning fw-bold shadow-sm reseller-action-btn"
                                            onclick="copyTextValue(this, @js($product['referral_link']))"
                                            title="Salin link referral">
                                            <i class="bi bi-link-45deg"></i>
                                            <span class="visually-hidden">Salin link referral</span>
                                        </button>
                                    </td>                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted small">
                                        <i class="bi bi-box-seam-fill fs-3 d-block mb-2"></i>
                                        Belum ada produk aktif untuk komisi reseller.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($commissionProducts, 'hasPages') && $commissionProducts->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $commissionProducts->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Referrals Table-->
            <div class="row g-4 mb-4">
                {{-- Level Section --}}
                <div class="col-lg-4">
                    <div id="tour-level-box" class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Level Anda</h5>

                            {{-- Bagian Avatar & Badge Utama --}}
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block mb-3">
                                    @php
                                        $badgeStyle = '';
                                        $badgeIcon = '';
                                        $frameStyle = '';
                                        if($level == 'Bronze') {
                                            $badgeStyle = 'background-color: #cd7f32; color: white; border: 1px solid #cd7f32;';
                                            $badgeIcon = 'bi-shield-fill';
                                            $frameStyle = 'border: 3px solid #cd7f32;';
                                        } elseif($level == 'Silver') {
                                            $badgeStyle = 'background-color: #f8f9fa; color: #495057; border: 1px solid #c0c0c0; box-shadow: 0 0 10px rgba(192,192,192,0.6);';
                                            $badgeIcon = 'bi-shield-fill text-secondary';
                                            $frameStyle = 'border: 3px solid #c0c0c0; box-shadow: 0 0 10px rgba(192,192,192,0.4);';
                                        } else {
                                            $badgeStyle = 'background: linear-gradient(135deg, #FFD700 0%, #FDB931 100%); color: #422800; border: 1px solid #FFD700; box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);';
                                            $badgeIcon = 'bi-award-fill';
                                            $frameStyle = 'border: 3px solid #FFD700; box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);';
                                        }
                                    @endphp
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                        style="width: 86px; height: 86px; {{ $frameStyle }}">
                                        <div class="rounded-circle overflow-hidden w-100 h-100">
                                            <img src="{{ Auth::user()->avatar_url }}" alt="Profile Picture" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6b7280&color=ffffff';">
                                        </div>
                                    </div>
                                    <span
                                        class="position-absolute bottom-0 start-50 translate-middle-x badge rounded-pill px-3 py-1 mt-2 d-flex align-items-center gap-1" style="{{ $badgeStyle }}">
                                        <i class="bi {{ $badgeIcon }}"></i> {{ $level }}
                                    </span>
                                </div>
                                <h5 class="fw-bold mb-0">{{ $user->name }}</h5>
                                <p class="text-muted small">{{ $totalReferrals }} Referrals</p>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between small fw-bold mb-1">
                                    <span>Progress ke {{ ($level == 'Bronze') ? 'Silver' : (($level == 'Silver') ?
                                        'Gold' : 'Max') }}</span>
                                    @if($level != 'Gold')
                                    <span class="text-warning">{{ $nextLevelTarget }} lagi</span>
                                    @else
                                    <span class="text-success">Maksimal</span>
                                    @endif
                                </div>
                                <div class="progress" role="progressbar" aria-label="Level Progress"
                                    aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                                        style="width: {{ $progress }}%">
                                    </div>
                                </div>
                            </div>

                            <hr class="border-secondary-subtle border-dashed">

                            {{-- Sistem Tiers List --}}
                            <h6 class="fw-semibold small text-muted mb-3">Sistem Tiers</h6>
                            <div class="d-flex flex-column gap-2">

                                {{-- BRONZE TIER --}}
                                <div
                                    class="p-2 rounded-3 border d-flex justify-content-between align-items-center
                                {{ $level == 'Bronze' ? 'border-warning bg-warning bg-opacity-10' : ($totalReferrals > 50 ? 'border-success bg-success bg-opacity-10' : 'opacity-75') }}">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-shield-fill me-2" style="color: #cd7f32; font-size: 1.1rem;"></i>
                                        <div class="lh-1">
                                            <span class="d-block fw-bold small text-dark">Bronze (0-50)</span>
                                            <span class="d-block text-muted" style="font-size: 10px;">Komisi 10%</span>
                                        </div>
                                    </div>
                                    {{-- Logic Icon: Kalau level Bronze (active) atau lebih tinggi (sudah lewat),
                                    tampilkan checklist --}}
                                    @if($totalReferrals >= 0)
                                    <i
                                        class="bi bi-check-circle-fill {{ $level == 'Bronze' ? 'text-warning' : 'text-success' }}"></i>
                                    @endif
                                </div>

                                {{-- SILVER TIER --}}
                                <div
                                    class="p-2 rounded-3 border d-flex justify-content-between align-items-center 
                                {{ $level == 'Silver' ? 'border-warning bg-warning bg-opacity-10' : ($totalReferrals > 150 ? 'border-success bg-success bg-opacity-10' : 'opacity-50') }}">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-shield-fill text-secondary me-2" style="filter: drop-shadow(0 0 3px rgba(192,192,192,0.8)); font-size: 1.1rem;"></i>
                                        <div class="lh-1">
                                            <span class="d-block fw-bold small text-dark">Silver (51-150)</span>
                                            <span class="d-block text-muted" style="font-size: 10px;">Komisi 12%</span>
                                        </div>
                                    </div>
                                    {{-- Logic Icon: Checklist jika Silver/Gold, Gembok jika Bronze --}}
                                    @if($totalReferrals >= 51)
                                    <i
                                        class="bi bi-check-circle-fill {{ $level == 'Silver' ? 'text-warning' : 'text-success' }}"></i>
                                    @else
                                    <small class="text-muted"><i class="bi bi-lock-fill"></i></small>
                                    @endif
                                </div>

                                {{-- GOLD TIER --}}
                                <div class="p-2 rounded-3 border d-flex justify-content-between align-items-center 
                                {{ $level == 'Gold' ? 'border-warning bg-warning bg-opacity-10' : 'opacity-50' }}">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-award-fill me-2" style="color: #FFD700; filter: drop-shadow(0 0 5px rgba(255,215,0,0.7)); font-size: 1.2rem;"></i>
                                        <div class="lh-1">
                                            <span class="d-block fw-bold small text-dark">Gold (151+)</span>
                                            <span class="d-block text-muted" style="font-size: 10px;">Komisi 15%</span>
                                        </div>
                                    </div>
                                    {{-- Logic Icon: Checklist jika Gold, Gembok jika belum --}}
                                    @if($totalReferrals >= 151)
                                    <i class="bi bi-check-circle-fill text-warning"></i>
                                    @else
                                    <small class="text-muted"><i class="bi bi-lock-fill"></i></small>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Resellers Section --}}
                <div class="col-lg-4">
                    <div id="tour-rank-box" class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body p-4 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0">Top Resellers (Monthly)</h5>
                                <i class="bi bi-trophy-fill text-warning fs-5"></i>
                            </div>

                            <ul class="list-group list-group-flush flex-grow-1 fw-medium">
                                @forelse($topResellers as $index => $reseller)
                                <li
                                    class="list-group-item px-0 py-2 d-flex align-items-center {{ $loop->last ? 'opacity-75' : '' }}">
                                    {{-- Ranking Number --}}
                                    <div class="{{ $index < 3 ? 'text-warning' : 'text-secondary' }} fst-italic me-2"
                                        style="min-width: 30px;">
                                        #{{ $index + 1 }}
                                    </div>

                                    {{-- FOTO PROFIL --}}
                                    @if(!empty($reseller->avatar))
                                    {{-- Jika punya foto di database --}}
                                    <img src="{{ $reseller->avatar_url }}"
                                        alt="{{ $reseller->name }}"
                                        class="rounded-circle border {{ $index < 3 ? 'border-warning' : '' }} me-3"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                    {{-- Fallback: Pakai UI Avatars jika tidak punya foto --}}
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($reseller->name) }}&background={{ $index < 3 ? 'ffc107' : 'e9ecef' }}&color={{ $index < 3 ? 'ffffff' : '6c757d' }}&size=40"
                                        alt="{{ $reseller->name }}"
                                        class="rounded-circle border {{ $index < 3 ? 'border-warning' : '' }} me-3"
                                        style="width: 40px; height: 40px; object-fit: cover;">
                                    @endif

                                    {{-- Nama & Jumlah Referral --}}
                                    <div class="flex-grow-1 lh-sm">
                                        <div class="fw-bold text-dark small">{{ Str::limit($reseller->name, 15) }}</div>
                                        <small class="text-muted" style="font-size: 11px;">{{ $reseller->referrals_count
                                            }} referrals</small>
                                    </div>

                                    {{-- Total Komisi (Badge) --}}
                                    <span
                                        class="badge {{ $index < 3 ? 'bg-warning bg-opacity-10 text-warning' : 'bg-light text-secondary border' }} rounded-pill">
                                        Rp {{ number_format(($reseller->referrals_sum_amount ?? 0) / 1000, 0) }}k
                                    </span>
                                </li>
                                @empty
                                {{-- Empty State (Tetap sama seperti sebelumnya) --}}
                                <li class="list-group-item border-0 text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-trophy text-secondary opacity-25" style="font-size: 3rem;"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-2">Papan Peringkat Masih Kosong!</h6>
                                    <p class="text-muted small mb-3 lh-sm">
                                        Belum ada yang masuk daftar ini. <br>
                                        Ayo bagikan linkmu dan jadilah <strong>Juara #1</strong>!
                                    </p>
                                    <button class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-4"
                                        onclick="copyToClipboard(this, 'referralLink')">
                                        <i class="bi bi-share-fill me-1"></i> Bagikan Sekarang
                                    </button>
                                </li>
                                @endforelse
                            </ul>

                            {{-- Sticky Rank User --}}
                            @if($topResellers->isNotEmpty())
                            <hr>
                            <div
                                class="p-2 rounded-3 border border-warning bg-warning bg-opacity-10 d-flex align-items-center">
                                <div class="text-dark fst-italic me-2" style="min-width: 30px;">#{{ $userRank }}</div>

                                {{-- FOTO PROFIL USER SENDIRI --}}
                                @if(!empty($user->avatar))
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                    class="rounded-circle border border-warning me-3"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=ffc107&color=ffffff&size=40"
                                    alt="{{ $user->name }}" class="rounded-circle border border-warning me-3"
                                    style="width: 40px; height: 40px; object-fit: cover;">
                                @endif

                                <div class="flex-grow-1 lh-sm">
                                    <div class="fw-bold text-dark small mb-0">{{ Str::limit($user->name, 15) }}</div>
                                    <small class="text-dark opacity-75" style="font-size: 11px;">{{ $totalReferrals }}
                                        referrals</small>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <span class="badge bg-white text-warning border border-warning rounded-pill"
                                        style="font-size: 9px; letter-spacing: 0.5px;">ANDA</span>
                                    <span class="badge bg-light text-dark border border-warning rounded-pill">
                                        Rp {{ number_format($totalEarnings / 1000, 0) }}k
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Riwayat (History) Section --}}
                <div class="col-lg-4">
                    <div id="tour-history-box" class="card h-100 border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body p-4 d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h5 class="fw-bold mb-0">Riwayat Referral</h5>
                                    <a href="{{ route('reseller.history') }}"
                                        class="btn btn-sm btn-outline-dark fw-bold px-3 shadow-sm" title="Lihat semua">
                                        <i class="bi bi-clock-history me-1"></i> Lihat Semua
                                    </a>
                            </div>

                            @forelse($history as $item)
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    {{-- Icon Check/Pending/Reject --}}
                                    <div class="rounded-circle {{ $item->status == 'paid' ? 'bg-success text-success' : ($item->status == 'rejected' ? 'bg-danger text-danger' : 'bg-warning text-warning') }} bg-opacity-10 d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        <i
                                            class="bi {{ $item->status == 'paid' ? 'bi-check-lg' : ($item->status == 'rejected' ? 'bi-x-lg' : 'bi-clock-fill') }}"></i>
                                    </div>

                                    {{-- Nama User & Keterangan --}}
                                    <div>
                                        <div
                                            class="fw-bold text-dark small {{ $item->status == 'rejected' ? 'text-decoration-line-through opacity-75' : '' }}">
                                            {{ $item->referredUser->name ?? 'Pengguna Baru' }}
                                        </div>
                                        <small class="text-muted" style="font-size: 11px;">
                                            {{ $item->created_at->format('d M Y') }}
                                        </small>
                                    </div>
                                </div>

                                {{-- Jumlah Komisi & Badge Status --}}
                                <div class="text-end">
                                    <div
                                        class="fw-bold {{ $item->status == 'paid' ? 'text-success' : ($item->status == 'rejected' ? 'text-danger text-decoration-line-through opacity-75' : 'text-secondary') }} small">
                                        {{ $item->status == 'rejected' ? '' : '+' }}Rp {{ number_format($item->amount,
                                        0, ',', '.') }}
                                    </div>
                                    <span
                                        class="badge {{ $item->status == 'paid' ? 'bg-success text-success' : ($item->status == 'rejected' ? 'bg-danger text-danger' : 'bg-warning text-warning') }} bg-opacity-10 rounded-1"
                                        style="font-size: 9px;">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="bi bi-inbox text-muted fs-1"></i>
                                <p class="text-muted small mt-2">Belum ada riwayat referral.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

            <!-- Withdraw History -->
            <div id="tour-withdraw-history-box" class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-arrow-up-right-circle-fill text-warning me-3"></i>
                            Riwayat Penarikan Dana
                        </h5>

                        <div class="d-flex gap-2">
                            <a href="{{ route('reseller.withdraw.history') }}"
                                class="btn btn-sm btn-outline-dark fw-bold px-3 shadow-sm" title="Lihat semua">
                                <i class="bi bi-clock-history me-1"></i> Lihat Semua
                            </a>
                            <a href="{{ route('reseller.withdraw.download') }}"
                                class="btn btn-sm btn-outline-warning text-dark fw-bold px-3 shadow-sm"
                                title="Download Riwayat Penarikan">
                                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Unduh
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #f8fafc; font-weight: 600; border-radius: 8px 0 0 8px;">ID Penarikan</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #f8fafc; font-weight: 600;">Tanggal Pengajuan</th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #f8fafc; font-weight: 600;">Pengguna</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #f8fafc; font-weight: 600;">Bank Tujuan</th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #f8fafc; font-weight: 600;">Nomor Rekening</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #f8fafc; font-weight: 600;">Total Penarikan</th>
                                    <th class="border-0 py-3 text-center text-secondary text-nowrap" style="background-color: #f8fafc; font-weight: 600;">Status</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #f8fafc; font-weight: 600; border-radius: 0 8px 8px 0;">Tanggal Diproses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->withdrawals()->latest()->take(5)->get() as $wd)
                                    @php
                                        $status = strtolower($wd->status);
                                        $isRejected = $status === 'rejected';
                                        
                                        // Set status badge style
                                        if ($status === 'approved') {
                                            $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-check-circle-fill"></i> Approved</span>';
                                        } elseif ($isRejected) {
                                            $statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-x-circle-fill"></i> Rejected</span>';
                                        } else {
                                            $statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-clock-fill"></i> Pending</span>';
                                        }
                                        
                                        // Mask and format account number with spacing
                                        $accountLen = strlen($wd->account_number);
                                        if ($accountLen > 4) {
                                            $maskedRaw = str_repeat('•', $accountLen - 4) . substr($wd->account_number, -4);
                                        } else {
                                            $maskedRaw = $wd->account_number;
                                        }
                                        preg_match_all('/.{1,4}/u', $maskedRaw, $matches);
                                        $maskedFormatted = implode(' ', $matches[0]);
                                    @endphp
                                    <tr>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'opacity-50' : '' }}">#WD-{{ str_pad($wd->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-dark fw-medium {{ $isRejected ? 'opacity-50' : '' }}">{{ $wd->created_at->format('d M Y') }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $wd->created_at->format('H:i') }} WIB</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'opacity-50' : '' }}">{{ $wd->user->name ?? Auth::user()->name }}</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2 {{ $isRejected ? 'opacity-50' : '' }}">
                                                <i class="bi bi-bank fs-5" style="color: var(--primary);"></i>
                                                <span class="fw-medium text-dark">{{ $wd->bank_name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'opacity-50' : '' }}">
                                                {{ $maskedFormatted }}
                                            </div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">A/n. {{ $wd->account_holder }}</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-bold text-success {{ $isRejected ? 'text-danger text-decoration-line-through opacity-50' : '' }}" style="font-size: 1.05rem;">
                                                Rp {{ number_format($wd->amount, 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            {!! $statusBadge !!}
                                        </td>
                                        <td class="py-3">
                                            @if($status !== 'pending')
                                                <div class="text-dark fw-medium">{{ $wd->updated_at->format('d M Y') }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $wd->updated_at->format('H:i') }} WIB</small>
                                            @else
                                                <span class="text-muted fst-italic small">Belum diproses</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-wallet2 fs-1 d-block mb-3 opacity-25"></i>
                                            Belum ada riwayat penarikan dana.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>



            {{-- <div class="card mb-4"> --}}
                <div class="card mb-4 border-0 shadow-sm" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">
                            <i class="bi bi-question-circle-fill text-warning me-3"></i>
                            Frequently Asked Questions
                        </h5>

                        <div class="accordion" id="faqAccordion">

                            
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

                        <div class="mt-4 p-3 rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">Masih ada pertanyaan?</div>
                                <small class="text-muted">Tim support kami siap membantu Anda</small>
                            </div>
                            <a href="https://wa.me/628989260731" target="_blank" class="text-decoration-none">
                                <button class="btn btn-warning px-4 fw-bold">
                                    <i class="bi bi-whatsapp me-2"></i>Hubungi Support
                                </button>
                            </a>
                        </div>

                    </div>
                </div>



            </div>
    </main>




    @include('partials.withdraw-modal')
    @include ('partials.footer-after-login')

    <!-- Modal Edit Kode Referral -->
    <div class="modal fade" id="editReferralCodeModal" tabindex="-1" aria-labelledby="editReferralCodeModalLabel" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 20px; background-color: #1a182e; color: #fff;">
                <div class="modal-header border-0 p-3" style="background: radial-gradient(circle at 10% 10%, #51376c 0%, #2e2050 100%);">
                    <h5 class="modal-title fw-bold text-white" id="editReferralCodeModalLabel">Kustomisasi Kode Referral</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('reseller.update-code') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="alert alert-warning border-0 small d-flex gap-2" style="background-color: rgba(251, 189, 35, 0.15); color: #ffca2c;">
                            <i class="bi bi-exclamation-triangle-fill fs-5 flex-shrink-0"></i>
                            <div>
                                <strong>Peringatan:</strong> Kode referral hanya dapat diganti **sekali dalam seminggu (7 hari)**. Pastikan kode baru Anda sudah profesional dan mudah diingat sebelum menyimpan.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                           <label for="newReferralCodeInput" class="form-label fw-bold small text-white-50">Kode Referral Baru</label>
                           <input type="text" class="form-control bg-dark border-secondary text-white" id="newReferralCodeInput" name="referral_code" 
                               value="{{ $user->referral_code }}" placeholder="Contoh: SPORABUDI" required 
                               style="text-transform: uppercase;">
                           <div class="form-text text-white-50" style="font-size: 11px;">
                               Gunakan huruf besar dan angka saja (minimal 3 karakter, maksimal 20 karakter, tanpa spasi/simbol).
                           </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3" style="background-color: rgba(255, 255, 255, 0.02);">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff;">Batal</button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4">Simpan Kode</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Container Toast Notifikasi Real-time -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <!-- Real-time Notif Toast -->
        <div id="resellerToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; background: linear-gradient(135deg, #FF9F1C 0%, #FF6B6B 100%) !important;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill fs-5"></i>
                    <div>
                        <strong id="toastTitle" class="d-block text-white" style="font-size: 13px;">Notifikasi Baru</strong>
                        <span id="toastMessage" style="font-size: 12px; opacity: 0.95;">Pesan notifikasi.</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>

        <!-- Copy Clipboard Toast (White) -->
        <div id="copyToast" class="toast align-items-center text-dark border-0 bg-white shadow-sm" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; border: 1px solid #e5e7eb !important;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2 py-3 px-3">
                    <div class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-circle" style="width: 28px; height: 28px;">
                        <i class="bi bi-check-lg" style="font-size: 1rem;"></i>
                    </div>
                    <div>
                        <strong id="copyToastTitle" class="d-block text-dark" style="font-size: 13px; font-weight: 600;">Berhasil Disalin</strong>
                        <span id="copyToastMessage" class="text-secondary" style="font-size: 11px;">Teks telah disalin ke clipboard.</span>
                    </div>
                </div>
                <button type="button" class="btn-close me-3 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Inisialisasi Grafik Chart.js
            const ctx = document.getElementById('performanceChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [
                            {
                                label: 'Klik Link',
                                data: {!! json_encode($clicksData) !!},
                                borderColor: '#FF9F1C',
                                backgroundColor: 'rgba(255, 159, 28, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Pendaftar Baru',
                                data: {!! json_encode($signupsData) !!},
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.3,
                                fill: true
                            },
                            {
                                label: 'Pembelian (Referral)',
                                data: {!! json_encode($purchasesData) !!},
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.3,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: {
                                    font: {
                                        family: 'Plus Jakarta Sans',
                                        weight: '600'
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            // 2. Real-time Notification Polling
            @php 
                $latestNotifId = \App\Models\UserNotification::where('user_id', Auth::id())->orderByDesc('id')->first()?->id ?? 0;
            @endphp
            let latestSeenId = {{ $latestNotifId }};
            const resellerToastEl = document.getElementById('resellerToast');
            const resellerToast = resellerToastEl ? new bootstrap.Toast(resellerToastEl, { delay: 10000 }) : null;

            function pollNotifications() {
                fetch('{{ route("notifications.index") }}')
                    .then(response => response.json())
                    .then(data => {
                        if (data.items && data.items.length > 0) {
                            let newNotifications = data.items.filter(item => item.id > latestSeenId);
                            
                            // Urutkan dari terlama ke terbaru agar toast muncul berurutan
                            newNotifications.sort((a, b) => a.id - b.id);

                            const notifList = document.getElementById('notificationList');

                            newNotifications.forEach(notif => {
                                // Tampilkan toast jika tipenya reseller
                                if (notif.type === 'reseller' && resellerToast) {
                                    document.getElementById('toastTitle').innerText = notif.title;
                                    document.getElementById('toastMessage').innerText = notif.message;
                                    resellerToast.show();
                                }

                                // Prepend ke daftar notifikasi di navbar
                                if (notifList) {
                                    // Hapus state kosong jika ada
                                    const emptyPlaceholder = notifList.querySelector('.text-muted') || (notifList.innerText.includes('Tidak ada notifikasi') ? notifList : null);
                                    if (emptyPlaceholder && emptyPlaceholder.innerText.includes('Tidak ada notifikasi')) {
                                        notifList.innerHTML = '';
                                    }

                                    const notifEl = document.createElement('div');
                                    notifEl.className = `dropdown-item p-3 border-bottom js-notif-item ${notif.read_at ? '' : 'bg-opacity-10 bg-primary'}`;
                                    notifEl.style.whiteSpace = 'normal';
                                    notifEl.style.cursor = 'pointer';
                                    notifEl.setAttribute('role', 'button');
                                    notifEl.setAttribute('tabindex', '0');
                                    notifEl.setAttribute('aria-expanded', 'false');

                                    notifEl.innerHTML = `
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div class="d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-warning me-2 flex-shrink-0" viewBox="0 0 16 16">
                                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                </svg>
                                                <h6 class="mb-1 fw-bold text-white small">${notif.title}</h6>
                                            </div>
                                            <small class="text-white-50 ms-2" style="font-size: 0.7rem; white-space: nowrap;">${notif.time_ago || 'Baru saja'}</small>
                                        </div>
                                        <p class="mb-1 small text-white-50 ms-4 notif-message-short">${notif.message.length > 80 ? notif.message.substring(0, 80) + '...' : notif.message}</p>
                                        <p class="mb-1 small text-white-50 ms-4 notif-message-full" style="display: none;">${notif.message}</p>
                                    `;
                                    
                                    notifList.insertBefore(notifEl, notifList.firstChild);
                                }
                                
                                // Update ID notifikasi terakhir yang dilihat
                                if (notif.id > latestSeenId) {
                                    latestSeenId = notif.id;
                                }
                            });

                            // Update badge jumlah notifikasi di navbar jika unread berubah
                            const desktopBadge = document.getElementById('notificationBadge');
                            const mobileBadge = document.getElementById('notificationBadgeMobile');
                            
                            if (data.unread > 0) {
                                if (desktopBadge) {
                                    desktopBadge.innerText = data.unread;
                                    desktopBadge.style.display = 'block';
                                }
                                if (mobileBadge) {
                                    mobileBadge.innerText = data.unread;
                                    mobileBadge.style.display = 'block';
                                }
                            } else {
                                if (desktopBadge) desktopBadge.style.display = 'none';
                                if (mobileBadge) mobileBadge.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => console.error('Error polling notifications:', error));
            }

            // Jalankan polling setiap 30 detik
            setInterval(pollNotifications, 30000);
        });
    </script>

    <script>
        function showCopyToast(title, message) {
            const toastEl = document.getElementById('copyToast');
            if (toastEl) {
                document.getElementById('copyToastTitle').innerText = title;
                document.getElementById('copyToastMessage').innerText = message;
                const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
                toast.show();
            }
        }

        function animateCopyIcon(button) {
            var icon = button.querySelector('i');
            if (!icon) return;

            var originalClass = icon.dataset.originalClass || icon.className;
            icon.dataset.originalClass = originalClass;
            icon.className = "bi bi-check-circle-fill";

            setTimeout(function () {
                icon.className = originalClass;
            }, 1800);
        }

        function copyToClipboard(button, elementId) {
            // 1. Ambil teks dan copy
            var copyText = document.getElementById(elementId);
            if (!copyText) return;

            copyText.select();
            copyText.setSelectionRange(0, 99999); // Untuk support mobile
            navigator.clipboard.writeText(copyText.value);

            // 2. Logika Ubah Icon (Clipboard -> Checklist -> Clipboard)
            var icon = button.querySelector('i'); // Ambil elemen ikon di dalam tombol

            // Simpan class asli (bi-clipboard)
            var originalClass = "bi bi-clipboard";
            // Class untuk checklist
            var successClass = "bi bi-check-lg";

            // Ubah ikon jadi checklist
            icon.className = successClass;

            // Kembalikan ke ikon awal setelah 2 detik (2000ms)
            setTimeout(function () {
                icon.className = originalClass;
            }, 2000);

            // Tampilkan Toast
            let label = 'Teks';
            if (elementId === 'referralCode') label = 'Kode referral';
            if (elementId === 'referralLink') label = 'Link referral';
            if (elementId === 'referralCaption') label = 'Caption broadcast';
            showCopyToast('Berhasil Disalin', `${label} berhasil disalin ke clipboard.`);
        }

        function copyTextValue(button, value) {
            if (!value) return;

            navigator.clipboard.writeText(value);
            animateCopyIcon(button);
            showCopyToast('Link Disalin', 'Link referral produk berhasil disalin ke clipboard.');
        }

        // Onboarding Tour JS Logic
        let tourSteps = [
            {
                elementId: 'tour-withdraw-box',
                title: 'Tarik Komisi & Saldo',
                description: 'Di sini Anda dapat melihat saldo dompet terkini, saldo pending, dan mengajukan penarikan dana.',
                placement: 'bottom'
            },
            {
                elementId: 'tour-tools-box',
                title: 'Alat Promosi Referral',
                description: 'Salin kode unik, link referral, atau caption broadcast secara instan untuk dibagikan ke media sosial Anda.',
                placement: 'bottom'
            },
            {
                elementId: 'tour-products-box',
                title: 'Katalog Produk Komisi',
                description: 'Cari course atau event yang aktif, salin tautan referral uniknya, lalu sebarkan untuk mendapatkan komisi 10% - 15%.',
                placement: 'top'
            },
            {
                elementId: 'tour-level-box',
                title: 'Tingkatan Level Anda',
                description: 'Tingkatkan jumlah referral sukses untuk naik level dari Bronze ke Silver atau Gold, guna mendapatkan rate komisi yang lebih besar.',
                placement: 'top'
            },
            {
                elementId: 'tour-rank-box',
                title: 'Leaderboard & Top Reseller',
                description: 'Pantau peringkat performa terbaik bulanan. Pacu promosi Anda untuk menduduki peringkat teratas secara global!',
                placement: 'top'
            },
            {
                elementId: 'tour-history-box',
                title: 'Riwayat Referral',
                description: 'Lihat daftar transaksi referral Anda di sini. Riwayat ini menampilkan nama pembeli yang menggunakan kode Anda beserta nominal komisi dan statusnya.',
                placement: 'top'
            },
            {
                elementId: 'tour-withdraw-history-box',
                title: 'Riwayat Penarikan Dana',
                description: 'Pantau status pencairan dana Anda di sini, mulai dari pengajuan pending, disetujui (approved), hingga ditolak (rejected) oleh admin.',
                placement: 'top'
            }
        ];

        let currentTourStep = 0;

        function startOnboardingTour() {
            currentTourStep = 0;
            const overlay = document.getElementById('tour-overlay');
            const popover = document.getElementById('tour-popover');
            
            if (overlay && popover) {
                overlay.style.display = 'block';
                popover.style.display = 'block';
                // Trigger reflow for transition
                popover.offsetHeight;
                popover.classList.add('show');
                showStep(currentTourStep);
            }
        }

        function showStep(stepIndex) {
            let step = tourSteps[stepIndex];
            
            // Clean previous highlight
            document.querySelectorAll('.tour-highlighted-element').forEach(el => {
                el.classList.remove('tour-highlighted-element');
            });

            let targetEl = document.getElementById(step.elementId);
            if (!targetEl) {
                // If element is not found, skip to next step
                nextTourStep();
                return;
            }

            // Highlight target element
            targetEl.classList.add('tour-highlighted-element');
            targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Set content
            document.getElementById('tour-title').innerText = step.title;
            document.getElementById('tour-description').innerText = step.description;
            document.getElementById('tour-progress').innerText = `${stepIndex + 1} dari ${tourSteps.length}`;

            // Handle Buttons visibility/label
            document.getElementById('tour-btn-prev').style.display = stepIndex === 0 ? 'none' : 'inline-block';
            document.getElementById('tour-btn-next').innerText = stepIndex === tourSteps.length - 1 ? 'Selesai' : 'Lanjut';

            // Wait a tiny bit for scrolling to complete, then calculate position
            setTimeout(() => {
                positionPopover(targetEl, step.placement);
            }, 150);
        }

        function positionPopover(targetEl, placement) {
            let popover = document.getElementById('tour-popover');
            if (!popover) return;
            
            let rect = targetEl.getBoundingClientRect();
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            let scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;

            let popoverWidth = popover.offsetWidth;
            let popoverHeight = popover.offsetHeight;

            let top = 0;
            let left = 0;

            // Simple placement calculation
            if (placement === 'bottom') {
                top = rect.bottom + scrollTop + 12;
                left = rect.left + scrollLeft + (rect.width / 2) - (popoverWidth / 2);
            } else if (placement === 'top') {
                top = rect.top + scrollTop - popoverHeight - 12;
                left = rect.left + scrollLeft + (rect.width / 2) - (popoverWidth / 2);
            } else if (placement === 'left') {
                top = rect.top + scrollTop + (rect.height / 2) - (popoverHeight / 2);
                left = rect.left + scrollLeft - popoverWidth - 12;
            } else if (placement === 'right') {
                top = rect.top + scrollTop + (rect.height / 2) - (popoverHeight / 2);
                left = rect.right + scrollLeft + 12;
            }

            // Adjust bounding to screen edges
            if (left < 10) left = 10;
            if (left + popoverWidth > window.innerWidth - 10) {
                left = window.innerWidth - popoverWidth - 10;
            }
            if (top < 10) top = 10;

            popover.style.top = `${top}px`;
            popover.style.left = `${left}px`;
            popover.setAttribute('data-popper-placement', placement);
        }

        function nextTourStep() {
            currentTourStep++;
            if (currentTourStep >= tourSteps.length) {
                finishTour();
            } else {
                showStep(currentTourStep);
            }
        }

        function prevTourStep() {
            currentTourStep--;
            if (currentTourStep < 0) {
                currentTourStep = 0;
            }
            showStep(currentTourStep);
        }

        // Allow close overlay when click outside
        function skipTour() {
            finishTour();
        }

        function finishTour() {
            const overlay = document.getElementById('tour-overlay');
            const popover = document.getElementById('tour-popover');
            
            if (overlay) overlay.style.display = 'none';
            if (popover) {
                popover.style.display = 'none';
                popover.classList.remove('show');
            }
            document.querySelectorAll('.tour-highlighted-element').forEach(el => {
                el.classList.remove('tour-highlighted-element');
            });
            localStorage.setItem('idspora_reseller_tour_done', 'true');
        }

        // Handle window resizing
        window.addEventListener('resize', () => {
            let popover = document.getElementById('tour-popover');
            if (popover && popover.style.display !== 'none') {
                let step = tourSteps[currentTourStep];
                let targetEl = document.getElementById(step.elementId);
                if (targetEl) {
                    positionPopover(targetEl, step.placement);
                }
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            // Auto run tour on first visit
            if (!localStorage.getItem('idspora_reseller_tour_done')) {
                setTimeout(() => {
                    startOnboardingTour();
                }, 1000);
            }
        });
    </script>

    <!-- Onboarding Tour Elements -->
    <div id="tour-overlay" class="tour-overlay" style="display: none;" onclick="skipTour()"></div>
    <div id="tour-popover" class="tour-popover" style="display: none;" role="dialog">
        <div class="tour-popover-arrow"></div>
        <div class="tour-popover-content">
            <h6 id="tour-title" class="tour-popover-title">Judul Panduan</h6>
            <p id="tour-description" class="tour-popover-desc">Penjelasan langkah panduan...</p>
        </div>
        <div class="tour-popover-footer d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <span id="tour-progress" class="tour-popover-progress text-muted small fw-medium">Langkah 1 dari 5</span>
            <div class="d-flex gap-2">
                <button type="button" id="tour-btn-skip" class="btn btn-sm btn-link text-decoration-none text-muted fw-semibold px-2 py-1" onclick="skipTour()" style="font-size: 0.85rem;">Lewati</button>
                <button type="button" id="tour-btn-prev" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 fw-semibold" onclick="prevTourStep()" style="display: none; font-size: 0.85rem;">Kembali</button>
                <button type="button" id="tour-btn-next" class="btn btn-sm btn-warning rounded-pill px-3 py-1 fw-bold text-dark shadow-sm" onclick="nextTourStep()" style="font-size: 0.85rem;">Lanjut</button>
            </div>
        </div>
    </div>
</body>

</html>