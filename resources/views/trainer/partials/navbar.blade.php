<style>
/* Trainer Navbar */
.trainer-navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background: radial-gradient(circle at 10% 10%, #51376c 0%, #2e2050 100%);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    height: 60px;
    display: flex;
    align-items: center;
}

.trainer-navbar-container {
    width: 100%;
    max-width: 100%;
    padding: 0 6px 0 24px;
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 24px;
    align-items: center;
    height: 100%;
}

/* Left Section */
.navbar-left {
    display: flex;
    align-items: center;
    gap: 130px;
}

.navbar-logo {
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: #ffffff;
    font-weight: 700;
    font-size: 16px;
    white-space: nowrap;
}

.navbar-logo img {
    height: 20px;
    width: auto;
}

.navbar-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.7);
}

.navbar-breadcrumb a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: color 0.2s;
}

.navbar-breadcrumb a:hover {
    color: #ffffff;
}

.navbar-breadcrumb .separator {
    color: rgba(255, 255, 255, 0.4);
}

.navbar-breadcrumb .current {
    color: #ffffff;
    font-weight: 600;
}

/* Right Section */
.navbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
    justify-self: end;
    margin-left: auto;
}

.navbar-icon-btn {
    position: relative;
    background: transparent;
    border: none;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
}

.navbar-icon-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

.notification-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 600;
    padding: 2px 5px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

.trainer-notification-wrap {
    position: relative;
}

.trainer-notification-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    width: 320px;
    background: #ffffff;
    border: 1px solid var(--line-clr);
    border-radius: 10px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
    overflow: hidden;
    display: none;
    z-index: 1100;
}

.trainer-notification-dropdown.show {
    display: block;
}

.trainer-notification-header {
    padding: 10px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid var(--line-clr);
    font-size: 12px;
    font-weight: 700;
    color: var(--main-navy-clr);
}

.mark-read-btn {
    border: none;
    background: transparent;
    color: var(--main-navy-clr);
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

.trainer-notification-list {
    max-height: 320px;
    overflow-y: auto;
}

.trainer-notification-item {
    padding: 10px 12px;
    border-bottom: 1px solid #f1f5f9;
}

.trainer-notification-item.is-unread {
    background: #eef2ff;
}

.trainer-notification-item:last-child {
    border-bottom: none;
}

.trainer-notification-title {
    margin: 0;
    font-size: 12px;
    font-weight: 700;
    color: #0f172a;
}

.trainer-notification-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.trainer-notification-pill {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 999px;
    padding: 2px 8px;
    flex-shrink: 0;
}

.trainer-notification-pill.accepted {
    background: #d1fae5;
    color: #065f46;
}

.trainer-notification-pill.rejected {
    background: #fee2e2;
    color: #991b1b;
}

.trainer-notification-message {
    margin: 4px 0 0;
    font-size: 12px;
    color: #475569;
    line-height: 1.4;
}

.trainer-notification-deadline {
    margin: 6px 0 0;
    font-size: 11px;
    font-weight: 600;
    color: #475569;
}

.trainer-notification-deadline.is-overdue {
    color: #b91c1c;
}

.trainer-notification-time {
    display: inline-block;
    margin-top: 6px;
    font-size: 11px;
    color: #94a3b8;
}

.trainer-notification-bottom {
    margin-top: 6px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
}

.trainer-notification-link {
    font-size: 11px;
    font-weight: 700;
    color: var(--main-navy-clr);
    text-decoration: none;
}

.trainer-notification-link:hover {
    color: #4338ca;
}

.trainer-notification-actions {
    margin-top: 8px;
    display: flex;
    gap: 6px;
}

.trainer-notification-actions form {
    flex: 1;
}

.trainer-notification-action {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 6px 8px;
    cursor: pointer;
}

.trainer-notification-action:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.trainer-notification-action.accept {
    border-color: #a7f3d0;
    color: #065f46;
    background: #ecfdf5;
}

.trainer-notification-action.accept:hover {
    background: #d1fae5;
}

.trainer-notification-action.reject {
    border-color: #fecaca;
    color: #991b1b;
    background: #fef2f2;
}

.trainer-notification-action.reject:hover {
    background: #fee2e2;
}

.trainer-notification-empty {
    padding: 16px 12px;
    text-align: center;
    font-size: 12px;
    color: #94a3b8;
}

/* Profile Dropdown */
.navbar-profile-dropdown {
    position: relative;
}

.profile-link {
    text-decoration: none;
    color: inherit;
}

.profile-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    border: none;
    cursor: pointer;
    padding: 4px 3px;
    border-radius: 8px;
    transition: background 0.2s;
}

