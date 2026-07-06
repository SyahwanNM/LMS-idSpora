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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media (min-width: 768px) {
            .border-end-md {
                border-right: 1px solid rgba(0, 0, 0, 0.1) !important;
            }
        }
        .btn-pdf-download {
            border: 1px solid var(--primary) !important;
            color: var(--primary) !important;
            background-color: transparent !important;
            transition: all 0.2s ease-in-out;
        }
        .btn-pdf-download:hover {
            background-color: var(--primary-dark) !important;
            color: #ffffff !important;
            border-color: var(--primary-dark) !important;
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
                    <h3 class="mb-1 fw-bold text-dark fs-4">Riwayat Penarikan Dana</h3>
                    <p class="text-muted mb-0 small">Kelola dan pantau semua pengajuan penarikan dana Anda</p>
                </div>
            </div>

            <!-- Stats Section (3 Separate Cards) -->
            <div class="row g-3 mb-4 animate-fade-in">
                <!-- Dana Disetujui -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm bg-white p-3 rounded-4 position-relative" style="border-radius: 16px;">
                        <div class="position-absolute top-0 end-0 p-3 text-secondary opacity-50" style="cursor: pointer;" title="Total dana yang berhasil dicairkan">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success" style="width: 56px; height: 56px;">
                                <i class="bi bi-wallet2 fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.8rem;">Dana Disetujui</h6>
                                <h4 class="fw-semibold mb-0 text-success">Rp {{ number_format($totalApproved, 0, ',', '.') }}</h4>
                                <span class="text-muted small" style="font-size: 0.75rem;">Total dana berhasil dicairkan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dana Pending -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm bg-white p-3 rounded-4 position-relative" style="border-radius: 16px;">
                        <div class="position-absolute top-0 end-0 p-3 text-secondary opacity-50" style="cursor: pointer;" title="Dana sedang menunggu persetujuan admin">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning" style="width: 56px; height: 56px;">
                                <i class="bi bi-clock fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.8rem;">Dana Pending</h6>
                                <h4 class="fw-semibold mb-0 text-warning">Rp {{ number_format($totalPending, 0, ',', '.') }}</h4>
                                <span class="text-muted small" style="font-size: 0.75rem;">Menunggu persetujuan</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dana Ditolak -->
                <div class="col-12 col-md-4">
                    <div class="card border-0 shadow-sm bg-white p-3 rounded-4 position-relative" style="border-radius: 16px;">
                        <div class="position-absolute top-0 end-0 p-3 text-secondary opacity-50" style="cursor: pointer;" title="Pengajuan penarikan dana yang ditolak">
                            <i class="bi bi-info-circle"></i>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger" style="width: 56px; height: 56px;">
                                <i class="bi bi-x-circle fs-3"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small fw-medium mb-1" style="font-size: 0.8rem;">Dana Ditolak</h6>
                                <h4 class="fw-semibold mb-0 text-danger">Rp {{ number_format($totalRejected, 0, ',', '.') }}</h4>
                                <span class="text-muted small" style="font-size: 0.75rem;">Pengajuan ditolak</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card border-0 shadow-sm mb-4 bg-white animate-fade-in delay-1" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('reseller.withdraw.history') }}" class="row g-3 align-items-end">
                        <!-- Search Input -->
                        <div class="col-12 col-md-4">
                            <label class="form-label text-muted small mb-2 fw-medium" for="searchFilter">Cari Penarikan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                                <input type="search" name="search" id="searchFilter" value="{{ request('search') }}" class="form-control bg-light border-0 py-2 fs-6" style="border-radius: 0 8px 8px 0;" placeholder="Cari nama bank, no. rekening, atau pemilik...">
                            </div>
                        </div>
                        <!-- Status Dropdown -->
                        <div class="col-12 col-md-2">
                            <label class="form-label text-muted small mb-2 fw-medium" for="statusFilter">Status</label>
                            <select name="status" id="statusFilter" class="form-select bg-light border-0 py-2 fs-6" style="border-radius: 8px;">
                                <option value="">Semua Status</option>
                                <option value="approved" @selected(request('status') === 'approved')>Approved</option>
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
                            <button type="submit" class="btn text-white py-2 px-3 fw-medium flex-grow-1" style="background-color: var(--primary-dark); border-radius: 8px; border: none;">
                                <i class="bi bi-funnel me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('reseller.withdraw.history') }}" class="btn btn-outline-secondary py-2 px-3 flex-grow-1" style="border-radius: 8px;">
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
                                <i class="bi bi-arrow-up-right-circle-fill text-warning me-3"></i>
                                Daftar Riwayat Penarikan
                            </h5>
                            <span class="badge rounded-pill bg-light text-secondary border border-light-subtle px-3 py-1 ms-2" style="font-weight: 500; font-size: 0.75rem;">{{ $withdrawals->total() }} hasil</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('reseller.withdraw.download') }}" class="btn btn-pdf-download btn-sm fw-medium px-3 py-1.5 rounded-pill d-flex align-items-center gap-1" style="font-size: 0.85rem;" target="_blank">
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
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600; border-radius: 8px 0 0 8px;">ID Penarikan</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600;">Tanggal Pengajuan <i class="bi bi-chevron-expand small"></i></th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #F9F9FC; font-weight: 600;">Pengguna</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600;">Bank Tujuan</th>
                                    <th class="border-0 py-3 text-secondary" style="background-color: #F9F9FC; font-weight: 600;">Nomor Rekening <i class="bi bi-eye text-primary px-5" id="toggleAllAccounts" title="Tampilkan/Sembunyikan Semua Rekening" style="cursor: pointer;"></i></th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600;">Nominal Penarikan</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600;">Biaya Admin</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600;">Bersih Diterima</th>
                                    <th class="border-0 py-3 text-center text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600;">Status</th>
                                    <th class="border-0 py-3 text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600;">Tanggal Diproses</th>
                                    <th class="border-0 py-3 text-center text-secondary text-nowrap" style="background-color: #F9F9FC; font-weight: 600; border-radius: 0 8px 8px 0;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($withdrawals as $wd)
                                    @php
                                        $status = strtolower($wd->status);
                                        $isRejected = $status === 'rejected';
                                        
                                        // Set status badge style
                                        if ($status === 'approved') {
                                            $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-check-circle-fill"></i> Approved</span>';
                                        } elseif ($isRejected) {
                                            $statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-x-circle-fill"></i> Rejected</span>';
                                        } else {
                                            $statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning-emphasis rounded-pill" style="font-weight: 500; font-size: 13px !important; padding: 5px 10px !important; display: inline-flex !important; align-items: center; justify-content: center; width: fit-content; gap: 0.25rem;"><i class="bi bi-clock-fill"></i> Pending</span>';
                                        }
                                        
                                        // Mask and format account number with spacing
                                        $accountLen = strlen($wd->account_number);
                                        if ($accountLen > 4) {
                                            $maskedRaw = str_repeat('•', $accountLen - 4) . substr($wd->account_number, -4);
                                        } else {
                                            $maskedRaw = $wd->account_number;
                                        }
                                        preg_match_all('/.{1,4}/u', $wd->account_number, $rawMatches);
                                        $rawFormatted = implode(' ', $rawMatches[0]);
                                        preg_match_all('/.{1,4}/u', $maskedRaw, $matches);
                                        $maskedFormatted = implode(' ', $matches[0]);
                                    @endphp
                                    <tr>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'opacity-50' : '' }}">#WD-{{ str_pad($wd->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-dark fw-medium {{ $isRejected ? 'opacity-50' : '' }}">{{ $wd->created_at->format('d M Y') }}</div>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $wd->created_at->format('H:i') }} WIB</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'opacity-50' : '' }}">{{ $wd->user->name ?? Auth::user()->name }}</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2 {{ $isRejected ? 'opacity-50' : '' }}">
                                                <i class="bi bi-bank fs-5" style="color:var(--primary-dark);"></i>
                                                <span class="fw-medium text-dark">{{ $wd->bank_name }}</span>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <!-- Masked account number, toggleable via JavaScript -->
                                            <div class="fw-semibold text-dark account-number {{ $isRejected ? 'opacity-50' : '' }}" 
                                                 data-raw="{{ $rawFormatted }}" 
                                                 data-masked="{{ $maskedFormatted }}">
                                                {{ $maskedFormatted }}
                                            </div>
                                            <small class="text-muted d-block" style="font-size: 0.75rem;">A/n. {{ $wd->account_holder }}</small>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-semibold text-dark {{ $isRejected ? 'text-decoration-line-through opacity-50' : '' }}">
                                                Rp {{ number_format($wd->amount, 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="text-muted {{ $isRejected ? 'opacity-50' : '' }}">
                                                Rp {{ number_format($wd->admin_fee ?? 3000, 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-bold text-success {{ $isRejected ? 'text-danger text-decoration-line-through opacity-50' : '' }}" style="font-size: 1.05rem;">
                                                Rp {{ number_format($wd->net_amount ?? ($wd->amount - ($wd->admin_fee ?? 3000)), 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            {!! $statusBadge !!}
                                        </td>
                                        <td class="py-3">
                                            @if($status !== 'pending')
                                                <div class="text-dark fw-medium">{{ $wd->updated_at->format('d M Y') }}</div>
                                                <small class="text-muted" style="font-size: 0.75rem;">{{ $wd->updated_at->format('H:i') }} WIB</small>
                                            @else
                                                <span class="text-muted fst-italic small">Belum diproses</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="d-flex align-items-center justify-content-center gap-1">
                                                <button type="button" 
                                                        class="btn btn-sm btn-light border border-light-subtle rounded-3 text-dark fw-medium px-2.5 py-1.5 btn-view-detail" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#wdDetailModal"
                                                        data-id="#WD-{{ str_pad($wd->id, 4, '0', STR_PAD_LEFT) }}"
                                                        data-date="{{ $wd->created_at->format('d M Y, H:i') }} WIB"
                                                        data-user="{{ $wd->user->name ?? Auth::user()->name }}"
                                                        data-bank="{{ $wd->bank_name }}"
                                                        data-account="{{ $rawFormatted }}"
                                                        data-holder="{{ $wd->account_holder }}"
                                                        data-amount="Rp {{ number_format($wd->amount, 0, ',', '.') }}"
                                                        data-status="{{ ucfirst($status) }}"
                                                        data-status-badge="{{ str_replace('"', "'", $statusBadge) }}"
                                                        data-processed-date="{{ $status !== 'pending' ? $wd->updated_at->format('d M Y, H:i') . ' WIB' : 'Belum diproses' }}"
                                                        data-proof="{{ $wd->proof_of_transfer ? asset('uploads/' . ltrim(str_replace('storage/', '', $wd->proof_of_transfer), '/')) : '' }}"
                                                        data-reason="{{ $wd->rejected_reason ?? '' }}"
                                                        style="font-size: 0.8rem;">
                                                    Detail
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5 text-muted">
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

            <!-- Info Banner -->
            <div class="alert border-0 p-4 shadow-sm animate-fade-in delay-3" style="background-color:var(--primary-dark); border-radius: 16px;">
                <div class="d-flex align-items-start gap-3 text-white">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white shadow-sm" style="color:var(--primary-dark); width: 36px; height: 36px; min-width: 36px;">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Informasi Penting</h6>
                        <p class="mb-0 small">Proses penarikan dana dilakukan pada hari kerja (Senin - Jumat) pukul 09.00 - 17.00 WIB. Pastikan data rekening Anda sudah benar untuk menghindari kegagalan transfer. Proses pencairan dana membutuhkan waktu 1-3 hari kerja tergantung bank tujuan.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Withdrawal Detail Modal -->
    <div class="modal fade" id="wdDetailModal" tabindex="-1" aria-labelledby="wdDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header border-bottom-0 pb-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center text-white" style="width: 48px; height: 48px; background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%); box-shadow: 0 4px 12px rgba(109, 40, 217, 0.2);">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="wdDetailModalLabel">Detail Penarikan Dana</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Status Header -->
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom mt-2">
                    <div>
                                <span class="text-muted d-block small" style="font-size: 0.75rem;">ID Penarikan</span>
                                <span class="fw-semibold text-dark d-block" style="font-size: 0.85rem;" id="modalWdId">-</span>
                            </div>    
                    
                        <div id="modalWdStatusBadge">
                            <!-- Populated by JS -->
                        </div>
                    </div>

                    <!-- Metadata Info Row -->
                    <div class="row g-3 py-3 border-bottom mb-4">
                        <!-- Tanggal Pengajuan -->
                        <div class="col-12 col-md-4 border-end-md d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--primary-subtle) !important; color: var(--primary) !important;">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small" style="font-size: 0.75rem;">Tanggal Pengajuan</span>
                                <span class="fw-semibold text-dark d-block" style="font-size: 0.85rem;" id="modalWdDate">-</span>
                            </div>
                        </div>
                        <!-- Tanggal Diproses -->
                        <div class="col-12 col-md-4 border-end-md d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--primary-subtle) !important; color: var(--primary) !important;">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small" style="font-size: 0.75rem;">Tanggal Diproses</span>
                                <span class="fw-semibold text-dark d-block" style="font-size: 0.85rem;" id="modalWdProcessed">-</span>
                            </div>
                        </div>
                        <!-- Pengguna -->
                        <div class="col-12 col-md-4 d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 36px; height: 36px; min-width: 36px; background-color: var(--primary-subtle) !important; color: var(--primary) !important;">
                                <i class="bi bi-person-fill-gear"></i>
                            </div>
                            <div>
                                <span class="text-muted d-block small" style="font-size: 0.75rem;">Pengguna</span>
                                <span class="fw-semibold text-dark d-block" style="font-size: 0.85rem;" id="modalWdUser">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recipient Info Box -->
                    <div class="p-4 rounded-4 mb-4" style="background-color: #F8F9FD; border: 1px solid #E2E8F0;">
                        <h6 class="fw-bold mb-3" style="color: var(--primary);">Informasi Penerima</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <span class="text-muted d-block small">Bank Tujuan</span>
                                <span class="fw-semibold text-dark fs-6" id="modalWdBank">-</span>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block small">Nomor Rekening</span>
                                <span class="fw-semibold text-dark fs-6" id="modalWdAccount">-</span>
                            </div>
                            <div class="col-12 mt-3 pt-3 border-top border-light-subtle">
                                <span class="text-muted d-block small">Atas Nama Penerima</span>
                                <span class="fw-semibold text-dark fs-6" id="modalWdHolder">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Penarikan Box -->
                    <div class="p-3 rounded-4 mb-4 d-flex align-items-center gap-3" style="background-color: #F0FDF4; border: 1px solid #DCFCE7;">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 40px; height: 40px; background-color: var(--success); font-size: 0.9rem;">
                            Rp
                        </div>
                        <div>
                            <span class="fw-semibold text-success d-block small">Total Penarikan</span>
                            <span class="fw-semibold fs-4 text-success" id="modalWdAmount">Rp 0</span>
                        </div>
                    </div>

                    <!-- Rejection Reason Box -->
                    <div class="p-3 rounded-4 mb-4" id="modalWdReasonPanel" style="background-color: #FEF2F2; border: 1px solid #FEE2E2; display: none;">
                        <div class="d-flex gap-3">
                            <div class="align-content-center">
                                <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>
                            </div>
                            <div>
                                <span class="fw-semibold text-danger d-block">Alasan Penolakan</span>
                                <span class="text-dark small" id="modalWdReasonText">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Proof of Transfer Panel -->
                    <div class="mt-4" id="modalWdProofPanel" style="display: none;">
                        <span class="text-muted d-block small mb-2">Bukti Transfer</span>
                        <div class="border rounded-3 p-2 text-center bg-light">
                            <a href="#" id="modalWdProofLink" target="_blank" title="Buka Gambar">
                                <img src="" id="modalWdProofImg" class="img-fluid rounded-2 shadow-sm" style="max-height: 200px; object-fit: contain;" alt="Bukti Transfer">
                            </a>
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

            // Detail Modal Populate
            const detailButtons = document.querySelectorAll('.btn-view-detail');
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    populateWdModal(this);
                });
            });

            function populateWdModal(btn) {
                document.getElementById('modalWdId').innerText = btn.getAttribute('data-id');
                document.getElementById('modalWdDate').innerText = btn.getAttribute('data-date');
                document.getElementById('modalWdUser').innerText = btn.getAttribute('data-user');
                document.getElementById('modalWdBank').innerText = btn.getAttribute('data-bank');
                document.getElementById('modalWdAccount').innerText = btn.getAttribute('data-account');
                document.getElementById('modalWdHolder').innerText = btn.getAttribute('data-holder');
                document.getElementById('modalWdAmount').innerText = btn.getAttribute('data-amount');
                document.getElementById('modalWdProcessed').innerText = btn.getAttribute('data-processed-date');
                
                // Status badge
                const badgeContainer = document.getElementById('modalWdStatusBadge');
                badgeContainer.innerHTML = btn.getAttribute('data-status-badge');
                
                // Rejection reason panel
                const reason = btn.getAttribute('data-reason');
                const reasonPanel = document.getElementById('modalWdReasonPanel');
                if (reason && reason.trim() !== '') {
                    document.getElementById('modalWdReasonText').innerText = reason;
                    reasonPanel.style.display = 'block';
                } else {
                    reasonPanel.style.display = 'none';
                }

                // Proof of transfer panel
                const proof = btn.getAttribute('data-proof');
                const proofPanel = document.getElementById('modalWdProofPanel');
                if (proof && proof.trim() !== '') {
                    document.getElementById('modalWdProofImg').src = proof;
                    document.getElementById('modalWdProofLink').href = proof;
                    proofPanel.style.display = 'block';
                } else {
                    proofPanel.style.display = 'none';
                }
            }

            // Toggle Masking for Account Numbers
            const toggleHeaderBtn = document.getElementById('toggleAllAccounts');
            if (toggleHeaderBtn) {
                let allUnmasked = false;
                toggleHeaderBtn.addEventListener('click', function() {
                    allUnmasked = !allUnmasked;
                    const accountDivs = document.querySelectorAll('.account-number');
                    accountDivs.forEach(div => {
                        if (allUnmasked) {
                            div.innerText = div.getAttribute('data-raw');
                        } else {
                            div.innerText = div.getAttribute('data-masked');
                        }
                    });
                    // Toggle icon
                    if (allUnmasked) {
                        toggleHeaderBtn.classList.remove('bi-eye');
                        toggleHeaderBtn.classList.add('bi-eye-slash');
                    } else {
                        toggleHeaderBtn.classList.remove('bi-eye-slash');
                        toggleHeaderBtn.classList.add('bi-eye');
                    }
                });
            }
        });
    </script>
</body>
</html>