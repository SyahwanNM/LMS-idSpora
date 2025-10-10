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
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Gagal autentikasi Google: '.$e->getMessage()]);
        }

        $user = User::where('google_id', $googleUser->getId())->first();
        if(!$user){
            // Cek apakah email sudah pernah dipakai registrasi biasa
            $user = User::where('email', $googleUser->getEmail())->first();
            if($user){
                $user->google_id = $googleUser->getId();
            } else {
                $user = User::create([
                    'name' => $googleUser->getName() ?: ($googleUser->getNickname() ?: 'User '.$googleUser->getId()),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'role' => 'user',
                    'password' => Str::password(16),
                ]);
            }
        }

        // Selalu sinkronkan avatar Google jika tersedia & berubah.
        $googleAvatar = $googleUser->getAvatar();
        if($googleAvatar && $googleAvatar !== $user->avatar){
            // Catatan: Jika nanti ada fitur upload manual, bisa tambahkan flag untuk tidak menimpa avatar lokal.
            $user->avatar = $googleAvatar;
            $user->save();
        }

        // Require OTP confirmation before actually logging in
        // Generate and store OTP (expires in 10 minutes)
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        LoginOtp::where('email', $user->email)->where('is_used', false)->delete();
        LoginOtp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
            'is_used' => false,
        ]);

        // Save pending session for OTP verification
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
}
