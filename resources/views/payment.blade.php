@include("partials.navbar-after-login")
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background-color: #F8FAFC;
        }
    </style>
</head>

<body>
    <div class="link-box mb-3">
        <a href="">Home</a>
        <p>/</p>
        <a href="">Event</a>
        <p>/</p>
        <a href="">Digital Marketing Masterclass 2025</a>
        <p>/</p>
        <a class="active" href="">Payment</a>
    </div>
    <div class="box-payment">
        <div class="kiri-payment">
            <h3>Data Peserta</h3>
            <p class="judul-input">Email</p>
            <input class="form" type="email">
            <p class="judul-input">Nama Lengkap</p>
            <div class="warning">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#EC0606" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                </svg>
                <p class="warning-text">Nama akan digunakan di sertifikat</p>
            </div>
            <input class="form" type="text">
            <div class="form-group">
                <p class="judul-input">No Whatsapp</p>
                <div class="wa-input">
                    <select>
                        <option>Kode Dial</option>
                        <option value="+62">+62</option>
                        <option value="+60">+60</option>
                        <option value="+65">+65</option>
                    </select>
                    <input class="no-wa" type="text" placeholder="No Whatsapp">
                </div>
            </div>
        </div>

        <div class="ticket">
            <div class="ticket-header">Order Detail</div>
            <div class="ticket-content"> <img src="{{ asset('aset/event.png') }}" alt="Event">
                <div class="info">
                    <h4>Digital Marketing Masterclass 2025</h4>
                    <p>IdSpora</p>
                    <div class="price">Rp 50.000</div>
                </div>
            </div>
            <div class="ticket-divider"></div>
            <div class="ticket-footer">
                <div>
                    <h5>Total</h5>
                    <p>Rp 50.000</p>
                </div>
                <div class="icon">📄</div>
            </div> <button class="btn-pay">Bayar</button>
        </div>
    </div>
</body>

</html>