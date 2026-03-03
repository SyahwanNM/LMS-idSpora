<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  @vite(['resources/css/app.css'])
  <link rel="stylesheet" href="/assets/css/events.css" />
</head>
<style>
  /* Events Page Specific Styles - Using main.css variables */

  main {
    padding: var(--spacing-4xl);
    background-color: var(--base-clr);
    overflow-y: auto;
    max-width: 1200px;
    margin: 0 auto;
    width: 100%;
  }

  .top-page {
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

  /* Decorative glow circles */
  .glow-circle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
  }

  .glow-circle-1 {
    top: -80px;
    right: -80px;
    width: 192px;
    height: 192px;
    background: rgba(251, 191, 36, 0.1);
    filter: blur(60px);
  }

  .glow-circle-2 {
    bottom: -40px;
    left: -40px;
    width: 128px;
    height: 128px;
    background: rgba(99, 102, 241, 0.1);
    filter: blur(50px);
  }

  .top-page-inner {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-2xl);
  }

  .top-page-content {
    display: flex;
    flex-direction: column;
    gap: 24px;
    flex: 1;
  }

  .badge-top {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 100px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 8px;
    backdrop-filter: blur(10px);
    width: fit-content;
  }

  .badge-top svg {
    width: 12px;
    height: 12px;
    color: var(--yellow-clr);
    flex-shrink: 0;
  }

  .title-page {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 720px;
  }

  .title-page h1 {
    margin: 0;
    color: var(--white-clr);
    font-size: 40px;
    font-weight: 800;
    line-height: 1.2;
  }

  .title-page h1 span {
    color: var(--accent-yellow);
  }

  .title-page h5 {
    margin: 0;
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    font-weight: 500;
    line-height: 1.6;
    max-width: 620px;
  }

  .upcoming-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    min-width: 200px;
    backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
  }

  .upcoming-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    background: var(--accent-yellow);
    border-radius: 14px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(251, 176, 52, 0.3);
  }

  .upcoming-icon svg {
    width: 24px;
    height: 24px;
    color: var(--main-navy-clr);
  }

  .upcoming-text {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
  }

  .upcoming-label {
    font-size: 9px;
    font-weight: 900;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1.4px;
  }

  .upcoming-count {
    font-size: 18px;
    font-weight: 900;
    color: var(--white-clr);
    line-height: 1;
  }

  .search-filter-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: var(--spacing-sm);
    flex-wrap: nowrap;
    align-self: flex-end;
    margin-left: 0;
    width: auto;
    flex-shrink: 0;
  }

  .search-column {
    margin-top: 0;
    background-color: rgba(255, 255, 255, 0.16);
    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    width: 100%;
    max-width: 280px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: none;
    height: 44px;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
  }

  .search-column:hover,
  .search-column:focus-within {
    background-color: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.35);
  }

  .search-column svg {
    color: rgba(255, 255, 255, 0.7);
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    transition: color 0.2s ease;
  }

  .search-column:focus-within svg {
    color: rgba(255, 255, 255, 0.9);
  }

  .search-column input {
    border: none;
    outline: none;
    flex: 1;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9);
    background: transparent;
    font-weight: 400;
  }

  .search-column input::placeholder {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 400;
    font-weight: 400;
  }

  .filter-bar {
    gap: 8px;
    background-color: rgba(255, 255, 255, 0.16);
    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    width: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: none;
    height: 44px;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
  }

  .filter-bar:hover {
    background-color: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.35);
  }

  .filter-bar svg {
    color: rgba(255, 255, 255, 0.8);
    width: 16px;
    height: 16px;
    transition: color 0.2s ease;
  }

  .filter-bar:hover svg {
    color: rgba(255, 255, 255, 0.9);
  }

  /* Event List Section */

  .event-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 24px;
    margin-top: 0;
    padding: 0;
  }

  .event-card {
    background: var(--white-clr);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
    color: inherit;
    max-width: 340px;
  }

  .event-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
  }

  .event-image-container {
    position: relative;
    width: 100%;
    height: 200px;
    overflow: hidden;
  }

  .event-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .event-date-badge {
    position: absolute;
    top: 16px;
    left: 16px;
    background: rgba(27, 23, 99, 0.9);
    backdrop-filter: blur(10px);
    padding: 8px 12px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
    min-width: 60px;
  }

  .event-date-badge .month {
    font-size: 10px;
    font-weight: 700;
    color: var(--yellow-clr);
    letter-spacing: 1px;
    text-transform: uppercase;
  }

  .event-date-badge .day {
    font-size: 24px;
    font-weight: 900;
    color: var(--white-clr);
    line-height: 1;
  }

  .event-arrow-btn {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--main-navy-clr);
  }

  .event-arrow-btn:hover {
    background: var(--white-clr);
    transform: scale(1.1);
  }

  .event-card-content {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .event-type-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 10px;
    font-weight: 700;
    color: var(--yellow-clr);
    letter-spacing: 1.2px;
    text-transform: uppercase;
  }

  .event-type-badge .badge-dash {
    font-weight: 900;
    font-size: 12px;
  }

  .event-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0;
    line-height: 1.3;
    min-height: 48px;
  }

  .event-info-row {
    display: flex;
    flex-direction: row;
    gap: 10px;
    margin-top: 8px;
  }

  .event-info-item {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    background: rgba(255, 250, 242, 0.9);
    padding: 12px;
    border-radius: 8px;
  }

  .event-info-item svg {
    flex-shrink: 0;
    color: #ffb446;
    width: 16px;
    height: 16px;
  }

  .event-info-item .info-text {
    display: flex;
    flex-direction: column;
    gap: 3px;
  }

  .event-info-item .info-label {
    font-size: 9px;
    font-weight: 700;
    color: #ffb446;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  .event-info-item .info-value {
    font-size: 13px;
    font-weight: 700;
    color: var(--main-navy-clr);
  }

  /* Responsive */
  @media (max-width: var(--breakpoint-md)) {
    .top-page {
      flex-direction: column;
      align-items: stretch;
      gap: var(--spacing-md);
    }

    .search-filter-bar {
      margin-right: 0;
      justify-content: space-between;
    }

    .event-list {
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
  }
</style>

<body>
  @include('partials.navbar-before-login')
  @include('trainer.partials.sidebar', ['activeMenu' => 'events'])
  <main>
    <div class="top-page">
      <div class="glow-circle glow-circle-1"></div>
      <div class="glow-circle glow-circle-2"></div>

      <div class="top-page-inner">
        <div class="top-page-content">
          <div class="title-page">
            <span class="badge-top">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path
                  d="M12 3l1.912 5.813a2 2 0 001.899 1.374h6.098l-4.931 3.582a2 2 0 00-.728 2.236l1.912 5.813-4.931-3.582a2 2 0 00-2.342 0l-4.931 3.582 1.912-5.813a2 2 0 00-.728-2.236L2.091 10.187h6.098a2 2 0 001.899-1.374L12 3z" />
              </svg>
              <span>SCHEDULE HUB + ACADEMIC EXCELLENCE</span>
            </span>
            <h1>Mastering the <br /><span>Session Ledger.</span></h1>
            <h5>
              Orchestrate your teaching commitments with precision. Track,
              manage, and excel in every session.
            </h5>
          </div>
        </div>
        <div class="search-filter-bar">
          <div class="search-column">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search"
              viewBox="0 0 16 16">
              <path
                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
            </svg>
            <input type="text" placeholder="Lookup Session..." />
          </div>
          <button class="filter-bar">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel"
              viewBox="0 0 16 16">
              <path
                d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
            </svg>
          </button>
        </div>
      </div>
    </div>
    <div class="event-list">
      <a href="detail-event.html" class="event-card">
        <div class="event-image-container">
          <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop"
            alt="Visual Branding Architecture" class="event-image" />
          <div class="event-date-badge">
            <span class="month">APRIL</span>
            <span class="day">10</span>
          </div>
          <button class="event-arrow-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
              <path fill-rule="evenodd"
                d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
            </svg>
          </button>
        </div>
        <div class="event-card-content">
          <div class="event-type-badge">
            <span class="badge-dash">—</span>
            <span>HYBRID SESSION</span>
          </div>
          <h3 class="event-card-title">Visual Branding Architecture</h3>
          <div class="event-info-row">
            <div class="event-info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
              </svg>
              <div class="info-text">
                <span class="info-label">START TIME</span>
                <span class="info-value">01:00 PM</span>
              </div>
            </div>
            <div class="event-info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                <path
                  d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
              </svg>
              <div class="info-text">
                <span class="info-label">CAPACITY</span>
                <span class="info-value">30 Learners</span>
              </div>
            </div>
          </div>
        </div>
      </a>

      <a href="detail-event.html" class="event-card">
        <div class="event-image-container">
          <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=300&fit=crop"
            alt="Web Design Workshop" class="event-image" />
          <div class="event-date-badge">
            <span class="month">APRIL</span>
            <span class="day">15</span>
          </div>
          <button class="event-arrow-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
              <path fill-rule="evenodd"
                d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
            </svg>
          </button>
        </div>
        <div class="event-card-content">
          <div class="event-type-badge">
            <span class="badge-dash">—</span>
            <span>ONLINE SESSION</span>
          </div>
          <h3 class="event-card-title">Web Design Workshop</h3>
          <div class="event-info-row">
            <div class="event-info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
              </svg>
              <div class="info-text">
                <span class="info-label">START TIME</span>
                <span class="info-value">02:00 PM</span>
              </div>
            </div>
            <div class="event-info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                <path
                  d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
              </svg>
              <div class="info-text">
                <span class="info-label">CAPACITY</span>
                <span class="info-value">32 Learners</span>
              </div>
            </div>
          </div>
        </div>
      </a>

      <a href="detail-event.html" class="event-card">
        <div class="event-image-container">
          <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop"
            alt="React Masterclass" class="event-image" />
          <div class="event-date-badge">
            <span class="month">APRIL</span>
            <span class="day">20</span>
          </div>
          <button class="event-arrow-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
              <path fill-rule="evenodd"
                d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
            </svg>
          </button>
        </div>
        <div class="event-card-content">
          <div class="event-type-badge">
            <span class="badge-dash">—</span>
            <span>ONLINE SESSION</span>
          </div>
          <h3 class="event-card-title">React Masterclass</h3>
          <div class="event-info-row">
            <div class="event-info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
              </svg>
              <div class="info-text">
                <span class="info-label">START TIME</span>
                <span class="info-value">10:00 AM</span>
              </div>
            </div>
            <div class="event-info-item">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor">
                <path
                  d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
              </svg>
              <div class="info-text">
                <span class="info-label">CAPACITY</span>
                <span class="info-value">58 Learners</span>
              </div>
            </div>
          </div>
        </div>
      </a>
    </div>
  </main>
</body>

<script type="text/javascript" src="/assets/js/components/sidebar.js" defer></script>

</html>