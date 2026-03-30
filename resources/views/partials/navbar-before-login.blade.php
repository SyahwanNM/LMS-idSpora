<nav class="navbar navbar-expand-lg navbar-public fixed-top">
    <div class="container-fluid d-flex align-items-center" style="padding: 0 15px;">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('landing-page') }}">
            <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="Logo idSpora" class="img-fluid"
                style="max-width:70px; height:auto;">
        </a>

        <!-- Menu Toggle (Mobile Only, next to logo) -->
        <button class="navbar-toggler border-0 shadow-none d-flex align-items-center gap-1 p-2 me-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" 
            style="color: rgba(255,255,255,0.8); font-size: 0.9rem; font-weight: 600;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            <span>Menu</span>
        </button>

        <!-- Main Links (Middle on Desktop) -->
        <div class="collapse navbar-collapse justify-content-center order-3 order-lg-2" id="navbarSupportedContent">
            <ul class="navbar-nav mb-lg-0 d-flex align-items-center">
                <li class="nav-item mx-1">
                    <a class="nav-link nav-section-link {{ request()->routeIs('landing-page') && !request()->query('section') ? 'active' : '' }}" href="{{ route('landing-page') }}">Beranda</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link nav-section-link" href="{{ request()->routeIs('landing-page') ? '#tentang' : route('landing-page').'#tentang' }}">Tentang</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link nav-section-link" href="{{ request()->routeIs('landing-page') ? '#layanan' : route('landing-page').'#layanan' }}">Layanan</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link nav-section-link" href="{{ request()->routeIs('landing-page') ? '#fitur' : route('landing-page').'#fitur' }}">Fitur</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link nav-section-link" href="{{ request()->routeIs('landing-page') ? '#event-section' : route('landing-page').'#event-section' }}">Event</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link nav-section-link" href="{{ request()->routeIs('landing-page') ? '#kursus' : route('landing-page').'#kursus' }}">Kursus</a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link {{ request()->routeIs('public.support') ? 'active' : '' }}" href="{{ route('public.support') }}">Kendala</a>
                </li>
            </ul>
        </div>

        <!-- Auth Buttons (Always Right) -->
        <div class="d-flex align-items-center order-2 order-lg-3 ms-auto ms-lg-0">
            <a href="{{ route('login') }}" class="btn btn-outline-new me-2 px-2 px-md-4 shadow-none border-2" style="font-size: 0.8rem; white-space: nowrap;">Masuk</a>
            <a href="{{ route('register') }}" class="btn btn-primary-new px-2 px-md-4 shadow-none" style="font-size: 0.8rem; white-space: nowrap;">Daftar</a>
        </div>
    </div>
</nav>

<style>
    /* Hide toggler on desktop (Standard Bootstrap behavior but explicit for our custom label) */
    @media (min-width: 992px) {
        .navbar-toggler {
            display: none !important;
        }
    }

    .navbar-public {
        background: radial-gradient(circle at 10% 10%, #51376c 0%, #2e2050 100%) !important;
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    /* Mobile Menu Styling */
    @media (max-width: 991.98px) {
        .navbar-collapse {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #2e2050;
            padding: 20px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .navbar-nav {
            flex-direction: column;
            align-items: flex-start !important;
            width: 100%;
        }
        .navbar-nav .nav-item {
            width: 100%;
            margin: 5px 0 !important;
        }
        .navbar-nav .nav-link {
            width: 100%;
            padding: 12px 20px !important;
        }
    }

    .navbar-public .nav-link {
        color: rgba(255,255,255,0.7) !important;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem !important;
        border-radius: 8px;
    }

    .navbar-public .nav-link:hover {
        color: #fff !important;
        background: rgba(255, 255, 255, 0.05);
    }

    .navbar-public .nav-link.active {
        color: #f4a442 !important;
        font-weight: 700;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hanya jalankan ScrollSpy jika berada di landing page
        const isLandingPage = {{ request()->routeIs('landing-page') ? 'true' : 'false' }};
        
        if (isLandingPage) {
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-section-link');

            function changeActiveLink() {
                const offset = 150; // Offset for better detection
                let current = "";

                // Detection logic
                if (window.scrollY < 200) {
                    current = "beranda"; 
                } else {
                    sections.forEach(section => {
                        const sectionTop = section.offsetTop;
                        const sectionHeight = section.clientHeight;
                        if (window.pageYOffset >= sectionTop - offset) {
                            current = section.getAttribute('id');
                        }
                    });
                }

                // Update links
                navLinks.forEach(link => {
                    link.classList.remove('active');
                    const href = link.getAttribute('href');
                    
                    if (current === "beranda" && !href.includes('#')) {
                        link.classList.add('active');
                    } else if (href.includes('#' + current)) {
                        link.classList.add('active');
                    }
                });
            }

            window.addEventListener('scroll', changeActiveLink);

            // Smooth scroll handling
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href.startsWith('#') || (href.includes('#') && href.includes(window.location.pathname))) {
                        const targetId = href.split('#')[1];
                        const targetElement = document.getElementById(targetId);
                        if (targetElement) {
                            e.preventDefault();
                            window.scrollTo({
                                top: targetElement.offsetTop - 70,
                                behavior: 'smooth'
                            });
                            // Update URL without jump
                            history.pushState(null, null, '#' + targetId);
                        }
                    }
                });
            });
        }
    });
</script>