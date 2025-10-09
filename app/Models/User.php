<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    /**
     * Accessor unified URL avatar (gunakan jika di view: Auth::user()->avatar_url)
     */
    public function getAvatarUrlAttribute(): string
    {
        if($this->avatar){
            // Jika avatar sudah berupa URL (Google) langsung pakai
            if(str_starts_with($this->avatar,'http')){
                return $this->avatar;
            }
            // Jika nanti kita simpan file lokal: storage/app/public/avatars
            return asset('storage/avatars/'.$this->avatar);
        }
        // Fallback avatar default
        return asset('aset/profile.png');
    }
}
