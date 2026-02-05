<nav class="navbar navbar-expand-lg premium-nav fixed-top">
    <div class="container-fluid px-2 px-lg-5">
        <!-- PART 1: LOGO & BRAND -->
        <a class="navbar-brand d-flex align-items-center ms-lg-0 ms-3" href="{{ route('dashboard') }}">
            <img src="{{ asset('images/logo idspora_nobg_dark 1.png') }}" alt="Logo idSpora" 
                 class="img-fluid nav-logo" style="max-width:80px; height:auto;">
        </a>

        <!-- PART 2: MOBILE CONTROLS (Always visible on mobile) -->
        <div class="d-flex align-items-center d-lg-none ms-auto me-2 position-relative">
            <!-- Expandable Mobile Search Bar (Slides in from the right) -->
            <div class="mobile-search-expandable" id="mobileSearchExpandable">
                <div class="d-flex align-items-center w-100 h-100 px-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="white" viewBox="0 0 16 16" class="me-2 opacity-50">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                    </svg>
                    <input type="text" class="form-control bg-transparent border-0 text-white p-0 shadow-none" placeholder="Cari..." id="mobileSearchInput">
                    <button class="btn p-0 ms-2 text-white opacity-75" type="button" id="closeMobileSearch">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Search Icon -->
            <button class="btn btn-nav-icon me-2" type="button" id="mobileSearchBtn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                </svg>
            </button>
            
            <!-- Mobile Notif Icon -->
            <button class="btn btn-nav-icon position-relative me-3" type="button" id="mobileNotifBtn" onclick="toggleNotificationDropdown()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-1.203-3.92L10 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                </svg>
                <span class="position-absolute translate-middle badge rounded-pill bg-danger badge-custom" id="notificationBadgeMobile" style="{{ $unreadNotificationCount > 0 ? '' : 'display: none;' }}">{{ $unreadNotificationCount }}</span>
            </button>

            <!-- Burger Menu Toggler -->
            <button class="navbar-toggler border-0 p-0 collapsed" type="button" id="burgerToggler">
                <div class="burger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>
        </div>

        <!-- PART 3: THE COLLAPSIBLE CONTENT (Left Menu + Desktop Actions) -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <div class="navbar-content-wrapper d-flex flex-column flex-lg-row w-100">
                
                <!-- LEFT SIDE: NAV LINKS -->
                <ul class="navbar-nav me-lg-auto mb-2 mb-lg-0 mt-3 mt-lg-0 ps-lg-4 align-items-start align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('courses.index') ? 'active' : '' }}" href="{{ route('courses.index') }}">Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('events.index') ? 'active' : '' }}" href="{{ route('events.index') }}">Events</a>
                    </li>
                </ul>

                <!-- RIGHT SIDE: DESKTOP ACTIONS (HIDDEN ON MOBILE TILL COLLAPSE) -->
                <div class="desktop-actions-nav d-flex flex-column flex-lg-row align-items-start align-items-lg-center mt-lg-0 mt-3">
                    
                    <!-- Search Bar (Desktop) -->
                    <div class="nav-search-wrapper d-none d-lg-block me-lg-4">
                        <form class="search-form-premium" role="search">
                            <input class="form-control nav-input-premium" type="search" placeholder="Search anything..." aria-label="Search">
                            <span class="search-icon-inside">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242 1.106a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/>
                                </svg>
                            </span>
                        </form>
                    </div>

                    <!-- Notif Icon (Desktop) -->
                    <div class="dropdown d-none d-lg-block me-lg-3">
                        <button class="btn btn-nav-icon position-relative" type="button" id="notifBtn" onclick="toggleNotificationDropdown()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2M8 1.918l-.797.161A4 4 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4 4 0 0 0-1.203-3.92L10 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5 5 0 0 1 13 6c0 .88.32 4.2 1.22 6"/>
                            </svg>
                            <span class="position-absolute translate-middle badge rounded-pill bg-danger badge-custom" id="notificationBadge" style="{{ $unreadNotificationCount > 0 ? '' : 'display: none;' }}">{{ $unreadNotificationCount }}</span>
                        </button>
                        
                        <!-- Notif Dropdown -->
                        <div class="dropdown-menu dropdown-menu-end shadow notification-dropdown-premium" id="notificationDropdown">
                            <div class="dropdown-header-premium d-flex justify-content-between align-items-center">
                                <span>Notifications</span>
                                <button class="btn btn-link btn-sm p-0 text-warning" onclick="markAllAsRead()">Mark all as read</button>
                            </div>
