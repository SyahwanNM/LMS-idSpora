<aside class="trainer-sidebar d-none d-lg-block">
    <span class="nav-menu-label">TRAINER MANAGEMENT</span>
    <a href="{{ route('admin.trainer.index') }}"
        class="sidebar-link {{ request()->routeIs('admin.trainer.index') ? 'active' : '' }}">
        <i class="bi bi-people"></i> All Trainers
    </a>
    <a href="{{ route('admin.trainer.create') }}"
        class="sidebar-link {{ request()->routeIs('admin.trainer.create') ? 'active' : '' }}">
        <i class="bi bi-person-plus"></i> Add New Trainer
    </a>

    <span class="nav-menu-label">QUICK ACCESS</span>
    <a href="#materialApprovalMenu"
        class="sidebar-link sidebar-parent {{ request()->routeIs('admin.material.*') ? 'active' : '' }}"
        data-bs-toggle="collapse" role="button"
        aria-expanded="{{ request()->routeIs('admin.material.*') ? 'true' : 'false' }}"
        aria-controls="materialApprovalMenu">
        <span><i class="bi bi-clipboard-check"></i> Material Approval</span>
        <i class="bi bi-chevron-down sidebar-chevron"></i>
    </a>
    <div class="collapse sidebar-submenu {{ request()->routeIs('admin.material.*') ? 'show' : '' }}"
        id="materialApprovalMenu">
        <a href="{{ route('admin.material.approvals') }}"
            class="sidebar-link {{ request()->routeIs('admin.material.approvals') ? 'active' : '' }}">
            <i class="bi bi-hourglass-split"></i> Pending Review
        </a>
        <a href="{{ route('admin.material.approved') }}"
            class="sidebar-link {{ request()->routeIs('admin.material.approved') ? 'active' : '' }}">
            <i class="bi bi-check-circle"></i> Approved
        </a>
        <a href="{{ route('admin.material.rejected') }}"
            class="sidebar-link {{ request()->routeIs('admin.material.rejected') ? 'active' : '' }}">
            <i class="bi bi-x-circle"></i> Rejected
        </a>
    </div>

    <a href="{{ url('/admin/trainer/certificates/queue') }}"
        class="sidebar-link {{ request()->routeIs('admin.trainer.certificates.*') ? 'active' : '' }}">
        <i class="bi bi-award-fill"></i> Sertifikat
    </a>
</aside>