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