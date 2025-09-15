<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - idSpora</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background: radial-gradient(circle, #51376C 0%, #2E2050 100%);
            color: white;
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }
        
        .btn-logout {
            background-color: #f4a442;
            border: none;
            color: white;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 8px;
        }
        
        .btn-logout:hover {
            background-color: #e68a00;
            color: white;
        }
        
        .welcome-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 30px;
            margin: 50px 0;
        }
        
        .user-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .role-badge {
            background-color: #f4a442;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('aset/logo.png') }}" alt="idSpora" height="40" class="me-2">
                idSpora
            </a>
            
            <div class="navbar-nav ms-auto">
                <a href="{{ route('welcome') }}" class="btn btn-outline-light me-2">Beranda</a>
                <a href="{{ route('events.index') }}" class="btn btn-outline-light me-2">Events</a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-logout">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-card">
            <h2>Selamat datang, {{ Auth::user()->name }}!</h2>
            <p class="lead">Anda telah berhasil masuk ke sistem idSpora.</p>
            
            <div class="user-info">
                <h5>Informasi Akun</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Nama:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Role:</strong> 
                            <span class="role-badge">{{ ucfirst(Auth::user()->role) }}</span>
                        </p>
                        <p><strong>Bergabung:</strong> {{ Auth::user()->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
            
            @if (session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
