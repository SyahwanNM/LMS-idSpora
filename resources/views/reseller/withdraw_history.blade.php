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
</head>

<body class="bg-light" style="font-family: 'Inter', system-ui, -apple-system, sans-serif;">
    <main class="pt-4 mt-4">
        <div class="container-xxl">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4 mt-2">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('reseller.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Kembali ke Dashboard">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <h3 class="mb-0 fw-semibold text-dark fs-4">Riwayat Penarikan Dana</h3>
                </div>
            </div>

            <!-- Stats Section (Single Card with Dividers) -->
            <div class="card border-0 shadow-sm mb-4 bg-white animate-fade-in" style="border-radius: 16px;">
                <div class="card-body py-4 px-3">
                    <div class="row text-center g-0">
                        <!-- Dana Approved -->
                        <div class="col-4 border-end d-flex flex-column align-items-center justify-content-center text-center">
                            <span class="text-muted d-block small mb-1" style="font-size: 0.8rem;">Dana Disetujui</span>
                            <h4 class="mb-0 text-success fw-semibold" style="font-size: 1.6rem;">Rp {{ number_format($totalApproved, 0, ',', '.') }}</h4>
                        </div>
                        <!-- Dana Pending -->
                        <div class="col-4 border-end d-flex flex-column align-items-center justify-content-center text-center">
                            <span class="text-muted d-block small mb-1" style="font-size: 0.8rem;">Dana Pending</span>
                            <h4 class="mb-0 text-warning fw-semibold" style="font-size: 1.6rem;">Rp {{ number_format($totalPending, 0, ',', '.') }}</h4>
                        </div>
                        <!-- Dana Rejected -->
                        <div class="col-4 d-flex flex-column align-items-center justify-content-center text-center">
                            <span class="text-muted d-block small mb-1" style="font-size: 0.8rem;">Dana Ditolak</span>
                            <h4 class="mb-0 text-danger fw-semibold" style="font-size: 1.6rem;">Rp {{ number_format($totalRejected, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section (Clean & Intuitive Grid) -->
            <div class="card border-0 shadow-sm mb-4 bg-white animate-fade-in delay-1" style="border-radius: 16px;">
                <div class="card-body p-3">
                    <form method="GET" action="{{ route('reseller.withdraw.history') }}" class="row g-3 align-items-center m-0">
                        <!-- Search Input -->
                        <div class="col-md-5 text-start">
                            <label class="form-label text-muted small mb-1 fw-medium">Cari Penarikan</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="search" name="search" value="{{ request('search') }}" class="form-control bg-light border-0" placeholder="Cari nama bank, no. rekening, atau pemilik...">
                            </div>
                        </div>
                        <!-- Status Dropdown -->
                        <div class="col-md-3 text-start">
                            <label class="form-label text-muted small mb-1 fw-medium">Filter Status</label>
                            <select name="status" class="form-select form-select-sm bg-light border-0">
                                <option value="">Semua Status</option>
                                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                            </select>
                        </div>
                        <!-- Filter Actions -->
                        <div class="col-md-4 d-flex gap-2 justify-content-md-end align-self-end">
                            <button type="submit" class="btn btn-warning text-dark btn-sm fw-medium rounded-pill px-4 py-2 flex-grow-1 flex-md-grow-0">
                                <i class="bi bi-funnel-fill me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('reseller.withdraw.history') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4 py-2 d-flex align-items-center justify-content-center">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm bg-white animate-fade-in delay-2" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <!-- Table Header Actions -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div>
                            <h4 class="fw-semibold mb-1 text-dark fs-5">Daftar Riwayat Penarikan</h4>
                            <p class="text-muted mb-0 small" style="font-size: 0.8rem;">Semua pengajuan penarikan tersusun berdasarkan yang terbaru.</p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('reseller.withdraw.download') }}" class="btn btn-outline-warning btn-sm text-dark fw-medium px-3 py-1.5 rounded-pill d-flex align-items-center gap-1" style="font-size: 0.85rem; border-color: #ffc107;">
                                <i class="bi bi-cloud-arrow-down-fill text-warning"></i>
                                <span>Unduh PDF</span>
                            </a>
                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-dark px-3 py-2 border border-warning-subtle" style="font-weight: 500; font-size: 0.75rem;">{{ $withdrawals->total() }} hasil</span>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="border-0 py-3 text-secondary" style="background-color: #f8fafc; font-weight: 600;">ID Penarikan</th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #f8fafc; font-weight: 600;">Nama Pemilik</th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #f8fafc; font-weight: 600;">Tanggal Pengajuan</th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #f8fafc; font-weight: 600;">Total</th>
                                    <th class="border-0 py-3 text-center text-secondary" style="background-color: #f8fafc; font-weight: 600; width: 150px;">Status</th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #f8fafc; font-weight: 600;">Tanggal Diproses</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $wd)
                                    @php
                                        $status = strtolower($wd->status);
                                        $isRejected = $status === 'rejected';
                                        
                                        // Set status badge style
                                        if ($status === 'approved') {
                                            $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1.5 small d-inline-flex align-items-center gap-1" style="font-weight: 500;"><i class="bi bi-check-circle-fill"></i> Approved</span>';
                                        } elseif ($isRejected) {
                                            $statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5 py-1.5 small d-inline-flex align-items-center gap-1" style="font-weight: 500;"><i class="bi bi-x-circle-fill"></i> Rejected</span>';
                                        } else {
                                            $statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill px-2.5 py-1.5 small d-inline-flex align-items-center gap-1" style="font-weight: 500;"><i class="bi bi-clock-fill"></i> Pending</span>';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'text-decoration-line-through opacity-75' : '' }}">#WD-{{ str_pad($wd->id, 4, '0', STR_PAD_LEFT) }}</div>
                                            <small class="text-muted">{{ $wd->bank_name }}</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'text-decoration-line-through opacity-75' : '' }}">{{ $wd->account_holder }}</div>
                                            <small class="text-muted">{{ $wd->account_number }}</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-dark {{ $isRejected ? 'text-decoration-line-through opacity-75' : '' }}">{{ $wd->created_at->format('d M Y') }}</div>
                                            <small class="text-muted">{{ $wd->created_at->format('H:i') }} WIB</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold {{ $isRejected ? 'text-danger text-decoration-line-through opacity-75' : 'text-dark' }}">Rp {{ number_format($wd->amount, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="py-3 text-center">
                                            {!! $statusBadge !!}
                                        </td>
                                        <td class="py-3">
                                            @if($wd->status !== 'pending')
                                                <div class="text-dark">{{ $wd->updated_at->format('d M Y') }}</div>
                                                <small class="text-muted">{{ $wd->updated_at->format('H:i') }} WIB</small>
                                            @else
                                                <span class="text-muted fst-italic small">Belum diproses</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-wallet2 fs-1 d-block mb-3 opacity-25"></i>
                                            Belum ada riwayat penarikan dana.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
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