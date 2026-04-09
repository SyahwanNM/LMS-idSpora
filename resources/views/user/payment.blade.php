@include("partials.navbar-after-login")
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

        /* Grid Layout */
        .grid-layout {
            display: grid;
            grid-template-columns: 1.4fr 0.8fr;
            gap: 16px; /* Gap antar kolom diperkecil */
            align-items: start;
        }

        /* Inputs yang lebih ramping */
        .form-label-custom { 
            font-weight: 600; 
            font-size: 13px; /* Font label diperkecil */
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

        /* Whatsapp & Voucher */
        .wa-group { display: flex; gap: 8px; }
        .wa-group select { width: 100px; }
        
        .voucher-header { display: flex; align-items: center; gap: 6px; font-weight: 600; font-size: 14px; margin-bottom: 8px; }
        .voucher-input-group { display: flex; gap: 8px; }
        .btn-check-voucher {
            background-color: #FACC15; color: #854D0E; border: none; padding: 0 14px;
            font-weight: 600; border-radius: 6px; font-size: 12px; white-space: nowrap;
        }

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
    <div class="payment-container" style="margin-bottom: 12px; margin-top: 85px;">
        <div style="font-size: 12px; color: #6B7280;">
            <a href="{{ route('dashboard') }}" style="text-decoration:none; color:inherit;">Home</a> / 
            <a href="{{ route('events.index') }}" style="text-decoration:none; color:inherit;">Event</a> / 
            <span style="color:#111827; font-weight:600;">Payment</span>
        </div>
    </div>

    <form id="paymentForm" method="POST" action="{{ route('payment.manual.register', $event->id ?? '') }}" enctype="multipart/form-data" novalidate>
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
                            <input type="email" class="form-control-custom" name="email" value="{{ auth()->user()->email ?? '' }}" readonly>
                        </div>

                        <div class="mb-custom">
                            <label class="form-label-custom" style="margin-bottom:0">Nama Lengkap</label>
                            <input type="text" class="form-control-custom" name="full_name" value="{{ auth()->user()->name ?? '' }}" placeholder="Nama sesuai sertifikat" required minlength="3">
                            <div class="warning-text" style="margin-top:4px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                </svg>
                                Nama Akan digunakan pada sertifikat
                            </div>
                        </div>

                        <div class="mb-custom" style="margin-bottom:0;"> <label class="form-label-custom">No Whatsapp</label>
                            <div class="wa-group">
                                <select class="form-select-custom" name="dial_code" required>
                                    <option value="">Kode</option>
                                    <option value="+62" selected>+62</option>
                                    <option value="+60">+60</option>
                                    <option value="+65">+65</option>
                                </select>
                                <input type="text" class="form-control-custom" name="whatsapp" placeholder="No Whatsapp" inputmode="numeric" required>
                            </div>
                        </div>
                         
                    </div>
                    <div class="card-custom">
                        <h3>
                            Voucher
                            @if(($event->is_reseller_event ?? false))
                                & Referral
                            @endif
                        </h3>
                        
                        <div class="voucher-header">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-patch-check" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M10.354 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7 8.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                <path d="m10.273 2.513-.921-.944.715-.698.622.637.89-.011a2.89 2.89 0 0 1 2.924 2.924l-.01.89.636.622a2.89 2.89 0 0 1 0 4.134l-.637.622.011.89a2.89 2.89 0 0 1-2.924 2.924l-.89-.01-.622.636a2.89 2.89 0 0 1-4.134 0l-.622-.637-.89.011a2.89 2.89 0 0 1-2.924-2.924l.01-.89-.636-.622a2.89 2.89 0 0 1 0-4.134l.637-.622-.011-.89a2.89 2.89 0 0 1 2.924-2.924l.89.01.622-.636a2.89 2.89 0 0 1 4.134 0l-.715.698a1.89 1.89 0 0 0-2.704 0l-.92.944-1.32-.016a1.89 1.89 0 0 0-1.911 1.912l.016 1.318-.944.921a1.89 1.89 0 0 0 0 2.704l.944.92-.016 1.32a1.89 1.89 0 0 0 1.912 1.911l1.318-.016.921.944a1.89 1.89 0 0 0 2.704 0l.92-.944 1.32.016a1.89 1.89 0 0 0 1.911-1.912l-.016-1.318.944-.921a1.89 1.89 0 0 0 0-2.704l-.944-.92.016-1.32a1.89 1.89 0 0 0-1.912-1.911z"/>
                            </svg>
                            Kode Voucher
                        </div>
                        <div class="voucher-input-group">
                            <input type="text" class="form-control-custom" placeholder="Input Kode Voucher" name="voucher_code">
                            <button type="button" class="btn-check-voucher">Cek</button>
                        </div>
                        @if(($event->is_reseller_event ?? false))
                        <div class="referral-event" style="margin-bottom:25px;margin-top:10px"> <label class="form-label-custom">Kode Referral</label>
                            <input type="text" class="form-control-custom" id="eventReferralCodeInput" name="referral_code" placeholder="Kode Referral (Opsional)">
                            <div id="referralFeedbackEvent" style="margin-top:6px; font-size:12px;"></div>
                        </div>
                        @endif
                    </div>
                    
                </div>

                <div class="right-col">
                    <div class="card-custom">
                        <h3>Checkout Detail Event</h3>
                        <div class="order-detail-content">
                            @if(isset($event))
                                <img src="{{ $event->image_url ?? asset('aset/event.png') }}" alt="{{ $event->title }}" class="order-img" onerror="this.src='{{ asset('aset/event.png') }}'">
                            @else
                                <img src="{{ asset('aset/event.png') }}" alt="Event" class="order-img">
                            @endif
                            <div class="order-info">
                                <h5>{{ isset($event)? $event->title : 'Event Title' }}</h5>
                                <p>IdSpora</p>
                                <div class="price-text" id="eventPriceText" data-base-amount="{{ (int) round($finalPrice ?? 0) }}">
                                    @if($isFree) FREE @else Rp{{ number_format($finalPrice,0,',','.') }} @endif
                                </div>
                            </div>
                        </div>

                            @if(!$isFree)
                                <div class="mt-3">
                                    <div class="form-label-custom" style="margin-bottom:6px;">Metode Pembayaran</div>
                                   <div style="display:flex; gap:14px; flex-wrap:wrap;">
                                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:black;">
                            <input type="radio" name="payment_method" value="manual" checked>
                             Manual (QRIS + upload bukti)
                                 </label>
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:black;">
                         <input type="radio" name="payment_method" value="midtrans" @if(!$midtransClientKey) disabled @endif>
                         Midtrans
                     </label>
                    </div>
                                    @if(!$midtransClientKey)
                                        <div class="small text-muted" style="margin-top:6px;">Midtrans belum dikonfigurasi.</div>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-3 text-center" id="manualQrisSection">
                                <img src="{{ asset('aset/qr-payment-idSpora.png') }}" alt="QRIS" style="max-width:220px;cursor:pointer;" id="qrisImage">
                                <div class="small text-muted mt-2">Scan QRIS di atas untuk membayar secara manual</div>
                            </div>
                    </div>

                        <div class="upload-bukti" id="manualUploadSection">
                        <div class="mt-3">
                            <h3>Upload Bukti Pembayaran </h3>
                            <small>Dengan (JPEG/PNG, max 5MB)</small>
                            <input type="file" name="payment_proof" accept="image/*" class="form-control-custom">
                   
                        </div>

                        <button type="submit" class="btn-pay">
                            @if(isset($event) && $isFree) Daftar Gratis @else Kirim Bukti Pembayaran @endif
                        </button>
                        
                    </div>

                    @if(!$isFree)
                        <div id="midtransSection" style="display:none;">
                            <button type="button" id="midtransPayBtn" class="btn-pay" style="margin-top:0;">Bayar dengan Midtrans</button>
                            <div class="small text-muted mt-2">Pembayaran akan terverifikasi otomatis setelah sukses.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const form = document.getElementById('paymentForm');
        if(!form) return;
        const fullName = form.querySelector('input[name="full_name"]');
        const dial = form.querySelector('select[name="dial_code"]');
        const wa = form.querySelector('input[name="whatsapp"]');
        const proof = form.querySelector('input[name="payment_proof"]');
        const btn = form.querySelector('.btn-pay');
        const methodRadios = form.querySelectorAll('input[name="payment_method"]');
        const manualQrisSection = document.getElementById('manualQrisSection');
        const manualUploadSection = document.getElementById('manualUploadSection');
        const midtransSection = document.getElementById('midtransSection');
        const midtransPayBtn = document.getElementById('midtransPayBtn');
        const isFree = @json(isset($event) ? ((int)($finalPrice ?? 0) === 0) : false);

        function getSelectedMethod(){
            const checked = form.querySelector('input[name="payment_method"]:checked');
            return checked ? checked.value : 'manual';
        }

        function toggleMethodUI(){
            if(isFree) return;
            const method = getSelectedMethod();
            const isManual = method === 'manual';
            if(manualQrisSection) manualQrisSection.style.display = isManual ? '' : 'none';
            if(manualUploadSection) manualUploadSection.style.display = isManual ? '' : 'none';
            if(midtransSection) midtransSection.style.display = isManual ? 'none' : '';
        }

        function isValidPhone(val){ return /^[0-9]{6,15}$/.test(String(val || '').trim()); }

        function validate(){
            if(isFree){ btn.disabled = false; btn.style.opacity = '1'; return true; }
            const method = getSelectedMethod();
            const nameOk = fullName && fullName.value.trim().length >= 3;
            const dialOk = dial && dial.value.trim() !== '';
            const waOk = wa && isValidPhone(wa.value);
            const proofOk = proof && proof.files.length > 0;
            const okManual = nameOk && dialOk && waOk && proofOk;
            const okMidtrans = nameOk && dialOk && waOk;
            const ok = method === 'midtrans' ? okMidtrans : okManual;
            if(btn){
                // Manual submit button only matters for manual method
                const shouldEnableManualSubmit = (method !== 'midtrans') && ok;
                btn.disabled = !shouldEnableManualSubmit;
                btn.style.opacity = shouldEnableManualSubmit ? '1' : '0.5';
            }
            if(midtransPayBtn){
                midtransPayBtn.disabled = !(method === 'midtrans' && okMidtrans);
                midtransPayBtn.style.opacity = (method === 'midtrans' && okMidtrans) ? '1' : '0.5';
            }
            return ok;
        }

        ['input','change','keyup','blur'].forEach(evt => {
            if(fullName) fullName.addEventListener(evt, validate);
            if(dial) dial.addEventListener(evt, validate);
            if(wa) wa.addEventListener(evt, validate);
            if(proof) proof.addEventListener(evt, validate);
        });

        if(methodRadios && methodRadios.length){
            methodRadios.forEach(r => r.addEventListener('change', function(){
                toggleMethodUI();
                validate();
            }));
        }

        form.addEventListener('submit', function(e){
            if(!isFree && getSelectedMethod() === 'midtrans'){
                e.preventDefault();
                return;
            }
            if(!validate()){
                e.preventDefault();
                alert('Lengkapi data peserta sebelum mengirim bukti pembayaran.');
            }
            // otherwise allow normal form submission (multipart upload handled by server)
        });

        // initial
        toggleMethodUI();
        validate();

        // Referral auto-check + auto discount (only when referral input exists)
        const referralInput = document.getElementById('eventReferralCodeInput');
        const referralFeedback = document.getElementById('referralFeedbackEvent');
        const priceText = document.getElementById('eventPriceText');
        const baseAmount = priceText ? parseInt(priceText.getAttribute('data-base-amount') || '0', 10) : 0;
        const referralCheckBaseUrl = @json(isset($event) ? route('payment.check-referral', $event->id) : '');

        let referralTimer = null;

        function formatIdrNumber(amount) {
            const n = Math.max(0, parseInt(amount || 0, 10));
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function setReferralFeedback(message, type) {
            if (!referralFeedback) return;
            referralFeedback.textContent = message || '';
            if (!message) {
                referralFeedback.style.color = '#6B7280';
                return;
            }
            referralFeedback.style.color = type === 'success' ? '#16A34A' : '#EF4444';
        }

        function setDisplayedPrice(amount) {
            if (!priceText) return;
            const n = parseInt(amount || 0, 10);
            if (n <= 0) {
                priceText.textContent = 'FREE';
            } else {
                priceText.textContent = 'Rp' + formatIdrNumber(n);
            }
        }

        async function checkReferral(code) {
            if (!referralCheckBaseUrl || !priceText) return;
            const c = String(code || '').trim();

            if (c === '') {
                setDisplayedPrice(baseAmount);
                setReferralFeedback('', '');
                return;
            }

            try {
                const res = await fetch(referralCheckBaseUrl + '?code=' + encodeURIComponent(c), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if (data && data.valid) {
                    setDisplayedPrice(data.final_amount);
                    setReferralFeedback(data.message || 'Kode referral valid.', 'success');
                } else {
                    setDisplayedPrice(baseAmount);
                    setReferralFeedback((data && data.message) ? data.message : 'Kode referral tidak valid.', 'error');
                }
            } catch (e) {
                setDisplayedPrice(baseAmount);
                setReferralFeedback('Gagal cek referral. Coba lagi.', 'error');
            }
        }

        if (referralInput) {
            referralInput.addEventListener('input', function() {
                clearTimeout(referralTimer);
                referralTimer = setTimeout(function(){
                    checkReferral(referralInput.value);
                }, 400);
            });
            referralInput.addEventListener('blur', function(){
                clearTimeout(referralTimer);
                checkReferral(referralInput.value);
            });
        }

        // Midtrans flow
        const snapTokenUrl = @json(isset($event) ? route('payment.snap-token', $event->id) : '');
        const pendingOrderUrl = @json(isset($event) ? route('payment.pending-order', $event->id) : '');
        const finalizeUrl = @json(isset($event) ? route('payment.finalize', $event->id) : '');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        let cachedPending = null;

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

                // Autofill WA from pending payment if empty
                if (pending.whatsapp_number && wa && (!wa.value || wa.value.trim() === '')) {
                    const raw = String(pending.whatsapp_number).trim();
                    // Expect formats like +62xxxxxxxx or 62xxxxxxxx or 0xxxxxxxx
                    const dialEl = dial;
                    if (raw.startsWith('+')) {
                        const m = raw.match(/^\+(\d{1,3})(.*)$/);
                        if (m) {
                            const dialCode = '+' + m[1];
                            const rest = String(m[2] || '').replace(/\D/g, '');
                            if (dialEl) {
                                const opt = Array.from(dialEl.options || []).find(o => o.value === dialCode);
                                if (opt) dialEl.value = dialCode;
                            }
                            wa.value = rest;
                        }
                    } else {
                        wa.value = raw.replace(/\D/g, '');
                    }
                }

                // Autofill referral code if available and empty
                if (pending.referral_code && referralInput && (!referralInput.value || referralInput.value.trim() === '')) {
                    referralInput.value = String(pending.referral_code);
                    try { checkReferral(referralInput.value); } catch(_e) {}
                }

                // Auto select Midtrans and disable Manual option while pending
                const midtransRadio = form.querySelector('input[name="payment_method"][value="midtrans"]');
                const manualRadio = form.querySelector('input[name="payment_method"][value="manual"]');
                if (midtransRadio && !midtransRadio.disabled) {
                    midtransRadio.checked = true;
                }
                if (manualRadio) {
                    manualRadio.disabled = true;
                }
                if (proof) {
                    proof.disabled = true;
                }

                toggleMethodUI();
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
                alert('Midtrans belum siap. Pastikan client key sudah diset.');
                return;
            }

            // ensure validation up-to-date
            validate();
            if(midtransPayBtn && midtransPayBtn.disabled){
                alert('Lengkapi data peserta sebelum membayar.');
                return;
            }

            const referralCode = (referralInput ? referralInput.value : '').trim();
            const dialVal = (dial ? dial.value : '').trim();
            const waVal = (wa ? wa.value : '').trim();

            async function getOrCreateSnapToken(forceNew){
                // Prefer pending order token if available
                const pending = cachedPending || await fetchPendingOrder();
                cachedPending = pending;
                if(!forceNew && pending && pending.pending && pending.order_id && pending.snap_token){
                    return { snap_token: pending.snap_token, order_id: pending.order_id };
                }

                const url = new URL(snapTokenUrl, window.location.origin);
                if(referralCode) url.searchParams.set('referral_code', referralCode);
                if(dialVal) url.searchParams.set('dial_code', dialVal);
                if(waVal) url.searchParams.set('whatsapp', waVal);
                if(forceNew) url.searchParams.set('force_new', '1');

                const res = await fetch(url.toString(), {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                });
                const data = await res.json();
                if(!res.ok || !data || !data.snap_token){
                    throw new Error(data && data.message ? data.message : 'Gagal membuat token Midtrans');
                }
                return data;
            }

            midtransPayBtn.disabled = true;
            const originalText = midtransPayBtn.textContent;
            midtransPayBtn.textContent = 'Memproses...';

            try{
                let data;
                try {
                    data = await getOrCreateSnapToken(false);
                } catch(e) {
                    // One-time fallback: force new transaction if needed
                    data = await getOrCreateSnapToken(true);
                }

                // If we used pending order, make label reflect it
                if(data && data.snap_token && originalText !== 'Lanjutkan pembayaran Midtrans'){
                    try { await ensurePendingLabel(); } catch(_e) {}
                }

                window.snap.pay(data.snap_token, {
                    onSuccess: async function(){
                        try {
                            await postFinalize(data.order_id);
                        } catch(_e) {}
                        window.location.href = @json(isset($event) ? route('events.show', $event->id) : route('dashboard'));
                    },
                    onPending: async function(){
                        // keep as pending; user can retry later
                        try { await postFinalize(data.order_id); } catch(_e) {}
                        alert('Pembayaran pending. Silakan selesaikan pembayaran di Midtrans.');
                        // Update label for next attempt
                        cachedPending = { pending: true, order_id: data.order_id, snap_token: data.snap_token };
                        if(midtransPayBtn) midtransPayBtn.textContent = 'Lanjutkan pembayaran Midtrans';
                    },
                    onError: function(){
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function(){
                        // user closed popup
                    }
                });
            } catch(e){
                alert(String(e && e.message ? e.message : e));
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

        // If there is a pending Midtrans payment, show "continue" label
        ensurePendingLabel();
    });
    </script>
</body>
</html>