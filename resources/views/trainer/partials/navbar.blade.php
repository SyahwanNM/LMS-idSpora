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
            <button class="navbar-icon-btn" onclick="toggleNotifications()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="currentColor">
                    <path
                        d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6" />
                </svg>
                <span class="notification-badge">3</span>
            </button>

            <!-- Profile Dropdown -->
            <div class="navbar-profile-dropdown">
                <a href="{{ route('trainer.profile') }}" class="profile-link profile-trigger">
                    <div class="profile-info">
                        <span class="profile-name">{{ Auth::user()->name ?? 'Trainer' }}</span>
                        <span class="profile-role">INSTRUCTOR</span>
                    </div>
                    <div class="profile-avatar">
                        @if(Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                alt="{{ Auth::user()->name }}">
                        @else
                            <div class="avatar-placeholder">{{ strtoupper(substr(Auth::user()->name ?? 'T', 0, 1)) }}</div>
                        @endif
                    </div>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
    function toggleNotifications() {
        // Implement notification dropdown logic here
        console.log('Toggle notifications');
    }
</script>