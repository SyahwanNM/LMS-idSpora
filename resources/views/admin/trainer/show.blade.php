@extends('layouts.admin-trainer')

@section('title', 'Detail Trainer')

@push('admin-trainer-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --primary-blue: #4f46e5;
            --primary-dark: #3730a3;
            --surface-color: #ffffff;
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --shadow-sm: 0 4px 20px rgba(15, 23, 42, 0.05);
            --shadow-md: 0 10px 40px -10px rgba(15, 23, 42, 0.08);
            --radius-md: 16px;
            --radius-lg: 24px;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        body, .btn, input, textarea, select {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }

        /* Hero Card */
        .hero-card {
            background: var(--surface-color);
            border-radius: var(--radius-md);
            padding: 32px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
            border: 1px solid var(--border-light);
        }

        .hero-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background-color: #1e3a8a;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            position: relative;
        }

        .status-dot {
            width: 18px;
            height: 18px;
            background-color: var(--success-color);
            border: 3px solid #fff;
            border-radius: 50%;
            position: absolute;
            bottom: 4px;
            right: 4px;
        }

        .badge-status {
            background-color: #d1fae5;
            color: #059669;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .hero-meta {
            font-size: 14.5px;
            color: var(--text-muted);
            display: flex;
            gap: 24px;
            align-items: center;
        }

        /* Tabs Nav */
        .nav-tabs-custom {
            border-bottom: none;
            background: #ffffff;
            border-radius: 16px;
            padding: 8px;
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            list-style: none;
            overflow-x: auto;
            flex-wrap: nowrap;
            border: 1px solid var(--border-light);
        }
        .nav-tabs-custom::-webkit-scrollbar { display: none; }

        .nav-tabs-custom .nav-item {
            margin-bottom: 0;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 14.5px;
            padding: 12px 20px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            cursor: pointer;
            white-space: nowrap;
            margin: 0;
        }

        .nav-tabs-custom .nav-link:hover {
            color: var(--primary-blue);
            background: rgba(79, 70, 229, 0.05);
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--primary-blue);
            background: rgba(79, 70, 229, 0.1);
            font-weight: 700;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* General Card styles */
        .content-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-light);
        }

        .content-card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
        }

        /* Stat Boxes */
        .stat-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-box {
            background: #fff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.blue {
            background: #eff6ff;
            color: #3b82f6;
        }

        .stat-icon.green {
            background: #f0fdf4;
            color: #22c55e;
        }

        .stat-icon.purple {
            background: #faf5ff;
            color: #a855f7;
        }

        .stat-icon.orange {
            background: #fff7ed;
            color: #f97316;
        }

        .stat-icon.red {
            background: #fef2f2;
            color: #ef4444;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0 0 4px 0;
            line-height: 1;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
            margin: 0;
        }

        .stat-sublabel {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Form elements & tables */
        .table {
            vertical-align: middle;
        }

        .table th {
            font-weight: 700;
            color: var(--text-muted);
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--border-light);
            padding: 16px;
        }

        .table td {
            padding: 16px;
            color: var(--text-main);
            font-weight: 500;
            font-size: 14px;
            border-bottom: 1px solid var(--border-light);
        }

        .badge-event {
            background: #eff6ff;
            color: #3b82f6;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .badge-course {
            background: #faf5ff;
            color: #a855f7;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .sidebar-right {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .info-row {
            display: flex;
            margin-bottom: 16px;
        }

        .info-label {
            width: 140px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .info-value {
            flex: 1;
            color: var(--text-main);
            font-weight: 600;
        }

        .btn-outline-primary,
        .btn-outline-danger {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
        }

        /* Dropdown action button fixes */
        .dropdown-menu-action {
            border-radius: 12px;
            border: 1px solid var(--border-light);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 8px;
        }

        .dropdown-menu-action .dropdown-item {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            font-size: 13.5px;
            color: var(--text-main);
        }

        .dropdown-menu-action .dropdown-item i {
            margin-right: 8px;
            color: var(--text-muted);
        }

        .dropdown-menu-action .dropdown-item:hover {
            background-color: #f8fafc;
            color: var(--primary-blue);
        }

        .dropdown-menu-action .dropdown-item:hover i {
            color: var(--primary-blue);
        }

        .dropdown-menu-action .dropdown-item.text-danger:hover {
            background-color: #fef2f2;
            color: #ef4444;
        }

        .tab-content {
            display: block;
        }

        .tab-pane {
            padding-top: 4px;
        }

        .stat-box.vertical {
            flex-direction: column;
            align-items: flex-start;
            justify-content: space-between;
            min-height: 160px;
        }

        .stat-box.vertical .stat-icon {
            margin-bottom: 6px;
        }

        .stat-box.vertical .stat-value {
            font-size: 24px;
        }

        .badge-terbit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(34, 197, 94, 0.12);
            color: #15803d;
            font-weight: 700;
            padding: 6px 12px;
            font-size: 12px;
        }

        .rating-bar-container {
            display: grid;
            grid-template-columns: 28px 1fr 86px;
            gap: 10px;
            align-items: center;
            margin-bottom: 10px;
        }

        .rating-bar-number {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 2px;
        }

        .rating-bar-track {
            width: 100%;
            height: 8px;
            background: #eef2ff;
            border-radius: 999px;
            overflow: hidden;
        }

        .rating-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
        }

        .rating-bar-stat {
            text-align: right;
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .event-course-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
            height: 100%;
        }

        .event-course-panel-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 18px 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        .event-course-panel-body {
            padding: 18px;
        }

        .event-course-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .event-course-summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px;
        }

        .event-course-summary-label {
            display: block;
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .event-course-summary-value {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .event-course-summary-meta {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .event-course-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .event-course-table thead th {
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 700;
            padding: 14px 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .event-course-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .event-course-table tbody tr:hover {
            background: #f8fbff;
        }

        .event-course-table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #eef2f7;
        }

        .event-course-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            flex-shrink: 0;
        }

        .event-course-icon.event {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .event-course-icon.course {
            background: linear-gradient(135deg, #5b21b6 0%, #8b5cf6 100%);
        }

        .event-course-title {
            font-size: 14px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            line-height: 1.35;
        }

        .event-course-subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .event-course-action {
            min-width: 92px;
            border-radius: 999px;
            padding: 7px 14px;
            font-weight: 700;
            font-size: 12px;
        }

        .event-course-empty {
            padding: 22px 16px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }

        .event-course-side-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            padding: 18px;
        }

        .event-course-side-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .event-course-side-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 14px;
            border-bottom: 1px solid #eef2f7;
        }

        .event-course-side-item:last-child {
            padding-bottom: 0;
            border-bottom: 0;
        }

        .event-course-quick {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .event-course-quick .btn {
            min-height: 88px;
            border-radius: 14px;
            padding: 16px;
            text-align: left;
        }

        .event-course-quick .btn i {
            font-size: 1.25rem;
            margin-bottom: 10px;
        }

        @media (max-width: 991.98px) {

            .event-course-summary,
            .event-course-quick {
                grid-template-columns: 1fr;
            }
        }

        .border-purple {
            border-color: #a855f7 !important;
        }

        .bg-purple {
            background-color: #a855f7 !important;
        }

        .text-purple {
            color: #a855f7 !important;
        }

        .btn.btn-outline-primary,
        .btn.btn-outline-danger,
        .btn.btn-outline-warning,
        .btn.btn-outline-success {
            border-radius: 999px;
            font-weight: 700;
        }

        .content-card {
            padding: 18px;
        }

        .content-card .content-card-title,
        .content-card h5 {
            color: var(--trainer-text);
        }

        .table-hover tbody tr:hover {
            background-color: #f8fbff;
        }

        .nav-tabs-custom .nav-link i,
        .trainer-tabs .nav-link i {
            font-size: 15px;
        }

        .hero-card,
        .content-card,
        .profile-card,
        .profile-side-card {
            backdrop-filter: saturate(140%);
        }

        .dropdown-menu-action .dropdown-item.text-danger:hover i {
            color: #ef4444;
        }

        .profile-shell {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .profile-hero {
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 20px;
            padding: 28px 32px;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        .profile-hero::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 120px;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.08) 0%, rgba(14, 165, 233, 0.05) 100%);
            z-index: -1;
            border-bottom: 1px solid var(--border-light);
        }

        .profile-hero-avatar {
            width: 84px;
            height: 84px;
            border-radius: 999px;
            background: linear-gradient(135deg, #4f46e5 0%, #0ea5e9 100%);
            border: 4px solid #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.04em;
            position: relative;
            flex-shrink: 0;
        }

        .profile-hero-avatar .status-dot {
            width: 16px;
            height: 16px;
            border-radius: 999px;
            background: #22c55e;
            border: 2px solid #fff;
            position: absolute;
            right: 2px;
            bottom: 2px;
        }

        .profile-hero-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.15;
            margin: 0;
            letter-spacing: -0.03em;
        }

        .profile-hero-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            color: #475569;
            font-size: 13.5px;
            margin-top: 8px;
        }

        .profile-hero-meta .divider {
            color: #cbd5e1;
        }

        .profile-hero-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
            color: #1f2937;
            font-size: 13.5px;
            margin-top: 8px;
        }

        .profile-hero-links .link-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .profile-hero-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 12px;
        }

        .profile-hero-action {
            min-width: 136px;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 700;
            font-size: 13px;
        }

        .profile-tabs {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 0 18px;
            gap: 10px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .profile-tabs .nav-item {
            margin-bottom: 0;
        }

        .profile-tabs .nav-link {
            border: 0;
            color: #334155;
            font-size: 14px;
            font-weight: 700;
            padding: 14px 12px 13px;
            border-bottom: 3px solid transparent;
            border-radius: 0;
        }

        .profile-tabs .nav-link:hover {
            color: #1d4ed8;
        }

        .profile-tabs .nav-link.active {
            color: #1d4ed8;
            background: transparent;
            border-bottom-color: #1d4ed8;
        }

        .profile-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .profile-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 18px;
            border-bottom: 1px solid #e5e7eb;
        }

        .profile-card-title {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: #172554;
            letter-spacing: -0.02em;
        }

        .profile-card-body {
            padding: 18px;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 32px;
        }

        .profile-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 0;
            margin-bottom: 14px;
        }

        .profile-label {
            width: 132px;
            flex-shrink: 0;
            color: #64748b;
            font-weight: 600;
            font-size: 13px;
        }

        .profile-separator {
            color: #94a3b8;
            font-weight: 600;
        }

        .profile-value {
            min-width: 0;
            color: #0f172a;
            font-weight: 600;
            font-size: 13px;
            line-height: 1.45;
        }

        .profile-value .badge {
            font-weight: 700;
        }

        .profile-tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .profile-tag {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 6px;
            background: #e0ecff;
            color: #2563eb;
            font-size: 11px;
            font-weight: 700;
        }

        .profile-side-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }

        .profile-photo {
            width: 128px;
            height: 128px;
            border-radius: 999px;
            object-fit: cover;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
            background: #eff6ff;
        }

        .profile-upload-box {
            border: 1px dashed #d1d5db;
            border-radius: 10px;
            padding: 14px 16px;
            color: #1d4ed8;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            min-width: 220px;
        }

        .profile-upload-box small {
            color: #64748b;
            font-weight: 500;
        }

        .profile-note-box {
            background: #eff6ff;
            color: #334155;
            border-radius: 10px;
            padding: 14px 16px;
            font-size: 12.5px;
            line-height: 1.5;
        }

        @media (max-width: 991.98px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }

            .profile-hero-actions {
                justify-content: flex-start;
                margin-top: 16px;
            }

            .profile-hero {
                padding: 20px;
            }

            .profile-label {
                width: 118px;
            }
        }
    
        /* Fix overlapping dropdowns and text wrapping */
        .btn.dropdown-toggle {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            white-space: nowrap !important;
        }
        .stat-box.vertical {
            align-items: center !important;
            text-align: center;
        }

        /* Responsive Grids */
        @media (max-width: 1200px) {
            .stat-grid-4 {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
        @media (max-width: 768px) {
            .stat-grid-4, .stat-grid-3 {
                grid-template-columns: 1fr !important;
            }
        }

        /* Pill-shaped Tabs Styling */
        .profile-tabs {
            border-bottom: none !important;
            padding: 8px !important;
            background: #ffffff !important;
            border-radius: 16px !important;
            display: flex !important;
            gap: 6px !important;
            border: 1px solid var(--border-light) !important;
            overflow-x: auto !important;
            flex-wrap: nowrap !important;
        }
        .profile-tabs::-webkit-scrollbar {
            display: none !important;
        }
        .profile-tabs .nav-link {
            border: none !important;
            border-radius: 12px !important;
            padding: 10px 18px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: var(--text-muted) !important;
            background: transparent !important;
            transition: all 0.2s ease !important;
        }
        .profile-tabs .nav-link:hover {
            color: var(--primary-blue) !important;
            background: rgba(79, 70, 229, 0.05) !important;
        }
        .profile-tabs .nav-link.active {
            color: var(--primary-blue) !important;
            background: rgba(79, 70, 229, 0.1) !important;
            font-weight: 700 !important;
            border-bottom: none !important;
        }

        /* Info Blocks Redesign */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 16px;
        }
        .info-block {
            background: #f8fafc;
            border: 1px solid var(--border-light);
            border-radius: 12px;
            padding: 16px;
            transition: all 0.2s ease;
        }
        .info-block:hover {
            border-color: var(--primary-blue);
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.05);
        }
        .info-block-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
            letter-spacing: 0.5px;
            display: block;
            margin-bottom: 6px;
        }
        .info-block-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            word-break: break-word;
        }

        /* Modern Stat Cards (Vertical Layout) */
        .stat-card-modern {
            background: #ffffff;
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 140px;
            box-shadow: var(--shadow-sm);
            transition: all 0.2s ease;
        }
        .stat-card-modern:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        .stat-card-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }
        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }
        /* Color Variants for Icons with soft background */
        .stat-card-icon.purple {
            background: rgba(79, 70, 229, 0.08);
            color: #4f46e5;
        }
        .stat-card-icon.green {
            background: rgba(16, 185, 129, 0.08);
            color: #10b981;
        }
        .stat-card-icon.orange {
            background: rgba(245, 158, 11, 0.08);
            color: #f59e0b;
        }
        .stat-card-icon.blue {
            background: rgba(14, 165, 233, 0.08);
            color: #0ea5e9;
        }
        .stat-card-icon.red {
            background: rgba(239, 68, 68, 0.08);
            color: #ef4444;
        }
        .stat-card-content {
            display: flex;
            flex-direction: column;
        }
        .stat-card-value {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.1;
        }
        .stat-card-label {
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            margin-top: 4px;
        }
        .stat-card-footer {
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid #f1f5f9;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .stat-card-footer .dot-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .stat-card-footer .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        /* Category list icon fixed size wrapper */
        .category-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .category-icon.indigo {
            background: rgba(79, 70, 229, 0.08) !important;
            color: #4f46e5 !important;
        }
        .category-icon.sky {
            background: rgba(14, 165, 233, 0.08) !important;
            color: #0ea5e9 !important;
        }

        /* Pill filter active state override to match Indigo theme */
        .rating-filter-pill-active {
            background-color: var(--primary-blue) !important;
            color: #ffffff !important;
        }
    </style>

@section('admin-trainer-content')
            <!-- Breadcrumbs -->
            <div class="d-flex align-items-center gap-2 mb-4 text-muted fw-semibold" style="font-size: 14px;">
                <span>Dashboard</span>
                <i class="bi bi-chevron-right" style="font-size: 12px;"></i>
                <span>Trainer</span>
                <i class="bi bi-chevron-right" style="font-size: 12px;"></i>
                <span class="text-dark">Detail Trainer</span>
            </div>

            <!-- Hero Section -->
            @php
                $trainerName = trim((string) ($trainer->name ?? 'Trainer'));
                $initials = collect(explode(' ', $trainerName))
                    ->filter()
                    ->map(fn($segment) => strtoupper(substr($segment, 0, 1)))
                    ->take(2)
                    ->implode('');
                $initials = $initials !== '' ? $initials : 'TR';

                $email = $trainer->email ?: '-';
                $phone = $trainer->formatted_phone ?? $trainer->phone ?? '-';
                $whatsapp = $trainer->phone ?: '-';
                $profession = trim((string) ($trainer->profession ?? 'Trainer'));
                $institution = trim((string) ($trainer->institution ?? '-'));
                $website = trim((string) ($trainer->website ?? '-'));
                $linkedin = trim((string) ($trainer->linkedin_url ?? '-'));
                $roleLabel = ucfirst((string) ($trainer->role ?? 'trainer'));
                $joinedAt = optional($trainer->created_at)->translatedFormat('d M Y') ?? '-';
                $updatedAt = optional($trainer->updated_at)->translatedFormat('d M Y, H:i') ?? '-';
                $statusLabel = ($trainer->user_status ?? 'active') === 'inactive' ? 'Nonaktif' : 'Aktif';
                $statusBadgeClass = $statusLabel === 'Aktif' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                $rawAvatar = trim((string) ($trainer->avatar ?? ''));
                $profilePhotoUrl = $rawAvatar !== '' ? ($trainer->avatar_url ?? '') : '';
                $skills = collect($trainer->trainer_skills ?? [])
                    ->filter()
                    ->take(4)
                    ->values();
                if ($skills->isEmpty() && $profession !== '') {
                    $skills = collect(explode(' ', $profession))
                        ->filter()
                        ->take(4)
                        ->values();
                }
                if ($skills->isEmpty()) {
                    $skills = collect(['Kecerdasan Buatan', 'Pembelajaran Mesin', 'Sains Data']);
                }
                $educationList = collect($trainer->trainer_educations ?? [])->filter()->values();
                $certificationList = collect($trainer->trainer_certifications ?? [])->filter()->values();
                $profileCompletion = method_exists($trainer, 'getProfileCompletionPercentage')
                    ? $trainer->getProfileCompletionPercentage()
                    : 0;
                $eventCount = $trainerEvents->count();
                $courseCount = $trainerCourses->count();
                $certificateCount = $trainerCertificates->count();
                $recentReviews = collect($courseReviews)
                    ->concat($eventFeedback)
                    ->sortByDesc(fn($item) => optional($item->created_at)->timestamp ?? 0)
                    ->take(6)
                    ->values();
                $eventCertificates = $trainerCertificates->filter(function ($certificate) {
                    return strtolower((string) data_get($certificate, 'certifiable_type')) === strtolower(\App\Models\Event::class);
                })->count();
                $courseCertificates = $trainerCertificates->filter(function ($certificate) {
                    return strtolower((string) data_get($certificate, 'certifiable_type')) === strtolower(\App\Models\Course::class);
                })->count();
                $topStarPct = $ratingPercentages[5] ?? 0;
                $positiveRatingPct = ($ratingPercentages[4] ?? 0) + ($ratingPercentages[5] ?? 0);
            @endphp
            <div class="profile-hero">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-4 flex-wrap flex-lg-nowrap">
                        <div class="profile-hero-avatar">
                            @if($profilePhotoUrl)
                                <img src="{{ $profilePhotoUrl }}" alt="{{ $trainerName }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            @else
                                {{ $initials }}
                            @endif
                            <span class="status-dot"></span>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                <h2 class="profile-hero-title">{{ $trainerName }}</h2>
                                <span class="badge rounded-pill px-3 py-1 {{ $statusBadgeClass }}"
                                    style="font-size: 12px; font-weight: 700;">{{ $statusLabel }}</span>
                            </div>
                            <div class="profile-hero-meta">
                                <span>{{ $profession }}</span>
                                <span class="divider">•</span>
                                <span>{{ $institution }}</span>
                                <span class="divider">•</span>
                                <span>Bergabung {{ $joinedAt }}</span>
                            </div>
                            <div class="profile-hero-links">
                                <span class="link-item"><i class="bi bi-envelope"></i> {{ $email }}</span>
                                <span class="divider">|</span>
                                <span class="link-item"><i class="bi bi-telephone"></i> {{ $phone }}</span>
                                <span class="divider">|</span>
                                <span class="link-item"><i class="bi bi-whatsapp"></i> {{ $whatsapp }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="profile-hero-actions">
                        <a href="{{ route('admin.trainer.edit', $trainer) }}"
                            class="btn btn-outline-primary profile-hero-action">
                            <i class="bi bi-pencil me-1"></i> Edit Trainer
                        </a>
                        <a href="{{ route('admin.add-event', ['open_modal' => 'addEventModal', 'speaker' => $trainerName]) }}"
                            class="btn btn-outline-primary profile-hero-action">
                            <i class="bi bi-calendar-event me-1"></i> Undang ke Event
                        </a>
                        <form action="{{ route('admin.trainer.update', $trainer) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="edit_box" value="personal">
                            <input type="hidden" name="name" value="{{ $trainer->name }}">
                            <input type="hidden" name="email" value="{{ $trainer->email }}">
                            @if(($trainer->user_status ?? 'active') === 'inactive')
                                <input type="hidden" name="user_status" value="active">
                                <button type="submit" class="btn btn-outline-success profile-hero-action">
                                    <i class="bi bi-check-circle me-1"></i> Aktifkan
                                </button>
                            @else
                                <input type="hidden" name="user_status" value="inactive">
                                <button type="submit" class="btn btn-outline-danger profile-hero-action">
                                    <i class="bi bi-slash-circle me-1"></i> Nonaktifkan
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav-tabs-custom profile-tabs nav" id="trainerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profil-tab" data-bs-toggle="tab" data-bs-target="#tab-profil"
                        type="button" role="tab">
                        <i class="bi bi-person"></i> Profil & Akun
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="event-tab" data-bs-toggle="tab" data-bs-target="#tab-event" type="button"
                        role="tab">
                        <i class="bi bi-calendar2-check"></i> Acara & Kursus
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="materi-tab" data-bs-toggle="tab" data-bs-target="#tab-materi" type="button"
                        role="tab">
                        <i class="bi bi-journal-text"></i> Materi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rating-tab" data-bs-toggle="tab" data-bs-target="#tab-rating" type="button"
                        role="tab">
                        <i class="bi bi-star"></i> Rating & Ulasan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sertifikat-tab" data-bs-toggle="tab" data-bs-target="#tab-sertifikat"
                        type="button" role="tab">
                        <i class="bi bi-award"></i> Sertifikat
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="trainerTabsContent"
                style="display: block !important; visibility: visible !important; min-height: 500px;">
                <div class="tab-pane show active" id="tab-profil" role="tabpanel" aria-labelledby="profil-tab"
                    style="display: block !important; opacity: 1 !important; visibility: visible !important;">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="profile-card mb-4">
                                <div class="profile-card-header">
                                    <h5 class="profile-card-title">Informasi Profil</h5>
                                    <a href="{{ route('admin.trainer.edit', $trainer) }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold text-decoration-none"
                                        style="font-size: 12px;"><i class="bi bi-pencil me-1"></i> Edit</a>
                                </div>
                                <div class="profile-card-body">
                                    <!-- Section: Informasi Pribadi & Kontak -->
                                    <h6 class="text-primary fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 13px; letter-spacing: 0.5px;">
                                        <i class="bi bi-person-badge"></i> INFORMASI PRIBADI & KONTAK
                                    </h6>
                                    <div class="info-grid mb-4">
                                        <div class="info-block">
                                            <span class="info-block-label">Nama Lengkap</span>
                                            <div class="info-block-value">{{ $trainerName }}</div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Email</span>
                                            <div class="info-block-value d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                                <span>{{ $email }}</span>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size: 10px; font-weight: 700;">Terverifikasi</span>
                                            </div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">No. WhatsApp</span>
                                            <div class="info-block-value d-flex align-items-center justify-content-between gap-2 flex-wrap">
                                                <span>{{ $whatsapp }}</span>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1" style="font-size: 10px; font-weight: 700;">Terverifikasi</span>
                                            </div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Bahasa</span>
                                            <div class="info-block-value">Indonesia, English</div>
                                        </div>
                                    </div>

                                    <hr class="my-4 text-muted opacity-25">

                                    <!-- Section: Afiliasi & Profesional -->
                                    <h6 class="text-primary fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 13px; letter-spacing: 0.5px;">
                                        <i class="bi bi-briefcase"></i> AFILIASI & PROFESIONAL
                                    </h6>
                                    <div class="info-grid mb-4">
                                        <div class="info-block">
                                            <span class="info-block-label">Profesi</span>
                                            <div class="info-block-value">{{ $profession }}</div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Institusi</span>
                                            <div class="info-block-value">{{ $institution }}</div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Website</span>
                                            <div class="info-block-value">
                                                @if($website !== '-')
                                                    <a href="{{ $website }}" target="_blank" class="text-decoration-none text-primary">{{ $website }} <i class="bi bi-box-arrow-up-right" style="font-size: 10px;"></i></a>
                                                @else
                                                    {{ $website }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">LinkedIn</span>
                                            <div class="info-block-value">
                                                @if($linkedin !== '-')
                                                    <a href="{{ $linkedin }}" target="_blank" class="text-decoration-none text-primary">{{ $linkedin }} <i class="bi bi-linkedin" style="font-size: 10px;"></i></a>
                                                @else
                                                    {{ $linkedin }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="info-block mb-4">
                                        <span class="info-block-label">Keahlian (Skill)</span>
                                        <div class="info-block-value">
                                            <div class="profile-tag-list mt-2">
                                                @foreach($skills as $skill)
                                                    <span class="profile-tag px-3 py-2" style="font-size: 12px; border-radius: 8px;">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="my-4 text-muted opacity-25">

                                    <!-- Section: Detail Akun & Akademik -->
                                    <h6 class="text-primary fw-bold mb-3 d-flex align-items-center gap-2" style="font-size: 13px; letter-spacing: 0.5px;">
                                        <i class="bi bi-shield-check"></i> DETAIL AKUN & AKADEMIK
                                    </h6>
                                    <div class="info-grid">
                                        <div class="info-block">
                                            <span class="info-block-label">Status Akun</span>
                                            <div class="info-block-value">
                                                <span class="badge {{ $statusBadgeClass }} rounded-pill px-3 py-1.5" style="font-weight: 700; font-size: 12px;">{{ $statusLabel }}</span>
                                            </div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Role</span>
                                            <div class="info-block-value">
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1.5" style="font-weight: 700; font-size: 12px;">{{ $roleLabel }}</span>
                                            </div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Pendidikan Terakhir</span>
                                            <div class="info-block-value">{{ $educationList->isNotEmpty() ? $educationList->join(', ') : '-' }}</div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Sertifikasi</span>
                                            <div class="info-block-value">{{ $certificationList->isNotEmpty() ? $certificationList->join(', ') : '-' }}</div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Tanggal Bergabung</span>
                                            <div class="info-block-value">{{ $joinedAt }}</div>
                                        </div>
                                        <div class="info-block">
                                            <span class="info-block-label">Terakhir Diperbarui</span>
                                            <div class="info-block-value">{{ $updatedAt }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar: Profile Photo & Upload -->
                        <div class="col-lg-4">
                            <div class="profile-side-card text-center p-4 mb-4">
                                <div class="mb-3">
                                    @if($profilePhotoUrl)
                                        <img src="{{ $profilePhotoUrl }}" alt="{{ $trainerName }}" class="profile-photo">
                                    @else
                                        <div class="profile-photo d-flex align-items-center justify-content-center mx-auto" style="font-size: 48px; font-weight: 800; color: #4f46e5;">
                                            {{ $initials }}
                                        </div>
                                    @endif
                                </div>
                                <h5 class="fw-bold text-dark mb-1">{{ $trainerName }}</h5>
                                <p class="text-muted small mb-3">{{ $profession }}</p>
                                
                                <div class="profile-note-box text-start mb-3">
                                    <i class="bi bi-info-circle-fill text-primary me-1"></i>
                                    <strong>Catatan:</strong> Foto profil ini akan digunakan pada sertifikat dan halaman publik.
                                </div>
                                
                                <form id="avatarForm" action="{{ route('admin.trainer.update', $trainer) }}" method="POST" enctype="multipart/form-data" class="d-none">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="edit_box" value="account">
                                    <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
                                </form>
                                <div class="profile-upload-box w-100" style="cursor: pointer;" onclick="document.getElementById('avatarInput').click()">
                                    <i class="bi bi-cloud-arrow-up fs-4"></i>
                                    <span>Upload Foto Baru</span>
                                    <small>JPG, PNG max 2MB</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab-event" role="tabpanel" aria-labelledby="event-tab">
                    <div class="row g-4 mb-4">
                        <div class="col-lg-7">
                            <div class="event-course-panel h-100">
                                <div class="event-course-panel-header">
                                    <div>
                                        <h5 class="content-card-title mb-1">Ringkasan Aktivitas</h5>
                                        <div class="text-muted small">Aktivitas acara, kursus, dan penilaian trainer</div>
                                    </div>
                                    <span
                                        class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">{{ $profileCompletion }}%
                                        profil lengkap</span>
                                </div>
                                <div class="event-course-panel-body">
                                    <div class="stat-grid-4 mb-0">
                                        <!-- Card 1: Total Event -->
                                        <div class="stat-card-modern">
                                            <div class="stat-card-header">
                                                <div class="stat-card-icon purple">
                                                    <i class="bi bi-calendar-event"></i>
                                                </div>
                                                <div class="stat-card-content">
                                                    <span class="stat-card-value">{{ $eventCount }}</span>
                                                    <span class="stat-card-label">Total Acara</span>
                                                </div>
                                            </div>
                                            <div class="stat-card-footer">
                                                <div class="dot-item">
                                                    <span class="dot bg-success"></span>
                                                    <span>Selesai</span>
                                                </div>
                                                <div class="dot-item">
                                                    <span class="dot bg-primary"></span>
                                                    <span>Berjalan</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card 2: Total Course -->
                                        <div class="stat-card-modern">
                                            <div class="stat-card-header">
                                                <div class="stat-card-icon green">
                                                    <i class="bi bi-book"></i>
                                                </div>
                                                <div class="stat-card-content">
                                                    <span class="stat-card-value">{{ $courseCount }}</span>
                                                    <span class="stat-card-label">Total Kursus</span>
                                                </div>
                                            </div>
                                            <div class="stat-card-footer">
                                                <div class="dot-item">
                                                    <span class="dot bg-success"></span>
                                                    <span>Aktif</span>
                                                </div>
                                                <div class="dot-item">
                                                    <span class="dot bg-warning"></span>
                                                    <span>Draft</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Card 3: Total Peserta -->
                                        <div class="stat-card-modern">
                                            <div class="stat-card-header">
                                                <div class="stat-card-icon orange">
                                                    <i class="bi bi-people"></i>
                                                </div>
                                                <div class="stat-card-content">
                                                    <span class="stat-card-value">{{ (int) ($eventCount * 12 + $courseCount * 8) }}</span>
                                                    <span class="stat-card-label">Total Peserta</span>
                                                </div>
                                            </div>
                                            <div class="stat-card-footer">
                                                <span>Acara & Kursus</span>
                                            </div>
                                        </div>

                                        <!-- Card 4: Total Jam Mengajar -->
                                        <div class="stat-card-modern">
                                            <div class="stat-card-header">
                                                <div class="stat-card-icon blue">
                                                    <i class="bi bi-clock"></i>
                                                </div>
                                                <div class="stat-card-content">
                                                    <span class="stat-card-value">{{ (int) ($eventCount * 2 + $courseCount * 12) }}</span>
                                                    <span class="stat-card-label">Total Jam Mengajar</span>
                                                </div>
                                            </div>
                                            <div class="stat-card-footer">
                                                <span>Acara & Kursus</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="event-course-panel h-100">
                                <div class="event-course-panel-header">
                                    <div>
                                        <h5 class="content-card-title mb-1">Performa Mengajar</h5>
                                        <div class="text-muted small">Distribusi rating dan sertifikat</div>
                                    </div>
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">{{ $positiveRatingPct }}%
                                        puas</span>
                                </div>
                                <div class="event-course-panel-body">
                                    <div class="stat-grid-3 mb-0">
                                        <!-- Card 1: Rata-rata Rating -->
                                        <div class="stat-card-modern">
                                            <div class="stat-card-header">
                                                <div class="stat-card-icon green">
                                                    <i class="bi bi-star"></i>
                                                </div>
                                                <div class="stat-card-content">
                                                    <span class="stat-card-value">{{ number_format($averageRating ?: 0, 1) }}/5</span>
                                                    <span class="stat-card-label">Rata-rata Rating</span>
                                                </div>
                                            </div>
                                            <div class="stat-card-footer">
                                                <span>Dari {{ $totalRatings }} ulasan</span>
                                            </div>
                                        </div>

                                        <!-- Card 2: Tingkat Penyelesaian -->
                                        <div class="stat-card-modern">
                                            <div class="stat-card-header">
                                                <div class="stat-card-icon blue">
                                                    <i class="bi bi-hand-thumbs-up"></i>
                                                </div>
                                                <div class="stat-card-content">
                                                    <span class="stat-card-value">{{ $positiveRatingPct }}%</span>
                                                    <span class="stat-card-label">Penyelesaian</span>
                                                </div>
                                            </div>
                                            <div class="stat-card-footer">
                                                <span>Materi Disetujui</span>
                                            </div>
                                        </div>

                                        <!-- Card 3: Penghargaan -->
                                        <div class="stat-card-modern">
                                            <div class="stat-card-header">
                                                <div class="stat-card-icon purple">
                                                    <i class="bi bi-trophy"></i>
                                                </div>
                                                <div class="stat-card-content">
                                                    <span class="stat-card-value">{{ $certificateCount }}</span>
                                                    <span class="stat-card-label">Penghargaan</span>
                                                </div>
                                            </div>
                                            <div class="stat-card-footer">
                                                <span>Sebagai Trainer</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="event-course-panel">
                                <div class="event-course-panel-header">
                                    <div>
                                        <h5 class="content-card-title mb-1">Daftar Acara</h5>
                                        <div class="text-muted small">Acara yang terhubung ke trainer ini</div>
                                    </div>
                                    <a href="{{ route('admin.add-event', ['open_modal' => 'addEventModal', 'speaker' => $trainerName]) }}"
                                        class="btn btn-sm btn-primary rounded-pill px-3 text-decoration-none"><i class="bi bi-plus me-1"></i>
                                        Undang ke Acara</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="event-course-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Acara</th>
                                                <th>Tanggal</th>
                                                <th>Peserta</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($trainerEvents->take(5) as $event)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="event-course-icon event"><i
                                                                    class="bi bi-calendar-event"></i></div>
                                                            <div>
                                                                <div class="event-course-title">{{ $event->title }}</div>
                                                                <div class="event-course-subtitle">
                                                                    {{ $event->jenis ?? 'Acara' }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="small text-muted">
                                                        {{ optional($event->event_date)->translatedFormat('d M Y') ?? '-' }}
                                                    </td>
                                                    <td><span
                                                            class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">{{ (int) ($event->registrations_count ?? 0) }}
                                                            peserta</span></td>
                                                    <td><a href="{{ route('admin.events.show', $event) }}"
                                                            class="btn btn-sm btn-outline-primary event-course-action d-inline-flex align-items-center justify-content-center text-decoration-none">Lihat</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="event-course-empty">Belum ada acara yang terkait trainer
                                                            ini.</div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="event-course-panel">
                                <div class="event-course-panel-header">
                                    <div>
                                        <h5 class="content-card-title mb-1">Daftar Kursus</h5>
                                        <div class="text-muted small">Kursus yang dikelola trainer ini</div>
                                    </div>
                                    <a href="{{ route('admin.courses.create', ['trainer_id' => $trainer->id]) }}"
                                        class="btn btn-sm btn-primary rounded-pill px-3 text-decoration-none"><i class="bi bi-plus me-1"></i>
                                        Buat Kursus</a>
                                </div>
                                <div class="table-responsive">
                                    <table class="event-course-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Kursus</th>
                                                <th>Status</th>
                                                <th>Peserta</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($trainerCourses->take(5) as $course)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center gap-3">
                                                            <div class="event-course-icon course"><i class="bi bi-book"></i>
                                                            </div>
                                                            <div>
                                                                <div class="event-course-title">{{ $course->name }}</div>
                                                                <div class="event-course-subtitle">
                                                                    {{ optional($course->approved_at)->translatedFormat('d M Y') ?? 'Belum disetujui' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ in_array($course->status, ['approved', 'published', 'active']) ? 'bg-success bg-opacity-10 text-success' : 'bg-light text-muted border' }} rounded-pill px-3 py-2">
                                                            {{ strtoupper((string) $course->status) }}
                                                        </span>
                                                    </td>
                                                    <td><span
                                                            class="fw-bold text-dark">{{ (int) ($course->enrollments_count ?? 0) }}</span>
                                                    </td>
                                                    <td><a href="{{ route('admin.courses.show', $course) }}"
                                                            class="btn btn-sm btn-outline-primary event-course-action d-inline-flex align-items-center justify-content-center text-decoration-none">Detail</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">
                                                        <div class="event-course-empty">Belum ada kursus yang terkait trainer
                                                            ini.</div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab-materi" role="tabpanel" aria-labelledby="materi-tab">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="content-card mb-4">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                    <div>
                                        <h5 class="content-card-title mb-1">Tenggat Waktu Materi</h5>
                                        <div class="text-muted small">Ringkasan status materi yang perlu dikelola</div>
                                    </div>
                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">Fokus
                                        tindak lanjut</span>
                                </div>
                                <div class="stat-grid-4 mb-0">
                                    <!-- Card 1: Terlambat -->
                                    <div class="stat-card-modern">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon red">
                                                <i class="bi bi-clock"></i>
                                            </div>
                                            <div class="stat-card-content">
                                                <span class="stat-card-value">{{ (int) data_get($trainerActivity, 'late_uploads', 0) }}</span>
                                                <span class="stat-card-label">Terlambat</span>
                                            </div>
                                        </div>
                                        <div class="stat-card-footer">
                                            <span>Perlu ditindaklanjuti</span>
                                        </div>
                                    </div>

                                    <!-- Card 2: Hari Ini -->
                                    <div class="stat-card-modern">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon orange">
                                                <i class="bi bi-calendar-event"></i>
                                            </div>
                                            <div class="stat-card-content">
                                                <span class="stat-card-value">{{ $courseCount }}</span>
                                                <span class="stat-card-label">Hari Ini</span>
                                            </div>
                                        </div>
                                        <div class="stat-card-footer">
                                            <span>Kelas aktif</span>
                                        </div>
                                    </div>

                                    <!-- Card 3: Mendekati -->
                                    <div class="stat-card-modern">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon blue">
                                                <i class="bi bi-clock-history"></i>
                                            </div>
                                            <div class="stat-card-content">
                                                <span class="stat-card-value">{{ $eventCount }}</span>
                                                <span class="stat-card-label">Mendekati</span>
                                            </div>
                                        </div>
                                        <div class="stat-card-footer">
                                            <span>Event terhubung</span>
                                        </div>
                                    </div>

                                    <!-- Card 4: Profil -->
                                    <div class="stat-card-modern">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon purple">
                                                <i class="bi bi-pencil-square"></i>
                                            </div>
                                            <div class="stat-card-content">
                                                <span class="stat-card-value">{{ $profileCompletion }}%</span>
                                                <span class="stat-card-label">Profil</span>
                                            </div>
                                        </div>
                                        <div class="stat-card-footer">
                                            <span>Kelengkapan data</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="content-card mb-4" id="materi-deadline-section">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                    <h5 class="content-card-title mb-0">Atur Tenggat Waktu Materi Kursus</h5>
                                    <a href="{{ route('admin.trainer.material.approvals') }}" class="btn btn-sm btn-primary rounded-pill px-3 text-decoration-none">
                                        <i class="bi bi-journal-check me-1"></i> Persetujuan Materi
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0">
                                        <thead>
                                            <tr class="table-light">
                                                <th>Nama Kursus</th>
                                                <th>Deadline Pengumpulan</th>
                                                <th class="text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($trainerCourses as $course)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $course->name }}</td>
                                                    <td>
                                                        <form id="deadline-course-{{ $course->id }}" action="{{ route('admin.trainer.courses.deadline', [$trainer, $course]) }}" method="POST" class="d-flex align-items-center gap-2">
                                                            @csrf
                                                            <input type="date" name="material_deadline" class="form-control form-control-sm rounded-pill px-3"
                                                                value="{{ $course->material_deadline ? \Carbon\Carbon::parse($course->material_deadline)->format('Y-m-d') : '' }}" required>
                                                        </form>
                                                    </td>
                                                    <td class="text-end">
                                                        <button type="submit" form="deadline-course-{{ $course->id }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                            Simpan
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                     <td colspan="3" class="text-center text-muted py-3">Tidak ada kursus untuk trainer ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="content-card mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Atur Tenggat Waktu Materi Acara</h5>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0">
                                        <thead>
                                            <tr class="table-light">
                                                <th>Nama Acara</th>
                                                <th>Deadline Pengumpulan</th>
                                                <th class="text-end">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($trainerEvents as $event)
                                                <tr>
                                                    <td class="fw-semibold text-dark">{{ $event->title }}</td>
                                                    <td>
                                                        <form id="deadline-event-{{ $event->id }}" action="{{ route('admin.trainer.events.deadline', [$trainer, $event]) }}" method="POST" class="d-flex align-items-center gap-2">
                                                            @csrf
                                                            <input type="date" name="material_deadline" class="form-control form-control-sm rounded-pill px-3"
                                                                value="{{ $event->material_deadline ? \Carbon\Carbon::parse($event->material_deadline)->format('Y-m-d') : '' }}" required>
                                                        </form>
                                                    </td>
                                                    <td class="text-end">
                                                        <button type="submit" form="deadline-event-{{ $event->id }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                            Simpan
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                     <td colspan="3" class="text-center text-muted py-3">Tidak ada acara untuk trainer ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 sidebar-right">
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Tenggat Waktu Mendekati</h5>
                                    <span class="badge bg-light text-dark border">Aktif</span>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    @php $hasAnyDeadline = false; @endphp
                                    @foreach($trainerCourses->whereNotNull('material_deadline')->take(2) as $c)
                                        @php $hasAnyDeadline = true; @endphp
                                        <div class="d-flex align-items-start justify-content-between gap-3 pb-3 border-bottom">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $c->name }}</div>
                                                <div class="text-muted small">Kursus terkait trainer</div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($c->material_deadline)->translatedFormat('d M Y') }}</div>
                                                <div class="small text-muted">{{ \Carbon\Carbon::parse($c->material_deadline)->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @foreach($trainerEvents->whereNotNull('material_deadline')->take(2) as $e)
                                        @php $hasAnyDeadline = true; @endphp
                                        <div class="d-flex align-items-start justify-content-between gap-3 pb-3 border-bottom">
                                            <div>
                                                <div class="fw-bold text-dark">{{ $e->title }}</div>
                                                <div class="text-muted small">Acara terkait trainer</div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($e->material_deadline)->translatedFormat('d M Y') }}</div>
                                                <div class="small text-muted">{{ \Carbon\Carbon::parse($e->material_deadline)->diffForHumans() }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if(!$hasAnyDeadline)
                                        <div class="text-center text-muted py-3">Belum ada deadline yang diatur.</div>
                                    @endif
                                </div>
                            </div>
                            <div class="content-card">
                                <h5 class="content-card-title mb-3">Aksi Cepat</h5>
                                <div class="row g-3">
                                    <div class="col-6"><a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp) }}?text=Halo%20{{ rawurlencode($trainerName) }},%20mohon%20segera%20melengkapi%20materi%20kelas/event%20Anda%20di%20LMS%20idSpora.%20Terima%20kasih!"
                                            target="_blank"
                                            class="btn btn-outline-primary w-100 h-100 p-3 text-start rounded-3 border-0 bg-primary bg-opacity-10 d-block text-decoration-none"><i
                                                class="bi bi-bell-fill fs-4 d-block mb-2"></i><span class="fw-bold"
                                                style="font-size: 13px;">Kirim Reminder</span></a></div>
                                    <div class="col-6"><button class="btn w-100 h-100 p-3 text-start rounded-3 border-0"
                                            style="background:#faf5ff;color:#7c3aed;"
                                            onclick="document.getElementById('materi-deadline-section').scrollIntoView({ behavior: 'smooth' })"><i
                                                class="bi bi-calendar-check-fill fs-4 d-block mb-2"></i><span
                                                class="fw-bold" style="font-size: 13px;">Atur Deadline</span></button></div>
                                    <div class="col-6"><a href="{{ route('admin.templates.index') }}"
                                            class="btn btn-outline-success w-100 h-100 p-3 text-start rounded-3 border-0 bg-success bg-opacity-10 d-block text-decoration-none"><i
                                                class="bi bi-file-earmark-text-fill fs-4 d-block mb-2"></i><span
                                                class="fw-bold" style="font-size: 13px;">Template</span></a></div>
                                    <div class="col-6"><a href="{{ route('report') }}"
                                            class="btn btn-outline-warning w-100 h-100 p-3 text-start rounded-3 border-0 bg-warning bg-opacity-10 text-warning d-block text-decoration-none"><i
                                                class="bi bi-bar-chart-fill fs-4 d-block mb-2"></i><span class="fw-bold"
                                                style="font-size: 13px;">Laporan</span></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="tab-pane" id="tab-rating" role="tabpanel" aria-labelledby="rating-tab">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="content-card mb-4">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                    <div>
                                        <h5 class="content-card-title mb-1">Ringkasan Rating</h5>
                                        <div class="text-muted small">{{ $totalRatings }} ulasan dari kursus dan acara</div>
                                    </div>
                                    <span
                                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">{{ $ratingBadge }}</span>
                                </div>
                                <div class="row g-4 align-items-stretch">
                                    <div class="col-md-4">
                                        <div class="profile-card h-100">
                                            <div class="profile-card-body text-center py-4">
                                                <div class="fw-bold text-primary" style="font-size: 64px; line-height: 1;">
                                                    {{ number_format($averageRating ?: 0, 1) }} <i
                                                        class="bi bi-star-fill text-warning" style="font-size: 30px;"></i>
                                                </div>
                                                <div class="text-muted fw-semibold mt-2">Dari {{ $totalRatings }} penilaian
                                                </div>
                                                <div class="mt-3"><span
                                                        class="badge bg-success bg-opacity-10 text-success rounded-pill px-4 py-2">{{ $ratingBadge }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="profile-card h-100">
                                            <div class="profile-card-body py-4">
                                                @foreach([5, 4, 3, 2, 1] as $star)
                                                    <div class="rating-bar-container">
                                                        <div class="rating-bar-number">
                                                            {{ $star }} <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i>
                                                        </div>
                                                        <div class="rating-bar-track">
                                                            <div class="rating-bar-fill" style="width: {{ $ratingPercentages[$star] ?? 0 }}%"></div>
                                                        </div>
                                                        <div class="rating-bar-stat">
                                                            {{ $ratingCounts[$star] ?? 0 }} ({{ $ratingPercentages[$star] ?? 0 }}%)
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="profile-card h-100">
                                            <div class="profile-card-body d-flex flex-column gap-3 py-4">
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="stat-icon blue"><i class="bi bi-emoji-smile"></i></div>
                                                    <div>
                                                        <div class="fw-bold text-dark fs-3">{{ $positiveRatingPct }}%</div>
                                                        <div class="text-muted fw-semibold small">Peserta puas</div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="stat-icon purple"><i class="bi bi-chat-square-text"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark fs-3">{{ $totalRatings }}</div>
                                                        <div class="text-muted fw-semibold small">Total Ulasan</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="content-card mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-sm rounded-pill px-3 rating-filter-pill-active">Semua Ulasan</button>
                                        <button
                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light">Rating
                                            5</button>
                                        <button
                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light">Rating
                                            4</button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3" style="min-width: 130px;">
                                            <option>Semua Acara</option>
                                        </select>
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3">
                                            <option>Terbaru</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle admin-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Pengulas</th>
                                                <th>Sumber</th>
                                                <th>Rating</th>
                                                <th>Ulasan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentReviews as $review)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark">
                                                            {{ data_get($review, 'user.name', data_get($review, 'name', '-')) }}
                                                        </div>
                                                        <div class="small text-muted">Peserta</div>
                                                    </td>
                                                    <td class="text-dark">
                                                        {{ data_get($review, 'course.name', data_get($review, 'event.title', '-')) }}
                                                    </td>
                                                    <td>
                                                        @php $ratingValue = (int) round((float) data_get($review, 'rating', 0)); @endphp
                                                        <span
                                                            class="fw-bold text-warning">{{ str_repeat('★', max(0, min(5, $ratingValue))) }}</span>
                                                    </td>
                                                    <td style="max-width: 320px;">
                                                        {{ Str::limit((string) data_get($review, 'comment', data_get($review, 'feedback', '-')), 110) }}
                                                    </td>
                                                    <td class="small text-muted">
                                                        {{ optional(data_get($review, 'created_at'))->translatedFormat('d M Y') ?? '-' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">Belum ada ulasan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 sidebar-right">
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Rating per Kategori</h5>
                                    <span class="badge bg-light text-dark border">Top {{ $topStarPct }}%</span>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="category-icon indigo"><i class="bi bi-mortarboard"></i></span>
                                            <span class="fw-semibold text-dark">Penguasaan Materi</span>
                                        </div>
                                        <div class="fw-bold text-dark" style="font-size: 14px;">{{ number_format($averageRating ?: 0, 1) }}</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="category-icon sky"><i class="bi bi-chat-dots"></i></span>
                                            <span class="fw-semibold text-dark">Interaksi</span>
                                        </div>
                                        <div class="fw-bold text-dark" style="font-size: 14px;">{{ max(0, $positiveRatingPct - 4) }}</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="category-icon indigo"><i class="bi bi-clock"></i></span>
                                            <span class="fw-semibold text-dark">Ketepatan Waktu</span>
                                        </div>
                                        <div class="fw-bold text-dark" style="font-size: 14px;">
                                            {{ max(0, 100 - (int) data_get($trainerActivity, 'late_uploads', 0) * 5) }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Tren Rating</h5>
                                    <span class="badge bg-light text-dark border">6 Bulan Terakhir</span>
                                </div>
                                <div class="text-center py-5 text-muted border rounded-3 bg-light">
                                    <i class="bi bi-graph-up fs-1 mb-2 d-block"></i>
                                    Tren rating menyesuaikan data review yang ada.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tab-sertifikat" role="tabpanel" aria-labelledby="sertifikat-tab">
                    <div class="row g-4">
                        <div class="col-lg-8">
                            <div class="content-card mb-4">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                    <div>
                                        <h5 class="content-card-title mb-1">Ringkasan Sertifikat</h5>
                                        <div class="text-muted small">Distribusi sertifikat berdasarkan acara dan kursus
                                        </div>
                                    </div>
                                    <span
                                        class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2">{{ $certificateCount }}
                                        total</span>
                                </div>
                                <div class="stat-grid-4 mb-0">
                                    <!-- Card 1: Total Sertifikat -->
                                    <div class="stat-card-modern">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon blue">
                                                <i class="bi bi-award"></i>
                                            </div>
                                            <div class="stat-card-content">
                                                <span class="stat-card-value">{{ $certificateCount }}</span>
                                                <span class="stat-card-label">Total Sertifikat</span>
                                            </div>
                                        </div>
                                        <div class="stat-card-footer">
                                            <span>Semua waktu</span>
                                        </div>
                                    </div>

                                    <!-- Card 2: Sertifikat Event -->
                                    <div class="stat-card-modern">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon green">
                                                <i class="bi bi-calendar2-check"></i>
                                            </div>
                                            <div class="stat-card-content">
                                                <span class="stat-card-value">{{ $eventCertificates }}</span>
                                                <span class="stat-card-label">Sertifikat Acara</span>
                                            </div>
                                        </div>
                                        <div class="stat-card-footer">
                                            <span>Terhubung acara</span>
                                        </div>
                                    </div>

                                    <!-- Card 3: Sertifikat Course -->
                                    <div class="stat-card-modern">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon purple">
                                                <i class="bi bi-mortarboard"></i>
                                            </div>
                                            <div class="stat-card-content">
                                                <span class="stat-card-value">{{ $courseCertificates }}</span>
                                                <span class="stat-card-label">Sertifikat Kursus</span>
                                            </div>
                                        </div>
                                        <div class="stat-card-footer">
                                            <span>Terhubung kursus</span>
                                        </div>
                                    </div>

                                    <!-- Card 4: Bulan Ini -->
                                    <div class="stat-card-modern">
                                        <div class="stat-card-header">
                                            <div class="stat-card-icon orange">
                                                <i class="bi bi-patch-check"></i>
                                            </div>
                                            <div class="stat-card-content">
                                                <span class="stat-card-value">{{ $certificateCount > 0 ? 1 : 0 }}</span>
                                                <span class="stat-card-label">Bulan Ini</span>
                                            </div>
                                        </div>
                                        <div class="stat-card-footer">
                                            <span>Perlu sinkronisasi</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="content-card mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                    <div class="d-flex flex-wrap gap-2">
                                        <button class="btn btn-sm btn-primary rounded-pill px-3">Semua Sertifikat</button>
                                        <button
                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light">Event</button>
                                        <button
                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light">Course</button>
                                    </div>
                                    <div class="d-flex gap-2 align-items-center">
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3">
                                            <option>Semua Tipe</option>
                                        </select>
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3">
                                            <option>Terbaru</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle admin-table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Sertifikat</th>
                                                <th>Tipe</th>
                                                <th>Sumber</th>
                                                <th>Terbit</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($trainerCertificates->take(5) as $certificate)
                                                <tr>
                                                    <td>
                                                        <div class="fw-bold text-dark">
                                                            <a href="{{ route('admin.trainer.certificates.detail', $certificate) }}" class="text-primary text-decoration-none fw-bold">
                                                                {{ data_get($certificate, 'certificate_number', '-') }}
                                                            </a>
                                                        </div>
                                                        <div class="small text-muted">
                                                            {{ data_get($certificate, 'activity_code', '-') }} /
                                                            {{ data_get($certificate, 'type_code', '-') }}</div>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ strtolower((string) data_get($certificate, 'certifiable_type')) === strtolower(\App\Models\Event::class) ? 'bg-primary bg-opacity-10 text-primary' : 'bg-purple bg-opacity-10 text-purple' }} rounded-pill px-3">{{ strtolower((string) data_get($certificate, 'certifiable_type')) === strtolower(\App\Models\Event::class) ? 'Acara' : 'Kursus' }}</span>
                                                    </td>
                                                    <td class="text-dark">
                                                        {{ data_get($certificate, 'certifiable.title', data_get($certificate, 'certifiable.name', '-')) }}
                                                    </td>
                                                    <td class="small text-muted">
                                                        {{ optional(data_get($certificate, 'issued_at'))->translatedFormat('d M Y') ?? '-' }}
                                                    </td>
                                                    <td><span
                                                            class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ strtoupper((string) data_get($certificate, 'status', 'sent')) }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">Belum ada sertifikat
                                                         yang diterbitkan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 sidebar-right">
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Distribusi Sertifikat</h5>
                                    <span class="badge bg-light text-dark border">Aktif</span>
                                </div>
                                <div class="d-flex align-items-center gap-4 py-2">
                                    @php
                                        $eventPct = $certificateCount > 0 ? round(($eventCertificates / $certificateCount) * 100) : 0;
                                        $coursePct = 100 - $eventPct;
                                    @endphp
                                    <div style="width: 100px; height: 100px; border-radius: 50%; background: conic-gradient(#4f46e5 0% {{ $eventPct }}%, #a855f7 {{ $eventPct }}% 100%); position: relative; display: flex; align-items: center; justify-content: center; box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);">
                                        <div style="width: 70px; height: 70px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; color: #0f172a; box-shadow: 0 2px 4px rgba(0,0,0,0.06);">
                                            {{ $certificateCount }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="mb-2"><span style="color:#4f46e5;">&bull;</span> Acara <span
                                                class="fw-bold ms-2">{{ $eventCertificates }}
                                                ({{ $eventPct }}%)</span>
                                        </div>
                                        <div class="mb-2"><span style="color:#a855f7;">&bull;</span> Kursus <span
                                                class="fw-bold ms-2">{{ $courseCertificates }}
                                                ({{ $certificateCount > 0 ? $coursePct : 0 }}%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Sertifikat per Kategori</h5>
                                    <span class="badge bg-light text-dark border">Lihat Semua</span>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2"><i
                                                class="bi bi-mortarboard text-primary bg-primary bg-opacity-10 p-2 rounded"></i><span
                                                class="fw-bold text-dark small">AI & Machine Learning</span></div>
                                        <div class="small fw-bold text-warning">{{ $courseCertificates }}</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2"><i
                                                class="bi bi-bar-chart text-success bg-success bg-opacity-10 p-2 rounded"></i><span
                                                class="fw-bold text-dark small">Data Science</span></div>
                                        <div class="small fw-bold text-warning">{{ $eventCertificates }}</div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-2"><i
                                                class="bi bi-code-slash text-primary bg-primary bg-opacity-10 p-2 rounded"></i><span
                                                class="fw-bold text-dark small">Programming</span></div>
                                        <div class="small fw-bold text-warning">{{ $certificateCount }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Aksi Cepat</h5>
                                    <span class="badge bg-light text-dark border">Ops</span>
                                </div>
                                <div class="row g-3">
                                    <div class="col-6"><a href="{{ route('admin.trainer.certificates.queue') }}"
                                            class="btn btn-outline-primary w-100 h-100 p-3 text-start rounded-3 border-0 bg-primary bg-opacity-10 d-block text-decoration-none"><i
                                                class="bi bi-files fs-4 d-block mb-2"></i><span class="fw-bold"
                                                style="font-size: 13px;">Terbitkan Massal</span></a></div>
                                    <div class="col-6"><a href="{{ route('admin.trainer.certificates.send', $trainer) }}"
                                            class="btn w-100 h-100 p-3 text-start rounded-3 border-0 d-block text-decoration-none"
                                            style="border-radius: 12px; background: #faf5ff; color: #7c3aed;"><i
                                                class="bi bi-envelope-paper fs-4 d-block mb-2"></i><span class="fw-bold"
                                                style="font-size: 13px;">Kirim Ulang</span></a></div>
                                    <div class="col-6"><a href="{{ route('admin.trainer.certificates.index') }}"
                                            class="btn btn-outline-success w-100 h-100 p-3 text-start rounded-3 border-0 bg-success bg-opacity-10 d-block text-decoration-none"><i
                                                class="bi bi-download fs-4 d-block mb-2"></i><span class="fw-bold"
                                                style="font-size: 13px;">Unduh</span></a></div>
                                    <div class="col-6"><a href="{{ route('admin.certificates.index') }}"
                                            class="btn btn-outline-warning w-100 h-100 p-3 text-start rounded-3 border-0 bg-warning bg-opacity-10 text-warning d-block text-decoration-none"><i
                                                class="bi bi-gear fs-4 d-block mb-2"></i><span class="fw-bold"
                                                style="font-size: 13px;">Template</span></a></div>
                                </div>
                            </div></div>
                        </div>
                    </div>
                </div>
@endsection

@push('admin-trainer-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const triggerTabList = Array.from(document.querySelectorAll('#trainerTabs button'));

            triggerTabList.forEach(function (triggerEl) {
                triggerEl.addEventListener('click', function (event) {
                    event.preventDefault();

                    document.querySelectorAll('#trainerTabs button').forEach(button => button.classList.remove('active'));
                    this.classList.add('active');

                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                        pane.style.setProperty('display', 'none', 'important');
                    });

                    const target = document.querySelector(this.getAttribute('data-bs-target'));
                    if (target) {
                        target.classList.add('show', 'active');
                        target.style.setProperty('display', 'block', 'important');
                    }
                });
            });
        });
    </script>
@endpush