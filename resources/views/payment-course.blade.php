<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - Digital Marketing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
            /* Background halaman */
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
            flex-wrap: wrap;
            /* Agar responsif di HP */
        }

        /* --- BAGIAN KIRI (FORM) --- */
        .box_kiri_biodata {
            flex: 1;
            /* Mengambil sisa ruang */
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
            border: 1px solid #FFC107;
            /* Border Kuning */
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
            accent-color: #FFC107;
            /* Warna radio button saat aktif */
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
            width: 350px;
            /* Lebar fix untuk sisi kanan */
        }

        .box_biodata {
            background-color: #e6f0fa;
            /* Warna biru muda */
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
            color: #2ecc71;
            /* Warna hijau harga */
            margin: 0;
        }

        /* Garis Putus-putus Tiket */
        .ticket-divider {
            border-bottom: 2px dashed #bacddf;
            margin: 20px -25px;
            /* Melebar keluar padding */
            position: relative;
        }

        /* Efek bulatan sobekan tiket kiri kanan */
        .ticket-divider::before,
        .ticket-divider::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: #fff;
            /* Sama dengan bg container utama */
            border-radius: 50%;
            top: -11px;
        }

        .ticket-divider::before {
            left: -10px;
        }

        .ticket-divider::after {
            right: -10px;
        }

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
            background-color: #f4c430;
            /* Warna tombol kuning */
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
        .qris-modal .modal-dialog {
            max-width: 520px;
            margin: .75rem auto;
        }

        .qris-modal .modal-content {
            border-radius: 16px;
            overflow: hidden;
        }

        .qris-modal .modal-header {
            padding: .9rem 1rem;
        }

        .qris-modal .modal-body {
            padding: 1rem;
        }

        .qris-modal .qris-image {
            max-width: 100%;
            height: auto;
            max-height: 42dvh;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .qris-modal .modal-body p {
            margin-bottom: .65rem;
        }
    </style>
</head>

<body>
    @include("partials.navbar-after-login")
    <div class="box_luar_payment">
        <div class="link_back_payment_course">
            <a href="{{ route('dashboard') }}">Home</a>
            <p>/</p>
            <a href="/courses">Course</a>
            <p>/</p>
            <a href="{{ route('course.detail', $course->id) }}">{{ $course->name ?? '-' }}</a>
            <p>/</p>
            <span style="color: #333; font-weight: 500;">Payment</span>
        </div>

        <div class="biodata_payment_course">

            <div class="box_kiri_biodata">
                <h5>Data Peserta</h5>


                <div class="input_biodata">
                    <p>Email</p>
                    <input class="kolom_input_biodata" type="email" placeholder="Masukkan email anda"
                        value="{{ Auth::user()->email ?? '' }}" readonly>
                </div>

                <div class="input_biodata">
                    <p>Nama Lengkap</p>
                    <div class="info_biodata">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                            class="bi bi-info-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path
                                d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                        </svg>
                        <span>Nama Akan digunakan pada sertifikat</span>
                    </div>
                    <input class="kolom_input_biodata" type="text" value="{{ Auth::user()->name ?? '' }}" readonly>
                </div>

                <div class="input_biodata">
                    <p>No Whatsapp</p>
                    <div class="whatsapp_biodata">
                        <div class="dropdown">
                            <button class="btn_nomor dropdown-toggle" type="button" id="kodeDialBtn"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                +62
                            </button>
                        </div>
                        <ul class="dropdown-menu" id="kodeDialMenu" aria-labelledby="kodeDialBtn"
                            style="position: absolute;">
                            <li><a class="dropdown-item" href="#" data-code="+62">+62</a></li>
                            <li><a class="dropdown-item" href="#" data-code="+60">+60</a></li>
                            <li><a class="dropdown-item" href="#" data-code="+1">+1</a></li>
                        </ul>
                        <input type="hidden" name="kode_dial" id="kodeDialInput" value="+62">
                        <input class="input_nomor" type="text" placeholder="No Whatsapp" id="whatsappNumberInput"
                            inputmode="tel" autocomplete="tel">
                    </div>
                </div>

                <div class="input_biodata">
                    <p>Kode Referral <span style="color:#888; font-weight:400;">(opsional)</span></p>
                    <div style="display: flex; gap: 10px;">
                        <input class="kolom_input_biodata" style="margin-bottom:0;" type="text" id="referralCodeInput"
                            name="referral_code" placeholder="Masukkan kode referral (jika ada)">
                        <button type="button" id="btnCheckReferral" class="btn_nomor"
                            style="background-color:#FFC107; border:none; font-weight:600;">Cek</button>
                    </div>
                    <small id="referralMessage" style="display:block; margin-top:5px; font-size:11px;"></small>
                </div>


            </div>

            <div class="box_kanan_biodata">
                <div class="box_biodata">
                    <div
                        style="width: 40px; height: 5px; background:#ff7f50; border-radius:5px; margin-bottom:15px; position:absolute; top:-2px; left:50%; transform:translateX(-50%);">
                    </div>

                    <h3>Order Detail Course</h3>

                    <div class="box_event_payment">
                        <img src="{{ $course->card_thumbnail ? asset('uploads/' . $course->card_thumbnail) : 'https://img.freepik.com/vektor-premium/live-concert-horizontal-banner-template_23-2150997973.jpg' }}"
                            alt="Course Card Image">
                        <div class="judul_event">
                            <h4>{{ $course->name ?? '-' }}</h4>
                            <p class="penyelenggara">{{ $course->category->name ?? '-' }}</p>
                            <p class="harga_judul_event">Rp{{ number_format($course->price ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="ticket-divider"></div>

                    <div class="harga_teks_payment">
                        <div class="teks_payment">
                            <p>Total</p>
                            <h4 id="totalPriceDisplay" data-original-price="{{ $course->price ?? 0 }}">Rp {{
                                number_format($course->price ?? 0, 0, ',', '.') }}</h4>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor"
                            class="bi bi-file-text icon-invoice" viewBox="0 0 16 16">
                        </svg>
                    </div>
                </div>

                <!-- Manual payment via QRIS: show QR image modal when clicking Bayar -->
                @php
                $isFreeCourse = (int) ($course->price ?? 0) <= 0; @endphp <form id="manualPaymentForm" method="POST"
                    action="{{ $isFreeCourse ? route('midtrans.pay', $course) : '#' }}"
                    data-is-free="{{ $isFreeCourse ? '1' : '0' }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ Auth::user()->email ?? '' }}">
                    <input type="hidden" name="name" value="{{ Auth::user()->name ?? '' }}">
                    <input type="hidden" name="kode_dial" id="formKodeDialInput" value="+62">
                    <input type="hidden" name="whatsapp" id="formWhatsappInput">
                    <button type="button" id="showQrisBtn" class="btn_bayar_payment" disabled>Bayar</button>
                    </form>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- QRIS Modal -->
    <div class="modal fade qris-modal" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrisModalLabel">Pembayaran - QRIS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-secondary">Scan QRIS berikut untuk melakukan pembayaran.</p>

                    <img id="qrisImage" class="qris-image" src="{{ asset('aset/Qris Payment IdSpora.jpeg') }}"
                        alt="QRIS Payment">

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ asset('aset/Qris Payment IdSpora.jpeg') }}" class="btn btn-outline-primary"
                            download>
                            Download QR
                        </a>
                        <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                            data-bs-target="#uploadProofCollapse" aria-expanded="false"
                            aria-controls="uploadProofCollapse">
                            Saya sudah bayar, upload bukti
                        </button>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    </div>

                    <div class="collapse mt-3 text-start" id="uploadProofCollapse">
                        <div class="p-3 rounded-3" style="background:#f8fafc; border:1px solid #e5e7eb;">
                            <p class="mb-2 text-secondary">Setelah melakukan pembayaran, silakan upload bukti pembayaran
                                di bawah ini.</p>

                            <!-- Upload bukti pembayaran -->
                            <form id="uploadProofForm" method="POST"
                                action="{{ route('courses.manual-payment.upload', $course) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="whatsapp" id="formWhatsappFullInput">
                                <input type="hidden" name="referral_code" id="formReferralCodeInput">

                                <div class="mb-2">
                                    <label for="paymentProofInput" class="form-label">Upload Bukti Pembayaran (JPG/PNG,
                                        max 5MB)</label>
                                    <input class="form-control" type="file" id="paymentProofInput" name="payment_proof"
                                        accept="image/*" required>
                                </div>
                                <div id="proofPreview" class="mb-3" style="display:none;">
                                    <p class="mb-1">Preview bukti:</p>
                                    <img id="proofPreviewImg" src="" alt="Preview"
                                        style="max-width:100%; height:auto; border-radius:8px; border:1px solid #e5e7eb;">
                                </div>

                                <button type="submit" id="payNowBtn" class="btn_bayar_payment">Bayar Sekarang</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm proof submission Modal -->
    <div class="modal fade" id="confirmProofModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Bukti Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-1 fw-semibold">Yakin untuk bukti pembayaran sudah benar?</p>
                    <p class="mb-0 text-danger">Tindakan ini tidak dapat dibatalkan!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="confirmProofSubmitBtn" class="btn btn-primary">Ya, kirim</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var kodeDialBtn = document.getElementById('kodeDialBtn');
            var kodeDialMenu = document.getElementById('kodeDialMenu');
            var kodeDialInput = document.getElementById('kodeDialInput');
            var formKodeDialInput = document.getElementById('formKodeDialInput');
            var whatsappInput = document.getElementById('whatsappNumberInput') || document.querySelector('.input_nomor');
            var formWhatsappInput = document.getElementById('formWhatsappInput');
            var referralCodeInput = document.getElementById('referralCodeInput');
            var formReferralCodeInput = document.getElementById('formReferralCodeInput');
            var formWhatsappFullInput = document.getElementById('formWhatsappFullInput');
            var showQrisBtn = document.getElementById('showQrisBtn');
            var manualPaymentForm = document.getElementById('manualPaymentForm');
            var uploadProofForm = document.getElementById('uploadProofForm');
            var confirmProofModalEl = document.getElementById('confirmProofModal');
            var confirmProofSubmitBtn = document.getElementById('confirmProofSubmitBtn');
            var pendingProofSubmit = false;

            var isFreeCourse = false;
            if (manualPaymentForm) {
                isFreeCourse = (manualPaymentForm.getAttribute('data-is-free') || '0') === '1';
            }

            function normalizePhone(value) {
                return (value || '').replace(/[^0-9]/g, '');
            }

            function updatePayButtonState() {
                if (!showQrisBtn) return;
                var wa = normalizePhone(whatsappInput ? whatsappInput.value : '');
                // For free course, allow without WhatsApp. For paid, WhatsApp is required.
                showQrisBtn.disabled = (!isFreeCourse) && (wa.length === 0);
            }

            // Show dropdown on button click
            if (kodeDialBtn) {
                kodeDialBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    kodeDialMenu.classList.toggle('show');
                });
            }
            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (kodeDialBtn && kodeDialMenu && !kodeDialBtn.contains(e.target) && !kodeDialMenu.contains(e.target)) {
                    kodeDialMenu.classList.remove('show');
                }
            });
            // Select code
            if (kodeDialMenu) {
                kodeDialMenu.querySelectorAll('.dropdown-item').forEach(function (item) {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        var code = item.getAttribute('data-code');
                        kodeDialBtn.textContent = code;
                        kodeDialInput.value = code;
                        formKodeDialInput.value = code;
                        kodeDialMenu.classList.remove('show');
                    });
                });
            }

            // Enable/disable Bayar button based on required fields
            updatePayButtonState();
            if (whatsappInput) {
                whatsappInput.addEventListener('input', updatePayButtonState);
                whatsappInput.addEventListener('blur', updatePayButtonState);
            }

            // Show QRIS modal when clicking bayar
            if (showQrisBtn) {
                showQrisBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    // guard (in case button enabled state is bypassed)
                    updatePayButtonState();
                    if (showQrisBtn.disabled) {
                        alert('Silakan isi No Whatsapp terlebih dahulu.');
                        try { whatsappInput && whatsappInput.focus(); } catch (err) { }
                        return;
                    }
                    if (formKodeDialInput) formKodeDialInput.value = kodeDialInput.value;
                    if (formWhatsappInput) formWhatsappInput.value = whatsappInput ? whatsappInput.value : '';
                    if (formReferralCodeInput) formReferralCodeInput.value = referralCodeInput ? referralCodeInput.value.trim() : '';
                    if (formWhatsappFullInput) {
                        var dial = (kodeDialInput && kodeDialInput.value) ? kodeDialInput.value : '+62';
                        var waRaw = whatsappInput ? whatsappInput.value : '';
                        formWhatsappFullInput.value = (dial + waRaw).replace(/\s+/g, '');
                    }

                    // Free course: submit directly to enroll (no QRIS / no admin validation)
                    if (isFreeCourse) {
                        try {
                            if (manualPaymentForm && manualPaymentForm.getAttribute('action') && manualPaymentForm.getAttribute('action') !== '#') {
                                manualPaymentForm.submit();
                                return;
                            }
                        } catch (_e) {
                            // fallthrough to default behavior
                        }
                    }

                    var qrisEl = document.getElementById('qrisModal');
                    if (qrisEl && window.bootstrap) {
                        var qrisModal = new window.bootstrap.Modal(qrisEl);
                        // keep modal compact on open
                        try {
                            var collapseEl = document.getElementById('uploadProofCollapse');
                            if (collapseEl && window.bootstrap.Collapse) {
                                var collapse = window.bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
                                collapse.hide();
                            }
                            var paymentProofInputEl = document.getElementById('paymentProofInput');
                            var proofPreviewEl = document.getElementById('proofPreview');
                            if (paymentProofInputEl) paymentProofInputEl.value = '';
                            if (proofPreviewEl) proofPreviewEl.style.display = 'none';
                        } catch (e) { }
                        qrisModal.show();
                    } else if (qrisEl) {
                        qrisEl.classList.add('show');
                        qrisEl.style.display = 'block';
                        document.body.classList.add('modal-open');
                    }
                });
            }

            // Confirm modal before submitting payment proof
            if (uploadProofForm && confirmProofModalEl && window.bootstrap) {
                uploadProofForm.addEventListener('submit', function (e) {
                    if (pendingProofSubmit) return;
                    e.preventDefault();

                    // sync latest inputs to hidden fields
                    if (formReferralCodeInput) formReferralCodeInput.value = referralCodeInput ? referralCodeInput.value.trim() : '';
                    if (formWhatsappFullInput) {
                        var dial = (kodeDialInput && kodeDialInput.value) ? kodeDialInput.value : '+62';
                        var waRaw = whatsappInput ? whatsappInput.value : '';
                        formWhatsappFullInput.value = (dial + waRaw).replace(/\s+/g, '');
                    }

                    var m = window.bootstrap.Modal.getOrCreateInstance(confirmProofModalEl);
                    m.show();
                });

                if (confirmProofSubmitBtn) {
                    confirmProofSubmitBtn.addEventListener('click', function () {
                        if (pendingProofSubmit) return;
                        pendingProofSubmit = true;
                        try { confirmProofSubmitBtn.disabled = true; } catch (e) { }
                        uploadProofForm.submit();
                    });
                }

                confirmProofModalEl.addEventListener('hidden.bs.modal', function () {
                    pendingProofSubmit = false;
                    if (confirmProofSubmitBtn) {
                        try { confirmProofSubmitBtn.disabled = false; } catch (e) { }
                    }
                });
            }

            // Preview selected proof image and simple client-side validation
            var paymentProofInput = document.getElementById('paymentProofInput');
            var proofPreview = document.getElementById('proofPreview');
            var proofPreviewImg = document.getElementById('proofPreviewImg');
            var uploadProofForm = document.getElementById('uploadProofForm');
            var payNowBtn = document.getElementById('payNowBtn');

            if (paymentProofInput) {
                paymentProofInput.addEventListener('change', function (e) {
                    var file = paymentProofInput.files[0];
                    if (!file) {
                        proofPreview.style.display = 'none';
                        return;
                    }
                    // size check (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal 5MB.');
                        paymentProofInput.value = '';
                        proofPreview.style.display = 'none';
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function (evt) {
                        proofPreviewImg.src = evt.target.result;
                        proofPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Optionally handle upload form submit: simple UX feedback
            if (uploadProofForm) {
                uploadProofForm.addEventListener('submit', function (e) {
                    // let normal POST happen; show simple feedback
                    if (!paymentProofInput || !paymentProofInput.files[0]) {
                        e.preventDefault();
                        alert('Silakan pilih file bukti pembayaran terlebih dahulu.');
                        return;
                    }
                    // ensure latest referral code value is posted
                    if (formReferralCodeInput) formReferralCodeInput.value = referralCodeInput ? referralCodeInput.value.trim() : '';
                    payNowBtn.disabled = true;
                    payNowBtn.textContent = 'Mengirim...';
                });
            }
            var btnCheck = document.getElementById('btnCheckReferral');
            var messageBox = document.getElementById('referralMessage');
            var priceDisplay = document.getElementById('totalPriceDisplay');

            if (priceDisplay && btnCheck) {
                // Ambil harga asli dari atribut HTML
                var originalPrice = parseInt(priceDisplay.getAttribute('data-original-price')) || 0;

                btnCheck.addEventListener('click', function () {
                    var code = referralCodeInput ? referralCodeInput.value.trim() : '';
                    if (code === '') {
                        messageBox.innerHTML = '<span style="color:red;">Masukkan kode dulu.</span>';
                        return;
                    }

                    btnCheck.textContent = '...';

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
                            btnCheck.textContent = 'Cek';
                            if (data.valid) {
                                messageBox.innerHTML = `<span style="color:green;">${data.message}</span>`;

                                // Hitung diskon persentase (misal 10%)
                                var discountAmount = originalPrice * (data.discount_percentage / 100);
                                var newPrice = originalPrice - discountAmount;

                                if (newPrice < 0) newPrice = 0;

                                // Format angka ke format Rupiah (titik)
                                priceDisplay.innerHTML = 'Rp ' + newPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            } else {
                                messageBox.innerHTML = `<span style="color:red;">${data.message}</span>`;
                                // Kembalikan ke harga asli kalau gagal
                                priceDisplay.innerHTML = 'Rp ' + originalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        })
                        .catch(error => {
                            btnCheck.textContent = 'Cek';
                            console.error('Error:', error);
                        });
                });
            }
        });
    </script>
</body>

</html>
@include('partials.footer-before-login')