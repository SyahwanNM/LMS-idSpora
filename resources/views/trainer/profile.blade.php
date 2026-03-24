@extends('layouts.trainer')

@section('title', 'Profile - Trainer')

@php
    $pageTitle = 'Profile';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Profile']
    ];

    $displayRole = $trainer->profession ?: 'Trainer';
    $displayLocation = $trainer->institution ?: 'Location not set';
    $displayBio = $trainer->bio ?: 'Profil belum dilengkapi. Tambahkan bio agar peserta mengenal Anda lebih baik.';

    $headline = $trainer->profession
        ? ($trainer->institution ? $trainer->profession . ' at ' . $trainer->institution : $trainer->profession)
        : 'Praktisi & Trainer Profesional';
    $isVerifiedTrainer = !empty($trainer->email_verified_at);

    $activeStatus = ['active', 'published', 'ongoing'];
    $activeCoursesCollection = collect($courses)->filter(function ($courseItem) use ($activeStatus) {
        return in_array(strtolower((string) ($courseItem->status ?? '')), $activeStatus, true);
    })->values();

    $archivedCoursesCollection = collect($courses)->reject(function ($courseItem) use ($activeStatus) {
        return in_array(strtolower((string) ($courseItem->status ?? '')), $activeStatus, true);
    })->values();

    if ($activeCoursesCollection->isEmpty()) {
        $activeCoursesCollection = collect($topCourses)->values();
    }

    $activeCourses = $activeCoursesCollection->take(3);
    $archivedCourses = $archivedCoursesCollection->take(3);
    $selectedTestimonials = collect($recentFeedbacks)->take(3);
@endphp

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    @vite(['resources/css/trainer/profile.css'])
@endpush

