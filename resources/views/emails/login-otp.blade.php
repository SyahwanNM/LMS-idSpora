<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kode OTP Login</title>
</head>
<body style="font-family: Arial, sans-serif; color:#111;">
  <p>Halo {{ $name }},</p>
  <p>Berikut adalah kode OTP untuk login akun Anda:</p>
  <h2 style="letter-spacing:4px;">{{ $code }}</h2>
  <p>Kode ini berlaku selama {{ $minutes }} menit.</p>
  <p>Jika Anda tidak merasa meminta kode ini, abaikan email ini.</p>
  <p>Terima kasih,<br/>Tim {{ config('app.name') }}</p>
</body>
</html>
