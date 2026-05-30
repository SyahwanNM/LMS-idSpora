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

.sentiment-matrix > p {
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

.sentiment-matrix .rating svg,
.sentiment-matrix .rating i {
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

.filter-button svg,
.filter-button i {
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

.title-container svg,
.title-container i {
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

.search-log svg,
.search-log i {
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
    min-height: 16px;
}

.stars {
    display: flex;
    gap: 2px;
    align-items: center;
    line-height: 1;
}

.stars svg,
.stars i {
    width: 10px;
    height: 10px;
    font-size: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
    vertical-align: middle;
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
.reply svg,
.like i,
.reply i {
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
@media (max-width: 768px) {
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
