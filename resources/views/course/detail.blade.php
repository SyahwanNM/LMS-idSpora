@include ('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
  :root {
    --navy: #252346;
    --white: #FFFFFF;
    --primary-dark: #333333;
    --secondary: #F4C430;
    --black: #000000;
  }

  .course-hero {
    background: var(--navy);
    height: fit-content;
    padding-bottom: 20px;
  }

  .title-course-hero {
    color: var(--white);
    margin: 20px 345px 0 60px;
  }

  .sub-title {
    display: flex;
    gap: 10px;
    align-items: center;
    margin-bottom: 10px;
    flex-wrap: wrap;
  }

  .sub-title h6 {
    font-weight: 500;
    font-size: 16px;
    color: var(--primary-dark);
    background: var(--secondary);
    width: fit-content;
    padding: 10px 10px;
    border-radius: 10px;
    margin-bottom: 0;
  }

  .sub-title p {
    font-size: 16px;
    color: var(--white);
    background: transparent;
    margin-bottom: 0;
  }

  .course-body {
    max-width: 1200px;
    margin: 20px 10px 10px 60px;
    padding: 0;
    display: grid;
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 24px;
    align-items: start;
  }

  .sidebar .kanan {
    position: sticky;
    top: 24px;
  }

  .video-container {
    width: 100%;
    margin: 0;
    border-radius: 20px;
    overflow: hidden;
  }

  .video-container video,
  .video-container iframe,
  .video-container .plyr {
    width: 100%;
    max-width: 100%;
    height: 40vw;
    max-height: 400px;
    min-height: 200px;
    display: block;
    border-radius: 20px;
    object-fit: cover;
    background: #000;
  }
  
  

  .content-description,
  .comments {
    width: 100%;
    margin: 0;
  }

  .main-title h1 {
    font-size: 34px;
    font-weight: 120%;
  }

  .container-icon {
    display: flex;
    gap: 20px;
    align-items: center;
    flex-wrap: wrap;
  }

  .container-icon {
    color: var(--secondary);
  }

  .container-icon span {
    color: var(--white);
  }

  .content-description {
    background: var(--white);
    max-width: 800px;
    border-radius: 20px;
    margin: 0;
    padding: 0;
    border: 1px solid #E4E4E6;
    overflow: hidden;
  }

  .content-description-title {
    display: flex;
    background: #EAEAEA;
    padding: 0;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
  }

  .tab-btn {
    background: transparent;
    border: none;
    font-size: 16px;
    cursor: pointer;
    padding: 8px 15px;
    font-weight: 500;
    color: #333;
    flex: 1;
    text-align: center;
  }

  .tab-btn:not(:first-child) {
    border-left: 1px solid #D8D8D8;
  }

  .tab-btn.active {
    color: var(--secondary);
    font-weight: 600;
    background: rgba(237, 227, 199, 0.4);
  }

  .tab-content {
    display: none;
    padding: 20px;
  }

  .tab-content.active {
    display: block;
  }

  .comments form {
    max-width: 800px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    /* jarak antara textarea & button */
  }

  .textarea {
    margin-bottom: 30px;
  }

  .kanan {
    border: solid #f4c430 2px;
    padding: 40px 27px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px 10px rgba(0, 0, 0, 0.08);
    flex: 1.5;
    max-width: 400px;
  }

  .price-text {
    color: #000;
  }

  .text-danger {
    width: 100%;
    margin: 0;
  }

  .diskon {
    background-color: #252346;
    color: #f4c430;
    padding: 10px;
  }

  .info-box {
    margin-top: 20px;
    padding: 14px 16px;

  }

  .info-box>div {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 6px 0;
  }

  .info-box>div p:last-of-type {
    margin-left: auto;
    white-space: nowrap;
    text-align: right;
  }

  .info-box svg {
    flex: 0 0 20px;
  }

  .info-box p {
    margin: 0 0 0 10px;
  }

  .time-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    flex-wrap: nowrap;
  }

  .time-alert svg {
    flex: 0 0 auto;
  }

  .time-alert .ikon {
    margin-top: 0;
  }

  .time-alert p {
    margin: 0;
    flex: 1 1 auto;
    white-space: nowrap;
  }

  .time-alert .diskon {
    margin: 0;
    font-weight: 600;
    white-space: nowrap;
    margin-left: auto;
  }

  .box-diskon {
    display: block;
  }

  .date {
    display: flex;
  }

  .date-judul {
    margin-left: 10px;
  }

  .date-text {
    margin-left: 118px;
    color: #6c6c6c;
  }

  .ikon {
    margin-top: 5px;
  }

  .time {
    display: flex;
  }

  .time-judul {
    margin-left: 10px;
  }

  .time-text {
    margin-left: 140px;
    color: #6c6c6c;
  }

  .location {
    display: flex;
  }

  .location-judul {
    margin-left: 10px;
  }

  .location-text {
    margin-left: 185px;
    color: #6c6c6c;
  }

  .bahasa {
    display: flex;
  }

  .bahasa-judul {
    margin-left: 10px;
  }

  .bahasa-text {
    margin-left: 197px;
    color: #6c6c6c;
  }

  .sertifikat {
    display: flex;
  }

  .sertifikat-judul {
    margin-left: 10px;
  }

  .sertifikat-text {
    margin-left: 220px;
    color: #6c6c6c;
  }

  .enroll {
    background-color: #f4c430;
    border: none;
    margin-top: 20px;
    padding: 10px;
    width: 100%;
  }

  .save {
    background-color: #252346;
    color: white;
    margin-top: 10px;
    border: none;
    padding: 10px;
    width: 100%;
  }

  .note {
    color: #6c6c6c;
    margin-top: 10px;
    font-size: medium;
    margin-bottom: 30px;
  }

  .box-benefit {
    margin-top: 20px;
    padding: 16px;
  }

  .box-benefit h4 {
    margin-bottom: 14px;
    font-weight: 600;
  }

  .box-benefit>div {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
  }

  .box-benefit svg {
    flex: 0 0 24px;
    height: 20px;
    width: 20px;
    color: var(--secondary);
  }

  .box-benefit p {
    margin: 0;
    color: var(--black);
    font-size: 16px;
  }

  .share-box {
    overflow: hidden;
  }

  .share-title {
    margin: 0;
    padding: 12px 16px;
    font-size: 20px;
    color: #111827;
  }

  .box-copy {
    background: #e6e7e8ff;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
  }

  .copy-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    border: 0;
    background: transparent;
    padding: 8px 10px;
    border-radius: 8px;
    color: #4E5566;
    font-weight: 600;
    line-height: 1;
    cursor: pointer;
  }

  .copy-btn:hover {
    background: rgba(0, 0, 0, .05);
  }

  .share-ico {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
  }

  .share-ico i,
  .copy-btn i {
    font-size: 22px;
    line-height: 1;
    color: #4E5566;
    z-index: 1;
  }

  .share-ico:hover {
    color: #111827;
  }

  .main-col>*+* {
    margin-top: 20px;
  }

  @media (max-width: 992px) {
    .course-body {
      grid-template-columns: 1fr;
    }

    .sidebar .kanan {
      position: static;
    }
  }

  .video-container img {
    width: 100%;
    display: block;
    border-radius: 20px;
  }

  /* Custom styles for Syllabus dropdowns */
  .syllabus-dropdown-item {
    background-color: var(--white);
    border: 1px solid #E4E4E6;
    border-radius: 10px;
    margin-bottom: 15px;
    /* Spacing between dropdowns */
    overflow: hidden;
  }

  .syllabus-dropdown-item details {
    padding: 15px 20px;
  }

  .syllabus-dropdown-item summary {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
    font-size: 18px;
    cursor: pointer;
    outline: none;
    position: relative;
    list-style: none;
    /* Hide default marker */
  }

  .syllabus-dropdown-item summary::-webkit-details-marker,
  .syllabus-dropdown-item summary::marker {
    display: none;
    /* Hide default marker for webkit and standard browsers */
  }


  .syllabus-dropdown-item summary::after {
    content: '\f282';
    /* Bootstrap Icons chevron-down */
    font-family: "bootstrap-icons" !important;
    font-size: 1.2em;
    transition: transform 0.3s ease;
    color: #333;
  }

  .syllabus-dropdown-item details[open] summary::after {
    transform: rotate(180deg);
    /* Rotate arrow up when open */
  }

  .syllabus-dropdown-item ul {
    list-style: none;
    padding: 10px 0 0 0;
    margin: 0;
    border-top: 1px solid #EAEAEA;
    margin-top: 15px;
  }

  .syllabus-dropdown-item ul li {
    padding: 8px 0;
    font-size: 16px;
    color: #333;
    display: flex;
    align-items: baseline;
    gap: 8px;
  }

  .syllabus-dropdown-item ul li::before {
    content: counter(lesson-counter);
    counter-increment: lesson-counter;
    font-weight: 500;
    min-width: 20px;
    text-align: right;
    color: var(--secondary);
  }

  .review-card {
    border: 1.5px solid #E4E4E6;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
  }

  .review-card h4 {
    margin-top: 0;
    margin-bottom: 4px;
    font-size: 20px;
    font-weight: 600;
  }

  .star-rating {
    color: #F4C430;
    font-size: 20px;
    margin-bottom: 12px;
  }

  .review-card p {
    margin-bottom: 0;
    color: #333;
    line-height: 1.5;
  }
