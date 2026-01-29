<nav class="navbar navbar-expand-lg navbar-public fixed-top">
    <div class="container-fluid d-flex align-items-center" style="padding: 0;">
        <a class="navbar-brand" href="{{ route('landing-page') }}" style="margin-left: 30px;">
            <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="Logo idSpora" class="img-fluid"
                style="max-width:80px; height:auto;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse align-items-center" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-lg-0 d-flex align-items-center ms-3">
                <li class="nav-item mx-3">
                    <a class="nav-link {{ request()->routeIs('landing-page') ? 'active' : '' }}" aria-current="page" href="{{ route('landing-page') }}">Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Request::is('layanan') ? 'active' : '' }}" href="/#layanan">Layanan</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="{{ route('landing-page') }}#tentang">Tentang Kami</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="{{ route('landing-page') }}#fitur">Fitur</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link {{ request()->routeIs('public.support') ? 'active' : '' }}" href="{{ route('public.support') }}">Kendala</a>
                </li>
            </ul>
            <div class="d-flex align-items-center ms-3" style="margin-right: 30px;">
                <a href="{{ route('login') }}" class="btn btn-outline-new me-2 px-4 shadow-none border-2">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary-new px-4 shadow-none">Daftar</a>
            </div>
        </div>
    </div>
</nav>

<style>
    .navbar-public {
        background: radial-gradient(circle at 10% 10%, #51376c 0%, #2e2050 100%) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    }

    .navbar-public .nav-link {
        color: rgba(255,255,255,0.85) !important;
        font-weight: 600;
        letter-spacing: 0.05em;
        font-size: 0.85rem;
        text-transform: capitalize;
        position: relative;
        padding-bottom: 4px;
        transition: color .2s ease;
    }

    .navbar-public .nav-link:hover,
    .navbar-public .nav-link:focus {
        color: #fff !important;
    }

    .navbar-public .nav-link.active {
        color: #fbbf24 !important; /* Secondary Yellow */
    }


</style>