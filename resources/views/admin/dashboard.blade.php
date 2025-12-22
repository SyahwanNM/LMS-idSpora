@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0 fw-semibold text-dark">Dashboard</h1>
        <button id="exportDataBtn" class="btn btn-sm btn-outline-primary" type="button" data-export-url="{{ route('admin.export') }}">
            <i class="bi bi-download me-1"></i> Export Data
        </button>
    </div>

    <!-- KPI Row -->
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="rounded-3 bg-warning bg-gradient p-3 text-white me-3 d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-muted fw-medium">Active Users</div>
                            <div class="d-flex align-items-baseline">
                                <div class="display-6 fs-3 fw-bold" data-active-users>{{ number_format($activeUsers ?? 0) }}</div>
                                @php $val = $activeUsersChangePercent; @endphp
                                <div class="ms-2 small fw-semibold @if(is_null($val)) text-secondary @elseif($val>0) text-success @elseif($val<0) text-danger @else text-muted @endif" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
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
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="rounded-3 bg-warning bg-gradient p-3 text-white me-3 d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                            <i class="bi bi-journal-bookmark-fill fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-muted fw-medium">Total Courses</div>
                            <div class="d-flex align-items-baseline">
                                <div class="display-6 fs-3 fw-bold">{{ number_format($totalCourses ?? 0) }}</div>
                                @php $val = $totalCoursesChangePercent; @endphp
                                <div class="ms-2 small fw-semibold @if(is_null($val)) text-secondary @elseif($val>0) text-success @elseif($val<0) text-danger @else text-muted @endif" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
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
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="rounded-3 bg-warning bg-gradient p-3 text-white me-3 d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                            <i class="bi bi-calendar-event-fill fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-muted fw-medium">Total Events</div>
                            <div class="d-flex align-items-baseline">
                                <div class="display-6 fs-3 fw-bold">{{ number_format($totalEvents ?? 0) }}</div>
                                @php $val = $totalEventsChangePercent; @endphp
                                <div class="ms-2 small fw-semibold @if(is_null($val)) text-secondary @elseif($val>0) text-success @elseif($val<0) text-danger @else text-muted @endif" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
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
        <div class="col-6 col-md-3">
            <div class="card h-100 shadow-sm border-0 rounded-3">
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="rounded-3 bg-warning bg-gradient p-3 text-white me-3 d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="small text-muted fw-medium">Total Revenue</div>
                            <div class="d-flex align-items-baseline">
                                <div class="display-6 fs-5 fw-bold">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</div>
                                @php $val = $totalRevenueChangePercent; @endphp
                                <div class="ms-2 small fw-semibold @if(is_null($val)) text-secondary @elseif($val>0) text-success @elseif($val<0) text-danger @else text-muted @endif" title="{{ isset($usingIntraDayBaseline)&&$usingIntraDayBaseline && !is_null($val) ? 'Perubahan sejak awal hari ini' : 'Perubahan dibanding kemarin' }}">
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
    <div class="text-end mb-4">
        @if(isset($usingIntraDayBaseline) && $usingIntraDayBaseline)
            <span class="badge bg-warning text-dark fw-normal">Persentase dibanding awal hari ini</span>
        @else
            <span class="badge bg-secondary fw-normal">Persentase dibanding kemarin</span>
        @endif
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-8">
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                    <span class="text-warning"><i class="bi bi-lightning-fill"></i></span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 btn-outline-primary d-flex flex-column align-items-center py-3" onclick="location.href='#'">
                                <i class="bi bi-people-fill fs-4 mb-1"></i>
                                <small class="fw-semibold">Manage Reseller</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 btn-outline-purple d-flex flex-column align-items-center py-3" onclick="location.href='#'">
                                <i class="bi bi-person-badge-fill fs-4 mb-1"></i>
                                <small class="fw-semibold">Manage Trainer</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 btn-outline-warning d-flex flex-column align-items-center py-3" onclick="location.href='{{ route('admin.courses.index') }}'">
                                <i class="bi bi-journal-text fs-4 mb-1"></i>
                                <small class="fw-semibold">Manage Courses</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 btn-outline-info d-flex flex-column align-items-center py-3" onclick="location.href='{{ route('admin.add-event') }}'">
                                <i class="bi bi-calendar2-event fs-4 mb-1"></i>
                                <small class="fw-semibold">Manage Events</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 btn-outline-success d-flex flex-column align-items-center py-3" onclick="location.href='#'">
                                <i class="bi bi-cash-stack fs-4 mb-1"></i>
                                <small class="fw-semibold">Manage Finance</small>
                            </button>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button" class="btn w-100 btn-outline-danger d-flex flex-column align-items-center py-3" onclick="location.href='{{ route('admin.crm.dashboard') }}'">
                                <i class="bi bi-diagram-3 fs-4 mb-1"></i>
                                <small class="fw-semibold">CRM</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-4">
            <div class="card shadow-sm border-0 rounded-3 h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                    <div class="d-flex align-items-center">
                        <button id="refreshRecentBtn" class="btn btn-sm btn-outline-secondary me-2" type="button" title="Refresh list">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <i class="bi bi-clock-history text-warning"></i>
                    </div>
                </div>
                <div class="card-body">
                    <div id="recentActivityList" style="max-height:260px; overflow-y:auto;">
                    @php $list = collect($recentActivities ?? [])->take(4); @endphp
                    @if($list->isNotEmpty())
                        @foreach($list as $index => $activity)
                            <div class="d-flex mb-3">
                                <img src="{{ $activity['avatar'] }}" alt="{{ $activity['user'] }}" class="rounded-circle flex-shrink-0" style="width:48px;height:48px;object-fit:cover;">
                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-semibold text-dark">{{ $activity['user'] }}</div>
                                    <div class="small text-muted">{{ $activity['action'] }}</div>
                                    @if(!empty($activity['description']))
                                        <div class="small text-secondary">{{ $activity['description'] }}</div>
                                    @endif
                                    <div class="small text-muted mt-1"><i class="bi bi-clock me-1"></i>{{ $activity['time'] }}</div>
                                </div>
                            </div>
                            @if($index < $list->count()-1)<hr class="my-2 border-light">@endif
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted small">No recent activity</div>
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