@extends('layouts.crm')

@section('title', 'CRM Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-2 text-dark fw-bold">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>CRM Dashboard
        </h2>
        <p class="text-muted mb-0">Overview customer dan aktivitas registrasi</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistics Cards with 3D Effect -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Total Customer</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ number_format($totalCustomers) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-success">
                    <i class="bi bi-person-check-fill"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Customer Aktif</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ number_format($activeCustomers) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-info">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Registrasi Event</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ number_format($totalRegistrations) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-warning">
                    <i class="bi bi-journal-text"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Enrollment Course</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ number_format($totalEnrollments) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Registrations -->
    <div class="col-12 col-xl-8">
        <div class="card-3d">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4">
                <h5 class="card-title mb-0 fw-semibold text-dark">
                    Registrasi Terbaru
                </h5>
                <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-sm btn-outline-primary">
                    Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body">
                @if($recentRegistrations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Event</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentRegistrations as $registration)
                                    <tr style="transition: all 0.3s ease;">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-3" style="width:40px;height:40px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                                    <img src="{{ $registration->user->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $registration->user->name }}</div>
                                                    <small class="text-muted">{{ $registration->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $registration->event?->title ?? 'Event tidak ditemukan' }}</div>
                                            <small class="text-muted">{{ $registration->event?->event_date ? \Carbon\Carbon::parse($registration->event->event_date)->format('d M Y') : '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $registration->created_at->format('d M Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">Belum ada registrasi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Events -->
    <div class="col-12 col-xl-4">
        <div class="card-3d h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title mb-0 fw-semibold text-dark">
                    Event Terpopuler
                </h5>
            </div>
            <div class="card-body">
                @if($topEvents->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($topEvents as $event)
                            <div class="list-group-item px-0 border-0 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">{{ $event->title }}</div>
                                        <small class="text-muted">{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : '-' }}</small>
                                    </div>
                                    <span class="badge bg-primary">
                                        {{ $event->registrations_count }} peserta
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x" style="font-size: 2rem; color: #ccc;"></i>
                        <p class="text-muted mt-3 small">Belum ada event</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

