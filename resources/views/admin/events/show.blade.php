@extends('layouts.admin')

@section('title', 'Detail Event')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-dark">
                            <i class="bi bi-calendar-event me-2"></i>
                            Detail Event
                        </h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Event Image -->
                        <div class="col-lg-4 mb-4">
                            <div class="position-relative">
                                @if($event->image)
                                    <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" 
                                         class="img-fluid rounded shadow-sm" style="width: 100%; height: 300px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                         style="height: 300px;">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                                            <p class="mt-2 mb-0">No Image</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <h2 class="text-dark mb-3">{{ $event->title }}</h2>
                                
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-fill text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Pembicara</small>
                                                <strong>{{ $event->speaker }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt-fill text-success me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Lokasi</small>
                                                <strong>{{ $event->location }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-date text-info me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Tanggal</small>
                                                <strong>{{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock text-warning me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Waktu</small>
                                                <strong>{{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }} WIB</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-currency-dollar text-success me-2"></i>
                                        <small class="text-muted">Harga Tiket</small>
                                    </div>
                                    <h4 class="text-success mb-0">Rp{{ number_format($event->price, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event Description -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="border-top pt-4">
                                <h5 class="text-dark mb-3">
                                    <i class="bi bi-file-text me-2"></i>Deskripsi Event
                                </h5>
                                <div class="bg-light rounded p-4">
                                    <div class="event-description">
                                        {!! $event->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                                </a>
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary btn-lg px-4">
                                    <i class="bi bi-pencil me-1"></i> Edit Event
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.event-description {
    line-height: 1.6;
    color: #333;
}

.event-description h1,
.event-description h2,
.event-description h3,
.event-description h4,
.event-description h5,
.event-description h6 {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.event-description p {
    margin-bottom: 1rem;
}

.event-description ul,
.event-description ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.event-description blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6c757d;
}

.event-description img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

.event-description table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.event-description table th,
.event-description table td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}

.event-description table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
@endsection