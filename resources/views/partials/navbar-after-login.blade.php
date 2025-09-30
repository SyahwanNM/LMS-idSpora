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
                    <a class="nav-link active" aria-current="page" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="#">Courses</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="{{ route('events.index') }}">Events</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link" href="#">About</a>
                </li>
            </ul>
            <form class="d-flex align-items-center h-100 me-2" style="margin: 0;" role="search">
                <div class="position-relative w-100">
                    <input class="form-control h-100 ps-4 pe-5" type="search" placeholder="Search" aria-label="Search"
                        style="border-radius: 2rem; background: none; border: 1px solid #fff; color: #fff; ::placeholder { color: #fff; opacity: 1; }">
                    <span class="position-absolute top-50 end-0 translate-middle-y pe-3"
                        style="pointer-events: none; opacity: 50%;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#fff" class="bi bi-search"
                            viewBox="0 0 16 16">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                        </svg>
                    </span>
                </div>
            </form>
            <div class="d-flex align-items-center ms-3" style="margin-right: 30px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell me-3"
                    viewBox="0 0 16 16">
                    <path
                        d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                </svg>
                
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" 
                            data-bs-toggle="dropdown" aria-expanded="false" style="background: none; border: none; color: white;"
                            onclick="toggleUserDropdown()">
                        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=facearea&w=64&h=64&facepad=2"
                            alt="Profile" class="rounded-circle me-2"
                            style="width:40px; height:40px; object-fit:cover; border:2px solid #fff;">
                        <span class="text-white">{{ Auth::user()->name }}</span>
                        <svg class="ms-2" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" id="dropdownArrow">
                            <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" id="userDropdownMenu" aria-labelledby="userDropdown" style="display: none;">
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                                                <li>
                                                        <button type="button" class="dropdown-item" onclick="openLogoutModal()">Logout</button>
                                                </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

@include('partials.flash')

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning-subtle">
                <h5 class="modal-title" id="logoutConfirmLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Anda yakin ingin keluar?</p>
                <small class="text-muted">Sesi Anda akan diakhiri dan perlu login kembali.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <form id="logoutRealForm" action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="button" id="confirmLogoutBtn" class="btn btn-danger">Ya, Logout</button>
        </form>
            </div>
        </div>
    </div>
</div>

<script>
// User Dropdown Functionality
function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdownMenu');
    const arrow = document.getElementById('dropdownArrow');
    
    if (dropdown && arrow) {
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
            arrow.style.transform = 'rotate(180deg)';
        } else {
            dropdown.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        }
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('userDropdownMenu');
    const button = document.getElementById('userDropdown');
    const arrow = document.getElementById('dropdownArrow');
    
    if (dropdown && button && !button.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
        if (arrow) arrow.style.transform = 'rotate(0deg)';
    }
});

// Initialize dropdown when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const button = document.getElementById('userDropdown');
    if (button) {
        // Remove Bootstrap data attributes to prevent conflicts
        button.removeAttribute('data-bs-toggle');
        button.removeAttribute('aria-expanded');
    }
});

// Logout modal logic
let logoutModalInstance;
function openLogoutModal(){
    const modalEl = document.getElementById('logoutConfirmModal');
    if (!logoutModalInstance) {
        logoutModalInstance = new bootstrap.Modal(modalEl);
    }
    logoutModalInstance.show();
}

// Pre-logout toast + submit
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('confirmLogoutBtn');
    const form = document.getElementById('logoutRealForm');
    if(btn && form){
        btn.addEventListener('click', () => {
            // Prevent double click
            if(btn.disabled) return;
            btn.disabled = true;
            showLogoutSuccessState();
            showInstantLogoutToast(); // tetap tampilkan toast kecil di pojok
            // delay supaya animasi check terlihat
            setTimeout(()=> form.submit(), 900);
        });
    }
});

function showInstantLogoutToast(){
    let container = document.querySelector('.flash-toast-container');
    if(!container){
        container = document.createElement('div');
        container.className = 'flash-toast-container';
        container.setAttribute('aria-live','polite');
        container.setAttribute('aria-atomic','true');
        document.body.appendChild(container);
    }
    const toast = document.createElement('div');
    toast.className = 'flash-toast flash-success';
    toast.setAttribute('role','status');
    toast.innerHTML = `
        <div class="flash-icon">
            <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill="currentColor" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.97 11.03 13 5l-1.06-1.06-4.97 4.95L4.53 7.47 3.47 8.53z"/>
            </svg>
        </div>
        <div class="flash-body">
            <div class="flash-title">Berhasil</div>
            <div class="flash-message">Anda berhasil logout</div>
        </div>
        <button class="flash-close" type="button" aria-label="Tutup">&times;</button>
        <div class="flash-progress" style="animation-duration: 0.6s"></div>`;
    container.appendChild(toast);
    requestAnimationFrame(()=> toast.classList.add('show'));
    const closeBtn = toast.querySelector('.flash-close');
    if(closeBtn){
        closeBtn.addEventListener('click', ()=> {toast.classList.add('closing'); setTimeout(()=> toast.remove(), 400);});
    }
    setTimeout(()=> {toast.classList.add('closing'); setTimeout(()=> toast.remove(), 400);}, 550);
}

function showLogoutSuccessState(){
    const modalEl = document.getElementById('logoutConfirmModal');
    if(!modalEl) return;
    const body = modalEl.querySelector('.modal-body');
    const footer = modalEl.querySelector('.modal-footer');
    if(footer) footer.style.display='none';
    if(body){
        body.classList.add('d-flex','flex-column','align-items-center','justify-content-center');
        body.innerHTML = `
            <div class="logout-success-feedback text-center">
                <svg class="check-anim" viewBox="0 0 72 72" width="88" height="88" aria-hidden="true">
                    <circle class="circle" cx="36" cy="36" r="32" fill="none" stroke="#16a34a" stroke-width="4" />
                    <path class="check" fill="none" stroke="#16a34a" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M22 36.5 32 46 50 27" />
                </svg>
                <p class="fw-semibold mb-1 mt-3">Berhasil logout</p>
                <small class="text-muted">Mengalihkan...</small>
            </div>`;
    }
}
</script>

<style>
/* Dropdown arrow rotation */
#dropdownArrow {
    transition: transform 0.2s ease-in-out;
}

/* Dropdown menu positioning */
#userDropdownMenu {
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 1000;
    min-width: 200px;
    background-color: white;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
}

/* Dropdown item hover effects */
#userDropdownMenu .dropdown-item:hover {
    background-color: #f8f9fa;
}

/* Logout success animation */
.logout-success-feedback .check-anim { display:block; }
.logout-success-feedback .circle { stroke-dasharray: 201; stroke-dashoffset:201; animation: draw-circle .55s ease-out forwards; }
.logout-success-feedback .check { stroke-dasharray: 40; stroke-dashoffset:40; animation: draw-check .35s ease-out .45s forwards; }
@keyframes draw-circle { to { stroke-dashoffset:0; } }
@keyframes draw-check { to { stroke-dashoffset:0; } }
</style>