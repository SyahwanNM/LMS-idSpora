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
            max-width: 960px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem;
        }

        /* Card Base */
        .feedback-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }

        /* Step Indicator */
        .step-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            color: #1e293b;
        }

        .step-item.inactive {
            color: #94a3b8;
        }

        .step-num {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e2e8f0;
            color: #64748b;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .step-item.active .step-num {
            background: #fbbf24;
            color: #000;
        }

        .step-line {
            width: 80px;
            height: 2px;
            background: #e2e8f0;
        }

        /* Rating Row */
        .rating-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            padding: 1.25rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .rating-row:last-child {
            border-bottom: none;
        }

        .rating-question {
            font-weight: 700;
            font-size: 1.15rem;
            color: #1e293b;
            flex: 1;
        }

        .stars-container {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .star-group {
            display: flex;
            gap: 0.75rem;
            font-size: 2rem;
            color: #cbd5e1;
        }

        .star-group i {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .star-group i:hover {
            transform: scale(1.1);
        }

        .star-group i.active {
            color: #fbbf24;
        }

        .check-status {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #cbd5e1;
            transition: all 0.3s ease;
            opacity: 0.5;
        }

        .check-status.active {
            color: #000;
            opacity: 1;
        }

        /* Textarea */
        .feedback-textarea {
            width: 100%;
            min-height: 180px;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            padding: 1.5rem;
            font-size: 1rem;
            color: #1e293b;
            resize: none;
            outline: none;
            transition: all 0.2s;
            margin-top: 1rem;
            background-color: #ffffff;
        }

        .feedback-textarea:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 4px rgba(251, 191, 36, 0.1);
            background-color: #fff;
        }

        .feedback-textarea::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .submit-btn {
            background: #1e293b;
            color: white;
            padding: 1.25rem 4rem;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1.1rem;
            transition: all 0.3s;
            margin-top: 2rem;
            box-shadow: 0 10px 20px rgba(30, 41, 59, 0.15);
            border: none;
        }

        .submit-btn:hover:not(:disabled) {
            background: #0f172a;
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(30, 41, 59, 0.2);
        }

        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        @media (max-width: 768px) {
            .rating-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            .stars-container {
                width: 100%;
                justify-content: space-between;
            }
            .step-wrapper {
                flex-direction: column;
                gap: 1rem;
            }
            .step-line {
                display: none;
            }
        }
    </style>
</head>
<body>
    @include("partials.navbar-after-login")

    <div class="main-container">
        <!-- Step Indicator Card -->
        <div class="feedback-card">
            <div class="step-wrapper">
                <div class="step-item active">
                    <span class="step-num">1</span>
                    <span>Give Course Rating</span>
                </div>
                <div class="step-line"></div>
                <div class="step-item inactive">
                    <span class="step-num">2</span>
                    <span>Print Certificate</span>
                </div>
            </div>
        </div>

        <form action="{{ route('course.rating.store', $course->id) }}" method="POST" id="ratingForm">
            @csrf
            <input type="hidden" name="rating" id="course_rating_input" required>
            <input type="hidden" name="trainer_rating" id="trainer_rating_input" required>

            <!-- Rating Card -->
            <div class="feedback-card">
                <div class="mb-8">
                    <h2 class="text-2xl font-extrabold text-slate-900">Give Course Rating</h2>
                    <p class="text-base text-slate-500 font-semibold mt-1">Before printing the certificate, please provide an assessment first.</p>
                </div>

                <div class="space-y-2">
                    <!-- Course Rating -->
                    <div class="rating-row">
                        <div class="rating-question">How is the curriculum and learning process in this class?</div>
                        <div class="stars-container">
                            <div id="course_stars" class="star-group">
                                <i class="bi bi-star" data-value="1"></i>
                                <i class="bi bi-star" data-value="2"></i>
                                <i class="bi bi-star" data-value="3"></i>
                                <i class="bi bi-star" data-value="4"></i>
                                <i class="bi bi-star" data-value="5"></i>
                            </div>
                            <div id="course_check" class="check-status">
                                <i class="bi bi-check-lg" style="font-size: 2rem; -webkit-text-stroke: 1.5px;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Trainer Rating -->
                    <div class="rating-row">
                        <div class="rating-question">How did the trainer teach during this class?</div>
                        <div class="stars-container">
                            <div id="trainer_stars" class="star-group">
                                <i class="bi bi-star" data-value="1"></i>
                                <i class="bi bi-star" data-value="2"></i>
                                <i class="bi bi-star" data-value="3"></i>
                                <i class="bi bi-star" data-value="4"></i>
                                <i class="bi bi-star" data-value="5"></i>
                            </div>
                            <div id="trainer_check" class="check-status">
                                <i class="bi bi-check-lg" style="font-size: 2rem; -webkit-text-stroke: 1.5px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Card -->
            <div class="feedback-card">
                <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Feedback</h2>
                <textarea name="comment" class="feedback-textarea" 
                    placeholder="Tell us about your impressive experience while learning in this class. Let other students know about the quality of the material taught"></textarea>
            </div>

            <div class="flex justify-center mb-12">
                <button type="submit" id="submitBtn" class="submit-btn" disabled>
                    Save Rating
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const courseStars = document.querySelectorAll('#course_stars i');
            const trainerStars = document.querySelectorAll('#trainer_stars i');
            const courseInput = document.getElementById('course_rating_input');
            const trainerInput = document.getElementById('trainer_rating_input');
            const courseCheck = document.getElementById('course_check');
            const trainerCheck = document.getElementById('trainer_check');
            const submitBtn = document.getElementById('submitBtn');

            function checkSubmit() {
                if (courseInput.value && trainerInput.value) {
                    submitBtn.disabled = false;
                }
            }

            function updateStars(stars, value, input, checkEl) {
                input.value = value;
                checkEl.classList.add('active');
                
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
                    updateStars(courseStars, star.getAttribute('data-value'), courseInput, courseCheck);
                });
            });

            trainerStars.forEach(star => {
                star.addEventListener('click', () => {
                    updateStars(trainerStars, star.getAttribute('data-value'), trainerInput, trainerCheck);
                });
            });
        });
    </script>
    
    @include('partials.footer-after-login')
</body>
</html>