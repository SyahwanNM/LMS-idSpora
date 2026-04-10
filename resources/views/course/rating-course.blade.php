<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <title>Penilaian Kelas - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Halaman ini mengikuti style di app.css (RATING COURSE + CETAK SERTIFIKAT).
             Override ringan agar responsif & rapi seperti screenshot. */
        .rating_box_luar { max-width: 1000px; width: 100%; }
        .box_dalam_penilaian, .box_feedback { border-color: #d9d9d9; border-radius: 14px; }
        .pertanyaan_penilaian { gap: 16px; }
        .pertanyaan_penilaian h4 { margin: 0; font-size: 18px; font-weight: 600; color: #111827; }
        .stars i, .stars_trainer i { cursor: pointer; color: #cfcfcf; }
        .stars i.active, .stars_trainer i.active { color: #FFD33B; }
        .checkmark { font-size: 22px; color: #16a34a; display: none; }
        .tombol_next_sertifikat { margin-left: 0; justify-content: flex-end; }
        .next_halaman_sertif { font-weight: 700; }
        @media (max-width: 768px) {
            .rating_box_luar { width: 92%; }
            .box_dalam_penilaian, .box_feedback { padding: 22px; }
            .pertanyaan_penilaian { flex-direction: column; align-items: flex-start; }
            .pertanyaan_penilaian h4 { width: 100%; }
        }
    </style>
</head>
<body>
    @include('partials.navbar-after-login')

    <div class="rating_box_luar">
        <div class="steps">
            <div class="step active">
                <span class="circle">1</span>
                Beri Penilaian Kelas
            </div>
            <div class="line"></div>
            <div class="step">
                <span class="circle">2</span>
                Cetak Sertifikat
            </div>
        </div>

        <form action="{{ route('course.rating.store', $course->id) }}" method="POST" id="ratingForm">
            @csrf
            <input type="hidden" name="rating" id="course_rating_input" value="">
            <input type="hidden" name="trainer_rating" id="trainer_rating_input" value="">

            <div class="box_dalam_penilaian">
                <h3 class="mb-1" style="font-weight:700;color:#111827;">Berikan penilaian Anda</h3>
                <p class="mb-0" style="color:#6b7280;">Bantu kami meningkatkan kualitas kelas dengan memberi rating dan feedback.</p>

                <div class="pertanyaan_penilaian">
                    <h4>Bagaimana kurikulum dan proses belajar di kelas ini?</h4>
                    <div class="d-flex align-items-center gap-3">
                        <div class="stars" id="course_stars" aria-label="Rating kelas">
                            <i class="bi bi-star" data-value="1"></i>
                            <i class="bi bi-star" data-value="2"></i>
                            <i class="bi bi-star" data-value="3"></i>
                            <i class="bi bi-star" data-value="4"></i>
                            <i class="bi bi-star" data-value="5"></i>
                        </div>
                        <i class="bi bi-check-lg checkmark" id="course_check" aria-hidden="true"></i>
                    </div>
                </div>

                <div class="pertanyaan_penilaian">
                    <h4>Bagaimana Trainer mengajar dalam proses belajar di kelas ini?</h4>
                    <div class="d-flex align-items-center gap-3">
                        <div class="stars_trainer" id="trainer_stars" aria-label="Rating trainer">
                            <i class="bi bi-star" data-value="1"></i>
                            <i class="bi bi-star" data-value="2"></i>
                            <i class="bi bi-star" data-value="3"></i>
                            <i class="bi bi-star" data-value="4"></i>
                            <i class="bi bi-star" data-value="5"></i>
                        </div>
                        <i class="bi bi-check-lg checkmark" id="trainer_check" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <div class="box_feedback">
                <h4 class="mb-3" style="font-weight:700;color:#111827;">Feedback</h4>
                <div class="comment-box">
                    <textarea
                        name="comment"
                        rows="4"
                        placeholder="Ceritakan pengalaman belajar Anda selama mengikuti kelas ini. Ini akan membantu kami meningkatkan kualitas materi yang diajarkan."
                    ></textarea>
                </div>

                <div class="tata_cara_feedback">
                    <div style="font-weight:700; margin-bottom:4px;">Bingung cara memberikan feedback?</div>
                    <div style="color:#4b5563; font-size:14px; line-height:1.5;">
                        Tuliskan feedback secara spesifik. Jika ada pembahasan yang kurang jelas, Anda bisa menuliskannya agar kami dapat memperbaiki kelas ini.
                    </div>
                </div>

                <div class="tombol_next_sertifikat" style="display:flex;">
                    <button type="submit" class="next_halaman_sertif">Next</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const courseStars = document.querySelectorAll('#course_stars i');
            const trainerStars = document.querySelectorAll('#trainer_stars i');
            const courseInput = document.getElementById('course_rating_input');
            const trainerInput = document.getElementById('trainer_rating_input');
            const courseCheck = document.getElementById('course_check');
            const trainerCheck = document.getElementById('trainer_check');

            function applyStars(stars, value) {
                stars.forEach((star) => {
                    const v = parseInt(star.getAttribute('data-value') || '0', 10);
                    const active = v <= value;

                    if (active) {
                        star.classList.remove('bi-star');
                        star.classList.add('bi-star-fill', 'active');
                    } else {
                        star.classList.remove('bi-star-fill', 'active');
                        star.classList.add('bi-star');
                    }
                });
            }

            function bindStarGroup(stars, inputEl, checkEl) {
                stars.forEach((star) => {
                    star.addEventListener('click', () => {
                        const value = parseInt(star.getAttribute('data-value') || '0', 10);
                        inputEl.value = String(value);
                        applyStars(stars, value);
                        if (checkEl) checkEl.style.display = value > 0 ? 'inline-flex' : 'none';
                    });
                });
            }

            bindStarGroup(courseStars, courseInput, courseCheck);
            bindStarGroup(trainerStars, trainerInput, trainerCheck);

            document.getElementById('ratingForm')?.addEventListener('submit', function (e) {
                const courseVal = parseInt(courseInput.value || '0', 10);
                const trainerVal = parseInt(trainerInput.value || '0', 10);

                if (courseVal < 1 || trainerVal < 1) {
                    e.preventDefault();
                    alert('Silakan beri rating untuk kelas dan trainer terlebih dahulu.');
                }
            });
        });
    </script>

    @include('partials.footer-after-login')
</body>
</html>