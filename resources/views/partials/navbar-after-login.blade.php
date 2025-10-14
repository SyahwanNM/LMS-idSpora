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
                <button id="notifBtn" type="button" class="btn p-0 text-white position-relative" aria-haspopup="true" aria-expanded="false" style="background:none;border:none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bell me-3"
                        viewBox="0 0 16 16">
                        <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-3.203-3.92zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                    </svg>
                    <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="font-size:10px;">0</span>
                </button>

                <div id="notifDropdown" class="notif-dropdown shadow-lg">
                    <div class="notif-head d-flex align-items-center justify-content-between">
                        <strong>Notifikasi</strong>
                        <div class="d-flex align-items-center">
                            <span id="notifHeaderText" class="small text-muted me-2">Tidak ada notifikasi baru</span>
                            <button class="btn btn-sm d-inline-flex align-items-center" id="markAllReadBtn">
                                <i class="bi bi-check2-all me-1"></i>
                                <span> Tandai terbaca</span>
                            </button>
                        </div>
                    </div>
                    <div id="notifList" class="notif-list">
                        <div class="text-muted small py-3 px-2">Memuat...</div>
                    </div>
                </div>
                
                <div class="dropdown">
                    <button class="btn dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" 
                            data-bs-toggle="dropdown" aria-expanded="false" style="background: none; border: none; color: white;"
                            onclick="toggleUserDropdown()">
                        <img src="{{ Auth::user()->avatar_url }}"
                            alt="Avatar" class="rounded-circle me-2"
                            style="width:40px; height:40px; object-fit:cover; border:2px solid #fff; background:#eee;">
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

