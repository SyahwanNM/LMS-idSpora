@extends('layouts.app')
@section('title', 'Pembayaran Tahap 2 - ' . $event->title)
@section('content')
@php
    $midtransClientKey = (string) config('midtrans.client_key');
    $midtransIsProduction = (bool) config('midtrans.is_production', false);
    $amount = (float) ($event->price_stage2 ?? 0);
    $hasMidtrans = !empty($midtransClientKey) && $event->accept_online_payment;
    $hasManual   = (bool) $event->accept_manual_transfer;
@endphp
@if($hasMidtrans)
    <script src="https://app{{ $midtransIsProduction ? '' : '.sandbox' }}.midtrans.com/snap/snap.js" data-client-key="{{ $midtransClientKey }}"></script>
@endif

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; background: #f0f4ff; }

    .stg2-hero {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 60%, #a855f7 100%);
        color: #fff;
        padding: 3rem 2rem 6rem;
        text-align: center;
    }
    .stg2-hero .badge-stage {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.35);
        border-radius: 999px;
        padding: 4px 16px;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }
    .stg2-hero h1 { font-size: 1.85rem; font-weight: 800; margin-bottom: 0.4rem; }
    .stg2-hero p { opacity: 0.85; font-size: 0.95rem; }

    .payment-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 8px 40px rgba(79,70,229,0.12);
        padding: 2rem;
        margin-top: -3.5rem;
        position: relative;
    }
    .amount-box {
        background: linear-gradient(135deg, #eef2ff, #f5f3ff);
        border: 2px solid #c7d2fe;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .amount-box .label { font-size: 0.8rem; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
    .amount-box .value { font-size: 2rem; font-weight: 800; color: #4f46e5; margin-top: 4px; }

    .method-tab {
        display: flex; gap: 10px; margin-bottom: 1.5rem;
    }
    .method-btn {
        flex: 1; padding: 10px 16px; border: 2px solid #e5e7eb;
        background: #f9fafb; border-radius: 12px; cursor: pointer;
        font-weight: 600; font-size: 0.85rem; color: #374151;
        transition: all .2s; text-align: center;
    }
    .method-btn.active { border-color: #4f46e5; background: #eef2ff; color: #4f46e5; }

    .pay-btn {
        width: 100%; padding: 14px; border: none; border-radius: 14px;
        font-size: 1rem; font-weight: 700; cursor: pointer;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        color: #fff; transition: all .2s; display: flex; align-items: center; justify-content: center; gap: 8px;
    }
    .pay-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(79,70,229,0.35); }
    .pay-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .bank-card {
        background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px;
        padding: 1rem 1.2rem; margin-bottom: 1rem;
    }
    .bank-card .bank-name { font-weight: 700; color: #1e293b; }
    .bank-card .account-no { font-size: 1.2rem; font-weight: 800; color: #4f46e5; letter-spacing: 1px; }
    .bank-card .account-holder { font-size: 0.8rem; color: #64748b; }

    .alert-reviewing { background: #fff7ed; border: 1.5px solid #fed7aa; border-radius: 12px; padding: 1rem 1.2rem; }
</style>

<div class="stg2-hero">
    <span class="badge-stage">🏆 Pembayaran Tahap 2</span>
    <h1>{{ $event->title }}</h1>
    <p>Selesaikan pembayaran untuk mengakses Submission Tahap 2</p>
</div>

<div class="container" style="max-width: 560px;">
    <div class="payment-card">

        @if(session('success'))
            <div class="alert alert-success rounded-3 mb-3">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger rounded-3 mb-3">{{ session('error') }}</div>
        @endif

        {{-- Amount Box --}}
        <div class="amount-box">
            <div class="label">Total Pembayaran Tahap 2</div>
            <div class="value">Rp {{ number_format($amount, 0, ',', '.') }}</div>
        </div>

        @if($existingPayment)
            {{-- Already submitted, waiting review --}}
            <div class="alert-reviewing">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-hourglass-split text-warning fs-5"></i>
                    <strong>Bukti Pembayaran Sedang Direview</strong>
                </div>
                <p class="mb-0 small text-muted">Bukti pembayaran Anda telah dikirim pada {{ $existingPayment->created_at->format('d M Y H:i') }}. Mohon tunggu konfirmasi admin.</p>
            </div>
        @else
            {{-- Method Tabs --}}
            @php $defaultTab = $hasMidtrans ? 'midtrans' : 'manual'; @endphp
            <div class="method-tab">
                @if($hasMidtrans)
                    <button type="button" class="method-btn {{ $defaultTab === 'midtrans' ? 'active' : '' }}" id="tab-midtrans" onclick="switchTab('midtrans')">
                        💳 Online Payment
                    </button>
                @endif
                @if($hasManual)
                    <button type="button" class="method-btn {{ $defaultTab === 'manual' ? 'active' : '' }}" id="tab-manual" onclick="switchTab('manual')">
                        🏦 Transfer Manual
                    </button>
                @endif
            </div>

            {{-- Midtrans Section --}}
            @if($hasMidtrans)
            <div id="section-midtrans" class="{{ $defaultTab !== 'midtrans' ? 'd-none' : '' }}">
                <p class="small text-muted mb-3">Bayar menggunakan kartu kredit, transfer bank, e-wallet, atau QRIS melalui Midtrans.</p>
                <button type="button" class="pay-btn" id="midtransPayBtn" onclick="payWithMidtrans()">
                    <i class="bi bi-credit-card-fill"></i> Bayar Sekarang
                </button>
            </div>
            @endif

            {{-- Manual Transfer Section --}}
            @if($hasManual)
            <div id="section-manual" class="{{ $defaultTab !== 'manual' ? 'd-none' : '' }}">
                <div class="bank-card">
                    <div class="bank-name">{{ $event->bank_name }}</div>
                    <div class="account-no">{{ $event->bank_account_number }}</div>
                    <div class="account-holder">a.n. {{ $event->bank_account_holder }}</div>
                </div>
                <p class="small text-muted mb-3">Transfer tepat <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong> ke rekening di atas, lalu unggah bukti di bawah.</p>

                <form method="POST" action="{{ route('events.payment.stage2.manual', $event) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" name="whatsapp_number" class="form-control" placeholder="08xxxxxxxxxx" required value="{{ old('whatsapp_number', auth()->user()->phone) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" name="payment_proof" class="form-control" accept="image/*,.pdf" required>
                        <div class="form-text">Format: JPG, PNG, PDF. Maks 5 MB.</div>
                    </div>
                    @if($errors->any())
                        <div class="alert alert-danger small py-2">
                            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                        </div>
                    @endif
                    <button type="submit" class="pay-btn">
                        <i class="bi bi-send-fill"></i> Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>
            @endif
        @endif

        <div class="mt-3 text-center">
            <a href="{{ route('events.registered.detail', $event) }}" class="text-muted small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke detail event
            </a>
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.method-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tab)?.classList.add('active');
        ['midtrans', 'manual'].forEach(t => {
            const el = document.getElementById('section-' + t);
            if (el) el.classList.toggle('d-none', t !== tab);
        });
    }

    async function payWithMidtrans() {
        const btn = document.getElementById('midtransPayBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';

        try {
            const res = await fetch("{{ route('events.payment.stage2.midtrans', $event) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            });
            const data = await res.json();

            if (!res.ok || !data.snap_token) {
                throw new Error(data.error || 'Gagal membuat transaksi.');
            }

            snap.pay(data.snap_token, {
                onSuccess: function(result) {
                    // Settle via backend
                    fetch("{{ route('events.payment.stage2.settle', $event) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ order_id: data.order_id })
                    }).then(() => {
                        window.location.href = "{{ route('events.registered.detail', $event) }}";
                    });
                },
                onPending: function(result) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-credit-card-fill"></i> Lanjutkan Pembayaran';
                },
                onError: function(result) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-credit-card-fill"></i> Bayar Sekarang';
                    alert('Pembayaran gagal. Silakan coba lagi.');
                },
                onClose: function() {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-credit-card-fill"></i> Bayar Sekarang';
                }
            });
        } catch (err) {
            alert(err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-credit-card-fill"></i> Bayar Sekarang';
        }
    }
</script>
@endsection
