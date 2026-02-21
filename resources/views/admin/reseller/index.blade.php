@include('partials.navbar-admin')
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - IdSpora</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* CSS CONFIG & ANIMASI */
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; }
        .sidebar-desktop { width: 280px; height: 100vh; position: fixed; overflow-y: auto; border-right: 1px solid #e9ecef; background: white; }
        .main-content { margin-left: 280px; transition: margin-left 0.3s ease; }
        @media (max-width: 992px) { .sidebar-desktop { display: none; } .main-content { margin-left: 0; } }
        .hover-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-card:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.05)!important; }
        .sidebar.active { background-color: #FEF3C7; color: #B45309; font-weight: 600; }
        .sidebar { color: #64748B; transition: all 0.2s ease; text-decoration: none; }
        .sidebar:hover { background-color: #FFFBEB; color: #B45309; }
    </style>
</head>

<body>
    @include('admin.reseller.sidebar')

    <main class="main-content min-vh-100">
        <nav class="navbar bg-white border-bottom sticky-top px-4 py-3 d-lg-none">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light border" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="fw-bold">Admin Panel</span>
            </div>
        </nav>

        <div class="p-4 p-md-5">
            @include('admin.reseller.dashboard')

            @include('admin.reseller.finance')

            @include('admin.reseller.data-reseller')
        </div>
    </main>

    @include('admin.reseller.detail-reseller')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function switchView(viewId, navElement) {
            document.querySelectorAll('.view-section').forEach(el => el.style.display = 'none');
            document.getElementById(viewId + '-view').style.display = 'block';

            if (navElement) {
                document.querySelectorAll('.sidebar-desktop .sidebar').forEach(el => el.classList.remove('active'));
                navElement.classList.add('active');
            }
        }

        function closeOffcanvas() {
            const offcanvasEl = document.getElementById('mobileSidebar');
            const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
            if (bsOffcanvas) bsOffcanvas.hide();
        }

        function openReviewModal(name, amount, bank, rek, holder) {
            document.getElementById('modalReseller').innerText = name;
            document.getElementById('modalBank').innerText = bank;
            document.getElementById('modalRekening').innerText = rek;
            document.getElementById('modalName').innerText = holder;
            
            const formatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
            document.getElementById('modalAmount').innerText = formatted;

            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        }

        // Logic memunculkan Modal Detail Reseller
        function openResellerDetail(name, code, earnings, totalRef) {
            document.getElementById('detailResellerName').innerText = name;
            document.getElementById('detailResellerCode').innerText = code;
            document.getElementById('detailTotalEarnings').innerText = earnings;
            document.getElementById('detailTotalRef').innerText = totalRef;
            
            new bootstrap.Modal(document.getElementById('detailResellerModal')).show();
        }

        // Initialize Chart
        const ctx = document.getElementById('dashboardChart');
        if(ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        label: 'Reseller Baru',
                        data: [2, 5, 3, 8, 4, 10, 6],
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true, tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { borderDash: [5, 5] } }, x: { grid: { display: false } } } }
            });
        }
    </script>
</body>
</html>