<div id="notificationList" class="notif-list-container" style="max-height: 350px; overflow-y: auto;">
                                @forelse($notifications as $notification)
                                    <div class="dropdown-item p-3 border-bottom {{ is_null($notification->read_at) ? 'bg-opacity-10 bg-primary' : '' }}" style="white-space: normal;">
                                        <div class="d-flex w-100 justify-content-between align-items-start">
                                            <div class="d-flex align-items-center">
                                                @if($notification->read_at)
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success me-2 flex-shrink-0" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-warning me-2 flex-shrink-0" viewBox="0 0 16 16">
                                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                                    </svg>
                                                @endif
                                                <h6 class="mb-1 fw-bold text-white small">{{ $notification->title }}</h6>
                                            </div>
                                            <small class="text-white-50 ms-2" style="font-size: 0.7rem; white-space: nowrap;">{{ $notification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 small text-white-50 ms-4">{{ Str::limit($notification->message, 80) }}</p>
                                    </div>
                                @empty
                                    <div class="px-3 py-4 text-center text-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16" class="mb-2 opacity-50">
                                            <path d="M4 8.5V7a4 4 0 1 1 8 0v1.5H4zm1 0h6V7a3 3 0 1 0-6 0v1.5zM3.5 10a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9z"/>
                                            <path d="M8 12a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                                        </svg>
                                        <p class="small mb-0">No new notifications</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Profile (Desktop & Mobile inside menu) -->
                    <div class="dropdown user-dropdown-premium w-100 w-lg-auto">
                        <button class="btn user-profile-btn d-flex align-items-center w-100" type="button" id="userDropdown" onclick="toggleUserDropdown()">
                            <img src="{{ Auth::user()->avatar_url }}" 
                                 alt="Avatar" class="avatar-nav me-2"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=6b7280&color=ffffff';">
                            <div class="user-info-nav text-start d-lg-block">
                                <span class="user-name-text text-white d-block">{{ Auth::user()->name }}</span>
                                <span class="user-points-sub text-warning small"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-star-fill me-1" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>{{ Auth::user()->points ?? 0 }} pts</span>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-2 arrow-icon" viewBox="0 0 16 16">
                                <path d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </button>
                        
                        <!-- Account Dropdown Menu -->
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg account-menu-premium" id="userDropdownMenu">
                            <li><a class="dropdown-item" href="{{ route('reseller.index') }}"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M8 2a.5.5 0 0 1 .5.5V4a.5.5 0 0 1-1 0V2.5A.5.5 0 0 1 8 2zM3.732 3.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM2 8a.5.5 0 0 1 .5-.5h1.586a.5.5 0 0 1 0 1H2.5A.5.5 0 0 1 2 8zm1.732 4.268a.5.5 0 0 1 0-.707l.914-.915a.5.5 0 1 1 .708.708l-.915.914a.5.5 0 0 1-.707 0zm4.268 1.732a.5.5 0 0 1-.5-.5V11a.5.5 0 0 1 1 0v2.5a.5.5 0 0 1-.5.5zm4.268-1.732a.5.5 0 0 1 .707 0l.915.914a.5.5 0 1 1-.708.708l-.914-.915a.5.5 0 0 1 0-.707zM14 8a.5.5 0 0 1-.5.5h-1.586a.5.5 0 0 1 0-1H13.5A.5.5 0 0 1 14 8zM12.268 3.732a.5.5 0 0 1 0 .707l-.914.915a.5.5 0 1 1-.708-.708l.915-.914a.5.5 0 0 1 .707 0zM8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>Reseller</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.index') }}"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/></svg>My Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.events') }}"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M11 6.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1z"/><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/></svg>Events & History</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile.settings') }}"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.233 1.841-.53 2.508l-.242.21c-1.082.942-1.082 2.59 0 3.532l.242.21c.763.667.976 1.688.53 2.508l-.169.311c-.699 1.282.704 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.311.17c1.282.699 2.686-.704 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .53-2.508l.242-.21c1.082-.942 1.082-2.59 0-3.532l-.242-.21a1.464 1.464 0 0 1-.53-2.508l.169-.311c.699-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/></svg>Settings</a></li>
                            <li><a class="dropdown-item" href="{{ route('public.guide') }}"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z"/></svg>Panduan Platform</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <button type="button" class="dropdown-item text-danger" onclick="openLogoutModal()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="me-2" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/></svg>Sign Out
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</nav>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 24px; background: #1F1D36;">
            <div class="modal-body p-5 text-center">
                <!-- Icon background circle -->
                <div class="d-inline-flex align-items-center justify-content-center mb-4" 
                     style="width: 80px; height: 80px; background: rgba(239, 68, 68, 0.1); border-radius: 50%;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#EF4444" viewBox="0 0 16 16">
                        <path d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/>
                        <path d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                    </svg>
                </div>
                
                <h4 class="text-white font-bold mb-2">Sign Out</h4>
                <p class="text-gray-400 mb-4 px-3" style="color: rgba(255,255,255,0.6);">Are you sure you want to sign out from your account?</p>
                
                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                    <button type="button" class="btn px-4 py-2" data-bs-dismiss="modal"
                            style="border-radius: 12px; background: rgba(255,255,255,0.05); color: #fff; border: 1px solid rgba(255,255,255,0.1); font-weight: 500;">
                        Cancel
                    </button>
                    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                        @csrf
                        <button type="submit" class="btn px-4 py-2 w-100" 
                                style="border-radius: 12px; background: #EF4444; color: #fff; border: none; font-weight: 600;">
                            Yes, Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('partials.flash')
