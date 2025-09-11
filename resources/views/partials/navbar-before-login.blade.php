<nav class="navbar navbar-expand-lg navbar-gradient fixed-top">
    <div class="container-fluid d-flex align-items-center" style="padding: 0;">
        <a class="navbar-brand" href="#" style="margin-left: 30px; mar">
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
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="#">Courses</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="#">Events</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="#">About</a>
                </li>
            </ul>
            <form class="d-flex align-items-center h-100 me-2" style="margin: 0;" role="search">
                <div class="position-relative w-100">
                    <input class="form-control h-100 ps-4 pe-5" type="search" placeholder="Search" aria-label="Search"
                        style="border-radius: 2rem; background: none; border: 1px solid #fff; color: #fff; ::placeholder { color: #fff; opacity: 1; }">
                    <span class="position-absolute top-50 end-0 translate-middle-y pe-3" style="pointer-events: none; opacity: 50%;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#fff" class="bi bi-search"
                            viewBox="0 0 16 16">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                        </svg>
                    </span>
                </div>
            </form>
            <div class="d-flex align-items-center ms-3" style="margin-right: 30px;">
                <a href="#" class="btn btn-primary me-2" sy>Login</a>
                <a href="#" class="btn btn-secondary">Sign Up</a>
            </div>
        </div>
    </div>
</nav>