<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationParticipant extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'last_read_at' => 'datetime',
        'archived_at' => 'datetime',
        'muted_until' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }

    public function isArchived(): bool
    {
        return !empty($this->archived_at);
    }
}
