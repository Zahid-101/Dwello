<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoommateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'age',
        'gender',
        'budget_min',
        'budget_max',
        'preferred_city',
        'preferred_property_type',
        'preferred_location',
        'move_in_date',
        'is_smoker',
        'is_verified',
        'has_pets',
        'bio',
        'pref_no_smoker',
        'pref_pets_ok',
        'pref_same_gender_only',
        'pref_visitors_ok',
        'pref_substance_free_required',
        'uses_substances',
        'cleanliness',
        'noise_tolerance',
        'sleep_schedule',
        'study_focus',
        'social_level',
        'schedule_type',
        'occupation_field',
    ];

    protected $casts = [
        'pref_no_smoker' => 'boolean',
        'pref_pets_ok' => 'boolean',
        'pref_same_gender_only' => 'boolean',
        'pref_visitors_ok' => 'boolean',
        'pref_substance_free_required' => 'boolean',
        'uses_substances' => 'boolean',
        'cleanliness' => 'integer',
        'noise_tolerance' => 'integer',
        'sleep_schedule' => 'integer',
        'study_focus' => 'integer',
        'social_level' => 'integer',
        'move_in_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the text label for a lifestyle attribute value (1-5).
     */
    public static function getLabel(string $attribute, ?int $value): string
    {
        if (is_null($value)) {
            return 'Not specified';
        }
        $value = (int) $value; // ensure int

        switch ($attribute) {
            case 'noise_tolerance':
                if ($value <= 2)
                    return 'Quiet';
                if ($value == 3)
                    return 'Moderate';
                return 'Loud / OK with noise';

            case 'sleep_schedule':
                if ($value <= 2)
                    return 'Early sleeper';
                if ($value == 3)
                    return 'Flexible';
                return 'Night owl';

            case 'study_focus':
                if ($value <= 2)
                    return 'Relaxed';
                if ($value == 3)
                    return 'Moderate';
                return 'Focused';

            case 'social_level':
                if ($value <= 2)
                    return 'Private';
                if ($value == 3)
                    return 'Balanced';
                return 'Social';

            default:
                return (string) $value;
        }
    }
}