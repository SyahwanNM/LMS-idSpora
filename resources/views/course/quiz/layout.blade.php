@include ('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quiz')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .quiz-layout {
            display: flex;
            min-height: 100vh;
        }

        /* --- Kartu Pertanyaan --- */
        .question-card {
            background: var(--bg-light);
            border-radius: 15px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px var(--shadow-light);
            border: 1px solid var(--border-light);
            border-left: 5px solid var(--secondary);
        }

        .question-card .question-text {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 20px;
        }

        /* --- Custom Radio Button --- */
        .answer-options .form-check {
            margin-bottom: 15px;
        }

        .answer-options .form-check-input {
            display: none;
        }

        .answer-options .form-check-label {
            position: relative;
            padding-left: 35px;
            cursor: pointer;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .answer-options .form-check-label::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid var(--border-light);
            background: var(--bg-light);
            transition: border-color 0.2s ease;
        }

        .answer-options .form-check-label::after {
            content: '';
            position: absolute;
            left: 6px;
            top: 50%;
            transform: translateY(-50%) scale(0);
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--secondary);
            transition: transform 0.2s ease;
        }

        .answer-options .form-check-input:checked+.form-check-label::before {
            border-color: var(--secondary);
        }

        .answer-options .form-check-input:checked+.form-check-label::after {
            transform: translateY(-50%) scale(1);
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
            list-style: none;
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

        .sidebar-item[open]>summary::after {
            transform: rotate(180deg);
        }

        .sidebar-item .dropdown-content {
            padding: 10px 15px;
        }

        .quiz-main-content {
            flex-grow: 1;
            padding: 40px;
            overflow-y: auto;
        }

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
            @yield('content')
        </main>
    </div>
</body>

</html>
@include('partials.footer-after-login')