@include('partials.profile-reminder-banner')

<style>
/* 
PREMIUM DESIGN SYSTEM - AUTH NAVBAR
==================================== */

/* Use same font as Dashboard */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

.premium-nav {
    font-family: 'Poppins', sans-serif;
    background: radial-gradient(circle at 10% 10%, #42327D 0%, #1A182E 100%) !important;
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    padding: 0.6rem 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1050;
    color: #fff !important; /* Force global text color to white */
}

/* Fix Tailwind Conflict for Bootstrap Collapse */
.premium-nav .collapse.navbar-collapse {
    display: none; /* Default Bootstrap behavior */
    visibility: visible !important;
}

.premium-nav .collapse.navbar-collapse.show {
    display: block !important;
}

@media (min-width: 992px) {
    .premium-nav .collapse.navbar-collapse {
        display: flex !important;
    }
}

/* Nav Link Styling */
.premium-nav .nav-link {
    color: rgba(255, 255, 255, 0.7) !important;
    font-size: 0.95rem;
    font-weight: 500;
    padding: 0.6rem 1.2rem !important;
    position: relative;
    transition: all 0.3s ease;
}

.premium-nav .nav-link:hover {
    color: #fff !important;
    transform: translateY(-2px);
}

.premium-nav .nav-link.active {
    color: #FBBD23 !important; /* Premium Yellow */
}

.premium-nav .nav-link::after {
    content: '';
    position: absolute;
    bottom: 5px;
    left: 50%;
    width: 0;
    height: 2px;
    background: #FBBD23;
    transform: translateX(-50%);
    transition: all 0.3s ease;
    border-radius: 10px;
}

.premium-nav .nav-link:hover::after,
.premium-nav .nav-link.active::after {
    width: 70%;
}

/* Premium Search Input - Fluid for Laptops */
.search-form-premium {
    position: relative;
    width: clamp(180px, 20vw, 280px);
}

.nav-input-premium {
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    border-radius: 50px !important;
    color: #fff !important;
    padding: 0.6rem 1.2rem 0.6rem 2.8rem !important;
    font-size: 0.88rem !important;
    transition: all 0.3s ease !important;
}

.nav-input-premium:focus {
    background: rgba(255, 255, 255, 0.1) !important;
    border-color: #FBBD23 !important;
    box-shadow: 0 0 15px rgba(251, 189, 35, 0.2) !important;
}

.search-icon-inside {
    position: absolute;
    left: 1.2rem;
    top: 50%;
    transform: translateY(-50%);
    color: rgba(255, 255, 255, 0.5);
    display: flex;
    align-items: center;
}

/* Icon Buttons */
.btn-nav-icon {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.btn-nav-icon:hover {
    background: rgba(255, 255, 255, 0.12);
    color: #FBBD23;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.badge-custom {
    top: 8px !important;
    right: 8px !important;
    padding: 3px 5px !important;
    font-size: 0.6rem !important;
    border: 2px solid #1A182E;
    font-weight: 700;
}

/* User Profile Section */
.user-profile-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    padding: 6px 14px 6px 8px !important;
    border-radius: 40px;
    transition: all 0.3s ease;
}

.user-profile-btn:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
}

