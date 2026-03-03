<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - idSpora</title>
  @vite(['resources/css/app.css'])
</head>
<style>
  .main-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    overflow-x: hidden;
    margin-left: 250px;
    margin-top: 70px;

    .dashboard-content {
      padding: var(--spacing-4xl);
      overflow-y: auto;
      max-width: 1200px;
      margin: 0 auto;
      width: 100%;
    }

    /* Dashboard Grid System */
    .dashboard-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: var(--spacing-2xl);
      align-items: start;
    }

    .grid-column {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-2xl);
    }

    /* Hero Section */
    .hero-card {
      background: linear-gradient(135deg,
          var(--main-navy-clr) 0%,
          var(--navy-dark) 100%);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-3xl);
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(27, 23, 99, 0.15);
      margin-bottom: var(--spacing-2xl);
      width: 100%;
    }

    .hero-decoration-1 {
      position: absolute;
      right: -80px;
      top: -80px;
      width: 350px;
      height: 350px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.04);
    }

    .hero-decoration-2 {
      position: absolute;
      right: 100px;
      bottom: -150px;
      width: 400px;
      height: 400px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.03);
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .hero-breadcrumb {
      font-size: var(--font-size-sm);
      color: rgba(255, 255, 255, 0.7);
      margin-bottom: var(--spacing-lg);
      letter-spacing: 1px;
      text-transform: uppercase;
      font-weight: 600;
    }

    .hero-breadcrumb-active {
      color: var(--yellow-clr);
    }

    .hero-heading {
      margin: 0 0 var(--spacing-md) 0;
      font-size: var(--font-size-4xl);
      font-weight: 800;
      line-height: var(--line-height-tight);
      color: white;
    }

    .hero-heading-name {
      color: var(--yellow-clr);
    }

    .hero-description {
      margin: 0 0 var(--spacing-3xl) 0;
      font-size: var(--font-size-lg);
      color: rgba(255, 255, 255, 0.85);
      line-height: var(--line-height-normal);
      max-width: 650px;
    }

    .hero-description strong {
      color: white;
      font-weight: 700;
    }

    .hero-buttons {
      display: flex;
      gap: var(--spacing-lg);
    }

    /* Buttons */
    .btn-primary,
    .btn-secondary {
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-md);
      padding: var(--spacing-md) var(--spacing-xl);
      border-radius: var(--radius-lg);
      text-decoration: none;
      font-weight: 700;
      font-size: var(--font-size-base);
      letter-spacing: 0.4px;
      transition: all 0.2s ease-in-out;
    }

    .btn-primary {
      background-color: var(--yellow-clr);
      color: var(--main-navy-clr);
      box-shadow: 0 8px 20px rgba(251, 197, 49, 0.3);
      border: 2px solid var(--yellow-clr);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 25px rgba(251, 197, 49, 0.4);
      background-color: #fff176;
      border-color: #fff176;
    }

    .btn-secondary {
      background-color: transparent;
      color: white;
      border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn-secondary:hover {
      background-color: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.8);
      transform: translateY(-3px);
    }

    /* Metrics Section */
    .metrics-section {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: var(--spacing-lg);
    }

    .metric-card {
      background: var(--white-clr);
      padding: var(--spacing-lg);
      border-radius: var(--radius-lg);
      border: 1px solid var(--line-clr);
      box-shadow: var(--shadow-sm);
      transition: transform 0.2s;
    }

    .metric-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
    }

    .metric-label {
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--text-clr);
      margin: 0 0 var(--spacing-sm) 0;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .metric-value-row {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
      flex-wrap: wrap;
    }

    .metric-value {
      font-size: var(--font-size-5xl);
      font-weight: 800;
      color: var(--main-text-clr);
      line-height: 1;
      margin: 0;
    }

    .metric-change {
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--success-clr);
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      background: var(--success-bg);
      padding: var(--spacing-xs) var(--spacing-sm);
      border-radius: 12px;
    }

    .metric-badge {
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--amber-clr);
      background: var(--warning-bg);
      padding: var(--spacing-xs) var(--spacing-sm);
      border-radius: 12px;
    }

    /* Studio Pipeline and Status */
    .widget-container,
    .studio-pipeline {
      background: var(--white-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-xl);
      border: 1px solid var(--line-clr);
      box-shadow: var(--shadow-md);
    }

    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: var(--spacing-lg);
      padding-bottom: var(--spacing-md);
      border-bottom: 1px solid var(--line-clr);
    }

    .section-title {
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--text-clr);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
    }

    .section-link {
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--main-navy-clr);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      transition: color 0.2s;
    }

    .section-link:hover {
      color: var(--yellow-clr);
    }

    .content-status-list {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
    }

    .status-item {
      background: var(--base-clr);
      padding: var(--spacing-md);
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
      border: 1px solid var(--line-clr);
      transition: all 0.2s ease;
    }

    .status-item:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      background: var(--white-clr);
      border-color: var(--line-clr);
    }

    /* Status colors */
    .status-item.requires-attention {
      background: #fef2f2;
      border-color: #fecaca;
    }

    .status-item.academic-audit {
      background: #fffbeb;
      border-color: #fde68a;
    }

    .status-item.published {
      background: #f0fdf4;
      border-color: #bbf7d0;
    }

    .status-icon {
      width: 36px;
      height: 36px;
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .status-icon.red {
      background: #fee2e2;
      color: #dc2626;
    }

    .status-icon.yellow {
      background: #fef3c7;
      color: #d97706;
    }

    .status-icon.green {
      background: #d1fae5;
      color: #059669;
    }

    .status-content {
      flex: 1;
    }

    .status-title {
      font-size: var(--font-size-base);
      font-weight: 700;
      color: var(--main-text-clr);
      margin: 0 0 var(--spacing-xs) 0;
    }

    .status-subtitle {
      font-size: var(--font-size-xs);
      font-weight: 500;
      color: var(--text-clr);
      margin: 0;
    }

    .status-action {
      padding: var(--spacing-xs) var(--spacing-md);
      background: var(--error-clr);
      color: var(--white-clr);
      border: none;
      border-radius: var(--radius-sm);
      font-size: var(--font-size-xs);
      font-weight: 700;
      cursor: pointer;
      text-transform: uppercase;
      transition: background 0.2s;
    }

    .status-action:hover {
      background: #b91c1c;
    }

    .status-chevron {
      color: #9ca3af;
      cursor: pointer;
      transition: color 0.2s;
    }

    .status-chevron:hover {
      color: var(--main-navy-clr);
    }

    /* Right Column Widgets */
    .invitation-card {
      background: var(--white-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-lg);
      border: 1px solid var(--line-clr);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-xl);
      box-shadow: var(--shadow-md);
    }

    .invitation-header {
      display: flex;
      align-items: flex-start;
      gap: var(--spacing-lg);
    }

    .invitation-icon {
      width: 44px;
      height: 44px;
      background: var(--yellow-background-clr);
      border: 1px solid #fde68a;
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .invitation-badge {
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: #d97706;
      text-transform: uppercase;
      margin: 0 0 var(--spacing-sm) 0;
      letter-spacing: 0.4px;
    }

    .invitation-title {
      font-size: var(--font-size-lg);
      font-weight: 700;
      color: var(--main-text-clr);
      margin: 0;
      line-height: var(--line-height-tight);
    }

    .invitation-buttons {
      display: flex;
      gap: var(--spacing-xl);
    }

    .btn-decline,
    .btn-review {
      flex: 1;
      padding: var(--spacing-md);
      border-radius: var(--radius-lg);
      font-size: var(--font-size-xs);
      font-weight: 700;
      cursor: pointer;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--spacing-sm);
      border: none;
      transition: all 0.2s;
    }

    .btn-decline {
      background: var(--line-clr);
      color: var(--text-clr);
    }

    .btn-decline:hover {
      background: #e2e8f0;
      color: var(--main-text-clr);
    }

    .btn-review {
      background: var(--main-navy-clr);
      color: var(--white-clr);
      box-shadow: 0 2px 8px rgba(27, 23, 99, 0.15);
    }

    .btn-review:hover {
      background: #1f1a5a;
      transform: translateY(-1px);
    }

    .recent-activity-card {
      background: var(--white-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-xl);
      border: 1px solid var(--line-clr);
      box-shadow: var(--shadow-md);
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: var(--spacing-lg);
      padding: var(--spacing-lg) 0;
      border-bottom: 1px solid var(--line-clr);
    }

    .activity-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .activity-thumbnail {
      width: 42px;
      height: 42px;
      border-radius: var(--radius-lg);
      overflow: hidden;
      border: 1px solid var(--line-clr);
    }

    .activity-thumbnail img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .activity-info {
      flex: 1;
    }

    .activity-name {
      font-size: var(--font-size-base);
      font-weight: 700;
      color: var(--main-text-clr);
      margin: 0 0 var(--spacing-xs) 0;
    }

    .activity-date {
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      font-size: var(--font-size-xs);
      color: var(--text-clr);
      margin: 0;
      font-weight: 500;
    }

    .pro-insight-card {
      background: linear-gradient(135deg,
          var(--main-navy-clr) 0%,
          var(--navy-dark) 100%);
      border-radius: var(--radius-xl);
      padding: var(--spacing-xl);
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    }

    .pro-insight-card::before {
      content: "";
      position: absolute;
      right: -50px;
      bottom: -50px;
      width: 200px;
      height: 200px;
      background: rgba(251, 197, 49, 0.08);
      border-radius: 50%;
      pointer-events: none;
    }

    .pro-insight-badge {
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--yellow-clr);
      margin-bottom: var(--spacing-md);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .pro-insight-text {
      font-size: var(--font-size-base);
      line-height: var(--line-height-normal);
      color: rgba(255, 255, 255, 0.85);
      margin: 0;
      position: relative;
      z-index: 1;
    }

    .pro-insight-text strong {
      color: var(--yellow-clr);
      font-weight: 700;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-2xl);
      }

      .metrics-section {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .main-wrapper {
        grid-column: 1 / -1;
      }

      .dashboard-content {
        padding: var(--spacing-2xl);
      }

      .hero-card {
        padding: var(--spacing-lg);
      }

      .metrics-section {
        grid-template-columns: 1fr;
      }

      .hero-heading {
        font-size: var(--font-size-3xl);
      }

      .hero-buttons {
        flex-direction: column;
      }

      .btn-primary,
      .btn-secondary {
        width: 100%;
        justify-content: center;
      }

      .invitation-buttons {
        gap: var(--spacing-md);
      }

      .btn-decline,
      .btn-review {
        font-size: var(--font-size-xs);
        padding: var(--spacing-sm);
      }
    }

    @media (max-width: 600px) {
      .dashboard-content {
        padding: var(--spacing-lg);
      }

      .dashboard-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
      }

      .hero-card {
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
      }

      .hero-heading {
        font-size: var(--font-size-3xl);
      }

      .hero-description {
        font-size: var(--font-size-base);
        margin-bottom: var(--spacing-xl);
      }

      .metric-card {
        padding: var(--spacing-md);
      }

      .metrics-section {
        gap: var(--spacing-md);
      }

      .widget-container,
      .studio-pipeline,
      .invitation-card,
      .recent-activity-card,
      .pro-insight-card {
        padding: var(--spacing-lg);
      }

      .section-header {
        margin-bottom: var(--spacing-md);
        padding-bottom: var(--spacing-sm);
      }

      .status-item {
        padding: var(--spacing-sm);
        gap: var(--spacing-sm);
      }

      .status-icon {
        width: 32px;
        height: 32px;
      }

      .invitation-buttons {
        gap: var(--spacing-sm);
      }
    }
