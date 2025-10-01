@include('partials.navbar-before-login')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>idSpora - Home</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    <div class="search-banner-container">
        <form class="search-banner-form" action="#" method="get" autocomplete="off">
            <div class="search-wrap">
                <input id="site-search" class="form-control search-input" type="search" name="search"
                    placeholder="Search" aria-label="Search" aria-expanded="false" aria-controls="search-suggest">
                <span class="search-icon" aria-hidden="false">
                    <svg id="search-icon-svg" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                        fill="currentColor" viewBox="0 0 16 16" focusable="false" style="cursor:pointer;">
                        <path
                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                    </svg>
                </span>
                <!-- dropdown rekomendasi -->
                <ul id="search-suggest" class="search-suggest" role="listbox"></ul>
            </div>
        </form>
    </div>

    <section class="hero-carousel">
        <div id="carouselExampleInterval" class="carousel slide custom-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="10000">
                    <img src="https://img.freepik.com/vektor-premium/live-concert-horizontal-banner-template_23-2150997973.jpg"
                        class="d-block" alt="...">
                </div>
                <div class="carousel-item" data-bs-interval="2000">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRt2J3i17I7bpToDbbrbL6ULzX8IPnF7JJXiQ&s"
                        class="d-block" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="https://img.freepik.com/free-psd/horizontal-banner-template-jazz-festival-club_23-2148979704.jpg"
                        class="d-block" alt="...">
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <section class="testimoni-section">
        <div class="section-title">
            <h3>Apa Kata Mereka?</h3>
            <h6>Testimoni dari ribuan learner yang telah bergabung</h6>
        </div>

        <div class="testimoni-container">
            <button class="nav-btn nav-btn-left" id="prevBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                </svg>
            </button>
            
            <div class="testimoni-slider" id="testimoniSlider">
                <div class="testimoni-track" id="testimoniTrack">
                    <!-- Review Set 1 -->
                    <div class="testimoni-slide active">
                    <ul class="reviews">
                        <!-- Card 1 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Sarah Sechan</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Kursus React idSpora ini sangat lengkap dan mudah dipahami.
                                        Instrukturnya berpengalaman dan selalu siap membantu.</p>
                                </div>
                            </div>
                        </li>

                        <!-- Card 2 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Sarah Sechan</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Kursus React idSpora ini sangat lengkap dan mudah dipahami.
                                        Instrukturnya berpengalaman dan selalu siap membantu.</p>
                                </div>
                            </div>
                        </li>

                        <!-- Card 3 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Ahmad Jani</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Materi UI/UX Design sangat praktis dan langsung bisa
                                        diterapkan di pekerjaan. Recommended banget!</p>
                                </div>
                            </div>
                        </li>

                        <!-- Card 4 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Vero Glorify</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Python untuk Data Science di sini sangat comprehensive. Dari
                                        basic sampai advanced semua ada.</p>
                                </div>
                            </div>
                        </li>

                        <!-- Card 5 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Agnes Mauaja</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Kursus Business Analysis membantu saya memahami proses bisnis
                                        dengan lebih baik. Terima kasih Idspora!</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                    <!-- Review Set 2 -->
                    <div class="testimoni-slide">
                    <ul class="reviews">
                        <!-- Card 5 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Agnes Mauaja</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Kursus Business Analysis membantu saya memahami proses bisnis
                                        dengan lebih baik. Terima kasih Idspora!</p>
                                </div>
                            </div>
                        </li>

                        <!-- Card 4 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Vero Glorify</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Python untuk Data Science di sini sangat comprehensive. Dari
                                        basic sampai advanced semua ada.</p>
                                </div>
                            </div>
                        </li>

                        <!-- Card 3 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Ahmad Jani</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Materi UI/UX Design sangat praktis dan langsung bisa
                                        diterapkan di pekerjaan. Recommended banget!</p>
                                </div>
                            </div>
                        </li>

                        <!-- Card 2 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Sarah Sechan</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Kursus React idSpora ini sangat lengkap dan mudah dipahami.
                                        Instrukturnya berpengalaman dan selalu siap membantu.</p>
                                </div>
                            </div>
                        </li>

                        <!-- Card 1 -->
                        <li>
                            <div class="card">
                                <div class="card-body">
                                    <div class="reviewer">
                                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                            alt="Profile">
                                        <h6 class="reviewer-name">Sarah Sechan</h6>
                                    </div>
                                    <div class="stars" aria-label="5 stars">
                                        @for ($i = 0; $i < 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="review-text">Kursus React idSpora ini sangat lengkap dan mudah dipahami.
                                        Instrukturnya berpengalaman dan selalu siap membantu.</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                </div>
            </div>
            
            <button class="nav-btn nav-btn-right" id="nextBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                </svg>
            </button>
        </div>
    </section>

    <section class="pelatihan">
        <div class="section-title">
            <h3>Jenis Pelatihan</h3>
            <h6>Pilih kategori yang sesuai dengan minat dan kebutuhan Anda</h6>
        </div>

        <ul class="kategori-list">
            <li>
                <div class="kategori-item">Artificial Intelligence</div>
            </li>
            <li>
                <div class="kategori-item">Machine Learning</div>
            </li>
            <li>
                <div class="kategori-item">Mental Health</div>
            </li>
            <li>
                <div class="kategori-item">Digital Marketing</div>
            </li>
            <li>
                <div class="kategori-item">Graphic Design</div>
            </li>
            <li>
                <div class="kategori-item wide">Business</div>
            </li>
        </ul>
    </section>

    <section class="kursus-pelatihan">
        <div class="section-title">
            <h3>Kursus Pilihan</h3>
            <h6>Kursus terpopuler dengan rating tertinggi</h6>
        </div>

        <ul class="course-list">
            @forelse($featuredCourses as $course)
            <li>
                <article class="course-card">
                    <div class="thumb-wrapper">
                        @if($course->image)
                            <img class="thumb" src="{{ Storage::url($course->image) }}" alt="{{ $course->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        @else
                            <img class="thumb" src="https://via.placeholder.com/300x200/4f46e5/ffffff?text=No+Image" alt="{{ $course->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        @endif
                        <div class="badge-save-group" style="gap:12px;">
                            <span class="course-badge {{ $course->level }}">{{ ucfirst($course->level) }}</span>
                            <button class="save-btn" aria-label="Save course">
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
                                <span>{{ $course->modules->count() }}</span>
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
                                <span style="font-size:13px;">{{ $course->modules->count() }} videos</span>
                            </div>
                        </div>
                        <div class="price-row">
                            <div class="price-col">
                                <span class="price-now">Rp{{ number_format($course->price, 0, ',', '.') }}</span>
                            </div>
                            <button class="btn-enroll">Enroll Now</button>
                        </div>
                    </div>
                </article>
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
            <a href="#" class="btn btn-primary me-2" style="display:inline-block;">Lihat Semua Kursus</a>
        </div>
    </section>

    <section class="event">
        <div class="section-title">
            <h3>Event & Webinar</h3>
            <h6>Jadwal event dan webinar terbaru dari idSpora</h6>
        </div>
        <div class="event-list">
            @forelse($featuredEvents as $event)
            <div class="card-event">
                <div class="event-poster">
                    @if($event->image)
                        <img class="event-poster-img" src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}">
                    @else
                        <img class="event-poster-img" src="https://via.placeholder.com/600x800/4f46e5/ffffff?text=No+Image" alt="{{ $event->title }}">
                    @endif
                    <button class="save-btn save-btn--event" aria-label="Save event" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 2v13.5l6-3 6 3V2z" />
                        </svg>
                    </button>
                   
                </div>

                <div class="card-body">
                    <h4>{{ $event->title }}</h4>
                    <div class="tags">
                        <span class="tag">Event</span> 
                        <span class="tag">{{ $event->speaker }}</span>
                    </div>
                    <p class="desc-event">{{ Str::limit(strip_tags($event->description), 80) }}</p>

                    <div class="keterangan keterangan-row">
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-calendar-event" viewBox="0 0 16 16">
                                <path
                                    d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                <path
                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            <span>{{ $event->event_date->format('d F Y') }}</span>
                        </div>
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                            </svg>
                            <span>{{ $event->location }}</span>
                        </div>
                    </div>
                    <div class="price-row">
                        <div class="price-col">
                            @if($event->hasDiscount())
                                <span class="price-old">Rp{{ number_format($event->price, 0, ',', '.') }}</span>
                                <span class="price-now">Rp{{ number_format($event->discounted_price, 0, ',', '.') }}</span>
                            @else
                                <span class="price-now">Rp{{ number_format($event->price, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        @auth
                            <button class="btn-register" type="button" onclick="window.location='{{ route('events.show', $event->id) }}'">Daftar Sekarang</button>
                        @endauth
                        @guest
                            <button class="btn-register need-login" type="button" data-event-id="{{ $event->id }}">Daftar Sekarang</button>
                        @endguest
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <h5 class="mb-3">Belum ada event tersedia</h5>
                <p class="text-muted">Event akan segera hadir!</p>
            </div>
            @endforelse
        </div>
        <div class="align-items-center" style="padding: 20px 0; text-align: center !important;">
            <a href="#" class="btn btn-primary">Lihat Semua Event</a>
        </div>
    </section>

    @guest
    <!-- Modal: Login Required -->
    <div class="modal fade" id="loginRequiredModal" tabindex="-1" aria-labelledby="loginRequiredLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginRequiredLabel">Butuh Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-shrink-0" style="width:46px;height:46px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:#eef2ff;color:#4f46e5;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                <path fill="#fff" d="M8.93 6.588a.5.5 0 0 0-.832-.374L5.5 8.293V9.5a.5.5 0 0 0 .5.5h.793l2.105-2.105a.5.5 0 0 0 .032-.707z"/>
                            </svg>
                        </div>
                        <div>
                            <h6 class="mb-1">Anda belum login</h6>
                            <p class="mb-0 text-muted" style="font-size:.9rem;">Silakan login terlebih dahulu untuk mendaftar atau mengikuti event ini.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Nanti Saja</button>
                    <a href="{{ route('login') }}" class="btn btn-primary">Login Sekarang</a>
                </div>
            </div>
        </div>
    </div>
    @endguest

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const needLoginButtons = document.querySelectorAll('.need-login');
        if(needLoginButtons.length){
            needLoginButtons.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const modalEl = document.getElementById('loginRequiredModal');
                    if(window.bootstrap && modalEl){
                        const m = new bootstrap.Modal(modalEl);
                        m.show();
                        animateModal(modalEl);
                    } else {
                        // Custom animated fallback (NO native alert)
                        createAnimatedLoginPrompt();
                    }
                });
            });
        }
    });

    function animateModal(modalEl){
        const dialog = modalEl.querySelector('.modal-dialog');
        if(!dialog) return;
        dialog.classList.add('modal-animate-in');
        dialog.addEventListener('animationend', ()=> dialog.classList.remove('modal-animate-in'), {once:true});
    }

    function createAnimatedLoginPrompt(){
        if(document.querySelector('.login-fallback-overlay')) return;
        const overlay = document.createElement('div');
        overlay.className = 'login-fallback-overlay';
        overlay.innerHTML = `
            <div class="login-fallback-card" role="dialog" aria-modal="true" aria-label="Butuh Login">
                <button type="button" class="close-fallback" aria-label="Tutup">&times;</button>
                <div class="icon-wrap">
                    <svg width="46" height="46" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="36" cy="36" r="34" class="pulse" fill="#EEF2FF" stroke="#4f46e5" stroke-width="2" />
                        <path d="M30 37.5 34.5 42 44 32" stroke="#4f46e5" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="draw-check" />
                    </svg>
                </div>
                <h5 class="mb-2">Anda belum login</h5>
                <p class="text-muted mb-3 small">Login untuk mendaftar & mengikuti event ini.</p>
                <div class="d-flex gap-2 flex-wrap">
                    
                    <a href="{{ route('login') }}" class="btn btn-primary btn-sm flex-grow-1">Login Sekarang</a>
                </div>
            </div>`;
        document.body.appendChild(overlay);
        requestAnimationFrame(()=> overlay.classList.add('show'));
        overlay.addEventListener('click', (e)=>{
            if(e.target.classList.contains('login-fallback-overlay') || e.target.classList.contains('close-fallback')){
                overlay.classList.remove('show');
                overlay.classList.add('closing');
                setTimeout(()=> overlay.remove(), 320);
            }
        });
    }
    </script>
    <style>
    /* Modal animation enhancement */
    #loginRequiredModal .modal-dialog { animation-duration:.65s; animation-fill-mode:both; }
    .modal-animate-in { animation: modalPop .65s cubic-bezier(.16,.8,.24,1); }
    @keyframes modalPop {0%{transform:translateY(28px) scale(.9);opacity:0;}50%{transform:translateY(-4px) scale(1.01);}70%{transform:translateY(0) scale(.998);}100%{transform:translateY(0) scale(1);opacity:1;}}

    /* Fallback overlay (when Bootstrap JS absent) */
    .login-fallback-overlay {position:fixed;inset:0;display:flex;align-items:center;justify-content:center;background:rgba(17,24,39,.38);backdrop-filter:blur(4px);opacity:0;transition:opacity .3s ease;z-index:1200;}
    .login-fallback-overlay.show {opacity:1;}
    .login-fallback-overlay.closing {opacity:0;}
    .login-fallback-card {width:100%;max-width:340px;background:#fff;border-radius:20px;padding:1.4rem 1.3rem 1.6rem;box-shadow:0 20px 40px -16px rgba(0,0,0,.25),0 4px 10px -2px rgba(0,0,0,.12);position:relative;animation:fallbackCardIn .6s cubic-bezier(.16,.8,.24,1);text-align:center;}
    @keyframes fallbackCardIn {0%{transform:translateY(28px) scale(.9);opacity:0;}55%{transform:translateY(-6px) scale(1.02);}75%{transform:translateY(0) scale(.995);}100%{transform:translateY(0) scale(1);opacity:1;}}
    .login-fallback-card h5 {font-weight:600;letter-spacing:.3px;}
    .login-fallback-card .icon-wrap {width:72px;height:72px;border-radius:24px;margin:0 auto 10px;display:flex;align-items:center;justify-content:center;position:relative;}
    .login-fallback-card .icon-wrap .pulse {animation:pulseRing 2s ease-out infinite;}
    @keyframes pulseRing {0%{transform:scale(.6);opacity:.55;}70%{transform:scale(1);opacity:0;}100%{opacity:0;}}
    .login-fallback-card .draw-check {stroke-dasharray:40;stroke-dashoffset:40;animation:drawCheck 1s ease forwards .35s;}
    @keyframes drawCheck {to{stroke-dashoffset:0;}}
    .login-fallback-card .close-fallback {position:absolute;top:6px;right:10px;background:none;border:none;font-size:1.35rem;line-height:1;color:#888;cursor:pointer;transition:color .2s, transform .2s;}
    .login-fallback-card .close-fallback:hover {color:#111;transform:scale(1.1);} 
    @media (max-width:520px){.login-fallback-card{margin:0 1rem;border-radius:18px;}}
    /* Adjust event card image size & spacing */
    .event-list .card-event .event-poster {position:relative; height: 340px; /* increased from previous ~200-260 */}
    .event-list .card-event .event-poster-img {width:100%; height:100%; object-fit:cover;}
    /* Slightly larger grid items vertically */
    .event-list {row-gap:34px;}
    /* If card body needs more top separation from image */
    .event-list .card-event .card-body {padding-top:18px;}
    @media (max-width: 768px){
        .event-list .card-event .event-poster {height:280px;}
    }
    </style>

    <section class="partner">
        <div class="section-title">
            <h3>Partner Kami</h3>
        </div>
        <div class="partner-logos">
            <img src="{{ asset('images/logo-telkom-indonesia.png') }}" alt="Telkom Indonesia">
            <img src="{{ asset('images/logoD3SI.webp') }}" alt="D3 Sistem Informasi">
            <img src="{{ asset('images/logo-padepokan-79.png') }}" alt="Padepokan 79">
            <img src="{{ asset('images/logo-ableid.png') }}" alt="Able.id">
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('testimoniSlider');
            const track = document.getElementById('testimoniTrack');
            const slides = document.querySelectorAll('.testimoni-slide');
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            let currentSlide = 0;
            let autoSlideInterval;
            
            // Function to show specific slide
            function showSlide(index) {
                slides.forEach((slide, i) => {
                    slide.classList.toggle('active', i === index);
                });
                
                // Smooth transition
                track.style.transform = `translateX(-${index * 100}%)`;
            }
            
            // Function to go to next slide
            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }
            
            // Function to go to previous slide
            function prevSlide() {
                currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(currentSlide);
            }
            
            // Auto slide every 10 seconds
            function startAutoSlide() {
                autoSlideInterval = setInterval(nextSlide, 10000);
            }
            
            // Stop auto slide
            function stopAutoSlide() {
                clearInterval(autoSlideInterval);
            }
            
            // Event listeners for navigation buttons
            nextBtn.addEventListener('click', function() {
                stopAutoSlide();
                nextSlide();
                startAutoSlide();
            });
            
            prevBtn.addEventListener('click', function() {
                stopAutoSlide();
                prevSlide();
                startAutoSlide();
            });
            
            // Pause auto slide on hover
            slider.addEventListener('mouseenter', stopAutoSlide);
            slider.addEventListener('mouseleave', startAutoSlide);
            
            // Initialize
            showSlide(0);
            startAutoSlide();
        });
        
        // Scroll reveal animation
        function reveal() {
            var reveals = document.querySelectorAll('.reveal');
            
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add('active');
                }
            }
        }
        
        // Add reveal class to sections
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.kursus-pelatihan, .testimoni-section, .partner, .footer-section');
            sections.forEach(section => {
                section.classList.add('reveal');
            });
            
            // Initial reveal check
            reveal();
        });
        
        // Listen for scroll events
        window.addEventListener('scroll', reveal);
        
        // Parallax effect for hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = document.querySelector('.hero-section');
            if (parallax) {
                const speed = scrolled * 0.5;
                parallax.style.transform = `translateY(${speed}px)`;
            }
        });
    </script>
</body>

</html>
@include('partials.footer-after-login')