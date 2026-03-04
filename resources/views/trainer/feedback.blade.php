@extends('layouts.trainer')

@section('title', 'Feedback - Trainer')

@php
  $pageTitle = 'Feedback';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Feedback']
  ];
@endphp

@push('styles')
  <link rel="stylesheet" href="/assets/css/feedback.css" />
  <style>
    main {
      padding: var(--spacing-2xl);
      background-color: var(--base-clr);
    }

    .trainer-page main {
      margin: 0;
      padding: var(--spacing-2xl);
    }

    .content-wrapper {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: var(--spacing-lg);
      margin: 0;
    }

    .left-content {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
    }

    .right-content {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
    }

    .top-container {
      display: flex;
      justify-content: space-between;
      margin: 0 0 var(--spacing-2xl) 0;
      align-items: center;
      padding: var(--spacing-xl) var(--spacing-2xl);
      background: linear-gradient(135deg, #2d2373 0%, #1b1763 100%);
      border-radius: var(--radius-xl);
      box-shadow: 0 4px 20px rgba(27, 23, 99, 0.3);
    }

    .description {
      flex: 1;
    }

    .learner-badge {
      display: inline-block;
      background-color: var(--yellow-clr);
      color: var(--main-navy-clr);
      padding: var(--spacing-xs) var(--spacing-sm);
      border-radius: var(--radius-sm);
      font-size: 9px;
      font-weight: 700;
      letter-spacing: 0.4px;
      margin-bottom: var(--spacing-sm);
      text-transform: uppercase;
    }

    .description h1 {
      color: white;
      margin: 0 0 6px 0;
      font-size: var(--font-size-4xl);
      font-weight: 700;
      line-height: 1.2;
    }

    .description p {
      margin: 0;
      color: rgba(148, 163, 184, 0.9);
      font-size: var(--font-size-sm);
      line-height: 1.5;
    }

    .stats-container {
      display: flex;
      gap: var(--spacing-sm);
    }

    .avg-container {
      text-align: center;
      background-color: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: var(--radius-lg);
      padding: var(--spacing-sm);
      min-width: 100px;
      backdrop-filter: blur(10px);
    }

    .avg-container h1 {
      margin: 0 0 4px 0;
      font-size: var(--font-size-4xl);
      font-weight: 700;
      color: white;
    }

    .avg-container p {
      margin: 0;
      color: var(--yellow-clr);
      font-size: 8px;
      font-weight: 700;
      letter-spacing: 0.8px;
      text-transform: uppercase;
    }

    .sentiment-matrix {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-xl);
      padding: var(--spacing-2xl);
      margin: 0;
      background-color: white;
      border-radius: var(--radius-xl);
    }

    .sentiment-matrix>p {
      color: var(--gray-second-clr);
      font-size: var(--font-size-xs);
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin: 0;
    }

    .statistic-rating {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
      margin-bottom: 0;
    }

    .rating-row {
      margin-bottom: 0;
    }

    .sentiment-matrix .rating {
      display: grid;
      grid-template-columns: 14px 1fr auto;
      position: static;
      align-items: center;
      column-gap: var(--spacing-sm);
      background: transparent;
      padding: 0;
      border-radius: 0;
      box-shadow: none;
      right: auto;
      bottom: auto;
      z-index: auto;
      margin-bottom: var(--spacing-xs);
    }

    .sentiment-matrix .rating svg {
      color: var(--yellow-clr);
      width: 14px;
      height: 14px;
      flex-shrink: 0;
    }

    .sentiment-matrix .rating p {
      color: var(--main-navy-clr);
      margin: 0;
      font-size: var(--font-size-sm);
      font-weight: 600;
    }

    .sentiment-matrix .rating .percentage {
      color: var(--main-navy-clr);
      font-weight: 700;
      font-size: var(--font-size-base);
      margin-left: 0;
    }

    .progress-bar-container {
      width: 100%;
      height: 6px;
      background-color: var(--base-clr);
      border-radius: var(--radius-2xl);
      overflow: hidden;
    }

    .progress-bar {
      height: 100%;
      border-radius: var(--radius-2xl);
      transition: width 0.3s ease;
    }

    .progress-bar.primary {
      background-color: var(--main-navy-clr);
    }

    .progress-bar.secondary {
      background-color: var(--yellow-clr);
    }

    .filter-button {
      width: 100%;
      background-color: var(--main-navy-clr);
      color: white;
      border: none;
      padding: var(--spacing-lg) var(--spacing-xl);
      border-radius: var(--radius-lg);
      font-size: var(--font-size-sm);
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--spacing-sm);
      transition: all 0.2s ease;
      margin-bottom: var(--spacing-xl);
    }

    .filter-button:hover {
      background-color: var(--click-clr);
      transform: translateY(-1px);
    }

    .filter-button svg {
      width: 16px;
      height: 16px;
    }

    .pedagogical-audit {
      text-align: center;
      color: var(--gray-second-clr);
      font-size: var(--font-size-xs);
      font-weight: 600;
      letter-spacing: 1px;
      text-transform: uppercase;
      margin: 0;
    }

    .top-part {
      margin: 0 0 var(--spacing-xl) 0;
      padding: 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .title-container {
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
    }

    .title-container svg {
      color: var(--main-navy-clr);
      width: 18px;
      height: 18px;
    }

    .title-container p {
      margin: 0;
      color: var(--main-navy-clr);
      font-weight: 600;
      font-size: var(--font-size-xs);
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .search-log {
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      border: 1px solid var(--line-clr);
      padding: var(--spacing-xs) var(--spacing-md);
      border-radius: var(--radius-lg);
      background-color: white;
    }

    .search-log svg {
      color: var(--gray-clr);
      width: 14px;
      height: 14px;
    }

    .search-log input {
      border: none;
      outline: none;
      font-size: var(--font-size-sm);
      color: var(--main-navy-clr);
      width: 150px;
    }

    .search-log input::placeholder {
      color: var(--gray-clr);
      font-size: var(--font-size-sm);
    }

    .interaction-archive {
      background-color: transparent;
      border-radius: 0;
      padding: 0;
    }

    .load-historical-button {
      width: 100%;
      background-color: transparent;
      color: var(--gray-second-clr);
      border: none;
      padding: var(--spacing-lg) var(--spacing-xl);
      border-radius: 0;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.8px;
      text-transform: uppercase;
      cursor: pointer;
      transition: all 0.2s ease;
      margin-top: var(--spacing-sm);
      text-align: center;
    }

    .load-historical-button:hover {
      color: var(--main-navy-clr);
    }

    .master-tip {
      background-color: white;
      border-radius: var(--radius-xl);
      padding: var(--spacing-3xl);
      display: flex;
      gap: var(--spacing-xl);
      align-items: flex-start;
      border-left: 4px solid var(--yellow-clr);
    }

    .master-tip svg {
      color: var(--yellow-clr);
      width: 24px;
      height: 24px;
      flex-shrink: 0;
      margin-top: 2px;
    }

    .master-tip-content p {
      margin: 0;
      color: var(--yellow-clr);
      font-size: var(--font-size-xs);
      font-weight: 700;
      line-height: 1.6;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .interaction-container {
      margin: 0 0 var(--spacing-xl) 0;
      padding: var(--spacing-3xl);
      background-color: white;
      border-radius: var(--radius-xl);
      border: 1px solid transparent;
      transition: all 0.2s ease;
    }

    .interaction-container:hover {
      border: 1px solid var(--line-clr);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .student {
      display: flex;
      align-items: flex-start;
      gap: var(--spacing-xl);
      margin-bottom: var(--spacing-xl);
    }

    .student img {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      object-fit: cover;
      background: var(--gray-clr);
    }

    .student-info {
      flex: 1;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .student-details h4 {
      margin: 0 0 6px 0;
      color: var(--main-navy-clr);
      font-size: 15px;
      font-weight: 600;
    }

    .student-rating {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-top: 2px;
    }

    .stars {
      display: flex;
      gap: 2px;
    }

    .stars svg {
      width: 12px;
      height: 12px;
      color: var(--yellow-clr);
    }

    .date-text {
      color: var(--gray-second-clr);
      font-size: var(--font-size-xs);
      margin: 0;
      font-weight: 400;
    }

    .course-tag {
      background-color: var(--base-clr);
      color: var(--gray-second-clr);
      padding: var(--spacing-xs) var(--spacing-md);
      border-radius: var(--radius-sm);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.3px;
      white-space: nowrap;
    }

    .comment p {
      margin: 0 0 var(--spacing-2xl) 0;
      padding: var(--spacing-lg) var(--spacing-2xl);
      background-color: var(--base-clr);
      border-radius: var(--radius-lg);
      color: var(--main-navy-clr);
      font-size: var(--font-size-base);
      font-style: italic;
      line-height: 1.6;
    }

    .reaction {
      display: flex;
      gap: var(--spacing-xl);
      align-items: center;
    }

    .like,
    .reply {
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      padding: 0;
      border-radius: 0;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
      background: none;
      color: var(--gray-clr);
    }

    .like:hover,
    .reply:hover {
      color: var(--main-navy-clr);
    }

    .like svg,
    .reply svg {
      width: 14px;
      height: 14px;
    }

    .like span,
    .reply span {
      font-size: var(--font-size-xs);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    .authoring-response {
      display: none;
      margin-top: var(--spacing-xl);
      padding: var(--spacing-xl);
      background-color: var(--base-clr);
      border-radius: var(--radius-lg);
      border-left: 4px solid var(--main-navy-clr);
    }

    .authoring-response.active {
      display: block;
    }

    .authoring-header {
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      margin-bottom: var(--spacing-xl);
    }

    .authoring-header img {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      object-fit: cover;
    }

    .authoring-header p {
      margin: 0;
      color: var(--main-navy-clr);
      font-size: var(--font-size-xs);
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    .textarea-wrapper {
      position: relative;
      margin-bottom: var(--spacing-xl);
    }

    .textarea-wrapper textarea {
      width: 100%;
      min-height: 100px;
      padding: var(--spacing-lg);
      border: 1px solid var(--line-clr);
      border-radius: var(--radius-lg);
      font-size: var(--font-size-base);
      color: var(--main-navy-clr);
      font-family: inherit;
      resize: vertical;
      outline: none;
      background-color: white;
    }

    .textarea-wrapper textarea::placeholder {
      color: var(--gray-clr);
    }

    .textarea-wrapper textarea:focus {
      border-color: var(--main-navy-clr);
    }

    .edit-icon {
      position: absolute;
      bottom: 12px;
      right: 12px;
      color: var(--gray-clr);
      width: 16px;
      height: 16px;
    }

    .authoring-actions {
      display: flex;
      justify-content: flex-end;
      gap: var(--spacing-sm);
    }

    .cancel-btn {
      padding: var(--spacing-sm) var(--spacing-xl);
      border: none;
      background: transparent;
      color: var(--gray-clr);
      font-size: var(--font-size-xs);
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .cancel-btn:hover {
      color: var(--main-navy-clr);
    }

    .sync-reply-btn {
      padding: var(--spacing-sm) var(--spacing-xl);
      border: none;
      background-color: var(--main-navy-clr);
      color: white;
      font-size: var(--font-size-xs);
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-radius: var(--radius-lg);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      transition: all 0.2s ease;
    }

    .sync-reply-btn:hover {
      background-color: var(--click-clr);
    }

    .sync-reply-btn svg {
      width: 14px;
      height: 14px;
    }

    /* Responsive Design */
    @media (max-width: var(--breakpoint-md)) {
      .content-wrapper {
        grid-template-columns: 1fr;
        gap: var(--spacing-md);
        margin: 0;
      }

      .left-content {
        grid-column: 1;
      }

      .right-content {
        grid-column: 1;
      }

      .top-container {
        flex-direction: column;
        gap: var(--spacing-lg);
        margin: 0;
        padding: var(--spacing-lg);
      }

      .description {
        width: 100%;
      }

      .stats-container {
        width: 100%;
        gap: var(--spacing-md);
      }

      .interaction-container {
        padding: var(--spacing-lg);
      }

      .student {
        gap: var(--spacing-lg);
      }
    }

    @media (max-width: 480px) {
      main {
        padding: var(--spacing-lg);
      }

      .content-wrapper {
        margin: 0;
        gap: var(--spacing-sm);
      }

      .top-container {
        padding: var(--spacing-lg);
        margin: 0;
      }

      .master-tip {
        gap: var(--spacing-lg);
        padding: var(--spacing-lg);
      }

      .interaction-container {
        padding: var(--spacing-lg);
        margin: 0 0 var(--spacing-lg) 0;
      }

      .student {
        flex-direction: column;
        gap: var(--spacing-md);
      }

      .student img {
        width: 40px;
        height: 40px;
      }

      .student-info {
        flex-direction: column;
        gap: var(--spacing-md);
      }

      .comment p {
        padding: var(--spacing-md) var(--spacing-lg);
        margin: 0 0 var(--spacing-lg) 0;
      }

      .reaction {
        gap: var(--spacing-lg);
      }
    }
  </style>
@endpush

@section('content')
  <div class="top-container">
    <div class="description">
      <div class="learner-badge">LEARNER INSIGHTS</div>
      <h1>Student Voice Portal</h1>
      <p>Managing pedagogical reputation and academic trust.</p>
    </div>
    <div class="stats-container">
      <div class="avg-container">
        <h1>4.8</h1>
        <p>GLOBAL AVG</p>
      </div>
      <div class="avg-container">
        <h1>98%</h1>
        <p>SATISFACTION</p>
      </div>
    </div>
  </div>

  <div class="content-wrapper">
    <div class="left-content">
      <div class="sentiment-matrix">
        <p>Sentiment Matrix</p>
        <div class="statistic-rating">
          <div class="rating-row">
            <div class="rating">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path
                  d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
              </svg>
              <p>5 STAR SCORE</p>
              <span class="percentage">70%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar primary" style="width: 70%"></div>
            </div>
          </div>

          <div class="rating-row">
            <div class="rating">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path
                  d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
              </svg>
              <p>4 STAR SCORE</p>
              <span class="percentage">20%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar secondary" style="width: 20%"></div>
            </div>
          </div>

          <div class="rating-row">
            <div class="rating">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path
                  d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
              </svg>
              <p>3 STAR SCORE</p>
              <span class="percentage">10%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar secondary" style="width: 10%"></div>
            </div>
          </div>
        </div>

        <button class="filter-button">
          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
            <path
              d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5" />
          </svg>
          Filter Content
        </button>

        <p class="pedagogical-audit">Pedagogical Audit</p>
      </div>
    </div>

    <div class="right-content">
      <div class="interaction-archive">
        <div class="top-part">
          <div class="title-container">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people"
              viewBox="0 0 16 16">
              <path
                d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
            </svg>
            <p>INTERACTION ARCHIVE</p>
          </div>
          <div class="search-log">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search"
              viewBox="0 0 16 16">
              <path
                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
            </svg>
            <span>
              <input type="text" placeholder="Search logs..." />
            </span>
          </div>
        </div>
        <div class="interaction-container">
          <div class="student">
            <img src="https://i.pravatar.cc/150?img=12" alt="profile picture" />
            <div class="student-info">
              <div class="student-details">
                <h4>Alex Johnson</h4>
                <div class="student-rating">
                  <div class="stars">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                  </div>
                  <span class="date-text">• 3/20/2024</span>
                </div>
              </div>
              <div class="course-tag">ADVANCED FIGMA CONST...</div>
            </div>
          </div>
          <div class="comment">
            <p>
              "Sarah is an incredible teacher. The Figma session was very
              detailed."
            </p>
          </div>
          <div class="reaction">
            <div class="like">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-hand-thumbs-up"
                viewBox="0 0 16 16">
                <path
                  d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
              </svg>
              <span>VALID (24)</span>
            </div>
            <div class="reply" onclick="toggleReplyForm(this)">
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-chat-left" viewBox="0 0 16 16">
                <path
                  d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
              </svg>
              <span>ADD REPLY</span>
            </div>
          </div>

          <div class="authoring-response">
            <div class="authoring-header">
              <img src="https://i.pravatar.cc/150?img=8" alt="author" />
              <p>Authoring Response</p>
            </div>
            <div class="textarea-wrapper">
              <textarea placeholder="Craft a professional response..."></textarea>
              <svg class="edit-icon" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                <path
                  d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                <path fill-rule="evenodd"
                  d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
              </svg>
            </div>
            <div class="authoring-actions">
              <button class="cancel-btn" onclick="toggleReplyForm(this)">
                Cancel
              </button>
              <button class="sync-reply-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                  <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z" />
                  <path
                    d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466" />
                </svg>
                Sync Reply
              </button>
            </div>
          </div>
        </div>

        <button class="load-historical-button">
          Load Historical Engagement Ledger
        </button>
      </div>
    </div>
  </div>

@endsection