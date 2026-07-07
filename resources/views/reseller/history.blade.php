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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .btn-pdf-download {
            border: 1px solid var(--primary) !important;
            color: var(--primary) !important;
            background-color: transparent !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-pdf-download:hover {
            background-color: var(--primary) !important;
            color: #ffffff !important;
            border-color: var(--primary) !important;
        }
    </style>
</head>

<body class="bg-light" style="font-family: 'Inter', system-ui, -apple-system, sans-serif;">
    @include('partials.navbar-after-login')
    <main class="pt-4 mt-4">
        <div class="container-xxl">
            <!-- Header Section -->
            <div class="d-flex align-items-center mb-4 mt-2">
                <a href="{{ route('reseller.index') }}" class="btn btn-outline-secondary bg-white border-light-subtle rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px; color: #475569;" title="Kembali ke Dashboard">
                    <i class="bi bi-arrow-left fs-5"></i>
                </a>
                <div class="ms-3">
                    <h3 class="mb-1 fw-bold text-dark fs-4">Riwayat Referral</h3>
                    <p class="text-muted mb-0 small">Kelola dan pantau semua transaksi referral Anda</p>
                </div>
            </div>

            <!-- Stats Section (3 Separate Cards) -->
            <div class="row g-3 mb-4 animate-fade-in">
                <!-- Komisi Terbayar -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm bg-white p-3 rounded-4 position-relative" style="border-radius: 16px;">
                        <div class="position-absolute top-0 end-0 p-3 text-secondary opacity-50" style="cursor: pointer;" title="Total komisi yang telah disetujui dan dibayarkan ke saldo Anda">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 56px; height: 56px;">
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.8rem;">Total Komisi Terbayar</h6>
                                <h4 class="fw-semibold mb-0 text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</h4>
                                <span class="text-muted small" style="font-size: 0.75rem;">Total komisi berhasil dicairkan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Komisi Pending -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm bg-white p-3 rounded-4 position-relative" style="border-radius: 16px;">
                        <div class="position-absolute top-0 end-0 p-3 text-secondary opacity-50" style="cursor: pointer;" title="Komisi dalam proses verifikasi transaksi pembeli">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 56px; height: 56px;">
                                <i class="bi bi-clock fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.8rem;">Komisi Pending</h6>
                                <h4 class="fw-semibold mb-0 text-warning">Rp {{ number_format($totalPending, 0, ',', '.') }}</h4>
                                <span class="text-muted small" style="font-size: 0.75rem;">Komisi pending</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Komisi Ditolak -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm bg-white p-3 rounded-4 position-relative" style="border-radius: 16px;">
                        <div class="position-absolute top-0 end-0 p-3 text-secondary opacity-50" style="cursor: pointer;" title="Komisi yang dibatalkan atau ditolak">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width: 56px; height: 56px;">
                                <i class="bi bi-x-circle fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.8rem;">Komisi Ditolak</h6>
                                <h4 class="fw-semibold mb-0 text-danger">Rp {{ number_format($totalRejected, 0, ',', '.') }}</h4>
                                <span class="text-muted small" style="font-size: 0.75rem;">Komisi ditolak</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 shadow-sm mb-4 bg-white animate-fade-in delay-1" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('reseller.history') }}" class="row g-3 align-items-end">
                        <!-- Search Input -->
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small mb-2 fw-medium" for="searchReferral">Cari Referral</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="search" name="search" id="searchReferral" value="{{ request('search') }}" class="form-control bg-light border-0 py-2 fs-6" style="border-radius: 0 8px 8px 0;" placeholder="Nama, email, atau detail transaksi...">
                            </div>
                        </div>
                        <!-- Status Dropdown -->
                        <div class="col-12 col-md-2">
                            <label class="form-label text-muted small mb-2 fw-medium" for="statusFilter">Status</label>
                            <select name="status" id="statusFilter" class="form-select bg-light border-0 py-2 fs-6" style="border-radius: 8px;">
                                <option value="">Semua Status</option>
                                <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                                <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                                <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
                            </select>
                        </div>
                        <!-- Periode Tanggal -->
                        <div class="col-12 col-md-3">
                            <label class="form-label text-muted small mb-2 fw-medium" for="dateRangePicker">Periode Tanggal</label>
                            <div class="input-group">
                                <input type="text" name="date_range" id="dateRangePicker" value="{{ request('date_range') }}" class="form-control bg-light border-0 py-2 fs-6" style="border-radius: 8px 0 0 8px;" placeholder="Pilih Periode Tanggal">
                                <span class="input-group-text bg-light border-0" style="cursor: pointer;"><i class="bi bi-calendar3 text-muted"></i></span>
                            </div>
                        </div>
                        <!-- Filter Actions -->
                        <div class="col-12 col-md-3 d-flex gap-2">
                            <button type="submit" class="btn text-white py-2 px-3 fw-medium flex-grow-1" style="background-color: var(--primary); border-radius: 8px; border: none;">
                                <i class="bi bi-funnel me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('reseller.history') }}" class="btn btn-outline-secondary py-2 px-3 flex-grow-1" style="border-radius: 8px;">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm bg-white animate-fade-in delay-2 mb-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <!-- Table Header Actions -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                        <div class="d-flex align-items-center">
                            <h5 class="fw-bold mb-0 text-dark">
                                <i class="bi bi-clock-history text-warning me-3"></i>
                                Daftar Riwayat Referral
                            </h5>
                            <span class="badge rounded-pill bg-light text-secondary border border-light-subtle px-3 py-2 ms-2" style="font-weight: 500; font-size: 0.75rem;">{{ $history->total() }} hasil</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('reseller.history.download', request()->query()) }}" class="btn btn-pdf-download btn-sm fw-medium px-3 py-1.5 rounded-pill d-flex align-items-center gap-1" style="font-size: 0.85rem;" target="_blank">
                                <i class="bi bi-cloud-arrow-down-fill"></i>
                                <span>Unduh PDF</span>
                            </a>
                        </div>
                    </div>

                    <!-- Responsive Table -->
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600; border-radius: 8px 0 0 8px; width: 15%;">Tanggal <i class="bi bi-chevron-expand small"></i></th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #F9F9FC; font-weight: 600; width: 25%;">Pengguna</th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #F9F9FC; font-weight: 600; width: 25%;">Detail Transaksi</th>
                                    <th class="border-0 py-3 text-center text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600; width: 15%;">Status</th>
                                    <th class="border-0 py-3 text-end text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600; width: 10%;">Komisi</th>
                                    <th class="border-0 py-3 text-center text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600; border-radius: 0 8px 8px 0; width: 10%;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                    @php
                                        $status = strtolower($item->status);
                                        $isRejected = $status === 'rejected';
                                        
                                        // Set status badge style
                                        if ($status === 'paid') {
                                            $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-check-circle-fill"></i> Paid</span>';
                                        } elseif ($isRejected) {
                                            $statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-x-circle-fill"></i> Rejected</span>';
                                        } else {
                                            $statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-clock-fill"></i> Pending</span>';
                                        }
                                    @endphp
                                    <tr>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark">{{ $item->created_at->format('d M Y') }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $item->created_at->format('H:i') }} WIB</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'text-decoration-line-through opacity-50' : '' }}">{{ $item->referredUser->name ?? 'Pengguna Baru' }}</div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">{{ $item->referredUser->email ?? '-' }}</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-dark {{ $isRejected ? 'text-decoration-line-through opacity-50' : '' }}">
                                                {{ $item->description ?? 'Pembelian event atau course' }}
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            {!! $statusBadge !!}
                                        </td>
                                        <td class="py-3 text-end">
                                            <div class="fw-bold {{ $isRejected ? 'text-danger text-decoration-line-through opacity-50' : 'text-success' }}" style="font-size: 1.05rem;">
                                                {{ $isRejected ? '-' : '+' }}Rp {{ number_format($item->amount, 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" 
                                                        class="btn btn-sm btn-light border border-light-subtle rounded-3 text-dark fw-medium px-2.5 py-1.5 btn-view-detail" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#refDetailModal"
                                                        data-id="#REF-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}"
                                                        data-date="{{ $item->created_at->format('d M Y, H:i') }} WIB"
                                                        data-user="{{ $item->referredUser->name ?? 'Pengguna Baru' }}"
                                                        data-email="{{ $item->referredUser->email ?? '-' }}"
                                                        data-desc="{{ $item->description ?? 'Pembelian event atau course' }}"
                                                        data-amount="Rp {{ number_format($item->amount, 0, ',', '.') }}"
                                                        data-status-badge="{{ str_replace('"', "'", $statusBadge) }}"
                                                        style="font-size: 0.8rem;">
                                                    Detail
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                            Belum ada riwayat referral.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $history->links() }}
                    </div>
                </div>
            </div>

            <!-- Info Banner -->
            <div class="alert alert-primary border-0 p-4 shadow-sm animate-fade-in delay-3" style="background-color:var(--primary-dark); border-radius: 16px;">
                <div class="d-flex align-items-start gap-3 text-white">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="color:var(--primary-dark); width: 36px; height: 36px; min-width: 36px;">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Informasi Penting</h6>
                        <p class="mb-0 small">Komisi referral akan diverifikasi secara otomatis setelah pembayaran pembeli berhasil dikonfirmasi. Saldo dompet Anda akan bertambah secara real-time untuk transaksi referral dengan status PAID. Pencairan komisi dapat diajukan melalui halaman dashboard reseller jika saldo dompet telah mencapai batas minimum penarikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Referral Detail Modal -->
    <div class="modal fade" id="refDetailModal" tabindex="-1" aria-labelledby="refDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%); box-shadow: 0 4px 12px rgba(109, 40, 217, 0.2);">
                            <i class="bi bi-person-badge fs-4"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="refDetailModalLabel">Detail Riwayat Referral</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Status Header -->
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom mt-2">
                        <div>
                            <span class="text-muted d-block small mb-1" style="font-size: 0.8rem;">Rincian Transaksi</span>
                            <h4 class="fw-bold text-dark mb-0" id="modalRefId" style="letter-spacing: -0.5px;">#REF-0000</h4>
                        </div>
                        <div id="modalRefStatusBadge">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- Details grid -->
                    <div class="row g-3">
                        <div class="col-12 mb-2">
                            <span class="text-muted d-block small mb-1" style="font-size: 0.75rem;">Tanggal Transaksi</span>
                            <span class="fw-semibold text-dark fs-6" id="modalRefDate">-</span>
                        </div>
                        
                        <div class="col-12 mb-2">
                            <div class="p-3 rounded-4" style="background-color: #F8F9FD; border: 1px solid #E2E8F0;">
                                <span class="text-muted d-block small mb-2" style="font-size: 0.75rem;">Pengguna Terdaftar</span>
                                <span class="fw-semibold text-dark d-block fs-6" id="modalRefUser">-</span>
                                <span class="text-muted small d-block" id="modalRefEmail">-</span>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <span class="text-muted d-block small mb-1" style="font-size: 0.75rem;">Detail Program / Pembelian</span>
                            <span class="fw-semibold text-dark fs-6" id="modalRefDesc">-</span>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center py-3 px-3 rounded-4" style="background-color: var(--primary-subtle); border: 1px solid var(--primary-light);">
                                <span class="fw-semibold small" style="color: var(--primary-dark);">Komisi Anda</span>
                                <span class="fw-semibold fs-4 text-dark" id="modalRefAmount">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-primary w-100 py-3 fw-semibold text-white d-flex align-items-center justify-content-center gap-2 shadow-sm" style="background-color: var(--primary); border: none; border-radius: 12px; font-size: 1rem;" data-bs-dismiss="modal">
                        <i class="bi bi-check-lg fs-5"></i> Selesai
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer-after-login')

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Flatpickr initialization
            flatpickr("#dateRangePicker", {
                mode: "range",
                dateFormat: "d/m/Y",
                locale: {
                    rangeSeparator: " - "
                }
            });

            const detailButtons = document.querySelectorAll('.btn-view-detail');
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    populateRefModal(this);
                });
            });

            function populateRefModal(btn) {
                document.getElementById('modalRefId').innerText = btn.getAttribute('data-id');
                document.getElementById('modalRefDate').innerText = btn.getAttribute('data-date');
                document.getElementById('modalRefUser').innerText = btn.getAttribute('data-user');
                document.getElementById('modalRefEmail').innerText = btn.getAttribute('data-email');
                document.getElementById('modalRefDesc').innerText = btn.getAttribute('data-desc');
                document.getElementById('modalRefAmount').innerText = btn.getAttribute('data-amount');
                
                const badgeContainer = document.getElementById('modalRefStatusBadge');
                badgeContainer.innerHTML = btn.getAttribute('data-status-badge');
            }
        });
    </script>
</body>
</html>