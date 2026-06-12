@extends('layouts.admin-trainer')

@section('title', 'Review Event Material - ' . $event->title)

@push('admin-trainer-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        :root {
            --admin-primary: #1e1b4b;
            --admin-secondary: #3949ab;
            --admin-accent: #5c6bc0;
            --admin-accent-soft: #eef1ff;
            --admin-accent-border: #dce3ff;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
            
            --status-pending-bg: rgba(245, 158, 11, 0.08);
            --status-pending-text: #d97706;
            --status-pending-border: rgba(245, 158, 11, 0.15);
            
            --status-approved-bg: rgba(16, 185, 129, 0.08);
            --status-approved-text: #059669;
            --status-approved-border: rgba(16, 185, 129, 0.15);
            
            --status-rejected-bg: rgba(239, 68, 68, 0.08);
            --status-rejected-text: #dc2626;
            --status-rejected-border: rgba(239, 68, 68, 0.15);
        }

        .show-page-wrapper {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .scrollable-content::-webkit-scrollbar {
            width: 6px;
        }
        .scrollable-content::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 8px;
        }
        .scrollable-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }
        .scrollable-content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
            padding: 16px 24px;
            background: #ffffff;
            border: 1px solid var(--admin-border);
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);
        }

        .btn-back {
            background: #fff;
            border: 1.5px solid #e2e8f0;
            color: #475569;
            height: 40px;
            padding: 0 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.86rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
        }

        .btn-back:hover {
            background: #f8fafc;
            color: var(--admin-secondary);
            border-color: #cbd5e1;
            transform: translateX(-3px);
        }

        .btn-back i {
            font-size: 0.95rem;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 38px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }

        .status-chip.pending {
            border: 1px solid var(--status-pending-border);
            background: var(--status-pending-bg);
            color: var(--status-pending-text);
        }

        .status-chip.approved {
            border: 1px solid var(--status-approved-border);
            background: var(--status-approved-bg);
            color: var(--status-approved-text);
        }

        .status-chip.rejected {
            border: 1px solid var(--status-rejected-border);
            background: var(--status-rejected-bg);
            color: var(--status-rejected-text);
        }

        .status-chip::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: currentColor;
            flex-shrink: 0;
        }

        /* Card custom adjustments */
        .card-custom {
            background: var(--admin-card-bg);
            border-radius: 20px;
            border: 1px solid var(--admin-border);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.01);
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
            border-color: #cbd5e1;
        }

        .card-title-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1.5px solid var(--admin-border);
        }

        .card-title-text {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--admin-text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            letter-spacing: -0.2px;
        }

        .card-title-text i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            background: rgba(57, 73, 171, 0.06);
            color: var(--admin-secondary);
            font-size: 1.05rem;
        }

        .review-count-badge {
            font-size: 0.76rem;
            color: var(--admin-text-muted);
            font-weight: 700;
            background: #f8fafc;
            padding: 5px 12px;
            border-radius: 8px;
            border: 1px solid var(--admin-border);
        }

        .review-hero-header {
            margin-bottom: 16px;
        }

        .review-hero-title {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--admin-text-main);
            margin: 0 0 6px;
            line-height: 1.35;
            letter-spacing: -0.4px;
        }

        .review-meta-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 0.8rem;
            color: var(--admin-text-muted);
            margin: 0;
            font-weight: 500;
        }

        .review-type-chip {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 0.72rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 3px 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Video / Player screen */
        .video-container {
            background: #0f172a;
            border-radius: 14px;
            overflow: hidden;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.3);
            border: 1.5px solid #1e293b;
            position: relative;
            transition: all 0.3s ease;
        }

        .video-container iframe,
        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #000;
        }

        .video-container.is-image,
        .video-container.is-link,
        .video-container.is-file {
            background: #fff;
            color: var(--admin-text-main);
            border-color: var(--admin-border);
            box-shadow: inset 0 2px 6px rgba(15, 23, 42, 0.02);
        }

        .video-container.is-image img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .video-container.is-link iframe,
        .video-container.is-file iframe {
            background: #fff;
        }

        .review-preview-footer {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
        }

        .review-preview-title {
            font-size: 0.84rem;
            font-weight: 800;
            color: #334155;
            flex: 1;
        }

        .review-preview-meta {
            font-size: 0.74rem;
            color: #64748b;
            font-weight: 600;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* Module Items styled lists */
        .module-item {
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px;
            display: flex;
            gap: 16px;
            background: #ffffff;
            align-items: flex-start;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .module-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.03);
            transform: translateY(-2px);
        }

        .module-item.is-preview-active {
            border-color: var(--admin-secondary);
            background: rgba(57, 73, 171, 0.01);
        }

        .module-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #f1f5f9;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .module-desc {
            flex: 1;
            min-width: 0;
        }

        .module-desc h6 {
            margin: 0 0 6px 0;
            font-weight: 800;
            color: var(--admin-text-main);
            font-size: 0.92rem;
            line-height: 1.4;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Small state indicators instead of large thick borders */
        .status-dot-indicator {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }
        .status-dot-indicator.pending { background-color: #f59e0b; }
        .status-dot-indicator.approved { background-color: #10b981; }
        .status-dot-indicator.rejected { background-color: #ef4444; }

        .module-meta-info {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }

        .module-type-tag {
            font-size: 0.7rem;
            font-weight: 800;
            color: var(--admin-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .module-filename-tag {
            font-size: 0.74rem;
            color: #475569;
            font-weight: 500;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 6px;
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .module-quick-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .module-icon-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            background: #ffffff;
            color: #475569;
        }

        .module-icon-btn:hover {
            border-color: var(--admin-secondary);
            color: var(--admin-secondary);
            background: #f8fafc;
        }

        .module-review-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-transform: uppercase;
            border: 1px solid transparent;
            width: fit-content;
        }

        .module-review-badge.approved {
            background: var(--status-approved-bg);
            color: var(--status-approved-text);
            border-color: var(--status-approved-border);
        }

        .module-review-badge.rejected {
            background: var(--status-rejected-bg);
            color: var(--status-rejected-text);
            border-color: var(--status-rejected-border);
        }

        .module-review-badge.pending {
            background: var(--status-pending-bg);
            color: var(--status-pending-text);
            border-color: var(--status-pending-border);
        }

        .module-decision-stack {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .module-btn-approve,
        .module-btn-reject,
        .module-btn-revoke {
            border: 1.5px solid transparent;
            border-radius: 8px;
            height: 34px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .module-btn-approve {
            color: #fff;
            background: var(--admin-secondary);
            border-color: var(--admin-secondary);
        }

        .module-btn-approve:hover {
            background: #283593;
            border-color: #283593;
        }

        .module-btn-reject {
            color: var(--status-rejected-text);
            background: var(--status-rejected-bg);
            border-color: var(--status-rejected-border);
        }

        .module-btn-reject:hover {
            background: var(--status-rejected-text);
            color: #fff;
            border-color: var(--status-rejected-text);
        }

        .module-btn-revoke {
            color: #d97706;
            background: #fffbeb;
            border-color: #fef3c7;
        }

        .module-btn-revoke:hover {
            background: #d97706;
            color: #fff;
            border-color: #d97706;
        }

        .module-reject-form {
            margin-top: 12px;
            padding: 14px;
            border-radius: 12px;
            border: 1px solid var(--status-rejected-border);
            background: #fffafa;
            animation: slideDown 0.25s ease-out;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .module-reject-form textarea {
            width: 100%;
            min-height: 80px;
            border-radius: 10px;
            border: 1.5px solid var(--status-rejected-border);
            padding: 10px 12px;
            font-size: 0.84rem;
            resize: vertical;
            margin-bottom: 10px;
            background: #fff;
            color: var(--admin-text-main);
            outline: none;
        }

        .module-reject-form textarea:focus {
            border-color: var(--status-rejected-text);
        }

        .module-reject-form button {
            background: var(--status-rejected-text);
            color: #fff;
            border: none;
            border-radius: 8px;
            height: 34px;
            padding: 0 14px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .module-reject-form button:hover {
            background: #b91c1c;
        }

        .review-survey-box {
            font-size: 0.78rem;
            background: var(--admin-accent-soft);
            border: 1.5px solid var(--admin-accent-border);
            border-radius: 10px;
            padding: 8px 12px;
            margin-top: 8px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .review-survey-box a {
            color: var(--admin-secondary);
            text-decoration: underline;
            font-weight: 700;
        }

        .review-rejection-box {
            font-size: 0.78rem;
            color: var(--status-rejected-text);
            background: var(--status-rejected-bg);
            border: 1.5px solid var(--status-rejected-border);
            border-radius: 10px;
            padding: 10px 14px;
            margin-top: 8px;
            line-height: 1.5;
        }

        .review-rejection-box strong {
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 2px;
        }

        .review-empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--admin-text-muted);
        }

        .review-empty-state i {
            font-size: 2.5rem;
            color: #cbd5e1;
            margin-bottom: 8px;
            display: block;
        }

        .review-empty-state h5 {
            color: var(--admin-text-main);
            font-weight: 800;
            margin: 0 0 4px;
        }

        /* Sidebar card refinements */
        .side-card {
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.01);
            padding: 20px !important;
        }

        .side-card-title {
            color: var(--admin-secondary);
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 16px;
            border-bottom: 1.5px solid var(--admin-accent-soft);
            padding-bottom: 8px;
        }

        .trainer-profile-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trainer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
            flex-shrink: 0;
            display: block;
        }

        .trainer-profile-name {
            font-weight: 800;
            color: var(--admin-text-main);
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .trainer-profile-role {
            font-size: 0.74rem;
            color: var(--admin-text-muted);
            font-weight: 600;
            margin-top: 1px;
        }

        .trainer-profile-email {
            font-size: 0.72rem;
            color: var(--admin-text-muted);
            margin-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .info-list-custom {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .info-item-custom {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .info-label-custom {
            font-size: 0.7rem;
            font-weight: 800;
            color: var(--admin-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-val-custom {
            font-size: 0.84rem;
            font-weight: 700;
            color: var(--admin-text-main);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .info-val-custom i {
            color: #64748b;
        }

        /* Mini stats grid */
        .grid-stats-mini {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .stat-mini-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 12px 8px;
            text-align: center;
            border: 1px solid #f1f5f9;
            transition: all 0.25s ease;
        }

        .stat-mini-item:hover {
            background: #f1f5f9;
            transform: translateY(-1px);
        }

        .stat-mini-num {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--admin-text-main);
            line-height: 1.2;
        }

        .stat-mini-label {
            font-size: 0.7rem;
            color: var(--admin-text-muted);
            font-weight: 750;
            margin-top: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .summary-progress-wrapper {
            margin-top: 14px;
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 4px;
            border: 1px solid #e2e8f0;
        }

        .progress-bar-fill {
            height: 100%;
            background: var(--admin-secondary);
            border-radius: 999px;
            transition: width 0.6s ease;
        }

        .review-pending-banner {
            margin-top: 12px;
            background: var(--status-pending-bg);
            border: 1px solid var(--status-pending-border);
            border-radius: 10px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.76rem;
            color: var(--status-pending-text);
            font-weight: 750;
        }

        /* Sticky Action Sidebar */
        .action-box {
            position: sticky;
            top: 90px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Final Action Buttons styles */
        .btn-approve {
            width: 100%;
            background: var(--admin-secondary);
            color: white;
            height: 48px;
            padding: 0 16px;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-approve:hover:not(:disabled) {
            background: #283593;
        }

        .btn-reject {
            width: 100%;
            background: #fff;
            color: var(--status-rejected-text);
            border: 1.5px solid var(--status-rejected-border);
            height: 48px;
            padding: 0 16px;
            border-radius: 12px;
            font-weight: 750;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-reject:hover {
            background: var(--status-rejected-bg);
            border-color: var(--status-rejected-text);
        }

        .btn-revoke {
            width: 100%;
            background: #fff;
            color: #d97706;
            border: 1.5px solid #fef3c7;
            height: 48px;
            padding: 0 16px;
            border-radius: 12px;
            font-weight: 750;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-revoke:hover {
            background: #fffbeb;
            border-color: #d97706;
        }

        .review-locked-notice {
            font-size: 0.82rem;
            padding: 12px;
            text-align: center;
            color: var(--admin-text-muted);
        }

        /* Alert notifications */
        .review-alert {
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 0.88rem;
        }

        .review-alert--success {
            background: var(--status-approved-bg);
            border-color: var(--status-approved-border);
            color: var(--status-approved-text);
        }

        .review-alert--danger {
            background: var(--status-rejected-bg);
            border-color: var(--status-rejected-border);
            color: var(--status-rejected-text);
        }

        /* Modal Dialog */
        .reject-modal .modal-content {
            border-radius: 20px;
            overflow: hidden;
            border: none;
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.1);
        }

        .reject-modal .modal-header {
            background: #f8fafc;
            border-bottom: 1.5px solid var(--admin-border);
            padding: 18px 24px;
        }

        .reject-modal .modal-title {
            color: var(--admin-text-main);
            font-size: 1.05rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.2px;
        }

        .reject-modal .modal-body {
            padding: 24px;
            background: #ffffff;
        }

        .reject-modal .form-control {
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.88rem;
            padding: 12px 14px;
            min-height: 120px;
            resize: vertical;
            outline: none;
        }

        .reject-modal .form-control:focus {
            border-color: var(--admin-secondary);
        }

        .reject-modal .btn-cancel {
            border: 1.5px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 700;
            height: 40px;
            padding: 0 18px;
            transition: all 0.2s ease;
        }

        .reject-modal .btn-cancel:hover {
            background: #f8fafc;
            color: var(--admin-text-main);
        }

        .reject-modal .btn-submit-reject {
            border: none;
            background: var(--status-rejected-text);
            color: #fff;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 750;
            height: 40px;
            padding: 0 18px;
            transition: all 0.2s ease;
        }

        .reject-modal .btn-submit-reject:hover {
            background: #b91c1c;
        }

        .preview-sticky-container {
            position: sticky;
            top: 90px;
            z-index: 10;
        }

        .module-item-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            width: 100%;
        }

        @media (max-width: 1199.98px) {
            .action-box {
                position: static !important;
            }
            .preview-sticky-container {
                position: static !important;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                align-items: flex-start;
                flex-direction: column;
                padding: 16px;
            }
            .status-chip {
                width: 100%;
                justify-content: center;
            }
            .btn-back {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 575.98px) {
            .module-item-header {
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }
            .module-quick-actions {
                justify-content: flex-start !important;
                margin-top: 4px;
            }
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="show-page-wrapper">
        {{-- Page Header --}}
        <div class="page-header">
            <a href="{{ route('admin.trainer.material.approvals') }}?tab=event" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
            @if(($materialStatus ?? 'pending') === 'approved')
                <span class="status-chip approved">
                    Disetujui
                </span>
            @elseif(($materialStatus ?? 'pending') === 'rejected')
                <span class="status-chip rejected">
                    Revisi / Ditolak
                </span>
            @else
                <span class="status-chip pending">
                    Menunggu Tinjauan
                </span>
            @endif
        </div>

        {{-- Notifications --}}
        @if (session('success'))
            <div class="review-alert review-alert--success">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="review-alert review-alert--danger">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- Main Grid: Left (8) | Right (4) --}}
        <div class="row g-4">

            {{-- ========== LEFT COLUMN ========== --}}
            <div class="col-xl-8" style="display:flex; flex-direction:column; gap:20px;">

                {{-- Sticky Preview Card at top of content area on desktop scroll --}}
                <div class="preview-sticky-container">
                    <div class="card-custom" style="margin-bottom:0;">
                        <div class="review-hero-header">
                            <h1 class="review-hero-title">{{ $event->title }}</h1>
                            <p class="review-meta-row">
                                <span class="review-type-chip">{{ $event->jenis ?? 'Event' }}</span>
                                <span>·</span>
                                <span>Diajukan oleh {{ $materialTrainer->name ?? 'Trainer' }}</span>
                            </p>
                        </div>

                        {{-- Player Container --}}
                        <div id="topReviewViewer" class="video-container">
                            <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; opacity:0.45; text-align:center; padding: 20px;">
                                <i class="bi bi-camera-video" style="font-size:3.5rem; margin-bottom:12px; color: var(--admin-accent);"></i>
                                <p style="font-size:0.84rem; font-weight: 700; margin:0;">Pilih Modul di Bawah</p>
                                <p class="text-muted" style="font-size:0.75rem; margin-top:4px; max-width: 250px;">Klik tombol <i class="bi bi-eye"></i> pada baris dokumen untuk meninjau konten</p>
                            </div>
                        </div>

                        <div class="review-preview-footer">
                            <div id="topReviewTitle" class="review-preview-title">Preview Materi Event</div>
                            <div id="topReviewMeta" class="review-preview-meta"></div>
                        </div>
                    </div>
                </div>

                {{-- Document Lists --}}
                <div class="card-custom" style="margin-bottom:0;">
                    <div class="card-title-bar">
                        <h5 class="card-title-text">
                            <i class="bi bi-folder2-open"></i>
                            Daftar Modul Event
                        </h5>
                        <span class="review-count-badge">{{ $event->trainerModules->count() }} Modul / Dokumen</span>
                    </div>

                    <div class="scrollable-content" style="max-height: 52vh; overflow-y: auto; padding-right: 4px;">
                        @if ($event->trainerModules->isNotEmpty())
                            @foreach ($event->trainerModules as $module)
                                @php
                                    $rawContent = trim((string) ($module->path ?? ''));
                                    $isHttp = (bool) preg_match('#^https?://#i', $rawContent);
                                    $surveyUrl = trim((string) ($module->survey_link ?? ''));
                                    $ext = strtolower(pathinfo($rawContent, PATHINFO_EXTENSION));

                                    $previewKind = 'file';
                                    $previewUrl = '';

                                    if ($rawContent !== '') {
                                        if ($isHttp) {
                                            $previewUrl = $rawContent;
                                            $previewKind = 'link';
                                        } else {
                                            $previewUrl = route('admin.event-material.stream', $event->id) . '?module_id=' . $module->id . '&download=0';
                                            if (in_array($ext, ['mp4', 'mov', 'webm', 'm4v'], true)) {
                                                $previewKind = 'video';
                                            } elseif ($ext === 'pdf') {
                                                $previewKind = 'pdf';
                                            } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'], true)) {
                                                $previewKind = 'image';
                                            }
                                        }
                                    } elseif ($surveyUrl !== '') {
                                        $previewUrl = $surveyUrl;
                                        $previewKind = 'link';
                                    }

                                    $downloadUrl = $rawContent !== '' && !$isHttp
                                        ? route('admin.event-material.stream', $event->id) . '?module_id=' . $module->id . '&download=1'
                                        : null;
                                    
                                    $reviewStatus = in_array(($module->status ?? ''), ['approved', 'rejected', 'pending_review', 'pending'], true)
                                        ? ($module->status === 'pending' ? 'pending_review' : $module->status) : 'pending_review';
                                @endphp

                                <div class="module-item {{ $reviewStatus }}">
                                    {{-- Icon based on survey or file --}}
                                    <div class="module-icon">
                                        @if($module->survey_link) <i class="bi bi-link-45deg"></i>
                                        @else <i class="bi bi-file-earmark-text-fill"></i>
                                        @endif
                                    </div>

                                    <div class="module-desc">
                                        <div class="module-item-header">
                                            <div>
                                                <h6>
                                                    <span class="status-dot-indicator {{ $module->status === 'approved' ? 'approved' : ($module->status === 'rejected' ? 'rejected' : 'pending') }}"></span>
                                                    {{ $module->original_name ?: basename($module->path) }}
                                                </h6>
                                                <div class="module-meta-info">
                                                    <span class="module-type-tag">
                                                        @if($module->survey_link) TAUTAN / SURVEI @else DOKUMEN MODUL @endif
                                                    </span>
                                                    <span style="color:#cbd5e1;">·</span>
                                                    <span class="module-filename-tag">
                                                        Oleh: {{ $module->trainer->name ?? '-' }} · Diunggah: {{ $module->created_at ? $module->created_at->format('d M Y H:i') : '-' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="module-quick-actions">
                                                @if($previewUrl !== '')
                                                    <button type="button" class="module-review-trigger module-icon-btn preview"
                                                        data-review-title="{{ e($module->original_name ?: basename($module->path)) }}"
                                                        data-review-url="{{ $previewUrl }}"
                                                        data-review-kind="{{ $previewKind }}"
                                                        data-review-file="{{ e($module->original_name ?: basename($module->path)) }}"
                                                        title="Preview Modul">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                @endif
                                                @if($downloadUrl)
                                                    <a href="{{ $downloadUrl }}" class="module-icon-btn download" title="Download File">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Link Survey info box --}}
                                        @if($module->survey_link)
                                            <div class="review-survey-box">
                                                <i class="bi bi-link-45deg me-1"></i>Link Survei Kepuasan:
                                                <a href="{{ $module->survey_link }}" target="_blank">{{ $module->survey_link }}</a>
                                            </div>
                                        @endif

                                        @if($module->rejection_reason)
                                            <div class="review-rejection-box">
                                                <strong><i class="bi bi-chat-left-text-fill me-1"></i>Catatan Revisi:</strong>
                                                {{ $module->rejection_reason }}
                                            </div>
                                        @endif

                                        {{-- Review badge and Actions --}}
                                        <div style="display:flex; flex-direction:column; gap:8px; margin-top:10px;">
                                            <span class="module-review-badge {{ $reviewStatus }}">
                                                @if($reviewStatus === 'approved')
                                                    Disetujui
                                                @elseif($reviewStatus === 'rejected')
                                                    Perlu Revisi
                                                @else
                                                    Menunggu Tinjauan
                                                @endif
                                            </span>

                                            {{-- Inline action buttons if pending review --}}
                                            @if((($materialStatus ?? 'pending') === 'pending' || ($materialStatus ?? 'pending') === 'pending_review') && $reviewStatus !== 'approved')
                                                <div class="module-decision-stack">
                                                    <form method="POST" action="{{ route('admin.event-material.approve', $event->id) }}" class="module-action-form">
                                                        @csrf
                                                        <input type="hidden" name="module_id" value="{{ $module->id }}">
                                                        <button type="submit" class="module-btn-approve">
                                                            Setujui Modul
                                                        </button>
                                                    </form>
                                                    <button type="button" class="module-btn-reject" data-bs-toggle="collapse" data-bs-target="#rejectModuleForm-{{ $module->id }}">
                                                        Revisi
                                                    </button>
                                                </div>

                                                <div class="collapse module-reject-form" id="rejectModuleForm-{{ $module->id }}">
                                                    <form method="POST" action="{{ route('admin.event-material.reject', $event->id) }}">
                                                        @csrf
                                                        <input type="hidden" name="module_id" value="{{ $module->id }}">
                                                        <textarea name="rejection_reason" required placeholder="Tuliskan catatan perbaikan untuk dokumen/modul event ini..."></textarea>
                                                        <button type="submit">
                                                            Kirim Catatan
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif

                                            {{-- Inline action buttons if approved or rejected --}}
                                            @if($reviewStatus === 'approved' || $reviewStatus === 'rejected')
                                                <div class="module-decision-stack">
                                                    <form method="POST" action="{{ route('admin.event-material.revoke', $event->id) }}">
                                                        @csrf
                                                        <input type="hidden" name="module_id" value="{{ $module->id }}">
                                                        <button type="submit" class="module-btn-revoke">
                                                            Batalkan Keputusan
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @elseif ($event->module_path)
                            @php
                                $legacyName = basename($event->module_path);
                                $legacyExt = strtolower(pathinfo($legacyName, PATHINFO_EXTENSION));
                                $legacyKind = 'file';
                                if (in_array($legacyExt, ['mp4', 'mov', 'webm', 'm4v'], true)) {
                                    $legacyKind = 'video';
                                } elseif ($legacyExt === 'pdf') {
                                    $legacyKind = 'pdf';
                                } elseif (in_array($legacyExt, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'], true)) {
                                    $legacyKind = 'image';
                                }
                                $legacyPreviewUrl = route('admin.event-material.stream', $event->id) . '?download=0';
                                $legacyDownloadUrl = route('admin.event-material.stream', $event->id) . '?download=1';
                            @endphp
                            <div class="module-item pending">
                                <div class="module-icon"><i class="bi bi-file-earmark-text-fill"></i></div>
                                <div class="module-desc">
                                    <div class="module-item-header">
                                        <div>
                                            <h6>{{ $legacyName }}</h6>
                                            <span class="module-type-tag">DOKUMEN MODUL EVENT (SINGLE FILE)</span>
                                        </div>
                                        <div class="module-quick-actions">
                                            <button type="button" class="module-review-trigger module-icon-btn preview"
                                                data-review-title="{{ e($legacyName) }}"
                                                data-review-url="{{ $legacyPreviewUrl }}"
                                                data-review-kind="{{ $legacyKind }}"
                                                data-review-file="{{ e($legacyName) }}"
                                                title="Preview Modul">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <a href="{{ $legacyDownloadUrl }}" class="module-icon-btn download" title="Download File">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="review-empty-state">
                                <i class="bi bi-folder-x"></i>
                                <h5>Tidak ada modul</h5>
                                <p class="mb-0 small">Belum ada modul yang diunggah trainer untuk event ini.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ========== RIGHT COLUMN (STICKY SIDEBAR) ========== --}}
            <div class="col-xl-4">
                <div class="action-box">
                    
                    {{-- Trainer Card Info --}}
                    <div class="card-custom side-card">
                        <div class="side-card-title">Instruktur</div>
                        <div class="trainer-profile-row">
                            <img src="{{ $materialTrainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($materialTrainer?->name ?? 'T') . '&background=3949ab&color=fff&bold=true' }}"
                                class="trainer-avatar"
                                alt="Avatar {{ $materialTrainer?->name ?? 'Trainer' }}">
                            <div style="min-width:0;">
                                <div class="trainer-profile-name">{{ $materialTrainer?->name ?? 'Anonim' }}</div>
                                <div class="trainer-profile-role">Trainer / Instruktur Event</div>
                                @if($materialTrainer?->email)
                                    <div class="trainer-profile-email">
                                        <i class="bi bi-envelope me-1"></i>{{ $materialTrainer->email }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Event Info Box --}}
                    <div class="card-custom side-card">
                        <div class="side-card-title">Informasi Event</div>
                        <div class="info-list-custom">
                            <div class="info-item-custom">
                                <span class="info-label-custom">Tanggal Pelaksanaan</span>
                                <span class="info-val-custom">
                                    <i class="bi bi-calendar-check text-muted me-1"></i>
                                    {{ optional($event->event_date)->format('d M Y') ?: '-' }}
                                    @if($event->event_time)
                                        · {{ optional($event->event_time)->format('H:i') }}
                                    @endif
                                </span>
                            </div>
                            <div class="info-item-custom">
                                <span class="info-label-custom">Lokasi</span>
                                <span class="info-val-custom" style="word-break: break-all;">
                                    <i class="bi bi-geo-alt text-muted me-1"></i>
                                    {{ $event->location ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Summary & Statistics Card --}}
                    <div class="card-custom side-card">
                        <div class="side-card-title">Ringkasan Modul</div>
                        @php
                            $etmCount = $event->trainerModules->count();
                            $etmApproved = $event->trainerModules->where('status', 'approved')->count();
                            $etmRejected = $event->trainerModules->where('status', 'rejected')->count();
                            $etmPending = $etmCount - $etmApproved - $etmRejected;
                            
                            $progressPercent = $etmCount > 0 ? round(($etmApproved / $etmCount) * 100) : 0;
                        @endphp

                        <div class="grid-stats-mini">
                            <div class="stat-mini-item">
                                <div class="stat-mini-num">{{ $etmCount }}</div>
                                <div class="stat-mini-label">Total Modul</div>
                            </div>
                            <div class="stat-mini-item">
                                <div class="stat-mini-num" style="color: var(--status-approved-text);">{{ $etmApproved }}</div>
                                <div class="stat-mini-label">Disetujui</div>
                            </div>
                            <div class="stat-mini-item">
                                <div class="stat-mini-num" style="color: var(--status-rejected-text);">{{ $etmRejected }}</div>
                                <div class="stat-mini-label">Ditolak</div>
                            </div>
                            <div class="stat-mini-item">
                                <div class="stat-mini-num" style="color: var(--status-pending-text);">{{ $etmPending }}</div>
                                <div class="stat-mini-label">Pending</div>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="summary-progress-wrapper">
                            <div style="display:flex; justify-content:space-between; font-size:0.75rem; font-weight:800; margin-bottom:4px;">
                                <span class="text-muted">Progres Persetujuan</span>
                                <span style="color: var(--admin-secondary);">{{ $progressPercent }}%</span>
                            </div>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                            </div>
                        </div>

                        @if($etmPending > 0)
                            <div class="review-pending-banner">
                                <i class="bi bi-hourglass-split"></i>
                                {{ $etmPending }} Modul Menunggu Tinjauan
                            </div>
                        @endif
                    </div>

                    {{-- Decision Card (Approve All or Reject Event) --}}
                    @if (($materialStatus ?? 'pending') === 'pending' || ($materialStatus ?? 'pending') === 'pending_review')
                        <div class="card-custom side-card">
                            <div class="side-card-title">Keputusan Akhir</div>
                            
                            <form method="POST" action="{{ route('admin.event-material.approve', $event->id) }}" style="margin-bottom:12px;">
                                @csrf
                                @if($assignment)
                                    <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                                @endif
                                <button type="submit" class="btn-approve">
                                    Setujui Semua Materi
                                </button>
                            </form>

                            <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectEventModal">
                                Tolak Semua Materi
                            </button>
                        </div>
                    @else
                        <div class="card-custom side-card">
                            <div class="side-card-title">Keputusan Akhir</div>
                            <div class="review-locked-notice mb-3" style="text-align: left; padding: 0; color: var(--admin-text-main); font-weight: 700; display: flex; align-items: center; gap: 8px;">
                                <i class="bi bi-info-circle-fill text-primary"></i> 
                                <span>Status: 
                                    @if(($materialStatus ?? 'pending') === 'approved')
                                        <span class="text-success">Disetujui</span>
                                    @elseif(($materialStatus ?? 'pending') === 'rejected')
                                        <span class="text-danger">Ditolak / Revisi</span>
                                    @else
                                        <span class="text-muted">{{ ucfirst($materialStatus ?? 'pending') }}</span>
                                    @endif
                                </span>
                            </div>
                            
                            <form method="POST" action="{{ route('admin.event-material.revoke', $event->id) }}">
                                @csrf
                                @if($assignment)
                                    <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                                @endif
                                <button type="submit" class="btn-revoke">
                                    Batalkan Keputusan
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <!-- Reject Event Modal Dialog -->
    <div class="modal fade reject-modal" id="rejectEventModal" tabindex="-1" aria-labelledby="rejectEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectEventModalLabel">
                        Tolak Semua Materi Event
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.event-material.reject', $event->id) }}" method="POST">
                    @csrf
                    @if($assignment)
                        <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                    @endif
                    <div class="modal-body">
                        <div class="mb-1">
                            <label for="rejection_reason" class="form-label" style="font-weight:750; color:var(--admin-text-main);">Catatan Revisi / Alasan Penolakan</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" required minlength="10" placeholder="Tuliskan catatan perbaikan atau alasan penolakan untuk seluruh modul event ini secara rinci agar trainer mengerti..."></textarea>
                            <span class="text-muted" style="font-size:0.76rem; display:block; margin-top:8px;">
                                <i class="bi bi-info-circle me-1"></i> Trainer akan menerima notifikasi beserta catatan revisi ini untuk memperbaiki materi event mereka. Minimal 10 karakter.
                            </span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-cancel" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn-submit-reject">Tolak & Minta Revisi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('admin-trainer-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewer = document.getElementById('topReviewViewer');
            const viewerTitle = document.getElementById('topReviewTitle');
            const viewerMeta = document.getElementById('topReviewMeta');

            function setViewerMode(mode) {
                if (!viewer) return;
                viewer.classList.remove('is-image', 'is-link', 'is-file', 'is-video', 'is-pdf');
                if (mode) viewer.classList.add(mode);
            }

            function showPreviewContent(content, title, meta, mode) {
                if (!viewer) return;
                setViewerMode(mode || '');
                viewer.innerHTML = '';
                if (content instanceof HTMLElement) {
                    viewer.appendChild(content);
                } else {
                    viewer.innerHTML = String(content || '');
                }
                if (viewerTitle) viewerTitle.textContent = title || 'Preview Modul';
                if (viewerMeta) viewerMeta.textContent = meta || '';
                
                // Active visual feedback outline
                viewer.style.outline = '3px solid var(--admin-accent)';
                viewer.style.outlineOffset = '2px';
                setTimeout(() => {
                    viewer.style.outline = '';
                    viewer.style.outlineOffset = '';
                }, 700);
                
                // Smooth scroll to previewer on mobile screens
                if (window.innerWidth < 1200) {
                    viewer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            function renderPreview(url, kind) {
                if (!url) {
                    return '<div class="text-center opacity-50 py-4"><i class="bi bi-file-earmark-x" style="font-size:3rem;"></i><p class="mt-2 mb-0">File tidak tersedia</p></div>';
                }
                if (kind === 'video') {
                    return `<video controls controlsList="nodownload" style="width:100%; height:100%;"><source src="${url}"></video>`;
                }
                if (kind === 'pdf') {
                    return `<iframe src="${url}#toolbar=1&navpanes=0" title="Preview PDF"></iframe>`;
                }
                if (kind === 'image') {
                    return `<img src="${url}" alt="Preview gambar">`;
                }
                if (kind === 'link') {
                    return `
                        <div class="d-flex flex-column align-items-center justify-content-center text-center p-4" style="height: 100%; width: 100%; background: #f8fafc; border-radius: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; box-sizing: border-box;">
                            <div class="mb-3" style="width: 50px; height: 50px; border-radius: 50%; background: #e0e7ff; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px;">
                                <i class="bi bi-box-arrow-up-right text-primary" style="font-size: 1.3rem;"></i>
                            </div>
                            <h6 class="mb-2" style="font-weight: 800; color: #1e293b; font-size: 0.95rem; margin-top: 0; margin-bottom: 8px;">Tautan Eksternal / Materi Link</h6>
                            <p class="text-muted mb-3" style="font-size: 0.76rem; max-width: 320px; line-height: 1.45; margin: 0 auto 14px;">
                                Tautan ini tidak dapat ditampilkan di sini karena kebijakan keamanan web (CSP). Silakan buka tautan secara langsung di tab baru:
                            </p>
                            <a href="${url}" target="_blank" class="btn btn-primary btn-sm px-4 py-2" style="font-weight: 700; border-radius: 8px; background-color: var(--admin-secondary, #3949ab); border-color: var(--admin-secondary, #3949ab); color: #fff; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                                <i class="bi bi-box-arrow-up-right"></i> Buka Tautan Materi
                            </a>
                            <div class="mt-3 text-muted" style="font-size: 0.68rem; word-break: break-all; max-width: 90%; font-family: monospace;">
                                ${url}
                            </div>
                        </div>
                    `;
                }
                return `<iframe src="${url}" title="Preview file"></iframe>`;
            }

            function previewModeClass(kind) {
                if (kind === 'video') return 'is-video';
                if (kind === 'pdf') return 'is-pdf';
                if (kind === 'image') return 'is-image';
                if (kind === 'link') return 'is-link';
                return 'is-file';
            }

            function openModulePreview(trigger) {
                const fileUrl = trigger.getAttribute('data-review-url') || '';
                const fileKind = trigger.getAttribute('data-review-kind') || 'file';
                const moduleTitle = trigger.getAttribute('data-review-title') || 'Materi';
                const fileName = trigger.getAttribute('data-review-file') || 'File';

                document.querySelectorAll('.module-item.is-preview-active').forEach((el) => {
                    el.classList.remove('is-preview-active');
                });
                const moduleItem = trigger.closest('.module-item');
                if (moduleItem) moduleItem.classList.add('is-preview-active');

                const titlePrefix = fileKind === 'link' ? 'Tinjau Tautan: ' : 'Tinjau File: ';
                const metaLabel = fileKind === 'link' ? 'Tautan Eksternal' : fileName;

                showPreviewContent(
                    renderPreview(fileUrl, fileKind),
                    titlePrefix + moduleTitle,
                    metaLabel,
                    previewModeClass(fileKind)
                );
            }

            document.addEventListener('click', function (e) {
                const trigger = e.target.closest('.module-review-trigger');
                if (!trigger) return;
                e.preventDefault();
                openModulePreview(trigger);
            });

            const firstPreviewTrigger = document.querySelector('.module-review-trigger');
            if (firstPreviewTrigger) {
                openModulePreview(firstPreviewTrigger);
            }
        });
    </script>
@endpush