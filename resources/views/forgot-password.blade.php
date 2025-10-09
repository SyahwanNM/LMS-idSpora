<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            font-family: "Poppins";
            background: radial-gradient(circle, #51376C 0%, #2E2050 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back {
            position: fixed;
            top: 50px;
            left: 70px;
            width: 20px;
            height: auto;
            cursor: pointer;
        }

        .main-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            padding: 40px;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
        }

        .kiri {
            flex: 0.8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
            font-weight: bold;
        }

        .logo {
            width: 400px;
            height: auto;
            margin-right: 100px;
        }

        .kanan {
            flex: 1;
            max-width: 350px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .kanan h3 {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-control {
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            background-color: #AFB6E54D;
            padding: 10px;
            width: 100%;
            color: white;
        }

        .form-control:focus {
            border-color: #f4a442;
            box-shadow: 0 0 0 2px rgba(244, 164, 66, 0.3);
            background-color: #AFB6E54D;
        }

        .btn-register {
            background-color: #f4a442;
            border: none;
            color: white;
            font-weight: bold;
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            margin-top: 15px;
        }

        .btn-register:hover {
            background-color: #e68a00;
        }
    </style>
</head>

<body>
    <img class="back" src="aset/back.png" alt="">
    <div class="main-container">
        <div class="kiri">
            <img class="logo" src="{{ asset('aset/logo.png') }}" alt="">
        </div>

        <div class="kanan">
            <h3>Lupa Kata Sandi?</h3>
            
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('forgot-password.send') }}" method="post">
                @csrf
                <div class="mb-3">
                    <h6>Email</h6>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <button type="submit" class="btn-register">Kirim Kode Verifikasi</button>
            </form>
            
            <div class="text-login" style="margin-top: 15px; text-align: center; font-size: 14px;">
                <a href="{{ route('login') }}" style="color: #f4a442; font-weight: bold; text-decoration: none;">Kembali ke Login</a>
            </div>
        </div>
    </div>
</body>

</html>