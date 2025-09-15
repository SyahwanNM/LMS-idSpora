# Sistem Autentikasi idSpora

## Deskripsi
Sistem autentikasi lengkap untuk aplikasi LMS idSpora yang mencakup fitur login, registrasi, dan logout.

## Fitur yang Tersedia

### 1. Registrasi User Baru
- **URL**: `/sign-up`
- **Method**: GET (form) dan POST (submit)
- **Validasi**:
  - Nama lengkap (required, string, max 255 karakter)
  - Email (required, email format, unique)
  - Password (required, min 6 karakter)
  - Konfirmasi password (harus sama dengan password)
- **Default Role**: User baru otomatis mendapat role 'user'

### 2. Login
- **URL**: `/sign-in`
- **Method**: GET (form) dan POST (submit)
- **Validasi**:
  - Email (required, email format)
  - Password (required, min 6 karakter)
- **Fitur Tambahan**:
  - Remember me (opsional)
  - Redirect otomatis ke dashboard setelah login berhasil

### 3. Logout
- **URL**: `/logout`
- **Method**: POST
- **Fitur**: 
  - Menghapus session
  - Redirect ke halaman utama

### 4. Dashboard
- **URL**: `/dashboard`
- **Method**: GET
- **Akses**: Hanya untuk user yang sudah login
- **Fitur**: Menampilkan informasi user dan tombol logout

## User Default

### Admin
- **Email**: admin@idspora.com
- **Password**: admin123
- **Role**: admin

### User Sample
- **Email**: john@example.com
- **Password**: password123
- **Role**: user

## Struktur Database

### Tabel Users
```sql
- id (primary key)
- name (string)
- email (string, unique)
- email_verified_at (timestamp, nullable)
- password (string, hashed)
- role (enum: 'admin', 'user')
- remember_token (string, nullable)
- created_at (timestamp)
- updated_at (timestamp)
```

## Middleware

### 1. Auth Middleware
- Melindungi route yang memerlukan autentikasi
- Redirect ke halaman login jika user belum login

### 2. Guest Middleware
- Mencegah user yang sudah login mengakses halaman login/register
- Redirect ke dashboard jika user sudah login

## File yang Dibuat/Dimodifikasi

### Controller
- `app/Http/Controllers/AuthController.php` - Controller untuk menangani autentikasi

### Model
- `app/Models/User.php` - Ditambahkan field 'role' ke fillable

### Views
- `resources/views/sign-in.blade.php` - Form login dengan validasi
- `resources/views/sign-up.blade.php` - Form registrasi dengan validasi
- `resources/views/dashboard.blade.php` - Halaman dashboard setelah login

### Routes
- `routes/web.php` - Route untuk autentikasi dan middleware

### Middleware
- `app/Http/Middleware/RedirectIfAuthenticated.php` - Middleware untuk guest

### Seeder
- `database/seeders/UserSeeder.php` - Seeder untuk user default
- `database/seeders/DatabaseSeeder.php` - Diupdate untuk menjalankan UserSeeder

## Cara Menggunakan

### 1. Menjalankan Aplikasi
```bash
php artisan serve
```

### 2. Mengakses Halaman
- **Registrasi**: http://localhost:8000/sign-up
- **Login**: http://localhost:8000/sign-in
- **Dashboard**: http://localhost:8000/dashboard (setelah login)

### 3. Testing dengan User Default
1. Buka http://localhost:8000/sign-in
2. Login dengan:
   - Email: admin@idspora.com
   - Password: admin123
3. Atau buat akun baru di http://localhost:8000/sign-up

## Keamanan

- Password di-hash menggunakan Laravel Hash facade
- CSRF protection pada semua form
- Validasi input yang ketat
- Session management yang aman
- Middleware untuk kontrol akses

## Error Handling

- Validasi form dengan pesan error dalam bahasa Indonesia
- Alert success/error yang user-friendly
- Redirect dengan input lama jika validasi gagal
- Error handling untuk email duplikat dan kredensial salah
