<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     @vite(['resources/css/app.css', 'resources/js/app.js'])
      @php
          $midtransClientKey = (string) config('midtrans.client_key');
          $midtransIsProduction = (bool) config('midtrans.is_production', false);
      @endphp
      @if($midtransClientKey)
          <script src="https://app{{ $midtransIsProduction ? '' : '.sandbox' }}.midtrans.com/snap/snap.js" data-client-key="{{ $midtransClientKey }}"></script>
      @endif
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc; /* Background halaman */
            color: #333;
        }

        /* Container Utama Putih */
        .box_luar_payment {
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 40px;
            margin: 40px auto;
            max-width: 1100px;
        }

        /* Breadcrumb / Navigasi Atas */
        .link_back_payment_course {
            display: flex;
            gap: 10px;
            color: #888;
            font-size: 14px;
            margin-bottom: 30px;
        }
        .link_back_payment_course a {
            text-decoration: none;
            color: #888;
            transition: 0.3s;
        }
        .link_back_payment_course a:hover {
            color: #333;
        }

        /* Layout Kiri (Form) dan Kanan (Card) */
        .biodata_payment_course {
            display: flex;
            gap: 50px;
            flex-wrap: wrap; /* Agar responsif di HP */
        }

        /* --- BAGIAN KIRI (FORM) --- */
        .box_kiri_biodata {
            flex: 1; /* Mengambil sisa ruang */
            min-width: 300px;
        }

        .box_kiri_biodata h5 {
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .input_biodata {
            margin-bottom: 20px;
        }

        .input_biodata p {
            margin-bottom: 8px;
            font-weight: 500;
        }

        /* Input Styles */
        .kolom_input_biodata {
            width: 100%;
            border: 1px solid #FFC107; /* Border Kuning */
            border-radius: 8px;
            padding: 12px 15px;
            outline: none;
            transition: 0.3s;
        }
        .kolom_input_biodata:focus {
            box-shadow: 0 0 5px rgba(255, 193, 7, 0.5);
        }

        /* Warning Text (Merah) */
        .info_biodata {
            display: flex;
            align-items: center;
            gap: 8px;
            color: red;
            font-size: 13px;
            font-style: italic;
            margin-bottom: 8px;
            margin-top: -5px;
        }
        .info_biodata svg {
            fill: red;
        }

        /* Whatsapp Input Group */
        .whatsapp_biodata {
            display: flex;
            gap: 10px;
        }
        .btn_nomor {
            background-color: #fff;
            color: #333;
            border: 1px solid #FFC107;
            border-radius: 8px;
            padding: 10px 15px;
        }
        .input_nomor {
            flex: 1;
            border: 1px solid #FFC107;
            border-radius: 8px;
            padding: 10px 15px;
            outline: none;
        }

        /* Radio Buttons */
        .radio_input_box {
            display: flex;
            gap: 20px;
        }
        .radio_input {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .radio_input input[type="radio"] {
            accent-color: #FFC107; /* Warna radio button saat aktif */
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .radio_input p {
            margin: 0;
            font-weight: 400;
            color: #666;
        }

        /* --- BAGIAN KANAN (ORDER DETAIL / TICKET) --- */
        .box_kanan_biodata {
            width: 350px; /* Lebar fix untuk sisi kanan */
        }

        .box_biodata {
            background-color: #e6f0fa; /* Warna biru muda */
            border-radius: 16px;
            padding: 25px;
            position: relative;
            /* Efek tiket */
        }
        
        .box_biodata h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }

        /* Event Card Mini */
        .box_event_payment {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .box_event_payment img {
            width: 100px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .judul_event h4 {
            font-size: 14px;
            font-weight: 700;
            margin: 0;
            line-height: 1.4;
        }
        .penyelenggara {
            font-size: 12px;
            color: #666;
            margin: 2px 0 5px 0;
        }
        .harga_judul_event {
            font-size: 14px;
            font-weight: 600;
            color: #2ecc71; /* Warna hijau harga */
            margin: 0;
        }

        /* Garis Putus-putus Tiket */
        .ticket-divider {
            border-bottom: 2px dashed #bacddf;
            margin: 20px -25px; /* Melebar keluar padding */
            position: relative;
        }
        /* Efek bulatan sobekan tiket kiri kanan */
        .ticket-divider::before, .ticket-divider::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: #fff; /* Sama dengan bg container utama */
            border-radius: 50%;
            top: -11px;
        }
        .ticket-divider::before { left: -10px; }
        .ticket-divider::after { right: -10px; }

        /* Total Price Section */
        .harga_teks_payment {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .teks_payment p {
            font-size: 12px;
            color: #888;
            margin: 0;
        }
        .teks_payment h4 {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        .icon-invoice {
            color: #6c757d;
        }

        /* Button Bayar */
        .btn_bayar_payment {
            width: 100%;
            background-color: #f4c430; /* Warna tombol kuning */
            color: white;
            font-weight: 700;
            font-size: 18px;
            border: none;
            border-radius: 8px;
            padding: 12px;
            margin-top: 20px;
            transition: 0.3s;
        }
        .btn_bayar_payment:hover {
            background-color: #e0b120;
        }
        .btn_cek_promo {
            padding: 12px 20px;
            background-color: #f4c430; /* Matches course payment button theme */
            color: white;
            font-weight: 700;
            font-size: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            white-space: nowrap;
            height: 45px;
            margin-bottom: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn_cek_promo:hover {
            background-color: #e0b120;
        }

        /* Disabled state: keep neutral + prevent hover effect */
        .btn_bayar_payment:disabled,
        .btn_bayar_payment[disabled] {
            background-color: #cbd5e1;
            color: #ffffff;
            cursor: not-allowed;
            opacity: 0.85;
        }
        .btn_bayar_payment:disabled:hover,
        .btn_bayar_payment[disabled]:hover {
            background-color: #cbd5e1;
        }

        /* QRIS modal: keep it compact and responsive */
        .qris-modal .modal-dialog { max-width: 520px; margin: .75rem auto; }
        .qris-modal .modal-content { border-radius: 16px; overflow: hidden; }
        .qris-modal .modal-header { padding: .9rem 1rem; }
        .qris-modal .modal-body { padding: 1rem; }
        .qris-modal .qris-image {
            max-width: 100%;
            height: auto;
            max-height: 42dvh;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }
        .qris-modal .modal-body p { margin-bottom: .65rem; }

    </style>
</head>

<body>
     @include("partials.navbar-after-login")
    <div class="box_luar_payment">
        <div class="link_back_payment_course">
            <a href="{{ route('dashboard') }}">Home</a> <p>/</p>
            <a href="/courses">Course</a> <p>/</p>
            <a href="{{ route('course.detail', $course->id) }}">{{ $course->name ?? '-' }}</a> <p>/</p>
            <span style="color: #333; font-weight: 500;">Payment</span>
        </div>

        <div class="biodata_payment_course">
            
            <div class="box_kiri_biodata">
                <h5>Participant Data</h5>
                

                <div class="input_biodata">
                    <p>Email</p>
                    <input class="kolom_input_biodata" type="email" placeholder="Masukkan email anda" value="{{ Auth::user()->email ?? '' }}" readonly>
                </div>

                <div class="input_biodata">
                    <p>Full Name</p>
                    <div class="info_biodata">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                        </svg>
                        <span>Name Will be used on certificate</span>
                    </div>
                    <input class="kolom_input_biodata" type="text" id="fullNameInput" name="display_name" value="{{ Auth::user()->name ?? '' }}">
                </div>

                <div class="input_biodata">
                    <p>No Whatsapp</p>
                    <div class="whatsapp_biodata">
                        <input type="hidden" name="kode_dial" id="kodeDialInput" value="">
                        <input class="input_nomor" type="text" placeholder="Example: 6281234567890" id="whatsappNumberInput" inputmode="tel" autocomplete="tel" style="width:100%;">
                    </div>
                </div>
                <div class="input_biodata">
                    <p>Kode Promo / Referral</p>
                    <div style="display: flex; gap: 8px; align-items: center; width: 100%;">
                        <input
                            class="kolom_input_biodata"
                            type="text"
                            id="promoCodeInput"
                            placeholder="Masukkan Kode Voucher atau Referral jika ada"
                            value="{{ request()->query('ref', request()->cookie('referral_code', '')) }}"
                            autocomplete="off"
                            style="flex: 1; margin-bottom: 0;"
                        >
                        <button type="button" id="checkPromoBtn" class="btn_cek_promo">Cek Kode</button>
                    </div>
                    <div id="promoMessage" style="display:none; margin-top:8px; font-size:13px; line-height:1.5;"></div>
                    <div style="margin-top:6px; font-size:12px; color:#6b7280;">
                        Masukkan Kode Voucher (dari Poin) atau Kode Referral Reseller untuk mendapatkan potongan harga.
                    </div>
                </div>

                <!-- Hidden inputs for validation state and submission -->
                <input type="hidden" id="referralCodeInput" value="{{ request()->query('ref', request()->cookie('referral_code', '')) }}">
                <input type="hidden" id="voucherCodeInput" value="">



            </div>

            <div class="box_kanan_biodata">
                <div class="box_biodata">
                    <div style="width: 40px; height: 5px; background:#ff7f50; border-radius:5px; margin-bottom:15px; position:absolute; top:-2px; left:50%; transform:translateX(-50%);"></div>

                    <h3>Order Detail Course</h3>
                    
                    <div class="box_event_payment">
                        @php
                            $cMedia = $course->card_thumbnail ?? $course->media;
                            if ($cMedia) {
                                $imgSrc = str_starts_with($cMedia, 'http') ? $cMedia : asset('uploads/' . $cMedia);
                            } else {
                                $imgSrc = 'https://img.freepik.com/vektor-premium/live-concert-horizontal-banner-template_23-2150997973.jpg';
                            }
                        @endphp
                        <img src="{{ $imgSrc }}" alt="Course Card Image">
                        <div class="judul_event">
                            <h4>{{ $course->name ?? '-' }}</h4>
                            <p class="penyelenggara">{{ $course->category->name ?? '-' }}</p>
                            @php $isFreeCourseLocal = (int) ($course->price ?? 0) <= 0; @endphp
                            <p class="harga_judul_event">
                                @if($isFreeCourseLocal)
                                    Free Enroll
                                @elseif($course->hasDiscount())
                                    <span style="text-decoration: line-through; color: #888; font-size: 12px; margin-right: 5px;">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                                    Rp{{ number_format($course->discounted_price, 0, ',', '.') }}
                                @else
                                    Rp{{ number_format($course->price ?? 0, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="ticket-divider"></div>

                    <!-- Detail rincian potongan harga -->
                    <div style="margin: 15px 0; padding-bottom: 10px; border-bottom: 1px dashed #e5e7eb; font-family: sans-serif;">
                        <div style="display: flex; justify-content: space-between; font-size: 13px; color: #4b5563; margin-bottom: 8px;">
                            <span>Harga Asli</span>
                            <span id="originalPriceText">Rp {{ number_format($course->hasDiscount() ? $course->discounted_price : ($course->price ?? 0), 0, ',', '.') }}</span>
                        </div>
                        <div id="discountRow" style="display: none; justify-content: space-between; font-size: 13px; color: #16a34a; margin-bottom: 8px; font-weight: 500;">
                            <span id="discountLabel">Diskon Promo</span>
                            <span id="discountValueText">-Rp 0</span>
                        </div>
                    </div>

                    <div class="harga_teks_payment">
                        <div class="teks_payment">
                            <p>Total</p>
                            <h4 id="totalAmountText" data-base-amount="{{ (int) round($course->hasDiscount() ? $course->discounted_price : ($course->price ?? 0)) }}">
                                @if($isFreeCourseLocal)
                                    Free Enroll!
                                @elseif($course->hasDiscount())
                                    <span style="text-decoration: line-through; color: #888; font-size: 14px; margin-right: 8px; font-weight: 400;">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                                    Rp {{ number_format($course->discounted_price, 0, ',', '.') }}
                                @else
                                    Rp {{ number_format($course->price ?? 0, 0, ',', '.') }}
                                @endif
                            </h4>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-file-text icon-invoice" viewBox="0 0 16 16">
                            <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
                        </svg>
                    </div>
                </div>

                <!-- Manual payment via QRIS: show QR image modal when clicking Bayar -->
                @php
                    $isFreeCourse = (int) ($course->price ?? 0) <= 0;
                @endphp
                <form id="manualPaymentForm" method="POST" action="{{ $isFreeCourse ? route('courses.free-enroll', $course) : '#' }}" data-is-free="{{ $isFreeCourse ? '1' : '0' }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ Auth::user()->email ?? '' }}">
                    <input type="hidden" name="name" id="hiddenNameInput" value="{{ Auth::user()->name ?? '' }}">
                    <input type="hidden" name="kode_dial" id="formKodeDialInput" value="+62">
                    <input type="hidden" name="whatsapp" id="formWhatsappInput">
                    <input type="hidden" name="referral_code" id="formReferralCodeInput" value="{{ request()->query('ref', request()->cookie('referral_code', '')) }}">

                    @if($isFreeCourse)
                        <button type="submit" id="freeEnrollBtn" class="btn_bayar_payment" disabled>Study Now!</button>
                    @else
                        <button type="button" id="midtransPayBtnCourse" class="btn_bayar_payment" disabled>Pay Now</button>
                    @endif
                </form>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- QRIS Modal removed -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sync editable full name to hidden input
            var fullNameInput = document.getElementById('fullNameInput');
            var hiddenNameInput = document.getElementById('hiddenNameInput');
            if (fullNameInput && hiddenNameInput) {
                fullNameInput.addEventListener('input', function() {
                    hiddenNameInput.value = this.value;
                    updatePayButtonState();
                });
            }

            var kodeDialInput = document.getElementById('kodeDialInput');
            var formKodeDialInput = document.getElementById('formKodeDialInput');
            var whatsappInput = document.getElementById('whatsappNumberInput') || document.querySelector('.input_nomor');
            var formWhatsappInput = document.getElementById('formWhatsappInput');
            var totalAmountText = document.getElementById('totalAmountText');
            var formWhatsappFullInput = document.getElementById('formWhatsappFullInput');
            var freeEnrollBtn = document.getElementById('freeEnrollBtn');
            var midtransPayBtn = document.getElementById('midtransPayBtnCourse');
            var manualPaymentForm = document.getElementById('manualPaymentForm');
            var promoInput = document.getElementById('promoCodeInput');
            var promoMessageEl = document.getElementById('promoMessage');
            var referralInput = document.getElementById('referralCodeInput'); // hidden
            var voucherInput = document.getElementById('voucherCodeInput'); // hidden
            var formReferralCodeInput = document.getElementById('formReferralCodeInput');
            
            var checkCodeUrl = @json((bool) ($course->is_reseller_course ?? false) ? route('courses.check-code', $course) : '');
            var currentUserReferral = @json((string) (Auth::user()->referral_code ?? ''));
            
            var referralState = 'idle';
            var voucherState = 'idle';
            var currentDiscount = 0;
            var promoTimer = null;

            var isFreeCourse = false;
            if (manualPaymentForm) {
                isFreeCourse = (manualPaymentForm.getAttribute('data-is-free') || '0') === '1';
            }

            var referralFinalAmount = getBaseAmount();

            function formatIdrNumber(amount) {
                var n = Math.max(0, parseInt(amount || 0, 10));
                return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function getBaseAmount() {
                if (!totalAmountText) return 0;
                return Math.max(0, parseInt(totalAmountText.getAttribute('data-base-amount') || '0', 10));
            }

            function getReferralCode() {
                return referralInput ? String(referralInput.value || '').trim() : '';
            }

            function getVoucherCode() {
                return voucherInput ? String(voucherInput.value || '').trim() : '';
            }

            function syncReferralInput() {
                if (formReferralCodeInput) {
                    formReferralCodeInput.value = getReferralCode();
                }
            }

            function setPromoMessage(message, kind) {
                if (!promoMessageEl) return;
                if (!message) {
                    promoMessageEl.style.display = 'none';
                    promoMessageEl.textContent = '';
                    promoMessageEl.style.color = '';
                    return;
                }
                promoMessageEl.style.display = '';
                promoMessageEl.textContent = message;
                promoMessageEl.style.color = kind === 'success' ? '#15803d' : (kind === 'info' ? '#6b7280' : '#dc2626');
            }

            function setTotalAmount(amount) {
                if (!totalAmountText) return;
                var n = Math.max(0, parseInt(amount || 0, 10));
                if (isFreeCourse || n === 0) {
                    totalAmountText.textContent = 'Free Enroll';
                    return;
                }
                totalAmountText.textContent = 'Rp ' + formatIdrNumber(n);
            }

            function updateTotalDisplay() {
                var baseAmount = getBaseAmount();
                var finalAmount = referralFinalAmount;
                if (voucherState === 'valid' && currentDiscount > 0) {
                    finalAmount = Math.max(0, referralFinalAmount - currentDiscount);
                }
                setTotalAmount(finalAmount);

                // Update discount row
                var discountRow = document.getElementById('discountRow');
                var discountLabel = document.getElementById('discountLabel');
                var discountValueText = document.getElementById('discountValueText');
                
                var discountAmount = baseAmount - finalAmount;
                if (discountAmount > 0) {
                    if (discountRow) discountRow.style.display = 'flex';
                    if (discountLabel) {
                        if (referralState === 'valid') {
                            discountLabel.textContent = 'Diskon Referral (10%)';
                        } else if (voucherState === 'valid') {
                            discountLabel.textContent = 'Diskon Voucher';
                        } else {
                            discountLabel.textContent = 'Potongan Harga';
                        }
                    }
                    if (discountValueText) discountValueText.textContent = '-Rp ' + formatIdrNumber(discountAmount);
                } else {
                    if (discountRow) discountRow.style.display = 'none';
                }

                if (midtransPayBtn) {
                    if (finalAmount === 0) {
                        midtransPayBtn.textContent = 'Study Now (Free)';
                    } else {
                        var pending = cachedPending;
                        if (pending && pending.pending) {
                            midtransPayBtn.textContent = 'Continue Payment';
                        } else {
                            midtransPayBtn.textContent = 'Pay Now';
                        }
                    }
                }
            }

            function normalizePhone(value) {
                return (value || '').replace(/[^0-9]/g, '');
            }

            async function validatePromoCodeServer(code) {
                if (!checkCodeUrl || !code) return null;
                try {
                    var url = new URL(checkCodeUrl, window.location.origin);
                    url.searchParams.set('code', code);
                    var res = await fetch(url.toString(), {
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) return null;
                    return await res.json();
                } catch (_e) {
                    return null;
                }
            }

            function updatePayButtonState() {
                var wa = normalizePhone(whatsappInput ? whatsappInput.value : '');
                var fullName = (fullNameInput ? fullNameInput.value : '').trim();
                var disable = wa.length < 8 || fullName.length === 0;
                var code = promoInput ? promoInput.value.trim() : '';
                if (code !== '') {
                    if (referralState !== 'valid' && voucherState !== 'valid') {
                        disable = true;
                    }
                }
                if (freeEnrollBtn) freeEnrollBtn.disabled = disable;
                if (midtransPayBtn) midtransPayBtn.disabled = disable;
            }

            function handlePromoCodeUI(data, code) {
                var baseAmount = getBaseAmount();
                
                if (code === '') {
                    referralState = 'idle';
                    voucherState = 'idle';
                    currentDiscount = 0;
                    referralFinalAmount = baseAmount;
                    if (referralInput) referralInput.value = '';
                    if (voucherInput) voucherInput.value = '';
                    syncReferralInput();
                    setPromoMessage('', 'info');
                    updateTotalDisplay();
                    updatePayButtonState();
                    return;
                }

                if (!data) {
                    referralState = 'invalid';
                    voucherState = 'invalid';
                    currentDiscount = 0;
                    referralFinalAmount = baseAmount;
                    if (referralInput) referralInput.value = '';
                    if (voucherInput) voucherInput.value = '';
                    syncReferralInput();
                    setPromoMessage('Gagal memvalidasi kode. Coba lagi.', 'error');
                    updateTotalDisplay();
                    updatePayButtonState();
                    return;
                }

                if (data.valid) {
                    if (data.type === 'referral') {
                        referralState = 'valid';
                        voucherState = 'idle';
                        currentDiscount = 0;
                        referralFinalAmount = data.final_amount || baseAmount;
                        if (referralInput) referralInput.value = code;
                        if (voucherInput) voucherInput.value = '';
                    } else if (data.type === 'voucher') {
                        referralState = 'idle';
                        voucherState = 'valid';
                        currentDiscount = data.discount || 0;
                        referralFinalAmount = baseAmount;
                        if (referralInput) referralInput.value = '';
                        if (voucherInput) voucherInput.value = code;
                    }
                    syncReferralInput();
                    setPromoMessage(data.message, 'success');
                } else {
                    referralState = 'invalid';
                    voucherState = 'invalid';
                    currentDiscount = 0;
                    referralFinalAmount = baseAmount;
                    if (referralInput) referralInput.value = '';
                    if (voucherInput) voucherInput.value = '';
                    syncReferralInput();
                    setPromoMessage(data.message || 'Kode tidak valid.', 'error');
                }

                updateTotalDisplay();
                updatePayButtonState();
            }

            function schedulePromoValidation() {
                if (promoTimer) {
                    clearTimeout(promoTimer);
                }

                var code = promoInput ? promoInput.value.trim() : '';

                if (code === '') {
                    handlePromoCodeUI({ valid: false, message: '' }, '');
                    return;
                }

                referralState = 'checking';
                voucherState = 'checking';
                setPromoMessage('Memeriksa kode...', 'info');
                updatePayButtonState();

                promoTimer = setTimeout(async function() {
                    var data = await validatePromoCodeServer(code);
                    if (promoInput && code !== promoInput.value.trim()) {
                        return;
                    }
                    handlePromoCodeUI(data, code);
                }, 400);
            }

            function showPaymentValidationAlert() {
                var wa = normalizePhone(whatsappInput ? whatsappInput.value : '');
                if (wa.length < 8) {
                    alert('Nomor WhatsApp tidak valid. Minimal 8 digit angka.');
                    try { whatsappInput && whatsappInput.focus(); } catch (_e) {}
                    return;
                }

                var code = promoInput ? promoInput.value.trim() : '';
                if (code !== '' && referralState !== 'valid' && voucherState !== 'valid') {
                    alert('Kode belum valid. Silakan periksa kembali kode Anda.');
                    try { promoInput.focus(); } catch (_e) {}
                    return;
                }
            }

            // Enable/disable button based on required fields
            updatePayButtonState();
            if (whatsappInput) {
                whatsappInput.addEventListener('input', updatePayButtonState);
                whatsappInput.addEventListener('blur', updatePayButtonState);
            }
            var checkPromoBtn = document.getElementById('checkPromoBtn');
            if (checkPromoBtn) {
                checkPromoBtn.addEventListener('click', function() {
                    if (promoTimer) clearTimeout(promoTimer);
                    var code = promoInput ? promoInput.value.trim() : '';
                    if (code === '') {
                        handlePromoCodeUI({ valid: false, message: 'Silakan masukkan kode terlebih dahulu.' }, '');
                        return;
                    }
                    referralState = 'checking';
                    voucherState = 'checking';
                    setPromoMessage('Memeriksa kode...', 'info');
                    updatePayButtonState();
                    validatePromoCodeServer(code).then(function(data) {
                        handlePromoCodeUI(data, code);
                    });
                });
            }

            if (promoInput) {
                promoInput.addEventListener('input', schedulePromoValidation);
                promoInput.addEventListener('blur', schedulePromoValidation);
                if (promoInput.value.trim() !== '') {
                    schedulePromoValidation();
                }
            } else {
                referralFinalAmount = getBaseAmount();
                updateTotalDisplay();
            }

            // Handle free registration submit
            if (manualPaymentForm && isFreeCourse) {
                manualPaymentForm.addEventListener('submit', function(e) {
                    updatePayButtonState();
                    if (freeEnrollBtn && freeEnrollBtn.disabled) {
                        e.preventDefault();
                        showPaymentValidationAlert();
                        return;
                    }
                    if (formWhatsappInput) formWhatsappInput.value = whatsappInput ? whatsappInput.value : '';
                    syncReferralInput();
                });
            }

            // Midtrans pay flow
            if (midtransPayBtn) {
                var pendingOrderUrl = @json(route('courses.payment.pending-order', $course));
                var cachedPending = null;

                async function fetchPendingCourseOrder(){
                    if (!pendingOrderUrl) return null;
                    try {
                        var res = await fetch(pendingOrderUrl, {
                            method: 'GET',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });
                        var data = await res.json();
                        if (!res.ok) return null;
                        return data;
                    } catch(_e) {
                        return null;
                    }
                }

                async function ensurePendingCourseLabel(){
                    var pending = await fetchPendingCourseOrder();
                    cachedPending = pending;
                    if (pending && pending.pending && pending.order_id) {
                        midtransPayBtn.textContent = 'Continue Payment';
                        if (pending.amount) {
                            setTotalAmount(pending.amount);
                        }
                        if (referralInput && pending.referral_code && (!getReferralCode() || getReferralCode() === '')) {
                            referralInput.value = pending.referral_code;
                            syncReferralInput();
                            scheduleReferralValidation();
                        }

                        // Autofill WA from pending payment if empty
                        if (pending.whatsapp_number && whatsappInput && (!whatsappInput.value || whatsappInput.value.trim() === '')) {
                            var raw = String(pending.whatsapp_number || '').trim();
                            if (raw.startsWith('+')) {
                                var m = raw.match(/^\+(\d{1,3})(.*)$/);
                                if (m) {
                                    var rest = String(m[2] || '').replace(/\D/g, '');
                                    whatsappInput.value = rest;
                                }
                            } else {
                                whatsappInput.value = raw.replace(/\D/g, '');
                            }
                        }

                        updatePayButtonState();
                    } else if (pending && pending.needs_force_new) {
                        cachedPending = null;
                        midtransPayBtn.textContent = 'Pay Now';
                        updatePayButtonState();
                    }
                }

                midtransPayBtn.addEventListener('click', async function(e){
                    e.preventDefault();
                    updatePayButtonState();
                    if (midtransPayBtn.disabled) {
                        showPaymentValidationAlert();
                        return;
                    }
                    if (typeof window.snap === 'undefined') {
                        alert('Midtrans belum siap. Pastikan client key sudah diset.');
                        return;
                    }

                    if (formWhatsappInput) formWhatsappInput.value = whatsappInput ? whatsappInput.value : '';
                    var dialVal = '+62';
                    var waVal = whatsappInput ? (whatsappInput.value || '').trim() : '';
                    var referralVal = getReferralCode();
                    var voucherVal = getVoucherCode();

                    var snapTokenUrl = @json(route('courses.payment.snap-token', $course));
                    var refreshUrl = @json(route('payment.refresh-course', ['orderId' => 'ORDER_ID']));
                    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                    async function getOrCreateSnapToken(forceNew){
                        var pending = cachedPending || await fetchPendingCourseOrder();
                        cachedPending = pending;
                        var pendingReferral = pending && pending.referral_code ? String(pending.referral_code).trim() : '';
                        var pendingVoucher = pending && pending.metadata && pending.metadata.voucher_code ? String(pending.metadata.voucher_code).trim() : '';
                        if (!forceNew && pending && pending.pending && pending.order_id && pending.snap_token && pendingReferral === referralVal && pendingVoucher === voucherVal) {
                            return { snap_token: pending.snap_token, order_id: pending.order_id };
                        }

                        var url = new URL(snapTokenUrl, window.location.origin);
                        if (dialVal) url.searchParams.set('dial_code', dialVal);
                        if (waVal) url.searchParams.set('whatsapp', waVal);
                        if (referralVal) url.searchParams.set('referral_code', referralVal);
                        if (voucherVal) url.searchParams.set('voucher_code', voucherVal);
                        if (forceNew) url.searchParams.set('force_new', '1');

                        var res = await fetch(url.toString(), {
                            method: 'GET',
                            headers: { 'X-Requested-With': 'XMLHttpRequest' },
                            credentials: 'same-origin'
                        });
                        var data = await res.json();
                        if (!res.ok || !data || (!data.snap_token && !data.redirect_url)) {
                            throw new Error((data && data.message) ? data.message : 'Gagal membuat token Midtrans');
                        }
                        return data;
                    }

                    midtransPayBtn.disabled = true;
                    var originalText = midtransPayBtn.textContent;
                    midtransPayBtn.textContent = 'Processing...';

                    try {
                        var data;
                        var forceNewFromQuery = (new URLSearchParams(window.location.search)).get('force_new') === '1';
                        var forceNewFromExpired = !!(cachedPending && cachedPending.needs_force_new);
                        if (forceNewFromQuery || forceNewFromExpired) {
                            cachedPending = null;
                            data = await getOrCreateSnapToken(true);
                        } else {
                            try {
                                data = await getOrCreateSnapToken(false);
                            } catch(_e) {
                                data = await getOrCreateSnapToken(true);
                            }
                        }

                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                            return;
                        }

                        window.snap.pay(data.snap_token, {
                            onSuccess: async function(){
                                try {
                                    var rUrl = refreshUrl.replace('ORDER_ID', encodeURIComponent(data.order_id));
                                    await fetch(rUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrf,
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        credentials: 'same-origin',
                                        body: JSON.stringify({})
                                    });
                                } catch(_e) {}
                                window.location.href = @json(route('course.learn', $course));
                            },
                            onPending: function(){
                                alert('Pembayaran pending. Silakan selesaikan pembayaran di Midtrans.');
                                cachedPending = { pending: true, order_id: data.order_id, snap_token: data.snap_token };
                                midtransPayBtn.textContent = 'Continue Payment';
                            },
                            onError: function(){
                                alert('Pembayaran gagal. Silakan coba lagi.');
                            },
                            onClose: async function(){
                                midtransPayBtn.disabled = true;
                                midtransPayBtn.textContent = 'Memeriksa status...';
                                try {
                                    var rUrl = refreshUrl.replace('ORDER_ID', encodeURIComponent(data.order_id));
                                    var res = await fetch(rUrl, {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': csrf,
                                            'X-Requested-With': 'XMLHttpRequest'
                                        },
                                        credentials: 'same-origin',
                                        body: JSON.stringify({})
                                    });
                                    var result = await res.json();
                                    if (result && result.status === 'settled') {
                                        window.location.href = @json(route('course.learn', $course));
                                        return;
                                    }
                                    if (result && (result.status === 'expired' || result.status === 'rejected')) {
                                        cachedPending = null;
                                        window.location.href = window.location.pathname + '?force_new=1';
                                        return;
                                    }
                                } catch(_e) {}
                                midtransPayBtn.disabled = false;
                                midtransPayBtn.textContent = 'Continue Payment';
                                updatePayButtonState();
                            }
                        });
                    } catch(err) {
                        alert(String(err && err.message ? err.message : err));
                    } finally {
                        midtransPayBtn.disabled = false;
                        if (cachedPending && cachedPending.pending && cachedPending.order_id) {
                            midtransPayBtn.textContent = 'Continue Payment';
                        } else {
                            midtransPayBtn.textContent = originalText;
                        }
                        updatePayButtonState();
                    }
                });

                ensurePendingCourseLabel();

                if ((new URLSearchParams(window.location.search)).get('force_new') === '1') {
                    cachedPending = null;
                    midtransPayBtn.textContent = 'Pay Now';
                }
            }
        });
    </script>
</body>
</html>
@include('partials.footer-after-login')