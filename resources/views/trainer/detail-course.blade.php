<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
  @vite(['resources/css/app.css', 'resources/css/trainer/main.css'])
  <link rel="stylesheet" href="/assets/css/detail-course.css" />
  <title>Document</title>
</head>

<body>
  @include('partials.navbar-before-login')

  <style>
    .course-hero {
      background: radial-gradient(circle at 20% 20%,
          var(--navy-gradient-start) 0%,
          var(--main-navy-clr) 45%,
          var(--navy-dark) 100%);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-2xl) var(--spacing-3xl);
      color: var(--white-clr);
      box-shadow: var(--shadow-xl);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
    }

    .hero-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: var(--spacing-3xl);
    }

    .hero-back {
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-md);
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: var(--white-clr);
      padding: var(--spacing-md) var(--spacing-lg);
      border-radius: var(--radius-xl);
      font-size: var(--font-size-xs);
      font-weight: 800;
      letter-spacing: 1px;
      cursor: pointer;
      transition: 0.3s;
    }

    .hero-back:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .hero-badges {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    /* Badge Kuning */
    .hero-pill-accent {
      background: var(--yellow-clr);
      color: var(--main-navy-clr);
      padding: var(--spacing-sm) var(--spacing-lg);
      border-radius: 999px;
      font-size: var(--font-size-xs);
      font-weight: 900;
      letter-spacing: 0.5px;
    }

    /* Badge ID di pojok kanan */
    .hero-pill-outline {
      border: 1px solid rgba(255, 255, 255, 0.3);
      background: rgba(255, 255, 255, 0.05);
      color: var(--white-clr);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: 999px;
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .hero-body {
      display: grid;
      grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
      gap: var(--spacing-2xl);
      align-items: center;
    }

    .hero-copy h1 {
      margin: 0 0 var(--spacing-md) 0;
      font-size: var(--font-size-3xl);
      font-weight: 800;
      color: var(--white-clr);
    }

    .hero-copy h1 span {
      color: var(--yellow-clr);
      font-style: italic;
    }

    .hero-kicker {
      display: inline-flex;
      align-items: center;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-xl);
      margin: 0 0 var(--spacing-md) 0;
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.8);
    }

    .kicker-icon {
      color: var(--yellow-clr);
      margin-right: var(--spacing-sm);
    }

    .hero-stats {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .stat-chip {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    .stat-chip>i {
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1.5px solid rgba(255, 255, 255, 0.3);
      border-radius: var(--radius-md);
      font-size: var(--font-size-base);
      flex-shrink: 0;
      -webkit-text-fill-color: transparent;
      -webkit-text-stroke: 1.2px var(--yellow-clr);
    }

    .stat-chip:nth-child(3)>i {
      -webkit-text-stroke: 1.2px var(--yellow-clr);
    }

    .stat-chip>div {
      display: flex;
      flex-direction: column;
    }

    .stat-icon {
      width: 32px;
      height: 32px;
      border-radius: 10px;
      background: transparent;
      color: #f5c542;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      flex-shrink: 0;
    }

    .stat-chip:nth-child(1) .stat-icon {
      background: rgba(27, 23, 99, 0.15);
      color: var(--main-navy-clr);
    }

    .stat-chip:nth-child(2) .stat-icon {
      background: rgba(255, 165, 0, 0.25);
      color: #ffa500;
    }

    .stat-chip:nth-child(3) .stat-icon {
      background: rgba(245, 197, 66, 0.25);
      color: #f5c542;
    }

    .stat-label {
      margin: 0;
      font-size: var(--font-size-xs);
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: rgba(255, 255, 255, 0.7);
      font-weight: 700;
    }

    .stat-value {
      margin: 2px 0 0 0;
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--white-clr);
    }

    .hero-image-wrap {
      background: rgba(255, 255, 255, 0.12);
      padding: var(--spacing-md);
      border-radius: var(--radius-2xl);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .hero-image-wrap img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: var(--radius-xl);
      display: block;
    }

    .course-tabs {
      margin: var(--spacing-lg) 0 var(--spacing-xl);
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-sm);
      display: flex;
      gap: var(--spacing-sm);
    }

    .tab-pill {
      flex: 1;
      padding: var(--spacing-md) var(--spacing-lg);
      border-radius: var(--radius-lg);
      border: 1px solid transparent;
      background: transparent;
      font-size: var(--font-size-xs);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--gray-second-clr);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--spacing-sm);
    }

    .tab-pill i {
      font-size: var(--font-size-base);
    }

    .tab-pill.active {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      color: var(--main-navy-clr);
      box-shadow: var(--shadow-md);
    }

    .tab-pill.active i {
      color: var(--yellow-clr);
    }

    .course-layout {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 300px;
      gap: 22px;
      align-items: start;
    }

    .unit-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .unit-header p {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.8px;
      color: var(--gray-second-clr);
      text-transform: uppercase;
    }

    .btn-propose {
      border: 1px solid var(--line-clr);
      background: transparent;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--main-navy-clr);
      cursor: pointer;
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-xs);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.2s;
    }

    .btn-propose:hover {
      background: var(--base-clr);
    }

    .btn-propose i {
      font-size: var(--font-size-sm);
      font-weight: bold;
    }

    .unit-card {
      background: var(--white-clr);
      border: 2px solid var(--main-navy-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-lg);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
      box-shadow: var(--shadow-lg);
      transition: all 0.2s;
    }

    .unit-card:not(.compact).collapsed {
      border: 1px solid var(--line-clr);
      box-shadow: none;
    }

    .unit-card:not(.compact).collapsed:hover {
      border-color: #dce4f0;
    }

    .unit-card.compact {
      border: 1px solid var(--line-clr);
      box-shadow: none;
      opacity: 1;
      padding: var(--spacing-lg);
      transition: all 0.2s;
    }

    .unit-card.compact:hover {
      border-color: #dce4f0;
    }

    .unit-card.compact.expanded {
      border: 2px solid var(--main-navy-clr);
      box-shadow: var(--shadow-lg);
    }

    .unit-top {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .unit-index {
      width: 42px;
      height: 42px;
      border-radius: var(--radius-xl);
      background: var(--main-navy-clr);
      color: var(--white-clr);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: var(--font-size-lg);
      flex-shrink: 0;
      transition: all 0.2s;
    }

    /* Collapsed state untuk first card (non-compact) */
    .unit-card:not(.compact).collapsed .unit-index {
      background: var(--line-clr);
      color: #c9d2e0;
    }

    .unit-card:not(.compact).collapsed:hover .unit-index {
      background: #e8eff7;
      color: var(--gray-light);
    }

    /* Default style untuk compact cards (Module 2-4) */
    .unit-card.compact .unit-index {
      background: var(--line-clr);
      color: #c9d2e0;
    }

    /* Hover style untuk compact cards */
    .unit-card.compact:hover .unit-index {
      background: #e8eff7;
      color: var(--gray-light);
    }

    /* Expanded style untuk compact cards */
    .unit-card.compact.expanded .unit-index {
      background: var(--main-navy-clr);
      color: var(--yellow-clr);
    }

    .unit-index.muted {
      background: var(--line-clr);
      color: var(--gray-second-clr);
    }

    .unit-card.compact .unit-index.muted {
      background: #e8eff7;
      color: var(--gray-light);
    }

    .unit-card.compact.expanded .unit-index.muted {
      background: var(--main-navy-clr);
      color: var(--yellow-clr);
    }

    .unit-title h3 {
      margin: 0;
      font-size: var(--font-size-base);
      font-weight: 700;
      color: var(--main-navy-clr);
    }

    .unit-meta {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
      margin-top: var(--spacing-xs);
      font-size: var(--font-size-xs);
      color: var(--gray-second-clr);
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.4px;
    }

    .unit-meta i {
      font-size: var(--font-size-xs);
      margin-right: 2px;
    }

    .unit-status {
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-xs);
      color: var(--success-clr);
      font-weight: 600;
    }

    .unit-status i {
      font-size: var(--font-size-xs);
    }

    .unit-toggle {
      margin-left: auto;
      width: 32px;
      height: 32px;
      border-radius: 999px;
      border: none;
      background: var(--main-navy-clr);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      flex-shrink: 0;
      transition: all 0.2s;
    }

    .unit-toggle:hover {
      background: var(--navy-gradient-start);
    }

    .unit-toggle i {
      color: var(--white-clr);
      font-size: var(--font-size-lg);
      transition: transform 0.3s ease;
      transform: rotate(180deg);
    }

    .unit-card.collapsed .unit-toggle i {
      transform: rotate(0deg);
    }

    .unit-card.compact .unit-toggle i {
      transform: rotate(0deg);
    }

    .unit-assets {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: var(--spacing-md);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .unit-card.collapsed .unit-assets {
      display: none;
    }

    .asset-mini {
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-md);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
      background: var(--base-clr);
      transition: all 0.2s;
      cursor: pointer;
    }

    .asset-mini:hover {
      border-color: var(--main-navy-clr);
      box-shadow: 0 4px 12px rgba(27, 23, 99, 0.1);
    }

    .asset-mini i {
      width: 32px;
      height: 32px;
      border-radius: var(--radius-lg);
      background: rgba(27, 23, 99, 0.08);
      color: var(--main-navy-clr);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: var(--font-size-lg);
      flex-shrink: 0;
    }

    .asset-mini:nth-child(2) i {
      background: var(--warning-bg);
      color: #e5a91e;
    }

    .asset-mini:nth-child(3) i {
      background: var(--success-bg);
      color: var(--success-clr);
    }

    .asset-mini h4 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--main-navy-clr);
    }

    .asset-mini p {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    .course-right {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .grading-card {
      background: linear-gradient(135deg, var(--navy-gradient-alt), #1b144c);
      border-radius: 18px;
      padding: 18px;
      color: #ffffff;
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .grading-head {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 11px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      font-weight: 700;
    }

    .bolt {
      width: 28px;
      height: 28px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.12);
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .grading-status {
      background: rgba(255, 255, 255, 0.08);
      padding: var(--spacing-md);
      border-radius: var(--radius-xl);
    }

    .grading-status p {
      margin: 0 0 var(--spacing-sm) 0;
      font-size: var(--font-size-xs);
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: rgba(255, 255, 255, 0.7);
    }

    .grading-status h4 {
      margin: 0;
      font-size: var(--font-size-base);
      font-weight: 700;
    }

    .grading-notes {
      margin: 0;
      padding-left: var(--spacing-lg);
      font-size: var(--font-size-xs);
      color: rgba(255, 255, 255, 0.8);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-sm);
    }

    .grading-btn {
      border: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(255, 255, 255, 0.1);
      color: var(--white-clr);
      padding: var(--spacing-md);
      border-radius: var(--radius-xl);
      font-size: var(--font-size-xs);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      cursor: pointer;
    }

    .instructor-card {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-lg);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
    }

    .instructor-title {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--gray-second-clr);
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }

    .instructor-item {
      display: flex;
      gap: var(--spacing-md);
      align-items: center;
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-md);
    }

    .instructor-item .dot {
      width: 28px;
      height: 28px;
      border-radius: var(--radius-lg);
      background: var(--base-clr);
    }

    .instructor-item h4 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--main-navy-clr);
    }

    .instructor-item p {
      margin: var(--spacing-xs) 0 0 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    /* Tab Content Management */
    .tab-content {
      display: none;
      flex-direction: column;
      gap: var(--spacing-lg);
      width: 100%;
    }

    .tab-content.active {
      display: flex;
    }

    /* Quiz Recap Styles */
    .recap-stats {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-sm);
    }

    .stat-box {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-xl);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
    }

    .stat-box-label {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.8px;
      color: var(--gray-second-clr);
      text-transform: uppercase;
    }

    .stat-box-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .stat-box-content h2 {
      margin: 0;
      font-size: var(--font-size-6xl);
      font-weight: 800;
      color: var(--main-navy-clr);
    }

    .stat-box-icon {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-xl);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: var(--font-size-lg);
    }

    .stat-box-icon.green {
      background: var(--success-bg);
      color: var(--success-clr);
    }

    .stat-box-icon.purple {
      background: rgba(27, 23, 99, 0.08);
      color: var(--main-navy-clr);
    }

    .stat-box-sub {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--success-clr);
      text-transform: uppercase;
      letter-spacing: 0.4px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .stat-box:nth-child(2) .stat-box-sub {
      color: var(--main-navy-clr);
    }

    .stat-box-sub i {
      font-size: var(--font-size-xs);
    }

    .grading-registry {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-xl);
      box-shadow: var(--shadow-lg);
    }

    .registry-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: var(--spacing-lg);
    }

    .registry-header h3 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 900;
      color: var(--main-navy-clr);
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .export-btn {
      border: 1px solid var(--line-clr);
      background: transparent;
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--main-navy-clr);
      cursor: pointer;
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-sm);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.2s;
    }

    .export-btn:hover {
      background: var(--base-clr);
    }

    .export-btn i {
      font-size: var(--font-size-base);
    }

    .registry-table {
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    .table-header {
      display: grid;
      grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
      gap: var(--spacing-lg);
      padding: var(--spacing-md) var(--spacing-lg);
      background: var(--base-clr);
      border-radius: var(--radius-lg);
      margin-bottom: var(--spacing-sm);
      font-size: var(--font-size-xs);
      font-weight: 800;
      color: var(--gray-second-clr);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .table-row {
      display: grid;
      grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
      gap: var(--spacing-lg);
      padding: var(--spacing-lg);
      border-bottom: 1px solid var(--line-clr);
      align-items: center;
    }

    .table-row:hover {
      background: var(--base-clr);
    }

    .col-learner {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .col-learner img {
      width: 36px;
      height: 36px;
      border-radius: var(--radius-xl);
      object-fit: cover;
      flex-shrink: 0;
    }

    .col-learner h4 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 800;
      color: var(--main-navy-clr);
    }

    .col-learner p {
      margin: 2px 0 0 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
    }

    .col-submission p {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--main-navy-clr);
    }

    .col-submission span {
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
    }

    .col-score {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .score-bullet {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .score-bullet.green {
      background: var(--success-clr);
    }

    .score-bullet.orange {
      background: #f5a623;
    }

    .col-score strong {
      font-size: var(--font-size-sm);
      font-weight: 800;
      color: var(--main-navy-clr);
    }

    .col-certificate {
      display: flex;
      justify-content: flex-start;
    }

    .badge-issued {
      background: var(--success-bg);
      color: var(--success-clr);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      font-size: var(--font-size-xs);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-xs);
    }

    .badge-issued i {
      font-size: var(--font-size-xs);
    }

    .badge-pending {
      background: var(--line-clr);
      color: var(--gray-second-clr);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      font-size: var(--font-size-xs);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    /* Enrollment Styles */
    .enrollment-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: var(--spacing-md);
    }

    .enrollment-header h3 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 900;
      color: var(--main-navy-clr);
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .total-badge {
      background: var(--main-navy-clr);
      color: var(--white-clr);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      font-size: var(--font-size-xs);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .learner-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: var(--spacing-lg);
    }

    .learner-card {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-lg);
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
      transition: all 0.2s;
      position: relative;
    }

    .learner-card:hover {
      box-shadow: var(--shadow-lg);
      border-color: var(--main-navy-clr);
    }

    .learner-card.inactive {
      opacity: 0.6;
    }

    .learner-card img {
      width: 48px;
      height: 48px;
      border-radius: var(--radius-xl);
      object-fit: cover;
      flex-shrink: 0;
    }

    .learner-info {
      flex: 1;
    }

    .learner-info h4 {
      margin: 0 0 var(--spacing-xs) 0;
      font-size: var(--font-size-base);
      font-weight: 800;
      color: var(--main-navy-clr);
    }

    .learner-info p {
      margin: 0 0 var(--spacing-sm) 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--main-navy-clr);
      letter-spacing: 0.3px;
    }

    .learner-date {
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
    }

    .inactive-badge {
      position: absolute;
      top: var(--spacing-md);
      right: var(--spacing-md);
      background: var(--error-bg);
      color: var(--error-clr);
      padding: var(--spacing-xs) var(--spacing-sm);
      border-radius: var(--radius-sm);
      font-size: var(--font-size-xs);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    /* Navigation Icons */
    .nav-icon {
      color: #e3e3e3;
    }

    .nav-icon-lg {
      color: #e3e3e3;
      font-size: var(--font-size-3xl);
    }

    /* Hero Back Icon */
    .back-icon {
      font-size: var(--font-size-base);
    }

    /* Grading Icon */
    .grading-icon {
      color: var(--yellow-clr);
      font-size: var(--font-size-2xl);
    }

    @media (max-width: 1024px) {
      .hero-body {
        grid-template-columns: 1fr;
      }

      .course-layout {
        grid-template-columns: 1fr;
      }

      .unit-assets {
        grid-template-columns: 1fr;
      }

      .recap-stats {
        grid-template-columns: 1fr;
      }

      .learner-grid {
        grid-template-columns: 1fr;
      }

      .table-header,
      .table-row {
        grid-template-columns: 1.5fr 1fr 1fr 1fr;
        gap: var(--spacing-md);
        font-size: var(--font-size-xs);
      }
    }

    @media (max-width: 720px) {
      .course-tabs {
        flex-direction: column;
      }

      .table-header {
        display: none;
      }

      .table-row {
        grid-template-columns: 1fr;
        gap: var(--spacing-md);
        padding: var(--spacing-lg);
        border: 1px solid var(--line-clr);
        border-radius: var(--radius-xl);
        margin-bottom: var(--spacing-md);
      }

      .col-learner,
      .col-submission,
      .col-score,
      .col-certificate {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
      }
    }

    main.detail-course {
      padding: var(--spacing-4xl);
      background-color: var(--base-clr);
      overflow-y: auto;
      max-width: 1200px;
      margin: 0 auto;
      width: 100%;
    }

    .course-hero {
      background: radial-gradient(circle at 20% 20%,
          var(--navy-gradient-start) 0%,
          var(--main-navy-clr) 45%,
          var(--navy-dark) 100%);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-2xl) var(--spacing-3xl);
      color: var(--white-clr);
      box-shadow: var(--shadow-xl);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
    }

    .hero-head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: var(--spacing-3xl);
    }

    .hero-back {
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-md);
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: var(--white-clr);
      padding: var(--spacing-md) var(--spacing-lg);
      border-radius: var(--radius-xl);
      font-size: var(--font-size-xs);
      font-weight: 800;
      letter-spacing: 1px;
      cursor: pointer;
      transition: 0.3s;
    }

    .hero-back:hover {
      background: rgba(255, 255, 255, 0.15);
    }

    .hero-badges {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    /* Badge Kuning */
    .hero-pill-accent {
      background: var(--yellow-clr);
      color: var(--main-navy-clr);
      padding: var(--spacing-sm) var(--spacing-lg);
      border-radius: 999px;
      font-size: var(--font-size-xs);
      font-weight: 900;
      letter-spacing: 0.5px;
    }

    /* Badge ID di pojok kanan */
    .hero-pill-outline {
      border: 1px solid rgba(255, 255, 255, 0.3);
      background: rgba(255, 255, 255, 0.05);
      color: var(--white-clr);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: 999px;
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.5px;
    }

    .hero-body {
      display: grid;
      grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
      gap: var(--spacing-2xl);
      align-items: center;
    }

    .hero-copy h1 {
      margin: 0 0 var(--spacing-md) 0;
      font-size: var(--font-size-3xl);
      font-weight: 800;
      color: var(--white-clr);
    }

    .hero-copy h1 span {
      color: var(--yellow-clr);
      font-style: italic;
    }

    .hero-kicker {
      display: inline-flex;
      align-items: center;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.2);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-xl);
      margin: 0 0 var(--spacing-md) 0;
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.8);
    }

    .kicker-icon {
      color: var(--yellow-clr);
      margin-right: var(--spacing-sm);
    }

    .hero-stats {
      display: flex;
      gap: 16px;
      flex-wrap: wrap;
    }

    .stat-chip {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
    }

    .stat-chip>i {
      width: 34px;
      height: 34px;
      display: flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border: 1.5px solid rgba(255, 255, 255, 0.3);
      border-radius: var(--radius-md);
      font-size: var(--font-size-base);
      flex-shrink: 0;
      -webkit-text-fill-color: transparent;
      -webkit-text-stroke: 1.2px var(--yellow-clr);
    }

    .stat-chip:nth-child(3)>i {
      -webkit-text-stroke: 1.2px var(--yellow-clr);
    }

    .stat-chip>div {
      display: flex;
      flex-direction: column;
    }

    .stat-icon {
      width: 32px;
      height: 32px;
      border-radius: 10px;
      background: transparent;
      color: #f5c542;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      flex-shrink: 0;
    }

    .stat-chip:nth-child(1) .stat-icon {
      background: rgba(27, 23, 99, 0.15);
      color: var(--main-navy-clr);
    }

    .stat-chip:nth-child(2) .stat-icon {
      background: rgba(255, 165, 0, 0.25);
      color: #ffa500;
    }

    .stat-chip:nth-child(3) .stat-icon {
      background: rgba(245, 197, 66, 0.25);
      color: #f5c542;
    }

    .stat-label {
      margin: 0;
      font-size: var(--font-size-xs);
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: rgba(255, 255, 255, 0.7);
      font-weight: 700;
    }

    .stat-value {
      margin: 2px 0 0 0;
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--white-clr);
    }

    .hero-image-wrap {
      background: rgba(255, 255, 255, 0.12);
      padding: var(--spacing-md);
      border-radius: var(--radius-2xl);
      border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .hero-image-wrap img {
      width: 100%;
      height: 160px;
      object-fit: cover;
      border-radius: var(--radius-xl);
      display: block;
    }

    .course-tabs {
      margin: var(--spacing-lg) 0 var(--spacing-xl);
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-sm);
      display: flex;
      gap: var(--spacing-sm);
    }

    .tab-pill {
      flex: 1;
      padding: var(--spacing-md) var(--spacing-lg);
      border-radius: var(--radius-lg);
      border: 1px solid transparent;
      background: transparent;
      font-size: var(--font-size-xs);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: var(--gray-second-clr);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--spacing-sm);
    }

    .tab-pill i {
      font-size: var(--font-size-base);
    }

    .tab-pill.active {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      color: var(--main-navy-clr);
      box-shadow: var(--shadow-md);
    }

    .tab-pill.active i {
      color: var(--yellow-clr);
    }

    .course-layout {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 300px;
      gap: 22px;
      align-items: start;
    }

    .unit-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
    }

    .unit-header p {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.8px;
      color: var(--gray-second-clr);
      text-transform: uppercase;
    }

    .btn-propose {
      border: 1px solid var(--line-clr);
      background: transparent;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--main-navy-clr);
      cursor: pointer;
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-xs);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.2s;
    }

    .btn-propose:hover {
      background: var(--base-clr);
    }

    .btn-propose i {
      font-size: var(--font-size-sm);
      font-weight: bold;
    }

    .unit-card {
      background: var(--white-clr);
      border: 2px solid var(--main-navy-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-lg);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
      box-shadow: var(--shadow-lg);
      transition: all 0.2s;
    }

    .unit-card:not(.compact).collapsed {
      border: 1px solid var(--line-clr);
      box-shadow: none;
    }

    .unit-card:not(.compact).collapsed:hover {
      border-color: #dce4f0;
    }

    .unit-card.compact {
      border: 1px solid var(--line-clr);
      box-shadow: none;
      opacity: 1;
      padding: var(--spacing-lg);
      transition: all 0.2s;
    }

    .unit-card.compact:hover {
      border-color: #dce4f0;
    }

    .unit-card.compact.expanded {
      border: 2px solid var(--main-navy-clr);
      box-shadow: var(--shadow-lg);
    }

    .unit-top {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .unit-index {
      width: 42px;
      height: 42px;
      border-radius: var(--radius-xl);
      background: var(--main-navy-clr);
      color: var(--white-clr);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: var(--font-size-lg);
      flex-shrink: 0;
      transition: all 0.2s;
    }

    /* Collapsed state untuk first card (non-compact) */
    .unit-card:not(.compact).collapsed .unit-index {
      background: var(--line-clr);
      color: #c9d2e0;
    }

    .unit-card:not(.compact).collapsed:hover .unit-index {
      background: #e8eff7;
      color: var(--gray-light);
    }

    /* Default style untuk compact cards (Module 2-4) */
    .unit-card.compact .unit-index {
      background: var(--line-clr);
      color: #c9d2e0;
    }

    /* Hover style untuk compact cards */
    .unit-card.compact:hover .unit-index {
      background: #e8eff7;
      color: var(--gray-light);
    }

    /* Expanded style untuk compact cards */
    .unit-card.compact.expanded .unit-index {
      background: var(--main-navy-clr);
      color: var(--yellow-clr);
    }

    .unit-index.muted {
      background: var(--line-clr);
      color: var(--gray-second-clr);
    }

    .unit-card.compact .unit-index.muted {
      background: #e8eff7;
      color: var(--gray-light);
    }

    .unit-card.compact.expanded .unit-index.muted {
      background: var(--main-navy-clr);
      color: var(--yellow-clr);
    }

    .unit-title h3 {
      margin: 0;
      font-size: var(--font-size-base);
      font-weight: 700;
      color: var(--main-navy-clr);
    }

    .unit-meta {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
      margin-top: var(--spacing-xs);
      font-size: var(--font-size-xs);
      color: var(--gray-second-clr);
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 0.4px;
    }

    .unit-meta i {
      font-size: var(--font-size-xs);
      margin-right: 2px;
    }

    .unit-status {
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-xs);
      color: var(--success-clr);
      font-weight: 600;
    }

    .unit-status i {
      font-size: var(--font-size-xs);
    }

    .unit-toggle {
      margin-left: auto;
      width: 32px;
      height: 32px;
      border-radius: 999px;
      border: none;
      background: var(--main-navy-clr);
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      flex-shrink: 0;
      transition: all 0.2s;
    }

    .unit-toggle:hover {
      background: var(--navy-gradient-start);
    }

    .unit-toggle i {
      color: var(--white-clr);
      font-size: var(--font-size-lg);
      transition: transform 0.3s ease;
      transform: rotate(180deg);
    }

    .unit-card.collapsed .unit-toggle i {
      transform: rotate(0deg);
    }

    .unit-card.compact .unit-toggle i {
      transform: rotate(0deg);
    }

    .unit-assets {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: var(--spacing-md);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .unit-card.collapsed .unit-assets {
      display: none;
    }

    .asset-mini {
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-md);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
      background: var(--base-clr);
      transition: all 0.2s;
      cursor: pointer;
    }

    .asset-mini:hover {
      border-color: var(--main-navy-clr);
      box-shadow: 0 4px 12px rgba(27, 23, 99, 0.1);
    }

    .asset-mini i {
      width: 32px;
      height: 32px;
      border-radius: var(--radius-lg);
      background: rgba(27, 23, 99, 0.08);
      color: var(--main-navy-clr);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: var(--font-size-lg);
      flex-shrink: 0;
    }

    .asset-mini:nth-child(2) i {
      background: var(--warning-bg);
      color: #e5a91e;
    }

    .asset-mini:nth-child(3) i {
      background: var(--success-bg);
      color: var(--success-clr);
    }

    .asset-mini h4 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--main-navy-clr);
    }

    .asset-mini p {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    .course-right {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .grading-card {
      background: linear-gradient(135deg, var(--navy-gradient-alt), #1b144c);
      border-radius: 18px;
      padding: 18px;
      color: #ffffff;
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .grading-head {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 11px;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      font-weight: 700;
    }

    .bolt {
      width: 28px;
      height: 28px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.12);
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .grading-status {
      background: rgba(255, 255, 255, 0.08);
      padding: var(--spacing-md);
      border-radius: var(--radius-xl);
    }

    .grading-status p {
      margin: 0 0 var(--spacing-sm) 0;
      font-size: var(--font-size-xs);
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: rgba(255, 255, 255, 0.7);
    }

    .grading-status h4 {
      margin: 0;
      font-size: var(--font-size-base);
      font-weight: 700;
    }

    .grading-notes {
      margin: 0;
      padding-left: var(--spacing-lg);
      font-size: var(--font-size-xs);
      color: rgba(255, 255, 255, 0.8);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-sm);
    }

    .grading-btn {
      border: 1px solid rgba(255, 255, 255, 0.2);
      background: rgba(255, 255, 255, 0.1);
      color: var(--white-clr);
      padding: var(--spacing-md);
      border-radius: var(--radius-xl);
      font-size: var(--font-size-xs);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.6px;
      cursor: pointer;
    }

    .instructor-card {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-lg);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
    }

    .instructor-title {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--gray-second-clr);
      letter-spacing: 0.6px;
      text-transform: uppercase;
    }

    .instructor-item {
      display: flex;
      gap: var(--spacing-md);
      align-items: center;
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-md);
    }

    .instructor-item .dot {
      width: 28px;
      height: 28px;
      border-radius: var(--radius-lg);
      background: var(--base-clr);
    }

    .instructor-item h4 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--main-navy-clr);
    }

    .instructor-item p {
      margin: var(--spacing-xs) 0 0 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    /* Tab Content Management */
    .tab-content {
      display: none;
      flex-direction: column;
      gap: var(--spacing-lg);
      width: 100%;
    }

    .tab-content.active {
      display: flex;
    }

    /* Quiz Recap Styles */
    .recap-stats {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-sm);
    }

    .stat-box {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-xl);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
    }

    .stat-box-label {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.8px;
      color: var(--gray-second-clr);
      text-transform: uppercase;
    }

    .stat-box-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .stat-box-content h2 {
      margin: 0;
      font-size: var(--font-size-6xl);
      font-weight: 800;
      color: var(--main-navy-clr);
    }

    .stat-box-icon {
      width: 40px;
      height: 40px;
      border-radius: var(--radius-xl);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: var(--font-size-lg);
    }

    .stat-box-icon.green {
      background: var(--success-bg);
      color: var(--success-clr);
    }

    .stat-box-icon.purple {
      background: rgba(27, 23, 99, 0.08);
      color: var(--main-navy-clr);
    }

    .stat-box-sub {
      margin: 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--success-clr);
      text-transform: uppercase;
      letter-spacing: 0.4px;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .stat-box:nth-child(2) .stat-box-sub {
      color: var(--main-navy-clr);
    }

    .stat-box-sub i {
      font-size: var(--font-size-xs);
    }

    .grading-registry {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-xl);
      box-shadow: var(--shadow-lg);
    }

    .registry-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: var(--spacing-lg);
    }

    .registry-header h3 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 900;
      color: var(--main-navy-clr);
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .export-btn {
      border: 1px solid var(--line-clr);
      background: transparent;
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--main-navy-clr);
      cursor: pointer;
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-sm);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      transition: all 0.2s;
    }

    .export-btn:hover {
      background: var(--base-clr);
    }

    .export-btn i {
      font-size: var(--font-size-base);
    }

    .registry-table {
      display: flex;
      flex-direction: column;
      gap: 0;
    }

    .table-header {
      display: grid;
      grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
      gap: var(--spacing-lg);
      padding: var(--spacing-md) var(--spacing-lg);
      background: var(--base-clr);
      border-radius: var(--radius-lg);
      margin-bottom: var(--spacing-sm);
      font-size: var(--font-size-xs);
      font-weight: 800;
      color: var(--gray-second-clr);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .table-row {
      display: grid;
      grid-template-columns: 2fr 1.5fr 1.2fr 1.2fr;
      gap: var(--spacing-lg);
      padding: var(--spacing-lg);
      border-bottom: 1px solid var(--line-clr);
      align-items: center;
    }

    .table-row:hover {
      background: var(--base-clr);
    }

    .col-learner {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .col-learner img {
      width: 36px;
      height: 36px;
      border-radius: var(--radius-xl);
      object-fit: cover;
      flex-shrink: 0;
    }

    .col-learner h4 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 800;
      color: var(--main-navy-clr);
    }

    .col-learner p {
      margin: 2px 0 0 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
    }

    .col-submission p {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--main-navy-clr);
    }

    .col-submission span {
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
    }

    .col-score {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .score-bullet {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      flex-shrink: 0;
    }

    .score-bullet.green {
      background: var(--success-clr);
    }

    .score-bullet.orange {
      background: #f5a623;
    }

    .col-score strong {
      font-size: var(--font-size-sm);
      font-weight: 800;
      color: var(--main-navy-clr);
    }

    .col-certificate {
      display: flex;
      justify-content: flex-start;
    }

    .badge-issued {
      background: var(--success-bg);
      color: var(--success-clr);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      font-size: var(--font-size-xs);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-xs);
    }

    .badge-issued i {
      font-size: var(--font-size-xs);
    }

    .badge-pending {
      background: var(--line-clr);
      color: var(--gray-second-clr);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      font-size: var(--font-size-xs);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    /* Enrollment Styles */
    .enrollment-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: var(--spacing-md);
    }

    .enrollment-header h3 {
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 900;
      color: var(--main-navy-clr);
      text-transform: uppercase;
      letter-spacing: 0.6px;
    }

    .total-badge {
      background: var(--main-navy-clr);
      color: var(--white-clr);
      padding: var(--spacing-sm) var(--spacing-md);
      border-radius: var(--radius-md);
      font-size: var(--font-size-xs);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .learner-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: var(--spacing-lg);
    }

    .learner-card {
      background: var(--white-clr);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-lg);
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
      transition: all 0.2s;
      position: relative;
    }

    .learner-card:hover {
      box-shadow: var(--shadow-lg);
      border-color: var(--main-navy-clr);
    }

    .learner-card.inactive {
      opacity: 0.6;
    }

    .learner-card img {
      width: 48px;
      height: 48px;
      border-radius: var(--radius-xl);
      object-fit: cover;
      flex-shrink: 0;
    }

    .learner-info {
      flex: 1;
    }

    .learner-info h4 {
      margin: 0 0 var(--spacing-xs) 0;
      font-size: var(--font-size-base);
      font-weight: 800;
      color: var(--main-navy-clr);
    }

    .learner-info p {
      margin: 0 0 var(--spacing-sm) 0;
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--main-navy-clr);
      letter-spacing: 0.3px;
    }

    .learner-date {
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--gray-second-clr);
    }

    .inactive-badge {
      position: absolute;
      top: var(--spacing-md);
      right: var(--spacing-md);
      background: var(--error-bg);
      color: var(--error-clr);
      padding: var(--spacing-xs) var(--spacing-sm);
      border-radius: var(--radius-sm);
      font-size: var(--font-size-xs);
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.4px;
    }

    /* Navigation Icons */
    .nav-icon {
      color: #e3e3e3;
    }

    .nav-icon-lg {
      color: #e3e3e3;
      font-size: var(--font-size-3xl);
    }

    /* Hero Back Icon */
    .back-icon {
      font-size: var(--font-size-base);
    }

    /* Grading Icon */
    .grading-icon {
      color: var(--yellow-clr);
      font-size: var(--font-size-2xl);
    }

    @media (max-width: 1024px) {
      .hero-body {
        grid-template-columns: 1fr;
      }

      .course-layout {
        grid-template-columns: 1fr;
      }

      .unit-assets {
        grid-template-columns: 1fr;
      }

      .recap-stats {
        grid-template-columns: 1fr;
      }

      .learner-grid {
        grid-template-columns: 1fr;
      }

      .table-header,
      .table-row {
        grid-template-columns: 1.5fr 1fr 1fr 1fr;
        gap: var(--spacing-md);
        font-size: var(--font-size-xs);
      }
    }

    @media (max-width: 720px) {
      .course-tabs {
        flex-direction: column;
      }

      .table-header {
        display: none;
      }

      .table-row {
        grid-template-columns: 1fr;
        gap: var(--spacing-md);
        padding: var(--spacing-lg);
        border: 1px solid var(--line-clr);
        border-radius: var(--radius-xl);
        margin-bottom: var(--spacing-md);
      }

      .col-learner,
      .col-submission,
      .col-score,
      .col-certificate {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>

  <body>
    <div class="trainer-page">
      @include('trainer.partials.sidebar', ['activeMenu' => 'course'])
      <main class="detail-course">
        <section class="course-hero">
          <div class="hero-head">
            <button class="hero-back" type="button">
              <i class="bi bi-chevron-left back-icon"></i>
              REPOSITORY LEDGER
            </button>

            <div class="hero-badges">
              <span class="hero-pill-accent">INTERMEDIATE TIER</span>
              <span class="hero-pill-outline">CUR-ID: C1</span>
            </div>
          </div>

          <div class="hero-body">
            <div class="hero-copy">
              <p class="hero-kicker">
                <i class="bi bi-star-fill kicker-icon"></i>ACADEMIC CURRICULUM •
                DETAIL
              </p>
              <h1>Visual Branding <span>Architecture</span></h1>
              <div class="hero-stats">
                <div class="stat-chip">
                  <i class="bi bi-people"></i>
                  <div>
                    <p class="stat-label">ENROLLMENT</p>
                    <p class="stat-value">850 Learners</p>
                  </div>
                </div>
                <div class="stat-chip">
                  <i class="bi bi-folder"></i>
                  <div>
                    <p class="stat-label">STRUCTURE</p>
                    <p class="stat-value">4 Academic Units</p>
                  </div>
                </div>
                <div class="stat-chip">
                  <i class="bi bi-star"></i>
                  <div>
                    <p class="stat-label">RATING</p>
                    <p class="stat-value">4.9 / 5.0</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="hero-media">
              <div class="hero-image-wrap">
                <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=360&fit=crop"
                  alt="Visual Branding Architecture" />
              </div>
            </div>
          </div>
        </section>

        <div class="course-tabs">
          <button class="tab-pill active" type="button">
            <i class="bi bi-clipboard-check"></i>
            <span>Curriculum Map</span>
          </button>
          <button class="tab-pill" type="button">
            <i class="bi bi-file-earmark-text"></i>
            <span>Quiz Recap</span>
          </button>
          <button class="tab-pill" type="button">
            <i class="bi bi-people"></i>
            <span>Enrollment</span>
          </button>
        </div>

        <div class="course-layout">
          <!-- Tab 1: Curriculum Map -->
          <section id="curriculum-map" class="tab-content active">
            <div class="unit-header">
              <p>ACADEMIC UNITS</p>
              <button class="btn-propose" type="button">
                <i class="bi bi-plus"></i> PROPOSE UNIT
              </button>
            </div>

            <div class="unit-card">
              <div class="unit-top">
                <div class="unit-index">01</div>
                <div class="unit-title">
                  <h3>Academic Unit: Module 1</h3>
                  <div class="unit-meta">
                    <span><i class="bi bi-folder"></i> 3 OPERATIONAL ASSETS</span>
                    <span class="unit-status"><i class="bi bi-check-circle-fill"></i> VALIDATED</span>
                  </div>
                </div>
                <button class="unit-toggle" type="button">
                  <i class="bi bi-chevron-down"></i>
                </button>
              </div>
              <div class="unit-assets">
                <div class="asset-mini" data-redirect="content-studio.html?tab=module">
                  <i class="bi bi-file-earmark-pdf"></i>
                  <div>
                    <h4>Lecture Slides</h4>
                    <p>PDF Material</p>
                  </div>
                </div>
                <div class="asset-mini" data-redirect="content-studio.html?tab=module">
                  <i class="bi bi-film"></i>
                  <div>
                    <h4>Session Recording</h4>
                    <p>Video Asset</p>
                  </div>
                </div>
                <div class="asset-mini" data-redirect="content-studio.html?tab=quiz">
                  <i class="bi bi-check-circle"></i>
                  <div>
                    <h4>Unit Assessment</h4>
                    <p>Quiz Engine</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="unit-card compact">
              <div class="unit-top">
                <div class="unit-index index-02">02</div>
                <div class="unit-title">
                  <h3>Academic Unit: Module 2</h3>
                  <div class="unit-meta">
                    <span><i class="bi bi-folder"></i> 3 OPERATIONAL ASSETS</span>
                    <span class="unit-status"><i class="bi bi-check-circle-fill"></i> VALIDATED</span>
                  </div>
                </div>
                <button class="unit-toggle" type="button">
                  <i class="bi bi-chevron-down"></i>
                </button>
              </div>
            </div>

            <div class="unit-card compact">
              <div class="unit-top">
                <div class="unit-index index-03">03</div>
                <div class="unit-title">
                  <h3>Academic Unit: Module 3</h3>
                  <div class="unit-meta">
                    <span><i class="bi bi-folder"></i> 3 OPERATIONAL ASSETS</span>
                    <span class="unit-status"><i class="bi bi-check-circle-fill"></i> VALIDATED</span>
                  </div>
                </div>
                <button class="unit-toggle" type="button">
                  <i class="bi bi-chevron-down"></i>
                </button>
              </div>
            </div>

            <div class="unit-card compact">
              <div class="unit-top">
                <div class="unit-index index-04">04</div>
                <div class="unit-title">
                  <h3>Academic Unit: Module 4</h3>
                  <div class="unit-meta">
                    <span><i class="bi bi-folder"></i> 3 OPERATIONAL ASSETS</span>
                    <span class="unit-status"><i class="bi bi-check-circle-fill"></i> VALIDATED</span>
                  </div>
                </div>
                <button class="unit-toggle" type="button">
                  <i class="bi bi-chevron-down"></i>
                </button>
              </div>
            </div>
          </section>

          <!-- Tab 2: Quiz Recap -->
          <section id="quiz-recap" class="tab-content">
            <div class="recap-stats">
              <div class="stat-box">
                <p class="stat-box-label">CLASS AVERAGE</p>
                <div class="stat-box-content">
                  <h2>84%</h2>
                  <div class="stat-box-icon green">
                    <i class="bi bi-graph-up-arrow"></i>
                  </div>
                </div>
                <p class="stat-box-sub">
                  <i class="bi bi-check-circle-fill"></i> AUTOMATED CALCULATION
                </p>
              </div>
              <div class="stat-box">
                <p class="stat-box-label">TOTAL SUBMISSIONS</p>
                <div class="stat-box-content">
                  <h2>4</h2>
                  <div class="stat-box-icon purple">
                    <i class="bi bi-bar-chart-line"></i>
                  </div>
                </div>
                <p class="stat-box-sub">
                  <i class="bi bi-dot"></i> LIVE FEED REGISTRY
                </p>
              </div>
            </div>

            <div class="grading-registry">
              <div class="registry-header">
                <h3>AUTOMATIC GRADING REGISTRY</h3>
                <button class="export-btn" type="button">
                  <i class="bi bi-download"></i> EXPORT LEDGER
                </button>
              </div>
              <div class="registry-table">
                <div class="table-header">
                  <div class="col-learner">LEARNER</div>
                  <div class="col-submission">SUBMISSION TIME</div>
                  <div class="col-score">SYSTEM SCORE</div>
                  <div class="col-certificate">CERTIFICATE</div>
                </div>
                <div class="table-row">
                  <div class="col-learner">
                    <img src="https://i.pravatar.cc/40?img=1" alt="Alex Rivera" />
                    <div>
                      <h4>Alex Rivera</h4>
                      <p>ID: S1</p>
                    </div>
                  </div>
                  <div class="col-submission">
                    <p>2024-03-15</p>
                    <span>09:45</span>
                  </div>
                  <div class="col-score">
                    <span class="score-bullet green"></span>
                    <strong>80/100</strong>
                  </div>
                  <div class="col-certificate">
                    <span class="badge-issued"><i class="bi bi-check-circle"></i> ISSUED</span>
                  </div>
                </div>
                <div class="table-row">
                  <div class="col-learner">
                    <img src="https://i.pravatar.cc/40?img=5" alt="Jessica Wong" />
                    <div>
                      <h4>Jessica Wong</h4>
                      <p>ID: S2</p>
                    </div>
                  </div>
                  <div class="col-submission">
                    <p>2024-03-15</p>
                    <span>10:20</span>
                  </div>
                  <div class="col-score">
                    <span class="score-bullet green"></span>
                    <strong>100/100</strong>
                  </div>
                  <div class="col-certificate">
                    <span class="badge-issued"><i class="bi bi-check-circle"></i> ISSUED</span>
                  </div>
                </div>
                <div class="table-row">
                  <div class="col-learner">
                    <img src="https://i.pravatar.cc/40?img=3" alt="Marcus Thorne" />
                    <div>
                      <h4>Marcus Thorne</h4>
                      <p>ID: S3</p>
                    </div>
                  </div>
                  <div class="col-submission">
                    <p>2024-03-15</p>
                    <span>11:05</span>
                  </div>
                  <div class="col-score">
                    <span class="score-bullet orange"></span>
                    <strong>60/100</strong>
                  </div>
                  <div class="col-certificate">
                    <span class="badge-pending">PENDING</span>
                  </div>
                </div>
                <div class="table-row">
                  <div class="col-learner">
                    <img src="https://i.pravatar.cc/40?img=9" alt="Elena Rodriguez" />
                    <div>
                      <h4>Elena Rodriguez</h4>
                      <p>ID: S4</p>
                    </div>
                  </div>
                  <div class="col-submission">
                    <p>2024-03-16</p>
                    <span>14:30</span>
                  </div>
                  <div class="col-score">
                    <span class="score-bullet green"></span>
                    <strong>95/100</strong>
                  </div>
                  <div class="col-certificate">
                    <span class="badge-issued"><i class="bi bi-check-circle"></i> ISSUED</span>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Tab 3: Enrollment -->
          <section id="enrollment" class="tab-content">
            <div class="enrollment-header">
              <h3>ENROLLED LEARNERS</h3>
              <span class="total-badge">5 TOTAL</span>
            </div>
            <div class="learner-grid">
              <div class="learner-card">
                <img src="https://i.pravatar.cc/80?img=1" alt="Alex Rivera" />
                <div class="learner-info">
                  <h4>Alex Rivera</h4>
                  <p>ALEX.R@EXAMPLE.COM</p>
                  <span class="learner-date">2024-01-10</span>
                </div>
              </div>
              <div class="learner-card">
                <img src="https://i.pravatar.cc/80?img=5" alt="Jessica Wong" />
                <div class="learner-info">
                  <h4>Jessica Wong</h4>
                  <p>JESS.W@EXAMPLE.COM</p>
                  <span class="learner-date">2024-01-12</span>
                </div>
              </div>
              <div class="learner-card inactive">
                <img src="https://i.pravatar.cc/80?img=3" alt="Marcus Thorne" />
                <div class="learner-info">
                  <h4>Marcus Thorne</h4>
                  <p>MARCUS.T@EXAMPLE.COM</p>
                  <span class="learner-date">2024-01-15</span>
                </div>
                <span class="inactive-badge">CHURN</span>
              </div>
              <div class="learner-card inactive">
                <img src="https://i.pravatar.cc/80?img=9" alt="Elena Rodriguez" />
                <div class="learner-info">
                  <h4>Elena Rodriguez</h4>
                  <p>ELENA.R@EXAMPLE.COM</p>
                  <span class="learner-date">2024-01-20</span>
                </div>
                <span class="inactive-badge">CHURN</span>
              </div>
              <div class="learner-card">
                <img src="https://i.pravatar.cc/80?img=7" alt="David Kim" />
                <div class="learner-info">
                  <h4>David Kim</h4>
                  <p>DAVID.K@EXAMPLE.COM</p>
                  <span class="learner-date">2024-02-01</span>
                </div>
              </div>
            </div>
          </section>

          <aside class="course-right">
            <div class="grading-card">
              <div class="grading-head">
                <i class="bi bi-lightning-fill grading-icon"></i>
                <p>GRADING PROTOCOL</p>
              </div>
              <div class="grading-status">
                <p>Oversight Status</p>
                <h4>Active Automated</h4>
              </div>
              <ul class="grading-notes">
                <li>System automatically calculates percentage scores.</li>
                <li>Manual overrides are disabled for audit compliance.</li>
              </ul>
              <button class="grading-btn" type="button">View Audit Ledger</button>
            </div>

            <div class="instructor-card">
              <p class="instructor-title">INSTRUCTOR HUB</p>
              <div class="instructor-item">
                <span class="dot"></span>
                <div>
                  <h4>Submit Assets</h4>
                  <p>Pedagogical Materials</p>
                </div>
              </div>
            </div>
          </aside>
        </div>
      </main>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const navbar = document.querySelector("navbar, .navbar");
        if (
          navbar &&
          navbar.parentNode.classList &&
          !navbar.parentNode.classList.contains("main-wrapper")
        ) {
          const mainWrapper = document.querySelector(".main-wrapper");
          if (mainWrapper) {
            mainWrapper.insertBefore(navbar, mainWrapper.firstChild);
          }
        }

        // Tab Switching
        const tabButtons = document.querySelectorAll(".tab-pill");
        const tabContents = document.querySelectorAll(".tab-content");

        tabButtons.forEach((button, index) => {
          button.addEventListener("click", () => {
            // Remove active class from all buttons and contents
            tabButtons.forEach((btn) => btn.classList.remove("active"));
            tabContents.forEach((content) =>
              content.classList.remove("active"),
            );

            // Add active class to clicked button and corresponding content
            button.classList.add("active");
            tabContents[index].classList.add("active");
          });
        });

        // Unit Card Expand/Collapse
        const unitCards = document.querySelectorAll(".unit-card");
        const unitToggles = document.querySelectorAll(".unit-toggle");

        const toggleUnitCard = (unitCard) => {
          const unitAssets = unitCard.querySelector(".unit-assets");
          const icon = unitCard.querySelector(".unit-toggle i");

          if (unitAssets && !unitCard.classList.contains("compact")) {
            unitCard.classList.toggle("collapsed");

            if (unitCard.classList.contains("collapsed")) {
              unitAssets.style.display = "none";
              icon.style.transform = "rotate(0deg)";
            } else {
              unitAssets.style.display = "grid";
              icon.style.transform = "rotate(180deg)";
            }
          } else {
            unitCard.classList.toggle("expanded");

            if (unitCard.classList.contains("expanded")) {
              if (!unitAssets) {
                const assetsDiv = document.createElement("div");
                assetsDiv.className = "unit-assets";
                assetsDiv.innerHTML = `
                  <div class="asset-mini" data-redirect="content-studio.html?tab=module">
                    <i class="bi bi-file-earmark-pdf"></i>
                    <div>
                      <h4>Lecture Slides</h4>
                      <p>PDF Material</p>
                    </div>
                  </div>
                  <div class="asset-mini" data-redirect="content-studio.html?tab=module">
                    <i class="bi bi-film"></i>
                    <div>
                      <h4>Session Recording</h4>
                      <p>Video Asset</p>
                    </div>
                  </div>
                  <div class="asset-mini" data-redirect="content-studio.html?tab=quiz">
                    <i class="bi bi-check-circle"></i>
                    <div>
                      <h4>Unit Assessment</h4>
                      <p>Quiz Engine</p>
                    </div>
                  </div>
                `;
                unitCard.appendChild(assetsDiv);
              }
              icon.style.transform = "rotate(180deg)";
            } else {
              const assetsDiv = unitCard.querySelector(".unit-assets");
              if (assetsDiv && unitCard.classList.contains("compact")) {
                assetsDiv.remove();
              }
              icon.style.transform = "rotate(0deg)";
            }
          }
        };

        unitToggles.forEach((toggle) => {
          toggle.addEventListener("click", (event) => {
            event.stopPropagation();
            const unitCard = toggle.closest(".unit-card");
            toggleUnitCard(unitCard);
          });
        });

        unitCards.forEach((unitCard) => {
          unitCard.addEventListener("click", (event) => {
            if (event.target.closest(".unit-assets")) return;
            const unitCardEl = event.currentTarget;
            toggleUnitCard(unitCardEl);
          });
        });

        document.addEventListener("click", (event) => {
          const assetCard = event.target.closest(".asset-mini[data-redirect]");
          if (!assetCard) return;

          event.preventDefault();
          event.stopPropagation();

          const targetPath = assetCard.getAttribute("data-redirect");
          if (targetPath) {
            window.location.href = targetPath;
          }
        });
      });
    </script>
  </body>

  <script type="text/javascript" src="/assets/js/components/sidebar.js" defer></script>

</html>