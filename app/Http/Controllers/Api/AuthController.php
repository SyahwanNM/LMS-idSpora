<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\RegistrationVerificationMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const OTP_EXPIRES_MINUTES = 10;
    private const OTP_RESEND_COOLDOWN_SECONDS = 60;

    // =========================================================================
    // REGISTER — Step 1: Validasi data, simpan ke cache, kirim OTP
    // =========================================================================

    /**
     * POST /api/register
     *
     * Menerima data pendaftaran, menyimpan sementara di cache,
     * lalu mengirim kode OTP 6-digit ke email.
     * Akun belum dibuat sampai OTP diverifikasi.
     *
     * Body:
     *   - name           : string, required
     *   - email          : string, required, unique
     *   - password       : string, min:8, confirmed
     *   - password_confirmation : string, required
     *   - phone          : string, optional
     *   - referrer_code  : string, optional (kode referral orang lain)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',        // minimal 1 huruf kapital
                'regex:/[0-9]/',        // minimal 1 angka
                'regex:/[^A-Za-z0-9]/', // minimal 1 simbol
            ],
            'phone'          => 'nullable|string|max:32|regex:/^[0-9+\-\s]{7,20}$/',
            'referrer_code'  => 'nullable|string|max:16',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'name.regex'         => 'Nama hanya boleh berisi huruf, spasi, tanda kutip, tanda hubung, dan titik.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex'     => 'Password harus mengandung huruf kapital, angka, dan simbol.',
        ]);

        $email = strtolower(trim($request->email));

        // Cek referrer jika ada kode referral
        $referrerId = null;
        if ($request->filled('referrer_code')) {
            $referrer = User::where('referral_code', $request->referrer_code)->first();
            if ($referrer) {
                $referrerId = $referrer->id;
            }
        }

        // Simpan payload pendaftaran ke cache (bukan DB) selama 15 menit
        $cacheKey = 'api_register_payload_' . md5($email);
        Cache::put($cacheKey, [
            'name'          => trim($request->name),
            'email'         => $email,
            'password_hash' => Hash::make($request->password),
            'phone'         => $request->phone ?? null,
            'referrer_id'   => $referrerId,
        ], now()->addMinutes(15));

        // Kirim OTP
        $this->sendRegistrationOtp($email, trim($request->name));

        return response()->json([
            'status'  => 'success',
            'message' => 'Kode OTP telah dikirim ke email Anda. Masukkan kode untuk menyelesaikan pendaftaran.',
            'data'    => [
                'email'       => $email,
                'otp_expires' => now()->addMinutes(self::OTP_EXPIRES_MINUTES)->toIso8601String(),
            ],
        ], 200);
    }

    // =========================================================================
    // REGISTER — Step 2: Verifikasi OTP, buat akun, kembalikan token
    // =========================================================================

    /**
     * POST /api/register/verify-otp
     *
     * Memverifikasi kode OTP yang dikirim ke email.
     * Jika valid, akun dibuat dan access token dikembalikan.
     *
     * Body:
     *   - email             : string, required
     *   - verification_code : string, 6 digit, required
     */
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'email'             => 'required|email',
            'verification_code' => 'required|string|size:6',
        ], [
            'email.required'             => 'Email wajib diisi.',
            'verification_code.required' => 'Kode OTP wajib diisi.',
            'verification_code.size'     => 'Kode OTP harus 6 digit.',
        ]);

        $email = strtolower(trim($request->email));

        // Cek apakah email sudah terdaftar (race condition guard)
        if (User::where('email', $email)->whereNotNull('email_verified_at')->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email sudah terdaftar dan terverifikasi. Silakan login.',
            ], 409);
        }

        // Ambil payload dari cache
        $cacheKey = 'api_register_payload_' . md5($email);
        $payload  = Cache::get($cacheKey);

        if (!$payload) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sesi pendaftaran tidak ditemukan atau sudah kadaluarsa. Silakan daftar ulang.',
            ], 422);
        }

        // Verifikasi kode OTP dari tabel password_reset_tokens
        $otpRecord = PasswordResetToken::where('email', $email)
            ->where('verification_code', $request->verification_code)
            ->where('is_used', false)
            ->latest('created_at')
            ->first();

        if (!$otpRecord || $otpRecord->isExpired()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.',
            ], 422);
        }

        // Tandai OTP sebagai sudah digunakan
        $otpRecord->update(['is_used' => true]);

        // Buat akun user
        $user = User::create([
            'name'              => $payload['name'],
            'email'             => $payload['email'],
            'password'          => $payload['password_hash'],
            'phone'             => $payload['phone'],
            'role'              => 'user',
            'referrer_id'       => $payload['referrer_id'],
            'email_verified_at' => now(),
        ]);

        // Hapus payload dari cache
        Cache::forget($cacheKey);

        // Buat access token Sanctum
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Pendaftaran berhasil! Selamat datang di LMS IdSpora.',
            'data'    => [
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ],
        ], 201);
    }

    // =========================================================================
    // REGISTER — Kirim ulang OTP
    // =========================================================================

    /**
     * POST /api/register/resend-otp
     *
     * Mengirim ulang kode OTP ke email yang sedang dalam proses pendaftaran.
     * Cooldown 60 detik antar pengiriman.
     *
     * Body:
     *   - email : string, required
     */
    public function resendRegisterOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $email = strtolower(trim($request->email));

        // Cek apakah payload pendaftaran masih ada
        $cacheKey = 'api_register_payload_' . md5($email);
        $payload  = Cache::get($cacheKey);

        if (!$payload) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Sesi pendaftaran tidak ditemukan. Silakan daftar ulang.',
            ], 422);
        }

        // Cooldown check
        $cooldownKey = 'api_register_otp_cooldown_' . md5($email);
        if (Cache::has($cooldownKey)) {
            $remaining = (int) ceil(Cache::get($cooldownKey) - time());
            return response()->json([
                'status'  => 'error',
                'message' => "Tunggu {$remaining} detik sebelum mengirim ulang kode OTP.",
            ], 429);
        }

        $this->sendRegistrationOtp($email, $payload['name']);

        // Set cooldown
        Cache::put($cooldownKey, time() + self::OTP_RESEND_COOLDOWN_SECONDS, self::OTP_RESEND_COOLDOWN_SECONDS);

        return response()->json([
            'status'  => 'success',
            'message' => 'Kode OTP baru telah dikirim ke email Anda.',
            'data'    => [
                'email'       => $email,
                'otp_expires' => now()->addMinutes(self::OTP_EXPIRES_MINUTES)->toIso8601String(),
            ],
        ]);
    }

    // =========================================================================
    // LOGIN
    // =========================================================================

    /**
     * POST /api/login
     *
     * Body:
     *   - email    : string, required
     *   - password : string, required
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        // Blokir login jika email belum diverifikasi
        if (is_null($user->email_verified_at)) {
            Auth::logout();
            return response()->json([
                'status'  => 'error',
                'message' => 'Email belum diverifikasi. Silakan selesaikan proses pendaftaran terlebih dahulu.',
                'data'    => ['email' => $user->email],
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil! Selamat datang di LMS IdSpora.',
            'data'    => [
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'Bearer',
            ],
        ]);
    }

    // =========================================================================
    // LOGOUT
    // =========================================================================

    /**
     * POST /api/logout
     * Auth: Bearer token required
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'message' => 'User tidak terautentikasi.',
            ], 401);
        }

        $currentToken = $user->currentAccessToken();
        if ($currentToken) {
            $currentToken->delete();
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil.',
        ]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Generate dan kirim OTP ke email pendaftar.
     * Menyimpan kode ke tabel password_reset_tokens.
     */
    private function sendRegistrationOtp(string $email, string $name): void
    {
        $code      = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token     = Str::random(64);
        $expiresAt = now()->addMinutes(self::OTP_EXPIRES_MINUTES);

        // Hapus OTP lama untuk email ini
        PasswordResetToken::where('email', $email)->delete();

        PasswordResetToken::create([
            'email'             => $email,
            'token'             => $token,
            'verification_code' => $code,
            'expires_at'        => $expiresAt,
            'is_used'           => false,
        ]);

        try {
            Mail::to($email)->send(new RegistrationVerificationMail($code, $name, self::OTP_EXPIRES_MINUTES));
        } catch (\Throwable $e) {
            \Log::error('[API Register] Gagal kirim OTP email', [
                'email' => $email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}