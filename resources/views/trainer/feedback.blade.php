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
        padding: var(--spacing-2xl);
    }
    
    .feedback-page * {
        box-sizing: border-box;
    }

    /* Grid Layouts */
    .content-wrapper {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 32px;
        margin: 0;
        width: 100%;
    }

    .left-content {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        min-width: 0;
    }

    .right-content {
        display: flex;
        flex-direction: column;
        gap: 24px;
        width: 100%;
        min-width: 0;
    }

    /* Top Banner / Hero */
    .top-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0 0 32px 0;
        padding: 40px;
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
        border-radius: 24px;
        box-shadow: 0 15px 35px rgba(27, 23, 99, 0.15);
        color: white;
        position: relative;
        overflow: hidden;
        width: 100%;
    }

    .top-container::before {
        content: '';
        position: absolute;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        top: -50px;
        right: -50px;
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
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.4);
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-bottom: 16px;
        text-transform: uppercase;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .description h1 {
        color: white;
        margin: 0 0 8px 0;
        font-size: 36px;
        font-weight: 800;
        line-height: 1.2;
        letter-spacing: -0.5px;
    }

    .description p {
        margin: 0;
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
        line-height: 1.5;
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
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 24px;
        min-width: 140px;
        backdrop-filter: blur(10px);
        transition: transform 0.3s ease;
    }

    .avg-container:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }

    .avg-container h1 {
        margin: 0 0 8px 0;
        font-size: 42px;
        font-weight: 800;
        color: white;
        line-height: 1;
    }

    .avg-container p {
        margin: 0;
        color: #fbbf24;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    /* Sentiment Matrix (Left Sidebar) */
    .sentiment-matrix {
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding: 32px;
        background-color: #ffffff;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
        width: 100%;
    }

    .sentiment-matrix > p {
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .sentiment-matrix > p::before {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #fbbf24;
    }

    .statistic-rating {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .rating-row {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .sentiment-matrix .rating {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sentiment-matrix .rating i {
        color: #fbbf24;
        font-size: 16px;
    }

    .sentiment-matrix .rating p {
        color: #334155;
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        flex: 1;
    }

    .sentiment-matrix .rating .percentage {
        color: #0f172a;
        font-weight: 800;
        font-size: 15px;
    }

    .progress-bar-container {
        width: 100%;
        height: 8px;
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
        padding: 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.5px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s ease;
    }

    .filter-button:hover {
        background: #2e2050;
        color: white;
        border-color: #2e2050;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(27, 23, 99, 0.2);
    }

    /* Right Content Header */
    .top-part {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding: 0;
    }

    .title-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .title-container i {
        color: #2e2050;
        font-size: 24px;
    }

    .title-container p {
        margin: 0;
        color: #0f172a;
        font-weight: 800;
        font-size: 20px;
        letter-spacing: -0.5px;
    }

    .search-log {
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid #e2e8f0;
        padding: 10px 16px;
        border-radius: 99px;
        background-color: white;
        transition: border-color 0.2s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
    }
    
    .search-log:focus-within {
        border-color: #2e2050;
        box-shadow: 0 2px 10px rgba(27, 23, 99, 0.1);
    }

    .search-log i {
        color: #94a3b8;
        font-size: 16px;
    }

    .search-log input {
        border: none;
        outline: none;
        font-size: 14px;
        color: #334155;
        width: 180px;
        font-family: inherit;
    }

    .search-log input::placeholder {
        color: #94a3b8;
    }

    /* Interaction Archive Cards */
    .interaction-container {
        padding: 32px;
        background-color: #ffffff;
        border-radius: 20px;
        border: none;
        margin-bottom: 32px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        width: 100%;
        position: relative;
    }
    
    .interaction-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
        background: linear-gradient(180deg, #2e2050 0%, #51376c 100%);
        border-radius: 20px 0 0 20px;
    }

    .interaction-container:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
    }

    .student {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .student img {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        background: #f1f5f9;
        border: 3px solid #f8fafc;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .student-info {
        flex: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-width: 0;
        flex-wrap: wrap;
        gap: 12px;
    }

    .student-details h4 {
        margin: 0 0 6px 0;
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.3px;
        word-break: break-word;
    }

    .student-rating {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stars {
        display: flex;
        gap: 4px;
        color: #fbbf24;
        font-size: 14px;
    }

    .date-text {
        color: #64748b;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .date-text::before {
        content: '';
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #cbd5e1;
    }

    .course-tag {
        background: #f0f9ff;
        color: #0369a1;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1px solid #bae6fd;
        white-space: normal;
        word-break: break-word;
        box-shadow: 0 2px 10px rgba(186, 230, 253, 0.3);
    }

    .course-tag i {
        color: #0284c7;
    }

    .comment p {
        position: relative;
        margin: 0 0 24px 0;
        padding: 32px;
        background: #f8fafc;
        border-radius: 0 24px 24px 24px;
        border: 1px solid #e2e8f0;
        border-left: 4px solid #2e2050;
        color: #1e293b;
        font-size: 16px;
        line-height: 1.8;
        font-style: italic;
        font-weight: 500;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.01);
    }
    
    .comment p::before {
        content: '\201C';
        position: absolute;
        top: -10px;
        left: 20px;
        font-size: 70px;
        color: rgba(27, 23, 99, 0.05);
        font-family: serif;
        line-height: 1;
        font-weight: 900;
    }

    .reaction {
        display: flex;
        gap: 24px;
        align-items: center;
        padding-top: 20px;
    }

    .like,
    .reply {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        border: none;
        background: #f1f5f9;
        color: #475569;
        font-weight: 700;
        font-size: 14px;
        padding: 10px 20px;
        border-radius: 99px;
        transition: all 0.2s ease;
    }

    .like:hover {
        background: #e0f2fe;
        color: #0284c7;
        transform: translateY(-2px);
    }
    
    .reply:hover {
        background: #2e2050;
        color: white;
        transform: translateY(-2px);
    }
    
    /* Elegant Authoring Response Override */
    .interaction-container > div[style*="background: #f3f1f9"] {
        background: #f8fafc !important;
        border-left: none !important;
        border-top: 4px solid #2e2050 !important;
        border-radius: 20px !important;
        padding: 24px !important;
        margin-top: 32px !important;
        box-shadow: 0 10px 30px rgba(27, 23, 99, 0.05) !important;
    }
    
    .interaction-container > div[style*="background: #f3f1f9"] > p {
        color: #2e2050 !important;
        font-size: 14px !important;
        font-weight: 800 !important;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px !important;
    }
    
    .interaction-container > div[style*="background: #f3f1f9"] > div > div {
        background: white !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        padding: 20px !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02) !important;
        transition: transform 0.2s ease;
    }
    
    .interaction-container > div[style*="background: #f3f1f9"] > div > div:hover {
        transform: translateY(-2px);
    }

    .authoring-response {
        display: none;
        margin-top: 24px;
        padding: 24px;
        background: #f8fafc;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
    }

    .authoring-response.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .authoring-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }

    .authoring-header img {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .authoring-header p {
        margin: 0;
        color: #0f172a;
        font-size: 14px;
        font-weight: 700;
    }

    .textarea-wrapper {
        margin-bottom: 16px;
    }

    .textarea-wrapper textarea {
        width: 100%;
        min-height: 120px;
        padding: 16px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        font-size: 14px;
        color: #334155;
        font-family: inherit;
        resize: vertical;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .textarea-wrapper textarea:focus {
        border-color: #2e2050;
        box-shadow: 0 0 0 3px rgba(27, 23, 99, 0.1);
    }

    .authoring-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .cancel-btn {
        padding: 10px 20px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .cancel-btn:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .sync-reply-btn {
        padding: 10px 24px;
        border: none;
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
        color: white;
        font-size: 13px;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 10px rgba(27, 23, 99, 0.2);
    }

    .sync-reply-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(27, 23, 99, 0.3);
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .content-wrapper {
            grid-template-columns: 280px 1fr;
            gap: 24px;
        }
    }

    @media (max-width: 768px) {
        .feedback-page {
            padding: 16px;
        }
        
        .content-wrapper {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .top-container {
            flex-direction: column;
            gap: 24px;
            padding: 32px 24px;
            text-align: center;
        }

        .stats-container {
            width: 100%;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .avg-container {
            flex: 1;
            min-width: 120px;
        }

        .top-part {
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
        }
        
        .search-log {
            width: 100%;
        }
        
        .search-log input {
            width: 100%;
        }

        .interaction-container {
            padding: 24px;
        }
        
        .student-info {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .feedback-page {
            padding: 12px;
        }
        
        .top-container {
            padding: 24px 16px;
        }

        .description h1 {
            font-size: 28px;
        }
        
        .avg-container h1 {
            font-size: 32px;
        }

        .student {
            gap: 16px;
        }

        .student img {
            width: 48px;
            height: 48px;
            border-radius: 12px;
        }
        
        .interaction-container {
            padding: 20px;
            border-radius: 16px;
        }

        .comment p {
            padding: 16px;
            font-size: 14px;
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

        <p class="pedagogical-audit">Audit Pedagogis</p>
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
                    </div>
                    <span class="date-text">• {{ $feedback->created_at->format('n/j/Y') }}</span>
                  </div>
                </div>
                <div class="course-tag">
                  @if($feedback->event_id)
                    <i class="bi bi-calendar-event"></i> ACARA:
                    {{ Str::limit(strtoupper($feedback->event->title ?? 'Sesi'), 20) }}
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
              <div class="like" title="Mark as valid">
                <i class="bi bi-hand-thumbs-up"></i>
              </div>
              <div class="reply" onclick="toggleReplyForm(this)" title="Add reply">
                <i class="bi bi-chat-left"></i>
              </div>
            </div>

            <div class="authoring-response">
              <div class="authoring-header">
                <img src="{{ Auth::user()->avatar_url ?? 'https://i.pravatar.cc/150' }}" alt="author" />
                <p>Tulis Balasan</p>
              </div>
              <form class="reply-form" onsubmit="submitReply(event, {{ $feedback->id }})">
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
              <div
                style="margin: 12px 0; padding: 12px; background: #f3f1f9; border-radius: 8px; border-left: 3px solid #2e2050;">
                <p style="margin: 0 0 10px; font-size: 12px; font-weight: 700; color: #2e2050;">Balasan Instruktur
                  ({{ $feedback->replies->count() }})</p>
                <div style="display: grid; gap: 10px;">
                  @foreach($feedback->replies as $reply)
                    <div style="background: #fff; padding: 10px; border-radius: 6px; border: 1px solid #eff2f7;">
                      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                        <img src="{{ $reply->trainer->avatar_url ?? 'https://i.pravatar.cc/150?u=' . $reply->trainer_id }}"
                          alt="trainer" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover;" />
                        <div style="font-size: 12px;">
                          <div style="font-weight: 600; color: #0f172a;">{{ $reply->trainer->name }}</div>
                          <div style="color: #94a3b8; font-size: 11px;">{{ $reply->created_at->format('n/j/Y H:i') }}</div>
                        </div>
                      </div>
                      <p style="margin: 6px 0 0; color: #475569; font-size: 13px; line-height: 1.5;">{{ $reply->response }}</p>
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

    function submitReply(event, feedbackId) {
      event.preventDefault();

      const form = event.target;
      const textarea = form.querySelector('textarea');
      const response = textarea.value.trim();

      if (!response) {
        Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Please write a response before submitting.' });
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
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Error: ' + (data.message || 'Failed to save reply') });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({ icon: 'error', title: 'Gagal', text: 'An error occurred while saving the reply.' });
        });
    }
  </script>
@endpush
