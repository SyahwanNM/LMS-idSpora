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
        'points',
        'badge',
        'last_event_date',
        'profession',
        'institution',
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
            'last_event_date' => 'date',
            'points' => 'integer',
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

    public function profileReminder()
    {
        return $this->hasOne(ProfileReminder::class);
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

    /**
     * Hitung persentase kelengkapan profile
     * Field yang dihitung: name, email, phone, avatar, bio (soft mandatory)
     * Profile dianggap lengkap jika ≥80%
     * 
     * @return int Persentase kelengkapan (0-100)
     */
    public function getProfileCompletionPercentage(): int
    {
        $fields = [
            'name' => !empty($this->name),
            'email' => !empty($this->email),
            'phone' => !empty($this->phone),
            'avatar' => !empty($this->avatar),
            'bio' => !empty($this->bio), // Soft mandatory
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);
        
        return (int) round(($completed / $total) * 100);
    }

    /**
     * Cek apakah profile sudah lengkap (≥80%)
     * 
     * @return bool
     */
    public function isProfileComplete(): bool
    {
        return $this->getProfileCompletionPercentage() >= 80;
    }

    /**
     * Dapatkan field yang masih kosong untuk deep-link
     * 
     * @return array List field yang kosong
     */
    public function getMissingProfileFields(): array
    {
        $missing = [];
        
        if (empty($this->name)) {
            $missing[] = 'name';
        }
        if (empty($this->email)) {
            $missing[] = 'email';
        }
        if (empty($this->phone)) {
            $missing[] = 'phone';
        }
        if (empty($this->avatar)) {
            $missing[] = 'avatar';
        }
        if (empty($this->bio)) {
            $missing[] = 'bio';
        }
        
        return $missing;
    }

    /**
     * Format nomor telepon untuk ditampilkan
     * Dari +6281234567890 menjadi +62 812 3456 7890
     * 
     * @return string|null
     */
    public function getFormattedPhoneAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }

        $phone = $this->phone;
        
        // Extract country code dan number
        $countryCode = $this->phone_country_code;
        $number = $this->phone_number;
        
        if ($countryCode && $number) {
            // Format dengan spasi untuk readability
            $formatted = preg_replace('/(\d{3})(\d{4})(\d{0,4})/', '$1 $2 $3', $number);
            $formatted = rtrim($formatted);
            return $countryCode . ' ' . $formatted;
        }
        
        // Fallback: parse dari phone field lama
        if (str_starts_with($phone, '+')) {
            // Extract country code (biasanya 1-3 digit setelah +)
            preg_match('/^\+(\d{1,3})(.+)$/', $phone, $matches);
            if (count($matches) === 3) {
                $code = '+' . $matches[1];
                $num = $matches[2];
                $formatted = preg_replace('/(\d{3})(\d{4})(\d{0,4})/', '$1 $2 $3', $num);
                $formatted = rtrim($formatted);
                return $code . ' ' . $formatted;
            }
        }
        
        // Fallback: return as is
        return $phone;
    }

    /**
     * Extract country code dari phone number
     * 
     * @return string|null
     */
    public function getPhoneCountryCodeAttribute(): ?string
    {
        if (empty($this->phone)) {
            return '+62'; // Default Indonesia
        }

        $phone = $this->phone;
        
        // Cek common country codes (urutkan dari yang terpanjang ke terpendek)
        $countryCodes = [
            '+62', '+60', '+65', '+44', '+61', '+86', '+81', '+82', '+66', '+84', '+63', '+91', '+1'
        ];

        foreach ($countryCodes as $code) {
            if (str_starts_with($phone, $code)) {
                return $code;
            }
        }

        // Default to +62
        return '+62';
    }

    /**
     * Extract phone number tanpa country code
     * 
     * @return string|null
     */
    public function getPhoneNumberAttribute(): ?string
    {
        if (empty($this->phone)) {
            return null;
        }

        $phone = $this->phone;
        $countryCode = $this->phone_country_code;
        
        if ($countryCode && str_starts_with($phone, $countryCode)) {
            $number = substr($phone, strlen($countryCode));
            // Hapus leading zero jika ada
            $number = ltrim($number, '0');
            return $number;
        }

        // Fallback: extract dari phone field
        if (str_starts_with($phone, '+')) {
            // Extract country code
            $countryCodes = ['+62', '+60', '+65', '+44', '+61', '+86', '+81', '+82', '+66', '+84', '+63', '+91', '+1'];
            foreach ($countryCodes as $code) {
                if (str_starts_with($phone, $code)) {
                    $number = substr($phone, strlen($code));
                    $number = ltrim($number, '0');
                    return $number;
                }
            }
        }

        return $phone;
    }

    /**
     * Get badge information
     */
    public function getBadgeInfoAttribute(): array
    {
        $service = app(\App\Services\UserPointsService::class);
        return $service->getBadgeInfo($this->badge ?? 'beginner');
    }

    /**
     * Get next badge information
     */
    public function getNextBadgeInfoAttribute(): ?array
    {
        $service = app(\App\Services\UserPointsService::class);
        $currentPoints = $this->points ?? 0;
        $currentBadge = $this->badge ?? 'beginner';
        
        $badges = ['beginner', 'explorer', 'learner', 'expert', 'master'];
        $currentIndex = array_search($currentBadge, $badges);
        
        if ($currentIndex !== false && $currentIndex < count($badges) - 1) {
            $nextBadge = $badges[$currentIndex + 1];
            $nextBadgeInfo = $service->getBadgeInfo($nextBadge);
            $nextBadgeInfo['points_needed'] = $nextBadgeInfo['min_points'] - $currentPoints;
            return $nextBadgeInfo;
        }
        
        return null;
    }
}
