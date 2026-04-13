<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Reseller - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    @include('partials.navbar-reseller')

    <main class="main-content min-vh-100">
        <div class="p-4 p-md-5">
            <h2 class="fw-bold text-dark mb-4">Master Data Reseller</h2>
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3">Profil Reseller</th>
                                    <th class="py-3">Kode Referral</th>
                                    <th class="py-3">Total Earnings</th>
                                    <th class="py-3">Konversi</th>
                                    <th class="py-3">Tier</th>
                                    <th class="pe-4 py-3 text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($resellers as $reseller)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-light text-dark fw-bold border-warning" style="width:40px;height:40px;border-radius:50%;display:grid;place-items:center;">
                                                {{ strtoupper(substr($reseller->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $reseller->name }}</div>
                                                <div class="small text-muted">{{ $reseller->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border px-2 py-1"><i class="bi bi-tag-fill text-warning me-1"></i>{{ $reseller->referral_code }}</span></td>
                                    <td class="fw-bold text-success">Rp {{ number_format($reseller->total_earned ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $reseller->referrals_count ?? 0 }} Klien</div>
                                        <div class="small text-muted">Join: {{ $reseller->created_at?->format('d M Y') ?? '-' }}</div>
                                    </td>
                                    <td>
                                        @php
                                            $count = $reseller->referrals_count ?? 0;
                                            $tier = 'Bronze'; $class = 'bg-secondary';
                                            if($count >= 151) { $tier = 'Gold'; $class = 'bg-warning text-dark'; }
                                            elseif($count >= 51) { $tier = 'Silver'; $class = 'bg-info text-dark'; }
                                        @endphp
                                        <span class="badge {{ $class }} rounded-pill px-3">{{ $tier }}</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $reseller->id }}">
                                            <i class="bi bi-eye-fill me-1"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @foreach($resellers as $reseller)
    <div class="modal fade" id="detailModal{{ $reseller->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 shadow">
                
                <div class="modal-header border-bottom p-4 bg-light rounded-top-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="avatar-circle bg-white text-dark fw-bold border-warning shadow-sm" style="width:50px;height:50px;border-radius:50%;display:grid;place-items:center;font-size:1.2rem;">
                            {{ strtoupper(substr($reseller->name, 0, 2)) }}
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0">{{ $reseller->name }}</h5>
                            <small class="text-muted"><i class="bi bi-envelope-fill me-1"></i> {{ $reseller->email }} | Bergabung: {{ $reseller->created_at?->format('d M Y') ?? '-' }}</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <div class="row g-3 mb-4">
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="small text-muted mb-1">Total Pendapatan</div>
                                <h5 class="fw-bold text-success mb-0">Rp {{ number_format($reseller->total_earned ?? 0, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="small text-muted mb-1">Sisa Saldo Akun</div>
                                <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($reseller->wallet_balance ?? 0, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="small text-muted mb-1">Total Penarikan (Sukses)</div>
                                <h5 class="fw-bold text-dark mb-0">
                                    Rp {{ number_format($reseller->withdrawals()->where('status', 'approved')->sum('amount') ?? 0, 0, ',', '.') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <div class="small text-muted mb-1">Referral Berhasil</div>
                                <h5 class="fw-bold text-dark mb-0">{{ $reseller->referrals()->where('status', 'paid')->count() }} Klien</h5>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills mb-3 border-bottom pb-3 gap-2" id="pills-tab-{{ $reseller->id }}" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill fw-medium px-4" id="pills-transaksi-tab-{{ $reseller->id }}" data-bs-toggle="pill" data-bs-target="#pills-transaksi-{{ $reseller->id }}" type="button" role="tab">Riwayat Referral</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill fw-medium px-4" id="pills-withdraw-tab-{{ $reseller->id }}" data-bs-toggle="pill" data-bs-target="#pills-withdraw-{{ $reseller->id }}" type="button" role="tab">Riwayat Penarikan</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent-{{ $reseller->id }}">
                        
                        <div class="tab-pane fade show active" id="pills-transaksi-{{ $reseller->id }}" role="tabpanel">
                            <div class="table-responsive border rounded-3">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted small">
                                        <tr>
                                            <th class="ps-3 py-3 border-bottom-0">Nama Pembeli</th>
                                            <th class="py-3 border-bottom-0">Produk/Event</th>
                                            <th class="py-3 border-bottom-0">Tanggal</th>
                                            <th class="py-3 border-bottom-0">Status</th>
                                            <th class="text-end pe-3 py-3 border-bottom-0">Komisi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reseller->referrals()->with(['referredUser'])->latest()->get() as $ref)
                                        <tr>
                                            <td class="ps-3 fw-medium">{{ $ref->referredUser->name ?? 'User Anonim' }}</td>
                                            <td class="text-muted small">{{ $ref->description ?? 'Pembelian Sistem' }}</td>
                                            <td class="text-muted small">{{ $ref->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                            <td>
                                                @if(strtolower($ref->status) == 'paid') <span class="badge bg-success bg-opacity-10 text-success">Paid</span>
                                                @elseif(strtolower($ref->status) == 'rejected') <span class="badge bg-danger bg-opacity-10 text-danger">Rejected</span>
                                                @else <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3 fw-bold {{ strtolower($ref->status) == 'rejected' ? 'text-decoration-line-through text-danger opacity-75' : 'text-success' }}">
                                                Rp {{ number_format($ref->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada transaksi dari kode referral ini.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pills-withdraw-{{ $reseller->id }}" role="tabpanel">
                            <div class="table-responsive border rounded-3">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light text-muted small">
                                        <tr>
                                            <th class="ps-3 py-3 border-bottom-0">Tanggal Pengajuan</th>
                                            <th class="py-3 border-bottom-0">Tujuan Bank/E-Wallet</th>
                                            <th class="py-3 border-bottom-0">Status</th>
                                            <th class="text-end pe-3 py-3 border-bottom-0">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($reseller->withdrawals()->latest()->get() as $wd)
                                        <tr>
                                            <td class="ps-3 text-muted small">{{ $wd->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $wd->bank_name }}</div>
                                                <div class="small text-muted">{{ $wd->account_number }} (a.n {{ $wd->account_holder }})</div>
                                            </td>
                                            <td>
                                                @if(strtolower($wd->status) == 'approved') <span class="badge bg-success">Approved</span>
                                                @elseif(strtolower($wd->status) == 'rejected') <span class="badge bg-danger">Rejected</span>
                                                @else <span class="badge bg-warning text-dark">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3 fw-bold text-dark">
                                                Rp {{ number_format($wd->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-4">Reseller ini belum pernah mengajukan penarikan dana.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div> </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Tutup Detail</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>