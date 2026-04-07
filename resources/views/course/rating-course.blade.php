<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Penilaian Kelas - idSPORA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            padding-top: 100px;
        }
        .rating-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
        }
        .rating-card {
            background: white; border-radius: 24px; border: 1px solid #f1f5f9; padding: 2.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
        .star-box {
            display: flex; gap: 0.5rem; font-size: 2.5rem; color: #d1d5db; transition: all 0.2s;
        }
        .star-box i { cursor: pointer; transition: transform 0.2s; }
        .star-box i:hover { transform: scale(1.1); }
        .star-box i.active { color: #fbbf24; }
        
        .step-indicator {
            display: flex; align-items: center; justify-content: center; gap: 1rem; margin-bottom: 2.5rem;
        }
        .step-item {
            display: flex; align-items: center; gap: 0.5rem; font-weight: 700; font-size: 0.875rem; color: #94a3b8;
        }
        .step-item.active { color: #4f46e5; }
        .step-circle {
            width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            border: 2px solid #e2e8f0; font-size: 0.75rem;
        }
        .step-item.active .step-circle { border-color: #4f46e5; background: #4f46e5; color: white; }
        
        .textarea-box {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1rem; width: 100%; min-height: 120px;
            font-family: inherit; resize: vertical; outline: none; transition: all 0.2s;
        }
        .textarea-box:focus { border-color: #4f46e5; background: white; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }
        
        .submit-btn {
            background: #4f46e5; color: white; padding: 1rem 2.5rem; border-radius: 16px; font-weight: 800; border: none;
            transition: all 0.3s; width: 100%; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
        }
        .submit-btn:hover { background: #4338ca; transform: translateY(-2px); }
        .submit-btn:active { transform: translateY(0); }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")

    <div class="rating-container">
        <!-- Step UI -->
        <div class="step-indicator">
            <div class="step-item active">
                <span class="step-circle">1</span>
                Beri Penilaian
            </div>
            <div class="w-12 h-0.5 bg-slate-200"></div>
            <div class="step-item">
                <span class="step-circle">2</span>
                Cetak Sertifikat
            </div>
        </div>

        <div class="rating-card">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-black text-slate-900 mb-2">Penilaian Anda Sangat Berharga</h2>
                <p class="text-slate-500 font-medium">Bantu kami meningkatkan kualitas materi dan pengajaran kelas.</p>
            </div>

            <form action="{{ route('course.rating.store', $course->id) }}" method="POST" id="ratingForm">
                @csrf
                <input type="hidden" name="rating" id="course_rating_input" required>
                <input type="hidden" name="trainer_rating" id="trainer_rating_input" required>

                <!-- Course Rating -->
                <div class="mb-8 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="block text-lg font-bold text-slate-800 mb-4 text-center">
                        Bagaimana kurikulum dan materi di kelas ini?
                    </label>
                    <div id="course_stars" class="star-box justify-center text-4xl mb-2">
                        <i class="bi bi-star" data-value="1"></i>
                        <i class="bi bi-star" data-value="2"></i>
                        <i class="bi bi-star" data-value="3"></i>
                        <i class="bi bi-star" data-value="4"></i>
                        <i class="bi bi-star" data-value="5"></i>
                    </div>
                    <p id="course_rating_text" class="text-center text-sm font-bold text-slate-400">Pilih bintang</p>
                </div>

                <!-- Trainer Rating -->
                <div class="mb-10 p-6 bg-slate-50 rounded-2xl border border-slate-100">
                    <label class="block text-lg font-bold text-slate-800 mb-4 text-center">
                        Bagaimana kualitas penyampaian Trainer?
                    </label>
                    <div id="trainer_stars" class="star-box justify-center text-4xl mb-2">
                        <i class="bi bi-star" data-value="1"></i>
                        <i class="bi bi-star" data-value="2"></i>
                        <i class="bi bi-star" data-value="3"></i>
                        <i class="bi bi-star" data-value="4"></i>
                        <i class="bi bi-star" data-value="5"></i>
                    </div>
                    <p id="trainer_rating_text" class="text-center text-sm font-bold text-slate-400">Pilih bintang</p>
                </div>

                <!-- Feedback -->
                <div class="mb-10">
                    <label class="block text-lg font-bold text-slate-800 mb-3 ml-1">
                        Feedback & Testimoni
                    </label>
                    <textarea name="comment" class="textarea-box" 
                        placeholder="Tuliskan pengalaman belajarmu di sini..." required></textarea>
                </div>

                <button type="submit" class="submit-btn text-lg">
                    <i class="bi bi-send-fill me-2 text-base"></i> Kirim Penilaian
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseStars = document.querySelectorAll('#course_stars i');
            const trainerStars = document.querySelectorAll('#trainer_stars i');
            const courseInput = document.getElementById('course_rating_input');
            const trainerInput = document.getElementById('trainer_rating_input');
            const courseText = document.getElementById('course_rating_text');
            const trainerText = document.getElementById('trainer_rating_text');

            const ratingLabels = {
                1: 'Sangat Kurang',
                2: 'Kurang',
                3: 'Cukup',
                4: 'Bagus',
                5: 'Sangat Bagus!'
            };

            function updateStars(container, stars, value, textEl, input) {
                input.value = value;
                textEl.innerText = ratingLabels[value];
                textEl.className = 'text-center text-sm font-black text-amber-500';
                
                stars.forEach(star => {
                    const starVal = star.getAttribute('data-value');
                    if (starVal <= value) {
                        star.classList.replace('bi-star', 'bi-star-fill');
                        star.classList.add('active');
                    } else {
                        star.classList.replace('bi-star-fill', 'bi-star');
                        star.classList.remove('active');
                    }
                });
            }

            courseStars.forEach(star => {
                star.addEventListener('click', () => {
                    updateStars('course', courseStars, star.getAttribute('data-value'), courseText, courseInput);
                });
            });

            trainerStars.forEach(star => {
                star.addEventListener('click', () => {
                    updateStars('trainer', trainerStars, star.getAttribute('data-value'), trainerText, trainerInput);
                });
            });

            document.getElementById('ratingForm').addEventListener('submit', function(e) {
                if (!courseInput.value || !trainerInput.value) {
                    e.preventDefault();
                    alert('Silakan berikan rating untuk Kursus dan Trainer terlebih dahulu!');
                }
            });
        });
    </script>
    
    @include('partials.footer-after-login')
</body>
</html>