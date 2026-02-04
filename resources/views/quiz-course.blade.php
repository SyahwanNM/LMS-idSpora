<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuis</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include("partials.navbar-after-login")

    <svg class="open_sidebar_btn" xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
        class="bi bi-layout-sidebar-inset-reverse" viewBox="0 0 16 16">
        <path
            d="M2 2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z" />
        <path d="M13 4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1z" />
    </svg>
    <div class="box_luar_kuis">
        <div class="box_kuis_kiri">

            <svg class="close_sidebar_btn" xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor"
                class="bi bi-layout-sidebar-inset" viewBox="0 0 16 16">
                <path
                    d="M14 2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zM2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2z" />
                <path d="M3 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z" />
            </svg>

            <div class="accordion-box">
                <div class="accordion-item">
                    <button class="accordion-header">
                        Pengenalan Dasar Pemrograman
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Apa itu Pemrograman?</p>
                        <hr>
                        <p>Materi 2: Sejarah Pemrograman</p>
                        <hr>
                        <p>Materi 3: Bahasa Pemrograman Populer</p>
                    </div>
                </div>

                <div class="accordion-item">
                    <button class="accordion-header">
                        Introduction Android Studio
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Pengenalan</p>
                        <hr>
                        <p>Materi 2: Bagian-bagian UI</p>
                        <hr>
                        <p>Materi 3: Cara Membuat Project Baru</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Introduction Android Studio
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Pengenalan</p>
                        <hr>
                        <p>Materi 2: Bagian-bagian UI</p>
                        <hr>
                        <p>Materi 3: Cara Membuat Project Baru</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Introduction Android Studio
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Pengenalan</p>
                        <hr>
                        <p>Materi 2: Bagian-bagian UI</p>
                        <hr>
                        <p>Materi 3: Cara Membuat Project Baru</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Introduction Android Studio
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Pengenalan</p>
                        <hr>
                        <p>Materi 2: Bagian-bagian UI</p>
                        <hr>
                        <p>Materi 3: Cara Membuat Project Baru</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Introduction Android Studio
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Pengenalan</p>
                        <hr>
                        <p>Materi 2: Bagian-bagian UI</p>
                        <hr>
                        <p>Materi 3: Cara Membuat Project Baru</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Introduction Android Studio
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Pengenalan</p>
                        <hr>
                        <p>Materi 2: Bagian-bagian UI</p>
                        <hr>
                        <p>Materi 3: Cara Membuat Project Baru</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Introduction Android Studio
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Pengenalan</p>
                        <hr>
                        <p>Materi 2: Bagian-bagian UI</p>
                        <hr>
                        <p>Materi 3: Cara Membuat Project Baru</p>
                    </div>
                </div>
                <div class="accordion-item">
                    <button class="accordion-header">
                        Introduction Android Studio
                        <span class="arrow">▲</span>
                    </button>
                    <div class="accordion-content">
                        <p>Materi 1: Pengenalan</p>
                        <hr>
                        <p>Materi 2: Bagian-bagian UI</p>
                        <hr>
                        <p>Materi 3: Cara Membuat Project Baru</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="box_kuis_kanan">
            <p class="waktu_kuis">00:15:30</p>
            <h2>Question</h2>
            <div class="nomor_kuis">
                <button class="kuis_belum_diisi">1</button>
                <button>2</button>
                <button>3</button>
                <button>4</button>
                <button>5</button>
                <button>6</button>
                <button class="kuis_aktif">7</button>
                <button>8</button>
                <button>9</button>
                <button>10</button>
            </div>
            <div class="box_soal_kuis">
                <h1>Kuis 1: Android Studio</h1>
                <h2 class="pertanyaan_kuis">1. What is meant by android studio</h2>
                <div class="pilihan_jawaban_kuis">
                    <button>A.</button>
                    <p>Integrated Development Environment (IDE) resmi untuk membuat aplikasi Android, yang dibuat dan
                        didistribusikan oleh Google </p>
                </div>
                <div class="pilihan_jawaban_kuis">
                    <button>B.</button>
                    <p>Integrated Development Environment (IDE) resmi untuk membuat aplikasi Android, yang dibuat dan
                        didistribusikan oleh Google </p>
                </div>
                <div class="pilihan_jawaban_kuis">
                    <button>C.</button>
                    <p>Integrated Development Environment (IDE) resmi untuk membuat aplikasi Android, yang dibuat dan
                        didistribusikan oleh Google </p>
                </div>
                <div class="pilihan_jawaban_kuis">
                    <button>D.</button>
                    <p>Integrated Development Environment (IDE) resmi untuk membuat aplikasi Android, yang dibuat dan
                        didistribusikan oleh Google </p>
                </div>
            </div>
            <div class="tombol_kuis">
                <button class="previous_question">Previous Question</button>
                <button class="next_question">Next Question</button>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll(".accordion-header").forEach(header => {
            header.addEventListener("click", () => {
                header.parentElement.classList.toggle("active");
            });
        });

        const sidebar = document.querySelector(".box_kuis_kiri");
        const openBtn = document.querySelector(".open_sidebar_btn");
        const closeBtn = document.querySelector(".close_sidebar_btn");

        closeBtn.addEventListener("click", () => {
            sidebar.classList.add("closed");
            openBtn.style.display = "block";
        });

        openBtn.addEventListener("click", () => {
            sidebar.classList.remove("closed");
            openBtn.style.display = "none";
        });
    </script>

</body>

</html>
