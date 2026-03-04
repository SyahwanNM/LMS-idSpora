<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>idSpora E-Learning</title>
  @vite(['resources/css/app.css', 'resources/css/trainer/main.css'])
  <link rel="stylesheet" href="/assets/css/pages.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
</head>
<style>
  /* Profile Page Specific Styles */

  main {
    padding: var(--spacing-2xl);
    background-color: var(--base-clr);
    overflow-y: auto;
    width: 100%;
  }

  .trainer-page main {
    margin: 0;
    padding: var(--spacing-2xl);
  }

  /* Header Sections */
  .top-content {
    margin: 0 0 var(--spacing-xl) 0;
    position: relative;
    border-radius: var(--radius-2xl);
    padding: var(--spacing-xl) var(--spacing-2xl);
    background-color: var(--main-navy-clr);
    box-shadow: 0 10px 32px rgba(11, 9, 38, 0.75);
    color: var(--white-clr);
    overflow: hidden;
  }

  .top-content::before {
    content: "";
    position: absolute;
    inset: 0;
    background: url("https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1600") center/cover no-repeat;
    opacity: 0.75;
  }

  .top-content::after {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg,
        rgba(20, 18, 68, 0.98),
        rgba(27, 23, 99, 0.85),
        rgba(27, 23, 99, 0.55));
  }

  .top-content-inner {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: var(--spacing-lg);
  }

  /* Profile Section */
  .profile-header {
    display: flex;
    align-items: center;
    gap: var(--spacing-xl);
  }

  .profile-left {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
  }

  .profile-photo {
    position: relative;
    flex-shrink: 0;
  }

  .profile-photo img {
    width: 80px;
    height: 80px;
    border-radius: var(--radius-lg);
    border: 3px solid var(--white-clr);
    object-fit: cover;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35);
  }

  .photo-badge {
    position: absolute;
    right: -6px;
    bottom: -6px;
    width: 24px;
    height: 24px;
    background: var(--yellow-clr);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
  }

  .level-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
    background: var(--yellow-background-clr);
    color: var(--yellow-clr);
    border: 1px solid rgba(251, 197, 49, 0.45);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: 16px;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.4px;
    margin-bottom: var(--spacing-md);
  }

  .profile-text h2 {
    margin: 0;
    font-size: 18px;
    line-height: var(--line-height-tight);
    color: var(--white-clr);
  }

  .profile-text .role {
    margin: var(--spacing-sm) 0 var(--spacing-md) 0;
    font-size: var(--font-size-base);
    color: rgba(255, 255, 255, 0.8);
  }

  .info {
    display: flex;
    flex-direction: row;
    gap: var(--spacing-lg);
    margin-top: var(--spacing-xs);
    flex-wrap: wrap;
  }

  .loc-mail {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: var(--font-size-xs);
    letter-spacing: 0.4px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.75);
  }

  .loc-mail svg {
    color: var(--yellow-clr);
  }

  .profile-actions {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
  }

  .profile-actions button {
    border: none;
    cursor: pointer;
  }

  .btn-configure {
    background: var(--white-clr);
    color: var(--main-navy-clr);
    padding: var(--spacing-md) var(--spacing-xl);
    border-radius: var(--radius-lg);
    font-weight: 700;
    font-size: var(--font-size-sm);
    letter-spacing: 0.5px;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    transition: all 0.2s ease;
  }

  .btn-configure:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
  }

  .btn-share {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-lg);
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white-clr);
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .btn-share:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
  }

  /* Records/Cards */
  .trainer-record,
  .record-card {
    background: var(--white-clr);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-lg);
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    border: 1px solid var(--line-clr);
  }

  .info-record {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-lg);
  }

  .detail-record {
    background: var(--base-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-md);
    text-align: center;
    box-shadow: inset 0 0 0 1px var(--line-clr);
  }

  .detail-record p {
    margin: 0;
    font-size: var(--font-size-xs);
    letter-spacing: 0.6px;
    color: var(--gray-clr);
    font-weight: 700;
    text-transform: uppercase;
  }

  .detail-record h2 {
    margin: var(--spacing-sm) 0 0 0;
    font-size: 16px;
    color: var(--main-navy-clr);
    display: inline-flex;
    align-items: center;
    gap: var(--spacing-xs);
  }

  .detail-record .score-star {
    color: #facc15;
  }

  .trainer-record h4,
  .record-card h4 {
    margin: var(--spacing-md) 0 var(--spacing-md) 0;
    font-size: var(--font-size-xs);
    letter-spacing: 0.6px;
    color: var(--secondary-text-clr);
    text-transform: uppercase;
  }

  /* Expertise/Skills */
  .expertise-list {
    display: flex;
    flex-wrap: wrap;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-lg);
  }

  .expertise-list span,
  .skill-tag {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    padding: var(--spacing-xs) var(--spacing-md);
    border-radius: 20px;
    font-size: var(--font-size-xs);
    font-weight: 700;
    letter-spacing: 0.3px;
  }

  /* Social Media */
  .info-socmed {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    padding-top: var(--spacing-lg);
    border-top: 1px solid var(--line-clr);
  }

  .info-socmed a {
    width: 34px;
    height: 34px;
    border-radius: var(--radius-lg);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--base-clr);
    color: var(--main-navy-clr);
    transition: all 0.2s ease;
    text-decoration: none;
  }

  .info-socmed a:hover {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    transform: translateY(-2px);
  }

  /* Course/Content Cards */
  .card-course,
  .cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: var(--spacing-xl);
    padding: var(--spacing-lg);
  }

  .card-item,
  .content-card {
    background-color: var(--white-clr);
    border-radius: var(--radius-2xl);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    transition: all 0.3s ease;
    position: relative;
  }

  .card-item:hover,
  .content-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
  }

  /* Page Header */
  .top-page {
    margin: 0 0 var(--spacing-2xl) 0;
    color: var(--main-navy-clr);
    padding: 0;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
  }

  /* Search and Filter */
  .search-filter-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-right: var(--spacing-lg);
    gap: var(--spacing-md);
    flex-wrap: wrap;
  }

  .search-column,
  .filter-bar {
    background-color: var(--white-clr);
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: var(--radius-lg);
    border: 1px solid var(--line-clr);
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
  }

  .search-column:hover,
  .search-column:focus-within,
  .filter-bar:hover {
    border-color: var(--main-navy-clr);
    box-shadow: 0 4px 12px rgba(45, 27, 105, 0.15);
  }

  .search-column {
    width: auto;
    max-width: 280px;
  }

  .search-column input,
  .filter-bar input {
    border: none;
    outline: none;
    flex: 1;
    font-size: var(--font-size-base);
    color: var(--gray-clr);
    background: transparent;
  }

  .search-column input::placeholder,
  .filter-bar input::placeholder {
    color: #999;
  }

  .search-column svg,
  .filter-bar svg {
    color: var(--gray-clr);
    flex-shrink: 0;
  }

  .filter-bar {
    width: auto;
    cursor: pointer;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .top-content {
      margin: 0 0 var(--spacing-2xl) 0;
    }

    .top-page {
      margin: 0 0 var(--spacing-lg) 0;
    }

    .card-course,
    .cards-container {
      grid-template-columns: repeat(2, 1fr);
      gap: var(--spacing-lg);
    }

    .info-record {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .top-content {
      margin: 0 0 var(--spacing-lg) 0;
      padding: var(--spacing-lg);
    }

    .top-content-inner {
      flex-direction: column;
      gap: var(--spacing-lg);
    }

    .profile-header {
      flex-direction: column;
      align-items: flex-start;
      gap: var(--spacing-lg);
    }

    .profile-actions {
      width: 100%;
      justify-content: flex-start;
    }

    .profile-text h2 {
      font-size: 16px;
    }

    .card-course,
    .cards-container {
      grid-template-columns: repeat(2, 1fr);
      gap: var(--spacing-lg);
      padding: var(--spacing-lg);
    }

    .search-filter-bar {
      margin-right: var(--spacing-lg);
      flex-direction: column;
      align-items: stretch;
    }

    .search-column {
      max-width: none;
    }

    .info-record {
      grid-template-columns: 1fr;
      gap: var(--spacing-md);
    }

    .top-page {
      flex-direction: column;
      margin: 0 0 var(--spacing-lg) 0;
    }
  }

  @media (max-width: 600px) {
    .top-content {
      margin: 0 0 var(--spacing-lg) 0;
      padding: var(--spacing-lg);
      border-radius: var(--radius-lg);
    }

    .top-content-inner {
      flex-direction: column;
      gap: var(--spacing-md);
    }

    .profile-header {
      flex-direction: column;
      gap: var(--spacing-md);
    }

    .profile-left {
      gap: var(--spacing-md);
    }

    .profile-photo img {
      width: 70px;
      height: 70px;
      border-radius: var(--radius-lg);
    }

    .profile-text h2 {
      font-size: 16px;
    }

    .profile-text .role {
      font-size: var(--font-size-sm);
    }

    .profile-actions {
      width: 100%;
      gap: var(--spacing-sm);
    }

    .btn-configure {
      flex: 1;
      padding: var(--spacing-sm) var(--spacing-md);
      font-size: var(--font-size-xs);
    }

    .btn-share {
      width: 32px;
      height: 32px;
    }

    .card-course,
    .cards-container {
      grid-template-columns: 1fr;
      gap: var(--spacing-md);
      padding: var(--spacing-md);
    }

    .trainer-record,
    .record-card {
      padding: var(--spacing-md);
      margin: var(--spacing-md);
    }

    .info-record {
      grid-template-columns: 1fr;
      gap: var(--spacing-sm);
      margin-bottom: var(--spacing-md);
    }

    .detail-record {
      padding: var(--spacing-sm);
    }

    .detail-record p {
      font-size: var(--font-size-xs);
    }

    .detail-record h2 {
      font-size: 14px;
    }

    .expertise-list {
      gap: var(--spacing-xs);
      margin-bottom: var(--spacing-md);
    }

    .expertise-list span,
    .skill-tag {
      padding: 4px 8px;
      font-size: 9px;
    }

    .search-filter-bar {
      margin-right: var(--spacing-md);
      gap: var(--spacing-sm);
    }

    .search-column,
    .filter-bar {
      padding: var(--spacing-sm) var(--spacing-md);
      gap: var(--spacing-sm);
    }

    .search-column input,
    .filter-bar input {
      font-size: var(--font-size-sm);
    }

    .top-page {
      margin: 0 0 var(--spacing-lg) 0;
      flex-direction: column;
    }

    .info-socmed {
      gap: var(--spacing-md);
      padding-top: var(--spacing-md);
    }

    .info-socmed a {
      width: 30px;
      height: 30px;
    }
  }

  .profile-dashboard {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: var(--spacing-xl);
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
  }

  .dashboard-sidebar {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
  }

  /* Stats Card (deprecated - use profile-info-card instead) */
  .stats-card {
    background: var(--white-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-xl);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: var(--spacing-xl);
  }

  .stat-item {
    flex: 1;
  }

  .stat-label {
    display: block;
    font-size: 8px;
    font-weight: 700;
    color: var(--text-secondary);
    letter-spacing: 0.8px;
    margin-bottom: var(--spacing-xs);
    text-transform: uppercase;
  }

  .stat-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
  }

  .stat-star {
    width: 16px;
    height: 16px;
  }

  .stat-divider {
    width: 1px;
    height: 40px;
    background: var(--line-clr);
  }

  /* Expertise Card */
  .expertise-card {
    background: var(--white-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .section-title {
    font-size: 6px;
    font-weight: 700;
    color: var(--text-secondary);
    letter-spacing: 0.8px;
    margin: 0 0 var(--spacing-sm) 0;
    text-transform: uppercase;
  }

  .expertise-pills {
    display: flex;
    gap: var(--spacing-md);
    flex-wrap: wrap;
  }

  .expertise-pill {
    display: inline-block;
    padding: 4px 8px;
    background: var(--main-navy-clr);
    color: var(--white-clr);
    border-radius: var(--radius-md);
    font-size: 8px;
    font-weight: 700;
    letter-spacing: 0.5px;
  }

  /* Network Card */
  .network-card {
    background: var(--white-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .network-icons {
    display: flex;
    gap: var(--spacing-md);
    justify-content: flex-start;
  }

  .network-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    background: #f8f9fa;
    color: var(--text-tertiary);
    transition: all 0.3s ease;
    text-decoration: none;
  }

  .network-icon:hover {
    background: var(--main-navy-clr);
    color: var(--white-clr);
    transform: translateY(-2px);
  }

  /* Combined Profile Info Card */
  .profile-info-card {
    background: var(--white-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .stats-section {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
  }

  .stats-section .stat-item {
    flex: 1;
  }

  .stats-section .stat-label {
    display: block;
    font-size: 8px;
    font-weight: 700;
    color: var(--text-secondary);
    letter-spacing: 0.8px;
    margin-bottom: var(--spacing-xs);
    text-transform: uppercase;
  }

  .stats-section .stat-value {
    font-size: 16px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
  }

  .info-divider {
    height: 1px;
    background: var(--line-clr);
    margin: var(--spacing-md) 0;
  }

  .expertise-section {
    padding: 0;
  }

  .expertise-section .section-title {
    margin-bottom: var(--spacing-md);
  }

  .network-section {
    padding: 0;
  }

  .network-section .section-title {
    margin-bottom: var(--spacing-md);
  }

  /* Reward Card */
  .reward-card {
    background: var(--main-navy-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.3);
    color: var(--white-clr);
    display: flex;
    flex-direction: column;
    gap: var(--spacing-md);
  }

  .reward-title {
    color: var(--yellow-clr);
    margin: 0 0 var(--spacing-xs) 0;
  }

  .reward-content {
    flex: 1;
  }

  .reward-label {
    font-size: 8px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.65);
    letter-spacing: 0.8px;
    margin: 0 0 var(--spacing-sm) 0;
    text-transform: uppercase;
  }

  .reward-amount {
    font-size: 20px;
    font-weight: 800;
    margin: 0;
    color: var(--white-clr);
    display: flex;
    align-items: baseline;
    gap: 2px;
    letter-spacing: -1px;
  }

  .reward-cents {
    font-size: 16px;
    color: var(--yellow-clr);
    font-weight: 700;
    opacity: 0.95;
  }

  .btn-view-records {
    width: 100%;
    padding: var(--spacing-md) var(--spacing-lg);
    background: rgba(255, 255, 255, 0.12);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-md);
    color: var(--white-clr);
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--spacing-sm);
  }

  .btn-view-records i {
    font-size: 12px;
  }

  .btn-view-records:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
  }

  /* Upcoming Schedule Card */
  .schedule-card {
    background: var(--white-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .schedule-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-md);
  }

  .schedule-title-main {
    font-size: 8px;
    font-weight: 700;
    color: var(--text-secondary);
    letter-spacing: 0.8px;
    margin: 0;
    text-transform: uppercase;
  }

  .schedule-header svg {
    color: var(--main-navy-clr);
    opacity: 0.6;
  }

  .schedule-header i {
    color: var(--main-navy-clr);
    opacity: 0.6;
    font-size: 20px;
  }

  .schedule-list {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
    margin-bottom: var(--spacing-md);
  }

  .schedule-item-link {
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
  }

  .schedule-item {
    padding: var(--spacing-md);
    background: #f8f9fa;
    border-radius: var(--radius-lg);
    transition: all 0.3s ease;
  }

  .schedule-item-link:hover .schedule-item {
    background: var(--main-navy-clr);
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
  }

  .schedule-badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.5px;
    margin-bottom: var(--spacing-sm);
    text-transform: uppercase;
  }

  .schedule-badge.workshop {
    background: #fbbf24;
    color: #000;
  }

  .schedule-badge.one-on-one {
    background: #fbbf24;
    color: #000;
  }

  .schedule-item-link:hover .schedule-badge {
    background: rgba(255, 255, 255, 0.25);
    color: var(--white-clr);
  }

  .schedule-item-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0 0 var(--spacing-xs) 0;
  }

  .schedule-item-link:hover .schedule-item-title {
    color: var(--white-clr);
  }

  .schedule-item-meta {
    font-size: 10px;
    color: var(--text-secondary);
    margin: 0 0 4px 0;
    font-weight: 500;
  }

  .schedule-item-link:hover .schedule-item-meta {
    color: rgba(255, 255, 255, 0.8);
  }

  .schedule-item-time {
    font-size: 12px;
    color: var(--text-tertiary);
    margin: 0;
  }

  .schedule-item-link:hover .schedule-item-time {
    color: rgba(255, 255, 255, 0.7);
  }

  .schedule-manage-link {
    display: block;
    text-align: center;
    padding: var(--spacing-sm);
    font-size: 10px;
    font-weight: 700;
    color: var(--main-navy-clr);
    text-decoration: none;
    letter-spacing: 0.5px;
    transition: color 0.2s ease;
  }

  .schedule-manage-link:hover {
    color: var(--brand-purple);
  }

  .dashboard-content {
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: var(--spacing-lg);
  }

  /* Pedagogical Statement */
  .pedagogical-statement {
    background: var(--white-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 2px solid transparent;
  }

  .pedagogical-statement:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-color: var(--main-navy-clr);
  }

  .statement-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-md);
  }

  .statement-title {
    font-size: 9px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
  }

  .statement-title svg {
    color: #fbbf24;
  }

  .btn-edit-statement {
    padding: 8px;
    background: transparent;
    border: none;
    border-radius: 8px;
    color: var(--main-navy-clr);
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
  }

  .btn-edit-statement:hover {
    background: #fbbf24;
    color: var(--main-navy-clr);
    border-color: transparent;
    box-shadow: 0 2px 8px rgba(251, 191, 36, 0.2);
  }

  .statement-text {
    font-size: 13px;
    line-height: 1.5;
    color: var(--text-primary);
    margin: 0;
  }

  /* Course Portfolio */
  .portfolio-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-md);
    padding: 0 var(--spacing-sm);
  }

  .portfolio-title {
    font-size: 9px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
  }

  .portfolio-title svg {
    color: var(--brand-purple);
  }

  .view-all {
    font-size: 10px;
    font-weight: 700;
    color: var(--main-navy-clr);
    text-decoration: none;
    letter-spacing: 0.5px;
    transition: color 0.2s ease;
  }

  .view-all:hover {
    color: var(--brand-purple);
  }

  .course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
  }

  .course-card-item {
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: transparent;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition:
      transform 0.3s ease,
      box-shadow 0.3s ease;
  }

  .course-card-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
  }

  .course-card-item:hover .course-title {
    color: #fbbf24;
  }

  .course-card-item:hover .course-info {
    background: var(--white-clr);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .course-card-item:hover .course-bottom {
    background: var(--white-clr);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .course-image {
    width: 100%;
    height: 160px;
    object-fit: cover;
  }

  .course-info {
    padding: var(--spacing-lg);
    background: var(--white-clr);
    transition: background 0.3s ease;
  }

  .course-meta {
    display: flex;
    align-items: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-sm);
  }

  .course-rating {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    font-weight: 600;
    color: var(--main-navy-clr);
  }

  .course-learners {
    font-size: 9px;
    font-weight: 600;
    color: var(--text-tertiary);
    letter-spacing: 0.5px;
  }
  }

  .course-title {
    font-size: 11px;
    font-weight: 600;
    color: var(--main-navy-clr);
    margin: 0 0 var(--spacing-sm) 0;
    transition: color 0.3s ease;
  }

  .course-bottom {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    justify-content: space-between;
    padding: var(--spacing-md) var(--spacing-lg);
    background: #f8f9fa;
    transition: background 0.3s ease;
  }

  .course-level {
    display: inline-block;
    padding: 4px 10px;
    border-radius: var(--radius-sm);
    font-size: 9px;
    font-weight: 700;
    letter-spacing: 0.5px;
  }

  .course-modules {
    font-size: 9px;
    font-weight: 600;
    color: var(--text-tertiary);
    letter-spacing: 0.5px;
  }

  .course-arrow {
    font-size: 16px;
    color: var(--main-navy-clr);
    transition: transform 0.2s ease;
  }

  .course-card-item:hover .course-arrow {
    transform: translateX(4px);
  }

  .course-level.intermediate {
    background: #fef3c7;
    color: #92400e;
  }

  .course-level.advanced {
    background: #fee2e2;
    color: #991b1b;
  }

  /* Student Feedback */
  .student-feedback {
    background: var(--white-clr);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
  }

  .student-feedback:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .feedback-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: var(--spacing-md);
  }

  .feedback-title {
    font-size: 12px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
  }

  .feedback-title svg {
    color: var(--brand-purple);
  }

  .feedback-rating {
    display: flex;
    align-items: center;
    gap: var(--spacing-xs);
    font-size: 12px;
    font-weight: 700;
    color: var(--main-navy-clr);
  }

  .feedback-list {
    display: grid;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-md);
  }

  .feedback-item {
    padding: var(--spacing-md);
    background: #f8f9fa;
    border-radius: var(--radius-lg);
    border-left: 4px solid #fbbf24;
    transition: all 0.3s ease;
  }

  .feedback-item:hover {
    background: #f0f1f3;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  }

  .feedback-stars {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: var(--spacing-sm);
  }

  .feedback-time {
    margin-left: var(--spacing-sm);
    font-size: 10px;
    font-weight: 600;
    color: var(--text-tertiary);
    letter-spacing: 0.5px;
  }

  .feedback-text {
    font-size: 12px;
    line-height: 1.5;
    color: var(--text-primary);
    margin: 0 0 var(--spacing-md) 0;
    position: relative;
    padding-left: var(--spacing-md);
  }

  .feedback-text::before {
    content: '"';
    position: absolute;
    left: 0;
    top: -5px;
    font-size: 24px;
    color: #fbbf24;
    opacity: 0.5;
  }

  .feedback-author {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
  }

  .author-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--main-navy-clr);
    color: var(--white-clr);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
  }

  .author-name {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-secondary);
    letter-spacing: 0.5px;
  }

  .view-all-reviews {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: var(--spacing-sm);
    font-size: 10px;
    font-weight: 700;
    color: var(--main-navy-clr);
    text-decoration: none;
    letter-spacing: 0.5px;
    transition: color 0.2s ease;
  }

  .view-all-reviews:hover {
    color: var(--brand-purple);
  }

  .view-all-reviews i {
    font-size: 12px;
    transition: transform 0.2s ease;
  }

  .view-all-reviews:hover i {
    transform: translateX(4px);
  }

  /* ============================================
   RESPONSIVE DESIGN
   ============================================ */

  @media (max-width: 1200px) {
    .profile-dashboard {
      grid-template-columns: 280px 1fr;
      gap: var(--spacing-lg);
    }
  }

  @media (max-width: 992px) {
    .profile-dashboard {
      grid-template-columns: 1fr;
    }

    .course-grid {
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    }
  }

  @media (max-width: 768px) {
    .stats-card {
      flex-direction: column;
      gap: var(--spacing-lg);
    }

    .stat-divider {
      width: 50px;
      height: 1px;
    }

    .course-grid {
      grid-template-columns: 1fr;
    }

    .statement-header,
    .portfolio-header,
    .feedback-header {
      flex-direction: column;
      align-items: flex-start;
      gap: var(--spacing-md);
    }
  }
