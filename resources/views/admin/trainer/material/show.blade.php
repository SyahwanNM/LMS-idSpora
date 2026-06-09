@extends('layouts.admin-trainer')

@section('title', 'Tinjau Materi - ' . $material->name)

@push('admin-trainer-styles')
    <style>
        :root {
            --admin-primary: #1e1b4b;
            --admin-secondary: #4f46e5;
            --admin-accent: #6366f1;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
            
            --status-pending-bg: #fffbeb;
            --status-pending-text: #d97706;
            --status-pending-border: #fef3c7;
            
            --status-approved-bg: #f0fdf4;
            --status-approved-text: #16a34a;
            --status-approved-border: #dcfce7;
            
            --status-rejected-bg: #fef2f2;
            --status-rejected-text: #dc2626;
            --status-rejected-border: #fee2e2;
        }

        /* Smooth scrollbar for scrollable elements */
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

        .show-page-wrapper {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 16px;
            flex-wrap: wrap;
            padding: 12px 0;
        }

        .btn-back {
            background: #fff;
            border: 1px solid #cbd5e1;
            color: #334155;
            height: 42px;
            padding: 0 18px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.88rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .btn-back:hover {
            background: #f8fafc;
            color: var(--admin-secondary);
            border-color: #cbd5e1;
            transform: translateX(-3px);
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 42px;
            padding: 0 16px;
            border-radius: 999px;
            font-size: 0.86rem;
            font-weight: 800;
            letter-spacing: 0.2px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
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

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            animation: pulse-dot-anim 1.5s infinite ease-in-out;
        }

        @keyframes pulse-dot-anim {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.3); opacity: 0.5; }
        }

        /* Card Setup */
        .card-custom {
            background: var(--admin-card-bg);
            border-radius: 20px;
            border: 1px solid var(--admin-border);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.03);
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
        }

        .card-custom:hover {
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
        }

        .card-title-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
        }

        .card-title-text {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--admin-text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Video / Preview Player Container */
        .video-container {
            background: #0f172a;
            border-radius: 16px;
            overflow: hidden;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: inset 0 4px 20px rgba(0, 0, 0, 0.3);
            border: 1px solid #1e293b;
            position: relative;
        }

        .video-container iframe,
        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #000;
        }

        .video-container.is-quiz,
        .video-container.is-module-html {
            aspect-ratio: auto;
            min-height: 400px;
            max-height: 550px;
            overflow-y: auto;
            background: #fff;
            color: #0f172a;
            display: block;
            padding: 24px;
            border: 1px solid var(--admin-border);
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        /* Article content rendering stylings */
        .module-preview-article {
            color: #334155;
            line-height: 1.8;
            font-size: 0.96rem;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        .module-preview-article h1,
        .module-preview-article h2,
        .module-preview-article h3 {
            color: var(--admin-primary);
            margin: 20px 0 12px;
            font-weight: 800;
            line-height: 1.3;
        }

        .module-preview-article h1 { font-size: 1.6rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 8px; }
        .module-preview-article h2 { font-size: 1.35rem; }
        .module-preview-article h3 { font-size: 1.15rem; }
        .module-preview-article p { margin: 0 0 14px; }
        .module-preview-article ul, .module-preview-article ol { margin: 0 0 16px 24px; }
        .module-preview-article li { margin-bottom: 6px; }

        .module-preview-article .module-inline-image {
            margin: 18px 0;
            display: flex;
            justify-content: center;
        }

        .module-preview-article .module-inline-image img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            border: 1px solid var(--admin-border);
        }

        .module-preview-article .module-code-block {
            border-radius: 14px;
            background: #0f172a;
            margin: 18px 0;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .module-preview-article .module-code-block pre {
            margin: 0;
            padding: 16px 20px;
            background: transparent;
            color: #e2e8f0;
            overflow-x: auto;
            font-family: 'Fira Code', Consolas, Monaco, monospace;
            font-size: 13.5px;
            line-height: 1.65;
        }

        .module-preview-article .module-code-copy {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            cursor: pointer;
            border: none;
            border-radius: 8px;
            margin: 0 16px 16px;
            transition: all 0.2s ease;
        }

        .module-preview-article .module-code-copy:hover {
            background: var(--admin-secondary);
        }

        /* Quiz Preview */
        .quiz-preview-head {
            font-size: 0.85rem;
            color: var(--admin-text-muted);
            margin-bottom: 16px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .quiz-preview-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .quiz-preview-item {
            border: 1px solid var(--admin-border);
            border-radius: 14px;
            padding: 16px;
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        .quiz-preview-item:hover {
            border-color: #cbd5e1;
            background: #f1f5f9;
        }

        .quiz-preview-q {
            margin: 0 0 12px;
            font-size: 0.94rem;
            font-weight: 750;
            color: var(--admin-text-main);
        }

        .quiz-preview-answers {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .quiz-preview-answer {
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.86rem;
            background: #fff;
            border: 1px solid var(--admin-border);
            color: #334155;
            transition: all 0.2s ease;
        }

        .quiz-preview-answer.is-correct {
            border-color: #86efac;
            background: #f0fdf4;
            color: #166534;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(22, 163, 74, 0.05);
        }

        /* Unit / Bab Section Label */
        .unit-header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
            flex-wrap: wrap;
        }

        .unit-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 750;
            box-shadow: 0 2px 4px rgba(0,0,0,0.01);
        }

        .module-item {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 18px;
            display: flex;
            gap: 16px;
            background: #ffffff;
            align-items: flex-start;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .module-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: transparent;
            transition: background 0.2s ease;
        }

        .module-item.pending::before { background: var(--status-pending-text); }
        .module-item.approved::before { background: var(--status-approved-text); }
        .module-item.rejected::before { background: var(--status-rejected-text); }

        .module-item:hover {
            border-color: #cbd5e1;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
            transform: translateY(-2px);
        }

        .module-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: #eef2ff;
            color: #4945ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            transition: all 0.25s ease;
        }

        .module-item:hover .module-icon {
            transform: scale(1.08) rotate(3deg);
        }

        .module-desc {
            flex: 1;
            min-width: 0;
        }

        .module-desc h6 {
            margin: 0 0 6px 0;
            font-weight: 750;
            color: var(--admin-text-main);
            font-size: 0.94rem;
            line-height: 1.4;
        }

        .module-meta-info {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .module-type-tag {
            font-size: 0.72rem;
            font-weight: 800;
            color: var(--admin-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .module-filename-tag {
            font-size: 0.75rem;
            color: var(--admin-text-muted);
            font-style: italic;
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
            gap: 8px;
        }

        .module-icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .module-icon-btn.preview {
            color: var(--admin-secondary);
            background: #f0f3ff;
            border-color: #dbe0ff;
        }

        .module-icon-btn.preview:hover {
            background: var(--admin-secondary);
            color: #fff;
        }

        .module-icon-btn.download {
            color: #166534;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .module-icon-btn.download:hover {
            background: #166534;
            color: #fff;
        }

        .module-review-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.3px;
            border: 1px solid transparent;
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

        .module-tag-missing {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--status-rejected-text);
            background: var(--status-rejected-bg);
            border: 1px solid var(--status-rejected-border);
        }

        .module-decision-stack {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
            flex-wrap: wrap;
        }

        .module-btn-approve,
        .module-btn-reject {
            border: 1px solid transparent;
            border-radius: 10px;
            height: 36px;
            padding: 0 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .module-btn-approve {
            color: #fff;
            background: var(--status-approved-text);
        }

        .module-btn-approve:hover {
            background: #15803d;
            box-shadow: 0 4px 10px rgba(22, 163, 74, 0.2);
        }

        .module-btn-reject {
            color: var(--status-rejected-text);
            background: var(--status-rejected-bg);
            border-color: var(--status-rejected-border);
        }

        .module-btn-reject:hover {
            background: var(--status-rejected-text);
            color: #fff;
        }

        .module-reject-form {
            margin-top: 12px;
            padding: 16px;
            border-radius: 14px;
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
            min-height: 84px;
            border-radius: 10px;
            border: 1px solid var(--status-rejected-border);
            padding: 10px 12px;
            font-size: 0.85rem;
            resize: vertical;
            margin-bottom: 10px;
            background: #fff;
            color: var(--admin-text-main);
        }

        .module-reject-form textarea:focus {
            outline: none;
            border-color: var(--status-rejected-text);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .module-reject-form button {
            background: var(--status-rejected-text);
            color: #fff;
            border: none;
            border-radius: 8px;
            height: 36px;
            padding: 0 14px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .module-reject-form button:hover {
            background: #b91c1c;
        }

        /* Sidebar Kanan Elements */
        .trainer-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
            display: block;
        }

        .trainer-box {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid var(--admin-border);
            border-radius: 16px;
            margin-bottom: 20px;
        }

        .trainer-box img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.06);
            object-fit: cover;
        }

        .side-card {
            background: #fff;
            border: 1px solid var(--admin-border);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.02);
            padding: 20px !important;
        }

        .side-card-title {
            color: var(--admin-text-muted);
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
        }

        /* Stats Blocks */
        .grid-stats-mini {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .stat-mini-item {
            background: #f8fafc;
            border-radius: 14px;
            padding: 12px;
            text-align: center;
            border: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .stat-mini-item:hover {
            background: #f1f5f9;
            transform: translateY(-1px);
        }

        .stat-mini-num {
            font-size: 1.4rem;
            font-weight: 850;
            color: var(--admin-text-main);
            line-height: 1.2;
        }

        .stat-mini-label {
            font-size: 0.72rem;
            color: var(--admin-text-muted);
            font-weight: 700;
            margin-top: 3px;
        }

        /* Final Actions Buttons */
        .btn-approve {
            width: 100%;
            background: linear-gradient(135deg, var(--admin-secondary) 0%, var(--admin-primary) 100%);
            color: white;
            height: 50px;
            padding: 0 16px;
            border: none;
            border-radius: 14px;
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 0.94rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.24);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-approve:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(79, 70, 229, 0.35);
        }

        .btn-approve:disabled {
            background: #cbd5e1;
            color: #94a3b8;
            box-shadow: none;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-reject {
            width: 100%;
            background: #fff;
            color: var(--status-rejected-text);
            border: 1px solid var(--status-rejected-border);
            height: 50px;
            padding: 0 16px;
            border-radius: 14px;
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
            transform: translateY(-1px);
        }

        /* Progress Bar Section */
        .summary-progress-wrapper {
            margin-top: 14px;
        }

        .progress-bar-container {
            width: 100%;
            height: 8px;
            background: #f1f5f9;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #22c55e 0%, #4f46e5 100%);
            border-radius: 999px;
            transition: width 0.4s ease;
        }

        /* Warning structure incompleteness */
        .warning-structure {
            font-size: 0.74rem;
            color: var(--status-rejected-text);
            margin-top: 10px;
            background: var(--status-rejected-bg);
            padding: 12px;
            border-radius: 12px;
            border: 1px solid var(--status-rejected-border);
        }

        .warning-structure ul {
            margin: 6px 0 0 16px;
            padding: 0;
        }

        .warning-structure li {
            margin-bottom: 3px;
        }

        /* Modal Dialog Customizations */
        .reject-modal .modal-content {
            border-radius: 24px;
            overflow: hidden;
            border: 1px solid var(--admin-border);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .reject-modal .modal-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--admin-border);
            padding: 20px 24px;
        }

        .reject-modal .modal-title {
            color: var(--admin-text-main);
            font-size: 1.05rem;
            font-weight: 850;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .reject-modal .modal-body {
            padding: 24px;
            background: #ffffff;
        }

        .reject-modal .form-control {
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            font-size: 0.9rem;
            padding: 12px 16px;
            min-height: 140px;
            resize: vertical;
        }

        .reject-modal .form-control:focus {
            border-color: var(--admin-secondary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.12);
            outline: none;
        }

        .reject-modal .btn-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 700;
            height: 44px;
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
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 750;
            height: 44px;
            padding: 0 20px;
            transition: all 0.2s ease;
        }

        .reject-modal .btn-submit-reject:hover {
            background: #b91c1c;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        }

        @media (max-width: 1200px) {
            .action-box {
                position: static !important;
            }
        }

        @media (max-width: 768px) {
            .module-item {
                flex-direction: column;
            }
            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }
            .status-chip {
                width: 100%;
                justify-content: center;
            }
            .btn-back {
                width: 100%;
                justify-content: center;
            }
            .module-quick-actions {
                width: 100%;
                justify-content: flex-end;
                margin-top: 10px;
            }
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="show-page-wrapper">
        {{-- Page Header --}}
        <div class="page-header">
            <a href="{{ route('admin.trainer.material.' . ($material->status === 'approved' ? 'approved' : 'approvals')) }}"
                class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Kembali
            </a>
            @if($material->status === 'approved')
                <span class="status-chip approved">
                    <span class="pulse-dot"></span>
                    Disetujui
                </span>
            @elseif($material->status === 'rejected')
                <span class="status-chip rejected">
                    <span class="pulse-dot"></span>
                    Revisi / Ditolak
                </span>
            @else
                <span class="status-chip pending">
                    <span class="pulse-dot"></span>
                    Menunggu Tinjauan
                </span>
            @endif
        </div>

        {{-- Main Content Grid --}}
        <div class="row g-4">

            {{-- ========== LEFT COLUMN ========== --}}
            <div class="col-xl-8" style="display:flex; flex-direction:column; gap:20px;">

                {{-- Preview Card (Sticky at top of content area on desktop scroll) --}}
                <div style="position: sticky; top: 90px; z-index: 10;">
                    <div class="card-custom" style="margin-bottom:0;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px;">
                            <div>
                                <h1 class="fw-bold text-dark mb-1 fs-5">{{ $material->name }}</h1>
                                <p class="text-muted mb-0" style="font-size:0.8rem; display:flex; align-items:center; gap:8px;">
                                    <span class="badge" style="background:#eef2ff; color:#4f46e5; font-weight:700; font-size:0.72rem; border:1px solid #e0e7ff;">
                                        {{ $material->category->name ?? 'Kategori Umum' }}
                                    </span>
                                    <span>·</span>
                                    <span>Diunggah {{ $material->updated_at?->format('d M Y') ?? $material->created_at->format('d M Y') }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- Player Container --}}
                        <div id="topReviewViewer" class="video-container">
                            @if($material->media && str_contains($material->media, 'mp4'))
                                <video controls controlsList="nodownload">
                                    <source src="{{ asset('storage/' . $material->media) }}" type="video/mp4">
                                </video>
                            @elseif($material->card_thumbnail)
                                <img src="{{ $material->card_thumbnail_url }}" style="width:100%; height:100%; object-fit:cover;">
                            @else
                                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; opacity:0.45; text-align:center; padding: 20px;">
                                    <i class="bi bi-camera-video" style="font-size:3.5rem; margin-bottom:12px; color: var(--admin-accent);"></i>
                                    <p style="font-size:0.84rem; font-weight: 700; margin:0;">Pilih Modul di Bawah</p>
                                    <p class="text-muted" style="font-size:0.75rem; margin-top:4px; max-width: 250px;">Klik tombol <i class="bi bi-eye"></i> pada baris modul untuk meninjau konten</p>
                                </div>
                            @endif
                        </div>

                        <div style="display:flex; align-items:center; gap:8px; margin-top: 12px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                            <div id="topReviewTitle" style="font-size:0.8rem; font-weight:800; color:#475569; flex:1;">
                                Preview Materi Course
                            </div>
                            <div id="topReviewMeta" style="font-size:0.75rem; color:#94a3b8; font-weight: 600;"></div>
                        </div>
                    </div>
                </div>

                {{-- Modules list organized by Bab / Unit --}}
                <div class="card-custom" style="margin-bottom:0;">
                    <div class="card-title-bar">
                        <h5 class="card-title-text">
                            <i class="bi bi-list-task text-primary"></i>
                            Struktur Modul Pembelajaran
                        </h5>
                        <span style="font-size:0.78rem; color:var(--admin-text-muted); font-weight:700; background: #f8fafc; padding: 4px 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            {{ $uploadedModulesCount ?? 0 }} Modul Aktif
                        </span>
                    </div>

                    {{-- Scrollable List --}}
                    <div class="scrollable-content" style="max-height: 52vh; overflow-y: auto; padding-right: 4px;">

                        @forelse($unitSummaries ?? [] as $unit)
                            @php
                                if ($unit['all_approved']) {
                                    $unitColor = ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'text' => '#166534', 'icon' => 'bi-check-circle-fill'];
                                } elseif ($unit['any_rejected']) {
                                    $unitColor = ['bg' => '#fff1f2', 'border' => '#fecaca', 'text' => '#be123c', 'icon' => 'bi-x-circle-fill'];
                                } elseif ($unit['any_pending']) {
                                    $unitColor = ['bg' => '#fffbeb', 'border' => '#fde68a', 'text' => '#92400e', 'icon' => 'bi-hourglass-split'];
                                } else {
                                    $unitColor = ['bg' => '#f8fafc', 'border' => '#e2e8f0', 'text' => '#94a3b8', 'icon' => 'bi-minus-circle'];
                                }
                            @endphp

                            {{-- Bab Header --}}
                            <div class="unit-header-bar" style="margin-top: {{ $loop->first ? '0' : '28px' }};">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span class="unit-badge" style="background:{{ $unitColor['bg'] }}; border:1px solid {{ $unitColor['border'] }}; color:{{ $unitColor['text'] }};">
                                        <i class="bi {{ $unitColor['icon'] }}"></i>
                                        {{ $unit['unit_label'] }}
                                    </span>
                                    <span style="font-size:0.78rem; color:#94a3b8; font-weight: 600;">
                                        {{ $unit['uploaded'] }}/{{ $unit['total'] }} Modul
                                    </span>
                                </div>
                                @if($material->status === 'pending_review' && $unit['any_pending'])
                                    <form method="POST" action="{{ route('admin.trainer.material.unit.approve', $material) }}" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="unit_no" value="{{ $unit['unit_no'] }}">
                                        <button type="submit" class="module-btn-approve" style="height:30px; font-size:0.74rem; padding:0 12px; border-radius:8px; box-shadow: 0 2px 4px rgba(22, 163, 74, 0.1);">
                                            <i class="bi bi-check-all"></i> Setujui Bab {{ $unit['unit_no'] }}
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Modules loop inside this Bab --}}
                            <div style="margin-bottom:8px;">
                                @foreach($unit['modules'] as $module)
                                    @php
                                        $rawContent = trim((string) ($module->content_url ?? ''));
                                        $hasTextContent = $module->isPdf() && trim((string) ($module->description ?? '')) !== '';
                                        $isHttp = str_starts_with($rawContent, 'http://') || str_starts_with($rawContent, 'https://');
                                        $normalizedContent = ltrim((string) preg_replace('#^/?storage/#', '', $rawContent), '/');
                                        $ext = strtolower(pathinfo($normalizedContent !== '' ? $normalizedContent : $rawContent, PATHINFO_EXTENSION));
                                        $mime = strtolower((string) ($module->mime_type ?? ''));
                                        $contentUrl = null;

                                        $previewKind = 'file';
                                        if ($hasTextContent) {
                                            $previewKind = 'module-html';
                                        } elseif ($module->isQuiz()) {
                                            $previewKind = 'quiz';
                                        } elseif ($module->isVideo() || str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'], true)) {
                                            $previewKind = 'video';
                                        } elseif ($module->isPdf() || str_contains($mime, 'pdf') || $ext === 'pdf') {
                                            $previewKind = 'pdf';
                                        }

                                        if ($isHttp) {
                                            $contentUrl = $rawContent;
                                        } elseif ($normalizedContent !== '' && $rawContent !== 'quiz_submitted') {
                                            $contentUrl = route('admin.trainer.material.module.stream', [$material, $module], false);
                                        } elseif ($hasTextContent) {
                                            $contentUrl = route('admin.trainer.material.module.stream', [$material, $module], false);
                                        }

                                        $canOpenFile = !$module->isQuiz() && !empty($contentUrl) && !$hasTextContent;
                                        $canPreview = $canOpenFile || $module->isQuiz() || $hasTextContent;
                                        $hasAnyContent = $canOpenFile || $hasTextContent || $module->isQuiz();
                                        $reviewStatus = in_array(($module->review_status ?? ''), ['approved', 'rejected', 'pending_review'], true)
                                            ? $module->review_status : 'pending_review';
                                    @endphp

                                    <div class="module-item {{ $reviewStatus }}">

                                        {{-- Icon based on module type --}}
                                        <div class="module-icon">
                                            @if($module->type === 'video') <i class="bi bi-play-btn-fill"></i>
                                            @elseif($module->type === 'pdf') <i class="bi bi-file-earmark-pdf-fill"></i>
                                            @elseif($module->type === 'quiz') <i class="bi bi-patch-question-fill"></i>
                                            @else <i class="bi bi-file-text-fill"></i>
                                            @endif
                                        </div>

                                        <div class="module-desc">
                                            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:12px;">
                                                <div>
                                                    <h6>{{ $module->order_no }}. {{ $module->title }}</h6>
                                                    <div class="module-meta-info">
                                                        <span class="module-type-tag">
                                                            {{ strtoupper($module->type) }}
                                                            @if($module->duration) · {{ $module->duration }} Mnt @endif
                                                        </span>
                                                        @if(!empty($module->file_name))
                                                            <span style="color:#cbd5e1;">·</span>
                                                            <span class="module-filename-tag" title="{{ $module->file_name }}">
                                                                <i class="bi bi-paperclip me-1"></i>{{ Str::limit($module->file_name, 25) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="module-quick-actions">
                                                    @if($canPreview)
                                                        <button type="button" class="module-review-trigger module-icon-btn preview"
                                                            data-review-module-id="{{ $module->id }}"
                                                            data-review-title="{{ e($module->title) }}"
                                                            data-review-url="{{ $contentUrl }}"
                                                            data-review-kind="{{ $previewKind }}"
                                                            data-review-template-id="{{ $hasTextContent ? 'module-html-preview-' . $module->id : '' }}"
                                                            data-review-file="{{ e($module->file_name ?: basename((string) $module->content_url)) }}"
                                                            title="Preview Konten">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                    @endif
                                                    @if($canOpenFile)
                                                        <a href="{{ route('admin.trainer.material.module.stream', [$material, $module], false) }}?download=1"
                                                            class="module-icon-btn download" title="Download File">
                                                            <i class="bi bi-download"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($hasTextContent)
                                                <template id="module-html-preview-{{ $module->id }}">{!! $module->description !!}</template>
                                            @endif

                                            {{-- Status Badge and Decision stack --}}
                                            <div style="display:flex; flex-direction:column; gap:8px; margin-top:6px;">
                                                @if($hasAnyContent)
                                                    <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                                        <span class="module-review-badge {{ $reviewStatus }}">
                                                            @if($reviewStatus === 'approved')
                                                                <i class="bi bi-check-circle-fill"></i> Disetujui
                                                            @elseif($reviewStatus === 'rejected')
                                                                <i class="bi bi-x-circle-fill"></i> Perlu Revisi
                                                            @else
                                                                <i class="bi bi-hourglass-split"></i> Menunggu Tinjauan
                                                            @endif
                                                        </span>
                                                        @if($module->isQuiz())
                                                            <span style="font-size:0.75rem; color:var(--admin-text-muted); font-weight:700;">
                                                                · {{ $module->quizQuestions->count() }} Pertanyaan
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($reviewStatus === 'rejected' && !empty($module->review_rejection_reason))
                                                        <div style="font-size:0.78rem; color:var(--status-rejected-text); background:var(--status-rejected-bg); border:1px solid var(--status-rejected-border); border-radius:10px; padding:10px 14px; margin-top:6px;">
                                                            <div style="font-weight:800; margin-bottom:2px;"><i class="bi bi-chat-left-text-fill me-1"></i>Catatan Revisi:</div>
                                                            {{ $module->review_rejection_reason }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="module-tag-missing">
                                                        <i class="bi bi-exclamation-triangle-fill"></i> File Modul Belum Diunggah
                                                    </span>
                                                @endif

                                                {{-- Inline decision action buttons --}}
                                                @if($material->status === 'pending_review' && $hasAnyContent && $reviewStatus !== 'approved')
                                                    <div class="module-decision-stack">
                                                        <form method="POST" action="{{ route('admin.trainer.material.module.approve', [$material, $module]) }}" class="module-action-form">
                                                            @csrf
                                                            <button type="submit" class="module-btn-approve">
                                                                <i class="bi bi-check2-circle"></i> Setujui Modul
                                                            </button>
                                                        </form>
                                                        <button type="button" class="module-btn-reject" data-bs-toggle="collapse" data-bs-target="#rejectModuleForm-{{ $module->id }}">
                                                            <i class="bi bi-exclamation-circle"></i> Revisi
                                                        </button>
                                                    </div>

                                                    <div class="collapse module-reject-form" id="rejectModuleForm-{{ $module->id }}">
                                                        <form method="POST" action="{{ route('admin.trainer.material.module.reject', [$material, $module]) }}">
                                                            @csrf
                                                            <textarea name="rejection_reason" required minlength="10" placeholder="Tuliskan detail perbaikan atau catatan revisi yang harus dikerjakan trainer..."></textarea>
                                                            <button type="submit">
                                                                <i class="bi bi-send-fill me-1"></i> Kirim Catatan
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @empty
                            <div class="text-center py-5 text-muted">
                                <div class="empty-state-icon">
                                    <i class="bi bi-folder-x" style="font-size:2.5rem; color:#cbd5e1;"></i>
                                </div>
                                <h5 class="fw-bold mt-3 mb-1" style="color:var(--admin-text-main);">Tidak ada modul</h5>
                                <p class="text-muted mb-0 small">Belum ada modul yang ditambahkan untuk course ini.</p>
                            </div>
                        @endforelse

                    </div>
                </div>

            </div>

            {{-- ========== RIGHT COLUMN (STICKY SIDEBAR) ========== --}}
            <div class="col-xl-4">
                <div class="action-box">

                    {{-- Trainer Card Info --}}
                    <div class="card-custom side-card">
                        <div class="side-card-title">Instruktur</div>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($material->trainer?->name ?? 'T') . '&background=4f46e5&color=fff&bold=true' }}"
                                class="trainer-avatar"
                                alt="Avatar {{ $material->trainer?->name ?? 'Trainer' }}">
                            <div style="min-width:0;">
                                <div style="font-weight:800; color:var(--admin-text-main); font-size:0.9rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $material->trainer?->name ?? 'Anonim' }}
                                </div>
                                <div style="font-size:0.75rem; color:var(--admin-text-muted); font-weight:600; margin-top: 1px;">Trainer Resmi</div>
                                @if($material->trainer?->email)
                                    <div style="font-size:0.72rem; color:var(--admin-text-muted); margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        <i class="bi bi-envelope me-1"></i>{{ $material->trainer->email }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Summary & Statistics Card --}}
                    <div class="card-custom side-card">
                        <div class="side-card-title">Ringkasan Materi</div>
                        @php
                            $totalModules = collect($unitSummaries ?? [])->sum('total');
                            $totalUploaded = collect($unitSummaries ?? [])->sum('uploaded');
                            $totalApprovedM = collect($unitSummaries ?? [])->flatMap(fn($u) => $u['modules'])->filter(fn($m) => ($m->review_status ?? '') === 'approved')->count();
                            $totalRejectedM = collect($unitSummaries ?? [])->flatMap(fn($u) => $u['modules'])->filter(fn($m) => ($m->review_status ?? '') === 'rejected')->count();
                            $totalBabs = count($unitSummaries ?? []);
                            $totalPending = $totalUploaded - $totalApprovedM - $totalRejectedM;
                            
                            $progressPercent = $totalUploaded > 0 ? round(($totalApprovedM / $totalUploaded) * 100) : 0;
                        @endphp
                        
                        <div class="grid-stats-mini">
                            <div class="stat-mini-item">
                                <div class="stat-mini-num">{{ $totalBabs }}</div>
                                <div class="stat-mini-label">Total Bab</div>
                            </div>
                            <div class="stat-mini-item">
                                <div class="stat-mini-num">{{ $totalUploaded }}</div>
                                <div class="stat-mini-label">Modul Aktif</div>
                            </div>
                            <div class="stat-mini-item" style="border-left: 3px solid var(--status-approved-text);">
                                <div class="stat-mini-num" style="color:var(--status-approved-text);">{{ $totalApprovedM }}</div>
                                <div class="stat-mini-label">Disetujui</div>
                            </div>
                            <div class="stat-mini-item" style="border-left: 3px solid {{ $totalRejectedM > 0 ? 'var(--status-rejected-text)' : '#f1f5f9' }};">
                                <div class="stat-mini-num" style="color:{{ $totalRejectedM > 0 ? 'var(--status-rejected-text)' : 'var(--admin-text-muted)' }};">{{ $totalRejectedM }}</div>
                                <div class="stat-mini-label">Ditolak</div>
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

                        @if($totalPending > 0)
                            <div style="margin-top:14px; background:var(--status-pending-bg); border: 1px solid var(--status-pending-border); border-radius:10px; padding:10px 12px; display:flex; align-items:center; gap:8px; font-size:0.78rem; color:var(--status-pending-text); font-weight:750;">
                                <i class="bi bi-hourglass-split animate-pulse" style="font-size:0.95rem;"></i>
                                {{ $totalPending }} Modul Menunggu Tinjauan
                            </div>
                        @endif
                    </div>

                    {{-- Decision Card (Approve All or Reject Course) --}}
                    @if($material->status === 'pending_review')
                        <div class="card-custom side-card">
                            <div class="side-card-title">Keputusan Akhir</div>
                            
                            <form method="POST" action="{{ route('admin.trainer.material.approve', $material) }}" style="margin-bottom:12px;">
                                @csrf
                                <button type="submit" class="btn-approve" {{ !$structureCompleteness['is_complete'] ? 'disabled' : '' }}>
                                    <i class="bi bi-check-circle-fill"></i> Setujui Seluruh Materi
                                </button>
                                
                                @if(!$structureCompleteness['is_complete'])
                                    <div class="warning-structure">
                                        <div style="font-weight:800; display:flex; align-items:center; gap:5px; margin-bottom:4px;">
                                            <i class="bi bi-exclamation-triangle-fill"></i> Struktur Belum Lengkap
                                        </div>
                                        <p class="mb-1" style="font-size:0.7rem; line-height: 1.3;">Trainer belum melengkapi seluruh struktur materi:</p>
                                        <ul>
                                            @foreach(array_slice($structureCompleteness['missing_items'], 0, 3) as $item)
                                                <li>{{ $item }}</li>
                                            @endforeach
                                            @if(count($structureCompleteness['missing_items']) > 3)
                                                <li class="fw-bold">...dan {{ count($structureCompleteness['missing_items']) - 3 }} lainnya</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </form>

                            <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectCourseModal">
                                <i class="bi bi-x-circle-fill"></i> Tolak Seluruh Course
                            </button>
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    <!-- Reject Course Modal Dialog -->
    <div class="modal fade reject-modal" id="rejectCourseModal" tabindex="-1" aria-labelledby="rejectCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectCourseModalLabel">
                        <i class="bi bi-x-circle-fill text-danger"></i> Tolak Seluruh Materi Course
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.trainer.material.reject', $material) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-1">
                            <label for="rejection_reason" class="form-label" style="font-weight:750; color:var(--admin-text-main);">Catatan Revisi / Alasan Penolakan</label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" required minlength="10" placeholder="Tuliskan catatan perbaikan atau alasan penolakan untuk seluruh course ini secara rinci agar trainer mengerti..."></textarea>
                            <span class="text-muted" style="font-size:0.76rem; display:block; margin-top:8px;">
                                <i class="bi bi-info-circle me-1"></i> Trainer akan menerima notifikasi beserta catatan revisi ini untuk memperbaiki materi. Minimal 10 karakter.
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
    @php
        $quizMapForJs = collect($uploadedModules ?? [])
            ->filter(fn($m) => $m->isQuiz())
            ->mapWithKeys(function ($m) {
                return [
                    (string) $m->id => $m->quizQuestions
                        ->map(function ($q) {
                            return [
                                'question' => (string) ($q->question ?? ''),
                                'points' => (int) ($q->points ?? 0),
                                'answers' => $q->answers
                                    ->sortBy('order_no')
                                    ->map(fn($a) => [
                                        'text' => (string) ($a->answer_text ?? ''),
                                        'is_correct' => (bool) ($a->is_correct ?? false),
                                    ])
                                    ->values()->all(),
                            ];
                        })
                        ->values()->all(),
                ];
            })
            ->all();
    @endphp

    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        (function () {
            const quizMap = @json($quizMapForJs);
            const viewer = document.getElementById('topReviewViewer');
            const viewerTitle = document.getElementById('topReviewTitle');
            const viewerMeta = document.getElementById('topReviewMeta');

            function escapeHtml(v) {
                return String(v ?? '')
                    .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;').replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function showPreviewContent(content, title, meta, mode = 'file') {
                if (!viewer) return;
                viewer.classList.toggle('is-quiz', mode === 'quiz');
                viewer.classList.toggle('is-module-html', mode === 'module-html');
                viewer.innerHTML = '';
                if (content instanceof HTMLElement) { viewer.appendChild(content); }
                else { viewer.innerHTML = String(content || ''); }
                if (viewerTitle) viewerTitle.textContent = title || 'Preview Modul';
                if (viewerMeta) viewerMeta.textContent = meta || '';
                
                // Active visual feedback outline
                viewer.style.outline = '3px solid var(--admin-accent)';
                viewer.style.outlineOffset = '2px';
                setTimeout(() => { viewer.style.outline = ''; viewer.style.outlineOffset = ''; }, 700);
                
                // Smooth scroll to previewer on mobile screens
                if (window.innerWidth < 1200) {
                    viewer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            function renderModuleHtml(rawHtml) {
                const wrapper = document.createElement('div');
                wrapper.className = 'module-preview-article';
                wrapper.innerHTML = rawHtml && rawHtml.trim() !== ''
                    ? rawHtml
                    : '<p class="text-muted">Konten teks modul belum tersedia.</p>';

                wrapper.querySelectorAll('.module-code-block').forEach((block) => {
                    const lang = block.querySelector('.module-code-lang')?.value || 'plaintext';
                    const codeText = block.querySelector('code')?.textContent || '';
                    const pre = document.createElement('pre');
                    const code = document.createElement('code');
                    code.className = `language-${lang}`;
                    code.textContent = codeText;
                    pre.appendChild(code);
                    const copyBtn = document.createElement('button');
                    copyBtn.type = 'button';
                    copyBtn.className = 'module-code-copy';
                    copyBtn.textContent = 'Salin Kode';
                    copyBtn.dataset.codeText = codeText;
                    const holder = document.createElement('div');
                    holder.className = 'module-code-block';
                    holder.appendChild(pre);
                    holder.appendChild(copyBtn);
                    block.replaceWith(holder);
                });

                wrapper.querySelectorAll('.module-code-copy').forEach((btn) => {
                    btn.addEventListener('click', function () {
                        const txt = this.dataset.codeText || '';
                        navigator.clipboard.writeText(txt).then(() => {
                            const orig = this.textContent;
                            this.textContent = 'Tersalin!';
                            setTimeout(() => { this.textContent = orig; }, 1000);
                        }).catch(() => { });
                    });
                });

                wrapper.querySelectorAll('pre code').forEach((el) => {
                    if (window.hljs) window.hljs.highlightElement(el);
                });
                return wrapper;
            }

            function renderQuiz(moduleId) {
                const questions = quizMap[String(moduleId)] || [];
                if (!questions.length) return '<div class="text-muted py-3 text-center">Belum ada soal pada modul kuis ini.</div>';
                const items = questions.map((q, i) => {
                    const answers = (q.answers || []).map(a =>
                        `<div class="quiz-preview-answer ${a.is_correct ? 'is-correct' : ''}">${escapeHtml(a.text || '-')}</div>`
                    ).join('');
                    return `<div class="quiz-preview-item">
                                <p class="quiz-preview-q">${i + 1}. ${escapeHtml(q.question || '?')} ${q.points ? `<span style="font-weight:450;color:var(--admin-text-muted);">(${q.points} poin)</span>` : ''}</p>
                                <div class="quiz-preview-answers">${answers || '<div class="quiz-preview-answer">Belum ada opsi jawaban</div>'}</div>
                            </div>`;
                }).join('');
                return `<div class="quiz-preview-head"><i class="bi bi-patch-question-fill text-primary"></i>Tinjauan Soal Kuis</div><div class="quiz-preview-list">${items}</div>`;
            }

            function renderPreview(url, kind) {
                if (!url) return '<div class="text-center opacity-50 py-4"><i class="bi bi-file-earmark-x" style="font-size:3rem;"></i><p class="mt-2 mb-0">File tidak tersedia</p></div>';
                if (kind === 'video') return `<video controls controlsList="nodownload"><source src="${url}"></video>`;
                if (kind === 'pdf') return `<iframe src="${url}#toolbar=1&navpanes=0"></iframe>`;
                return `<iframe src="${url}"></iframe>`;
            }

            // Click event delegation to support dynamically loaded content previewing
            document.addEventListener('click', function (e) {
                const trigger = e.target.closest('.module-review-trigger');
                if (!trigger) return;

                const fileUrl = trigger.getAttribute('data-review-url') || '';
                const fileKind = trigger.getAttribute('data-review-kind') || 'file';
                const moduleTitle = trigger.getAttribute('data-review-title') || 'Materi';
                const fileName = trigger.getAttribute('data-review-file') || 'File';
                const moduleId = trigger.getAttribute('data-review-module-id') || '';

                if (fileKind === 'quiz') {
                    showPreviewContent(renderQuiz(moduleId), 'Tinjau Kuis: ' + moduleTitle, 'Soal Kuis', 'quiz');
                    return;
                }
                if (fileKind === 'module-html') {
                    const tmplId = trigger.getAttribute('data-review-template-id') || '';
                    const tmplEl = tmplId ? document.getElementById(tmplId) : null;
                    showPreviewContent(renderModuleHtml(tmplEl ? tmplEl.innerHTML : ''), 'Tinjau Teks: ' + moduleTitle, 'Konten Teks Modul', 'module-html');
                    return;
                }
                showPreviewContent(renderPreview(fileUrl, fileKind), 'Tinjau File: ' + moduleTitle, fileName, 'file');
            });

        })();
    </script>
@endpush