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
                    <a href="{{ route('trainer.dashboard') }}">Home</a>
                    <span class="separator">›</span>
                    <span class="current">{{ $pageTitle ?? 'Dashboard' }}</span>
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
                                    @elseif($isInvitation && $notificationStatus === 'expired')
                                        <span class="trainer-notification-pill rejected">Expired</span>
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
                                @if($isMaterialRejected && !empty(data_get($notification->data, 'rejection_reason')))
                                    <p class="trainer-notification-message" style="margin-top:6px; color:#b42318;">
                                        Alasan revisi:
                                        {{ \Illuminate\Support\Str::limit((string) data_get($notification->data, 'rejection_reason'), 180) }}
                                    </p>
                                @endif
                                @if($isInvitation && $notificationDueDate)
                                    <p class="trainer-notification-deadline {{ $notificationIsOverdue ? 'is-overdue' : '' }}">
                                        Deadline: {{ $notificationDueDate->format('d M Y H:i') }}
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
                                        @php
                                            $notificationEntityType = data_get($notification->data, 'entity_type');
                                            $notificationTitle = addslashes((string) ($notification->title ?? 'Undangan Event'));
                                        @endphp
                                        @if($notificationEntityType === 'course')
                                            <button type="button" class="trainer-notification-action accept"
                                                onclick="openSchemeSelectionModal({{ $notification->id }}, '{{ $notificationTitle }}', '{{ $notificationEntityType }}')">
                                                Terima
                                            </button>
                                        @else
                                            <form method="POST" class="js-invitation-response-form"
                                                action="{{ route('trainer.notifications.respond', $notification->id) }}">
                                                @csrf
                                                <input type="hidden" name="decision" value="accept">
                                                <input type="hidden" name="e_agreement" value="1">
                                                <button type="submit" class="trainer-notification-action accept"
                                                    data-loading-text="Memproses...">Terima</button>
                                            </form>
                                        @endif
                                        <form method="POST" class="js-invitation-response-form"
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
            <div class="navbar-profile-dropdown">
                <a href="{{ route('trainer.profile') }}" class="profile-link profile-trigger">
                    <div class="profile-info">
                        <span class="profile-name">{{ Auth::user()->name ?? 'Trainer' }}</span>
                        <span class="profile-role">INSTRUCTOR</span>
                    </div>
                    <div class="profile-avatar">
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}">
                    </div>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('trainerNotificationDropdown');
        if (!dropdown) return;
        dropdown.classList.toggle('show');
    }

    document.addEventListener('click', function (event) {
        const wrap = document.getElementById('trainerNotificationWrap');
        const dropdown = document.getElementById('trainerNotificationDropdown');
        if (!wrap || !dropdown) return;

        if (!wrap.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.classList.contains('js-invitation-response-form')) {
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