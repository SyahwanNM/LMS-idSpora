<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Reseller - Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-dark: #4c1d95;
            --primary-light: #8b5cf6;
            --primary-subtle: #f3e8ff;
            --bg-surface: #ffffff;
            --text-main: #1e1b4b;
            --text-muted: #64748b;
        }

        .hover-card {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
            border-radius: 16px !important;
        }

        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(76, 29, 149, 0.08) !important;
        }

        .text-primary-dark {
            color: var(--primary-dark) !important;
        }

        .bg-primary-subtle-custom {
            background-color: var(--primary-subtle) !important;
            color: var(--primary-dark) !important;
        }

        .btn-primary-custom {
            background-color: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            color: #ffffff !important;
            transition: all 0.2s ease;
        }

        .btn-primary-custom:hover {
            background-color: #3b1673 !important;
            border-color: #3b1673 !important;
        }

        .fs-7 {
            font-size: 0.75rem !important;
        }

        /* Timeline style */
        .activity-timeline {
            position: relative;
            padding-left: 24px;
            border-left: 2px solid #e2e8f0;
        }

        .activity-item {
            position: relative;
            margin-bottom: 20px;
        }

        .activity-item::before {
            content: '';
            position: absolute;
            left: -31px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: var(--primary-dark);
            border: 2px solid #fff;
        }

        .activity-item.success::before {
            background-color: #10b981;
        }

        .activity-item.warning::before {
            background-color: #f59e0b;
        }

        /* Navigation pills customization */
        .nav-pills .nav-link {
            color: var(--text-muted);
            background-color: transparent;
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link.active {
            color: #ffffff !important;
            background-color: var(--primary-dark) !important;
            box-shadow: 0 4px 12px rgba(76, 29, 149, 0.15);
        }

        /* Table header style to match user dashboard */
        .table thead th {
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-bottom: 2px solid #dee2e6 !important;
        }
    </style>
</head>
<body>
    @include('partials.navbar-reseller')

    <main class="main-content min-vh-100">
        <div class="p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-1 text-dark fs-2">Master Data Reseller</h2>
                    <p class="text-secondary mb-0" style="font-size: 1.05rem;">Kelola data reseller, kode referral, status, dan komisi.</p>
                </div>
            </div>

            <!-- Search and Filters Bar -->
            <div class="row g-3 mb-4 align-items-center bg-white p-3 rounded-4 shadow-sm mx-0">
                <div class="col-12 col-md-5">
                    <div class="position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                        <input type="text" id="resellerSearchInput" class="form-control ps-5 rounded-pill" placeholder="Cari nama, email, atau kode referral..." style="height: 44px;" value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-5 col-md-3">
                    <select id="resellerTierSelect" class="form-select rounded-pill" style="height: 44px;">
                        <option value="">Semua Tier</option>
                        <option value="Bronze">Tier Bronze</option>
                        <option value="Silver">Tier Silver</option>
                        <option value="Gold">Tier Gold</option>
                    </select>
                </div>
                <div class="col-5 col-md-3">
                    <select id="resellerStatusSelect" class="form-select rounded-pill" style="height: 44px;">
                        <option value="">Semua Status</option>
                        <option value="Aktif">Status Aktif</option>
                        <option value="Suspended">Status Suspended</option>
                    </select>
                </div>
                <div class="col-2 col-md-1 d-flex justify-content-center justify-content-md-start">
                    <button type="button" class="btn btn-outline-secondary rounded-circle p-0 d-flex align-items-center justify-content-center" style="height: 44px; width: 44px; flex-shrink: 0;" onclick="resetFilters()" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Reseller Table Card -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="text-secondary small">
                                    <th class="ps-4 py-3">RESELLER</th>
                                    <th class="py-3">KODE REFERRAL</th>
                                    <th class="py-3" id="headerTier" style="cursor: pointer; user-select: none;" title="Urutkan berdasarkan Tier">
                                        TIER <i class="bi bi-arrow-down-up ms-1 text-muted" id="sortTierIcon"></i>
                                    </th>
                                    <th class="py-3" id="headerSales" style="cursor: pointer; user-select: none;" title="Urutkan berdasarkan Penjualan">
                                        PENJUALAN <i class="bi bi-arrow-down-up ms-1 text-muted" id="sortSalesIcon"></i>
                                    </th>
                                    <th class="py-3" id="headerDate" style="cursor: pointer; user-select: none;" title="Urutkan berdasarkan Tanggal Dibuat">
                                        TANGGAL DIBUAT <i class="bi bi-arrow-down-up ms-1 text-muted" id="sortDateIcon"></i>
                                    </th>
                                    <th class="py-3">STATUS</th>
                                    <th class="pe-4 py-3 text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody id="resellerTableBody">
                                @forelse($resellers as $reseller)
                                    @php
                                        $count = $reseller->referrals_count ?? 0;
                                        if ($count >= 151) {
                                            $tier = 'Gold';
                                            $tierBg = 'rgba(234, 179, 8, 0.1)';
                                            $tierColor = '#d97706';
                                            $commission = '15%';
                                        } elseif ($count >= 51) {
                                            $tier = 'Silver';
                                            $tierBg = 'rgba(100, 116, 139, 0.1)';
                                            $tierColor = '#475569';
                                            $commission = '12%';
                                        } else {
                                            $tier = 'Bronze';
                                            $tierBg = 'rgba(180, 83, 9, 0.1)';
                                            $tierColor = '#b45309';
                                            $commission = '10%';
                                        }

                                        $resellerClicks = \DB::table('referral_clicks')->where('user_id', $reseller->id)->count();
                                        $status = $reseller->reseller_status === 'suspended' ? 'Suspended' : 'Aktif';
                                    @endphp
                                    <tr class="reseller-row" 
                                        data-name="{{ strtolower($reseller->name) }}" 
                                        data-email="{{ strtolower($reseller->email) }}" 
                                        data-code="{{ strtolower($reseller->referral_code) }}"
                                        data-tier="{{ $tier }}"
                                        data-sales="{{ $reseller->total_earned ?? 0 }}"
                                        data-date="{{ $reseller->created_at ? $reseller->created_at->timestamp : 0 }}"
                                        data-status="{{ $status }}">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="{{ $reseller->avatar_url }}" alt="{{ $reseller->name }}" class="rounded-circle shadow-sm" style="width: 42px; height: 42px; object-fit: cover; border: 2px solid #fff;">
                                                <div>
                                                    <div class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $reseller->name }}</div>
                                                    <div class="small text-muted text-truncate" style="font-size: 0.8rem; max-width: 160px;" title="{{ $reseller->email }}">{{ $reseller->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge bg-light text-dark border px-2 py-1.5"><i class="bi bi-tag-fill text-warning me-1"></i>{{ $reseller->referral_code }}</span>
                                                <button class="btn btn-sm btn-light border border-light-subtle rounded-3 text-secondary py-1 px-2" onclick="navigator.clipboard.writeText('{{ $reseller->referral_code }}'); showToast('Kode copied!');" title="Salin Kode">
                                                    <i class="bi bi-clipboard" style="font-size: 0.8rem;"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <span class="badge rounded-pill px-3 py-1.5 fw-semibold" style="background-color: {{ $tierBg }}; color: {{ $tierColor }}; font-size: 0.75rem;">
                                                {{ $tier }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <div class="fw-bold text-dark small" style="font-size: 0.9rem;">Rp {{ number_format($reseller->total_earned ?? 0, 0, ',', '.') }}</div>
                                        </td>
                                        <td class="py-3 text-muted small">{{ $reseller->created_at?->format('d M Y') ?? '-' }}</td>
                                        <td class="py-3">
                                            <div class="form-check form-switch" title="Toggle Status Aktif/Suspend">
                                                <input class="form-check-input" type="checkbox" role="switch" @checked($status === 'Aktif') id="statusSwitchRow{{ $reseller->id }}" onchange="toggleUserStatus({{ $reseller->id }}, '{{ addslashes($reseller->name) }}')">
                                            </div>
                                        </td>
                                        <td class="pe-4 py-3 text-end">
                                            <button class="btn btn-sm text-primary-dark shadow-sm" style="border: 1px solid var(--primary-dark); background-color: transparent;" data-bs-toggle="modal" data-bs-target="#detailModal{{ $reseller->id }}">
                                                <i class="bi bi-eye-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <div class="py-4">
                                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-people text-muted fs-1"></i>
                                                </div>
                                                <h5 class="fw-bold mb-1">Tidak Ada Data Reseller</h5>
                                                <p class="text-secondary small mb-0">Belum ada user yang terdaftar sebagai reseller di sistem.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                                <tr id="emptyRow" style="display: none;">
                                    <td colspan="7" class="text-center py-5">
                                        <div class="py-4">
                                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                <i class="bi bi-people text-muted fs-1"></i>
                                            </div>
                                            <h5 class="fw-bold mb-1">Reseller Tidak Ditemukan</h5>
                                            <p class="text-secondary small mb-3">Tidak ada data reseller yang cocok dengan kriteria pencarian Anda.</p>
                                            <button type="button" class="btn btn-sm btn-primary-custom rounded-pill px-4" onclick="resetFilters()">Reset Filter</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Reseller Detail Modals -->
    @foreach($resellers as $reseller)
        @php
            $count = $reseller->referrals_count ?? 0;
            if ($count >= 151) {
                $tier = 'Gold';
                $tierBg = 'rgba(234, 179, 8, 0.1)';
                $tierColor = '#d97706';
            } elseif ($count >= 51) {
                $tier = 'Silver';
                $tierBg = 'rgba(100, 116, 139, 0.1)';
                $tierColor = '#475569';
            } else {
                $tier = 'Bronze';
                $tierBg = 'rgba(180, 83, 9, 0.1)';
                $tierColor = '#b45309';
            }

            $resellerClicks = \DB::table('referral_clicks')->where('user_id', $reseller->id)->count();
            $conversionRate = ($resellerClicks > 0) ? number_format(($count / $resellerClicks) * 100, 1) . '%' : '0%';
            $status = $reseller->reseller_status === 'suspended' ? 'Suspended' : 'Aktif';
            
            $statusBadgeClass = $status === 'Suspended' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success';

            $whatsappPhone = $reseller->phone;
            if ($whatsappPhone) {
                $whatsappNumber = preg_replace('/[^0-9]/', '', $whatsappPhone);
                if (str_starts_with($whatsappNumber, '0')) {
                    $whatsappNumber = '62' . substr($whatsappNumber, 1);
                }
            } else {
                $whatsappNumber = null;
            }

            $referralActivities = $reseller->referrals()->with(['referredUser'])->latest()->take(8)->get();
        @endphp
        
        <div class="modal fade" id="detailModal{{ $reseller->id }}" tabindex="-1" aria-hidden="true" style="z-index: 1080;">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0 bg-white p-4">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-person-badge-fill text-dark"></i> Detail Performa Reseller
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body p-4 bg-white">
                        <div class="row g-4">
                            <!-- Profile Card Left -->
                            <div class="col-lg-4">
                                <div class="card border-0 bg-light p-4 rounded-4 text-center h-100 shadow-none">
                                    <img src="{{ $reseller->avatar_url }}" alt="{{ $reseller->name }}" class="rounded-circle shadow-sm mx-auto mb-3" style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #fff;">
                                    <h4 class="fw-bold text-dark mb-1">{{ $reseller->name }}</h4>
                                    <p class="text-muted small mb-3">{{ $reseller->email }}</p>
                                    
                                    <div class="mb-4">
                                        <span class="badge rounded-pill px-3 py-1.5 fw-bold fs-7 {{ $statusBadgeClass }} mb-2" id="statusBadgeModal{{ $reseller->id }}">{{ $status }}</span>
                                        <span class="badge rounded-pill px-3 py-1.5 fw-semibold d-block mx-auto" style="background-color: {{ $tierBg }}; color: {{ $tierColor }}; font-size: 0.75rem; width: fit-content;">
                                            Tier {{ $tier }}
                                        </span>
                                    </div>
                                    
                                    <hr class="my-3 opacity-50">
                                    
                                    <div class="text-start mb-4">
                                        <div class="mb-2">
                                            <small class="text-muted d-block">WhatsApp / Telepon</small>
                                            <span class="fw-bold text-dark small"><i class="bi bi-telephone-fill me-1 text-secondary"></i> {{ $reseller->phone ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Tanggal Join</small>
                                            <span class="fw-bold text-dark small"><i class="bi bi-calendar-event-fill me-1 text-secondary"></i> {{ $reseller->created_at?->format('d M Y H:i') ?? '-' }}</span>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted d-block">Kode Referral</small>
                                            <span class="badge bg-white text-dark border px-2 py-1"><i class="bi bi-tag-fill text-warning me-1"></i> {{ $reseller->referral_code }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($whatsappNumber)
                                        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" class="btn btn-success rounded-pill w-100 fw-bold mt-auto py-2 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                            <i class="bi bi-whatsapp"></i> Hubungi WhatsApp
                                        </a>
                                    @else
                                        <button class="btn btn-secondary rounded-pill w-100 fw-bold mt-auto py-2 shadow-sm d-flex align-items-center justify-content-center gap-2" disabled>
                                            <i class="bi bi-whatsapp"></i> No WhatsApp Kosong
                                        </button>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Stats Grid and Tables Right -->
                            <div class="col-lg-8">
                                <!-- Stats row -->
                                <div class="row g-3 mb-4">
                                    <div class="col-sm-6 col-md-3">
                                        <div class="card border-0 bg-primary-subtle-custom p-3 rounded-4 text-center h-100 shadow-none">
                                            <span class="text-muted small d-block mb-1">Total Komisi</span>
                                            <h5 class="fw-bold text-primary-dark mb-0">Rp {{ number_format($reseller->total_earned ?? 0, 0, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="card border-0 bg-light p-3 rounded-4 text-center h-100 shadow-none border">
                                            <span class="text-muted small d-block mb-1">Sisa Saldo</span>
                                            <h5 class="fw-bold text-dark mb-0">Rp {{ number_format($reseller->wallet_balance ?? 0, 0, ',', '.') }}</h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="card border-0 bg-light p-3 rounded-4 text-center h-100 shadow-none border">
                                            <span class="text-muted small d-block mb-1">Penjualan</span>
                                            <h5 class="fw-bold text-dark mb-0">{{ $count }} Klien</h5>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="card border-0 bg-light p-3 rounded-4 text-center h-100 shadow-none border">
                                            <span class="text-muted small d-block mb-1">Konversi Klik</span>
                                            <h5 class="fw-bold text-dark mb-0">{{ $conversionRate }}</h5>
                                            <small class="text-muted" style="font-size: 10px;">Dari {{ $resellerClicks }} klik</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Detail Tabs -->
                                <ul class="nav nav-pills mb-3 border-bottom pb-3 gap-2" id="pills-tab-{{ $reseller->id }}" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active rounded-pill fw-medium px-4" id="pills-transaksi-tab-{{ $reseller->id }}" data-bs-toggle="pill" data-bs-target="#pills-transaksi-{{ $reseller->id }}" type="button" role="tab">Riwayat Referral</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-pill fw-medium px-4" id="pills-withdraw-tab-{{ $reseller->id }}" data-bs-toggle="pill" data-bs-target="#pills-withdraw-{{ $reseller->id }}" type="button" role="tab">Riwayat Penarikan</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link rounded-pill fw-medium px-4" id="pills-timeline-tab-{{ $reseller->id }}" data-bs-toggle="pill" data-bs-target="#pills-timeline-{{ $reseller->id }}" type="button" role="tab">Timeline Aktivitas</button>
                                    </li>
                                </ul>
                                
                                <div class="tab-content" id="pills-tabContent-{{ $reseller->id }}">
                                    <!-- Riwayat Referral Tab -->
                                    <div class="tab-pane fade show active" id="pills-transaksi-{{ $reseller->id }}" role="tabpanel">
                                        <div class="table-responsive border rounded-4" style="max-height: 300px; overflow-y: auto;">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light text-muted small position-sticky top-0 shadow-sm" style="z-index: 10;">
                                                    <tr>
                                                        <th class="ps-3 py-3 border-bottom-0 bg-light">Pembeli</th>
                                                        <th class="py-3 border-bottom-0 bg-light">Produk/Event</th>
                                                        <th class="py-3 border-bottom-0 bg-light">Tanggal</th>
                                                        <th class="py-3 border-bottom-0 bg-light">Status</th>
                                                        <th class="text-end pe-3 py-3 border-bottom-0 bg-light">Komisi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($reseller->referrals()->with(['referredUser'])->latest()->get() as $ref)
                                                        <tr>
                                                            <td class="ps-3 fw-medium small">{{ $ref->referredUser->name ?? 'User Anonim' }}</td>
                                                            <td class="text-muted small">{{ $ref->description ?? 'Pembelian Sistem' }}</td>
                                                            <td class="text-muted small" style="font-size: 0.75rem;">{{ $ref->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                                            <td>
                                                                @if(strtolower($ref->status) == 'paid') <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5">Paid</span>
                                                                @elseif(strtolower($ref->status) == 'rejected') <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5">Rejected</span>
                                                                @else <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5">Pending</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end pe-3 fw-bold small {{ strtolower($ref->status) == 'rejected' ? 'text-decoration-line-through text-danger opacity-75' : 'text-success' }}">
                                                                Rp {{ number_format($ref->amount, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5" class="text-center text-muted py-4 small">Belum ada transaksi dari kode referral ini.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Riwayat Penarikan Tab -->
                                    <div class="tab-pane fade" id="pills-withdraw-{{ $reseller->id }}" role="tabpanel">
                                        <div class="table-responsive border rounded-4" style="max-height: 300px; overflow-y: auto;">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="bg-light text-muted small position-sticky top-0 shadow-sm" style="z-index: 10;">
                                                    <tr>
                                                        <th class="ps-3 py-3 border-bottom-0 bg-light">Tanggal</th>
                                                        <th class="py-3 border-bottom-0 bg-light">Bank Tujuan</th>
                                                        <th class="py-3 border-bottom-0 bg-light">Status</th>
                                                        <th class="text-end pe-3 py-3 border-bottom-0 bg-light">Nominal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($reseller->withdrawals()->latest()->get() as $wd)
                                                        <tr>
                                                            <td class="ps-3 text-muted small" style="font-size: 0.75rem;">{{ $wd->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                                            <td class="small">
                                                                <div class="fw-bold text-dark">{{ $wd->bank_name }}</div>
                                                                <div class="text-muted" style="font-size: 0.7rem;">{{ $wd->account_number }} (a.n {{ $wd->account_holder }})</div>
                                                            </td>
                                                            <td>
                                                                @if(strtolower($wd->status) == 'approved') <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5">Approved</span>
                                                                @elseif(strtolower($wd->status) == 'rejected') <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2.5">Rejected</span>
                                                                @else <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5">Pending</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-end pe-3 fw-bold text-dark small">
                                                                Rp {{ number_format($wd->amount, 0, ',', '.') }}
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center text-muted py-4 small">Reseller ini belum pernah mengajukan penarikan dana.</td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Timeline Aktivitas Tab -->
                                    <div class="tab-pane fade" id="pills-timeline-{{ $reseller->id }}" role="tabpanel">
                                        <div class="p-3 border rounded-4" style="max-height: 300px; overflow-y: auto;">
                                            <div class="activity-timeline">
                                                @php $hasActivities = false; @endphp
                                                
                                                <!-- Referral Transactions -->
                                                @foreach($referralActivities as $act)
                                                    @php $hasActivities = true; @endphp
                                                    @if(strtolower($act->status) == 'paid')
                                                        <div class="activity-item success">
                                                            <div class="fw-bold text-dark small">Penjualan Sukses</div>
                                                            <p class="text-muted small mb-1">
                                                                <strong>{{ $act->referredUser->name ?? 'User Anonim' }}</strong> berhasil membeli <em>{{ $act->description }}</em>. Komisi sebesar <strong>Rp {{ number_format($act->amount, 0, ',', '.') }}</strong> telah masuk saldo.
                                                            </p>
                                                            <small class="text-secondary opacity-75 d-block" style="font-size: 0.7rem;">{{ $act->created_at?->diffForHumans() ?? '-' }} ({{ $act->created_at?->format('d M Y H:i') }})</small>
                                                        </div>
                                                    @elseif(strtolower($act->status) == 'pending')
                                                        <div class="activity-item warning">
                                                            <div class="fw-bold text-warning-emphasis small">Referral Digunakan</div>
                                                            <p class="text-muted small mb-1">
                                                                <strong>{{ $act->referredUser->name ?? 'User Anonim' }}</strong> mendaftar program <em>{{ $act->description }}</em>. Komisi tertunda sebesar <strong>Rp {{ number_format($act->amount, 0, ',', '.') }}</strong> sedang diproses.
                                                            </p>
                                                            <small class="text-secondary opacity-75 d-block" style="font-size: 0.7rem;">{{ $act->created_at?->diffForHumans() ?? '-' }} ({{ $act->created_at?->format('d M Y H:i') }})</small>
                                                        </div>
                                                    @else
                                                        <div class="activity-item" style="color: #ef4444;">
                                                            <div class="fw-bold text-danger small">Transaksi Ditolak/Batal</div>
                                                            <p class="text-muted small mb-1">
                                                                Referral pembelian oleh <strong>{{ $act->referredUser->name ?? 'User Anonim' }}</strong> dibatalkan atau ditolak oleh admin.
                                                            </p>
                                                            <small class="text-secondary opacity-75 d-block" style="font-size: 0.7rem;">{{ $act->created_at?->diffForHumans() ?? '-' }} ({{ $act->created_at?->format('d M Y H:i') }})</small>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                
                                                <!-- Join Event -->
                                                <div class="activity-item">
                                                    <div class="fw-bold text-dark small">Kemitraan Reseller Dimulai</div>
                                                    <p class="text-muted small mb-1">
                                                        Mendaftar sebagai mitra reseller IdSpora dengan kode referral awal <strong>{{ $reseller->referral_code }}</strong>.
                                                    </p>
                                                    <small class="text-secondary opacity-75 d-block" style="font-size: 0.7rem;">{{ $reseller->created_at?->format('d M Y H:i') ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup Detail</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999;">
        <div id="actionToast" class="toast align-items-center text-white border-0 bg-dark" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px;">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                    <span id="toastMessage">Tindakan berhasil diselesaikan!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-toast="dismiss" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toast Helper
        function showToast(msg) {
            document.getElementById('toastMessage').innerText = msg;
            const toastEl = document.getElementById('actionToast');
            const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
            toast.show();
        }

        // Toggle user status dynamically via API
        function toggleUserStatus(id, name) {
            const switchEl = document.getElementById('statusSwitchRow' + id);
            const badgeModal = document.getElementById('statusBadgeModal' + id);

            const isChecked = switchEl.checked;
            const newStatusValue = isChecked ? 'active' : 'suspended';
            
            const newStatusText = isChecked ? 'Aktif' : 'Suspended';
            const newClass = isChecked ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';

            fetch("{{ route('api.admin.reseller.toggle-user-status') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ id: id, status: newStatusValue })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message);
                    
                    // Update modal badge
                    if (badgeModal) {
                        badgeModal.innerText = newStatusText;
                        badgeModal.className = 'badge rounded-pill px-3 py-1.5 fw-bold fs-7 ' + newClass + ' mb-2';
                    }

                    // Update row data-status attribute for filtering
                    const rowEl = switchEl.closest('tr');
                    if (rowEl) {
                        rowEl.setAttribute('data-status', newStatusText);
                    }
                } else {
                    switchEl.checked = !isChecked;
                    showToast(`Gagal mengubah status: ${data.message || 'Error'}`);
                }
            })
            .catch(err => {
                switchEl.checked = !isChecked;
                showToast(`Terjadi kesalahan jaringan.`);
            });
        }

        // Client-side filtering logic
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('resellerSearchInput');
            const tierSelect = document.getElementById('resellerTierSelect');
            const statusSelect = document.getElementById('resellerStatusSelect');
            const tableRows = document.querySelectorAll('.reseller-row');
            const emptyRow = document.getElementById('emptyRow');

            function filterTable() {
                const query = searchInput.value.toLowerCase().trim();
                const tierFilter = tierSelect.value;
                const statusFilter = statusSelect.value;
                let visibleCount = 0;

                tableRows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const email = row.getAttribute('data-email') || '';
                    const code = row.getAttribute('data-code') || '';
                    const tier = row.getAttribute('data-tier') || '';
                    const status = row.getAttribute('data-status') || '';

                    const matchesSearch = !query || name.includes(query) || email.includes(query) || code.includes(query);
                    const matchesTier = !tierFilter || tier === tierFilter;
                    const matchesStatus = !statusFilter || status === statusFilter;

                    if (matchesSearch && matchesTier && matchesStatus) {
                        row.style.setProperty('display', '', 'important');
                        visibleCount++;
                    } else {
                        row.style.setProperty('display', 'none', 'important');
                    }
                });

                if (visibleCount === 0) {
                    emptyRow.style.setProperty('display', 'table-row', 'important');
                } else {
                    emptyRow.style.setProperty('display', 'none', 'important');
                }
            }

            searchInput.addEventListener('input', filterTable);
            tierSelect.addEventListener('change', filterTable);
            statusSelect.addEventListener('change', filterTable);

            // Initial filter run in case values are pre-filled (like URL search parameters)
            if (searchInput.value) {
                filterTable();
            }

            // Client-side sorting logic
            const resellerTableBody = document.getElementById('resellerTableBody');
            let sortDirections = {
                tier: 'none',
                sales: 'none',
                date: 'none'
            };

            function resetSortIcons() {
                document.getElementById('sortTierIcon').className = 'bi bi-arrow-down-up ms-1 text-muted';
                document.getElementById('sortSalesIcon').className = 'bi bi-arrow-down-up ms-1 text-muted';
                document.getElementById('sortDateIcon').className = 'bi bi-arrow-down-up ms-1 text-muted';
            }

            function sortTable(column) {
                const currentDir = sortDirections[column];
                let nextDir = 'asc';
                if (currentDir === 'asc') {
                    nextDir = 'desc';
                }
                
                sortDirections = {
                    tier: 'none',
                    sales: 'none',
                    date: 'none'
                };
                sortDirections[column] = nextDir;
                
                resetSortIcons();
                
                const iconId = column === 'tier' ? 'sortTierIcon' : (column === 'sales' ? 'sortSalesIcon' : 'sortDateIcon');
                const iconEl = document.getElementById(iconId);
                if (nextDir === 'asc') {
                    iconEl.className = 'bi bi-sort-up ms-1 text-primary';
                } else {
                    iconEl.className = 'bi bi-sort-down ms-1 text-primary';
                }

                const rows = Array.from(document.querySelectorAll('.reseller-row'));
                const tierOrder = { 'Bronze': 1, 'Silver': 2, 'Gold': 3 };

                rows.sort((a, b) => {
                    let valA, valB;
                    if (column === 'tier') {
                        valA = tierOrder[a.getAttribute('data-tier')] || 0;
                        valB = tierOrder[b.getAttribute('data-tier')] || 0;
                    } else if (column === 'sales') {
                        valA = parseFloat(a.getAttribute('data-sales')) || 0;
                        valB = parseFloat(b.getAttribute('data-sales')) || 0;
                    } else if (column === 'date') {
                        valA = parseInt(a.getAttribute('data-date')) || 0;
                        valB = parseInt(b.getAttribute('data-date')) || 0;
                    }

                    if (valA === valB) return 0;
                    if (nextDir === 'asc') {
                        return valA > valB ? 1 : -1;
                    } else {
                        return valA < valB ? 1 : -1;
                    }
                });

                const emptyRow = document.getElementById('emptyRow');
                rows.forEach(row => resellerTableBody.insertBefore(row, emptyRow));
            }

            document.getElementById('headerTier').addEventListener('click', () => sortTable('tier'));
            document.getElementById('headerSales').addEventListener('click', () => sortTable('sales'));
            document.getElementById('headerDate').addEventListener('click', () => sortTable('date'));
        });

        function resetFilters() {
            document.getElementById('resellerSearchInput').value = '';
            document.getElementById('resellerTierSelect').value = '';
            document.getElementById('resellerStatusSelect').value = '';
            // Trigger input event to re-run filter
            document.getElementById('resellerSearchInput').dispatchEvent(new Event('input'));
        }
    </script>
</body>
</html>