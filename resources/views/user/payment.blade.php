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

        /* Whatsapp */
        .wa-group { display: flex; gap: 8px; }
        .wa-group select { width: 100px; }

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
                        @if(isset($event) && (bool) ($event->is_reseller_event ?? false))
                        <div class="mb-custom">
                            <label class="form-label-custom">Kode Referral (opsional)</label>
                            <input type="text" class="form-control-custom" name="referral_code" id="referralCodeInput" placeholder="Masukkan kode referral jika ada" value="{{ request()->query('ref', '') }}">
                            <div id="referralMessage" class="form-text small text-danger" style="display:none;">&nbsp;</div>
                            <div class="form-text small">Masukkan kode referral reseller untuk mendapatkan diskon/komisi.</div>
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
                                        @if(!$midtransClientKey)
                                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:black;">
                                                <input type="radio" name="payment_method" value="manual" checked>
                                                Manual (QRIS + upload bukti)
                                            </label>
                                        @endif
                                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px; color:black;">
                                            <input type="radio" name="payment_method" value="midtrans" @if(!$midtransClientKey) disabled @endif @if($midtransClientKey) checked @endif>
                                            Midtrans
                                        </label>
                                   </div>
                                    @if(!$midtransClientKey)
                                        <div class="small text-muted" style="margin-top:6px;">Midtrans belum dikonfigurasi.</div>
                                    @endif
                                </div>
                            @endif

                    </div>

                    @if(isset($event) && $isFree)
                        <div class="mt-3" id="manualPaySection">
                            <button type="submit" class="btn-pay">Daftar Gratis</button>
                        </div>
                    @else
                        @if(!$midtransClientKey)
                            <div class="mt-3" id="manualPaySection">
                                <button type="button" id="showQrisBtn" class="btn-pay" disabled>Bayar</button>
                            </div>
                        @else
                            <div class="mt-3" id="manualPaySection" style="display:none;"></div>
                        @endif
                    @endif

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

    <!-- QRIS Modal (manual) - mirip Course payment -->
    @if((!isset($event) || !(isset($event) && $isFree)) && !$midtransClientKey)
    <div class="modal fade qris-modal" id="qrisModal" tabindex="-1" aria-labelledby="qrisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrisModalLabel">Pembayaran - QRIS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p class="text-secondary">Scan QRIS berikut untuk melakukan pembayaran.</p>

                    <img id="qrisImage" class="qris-image" src="{{ asset('aset/qr-payment-idSpora.png') }}" alt="QRIS Payment" style="max-width: 260px; width: 100%; height: auto; border-radius: 10px; border: 1px solid #e5e7eb;">

                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ asset('aset/qr-payment-idSpora.png') }}" class="btn btn-outline-primary" download>
                            Download QR
                        </a>
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

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
                    <h5 class="mb-2" style="font-weight: 700;">Berhasil!</h5>
                    <div id="midtransSuccessModalText" class="text-muted" style="font-size: 14px;"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal" style="border-radius: 10px; padding: 10px 18px; font-weight: 600;">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const form = document.getElementById('paymentForm');
        if(!form) return;
        const fullName = form.querySelector('input[name="full_name"]');
        const dial = form.querySelector('select[name="dial_code"]');
        const wa = form.querySelector('input[name="whatsapp"]');
        const showQrisBtn = document.getElementById('showQrisBtn');
        const paymentProofInput = document.getElementById('paymentProofInput');
        const referralInput = form.querySelector('input[name="referral_code"]');
        const referralMessageEl = document.getElementById('referralMessage');
        const eventPriceEl = document.getElementById('eventPriceText');
        const currentUserReferral = @json(auth()->user()->referral_code ?? '');
        const REFERRAL_RATE = 0.10;

        function formatRupiah(val){
            try{
                return (new Intl.NumberFormat('id-ID')).format(Math.round(val));
            }catch(e){
                return String(Math.round(val));
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

        function updateReferralUI(){
            if(!eventPriceEl) return;
            const base = parseFloat(eventPriceEl.dataset.baseAmount || '0') || 0;
            const code = referralInput ? String(referralInput.value || '').trim() : '';

            // self-referral block
            if(code !== '' && currentUserReferral && code.toUpperCase() === String(currentUserReferral).toUpperCase()){
                if(referralMessageEl){ referralMessageEl.style.display = ''; referralMessageEl.classList.remove('text-muted'); referralMessageEl.classList.add('text-danger'); referralMessageEl.textContent = 'Kode referral tidak boleh milik Anda sendiri.'; }
                if(showQrisBtn) showQrisBtn.disabled = true;
                if(midtransPayBtn) midtransPayBtn.disabled = true;
                if(payNowBtn) payNowBtn.disabled = true;
                if(eventPriceEl) eventPriceEl.textContent = 'Rp' + formatRupiah(base);
                return;
            }

            if(code === ''){
                if(eventPriceEl) eventPriceEl.textContent = base > 0 ? ('Rp' + formatRupiah(base)) : 'FREE';
                if(referralMessageEl){ referralMessageEl.style.display = 'none'; referralMessageEl.textContent = ''; }
                validate();
                return;
            }

            // debounce server validation
            if(_referralTimer) clearTimeout(_referralTimer);
            _referralTimer = setTimeout(async function(){
                const data = await validateReferralServer(code);
                if(!data){
                    if(referralMessageEl){ referralMessageEl.style.display = ''; referralMessageEl.classList.remove('text-muted'); referralMessageEl.classList.add('text-danger'); referralMessageEl.textContent = 'Gagal memeriksa kode. Coba lagi.'; }
                    if(showQrisBtn) showQrisBtn.disabled = true;
                    if(midtransPayBtn) midtransPayBtn.disabled = true;
                    if(payNowBtn) payNowBtn.disabled = true;
                    if(eventPriceEl) eventPriceEl.textContent = 'Rp' + formatRupiah(base);
                    return;
                }

                if(data.valid){
                    if(eventPriceEl) eventPriceEl.textContent = 'Rp' + formatRupiah(data.final_amount) + ' (' + Math.round((data.discount_rate||REFERRAL_RATE)*100) + '% off)';
                    if(referralMessageEl){ referralMessageEl.style.display = ''; referralMessageEl.classList.remove('text-danger'); referralMessageEl.classList.add('text-muted'); referralMessageEl.textContent = data.message || 'Kode referral valid.'; }
                    validate();
                } else {
                    if(referralMessageEl){ referralMessageEl.style.display = ''; referralMessageEl.classList.remove('text-muted'); referralMessageEl.classList.add('text-danger'); referralMessageEl.textContent = data.message || 'Kode referral tidak valid.'; }
                    if(showQrisBtn) showQrisBtn.disabled = true;
                    if(midtransPayBtn) midtransPayBtn.disabled = true;
                    if(payNowBtn) payNowBtn.disabled = true;
                    if(eventPriceEl) eventPriceEl.textContent = 'Rp' + formatRupiah(base);
                }
            }, 450);
        }
        const payNowBtn = document.getElementById('payNowBtn');
        const methodRadios = form.querySelectorAll('input[name="payment_method"]');
        const manualPaySection = document.getElementById('manualPaySection');
        const midtransSection = document.getElementById('midtransSection');
        const midtransPayBtn = document.getElementById('midtransPayBtn');
        const isFree = @json(isset($event) ? ((int)($finalPrice ?? 0) === 0) : false);

        let pendingProofSubmit = false;

        function getSelectedMethod(){
            const checked = form.querySelector('input[name="payment_method"]:checked');
            return checked ? checked.value : 'manual';
        }

        function toggleMethodUI(){
            if(isFree) return;
            const method = getSelectedMethod();
            const isManual = method === 'manual';
            if(manualPaySection) manualPaySection.style.display = isManual ? '' : 'none';
            if(midtransSection) midtransSection.style.display = isManual ? 'none' : '';
        }

        function isValidPhone(val){ return /^[0-9]{6,15}$/.test(String(val || '').trim()); }

        function validate(){
            if(isFree){
                return true;
            }
            const method = getSelectedMethod();
            const nameOk = fullName && fullName.value.trim().length >= 3;
            const dialOk = dial && dial.value.trim() !== '';
            const waOk = wa && isValidPhone(wa.value);
            const okMidtrans = nameOk && dialOk && waOk;
            const okManualBase = nameOk && dialOk && waOk;
            if(showQrisBtn){
                showQrisBtn.disabled = !(method === 'manual' && okManualBase);
                showQrisBtn.style.opacity = (method === 'manual' && okManualBase) ? '1' : '0.5';
            }
            if(midtransPayBtn){
                midtransPayBtn.disabled = !(method === 'midtrans' && okMidtrans);
                midtransPayBtn.style.opacity = (method === 'midtrans' && okMidtrans) ? '1' : '0.5';
            }
            // payNow button depends on proof selected
            if(payNowBtn){
                const proofOk = paymentProofInput && paymentProofInput.files && paymentProofInput.files.length > 0;
                payNowBtn.disabled = !(method === 'manual' && okManualBase && proofOk);
            }
            return (method === 'midtrans') ? okMidtrans : okManualBase;
        }

        ['input','change','keyup','blur'].forEach(evt => {
            if(fullName) fullName.addEventListener(evt, validate);
            if(dial) dial.addEventListener(evt, validate);
            if(wa) wa.addEventListener(evt, validate);
            if(paymentProofInput) paymentProofInput.addEventListener(evt, validate);
            if(referralInput) referralInput.addEventListener(evt, updateReferralUI);
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
            if(!isFree && getSelectedMethod() === 'manual'){
                // Require proof for manual submit
                const proofOk = paymentProofInput && paymentProofInput.files && paymentProofInput.files.length > 0;
                if(!proofOk){
                    e.preventDefault();
                    alert('Silakan upload bukti pembayaran terlebih dahulu.');
                    return;
                }

                if(pendingProofSubmit){
                    return;
                }

                e.preventDefault();
                const confirmModalEl = document.getElementById('confirmProofModal');
                if(confirmModalEl && window.bootstrap){
                    const m = window.bootstrap.Modal.getOrCreateInstance(confirmModalEl);
                    m.show();
                } else {
                    // If bootstrap modal is unavailable, submit directly
                    pendingProofSubmit = true;
                    form.submit();
                }
                return;
            }

            if(!validate()){
                e.preventDefault();
                alert('Lengkapi data peserta sebelum membayar.');
            }
        });

        // initial
        toggleMethodUI();
        updateReferralUI();
        validate();

        // Manual QRIS modal open
        if(showQrisBtn){
            showQrisBtn.addEventListener('click', function(e){
                e.preventDefault();
                validate();
                if(showQrisBtn.disabled){
                    alert('Lengkapi data peserta terlebih dahulu.');
                    return;
                }

                const qrisEl = document.getElementById('qrisModal');
                if(qrisEl && window.bootstrap){
                    try {
                        const collapseEl = document.getElementById('uploadProofCollapse');
                        if(collapseEl && window.bootstrap.Collapse){
                            const collapse = window.bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
                            collapse.hide();
                        }
                        const proofPreviewEl = document.getElementById('proofPreview');
                        if(paymentProofInput) paymentProofInput.value = '';
                        if(proofPreviewEl) proofPreviewEl.style.display = 'none';
                        if(payNowBtn) payNowBtn.disabled = true;
                    } catch(_e) {}

                    const modal = window.bootstrap.Modal.getOrCreateInstance(qrisEl);
                    modal.show();
                }
            });
        }

        // Proof preview + size validation
        if(paymentProofInput){
            paymentProofInput.addEventListener('change', function(){
                const file = paymentProofInput.files && paymentProofInput.files[0];
                const proofPreviewEl = document.getElementById('proofPreview');
                const proofPreviewImg = document.getElementById('proofPreviewImg');
                if(!file){
                    if(proofPreviewEl) proofPreviewEl.style.display = 'none';
                    validate();
                    return;
                }
                if(file.size > 5 * 1024 * 1024){
                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                    paymentProofInput.value = '';
                    if(proofPreviewEl) proofPreviewEl.style.display = 'none';
                    validate();
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(evt){
                    if(proofPreviewImg) proofPreviewImg.src = evt.target.result;
                    if(proofPreviewEl) proofPreviewEl.style.display = 'block';
                    validate();
                };
                reader.readAsDataURL(file);
            });
        }

        // Confirm modal submit
        const confirmProofModalEl = document.getElementById('confirmProofModal');
        const confirmProofSubmitBtn = document.getElementById('confirmProofSubmitBtn');
        if(confirmProofModalEl && confirmProofSubmitBtn){
            confirmProofSubmitBtn.addEventListener('click', function(){
                if(pendingProofSubmit) return;
                pendingProofSubmit = true;
                try { confirmProofSubmitBtn.disabled = true; } catch(_e) {}
                form.submit();
            });

            confirmProofModalEl.addEventListener('hidden.bs.modal', function(){
                pendingProofSubmit = false;
                try { confirmProofSubmitBtn.disabled = false; } catch(_e) {}
            });
        }

        // Midtrans flow
        const snapTokenUrl = @json(isset($event) ? route('payment.snap-token', $event->id) : '');
        const pendingOrderUrl = @json(isset($event) ? route('payment.pending-order', $event->id) : '');
        const finalizeUrl = @json(isset($event) ? route('payment.finalize', $event->id) : '');
        const eventTitle = @json(isset($event) ? ($event->title ?? 'Event') : 'Event');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const checkReferralUrl = @json(isset($event) ? route('payment.check-referral', $event->id) : '');

        function showMidtransSuccessModal(){
            const text = document.getElementById('midtransSuccessModalText');
            if (text) {
                text.textContent = 'Anda berhasil terdaftar di event "' + eventTitle + '".';
            }

            const modalEl = document.getElementById('midtransSuccessModal');
            if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                const m = window.bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false });
                m.show();
            }
        }

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

                // Auto select Midtrans and disable Manual option while pending
                const midtransRadio = form.querySelector('input[name="payment_method"][value="midtrans"]');
                const manualRadio = form.querySelector('input[name="payment_method"][value="manual"]');
                if (midtransRadio && !midtransRadio.disabled) {
                    midtransRadio.checked = true;
                }
                if (manualRadio) {
                    manualRadio.disabled = true;
                }
                if (paymentProofInput) paymentProofInput.disabled = true;
                if (showQrisBtn) showQrisBtn.disabled = true;

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

            const forceNewFromQuery = (new URLSearchParams(window.location.search)).get('force_new') === '1';

            // ensure validation up-to-date
            validate();
            if(midtransPayBtn && midtransPayBtn.disabled){
                alert('Lengkapi data peserta sebelum membayar.');
                return;
            }

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
                if(dialVal) url.searchParams.set('dial_code', dialVal);
                if(waVal) url.searchParams.set('whatsapp', waVal);
                if(referralInput && referralInput.value && referralInput.value.trim() !== '') url.searchParams.set('referral_code', referralInput.value.trim());
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
                if (forceNewFromQuery) {
                    // Explicit user intent: always create a new Midtrans transaction.
                    cachedPending = null;
                    data = await getOrCreateSnapToken(true);
                } else {
                    try {
                        data = await getOrCreateSnapToken(false);
                    } catch(e) {
                        // One-time fallback: force new transaction if needed
                        data = await getOrCreateSnapToken(true);
                    }
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
                        showMidtransSuccessModal();
                        // Give user a moment to see success modal before redirect
                        setTimeout(function(){
                            window.location.href = @json(isset($event) ? route('events.show', $event->id) : route('dashboard'));
                        }, 1400);
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