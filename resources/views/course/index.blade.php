<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2Pkf3BD3vO5e5pSxb6YV9jwWTA/gG05Jg9TLEbiFU6BxZ1S3XmGmGC3w9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }
        .hero-carousel {
            margin-top: 100px; 
        }

        .footer-section {
            width: 100vw;
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -50vw;
            margin-right: -50vw;
            margin-top: 40px; /* Jarak antara konten terakhir dengan footer */
        }
    </style>
</head>

<body>
    @include ('partials.navbar-after-login')

    <div class="container-fluid page-content pb-5">
        <div id="carouselCaptions" class="carousel slide rounded-4 overflow-hidden mb-4" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="0" class="active"
                        aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselCaptions" data-bs-slide-to="2"
                        aria-label="Slide 3"></button>
                </div>

                <div class="carousel-inner">
                    <!-- Slide 1 -->
                    <div class="carousel-item active" style="height: clamp(250px, 40vh, 420px); position: relative;">
                        <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1600&auto=format&fit=crop"
                            alt="Slide 1"
                            style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; filter:brightness(0.6);">

                        <div class="carousel-caption text-start" style="bottom: 40px; left: 60px;">
                            <h2 class="fw-bold">Upgrade Skill Digitalmu</h2>
                            <p>Belajar langsung dari praktisi industri dengan kurikulum relevan.</p>
                            <button class="btn btn-warning fw-bold">Mulai Sekarang</button>
                        </div>
                    </div>

                    <!-- Slide 2 -->
                    <div class="carousel-item" style="height: clamp(250px, 40vh, 420px); position: relative;">
                        <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=1600&auto=format&fit=crop"
                            alt="Slide 2"
                            style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; filter:brightness(0.6);">

                        <div class="carousel-caption text-start" style="bottom: 40px; left: 60px;">
                            <h2 class="fw-bold">Webinar AI Masa Depan</h2>
                            <p>Diskusi panel eksklusif bersama expert global.</p>
                            <button class="btn btn-light fw-bold">Daftar Sekarang</button>
                        </div>
                    </div>

                    <!-- Slide 3 -->
                    <div class="carousel-item" style="height: clamp(250px, 40vh, 420px); position: relative;">
                        <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?q=80&w=1600&auto=format&fit=crop"
                            alt="Slide 3"
                            style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; filter:brightness(0.6);">

                        <div class="carousel-caption text-start" style="bottom: 40px; left: 60px;">
                            <h2 class="fw-bold">Sertifikasi & Career Path</h2>
                            <p>Bangun portofolio dan karier profesionalmu.</p>
                            <button class="btn btn-outline-light fw-bold">Lihat Program</button>
                        </div>
                    </div>
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

        <div class="row justify-content-center mb-5" style="margin-top: -30px; position: relative; z-index: 10;">
            <div class="col-lg-8">
                <form action="#" class="d-flex bg-white rounded-pill p-2 shadow-sm border">
                    <input class="form-control border-0 rounded-pill ps-4 py-2" type="search" placeholder="Cari event berdasarkan judul, pembicara atau kategori..." aria-label="Search" style="box-shadow: none;">
                    <button class="btn rounded-pill px-4 fw-bold" type="submit" style="background-color: #51376c; color: white;">
                        Cari
                    </button>
                </form>
            </div>
        </div>
    
    <div class="filter-container">
        <div class="filter-box">
            <div class="options">
                <label>Level</label>
                <select>
                    <option>Beginner</option>
                    <option>Intermediate</option>
                    <option>Advanced</option>
                </select>
            </div>
            <div class="options">
                <label>Place</label>
                <select>
                    <option>Bandung</option>
                    <option>Jakarta</option>
                    <option>Bekasi</option>
                </select>
            </div>
            <div class="options">
                <label>Price</label>
                <select>
                    <option>Low to High</option>
                    <option>High to Low</option>
                </select>
            </div>
        </div>
        <div class="search-container">
            <form class="search-form" action="#" method="get" autocomplete="off">
                <div class="search-wrap">
                    <input id="site-search" class="form-control search-input-2" type="search" name="search"
                        placeholder="Search" aria-label="Search" aria-expanded="false" aria-controls="search-suggest">
                    <span class="search-icon" ariza-hidden="false">
                        <svg id="search-icon-svg" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                            fill="currentColor" viewBox="0 0 16 16" focusable="false" style="cursor:pointer;">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                        </svg>
                    </span>

                    <ul id="search-suggest" class="search-suggest" role="listbox"></ul>
                </div>
            </form>
        </div>
    </div>

    <section class="kursus-pelatihan">
        <div class="header-card">
            <h3>Lanjutkan Belajar</h3>
        </div>

        <ul class="course-list">
            <li>
                <article class="course-card">
                    <div class="thumb-wrapper">
                        <img class="thumb"
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSCIDIbCVbsnQYeBqKi7-yTQpyeMCH02BEug&s"
                            alt="thumb">
                        <div class="badge-save-group" style="gap:12px;">
                            <span class="course-badge beginner">Beginner</span>
                            <button class="save-btn" aria-label="Save course">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M2 2v13.5l6-3 6 3V2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="inner">
                        <h5 class="title">Learn Artificial Intelligence Python</h5>
                        <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididun</p>
                        <div class="tags"> <span class="tag">Programming</span> <span class="tag">AI</span>
                            <div class="meta" style="margin-left:auto; gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                </svg>
                                <span>118</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                                <span>5.0</span>
                            </div>
                        </div>
                        <div class="author"> <img
                                src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                alt="Profile">
                            <h6 class="mb-0" style="font-size:13px; font-weight:500;">Agnes Mauaja</h6>
                            <div style="margin-left:auto; display:flex; align-items:center; gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445" />
                                </svg> <span style="font-size:13px;">10 videos</span>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <div class="progress">
                                <div class="progress-bar"></div>
                            </div>
                            <p>30% selesai</p>
                        </div>
                        <button class="btn-lanjut">Lanjutkan</button>
                    </div>
                </article>
            </li>
            <li>
                <article class="course-card">
                    <div class="thumb-wrapper">
                        <img class="thumb"
                            src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSCIDIbCVbsnQYeBqKi7-yTQpyeMCH02BEug&s"
                            alt="thumb">
                        <div class="badge-save-group" style="gap:12px;">
                            <span class="course-badge beginner">Beginner</span>
                            <button class="save-btn" aria-label="Save course">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M2 2v13.5l6-3 6 3V2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="inner">
                        <h5 class="title">Learn Artificial Intelligence Python</h5>
                        <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididun</p>
                        <div class="tags"> <span class="tag">Programming</span> <span class="tag">AI</span>
                            <div class="meta" style="margin-left:auto; gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                </svg>
                                <span>118</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                                <span>5.0</span>
                            </div>
                        </div>
                        <div class="author"> <img
                                src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                                alt="Profile">
                            <h6 class="mb-0" style="font-size:13px; font-weight:500;">Agnes Mauaja</h6>
                            <div style="margin-left:auto; display:flex; align-items:center; gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445" />
                                </svg> <span style="font-size:13px;">10 videos</span>
                            </div>
                        </div>
                        <div class="progress-wrapper">
                            <div class="progress">
                                <div class="progress-bar"></div>
                            </div>
                            <p>30% selesai</p>
                        </div>
                        <button class="btn-lanjut">Lanjutkan</button>
                    </div>
                </article>
            </li>
        </ul>
    </section>
    
    <section class="kursus-pelatihan">
        <div class="section-title">
            <h3>Kursus Pilihan</h3>
            <h6>Kursus terpopuler dengan rating tertinggi</h6>
        </div>

        @php
            $publishedFeaturedCourses = isset($featuredCourses)
                ? collect($featuredCourses)->filter(function($c){ return ($c->status ?? null) === 'active'; })
                : collect();
        @endphp
        <ul class="course-list">
            @forelse($publishedFeaturedCourses as $course)
            <li>
                <a href="{{ route('course.detail', $course->id) }}" style="text-decoration:none;color:inherit;">
                <article class="course-card">
                    <div class="thumb-wrapper">
                        @php
                            $cardImage = $course->card_thumbnail ?? $course->image;
                        @endphp
                        @if($cardImage)
                            <img class="thumb" src="{{ Storage::url($cardImage) }}" alt="{{ $course->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        @else
                            <img class="thumb" src="https://via.placeholder.com/300x200/4f46e5/ffffff?text=No+Image" alt="{{ $course->name }}" style="width: 100%; height: 200px; object-fit: cover;">
                        @endif
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
                            <a href="{{ route('course.detail', $course->id) }}" class="btn-enroll" style="text-decoration:none;">Enroll Now</a>
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
            <a href="#" class="btn btn-primary me-2" style="display:inline-block;">Lihat Semua Kursus</a>
        </div>
    </section>

    <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item">
                    <a class="page-link" href="#" id="prevBtn" aria-label="Previous">
                        <span aria-hidden="true">&lt;</span>
                    </a>
                </li>
                <li class="page-item active"><a class="page-link" href="javascript:void(0)" data-page="1">1</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="2">2</a></li>
                <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page="2">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true">&gt;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pageLinks = document.querySelectorAll('.pagination .page-link[data-page]');
            const paginationContainer = document.querySelector('.pagination');
            const eventLists = document.querySelectorAll('.event-list');

            eventLists.forEach((list, index) => {
                if (list.id !== 'page-1') {
                    list.style.display = 'none';
                }
            });

            paginationContainer.addEventListener('click', function(e) {
                const clickedElement = e.target.closest('.page-link');
                if (!clickedElement) return;

                e.preventDefault();

                const targetPage = clickedElement.getAttribute('data-page');

                if (targetPage) {
                    document.querySelectorAll('.pagination .page-item').forEach(item => {
                        item.classList.remove('active');
                    });

                    eventLists.forEach(list => {
                        if (list.id === 'page-' + targetPage) {
                            list.style.display = 'grid';
                        } else {
                            list.style.display = 'none';
                        }
                    });
                    clickedElement.closest('.page-item').classList.add('active');
                }
            });
        });
    </script>
    @include('partials.footer-before-login')
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>