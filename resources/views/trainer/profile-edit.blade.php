@extends('layouts.trainer')

@section('title', 'Edit Profile Trainer')

@push('styles')
<!-- Cropper.js CSS CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" />
<style>
.profile-wrap {
    display: grid;
    gap: 16px;
    padding: 0 !important;
    margin: 0 !important;
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
    background: url("https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1600")
        center/cover no-repeat;
    opacity: 0.75;
}

.top-content::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
        90deg,
        rgba(20, 18, 68, 0.98),
        rgba(27, 23, 99, 0.85),
        rgba(27, 23, 99, 0.55)
    );
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
    color: #2e2050;
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
    background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
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
    border: 2px solid rgba(255, 255, 255, 0.45);
    background: #fff;
}

.profile-hero h1 {
    font-size: 22px;
    margin: 0;
    line-height: 1.2;
}

.profile-hero p {
    margin: 4px 0 0;
    color: rgba(255, 255, 255, 0.82);
}

.profile-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 8px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.82);
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
    border: 1px solid rgba(255, 255, 255, 0.35);
    background: rgba(255, 255, 255, 0.14);
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
    gap: 24px;
    align-items: start;
    padding: 0 !important;
    margin: 0 !important;
}

.profile-dashboard .dashboard-sidebar,
.profile-dashboard .dashboard-content {
    display: grid;
    gap: 20px;
}

@media (max-width: 992px) {
    .profile-dashboard {
        grid-template-columns: 1fr;
    }
}

.profile-info-card {
    background: #fff;
    border: 1px solid #eff2f7;
    border-radius: 28px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    display: grid;
    gap: 20px;
}