</style>

<body>
  @include('partials.navbar-before-login')
  <div class="trainer-page">
    @include('trainer.partials.sidebar', ['activeMenu' => 'profile'])

    <main>
      <!-- Top Profile Section -->
      <section class="top-content">
        <div class="top-content-inner">
          <div class="profile-left">
            <div class="profile-photo">
              <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=300"
                alt="Sarah Jenkins" />
              <span class="photo-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#1b1763" viewBox="0 0 24 24">
                  <path
                    d="M12 2l7 4v6c0 5-3.5 9-7 10-3.5-1-7-5-7-10V6l7-4zm0 4.5L8 8v4.2c0 3.2 2.1 6 4 6.8 1.9-.8 4-3.6 4-6.8V8l-4-1.5z" />
                </svg>
              </span>
            </div>

            <div class="profile-text">
              <div class="level-badge">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                  <path
                    d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                MASTER LEVEL ACADEMIC
              </div>
              <h2>Sarah Jenkins</h2>
              <p class="role">Senior Product Design Specialist</p>
              <div class="info">
                <div class="loc-mail">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path
                      d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                    <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                  </svg>
                  <span>NEW YORK, USA</span>
                </div>
                <div class="loc-mail">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path
                      d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                  </svg>
                  <span>SARAH.J@IDSPORA.COM</span>
                </div>
              </div>
            </div>
          </div>

          <div class="profile-actions">
            <button class="btn-configure">CONFIGURE PROFILE</button>
            <button class="btn-share" aria-label="Share">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path
                  d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5m-8.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m11 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3" />
              </svg>
            </button>
          </div>
        </div>
      </section>

      <div class="profile-dashboard">
        <!-- Left Sidebar -->
        <aside class="dashboard-sidebar">
          <!-- Combined Info Card: Stats + Expertise + Network -->
          <div class="profile-info-card">
            <!-- Stats Section -->
            <div class="stats-section">
              <div class="stat-item">
                <span class="stat-label">GLOBAL LEARNERS</span>
                <h2 class="stat-value">1,250</h2>
              </div>
              <div class="stat-divider"></div>
              <div class="stat-item">
                <span class="stat-label">QUALITY SCORE</span>
                <h2 class="stat-value">
                  4.8
                  <svg class="stat-star" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#fbbf24"
                    viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                </h2>
              </div>
            </div>

            <div class="info-divider"></div>

            <!-- Expertise Stack Section -->
            <div class="expertise-section">
              <h3 class="section-title">EXPERTISE STACK</h3>
              <div class="expertise-pills">
                <span class="expertise-pill">FIGMA</span>
                <span class="expertise-pill">SYSTEM DESIGN</span>
              </div>
            </div>

            <div class="info-divider"></div>

            <!-- Network Tunnels Section -->
            <div class="network-section">
              <h3 class="section-title">NETWORK TUNNELS</h3>
              <div class="network-icons">
                <a href="#" class="network-icon" aria-label="LinkedIn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path
                      d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z" />
                  </svg>
                </a>
                <a href="#" class="network-icon" aria-label="Website">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path
                      d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-4.468 12.39c.69-1.2 1.92-2.39 3.468-2.39 1.55 0 2.78 1.19 3.47 2.39A7 7 0 0 0 8 1m0 1c-1.44 0-2.71 1.13-3.44 2.78A7.8 7.8 0 0 0 4 8c0 1.12.2 2.18.56 3.22C5.29 12.87 6.56 14 8 14s2.71-1.13 3.44-2.78A7.8 7.8 0 0 0 12 8c0-1.12-.2-2.18-.56-3.22C10.71 3.13 9.44 2 8 2" />
                  </svg>
                </a>
                <a href="#" class="network-icon" aria-label="Twitter">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path
                      d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334q0-.213-.006-.425A6.7 6.7 0 0 0 16 3.542a6.6 6.6 0 0 1-1.889.518 3.3 3.3 0 0 0 1.447-1.817 6.6 6.6 0 0 1-2.087.793A3.28 3.28 0 0 0 7.88 6.03a9.3 9.3 0 0 1-6.766-3.43 3.28 3.28 0 0 0 1.015 4.381A3.3 3.3 0 0 1 .64 6.575v.045A3.28 3.28 0 0 0 3.277 9.84a3.3 3.3 0 0 1-1.482.056 3.28 3.28 0 0 0 3.064 2.277A6.58 6.58 0 0 1 .78 13.58 6.6 6.6 0 0 1 0 13.54a9.29 9.29 0 0 0 5.026 1.47" />
                  </svg>
                </a>
                <a href="#" class="network-icon" aria-label="GitHub">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path
                      d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z" />
                  </svg>
                </a>
              </div>
            </div>
          </div>

          <!-- Reward Ledger -->
          <div class="reward-card">
            <h3 class="section-title reward-title">REWARD LEDGER</h3>
            <div class="reward-content">
              <p class="reward-label">GROSS EARNINGS</p>
              <h2 class="reward-amount">
                $12,450<span class="reward-cents">.00</span>
              </h2>
            </div>
            <button class="btn-view-records">
              <i class="bi bi-window-stack"></i>
              VIEW PAYMENT RECORDS
            </button>
          </div>

          <!-- Upcoming Schedule -->
          <div class="schedule-card">
            <div class="schedule-header">
              <h3 class="schedule-title-main">UPCOMING SCHEDULE</h3>
              <i class="bi bi-calendar3"></i>
            </div>

            <div class="schedule-list">
              <!-- Schedule Item 1 -->
              <a href="/pages/detail-course.html" class="schedule-item-link">
                <div class="schedule-item">
                  <div class="schedule-badge workshop">WORKSHOP</div>
                  <h4 class="schedule-item-title">Advanced UI Patterns</h4>
                  <p class="schedule-item-meta">45 Learners Enrolled</p>
                  <p class="schedule-item-time">Tomorrow, 10:00 AM</p>
                </div>
              </a>

              <!-- Schedule Item 2 -->
              <a href="/pages/detail-course.html" class="schedule-item-link">
                <div class="schedule-item">
                  <div class="schedule-badge workshop">WORKSHOP</div>
                  <h4 class="schedule-item-title">Design Systems Workshop</h4>
                  <p class="schedule-item-meta">28 Learners Enrolled</p>
                  <p class="schedule-item-time">Wed, 2:00 PM</p>
                </div>
              </a>

              <!-- Schedule Item 3 -->
              <a href="/pages/detail-course.html" class="schedule-item-link">
                <div class="schedule-item">
                  <div class="schedule-badge one-on-one">1-ON-1</div>
                  <h4 class="schedule-item-title">Portfolio Review</h4>
                  <p class="schedule-item-meta">1 Learners Enrolled</p>
                  <p class="schedule-item-time">Fri, 4:30 PM</p>
                </div>
              </a>
            </div>

            <a href="/pages/dashboard.html" class="schedule-manage-link">
              MANAGE FULL CALENDAR
            </a>
          </div>
        </aside>

        <!-- Right Content Area -->
        <div class="dashboard-content">
          <!-- Pedagogical Statement -->
          <div class="pedagogical-statement">
            <div class="statement-header">
              <h2 class="statement-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                  <path
                    d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                </svg>
                Pedagogical Statement
              </h2>
              <button class="btn-edit-statement" aria-label="Edit Statement">
                <i class="bi bi-pencil"></i>
              </button>
            </div>
            <p class="statement-text">
              Transforming complex problems into elegant solutions.
            </p>
          </div>

          <!-- Active Course Portfolio -->
          <div class="portfolio-header">
            <h2 class="portfolio-title">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path
                  d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm15 2h-4v3h4zm0 4h-4v3h4zm0 4h-4v3h3a1 1 0 0 0 1-1zm-5 3v-3H6v3zm-5 0v-3H1v2a1 1 0 0 0 1 1zm-4-4h4V8H1zm0-4h4V4H1zm5-3v3h4V4zm4 4H6v3h4z" />
              </svg>
              ACTIVE COURSE PORTFOLIO
            </h2>
            <a href="course.html" class="view-all">VIEW ALL</a>
          </div>
          <div class="course-grid">
            <div class="course-card-item">
              <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&q=80&w=600"
                alt="Visual Branding Architecture" class="course-image" />
              <div class="course-info">
                <div class="course-meta">
                  <span class="course-rating">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                    4.9
                  </span>
                  <span class="course-learners">850 LEARNERS</span>
                </div>
                <h3 class="course-title">Visual Branding Architecture</h3>
                <span class="course-level intermediate">INTERMEDIATE</span>
              </div>
              <div class="course-bottom">
                <span class="course-modules">4 MODULES</span>
                <i class="bi bi-arrow-right course-arrow"></i>
              </div>
            </div>
            <div class="course-card-item">
              <img src="https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&q=80&w=600"
                alt="Advanced AI Architectures" class="course-image" />
              <div class="course-info">
                <div class="course-meta">
                  <span class="course-rating">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                    4.7
                  </span>
                  <span class="course-learners">420 LEARNERS</span>
                </div>
                <h3 class="course-title">Advanced AI Architectures</h3>
                <span class="course-level advanced">ADVANCED</span>
              </div>
              <div class="course-bottom">
                <span class="course-modules">6 MODULES</span>
                <i class="bi bi-arrow-right course-arrow"></i>
              </div>
            </div>
          </div>

          <!-- Recent Student Feedback -->
          <div class="student-feedback">
            <div class="feedback-header">
              <h2 class="feedback-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                  <path
                    d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z" />
                </svg>
                Recent Student Feedback
              </h2>
              <span class="feedback-rating">
                4.8
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#fbbf24" viewBox="0 0 16 16">
                  <path
                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                </svg>
              </span>
            </div>
            <div class="feedback-list">
              <div class="feedback-item">
                <div class="feedback-stars">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <span class="feedback-time">2 DAYS AGO</span>
                </div>
                <p class="feedback-text">
                  "Sarah is an amazing mentor! Her insights on design systems
                  changed how I work."
                </p>
                <div class="feedback-author">
                  <div class="author-avatar">J</div>
                  <span class="author-name">JOHN DOE</span>
                </div>
              </div>
              <div class="feedback-item">
                <div class="feedback-stars">
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="#fbbf24" viewBox="0 0 16 16">
                    <path
                      d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                  </svg>
                  <span class="feedback-time">5 DAYS AGO</span>
                </div>
                <p class="feedback-text">
                  "The course was very practical and the feedback was extremely
                  helpful."
                </p>
                <div class="feedback-author">
                  <div class="author-avatar">J</div>
                  <span class="author-name">JANE SMITH</span>
                </div>
              </div>
            </div>
            <a href="feedback.html" class="view-all-reviews">VIEW ALL REVIEWS <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>

<script type="text/javascript" src="/assets/js/components/sidebar.js" defer></script>

</html>