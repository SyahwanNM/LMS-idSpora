@extends('layouts.crm')

@section('title', 'Riwayat Broadcast')

@section('styles')
<style>
    /* Hero Header */
    .crm-hero {
        background: linear-gradient(135deg, #1A1D1F 0%, #2A2F34 100%);
        border-radius: 24px;
        padding: 32px;
        color: #fff;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .hero-label {
        background: rgba(109, 40, 217, 0.2);
        color: #a78bfa;
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        display: inline-block;
        margin-bottom: 16px;
        border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .hero-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .ls-wide { letter-spacing: 0.5px; }
    .smaller { font-size: 0.85rem; }
</style>
@endsection

@section('content')
<div class="crm-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <span class="hero-label">Broadcasting Module</span>
        <h1 class="hero-title">Blast Broadcast</h1>
        <p class="hero-subtitle mb-0">Kelola riwayat pengiriman pesan massal dan optimasi strategi jangkauan pengguna.</p>
    </div>
    <div class="mt-4 mt-md-0">
        <a href="{{ route('admin.crm.broadcast.create') }}" class="btn btn-primary shadow-lg border-0 px-4 py-2 rounded-3 fw-600">
            <i class="bi bi-plus-lg me-1"></i> Buat Blast Baru
        </a>
    </div>
</div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card-minimal overflow-hidden shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8fafc;">
                        <tr style="font-size: 13px; color: #64748b; font-weight: 700;">
                            <th class="ps-4 py-3">TANGGAL</th>
                            <th class="py-3">JUDUL / SUBJEK</th>
                            <th class="py-3">SEGMEN</th>
                            <th class="py-3">PLATFORM</th>
                            <th class="py-3 text-center">TARGET</th>
                            <th class="py-3 text-center">STATUS</th>
                            <th class="py-3 text-center pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($broadcasts as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-navy small">{{ $item->created_at->format('d M Y') }}</div>
                                <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $item->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <div class="fw-bold small text-truncate" style="max-width: 250px;">{{ $item->title }}</div>
                                <div class="text-muted smaller" style="font-size: 0.75rem;">{{ Str::limit($item->message, 50) }}</div>
                            </td>
                            <td>
                                @php
                                    $segments = [
                                        'all' => ['bg-light text-dark', 'Semua'],
                                        'reseller' => ['bg-warning-subtle text-warning', 'Reseller'],
                                        'trainer' => ['bg-info-subtle text-info', 'Trainer'],
                                        'no_event' => ['bg-danger-subtle text-danger', 'Belum Ikut Event']
                                    ];
                                    $seg = $segments[$item->segment] ?? ['bg-secondary-subtle', 'Lainnya'];
                                @endphp
                                <span class="badge rounded-pill {{ $seg[0] }}" style="font-size: 0.7rem;">{{ $seg[1] }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if(in_array($item->platform, ['email', 'both']))
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10" title="Email"><i class="bi bi-envelope"></i></span>
                                    @endif
                                    @if(in_array($item->platform, ['whatsapp', 'both']))
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10" title="WhatsApp"><i class="bi bi-whatsapp"></i></span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold">{{ $item->target_count }}</div>
                                <div class="smaller text-muted" style="font-size: 0.65rem;">User</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.7rem;">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>


                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-megaphone text-muted opacity-25" style="font-size: 3rem;"></i>
                                    <p class="text-muted small mt-2">Belum ada riwayat broadcast pengiriman.</p>
                                    <a href="{{ route('admin.crm.broadcast.create') }}" class="btn btn-link btn-sm text-decoration-none">Kirim Broadcast Pertama</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @foreach($broadcasts as $item)
            @php
                $segments = [
                    'all' => ['bg-light text-dark', 'Semua'],
                    'reseller' => ['bg-warning-subtle text-warning', 'Reseller'],
                    'trainer' => ['bg-info-subtle text-info', 'Trainer'],
                    'no_event' => ['bg-danger-subtle text-danger', 'Belum Ikut Event']
                ];
                $seg = $segments[$item->segment] ?? ['bg-secondary-subtle', 'Lainnya'];
            @endphp
            <!-- Modal Detail -->
            <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold">Detail Broadcast</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-4">
                                <label class="text-muted smaller fw-bold text-uppercase ls-wide mb-1">Judul / Subjek</label>
                                <div class="p-3 border rounded-3 bg-light fw-bold">{{ $item->title }}</div>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted smaller fw-bold text-uppercase ls-wide mb-1">Pesan</label>
                                <div class="p-3 border rounded-3 bg-white" style="white-space: pre-wrap; font-size: 0.9rem;">{{ $item->message }}</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4">
                                        <div class="text-muted smaller fw-bold text-uppercase mb-1">Segmen</div>
                                        <div class="fw-bold">{{ $seg[1] }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded-4">
                                        <div class="text-muted smaller fw-bold text-uppercase mb-1">Platform</div>
                                        <div class="fw-bold">{{ ucfirst($item->platform) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
@endsection
