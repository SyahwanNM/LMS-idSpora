@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .dashboard-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        overflow: hidden;
        position: relative;
    }
    
    .dashboard-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--gradient-start), var(--gradient-end));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12) !important;
    }
    
    .dashboard-card:hover::before {
        transform: scaleX(1);
    }
    
    .kpi-icon {
        transition: all 0.3s ease;
    }
    
    .dashboard-card:hover .kpi-icon {
        transform: scale(1.1) rotate(5deg);
    }
    
    .quick-action-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent !important;
        position: relative;
        overflow: hidden;
    }
    
    .quick-action-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .quick-action-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .quick-action-btn:hover {
        transform: translateY(-5px);
        border-color: transparent !important;
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .quick-action-btn i {
        transition: transform 0.3s ease;
    }
    
    .quick-action-btn:hover i {
        transform: scale(1.2);
    }
    
    .activity-item {
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px;
    }
    
    .activity-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }
    
    .fade-in {
        animation: fadeInUp 0.6s ease-out;
    }
    
    .fade-in-delay-1 { animation-delay: 0.1s; }
    .fade-in-delay-2 { animation-delay: 0.2s; }
    .fade-in-delay-3 { animation-delay: 0.3s; }
    .fade-in-delay-4 { animation-delay: 0.4s; }
    
    .kpi-card-1 { --gradient-start: #fbbf24; --gradient-end: #f59e0b; }
    .kpi-card-2 { --gradient-start: #667eea; --gradient-end: #764ba2; }
    .kpi-card-3 { --gradient-start: #fbbf24; --gradient-end: #f59e0b; }
    .kpi-card-4 { --gradient-start: #667eea; --gradient-end: #764ba2; }
    
    .kpi-icon-wrapper {
        background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    
    .welcome-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #fbbf24 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 8px 30px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .welcome-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.2) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    
    .quick-action-btn.btn-outline-warning {
        border-color: transparent !important;
        color: #f59e0b;
    }
    
    .quick-action-btn.btn-outline-warning:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
        border-color: transparent !important;
    }
    
    .quick-action-btn.btn-outline-primary {
        border-color: transparent !important;
    }
    
    .quick-action-btn.btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent !important;
    }
    
    .quick-action-btn.btn-outline-purple {
        border-color: transparent !important;
        color: #667eea;
    }
    
    .quick-action-btn.btn-outline-purple:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent !important;
    }
    
    .quick-action-btn.btn-outline-info,
    .quick-action-btn.btn-outline-success,
    .quick-action-btn.btn-outline-danger,
    .quick-action-btn.btn-outline-secondary {
        border-color: transparent !important;
    }
    
    .quick-action-btn.btn-outline-info:hover,
    .quick-action-btn.btn-outline-success:hover,
    .quick-action-btn.btn-outline-danger:hover,
    .quick-action-btn.btn-outline-secondary:hover {
        border-color: transparent !important;
    }
</style>

<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="welcome-header fade-in mb-4" style="position: relative; z-index: 1;">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="position: relative; z-index: 2;">
            <div>
                <h3 class="mb-2 fw-bold">
                    <i class="bi bi-speedometer2 me-2"></i>Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}!
                </h3>
                <p class="mb-0 opacity-95">Kelola dan pantau aktivitas sistem dari dashboard ini</p>
            </div>
            <button id="exportDataBtn" class="btn btn-light btn-lg shadow-lg mt-2 mt-md-0 fw-semibold" type="button" data-export-url="{{ route('admin.export') }}" style="background: rgba(255,255,255,0.95); border: none;">
                <i class="bi bi-download me-2"></i>Export Data
            </button>
        </div>
    </div>

    <!-- KPI Row -->
    <div class="row g-4 mb-4">
                        <div class="col-6 col-md-3 fade-in">
            <div class="card h-100 dashboard-card shadow-sm rounded-4 kpi-card-1">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="kpi-icon-wrapper rounded-4 p-3 text-white me-3 kpi-icon d-flex align-items-center justify-content-center" style="width:64px;height:64px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                            <i class="bi bi-people-fill fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-muted fw-semibold mb-2 text-uppercase" style="letter-spacing: 0.5px;">Active Users</div>
                            <div class="d-flex align-items-baseline flex-wrap">
                                <div class="display-6 fs-2 fw-bold text-dark" data-active-users>{{ number_format($activeUsers ?? 0) }}</div>
                                @php $val = $activeUsersChangePercent; @endphp
                                <div class="ms-2 small fw-bold @if(is_null($val)) text-secondary @elseif($val>0) text-success @elseif($val<0) text-danger @else text-muted @endif" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
                                    @if(!is_null($val))
                                        @if($val>0)<i class="bi bi-arrow-up-short"></i>@elseif($val<0)<i class="bi bi-arrow-down-short"></i>@else <i class="bi bi-dash"></i>@endif
                                        {{ $val>0?'+':'' }}{{ $val }}%
                                    @else — @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 fade-in fade-in-delay-1">
            <div class="card h-100 dashboard-card shadow-sm rounded-4 kpi-card-2">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="kpi-icon-wrapper rounded-4 p-3 text-white me-3 kpi-icon d-flex align-items-center justify-content-center" style="width:64px;height:64px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-journal-bookmark-fill fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-muted fw-semibold mb-2 text-uppercase" style="letter-spacing: 0.5px;">Total Courses</div>
                            <div class="d-flex align-items-baseline flex-wrap">
                                <div class="display-6 fs-2 fw-bold text-dark">{{ number_format($totalCourses ?? 0) }}</div>
                                @php $val = $totalCoursesChangePercent; @endphp
                                <div class="ms-2 small fw-bold @if(is_null($val)) text-secondary @elseif($val>0) text-success @elseif($val<0) text-danger @else text-muted @endif" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
                                    @if(!is_null($val))
                                        @if($val>0)<i class="bi bi-arrow-up-short"></i>@elseif($val<0)<i class="bi bi-arrow-down-short"></i>@else <i class="bi bi-dash"></i>@endif
                                        {{ $val>0?'+':'' }}{{ $val }}%
                                    @else — @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 fade-in fade-in-delay-2">
            <div class="card h-100 dashboard-card shadow-sm rounded-4 kpi-card-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="kpi-icon-wrapper rounded-4 p-3 text-white me-3 kpi-icon d-flex align-items-center justify-content-center" style="width:64px;height:64px; background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                            <i class="bi bi-calendar-event-fill fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-muted fw-semibold mb-2 text-uppercase" style="letter-spacing: 0.5px;">Total Events</div>
                            <div class="d-flex align-items-baseline flex-wrap">
                                <div class="display-6 fs-2 fw-bold text-dark">{{ number_format($totalEvents ?? 0) }}</div>
                                @php $val = $totalEventsChangePercent; @endphp
                                <div class="ms-2 small fw-bold @if(is_null($val)) text-secondary @elseif($val>0) text-success @elseif($val<0) text-danger @else text-muted @endif" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
                                    @if(!is_null($val))
                                        @if($val>0)<i class="bi bi-arrow-up-short"></i>@elseif($val<0)<i class="bi bi-arrow-down-short"></i>@else <i class="bi bi-dash"></i>@endif
                                        {{ $val>0?'+':'' }}{{ $val }}%
                                    @else — @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3 fade-in fade-in-delay-3">
            <div class="card h-100 dashboard-card shadow-sm rounded-4 kpi-card-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="kpi-icon-wrapper rounded-4 p-3 text-white me-3 kpi-icon d-flex align-items-center justify-content-center" style="width:64px;height:64px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="bi bi-currency-dollar fs-3"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-muted fw-semibold mb-2 text-uppercase" style="letter-spacing: 0.5px;">Total Revenue</div>
                            <div class="d-flex align-items-baseline flex-wrap">
                                <div class="display-6 fs-5 fw-bold text-dark">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                                @php $val = $totalRevenueChangePercent; @endphp
                                <div class="ms-2 small fw-bold @if(is_null($val)) text-secondary @elseif($val>0) text-success @elseif($val<0) text-danger @else text-muted @endif" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
                                    @if(!is_null($val))
                                        @if($val>0)<i class="bi bi-arrow-up-short"></i>@elseif($val<0)<i class="bi bi-arrow-down-short"></i>@else <i class="bi bi-dash"></i>@endif
                                        {{ $val>0?'+':'' }}{{ $val }}%
                                    @else — @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Baseline Legend -->
    <div class="text-end mb-4 fade-in fade-in-delay-4">
        @if(isset($usingIntraDayBaseline) && $usingIntraDayBaseline)
            <span class="badge bg-warning bg-opacity-20 text-dark border border-warning fw-normal px-3 py-2" style="border-width: 2px !important;">
                <i class="bi bi-info-circle me-1"></i>Persentase dibanding awal hari ini
            </span>
        @else
            <span class="badge bg-warning bg-opacity-20 text-dark border border-warning fw-normal px-3 py-2" style="border-width: 2px !important;">
                <i class="bi bi-info-circle me-1"></i>Persentase dibanding kemarin
            </span>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8 fade-in fade-in-delay-4">
            <div class="card shadow-sm border-0 rounded-4 mb-4" style="border-top: 4px solid #fbbf24 !important;">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-4">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-lightning-charge-fill text-warning me-2" style="filter: drop-shadow(0 2px 4px rgba(251, 191, 36, 0.3));"></i>Quick Actions
                    </h5>
                    <span class="badge bg-warning bg-opacity-20 text-dark border border-warning px-3 py-2 fw-semibold" style="border-width: 2px !important;">
                        <i class="bi bi-star-fill me-1"></i>Akses Cepat
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 quick-action-btn btn-outline-primary d-flex flex-column align-items-center py-4 rounded-4" onclick="location.href='{{ route('admin.reseller') }}'">
                                <i class="bi bi-people-fill fs-3 mb-2"></i>
                                <small class="fw-semibold">Manage Reseller</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 quick-action-btn btn-outline-purple d-flex flex-column align-items-center py-4 rounded-4" onclick="location.href='#'">
                                <i class="bi bi-person-badge-fill fs-3 mb-2"></i>
                                <small class="fw-semibold">Manage Trainer</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 quick-action-btn btn-outline-warning d-flex flex-column align-items-center py-4 rounded-4" onclick="location.href='{{ route('admin.courses.index') }}'">
                                <i class="bi bi-journal-text fs-3 mb-2"></i>
                                <small class="fw-semibold">Manage Courses</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 quick-action-btn btn-outline-info d-flex flex-column align-items-center py-4 rounded-4" onclick="location.href='{{ route('admin.add-event') }}'">
                                <i class="bi bi-calendar2-event fs-3 mb-2"></i>
                                <small class="fw-semibold">Manage Events</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 quick-action-btn btn-outline-success d-flex flex-column align-items-center py-4 rounded-4" onclick="location.href='{{ route('admin.finance.index') }}'">
                                <i class="bi bi-cash-stack fs-3 mb-2"></i>
                                <small class="fw-semibold">Manage Finance</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 quick-action-btn btn-outline-danger d-flex flex-column align-items-center py-4 rounded-4" onclick="location.href='{{ route('admin.crm.dashboard') }}'">
                                <i class="bi bi-diagram-3 fs-3 mb-2"></i>
                                <small class="fw-semibold">CRM</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 quick-action-btn btn-outline-secondary d-flex flex-column align-items-center py-4 rounded-4" onclick="location.href='{{ route('admin.carousels.index', ['location' => 'dashboard']) }}'">
                                <i class="bi bi-images fs-3 mb-2"></i>
                                <small class="fw-semibold">Manage Carousel</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4 fade-in fade-in-delay-4">
            <div class="card shadow-sm border-0 rounded-4 h-100" style="border-top: 4px solid #667eea !important;">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-4">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-clock-history text-warning me-2" style="filter: drop-shadow(0 2px 4px rgba(102, 126, 234, 0.3));"></i>Recent Activity
                    </h5>
                    <button id="refreshRecentBtn" class="btn btn-sm btn-outline-warning rounded-circle" type="button" title="Refresh list" style="width: 36px; height: 36px; border-color: #fbbf24;">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="recentActivityList" style="max-height:320px; overflow-y:auto; padding-right: 8px;">
                    @php $list = collect($recentActivities ?? [])->take(4); @endphp
                    @if($list->isNotEmpty())
                        @foreach($list as $index => $activity)
                            <div class="activity-item mb-3">
                                <div class="d-flex">
                                    <img src="{{ $activity['avatar'] }}" alt="{{ $activity['user'] }}" class="rounded-circle flex-shrink-0 shadow-sm" style="width:48px;height:48px;object-fit:cover; border: 2px solid #e9ecef;">
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-semibold text-dark mb-1">{{ $activity['user'] }}</div>
                                        <div class="small text-muted mb-1">{{ $activity['action'] }}</div>
                                        @if(!empty($activity['description']))
                                            <div class="small text-secondary mb-2">{{ $activity['description'] }}</div>
                                        @endif
                                        <div class="small text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $activity['time'] }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($index < $list->count()-1)<hr class="my-3 border-light opacity-25">@endif
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                            <small>No recent activity</small>
                        </div>
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initActiveUsersPoll();
    animateCounters();
    showFlashMessages();
    initExportButton();
    initRecentActivityRefresh();
});