.avatar-nav {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(251, 189, 35, 0.5);
    background: #2D2B4A;
}

.user-name-text {
    font-size: 0.9rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 120px;
}

.user-points-sub {
    font-weight: 600;
    letter-spacing: 0.5px;
    color: #FBBD23 !important;
}

.arrow-icon {
    color: rgba(255, 255, 255, 0.4);
    font-size: 0.75rem;
    transition: transform 0.3s ease;
}

.user-dropdown-premium .show .arrow-icon {
    transform: rotate(180deg);
}

/* Dropdowns Premium */
.dropdown-menu {
    background: #1F1D36 !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 16px !important;
    margin-top: 12px !important;
    padding: 8px !important;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4) !important;
    animation: dropdownFadeIn 0.3s ease forwards;
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.dropdown-item {
    color: rgba(255, 255, 255, 0.8) !important;
    padding: 0.8rem 1.2rem !important;
    border-radius: 10px !important;
    font-size: 0.9rem !important;
    transition: all 0.2s ease !important;
}

.dropdown-item:hover {
    background: rgba(255, 255, 255, 0.05) !important;
    color: #FBBD23 !important;
    padding-left: 1.5rem !important;
}

.notification-dropdown-premium {
    width: 380px;
    padding: 0 !important;
    overflow: hidden;
    right: 0 !important;
    left: auto !important;
}

.dropdown-header-premium {
    background: rgba(255, 255, 255, 0.03);
    padding: 1rem 1.2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    color: #fff;
    font-weight: 600;
}

/* Burger Icon Animation */
.burger-icon {
    width: 24px;
    height: 18px;
    position: relative;
    cursor: pointer;
}

.burger-icon span {
    display: block;
    position: absolute;
    height: 2px;
    width: 100%;
    background: #fff;
    border-radius: 9px;
    transition: .3s ease-in-out;
}

.burger-icon span:nth-child(1) { top: 0; }
.burger-icon span:nth-child(2) { top: 8px; width: 70%; right: 0; }
.burger-icon span:nth-child(3) { top: 16px; }

.navbar-toggler:not(.collapsed) .burger-icon span:nth-child(1) { transform: rotate(45deg); top: 8px; }
.navbar-toggler:not(.collapsed) .burger-icon span:nth-child(2) { opacity: 0; width: 0; }
.navbar-toggler:not(.collapsed) .burger-icon span:nth-child(3) { transform: rotate(-45deg); top: 8px; }

