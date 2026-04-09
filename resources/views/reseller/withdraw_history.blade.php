@include('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penarikan Dana - Dashboard Reseller IdSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background:
                radial-gradient(circle at top right, rgba(255, 193, 7, 0.2), transparent 30%),
                radial-gradient(circle at bottom left, rgba(25, 135, 84, 0.12), transparent 28%),
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
            background: rgba(255, 193, 7, 0.32);
            top: 10%;
            left: -60px;
        }

        .page-shell::after {
            width: 260px;
            height: 260px;
            background: rgba(25, 135, 84, 0.14);
            bottom: 8%;
            right: -80px;
        }

        .history-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1f2937 55%, #3b4252 100%);
            color: #fff;
        }

        .history-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.92);
        }

        .action-group .btn {
            min-width: 132px;
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
                                <i class="bi bi-wallet2"></i>
                                Riwayat Penarikan Dana
                            </div>
                            <h1 class="display-6 fw-bold mb-2">Rekap semua pengajuan penarikan Anda.</h1>
                            <p class="text-white-50 mb-0" style="max-width: 720px;">
                                Lacak status approved, pending, dan rejected dalam satu halaman dengan tampilan yang lebih ringkas dan mudah dipindai.
                            </p>
                        </div>

                        <div class="action-group d-flex flex-column flex-sm-row gap-2 ms-lg-auto align-items-stretch align-items-sm-center">
                            <a href="{{ route('reseller.index') }}" class="btn btn-light fw-bold px-4">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <a href="{{ route('reseller.withdraw.download') }}" class="btn btn-warning fw-bold px-4">
                                <i class="bi bi-cloud-arrow-down-fill me-1"></i> Unduh PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 history-card mb-4">
                <div class="card-body p-4 p-lg-4">
                    <form method="GET" action="{{ route('reseller.withdraw.history') }}" class="row g-3 align-items-end">
                        <div class="col-lg-6">
                            <label class="form-label fw-semibold text-muted small">Cari penarikan</label>
                            <input type="search" name="search" value="{{ request('search') }}" class="form-control form-control-lg"
                                placeholder="Bank, nomor rekening, atau nama pemilik rekening">
                        </div>
                        <div class="col-lg-3">
                            <label class="form-label fw-semibold text-muted small">Status</label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="">Semua status</option>
                                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                            </select>
                        </div>
                        <div class="col-lg-3 d-flex gap-2 justify-content-lg-end">
                            <button type="submit" class="btn btn-warning fw-bold flex-grow-1 flex-lg-grow-0 px-4">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('reseller.withdraw.history') }}" class="btn btn-outline-secondary fw-bold px-4">
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
                            <small class="text-muted">Dana Approved</small>
                            <h3 class="mb-0 text-success fw-bold">Rp {{ number_format($totalApproved, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card history-card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted">Dana Pending</small>
                            <h3 class="mb-0 text-warning fw-bold">Rp {{ number_format($totalPending, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card history-card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4">
                            <small class="text-muted">Dana Rejected</small>
                            <h3 class="mb-0 text-danger fw-bold">Rp {{ number_format($totalRejected, 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 history-card">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">Daftar Riwayat Penarikan</h4>
                            <p class="text-muted mb-0">Semua pengajuan penarikan tersusun berdasarkan yang terbaru.</p>
                        </div>
                        <span class="badge rounded-pill bg-warning text-dark px-3 py-2">{{ $withdrawals->total() }} hasil</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="border-0 py-3">ID Penarikan</th>
                                    <th class="border-0 py-3">Tanggal Pengajuan</th>
                                    <th class="border-0 py-3">Total</th>
                                    <th class="border-0 py-3 text-center">Status</th>
                                    <th class="border-0 py-3">Tanggal Diproses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $wd)
                                    @php
                                        $status = strtolower($wd->status);
                                        $isRejected = $status === 'rejected';
                                        $statusClass = $status === 'approved' ? 'bg-success text-success' : ($isRejected ? 'bg-danger text-danger' : 'bg-warning text-warning');
                                    @endphp
                                    <tr>
                                        <td class="py-3">
                                            <div class="fw-bold text-dark {{ $isRejected ? 'text-decoration-line-through opacity-75' : '' }}">#WD-{{ str_pad($wd->id, 4, '0', STR_PAD_LEFT) }}</div>
                                            <small class="text-muted">{{ $wd->bank_name }}</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-dark {{ $isRejected ? 'text-decoration-line-through opacity-75' : '' }}">{{ $wd->created_at->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $wd->created_at->format('H:i') }} WIB</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-bold {{ $isRejected ? 'text-danger text-decoration-line-through opacity-75' : 'text-dark' }}">Rp {{ number_format($wd->amount, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <span class="badge {{ $statusClass }} bg-opacity-10 rounded-pill px-3 py-2">
                                                {{ ucfirst($wd->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            @if($wd->status !== 'pending')
                                                <div class="text-dark">{{ $wd->updated_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $wd->updated_at->format('H:i') }} WIB</small>
                                            @else
                                                <span class="text-muted fst-italic">Belum diproses</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="bi bi-wallet2 fs-1 d-block mb-3 opacity-25"></i>
                                            Belum ada riwayat penarikan dana.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $withdrawals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('partials.footer-after-login')
</body>

</html>