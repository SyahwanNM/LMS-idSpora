@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    <div class="container-ungu">
        <div class="link-box">
            <a href="#">Home</a>
            <p>></p>
            <a href="#">Events</a>
            <p>></p>
            <a href="#">Digital Marketing Masterclass 2025</a>
        </div>
        <div class="box-event-creator">
            <div class="event-creator">
                <p><span class="highlite-yellow">Event</span> by idSpora</p>
            </div>
            <div class="add-calender">
                <button class="">
                    <svg class="ikon-calender-event" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar-plus" viewBox="0 0 16 16">
                        <path d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7" />
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                    </svg>
                    <p>Add To Calender</p>
                </button>
            </div>
        </div>
        <div class="event-title">
            <h4>Digital Marketing Masterclass 2025</h4>
            <p>Ruang belajar singkat dan praktis untuk memahami strategi digital marketing modern yang fokus pada konten, social media, dan pemanfaatan AI yang bisa langsung dipraktekkan.</p>
        </div>
    </div>
    <div class="detail-box">
        <div class="detail-box-left">
            <img src="{{ asset('aset/event.png') }}" alt="Gambar Event">
            <div class="progress-box">
                <h5>Your Progress</h5>
                <div class="progress-line">-</div>
                <div class="progress-steps">
                    <div class="step active">
                        <div class="circle">
                            <p>o</p>
                        </div>
                        <p>Registered</p>
                    </div>
                    <div class="step">
                        <div class="circle">
                            <p>o</p>
                        </div>
                        <p>Attended</p>
                    </div>
                    <div class="step">
                        <div class="circle">
                            <p>o</p>
                        </div>
                        <p>Feedback</p>
                    </div>
                    <div class="step">
                        <div class="circle">
                            <p>o</p>
                        </div>
                        <p>Certificate</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="detail-box-right">
            <div class="info-price-box">
                <div class="price-box">
                    <span>Rp.300.000</span>
                    <h5>Rp.150.000</h5>
                    <div class="diskon-time">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-alarm" viewBox="0 0 16 16">
                            <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9z" />
                            <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1zm1.038 3.018a6 6 0 0 1 .924 0 6 6 0 1 1-.924 0M0 3.5c0 .753.333 1.429.86 1.887A8.04 8.04 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5M13.5 1c-.753 0-1.429.333-1.887.86a8.04 8.04 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1" />
                        </svg>
                        <p>2 Days left at this price!</p>
                    </div>
                </div>
                <div class="diskon-event">
                    <p>50% OFF</p>
                </div>
            </div>
            <hr class="line-info">
            <div class="info-boxluar">
                <div class="info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar-date" viewBox="0 0 16 16">
                        <path d="M6.445 11.688V6.354h-.633A13 13 0 0 0 4.5 7.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23" />
                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                    </svg>
                    <div class="info-text">
                        <span class="label-event">Date</span>
                        <span class="isi-event">12 September 2025</span>
                    </div>
                </div>
                <div class="info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                        <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                        <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
                    </svg>
                    <div class="info-text">
                        <span class="label-event">Time</span>
                        <span class="isi-event">14.30 WIB - selesai</span>
                    </div>
                </div>
                <div class="info-item ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16">
                        <path d="M4 11H2v3h2zm5-4H7v7h2zm5-5v12h-2V2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z" />
                    </svg>
                    <div class="info-text">
                        <span class="label-event">Location</span>
                        <span class="isi-event">Fakultas Ilmu Terapan Telkom University</span>
                    </div>
                </div>
                <div class="info-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                        <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                    </svg>
                    <div class="info-text">
                        <span class="label-event">Student Enrolled</span>
                        <span class="isi-event">190</span>
                    </div>
                </div>
            </div>
            <hr>
            <button class="bookseat">Book Seat</button>
            <button class="save">Save</button>
            <hr>
            <div class="include-box">
                <div class="include-title">
                    <h6>This event includes:</h6>
                    <div class="include-isi">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-file-earmark-slides-fill" viewBox="0 0 16 16">
                            <path d="M7 9.78V7.22c0-.096.106-.156.19-.106l2.13 1.279a.125.125 0 0 1 0 .214l-2.13 1.28A.125.125 0 0 1 7 9.778z" />
                            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1M5 6h6a.5.5 0 0 1 .496.438l.5 4A.5.5 0 0 1 11.5 11h-3v2.016c.863.055 1.5.251 1.5.484 0 .276-.895.5-2 .5s-2-.224-2-.5c0-.233.637-.429 1.5-.484V11h-3a.5.5 0 0 1-.496-.562l.5-4A.5.5 0 0 1 5 6" />
                        </svg>
                        <p>Slide Materi</p>
                    </div>
                    <div class="include-isi">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-file-richtext" viewBox="0 0 16 16">
                            <path d="M7 4.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0m-.861 1.542 1.33.886 1.854-1.855a.25.25 0 0 1 .289-.047l1.888.974V7.5a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V7s1.54-1.274 1.639-1.208M5 9a.5.5 0 0 0 0 1h6a.5.5 0 0 0 0-1zm0 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1z" />
                            <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2zm10-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1" />
                        </svg>
                        <p>Sertifikat</p>
                    </div>
                    <div class="include-isi">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-chat-left-text" viewBox="0 0 16 16">
                            <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4.414A2 2 0 0 0 3 11.586l-2 2V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12.793a.5.5 0 0 0 .854.353l2.853-2.853A1 1 0 0 1 4.414 12H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z" />
                            <path d="M3 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5M3 6a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9A.5.5 0 0 1 3 6m0 2.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5" />
                        </svg>
                        <p>Group Diskusi</p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="share">
                <h6 class="share-title">Share this event:</h6>
                <div class="share-list">
                    <button type="button" class="share-item" id="copyLinkBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-clipboard" viewBox="0 0 16 16">
                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1z" />
                            <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0z" />
                        </svg>
                        <p class="copy">Copy link</p>
                    </button>

                    <a id="fbShare" class="share-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-facebook" viewBox="0 0 16 16">
                            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                        </svg>
                    </a>

                    <a id="xShare" class="share-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-twitter-x" viewBox="0 0 16 16">
                            <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
                        </svg>
                    </a>

                    <a id="emailShare" href="#" class="share-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-envelope" viewBox="0 0 16 16">
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z" />
                        </svg>
                    </a>
                    <a id="waShare" class="share-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-whatsapp" viewBox="0 0 16 16">
                            <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="desc-box">
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-event nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Overview</button>
                <button class="nav-event nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Schedule</button>
                <button class="nav-event nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Terms & Condition</button>
            </div>
        </nav>
        <div class="overview-box tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                <p>Webinar Digital Marketing Mastery 2025: Strategies to Boost Your Brand in the Digital Era hadir sebagai ruang pembelajaran bagi siapa saja yang ingin memahami lebih dalam perkembangan dunia pemasaran digital. Dalam sesi berdurasi 2,5 jam ini, peserta akan dibekali wawasan terkini seputar tren digital marketing, mulai dari strategi social media, content marketing, hingga pemanfaatan Artificial Intelligence (AI) untuk meningkatkan efektivitas iklan digital.
                    <br><br>
                    Tidak hanya membahas teori, webinar ini juga menghadirkan studi kasus nyata dari brand lokal maupun internasional yang sukses membangun engagement serta meningkatkan konversi melalui strategi digital yang tepat. Webinar ini ditujukan bagi pemilik bisnis, praktisi marketing, mahasiswa, maupun freelancer yang ingin mengembangkan kompetensinya di bidang digital marketing. Setiap peserta akan mendapatkan e-certificate, materi presentasi, serta akses rekaman webinar untuk dipelajari kembali.
                    <br><br>
                    Acara akan berlangsung secara online melalui Zoom Meeting dan disiarkan langsung di YouTube, dengan menghadirkan narasumber dari kalangan praktisi bersertifikasi, CEO startup, dan content creator berpengalaman. Selain itu, tersedia sesi diskusi interaktif dan tanya jawab yang memungkinkan peserta membangun networking serta mendapatkan insight langsung dari para pakar.
                </p>
            </div>
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                <div class="scroll-schedule-box">
                    <div class="schedule-box">
                        <h6 class="title-schedule">Event Schedule</h6>
                        <div class="schedule-item-box">
                            <div class="schedule-line">1</div>
                            <div class="schedule-item">
                                <p class="time">14.30 - 17.00 WIB</p>
                                <p class="activity">Open Gate & Registration</p>
                                <p class="desc">Check-in and networking session with refreshments</p>
                            </div>
                        </div>
                        <br>
                        <div class="schedule-item-box">
                            <div class="schedule-line">1</div>
                            <div class="schedule-item">
                                <p class="time">15.00 - 16.30 WIB</p>
                                <p class="activity">Main Session: Digital Marketing Trends 2025</p>
                                <p class="desc">Deep dive into the latest strategies, tools, and techniques for successful digital marketing campaigns</p>
                            </div>
                        </div>
                        <br>
                        <div class="schedule-item-box">
                            <div class="schedule-line">1</div>
                            <div class="schedule-item">
                                <p class="time">16.30 - 17.00 WIB</p>
                                <p class="activity">Interactive Q&A Session</p>
                                <p class="desc">Ask questions directly to our expert speakers</p>
                            </div>
                        </div>
                        <br>
                        <div class="schedule-item-box">
                            <div class="schedule-line">1</div>
                            <div class="schedule-item">
                                <p class="time">17.00 - 17.30 WIB</p>
                                <p class="activity">Networking & Closing</p>
                                <p class="desc">Connect with fellow participants and speakers, certificate distribution</p>
                            </div>
                        </div>
                        <br>
                        <div class="schedule-item-box">
                            <div class="schedule-line">1</div>
                            <div class="schedule-item">
                                <p class="time">17.30 - 17.40 WIB</p>
                                <p class="activity">Documentation</p>
                                <p class="desc">Take a picture</p>
                            </div>
                        </div>
                        <br>
                        <div class="schedule-item-box">
                            <div class="schedule-line">1</div>
                            <div class="schedule-item">
                                <p class="time">17.40 - 17.50 WIB</p>
                                <p class="activity">Closing</p>
                                <p class="desc">Closing with Mc</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="terms-box tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">
                <h6>Terms & Condition</h6>
                <p>
                    1. Pendaftaran
                    Peserta wajib melakukan registrasi dan pembayaran sebelum tanggal acara
                    Konfirmasi pendaftaran akan dikirim via email maksimal 2x24 jam
                    Link meeting akan dikirimkan H-1 acara
                    <br>
                    2. Pembatalan & Refund
                    Pembatalan dapat dilakukan maksimal 7 hari sebelum acara
                    Refund akan diproses dalam 14 hari kerja
                    Biaya admin 10% akan dikenakan untuk setiap refund
                    <br>
                    3. Sertifikat
                    Sertifikat diberikan kepada peserta yang mengikuti minimal 80% acara
                    Sertifikat digital dapat diunduh melalui portal peserta 3 hari setelah acara
                    <br>
                    4. Hak Cipta & Rekaman
                    Seluruh materi dilindungi hak cipta dan hanya untuk penggunaan pribadi
                    Dilarang merekam, mendistribusikan, atau mempublikasikan ulang materi tanpa izin
                </p>
            </div>
        </div>
    </div>
    <div class="resource-box">
        <h5>Participant Resources</h5>
        <div class="participant-resources">
            <div class="resource-card">
                <div class="img-resource">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                        <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5" />
                        <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                    </svg>
                </div>
                <div class="resource-value">
                    <h6>Registration Form</h6>
                    <p>Available for registered participants</p>
                </div>
                <a class="link-share" href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                        <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                    </svg>
                </a>
            </div>

            <div class="resource-card locked">
                <div class="img-resource">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-check" viewBox="0 0 16 16">
                        <path d="M10.854 7.854a.5.5 0 0 0-.708-.708L7.5 9.793 6.354 8.646a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z" />
                        <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                    </svg>
                </div>
                <div class="resource-value">
                    <h6>Attendance Form</h6>
                    <p>Available for registered participants</p>
                </div>
                <a class="link-share" href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                        <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                    </svg>
                </a>
            </div>

            <div class="resource-card">
                <div class="img-resource">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                        <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                        <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                    </svg>
                </div>
                <div class="resource-value">
                    <h6>Location Map</h6>
                    <p>Available for registered participants</p>
                </div>
                <a class="link-share" href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                        <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                    </svg>
                </a>
            </div>

            <div class="resource-card locked">
                <div class="img-resource">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-award" viewBox="0 0 16 16">
                        <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z" />
                        <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z" />
                    </svg>
                </div>
                <div class="resource-value">
                    <h6>Certificate</h6>
                    <p>Available after event completion</p>
                </div>
                <a class="link-share" href="">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="lock-bi bi-lock" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M8 0a4 4 0 0 1 4 4v2.05a2.5 2.5 0 0 1 2 2.45v5a2.5 2.5 0 0 1-2.5 2.5h-7A2.5 2.5 0 0 1 2 13.5v-5a2.5 2.5 0 0 1 2-2.45V4a4 4 0 0 1 4-4M4.5 7A1.5 1.5 0 0 0 3 8.5v5A1.5 1.5 0 0 0 4.5 15h7a1.5 1.5 0 0 0 1.5-1.5v-5A1.5 1.5 0 0 0 11.5 7zM8 1a3 3 0 0 0-3 3v2h6V4a3 3 0 0 0-3-3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="feedback-box">
        <h5>Feedback & Reviews</h5>
        <h6>Participant Ratings</h6>
        <div class="rating-card">
            <div class="average-rating-box">
                <div class="scroll-review-box">
                    <div class="average-rating">
                        <div class="stars-event">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                        </div>
                        <p>Webinar yang sangat insightful! Materi disampaikan dengan jelas dan praktis. Saya langsung bisa menerapkan strategi yang diajarkan untuk bisnis saya.</p>
                        <span>-Vero Glorify</span>
                    </div>
                    <div class="average-rating">
                        <div class="stars-event">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                        </div>
                        <p>Pembicara sangat kompeten dan berpengalaman. Sesi Q&A juga sangat membantu untuk mendapatkan insight lebih detail. Highly recommended!</p>
                        <span>-Rina Pebri</span>
                    </div>
                    <div class="average-rating">
                        <div class="stars-event">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                        </div>
                        <p>Pembicara sangat kompeten dan berpengalaman. Sesi Q&A juga sangat membantu untuk mendapatkan insight lebih detail. Highly recommended!</p>
                        <span>-Rina Pebri</span>
                    </div>
                    <div class="average-rating">
                        <div class="stars-event">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#ffcc00" class="bi bi-star-fill" viewBox="0 0 16 16">
                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746 .592L8 13.187l-4.389 2.256z" />
                            </svg>
                        </div>
                        <p>Pembicara sangat kompeten dan berpengalaman. Sesi Q&A juga sangat membantu untuk mendapatkan insight lebih detail. Highly recommended!</p>
                        <span>-Rina Pebri</span>
                    </div>
                </div>
            </div>
            <div class="add-rating">
                <h5>Share your feedback</h5>
                <div class="add-stars">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" class="stars-bi bi-star-fill" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" class="stars-bi bi-star-fill" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" class="stars-bi bi-star-fill" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" class="stars-bi bi-star-fill" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" class="stars-bi bi-star-fill" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                </div>
                <textarea type="text" placeholder="Write your thoughts..."></textarea>
                <button>Submit Feedback</button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pageUrl = encodeURIComponent(window.location.href);
            const pageTitle = encodeURIComponent(document.title);

            const fb = document.getElementById('fbShare');
            const x = document.getElementById('xShare');
            const email = document.getElementById('emailShare');
            const wa = document.getElementById('waShare');

            // Buat URL share otomatis
            fb.href = `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`;
            x.href = `https://twitter.com/intent/tweet?text=${pageTitle}&url=${pageUrl}`;
            email.href = `mailto:?subject=${pageTitle}&body=Check this event: ${decodeURIComponent(pageUrl)}`;
            wa.href = `https://wa.me/?text=${pageTitle}%20${pageUrl}`;

            // Copy link ke clipboard
            const copyBtn = document.getElementById('copyLinkBtn');
            copyBtn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(window.location.href);
                    copyBtn.innerHTML = `<i class="bi bi-check2"></i> Copied!`;
                    copyBtn.style.backgroundColor = '#4b2dbf';
                    copyBtn.style.color = '#fff';
                    setTimeout(() => {
                        copyBtn.innerHTML = `<i class="bi bi-clipboard"></i> Copy link`;
                        copyBtn.style.backgroundColor = '#fff';
                        copyBtn.style.color = '#333';
                    }, 2000);
                } catch (err) {
                    alert('Gagal menyalin link');
                }
            });
        });
        //Rating
        const stars = document.querySelectorAll('.add-stars .stars-bi');
        let rating = 0;

        stars.forEach((star, index) => {
            star.addEventListener('mouseover', () => {
                resetStars();
                highlightStars(index);
            });
            star.addEventListener('mouseleave', () => {
                resetStars();
                highlightStars(rating - 1);
            });
            star.addEventListener('click', () => {
                rating = index + 1;
                resetStars();
                highlightStars(index);
                console.log(`Rating diberikan: ${rating}`);
            });
        });
        function highlightStars(index) {
            for (let i = 0; i <= index; i++) {
                stars[i].setAttribute('fill', '#ffcc00');
            }
        }
       function resetStars() {
        stars.forEach(s => s.setAttribute('fill', 'gray'));
    }
        
    </script>
</body>

</html>

@include('partials.footer-after-login')