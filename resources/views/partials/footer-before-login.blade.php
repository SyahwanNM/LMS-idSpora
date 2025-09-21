<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
</head>
<style>
    .footer-section {
        background: var(--primary-dark);
        color: #fff;
        padding: 50px 0 10px;
        position: relative;
    }

    .footer-section::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
    }

    .footer-cta {
        margin: 30px 0 80px;
    }

    .cta-actions {
        gap: 15px;
    }

    .cta-row {
        max-width: 270px;
        margin: 0;
    }

    .btn-login {
        background: var(--secondary);
        color: #fff;
        border: none;
    }

    .btn-login:hover {
        background: var(--primary-dark);
        color: var(--secondary);
        border: 1px solid var(--secondary);
    }

    .btn-signup {
        background: #fff;
        color: #000;
        border: 1px solid rgba(0, 0, 0, .12);
    }

    .btn-signup:hover {
        background: transparent;
        color: #fff;
        border-color: #fff;
    }

    .footer-divider {
        border-color: #495057;
        opacity: 1;
    }

    .footer-copy {
        font-size: .8rem;
        font-weight: 500;
        color: rgba(255, 255, 255, .9);
    }

    .footer-section a:not(.btn) {
        text-decoration: none;
        color: inherit;
    }

    .footer-section a:not(.btn):hover {
        text-decoration: none;
        color: var(--secondary);
        border-bottom: 1px solid var(--secondary);
        background: transparent;
    }
</style>

<body>
    <div class="footer-section">
        <div class="footer-cta">
            <h4 class="text-center mb-3">Siap Memulai Perjalanan Belajarmu?</h4>
            <h6 class="text-center mb-4 footer-subtitle">
                Bergabunglah dengan ribuan learner lainnya dan tingkatkan skill digital Anda hari ini
            </h6>

            <div class="d-flex justify-content-center text-center mt-2 cta-actions">
                <div class="row justify-content-center w-100 cta-row">
                    <div class="col">
                        <a href="#" class="btn btn-lg w-100 btn-login">Login</a>
                    </div>
                    <div class="col">
                        <a href="#" class="btn btn-lg w-100 btn-signup">Sign Up</a>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-3 footer-divider" />

        <div class="row justify-content-center">
            <div class="col-md-4 text-center">
                <p class="mb-0 footer-copy">
                    &copy; 2024 idSpora. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</body>

</html>