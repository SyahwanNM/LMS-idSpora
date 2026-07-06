<aside class="{{ $class ?? 'finance-sidebar' }}" style="position: sticky; top: 72px; height: calc(100vh - 72px);">
    <span class="nav-menu-label">Menu Utama</span>
    <a href="{{ route('admin.finance.index') }}" class="sidebar-link {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
        <i class="bi bi-wallet2"></i> Keuangan & Payout
    </a>
    <span class="nav-menu-label mt-4">Menu Transaksi</span>
    <a href="{{ route('admin.finance.incomes') }}" class="sidebar-link {{ request()->routeIs('admin.finance.incomes') ? 'active' : '' }}">
        <i class="bi bi-arrow-down-circle"></i> Pemasukan
    </a>
    <a href="{{ route('admin.finance.expenses') }}" class="sidebar-link {{ request()->routeIs('admin.finance.expenses') ? 'active' : '' }}">
        <i class="bi bi-arrow-up-circle"></i> Pengeluaran
        @if(isset($pendingWithdrawals) && $pendingWithdrawals > 0)
            <span class="badge-notif">{{ $pendingWithdrawals }}</span>
        @endif
    </a>
    <a href="{{ route('admin.finance.invoice-history') }}" class="sidebar-link {{ request()->routeIs('admin.finance.invoice-history') ? 'active' : '' }}">
        <i class="bi bi-receipt"></i> History Invoice
    </a>
    <a href="{{ route('admin.finance.trainers') }}" class="sidebar-link {{ request()->routeIs('admin.finance.trainers') ? 'active' : '' }}">
        <i class="bi bi-person-check"></i> Kelola Trainer
    </a>

    <span class="nav-menu-label mt-4">Laporan Aktivitas</span>
    <a href="{{ route('admin.finance.events') }}" class="sidebar-link {{ request()->routeIs('admin.finance.events') ? 'active' : '' }}">
        <i class="bi bi-calendar-event"></i> Laporan Event
    </a>
    <a href="{{ route('admin.finance.courses') }}" class="sidebar-link {{ request()->routeIs('admin.finance.courses') ? 'active' : '' }}">
        <i class="bi bi-book"></i> Laporan Course
    </a>
</aside>
