@extends('layouts.admin')

@section('title', 'Review Material - ' . $material->name)

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        :root {
            --admin-primary: #1e1b4b;
            --admin-secondary: #4338ca;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
        }

        body {
            background-color: var(--admin-bg);
        }

        html {
            scrollbar-gutter: stable;
        }

        .material-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .trainer-sidebar {
            width: 260px;
            background: var(--admin-card-bg);
            padding: 24px 16px;
            border-right: 1px solid #eee;
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .nav-menu-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--admin-text-muted);
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

        .material-main {
            flex-grow: 1;
            padding: 32px;
            overflow-x: hidden;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid #c7d2fe;
            background: #eef2ff;
            color: #312e81;
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .btn-back {
            background: #fff;
            border: 1px solid #cbd5e1;
            color: #334155;
            height: 44px;
            padding: 0 16px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-back:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        /* Card Setup */
        .card-custom {
            background: var(--admin-card-bg);
            border-radius: 16px;
            border: 1px solid var(--admin-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--admin-text-main);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Video Player */
        .video-container {
            background: #0f172a;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .video-container iframe,
        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-container.is-quiz {
            aspect-ratio: auto;
            min-height: 360px;
            max-height: 520px;
            overflow: auto;
            background: #fff;
            color: #0f172a;
            display: block;
            padding: 18px;
        }

        .video-container.is-module-html {
            aspect-ratio: auto;
            min-height: 360px;
            max-height: 520px;
            overflow: auto;
            background: #fff;
            color: #0f172a;
            display: block;
            padding: 18px;
        }

        .module-preview-article {
            color: #1e293b;
            line-height: 1.7;
            font-size: 0.95rem;
        }

        .module-preview-article h1,
        .module-preview-article h2,
        .module-preview-article h3 {
            color: #1e1b4b;
            margin: 12px 0 8px;
            line-height: 1.35;
        }

        .module-preview-article h1 {
            font-size: 1.45rem;
        }

        .module-preview-article h2 {
            font-size: 1.2rem;
        }

        .module-preview-article h3 {
            font-size: 1.05rem;
        }

        .module-preview-article p {
            margin: 0 0 10px;
        }

        .module-preview-article ul,
        .module-preview-article ol {
            margin: 0 0 12px 20px;
        }

        .module-preview-article .module-inline-image {
            margin: 12px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .module-preview-article .module-inline-image img {
            max-width: 560px;
            width: 100%;
            max-height: 360px;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .module-preview-article .module-inline-image[data-image-align="left"] {
            justify-content: flex-start;
        }

        .module-preview-article .module-inline-image[data-image-align="center"] {
            justify-content: center;
        }

        .module-preview-article .module-inline-image[data-image-align="right"] {
            justify-content: flex-end;
        }

        .module-preview-article .module-code-block {
            border: 1px solid #d7deea;
            border-radius: 10px;
            background: #f8fafc;
            margin: 12px 0;
            overflow: hidden;
        }

        .module-preview-article .module-code-block pre {
            margin: 0;
            padding: 12px 14px;
            background: #0f172a;
            color: #e2e8f0;
            overflow-x: auto;
            font-family: Consolas, Monaco, 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
        }

        .module-preview-article .module-code-copy {
            border: 1px solid #c7d1e2;
            border-radius: 6px;
            background: #fff;
            color: #334155;
            font-size: 12px;
            padding: 4px 8px;
            cursor: pointer;
            margin: 8px 12px 12px;
        }

        .module-preview-article .module-code-copy:hover {
            border-color: #1e1b4b;
            color: #1e1b4b;
        }

        .quiz-preview-head {
            font-size: 0.82rem;
            color: #64748b;
            margin-bottom: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .quiz-preview-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .quiz-preview-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            background: #f8fafc;
        }

        .quiz-preview-q {
            margin: 0 0 8px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e293b;
        }

        .quiz-preview-answers {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .quiz-preview-answer {
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 0.82rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #334155;
        }

        .quiz-preview-answer.is-correct {
            border-color: #86efac;
            background: #f0fdf4;
            color: #166534;
            font-weight: 700;
        }

        /* Module List */
        .module-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 68vh;
            overflow-y: auto;
            padding-right: 4px;
        }

        .status-board {
            margin-top: 4px;
        }

        .status-switcher {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .status-pill {
            border: 1px solid #dbe3f2;
            background: #f8fafc;
            color: #334155;
            border-radius: 999px;
            height: 36px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .status-pill:hover {
            border-color: #c7d2fe;
            background: #eef2ff;
            color: #312e81;
        }

        .status-pill.active {
            background: #312e81;
            border-color: #312e81;
            color: #fff;
        }

        .status-pill-count {
            min-width: 22px;
            height: 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            padding: 0 6px;
            background: rgba(15, 23, 42, 0.08);
            color: inherit;
        }

        .status-pill.active .status-pill-count {
            background: rgba(255, 255, 255, 0.2);
        }

        .status-panel {
            display: none;
        }

        .status-panel.active {
            display: block;
        }

        .module-item {
            border: 1px solid #e5ebf5;
            border-radius: 14px;
            padding: 14px;
            display: flex;
            gap: 14px;
            background: #ffffff;
            align-items: flex-start;
            transition: all 0.2s ease;
        }

        .module-item:hover {
            border-color: #c7d2fe;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }

        .module-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #eef2ff;
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .module-desc h6 {
            margin: 0 0 4px 0;
            font-weight: 700;
            color: #1e293b;
        }

        .module-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .module-head-left {
            min-width: 0;
        }

        .module-quick-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .module-icon-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
        }

        .module-icon-btn.preview {
            color: #2b2470;
            background: #f1f4ff;
            border-color: #d6dcff;
        }

        .module-icon-btn.download {
            color: #166534;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .module-desc p {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
        }

        .module-desc {
            flex: 1;
        }

        .module-meta {
            margin-top: 6px;
            font-size: 0.8rem;
            color: #64748b;
        }

        .module-actions {
            display: flex;
            gap: 6px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .module-decision-stack {
            display: flex;
            flex-direction: row;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .module-action-form {
            margin: 0;
        }

        .module-btn-approve,
        .module-btn-reject {
            border: 1px solid transparent;
            border-radius: 8px;
            height: 34px;
            padding: 0 11px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.77rem;
            font-weight: 700;
            cursor: pointer;
        }

        .module-btn-approve {
            color: #166534;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .module-btn-approve:hover {
            background: #dcfce7;
        }

        .module-btn-reject {
            color: #991b1b;
            background: #fef2f2;
            border-color: #fecaca;
        }

        .module-btn-reject:hover {
            background: #fee2e2;
        }

        .module-reject-form {
            margin-top: 10px;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #fee2e2;
            background: #fff7f7;
        }

        .module-reject-form textarea {
            width: 100%;
            min-height: 78px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            padding: 8px 10px;
            font-size: 0.82rem;
            resize: vertical;
            margin-bottom: 8px;
        }

        .module-review-badge {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .module-review-badge.approved {
            background: #dcfce7;
            color: #166534;
        }

        .module-review-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .module-review-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .module-review-trigger {
            appearance: none;
            display: inline-flex;
            border: 0;
            background: transparent;
            padding: 0;
        }

        .module-review-trigger:hover {
            background: transparent;
        }

        .module-handoff-panel {
            border-top: 1px dashed #e2e8f0;
            padding-top: 10px;
            margin-top: 2px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .module-handoff-head {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            font-size: 0.75rem;
            color: #475569;
            font-weight: 700;
        }

        .module-handoff-state {
            color: #1e3a8a;
            font-weight: 800;
        }

        .module-handoff-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 2px;
        }

        .module-handoff-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1;
        }

        .module-handoff-pill.assigned {
            color: #1d4ed8;
            background: #eff6ff;
        }

        .module-handoff-pill.uploaded {
            color: #0f766e;
            background: #f0fdfa;
        }

        .module-handoff-pill.revision {
            color: #9a3412;
            background: #fff7ed;
        }

        .module-handoff-note {
            margin: 0;
            padding: 8px 10px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 0.76rem;
            line-height: 1.45;
        }

        .module-handoff-form,
        .module-handoff-row {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        .module-handoff-field {
            flex: 1 1 260px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 0.75rem;
            line-height: 1.3;
            min-width: 200px;
        }

        .module-handoff-file {
            flex: 1 1 240px;
            font-size: 0.72rem;
            min-width: 220px;
        }

        .module-handoff-action {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: flex-start;
        }

        .module-handoff-action form {
            margin: 0;
        }

        @media (max-width: 768px) {

            .module-handoff-field,
            .module-handoff-file {
                min-width: 100%;
                flex-basis: 100%;
            }

            .module-handoff-form .module-btn-approve,
            .module-handoff-row .module-btn-approve,
            .module-handoff-action .module-btn-approve,
            .module-handoff-action .module-btn-reject {
                width: 100%;
                justify-content: center;
            }
        }

        .module-btn {
            text-decoration: none;
            border-radius: 8px;
            height: 34px;
            padding: 0 11px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.77rem;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .module-btn-open {
            color: #1e1b4b;
            background: #eef2ff;
            border-color: #c7d2fe;
        }

        .module-btn-open:hover {
            background: #e0e7ff;
        }

        .module-btn-download {
            color: #166534;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .module-btn-download:hover {
            background: #dcfce7;
        }

        .module-tag {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            margin-top: 8px;
            width: fit-content;
        }

        .module-tag-ready {
            color: #166534;
            background: #dcfce7;
        }

        .module-tag-missing {
            color: #991b1b;
            background: #fee2e2;
        }

        .review-state {
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .review-state-title {
            font-size: 0.88rem;
            color: #334155;
            font-weight: 700;
        }

        .review-state-meta {
            font-size: 0.82rem;
            color: #64748b;
        }

        /* Sidebar Kanan (Action) */
        .trainer-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .trainer-box img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
        }

        .action-box {
            position: sticky;
            top: 32px;
        }

        .side-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
        }

        .action-box .side-card {
            margin-bottom: 14px;
        }

        .side-card-title {
            color: #475569;
            font-size: 0.88rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 12px;
        }

        .btn-approve {
            width: 100%;
            background: linear-gradient(135deg, #3949ab 0%, #1e1b4b 100%);
            color: white;
            height: 52px;
            padding: 0 14px;
            border: 1px solid transparent;
            border-radius: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 20px rgba(57, 73, 171, 0.24);
            transition: all 0.2s ease;
        }

        .btn-approve:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, #3949ab 0%, #1e1b4b 100%);
            ;
            box-shadow: 0 14px 24px rgba(30, 27, 75, 0.28);
        }

        .btn-approve:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-reject {
            width: 100%;
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
            height: 50px;
            padding: 0 12px;
            border-radius: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-reject:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .reject-modal .modal-content {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 38px rgba(15, 23, 42, 0.16);
        }

        .reject-modal .modal-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 18px 20px;
        }

        .reject-modal .modal-title {
            color: #334155;
            font-size: 1.02rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .reject-modal .btn-close {
            filter: none;
            opacity: 0.6;
            background-color: transparent;
            border-radius: 8px;
            padding: 0.45rem;
            box-shadow: none;
            outline: none;
        }

        .reject-modal .btn-close:hover,
        .reject-modal .btn-close:focus,
        .reject-modal .btn-close:focus-visible {
            background-color: #e2e8f0;
            opacity: 1;
            box-shadow: none;
            outline: none;
        }

        .reject-modal .btn-close:active {
            background-color: #e2e8f0;
            opacity: 1;
            filter: brightness(0);
            box-shadow: none;
            outline: none;
        }

        .reject-modal .modal-body {
            padding: 18px 20px;
            background: #ffffff;
        }

        .reject-modal .form-label {
            color: #334155;
            font-size: 0.9rem;
            letter-spacing: 0.1px;
            margin-bottom: 8px;
        }

        .reject-modal .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            line-height: 1.45;
            color: #1e293b;
            padding: 12px 14px;
            min-height: 132px;
            resize: vertical;
        }

        .reject-modal .form-control::placeholder {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .reject-modal .form-control:focus {
            border-color: #3949ab;
            box-shadow: 0 0 0 0.2rem rgba(57, 73, 171, 0.14);
        }

        .reject-modal .help-text {
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 8px;
            display: block;
        }

        .reject-modal .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 14px 20px 18px;
            gap: 8px;
        }

        .reject-modal .btn-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 700;
            height: 40px;
            min-width: 92px;
            padding: 0 14px;
        }

        .reject-modal .btn-cancel:hover {
            background: #f8fafc;
        }

        .reject-modal .btn-submit-reject {
            border: 1px solid #334155;
            background: #334155;
            color: #fff;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 800;
            height: 40px;
            min-width: 128px;
            padding: 0 14px;
        }

        .reject-modal .btn-submit-reject:hover {
            background: #1e293b;
            border-color: #1e293b;
        }

        .module-item-approved {
            border-color: #bbf7d0;
            background: #f8fff9;
        }

        .module-item-rejected {
            border-color: #fecaca;
            background: #fff8f8;
        }

        @media (max-width: 768px) {
            .module-item {
                flex-direction: column;
            }

            .page-header {
                align-items: flex-start;
            }

            .action-box {
                position: static;
                top: auto;
            }
        }
    </style>
@endsection


@section('content')
    <div class="material-wrapper">
        @include('admin.partials.trainer-sidebar')

        <main class="material-main">

            {{-- Flash Messages --}}
            @if(session('error'))
                <div class="alert alert-danger border-0 rounded-3 mb-4 d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-3 mb-4 d-flex align-items-center gap-2 py-3">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- Page Header --}}
            <div class="page-header mb-4">
                <a href="{{ route('admin.material.' . ($material->status === 'approved' ? 'approved' : 'approvals')) }}"
                    class="btn-back"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
                @if($material->status === 'approved')
                    <span class="status-chip" style="border-color:#bbf7d0; background:#dcfce7; color:#166534;">
                        <i class="bi bi-check-circle-fill"></i> Disetujui
                    </span>
                @elseif($material->status === 'rejected')
                    <span class="status-chip" style="border-color:#fecaca; background:#fee2e2; color:#991b1b;">
                        <i class="bi bi-x-circle-fill"></i> Ditolak
                    </span>
                @else
                    <span class="status-chip">
                        <i class="bi bi-hourglass-split"></i> Pending Review
                    </span>
                @endif
            </div>

            {{-- Main Grid: Left (8) | Right (4) --}}
            <div class="row g-4">

                {{-- ========== KOLOM KIRI ========== --}}
                <div class="col-xl-8" style="display:flex;flex-direction:column;gap:16px;">

                    {{-- Preview Card (STICKY — tetap di atas saat scroll modul) --}}
                    <div style="position:sticky;top:80px;z-index:10;">
                        <div class="card-custom" style="margin-bottom:0;">
                            <div
                                style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:14px;">
                                <div>
                                    <h1 class="fw-bold text-dark mb-1 fs-5">{{ $material->name }}</h1>
                                    <p class="text-muted mb-0" style="font-size:0.8rem;">
                                        <span class="badge me-1"
                                            style="background:#e2e8f0;color:#475569;font-weight:700;font-size:0.72rem;">
                                            {{ $material->category->name ?? 'Kategori Umum' }}
                                        </span>
                                        Diupload
                                        {{ $material->updated_at?->format('d M Y') ?? $material->created_at->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Player --}}
                            <div id="topReviewViewer" class="video-container"
                                style="border-radius:10px;overflow:hidden;margin-bottom:10px;">
                                @if($material->media && str_contains($material->media, 'mp4'))
                                    <video controls controlsList="nodownload" style="width:100%;height:100%;">
                                        <source src="{{ asset('storage/' . $material->media) }}" type="video/mp4">
                                    </video>
                                @elseif($material->card_thumbnail)
                                    <img src="{{ $material->card_thumbnail }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    <div
                                        style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;opacity:0.45;">
                                        <i class="bi bi-camera-video" style="font-size:3rem;"></i>
                                        <p class="mt-2 mb-0" style="font-size:0.78rem;">Klik <i class="bi bi-eye"></i> pada
                                            modul di bawah untuk preview</p>
                                    </div>
                                @endif
                            </div>

                            <div style="display:flex;align-items:center;gap:8px;">
                                <div id="topReviewTitle" style="font-size:0.78rem;font-weight:700;color:#475569;flex:1;">
                                    Preview materi course</div>
                                <div id="topReviewMeta" style="font-size:0.72rem;color:#94a3b8;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Modul per Bab (dengan scroll internal) --}}
                    <div class="card-custom" style="margin-bottom:0;">

                        <div
                            style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #f1f5f9;">
                            <h5 style="font-size:0.95rem;font-weight:800;color:#1e293b;margin:0;">Isi Materi</h5>
                            <span style="font-size:0.75rem;color:#64748b;font-weight:600;">{{ $uploadedModulesCount ?? 0 }}
                                modul</span>
                        </div>

                        {{-- Scrollable module list container --}}
                        <div style="max-height:52vh;overflow-y:auto;padding-right:4px;">


                            @forelse($unitSummaries ?? [] as $unit)
                                @php
                                    // Tentukan warna label bab berdasarkan status
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


                                {{-- Label Bab --}}
                                <div
                                    style="display:flex;align-items:center;gap:8px;margin-bottom:10px;margin-top:{{ $loop->first ? '0' : '24px' }};">
                                    <span style="
                                            display:inline-flex;align-items:center;gap:6px;
                                            background:{{ $unitColor['bg'] }};border:1px solid {{ $unitColor['border'] }};
                                            color:{{ $unitColor['text'] }};border-radius:8px;
                                            padding:4px 12px;font-size:0.78rem;font-weight:700;
                                        ">
                                        <i class="bi {{ $unitColor['icon'] }}" style="font-size:0.75rem;"></i>
                                        {{ $unit['unit_label'] }}
                                    </span>
                                    <span style="font-size:0.75rem;color:#94a3b8;">
                                        {{ $unit['uploaded'] }}/{{ $unit['total'] }} modul
                                    </span>
                                    @if($material->status === 'pending_review' && $unit['any_pending'])
                                        <form method="POST" action="{{ route('admin.material.unit.approve', $material) }}"
                                            style="margin:0;margin-left:auto;">
                                            @csrf
                                            <input type="hidden" name="unit_no" value="{{ $unit['unit_no'] }}">
                                            <button type="submit" class="module-btn-approve"
                                                style="height:28px; font-size:0.7rem; padding:0 10px; border-radius:6px;">
                                                <i class="bi bi-check-all"></i> Setujui Bab {{ $unit['unit_no'] }}
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                {{-- Daftar Modul dalam Bab --}}
                                <div class="module-list" style="max-height:none;gap:10px;margin-bottom:4px;">
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
                                                $contentUrl = route('admin.material.module.stream', [$material, $module], false);
                                            } elseif ($hasTextContent) {
                                                $contentUrl = route('admin.material.module.stream', [$material, $module], false);
                                            }

                                            $canOpenFile = !$module->isQuiz() && !empty($contentUrl) && !$hasTextContent;
                                            $canPreview = $canOpenFile || $module->isQuiz() || $hasTextContent;
                                            $hasAnyContent = $canOpenFile || $hasTextContent || $module->isQuiz();
                                            $reviewStatus = in_array(($module->review_status ?? ''), ['approved', 'rejected', 'pending_review'], true)
                                                ? $module->review_status : 'pending_review';
                                            $processingStatus = (string) ($module->processing_status ?? '');
                                            $handoffAssigned = in_array($processingStatus, ['assigned_to_admin_course', 'revision_requested', 'processed_uploaded', 'ready_for_publish'], true);
                                            $handoffReadyToUpload = in_array($processingStatus, ['assigned_to_admin_course', 'revision_requested'], true);
                                            $handoffReadyToReview = $processingStatus === 'processed_uploaded';
                                            $hasProcessedVideo = filled($module->processed_file_url ?? null);
                                        @endphp

                                        <div
                                            class="module-item{{ $reviewStatus === 'approved' ? ' module-item-approved' : ($reviewStatus === 'rejected' ? ' module-item-rejected' : '') }}">

                                            {{-- Ikon tipe --}}
                                            <div class="module-icon">
                                                @if($module->type === 'video') <i class="bi bi-play-fill"></i>
                                                @elseif($module->type === 'pdf') <i class="bi bi-file-pdf-fill"></i>
                                                @elseif($module->type === 'quiz') <i class="bi bi-question-circle-fill"></i>
                                                @else <i class="bi bi-file-earmark-fill"></i>
                                                @endif
                                            </div>

                                            <div class="module-desc">
                                                {{-- Header modul --}}
                                                <div class="module-head">
                                                    <div class="module-head-left">
                                                        <h6 style="font-size:0.88rem;">{{ $module->order_no }}. {{ $module->title }}
                                                        </h6>
                                                        <div
                                                            style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-top:2px;">
                                                            <span style="font-size:0.75rem;color:#94a3b8;font-weight:600;">
                                                                {{ strtoupper($module->type) }}
                                                                @if($module->duration) · {{ $module->duration }} mnt @endif
                                                            </span>
                                                            @if(!empty($module->file_name))
                                                                <span style="font-size:0.72rem;color:#cbd5e1;">·</span>
                                                                <span
                                                                    style="font-size:0.72rem;color:#94a3b8;font-style:italic;">{{ Str::limit($module->file_name, 30) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    {{-- Tombol aksi cepat --}}
                                                    <div class="module-quick-actions">
                                                        @if($canPreview)
                                                            <button type="button" class="module-review-trigger module-icon-btn preview"
                                                                data-review-module-id="{{ $module->id }}"
                                                                data-review-title="{{ e($module->title) }}"
                                                                data-review-url="{{ $contentUrl }}"
                                                                data-review-kind="{{ $previewKind }}"
                                                                data-review-template-id="{{ $hasTextContent ? 'module-html-preview-' . $module->id : '' }}"
                                                                data-review-file="{{ e($module->file_name ?: basename((string) $module->content_url)) }}"
                                                                title="Preview">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        @endif
                                                        @if($canOpenFile)
                                                            <a href="{{ route('admin.material.module.stream', [$material, $module], false) }}?download=1"
                                                                class="module-icon-btn download" title="Unduh">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>

                                                @if($hasTextContent)
                                                    <template
                                                        id="module-html-preview-{{ $module->id }}">{!! $module->description !!}</template>
                                                @endif

                                                {{-- Status badge + approve/reject per modul --}}
                                                <div style="margin-top:10px;display:flex;flex-direction:column;gap:8px;">

                                                    @if($hasAnyContent)
                                                        {{-- Badge status review (semua tipe modul termasuk quiz) --}}
                                                        <div>
                                                            <span
                                                                class="module-review-badge {{ $reviewStatus === 'approved' ? 'approved' : ($reviewStatus === 'rejected' ? 'rejected' : 'pending') }}">
                                                                @if($reviewStatus === 'approved')
                                                                    <i class="bi bi-check-circle-fill"></i> Disetujui
                                                                @elseif($reviewStatus === 'rejected')
                                                                    <i class="bi bi-x-circle-fill"></i> Ditolak
                                                                @else
                                                                    <i class="bi bi-hourglass-split"></i> Menunggu review
                                                                @endif
                                                            </span>
                                                            @if($module->isQuiz())
                                                                <span style="font-size:0.72rem;color:#64748b;margin-left:6px;">·
                                                                    {{ $module->quizQuestions->count() }} soal</span>
                                                            @endif
                                                            @if($reviewStatus === 'rejected' && !empty($module->review_rejection_reason))
                                                                <div
                                                                    style="margin-top:5px;font-size:0.75rem;color:#be123c;background:#fff1f2;border:1px solid #fecaca;border-radius:6px;padding:5px 9px;">
                                                                    <i
                                                                        class="bi bi-chat-left-text me-1"></i>{{ Str::limit($module->review_rejection_reason, 80) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="module-tag module-tag-missing"><i
                                                                class="bi bi-exclamation me-1"></i>File belum diupload</span>
                                                    @endif

                                                    {{-- Tombol Setujui/Tolak — tampil untuk SEMUA tipe modul (termasuk quiz) jika
                                                    belum approved --}}
                                                    @if($material->status === 'pending_review' && $hasAnyContent && $reviewStatus !== 'approved')
                                                        <div class="module-decision-stack">
                                                            <form method="POST"
                                                                action="{{ route('admin.material.module.approve', [$material, $module]) }}"
                                                                class="module-action-form">
                                                                @csrf
                                                                <button type="submit" class="module-btn-approve">
                                                                    <i class="bi bi-check2-circle"></i> Setujui
                                                                </button>
                                                            </form>
                                                            <button type="button" class="module-btn-reject" data-bs-toggle="collapse"
                                                                data-bs-target="#rejectModuleForm-{{ $module->id }}">
                                                                <i class="bi bi-x-circle"></i> Tolak
                                                            </button>
                                                        </div>

                                                        <div class="collapse module-reject-form"
                                                            id="rejectModuleForm-{{ $module->id }}">
                                                            <form method="POST"
                                                                action="{{ route('admin.material.module.reject', [$material, $module]) }}">
                                                                @csrf
                                                                <textarea name="rejection_reason" required minlength="10"
                                                                    placeholder="Tulis catatan revisi untuk modul ini..."></textarea>
                                                                <button type="submit" class="module-btn-reject">
                                                                    <i class="bi bi-send"></i> Kirim Catatan Revisi
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif

                                                    @if($module->isVideo() && $reviewStatus === 'approved')
                                                        <div class="module-handoff-panel">
                                                            <div class="module-handoff-head">
                                                                <i class="bi bi-arrow-left-right"></i>
                                                                <span>Handoff Admin Trainer -> Admin Course</span>
                                                                @if($processingStatus !== '')
                                                                    <span
                                                                        class="module-handoff-state">({{ str_replace('_', ' ', strtoupper($processingStatus)) }})</span>
                                                                @endif
                                                            </div>

                                                            <div class="module-handoff-summary">
                                                                @if($processingStatus === 'assigned_to_admin_course')
                                                                    <span class="module-handoff-pill assigned"><i
                                                                            class="bi bi-send-check"></i> Diserahkan ke Admin Course</span>
                                                                @elseif($processingStatus === 'revision_requested')
                                                                    <span class="module-handoff-pill revision"><i
                                                                            class="bi bi-arrow-counterclockwise"></i> Revisi Diminta</span>
                                                                @elseif($processingStatus === 'processed_uploaded')
                                                                    <span class="module-handoff-pill uploaded"><i class="bi bi-upload"></i>
                                                                        Hasil Edit Diunggah</span>
                                                                @elseif($processingStatus === 'ready_for_publish')
                                                                    <span class="module-handoff-pill uploaded"><i
                                                                            class="bi bi-check2-circle"></i> Siap Dipublikasikan</span>
                                                                @else
                                                                    <span class="module-handoff-pill assigned"><i
                                                                            class="bi bi-clock-history"></i> Menunggu Handoff</span>
                                                                @endif
                                                            </div>

                                                            @if(!empty($module->assignment_notes))
                                                                <p class="module-handoff-note"><strong>Catatan:</strong>
                                                                    {{ $module->assignment_notes }}</p>
                                                            @endif

                                                            @if(!$handoffAssigned)
                                                                <form method="POST"
                                                                    action="{{ route('admin.material.module.assign-course', [$material, $module]) }}"
                                                                    class="module-handoff-form">
                                                                    @csrf
                                                                    <input type="text" name="assignment_notes" class="module-handoff-field"
                                                                        placeholder="Catatan handoff ke admin course (opsional)">
                                                                    <button type="submit" class="module-btn-approve">
                                                                        <i class="bi bi-send-check"></i> Serahkan
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if($handoffReadyToUpload)
                                                                <form method="POST"
                                                                    action="{{ route('admin.material.module.upload-processed', [$material, $module]) }}"
                                                                    enctype="multipart/form-data" class="module-handoff-row">
                                                                    @csrf
                                                                    <input type="file" name="processed_file" class="module-handoff-file"
                                                                        accept="video/mp4,video/quicktime,video/x-matroska,video/webm"
                                                                        required>
                                                                    <button type="submit" class="module-btn-approve">
                                                                        <i class="bi bi-upload"></i> Upload Hasil Edit
                                                                    </button>
                                                                </form>
                                                            @endif

                                                            @if($handoffReadyToReview && $hasProcessedVideo)
                                                                <div class="module-handoff-action">
                                                                    <form method="POST"
                                                                        action="{{ route('admin.material.module.accept-processed', [$material, $module]) }}">
                                                                        @csrf
                                                                        <button type="submit" class="module-btn-approve">
                                                                            <i class="bi bi-check2-circle"></i> Terima Hasil Edit
                                                                        </button>
                                                                    </form>
                                                                    <form method="POST"
                                                                        action="{{ route('admin.material.module.request-revision', [$material, $module]) }}"
                                                                        class="module-handoff-form">
                                                                        @csrf
                                                                        <input type="text" name="assignment_notes"
                                                                            class="module-handoff-field" minlength="10" required
                                                                            placeholder="Alasan minta revisi hasil edit">
                                                                        <button type="submit" class="module-btn-reject">
                                                                            <i class="bi bi-arrow-counterclockwise"></i> Minta Revisi
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif

                                                </div>{{-- end status+actions --}}
                                            </div>{{-- end module-desc --}}
                                        </div>{{-- end module-item --}}

                                    @endforeach
                                </div>{{-- end module-list --}}

                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox" style="font-size:2.5rem;opacity:0.4;"></i>
                                    <p class="mt-3 mb-0 fw-semibold">Belum ada modul untuk course ini.</p>
                                </div>
                            @endforelse
                        </div>{{-- end scrollable area --}}

                    </div>{{-- end card-custom modul --}}

                </div>{{-- end col-xl-8 --}}


                {{-- ========== KOLOM KANAN (Sticky) ========== --}}
                <div class="col-xl-4">
                    <div class="action-box">

                        {{-- Trainer Info Card --}}
                        <div class="card-custom side-card" style="padding:16px;margin-bottom:14px;">
                            <div class="side-card-title">Dibuat Oleh</div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($material->trainer?->name ?? 'T') . '&background=3949ab&color=fff' }}"
                                    alt="{{ $material->trainer?->name ?? 'Trainer' }}"
                                    style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                                <div>
                                    <div style="font-weight:700;color:#1e293b;font-size:0.85rem;">
                                        {{ $material->trainer?->name ?? 'Anonim' }}</div>
                                    <div style="font-size:0.73rem;color:#64748b;">Instruktur</div>
                                    @if($material->trainer?->email)
                                        <div style="font-size:0.7rem;color:#94a3b8;">{{ $material->trainer->email }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Ringkasan Statistik --}}
                        <div class="card-custom side-card" style="padding:16px;">
                            <div class="side-card-title">Ringkasan</div>
                            @php
                                $totalModules = collect($unitSummaries ?? [])->sum('total');
                                $totalUploaded = collect($unitSummaries ?? [])->sum('uploaded');
                                $totalApprovedM = collect($unitSummaries ?? [])->flatMap(fn($u) => $u['modules'])->filter(fn($m) => ($m->review_status ?? '') === 'approved')->count();
                                $totalRejectedM = collect($unitSummaries ?? [])->flatMap(fn($u) => $u['modules'])->filter(fn($m) => ($m->review_status ?? '') === 'rejected')->count();
                                $totalBabs = count($unitSummaries ?? []);
                                $totalPending = $totalUploaded - $totalApprovedM - $totalRejectedM;
                            @endphp
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                                <div style="background:#f8fafc;border-radius:10px;padding:10px 12px;text-align:center;">
                                    <div style="font-size:1.3rem;font-weight:800;color:#1e293b;">{{ $totalBabs }}</div>
                                    <div style="font-size:0.7rem;color:#64748b;margin-top:2px;">Total Bab</div>
                                </div>
                                <div style="background:#f8fafc;border-radius:10px;padding:10px 12px;text-align:center;">
                                    <div style="font-size:1.3rem;font-weight:800;color:#1e293b;">{{ $totalUploaded }}</div>
                                    <div style="font-size:0.7rem;color:#64748b;margin-top:2px;">Modul Aktif</div>
                                </div>
                                <div style="background:#f0fdf4;border-radius:10px;padding:10px 12px;text-align:center;">
                                    <div style="font-size:1.3rem;font-weight:800;color:#166534;">{{ $totalApprovedM }}</div>
                                    <div style="font-size:0.7rem;color:#166534;margin-top:2px;">Disetujui</div>
                                </div>
                                <div
                                    style="background:{{ $totalRejectedM > 0 ? '#fff1f2' : '#f8fafc' }};border-radius:10px;padding:10px 12px;text-align:center;">
                                    <div
                                        style="font-size:1.3rem;font-weight:800;color:{{ $totalRejectedM > 0 ? '#be123c' : '#94a3b8' }};">
                                        {{ $totalRejectedM }}</div>
                                    <div
                                        style="font-size:0.7rem;color:{{ $totalRejectedM > 0 ? '#be123c' : '#94a3b8' }};margin-top:2px;">
                                        Ditolak</div>
                                </div>
                            </div>
                            @if($totalPending > 0)
                                <div
                                    style="margin-top:10px;background:#fffbeb;border-radius:8px;padding:8px 12px;display:flex;align-items:center;gap:6px;font-size:0.78rem;color:#92400e;font-weight:700;">
                                    <i class="bi bi-hourglass-split"></i>
                                    {{ $totalPending }} modul menunggu review
                                </div>
                            @endif
                        </div>

                        {{-- Final Actions --}}
                        @if($material->status === 'pending_review')
                            <div class="card-custom side-card" style="padding:16px; margin-top:14px;">
                                <div class="side-card-title">Keputusan Akhir</div>
                                <form method="POST" action="{{ route('admin.material.approve', $material) }}"
                                    style="margin-bottom:10px;">
                                    @csrf
                                    <button type="submit" class="btn-approve"
                                        {{ !$structureCompleteness['is_complete'] ? 'disabled' : '' }}>
                                        <i class="bi bi-check-circle-fill"></i> Setujui Seluruh Materi
                                    </button>
                                    @if(!$structureCompleteness['is_complete'])
                                        <div
                                            style="font-size:0.7rem; color:#be123c; margin-top:6px; background:#fff1f2; padding:8px; border-radius:8px; border:1px solid #fecaca;">
                                            <i class="bi bi-exclamation-triangle-fill"></i> <strong>Struktur Belum Lengkap</strong>
                                            <ul style="margin:4px 0 0 16px; padding:0;">
                                                @foreach(array_slice($structureCompleteness['missing_items'], 0, 3) as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                                @if(count($structureCompleteness['missing_items']) > 3)
                                                    <li>...dan {{ count($structureCompleteness['missing_items']) - 3 }} lainnya</li>
                                                @endif
                                            </ul>
                                        </div>
                                    @endif
                                </form>

                                <button type="button" class="btn-reject" data-bs-toggle="modal"
                                    data-bs-target="#rejectCourseModal">
                                    <i class="bi bi-x-circle-fill"></i> Tolak Seluruh Course
                                </button>
                            </div>
                        @endif

                    </div>
                </div>{{-- end col-xl-4 --}}

            </div>{{-- end row --}}
        </main>
    </div>

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
                // Flash border feedback
                viewer.style.outline = '3px solid #3949ab';
                viewer.style.outlineOffset = '2px';
                setTimeout(() => { viewer.style.outline = ''; viewer.style.outlineOffset = ''; }, 700);
                // Scroll hanya di mobile
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
                    copyBtn.textContent = 'Copy Code';
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
                            this.textContent = 'Copied!';
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
                            <p class="quiz-preview-q">${i + 1}. ${escapeHtml(q.question || '?')} ${q.points ? `<span style="font-weight:400;color:#64748b;">(${q.points} poin)</span>` : ''}</p>
                            <div class="quiz-preview-answers">${answers || '<div class="quiz-preview-answer">Belum ada opsi</div>'}</div>
                        </div>`;
                }).join('');
                return `<div class="quiz-preview-head">Review Soal Kuis</div><div class="quiz-preview-list">${items}</div>`;
            }

            function renderPreview(url, kind) {
                if (!url) return '<div class="text-center opacity-50 py-4"><i class="bi bi-file-earmark-x" style="font-size:3rem;"></i><p class="mt-2 mb-0">File tidak tersedia</p></div>';
                if (kind === 'video') return `<video controls controlsList="nodownload"><source src="${url}"></video>`;
                if (kind === 'pdf') return `<iframe src="${url}#toolbar=1&navpanes=0"></iframe>`;
                return `<iframe src="${url}"></iframe>`;
            }

            // Event delegation — bekerja di semua unit section
            document.addEventListener('click', function (e) {
                const trigger = e.target.closest('.module-review-trigger');
                if (!trigger) return;

                const fileUrl = trigger.getAttribute('data-review-url') || '';
                const fileKind = trigger.getAttribute('data-review-kind') || 'file';
                const moduleTitle = trigger.getAttribute('data-review-title') || 'Materi';
                const fileName = trigger.getAttribute('data-review-file') || 'File';
                const moduleId = trigger.getAttribute('data-review-module-id') || '';

                if (fileKind === 'quiz') {
                    showPreviewContent(renderQuiz(moduleId), 'Preview: ' + moduleTitle, 'Soal kuis', 'quiz');
                    return;
                }
                if (fileKind === 'module-html') {
                    const tmplId = trigger.getAttribute('data-review-template-id') || '';
                    const tmplEl = tmplId ? document.getElementById(tmplId) : null;
                    showPreviewContent(renderModuleHtml(tmplEl ? tmplEl.innerHTML : ''), 'Preview: ' + moduleTitle, 'Konten teks modul', 'module-html');
                    return;
                }
                showPreviewContent(renderPreview(fileUrl, fileKind), 'Preview: ' + moduleTitle, fileName, 'file');
            });


        })();
    </script>
@endsection