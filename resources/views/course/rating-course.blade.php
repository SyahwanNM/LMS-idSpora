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
        <form action="{{ route('course.rating.store', $course->id) }}" method="POST" id="ratingForm">
            @csrf
            <input type="hidden" name="rating" id="ratingInput" required>
            
            <div class="box_dalam_penilaian">
                <div>
                    <h4>Berikan Penilaian Anda</h4>
                    <p>Sebelum cetak sertifikat, berikan penilaian terlebih dahulu</p>
                </div>
                <!-- Menampilkan Error Validasi -->
                @if($errors->any())
                    <div class="alert alert-danger" style="margin-top: 10px;">
                        <ul style="margin:0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="pertanyaan_penilaian">
                    <h4>Bagaimana kurikulum dan keseluruhan proses belajar di kelas ini?</h4>
                    <div style="display:flex; align-items:center;">
                        <div class="stars" id="course_stars">
                            <i class="star" data-value="1" style="cursor: pointer;">☆</i>
                            <i class="star" data-value="2" style="cursor: pointer;">☆</i>
                            <i class="star" data-value="3" style="cursor: pointer;">☆</i>
                            <i class="star" data-value="4" style="cursor: pointer;">☆</i>
                            <i class="star" data-value="5" style="cursor: pointer;">☆</i>
                        </div>
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
                <div class="tata_cara_feedback" style="margin-top: 20px;">
                    <h4>Bingung cara memberikan feedback?</h4>
                    <p style="font-size: 14px; color: #666;">
                        Tuliskan lah feedback secara spesifik. Jika Anda memberikan rating 1-4,
                        maka beritahu kami apa yang harus kami tingkatkan lagi pada kelas ini.
                        Jika Anda memberikan rating 5, Anda bisa ceritakan pengalaman
                        mengesankan selama belajar di kelas ini.
                    </p>
                </div>
            </div>

            <div class="tombol_next_sertifikat" style="margin-top: 30px;">
                <button type="submit" class="next_halaman_sertif" style="border:none; padding: 12px 30px; background: #2563eb; color: #fff; border-radius: 8px; font-weight: 600;">Kirim Penilaian</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('#course_stars .star');
            const ratingInput = document.getElementById('ratingInput');
            const form = document.getElementById('ratingForm');

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const ratingValue = this.getAttribute('data-value');
                    ratingInput.value = ratingValue;
                    
                    // Update star UI
                    stars.forEach(s => {
                        if (s.getAttribute('data-value') <= ratingValue) {
                            s.innerHTML = '★';
                            s.style.color = '#fbbf24'; // Yellow
                        } else {
                            s.innerHTML = '☆';
                            s.style.color = '#d1d5db'; // Gray
                        }
                    });
                });
            });

            form.addEventListener('submit', function(e) {
                if (!ratingInput.value) {
                    e.preventDefault();
                    alert('Silakan pilih rating (bintang) terlebih dahulu!');
                }
            });
        });
    </script>
</body>

</html>