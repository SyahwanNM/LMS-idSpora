
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>idSpora - Home</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 15px 0;
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
            font-size: 24px;
        }

        .btn-auth {
            background-color: #f4a442;
            border: none;
            color: white;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 8px;
            margin-left: 10px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-auth:hover {
            background-color: #e68a00;
            color: white;
        }

        .btn-outline {
            background-color: transparent;
            border: 2px solid #f4a442;
            color: #f4a442;
        }

        .btn-outline:hover {
            background-color: #f4a442;
            color: white;
        }

        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
        }

        body {
            background: radial-gradient(circle, #51376C 0%, #2E2050 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: "Poppins", sans-serif;
        }
    </style>
</head>

<body>
    <!-- Navigation Header -->
    <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="{{ route('welcome') }}">
                    <img src="{{ asset('aset/logo.png') }}" alt="idSpora" height="40" class="me-2">
                    idSpora
                </a>
                
                <div class="d-flex align-items-center">
                    @auth
                        <span class="text-white me-3">
                            Halo, {{ Auth::user()->name }}!
                            @if(Auth::user()->role === 'admin')
                                <span class="badge bg-warning text-dark ms-2">ADMIN</span>
                            @endif
                        </span>
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="btn-auth">Admin Panel</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="btn-auth">Dashboard</a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-auth btn-outline">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-auth btn-outline">Masuk</a>
                        <a href="{{ route('register') }}" class="btn-auth">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>