.info-divider {
    border-top: 1px solid #eff2f7;
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
    color: #2e2050;
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
    border: 1px solid #eff2f7;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}

.list-stack {
    display: grid;
    gap: 10px;
}

.card-box {
    background: #fff;
    border: 1px solid #eff2f7;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
}

.card-title {
    margin: 0 0 10px;
    font-size: 12px;
    letter-spacing: 0.32px;
    color: #64748b;
    font-weight: 700;
    text-transform: none;
}

.section-title {
    margin: 0;
    font-size: 12px;
    letter-spacing: 4.48px;
    text-transform: uppercase;
    color: #2e2050;
    font-weight: 800;
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
    font-size: 14px;
    color: #0f172a;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    letter-spacing: 0.3px;
}

.view-all,
.view-all-reviews,
.schedule-manage-link {
    color: #2e2050;
    text-decoration: none;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.2s ease;
}

.view-all:hover,
.view-all-reviews:hover,
.schedule-manage-link:hover {
    opacity: 0.7;
}

.statement-text {
    margin: 0;
    color: #475569;
    line-height: 1.7;
    font-size: 13px;
}

.feedback-list,
.schedule-list {
    display: grid;
    gap: 10px;
}

.feedback-time {
    color: #94a3b8;
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
    background: #2e2050;
    color: #fff;
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
    color: #334155;
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
    font-size: 10px;
    color: #94a3b8;
    font-weight: 600;
    letter-spacing: 0.8px;
    text-transform: uppercase;
}

.stats-item h4 {
    margin: 4px 0 0;
    color: #0f172a;
    font-size: 20px;
    font-weight: 700;
}

.pill-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.pill {
    background: #f3f4f6;
    color: #2e2050;
    font-size: 11px;
    border-radius: 12px;
    padding: 8px 14px;
    font-weight: 700;
    letter-spacing: 0.32px;
    border: 1px solid #e9edf4;
}

.network-icons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.network-icon {
    min-height: 42px;
    border-radius: 12px;
    padding: 0 12px;
    gap: 8px;
    font-size: 11px;
    justify-content: flex-start;
}

.network-icon i {
    font-size: 14px;
}

.network-icon.linkedin {
    background: #eaf1ff;
    color: #3168d8;
}

.network-icon.website {
    background: #dff1e8;
    color: #0f8a4b;
}

.network-icon.twitter {
    background: #f2f3f6;
    color: #111827;
}

.network-icon.github {
    background: #eceffd;
    color: #4f46e5;
}

.reward-box {
    background: linear-gradient(135deg, #1f1a77 0%, #261f8c 100%);
    color: #fff;
    border-radius: 30px;
    padding: 26px;
    display: grid;
    gap: 14px;
    position: relative;
    overflow: hidden;
}

.reward-box p {
    margin: 0;
    color: #f8d537;
    font-size: 11px;
    letter-spacing: 2.56px;
    text-transform: uppercase;
    font-weight: 700;
}

.reward-box h3 {
    margin: 0;
    font-size: 43px;
    font-weight: 700;
    line-height: 1;
    letter-spacing: -0.16px;
    color: #fff;
}

.reward-box .decimals {
    color: #f8d537;
}

.reward-box .reward-icon {
    position: absolute;
    right: 26px;
    top: 32px;
    font-size: 46px;
    color: rgba(255, 255, 255, 0.08);
}

.reward-box button {
    border: none;
    background: #f8fafc;
    color: #2e2050;
    border-radius: 12px;
    padding: 13px 14px;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: 1.28px;
    display: flex;
    align-items: center;
    gap: 6px;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
}

.reward-box button:hover {
    background: #fff;
}

.pedagogical-statement {
    border-radius: 30px;
    padding: 34px 40px;
    position: relative;
    overflow: hidden;
}

.pedagogical-statement::after {
    content: "99";
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
    font-size: 16px;
    margin-bottom: 12px;
}

.statement-text {
    font-size: 13px;
    line-height: 1.8;
    max-width: 840px;
    position: relative;
    z-index: 1;
}

.experience-card {
    background: #211870;
    border-radius: 40px;
    padding: 34px 38px;
    color: #fff;
}

.experience-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 26px;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
}

.experience-header i {
    color: #fbbf24;
    font-size: 22px;
}

.pedagogical-statement .btn-share {
    background: #f5f7fc;
    border: 1px solid #e9edf4;
    color: #2e2050;
    width: 38px;
    height: 38px;
}

.pedagogical-statement .btn-share:hover {
    background: #eef2fa;
}

.experience-list {
    display: grid;
    gap: 30px;
}

.experience-item {
    display: grid;
    grid-template-columns: 18px minmax(0, 1fr);
    gap: 16px;
}

.experience-marker {
    position: relative;
    padding-top: 6px;
}

.experience-dot {
    width: 12px;
    height: 12px;
    border-radius: 999px;
    background: #fbbf24;
    display: block;
    box-shadow: 0 0 10px rgba(251, 191, 36, 0.45);
}

.experience-line {
    position: absolute;
    top: 20px;
    left: 5px;
    width: 2px;
    height: calc(100% + 24px);
    background: rgba(255, 255, 255, 0.16);
}

.experience-item:last-child .experience-line {
    display: none;
}

.experience-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.experience-role {
    font-size: 16px;
    font-weight: 700;
    line-height: 1.25;
    color: #fff;
}

.experience-range {
    background: rgba(255, 255, 255, 0.08);
    color: #fbbf24;
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.28px;
    white-space: nowrap;
    text-transform: uppercase;
}

.experience-company {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    line-height: 1.3;
}

.experience-desc {
    margin: 8px 0 0;
    color: rgba(255, 255, 255, 0.76);
    font-size: 12px;
    line-height: 1.55;
}

@media (max-width: 992px) {
    .profile-dashboard {
        grid-template-columns: 1fr;
    }

    .network-icons {
        grid-template-columns: 1fr;
    }

    .statement-title,
    .experience-header {
        font-size: 18px;
    }

    .statement-text {
        font-size: 14px;
    }

    .experience-role {
        font-size: 16px;
    }

    .experience-company {
        font-size: 15px;
    }

    .experience-desc {
        font-size: 13px;
    }

    .experience-range {
        font-size: 10px;
    }

    .experience-top {
        flex-wrap: wrap;
    }
}

.item-box {
    border: 1px solid #eff2f7;
    background: #fafbfc;
    border-radius: 10px;
    padding: 12px;
}

.item-box h5 {
    margin: 0;
    color: #0f172a;
    font-size: 14px;
    font-weight: 600;
}

.item-box p {
    margin: 6px 0 0;
    color: #64748b;
    font-size: 12px;
}

.content-section .list-stack {
    grid-template-columns: 1fr;
}

.content-section {
    display: grid;
    gap: 14px;
}

.course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
}

