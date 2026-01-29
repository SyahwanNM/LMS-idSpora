<nav class="navbar navbar-expand-lg navbar-gradient fixed-top">
    <div class="container-fluid d-flex align-items-center" style="padding: 0;">
        <a class="navbar-brand" href="{{ route('dashboard') }}" style="margin-left: 30px;">
            <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="Logo idSpora" class="img-fluid"
                style="max-width:80px; height:auto;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
            aria-controls="navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse align-items-center" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-lg-0 d-flex align-items-center ms-3">
                <li class="nav-item mx-3">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="page" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link {{ request()->routeIs('courses.index') ? 'active' : '' }}" href="{{ route('courses.index') }}">Courses</a>
                </li>
                <li class="nav-item mx-3">
                    <a class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}" href="{{ route('events.index') }}">Events</a>
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
            <div class="d-flex align-items-center ms-3 position-relative" style="margin-right: 30px;">
                <!-- Notification Dropdown -->
                <div class="dropdown me-3">
                    <button class="btn position-relative" type="button" id="notifBtn" 
                            style="background: none; border: none; color: white; padding: 0.5rem;"
                            onclick="toggleNotificationDropdown()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16" style="display: inline-block;">
                            <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-1.203-3.92L10 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                        </svg>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" 
                              id="notificationBadge" style="display: none; font-size: 0.65rem; padding: 0.25em 0.5em;">
                            0
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow notification-dropdown" id="notificationDropdown" 
                        aria-labelledby="notifBtn" style="display: none; min-width: 350px; max-width: 400px; padding: 0; border: none;">
                        <!-- Header -->
                        <div class="notification-header d-flex justify-content-between align-items-center px-3 py-2" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef;">
                            <span id="notificationStatus" class="text-muted" style="font-size: 0.875rem;">Memuat...</span>
                            <button class="btn btn-sm mark-read-btn" onclick="markAllAsRead(); return false;" style="background-color: #ffc107; color: #333; border: none; border-radius: 6px; padding: 0.25rem 0.75rem; font-size: 0.75rem; font-weight: 500;">
                                Tandai terbaca
                            </button>
                        </div>
                        <!-- Notification List -->
                        <div id="notificationList" style="max-height: 400px; overflow-y: auto; background-color: #f8f9fa;">
                            <div class="px-3 py-4 text-center text-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16" style="opacity: 0.5; margin-bottom: 0.5rem;">
                                    <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 8 9h6.46l-3.05-3.812A.5.5 0 0 0 11.02 5H4.98zm9.954 5H8.854l.147-.146a.5.5 0 0 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L8.854 12h5.08a.5.5 0 0 0 .496-.563l-1-8a.5.5 0 0 0-.496-.437H4.98a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 9h4.932l.5 4z"/>
                                </svg>
                                <p class="mt-2 mb-0 small">Tidak ada notifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn d-flex align-items-center" type="button" id="userDropdown" 
                            style="background: none; border: none; color: white; padding: 0.25rem 0.5rem;"
                            onclick="toggleUserDropdown()">
                        <img src="{{ Auth::user()->avatar_url }}"
                            alt="Avatar" class="rounded-circle me-2 avatar-navbar"
                            referrerpolicy="no-referrer"
                            onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6b7280&color=ffffff&format=png';"
                            style="width:40px !important; height:40px !important; object-fit:cover !important; border:2px solid #fff !important; background:#eee !important; border-radius:50% !important; aspect-ratio:1/1 !important;">
                        <span class="text-white d-none d-md-inline" style="cursor:pointer;" onclick="event.stopPropagation();toggleUserDropdown();">{{ Auth::user()->name }}</span>
                        <svg class="ms-2 d-none d-md-inline" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" id="dropdownArrow">
                            <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" id="userDropdownMenu" aria-labelledby="userDropdown" style="display: none;">
                        <li>
                            <div class="dropdown-item-text d-flex align-items-center justify-content-between px-3 py-2" style="cursor: default;">
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#ffc107" viewBox="0 0 16 16" class="me-2">
                                        <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5.5m.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935m10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935M3.504 1c.007.517.026 1.006.056 1.469.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10c.484 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667.03-.463.049-.952.056-1.469H3.504z"/>
                                    </svg>
                                    <span class="fw-semibold">Poin Saya</span>
                                </div>
                                <span class="badge bg-warning text-dark ms-2 d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" class="me-1">
                                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                    </svg>
                                    {{ Auth::user()->points ?? 0 }}
                                </span>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('profile.settings') }}"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                                                <li>
                            <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutConfirmModal">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                                                </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

