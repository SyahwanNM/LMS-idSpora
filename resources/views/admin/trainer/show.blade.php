@extends('layouts.admin')

@section('title', 'Detail Trainer')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        /* Trainer Hero Section */
        .trainer-hero {
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            border-radius: 24px;
            padding: 48px;
            color: #fff;
            margin-bottom: 36px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .hero-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        /* Detail Cards */
        .detail-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e9ecef;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .detail-card h5 {
            font-size: 18px;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .detail-card-header h5 {
            margin: 0;
            padding: 0;
            border: 0;
        }

        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
            width: 150px;
            flex-shrink: 0;
            font-size: 14px;
        }

        .detail-value {
            color: #212529;
            flex-grow: 1;
            font-size: 14px;
        }

        /* Stats Card */
        .stat-box {
            background: linear-gradient(135deg, #ede7f6 0%, #e8eaf6 100%);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            border: 2px solid #d1c4e9;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(57, 73, 171, 0.15);
        }

        .stat-box .stat-icon {
            font-size: 40px;
            color: #3949ab;
            margin-bottom: 12px;
        }

        .stat-box .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1a237e;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-box .stat-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 600;
        }

        /* Action Buttons */
        .btn-action-large {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .btn-edit-large {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: #fff;
            border: 0;
        }

        .btn-edit-large:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26, 35, 126, 0.3);
            color: #fff;
        }

        .btn-delete-large {
            background: #fff;
            color: #c62828;
            border: 2px solid #c62828;
        }

        .btn-delete-large:hover {
            background: #c62828;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(198, 40, 40, 0.3);
        }

        /* Sidebar Navigation */
        .trainer-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .trainer-sidebar {
            width: 260px;
            background: #fff;
            padding: 24px 16px;
            border-right: 1px solid #eee;
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .trainer-main {
            flex-grow: 1;
            padding: 32px;
            background-color: #F8F9FA;
        }

        .nav-menu-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 1px;
            margin-bottom: 12px;
            margin-top: 24px;
            display: block;
            padding-left: 16px;
        }

        .nav-menu-label:first-child {
            margin-top: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            color: #1e293b;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .sidebar-link i {
            font-size: 18px;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: #f8fafc;
            color: #3949ab;
        }

        .sidebar-link:hover i {
            color: #3949ab;
        }

        .sidebar-link.active {
            background-color: #3949ab;
            color: #fff;
        }

        .sidebar-link.active i {
            color: #fff;
        }

        .sidebar-parent {
            justify-content: space-between;
        }

        .sidebar-parent .sidebar-chevron {
            font-size: 0.8rem;
            transition: transform 0.2s ease;
        }

        .sidebar-parent[aria-expanded='true'] .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            margin: 4px 0 8px;
        }

        .sidebar-submenu .sidebar-link {
            margin-left: 14px;
            padding: 7px 10px;
            font-size: 0.82rem;
            border-radius: 8px;
        }

        .sidebar-submenu .sidebar-link i {
            font-size: 0.95rem;
        }
        
        /* Custom Tabs Styling */
        .trainer-tabs .nav-link {
            color: #64748b;
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
            margin-right: 8px;
            transition: all 0.2s ease;
        }
        
        .trainer-tabs .nav-link:hover {
            color: #3949ab;
            border-color: #e0e7ff;
        }
        
        .trainer-tabs .nav-link.active {
            color: #3949ab;
            background: transparent;
            border-color: #3949ab;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .trainer-hero {
                padding: 32px 24px;
            }

            .trainer-sidebar {
                display: none !important;
            }

            .trainer-main {
                padding: 20px;
            }

            .detail-label {
                width: 120px;
            }
            
            .trainer-tabs {
                flex-wrap: nowrap;
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 5px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="trainer-wrapper">
        <!-- Sidebar Navigation (local partial to avoid missing named route) -->
        @include('admin.trainer.partials.sidebar')

        <main class="trainer-main">
            @php $editBox = request('edit'); @endphp

            <!-- Late Upload Warning -->
            @php
                $lateUploads = (int) data_get($trainerActivity ?? [], 'late_uploads', 0);
            @endphp
            @if($lateUploads > 0)
                <div class="alert alert-{{ $lateUploads >= 2 ? 'danger' : 'warning' }} d-flex align-items-center mb-4" role="alert" style="border-radius: 12px; font-weight: 500; border-left: 5px solid {{ $lateUploads >= 2 ? '#dc3545' : '#ffc107' }};">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <strong>Peringatan Keterlambatan:</strong> Trainer ini memiliki {{ $lateUploads }}x riwayat keterlambatan upload materi. 
                        @if($lateUploads >= 2) <br><small>Pertimbangkan untuk memberikan sanksi atau membekukan akun sementara.</small> @endif
                    </div>
                </div>
            @endif

            <!-- Hero Header with Trainer Info -->
            <div class="trainer-hero">
                <div class="d-flex align-items-center gap-4 position-relative" style="z-index: 2;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($trainer->name) }}&background=fff&color=3949ab&bold=true&size=200"
                        class="hero-avatar" alt="{{ $trainer->name }}">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <h1 class="mb-0" style="font-size: 40px; font-weight: 800;">{{ $trainer->name }}</h1>
                            @php
                                $statusLabel = match($trainer->user_status) {
                                    'active' => '<i class="bi bi-check-circle-fill me-1"></i> Aktif / Tersedia',
                                    'inactive' => '<i class="bi bi-pause-circle-fill me-1"></i> Tidak Tersedia',
                                    'suspended' => '<i class="bi bi-x-circle-fill me-1"></i> Ditangguhkan',
                                    default => '<i class="bi bi-check-circle-fill me-1"></i> Aktif',
                                };
                                $statusColor = match($trainer->user_status) {
                                    'active' => '#2e7d32',
                                    'inactive' => '#ed6c02',
                                    'suspended' => '#c62828',
                                    default => '#2e7d32',
                                };
                            @endphp
                            <span class="badge"
                                style="background: {{ $statusColor }}; padding: 8px 16px; font-size: 14px;">
                                {!! $statusLabel !!}
                            </span>
                        </div>
                        <p class="mb-3" style="font-size: 18px; opacity: 0.9;">
                            <i class="bi bi-envelope-fill me-2"></i>{{ $trainer->email }}
                        </p>
                        @if($trainer->phone)
                            <p class="mb-0" style="font-size: 18px; opacity: 0.9;">
                                <i class="bi bi-telephone-fill me-2"></i>{{ $trainer->phone }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <div class="stat-number">{{ $trainer->courses_as_trainer_count ?? 0 }}</div>
                        <div class="stat-label">Total Kelas</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-event-fill"></i>
                        </div>
                        <div class="stat-number">{{ $trainer->events_as_trainer_count ?? 0 }}</div>
                        <div class="stat-label">Total Event</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="stat-number">Rp {{ number_format($walletBalance ?? 0, 0, ',', '.') }}</div>
                        <div class="stat-label">Saldo Dompet</div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs trainer-tabs mb-4" id="trainerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $editBox || !request()->has('tab') ? 'active' : '' }}" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">
                        <i class="bi bi-person-badge me-2"></i>Profil & Akun
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="classes-tab" data-bs-toggle="tab" data-bs-target="#classes" type="button" role="tab" aria-controls="classes" aria-selected="false">
                        <i class="bi bi-journals me-2"></i>Kelas & Event
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">
                        <i class="bi bi-star-fill text-warning me-2"></i>Rating & Ulasan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="finance-tab" data-bs-toggle="tab" data-bs-target="#finance" type="button" role="tab" aria-controls="finance" aria-selected="false">
                        <i class="bi bi-cash-stack text-success me-2"></i>Keuangan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates" type="button" role="tab" aria-controls="certificates" aria-selected="false">
                        <i class="bi bi-award-fill text-primary me-2"></i>Sertifikat
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="trainerTabsContent">
                
                <!-- TAB: PROFIL & AKUN -->
                <div class="tab-pane fade {{ $editBox || !request()->has('tab') ? 'show active' : '' }}" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <div class="col-lg-6">
                            <!-- Personal Information -->
                            <div class="detail-card">
                                <div class="detail-card-header">
                                    <h5>
                                        <i class="bi bi-person-circle" style="color: #3949ab;"></i>
                                        Informasi Pribadi
                                    </h5>
                                    <a href="{{ route('admin.trainer.show', ['trainer' => $trainer->id, 'edit' => 'personal']) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                </div>

                                @if($editBox === 'personal')
                                    <form action="{{ route('admin.trainer.update', $trainer) }}" method="POST" class="row g-3">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="edit_box" value="personal">

                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Nama Lengkap</label>
                                            <input type="text" name="name" value="{{ old('name', $trainer->name) }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Email</label>
                                            <input type="email" name="email" value="{{ old('email', $trainer->email) }}"
                                                class="form-control" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">No. WhatsApp</label>
                                            <input type="text" name="phone" value="{{ old('phone', $trainer->phone) }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Status Ketersediaan</label>
                                            <select name="user_status" class="form-select">
                                                <option value="active" {{ old('user_status', $trainer->user_status) === 'active' ? 'selected' : '' }}>Aktif (Siap Mengajar)</option>
                                                <option value="inactive" {{ old('user_status', $trainer->user_status) === 'inactive' ? 'selected' : '' }}>Tidak Tersedia (Cuti/Pasif)</option>
                                                <option value="suspended" {{ old('user_status', $trainer->user_status) === 'suspended' ? 'selected' : '' }}>Suspended (Dibekukan)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Profesi</label>
                                            <input type="text" name="profession" value="{{ old('profession', $trainer->profession) }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-semibold">Institusi</label>
                                            <input type="text" name="institution"
                                                value="{{ old('institution', $trainer->institution) }}" class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Website</label>
                                            <input type="text" name="website" value="{{ old('website', $trainer->website) }}"
                                                class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">URL LinkedIn</label>
                                            <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $trainer->linkedin_url) }}"
                                                class="form-control" placeholder="https://linkedin.com/in/username">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Keahlian (Skills)</label>
                                            <textarea name="trainer_skills" class="form-control" rows="2" placeholder="Contoh: Public Speaking, Web Development (Pisahkan dengan koma)">{{ old('trainer_skills', is_array($trainer->trainer_skills) ? implode(', ', $trainer->trainer_skills) : $trainer->trainer_skills) }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Pengalaman Kerja</label>
                                            <textarea name="trainer_experiences" class="form-control" rows="2">{{ old('trainer_experiences', is_array($trainer->trainer_experiences) ? implode(', ', $trainer->trainer_experiences) : $trainer->trainer_experiences) }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Riwayat Pendidikan</label>
                                            <textarea name="trainer_educations" class="form-control" rows="2">{{ old('trainer_educations', is_array($trainer->trainer_educations) ? implode(', ', $trainer->trainer_educations) : $trainer->trainer_educations) }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label small fw-semibold">Sertifikasi Profesi</label>
                                            <textarea name="trainer_certifications" class="form-control" rows="2">{{ old('trainer_certifications', is_array($trainer->trainer_certifications) ? implode(', ', $trainer->trainer_certifications) : $trainer->trainer_certifications) }}</textarea>
                                        </div>
                                        
                                        <div class="col-12 d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.trainer.show', $trainer) }}"
                                                class="btn btn-outline-secondary btn-sm">Batal</a>
                                            <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="detail-row">
                                        <div class="detail-label">Nama Lengkap</div>
                                        <div class="detail-value"><strong>{{ $trainer->name }}</strong></div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Email</div>
                                        <div class="detail-value">{{ $trainer->email }}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">No. WhatsApp</div>
                                        <div class="detail-value">{{ $trainer->phone ?? '—' }}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Status</div>
                                        <div class="detail-value">
                                            <span class="badge"
                                                style="background: {{ $statusColor }}; padding: 6px 12px;">
                                                {!! $statusLabel !!}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Profesi</div>
                                        <div class="detail-value">{{ $trainer->profession ?? '—' }}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Institusi</div>
                                        <div class="detail-value">{{ $trainer->institution ?? '—' }}</div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Website</div>
                                        <div class="detail-value">
                                            @if($trainer->website)
                                                <a href="{{ $trainer->website }}" target="_blank">{{ $trainer->website }}</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">LinkedIn</div>
                                        <div class="detail-value">
                                            @if($trainer->linkedin_url)
                                                <a href="{{ $trainer->linkedin_url }}" target="_blank">{{ $trainer->linkedin_url }}</a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Keahlian (Skills)</div>
                                        <div class="detail-value">
                                            @if(!empty($trainer->trainer_skills))
                                                @php $skills = is_array($trainer->trainer_skills) ? $trainer->trainer_skills : explode(',', $trainer->trainer_skills); @endphp
                                                @foreach($skills as $skill)
                                                    <span class="badge bg-light text-dark border me-1 mb-1">{{ trim($skill) }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Pengalaman</div>
                                        <div class="detail-value text-break">
                                            @if(!empty($trainer->trainer_experiences))
                                                {{ is_array($trainer->trainer_experiences) ? implode(', ', $trainer->trainer_experiences) : $trainer->trainer_experiences }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Pendidikan</div>
                                        <div class="detail-value text-break">
                                            @if(!empty($trainer->trainer_educations))
                                                {{ is_array($trainer->trainer_educations) ? implode(', ', $trainer->trainer_educations) : $trainer->trainer_educations }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Sertifikasi</div>
                                        <div class="detail-value text-break">
                                            @if(!empty($trainer->trainer_certifications))
                                                {{ is_array($trainer->trainer_certifications) ? implode(', ', $trainer->trainer_certifications) : $trainer->trainer_certifications }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Role</div>
                                        <div class="detail-value">
                                            <span class="badge" style="background: #3949ab; padding: 6px 12px;">
                                                <i class="bi bi-person-badge me-1"></i>Trainer
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Bio / Skills -->
                            <div class="detail-card">
                                <div class="detail-card-header">
                                    <h5>
                                        <i class="bi bi-file-text-fill" style="color: #3949ab;"></i>
                                        Bio & Keahlian
                                    </h5>
                                    <a href="{{ route('admin.trainer.show', ['trainer' => $trainer->id, 'edit' => 'bio']) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                </div>
                                @if($editBox === 'bio')
                                    <form action="{{ route('admin.trainer.update', $trainer) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="edit_box" value="bio">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Bio</label>
                                            <textarea name="bio" rows="6"
                                                class="form-control">{{ old('bio', $trainer->bio) }}</textarea>
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.trainer.show', $trainer) }}"
                                                class="btn btn-outline-secondary btn-sm">Batal</a>
                                            <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                @elseif($trainer->bio)
                                    <p class="mb-0" style="color: #424242; line-height: 1.7;">
                                        {{ $trainer->bio }}
                                    </p>
                                @else
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-info-circle me-2"></i>Belum ada deskripsi keahlian.
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <!-- Account Information -->
                            <div class="detail-card">
                                <div class="detail-card-header">
                                    <h5>
                                        <i class="bi bi-shield-check" style="color: #3949ab;"></i>
                                        Informasi Akun
                                    </h5>
                                    <a href="{{ route('admin.trainer.show', ['trainer' => $trainer->id, 'edit' => 'account']) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square me-1"></i>Edit
                                    </a>
                                </div>
                                @if($editBox === 'account')
                                    <form action="{{ route('admin.trainer.update', $trainer) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="edit_box" value="account">

                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Avatar Baru</label>
                                            <input type="file" name="avatar" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Password Baru</label>
                                            <input type="password" name="password" class="form-control"
                                                placeholder="Kosongkan jika tidak diubah">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Konfirmasi Password</label>
                                            <input type="password" name="password_confirmation" class="form-control">
                                        </div>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.trainer.show', $trainer) }}"
                                                class="btn btn-outline-secondary btn-sm">Batal</a>
                                            <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="detail-row">
                                        <div class="detail-label">Bergabung Pada</div>
                                        <div class="detail-value">
                                            <strong>{{ $trainer->created_at->format('d F Y') }}</strong>
                                            <small class="text-muted ms-2">
                                                ({{ $trainer->created_at->diffForHumans() }})
                                            </small>
                                        </div>
                                    </div>
                                    <div class="detail-row">
                                        <div class="detail-label">Last Update</div>
                                        <div class="detail-value">
                                            {{ $trainer->updated_at->format('d F Y, H:i') }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Quick Actions -->
                            <div class="detail-card">
                                <h5>
                                    <i class="bi bi-lightning-charge-fill" style="color: #3949ab;"></i>
                                    Aksi Cepat
                                </h5>
                                <div class="d-grid gap-2">
                                    <a href="{{ route('admin.trainer.show', ['trainer' => $trainer->id, 'edit' => 'personal']) }}"
                                        class="btn btn-edit-large text-center text-decoration-none">
                                        <i class="bi bi-pencil-square me-2"></i>Edit Data Trainer
                                    </a>
                                    <a href="{{ route('admin.trainer.index') }}" class="btn btn-outline-secondary btn-action-large">
                                        <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                                    </a>
                                    <form action="{{ route('admin.trainer.destroy', $trainer) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus trainer {{ $trainer->name }}?\n\nData yang terhapus tidak dapat dikembalikan!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-delete-large w-100">
                                            <i class="bi bi-trash-fill me-2"></i>Hapus Trainer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- TAB: KELAS & EVENT -->
                <div class="tab-pane fade" id="classes" role="tabpanel" aria-labelledby="classes-tab">
                    <div class="detail-card">
                        <h5>
                            <i class="bi bi-journal-bookmark-fill" style="color: #3949ab;"></i>
                            Daftar Kelas (Course)
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Kelas</th>
                                        <th>Status</th>
                                        <th>Murid Aktif</th>
                                        <th>Disetujui Pada</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trainerCourses ?? [] as $course)
                                        <tr>
                                            <td class="fw-semibold">{{ $course->name }}</td>
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
                                                <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" onclick="openDeadlineModal({{ $course->id }}, '{{ addslashes($course->name) }}', '{{ $course->material_deadline ? \Carbon\Carbon::parse($course->material_deadline)->format('Y-m-d\TH:i') : '' }}')">
                                                    <i class="bi bi-calendar-check me-1"></i>Atur Deadline
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Belum ada kelas yang diajarkan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="detail-card">
                        <h5>
                            <i class="bi bi-calendar-event-fill" style="color: #e65100;"></i>
                            Daftar Event
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul Event</th>
                                        <th>Jenis</th>
                                        <th>Peserta Hadir</th>
                                        <th>Tanggal Pelaksanaan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trainerEvents ?? [] as $event)
                                        <tr>
                                            <td class="fw-semibold">{{ $event->title }}</td>
                                            <td>
                                                <span class="badge bg-info text-dark">
                                                    {{ $event->jenis ?? 'Lainnya' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-people-fill text-primary me-2"></i>
                                                    {{ $event->registrations_count ?? 0 }} Peserta
                                                </div>
                                            </td>
                                            <td class="small">
                                                @if($event->event_date)
                                                    <i class="bi bi-calendar-check text-success me-1"></i> 
                                                    {{ \Carbon\Carbon::parse($event->event_date)->translatedFormat('d M Y') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Belum ada event yang diajarkan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- TAB: RATING & ULASAN -->
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    
                    <!-- Rating Summary -->
                    <div class="detail-card mb-4" style="background: linear-gradient(135deg, #1f1a77 0%, #261f8c 100%); color: #fff; border: none;">
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center border-end border-light border-opacity-25">
                                <h6 class="text-uppercase mb-2" style="color: #f8d537; font-weight: 700; letter-spacing: 1px;">Rata-rata Rating</h6>
                                <h2 class="display-4 fw-bold mb-0">{{ number_format($averageRating ?? 0, 1) }}</h2>
                                <div class="text-warning mb-2 fs-5">
                                    @for($i=1; $i<=5; $i++)
                                        @if($i <= round($averageRating ?? 0))
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="badge bg-light text-dark px-3 py-2 rounded-pill fw-bold">{{ $ratingBadge ?? 'Cukup' }}</span>
                                <div class="mt-2 small opacity-75">Berdasarkan {{ $totalRatings ?? 0 }} Ulasan</div>
                            </div>
                            <div class="col-md-8 ps-md-5 py-3">
                                <h6 class="mb-3 opacity-75">Distribusi Ulasan</h6>
                                @for($star=5; $star>=1; $star--)
                                    <div class="d-flex align-items-center mb-2" style="font-size: 13px;">
                                        <div style="width: 45px;">{{ $star }} Bintang</div>
                                        <div class="progress flex-grow-1 mx-3" style="height: 8px; background: rgba(255,255,255,0.1);">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $ratingPercentages[$star] ?? 0 }}%"></div>
                                        </div>
                                        <div style="width: 30px; text-align: right;">{{ $ratingPercentages[$star] ?? 0 }}%</div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <h5>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    Ulasan Kelas (Course)
                                </h5>
                                <div class="list-group list-group-flush mt-3">
                                    @forelse($courseReviews ?? [] as $review)
                                        <div class="list-group-item px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <strong class="text-primary">{{ $review->user->name ?? 'User' }}</strong>
                                                <div class="text-warning small">
                                                    @for($i=1; $i<=5; $i++)
                                                        @if($i <= ($review->rating ?? 5))
                                                            <i class="bi bi-star-fill"></i>
                                                        @else
                                                            <i class="bi bi-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="small fw-bold text-muted mb-2">Kelas: {{ $review->course->name ?? 'Course' }}</div>
                                            <p class="mb-1 small">{{ $review->review ?? 'Tidak ada ulasan tertulis.' }}</p>
                                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4">Belum ada ulasan kelas.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="detail-card h-100">
                                <h5>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    Feedback Event
                                </h5>
                                <div class="list-group list-group-flush mt-3">
                                    @forelse($eventFeedback ?? [] as $feedback)
                                        <div class="list-group-item px-0 py-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <strong class="text-primary">{{ $feedback->user->name ?? 'User' }}</strong>
                                                <div class="text-warning small">
                                                    @php $eventRating = $feedback->speaker_rating ?? $feedback->rating ?? 5; @endphp
                                                    @for($i=1; $i<=5; $i++)
                                                        @if($i <= $eventRating)
                                                            <i class="bi bi-star-fill"></i>
                                                        @else
                                                            <i class="bi bi-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            <div class="small fw-bold text-muted mb-2">Event: {{ $feedback->event->title ?? 'Event' }}</div>
                                            <p class="mb-1 small">{{ $feedback->speaker_feedback ?? $feedback->feedback ?? 'Tidak ada ulasan tertulis.' }}</p>
                                            <small class="text-muted">{{ $feedback->created_at->diffForHumans() }}</small>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-4">Belum ada feedback event.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- TAB: KEUANGAN -->
                <div class="tab-pane fade" id="finance" role="tabpanel" aria-labelledby="finance-tab">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="stat-box" style="background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%); border-color: #a5d6a7;">
                                <div class="stat-icon" style="color: #2e7d32;">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <div class="stat-number" style="color: #1b5e20;">Rp {{ number_format($walletBalance ?? 0, 0, ',', '.') }}</div>
                                <div class="stat-label">Saldo Aktif Tersedia</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-box" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); border-color: #90caf9;">
                                <div class="stat-icon" style="color: #1565c0;">
                                    <i class="bi bi-cash-stack"></i>
                                </div>
                                <div class="stat-number" style="color: #0d47a1;">Rp {{ number_format($totalPaidOut ?? 0, 0, ',', '.') }}</div>
                                <div class="stat-label">Total Pencairan Berhasil</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-card">
                        <h5>
                            <i class="bi bi-clock-history" style="color: #3949ab;"></i>
                            Riwayat Penarikan Dana (Payouts)
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Tanggal Diproses</th>
                                        <th>Nominal</th>
                                        <th>Metode Transfer</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trainerPayouts ?? [] as $payout)
                                        <tr>
                                            <td class="small">{{ $payout->created_at->format('d M Y H:i') }}</td>
                                            <td class="small">{{ $payout->payment_date ? \Carbon\Carbon::parse($payout->payment_date)->format('d M Y') : '-' }}</td>
                                            <td class="fw-bold text-success">Rp {{ number_format($payout->amount, 0, ',', '.') }}</td>
                                            <td>
                                                @if($payout->bank_name)
                                                    <div>{{ $payout->bank_name }}</div>
                                                    <div class="small text-muted">{{ $payout->account_number }} a.n. {{ $payout->account_name }}</div>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $bgClass = match($payout->status) {
                                                        'pending' => 'bg-warning text-dark',
                                                        'approved' => 'bg-success',
                                                        'rejected' => 'bg-danger',
                                                        default => 'bg-secondary'
                                                    };
                                                @endphp
                                                <span class="badge {{ $bgClass }}">{{ strtoupper($payout->status) }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat penarikan dana.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- TAB: SERTIFIKAT -->
                <div class="tab-pane fade" id="certificates" role="tabpanel" aria-labelledby="certificates-tab">
                    <div class="detail-card">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0 border-0 pb-0">
                                <i class="bi bi-award-fill" style="color: #3949ab;"></i>
                                Sertifikat Trainer (Diterbitkan Admin)
                            </h5>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>No Sertifikat</th>
                                        <th>Konteks</th>
                                        <th>Status</th>
                                        <th>Terbit</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($trainerCertificates ?? collect()) as $cert)
                                        <tr>
                                            <td
                                                style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">
                                                {{ $cert->certificate_number }}
                                            </td>
                                            <td>
                                                @php
                                                    $label = $cert->certifiable instanceof \App\Models\Event
                                                        ? ('Event: ' . ($cert->certifiable->title ?? '#' . $cert->certifiable_id))
                                                        : ('Course: ' . ($cert->certifiable->name ?? '#' . $cert->certifiable_id));
                                                @endphp
                                                <div class="small fw-semibold">{{ $label }}</div>
                                                <div class="text-muted small">Diterbitkan oleh: {{ $cert->issuer->name ?? '-' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge {{ ($cert->status ?? '') === 'revoked' ? 'bg-danger' : 'bg-success' }}">
                                                    {{ strtoupper($cert->status ?? 'sent') }}
                                                </span>
                                            </td>
                                            <td class="small text-muted">{{ $cert->issued_at?->format('d M Y') ?? '-' }}</td>
                                            <td class="text-end">
                                                @if(($cert->status ?? '') !== 'revoked')
                                                    <span class="text-muted small">Cabut (Belum Tersedia)</span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">Belum ada sertifikat yang
                                                diterbitkan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div> <!-- End Tab Content -->
        </main>
    </div>

    <!-- Modal Atur Deadline Course -->
    <div class="modal fade" id="deadlineModal" tabindex="-1" aria-labelledby="deadlineModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <form id="deadlineForm" method="POST" action="">
                    @csrf
                    <div class="modal-header border-0 bg-light px-4 py-3">
                        <h5 class="modal-title fw-bold" id="deadlineModalLabel">
                            <i class="bi bi-calendar-event me-2 text-primary"></i> Atur Batas Waktu Materi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-4">
                        <p class="text-muted small mb-4">Ubah batas waktu penyusunan materi untuk kelas <strong id="deadlineCourseName" class="text-dark"></strong>.</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark">Batas Waktu (Tgl & Jam) <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control form-control-lg bg-light" name="material_deadline" id="material_deadline" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light px-4 py-3">
                        <button type="button" class="btn btn-light fw-medium px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4 shadow-sm" style="border-radius: 8px;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('admin-trainer-scripts')
    <script>
        function openDeadlineModal(courseId, courseName, currentDeadline) {
            // Update UI modal
            document.getElementById('deadlineCourseName').textContent = courseName;
            
            // Set form action dinamis
            const form = document.getElementById('deadlineForm');
            // Route POST /admin/trainer/{trainer}/course/{course}/deadline
            form.action = `/admin/trainer/{{ $trainer->id }}/course/${courseId}/deadline`;

            // Set nilai input jika ada
            if(currentDeadline) {
                document.getElementById('material_deadline').value = currentDeadline;
            } else {
                document.getElementById('material_deadline').value = '';
            }
            
            // Tampilkan modal
            var modal = new bootstrap.Modal(document.getElementById('deadlineModal'));
            modal.show();
        }
    </script>
    @endpush
@endsection