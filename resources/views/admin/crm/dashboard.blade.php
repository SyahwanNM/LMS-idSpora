@extends('layouts.crm')

@section('title', 'CRM Dashboard')

@section('styles')
<style>
    .hero-eyebrow {
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: var(--crm-primary);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 6px;
    }
    .hero-eyebrow::before {
        content: '';
        display: inline-block;
        width: 16px; height: 2px;
        background: var(--crm-primary);
        border-radius: 2px;
    }
    .dashboard-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--crm-navy);
        letter-spacing: -0.8px;
        line-height: 1.2;
        margin: 0;
    }
    .dashboard-subtitle {
        font-size: 0.82rem;
        color: var(--crm-text-subtle);
        margin: 6px 0 0;
    }
    .date-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid var(--crm-border);
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--crm-navy);
        box-shadow: var(--crm-shadow-sm);
    }
    .date-pill i { color: var(--crm-primary); }
    .smaller { font-size: 0.82rem; }
    .text-navy { color: var(--crm-navy); }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="hero-eyebrow">Relations Center</div>
        <h1 class="dashboard-title">CRM Dashboard</h1>
        <p class="dashboard-subtitle">Pantau performa, kelola interaksi pelanggan, dan optimasi feedback IDSPora.</p>
    </div>
    <div class="d-flex align-items-center gap-2 mt-3 mt-md-0">
        <span class="date-pill">
            <i class="bi bi-calendar3"></i>
            {{ now()->translatedFormat('d F Y') }}
        </span>
        <a href="{{ route('admin.crm.broadcast.create') }}" class="btn btn-sm fw-700 px-3 hover-scale" style="background:var(--crm-primary);color:#fff;border-radius:9px;font-size:0.8rem;">
            <i class="bi bi-megaphone-fill me-1"></i> Broadcast
        </a>
    </div>
</div>

@if(session('success'))
@endif

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="kpi-card-v2 hover-scale" style="--kpi-color: #7c3aed;">
            <div class="kpi-icon-v2" style="background: rgba(124,58,237,0.1); color: #7c3aed;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="kpi-label">Total Customer</div>
            <div class="kpi-number">{{ number_format($totalCustomers) }}</div>
            <span class="kpi-trend neutral">
                <i class="bi bi-person-check"></i> {{ number_format($activeCustomersCount) }} aktif
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card-v2 hover-scale" style="--kpi-color: #f59e0b;">
            <div class="kpi-icon-v2" style="background: rgba(245,158,11,0.1); color: #d97706;">
                <i class="bi bi-award-fill"></i>
            </div>
            <div class="kpi-label">Sertifikat Terbit</div>
            <div class="kpi-number">{{ number_format($totalCerts) }}</div>
            <span class="kpi-trend up">
                <i class="bi bi-arrow-up-short"></i> Event & Course
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card-v2 hover-scale" style="--kpi-color: #10b981;">
            <div class="kpi-icon-v2" style="background: rgba(16,185,129,0.1); color: #059669;">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>
            <div class="kpi-label">Course Enrollment</div>
            <div class="kpi-number">{{ number_format($totalEnrollments) }}</div>
            <span class="kpi-trend up">
                <i class="bi bi-arrow-up-short"></i> Siswa aktif
            </span>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="kpi-card-v2 hover-scale" style="--kpi-color: #ec4899;">
            <div class="kpi-icon-v2" style="background: rgba(236,72,153,0.1); color: #db2777;">
                <i class="bi bi-headset"></i>
            </div>
            <div class="kpi-label">Tiket Support</div>
            <div class="kpi-number">{{ number_format($newSupportMessages) }}</div>
            <span class="kpi-trend {{ $newSupportMessages > 0 ? 'down' : 'up' }}">
                <i class="bi bi-{{ $newSupportMessages > 0 ? 'exclamation-circle' : 'check-circle' }}"></i>
                {{ $newSupportMessages > 0 ? 'Perlu respon' : 'Semua resolved' }}
            </span>
        </div>
    </div>
</div>

