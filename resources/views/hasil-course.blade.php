<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include("partials.navbar-after-login")
    <div class="box_luar_hasil">
        <div class="box_kiri_hasil">
            <h5>Tanggal Ujian : 06 0ct 2025 pukul 08:35:20</h5>
            <div class="informasi_hasil">
                <div class="score_hasil">
                    <p>Total Soal</p>
                    <h3 class="deactive">4</h3>
                </div>
                <div class="score_hasil">
                    <p>Score</p>
                    <h3>80</h3>
                </div>
            </div>
            <p class="batas_minimum_nilai">Score anda belum memenuhi batas minimum yang ditentukan pada ujian ini: 75.</p>
            <p class="batas_minimum_nilai">Mohon untuk mempelajari kembali modul-modul terkait: Android Studio.</p>
        </div>
        <div class="box_kanan_hasil">
            <div class="card_soal">
                <div class="soal_hasil">
                    <h5>1. Apa yang dimaksud dengan android studio?</h5>
                    <h3 class="true">1</h3>
                </div>
                <div class="pilihan_box">
                    <div class="pilihan_jawaban_benar">
                        <p class="bulatan_jawaban_benar">o</p>
                        <p class="jawaban_pilihan_benar">a. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">b. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">c. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">d. lorem ipsum</p>
                    </div>

                </div>
            </div>
            <div class="card_soal">
                <div class="soal_hasil">
                    <h5>2. Apa yang dimaksud dengan android studio?</h5>
                    <h3 class="true">2</h3>
                </div>
                <div class="pilihan_box">
                    <div class="pilihan_jawaban_benar">
                        <p class="bulatan_jawaban_benar">o</p>
                        <p class="jawaban_pilihan_benar">a. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">b. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">c. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">d. lorem ipsum</p>
                    </div>

                </div>
            </div>
            <div class="card_soal">
                <div class="soal_hasil">
                    <h5>3. Apa yang dimaksud dengan android studio?</h5>
                    <h3 class="false">3</h3>
                </div>
                <div class="pilihan_box">
                    <div class="pilihan_jawaban_benar">
                        <p class="bulatan_jawaban_benar">o</p>
                        <p class="jawaban_pilihan_benar">a. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">b. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban_salah">
                        <p class="bulatan_jawaban_salah">o</p>
                        <p class="jawaban_pilihan_salah">c. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">d. lorem ipsum</p>
                    </div>

                </div>
            </div>
            <div class="card_soal">
                <div class="soal_hasil">
                    <h5>4. Apa yang dimaksud dengan android studio?</h5>
                    <h3 class="true">4</h3>
                </div>
                <div class="pilihan_box">
                    <div class="pilihan_jawaban_benar">
                        <p class="bulatan_jawaban_benar">o</p>
                        <p class="jawaban_pilihan_benar">a. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">b. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">c. lorem ipsum</p>
                    </div>
                    <div class="pilihan_jawaban">
                        <p class="bulatan_jawaban">o</p>
                        <p class="jawaban_pilihan">d. lorem ipsum</p>
                    </div>

                </div>
            </div>
            <button class="kembali_course">
                <div>
                    <p>Back To Course</p>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right" viewBox="0 0 16 16">
                        <path d="M6 12.796V3.204L11.481 8zm.659.753 5.48-4.796a1 1 0 0 0 0-1.506L6.66 2.451C6.011 1.885 5 2.345 5 3.204v9.592a1 1 0 0 0 1.659.753" />
                    </svg>
                </div>
            </button>
        </div>
    </div>
</body>

</html>