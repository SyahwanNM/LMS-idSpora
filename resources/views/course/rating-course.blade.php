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
            padding-top: 80px;
        }

        .main-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* Step Indicator Styles */
        .step-container {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.25rem 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 3rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            color: #1e293b;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .step-item.active .step-circle {
            background: #fbbf24;
            color: #000;
        }

        .step-line {
            width: 80px;
            height: 1px;
            background: #e2e8f0;
        }

        /* Rating Card Styles */
        .rating-card {
            background: white;
            border-radius: 24px;
            padding: 3rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
        }

        .star-box {
            display: flex;
            gap: 0.75rem;
            font-size: 2.75rem;
            color: #e2e8f0;
            justify-content: center;
        }

        .star-box i {
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .star-box i:hover {
            transform: scale(1.15);
        }

        .star-box i.active {
            color: #fbbf24;
        }

        .textarea-box {
            background: #fcfcfc;
            border: 1.5px solid #eef2f6;
            border-radius: 16px;
            padding: 1.25rem;
            width: 100%;
            min-height: 140px;
            font-family: inherit;
            resize: none;
            outline: none;
            transition: all 0.2s;
            font-size: 1rem;
        }

        .textarea-box:focus {
            border-color: #fbbf24;
            background: white;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1);
        }

        .submit-btn {
            background: #fbbf24;
            color: #000;
            padding: 1.25rem 2.5rem;
            border-radius: 50px;
            font-weight: 800;
            border: none;
            transition: all 0.3s;
            width: 100%;
            font-size: 1.125rem;
            box-shadow: 0 10px 15px -3px rgba(251, 191, 36, 0.2);
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -3px rgba(251, 191, 36, 0.3);
        }

        .submit-btn:disabled {
            background: #f1f5f9;
            color: #94a3b8;
            box-shadow: none;
            transform: none;
            cursor: not-allowed;
        }

        .rating-section {
            background: #fafafa;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #f1f5f9;
        }

        .section-label {
            display: block;
            font-size: 1.15rem;
            font-weight: 800;
            color: #1a1b1e;
            margin-bottom: 1.5rem;
            text-align: center;
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")

    <div class="main-container">
        <!-- Step Indicator -->
        <div class="step-container">
            <div class="step-item active">
                <span class="step-circle">1</span>
                Beri Penilaian Kelas
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <span class="step-circle">2</span>
                Cetak Sertifikat
            </div>
        </div>

        <div class="rating-card">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-black text-slate-900 mb-3">Bagaimana pengalaman belajar Anda?</h2>
                <p class="text-slate-500 font-medium">Penilaian Anda membantu kami terus berkembang.</p>
            </div>

            <form action="{{ route('course.rating.store', $course->id) }}" method="POST" id="ratingForm">
                @csrf
                <input type="hidden" name="rating" id="course_rating_input" required>
                <input type="hidden" name="trainer_rating" id="trainer_rating_input" required>

                <!-- Course Rating -->
                <div class="rating-section">
                    <label class="section-label">
                        Kualitas Materi & Kurikulum
                    </label>
                    <div id="course_stars" class="star-box">
                        <i class="bi bi-star" data-value="1"></i>
                        <i class="bi bi-star" data-value="2"></i>
                        <i class="bi bi-star" data-value="3"></i>
                        <i class="bi bi-star" data-value="4"></i>
                        <i class="bi bi-star" data-value="5"></i>
                    </div>
                    <p id="course_rating_text" class="text-center text-sm font-bold text-slate-400 mt-4 h-5">Pilih rating</p>
                </div>

                <!-- Trainer Rating -->
                <div class="rating-section">
                    <label class="section-label">
                        Kualitas Penyampaian Trainer
                    </label>
                    <div id="trainer_stars" class="star-box">
                        <i class="bi bi-star" data-value="1"></i>
                        <i class="bi bi-star" data-value="2"></i>
                        <i class="bi bi-star" data-value="3"></i>
                        <i class="bi bi-star" data-value="4"></i>
                        <i class="bi bi-star" data-value="5"></i>
                    </div>
                    <p id="trainer_rating_text" class="text-center text-sm font-bold text-slate-400 mt-4 h-5">Pilih rating</p>
                </div>

                <!-- Feedback -->
                <div class="mb-10">
                    <label class="block text-lg font-bold text-slate-800 mb-4 ml-1">
                        Saran & Kesan (opsional)
                    </label>
                    <textarea name="comment" class="textarea-box" 
                        placeholder="Apa yang paling Anda sukai atau apa yang perlu diperbaiki?"></textarea>
                </div>

                <div class="flex justify-center">
                    <button type="submit" id="submitBtn" class="submit-btn" disabled>
                        Simpan Penilaian
                    </button>
                </div>
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
            const submitBtn = document.getElementById('submitBtn');

            const ratingLabels = {
                1: 'Sangat Buruk',
                2: 'Buruk',
                3: 'Cukup',
                4: 'Bagus',
                5: 'Sangat Luar Biasa!'
            };

            function checkSubmit() {
                if (courseInput.value && trainerInput.value) {
                    submitBtn.disabled = false;
                }
            }

            function updateStars(container, stars, value, textEl, input) {
                input.value = value;
                textEl.innerText = ratingLabels[value];
                textEl.className = 'text-center text-sm font-black text-amber-500 mt-4 h-5';
                
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
                checkSubmit();
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
        });
    </script>
    
    @include('partials.footer-after-login')
</body>
</html>