</style>

<body>
  @include('partials.navbar-before-login')
  @include('trainer.partials.sidebar', ['activeMenu' => 'home'])

  <div class="main-wrapper">
    <main class="dashboard-content">
      <div class="hero-card">
        <div class="hero-decoration-1"></div>
        <div class="hero-decoration-2"></div>
        <div class="hero-content">
          <div class="hero-breadcrumb">
            Home -
            <span class="hero-breadcrumb-active">Trainer Dashboard</span>
          </div>
          <h1 class="hero-heading">
            Senior Product Design Specialist,<br />
            <span class="hero-heading-name">Sarah.</span>
          </h1>
          <p class="hero-description">
            Your educational studio is live. Managing
            <strong>3 pending assets</strong> and a network of
            <strong>1,250 learners</strong>.
          </p>
          <div class="hero-buttons">
            <a href="#" class="btn-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path
                  d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
              </svg>
              SCHEDULE HUB
            </a>
            <a href="#" class="btn-secondary">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path
                  d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
              </svg>
              CONTENT MANAGER
            </a>
          </div>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="grid-column">
          <div class="metrics-section">
            <div class="metric-card">
              <p class="metric-label">Learners Reached</p>
              <div class="metric-value-row">
                <h2 class="metric-value">1,250</h2>
                <span class="metric-change">
                  <svg width="16" height="16" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9V3M6 3L3 6M6 3L9 6" />
                  </svg>
                  +5.4%
                </span>
              </div>
            </div>

            <div class="metric-card">
              <p class="metric-label">Avg. Satisfaction</p>
              <div class="metric-value-row">
                <h2 class="metric-value">4.8</h2>
                <span class="metric-badge">Top 2%</span>
              </div>
            </div>

            <div class="metric-card">
              <p class="metric-label">Influence Score</p>
              <div class="metric-value-row">
                <h2 class="metric-value">94</h2>
                <span class="metric-change">
                  <svg width="16" height="16" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 9V3M6 3L3 6M6 3L9 6" />
                  </svg>
                  +1.2
                </span>
              </div>
            </div>
          </div>

          <div class="studio-pipeline">
            <div class="section-header">
              <h3 class="section-title">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path
                    d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                  </path>
                  <polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline>
                  <polyline points="7.5 19.79 7.5 14.6 3 12"></polyline>
                  <polyline points="21 12 16.5 14.6 16.5 19.79"></polyline>
                  <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                  <line x1="12" y1="22.08" x2="12" y2="12"></line>
                </svg>
                Studio Pipeline
              </h3>
              <a href="#" class="section-link">
                Manage Lab
                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M6 3L11 8L6 13" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </a>
            </div>

            <div class="content-status-list">
              <div class="status-item requires-attention">
                <div class="status-icon red">
                  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="M12 8v4"></path>
                    <path d="M12 16h.01"></path>
                  </svg>
                </div>
                <div class="status-content">
                  <h4 class="status-title">Requires Attention</h4>
                  <p class="status-subtitle">1 Assets Need Refinement</p>
                </div>
                <button class="status-action">ACTION</button>
              </div>

              <div class="status-item academic-audit">
                <div class="status-icon yellow">
                  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                  </svg>
                </div>
                <div class="status-content">
                  <h4 class="status-title">Academic Audit</h4>
                  <p class="status-subtitle">2 Assets Under Review</p>
                </div>
                <svg class="status-chevron" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </div>

              <div class="status-item published">
                <div class="status-icon green">
                  <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                  </svg>
                </div>
                <div class="status-content">
                  <h4 class="status-title">Published Content</h4>
                  <p class="status-subtitle">1 Assets Live for Students</p>
                </div>
                <svg class="status-chevron" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                  stroke-width="2">
                  <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <div class="grid-column">
          <div class="invitation-card">
            <div class="invitation-header">
              <div class="invitation-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round">
                  <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z" fill="#d97706" />
                </svg>
              </div>
              <div>
                <p class="invitation-badge">Special Engagement</p>
                <h3 class="invitation-title">
                  Interaction Design for Mobile
                </h3>
              </div>
            </div>
            <div class="invitation-buttons">
              <button class="btn-decline">DECLINE</button>
              <button class="btn-review">
                REVIEW OFFER
                <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M6 3L11 8L6 13" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>
            </div>
          </div>

          <div class="recent-activity-card">
            <div class="section-header">
              <h3 class="section-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FDB913" stroke-width="2">
                  <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z" fill="#FDB913" />
                </svg>
                Recent Activity
              </h3>
              <a href="#" class="section-link">Schedule</a>
            </div>

            <div class="activity-content">
              <div class="activity-item">
                <div class="activity-thumbnail">
                  <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=100&h=100&fit=crop"
                    alt="Figma" />
                </div>
                <div class="activity-info">
                  <h4 class="activity-name">Advanced Figma Constraints</h4>
                  <p class="activity-date">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="#FDB913">
                      <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z" />
                      <path
                        d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                    </svg>
                    MAR 25
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="pro-insight-card">
            <div class="pro-insight-badge">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FDB913" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5z" fill="#FDB913" />
                <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              Pro Insight
            </div>
            <p class="pro-insight-text">
              Interactive quizzes increase student retention by
              <strong>24%</strong>. Propose a new module today.
            </p>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>

<script type="text/javascript" src="/assets/js/components/sidebar.js" defer></script>

</html>