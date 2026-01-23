<<<<<<< HEAD
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

    <form id="paymentForm" method="POST" action="#" novalidate>
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
                                <div class="price-text">
                                    @if($isFree) FREE @else Rp{{ number_format($finalPrice,0,',','.') }} @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-custom">
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
                    </div>

                    <div class="card-custom" style="background-color: #FFFBEB; border-color: #FCD34D;">
                        <div class="total-header">
                            <div class="bag-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-handbag" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 1 2 2v2H6V3a2 2 0 0 1 2-2m3 4V3a3 3 0 1 0-6 0v2H3.36a1.5 1.5 0 0 0-1.483 1.277L.85 13.13A2.5 2.5 0 0 0 3.322 16h9.355a2.5 2.5 0 0 0 2.473-2.87l-1.028-6.853A1.5 1.5 0 0 0 12.64 5zm-1 0v-.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5V5zM3.633 7h8.734l1.03 6.865a1.5 1.5 0 0 1-1.483 1.725H3.322a1.5 1.5 0 0 1-1.483-1.725z"/>
                                </svg>
                            </div>
                            <h3 style="margin:0; font-size:15px; font-weight:700;">Total Biaya</h3>
                        </div>

                        <div class="price-row">
                            <span>Price</span>
                            <span>
                                @if(isset($event) && !$isFree) Rp{{ number_format($finalPrice,0,',','.') }} @elseif($isFree) Rp0 @else - @endif
                            </span>
                        </div>

                        <div class="total-row">
                            <span>Total</span>
                            <span>
                                @if(isset($event) && !$isFree) Rp{{ number_format($finalPrice,0,',','.') }} @elseif($isFree) Rp0 @else - @endif
                            </span>
                        </div>
                        
                        <div id="pending-payment-banner" style="display:none; margin-top:8px; padding:6px 8px; border-radius:4px; background:#fff; border:1px solid #FCD34D; color:#92400E; font-size:11px;"></div>

                        <button type="submit" class="btn-pay" disabled>
                            @if(isset($event) && $isFree) Daftar Gratis @else Bayar @endif
                        </button>
                        
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const form = document.getElementById('paymentForm');
        if(!form) return;
        const fullName = form.querySelector('input[name="full_name"]');
        const dial = form.querySelector('select[name="dial_code"]');
        const wa = form.querySelector('input[name="whatsapp"]');
        const btn = form.querySelector('.btn-pay');
        const emailInput = form.querySelector('input[name="email"]');
        const isFree = @json(isset($event) ? ((int)($finalPrice ?? 0) === 0) : false);
        const eventId = @json($event->id ?? null);
        const pendingKey = `pending_payment_event_${eventId}`;
        const pendingBanner = document.getElementById('pending-payment-banner');
        let isPendingResume = false;

        async function fetchPendingOrder(){
            if(!eventId) return null;
            try{
                const res = await fetch(`/payment/${eventId}/pending-order`, { headers: { 'Accept':'application/json' } });
                if(!res.ok) return null;
                const data = await res.json();
                // Consider finished when status is settlement or active
                const status = (data?.status || '').toLowerCase();
                const isPendingLike = status === 'pending' || status === 'challenge';
                if(data?.order_id && isPendingLike){
                    try{ localStorage.setItem(pendingKey, JSON.stringify({ order_id: data.order_id, ts: Date.now() })); }catch(_e){}
                    return { order_id: data.order_id, status: data.status };
                } else {
                    try{ localStorage.removeItem(pendingKey); }catch(_e){}
                    return null;
                }
            }catch(_e){ return null; }
        }

        function setPendingMode(){
            try{
                if(fullName) { fullName.setAttribute('readonly','readonly'); }
                if(dial) { dial.setAttribute('disabled','disabled'); }
                if(wa) { wa.setAttribute('readonly','readonly'); }
                if(btn) { btn.innerText = 'Lanjutkan Bayar'; btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer'; }
                isPendingResume = true;
            }catch(_e){}
        }

        function clearPendingMode(){
            try{
                if(fullName) { fullName.removeAttribute('readonly'); }
                if(dial) { dial.removeAttribute('disabled'); }
                if(wa) { wa.removeAttribute('readonly'); }
                isPendingResume = false;
                if(pendingBanner){ pendingBanner.style.display = 'none'; pendingBanner.innerText = ''; }
                if(btn){ btn.innerText = 'Bayar'; }
                // Re-evaluate validation to properly disable the button until fields are complete
                validate();
            }catch(_e){}
        }

        (async function checkPendingBanner(){
            // Prefer server truth; fallback to localStorage if server unavailable
            const serverPending = await fetchPendingOrder();
            if(serverPending){
                if(pendingBanner){
                    pendingBanner.style.display = 'block';
                    pendingBanner.innerText = '⚠️ Menunggu pembayaran.';
                }
                setPendingMode();
                return;
            }
            try{
                const raw = localStorage.getItem(pendingKey);
                if(!raw){ clearPendingMode(); return; }
                const obj = JSON.parse(raw);
                if(!obj || !obj.ts || !obj.order_id){ localStorage.removeItem(pendingKey); clearPendingMode(); return; }
                const age = Date.now() - (obj.ts || 0);
                if(age <= 24*60*60*1000){ // allow resume up to 24h if server unreachable
                    if(pendingBanner){ pendingBanner.style.display = 'block'; pendingBanner.innerText = '⚠️ Menunggu pembayaran.'; }
                    setPendingMode();
                } else { localStorage.removeItem(pendingKey); clearPendingMode(); }
            }catch(_e){ clearPendingMode(); }
        })();

        function isValidPhone(val){ return /^[0-9]{6,15}$/.test(val.trim()); }

        function validate(){
            if(isPendingResume){
                btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer';
                return;
            }
            const nameOk = fullName.value.trim().length >= 3;
            const dialOk = dial.value.trim() !== '';
            const waOk = isValidPhone(wa.value);
            const allOk = nameOk && dialOk && waOk;
            btn.disabled = !allOk;
            btn.style.opacity = !allOk ? '0.5' : '1';
            btn.style.cursor = !allOk ? 'not-allowed' : 'pointer';
        }

        ['input','change','keyup','blur'].forEach(evt => {
            fullName.addEventListener(evt, validate);
            dial.addEventListener(evt, validate);
            wa.addEventListener(evt, validate);
        });

        function buildPhone(){
            const dialVal = (dial.value || '').trim();
            let waVal = (wa.value || '').trim();
            waVal = waVal.replace(/[^0-9]/g,'');
            if(waVal.startsWith('0')) waVal = waVal.substring(1);
            return `${dialVal}${waVal}`;
        }

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            validate();
            if(btn.disabled && !isPendingResume){ alert('Lengkapi data peserta.'); return; }
            if(!eventId){ alert('Event tidak valid.'); return; }

            if(isFree){
                try{
                    const res = await fetch(`/events/${eventId}/register`, {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }, body: JSON.stringify({})
                    });
                    const data = await res.json();
                    if(data.status === 'ok' || data.status === 'already'){ window.location = `/events/${eventId}`; }
                    else { alert(data.message || 'Gagal mendaftar.'); }
                }catch(err){ alert('Error mendaftar.'); }
                return;
            }

            try{
                btn.disabled = true; btn.innerText = '...';
                const url = new URL(`${window.location.origin}/payment/${eventId}/snap-token`);
                url.searchParams.set('phone', buildPhone());
                url.searchParams.set('name', fullName.value.trim());
                if(emailInput && emailInput.value) url.searchParams.set('email', emailInput.value.trim());

                // Prefer server pending order to force resume until settlement/active
                try {
                    const serverPending = await fetchPendingOrder();
                    if(serverPending?.order_id){
                        url.searchParams.set('order_id', serverPending.order_id);
                    } else {
                        const pendingRaw = localStorage.getItem(pendingKey);
                        if (pendingRaw) {
                            const pendingObj = JSON.parse(pendingRaw);
                            if (pendingObj && pendingObj.order_id && pendingObj.ts) {
                                url.searchParams.set('order_id', pendingObj.order_id);
                            }
                        }
                    }
                } catch (_e) {}

                const tokenRes = await fetch(url.toString(), { headers: { 'Accept':'application/json' } });
                const data = await tokenRes.json();
                if(data && data.free){ return; }

                if (data && (data.orderId || data.order_id)) {
                    try { localStorage.setItem(pendingKey, JSON.stringify({ order_id: data.orderId || data.order_id, ts: Date.now() })); } catch (_e){}
                }

                if(!data?.snapToken){
                    if(data?.redirectUrl){
                        // Fallback to redirect URL when snap token unavailable (resume continuity)
                        window.location = data.redirectUrl;
                        return;
                    }
                    throw new Error(data?.message || 'Token error');
                }

                window.snap.pay(data.snapToken, {
                    onSuccess: async function(r){
                        try{ localStorage.removeItem(pendingKey); } catch(_e){}
                        try{ await fetch(`/payment/${eventId}/finalize`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }, body: JSON.stringify({ order_id: r.order_id }) }); }catch(e){}
                        window.location = `/events/${eventId}`;
                    },
                    onPending: function(r){ window.location = `/events/${eventId}`; },
                    onError: function(r){ alert('Gagal bayar.'); btn.innerText = isPendingResume ? 'Lanjutkan Bayar' : 'Bayar'; },
                    onClose: function(){ btn.innerText = isPendingResume ? 'Lanjutkan Bayar' : 'Bayar'; }
                });
            }catch(err){
                console.error(err); alert('Gagal inisiasi.'); btn.innerText = isPendingResume ? 'Lanjutkan Bayar' : 'Bayar';
            }finally{
                if(btn.innerText !== '...') btn.disabled = false;
            }
        });
<<<<<<< HEAD
    }
});
</script>
<style>
    .btn-pay:disabled {opacity:.55; cursor:not-allowed; filter:grayscale(.15);} 
    .btn-pay.btn-disabled:hover {background:inherit;}
</style>
=======
>>>>>>> 7c287cc6e13fddde0a1fa94ce4bba305577efb13
=======
        
        validate();

        
    });
    </script>
</body>
</html>
>>>>>>> 72354fd716044e12a05a1743a3c2f66b45a2728a
