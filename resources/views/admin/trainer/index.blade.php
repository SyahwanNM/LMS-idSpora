@extends('layouts.admin-trainer')

@section('title', request()->query('view') === 'list' ? 'Daftar Seluruh Trainer - Admin' : 'Dashboard Admin Trainer')

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
            --dash-primary: #1e1b4b;
            --dash-purple: #1e1b4b;
            --dash-blue: #1e1b4b;
            --dash-green: #10b981;
            --dash-orange: #f59e0b;
            --dash-red: #f43f5e;
            --dash-muted: #64748b;
            --dash-soft: #f8fafc;
            --dash-border: rgba(148, 163, 184, 0.08);
            --dash-card-shadow: 0 10px 30px -10px rgba(30, 41, 59, 0.04), 0 20px 40px -15px rgba(15, 23, 42, 0.03);
        }

        body {
            background-color: #f8fafc;
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
            gap: 28px;
            color: var(--dash-navy);
        }

        /* Hero banner & Target widget */
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 28px;
        }
        .welcome-card {
            background-color: var(--dash-purple);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 24px;
            padding: 36px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            overflow: hidden;
            box-shadow: var(--dash-card-shadow);
        }
        .welcome-card::after {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 50%;
            top: -120px;
            right: -60px;
            pointer-events: none;
            z-index: 1;
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
            font-size: 13px;
            color: rgba(255, 255, 255, 0.65);
            margin-bottom: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .welcome-title-main {
            font-size: 32px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 10px;
            letter-spacing: -0.03em;
            line-height: 1.2;
        }
        .welcome-desc {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.75);
            margin-bottom: 24px;
            font-weight: 500;
            line-height: 1.5;
        }
        .welcome-stats {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        .welcome-stat-pill {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 18px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 155px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .welcome-stat-pill:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        .welcome-stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .welcome-stat-icon.orange { background: rgba(148, 163, 184, 0.15); color: #cbd5e1; }
        .welcome-stat-icon.green { background: rgba(16, 185, 129, 0.2); color: #34d399; }
        .welcome-stat-icon.blue { background: rgba(37, 99, 235, 0.2); color: #60a5fa; }
        .welcome-stat-num {
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
            letter-spacing: -0.5px;
        }
        .welcome-stat-label {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 500;
        }

        .target-card {
            background: #fff;
            border: 1px solid var(--dash-border);
            border-radius: 24px;
            padding: 28px;
            box-shadow: var(--dash-card-shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .target-date-row {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-bottom: 18px;
            border-bottom: 1px solid #f1f5f9;
        }
        .target-date-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: #f8fafc;
            color: #1e1b4b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 1px solid rgba(30, 27, 75, 0.05);
        }
        .target-date-title {
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.1px;
        }
        .target-date-sub {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }
        .target-body {
            margin-top: 18px;
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
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .target-value {
            font-size: 26px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            letter-spacing: -0.02em;
        }
        .target-progress-container {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 10px;
        }
        .target-progress-bar {
            height: 10px;
            border-radius: 99px;
            background: #f1f5f9;
            flex: 1;
            overflow: hidden;
        }
        .target-progress-fill {
            height: 100%;
            background: var(--dash-primary);
            border-radius: 99px;
        }
        .target-sparkline {
            width: 60px;
            height: 30px;
            flex-shrink: 0;
        }
        .target-footer-text {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        /* Metric Grid */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
        }
        .metric-card-clean {
            background: #fff;
            border: 1px solid var(--dash-border);
            border-radius: 24px;
            padding: 24px;
            box-shadow: var(--dash-card-shadow);
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .metric-card-clean:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.08);
        }
        .metric-card-clean.theme-purple:hover,
        .metric-card-clean.theme-blue:hover {
            border-color: rgba(37, 99, 235, 0.2);
            box-shadow: 0 16px 36px -8px rgba(37, 99, 235, 0.08);
        }
        .metric-card-clean.theme-orange:hover {
            border-color: rgba(245, 158, 11, 0.2);
            box-shadow: 0 16px 36px -8px rgba(245, 158, 11, 0.08);
        }
        .metric-card-clean.theme-green:hover {
            border-color: rgba(16, 185, 129, 0.2);
            box-shadow: 0 16px 36px -8px rgba(16, 185, 129, 0.08);
        }
        .metric-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .metric-circle-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .metric-circle-icon.purple { background: #eff6ff; color: #1e1b4b; }
        .metric-circle-icon.blue { background: #eff6ff; color: #1e1b4b; }
        .metric-circle-icon.orange { background: #fef3c7; color: #d97706; }
        .metric-circle-icon.green { background: #ecfdf5; color: #10b981; }
        
        .metric-titles {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .metric-clean-label {
            font-size: 13px;
            color: #64748b;
            font-weight: 600;
        }
        .metric-clean-val-row {
            display: flex;
            align-items: baseline;
            gap: 6px;
        }
        .metric-clean-value {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
            letter-spacing: -0.03em;
        }
        .metric-clean-suffix {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .metric-clean-change {
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .metric-clean-change.up { color: #10b981; }
        .metric-clean-change.down { color: #f43f5e; }
        .metric-clean-change.neutral { color: var(--dash-muted); }

        /* General Dash Card */
        .dash-card {
            background: #ffffff;
            border: 1px solid var(--dash-border);
            border-radius: 24px;
            box-shadow: var(--dash-card-shadow);
            padding: 28px;
            display: flex;
            flex-direction: column;
            gap: 22px;
            transition: all 0.3s ease;
        }
        .dash-card:hover {
            border-color: rgba(30, 27, 75, 0.1);
            box-shadow: 0 16px 36px -8px rgba(15, 23, 42, 0.06);
        }
        .card-header-clean {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 4px;
        }
        .card-title-clean {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-link {
            color: var(--dash-blue);
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }
        .card-link:hover {
            color: var(--dash-primary);
            text-decoration: none;
            gap: 9px;
        }

        /* 3-column rows */
        .main-three-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr 1fr;
            gap: 28px;
        }
        .bottom-three-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr;
            gap: 28px;
        }

        /* Donut Charts */
        .approval-donut {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }
        .approval-donut:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.12);
        }
        .approval-donut-inner {
            width: 136px;
            height: 136px;
            border-radius: 50%;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        /* Timeline */
        .timeline-container {
            position: relative;
            padding-left: 24px;
            margin-left: 10px;
            border-left: 2px dashed #e2e8f0;
            display: flex;
            flex-direction: column;
            gap: 24px;
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
            left: -31px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 3px solid currentColor;
            background: #fff;
            box-shadow: 0 0 0 4px #fff;
        }
        .timeline-dot.red { color: #f43f5e; }
        .timeline-dot.yellow { color: #475569; }
        .timeline-dot.green { color: #10b981; }
        .timeline-dot.blue { color: #1e1b4b; }
        
        .timeline-content {
            flex: 1;
            min-width: 0;
            padding-right: 14px;
        }
        .timeline-header-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }
        .timeline-time-label {
            font-weight: 700;
            font-size: 13px;
            color: #0f172a;
        }
        .timeline-badge {
            font-size: 9px;
            font-weight: 800;
            padding: 3px 7px;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .timeline-title {
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 2px;
        }
        .timeline-trainer {
            font-size: 12px;
            color: #64748b;
        }
        .timeline-date {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 3px;
        }
        .timeline-icon-box {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }
        .timeline-icon-box.neutral {
            background: #f8fafc;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        /* Work queue styling */
        .queue-item-card {
            padding: 20px !important;
            border: 1px solid var(--dash-border) !important;
            border-radius: 18px !important;
            background: #fff !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 16px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        .queue-item-card:hover {
            transform: translateY(-3px) !important;
            border-color: rgba(37, 99, 235, 0.15) !important;
            box-shadow: 0 10px 24px -6px rgba(15, 23, 42, 0.05) !important;
        }
        .queue-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .queue-info-title {
            font-weight: 700;
            font-size: 15px;
            color: #0f172a;
            margin-bottom: 4px;
            letter-spacing: -0.1px;
        }
        .queue-info-meta {
            font-size: 12px;
            color: #64748b;
        }
        .queue-badge {
            display: inline-block;
            font-weight: 800;
            font-size: 9px;
            border-radius: 6px;
            padding: 3px 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .queue-dates {
            text-align: right;
            font-size: 11px;
            flex-shrink: 0;
            line-height: 1.4;
        }
        .queue-date-label {
            color: #64748b;
            margin-bottom: 1px;
        }
        .queue-date-val {
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .queue-action-btn {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .queue-action-btn:hover {
            transform: scale(1.08);
        }

        /* Top Trainers & Feedback */
        .trainer-list, .feedback-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .trainer-item {
            border: 1px solid var(--dash-border);
            border-radius: 18px;
            padding: 16px;
            display: flex;
            gap: 14px;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: #fff;
        }
        .trainer-item:hover {
            transform: translateY(-3px);
            border-color: rgba(37, 99, 235, 0.15);
            box-shadow: 0 12px 24px -8px rgba(15, 23, 42, 0.04);
        }
        .rank-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 12px;
            flex-shrink: 0;
        }
        .rank-icon.gold { background: #eff6ff; color: #1e1b4b; }
        .rank-icon.silver { background: #f1f5f9; color: #475569; }
        .rank-icon.bronze { background: #f8fafc; color: #64748b; }
        
        .trainer-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
            border: 1.5px solid #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }
        .trainer-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--dash-navy);
            margin-bottom: 2px;
        }
        .trainer-meta {
            font-size: 12px;
            color: var(--dash-muted);
        }
        .score-area {
            text-align: right;
            font-size: 11px;
            color: var(--dash-muted);
            margin-left: auto;
            min-width: 70px;
            flex-shrink: 0;
        }
        .score-area strong {
            font-size: 16px;
            color: var(--dash-navy);
            font-weight: 800;
        }
        .score-bar {
            height: 5px;
            border-radius: 99px;
            background: #f1f5f9;
            overflow: hidden;
            margin-top: 5px;
        }
        .score-bar span {
            display: block;
            height: 100%;
            border-radius: 99px;
            background: var(--dash-primary);
        }

        .feedback-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--dash-border);
        }
        .feedback-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }
        .stars {
            color: #1e1b4b;
            font-size: 13px;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .feedback-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--dash-navy);
            margin-bottom: 2px;
        }
        .feedback-text {
            font-style: italic;
            font-size: 12.5px;
            color: var(--dash-muted);
            margin-bottom: 6px;
            line-height: 1.4;
        }
        .feedback-name {
            font-size: 12px;
            color: #475569;
            font-weight: 600;
        }
        .feedback-time {
            font-size: 11px;
            color: var(--dash-muted);
            margin-left: auto;
            flex-shrink: 0;
        }

        /* SVG Chart */
        .chart-select {
            height: 38px;
            border: 1px solid var(--dash-border);
            border-radius: 10px;
            padding: 0 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--dash-navy);
            background: #fff;
            outline: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .chart-select:hover {
            border-color: rgba(30, 27, 75, 0.2);
        }
        .legend-row {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 6px;
            padding-left: 36px;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            color: var(--dash-muted);
        }
        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        .line-chart {
            width: 100%;
            height: 220px;
            display: block;
            cursor: pointer;
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
            margin-top: 10px;
            padding: 0 8px 0 2px;
        }
        .chart-tooltip {
            position: absolute;
            background: rgba(15, 23, 42, 0.96);
            color: #fff;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 1.5;
            min-width: 140px;
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
            border-collapse: separate;
            border-spacing: 0;
            background-color: #ffffff;
        }
        .table-premium th {
            font-size: 11px !important;
            font-weight: 800 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            color: #64748b !important;
            background-color: #f8fafc !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 18px 24px !important;
            text-align: left;
        }
        .table-premium td {
            padding: 18px 24px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 13.5px !important;
            color: #334155 !important;
            vertical-align: middle !important;
        }
        .table-premium tbody tr {
            transition: all 0.2s ease;
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
                font-size: 26px;
            }
            .welcome-card {
                flex-direction: column;
                align-items: stretch;
                padding: 24px;
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
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px !important;
            }
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="admin-dashboard">

        @if(request()->query('view') !== 'list')
        <!-- Row 1: Hero Card and Date/Target Card -->
        <div class="hero-grid">
            <!-- Welcome Banner -->
            <div class="welcome-card">
                <div class="welcome-left">
                    <div class="welcome-title-sub">Selamat datang kembali,</div>
                    <div class="welcome-title-main">Admin Trainer! 👋</div>
                    <div class="welcome-desc">Kelola trainer, materi, course, dan event dengan lebih efisien.</div>
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
                        <span class="metric-clean-label">Course Baru (Bulan Ini)</span>
                        <div class="metric-clean-val-row">
                            <span class="metric-clean-value">{{ $totalCourses }}</span>
                            <span class="metric-clean-suffix">Course</span>
                        </div>
                    </div>
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
                        <span class="metric-clean-label">Event Baru (Bulan Ini)</span>
                        <div class="metric-clean-val-row">
                            <span class="metric-clean-value">{{ $totalEvents }}</span>
                            <span class="metric-clean-suffix">Event</span>
                        </div>
                    </div>
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
                        <span class="metric-clean-label">Sertifikat Terbit (Bulan Ini)</span>
                        <div class="metric-clean-val-row">
                            <span class="metric-clean-value">{{ $approvedMaterials }}</span>
                            <span class="metric-clean-suffix">Sertifikat</span>
                        </div>
                    </div>
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
                        <i class="bi bi-list-task" style="color:var(--dash-blue); margin-right:6px;"></i>Antrean Kerja Admin
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
                                $iconBg = '#f1f5f9';
                                $iconColor = '#475569';
                                $badgeBg = '#f1f5f9';
                                $badgeColor = '#475569';
                                $badgeText = 'HARI INI';
                                $deadlineColor = '#475569';
                                $deadlineText = 'Hari ini';
                                $btnBg = '#f8fafc';
                                $btnColor = '#475569';
                            } else {
                                $diffInDays = $now->diffInDays($deadlineDate, false);
                                $iconBg = '#eff6ff';
                                $iconColor = '#1e1b4b';
                                $badgeBg = '#eff6ff';
                                $badgeColor = '#1e1b4b';
                                $badgeText = 'ON PROGRESS';
                                $deadlineColor = '#1e1b4b';
                                $deadlineText = ($diffInDays <= 0) ? 'Hari ini' : ($diffInDays . ' hari lagi');
                                $btnBg = '#eff6ff';
                                $btnColor = '#1e1b4b';
                            }
                        @endphp
                        <div class="queue-item-card">
                            <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0;">
                                <div class="queue-icon-box" style="background: {{ $iconBg }}; color: {{ $iconColor }};">
                                    <i class="bi {{ $iconClass }}"></i>
                                </div>
                                <div style="flex: 1; min-width: 0; display: flex; justify-content: space-between; align-items: center; gap: 16px;">
                                    <div style="min-width: 0;">
                                        <div class="queue-info-title" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $item['source'] ?? $item['title'] }}
                                        </div>
                                        <div class="queue-info-meta" style="margin-bottom: 6px;">
                                            Trainer: <strong style="color: #475569;">{{ $item['trainer'] }}</strong>
                                        </div>
                                        <span class="queue-badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                                            {{ $badgeText }}
                                        </span>
                                    </div>
                                    
                                    <div class="queue-dates">
                                        <div class="queue-date-label">Dikirim</div>
                                        <div class="queue-date-val">
                                            {{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d M Y') }}
                                        </div>
                                        <div class="queue-date-label">Deadline</div>
                                        <div class="queue-date-val" style="color: {{ $deadlineColor }};">
                                            {{ $deadlineText }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="flex-shrink: 0;">
                                <a href="{{ $item['url'] }}" class="queue-action-btn" style="background: {{ $btnBg }}; color: {{ $btnColor }};">
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
            <div class="dash-card" style="position: relative;">
                <div class="chart-tooltip" id="donut-chart-tooltip" style="position: absolute; opacity: 0; pointer-events: none; transition: opacity 0.15s ease, transform 0.15s ease; transform: translateY(-4px); z-index: 20;"></div>
                
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Status Persetujuan</h5>
                </div>

                @php
                    $pendingPct = (float) ($approvalStats['pending_pct'] ?? 0);
                    $approvedPct = (float) ($approvalStats['approved_pct'] ?? 0);
                    $splitPct = min(100, $pendingPct + $approvedPct);
                    $totalApprovals = (int) ($approvalStats['total'] ?? 0);
                    $gradientStyle = $totalApprovals > 0 
                        ? "conic-gradient(#10b981 0 {$approvedPct}%, #f59e0b {$approvedPct}% {$splitPct}%, #ef4444 {$splitPct}% 100%)" 
                        : "#e2e8f0";
                @endphp
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 20px; width: 100%; flex: 1; padding: 10px 0;">
                    <div class="approval-donut" id="approval-donut-chart"
                        data-approved="{{ $approvalStats['approved'] }}"
                        data-approved-pct="{{ $approvalStats['approved_pct'] }}"
                        data-pending="{{ $approvalStats['pending'] }}"
                        data-pending-pct="{{ $approvalStats['pending_pct'] }}"
                        data-rejected="{{ $approvalStats['rejected'] }}"
                        data-rejected-pct="{{ $approvalStats['rejected_pct'] }}"
                        style="background: {{ $gradientStyle }}; margin: 0 auto;">
                        <div class="approval-donut-inner">
                            <div style="font-size: 32px; font-weight: 800; color: var(--dash-navy); line-height: 1; letter-spacing: -0.03em;">{{ $approvalStats['total'] }}</div>
                            <div style="font-size: 10px; font-weight: 700; color: var(--dash-muted); margin-top: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Total Materi</div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; width: 100%; margin-top: 8px;">
                        <div style="text-align: center; background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.15); padding: 12px 8px; border-radius: 16px; transition: all 0.2s ease;">
                            <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: #059669; font-weight: 700; margin-bottom: 4px;">
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #10b981;"></span>
                                Disetujui
                            </div>
                            <div style="font-size: 14px; font-weight: 800; color: #0f172a; letter-spacing: -0.2px;">
                                {{ $approvalStats['approved'] }} <span style="font-weight:600; font-size:11px; color:#64748b">({{ $approvalStats['approved_pct'] }}%)</span>
                            </div>
                        </div>
                        <div style="text-align: center; background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.15); padding: 12px 8px; border-radius: 16px; transition: all 0.2s ease;">
                            <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: #d97706; font-weight: 700; margin-bottom: 4px;">
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #f59e0b;"></span>
                                Menunggu
                            </div>
                            <div style="font-size: 14px; font-weight: 800; color: #0f172a; letter-spacing: -0.2px;">
                                {{ $approvalStats['pending'] }} <span style="font-weight:600; font-size:11px; color:#64748b">({{ $approvalStats['pending_pct'] }}%)</span>
                            </div>
                        </div>
                        <div style="text-align: center; background: rgba(239, 68, 68, 0.06); border: 1px solid rgba(239, 68, 68, 0.15); padding: 12px 8px; border-radius: 16px; transition: all 0.2s ease;">
                            <div style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; color: #dc2626; font-weight: 700; margin-bottom: 4px;">
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></span>
                                Ditolak
                            </div>
                            <div style="font-size: 14px; font-weight: 800; color: #0f172a; letter-spacing: -0.2px;">
                                {{ $approvalStats['rejected'] }} <span style="font-weight:600; font-size:11px; color:#64748b">({{ $approvalStats['rejected_pct'] }}%)</span>
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
                                $badgeBg = '#f1f5f9';
                                $badgeColor = '#475569';
                            } else {
                                $timeLabelText = $item['badge_text'] ?? '3 Hari Lagi';
                                $badgeText = 'ON PROGRESS';
                                $badgeBg = '#eff6ff';
                                $badgeColor = '#1e1b4b';
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

                <a href="{{ route('admin.trainer.index', ['view' => 'list']) }}" class="card-link mt-3">
                    Kelola dari profil trainer
                    <i class="bi bi-arrow-right"></i>
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
                    <span class="legend-item" id="legend-course" style="cursor: pointer; user-select: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                        <span class="legend-dot" style="background:#1e1b4b;"></span>
                        Course Dibuat
                    </span>

                    <span class="legend-item" id="legend-event" style="cursor: pointer; user-select: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                        <span class="legend-dot" style="background:#10b981;"></span>
                        Event Berjalan
                    </span>

                    <span class="legend-item" id="legend-material" style="cursor: pointer; user-select: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                        <span class="legend-dot" style="background:#475569;"></span>
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
                    @php
                        $coursePointsStr = $chartPoints['course'] ?? '';
                        $coursePoints = array_filter(explode(' ', $coursePointsStr));
                        
                        $eventPointsStr = $chartPoints['event'] ?? '';
                        $eventPoints = array_filter(explode(' ', $eventPointsStr));
                        
                        $materialPointsStr = $chartPoints['material'] ?? '';
                        $materialPoints = array_filter(explode(' ', $materialPointsStr));
                    @endphp
                    <svg class="line-chart" id="dashboard-line-chart" viewBox="0 0 680 260" preserveAspectRatio="none">
                        <line x1="0" y1="35" x2="680" y2="35" stroke="#e7edf6" />
                        <line x1="0" y1="80" x2="680" y2="80" stroke="#e7edf6" />
                        <line x1="0" y1="125" x2="680" y2="125" stroke="#e7edf6" />
                        <line x1="0" y1="170" x2="680" y2="170" stroke="#e7edf6" />
                        <line x1="0" y1="215" x2="680" y2="215" stroke="#e7edf6" />

                        <g id="group-course" style="transition: opacity 0.3s ease;">
                            <polyline points="{{ $coursePointsStr }}" fill="none" stroke="#1e1b4b" stroke-width="4" stroke-linecap="round" />
                            @foreach($coursePoints as $point)
                                @if(str_contains($point, ','))
                                    @php list($x, $y) = explode(',', $point); @endphp
                                    <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#1e1b4b" stroke="#ffffff" stroke-width="1.5" />
                                @endif
                            @endforeach
                        </g>

                        <g id="group-event" style="transition: opacity 0.3s ease;">
                            <polyline points="{{ $eventPointsStr }}" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round" />
                            @foreach($eventPoints as $point)
                                @if(str_contains($point, ','))
                                    @php list($x, $y) = explode(',', $point); @endphp
                                    <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#10b981" stroke="#ffffff" stroke-width="1.5" />
                                @endif
                            @endforeach
                        </g>

                        <g id="group-material" style="transition: opacity 0.3s ease;">
                            <polyline points="{{ $materialPointsStr }}" fill="none" stroke="#475569" stroke-width="4" stroke-linecap="round" />
                            @foreach($materialPoints as $point)
                                @if(str_contains($point, ','))
                                    @php list($x, $y) = explode(',', $point); @endphp
                                    <circle cx="{{ $x }}" cy="{{ $y }}" r="5" fill="#475569" stroke="#ffffff" stroke-width="1.5" />
                                @endif
                            @endforeach
                        </g>
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
                <div class="card-header-clean" style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px; border-bottom: 1px solid var(--dash-border); padding-bottom: 12px; margin-bottom: 6px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <h5 class="card-title-clean" style="margin: 0; font-size: 16px; font-weight: 800; color: #0f172a;">
                            <i class="bi bi-trophy-fill" style="color: #f59e0b; margin-right: 6px;"></i>Top Trainer
                        </h5>
                        <a href="{{ route('admin.trainer.index', ['view' => 'list']) }}" class="card-link" style="font-size: 11px; font-weight: 600;">Lihat semua</a>
                    </div>
                    <span style="font-size: 11px; font-weight: 500; color: var(--dash-muted);">(Berdasarkan Total Event & Course)</span>
                </div>

                <div class="trainer-list">
                    @forelse($topTrainers as $index => $trainer)
                        @php
                            $rankClass = ['gold', 'silver', 'bronze'][$index] ?? 'silver';
                            $score = (int) ($trainer->score ?? 0);
                            $badgeText = $score > 0 ? (['Top Creator', 'Fast Responder', 'Rising Trainer'][$index] ?? null) : null;
                            $badgeBg = ['#eff6ff', '#eff6ff', '#f0fdf4'][$index] ?? '#f1f5f9';
                            $badgeColor = ['#1e1b4b', '#1e1b4b', '#10b981'][$index] ?? '#475569';
                            
                            $fallbackUrl = 'https://ui-avatars.com/api/?name=' . urlencode($trainer->name ?? 'Trainer') . '&background=1e1b4b&color=fff&bold=true';
                            $avatarUrl = $trainer->avatar_url ?: $fallbackUrl;
                        @endphp

                        <div class="trainer-item" style="border: 1px solid var(--dash-border); border-radius: 18px; padding: 14px; display: flex; gap: 12px; align-items: center; background: #fff; transition: all 0.2s;">
                            <div class="rank-icon {{ $rankClass }}" style="width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; flex-shrink: 0;">
                                {{ $index + 1 }}
                            </div>

                            <img src="{{ $avatarUrl }}"
                                 onerror="this.onerror=null; this.src='{{ $fallbackUrl }}'"
                                 class="trainer-avatar" 
                                 style="width: 42px; height: 42px; border-radius: 50%; object-fit: cover; flex-shrink: 0; border: 1.5px solid #fff; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);" 
                                 alt="{{ $trainer->name ?? 'Trainer' }}">

                            <div style="min-width: 0; flex: 1;">
                                <div class="trainer-name" style="margin-bottom: 2px;">
                                    <a href="{{ route('admin.trainer.show', $trainer->id ?? 0) }}" class="text-decoration-none text-dark hover-primary" style="font-weight: 700; font-size: 13.5px; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $trainer->name ?? 'Trainer' }}
                                    </a>
                                </div>
                                @if($badgeText)
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                         <span class="badge" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 9px; padding: 2px 6px; font-weight: 800; border-radius: 4px;">{{ $badgeText }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="score-area" style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; justify-content: center; min-width: 80px; flex-shrink: 0; margin-left: 12px; font-size: 12px; font-weight: 700; color: #0f172a; line-height: 1.4;">
                                <div>{{ $trainer->courses_as_trainer_count ?? 0 }} Course</div>
                                <div style="color: var(--dash-muted); font-size: 11px; font-weight: 500;">{{ $trainer->events_as_trainer_count ?? 0 }} Event</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-3" style="font-size:13px;">
                            Belum ada data trainer.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.trainer.index', ['view' => 'list']) }}" class="card-link mt-auto" style="border-top: 1px solid var(--dash-border); padding-top: 12px; margin-top: 4px;">
                    Lihat semua trainer
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <!-- Feedback Terbaru -->
            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Feedback Terbaru</h5>
                    <a href="{{ route('admin.crm.feedback.index') }}" class="card-link" style="font-size: 11px; font-weight: 600;">Lihat semua &rarr;</a>
                </div>

                <div class="feedback-list">
                    @forelse($feedbackItems as $item)
                        @php
                            $bgColors = ['#1e1b4b', '#1e1b4b', '#475569'];
                            $bgColor = $bgColors[$loop->index % 3];
                            $initials = collect(explode(' ', $item['name']))->map(fn($n) => strtoupper(substr($n, 0, 1)))->take(2)->join('');
                        @endphp
                        <div class="feedback-item">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ $bgColor }}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;">
                                {{ $initials }}
                            </div>

                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex !important; align-items: center !important; width: 100% !important; margin-bottom: 4px !important;">
                                    <div class="stars" style="display: inline-flex !important; width: auto !important; min-width: 0 !important; justify-content: flex-start !important; margin-bottom: 0 !important; margin-left: 0 !important; margin-right: auto !important;">{{ $item['stars'] ?? '★★★★★' }}</div>
                                    <div class="feedback-time" style="font-size: 11px !important; color: var(--dash-muted) !important; margin-left: 12px !important; margin-right: 0 !important; flex-shrink: 0 !important;">{{ $item['time'] ?? '-' }}</div>
                                </div>
                                <div class="feedback-title" style="font-size: 12px; font-weight: 700; color: var(--dash-navy); margin-bottom: 2px;">{{ $item['title'] ?? '' }}</div>
                                <div class="feedback-text" style="font-style: italic; font-size: 12px; color: var(--dash-muted); margin-bottom: 4px; line-height: 1.3;">
                                    "{{ $item['comment'] ?? 'Tidak ada komentar.' }}"
                                </div>
                                <div class="feedback-name" style="font-weight: 600; color: #475569; font-size: 11px;">{{ $item['name'] ?? 'User' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center py-3" style="font-size:13px;">
                            Belum ada feedback terbaru.
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.crm.feedback.index') }}" class="card-link mt-auto">
                    Lihat semua feedback
                    <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
        @endif
        @if(request()->query('view') === 'list')
        <!-- Row 5: Daftar Seluruh Trainer -->
        <div id="daftar-trainer" class="dash-card mb-4" style="padding: 0; overflow: hidden; margin-top: 0;">
            <div class="card-header-clean" style="padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <h5 class="card-title-clean">
                    <i class="bi bi-people-fill" style="color: #1e1b4b; font-size: 20px;"></i>
                    Daftar Seluruh Trainer
                </h5>
                
                <!-- Search & Filter Form -->
                <form action="{{ route('admin.trainer.index') }}" method="GET" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 0;">
                    <input type="hidden" name="view" value="list">
                    
                    <!-- Search Input -->
                    <div style="position: relative; display: flex; align-items: center;">
                        <i class="bi bi-search" style="position: absolute; left: 14px; color: var(--dash-muted); font-size: 14px;"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, telepon..." 
                            style="height: 40px; padding: 0 16px 0 38px; border: 1px solid rgba(30, 27, 75, 0.15); border-radius: 12px; font-size: 13px; font-weight: 600; outline: none; width: 260px; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                            onfocus="this.style.borderColor='var(--dash-blue)'; this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)'"
                            onblur="this.style.borderColor='rgba(30, 27, 75, 0.15)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)'">
                    </div>

                    <!-- Sort Select -->
                    <div style="position: relative; display: flex; align-items: center;">
                        <select name="sort" onchange="this.form.submit()" 
                            style="height: 40px; padding: 0 36px 0 16px; border: 1px solid rgba(30, 27, 75, 0.15); border-radius: 12px; font-size: 13px; font-weight: 600; color: #0f172a; outline: none; background: #fff; cursor: pointer; transition: all 0.2s; -webkit-appearance: none; appearance: none; box-shadow: 0 2px 4px rgba(0,0,0,0.02);"
                            onfocus="this.style.borderColor='var(--dash-blue)'; this.style.boxShadow='0 0 0 3px rgba(37,99,235,0.1)'"
                            onblur="this.style.borderColor='rgba(30, 27, 75, 0.15)'; this.style.boxShadow='0 2px 4px rgba(0,0,0,0.02)'">
                            <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nama (A - Z)</option>
                            <option value="name_desc" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Nama (Z - A)</option>
                        </select>
                        <i class="bi bi-chevron-down" style="position: absolute; right: 14px; color: var(--dash-muted); font-size: 12px; pointer-events: none;"></i>
                    </div>

                    <!-- Reset Button if filtered -->
                    @if(request()->filled('search') || request()->filled('sort'))
                        <a href="{{ route('admin.trainer.index', ['view' => 'list']) }}" class="btn btn-light d-flex align-items-center justify-content-center" 
                            style="height: 40px; border: 1px solid rgba(30, 27, 75, 0.1); border-radius: 12px; font-size: 13px; font-weight: 700; color: var(--dash-muted); padding: 0 16px; background: #f8fafc; transition: all 0.2s; text-decoration: none;">
                            Reset
                        </a>
                    @endif
                </form>
            </div>
            <div class="table-responsive">
                <table class="table-premium align-middle mb-0">
                    <thead>
                        <tr>
                            <th>TRAINER</th>
                            <th>SKILL UTAMA</th>
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
                                
                                // Fetch real specializations from User model 'trainer_specializations' cast
                                $skillsHtml = '';
                                $specs = $trainerItem->trainer_specializations;
                                if (!is_array($specs)) {
                                    $specs = [];
                                }
                                if (empty($specs)) {
                                    $skillsHtml = '<span style="display: inline-block; padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;">-</span>';
                                } else {
                                    $takeSpecs = array_slice($specs, 0, 3);
                                    $restCount = count($specs) - count($takeSpecs);
                                    
                                    $colors = [
                                        ['#eff6ff', '#1e1b4b', '#dbeafe'], // Dark Blue
                                        ['#f0fdf4', '#10b981', '#bbf7d0'], // Green
                                        ['#f1f5f9', '#475569', '#cbd5e1'], // Slate
                                    ];
                                    
                                    foreach ($takeSpecs as $idx => $s) {
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
                                    'inactive' => 'background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;',
                                    'suspended' => 'background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5;',
                                    default => 'background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;',
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="{{ $trainerItem->avatar_url ?? ('https://ui-avatars.com/api/?name=' . urlencode($trainerItem->name) . '&background=1e3a8a&color=fff&bold=true') }}" 
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
                                <td class="fw-semibold text-dark" style="font-size: 13px;">{{ $trainerItem->courses_as_trainer_count }}</td>
                                <td class="fw-semibold text-dark" style="font-size: 13px;">{{ $trainerItem->events_as_trainer_count }}</td>
                                <td class="fw-semibold text-dark" style="font-size: 13px;">{{ $pesertaCount }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="fw-bold text-dark" style="font-size: 13px;">{{ $ratingVal }}</span>
                                        <div style="color: #1e1b4b; font-size: 10px; display: flex; gap: 1px;">
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
                                        <a href="{{ route('admin.trainer.show', $trainerItem->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold" style="background: #1e1b4b; border-color: #1e1b4b; font-size: 12px;">
                                            Profil
                                        </a>
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
            <div class="d-flex align-items-center justify-content-between mt-3 p-3" style="border-top: 1px solid #f1f5f9; background: #fff;">
                <div class="small text-muted" style="font-weight: 600; font-size: 13px;">
                    Menampilkan Halaman {{ $trainers->currentPage() }} dari {{ $trainers->lastPage() }}
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if(!$trainers->onFirstPage())
                        <a href="{{ $trainers->previousPageUrl() }}" class="btn d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid rgba(30, 27, 75, 0.15); color: #1e1b4b; background: #fff; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='var(--dash-blue)'" onmouseout="this.style.background='#fff'; this.style.borderColor='rgba(30, 27, 75, 0.15)'">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    @else
                        <button class="btn d-flex align-items-center justify-content-center" disabled style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid rgba(30, 27, 75, 0.08); color: #cbd5e1; background: #f8fafc; cursor: not-allowed;">
                            <i class="bi bi-chevron-left"></i>
                        </button>
                    @endif

                    @if($trainers->hasMorePages())
                        <a href="{{ $trainers->nextPageUrl() }}" class="btn d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid rgba(30, 27, 75, 0.15); color: #1e1b4b; background: #fff; transition: all 0.2s;" onmouseover="this.style.background='#f8fafc'; this.style.borderColor='var(--dash-blue)'" onmouseout="this.style.background='#fff'; this.style.borderColor='rgba(30, 27, 75, 0.15)'">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    @else
                        <button class="btn d-flex align-items-center justify-content-center" disabled style="width: 40px; height: 40px; border-radius: 12px; border: 1px solid rgba(30, 27, 75, 0.08); color: #cbd5e1; background: #f8fafc; cursor: not-allowed;">
                            <i class="bi bi-chevron-right"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @endif

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
                { name: 'Course Dibuat', color: '#1e1b4b', values: data.course || [] },
                { name: 'Event Berjalan', color: '#10b981', values: data.event || [] },
                { name: 'Materi Dikirim', color: '#475569', values: data.material || [] }
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

            var activeSeries = { course: true, event: true, material: true };

            function toggleSeries(name, groupEl, legendEl) {
                activeSeries[name] = !activeSeries[name];
                if (activeSeries[name]) {
                    groupEl.style.opacity = '1';
                    legendEl.style.opacity = '1';
                    legendEl.style.textDecoration = 'none';
                    legendEl.style.color = 'var(--dash-muted)';
                } else {
                    groupEl.style.opacity = '0.12';
                    legendEl.style.opacity = '0.4';
                    legendEl.style.textDecoration = 'line-through';
                    legendEl.style.color = '#94a3b8';
                }
            }

            var groupCourse = document.getElementById('group-course');
            var legendCourse = document.getElementById('legend-course');
            if (groupCourse && legendCourse) {
                legendCourse.addEventListener('click', function () {
                    toggleSeries('course', groupCourse, legendCourse);
                });
            }

            var groupEvent = document.getElementById('group-event');
            var legendEvent = document.getElementById('legend-event');
            if (groupEvent && legendEvent) {
                legendEvent.addEventListener('click', function () {
                    toggleSeries('event', groupEvent, legendEvent);
                });
            }

            var groupMaterial = document.getElementById('group-material');
            var legendMaterial = document.getElementById('legend-material');
            if (groupMaterial && legendMaterial) {
                legendMaterial.addEventListener('click', function () {
                    toggleSeries('material', groupMaterial, legendMaterial);
                });
            }

            function formatTooltip(index) {
                var label = labels[index] || '';
                var html = '<div style="font-weight:700;margin-bottom:6px;border-bottom:1px solid rgba(255,255,255,0.15);padding-bottom:4px;">' + label + '</div>';
                series.forEach(function (item, idx) {
                    var key = ['course', 'event', 'material'][idx];
                    var isActive = activeSeries[key] !== false;
                    var value = item.values[index] ?? 0;
                    if (isActive) {
                        html += '<div style="color:' + item.color + ';font-weight:700;display:flex;justify-content:space-between;gap:12px;"><span>' + item.name + ':</span><strong>' + value + '</strong></div>';
                    } else {
                        html += '<div style="color:#64748b;text-decoration:line-through;opacity:0.5;display:flex;justify-content:space-between;gap:12px;"><span>' + item.name + ':</span><strong>' + value + '</strong></div>';
                    }
                });
                tooltip.innerHTML = html;
            }

            function setHover(index, clientX, clientY) {
                var x = xStart + (xSpan * index / Math.max(1, labels.length - 1));
                hoverLine.setAttribute('x1', x);
                hoverLine.setAttribute('x2', x);
                hoverLine.style.opacity = '1';

                series.forEach(function (item, idx) {
                    var key = ['course', 'event', 'material'][idx];
                    var isActive = activeSeries[key] !== false;
                    if (isActive) {
                        var value = item.values[index] ?? 0;
                        var ratio = max > 0 ? (value / max) : 0;
                        var y = yBottom - (ratio * ySpan);
                        hoverDots[idx].setAttribute('cx', x);
                        hoverDots[idx].setAttribute('cy', y);
                        hoverDots[idx].style.opacity = '1';
                    } else {
                        hoverDots[idx].style.opacity = '0';
                    }
                });

                formatTooltip(index);

                var rect = svg.getBoundingClientRect();
                var tooltipX = clientX - rect.left + 24;
                var tooltipY = clientY - rect.top - 60;

                tooltip.style.left = tooltipX + 'px';
                tooltip.style.top = tooltipY + 'px';
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
                setHover(index, event.clientX, event.clientY);
            });

            svg.addEventListener('mouseleave', hideHover);
        })();

        (function () {
            var donut = document.getElementById('approval-donut-chart');
            var tooltip = document.getElementById('donut-chart-tooltip');
            if (!donut || !tooltip) {
                return;
            }

            donut.addEventListener('mousemove', function (event) {
                var rect = donut.parentElement.getBoundingClientRect();
                var x = event.clientX - rect.left + 15;
                var y = event.clientY - rect.top - 85;

                var approved = donut.getAttribute('data-approved');
                var approvedPct = donut.getAttribute('data-approved-pct');
                var pending = donut.getAttribute('data-pending');
                var pendingPct = donut.getAttribute('data-pending-pct');
                var rejected = donut.getAttribute('data-rejected');
                var rejectedPct = donut.getAttribute('data-rejected-pct');

                tooltip.innerHTML = '<div style="font-weight:700;margin-bottom:6px;border-bottom:1px solid rgba(255,255,255,0.15);padding-bottom:4px;font-size:11px;text-transform:uppercase;letter-spacing:0.5px;">Detail Persetujuan</div>' +
                    '<div style="color:#10b981;display:flex;justify-content:space-between;gap:16px;margin-bottom:2px;"><span>Disetujui:</span><strong>' + approved + ' (' + approvedPct + '%)</strong></div>' +
                    '<div style="color:#f59e0b;display:flex;justify-content:space-between;gap:16px;margin-bottom:2px;"><span>Menunggu:</span><strong>' + pending + ' (' + pendingPct + '%)</strong></div>' +
                    '<div style="color:#f43f5e;display:flex;justify-content:space-between;gap:16px;"><span>Ditolak:</span><strong>' + rejected + ' (' + rejectedPct + '%)</strong></div>';

                tooltip.style.left = x + 'px';
                tooltip.style.top = y + 'px';
                tooltip.style.opacity = '1';
                tooltip.style.transform = 'translateY(0)';
                tooltip.classList.add('visible');
            });

            donut.addEventListener('mouseleave', function () {
                tooltip.style.opacity = '0';
                tooltip.style.transform = 'translateY(-4px)';
                tooltip.classList.remove('visible');
            });
        })();
    </script>


@endpush