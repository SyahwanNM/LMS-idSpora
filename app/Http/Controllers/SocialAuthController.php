<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\LoginOtp;
use App\Mail\LoginOtpMail;

class SocialAuthController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        if($request->filled('redirect')){
            session(['social_redirect' => $request->get('redirect')]);
        }
        return Socialite::driver('google')
            ->scopes(['openid','profile','email'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            // Optional: log payload to diagnose missing avatar/name/email
            \Log::info('[Google OAuth] payload', [
                'id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'nickname' => $googleUser->getNickname(),
                'email' => $googleUser->getEmail(),
                'avatar' => $googleUser->getAvatar(),
                'raw' => method_exists($googleUser, 'user') ? $googleUser->user : null,
            ]);
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal autentikasi Google: '.$e->getMessage()]);
        }

        $user = User::where('google_id', $googleUser->getId())->first();
        if(!$user){
            // Cek apakah email sudah pernah dipakai registrasi biasa
            // Fallback email from raw payload if Socialite's getEmail is empty
            $raw = method_exists($googleUser, 'user') ? (array) $googleUser->user : [];
            $emailFromGoogle = $googleUser->getEmail() ?: ($raw['email'] ?? null);
            $avatarFromGoogle = $googleUser->getAvatar() ?: ($raw['picture'] ?? null);
            $nameFromGoogle = $googleUser->getName() ?: ($raw['name'] ?? ($googleUser->getNickname() ?: 'User '.$googleUser->getId()));

            $user = User::where('email', $emailFromGoogle)->first();
            if($user){
                $user->google_id = $googleUser->getId();
                if(!$user->avatar && $avatarFromGoogle){
                    $user->avatar = $avatarFromGoogle;
                }
                $user->save();
            } else {
                $user = User::create([
                    'name' => $nameFromGoogle,
                    'email' => $emailFromGoogle,
                    'google_id' => $googleUser->getId(),
                    'avatar' => $avatarFromGoogle,
                    'role' => 'user',
                    'password' => Str::password(16),
                ]);
            }
        }

        // Selalu sinkronkan avatar Google jika tersedia & berubah.
        $raw = method_exists($googleUser, 'user') ? (array) $googleUser->user : [];
        $googleAvatar = $googleUser->getAvatar() ?: ($raw['picture'] ?? null);
        if($googleAvatar && $googleAvatar !== $user->avatar){
            // Catatan: Jika nanti ada fitur upload manual, bisa tambahkan flag untuk tidak menimpa avatar lokal.
            $user->avatar = $googleAvatar;
            $user->save();
        }

        // Opsional: jika ingin bypass untuk email tertentu, hapus blok bypass agar semua lewat OTP

        // Only require OTP on first Google login (when email not yet verified)
        if (empty($user->email_verified_at)) {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            LoginOtp::where('email', $user->email)->where('is_used', false)->delete();
            LoginOtp::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'code' => $code,
                'expires_at' => now()->addMinutes(10),
                'is_used' => false,
            ]);

            session([
                'login_otp_user_id' => $user->id,
                'login_otp_email' => $user->email,
                'login_otp_redirect' => session()->pull('social_redirect')
            ]);

            try {
                Mail::to($user->email)->send(new LoginOtpMail($code, $user->name, 10));
            } catch (\Throwable $e) {
                \Log::error('Google login OTP mail failed: '.$e->getMessage());
                return redirect()->route('login')->withErrors(['email' => 'Gagal mengirim kode OTP untuk login Google. Coba lagi.']);
            }

            return redirect()->route('login.otp')->with('success', 'Kode OTP telah dikirim ke email Anda.');
        }

        // If already verified before, log in directly
        Auth::login($user, true);
        $redirect = session()->pull('social_redirect');
        if (strcasecmp($user->role ?? '', 'admin') === 0) {
            return redirect('/admin/dashboard')->with('login_success', 'Login berhasil! Selamat datang di Admin Panel.');
        }
        if ($redirect) {
            return redirect($redirect)->with('success', 'Login berhasil!');
        }
        return redirect()->intended('/dashboard')->with('success','Login berhasil!');
    }
}
