@extends('layouts.trainer')

@section('title', 'Profile - Trainer')

@push('styles')
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
    color: #1b1763;
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
    color: #1b1763;
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
    background: #1b1763;
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
    color: #1b1763;
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
    color: #1b1763;
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
    color: #1b1763;
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
    color: #1b1763;
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

</style>
@endpush

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
    $profileCompletion = (int) $trainer->getProfileCompletionPercentage();
@endphp

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <style>
        :root {
            --primary: #1e3a8a;
            --primary-light: #3b82f6;
            --primary-soft: #eff6ff;
            --primary-border: #bfdbfe;
            --text-dark: #111827;
            --text-muted: #6b7280;
            --bg-gray: #f9fafb;
            --border-light: #f3f4f6;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
        }

        body {
            background-color: #ffffff;
            font-family: 'Inter', sans-serif;
        }

        .profile-container {
            display: flex;
            flex-direction: column;
            gap: 24px;
            width: 100%;
            color: var(--text-dark);
        }

        .top-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        /* HERO CARD (LEFT) - ULTRA PREMIUM EDITION */
        .hero-card {
            background: linear-gradient(120deg, #0f172a, #1e3a8a, #4c1d95, #0f172a);
            background-size: 300% 300%;
            animation: gradientMove 12s ease infinite;
            border-radius: var(--radius-xl);
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            box-shadow: 0 20px 50px -10px rgba(30, 58, 138, 0.4), 0 10px 20px -15px rgba(0, 0, 0, 0.5);
            display: flex;
            gap: 40px;
            align-items: center;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1;
        }

        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .hero-card:hover {
            box-shadow: 0 30px 60px -15px rgba(30, 58, 138, 0.5), 0 15px 25px -10px rgba(0, 0, 0, 0.6);
            transform: translateY(-4px);
        }

        .hero-card::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.4) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            filter: blur(40px);
            z-index: -1;
            animation: pulseGlow 6s infinite alternate;
        }

        .hero-card::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: 10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.3) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            filter: blur(40px);
            z-index: -1;
            animation: pulseGlow2 8s infinite alternate;
        }

        @keyframes pulseGlow {
            0% { transform: scale(1) translate(0, 0); opacity: 0.5; }
            100% { transform: scale(1.2) translate(-20px, 20px); opacity: 0.8; }
        }
        @keyframes pulseGlow2 {
            0% { transform: scale(1) translate(0, 0); opacity: 0.5; }
            100% { transform: scale(1.3) translate(20px, -20px); opacity: 0.9; }
        }

        .hero-card .edit-btn {
            position: absolute;
            top: 24px;
            right: 24px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .hero-card .edit-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
            color: #ffffff;
        }

        .hero-avatar {
            position: relative;
            flex-shrink: 0;
            z-index: 2;
        }

        .hero-avatar img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255, 255, 255, 0.3);
            padding: 4px;
            background: linear-gradient(135deg, rgba(255,255,255,0.5), rgba(255,255,255,0.1));
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.3), 0 0 20px rgba(59, 130, 246, 0.4);
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .hero-card:hover .hero-avatar img {
            transform: scale(1.08) rotate(-3deg);
            box-shadow: 0 20px 40px -5px rgba(0, 0, 0, 0.4), 0 0 30px rgba(59, 130, 246, 0.6);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .hero-avatar .edit-photo {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: #38bdf8;
            color: #ffffff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.5);
            border: 3px solid rgba(255,255,255,0.8);
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .hero-avatar .edit-photo:hover {
            background: #0ea5e9;
            transform: scale(1.15) rotate(15deg);
        }

        .hero-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            flex-grow: 1;
            z-index: 2;
        }

        .hero-info h1 {
            margin: 0;
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }

        .hero-info h1 i {
            color: #38bdf8;
            font-size: 24px;
            filter: drop-shadow(0 0 8px rgba(56, 189, 248, 0.6));
        }

        .role-badge {
            display: inline-block;
            background: linear-gradient(135deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: #e0f2fe;
            padding: 8px 20px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 700;
            margin: 16px 0 20px;
            width: fit-content;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.5px;
        }

        .hero-bio {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.8;
            margin: 0 0 28px;
            max-width: 580px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }

        .hero-meta {
            display: flex;
            gap: 28px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
            margin-bottom: 28px;
        }

        .hero-meta span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hero-meta i {
            font-size: 18px;
            color: #7dd3fc;
        }

        .hero-contacts {
            display: flex;
            gap: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            padding-top: 24px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 600;
        }

        .hero-contacts span {
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }
        
        .hero-contacts span:hover {
            color: #ffffff;
            text-shadow: 0 0 8px rgba(255,255,255,0.4);
            transform: translateY(-1px);
        }

        .hero-contacts i {
            color: #38bdf8;
            font-size: 18px;
            background: rgba(255, 255, 255, 0.1);
            padding: 8px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        .hero-contacts span:hover i {
            background: #38bdf8;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(56, 189, 248, 0.4);
        }

        /* ACTIVITY CARD (RIGHT) */
        .sidebar-card {
            background: #ffffff;
            border-radius: var(--radius-xl);
            padding: 24px;
            border: 1px solid var(--border-light);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01);
            height: fit-content;
        }

        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .sidebar-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .activity-item {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            padding: 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .activity-icon.primary {
            background: var(--primary-soft);
            color: var(--primary-light);
        }

        .activity-icon.green {
            background: #ecfdf5;
            color: #10b981;
        }

        .activity-icon.orange {
            background: #fffbeb;
            color: #f59e0b;
        }

        .activity-icon.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .activity-text h4 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .activity-text p {
            margin: 4px 0 0;
            font-size: 11px;
            color: var(--text-muted);
        }

        /* ACHIEVEMENT LIST */
        .achievement-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .achievement-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }

        .achieve-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 16px;
        }

        .achieve-icon.gold {
            background: #fef3c7;
            color: #d97706;
        }

        .achieve-icon.green {
            background: #dcfce7;
            color: #15803d;
        }

        .achieve-icon.primary {
            background: var(--primary-soft);
            color: var(--primary-light);
        }

        .achieve-info {
            flex-grow: 1;
        }

        .achieve-info-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
        }

        .achieve-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .achieve-date {
            font-size: 11px;
            color: var(--text-muted);
        }

        .achieve-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.4;
        }

        /* RATING SUMMARY SIDEBAR */
        .rating-sidebar-big {
            text-align: left;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--border-light);
            padding-bottom: 24px;
        }

        .rating-sidebar-big h2 {
            margin: 0;
            font-size: 40px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -1px;
        }

        .rating-sidebar-big h2 i {
            font-size: 24px;
            color: #facc15;
        }

        .rating-sidebar-big p {
            margin: 4px 0 0;
            font-size: 13px;
            color: var(--text-muted);
        }

        .rating-bars {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 24px;
        }

        .rating-bar-row {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
        }

        .rating-bar-wrap {
            flex-grow: 1;
            height: 6px;
            background: #e5e7eb;
            border-radius: 99px;
            overflow: hidden;
        }

        .rating-bar-fill {
            height: 100%;
            background: #facc15;
            border-radius: 99px;
        }

        .rating-count {
            width: 30px;
            text-align: right;
            color: var(--text-dark);
        }

        .rating-pct {
            font-size: 11px;
            color: var(--text-muted);
            width: 35px;
            text-align: right;
        }

        .rating-aspects {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 24px;
        }

        .aspect-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 12px;
        }

        .aspect-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: var(--text-muted);
        }

        .aspect-row span:last-child {
            font-weight: 600;
            color: var(--text-dark);
        }

        .aspect-row i {
            color: var(--primary-light);
            margin-right: 8px;
            font-size: 16px;
            width: 20px;
            text-align: center;
            display: inline-block;
        }

        .btn-write-review {
            width: 100%;
            background: #ffffff;
            border: 1px solid var(--primary-light);
            color: var(--primary-light);
            padding: 10px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }

        .btn-write-review:hover {
            background: var(--primary-soft);
        }

        /* TABS NAV */
        .profile-tabs {
            display: flex;
            padding: 0 32px;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-light);
        }

        .tab-item {
            padding: 16px 12px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-item.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab-item i {
            font-size: 16px;
        }

        /* TAB CONTENT PANELS */
        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* SHARED SECTION STYLES */
        .content-section {
            background: #ffffff;
            border-radius: var(--radius-xl);
            padding: 32px;
            border: 1px solid var(--border-light);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.01);
            margin-bottom: 0;
        }

        .section-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
            letter-spacing: -0.5px;
        }

        .section-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin: 8px 0 24px;
        }

        .btn-outline-primary {
            background: #ffffff;
            border: 1px solid var(--border-light);
            color: var(--primary);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-icon-edit {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
        }

        .btn-icon-edit:hover {
            color: var(--primary);
            background: var(--primary-soft);
        }

        .view-all-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* ---------------- TENTANG SAYA TAB ---------------- */
        .about-text {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.8;
            margin: 0 0 32px;
        }

        .pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .pill {
            background: #f9fafb;
            border: 1px solid var(--border-light);
            color: #4b5563;
            padding: 8px 20px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 600;
        }

        .pill.active {
            background: var(--primary-soft);
            color: var(--primary-light);
            border-color: var(--primary-border);
        }

        .stat-horizontal-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 0;
        }

        .stat-h-box {
            background: #f9fafb;
            border-radius: var(--radius-md);
            padding: 16px;
            border: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .stat-h-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-light);
            font-size: 16px;
        }

        .stat-h-text p {
            margin: 0;
            font-size: 11px;
            color: var(--text-muted);
        }

        .stat-h-text h4 {
            margin: 4px 0 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            letter-spacing: -0.5px;
        }

        /* ---------------- KEAHLIAN TAB ---------------- */
        .expertise-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .expertise-card {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
        }

        .exp-header {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .exp-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .exp-icon.purple {
            background: var(--primary-soft);
            color: var(--primary-light);
        }

        .exp-icon.green {
            background: #ecfdf5;
            color: #10b981;
        }

        .exp-icon.orange {
            background: #fffbeb;
            color: #f59e0b;
        }

        .exp-title-box h4 {
            margin: 0;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .exp-title-box p {
            margin: 2px 0 0;
            font-size: 12px;
            color: var(--text-muted);
        }

        .exp-progress-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .exp-progress-bar {
            flex-grow: 1;
            height: 6px;
            background: #f3f4f6;
            border-radius: 99px;
            overflow: hidden;
        }

        .exp-progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 99px;
        }

        .exp-percentage {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-dark);
            width: 35px;
            text-align: right;
        }

        /* ---------------- PENGALAMAN TAB ---------------- */
        .timeline {
            position: relative;
            padding-left: 24px;
            margin-top: 12px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 8px;
            bottom: 0;
            width: 1px;
            background: #e5e7eb;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 32px;
            display: grid;
            grid-template-columns: 100px 1fr;
            gap: 24px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-dot {
            position: absolute;
            left: -28px;
            top: 8px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary-light);
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .timeline-date {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            line-height: 1.5;
            padding-top: 4px;
        }

        .timeline-content {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 20px;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
        }

        .timeline-content-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .timeline-role {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .timeline-company {
            font-size: 13px;
            color: var(--text-muted);
            margin: 4px 0 0;
        }

        .timeline-duration {
            font-size: 11px;
            font-weight: 600;
            color: var(--primary-light);
            background: var(--primary-soft);
            padding: 4px 10px;
            border-radius: 99px;
        }

        .timeline-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin: 12px 0 0;
            line-height: 1.6;
        }

        /* ---------------- PENDIDIKAN & SERTIFIKASI TAB ---------------- */
        .edu-cert-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }

        .ec-list-card {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 16px;
            background: #ffffff;
            display: flex;
            gap: 16px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.01);
            transition: all 0.2s ease;
        }

        .ec-list-card:hover {
            border-color: #d1d5db;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .ec-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .ec-icon.gold {
            background: #fff8e1;
            color: #f5b041;
        }

        .ec-icon.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .ec-icon.navy {
            background: #f8fafc;
            color: #475569;
        }

        .ec-icon.orange {
            background: #fff7ed;
            color: #f97316;
        }

        .ec-info {
            flex-grow: 1;
        }

        .ec-info-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
        }

        .ec-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .ec-subtitle {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin: 4px 0 2px;
        }

        .ec-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.5;
        }

        .ec-year {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .ec-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .ec-badge.primary {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .ec-badge.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .ec-badge.green {
            background: #dcfce7;
            color: #166534;
        }

        /* Timeline specific */
        .edu-timeline {
            position: relative;
            padding-left: 24px;
            margin-top: 24px;
        }

        .edu-timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 12px;
            bottom: 0;
            width: 1px;
            background: #e5e7eb;
        }

        .edu-timeline-item {
            position: relative;
            margin-bottom: 24px;
        }

        .edu-timeline-item:last-child {
            margin-bottom: 0;
        }

        .edu-timeline-dot {
            position: absolute;
            left: -29px;
            top: 16px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary-light);
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 2px var(--primary-light);
        }

        .cert-list {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-top: 24px;
        }

        /* ---------------- ULASAN TAB ---------------- */
        .review-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .review-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .review-filters {
            display: flex;
            gap: 12px;
        }

        .filter-select {
            padding: 8px 12px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            font-size: 13px;
            color: var(--text-dark);
            background: #ffffff;
            outline: none;
        }

        .review-list {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .review-card {
            padding: 24px 0;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            gap: 16px;
        }

        .review-card:last-child {
            border-bottom: none;
        }

        .reviewer-img {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
        }

        .review-content {
            flex-grow: 1;
        }

        .review-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }

        .reviewer-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .reviewer-role {
            font-size: 12px;
            color: var(--text-muted);
            margin: 2px 0 0;
        }

        .review-date {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            display: block;
        }

        .review-rating-badge {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .review-rating-badge i {
            color: #facc15;
        }

        .review-text {
            font-size: 13px;
            color: var(--text-dark);
            line-height: 1.6;
            margin: 8px 0 12px;
        }

        .review-event-badge {
            display: inline-block;
            background: var(--primary-soft);
            color: var(--primary-light);
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .review-pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
        }

        .page-btn {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            color: var(--text-muted);
            border: 1px solid transparent;
        }

        .page-btn:hover {
            background: #f9fafb;
        }

        .page-btn.active {
            background: var(--primary-soft);
            color: var(--primary-light);
        }

        /* ---------------- EVENT & COURSE TERBARU ---------------- */
        .event-course-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .ec-card {
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #ffffff;
            display: flex;
            flex-direction: column;
        }

        .ec-img-wrap {
            position: relative;
            height: 160px;
        }

        .ec-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ec-badge-overlay {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            color: #ffffff;
        }

        .ec-badge-overlay.event {
            background: rgba(139, 92, 246, 0.9);
        }

        .ec-badge-overlay.course {
            background: rgba(16, 185, 129, 0.9);
        }

        .ec-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .ec-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0 0 16px;
            line-height: 1.4;
        }

        .ec-meta {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            font-size: 12px;
        }

        .ec-meta span {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 6px 12px;
            border-radius: 8px;
            color: #475569;
            font-weight: 600;
        }

        .ec-meta i {
            font-size: 14px;
        }

        /* ---------------- RESPONSIVE DESIGN ---------------- */
        @media (max-width: 1200px) {
            .top-grid {
                grid-template-columns: 1fr;
            }
            .hero-card {
                gap: 24px;
                padding: 32px;
            }
        }

        @media (max-width: 992px) {
            .main-grid {
                grid-template-columns: 1fr;
            }

            .hero-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .hero-info {
                align-items: center;
            }
            
            .hero-info h1 {
                text-align: center;
            }
            
            .hero-bio {
                text-align: center;
            }

            .hero-contacts {
                justify-content: center;
                flex-wrap: wrap;
            }

            .stat-horizontal-grid,
            .expertise-grid,
            .edu-cert-grid,
            .event-course-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .timeline-item {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .timeline-date {
                padding-top: 0;
            }

            .timeline-dot {
                top: 6px;
                left: -28px;
            }
        }

        @media (max-width: 768px) {
            .hero-info h1 {
                font-size: 26px;
            }
            .hero-bio {
                font-size: 14px;
            }
            
            .stat-horizontal-grid,
            .expertise-grid,
            .edu-cert-grid,
            .event-course-grid {
                grid-template-columns: 1fr;
            }

            .profile-tabs {
                justify-content: flex-start;
                gap: 16px;
                overflow-x: auto;
                padding: 0 16px;
                -webkit-overflow-scrolling: touch;
            }
            
            .profile-tabs::-webkit-scrollbar {
                display: none;
            }

            .left-content > div:not(.profile-tabs) {
                padding: 0 16px 16px 16px !important;
            }

            .content-section {
                padding: 20px !important;
                border-radius: var(--radius-lg);
            }

            .sidebar-card {
                padding: 20px;
                border-radius: var(--radius-lg);
            }
            
            .hero-avatar img {
                width: 120px;
                height: 120px;
            }
            
            .hero-card::before, .hero-card::after {
                width: 200px;
                height: 200px;
            }
            
            .modal-box {
                max-width: 95%;
            }
            
            .activity-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .hero-contacts {
                flex-direction: column;
                gap: 16px;
                align-items: center;
            }

            .hero-meta {
                flex-direction: column;
                gap: 12px;
                align-items: center;
            }

            .hero-card .edit-btn {
                position: relative;
                top: 0;
                right: 0;
                margin-top: 0;
                width: 100%;
                justify-content: center;
                order: -1;
                margin-bottom: 20px;
            }
            
            .hero-card {
                display: flex;
                flex-direction: column;
                gap: 16px;
                padding: 24px;
            }
            
            .ec-card {
                border-radius: var(--radius-md);
            }
            
            .ec-img-wrap {
                height: 140px;
            }
            
            .tab-item {
                font-size: 12px;
                padding: 12px 8px;
            }
            
            .rating-sidebar-big h2 {
                font-size: 32px;
            }
        }

        /* ---------------- MODALS ---------------- */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(17, 24, 39, 0.6);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
            opacity: 1;
        }

        .modal-box {
            background: #ffffff;
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            transform: translateY(20px);
            transition: transform 0.3s ease;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .modal-box>form {
            display: flex;
            flex-direction: column;
            max-height: 100%;
            flex: 1;
            min-height: 0;
        }

        .modal-overlay.active .modal-box {
            transform: translateY(0);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .btn-close-modal {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            transition: background 0.2s;
        }

        .btn-close-modal:hover {
            background: var(--bg-gray);
            color: var(--text-dark);
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
            flex-grow: 1;
        }

        .modal-footer {
            padding: 20px 24px;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-size: 14px;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Tag Input Styles */
        .tag-input-container {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 8px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
            cursor: text;
            background: white;
        }

        .tag-input-container:focus-within {
            border-color: var(--primary);
        }

        .tag-item {
            background: #eff6ff;
            color: #1e3a8a;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
        }

        .tag-item button {
            background: none;
            border: none;
            color: #1e3a8a;
            cursor: pointer;
            font-size: 16px;
            padding: 0;
            line-height: 1;
            display: flex;
            align-items: center;
        }

        .tag-item button:hover {
            color: #dc2626;
        }

        .btn-primary {
            background: var(--primary);
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background: #ffffff;
            color: var(--text-dark);
            border: 1px solid #d1d5db;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-secondary:hover {
            background: var(--bg-gray);
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1024px) {
            .top-grid, .main-grid {
                grid-template-columns: 1fr;
            }
            .hero-card {
                padding: 32px 24px;
            }
            .hero-card-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 24px;
            }
            .hero-avatar-box {
                margin: 0 auto;
            }
            .hero-stats-row {
                justify-content: center;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 768px) {
            .course-list {
                grid-template-columns: 1fr;
            }
            .cert-grid {
                grid-template-columns: 1fr;
            }
            .profile-header-actions {
                flex-direction: column;
                width: 100%;
                margin-top: 16px;
            }
            .profile-header-actions button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="profile-container">

        <!-- TOP SECTION: HERO & ACTIVITY SUMMARY -->
        <div class="top-grid">
            <!-- HERO CARD (LEFT) -->
            <div class="hero-card">
                <button class="edit-btn" onclick="openModal('modal-edit-profil')"><i class="bi bi-pencil"></i> Edit
                    Profil</button>
                <div class="hero-avatar">
                    <img src="{{ $trainer->avatar_url }}" alt="Profile Photo">
                    <div class="edit-photo" onclick="document.getElementById('photo-upload').click()"><i
                            class="bi bi-pencil-fill"></i></div>
                    <form id="avatar-form" action="{{ route('trainer.profile.update') }}" method="POST"
                        enctype="multipart/form-data" style="display: none;">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="name" value="{{ $trainer->name }}">
                        <input type="file" id="photo-upload" name="avatar" accept="image/*"
                            onchange="document.getElementById('avatar-form').submit()">
                    </form>
                </div>
                <div class="hero-info">
                    <h1 id="ui-hero-name">{{ $displayFullName }} @if($isVerifiedTrainer) <i class="bi bi-patch-check-fill"
                    title="Verified"></i> @endif</h1>
                    <span id="ui-hero-role" class="role-badge">{{ $displayRole }}</span>
                    <p id="ui-hero-bio" class="hero-bio">{{ \Illuminate\Support\Str::limit($displayBio, 150) }}</p>
                    <div class="hero-meta">
                        <span><i class="bi bi-geo-alt"></i> <span id="ui-hero-location">{{ $displayLocation }}</span></span>
                        <span><i class="bi bi-calendar3"></i> Bergabung sejak
                            {{ $trainer->created_at ? $trainer->created_at->format('M Y') : 'Jan 2023' }}</span>
                    </div>
                    <div class="hero-contacts">
                        <span><i class="bi bi-envelope"></i> <span id="ui-hero-email">{{ $trainer->email }}</span></span>
                        <span><i class="bi bi-telephone"></i> <span
                                id="ui-hero-phone">{{ $trainer->phone ?: '+62 812 3456 7890' }}</span></span>
                    </div>
                </div>
            </div>

            <!-- ACTIVITY SUMMARY (RIGHT) -->
            <div class="sidebar-card">
                <div class="sidebar-header">
                    <h3 class="sidebar-title">Ringkasan Aktivitas</h3>
                </div>
                <div class="activity-grid">
                    <div class="activity-item">
                        <div class="activity-icon primary"><i class="bi bi-calendar-event"></i></div>
                        <div class="activity-text">
                            <h4>{{ $totalEvents ?? 0 }}</h4>
                            <p>Event Diampu</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon green"><i class="bi bi-play-btn"></i></div>
                        <div class="activity-text">
                            <h4>{{ $totalCourses ?? 0 }}</h4>
                            <p>Course Diampu</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon orange"><i class="bi bi-people"></i></div>
                        <div class="activity-text">
                            <h4>{{ $totalStudents ?? 0 }}</h4>
                            <p>Total Peserta</p>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon blue"><i class="bi bi-person-badge"></i></div>
                        <div class="activity-text">
                            <h4>{{ number_format($averageRating ?? 0, 1) }} <i class="bi bi-star-fill"
                                    style="font-size:12px; color:#facc15;"></i></h4>
                            <p>Rating Pengajar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT AREA -->
        <div class="main-grid">

            <!-- LEFT COLUMN: TAB PANELS -->
            <div class="left-content content-section" style="padding: 0; overflow: hidden;">
                <!-- TABS NAV -->
                <div class="profile-tabs">
                    <a href="#" class="tab-item active" onclick="switchTab(event, 'tab-tentang')">
                        <i class="bi bi-person-vcard"></i> Tentang Saya
                    </a>
                    <a href="#" class="tab-item" onclick="switchTab(event, 'tab-keahlian')">
                        <i class="bi bi-lightning"></i> Keahlian
                    </a>
                    <a href="#" class="tab-item" onclick="switchTab(event, 'tab-pengalaman')">
                        <i class="bi bi-briefcase"></i> Pengalaman
                    </a>
                    <a href="#" class="tab-item" onclick="switchTab(event, 'tab-pendidikan')">
                        <i class="bi bi-mortarboard"></i> Pendidikan & Sertifikasi
                    </a>
                    <a href="#" class="tab-item" onclick="switchTab(event, 'tab-ulasan')">
                        <i class="bi bi-star"></i> Ulasan ({{ $totalFeedbacks ?? 0 }})
                    </a>
                </div>

                <div style="padding: 0 32px 32px 32px;">
                    <!-- TAB: TENTANG SAYA -->
                    <div id="tab-tentang" class="tab-panel active">
                        <div class="section-header-flex">
                            <h3 class="section-title mb-0" style="font-size:18px; text-align:left; text-transform:none;">
                                Tentang Saya</h3>
                            <button class="btn-icon-edit" onclick="openModal('modal-edit-tentang')"><i
                                    class="bi bi-pencil"></i></button>
                        </div>
                        <p id="ui-about-text" class="about-text">{{ $displayBio }}</p>

                        <div class="section-header-flex mb-0 mt-2">
                            <h3 class="section-title mb-0" style="font-size:18px; text-align:left; text-transform:none;">
                                Spesialisasi Saya</h3>
                            <button class="btn-icon-edit" onclick="openModalSpesialisasi()"><i
                                    class="bi bi-pencil"></i></button>
                        </div>
                        <div class="pill-list" id="ui-spesialisasi-list">
                            @if(empty($expertiseTags))
                                <span class="pill active">Leadership</span>
                                <span class="pill active">Team Building</span>
                                <span class="pill active">Communication</span>
                                <span class="pill active">Public Speaking</span>
                                <span class="pill active">Time Management</span>
                            @else
                                @foreach($expertiseTags as $tag)
                                    <span class="pill active">{{ $tag }}</span>
                                @endforeach
                            @endif
                        </div>

                        <div class="stat-horizontal-grid">
                            <div class="stat-h-box">
                                <div class="stat-h-icon"><i class="bi bi-person-workspace"></i></div>
                                <div class="stat-h-text">
                                    <p>Event Selesai</p>
                                    <h4>{{ $completedEventsCount ?? 0 }} Event</h4>
                                </div>
                            </div>
                            <div class="stat-h-box">
                                <div class="stat-h-icon" style="color: #3b82f6;"><i class="bi bi-journal-check"></i></div>
                                <div class="stat-h-text">
                                    <p>Course Selesai</p>
                                    <h4>{{ $completedCoursesCount ?? 0 }} Course</h4>
                                </div>
                            </div>
                            <div class="stat-h-box">
                                <div class="stat-h-icon" style="color: #3b82f6;"><i class="bi bi-star"></i></div>
                                <div class="stat-h-text">
                                    <p>Rata-rata Rating</p>
                                    <h4><i class="bi bi-star-fill" style="font-size:12px; color:#111827;"></i>
                                        {{ number_format($averageRating ?? 0, 1) }} / 5.0</h4>
                                </div>
                            </div>
                        </div>

                        {{-- Data bahasa tidak ada di schema saat ini --}}
                    </div>

                    <!-- TAB: KEAHLIAN -->
                    <div id="tab-keahlian" class="tab-panel">
                        <div class="section-header-flex" style="margin-bottom: 24px;">
                            <div>
                                <h3 class="section-title mb-0"
                                    style="font-size:18px; text-align:left; text-transform:none;">Keahlian Saya</h3>
                                <p class="section-subtitle" style="margin-bottom: 0;">Keahlian yang saya kuasai dan sering
                                    saya terapkan dalam pelatihan.</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-primary" onclick="openAddKeahlian()">
                                <i class="bi bi-plus"></i> Tambah Keahlian
                            </button>
                        </div>

                        <div class="expertise-grid" id="ui-keahlian-list">
                            @foreach($expertiseTags as $index => $skill)
                                @php
                                    $skillName = is_array($skill) ? ($skill['name'] ?? '') : $skill;
                                    $skillPercent = is_array($skill) ? ($skill['percent'] ?? '100') : '100';
                                @endphp
                                <div class="expertise-card">
                                    <div style="position:absolute; top:12px; right:12px; display:flex; gap:4px;">
                                        <button class="btn-icon-edit" style="width:20px; height:20px; font-size:12px;"
                                            onclick="openEditKeahlian({{ $index }}, '{{ addslashes($skillName) }}', '{{ $skillPercent }}')"><i
                                                class="bi bi-pencil"></i></button>
                                    </div>
                                    <div class="exp-header">
                                        <div class="exp-icon primary"><i class="bi bi-check2-circle"></i></div>
                                        <div class="exp-title-box">
                                            <h4 class="skill-name">{{ $skillName }}</h4>
                                            <p>Kompeten</p>
                                        </div>
                                    </div>
                                    <div class="exp-progress-wrap">
                                        <div class="exp-progress-bar">
                                            <div class="exp-progress-fill" style="width: {{ $skillPercent }}%;"></div>
                                        </div>
                                        <span class="exp-percentage skill-percent">{{ $skillPercent }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- TAB: PENGALAMAN -->
                    <div id="tab-pengalaman" class="tab-panel">
                        <div class="section-header-flex">
                            <div>
                                <h3 class="section-title mb-0"
                                    style="font-size:18px; text-align:left; text-transform:none;">Pengalaman Mengajar &
                                    Profesional</h3>
                                <p class="section-subtitle" style="margin-bottom:0;">Pengalaman saya dalam mengajar dan
                                    bekerja di berbagai bidang.</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mb-3">
                            <button class="btn btn-outline-primary" onclick="openAddPengalaman()">
                                <i class="bi bi-plus"></i> Tambah Pengalaman
                            </button>
                        </div>

                        <div class="timeline" id="ui-pengalaman-list">
                            @forelse($trainerExperiences as $index => $exp)
                                @php
                                    $startStr = $exp['start_date'] ?? '';
                                    $endStr = $exp['end_date'] ?? '';
                                    $startLabel = $startStr ? \Carbon\Carbon::parse($startStr)->locale('id')->translatedFormat('M Y') : 'Sekarang';
                                    $endLabel = $endStr ? \Carbon\Carbon::parse($endStr)->locale('id')->translatedFormat('M Y') : 'Sekarang';
                                @endphp
                                <div class="timeline-item">
                                    <div class="timeline-date">{!! $startLabel !!}<br>- {!! $endLabel !!}</div>
                                    <div class="timeline-dot"></div>
                                    <div class="timeline-content" style="position:relative;">
                                        <div style="position:absolute; top:0; right:0;">
                                            <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;"
                                                onclick="openEditPengalaman({{ $index }}, '{{ addslashes($exp['role'] ?? '') }}', '{{ addslashes($exp['company'] ?? '') }}', '{{ $exp['start_date'] ?? '' }}', '{{ $exp['end_date'] ?? '' }}', '{{ addslashes(str_replace("\n", "\\n", $exp['description'] ?? '')) }}')"><i
                                                    class="bi bi-pencil"></i></button>
                                        </div>
                                        <div class="timeline-content-head">
                                            <div>
                                                <h4 class="timeline-role">{{ $exp['role'] ?? '' }}</h4>
                                                <p class="timeline-company">{{ $exp['company'] ?? '' }}</p>
                                            </div>
                                        </div>
                                        <p class="timeline-desc">{{ $exp['description'] ?? '' }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">Belum ada pengalaman yang ditambahkan.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- TAB: PENDIDIKAN & SERTIFIKASI -->
                    <div id="tab-pendidikan" class="tab-panel">
                        <div class="edu-cert-grid">
                            <div style="grid-column: 1 / -1; margin-bottom: 32px;">
                                <div class="section-header-flex">
                                    <h3 class="section-title mb-0"
                                        style="font-size:18px; text-align:left; text-transform:none;">Pendidikan</h3>
                                </div>

                                <div class="d-flex justify-content-end mb-3">
                                    <button class="btn btn-outline-primary" onclick="openAddPendidikan()">
                                        <i class="bi bi-plus"></i> Tambah Pendidikan
                                    </button>
                                </div>

                                <div class="timeline" id="ui-pendidikan-list" style="margin-top: 16px;">
                                    @forelse($trainerEducations as $index => $edu)
                                        <div class="edu-timeline-item">
                                            <div class="edu-timeline-dot"></div>
                                            <div class="ec-list-card" style="position:relative;">
                                                <div style="position:absolute; top:8px; right:8px;">
                                                    <button class="btn-icon-edit"
                                                        style="width:24px; height:24px; font-size:12px;"
                                                        onclick="openEditPendidikan({{ $index }}, '{{ addslashes($edu['institution'] ?? '') }}', '{{ addslashes($edu['degree'] ?? '') }}', '{{ $edu['start_year'] ?? '' }}', '{{ $edu['end_year'] ?? '' }}')"><i
                                                            class="bi bi-pencil"></i></button>
                                                </div>
                                                <div class="ec-icon gold"><i class="bi bi-bank"></i></div>
                                                <div class="ec-info">
                                                    <div class="ec-info-head">
                                                        <div>
                                                            <h4 class="ec-title">{{ $edu['institution'] ?? '' }}</h4>
                                                            <div class="ec-year">{{ $edu['start_year'] ?? '' }} -
                                                                {{ $edu['end_year'] ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ec-subtitle" style="margin-top:6px;">{{ $edu['degree'] ?? '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">Belum ada data pendidikan.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div style="grid-column: 1 / -1;">
                                <div class="section-header-flex">
                                    <h3 class="section-title mb-0"
                                        style="font-size:18px; text-align:left; text-transform:none;">Sertifikasi
                                    </h3>
                                </div>

                                <div class="d-flex justify-content-end mb-3">
                                    <button class="btn btn-outline-primary" onclick="openAddSertifikasi()">
                                        <i class="bi bi-plus"></i> Tambah Sertifikasi
                                    </button>
                                </div>

                                <div class="cert-list" id="ui-sertifikasi-list"
                                    style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 16px; margin-top:16px;">

                                    {{-- Manual Certifications --}}
                                    @foreach($trainerManualCertifications as $index => $cert)
                                        <div class="ec-list-card" style="position:relative;">
                                            <div style="position:absolute; top:8px; right:8px;">
                                                <button class="btn-icon-edit" style="width:24px; height:24px; font-size:12px;"
                                                    onclick="openEditSertifikasi({{ $index }}, '{{ addslashes($cert['name'] ?? '') }}', '{{ addslashes($cert['issuer'] ?? '') }}', '{{ $cert['start_date'] ?? '' }}', '{{ $cert['end_date'] ?? '' }}')"><i
                                                        class="bi bi-pencil"></i></button>
                                            </div>
                                            <div class="ec-icon navy"><i class="bi bi-patch-check-fill"></i></div>
                                            <div class="ec-info">
                                                <div class="ec-info-head">
                                                    <div>
                                                        <h4 class="ec-title">{{ $cert['name'] ?? '' }}</h4>
                                                        <div class="ec-year">
                                                            {{ \Carbon\Carbon::parse($cert['start_date'])->format('Y') }}
                                                        </div>
                                                    </div>
                                                    <span class="ec-badge green">Manual</span>
                                                </div>
                                                <div class="ec-subtitle" style="margin-top:6px;">{{ $cert['issuer'] ?? '' }}
                                                </div>
                                                <p class="ec-desc">
                                                    @if(!empty($cert['end_date']))
                                                        Masa Berlaku:
                                                        {{ \Carbon\Carbon::parse($cert['start_date'])->locale('id')->translatedFormat('M Y') }}
                                                        -
                                                        {{ \Carbon\Carbon::parse($cert['end_date'])->locale('id')->translatedFormat('M Y') }}
                                                    @else
                                                        Berlaku sejak:
                                                        {{ \Carbon\Carbon::parse($cert['start_date'])->locale('id')->translatedFormat('M Y') }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- System Certificates --}}
                                    @foreach($trainerCertificates as $cert)
                                        <div class="ec-list-card" style="position:relative;">
                                            <div class="ec-icon navy"><i class="bi bi-patch-check-fill"></i></div>
                                            <div class="ec-info">
                                                <div class="ec-info-head">
                                                    <div>
                                                        <h4 class="ec-title">
                                                            {{ $cert->certifiable->title ?? $cert->certifiable->name ?? 'Sertifikat Trainer' }}
                                                        </h4>
                                                        <div class="ec-year">
                                                            {{ optional($cert->issued_at)->format('Y') ?? now()->format('Y') }}
                                                        </div>
                                                    </div>
                                                    <span class="ec-badge green">Otomatis</span>
                                                </div>
                                                <div class="ec-subtitle" style="margin-top:6px;">idSpora</div>
                                                <p class="ec-desc">No: {{ $cert->certificate_number ?? '-' }}</p>
                                            </div>
                                        </div>
                                    @endforeach

                                    @if(empty($trainerManualCertifications) && $trainerCertificates->isEmpty())
                                        <p class="text-muted" style="grid-column: 1/-1;">Belum ada sertifikasi.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB: ULASAN -->
                    <div id="tab-ulasan" class="tab-panel">
                        <div class="review-header-flex"
                            style="display: flex; align-items: center; justify-content: space-between;">

                            <!-- Bagian Kiri: Judul dan Bintang -->
                            <h3 class="review-title" style="margin: 0; display: flex; align-items: center; gap: 8px;">
                                Ulasan Peserta
                                <span
                                    style="font-size:18px; color:var(--text-dark); display: flex; align-items: center; gap: 4px;">
                                    {{ number_format($averageRating ?? 0, 1) }}
                                    <i class="bi bi-star-fill" style="color:#facc15; font-size:14px;"></i>
                                </span>
                                <span style="font-size:13px; color:var(--text-muted); font-weight:normal;">
                                    ({{ $totalFeedbacks ?? 0 }} ulasan)
                                </span>
                            </h3>

                            <!-- Bagian Kanan: Filter Dropdown -->
                            <div class="review-filters" style="display: flex; gap: 10px;">
                                <select class="filter-select">
                                    <option>Semua Rating</option>
                                    <option>5 Bintang</option>
                                </select>
                                <select class="filter-select">
                                    <option>Terbaru</option>
                                    <option>Terlama</option>
                                </select>
                            </div>
                        </div>

                        <div class="review-list">
                            @forelse($recentFeedbacks as $feedback)
                                <div class="review-card">
                                    <img src="{{ $feedback->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($feedback->user->name ?? 'User') . '&background=random' }}"
                                        alt="Avatar" class="reviewer-img">
                                    <div class="review-content">
                                        <div class="review-head">
                                            <div>
                                                <h4 class="reviewer-name">{{ $feedback->user->name ?? 'Anonim' }}</h4>
                                                <p class="reviewer-role">Peserta</p>
                                                <span
                                                    class="review-date">{{ optional($feedback->created_at)->format('d M Y') }}</span>
                                            </div>
                                            <div style="text-align: right;">
                                                <div class="review-rating-badge">
                                                    @php $rating = $feedback->rating ?? $feedback->speaker_rating ?? 0; @endphp
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $rating)
                                                            <i class="bi bi-star-fill"></i>
                                                        @else
                                                            <i class="bi bi-star" style="color: #e5e7eb;"></i>
                                                        @endif
                                                    @endfor
                                                    <span>{{ number_format($rating, 1) }}</span>
                                                </div>
                                                <i class="bi bi-three-dots-vertical"
                                                    style="color:var(--text-muted); cursor:pointer; margin-top:4px; display:inline-block;"></i>
                                            </div>
                                        </div>
                                        <p class="review-text">{{ $feedback->comment ?? 'Tidak ada komentar.' }}</p>
                                        @if($feedback->event)
                                            <span
                                                class="review-event-badge">{{ $feedback->event->title ?? 'Training Session' }}</span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div style="padding: 24px; text-align: center; color: var(--text-muted);">
                                    Belum ada ulasan.
                                </div>
                            @endforelse
                        </div>

                        @if(count($recentFeedbacks) > 0)
                            <div class="review-pagination">
                                <div class="page-btn"><i class="bi bi-chevron-left"></i></div>
                                <div class="page-btn active">1</div>
                                <div class="page-btn"><i class="bi bi-chevron-right"></i></div>
                            </div>
                        @endif
                    </div>
                </div> <!-- END PADDING WRAPPER -->

            </div> <!-- END LEFT COLUMN -->

            <!-- RIGHT COLUMN: SIDEBARS -->
            <div class="right-content">

                <!-- PENCAPAIAN SIDEBAR (Visible by default except on Ulasan) -->
                <div id="sidebar-pencapaian" class="sidebar-card">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">Pencapaian</h3>
                    </div>
                    <div class="achievement-list">
                        @if(($averageRating ?? 0) >= 4.5)
                            <div class="achievement-item">
                                <div class="achieve-icon primary"><i class="bi bi-trophy-fill"></i></div>
                                <div class="achieve-info">
                                    <div class="achieve-info-head">
                                        <h4 class="achieve-title">Top Rated Trainer</h4>
                                    </div>
                                    <p class="achieve-desc">Mendapatkan rating sangat baik
                                        ({{ number_format($averageRating, 1) }}) dari peserta</p>
                                </div>
                            </div>
                        @endif

                        @if(($totalStudents ?? 0) >= 50)
                            <div class="achievement-item">
                                <div class="achieve-icon green"><i class="bi bi-patch-check-fill"></i></div>
                                <div class="achieve-info">
                                    <div class="achieve-info-head">
                                        <h4 class="achieve-title">{{ $totalStudents }}+ Peserta</h4>
                                    </div>
                                    <p class="achieve-desc">Telah mengajar dan membimbing banyak peserta</p>
                                </div>
                            </div>
                        @endif

                        @if(($completedEventsCount ?? 0) >= 10)
                            <div class="achievement-item">
                                <div class="achieve-icon gold"><i class="bi bi-star-fill"></i></div>
                                <div class="achieve-info">
                                    <div class="achieve-info-head">
                                        <h4 class="achieve-title">Experienced Trainer</h4>
                                    </div>
                                    <p class="achieve-desc">Telah menyelesaikan {{ $completedEventsCount }} event dengan sukses
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if(($averageRating ?? 0) < 4.5 && ($totalStudents ?? 0) < 50 && ($completedEventsCount ?? 0) < 10)
                            <div class="achievement-item">
                                <div class="achieve-info">
                                    <p class="achieve-desc text-muted">Belum ada pencapaian. Terus aktif mengajar untuk
                                        mendapatkan badge pencapaian!</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RINGKASAN PENILAIAN SIDEBAR (Visible ONLY on Ulasan tab) -->
                <div id="sidebar-penilaian" class="sidebar-card" style="display: none;">
                    <div class="sidebar-header">
                        <h3 class="sidebar-title">Ringkasan Penilaian</h3>
                    </div>

                    <div class="rating-sidebar-big">
                        <h2>{{ number_format($averageRating ?? 0, 1) }} <i class="bi bi-star-fill"></i></h2>
                        <p>Dari {{ $totalFeedbacks ?? 0 }} ulasan</p>
                    </div>

                    <div class="rating-bars">
                        <div class="rating-bar-row">
                            <span>5 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap">
                                <div class="rating-bar-fill" style="width: 75%;"></div>
                            </div>
                            <span class="rating-count">36</span>
                            <span class="rating-pct">(75%)</span>
                        </div>
                        <div class="rating-bar-row">
                            <span>4 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap">
                                <div class="rating-bar-fill" style="width: 21%;"></div>
                            </div>
                            <span class="rating-count">10</span>
                            <span class="rating-pct">(21%)</span>
                        </div>
                        <div class="rating-bar-row">
                            <span>3 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap">
                                <div class="rating-bar-fill" style="width: 4%;"></div>
                            </div>
                            <span class="rating-count">2</span>
                            <span class="rating-pct">(4%)</span>
                        </div>
                        <div class="rating-bar-row">
                            <span>2 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap">
                                <div class="rating-bar-fill" style="width: 0%;"></div>
                            </div>
                            <span class="rating-count">0</span>
                            <span class="rating-pct">(0%)</span>
                        </div>
                        <div class="rating-bar-row">
                            <span>1 <i class="bi bi-star-fill" style="color:#facc15"></i></span>
                            <div class="rating-bar-wrap">
                                <div class="rating-bar-fill" style="width: 0%;"></div>
                            </div>
                            <span class="rating-count">0</span>
                            <span class="rating-pct">(0%)</span>
                        </div>
                    </div>

                    <div class="rating-aspects">
                        <h4 class="aspect-title">Aspek Penilaian</h4>
                        <div class="aspect-row">
                            <span><i class="bi bi-journal-text"></i> Penyampaian Materi</span>
                            <span>4.9</span>
                        </div>
                        <div class="aspect-row">
                            <span><i class="bi bi-book"></i> Penguasaan Materi</span>
                            <span>4.8</span>
                        </div>
                        <div class="aspect-row">
                            <span><i class="bi bi-people"></i> Interaktivitas</span>
                            <span>4.8</span>
                        </div>
                        <div class="aspect-row">
                            <span><i class="bi bi-lightbulb"></i> Manfaat & Aplikasi</span>
                            <span>4.7</span>
                        </div>
                    </div>

                    <button class="btn-write-review">Tulis Ulasan</button>
                </div>

            </div> <!-- END RIGHT COLUMN -->

        </div> <!-- END MAIN GRID -->

        <!-- EVENT & COURSE TERBARU (Bottom section) -->
        <div class="content-section" style="margin-bottom: 0; padding: 20px 32px">
            <div class="section-header-flex">
                <h3 class="section-title mb-3">Event dan Course Terbaru</h3>
            </div>

            <div class="event-course-grid">
                @php
                    $mixedItems = collect();
                    foreach ($upcomingEvents as $event) {
                        $img = null;
                        if (!empty($event->image_url))
                            $img = $event->image_url;
                        elseif (!empty($event->thumbnail_url))
                            $img = $event->thumbnail_url;
                        elseif (!empty($event->thumbnail))
                            $img = \Illuminate\Support\Facades\Storage::url($event->thumbnail);
                        else
                            $img = 'https://ui-avatars.com/api/?name=' . urlencode($event->title) . '&background=1e3a8a&color=fff';

                        $mixedItems->push((object) [
                            'type' => 'EVENT',
                            'title' => $event->title,
                            'date' => $event->event_date ? \Carbon\Carbon::parse($event->event_date)->locale('id')->translatedFormat('d M Y') : '-',
                            'participants' => $event->participants_count ?? 0,
                            'image' => $img,
                            'url' => route('trainer.events.show', $event->id),
                            'meta_icon' => 'bi-calendar3',
                            'badge_color' => '#10b981'
                        ]);
                    }
                    foreach ($courses->sortByDesc('created_at')->take(3) as $course) {
                        $img = null;
                        if (!empty($course->card_thumbnail_url))
                            $img = $course->card_thumbnail_url;
                        elseif (!empty($course->thumbnail_url))
                            $img = $course->thumbnail_url;
                        elseif (!empty($course->thumbnail))
                            $img = \Illuminate\Support\Facades\Storage::url($course->thumbnail);
                        else
                            $img = 'https://ui-avatars.com/api/?name=' . urlencode($course->name) . '&background=3b82f6&color=fff';

                        $mixedItems->push((object) [
                            'type' => 'COURSE',
                            'title' => $course->name,
                            'date' => number_format($course->reviews_avg_rating ?? 0, 1) . ' Rating',
                            'participants' => $course->active_enrollments_count ?? 0,
                            'image' => $img,
                            'url' => route('trainer.detail-course', $course->id),
                            'meta_icon' => 'bi-star-fill',
                            'badge_color' => '#3b82f6'
                        ]);
                    }
                    $displayItems = $mixedItems->take(3);
                @endphp

                @forelse($displayItems as $item)
                    <a href="{{ $item->url }}" style="text-decoration:none; color:inherit; display:block;">
                        <div class="ec-card" style="transition: transform 0.2s, box-shadow 0.2s; height:100%;">
                            <div class="ec-img-wrap" style="position:relative;">
                                <span class="ec-badge-overlay {{ strtolower($item->type) }}"
                                    style="background:{{ $item->badge_color }}; color:white; padding:4px 8px; border-radius:4px; font-size:10px; font-weight:bold; position:absolute; top:8px; left:8px; z-index:1;">{{ $item->type }}</span>
                                <img src="{{ $item->image }}" alt="{{ $item->type }}" class="ec-img"
                                    style="width:100%; height:140px; object-fit:cover; border-radius:12px 12px 0 0; background:#f3f4f6;">
                            </div>
                            <div class="ec-body" style="padding:16px;">
                                <h4 class="ec-title" style="margin:0 0 8px; font-size:14px; font-weight:700;">
                                    {{ Str::limit($item->title, 40) }}
                                </h4>
                                <div class="ec-meta"
                                    style="display:flex; justify-content:space-between; font-size:12px; color:var(--text-muted);">
                                    <span><i class="bi {{ $item->meta_icon }}" @if($item->meta_icon == 'bi-star-fill')
                                    style="color:#facc15;" @endif></i> {{ $item->date }}</span>
                                    <span><i class="bi bi-people"></i> {{ $item->participants }} Peserta</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <p class="text-muted" style="grid-column: 1 / -1; padding: 20px 0;">Belum ada event atau course.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- ---------------- HTML MODALS ---------------- -->

    <!-- Modal Edit Profil -->
    <div id="modal-edit-profil" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-profil')">
        <div class="modal-box">
            <form action="{{ route('trainer.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h3 class="modal-title">Edit Profil Utama</h3>
                    <button type="button" class="btn-close-modal" onclick="closeModal('modal-edit-profil')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ $trainer->name }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jabatan / Peran</label>
                        <input type="text" name="profession" class="form-control" value="{{ $trainer->profession }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Institusi</label>
                        <input type="text" name="institution" class="form-control" value="{{ $trainer->institution }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bio Singkat</label>
                        <textarea name="bio" class="form-control" rows="3">{{ $trainer->bio }}</textarea>
                    </div>
                    <div style="display:flex; flex-wrap:wrap; gap:16px;">
                        <div class="form-group" style="flex:1; min-width:200px;">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $trainer->email }}" readonly
                                style="background-color: #f3f4f6; cursor: not-allowed;"
                                title="Email tidak dapat diubah di sini">
                        </div>
                        <div class="form-group" style="flex:1; min-width:200px;">
                            <label class="form-label">Nomor Telepon</label>
                            <input type="text" name="phone" class="form-control" value="{{ $trainer->phone }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer"
                    style="display: flex; flex-direction: row; justify-content: flex-end; gap: 12px; flex-wrap: nowrap;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-profil')"
                        style="flex: 1; white-space: nowrap;">Batal</button>
                    <button type="submit" class="btn-primary" style="flex: 1; white-space: nowrap;">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Tentang Saya -->
    <div id="modal-edit-tentang" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-tentang')">
        <div class="modal-box">
            <form action="{{ route('trainer.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $trainer->name }}">
                <div class="modal-header">
                    <h3 class="modal-title">Edit Tentang Saya</h3>
                    <button type="button" class="btn-close-modal"
                        onclick="closeModal('modal-edit-tentang')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Deskripsi Tentang Saya</label>
                        <textarea name="bio" class="form-control" rows="5">{{ $trainer->bio }}</textarea>
                    </div>
                </div>
                <div class="modal-footer"
                    style="display: flex; flex-direction: row; justify-content: flex-end; gap: 12px; flex-wrap: nowrap;">
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-tentang')"
                        style="flex: 1; white-space: nowrap;">Batal</button>
                    <button type="submit" class="btn-primary" style="flex: 1; white-space: nowrap;">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Spesialisasi -->
    <div id="modal-edit-spesialisasi" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-spesialisasi')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Spesialisasi</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-spesialisasi')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Spesialisasi</label>
                    <div class="tag-input-container" onclick="document.getElementById('input-tag-spesialisasi').focus()">
                        <div id="tag-list-spesialisasi" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
                        <input type="text" id="input-tag-spesialisasi" placeholder="Ketik spesialisasi lalu tekan Enter..."
                            style="border:none; outline:none; flex:1; min-width:120px;"
                            onkeydown="handleTagInput(event, 'spesialisasi')">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-spesialisasi')">Batal</button>
                <button class="btn-primary" onclick="saveSpesialisasi()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Bahasa -->
    <div id="modal-edit-bahasa" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-bahasa')">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Bahasa</h3>
                <button class="btn-close-modal" onclick="closeModal('modal-edit-bahasa')">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Bahasa</label>
                    <div class="tag-input-container" onclick="document.getElementById('input-tag-bahasa').focus()">
                        <div id="tag-list-bahasa" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
                        <input type="text" id="input-tag-bahasa" placeholder="Ketik bahasa lalu tekan Enter..."
                            style="border:none; outline:none; flex:1; min-width:120px;"
                            onkeydown="handleTagInput(event, 'bahasa')">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('modal-edit-bahasa')">Batal</button>
                <button class="btn-primary" onclick="saveBahasa()">Simpan Perubahan</button>
            </div>
        </div>
    </div>

    <!-- Modal Keahlian -->
    <div id="modal-edit-keahlian" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-keahlian')">
        <div class="modal-box">
            <form id="form-edit-keahlian" action="{{ route('trainer.profile.list.update') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="trainer_skills">
                <input type="hidden" name="action" id="input-keahlian-action" value="add">
                <input type="hidden" name="index" id="input-keahlian-index" value="">

                <div class="modal-header">
                    <h3 class="modal-title" id="title-edit-keahlian">Tambah Keahlian</h3>
                    <button type="button" class="btn-close-modal"
                        onclick="closeModal('modal-edit-keahlian')">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Keahlian Baru</label>
                        <input type="text" name="name" id="input-keahlian-nama" class="form-control"
                            placeholder="Contoh: Problem Solving" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tingkat Penguasaan (%)</label>
                        <input type="number" name="percent" id="input-keahlian-persen" class="form-control" placeholder="85"
                            min="1" max="100" required>
                    </div>
                </div>
                <div class="modal-footer"
                    style="display: flex; flex-direction: row; justify-content: flex-end; gap: 12px; flex-wrap: nowrap;">
                    <button type="button" id="btn-delete-keahlian" class="btn-danger"
                        style="display:none; white-space: nowrap;" onclick="deleteKeahlian()">Hapus</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-keahlian')"
                        style="flex: 1; white-space: nowrap;">Batal</button>
                    <button type="submit" class="btn-primary" style="flex: 1; white-space: nowrap;">Simpan
                        Perubahan</button>
                </div>
            </form>

            <form id="form-delete-keahlian" action="{{ route('trainer.profile.list.update') }}" method="POST"
                style="display:none;">
                @csrf
                <input type="hidden" name="type" value="trainer_skills">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="index" id="input-keahlian-delete-index" value="">
            </form>
        </div>
    </div>

    <!-- Modal Pengalaman -->
    <div id="modal-edit-pengalaman" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-pengalaman')">
        <div class="modal-box">
            <form id="form-edit-pengalaman" action="{{ route('trainer.profile.list.update') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="trainer_experiences">
                <input type="hidden" name="action" id="input-pengalaman-action" value="add">
                <input type="hidden" name="index" id="input-pengalaman-index" value="">

                <div class="modal-header">
                    <h3 class="modal-title" id="title-edit-pengalaman">Tambah Pengalaman</h3>
                    <button type="button" class="btn-close-modal"
                        onclick="closeModal('modal-edit-pengalaman')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Jabatan / Posisi</label>
                        <input type="text" name="role" id="input-pengalaman-jabatan" class="form-control"
                            placeholder="Contoh: Senior Trainer" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Perusahaan / Organisasi</label>
                        <input type="text" name="company" id="input-pengalaman-perusahaan" class="form-control"
                            placeholder="Contoh: PT Sukses Selalu" required>
                    </div>
                    <div style="display:flex; gap:16px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Bulan & Tahun Mulai</label>
                            <input type="month" name="start_date" id="input-pengalaman-mulai" class="form-control" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Bulan & Tahun Selesai</label>
                            <input type="month" name="end_date" id="input-pengalaman-selesai" class="form-control">
                            <small style="color:var(--text-muted); font-size:12px;">Kosongkan jika masih bekerja di
                                sini</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Deskripsi Pekerjaan</label>
                        <textarea name="description" id="input-pengalaman-deskripsi" class="form-control"
                            placeholder="Jelaskan peran Anda..." rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer"
                    style="display: flex; flex-direction: row; justify-content: flex-end; gap: 12px; flex-wrap: nowrap;">
                    <button type="button" id="btn-delete-pengalaman" class="btn-danger"
                        style="display:none; white-space: nowrap;" onclick="deletePengalaman()">Hapus</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-pengalaman')"
                        style="flex: 1; white-space: nowrap;">Batal</button>
                    <button type="submit" class="btn-primary" style="flex: 1; white-space: nowrap;">Simpan
                        Perubahan</button>
                </div>
            </form>

            <form id="form-delete-pengalaman" action="{{ route('trainer.profile.list.update') }}" method="POST"
                style="display:none;">
                @csrf
                <input type="hidden" name="type" value="trainer_experiences">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="index" id="input-pengalaman-delete-index" value="">
            </form>
        </div>
    </div>

    <!-- Modal Pendidikan -->
    <div id="modal-edit-pendidikan" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-pendidikan')">
        <div class="modal-box">
            <form id="form-edit-pendidikan" action="{{ route('trainer.profile.list.update') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="trainer_educations">
                <input type="hidden" name="action" id="input-pendidikan-action" value="add">
                <input type="hidden" name="index" id="input-pendidikan-index" value="">

                <div class="modal-header">
                    <h3 class="modal-title" id="title-edit-pendidikan">Tambah Pendidikan</h3>
                    <button type="button" class="btn-close-modal"
                        onclick="closeModal('modal-edit-pendidikan')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Institusi / Universitas</label>
                        <input type="text" name="institution" id="input-pendidikan-kampus" class="form-control"
                            placeholder="Contoh: Universitas Indonesia" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Gelar / Tingkat / Jurusan</label>
                        <input type="text" name="degree" id="input-pendidikan-gelar" class="form-control"
                            placeholder="Contoh: S2 Magister Manajemen" required>
                    </div>
                    <div style="display:flex; gap:16px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Tahun Masuk</label>
                            <input type="number" name="start_year" id="input-pendidikan-mulai" class="form-control"
                                placeholder="YYYY" min="1950" max="2050" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Tahun Lulus</label>
                            <input type="number" name="end_year" id="input-pendidikan-selesai" class="form-control"
                                placeholder="YYYY" min="1950" max="2050">
                        </div>
                    </div>
                </div>
                <div class="modal-footer"
                    style="display: flex; flex-direction: row; justify-content: flex-end; gap: 12px; flex-wrap: nowrap;">
                    <button type="button" id="btn-delete-pendidikan" class="btn-danger"
                        style="display:none; white-space: nowrap;" onclick="deletePendidikan()">Hapus</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-pendidikan')"
                        style="flex: 1; white-space: nowrap;">Batal</button>
                    <button type="submit" class="btn-primary" style="flex: 1; white-space: nowrap;">Simpan
                        Perubahan</button>
                </div>
            </form>

            <form id="form-delete-pendidikan" action="{{ route('trainer.profile.list.update') }}" method="POST"
                style="display:none;">
                @csrf
                <input type="hidden" name="type" value="trainer_educations">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="index" id="input-pendidikan-delete-index" value="">
            </form>
        </div>
    </div>

    <!-- Modal Sertifikasi -->
    <div id="modal-edit-sertifikasi" class="modal-overlay" onclick="closeModalOutside(event, 'modal-edit-sertifikasi')">
        <div class="modal-box">
            <form id="form-edit-sertifikasi" action="{{ route('trainer.profile.list.update') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="trainer_certifications">
                <input type="hidden" name="action" id="input-sertifikasi-action" value="add">
                <input type="hidden" name="index" id="input-sertifikasi-index" value="">

                <div class="modal-header">
                    <h3 class="modal-title" id="title-edit-sertifikasi">Tambah Sertifikasi</h3>
                    <button type="button" class="btn-close-modal"
                        onclick="closeModal('modal-edit-sertifikasi')">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nama Sertifikasi</label>
                        <input type="text" name="name" id="input-sertifikasi-nama" class="form-control"
                            placeholder="Contoh: AWS Certified Solutions Architect" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Organisasi Penerbit</label>
                        <input type="text" name="issuer" id="input-sertifikasi-penerbit" class="form-control"
                            placeholder="Contoh: Amazon Web Services" required>
                    </div>
                    <div style="display:flex; gap:16px;">
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Bulan & Tahun Terbit</label>
                            <input type="month" name="start_date" id="input-sertifikasi-mulai" class="form-control"
                                required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label class="form-label">Bulan & Tahun Kedaluwarsa</label>
                            <input type="month" name="end_date" id="input-sertifikasi-selesai" class="form-control">
                            <small style="color:var(--text-muted); font-size:12px;">Kosongkan jika sertifikasi berlaku
                                seumur hidup</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"
                    style="display: flex; flex-direction: row; justify-content: flex-end; gap: 12px; flex-wrap: nowrap;">
                    <button type="button" id="btn-delete-sertifikasi" class="btn-danger"
                        style="display:none; white-space: nowrap;" onclick="deleteSertifikasi()">Hapus</button>
                    <button type="button" class="btn-secondary" onclick="closeModal('modal-edit-sertifikasi')"
                        style="flex: 1; white-space: nowrap;">Batal</button>
                    <button type="submit" class="btn-primary" style="flex: 1; white-space: nowrap;">Simpan
                        Perubahan</button>
                </div>
            </form>

            <form id="form-delete-sertifikasi" action="{{ route('trainer.profile.list.update') }}" method="POST"
                style="display:none;">
                @csrf
                <input type="hidden" name="type" value="trainer_certifications">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="index" id="input-sertifikasi-delete-index" value="">
            </form>
        </div>
    </div>

    <!-- Script for Tab Switching & Sidebar Toggling & Modals -->
    <script>
        // Modal Logic
        function openModal(modalId, title = '') {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                if (title && document.getElementById('modal-section-title')) {
                    document.getElementById('modal-section-title').innerText = 'Edit ' + title;
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        function closeModalOutside(event, modalId) {
            if (event.target.id === modalId) {
                closeModal(modalId);
            }
        }

        // --- Advanced Mock Save Functions for Simulation ---
        let currentEditCard = null;

        function saveHeroProfil() {
            if (document.getElementById('input-hero-name')) {
                document.getElementById('ui-hero-name').innerHTML = document.getElementById('input-hero-name').value + ' @if($isVerifiedTrainer) <i class="bi bi-patch-check-fill" title="Verified"></i> @endif';
                document.getElementById('ui-hero-role').innerText = document.getElementById('input-hero-role').value;
                document.getElementById('ui-hero-location').innerText = document.getElementById('input-hero-location').value;
                document.getElementById('ui-hero-bio').innerText = document.getElementById('input-hero-bio').value;
                document.getElementById('ui-hero-email').innerText = document.getElementById('input-hero-email').value;
                document.getElementById('ui-hero-phone').innerText = document.getElementById('input-hero-phone').value;
            }
            closeModal('modal-edit-profil');
        }

        function saveTentang() {
            if (document.getElementById('input-tentang-bio')) {
                document.getElementById('ui-about-text').innerText = document.getElementById('input-tentang-bio').value;
            }
            closeModal('modal-edit-tentang');
        }

        let activeTags = { spesialisasi: [], bahasa: [] };

        function renderTags(type) {
            const list = document.getElementById(`tag-list-${type}`);
            if (list) {
                list.innerHTML = '';
                activeTags[type].forEach((tag, idx) => {
                    list.innerHTML += `<div class="tag-item"><span>${tag}</span><button type="button" onclick="removeTag('${type}', ${idx})">&times;</button></div>`;
                });
            }
        }

        function handleTagInput(event, type) {
            if (event.key === 'Enter') {
                event.preventDefault();
                const val = event.target.value.trim();
                if (val && !activeTags[type].includes(val)) {
                    activeTags[type].push(val);
                    renderTags(type);
                }
                event.target.value = '';
            }
        }

        function removeTag(type, idx) {
            activeTags[type].splice(idx, 1);
            renderTags(type);
        }

        function openModalSpesialisasi() {
            const container = document.getElementById('ui-spesialisasi-list');
            activeTags.spesialisasi = Array.from(container.querySelectorAll('.pill')).map(p => p.innerText);
            renderTags('spesialisasi');
            openModal('modal-edit-spesialisasi');
        }

        function saveSpesialisasi() {
            const container = document.getElementById('ui-spesialisasi-list');
            if (container) {
                container.innerHTML = '';
                activeTags.spesialisasi.forEach(tag => {
                    container.innerHTML += `<span class="pill active">${tag}</span>`;
                });
            }
            closeModal('modal-edit-spesialisasi');
        }

        function openModalBahasa() {
            const container = document.getElementById('ui-bahasa-list');
            activeTags.bahasa = Array.from(container.querySelectorAll('.pill')).map(p => p.innerText);
            renderTags('bahasa');
            openModal('modal-edit-bahasa');
        }

        function saveBahasa() {
            const container = document.getElementById('ui-bahasa-list');
            if (container) {
                container.innerHTML = '';
                activeTags.bahasa.forEach(tag => {
                    container.innerHTML += `<span class="pill">${tag}</span>`;
                });
            }
            closeModal('modal-edit-bahasa');
        }

        // Keahlian
        function openAddKeahlian() {
            document.getElementById('form-edit-keahlian').reset();
            document.getElementById('input-keahlian-action').value = 'add';
            document.getElementById('input-keahlian-index').value = '';
            document.getElementById('title-edit-keahlian').innerText = 'Tambah Keahlian';
            document.getElementById('btn-delete-keahlian').style.display = 'none';
            openModal('modal-edit-keahlian');
        }
        function openEditKeahlian(index, nama, persen) {
            document.getElementById('input-keahlian-nama').value = nama;
            document.getElementById('input-keahlian-persen').value = persen;
            document.getElementById('input-keahlian-action').value = 'edit';
            document.getElementById('input-keahlian-index').value = index;
            document.getElementById('title-edit-keahlian').innerText = 'Edit Keahlian';
            document.getElementById('btn-delete-keahlian').style.display = 'block';
            openModal('modal-edit-keahlian');
        }
        function deleteKeahlian() {
            if (confirm('Apakah Anda yakin ingin menghapus keahlian ini?')) {
                const index = document.getElementById('input-keahlian-index').value;
                document.getElementById('input-keahlian-delete-index').value = index;
                document.getElementById('form-delete-keahlian').submit();
            }
        }

        // Utility Functions for Dates
        function formatMonthString(val) {
            if (!val) return 'Sekarang';
            const parts = val.split('-');
            if (parts.length !== 2) return val;
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${months[parseInt(parts[1]) - 1]} ${parts[0]}`;
        }
        function parseMonthString(str) {
            if (!str || str.toLowerCase().includes('sekarang')) return '';
            str = str.replace('-', '').trim();
            const parts = str.split(' ');
            if (parts.length !== 2) return '';
            const months = { 'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04', 'Mei': '05', 'Jun': '06', 'Jul': '07', 'Ags': '08', 'Sep': '09', 'Okt': '10', 'Nov': '11', 'Des': '12' };
            const mm = months[parts[0]] || '01';
            return `${parts[1]}-${mm}`;
        }

        // Pengalaman
        function openAddPengalaman() {
            document.getElementById('form-edit-pengalaman').reset();
            document.getElementById('input-pengalaman-action').value = 'add';
            document.getElementById('input-pengalaman-index').value = '';
            document.getElementById('title-edit-pengalaman').innerText = 'Tambah Pengalaman';
            document.getElementById('btn-delete-pengalaman').style.display = 'none';
            openModal('modal-edit-pengalaman');
        }
        function openEditPengalaman(index, role, company, startDate, endDate, description) {
            document.getElementById('input-pengalaman-jabatan').value = role;
            document.getElementById('input-pengalaman-perusahaan').value = company;
            document.getElementById('input-pengalaman-mulai').value = startDate;
            document.getElementById('input-pengalaman-selesai').value = endDate;
            document.getElementById('input-pengalaman-deskripsi').value = description;

            document.getElementById('input-pengalaman-action').value = 'edit';
            document.getElementById('input-pengalaman-index').value = index;
            document.getElementById('title-edit-pengalaman').innerText = 'Edit Pengalaman';
            document.getElementById('btn-delete-pengalaman').style.display = 'block';
            openModal('modal-edit-pengalaman');
        }
        function deletePengalaman() {
            if (confirm('Apakah Anda yakin ingin menghapus pengalaman ini?')) {
                const index = document.getElementById('input-pengalaman-index').value;
                document.getElementById('input-pengalaman-delete-index').value = index;
                document.getElementById('form-delete-pengalaman').submit();
            }
        }

        // Pendidikan
        function openAddPendidikan() {
            document.getElementById('form-edit-pendidikan').reset();
            document.getElementById('input-pendidikan-action').value = 'add';
            document.getElementById('input-pendidikan-index').value = '';
            document.getElementById('title-edit-pendidikan').innerText = 'Tambah Pendidikan';
            document.getElementById('btn-delete-pendidikan').style.display = 'none';
            openModal('modal-edit-pendidikan');
        }
        function openEditPendidikan(index, institution, degree, startYear, endYear) {
            document.getElementById('input-pendidikan-kampus').value = institution;
            document.getElementById('input-pendidikan-gelar').value = degree;
            document.getElementById('input-pendidikan-mulai').value = startYear;
            document.getElementById('input-pendidikan-selesai').value = endYear;

            document.getElementById('input-pendidikan-action').value = 'edit';
            document.getElementById('input-pendidikan-index').value = index;
            document.getElementById('title-edit-pendidikan').innerText = 'Edit Pendidikan';
            document.getElementById('btn-delete-pendidikan').style.display = 'block';
            openModal('modal-edit-pendidikan');
        }
        function deletePendidikan() {
            if (confirm('Apakah Anda yakin ingin menghapus pendidikan ini?')) {
                const index = document.getElementById('input-pendidikan-index').value;
                document.getElementById('input-pendidikan-delete-index').value = index;
                document.getElementById('form-delete-pendidikan').submit();
            }
        }

        // Sertifikasi
        function openAddSertifikasi() {
            document.getElementById('form-edit-sertifikasi').reset();
            document.getElementById('input-sertifikasi-action').value = 'add';
            document.getElementById('input-sertifikasi-index').value = '';
            document.getElementById('title-edit-sertifikasi').innerText = 'Tambah Sertifikasi';
            document.getElementById('btn-delete-sertifikasi').style.display = 'none';
            openModal('modal-edit-sertifikasi');
        }
        function openEditSertifikasi(index, name, issuer, startDate, endDate) {
            document.getElementById('input-sertifikasi-nama').value = name;
            document.getElementById('input-sertifikasi-penerbit').value = issuer;
            document.getElementById('input-sertifikasi-mulai').value = startDate;
            document.getElementById('input-sertifikasi-selesai').value = endDate;

            document.getElementById('input-sertifikasi-action').value = 'edit';
            document.getElementById('input-sertifikasi-index').value = index;
            document.getElementById('title-edit-sertifikasi').innerText = 'Edit Sertifikasi';
            document.getElementById('btn-delete-sertifikasi').style.display = 'block';
            openModal('modal-edit-sertifikasi');
        }
        function deleteSertifikasi() {
            if (confirm('Apakah Anda yakin ingin menghapus sertifikasi ini?')) {
                const index = document.getElementById('input-sertifikasi-index').value;
                document.getElementById('input-sertifikasi-delete-index').value = index;
                document.getElementById('form-delete-sertifikasi').submit();
            }
        }

        function switchTab(event, tabId) {
            event.preventDefault();

            // Remove active class from all tab links
            document.querySelectorAll('.tab-item').forEach(item => {
                item.classList.remove('active');
            });

            // Add active class to clicked tab link
            event.currentTarget.classList.add('active');

            // Hide all tab panels
            document.querySelectorAll('.tab-panel').forEach(panel => {
                panel.classList.remove('active');
            });

            // Show target panel
            document.getElementById(tabId).classList.add('active');

            // Handle Right Sidebar Visibility
            const sidebarPencapaian = document.getElementById('sidebar-pencapaian');
            const sidebarPenilaian = document.getElementById('sidebar-penilaian');

            if (tabId === 'tab-ulasan') {
                sidebarPencapaian.style.display = 'none';
                sidebarPenilaian.style.display = 'block';
            } else {
                sidebarPencapaian.style.display = 'block';
                sidebarPenilaian.style.display = 'none';
            }
        }
    </script>
@endsection
