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
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #F8FAFC; 
        }

        .main-content { 
            margin-left: 0; 
            transition: margin-left 0.3s ease; 
        }

        .hover-card { 
            transition: transform 0.2s ease, box-shadow 0.2s ease; 
        }

        .hover-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.05)!important; 
        }
    </style>
</head>

<body>

    @include('partials.navbar-admin')

    <main class="main-content min-vh-100">
        <div class="p-4 p-md-5">
            @include('admin.reseller.dashboard')
            @include('admin.reseller.finance')
            @include('admin.reseller.data-reseller')
        </div>
    </main>

    @include('admin.reseller.detail-reseller')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const initialView = @json($activeView ?? 'dashboard');

            // Switch View
            window.switchView = function(viewId) {
                document.querySelectorAll('.view-section').forEach(el => {
                    el.style.display = 'none';
                });

                const target = document.getElementById(viewId + '-view');
                if (target) {
                    target.style.display = 'block';
                }

                const mainLinks = document.querySelectorAll('.admin-main-nav-link');
                mainLinks.forEach(link => {
                    link.classList.toggle('active', link.dataset.view === viewId);
                });
            };

            window.switchView(initialView);

            // Review Modal
            window.openReviewModal = function(name, amount, bank, rek, holder) {

                const reseller = document.getElementById('modalReseller');
                const bankEl = document.getElementById('modalBank');
                const rekening = document.getElementById('modalRekening');
                const holderEl = document.getElementById('modalName');
                const amountEl = document.getElementById('modalAmount');
                const modalEl = document.getElementById('reviewModal');

                if (!modalEl) return;

                if (reseller) reseller.innerText = name;
                if (bankEl) bankEl.innerText = bank;
                if (rekening) rekening.innerText = rek;
                if (holderEl) holderEl.innerText = holder;

                const formatted = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);

                if (amountEl) amountEl.innerText = formatted;

                new bootstrap.Modal(modalEl).show();
            };

            // Detail Reseller Modal
            window.openResellerDetail = function(name, code, earnings, totalRef) {

                const nameEl = document.getElementById('detailResellerName');
                const codeEl = document.getElementById('detailResellerCode');
                const earningsEl = document.getElementById('detailTotalEarnings');
                const totalRefEl = document.getElementById('detailTotalRef');
                const modalEl = document.getElementById('detailResellerModal');

                if (!modalEl) return;

                if (nameEl) nameEl.innerText = name;
                if (codeEl) codeEl.innerText = code;
                if (earningsEl) earningsEl.innerText = earnings;
                if (totalRefEl) totalRefEl.innerText = totalRef;

                new bootstrap.Modal(modalEl).show();
            };

            // Initialize Chart
            const ctx = document.getElementById('dashboardChart');

            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                        datasets: [{
                            label: 'Reseller Baru',
                            data: [2, 5, 3, 8, 4, 10, 6],
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { borderDash: [5, 5] } 
                            },
                            x: { 
                                grid: { display: false } 
                            }
                        }
                    }
                });
            }

        });
    </script>

</body>
</html>