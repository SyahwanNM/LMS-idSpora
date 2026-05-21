@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    :root {
        --surface: #ffffff;
        --background: #f8fafc;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --border: #e2e8f0;
    }

    body {
        background-color: var(--background);
    }

    /* Animations */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-up {
        animation: fadeUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }

    /* Welcome Area */
    .welcome-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .welcome-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        letter-spacing: -0.02em;
        margin: 0;
    }
    .welcome-subtitle {
        color: var(--text-muted);
        font-size: 0.95rem;
        margin: 0;
    }

    /* KPI Cards */
    .kpi-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }
    .kpi-card:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
        transform: translateY(-2px);
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .kpi-content {
        flex-grow: 1;
    }
    .kpi-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }
    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
    }

    /* KPI Specific Colors */
    .kpi-users .kpi-icon { background: #e0e7ff; color: #4338ca; }
    .kpi-courses .kpi-icon { background: #ffedd5; color: #c2410c; }
    .kpi-events .kpi-icon { background: #e0f2fe; color: #0369a1; }
    .kpi-revenue .kpi-icon { background: #dcfce7; color: #15803d; }

    /* Module Grid */
    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 1rem;
    }
    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }
    .module-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        text-decoration: none !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .module-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: transparent;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }
    .module-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.08);
        border-color: transparent;
    }
    .module-card:hover::after {
        transform: scaleX(1);
    }
    .module-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.25rem;
        transition: transform 0.3s ease;
    }
    .module-card:hover .module-icon {
        transform: scale(1.1) rotate(5deg);
    }
    .module-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-main);
        margin-bottom: 0.5rem;
    }
    .module-desc {
        font-size: 0.875rem;
        color: var(--text-muted);
        line-height: 1.5;
        margin-bottom: 0;
    }

    /* Module Specific Colors (Pastel + Deep Core) */
    .mod-course .module-icon { background: #ffedd5; color: #c2410c; }
    .mod-course::after { background: #c2410c; }

    .mod-event .module-icon { background: #e0f2fe; color: #0369a1; }
    .mod-event::after { background: #0369a1; }

    .mod-finance .module-icon { background: #dcfce7; color: #15803d; }
    .mod-finance::after { background: #15803d; }

    .mod-crm .module-icon { background: #fee2e2; color: #b91c1c; }
    .mod-crm::after { background: #b91c1c; }

    .mod-trainer .module-icon { background: #fce7f3; color: #be185d; }
    .mod-trainer::after { background: #be185d; }

    .mod-reseller .module-icon { background: #e0e7ff; color: #4338ca; }
    .mod-reseller::after { background: #4338ca; }

    .mod-content .module-icon { background: #f3f4f6; color: #475569; }
    .mod-content::after { background: #475569; }

    /* Action Items Panels */
    .panel-card {
        background: var(--surface);
        border-radius: 16px;
        border: 1px solid var(--border);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .panel-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .panel-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .panel-body {
        padding: 1.25rem 1.5rem;
        flex-grow: 1;
        overflow-y: auto;
    }
    
    /* Action Items Rows */
    .action-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 0.75rem;
        border: 1px solid transparent;
        transition: all 0.2s;
    }
    .action-item:hover {
        border-color: #e2e8f0;
        background: #ffffff;
        transform: translateX(4px);
    }
    .action-title {
        font-weight: 600;
        font-size: 0.9rem;
        color: var(--text-main);
    }
    .action-meta {
        font-size: 0.8rem;
        color: var(--text-muted);
    }
</style>

<div class="container-fluid py-4 px-xl-4">
    
    <!-- Welcome Section -->
    <div class="welcome-header animate-fade-up">
        <div>
            <h1 class="welcome-title">Overview Dashboard</h1>
            <p class="welcome-subtitle">Welcome back, {{ auth()->user()->name ?? 'Admin' }}. Here's what's happening today.</p>
        </div>
        <div>
            <button id="exportDataBtn" class="btn btn-dark fw-medium px-4 py-2" style="border-radius: 10px;" data-export-url="{{ route('admin.export') }}">
                <i class="bi bi-cloud-download me-2"></i>Export Data
            </button>
        </div>
    </div>

    <!-- Financial Critical Alerts -->
    @if(($pendingWithdrawalsCount ?? 0) > 0)
        <div class="alert alert-warning border-0 rounded-4 shadow-sm animate-fade-up delay-1 mb-4 d-flex align-items-center justify-content-between p-3" style="background: linear-gradient(to right, #fffbeb, #fef3c7); border-left: 5px solid #f59e0b !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-warning bg-opacity-25 p-2 rounded-circle text-warning fs-4 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-dark" style="font-size: 1rem;">Action Required: Pending Trainer Withdrawals</h5>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                        You have <strong class="text-dark">{{ $pendingWithdrawalsCount }} withdrawal requests</strong> waiting for verification and payout.
                    </p>
                </div>
            </div>
            <a href="{{ route('admin.finance.index') }}" class="btn btn-warning fw-bold shadow-sm px-4 rounded-pill">
                Review Now
            </a>
        </div>
    @endif

    <!-- KPI Metrics -->
    <div class="row g-3 mb-5 animate-fade-up delay-1">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card kpi-users">
                <div class="kpi-icon"><i class="bi bi-people-fill"></i></div>
                <div class="kpi-content">
                    <div class="kpi-label">Active Users</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <div class="kpi-value" data-active-users>{{ number_format($activeUsers ?? 0) }}</div>
                        @php $val = $activeUsersChangePercent; @endphp
                        @if(!is_null($val))
                            <span class="badge {{ $val > 0 ? 'bg-success-subtle text-success' : ($val < 0 ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary') }} rounded-pill" style="font-size: 0.7rem;">
                                {{ $val > 0 ? '↑' : ($val < 0 ? '↓' : '') }} {{ abs($val) }}%
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card kpi-courses">
                <div class="kpi-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
                <div class="kpi-content">
                    <div class="kpi-label">Total Courses</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <div class="kpi-value">{{ number_format($totalCourses ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card kpi-events">
                <div class="kpi-icon"><i class="bi bi-calendar2-event-fill"></i></div>
                <div class="kpi-content">
                    <div class="kpi-label">Total Events</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <div class="kpi-value">{{ number_format($totalEvents ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="kpi-card kpi-revenue">
                <div class="kpi-icon"><i class="bi bi-wallet2"></i></div>
                <div class="kpi-content">
                    <div class="kpi-label">Total Revenue</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <div class="kpi-value fs-4">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Modules -->
    <div class="animate-fade-up delay-2">
        <h2 class="section-title">Core Management Modules</h2>
        <div class="module-grid">
            
            <a href="{{ route('admin.courses.index') }}" class="module-card mod-course">
                <div class="module-icon"><i class="bi bi-journal-richtext"></i></div>
                <h3 class="module-title">Courses</h3>
                <p class="module-desc">Manage e-learning curriculums, classes, and materials.</p>
            </a>

            <a href="{{ route('admin.add-event') }}" class="module-card mod-event">
                <div class="module-icon"><i class="bi bi-calendar2-event"></i></div>
                <h3 class="module-title">Events</h3>
                <p class="module-desc">Organize webinars, offline training, and participant registrations.</p>
            </a>

            <a href="{{ route('admin.finance.index') }}" class="module-card mod-finance">
                <div class="module-icon"><i class="bi bi-piggy-bank"></i></div>
                <h3 class="module-title">Finance</h3>
                <p class="module-desc">Monitor revenue, process payouts, and manage operational expenses.</p>
            </a>

            <a href="{{ route('admin.crm.dashboard') }}" class="module-card mod-crm">
                <div class="module-icon"><i class="bi bi-headset"></i></div>
                <h3 class="module-title">CRM & Support</h3>
                <p class="module-desc">Handle customer relations, support tickets, and broadcasts.</p>
            </a>

            <a href="{{ route('admin.trainer.index') }}" class="module-card mod-trainer">
                <div class="module-icon"><i class="bi bi-person-badge"></i></div>
                <h3 class="module-title">Trainers</h3>
                <p class="module-desc">Manage instructors, assignments, and teaching performance.</p>
            </a>

            <a href="{{ route('admin.reseller') }}" class="module-card mod-reseller">
                <div class="module-icon"><i class="bi bi-diagram-3"></i></div>
                <h3 class="module-title">Resellers</h3>
                <p class="module-desc">Manage affiliates, commissions, and partnership networks.</p>
            </a>

            <a href="{{ route('admin.carousels.index', ['location' => 'dashboard']) }}" class="module-card mod-content">
                <div class="module-icon"><i class="bi bi-images"></i></div>
                <h3 class="module-title">Content</h3>
                <p class="module-desc">Update homepage banners, carousels, and visual assets.</p>
            </a>

        </div>
    </div>

    <!-- Bottom Panels: Action Items -->
    <div class="row g-4 animate-fade-up delay-3">
        <!-- Pending Materials -->
        <div class="col-12 col-xl-6">
            <div class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title"><i class="bi bi-clock-history text-warning"></i> Pending Material Approvals</h3>
                    <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-link text-decoration-none">View All</a>
                </div>
                <div class="panel-body">
                    @if(($pendingMaterials ?? collect())->count() > 0)
                        @foreach($pendingMaterials as $material)
                            <div class="action-item">
                                <div>
                                    <div class="action-title">{{ $material->name }}</div>
                                    <div class="action-meta">
                                        Trainer: {{ $material->trainer->name ?? 'Anonim' }} • Diunggah: {{ optional($material->created_at)->diffForHumans() }}
                                    </div>
                                </div>
                                <a href="{{ route('admin.trainer.material.show', $material->id) }}" class="btn btn-sm btn-success">
                                    Detail
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2 text-success"></i>
                        <small>No material has been approved yet.</small>
                    </div>
                @endif
                <a href="{{ route('admin.trainer.material.approved') }}" class="btn btn-outline-success w-100 fw-semibold mt-2">
                    View All Approved Materials
                </a>
            </div>
        </div>
        </div>

        <!-- Overdue Assignments -->
        <div class="col-12 col-xl-6">
            <div class="panel-card">
                <div class="panel-header">
                    <h3 class="panel-title"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Pending/Overdue Invitations</h3>
                    <a href="{{ route('admin.trainer.index') }}" class="btn btn-sm btn-link text-decoration-none">Manage Trainers</a>
                </div>
                <div class="panel-body">
                    @if(($overdueAssignmentsPreview ?? collect())->count() > 0)
                        @foreach($overdueAssignmentsPreview as $assignment)
                            <div class="action-item" style="background: #fff5f5; border-color: #fed7d7;">
                                <div>
                                    <div class="action-title text-danger">{{ $assignment['trainer'] }}</div>
                                    <div class="action-meta">
                                        Undangan: {{ $assignment['title'] }} <br>
                                        <span class="text-danger fw-medium"><i class="bi bi-alarm"></i> Tenggat: {{ $assignment['due_at_text'] }}</span>
                                    </div>
                                </div>
                                <a href="{{ route('admin.trainer.index') }}" class="btn btn-sm btn-danger rounded-pill px-3">
                                    Tindak Lanjuti
                                </a>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-shield-check fs-1 d-block mb-2 text-success opacity-50"></i>
                            <small>No pending or overdue trainer invitations.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        initActiveUsersPoll();
        animateCounters();
        initExportButton();
        initRecentActivityRefresh();
    });

    function initActiveUsersPoll() {
        setInterval(function () {
            fetch('{{ route("admin.active-users-count") }}')
                .then(r => r.json())
                .then(data => { if (data.count) { document.querySelector('[data-active-users]').textContent = data.count.toLocaleString('id-ID'); } })
                .catch(() => { });
        }, 30000);
    }

    function animateCounters() {
        const counters = document.querySelectorAll('[data-active-users]');
        counters.forEach(counter => {
            const original = counter.textContent;
            const numeric = parseInt(original.replace(/[^0-9]/g, '')) || 0;
            let current = 0;
            const steps = 40;
            const increment = numeric / steps;
            const timer = setInterval(() => {
                current += increment;
                if (current >= numeric) { current = numeric; clearInterval(timer); }
                counter.textContent = Math.floor(current).toLocaleString('id-ID');
            }, 20);
        });
    }

    function initExportButton() {
        const btn = document.getElementById('exportDataBtn');
        if (!btn) return;
        btn.addEventListener('click', function () {
            const url = btn.getAttribute('data-export-url');
            if (!url) return;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Exporting...';
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            iframe.src = url;
            document.body.appendChild(iframe);
            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                setTimeout(() => iframe.remove(), 60000);
            }, 3000);
        });
    }

</script>
@endsection