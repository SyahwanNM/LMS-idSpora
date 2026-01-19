<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Digital Marketing</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
     @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                <h5>Data Peserta</h5>
                

                <div class="input_biodata">
                    <p>Email</p>
                    <input class="kolom_input_biodata" type="email" placeholder="Masukkan email anda" value="{{ Auth::user()->email ?? '' }}" readonly>
                </div>

                <div class="input_biodata">
                    <p>Nama Lengkap</p>
                    <div class="info_biodata">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-info-circle" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                        </svg>
                        <span>Nama Akan digunakan pada sertifikat</span>
                    </div>
                    <input class="kolom_input_biodata" type="text" value="{{ Auth::user()->name ?? '' }}" readonly>
                </div>

                <div class="input_biodata">
                    <p>No Whatsapp</p>
                    <div class="whatsapp_biodata">
                        <div class="dropdown">
                            <button class="btn_nomor dropdown-toggle" type="button" id="kodeDialBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                +62
                            </button>
                        </div>
                        <ul class="dropdown-menu" id="kodeDialMenu" aria-labelledby="kodeDialBtn" style="position: absolute;">
                            <li><a class="dropdown-item" href="#" data-code="+62">+62</a></li>
                            <li><a class="dropdown-item" href="#" data-code="+60">+60</a></li>
                            <li><a class="dropdown-item" href="#" data-code="+1">+1</a></li>
                        </ul>
                        <input type="hidden" name="kode_dial" id="kodeDialInput" value="+62">
                        <input class="input_nomor" type="text" placeholder="No Whatsapp">
                    </div>
                </div>

                
            </div>

            <div class="box_kanan_biodata">
                <div class="box_biodata">
                    <div style="width: 40px; height: 5px; background:#ff7f50; border-radius:5px; margin-bottom:15px; position:absolute; top:-2px; left:50%; transform:translateX(-50%);"></div>

                    <h3>Order Detail Course</h3>
                    
                    <div class="box_event_payment">
                        <img src="{{ $course->card_thumbnail ? asset('storage/' . $course->card_thumbnail) : 'https://img.freepik.com/vektor-premium/live-concert-horizontal-banner-template_23-2150997973.jpg' }}" alt="Course Card Image">
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
                            <h4>Rp {{ number_format($course->price ?? 0, 0, ',', '.') }}</h4>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="currentColor" class="bi bi-file-text icon-invoice" viewBox="0 0 16 16">
                            <path d="M5 4a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm-.5 2.5A.5.5 0 0 1 5 6h6a.5.5 0 0 1 0 1H5a.5.5 0 0 1-.5-.5zM5 8a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1H5zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1H5z"/>
                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1z"/>
                        </svg>
                    </div>
                </div>

                <form id="midtransPaymentForm" method="POST" action="{{ route('midtrans.pay', $course->id) }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ Auth::user()->email ?? '' }}">
                    <input type="hidden" name="name" value="{{ Auth::user()->name ?? '' }}">
                    <input type="hidden" name="kode_dial" id="formKodeDialInput" value="+62">
                    <input type="hidden" name="whatsapp" id="formWhatsappInput">
                    <button type="submit" class="btn_bayar_payment">Bayar</button>
                </form>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @php
        $snapJsUrl = config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js';
        $clientKey = config('midtrans.client_key');
    @endphp
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var kodeDialBtn = document.getElementById('kodeDialBtn');
            var kodeDialMenu = document.getElementById('kodeDialMenu');
            var kodeDialInput = document.getElementById('kodeDialInput');
            var formKodeDialInput = document.getElementById('formKodeDialInput');
            var whatsappInput = document.querySelector('.input_nomor');
            var formWhatsappInput = document.getElementById('formWhatsappInput');
            var midtransForm = document.getElementById('midtransPaymentForm');

            // Show dropdown on button click
            kodeDialBtn.addEventListener('click', function(e) {
                e.preventDefault();
                kodeDialMenu.classList.toggle('show');
            });
            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!kodeDialBtn.contains(e.target) && !kodeDialMenu.contains(e.target)) {
                    kodeDialMenu.classList.remove('show');
                }
            });
            // Select code
            kodeDialMenu.querySelectorAll('.dropdown-item').forEach(function(item) {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    var code = item.getAttribute('data-code');
                    kodeDialBtn.textContent = code;
                    kodeDialInput.value = code;
                    formKodeDialInput.value = code;
                    kodeDialMenu.classList.remove('show');
                });
            });

            // On form submit, use AJAX to get snap token and open Snap modal
            if (midtransForm) {
                midtransForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    formKodeDialInput.value = kodeDialInput.value;
                    formWhatsappInput.value = whatsappInput.value;

                    var formData = new FormData(midtransForm);
                    // send request to create payment and return snap token
                    fetch(midtransForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '{{ csrf_token() }}'
                        },
                        body: formData,
                    }).then(function(res){
                        return res.json();
                    }).then(function(json){
                        if(json.snapToken){
                            // load snap.js dynamically with client key
                            var snapScript = document.createElement('script');
                            snapScript.src = '{{ $snapJsUrl }}';
                            snapScript.setAttribute('data-client-key', '{{ $clientKey }}');
                            document.body.appendChild(snapScript);
                            snapScript.onload = function(){
                                window.snap.pay(json.snapToken, {
                                    onSuccess: function(result){
                                        // refresh payment status on server
                                        fetch('/payment/refresh-course/'+json.orderId, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                            .then(function(r){ return r.json(); })
                                            .catch(function(){ /* ignore */ })
                                            .finally(function(){
                                                // redirect to course detail
                                                window.location.href = '/course-detail/' + json.courseId;
                                            });
                                    },
                                    onPending: function(result){
                                        window.location.href = '/course-detail/' + json.courseId;
                                    },
                                    onError: function(err){
                                        alert('Pembayaran gagal: ' + (err.message || JSON.stringify(err)));
                                    },
                                    onClose: function(){
                                        // User closed the popup
                                    }
                                });
                            };
                        } else if (json.redirectUrl) {
                            // fallback to redirect link
                            window.location.href = json.redirectUrl;
                        } else {
                            alert('Gagal memproses pembayaran. Coba lagi.');
                        }
                    }).catch(function(err){
                        alert('Terjadi kesalahan jaringan saat memproses pembayaran.');
                    });
                });
            }
        });
    </script>
</body>
</html>
@include('partials.footer-before-login')