.profile-trigger:hover {
    background: rgba(255, 255, 255, 0.1);
}

.profile-info {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    text-align: right;
}

.profile-name {
    font-size: 13px;
    font-weight: 600;
    color: #ffffff;
    line-height: 1.1;
}

.profile-role {
    font-size: 11px;
    color: #fbbf24;
    font-weight: 600;
    letter-spacing: 0.025em;
}

.profile-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    overflow: hidden;
    background: #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    font-size: 16px;
}

/* Profile Menu Dropdown */
.profile-menu {
    position: absolute;
    top: calc(100% + 14px); /* Memberikan jarak lebih lega dari navbar */
    right: 0;
    background: #ffffff;
    border: 1px solid var(--line-clr);
    border-radius: 12px;
    box-shadow: 0 10px 35px -5px rgba(0, 0, 0, 0.1), 0 5px 15px -5px rgba(0, 0, 0, 0.05);
    min-width: 220px;
    padding: 8px; /* Padding di sekitar menu items */
    opacity: 0;
    visibility: hidden;
    transform: translateY(-15px);
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1100;
}

/* Segitiga kecil (caret) penunjuk arah navbar */
.profile-menu::before {
    content: '';
    position: absolute;
    top: -6px;
    right: 24px;
    width: 12px;
    height: 12px;
    background: #ffffff;
    border-left: 1px solid var(--line-clr);
    border-top: 1px solid var(--line-clr);
    transform: rotate(45deg);
    border-radius: 2px;
    z-index: -1;
}

.profile-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    color: var(--text-clr);
    font-weight: 500;
    text-decoration: none;
    font-size: 14px;
    transition: all 0.2s ease;
    border: none;
    background: transparent;
    width: 100%;
    text-align: left;
    cursor: pointer;
    border-radius: 8px;
}

.menu-item:hover {
    background: var(--base-clr);
    color: var(--main-navy-clr);
}

.menu-item svg {
    flex-shrink: 0;
    opacity: 0.8;
    color: inherit;
    transition: transform 0.2s ease;
}

.menu-item:hover svg {
    transform: scale(1.1);
}

.menu-item.logout {
    color: #ef4444; /* Merah untuk Sign out */
}

.menu-item.logout:hover {
    background: #fef2f2;
    color: #dc2626;
}

.menu-divider {
    margin: 8px 0;
    border: none;
    border-top: 1px solid var(--line-clr);
}

.menu-item-form {
    padding: 0;
    margin: 0;
}

/* Responsive */
@media (max-width: 1024px) {
    .navbar-left {
        gap: 60px; /* Kurangi jarak logo dan breadcrumb di tablet */
    }
}

