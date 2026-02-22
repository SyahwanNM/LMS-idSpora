@extends('layouts.admin')

@section('title', 'Manajemen Support | CRM')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h4 class="fw-bold mb-0">Pesan Support & Kendala</h4>
            <p class="text-muted">Kelola masukan, pertanyaan, dan kendala dari pengguna</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 py-3">
            <form action="{{ route('admin.crm.support.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="type" class="form-select border-0 bg-light" onchange="this.form.submit()">
                        <option value="">Semua Jenis</option>
                        <option value="kendala" {{ request('type') == 'kendala' ? 'selected' : '' }}>Kendala / Bug</option>
                        <option value="pertanyaan" {{ request('type') == 'pertanyaan' ? 'selected' : '' }}>Pertanyaan</option>
                        <option value="masukan" {{ request('type') == 'masukan' ? 'selected' : '' }}>Masukan</option>
                        <option value="lainnya" {{ request('type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select border-0 bg-light" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>Baru</option>
                        <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Diproses</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Selesai</option>
                        <option value="ignored" {{ request('status') == 'ignored' ? 'selected' : '' }}>Diabaikan</option>
                    </select>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr style="font-size: 13px; color: #666;">
                            <th class="ps-4 py-3">PENGIRIM</th>
                            <th class="py-3">JENIS & SUBJEK</th>
                            <th class="py-3">PESAN</th>
                            <th class="py-3">LAMPIRAN</th>
                            <th class="py-3">STATUS</th>
                            <th class="py-3 text-center pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $msg)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $msg->name }}</div>
                                    <div class="text-muted small">{{ $msg->email }}</div>
                                    <div class="text-muted small">{{ $msg->created_at->format('d M Y, H:i') }}</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill mb-1 
                                        {{ $msg->type == 'kendala' ? 'bg-danger-subtle text-danger' : 
                                           ($msg->type == 'pertanyaan' ? 'bg-info-subtle text-info' : 
                                           ($msg->type == 'masukan' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary')) }}">
                                        {{ ucfirst($msg->type) }}
                                    </span>
                                    <div class="fw-bold small text-truncate" style="max-width: 200px;">{{ $msg->subject }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small" style="max-width: 300px; white-space: normal;">
                                        {{ Str::limit($msg->message, 100) }}
                                        @if(strlen($msg->message) > 100)
                                            <a href="#" class="text-primary decoration-none" data-bs-toggle="modal" data-bs-target="#modalMsg{{ $msg->id }}">Lihat</a>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($msg->attachment)
                                        <a href="{{ asset('uploads/' . $msg->attachment) }}" target="_blank">
                                            <img src="{{ asset('uploads/' . $msg->attachment) }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                        </a>
                                    @else
                                        <span class="text-muted small italic">Tidak ada</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'new' => 'bg-primary',
                                            'processed' => 'bg-warning text-dark',
                                            'resolved' => 'bg-success',
                                            'ignored' => 'bg-secondary'
                                        ][$msg->status] ?? 'bg-light text-dark';
                                        
                                        $statusLabel = [
                                            'new' => 'Baru',
                                            'processed' => 'Diproses',
                                            'resolved' => 'Selesai',
                                            'ignored' => 'Diabaikan'
                                        ][$msg->status] ?? $msg->status;
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                            <li><h6 class="dropdown-header">Ubah Status</h6></li>
                                            <li>
                                                <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="processed">
                                                    <button type="submit" class="dropdown-item small">Set Diproses</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="resolved">
                                                    <button type="submit" class="dropdown-item small">Set Selesai</button>
                                                </form>
                                            </li>
                                            <li>
                                                <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="ignored">
                                                    <button type="submit" class="dropdown-item small text-danger">Abaikan</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Detail Pesan -->
                            <div class="modal fade" id="modalMsg{{ $msg->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title fw-bold">Detail Pesan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="text-muted small fw-bold text-uppercase">Subjek</label>
                                                <p class="mb-0 fw-bold">{{ $msg->subject }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="text-muted small fw-bold text-uppercase">Pesan</label>
                                                <p class="mb-0" style="white-space: pre-wrap;">{{ $msg->message }}</p>
                                            </div>
                                            @if($msg->attachment)
                                            <div>
                                                <label class="text-muted small fw-bold text-uppercase d-block mb-2">Lampiran</label>
                                                <a href="{{ asset('uploads/' . $msg->attachment) }}" target="_blank">
                                                    <img src="{{ asset('uploads/' . $msg->attachment) }}" class="img-fluid rounded-3 shadow-sm">
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">Tidak ada pesan support ditemukan.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($messages->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $messages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
