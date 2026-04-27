<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

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
use App\Support\AdminSettings;

class AuthController extends Controller
{
    private const VERIFICATION_CODE_EXPIRES_MINUTES = 10;
    public function showLogin()
    {
        return view('auth.sign-in');
    }

    public function showRegister()
    {
        return view('auth.sign-up');
    }

    public function login(Request $request)
    {
    $rules = [
    'email' => ['required', 'email'],
    'password' => ['required', 'min:6'],
    'redirect' => ['sometimes', 'string', 'nullable'],
    ];

    $messages = [
    'email.required' => 'Email harus diisi',
    'email.email' => 'Format email tidak valid',
    'password.required' => 'Kata sandi harus diisi',
    'password.min' => 'Kata sandi minimal 6 karakter',
    ];

$validator = Validator::make($request->all(), $rules, $messages);

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

        // During maintenance, do not create a login session for non-admin users.
        if (strcasecmp($user->role ?? '', 'admin') !== 0 && AdminSettings::maintenanceEnabled()) {
            $msg = AdminSettings::maintenanceMessage() ?: 'Mohon maaf, akses LMS sedang maintenance.';
            return redirect('/')->with('maintenance_notice', $msg);
        }

        // Langsung login tanpa OTP (OTP dipindah ke proses pendaftaran akun)
        // Hormati pilihan "ingat saya" dari form
        $remember = $request->boolean('remember');
        Auth::loginUsingId($user->id, $remember);
        // Catat aktivitas login
        try {
            \App\Models\ActivityLog::create(['user_id' => $user->id, 'action' => 'Login', 'description' => 'Login (direct)']);
        } catch (\Throwable $e) {
        }
        // If admin, go to admin dashboard
        if (strcasecmp($user->role ?? '', 'admin') === 0) {
            return redirect('/admin/dashboard')
                ->with('login_success', 'Login successful! Welcome to the Admin Panel!');
        }

        // (Maintenance for non-admin is handled above before login session is created.)
        //trainer
        if (strcasecmp($user->role ?? '', 'trainer') === 0) {
            return redirect()
                ->route('trainer.dashboard');
        }

        // For regular users, redirect back to intended URL (protected page) when available.
        // Also honor explicit internal redirect param (used by guest-only links).
        $redirect = trim((string) $request->input('redirect', ''));
        if ($redirect !== '' && str_starts_with($redirect, '/') && !str_starts_with($redirect, '//')) {
            return redirect($redirect);
        }
        return redirect()->intended('/dashboard');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',        // at least one uppercase letter
                'regex:/[0-9]/',        // at least one digit
                'regex:/[^A-Za-z0-9]/', // at least one symbol
            ],
            'avatar' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
           'name.required' => 'Full name is required',
            'name.regex' => 'Full name may only contain letters, spaces, apostrophes, hyphens, and dots.',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.unique' => 'Email is already registered',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Password confirmation does not match',
            'password.regex' => 'Password must contain uppercase letters, numbers, and punctuation',
            'avatar.image' => 'Avatar file must be an image',
            'avatar.mimes' => 'Avatar format must be jpg, jpeg, png, or webp',
            'avatar.max' => 'Avatar size must not exceed 2MB',
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

        // Check for referrer
        $referrerId = null;
        if ($request->filled('referrer_code')) {
            $referrer = User::where('referral_code', $request->referrer_code)->first();
            if ($referrer) {
                $referrerId = $referrer->id;
            }
        }

        // Simpan payload pendaftaran di sesi
        session([
            'register_payload' => [
                'name' => $request->name,
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'avatar' => $avatarFileName,
                'referrer_id' => $referrerId,
            ],
        ]);
        // Pastikan sesi forgot password dibersihkan agar tidak tumpang tindih
        session()->forget(['forgot_password_email', 'forgot_password_last_sent_at', 'token']);

        // Kirim kode verifikasi menggunakan PasswordResetToken + PasswordResetMail
        try {
            $expiresMinutes = self::VERIFICATION_CODE_EXPIRES_MINUTES;
            $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = Str::random(64);
            $expiresAt = now()->addMinutes($expiresMinutes);

            PasswordResetToken::where('email', $request->email)->delete();
            PasswordResetToken::create([
                'email' => $request->email,
                'token' => $token,
                'verification_code' => $verificationCode,
                'expires_at' => $expiresAt,
                'is_used' => false,
            ]);
            Mail::to($request->email)->send(new \App\Mail\RegistrationVerificationMail($verificationCode, $request->name, $expiresMinutes));
        } catch (\Throwable $e) {
            \Log::error('Send registration verification failed: ' . $e->getMessage());
        }

