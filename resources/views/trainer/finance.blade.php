@extends('layouts.trainer')

@section('title', 'Finance Trainer')

@php
    $pageTitle = 'Finance';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Finance']
    ];
@endphp

@section('content')
    <div style="display:grid;gap:16px;">
        <section style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
            <p style="margin:0 0 8px 0;font-size:13px;color:#6b7280;letter-spacing:.08em;font-weight:600;">TOTAL PENDAPATAN
            </p>
            <h2 style="margin:0;color:#0f172a;">Rp {{ number_format($totalEarned, 0, ',', '.') }}</h2>
            <p style="margin:6px 0 0 0;font-size:13px;color:#64748b;">Akumulasi pembayaran settled dari course/event yang
                Anda ampu.</p>
        </section>

        @if($payouts->count() > 0)
        <section style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
            <h3 style="margin:0 0 14px 0;color:#0f172a;">Gaji & Nota Pembayaran</h3>
            <div style="overflow:auto;">
                <table style="width:100%;border-collapse:collapse;min-width:740px;">
                    <thead>
                        <tr style="background:#fffcf1;color:#856404;">
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #ffeeba;">Periode</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #ffeeba;">Nominal</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #ffeeba;">Nota Gaji</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #ffeeba;">Bukti Transfer</th>
                            <th style="text-align:left;padding:10px;border-bottom:1px solid #ffeeba;">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                            <tr>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                    {{ date('F', mktime(0, 0, 0, $payout->month, 1)) }} {{ $payout->year }}
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#0f172a;font-weight:600;">
                                    Rp {{ number_format($payout->amount, 0, ',', '.') }}
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    @if($payout->salary_slip)
                                        <a href="{{ asset('storage/' . $payout->salary_slip) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0" style="font-size: 11px;">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> Lihat Nota
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                                    @if($payout->proof_of_payment)
                                        <a href="{{ asset('storage/' . $payout->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-success py-0" style="font-size: 11px;">
                                            <i class="bi bi-image me-1"></i> Lihat Bukti
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#64748b;font-size: 12px;">
                                    {{ $payout->note ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

        <section style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
            <h3 style="margin:0 0 14px 0;color:#0f172a;">Riwayat Pembayaran</h3>

            @if($payments->count() === 0)
                <p style="margin:0;color:#64748b;">Belum ada pembayaran yang masuk.</p>
            @else
                <div style="overflow:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:740px;">
                        <thead>
                            <tr style="background:#f8fafc;color:#334155;">
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Tanggal</th>
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Order ID</th>
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Peserta</th>
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Sumber</th>
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Metode</th>
                                <th style="text-align:right;padding:10px;border-bottom:1px solid #e2e8f0;">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        {{ optional($payment->created_at)->format('d M Y H:i') }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        {{ $payment->order_id ?: '-' }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        {{ optional($payment->user)->name ?: '-' }}</td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        {{ optional($payment->course)->name ?: optional($payment->event)->title ?: '-' }}
                                    </td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        {{ strtoupper($payment->method ?? '-') }}</td>
                                    <td
                                        style="padding:10px;border-bottom:1px solid #f1f5f9;color:#0f172a;text-align:right;font-weight:600;">
                                        Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top:14px;">
                    {{ $payments->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection