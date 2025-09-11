@include('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>idSpora - Home</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .custom-carousel {
            width: 1350px;
            height: 400px;
            max-width: 100vw;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 24px;
            padding: 18px 45px 0 45px;
        }

        .custom-carousel .carousel-item img {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div id="carouselExampleInterval" class="carousel slide custom-carousel" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active" data-bs-interval="10000">
                <img src="https://img.freepik.com/vektor-premium/live-concert-horizontal-banner-template_23-2150997973.jpg"
                    class="d-block" alt="...">
            </div>
            <div class="carousel-item" data-bs-interval="2000">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRt2J3i17I7bpToDbbrbL6ULzX8IPnF7JJXiQ&s" class="d-block" alt="...">
            </div>
            <div class="carousel-item">
                <img src="https://img.freepik.com/free-psd/horizontal-banner-template-jazz-festival-club_23-2148979704.jpg" class="d-block" alt="...">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleInterval"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleInterval"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

</body>

</html>