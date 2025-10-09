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
    width: fit-content;
    margin: 60px;
    padding: 0;
    border-color: #E4E4E6;
    display: flex;
    flex: wrap;
  }

  .content-description-title {
    display: flex;
    gap: 110px;
    align-items: center;
    background: #E4E4E6;
    padding: 25px;
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
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
        <div class="overview" style="padding-left: 45px; padding-right: 45px;">
          <h5>Overview</h5>
        </div>
        <div class="syllabus" style="padding-left: 45px; padding-right: 45px;">
          <h5>Syllabus</h5>
        </div>
        <div class="reviews">
          <h5>Reviews</h5>
        </div>
      </div>
    </div>

  </section>

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
  </script>

</body>

</html>