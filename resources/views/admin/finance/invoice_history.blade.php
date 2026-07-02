@extends('layouts.admin')

@section('title', 'History Invoice & Receipt')

@section('navbar')
    @include('partials.navbar-finance')
@endsection

@section('styles')
    @include('partials.finance-styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--ids-secondary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--ids-secondary); border-radius: 2px; }

    .stat-card {
        background: #fff; border: 1px solid var(--ids-border);
        border-radius: 16px; padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        transition: transform 0.18s, box-shadow 0.18s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(0,0,0,0.08); }
    .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem; flex-shrink: 0;
    }
    .stat-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; margin-bottom: 4px; }
    .stat-value { font-size: 1.45rem; font-weight: 800; color: #111827; line-height: 1; }
    .stat-sub { font-size: 0.75rem; color: #6b7280; margin-top: 4px; }

    .crm-card {
        background: #fff; border: 1px solid var(--ids-border);
        border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        margin-bottom: 1.5rem; overflow: hidden;
    }
    .crm-card-header {
        padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--ids-border);
        display: flex; justify-content: space-between; align-items: center;
    }
    .crm-card-title { font-size: 1.1rem; font-weight: 700; color: var(--ids-text-main); margin: 0; }

    .crm-table { width: 100%; margin-bottom: 0; border-collapse: collapse; }
    .crm-table th {
        background: #f8f9fa; color: #6b7280;
        font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;
        padding: 0.9rem 1.25rem; border-bottom: 1px solid var(--ids-border);
    }
    .crm-table td {
        padding: 0.95rem 1.25rem; vertical-align: middle;
        border-bottom: 1px solid #f3f4f6; color: var(--ids-text-main); font-size: 0.875rem;
    }
    .crm-table tbody tr:hover { background: #fafbfc; }
    .crm-table tbody tr:last-child td { border-bottom: none; }

    .badge-type { padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }
    .badge-event  { background: #dbeafe; color: #1d4ed8; }
    .badge-course { background: #f0fdf4; color: #15803d; }
    .badge-manual { background: #f3f4f6; color: #6b7280; }
    .badge-settled { background: #d1fae5; color: #065f46; padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }

    .btn-download {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 8px;
        background: #fef3c7; color: #92400e;
        font-size: 0.78rem; font-weight: 700;
        text-decoration: none; border: 1px solid #fde68a;
        transition: all 0.18s;
    }
    .btn-download:hover { background: #fbbf24; color: #1c1917; transform: translateY(-1px); box-shadow: 0 3px 10px rgba(251,191,36,0.4); }

    .order-id-badge {
        font-family: 'Courier New', monospace; font-size: 0.75rem; font-weight: 700;
        color: #4f46e5; background: #eef2ff; padding: 3px 8px; border-radius: 6px;
    }

    .filter-bar {
        background: #fff; border: 1px solid var(--ids-border); border-radius: 14px;
        padding: 1rem 1.25rem; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    }
    .filter-label { font-size: 0.78rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; white-space: nowrap; }

    .summary-banner {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-radius: 12px; padding: 14px 20px; margin-bottom: 1.5rem;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;
    }
    .summary-banner .lbl { font-size: 0.85rem; font-weight: 700; color: #065f46; }
    .summary-banner .sub { font-size: 0.75rem; color: #047857; }
    .summary-banner .val { font-size: 1.5rem; font-weight: 800; color: #16a34a; }
</style>
@endsection

@section('content')
<div class="finance-wrapper" style="margin-top: 0;">
    @include('partials.finance-sidebar')

    <main class="finance-main">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <div class="page-eyebrow">Finance Dashboard</div>
                <h1 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 4px;">History Invoice &amp; Receipt</h1>
                <p class="text-muted mb-0">Riwayat semua transaksi settled — unduh invoice PDF kapan saja.</p>
            </div>
            <a href="{{ route('admin.finance.export', ['period' => 'this_month', 'format' => 'pdf']) }}"
               class="btn btn-sm btn-outline-secondary rounded-pill px-3 mt-1" target="_blank">
                <i class="bi bi-download me-1"></i> Export Laporan
            </a>
        </div>

        {{-- Summary Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#dbeafe;"><i class="bi bi-receipt" style="color:#1d4ed8;"></i></div>
                    <div>
                        <div class="stat-label">Total Invoice</div>
                        <div class="stat-value">{{ number_format($totalInvoices) }}</div>
                        <div class="stat-sub">Seluruh transaksi settled</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#d1fae5;"><i class="bi bi-cash-stack" style="color:#16a34a;"></i></div>
                    <div>
                        <div class="stat-label">Total Nilai Invoice</div>
                        <div class="stat-value" style="font-size:1.1rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
                        <div class="stat-sub">Omzet keseluruhan</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="stat-card d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:#fef3c7;"><i class="bi bi-funnel-fill" style="color:#d97706;"></i></div>
                    <div>
                        <div class="stat-label">Hasil Filter</div>
                        <div class="stat-value" style="font-size:1.1rem;">Rp {{ number_format($filteredTotal, 0, ',', '.') }}</div>
                        <div class="stat-sub">{{ number_format($filteredCount) }} invoice ditemukan</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <form action="{{ route('admin.finance.invoice-history') }}" method="GET" class="filter-bar">
            <span class="filter-label"><i class="bi bi-funnel-fill me-1"></i>Filter</span>
            <select name="month" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Bulan</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
            <select name="year" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                @for($y = date('Y'); $y >= 2023; $y--)
                    <option value="{{ $y }}" {{ (request('year') ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <select name="type" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="event"  {{ request('type') === 'event'  ? 'selected' : '' }}>Event</option>
                <option value="course" {{ request('type') === 'course' ? 'selected' : '' }}>Course</option>
                <option value="other"  {{ request('type') === 'other'  ? 'selected' : '' }}>Manual / Lainnya</option>
            </select>
            @if(request('month') || request('year') || request('type'))
                <a href="{{ route('admin.finance.invoice-history') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>

        {{-- Active Filter Banner --}}
        @if(request('month') || request('year') || request('type'))
        <div class="summary-banner">
            <div>
                <div class="lbl"><i class="bi bi-calendar-check-fill me-2"></i>Filter aktif:
                    @if(request('month')) {{ \Carbon\Carbon::create()->month((int)request('month'))->translatedFormat('F') }} @endif
                    @if(request('year')) {{ request('year') }} @endif
                    @if(request('type')) — {{ ['event'=>'Event','course'=>'Course','other'=>'Manual'][request('type')] ?? '' }} @endif
                </div>
                <div class="sub">{{ number_format($filteredCount) }} invoice ditemukan</div>
            </div>
            <div class="text-end">
                <div class="sub">Total Nilai</div>
                <div class="val">Rp {{ number_format($filteredTotal, 0, ',', '.') }}</div>
            </div>
        </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Invoice Table --}}
        <div class="crm-card">
            <div class="crm-card-header">
                <h2 class="crm-card-title"><i class="bi bi-receipt me-2 text-warning"></i>Daftar Invoice</h2>
                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill" style="font-weight:700;">
                    {{ $invoices->total() }} Invoice
                </span>
            </div>
            <div class="table-responsive">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>NO. INVOICE</th>
                            <th>USER / PELANGGAN</th>
                            <th>ITEM</th>
                            <th>TIPE</th>
                            <th>JUMLAH</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            @php
                                $invoiceNo = 'INV-' . strtoupper(substr($inv->order_id ?? 'MANUAL', 0, 8));
                                if ($inv->event_id) {
                                    $tipe = 'event';
                                    $itemName = $inv->event?->title ?? 'Event';
                                } elseif ($inv->course_id) {
                                    $tipe = 'course';
                                    $itemName = $inv->course?->name ?? 'Course';
                                } else {
                                    $tipe = 'manual';
                                    $itemName = $inv->metadata['description'] ?? 'Pemasukan Manual';
                                }
                            @endphp
                            <tr>
                                <td style="white-space:nowrap;">
                                    <div>{{ $inv->created_at->format('d M Y') }}</div>
                                    <div style="font-size:0.72rem; color:#9ca3af;">{{ $inv->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td><span class="order-id-badge">{{ $invoiceNo }}</span></td>
                                <td>
                                    <div style="font-weight:600;">{{ $inv->user?->name ?? 'Guest' }}</div>
                                    <div style="font-size:0.75rem; color:#9ca3af;">{{ $inv->user?->email ?? '-' }}</div>
                                </td>
                                <td style="max-width:220px;">
                                    <div title="{{ $itemName }}" style="font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;">{{ $itemName }}</div>
                                    <div style="font-size:0.72rem; color:#9ca3af;">{{ $inv->order_id ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($tipe === 'event')
                                        <span class="badge-type badge-event">Event</span>
                                    @elseif($tipe === 'course')
                                        <span class="badge-type badge-course">Course</span>
                                    @else
                                        <span class="badge-type badge-manual">Manual</span>
                                    @endif
                                </td>
                                <td style="font-weight:700; color:#16a34a; white-space:nowrap;">
                                    Rp {{ number_format($inv->amount, 0, ',', '.') }}
                                </td>
                                <td><span class="badge-settled">SETTLED</span></td>
                                <td>
                                    @if($inv->order_id)
                                        <a href="{{ route('invoice.manual', $inv->order_id) }}"
                                           class="btn-download" target="_blank">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Unduh PDF
                                        </a>
                                    @else
                                        <span style="font-size:0.75rem; color:#d1d5db;">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5" style="color:#9ca3af;">
                                    <i class="bi bi-inbox" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                                    Tidak ada invoice ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($invoices->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #f3f4f6;">
                    {{ $invoices->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </main>
</div>
@endsection