</style>

<body>
  <section class="course-hero">
    <nav aria-label="breadcrumb">
      <div class="container">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item"><a href="#">Course</a></li>
          <li class="breadcrumb-item active">Learn Artificial Intelligence with Python</li>
        </ol>
      </div>
    </nav>

    <div class="title-course-hero">
      <div class="sub-title">
        <h6>
          Website Design
        </h6>
        <p>by idSpora</p>
      </div>
      <div class="main-title">
        <h1>
          Complete Website Responsive Design: from Figma to Webflow to Website Design
        </h1>
      </div>
      <div class="container-icon">
        <div class="icon-time">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock-fill"
            viewBox="0 0 20 20">
            <path
              d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
          </svg>
          <span>2 Weeks</span>
        </div>
        <div class="icon-attendant">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
            class="bi bi-mortarboard-fill" viewBox="0 0 16 16">
            <path
              d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z" />
            <path
              d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z" />
          </svg>
          <span>156 Students</span>
        </div>
        <div class="icon-badge">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-reception-4"
            viewBox="0 0 16 16">
            <path
              d="M0 11.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5zm4-3a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5zm4-3a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5zm4-3a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5z" />
          </svg>
          <span>AI Level</span>
        </div>
        <div class="icon-lesson">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
            class="bi bi-file-earmark-fill" viewBox="0 0 16 16">
            <path
              d="M4 0h5.293A1 1 0 0 1 10 .293L13.707 4a1 1 0 0 1 .293.707V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2m5.5 1.5v2a1 1 0 0 0 1 1h2z" />
          </svg>
          <span>20 Lessons</span>
        </div>
        <div class="icon-quizzez">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
            <path fill="currentColor"
              d="M20 2H4c-.53 0-1.04.21-1.41.59C2.21 2.96 2 3.47 2 4v12c0 .53.21 1.04.59 1.41c.37.38.88.59 1.41.59h4l4 4l4-4h4c.53 0 1.04-.21 1.41-.59S22 16.53 22 16V4c0-.53-.21-1.04-.59-1.41C21.04 2.21 20.53 2 20 2m-9.95 4.04c.54-.36 1.25-.54 2.14-.54c.94 0 1.69.21 2.23.62q.81.63.81 1.68c0 .44-.15.83-.44 1.2c-.29.36-.67.64-1.13.85c-.26.15-.43.3-.52.47c-.09.18-.14.4-.14.68h-2c0-.5.1-.84.29-1.08c.21-.24.55-.52 1.07-.84c.26-.14.47-.32.64-.54c.14-.21.22-.46.22-.74c0-.3-.09-.52-.27-.69c-.18-.18-.45-.26-.76-.26c-.27 0-.49.07-.69.21c-.16.14-.26.35-.26.63H9.27c-.05-.69.23-1.29.78-1.65M11 14v-2h2v2Z" />
          </svg>
          <span>3 quizzez</span>
        </div>
      </div>
    </div>
  </section>

  <section class="course-body">
    <div class="main-col">

      <div class="video-container">
        <div id="video-wrapper"></div>
      </div>
      <div class="content-description">
        <div class="content-description-title">
          <button class="tab-btn" data-tab="overview">Overview</button>
          <button class="tab-btn active" data-tab="syllabus">Syllabus</button>
          <button class="tab-btn" data-tab="review">Review</button>
        </div>
        <div class="tab-content" id="overview">
          <h5>Overview</h5>
          <p>
            Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
            placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
            vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc
            posuere.
            Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
            himenaeos.
          </p>
          <p>
            Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
            placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
            vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc
            posuere.
            Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
            himenaeos.
          </p>
          <p>
            Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
            placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
            vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc
            posuere.
            Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
            himenaeos.
          </p>
        </div>
        <div class="tab-content active" id="syllabus">
          <h5>Syllabus</h5>
          <div class="syllabus-list">
            <div class="syllabus-dropdown-item">
              <details>
                <summary>Pengenalan Dasar Pemrograman</summary>
                <ul style="counter-reset: lesson-counter;">
                  <li>Apa itu Pemrograman</li>
                  <li>Mengenal Python</li>
                  <li>Instalasi Python & IDE (VSCode, PyCharm)</li>
                  <li>Hello World</li>
                </ul>
              </details>
            </div>
            <div class="syllabus-dropdown-item">
              <details>
                <summary>Pengenalan Dasar Pemrograman</summary>
                <ul style="counter-reset: lesson-counter;">
                  <li>Apa itu Pemrograman</li>
                  <li>Mengenal Python</li>
                  <li>Instalasi Python & IDE (VSCode, PyCharm)</li>
                  <li>Hello World</li>
                </ul>
              </details>
            </div>
            <div class="syllabus-dropdown-item">
              <details>
                <summary>Pengenalan Dasar Pemrograman</summary>
                <ul style="counter-reset: lesson-counter;">
                  <li>Apa itu Pemrograman</li>
                  <li>Mengenal Python</li>
                  <li>Instalasi Python & IDE (VSCode, PyCharm)</li>
                  <li>Hello World</li>
                </ul>
              </details>
            </div>
          </div>
        </div>
        <div class="tab-content" id="review">
          <h5>Review</h5>
          <div class="review-card">
            <h4>Erika Diana</h4>
            <div class="star-rating">
              <span>★★★★★</span>
            </div>
            <p>
              Course ini sangat membantu saya memahami dasar-dasar pengembangan aplikasi mobile. Penjelasannya runtut
              dan mudah dipahami, bahkan untuk pemula.
            </p>
          </div>

          <div class="review-card">
            <h4>Erika Diana</h4>
            <div class="star-rating">
              <span>★★★★★</span>
            </div>
            <p>
              Course ini sangat membantu saya memahami dasar-dasar pengembangan aplikasi mobile. Penjelasannya runtut
              dan mudah dipahami, bahkan untuk pemula.
            </p>
          </div>

        </div>
      </div>

      <div class="comments">
        <h6>Leave a Comment</h6>
        <form action="#" method="POST">
          <div class="form-group">
            <textarea id="comment" name="comment" rows="4" class="form-control" placeholder="Comment"
              required></textarea>
          </div>
          <button type="submit" class="btn btn-primary">Post Comment</button>
        </form>
      </div>
    </div>

    <aside class="sidebar">
      <div class="kanan">
        <div class="price">
          <span class="text-muted text-decoration-line-through">Rp300.000</span>
          <h4 class="price-text">Rp150.000</h4>
          <div class="box-diskon">
            <div class="time-alert">
              <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="red" class="ikon bi bi-alarm"
                viewBox="0 0 16 16">
                <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9z" />
                <path
                  d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1zm1.038 3.018a6 6 0 0 1 .924 0 6 6 0 1 1-.924 0M0 3.5c0 .753.333 1.429.86 1.887A8.04 8.04 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5M13.5 1c-.753 0-1.429.333-1.887.86a8.04 8.04 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1" />
              </svg>
              <p class="text-danger">2 days left at this price!</p>
              <small class="diskon">50% OFF</small>
            </div>
          </div>
          <hr>
          <div class="info-box">
            <div class="time">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock"
                style="align-items: center;" viewBox="0 0 20 20">
                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
              </svg>
              <p class="date-judul">Course Duration</p>
              <p class="date-text">12 Jam</p>
            </div>
            <div class="level">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bar-chart"
                viewBox="0 0 20 20">
                <path
                  d="M4 11H2v3h2zm5-4H7v7h2zm5-5v12h-2V2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z" />
              </svg>
              <p class="level-judul">Course Level</p>
              <p class="level-text">Beginner</p>
            </div>
            <div class="location">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people"
                viewBox="0 0 20 20">
                <path
                  d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
              </svg>
              <p class="location-judul">Students Enrolled</p>
              <p class="location-text">190</p>
            </div>
            <div class="bahasa">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#
                                                A1A5B3" class="ikon bi bi-journal-text" viewBox="0 0 20 20">
                <path
                  d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                <path
                  d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2" />
                <path
                  d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z" />
              </svg>
              <p class="bahasa-judul">Language</p>
              <p class="bahasa-text">Indonesia</p>
            </div>
            <div class="sertifikat">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="var(--secondary)"
                class="ikon bi bi-book" viewBox="0 0 20 20">
                <path
                  d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
              </svg>
              <p class="sertifikat-judul">Certificate</p>
              <p class="sertifikat-text">Include</p>
            </div>
          </div>
          <hr>
          <button class="enroll">Enroll Now</button>
          <button class="save">Save</button>
          <p class="note">Note: all course have 30-days money-back guarantee</p>
        </div>
        <hr>

        <div class="box-benefit">
          <h4>This course include:</h4>
          <div class="time-benefit">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock"
              viewBox="0 0 16 16">
              <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
              <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
            </svg>
            <p class="time-text">Akses Seumur Hidup</p>
          </div>
          <div class="materi">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
              class="bi bi-journal-text" viewBox="0 0 16 16">
              <path
                d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
              <path
                d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2" />
              <path
                d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z" />
            </svg>
            <p class="materi-text">Materi pembelajaran Lengkap</p>
          </div>
          <div class="sertif">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-trophy"
              viewBox="0 0 16 16">
              <path
                d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5q0 .807-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1q.01.775.056 1.469c.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.5.5 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667q.045-.694.056-1.469z" />
            </svg>
            <p class="sertif-text">Sertifikat Kehadiran</p>
          </div>
          <div class="record">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-tv"
              viewBox="0 0 16 16">
              <path
                d="M2.5 13.5A.5.5 0 0 1 3 13h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5M13.991 3l.024.001a1.5 1.5 0 0 1 .538.143.76.76 0 0 1 .302.254c.067.1.145.277.145.602v5.991l-.001.024a1.5 1.5 0 0 1-.143.538.76.76 0 0 1-.254.302c-.1.067-.277.145-.602.145H2.009l-.024-.001a1.5 1.5 0 0 1-.538-.143.76.76 0 0 1-.302-.254C1.078 10.502 1 10.325 1 10V4.009l.001-.024a1.5 1.5 0 0 1 .143-.538.76.76 0 0 1 .254-.302C1.498 3.078 1.675 3 2 3zM14 2H2C0 2 0 4 0 4v6c0 2 2 2 2 2h12c2 0 2-2 2-2V4c0-2-2-2-2-2" />
            </svg>
            <p class="record-text">Video Tersedia</p>
          </div>
          <div class="online">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-layers"
              viewBox="0 0 16 16">
              <path
                d="M8.235 1.559a.5.5 0 0 0-.47 0l-7.5 4a.5.5 0 0 0 0 .882L3.188 8 .264 9.559a.5.5 0 0 0 0 .882l7.5 4a.5.5 0 0 0 .47 0l7.5-4a.5.5 0 0 0 0-.882L12.813 8l2.922-1.559a.5.5 0 0 0 0-.882zm3.515 7.008L14.438 10 8 13.433 1.562 10 4.25 8.567l3.515 1.874a.5.5 0 0 0 .47 0zM8 9.433 1.562 6 8 2.567 14.438 6z" />
            </svg>
            <p class="online-text">100% Online Course</p>
          </div>
        </div>
        <hr>
        <div class="share-box mt-4">
          <p class="fw-semibold">Share this course:</p>
          <div class="box-copy">
            <button class="copy-btn" type="button" onclick="copyLink()">
              <i class="bi bi-clipboard"></i>
              <span>Copy Link</span>
            </button>

            <a href="#" class="share-ico" aria-label="Share to Facebook">
              <i class="bi bi-facebook"></i>
            </a>

            <a href="#" class="share-ico" aria-label="Share to X/Twitter">
              <i class="bi bi-twitter"></i>
            </a>

            <a href="mailto:?subject=Check this course" class="share-ico" aria-label="Share via Email">
              <i class="bi bi-envelope"></i>
            </a>

            <a href="https://wa.me/?text=Check this course" target="_blank" class="share-ico"
              aria-label="Share to WhatsApp">
              <i class="bi bi-whatsapp"></i>
            </a>
          </div>
        </div>
      </div>
    </aside>
  </section>

  <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // --- DATA VIDEO (Ganti sesuai kebutuhan) ---

      // Skenario 1: Video dari YouTube (Aktif saat ini)
      const videoData = {
        type: 'youtube',
        id: 'oEwTLmyVheA' // ID Video YouTube
      };

      /*
      // Skenario 2: Video dari file lokal (Gunakan ini jika video dari file)
      const videoData = {
        type: 'local',
        path: 'URL_FILE_VIDEO_ANDA.mp4', // Ganti dengan path video Anda
        poster: 'property/idspora.png'      // Ganti dengan path poster Anda
      };
      */


      // --- LOGIKA UNTUK MEMBUAT PLAYER VIDEO ---
      const videoWrapper = document.getElementById('video-wrapper');
      let playerElement;

      if (videoData.type === 'youtube') {
        playerElement = document.createElement('div');
        playerElement.setAttribute('data-plyr-provider', 'youtube');
        playerElement.setAttribute('data-plyr-embed-id', videoData.id);
      } else if (videoData.type === 'local') {
        playerElement = document.createElement('video');
        playerElement.setAttribute('playsinline', '');
        playerElement.setAttribute('controls', '');
        playerElement.setAttribute('poster', videoData.poster);
        const sourceElement = document.createElement('source');
        sourceElement.setAttribute('src', videoData.path);
        sourceElement.setAttribute('type', 'video/mp4');
        playerElement.appendChild(sourceElement);
      }

      if (playerElement) {
        playerElement.id = 'player';
        videoWrapper.appendChild(playerElement);
        const player = new Plyr('#player');
      }


      // --- LOGIKA UNTUK TAB KONTEN ---
      const tabs = document.querySelectorAll(".tab-btn");
      const contents = document.querySelectorAll(".tab-content");

      // Atur tab 'Overview' sebagai default saat halaman dimuat
      document.getElementById('overview').classList.add('active');
      document.querySelector('.tab-btn[data-tab="overview"]').classList.add('active');

      tabs.forEach(tab => {
        tab.addEventListener("click", () => {
          tabs.forEach(btn => btn.classList.remove("active"));
          contents.forEach(content => content.classList.remove("active"));
          tab.classList.add("active");
          document.getElementById(tab.dataset.tab).classList.add("active");
        });
      });
    });
  </script>

</body>

</html>
@include('partials.footer-after-login')