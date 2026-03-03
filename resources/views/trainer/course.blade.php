<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>My Courses - idSpora</title>
  @vite(['resources/css/app.css'])
  <link rel="stylesheet" href="/assets/css/course.css" />
</head>

<body>
  @include('partials.navbar-before-login')

  <style>
    main {
      padding: var(--spacing-4xl);
      background-color: var(--base-clr);
      overflow-y: auto;
      max-width: 1200px;
      margin: 0 auto;
      width: 100%;
      margin-left: 250px;
      margin-top: 70px;
    }

    .top-page {
      background: linear-gradient(135deg, var(--main-navy-clr) 0%, #0f0820 100%);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-3xl);
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(27, 23, 99, 0.15);
      margin-bottom: var(--spacing-2xl);
      width: 100%;
    }

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
      width: 100%;
      position: relative;
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
      max-width: 600px;
    }

    .title-page h1 {
      margin: 0;
      color: var(--white-clr);
      font-size: 40px;
      font-weight: 800;
      line-height: 1.2;
    }

    .title-page h1 span {
      color: #fbb034;
    }

    .title-page h5 {
      margin: 0;
      color: rgba(255, 255, 255, 0.7);
      font-size: 14px;
      font-weight: 500;
      line-height: 1.6;
      max-width: 500px;
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
      background: #fbb034;
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

    .card-course {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: var(--spacing-2xl);
      padding: 0;
    }

    .card-item {
      background-color: var(--white-clr);
      border-radius: 20px;
      overflow: hidden;
      border: 1px solid #eef2f7;
      box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
      transition: all 0.25s ease;
      position: relative;
      display: flex;
      flex-direction: column;
      text-decoration: none;
      color: inherit;
    }

    .card-item:hover {
      transform: translateY(-6px);
      box-shadow: 0 18px 32px rgba(15, 23, 42, 0.12);
    }

    .card-media {
      position: relative;
      overflow: hidden;
    }

    .badge-online {
      position: absolute;
      top: 12px;
      left: 12px;
      background: rgba(255, 255, 255, 0.18);
      color: #ffffff;
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 10px;
      font-weight: 800;
      margin: 0;
      z-index: 10;
      letter-spacing: 0.7px;
      text-transform: uppercase;
      border: 1px solid rgba(255, 255, 255, 0.35);
      backdrop-filter: blur(8px);
      box-shadow: none;
    }

    .rating {
      position: absolute;
      bottom: 10px;
      right: 12px;
      background-color: #2f1f4f;
      padding: 6px 11px;
      border-radius: 999px;
      display: flex;
      align-items: center;
      gap: 6px;
      font-weight: 800;
      font-size: 12px;
      color: var(--white-clr);
      box-shadow: 0 6px 16px rgba(15, 23, 42, 0.2);
      z-index: 20;
    }

    .rating svg {
      color: #f5c542;
      width: 14px;
      height: 14px;
    }

    .rating p {
      margin: 0;
      color: var(--white-clr);
    }

    .card-image {
      width: 100%;
      height: 170px;
      object-fit: cover;
      display: block;
    }

    .card-content {
      max-width: none;
      padding: 16px 16px 14px;
      margin: 0;
      background-color: var(--white-clr);
      border-radius: 0;
      display: flex;
      flex-direction: column;
    }

    .course-title {
      margin: 0 0 8px 0;
    }

    .course-title h3 {
      margin: 0;
      font-size: 18px;
      font-weight: 800;
      color: #2b2350;
      line-height: 1.3;
    }

    .course-title p {
      font-size: 13px;
      font-weight: 400;
      color: #7d98b3;
      margin: 6px 0 0 0;
      line-height: 1.5;
    }

    .bottom-card {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0 0 0;
      margin-top: 8px;
      border-top: 1px solid #edf1f7;
      gap: 12px;
    }

    .total-participant-path {
      display: flex;
      flex-direction: row;
      gap: 14px;
      align-items: center;
    }

    .total-participant {
      display: flex;
      flex-direction: row;
      margin: 0;
      align-items: center;
      gap: 5px;
    }

    .total-participant p {
      margin: 0;
      font-size: 12px;
      font-weight: 700;
      color: #2b2350;
    }

    .total-participant svg {
      color: #95a4b7;
      width: 16px;
      height: 16px;
    }

    .total-path {
      display: flex;
      flex-direction: row;
      margin: 0;
      align-items: center;
      gap: 5px;
    }

    .total-path p {
      margin: 0;
      font-size: 12px;
      font-weight: 700;
      color: #2b2350;
    }

    .total-path svg {
      color: #95a4b7;
      width: 16px;
      height: 16px;
    }

    .btn-detail-course {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background-color: #f7f9fd;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 1px solid #e9eef6;
      padding: 0;
      flex-shrink: 0;
    }

    .btn-detail-course:hover {
      background-color: #2b2350;
      border-color: #2b2350;
    }

    .btn-detail-course:hover svg {
      color: var(--white-clr);
    }

    .btn-detail-course svg {
      color: #2b2350;
      width: 16px;
      height: 16px;
      transition: color 0.3s ease;
    }

    /* Responsive */
    @media (max-width: var(--breakpoint-md)) {
      .card-course {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: var(--spacing-lg);
        padding: var(--spacing-lg);
      }

      .search-filter-bar {
        flex-direction: column;
      }
    }
  </style>

  <body>
    @include('trainer.partials.sidebar', ['activeMenu' => 'course'])
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

      <div class="card-course">
        <!-- Card 1 -->
        <a href="/trainer/detail-course" class="card-item">
          <div class="card-media">
            <p class="badge-online">INTERMEDIATE TIER</p>
            <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400&h=220&fit=crop"
              alt="Visual Branding Architecture" class="card-image" />
            <div class="rating">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill"
                viewBox="0 0 16 16">
                <path
                  d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
              </svg>
              <p>4.9</p>
            </div>
          </div>
          <div class="card-content">
            <div class="course-title">
              <h3>Visual Branding Architecture</h3>
              <p>Structure and manage your professional learning pathways.</p>
            </div>
            <div class="bottom-card">
              <div class="total-participant-path">
                <div class="total-participant">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-people" viewBox="0 0 16 16">
                    <path
                      d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                  </svg>
                  <p>850</p>
                </div>
                <div class="total-path">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-stack"
                    viewBox="0 0 16 16">
                    <path
                      d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z" />
                    <path
                      d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z" />
                  </svg>
                  <p>4 UNITS</p>
                </div>
              </div>
              <div class="btn-detail-course">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                  class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                  <path fill-rule="evenodd"
                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                </svg>
              </div>
            </div>
          </div>
        </a>

        <!-- Card 2 -->
        <a href="/trainer/detail-course" class="card-item">
          <div class="card-media">
            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=220&fit=crop"
              alt="Web Design Fundamentals" class="card-image" />
          </div>
          <div class="card-content">
            <div class="course-title">
              <h3>Web Design Fundamentals</h3>
              <p>Master the essentials of modern web design principles.</p>
            </div>
            <div class="bottom-card">
              <div class="total-participant-path">
                <div class="total-participant">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-people" viewBox="0 0 16 16">
                    <path
                      d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                  </svg>
                  <p>920</p>
                </div>
                <div class="total-path">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-stack"
                    viewBox="0 0 16 16">
                    <path
                      d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z" />
                    <path
                      d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z" />
                  </svg>
                  <p>10 UNIT</p>
                </div>
              </div>
              <div class="btn-detail-course">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                  class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                  <path fill-rule="evenodd"
                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                </svg>
              </div>
            </div>
          </div>
        </a>

        <!-- Card 3 -->
        <a href="/trainer/detail-course" class="card-item">
          <div class="card-media">
            <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=220&fit=crop"
              alt="UX/UI Design Principles" class="card-image" />
          </div>
          <div class="card-content">
            <div class="course-title">
              <h3>UX/UI Design Principles</h3>
              <p>
                Learn the art of creating user-centered digital experiences.
              </p>
            </div>
            <div class="bottom-card">
              <div class="total-participant-path">
                <div class="total-participant">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-people" viewBox="0 0 16 16">
                    <path
                      d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                  </svg>
                  <p>1.2K</p>
                </div>
                <div class="total-path">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-stack"
                    viewBox="0 0 16 16">
                    <path
                      d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z" />
                    <path
                      d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z" />
                  </svg>
                  <p>12 UNIT</p>
                </div>
              </div>
              <div class="btn-detail-course">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                  class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                  <path fill-rule="evenodd"
                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                </svg>
              </div>
            </div>
          </div>
        </a>

        <!-- Card 4 -->
        <a href="/trainer/detail-course" class="card-item">
          <div class="card-media">
            <img src="https://images.unsplash.com/photo-1544716278-ca5e3af4abd8?w=400&h=220&fit=crop"
              alt="Digital Marketing Strategy" class="card-image" />
          </div>
          <div class="card-content">
            <div class="course-title">
              <h3>Digital Marketing Strategy</h3>
              <p>
                Develop comprehensive digital marketing strategies and tactics.
              </p>
            </div>
            <div class="bottom-card">
              <div class="total-participant-path">
                <div class="total-participant">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-people" viewBox="0 0 16 16">
                    <path
                      d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                  </svg>
                  <p>780</p>
                </div>
                <div class="total-path">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-stack"
                    viewBox="0 0 16 16">
                    <path
                      d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z" />
                    <path
                      d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z" />
                  </svg>
                  <p>9 UNIT</p>
                </div>
              </div>
              <div class="btn-detail-course">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                  class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                  <path fill-rule="evenodd"
                    d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                </svg>
              </div>
            </div>
          </div>
        </a>
      </div>
    </main>
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
      });
    </script>
  </body>

</html>