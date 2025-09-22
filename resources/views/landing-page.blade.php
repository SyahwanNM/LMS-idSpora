@include('partials.navbar-before-login')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>idSpora - Home</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body class="bg-split-horizontal">
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

        <div id="testimoniCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500"
            data-bs-pause="false" data-bs-touch="true">
            <div class="carousel-inner">

                <!-- SLIDE 1: 1-2-3-4-5 -->
                <div class="carousel-item active">
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

                <!-- SLIDE 2: 5-4-3-2-1 (dibalik) -->
                <div class="carousel-item">
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
                                </svg>
                                <span style="font-size:13px;">10 videos</span>
                            </div>
                        </div>
                        <div class="price-row">
                            <div class="price-col">
                                <span class="price-old">Rp650.000</span>
                                <span class="price-now">Rp150.000</span>
                            </div>
                            <button class="btn-enroll">Enroll Now</button>
                        </div>
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
                            <div style="margin-left:auto; display:flex; align-items:center; gap:6px;"> <svg
                                    xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445" />
                                </svg> <span style="font-size:13px;">10 videos</span> </div>
                        </div>
                        <div class="price-row">
                            <div class="price-col">
                                <span class="price-old">Rp650.000</span>
                                <span class="price-now">Rp150.000</span>
                            </div>
                            <button class="btn-enroll">Enroll Now</button>
                        </div>
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
                            <div style="margin-left:auto; display:flex; align-items:center; gap:6px;"> <svg
                                    xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="M6.271 5.055a.5.5 0 0 1 .52.038l3.5 2.5a.5.5 0 0 1 0 .814l-3.5 2.5A.5.5 0 0 1 6 10.5v-5a.5.5 0 0 1 .271-.445" />
                                </svg> <span style="font-size:13px;">10 videos</span> </div>
                        </div>
                        <div class="price-row">
                            <div class="price-col">
                                <span class="price-old">Rp650.000</span>
                                <span class="price-now">Rp150.000</span>
                            </div>
                            <button class="btn-enroll">Enroll Now</button>
                        </div>
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
                        <div class="price-row">
                            <div class="price-col">
                                <span class="price-old">Rp100.000</span>
                                <span class="price-now">Rp75.000</span>
                            </div>
                            <button class="btn-enroll">Enroll Now</button>
                        </div>
                    </div>
                </article>
            </li>
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
            <div class="card-event">
                <div class="thumb-wrapper">
                    <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
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

                <div class="card-body">
                    <h4>AI For Lectures</h4>
                    <div class="tags"> <span class="tag">Workshop</span> <span class="tag">AI</span>
                        <div class="meta" style="margin-left:auto; gap:6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                            </svg>
                            <span>118</span>
                        </div>
                    </div>
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                        eiusmod
                        tempor
                        incididunt...</p>

                    <div class="keterangan keterangan-row">
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-calendar-event" viewBox="0 0 16 16">
                                <path
                                    d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                <path
                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            <span>04 September 2025</span>
                        </div>
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                            </svg>
                            <span>Bandung • 09.00 WIB</span>
                        </div>
                    </div>
                    <div class="price-row">
                        <div class="price-col">
                            <span class="price-old">Rp650.000</span>
                            <span class="price-now">Rp150.000</span>
                        </div>
                        <button class="btn-register">Register</button>
                    </div>
                </div>
            </div>
            <div class="card-event">
                <div class="thumb-wrapper">
                    <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
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

                <div class="card-body">
                    <h3>AI For Lectures</h3>
                    <div class="tags">
                        <span class="tag">Workshop</span>
                        <span class="tag">AI</span>
                        <div class="meta" style="margin-left:auto; gap:6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                            </svg>
                            <span>118</span>
                        </div>
                    </div>
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                        eiusmod
                        tempor
                        incididunt...</p>

                    <div class="keterangan keterangan-row">
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-calendar-event" viewBox="0 0 16 16">
                                <path
                                    d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                <path
                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            <span>04 September 2025</span>
                        </div>
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                            </svg>
                            <span>Bandung • 09.00 WIB</span>
                        </div>
                    </div>
                    <div class="price-row">
                        <div class="price-col">
                            <span class="price-old">Rp100.000</span>
                            <span class="price-now">Rp75.000</span>
                        </div>
                        <button class="btn-register">Register</button>
                    </div>
                </div>
            </div>
            <div class="card-event">
                <div class="thumb-wrapper">
                    <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
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
                <div class="card-body">
                    <h3>AI For Lectures</h3>

                    <div class="tags">
                        <span class="tag">Workshop</span>
                        <span class="tag">AI</span>
                        <div class="meta" style="margin-left:auto; gap:6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                            </svg>
                            <span>118</span>
                        </div>
                    </div>
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                        eiusmod
                        tempor
                        incididunt...</p>

                    <div class="keterangan keterangan-row">
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-calendar-event" viewBox="0 0 16 16">
                                <path
                                    d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                <path
                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            <span>04 September 2025</span>
                        </div>
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                            </svg>
                            <span>Bandung • 09.00 WIB</span>
                        </div>
                    </div>
                    <div class="price-row">
                        <div class="price-col">
                            <span class="price-old">Rp100.000</span>
                            <span class="price-now">Rp75.000</span>
                        </div>
                        <button class="btn-register">Register</button>
                    </div>
                </div>
            </div>
            <div class="card-event">
                <div class="thumb-wrapper">
                    <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
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

                <div class="card-body">
                    <h5>AI For Lectures</h5>

                    <div class="tags">
                        <span class="tag">Workshop</span>
                        <span class="tag">AI</span>
                        <div class="meta" style="margin-left:auto; gap:6px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                            </svg>
                            <span>118</span>
                        </div>
                    </div>
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                        eiusmod
                        tempor
                        incididunt...</p>

                    <div class="keterangan keterangan-row">
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-calendar-event" viewBox="0 0 16 16">
                                <path
                                    d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                <path
                                    d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            <span>04 September 2025</span>
                        </div>
                        <div class="keterangan-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                <path
                                    d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                            </svg>
                            <span>Bandung • 09.00 WIB</span>
                        </div>
                    </div>
                    <div class="price-row">
                        <div class="price-col">
                            <span class="price-old">Rp100.000</span>
                            <span class="price-now">Rp75.000</span>
                        </div>
                        <button class="btn-register">Register</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="align-items-center" style="padding: 20px 0; text-align: center !important;">
            <a href="#" class="btn btn-primary">Lihat Semua Event</a>
        </div>
    </section>

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

</body>

</html>
@include('partials.footer-after-login')