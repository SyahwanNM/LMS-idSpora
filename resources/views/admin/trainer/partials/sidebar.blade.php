@php
    $isMaterialActive = request()->routeIs('admin.trainer.material.*');
    $isCertificateActive = request()->routeIs('admin.trainer.certificates.*');
@endphp

{{-- Sidebar Navigation --}}
<aside class="trainer-sidebar">
    <span class="nav-menu-label desktop-only">MANAJEMEN TRAINER</span>

    <a href="{{ route('admin.trainer.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.trainer.index') && request()->query('view') !== 'list' ? 'active' : '' }}">
        <i class="bi bi-grid-1x2-fill"></i>
        <span class="desktop-only">Dashboard</span>
        <span class="mobile-only">Dasbor</span>
    </a>

    <a href="{{ route('admin.trainer.index', ['view' => 'list']) }}#daftar-trainer"
       class="sidebar-link {{ request()->routeIs('admin.trainer.index') && request()->query('view') === 'list' ? 'active' : '' }}">
        <i class="bi bi-people"></i>
        <span class="desktop-only">Seluruh Trainer</span>
        <span class="mobile-only">Trainer</span>
    </a>

    <a href="{{ route('admin.trainer.create') }}"
       class="sidebar-link {{ request()->routeIs('admin.trainer.create') ? 'active' : '' }}">
        <i class="bi bi-person-plus"></i>
        <span class="desktop-only">Tambah Trainer Baru</span>
        <span class="mobile-only">Tambah</span>
    </a>

    <span class="nav-menu-label desktop-only">AKSES CEPAT</span>

    {{-- Desktop Persetujuan Materi (with Dropdown) --}}
    <a href="#materialApprovalMenuDesktop"
       class="sidebar-link sidebar-parent desktop-only {{ $isMaterialActive ? 'active' : '' }}"
       data-bs-toggle="collapse"
       role="button"
       aria-expanded="{{ $isMaterialActive ? 'true' : 'false' }}"
       aria-controls="materialApprovalMenuDesktop">
        <span>
            <i class="bi bi-clipboard-check"></i>
            Persetujuan Materi
        </span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </a>

    {{-- Mobile Persetujuan Materi (Direct Link) --}}
    <a href="{{ route('admin.trainer.material.approvals') }}"
       class="sidebar-link mobile-only {{ $isMaterialActive ? 'active' : '' }}">
        <i class="bi bi-clipboard-check"></i>
        <span>Persetujuan</span>
    </a>

    <div class="collapse sidebar-submenu desktop-only {{ $isMaterialActive ? 'show' : '' }}"
         id="materialApprovalMenuDesktop">
        <a href="{{ route('admin.trainer.material.approvals') }}"
           class="sidebar-link {{ request()->routeIs('admin.trainer.material.approvals') ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i>
            <span>Menunggu Tinjauan</span>
        </a>

        <a href="{{ route('admin.trainer.material.approved') }}"
           class="sidebar-link {{ request()->routeIs('admin.trainer.material.approved') ? 'active' : '' }}">
            <i class="bi bi-check-circle"></i>
            <span>Disetujui</span>
        </a>

        <a href="{{ route('admin.trainer.material.rejected') }}"
           class="sidebar-link {{ request()->routeIs('admin.trainer.material.rejected') ? 'active' : '' }}">
            <i class="bi bi-x-circle"></i>
            <span>Ditolak</span>
        </a>
    </div>

    <a href="{{ route('admin.trainer.certificates.index') }}"
       class="sidebar-link {{ $isCertificateActive ? 'active' : '' }}">
        <i class="bi bi-award-fill"></i>
        <span>Sertifikat</span>
    </a>

    <a href="{{ route('admin.trainer.studio.list') }}"
       class="sidebar-link {{ request()->routeIs('admin.trainer.studio.*') ? 'active' : '' }}">
        <i class="bi bi-collection-play-fill"></i>
        <span class="desktop-only">Course Studio</span>
        <span class="mobile-only">Studio</span>
    </a>
</aside>