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
            --dash-primary: #1e3a8a;
            --dash-blue: #3b82f6;
            --dash-green: #10b981;
            --dash-orange: #f59e0b;
            --dash-purple: #8b5cf6;
            --dash-red: #ef4444;
            --dash-muted: #64748b;
            --dash-soft: #f8fafc;
            --dash-border: #e2e8f0;
            --dash-card-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02);
        }

        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: #334155;
            -webkit-font-smoothing: antialiased;
        }

        .admin-dashboard {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Header Section */
        .header-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            border: none;
            border-radius: 24px;
            padding: 32px 40px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 15px 35px rgba(30, 58, 138, 0.15);
            position: relative;
            overflow: hidden;
        }
        .header-section::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }
        .header-content {
            position: relative;
            z-index: 2;
        }
        .welcome-greeting { font-size: 32px; font-weight: 800; color: white; margin: 0 0 12px 0; display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px; }
        .welcome-subtitle { font-size: 16px; color: rgba(255, 255, 255, 0.8); margin: 0; font-weight: 500; }


        .admin-dashboard {
            width: 100%;
            color: var(--dash-navy);
        }

        .dashboard-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px;
            margin-bottom: 20px;
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: 900;
            color: var(--dash-navy);
            margin: 0 0 4px;
            letter-spacing: -.7px;
        }

        .dashboard-greeting {
            font-size: 17px;
            color: #415077;
            margin: 0;
            line-height: 1.45;
        }

        .dashboard-note {
            color: #71809d;
            font-size: 14px;
            margin-top: 2px;
        }

        .date-widget {
            min-width: 220px;
            height: 64px;
            padding: 12px 16px;
            background: #fff;
            border: 1px solid var(--dash-border);
            border-radius: 14px;
            box-shadow: var(--dash-card-shadow);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .date-widget-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #f3f6ff;
            color: #5265a4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }

        .date-main {
            font-weight: 900;
            font-size: 14px;
            color: var(--dash-navy);
            line-height: 1.2;
        }

        .date-sub {
            font-size: 12px;
            color: #71809d;
            margin-top: 2px;
        }

        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-bottom: 8px;
        }

        .metric-card {
            background: #ffffff;
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--dash-card-shadow);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }
            padding: 14px 16px;
            display: flex;
            gap: 12px;
            align-items: center;
            overflow: hidden;
        }

        .metric-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            color: white;
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.1);
        }

        .metric-body {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex: 1;
            min-height: 50px;
        }

        .metric-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-height: 50px;
            justify-content: center;
        }

        .metric-icon.blue { background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%); }
        .metric-icon.green { background: linear-gradient(135deg, #34d399 0%, #059669 100%); }
        .metric-icon.orange { background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%); }
        .metric-icon.purple { background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%); }

        .metric-label {
            font-size: 12px;
            color: #71809d;
            margin-bottom: 0;
        }

        .metric-value {
            font-size: 24px;
            font-weight: 900;
            line-height: 1;
            color: var(--dash-navy);
        }

        .metric-change {
            font-size: 11px;
            margin-top: 2px;
            font-weight: 800;
            background: none;
            border: none;
        }

        .metric-change.up {
            color: #00a961;
        }

        .metric-change.down {
            color: #ff3b30;
        }

        .metric-sparkline {
            width: 96px;
            height: 44px;
        }

        .queue-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .queue-item-card {
            border: 1px solid var(--dash-border);
            border-radius: 14px;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            transition: all 0.22s ease-in-out;
            box-shadow: 0 4px 10px rgba(15, 23, 42, .01);
        }

        .queue-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
            border-color: #cbd5e1;
        }

        .queue-item-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex-grow: 1;
        }

        .queue-icon-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .queue-icon-wrapper.course,
        .queue-icon-wrapper.event {
            background: #eff6ff;
            color: #2563eb;
        }

        .queue-item-details {
            min-width: 0;
        }

        .queue-item-title {
            font-weight: 800;
            font-size: 14px;
            color: var(--dash-navy);
            line-height: 1.4;
            margin-bottom: 3px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .queue-item-meta {
            font-size: 12px;
            color: var(--dash-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .meta-dot {
            color: #cbd5e1;
        }

        .btn-queue-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 800;
            border-radius: 8px;
            padding: 8px 14px;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            border: 0;
        }

        .btn-queue-action.course,
        .btn-queue-action.event,
        .btn-queue-action.publish {
            background: #eff6ff;
            color: #2563eb;
        }

        .btn-queue-action.course:hover,
        .btn-queue-action.event:hover,
        .btn-queue-action.publish:hover {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1.55fr .92fr 1.06fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1.12fr 1.05fr 1.05fr;
            gap: 16px;
            margin-bottom: 18px;
        }

        .dash-card {
            background: #fff;
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            box-shadow: var(--dash-card-shadow);
            padding: 24px;
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .dash-card .card-link {
            margin-top: auto;
        }

        .card-header-clean {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 24px;
        }

        .card-title-clean {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .card-link {
            color: #1e3a8a;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .card-link:hover {
            color: #3b82f6;
            text-decoration: underline;
        }

        .chart-select {
            height: 34px;
            border: 1px solid var(--dash-border);
            border-radius: 9px;
            padding: 0 12px;
            font-size: 13px;
            color: #53617f;
            background: #fff;
        }

        .legend-row {
            display: flex;
            align-items: center;
            gap: 26px;
            flex-wrap: wrap;
            margin-bottom: 8px;
            padding-left: 28px;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: #53617f;
            white-space: nowrap;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .line-chart {
            width: 100%;
            height: 230px;
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
            top: 28px;
            bottom: 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            font-size: 11px;
            color: #8b96ad;
            text-align: right;
            width: 32px;
            padding-right: 6px;
        }

        .chart-x-axis {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #8b96ad;
            margin-top: 6px;
            padding: 0 8px 0 2px;
        }

        .chart-tooltip {
            position: absolute;
            top: 8px;
            left: 12px;
            background: #0f172a;
            color: #fff;
            padding: 8px 10px;
            border-radius: 10px;
            font-size: 12px;
            line-height: 1.4;
            min-width: 120px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, .25);
            opacity: 0;
            transform: translateY(-6px);
            transition: .18s;
            pointer-events: none;
            z-index: 2;
        }

        .chart-tooltip.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .approval-wrap {
            display: flex;
            align-items: center;
            gap: 28px;
            min-height: 255px;
        }

        .approval-donut {
            width: 190px;
            height: 190px;
            border-radius: 50%;
            background: conic-gradient(var(--dash-orange) 0 10%,
                    var(--dash-green) 10% 90%,
                    var(--dash-red) 90% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .approval-donut-inner {
            width: 102px;
            height: 102px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            box-shadow: inset 0 0 0 1px #eef2f7;
        }

        .donut-number {
            font-size: 23px;
            font-weight: 900;
            color: var(--dash-navy);
            line-height: 1;
        }

        .donut-label {
            font-size: 12px;
            color: #71809d;
            margin-top: 5px;
        }

        .approval-list {
            display: grid;
            gap: 18px;
            flex: 1;
        }

        .approval-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            color: #53617f;
            line-height: 1.35;
        }

        .approval-item strong {
            color: var(--dash-navy);
            font-weight: 900;
        }

        .deadline-list,
        .trainer-list,
        .feedback-list {
            display: grid;
            gap: 12px;
        }

        .deadline-item {
            min-height: 68px;
            padding: 12px;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 6px 16px rgba(15, 23, 42, .035);
            display: grid;
            grid-template-columns: 44px 1fr auto;
            align-items: center;
            gap: 12px;
        }

        .deadline-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .deadline-icon.green {
            background: #e9fff3;
            color: #19bd6b;
        }

        .deadline-icon.orange {
            background: #fff7e8;
            color: #ff970f;
        }

        .deadline-icon.blue {
            background: #eef4ff;
            color: #2f5bff;
        }

        .deadline-title {
            font-size: 13px;
            font-weight: 900;
            color: var(--dash-navy);
            margin-bottom: 3px;
            line-height: 1.3;
        }

        .deadline-sub {
            font-size: 12px;
            color: #71809d;
        }

        .deadline-right {
            text-align: right;
        }

        .deadline-badge {
            display: inline-block;
            padding: 5px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 900;
            margin-bottom: 5px;
        }

        .deadline-badge.red {
            background: #ffe7e7;
            color: #ef2727;
        }

        .deadline-badge.yellow {
            background: #fff2c8;
            color: #e79500;
        }

        .deadline-badge.blue {
            background: #e8efff;
            color: #275bff;
        }

        .deadline-date {
            font-size: 12px;
            color: #53617f;
        }

        .trainer-item {
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 10px 12px;
            display: grid;
            grid-template-columns: 34px 42px 1fr 110px;
            gap: 12px;
            align-items: center;
        }

        .rank-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 12px;
        }

        .rank-icon.gold {
            background: #fff2c8;
            color: #f2a900;
        }

        .rank-icon.silver {
            background: #eef2f7;
            color: #8a97aa;
        }

        .rank-icon.bronze {
            background: #ffe8d7;
            color: #e87531;
        }

        .trainer-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .trainer-name {
            font-size: 13px;
            font-weight: 900;
            color: var(--dash-navy);
            margin-bottom: 2px;
        }

        .trainer-meta {
            font-size: 12px;
            color: #71809d;
        }

        .score-area {
            text-align: right;
            font-size: 12px;
            color: #53617f;
        }

        .score-area strong {
            font-size: 17px;
            color: var(--dash-navy);
        }

        .score-bar {
            height: 5px;
            border-radius: 99px;
            background: #edf2fb;
            overflow: hidden;
            margin-top: 6px;
        }

        .score-bar span {
            display: block;
            height: 100%;
            border-radius: 99px;
            background: #2745e8;
        }

        .category-wrap {
            display: flex;
            align-items: center;
            gap: 30px;
            min-height: 245px;
        }

        .category-donut {
            width: 190px;
            height: 190px;
            border-radius: 50%;
            background: conic-gradient(#2f5bff 0 37.5%,
                    #19bd6b 37.5% 62.5%,
                    #ff970f 62.5% 79.2%,
                    #8d54df 79.2% 91.7%,
                    #9ca3af 91.7% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .category-inner {
            width: 102px;
            height: 102px;
            border-radius: 50%;
            background: #fff;
            box-shadow: inset 0 0 0 1px #eef2f7;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: var(--dash-navy);
        }

        .category-list {
            flex: 1;
            display: grid;
            gap: 13px;
        }

        .category-item {
            display: grid;
            grid-template-columns: 12px 1fr auto;
            gap: 10px;
            align-items: center;
            font-size: 13px;
            color: #53617f;
        }

        .category-item strong {
            color: #53617f;
            font-weight: 700;
        }

        .feedback-item {
            display: grid;
            grid-template-columns: 44px 1fr auto;
            gap: 12px;
            align-items: center;
            padding-bottom: 13px;
            border-bottom: 1px solid #eef2f7;
        }

        .feedback-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .feedback-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
        }

        .stars {
            color: #ffb020;
            font-size: 13px;
            letter-spacing: 1px;
            line-height: 1;
            margin-bottom: 5px;
            justify-content: flex-start;
        }

        .feedback-title {
            font-size: 13px;
            color: #53617f;
            margin-bottom: 2px;
            line-height: 1.25;
        }

        .feedback-name {
            font-size: 12px;
            color: var(--dash-navy);
        }

        .feedback-time {
            font-size: 12px;
            color: #71809d;
            white-space: nowrap;
            align-self: center;
        }

        .insight-card {
            min-height: 96px;
            background: linear-gradient(135deg, #eef2ff, #f7f8ff);
            border: 1px solid #dfe7ff;
            border-radius: 12px;
            box-shadow: var(--dash-card-shadow);
            padding: 20px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .insight-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .insight-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: #dfe8ff;
            color: var(--dash-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            box-shadow: 0 10px 22px rgba(47, 91, 255, .16);
            flex-shrink: 0;
        }

        .insight-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 6px;
        }

        .insight-text {
            margin: 0;
            color: #53617f;
            font-size: 14px;
        }

        .btn-report {
            height: 48px;
            min-width: 230px;
            padding: 0 20px;
            border: 0;
            border-radius: 12px;
            background: #1e3a8a;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            transition: all 0.2s ease;
        }

        .btn-report:hover {
            color: #fff;
            background: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
        }

        @media (max-width: 1400px) {
            .metric-grid {
                grid-template-columns: repeat(2, minmax(220px, 1fr));
            }

            .main-grid,
            .bottom-grid {
                grid-template-columns: 1fr 1fr;
            }

            .activity-card {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 992px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .date-widget {
                width: 100%;
            }

            .main-grid,
            .bottom-grid {
                grid-template-columns: 1fr;
            }

            .approval-wrap,
            .category-wrap {
                flex-direction: column;
                align-items: center;
            }

            .approval-list,
            .category-list {
                width: 100%;
            }

            .insight-card {
                flex-direction: column;
                align-items: flex-start;
            }

            .btn-report {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .welcome-greeting {
                font-size: 24px;
            }

            .metric-grid {
                grid-template-columns: 1fr;
            }

            .metric-card {
                grid-template-columns: 58px 1fr;
            }

            .dash-card {
                padding: 16px;
            }

            .line-chart {
                height: 210px;
            }

            .legend-row {
                padding-left: 0;
                gap: 12px;
            }

            .trainer-item {
                grid-template-columns: 30px 38px 1fr;
            }

            .score-area {
                grid-column: 3;
                text-align: left;
            }

            .feedback-item {
                grid-template-columns: 42px 1fr;
            }

            .feedback-time {
                grid-column: 2;
            }

            .insight-left {
                align-items: flex-start;
            }
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="admin-dashboard">

        <!-- Modern Hero Header -->
        <div class="header-section">
            <div class="header-content">
                <h1 class="welcome-greeting">
                    <i class="bi bi-shield-lock-fill"></i> Admin Trainer Workspace
                </h1>
                <p class="welcome-subtitle">Pantau kinerja, materi, dan portofolio seluruh Trainer.</p>
            </div>
            
            <div class="date-widget" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white;">
                <div class="date-widget-icon" style="background: rgba(255,255,255,0.2); color: white;">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <div class="date-main" style="color: white;">{{ $todayLabel }}</div>
                    <div class="date-sub" style="color: rgba(255,255,255,0.8);">{{ $timeLabel }}</div>
                </div>
            </div>
        </div>

        <div class="metric-grid">
            <div class="metric-card">
                <div class="metric-icon blue">
                    <i class="bi bi-journal-richtext"></i>
                </div>

                <div>
                    <div class="metric-label">Total Course</div>
                    <div class="metric-value">{{ $totalCourses }}</div>
                    <div class="metric-change {{ $metricChanges['courses']['direction'] ?? 'up' }}">
                        {{ $metricChanges['courses']['text'] ?? '0 dari bulan lalu' }}
                    </div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon green">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <div>
                    <div class="metric-label">Total Event</div>
                    <div class="metric-value">{{ $totalEvents }}</div>
                    <div class="metric-change {{ $metricChanges['events']['direction'] ?? 'up' }}">
                        {{ $metricChanges['events']['text'] ?? '0 dari bulan lalu' }}
                    </div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon orange">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div>
                    <div class="metric-label">Pending Review</div>
                    <div class="metric-value">{{ $pendingReviews }}</div>
                    <div class="metric-change {{ $metricChanges['pending']['direction'] ?? 'up' }}">
                        {{ $metricChanges['pending']['text'] ?? '0 dari bulan lalu' }}
                    </div>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-icon purple">
                    <i class="bi bi-check2-circle"></i>
                </div>

                <div>
                    <div class="metric-label">Disetujui</div>
                    <div class="metric-value">{{ $approvedMaterials }}</div>
                    <div class="metric-change {{ $metricChanges['approved']['direction'] ?? 'up' }}">
                        {{ $metricChanges['approved']['text'] ?? '0 dari bulan lalu' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Antrean Kerja Admin Section -->
        <h4 style="font-weight: 700; font-size: 18px; color: var(--dash-navy); margin: 28px 0 14px; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-list-task" style="color: #3b82f6;"></i> Antrean Kerja Admin
        </h4>
        
        <div class="queue-grid">
            <!-- 1. Materi Menunggu Persetujuan -->
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Materi Menunggu Persetujuan</h5>
                    <span style="background: rgba(39, 69, 232, 0.1); color: var(--dash-primary); font-weight: 800; font-size: 11px; border-radius: 6px; padding: 4px 8px;">
                        {{ $pendingMaterialsQueue->count() }} Antrean
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 8px;">
                    @forelse($pendingMaterialsQueue as $item)
                        <div class="queue-item-card">
                            <div class="queue-item-left">
                                <div class="queue-icon-wrapper {{ $item['type'] }}">
                                    <i class="bi {{ $item['type'] === 'course' ? 'bi-journal-richtext' : 'bi-calendar-event' }}"></i>
                                </div>
                                <div class="queue-item-details">
                                    <div class="queue-item-title">{{ $item['title'] }}</div>
                                    <div class="queue-item-meta">
                                        <span>Trainer: <strong>{{ $item['trainer'] }}</strong></span>
                                        <span class="meta-dot">&bull;</span>
                                        <span>{{ $item['type'] === 'course' ? 'Course: ' : 'Event: ' }}{{ $item['source'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="queue-item-right">
                                <a href="{{ $item['url'] }}" class="btn-queue-action {{ $item['type'] }}">
                                    <span>Review</span>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4" style="font-size: 13px;">
                            <i class="bi bi-check2-circle" style="font-size: 24px; color: #19bd6b; display: block; margin-bottom: 6px;"></i>
                            Semua materi sudah diperiksa!
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.material.approvals') }}" class="card-link mt-auto">
                    Buka Halaman Approval Materi
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <!-- 2. Sertifikat Belum Diterbitkan -->
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Sertifikat Perlu Diterbitkan</h5>
                    <span style="background: rgba(255, 151, 15, 0.1); color: var(--dash-orange); font-weight: 800; font-size: 11px; border-radius: 6px; padding: 4px 8px;">
                        {{ $pendingCertificatesQueue->count() }} Antrean
                    </span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 8px;">
                    @forelse($pendingCertificatesQueue as $item)
                        <div class="queue-item-card">
                            <div class="queue-item-left">
                                <div class="queue-icon-wrapper {{ $item['type'] }}">
                                    <i class="bi {{ $item['type'] === 'course' ? 'bi-patch-check-fill' : 'bi-award-fill' }}"></i>
                                </div>
                                <div class="queue-item-details">
                                    <div class="queue-item-title">{{ $item['title'] }}</div>
                                    <div class="queue-item-meta">
                                        <span>Trainer: <strong>{{ $item['trainer'] }}</strong></span>
                                        <span class="meta-dot">&bull;</span>
                                        <span>Selesai: {{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="queue-item-right">
                                <a href="{{ $item['url'] }}" class="btn-queue-action publish">
                                    <span>Terbitkan</span>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-4" style="font-size: 13px;">
                            <i class="bi bi-check2-circle" style="font-size: 24px; color: #19bd6b; display: block; margin-bottom: 6px;"></i>
                            Semua sertifikat selesai diterbitkan!
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.certificates.index') }}" class="card-link mt-auto">
                    Buka Halaman Sertifikat
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="main-grid">
            <div class="dash-card activity-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Aktivitas Overview</h5>

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
                        Event Dibuat
                    </span>

                    <span class="legend-item">
                        <span class="legend-dot" style="background:#ff970f;"></span>
                        Material Disubmit
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

            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Status Approval</h5>
                </div>

                <div class="approval-wrap">
                    @php
                        $pendingPct = (float) ($approvalStats['pending_pct'] ?? 0);
                        $approvedPct = (float) ($approvalStats['approved_pct'] ?? 0);
                        $splitPct = min(100, $pendingPct + $approvedPct);
                    @endphp
                    <div class="approval-donut"
                        style="background: conic-gradient(var(--dash-orange) 0 {{ $pendingPct }}%, var(--dash-green) {{ $pendingPct }}% {{ $splitPct }}%, var(--dash-red) {{ $splitPct }}% 100%);">
                        <div class="approval-donut-inner">
                            <div class="donut-number">{{ $approvalStats['total'] }}</div>
                            <div class="donut-label">Total</div>
                        </div>
                    </div>

                    <div class="approval-list">
                        <div class="approval-item">
                            <span class="legend-dot" style="background:#ff970f;margin-top:3px;"></span>
                            <div>Menunggu Review<br><strong>{{ $approvalStats['pending'] }}</strong>
                                ({{ $approvalStats['pending_pct'] }}%)</div>
                        </div>

                        <div class="approval-item">
                            <span class="legend-dot" style="background:#19bd6b;margin-top:3px;"></span>
                            <div>Disetujui<br><strong>{{ $approvalStats['approved'] }}</strong>
                                ({{ $approvalStats['approved_pct'] }}%)</div>
                        </div>

                        <div class="approval-item">
                            <span class="legend-dot" style="background:#ff4d4f;margin-top:3px;"></span>
                            <div>Ditolak<br><strong>{{ $approvalStats['rejected'] }}</strong>
                                ({{ $approvalStats['rejected_pct'] }}%)</div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('admin.trainer.material.approvals') }}" class="card-link mt-2">
                    Lihat semua approval
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Deadline Terdekat</h5>
                </div>

                <div class="deadline-list">
                    @forelse($deadlineItems as $item)
                        <div class="deadline-item">
                            <div class="deadline-icon {{ $item['type'] === 'event' ? 'orange' : 'green' }}">
                                <i class="bi {{ $item['type'] === 'event' ? 'bi-calendar-event' : 'bi-calendar-check' }}"></i>
                            </div>

                            <div>
                                <div class="deadline-title">
                                    {{ $item['type'] === 'event' ? 'Event:' : 'Materi:' }} {{ $item['title'] }}
                                </div>
                                <div class="deadline-sub">Trainer: {{ $item['trainer'] }}</div>
                            </div>

                            <div class="deadline-right">
                                <span class="deadline-badge {{ $item['badge_class'] }}">{{ $item['badge_text'] }}</span>
                                <div class="deadline-date">{{ $item['date_text'] }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted" style="font-size:13px;">
                            Belum ada deadline terdekat.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.material.approvals') }}" class="card-link mt-3">
                    Lihat semua deadline
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="bottom-grid">
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        Top Trainer
                        <span style="font-size:12px;font-weight:500;color:#71809d;">
                            (Berdasarkan Aktivitas)
                        </span>
                    </h5>

                    <span style="font-size:12px;color:#71809d;">Skor Aktivitas</span>
                </div>

                <div class="trainer-list">
                    @forelse($topTrainers as $index => $trainer)
                        @php
                            $rankClass = ['gold', 'silver', 'bronze'][$index] ?? 'silver';
                            $score = (int) ($trainer->score ?? 0);
                            $scorePct = (int) ($trainer->score_pct ?? 0);
                        @endphp

                        <div class="trainer-item">
                            <div class="rank-icon {{ $rankClass }}">
                                {{ $index + 1 }}
                            </div>

                            <img src="{{ $trainer->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($trainer->name ?? 'Trainer') . '&background=2745e8&color=fff&bold=true') }}"
                                class="trainer-avatar" alt="{{ $trainer->name ?? 'Trainer' }}">

                            <div>
                                <div class="trainer-name">
                                    <a href="{{ route('admin.trainer.show', $trainer->id ?? 0) }}" class="text-decoration-none text-dark hover-primary">
                                        {{ $trainer->name ?? 'Trainer' }} <i class="bi bi-box-arrow-up-right ms-1 small text-muted"></i>
                                    </a>
                                </div>
                                <div class="trainer-meta">
                                    {{ $trainer->courses_as_trainer_count ?? 0 }} Course •
                                    {{ $trainer->events_as_trainer_count ?? 0 }} Event
                                </div>
                            </div>

                            <div class="score-area">
                                <strong>{{ $score }}</strong>/100
                                <div class="score-bar">
                                    <span style="width: {{ $scorePct }}%;"></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted" style="font-size:13px;">
                            Belum ada data trainer.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.index') }}" class="card-link mt-3">
                    Lihat semua trainer
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Distribusi Course per Kategori</h5>
                </div>

                <div class="category-wrap">
                    <div class="category-donut" style="background: conic-gradient({{ $categoryGradient }});">
                        <div class="category-inner">
                            <div class="donut-number">{{ $totalCourses }}</div>
                            <div class="donut-label">Total</div>
                        </div>
                    </div>

                    <div class="category-list">
                        @forelse($categoryStats as $stat)
                            <div class="category-item">
                                <span class="legend-dot" style="background:{{ $stat['color'] }};"></span>
                                <span>{{ $stat['name'] }}</span>
                                <strong>{{ $stat['total'] }} ({{ $stat['pct'] }}%)</strong>
                            </div>
                        @empty
                            <div class="text-muted" style="font-size:13px;">
                                Belum ada data kategori.
                            </div>
                        @endforelse
                    </div>
                </div>

                <a href="{{ route('admin.trainer.index') }}" class="card-link mt-3">
                    Lihat semua kategori
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Feedback Terbaru</h5>
                </div>

                <div class="feedback-list">
                    @forelse($feedbackItems as $item)
                        <div class="feedback-item">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($item['name'] ?? 'User') }}&background=2745e8&color=fff&bold=true"
                                class="feedback-avatar" alt="{{ $item['name'] ?? 'User' }}">

                            <div>
                                <div class="stars">{{ $item['stars'] ?? '☆☆☆☆☆' }}</div>
                                <div class="feedback-title">{{ $item['title'] ?? '' }}</div>
                                <div class="feedback-name">{{ $item['name'] ?? 'User' }}</div>
                            </div>

                            <div class="feedback-time">{{ $item['time'] ?? '-' }}</div>
                        </div>
                    @empty
                        <div class="text-muted" style="font-size:13px;">
                            Belum ada feedback terbaru.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.material.approved') }}" class="card-link mt-3">
                    Lihat semua feedback
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Daftar Seluruh Trainer -->
        <h4 style="font-weight: 800; font-size: 18px; color: var(--dash-navy); margin: 36px 0 16px; letter-spacing: -.4px; display: flex; align-items: center; gap: 8px;">
            <i class="bi bi-people-fill text-primary"></i> Daftar Seluruh Trainer
        </h4>
        <div class="dash-card mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Trainer</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Bergabung</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainers as $trainerItem)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $trainerItem->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($trainerItem->name) . '&background=2745e8&color=fff&bold=true') }}" 
                                             alt="{{ $trainerItem->name }}" 
                                             class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $trainerItem->name }}</div>
                                            <div class="small text-muted">{{ $trainerItem->phone ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $trainerItem->email }}</td>
                                <td>
                                    @php
                                        $statusLabel = match($trainerItem->user_status ?? 'active') {
                                            'active' => 'Aktif',
                                            'inactive' => 'Tidak Tersedia',
                                            'suspended' => 'Ditangguhkan',
                                            default => 'Aktif',
                                        };
                                        $statusColor = match($trainerItem->user_status ?? 'active') {
                                            'active' => 'success',
                                            'inactive' => 'warning text-dark',
                                            'suspended' => 'danger',
                                            default => 'success',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="small text-muted">{{ $trainerItem->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.trainer.show', $trainerItem->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                                        <i class="bi bi-person-lines-fill me-1"></i> Profil
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada trainer terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($trainers->hasPages())
                <div class="d-flex justify-content-end mt-4">
                    {{ $trainers->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>

        <div class="insight-card">
            <div class="insight-left">
                <div class="insight-icon">
                    <i class="bi bi-lightbulb-fill"></i>
                </div>

                <div>
                    <div class="insight-title">Platform Insights</div>
                    <p class="insight-text">
                        Terus tingkatkan kualitas konten dan dukung para trainer untuk menciptakan pengalaman belajar
                        terbaik!
                    </p>
                </div>
            </div>

            <a href="{{ route('admin.trainer.material.approvals') }}" class="btn-report">
                <i class="bi bi-graph-up-arrow"></i>
                Lihat Laporan Lengkap
                <i class="bi bi-chevron-down"></i>
            </a>
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
                { name: 'Course', color: '#2f5bff', values: data.course || [] },
                { name: 'Event', color: '#19bd6b', values: data.event || [] },
                { name: 'Materi', color: '#ff970f', values: data.material || [] }
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
@endpush