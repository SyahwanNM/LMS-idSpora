@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
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
                    <option>Free</option>
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
    </div>
    <section class="event">
        <div class="header-card">
            <h3>Event Mendatang</h3>
            <div class="dropdown-box d-flex gap-2">
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Weekdays
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Weekdays</a></li>
                        <li><a class="dropdown-item" href="#">Weekend</a></li>
                        <li><a class="dropdown-item" href="#">Today</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Event Type
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Online</a></li>
                        <li><a class="dropdown-item" href="#">Onsite</a></li>
                        <li><a class="dropdown-item" href="#">Hybrid</a></li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Any Categori
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Workshop</a></li>
                        <li><a class="dropdown-item" href="#">Training</a></li>
                        <li><a class="dropdown-item" href="#">Webinar</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="event-list" id="page-1">
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
        <div class="event-list" id="page-1">
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
        <div class="event-list" id="page-1">
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
        <div class="event-list page-content hidden" id="page-2" style="display: none;">
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
            <div class="card-event">
                <div class="thumb-wrapper">
                    <img class="card-image-event" src="{{ asset('aset/poster.png') }}" alt="Course">
                    <div class="badge-save-group" style="gap: 12px;">
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
        <div class="event-list page-content hidden" id="page-2" style="display: none;">
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
    </section>
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
</body>

</html>
@include('partials.footer-after-login')