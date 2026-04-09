@include('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Referral - Dashboard Reseller IdSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(255, 193, 7, 0.22), transparent 28%),
                radial-gradient(circle at top right, rgba(33, 37, 41, 0.08), transparent 25%),
                linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
        }

        .page-shell {
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            min-height: 100vh;
        }

        .page-shell::before,
        .page-shell::after {
            content: '';
            position: fixed;
            border-radius: 999px;
            filter: blur(50px);
            pointer-events: none;
            z-index: 0;
            opacity: 0.45;
        }

        .page-shell::before {
            width: 220px;
            height: 220px;
            background: rgba(255, 193, 7, 0.35);
            top: 12%;
            right: -70px;
        }

        .page-shell::after {
            width: 260px;
            height: 260px;
            background: rgba(13, 110, 253, 0.12);
            bottom: 6%;
            left: -90px;
        }

        .history-hero {
            background: linear-gradient(135deg, #111827 0%, #1f2937 50%, #495057 100%);
            color: #fff;
        }

        .history-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.9);
        }

        .action-group .btn {
            min-width: 132px;
        }

        .stat-chip {
            border: 1px solid rgba(255, 193, 7, 0.18);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.92), rgba(255, 248, 225, 0.92));
        }
    </style>
</head>

<body class="page-shell">
    <main class="pt-4 mt-4 position-relative" style="z-index: 1;">
        <div class="container-xxl pb-5">
            <div class="card history-hero border-0 shadow-lg rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 align-items-start">
                        <div>
                            <div class="d-inline-flex align-items-center gap-2 badge rounded-pill bg-warning text-dark px-3 py-2 mb-3">
                                <i class="bi bi-clock-history"></i>
                                Riwayat Referral
                            </div>
                            <h1 class="fw-bold mb-2">Semua aktivitas referral dalam satu tampilan.</h1>
                            <p class="text-white-50 mb-0" style="max-width: 720px;">
                                Pantau transaksi referral, status komisi, dan progres pembayaran Anda.
                            </p>
                        </div>

                        <div class="action-group d-flex flex-column flex-sm-row gap-2 ms-lg-auto align-items-stretch align-items-sm-center">
                            <a href="{{ route('reseller.index') }}" class="btn btn-light fw-bold px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <a href="{{ route('reseller.history.download') }}" class="btn btn-warning fw-bold px-4">
                                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Unduh PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 history-card mb-4">
                <div class="card-body p-4 p-lg-4">
                    <form method="GET" action="{{ route('reseller.history') }}" class="row g-3 align-items-end">
                        <div class="col-lg-6">
                            <label class="form-label fw-semibold text-muted small">Cari referral</label>
                            <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-lg"
                                placeholder="Nama, email, atau deskripsi transaksi">
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label fw-semibold text-muted small">Status</label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="">Semua status</option>
                                <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                            </select>
                        </div>
                        <div class="col-lg-3 d-flex gap-2 justify-content-lg-end">
                            <button type="submit" class="btn btn-warning fw-bold flex-grow-1 flex-lg-grow-0 px-4">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('reseller.history') }}" class="btn btn-outline-secondary fw-bold px-4">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card history-card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted">Total Komisi Terbayar</small>
                            <h3 class="mb-0 text-success fw-bold">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card history-card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted">Komisi Pending</small>
                            <h3 class="mb-0 text-warning fw-bold">Rp {{ number_format($totalPending, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card history-card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted">Komisi Ditolak</small>
                            <h3 class="mb-0 text-danger fw-bold">Rp {{ number_format($totalRejected, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 history-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">Daftar Riwayat Referral</h4>
                            <p class="text-muted mb-0">Menampilkan semua transaksi referral yang tercatat untuk akun Anda.</p>
                        </div>
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2">{{ $history->total() }} hasil</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="border-0 py-3">Tanggal</th>
                                    <th class="border-0 py-3">Pengguna Baru</th>
                                    <th class="border-0 py-3">Detail</th>
                                    <th class="border-0 py-3 text-center">Status</th>
                                    <th class="border-0 py-3 text-end">Komisi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                    @php
                                        $status = strtolower($item->status);
                                        $badgeClass = $status === 'paid' ? 'bg-success text-success' : ($status === 'rejected' ? 'bg-danger text-danger' : 'bg-warning text-warning');
                                        $isRejected = $status === 'rejected';
                                    @endphp
                                    <tr>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark">{{ $item->created_at->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $item->created_at->format('H:i') }} WIB</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark">{{ $item->referredUser->name ?? 'Pengguna Baru' }}</div>
                                            <small class="text-muted">{{ $item->referredUser->email ?? '-' }}</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-dark {{ $isRejected ? 'text-decoration-line-through opacity-75' : '' }}">
                                                {{ $item->description ?? 'Pembelian event atau course' }}
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge {{ $badgeClass }} bg-opacity-10 rounded-pill px-3 py-2">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-end">
                                            <div class="fw-bold {{ $isRejected ? 'text-danger text-decoration-line-through opacity-75' : 'text-dark' }}">
                                                {{ $isRejected ? '-' : '+' }}Rp {{ number_format($item->amount, 0, ',', '.') }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                            Belum ada riwayat referral.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $history->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer-after-login')
</body>

</html>