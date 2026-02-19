@include('partials.navbar-after-login')
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Reseller IdSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    {{--
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}"> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div>
        <div class="row justify-content-center">

            {{-- Bagian Teks & Tombol (Kanan di Desktop) --}}
            <div class="col-md-7 p-5">
                <div class="text-start">
                    <span
                        class="badge bg-warning bg-opacity-25 text-warning-emphasis mb-3 px-3 py-2 rounded-pill fw-bold">
                        <i class="bi bi-stars me-1"></i> Program Reseller IdSpora
                    </span>

                    <h2 class="fw-bold mb-3">Wah, kamu belum punya Kode Referral!</h2>
                    <p class="text-muted mb-4">
                        Akun kamu sudah terdaftar, tapi fitur Reseller belum aktif.
                        Aktifkan sekarang untuk mendapatkan <strong>Kode Unik</strong> dan mulai bagikan ke teman-temanmu.
                    </p>

                    <ul class="list-unstyled mb-4">
                        <li class="mb-2 d-flex align-items-center text-secondary">
                            <i class="bi bi-check-circle-fill text-success me-2"></i> Komisi hingga 15% per transaksi
                        </li>
                        <li class="mb-2 d-flex align-items-center text-secondary">
                            <i class="bi bi-check-circle-fill text-success me-2"></i> Pencairan dana mudah & cepat
                        </li>
                        <li class="d-flex align-items-center text-secondary">
                            <i class="bi bi-check-circle-fill text-success me-2"></i> Pantau performa real-time
                        </li>
                    </ul>

                    <form action="{{ route('reseller.activate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 py-3 rounded-3 fw-bold shadow-sm hover-scale">
                            <i class="bi bi-magic me-2"></i> Generate Kode Referral Saya
                        </button>
                    </form>
                    <p class="text-center mt-3 small text-muted">Gratis, tanpa biaya pendaftaran!</p>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer-after-login')

    <style>
        .hover-scale {
            transition: transform 0.2s, box-shadow 0.3s;
            box-shadow: 0 0 0 rgba(255, 193, 7, 0.5);
        }

        .hover-scale:hover {
            transform: scale(1.02);
            box-shadow: 0 0 20px rgba(255, 193, 7, 0.7), 0 0 40px rgba(255, 193, 7, 0.4);
        }
    </style>
</body>

</html>