.course-card {
    border: 1px solid #eff2f7;
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
    color: #94a3b8;
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
    color: #0f172a;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    word-break: break-word;
}

.feedback-item {
    border: 1px solid #eff2f7;
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
    color: #2e2050;
    text-decoration: none;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
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
    background: rgba(2, 6, 23, 0.55);
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

/* Cropper Modal CSS */
.crop-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(8px);
    opacity: 0;
    transition: opacity 0.25s ease-in-out;
}
.crop-modal-overlay.show {
    display: flex;
    opacity: 1;
}
.crop-modal-container {
    background: #ffffff;
    border-radius: 20px;
    width: 90%;
    max-width: 480px;
    padding: 24px;
    box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
    transform: translateY(30px);
    transition: transform 0.25s ease-in-out;
}
.crop-modal-overlay.show .crop-modal-container {
    transform: translateY(0);
}
.crop-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 12px;
}
.crop-modal-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e1b4b; /* Navy */
}
.crop-modal-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #64748b;
    transition: color 0.15s ease;
}
.crop-modal-close:hover {
    color: #1e293b;
}
.crop-workspace {
    width: 100%;
    height: 300px;
    background-color: #f8fafc;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    border: 1px dashed #e2e8f0;
}
.crop-workspace img {
    max-width: 100%;
    max-height: 100%;
    display: block;
}
.crop-footer {
    margin-top: 18px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}
.crop-zoom-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8fafc;
    padding: 8px 14px;
    border-radius: 10px;
    border: 1px solid #f1f5f9;
}
.crop-zoom-wrapper i {
    color: #64748b;
    font-size: 14px;
}
.crop-zoom-slider {
    flex: 1;
    height: 5px;
    background: #cbd5e1;
    border-radius: 10px;
    outline: none;
    -webkit-appearance: none;
}
.crop-zoom-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #2e2050;
    cursor: pointer;
}
.crop-button-group {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
.btn-crop-cancel {
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #334155;
    border-radius: 10px;
    padding: 10px 16px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}
.btn-crop-cancel:hover {
    background: #f8fafc;
}
.btn-crop-submit {
    border: none;
    background: #2e2050;
    color: #ffffff;
    border-radius: 10px;
    padding: 10px 18px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}
.btn-crop-submit:hover {
    background: #19102c;
}

/* Rounded square cropper box style (to match the profile avatar preview radius) */
.cropper-view-box {
    border-radius: 16px;
    outline: 2px solid #2e2050;
    outline-color: rgba(46, 32, 80, 0.75);
}
.cropper-face {
    border-radius: 16px;
    background-color: transparent;
}
.cropper-line, .cropper-point {
    display: none !important;
}

</style>
@endpush

@php
    $pageTitle = 'Edit Profile';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Profile', 'url' => route('trainer.profile')],
        ['label' => 'Edit']
    ];
@endphp

