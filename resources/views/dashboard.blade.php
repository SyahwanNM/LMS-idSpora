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
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-x: hidden;
            font-family: "Poppins";
            color: black;
        }


        .gambar-carousel {
            width: 1350px;
            height: 300px;
            margin: 35px;
            object-fit: cover;
        }

        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .col-md-4 {
            padding-left: 5px;
            padding-right: 5px;
        }

        .kotak {
            width: 400px;
            height: auto;
            margin: 5px 40px;
            padding: 15px;
            box-shadow: 0 0 10px rgb(219, 215, 215);
            color: black;
            border-radius: 20px;
        }

        #gradesChart {
            display: block;
            margin: 0 auto;
            max-width: 180px;
        }

        .judul {
            text-align: left;
            margin: 0px 50px;
            font-weight: 600;
        }

        .bulan {
            text-align: left;
            margin: 0px 30px;
            padding: 0px 20px 13px 20px;
            font-weight: 400;
            color: grey;
        }

        .stat-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .keterangan {
            flex: 1;
        }

        .chart-wrapper {
            flex-shrink: 0;
            width: 180px;
            height: 180px;
        }

        .chart-wrapper canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .stat-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-item img {
            width: 40px;
            height: 40px;
            margin-right: 15px;
        }

        .stat-text .label {
            font-size: 14px;
            color: gray;
            margin: 0;
        }

        .stat-text .value {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }

        .task-item {
            margin: 20px 50px;
            padding: 8px 0;

        }

        .task-score {
            float: right;
            color: gray;
        }

        .progress {
            height: 10px;
        }

        .progress-bar {
            background-color: #F4C430;
            height: 10px;
        }

        #hoursChart {
            max-height: 250px;
            margin-top: 20px;
        }

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
            margin-left: 40px;
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
            margin-left: 40px;

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

        .keterangan {
            color: grey;
            font-size: 13px;
            padding: 2px;
        }

        .box-harga {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .coret {
            text-decoration: line-through;
            color: black;
            margin-top: 10px;
        }

        .harga-teks {
            font-size: 20px;
            font-weight: bold;
            color: #f4c542;
            margin-top: -20px;
        }

        .harga {
            display: flex;
            flex-direction: column;

        }

        .btn-regist {
            width: auto;
            padding: 8px 16px;
            background: #f4c542;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }

        .box5 {
            margin-left: 50px;
            margin-bottom: 50px;
            padding: 10px;
        }

        .jenis-pelatihan {
            display: flex;
            align-items: center;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 10px;
            scroll-behavior: smooth;
        }

        .nama-jenis {
            background: #f4c542;
            color: #fff;
            padding: 10px;
            margin-right: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
   
    <div class="box1">
        <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                    aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                    aria-label="Slide 3"></button>
            </div>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img class="gambar-carousel" src="{{ asset('aset/ai.png') }}" class="d-block w-100" alt="Slide 1">
                </div>
                <div class="carousel-item">
                    <img class="gambar-carousel" src="{{ asset('aset/ai2.png') }}" class="d-block w-100" alt="Slide 2">
                </div>
                <div class="carousel-item">
                    <img class="gambar-carousel" src="{{ asset('aset/ai3.png') }}" class="d-block w-100" alt="Slide 3">
                </div>
            </div>
        </div>

        <div class="box2">
            <div class="row gx-2">
                <div class="col-md-4">
                    <div class="kotak">
                        <h3 class="judul">Task Progress</h3>
                        <div class="task-item">
                            <span class="task-title">Web Programming</span>
                            <span class="task-score">5/10</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 50%"></div>
                            </div>
                        </div>
                        <div class="task-item">
                            <span class="task-title">Data and Structure</span>
                            <span class="task-score">4/15</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 20%"></div>
                            </div>
                        </div>
                        <div class="task-item">
                            <span class="task-title">Artificiall Intelligence</span>
                            <span class="task-score">2/15</span>
                            <div class="progress">
                                <div class="progress-bar" style="width: 30%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kotak">
                        <h3 class="judul">Statistics</h3>
                        <p class="bulan">Januari - Juni 2025</p>
                        <div class="stat-container">
                            <div class="keterangan">
                                <div class="stat-item">
                                    <img src="{{ asset('aset/logo-kehadiran.png') }}" alt="Kehadiran">
                                    <div class="stat-text">
                                        <p class="label">Kehadiran</p>
                                        <p class="value">90%</p>
                                    </div>
                                </div>

                                <div class="stat-item">
                                    <img src="{{ asset('aset/logo-ujian.png') }}" alt="Tugas & Ujian">
                                    <div class="stat-text">
                                        <p class="label">Tugas & Ujian</p>
                                        <p class="value">70%</p>
                                    </div>
                                </div>

                                <div class="stat-item">
                                    <img src="{{ asset('aset/logo-kuis.png') }}" alt="Kuis">
                                    <div class="stat-text">
                                        <p class="label">Kuis</p>
                                        <p class="value">85%</p>
                                    </div>
                                </div>
                            </div>

                            <div class="chart-wrapper">
                                <canvas id="gradesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="kotak">
                        <h3 class="judul">Hours Spent</h3>
                        <canvas id="hoursChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="box3">
            <div class="header">
                <h3>Lanjutkan Belajar</h3>
                <a href="#">Lihat Lainnya</a>
            </div>

            <div class="card-container-course">
                <div class="card-course">
                    <div class="card-image-course-wrapper">
                        <img src="{{ asset('aset/logo.png') }}" alt="Course">
                        <img class="bookmark" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge">Beginner</span>
                    </div>

                    <div class="card-body">
                        <h4>Learn Artificial Intelligence Python</h4>
                        <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt...</p>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Programming</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                                <div><img src="{{ asset('aset/ikon-bintang.png') }}"> <span>5.0</span></div>
                            </div>
                        </div>

                        <div class="pembicara">
                            <div class="pembicara-left">
                                <img src="{{ asset('aset/profile.png') }}" alt="pembicara">
                                <span>Sianunamanya</span>
                            </div>
                            <div class="pembicara-right">
                                <div><img src="{{ asset('aset/ikon-playvideo.png') }}"> <span>10 Videos</span></div>
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
                <div class="card-course">
                    <div class="card-image">
                        <img src="{{ asset('aset/code.png') }}" alt="Course">
                        <img class="bookmark" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge">Beginner</span>
                    </div>

                    <div class="card-body">
                        <h4>Learn Artificial Intelligence Python</h4>
                        <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt...</p>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Programming</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                                <div><img src="{{ asset('aset/ikon-bintang.png') }}"> <span>5.0</span></div>
                            </div>
                        </div>

                        <div class="pembicara">
                            <div class="pembicara-left">
                                <img src="{{ asset('aset/profile.png') }}" alt="pembicara">
                                <span>Sianunamanya</span>
                            </div>
                            <div class="pembicara-right">
                                <div><img src="{{ asset('aset/ikon-playvideo.png') }}"> <span>10 Videos</span></div>
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
                <div class="card-course">
                    <div class="card-image-course-wrapper">
                        <img src="{{ asset('aset/code.png') }}" alt="Course">
                        <img src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge">Beginner</span>
                    </div>
                    <div class="card-body">
                        <h4>Learn Artificial Intelligence Python</h4>
                        <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt...</p>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Programming</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                                <div><img src="{{ asset('aset/ikon-bintang.png') }}"> <span>5.0</span></div>
                            </div>
                        </div>

                        <div class="pembicara">
                            <div class="pembicara-left">
                                <img src="{{ asset('aset/profile.png') }}" alt="pembicara">
                                <span>Sianunamanya</span>
                            </div>
                            <div class="pembicara-right">
                                <div><img src="{{ asset('aset/ikon-playvideo.png') }}"> <span>10 Videos</span></div>
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
                <div class="card-course">
                    <div class="card-image-course-wrapper">
                        <img src="{{ asset('aset/code.png') }}" alt="Course">
                        <img class="bookmark" src="{{ asset('aset/ikon-bookmark.png') }}" alt="Bookmark">
                        <span class="badge">Beginner</span>
                    </div>

                    <div class="card-body">
                        <h4>Learn Artificial Intelligence Python</h4>
                        <p class="desc">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt...</p>

                        <div class="tags">
                            <div class="tags-left">
                                <span>Programming</span>
                                <span>AI</span>
                            </div>
                            <div class="tags-right">
                                <div><img src="{{ asset('aset/ikon-participant.png') }}"><span>118</span></div>
                                <div><img src="{{ asset('aset/ikon-bintang.png') }}"> <span>5.0</span></div>
                            </div>
                        </div>

                        <div class="pembicara">
                            <div class="pembicara-left">
                                <img src="{{ asset('aset/profile.png') }}" alt="pembicara">
                                <span>Sianunamanya</span>
                            </div>
                            <div class="pembicara-right">
                                <div><img src="{{ asset('aset/ikon-playvideo.png') }}"> <span>10 Videos</span></div>
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
            </div>
        </div>
    </div>

    <div class="box4">
        <div class="header">
            <h3>Event Mendatang</h3>
            <a href="#">Lihat Lainnya</a>
        </div>

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
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
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
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
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
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
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
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
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
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
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
                    <p class="desc-event">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
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

    <div class="box5">
        <h3 class="jenis-text">Jenis Pelatihan</h3>
        <div class="row">
            <div class="jenis-pelatihan">
                <span class="nama-jenis">Artificial Intelligence</span>
                <span class="nama-jenis">Machine Learning</span>
                <span class="nama-jenis">Mental Health</span>
                <span class="nama-jenis">Digital Marketing</span>
                <span class="nama-jenis">Graphic Design</span>
                <span class="nama-jenis">Business</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    const { width } = chart;
                    const { height } = chart;
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
                    ctx.fillText("Grades Completed", width / 2.9, height / 1.7);

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
                datasets: [
                    {
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
                    x: { stacked: true },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { callback: value => value + ' Hr' }
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



</body>

</html>