@extends('layouts.admin-trainer')

@section('title', 'Dashboard Admin Trainer')

@php
    $totalCourses = $totalCourses ?? 0;
    $totalEvents = $totalEvents ?? 0;
    $pendingReviews = $pendingReviews ?? 0;
    $approvedMaterials = $approvedMaterials ?? 0;
    $activeTrainers = $activeTrainers ?? 0;
    $approvalStats = $approvalStats ?? [
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'total' => 0,
        'pending_pct' => 0,
        'approved_pct' => 0,
        'rejected_pct' => 0,
    ];
    $metricChanges = $metricChanges ?? [];
    $topTrainers = $topTrainers ?? collect();
    $deadlineItems = $deadlineItems ?? collect();
    $feedbackItems = $feedbackItems ?? collect();
    $chartPoints = $chartPoints ?? [];
    $chartData = $chartData ?? ['labels' => [], 'course' => [], 'event' => [], 'material' => []];
    $categoryStats = $categoryStats ?? collect();
    $categoryGradient = $categoryGradient ?? '#9ca3af 0% 100%';

    $courseLast30 = $courseLast30 ?? 0;
    $eventLast30 = $eventLast30 ?? 0;
    $monthlyCreated = $courseLast30 + $eventLast30;
    $monthlyTargetVal = 10;
    $targetPct = $monthlyTargetVal > 0 ? min(100, (int) round(($monthlyCreated / $monthlyTargetVal) * 100)) : 0;

    $chartSeries = array_values(array_merge(
        (array) ($chartData['course'] ?? []),
        (array) ($chartData['event'] ?? []),
        (array) ($chartData['material'] ?? [])
    ));
    $chartSeriesInts = array_map(fn($value) => (int) $value, $chartSeries);
    $chartMaxValue = max(array_merge([0], $chartSeriesInts));
    $chartStep = $chartMaxValue > 0 ? (int) ceil($chartMaxValue / 4) : 1;
    $chartYAxis = [
        $chartStep * 4,
        $chartStep * 3,
        $chartStep * 2,
        $chartStep,
    ];

    $todayLabel = now()->translatedFormat('d M Y');
    $timeLabel = now()->translatedFormat('l, H:i') . ' WIB';

@endphp

