<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
        'phone',
        'website',
        'bio',
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

    public function savedEvents()
    {
        return $this->belongsToMany(Event::class, 'user_saved_events', 'user_id', 'event_id')->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Accessor unified URL avatar (gunakan jika di view: Auth::user()->avatar_url)
     */
    public function getAvatarUrlAttribute(): string
    {
        $avatar = (string) ($this->avatar ?? '');
        if ($avatar !== '') {
            // External URL (e.g., Google)
            if (str_starts_with($avatar, 'http')) {
                return $avatar;
            }
            // Normalize common stored formats
            // Case 1: filename only -> storage/avatars/{filename}
            if (!str_contains($avatar, '/')) {
                return asset('storage/avatars/'.$avatar);
            }
            // Case 2: path like "avatars/filename"
            if (str_starts_with($avatar, 'avatars/')) {
                return asset('storage/'.$avatar);
            }
            // Case 3: already includes "storage/" prefix
            if (str_starts_with($avatar, 'storage/')) {
                return asset($avatar);
            }
            // Fallback: treat as relative to storage
            return asset('storage/'.$avatar);
        }
        // Fallback to UI Avatars using user's name if available
        $name = trim((string)($this->name ?? 'User'));
        $bg = '6b7280'; // slate-500
        $color = 'ffffff';
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . "&background={$bg}&color={$color}&format=png";
    }
}
