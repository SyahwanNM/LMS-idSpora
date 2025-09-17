<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="footer-section">
        <div style="margin-bottom: 80px; margin-top: 30px;">
        <h4 class="text-center mb-3">Siap Memulai Perjalanan Belajarmu?</h4>
        <h6 class="text-center mb-4" style="font-size: 16px; font-weight: 400;">Bergabunglah dengan ribuan learner
            lainnya
            dan tingkatkan skill digital Anda hari ini</h6>
            <div class="d-flex justify-content-center text-center mt-2" style="gap: 15px;">
                <div class="row justify-content-center w-100" style="max-width:270px; margin: 0px; gap:0px;">
                    <div class="col">
                        <a href="#" class="btn btn-lg w-100" style="background: var(--secondary);">Login</a>
                    </div>
                    <div class="col">
                        <a href="#" class="btn btn-lg w-100" style="background: #fff; color: #000;">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>
        <hr class="my-3" style="border-color: #495057" />
        <div class="row justify-content-center">
            <div class="col-md-4 text-center">
                <p class="text-light mb-0" style="font-size: 0.8rem; font-weight: 500;">
                    &copy; 2024 idSpora. All rights reserved.
                </p>
            </div>
        </div>
    </div>

</body>

</html>