@extends('layouts.crm')

@section('title', 'Detail Customer')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 text-dark fw-semibold"><i class="bi bi-person me-2"></i>Detail Customer</h4>
        <p class="text-muted small mb-0">Informasi lengkap customer</p>
    </div>
    <div>
        <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-outline-secondary me-2">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('admin.crm.customers.edit', $customer) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Customer Info -->
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body text-center">
                <div class="avatar-circle mx-auto mb-3" style="width:120px;height:120px;">
                    <img src="{{ $customer->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                </div>
                <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
                <p class="text-muted mb-3">{{ $customer->email }}</p>
                @if($customer->role === 'reseller')
                    <span class="badge bg-primary fs-6">Reseller</span>
                @elseif($customer->role === 'trainer')
                    <span class="badge bg-info fs-6">Trainer</span>
                @else
                    <span class="badge bg-secondary fs-6">User</span>
                @endif
                
                <hr class="my-4">
                
                <div class="text-start">
                    @if($customer->phone)
                        <div class="mb-3">
                            <small class="text-muted d-block">Telepon</small>
                            <div class="fw-semibold"><i class="bi bi-telephone me-2"></i>{{ $customer->phone }}</div>
                        </div>
                    @endif
                    @if($customer->website)
                        <div class="mb-3">
                            <small class="text-muted d-block">Website</small>
                            <div class="fw-semibold">
                                <a href="{{ $customer->website }}" target="_blank" class="text-decoration-none">
                                    <i class="bi bi-globe me-2"></i>{{ $customer->website }}
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="mb-3">
                        <small class="text-muted d-block">Bergabung</small>
                        <div class="fw-semibold">{{ $customer->created_at->format('d M Y') }}</div>
                    </div>
                </div>
                
                @if($customer->bio)
                    <hr class="my-4">
                    <div class="text-start">
                        <small class="text-muted d-block mb-2">Bio</small>
                        <p class="mb-0">{{ $customer->bio }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Registrations & Enrollments -->
    <div class="col-12 col-md-8">
        <!-- Event Registrations -->
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Registrasi Event</h5>
                <span class="badge bg-success">{{ $registrations->count() }}</span>
            </div>
            <div class="card-body">
                @if($registrations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Event</th>
                                    <th>Tanggal Event</th>
                                    <th>Status</th>
                                    <th>Tanggal Registrasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registrations as $registration)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $registration->event->title }}</div>
                                        </td>
                                        <td>
                                            <small>{{ $registration->event->event_date ? \Carbon\Carbon::parse($registration->event->event_date)->format('d M Y') : '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $registration->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($registration->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $registration->created_at->format('d M Y') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x" style="font-size: 2rem; color: #ccc;"></i>
                        <p class="text-muted mt-3 small">Belum ada registrasi event</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Course Enrollments -->
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Enrollment Course</h5>
                <span class="badge bg-warning">{{ $enrollments->count() }}</span>
            </div>
            <div class="card-body">
                @if($enrollments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Tanggal Enrollment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $enrollment->course->name ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $enrollment->status === 'active' ? 'success' : ($enrollment->status === 'completed' ? 'primary' : 'secondary') }}">
                                                {{ ucfirst($enrollment->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $enrollment->created_at->format('d M Y') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-journal-x" style="font-size: 2rem; color: #ccc;"></i>
                        <p class="text-muted mt-3 small">Belum ada enrollment course</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

