
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment - {{ isset($event)? $event->title : 'Event' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
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
            border-radius: 10px; /* Radius sedikit diperkecil */
            padding: 16px; /* Padding card diperkecil (sebelumnya 24px) */
            margin-bottom: 16px; /* Jarak antar card diperkecil */
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1.4fr 0.8fr;
            gap: 16px; /* Gap antar kolom diperkecil */
            align-items: start;
        }

        .form-label-custom { 
            font-weight: 600; 
            font-size: 13px; 
            margin-bottom: 4px; 
            color: #111827; 
        }
        .form-control-custom, .form-select-custom {
            display: block; width: 100%; 
            padding: 8px 10px; /* Padding input diperkecil */
            font-size: 13px;
            color: #111827; background-color: #fff; border: 1px solid #FCD34D;
            border-radius: 6px; transition: 0.15s;
        }
        .form-control-custom:focus, .form-select-custom:focus {
            border-color: #EAB308; outline: 0; box-shadow: 0 0 0 3px rgba(253, 224, 71, 0.25);
        }
        .form-control-custom[readonly] { background-color: #FAFAFA; color: #6B7280; }
        
        /* Jarak antar form group diperkecil */
        .mb-custom { margin-bottom: 12px; } 

        /* Warning Text */
        .warning-text { color: #EF4444; font-size: 11px; font-style: italic; display: flex; align-items: center; gap: 4px; margin-bottom: 4px; }

        /* Whatsapp */
        .wa-group { display: flex; gap: 8px; align-items: center; }
        .wa-group input[type="text"] { flex: 1; min-width: 0; }

        /* Order Detail Items */
        .order-detail-content { display: flex; gap: 12px; align-items: flex-start; }
        .order-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; } /* Gambar lebih kecil */
        .order-info h5 { font-size: 13px; font-weight: 600; margin: 0 0 2px; line-height: 1.2; }
        .order-info p { font-size: 11px; color: #6B7280; margin: 0 0 4px; }
        .price-text { color: #16A34A; font-weight: 600; font-size: 13px; }

        /* --- TOTAL BIAYA SECTION (Compact) --- */
        .total-header { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .bag-icon {
            border: 1px solid #111827; border-radius: 6px; width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
        }
        .bag-icon svg { width: 16px; height: 16px; }
        
        .price-row { display: flex; justify-content: space-between; color: #6B7280; font-size: 13px; font-weight: 500; margin-bottom: 4px; }
        
        .total-row {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 18px; font-weight: 700; color: #111827; margin-top: 10px; padding-top: 10px;
            border-top: 1px solid #E5E7EB;
        }

        .btn-pay {
            width: 100%;
            background-color: #FACC15;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            padding: 10px; /* Padding tombol diperkecil */
            border: none;
            border-radius: 8px;
            margin-top: 14px;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-pay:hover { background-color: #EAB308; }
        .btn-pay:disabled { opacity: 0.5; cursor: not-allowed; }
        
        h3 { font-size: 16px; font-weight: 700; margin-bottom: 12px; }

        /* Responsive */
        @media (max-width: 992px) {
            .grid-layout { grid-template-columns: 1fr; }
        }
    </style>
</head>

<body>
    @include('partials.navbar-after-login')
    <div class="payment-container" style="margin-bottom: 12px; margin-top: 85px;">
        <div style="font-size: 12px; color: #6B7280;">
            <a href="{{ route('dashboard') }}" style="text-decoration:none; color:inherit;">Home</a> / 
            <a href="{{ route('events.index') }}" style="text-decoration:none; color:inherit;">Event</a> / 
            @if(isset($isStage2) && $isStage2)
                <a href="{{ route('events.registered.detail', $event) }}" style="text-decoration:none; color:inherit;">{{ $event->title }}</a> / 
                <span style="color:#111827; font-weight:600;">Payment Tahap 2</span>
            @else
                <span style="color:#111827; font-weight:600;">Payment</span>
            @endif
        </div>
    </div>

    <form id="paymentForm" method="POST" action="{{ isset($isStage2) && $isStage2 ? route('events.payment.stage2.manual', $event) : route('payment.manual.register', $event->id ?? '') }}" enctype="multipart/form-data" novalidate>
        @csrf
        <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">

        @php
            $isStage2 = $isStage2 ?? false;
            $isHybridPayment = !$isStage2 && isset($event) && !empty($event->maps_url) && !empty($event->zoom_link)
                               && ($event->price_offline > 0 || $event->price_online > 0);
            $discountRate    = (!$isStage2 && isset($event) && $event->hasDiscount() && !empty($event->discount_percentage))
                               ? (float) $event->discount_percentage / 100 : 0.0;
            $priceOfflineFinal = $isHybridPayment
                               ? (int) round((float)($event->price_offline ?? 0) * (1 - $discountRate))
                               : 0;
            $priceOnlineFinal  = $isHybridPayment
                               ? (int) round((float)($event->price_online ?? 0) * (1 - $discountRate))
                               : 0;
            $isFree = $isStage2 ? ((int)($event->price_stage2 ?? 0) === 0) : (isset($event) ? ((int)($event->price ?? 0) === 0) : false);
            $hasDiscount = !$isStage2 && isset($event) ? $event->hasDiscount() : false;
            $finalPrice = $isStage2
                        ? ($event->price_stage2 ?? 0)
                        : ($isHybridPayment
                            ? $priceOfflineFinal
                            : (isset($event) ? ($hasDiscount ? ($event->discounted_price ?? 0) : ($event->price ?? 0)) : 0));
        @endphp

        <div class="payment-container" style="margin-top: 0;">
            <div class="grid-layout">
                
                <div class="left-col">
                    <div class="card-custom">
                        <h3>{{ $isStage2 ? 'Participant Data (Konfirmasi)' : 'Participant Data' }}</h3>
                        
                        <div class="mb-custom">
                            <label class="form-label-custom">Email</label>
                            <input type="email" class="form-control-custom" name="email" value="{{ auth()->user()->email ?? '' }}" readonly>
                        </div>

                        <div class="mb-custom">
                            <label class="form-label-custom" style="margin-bottom:0">Full Name</label>
                            <input type="text" class="form-control-custom" name="full_name" value="{{ old('full_name', $isStage2 ? ($registration->full_name ?? auth()->user()->name) : (auth()->user()->name ?? '')) }}" placeholder="Nama sesuai sertifikat" required minlength="3" {{ $isStage2 ? 'readonly' : '' }}>
                            <div class="warning-text" style="margin-top:4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                </svg>
                                Harap Diisi dengan Nama Lengkap (beserta gelar jika ada) yang Akan digunakan pada sertifikat
                                
                            </div>
                        </div>

                        <div class="mb-custom"> <label class="form-label-custom">Whatsapp Number</label>
                            <div class="wa-group">
                                <input type="hidden" name="dial_code" value="">
                                <input type="text" class="form-control-custom" name="whatsapp" value="{{ old('whatsapp', $isStage2 ? ($registration->whatsapp_number ?? auth()->user()->phone) : '') }}" placeholder="Example: 6281234567890" inputmode="numeric" required style="flex:1;min-width:0;" {{ $isStage2 ? 'readonly' : '' }}>
                            </div>
                        </div>

                        <!-- Asal Perguruan Tinggi -->
                        <div class="mb-custom">
                            <label class="form-label-custom">Company Name/Organization <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control-custom" name="university_origin" value="{{ old('university_origin', $isStage2 ? ($registration->university_origin ?? auth()->user()->institution) : (auth()->user()->institution ?? '')) }}" placeholder="Example: Telkom University" required {{ $isStage2 ? 'readonly' : '' }}>
                        </div>

                        <!-- Program Studi / Departemen / Unit Kerja -->
                        <div class="mb-custom">
                            <label class="form-label-custom">Study Program / Department / Work Unit <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control-custom" name="study_program" value="{{ old('study_program', $isStage2 ? ($registration->study_program ?? '') : '') }}" placeholder="Example: Informatics Engineering / Department of Computer Science" required {{ $isStage2 ? 'readonly' : '' }}>
                        </div>

                        <!-- Jabatan -->
                        <div class="mb-custom">
                            <label class="form-label-custom">Position <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="form-control-custom" name="position" value="{{ old('position', $isStage2 ? ($registration->position ?? auth()->user()->profession) : (auth()->user()->profession ?? '')) }}" placeholder="Example: Lecturer / Student / Staff" required {{ $isStage2 ? 'readonly' : '' }}>
                        </div>
                        @if(isset($event) && (bool) ($event->is_reseller_event ?? false) && !$isStage2)
                        <div class="mb-custom">
                            <label class="form-label-custom">Referral Code (opsional)</label>
                            <input type="text" class="form-control-custom" name="referral_code" id="referralCodeInput" placeholder="Have a referral code? Enter it here" value="{{ request()->query('ref', request()->cookie('referral_code', '')) }}">
                            <div id="referralMessage" class="form-text small" style="display:none;">&nbsp;</div>
                            <div class="form-text small">Enter the reseller referral code to get a discount/commission.</div>
                        </div>
                        @endif

                        @if(!$isStage2)
                        <div class="mb-custom">
                            <label class="form-label-custom">Voucher Code (opsional)</label>
                            <input type="text" class="form-control-custom" name="voucher_code" id="voucherCodeInput" placeholder="Masukkan kode voucher penukaran Anda" autocomplete="off">
                            <div id="voucherMessage" class="form-text small text-danger" style="display:none;">&nbsp;</div>
                            <div class="form-text small">Masukkan kode voucher yang telah ditukarkan di halaman profil untuk potongan harga.</div>
                        </div>
                        @endif


                        @if($isHybridPayment)
                        <div class="mb-custom" style="margin-top:14px;">
                            <label class="form-label-custom" style="margin-bottom:8px;">Attendance Type <span style="color:#ef4444;">*</span></label>
                            <input type="hidden" name="attendance_type" id="attendanceTypeInput" value="offline">
                            <div style="display:flex; gap:10px;">
                                <label id="label-offline" style="flex:1; display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; border:2px solid #1565c0; background:#e8f4fd; cursor:pointer; font-size:13px; font-weight:600; color:#1565c0; transition:all .2s;">
                                    <input type="radio" name="attendance_type_radio" value="offline" checked style="accent-color:#1565c0;">
                                    <span>Offline</span>
                                    <span id="price-offline-label" style="margin-left:auto; font-weight:700;">
                                        Rp{{ number_format($priceOfflineFinal, 0, ',', '.') }}
                                    </span>
                                </label>
                                <label id="label-online" style="flex:1; display:flex; align-items:center; gap:10px; padding:10px 14px; border-radius:10px; border:2px solid #e0e0e0; background:#fff; cursor:pointer; font-size:13px; font-weight:600; color:#555; transition:all .2s;">
                                    <input type="radio" name="attendance_type_radio" value="online" style="accent-color:#c62828;">
                                    <span>Online</span>
                                    <span id="price-online-label" style="margin-left:auto; font-weight:700;">
                                        Rp{{ number_format($priceOnlineFinal, 0, ',', '.') }}
                                    </span>
                                </label>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    
                </div>

                <div class="right-col">
                    <div class="card-custom">
                        <h3>{{ $isStage2 ? 'Pembayaran Tahap 2' : 'Checkout Detail Event' }}</h3>
                        <div class="order-detail-content">
                            @if(isset($event))
                                <img src="{{ $event->image_url ?? asset('aset/event.png') }}" alt="{{ $event->title }}" class="order-img" onerror="this.src='{{ asset('aset/event.png') }}'">
                            @else
                                <img src="{{ asset('aset/event.png') }}" alt="Event" class="order-img">
                            @endif
                            <div class="order-info">
                                <h5>{{ isset($event)? $event->title : 'Event Title' }}</h5>
                                <p>IdSpora</p>
                                <div class="price-text" id="eventPriceText"
                                    data-base-amount="{{ (int) round($finalPrice ?? 0) }}"
                                    data-is-hybrid="{{ $isHybridPayment ? 'true' : 'false' }}"
                                    data-price-offline="{{ $priceOfflineFinal ?? 0 }}"
                                    data-price-online="{{ $priceOnlineFinal ?? 0 }}"
                                    data-price-offline-raw="{{ (int) round((float)($event->price_offline ?? 0)) }}"
                                    data-price-online-raw="{{ (int) round((float)($event->price_online ?? 0)) }}">
                                    @if($isFree)
                                        FREE
                                    @elseif($isHybridPayment)
                                        Rp{{ number_format($priceOfflineFinal, 0, ',', '.') }}
                                    @elseif(isset($event) && $event->hasDiscount())
                                        <span style="text-decoration: line-through; color: #888; font-size: 0.85em; margin-right: 6px; font-weight: 400;">Rp{{ number_format($event->price, 0, ',', '.') }}</span>
                                        Rp{{ number_format($finalPrice, 0, ',', '.') }}
                                    @else
                                        Rp{{ number_format($finalPrice, 0, ',', '.') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                    @if(isset($isStage2) && $isStage2 && $existingPayment)
                        <div style="background:#fff7ed; border:1.5px solid #fed7aa; border-radius:12px; padding:1.2rem; margin-top: 14px;">
                            <div class="d-flex align-items-center gap-2 mb-1" style="color:#c2410c; font-weight:700; font-size:14px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-hourglass-split" viewBox="0 0 16 16" style="margin-right: 4px;">
                                    <path d="M2.5 15a.5.5 0 1 1 0-1h1v-1a4.5 4.5 0 0 1 2.557-4.06c.29-.139.443-.377.443-.59v-.7c0-.213-.154-.451-.443-.59A4.5 4.5 0 0 1 3.5 3V2h-1a.5.5 0 0 1 0-1h11a.5.5 0 0 1 0 1h-1v1a4.5 4.5 0 0 1-2.557 4.06c-.29.139-.443.377-.443.59v.7c0 .213.154.451.443.59A4.5 4.5 0 0 1 12.5 13v1h1a.5.5 0 0 1 0 1zm2-13v1c0 .537.12 1.045.337 1.5h6.326c.216-.455.337-.963.337-1.5V2zm3 6.35c0 .07.135.254.412.386a3.5 3.5 0 0 0 1.951.386v-.772A3.5 3.5 0 0 0 7.828 8.35a.4.4 0 0 0-.412-.386V8.35zm0 1.3v.772a3.5 3.5 0 0 0 1.951-.386.4.4 0 0 0 .412-.386v-.386a.4.4 0 0 0-.412.386 3.5 3.5 0 0 0-1.951.386z"/>
                                </svg>
                                <strong>Bukti Pembayaran Sedang Direview</strong>
                            </div>
                            <p class="mb-0 small text-muted" style="font-size:12px; margin-top:6px;">Bukti pembayaran Anda telah dikirim pada {{ $existingPayment->created_at->format('d M Y H:i') }}. Mohon tunggu konfirmasi admin.</p>
                        </div>
                    @else
                        @if(!$isFree)
                                    <div class="mt-3">
                                        <div class="form-label-custom" style="margin-bottom:8px; font-weight:600;">Payment Method</div>
                                        <div style="display:flex; flex-direction:column; gap:10px;">
                                            @if(isset($event) && $event->accept_online_payment)
                                            <label id="method-midtrans-label" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;border:2px solid #e0e0e0;background:#fff;cursor:pointer;font-size:14px;font-weight:600;color:#374151;transition:all .2s;">
                                                <input type="radio" name="payment_method" value="midtrans" id="method-midtrans" style="accent-color:#4f46e5;" {{ !$event->accept_manual_transfer ? 'checked' : '' }}>
                                                <span>Online Payment</span>
                                                <span style="margin-left:auto;font-size:11px;font-weight:400;color:#6b7280;">Instant process</span>
                                            </label>
                                            @endif
                                            @if(isset($event) && $event->accept_manual_transfer)
                                            <label id="method-transfer-label" style="display:flex;align-items:center;gap:12px;padding:12px 16px;border-radius:10px;border:2px solid #059669;background:#ecfdf5;cursor:pointer;font-size:14px;font-weight:600;color:#047857;transition:all .2s;">
                                                <input type="radio" name="payment_method" value="transfer" id="method-transfer" style="accent-color:#059669;" {{ $event->accept_manual_transfer ? 'checked' : '' }}>
                                                <span>Bank Account Transfer</span>
                                                <span style="margin-left:auto;font-size:11px;font-weight:400;color:#6b7280;">Upload proof • pending verification</span>
                                            </label>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Transfer proof upload — shown only when Transfer Rekening selected --}}
                                    <div id="transferProofSection" class="mt-3" style="display:none;">
                                        {{-- Bank account info --}}
                                        <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:10px;padding:12px 14px;margin-bottom:12px;">
                                            <div style="font-size:12px;font-weight:600;color:#15803d;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" class="bi bi-bank" viewBox="0 0 16 16" style="margin-right:4px;">
                                                    <path d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.379l.5 2A.5.5 0 0 1 15.5 16h-15a.5.5 0 0 1-.485-.621l.5-2A.5.5 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89zM3.777 3h8.447L8 1zM2 6v7h1V6zm2 0v7h2.5V6zm3.5 0v7h1V6zm2 0v7H12V6zM13 6v7h1V6zm2-1V4H1v1zm-.39 9H1.39l-.25 1h13.72z"/>
                                                </svg>
                                                Transfer Purpose
                                            </div>
                                            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap;">
                                                <div>
                                                    <div style="font-size:15px;font-weight:700;color:#15803d;letter-spacing:.5px;">{{ isset($event) ? $event->bank_account_number : '' }}</div>
                                                    <div style="font-size:12px;color:#374151;margin-top:2px;"><strong>{{ isset($event) ? $event->bank_name : '' }}</strong> &nbsp;·&nbsp; {{ isset($event) ? $event->bank_account_holder : '' }}</div>
                                                </div>
                                                <button type="button" onclick="navigator.clipboard.writeText('{{ isset($event) ? $event->bank_account_number : '' }}');this.textContent='✓ Copied';setTimeout(()=>this.textContent='Copy',1500);"
                                                    style="font-size:11px;padding:4px 10px;border-radius:6px;border:1px solid #16a34a;background:#fff;color:#16a34a;cursor:pointer;font-weight:600;">
                                                    Copy
                                                </button>
                                            </div>
                                        </div>

                                        <label class="form-label-custom" style="font-weight:600;">Transfer Proof <span style="color:#ef4444;">*</span></label>
                                        <input type="file" name="payment_proof" id="paymentProofInput"
                                            class="form-control-custom"
                                            accept="image/jpeg,image/png,image/jpg,image/webp"
                                            style="padding:8px;">
                                        <div class="form-text small text-muted mt-1">Format: JPG, PNG, WebP. Maks <strong>1 MB</strong>.</div>
                                        <div id="proofSizeError" class="form-text small text-danger mt-1" style="display:none;">The file size exceeds 1 MB. Please select a smaller file.</div>
                                    </div>
                                @endif

                        </div>

                        @if(isset($event) && $isFree)
                            <div class="mt-3" id="manualPaySection">
                                <button type="submit" class="btn-pay" id="freeRegBtn">Register Now!</button>
                            </div>
                        @endif

                        @if(!$isFree)
                            <div id="midtransSection">
                                <button type="button" id="midtransPayBtn" class="btn-pay" style="margin-top:0;">Pay Now!</button>
                                <div class="small text-muted mt-2">Payment will be verified automatically after success.</div>
                            </div>
                            <div id="transferSection" style="display:none; margin-top:0;">
                                <button type="button" id="transferPayBtn" class="btn-pay"
                                    style="background:#059669;"
                                    data-bs-toggle="modal" data-bs-target="#transferConfirmModal">
                                    Submit Transfer Proof
                                </button>
                                <div class="small text-muted mt-2">Payment will be verified by admin within 1×24 hours.</div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </form>

    <!-- Transfer Confirmation Modal -->
    <div class="modal fade" id="transferConfirmModal" tabindex="-1" aria-labelledby="transferConfirmLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
            <div class="modal-content" style="border-radius:16px;overflow:hidden;border:none;">
                <div class="modal-header" style="background:#f0fdf4;border-bottom:1px solid #bbf7d0;padding:20px 24px 16px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#16a34a" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>
                            </svg>
                        </div>
                        <h5 class="modal-title mb-0 fw-bold" id="transferConfirmLabel" style="color:#15803d;">Confirm Payment</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding:20px 24px;">
                    <p style="color:#374151;font-size:14px;line-height:1.6;margin-bottom:16px;">
                        Please ensure that your payment matches the total amount listed on the previous page.
                        Incorrect payment amounts may delay the verification process.
                    </p>
                    <p style="color:#374151;font-size:14px;line-height:1.6;margin-bottom:20px;">
                        Please check carefully before proceeding to payment.
                    </p>
                    <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;padding:12px 14px;border-radius:10px;border:1.5px solid #d1d5db;background:#f9fafb;">
                        <input type="checkbox" id="transferConfirmCheck" style="margin-top:2px;accent-color:#059669;width:16px;height:16px;flex-shrink:0;">
                        <span style="font-size:13px;color:#374151;line-height:1.5;">
                            I declare that I have checked and confirmed that the payment amount matches the total listed.
                        </span>
                    </label>
                </div>
                <div class="modal-footer" style="padding:12px 24px 20px;border-top:none;gap:10px;">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                        style="border-radius:10px;padding:10px 20px;font-weight:600;flex:1;">
                        Cancel
                    </button>
                    <button type="button" id="transferConfirmProceed"
                        style="flex:2;border-radius:10px;padding:10px 20px;font-weight:600;background:#059669;color:#fff;border:none;opacity:0.5;cursor:not-allowed;transition:all .2s;"
                        disabled>
                        Continue Payment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const check   = document.getElementById('transferConfirmCheck');
        const proceed = document.getElementById('transferConfirmProceed');
        const modal   = document.getElementById('transferConfirmModal');

        if (check && proceed) {
            check.addEventListener('change', function() {
                proceed.disabled = !this.checked;
                proceed.style.opacity = this.checked ? '1' : '0.5';
                proceed.style.cursor  = this.checked ? 'pointer' : 'not-allowed';
            });
        }

        if (proceed) {
            proceed.addEventListener('click', function() {
                // Close modal and submit form
                if (window.bootstrap) {
                    bootstrap.Modal.getInstance(modal)?.hide();
                }
                // Small delay to let modal close before submit
                setTimeout(function() {
                    const form = document.getElementById('paymentForm');
                    if (form) form.submit();
                }, 200);
            });
        }

        // Reset checkbox when modal closes
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                if (check) { check.checked = false; }
                if (proceed) {
                    proceed.disabled = true;
                    proceed.style.opacity = '0.5';
                    proceed.style.cursor  = 'not-allowed';
                }
            });
        }
    });
    </script>

    <!-- Midtrans Success Modal (Checklist hijau) -->
    <div class="modal fade" id="midtransSuccessModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-body p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center mb-3"
                         style="width: 64px; height: 64px; background: rgba(22, 163, 74, 0.12); border-radius: 50%;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#16A34A" viewBox="0 0 16 16" aria-hidden="true">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.97 11.03a.75.75 0 0 0 1.07 0l3.992-3.992a.75.75 0 0 0-1.06-1.06L7.5 9.44 5.53 7.47a.75.75 0 0 0-1.06 1.06z"/>
                        </svg>
                    </div>
                    <h5 class="mb-2" style="font-weight: 700;">Success!</h5>
                    <div id="midtransSuccessModalText" class="text-muted" style="font-size: 14px;"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal" style="border-radius: 10px; padding: 10px 18px; font-weight: 600;">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Generic Notification Modal -->
    <div class="modal fade" id="appNotificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-body p-4 text-center">
                    <div id="appNotifyIcon" class="mb-3"></div>
                    <h5 id="appNotifyTitle" class="mb-2" style="font-weight: 700;">Information</h5>
                    <div id="appNotifyMessage" class="text-muted" style="font-size: 14px;"></div>
                    <div class="mt-4">
                        <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal" style="border-radius: 10px; padding: 10px; font-weight: 600;">Got it</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        function showAppNotify(message, type = 'info') {
            const modalEl = document.getElementById('appNotificationModal');
            const titleEl = document.getElementById('appNotifyTitle');
            const messageEl = document.getElementById('appNotifyMessage');
            const iconEl = document.getElementById('appNotifyIcon');
            if (!modalEl || !messageEl) return;
            messageEl.textContent = message;
            let iconHtml = '';
            let titleText = 'Information';
            let btnClass = 'btn-primary';
            if (type === 'error') {
                iconHtml = '<div class="d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: rgba(239, 68, 68, 0.1); border-radius: 50%;"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#EF4444" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg></div>';
                titleText = 'Oops!';
                btnClass = 'btn-danger';
            } else if (type === 'success') {
                iconHtml = '<div class="d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: rgba(22, 163, 74, 0.1); border-radius: 50%;"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#16A34A" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.97 11.03a.75.75 0 0 0 1.07 0l3.992-3.992a.75.75 0 0 0-1.06-1.06L7.5 9.44 5.53 7.47a.75.75 0 0 0-1.06 1.06z"/></svg></div>';
                titleText = 'Success';
                btnClass = 'btn-success';
            } else {
                iconHtml = '<div class="d-inline-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: rgba(59, 130, 246, 0.1); border-radius: 50%;"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#3B82F6" viewBox="0 0 16 16"><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg></div>';
            }
            if (iconEl) iconEl.innerHTML = iconHtml;
            if (titleEl) titleEl.textContent = titleText;
            const btn = modalEl.querySelector('.btn');
            if (btn) btn.className = 'btn w-100 ' + btnClass;
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        const form = document.getElementById('paymentForm');
        if(!form) return;

        const isStage2 = @json($isStage2 ?? false);
        const snapTokenUrl = isStage2 
            ? @json(isset($event) ? route('events.payment.stage2.midtrans', $event) : '')
            : @json(isset($event) ? route('payment.snap-token', $event->id) : '');
        const pendingOrderUrl = isStage2 
            ? @json(isset($event) ? route('events.payment.stage2.pending-order', $event) : '')
            : @json(isset($event) ? route('payment.pending-order', $event->id) : '');
        const finalizeUrl = isStage2 
            ? @json(isset($event) ? route('events.payment.stage2.settle', $event) : '')
            : @json(isset($event) ? route('payment.finalize', $event->id) : '');
        const redirectUrl = isStage2 
            ? @json(isset($event) ? route('events.registered.detail', $event) : route('dashboard'))
            : @json(isset($event) ? route('events.show', $event->id) : route('dashboard'));
        const eventTitle = @json(isset($event) ? ($event->title ?? 'Event') : 'Event');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const checkReferralUrl = @json(isset($event) ? route('payment.check-referral', $event->id) : '');
        let cachedPending = null;

        const fullName = form.querySelector('input[name="full_name"]');
        const wa = form.querySelector('input[name="whatsapp"]');
        const universityInput = form.querySelector('input[name="university_origin"]');
        const studyProgramInput = form.querySelector('input[name="study_program"]');
        const positionInput = form.querySelector('input[name="position"]');
        const showQrisBtn = document.getElementById('showQrisBtn');
        const paymentProofInput = document.getElementById('paymentProofInput');
        const referralInput = form.querySelector('input[name="referral_code"]');
        const referralMessageEl = document.getElementById('referralMessage');
        const eventPriceEl = document.getElementById('eventPriceText');
        const currentUserReferral = @json(auth()->user()->referral_code ?? '');
        const REFERRAL_RATE = 0.10;

        const voucherInput = document.getElementById('voucherCodeInput');
        const voucherMessageEl = document.getElementById('voucherMessage');
        let voucherState = voucherInput ? 'idle' : 'disabled';
        let referralState = referralInput ? 'idle' : 'disabled';
        let voucherTimer = null;
        let currentDiscount = 0;

        function formatRupiah(val){
            try{
                return (new Intl.NumberFormat('id-ID')).format(Math.round(val));
            }catch(e){
                return String(Math.round(val));
            }
        }

        const isHybrid = eventPriceEl?.dataset.isHybrid === 'true';
        const priceOffline = parseInt(eventPriceEl?.dataset.priceOffline || '0', 10);
        const priceOnline  = parseInt(eventPriceEl?.dataset.priceOnline  || '0', 10);
        const priceOfflineRaw = parseInt(eventPriceEl?.dataset.priceOfflineRaw || '0', 10);
        const priceOnlineRaw  = parseInt(eventPriceEl?.dataset.priceOnlineRaw  || '0', 10);
        const attendanceTypeInput = document.getElementById('attendanceTypeInput');
        const labelOffline = document.getElementById('label-offline');
        const labelOnline  = document.getElementById('label-online');

        let referralFinalAmount = parseFloat(eventPriceEl?.dataset.baseAmount || '0') || 0;

        function getReferralCode() {
            return referralInput ? String(referralInput.value || '').trim() : '';
        }

        function getVoucherCode() {
            return voucherInput ? String(voucherInput.value || '').trim() : '';
        }

        function setHybridPrice(type) {
            if (!isHybrid || !eventPriceEl) return;
            const price = type === 'online' ? priceOnline : priceOffline;
            const raw   = type === 'online' ? priceOnlineRaw : priceOfflineRaw;
            
            eventPriceEl.dataset.baseAmount = price;
            
            if (attendanceTypeInput) attendanceTypeInput.value = type;
            
            if (labelOffline && labelOnline) {
                if (type === 'offline') {
                    labelOffline.style.border = '2px solid #1565c0';
                    labelOffline.style.background = '#e8f4fd';
                    labelOffline.style.color = '#1565c0';
                    labelOnline.style.border = '2px solid #e0e0e0';
                    labelOnline.style.background = '#fff';
                    labelOnline.style.color = '#555';
                } else {
                    labelOnline.style.border = '2px solid #c62828';
                    labelOnline.style.background = '#fce4ec';
                    labelOnline.style.color = '#c62828';
                    labelOffline.style.border = '2px solid #e0e0e0';
                    labelOffline.style.background = '#fff';
                    labelOffline.style.color = '#555';
                }
            }
            
            updateReferralUI();
        }

        if (isHybrid) {
            document.querySelectorAll('input[name="attendance_type_radio"]').forEach(function(radio) {
                radio.addEventListener('change', function() {
                    setHybridPrice(this.value);
                });
            });
            setHybridPrice('offline');
        }

        function updateTotalDisplay() {
            if (!eventPriceEl) return;
            const base = parseFloat(eventPriceEl.dataset.baseAmount || '0') || 0;
            let finalAmount = referralFinalAmount || base;
            if (voucherState === 'valid' && currentDiscount > 0) {
                finalAmount = Math.max(0, finalAmount - currentDiscount);
            }

            if (finalAmount === 0) {
                eventPriceEl.textContent = 'FREE';
            } else {
                eventPriceEl.textContent = 'Rp' + formatRupiah(finalAmount);
            }

            if (midtransPayBtn) {
                if (finalAmount === 0) {
                    midtransPayBtn.textContent = 'Register Now (Free)';
                } else {
                    var pending = cachedPending;
                    if (pending && pending.pending) {
                        midtransPayBtn.textContent = 'Lanjutkan pembayaran Midtrans';
                    } else {
                        midtransPayBtn.textContent = 'Pay Now!';
                    }
                }
            }
        }

        let _referralTimer = null;
        async function validateReferralServer(code){
            if(!checkReferralUrl) return null;
            try{
                const url = new URL(checkReferralUrl, window.location.origin);
                url.searchParams.set('code', code);
                const res = await fetch(url.toString(), { method: 'GET', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if(!res.ok) return null;
                return await res.json();
            } catch(e){ return null; }
        }

        async function validateVoucherServer(code) {
            if(!code) return null;
            try {
                const url = new URL(@json(route('events.check-voucher', $event->id ?? 0)), window.location.origin);
                url.searchParams.set('code', code);
                
                const ref = getReferralCode();
                if (ref !== '' && referralState === 'valid') {
                    url.searchParams.set('referral_code', ref);
                }

                const attendanceTypeEl = document.getElementById('attendanceTypeInput');
                if(attendanceTypeEl && attendanceTypeEl.value) {
                    url.searchParams.set('attendance_type', attendanceTypeEl.value);
                }

                const res = await fetch(url.toString(), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                if(!res.ok) return null;
                return await res.json();
            } catch(e) {
                return null;
            }
        }

        function updateReferralUI(){
            if(!eventPriceEl) return;
            const base = parseFloat(eventPriceEl.dataset.baseAmount || '0') || 0;
            const code = getReferralCode();

            if(code !== '' && currentUserReferral && code.toUpperCase() === String(currentUserReferral).toUpperCase()){
                if(referralMessageEl){ referralMessageEl.style.display = ''; referralMessageEl.classList.remove('text-muted', 'text-success'); referralMessageEl.classList.add('text-danger'); referralMessageEl.textContent = 'You cannot use your own referral code.'; }
                referralState = 'invalid';
                referralFinalAmount = base;
                if (getVoucherCode() !== '') {
                    scheduleVoucherValidation();
                } else {
                    updateTotalDisplay();
                    validate();
                }
                return;
            }

            if(code === ''){
                referralState = 'idle';
                referralFinalAmount = base;
                if(referralMessageEl){ referralMessageEl.style.display = 'none'; referralMessageEl.textContent = ''; referralMessageEl.classList.remove('text-success', 'text-danger', 'text-muted'); }
                if (getVoucherCode() !== '') {
                    scheduleVoucherValidation();
                } else {
                    updateTotalDisplay();
                    validate();
                }
                return;
            }

            if(_referralTimer) clearTimeout(_referralTimer);
            _referralTimer = setTimeout(async function(){
                const data = await validateReferralServer(code);
                if(!data){
                    referralState = 'invalid';
                    referralFinalAmount = base;
                    if(referralMessageEl){ referralMessageEl.style.display = ''; referralMessageEl.classList.remove('text-muted', 'text-success'); referralMessageEl.classList.add('text-danger'); referralMessageEl.textContent = 'Failed to verify code. Please try again.'; }
                    if (getVoucherCode() !== '') {
                        scheduleVoucherValidation();
                    } else {
                        updateTotalDisplay();
                        validate();
                    }
                    return;
                }

                if(data.valid){
                    referralState = 'valid';
                    const rate = parseFloat(data.discount_rate) || 0.0;
                    referralFinalAmount = base * (1 - rate);
                    if(referralMessageEl){ referralMessageEl.style.display = ''; referralMessageEl.classList.remove('text-danger', 'text-muted'); referralMessageEl.classList.add('text-success'); referralMessageEl.textContent = data.message || 'Referral code is valid.'; }
                } else {
                    referralState = 'invalid';
                    referralFinalAmount = base;
                    if(referralMessageEl){ referralMessageEl.style.display = ''; referralMessageEl.classList.remove('text-muted', 'text-success'); referralMessageEl.classList.add('text-danger'); referralMessageEl.textContent = data.message || 'Invalid referral code.'; }
                }
                
                if (getVoucherCode() !== '') {
                    scheduleVoucherValidation();
                } else {
                    updateTotalDisplay();
                    validate();
                }
            }, 450);
        }

        function updateVoucherUIFromResult(data) {
            const code = getVoucherCode();

            if (code === '') {
                voucherState = 'idle';
                currentDiscount = 0;
                if (voucherMessageEl) {
                    voucherMessageEl.style.display = 'none';
                    voucherMessageEl.textContent = '';
                }
                updateTotalDisplay();
                validate();
                return;
            }

            if (!data) {
                voucherState = 'invalid';
                currentDiscount = 0;
                if (voucherMessageEl) {
                    voucherMessageEl.style.display = '';
                    voucherMessageEl.classList.remove('text-muted');
                    voucherMessageEl.classList.add('text-danger');
                    voucherMessageEl.textContent = 'Gagal memeriksa kode voucher. Coba lagi.';
                }
                updateTotalDisplay();
                validate();
                return;
            }

            if (data.valid) {
                voucherState = 'valid';
                currentDiscount = data.discount || 0;
                if (voucherMessageEl) {
                    voucherMessageEl.style.display = '';
                    voucherMessageEl.classList.remove('text-danger');
                    voucherMessageEl.classList.add('text-muted');
                    voucherMessageEl.textContent = data.message || 'Voucher berhasil diterapkan.';
                }
            } else {
                voucherState = 'invalid';
                currentDiscount = 0;
                if (voucherMessageEl) {
                    voucherMessageEl.style.display = '';
                    voucherMessageEl.classList.remove('text-muted');
                    voucherMessageEl.classList.add('text-danger');
                    voucherMessageEl.textContent = data.message || 'Voucher tidak valid.';
                }
            }

            updateTotalDisplay();
            validate();
        }

        function scheduleVoucherValidation() {
            if (!voucherInput) return;
            if (voucherTimer) {
                clearTimeout(voucherTimer);
            }

            const code = getVoucherCode();

            if (code === '') {
                updateVoucherUIFromResult({ valid: false, message: '' });
                return;
            }

            voucherState = 'checking';
            if (voucherMessageEl) {
                voucherMessageEl.style.display = '';
                voucherMessageEl.classList.remove('text-danger');
                voucherMessageEl.classList.add('text-muted');
                voucherMessageEl.textContent = 'Memeriksa kode voucher...';
            }
            validate();

            voucherTimer = setTimeout(async function() {
                const data = await validateVoucherServer(code);
                if (code !== getVoucherCode()) {
                    return;
                }
                updateVoucherUIFromResult(data);
            }, 400);
        }

        const payNowBtn = document.getElementById('payNowBtn');
        const manualPaySection = document.getElementById('manualPaySection');
        const midtransSection = document.getElementById('midtransSection');
        const midtransPayBtn = document.getElementById('midtransPayBtn');
        const isFree = @json(isset($event) ? ((int)($finalPrice ?? 0) === 0) : false);

        let pendingProofSubmit = false;

        // Payment method toggle
        const methodMidtrans  = document.getElementById('method-midtrans');
        const methodTransfer  = document.getElementById('method-transfer');
        const midtransLbl     = document.getElementById('method-midtrans-label');
        const transferLbl     = document.getElementById('method-transfer-label');
        const transferSection = document.getElementById('transferSection');
        const transferProofSection = document.getElementById('transferProofSection');
        const proofInput      = document.getElementById('paymentProofInput');
        const proofSizeError  = document.getElementById('proofSizeError');

        function getSelectedMethod(){
            if (methodTransfer && methodTransfer.checked) return 'transfer';
            return 'midtrans';
        }

        function syncMethodUI() {
            const m = getSelectedMethod();
            const isMidtrans = m === 'midtrans';

            // Style active label
            if (midtransLbl) {
                midtransLbl.style.border = isMidtrans ? '2px solid #4f46e5' : '2px solid #e0e0e0';
                midtransLbl.style.background = isMidtrans ? '#eef2ff' : '#fff';
                midtransLbl.style.color = isMidtrans ? '#4338ca' : '#374151';
            }
            if (transferLbl) {
                transferLbl.style.border = !isMidtrans ? '2px solid #059669' : '2px solid #e0e0e0';
                transferLbl.style.background = !isMidtrans ? '#ecfdf5' : '#fff';
                transferLbl.style.color = !isMidtrans ? '#047857' : '#374151';
            }

            // Show/hide sections
            const midSec = document.getElementById('midtransSection');
            if (midSec) midSec.style.display = isMidtrans ? '' : 'none';
            if (transferSection) transferSection.style.display = isMidtrans ? 'none' : '';
            if (transferProofSection) transferProofSection.style.display = isMidtrans ? 'none' : '';

            validate();
        }

        if (methodMidtrans) methodMidtrans.addEventListener('change', syncMethodUI);
        if (methodTransfer)  methodTransfer.addEventListener('change', syncMethodUI);

        // Init payment method UI — transfer is default (midtrans disabled)
        syncMethodUI();

        // Validate proof file size (max 1MB)
        if (proofInput) {
            proofInput.addEventListener('change', function() {
                if (this.files[0] && this.files[0].size > 1 * 1024 * 1024) {
                    proofSizeError && (proofSizeError.style.display = '');
                    this.value = '';
                } else {
                    proofSizeError && (proofSizeError.style.display = 'none');
                }
                validate();
            });
        }
        function isValidPhone(val){ return /^[0-9]{8,15}$/.test(String(val || '').trim()); }

        function validate(){
            const nameOk = fullName && fullName.value.trim().length >= 3;
            const waOk = wa && isValidPhone(wa.value);
            const univOk = !universityInput || universityInput.value.trim().length >= 2;
            const studyOk = !studyProgramInput || studyProgramInput.value.trim().length >= 2;
            const posOk = !positionInput || positionInput.value.trim().length >= 2;
            
            let refOk = true;
            if (referralInput && referralInput.value.trim() !== '') {
                refOk = (referralState === 'valid');
            }

            let vchOk = true;
            if (voucherInput && getVoucherCode() !== '') {
                vchOk = (voucherState === 'valid');
            }

            const allOk = nameOk && waOk && univOk && studyOk && posOk && refOk && vchOk;

            if(isFree){
                const freeBtn = document.getElementById('freeRegBtn');
                if(freeBtn){
                    freeBtn.disabled = !allOk;
                    freeBtn.style.opacity = allOk ? '1' : '0.5';
                }
                return allOk;
            }

            const okMidtrans = allOk;

            if(midtransPayBtn){
                midtransPayBtn.disabled = !okMidtrans;
                midtransPayBtn.style.opacity = okMidtrans ? '1' : '0.5';
            }

            const method = getSelectedMethod();
            const transferPayBtn = document.getElementById('transferPayBtn');
            const hasProof = proofInput && proofInput.files && proofInput.files.length > 0
                             && proofInput.files[0].size <= 1 * 1024 * 1024;
            if (transferPayBtn) {
                const okTransfer = allOk && hasProof;
                transferPayBtn.disabled = !(method === 'transfer' && okTransfer);
                transferPayBtn.style.opacity = (method === 'transfer' && okTransfer) ? '1' : '0.5';
            }

            return okMidtrans;
        }

        ['input','change','keyup','blur'].forEach(evt => {
            if(fullName) fullName.addEventListener(evt, validate);
            if(wa) wa.addEventListener(evt, validate);
            if(universityInput) universityInput.addEventListener(evt, validate);
            if(studyProgramInput) studyProgramInput.addEventListener(evt, validate);
            if(positionInput) positionInput.addEventListener(evt, validate);
            if(referralInput) referralInput.addEventListener(evt, updateReferralUI);
        });

        // Init payment method UI
        syncMethodUI();

        if (voucherInput) {
            voucherInput.addEventListener('input', scheduleVoucherValidation);
            voucherInput.addEventListener('blur', scheduleVoucherValidation);
        }

        form.addEventListener('submit', function(e){
            if(isFree){
                e.preventDefault();
                if(!validate()) return;
                
                const freeBtn = document.getElementById('freeRegBtn');
                if(freeBtn) {
                    freeBtn.disabled = true;
                    freeBtn.textContent = 'Processing...';
                }

                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success){
                        showMidtransSuccessModal(data.message);
                        setTimeout(function(){
                            window.location.href = data.redirect || redirectUrl;
                        }, 1600);
                    } else {
                        showAppNotify(data.message || 'Registration failed.', 'error');
                        if(freeBtn) {
                            freeBtn.disabled = false;
                            freeBtn.textContent = 'Register Free';
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    showAppNotify('A system error occurred.', 'error');
                    if(freeBtn) {
                        freeBtn.disabled = false;
                        freeBtn.textContent = 'Register Free';
                    }
                });
                return;
            }
            e.preventDefault();
        });

        updateReferralUI();
        validate();

        function showMidtransSuccessModal(customMsg = null){
            const text = document.getElementById('midtransSuccessModalText');
            if (text) {
                text.textContent = customMsg || ('You have successfully registered for the event "' + eventTitle + '".');
            }
            const modalEl = document.getElementById('midtransSuccessModal');
            if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                const m = window.bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false });
                m.show();
            }
        }

        async function fetchPendingOrder(){
            if(!pendingOrderUrl) return null;
            try{
                const res = await fetch(pendingOrderUrl, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if(!res.ok) return null;
                return data;
            } catch(_e){
                return null;
            }
        }

        async function ensurePendingLabel(){
            if(!midtransPayBtn || !pendingOrderUrl) return;
            const pending = await fetchPendingOrder();
            cachedPending = pending;
            if(pending && pending.pending && pending.order_id){
                midtransPayBtn.textContent = 'Lanjutkan pembayaran Midtrans';

                if (pending.whatsapp_number && wa && (!wa.value || wa.value.trim() === '')) {
                    const raw = String(pending.whatsapp_number).trim();
                    if (raw.startsWith('+')) {
                        const m = raw.match(/^\+(\d{1,3})(.*)$/);
                        if (m) {
                            const rest = String(m[2] || '').replace(/\D/g, '');
                            wa.value = rest;
                        }
                    } else {
                        wa.value = raw.replace(/\D/g, '');
                    }
                }
                validate();
            } else if (pending && pending.needs_force_new) {
                cachedPending = null;
                midtransPayBtn.textContent = 'Pay Now';
                validate();
            }
        }

        async function postFinalize(orderId){
            if(!finalizeUrl) return null;
            const res = await fetch(finalizeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ order_id: orderId })
            });
            return await res.json();
        }

        async function startMidtrans(){
            if(typeof window.snap === 'undefined'){
                showAppNotify('Midtrans belum siap. Pastikan client key sudah diset.', 'error');
                return;
            }

            const forceNewFromQuery = (new URLSearchParams(window.location.search)).get('force_new') === '1';
            const forceNewFromExpired = !!(cachedPending && cachedPending.needs_force_new);

            validate();
            if(midtransPayBtn && midtransPayBtn.disabled){
                showAppNotify('Please complete your details before paying.', 'error');
                return;
            }

            const dialVal = '+62';
            const waVal = (wa ? wa.value : '').trim();
            const voucherVal = getVoucherCode();

            async function getOrCreateSnapToken(forceNew){
                const pending = cachedPending || await fetchPendingOrder();
                cachedPending = pending;
                const pendingReferral = pending && pending.referral_code ? String(pending.referral_code).trim() : '';
                const pendingVoucher = pending && pending.metadata && pending.metadata.voucher_code ? String(pending.metadata.voucher_code).trim() : '';
                
                if(!forceNew && pending && pending.pending && pending.order_id && pending.snap_token && pendingReferral === (referralInput ? referralInput.value.trim() : '') && pendingVoucher === voucherVal){
                    return { snap_token: pending.snap_token, order_id: pending.order_id };
                }

                const url = new URL(snapTokenUrl, window.location.origin);
                const method = isStage2 ? 'POST' : 'GET';
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                };
                
                let body = null;
                if (method === 'POST') {
                    body = JSON.stringify({
                        dial_code: dialVal,
                        whatsapp: waVal,
                        referral_code: referralInput ? referralInput.value.trim() : '',
                        voucher_code: voucherVal,
                        force_new: forceNew ? '1' : '0',
                        attendance_type: document.getElementById('attendanceTypeInput')?.value || ''
                    });
                } else {
                    if(dialVal) url.searchParams.set('dial_code', dialVal);
                    if(waVal) url.searchParams.set('whatsapp', waVal);
                    if(referralInput && referralInput.value && referralInput.value.trim() !== '') url.searchParams.set('referral_code', referralInput.value.trim());
                    if(voucherVal !== '') url.searchParams.set('voucher_code', voucherVal);
                    if(forceNew) url.searchParams.set('force_new', '1');
                    
                    const attendanceTypeEl = document.getElementById('attendanceTypeInput');
                    if(attendanceTypeEl && attendanceTypeEl.value) url.searchParams.set('attendance_type', attendanceTypeEl.value);
                }

                const fetchOptions = {
                    method: method,
                    headers: headers,
                    credentials: 'same-origin'
                };
                if (body) {
                    fetchOptions.body = body;
                }

                const res = await fetch(url.toString(), fetchOptions);
                const data = await res.json();
                if(!res.ok || !data || (!data.snap_token && !data.redirect_url)){
                    throw new Error((data && (data.message || data.error)) ? (data.message || data.error) : 'Failed membuat token Midtrans');
                }
                return data;
            }

            midtransPayBtn.disabled = true;
            const originalText = midtransPayBtn.textContent;
            midtransPayBtn.textContent = 'Processing...';

            try{
                let data;
                if (forceNewFromQuery || forceNewFromExpired) {
                    cachedPending = null;
                    data = await getOrCreateSnapToken(true);
                } else {
                    try {
                        data = await getOrCreateSnapToken(false);
                    } catch(e) {
                        data = await getOrCreateSnapToken(true);
                    }
                }

                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                    return;
                }

                if(data && data.snap_token && originalText !== 'Lanjutkan pembayaran Midtrans'){
                    try { await ensurePendingLabel(); } catch(_e) {}
                }

                window.snap.pay(data.snap_token, {
                    onSuccess: async function(){
                        clearInterval(window._snapPollTimer);
                        try {
                            await postFinalize(data.order_id);
                        } catch(_e) {}
                        showMidtransSuccessModal();
                        setTimeout(function(){
                            window.location.href = redirectUrl;
                        }, 1400);
                    },
                    onPending: async function(){
                        clearInterval(window._snapPollTimer);
                        try { await postFinalize(data.order_id); } catch(_e) {}
                        showAppNotify('Payment pending. Please complete the paybayaran di Midtrans.', 'info');
                        cachedPending = { pending: true, order_id: data.order_id, snap_token: data.snap_token };
                        if(midtransPayBtn) midtransPayBtn.textContent = 'Lanjutkan pembayaran Midtrans';
                    },
                    onError: function(){
                        clearInterval(window._snapPollTimer);
                        showAppNotify('Payment failed. Please try again.', 'error');
                    },
                    onClose: async function(){
                        clearInterval(window._snapPollTimer);
                        midtransPayBtn.disabled = true;
                        midtransPayBtn.textContent = 'Memeriksa status...';
                        try {
                            const result = await postFinalize(data.order_id);
                            if (result && result.status === 'settled') {
                                showMidtransSuccessModal();
                                setTimeout(function(){
                                    window.location.href = redirectUrl;
                                }, 1400);
                                return;
                            }
                            if (result && (result.status === 'expired' || result.status === 'rejected')) {
                                cachedPending = null;
                                window.location.href = window.location.pathname + '?force_new=1';
                                return;
                            }
                        } catch(_e) {}
                        midtransPayBtn.disabled = false;
                        midtransPayBtn.textContent = 'Lanjutkan pembayaran Midtrans';
                        validate();
                    }
                });

                clearInterval(window._snapPollTimer);
                window._snapPollTimer = setInterval(async function() {
                    try {
                        const result = await postFinalize(data.order_id);
                        if (!result) return;
                        if (result.status === 'settled') {
                            clearInterval(window._snapPollTimer);
                            if (window.snap && typeof window.snap.hide === 'function') window.snap.hide();
                            showMidtransSuccessModal();
                            setTimeout(function(){
                                window.location.href = redirectUrl;
                            }, 1400);
                        } else if (result.status === 'expired' || result.status === 'rejected') {
                            clearInterval(window._snapPollTimer);
                            cachedPending = null;
                            if (window.snap && typeof window.snap.hide === 'function') window.snap.hide();
                            window.location.href = window.location.pathname + '?force_new=1';
                        }
                    } catch(_e) {}
                }, 5000);
            } catch(e){
                showAppNotify(String(e && e.message ? e.message : e), 'error');
            } finally {
                midtransPayBtn.disabled = false;
                if(cachedPending && cachedPending.pending && cachedPending.order_id){
                    midtransPayBtn.textContent = 'Lanjutkan pembayaran Midtrans';
                } else {
                    midtransPayBtn.textContent = originalText;
                }
                validate();
            }
        }

        if(midtransPayBtn){
            midtransPayBtn.addEventListener('click', function(e){
                e.preventDefault();
                startMidtrans();
            });
        }

        ensurePendingLabel();

        if ((new URLSearchParams(window.location.search)).get('force_new') === '1') {
            cachedPending = null;
            if (midtransPayBtn) {
                midtransPayBtn.textContent = 'Pay Now';
            }
        }
    });
    </script>
</body>
</html>
