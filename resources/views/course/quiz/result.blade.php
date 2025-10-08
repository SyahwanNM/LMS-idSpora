@extends('course.quiz.layout')

@section('content')
    <div class="quiz-result">
        <h1>Hasil Kuis</h1>
        <div class="result-summary">
            <!-- Example result summary, replace with dynamic data as needed -->
            <p>Skor Anda: <strong>80/100</strong></p>
            <p>Jawaban benar: <strong>8</strong></p>
            <p>Jawaban salah: <strong>2</strong></p>
        </div>
        <a href="#" class="btn btn-secondary mt-4">Kembali ke Course</a>
    </div>
@endsection