/* Mobile Search Expandable */
.mobile-search-expandable {
    position: absolute;
    top: 50%;
    right: 0;
    transform: translateY(-50%);
    width: 0;
    height: 42px;
    background: #2D2B4A;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    opacity: 0;
    z-index: 100;
}

.mobile-search-expandable.active {
    width: calc(100vw - 120px); /* Fill most of the navbar */
    opacity: 1;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
}

.mobile-search-expandable input {
    font-size: 0.9rem !important;
}

.mobile-search-expandable input::placeholder {
    color: rgba(255, 255, 255, 0.4);
}

/* Mobile Responsive Tweaks */
@media (max-width: 991.98px) {
    .premium-nav .container-fluid {
        padding: 0 15px !important;
    }

    .navbar-collapse {
        background: rgba(26, 24, 46, 0.95) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        margin: 15px 0 !important;
        padding: 25px !important;
        border-radius: 20px !important;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        position: absolute;
        top: 100%;
        left: 15px;
        right: 15px;
        z-index: 1060;
    }
    
    .navbar-nav .nav-link {
        width: 100%;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding: 0.8rem 0 !important;
        font-size: 1rem !important;
    }
    
    .navbar-nav .nav-link::after { display: none; }
    
    .desktop-actions-nav {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: 20px;
        padding-top: 20px;
        width: 100%;
    }
    
    .user-profile-btn {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(251, 189, 35, 0.3);
        width: 100%;
        margin-bottom: 10px;
    }

    .user-name-text {
        max-width: 100% !important;
    }

    /* Mobile Dropdown Fix */
    .dropdown-menu {
        position: static !important;
        float: none !important;
        width: 100% !important;
        background: rgba(255, 255, 255, 0.03) !important;
        margin-top: 5px !important;
        border: none !important;
        box-shadow: none !important;
    }

    .notification-dropdown-premium {
        width: calc(100vw - 40px) !important;
        position: fixed !important;
        top: 85px !important;
        left: 20px !important;
        right: 20px !important;
        margin: 0 auto !important;
        max-height: 70vh !important;
        transform: none !important;
        z-index: 9999 !important;
    }
}

/* Backdrop for focus */
.nav-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    z-index: 1040;
    display: none;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.nav-backdrop.show {
    display: block;
    opacity: 1;
}
</style>

<script>
/**
 * PREMIUM NAVBAR LOGIC
 * Interacting with Bootstrap API manually for smoothness
 */
