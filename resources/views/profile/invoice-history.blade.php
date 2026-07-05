<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Invoice - idSPORA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; }

        .page-container { max-width: 960px; margin: 0 auto; padding: 2rem 1rem 4rem; }

        .back-link {
            display: inline-flex; align-items: center; gap: 8px;
            color: #6b7280; font-size: 0.85rem; font-weight: 600;
            text-decoration: none; margin-bottom: 1.5rem;
            transition: color 0.15s;
        }
        .back-link:hover { color: #111827; }

        .page-title { font-size: 1.75rem; font-weight: 800; color: #111827; margin-bottom: 4px; }
        .page-subtitle { font-size: 0.9rem; color: #6b7280; margin-bottom: 2rem; }

        /* Summary Cards */
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
        .summary-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
            padding: 1.25rem 1.5rem; box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }
        .summary-card .label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #9ca3af; margin-bottom: 6px; }
        .summary-card .value { font-size: 1.6rem; font-weight: 800; color: #111827; line-height: 1; }
        .summary-card .sub   { font-size: 0.75rem; color: #9ca3af; margin-top: 4px; }

        /* Filter Bar */
        .filter-bar {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 14px;
            padding: 0.9rem 1.25rem; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
        }
        .filter-bar select {
            font-size: 0.84rem; border-radius: 8px; border: 1px solid #e5e7eb;
            padding: 7px 12px; color: #374151; background: #f9fafb;
        }
        .filter-label { font-size: 0.75rem; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }

        /* Table Card */
        .table-card {
            background: #fff; border: 1px solid #e5e7eb; border-radius: 18px;
            overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        }
        .table-card-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3f4f6;
        }
        .table-card-title { font-size: 1rem; font-weight: 700; color: #111827; margin: 0; }
        .table-card-count { font-size: 0.8rem; font-weight: 700; color: #6b7280; background: #f3f4f6; padding: 4px 12px; border-radius: 20px; }

        .inv-table { width: 100%; border-collapse: collapse; }
        .inv-table th {
            background: #f9fafb; font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.5px; color: #9ca3af; padding: 0.85rem 1.25rem;
            border-bottom: 1px solid #f3f4f6; text-align: left;
        }
        .inv-table td {
            padding: 1rem 1.25rem; border-bottom: 1px solid #f9fafb;
            font-size: 0.875rem; color: #374151; vertical-align: middle;
        }
        .inv-table tbody tr:hover { background: #fafbfc; }
        .inv-table tbody tr:last-child td { border-bottom: none; }

        .inv-no {
            font-family: monospace; font-size: 0.78rem; font-weight: 700;
            color: #4f46e5; background: #eef2ff; padding: 4px 10px; border-radius: 7px;
        }
        .badge-tipe { padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }
        .badge-event  { background: #dbeafe; color: #1d4ed8; }
        .badge-course { background: #dcfce7; color: #15803d; }
        .badge-other  { background: #f3f4f6; color: #6b7280; }
        .badge-settled { background: #d1fae5; color: #065f46; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }

        .btn-inv-download {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 16px; border-radius: 10px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e; font-size: 0.8rem; font-weight: 700;
            text-decoration: none; border: 1px solid #fde68a;
            transition: all 0.2s; white-space: nowrap;
        }
        .btn-inv-download:hover {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #1c1917; transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(251,191,36,0.45);
        }

        .empty-state { text-align: center; padding: 4rem 1rem; color: #9ca3af; }
        .empty-state i { font-size: 3rem; display: block; margin-bottom: 1rem; }
        .empty-state h3 { font-size: 1.1rem; font-weight: 700; color: #374151; margin-bottom: 6px; }
        .empty-state p { font-size: 0.875rem; }

        .amount-cell { font-weight: 700; color: #16a34a; white-space: nowrap; }
    </style>
</head>
<body>
    @include('partials.navbar-after-login')

    <div class="page-container">

        {{-- Back Navigation --}}
        <a href="{{ route('profile.index') }}" class="back-link">
            <i class="bi bi-arrow-left-circle"></i> Kembali ke Profil
        </a>

        {{-- Header --}}
        <h1 class="page-title"><i class="bi bi-receipt me-2" style="color:#f59e0b;"></i>Riwayat Invoice Saya</h1>
        <p class="page-subtitle">Unduh invoice PDF untuk setiap transaksi yang sudah selesai kapan saja.</p>

        {{-- Summary Cards --}}
        <div class="summary-grid">
            <div class="summary-card">
                <div class="label">Total Invoice</div>
                <div class="value">{{ number_format($totalInvoiceCount) }}</div>
                <div class="sub">Transaksi diselesaikan</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Pembelian</div>
                <div class="value" style="font-size:1.25rem; color:#16a34a;">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div>
                <div class="sub">Semua waktu</div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <form action="{{ route('profile.invoice-history') }}" method="GET" class="filter-bar">
            <span class="filter-label"><i class="bi bi-funnel me-1"></i>Filter</span>
            <select name="type" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="event"  {{ request('type') === 'event'  ? 'selected' : '' }}>Event</option>
                <option value="course" {{ request('type') === 'course' ? 'selected' : '' }}>Course</option>
            </select>
            <select name="year" class="form-select form-select-sm" style="width:auto;" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @for($y = date('Y'); $y >= 2023; $y--)
                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            @if(request('type') || request('year'))
                <a href="{{ route('profile.invoice-history') }}" class="btn btn-sm btn-outline-secondary rounded-pill">
                    <i class="bi bi-x"></i> Reset
                </a>
            @endif
        </form>

        {{-- Table --}}
        <div class="table-card">
            <div class="table-card-header">
                <h2 class="table-card-title"><i class="bi bi-receipt me-2 text-warning"></i>Daftar Invoice</h2>
                <span class="table-card-count">{{ $invoices->total() }} Invoice</span>
            </div>
            <div class="table-responsive">
                <table class="inv-table">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>NO. INVOICE</th>
                            <th>ITEM</th>
                            <th>TIPE</th>
                            <th>JUMLAH</th>
                            <th>STATUS</th>
                            <th>UNDUH</th>
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
                                    $tipe = 'other';
                                    $itemName = $inv->metadata['description'] ?? 'Transaksi';
                                }
                            @endphp
                            <tr>
                                <td style="white-space:nowrap;">
                                    <div style="font-weight:600;">{{ $inv->created_at->format('d M Y') }}</div>
                                    <div style="font-size:0.72rem; color:#9ca3af;">{{ $inv->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td><span class="inv-no">{{ $invoiceNo }}</span></td>
                                <td style="max-width:220px;">
                                    <div title="{{ $itemName }}" style="font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px;">{{ $itemName }}</div>
                                    <div style="font-size:0.72rem; color:#9ca3af;">{{ $inv->order_id ?? '-' }}</div>
                                </td>
                                <td>
                                    @if($tipe === 'event')
                                        <span class="badge-tipe badge-event">Event</span>
                                    @elseif($tipe === 'course')
                                        <span class="badge-tipe badge-course">Course</span>
                                    @else
                                        <span class="badge-tipe badge-other">Lainnya</span>
                                    @endif
                                </td>
                                <td class="amount-cell">Rp {{ number_format($inv->amount, 0, ',', '.') }}</td>
                                <td><span class="badge-settled">LUNAS</span></td>
                                <td>
                                    @if($inv->order_id)
                                        <a href="{{ route('invoice.manual', $inv->order_id) }}"
                                           class="btn-inv-download" target="_blank">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Invoice PDF
                                        </a>
                                    @else
                                        <span style="font-size:0.75rem; color:#d1d5db;">Tidak tersedia</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-receipt"></i>
                                        <h3>Belum ada invoice</h3>
                                        <p>Invoice akan muncul di sini setelah Anda menyelesaikan pembayaran untuk event atau course.</p>
                                        <a href="{{ route('events.index') }}" class="btn btn-warning btn-sm mt-2 rounded-pill px-4">
                                            Jelajahi Event
                                        </a>
                                    </div>
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
    </div>

    @include('partials.footer-after-login')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