@section('content')
    <div style="max-width:900px;margin:0 auto;display:grid;gap:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
            <h1 style="margin:0;color:#0f172a;font-size:24px;">Edit Profile Trainer</h1>
            <a href="{{ route('trainer.profile') }}"
                style="text-decoration:none;color:#2e2050;font-weight:600;font-size:13px;">? Kembali ke Profile</a>
        </div>

        <form action="{{ route('trainer.profile.update') }}" method="POST" enctype="multipart/form-data"
            style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:16px;display:grid;gap:14px;">
            @csrf
            @method('PUT')

            <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                <div style="position:relative; width:80px; height:80px; border-radius:14px; overflow:hidden; border:2px solid #e2e8f0; background:#f8fafc; flex-shrink:0;">
                    <img id="avatar-preview" src="{{ $trainer->avatar_url }}" alt="{{ $trainer->name }}"
                        style="width:100%;height:100%;object-fit:cover;" />
                </div>
                <div style="display:grid;gap:6px;">
                    <label style="font-size:12px;color:#334155;font-weight:700;">Foto Profil</label>
                    <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                        <!-- Styled buttons -->
                        <button type="button" id="btn-select-file" style="border:1px solid #cbd5e1; background:#ffffff; border-radius:10px; padding:8px 14px; font-size:12px; font-weight:700; color:#334155; cursor:pointer; transition:all 0.15s ease;">
                            Pilih Foto Baru
                        </button>
                        <input type="file" id="avatar" name="avatar" accept="image/*" style="display:none;" />
                    </div>
                    <span style="font-size:11px; color:#64748b;">
                        Foto baru akan langsung diatur (digeser & di-zoom) setelah Anda memilih berkas.
                    </span>
                    @error('avatar')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px;">
                <div style="display:grid;gap:6px;">
                    <label for="name" style="font-size:12px;font-weight:600;color:#334155;">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $trainer->name) }}" required
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('name')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="email" style="font-size:12px;font-weight:600;color:#334155;">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $trainer->email) }}" required
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('email')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="academic_title" style="font-size:12px;font-weight:600;color:#334155;">Gelar Akademik</label>
                    <input id="academic_title" name="academic_title" type="text"
                        value="{{ old('academic_title', $trainer->academic_title) }}" placeholder="Contoh: S.Kom., M.Kom."
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('academic_title')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="phone" style="font-size:12px;font-weight:600;color:#334155;">WhatsApp Aktif</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $trainer->phone) }}"
                        placeholder="Contoh: +6281234567890"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('phone')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="profession" style="font-size:12px;font-weight:600;color:#334155;">Jabatan / Profesi</label>
                    <input id="profession" name="profession" type="text"
                        value="{{ old('profession', $trainer->profession) }}"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('profession')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="institution" style="font-size:12px;font-weight:600;color:#334155;">Institusi</label>
                    <input id="institution" name="institution" type="text"
                        value="{{ old('institution', $trainer->institution) }}"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('institution')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display:grid;gap:6px;">
                <label for="website" style="font-size:12px;font-weight:600;color:#334155;">Website</label>
                <input id="website" name="website" type="text" value="{{ old('website', $trainer->website) }}"
                    placeholder="contoh: https://example.com"
                    style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                @error('website')
                    <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:grid;gap:6px;">
                <label for="linkedin_url" style="font-size:12px;font-weight:600;color:#334155;">LinkedIn</label>
                <input id="linkedin_url" name="linkedin_url" type="url"
                    value="{{ old('linkedin_url', $trainer->linkedin_url) }}" placeholder="https://www.linkedin.com/in/..."
                    style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                @error('linkedin_url')
                    <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;">
                <div style="display:grid;gap:6px;">
                    <label for="bank_name" style="font-size:12px;font-weight:600;color:#334155;">Nama Bank</label>
                    <input id="bank_name" name="bank_name" type="text" value="{{ old('bank_name', $trainer->bank_name) }}"
                        placeholder="BCA / Mandiri / BNI / dll"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('bank_name')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="bank_account_number" style="font-size:12px;font-weight:600;color:#334155;">Nomor
                        Rekening</label>
                    <input id="bank_account_number" name="bank_account_number" type="text"
                        value="{{ old('bank_account_number', $trainer->bank_account_number) }}"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('bank_account_number')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display:grid;gap:6px;">
                    <label for="bank_account_holder" style="font-size:12px;font-weight:600;color:#334155;">Nama Pemilik
                        Rekening</label>
                    <input id="bank_account_holder" name="bank_account_holder" type="text"
                        value="{{ old('bank_account_holder', $trainer->bank_account_holder) }}"
                        style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                    @error('bank_account_holder')
                        <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display:grid;gap:6px;">
                <label for="bio" style="font-size:12px;font-weight:600;color:#334155;">Bio</label>
                <textarea id="bio" name="bio" rows="5"
                    style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;resize:vertical;">{{ old('bio', $trainer->bio) }}</textarea>
                @error('bio')
                    <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="border-top: 1px solid #e2e8f0; margin-top: 8px; padding-top: 16px; display: grid; gap: 12px;">
                <h3 style="font-size: 14px; font-weight: 700; color: #2e2050; margin: 0;">Ubah Password <span style="font-weight: 400; color: #64748b; font-size: 12px;">(Kosongkan jika tidak ingin mengubah)</span></h3>
                
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:12px;">
                    <div style="display:grid;gap:6px;">
                        <label for="current_password" style="font-size:12px;font-weight:600;color:#334155;">Password Saat Ini</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                            style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                        @error('current_password')
                            <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="display:grid;gap:6px;">
                        <label for="password" style="font-size:12px;font-weight:600;color:#334155;">Password Baru</label>
                        <input id="password" name="password" type="password" autocomplete="new-password"
                            style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                        @error('password')
                            <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="display:grid;gap:6px;">
                        <label for="password_confirmation" style="font-size:12px;font-weight:600;color:#334155;">Konfirmasi Password Baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                            style="border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;" />
                        @error('password_confirmation')
                            <span class="text-danger small" style="font-size: 11px; margin-top: 2px; display: block;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;">
                <a href="{{ route('trainer.profile') }}"
                    style="text-decoration:none;border:1px solid #cbd5e1;border-radius:10px;padding:10px 14px;color:#334155;font-size:12px;font-weight:600;">Batal</a>
                <button type="submit"
                    style="border:none;background:#2e2050;color:#fff;border-radius:10px;padding:10px 14px;font-size:12px;font-weight:700;">Simpan
                    Perubahan</button>
            </div>
        </form>
    </div>

    <!-- Cropper Modal -->
    <div id="cropModal" class="crop-modal-overlay">
        <div class="crop-modal-container">
            <div class="crop-modal-header">
                <span class="crop-modal-title">Atur Posisi & Ukuran Foto</span>
                <button type="button" class="crop-modal-close" onclick="closeCropModal()">&times;</button>
            </div>
            <div class="crop-workspace">
                <img id="crop-image" src="" alt="Crop Area" />
            </div>
            <div class="crop-footer">
                <div class="crop-zoom-wrapper">
                    <i class="bi bi-zoom-out"></i>
                    <input type="range" id="crop-zoom-slider" class="crop-zoom-slider" min="0" max="100" value="0" />
                    <i class="bi bi-zoom-in"></i>
                </div>
                <div class="crop-button-group">
                    <button type="button" class="btn-crop-cancel" onclick="closeCropModal()">Batal</button>
                    <button type="button" id="btn-crop-save" class="btn-crop-submit">Selesai</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- Cropper.js JS CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
