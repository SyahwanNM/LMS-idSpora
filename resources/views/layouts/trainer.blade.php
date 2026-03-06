<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Trainer Area') - idSpora</title>

    {{-- CSS Global & Spesifik Trainer --}}
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
        /* Pastikan navbar fixed tidak mengganggu layout */
        body {
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .trainer-page {
            margin-top: 60px;
            /* Height navbar */
            min-height: calc(100vh - 60px);
        }
    </style>
    @stack('styles')
</head>

<body>
    {{-- Navbar Khusus Trainer --}}
    @include('trainer.partials.navbar')

    <div class="trainer-page">
        {{-- Sidebar Trainer --}}
        @include('trainer.partials.sidebar')

        <div class="main-wrapper">
            <main class="dashboard-content">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @vite(['resources/js/trainer/sidebar.js'])

    @stack('scripts')
</body>

</html>