<script>
// Notifications dropdown logic with animations
(function(){
    const btn = document.getElementById('notifBtn');
    const panel = document.getElementById('notifDropdown');
    const badge = document.getElementById('notifBadge');
    const list = document.getElementById('notifList');
    const markAllBtn = document.getElementById('markAllReadBtn');
    const headerText = document.getElementById('notifHeaderText');
    let prevUnread = 0;
    // track seen notification IDs across polls to avoid duplicate popups
    const seenKey = 'notifSeenIds';
    let seenIds = new Set();
    try{ seenIds = new Set(JSON.parse(sessionStorage.getItem(seenKey) || '[]')); }catch(_e){ seenIds = new Set(); }

    function persistSeen(){
        try {
            const arr = Array.from(seenIds).slice(-200); // cap
            sessionStorage.setItem(seenKey, JSON.stringify(arr));
        } catch(_e){}
    }

    function showInstantToast(opts, anchorEl){
        const { title = 'Notifikasi', message = '', type = 'success', url = null } = opts || {};
        const toast = document.createElement('div');
        toast.className = `flash-toast ${type === 'error' ? 'flash-error' : 'flash-success'}`;
        toast.setAttribute('role','status');
        toast.innerHTML = `
            <div class="flash-icon">
                <svg width="20" height="20" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.97 11.03 13 5l-1.06-1.06-4.97 4.95L4.53 7.47 3.47 8.53z"/>
                </svg>
            </div>
            <div class="flash-body">
                <div class="flash-title">${title}</div>
                <div class="flash-message">${message}</div>
            </div>
            <button class="flash-close" type="button" aria-label="Tutup">&times;</button>
            <div class="flash-progress" style="animation-duration: 3.6s"></div>`;
        if(url){
            toast.style.cursor = 'pointer';
            toast.addEventListener('click', (e)=>{
                // allow closing via X without navigation
                if(e.target && (e.target.closest('.flash-close'))) return;
                window.location.href = url;
            });
        }
        if(anchorEl){
            toast.style.position = 'fixed';
            toast.style.zIndex = '2141';
            toast.style.maxWidth = '360px';
            toast.style.visibility = 'hidden';
            document.body.appendChild(toast);
            // position near bell icon (below and slightly to the left)
            const r = anchorEl.getBoundingClientRect();
            const tw = toast.offsetWidth;
            const th = toast.offsetHeight;
            const pad = 10;
            const gap = 8; // distance between bell and toast
            let top = r.bottom + pad;
            // Place toast so its right edge is gap px to the left of bell's left edge
            let left = Math.min(Math.max(r.left + gap - tw, pad), window.innerWidth - tw - pad);
            // Prevent off-screen bottom
            if(top + th > window.innerHeight - pad){ top = Math.max(pad, r.top - th - pad); }
            toast.style.top = `${top}px`;
            toast.style.right = 'auto';
            toast.style.left = `${left}px`;
            toast.style.visibility = '';
            requestAnimationFrame(()=> toast.classList.add('show'));
        } else {
            // fallback to global container
            let container = document.querySelector('.flash-toast-container');
            if(!container){
                container = document.createElement('div');
                container.className = 'flash-toast-container';
                container.setAttribute('aria-live','polite');
                container.setAttribute('aria-atomic','true');
                document.body.appendChild(container);
            }
            container.appendChild(toast);
            requestAnimationFrame(()=> toast.classList.add('show'));
        }
        const closeBtn = toast.querySelector('.flash-close');
        if(closeBtn){
            closeBtn.addEventListener('click', (ev)=> { ev.stopPropagation(); toast.classList.add('closing'); setTimeout(()=> toast.remove(), 400); });
        }
        setTimeout(()=> { toast.classList.add('closing'); setTimeout(()=> toast.remove(), 400); }, 3400);
    }

    function applyStaggerAnimations(){
        const items = list.querySelectorAll('.notif-item');
        items.forEach((el, idx)=>{
            el.style.setProperty('--delay', `${idx*40}ms`);
            el.classList.add('anim-in');
        });
    }

    function load(){
        fetch('{{ route('notifications.index') }}', {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(r=>r.json()).then(({items,unread})=>{
                // badge
                if(unread > (prevUnread||0)){
                    badge.classList.add('badge-bump');
                    setTimeout(()=> badge.classList.remove('badge-bump'), 320);
                }
                prevUnread = unread;
                badge.classList.toggle('d-none', !unread);
                badge.textContent = unread;

                // header info text beside the button
                if(headerText){
                    if(unread > 0){
                        headerText.textContent = `${unread} belum dibaca`;
                        headerText.classList.remove('text-success');
                    } else if(items.length > 0){
                        headerText.textContent = 'Semua telah dibaca';
                        headerText.classList.add('text-success');
                    } else {
                        headerText.textContent = 'Tidak ada notifikasi';
                        headerText.classList.remove('text-success');
                    }
                }

                if(!items.length){ list.innerHTML = '<div class="text-muted small py-3 px-2">Tidak ada notifikasi</div>'; return; }
                list.innerHTML = items.map(n => `
                    <a href="${n.url ?? '#'}" class="notif-item ${n.read_at ? '' : 'unread'}">
                        <div class="notif-icon">
                            <svg width="18" height="18" viewBox="0 0 16 16" fill="${n.read_at ? '#0ea5e9' : '#f59e0b'}" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.97 11.03 13 5l-1.06-1.06-4.97 4.95L4.53 7.47 3.47 8.53z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="fw-semibold d-flex align-items-center gap-2">${n.title} ${n.read_at ? '' : '<span class="notif-dot"></span>'}</div>
                            ${n.message ? `<div class="small text-muted">${n.message}</div>` : ''}
                            <div class="notif-time">${n.time_ago ?? ''}</div>
                        </div>
                    </a>`).join('');
                // stagger in
                requestAnimationFrame(applyStaggerAnimations);
            }).catch(()=>{ list.innerHTML = '<div class="text-danger small py-3 px-2">Gagal memuat notifikasi</div>'; });
    }

    function openPanel(){
        if(!panel.classList.contains('open')){
            panel.classList.add('open');
            load();
        }
    }
    function closePanel(){ panel.classList.remove('open'); }
    function togglePanel(){ panel.classList.contains('open') ? closePanel() : openPanel(); }

    btn?.addEventListener('click', (e)=>{ e.stopPropagation(); togglePanel(); });
    document.addEventListener('click', (e)=>{ if(panel && !panel.contains(e.target) && !btn.contains(e.target)) closePanel(); });
        markAllBtn?.addEventListener('click', ()=>{
            if(markAllBtn.disabled) return;
            const original = markAllBtn.innerHTML;
            markAllBtn.disabled = true;
            markAllBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" aria-hidden="true"></span> Memproses...';
            fetch('{{ route('notifications.markAllRead') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'}})
                .then(()=>{
                    badge.classList.add('d-none');
                    // animate unread -> read
                    list.querySelectorAll('.notif-item.unread').forEach(el=>{
                        el.classList.remove('unread');
                        const dot = el.querySelector('.notif-dot');
                        if(dot) dot.remove();
                    });
                    if(headerText){ headerText.textContent = 'Semua telah dibaca'; headerText.classList.add('text-success'); }
                })
                .finally(()=>{
                    markAllBtn.disabled = false;
                    markAllBtn.innerHTML = original;
                });
        });

    // Background polling to auto-popup new notifications (e.g., payment/registration success)
    let pollingTimer = null;
    function pollAndPopup(){
        fetch('{{ route('notifications.index') }}', {headers:{'X-Requested-With':'XMLHttpRequest'}})
            .then(r=>r.json())
            .then(({items, unread}) => {
                // Update badge and header text even when panel closed
                badge.classList.toggle('d-none', !unread);
                badge.textContent = unread || 0;
                if(headerText){
                    if(unread > 0){ headerText.textContent = `${unread} belum dibaca`; headerText.classList.remove('text-success'); }
                    else if(items.length > 0){ headerText.textContent = 'Semua telah dibaca'; headerText.classList.add('text-success'); }
                    else { headerText.textContent = 'Tidak ada notifikasi'; headerText.classList.remove('text-success'); }
                }
                // Find new unread notifications not seen before
                const newbies = (items || []).filter(n => !n.read_at && n.id && !seenIds.has(n.id));
                // Only popup at most 2 per poll to avoid spam
                const badgeEl = document.getElementById('notifBadge');
                const badgeVisible = badgeEl && !badgeEl.classList.contains('d-none') && badgeEl.offsetWidth > 0 && badgeEl.offsetHeight > 0;
                const anchor = (badgeVisible ? badgeEl : (btn || document.getElementById('notifBtn') || document.querySelector('#notifBtn')));
                newbies.slice(0,2).forEach(n => {
                    seenIds.add(n.id);
                    showInstantToast({
                        type: 'success',
                        title: n.title || 'Notifikasi Baru',
                        message: n.message || 'Klik untuk melihat detail notifikasi ini.',
                        url: n.url || null,
                    }, anchor);
                });
                if(newbies.length){ persistSeen(); }
            })
            .catch(()=>{});
    }

    // Start polling after slight delay to allow page to render
    setTimeout(pollAndPopup, 1200);
    pollingTimer = setInterval(pollAndPopup, 20000); // every 20s
})();
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