<script>
    let cropper = null;
    let originalFile = null;
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatar-preview');
    const cropModal = document.getElementById('cropModal');
    const cropImage = document.getElementById('crop-image');
    const zoomSlider = document.getElementById('crop-zoom-slider');
    const btnCropSave = document.getElementById('btn-crop-save');
    const btnSelectFile = document.getElementById('btn-select-file');
    let isCropConfirmed = false;

    // Reset crop modal
    function closeCropModal() {
        cropModal.classList.remove('show');
        setTimeout(() => {
            cropModal.style.display = 'none';
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            // If crop was not confirmed and they selected a new file, reset input
            if (!isCropConfirmed && originalFile) {
                avatarInput.value = '';
            }
        }, 250);
    }

    // Trigger file selection
    if (btnSelectFile) {
        btnSelectFile.addEventListener('click', function() {
            avatarInput.click();
        });
    }

    avatarInput.addEventListener('change', function(e) {
        const files = e.target.files;
        if (files && files.length > 0) {
            isCropConfirmed = false;
            const file = files[0];
            originalFile = file;
            
            // Set image in modal
            const fileReader = new FileReader();
            fileReader.onload = function(event) {
                cropImage.src = event.target.result;
                openCropper();
            };
            fileReader.readAsDataURL(file);
        }
    });

    function openCropper() {
        // Show modal
        cropModal.style.display = 'flex';
        cropModal.offsetHeight; // Force reflow
        cropModal.classList.add('show');
        
        // Initialize Cropper
        if (cropper) {
            cropper.destroy();
        }
        
        cropper = new Cropper(cropImage, {
            aspectRatio: 1,
            viewMode: 1,
            dragMode: 'move',
            cropBoxMovable: false,
            cropBoxResizable: false,
            toggleDragModeOnDblclick: false,
            background: false,
            autoCropArea: 1,
            checkCrossOrigin: false, // Prevent adding crossorigin attribute
            ready: function() {
                zoomSlider.value = 0;
            }
        });

        // Listen to mousewheel or zoom events to update slider
        cropImage.addEventListener('zoom', function(e) {
            const imageData = cropper.getImageData();
            const minZoom = imageData.width / imageData.naturalWidth;
            const maxZoom = 3.0;
            const ratio = e.detail.ratio;
            const percent = Math.min(100, Math.max(0, ((ratio - minZoom) / (maxZoom - minZoom)) * 100));
            zoomSlider.value = percent;
        });
    }

    // Zoom slider control
    zoomSlider.addEventListener('input', function() {
        if (!cropper) return;
        const zoomPercent = parseFloat(this.value);
        const imageData = cropper.getImageData();
        const minZoom = imageData.width / imageData.naturalWidth;
        const maxZoom = 3.0;
        const targetZoom = minZoom + (maxZoom - minZoom) * (zoomPercent / 100);
        cropper.zoomTo(targetZoom);
    });

    // Handle crop save
    btnCropSave.addEventListener('click', function() {
        if (!cropper) return;
        
        try {
            // Get high quality cropped canvas
            const canvas = cropper.getCroppedCanvas({
                width: 400,
                height: 400,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });
            
            if (!canvas) {
                alert("Gagal memotong gambar. Kanvas tidak valid.");
                return;
            }
            
            canvas.toBlob(function(blob) {
                if (!blob) {
                    alert("Gagal memproses potongan gambar.");
                    return;
                }
                
                isCropConfirmed = true;
                
                // Update preview on page
                const previewUrl = URL.createObjectURL(blob);
                avatarPreview.src = previewUrl;
                
                // Create cropped file object
                let fileName = 'cropped_avatar.png';
                let fileType = 'image/png';
                if (originalFile) {
                    fileName = originalFile.name;
                    fileType = originalFile.type;
                }
                
                const croppedFile = new File([blob], fileName, { type: fileType });
                
                // Put cropped file into hidden file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(croppedFile);
                avatarInput.files = dataTransfer.files;
                
                // Close modal
                closeCropModal();
            }, originalFile ? originalFile.type : 'image/png');
        } catch (error) {
            console.error("Error cropping image:", error);
            alert("Terjadi kesalahan saat memproses gambar.");
        }
    });
</script>
@endpush

