@extends('layouts.admin-trainer')

@section('title', 'Antrian Sertifikat Trainer')

@section('admin-trainer-content')
    <div class="container py-4">
        <h3>Antrian Sertifikat Trainer</h3>
        <p class="text-muted">Pilih trainer untuk mengelola sertifikat.</p>

        <div class="card">
            <div class="card-body">
                @forelse($trainers as $trainer)
                    <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                        <div>
                            <strong>{{ $trainer->name }}</strong><br>
                            <small class="text-muted">{{ $trainer->email }}</small>
                        </div>

                        <a href="{{ route('admin.trainer.certificates.show', $trainer) }}" class="btn btn-sm btn-primary">
                            Kelola Sertifikat
                        </a>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada trainer.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-3">
            {{ $trainers->links() }}
        </div>
    </div>
@endsection