/* Logout success animation */
.logout-success-feedback .check-anim { display:block; }
.logout-success-feedback .circle { stroke-dasharray: 201; stroke-dashoffset:201; animation: draw-circle .55s ease-out forwards; }
.logout-success-feedback .check { stroke-dasharray: 40; stroke-dashoffset:40; animation: draw-check .35s ease-out .45s forwards; }
@keyframes draw-circle { to { stroke-dashoffset:0; } }
@keyframes draw-check { to { stroke-dashoffset:0; } }
</style>
<style>
/* Notifications dropdown styling + animations */
.notif-dropdown{ position:absolute; right:-6px; top:120%; width:320px; background:#fff; border:1px solid rgba(0,0,0,.08); border-radius:10px; z-index:2140; box-shadow:0 14px 34px -18px rgba(2,6,23,.35), 0 6px 16px -8px rgba(2,6,23,.18); opacity:0; transform: translateY(8px); visibility:hidden; pointer-events:none; transition: opacity .18s ease, transform .18s ease, visibility .18s; }
.notif-dropdown.open{ opacity:1; transform: translateY(0); visibility:visible; pointer-events:auto; }
.notif-head{ padding:10px 12px; border-bottom:1px solid #f0f0f0; }
.notif-list{ max-height:360px; overflow:auto; }
.notif-item{ display:flex; gap:10px; padding:10px 12px; border-bottom:1px solid #f6f6f6; text-decoration:none; color:#111; border-left:3px solid transparent; opacity:0; transform: translateY(6px); transition: background-color .25s ease, border-color .25s ease; }
.notif-item.anim-in{ animation: notifSlideIn .22s ease var(--delay, 0ms) forwards; }
.notif-item:hover{ background:#f9fafb; }
.notif-item.unread{ background:#fffdf5; border-left-color:#f59e0b; }
.notif-icon{ width:34px; height:34px; border-radius:50%; background:#e9f5ff; display:flex; align-items:center; justify-content:center; }
.notif-item.unread .notif-icon{ background:#fff7e6; }
.notif-time{ color:#6b7280; font-size:12px; }
.notif-dot{ display:inline-block; width:8px; height:8px; border-radius:50%; background:#f59e0b; box-shadow:0 0 0 0 rgba(245,158,11,.6); animation: pulse 1.6s ease infinite; }
.badge-bump{ animation:bump .28s ease; }
@keyframes notifSlideIn{ from{ opacity:0; transform: translateY(6px);} to{ opacity:1; transform: translateY(0);} }
@keyframes pulse{ 0%{ box-shadow:0 0 0 0 rgba(245,158,11,.6);} 70%{ box-shadow:0 0 0 10px rgba(245,158,11,0);} 100%{ box-shadow:0 0 0 0 rgba(245,158,11,0);} }
@keyframes bump{ 0%{ transform: scale(1);} 40%{ transform: scale(1.15);} 100%{ transform: scale(1);} }

/* Mark all read button theme: navy bg, yellow text */
#markAllReadBtn{ background:#252346; color:#F4C430; border-color:#252346; }
#markAllReadBtn:hover{ filter:brightness(1.08); color:#F4C430; }
#markAllReadBtn:disabled{ opacity:.75; cursor:not-allowed; }
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
.navbar-gradient .nav-link.active::after { width:70%; left:15%; }
@media (hover: none) { .navbar-gradient .nav-link::after { display:none; } }
</style>