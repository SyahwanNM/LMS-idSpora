@include ('partials.navbar-before-login')
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
  .course-hero {
    background: var(--navy);
    height: fit-content;
    padding-bottom: 20px
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
    margin: 60px 60px;
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

  .comments {
    margin: 60px;
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
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px 10px rgba(0, 0, 0, 0.08);
    flex: 1.5;
    max-width: 400px;
}
.price-text {
    color: #000;
}
.text-danger{
    width: 100%;
}
.diskon {
    background-color: #252346;
    color: #f4c430;
    padding: 10px;
    margin-left: 60px;
    margin-top: -20px;
    margin-bottom: 30px;
}
.info-box {
    margin-top: 30px;
}
.info-box > div {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.info-box svg {
    flex-shrink: 0;
}

.info-box p {
    margin: 0 0 0 10px;
}

.time-alert {
    display: flex;
    align-items: center;
    font-weight: 500;
    margin-top: 5px;
}

.time-alert svg {
    margin-right: 8px;
}

.time-alert .ikon {
    margin-top: -30px;
}

.time-alert p {
    margin-top: -10px;
}
.box-diskon {
    display: flex;
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
}
.materi {
    display: flex;
    margin-top: 20px;
}

.materi-text {
    margin-left: 10px;
}
.sertif {
    display: flex;
}

.sertif-text {
    margin-left: 10px;
}
.record {
    display: flex;
}

.record-text {
    margin-left: 10px;
}
.online {
    display: flex;
}

.online-text {
    margin-left: 10px;
}
    .sponsor-box {
    margin-top: 30px;
}

.share-box {
    margin-top: 30px;
}
.box-copy {
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    gap: 30px;
    padding: 5px;
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
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-fill"
            viewBox="0 0 16 16">
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

  <section class="content">
    <div class="video-container">
      <img src="property/idspora.png" alt="Video Recap" class="img-fluid" />
      <div class="video-overlay" onclick="playVideo()">
        <div class="play-button">
          <i class="fas fa-play"></i>
        </div>
      </div>
    </div>
    <div class="content-description">
      <div class="content-description-title">
        <button class="tab-btn" data-tab="overview">Overview</button>
        <button class="tab-btn" data-tab="syllabus">Syllabus</button>
        <button class="tab-btn" data-tab="review">Review</button>
      </div>
      <div class="tab-content active" id="overview">
        <h5>Overview</h5>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
      </div>
      <div class="tab-content" id="syllabus">
        <h5>Syllabus</h5>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
      </div>
      <div class="tab-content" id="review">
        <h5>Reviews</h5>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
        <p>
          Lorem ipsum dolor sit amet consectetur adipiscing elit. Quisque faucibus ex sapien vitae pellentesque sem
          placerat. In id cursus mi pretium tellus duis convallis. Tempus leo eu aenean sed diam urna tempor. Pulvinar
          vivamus fringilla lacus nec metus bibendum egestas. Iaculis massa nisl malesuada lacinia integer nunc posuere.
          Ut hendrerit semper vel class aptent taciti sociosqu. Ad litora torquent per conubia nostra inceptos
          himenaeos.
        </p>
      </div>
    </div>
    <div class="comments">
      <h6>Leave a Comment</h6>
      <form action="#" method="POST">
        <div class="form-group">
          <textarea id="comment" name="comment" rows="4" class="form-control" placeholder="Comment" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Post Comment</button>
      </form>
    </div>
  </section>

  <div class="kanan">
    <div class="price">
      <span class="text-muted text-decoration-line-through">Rp.300.000</span>
      <h4 class="price-text">RP.150.000</h4>
      <div class="box-diskon">
        <div class="time-alert">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="red" class="ikon bi bi-alarm"
            viewBox="0 0 16 16">
            <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9z" />
            <path
              d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1zm1.038 3.018a6 6 0 0 1 .924 0 6 6 0 1 1-.924 0M0 3.5c0 .753.333 1.429.86 1.887A8.04 8.04 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5M13.5 1c-.753 0-1.429.333-1.887.86a8.04 8.04 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1" />
          </svg>
          <p class="text-danger">2 days left at this price!</p>
        </div>
        <small class="diskon">50% OFF</small>
      </div>
      <hr>
      <div class="info-box">
        <div class="date">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#
                        A1A5B3" class="ikon bi bi-calendar2-date" viewBox="0 0 16 16">
            <path
              d="M6.445 12.688V7.354h-.633A13 13 0 0 0 4.5 8.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23" />
            <path
              d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1z" />
            <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H3a.5.5 0 0 1-.5-.5z" />
          </svg>
          <p class="date-judul">Tanggal</p>
          <p class="date-text">12 September 2025</p>
        </div>
        <div class="time">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#
                        A1A5B3" class="ikon bi bi-clock" viewBox="0 0 16 16">
            <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
          </svg>
          <p class="time-judul">Waktu</p>
          <p class="time-text">19:30 WIB - Selesai</p>
        </div>
        <div class="location">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#
                        A1A5B3" class="ikon bi bi-geo-alt" viewBox="0 0 16 16">
            <path
              d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
          </svg>
          <p class="location-judul">Lokasi</p>
          <p class="location-text">Google Meet</p>
        </div>
        <div class="bahasa">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#
                        A1A5B3" class="ikon bi bi-journal-text" viewBox="0 0 16 16">
            <path
              d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
            <path
              d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2" />
            <path
              d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z" />
          </svg>
          <p class="bahasa-judul">Bahasa</p>
          <p class="bahasa-text">Indonesia</p>
        </div>
        <div class="sertifikat">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#
                        A1A5B3" class="ikon bi bi-book" viewBox="0 0 16 16">
            <path
              d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
          </svg>
          <p class="sertifikat-judul">Sertifikat</p>
          <p class="sertifikat-text">Gratis</p>
        </div>
      </div>
      <hr>
      <button class="enroll">Enroll Now</button>
      <button class="save">Save</button>
      <p class="note">Note: all course have 30-days money-back guarantee</p>
    </div>
    <hr>
    <div class="box-benefit">
      <h4>This Event Include</h4>
      <div class="materi">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#f4c430" class="ikon bi bi-book"
          viewBox="0 0 16 16">
          <path
            d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
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
        <p class="record-text">Rekaman Tersedia</p>
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
    <div class="sponsor-box">
      <h4 class="mb-3">Sponsor & Partner</h4>
      <p>Ko+Lab</p>
      <p>Fakultas Ilmu Terapan</p>
      <p>Telkom University</p>
    </div>
    <hr>
    <div class="share-box mt-4">
      <p class="fw-semibold">Share this course:</p>
      <div class="box-copy">


        <button class="btn btn-outline-secondary" onclick="copyLink()">
          <i class="bi bi-clipboard"></i> Copy link
        </button>

        <a href="#" class="text-dark fs-5">
          <i class="bi bi-facebook"></i>
        </a>

        <a href="#" class="text-dark fs-5">
          <i class="bi bi-twitter"></i>
        </a>

        <a href="mailto:?subject=Check this course" class="text-dark fs-5">
          <i class="bi bi-envelope"></i>
        </a>

        <a href="https://wa.me/?text=Check this course" target="_blank" class="text-dark fs-5">
          <i class="bi bi-whatsapp"></i>
        </a>

      </div>
    </div>

    <script>
      function playVideo() {
        const videoContainer = document.querySelector(".video-container");
        const videoEmbed = `
                  <video width="100%" height="100%" controls autoplay style="border-radius: 20px;">
                      <source src="https://youtu.be/Uc8d8P9_p-A?si=-0jXlAcNTi20hywU" type="video/mp4">
                      <p>Your browser does not support the video tag.</p>
                  </video>
              `;
        videoContainer.innerHTML = videoEmbed;
      }

      const tabs = document.querySelectorAll(".tab-btn");
      const contents = document.querySelectorAll(".tab-content");

      tabs.forEach(tab => {
        tab.addEventListener("click", () => {
          // hapus class active dari semua
          tabs.forEach(btn => btn.classList.remove("active"));
          contents.forEach(content => content.classList.remove("active"));

          // tambahkan class active ke tab & konten yang diklik
          tab.classList.add("active");
          document.getElementById(tab.dataset.tab).classList.add("active");
        });
      });


    </script>

</body>

</html>