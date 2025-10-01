@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    <div class="search-banner-container">
        <form class="search-banner-form" action="#" method="get" autocomplete="off">
            <div class="search-wrap">
                <input id="site-search" class="form-control search-input" type="search" name="search"
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
    <section class="hero-carousel">
        <div id="carouselExampleInterval" class="carousel slide custom-carousel" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="10000">
                    <img src="{{ asset('aset/ai.jpg') }}"
                        class="d-block" alt="...">
                </div>
                <div class="carousel-item" data-bs-interval="2000">
                    <img src="{{ asset('aset/ai2.jpg') }}"
                        class="d-block" alt="...">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('aset/ai3.jpg') }}"
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

    <div class="container">

        <div class="box2">
            <div class="row justify-content-center gx-4">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="kotak h-100">
                        <h3 class="judul">Task Progress</h3>
                        <div class="task-item"> <span class="task-title">Web Programming</span> <span class="task-score">5/10</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 50%"></div>
                            </div>
                        </div>
                        <div class="task-item"> <span class="task-title">Data and Structure</span> <span class="task-score">4/15</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 20%"></div>
                            </div>
                        </div>
                        <div class="task-item"> <span class="task-title">Artificiall Intelligence</span> <span class="task-score">2/15</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 30%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="kotak h-100">
                        <h3 class="judul">Statistics</h3>
                        <p class="bulan">Januari - Juni 2025</p>
                        <div class="stat-container">
                            <div class="keterangan">
                                <div class="stat-item"> <img src="{{ asset('aset/logo-kehadiran.png') }}" alt="Kehadiran">
                                    <div class="stat-text">
                                        <p class="label">Kehadiran</p>
                                        <p class="value">90%</p>
                                    </div>
                                </div>
                                <div class="stat-item"> <img src="{{ asset('aset/logo-ujian.png') }}" alt="Tugas & Ujian">
                                    <div class="stat-text">
                                        <p class="label">Tugas & Ujian</p>
                                        <p class="value">70%</p>
                                    </div>
                                </div>
                                <div class="stat-item"> <img src="{{ asset('aset/logo-kuis.png') }}" alt="Kuis">
                                    <div class="stat-text">
                                        <p class="label">Kuis</p>
                                        <p class="value">85%</p>
                                    </div>
                                </div>
                            </div>
                            <div class="chart-wrapper"> <canvas id="gradesChart"></canvas> </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 mb-4">
                    <div class="kotak h-100">
                        <h3 class="judul">Hours Spent</h3> <canvas id="hoursChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <section class="kursus-pelatihan">
        <div class="header-card d-flex align-items-center justify-content-between" style="gap:16px;">
            <h3 class="mb-0">Lanjutkan Belajar</h3>
            <a href="#" class="see-more-link text-decoration-none">Lihat Lainnya &raquo;</a>
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
                        <div class="progress-wrapper">
                            <div class="progress">
                                <div class="progress-bar"></div>
                            </div>
                            <p>30% selesai</p>
                        </div>
                        <button class="btn-lanjut">Lanjutkan</button>
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
                        <div class="progress-wrapper">
                            <div class="progress">
                                <div class="progress-bar"></div>
                            </div>
                            <p>30% selesai</p>
                        </div>
                        <button class="btn-lanjut">Lanjutkan</button>
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
                        <div class="progress-wrapper">
                            <div class="progress">
                                <div class="progress-bar"></div>
                            </div>
                            <p>30% selesai</p>
                        </div>
                        <button class="btn-lanjut">Lanjutkan</button>
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

    <section class="event">
        <div class="header-card d-flex align-items-center justify-content-between" style="gap:16px;">
            <h3 class="mb-0">Event Mendatang</h3>
            <a href="#" class="see-more-link text-decoration-none">Lihat Lainnya &raquo;</a>
        </div>
        <div class="event-list">
            @forelse($upcomingEvents as $event)
                @php
                    $startAt = null;
                    if($event->event_date){
                        $dateStr = $event->event_date->format('Y-m-d');
                        if($event->event_time){
                            // Ensure we have H:i:s
                            $timeStr = method_exists($event->event_time,'format') ? $event->event_time->format('H:i:s') : (is_string($event->event_time)? $event->event_time : '00:00:00');
                        } else {
                            $timeStr = '00:00:00';
                        }
                        try { $startAt = \Carbon\Carbon::parse($dateStr.' '.$timeStr, config('app.timezone')); } catch (Exception $e) { $startAt = null; }
                    }
                @endphp
                <div class="card-event" @if($startAt) data-event-start-ts="{{ $startAt->timestamp }}" @endif>
                    <div class="thumb-wrapper">
                        @if($event->image)
                            <img class="card-image-event" src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}">
                        @else
                            <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="{{ $event->title }}">
                        @endif
                        @php
                            $showDiscountBadge = $event->hasDiscount() && $event->price > 0 && $event->price > $event->discounted_price;
                            $percentOff = $showDiscountBadge ? round((($event->price - $event->discounted_price) / $event->price) * 100) : 0;
                        @endphp
                        @if($showDiscountBadge && $percentOff > 0)
                            <span class="discount-badge">{{ $percentOff }}% off</span>
                        @endif
                        <div class="badge-save-group" style="gap:12px;">
                            <button class="save-btn" aria-label="Save event" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M2 2v13.5l6-3 6 3V2z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <h4>{{ $event->title }}</h4>
                        <div class="tags">
                            <span class="tag">{{ $event->speaker ? Str::limit($event->speaker, 18) : 'Narasumber' }}</span>
                            <span class="tag">{{ $event->location ? Str::limit($event->location, 18) : 'Lokasi TBA' }}</span>
                            <div class="meta" style="margin-left:auto; gap:6px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                                </svg>
                                <span>118</span>
                            </div>
                        </div>
                        <div class="desc-event rich-desc">{!! Str::limit(strip_tags($event->description, '<p><br><strong><em><ul><ol><li><b><i>'), 220) !!}</div>
                        <div class="keterangan keterangan-row">
                            <div class="keterangan-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-calendar-event" viewBox="0 0 16 16">
                                    <path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5z" />
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                </svg>
                                <span>{{ $event->event_date?->format('d F Y') }}</span>
                            </div>
                            <div class="keterangan-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 16 16">
                                    <path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6" />
                                </svg>
                                <span>{{ $event->location ? ($event->event_time ? $event->location.' • '.$event->event_time?->format('H:i').' WIB' : $event->location) : '-' }}</span>
                            </div>
                        </div>
                        @if($startAt)
                        <div class="countdown-wrapper" data-countdown-wrapper>
                            <span class="countdown-label">Mulai dalam:</span>
                            <span class="countdown-timer" data-countdown data-start-ts="{{ $startAt->timestamp }}">--:--:--</span>
                        </div>
                        @endif
                        <div class="price-row">
                            <div class="price-col">
                                @if($event->hasDiscount())
                                    <span class="price-old">Rp{{ number_format($event->price, 0, ',', '.') }}</span>
                                    <span class="price-now">Rp{{ number_format($event->discounted_price, 0, ',', '.') }}</span>
                                @else
                                    <span class="price-now">Rp{{ number_format($event->price, 0, ',', '.') }}</span>
                                @endif
                            </div>
                            @php $registered = !empty($event->is_registered); @endphp
                            <button class="btn-register btn {{ $registered ? 'btn-success' : 'btn-primary' }}" type="button" {{ $registered ? 'disabled' : '' }}>
                                {{ $registered ? 'Anda Terdaftar' : 'Register' }}
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5" style="grid-column:1/-1;">
                    <h5 class="mb-3">Belum ada event tersedia</h5>
                    <p class="text-muted">Event akan segera hadir!</p>
                </div>
            @endforelse
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
    /* === Dashboard Event Card Image Size (Reduced Slightly) & Horizontal Spacing === */
    .event .event-list {row-gap:38px; padding:0 18px;} /* added horizontal breathing space */
    .event .card-event .thumb-wrapper {position:relative;height:360px;} /* was 400px */
    .event .card-event .card-image-event {width:100%;height:100%;object-fit:cover;}
    .event .card-event .card-body {padding-top:20px;} /* slightly reduced */
    @media (max-width:1200px){ .event .card-event .thumb-wrapper {height:340px;} }
    @media (max-width:992px){ .event .card-event .thumb-wrapper {height:320px;} }
    @media (max-width:768px){ .event .card-event .thumb-wrapper {height:260px;} }
    /* Discount badge styling */
    .event .card-event .thumb-wrapper {overflow:hidden;}
    .event .card-event .discount-badge {
        position:absolute;
        top:12px;
        left:12px;
        background:#212f4d;
        color:#d6bc3a;
        font-size:13px;
        font-weight:600;
        padding:6px 10px 5px;
        border-radius:6px;
        line-height:1;
        letter-spacing:.5px;
        box-shadow:0 2px 6px rgba(0,0,0,.25);
        display:inline-flex;
        align-items:center;
        gap:4px;
        text-transform:uppercase;
    }
    .see-more-link {font-size:14px; font-weight:500; color:#0d6efd; transition:color .25s;}
    .see-more-link:hover {color:#0a58ca; text-decoration:underline;}
    /* Countdown styles */
    .countdown-wrapper {margin-top:10px; display:flex; align-items:center; gap:6px; font-size:13px; font-weight:500;}
    .countdown-label {color:#555; font-weight:500;}
    .countdown-timer {background:#212f4d; color:#ffd54f; padding:2px 8px; border-radius:4px; font-family:monospace; letter-spacing:1px; min-width:150px; text-align:center;}
    .countdown-timer.started {background:#198754; color:#fff;}
    .countdown-timer.expired {background:#6c757d; color:#fff;}
    </style>
    <script>
        const ctx = document.getElementById('gradesChart');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [75, 25],
                    backgroundColor: ['#F4C430', '#e6eef4'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '75%',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false }
                }
            },
            plugins: [{
                id: 'textInside',
                beforeDraw(chart) {
                    const { width, height } = chart;
                    const ctx = chart.ctx;
                    ctx.restore();
                    const fontSize = (height / 5).toFixed(2);
                    ctx.font = `${fontSize}px Poppins`;
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = '#d4af37';
                    const text = '75%';
                    const textX = Math.round((width - ctx.measureText(text).width) / 2);
                    const textY = height / 2.2;
                    ctx.fillText(text, textX, textY);
                    ctx.font = `${(height / 15).toFixed(2)}px Poppins`;
                    ctx.fillStyle = '#999';
                    ctx.fillText('Grades Completed', width / 2.9, height / 1.7);
                    ctx.save();
                }
            }]
        });
    </script>
    <script>
        const hoursCtx = document.getElementById('hoursChart');

        new Chart(hoursCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
                datasets: [{
                        label: 'Study',
                        data: [40, 20, 65, 35, 15],
                        backgroundColor: '#F4C430'
                    },
                    {
                        label: 'Online Test',
                        data: [30, 20, 20, 25, 10],
                        backgroundColor: '#e6e6e6'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            callback: value => value + ' Hr'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: context => context.parsed.y + ' Hr'
                        }
                    }
                }
            }
        });
    </script>
    <script>
       
        (function(){
            function formatDiff(totalSec){
                if(totalSec <= 0) return 'Dimulai';
                let sec = totalSec;
                const days = Math.floor(sec/86400); sec%=86400;
                const hours = Math.floor(sec/3600); sec%=3600;
                const minutes = Math.floor(sec/60);
                if(minutes === 0 && hours === 0 && days === 0) return '< 1 menit';
                const parts = [];
                if(days > 0) parts.push(days + ' hari');
                if(hours > 0 || days > 0) parts.push(hours + ' jam');
                parts.push(minutes + ' menit');
                return parts.join(' ');
            }
            function update(){
                const now = Math.floor(Date.now()/1000);
                document.querySelectorAll('[data-countdown]').forEach(el=>{
                    const start = parseInt(el.getAttribute('data-start-ts'),10);
                    if(!start) return;
                    const diff = start - now;
                    if(diff <= 0){
                        el.textContent = 'Dimulai';
                        el.classList.add('started');
                        return;
                    }
                    el.textContent = formatDiff(diff);
                });
            }
            update();
            setInterval(update,1000);
        })();
    </script>

</body>

</html>
@include('partials.footer-before-login')