        $request->session()->put('register_verify_email', $request->email);
        $request->session()->put('otp_expires_at', $expiresAt->toIso8601String());

        return redirect()->route('verifikasi')
            ->with('success', 'Registration successful! A verification code has been sent to your email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logout Successfully!');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.exists' => 'Email is not registered in the system',
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
        $expiresAt = now()->addMinutes(self::VERIFICATION_CODE_EXPIRES_MINUTES);

        // Hapus token lama jika ada
        PasswordResetToken::where('email', $request->email)->delete();

        // Simpan token baru
        PasswordResetToken::create([
            'email' => $request->email,
            'token' => $token,
            'verification_code' => $verificationCode,
            'expires_at' => $expiresAt,
            'is_used' => false,
        ]);

        // Bersihkan sesi registrasi agar tidak mengganggu alur forgot password
        $request->session()->forget(['register_verify_email', 'register_payload']);

        // Kirim email
        try {
            Mail::to($request->email)->send(new PasswordResetMail($verificationCode, $user->name));

            $request->session()->put('forgot_password_email', $request->email);
            $request->session()->put('forgot_password_last_sent_at', now()->toIso8601String());
            $request->session()->put('otp_expires_at', $expiresAt->toIso8601String());

            return redirect()->route('verifikasi')
                ->with('success', 'A verification code has been sent to your email.')
                ->with('email', $request->email);
        } catch (\Exception $e) {
            // Log error untuk debugging
            \Log::error('Email sending failed: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['email' => 'Gagal mengirim email: ' . $e->getMessage()])
                ->withInput($request->only('email'));
        }
    }

    public function showVerification(Request $request)
    {
        // Jika ada email forgot password di session → tampilkan view forgot verify
        if ($request->session()->has('forgot_password_email')) {
            return view('auth.forgot-verify');
        }
        return view('auth.verifikasi');
    }