<!-- Insights & Satisfaction Section -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <div class="card-minimal border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-800 text-navy mb-0">Analisis Kepuasan Pelanggan</h5>
                    <p class="text-muted smaller mb-0">Perbandingan tingkat kepuasan peserta pada Event vs Course</p>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                        Filter Waktu
                    </button>
                    <ul class="dropdown-menu border-0 shadow-sm">
                        <li><a class="dropdown-item" href="#">7 Hari Terakhir</a></li>
                        <li><a class="dropdown-item" href="#">30 Hari Terakhir</a></li>
                        <li><a class="dropdown-item" href="#">Semua Waktu</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div style="height: 300px;">
                            <canvas id="satisfactionChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="ps-md-4 mt-4 mt-md-0">
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-700 text-navy small">Event Satisfaction</span>
                                    <span class="badge bg-primary rounded-pill px-3">{{ number_format($avgEventRating, 1) }} / 5.0</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ ($avgEventRating/5)*100 }}%"></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-700 text-navy small">Course Satisfaction</span>
                                    <span class="badge bg-warning text-dark rounded-pill px-3">{{ number_format($avgCourseRating, 1) }} / 5.0</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ ($avgCourseRating/5)*100 }}%"></div>
                                </div>
                            </div>
                            <div class="p-3 bg-light rounded-4 border">
                                <p class="smaller mb-0 text-muted">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    Data dihitung berdasarkan rata-rata rating dari feedback dan ulasan masuk.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-minimal border-0 shadow-sm h-100" style="background: var(--crm-accent-light); border: 1px solid rgba(109, 40, 217, 0.1);">
            <div class="card-body p-4">
                <h6 class="fw-800 text-navy mb-4">Ringkasan Rating</h6>
                <div class="text-center py-3">
                    <div class="display-3 fw-800 text-primary mb-0">{{ number_format(($avgEventRating + $avgCourseRating) / 2, 1) }}</div>
                    <div class="text-warning mb-3">
                        @php $totalAvg = ($avgEventRating + $avgCourseRating) / 2; @endphp
                        @for($i=1; $i<=5; $i++)
                            <i class="bi bi-star{{ $i <= round($totalAvg) ? '-fill' : '' }} fs-5"></i>
                        @endfor
                    </div>
                    <p class="text-muted smaller px-3">Kepuasan pelanggan secara keseluruhan berada pada level <strong>Sangat Baik</strong>. Pertahankan kualitas pelayanan!</p>
                </div>
                <hr class="my-4 opacity-50">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.crm.feedback.index') }}" class="btn btn-navy text-white rounded-pill fw-700 py-2" style="background: var(--crm-navy);">
                        Lihat Detail Analisis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Activity Section -->
    <div class="col-lg-8">
        <div class="card-minimal mb-4">
            <div class="d-flex justify-content-between align-items-center px-4 pt-4 pb-3" style="border-bottom: 1px solid var(--crm-border-soft);">
                <div>
                    <h6 class="fw-800 mb-0" style="color: var(--crm-navy); font-size: 0.95rem;">Registrasi Terbaru</h6>
                    <p class="mb-0 mt-1" style="font-size: 0.78rem; color: var(--crm-text-subtle);">Pendaftar program IDSPora secara real-time</p>
                </div>
                <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-sm px-3 fw-600" style="background: var(--crm-border-soft); color: var(--crm-navy); border-radius: 8px; font-size: 0.8rem;">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th style="padding-left: 1.25rem;">Customer</th>
                            <th>Program / Event</th>
                            <th>Waktu</th>
                            <th style="padding-right: 1.25rem;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentRegistrations as $registration)
                        <tr onclick="window.location='{{ route('admin.crm.customers.show', $registration->user->id) }}'">
                            <td style="padding-left: 1.25rem;">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $registration->user->avatar_url }}"
                                         style="width:38px;height:38px;border-radius:10px;object-fit:cover;border:1.5px solid var(--crm-border);"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($registration->user->name) }}&background=7c3aed&color=fff&bold=true'">
                                    <div>
                                        <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">{{ $registration->user->name }}</div>
                                        <div style="font-size:0.75rem;color:var(--crm-text-subtle);">{{ $registration->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:0.82rem;font-weight:600;color:var(--crm-navy-soft);" class="text-truncate" style="max-width:200px;">{{ $registration->event?->title ?? 'N/A' }}</div>
                                <span class="badge-soft" style="background:rgba(124,58,237,0.08);color:var(--crm-primary);margin-top:2px;">Event</span>
                            </td>
                            <td>
                                <div style="font-size:0.82rem;font-weight:600;color:var(--crm-navy);">{{ $registration->created_at->translatedFormat('d M') }}</div>
                                <div style="font-size:0.75rem;color:var(--crm-text-subtle);">{{ $registration->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td style="padding-right: 1.25rem; text-align:right;">
                                <a href="{{ route('admin.crm.customers.show', $registration->user->id) }}"
                                   class="row-action-btn btn btn-sm"
                                   style="width:30px;height:30px;padding:0;display:inline-flex;align-items:center;justify-content:center;background:var(--crm-border-soft);border-radius:8px;color:var(--crm-navy);">
                                    <i class="bi bi-arrow-right" style="font-size:0.8rem;"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center;padding:3rem;">
                                <i class="bi bi-inbox" style="font-size:2rem;color:var(--crm-text-subtle);"></i>
                                <p style="margin-top:0.75rem;color:var(--crm-text-subtle);font-size:0.85rem;">Belum ada registrasi baru</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card-minimal p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-800 mb-0" style="color:var(--crm-navy);font-size:0.9rem;">Segmentasi Peran</h6>
                        <span class="badge-soft" style="background:var(--crm-border-soft);color:var(--crm-text-muted);">User Roles</span>
                    </div>
                    @php $maxVal = max($totalCustomers, $totalResellers, $totalTrainers, 1); @endphp
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#7c3aed;display:inline-block;"></span>
                                <span style="font-size:0.8rem;font-weight:600;color:var(--crm-text-muted);">Customer Umum</span>
                            </div>
                            <span style="font-size:0.8rem;font-weight:800;color:var(--crm-navy);">{{ number_format($totalCustomers) }}</span>
                        </div>
                        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ ($totalCustomers/$maxVal)*100 }}%;background:#7c3aed;"></div></div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;"></span>
                                <span style="font-size:0.8rem;font-weight:600;color:var(--crm-text-muted);">Reseller Affiliate</span>
                            </div>
                            <span style="font-size:0.8rem;font-weight:800;color:var(--crm-navy);">{{ number_format($totalResellers) }}</span>
                        </div>
                        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ ($totalResellers/$maxVal)*100 }}%;background:#f59e0b;"></div></div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span style="width:8px;height:8px;border-radius:50%;background:#06b6d4;display:inline-block;"></span>
                                <span style="font-size:0.8rem;font-weight:600;color:var(--crm-text-muted);">Trainer / Pemateri</span>
                            </div>
                            <span style="font-size:0.8rem;font-weight:800;color:var(--crm-navy);">{{ number_format($totalTrainers) }}</span>
                        </div>
                        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ ($totalTrainers/$maxVal)*100 }}%;background:#06b6d4;"></div></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-minimal p-4 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="fw-800 mb-0" style="color:var(--crm-navy);font-size:0.9rem;">Event Terpopuler</h6>
                        <span class="badge-soft" style="background:rgba(245,158,11,0.1);color:#d97706;"><i class="bi bi-star-fill me-1"></i>Top 5</span>
                    </div>
                    @forelse($topEvents as $loop_idx => $event)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-border-soft);display:flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:800;color:var(--crm-text-subtle);flex-shrink:0;">#{{ $loop_idx+1 }}</div>
                        <div class="flex-grow-1 min-w-0">
                            <div style="font-size:0.82rem;font-weight:700;color:var(--crm-navy);" class="text-truncate">{{ $event->title }}</div>
                            <div style="font-size:0.72rem;color:var(--crm-text-subtle);">{{ number_format($event->registrations_count) }} pendaftar</div>
                        </div>
                        <div style="font-size:0.78rem;font-weight:800;color:var(--crm-primary);">{{ round(($event->registrations_count / max($totalRegistrations, 1)) * 100) }}%</div>
                    </div>
                    @empty
                    <p style="color:var(--crm-text-subtle);font-size:0.82rem;">Belum ada data event populer.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Side Content / Analytics -->
    <div class="col-lg-4">
        <div class="card-minimal sticky-top" style="top: 80px;">
            <div class="p-4" style="border-bottom: 1px solid var(--crm-border-soft);">
                <h6 class="fw-800 mb-0" style="color:var(--crm-navy);font-size:0.9rem;">Engagement Rank</h6>
                <p style="font-size:0.75rem;color:var(--crm-text-subtle);margin:4px 0 0;">Customer dengan aktivitas tertinggi</p>
            </div>
            <div class="p-4">
                @forelse($topCustomers as $index => $customer)
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="position-relative" style="flex-shrink:0;">
                        <img src="{{ $customer->avatar_url }}"
                             style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:1.5px solid var(--crm-border);"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=f5f3ff&color=7c3aed&bold=true'">
                        <span style="position:absolute;bottom:-4px;right:-4px;width:18px;height:18px;border-radius:5px;background:var(--crm-primary);color:#fff;font-size:0.55rem;font-weight:800;display:flex;align-items:center;justify-content:center;border:2px solid #fff;">#{{ $index+1 }}</span>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div style="font-size:0.82rem;font-weight:700;color:var(--crm-navy);" class="text-truncate">{{ $customer->name }}</div>
                        <div class="d-flex gap-2 mt-1">
                            <span style="font-size:0.7rem;color:var(--crm-text-subtle);"><i class="bi bi-calendar-event me-1"></i>{{ $customer->event_registrations_count }} Event</span>
                            <span style="font-size:0.7rem;color:var(--crm-text-subtle);"><i class="bi bi-journal-bookmark me-1"></i>{{ $customer->enrollments_count }} Course</span>
                        </div>
                    </div>
                    <a href="{{ route('admin.crm.customers.show', $customer->id) }}"
                       style="width:30px;height:30px;border-radius:8px;background:var(--crm-border-soft);display:flex;align-items:center;justify-content:center;color:var(--crm-navy);flex-shrink:0;"
                       class="text-decoration-none">
                        <i class="bi bi-arrow-right" style="font-size:0.75rem;"></i>
                    </a>
                </div>
                @empty
                <p style="color:var(--crm-text-subtle);font-size:0.82rem;text-align:center;padding:2rem 0;">Belum ada data aktivitas</p>
                @endforelse

                <div class="quick-action-card mt-2">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-lightbulb-fill" style="color:rgba(255,255,255,0.8);"></i>
                        <span style="font-size:0.82rem;font-weight:700;">Insights & Tips</span>
                    </div>
                    <p style="font-size:0.75rem;opacity:0.8;margin-bottom:1rem;line-height:1.5;">Kirim promo khusus ke segmen yang belum pernah mendaftar event untuk meningkatkan konversi.</p>
                    <a href="{{ route('admin.crm.customers.index') }}" style="display:block;background:rgba(255,255,255,0.15);color:#fff;text-align:center;border-radius:8px;padding:8px;font-size:0.8rem;font-weight:700;text-decoration:none;">Buka Data Pelanggan</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('satisfactionChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['1 ★', '2 ★', '3 ★', '4 ★', '5 ★'],
                datasets: [
                    {
                        label: 'Event Feedback',
                        data: @json($eventRatingData),
                        backgroundColor: '#7c3aed',
                        borderRadius: 6,
                        borderSkipped: false,
                    },
                    {
                        label: 'Course Review',
                        data: @json($courseRatingData),
                        backgroundColor: '#fbbf24',
                        borderRadius: 6,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, padding: 20, font: { family: 'Poppins', size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: '#1e1b4b',
                        padding: 12,
                        titleFont: { family: 'Poppins' },
                        bodyFont: { family: 'Poppins' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { precision: 0, font: { family: 'Poppins', size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Poppins', size: 11 } }
                    }
                }
            }
        });
    });
</script>
@endsection
