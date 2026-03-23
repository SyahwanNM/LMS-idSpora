@extends('layouts.trainer')

@section('title', 'Profile - Trainer')

@php
    $pageTitle = 'Profile';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Profile']
    ];

    $displayRole = $trainer->profession ?: 'Trainer';
    $displayLocation = $trainer->institution ?: 'Location not set';
    $displayBio = $trainer->bio ?: 'Profil belum dilengkapi. Tambahkan bio agar peserta mengenal Anda lebih baik.';
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <style>
        .profile-wrap {
            display: grid;
            gap: 16px;
        }

        .top-content {
            margin: 0 0 var(--spacing-xl) 0;
            position: relative;
            border-radius: var(--radius-2xl);
            padding: var(--spacing-xl) var(--spacing-2xl);
            background-color: var(--main-navy-clr);
            box-shadow: 0 10px 32px rgba(11, 9, 38, 0.75);
            color: var(--white-clr);
            overflow: hidden;
        }

        .top-content::before {
            content: "";
            position: absolute;
            inset: 0;
            background: url("https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1600") center/cover no-repeat;
            opacity: 0.75;
        }

        .top-content::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg,
                    rgba(20, 18, 68, 0.98),
                    rgba(27, 23, 99, 0.85),
                    rgba(27, 23, 99, 0.55));
        }

        .top-content-inner {
            position: relative;
            z-index: 1;
            display: grid;
            gap: var(--spacing-md);
        }

        .top-main-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: var(--spacing-lg);
        }

        .profile-left {
            display: flex;
            align-items: center;
            gap: var(--spacing-lg);
            min-width: 0;
            flex: 1;
        }

        .profile-photo {
            position: relative;
            flex-shrink: 0;
        }

        .profile-photo img {
            width: 110px;
            height: 110px;
            border-radius: var(--radius-lg);
            border: 3px solid var(--white-clr);
            object-fit: cover;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
        }

        .photo-badge {
            position: absolute;
            right: -6px;
            bottom: -6px;
            width: 24px;
            height: 24px;
            background: var(--yellow-clr);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
            border: none;
            padding: 0;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .photo-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.35);
        }

        .level-badge {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            background: var(--yellow-background-clr);
            color: var(--yellow-clr);
            border: 1px solid rgba(251, 197, 49, 0.45);
            padding: var(--spacing-xs) var(--spacing-md);
            border-radius: 16px;
            font-size: var(--font-size-xs);
            font-weight: 700;
            letter-spacing: 0.4px;
            margin-bottom: var(--spacing-sm);
        }

        .profile-text h2 {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: 24px;
            font-weight: 700;
            line-height: var(--line-height-tight);
            color: var(--white-clr);
        }

        .profile-text .role {
            margin: 0 0 var(--spacing-sm) 0;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.8);
        }

        .profile-text {
            min-width: 0;
            width: 100%;
        }

        .info {
            display: flex;
            flex-direction: row;
            gap: var(--spacing-lg);
            margin-top: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .loc-mail {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: var(--spacing-xs);
            font-size: 13px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.75);
        }

        .loc-mail svg {
            color: var(--yellow-clr);
        }

        .btn-configure {
            background: var(--white-clr);
            color: var(--main-navy-clr);
            padding: var(--spacing-md) var(--spacing-xl);
            border-radius: var(--radius-lg);
            font-weight: 700;
            font-size: var(--font-size-sm);
            letter-spacing: 0.5px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }

        .btn-configure:hover {
            transform: translateY(-1px);
        }

        .btn-share {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white-clr);
            text-decoration: none;
            cursor: pointer;
        }

        .btn-share:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .top-edit-form {
            display: none;
            margin-top: 4px;
            width: 100%;
        }

        .top-edit-form.active {
            display: grid;
            gap: 10px;
        }

        .top-edit-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .top-edit-field {
            display: grid;
            gap: 5px;
        }

        .top-edit-field label {
            font-size: 11px;
            letter-spacing: .04em;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            font-weight: 600;
        }

        .top-edit-field input {
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 13px;
        }

        .top-edit-field input[readonly] {
            opacity: 0.85;
            cursor: not-allowed;
        }

        .top-edit-field input::placeholder {
            color: rgba(255, 255, 255, 0.65);
        }

        .top-edit-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 2px;
        }

        .top-edit-save,
        .top-edit-cancel {
            border: none;
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .top-edit-save {
            background: #ffffff;
            color: #1b1763;
        }

        .top-edit-cancel {
            background: rgba(255, 255, 255, 0.16);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.28);
        }

        @media (max-width: 768px) {
            .top-main-row {
                flex-direction: column;
                align-items: stretch;
            }

            .profile-actions {
                justify-content: flex-start;
            }

            .top-edit-grid {
                grid-template-columns: 1fr;
            }
        }

        .profile-hero {
            background: linear-gradient(135deg, #1b1763 0%, #27227e 70%, #332fa0 100%);
            color: #fff;
            border-radius: 18px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .profile-hero-main {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .profile-wrap .profile-avatar {
            width: 72px;
            height: 72px;
            border-radius: 14px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, .45);
            background: #fff;
        }

        .profile-hero h1 {
            font-size: 22px;
            margin: 0;
            line-height: 1.2;
        }

        .profile-hero p {
            margin: 4px 0 0;
            color: rgba(255, 255, 255, .82);
        }

        .profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 8px;
            font-size: 12px;
            color: rgba(255, 255, 255, .82);
        }

        .profile-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .profile-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .profile-btn {
            border: 1px solid rgba(255, 255, 255, .35);
            background: rgba(255, 255, 255, .14);
            color: #fff;
            border-radius: 10px;
            padding: 8px 12px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 16px;
        }

        @media (max-width: 992px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }

        .card-box {
            background: #fff;
            border: 1px solid #e8ecf3;
            border-radius: 14px;
            padding: 14px;
        }

        .card-title {
            margin: 0 0 10px;
            font-size: 11px;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }

        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stats-item {
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 10px;
            padding: 10px;
        }

        .stats-item p {
            margin: 0;
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .stats-item h4 {
            margin: 6px 0 0;
            color: #0f172a;
            font-size: 18px;
        }

        .pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .pill {
            background: #1b1763;
            color: #fff;
            font-size: 11px;
            border-radius: 999px;
            padding: 4px 10px;
            font-weight: 600;
            letter-spacing: .03em;
        }

        .reward-box {
            background: #1b1763;
            color: #fff;
            border-radius: 14px;
            padding: 14px;
            display: grid;
            gap: 8px;
        }

        .reward-box p {
            margin: 0;
            color: rgba(255, 255, 255, .75);
            font-size: 11px;
            letter-spacing: .05em;
            text-transform: uppercase;
            font-weight: 600;
        }

        .reward-box h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }

        .reward-box button {
            border: 1px solid rgba(255, 255, 255, .25);
            background: rgba(255, 255, 255, .12);
            color: #fff;
            border-radius: 10px;
            padding: 8px 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
        }

        .list-stack {
            display: grid;
            gap: 10px;
        }

        .item-box {
            border: 1px solid #edf1f7;
            background: #f8fafc;
            border-radius: 10px;
            padding: 10px;
        }

        .item-box h5 {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
        }

        .item-box p {
            margin: 6px 0 0;
            color: #64748b;
            font-size: 12px;
        }

        .content-section {
            display: grid;
            gap: 14px;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 10px;
        }

        .course-card {
            border: 1px solid #e8ecf3;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            text-decoration: none;
            color: inherit;
        }

        .course-card img {
            width: 100%;
            height: 128px;
            object-fit: cover;
            background: #eef2ff;
        }

        .course-card-body {
            padding: 10px;
            display: grid;
            gap: 8px;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 11px;
            font-weight: 600;
        }

        .course-title {
            margin: 0;
            color: #0f172a;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.35;
        }

        .feedback-item {
            border: 1px solid #e8ecf3;
            border-radius: 10px;
            padding: 10px;
            background: #fff;
            display: grid;
            gap: 6px;
        }

        .feedback-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: #64748b;
        }

        .stars {
            color: #f59e0b;
            letter-spacing: 1px;
            font-size: 12px;
        }

        .feedback-author {
            font-size: 12px;
            color: #0f172a;
            font-weight: 600;
        }

        .profile-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2000;
        }

        .profile-modal.active {
            display: block;
        }

        .profile-modal-overlay {
            position: absolute;
            inset: 0;
            background: rgba(2, 6, 23, .55);
        }

        .profile-modal-content {
            position: relative;
            z-index: 1;
            width: min(620px, 94vw);
            margin: 8vh auto;
            background: #fff;
            border-radius: 14px;
            padding: 14px;
            max-height: 84vh;
            overflow: auto;
        }

        .ledger-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border: 1px solid #e8ecf3;
            border-radius: 10px;
            padding: 10px;
            background: #f8fafc;
            margin-bottom: 8px;
        }

        .ledger-row h6 {
            margin: 0;
            font-size: 14px;
            color: #0f172a;
        }

        .ledger-row p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #64748b;
        }

        .ledger-amount {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="profile-wrap">
        @if(session('success'))
            <div
                style="background:#ecfdf5;border:1px solid #86efac;color:#166534;padding:10px 12px;border-radius:10px;font-size:13px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div
                style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 12px;border-radius:10px;font-size:13px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="top-content">
            <div class="top-content-inner">
                <div class="top-main-row">
                    <div class="profile-left">
                        <div class="profile-photo">
                            <img src="{{ $trainer->avatar_url }}" alt="{{ $trainer->name }}" />
                            <button type="button" id="profilePhotoBadge" class="photo-badge" title="Ganti Foto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                    fill="none" stroke="#1b1763" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z">
                                    </path>
                                    <circle cx="12" cy="13" r="4"></circle>
                                </svg>
                            </button>
                            <input type="file" id="avatarFileInput" name="avatar_file" accept="image/*"
                                style="display: none;" />
                        </div>

                        <div class="profile-text">
                            <div class="level-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                    fill="currentColor">
                                    <path
                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                                MASTER LEVEL ACADEMIC
                            </div>
                            <h2>{{ $trainer->name }}</h2>
                            <p class="role">{{ $displayRole }}</p>
                            <div class="info">
                                <div class="loc-mail">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                                    </svg>
                                    <span>{{ strtoupper($displayLocation) }}</span>
                                </div>
                                <div class="loc-mail">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        viewBox="0 0 16 16">
                                        <path
                                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                                    </svg>
                                    <span>{{ strtoupper($trainer->email) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="button" id="topEditToggleBtn" class="btn-configure">CONFIGURE PROFILE</button>
                        <button type="button" class="btn-share" aria-label="Share">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form id="topInlineEditForm" class="top-edit-form {{ $errors->any() ? 'active' : '' }}"
                    action="{{ route('trainer.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="top-edit-grid">
                        <div class="top-edit-field">
                            <label for="top_name">Nama</label>
                            <input id="top_name" type="text" name="name" value="{{ old('name', $trainer->name) }}" required>
                        </div>
                        <div class="top-edit-field">
                            <label for="top_profession">Profesi</label>
                            <input id="top_profession" type="text" name="profession"
                                value="{{ old('profession', $trainer->profession) }}">
                        </div>
                        <div class="top-edit-field">
                            <label for="top_institution">Institusi / Lokasi</label>
                            <input id="top_institution" type="text" name="institution"
                                value="{{ old('institution', $trainer->institution) }}">
                        </div>
                        <div class="top-edit-field">
                            <label for="top_phone">Telepon</label>
                            <input id="top_phone" type="text" name="phone" value="{{ old('phone', $trainer->phone) }}">
                        </div>
                        <div class="top-edit-field">
                            <label>Email</label>
                            <input type="text" value="{{ $trainer->email }}" readonly>
                        </div>
                    </div>
                    <div class="top-edit-actions">
                        <button type="button" id="topEditCancelBtn" class="top-edit-cancel">BATAL</button>
                        <button type="submit" class="top-edit-save">SIMPAN</button>
                    </div>
                </form>
            </div>
        </section>

        <div class="profile-grid">
            <aside class="list-stack">
                <div class="card-box">
                    <h3 class="card-title">Trainer Insights</h3>
                    <div class="stats-row">
                        <div class="stats-item">
                            <p>Global Learners</p>
                            <h4>{{ number_format($totalStudents) }}</h4>
                        </div>
                        <div class="stats-item">
                            <p>Quality Score</p>
                            <h4>{{ number_format($averageRating, 1) }} <i class="bi bi-star-fill" style="color:#f59e0b"></i>
                            </h4>
                        </div>
                    </div>
                    <div style="margin-top:10px;padding-top:10px;border-top:1px solid #e8ecf3;display:flex;justify-content:space-between;font-size:12px;color:#64748b;">
                        <div style="text-align:center;flex:1;">
                            <p style="margin:0 0 4px;font-weight:700;color:#0f172a;">{{ number_format($totalCourses) }}</p>
                            <span>Courses</span>
                        </div>
                        <div style="text-align:center;flex:1;">
                            <p style="margin:0 0 4px;font-weight:700;color:#0f172a;">{{ number_format($totalFeedbacks) }}</p>
                            <span>Reviews</span>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <h3 class="card-title">Professional Info</h3>
                    <div style="display:grid;gap:10px;font-size:12px;">
                        @if(!empty($trainer->profession))
                            <div style="display:flex;gap:8px;align-items:flex-start;">
                                <i class="bi bi-briefcase-fill" style="color:#1b1763;flex-shrink:0;margin-top:2px;"></i>
                                <div>
                                    <p style="margin:0;font-weight:700;color:#0f172a;">{{ $trainer->profession }}</p>
                                    <p style="margin:2px 0 0;color:#64748b;">Role</p>
                                </div>
                            </div>
                        @endif
                        @if(!empty($trainer->institution))
                            <div style="display:flex;gap:8px;align-items:flex-start;">
                                <i class="bi bi-building" style="color:#1b1763;flex-shrink:0;margin-top:2px;"></i>
                                <div>
                                    <p style="margin:0;font-weight:700;color:#0f172a;">{{ $trainer->institution }}</p>
                                    <p style="margin:2px 0 0;color:#64748b;">Institution</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-box">
                    <h3 class="card-title">Area of Expertise</h3>
                    <div class="pill-list">
                        @foreach($expertiseTags as $tag)
                            <span class="pill">{{ strtoupper($tag) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="reward-box">
                    <p>Gross Earnings</p>
                    <h3>Rp {{ number_format($totalEarned, 0, ',', '.') }}</h3>
                    <button type="button" id="openLedgerBtn"><i class="bi bi-window-stack"></i> VIEW PAYMENT
                        RECORDS</button>
                </div>

                <div class="card-box">
                    <h3 class="card-title">Upcoming Schedule</h3>
                    <div class="list-stack">
                        @forelse($upcomingEvents as $event)
                            <a href="{{ route('trainer.events.show', $event->id) }}" class="item-box"
                                style="text-decoration:none;color:inherit;">
                                <h5>{{ $event->title }}</h5>
                                <p>{{ optional($event->event_date)->format('d M Y') }} • {{ $event->participants_count }}
                                    peserta</p>
                            </a>
                        @empty
                            <div class="item-box">
                                <h5>Belum ada jadwal terdekat</h5>
                                <p>Event yang akan datang akan tampil di sini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </aside>

            <div class="content-section">
                <div class="card-box">
                    <h3 class="card-title">Pedagogical Statement</h3>
                    <p style="margin:0;color:#334155;line-height:1.6;">{{ $displayBio }}</p>
                </div>

                <div style="background:linear-gradient(135deg, #f0f4ff 0%, #fbf8ff 100%);border-left:4px solid #1b1763;border-radius:10px;padding:14px;display:grid;gap:8px;">
                    <h4 style="margin:0;font-size:13px;font-weight:700;color:#1b1763;">Trainer Impact</h4>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:12px;">
                        <div>
                            <p style="margin:0;color:#64748b;font-weight:600;">Student Base</p>
                            <p style="margin:4px 0 0;color:#0f172a;font-size:16px;font-weight:700;">{{ number_format($totalStudents) }} Active</p>
                        </div>
                        <div>
                            <p style="margin:0;color:#64748b;font-weight:600;">Teaching Portfolio</p>
                            <p style="margin:4px 0 0;color:#0f172a;font-size:16px;font-weight:700;">{{ number_format($totalCourses) }} Courses</p>
                        </div>
                        <div>
                            <p style="margin:0;color:#64748b;font-weight:600;">Community Feedback</p>
                            <p style="margin:4px 0 0;color:#0f172a;font-size:16px;font-weight:700;">{{ number_format($totalFeedbacks) }} Reviews</p>
                        </div>
                        <div>
                            <p style="margin:0;color:#64748b;font-weight:600;">Quality Rating</p>
                            <p style="margin:4px 0 0;color:#0f172a;font-size:16px;font-weight:700;">{{ number_format($averageRating, 1) }} ⭐</p>
                        </div>
                    </div>
                </div>

                <div class="card-box">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:10px;">
                        <h3 class="card-title" style="margin:0;">Featured Courses</h3>
                        <a href="{{ route('trainer.courses') }}"
                            style="font-size:12px;font-weight:700;text-decoration:none;color:#1b1763;">SEE ALL ({{ $totalCourses }})</a>
                    </div>

                    <div class="course-grid">
                        @forelse($topCourses as $course)
                            @php
                                $thumbnail = $course->card_thumbnail;
                                $thumbnailUrl = null;
                                if (!empty($thumbnail)) {
                                    $thumbnailUrl = \Illuminate\Support\Str::startsWith($thumbnail, ['http://', 'https://'])
                                        ? $thumbnail
                                        : asset('storage/' . ltrim($thumbnail, '/'));
                                }
                                $displayCourseImage = $thumbnailUrl ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=900';
                                $rating = number_format((float) ($course->reviews_avg_rating ?? 0), 1);
                              @endphp
                            <a href="{{ route('trainer.detail-course', $course->id) }}" class="course-card" style="position:relative;">
                                @if((float)$rating >= 4.5)
                                    <div style="position:absolute;top:8px;right:8px;background:#fbbf24;color:#78350f;padding:4px 8px;border-radius:6px;font-size:10px;font-weight:700;z-index:10;display:flex;align-items:center;gap:3px;">
                                        <i class="bi bi-star-fill"></i> TOP
                                    </div>
                                @endif
                                <img src="{{ $displayCourseImage }}" alt="{{ $course->name }}">
                                <div class="course-card-body">
                                    <div class="course-meta">
                                        <span><i class="bi bi-star-fill" style="color:#f59e0b"></i> {{ $rating }}</span>
                                        <span>{{ number_format($course->active_enrollments_count) }} LEARNERS</span>
                                    </div>
                                    <h4 class="course-title">{{ $course->name }}</h4>
                                    <div class="course-meta">
                                        <span>{{ strtoupper($course->level ?? 'GENERAL') }}</span>
                                        <span>{{ $course->modules_count }} MODULES</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="item-box" style="grid-column:1/-1;">
                                <h5>Belum ada course</h5>
                                <p>Course yang Anda ampu akan muncul di sini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card-box">
                    <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:10px;">
                        <h3 class="card-title" style="margin:0;">Recent Student Feedback</h3>
                        <div style="font-size:12px;font-weight:700;color:#1b1763;">
                            {{ number_format($averageRating, 1) }} <i class="bi bi-star-fill" style="color:#f59e0b"></i>
                        </div>
                    </div>

                    <div class="list-stack">
                        @forelse($recentFeedbacks as $feedback)
                            @php
                                $rating = max(1, min(5, (int) $feedback->rating));
                                $authorName = optional($feedback->user)->name ?: 'Anonymous';
                              @endphp
                            <div class="feedback-item">
                                <div class="feedback-head">
                                    <span class="stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span>
                                    <span>{{ optional($feedback->created_at)->diffForHumans() }}</span>
                                </div>
                                <div style="font-size:13px;color:#334155;line-height:1.55;">
                                    {{ $feedback->comment ?: 'Tidak ada komentar.' }}
                                </div>
                                <div class="feedback-author">{{ strtoupper($authorName) }}</div>
                            </div>
                        @empty
                            <div class="item-box">
                                <h5>Belum ada feedback</h5>
                                <p>Feedback peserta untuk course Anda akan tampil di sini.</p>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('trainer.feedback') }}"
                        style="display:inline-flex;gap:6px;align-items:center;margin-top:10px;text-decoration:none;font-size:12px;font-weight:700;color:#1b1763;">
                        VIEW ALL REVIEWS <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="ledgerModal" class="profile-modal">
        <div class="profile-modal-overlay" id="ledgerModalOverlay"></div>
        <div class="profile-modal-content">
            <h3 style="margin:0 0 12px;color:#0f172a;">Financial Ledger</h3>

            @forelse($ledgerPayments as $payment)
                <div class="ledger-row">
                    <div>
                        <h6>{{ optional($payment->course)->name ?: optional($payment->event)->title ?: 'Pembayaran' }}</h6>
                        <p>{{ optional($payment->created_at)->format('d M Y H:i') }} • {{ strtoupper($payment->method ?? '-') }}
                        </p>
                    </div>
                    <div class="ledger-amount">+ Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                </div>
            @empty
                <div class="item-box">
                    <h5>Belum ada transaksi</h5>
                    <p>Data pembayaran settled akan tampil di sini.</p>
                </div>
            @endforelse

            <button type="button" id="closeLedgerBtn"
                style="width:100%;margin-top:10px;border:none;background:#1b1763;color:#fff;border-radius:10px;padding:10px 12px;font-size:12px;font-weight:700;">CLOSE
                RECORDS</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const topEditToggleBtn = document.getElementById('topEditToggleBtn');
            const topInlineEditForm = document.getElementById('topInlineEditForm');
            const topEditCancelBtn = document.getElementById('topEditCancelBtn');

            const syncTopEditButton = () => {
                if (!topEditToggleBtn || !topInlineEditForm) return;
                const isActive = topInlineEditForm.classList.contains('active');
                topEditToggleBtn.textContent = isActive ? 'TUTUP CONFIGURE' : 'CONFIGURE PROFILE';
            };

            if (topEditToggleBtn && topInlineEditForm) {
                topEditToggleBtn.addEventListener('click', function () {
                    topInlineEditForm.classList.toggle('active');
                    syncTopEditButton();
                });
            }

            if (topEditCancelBtn && topInlineEditForm) {
                topEditCancelBtn.addEventListener('click', function () {
                    topInlineEditForm.classList.remove('active');
                    syncTopEditButton();
                });
            }

            syncTopEditButton();

            // Profile photo upload handler
            const profilePhotoBadge = document.getElementById('profilePhotoBadge');
            const avatarFileInput = document.getElementById('avatarFileInput');

            if (profilePhotoBadge && avatarFileInput) {
                profilePhotoBadge.addEventListener('click', function (e) {
                    e.preventDefault();
                    avatarFileInput.click();
                });

                avatarFileInput.addEventListener('change', function (e) {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('input[name="_token"]')?.value || '');
                        formData.append('_method', 'PUT');
                        formData.append('avatar', file);

                        fetch('{{ route("trainer.profile.update") }}', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update image src
                                    const profileImg = document.querySelector('.profile-photo img');
                                    if (profileImg && data.avatar_url) {
                                        profileImg.src = data.avatar_url + '?' + new Date().getTime();
                                    }
                                    // Reset file input
                                    avatarFileInput.value = '';
                                }
                            })
                            .catch(err => console.error('Upload error:', err));
                    }
                });
            }

            const modal = document.getElementById('ledgerModal');
            const openBtn = document.getElementById('openLedgerBtn');
            const closeBtn = document.getElementById('closeLedgerBtn');
            const overlay = document.getElementById('ledgerModalOverlay');

            if (!modal || !openBtn || !closeBtn || !overlay) {
                return;
            }

            const openModal = () => {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            const closeModal = () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            };

            openBtn.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', closeModal);
        });
    </script>
@endpush