    public function resendRegisterOtp(Request $request)
    {
        // Cooldown 60 detik untuk kirim ulang
        $last = $request->session()->get('register_otp_last_resend_at');
        if ($last) {
            $diff = now()->diffInSeconds($last);
            if ($diff < 60) {
                $remaining = 60 - $diff;
                return back()->withErrors(['error' => "Wait $remaining seconds to resend the code."]);
            }
        }
        $email = $request->session()->get('register_verify_email');
        if (!$email) {
            return redirect()->route('register')->withErrors(['email' => 'Verification session not found. Please re-register.']);
        }
        try {
            $expiresMinutes = self::VERIFICATION_CODE_EXPIRES_MINUTES;
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = Str::random(64);
            $expiresAt = now()->addMinutes($expiresMinutes);

            PasswordResetToken::where('email', $email)->delete();
            PasswordResetToken::create([
                'email' => $email,
                'token' => $token,
                'verification_code' => $code,
                'expires_at' => $expiresAt,
                'is_used' => false,
            ]);
            $name = ($request->session()->get('register_payload')['name'] ?? 'Pengguna');
            Mail::to($email)->send(new \App\Mail\RegistrationVerificationMail($code, $name, $expiresMinutes));
            // Set cooldown start
            $request->session()->put('register_otp_last_resend_at', now());
            $request->session()->put('otp_expires_at', $expiresAt->toIso8601String());
            return back()->with('success', 'A new verification code has been sent. Please check your Inbox/Spam folder.');
        } catch (\Throwable $e) {
            \Log::error('Resend registration verification failed: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Failed to resend verification code.']);
        }
    }

    public function verifyCode(Request $request)
    {
        // Normalize (e.g., pasted with spaces): keep digits only
        $normalizedCode = preg_replace('/\D+/', '', (string) $request->input('verification_code'));
        $request->merge(['verification_code' => $normalizedCode]);

        $validator = Validator::make($request->all(), [
            'verification_code' => 'required|string|size:6',
        ], [
            'verification_code.required' => 'Verification code must be filled in',
            'verification_code.size' => 'Verification code must be 6 digits',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('verification_code'));
        }

        // Tentukan context: apakah sedang Registrasi atau Forgot Password?
        $forgotEmail = $request->session()->get('forgot_password_email');
        $registerEmailFromInput = $request->input('register_email');
        $registerEmailFromSession = $request->session()->get('register_verify_email');
        
        // Jika ada forgotEmail, kita prioritaskan alur Forgot Password 
        // kecuali jika ada input register_email secara eksplisit (dari form registrasi)
        $isRegistration = (bool)$registerEmailFromInput || ($registerEmailFromSession && !$forgotEmail);
        $registerEmail = $registerEmailFromInput ?: $registerEmailFromSession;

        if ($isRegistration) {
            $resetToken = PasswordResetToken::where('email', $registerEmail)
                ->where('verification_code', $request->verification_code)
                ->where('is_used', false)
                ->latest()
                ->first();

            if (!$resetToken || $resetToken->isExpired()) {
                return redirect()->back()->withErrors(['verification_code' => 'Verification code is invalid or has expired.']);
            }

            $payload = $request->session()->get('register_payload');
            if (!$payload || ($payload['email'] ?? null) !== $registerEmail) {
                return redirect()->route('register')->withErrors(['email' => 'Registration session not found. Please re-register.']);
            }

            $user = User::where('email', $registerEmail)->first();
            if (!$user) {
                $user = User::create([
                    'name' => $payload['name'],
                    'email' => $payload['email'],
                    'password' => $payload['password_hash'],
                    'role' => 'user',
                    'avatar' => $payload['avatar'] ?? null,
                    'referrer_id' => $payload['referrer_id'] ?? null,
                    'email_verified_at' => now(),
                ]);
            } else {
                if (is_null($user->email_verified_at)) {
                    $user->email_verified_at = now();
                    $user->save();
                }
            }
            $resetToken->update(['is_used' => true]);
            $request->session()->forget(['register_verify_email', 'register_payload', 'otp_expires_at']);
            return redirect()->route('login')
                ->with('success', 'Email successfully verified! Please login.')
                ->withInput(['email' => $user->email]);
        }

        // --- Alur Forgot Password ---
        $resetToken = PasswordResetToken::where('verification_code', $request->verification_code)
            ->where('is_used', false);
            
        if ($forgotEmail) {
            $resetToken->where('email', $forgotEmail);
        }

        $resetToken = $resetToken->latest()->first();

        if (!$resetToken || $resetToken->isExpired()) {
            return redirect()->back()
                ->withErrors(['verification_code' => 'Verification code is invalid or has expired']);
        }

        // Mark token as used dan perpanjang waktu untuk reset password (misal +30 menit)
        $resetToken->update([
            'is_used' => true,
            'expires_at' => now()->addMinutes(30)
        ]);

        // Simpan token ke sesi non-flash agar tidak hilang
        $request->session()->put('token', $resetToken->token);
        $request->session()->forget('otp_expires_at');

        return redirect()->route('new-password')
            ->with('success', 'Verification code successful');
    }

    public function showNewPassword(Request $request)
    {
        // Token sudah disimpan di session secara persistent di verifyCode
        return view('auth.new-password');
    }

    public function resendResetCode(Request $request)
    {
        // Cooldown 60 detik
        $lastSent = $request->session()->get('forgot_password_last_sent_at');
        if ($lastSent) {
            $diff = now()->diffInSeconds(\Carbon\Carbon::parse($lastSent));
            if ($diff < 60) {
                $remaining = 60 - $diff;
                // Don't show error — client-side countdown handles this
                return back();
            }
        }

        $email = $request->session()->get('forgot_password_email');
        if (!$email) {
            return redirect()->route('forgot-password')
                ->withErrors(['error' => 'Session not found. Please re-enter your email address.']);
        }

        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('forgot-password')
                ->withErrors(['error' => 'Email tidak ditemukan.']);
        }

        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(64);

        // Bersihkan sesi registrasi agar tidak mengganggu
        $request->session()->forget(['register_verify_email', 'register_payload']);

        PasswordResetToken::where('email', $email)->delete();
        PasswordResetToken::create([
            'email' => $email,
            'token' => $token,
            'verification_code' => $verificationCode,
            'expires_at' => now()->addMinutes(self::VERIFICATION_CODE_EXPIRES_MINUTES),
        ]);

        try {
            Mail::to($email)->send(new PasswordResetMail($verificationCode, $user->name));
            $request->session()->put('forgot_password_last_sent_at', now()->toIso8601String());
            $request->session()->put('otp_expires_at', now()->addMinutes(self::VERIFICATION_CODE_EXPIRES_MINUTES)->toIso8601String());
            return back()->with('success', 'A new verification code has been sent to your email.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to send email. Please try again.']);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',        // at least one uppercase letter
                'regex:/[0-9]/',        // at least one digit
                'regex:/[^A-Za-z0-9]/', // at least one symbol
            ],
            'token' => 'required|string',
        ], [
            'password.required' => 'Password must be entered',
            'password.min' => 'Password must be at least 8 characters',
            'password.confirmed' => 'Confirm password does not match',
            'password.regex' => 'Password must contain uppercase letters, numbers, and punctuation marks.',
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
                ->withErrors(['error' => 'User not found']);
        }

        // Cek apakah password baru sama dengan password lama
        if (Hash::check($request->password, $user->password)) {
            return redirect()->back()
                ->withErrors(['password' => 'Your password is the same as your previous password. Please use a new password.'])
                ->withInput($request->except('password', 'password_confirmation'));
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Hapus token
        $resetToken->delete();

        return redirect()->route('login')
            ->with('success', 'Your password has been reset successfully. Please log in with your new password.');
    }

    // ===== Login OTP (2FA) =====
    public function showLoginOtpForm(Request $request)
    {
        if (!$request->session()->has('login_otp_user_id')) {
            return redirect()->route('login')->withErrors(['email' => 'Please login first.']);
        }
        $email = $request->session()->get('login_otp_email');
        $masked = $email ? preg_replace('/(^.).*(@.*$)/', '$1***$2', $email) : '';
        // Gunakan tampilan auth.blade.php sebagai halaman verifikasi OTP
        return view('auth.auth', ['maskedEmail' => $masked]);
    }

    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6']
        ], [
            'code.required' => 'OTP code must be filled in',
            'code.size' => 'OTP code must be 6 digits',
        ]);
        $email = $request->session()->get('login_otp_email');
        $userId = $request->session()->get('login_otp_user_id');
        if (!$email || !$userId) {
            return redirect()->route('login')->withErrors(['email' => 'OTP session not found.']);
        }
        $otp = \App\Models\LoginOtp::where('email', $email)
            ->where('code', $request->code)
            ->where('is_used', false)
            ->latest()
            ->first();
        if (!$otp || $otp->isExpired()) {
            return back()->withErrors(['code' => 'Kode OTP tidak valid atau sudah kadaluarsa']);
        }
        $otp->update(['is_used' => true]);
        // Mark email as verified on first successful OTP (for Google-first-login scenario)
        $userToVerify = \App\Models\User::find($userId);
        if ($userToVerify && strcasecmp($userToVerify->role ?? '', 'admin') !== 0 && AdminSettings::maintenanceEnabled()) {
            // Clear OTP session and block login
            $request->session()->forget(['login_otp_user_id', 'login_otp_email', 'login_otp_redirect']);
            $msg = AdminSettings::maintenanceMessage() ?: 'Mohon maaf, akses LMS sedang maintenance.';
            return redirect('/')->with('maintenance_notice', $msg);
        }
        if ($userToVerify && is_null($userToVerify->email_verified_at)) {
            $userToVerify->email_verified_at = now();
            $userToVerify->save();
        }
        // Login user
        Auth::loginUsingId($userId, true);
        // Catat aktivitas login
        try {
            \App\Models\ActivityLog::create(['user_id' => $userId, 'action' => 'Login', 'description' => 'Login via OTP']);
        } catch (\Throwable $e) {
        }
        // Bersihkan sesi OTP
        $redirect = $request->session()->pull('login_otp_redirect');
        $request->session()->forget(['login_otp_user_id', 'login_otp_email']);

        $user = Auth::user();
        if (strcasecmp($user->role ?? '', 'admin') === 0) {
            return redirect('/admin/dashboard')
                ->with('login_success', 'Login successful! Welcome to the Admin Panel!');
        }
        // (Maintenance for non-admin is handled above before login session is created.)
        //trainer
        if (strcasecmp($user->role ?? '', 'trainer') === 0) {
            return redirect()
                ->route('trainer.dashboard');
        }
        if ($redirect) {
            return redirect($redirect);
        }
        return redirect()->intended('/dashboard');
    }

    public function resendLoginOtp(Request $request)
    {
        $email = $request->session()->get('login_otp_email');
        $userId = $request->session()->get('login_otp_user_id');
        if (!$email || !$userId) {
            return redirect()->route('login')->withErrors(['email' => 'OTP session not found.']);
        }
        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found.']);
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
            \Log::error('Resend OTP mail failed: ' . $e->getMessage());
            return back()->withErrors(['code' => 'Failed to resend OTP code.']);
        }
        return back()->with('success', 'A new OTP code has been sent.');
    }

    /**
     * Validasi dan kembalikan redirect internal yang aman.
     * Mengizinkan: relative path ("/something") atau full URL dengan host yang sama.
     */
    private function resolveSafeRedirect(Request $request): ?string
    {
        $redirect = $request->input('redirect');
        if (!$redirect)
            return null;

        // Jika full URL, pastikan host sama
        if (filter_var($redirect, FILTER_VALIDATE_URL)) {
            $appHost = $request->getHost();
            $urlHost = parse_url($redirect, PHP_URL_HOST);
            if ($urlHost !== $appHost)
                return null; // Host berbeda -> tolak
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