@push('admin-trainer-styles')
    <style>
        :root {
            --dash-navy: #0f172a;
            --dash-primary: #4f46e5;
            --dash-purple: #7c3aed;
            --dash-blue: #0ea5e9;
            --dash-green: #10b981;
            --dash-orange: #f59e0b;
            --dash-red: #f43f5e;
            --dash-muted: #64748b;
            --dash-soft: #f8fafc;
            --dash-border: #f1f5f9;
            --dash-card-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.02), 0 4px 6px -4px rgba(15, 23, 42, 0.02);
        }

        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        .admin-dashboard {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 24px;
            color: var(--dash-navy);
        }

        /* Hero banner & Target widget */
        .hero-grid {
            display: grid;
            grid-template-columns: 2.2fr 1fr;
            gap: 24px;
        }
        .welcome-card {
            background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 100%);
            border: 1px solid #e0e7ff;
            border-radius: 24px;
            padding: 28px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: var(--dash-card-shadow);
        }
        .welcome-left {
            flex: 1;
            z-index: 2;
        }
        .welcome-right {
            flex-shrink: 0;
            z-index: 2;
        }
        .welcome-img {
            height: 160px;
            object-fit: contain;
            margin-left: 20px;
        }
        .welcome-title-sub {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .welcome-title-main {
            font-size: 30px;
            font-weight: 800;
            color: #1e1b4b;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .welcome-desc {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .welcome-stats {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .welcome-stat-pill {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
            min-width: 140px;
        }
        .welcome-stat-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .welcome-stat-icon.orange { background: #fff7ed; color: #f97316; }
        .welcome-stat-icon.green { background: #f0fdf4; color: #22c55e; }
        .welcome-stat-icon.blue { background: #eff6ff; color: #3b82f6; }
        .welcome-stat-num {
            font-size: 18px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.1;
        }
        .welcome-stat-label {
            font-size: 10px;
            color: #64748b;
            font-weight: 500;
        }

        .target-card {
            background: #fff;
            border: 1px solid var(--dash-border);
            border-radius: 24px;
            padding: 24px;
            box-shadow: var(--dash-card-shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .target-date-row {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
        }
        .target-date-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: #f8fafc;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .target-date-title {
            font-weight: 700;
            font-size: 13px;
            color: #1e293b;
            line-height: 1.2;
        }
        .target-date-sub {
            font-size: 11px;
            color: #64748b;
        }
        .target-body {
            margin-top: 16px;
        }
        .target-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 12px;
        }
        .target-title {
            font-size: 11px;
            font-weight: 700;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .target-value {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }
        .target-progress-container {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 8px;
        }
        .target-progress-bar {
            height: 8px;
            border-radius: 4px;
            background: #f1f5f9;
            flex: 1;
            overflow: hidden;
        }
        .target-progress-fill {
            height: 100%;
            background: var(--dash-primary);
            border-radius: 4px;
        }
        .target-sparkline {
            width: 60px;
            height: 30px;
            flex-shrink: 0;
        }
        .target-footer-text {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        /* Metric Grid */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        .metric-card-clean {
            background: #fff;
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--dash-card-shadow);
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: all 0.3s ease;
        }
        .metric-card-clean:hover {
            transform: translateY(-4px);
        }
        .metric-card-clean.theme-purple:hover {
            border-color: rgba(124, 58, 237, 0.3);
            box-shadow: 0 12px 20px -5px rgba(124, 58, 237, 0.08);
        }
        .metric-card-clean.theme-blue:hover {
            border-color: rgba(14, 165, 233, 0.3);
            box-shadow: 0 12px 20px -5px rgba(14, 165, 233, 0.08);
        }
        .metric-card-clean.theme-orange:hover {
            border-color: rgba(245, 158, 11, 0.3);
            box-shadow: 0 12px 20px -5px rgba(245, 158, 11, 0.08);
        }
        .metric-card-clean.theme-green:hover {
            border-color: rgba(16, 185, 129, 0.3);
            box-shadow: 0 12px 20px -5px rgba(16, 185, 129, 0.08);
        }
        .metric-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .metric-circle-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .metric-circle-icon.purple { background: #f3e8ff; color: #7c3aed; }
        .metric-circle-icon.blue { background: #e0f2fe; color: #0ea5e9; }
        .metric-circle-icon.orange { background: #fff7ed; color: #f59e0b; }
        .metric-circle-icon.green { background: #d1fae5; color: #10b981; }
        
        .metric-titles {
            display: flex;
            flex-direction: column;
        }
        .metric-clean-label {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
        }
        .metric-clean-val-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }
        .metric-clean-value {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1.1;
        }
        .metric-clean-suffix {
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }
        .metric-sparkline-container {
            height: 36px;
            width: 100%;
            margin: 4px 0;
        }
        .metric-sparkline-svg {
            width: 100%;
            height: 100%;
        }
        .metric-clean-change {
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .metric-clean-change.up { color: #10b981; }
        .metric-clean-change.down { color: #f43f5e; }

        /* General Dash Card */
        .dash-card {
            background: #ffffff;
            border: 1px solid var(--dash-border);
            border-radius: 24px;
            box-shadow: var(--dash-card-shadow);
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            transition: border-color 0.3s ease;
        }
        .dash-card:hover {
            border-color: rgba(15, 23, 42, 0.08);
        }
        .card-header-clean {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 4px;
        }
        .card-title-clean {
            font-size: 16px;
            font-weight: 700;
            color: var(--dash-navy);
            margin: 0;
            letter-spacing: -0.2px;
        }
        .card-link {
            color: var(--dash-primary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: gap 0.2s ease, color 0.2s ease;
        }
        .card-link:hover {
            color: var(--dash-purple);
            text-decoration: none;
            gap: 10px;
        }

        /* 3-column rows */
        .main-three-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 24px;
        }
        .bottom-three-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr;
            gap: 24px;
        }

        /* Donut Charts */
        .approval-wrap {
            display: flex;
            align-items: center;
            gap: 24px;
            justify-content: center;
            flex: 1;
        }
        .approval-donut {
            width: 170px;
            height: 170px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        .approval-donut-inner {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        /* Timeline */
        .timeline-container {
            position: relative;
            padding-left: 20px;
            margin-left: 10px;
            border-left: 2px dashed #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .timeline-item {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
        }
        .timeline-dot {
            position: absolute;
            left: -25px;
            top: 6px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid currentColor;
            background: #fff;
        }
        .timeline-dot.red { color: #ef4444; }
        .timeline-dot.yellow { color: #f59e0b; }
        .timeline-dot.green { color: #10b981; }
        .timeline-dot.blue { color: #3b82f6; }
        
        .timeline-content {
            flex: 1;
            min-width: 0;
            padding-right: 12px;
        }
        .timeline-header-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 2px;
        }
        .timeline-time-label {
            font-weight: 700;
            font-size: 13px;
            color: #1e293b;
        }
        .timeline-badge {
            font-size: 9px;
            font-weight: 800;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .timeline-title {
            font-size: 12px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1px;
        }
        .timeline-trainer {
            font-size: 11px;
            color: #64748b;
        }
        .timeline-date {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .timeline-icon-box {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }
        .timeline-icon-box.neutral {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* Top Trainers & Feedback */
        .trainer-list, .feedback-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .trainer-item {
            border: 1px solid var(--dash-border);
            border-radius: 16px;
            padding: 14px;
            display: flex;
            gap: 12px;
            align-items: center;
            transition: all 0.2s ease;
        }
        .trainer-item:hover {
            transform: translateY(-2px);
            border-color: rgba(79, 70, 229, 0.15);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.03);
        }
        .rank-icon {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 11px;
            flex-shrink: 0;
        }
        .rank-icon.gold { background: #fef3c7; color: #d97706; }
        .rank-icon.silver { background: #f1f5f9; color: #475569; }
        .rank-icon.bronze { background: #ffedd5; color: #c2410c; }
        
        .trainer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }
        .trainer-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--dash-navy);
            margin-bottom: 2px;
        }
        .trainer-meta {
            font-size: 11px;
            color: var(--dash-muted);
        }
        .score-area {
            text-align: right;
            font-size: 11px;
            color: var(--dash-muted);
            margin-left: auto;
            min-width: 60px;
            flex-shrink: 0;
        }
        .score-area strong {
            font-size: 15px;
            color: var(--dash-navy);
            font-weight: 800;
        }
        .score-bar {
            height: 4px;
            border-radius: 99px;
            background: #f1f5f9;
            overflow: hidden;
            margin-top: 4px;
        }
        .score-bar span {
            display: block;
            height: 100%;
            border-radius: 99px;
            background: var(--dash-primary);
        }

        .feedback-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--dash-border);
        }
        .feedback-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
        .stars {
            color: #fbbf24;
            font-size: 12px;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }
        .feedback-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--dash-navy);
            margin-bottom: 1px;
        }
        .feedback-name {
            font-size: 11px;
            color: var(--dash-muted);
        }
        .feedback-time {
            font-size: 11px;
            color: var(--dash-muted);
            margin-left: auto;
            flex-shrink: 0;
        }

        /* SVG Chart */
        .chart-select {
            height: 36px;
            border: 1px solid var(--dash-border);
            border-radius: 10px;
            padding: 0 12px;
            font-size: 12px;
            font-weight: 600;
            color: var(--dash-navy);
            background: #fff;
            outline: none;
            cursor: pointer;
        }
        .legend-row {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 4px;
            padding-left: 36px;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--dash-muted);
        }
        .line-chart {
            width: 100%;
            height: 220px;
            display: block;
        }
        .chart-shell {
            position: relative;
            width: 100%;
            padding: 6px 8px 0 36px;
        }
        .chart-y-axis {
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 10px;
            font-weight: 600;
            color: var(--dash-muted);
            text-align: right;
            width: 28px;
        }
        .chart-x-axis {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            font-weight: 600;
            color: var(--dash-muted);
            margin-top: 8px;
            padding: 0 8px 0 2px;
        }
        .chart-tooltip {
            position: absolute;
            background: rgba(15, 23, 42, 0.95);
            color: #fff;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 11px;
            line-height: 1.5;
            min-width: 130px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            opacity: 0;
            transform: translateY(-4px);
            transition: opacity 0.15s ease, transform 0.15s ease;
            pointer-events: none;
            z-index: 10;
            backdrop-filter: blur(4px);
        }
        .chart-tooltip.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Premium Table Style */
        .table-premium {
            width: 100%;
            border-collapse: collapse;
            background-color: #ffffff;
        }
        .table-premium th {
            font-size: 11px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            color: #64748b !important;
            background-color: #f8fafc !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 16px 24px !important;
            text-align: left;
        }
        .table-premium td {
            padding: 16px 24px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 13px !important;
            color: #334155 !important;
            vertical-align: middle !important;
        }
        .table-premium tbody tr {
            transition: background-color 0.2s ease;
        }
        .table-premium tbody tr:hover {
            background-color: #f8fafc !important;
        }
        .table-premium tr:last-child td {
            border-bottom: 0 !important;
        }

        /* Responsive Layouts */
        @media (max-width: 1400px) {
            .bottom-three-grid {
                grid-template-columns: 1.2fr 1fr;
            }
            .bottom-three-grid .activity-card {
                grid-column: span 2;
            }
        }
        @media (max-width: 1200px) {
            .hero-grid {
                grid-template-columns: 1fr;
            }
            .metric-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .main-three-grid, .bottom-three-grid {
                grid-template-columns: 1fr;
            }
            .bottom-three-grid .activity-card {
                grid-column: span 1;
            }
        }
        @media (max-width: 576px) {
            .welcome-title-main {
                font-size: 24px;
            }
            .welcome-card {
                flex-direction: column;
                align-items: stretch;
                padding: 20px;
            }
            .welcome-img {
                height: 120px;
                margin-left: 0;
                margin-top: 16px;
            }
            .metric-grid {
                grid-template-columns: 1fr;
            }
            .dash-card {
                padding: 20px;
            }
            .trainer-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .score-area {
                margin-left: 0;
                width: 100%;
                text-align: left;
            }
            .feedback-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .feedback-time {
                margin-left: 0;
                margin-top: 4px;
            }
            .queue-item-card {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }
            .queue-item-left {
                width: 100%;
            }
            .queue-item-right {
                width: 100%;
            }
            .btn-queue-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush

@section('admin-trainer-content')
    @php
        // Premium wavy sparklines when actual data is flat (all Y values are equal)
        $sparkCourse = $chartPoints['spark_course'] ?? '';
        $yCourse = [];
        foreach (explode(' ', $sparkCourse) as $p) {
            $parts = explode(',', $p);
            if (isset($parts[1])) $yCourse[] = $parts[1];
        }
        if (empty($sparkCourse) || count(array_unique($yCourse)) <= 1) {
            $sparkCourse = '0,42 20,35 40,40 60,30 80,45 100,25 120,35';
        }
        
        $sparkEvent = $chartPoints['spark_event'] ?? '';
        $yEvent = [];
        foreach (explode(' ', $sparkEvent) as $p) {
            $parts = explode(',', $p);
            if (isset($parts[1])) $yEvent[] = $parts[1];
        }
        if (empty($sparkEvent) || count(array_unique($yEvent)) <= 1) {
            $sparkEvent = '0,45 20,40 40,43 60,35 80,42 100,30 120,38';
        }
        
        $sparkPending = $chartPoints['spark_pending'] ?? '';
        $yPending = [];
        foreach (explode(' ', $sparkPending) as $p) {
            $parts = explode(',', $p);
            if (isset($parts[1])) $yPending[] = $parts[1];
        }
        if (empty($sparkPending) || count(array_unique($yPending)) <= 1) {
            $sparkPending = '0,40 20,45 40,38 60,42 80,35 100,40 120,38';
        }
        
        $sparkApproved = $chartPoints['spark_approved'] ?? '';
        $yApproved = [];
        foreach (explode(' ', $sparkApproved) as $p) {
            $parts = explode(',', $p);
            if (isset($parts[1])) $yApproved[] = $parts[1];
        }
        if (empty($sparkApproved) || count(array_unique($yApproved)) <= 1) {
            $sparkApproved = '0,38 20,42 40,35 60,40 80,38 100,45 120,42';
        }
    @endphp

    <div class="admin-dashboard">

        <!-- Row 1: Hero Card and Date/Target Card -->
        <div class="hero-grid">
            <!-- Welcome Banner -->
            <div class="welcome-card">
                <div class="welcome-left">
                    <div class="welcome-title-sub">Selamat datang kembali,</div>
                    <div class="welcome-title-main">Admin Trainer! 👋</div>
                    <div class="welcome-desc">Kelola trainer, materi, course, dan event dengan lebih efisien.</div>
                    
                    <div class="welcome-stats">
                        <div class="welcome-stat-pill">
                            <div class="welcome-stat-icon orange">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div>
                                <div class="welcome-stat-num">{{ $pendingReviews }}</div>
                                <div class="welcome-stat-label">Menunggu Review</div>
                            </div>
                        </div>
                        <div class="welcome-stat-pill">
                            <div class="welcome-stat-icon green">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div>
                                <div class="welcome-stat-num">{{ $approvedMaterials }}</div>
                                <div class="welcome-stat-label">Telah Disetujui</div>
                            </div>
                        </div>
                        <div class="welcome-stat-pill">
                            <div class="welcome-stat-icon blue">
                                <i class="bi bi-calendar3"></i>
                            </div>
                            <div>
                                <div class="welcome-stat-num">{{ $totalEvents }}</div>
                                <div class="welcome-stat-label">Event Aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="welcome-right">
                    <img src="{{ asset('aset/trainer_welcome.png') }}" alt="Welcome" class="welcome-img">
                </div>
            </div>

            <!-- Date & Target widget -->
            <div class="target-card">
                <div class="target-date-row">
                    <div class="target-date-icon">
                        <i class="bi bi-calendar3"></i>
                    </div>
                    <div>
                        <div class="target-date-title">{{ $todayLabel }}</div>
                        <div class="target-date-sub">{{ $timeLabel }}</div>
                    </div>
                </div>
                
                <div class="target-body">
                    <div class="target-header">
                        <span class="target-title">Target Bulanan</span>
                        <span class="target-value">{{ $targetPct }}%</span>
                    </div>
                    
                    <div class="target-progress-container">
                        <div class="target-progress-bar">
                            <div class="target-progress-fill" style="width: {{ $targetPct }}%;"></div>
                        </div>
                        <svg class="target-sparkline" viewBox="0 0 60 30">
                            <path d="M 0 25 Q 15 5, 30 20 T 60 5" fill="none" stroke="#22c55e" stroke-width="2.5" stroke-linecap="round" />
                        </svg>
                    </div>
                    
                    <div class="target-footer-text">{{ $monthlyCreated }} dari {{ $monthlyTargetVal }} program baru bulan ini</div>
                </div>
            </div>
        </div>

        <!-- Row 2: 4 Metric Cards -->
        <div class="metric-grid">
            <!-- Course Aktif -->
            <div class="metric-card-clean theme-purple">
                <div class="metric-header">
                    <div class="metric-circle-icon purple">
                        <i class="bi bi-journal-richtext"></i>
                    </div>
                    <div class="metric-titles">
                        <span class="metric-clean-label">Course Aktif</span>
                        <div class="metric-clean-val-row">
                            <span class="metric-clean-value">{{ $totalCourses }}</span>
                            <span class="metric-clean-suffix">Course</span>
                        </div>
                    </div>
                </div>
                <div class="metric-sparkline-container">
                    <svg class="metric-sparkline-svg" viewBox="0 0 120 50">
                        <defs>
                            <linearGradient id="grad-course" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#7c3aed" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#7c3aed" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <path d="M {{ str_replace(' ', ' L ', $sparkCourse) }}" fill="none" stroke="#7c3aed" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M {{ str_replace(' ', ' L ', $sparkCourse) }} L 120 50 L 0 50 Z" fill="url(#grad-course)" />
                    </svg>
                </div>
                @php
                    $courseChange = $metricChanges['courses'] ?? ['text' => '0 dari periode sebelumnya', 'direction' => 'up'];
                    $courseText = str_replace(['+', '-'], ['↑ ', '↓ '], $courseChange['text']);
                @endphp
                <div class="metric-clean-change {{ $courseChange['direction'] }}">
                    {{ $courseText }}
                </div>
            </div>

            <!-- Event Berjalan -->
            <div class="metric-card-clean theme-blue">
                <div class="metric-header">
                    <div class="metric-circle-icon blue">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="metric-titles">
                        <span class="metric-clean-label">Event Berjalan</span>
                        <div class="metric-clean-val-row">
                            <span class="metric-clean-value">{{ $totalEvents }}</span>
                            <span class="metric-clean-suffix">Event</span>
                        </div>
                    </div>
                </div>
                <div class="metric-sparkline-container">
                    <svg class="metric-sparkline-svg" viewBox="0 0 120 50">
                        <defs>
                            <linearGradient id="grad-event" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#0ea5e9" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#0ea5e9" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <path d="M {{ str_replace(' ', ' L ', $sparkEvent) }}" fill="none" stroke="#0ea5e9" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M {{ str_replace(' ', ' L ', $sparkEvent) }} L 120 50 L 0 50 Z" fill="url(#grad-event)" />
                    </svg>
                </div>
                @php
                    $eventChange = $metricChanges['events'] ?? ['text' => '0 dari periode sebelumnya', 'direction' => 'up'];
                    $eventText = str_replace(['+', '-'], ['↑ ', '↓ '], $eventChange['text']);
                @endphp
                <div class="metric-clean-change {{ $eventChange['direction'] }}">
                    {{ $eventText }}
                </div>
            </div>

            <!-- Menunggu Review -->
            <div class="metric-card-clean theme-orange">
                <div class="metric-header">
                    <div class="metric-circle-icon orange">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div class="metric-titles">
                        <span class="metric-clean-label">Menunggu Review</span>
                        <div class="metric-clean-val-row">
                            <span class="metric-clean-value">{{ $pendingReviews }}</span>
                            <span class="metric-clean-suffix">Materi</span>
                        </div>
                    </div>
                </div>
                <div class="metric-sparkline-container">
                    <svg class="metric-sparkline-svg" viewBox="0 0 120 50">
                        <defs>
                            <linearGradient id="grad-pending" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#f59e0b" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <path d="M {{ str_replace(' ', ' L ', $sparkPending) }}" fill="none" stroke="#f59e0b" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M {{ str_replace(' ', ' L ', $sparkPending) }} L 120 50 L 0 50 Z" fill="url(#grad-pending)" />
                    </svg>
                </div>
                @php
                    $pendingChange = $metricChanges['pending'] ?? ['text' => '0 dari periode sebelumnya', 'direction' => 'down'];
                    $pendingText = str_replace(['+', '-'], ['↑ ', '↓ '], $pendingChange['text']);
                    // Invert color for pending reviews count change (decrease is good, increase is bad)
                    $pendingColorClass = ($pendingChange['direction'] === 'down') ? 'up' : 'down';
                @endphp
                <div class="metric-clean-change {{ $pendingColorClass }}">
                    {{ $pendingText }}
                </div>
            </div>

            <!-- Sertifikat Terbit -->
            <div class="metric-card-clean theme-green">
                <div class="metric-header">
                    <div class="metric-circle-icon green">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div class="metric-titles">
                        <span class="metric-clean-label">Sertifikat Terbit</span>
                        <div class="metric-clean-val-row">
                            <span class="metric-clean-value">{{ $approvedMaterials }}</span>
                            <span class="metric-clean-suffix">Sertifikat</span>
                        </div>
                    </div>
                </div>
                <div class="metric-sparkline-container">
                    <svg class="metric-sparkline-svg" viewBox="0 0 120 50">
                        <defs>
                            <linearGradient id="grad-approved" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#10b981" stop-opacity="0.25" />
                                <stop offset="100%" stop-color="#10b981" stop-opacity="0" />
                            </linearGradient>
                        </defs>
                        <path d="M {{ str_replace(' ', ' L ', $sparkApproved) }}" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M {{ str_replace(' ', ' L ', $sparkApproved) }} L 120 50 L 0 50 Z" fill="url(#grad-approved)" />
                    </svg>
                </div>
                @php
                    $approvedChange = $metricChanges['approved'] ?? ['text' => '0 dari periode sebelumnya', 'direction' => 'up'];
                    $approvedText = str_replace(['+', '-'], ['↑ ', '↓ '], $approvedChange['text']);
                @endphp
                <div class="metric-clean-change {{ $approvedChange['direction'] }}">
                    {{ $approvedText }}
                </div>
            </div>
        </div>

        <!-- Row 3: Antrean Kerja, Status Persetujuan, Deadline Terdekat -->
        <div class="main-three-grid">
            <!-- Antrean Kerja Admin -->
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="bi bi-list-task" style="color:#2f5bff; margin-right:6px;"></i>Antrean Kerja Admin
                    </h5>
                    <a href="{{ route('admin.trainer.material.approvals') }}" class="card-link" style="font-size:12px; font-weight:600;">Lihat Semua</a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 8px;">
                    @forelse($pendingMaterialsQueue->take(3) as $item)
                        @php
                            $sentDate = \Carbon\Carbon::parse($item['date']);
                            $deadlineDate = $sentDate->copy()->addDays(3);
                            $now = \Carbon\Carbon::now();
                            
                            $iconClass = ($item['type'] === 'course') ? 'bi-file-earmark-pdf-fill' : 'bi-calendar3';
                            
                            if ($now->greaterThan($deadlineDate)) {
                                $iconBg = '#fee2e2';
                                $iconColor = '#ef4444';
                                $badgeBg = '#fee2e2';
                                $badgeColor = '#dc2626';
                                $badgeText = 'LEWAT TENGGAT';
                                $deadlineColor = '#dc2626';
                                $deadlineText = 'Lewat Tenggat';
                                $btnBg = '#fef2f2';
                                $btnColor = '#ef4444';
                            } elseif ($now->isSameDay($deadlineDate)) {
                                $iconBg = '#fef3c7';
                                $iconColor = '#f59e0b';
                                $badgeBg = '#fef3c7';
                                $badgeColor = '#b45309';
                                $badgeText = 'HARI INI';
                                $deadlineColor = '#d97706';
                                $deadlineText = 'Hari ini';
                                $btnBg = '#fffbeb';
                                $btnColor = '#f59e0b';
                            } else {
                                $diffInDays = $now->diffInDays($deadlineDate, false);
                                $iconBg = '#eff6ff';
                                $iconColor = '#3b82f6';
                                $badgeBg = '#eff6ff';
                                $badgeColor = '#3b82f6';
                                $badgeText = 'ON PROGRESS';
                                $deadlineColor = '#3b82f6';
                                $deadlineText = ($diffInDays <= 0) ? 'Hari ini' : ($diffInDays . ' hari lagi');
                                $btnBg = '#eff6ff';
                                $btnColor = '#3b82f6';
                            }
                        @endphp
                        <div class="queue-item-card" style="padding: 16px; border: 1px solid var(--dash-border); border-radius: 16px; background: #fff; display: flex; align-items: center; justify-content: space-between; gap: 16px; transition: all 0.2s ease;">
                            <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0;">
                                <div style="width: 44px; height: 44px; border-radius: 12px; background: {{ $iconBg }}; color: {{ $iconColor }}; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                    <i class="bi {{ $iconClass }}"></i>
                                </div>
                                <div style="flex: 1; min-width: 0; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                    <div style="min-width: 0;">
                                        <div style="font-weight: 700; font-size: 15px; color: var(--dash-navy); margin-bottom: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $item['source'] ?? $item['title'] }}
                                        </div>
                                        <div style="font-size: 12px; color: var(--dash-muted); margin-bottom: 6px;">
                                            Trainer: <strong style="color: #475569;">{{ $item['trainer'] }}</strong>
                                        </div>
                                        <span style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-weight: 800; font-size: 9px; border-radius: 6px; padding: 3px 8px; text-transform: uppercase;">
                                            {{ $badgeText }}
                                        </span>
                                    </div>
                                    
                                    <div style="text-align: right; font-size: 11px; flex-shrink: 0;">
                                        <div style="color: var(--dash-muted); margin-bottom: 2px;">Dikirim</div>
                                        <div style="font-weight: 700; color: var(--dash-navy); margin-bottom: 4px;">
                                            {{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d M Y') }}
                                        </div>
                                        <div style="color: var(--dash-muted); margin-bottom: 2px;">Deadline</div>
                                        <div style="font-weight: 700; color: {{ $deadlineColor }};">
                                            {{ $deadlineText }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="flex-shrink: 0;">
                                <a href="{{ $item['url'] }}" style="width: 32px; height: 32px; border-radius: 8px; background: {{ $btnBg }}; color: {{ $btnColor }}; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: all 0.2s;">
                                    <i class="bi bi-chevron-right" style="font-size: 14px; font-weight: 700;"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4" style="font-size: 13px;">
                            <i class="bi bi-check2-circle" style="font-size: 24px; color: #10b981; display: block; margin-bottom: 6px;"></i>
                            Semua materi sudah diperiksa!
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.material.approvals') }}" class="card-link mt-auto">
                    Buka Halaman Approval Materi
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <!-- Status Persetujuan -->
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Status Persetujuan</h5>
                </div>

                @php
                    $pendingPct = (float) ($approvalStats['pending_pct'] ?? 0);
                    $approvedPct = (float) ($approvalStats['approved_pct'] ?? 0);
                    $splitPct = min(100, $pendingPct + $approvedPct);
                @endphp
                <div style="display: flex; flex-direction: column; align-items: center; gap: 20px; width: 100%; flex: 1;">
                    <div class="approval-donut"
                        style="background: conic-gradient(#10b981 0 {{ $approvedPct }}%, #f59e0b {{ $approvedPct }}% {{ $splitPct }}%, #ef4444 {{ $splitPct }}% 100%); margin: 0 auto;">
                        <div class="approval-donut-inner" style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center;">
                            <div style="font-size: 26px; font-weight: 800; color: var(--dash-navy); line-height: 1;">{{ round($approvalStats['approved_pct']) }}%</div>
                            <div style="font-size: 10px; font-weight: 600; color: var(--dash-muted); margin-top: 2px;">Approval Rate</div>
                            <div style="font-size: 10px; font-weight: 700; color: #10b981; margin-top: 4px; display: flex; align-items: center; gap: 2px;">
                                <i class="bi bi-arrow-up-short"></i> 12%
                            </div>
                            <div style="font-size: 9px; color: var(--dash-muted);">dari bulan lalu</div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; width: 100%; border-top: 1px solid #f1f5f9; padding-top: 16px; margin-top: auto;">
                        <div style="text-align: center;">
                            <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--dash-muted); font-weight: 600; margin-bottom: 2px;">
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span>
                                Disetujui
                            </div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--dash-navy);">
                                {{ $approvalStats['approved'] }} <span style="font-weight:500; font-size:10px; color:var(--dash-muted)">({{ $approvalStats['approved_pct'] }}%)</span>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--dash-muted); font-weight: 600; margin-bottom: 2px;">
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #f59e0b;"></span>
                                Menunggu
                            </div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--dash-navy);">
                                {{ $approvalStats['pending'] }} <span style="font-weight:500; font-size:10px; color:var(--dash-muted)">({{ $approvalStats['pending_pct'] }}%)</span>
                            </div>
                        </div>
                        <div style="text-align: center;">
                            <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: var(--dash-muted); font-weight: 600; margin-bottom: 2px;">
                                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #ef4444;"></span>
                                Ditolak
                            </div>
                            <div style="font-size: 12px; font-weight: 700; color: var(--dash-navy);">
                                {{ $approvalStats['rejected'] }} <span style="font-weight:500; font-size:10px; color:var(--dash-muted)">({{ $approvalStats['rejected_pct'] }}%)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.trainer.material.approvals') }}" class="card-link mt-2">
                    Lihat semua persetujuan
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <!-- Deadline Terdekat -->
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Deadline Terdekat</h5>
                </div>

                <div class="timeline-container" style="flex: 1;">
                    @forelse($deadlineItems->take(3) as $item)
                        @php
                            $dotColor = $item['badge_class'] ?? 'green';
                            if ($dotColor === 'blue') {
                                $dotColor = 'green';
                            }
                            
                            if ($item['badge_class'] === 'red') {
                                $timeLabelText = 'Hari Ini';
                                $badgeText = 'LEWAT TENGGAT';
                                $badgeBg = '#fef2f2';
                                $badgeColor = '#ef4444';
                            } elseif ($item['badge_class'] === 'yellow') {
                                $timeLabelText = 'Besok';
                                $badgeText = 'HARI INI';
                                $badgeBg = '#fff7ed';
                                $badgeColor = '#ea580c';
                            } else {
                                $timeLabelText = $item['badge_text'] ?? '3 Hari Lagi';
                                $badgeText = 'ON PROGRESS';
                                $badgeBg = '#eff6ff';
                                $badgeColor = '#2563eb';
                            }
                        @endphp
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $dotColor }}"></div>
                            <div class="timeline-content">
                                <div class="timeline-header-row">
                                    <span class="timeline-time-label">{{ $timeLabelText }}</span>
                                    <span class="timeline-badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                                        {{ $badgeText }}
                                    </span>
                                </div>
                                <div class="timeline-title">
                                    {{ $item['type'] === 'event' ? 'Acara:' : 'Materi:' }} {{ $item['title'] }}
                                </div>
                                <div class="timeline-trainer">Trainer: {{ $item['trainer'] }}</div>
                                @if($item['badge_class'] !== 'red')
                                    <div class="timeline-date">{{ $item['date_text'] }}</div>
                                @endif
                            </div>
                            <div class="timeline-icon-box neutral">
                                <i class="bi bi-calendar3"></i>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted" style="font-size:13px;">
                            Belum ada deadline terdekat.
                        </div>
                    @endforelse
                </div>

                <a href="#daftar-trainer" class="card-link mt-3" onclick="document.querySelector('.table-responsive').scrollIntoView({behavior: 'smooth'}); return false;">
                    Kelola dari profil trainer
                    <i class="bi bi-arrow-down"></i>
                </a>
            </div>
        </div>

        <!-- Row 4: Ringkasan Aktivitas, Top Trainer, Feedback Terbaru -->
        <div class="bottom-three-grid">
            <!-- Ringkasan Aktivitas -->
            <div class="dash-card activity-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Ringkasan Aktivitas</h5>

                    <select class="chart-select">
                        <option>7 Hari Terakhir</option>
                        <option>30 Hari Terakhir</option>
                    </select>
                </div>

                <div class="legend-row">
                    <span class="legend-item">
                        <span class="legend-dot" style="background:#2f5bff;"></span>
                        Course Dibuat
                    </span>

                    <span class="legend-item">
                        <span class="legend-dot" style="background:#19bd6b;"></span>
                        Event Berjalan
                    </span>

                    <span class="legend-item">
                        <span class="legend-dot" style="background:#ff970f;"></span>
                        Materi Dikirim
                    </span>
                </div>

                <div class="chart-shell">
                    <div class="chart-tooltip" id="dashboard-chart-tooltip"></div>
                    <div class="chart-y-axis">
                        @foreach($chartYAxis as $tick)
                            <div>{{ $tick }}</div>
                        @endforeach
                    </div>
                    <svg class="line-chart" id="dashboard-line-chart" viewBox="0 0 680 260" preserveAspectRatio="none">
                        <line x1="0" y1="35" x2="680" y2="35" stroke="#e7edf6" />
                        <line x1="0" y1="80" x2="680" y2="80" stroke="#e7edf6" />
                        <line x1="0" y1="125" x2="680" y2="125" stroke="#e7edf6" />
                        <line x1="0" y1="170" x2="680" y2="170" stroke="#e7edf6" />
                        <line x1="0" y1="215" x2="680" y2="215" stroke="#e7edf6" />

                        <polyline points="{{ $chartPoints['course'] ?? '' }}" fill="none" stroke="#2f5bff" stroke-width="4"
                            stroke-linecap="round" />
                        <polyline points="{{ $chartPoints['event'] ?? '' }}" fill="none" stroke="#19bd6b" stroke-width="4"
                            stroke-linecap="round" />
                        <polyline points="{{ $chartPoints['material'] ?? '' }}" fill="none" stroke="#ff970f"
                            stroke-width="4" stroke-linecap="round" />
                    </svg>
                    <div class="chart-x-axis">
                        @foreach($chartData['labels'] ?? [] as $label)
                            <div>{{ $label }}</div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Top Trainer -->
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Top Trainer <span style="font-size:11px; font-weight:500; color:var(--dash-muted);">(Berdasarkan Event/Course Bulan Ini)</span></h5>
                    <a href="{{ route('admin.trainer.index', ['view' => 'list']) }}#daftar-trainer" class="card-link" style="font-size: 11px; font-weight: 600;">Lihat semua</a>
                </div>

                <div class="trainer-list">
                    @forelse($topTrainers as $index => $trainer)
                        @php
                            $rankClass = ['gold', 'silver', 'bronze'][$index] ?? 'silver';
                            $score = (int) ($trainer->score ?? 0);
                            $scorePct = (int) ($trainer->score_pct ?? 0);
                            $badgeText = ['Top Creator', 'Fast Responder', 'Rising Trainer'][$index] ?? 'Trainer';
                            $badgeBg = ['#f5f3ff', '#eff6ff', '#f0fdf4'][$index] ?? '#f1f5f9';
                            $badgeColor = ['#7c3aed', '#3b82f6', '#22c55e'][$index] ?? '#64748b';
                        @endphp

                        <div class="trainer-item">
                            <div class="rank-icon {{ $rankClass }}">
                                {{ $index + 1 }}
                            </div>

                            <img src="{{ $trainer->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($trainer->name ?? 'Trainer') . '&background=2745e8&color=fff&bold=true') }}"
                                class="trainer-avatar" alt="{{ $trainer->name ?? 'Trainer' }}">

                            <div style="min-width: 0;">
                                <div class="trainer-name" style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                    <a href="{{ route('admin.trainer.show', $trainer->id ?? 0) }}" class="text-decoration-none text-dark hover-primary" style="font-weight:700;">
                                        {{ $trainer->name ?? 'Trainer' }}
                                    </a>
                                    <span class="badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 9px; padding: 2px 6px; font-weight: 800; border-radius: 4px;">{{ $badgeText }}</span>
                                </div>
                                <div class="trainer-meta">
                                    {{ $trainer->courses_as_trainer_count ?? 0 }} Course •
                                    {{ $trainer->events_as_trainer_count ?? 0 }} Event
                                </div>
                            </div>

                            <div class="score-area">
                                <strong style="font-size: 16px; font-weight: 800; color: var(--dash-navy);">{{ $score }}</strong>
                                <span style="font-size: 10px; color: var(--dash-muted);">Event/Course</span>
                                <div class="score-bar" style="width: 100%;">
                                    <span style="width: {{ $scorePct }}%;"></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-3" style="font-size:13px;">
                            Belum ada data trainer.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.index', ['view' => 'list']) }}#daftar-trainer" class="card-link mt-auto">
                    Lihat semua trainer
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <!-- Feedback Terbaru -->
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Feedback Terbaru</h5>
                    <a href="{{ route('admin.trainer.material.approved') }}" class="card-link" style="font-size: 11px; font-weight: 600;">Lihat semua &rarr;</a>
                </div>

                <div class="feedback-list">
                    @forelse($feedbackItems as $item)
                        @php
                            $bgColors = ['#2f5bff', '#8d54df', '#1e293b'];
                            $bgColor = $bgColors[$loop->index % 3];
                            $initials = collect(explode(' ', $item['name']))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->join('');
                        @endphp
                        <div class="feedback-item">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ $bgColor }}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;">
                                {{ $initials }}
                            </div>

                            <div style="min-width: 0;">
                                <div class="stars">{{ $item['stars'] ?? '★★★★★' }}</div>
                                <div class="feedback-title" style="font-size: 12px; font-weight: 700; color: var(--dash-navy); margin-bottom: 2px;">{{ $item['title'] ?? '' }}</div>
                                <div class="feedback-text" style="font-style: italic; font-size: 12px; color: var(--dash-muted); margin-bottom: 4px; line-height: 1.3;">
                                    "{{ $item['comment'] ?? 'Tidak ada komentar.' }}"
                                </div>
                                <div class="feedback-name" style="font-weight: 600; color: #475569; font-size: 11px;">{{ $item['name'] ?? 'User' }}</div>
                            </div>

                            <div class="feedback-time" style="font-size: 11px; color: var(--dash-muted);">{{ $item['time'] ?? '-' }}</div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-3" style="font-size:13px;">
                            Belum ada feedback terbaru.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.material.approved') }}" class="card-link mt-auto">
                    Lihat semua feedback
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Row 5: Daftar Seluruh Trainer -->
        <div class="dash-card mb-4" style="padding: 0; overflow: hidden; margin-top: 24px;">
            <div class="card-header-clean" style="padding: 24px 24px 16px 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between;">
                <h5 class="card-title-clean" style="font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: 16px; color: #0f172a; letter-spacing: -0.4px; margin: 0; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-people-fill" style="color: #4f46e5; font-size: 18px;"></i>
                    Daftar Seluruh Trainer
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table-premium align-middle mb-0">
                    <thead>
                        <tr>
                            <th>TRAINER</th>
                            <th>SKILL UTAMA</th>
                            <th>AKTIVITAS</th>
                            <th>COURSE</th>
                            <th>EVENT</th>
                            <th>PESERTA</th>
                            <th>RATING</th>
                            <th>STATUS</th>
                            <th>BERGABUNG</th>
                            <th class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainers as $trainerItem)
                            @php
                                $score = (int) ($trainerItem->points ?? 0);
                                $maxPoints = max(1, (int) $trainers->max('points'));
                                $scorePct = min(100, (int) round(($score / $maxPoints) * 100));
                                
                                // Fetch real skills from User model 'trainer_skills' cast
                                $skillsHtml = '';
                                $skills = $trainerItem->trainer_skills;
                                if (!is_array($skills)) {
                                    $skills = [];
                                }
                                if (empty($skills)) {
                                    $skillsHtml = '<span style="display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">-</span>';
                                } else {
                                    $takeSkills = array_slice($skills, 0, 3);
                                    $restCount = count($skills) - count($takeSkills);
                                    
                                    $colors = [
                                        ['#f5f3ff', '#7c3aed', '#e9d5ff'], // Purple
                                        ['#eff6ff', '#3b82f6', '#bfdbfe'], // Blue
                                        ['#f0fdf4', '#22c55e', '#bbf7d0'], // Green
                                    ];
                                    
                                    foreach ($takeSkills as $idx => $s) {
                                        $colorSet = $colors[$idx % count($colors)];
                                        $skillsHtml .= '<span style="display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; background: ' . $colorSet[0] . '; color: ' . $colorSet[1] . '; border: ' . $colorSet[2] . '; margin-right: 4px;">' . e($s) . '</span>';
                                    }
                                    if ($restCount > 0) {
                                        $skillsHtml .= '<span style="display: inline-block; padding: 4px 6px; border-radius: 8px; font-size: 11px; font-weight: 700; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">+' . $restCount . '</span>';
                                    }
                                }
                                
                                // Calculate real participants count
                                $courseParticipants = $trainerItem->trainer_enrollments_count ?? 0;
                                $eventParticipants = \App\Models\EventRegistration::whereHas('event', function ($q) use ($trainerItem) {
                                    $q->where('trainer_id', $trainerItem->id);
                                })->where('status', 'active')->count();
                                $pesertaCount = $courseParticipants + $eventParticipants;
                                
                                $ratingVal = number_format((float) ($trainerItem->average_rating ?: 5.0), 1);
                                $filledStars = (int) round((float) $ratingVal);
                                
                                $statusLabel = match($trainerItem->user_status ?? 'active') {
                                    'active' => 'Aktif',
                                    'inactive' => 'Tidak Tersedia',
                                    'suspended' => 'Ditangguhkan',
                                    default => 'Aktif',
                                };
                                $statusColorStyle = match($trainerItem->user_status ?? 'active') {
                                    'active' => 'background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;',
                                    'inactive' => 'background: #fffbeb; color: #d97706; border: 1px solid #fde68a;',
                                    'suspended' => 'background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5;',
                                    default => 'background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $trainerItem->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($trainerItem->name) . '&background=2745e8&color=fff&bold=true') }}" 
                                             alt="{{ $trainerItem->name }}" 
                                             class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 13px;">{{ $trainerItem->name }}</div>
                                            <div class="small text-muted" style="font-size: 11px;">{{ $trainerItem->phone ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 flex-wrap">
                                        {!! $skillsHtml !!}
                                    </div>
                                </td>
                                <td>
                                    <div style="min-width: 80px;">
                                        <div style="font-weight: 700; color: var(--dash-navy); font-size: 13px;">
                                            {{ $score }} <span style="font-weight: 500; font-size: 11px; color: var(--dash-muted);">Poin</span>
                                        </div>
                                        <div style="height: 4px; border-radius: 2px; background: #f1f5f9; overflow: hidden; margin-top: 4px; width: 60px;">
                                            <span style="display: block; height: 100%; background: #3b82f6; width: {{ $scorePct }}%;"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-semibold text-dark" style="font-size: 13px;">{{ $trainerItem->courses_as_trainer_count }}</td>
                                <td class="fw-semibold text-dark" style="font-size: 13px;">{{ $trainerItem->events_as_trainer_count }}</td>
                                <td class="fw-semibold text-dark" style="font-size: 13px;">{{ $pesertaCount }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fw-bold text-dark" style="font-size: 13px;">{{ $ratingVal }}</span>
                                        <div style="color: #fbbf24; font-size: 10px; display: flex; gap: 1px;">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $filledStars ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; {!! $statusColorStyle !!}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="small text-muted" style="font-size: 12px;">{{ $trainerItem->created_at->translatedFormat('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <a href="{{ route('admin.trainer.show', $trainerItem->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" style="background: #3949ab; border-color: #3949ab; font-size: 12px;">
                                            Profil
                                        </a>
                                        <button class="btn btn-link text-muted p-0 border-0" style="font-size: 18px;">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Belum ada trainer terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($trainers->hasPages())
                <div class="d-flex justify-content-end mt-4 p-3">
                    {{ $trainers->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

    </div>
@endsection

@push('admin-trainer-scripts')
    <script>
        window.dashboardChartData = @json($chartData);

        (function () {
            var svg = document.getElementById('dashboard-line-chart');
            var tooltip = document.getElementById('dashboard-chart-tooltip');
            if (!svg || !tooltip || !window.dashboardChartData) {
                return;
            }

            var data = window.dashboardChartData;
            var labels = data.labels || [];
            var series = [
                { name: 'Course Dibuat', color: '#2f5bff', values: data.course || [] },
                { name: 'Event Berjalan', color: '#19bd6b', values: data.event || [] },
                { name: 'Materi Dikirim', color: '#ff970f', values: data.material || [] }
            ];

            if (!labels.length) {
                return;
            }

            var ns = 'http://www.w3.org/2000/svg';
            var hoverGroup = document.createElementNS(ns, 'g');
            var hoverLine = document.createElementNS(ns, 'line');
            hoverLine.setAttribute('y1', '35');
            hoverLine.setAttribute('y2', '215');
            hoverLine.setAttribute('stroke', '#94a3b8');
            hoverLine.setAttribute('stroke-width', '1');
            hoverLine.setAttribute('stroke-dasharray', '4 4');
            hoverLine.style.opacity = '0';
            hoverGroup.appendChild(hoverLine);

            var hoverDots = series.map(function (item) {
                var dot = document.createElementNS(ns, 'circle');
                dot.setAttribute('r', '4');
                dot.setAttribute('fill', item.color);
                dot.style.opacity = '0';
                hoverGroup.appendChild(dot);
                return dot;
            });

            svg.appendChild(hoverGroup);

            var viewBox = svg.viewBox.baseVal;
            var xStart = 15;
            var xEnd = 665;
            var yTop = 35;
            var yBottom = 215;
            var xSpan = xEnd - xStart;
            var ySpan = yBottom - yTop;

            function maxValue() {
                var max = 1;
                series.forEach(function (item) {
                    item.values.forEach(function (value) {
                        if (value > max) {
                            max = value;
                        }
                    });
                });
                return max;
            }

            var max = maxValue();

            function formatTooltip(index) {
                var label = labels[index] || '';
                var html = '<div style="font-weight:700;margin-bottom:4px;">' + label + '</div>';
                series.forEach(function (item) {
                    var value = item.values[index] ?? 0;
                    html += '<div style="color:' + item.color + '">' + item.name + ': ' + value + '</div>';
                });
                tooltip.innerHTML = html;
            }

            function setHover(index, clientX) {
                var x = xStart + (xSpan * index / Math.max(1, labels.length - 1));
                hoverLine.setAttribute('x1', x);
                hoverLine.setAttribute('x2', x);
                hoverLine.style.opacity = '1';

                series.forEach(function (item, idx) {
                    var value = item.values[index] ?? 0;
                    var ratio = max > 0 ? (value / max) : 0;
                    var y = yBottom - (ratio * ySpan);
                    hoverDots[idx].setAttribute('cx', x);
                    hoverDots[idx].setAttribute('cy', y);
                    hoverDots[idx].style.opacity = '1';
                });

                formatTooltip(index);

                var rect = svg.getBoundingClientRect();
                tooltip.style.left = Math.max(8, clientX - rect.left + 12) + 'px';
                tooltip.classList.add('visible');
            }

            function hideHover() {
                hoverLine.style.opacity = '0';
                hoverDots.forEach(function (dot) { dot.style.opacity = '0'; });
                tooltip.classList.remove('visible');
            }

            svg.addEventListener('mousemove', function (event) {
                var rect = svg.getBoundingClientRect();
                var x = ((event.clientX - rect.left) / rect.width) * viewBox.width;
                var rawIndex = Math.round((x - xStart) / xSpan * (labels.length - 1));
                var index = Math.min(labels.length - 1, Math.max(0, rawIndex));
                setHover(index, event.clientX);
            });

            svg.addEventListener('mouseleave', hideHover);
        })();
    </script>

    @if(request()->query('view') === 'list')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var el = document.getElementById('daftar-trainer');
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 150);
            });
        </script>
    @endif
@endpush