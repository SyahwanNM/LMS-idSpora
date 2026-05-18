@extends('layouts.admin')

@section('title', 'Sertifikat Trainer')

@section('content')
<div class="container py-4">
    <h3>Sertifikat Trainer</h3>
    <p class="text-muted">Daftar sertifikat trainer yang sudah diterbitkan.</p>

    <a href="{{ route('admin.trainer.certificates.queue') }}" class="btn btn-primary mb-3">
        Buka Antrian Sertifikat
    </a>

    <div class="card">
        <div class="card-body">
            @forelse($certificates as $certificate)
                <div class="border-bottom py-2">
                    <strong>{{ $certificate->certificate_number ?? '-' }}</strong><br>
                    Trainer: {{ $certificate->trainer?->name ?? '-' }}<br>
                    Status: {{ $certificate->status ?? '-' }}
                </div>
            @empty
                <p class="text-muted mb-0">Belum ada sertifikat trainer.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        {{ $certificates->links() }}
    </div>
</div>
@endsection