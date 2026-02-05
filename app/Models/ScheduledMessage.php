<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduledMessage extends Model
{
    protected $fillable = [
        'user_id',
        'receiver_id',
        'message',
        'scheduled_at',
        'sent',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent' => 'boolean',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
