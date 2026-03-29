<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat</title>
    <title>Dashboard Learner</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    @include("partials.navbar-after-login")
    <div class="container">
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
        <h1>Selamat Anda telah menyelesaikan semua Modul!</h1>
        <p class="desc">
            Kami sangat bangga atas dedikasi dan kerja keras Anda dalam menyelesaikan
            semua modul kursus. Ini adalah pencapaian yang luar biasa dan merupakan
            bukti komitmen Anda terhadap pembelajaran dan pengembangan diri.
            Teruslah berkarya dan raih sukses!
        </p>
        <div class="certificate">
            <img src="" alt="Sertifikat">
        </div>
        <div class="btn-area">
            <button onclick="window.print()">Cetak Sertifikat</button>
        </div>

    </div>
</body>

</html>