@extends('layouts.admin-trainer')

@section('title', 'Studio Kelas')

@push('admin-trainer-styles')
    <style>
        .studio-hero {
            background: #1e1b4b;
            border-radius: 20px;
            padding: 36px 40px;
            color: #fff;
            margin-bottom: 32px;
            box-shadow: 0 15px 35px rgba(30, 27, 75, 0.15);
            position: relative;
            overflow: hidden;
        }

        .studio-hero::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .detail-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e9ecef;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }
    </style>
@endpush

@section('admin-trainer-content')
        <div class="studio-hero">
            <h1 class="fw-bold mb-2 position-relative" style="z-index: 2; color: #fff;">Studio Kelas</h1>
            <p class="mb-0 text-white-50 position-relative" style="z-index: 2; font-size: 1.1rem;">
                Manajemen Materi (Modul, Video, Quiz) untuk Semua Kelas Trainer
            </p>
        </div>

        <div class="detail-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Kelas</th>
                            <th>Trainer</th>
                            <th>Status</th>
                            <th>Murid Aktif</th>
                            <th>Disetujui Pada</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td class="fw-semibold">{{ $course->name }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($course->trainer->name ?? 'T') }}&background=e2e8f0&color=475569" 
                                             class="rounded-circle" width="32" height="32" alt="">
                                        <span class="fw-medium text-dark">{{ $course->trainer->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $course->status === 'approved' || $course->status === 'published' ? 'success' : 'secondary' }}">
                                        {{ strtoupper($course->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-people-fill text-primary me-2"></i>
                                        {{ $course->enrollments_count ?? 0 }} Siswa
                                    </div>
                                </td>
                                <td class="small text-muted">{{ $course->approved_at ? $course->approved_at->format('d M Y') : '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.courses.studio', $course->id) }}" 
                                       class="btn btn-sm shadow-sm fw-semibold d-inline-flex align-items-center gap-1"
                                       style="background: #1e1b4b; color: white; border-radius: 8px; padding: 6px 14px; transition: all 0.2s ease; border: none; position: relative; z-index: 10; cursor: pointer; pointer-events: auto;"
                                       onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(30, 27, 75, 0.4)';"
                                       onmouseout="this.style.transform='none'; this.style.boxShadow='0 .125rem .25rem rgba(0,0,0,.075)';"
                                       title="Buka Studio Materi">
                                        <i class="bi bi-collection-play-fill"></i> Studio
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada kelas yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
@endsection
