@extends('layouts.trainer')

@section('title', 'Ulasan - Trainer')

@php
  $pageTitle = 'Ulasan';
  $breadcrumbs = [
    ['label' => 'Dasbor', 'url' => route('trainer.dashboard')],
    ['label' => 'Ulasan']
  ];
@endphp

@push('styles')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
  <style>
    /* Import Google Font */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');

    .trainer-page main {
        padding: 0;
        margin: 0;
    }

    .feedback-page {
        font-family: 'Outfit', sans-serif;
        color: #334155;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: hidden;
        box-sizing: border-box;
        padding: 24px;
    }
    
    .feedback-page * {
        box-sizing: border-box;
    }

    /* Grid Layouts */
    .content-wrapper {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 24px;
        margin: 0;
        width: 100%;
    }

    .left-content {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
        min-width: 0;
    }

    .right-content {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 100%;
        min-width: 0;
    }

    /* Top Banner / Hero */
    .top-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0 0 24px 0;
        padding: 32px 40px;
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
        border-radius: 16px;
        box-shadow: 0 15px 35px rgba(27, 23, 99, 0.1);
        color: white;
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .top-container::before {
        content: '';
        position: absolute;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        top: -40px;
        right: -40px;
        border-radius: 50%;
    }

    .description {
        flex: 1;
        position: relative;
        z-index: 2;
    }

    .learner-badge {
        display: inline-flex;
        align-items: center;
        background-color: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(5px);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.25);
        padding: 5px 12px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.8px;
        margin-bottom: 12px;
        text-transform: uppercase;
    }

    .description h1 {
        color: white;
        margin: 0 0 6px 0;
        font-size: 28px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    .description p {
        margin: 0;
        color: rgba(255, 255, 255, 0.8);
        font-size: 14.5px;
        line-height: 1.4;
        font-weight: 400;
    }

    .stats-container {
        display: flex;
        gap: 16px;
        position: relative;
        z-index: 2;
    }

    .avg-container {
        text-align: center;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px;
        padding: 16px 20px;
        min-width: 120px;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease, background 0.3s ease;
    }

    .avg-container:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.12);
    }

    .avg-container h1 {
        margin: 0 0 4px 0;
        font-size: 32px;
        font-weight: 800;
        color: white;
        line-height: 1;
    }

    .avg-container p {
        margin: 0;
        color: #fbbf24;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    /* Sentiment Matrix (Left Sidebar) */
    .sentiment-matrix {
        display: flex;
        flex-direction: column;
        gap: 18px;
        padding: 24px;
        background-color: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        border: 1px solid #f1f5f9;
        width: 100%;
    }

    .sentiment-matrix > p {
        color: #0f172a;
        font-size: 16px;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .sentiment-matrix > p::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #fbbf24;
    }

    .statistic-rating {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .rating-row {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .sentiment-matrix .rating {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sentiment-matrix .rating i {
        color: #fbbf24;
        font-size: 14px;
    }

    .sentiment-matrix .rating p {
        color: #475569;
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        flex: 1;
    }

    .sentiment-matrix .rating .percentage {
        color: #0f172a;
        font-weight: 800;
        font-size: 14px;
    }

    .progress-bar-container {
        width: 100%;
        height: 6px;
        background-color: #f1f5f9;
        border-radius: 99px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        border-radius: 99px;
        transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .progress-bar.primary {
        background: linear-gradient(90deg, #2e2050 0%, #51376c 100%);
    }

    .progress-bar.secondary {
        background: linear-gradient(90deg, #19102c 0%, #51376c 100%);
    }

    .filter-button {
        width: 100%;
        background: #f8fafc;
        color: #2e2050;
        border: 1px solid #e2e8f0;
        padding: 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .filter-button:hover {
        background: #2e2050;
        color: white;
        border-color: #2e2050;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(27, 23, 99, 0.15);
    }

    /* Right Content Header */
    .top-part {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding: 0;
    }

    .title-container {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .title-container i {
        color: #2e2050;
        font-size: 20px;
    }

    .title-container p {
        margin: 0;
        color: #0f172a;
        font-weight: 800;
        font-size: 18px;
        letter-spacing: -0.3px;
    }

    .search-log {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #e2e8f0;
        padding: 8px 14px;
        border-radius: 99px;
        background-color: white;
        transition: border-color 0.2s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.01);
    }
    
    .search-log:focus-within {
        border-color: #2e2050;
        box-shadow: 0 2px 8px rgba(27, 23, 99, 0.08);
    }

    .search-log i {
        color: #94a3b8;
        font-size: 14px;
    }

    .search-log span {
        flex: 1;
        display: flex;
    }

    .search-log input {
        border: none;
        outline: none;
        font-size: 13.5px;
        color: #334155;
        width: 160px;
        font-family: inherit;
    }

    .search-log input::placeholder {
        color: #94a3b8;
    }

    /* Interaction Archive Cards */
    .interaction-container {
        padding: 16px 20px;
        background-color: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        width: 100%;
        position: relative;
    }
    
    .interaction-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(180deg, #2e2050 0%, #51376c 100%);
        border-radius: 12px 0 0 12px;
    }

    .interaction-container:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
    }

    .student {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .student img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        flex-shrink: 0;
    }

    .student-info {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        min-width: 0;
        flex-wrap: wrap;
        gap: 8px;
    }

    .student-details {
        display: flex;
        flex-direction: column;
        gap: 2px;
        align-items: flex-start;
    }

    .student-details h4 {
        margin: 0;
        color: #0f172a;
        font-size: 14.5px;
        font-weight: 700;
        letter-spacing: -0.2px;
        word-break: break-word;
    }

    .student-rating {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .student-rating .stars {
        display: flex;
        gap: 3px;
        color: #fbbf24;
        font-size: 12px;
        align-items: center;
        width: auto !important;
        justify-content: flex-start;
        flex: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .date-text {
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .date-text::before {
        content: '';
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: #cbd5e1;
        display: inline-block;
    }

    .course-tag {
        background: #f0f9ff;
        color: #0369a1;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #bae6fd;
        white-space: normal;
        word-break: break-word;
    }

    .course-tag i {
        color: #0284c7;
        font-size: 12px;
    }

    .comment p {
        position: relative;
        margin: 0 0 12px 0;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        border-left: 3px solid #2e2050;
        color: #334155;
        font-size: 13.5px;
        line-height: 1.5;
        font-style: italic;
        font-weight: 500;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.01);
    }
    
    .comment p::before {
        content: '\201C';
        position: absolute;
        top: -6px;
        left: 8px;
        font-size: 32px;
        color: rgba(27, 23, 99, 0.03);
        font-family: serif;
        line-height: 1;
        font-weight: 900;
    }

    .reaction {
        display: flex;
        gap: 12px;
        align-items: center;
        padding-top: 12px;
    }

    .like,
    .reply {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        font-weight: 700;
        font-size: 13px;
        padding: 8px 16px;
        border-radius: 99px;
        transition: all 0.2s ease;
    }

    .like:hover {
        background: #e0f2fe;
        color: #0284c7;
        border-color: #bae6fd;
        transform: translateY(-1px);
    }

    .like.liked {
        background: #f3f1f9;
        color: #2e2050;
        border-color: #e9d5ff;
    }

    .like.liked i {
        color: #2e2050;
    }
    
    .reply:hover {
        background: #2e2050;
        color: white;
        border-color: #2e2050;
        transform: translateY(-1px);
    }

    /* Trainer Replies Design */
    .trainer-replies-block {
        margin-top: 16px;
        padding: 16px;
        background: #f8fafc;
        border-top: 3px solid #2e2050;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(27, 23, 99, 0.02);
    }

    .trainer-replies-block .replies-title {
        margin: 0 0 12px;
        font-size: 12px;
        font-weight: 700;
        color: #2e2050;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .trainer-reply-card {
        background: white;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #eff2f7;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
        transition: transform 0.2s ease;
    }

    .trainer-reply-card:hover {
        transform: translateY(-1px);
    }

    .trainer-reply-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .trainer-reply-header img {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        object-fit: cover;
    }

    .trainer-reply-meta {
        font-size: 12px;
    }

    .trainer-reply-meta .trainer-name {
        font-weight: 600;
        color: #0f172a;
    }

    .trainer-reply-meta .reply-time {
        color: #94a3b8;
        font-size: 11px;
    }

    .trainer-reply-text {
        margin: 6px 0 0;
        color: #475569;
        font-size: 13px;
        line-height: 1.5;
    }

    .authoring-response {
        display: none;
        margin-top: 16px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .authoring-response.active {
        display: block;
        animation: fadeIn 0.25s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .authoring-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .authoring-header img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
    }

    .authoring-header p {
        margin: 0;
        color: #0f172a;
        font-size: 13.5px;
        font-weight: 700;
    }

    .textarea-wrapper {
        margin-bottom: 12px;
    }

    .textarea-wrapper textarea {
        width: 100%;
        min-height: 100px;
        padding: 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        font-size: 13.5px;
        color: #334155;
        font-family: inherit;
        resize: vertical;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .textarea-wrapper textarea:focus {
        border-color: #2e2050;
        box-shadow: 0 0 0 3px rgba(27, 23, 99, 0.08);
    }

    .authoring-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .cancel-btn {
        padding: 8px 16px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 12.5px;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cancel-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .sync-reply-btn {
        padding: 8px 20px;
        border: none;
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
        color: white;
        font-size: 12.5px;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(27, 23, 99, 0.15);
    }

    .sync-reply-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(27, 23, 99, 0.2);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .content-wrapper {
            grid-template-columns: 260px 1fr;
            gap: 20px;
        }
    }

    @media (max-width: 768px) {
        .feedback-page {
            padding: 16px;
        }
        
        .content-wrapper {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .top-container {
            flex-direction: column;
            gap: 20px;
            padding: 24px 20px;
            text-align: center;
        }

        .stats-container {
            width: 100%;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .avg-container {
            flex: 1;
            min-width: 110px;
            padding: 16px;
        }

        .avg-container h1 {
            font-size: 32px;
        }

        .top-part {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        
        .search-log {
            width: 100%;
        }
        
        .search-log input {
            width: 100%;
        }

        .interaction-container {
            padding: 16px;
        }
        
        .student-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 6px;
        }

        .course-tag {
            width: auto;
        }
    }

    @media (max-width: 480px) {
        .feedback-page {
            padding: 12px;
        }
        
        .top-container {
            padding: 20px 16px;
        }

        .description h1 {
            font-size: 24px;
        }
        
        .avg-container h1 {
            font-size: 28px;
        }

        .student {
            gap: 10px;
        }

        .student img {
            width: 36px;
            height: 36px;
        }
        
        .interaction-container {
            padding: 12px 14px;
            border-radius: 8px;
        }

        .comment p {
            padding: 10px 14px;
            font-size: 13px;
        }

        .like, .reply {
            padding: 6px 12px;
            font-size: 12px;
        }

        .sentiment-matrix {
            padding: 16px;
        }

        .trainer-replies-block {
            padding: 10px;
            margin-top: 12px;
        }

        .trainer-reply-card {
            padding: 8px;
        }

        .trainer-reply-header img {
            width: 24px;
            height: 24px;
        }

        .trainer-reply-meta .trainer-name {
            font-size: 11px;
        }

        .trainer-reply-meta .reply-time {
            font-size: 10px;
        }

        .trainer-reply-text {
            font-size: 12px;
        }

        .authoring-response {
            padding: 10px;
            margin-top: 12px;
        }
    }
  </style>
@endpush

@section('content')
<div class="feedback-page">
  <div class="top-container">
    <div class="description">
      <div class="learner-badge">WAWASAN SISWA</div>
      <h1>Portal Suara Siswa</h1>
      <p>Mengelola reputasi pengajaran dan kepercayaan akademik.</p>
    </div>
    <div class="stats-container">
      <div class="avg-container">
        <h1>{{ $averageRating }}</h1>
        <p>RATA-RATA GLOBAL</p>
      </div>
      <div class="avg-container">
        <h1>{{ $satisfactionRate }}%</h1>
        <p>KEPUASAN</p>
      </div>
    </div>
  </div>

  <div class="content-wrapper">
    <div class="left-content">
      <div class="sentiment-matrix">
        <p>Matriks Sentimen</p>
        <div class="statistic-rating">

          {{-- Baris Bintang 5 --}}
          <div class="rating-row">
            <div class="rating">
              <i class="bi bi-star-fill"></i>
              <p>SKOR BINTANG 5</p>
              <span class="percentage">{{ $ratingStats[5] }}%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar primary" style="width: {{ $ratingStats[5] }}%"></div>
            </div>
          </div>

          {{-- Baris Bintang 4 --}}
          <div class="rating-row">
            <div class="rating">
              <i class="bi bi-star-fill"></i>
              <p>SKOR BINTANG 4</p>
              <span class="percentage">{{ $ratingStats[4] }}%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar secondary" style="width: {{ $ratingStats[4] }}%"></div>
            </div>
          </div>

          {{-- Baris Bintang 3 --}}
          <div class="rating-row">
            <div class="rating">
              <i class="bi bi-star-fill"></i>
              <p>SKOR BINTANG 3</p>
              <span class="percentage">{{ $ratingStats[3] }}%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar secondary" style="width: {{ $ratingStats[3] }}%"></div>
            </div>
          </div>
        </div>
        <button class="filter-button">
          <i class="bi bi-funnel"></i>
          Saring Konten
        </button>
      </div>
    </div>

    <div class="right-content">
      <div class="interaction-archive">
        <div class="top-part">
          <div class="title-container">
            <i class="bi bi-people"></i>
            <p>ARSIP INTERAKSI</p>
          </div>

          {{-- Form Pencarian --}}
          <form action="{{ route('trainer.feedback') }}" method="GET" class="search-log">
            <i class="bi bi-search"></i>
            <span>
              <input type="text" name="search" placeholder="Cari ulasan..." value="{{ request('search') }}" />
            </span>
          </form>
        </div>

        {{-- Looping Data Feedback --}}
        @forelse($feedbacks as $feedback)
          <div class="interaction-container">
            <div class="student">
              <img src="{{ $feedback->user->avatar_url ?? 'https://i.pravatar.cc/150?u=' . $feedback->user_id }}"
                alt="profile picture" />
              <div class="student-info">
                <div class="student-details">
                  <h4>{{ $feedback->user->name ?? 'Siswa Anonim' }}</h4>
                  <div class="student-rating">
                    <div class="stars">
                      {{-- Loop untuk menampilkan bintang sesuai rating (1-5) --}}
                      @for($i = 1; $i <= 5; $i++)
                        @if($i <= $feedback->rating)
                          <i class="bi bi-star-fill"></i>
                        @else
                          <i class="bi bi-star"></i>
                        @endif
                      @endfor
                      <span class="date-text">{{ $feedback->created_at->format('n/j/Y') }}</span>
                    </div>
                  </div>
                </div>
                <div class="course-tag">
                  @if($feedback->event_id)
                    <i class="bi bi-calendar-event"></i> ACARA:
                    {{ Str::limit(strtoupper($feedback->event->title ?? 'Sesi'), 20) }}
                  @elseif(isset($feedback->course))
                    <i class="bi bi-book"></i> KELAS:
                    {{ Str::limit(strtoupper($feedback->course->name ?? 'Kelas'), 20) }}
                  @else
                    <i class="bi bi-chat-quote"></i> UMPAN BALIK
                  @endif
                </div>
              </div>
            </div>

            <div class="comment">
              <p>"{{ $feedback->comment }}"</p>
            </div>

            <div class="reaction">
              <button type="button" class="like {{ $feedback->is_liked ? 'liked' : '' }}" onclick="toggleLike(this, {{ $feedback->id }}, '{{ $feedback->type }}')" title="Suka">
                <i class="bi {{ $feedback->is_liked ? 'bi-hand-thumbs-up-fill' : 'bi-hand-thumbs-up' }}"></i>
                <span>{{ $feedback->is_liked ? 'Disukai' : 'Suka' }}</span>
              </button>
              <button type="button" class="reply" onclick="toggleReplyForm(this)" title="Balas">
                <i class="bi bi-chat-left"></i>
                <span>Balas</span>
              </button>
            </div>

            <div class="authoring-response">
              <div class="authoring-header">
                <img src="{{ Auth::user()->avatar_url ?? 'https://i.pravatar.cc/150' }}" alt="author" />
                <p>Tulis Balasan</p>
              </div>
              <form class="reply-form" onsubmit="submitReply(event, {{ $feedback->id }}, '{{ $feedback->type }}')">
                @csrf
                <div class="textarea-wrapper">
                  <textarea name="response" placeholder="Tulis balasan yang profesional..." required></textarea>
                </div>
                <div class="authoring-actions">
                  <button type="button" class="cancel-btn" onclick="toggleReplyForm(this)">
                    Batal
                  </button>
                  <button type="submit" class="sync-reply-btn">
                    Kirim Balasan
                  </button>
                </div>
              </form>
            </div>

            @if($feedback->replies && $feedback->replies->count() > 0)
              <div class="trainer-replies-block">
                <p class="replies-title">Balasan Instruktur ({{ $feedback->replies->count() }})</p>
                <div style="display: grid; gap: 10px;">
                  @foreach($feedback->replies as $reply)
                    <div class="trainer-reply-card">
                      <div class="trainer-reply-header">
                        <img src="{{ $reply->trainer->avatar_url ?? 'https://i.pravatar.cc/150?u=' . $reply->trainer_id }}"
                          alt="trainer" />
                        <div class="trainer-reply-meta">
                          <div class="trainer-name">{{ $reply->trainer->name ?? 'Instruktur' }}</div>
                          <div class="reply-time">{{ $reply->created_at ? (\Carbon\Carbon::parse($reply->created_at)->format('n/j/Y H:i')) : '' }}</div>
                        </div>
                      </div>
                      <p class="trainer-reply-text">{{ $reply->response }}</p>
                    </div>
                  @endforeach
                </div>
              </div>
            @endif
          </div>
        @empty
          <div style="text-align: center; padding: 40px; color: #94a3b8;">
            <p>Belum ada feedback dari student pada sesi Anda.</p>
          </div>
        @endforelse

        {{-- Pagination --}}
        @if($feedbacks->hasPages())
          <div style="margin-top: 20px;">
            {{ $feedbacks->links() }}
          </div>
        @endif

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script>
    function toggleReplyForm(element) {
      // Mencari div .authoring-response di dalam container yang sama
      const container = element.closest('.interaction-container');
      const responseForm = container.querySelector('.authoring-response');
      responseForm.classList.toggle('active');
    }

    function toggleLike(element, id, type) {
      const token = document.querySelector('input[name="_token"]').value;
      const likeUrl = "{{ route('trainer.feedback.like', ['id' => ':id']) }}".replace(':id', id);

      fetch(likeUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          type: type
        })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const icon = element.querySelector('i');
            const text = element.querySelector('span');
            if (data.is_liked) {
              element.classList.add('liked');
              icon.className = 'bi bi-hand-thumbs-up-fill';
              if (text) text.textContent = 'Disukai';
            } else {
              element.classList.remove('liked');
              icon.className = 'bi bi-hand-thumbs-up';
              if (text) text.textContent = 'Suka';
            }
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    function submitReply(event, feedbackId, type) {
      event.preventDefault();

      const form = event.target;
      const textarea = form.querySelector('textarea');
      const response = textarea.value.trim();

      if (!response) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Tulis balasan terlebih dahulu.' });
        return;
      }

      // Get CSRF token
      const token = document.querySelector('input[name="_token"]').value;

      // Submit via AJAX
      fetch(`{{ route('trainer.feedback.reply.store') }}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token
        },
        body: JSON.stringify({
          feedback_id: feedbackId,
          type: type,
          response: response
        })
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Clear form
            textarea.value = '';
            // Close form
            toggleReplyForm(form);
            // Reload page to show new reply
            location.reload();
          } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Error: ' + (data.message || 'Gagal menyimpan balasan') });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({ icon: 'error', title: 'Gagal', text: 'Terjadi kesalahan saat menyimpan balasan.' });
        });
    }
  </script>
@endpush
