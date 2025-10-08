@extends('course.quiz.layout')

@section('content')
    <h1>Kuis 1 : Android Studio</h1>

    <div class="question-card">
        <p class="question-text">1. Apa yang dimaksud dengan android studio?</p>
        <div class="answer-options">
            <div class="form-check">
                <input class="form-check-input" type="radio" name="question1" id="q1_opt1">
                <label class="form-check-label" for="q1_opt1">a. lorem ipsum</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="question1" id="q1_opt2" checked>
                <label class="form-check-label" for="q1_opt2">a. lorem ipsum</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="question1" id="q1_opt3">
                <label class="form-check-label" for="q1_opt3">a. lorem ipsum</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="question1" id="q1_opt4">
                <label class="form-check-label" for="q1_opt4">a. lorem ipsum</label>
            </div>
        </div>
    </div>

    <div class="question-card">
        <p class="question-text">2. Apa yang dimaksud dengan android studio?</p>
        <div class="answer-options">
            <div class="form-check"><input class="form-check-input" type="radio" name="question2" id="q2_opt1"><label
                    class="form-check-label" for="q2_opt1">a. lorem ipsum</label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="question2" id="q2_opt2"><label
                    class="form-check-label" for="q2_opt2">a. lorem ipsum</label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="question2" id="q2_opt3"><label
                    class="form-check-label" for="q2_opt3">a. lorem ipsum</label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="question2" id="q2_opt4"><label
                    class="form-check-label" for="q2_opt4">a. lorem ipsum</label></div>
        </div>
    </div>

    <div class="question-card">
        <p class="question-text">3. Apa yang dimaksud dengan android studio?</p>
        <div class="answer-options">
            <div class="form-check"><input class="form-check-input" type="radio" name="question3" id="q3_opt1"><label
                    class="form-check-label" for="q3_opt1">a. lorem ipsum</label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="question3" id="q3_opt2"><label
                    class="form-check-label" for="q3_opt2">a. lorem ipsum</label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="question3" id="q3_opt3"><label
                    class="form-check-label" for="q3_opt3">a. lorem ipsum</label></div>
            <div class="form-check"><input class="form-check-input" type="radio" name="question3" id="q3_opt4"><label
                    class="form-check-label" for="q3_opt4">a. lorem ipsum</label></div>
        </div>
    </div>

    <div class="quiz-navigation">
        <ul class="pagination quiz-pagination">
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
        </ul>
        <a href="#" class="btn-next">
            Next <i class="bi bi-arrow-right-short"></i>
        </a>
    </div>

@endsection