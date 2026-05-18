@extends('layouts.admin')
@section('title', 'Kelola Sertifikat Trainer')
@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">
                    Kelola Sertifikat Trainer
                </h3>
                <p class="text-muted mb-0">
                    Trainer:
                    <strong>{{ $trainer->name }}</strong>
                </p>
            </div>
            <a href="{{ route('admin.trainer.certificates.queue') }}" class="btn btn-outline-secondary">
                Kembali
            </a>
        </div>
        {{-- SIAP DITERBITKAN --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white">
                <strong>
                    Event / Course Siap Sertifikat
                </strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tipe</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th width="180">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingItems as $item)
                                <tr>
                                    <td>
                                        @if($item['context'] == 'event')
                                            <span class="badge bg-primary">EVENT</span>
                                        @else
                                            <span class="badge bg-success">COURSE</span>
                                        @endif
                                    </td>
                                    <td>
                                    <strong>{{ $item['title'] }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $item['type'] }}</small>
                                    </td>
                                    <td>
                                        @if($item['date'])
                                            {{ \Carbon\Carbon::parse($item['date'])->format('d M Y') }}
                                        @else 
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.trainer.certificates.edit',
                                            [
                                                'trainer' => $trainer->id,
                                                'context' => $item['context'],
                                                'id' => $item['id']
                                            ]) }}" class="btn btn-sm btn-primary">Atur Template
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Tidak ada event / course
                                        yang menunggu sertifikat
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- SUDAH TERBIT --}}
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <strong>
                    Sertifikat Trainer
                </strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No Sertifikat</th>
                                <th>Kegiatan</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($certificates as $certificate)
                                <tr>
                                    <td>
                                        <strong>{{ $certificate->certificate_number }}</strong>
                                    </td>
                                    <td>
                                        @if($certificate->certifiable_type === \App\Models\Event::class)
                                            {{ $certificate->certifiable?->title }}
                                        @else
                                            {{ $certificate->certifiable?->name }}
                                        @endif
                                    </td>
                                    <td>
                                    @php
                                        $badge = match ($certificate->status) {
                                            'published' => 'success',
                                            'draft' => 'warning',
                                            'revoked' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                        <span class="badge bg-{{ $badge }}">{{ ucfirst($certificate->status) }}</span>
                                    </td>
                                    <td>
                                        {{ optional($certificate->issued_at)->format('d M Y') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Belum ada sertifikat
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection