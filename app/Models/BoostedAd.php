<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Assuming User model is in App\Models namespace

class BoostedAd extends Model
{
    protected $fillable = ['user_id', 'property_id', 'property_image', 'note', 'is_active'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
    //
}
