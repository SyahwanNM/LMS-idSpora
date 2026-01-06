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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #F8FAFC;
        }
        /* Layout 2 kolom: kiri data peserta, kanan order detail */
        #paymentForm { 
            display:flex; 
            gap:48px; 
            align-items:flex-start; 
            flex-wrap:nowrap; 
            width:100%;
        }
        #paymentForm .kiri-payment { 
            flex:1 1 60%; 
            max-width:680px; 
        }
        #paymentForm .ticket { 
            flex:1 1 40%; 
            max-width:440px; 
            margin-top:0; /* hilangkan kemungkinan offset */
        }
        .box-payment { max-width:1280px; margin:0 auto 60px; padding:10px 20px; }
        @media (max-width: 1100px){
            #paymentForm { gap:36px; }
        }
        @media (max-width: 992px){
            #paymentForm { flex-direction:column; flex-wrap:wrap; }
            #paymentForm .kiri-payment, #paymentForm .ticket { max-width:100%; }
        }
        @media (max-width: 576px){
            #paymentForm { gap:28px; }
        }
    </style>
</head>

<body>
    <div class="link-box mb-3" style="margin-top:80px;">
        <a href="{{ route('dashboard') }}">Home</a>
        <p>/</p>
        <a href="{{ route('events.index') }}">Event</a>
        <p>/</p>
        @if(isset($event))
            <a href="{{ route('events.show',$event) }}">{{ Str::limit($event->title,40) }}</a>
            <p>/</p>
        @endif
        <a class="active" href="#">Payment</a>
    </div>
    <div class="box-payment">
    <form id="paymentForm" method="POST" action="#" novalidate>
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">
        <div class="kiri-payment">
            <h3>Data Peserta</h3>
            <p class="judul-input">Email</p>
            <input class="form" type="email" value="{{ auth()->user()->email ?? '' }}" readonly name="email">
            <p class="judul-input">Nama Lengkap</p>
            <div class="warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#EC0606" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                </svg>
                <p class="warning-text">Nama akan digunakan di sertifikat</p>
            </div>
            <input class="form" type="text" name="full_name" value="{{ auth()->user()->name ?? '' }}" placeholder="Nama sesuai sertifikat" minlength="3" required>
            <div class="form-group">
                <p class="judul-input">No Whatsapp</p>
                <div class="wa-input">
                    <select name="dial_code" required>
                        <option value="">Kode Dial</option>
                        <option value="+62">+62</option>
                        <option value="+60">+60</option>
                        <option value="+65">+65</option>
                    </select>
                    <input class="no-wa" name="whatsapp" type="text" placeholder="No Whatsapp" inputmode="numeric" pattern="[0-9]{6,15}" required>
                </div>
            </div>
        </div>

        <div class="ticket">
            <div class="ticket-header">Order Detail</div>
            <div class="ticket-content">
                @if(isset($event))
                    <img src="{{ $event->image_url ?? asset('aset/event.png') }}" alt="{{ $event->title }}" onerror="this.src='{{ asset('aset/event.png') }}'">
                @else
                    <img src="{{ asset('aset/event.png') }}" alt="Event">
                @endif
                <div class="info">
                    <h4>{{ isset($event)? $event->title : 'Event Title' }}</h4>
                    <p>{{ isset($event)? 'IdSpora' : '' }}</p>
                    @php
                        $isFree = isset($event) ? ((int)$event->price === 0) : false;
                        $hasDiscount = isset($event) ? $event->hasDiscount() : false;
                        $finalPrice = isset($event) ? ($hasDiscount ? $event->discounted_price : $event->price) : 0;
                    @endphp
                    <div class="price">
                        @if($isFree)
                            <span class="badge-diskon">Diskon 100%</span>
                            <span class="ms-2 fw-semibold" style="color:#16a34a;">FREE</span>
                        @else
                            @if($hasDiscount)
                                <span class="text-decoration-line-through text-muted me-2">Rp{{ number_format($event->price,0,',','.') }}</span>
                                <span class="fw-semibold">Rp{{ number_format($finalPrice,0,',','.') }}</span>
                                <span class="badge-diskon ms-2">Diskon {{ $event->discount_percentage }}%</span>
                            @else
                                <span class="fw-semibold">Rp{{ number_format($finalPrice,0,',','.') }}</span>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="ticket-divider"></div>
            <div class="ticket-footer">
                <div>
                    <h5>Total</h5>
                    <p class="m-0">
                        @if(isset($event))
                            @if($isFree)
                                FREE
                            @else
                                Rp{{ number_format($finalPrice,0,',','.') }}
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div class="icon">📄</div>
            </div>
            <div id="pending-payment-banner" style="display:none;margin-top:10px;padding:8px;border-radius:6px;background:#fff4e5;color:#7a4b00;font-size:13px;">You have a pending payment — klik "Bayar" untuk melanjutkan pembayaran yang tertunda.</div>
            <button type="submit" class="btn-pay mt-2" disabled>@if(isset($event) && $isFree) Daftar Gratis @else Bayar @endif</button>
            @if(isset($event) && !$isFree)
            <div class="mt-2" style="font-size:12px;color:#6b7280;">
                QRIS gagal dipindai? <a href="#" id="btnQrisFallback" style="text-decoration:underline;">Coba QRIS Mode Alternatif</a>
            </div>
            <div id="qrisFallbackBox" style="display:none;margin-top:10px;padding:12px;border:1px dashed #cbd5e1;border-radius:8px;background:#f8fafc;">
                <div style="display:flex;gap:16px;align-items:center;flex-wrap:wrap;">
                    <img id="qrisFallbackImg" src="" alt="QRIS" style="width:240px;height:240px;display:none;background:#fff;border:1px solid #e5e7eb;border-radius:6px;"/>
                    <div style="flex:1;min-width:220px;">
                        <div id="qrisFallbackInfo" style="font-size:13px;color:#374151;">Menghasilkan QRIS alternatif…</div>
                        <div id="qrisFallbackString" style="display:none;margin-top:8px;word-break:break-all;font-size:11px;color:#6b7280;"></div>
                        <div style="margin-top:8px;font-size:12px;color:#6b7280;">Scan gambar ini dengan Midtrans QRIS Sandbox Simulator dalam 15 menit.</div>
                    </div>
                </div>
            </div>
            @endif
        </div>
        </form>
    </div>
