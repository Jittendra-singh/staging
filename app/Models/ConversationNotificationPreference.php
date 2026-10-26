<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationNotificationPreference extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'muted_until' => 'datetime',
    ];

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
}
