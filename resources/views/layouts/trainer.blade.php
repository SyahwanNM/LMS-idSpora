<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trainer Area') - idSpora</title>

    <script>
        (function () {
            try {
                const sidebarState = localStorage.getItem('sidebar-state');
                const legacyCollapsed = localStorage.getItem('trainerSidebarCollapsed') === '1';

                if (sidebarState === 'closed' || (!sidebarState && legacyCollapsed)) {
                    document.documentElement.classList.add('sidebar-collapsed');
                }
            } catch (e) {
            }
        })();
    </script>

    @vite([
        'resources/css/app.css',
    ])
    <style>
        /* Import Bootstrap */
        @import "bootstrap/dist/css/bootstrap.min.css";

        /* Import Google Fonts */
        @import url("https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;700&display=swap");

        /* CSS Variables - Global untuk diakses di .trainer-page */
        :root {
            /* Colors */
            --base-clr: #f8fafc;
            --white-clr: #ffffff;
            --line-clr: #f1f5f9;
            --hover-clr: rgba(37, 35, 70, 0.9);
            --main-text-clr: rgb(71 85 105 / 1);
            --text-clr: #64748b;
            --main-navy-clr: #2e2050;
            --navy-dark: #19102c;
            --click-clr: #252346;
            --blue-background-clr: rgb(238 242 255 / 0.3);
            --yellow-background-clr: rgb(254 249 231 / 0.3);
            --secondary-text-clr: #b0b3c1;
            --yellow-clr: rgb(251 197 49);
            --accent-yellow: #fbb034;
            --accent-yellow-star: #f5c542;
            --accent-blue: #6366f1;
            --gray-clr: rgb(100 116 139 / 1);
            --gray-second-clr: rgb(148 163 184 / 1);
            --purple-dark: #2f1f4f;
            --purple-text: #2b2350;
            --text-secondary: #7d98b3;
            --text-muted: #95a4b7;
            --light-border: #eef2f7;
            --gray-light: #9aa8bd;
            --dark-text-clr: #1a1335;
            --navy-gradient-start: #51376c;
            --navy-gradient-alt: #3f2a54;
            --indigo-clr: #4f46e5;
            --indigo-light: #224231255;
            --amber-clr: #d59a10;
            --success-clr: #20b386;
            --success-bg: #d1fae5;
            --error-clr: #dc2626;
            --error-bg: #fecaca;
            --warning-bg: #fef3c7;

            /* Spacing */
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 12px;
            --spacing-lg: 16px;
            --spacing-xl: 20px;
            --spacing-2xl: 24px;
            --spacing-3xl: 28px;
            --spacing-4xl: 32px;
            --spacing-5xl: 40px;

            /* Typography */
            --font-family-base:
                "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
                Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
            --font-family-mono: "Fira Code", monospace;
            --line-height-tight: 1.2;
            --line-height-normal: 1.6;
            --line-height-relaxed: 1.8;

            /* Font Sizes */
            --font-size-xs: 10px;
            --font-size-sm: 12px;
            --font-size-base: 14px;
            --font-size-lg: 16px;
            --font-size-xl: 18px;
            --font-size-2xl: 20px;
            --font-size-3xl: 24px;
            --font-size-4xl: 28px;
            --font-size-5xl: 32px;
            --font-size-6xl: 36px;

            /* Border Radius */
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 10px;
            --radius-xl: 12px;
            --radius-2xl: 16px;

            /* Shadows */
            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 4px 8px rgba(0, 0, 0, 0.08);
            --shadow-xl: 0 10px 24px rgba(0, 0, 0, 0.12);

            /* Responsive Breakpoints */
            --breakpoint-sm: 600px;
            --breakpoint-md: 768px;
            --breakpoint-lg: 1024px;
            --breakpoint-xl: 1280px;
        }

        /* Scoped Reset untuk .trainer-page */
        .trainer-page,
        .trainer-page *,
        .trainer-page *::before,
        .trainer-page *::after {
            box-sizing: border-box;
        }

        .trainer-page * {
            margin: 0;
            padding: 0;
        }

        /* Base Styles - Scoped ke .trainer-page */
        .trainer-page {
            font-family: var(--font-family-base);
            line-height: var(--line-height-normal);
            font-size: var(--font-size-base);
            min-height: calc(100vh - 60px);
            background-color: var(--base-clr);
            color: var(--text-clr);
            display: grid;
            grid-template-columns: 1fr;
            margin: 0;
            padding: 0;
        }

        /* Main Content Styles */
        .trainer-page .main-content {
            padding: var(--spacing-4xl);
            overflow-y: auto;
            max-width: 100%;
            margin: 0;
            width: 100%;
        }

        .trainer-page .dashboard-content {
            padding: var(--spacing-4xl);
            overflow-y: auto;
            width: 100%;
            max-width: 100%;
            min-width: 0;
            margin: 0;
        }

        /* Typography Defaults */
        .trainer-page h1 {
            font-size: var(--font-size-6xl);
            font-weight: 800;
            line-height: var(--line-height-tight);
            margin: 0 0 var(--spacing-lg) 0;
        }

        .trainer-page h2 {
            font-size: var(--font-size-4xl);
            font-weight: 800;
            line-height: var(--line-height-tight);
            margin: 0 0 var(--spacing-md) 0;
        }

        /* Profile Page h2 Override */
        .profile-wrap h2 {
            font-size: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
            margin: inherit !important;
        }

        .trainer-page h3 {
            font-size: var(--font-size-2xl);
            font-weight: 700;
            line-height: var(--line-height-tight);
            margin: 0 0 var(--spacing-md) 0;
        }

        .trainer-page h4 {
            font-size: var(--font-size-lg);
            font-weight: 700;
            line-height: var(--line-height-tight);
            margin: 0 0 var(--spacing-sm) 0;
        }

        .trainer-page p {
            margin: 0;
            line-height: var(--line-height-normal);
        }

        /* Common Components */
        .trainer-page button,
        .trainer-page a.btn {
            font-family: var(--font-family-base);
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        /* Cards and Containers */
        .trainer-page .card,
        .trainer-page .widget-container {
            background: var(--white-clr);
            border: 1px solid var(--line-clr);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-md);
        }

        /* Responsive Default */
        @media (max-width: 1024px) {

            .trainer-page .main-content,
            .trainer-page .dashboard-content {
                padding: var(--spacing-4xl);
            }
        }

        @media (max-width: 768px) {

            .trainer-page .main-content,
            .trainer-page .dashboard-content {
                padding: var(--spacing-2xl);
            }
        }

        @media (max-width: 600px) {

            .trainer-page .main-content,
            .trainer-page .dashboard-content {
                padding: var(--spacing-lg);
            }

            .trainer-page {
                grid-template-columns: 1fr;
            }

            .trainer-page h1 {
                font-size: var(--font-size-3xl);
            }

            .trainer-page h2 {
                font-size: var(--font-size-2xl);
            }

            .trainer-page h3 {
                font-size: var(--font-size-lg);
            }
        }
    </style>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            max-width: 100vw;
        }

        .trainer-page {
            margin-top: 60px;
            min-height: calc(100vh - 60px);
        }

        .main-wrapper {
            animation: none;
            transform: none;
            min-width: 0;
        }

        .main-wrapper.full-width {
            margin-left: 0 !important;
            padding: 20px;
            max-width: 100%;
            margin-right: 0;
            margin-left: 0;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
</head>

<body>
    @include('trainer.partials.navbar')

    <div class="trainer-page">
        @unless(View::hasSection('noTrainerSidebar'))
            <!-- Sidebar Trainer -->
            @include('trainer.partials.sidebar')
        @endunless

        <div class="main-wrapper {{ View::hasSection('noTrainerSidebar') ? 'full-width' : '' }}">
            <main class="dashboard-content">
                @if(session('success'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: '{{ session('success') }}',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        });
                    </script>
                @endif

                @if(session('error'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: '{{ session('error') }}'
                            });
                        });
                    </script>
                @endif

                @if($errors->any())
                    <script>
                        document.addEventListener("DOMContentLoaded", function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: '{{ $errors->first() }}'
                            });
                        });
                    </script>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @unless(View::hasSection('noTrainerSidebar'))
        @vite(['resources/js/trainer/sidebar.js'])
    @endunless

    @stack('scripts')
</body>

</html>