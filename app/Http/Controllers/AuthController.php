<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordResetToken;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('sign-in');
    }

    public function showRegister()
    {
        return view('sign-up');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'redirect' => 'sometimes|string|nullable'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Kata sandi harus diisi',
            'password.min' => 'Kata sandi minimal 6 karakter',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Validasi kredensial secara manual untuk memulai OTP 2FA tanpa langsung login
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['email' => 'Email atau kata sandi salah'])
                ->withInput($request->except('password'));
        }

        // Langsung login tanpa OTP (OTP dipindah ke proses pendaftaran akun)
        // Hormati pilihan "ingat saya" dari form
        $remember = $request->boolean('remember');
        Auth::loginUsingId($user->id, $remember);
        // Catat aktivitas login
        try { \App\Models\ActivityLog::create(['user_id' => $user->id, 'action' => 'Login', 'description' => 'Login (direct)']); } catch (\Throwable $e) {}
        // If admin, go to admin dashboard
        if (strcasecmp($user->role ?? '', 'admin') === 0) {
            return redirect('/admin/dashboard')->with('login_success', 'Login berhasil! Selamat datang di Admin Panel.');
        }

        // For regular users, always redirect to the main dashboard after successful login
        return redirect('/dashboard')->with('success', 'Login berhasil. Selamat datang di IdSpora Academy!');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required','string','min:8','confirmed',
                'regex:/[A-Z]/',        // at least one uppercase letter
                'regex:/[0-9]/',        // at least one digit
                'regex:/[^A-Za-z0-9]/', // at least one symbol
            ],
            'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Kata sandi harus diisi',
            'password.min' => 'Kata sandi minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'password.regex' => 'Kata sandi harus mengandung huruf besar, angka, dan tanda baca',
            'avatar.image' => 'File avatar harus berupa gambar',
            'avatar.mimes' => 'Format avatar harus jpg, jpeg, png, atau webp',
            'avatar.max' => 'Ukuran avatar maks 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }
        // Simpan avatar (jika ada) namun JANGAN buat user sebelum verifikasi
        $avatarFileName = null;
        if ($request->hasFile('avatar')) {
            $storedPath = $request->file('avatar')->store('avatars', 'public');
            $avatarFileName = basename($storedPath);
        }

        // Simpan payload pendaftaran di sesi
        session([
            'register_payload' => [
                'name' => $request->name,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'avatar' => $avatarFileName,
            ],
        ]);

        // Kirim kode verifikasi menggunakan PasswordResetToken + PasswordResetMail
        try {
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = Str::random(64);
            PasswordResetToken::where('email', $request->email)->delete();
            PasswordResetToken::create([
                'email' => $request->email,
                'token' => $token,
                'verification_code' => $verificationCode,
                'expires_at' => Carbon::now()->addMinutes(15),
                'is_used' => false,
            ]);
            Mail::to($request->email)->send(new \App\Mail\RegistrationVerificationMail($verificationCode, $request->name, 15));
        } catch (\Throwable $e) {
            \Log::error('Send registration verification failed: ' . $e->getMessage());
        }

        return redirect()->route('verifikasi')
            ->with('success', 'Registrasi berhasil! Kode verifikasi telah dikirim ke email Anda.')
            ->with('register_verify_email', $request->email);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout berhasil!');
    }

    public function showForgotPassword()
    {
        return view('forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.exists' => 'Email tidak terdaftar dalam sistem',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        $user = User::where('email', $request->email)->first();
        
        // Generate kode verifikasi 6 digit
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Generate token unik
        $token = Str::random(64);
        
        // Hapus token lama jika ada
        PasswordResetToken::where('email', $request->email)->delete();
        
        // Simpan token baru
        PasswordResetToken::create([
            'email' => $request->email,
            'token' => $token,
            'verification_code' => $verificationCode,
            'expires_at' => Carbon::now()->addMinutes(15), // 15 menit
        ]);

        // Kirim email
        try {
            Mail::to($request->email)->send(new PasswordResetMail($verificationCode, $user->name));
            
            return redirect()->route('verifikasi')
                ->with('success', 'Kode verifikasi telah dikirim ke email Anda')
                ->with('email', $request->email);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Email sending failed: ' . $e->getMessage());
            \Log::error('Email config: ' . json_encode([
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'username' => config('mail.mailers.smtp.username'),
                'encryption' => config('mail.mailers.smtp.encryption'),
            ]));
            
            return redirect()->back()
                ->withErrors(['email' => 'Gagal mengirim email: ' . $e->getMessage() . '. Silakan periksa konfigurasi email.'])
                ->withInput($request->only('email'));
        }
    }

    public function showVerification(Request $request)
    {
        // Pastikan flash data email pendaftaran tetap tersedia untuk request berikutnya (verify/resend)
        try { $request->session()->keep('register_verify_email'); } catch (\Throwable $e) {}
        return view('verifikasi');
    }

    public function resendRegisterOtp(Request $request)
    {
        // Cooldown 60 detik untuk kirim ulang
        $last = $request->session()->get('register_otp_last_resend_at');
        if ($last) {
            $diff = now()->diffInSeconds($last);
            if ($diff < 60) {
                $remaining = 60 - $diff;
                return back()->withErrors(['error' => "Tunggu $remaining detik untuk kirim ulang kode."]);
            }
        }
        $email = $request->session()->get('register_verify_email');
        if (!$email) {
            return redirect()->route('register')->withErrors(['email' => 'Sesi verifikasi tidak ditemukan. Silakan daftar ulang.']);
        }
        try {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = Str::random(64);
            PasswordResetToken::where('email', $email)->delete();
            PasswordResetToken::create([
                'email' => $email,
                'token' => $token,
                'verification_code' => $code,
                'expires_at' => Carbon::now()->addMinutes(15),
                'is_used' => false,
            ]);
            $name = ($request->session()->get('register_payload')['name'] ?? 'Pengguna');
            Mail::to($email)->send(new \App\Mail\RegistrationVerificationMail($code, $name, 15));
            // Set cooldown start
            $request->session()->put('register_otp_last_resend_at', now());
            return back()->with('success', 'Kode verifikasi baru telah dikirim. Periksa folder Inbox/Spam.');
        } catch (\Throwable $e) {
            \Log::error('Resend registration verification failed: '.$e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengirim ulang kode verifikasi.']);
        }
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required|string|size:6',
        ], [
            'verification_code.required' => 'Kode verifikasi harus diisi',
            'verification_code.size' => 'Kode verifikasi harus 6 digit',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('verification_code'));
        }

        // Jika berasal dari alur pendaftaran, verifikasi via PasswordResetToken lalu buat user
        $registerEmail = $request->input('register_email') ?: $request->session()->get('register_verify_email');
        if ($registerEmail) {
            $resetToken = PasswordResetToken::where('email', $registerEmail)
                ->where('verification_code', $request->verification_code)
                ->where('is_used', false)
                ->first();
            if (!$resetToken || $resetToken->isExpired()) {
                return redirect()->back()->withErrors(['verification_code' => 'Kode verifikasi tidak valid atau sudah kadaluarsa']);
            }
            $payload = $request->session()->get('register_payload');
            if (!$payload || ($payload['email'] ?? null) !== $registerEmail) {
                return redirect()->route('register')->withErrors(['email' => 'Sesi pendaftaran tidak ditemukan. Silakan daftar ulang.']);
            }
            $user = User::where('email', $registerEmail)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'password' => $payload['password_hash'],
                    'role' => 'user',
                    'avatar' => $payload['avatar'] ?? null,
                    'email_verified_at' => now(),
                ]);
            } else {
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $user->save();
                }
            }
            $resetToken->update(['is_used' => true]);
            $request->session()->forget(['register_verify_email','register_payload']);
            return redirect()->route('login')
                ->with('success', 'Email berhasil diverifikasi! Silakan login.')
                ->withInput(['email' => $user->email]);
        }

        // Default: verifikasi reset password via PasswordResetToken
        $resetToken = PasswordResetToken::where('verification_code', $request->verification_code)
            ->where('is_used', false)
            ->first();

        if (!$resetToken || $resetToken->isExpired()) {
            return redirect()->back()
                ->withErrors(['verification_code' => 'Kode verifikasi tidak valid atau sudah kadaluarsa']);
        }

        // Mark token as used
        $resetToken->update(['is_used' => true]);

        // Simpan token ke sesi non-flash agar tidak hilang
        $request->session()->put('token', $resetToken->token);

        return redirect()->route('new-password')
            ->with('success', 'Kode verifikasi berhasil');
    }

    public function showNewPassword(Request $request)
    {
        // Pastikan token tetap ada di sesi (jika datang via flash/redirect)
        try { $request->session()->keep('token'); } catch (\Throwable $e) {}
        return view('new-password');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required','string','min:8','confirmed',
                'regex:/[A-Z]/',        // at least one uppercase letter
                'regex:/[0-9]/',        // at least one digit
                'regex:/[^A-Za-z0-9]/', // at least one symbol
            ],
            'token' => 'required|string',
        ], [
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'password.regex' => 'Password harus mengandung huruf besar, angka, dan tanda baca',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $resetToken = PasswordResetToken::where('token', $request->token)
            ->where('is_used', true)
            ->first();

        if (!$resetToken || $resetToken->isExpired()) {
            return redirect()->route('forgot-password')
                ->withErrors(['error' => 'Token tidak valid atau sudah kadaluarsa']);
        }

        $user = User::where('email', $resetToken->email)->first();
        
        if (!$user) {
            return redirect()->route('forgot-password')
                ->withErrors(['error' => 'User tidak ditemukan']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token
        $resetToken->delete();

        return redirect()->route('login')
            ->with('success', 'Password berhasil direset. Silakan login dengan password baru.');
    }

    // ===== Login OTP (2FA) =====
    public function showLoginOtpForm(Request $request)
    {
        if (!$request->session()->has('login_otp_user_id')) {
            return redirect()->route('login')->withErrors(['email' => 'Silakan login terlebih dahulu.']);
        }
        $email = $request->session()->get('login_otp_email');
        $masked = $email ? preg_replace('/(^.).*(@.*$)/', '$1***$2', $email) : '';
        // Gunakan tampilan auth.blade.php sebagai halaman verifikasi OTP
        return view('auth', ['maskedEmail' => $masked]);
    }

    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'code' => ['required','string','size:6']
        ], [
            'code.required' => 'Kode OTP harus diisi',
            'code.size' => 'Kode OTP harus 6 digit',
        ]);
        $email = $request->session()->get('login_otp_email');
        $userId = $request->session()->get('login_otp_user_id');
        if(!$email || !$userId){
            return redirect()->route('login')->withErrors(['email' => 'Sesi OTP tidak ditemukan.']);
        }
        $otp = \App\Models\LoginOtp::where('email', $email)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->latest()
            ->first();
        if(!$otp || $otp->isExpired()){
            return back()->withErrors(['code' => 'Kode OTP tidak valid atau sudah kadaluarsa']);
        }
        $otp->update(['is_used' => true]);
        // Mark email as verified on first successful OTP (for Google-first-login scenario)
        $userToVerify = \App\Models\User::find($userId);
        if ($userToVerify && is_null($userToVerify->email_verified_at)) {
            $userToVerify->email_verified_at = now();
            $userToVerify->save();
        }
        // Login user
        Auth::loginUsingId($userId, true);
        // Catat aktivitas login
        try { \App\Models\ActivityLog::create(['user_id' => $userId, 'action' => 'Login', 'description' => 'Login via OTP']); } catch (\Throwable $e) {}
        // Bersihkan sesi OTP
        $redirect = $request->session()->pull('login_otp_redirect');
        $request->session()->forget(['login_otp_user_id','login_otp_email']);

        $user = Auth::user();
        if (strcasecmp($user->role ?? '', 'admin') === 0) {
            return redirect('/admin/dashboard')->with('login_success', 'Login berhasil! Selamat datang di Admin Panel.');
        }
        if ($redirect) {
            return redirect($redirect)->with('success', 'Login berhasil!');
        }
        return redirect()->intended('/dashboard')->with('success','Login berhasil!');
    }

    public function resendLoginOtp(Request $request)
    {
        $email = $request->session()->get('login_otp_email');
        $userId = $request->session()->get('login_otp_user_id');
        if(!$email || !$userId){
            return redirect()->route('login')->withErrors(['email' => 'Sesi OTP tidak ditemukan.']);
        }
        $user = User::find($userId);
        if(!$user){
            return redirect()->route('login')->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }
        // Regenerate code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        \App\Models\LoginOtp::where('email', $email)->where('is_used', false)->delete();
        \App\Models\LoginOtp::create([
            'user_id' => $user->id,
            'email' => $email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ]);
        try {
            Mail::to($email)->send(new \App\Mail\LoginOtpMail($code, $user->name, 10));
        } catch (\Throwable $e) {
            \Log::error('Resend OTP mail failed: '.$e->getMessage());
            return back()->withErrors(['code' => 'Gagal mengirim ulang kode OTP.']);
        }
        return back()->with('success','Kode OTP baru telah dikirim.');
    }

    /**
     * Validasi dan kembalikan redirect internal yang aman.
     * Mengizinkan: relative path ("/something") atau full URL dengan host yang sama.
     */
    private function resolveSafeRedirect(Request $request): ?string
    {
        $redirect = $request->input('redirect');
        if (!$redirect) return null;

        // Jika full URL, pastikan host sama
        if (filter_var($redirect, FILTER_VALIDATE_URL)) {
            $appHost = $request->getHost();
            $urlHost = parse_url($redirect, PHP_URL_HOST);
            if ($urlHost !== $appHost) return null; // Host berbeda -> tolak
            $path = parse_url($redirect, PHP_URL_PATH) ?: '/';
            $query = parse_url($redirect, PHP_URL_QUERY);
            return $path . ($query ? ('?' . $query) : '');
        }

        // Jika relative path aman
        if (str_starts_with($redirect, '/') && !str_starts_with($redirect, '//')) {
            return $redirect;
        }
        return null;
    }
}