document.addEventListener('DOMContentLoaded', function() {
    const navCollapseEl = document.getElementById('navbarSupportedContent');
    const mobileSearchExpandable = document.getElementById('mobileSearchExpandable');
    const closeMobileSearch = document.getElementById('closeMobileSearch');
    const burgerToggler = document.getElementById('burgerToggler');
    const mobileSearchBtn = document.getElementById('mobileSearchBtn');

    // Create Backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'nav-backdrop';
    document.body.appendChild(backdrop);

    function toggleBackdrop(show) {
        if (show) {
            backdrop.classList.add('show');
            document.body.style.overflow = 'hidden';
        } else {
            backdrop.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    if (navCollapseEl && burgerToggler) {
        // Safe Check for Bootstrap
        const getBootstrap = () => window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
        
        const initNavbar = () => {
            const bs = getBootstrap();
            if (!bs) return;

            const bsNav = new bs.Collapse(navCollapseEl, { toggle: false });

            burgerToggler.addEventListener('click', function (e) {
                e.preventDefault();
                const isOpen = navCollapseEl.classList.contains('show');
                if (isOpen) {
                    bsNav.hide();
                    burgerToggler.classList.add('collapsed');
                    toggleBackdrop(false);
                } else {
                    if (mobileSearchExpandable && mobileSearchExpandable.classList.contains('active')) {
                        mobileSearchExpandable.classList.remove('active');
                    }
                    bsNav.show();
                    burgerToggler.classList.remove('collapsed');
                    toggleBackdrop(true);
                }
            });

            if (mobileSearchBtn && mobileSearchExpandable) {
                mobileSearchBtn.addEventListener('click', function() {
                    bsNav.hide();
                    burgerToggler.classList.add('collapsed');
                    mobileSearchExpandable.classList.add('active');
                    document.getElementById('mobileSearchInput').focus();
                    toggleBackdrop(true);
                });

                if (closeMobileSearch) {
                    closeMobileSearch.addEventListener('click', function() {
                        mobileSearchExpandable.classList.remove('active');
                        toggleBackdrop(false);
                    });
                }
            }

            backdrop.addEventListener('click', () => {
                bsNav.hide();
                if (mobileSearchExpandable) mobileSearchExpandable.classList.remove('active');
                burgerToggler.classList.add('collapsed');
                toggleBackdrop(false);
            });
        };

        // Try to init, or wait if bootstrap isn't ready
        if (getBootstrap()) {
            initNavbar();
        } else {
            window.addEventListener('load', initNavbar);
        }
    }
});

function toggleNotificationDropdown() {
    const dropdown = document.getElementById('notificationDropdown');
    const userMenu = document.getElementById('userDropdownMenu');
    
    if (userMenu) userMenu.style.display = 'none';
    
    const isVisible = dropdown.style.display === 'block';
    dropdown.style.display = isVisible ? 'none' : 'block';
    
    if (!isVisible && typeof loadNotifications === 'function') {
        // loadNotifications(); // Disabled as we are using View Composer
    }
}

function markAllAsRead() {
    fetch('{{ route("notifications.markAllRead") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.ok) {
            // Update UI: hide badges
            const badges = document.querySelectorAll('.badge-custom');
            badges.forEach(b => b.style.display = 'none');
            
            // Update icons to green
            const icons = document.querySelectorAll('#notificationList .text-warning');
            icons.forEach(icon => {
                icon.classList.remove('text-warning');
                icon.classList.add('text-success');
            });
            
            // Remove unread background styling
            const items = document.querySelectorAll('#notificationList .bg-opacity-10.bg-primary');
            items.forEach(item => {
                item.classList.remove('bg-opacity-10', 'bg-primary');
            });
        }
    })
    .catch(error => console.error('Error marking notifications as read:', error));
}

function toggleUserDropdown() {
    const dropdown = document.getElementById('userDropdownMenu');
    const notifMenu = document.getElementById('notificationDropdown');
    
    if (notifMenu) notifMenu.style.display = 'none';
    
    const isVisible = dropdown.style.display === 'block';
    dropdown.style.display = isVisible ? 'none' : 'block';
}

function openLogoutModal() {
    const getBootstrap = () => window.bootstrap || (typeof bootstrap !== 'undefined' ? bootstrap : null);
    const bs = getBootstrap();
    if (bs) {
        const modalEl = document.getElementById('logoutModal');
        const modal = new bs.Modal(modalEl);
        modal.show();
    } else {
        // Fallback if bootstrap is not ready
        if (confirm('Are you sure you want to sign out?')) {
            document.getElementById('logoutForm').submit();
        }
    }
}

// Close dropdowns on outside click
document.addEventListener('click', function(e) {
    if (!e.target.closest('#notifBtn') && !e.target.closest('#notificationDropdown') && !e.target.closest('#mobileNotifBtn')) {
        const d = document.getElementById('notificationDropdown');
        if (d) d.style.display = 'none';
    }
    if (!e.target.closest('#userDropdown') && !e.target.closest('#userDropdownMenu')) {
        const d = document.getElementById('userDropdownMenu');
        if (d) d.style.display = 'none';
    }
});
</script>   