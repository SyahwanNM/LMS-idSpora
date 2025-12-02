@include ('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Rules</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        .quiz-layout {
            display: flex;
            min-height: 100vh;
        }
        
        .quiz-nav-sidebar {
            width: 280px;
            background-color: #eef2ff; 
            padding: 20px;
            border-right: 1px solid var(--border-light);
            flex-shrink: 0;
        }

        .sidebar-item summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            background-color: var(--bg-light);
            border-radius: 8px;
            font-weight: 500;
            color: var(--text-primary);
            cursor: pointer;
            list-style: none; /* Hilangkan panah default */
            transition: background-color 0.2s ease;
        }
        
        .sidebar-item summary::-webkit-details-marker {
            display: none;
        }

        .sidebar-item summary:hover {
            background-color: #f9fafb;
        }
        
        .sidebar-item summary::after {
            font-size: 0.8em;
            transition: transform 0.3s ease;
        }

        .sidebar-item[open] > summary::after {
            transform: rotate(180deg);
        }
        
        .sidebar-item .dropdown-content {
            padding: 10px 15px;
        }

        /* --- Konten Utama Kanan --- */
        .quiz-main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }

        .content-card {
            background: var(--bg-light);
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 40px;
            box-shadow: 0 4px 12px var(--shadow-medium);
            border-top: 4px solid var(--secondary); /* Garis kuning di atas */
        }
        
        .content-card h2 {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 24px;
        }
        
        .content-card p, .content-card li {
            color: var(--text-secondary);
            line-height: 1.7;
        }

        .btn-start {
            background-color: var(--secondary);
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 18px;
            padding: 12px 30px;
            border-radius: 50px; /* Membuat tombol lebih oval */
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(244, 196, 48, 0.4);
        }

        /* --- Tabel Riwayat --- */
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }

        .history-table th, .history-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        .history-table thead th {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 14px;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 12px;
        }

        .status-passed {
            background-color: #ecfdf5;
            color: var(--success);
        }

        .status-failed {
            background-color: #fef2f2;
            color: var(--danger);
        }
        
        .btn-details {
            background-color: #f3f4f6;
            color: var(--text-secondary);
            border: none;
            padding: 6px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }
        
        .btn-details:hover {
            background-color: #e5e7eb;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .quiz-layout {
                flex-direction: column;
            }
            .quiz-nav-sidebar {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--border-light);
            }
            .quiz-main-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<div class="quiz-layout">
    <aside class="quiz-nav-sidebar">
        <ul class="nav flex-column gap-3">
            <li class="nav-item">
                <details class="sidebar-item">
                    <summary>Introduction Android Studio <i class="bi bi-chevron-down"></i></summary>
                    <div class="dropdown-content">
                        <p class="fs-sm text-muted">Konten dropdown di sini.</p>
                    </div>
                </details>
            </li>
            <li class="nav-item">
                <details class="sidebar-item">
                    <summary>Layouting & <i class="bi bi-chevron-down"></i></summary>
                    <div class="dropdown-content">
                        <p class="fs-sm text-muted">Konten dropdown di sini.</p>
                    </div>
                </details>
            </li>
             <li class="nav-item">
                <details class="sidebar-item">
                    <summary>Activity & Intent <i class="bi bi-chevron-down"></i></summary>
                    <div class="dropdown-content">
                        <p class="fs-sm text-muted">Konten dropdown di sini.</p>
                    </div>
                </details>
            </li>
        </ul>
    </aside>

    <main class="quiz-main-content">
        <div class="content-card">
            <h2>Aturan Kuis</h2>
            <p>
                Kuis ini bertujuan untuk mengukur pemahaman Anda terhadap materi Android Studio.
            </p>
            <p>
                Silakan perhatikan ketentuan berikut sebelum memulai:
            </p>
            <ol class="ps-3 mb-4">
                <li><strong>Jumlah Soal:</strong> 5 pertanyaan pilihan ganda.</li>
                <li><strong>Durasi Pengerjaan:</strong> 10 menit.</li>
                <li><strong>Nilai Kelulusan:</strong> Minimum 75% untuk dinyatakan lulus.</li>
                <li>Jika belum mencapai nilai kelulusan, Anda dapat mengulang kuis setelah 2 menit. Gunakan waktu tersebut untuk mempelajari kembali materi sebelumnya.</li>
                <li>Pastikan Anda menjawab semua pertanyaan sebelum waktu habis.</li>
            </ol>
            <p>
                Selamat mengerjakan dan semoga sukses!
            </p>
            <div class="text-end mt-5">
                <a href="#" class="btn-start">
                    Start
                    <i class="bi bi-arrow-right-short"></i>
                </a>
            </div>
        </div>

        <div class="quiz-history">
            <h3 class="mb-4 fw-bold">Riwayat</h3>
            <div class="table-responsive">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Persentase</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>06 Oct 2025 08:35</td>
                            <td>0%</td>
                            <td><span class="status-badge status-failed">Not Pass</span></td>
                            <td><button class="btn-details">see details</button></td>
                        </tr>
                        <tr>
                            <td>06 Oct 2025 08:35</td>
                            <td>90%</td>
                            <td><span class="status-badge status-passed">Passed</span></td>
                            <td><button class="btn-details">see details</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

</body>
</html>