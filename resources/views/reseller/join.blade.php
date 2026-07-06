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
    @include('partials.navbar-after-login')
    <div>
        <div class="row justify-content-center">
            <div class="col-md-9 p-5">
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

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">Syarat & Ketentuan Reseller</label>
                        <div class="p-3 border rounded-3 bg-light text-muted small" style="max-height: 200px; overflow-y: scroll; text-align: justify; line-height: 1.6;">
                            <p>Dengan mengaktifkan akun Reseller, pengguna menyatakan telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan berikut:</p>
                            <ol class="ps-3 mb-3">
                                <li class="mb-2">Program Reseller hanya dapat diikuti oleh pengguna yang telah memiliki akun aktif pada LMS idSpora.</li>
                                <li class="mb-2">Setelah akun reseller diaktifkan, sistem akan membuat <strong>kode referral</strong> dan <strong>tautan referral</strong> yang bersifat unik.</li>
                                <li class="mb-2">Reseller akan memperoleh komisi dari setiap transaksi pembelian program pelatihan yang dilakukan melalui kode atau tautan referral miliknya dengan status pembayaran <strong>berhasil</strong>.</li>
                                <li class="mb-2">Besaran komisi untuk setiap program pelatihan akan ditampilkan pada halaman detail produk sebelum reseller mulai melakukan promosi.</li>
                                <li class="mb-2">Pengajuan pencairan komisi (<em>withdraw</em>) hanya dapat dilakukan apabila saldo komisi telah mencapai minimal <strong>Rp50.000</strong>.</li>
                                <li class="mb-2">Setiap pencairan komisi dikenakan <strong>biaya administrasi transfer sebesar Rp2.500</strong> yang akan dipotong langsung dari nominal pencairan dan ditampilkan sebelum reseller mengonfirmasi pengajuan <em>withdraw</em>.</li>
                                <li class="mb-2">Reseller wajib mengisi data rekening bank yang benar dan masih aktif. Dana akan dikirim ke rekening yang tersimpan pada akun reseller. Kesalahan pengisian data rekening menjadi tanggung jawab reseller.</li>
                                <li class="mb-2">
                                    Reseller dilarang melakukan tindakan berikut:
                                    <ul class="ps-3 mt-1" style="list-style-type: disc;">
                                        <li>Membuat transaksi fiktif untuk memperoleh komisi.</li>
                                        <li>Menggunakan akun ganda untuk memanipulasi transaksi.</li>
                                        <li>Menggunakan bot, spam, atau metode promosi yang mengganggu pihak lain.</li>
                                        <li>Memberikan informasi yang tidak benar mengenai program pelatihan idSpora.</li>
                                        <li>Melakukan tindakan yang dapat merugikan idSpora maupun peserta pelatihan.</li>
                                    </ul>
                                </li>
                                <li class="mb-2">
                                    Apabila reseller terbukti melakukan pelanggaran sebagaimana disebutkan pada poin 8, idSpora berhak:
                                    <ul class="ps-3 mt-1" style="list-style-type: disc;">
                                        <li>Membatalkan komisi dari transaksi yang melanggar.</li>
                                        <li>Menolak pengajuan pencairan komisi.</li>
                                        <li>Menangguhkan akun reseller untuk sementara.</li>
                                        <li>Menonaktifkan akun reseller secara permanen apabila pelanggaran dilakukan berulang atau terbukti merupakan tindakan kecurangan.</li>
                                    </ul>
                                </li>
                                <li class="mb-2">Reseller dapat berhenti mengikuti Program Reseller kapan saja melalui pengajuan kepada administrator. Saldo komisi yang telah memenuhi syarat <em>withdraw</em> tetap dapat dicairkan sesuai ketentuan yang berlaku.</li>
                            </ol>
                            <p class="mb-0">Dengan mencentang kotak persetujuan, pengguna menyatakan telah membaca, memahami, dan menyetujui seluruh Syarat dan Ketentuan Program Reseller LMS idSpora.</p>
                        </div>
                    </div>

                    <form action="{{ route('reseller.activate') }}" method="POST" id="activateForm">
                        @csrf
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="agree_tos" name="agree_tos" value="1" required>
                            <label class="form-check-label small text-secondary" for="agree_tos" style="cursor: pointer;">
                                Saya telah membaca, memahami, dan menyetujui seluruh Syarat & Ketentuan di atas.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-warning w-100 py-3 rounded-3 fw-bold shadow-sm hover-scale" id="submitBtn" disabled>
                            <i class="bi bi-magic me-2"></i> Generate Kode Referral Saya
                        </button>
                    </form>
                    <p class="text-center mt-3 small text-muted">Gratis, tanpa biaya pendaftaran!</p>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const checkbox = document.getElementById('agree_tos');
                            const button = document.getElementById('submitBtn');
                            if (checkbox && button) {
                                checkbox.addEventListener('change', function() {
                                    button.disabled = !this.checked;
                                });
                            }
                        });
                    </script>
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