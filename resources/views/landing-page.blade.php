@include('partials.navbar-before-login')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>idSpora - Home</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<style>
    .box3 {
        padding: 20px;
    }

    .box3 .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .box3 .header h3 {
        margin-left: 40px;
        padding: 5px;
        font-weight: 500;
    }

    .box3 .header a {
        color: #f4c542;
        text-decoration: none;
    }

    .card-container-course {
        display: flex;
        gap: 30px;
        overflow-x: auto;
        padding-bottom: 10px;
        scroll-behavior: smooth;
        padding-left: 16px;
    }

    .card-container-course::-webkit-scrollbar {
        display: none;
    }

    .card-course {
        flex: 0 0 auto;
        background: #fff;
        padding: 16px;
        border: 1px solid #cfd8dc;
        border-radius: 12px;
        overflow: hidden;
        width: 350px;
        border: 2px solid #f4c542;
        display: flex;
        flex-direction: column;
    }

    .card-image-course-wrapper {
        position: relative;
    }

    .bookmark {
        position: absolute;
        top: 147px;
        right: 0px;
        width: 25px;
        height: auto;
        cursor: pointer;
    }

    .badge {
        position: absolute;
        top: 152px;
        right: 35px;
        background: #34C75930;
        border: 1px solid white;
        color: #34C759;
        padding: 3px 8px;
        font-size: 12px;
        border-radius: 12px;
    }

    .card-body {
        padding: 12px;
        display: flex;
        flex-direction: column;
    }

    .card-body h4 {
        font-size: 15px;
        font-weight: bold;
        margin: 8px 0 4px;
    }

    .card-body .desc {
        font-size: 13px;
        color: #666;
        margin-bottom: 10px;
        line-height: 1.4em;
        height: 38px;
        overflow: hidden;
    }

    .tags {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .tags-left {
        display: flex;
        gap: 6px;
    }

    .tags-left span {
        background: #eceff1;
        font-size: 13px;
        padding: 2px 8px;
        border-radius: 10px;
    }

    .tags-right {
        display: flex;
        gap: 10px;
        font-size: 14px;
        color: #444;
    }

    .tags-right div {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .tags-right img {
        width: 14px;
        height: 14px;
    }

    .info {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        margin: 6px 0;
    }

    .info div {
        display: flex;
        align-items: center;
        gap: 4px;
        color: #444;
    }

    .info img {
        width: 14px;
        height: 14px;
    }

    .pembicara {
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 8px 0;
    }

    .pembicara-left {
        display: flex;
        gap: 6px;
    }

    .pembicara-left span {
        font-size: 12px;
        margin-top: 5px;
        border-radius: 10px;
    }

    .pembicara-right {
        display: flex;
        gap: 10px;
        font-size: 13px;
        color: #444;
    }

    .pembicara-right div {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .pembicara-right img {
        width: 14px;
        height: 14px;
        margin-left: 70px;
    }

    .progress-wrapper {
        margin-bottom: 8px;
    }

    .progress {
        height: 6px;
        background: #e0e0e0;
        border-radius: 6px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        width: 30%;
        background: #f4c542;
    }

    .progress-wrapper p {
        font-size: 12px;
        margin-top: 4px;
    }

    .btn-lanjut {
        width: 100%;
        padding: 10px;
        background: #1e2a57;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
    }

    .box4 {
        padding: 20px;
    }

    .box4 .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .box4 .header h3 {
        margin-left: 40px;
        padding: 5px;
        font-weight: 500;
    }

    .box4 .header a {
        color: #f4c542;
        text-decoration: none;
    }

    .card-container-event {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 10px;
        scroll-behavior: smooth;
        padding-left: 16px;

    }

    .card-container-event::-webkit-scrollbar {
        display: none;
    }

    .card-event {
        flex: 0 0 auto;
        width: 315px;
        border: 2px solid #f4c542;
        border-radius: 12px;
        background: #fff;
        padding: 16px;
    }

    .card-image-event {
        width: 280px;
        height: 320px;
        object-fit: cover;
        border-radius: 10px;
    }

    .card-image-event-wrapper {
        position: relative;
    }

    .bookmark-event {
        position: absolute;
        top: 270px;
        right: 10px;
        width: 30px;
        height: auto;
        cursor: pointer;
    }

    .badge-event {
        position: absolute;
        top: 274px;
        right: 50px;
        background: #34C75940;
        border: 1px solid white;
        color: #34C759;
        padding: 3px 8px;
        font-size: 12px;
        border-radius: 12px;
    }

    .desc-event {
        font-size: 14px;
        color: #666;
        margin: 10px 0;
        line-height: 1.4em;
        height: 38px;
        overflow: hidden;
    }
</style>

<body>

    {{-- SECTION: Hero Carousel --}}
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

        <ul class="reviews">
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            @endfor
                        </div>
                        <p class="review-text">Kursus React idSpora ini sangat lengkap dan mudah dipahami. Instrukturnya
                            berpengalaman dan selalu siap membantu.</p>
                    </div>
                </div>
            </li>

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
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            @endfor
                        </div>
                        <p class="review-text">Materi UI/UX Design sangat praktis dan langsung bisa diterapkan di
                            pekerjaan. Recommended banget!</p>
                    </div>
                </div>
            </li>

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
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            @endfor
                        </div>
                        <p class="review-text">Python untuk Data Science di sini sangat comprehensive. Dari basic sampai
                            advanced semua ada.</p>
                    </div>
                </div>
            </li>

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
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.32-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.63.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                                </svg>
                            @endfor
                        </div>
                        <p class="review-text">Kursus Business Analysis membantu saya memahami proses bisnis dengan
                            lebih baik. Terima kasih Idspora!</p>
                    </div>
                </div>
            </li>
        </ul>
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

                        <!-- Badge + Save di dalam gambar, bawah -->
                        <div class="overlay-bottom">
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
                            <div class="price-col"> <span class="price-old">Rp650.000</span> <span
                                    class="price-now">Rp150.000</span> </div> <button class="btn-enroll">Enroll
                                Now</button>
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

                        <!-- Badge + Save di dalam gambar, bawah -->
                        <div class="overlay-bottom">
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
                            <div class="price-col"> <span class="price-old">Rp650.000</span> <span
                                    class="price-now">Rp150.000</span> </div> <button class="btn-enroll">Enroll
                                Now</button>
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

                        <!-- Badge + Save di dalam gambar, bawah -->
                        <div class="overlay-bottom">
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
                            <div class="price-col"> <span class="price-old">Rp650.000</span> <span
                                    class="price-now">Rp150.000</span> </div> <button class="btn-enroll">Enroll
                                Now</button>
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

                        <!-- Badge + Save di dalam gambar, bawah -->
                        <div class="overlay-bottom">
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
                            <div class="price-col"> <span class="price-old">Rp650.000</span> <span
                                    class="price-now">Rp150.000</span> </div> <button class="btn-enroll">Enroll
                                Now</button>
                        </div>
                    </div>
                </article>
            </li>
        </ul>
    </section>
    <section class="event">
        <div class="section-title">
            <h3>Event & Webinar</h3>
            <h6>Jadwal event dan webinar terbaru dari idSpora</h6>
        </div>
        <div class="box4">
            <div class="card-container-event">
                <div class="card-event">
                    <div class="card-image-event-wrapper">
                        <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
                        <img class="bookmark-event" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge-event">Beginner</span>
                    </div>

                    <div class="card-body">
                        <h4>AI For Lectures</h4>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Workshop</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                            </div>
                        </div>
                        <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                            tempor
                            incididunt...</p>

                        <div class="keterangan">
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-kalender.png') }}" alt="tanggal">
                                <span>04 September 2025</span>
                            </div>
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-lokasi.png') }}" alt="lokasi">
                                <span>Bandung • 09.00 WIB</span>
                            </div>
                        </div>
                        <div class="box-harga">
                            <div class="harga">
                                <p class="coret">Rp. 100.000</p>
                                <p class="harga-teks">Rp. 75.000</p>
                            </div>
                            <button class="btn-regist">Register</button>
                        </div>
                    </div>
                </div>
                <div class="card-event">
                    <div class="card-image-event-wrapper">
                        <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
                        <img class="bookmark-event" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge-event">Beginner</span>
                    </div>

                    <div class="card-body">
                        <h4>AI For Lectures</h4>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Workshop</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                            </div>
                        </div>
                        <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                            tempor
                            incididunt...</p>

                        <div class="keterangan">
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-kalender.png') }}" alt="tanggal">
                                <span>04 September 2025</span>
                            </div>
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-lokasi.png') }}" alt="lokasi">
                                <span>Bandung • 09.00 WIB</span>
                            </div>
                        </div>
                        <div class="box-harga">
                            <div class="harga">
                                <p class="coret">Rp. 100.000</p>
                                <p class="harga-teks">Rp. 75.000</p>
                            </div>
                            <button class="btn-regist">Register</button>
                        </div>
                    </div>
                </div>
                <div class="card-event">
                    <div class="card-image-event-wrapper">
                        <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
                        <img class="bookmark-event" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge-event">Beginner</span>
                    </div>
                    <div class="card-body">
                        <h4>AI For Lectures</h4>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Workshop</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                            </div>
                        </div>
                        <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                            tempor
                            incididunt...</p>

                        <div class="keterangan">
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-kalender.png') }}" alt="tanggal">
                                <span>04 September 2025</span>
                            </div>
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-lokasi.png') }}" alt="lokasi">
                                <span>Bandung • 09.00 WIB</span>
                            </div>
                        </div>
                        <div class="box-harga">
                            <div class="harga">
                                <p class="coret">Rp. 100.000</p>
                                <p class="harga-teks">Rp. 75.000</p>
                            </div>
                            <button class="btn-regist">Register</button>
                        </div>
                    </div>
                </div>
                <div class="card-event">
                    <div class="card-image-event-wrapper">
                        <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
                        <img class="bookmark-event" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge-event">Beginner</span>
                    </div>

                    <div class="card-body">
                        <h4>AI For Lectures</h4>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Workshop</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                            </div>
                        </div>
                        <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                            tempor
                            incididunt...</p>

                        <div class="keterangan">
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-kalender.png') }}" alt="tanggal">
                                <span>04 September 2025</span>
                            </div>
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-lokasi.png') }}" alt="lokasi">
                                <span>Bandung • 09.00 WIB</span>
                            </div>
                        </div>
                        <div class="box-harga">
                            <div class="harga">
                                <p class="coret">Rp. 100.000</p>
                                <p class="harga-teks">Rp. 75.000</p>
                            </div>
                            <button class="btn-regist">Register</button>
                        </div>
                    </div>
                </div>
                <div class="card-event">
                    <div class="card-image-event-wrapper">
                        <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
                        <img class="bookmark-event" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge-event">Beginner</span>
                    </div>

                    <div class="card-body">
                        <h4>AI For Lectures</h4>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Workshop</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                            </div>
                        </div>
                        <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                            tempor
                            incididunt...</p>

                        <div class="keterangan">
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-kalender.png') }}" alt="tanggal">
                                <span>04 September 2025</span>
                            </div>
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-lokasi.png') }}" alt="lokasi">
                                <span>Bandung • 09.00 WIB</span>
                            </div>
                        </div>
                        <div class="box-harga">
                            <div class="harga">
                                <p class="coret">Rp. 100.000</p>
                                <p class="harga-teks">Rp. 75.000</p>
                            </div>
                            <button class="btn-regist">Register</button>
                        </div>
                    </div>
                </div>
                <div class="card-event">
                    <div class="card-image-event-wrapper">
                        <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
                        <img class="bookmark-event" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge-event">Beginner</span>
                    </div>

                    <div class="card-body">
                        <h4>AI For Lectures</h4>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Workshop</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                            </div>
                        </div>
                        <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                            tempor
                            incididunt...</p>

                        <div class="keterangan">
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-kalender.png') }}" alt="tanggal">
                                <span>04 September 2025</span>
                            </div>
                            <div class="keterangan">
                                <img src="{{ asset('aset/ikon-lokasi.png') }}" alt="lokasi">
                                <span>Bandung • 09.00 WIB</span>
                            </div>
                        </div>
                        <div class="box-harga">
                            <div class="harga">
                                <p class="coret">Rp. 100.000</p>
                                <p class="harga-teks">Rp. 75.000</p>
                            </div>
                            <button class="btn-regist">Register</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul>
            <li></li>
        </ul>
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
@include('partials.footer-before-login')