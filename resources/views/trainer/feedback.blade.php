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
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path
                  d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
              </svg>
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
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path
                  d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
              </svg>
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
              <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                <path
                  d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
              </svg>
              <p>3 STAR SCORE</p>
              <span class="percentage">{{ $ratingStats[3] }}%</span>
            </div>
            <div class="progress-bar-container">
              <div class="progress-bar secondary" style="width: {{ $ratingStats[3] }}%"></div>
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

          {{-- Form Pencarian --}}
          <form action="{{ route('trainer.feedback') }}" method="GET" class="search-log">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search"
              viewBox="0 0 16 16">
              <path
                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
            </svg>
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
                          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-star-fill"
                            viewBox="0 0 16 16">
                            <path
                              d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                          </svg>
                        @else
                          {{-- Bintang kosong jika diperlukan, atau tidak perlu ditampilkan --}}
                        @endif
                      @endfor
                    </div>
                    <span class="date-text">• {{ $feedback->created_at->format('n/j/Y') }}</span>
                  </div>
                </div>
                <div class="course-tag">
                  @if($feedback->event_id)
                    EVENT: {{ Str::limit(strtoupper($feedback->event->title ?? 'Session'), 20) }}
                  @elseif($feedback->course_id)
                    COURSE: {{ Str::limit(strtoupper($feedback->course->name ?? 'Course'), 20) }}
                  @else
                    SESSION
                  @endif
                </div>
              </div>
            </div>

            <div class="comment">
              <p>"{{ $feedback->comment }}"</p>
            </div>

            <div class="reaction">
              <div class="like">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-hand-thumbs-up"
                  viewBox="0 0 16 16">
                  <path
                    d="M8.864.046C7.908-.193 7.02.53 6.956 1.466c-.072 1.051-.23 2.016-.428 2.59-.125.36-.479 1.013-1.04 1.639-.557.623-1.282 1.178-2.131 1.41C2.685 7.288 2 7.87 2 8.72v4.001c0 .845.682 1.464 1.448 1.545 1.07.114 1.564.415 2.068.723l.048.03c.272.165.578.348.97.484.397.136.861.217 1.466.217h3.5c.937 0 1.599-.477 1.934-1.064a1.86 1.86 0 0 0 .254-.912c0-.152-.023-.312-.077-.464.201-.263.38-.578.488-.901.11-.33.172-.762.004-1.149.069-.13.12-.269.159-.403.077-.27.113-.568.113-.857 0-.288-.036-.585-.113-.856a2 2 0 0 0-.138-.362 1.9 1.9 0 0 0 .234-1.734c-.206-.592-.682-1.1-1.2-1.272-.847-.282-1.803-.276-2.516-.211a10 10 0 0 0-.443.05 9.4 9.4 0 0 0-.062-4.509A1.38 1.38 0 0 0 9.125.111zM11.5 14.721H8c-.51 0-.863-.069-1.14-.164-.281-.097-.506-.228-.776-.393l-.04-.024c-.555-.339-1.198-.731-2.49-.868-.333-.036-.554-.29-.554-.55V8.72c0-.254.226-.543.62-.65 1.095-.3 1.977-.996 2.614-1.708.635-.71 1.064-1.475 1.238-1.978.243-.7.407-1.768.482-2.85.025-.362.36-.594.667-.518l.262.066c.16.04.258.143.288.255a8.34 8.34 0 0 1-.145 4.725.5.5 0 0 0 .595.644l.003-.001.014-.003.058-.014a9 9 0 0 1 1.036-.157c.663-.06 1.457-.054 2.11.164.175.058.45.3.57.65.107.308.087.67-.266 1.022l-.353.353.353.354c.043.043.105.141.154.315.048.167.075.37.075.581 0 .212-.027.414-.075.582-.05.174-.111.272-.154.315l-.353.353.353.354c.047.047.109.177.005.488a2.2 2.2 0 0 1-.505.805l-.353.353.353.354c.006.005.041.05.041.17a.9.9 0 0 1-.121.416c-.165.288-.503.56-1.066.56z" />
                </svg>
                <span>VALID</span>
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
                <img src="{{ Auth::user()->avatar_url ?? 'https://i.pravatar.cc/150' }}" alt="author" />
                <p>Authoring Response</p>
              </div>
              <div class="textarea-wrapper">
                <textarea placeholder="Craft a professional response..."></textarea>
              </div>
              <div class="authoring-actions">
                <button class="cancel-btn" onclick="toggleReplyForm(this)">
                  Cancel
                </button>
                <button class="sync-reply-btn">
                  Sync Reply
                </button>
              </div>
            </div>
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
  </script>
@endpush