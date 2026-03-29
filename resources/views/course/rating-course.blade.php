<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Learner</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    @include("partials.navbar-after-login")
    <div class="rating_box_luar">
    <div class="steps">
                <div class="step">
                    <span class="circle">1</span>
                    Beri Penilaian Kelas
                </div>
                <div class="line"></div>
                <div class="step active">
                    <span class="circle">2</span>
                    Cetak Sertifikat
                </div>
            </div>
        <div class="box_dalam_penilaian">
            <div>
                <h4>Berikan Penilaian Anda</h4>
                <p>Sebelum cetak sertifikat, berikan penilaian terlebih dahulu</p>
            </div>
            <div class="pertanyaan_penilaian">
                <h4>Bagaimana kurikulum dan proses belajar di kelas ini?</h4>
                <div style="display:flex; align-items:center;">
                    <div class="stars">
                        <i class="star" data-value="1">☆</i>
                        <i class="star" data-value="2">☆</i>
                        <i class="star" data-value="3">☆</i>
                        <i class="star" data-value="4">☆</i>
                        <i class="star" data-value="5">☆</i>
                    </div>
                    <button class="submit-rating">
                        ✓
                    </button>
                </div>
            </div>
            <div class="pertanyaan_penilaian">
                <h4>Bagaimana Trainer mengajar dalam proses belajar di kelas ini?</h4>
                <div style="display:flex; align-items:center;">
                    <div class="stars_trainer">
                        <i class="star" data-value="1">☆</i>
                        <i class="star" data-value="2">☆</i>
                        <i class="star" data-value="3">☆</i>
                        <i class="star" data-value="4">☆</i>
                        <i class="star" data-value="5">☆</i>
                    </div>
                    <button class="submit-rating">
                        ✓
                    </button>
                </div>
            </div>
        </div>
        <div class="box_feedback">
            <h4>Feedback</h4>
            <div class="comment-box">
                <textarea name="comment"
                    placeholder="Ceritakan pengalaman mengesankan Anda selama mempelajari kelas ini. Beri tahu siswa lain mengenai kualitas materi yang diajarkan..."
                    rows="5"></textarea>
            </div>
            <div class="tata_cara_feedback">
                <h4>Bingung cara memberikan feedback?</h4>
                <p>
                    Tuliskan lah feedback secara spesifik. Jika Anda memberikan rating 1-4,
                    maka beritahu kami apa yang harus kami tingkatkan lagi pada kelas ini.
                    Jika Anda memberikan rating 5, Anda bisa ceritakan pengalaman
                    mengesankan selama belajar di kelas ini.
                </p>
            </div>
        </div>
    </div>
    <div class="tombol_next_sertifikat">
        <button class="next_halaman_sertif">Next</button>
    </div>
</body>

</html>