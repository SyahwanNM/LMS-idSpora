@php
    $isMaterialActive = request()->routeIs('admin.trainer.material.*');
    $isCertificateActive = request()->routeIs('admin.trainer.certificates.*');
@endphp

{{-- Desktop Sidebar --}}
<aside class="trainer-sidebar d-none d-lg-block">
    <span class="nav-menu-label">TRAINER MANAGEMENT</span>

    <a href="{{ route('admin.trainer.index') }}"
       class="sidebar-link {{ request()->routeIs('admin.trainer.index') ? 'active' : '' }}">
        <i class="bi bi-people"></i>
        <span>All Trainers</span>
    </a>

    <a href="{{ route('admin.trainer.create') }}"
       class="sidebar-link {{ request()->routeIs('admin.trainer.create') ? 'active' : '' }}">
        <i class="bi bi-person-plus"></i>
        <span>Add New Trainer</span>
    </a>

    <span class="nav-menu-label">QUICK ACCESS</span>

    <a href="#materialApprovalMenuDesktop"
       class="sidebar-link sidebar-parent {{ $isMaterialActive ? 'active' : '' }}"
       data-bs-toggle="collapse"
       role="button"
       aria-expanded="{{ $isMaterialActive ? 'true' : 'false' }}"
       aria-controls="materialApprovalMenuDesktop">
        <span>
            <i class="bi bi-clipboard-check"></i>
            Material Approval
        </span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </a>

    <div class="collapse sidebar-submenu {{ $isMaterialActive ? 'show' : '' }}"
         id="materialApprovalMenuDesktop">
        <a href="{{ route('admin.trainer.material.approvals') }}"
           class="sidebar-link {{ request()->routeIs('admin.trainer.material.approvals') ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i>
            <span>Pending Review</span>
        </a>

        <a href="{{ route('admin.trainer.material.approved') }}"
           class="sidebar-link {{ request()->routeIs('admin.trainer.material.approved') ? 'active' : '' }}">
            <i class="bi bi-check-circle"></i>
            <span>Approved</span>
        </a>

        <a href="{{ route('admin.trainer.material.rejected') }}"
           class="sidebar-link {{ request()->routeIs('admin.trainer.material.rejected') ? 'active' : '' }}">
            <i class="bi bi-x-circle"></i>
            <span>Rejected</span>
        </a>
    </div>

    <a href="{{ route('admin.trainer.certificates.index') }}"
       class="sidebar-link {{ $isCertificateActive ? 'active' : '' }}">
        <i class="bi bi-award-fill"></i>
        <span>Sertifikat</span>
    </a>
</aside>

{{-- Mobile Sidebar --}}
<div class="offcanvas offcanvas-start admin-trainer-offcanvas d-lg-none"
     tabindex="-1"
     id="adminTrainerSidebarMobile"
     aria-labelledby="adminTrainerSidebarMobileLabel">

    <div class="offcanvas-header">
        <div>
            <h5 class="offcanvas-title fw-bold mb-0"
                id="adminTrainerSidebarMobileLabel">
                Admin Trainer
            </h5>
            <small class="text-muted">
                Management Menu
            </small>
        </div>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">
        <span class="nav-menu-label">TRAINER MANAGEMENT</span>

        <a href="{{ route('admin.trainer.index') }}"
           class="sidebar-link {{ request()->routeIs('admin.trainer.index') ? 'active' : '' }}">
            <i class="bi bi-people"></i>
            <span>All Trainers</span>
        </a>

        <a href="{{ route('admin.trainer.create') }}"
           class="sidebar-link {{ request()->routeIs('admin.trainer.create') ? 'active' : '' }}">
            <i class="bi bi-person-plus"></i>
            <span>Add New Trainer</span>
        </a>

        <span class="nav-menu-label">QUICK ACCESS</span>

        <a href="#materialApprovalMenuMobile"
           class="sidebar-link sidebar-parent {{ $isMaterialActive ? 'active' : '' }}"
           data-bs-toggle="collapse"
           role="button"
           aria-expanded="{{ $isMaterialActive ? 'true' : 'false' }}"
           aria-controls="materialApprovalMenuMobile">
            <span>
                <i class="bi bi-clipboard-check"></i>
                Material Approval
            </span>
            <i class="bi bi-chevron-down sidebar-chevron"></i>
        </a>

        <div class="collapse sidebar-submenu {{ $isMaterialActive ? 'show' : '' }}"
             id="materialApprovalMenuMobile">
            <a href="{{ route('admin.trainer.material.approvals') }}"
               class="sidebar-link {{ request()->routeIs('admin.trainer.material.approvals') ? 'active' : '' }}">
                <i class="bi bi-hourglass-split"></i>
                <span>Pending Review</span>
            </a>

            <a href="{{ route('admin.trainer.material.approved') }}"
               class="sidebar-link {{ request()->routeIs('admin.trainer.material.approved') ? 'active' : '' }}">
                <i class="bi bi-check-circle"></i>
                <span>Approved</span>
            </a>

            <a href="{{ route('admin.trainer.material.rejected') }}"
               class="sidebar-link {{ request()->routeIs('admin.trainer.material.rejected') ? 'active' : '' }}">
                <i class="bi bi-x-circle"></i>
                <span>Rejected</span>
            </a>
        </div>

        <a href="{{ route('admin.trainer.certificates.queue') }}"
           class="sidebar-link {{ $isCertificateActive ? 'active' : '' }}">
            <i class="bi bi-award-fill"></i>
            <span>Sertifikat</span>
        </a>
    </div>
</div>

@push('admin-trainer-scripts')
<script>
    document.addEventListener('click', function (event) {
        const link = event.target.closest('.admin-trainer-offcanvas .sidebar-link');

        if (!link) return;

        const isCollapseTrigger = link.getAttribute('data-bs-toggle') === 'collapse';

        if (isCollapseTrigger) return;

        const offcanvasEl = document.getElementById('adminTrainerSidebarMobile');

        if (!offcanvasEl || typeof bootstrap === 'undefined') return;

        const instance = bootstrap.Offcanvas.getInstance(offcanvasEl);

        if (instance) {
            instance.hide();
        }
    });
</script>
@endpush