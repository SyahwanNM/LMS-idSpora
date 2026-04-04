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
    $displayFullName = $trainer->full_name_with_title ?: $trainer->name;
    $displayLinkedIn = $trainer->linkedin_url ?: null;
    $displayBankName = $trainer->bank_name ?: 'Belum diisi';
    $displayBankAccountNumber = $trainer->bank_account_number ?: 'Belum diisi';
    $displayBankAccountHolder = $trainer->bank_account_holder ?: 'Belum diisi';

    $headline = $trainer->profession
        ? ($trainer->institution ? $trainer->profession . ' at ' . $trainer->institution : $trainer->profession)
        : 'Praktisi & Trainer Profesional';
    $isVerifiedTrainer = !empty($trainer->email_verified_at);

    $activeStatus = ['active', 'published', 'ongoing'];
    $activeCoursesCollection = collect($courses)->filter(function ($courseItem) use ($activeStatus) {
        return in_array(strtolower((string) ($courseItem->status ?? '')), $activeStatus, true);
    })->values();

    $archivedCoursesCollection = collect($courses)->reject(function ($courseItem) use ($activeStatus) {
        return in_array(strtolower((string) ($courseItem->status ?? '')), $activeStatus, true);
    })->values();

    if ($activeCoursesCollection->isEmpty()) {
        $activeCoursesCollection = collect($topCourses)->values();
    }

    $activeCourses = $activeCoursesCollection->take(3);
    $archivedCourses = $archivedCoursesCollection->take(3);
    $selectedTestimonials = collect($recentFeedbacks)->take(3);
    $completedEventsCount = (int) ($completedEventsCount ?? 0);
    $completedCoursesCount = (int) ($completedCoursesCount ?? 0);
    $totalCertificates = (int) ($totalCertificates ?? 0);
    $trainerCertificates = collect($trainerCertificates ?? [])->take(3);
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <style>
        .profile-wrap {
            display: grid;
            gap: 20px;
        }

        .profile-content-stack {
            display: grid;
            gap: 14px;
            margin-top: 4px;
        }

        .profile-content-stack {
            --content-text: #334155;
            --content-heading: #0f172a;
            --content-muted: #64748b;
            --content-border: #e8edf5;
            --content-bg-soft: #f8fafc;
            --content-icon: var(--yellow-clr);
        }

        .profile-content-stack i.bi {
            color: var(--content-icon);
            font-size: 16px;
            line-height: 1;
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

        .section-label {
            margin: 2px 0 0;
            font-size: 15px;
            color: #0f172a;
            font-weight: 700;
        }

        .section-subtle {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 12px;
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
            width: 28px;
            height: 28px;
            background: var(--yellow-clr);
            border-radius: 8px;
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

        .profile-text .headline {
            margin: 0;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.92);
            font-weight: 700;
            letter-spacing: 0.32px;
        }

        .hero-bio {
            margin: 10px 0 0;
            color: rgba(255, 255, 255, 0.88);
            font-size: 13px;
            line-height: 1.6;
            max-width: 800px;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(14, 165, 233, 0.18);
            color: #dbeafe;
            border: 1px solid rgba(125, 211, 252, 0.45);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.48px;
            margin-top: 8px;
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

        .loc-mail i {
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
            gap: 6px;
        }

        .btn-configure:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .btn-share {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white-clr);
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-share:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
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
            letter-spacing: 0.64px;
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
            letter-spacing: 0.64px;
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

        .profile-dashboard {
            display: grid;
            grid-template-columns: 305px minmax(0, 1fr);
            gap: 16px;
            align-items: start;
        }

        .dashboard-sidebar,
        .dashboard-content {
            display: grid;
            gap: 16px;
        }

        @media (max-width: 992px) {
            .profile-dashboard {
                grid-template-columns: 1fr;
            }
        }

        .profile-info-card {
            background: #fff;
            border: 1px solid var(--content-border);
            border-radius: 16px;
            padding: 18px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            display: grid;
            gap: 14px;
        }

        .info-divider {
            border-top: 1px solid var(--content-border);
            margin: 0 -2px;
        }

        .network-icons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .network-icon {
            min-height: 40px;
            border-radius: 12px;
            padding: 0 14px;
            background: #f8fafc;
            border: 1px solid #eff2f7;
            color: #1b1763;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.64px;
            transition: all 0.2s ease;
        }

        .network-icon:hover {
            background: #f3f1f9;
            border-color: #e8ecf3;
        }

        .reward-card,
        .schedule-card,
        .pedagogical-statement,
        .student-feedback {
            background: #fff;
            border: 1px solid var(--content-border);
            border-radius: 16px;
            padding: 14px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }

        .list-stack {
            display: grid;
            gap: 10px;
        }

        .card-box {
            background: var(--content-bg-soft);
            border: 1px solid var(--content-border);
            border-radius: 16px;
            padding: 12px;
            box-shadow: none;
        }

        .profile-overview .card-box {
            background: transparent;
            border-radius: 12px;
        }

        .card-title {
            margin: 0 0 10px;
            font-size: 9px;
            letter-spacing: 0.32px;
            color: var(--content-muted);
            font-weight: 500;
            text-transform: none;
        }

        .section-title {
            margin: 0;
            font-size: 12px;
            letter-spacing: 0;
            text-transform: uppercase;
            color: var(--content-heading);
            font-weight: 600;
            line-height: 1;
        }

        .statement-header,
        .portfolio-header,
        .feedback-header,
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
        }

        .statement-title,
        .portfolio-title,
        .feedback-title {
            margin: 0;
            font-size: 12px;
            color: var(--content-heading);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 0;
            text-transform: none;
            line-height: 1;
            align-self: center;
        }

        .statement-title i,
        .portfolio-title i,
        .feedback-title i,
        .experience-header i {
            font-size: 12px;
            width: 12px;
            height: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 12px;
            line-height: 1;
            transform: translateY(-0.5px);
        }

        .view-all,
        .view-all-reviews,
        .schedule-manage-link {
            color: #ffffff;
            background: #1b1763;
            border: 1px solid #1b1763;
            border-radius: 10px;
            padding: 8px 12px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s ease;
        }

        .view-all:hover,
        .view-all-reviews:hover,
        .schedule-manage-link:hover {
            background: #26206f;
            border-color: #26206f;
            opacity: 1;
        }

        .statement-text {
            margin: 0;
            color: var(--content-text);
            line-height: 1.6;
            font-size: 13px;
        }

        .feedback-list,
        .schedule-list {
            display: grid;
            gap: 12px;
        }

        .feedback-time {
            color: var(--content-muted);
            font-size: 10px;
            font-weight: 500;
            margin-left: auto;
            letter-spacing: 0.3px;
        }

        .feedback-stars {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        .author-avatar {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            background: #fff7e5;
            color: #9a6700;
            border: 1px solid #f6d48a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }

        .feedback-author {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .author-name {
            font-size: 11px;
            color: var(--content-text);
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stats-item {
            background: transparent;
            border: none;
            border-radius: 0;
            padding: 0;
        }

        .stats-item p {
            margin: 0;
            font-size: 9px;
            color: var(--content-muted);
            font-weight: 500;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .stats-item h4 {
            margin: 4px 0 0;
            color: var(--content-heading);
            font-size: 14px;
            font-weight: 600;
        }

        .pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .pill {
            background: #fff8e1;
            color: #9a6700;
            font-size: 9px;
            border-radius: 12px;
            padding: 6px 10px;
            font-weight: 600;
            letter-spacing: 0.32px;
            border: 1px solid #f6d48a;
        }

        .network-icons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .network-icon {
            min-height: 38px;
            border-radius: 12px;
            padding: 0 10px;
            gap: 6px;
            font-size: 10px;
            justify-content: flex-start;
            border: 1px solid var(--content-border);
        }

        .network-icon i {
            font-size: 16px;
        }

        .network-icon.linkedin {
            background: #fff7e5;
            color: #9a6700;
        }

        .network-icon.website {
            background: #fff7e5;
            color: #9a6700;
        }

        .reward-box {
            background: #ffffff;
            color: var(--content-heading);
            border-radius: 16px;
            padding: 14px;
            display: grid;
            gap: 10px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--content-border);
        }

        .reward-box p {
            margin: 0;
            color: var(--content-muted);
            font-size: 11px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .reward-box h3 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            line-height: 1;
            letter-spacing: -0.16px;
            color: var(--content-heading);
        }

        .reward-box .decimals {
            color: var(--content-muted);
        }

        .reward-box .reward-icon {
            position: absolute;
            right: 14px;
            top: 14px;
            font-size: 22px;
            color: #fde8b2;
        }

        .reward-box button {
            border: 1px solid #1b1763;
            background: #1b1763;
            color: #ffffff;
            border-radius: 12px;
            padding: 9px 10px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            text-transform: uppercase;
        }

        .reward-box button:hover {
            background: #26206f;
            border-color: #26206f;
        }

        .pedagogical-statement {
            border-radius: 16px;
            padding: 14px;
            position: relative;
            overflow: hidden;
            display: grid;
            gap: 10px;
        }

        .pedagogical-statement::after {
            content: "";
            position: absolute;
            right: 24px;
            top: 12px;
            font-size: 74px;
            font-weight: 700;
            color: rgba(15, 23, 42, 0.03);
            line-height: 1;
            pointer-events: none;
        }

        .statement-title {
            font-size: 8px;
            margin-bottom: 0;
            font-weight: 500;
        }

        .statement-text {
            font-size: 12px;
            line-height: 1.5;
            max-width: 840px;
            position: relative;
            z-index: 1;
            margin: 0;
        }

        .experience-card {
            background: #fff;
            border-radius: 16px;
            padding: 14px;
            color: var(--content-heading);
            border: 1px solid var(--content-border);
        }

        .experience-header {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--content-heading);
            line-height: 1;
        }

        .experience-header i {
            color: var(--content-icon);
            font-size: 12px;
        }

        .pedagogical-statement .btn-share {
            background: transparent;
            border: none;
            color: #1b1763;
            width: auto;
            height: auto;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pedagogical-statement .btn-share:hover {
            background: transparent;
            border: none;
            color: #26206f;
        }

        .experience-list {
            display: grid;
            gap: 10px;
        }

        .experience-item {
            display: grid;
            grid-template-columns: 18px minmax(0, 1fr);
            gap: 10px;
        }

        .experience-marker {
            position: relative;
            padding-top: 6px;
        }

        .experience-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--content-icon);
            display: block;
            box-shadow: none;
        }

        .experience-line {
            position: absolute;
            top: 18px;
            left: 4px;
            width: 2px;
            height: calc(100% + 10px);
            background: #f2f5fa;
        }

        .experience-item:last-child .experience-line {
            display: none;
        }

        .experience-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 4px;
        }

        .experience-role {
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
            color: var(--content-heading);
        }

        .experience-range {
            background: #fff7e5;
            color: #9a6700;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            white-space: nowrap;
            text-transform: uppercase;
        }

        .experience-company {
            margin: 0;
            font-size: 11px;
            font-weight: 500;
            color: var(--content-text);
            line-height: 1.25;
        }

        .experience-desc {
            margin: 6px 0 0;
            color: var(--content-muted);
            font-size: 10px;
            line-height: 1.45;
        }

        @media (max-width: 992px) {
            .profile-dashboard {
                grid-template-columns: 1fr;
            }

            .network-icons {
                grid-template-columns: 1fr;
            }

            .statement-title,
            .experience-header,
            .portfolio-title,
            .feedback-title {
                font-size: 8px;
                font-weight: 500;
            }

            .statement-text {
                font-size: 13px;
            }

            .experience-role {
                font-size: 12px;
            }

            .experience-company {
                font-size: 11px;
            }

            .experience-desc {
                font-size: 10px;
            }

            .experience-range {
                font-size: 10px;
            }

            .experience-top {
                flex-wrap: wrap;
            }
        }

        .item-box {
            border: 1px solid var(--content-border);
            background: var(--content-bg-soft);
            border-radius: 10px;
            padding: 12px;
        }

        .item-box h5 {
            margin: 0;
            color: var(--content-heading);
            font-size: 13px;
            font-weight: 600;
        }

        .item-box p {
            margin: 6px 0 0;
            color: var(--content-muted);
            font-size: 12px;
        }

        .content-section .list-stack {
            grid-template-columns: 1fr;
        }

        .content-section {
            display: grid;
            gap: 14px;
        }

        .card-box.content-section {
            background: #fff;
            border: 1px solid var(--content-border);
            padding: 14px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }

        .dashboard-content>.portfolio-header {
            background: #fff;
            border: 1px solid var(--content-border);
            border-radius: 16px;
            padding: 14px;
            margin-bottom: 0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
            margin-bottom: 14px;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 14px;
        }

        .course-card {
            border: 1px solid var(--content-border);
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            text-decoration: none;
            color: inherit;
            transition: all 0.25s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .course-card:hover {
            border-color: #e8ecf3;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.04);
        }

        .course-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            background: #f3f1f9;
        }

        .course-card-body {
            padding: 12px;
            display: grid;
            gap: 8px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .course-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            color: var(--content-muted);
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .course-meta span {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .course-title {
            margin: 0;
            color: var(--content-heading);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            word-break: break-word;
        }

        .feedback-item {
            border: 1px solid var(--content-border);
            border-radius: 10px;
            padding: 12px;
            background: #fff;
            display: grid;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .feedback-item:hover {
            border-color: #e8ecf3;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
        }

        .feedback-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            font-size: 12px;
            color: var(--content-muted);
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

        .mini-list {
            display: grid;
            gap: 8px;
        }

        .mini-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            border: 1px solid #eff2f7;
            background: transparent;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 12px;
            transition: all 0.2s ease;
        }

        .mini-item:hover {
            background: #f8fafc;
            border-color: #e8ecf3;
        }

        .mini-item b {
            color: #0f172a;
            font-size: 12px;
            display: block;
            margin-bottom: 2px;
        }

        .material-link {
            color: #ffffff;
            background: #1b1763;
            border: 1px solid #1b1763;
            border-radius: 10px;
            padding: 7px 10px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .material-link:hover {
            background: #26206f;
            border-color: #26206f;
            color: #ffffff;
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
            padding: 20px;
            max-height: 84vh;
            overflow: auto;
        }

        .profile-modal-content h3 {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .ledger-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border: 1px solid #eff2f7;
            border-radius: 10px;
            padding: 12px;
            background: #fafbfc;
            margin-bottom: 10px;
        }

        .ledger-row h6 {
            margin: 0;
            font-size: 14px;
            color: #0f172a;
            font-weight: 600;
        }

        .ledger-row p {
            margin: 4px 0 0;
            font-size: 11px;
            color: #64748b;
        }

        .ledger-amount {
            font-size: 14px;
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
                                <i class="bi bi-camera-fill" style="font-size: 14px; color: #1b1763;"></i>
                            </button>
                            <input type="file" id="avatarFileInput" name="avatar_file" accept="image/*"
                                style="display: none;" />
                        </div>

                        <div class="profile-text">
                            <div class="level-badge">
                                <i class="bi bi-star-fill" style="font-size: 12px;"></i>
                                Profil Trainer Profesional
                            </div>
                            <h2>{{ $displayFullName }}</h2>
                            <p class="role">{{ $displayRole }}</p>
                            <p class="headline">{{ $headline }}</p>
                            @if($isVerifiedTrainer)
                                <span class="verified-badge">
                                    <i class="bi bi-patch-check-fill"></i> VERIFIED TRAINER
                                </span>
                            @endif
                            <div class="info">
                                <div class="loc-mail">
                                    <i class="bi bi-geo-alt-fill" style="color: var(--yellow-clr); font-size: 14px;"></i>
                                    <span>{{ strtoupper($displayLocation) }}</span>
                                </div>
                                <div class="loc-mail">
                                    <i class="bi bi-envelope-fill" style="color: var(--yellow-clr); font-size: 14px;"></i>
                                    <span>{{ strtoupper($trainer->email) }}</span>
                                </div>
                            </div>
                            <p class="hero-bio">{{ \Illuminate\Support\Str::limit($displayBio, 260) }}</p>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="button" id="topEditToggleBtn" class="btn-configure">Edit Profil</button>
                        <button type="button" class="btn-share" aria-label="Share">
                            <i class="bi bi-share-fill" style="font-size: 14px;"></i>
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
                            <label for="top_academic_title">Gelar Akademik</label>
                            <input id="top_academic_title" type="text" name="academic_title"
                                value="{{ old('academic_title', $trainer->academic_title) }}">
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
                            <label for="top_phone">WhatsApp Aktif</label>
                            <input id="top_phone" type="text" name="phone" value="{{ old('phone', $trainer->phone) }}">
                        </div>
                        <div class="top-edit-field">
                            <label>Email</label>
                            <input type="text" value="{{ $trainer->email }}" readonly>
                        </div>
                        <div class="top-edit-field">
                            <label for="top_linkedin_url">LinkedIn</label>
                            <input id="top_linkedin_url" type="url" name="linkedin_url"
                                value="{{ old('linkedin_url', $trainer->linkedin_url) }}">
                        </div>
                        <div class="top-edit-field">
                            <label for="top_bank_name">Nama Bank</label>
                            <input id="top_bank_name" type="text" name="bank_name"
                                value="{{ old('bank_name', $trainer->bank_name) }}">
                        </div>
                        <div class="top-edit-field">
                            <label for="top_bank_account_number">Nomor Rekening</label>
                            <input id="top_bank_account_number" type="text" name="bank_account_number"
                                value="{{ old('bank_account_number', $trainer->bank_account_number) }}">
                        </div>
                        <div class="top-edit-field">
                            <label for="top_bank_account_holder">Nama Pemilik Rekening</label>
                            <input id="top_bank_account_holder" type="text" name="bank_account_holder"
                                value="{{ old('bank_account_holder', $trainer->bank_account_holder) }}">
                        </div>
                    </div>
                    <div class="top-edit-actions">
                        <button type="button" id="topEditCancelBtn" class="top-edit-cancel">BATAL</button>
                        <button type="submit" class="top-edit-save">SIMPAN</button>
                    </div>
                </form>
            </div>
        </section>

        <div class="profile-content-stack">
            <section class="profile-info-card profile-overview">
                <div class="statement-header" style="margin-bottom:0;">
                    <h2 class="statement-title"><i class="bi bi-person-vcard"></i> RINGKASAN PROFIL</h2>
                    <span class="view-all" style="cursor:default;">SIAP UNTUK PROFILE & SERTIFIKAT</span>
                </div>

                <div class="stats-row" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
                    <div class="card-box">
                        <p class="card-title">INFORMASI DASAR</p>
                        <h5 style="margin:0 0 6px;color:#0f172a;font-size:16px;">{{ $displayFullName }}</h5>
                        <p style="margin:0;font-size:12px;color:#475569;">{{ $trainer->email }}</p>
                        <p style="margin:4px 0 0;font-size:12px;color:#475569;">WhatsApp:
                            {{ $trainer->phone ?: 'Belum diisi' }}</p>
                    </div>

                    <div class="card-box">
                        <p class="card-title">INFORMASI PROFESIONAL</p>
                        <h5 style="margin:0 0 6px;color:#0f172a;font-size:16px;">
                            {{ $trainer->profession ?: 'Jabatan belum diisi' }}</h5>
                        <p style="margin:0;font-size:12px;color:#475569;">
                            {{ $trainer->institution ?: 'Institusi belum diisi' }}</p>
                        <p style="margin:4px 0 0;font-size:12px;color:#475569;">LinkedIn:
                            {{ $displayLinkedIn ?: 'Belum diisi' }}</p>
                    </div>

                    <div class="card-box">
                        <p class="card-title">DATA FINANSIAL</p>
                        <h5 style="margin:0 0 6px;color:#0f172a;font-size:16px;">{{ $displayBankName }}</h5>
                        <p style="margin:0;font-size:12px;color:#475569;">{{ $displayBankAccountNumber }}</p>
                        <p style="margin:4px 0 0;font-size:12px;color:#475569;">A/n {{ $displayBankAccountHolder }}</p>
                    </div>

                    <div class="card-box">
                        <p class="card-title">RIWAYAT &amp; REPUTASI</p>
                        <div class="stats-row" style="grid-template-columns:1fr 1fr; gap:8px;">
                            <div class="stats-item">
                                <p>Event</p>
                                <h4>{{ $completedEventsCount }}</h4>
                            </div>
                            <div class="stats-item">
                                <p>Course</p>
                                <h4>{{ $completedCoursesCount }}</h4>
                            </div>
                        </div>
                        <p style="margin:8px 0 0;font-size:12px;color:#475569;">Rating rata-rata:
                            {{ number_format($averageRating, 1) }} / 5</p>
                        <p style="margin:4px 0 0;font-size:12px;color:#475569;">Total E-Sertifikat: {{ $totalCertificates }}
                        </p>
                    </div>
                </div>
            </section>

            <div class="profile-dashboard">
                <aside class="dashboard-sidebar">
                    <div class="profile-info-card">
                        <div class="expertise-section">
                            <h3 class="section-title">EXPERTISE STACK</h3>
                            <div class="pill-list" style="margin-top:8px;">
                                @foreach($expertiseTags as $tag)
                                    <span class="pill">{{ strtoupper($tag) }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="profile-info-card">
                        <div class="network-section">
                            <h3 class="section-title">NETWORK TUNNELS</h3>
                            <div class="network-icons" style="margin-top:8px;">
                                <a href="{{ $displayLinkedIn ?: '#' }}" class="network-icon linkedin" aria-label="LinkedIn"
                                    {{ !empty($displayLinkedIn) ? 'target=_blank rel=noopener noreferrer' : 'aria-disabled=true style=opacity:.55;pointer-events:none;' }}><i
                                        class="bi bi-linkedin"></i><span>LINKEDIN</span></a>
                                <a href="{{ !empty($trainer->website) ? $trainer->website : '#' }}"
                                    class="network-icon website" aria-label="Website" {{ !empty($trainer->website) ? 'target=_blank rel=noopener noreferrer' : 'aria-disabled=true style=opacity:.55;pointer-events:none;' }}><i
                                        class="bi bi-globe2"></i><span>WEBSITE</span></a>
                            </div>
                        </div>
                    </div>

                    <div class="profile-info-card">
                        <div class="bank-section">
                            <h3 class="section-title">FINANCIAL DATA</h3>
                            <div class="item-box" style="margin-top:8px; display:grid; gap:8px;">
                                <div>
                                    <h5>Nama Bank</h5>
                                    <p>{{ $displayBankName }}</p>
                                </div>
                                <div>
                                    <h5>Nomor Rekening</h5>
                                    <p>{{ $displayBankAccountNumber }}</p>
                                </div>
                                <div>
                                    <h5>Nama Pemilik Rekening</h5>
                                    <p>{{ $displayBankAccountHolder }}</p>
                                </div>
                                <p style="margin:0;font-size:11px;color:#64748b;line-height:1.5;">
                                    Data rekening ini dipakai Finance untuk transfer pembayaran. Trainer tidak perlu
                                    melakukan withdraw manual.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="reward-card reward-box">
                        @php
                            $formattedRevenue = number_format((float) $totalEarned, 2, '.', ',');
                            [$revenueMain, $revenueDecimals] = explode('.', $formattedRevenue);
                        @endphp
                        <p>GROSS REVENUE</p>
                        <h3><span style="font-size:42px;">Rp</span>{{ $revenueMain }}.<span
                                class="decimals">{{ $revenueDecimals }}</span></h3>
                        <i class="bi bi-wallet2 reward-icon"></i>
                        <button type="button" id="openLedgerBtn">FINANCIAL RECORDS</button>
                    </div>
                </aside>

                <div class="dashboard-content">
                    <div class="pedagogical-statement">
                        <div class="statement-header">
                            <h2 class="statement-title"><i class="bi bi-person"></i> Bio</h2>
                            <button type="button" id="topEditToggleBtnMirror" class="btn-share"
                                aria-label="Edit Statement"><i class="bi bi-pencil"></i></button>
                        </div>
                        <p class="statement-text">{{ $displayBio }}</p>
                    </div>

                    <div class="experience-card">
                        <div class="experience-header"><i class="bi bi-briefcase"></i> Experience</div>
                        <div class="experience-list">
                            @forelse($upcomingEvents as $event)
                                <div class="experience-item">
                                    <div class="experience-marker">
                                        <span class="experience-dot"></span>
                                        <span class="experience-line"></span>
                                    </div>
                                    <div>
                                        <div class="experience-top">
                                            <h3 class="experience-role">{{ $event->title }}</h3>
                                            <span
                                                class="experience-range">{{ optional($event->event_date)->format('Y') ?? now()->format('Y') }}</span>
                                        </div>
                                        <p class="experience-company">{{ $displayRole }}</p>
                                        <p class="experience-desc">{{ $event->participants_count ?? 0 }} participants •
                                            {{ optional($event->event_date)->format('d M Y') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="experience-item">
                                    <div class="experience-marker">
                                        <span class="experience-dot"></span>
                                    </div>
                                    <div>
                                        <div class="experience-top">
                                            <h3 class="experience-role">{{ $displayRole }}</h3>
                                            <span class="experience-range">PRESENT</span>
                                        </div>
                                        <p class="experience-company">{{ $trainer->institution ?: 'idSpora Trainer' }}</p>
                                        <p class="experience-desc">Aktif mengembangkan pengalaman belajar peserta dengan sesi
                                            training praktis.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="card-box content-section">
                        <div class="portfolio-header">
                            <h2 class="portfolio-title"><i class="bi bi-award-fill"></i> E-SERTIFIKAT APRESIASI</h2>
                            <a href="{{ route('trainer.certificates.index') }}" class="view-all">VIEW ALL</a>
                        </div>

                        <div class="mini-list">
                            @forelse($trainerCertificates as $certificate)
                                @php
                                    $isEventCert = $certificate->certifiable_type === \App\Models\Event::class;
                                    $certifiable = $certificate->certifiable;
                                    $certTitle = $isEventCert
                                        ? (optional($certifiable)->title ?? 'Event')
                                        : (optional($certifiable)->name ?? 'Course');
                                    $downloadUrl = $isEventCert && $certifiable
                                        ? route('trainer.certificates.events.download', $certifiable)
                                        : (!$isEventCert && $certifiable ? route('trainer.certificates.courses.download', $certifiable) : null);
                                @endphp
                                <div class="mini-item">
                                    <div>
                                        <b>{{ \Illuminate\Support\Str::limit($certTitle, 44) }}</b>
                                        <p style="margin:0;color:#64748b;">{{ $certificate->certificate_number }}</p>
                                    </div>
                                    @if($downloadUrl)
                                        <a href="{{ $downloadUrl }}" class="material-link" target="_blank">UNDUH</a>
                                    @endif
                                </div>
                            @empty
                                <div class="item-box">
                                    <h5>Belum ada e-sertifikat</h5>
                                    <p>Sertifikat apresiasi akan muncul di sini setelah event atau course selesai.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="portfolio-header">
                        <h2 class="portfolio-title"><i class="bi bi-grid-3x3-gap-fill"></i> ACTIVE COURSE PORTFOLIO</h2>
                        <a href="{{ route('trainer.courses') }}" class="view-all">VIEW ALL</a>
                    </div>

                    <div class="course-grid">
                        @forelse($activeCourses as $course)
                            @php
                                $thumbnailUrl = $course->card_thumbnail_url;
                                $displayCourseImage = $thumbnailUrl ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=900';
                                $rating = number_format((float) ($course->reviews_avg_rating ?? 0), 1);
                              @endphp
                            <a href="{{ route('trainer.detail-course', $course->id) }}" class="course-card"
                                style="position:relative;">
                                @if((float) $rating >= 4.5)
                                    <div
                                        style="position:absolute;top:8px;right:8px;background:#fbbf24;color:#78350f;padding:5px 10px;border-radius:5px;font-size:9px;font-weight:700;z-index:10;display:flex;align-items:center;gap:4px;letter-spacing:0.5px;">
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
                                <h5>Belum ada kelas aktif</h5>
                                <p>Kelas yang sedang Anda ampu akan muncul di sini.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="student-feedback">
                        <div class="feedback-header">
                            <h2 class="feedback-title"><i class="bi bi-chat-quote-fill"></i> Recent Student Feedback</h2>
                            <span
                                style="font-size:12px;font-weight:700;color:#1b1763;">{{ number_format($averageRating, 1) }}
                                <i class="bi bi-star-fill" style="color:#f59e0b"></i></span>
                        </div>

                        <div class="feedback-list">
                            @forelse($selectedTestimonials as $feedback)
                                @php
                                    $rating = max(1, min(5, (int) $feedback->rating));
                                    $authorName = optional($feedback->user)->name ?: 'Anonymous';
                                    $authorInitial = strtoupper(mb_substr($authorName, 0, 1));
                                  @endphp
                                <div class="feedback-item">
                                    <div class="feedback-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi {{ $i <= $rating ? 'bi-star-fill' : 'bi-star' }}"
                                                style="color:#f59e0b;font-size:12px;"></i>
                                        @endfor
                                        <span
                                            class="feedback-time">{{ strtoupper(optional($feedback->created_at)->diffForHumans()) }}</span>
                                    </div>
                                    <p style="margin:6px 0 0;color:#475569;line-height:1.5;font-size:12px;">
                                        "{{ $feedback->comment ?: 'Tidak ada komentar.' }}"</p>
                                    <div class="feedback-author">
                                        <span class="author-avatar">{{ $authorInitial }}</span>
                                        <span class="author-name">{{ strtoupper($authorName) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="item-box">
                                    <h5>Belum ada feedback</h5>
                                    <p>Feedback peserta untuk course Anda akan tampil di sini.</p>
                                </div>
                            @endforelse
                        </div>

                        <a href="{{ route('trainer.feedback') }}" class="view-all-reviews"
                            style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;">VIEW ALL REVIEWS <i
                                class="bi bi-arrow-right"></i></a>
                    </div>
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
            const topEditToggleBtnMirror = document.getElementById('topEditToggleBtnMirror');
            const topInlineEditForm = document.getElementById('topInlineEditForm');
            const topEditCancelBtn = document.getElementById('topEditCancelBtn');

            const syncTopEditButton = () => {
                if (!topEditToggleBtn || !topInlineEditForm) return;
                const isActive = topInlineEditForm.classList.contains('active');
                topEditToggleBtn.textContent = isActive ? 'Tutup Edit' : 'Edit Profil';
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

            if (topEditToggleBtnMirror && topEditToggleBtn) {
                topEditToggleBtnMirror.addEventListener('click', function () {
                    topEditToggleBtn.click();
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