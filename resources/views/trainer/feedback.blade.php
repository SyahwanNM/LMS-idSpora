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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
  @vite(['resources/css/trainer/feedback.css'])
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
        <h1>{{ $averageRating }}</h1>
        <p>GLOBAL AVG</p>
      </div>
      <div class="avg-container">
        <h1>{{ $satisfactionRate }}%</h1>
        <p>SATISFACTION</p>
      </div>
    </div>
  </div>

  <div class="content-wrapper">
    <div class="left-content">
      <div class="sentiment-matrix">
        <p>Sentiment Matrix</p>
        <div class="statistic-rating">

          {{-- Baris Bintang 5 --}}
          <div class="rating-row">
            <div class="rating">
              <i class="bi bi-star-fill"></i>
              <p>5 STAR SCORE</p>
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
              <p>4 STAR SCORE</p>
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
              <p>3 STAR SCORE</p>
              <span class="percentage">{{ $ratingStats[3] }}%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar secondary" style="width: {{ $ratingStats[3] }}%"></div>
            </div>
          </div>

        </div>

        <button class="filter-button">
          <i class="bi bi-funnel"></i>
          Filter Content
        </button>

        <p class="pedagogical-audit">Pedagogical Audit</p>
      </div>
    </div>

    <div class="right-content">
      <div class="interaction-archive">
        <div class="top-part">
          <div class="title-container">
            <i class="bi bi-people"></i>
            <p>INTERACTION ARCHIVE</p>
          </div>

          {{-- Form Pencarian --}}
          <form action="{{ route('trainer.feedback') }}" method="GET" class="search-log">
            <i class="bi bi-search"></i>
            <span>
              <input type="text" name="search" placeholder="Search logs..." value="{{ request('search') }}" />
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
                  <h4>{{ $feedback->user->name ?? 'Anonymous Learner' }}</h4>
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
                    <i class="bi bi-calendar-event"></i> EVENT:
                    {{ Str::limit(strtoupper($feedback->event->title ?? 'Session'), 20) }}
                  @else
                    <i class="bi bi-chat-quote"></i> FEEDBACK
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
                <p>Authoring Response</p>
              </div>
              <form class="reply-form" onsubmit="submitReply(event, {{ $feedback->id }})">
                @csrf
                <div class="textarea-wrapper">
                  <textarea name="response" placeholder="Craft a professional response..." required></textarea>
                </div>
                <div class="authoring-actions">
                  <button type="button" class="cancel-btn" onclick="toggleReplyForm(this)">
                    Cancel
                  </button>
                  <button type="submit" class="sync-reply-btn">
                    Sync Reply
                  </button>
                </div>
              </form>
            </div>

            @if($feedback->replies && $feedback->replies->count() > 0)
              <div
                style="margin: 12px 0; padding: 12px; background: #f3f1f9; border-radius: 8px; border-left: 3px solid #1b1763;">
                <p style="margin: 0 0 10px; font-size: 12px; font-weight: 700; color: #1b1763;">Trainer Responses
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
        textarea.focus();
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
            console.error('Error: ' + (data.message || 'Failed to save reply'));
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
  </script>
@endpush