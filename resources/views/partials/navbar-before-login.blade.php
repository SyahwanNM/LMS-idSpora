<nav class="navbar navbar-expand-lg navbar-gradient fixed-top">
    <div class="container-fluid d-flex align-items-center" style="padding: 0;">
        <a class="navbar-brand" href="#" style="margin-left: 30px;">
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
                    <a class="nav-link {{ request()->routeIs('landing-page') ? 'active' : '' }}" aria-current="page" href="{{ route('landing-page') }}">Home</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="#">Courses</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}" href="{{ route('events.index') }}">Events</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="#">About</a>
                </li>
            </ul>
            <div class="d-flex align-items-center ms-3" style="margin-right: 30px;">
                <a href="{{ route('login') }}" class="btn btn-primary me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-secondary">Sign Up</a>
            </div>
        </div>
    </div>
</nav>
<style>
/* Enhanced navbar hover + active indicator */
.navbar-gradient .nav-link {
    position: relative;
    color: #fff !important;
    transition: color .25s ease;
    padding-bottom: 6px;
}
.navbar-gradient .nav-link::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: 2px;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg,#ffe259,#ffa751);
    transition: width .35s cubic-bezier(.65,.05,.36,1), left .35s cubic-bezier(.65,.05,.36,1);
    border-radius: 2px;
    opacity: .9;
}
.navbar-gradient .nav-link:hover,
.navbar-gradient .nav-link:focus { color:#ffe8b3 !important; }
/* Only active link shows underline */
.navbar-gradient .nav-link.active { font-weight:600; }
.navbar-gradient .nav-link.active::after { width:70%; left:15%; }
@media (hover: none) {
    .navbar-gradient .nav-link::after { display:none; }
}
</style>