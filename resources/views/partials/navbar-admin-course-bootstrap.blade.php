<!-- Ensure Bootstrap CSS is available for this partial when layout doesn't include it -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<nav class="navbar navbar-expand-lg navbar-dark bg-purple-gradient shadow-sm fixed-top w-100">
  <div class="container-fluid px-3">
    <a class="navbar-brand d-flex align-items-center" href="{{ route('admin.dashboard') }}">
      <img src="{{ asset('aset/logo.png') }}" alt="idSpora" class="me-2" style="height:28px;">
      <span class="fw-semibold">Admin • Course</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdminCourse" aria-controls="navbarAdminCourse" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarAdminCourse">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">Course Builder</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}" href="{{ route('report') }}">Report</a>
        </li>
        @php
          $u = Auth::user();
          $displayName = $u?->name ?? 'Admin';
          $avatarUrl = null;
          if($u && !empty($u->avatar)){
            $avatarUrl = filter_var($u->avatar, FILTER_VALIDATE_URL) ? $u->avatar : \Illuminate\Support\Facades\Storage::url($u->avatar);
          } elseif($u && !empty($u->avatar_url)){
            $avatarUrl = $u->avatar_url;
          } else {
            $avatarUrl = 'https://ui-avatars.com/api/?name='.urlencode($displayName).'&background=6f42c1&color=fff&size=64';
          }
        @endphp
        <li class="nav-item dropdown ms-lg-3">
          <a class="nav-link admin-profile-link dropdown-toggle d-flex align-items-center gap-2" href="#" id="adminProfileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ $avatarUrl }}" alt="Profile" class="rounded-circle border border-2 border-warning" style="width:32px;height:32px;object-fit:cover;">
            <span class="d-none d-lg-inline">{{ $displayName }}</span>
            <span class="badge bg-warning text-dark">Admin</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="adminProfileDropdown">
            <li class="px-3 py-2">
              <div class="d-flex align-items-center gap-2">
                <img src="{{ $avatarUrl }}" alt="Profile" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;">
                <div class="small">
                  <div class="fw-semibold">{{ $displayName }}</div>
                  @if(!empty($u?->email))
                    <div class="text-muted">{{ $u->email }}</div>
                  @endif
                </div>
              </div>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('admin.courses.index') }}">Kelola Course</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
/* Ensure gradient + link colors match admin layout when used standalone */
.bg-purple-gradient {background:linear-gradient(90deg,#6f42c1 0%, #a855f7 100%);}    
.navbar .nav-link {color: rgba(255,255,255,.9);} 
.navbar .nav-link:hover {color: #fff;}
.navbar .nav-link.active {color:#fff;position:relative;}
.navbar .nav-link.active::after {content:"";position:absolute;left:.5rem;right:.5rem;bottom:-.4rem;height:2px;background:#fff;border-radius:2px;opacity:.9;}
/* Force admin profile text to black for visibility */
.navbar .admin-profile-link {color:#000 !important;}
.navbar .admin-profile-link:hover, .navbar .admin-profile-link:focus {color:#000 !important;}
/* White background pill for profile trigger */
.navbar .admin-profile-link { background:#fff; padding:.2rem .55rem; border-radius:999px; box-shadow:0 1px 4px rgba(0,0,0,0.05); }
.navbar .admin-profile-link img { background:#fff; }
/* Ensure fixed-top navbar stays above toasts/other fixed elements */
.navbar { z-index: 1100; }
.navbar .dropdown-menu { z-index: 1110; }
/* Offset page content so fixed-top navbar doesn't overlap */
body {padding-top: 64px;}
</style>

<!-- Include Bootstrap JS so collapse/dropdown work when this partial is used standalone -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script>
  // Ensure the navbar collapse is visible on large screens when the layout doesn't initialize Bootstrap
  document.addEventListener('DOMContentLoaded', function(){
    try{
      var collapse = document.getElementById('navbarAdminCourse');
      if(!collapse) return;
      // On large viewports, force expand so links and profile are visible
      if(window.innerWidth >= 992){
        collapse.classList.add('show');
      }
      // Create a bootstrap Dropdown instance for the profile if available
      var toggle = document.getElementById('adminProfileDropdown');
      if(window.bootstrap && toggle){
        try{ new window.bootstrap.Dropdown(toggle); }catch(e){}
      }
    }catch(e){}
  });
</script>
