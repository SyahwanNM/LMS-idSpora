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
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('admin.courses.*') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">Course Builder</a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ request()->routeIs('report') ? 'active' : '' }}" href="{{ route('report') }}">Report</a>
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
/* Offset page content so fixed-top navbar doesn't overlap */
body {padding-top: 64px;}
</style>
