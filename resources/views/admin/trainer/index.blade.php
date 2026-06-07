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

        /* Header Section */
        .header-section {
            background: linear-gradient(135deg, var(--dash-primary) 0%, var(--dash-purple) 100%);
            border: none;
            border-radius: 24px;
            padding: 32px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 40px -15px rgba(79, 70, 229, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .header-section::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .welcome-greeting {
            font-size: 30px;
            font-weight: 800;
            color: white;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            letter-spacing: -0.5px;
        }
        
        .welcome-subtitle {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.85);
            margin: 0;
            font-weight: 500;
        }

        .date-widget {
            min-width: 220px;
            padding: 12px 18px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: white;
            z-index: 2;
        }

        .date-widget-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .date-main {
            font-weight: 700;
            font-size: 14px;
            line-height: 1.2;
        }

        .date-sub {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 2px;
        }

        /* Metric Grid */
        .metric-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .metric-card {
            background: #ffffff;
            border: 1px solid var(--dash-border);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--dash-card-shadow);
            display: flex;
            align-items: center;
            gap: 18px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.05), 0 10px 10px -5px rgba(15, 23, 42, 0.02);
            border-color: rgba(79, 70, 229, 0.2);
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
            transition: transform 0.3s ease;
        }
        
        .metric-card:hover .metric-icon {
            transform: scale(1.05);
        }

        .metric-icon.blue { background: #e0e7ff; color: var(--dash-primary); }
        .metric-icon.green { background: #d1fae5; color: var(--dash-green); }
        .metric-icon.orange { background: #fef3c7; color: var(--dash-orange); }
        .metric-icon.purple { background: #f3e8ff; color: var(--dash-purple); }

        .metric-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            flex: 1;
        }

        .metric-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--dash-muted);
            margin: 0;
        }

        .metric-value {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.1;
            color: var(--dash-navy);
            letter-spacing: -0.5px;
        }

        .metric-change {
            font-size: 11px;
            font-weight: 700;
            margin-top: 2px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .metric-change.up { color: var(--dash-green); }
        .metric-change.down { color: var(--dash-red); }

        /* Work Queue Section */
        .queue-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

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

        .queue-item-card {
            border: 1px solid var(--dash-border);
            border-radius: 16px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.01);
        }

        .queue-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.04);
            border-color: rgba(79, 70, 229, 0.2);
        }

        .queue-item-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1;
        }

        .queue-icon-wrapper {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .queue-icon-wrapper.course { background: #e0e7ff; color: var(--dash-primary); }
        .queue-icon-wrapper.event { background: #e0f2fe; color: #0284c7; }
        .queue-icon-wrapper.publish { background: #fef3c7; color: var(--dash-orange); }

        .queue-item-details {
            min-width: 0;
            flex: 1;
        }

        .queue-item-title {
            font-weight: 700;
            font-size: 14px;
            color: var(--dash-navy);
            line-height: 1.4;
            margin-bottom: 2px;
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
        
        .queue-item-meta strong {
            color: #475569;
        }

        .meta-dot {
            color: #cbd5e1;
        }

        .btn-queue-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 700;
            border-radius: 10px;
            padding: 8px 16px;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            border: 0;
        }

        .btn-queue-action.course { background: #f1f5f9; color: #334155; }
        .btn-queue-action.course:hover { background: var(--dash-primary); color: #fff; }
        
        .btn-queue-action.event { background: #f1f5f9; color: #334155; }
        .btn-queue-action.event:hover { background: #0284c7; color: #fff; }

        .btn-queue-action.publish { background: #fef3c7; color: #b45309; }
        .btn-queue-action.publish:hover { background: var(--dash-orange); color: #fff; }

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

        /* Layout Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr 1fr;
            gap: 24px;
        }

        .bottom-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        /* Donut Charts */
        .approval-wrap, .category-wrap {
            display: flex;
            align-items: center;
            gap: 24px;
            justify-content: center;
            flex: 1;
        }

        .approval-donut, .category-donut {
            width: 170px;
            height: 170px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .approval-donut-inner, .category-inner {
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

        .donut-number {
            font-size: 24px;
            font-weight: 800;
            color: var(--dash-navy);
            line-height: 1;
        }

        .donut-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--dash-muted);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .approval-list, .category-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            flex: 1;
        }

        .approval-item, .category-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 12px;
            color: var(--dash-muted);
            line-height: 1.4;
        }

        .approval-item strong, .category-item strong {
            color: var(--dash-navy);
            font-weight: 700;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
            display: inline-block;
        }

        /* Deadlines */
        .deadline-list, .trainer-list, .feedback-list {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .deadline-item {
            padding: 14px;
            border: 1px solid var(--dash-border);
            border-radius: 16px;
            background: #fff;
            display: grid;
            grid-template-columns: 40px 1fr auto;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }
        
        .deadline-item:hover {
            transform: translateY(-2px);
            border-color: rgba(79, 70, 229, 0.15);
            box-shadow: 0 8px 16px rgba(15, 23, 42, 0.03);
        }

        .deadline-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .deadline-icon.green { background: #e6fdf0; color: var(--dash-green); }
        .deadline-icon.orange { background: #fff7ed; color: var(--dash-orange); }
        .deadline-icon.blue { background: #e0f2fe; color: #0284c7; }

        .deadline-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--dash-navy);
            margin-bottom: 2px;
            line-height: 1.3;
        }

        .deadline-sub {
            font-size: 11px;
            color: var(--dash-muted);
        }

        .deadline-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .deadline-badge.red { background: #fee2e2; color: #dc2626; }
        .deadline-badge.yellow { background: #fef3c7; color: #d97706; }
        .deadline-badge.blue { background: #e0e7ff; color: var(--dash-primary); }

        .deadline-date {
            font-size: 11px;
            font-weight: 600;
            color: var(--dash-muted);
        }

        /* Top Trainers & Feedback */
        .trainer-item {
            border: 1px solid var(--dash-border);
            border-radius: 16px;
            padding: 14px;
            display: grid;
            grid-template-columns: 28px 40px 1fr 100px;
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
        }

        .rank-icon.gold { background: #fef3c7; color: #d97706; }
        .rank-icon.silver { background: #f1f5f9; color: #475569; }
        .rank-icon.bronze { background: #ffedd5; color: #c2410c; }

        .trainer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .trainer-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--dash-navy);
            margin-bottom: 2px;
        }
        
        .trainer-name a:hover {
            color: var(--dash-primary) !important;
        }

        .trainer-meta {
            font-size: 11px;
            color: var(--dash-muted);
        }

        .score-area {
            text-align: right;
            font-size: 11px;
            color: var(--dash-muted);
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
            display: grid;
            grid-template-columns: 40px 1fr auto;
            gap: 12px;
            align-items: center;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--dash-border);
        }

        .feedback-item:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .feedback-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
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
            align-self: center;
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

        /* Trainer Table */
        .table {
            margin-bottom: 0;
        }
        
        .table thead th {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #475569;
            background-color: #f8fafc;
            border-bottom: 1px solid var(--dash-border);
            padding: 14px 16px;
        }
        
        .table tbody td {
            padding: 16px;
            border-bottom: 1px solid var(--dash-border);
            font-size: 13px;
        }
        
        .table tbody tr:last-child td {
            border-bottom: 0;
        }
        
        .table-hover tbody tr {
            transition: background-color 0.2s ease;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f8fafc;
        }
        
        .badge {
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
        }
        
        .badge.bg-success { background: #ecfdf5 !important; color: #059669; border: 1px solid #a7f3d0; }
        .badge.bg-warning { background: #fffbeb !important; color: #d97706; border: 1px solid #fde68a; }
        .badge.bg-danger { background: #fef2f2 !important; color: #dc2626; border: 1px solid #fca5a5; }

        /* Insight Card */
        .insight-card {
            background: linear-gradient(135deg, #eef2ff 0%, #faf5ff 100%);
            border: 1px solid #e0e7ff;
            border-radius: 20px;
            box-shadow: var(--dash-card-shadow);
            padding: 24px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .insight-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .insight-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: #dfe8ff;
            color: var(--dash-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.15);
            flex-shrink: 0;
        }

        .insight-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 4px;
        }

        .insight-text {
            margin: 0;
            color: #475569;
            font-size: 13px;
            line-height: 1.4;
        }

        .btn-report {
            height: 44px;
            padding: 0 24px;
            border: 0;
            border-radius: 12px;
            background: var(--dash-primary);
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-report:hover {
            color: #fff;
            background: var(--dash-purple);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(124, 58, 237, 0.25);
        }

        /* Responsive Layouts */
        @media (max-width: 1400px) {
            .metric-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .main-grid {
                grid-template-columns: 1.6fr 1fr;
            }
            
            .main-grid .dash-card:first-child {
                grid-column: 1 / -1;
            }

            .bottom-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 1200px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
                padding: 24px 30px;
            }

            .date-widget {
                width: 100%;
            }

            .main-grid, .bottom-grid {
                grid-template-columns: 1fr;
            }
            
            .main-grid .dash-card:first-child, .bottom-grid .dash-card:first-child {
                grid-column: span 1;
            }

            .approval-wrap, .category-wrap {
                flex-direction: column;
                align-items: center;
            }

            .approval-list, .category-list {
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

            .dash-card {
                padding: 20px;
            }

            .trainer-item {
                grid-template-columns: 24px 36px 1fr;
            }

            .score-area {
                grid-column: 3;
                text-align: left;
                margin-top: 6px;
            }

            .feedback-item {
                grid-template-columns: 36px 1fr;
            }

            .feedback-time {
                grid-column: 2;
                margin-top: 4px;
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
                    <i class="bi bi-shield-lock-fill"></i> Ruang Kerja Admin Trainer
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

                <div class="metric-info">
                    <div class="metric-label">Total Kursus</div>
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

                <div class="metric-info">
                    <div class="metric-label">Total Acara</div>
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

                <div class="metric-info">
                    <div class="metric-label">Menunggu Tinjauan</div>
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

                <div class="metric-info">
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
                                        <span>{{ $item['type'] === 'course' ? 'Kursus: ' : 'Acara: ' }}{{ $item['source'] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="queue-item-right">
                                <a href="{{ $item['url'] }}" class="btn-queue-action {{ $item['type'] }}">
                                    <span>Tinjau</span>
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
                    <h5 class="card-title-clean">Ringkasan Aktivitas</h5>

                    <select class="chart-select">
                        <option>7 Hari Terakhir</option>
                        <option>30 Hari Terakhir</option>
                    </select>
                </div>

                <div class="legend-row">
                    <span class="legend-item">
                        <span class="legend-dot" style="background:#2f5bff;"></span>
                        Kursus Dibuat
                    </span>

                    <span class="legend-item">
                        <span class="legend-dot" style="background:#19bd6b;"></span>
                        Acara Dibuat
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

            <div class="dash-card">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">Status Persetujuan</h5>
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
                            <div>Menunggu Tinjauan<br><strong>{{ $approvalStats['pending'] }}</strong>
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
                    Lihat semua persetujuan
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
                                    {{ $item['type'] === 'event' ? 'Acara:' : 'Materi:' }} {{ $item['title'] }}
                                </div>
                                <div class="deadline-sub">Trainer: {{ $item['trainer'] }}</div>
                            </div>

                            <div class="deadline-right text-end">
                                <span class="deadline-badge {{ $item['badge_class'] }} d-block mb-1">{{ $item['badge_text'] }}</span>
                                <div class="deadline-date mb-2">{{ $item['date_text'] }}</div>
                                @if(isset($item['trainer_id']))
                                    <a href="{{ route('admin.trainer.show', ['trainer' => $item['trainer_id'], 'tab' => 'deadline']) }}" 
                                       class="btn btn-sm btn-outline-primary" style="font-size: 11px; padding: 2px 8px; font-weight: 700;">
                                        <i class="bi bi-pencil-square me-1"></i>Atur
                                    </a>
                                @endif
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
                                    {{ $trainer->courses_as_trainer_count ?? 0 }} Kursus •
                                    {{ $trainer->events_as_trainer_count ?? 0 }} Acara
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

                <a href="{{ route('admin.trainer.index', ['view' => 'list']) }}#daftar-trainer" class="card-link mt-3">
                    Lihat semua trainer
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
        <h4 id="daftar-trainer" style="font-weight: 800; font-size: 18px; color: var(--dash-navy); margin: 36px 0 16px; letter-spacing: -.4px; display: flex; align-items: center; gap: 8px;">
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
                { name: 'Kursus', color: '#2f5bff', values: data.course || [] },
                { name: 'Acara', color: '#19bd6b', values: data.event || [] },
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