function initActiveUsersPoll() {
    setInterval(function() {
        fetch('{{ route("admin.active-users-count") }}')
            .then(r => r.json())
            .then(data => { if (data.count) { document.querySelector('[data-active-users]').textContent = data.count.toLocaleString('id-ID'); } })
            .catch(() => {});
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

function showFlashMessages() {
    @php($loginSuccess = session()->pull('login_success'))
    @if(!empty($loginSuccess))
        createToast('success', `{{ addslashes($loginSuccess) }}`);
    @endif
    @if($errors->any())
        createToast('error', 'Please check the form for errors');
    @endif
}

function initExportButton(){
    const btn = document.getElementById('exportDataBtn');
    if(!btn) return;
    btn.addEventListener('click', function(){
        const url = btn.getAttribute('data-export-url');
        if(!url) return;
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Mengunduh...';
        const iframe = document.createElement('iframe');
        iframe.style.display='none';
        iframe.src = url;
        document.body.appendChild(iframe);
        setTimeout(()=>{
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            setTimeout(()=> iframe.remove(), 60000);
        }, 3000);
    });
}

function initRecentActivityRefresh(){
    const btn = document.getElementById('refreshRecentBtn');
    const listEl = document.getElementById('recentActivityList');
    if(!btn || !listEl) return;
    btn.addEventListener('click', function(){
        const original = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        fetch('{{ route("admin.recent-activities") }}?limit=8')
            .then(r => r.json())
            .then(json => {
                btn.disabled = false;
                btn.innerHTML = original;
                const data = (json.data || []).slice(0,4);
                if(!data.length){
                    listEl.innerHTML = '<div class="text-center py-4 text-muted small">No recent activity</div>';
                    return;
                }
                // build HTML
                let html = '';
                data.forEach((a, idx) => {
                    html += `<div class="d-flex mb-3">`;
                    html += `<img src="${a.avatar}" alt="${escapeHtml(a.user)}" class="rounded-circle flex-shrink-0" style="width:48px;height:48px;object-fit:cover;">`;
                    html += `<div class="ms-3 flex-grow-1"><div class="fw-semibold text-dark">${escapeHtml(a.user)}</div>`;
                    html += `<div class="small text-muted">${escapeHtml(a.action)}</div>`;
                    if(a.description) html += `<div class="small text-secondary">${escapeHtml(a.description)}</div>`;
                    html += `<div class="small text-muted mt-1"><i class="bi bi-clock me-1"></i>${escapeHtml(a.time)}</div></div></div>`;
                    if(idx < data.length-1) html += '<hr class="my-2 border-light">';
                });
                listEl.innerHTML = html;
            })
            .catch(()=>{
                btn.disabled = false;
                btn.innerHTML = original;
                // keep current list
            });
    });
}

function escapeHtml(str){
    if(!str) return '';
    return String(str).replace(/[&<>"'`]/g, function(s){
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','`':'&#96;'})[s];
    });
}

function createToast(type, message) {
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-white border-0 position-fixed top-0 end-0 m-3';
    toast.setAttribute('role','alert');
    toast.style.zIndex = '1080';
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
    toast.classList.add(type==='success'?'bg-success':'bg-danger');
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    toast.addEventListener('hidden.bs.toast', ()=> toast.remove());
}
</script>
@endsection