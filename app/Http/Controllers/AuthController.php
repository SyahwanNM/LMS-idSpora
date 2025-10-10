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

        // Generate OTP 6 digit, kadaluarsa 10 menit
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        // Hapus OTP lama yang belum dipakai untuk email ini
        \App\Models\LoginOtp::where('email', $user->email)->where('is_used', false)->delete();
        \App\Models\LoginOtp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Simpan sesi pending login
        $request->session()->put('login_otp_user_id', $user->id);
        $request->session()->put('login_otp_email', $user->email);
        $request->session()->put('login_otp_redirect', $this->resolveSafeRedirect($request));

        // Kirim email OTP
        try {
            Mail::to($user->email)->send(new \App\Mail\LoginOtpMail($code, $user->name, 10));
        } catch (\Throwable $e) {
            \Log::error('Login OTP mail failed: '.$e->getMessage());
            return redirect()->back()->withErrors(['email' => 'Gagal mengirim kode OTP. Coba lagi.'])->withInput($request->except('password'));
        }

        return redirect()->route('login.otp')->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Kata sandi harus diisi',
            'password.min' => 'Kata sandi minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok',
            'avatar.image' => 'File avatar harus berupa gambar',
            'avatar.mimes' => 'Format avatar harus jpg, jpeg, png, atau webp',
            'avatar.max' => 'Ukuran avatar maks 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $avatarFileName = null;
        if ($request->hasFile('avatar')) {
            // Simpan di disk public (storage/app/public/avatars) dengan folder "avatars"
            $storedPath = $request->file('avatar')->store('avatars', 'public'); // hasil: avatars/namafile.ext
            // Simpan hanya nama file (atau bisa simpan full path; accessor kita mendukung kasus tanpa slash)
            $avatarFileName = basename($storedPath);
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'avatar' => $avatarFileName,
        ]);

        // Jangan auto-login. Minta user login manual agar konsisten dengan requirement.
        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Silakan login menggunakan email & kata sandi yang baru dibuat.')
            ->withInput(['email' => $request->email]);
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

    public function showVerification()
    {
        return view('verifikasi');
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

        $resetToken = PasswordResetToken::where('verification_code', $request->verification_code)
            ->where('is_used', false)
            ->first();

        if (!$resetToken || $resetToken->isExpired()) {
            return redirect()->back()
                ->withErrors(['verification_code' => 'Kode verifikasi tidak valid atau sudah kadaluarsa']);
        }

        // Mark token as used
        $resetToken->update(['is_used' => true]);

        return redirect()->route('new-password')
            ->with('success', 'Kode verifikasi berhasil')
            ->with('token', $resetToken->token);
    }

    public function showNewPassword()
    {
        return view('new-password');
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required|string',
        ], [
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
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
        // Login user
        Auth::loginUsingId($userId, true);
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