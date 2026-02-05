<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Property;
use App\Models\RoommateProfile;

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
        'profile_photo_path',
        'phone_number',
        'is_premium',
        'premium_subscription_date',
        'subscription_plan',
        'listing_limit',
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

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function roommateProfile()
    {
        return $this->hasOne(RoommateProfile::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(RoommateProfile::class, 'favorites', 'user_id', 'roommate_profile_id')
            ->withTimestamps();
    }

    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    public function isSeeker(): bool // Roommate Seeker/Tenant
    {
        return $this->role === 'seeker';
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    public function isAdmin(): bool
    {
        // Simple admin check based on .env config or specific email
        // Logic: if email matches config OR role is 'admin' (if we had that role)
        return $this->email === config('app.admin_email', 'admin@dwello.com');
    }

    public function rejectedUsers()
    {
        return $this->belongsToMany(User::class, 'rejected_matches', 'user_id', 'rejected_user_id')
            ->withTimestamps();
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return \Illuminate\Support\Facades\Storage::url($this->profile_photo_path) . '?v=' . time();
        }
        return null;
    }
}
