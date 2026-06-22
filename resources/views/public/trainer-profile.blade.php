<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Trainer - {{ $trainer->full_name_with_title ?: $trainer->name }}</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --brand-navy: #2e2050;
            --brand-purple: #51376c;
            --brand-gold: #fcc12d;
            --brand-gold-hover: #e0a50b;
            --muted-text: #64748b;
            --bg-soft: #fcfdfe;
            --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 20px 40px -15px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-soft);
            color: #1e293b;
            margin: 0;
            overflow-x: hidden;
        }

        .container-custom {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Hero Section */
        .hero-section {
            padding: 96px 0 76px;
            background: radial-gradient(circle at 10% 10%, var(--brand-purple) 0%, var(--brand-navy) 100%);
            color: #ffffff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
        }

        .hero-flex {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 40px;
        }

        .hero-content {
            flex: 1;
            max-width: 820px;
            min-width: 0;
        }

        .badge-group {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .badge-item {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 6px 14px;
            border-radius: 8px;
        }

        .badge-expert {
            background-color: rgba(251, 189, 35, 0.15);
            color: #fcc12d;
            border: 1px solid rgba(251, 189, 35, 0.3);
        }

        .badge-verified {
            background-color: rgba(138, 98, 171, 0.15);
            color: #c084fc;
            border: 1px solid rgba(138, 98, 171, 0.3);
        }

        .trainer-name {
            font-size: 48px;
            font-weight: 800;
            color: #ffffff;
            margin: 0 0 10px;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .trainer-role {
            font-size: 20px;
            color: #cbd5e1;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .hero-meta {
            display: flex;
            gap: 32px;
            margin-bottom: 0;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .meta-item i {
            color: var(--brand-gold);
            font-size: 16px;
        }

        .hero-img-box {
            flex-shrink: 0;
            position: relative;
            width: 250px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
        }

        .hero-img-ring-outer {
            position: absolute;
            width: 240px;
            height: 240px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-img-ring-middle {
            width: 210px;
            height: 210px;
            border: 1.5px dashed rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-img-ring-inner {
            width: 180px;
            height: 180px;
            border: 4px solid #8a62ab;
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(138, 98, 171, 0.3);
            background: var(--brand-navy);
        }

        .trainer-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-image-btn {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(8px);
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .hero-image-btn:hover {
            background: var(--brand-navy);
            border-color: #8a62ab;
            transform: translateX(-50%) translateY(-2px);
        }

        /* Metrics */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: -38px;
            margin-bottom: 60px;
            position: relative;
            z-index: 10;
        }

        .metric-card {
            background: #ffffff;
            padding: 28px 24px;
            border-radius: 22px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .metric-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px -10px rgba(0, 0, 0, 0.1);
            border-color: rgba(138, 98, 171, 0.2);
        }

        .metric-top {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }

        .metric-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
        }

        .metric-blue {
            color: #8a62ab;
            background: rgba(138, 98, 171, 0.1);
        }

        .metric-gold {
            color: #f59e0b;
            background: rgba(245, 158, 11, 0.1);
        }

        .metric-green {
            color: #10b981;
            background: rgba(16, 185, 129, 0.1);
        }

        .metric-purple {
            color: #8b5cf6;
            background: rgba(139, 92, 246, 0.1);
        }

        .metric-num {
            display: block;
            font-size: 28px;
            font-weight: 800;
            color: var(--brand-navy);
            line-height: 1;
        }

        .metric-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.5px;
        }

        /* Bio & Sidebar */
        .content-wrap {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 60px;
            margin-bottom: 72px;
        }

        .bio-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--brand-navy);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .bio-title::after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 0;
            width: 40px;
            height: 4px;
            background: var(--brand-gold);
            border-radius: 2px;
        }

        .bio-text {
            font-size: 16px;
            line-height: 1.8;
            color: #475569;
            margin-bottom: 40px;
        }

        .philosophy-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .phil-card {
            background-color: #fffbeb;
            padding: 28px;
            border-radius: 20px;
            border: 1px solid rgba(245, 158, 11, 0.15);
            transition: all 0.3s ease;
        }

        .phil-card:hover {
            background: #fffdf5;
            box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.08);
            border-color: rgba(245, 158, 11, 0.3);
        }

        .phil-header {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: #f59e0b;
            letter-spacing: 1px;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .phil-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.6;
            margin: 0;
        }

        .sidebar-box {
            background: #f8fafc;
            border: 1px solid rgba(0, 0, 0, 0.03);
            border-radius: 24px;
            padding: 32px 28px;
        }

        .side-label {
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--brand-navy);
            letter-spacing: 1px;
            margin-bottom: 16px;
        }

        .tag-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 32px;
        }

        .tag-pill {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.06);
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            transition: all 0.2s ease;
        }

        .tag-pill:hover {
            background: var(--brand-navy);
            color: white;
            border-color: var(--brand-navy);
        }

        .social-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .social-item {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.06);
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .social-item:hover {
            background: var(--brand-navy);
            color: white;
            border-color: var(--brand-navy);
        }

        /* Track Record Section */
        .track-section {
            margin-bottom: 88px;
        }

        .track-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .track-title h2 {
            font-size: 28px;
            font-weight: 800;
            color: var(--brand-navy);
            margin: 0;
            text-transform: uppercase;
            line-height: 1;
            letter-spacing: -0.01em;
        }

        .track-title p {
            color: #94a3b8;
            font-size: 14px;
            margin: 12px 0 0;
            font-weight: 500;
        }

        .tabs-container {
            background: #f1f5f9;
            padding: 4px;
            border-radius: 14px;
            display: flex;
            gap: 4px;
        }

        .tab-link {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .tab-link i {
            font-size: 16px;
        }

        .tab-link:hover {
            color: var(--brand-navy);
        }

        .tab-link.active {
            background: var(--brand-navy);
            color: white;
            box-shadow: 0 4px 12px rgba(46, 32, 80, 0.15);
        }

        .tab-link.active i {
            color: white;
        }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        .course-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .course-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.1);
            border-color: rgba(138, 98, 171, 0.15);
        }

        .course-img-box {
            height: 180px;
            position: relative;
            background: #f1f5f9;
        }

        .course-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .course-lvl-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: #fffbeb;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 800;
            color: #d97706;
            text-transform: uppercase;
            border: 1px solid rgba(217, 119, 6, 0.2);
        }

        .course-body {
            padding: 24px;
        }

        .course-meta-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .course-meta-row span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .course-meta-row span i {
            color: #f59e0b;
        }

        .course-main-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--brand-navy);
            margin-bottom: 16px;
            line-height: 1.3;
            height: 42px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .course-btn-details {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            background: #f8fafc;
            border: 1px solid rgba(0, 0, 0, 0.02);
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            color: var(--brand-navy);
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            transition: all 0.2s ease;
        }

        .course-btn-details:hover {
            background: var(--brand-navy);
            color: white;
            border-color: var(--brand-navy);
        }

        /* Timeline Experience */
        .timeline-wrap {
            position: relative;
            padding-left: 20px;
        }

        .timeline-wrap::before {
            content: '';
            position: absolute;
            left: 6px;
            top: 12px;
            bottom: 12px;
            width: 1px;
            background: #e2e8f0;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 40px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -19px;
            top: 6px;
            width: 11px;
            height: 11px;
            background: var(--brand-gold);
            border-radius: 50%;
            box-shadow: 0 0 0 6px white;
        }

        .tm-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .tm-role {
            font-size: 18px;
            font-weight: 800;
            color: var(--brand-navy);
            margin: 0;
        }

        .tm-period {
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
        }

        .tm-brand {
            color: #d97706;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .tm-info {
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
            max-width: 700px;
            margin: 0;
        }

        /* Credentials */
        .cred-card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            transition: all 0.3s ease;
        }

        .cred-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
            border-color: rgba(138, 98, 171, 0.15);
        }

        .cred-icon {
            width: 48px;
            height: 48px;
            margin-bottom: 20px;
        }

        .cred-title {
            font-size: 16px;
            font-weight: 800;
            color: var(--brand-navy);
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .cred-meta {
            font-size: 12px;
            font-weight: 700;
            color: #94a3b8;
            margin: 0;
            text-transform: uppercase;
        }

        /* Student Feedback Section */
        .feedback-container {
            background: radial-gradient(circle at 10% 10%, var(--brand-navy) 0%, #150e24 100%);
            border-radius: 32px;
            padding: 60px 48px;
            position: relative;
            overflow: hidden;
            margin-bottom: 84px;
        }

        .feedback-container::after {
            content: '”';
            position: absolute;
            right: 40px;
            bottom: -60px;
            font-size: 300px;
            color: rgba(255, 255, 255, 0.02);
            font-family: serif;
            line-height: 1;
        }

        .feed-head h2 {
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: -0.01em;
        }

        .feed-head p {
            color: #94a3b8;
            font-size: 15px;
            margin-bottom: 40px;
            font-weight: 500;
        }

        .f-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .f-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            padding: 28px;
            border-radius: 20px;
            backdrop-filter: blur(12px);
            transition: all 0.3s ease;
        }

        .f-card:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.12);
            transform: translateY(-4px);
        }

        .f-stars {
            color: #fcc12d;
            font-size: 14px;
            margin-bottom: 16px;
            display: flex;
            gap: 4px;
        }

        .f-text {
            font-size: 15px;
            color: #e2e8f0;
            font-style: italic;
            line-height: 1.7;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .f-author {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .f-img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        .f-name {
            color: white;
            font-weight: 800;
            font-size: 14px;
            display: block;
        }

        .f-role {
            color: #fcc12d;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 1024px) {
            .hero-flex {
                flex-direction: column;
                text-align: center;
                gap: 32px;
            }

            .hero-img-box {
                margin: 0 auto;
            }

            .trainer-name {
                font-size: 40px;
            }

            .track-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 24px;
            }

            .course-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .feedback-container {
                padding: 44px 24px;
            }

            .f-grid {
                grid-template-columns: 1fr;
            }

            .feedback-decor {
                display: none;
            }

            .badge-group {
                justify-content: center;
            }

            .hero-meta {
                justify-content: center;
                gap: 16px;
            }

            .back-btn-container {
                position: absolute !important;
                top: 90px !important;
                left: 16px !important;
            }
        }

        @media (max-width: 768px) {
            .container-custom {
                padding: 0 16px;
            }

            .hero-section {
                padding: 64px 0 54px;
            }

            .trainer-name {
                font-size: 32px;
            }

            .trainer-role {
                font-size: 16px;
            }

            .metrics-grid {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
                margin-top: -30px;
            }

            .metric-card {
                padding: 20px;
                border-radius: 16px;
            }

            .metric-num {
                font-size: 24px;
            }

            .content-wrap {
                grid-template-columns: 1fr;
                gap: 40px;
                margin-bottom: 52px;
            }

            .track-title h2 {
                font-size: 24px;
            }

            .course-grid {
                grid-template-columns: 1fr;
            }

            .course-body {
                padding: 20px;
            }

            .course-main-title {
                font-size: 16px;
            }

            .feed-head h2 {
                font-size: 26px;
            }

            .feed-head p {
                font-size: 14px;
                margin-bottom: 24px;
            }

            .f-card {
                padding: 20px;
                border-radius: 16px;
            }

            .f-text {
                font-size: 14px;
                margin-bottom: 16px;
            }

            .f-img {
                width: 44px;
                height: 44px;
            }

            .trainer-img {
                width: 126px;
                height: 162px;
            }

            .track-stat-strip {
                grid-template-columns: 1fr 1fr !important;
                gap: 16px !important;
            }

            .philosophy-cards {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }
        @media (max-width: 576px) {
            .metrics-grid {
                grid-template-columns: 1fr !important;
                gap: 12px;
                margin-top: -20px;
            }
            .tabs-container {
                width: 100%;
                overflow-x: auto;
                white-space: nowrap;
                justify-content: flex-start;
                -webkit-overflow-scrolling: touch;
            }
            .tab-link {
                flex: 1;
                justify-content: center;
            }
            .feed-head {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 16px;
            }
            .feed-head > div:last-child {
                text-align: left !important;
            }
            .feed-head > div:last-child > div {
                justify-content: flex-start !important;
            }
        }

        @media (max-width: 480px) {
            .track-stat-strip {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
                padding: 16px !important;
            }
        }
    </style>
</head>

<body>
    @include('partials.navbar-after-login')

    <div style="position:fixed; top:100px; left:30px; z-index:999;" class="back-btn-container">
        <a href="javascript:history.back()" style="display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; background:#fff; border-radius:50%; box-shadow:0 2px 8px rgba(0,0,0,0.15); color:#1f2937; text-decoration:none; transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.2)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.15)'">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
            </svg>
        </a>
    </div>

    <section class="hero-section">
        <div class="container-custom">
            <div class="hero-flex">
                <div class="hero-content">
                    <div class="badge-group">
                        <span class="badge-item badge-expert">Expert Instructor</span>
                        <span class="badge-item badge-verified">Verified Professional</span>
                    </div>
                    <h4 class="trainer-name">{{ $trainer->full_name_with_title ?: $trainer->name }}</h4>
                    <p class="trainer-role">{{ $trainer->profession ?: 'Trainer' }}</p>

                    <div class="hero-meta">
                        <div class="meta-item"><i class="bi bi-geo-alt-fill"></i>
                            {{ strtoupper($trainer->institution ?: 'Lokasi tidak diisi') }}</div>
                        <div class="meta-item"><i class="bi bi-star-fill"></i>
                            {{ isset($reputation['rating']) ? number_format($reputation['rating'], 1) : '0.0' }} RATING
                        </div>
                        <div class="meta-item"><i class="bi bi-people-fill"></i>
                            {{ isset($reputation['students']) ? number_format($reputation['students']) : '0' }} STUDENTS
                        </div>
                    </div>

                    <p class="hero-tagline" style="margin-top: 24px; font-size: 15px; font-style: italic; color: #94a3b8; font-weight: 500; margin-bottom: 0;">
                        "Berbagi ilmu, membimbing dengan pengalaman, dan menginspirasi untuk masa depan yang lebih baik."
                    </p>
                </div>

                <div class="hero-img-box">
                    <div class="hero-img-ring-outer">
                        <div class="hero-img-ring-middle">
                            <div class="hero-img-ring-inner">
                                <img src="{{ $trainer->avatar_url }}" alt="{{ $trainer->name }}" class="trainer-img">
                            </div>
                        </div>
                    </div>
                    <a href="javascript:void(0)" class="hero-image-btn"><i class="bi bi-person-fill"></i> Trainer Profile</a>
                </div>
            </div>
        </div>
    </section>

    <div class="container-custom">
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-top" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div class="metric-icon" style="width: 40px; height: 40px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"><i class="bi bi-briefcase-fill"></i></div>
                    <span class="metric-label" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Years Experience</span>
                </div>
                <span class="metric-num" style="display: block; font-size: 32px; font-weight: 800; color: var(--brand-navy); line-height: 1.1; margin-bottom: 4px;">{{ $reputation['experience_years'] }}+</span>
                <span style="font-size: 13px; color: #64748b; font-weight: 500;">Tahun Mengajar</span>
            </div>
            <div class="metric-card">
                <div class="metric-top" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div class="metric-icon" style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"><i class="bi bi-calendar3"></i></div>
                    <span class="metric-label" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Courses & Events</span>
                </div>
                <span class="metric-num" style="display: block; font-size: 32px; font-weight: 800; color: var(--brand-navy); line-height: 1.1; margin-bottom: 4px;">{{ $activeCourses->count() + $activeEvents->count() }}</span>
                <span style="font-size: 13px; color: #64748b; font-weight: 500;">Dibuat</span>
            </div>
            <div class="metric-card">
                <div class="metric-top" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div class="metric-icon" style="width: 40px; height: 40px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"><i class="bi bi-bullseye"></i></div>
                    <span class="metric-label" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Success Rate</span>
                </div>
                <span class="metric-num" style="display: block; font-size: 32px; font-weight: 800; color: var(--brand-navy); line-height: 1.1; margin-bottom: 4px;">{{ $reputation['success_rate'] }}%</span>
                <span style="font-size: 13px; color: #64748b; font-weight: 500;">Tingkat Keberhasilan</span>
            </div>
            <div class="metric-card">
                <div class="metric-top" style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
                    <div class="metric-icon" style="width: 40px; height: 40px; border-radius: 12px; background: rgba(138, 98, 171, 0.1); color: #8a62ab; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;"><i class="bi bi-people-fill"></i></div>
                    <span class="metric-label" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Active Learners</span>
                </div>
                <span class="metric-num" style="display: block; font-size: 32px; font-weight: 800; color: var(--brand-navy); line-height: 1.1; margin-bottom: 4px;">
                    @if($reputation['active_learners'] >= 1000)
                        {{ number_format($reputation['active_learners'] / 1000, 1) }}k
                    @else
                        {{ $reputation['active_learners'] }}
                    @endif
                </span>
                <span style="font-size: 13px; color: #64748b; font-weight: 500;">Total Mahasiswa</span>
            </div>
        </div>

        <div class="content-wrap">
            <main>
                <h2 class="bio-title"><i class="bi bi-person-fill"></i> Professional Biography</h2>
                <div class="bio-text">
                    {{ $trainer->bio ?: 'Profil belum dilengkapi. Tambahkan bio agar peserta mengenal Anda lebih baik.' }}
                </div>

                <div class="philosophy-cards">
                    <div class="phil-card">
                        <div class="phil-header" style="color: #f59e0b;"><i class="bi bi-lightning-fill"></i> Teaching Philosophy</div>
                        <p class="phil-text">{{ $philosophy ?: '-' }}</p>
                    </div>
                    <div class="phil-card">
                        <div class="phil-header" style="color: #f59e0b;"><i class="bi bi-heart-fill"></i> Learning Outcomes</div>
                        <p class="phil-text">{{ $outcomes ?: '-' }}</p>
                    </div>
                </div>
            </main>

            <aside>
                <div class="sidebar-box">
                    <div class="side-label">Core Expertise</div>
                    <div class="tag-list">
                        @foreach($expertise as $tag)
                            <span class="tag-pill">{{ strtoupper($tag) }}</span>
                        @endforeach
                    </div>

                    <div class="side-label">Digital Presence</div>
                    <div class="social-grid">
                        <a href="{{ $trainer->linkedin_url ?: '#' }}" class="social-item" {{ $trainer->linkedin_url ? 'target=_blank rel=noopener noreferrer' : 'aria-disabled=true style=opacity:.55;pointer-events:none;' }}><i class="bi bi-linkedin"></i> LinkedIn</a>
                        <a href="{{ $trainer->website ?: '#' }}" class="social-item" {{ $trainer->website ? 'target=_blank rel=noopener noreferrer' : 'aria-disabled=true style=opacity:.55;pointer-events:none;' }}><i class="bi bi-globe"></i> Website</a>
                    </div>
                </div>
            </aside>
        </div>

        <section class="track-section" id="courses">
            <div class="track-header">
                <div class="track-title">
                    <h2>TRACK RECORD</h2>
                    <p>Verified courses, professional experience, and academic credentials.</p>
                </div>
                <div class="tabs-container">
                    <a href="javascript:void(0)" class="tab-link active" onclick="goTab(this, 'c')"><i
                            class="bi bi-book-fill"></i> Courses</a>
                    <a href="javascript:void(0)" class="tab-link" onclick="goTab(this, 'e')"><i
                            class="bi bi-briefcase-fill"></i> Experience</a>
                    <a href="javascript:void(0)" class="tab-link" onclick="goTab(this, 'cr')"><i
                            class="bi bi-patch-check-fill"></i> Credentials</a>
                </div>
            </div>

            <div id="t-c" class="t-panel">
                <!-- Stat strip as in the photo -->
                <div class="track-stat-strip" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; background: #f8fafc; border: 1px solid #f1f5f9; padding: 24px; border-radius: 20px; margin-bottom: 32px;">
                    <div class="track-stat-item" style="display: flex; align-items: center; gap: 16px;">
                        <div class="track-stat-icon" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6; display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="bi bi-book-half"></i></div>
                        <div>
                            <span class="track-stat-num" style="display: block; font-size: 24px; font-weight: 800; color: var(--brand-navy); line-height: 1.1;">{{ $activeCourses->count() }}</span>
                            <span class="track-stat-label" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.3px;">Courses Created</span>
                        </div>
                    </div>
                    <div class="track-stat-item" style="display: flex; align-items: center; gap: 16px;">
                        <div class="track-stat-icon" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="bi bi-calendar3"></i></div>
                        <div>
                            <span class="track-stat-num" style="display: block; font-size: 24px; font-weight: 800; color: var(--brand-navy); line-height: 1.1;">{{ $activeEvents->count() }}</span>
                            <span class="track-stat-label" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.3px;">Events Hosted</span>
                        </div>
                    </div>
                    <div class="track-stat-item" style="display: flex; align-items: center; gap: 16px;">
                        <div class="track-stat-icon" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <span class="track-stat-num" style="display: block; font-size: 24px; font-weight: 800; color: var(--brand-navy); line-height: 1.1;">{{ $reputation['students'] }}</span>
                            <span class="track-stat-label" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.3px;">Students Taught</span>
                        </div>
                    </div>
                    <div class="track-stat-item" style="display: flex; align-items: center; gap: 16px;">
                        <div class="track-stat-icon" style="width: 44px; height: 44px; border-radius: 12px; background: rgba(138, 98, 171, 0.1); color: #8a62ab; display: flex; align-items: center; justify-content: center; font-size: 20px;"><i class="bi bi-patch-check-fill"></i></div>
                        <div>
                            <span class="track-stat-num" style="display: block; font-size: 24px; font-weight: 800; color: var(--brand-navy); line-height: 1.1;">{{ $certificates->count() }}</span>
                            <span class="track-stat-label" style="font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.3px;">Certificates Issued</span>
                        </div>
                    </div>
                </div>

                <div class="course-grid">
                    @forelse($activeCourses as $course)
                        <div class="course-card">
                            <div class="course-img-box">
                                <img src="{{ $course->thumbnail_url ?: 'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?q=80&w=800' }}"
                                    class="course-img">
                                <span class="course-lvl-badge">{{ $course->level ?? 'Intermediate' }}</span>
                            </div>
                            <div class="course-body">
                                <div class="course-meta-row">
                                    <span><i class="bi bi-clock"></i> {{ $course->modules_count ?? '-' }} Modules</span>
                                    <span><i class="bi bi-star-fill"></i> {{ $course->rating ?? '-' }}</span>
                                </div>
                                <h3 class="course-main-title">{{ $course->name }}</h3>
                                <a href="{{ route('course.detail', $course->id) }}" class="course-btn-details">Course
                                    Details <i class="bi bi-arrow-right ms-2"></i></a>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>

            <div id="t-e" class="t-panel" style="display:none;">
                <div class="timeline-wrap">
                    @forelse($experiences as $exp)
                        <div class="timeline-item">
                            <div class="tm-header-row">
                                <h4 class="tm-role">{{ $exp->role }}</h4>
                                <span class="tm-period">{{ $exp->period }}</span>
                            </div>
                            <div class="tm-brand">{{ $exp->company }}</div>
                            <p class="tm-info">{{ $exp->description }}</p>
                        </div>
                    @empty
                        <div class="timeline-item">
                            <div class="tm-header-row">
                                <h4 class="tm-role">{{ $trainer->profession ?: 'Trainer' }}</h4>
                                <span class="tm-period">PRESENT</span>
                            </div>
                            <div class="tm-brand">{{ $trainer->institution ?: 'idSpora Trainer' }}</div>
                            <p class="tm-info">Aktif mengembangkan pengalaman belajar peserta dengan sesi training praktis.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div id="t-cr" class="t-panel" style="display:none;">
                <div class="course-grid">
                    @forelse($certificates as $certificate)
                        <div class="cred-card">
                            <img src="{{ $certificate->icon_url ?? 'https://cdn-icons-png.flaticon.com/512/2991/2991148.png' }}"
                                class="cred-icon">
                            <h4 class="cred-title">{{ $certificate->title }}</h4>
                            <p class="cred-meta">{{ $certificate->issuer }} • {{ $certificate->year }}</p>
                        </div>
                    @empty
                        <div class="cred-card">
                            <h4 class="cred-title">Belum ada sertifikat</h4>
                            <p class="cred-meta">Sertifikat akan muncul di sini setelah diverifikasi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="feedback-container" style="position: relative;">
            <div class="feed-head" style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; flex-wrap: wrap; gap: 24px; position: relative; z-index: 2;">
                <div>
                    <h2 style="font-size: 24px; font-weight: 800; color: white; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; gap: 12px;">
                        <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: center; color: #8b5cf6;">
                            <i class="bi bi-chat-left-quote-fill" style="font-size: 20px;"></i>
                        </div>
                        STUDENT FEEDBACK
                    </h2>
                    <p style="color: #94a3b8; font-size: 14px; margin: 0; font-weight: 500;">
                        Direct reviews from professionals who completed mentorship programs.
                    </p>
                </div>
                
                <div style="text-align: right; min-width: 200px;">
                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px; margin-bottom: 6px;">
                        <span style="color: #94a3b8; font-size: 13px; font-weight: 600;">Overall Rating</span>
                        <span style="color: #fcc12d; font-size: 24px; font-weight: 800;">{{ isset($reputation['rating']) ? number_format($reputation['rating'], 1) : '0.0' }}</span>
                        <div style="display: flex; gap: 2px;">
                            @php
                                $rating = $reputation['rating'] ?? 0.0;
                                $fullStars = floor($rating);
                                $halfStar = ($rating - $fullStars) >= 0.5 ? 1 : 0;
                                $emptyStars = 5 - $fullStars - $halfStar;
                            @endphp
                            @for($i = 0; $i < $fullStars; $i++)
                                <i class="bi bi-star-fill" style="color: #fcc12d; font-size: 14px;"></i>
                            @endfor
                            @if($halfStar)
                                <i class="bi bi-star-half" style="color: #fcc12d; font-size: 14px;"></i>
                            @endif
                            @for($i = 0; $i < $emptyStars; $i++)
                                <i class="bi bi-star" style="color: #475569; font-size: 14px;"></i>
                            @endfor
                        </div>
                    </div>
                    <p style="color: #94a3b8; font-size: 12px; margin: 0; font-weight: 500;">
                        Based on {{ $reputation['rating_count'] }} reviews
                    </p>
                </div>
            </div>

            <div class="f-grid" style="position: relative; z-index: 2;">
                @forelse($feedbacks as $feedback)
                    <div class="f-card">
                        <div style="display: flex; gap: 20px; align-items: flex-start;">
                            <!-- Left: Avatar or Initials -->
                            @if($feedback->user_avatar_url)
                                <img src="{{ $feedback->user_avatar_url }}" class="f-avatar-circle" style="width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.1); flex-shrink: 0;">
                            @else
                                @php
                                    $words = explode(' ', $feedback->user_name);
                                    $initials = '';
                                    if (count($words) >= 2) {
                                        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                    } else {
                                        $initials = strtoupper(substr($feedback->user_name, 0, 2));
                                    }
                                    
                                    $bgColors = ['#51376c', '#7c3aed', '#8a62ab', '#6d4d8c', '#a21caf'];
                                    $colorIndex = abs(crc32($feedback->user_name)) % count($bgColors);
                                    $bgColor = $bgColors[$colorIndex];
                                @endphp
                                <div class="f-initials" style="width: 48px; height: 48px; border-radius: 50%; background-color: {{ $bgColor }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; border: 2px solid rgba(255,255,255,0.1);">
                                    {{ $initials }}
                                </div>
                            @endif

                            <!-- Right: Details -->
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                    <div>
                                        <span class="f-name" style="color: white; font-weight: 700; font-size: 15px; display: block; margin-bottom: 2px;">{{ $feedback->user_name }}</span>
                                        <span class="f-role" style="font-size: 10px; font-weight: 800; color: #fcc12d; text-transform: uppercase; letter-spacing: 0.5px;">{{ $feedback->user_role }}</span>
                                    </div>
                                    <div class="f-stars" style="color: #fcc12d; font-size: 13px; display: flex; gap: 3px;">
                                        @for($i = 0; $i < 5; $i++)
                                            <i class="bi {{ $i < $feedback->rating ? 'bi-star-fill' : 'bi-star' }}" style="color: {{ $i < $feedback->rating ? '#fcc12d' : '#475569' }};"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="f-text" style="font-size: 14px; color: #cbd5e1; line-height: 1.6; margin: 0 0 16px 0; font-style: normal; font-weight: 400;">
                                    {{ $feedback->comment }}
                                </p>
                                <div style="display: flex; align-items: center; gap: 8px; color: #94a3b8; font-size: 12px; font-weight: 500;">
                                    <i class="bi bi-calendar3"></i>
                                    <span>{{ $feedback->created_at ? $feedback->created_at->locale('id')->diffForHumans() : 'Beberapa waktu lalu' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="f-card" style="grid-column: span 2;">
                        <p class="f-text" style="margin: 0; text-align: center; color: #94a3b8;">Belum ada feedback dari peserta.</p>
                    </div>
                @endforelse
            </div>

            <!-- Speech bubble decoration in bottom right corner -->
            <div class="feedback-decor" style="position: absolute; right: 40px; bottom: 10px; width: 300px; height: 180px; pointer-events: none; opacity: 0.85; z-index: 1;">
                <!-- Speech bubble 1 (Plum light gradient) -->
                <div style="position: absolute; bottom: 30px; left: 10px; width: 100px; height: 60px; background: linear-gradient(135deg, #51376c, #8a62ab); border-radius: 16px; display: flex; align-items: center; justify-content: center; gap: 6px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-bottom-left-radius: 2px;">
                    <span style="width: 6px; height: 6px; background: white; border-radius: 50%; opacity: 0.5;"></span>
                    <span style="width: 6px; height: 6px; background: white; border-radius: 50%; opacity: 0.8;"></span>
                    <span style="width: 6px; height: 6px; background: white; border-radius: 50%; opacity: 0.5;"></span>
                </div>
                <!-- Speech bubble 2 (Dark plum gradient) -->
                <div style="position: absolute; bottom: 70px; right: 70px; width: 80px; height: 50px; background: linear-gradient(135deg, #2e2050, #6d4d8c); border-radius: 14px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-bottom-left-radius: 2px;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.7;">
                        <path d="M20 2H4C2.9 2 2 2.9 2 4V22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2Z" fill="white"/>
                    </svg>
                </div>
                <!-- Stars card (Plum theme) -->
                <div style="position: absolute; bottom: 20px; right: 20px; width: 140px; height: 40px; background: linear-gradient(135deg, #8a62ab, #51376c); border-radius: 10px; display: flex; align-items: center; justify-content: center; gap: 4px; box-shadow: 0 10px 20px rgba(0,0,0,0.3); transform: rotate(-5deg);">
                    <i class="bi bi-star-fill" style="color: white; font-size: 12px;"></i>
                    <i class="bi bi-star-fill" style="color: white; font-size: 12px;"></i>
                    <i class="bi bi-star-fill" style="color: white; font-size: 12px;"></i>
                    <i class="bi bi-star-fill" style="color: white; font-size: 12px;"></i>
                    <i class="bi bi-star-fill" style="color: white; font-size: 12px;"></i>
                </div>
            </div>
        </section>
    </div>

    @include('partials.footer-after-login')

    <script>
        function goTab(el, id) {
            document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
            el.classList.add('active');

            document.querySelectorAll('.t-panel').forEach(p => p.style.display = 'none');
            document.getElementById('t-' + id).style.display = 'block';
        }
    </script>
</body>

</html>