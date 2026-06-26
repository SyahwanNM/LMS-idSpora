<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Reseller Ditangguhkan - IdSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.navbar-after-login')

    <div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
        <div class="row justify-content-center w-100">
            <div class="col-md-8 col-lg-6 text-center">
                <div class="card border-0 shadow-lg p-5 align-items-center rounded-4 bg-white">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                        <i class="bi bi-shield-slash-fill text-danger" style="font-size: 3rem;"></i>
                    </div>
                    
                    <h2 class="fw-bold mb-3 text-dark">Kemitraan Reseller Ditangguhkan</h2>
                    <p class="text-secondary mb-4" style="font-size: 1.05rem; line-height: 1.6;">
                        Akses kemitraan reseller Anda untuk akun <strong>{{ $user->email }}</strong> saat ini ditangguhkan (suspended) oleh Administrator. 
                        Selama masa penangguhan, Anda tidak dapat mengakses dashboard reseller, mengelola kode referral, atau mengajukan pencairan komisi.
                    </p>

                    <div class="alert alert-warning border-0 rounded-3 text-start small mb-4 py-3 px-4">
                        <h6 class="fw-bold text-warning-emphasis mb-1"><i class="bi bi-info-circle-fill me-1"></i> Apa artinya bagi Anda?</h6>
                        <ul class="mb-0 ps-3 text-secondary">
                            <li>Kode referral unik Anda tidak aktif sementara waktu.</li>
                            <li>Pembelian baru yang menggunakan kode Anda tidak akan menghasilkan komisi.</li>
                            <li>Saldo wallet yang ada dibekukan hingga status penangguhan dicabut.</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="https://wa.me/628989260731?text=Halo%20Admin%20IdSpora,%20akun%20reseller%20saya%20({{ urlencode($user->email) }})%20ditangguhkan.%20Mohon%20bantuannya%20untuk%20pengecekan." target="_blank" class="btn btn-success py-3 rounded-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-whatsapp"></i> Hubungi Admin via WhatsApp
                        </a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary py-3 rounded-3 fw-bold">
                            Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.footer-after-login')
</body>

</html>