@include('partials.flash')
@include('partials.profile-reminder-banner')

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-labelledby="logoutConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content card-logout-style">
            
            <div class="modal-header header-logout-warning">
                <h5 class="modal-title title-logout-text" id="logoutConfirmLabel">Konfirmasi Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body body-logout-content">
                <h4 class="logout-main-question">Anda yakin ingin keluar?</h4>
                <p class="logout-sub-desc">Sesi Anda akan diakhiri dan perlu login kembali.</p>
            </div>

            <div class="modal-footer footer-logout-action">
                <button type="button" class="btn btn-action-cancel" data-bs-dismiss="modal">Batal</button>
                
                <form id="logoutRealForm" action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" id="confirmLogoutBtn" class="btn btn-action-confirm">Ya, Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Logout Modal Functionality
function openLogoutModal() {
    var modal = document.getElementById('logoutConfirmModal');
    if (window.bootstrap && modal) {
        var m = new bootstrap.Modal(modal);
        m.show();
    } else if (modal) {
        // Fallback for Bootstrap 4
        $(modal).modal('show');
    }
}
// Notification Dropdown Functionality
function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    
    if (dropdown) {
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
            loadNotifications();
        } else {
            dropdown.style.display = 'none';
        }
    }
}

function loadNotifications() {
    const notificationList = document.getElementById('notificationList');
    const badge = document.getElementById('notificationBadge');
    const notificationStatus = document.getElementById('notificationStatus');
    
    if (!notificationList) return;
    
    // Show loading state
    notificationList.innerHTML = '<div class="px-3 py-3 text-center text-muted"><small>Memuat...</small></div>';
    if (notificationStatus) notificationStatus.textContent = 'Memuat...';
    
    fetch('{{ route("notifications.index") }}', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        // Update badge
        if (badge) {
            if (data.unread > 0) {
                badge.textContent = data.unread > 99 ? '99+' : data.unread;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        }
        
        // Update status text
        if (notificationStatus) {
            const unreadCount = data.unread || 0;
            if (unreadCount === 0) {
                notificationStatus.textContent = 'Semua telah dibaca';
            } else {
                notificationStatus.textContent = `${unreadCount} belum dibaca`;
            }
        }
        
        // Update notification list
        if (data.items && data.items.length > 0) {
            let html = '';
            data.items.forEach(item => {
                const isRead = item.read_at !== null;
                const url = item.url || '#';
                html += `
                    <a class="notification-item" href="${url}" data-id="${item.id}" style="display: flex; padding: 1rem; border-bottom: 1px solid #e9ecef; text-decoration: none; color: inherit; background-color: white; transition: background-color 0.2s;">
                        <div class="notification-icon" style="flex-shrink: 0; width: 36px; height: 36px; border-radius: 50%; background-color: #ffd54f; display: flex; align-items: center; justify-content: center; margin-right: 0.875rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="white" viewBox="0 0 16 16">
                                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                            </svg>
                        </div>
                        <div class="notification-content" style="flex-grow: 1; min-width: 0;">
                            <div class="notification-title" style="font-weight: 600; color: #333; margin-bottom: 0.25rem; font-size: 0.875rem;">${escapeHtml(item.title)}</div>
                            <div class="notification-message" style="color: #666; margin-bottom: 0.5rem; font-size: 0.875rem; line-height: 1.4;">${escapeHtml(item.message || '')}</div>
                            <div class="notification-time" style="color: #999; font-size: 0.75rem;">${item.time_ago || ''}</div>
                        </div>
                    </a>
                `;
            });
            notificationList.innerHTML = html;
        } else {
            notificationList.innerHTML = `
                <div class="px-3 py-4 text-center text-muted" style="background-color: #f8f9fa;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16" style="opacity: 0.5; margin-bottom: 0.5rem;">
                        <path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 8 9h6.46l-3.05-3.812A.5.5 0 0 0 11.02 5H4.98zm9.954 5H8.854l.147-.146a.5.5 0 0 0-.708-.708l-3 3a.5.5 0 0 0 0 .708l3 3a.5.5 0 0 0 .708-.708L8.854 12h5.08a.5.5 0 0 0 .496-.563l-1-8a.5.5 0 0 0-.496-.437H4.98a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 9h4.932l.5 4z"/>
                    </svg>
                    <p class="mt-2 mb-0 small">Tidak ada notifikasi</p>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading notifications:', error);
        notificationList.innerHTML = '<div class="px-3 py-3 text-center text-danger"><small>Gagal memuat notifikasi</small></div>';
        if (notificationStatus) notificationStatus.textContent = 'Error';
    });
}

function markAllAsRead() {
    fetch('{{ route("notifications.markAllRead") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            // Reload notifications
            loadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking notifications as read:', error);
    });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load notification count on page load
document.addEventListener('DOMContentLoaded', function() {
    const badge = document.getElementById('notificationBadge');
    if (badge) {
        fetch('{{ route("notifications.index") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.unread > 0) {
                badge.textContent = data.unread > 99 ? '99+' : data.unread;
                badge.style.display = 'inline-block';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error loading notification count:', error);
        });
    }
});

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

// Settings Submenu Toggle
function toggleSettingsSubmenu(event) {
    event.preventDefault();
    event.stopPropagation();
    const submenu = document.getElementById('settingsSubmenu');
    const arrow = document.getElementById('settingsArrow');
    
    if (submenu && arrow) {
        if (submenu.style.display === 'none' || submenu.style.display === '') {
            submenu.style.display = 'block';
            arrow.style.transform = 'rotate(180deg)';
        } else {
            submenu.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        }
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    const userDropdown = document.getElementById('userDropdownMenu');
    const userButton = document.getElementById('userDropdown');
    const userArrow = document.getElementById('dropdownArrow');
    const notifDropdown = document.getElementById('notificationDropdown');
    const notifButton = document.getElementById('notifBtn');
    
    // Close user dropdown
    if (userDropdown && userButton && !userButton.contains(e.target) && !userDropdown.contains(e.target)) {
        userDropdown.style.display = 'none';
        if (userArrow) userArrow.style.transform = 'rotate(0deg)';
    }
    
    // Close notification dropdown
    if (notifDropdown && notifButton && !notifButton.contains(e.target) && !notifDropdown.contains(e.target)) {
        notifDropdown.style.display = 'none';
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
    // If Bootstrap JS is available, show modal; otherwise, fallback to direct submit
    if (window.bootstrap && typeof bootstrap.Modal === 'function' && modalEl) {
        if (!logoutModalInstance) {
            try { logoutModalInstance = new bootstrap.Modal(modalEl); } catch (_e) { logoutModalInstance = null; }
        }
        if (logoutModalInstance) { logoutModalInstance.show(); return; }
    }
    // Fallback: submit immediately if modal cannot be shown
    const form = document.getElementById('logoutRealForm');
    if(form){ form.submit(); }
}

// Pre-logout toast + success animation, then submit with slight delay
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('confirmLogoutBtn');
    const form = document.getElementById('logoutRealForm');
    if(!btn || !form) return;

    function performAnimatedLogout(e){
        // Prevent immediate navigation to let animation play
        if(e) e.preventDefault();
        if(form.dataset.submitting === '1') return; // guard double-submit
        form.dataset.submitting = '1';
        btn.disabled = true;
        try { showLogoutSuccessState(); } catch(_e){}
        try { showInstantLogoutToast(); } catch(_e){}
        setTimeout(() => {
            try { form.submit(); } catch(_e) { form.removeAttribute('data-submitting'); btn.disabled = false; }
        }, 900);
    }

    // Intercept both click and submit to be safe
    btn.addEventListener('click', performAnimatedLogout);
    form.addEventListener('submit', performAnimatedLogout);
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

<script>
// Ensure Bootstrap collapse works properly for mobile menu
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('#navbarSupportedContent');
    
    if (navbarToggler && navbarCollapse) {
        // Listen for Bootstrap collapse events
        navbarCollapse.addEventListener('show.bs.collapse', function () {
            navbarToggler.setAttribute('aria-expanded', 'true');
        });
        
        navbarCollapse.addEventListener('hide.bs.collapse', function () {
            navbarToggler.setAttribute('aria-expanded', 'false');
        });
        
        // Close dropdown when clicking on nav links in mobile
        const navLinks = navbarCollapse.querySelectorAll('.nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
                });
        });
    }
});
</script>

<script>
// Auto-logout on long inactivity or when tab was closed for a long period
(function(){
    const IDLE_LIMIT_MS = 30 * 60 * 1000; // 30 menit (atur sesuai kebutuhan)
    const KEY_LAST_ACTIVE = 'lms:lastActiveAt';
    const KEY_LAST_HIDDEN = 'lms:lastHiddenAt';
    const LOGOUT_URL = '{{ route('logout') }}';
    const SIGNIN_URL = '{{ route('login') }}';
    const CSRF = '{{ csrf_token() }}';
    let logoutTriggered = false;

    function now(){ return Date.now(); }

    function markActive(){
        try{ localStorage.setItem(KEY_LAST_ACTIVE, String(now())); }catch(_e){}
    }

    function recordHidden(){
        try{ localStorage.setItem(KEY_LAST_HIDDEN, String(now())); }catch(_e){}
    }

    function getMsSince(ts){
        const t = parseInt(ts || '0', 10); return t ? (now() - t) : 0;
    }

    function createSessionModal(){
        let el = document.getElementById('sessionExpiredModal');
        if(el) return el;
        el = document.createElement('div');
        el.id = 'sessionExpiredModal';
        el.className = 'modal fade';
        el.tabIndex = -1;
        el.setAttribute('aria-hidden','true');
        el.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning-subtle">
                        <h5 class="modal-title">Sesi Anda Berakhir</h5>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">Anda telah keluar karena tidak ada aktivitas untuk waktu yang lama.</p>
                        <small class="text-muted">Anda akan diarahkan ke halaman login.</small>
                    </div>
                    <div class="modal-footer">
                        <a href="${SIGNIN_URL}" class="btn btn-primary">Ke Halaman Login</a>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(el);
        return el;
    }

    function showSessionEndedPopup(){
        const el = createSessionModal();
        try {
            const modal = new bootstrap.Modal(el, {backdrop:'static', keyboard:false});
            modal.show();
        } catch(_e) {
            // Fallback
            alert('Sesi Anda berakhir. Anda akan diarahkan ke halaman login.');
        }
    }

    function doLogoutAndNotify(){
        if(logoutTriggered) return; logoutTriggered = true;
        // Try to logout on server (ignore errors), then show popup and redirect
        fetch(LOGOUT_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'X-Requested-With':'XMLHttpRequest', 'Accept':'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' },
            credentials: 'same-origin'
        }).finally(()=>{
            showSessionEndedPopup();
            setTimeout(()=> { window.location.href = SIGNIN_URL; }, 1800);
        });
    }

    // Initial marks
    markActive();

    // Activity listeners
    ['mousemove','keydown','scroll','click','touchstart'].forEach(evt => {
        window.addEventListener(evt, markActive, { passive: true });
    });

    // Visibility tracking (tab close or background)
    document.addEventListener('visibilitychange', () => {
        if(document.hidden){
            recordHidden();
        } else {
            // When returning, if hidden duration exceeded, logout
            const hiddenAt = localStorage.getItem(KEY_LAST_HIDDEN);
            if(hiddenAt){
                const hiddenMs = getMsSince(hiddenAt);
                if(hiddenMs > IDLE_LIMIT_MS){
                    doLogoutAndNotify();
                }
            }
        }
    });

    // Also record on unload
    window.addEventListener('beforeunload', recordHidden);

    // Periodic idle check (in case tab stays open)
    setInterval(() => {
        if(logoutTriggered) return;
        const lastActive = localStorage.getItem(KEY_LAST_ACTIVE);
        const idleMs = getMsSince(lastActive);
        if(idleMs > IDLE_LIMIT_MS){
            doLogoutAndNotify();
        }
    }, 15000); // check every 15s
})();
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

/* Points display styling */
#userDropdownMenu .dropdown-item-text {
    background-color: #fffbf0;
    border-bottom: 1px solid #ffe8b3;
}

#userDropdownMenu .dropdown-item-text .badge {
    font-size: 0.875rem;
    padding: 0.35em 0.65em;
    font-weight: 600;
}

/* Settings Submenu Styles */
.dropdown-submenu {
    background-color: #f8f9fa;
    border-left: 3px solid #ffc107;
    margin-top: 0.25rem;
}

.dropdown-submenu .dropdown-item {
    padding-left: 1.5rem !important;
    font-size: 0.9rem;
    color: #495057;
}

.dropdown-submenu .dropdown-item:hover {
    background-color: #e9ecef;
    color: #212529;
}

/* Notification dropdown styles */
.notification-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    z-index: 1000;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.notification-header {
    border-radius: 8px 8px 0 0;
}

.notification-item {
    border-bottom: 1px solid #e9ecef;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background-color: #f0f0f0 !important;
}

.mark-read-btn:hover {
    background-color: #ffb300 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}

.mark-read-btn:active {
    transform: translateY(0);
}

#notificationList::-webkit-scrollbar {
    width: 6px;
}

#notificationList::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#notificationList::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

#notificationList::-webkit-scrollbar-thumb:hover {
    background: #555;
}

#notifBtn {
    position: relative;
    transition: color 0.2s ease;
}

#notifBtn:hover {
    color: #ffe8b3 !important;
}

#notificationBadge {
    font-size: 0.65rem;
    padding: 0.25em 0.5em;
    min-width: 18px;
    text-align: center;
}

/* Logout success animation */
.logout-success-feedback .check-anim { display:block; }
.logout-success-feedback .circle { stroke-dasharray: 201; stroke-dashoffset:201; animation: draw-circle .55s ease-out forwards; }
.logout-success-feedback .check { stroke-dasharray: 40; stroke-dashoffset:40; animation: draw-check .35s ease-out .45s forwards; }
@keyframes draw-circle { to { stroke-dashoffset:0; } }
@keyframes draw-check { to { stroke-dashoffset:0; } }
</style>
<style>
/* Enhanced navbar hover + active indicator (logged-in) */
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
.navbar-gradient .nav-link:focus { color: #ffe8b3 !important; }
/* Only active link shows underline */
.navbar-gradient .nav-link.active { font-weight:600; }

/* Navbar Responsive Styles */
@media (max-width: 991px) {
    .navbar {
        min-height: 70px;
    }
    
    .navbar-brand {
        margin-left: 15px !important;
    }
    
    .navbar-brand img {
        max-width: 60px !important;
    }
    
    .navbar .container-fluid {
        padding: 0.5rem 0 !important;
        flex-wrap: wrap;
    }
    
    .navbar-toggler {
        margin-right: 15px;
        border-color: rgba(255, 255, 255, 0.3);
        padding: 0.25rem 0.5rem;
    }
    
    .navbar-toggler:focus {
        box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.25);
    }
    
    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    }
    
    .navbar-collapse {
        width: 100%;
        margin-top: 1rem;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 0.5rem;
        margin-left: 0.5rem;
        margin-right: 0.5rem;
    }
    
    .navbar-nav {
        flex-direction: column !important;
        width: 100%;
        margin: 0 !important;
        padding: 0;
    }
    
    .navbar-nav .nav-item {
        margin: 0.25rem 0 !important;
        width: 100%;
    }
    
    .navbar-nav .nav-link {
        padding: 0.75rem 1rem !important;
        width: 100%;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
    }
    
    .navbar-nav .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    form.d-flex {
        width: 100%;
        margin: 0.75rem 0 !important;
        padding: 0;
        order: 2;
    }
    
    form.d-flex .position-relative {
        width: 100%;
    }
    
    .navbar .form-control {
        width: 100% !important;
        margin: 0 !important;
        font-size: 14px !important;
        padding: 0.5rem 1rem 0.5rem 2.5rem !important;
    }
    
    .d-flex.align-items-center.ms-3 {
        flex-direction: row !important;
        width: 100%;
        margin: 0.75rem 0 0 0 !important;
        padding: 0;
        justify-content: flex-end;
        order: 3;
    }
    
    #notifBtn {
        margin-right: 0.5rem !important;
        padding: 0.5rem !important;
    }
    
    #userDropdown {
        margin: 0 !important;
        padding: 0.25rem 0.5rem !important;
        width: auto !important;
        justify-content: flex-end;
    }
    
    #notificationDropdown {
        right: 0 !important;
        left: auto !important;
        margin-top: 0.5rem;
        max-width: 90vw;
    }
    
    #userDropdown img {
        width: 36px !important;
        height: 36px !important;
    }
    
    #userDropdownMenu {
        position: absolute !important;
        right: 0 !important;
        left: auto !important;
        margin-top: 0.5rem;
    }
}

@media (max-width: 576px) {
    .navbar-brand {
        margin-left: 10px !important;
    }
    
    .navbar-brand img {
        max-width: 50px !important;
    }
    
    .navbar-toggler {
        padding: 0.25rem 0.5rem;
        font-size: 0.9rem;
    }
    
    .navbar-collapse {
        margin-left: 0.25rem;
        margin-right: 0.25rem;
        padding: 0.75rem;
    }
    
    .navbar-nav .nav-link {
        padding: 0.625rem 0.75rem !important;
        font-size: 0.9rem;
    }
    
    form.d-flex {
        margin: 0.5rem 0 !important;
    }
    
    #userDropdown {
        padding: 0.2rem 0.4rem !important;
    }
    
    #userDropdown img {
        width: 32px !important;
        height: 32px !important;
    }
    
    #userDropdownMenu {
        min-width: 180px;
        font-size: 0.9rem;
    }
}
.navbar-gradient .nav-link.active::after { width:70%; left:15%; }
@media (hover: none) { .navbar-gradient .nav-link::after { display:none; } }
</style>