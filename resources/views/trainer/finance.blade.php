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
        <!-- Realized earnings (from manual_payments) -->
        <section style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
            <p style="margin:0 0 8px 0;font-size:13px;color:#6b7280;letter-spacing:.08em;font-weight:600;">
                TOTAL PENDAPATAN (TERCATAT)
            </p>
            <h2 style="margin:0;color:#0f172a;">Rp {{ number_format($totalEarned ?? 0, 0, ',', '.') }}</h2>
            <p style="margin:6px 0 0 0;font-size:13px;color:#64748b;">
                Akumulasi pembayaran settled dari course/event yang Anda ampu (data dari admin finance).
            </p>
        </section>

        <!-- Estimated earnings (by event & course) -->
        <section style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
            <p style="margin:0 0 8px 0;font-size:13px;color:#6b7280;letter-spacing:.08em;font-weight:600;">
                ESTIMASI PENDAPATAN (Berdasarkan skema & pendaftar)
            </p>
            <h2 style="margin:0;color:#0f172a;">Rp {{ number_format($estimatedTotal ?? 0, 0, ',', '.') }}</h2>
            <p style="margin:6px 0 14px 0;font-size:13px;color:#64748b;">
                Estimasi = total event (fee per peserta * peserta aktif) + total course (harga * peserta aktif * persen trainer).
            </p>

            <div style="display:flex;gap:20px;flex-wrap:wrap;">
                <div style="flex:1;min-width:300px;">
                    <h4 style="margin:0 0 8px 0;color:#0f172a;">Event</h4>
                    @if(isset($events) && $events->count() > 0)
                        @foreach($events as $row)
                            <div style="padding:8px 0;border-bottom:1px dashed #eee;color:#334155;">
                                <div style="font-weight:600;">{{ $row['event']->title ?? '-' }}</div>
                                <div style="font-size:13px;color:#64748b;">
                                    Peserta aktif: {{ number_format($row['active_participants_count'] ?? ($row['event']->active_participants_count ?? 0)) }} —
                                    Fee Trainer: Rp {{ number_format($row['fee_trainer'] ?? 0, 0, ',', '.') }} —
                                    Estimasi: Rp {{ number_format($row['estimated_fee'] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="color:#64748b;">Tidak ada event.</div>
                    @endif
                </div>

                <div style="flex:1;min-width:300px;">
                    <h4 style="margin:0 0 8px 0;color:#0f172a;">Course</h4>
                    @if(isset($courses) && count($courses) > 0)
                        @foreach($courses as $row)
                            <div style="padding:8px 0;border-bottom:1px dashed #eee;color:#334155;">
                                <div style="font-weight:600;">{{ $row['course']->name ?? '-' }}</div>
                                <div style="font-size:13px;color:#64748b;">
                                    Peserta aktif: {{ number_format($row['active_students'] ?? 0) }} —
                                    Skema: {{ $row['scheme_percent'] ?? 0 }}% —
                                    Estimasi: Rp {{ number_format($row['estimated_revenue'] ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="color:#64748b;">Tidak ada course.</div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Payment history (realized) -->
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
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#0f172a;text-align:right;font-weight:600;">
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

        <!-- Payout / Disbursement History -->
        <section style="background:#fff;border:1px solid #e5e7eb;border-radius:16px;padding:20px;">
            <h3 style="margin:0 0 14px 0;color:#0f172a;">Riwayat Pencairan Dana & Fee</h3>

            @if($payouts->count() === 0)
                <p style="margin:0;color:#64748b;">Belum ada riwayat pencairan dana.</p>
            @else
                <div style="overflow:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:740px;">
                        <thead>
                            <tr style="background:#f8fafc;color:#334155;">
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Tanggal</th>
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Deskripsi Pencairan</th>
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Tipe</th>
                                <th style="text-align:left;padding:10px;border-bottom:1px solid #e2e8f0;">Status</th>
                                <th style="text-align:right;padding:10px;border-bottom:1px solid #e2e8f0;">Jumlah</th>
                                <th style="text-align:center;padding:10px;border-bottom:1px solid #e2e8f0;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payouts as $payout)
                                <tr>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        {{ $payout->payment_date ? $payout->payment_date->format('d M Y H:i') : $payout->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        <div style="font-weight:600;">{{ $payout->title }}</div>
                                        @if($payout->notes)
                                            <div style="font-size:12px;color:#64748b;font-style:italic;margin-top:2px;">
                                                Catatan: "{{ $payout->notes }}"
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        <span style="font-size: 12px; font-weight: 700; text-transform: uppercase;">
                                            {{ $payout->type == 'course_payout' ? 'Course Payout' : 'Event Fee' }}
                                        </span>
                                    </td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#334155;">
                                        @if($payout->status == 'approved')
                                            <span style="display:inline-block;padding:4px 8px;font-size:11px;font-weight:bold;color:#10b981;background:#e6f9f0;border-radius:12px;">LUNAS</span>
                                        @elseif($payout->status == 'pending')
                                            <span style="display:inline-block;padding:4px 8px;font-size:11px;font-weight:bold;color:#f59e0b;background:#fffbeb;border-radius:12px;">PROSES</span>
                                        @else
                                            <span style="display:inline-block;padding:4px 8px;font-size:11px;font-weight:bold;color:#ef4444;background:#fef2f2;border-radius:12px;">DITOLAK</span>
                                        @endif
                                    </td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;color:#0f172a;text-align:right;font-weight:600;">
                                        Rp {{ number_format($payout->amount, 0, ',', '.') }}
                                    </td>
                                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;text-align:center;">
                                        @if($payout->status == 'approved')
                                            <a href="{{ route('trainer.finance.payouts.invoice', $payout->id) }}" target="_blank" 
                                               style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;font-size:12px;font-weight:bold;color:#fff;background:linear-gradient(135deg, #FFB703 0%, #FB8500 100%);border-radius:8px;text-decoration:none;box-shadow:0 2px 6px rgba(251,133,0,0.15);transition:all 0.2s ease;"
                                               onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 10px rgba(251,133,0,0.25)';"
                                               onmouseout="this.style.transform='none';this.style.boxShadow='0 2px 6px rgba(251,133,0,0.15)';">
                                                <i class="bi bi-file-earmark-text"></i> Invoice
                                            </a>
                                        @else
                                            <span style="color:#94a3b8;font-size:12px;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection