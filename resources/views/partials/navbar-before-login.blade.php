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
                <li class="nav-item mx-2">
                    <a class="nav-link nav-section-link {{ request()->routeIs('landing-page') && !request()->query('section') ? 'active' : '' }}" href="{{ route('landing-page') }}">Beranda</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link nav-section-link" href="{{ request()->routeIs('landing-page') ? '#layanan' : route('landing-page').'#layanan' }}">Layanan</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link nav-section-link" href="{{ request()->routeIs('landing-page') ? '#tentang' : route('landing-page').'#tentang' }}">Tentang Kami</a>
                </li>
                <li class="nav-item mx-2">
                    <a class="nav-link nav-section-link" href="{{ request()->routeIs('landing-page') ? '#fitur' : route('landing-page').'#fitur' }}">Fitur</a>
                </li>
                <li class="nav-item mx-2">
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
        transition: all 0.3s ease;
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
                let scrollPos = window.scrollY + 100;

                // Jika di paling atas, aktifkan Beranda
                if (window.scrollY < 200) {
                    navLinks.forEach(link => link.classList.remove('active'));
                    navLinks[0].classList.add('active');
                    return;
                }

                sections.forEach(section => {
                    if (scrollPos >= section.offsetTop && scrollPos < section.offsetTop + section.offsetHeight) {
                        const id = section.getAttribute('id');
                        navLinks.forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === '#' + id || link.getAttribute('href').includes('#' + id)) {
                                link.classList.add('active');
                            }
                        });
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