</body>

</html>
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
// Sanity check & enable/disable tombol bayar
document.addEventListener('DOMContentLoaded', function(){
    const form = document.getElementById('paymentForm');
    if(!form) return;
    const fullName = form.querySelector('input[name="full_name"]');
    const dial = form.querySelector('select[name="dial_code"]');
    const wa = form.querySelector('input[name="whatsapp"]');
    const btn = form.querySelector('.btn-pay');
    const emailInput = form.querySelector('input[name="email"]');
    // Gunakan harga final (setelah diskon) untuk menentukan free vs paid
    const isFree = @json(isset($event) ? ((int)($finalPrice ?? 0) === 0) : false);
    const eventId = @json($event->id ?? null);
    const pendingKey = `pending_payment_event_${eventId}`;
    const pendingBanner = document.getElementById('pending-payment-banner');

    // Check for pending payment and show banner if within 15 minutes
    (function checkPendingBanner(){
        try{
            const raw = localStorage.getItem(pendingKey);
            if(!raw){ if(pendingBanner) pendingBanner.style.display = 'none'; return; }
            const obj = JSON.parse(raw);
            if(!obj || !obj.ts || !obj.order_id){ localStorage.removeItem(pendingKey); if(pendingBanner) pendingBanner.style.display = 'none'; return; }
            const age = Date.now() - (obj.ts || 0);
            const FIFTEEN_MIN = 15 * 60 * 1000;
            if(age <= FIFTEEN_MIN){ if(pendingBanner){ pendingBanner.style.display = 'block'; pendingBanner.innerText = 'Anda memiliki pembayaran tertunda — klik "Bayar" untuk melanjutkan pembayaran yang dipilih (berlaku 15 menit).'; } }
            else { localStorage.removeItem(pendingKey); if(pendingBanner) pendingBanner.style.display = 'none'; }
        }catch(_e){ if(pendingBanner) pendingBanner.style.display = 'none'; }
    })();

    function isValidPhone(val){ return /^[0-9]{6,15}$/.test(val.trim()); }

    function validate(){
        const nameOk = fullName.value.trim().length >= 3;
        const dialOk = dial.value.trim() !== '';
        const waOk = isValidPhone(wa.value);
        const allOk = nameOk && dialOk && waOk;
        btn.disabled = !allOk;
        btn.classList.toggle('btn-disabled', !allOk);
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
        if(btn.disabled){
            alert('Lengkapi data peserta terlebih dahulu (Nama minimal 3 huruf, pilih kode dial, nomor WA 6–15 digit).');
            return;
        }
        if(!eventId){
            alert('Event tidak valid.');
            return;
        }
        // Free event: langsung arahkan ke pendaftaran (skip Midtrans)
        if(isFree){
            try{
                const res = await fetch(`/events/${eventId}/register`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                const data = await res.json();
                if(data.status === 'ok' || data.status === 'already'){
                    window.location = `/events/${eventId}`;
                } else {
                    alert(data.message || 'Gagal mendaftar.');
                }
            }catch(err){
                alert('Terjadi kesalahan saat mendaftar.');
            }
            return;
        }
        // Paid event: get snap token then open snap
        try{
            btn.disabled = true;
            // Support resuming an existing pending payment: store pending order id in localStorage
            const pendingKey = `pending_payment_event_${eventId}`;
            const url = new URL(`${window.location.origin}/payment/${eventId}/snap-token`);
            url.searchParams.set('phone', buildPhone());
            url.searchParams.set('name', fullName.value.trim());
            if(emailInput && emailInput.value) url.searchParams.set('email', emailInput.value.trim());

            // If there is a pending order id saved, request a resume token for that order
            try {
                const pendingRaw = localStorage.getItem(pendingKey);
                if (pendingRaw) {
                    const pendingObj = JSON.parse(pendingRaw);
                    if (pendingObj && pendingObj.order_id && pendingObj.ts) {
                        const age = Date.now() - (pendingObj.ts || 0);
                        const FIFTEEN_MIN = 15 * 60 * 1000;
                        if (age <= FIFTEEN_MIN) {
                            url.searchParams.set('order_id', pendingObj.order_id);
                        } else {
                            // expired: remove pending marker so backend creates a fresh order
                            try { localStorage.removeItem(pendingKey); } catch(_e){}
                        }
                    }
                }
            } catch (_e) { /* ignore JSON errors */ }

            const tokenRes = await fetch(url.toString(), { headers: { 'Accept':'application/json' } });
            const data = await tokenRes.json();

            // If backend indicates a free flow, treat as free
            if(data && data.free){
                const res = await fetch(`/events/${eventId}/register`, {
                    method: 'POST', headers: { 'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),'Accept':'application/json' }, body: JSON.stringify({})
                });
                const rj = await res.json();
                if(rj.status==='ok' || rj.status==='already'){ window.location = `/events/${eventId}`; return; }
                alert(rj.message || 'Gagal mendaftar.');
                return;
            }

            const snapToken = data?.snapToken;
            // If server returned an order id for pending payment, persist it so user can resume later
            if (data && (data.orderId || data.order_id)) {
                const oid = data.orderId || data.order_id;
                try { localStorage.setItem(pendingKey, JSON.stringify({ order_id: oid, ts: Date.now() })); } catch (_e){}
            }

            if(!snapToken){
                const msg = data?.message || 'Snap token not found';
                throw new Error(msg);
            }

            window.snap.pay(snapToken, {
                onSuccess: async function(result){
                    // Clear pending marker on success and confirm to server
                    try{ localStorage.removeItem(pendingKey); } catch(_e){}
                    try{
                        await fetch(`/payment/${eventId}/finalize`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ order_id: result.order_id })
                        });
                    }catch(e){ /* ignore and continue redirect */ }
                    window.location = `/events/${eventId}`;
                },
                onPending: function(result){
                    // Keep pending marker so user can continue; redirect to detail
                    try{ localStorage.removeItem(pendingKey); } catch(_e){}
                    window.location = `/events/${eventId}`;
                },
                onError: function(result){
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: function(){
                    // User closed popup. Keep pending order in localStorage so clicking "Bayar" resumes.
                    // Optionally inform the user to continue their pending payment.
                    try{
                        const pendingRaw = localStorage.getItem(pendingKey);
                        if (pendingRaw) {
                            // show a subtle notice (non-blocking)
                            setTimeout(()=>{
                                alert('Pembayaran belum selesai. Klik tombol Bayar untuk melanjutkan metode pembayaran yang dipilih.');
                            }, 200);
                        }
                    } catch(_e){}
                }
            });
        }catch(err){
            console.error(err);
            alert('Gagal menginisiasi pembayaran: ' + (err?.message || 'unknown error'));
        }finally{
            btn.disabled = false;
        }
    });

    validate();

    // QRIS Core API fallback (generates QR string + PNG)
    const btnQrisFallback = document.getElementById('btnQrisFallback');
    if(btnQrisFallback && !isFree && eventId){
        btnQrisFallback.addEventListener('click', async function(ev){
            ev.preventDefault();
            const box = document.getElementById('qrisFallbackBox');
            const img = document.getElementById('qrisFallbackImg');
            const info = document.getElementById('qrisFallbackInfo');
            const str = document.getElementById('qrisFallbackString');
            if(box) box.style.display = 'block';
            if(info) info.textContent = 'Menghasilkan QRIS alternatif…';
            try{
                const res = await fetch(`/payment/${eventId}/qris-core`, { headers: { 'Accept':'application/json' }});
                const data = await res.json();
                if(data.qr_png){
                    if(img){ img.src = data.qr_png; img.style.display = 'block'; }
                    if(info){ info.textContent = 'QRIS alternatif siap. Scan menggunakan Midtrans QRIS Sandbox Simulator.'; }
                } else if(data.qr_string){
                    if(str){ str.style.display='block'; str.textContent = data.qr_string; }
                    if(info){ info.textContent = 'Salin QR content di bawah ke simulator QRIS.'; }
                } else {
                    if(info){ info.textContent = 'Gagal menghasilkan QRIS alternatif.'; }
                }
            }catch(err){
                if(info){ info.textContent = 'Terjadi kesalahan saat membuat QRIS alternatif.'; }
            }
        });
    }
});
</script>
<style>
    .btn-pay:disabled {opacity:.55; cursor:not-allowed; filter:grayscale(.15);} 
    .btn-pay.btn-disabled:hover {background:inherit;}
</style>
=======
>>>>>>> 7c287cc6e13fddde0a1fa94ce4bba305577efb13
