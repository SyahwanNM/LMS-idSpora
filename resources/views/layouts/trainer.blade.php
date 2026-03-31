<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
        'resources/css/trainer/main.css',
        "resources/css/trainer/dashboard.css",
        'resources/css/trainer/course.css',
        "resources/css/trainer/detail-course.css",
        'resources/css/trainer/events.css',
        "resources/css/trainer/detail-event.css",
        "resources/css/trainer/feedback.css",
    ])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .trainer-page {
            margin-top: 60px;
            min-height: calc(100vh - 60px);
        }

        .main-wrapper {
            animation: none;
            transform: none;
        }

        .main-wrapper.full-width {
            margin-left: 0 !important;
            padding: 20px;
            max-width: 1200px;
            margin-right: auto;
            margin-left: auto;
        }
    </style>
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
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
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