@section('content')
    <div class="profile-wrap">
        @if(session('success'))
            <div
                style="background:#ecfdf5;border:1px solid #86efac;color:#166534;padding:10px 12px;border-radius:10px;font-size:13px;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div
                style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 12px;border-radius:10px;font-size:13px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="top-content">
            <div class="top-content-inner">
                <div class="top-main-row">
                    <div class="profile-left">
                        <div class="profile-photo">
                            <img src="{{ $trainer->avatar_url }}" alt="{{ $trainer->name }}" />
                            <button type="button" id="profilePhotoBadge" class="photo-badge" title="Ganti Foto">
                                <i class="bi bi-camera-fill" style="font-size: 14px; color: #1b1763;"></i>
                            </button>
                            <input type="file" id="avatarFileInput" name="avatar_file" accept="image/*"
                                style="display: none;" />
                        </div>

                        <div class="profile-text">
                            <div class="level-badge">
                                <i class="bi bi-star-fill" style="font-size: 12px;"></i>
                                Profil Trainer Profesional
                            </div>
                            <h2>{{ $trainer->name }}</h2>
                            <p class="role">{{ $displayRole }}</p>
                            <p class="headline">{{ $headline }}</p>
                            @if($isVerifiedTrainer)
                                <span class="verified-badge">
                                    <i class="bi bi-patch-check-fill"></i> VERIFIED TRAINER
                                </span>
                            @endif
                            <div class="info">
                                <div class="loc-mail">
                                    <i class="bi bi-geo-alt-fill" style="color: var(--yellow-clr); font-size: 14px;"></i>
                                    <span>{{ strtoupper($displayLocation) }}</span>
                                </div>
                                <div class="loc-mail">
                                    <i class="bi bi-envelope-fill" style="color: var(--yellow-clr); font-size: 14px;"></i>
                                    <span>{{ strtoupper($trainer->email) }}</span>
                                </div>
                            </div>
                            <p class="hero-bio">{{ \Illuminate\Support\Str::limit($displayBio, 260) }}</p>
                        </div>
                    </div>

                    <div class="profile-actions">
                        <button type="button" id="topEditToggleBtn" class="btn-configure">Edit Profil</button>
                        <button type="button" class="btn-share" aria-label="Share">
                            <i class="bi bi-share-fill" style="font-size: 14px;"></i>
                        </button>
                    </div>
                </div>

                <form id="topInlineEditForm" class="top-edit-form {{ $errors->any() ? 'active' : '' }}"
                    action="{{ route('trainer.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="top-edit-grid">
                        <div class="top-edit-field">
                            <label for="top_name">Nama</label>
                            <input id="top_name" type="text" name="name" value="{{ old('name', $trainer->name) }}" required>
                        </div>
                        <div class="top-edit-field">
                            <label for="top_profession">Profesi</label>
                            <input id="top_profession" type="text" name="profession"
                                value="{{ old('profession', $trainer->profession) }}">
                        </div>
                        <div class="top-edit-field">
                            <label for="top_institution">Institusi / Lokasi</label>
                            <input id="top_institution" type="text" name="institution"
                                value="{{ old('institution', $trainer->institution) }}">
                        </div>
                        <div class="top-edit-field">
                            <label for="top_phone">Telepon</label>
                            <input id="top_phone" type="text" name="phone" value="{{ old('phone', $trainer->phone) }}">
                        </div>
                        <div class="top-edit-field">
                            <label for="top_website">Website</label>
                            <input id="top_website" type="text" name="website"
                                value="{{ old('website', $trainer->website) }}" placeholder="https://example.com">
                        </div>
                        <div class="top-edit-field">
                            <label>Email</label>
                            <input type="text" value="{{ $trainer->email }}" readonly>
                        </div>
                    </div>
                    <div class="top-edit-actions">
                        <button type="button" id="topEditCancelBtn" class="top-edit-cancel">BATAL</button>
                        <button type="submit" class="top-edit-save">SIMPAN</button>
                    </div>
                </form>
            </div>
        </section>

        <div class="profile-dashboard">
            <aside class="dashboard-sidebar">
                <div class="profile-info-card">
                    <div class="expertise-section">
                        <h3 class="section-title">EXPERTISE STACK</h3>
                        <div class="pill-list" style="margin-top:8px;">
                            @foreach($expertiseTags as $tag)
                                <span class="pill">{{ strtoupper($tag) }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="info-divider"></div>

                    <div class="network-section">
                        <h3 class="section-title">NETWORK TUNNELS</h3>
                        <div class="network-icons" style="margin-top:8px;">
                            <a href="#" class="network-icon linkedin" aria-label="LinkedIn"><i
                                    class="bi bi-linkedin"></i><span>LINKEDIN</span></a>
                            <a href="{{ !empty($trainer->website) ? $trainer->website : '#' }}" class="network-icon website"
                                aria-label="Website" {{ !empty($trainer->website) ? 'target=_blank rel=noopener noreferrer' : '' }}><i class="bi bi-globe2"></i><span>WEBSITE</span></a>
                            <a href="#" class="network-icon twitter" aria-label="Twitter"><i
                                    class="bi bi-twitter-x"></i><span>TWITTER</span></a>
                            <a href="#" class="network-icon github" aria-label="Github"><i
                                    class="bi bi-github"></i><span>GITHUB</span></a>
                        </div>
                    </div>
                </div>

                <div class="reward-card reward-box">
                    @php
                        $formattedRevenue = number_format((float) $totalEarned, 2, '.', ',');
                        [$revenueMain, $revenueDecimals] = explode('.', $formattedRevenue);
                    @endphp
                    <p>GROSS REVENUE</p>
                    <h3><span style="font-size:42px;">Rp</span>{{ $revenueMain }}.<span
                            class="decimals">{{ $revenueDecimals }}</span></h3>
                    <i class="bi bi-wallet2 reward-icon"></i>
                    <button type="button" id="openLedgerBtn">FINANCIAL RECORDS</button>
                </div>
            </aside>

            <div class="dashboard-content">
                <div class="pedagogical-statement">
                    <div class="statement-header">
                        <h2 class="statement-title"><i class="bi bi-person"></i> Bio</h2>
                        <button type="button" id="topEditToggleBtnMirror" class="btn-share" aria-label="Edit Statement"><i
                                class="bi bi-pencil"></i></button>
                    </div>
                    <p class="statement-text">{{ $displayBio }}</p>
                </div>

                <div class="experience-card">
                    <div class="experience-header"><i class="bi bi-briefcase"></i> Experience</div>
                    <div class="experience-list">
                        @forelse($upcomingEvents as $event)
                            <div class="experience-item">
                                <div class="experience-marker">
                                    <span class="experience-dot"></span>
                                    <span class="experience-line"></span>
                                </div>
                                <div>
                                    <div class="experience-top">
                                        <h3 class="experience-role">{{ $event->title }}</h3>
                                        <span
                                            class="experience-range">{{ optional($event->event_date)->format('Y') ?? now()->format('Y') }}</span>
                                    </div>
                                    <p class="experience-company">{{ $displayRole }}</p>
                                    <p class="experience-desc">{{ $event->participants_count ?? 0 }} participants â€¢
                                        {{ optional($event->event_date)->format('d M Y') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <div class="experience-item">
                                <div class="experience-marker">
                                    <span class="experience-dot"></span>
                                </div>
                                <div>
                                    <div class="experience-top">
                                        <h3 class="experience-role">{{ $displayRole }}</h3>
                                        <span class="experience-range">PRESENT</span>
                                    </div>
                                    <p class="experience-company">{{ $trainer->institution ?: 'idSpora Trainer' }}</p>
                                    <p class="experience-desc">Aktif mengembangkan pengalaman belajar peserta dengan sesi
                                        training praktis.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="portfolio-header">
                    <h2 class="portfolio-title"><i class="bi bi-grid-3x3-gap-fill"></i> ACTIVE COURSE PORTFOLIO</h2>
                    <a href="{{ route('trainer.courses') }}" class="view-all">VIEW ALL</a>
                </div>

                <div class="course-grid">
                    @forelse($activeCourses as $course)
                        @php
                            $thumbnail = $course->card_thumbnail;
                            $thumbnailUrl = null;
                            if (!empty($thumbnail)) {
                                $thumbnailUrl = \Illuminate\Support\Str::startsWith($thumbnail, ['http://', 'https://'])
                                    ? $thumbnail
                                    : asset('storage/' . ltrim($thumbnail, '/'));
                            }
                            $displayCourseImage = $thumbnailUrl ?: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&q=80&w=900';
                            $rating = number_format((float) ($course->reviews_avg_rating ?? 0), 1);
                          @endphp
                        <a href="{{ route('trainer.detail-course', $course->id) }}" class="course-card"
                            style="position:relative;">
                            @if((float) $rating >= 4.5)
                                <div
                                    style="position:absolute;top:8px;right:8px;background:#fbbf24;color:#78350f;padding:5px 10px;border-radius:5px;font-size:9px;font-weight:700;z-index:10;display:flex;align-items:center;gap:4px;letter-spacing:0.5px;">
                                    <i class="bi bi-star-fill"></i> TOP
                                </div>
                            @endif
                            <img src="{{ $displayCourseImage }}" alt="{{ $course->name }}">
                            <div class="course-card-body">
                                <div class="course-meta">
                                    <span><i class="bi bi-star-fill" style="color:#f59e0b"></i> {{ $rating }}</span>
                                    <span>{{ number_format($course->active_enrollments_count) }} LEARNERS</span>
                                </div>
                                <h4 class="course-title">{{ $course->name }}</h4>
                                <div class="course-meta">
                                    <span>{{ strtoupper($course->level ?? 'GENERAL') }}</span>
                                    <span>{{ $course->modules_count }} MODULES</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="item-box" style="grid-column:1/-1;">
                            <h5>Belum ada kelas aktif</h5>
                            <p>Kelas yang sedang Anda ampu akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>

                <div class="student-feedback">
                    <div class="feedback-header">
                        <h2 class="feedback-title"><i class="bi bi-chat-quote-fill"></i> Recent Student Feedback</h2>
                        <span style="font-size:12px;font-weight:700;color:#1b1763;">{{ number_format($averageRating, 1) }}
                            <i class="bi bi-star-fill" style="color:#f59e0b"></i></span>
                    </div>

                    <div class="feedback-list">
                        @forelse($selectedTestimonials as $feedback)
                            @php
                                $rating = max(1, min(5, (int) $feedback->rating));
                                $authorName = optional($feedback->user)->name ?: 'Anonymous';
                                $authorInitial = strtoupper(mb_substr($authorName, 0, 1));
                              @endphp
                            <div class="feedback-item">
                                <div class="feedback-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $rating ? 'bi-star-fill' : 'bi-star' }}"
                                            style="color:#f59e0b;font-size:12px;"></i>
                                    @endfor
                                    <span
                                        class="feedback-time">{{ strtoupper(optional($feedback->created_at)->diffForHumans()) }}</span>
                                </div>
                                <p style="margin:6px 0 0;color:#475569;line-height:1.5;font-size:12px;">
                                    "{{ $feedback->comment ?: 'Tidak ada komentar.' }}"</p>
                                <div class="feedback-author">
                                    <span class="author-avatar">{{ $authorInitial }}</span>
                                    <span class="author-name">{{ strtoupper($authorName) }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="item-box">
                                <h5>Belum ada feedback</h5>
                                <p>Feedback peserta untuk course Anda akan tampil di sini.</p>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('trainer.feedback') }}" class="view-all-reviews"
                        style="display:inline-flex;align-items:center;gap:6px;margin-top:10px;">VIEW ALL REVIEWS <i
                            class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div id="ledgerModal" class="profile-modal">
        <div class="profile-modal-overlay" id="ledgerModalOverlay"></div>
        <div class="profile-modal-content">
            <h3 style="margin:0 0 12px;color:#0f172a;">Financial Ledger</h3>

            @forelse($ledgerPayments as $payment)
                <div class="ledger-row">
                    <div>
                        <h6>{{ optional($payment->course)->name ?: optional($payment->event)->title ?: 'Pembayaran' }}</h6>
                        <p>{{ optional($payment->created_at)->format('d M Y H:i') }} â€¢
                            {{ strtoupper($payment->method ?? '-') }}
                        </p>
                    </div>
                    <div class="ledger-amount">+ Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                </div>
            @empty
                <div class="item-box">
                    <h5>Belum ada transaksi</h5>
                    <p>Data pembayaran settled akan tampil di sini.</p>
                </div>
            @endforelse

            <button type="button" id="closeLedgerBtn"
                style="width:100%;margin-top:10px;border:none;background:#1b1763;color:#fff;border-radius:10px;padding:10px 12px;font-size:12px;font-weight:700;">CLOSE
                RECORDS</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const topEditToggleBtn = document.getElementById('topEditToggleBtn');
            const topEditToggleBtnMirror = document.getElementById('topEditToggleBtnMirror');
            const topInlineEditForm = document.getElementById('topInlineEditForm');
            const topEditCancelBtn = document.getElementById('topEditCancelBtn');

            const syncTopEditButton = () => {
                if (!topEditToggleBtn || !topInlineEditForm) return;
                const isActive = topInlineEditForm.classList.contains('active');
                topEditToggleBtn.textContent = isActive ? 'Tutup Edit' : 'Edit Profil';
            };

            if (topEditToggleBtn && topInlineEditForm) {
                topEditToggleBtn.addEventListener('click', function () {
                    topInlineEditForm.classList.toggle('active');
                    syncTopEditButton();
                });
            }

            if (topEditCancelBtn && topInlineEditForm) {
                topEditCancelBtn.addEventListener('click', function () {
                    topInlineEditForm.classList.remove('active');
                    syncTopEditButton();
                });
            }

            if (topEditToggleBtnMirror && topEditToggleBtn) {
                topEditToggleBtnMirror.addEventListener('click', function () {
                    topEditToggleBtn.click();
                });
            }

            syncTopEditButton();

            // Profile photo upload handler
            const profilePhotoBadge = document.getElementById('profilePhotoBadge');
            const avatarFileInput = document.getElementById('avatarFileInput');

            if (profilePhotoBadge && avatarFileInput) {
                profilePhotoBadge.addEventListener('click', function (e) {
                    e.preventDefault();
                    avatarFileInput.click();
                });

                avatarFileInput.addEventListener('change', function (e) {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const formData = new FormData();
                        formData.append('_token', document.querySelector('input[name="_token"]')?.value || '');
                        formData.append('_method', 'PUT');
                        formData.append('avatar', file);

                        fetch('{{ route("trainer.profile.update") }}', {
                            method: 'POST',
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Update image src
                                    const profileImg = document.querySelector('.profile-photo img');
                                    if (profileImg && data.avatar_url) {
                                        profileImg.src = data.avatar_url + '?' + new Date().getTime();
                                    }
                                    // Reset file input
                                    avatarFileInput.value = '';
                                }
                            })
                            .catch(err => console.error('Upload error:', err));
                    }
                });
            }

            const modal = document.getElementById('ledgerModal');
            const openBtn = document.getElementById('openLedgerBtn');
            const closeBtn = document.getElementById('closeLedgerBtn');
            const overlay = document.getElementById('ledgerModalOverlay');

            if (!modal || !openBtn || !closeBtn || !overlay) {
                return;
            }

            const openModal = () => {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            };

            const closeModal = () => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            };

            openBtn.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            overlay.addEventListener('click', closeModal);
        });
    </script>
@endpush