<!DOCTYPE html>
<html lang="en">

<head> <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <title>Courses</title> <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"> @vite(['resources/css/app.css', 'resources/js/app.js']) <style> /* FIX FOOTER FULL WIDTH */ body { overflow-x: hidden; margin: 0; } .footer-section { width: 100vw; position: relative; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; margin-top: 40px; }

    /* FIX SCROLL/TOP SPACING (Agar tidak tertutup navbar) */
    .hero-carousel {
        margin-top: 85px; /* Jarak dikurangi agar lebih rapat dengan navbar */
    }
</style>
<style>
    .carousel-control-prev,
    .carousel-control-next {
        display: none !important;
    }
    .carousel-indicators [data-bs-target] {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #f4c430;
        opacity: 0.5;
        transition: opacity 0.2s;
        border: none;
        margin: 0 4px;
    }
    .carousel-indicators .active {
        opacity: 1;
        background-color: #51376c;
    }
</style>
</head>

@include('partials.navbar-after-login') 
<body style="padding-top: 0;"> 
    <main class="container-xl pb-5">
        <div id="carouselCaptions" class="carousel slide rounded-4 overflow-hidden mb-4 hero-carousel" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @forelse($courseCarousels as $index => $carousel)
                        <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="{{ $index }}" 
                            class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                            aria-label="Slide {{ $index + 1 }}"></button>
                    @empty
                        <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="0" class="active"
                            aria-current="true" aria-label="Slide 1"></button>
                    @endforelse
                </div>

                <div class="carousel-inner">
                    @forelse($courseCarousels as $index => $carousel)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" style="height: clamp(250px, 40vh, 420px); position: relative;">
                            @php
                                $btnUrl = $carousel->link_url ?? '#';
                                $isExternal = Str::startsWith($btnUrl, ['http://', 'https://']);
                            @endphp
                            
                            @if($carousel->link_url)
                                <a href="{{ $btnUrl }}" {{ $isExternal ? 'target="_blank"' : '' }}>
                            @endif
                            
                            <img src="{{ $carousel->image_url }}"
                                alt="{{ $carousel->title ?? 'Slide ' . ($index + 1) }}"
                                style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; "
                                onerror="this.src='https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop'">

                            @if($carousel->title)
                            <div class="carousel-caption text-start" style="bottom: 40px; left: 60px;">
                                <h2 class="fw-bold">{{ $carousel->title }}</h2>
                                @if($carousel->link_url)
                                    <button class="btn btn-warning fw-bold mt-2">Lihat Detail</button>
                                @endif
                            </div>
                            @endif

                            @if($carousel->link_url)
                                </a>
                            @endif
                        </div>
                    @empty
                        <div class="carousel-item active" style="height: clamp(250px, 40vh, 420px); position: relative;">
                            <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop"
                                alt="Slide 1"
                                style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; ">

                            <div class="carousel-caption text-start" style="bottom: 40px; left: 60px;">
                                <h2 class="fw-bold">Upgrade Skill Digitalmu</h2>
                                <p>Belajar langsung dari praktisi industri dengan kurikulum relevan.</p>
                                <button class="btn btn-warning fw-bold">Mulai Sekarang</button>
                            </div>
                        </div>
                    @endforelse
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselCaptions"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Previous</span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#carouselCaptions"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>

    <div class="filter-container" style="margin-top: -30px; position: relative; z-index: 10;">
        <form action="{{ route('courses.index') }}" method="GET" id="filter-form">
            <div class="filter-box">
                <div class="options">
                    <label>Level</label>
                    <select name="level" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Semua Level</option>
                        <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                <div class="options">
                    <label>Topic</label>
                    <select name="category" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="options">
                    <label>Nama Kursus</label>
                    <select name="topic" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Semua Topik</option>
                        @foreach($topics as $t)
                            <option value="{{ $t }}" {{ request('topic') == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="options">
                    <label>Price</label>
                    <select name="price" onchange="document.getElementById('filter-form').submit()">
                        <option value="">Default</option>
                        <option value="asc" {{ request('price') == 'asc' ? 'selected' : '' }}>Low to High</option>
                        <option value="desc" {{ request('price') == 'desc' ? 'selected' : '' }}>High to Low</option>
                    </select>
                </div>
            </div>
            <div class="search-container">
                <div class="search-wrap">
                    <input id="site-search" class="form-control search-input-2" type="search" name="search"
                        placeholder="Search" aria-label="Search" value="{{ request('search') }}">
                    <span class="search-icon" style="cursor:pointer;" onclick="document.getElementById('filter-form').submit()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            fill="currentColor" viewBox="0 0 16 16" focusable="false">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                        </svg>
                    </span>
                </div>
            </div>
        </form>
    </div>

    <section class="kursus-pelatihan">
        <div class="header-card">
            <h3>Lanjutkan Belajar</h3>
        </div>

        <ul class="course-list">
            @forelse(($continueEnrollments ?? collect()) as $enrollment)
                @php
                    $course = $enrollment->course;
                    if(!$course) continue;
                    $courseHref = route('course.detail', $course->id);
                    $cardImage = $course->card_thumbnail ?? ($course->media ?? null);
                    $pct = $enrollment->getProgressPercentage();
                    $pct = max(0, min(100, (int) $pct));
                @endphp
                <li>
                    <a href="{{ $courseHref }}" style="text-decoration:none;color:inherit;">
                        <article class="course-card">
                            <div class="thumb-wrapper">
                                @php
                                    if ($cardImage) {
                                        $imgSrc = str_starts_with($cardImage, 'http') ? $cardImage : asset('uploads/' . $cardImage);
                                    } else {
                                        $imgSrc = 'https://via.placeholder.com/300x200/4f46e5/ffffff?text=No+Image';
                                    }
                                @endphp
                                <img class="thumb" src="{{ $imgSrc }}" alt="{{ $course->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                                <div class="badge-save-group" style="gap:12px;">
                                    <span class="course-badge {{ $course->level }}">{{ ucfirst($course->level) }}</span>
                                    <button class="save-btn" aria-label="Save course" type="button" onclick="event.preventDefault();">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M2 2v13.5l6-3 6 3V2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="inner">
                                <h5 class="title">{{ $course->name }}</h5>
                                <p class="desc">{{ Str::limit(strip_tags($course->description), 80) }}</p>
                                <div class="tags">
                                    <span class="tag">{{ $course->category->name ?? 'No Category' }}</span>
                                    <span class="tag">{{ $course->duration }}h</span>
                                    <div class="meta" style="margin-left:auto; gap:6px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                        </svg>
                                        <span>{{ $course->enrollments_count ?? 0 }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                        </svg>
                                        <span>4.8</span>
                                    </div>
                                </div>
                                <div class="author">
                                    @php
                                        $trainer = $course->trainer;
                                        $trainerName = $trainer ? ($trainer->full_name_with_title ?: $trainer->name) : 'idSpora Team';
                                        $trainerAvatar = $trainer ? $trainer->avatar_url : 'https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2';
                                        $trainerHref = $trainer ? route('public.trainer-profile.show', $trainer->id) : '#';
                                    @endphp
                                    <img src="{{ $trainerAvatar }}" alt="{{ $trainerName }}">
                                    <a href="{{ $trainerHref }}" style="text-decoration:none; color:inherit;">
                                        <h6 class="mb-0" style="font-size:13px; font-weight:600;">{{ $trainerName }}</h6>
                                    </a>
                                    <div style="margin-left:auto; display:flex; align-items:center; gap:6px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                            <path d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445" />
                                        </svg>
                                        <span style="font-size:13px;">{{ $course->modules->where('type','video')->count() }} videos</span>
                                    </div>
                                </div>
                                <div class="progress-wrapper">
                                    <div class="progress">
                                        <div class="progress-bar" style="width: {{ $pct }}%;"></div>
                                    </div>
                                    <p>{{ $pct }}% selesai</p>
                                </div>
                                <div class="btn-lanjut" role="button">Lanjutkan</div>
                            </div>
                        </article>
                    </a>
                </li>
            @empty
                <li>
                    <div class="text-center py-5" style="grid-column:1/-1;">
                        <h5 class="mb-2">Belum ada kursus untuk dilanjutkan</h5>
                        <p class="text-muted mb-0">Mulai belajar dari kursus pilihan di bawah.</p>
                    </div>
                </li>
            @endforelse
        </ul>
    </section>
    
    <section class="kursus-pelatihan">
        <div class="section-title">
            <h3>Kursus Pilihan</h3>
        </div>
        <ul class="course-list">
            @forelse($courses as $course)
            <li>
                @php
                    // Always go to detail first when clicking the card
                    $courseHref = route('course.detail', $course->id);
                @endphp
                <a href="{{ $courseHref }}" style="text-decoration:none;color:inherit;">
                <article class="course-card">
                    <div class="thumb-wrapper">
                        @php
                            $cardImage = $course->card_thumbnail ?? $course->media;
                        @endphp
                        @php
                            if ($cardImage) {
                                $imgSrc = str_starts_with($cardImage, 'http') ? $cardImage : asset('uploads/' . $cardImage);
                            } else {
                                $imgSrc = 'https://via.placeholder.com/300x200/4f46e5/ffffff?text=No+Image';
                            }
                        @endphp
                        <img class="thumb" src="{{ $imgSrc }}" alt="{{ $course->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        <div class="badge-save-group" style="gap:12px;">
                            <span class="course-badge {{ $course->level }}">{{ ucfirst($course->level) }}</span>
                            <button class="save-btn" aria-label="Save course" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M2 2v13.5l6-3 6 3V2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="inner">
                        <h5 class="title">{{ $course->name }}</h5>
                        <p class="desc">{{ Str::limit(strip_tags($course->description), 80) }}</p>
                        <div class="tags"> 
                            <span class="tag">{{ $course->category->name ?? 'No Category' }}</span> 
                            <span class="tag">{{ $course->duration }}h</span>
                            <div class="meta" style="margin-left:auto; gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                </svg>
                                <span>{{ $course->enrollments_count ?? 0 }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                                <span>4.8</span>
                            </div>
                        </div>
                        <div class="author"> 
                            <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2" alt="Profile">
                            <h6 class="mb-0" style="font-size:13px; font-weight:500;">idSpora Team</h6>
                            <div style="margin-left:auto; display:flex; align-items:center; gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445" />
                                </svg>
                                <span style="font-size:13px;">{{ $course->modules->where('type','video')->count() }} videos</span>
                            </div>
                        </div>
                        <div class="price-row">
                            <div class="price-col">
                                <span class="price-now">
                                    @if($course->hasDiscount())
                                        <div class="d-flex flex-column" style="line-height:1.2;">
                                            <span class="text-muted text-decoration-line-through mb-1" style="font-size: 11px; font-weight: normal; opacity: 0.7;">
                                                Rp{{ number_format($course->price, 0, ',', '.') }}
                                            </span>
                                            <span style="font-size: 16px;">Rp{{ number_format($course->discounted_price, 0, ',', '.') }}</span>
                                        </div>
                                    @elseif((int) ($course->price ?? 0) <= 0)
                                        GRATIS
                                    @else
                                        Rp{{ number_format($course->price, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            <a href="{{ $courseHref }}" class="btn-enroll" style="text-decoration:none;">Lihat Detail</a>
                        </div>
                    </div>
                </article>
                </a>
            </li>
            @empty
            <li>
                <div class="text-center py-5">
                    <h5 class="mb-3">Belum ada kursus tersedia</h5>
                    <p class="text-muted">Kursus akan segera hadir!</p>
                    </div>
            </li>
            @endforelse
        </ul>
        <div class="align-items-center" style="padding: 20px; text-align: center !important;">
            <a href="{{ route('courses.index') }}" class="btn btn-primary me-2" style="display:inline-block;">Lihat Semua Kursus</a>
        </div>
    </section>
</main>

    @if($courses->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $courses->onEachSide(1)->links('pagination::bootstrap-5') }}
        </div>
    @endif
   @include('partials.footer-after-login')
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>