@media (max-width: 768px) {
    .trainer-navbar-container {
        padding: 0 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .navbar-left {
        gap: 16px;
    }

    .navbar-breadcrumb {
        display: none;
    }

    .trainer-navbar {
        height: 64px;
    }

    .profile-info {
        display: none;
    }
}

</style>

<nav class="trainer-navbar">
    <div class="trainer-navbar-container">
        <!-- Left: Logo & Breadcrumb -->
        <div class="navbar-left">
            <a href="{{ route('trainer.dashboard') }}" class="navbar-logo">
                <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="idSpora">
            </a>

            <div class="navbar-breadcrumb">
                @if(isset($breadcrumbs))
                    @foreach($breadcrumbs as $breadcrumb)
                        @if(!$loop->last)
                            <a href="{{ $breadcrumb['url'] ?? '#' }}">{{ $breadcrumb['label'] }}</a>
                            <span class="separator">›</span>
                        @else
                            <span class="current">{{ $breadcrumb['label'] }}</span>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('trainer.dashboard') }}">Beranda</a>
                    <span class="separator">›</span>
                    <span class="current">{{ $pageTitle ?? 'Dasbor' }}</span>
                @endif
            </div>
        </div>

        <!-- Right: Notification & Profile -->
        <div class="navbar-right">
            <!-- Notification -->
            <div class="trainer-notification-wrap" id="trainerNotificationWrap">
                <button class="navbar-icon-btn" type="button" onclick="toggleNotifications()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16"
                        fill="currentColor">
                        <path
                            d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                    </svg>
                    <span class="notification-badge" id="trainerNotificationBadge"
                        style="{{ $trainerUnreadNotificationCount > 0 ? '' : 'display:none;' }}">{{ $trainerUnreadNotificationCount }}</span>
                </button>

                <div class="trainer-notification-dropdown" id="trainerNotificationDropdown">
                    <div class="trainer-notification-header">
                        <span>Notifikasi Trainer</span>
                        @if($trainerUnreadNotificationCount > 0)
                            <form action="{{ route('trainer.notifications.markAllRead') }}" method="POST">
                                @csrf
                                <button type="submit" class="mark-read-btn">Tandai dibaca</button>
                            </form>
                        @endif
                    </div>

                    <div class="trainer-notification-list">
                        @forelse($trainerNotifications as $notification)
                            @php
                                $notificationUrl = data_get($notification->data, 'url');
                                $notificationStatus = data_get($notification->data, 'invitation_status');
                                $isInvitation = in_array($notification->type, ['course_invitation', 'event_invitation'], true);
                                $isMaterialApproved = in_array($notification->type, ['course_material_approved', 'event_material_approved'], true);
                                $isMaterialRejected = in_array($notification->type, ['course_material_rejected', 'event_material_rejected'], true);
                                $notificationDueDate = null;
                                $notificationIsOverdue = false;
                                $notificationDueAt = data_get($notification->data, 'due_at');
                                if (!empty($notificationDueAt)) {
                                    try {
                                        $notificationDueDate = \Illuminate\Support\Carbon::parse((string) $notificationDueAt);
                                        $notificationIsOverdue = $notificationStatus !== 'accepted' && $notificationStatus !== 'rejected' && $notificationDueDate->isPast();
                                    } catch (\Throwable $e) {
                                        $notificationDueDate = null;
                                    }
                                }
                            @endphp
                            <div class="trainer-notification-item {{ is_null($notification->read_at) ? 'is-unread' : '' }}">
                                <div class="trainer-notification-top">
                                    <p class="trainer-notification-title">{{ $notification->title }}</p>
                                    @if($isInvitation && $notificationStatus === 'accepted')
                                        <span class="trainer-notification-pill accepted">Diterima</span>
                                    @elseif($isInvitation && $notificationStatus === 'rejected')
                                        <span class="trainer-notification-pill rejected">Ditolak</span>
                                    @elseif($isMaterialApproved)
                                        <span class="trainer-notification-pill accepted">Materi Diterima</span>
                                    @elseif($isMaterialRejected)
                                        <span class="trainer-notification-pill rejected">Perlu Revisi</span>
                                    @endif
                                </div>
                                @if(!empty($notification->message))
                                    <p class="trainer-notification-message">
                                        {{ \Illuminate\Support\Str::limit($notification->message, 90) }}
                                    </p>
                                @endif
                                @if($isInvitation && $notificationDueDate)
                                    <p class="trainer-notification-deadline {{ $notificationIsOverdue ? 'is-overdue' : '' }}">
                                        Tenggat: {{ $notificationDueDate->format('d M Y H:i') }}
                                        @if($notificationIsOverdue)
                                            (Terlambat)
                                        @endif
                                    </p>
                                @endif
                                <div class="trainer-notification-bottom">
                                    <span
                                        class="trainer-notification-time">{{ $notification->created_at?->diffForHumans() }}</span>
                                    @if(!empty($notificationUrl))
                                        <a href="{{ route('trainer.notifications.open', $notification->id) }}"
                                            class="trainer-notification-link">Buka</a>
                                    @endif
                                </div>
                                @if($isInvitation && ($notificationStatus === 'pending' || empty($notificationStatus)))
                                    <div class="trainer-notification-actions">
                                        <form method="POST" class="js-invitation-response-form"
                                            action="{{ route('trainer.notifications.respond', $notification->id) }}">
                                            @csrf
                                            <input type="hidden" name="decision" value="accept">
                                            <button type="submit" class="trainer-notification-action accept"
                                                data-loading-text="Memproses...">Terima</button>
                                        </form>
                                        <form method="POST" class="js-invitation-response-form"
                                            data-confirm="Yakin ingin menolak undangan ini?"
                                            action="{{ route('trainer.notifications.respond', $notification->id) }}">
                                            @csrf
                                            <input type="hidden" name="decision" value="reject">
                                            <button type="submit" class="trainer-notification-action reject"
                                                data-loading-text="Memproses...">Tolak</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="trainer-notification-empty">Belum ada notifikasi trainer.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div class="navbar-profile-dropdown" id="profileDropdownWrap">
                <button type="button" class="profile-link profile-trigger" onclick="toggleProfileDropdown()">
                    <div class="profile-info">
                        <span class="profile-name">{{ Auth::user()->name ?? 'Trainer' }}</span>
                        <span class="profile-role">INSTRUKTUR</span>
                    </div>
                    <div class="profile-avatar">
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}">
                    </div>
                </button>
                
                <div class="profile-menu" id="profileDropdownMenu">
                    <a href="{{ route('trainer.profile') }}" class="menu-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Lihat Profil
                    </a>
                    <hr class="menu-divider">
                    <form action="{{ route('logout') }}" method="POST" class="menu-item-form">
                        @csrf
                        <button type="submit" class="menu-item logout">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('trainerNotificationDropdown');
        const profileMenu = document.getElementById('profileDropdownMenu');
        if (profileMenu) profileMenu.classList.remove('show');
        if (!dropdown) return;
        dropdown.classList.toggle('show');
    }

    function toggleProfileDropdown() {
        const menu = document.getElementById('profileDropdownMenu');
        const notifDropdown = document.getElementById('trainerNotificationDropdown');
        if (notifDropdown) notifDropdown.classList.remove('show');
        if (!menu) return;
        menu.classList.toggle('show');
    }

    document.addEventListener('click', function (event) {
        const wrap = document.getElementById('trainerNotificationWrap');
        const dropdown = document.getElementById('trainerNotificationDropdown');
        if (wrap && dropdown && !wrap.contains(event.target)) {
            dropdown.classList.remove('show');
        }
        
        const profileWrap = document.getElementById('profileDropdownWrap');
        const profileMenu = document.getElementById('profileDropdownMenu');
        if (profileWrap && profileMenu && !profileWrap.contains(event.target)) {
            profileMenu.classList.remove('show');
        }
    });

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.classList.contains('js-invitation-response-form')) {
            return;
        }

        const confirmationMessage = form.dataset.confirm;
        if (confirmationMessage && !window.confirm(confirmationMessage)) {
            event.preventDefault();
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        if (!submitButton) {
            return;
        }

        if (submitButton.disabled) {
            event.preventDefault();
            return;
        }

        submitButton.disabled = true;
        const loadingText = submitButton.getAttribute('data-loading-text');
        if (loadingText) {
            submitButton.dataset.originalText = submitButton.textContent || '';
            submitButton.textContent = loadingText;
        }
    }, true);
</script>

