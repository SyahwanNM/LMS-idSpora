@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - {{ isset($event)? $event->title : 'Event' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #F8FAFC;
            font-family: 'Poppins', sans-serif;
            color: #111827;
        }

        .payment-container {
            max-width: 1100px;
            /* Jarak dari atas dikurangi agar 'mentok' ke navbar (asumsi tinggi navbar ~60-70px) */
            margin: 85px auto 40px;
            padding: 0 20px;
        }

        /* --- COMPACT CARD STYLES --- */
        .card-custom {
            background: #fff;
            border: 1px solid #FCD34D;
            border-radius: 10px;
            /* Radius sedikit diperkecil */
            padding: 16px;
            /* Padding card diperkecil (sebelumnya 24px) */
            margin-bottom: 16px;
            /* Jarak antar card diperkecil */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        /* Grid Layout */
        .grid-layout {
            display: grid;
            grid-template-columns: 1.4fr 0.8fr;
            gap: 16px;
            /* Gap antar kolom diperkecil */
            align-items: start;
        }

        /* Inputs yang lebih ramping */
        .form-label-custom {
            font-weight: 600;
            font-size: 13px;
            /* Font label diperkecil */
            margin-bottom: 4px;
            color: #111827;
        }

        .form-control-custom,
        .form-select-custom {
            display: block;
            width: 100%;
            padding: 8px 10px;
            /* Padding input diperkecil */
            font-size: 13px;
            color: #111827;
            background-color: #fff;
            border: 1px solid #FCD34D;
            border-radius: 6px;
            transition: 0.15s;
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            border-color: #EAB308;
            outline: 0;
            box-shadow: 0 0 0 3px rgba(253, 224, 71, 0.25);
        }

        .form-control-custom[readonly] {
            background-color: #FAFAFA;
            color: #6B7280;
        }

        /* Jarak antar form group diperkecil */
        .mb-custom {
            margin-bottom: 12px;
        }

        /* Warning Text */
        .warning-text {
            color: #EF4444;
            font-size: 11px;
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;
        }

        /* Whatsapp & Voucher */
        .wa-group {
            display: flex;
            gap: 8px;
        }

        .wa-group select {
            width: 100px;
        }

        .voucher-header {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .voucher-input-group {
            display: flex;
            gap: 8px;
        }

        .btn-check-voucher {
            background-color: #FACC15;
            color: #854D0E;
            border: none;
            padding: 0 14px;
            font-weight: 600;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
        }

        /* Order Detail Items */
        .order-detail-content {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .order-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
        }

        /* Gambar lebih kecil */
        .order-info h5 {
            font-size: 13px;
            font-weight: 600;
            margin: 0 0 2px;
            line-height: 1.2;
        }

        .order-info p {
            font-size: 11px;
            color: #6B7280;
            margin: 0 0 4px;
        }

        .price-text {
            color: #16A34A;
            font-weight: 600;
            font-size: 13px;
        }

        /* --- TOTAL BIAYA SECTION (Compact) --- */
        .total-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .bag-icon {
            border: 1px solid #111827;
            border-radius: 6px;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bag-icon svg {
            width: 16px;
            height: 16px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            color: #6B7280;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #E5E7EB;
        }

        .btn-pay {
            width: 100%;
            background-color: #FACC15;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            padding: 10px;
            /* Padding tombol diperkecil */
            border: none;
            border-radius: 8px;
            margin-top: 14px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-pay:hover {
            background-color: #EAB308;
        }

        .btn-pay:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="payment-container" style="margin-bottom: 12px; margin-top: 85px;">
        <div style="font-size: 12px; color: #6B7280;">
            <a href="{{ route('dashboard') }}" style="text-decoration:none; color:inherit;">Home</a> /
            <a href="{{ route('events.index') }}" style="text-decoration:none; color:inherit;">Event</a> /
            <span style="color:#111827; font-weight:600;">Payment</span>
        </div>
    </div>

    <form id="paymentForm" method="POST" action="{{ route('payment.manual.register', $event->id ?? '') }}"
        enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">

        @php
        $isFree = isset($event) ? ((int)($event->price ?? 0) === 0) : false;
        $hasDiscount = isset($event) ? $event->hasDiscount() : false;
        $finalPrice = isset($event) ? ($hasDiscount ? ($event->discounted_price ?? 0) : ($event->price ?? 0)) : 0;
        @endphp

        <div class="payment-container" style="margin-top: 0;">
            <div class="grid-layout">

                <div class="left-col">
                    <div class="card-custom">
                        <h3>Data Peserta</h3>

                        <div class="mb-custom">
                            <label class="form-label-custom">Email</label>
                            <input type="email" class="form-control-custom" name="email"
                                value="{{ auth()->user()->email ?? '' }}" readonly>
                        </div>

                        <div class="mb-custom">
                            <label class="form-label-custom" style="margin-bottom:0">Nama Lengkap</label>
                            <input type="text" class="form-control-custom" name="full_name"
                                value="{{ auth()->user()->name ?? '' }}" placeholder="Nama sesuai sertifikat" required
                                minlength="3">
                            <div class="warning-text" style="margin-top:4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor"
                                    class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                </svg>
                                Nama Akan digunakan pada sertifikat
                            </div>
                        </div>

                        <div class="mb-custom" style="margin-bottom:0;"> <label class="form-label-custom">No
                                Whatsapp</label>
                            <div class="wa-group">
                                <select class="form-select-custom" name="dial_code" required>
                                    <option value="">Kode</option>
                                    <option value="+62" selected>+62</option>
                                    <option value="+60">+60</option>
                                    <option value="+65">+65</option>
                                </select>
                                <input type="text" class="form-control-custom" name="whatsapp" placeholder="No Whatsapp"
                                    inputmode="numeric" required>
                            </div>
                        </div>

                    </div>
                    <div class="card-custom">
                        <h3>Voucher & Referral</h3>

                        <div class="voucher-header">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-patch-check" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M10.354 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                <path
                                    d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z" />
                            </svg>
                            Kode Voucher
                        </div>
                        <div class="voucher-input-group">
                            <input type="text" class="form-control-custom" placeholder="Input Kode Voucher"
                                name="voucher_code">
                            <button type="button" class="btn-check-voucher">Cek</button>
                        </div>
                        <div class="referral-event" style="margin-bottom:25px;margin-top:10px">
                            <label class="form-label-custom">Kode Referral</label>
                            <div class="voucher-input-group">
                                <input type="text" id="referralCodeInput" class="form-control-custom"
                                    name="referral_code" placeholder="Kode Referral (Opsional)"
                                    value="{{ request()->cookie('referral_code') ?? old('referral_code') }}">
                                <button type="button" id="btnCheckReferral" class="btn-check-voucher">Cek Kode</button>
                            </div>
                            <small id="referralMessage" style="display:block; margin-top:5px; font-size:11px;"></small>
                        </div>
                    </div>

                </div>

                <div class="right-col">
                    <div class="card-custom">
                        <h3>Checkout Detail Event</h3>
                        <div class="order-detail-content">
                            @if(isset($event))
                            <img src="{{ $event->image_url ?? asset('aset/event.png') }}" alt="{{ $event->title }}"
                                class="order-img" onerror="this.src='{{ asset('aset/event.png') }}'">
                            @else
                            <img src="{{ asset('aset/event.png') }}" alt="Event" class="order-img">
                            @endif
                            <div class="order-info">
                                <h5>{{ isset($event)? $event->title : 'Event Title' }}</h5>
                                <p>IdSpora</p>
                                <div class="price-text" id="totalPriceDisplay" data-original-price="{{ $finalPrice }}">
                                    @if($isFree) FREE @else Rp{{ number_format($finalPrice,0,',','.') }} @endif
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <img src="{{ asset('aset/qr-payment-idSpora.png') }}" alt="QRIS"
                                style="max-width:220px;cursor:pointer;" id="qrisImage">
                            <div class="small text-muted mt-2">Scan QRIS di atas untuk membayar secara manual</div>
                        </div>
                    </div>


                    <div class="upload-bukti">
                        <div class="mt-3">
                            <h3>Upload Bukti Pembayaran </h3>
                            <small>Dengan (JPEG/PNG, max 5MB)</small>
                            <input type="file" name="payment_proof" accept="image/*" class="form-control-custom">

                        </div>

                        <button type="submit" class="btn-pay">
                            @if(isset($event) && $isFree) Daftar Gratis @else Kirim Bukti Pembayaran @endif
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('paymentForm');
            if (!form) return;
            const fullName = form.querySelector('input[name="full_name"]');
            const dial = form.querySelector('select[name="dial_code"]');
            const wa = form.querySelector('input[name="whatsapp"]');
            const proof = form.querySelector('input[name="payment_proof"]');
            const btn = form.querySelector('.btn-pay');
            const isFree = @json(isset($event) ? ((int)($finalPrice ?? 0) === 0) : false);

            function isValidPhone(val) { return /^[0-9]{6,15}$/.test(String(val || '').trim()); }

            function validate() {
                if (isFree) { btn.disabled = false; btn.style.opacity = '1'; return true; }
                const nameOk = fullName && fullName.value.trim().length >= 3;
                const dialOk = dial && dial.value.trim() !== '';
                const waOk = wa && isValidPhone(wa.value);
                const proofOk = proof && proof.files.length > 0;
                const ok = nameOk && dialOk && waOk && proofOk;
                if (btn) { btn.disabled = !ok; btn.style.opacity = ok ? '1' : '0.5'; }
                return ok;
            }

            ['input', 'change', 'keyup', 'blur'].forEach(evt => {
                if (fullName) fullName.addEventListener(evt, validate);
                if (dial) dial.addEventListener(evt, validate);
                if (wa) wa.addEventListener(evt, validate);
                if (proof) proof.addEventListener(evt, validate);
            });

            form.addEventListener('submit', function (e) {
                if (!validate()) {
                    e.preventDefault();
                    alert('Lengkapi data peserta sebelum mengirim bukti pembayaran.');
                }
                // otherwise allow normal form submission (multipart upload handled by server)
            });

            // initial
            validate();

            // javasrpict buat cek kode referral secara realtime tanpa refresh page
            const btnCheck = document.getElementById('btnCheckReferral');
            const inputCode = document.getElementById('referralCodeInput');
            const messageBox = document.getElementById('referralMessage');
            const priceDisplay = document.getElementById('totalPriceDisplay');

            if (priceDisplay && btnCheck) {
                // Ambil harga asli dari atribut HTML
                const originalPrice = parseInt(priceDisplay.getAttribute('data-original-price')) || 0;

                btnCheck.addEventListener('click', function () {
                    const code = inputCode.value.trim();
                    if (code === '') {
                        messageBox.innerHTML = '<span style="color:red;">Masukkan kode dulu.</span>';
                        return;
                    }

                    btnCheck.textContent = 'Mengecek...';

                    // Panggil route ajax check.referral
                    fetch('{{ route('check.referral') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ code: code })
                    })
                        .then(response => response.json())
                        .then(data => {
                            btnCheck.textContent = 'Cek Kode';
                            if (data.valid) {
                                messageBox.innerHTML = `<span style="color:green;">${data.message}</span>`;

                                // Hitung diskon persentase (misal 10%)
                                let discountAmount = originalPrice * (data.discount_percentage / 100);
                                let newPrice = originalPrice - discountAmount;

                                if (newPrice < 0) newPrice = 0;

                                // Format angka ke format Rupiah (titik)
                                priceDisplay.innerHTML = 'Rp' + newPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            } else {
                                messageBox.innerHTML = `<span style="color:red;">${data.message}</span>`;
                                // Kembalikan ke harga asli kalau gagal
                                priceDisplay.innerHTML = 'Rp' + originalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        })
                        .catch(error => {
                            btnCheck.textContent = 'Cek Kode';
                            console.error('Error:', error);
                        });
                });
            }
        });
    </script>
</body>

</html>