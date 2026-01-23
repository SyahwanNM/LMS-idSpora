<div class="footer-section">
    <div class="container-fluid px-4 px-lg-5">
        <div class="row justify-content-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    <img src="{{ asset('logo-idspora.png') }}" alt="idSpora Logo" style="height: auto; width: 120px; object-fit: contain; display: block;" />
                </a>
                <p class="text-light mt-2">
                    Belajar tanpa batas, berkembang tanpa henti.
                </p>
                <div class="social-links">
                    <a href="https://www.tiktok.com/@idspora" class="text-light me-3"><i class="fab fa-tiktok"></i></a>
                    <a href="https://www.instagram.com/idspora.official/" class="text-light me-3"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/idspora/" class="text-light"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="text-white mb-3">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="{{ route('dashboard') }}" class="text-light">Beranda</a></li>
                    @if(Route::has('courses.index'))
                    <li><a href="{{ route('courses.index') }}" class="text-light">Kursus</a></li>
                    @elseif(Route::has('course.index'))
                    <li><a href="{{ route('course.index') }}" class="text-light">Kursus</a></li>
                    @endif
                    @if(Route::has('events.index'))
                    <li><a href="{{ route('events.index') }}" class="text-light">Event</a></li>
                    @elseif(Route::has('event.index'))
                    <li><a href="{{ route('event.index') }}" class="text-light">Event</a></li>
                    @endif
                    <li><a href="{{ route('profile.index') }}" class="text-light">Profil</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="text-white mb-3">Layanan</h6>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-light">Live Webinars</a></li>
                    <li><a href="#" class="text-light">Training & Mini Workshops</a></li>
                    <li><a href="#" class="text-light">E-Learning</a></li>
                    <li><a href="#" class="text-light">Video Production</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <h6 class="text-white mb-3">Hubungi Kami</h6>
                <p class="text-light mb-2">
                    <i class="fas fa-envelope me-2"></i>info@idspora.com
                </p>
                <p class="text-light mb-2">
                    <i class="fas fa-phone me-2"></i>+62 898-926-0731
                </p>
                <p class="text-light mb-3">
                    <i class="fas fa-map-marker-alt me-2"></i>Bandung, Indonesia
                </p>
                <div class="social-links">
                    <a href="https://www.tiktok.com/@idspora" class="text-light me-3" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="https://www.instagram.com/idspora.official/" class="text-light me-3" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.linkedin.com/company/idspora/" class="text-light" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-3" style="border-color: #495057" />
        <div class="row justify-content-center">
            <div class="col-md-4 text-center">
                <p class="text-light mb-1" style="font-size: 0.8rem">
                    &copy; {{ date('Y') }} idSpora. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .footer-section {
        /* Warna background */
        background: linear-gradient(90deg, #252346 0%, #5b56ac 100%);
        color: #fff;
        padding: 50px 0 20px;
        margin-top: 60px;
        
       
        width: 100vw; 
        position: relative;
        left: 50%;
        right: 50%;
        margin-left: -50vw; 
        margin-right: -50vw; 
    }

    
    body {
        overflow-x: hidden; 
    }
    
    d
    .footer-section a:not(.btn) {
        text-decoration: none;
        color: inherit;
        opacity: 0.9;
        transition: opacity 0.3s;
    }
    .footer-section a:not(.btn):hover {
        opacity: 1;
        color: #ffe8b3;
    }
    @media (max-width: 768px) {
        .footer-section {
            padding: 30px 0 15px !important;
        }
    }
</style>