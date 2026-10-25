<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'last_message_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Conversation $conversation) {
            $conversation->uuid = $conversation->uuid ?: (string) Str::uuid();
            $conversation->reply_token = $conversation->reply_token ?: Str::random(40);
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function organizer()
    {
        return $this->belongsTo(Volunteer::class, 'organizer_id');
    }

    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class, 'volunteer_id');
    }

    public function participants()
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function latestMessage()
    {
        return $this->belongsTo(Message::class, 'latest_message_id');
    }

    public function scopeForParticipant(Builder $query, Volunteer $volunteer): Builder
    {
        return $query->whereHas('participants', function (Builder $participantQuery) use ($volunteer) {
            $participantQuery->where('volunteer_id', $volunteer->id);
        });
    }

    public function participantFor(Volunteer $volunteer): ?ConversationParticipant
    {
        if ($this->relationLoaded('participants')) {
            return $this->participants->firstWhere('volunteer_id', $volunteer->id);
        }

        return $this->participants()->where('volunteer_id', $volunteer->id)->first();
    }

    public function unreadCountFor(Volunteer $volunteer): int
    {
        $participant = $this->participantFor($volunteer);

        if (!$participant || !$participant->last_read_message_id) {
            return $this->messages()
                ->where('sender_id', '!=', $volunteer->id)
                ->count();
        }

        return $this->messages()
            ->where('id', '>', $participant->last_read_message_id)
            ->where('sender_id', '!=', $volunteer->id)
            ->count();
    }
}
