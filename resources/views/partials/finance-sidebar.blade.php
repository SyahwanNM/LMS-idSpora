<aside class="{{ $class ?? 'finance-sidebar' }}" style="position: sticky; top: 72px; height: calc(100vh - 72px);">
    <span class="nav-menu-label">Menu Utama</span>
    <a href="{{ route('admin.finance.index') }}" class="sidebar-link {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
        <i class="bi bi-wallet2"></i> Keuangan & Payout
    </a>
    <a href="{{ route('admin.withdrawals.index') }}" class="sidebar-link {{ request()->routeIs('admin.withdrawals.index') ? 'active' : '' }}">
        <i class="bi bi-cash-stack"></i> Persetujuan Payout
        @if(isset($pendingWithdrawals) && $pendingWithdrawals > 0)
            <span class="badge-notif">{{ $pendingWithdrawals }}</span>
        @endif
    </a>

    <span class="nav-menu-label mt-4">Laporan Aktivitas</span>
    <a href="{{ route('admin.finance.events') }}" class="sidebar-link {{ request()->routeIs('admin.finance.events') ? 'active' : '' }}">
        <i class="bi bi-calendar-event"></i> Laporan Event
    </a>
    <a href="{{ route('admin.finance.courses') }}" class="sidebar-link {{ request()->routeIs('admin.finance.courses') ? 'active' : '' }}">
        <i class="bi bi-book"></i> Laporan Course
    </a>
</aside>
