<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\ActivityLog;

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

        Auth::login($user, true);

        // Log activity
        if(class_exists(ActivityLog::class)){
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'Login Google',
                'description' => 'Login via Google OAuth'
            ]);
        }

        if($user->role === 'admin'){
            return redirect('/admin/dashboard')->with('login_success','Login Google berhasil (Admin)');
        }

        $intended = session()->pull('social_redirect');
        if($intended && str_starts_with($intended,'/')){
            return redirect($intended)->with('success','Login Google berhasil!');
        }
        return redirect()->intended('/dashboard')->with('success','Login Google berhasil!');
    }
}
