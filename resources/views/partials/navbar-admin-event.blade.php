<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="navbar-admin">
        <ul>
            <li><img src="{{ asset('aset/logo.png') }}" alt=""></li>
            <a href="">
                <li>Manage Event</li>
            </a>
            <a href="">
                <li>Manage Users</li>
            </a>
            <a href="">
                <li>Report</li>
            </a>
        </ul>
    </div>
</body>

</html>