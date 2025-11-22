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
        'preferred_location',
        'move_in_date',
        'is_smoker',
        'has_pets',
        'bio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}