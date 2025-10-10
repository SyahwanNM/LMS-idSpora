@extends('course.quiz.layout')

@section('content')
    <style>
        .result-container {
            display: flex;
            gap: 32px;
            flex-wrap: wrap;
        }

        .result-left {
            background: var(--bg-light);
            border-radius: 16px;
            box-shadow: 0 4px 16px var(--shadow-light);
            border: 1px solid var(--border-light);
            padding: 32px 24px;
            min-width: 260px;
            max-width: 320px;
            flex: 1 1 260px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .result-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 18px;
        }

        .result-score {
            font-size: 3rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 8px;
        }

        .result-info {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-bottom: 18px;
            text-align: center;
        }

        .result-detail {
            font-size: 1.2rem;
            margin-bottom: 12px;
        }

        .result-right {
            flex: 2 1 400px;
            min-width: 320px;
        }

        .question-result-card {
            background: var(--bg-light);
            border-radius: 12px;
            box-shadow: 0 2px 8px var(--shadow-light);
            border: 1px solid var(--border-light);
            padding: 18px 24px;
            margin-bottom: 18px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
        }

        .question-result-content {
            flex: 1;
        }

        .question-result-status {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            color: white;
            background: var(--danger);
            margin-left: 18px;
        }

        .question-result-status.correct {
            background: var(--success);
        }

        .question-result-status.incorrect {
            background: var(--danger);
        }

        .answer-options .form-check-label {
            color: var(--text-secondary);
        }

        .btn-back {
            background: var(--secondary);
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 1.1rem;
            padding: 12px 32px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            box-shadow: 0 2px 8px var(--shadow-light);
            margin-top: 32px;
            transition: background 0.2s;
        }

        .btn-back:hover {
            background: var(--secondary-dark);
        }

        @media (max-width: 900px) {
            .result-container {
                flex-direction: column;
            }

            .result-left,
            .result-right {
                max-width: 100%;
            }
        }
    </style>

    <div class="result-title">Quiz Result</div>
    <div class="result-container">
        <div class="result-left">
            <div style="font-size: 1rem; color: var(--text-secondary); margin-bottom: 10px;">
                Tanggal Ujian : 08 Oct 2025 pukul 08:35:20
            </div>
            <div class="result-detail">Total Soal</div>
            <div class="result-score">5</div>
            <div class="result-detail">Score</div>
            <div class="result-score">90</div>
            <div class="result-info">
                Score anda belum memenuhi batas minimum yang ditentukan pada ujian ini: 75.<br>
                Mohon untuk mempelajari kembali modul-modul terkait: Android Studio.
            </div>
        </div>
        <div class="result-right">
            <!-- Example question results, replace with dynamic data -->
            <div class="question-result-card">
                <div class="question-result-content">
                    <div class="question-text">1. Apa yang dimaksud dengan android studio?</div>
                    <div class="answer-options">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question1" id="q1_opt1" disabled>
                            <label class="form-check-label" for="q1_opt1">a. lorem ipsum</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question1" id="q1_opt2" checked disabled>
                            <label class="form-check-label" for="q1_opt2">a. lorem ipsum</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question1" id="q1_opt3" disabled>
                            <label class="form-check-label" for="q1_opt3">a. lorem ipsum</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="question1" id="q1_opt4" disabled>
                            <label class="form-check-label" for="q1_opt4">a. lorem ipsum</label>
                        </div>
                    </div>
                </div>
                <div class="question-result-status incorrect"><i class="bi bi-x-lg"></i></div>
            </div>
            <div class="question-result-card">
                <div class="question-result-content">
                    <div class="question-text">2. Apa yang dimaksud dengan android studio?</div>
                    <div class="answer-options">
                        <div class="form-check"><input class="form-check-input" type="radio" name="question2" id="q2_opt1"
                                disabled><label class="form-check-label" for="q2_opt1">a. lorem ipsum</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="question2" id="q2_opt2"
                                checked disabled><label class="form-check-label" for="q2_opt2">a. lorem ipsum</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="question2" id="q2_opt3"
                                disabled><label class="form-check-label" for="q2_opt3">a. lorem ipsum</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="question2" id="q2_opt4"
                                disabled><label class="form-check-label" for="q2_opt4">a. lorem ipsum</label></div>
                    </div>
                </div>
                <div class="question-result-status correct"><i class="bi bi-check-lg"></i></div>
            </div>
            <!-- Repeat for other questions, use .correct or .incorrect -->
            <div class="question-result-card">
                <div class="question-result-content">
                    <div class="question-text">3. Apa yang dimaksud dengan android studio?</div>
                    <div class="answer-options">
                        <div class="form-check"><input class="form-check-input" type="radio" name="question3" id="q3_opt1"
                                disabled><label class="form-check-label" for="q3_opt1">a. lorem ipsum</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="question3" id="q3_opt2"
                                checked disabled><label class="form-check-label" for="q3_opt2">a. lorem ipsum</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="question3" id="q3_opt3"
                                disabled><label class="form-check-label" for="q3_opt3">a. lorem ipsum</label></div>
                        <div class="form-check"><input class="form-check-input" type="radio" name="question3" id="q3_opt4"
                                disabled><label class="form-check-label" for="q3_opt4">a. lorem ipsum</label></div>
                    </div>
                </div>
                <div class="question-result-status correct"><i class="bi bi-check-lg"></i></div>
            </div>
            <!-- Add more question cards as needed -->
            <a href="#" class="btn-back">Back To Course <i class="bi bi-arrow-right-short"></i></a>
        </div>
    </div>
@endsection